<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    
    public function __construct() {
		$this->a_access = new \App\Models\Apiaccess();
                $this->brevo = new \App\Models\BrevoEmail();
	} 
    
    /**************************************
     ******** Home Page functions *********
     *************************************/
    
    //index
    public function index(): string
    {
        
        //get banner images
        $data['home_imgs'] = $this->getBannerImages();
        $data['categories'] = $this->getCategories();
        $data['artists'] = $this->getBrands();
        $data['pre_order'] = $this->getPreOrderItems();
        $data['on_hand'] = $this->getOnHandItems();
        $data['featured_items'] = $this->getFeaturedItems();
        
        //check if user logged in
        if(session()->has('customer_id')){
            $data['customer_id']=session()->get('customer_id');
            $data['customer_name']=session()->get('customer_name');
        }
        
        return view('store_home',$data);
    }

    //get banner images for home
    public function getBannerImages(){
        //get images
	$sql = "Select * from Home_images where status ='Y' Order By id desc";
	return $this->a_access->custom_query($sql);
                
    }
    
    //get pre-order items for home
    public function getPreOrderItems()
    {
        $sql = "SELECT P.id, P.cat_id, P.main_image, P.release_date,
                PD.product_name,
                PS.product_qty, PS.on_hand,
                PP.price,  PP.discounted_price
            FROM Products P
            LEFT JOIN Product_description PD  ON PD.product_id = P.id
            LEFT JOIN Products_stock PS  ON PS.product_id = P.id
            LEFT JOIN Product_price PP ON PP.product_id = P.id
            WHERE P.release_date > CURDATE()
            ORDER BY P.release_date ASC
            LIMIT 9 ";

        return $this->a_access->custom_query($sql);
    }
    
    //get on hand items for home
    public function getOnHandItems()
    {
        $sql = " SELECT P.id,  P.cat_id, P.main_image,  P.release_date,
                PD.product_name,
                PS.product_qty, PS.on_hand,
                PP.price,  PP.discounted_price
            FROM Products P
            LEFT JOIN Product_description PD ON PD.product_id = P.id
            LEFT JOIN Products_stock PS ON PS.product_id = P.id
            LEFT JOIN Product_price PP ON PP.product_id = P.id
            WHERE PS.on_hand = 'Y'
            ORDER BY P.id DESC
            LIMIT 9 ";

        return $this->a_access ->custom_query($sql);
    }
    
    //get featured items for home 
    public function getFeaturedItems()
    {
        $sql="SELECT   P.id,  P.main_image,
        PD.product_name,
        PP.price, PP.discounted_price,
        PS.product_qty
        FROM Products P
        LEFT JOIN Product_description PD ON PD.product_id=P.id
        LEFT JOIN Product_price PP ON PP.product_id=P.id
        LEFT JOIN Products_stock PS ON PS.product_id = P.id
        WHERE P.is_featured='Y'
        LIMIT 6";

        return $this->a_access->custom_query($sql);
    }
    
    //get products by category from all categories page
    public function getProductsByCat()
    {
        $cat_id =$this->request->getPost('cat_id' );
        
        //to only get photocards for unofficial goods
        if($cat_id == 5){
            $cat_id =17;
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

        $products = $this->a_access->custom_query($sql);

        echo json_encode($products);

        exit;
    }
    
    //get products by category for 1 category page
    public function getProductsByCategory()
    {
        $cat_id =$this->request->getPost('cat_id' );
        $categoryName = $this->request->getPost('category_name');
        
        //to only get photocards for unofficial goods
        if($cat_id == 5){
            $cat_id =17;
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

        $products = $this->a_access->custom_query($sql);

        return view('categories', [
            'products'   => $products,
            'cat_id'   => $cat_id,
            'category_name'  => $categoryName
         ]);

        
    }
    
    //get product list by band
    public function getProductsByBand()
    {
        $brandId = $this->request->getPost('brand_id');
        $brandName = $this->request->getPost('brand_name');

        if (empty($brandId)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Band ID is required.'
            ]);
        }


        $sql = "
            SELECT
                p.id, p.brand_id, p.cat_id,p.main_image, p.release_date, p.url,
                pd.product_name,
                ps.product_qty, ps.instock, ps.on_hand,
                pp.price, pp.discounted_price
            FROM Products p
            INNER JOIN Product_description pd ON pd.product_id = p.id
            INNER JOIN Products_stock ps ON ps.product_id = p.id
            INNER JOIN Product_price pp ON pp.product_id = p.id
            WHERE p.status = 'Y' AND p.brand_id = " . (int)$brandId . "
            ORDER BY p.id DESC ";

        $products = $this->a_access->custom_query($sql);

        return view('band_detail', [
        'products'   => $products,
        'brand_id'   => $brandId,
        'brand_name' => $brandName
         ]);
    }
    
    //forgot password
    public function forgotPassword()
    {
        $email = trim($this->request->getPost('email'));

        if($email == '')
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Email address is required.'
            ]);
        }

        // Find customer
        $sql = "SELECT id, first_name, last_name, email
                FROM Customer
                WHERE email = '" . addslashes($email) . "'
                LIMIT 1";

        $customer = $this->a_access->custom_query($sql);

        if(empty($customer))
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No account was found with this email address.'
            ]);
        }

        $customerId = $customer[0]['id'];
        $firstName  = $customer[0]['first_name'];
        $lastName   = $customer[0]['last_name'];

        // Generate temporary password
        $tempPassword = $this->a_access->generateidC();

        // Hash temporary password
        $tempPasswordHash = md5($tempPassword);

        // Update customer password
        $updateSql = "UPDATE Customer
                      SET customer_pass = '" . $tempPasswordHash . "'
                      WHERE id = '" . $customerId . "'";

        $this->a_access->custom_query_update($updateSql);


        // Send temporary password email
        $emailResult = $this->brevo->sendPasswordEmail( $email, $firstName, $tempPassword );

        if(!$emailResult['success'])
        {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Your password was reset, but we were unable to send the email. Please contact support.'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'A new temporary password has been sent to your email address.'
        ]);
    }
    
    
    
        
    /**************************************
     ******** Product functions *********
     *************************************/
    
    
    //to open product details page
    public function getProductDetails($productId)
    {
        $productId = trim($productId);
        if($productId == '')
        {
            return redirect()->to(base_url());
        }
        $productId = addslashes($productId);

        //product details
        $sql = "SELECT   p.id,  p.SKU,  p.UPC,  p.cat_id,  p.brand_id,  p.main_image,  p.status,   p.release_date,   p.weight,   p.url,  p.is_amazon, p.is_featured,
                    pd.product_name,  pd.product_description,
                    pp.price,  pp.discounted_price,
                    ps.instock,  ps.product_qty,  ps.on_hand
                FROM Products p
                LEFT JOIN Product_description pd  ON pd.product_id = p.id
                LEFT JOIN Product_price pp  ON pp.product_id = p.id
                LEFT JOIN Products_stock ps   ON ps.product_id = p.id
                WHERE p.id = '".$productId."' AND p.status = 'Y'

                LIMIT 1";
        $productResult = $this->a_access->custom_query($sql);

        if(empty($productResult))
        {
            return redirect()->to(base_url());
        }
        $product = $productResult[0];

        //additional product images
        $sql = "SELECT  id, image_path,  status
                FROM Product_addiitonal_images
                WHERE product_id = '".$productId."'  AND status = 'Y'
                ORDER BY id ASC";
        $additionalImages = $this->a_access->custom_query($sql);

        //product reviews
        $sql = "SELECT r.id, r.cust_id, r.review_text,  r.review_star_points,  r.review_title,
                    c.first_name,  c.last_name
                FROM Review_master r
                LEFT JOIN Customer c   ON c.id = r.cust_id
                WHERE r.product_id = '".$productId."'  AND r.status = 'Y'
                ORDER BY r.id DESC";
        $reviews = $this->a_access->custom_query($sql);

        //calculate review summary
        $reviewCount = count($reviews);
        $averageRating = 0;

        $ratingCounts = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0
        ];

        if($reviewCount > 0)
        {
            $totalRating = 0;

            foreach($reviews as $review)
            {
                $rating = (float)$review['review_star_points'];

                $totalRating += $rating;

                $ratingKey = (int)round($rating);

                if(isset($ratingCounts[$ratingKey]))
                {
                    $ratingCounts[$ratingKey]++;
                }
            }

            $averageRating = $totalRating / $reviewCount;
        }
        
        //same band product recommendations
        $recommendationSql = "SELECT p.id, p.main_image,  p.release_date,
                                  pd.product_name,
                                  pp.price,  pp.discounted_price,
                                  ps.product_qty,  ps.on_hand
                              FROM Products p
                              LEFT JOIN Product_description pd  ON pd.product_id = p.id
                              LEFT JOIN Product_price pp ON pp.product_id = p.id
                              LEFT JOIN Products_stock ps  ON ps.product_id = p.id
                              WHERE p.brand_id = '".$product['brand_id']."'
                                AND p.id != '".$productId."'  AND p.status = 'Y'
                                AND ps.product_qty > 0  AND ps.on_hand = 'Y'
                              ORDER BY p.release_date DESC
                              LIMIT 5";
        $recommendations = $this->a_access->custom_query($recommendationSql);

        return view('product_detail', [
            'product'          => $product,
            'additionalImages' => $additionalImages,
            'reviews'          => $reviews,
            'reviewCount'      => $reviewCount,
            'averageRating'    => $averageRating,
            'ratingCounts'     => $ratingCounts,
    'recommendations'  => $recommendations
        ]);
    }
    
    //save product review
    public function saveProductReview()
    {
        $customerId = session()->get('customer_id');
        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please sign in to write a review.'
            ]);
        }

        $productId = trim($this->request->getPost('product_id'));
        $stars     = (int)$this->request->getPost('review_star_points');
        $title     = trim($this->request->getPost('review_title'));
        $reviewText = trim($this->request->getPost('review_text'));

        if($productId == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid product.'
            ]);
        }

        if($stars < 1 || $stars > 5)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please select a rating between 1 and 5 stars.'
            ]);
        }

        if($title == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Review title is required.'
            ]);
        }

        if(strlen($title) > 100)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Review title cannot exceed 100 characters.'
            ]);
        }

        if(strlen($reviewText) > 500)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Review cannot exceed 500 characters.'
            ]);
        }

        //check product exists
        $product = $this->a_access->custom_query(
            "SELECT id
             FROM Products
             WHERE id = '".$productId."'
             AND status = 'Y'
             LIMIT 1" );
        if(empty($product))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Product not found.'
            ]);
        }

        //save review
        $reviewData = [
            'cust_id'           => $customerId,
            'review_text'       => ($reviewText != '' ? $reviewText : null),
            'review_star_points'=> $stars,
            'product_id'        => $productId,
            'status'            => 'Y',
            'review_title'      => $title
        ];
        $inserted = $this->a_access->insert_table( 'Review_master', $reviewData  );

        if(!$inserted)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unable to submit your review. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Your review has been submitted successfully.'
        ]);
    }
    
    // Get logged-in customer's wishlists
    public function getCustomerWishlists()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Please sign in again.',
                'wishlists' => []
            ]);
        }

        $sql = "SELECT id, wishlist_name
                FROM Wishlists_master
                WHERE cust_id = ".(int)$customerId."
                  AND status = 'Y'
                ORDER BY id DESC";

        $wishlists = $this->a_access->custom_query($sql);

        return $this->response->setJSON([
            'status'    => true,
            'wishlists' => $wishlists
        ]);
    }
    
    // Add product to an existing wishlist
    public function addProductToWishlist()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please sign in again.'
            ]);
        }

        $wishlistId = trim($this->request->getPost('wishlist_id'));
        $productId  = trim($this->request->getPost('product_id'));

        if($wishlistId == '' || $productId == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid wishlist or product.'
            ]);
        }

        $safeWishlistId = addslashes($wishlistId);
        $safeProductId  = addslashes($productId);

        // Confirm the wishlist belongs to this customer
        $wishlist = $this->a_access->custom_query(
            "SELECT id
             FROM Wishlists_master
             WHERE id = '".$safeWishlistId."'
               AND cust_id = ".(int)$customerId."
               AND status = 'Y'
             LIMIT 1"
        );

        if(empty($wishlist))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Wishlist not found.'
            ]);
        }

        // Confirm the product exists and is active
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

        // Prevent duplicate entries
        $existingItem = $this->a_access->custom_query(
            "SELECT id
             FROM Wishlist_details
             WHERE wishlist_id = '".$safeWishlistId."'
               AND product_id = '".$safeProductId."'
             LIMIT 1"
        );

        if(!empty($existingItem))
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'This product is already in that wishlist.'
            ]);
        }

        $inserted = $this->a_access->insert_table(
            "Wishlist_details",
            [
                'wishlist_id'     => $wishlistId,
                'product_id'      => $productId,
                'acquired_status' => 'N'
            ]
        );

        if(!$inserted)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unable to add this product. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Product added to your wishlist successfully.'
        ]);
    }
     
    // Subscribe to back-in-stock notifications
    public function subscribeProductNotification()
    {
        $customerId = session()->get('customer_id');

        if(!$customerId)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Please sign in to receive notifications.'
            ]);
        }

        $productId = trim($this->request->getPost('product_id') ?? '');

        if($productId == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Invalid product.'
            ]);
        }

        $safeProductId = addslashes($productId);

        // Get the customer's registered email
        $customer = $this->a_access->custom_query(
            "SELECT email
             FROM Customer
             WHERE id = ".(int)$customerId."
             LIMIT 1"
        );

        if(empty($customer) || trim($customer[0]['email']) == '')
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unable to find your registered email address.'
            ]);
        }

        $email = strtolower(trim($customer[0]['email']));
        $safeEmail = addslashes($email);

        // Confirm that the product exists and is active
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

        // Find an existing subscription
        $existing = $this->a_access->custom_query(
            "SELECT id, status
             FROM Notify_subscribers
             WHERE product_id = '".$safeProductId."'
               AND cust_email = '".$safeEmail."'
             LIMIT 1"
        );

        if(!empty($existing))
        {
            if($existing[0]['status'] == 'opt_in')
            {
                return $this->response->setJSON([
                    'status'  => true,
                    'message' => "You're already subscribed to notifications for this product."
                ]);
            }

            // Reactivate a previous subscription
            $updated = $this->a_access->update_table(
                'Notify_subscribers',
                ['status' => 'opt_in'],
                ['id' => $existing[0]['id']]
            );

            if(!$updated)
            {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Unable to reactivate your notification. Please try again.'
                ]);
            }

            return $this->response->setJSON([
                'status'  => true,
                'message' => "You'll receive an email when this product is back in stock."
            ]);
        }

        // Create a new subscription
        $inserted = $this->a_access->insert_table(
            'Notify_subscribers',
            [
                'product_id' => $productId,
                'cust_email' => $email,
                'status'     => 'opt_in'
            ]
        );

        if(!$inserted)
        {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unable to set up your notification. Please try again.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => "You'll receive an email when this product is back in stock."
        ]);
    }

    
    
    
    
    
}
