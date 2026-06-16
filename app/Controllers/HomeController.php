<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    
    public function __construct() {
		$this->a_access = new \App\Models\Apiaccess();
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

    
    
    
    
    //get products by category
    public function getProductsByCat()
    {
        $cat_id =$this->request->getPost('cat_id' );

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
    
    //to open register page
    public function register(){
        return view('register');
    }
}
