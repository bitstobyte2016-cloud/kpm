<?php

namespace App\Controllers;

class HeaderCont extends BaseController
{
    
      public function __construct() {
		$this->a_access = new \App\Models\Apiaccess();
                $this->b_email = new \App\Models\BrevoEmail();
	} 
        
    //running test page
    public function test(){
        return view('test');
    }
        
        
        
        
        
    //live search
    public function liveSearch()
    {
        $query = trim($this->request->getPost('query'));

        // MINIMUM LENGTH
        if(strlen($query) < 2){
            return $this->response->setJSON([]);
        }

        // ESCAPE
        $queryEscaped =addslashes($query);

        $keywords =explode(" ", $queryEscaped);
        $whereParts = [];
        foreach($keywords as $word){
            $word = trim($word);
            if($word != ""){
                $whereParts[] =
                    "PD.product_name LIKE '%".$word."%'";
            }
        }

        // FINAL WHERE CLAUS
        $whereClause =implode(" AND ", $whereParts);

        $productSql = "SELECT P.id,P.cat_id,P.main_image, P.release_date,PD.product_name,PS.product_qty,PS.on_hand
            FROM Products P
            LEFT JOIN Product_description PD ON PD.product_id = P.id
            LEFT JOIN Products_stock PS ON PS.product_id = P.id
            WHERE ".$whereClause."
            ORDER BY
                CASE
                    WHEN P.release_date >= CURDATE()
                    THEN 0
                    ELSE 1
                END,
                CASE
                    WHEN P.cat_id = 1 THEN 1
                    WHEN P.cat_id = 2 THEN 2
                    WHEN P.cat_id = 3 THEN 3
                    ELSE 4
                END,
                P.release_date DESC
            LIMIT 10 ";

        $products =$this->a_access->custom_query($productSql);

        return $this->response->setJSON($products);
    }

    //compelte search
    public function search()
    {
        $query =trim($this->request->getGet('q'));

        if($query == "")
        {
            return redirect()->to(base_url());
        }

        $queryEscaped = addslashes($query);
        $keywords = explode(" ", $queryEscaped);
        $whereParts = [];

        foreach($keywords as $word)
        {
            $word = trim($word);

            if($word != "")
            {
                $whereParts[] ="PD.product_name LIKE '%".$word."%'";
            }
        }

        $whereClause = implode(" AND ", $whereParts);

        $sql = " SELECT

                P.id, P.cat_id, P.main_image,P.release_date,
                PD.product_name,
                PS.product_qty, PS.on_hand,
                PP.price,PP.discounted_price
            FROM Products P
            LEFT JOIN Product_description PD ON PD.product_id = P.id
            LEFT JOIN Products_stock PS ON PS.product_id = P.id
            LEFT JOIN Product_price PP ON PP.product_id = P.id
            WHERE ".$whereClause."
            ORDER BY
                CASE
                    WHEN P.release_date >= CURDATE()
                    THEN 0
                    ELSE 1
                END,
                CASE

                    WHEN P.cat_id = 1 THEN 1
                    WHEN P.cat_id = 2 THEN 2
                    WHEN P.cat_id = 3 THEN 3
                    ELSE 4
                END,
                P.release_date DESC ";

        $data['products'] = $this->a_access->custom_query($sql);

        $data['search_query'] = $query;

        $data['total_results'] = count($data['products']);

        return view('search_results', $data );
    }

    
    

    
    // to open sign in page
    public function signin(){
        return view('signin');
    }
    
    //to log in user
    public function login()
    {
        $email = strtolower(trim($this->request->getPost('email')));
        $password = $this->request->getPost('password');
        $redirect = trim($this->request->getPost('redirect'));

        if($email == '' || $password == '')
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Please enter your email and password.'
            ]);
        }

        $password = md5($password);

        $sql = "SELECT * FROM Customer  WHERE email = '" . $email . "'  AND customer_pass = '" . $password . "'
                LIMIT 1";

        $customer = $this->a_access->custom_query($sql);

        if(empty($customer))
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid email address or password.'
            ]);
        }

        $customer = $customer[0];

        // Check account status
        if($customer['status'] != 'Y')
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Your account is currently inactive. Please contact us via email to resolve this.'
            ]);
        }
        
        if($redirect == '')
        {
            $redirect = base_url();
        }
        else
        {
            $baseUrl = base_url();

            if(strpos($redirect, $baseUrl) !== 0)
            {
                $redirect = base_url();
            }
        }

        // Store customer information in session
        session()->set([
            'customer_id'   => $customer['id'],
            'customer_name' => $customer['first_name']
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'You have successfully signed in.',
            'redirect' => $redirect
        ]);
    }
    
    //to open register page
    public function register(){
        return view('register');
    }
    
    //to get states and countires list
    public function getIndiaData()
    {
        $file =ROOTPATH.'public/assets/countries_data.json';

        if(!file_exists($file))
        {
            return $this->response->setJSON([
                'success'=>false,
                'message'=>'File not found'
            ]);
        }

        return $this->response ->setContentType('application/json') ->setBody(file_get_contents($file));
    }

    //to open verfy pop-up
    public function verify()
    {
        //sending email with otp
        $email =$this->request->getPost('email');
        $result =$this->b_email->sendOtpEmail( $email );
        

        if(!$result['success'])
        {
            return redirect() ->back();
        }

        session()->set('email_otp',  $result['otp']);
        session()->set( 'email_otp_expiry',time()+600 );
        session()->set('email',$email);
        
        $data=[];
        $data['email']=$email;

        return view('verify',$data);
    }
    
    //to verify otp
    public function verifyOtp()
    {
        //check email otp
        $enteredEOtp =$this->request->getPost('e_otp');
        $eOtp =session() ->get( 'email_otp' );
        
        if($enteredEOtp == $eOtp)
        {
            return $this->response->setJSON(['success'=>true ]);
        }

        return $this->response ->setJSON([ 'success'=>false ]);
    }
    
    //to resend emailotp
    public function resendEmailOtp()
    {
        $email =session()->get( 'email' );

        if(empty($email))
        {
            return $this->response->setJSON([
                    'success'=>false,
                    'message'=>'Session expired'
                ]);
        }

        $result =$this->b_email->sendOtpEmail($email );

        if(!$result['success'])
        {
            return $this->response->setJSON([
                    'success'=>false,
                    'message'=>'Unable to send Email OTP'
                ]);
        }

        session()->set('email_otp', $result['otp']);
        session()->set( 'email_otp_expiry', time()+600);

        return $this->response ->setJSON([ 'success'=>true ]);
    }
  
    //to register user 
    public function registerUser(){

        $firstName = trim($this->request->getPost('fname'));
        $lastName  = trim($this->request->getPost('lname'));
        $email = strtolower(trim($this->request->getPost('email')));
        $phone = trim( $this->request->getPost('phone'));
        $password = $this->request->getPost('password');
        $address1 = trim( $this->request->getPost('address1'));
        $address2 = trim( $this->request->getPost('address2'));
        $city = trim( $this->request->getPost('city'));
        $state = trim($this->request->getPost('state'));
        $country = trim($this->request->getPost('country'));
        $pincode = trim( $this->request->getPost('pincode'));
        $c_name = trim($this->request->getPost('company_name'));
        $c_gst = trim($this->request->getPost('company_gst'));
        
        //check duplicate email
        $emailExists = $this->a_access->if_exist('Customer','email', $email);

        if ($emailExists)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'An account already exists with this email address.'
            ]);
        }
        
        //check duplicate phone number
        $phoneExists = $this->a_access->if_exist('Customer_addresses','contact_no',$phone);

        if ($phoneExists)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'An account already exists with this mobile number.'
            ]);
        }
        
        //insert customer
            $customerData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'customer_pass' =>md5($password),
                'status' => 'Y',

                // INR
                'preferred_currency_id' => 1,
                'redeem_points_balance' => 0,
                'store_credit' => 0,

                // Active
                'acc_status' => 1
            ];

            $customerId = $this->a_access->insert_table_id('Customer', $customerData);
            
        //insert address
                $addressData = [

                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'address_line1' => $address1,
                    'address_line2' => $address2,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'zip' => $pincode,
                    'company_name' => $c_name,
                    'company_gstin' => $c_gst,
                    'contact_no' => $phone,
                    'cust_id' => $customerId,
                    'status' => 'Y'
                ];

                $addressInserted = $this->a_access->insert_table('Customer_addresses',$addressData );
        
        //add to subscriptions
            $subscriptionData = [
                'email_address' => $email,
                'subscription_date' => date('Y-m-d'),
                'status' => 'opt-in'
            ];

            $subscriptionInserted = $this->a_access->insert_table('Subscriptions', $subscriptionData );
            
             if ($customerId &&$addressInserted &&$subscriptionInserted )
            {
                  session()->set([
                    'customer_id' => $customerId,
                    'customer_name' => $firstName
                ]);
                return $this->response->setJSON([
                    'status' => true,
                    'message' =>'Your account has been created successfully.',
                    'customer_id' => $customerId
                ]);
            }

            return $this->response->setJSON([
                'status' => false,
                'message' =>'Something went wrong while creating your account. Please try again.'
            ]);
    }
    
    
    //open all categories page
    public function categoriesAll($cat_id)
    {
        $categorySql = "Select * from Category where parent_id = 0 AND status = 'Y'";
        $category = $this->a_access ->custom_query($categorySql);
        if($category){
			$categories = array();
			foreach($category as $row){
				$cat = $row;
				//get sub categories
				$sql1 = "Select * from Category where parent_id = ".$row["id"]." AND status = 'Y'";
				$cat["scat"] = $this->a_access->custom_query($sql1);
				
				array_push($categories,$cat);
			}
			$data['category'] = $categories;
		} 
		else {
                    $data['category'] = [];
		}
        
        foreach($category as $row){
            if($row["id"]==$cat_id){
                $data['selected_category'] = $cat_id;
                $data['category_name'] =$row["category_name"];
            }
        }

        $sql = "SELECT P.id, P.cat_id, P.main_image, P.release_date,
                PD.product_name,
                PS.product_qty, PS.on_hand,
                PP.price, PP.discounted_price
            FROM Products P
            LEFT JOIN Product_description PD ON PD.product_id = P.id
            LEFT JOIN Products_stock PS ON PS.product_id = P.id
            LEFT JOIN Product_price PP ON PP.product_id = P.id
            WHERE P.cat_id = '".$cat_id."' ";
        $res=$this->a_access->custom_query($sql);
        $data['products'] = $res;

        return view('categories_all',$data);
    }
    
    //open all bands page
    public function bands_all(): string
    {
           $sql = "SELECT *
            FROM Brands
            WHERE status='Y'
            ORDER BY brand_name ASC";

            $brands = $this->a_access->custom_query($sql);

            $data['brands'] = $brands;
            $data['total_brands'] = count($brands);

            return view('bands_all',$data);
    }
    
    //open pre-order items page
    public function preorder(): string
    {
        $today = date('Y-m-d');
        $sql = "SELECT
                    p.id AS product_id, p.cat_id, p.brand_id, p.main_image, p.release_date,
                    pd.product_name,  pp.price,
                    pp.discounted_price,
                    ps.instock, ps.product_qty, ps.on_hand,
                    b.brand_name
                FROM Products p
                INNER JOIN Product_description pd ON pd.product_id = p.id
                INNER JOIN Product_price pp ON pp.product_id = p.id
                INNER JOIN Products_stock ps ON ps.product_id = p.id
                LEFT JOIN Brands b ON b.id = p.brand_id
                WHERE p.status = 'Y'
                AND p.release_date > '" . $today . "'
                ORDER BY p.release_date DESC";

        $products = $this->a_access->custom_query($sql);
        $data = ['products' => $products ];
        return view('preorder', $data);

    }

    //open on hand items page
    public function onhand(): string
    {
        $sql = "
            SELECT
                p.id,  p.cat_id,  p.main_image, p.release_date,
                pd.product_name,
                ps.product_qty, ps.on_hand,
                pp.price, pp.discounted_price

            FROM Products p
            INNER JOIN Product_description pd ON pd.product_id = p.id
            INNER JOIN Products_stock ps ON ps.product_id = p.id
            INNER JOIN Product_price pp ON pp.product_id = p.id
            WHERE  p.status = 'Y'  AND ps.on_hand = 'Y' ";

        $products = $this->a_access->custom_query($sql);

        return view('onhand', ['products' => $products]);
    }
    
    //to return cart page 
    public function cart()
    {
        $customerId = session()->get('customer_id');
        $ipAddress = $this->request->getIPAddress();

        // Find the current cart
        if (!empty($customerId)) {
            $cartResult = $this->a_access->all_info('Cart_master', [
                'cust_id' => $customerId
            ]);
        } else {
            $cartResult = $this->a_access->all_info('Cart_master', [
                'cart_ip_address' => $ipAddress,
                'cust_id' => null
            ]);
        }

        $cartItems = [];
        $subtotal = 0;
        $cartCount = 0;

        if (!empty($cartResult)) {
            $cartId = $cartResult[0]['id'];

            // Get the items in this cart
            $cartDetails = $this->a_access->all_info('Cart_details', [
                'cart_id' => $cartId
            ]);

            foreach ($cartDetails as $detail) {
                $productId = $detail['product_id'];
                $quantity = (int) $detail['product_quantity'];

                // Fetch product information
                $productResult = $this->a_access->all_info('Products', [
                    'id' => $productId
                ]);

                $descriptionResult = $this->a_access->all_info('Product_description', [
                    'product_id' => $productId
                ]);

                $priceResult = $this->a_access->all_info('Product_price', [
                    'product_id' => $productId
                ]);

                $stockResult = $this->a_access->all_info('Products_stock', [
                    'product_id' => $productId
                ]);

                // Skip items whose product record no longer exists
                if (empty($productResult)) {
                    continue;
                }

                $product = $productResult[0];
                $description = $descriptionResult[0] ?? [];
                $priceInfo = $priceResult[0] ?? [];
                $stockInfo = $stockResult[0] ?? [];

                $price = (float) ($priceInfo['price'] ?? 0);
                $discountedPrice = (float) ($priceInfo['discounted_price'] ?? 0);

                $unitPrice = (
                    $discountedPrice > 0 &&
                    $discountedPrice < $price
                ) ? $discountedPrice : $price;

                $lineTotal = $unitPrice * $quantity;

                $cartItems[] = [
                    'id' => $productId,
                    'product_name' => $description['product_name'] ?? 'Product',
                    'main_image' => $product['main_image'] ?? '',
                    'price' => $price,
                    'discounted_price' => $discountedPrice,
                    'unit_price' => $unitPrice,
                    'product_quantity' => $quantity,
                    'product_qty' => (int) ($stockInfo['product_qty'] ?? 0),
                    'line_total' => $lineTotal
                ];

                $subtotal += $lineTotal;
                $cartCount += $quantity;
            }
        }
        
        $recommendations = [];

        if (!empty($cartItems)) {
            $cartProductIds = [];
            $cartBrandIds = [];

            foreach ($cartItems as $item) {
                $cartProductIds[] = (string) $item['id'];

                $productResult = $this->a_access->all_info(
                    'Products',
                    ['id' => $item['id']]
                );

                if (!empty($productResult)) {
                    $cartBrandIds[] = (int) $productResult[0]['brand_id'];
                }
            }

            $cartBrandIds = array_values(array_unique($cartBrandIds));

            $recommendations = $this->getCartRecommendations(
                $cartBrandIds,
                $cartProductIds
            );
        }

        return view('cart', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'cartCount' => $cartCount,
            'recommendations' => $recommendations
        ]);
    }
    
    //get recommendations for cart
    private function getCartRecommendations(array $brandIds, array $cartProductIds): array
    {
        $recommendations = [];

        $brandIds = array_values(array_unique(array_map('intval', $brandIds)));

        if (empty($brandIds)) {
            return [];
        }

        $selectedBrands = array_slice($brandIds, 0, 4);
        $brandCount = count($selectedBrands);

        $baseCount = intdiv(4, $brandCount);
        $remainder = 4 % $brandCount;

        // Exclude products already in the cart.
        $excludedIds = array_values(array_unique(array_map('strval', $cartProductIds)));

        foreach ($selectedBrands as $index => $brandId) {
            $limit = $baseCount + ($index < $remainder ? 1 : 0);

            $exclude = $excludedIds;

            // Also exclude recommendations already selected.
            foreach ($recommendations as $item) {
                $exclude[] = (string) $item['id'];
            }

            $exclude = array_values(array_unique($exclude));

            $sql = "
                SELECT
                    p.id,
                    p.main_image,
                    p.brand_id,
                    pd.product_name,
                    pp.price,
                    pp.discounted_price
                FROM Products p
                INNER JOIN Product_description pd
                    ON pd.product_id = p.id
                INNER JOIN Product_price pp
                    ON pp.product_id = p.id
                INNER JOIN Products_stock ps
                    ON ps.product_id = p.id
                WHERE p.brand_id = " . $brandId . "
                  AND p.status = 'Y'
                  AND ps.product_qty > 0
            ";

            foreach ($exclude as $productId) {
                $escapedId = addslashes($productId);
                $sql .= " AND p.id <> '" . $escapedId . "'";
            }

            $sql .= " ORDER BY RAND() LIMIT " . (int) $limit;

            $results = $this->a_access->custom_query($sql);

            $recommendations = array_merge($recommendations, $results);
        }

        // Fill any remaining slots from brands represented in the cart.
        if (count($recommendations) < 4) {
            foreach ($brandIds as $brandId) {
                if (count($recommendations) >= 4) {
                    break;
                }

                $remaining = 4 - count($recommendations);
                $exclude = $excludedIds;

                foreach ($recommendations as $item) {
                    $exclude[] = (string) $item['id'];
                }

                $exclude = array_values(array_unique($exclude));

                $sql = "
                    SELECT
                        p.id,
                        p.main_image,
                        p.brand_id,
                        pd.product_name,
                        pp.price,
                        pp.discounted_price
                    FROM Products p
                    INNER JOIN Product_description pd
                        ON pd.product_id = p.id
                    INNER JOIN Product_price pp
                        ON pp.product_id = p.id
                    INNER JOIN Products_stock ps
                        ON ps.product_id = p.id
                    WHERE p.brand_id = " . (int) $brandId . "
                      AND p.status = 'Y'
                      AND ps.product_qty > 0
                ";

                foreach ($exclude as $productId) {
                    $escapedId = addslashes($productId);
                    $sql .= " AND p.id <> '" . $escapedId . "'";
                }

                $sql .= " ORDER BY RAND() LIMIT " . (int) $remaining;

                $results = $this->a_access->custom_query($sql);

                $recommendations = array_merge($recommendations, $results);
            }
        }

        return array_slice($recommendations, 0, 4);
    }
    
    //to add product to cart
    public function addProductToCart()
    {
        $productId = trim((string) $this->request->getPost('product_id'));
        $requestedQuantity = filter_var(
            $this->request->getPost('product_quantity'),
            FILTER_VALIDATE_INT
        );

        // Validate input
        if (
            $productId === '' ||
            $requestedQuantity === false ||
            $requestedQuantity < 1 || $requestedQuantity > 25
        ) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please select a valid quantity between 1 and 25.'
            ]);
        }

        // Fetch the product's current stock
        $stockResult = $this->a_access->all_info('Products_stock', [ 'product_id' => $productId]);
        if (empty($stockResult)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Product stock information could not be found.'
            ]);
        }

        $availableStock = (int) $stockResult[0]['product_qty'];

        if ($availableStock <= 0) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'This product is currently out of stock.'
            ]);
        }

        // Identify the customer or guest
        $customerId = session()->get('customer_id');
        $ipAddress = $this->request->getIPAddress();

        // Find the customer's existing cart
        if (!empty($customerId)) {
            $cartResult = $this->a_access->all_info('Cart_master', [ 'cust_id' => $customerId]);
        } else {
            $cartResult = $this->a_access->all_info('Cart_master', [
                'cart_ip_address' => $ipAddress,
                'cust_id'          => null
            ]);
        }

        // Create a cart if one does not exist
        if (empty($cartResult)) {
            $cartData = [
                'cust_id'         => !empty($customerId) ? $customerId : null,
                'cart_ip_address' => empty($customerId) ? $ipAddress : null
            ];

            $cartId = $this->a_access->insert_table_id(
                'Cart_master',
                $cartData
            );

            if (!$cartId) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Unable to create your cart. Please try again.'
                ]);
            }
        } else {
            $cartId = $cartResult[0]['id'];
        }

        // Check whether this product is already in the cart
        $detailResult = $this->a_access->all_info('Cart_details', [
            'cart_id'    => $cartId,
            'product_id' => $productId
        ]);

        $existingQuantity = !empty($detailResult)
            ? (int) $detailResult[0]['product_quantity']
            : 0;

        // Calculate the total quantity after this addition
        $newQuantity = $existingQuantity + $requestedQuantity;

        // Enforce the maximum quantity per product
        if ($newQuantity > 11) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'You can add a maximum of 10 units of this product to your cart.'
            ]);
        }

        // Validate the combined quantity against current stock
        if ($newQuantity > $availableStock) {
            $remainingStock = max(0, $availableStock - $existingQuantity);

            return $this->response->setJSON([
                'status'  => false,
                'message' => $remainingStock > 0
                    ? "Only {$remainingStock} more unit(s) can be added. Available stock is {$availableStock}."
                    : 'You already have the maximum available stock of this product in your cart.'
            ]);
        }

        // Update the existing cart item or insert a new one
        if (!empty($detailResult)) {
            $saved = $this->a_access->update_table(
                'Cart_details',
                ['product_quantity' => $newQuantity],
                [
                    'cart_id'    => $cartId,
                    'product_id' => $productId
                ]
            );
        } else {
            $saved = $this->a_access->insert_table('Cart_details', [
                'cart_id'          => $cartId,
                'product_id'       => $productId,
                'product_quantity' => $newQuantity
            ]);
        }

        if (!$saved) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unable to update your cart. Please try again.'
            ]);
        }

        // Calculate total units in the cart for the header count
        $allCartItems = $this->a_access->all_info('Cart_details', [
            'cart_id' => $cartId
        ]);

        $cartCount = 0;

        foreach ($allCartItems as $item) {
            $cartCount += (int) $item['product_quantity'];
        }

        return $this->response->setJSON([
            'status'    => true,
            'message'   => 'Product added to your cart.',
            'cartCount' => $cartCount
        ]);
    }

    //to change item quantity in cart
    public function updateCartItem()
    {
        $productId = (string) $this->request->getPost('product_id');
        $quantity = (int) $this->request->getPost('quantity');

        if ($productId === '' || $quantity < 1 || $quantity > 25) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid product or quantity.'
            ]);
        }

        $customerId = session()->get('customer_id');
        $ipAddress = $this->request->getIPAddress();

        if (!empty($customerId)) {
            $cartResult = $this->a_access->all_info('Cart_master', [
                'cust_id' => $customerId
            ]);
        } else {
            $cartResult = $this->a_access->all_info('Cart_master', [
                'cart_ip_address' => $ipAddress,
                'cust_id' => null
            ]);
        }

        if (empty($cartResult)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Cart not found.'
            ]);
        }

        $cartId = $cartResult[0]['id'];

        $detailResult = $this->a_access->all_info('Cart_details', [
            'cart_id' => $cartId,
            'product_id' => $productId
        ]);

        if (empty($detailResult)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found in your cart.'
            ]);
        }

        $stockResult = $this->a_access->all_info('Products_stock', [
            'product_id' => $productId
        ]);

        $availableStock = (int) ($stockResult[0]['product_qty'] ?? 0);

        if ($quantity > $availableStock) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Only ' . $availableStock . ' item(s) are currently available.'
            ]);
        }

        $this->a_access->update_table(
            'Cart_details',
            ['product_quantity' => $quantity],
            [
                'cart_id' => $cartId,
                'product_id' => $productId
            ]
        );

        $details = $this->a_access->all_info('Cart_details', [
            'cart_id' => $cartId
        ]);

        $cartCount = 0;

        foreach ($details as $detail) {
            $cartCount += (int) $detail['product_quantity'];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Cart updated.',
            'cartCount' => $cartCount
        ]);
    }

    //to delete item from cart
    public function removeCartItem()
    {
        $productId = (string) $this->request->getPost('product_id');

        if ($productId === '') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid product.'
            ]);
        }

        $customerId = session()->get('customer_id');
        $ipAddress = $this->request->getIPAddress();

        if (!empty($customerId)) {
            $cartResult = $this->a_access->all_info('Cart_master', [
                'cust_id' => $customerId
            ]);
        } else {
            $cartResult = $this->a_access->all_info('Cart_master', [
                'cart_ip_address' => $ipAddress,
                'cust_id' => null
            ]);
        }

        if (empty($cartResult)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Cart not found.'
            ]);
        }

        $cartId = $cartResult[0]['id'];

        $detailResult = $this->a_access->all_info('Cart_details', [
            'cart_id' => $cartId,
            'product_id' => $productId
        ]);

        if (empty($detailResult)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found in your cart.'
            ]);
        }

        $this->a_access->custom_query_delete(
            'DELETE FROM Cart_details WHERE cart_id = ? AND product_id = ?',
            [$cartId, $productId]
        );

        $details = $this->a_access->all_info('Cart_details', [
            'cart_id' => $cartId
        ]);

        $cartCount = 0;

        foreach ($details as $detail) {
            $cartCount += (int) $detail['product_quantity'];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Product removed from cart.',
            'cartCount' => $cartCount
        ]);
    }

    
}
