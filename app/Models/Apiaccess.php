<?php

class ApiAccess extends CI_model{
	
	//generate random characters id
	public function generateidC(){
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 10; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		
		return $randomString;
	}
	
	//generate uid
	public function generateid(){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 10; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		
		return $randomString;
	}
	
	//to check if already exists
	public function if_exist($table,$field,$chkval){

		$sql = "SELECT * FROM ".$table." WHERE ".$field."='".$chkval."'";

		$query = $this->db->query($sql);

		$count=$query->num_rows();

		if($count>0){

			return true;

		}

		else{

			return false;

		}

	}
	
	//insert into table
	public function insert_table($table,$val=array()){

		return $this->db->insert($table,$val);

	}
	
	//insert into table with id
	public function insert_table_id($table,$val=array()){
		$this->db->insert($table,$val);
		return $this->db->insert_id();

	}
	
	//get value from db
	public function single_info($table,$fields=array(),$sval=array()){

		$this->db->select(implode(",",$fields));

		$this->db->from($table);

		if(count($sval)>0){

			$this->db->where($sval);

		}

		$query_result=$this->db->get();

		$result=$query_result->result_array();

		return $result;

	}
	
	//get all fields
	public function all_info($table,$sval=array()){

		$this->db->select("*");

		$this->db->from($table);
		
		if(count($sval)>0){

			$this->db->where($sval);

		}
		$query_result=$this->db->get();

		$result=$query_result->result_array();

		return $result;

	}
	
	//to update table
	public function update_table($table,$val=array(),$sval=array()){

		if(count($sval)>0){

			$this->db->where($sval);

		}

		return $this->db->update($table,$val);

	}
	
    //custom query
    public function custom_query($query){

		$qresult=$this->db->query($query);

		$result=$qresult->result_array();

		return $result;

	}
	
	//custom query update
	public function custom_query_update($query){
		
		$qresult=$this->db->query($query);
		
		$result=$qresult;
		
		return $result;
		
	}
	
	//custom query delete
	public function custom_query_delete($query){
		$qresult=$this->db->query($query);
		
		$result=$qresult;
		
		return $result;
	}

}

?>
