<?php

namespace App\Controllers;

class CheckoutController extends BaseController
{
    public function __construct() {
		$this->a_access = new \App\Models\Apiaccess();
                $this->ship = new \App\Models\ShipAPI();
                $this->Bemail = new \App\Models\BrevoEmail();
                $this->cpay = new \App\Models\CashFreeApi();
    } 
    
    
    //to open checkout page   
    public function checkout()
    {
        // Check login
        $customerId = session()->get('customer_id');
        if (!$customerId) {
            $redirectUrl = current_url();
            $queryString = $this->request->getServer('QUERY_STRING');

            if (!empty($queryString)) {
                $redirectUrl .= '?' . $queryString;
            }

            return redirect()->to(
                base_url('signin') . '?redirect=' . urlencode($redirectUrl)
            );
        }

        // Get selected product IDs
        $productIds = $this->request->getGet('product_ids');
        if (!is_array($productIds)) {
            $productIds = [];
        }
        $productIds = array_values(array_unique(array_filter(
            array_map('trim', $productIds),
            function ($id) {
                return $id !== '';
            }
        )));

        // Get customer information
        $customerResult = $this->a_access->custom_query(
            "SELECT first_name, last_name, email, store_credit
             FROM Customer
             WHERE id = '".$customerId."'"
        );
        if (empty($customerResult)) {
            session()->remove(['customer_id', 'customer_name']);

            return redirect()->to(
                base_url('signin') . '?redirect=' .
                urlencode(current_url())
            );
        }
        $customer = $customerResult[0];

        // Get customer's active saved addresses
        $addresses = $this->a_access->custom_query(
            "SELECT id, first_name, last_name,
                    address_line1, address_line2,
                    city, state, country, zip,
                    contact_no, company_name, company_gstin,
                    address_title, is_default
             FROM Customer_addresses
             WHERE cust_id = '".$customerId."' 
               AND status = 'Y'
             ORDER BY
                 CASE WHEN is_default = 'Y' THEN 0 ELSE 1 END,
                 id DESC"
        );
        
        $defaultAddress = null;
        foreach ($addresses as $address) {
            if (($address['is_default'] ?? 'N') === 'Y') {
                $defaultAddress = $address;
                break;
            }
        }
        
        // Fetch the customer's cart.
        $cartResult = $this->a_access->custom_query(
            "SELECT id
             FROM Cart_master
             WHERE cust_id = '".$customerId."'
             LIMIT 1"
        );

        $checkoutItems = [];
        $subtotal = 0;
        $itemCount = 0;
        $totalProductWeight = 0;


        if (!empty($cartResult) && !empty($productIds)) {
            $cartId = $cartResult[0]['id'];

            $cartDetails = $this->a_access->custom_query(
                "SELECT product_id, product_quantity
                 FROM Cart_details
                 WHERE cart_id = ".$cartId  );

            foreach ($cartDetails as $detail) {
                //to check that product exists in selected products
                if(in_array($detail['product_id'] , $productIds)){
                    $productId = $detail['product_id'];
                    $quantity = (int) $detail['product_quantity'];

                    if ($quantity < 1) {
                        continue;
                    }

                    $productResult = $this->a_access->custom_query(
                        "SELECT  p.id,  p.main_image,  p.weight,
                            pd.product_name, pp.price,  pp.discounted_price
                         FROM Products p
                         INNER JOIN Product_description pd ON pd.product_id = p.id
                         INNER JOIN Product_price pp ON pp.product_id = p.id
                         WHERE p.id = '".$productId."'"
                    );

                    if (empty($productResult)) {
                        continue;
                    }

                    $product = $productResult[0];

                    $price = (float) $product['price'];
                    $discountedPrice = (float) ($product['discounted_price'] ?? 0);

                    $unitPrice = (
                        $discountedPrice > 0 &&
                        $discountedPrice < $price
                    ) ? $discountedPrice : $price;

                    $lineTotal = $unitPrice * $quantity;

                    $checkoutItems[] = [
                        'id' => $productId,
                        'product_name' => $product['product_name'],
                        'main_image' => $product['main_image'],
                        'unit_price' => $unitPrice,
                        'product_quantity' => $quantity,
                        'line_total' => $lineTotal,
                        'weight' => (float) ($product['weight'] ?? 0)
                    ];
                    
                    
                    $totalProductWeight += (float) ($product['weight'] ?? 0) * (int) ($quantity ?? 0);
                    $subtotal += $lineTotal;
                    $itemCount += $quantity;
                }
            }
        }

        $shippingCost = 0;
        $shippingError = null;

        if (!empty($checkoutItems) && !empty($defaultAddress)) {
            // Apply the existing packaging-weight rule.
            $shippingWeight = $totalProductWeight < 1  ? 1.2 : $totalProductWeight + 0.5;
            $pincode = trim((string) ($defaultAddress['zip'] ?? ''));

            if (preg_match('/^\d{6}$/', $pincode) &&  !empty($defaultAddress['state']) &&  !empty($defaultAddress['city'])   ) {
                try {
                    $auth = $this->ship->getAuthentication();
                    $token = $auth->token;

                    $rateResponse = $this->ship->getServiceAbility( $pincode, $token, round($shippingWeight, 3)  );
                    $couriers = $rateResponse->data->available_courier_companies ?? [];
                    $selectedRate = null;

                    foreach ($couriers as $courier) {
                        $rate = $courier->rate ?? $courier->freight_charge ?? null;

                        if (!is_numeric($rate) || (float) $rate <= 0) {
                            continue;
                        }

                        // Keep the courier with the highest rate.
                        if ($selectedRate === null || (float) $rate > $selectedRate['rate']) {
                            $selectedRate = [
                                'courier_name' => $courier->courier_name ?? 'Courier',
                                'rate' => (float) $rate,
                                'cod_available' => (int) ($courier->cod ?? 0) === 1,
                                'cod_charges' => (float) ($courier->cod_charges ?? 0),
                                'estimated_delivery_days' => $courier->estimated_delivery_days ?? null
                            ];
                        }
                    }

                    if ($selectedRate !== null) {
                        $shippingCost = round($selectedRate['rate'], 2);
                        $recommendedCourier = $selectedRate['courier_name'];
                        $codAvailable = $selectedRate['cod_available'];

                        $codCharges = $codAvailable
                            ? (int) $selectedRate['cod_charges']
                            : 0;
                        $codCharges = $codCharges+12;

                    } else {
                        $shippingError = 'No courier service is available for this pincode.';
                    }
                } catch (\Throwable $e) {
                    log_message( 'error', 'Checkout shipping calculation failed: ' . $e->getMessage()  );
                    $shippingError =  'Unable to calculate shipping right now.';
                }
            } else {
                $shippingError =  'The default shipping address has invalid details.';
            }
        } elseif (empty($checkoutItems)) {
            $shippingError = 'No selected products were found.';
        } else {
            $shippingError = 'Please set a default shipping address.';
        }

        // Calculate the initial order summary.
        $gst = round(($subtotal+$shippingCost) * 0.18, 2);
        $total = $shippingCost !== null  ? round($subtotal + $shippingCost + $gst, 2)   : null;
        
        // COD is not allowed for orders above ₹8,000.
        $codLimit = 8000;

        $codEligible = (
            $total !== null &&
            $total <= $codLimit &&
            !empty($codAvailable)
        );

        if (!$codEligible) {
            $codAvailable = false;
            $codCharges = 0;
        }
        
        return view('checkout', [
            'page_title' => 'Checkout | K-Pop Merch',
            'selectedProductIds' => $productIds,
            'customer' => $customer,
            'addresses' => $addresses,
            'checkoutItems' => $checkoutItems,
            'subtotal'      => $subtotal,
            'itemCount'     => $itemCount,
            'shippingCost'  => $shippingCost,
            'shippingError' => $shippingError,
            'gst'           => $gst,
            'total'         => $total,
            'recommendedCourier' => $recommendedCourier ?? null,
            'codAvailable'       => $codAvailable ?? false,
            'codCharges'          => $codCharges ?? 0,
            'codEligible'  => $codEligible
        ]);
    }

    //to get shipping estimate
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
            $product = $this->a_access->custom_query("SELECT id,weight FROM Products WHERE id ='".$productId."'");

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
            $rateResponse = $this->ship->getServiceAbility(
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
                        'rate' => (float) round($rate),
                        'estimated_delivery_days' =>
                            $courier->estimated_delivery_days ?? null,
                            'cod_available' => (int) ($courier->cod ?? 0) === 1,
                            'cod_charges' => (float) ($courier->cod_charges ?? 0)
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
                'shipping_cost' => round($selectedRate['rate'], 2),
                'courier_name' => $selectedRate['courier_name'],
                'cod_available' => $selectedRate['cod_available'],
                'cod_charges' => $selectedRate['cod_charges']
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

    //to apply coupon
    public function applyCoupon()
    {
        $couponCode = trim((string) $this->request->getPost('coupon_code'));
        $customerId = session()->get('customer_id');

        if ($couponCode === '') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Please enter a coupon code.'
            ]);
        }

        // Find the coupon by its code.
        $coupon = $this->a_access->all_info(
            'Coupon_master',
            ['coupon_code' => $couponCode]
        );
        $coupon = $coupon[0];

        if (empty($coupon)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid coupon code.'
            ]);
        }

        // Check whether the coupon is active.
        if (($coupon['status'] ?? '') !== 'Y') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'This coupon is no longer active.'
            ]);
        }

        // Check the expiry date, if one is configured.
        $expiryDate = $coupon['expiry_date'] ?? null;

        if (
            !empty($expiryDate) &&
            $expiryDate !== '0000-00-00' &&
            $expiryDate < date('Y-m-d')
        ) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'This coupon has expired.'
            ]);
        }

        // Customer-specific coupons require login.
        if (($coupon['coupon_type'] ?? '') === 'customer') {

            if (empty($customerId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Please log in to use this coupon.',
                    'login_required' => true
                ]);
            }

            // Ensure the coupon belongs to the logged-in customer.
            if (
                !isset($coupon['cust_id']) ||
                (string) $coupon['cust_id'] !== (string) $customerId
            ) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'This coupon is not available for your account.'
                ]);
            }
        }

        // Coupon is valid. Discount calculation comes next.
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Coupon applied successfully.',
            'data' => [
                'coupon_code' => $coupon['coupon_code'],
                'coupon_type' => $coupon['coupon_type'],
                'amount_type' => $coupon['amount_type'],
                'amount' => (float) $coupon['amount']
            ]
        ]); 
    }

    
    
    
    
    
    

    //to find or add address into order_address
    private function getOrCreateOrderAddress(array $address): int
    {
        // Build a lookup using every address-data column.
        $fields = [
            'first_name', 'last_name',
            'company_name', 'company_gstin',
            'address_line1', 'address_line2',
            'city', 'state', 'zip', 'country',
            'contact_no'
        ];

        $conditions = [];

        foreach ($fields as $field) {
            $value = $address[$field] ?? null;

            if ($value === null) {
                $conditions[] = "`".$field."` IS NULL";
            } else {
                $safeValue = addslashes((string) $value);
                $conditions[] = "`".$field."` = '" . $safeValue . "'";
            }
        }

        $where = implode(' AND ', $conditions);
        $existing = $this->a_access->custom_query( "SELECT id  FROM Order_addresses WHERE ".$where." LIMIT 1" );
        if (!empty($existing)) {
            return (int) $existing[0]['id'];
        }

        $addressData = [];
        foreach ($fields as $field) {
            $addressData[$field] = $address[$field] ?? null;
        }

        $newId = $this->a_access->insert_table_id( 'Order_addresses',  $addressData );
        if (!$newId) {
            throw new \RuntimeException('Unable to save the order address.' );
        }

        return (int) $newId;
    }
    
    //to get address from custom_address
    private function validateCustomerAddress(int $id,int $customerId){
        
            $AddressRows = $this->a_access->custom_query(
                "SELECT *
                 FROM Customer_addresses
                 WHERE id = " . (int) $id . "
                   AND cust_id = " . (int) $customerId . "
                   AND status = 'Y'
                 LIMIT 1"
            );

            if (empty($AddressRows)) {
                return ;
            }

            $Address = $AddressRows[0];
            
            return $Address;
    }
    
    //to get shipping fee
    private function getShippingFee($pincode,$weight){
       
        $auth = $this->ship->getAuthentication();
        $rateResponse = $this->ship->getServiceAbility( $pincode, $auth->token, round($weight, 3) );

            $couriers = $rateResponse->data->available_courier_companies ?? [];
            $shippingCost = null;

            foreach ($couriers as $courier) {
                $rate = $courier->rate ?? $courier->freight_charge ?? null;
                if (is_numeric($rate) && (float) $rate > 0) {
                    if ($shippingCost === null || (float) $rate > $shippingCost) {
                        $shippingCost = (float) $rate;
                    }
                }
            }

            if ($shippingCost === null) {  return; }
            return round($shippingCost);
    }
    
    //to clear cart after order placement 
    private function clearCart($customerId,$products){
        
         // Extract and validate product IDs.
        $productIds = [];
        foreach ($products as $item) {
            $productId = trim((string) ($item['product_id'] ?? ''));

            if ($productId === '') {
                return false;
            }

            $productIds[] = $productId;
        }

        // Remove duplicates.   
        $productIds = array_values(array_unique($productIds));
        

        // Find the cart IDs belonging to this customer.
        $cartRows = $this->a_access->all_info("Cart_master",['cust_id'=>$customerId]);

        if (empty($cartRows)) {
            // No cart exists, so there is nothing to remove.
            return false;
        }

        $cartIds = $cartRows[0]['id'];

        // Delete only the ordered products from this customer's carts.
        foreach($productIds as $id){
            $this->a_access->delete_data("Cart_details",['cart_id'=>$cartIds,'product_id'=>$id]);
        }
        
        return $cartIds;
    }
    
    //to get order products info
    private function buildOrderProducts($products){
        $subtotal =0;
        $totalWeight=0;
        $orderItems=[];
        
        
            // Normalize and validate product IDs and quantities.
            $requestedProducts = [];

            foreach ($products as $item) {
                $productId = trim((string) ($item['product_id'] ?? ''));
                $quantity = !empty($item['quantity']) ? $item['quantity'] : $item['product_qty'];
                $quantity = filter_var( $quantity ?? null ,  FILTER_VALIDATE_INT );

                if ($productId === '' || $quantity === false || $quantity < 1) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'status'  => 'error',
                        'message' => 'One or more products have invalid quantities.'
                    ]);
                }

                if (isset($requestedProducts[$productId])) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'status'  => 'error',
                        'message' => 'Duplicate products were submitted.'
                    ]);
                }

                $requestedProducts[$productId] = $quantity;
            }
        
         foreach ($requestedProducts as $productId => $quantity) {
                $safeProductId = addslashes((string) $productId);

                $productRows = $this->a_access->custom_query(
                    "SELECT p.id,  p.main_image,  p.weight,
                        pd.product_name,
                        ps.product_qty, ps.on_hand,
                        pp.price,   pp.discounted_price
                     FROM Products p
                     INNER JOIN Products_stock ps ON ps.product_id = p.id
                     INNER JOIN Product_price pp ON pp.product_id = p.id
                     INNER JOIN Product_description pd ON pd.product_id = p.id
                     WHERE p.id = '" . $safeProductId . "'"
                );

                if (empty($productRows)) {
                    throw new \RuntimeException(  'One of the items is no longer available. Please modify cart and try again.'  );
                }

                $product = $productRows[0];
                $availableQuantity = (int) ($product['product_qty'] ?? 0);

                if ($availableQuantity < $quantity) {
                    throw new \RuntimeException(  'Insufficient stock available for one of the items.' );
                }

                $price = (float) $product['price'];
                $discountedPrice = (float) ($product['discounted_price'] ?? 0);
                $unitPrice = ( $discountedPrice > 0 && $discountedPrice < $price  ) ? $discountedPrice : $price;
                $lineTotal = round($unitPrice * $quantity, 2);
                $subtotal += $lineTotal;
                $totalWeight += (float) ($product['weight'] ?? 0) * $quantity;

                $orderItems[] = [
                    'product_id'   => $productId,
                    'quantity'     => $quantity,
                    'availableQty' =>$availableQuantity,
                    'unit_price'   => $unitPrice,
                    'product_name' => $product['product_name'] ?? 'Product',
                    'main_image'   => $product['main_image'] ?? '',
                    'on_hand'      => $product['on_hand']
                ];
            }
            
            $value =[$orderItems,$subtotal,$totalWeight];
            return $value;
    }
    
    //to get discount
    private function getDiscount($couponCode,$subtotal,$customerId){
        // Revalidate the coupon on the server.
            $couponId = null;
            $discount = 0;

            if ($couponCode !== '') {
                $safeCouponCode = addslashes($couponCode);

                $couponRows = $this->a_access->custom_query(
                    "SELECT *
                     FROM Coupon_master
                     WHERE coupon_code = '" . $safeCouponCode . "'
                     LIMIT 1"
                );
                if (empty($couponRows)) {
                    throw new \RuntimeException('The coupon is invalid.');
                }

                $coupon = $couponRows[0];
                if (($coupon['status'] ?? '') !== 'Y') {
                    throw new \RuntimeException( 'This coupon is no longer active.' );
                }

                $expiryDate = $coupon['expiry_date'] ?? null;
                if ( !empty($expiryDate) && $expiryDate !== '0000-00-00' && $expiryDate < date('Y-m-d')
                ) {
                    throw new \RuntimeException('This coupon has expired.');
                }

                if ( ($coupon['coupon_type'] ?? '') === 'customer' && (string) ($coupon['cust_id'] ?? '') !== (string) $customerId
                ) {
                    throw new \RuntimeException(
                        'This coupon is not available for your account.'
                    );
                }

                $amount = (float) ($coupon['amount'] ?? 0);
                if (($coupon['amount_type'] ?? '') === 'percent') {
                    $discount = $subtotal * $amount / 100;
                } elseif (($coupon['amount_type'] ?? '') === 'fixed') {
                    $discount = $amount;
                } else {
                    throw new \RuntimeException(  'The applied coupon has an unsupported discount type.' );
                }

                $discount = round(min(max(0, $discount), $subtotal), 2);
                $couponId = $coupon['id'];
            }
            
            return [$discount,$couponId];
    }
    
    //to send email after order placed
    private function sendOrderEmail($emailData){
        
        $emailResult = $this->Bemail->sendOrderPlacedEmail($emailData);
        return $emailResult;
        
    }
   
    //to place order with store credit
    public function placeOrderWithStoreCredit()
    {
        //check customer
        $customerId = session()->get('customer_id');
        if (!$customerId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'message' => 'Please log in to place your order.'
            ]);
        }
        
        
        //get order details 
        $products = json_decode((string) $this->request->getPost('products'),   true );
        $requestedCredit  = $this->request->getPost('usedStoreCredit');
        $couponCode       = trim((string) $this->request->getPost('coupon_code'));
        $shippingAddressId = $this->request->getPost('shipping_address_id');
        $billingAddressId  = $this->request->getPost('billing_address_id');
        $orderComment      = trim((string) $this->request->getPost('order_comment'));

        if (
            !is_array($products) || empty($products) ||
            !is_numeric($requestedCredit) || (float) $requestedCredit <= 0 ||
            !is_numeric($shippingAddressId) || !is_numeric($billingAddressId)
        ) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Invalid order details.'
            ]);
        }

        try {
            // Fetch the customer's credit and email in the same query.
            $customerRows = $this->a_access->custom_query(
                "SELECT store_credit, email
                 FROM Customer
                 WHERE id = " . (int) $customerId . "
                 LIMIT 1"
            );

            if (empty($customerRows)) {
                throw new \RuntimeException('Customer account was not found.');
            }

            $availableCredit = (float) $customerRows[0]['store_credit'];
            $customerEmail   = $customerRows[0]['email'] ?? '';
            
            // Fetch products, prices, stock, names and images.
            $subtotal = 0;
            $orderItems = [];
            $totalWeight = 0;

            $value= $this->buildOrderProducts($products);
            $orderItems=$value[0];
            $subtotal=$value[1];
            $totalWeight=$value[2];

            $subtotal = round($subtotal, 2);

            //validate the shipping and billing address
            $shippingAddress = $this->validateCustomerAddress($shippingAddressId, $customerId);
            $billingAddress = $this->validateCustomerAddress($billingAddressId, $customerId);

            // get pincode
            $pincode = trim((string) ($shippingAddress['zip'] ?? ''));
            if (  !preg_match('/^\d{6}$/', $pincode) || empty($shippingAddress['city']) ||  empty($shippingAddress['state'])  ) {
                throw new \RuntimeException(  'The shipping address has incomplete details.' );
            }
            
            //get id from order_addresess 
            $shippingOrderAddressId = $this->getOrCreateOrderAddress($shippingAddress);
            $billingOrderAddressId = $this->getOrCreateOrderAddress($billingAddress);

            // Calculate shipping.
            $shippingWeight = $totalWeight < 1  ? 1.2: $totalWeight + 0.5;

            //get shipping charges
            $shippingCost = $this->getShippingFee($pincode, $shippingWeight);
            if ($shippingCost === null) {
                throw new \RuntimeException(  'No courier service is available for this address.' );
            }

            // Revalidate the coupon on the server.
            $value = $this->getDiscount($couponCode,$subtotal,$customerId);
            $couponId =$value[1];
            $discount = $value[0];

            // Store-credit-only orders have no COD charges.
            $amountBeforeCredit = round(  max(0, $subtotal + $shippingCost - $discount),  2 );
            $creditToUse = round((float) $requestedCredit, 2);
            if ($creditToUse > $availableCredit) {
                throw new \RuntimeException(  'Your store-credit is less than order total.'  );
            }

            $gst = round($amountBeforeCredit * 0.18);
            $grandTotal = round($amountBeforeCredit + $gst, 2);
            if ($creditToUse + 0.001 < $grandTotal) {
                throw new \RuntimeException( 'Store credit does not cover the full order total.'  );
            }
            $creditToUse = $grandTotal;
            
            // Create the order.
            $orderDate = date('Y-m-d');

            $orderId = $this->a_access->insert_table_id('Orders', [
                'cust_id'                 => $customerId,
                'order_status_id'         => 1,
                'shipping_address_id'     => $shippingOrderAddressId,
                'billing_address_id'      => $billingOrderAddressId,
                'order_date'              => $orderDate,
                'sub_total_amount'        => $subtotal,
                'discounted_total_amount' => $discount,
                'total_taxed_amount'      => $gst,
                'shipping_charged_amount' => $shippingCost,
                'grand_total_amount'      => $grandTotal,
                'payment_type'            => 'store_credit',
                'order_comment'           => $orderComment,
                'coupon_id'               => $couponId,
                'store_credit'            => $creditToUse,
                'cod_charges'             => 0,
                'order_currency'          => 1,
                'last_status_updated_date'=>$orderDate
            ]);

            // Save order products and decrease stock.
            foreach ($orderItems as $item) {
                //add product to order
                $inserted = $this->a_access->insert_table_id('Order_products', [
                    'order_id'      => $orderId,
                    'product_id'    => $item['product_id'],
                    'product_price' => $item['unit_price'],
                    'product_qty'   => $item['quantity'],
                    'status'        => 1
                ]);
            
                //update quantity
                $newQty = (int)($item['availableQty'] - $item['quantity']);
                
                //to check for on-hand
                if($newQty==0){
                    $this->a_access->update_table( 'Products_stock',
                        [ 'product_qty' =>$newQty,'on_hand' =>'N','instock'=>'N' ],
                        [ 'product_id' => $item['product_id']]
                    );
                }
                
                $this->a_access->update_table( 'Products_stock',
                        [ 'product_qty' =>$newQty ],
                        [ 'product_id' => $item['product_id']]
                );
            }

            // Deduct store credit
            $newStoreCredit = round($availableCredit - $creditToUse, 2);
            $this->a_access->update_table( 'Customer',
                [ 'store_credit' =>$newStoreCredit ],
                [ 'id' => $customerId]
            );
                

            //change coupon if applied
            if($discount>0){
                $this->a_access->update_table( 'Coupon_master',
                    [ 'status' => 'N'  ],
                    [ 'id' => $couponId]  );
            }
                
            //clear cart
            $res = $this->clearCart((int) $customerId, $orderItems);
                
            //send confirmation email
            $emailProducts = [];
            foreach ($orderItems as $item) {
                $emailProducts[] = [
                    'product_name'  => $item['product_name'],
                    'main_image'    => !empty($item['main_image']) ? $item['main_image']: base_url('Images/icons/ic_product.png'),
                    'product_price' => (float) $item['unit_price'],
                    'product_qty'   => (int) $item['quantity'],
                    'line_total'    => round((float) $item['unit_price'] * (int) $item['quantity'],  2 )
                ];
            }
            
            $orderDateObj = new \DateTime($orderDate);
            $orderDateObj->modify('+1 month');
            $orderDateObj->modify('+1 week');
            $estimatedDeliveryDate = $orderDateObj->format('Y-m-d');

            $emailData = [
                'customer_email' => $customerEmail,
                'order' => [
                    'id'                      => $orderId,
                    'order_date'              => $orderDate,
                    'estimated_delivery_date' => $estimatedDeliveryDate,
                    'sub_total_amount'        => $subtotal,
                    'discounted_total_amount' => $discount,
                    'total_taxed_amount'      => $gst,
                    'shipping_charged_amount' => $shippingCost,
                    'grand_total_amount'      => $grandTotal,
                    'payment_type'            => 'Store Credit',
                    'store_credit'            => $creditToUse,
                    'cod_charges'             => 0,
                    'customer_first_name'     => $billingAddress['first_name'] ?? ''
                ],
                'billing_address'  => $billingAddress,
                'shipping_address' => $shippingAddress,
                'products'         => $emailProducts
            ];

            $emailResult = $this->sendOrderEmail($emailData);
            
            if($emailResult['success']){

                return $this->response->setJSON([
                    'status'       => 'success',
                    'message'      => 'Your order has been placed successfully.',
                    'order_id'     => $orderId
                ]);
            }else{
                log_message("error", $emailResult['curl_error']);
                return $this->response->setJSON([
                    'status'       => 'success',
                    'message'      => 'Your order has been placed but email could not be sent',
                    'order_id'     => $orderId
                ]);
            }

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    //to place order with store credit
    public function placeOrderWithCod()
    {
        //check customer
        $customerId = session()->get('customer_id');
        if (!$customerId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'message' => 'Please log in to place your order.'
            ]);
        }
        
        
        //get order details 
        $products = json_decode((string) $this->request->getPost('products'),   true );
        $requestedCredit  = (float)$this->request->getPost('usedStoreCredit');
        $couponCode       = trim((string) $this->request->getPost('coupon_code'));
        $shippingAddressId = $this->request->getPost('shipping_address');
        $billingAddressId  = $this->request->getPost('billing_address');
        $orderComment      = trim((string) $this->request->getPost('order_comment'));
        $codCharges = (float)$this->request->getPost('codCharges');

        if (
            !is_array($products) || empty($products) ||
            !is_numeric($shippingAddressId) || !is_numeric($billingAddressId)
        ) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Invalid order details.'
            ]);
        }

        try {
            // Fetch the customer's credit and email in the same query.
            $customerRows = $this->a_access->custom_query(
                "SELECT store_credit, email
                 FROM Customer
                 WHERE id = " . (int) $customerId . "
                 LIMIT 1"
            );

            if (empty($customerRows)) {
                throw new \RuntimeException('Customer account was not found.');
            }

            $availableCredit = (float) $customerRows[0]['store_credit'];
            $customerEmail   = $customerRows[0]['email'] ?? '';
            
            // Fetch products, prices, stock, names and images.
            $subtotal = 0;
            $orderItems = [];
            $totalWeight = 0;

            $value = $this->buildOrderProducts($products);
            $orderItems=$value[0];
            $subtotal=$value[1];
            $totalWeight=$value[2];

            $subtotal = round($subtotal, 2);

            //validate the shipping and billing address
            $shippingAddress = $this->validateCustomerAddress($shippingAddressId, $customerId);
            $billingAddress = $this->validateCustomerAddress($billingAddressId, $customerId);

            // get pincode
            $pincode = trim((string) ($shippingAddress['zip'] ?? ''));
            if (  !preg_match('/^\d{6}$/', $pincode) || empty($shippingAddress['city']) ||  empty($shippingAddress['state'])  ) {
                throw new \RuntimeException(  'The shipping address has incomplete details.' );
            }
            
            //get id from order_addresess 
            $shippingOrderAddressId = $this->getOrCreateOrderAddress($shippingAddress);
            $billingOrderAddressId = $this->getOrCreateOrderAddress($billingAddress);

            // Calculate shipping.
            $shippingWeight = $totalWeight < 1  ? 1.2: $totalWeight + 0.5;

            //get shipping charges
            $shippingCost = $this->getShippingFee($pincode, $shippingWeight);
            if ($shippingCost === null) {
                throw new \RuntimeException(  'No courier service is available for this address.' );
            }

            $value = $this->getDiscount($couponCode,$subtotal,$customerId);
            $discount = $value[0];
            $couponId = $value[1];

            // Store-credit-only orders have no COD charges.
            $amountBeforeCredit = round(  max(0, $subtotal + $shippingCost +$codCharges - $discount),  2 );
            $creditToUse = round((float) $requestedCredit, 2);
            if ($creditToUse > $availableCredit) {
                throw new \RuntimeException(  'Your store-credit is less than applied credit.'  );
            }

            $gst = round($amountBeforeCredit * 0.18);
            $grandTotal = round($amountBeforeCredit + $gst - $creditToUse, 2);
            
            
            // Create the order.
            $orderDate = date('Y-m-d');

            $orderId = $this->a_access->insert_table_id('Orders', [
                'cust_id'                 => $customerId,
                'order_status_id'         => 1,
                'shipping_address_id'     => $shippingOrderAddressId,
                'billing_address_id'      => $billingOrderAddressId,
                'order_date'              => $orderDate,
                'sub_total_amount'        => $subtotal,
                'discounted_total_amount' => $discount,
                'total_taxed_amount'      => $gst,
                'shipping_charged_amount' => $shippingCost,
                'grand_total_amount'      => $grandTotal,
                'payment_type'            => 'COD',
                'order_comment'           => $orderComment,
                'coupon_id'               => $couponId,
                'store_credit'            => $creditToUse,
                'cod_charges'             => $codCharges,
                'order_currency'          => 1,
                'last_status_updated_date'=>$orderDate
            ]);

            // Save order products and decrease stock.
            foreach ($orderItems as $item) {
                //add product to order
                $inserted = $this->a_access->insert_table_id('Order_products', [
                    'order_id'      => $orderId,
                    'product_id'    => $item['product_id'],
                    'product_price' => $item['unit_price'],
                    'product_qty'   => $item['quantity'],
                    'status'        => 1
                ]);
            
                //update quantity
                $newQty = (int)($item['availableQty'] - $item['quantity']);
                
                //to check for on-hand
                if($newQty==0 ){
                    $this->a_access->update_table( 'Products_stock',
                        [ 'product_qty' =>$newQty,'on_hand' =>'N','instock'=>'N' ],
                        [ 'product_id' => $item['product_id']]
                    );
                }
                
                $this->a_access->update_table( 'Products_stock',
                        [ 'product_qty' =>$newQty ],
                        [ 'product_id' => $item['product_id']]
                );
            }

            if($creditToUse>0){
                // Deduct store credit
                $newStoreCredit = round($availableCredit - $creditToUse, 2);
                $this->a_access->update_table( 'Customer',
                    [ 'store_credit' =>$newStoreCredit ],
                    [ 'id' => $customerId]
                );
            }
                

            //change coupon if applied
            if($discount>0){
                $this->a_access->update_table( 'Coupon_master',
                    [ 'status' => 'N'  ],
                    [ 'id' => $couponId]  );
            }
                
            //clear cart
            $res = $this->clearCart((int) $customerId, $orderItems);
                
            //send confirmation email
            $emailProducts = [];
            foreach ($orderItems as $item) {
                $emailProducts[] = [
                    'product_name'  => $item['product_name'],
                    'main_image'    => !empty($item['main_image']) ? $item['main_image']: base_url('Images/icons/ic_product.png'),
                    'product_price' => (float) $item['unit_price'],
                    'product_qty'   => (int) $item['quantity'],
                    'line_total'    => round((float) $item['unit_price'] * (int) $item['quantity'],  2 )
                ];
            }
            
            $orderDateObj = new \DateTime($orderDate);
            $orderDateObj->modify('+1 month');
            $orderDateObj->modify('+1 week');
            $estimatedDeliveryDate = $orderDateObj->format('d M Y');

            $emailData = [
                'customer_email' => $customerEmail,
                'order' => [
                    'id'                      => $orderId,
                    'order_date'              => $orderDate,
                    'estimated_delivery_date' => $estimatedDeliveryDate,
                    'sub_total_amount'        => $subtotal,
                    'discounted_total_amount' => $discount,
                    'total_taxed_amount'      => $gst,
                    'shipping_charged_amount' => $shippingCost,
                    'grand_total_amount'      => $grandTotal,
                    'payment_type'            => 'COD',
                    'store_credit'            => $creditToUse,
                    'cod_charges'             => $codCharges,
                    'customer_first_name'     => $billingAddress['first_name'] ?? ''
                ],
                'billing_address'  => $billingAddress,
                'shipping_address' => $shippingAddress,
                'products'         => $emailProducts
            ];

            $emailResult = $this->sendOrderEmail($emailData);
            
            if($emailResult['success']){

                return $this->response->setJSON([
                    'status'       => 'success',
                    'message'      => 'Your order has been placed successfully.',
                    'order_id'     => $orderId
                ]);
            }else{
                log_message("error", $emailResult['curl_error']);
                return $this->response->setJSON([
                    'status'       => 'success',
                    'message'      => 'Your order has been placed but email could not be sent',
                    'order_id'     => $orderId
                ]);
            }

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    //to get cashfree session
    public function createCashFreeOrder(){

        //check customer
        $customerId = session()->get('customer_id');
        if (!$customerId) {
            return $this->response->setStatusCode(401)->setJSON([
                'success'  => 0,
                'message' => 'Please log in to place your order.'
            ]);
        }
        
        $data= $this->request->getJSON(true);
        
        //get user details
        $sql="Select * from Customer where id = ".$customerId;
        $customer = $this->a_access->custom_query($sql);
        
        //validate the shipping and billing address
        $shippingAddress = $this->validateCustomerAddress($data['shipping_address'], $customerId);
        $billingAddress = $this->validateCustomerAddress($data['billing_address'], $customerId);

        // get pincode
        $pincode = trim((string) ($shippingAddress['zip'] ?? ''));
        if (  !preg_match('/^\d{6}$/', $pincode) || empty($shippingAddress['city']) ||  empty($shippingAddress['state'])  ) {
                
            return $this->response->setStatusCode(401)->setJSON([
                'success'  => 0,
                'message' => 'Address is not corect.'
            ]);
        }
            
        //get id from order_addresess 
        $shippingOrderAddressId = $this->getOrCreateOrderAddress($shippingAddress);
        $billingOrderAddressId = $this->getOrCreateOrderAddress($billingAddress);
        
        //get shipping fee and subtotal
        $products = json_decode((string)$data['products'],true);
        $value= $this->buildOrderProducts($products);
        $orderItems=$value[0];
        $subtotal = $value[1];
        $totalWeight = $value[2];
        
        $totalWeight = $totalWeight <= 1 ? 1.2 : $totalWeight+0.5;
        $shippingfee = $this->getShippingFee($pincode, $totalWeight);
        
        //get discount
        $value = $this->getDiscount($data['coupon_code'],$subtotal,$customerId);
        $discount = max(0,$value[0]);
        $couponId=$value[1];

        //get store credit
        $sc = $data['usedStoreCredit'];
        $sc = max(0,$sc);
        if($sc>$customer[0]['store_credit']){
            return $this->response->setStatusCode(400)->setJSON([
                'success'  => 0,
                'message' => 'Used store credit is more than available store credit.'
            ]);
        }
       
        //get total amount
        $amount = round(  max(0, $subtotal + $shippingfee - $discount));
        $gst = round($amount * 0.18);
        $grandTotal = round($amount + $gst - $sc);
        
        //Create temp order.
            $orderDate = date('Y-m-d');

            $orderId = $this->a_access->insert_table_id('TOrders', [
                'cust_id'                 => $customerId,
                'order_status_id'         => 1,
                'shipping_address_id'     => $shippingOrderAddressId,
                'billing_address_id'      => $billingOrderAddressId,
                'order_date'              => $orderDate,
                'sub_total_amount'        => $subtotal,
                'discounted_total_amount' => $discount,
                'total_taxed_amount'      => $gst,
                'shipping_charged_amount' => $shippingfee,
                'grand_total_amount'      => $grandTotal,
                'payment_type'            => 'cashfree',
                'order_comment'           => $data['order_comment'],
                'coupon_id'               => $couponId,
                'store_credit'            => $sc,
                'cod_charges'             => 0,
                'order_currency'          => 1,
                'last_status_updated_date'=>$orderDate
            ]);

            // Save order products and decrease stock.
            foreach ($orderItems as $item) {
                //add product to order
                $inserted = $this->a_access->insert_table_id('TOrder_products', [
                    'order_id'      => $orderId,
                    'product_id'    => $item['product_id'],
                    'product_price' => $item['unit_price'],
                    'product_qty'   => $item['quantity']
                ]);
            
            }
            
            if(!empty($orderId) && !empty($inserted)){
            // get order id from cash free
            $orderResponse=$this->cpay->createorder($orderId, $grandTotal,$customerId,
                    $customer[0]['first_name']." ".$customer[0]['last_name'],
                    $customer[0]['email'],$shippingAddress['contact_no']);
            
             // Step 2: Check and return payment link
                if(isset($orderResponse['payment_session_id'])){

                    $jsonArr[0] = [
                        "success" => 1,
                        "payment_session_id" => $orderResponse['payment_session_id'],
                        "order_id"=>$orderId
                    ];
                    $jsonArr[1]=$orderResponse;

                } else {

                    $jsonArr[0] = [
                        "success" => 0,
                        "msg" => "Failed to create order",
                        "response" => $orderResponse
                    ];
                }

            } else {

                $jsonArr[0] = [
                    "success" => 0,
                    "msg" => "Could not find user details"
                ];
            }

            echo json_encode($jsonArr);
    }
    
    //payment verification
    public function paymentAuth($orderId){

        //to verify payment was done
      $result = $this->cpay->verifyPayment($orderId);
      $orderVal=[];

        //check
        if ($result && $result['order_status'] === 'PAID') {
                    
            //get order payment type details
            $payments = $this->cpay->getPaymentDetails($orderId);

            if (!empty($payments)) {

                $payment = $payments[0]; // latest payment
                $pmethod = $payment['payment_method']; //array
                $ptype=array_key_first($pmethod); //gets the payment method
            }
                    
            //get order details
            $result = $this->a_access->all_info("TOrders",["id"=>$orderId]);
            if($result){
                    foreach($result as $row){
                        $orderVal = array(
                                "cust_id" => $row["cust_id"],
                                "order_status_id" => 1,
                                "shipping_address_id" => $row["shipping_address_id"],
                                "billing_address_id" => $row["billing_address_id"],
                                "order_date" => date("Y-m-d"),
                                "last_status_updated_date" => date("Y-m-d"),
                                "sub_total_amount" => $row["sub_total_amount"],
                                "discounted_total_amount" => $row["discounted_total_amount"],
                                "total_taxed_amount" => $row["total_taxed_amount"],
                                "shipping_charged_amount" => $row["shipping_charged_amount"],
                                "grand_total_amount" => $row["grand_total_amount"],
                                "payment_type" => $ptype,
                                "order_comment" => $row["order_comment"],
                                "order_currency" => $row["order_currency"],
                                "store_credit" => $row["store_credit"],
                                "coupon_id" => $row["coupon_id"]
                            );
                        //store temp order id for delettion later 
                        $tempId=$orderId;
                        
                        //add to orders table
                        $order_id = $this->a_access->insert_table_id("Orders",$orderVal);

                        //insert products
                        $sql1 = "Select * from TOrder_products where order_id = ".$orderId;
                        $products = $this->a_access->custom_query($sql1);
                        
                        $emailProducts = [];
                        
                        if(!empty($products)){
                            foreach($products as $row1){
                                $val1 = array(
                                        "order_id" => $order_id,
                                        "product_id" => $row1["product_id"],
                                        "product_qty" => $row1["product_qty"],
                                        "product_price" => $row1["product_price"]
                                    );
                                                                        
                                $this->a_access->insert_table_id("Order_products",$val1);
                                
                                $pdetails = $this->a_access->custom_query("SELECT p.main_image, pd.product_name 
                                        FROM Products p 
                                        LEFT JOIN Product_description pd ON pd.product_id = p.id 
                                        WHERE p.id ='".$row1['product_id']."'");
                                
                                $emailProducts[] = [
                                    'product_name'  => $pdetails[0]['product_name'],
                                    'main_image'    => !empty($pdetails[0]['main_image']) ? $pdetails[0]['main_image']: base_url('Images/icons/ic_product.png'),
                                    'product_price' => (float) $row1['product_price'],
                                    'product_qty'   => (int) $row1['product_qty'],
                                    'line_total'    => round((float) $row1['product_price'] * (int) $row1['product_qty'],  2 )
                                ];
                                    

                                //modify stock
                                $sql4 = "Select * from Products_stock where product_id = '".$row1["product_id"]."'";
                                $result4 = $this->a_access->custom_query($sql4);
                                $newQty = (int)($result4[0]['product_qty'] - $row1['product_qty']);
                
                                //change qty
                                if($newQty==0){
                                    $this->a_access->update_table( 'Products_stock',
                                        [ 'product_qty' =>$newQty,'on_hand' =>'N','instock'=>'N' ],
                                        [ 'product_id' => $row1['product_id']]
                                    );
                                }
                
                                $this->a_access->update_table( 'Products_stock',
                                        [ 'product_qty' =>$newQty ],
                                        [ 'product_id' => $row1['product_id']]
                                );
                                
                                //build email product
                                                                       
                            }
                        }
                        $customer = $this->a_access->all_info("Customer",["id"=>$orderVal['cust_id']]);

                        //update user store credit
                        if($row["store_credit"] != 0){
                            $sc = max(0, (float)($customer[0]['store_credit'] - $row['store_credit']));
                            $this->a_access->update_table( 'Customer',
                                [ 'store_credit' =>$sc ],
                                [ 'id' => $row['cust_id']]
                            );
                            
                        }

                        //check for coupon code
                        if($row["coupon_id"]!=null){
                            
                            $this->a_access->update_table( 'Coupon_master',
                                [ 'status' => 'N'  ],
                                [ 'id' => $row["coupon_id"]]  );
                        }

                        //empty user cart
                        $res = $this->clearCart((int) $row["cust_id"], $products);
                        
                        //delete temp order
                        $this->a_access->delete_data("TOrder_products",["order_id"=>$tempId]);
                        $this->a_access->delete_data("TOrders",["id"=>$tempId]);
                        
                        //send email
            
                        $orderDateObj = new \DateTime($orderVal['order_date']);
                        $orderDateObj->modify('+1 month');
                        $orderDateObj->modify('+1 week');
                        $estimatedDeliveryDate = $orderDateObj->format('d M Y');
                        
                        $orderVal['id']=$order_id;
                        $orderVal['estimated_delivery_date']=$estimatedDeliveryDate;
                        $orderVal['cod_charges']=0;
                        $orderVal['customer_first_name']=$customer[0]['first_name'];
                        
                        //get shipping address
                        $shippingAddress = $this->a_access->all_info("Order_addresses",["id"=>$orderVal['shipping_address_id']]);
                        $billingAddress = $this->a_access->all_info("Order_addresses",["id"=>$orderVal['billing_address_id']]);

                        $emailData = [
                            'customer_email' => $customer[0]['email'],
                            'order' => $orderVal,
                            'billing_address'  => $shippingAddress[0],
                            'shipping_address' => $billingAddress[0],
                            'products'         => $emailProducts
                        ];

                        $this->sendOrderEmail($emailData);
                        
                        return redirect()->to(base_url('userhome'));
                    }
					
            } else {
		//order not found
		return redirect->to(base_url('cart'));
            }
                    
        } else {
            return redirect->to(base_url('cart'));
        }
    }
}
