<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_receipt extends CI_Model{
	
	function get_sr($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('in_sr.*, in_gi.gi_code, ms_department.department_name');
		$this->db->from('in_sr');
		$this->db->join('in_gi', 'in_sr.gi_id = in_gi.gi_id', 'left');
        $this->db->join('ms_department', 'in_sr.department_id = ms_department.department_id', 'left');
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
	
	function get_sr_detail($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0, $where_string = ''){
        $this->db->select('in_sr_detail.*, in_gi_detail.item_qty as gi_qty, in_gi_detail.item_qty_remain as gi_qty_remain, view_in_get_item_stock.item_code, view_in_get_item_stock.item_desc, view_in_get_item_stock.on_hand_qty as ms_on_hand_qty, view_in_get_item_stock.account_coa_id, view_in_get_item_stock.exp_coa_id, view_in_get_item_stock.uom_out_code');
        $this->db->from('in_sr_detail');
        $this->db->join('in_gi_detail', 'in_sr_detail.gi_detail_id = in_gi_detail.gi_detail_id', 'left');
        $this->db->join('view_in_get_item_stock', 'in_sr_detail.item_id = view_in_get_item_stock.item_id', 'left');
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