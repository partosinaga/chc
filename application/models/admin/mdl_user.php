<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_user extends CI_Model{
	
	function get($where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('ms_user.*, ms_department.department_name');
		$this->db->from('ms_user');
		$this->db->join('ms_department', 'ms_user.department_id = ms_department.department_id', 'left');
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
		return $this->db->get();
    }

}
?>