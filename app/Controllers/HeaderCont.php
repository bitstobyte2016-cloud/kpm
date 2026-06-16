<?php

namespace App\Controllers;

class HeaderCont extends BaseController
{
    
      public function __construct() {
		$this->a_access = new \App\Models\Apiaccess();
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

    // user register
    public function signin(){
        return view('signin');
    }
    
    //user cart
    public function cart(): string
    {
        return view('cart');
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
        return view('preorder');
    }

    //open on hand items page
    public function onhand(): string
    {
        return view('onhand');
    }
    
}
