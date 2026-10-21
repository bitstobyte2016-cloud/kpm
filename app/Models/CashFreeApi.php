<?php

namespace App\Models;

use CodeIgniter\Model;

class CashFreeApi extends Model
{

    protected $appId;
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        parent::__construct();

        $this->appId = env('CASHFREE_APP_ID');
        $this->secretKey = env('CASHFREE_SECRET_KEY');
        $this->baseUrl = env('CASHFREE_BASE_URL');
    }
    private $kmp = "https://kpm.kpopmerch.in/";
    
    
    // ===== CREATE ORDER =====
    public function createOrder($orderId, $amount, $cid,$cname,$customerEmail, $customerPhone) {
        $data = [
            "order_id" => $orderId."",
            "order_amount" => $amount,
            "order_currency" => "INR",
            "customer_details" => [
                "customer_id" => $cid,
                "customer_name" => $cname,
                "customer_email" => $customerEmail,
                "customer_phone" => $customerPhone
            ],
            "order_meta" => [
                "return_url" => "https://kpm.kpopmerch.in/payment_return/{order_id}"
            ]
        ];

        $headers = [
            "Content-Type: application/json",
            "x-client-id: " . $this->appId,
            "x-client-secret: " . $this->secretKey,
            "x-api-version: 2026-01-01"
        ];

        $ch = curl_init($this->baseUrl);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        
        curl_close($ch);

        return json_decode($response, true);
    }

    // ===== REDIRECT USER =====
    public function redirectToPayment($orderResponse) {

        if (isset($orderResponse['payment_link'])) {
            header("Location: " . $orderResponse['payment_link']);
            exit;
        }

        // fallback
        echo "Error creating payment link";
        exit;
    }

    // ===== VERIFY PAYMENT =====
    public function verifyPayment($orderId) {

        $headers = [
            "Content-Type: application/json",
            "x-client-id: " . $this->appId,
            "x-client-secret: " . $this->secretKey,
            "x-api-version: 2026-01-01"
        ];

        $ch = curl_init($this->baseUrl . "/" . $orderId);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
    
    public function getPaymentDetails($orderId)
    {
        $url = "https://api.cashfree.com/pg/orders/".$orderId."/payments";

        $headers = [
            "Content-Type: application/json",
            "x-client-id: " . $this->appId,
            "x-client-secret: " . $this->secretKey,
            "x-api-version: 2026-01-01"
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}