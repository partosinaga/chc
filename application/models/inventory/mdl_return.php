<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_return extends CI_Model{
	
	function get_return($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0, $where_string = ''){
		$this->db->select('in_return.*, in_grn.grn_code, in_grn.do_no, in_po.po_code, return_detail.item_description, in_supplier.supplier_name');
		$this->db->from('in_return');
		$this->db->join('in_grn', 'in_return.grn_id = in_grn.grn_id', 'left');
        $this->db->join('in_po', 'in_grn.po_id = in_po.po_id', 'left');
        $this->db->join('in_supplier', 'in_return.supplier_id = in_supplier.supplier_id', 'left');
        $this->db->join("( SELECT DISTINCT
                            a.return_id,
                            CONVERT (
                                VARCHAR,
                                STUFF(
                                    (
                                        SELECT
                                            concat (', ', (CASE WHEN c.item_code = '" . Purchasing::DIRECT_PURCHASE . "' THEN f.item_desc ELSE c.item_desc END))
                                        FROM
                                            in_return_detail b
                                        LEFT JOIN in_ms_item c ON b.item_id = c.item_id
                                        LEFT JOIN in_grn_detail d ON b.grn_detail_id = d.grn_detail_id
                                        LEFT JOIN in_po_detail e ON d.po_detail_id = e.po_detail_id
                                        LEFT JOIN in_pr_item f ON e.pr_item_id = f.pr_item_id
                                        WHERE
                                            a.return_id = b.return_id FOR XML PATH ('')
                                    ),
                                    1,
                                    1,
                                    ''
                                )
                            ) AS 'item_description'
                        FROM
                            in_return a
                    ) AS return_detail ", "return_detail.return_id = in_return.return_id","left");
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

    function get_return_detail($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
        $this->db->select('*');
        $this->db->from('view_in_return_detail');
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