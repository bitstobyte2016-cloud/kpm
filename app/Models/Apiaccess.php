<?php

namespace App\Models;

use CodeIgniter\Model;

class Apiaccess extends Model
{
    protected $DBGroup = 'default';
    
    
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

    // to check if already exists
    public function if_exist($table, $field, $chkval)
    {
        $builder = $this->db->table($table);

        $builder->where($field, $chkval);

        return $builder->countAllResults() > 0;
    }

    // insert into table
    public function insert_table($table, $val = [])
    {
        return $this->db->table($table)->insert($val);
    }

    // insert into table with id
    public function insert_table_id($table, $val = [])
    {
        $this->db->table($table)->insert($val);

        return $this->db->insertID();
    }

    // get value from db
    public function single_info($table, $fields = [], $sval = [])
    {
        $builder = $this->db->table($table);

        $builder->select(implode(",", $fields));

        if (!empty($sval)) {
            $builder->where($sval);
        }

        return $builder->get()->getResultArray();
    }

    // get all fields
    public function all_info($table, $sval = [])
    {
        $builder = $this->db->table($table);

        $builder->select('*');

        if (!empty($sval)) {
            $builder->where($sval);
        }

        return $builder->get()->getResultArray();
    }

    // update table
    public function update_table($table, $val = [], $sval = [])
    {
        $builder = $this->db->table($table);

        if (!empty($sval)) {
            $builder->where($sval);
        }

        return $builder->update($val);
    }

    // custom query
    public function custom_query($query)
    {
        return $this->db->query($query)->getResultArray();
    }

    // custom query update
    public function custom_query_update($query)
    {
        return $this->db->query($query);
    }

    // custom query delete
    public function custom_query_delete($query)
    {
        return $this->db->query($query);
    }
}
