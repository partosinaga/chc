<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_menu extends CI_Model{
	
	function get($where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('mn1.*, mn1.module_name as parent_name');
		$this->db->from('ms_menu mn1');
		$this->db->join('ms_menu mn2', 'mn2.parent_id = mn1.menu_id', 'left');
		if(count($where) > 0){
			$this->db->where($where);
		}
		if(count($like) > 0){
			$this->db->like($like);
		}
		if($order_by != ""){
			$this->db->order_by($order_by);
		}
		if($limit_row > 0){
			$this->db->limit($limit_row, $limit_start);
		}

        $this->db->distinct();

		return $this->db->get();
    }

}
?>