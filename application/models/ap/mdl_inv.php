<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_inv extends CI_Model{
	function get_inv($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0, $where_string = ''){
		$this->db->select('*');
		$this->db->from('view_ap_inv_header');
        
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
	function get_inv_detail($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('ap_invoicedetail.*, ms_department.department_name, gl_coa.coa_code, gl_coa.coa_desc, in_grn.grn_code, in_grn.remarks ');
		$this->db->from('ap_invoicedetail'); 
        $this->db->join('gl_coa', 'ap_invoicedetail.charge_to = gl_coa.coa_id and ap_invoicedetail.inv_actype = 2', 'left');
        $this->db->join('in_grn', 'ap_invoicedetail.charge_to = in_grn.grn_id and ap_invoicedetail.inv_actype = 1', 'left');
		$this->db->join('ms_department', 'ap_invoicedetail.dept_id =ms_department.department_id', 'left');
        
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
	function get_grn_amount ($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('view_grn_amount.*  ');
		$this->db->from('view_grn_amount');
        
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