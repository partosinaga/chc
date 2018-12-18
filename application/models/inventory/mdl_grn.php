<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_grn extends CI_Model{
	
	function get_grn($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('in_grn.*, in_po.po_code, in_supplier.supplier_name, grn_detail.item_description');
		$this->db->from('in_grn');
		$this->db->join('in_po', 'in_grn.po_id = in_po.po_id', 'left');
        $this->db->join('in_supplier', 'in_grn.supplier_id = in_supplier.supplier_id', 'left');
        $this->db->join("( SELECT DISTINCT
                            a.grn_id,
                            CONVERT (
                                VARCHAR,
                                STUFF(
                                    (
                                        SELECT
                                            concat (', ', (CASE WHEN d.item_code = '" . Purchasing::DIRECT_PURCHASE . "' THEN e.item_desc ELSE d.item_desc END))
                                        FROM
                                            in_grn_detail b
                                        LEFT JOIN in_po_detail c ON b.po_detail_id = c.po_detail_id
                                        LEFT JOIN in_ms_item d ON c.item_id = d.item_id
                                        LEFT JOIN in_pr_item e ON c.pr_item_id = e.pr_item_id
                                        WHERE
                                            a.grn_id = b.grn_id FOR XML PATH ('')
                                    ),
                                    1,
                                    1,
                                    ''
                                )
                            ) AS 'item_description'
                        FROM
                            in_grn a
                    ) AS grn_detail ", "grn_detail.grn_id = in_grn.grn_id","left");
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

    function get_po_by_supplier($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
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
	
	function get_grn_detail($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('*');
        $this->db->from('view_in_grn_detail');
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
	
	function get_po_detail($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
        $this->db->select('in_po_detail.*, in_ms_item.item_code, in_pr_item.item_desc, in_ms_uom.uom_code, in_ms_item.item_desc as ms_item_desc, in_ms_item.qty_distribution AS item_factor');
        $this->db->from('in_po_detail');
        $this->db->join('in_pr_item', 'in_po_detail.pr_item_id = in_pr_item.pr_item_id', 'left');
        $this->db->join('in_ms_item', 'in_po_detail.item_id = in_ms_item.item_id', 'left');
        $this->db->join('in_ms_uom', 'in_po_detail.uom_id = in_ms_uom.uom_id', 'left');
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

    function get_new_stock($item_id = 0, $qty = 0, $price = 0){
        $result = array();
        $result['valid'] = false;

        if($item_id > 0){
            $stock = $this->get_stock($item_id);

            $result['valid'] = true;

            $result['item_id'] = $item_id;
            $result['doc_qty'] = $qty;
            $result['price'] = $price;
            $result['total_price'] = $result['doc_qty'] * $result['price'];
            $result['stock_qty'] = $stock['stock_qty'] + $result['doc_qty'];

            if ($result['stock_qty'] > 0)
            {
                $result['avg_price'] = ($stock['total_avg_price'] + $result['total_price']) / $result['stock_qty'];
            }
            else
            {
                $result['avg_price'] = 0;
            }
            $result['total_avg_price'] = $result['avg_price'] * $result['stock_qty'];
        }

        return $result;
    }

    function get_stock($item_id = 0){
        $result = array();
        $result['stock_id'] = 0;
        $result['price'] = 0;
        $result['avg_price'] = 0;
        $result['doc_id'] = 0;
        $result['doc_type'] = 0;
        $result['stock_qty'] = 0;
        $result['doc_qty'] = 0;
        $result['total_price'] = 0;
        $result['total_avg_price'] = 0;

        $qry = $this->mdl_general->get('in_ms_item_stock', array('item_id' => $item_id, 'status <>' => STATUS_DELETE), array(), 'stock_id desc', 1, 0);
        if($qry->num_rows() > 0){
            $row = $qry->row();

            $result['stock_id'] = $row->stock_id;
            $result['price'] = $row->price;
            $result['avg_price'] = $row->avg_price;
            $result['doc_id'] = $row->doc_id;
            $result['doc_type'] = $row->doc_type;
            $result['stock_qty'] = $row->stock_qty;
            $result['doc_qty'] = $row->doc_qty;
            $result['total_price'] = $row->total_price;
            $result['total_avg_price'] = $row->total_avg_price;
        }

        return $result;
    }

    function postStock($detail = array()){
        $valid = true;

        if ($this->db->trans_status() === FALSE){
            $valid = false;

            echo 'No transaction detected.';
        } else {
            if(isset($detail)){
                for ($i = 0; $i < count($detail); $i++)
                {
                    $row = $detail[$i];

                    $this->db->insert('in_ms_item_stock', $row);
                    $newDetailId = $this->db->insert_id();

                    if($newDetailId <= 0){
                        $valid = false;
                        break;
                    }
                }
            } else {
                $valid = false;
            }
        }

        return $valid;
    }

}
?>