<?php

namespace App\Controllers;

class CheckoutController extends BaseController
{
    public function __construct() {
		$this->a_access = new \App\Models\Apiaccess();
                $this->ship = new \App\Models\ShipAPI();
    } 
    
    public function calculateShipping()
    {
        $state   = trim((string) $this->request->getPost('state'));
        $city    = trim((string) $this->request->getPost('city'));
        $pincode = trim((string) $this->request->getPost('pincode'));

        $selectedItems = json_decode(
            (string) $this->request->getPost('selected_items'),
            true
        );

        if ($state === '' || $city === '' ||
            !preg_match('/^\d{6}$/', $pincode)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Please enter valid address details.'
            ]);
        }

        if (!is_array($selectedItems) || empty($selectedItems)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Please select at least one product.'
            ]);
        }

        $totalProductWeight = 0;
        $items = [];

        foreach ($selectedItems as $item) {
            $productId = trim((string) ($item['product_id'] ?? ''));
            $quantity = filter_var(
                $item['quantity'] ?? null,
                FILTER_VALIDATE_INT
            );

            if ($productId === '' || $quantity === false || $quantity < 1) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid product or quantity.'
                ]);
            }

            // Fetch only the fields needed for shipping.
            $product = $this->a-access->custom_query("SELECT id,weight FROM Products WHERE id ='".$productId."'");

            if (empty($product)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Weight is missing or invalid for product ' . $productId . '.'
                ]);
            }

            $unitWeight = (float) $product[0]['weight'];
            $lineWeight = $unitWeight * $quantity;
            $totalProductWeight += $lineWeight;

            $items[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_weight_kg' => $unitWeight,
                'line_weight_kg' => round($lineWeight, 3)
            ];
        }

        // Apply KPM's packaging rule.
        $shippingWeight = $totalProductWeight < 1 ? 1.2: $totalProductWeight + 0.5;

        try {

            // Get the Shiprocket authentication token.
            $auth = $this->ship->getAuthentication();
            $token = $auth->token;

            // Fetch prepaid shipping rates for the calculated weight.
            $rateResponse = $shipAPI->getServiceAbility(
                $pincode,
                $token,
                round($shippingWeight, 3)
            );

            $couriers = $rateResponse->data->available_courier_companies ?? [];

            if (empty($couriers)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No courier service is available for this pincode.'
                ]);
            }

            $availableRates = [];

            foreach ($couriers as $courier) {
                $rate = $courier->rate ?? $courier->freight_charge ?? null;

                if (is_numeric($rate) && (float) $rate > 0) {
                    $availableRates[] = [
                        'courier_name' => $courier->courier_name ?? 'Courier',
                        'rate' => (float) $rate,
                        'estimated_delivery_days' =>
                            $courier->estimated_delivery_days ?? null
                    ];
                }
            }

            if (empty($availableRates)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Shiprocket returned no usable shipping rates.'
                ]);
            }

            usort($availableRates, function ($a, $b) {
                return $b['rate'] <=> $a['rate'];
            });

            $selectedRate = $availableRates[0];

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Shipping rate calculated successfully.',
                'data' => [
                    'pincode' => $pincode,
                    'total_product_weight_kg' => round($totalProductWeight, 3),
                    'shipping_weight_kg' => round($shippingWeight, 3),
                    'shipping_cost' => round($selectedRate['rate'], 2),
                    'courier_name' => $selectedRate['courier_name'],
                    'estimated_delivery_days' => $selectedRate['estimated_delivery_days'],
                    'available_rates' => $availableRates
                ]
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'Shiprocket shipping calculation failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unable to calculate shipping right now. Please try again.'
            ]);
        }

    }



}