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
            'page_title' => 'Account | K-Pop Merch',
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
        $isCheckout = $this->request->getPost('isCheckout') === 'true';

        if(!$customerId){
            return '';
        }

        return view('account/acc_add_address',[
            'isCheckout'=>$isCheckout
        ]);
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
        
        //get order chat
        $sql = "SELECT id, order_id, order_message, sender_id, message_date, read_status
                FROM Order_messaging
                WHERE order_id = ".$orderId."
                ORDER BY id ASC";

        $orderMessages = $this->a_access->custom_query($sql);
        

        return view('account/acc_order_details', [
            'page_title' => 'Order Details | K-Pop Merch',
            'order'           => $order,
            'billingAddress'  => $billingAddress,
            'shippingAddress' => $shippingAddress,
            'products'        => $products,
            'order_messages'   => $orderMessages
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
                        AS UNSIGNED )
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

        try {
            $pdf = $this->pdf->createInvoice($invoiceData);
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader( 'Content-Disposition', 'inline; filename="KPM_Invoice_' . $invoiceData['order']['id'] . '.pdf"' )
                ->setBody($pdf);

        } catch (\Throwable $e) {
            
            return $this->response
                ->setStatusCode(500)
                ->setBody('PDF generation error: ' . $e->getMessage() );
        }
    }
    
    //to add new message to order
    public function sendOrderMessage()
    {
        $orderId = (int) $this->request->getPost('order_id');
        $message = trim($this->request->getPost('message'));

        //check order ID
        if (!$orderId) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Invalid order ID.'
                ]);
        }

        //get customer ID
        $customerId = (int) session()->get('customer_id');

        if (!$customerId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Please login to continue.'
                ]);
        }

        //check message
        if ($message === '') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Please enter a message.'
                ]);
        }

        //limit message length
        if (mb_strlen($message) > 2000) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Message cannot exceed 2000 characters.'
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

        //insert message
        $this->a_access->insert_table(
            'Order_messaging',
            [
                'order_id'      => $orderId,
                'order_message' => $message,
                'sender_id'     => $customerId,
                'message_date'  => date('Y-m-d'),
                'read_status'   => 'unread'
            ]
        );

        return $this->response
            ->setJSON([
                'status'  => 'success',
                'message' => 'Message sent successfully.'
            ]);
    }
    
    public function getOrderMessages()
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


        //get customer ID
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


        //get order messages
        $sql = "SELECT
                    id,
                    order_id,
                    order_message,
                    sender_id,
                    message_date,
                    read_status
                FROM Order_messaging
                WHERE order_id = ".$orderId."
                ORDER BY id ASC";

        $messages = $this->a_access->custom_query($sql);


        //format dates for display
        foreach ($messages as &$message) {

            $message['formatted_date'] = date(
                'd M Y',
                strtotime($message['message_date'])
            );
        }

        unset($message);


        return $this->response
            ->setJSON([
                'status'   => 'success',
                'messages' => $messages
            ]);
    }
    
    //to cancel order 
    public function cancelOrder()
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

        // Get customer ID
        $customerId = (int) session()->get('customer_id');
        if (!$customerId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Please login to continue.'
                ]);
        }

        // Get order
        $sql = "SELECT *
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
        $order = $orderResult[0];

        // Get order products
        $sql = "SELECT *
                FROM Order_products
                WHERE order_id = ".$orderId;
        $orderProducts = $this->a_access->custom_query($sql);

        // Calculate previously refunded amount
        $previouslyRefundedAmount = 0;
        if (!empty($orderProducts)) {
            foreach ($orderProducts as $product) {
                if (isset($product['status']) && $product['status'] === 'refunded' ) {
                    $productPrice = (float) $product['product_price'];
                    $productQty   = (int) $product['product_qty'];
                    $previouslyRefundedAmount +=$productPrice * $productQty;
                }
            }
        }

        // Order payment information
        $paymentType = strtolower( trim((string) $order['payment_type']) );
        $grandTotal = (float) $order['grand_total_amount'];
        $storeCreditUsed = !empty($order['store_credit']) ? (float) $order['store_credit'] : 0;

        //calculate store credit refund
        $storeCreditRefund = 0;

        // COD order
        if ($paymentType === 'cod') {
            $storeCreditRefund = $storeCreditUsed;
        }
        // Prepaid order
        else {
            $storeCreditRefund = max(  0, $grandTotal - $previouslyRefundedAmount );
        }

        //fetch customer's store credit
        $sql = "SELECT store_credit
                FROM Customer
                WHERE id = ".$customerId."
                LIMIT 1";
        $customerResult = $this->a_access->custom_query($sql);

        if (empty($customerResult)) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Customer not found.'
                ]);
        }

        $currentStoreCredit = (float) $customerResult[0]['store_credit'];

        //calculate new store credit
        if ($storeCreditRefund > 0) {
            $newStoreCredit = $currentStoreCredit + $storeCreditRefund;
            $this->a_access->update_table("Customer",['store_credit'=>$newStoreCredit],['id'=>$customerId]);
        }

        //get cancelled status ID
        $sql = "SELECT id
                FROM Order_status_master
                WHERE LOWER(order_status) = 'cancelled'
                LIMIT 1";
        $statusResult = $this->a_access->custom_query($sql);

        if (empty($statusResult)) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Cancelled order status could not be found.'
                ]);
        }
        $cancelledStatusId = (int) $statusResult[0]['id'];

        //update order status
        $this->a_access->update_table("Orders",['order_status_id'=>$cancelledStatusId],['cust_id'=>$customerId , 'id'=>$orderId]);

        //return

        return $this->response
            ->setJSON([
                'status'                  => 'success',
                'message'                 => 'Order cancelled successfully.',
                'store_credit_refunded'   => number_format(
                    $storeCreditRefund,
                    2,
                    '.',
                    ''
                ),
                'previously_refunded'     => number_format(
                    $previouslyRefundedAmount,
                    2,
                    '.',
                    ''
                )
            ]);
    }
    
    //to send email for cancelled order
    public function emailCancelOrder()
    {
        //get logged-in customer
        $customerId = session()->get('customer_id');
        if (!$customerId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Please login to continue.'
                ]);
        }

        //get data sent from JS
        $data = $this->request->getJSON(true);
        if (empty($data)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Invalid email data.'
                ]);
        }

        //get order
        $order = $data['order'] ?? [];
        if (
            empty($order) ||
            empty($order['id'])
        ) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Order information is missing.'
                ]);
        }

        //make sure this order belongs to logged-in customer
        if (
            !isset($order['cust_id']) ||
            (int)$order['cust_id'] !== (int)$customerId
        ) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => false,
                    'message' => 'You are not authorised to send this email.'
                ]);
        }

        //make sure customer email exists
        if (
            empty($data['customer_email'])
        ) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Customer email is missing.'
                ]);
        }

        //send email through BrevoEmail model
        $brevoEmail = new \App\Models\BrevoEmail();
        $emailResult = $brevoEmail->sendCancelOrderEmail( $data   );

        //email sent successfully
        if (
            !empty($emailResult) &&
            isset($emailResult['success']) &&
            $emailResult['success'] === true
        ) {
            return $this->response
                ->setJSON([
                    'status'  => true,
                    'message' => 'Cancellation email sent successfully.'
                ]);
        }

        //email failed
        return $this->response
            ->setStatusCode(500)
            ->setJSON([
                'status'  => false,
                'message' => 'Unable to send cancellation email.',
                'response' => $emailResult
            ]);
    }
    
    
    
    /* ---------------------------------
     * -------- Account Whishlist Page -------
    ------------------------------------- */
    
    
    //to get customer wishlists
    public function accountWishlists()
    {
        //get customer id
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return redirect()->to(base_url('signin'));
        }

        //get wishlists
        $sql = "SELECT id, wishlist_name
                FROM Wishlists_master
                WHERE cust_id = ".$customerId."
                  AND status = 'Y'
                ORDER BY id DESC";
        $wishlistResult = $this->a_access->custom_query($sql);

        $wishlists = [];
        if(!empty($wishlistResult))
        {
            foreach($wishlistResult as $wishlist)
            {
                $wishlistId = (int)$wishlist['id'];

                //get wishlist products
                $sql = "SELECT wd.product_id,
                            p.main_image,
                            pd.product_name
                        FROM Wishlist_details wd
                        LEFT JOIN Products p   ON p.id = wd.product_id
                        LEFT JOIN Product_description pd  ON pd.product_id = p.id
                        WHERE wd.wishlist_id = ".$wishlistId."
                        ORDER BY wd.id ASC";
                $wishlistProducts = $this->a_access->custom_query($sql);

                $itemCount = count($wishlistProducts);

                //first 4 products for preview
                $previewProducts = array_slice( $wishlistProducts,  0,  4 );
                $remainingCount = max(  0,  $itemCount - count($previewProducts)  );

                $wishlists[] = [
                    'id'              => $wishlistId,
                    'wishlist_name'   => $wishlist['wishlist_name'],
                    'item_count'      => $itemCount,
                    'products'        => $previewProducts,
                    'remaining_count' => $remainingCount
                ];
            }
        }


        return view('account/acc_wishlist', [
            'wishlists' => $wishlists
        ]);
    }
    
    //to get wishlist details
    public function accountWishlistDetail()
    {
        //get customer id
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return redirect()->to(base_url('signin'));
        }

        //get wishlist id
        $wishlistId = (int) $this->request->getPost('wishlist_id');
        if(!$wishlistId)
        {
            return $this->response
                ->setStatusCode(400)
                ->setBody('Invalid wishlist.');
        }

        //make sure wishlist belongs to this customer
        $sql = "SELECT id, cust_id, wishlist_name
                FROM Wishlists_master
                WHERE id = ".$wishlistId."
                  AND cust_id = ".$customerId."
                  AND status = 'Y'
                LIMIT 1";
        $wishlistResult = $this->a_access->custom_query($sql);

        if(empty($wishlistResult))
        {
            return $this->response
                ->setStatusCode(404)
                ->setBody('Wishlist not found.');
        }
        $wishlist = $wishlistResult[0];

        $sql = "SELECT
                    wd.id,
                    wd.product_id,
                    wd.acquired_status,
                    p.main_image,
                    pd.product_name,
                    ps.product_qty,
                    ps.on_hand,
                    CASE
                        WHEN ps.product_qty > 0
                        THEN 1
                        ELSE 0
                    END AS is_in_stock
                FROM Wishlist_details wd
                LEFT JOIN Products p ON p.id = wd.product_id
                LEFT JOIN Product_description pd ON pd.product_id = p.id
                LEFT JOIN Products_stock ps ON ps.product_id = p.id
                WHERE wd.wishlist_id = ".$wishlistId."
                ORDER BY wd.id ASC";

        $wishlistProducts = $this->a_access->custom_query($sql);

        //count wishlist items
        $itemCount = count($wishlistProducts);

        //add wishlist data
        $wishlist['item_count'] = $itemCount;

        //return wishlist detail view
        return view('account/acc_wishlist_detail', [
            'page_title' => 'Wishlist Detail | K-Pop Merch',
            'wishlist' => [
                'id'            => $wishlist['id'],
                'wishlist_name' => $wishlist['wishlist_name'],
                'item_count'    => $wishlist['item_count'],
                'products'      => $wishlistProducts
            ]
        ]);
    }
    
    //rename wishlist
    public function renameWishlist()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please sign in again.'
            ]);
        }

        $wishlistId = (int)$this->request->getPost('wishlist_id');
        $wishlistName = trim($this->request->getPost('wishlist_name'));

        if(!$wishlistId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid wishlist.'
            ]);
        }

        if($wishlistName == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Wishlist name is required.'
            ]);
        }

        if(strlen($wishlistName) > 255)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Wishlist name cannot be longer than 255 characters.'
            ]);
        }

        //make sure wishlist belongs to this customer
        $sql = "SELECT id
                FROM Wishlists_master
                WHERE id = ".$wishlistId."
                  AND cust_id = ".$customerId."
                  AND status = 'Y'
                LIMIT 1";
        $wishlist = $this->a_access->custom_query($sql);

        if(empty($wishlist))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Wishlist not found.'
            ]);
        }

        //update wishlist name
        $this->a_access->update_table(
            "Wishlists_master", [  'wishlist_name' => addslashes($wishlistName)  ],  [ 'id'      => $wishlistId,  'cust_id' => $customerId  ]
        );


        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Your wishlist has been renamed successfully.'
        ]);
    }
    
    //create new wishlist
    public function createWishlist()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please sign in again.'
            ]);
        }

        $wishlistName = trim(
            $this->request->getPost('wishlist_name')
        );

        $productId = trim(
            $this->request->getPost('product_id') ?? ''
        );

        if($wishlistName == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Wishlist name is required.'
            ]);
        }

        if(strlen($wishlistName) > 255)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Wishlist name cannot be longer than 255 characters.'
            ]);
        }

        // Check whether wishlist with same name already exists
        $sql = "SELECT id
                FROM Wishlists_master
                WHERE cust_id = ".(int)$customerId."
                  AND wishlist_name = '".addslashes($wishlistName)."'
                  AND status = 'Y'
                LIMIT 1";

        $existingWishlist = $this->a_access->custom_query($sql);

        if(!empty($existingWishlist))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'You already have a wishlist with this name.'
            ]);
        }

        // If a product was supplied, validate it first
        if($productId != '')
        {
            $safeProductId = addslashes($productId);

            $product = $this->a_access->custom_query(
                "SELECT id
                 FROM Products
                 WHERE id = '".$safeProductId."'
                   AND status = 'Y'
                 LIMIT 1"
            );

            if(empty($product))
            {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Product not found.'
                ]);
            }
        }

        // Create wishlist
        $wishlistId = $this->a_access->insert_table_id(
            "Wishlists_master",
            [
                'cust_id'       => $customerId,
                'wishlist_name' => addslashes($wishlistName),
                'status'        => 'Y'
            ]
        );

        if(!$wishlistId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unable to create your wishlist. Please try again.'
            ]);
        }

        // Add product only when the request includes one
        if($productId != '')
        {
            $inserted = $this->a_access->insert_table(
                "Wishlist_details",
                [
                    'wishlist_id'   => $wishlistId,
                    'product_id'    => $productId,
                    'acquired_status' => 'N'
                ]
            );

            if(!$inserted)
            {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'The wishlist was created, but the product could not be added.'
                ]);
            }
        }

        return $this->response->setJSON([
            'status'      => true,
            'message'     => ($productId != '')
                                ? 'Your wishlist has been created and the product added successfully.'
                                : 'Your wishlist has been created successfully.',
            'wishlist_id' => $wishlistId
        ]);
    }
    
    //delete wishlist
    public function deleteWishlist()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please sign in again.'
            ]);
        }
        $wishlistId = (int)$this->request->getPost('wishlist_id');

        if(!$wishlistId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid wishlist.'
            ]);
        }

        //make sure wishlist belongs to this customer
        $sql = "SELECT id
                FROM Wishlists_master
                WHERE id = ".$wishlistId."
                  AND cust_id = ".$customerId."
                  AND status = 'Y'
                LIMIT 1";
        $wishlist = $this->a_access->custom_query($sql);

        if(empty($wishlist))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Wishlist not found.'
            ]);
        }

        //delete wishlist products
        $sql = "DELETE FROM Wishlist_details  WHERE wishlist_id = ".$wishlistId;
        $this->a_access->custom_query_delete($sql);

        //delete wishlist
         $sql = "DELETE FROM Wishlists_master
            WHERE id = ".$wishlistId."  AND cust_id = ".$customerId;
        $this->a_access->custom_query_update($sql);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Your wishlist has been deleted successfully.'
        ]);
    }
    
    //to delete item from wishlist
    public function deleteWishlistItem()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please sign in again.'
            ]);
        }


        $itemId = (int)$this->request->getPost('item_id');

        if(!$itemId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid wishlist item.'
            ]);
        }


        //make sure this wishlist item belongs to this customer
        $sql = "SELECT
                    wd.id
                FROM Wishlist_details wd
                INNER JOIN Wishlists_master wm
                    ON wm.id = wd.wishlist_id
                WHERE wd.id = ".$itemId."
                  AND wm.cust_id = ".$customerId."
                LIMIT 1";

        $item = $this->a_access->custom_query($sql);

        if(empty($item))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Wishlist item not found.'
            ]);
        }


        //delete wishlist item
        $sql = "DELETE FROM Wishlist_details
                WHERE id = ".$itemId;

        $this->a_access->custom_query_delete($sql);


        return $this->response->setJSON([
            'status'  => true,
            'message' => 'The item has been removed from your wishlist.'
        ]);
    }
    
    //to mark item as fulfilled in wishlist
    public function markWishlistFulfilled()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please sign in again.'
            ]);
        }
        $itemId = (int)$this->request->getPost('item_id');

        if(!$itemId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid wishlist item.'
            ]);
        }

        //make sure this wishlist item belongs to this customer
        $sql = "SELECT
                    wd.id
                FROM Wishlist_details wd
                INNER JOIN Wishlists_master wm
                    ON wm.id = wd.wishlist_id
                WHERE wd.id = ".$itemId."
                  AND wm.cust_id = ".$customerId."
                LIMIT 1";
        $item = $this->a_access->custom_query($sql);

        if(empty($item))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Wishlist item not found.'
            ]);
        }

        //mark item as fulfilled
        $sql = "UPDATE Wishlist_details
                SET acquired_status = 'Y'
                WHERE id = ".$itemId;

        $this->a_access->custom_query_update($sql);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'The item has been marked as fulfilled.'
        ]);
    }
}