<?php

namespace App\Controllers;

class UserController extends BaseController
{
    
    public function __construct() {
		$this->a_access = new \App\Models\Apiaccess();
                $this->pdf = new \App\Models\DomPDFModel();
    } 
    
    
    /* ---------------------------------
     * -------- Account Home Page -------
    ------------------------------------- */
    
    
    //to open account home page
    public function accountHome()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId){
            return redirect()->to(base_url('signin'));
        }

        $sql = "SELECT  id, first_name, last_name, email, redeem_points_balance, store_credit, acc_status
                FROM Customer
                WHERE id = '" . $customerId . "'
                LIMIT 1";

        $customer = $this->a_access->custom_query($sql);

        if(empty($customer)){
            return redirect()->to(base_url('signin'));
        }

        $customer = $customer[0];
        
        //to get active coupons
        $couponSql = "SELECT coupon_code, amount_type, amount, expiry_date, coupon_type
              FROM Coupon_master
              WHERE status = 'Y'
              AND (expiry_date IS NULL OR expiry_date >= CURDATE())
              AND (
                  coupon_type = 'global'
                  OR (coupon_type = 'customer' AND cust_id = '" . $customerId . "')
              )
              ORDER BY created_date DESC";

        $coupons = $this->a_access->custom_query($couponSql);

        return view('user_home', [
            'customer' => $customer,
            'coupons' => $coupons,
            'account_view'=>'account/acc_home'
        ]);
    }
    
    //to open home page from side bar
    public function accountHomeContent()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId){
            return '';
        }

        $sql = "SELECT id, first_name, last_name,  email, redeem_points_balance, store_credit,  acc_status
                FROM Customer
                WHERE id = '" . $customerId . "'
                LIMIT 1";

        $customer = $this->a_access->custom_query($sql);

        if(empty($customer)){
            return '';
        }

        $customer = $customer[0];

        // Get active coupons
        $couponSql = "SELECT  coupon_code,   amount_type, amount, expiry_date, coupon_type
                      FROM Coupon_master
                      WHERE status = 'Y'
                      AND (expiry_date IS NULL OR expiry_date >= CURDATE())
                      AND ( coupon_type = 'global' OR (
                              coupon_type = 'customer'
                              AND cust_id = '" . $customerId . "' )
                            )
                      ORDER BY created_date DESC";

        $coupons = $this->a_access->custom_query($couponSql);

        return view('account/acc_home', [
            'customer' => $customer,
            'coupons' => $coupons
        ]);
    }
    
    //to logout user
    public function logout()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'You are already logged out.'
            ]);
        }

        session()->remove([
            'customer_id',
            'customer_name'
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'You have been logged out successfully.'
        ]);
    }
    
    //to open edit profile page
    public function editProfile()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId){
            return redirect()->to(base_url('signin'));
        }

        $firstName = $this->request->getPost('first_name');
        $lastName  = $this->request->getPost('last_name');

        return view('edit_profile', [
            'first_name' => $firstName,
            'last_name'  => $lastName
        ]);
    }
    
    //to update user names
    public function updateProfile()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId){
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Please sign in again.'
            ]);
        }

        $firstName = trim($this->request->getPost('first_name'));
        $lastName  = trim($this->request->getPost('last_name'));

        if($firstName == ''){
            return $this->response->setJSON([
                'status' => false,
                'message' => 'First name is required.'
            ]);
        }
        if($lastName == ''){
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Last name is required.'
            ]);
        }

        $updateSql = "UPDATE Customer  SET first_name = '" . addslashes($firstName) . "', last_name = '" . addslashes($lastName) . "'
                      WHERE id = '" . $customerId . "'";
        $this->a_access->custom_query_update($updateSql);

        // Update session name as well
        session()->set('customer_name', $firstName);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Your profile has been updated successfully.'
        ]);
    }
    
    //to change user password
    public function changePassword()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Please sign in again.' ]);
        }

        $oldPassword = trim($this->request->getPost('old_password'));
        $newPassword = trim($this->request->getPost('new_password'));

        if($oldPassword == '')
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Please enter your old password.' ]);
        }

        if($newPassword == '')
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Please enter your new password.' ]);
        }

        $sql = "SELECT customer_pass
                FROM Customer
                WHERE id = '" . $customerId . "' LIMIT 1";

        $customer = $this->a_access->custom_query($sql);

        if(empty($customer))
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Customer account not found.'  ]);
        }

        if(md5($oldPassword) != $customer[0]['customer_pass'])
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'The old password you entered is incorrect.' ]);
        }

        $newPasswordHash = md5($newPassword);

        $updateSql = "UPDATE Customer
                      SET customer_pass = '" . $newPasswordHash . "'
                      WHERE id = '" . $customerId . "'";

        $this->a_access->custom_query_update($updateSql);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Your password has been changed successfully.' ]);
    }
    
    //to redeem points
    public function redeemReward()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Please sign in again.'
            ]);
        }

        $points = (int)$this->request->getPost('points');
        $amount = (float)$this->request->getPost('amount');

        $rewards = [
            500   => 5,
            1000  => 10,
            2000  => 20,
            5000  => 50,
            10000 => 100
        ];

        if(!isset($rewards[$points]) || $rewards[$points] != $amount)
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid reward selected.'
            ]);
        }

        $sql = "SELECT redeem_points_balance, store_credit
                FROM Customer
                WHERE id = '" . $customerId . "'
                LIMIT 1";

        $customer = $this->a_access->custom_query($sql);

        if(empty($customer))
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Customer account not found.'
            ]);
        }

        $currentPoints = (int)$customer[0]['redeem_points_balance'];
        $currentCredit = (float)$customer[0]['store_credit'];

        if($currentPoints < $points)
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'You do not have enough reward points for this reward.'
            ]);
        }

        $newPoints = $currentPoints - $points;
        $newCredit = $currentCredit + $amount;

        $updateSql = "UPDATE Customer
                      SET redeem_points_balance = '" . $newPoints . "', store_credit = '" . number_format($newCredit, 2, '.', '') . "'
                      WHERE id = '" . $customerId . "'";

        $this->a_access->custom_query_update($updateSql);

        return $this->response->setJSON([
            'status' => true,
            'message' => '₹' . number_format($amount, 2) . ' store credit has been added to your account.'
        ]);
    }
    
    
     
    /* ---------------------------------
     * -------- Account Address Page -------
    ------------------------------------- */
    
    //open account addresses list
    public function accountAddresses()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId){
            return redirect()->to(base_url('signin'));
        }

        $sql = "SELECT id, first_name, last_name,address_line1, address_line2, city, state, country, zip,
                    company_name, company_gstin, contact_no,  status, address_title, is_default
                FROM Customer_addresses
                WHERE cust_id = '" . $customerId . "'
                AND status = 'Y'
                ORDER BY id DESC";

        $addresses = $this->a_access->custom_query($sql);

        return view('account/acc_address', [
            'addresses' => $addresses
        ]);
    }
    
    //to set default address
    public function setDefaultAddress()
    {
        $customerId = session()->get('customer_id');
        $addressId = $this->request->getPost('address_id');

        if(!$customerId || !$addressId)
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid address.'
            ]);
        }

        // Check that the address belongs to the logged-in customer
        $address = $this->a_access->single_info(
            'Customer_addresses',
            ['id'],
            [
                'id' => $addressId,
                'cust_id' => $customerId,
                'status' => 'Y'
            ]
        );

        if(empty($address))
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Address not found.'
            ]);
        }

        // Remove default status from all addresses of this customer
        $this->a_access->update_table(
            'Customer_addresses',
            [
                'is_default' => 'N'
            ],
            [
                'cust_id' => $customerId
            ]
        );

        // Set selected address as default
        $this->a_access->update_table(
            'Customer_addresses',  [ 'is_default' => 'Y' ],
            [ 'id' => $addressId,
                'cust_id' => $customerId ]
        );

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Default address updated successfully.'
        ]);
    }
    
    //to open add address popup
    public function accountAddAddress()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId){
            return '';
        }

        return view('account/acc_add_address');
    }
    
    //save new account address
    public function saveAccountAddress()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Your session has expired. Please sign in again.'
            ]);
        }

        $addressTitle  = trim($this->request->getPost('address_title'));
        $firstName     = trim($this->request->getPost('first_name'));
        $lastName      = trim($this->request->getPost('last_name'));
        $contactNo     = trim($this->request->getPost('contact_no'));
        $addressLine1  = trim($this->request->getPost('address_line1'));
        $addressLine2  = trim($this->request->getPost('address_line2'));
        $country       = trim($this->request->getPost('country'));
        $state         = trim($this->request->getPost('state'));
        $city          = trim($this->request->getPost('city'));
        $zip           = trim($this->request->getPost('zip'));
        $companyName   = trim($this->request->getPost('company_name'));
        $companyGstin  = trim($this->request->getPost('company_gstin'));
        $isDefault     = $this->request->getPost('is_default') == 'Y' ? 'Y' : 'N';


        //check duplicate phone number
         $phoneExists = $this->a_access->if_exist(
             'Customer_addresses',
             'contact_no',
             $contactNo
         );

         if($phoneExists)
         {
             return $this->response->setJSON([
                 'status'  => false,
                 'message' => 'An account or address already exists with this mobile number.'
             ]);
         }
        
        
        if($isDefault == 'Y')
        {
            $this->a_access->update_table(
                'Customer_addresses',
                [
                    'is_default' => 'N'
                ],
                [
                    'cust_id' => $customerId
                ]
            );
        }


        // Address data
        $addressData = [
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'address_line1' => $addressLine1,
            'address_line2' => ($addressLine2 != '' ? $addressLine2 : null),
            'city'          => $city,
            'state'         => $state,
            'country'       => 'India',
            'zip'           => $zip,
            'company_name'  => ($companyName != '' ? $companyName : null),
            'company_gstin' => ($companyGstin != '' ? $companyGstin : null),
            'contact_no'    => $contactNo,
            'cust_id'       => $customerId,
            'status'        => 'Y',
            'address_title' => $addressTitle,
            'is_default'    => $isDefault
        ];

        $inserted = $this->a_access->insert_table( 'Customer_addresses', $addressData  );

        if(!$inserted)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unable to save your address. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Your address has been saved successfully.'
        ]);
    }
    
    //delete account address
    public function deleteAccountAddress()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Your session has expired. Please sign in again.'
            ]);
        }

        $addressId = trim($this->request->getPost('address_id'));

        if($addressId == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid address.'
            ]);
        }


        // Make sure this address belongs to this customer
        $address = $this->a_access->single_info(
            'Customer_addresses',
            ['id'],
            [
                'id'      => $addressId,
                'cust_id' => $customerId,
                'status'  => 'Y'
            ]
        );

        if(empty($address))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Address not found.'
            ]);
        }

        // Permanently delete the address
        $deleted = $this->a_access->custom_query_delete(
            "DELETE FROM Customer_addresses
             WHERE id = '" . $addressId . "'
             AND cust_id = '" . $customerId . "'
             LIMIT 1"
        );

        if(!$deleted)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unable to delete this address. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Address deleted successfully.'
        ]);
    }
    
    //open address edit popup
    public function editAccountAddress()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Your session has expired. Please sign in again.'
            ]);
        }

        $addressId = trim($this->request->getPost('address_id'));

        if($addressId == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid address.'
            ]);
        }

        $address = $this->a_access->single_info(
            'Customer_addresses',
            [
                'id',
                'first_name',
                'last_name',
                'address_line1',
                'address_line2',
                'city',
                'state',
                'country',
                'zip',
                'company_name',
                'company_gstin',
                'contact_no',
                'address_title',
                'is_default'
            ],
            [
                'id'      => $addressId,
                'cust_id' => $customerId,
                'status'  => 'Y'
            ]
        );

        if(empty($address))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Address not found.'
            ]);
        }

        return view('account/acc_add_address', [
            'address'   => $address[0],
            'editMode'  => true
        ]);
    }
    
    //update account address
    public function updateAccountAddress()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Your session has expired. Please sign in again.'
            ]);
        }

        $addressId = trim($this->request->getPost('address_id'));
        $addressTitle = trim($this->request->getPost('address_title'));
        $firstName    = trim($this->request->getPost('first_name'));
        $lastName     = trim($this->request->getPost('last_name'));
        $contactNo    = trim($this->request->getPost('contact_no'));
        $addressLine1 = trim($this->request->getPost('address_line1'));
        $addressLine2 = trim($this->request->getPost('address_line2'));
        $state        = trim($this->request->getPost('state'));
        $city         = trim($this->request->getPost('city'));
        $zip          = trim($this->request->getPost('zip'));
        $companyName  = trim($this->request->getPost('company_name'));
        $companyGstin = trim($this->request->getPost('company_gstin'));
        $isDefault    = $this->request->getPost('is_default') == 'Y' ? 'Y' : 'N';

        if($addressId == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid address.'
            ]);
        }

        // Verify address belongs to this customer
        $existingAddress = $this->a_access->single_info(
            'Customer_addresses',
            ['id'],
            [
                'id'      => $addressId,
                'cust_id' => $customerId,
                'status'  => 'Y'
            ]
        );

        if(empty($existingAddress))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Address not found.'
            ]);
        }

        if($addressTitle == '' || $firstName == '' || $lastName == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please fill in all required fields.'
            ]);
        }

        if(!preg_match('/^[0-9]{10}$/', $contactNo))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please enter a valid 10-digit phone number.'
            ]);
        }

        //check duplicate phone number
        $duplicatePhone = $this->a_access->custom_query(
            "SELECT id
             FROM Customer_addresses
             WHERE contact_no = '" . $contactNo . "'
             AND id != '" . $addressId . "'
             LIMIT 1"
        );

        if(!empty($duplicatePhone))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'This mobile number is already being used by another address. Please use a different number.'
            ]);
        }

        //check default address
        if($isDefault == 'Y')
        {
            $this->a_access->update_table( 'Customer_addresses', [ 'is_default' => 'N'  ], [ 'cust_id' => $customerId ] );
        }

        $updateData = [
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'address_line1' => $addressLine1,
            'address_line2' => ($addressLine2 != '' ? $addressLine2 : null),
            'city'          => $city,
            'state'         => $state,
            'country'       => 'India',
            'zip'           => $zip,
            'company_name'  => ($companyName != '' ? $companyName : null),
            'company_gstin' => ($companyGstin != '' ? $companyGstin : null),
            'contact_no'    => $contactNo,
            'address_title' => $addressTitle,
            'is_default'    => $isDefault
        ];

        $updated = $this->a_access->update_table(
            'Customer_addresses',
            $updateData,
            [
                'id'      => $addressId,
                'cust_id' => $customerId
            ]
        );

        if($updated === false)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unable to update your address. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Your address has been updated successfully.'
        ]);
    }
    
    
    
    /* ---------------------------------
     * -------- Account Orders Page -------
    ------------------------------------- */
    
    //to fetch orders list
    public function accountOrders()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return '';
        }

        //get order list
        $orderSql = "  SELECT o.id, o.cust_id, o.order_status_id, o.order_date, o.grand_total_amount, o.payment_type,
                os.order_status
            FROM Orders o
            LEFT JOIN Order_status_master os
                ON os.id = o.order_status_id
            WHERE o.cust_id = '" . $customerId . "'
            ORDER BY o.order_date DESC, o.id DESC ";

        $orders = $this->a_access->custom_query($orderSql);

        //add info to each order
        foreach($orders as &$order)
        {
            //payment type
            if(strtolower(trim($order['payment_type'])) == 'cod')
            {
                $order['payment_label'] = 'COD';
            }
            else
            {
                $order['payment_label'] = 'Prepaid';
            }

            //calculate deliuvery date
            $orderDate = new \DateTime($order['order_date']);

            $deliveryStart = clone $orderDate;
            $deliveryStart->modify('+1 month');

            $deliveryEnd = clone $deliveryStart;
            $deliveryEnd->modify('+4 days');

            $order['delivery_start'] = $deliveryStart->format('d M Y');
            $order['delivery_end']   = $deliveryEnd->format('d M Y');


            //no of items
            $itemSql = " SELECT SUM(product_qty) AS item_count
                FROM Order_products
                WHERE order_id = '" . $order['id'] . "' ";

            $itemResult = $this->a_access->custom_query($itemSql);

            $order['item_count'] = !empty($itemResult) ? (int)$itemResult[0]['item_count'] : 0;

            //get main product image
            $productSql = " SELECT p.id,  p.main_image,
                    pd.product_name
                FROM Order_products op
                LEFT JOIN Products p ON p.id = op.product_id
                LEFT JOIN Product_description pd ON pd.product_id = p.id
                WHERE op.order_id = '" . $order['id'] . "'
                ORDER BY op.id ASC
                LIMIT 1 ";

            $productResult = $this->a_access->custom_query($productSql);

            if(!empty($productResult))
            {
                $order['main_image']  = $productResult[0]['main_image'];
                $order['product_name'] = $productResult[0]['product_name'];
            }
            else
            {
                $order['main_image']  = '';
                $order['product_name'] = '';
            }
        }

        unset($order);

        return view('account/acc_orders', [
            'orders' => $orders
        ]);
    }
    
    //to open account order details
    public function orderDetails()
    {
        $customerId = session()->get('customer_id');
        $orderId = $this->request->getPost("order_id");

        if (!$customerId || !$orderId) {
            return '';
        }

        //get order details
        $orderSql = "
            SELECT o.*,
                os.order_status
            FROM Orders o
            LEFT JOIN Order_status_master os ON os.id = o.order_status_id
            WHERE o.id = '" . $orderId . "'
            AND o.cust_id = '" . $customerId . "'
            LIMIT 1 ";

        $orderResult = $this->a_access->custom_query($orderSql);

        if (empty($orderResult)) {
            return '';
        }

        $order = $orderResult[0];

        //get billing address
        $billingSql = "
            SELECT id, first_name, last_name, address_line1, address_line2, company_name, company_gstin,  city, state, country, zip, contact_no
            FROM Order_addresses
            WHERE id = '" . $order['billing_address_id'] . "'
            LIMIT 1 ";
        $billingResult = $this->a_access->custom_query($billingSql);
        $billingAddress = !empty($billingResult) ? $billingResult[0]  : [];

        //shipping address
        $shippingSql = "
            SELECT id,  first_name, last_name, address_line1, address_line2, company_name,  company_gstin, city, state, country,  zip, contact_no
            FROM Order_addresses
            WHERE id = '" . $order['shipping_address_id'] . "'
            LIMIT 1 ";
        $shippingResult = $this->a_access->custom_query($shippingSql);
        $shippingAddress = !empty($shippingResult)  ? $shippingResult[0]  : [];

        //get products
        $productSql = "
            SELECT  op.id, op.order_id, op.product_id, op.product_price, op.product_qty,  op.status,
                p.main_image,
                pd.product_name
            FROM Order_products op
            LEFT JOIN Products p  ON p.id = op.product_id
            LEFT JOIN Product_description pd ON pd.product_id = p.id
            WHERE op.order_id = '" . $orderId . "'
            ORDER BY op.id ASC     ";
        
        $products = $this->a_access->custom_query($productSql);

        //get last totals
        foreach ($products as &$product) {
            $product['line_total'] = (float)$product['product_price'] * (int)$product['product_qty'];
        }
        unset($product);


        //get payment type
        $order['payment_label']=$order['payment_type'];

        //calculate delivery date
        $orderDate = new \DateTime($order['order_date']);
        $deliveryStart = clone $orderDate;
        $deliveryStart->modify('+1 month');
        $deliveryEnd = clone $deliveryStart;
        $deliveryEnd->modify('+4 days');
        $order['delivery_start'] = $deliveryStart->format('d M Y');
        $order['delivery_end']   = $deliveryEnd->format('d M Y');

        return view('account/acc_order_details', [
            'order'           => $order,
            'billingAddress'  => $billingAddress,
            'shippingAddress' => $shippingAddress,
            'products'        => $products
        ]);
    }
    
    //to get Invoice ID
    public function getInvoiceID()
    {
        $orderId = (int) $this->request->getPost('order_id');

        if (!$orderId) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid order ID.'
                ]);
        }

        //get customer id
        $customerId = (int) session()->get('customer_id');

        if (!$customerId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Please login to continue.'
                ]);
        }

        //make sure this order belongs to this customer
        $sql = "SELECT id
                FROM Orders
                WHERE id = ".$orderId."
                  AND cust_id = ".$customerId."
                LIMIT 1";

        $orderResult = $this->a_access->custom_query($sql);

        if (empty($orderResult)) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Order not found.'
                ]);
        }


        //check if invoice already exists
        $sql = "SELECT inID, iDate
            FROM Invoices
            WHERE oID = ".$orderId."
            LIMIT 1";

        $invoiceResult = $this->a_access->custom_query($sql);

        if (!empty($invoiceResult)) {

            return $this->response
                ->setJSON([
                    'status'       => 'existing',
                    'invoice_id'   => $invoiceResult[0]['inID'],
                    'invoice_date' => $invoiceResult[0]['iDate']
                ]);
        }


        //create new invoice number
        $currentMonth = (int) date('m');
        $currentYear  = (int) date('Y');

        if ($currentMonth >= 4) {
            $financialYearStart = $currentYear;
        } else {
            $financialYearStart = $currentYear - 1;
        }

        $financialYearEnd = $financialYearStart + 1;

        $fyStart = $financialYearStart . '-04-01 00:00:00';
        $fyEnd   = $financialYearEnd . '-04-01 00:00:00';


        //get latest invoice number for this financial year
        $sql = "SELECT MAX(
                    CAST(
                        SUBSTRING_INDEX(inID, '_', -1)
                        AS UNSIGNED
                    )
                ) AS last_number
                FROM Invoices
                WHERE iDate >= '".$fyStart."'
                  AND iDate < '".$fyEnd."'";

        $numberResult = $this->a_access->custom_query($sql);

        $lastNumber = 0;

        if (
            !empty($numberResult) &&
            isset($numberResult[0]['last_number']) &&
            $numberResult[0]['last_number'] !== null
        ) {
            $lastNumber = (int) $numberResult[0]['last_number'];
        }

        $nextNumber = $lastNumber + 1;


        //generate invoice ID
        $invoiceId = '#BTB' . date('Y_m_d') . '_' . $nextNumber;

        $invoiceDate = date('Y-m-d H:i:s');


        //insert invoice
        $this->a_access->insert_table(
            'Invoices',
            [
                'oID'   => $orderId,
                'iDate' => $invoiceDate,
                'isP'   => 0,
                'inID'  => $invoiceId
            ]
        );


        return $this->response
            ->setJSON([
                'status'      => 'created',
                'invoice_id'  => $invoiceId,
                'invoice_date'=> $invoiceDate
            ]);
    }

    //to generate invoice pdf
   public function generateInvoice()
{
    $invoiceData = $this->request->getJSON(true);

    if (empty($invoiceData)) {
        return $this->response
            ->setStatusCode(400)
            ->setBody('Invalid invoice data.');
    }

    $invoiceId = $invoiceData['invoice_id'] ?? '';

    if (empty($invoiceId)) {
        return $this->response
            ->setStatusCode(400)
            ->setBody('Invalid invoice ID.');
    }

    log_message('error', 'GENERATE INVOICE DATA: ' . print_r($invoiceData, true));

    try {

        $pdf = $this->pdf->createInvoice($invoiceData);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader(
                'Content-Disposition',
                'inline; filename="KPM_Invoice_' . $invoiceData['order']['id'] . '.pdf"'
            )
            ->setBody($pdf);

    } catch (\Throwable $e) {

        log_message(
            'error',
            'GENERATE INVOICE ERROR: ' .
            $e->getMessage() .
            ' | FILE: ' .
            $e->getFile() .
            ' | LINE: ' .
            $e->getLine()
        );

        return $this->response
            ->setStatusCode(500)
            ->setBody(
                'PDF generation error: ' .
                $e->getMessage()
            );
    }
}
    
}