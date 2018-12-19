<?php

class M_delivery_order extends CI_Model
{
    public function get_so_no($date)
    {
        $data = $this->db->query("SELECT dbo.fxnGetDeliveryOrderCode('" . $date . "') AS do_number");
        return $data->row();
    }

    function update($table_name, $where = array(), $data = array())
    {
        $this->db->where($where);
        $this->db->update($table_name, $data);
    }

    function get_so_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('h.*, a.customer_address');
        $this->db->from('sales_order_header h');
        $this->db->join('ms_customer_address a', 'a.customer_address_id = h.delivery_address_id ');

        if (count($where) > 0) {
            $this->db->where($where);
        }

        if (count($like) > 0) {
            $this->db->like($like);
        }
        if ($order_by != "") {
            $this->db->order_by($order_by);
        }
        if ($limit_row > 0) {
            $this->db->limit($limit_row, $limit_start);
        }

        if ($is_count) {
            return $this->db->count_all_results();
        } else {
            return $this->db->get();
        }
    }

    function get_delivery_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('*');
        $this->db->from('ms_delivery_type');

        if (count($where) > 0) {
            $this->db->where($where);
        }

        if (count($like) > 0) {
            $this->db->like($like);
        }
        if ($order_by != "") {
            $this->db->order_by($order_by);
        }
        if ($limit_row > 0) {
            $this->db->limit($limit_row, $limit_start);
        }

        if ($is_count) {
            return $this->db->count_all_results();
        } else {
            return $this->db->get();
        }
    }

    function get_items_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('sod.so_detail_id,
                          sod.so_id,
                          sod.stock_id,
                          im.item_code,
                          im.item_desc,
                          sod.stock_qty,
                          um.uom_code,
                          sod.price,
                          sod.status');
        $this->db->from('sales_order_detail sod');
        $this->db->join('in_ms_item im', 'im.item_id = sod.stock_id');
        $this->db->join('in_ms_uom um', 'um.uom_id = im.uom_id');

        if (count($where) > 0) {
            $this->db->where($where);
        }

        if (count($like) > 0) {
            $this->db->like($like);
        }
        if ($order_by != "") {
            $this->db->order_by($order_by);
        }
        if ($limit_row > 0) {
            $this->db->limit($limit_row, $limit_start);
        }

        if ($is_count) {
            return $this->db->count_all_results();
        } else {
            return $this->db->get();
        }
    }

    public function get_header_id($do_code)
    {
        $data = $this->db->query("SELECT * FROM delivery_order_header WHERE do_code = '" . $do_code . "' ");
        return $data->row();
    }

    public function get_existing_do($so_id)
    {
        $this->db->select('max( h.so_id ) AS so_id,
                              max(h.do_id) as do_id,
                              d.stock_id,
                              SUM( d.delivery_qty ) AS stock_delivered ');
        $this->db->from('delivery_order_detail d');
        $this->db->join('delivery_order_header h', 'd.do_id = h.do_id');
        $this->db->where('h.so_id', $so_id);
        $this->db->group_by('d.stock_id	');
        return $this->db->get();
    }

    function get_do_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('h.do_id,
                          h.do_code,
                          hs.so_code,
                          h.do_date,
                          c.customer_name,
                          ms.customer_address,
                          h.remarks,
                          h.status ');
        $this->db->from('delivery_order_header h');
        $this->db->join('sales_order_header hs', 'hs.so_id = h.so_id');
        $this->db->join('ms_customer c', 'c.customer_id = h.customer_id');
        $this->db->join('ms_customer_address ms', 'ms.customer_address_id = h.delivery_address');

        if (count($where) > 0) {
            $this->db->where($where);
        }

        if (count($like) > 0) {
            $this->db->like($like);
        }
        if ($order_by != "") {
            $this->db->order_by($order_by);
        }
        if ($limit_row > 0) {
            $this->db->limit($limit_row, $limit_start);
        }

        if ($is_count) {
            return $this->db->count_all_results();
        } else {
            return $this->db->get();
        }
    }

    public function get_header($do_id)
    {
        $this->db->select('h.*,
                          hs.so_code,
                          c.customer_name,
                          ms.customer_address_id,
                          ms.customer_address,
                          dt.delivery_type_name,
                           ');
        $this->db->from('delivery_order_header h');
        $this->db->join('sales_order_header hs ', 'hs.so_id = h.so_id', 'left');
        $this->db->join('ms_customer c', 'c.customer_id = h.customer_id', 'left');
        $this->db->join('ms_customer_address ms', 'ms.customer_address_id = h.delivery_address', 'left');
        $this->db->join('ms_delivery_type dt', 'dt.delivery_type_id = h.delivery_by', 'left');
        $this->db->where('h.do_id', $do_id);
        return $this->db->get();
    }

    public function get_detail($do_id)
    {
        $this->db->select('dod.do_detail_id,
                          dod.do_id,
                          dod.stock_id,
                          im.item_id,
                          im.item_code,
                          im.item_desc,
                          dod.delivery_qty,
                          um.uom_code,
                          im.item_price');
        $this->db->from('delivery_order_detail dod');
        $this->db->join('in_ms_item im', 'im.item_id = dod.stock_id', 'left');
        $this->db->join('in_ms_uom um', 'um.uom_id = im.uom_id', 'left');
        $this->db->where('dod.do_id', $do_id);
        return $this->db->get();
    }

    public function get_stock_qty($so_id)
    {
        $this->db->Select('*');
        $this->db->from('sales_order_detail');
        $this->db->where('so_id', $so_id);
        return $this->db->get();
    }

    public function get_max_do_qty($so_id, $do_id)
    {
        $this->db->select('max( h.so_id ) AS so_id,
                              max(h.do_id) as do_id,
                              d.stock_id,
                              SUM( d.delivery_qty ) AS stock_delivered ');
        $this->db->from('delivery_order_detail d');
        $this->db->join('delivery_order_header h', 'd.do_id = h.do_id');
        $this->db->where('h.so_id', $so_id);
        $this->db->where('h.do_id != ', $do_id);
        $this->db->group_by('d.stock_id	');
        return $this->db->get();
    }

    public function check_so_status($do_id)
    {
        $this->db->select('*');
        $this->db->from('delivery_order_header');
        $this->db->where('do_id', $do_id);
        return $this->db->get();
    }

    public function get_approval($order)
    {
        $this->db->select(' top 1 *');
        $this->db->from('sales_approval');
        $this->db->where('status', STATUS_NEW);
        $this->db->where('document_type', DOC_DO);
        $this->db->where('level >', $order);
        return $this->db->get();
    }

    public function get_last_approval()
    {
        $this->db->select(' max(level) as last_level ');
        $this->db->from('sales_approval');
        $this->db->where('status', STATUS_NEW);
        $this->db->where('document_type', DOC_DO);
        return $this->db->get();
    }

    public function get_gl_postjournal_header_id($id)
    {
        $this->db->select('*');
        $this->db->from('gl_postjournal_header');
        $this->db->where('journal_no', $id);
        return $this->db->get();
    }
    public function get_do_debit($spec_key)
    {
        $this->db->select('ffs.spec_key,c.*');
        $this->db->from('fn_feature_spec ffs');
        $this->db->join('gl_coa c', 'c.coa_id = ffs.coa_id ', 'left');
        $this->db->where('ffs.spec_key', $spec_key);
        return $this->db->get();
    }
    public function entry_data_journal_detail($postheader_id, $do_id)
    {
        $this->db->query("INSERT INTO gl_postjournal_detail (
                                    postheader_id,
                                    coa_id,
                                    coa_code,
                                    journal_note,
                                    journal_debit,
                                    journal_credit,
                                    dept_id,
                                    status)
                          SELECT
                                  '".$postheader_id."',
                                  im.account_coa_id,
                                  gc.coa_code,
                                  dh.remarks,
                                  '0',
                                  dd.delivery_qty * dd.price as credit,
                                  '".my_sess('department_id')."',
                                  '".STATUS_NEW."'
                                FROM
                                  delivery_order_detail dd
                                LEFT JOIN delivery_order_header dh on dh.do_id = dd.do_id
                                LEFT JOIN in_ms_item im ON im.item_id = dd.stock_id
                                LEFT JOIN gl_coa gc ON gc.coa_id = im.account_coa_id
                                WHERE
                                  dd.do_id = ".$do_id.";
                                        ");

    }

}