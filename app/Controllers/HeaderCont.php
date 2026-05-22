<?php

namespace App\Controllers;

class HeaderCont extends BaseController
{
    
      public function __construct() {
		$this->a_access = new \App\Models\Apiaccess();
	} 

    // user register
    public function register(){
        return view('register');
    }
    
    //user cart
    public function cart(): string
    {
        return view('cart');
    }
    
    //open all categories page
    public function categories_all(): string
    {
        return view('categories_all');
    }
    
    //open all bands page
    public function bands_all(): string
    {
        return view('bands_all');
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

        $query =
            trim($this->request->getGet('q'));

        // EMPTY QUERY

        if($query == "")
        {
            return redirect()->to(base_url());
        }

        $queryEscaped =
            addslashes($query);


        $keywords =
            explode(" ", $queryEscaped);

        $whereParts = [];

        foreach($keywords as $word)
        {
            $word = trim($word);

            if($word != "")
            {
                $whereParts[] =
                    "PD.product_name LIKE '%".$word."%'";
            }
        }

        $whereClause =
            implode(" AND ", $whereParts);

        // =========================================
        // FETCH PRODUCTS
        // =========================================

        $sql = "

            SELECT

                P.id,
                P.cat_id,
                P.main_image,
                P.release_date,

                PD.product_name,

                PS.product_qty,
                PS.on_hand,

                PP.price,
                PP.discounted_price

            FROM Products P

            LEFT JOIN Product_description PD
            ON PD.product_id = P.id

            LEFT JOIN Products_stock PS
            ON PS.product_id = P.id

            LEFT JOIN Product_price PP
            ON PP.product_id = P.id

            WHERE

                ".$whereClause."

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

        ";

        $data['products'] =
            $this->a_access->custom_query($sql);

        // =========================================
        // PAGE DATA
        // =========================================

        $data['search_query'] =
            $query;

        $data['total_results'] =
            count($data['products']);

        // =========================================
        // LOAD VIEW
        // =========================================

        return view(
            'search_results',
            $data
        );
    }
}
