<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_request extends CI_Model{
	
	function get_request($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0, $where_or = ''){
		$this->db->select('in_request.*, ms_user.user_fullname, ms_department.department_name, ms_department.department_desc');
		$this->db->from('in_request');
		$this->db->join('ms_user', 'in_request.user_created = ms_user.user_id', 'left');
		$this->db->join('ms_department', 'in_request.department_id = ms_department.department_id', 'left');
		if(count($where) > 0){
			$this->db->where($where);
		}
        if(trim($where_or) != ''){
            $this->db->where($where_or);
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
		
		if($is_count){
			return $this->db->count_all_results();
		}
		else {
			return $this->db->get();
		}
    }
	
	function get_request_detail($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('in_request_detail.*, view_in_get_item_stock.item_code, view_in_get_item_stock.item_desc, in_ms_uom.uom_code, view_in_get_item_stock.on_hand_qty as ms_on_hand_qty, view_in_get_item_stock.unit_cost');
		$this->db->from('in_request_detail');
		$this->db->join('view_in_get_item_stock', 'in_request_detail.item_id = view_in_get_item_stock.item_id', 'left');
		$this->db->join('in_ms_uom', 'in_request_detail.uom_id = in_ms_uom.uom_id', 'left');
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
		
		if($is_count){
			return $this->db->count_all_results();
		}
		else {
			return $this->db->get();
		}
    }
	
	function get_item($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->from('view_in_get_item_stock');
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
		
		if($is_count){
			return $this->db->count_all_results();
		}
		else {
			return $this->db->get();
		}
    }
}
?>