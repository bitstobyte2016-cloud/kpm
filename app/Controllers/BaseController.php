<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    
    protected $header_data=[];
    
    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        
        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        $this->a_access = new \App\Models\Apiaccess();
        $this->header_data['categories'] = $this->getCategories();
        $this->header_data['brands']=$this->getBrands();
      
            service('renderer')->setData(
                $this->header_data
            );
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
    
     //get categories list 
    public function getBrands(){
        
        $sql = "Select * from Brands where is_Header = 'Y' AND status = 'Y'";
		$result = $this->a_access->custom_query($sql);
		
                return $result;
    }
}
