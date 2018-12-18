<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_issue extends CI_Model{
	
	function get_issue($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('in_gi.*, in_request.request_code, in_request.is_pos, ms_department.department_name, ms_user.user_fullname as user_created_name');
		$this->db->from('in_gi');
		$this->db->join('in_request', 'in_gi.request_id = in_request.request_id', 'left');
        $this->db->join('ms_department', 'in_gi.department_id = ms_department.department_id', 'left');
        $this->db->join('ms_user', 'in_request.user_created = ms_user.user_id', 'left');
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
	
	function get_gi_detail($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0, $where_string = ''){
        $this->db->select('in_gi_detail.*, in_request_detail.item_qty as req_qty, in_request_detail.item_qty_remain as req_qty_remain, view_in_get_item_stock.item_code, view_in_get_item_stock.item_desc, in_ms_uom.uom_id, in_ms_uom.uom_code, view_in_get_item_stock.on_hand_qty as ms_on_hand_qty, view_in_get_item_stock.unit_cost as ms_unit_cost, view_in_get_item_stock.account_coa_id, view_in_get_item_stock.exp_coa_id');
        $this->db->from('in_gi_detail');
        $this->db->join('in_request_detail', 'in_gi_detail.request_detail_id = in_request_detail.request_detail_id', 'left');
        $this->db->join('view_in_get_item_stock', 'in_request_detail.item_id = view_in_get_item_stock.item_id', 'left');
        $this->db->join('in_ms_uom', 'in_request_detail.uom_id = in_ms_uom.uom_id', 'left');
		if(count($where) > 0){
			$this->db->where($where);
		}
        if($where_string != ''){
            $this->db->where($where_string);
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