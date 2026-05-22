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
       
        
        return view('store_home',$data);
    }

    //get banner images
    public function getBannerImages(){
        //get images
	$sql = "Select * from Home_images where status ='Y' Order By id desc";
	return $this->a_access->custom_query($sql);
                
    }

    //get categories list 
    public function getCategories(){
        
        $sql = "Select * from Category where parent_id = 0 AND status = 'Y'";
		$result = $this->a_access->custom_query($sql);
		if($result){
			$categories = array();
			foreach($result as $row){
				$cat = $row;
				//get sub categories
				$sql1 = "Select * from Category where parent_id = ".$row["id"];
				$cat["scat"] = $this->a_access->custom_query($sql1);
				
				array_push($categories,$cat);
			}
		} 
		else {
			$categories = [];
		}
                return $categories;
    }
    
    public function categories(): string
    {
        return view('categories');
    }


}
