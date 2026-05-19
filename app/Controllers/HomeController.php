<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    
    public function __construct() {
		parent::__construct();
		$this->load->model("apiaccess","a_access");
	} 
    
    //home page functions
    
    //index
    public function index(): string
    {
        $data['home_imgs'] = getBannerImages();
        
        return view('store_home',$data);
    }

    //get banner images
    public function getBannerImages(){
        //get images
		$sql = "Select * from Home_images where status ='Y' Order By id desc";
		return $this->a_access->custom_query($sql);
                
    }

    
    public function categories(): string
    {
        return view('categories');
    }


}
