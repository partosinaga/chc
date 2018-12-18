<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_control_sheet extends CI_Model{
	
	function get_po($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('in_po.*, in_supplier.supplier_name');
		$this->db->from('in_po');
        $this->db->join('in_supplier', 'in_po.supplier_id = in_supplier.supplier_id', 'left');
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