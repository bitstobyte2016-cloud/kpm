<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    
    public function __construct() {
		$this->a_access = new \App\Models\Apiaccess();
                $this->brevo = new \App\Models\BrevoEmail();
	} 
    
    //home page functions
    
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
        $sql="SELECT 
        P.id,
        P.main_image,
        PD.product_name,
        PP.price,
        PP.discounted_price

        FROM Products P

        LEFT JOIN Product_description PD
        ON PD.product_id=P.id

        LEFT JOIN Product_price PP
        ON PP.product_id=P.id

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
    
}
