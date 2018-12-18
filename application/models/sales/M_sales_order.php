<?php

class M_sales_order extends CI_Model
{
    public function get_detail_customer($id)
    {
        $this->db->select('ms_customer_address.*, master_country.country_name');
        $this->db->from('ms_customer_address ');
        $this->db->join("master_country", 'ms_customer_address.customer_country = master_country.master_country_id ');
        $this->db->where('ms_customer_address.customer_id', $id);
        $this->db->where('ms_customer_address.status', 1);
        return $this->db->get();
    }

    public function get_so_no($date)
    {
        $data = $this->db->query("SELECT dbo.fxnGetSalesOrderCode('" . $date . "') AS so_number");
        return $data->row();
    }

    function get_items_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('im.status, im.item_id, im.item_code, im.item_desc, iu.uom_code, iu.uom_id, im.item_price');
        $this->db->from('in_ms_item_stock ims');
        $this->db->join('in_ms_item im', 'im.item_id = ims.item_id');
        $this->db->join('in_ms_uom iu', 'iu.uom_id = im.uom_id');

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

    function get_package_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {
        $this->db->select('*');
        $this->db->from('in_ms_package_header');

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

    public function get_header_id($so_code)
    {
        $data = $this->db->query("SELECT * FROM sales_order_header WHERE so_code = '" . $so_code . "' ");
        return $data->row();
    }

    function get_so_ajax_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('sh.so_id,
                          sh.so_code,
                          sh.so_date,
                          sh.request_do_date,
                          cst.customer_name,
                          sls.sales_name,
                          sh.remarks,
                          sh.status ');
        $this->db->from('sales_order_header sh');
        $this->db->join('ms_customer cst', 'sh.customer_id = cst.customer_id');
        $this->db->join('ms_sales sls', 'sls.sales_id = sh.sales_id');

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


    public function get_header($so_id)
    {
        $this->db->select('sh.*,
                          cst.customer_name,
                          sls.sales_name,
                          addr.customer_address as delivery_address,
                          addr2.customer_address as invoice_address
                          ');
        $this->db->from('sales_order_header sh');
        $this->db->join('ms_customer cst', 'sh.customer_id = cst.customer_id', 'left');
        $this->db->join('ms_sales sls', 'sls.sales_id = sh.sales_id', 'left');
        $this->db->join('ms_customer_address addr', 'addr.customer_address_id = sh.delivery_address_id', 'left');
        $this->db->join(' ms_customer_address addr2', 'addr2.customer_address_id = sh.invoice_address_id', 'left');
        $this->db->where('sh.so_id', $so_id);
        return $this->db->get();
    }

    public function get_detail($so_id)
    {
        $this->db->select('	sd.so_detail_id,
                          sd.so_id,
                          im.item_id,
                          im.item_code,
                          im.item_desc,
                          sd.price,
                          sd.stock_qty,
                          uom.uom_code,
                          sd.discount ');
        $this->db->from('sales_order_detail sd');
        $this->db->join('in_ms_item im', 'im.item_id = sd.stock_id');
        $this->db->join('in_ms_uom uom', 'uom.uom_id = im.uom_id');
        $this->db->where('sd.so_id', $so_id);
        return $this->db->get();
    }

    public function get_payment($so_id)
    {
        $this->db->select('sop.so_payment_id,
                          msp.sp_type_id,
                          msp.sp_type_name,
                          sop.amount');
        $this->db->from('sales_order_payment sop');
        $this->db->join('sales_payment_type msp', 'msp.sp_type_id = sop.paymenttype_id');
        $this->db->where('sop.so_id', $so_id);
        return $this->db->get();
    }

    function update($table_name, $where = array(), $data = array())
    {
        $this->db->where($where);
        $this->db->update($table_name, $data);
    }

    public function check_so_status($so_id)
    {
        $this->db->select('*');
        $this->db->from('sales_order_header');
        $this->db->where('so_id', $so_id);
        return $this->db->get();
    }

    public function get_approval($order)
    {
        $this->db->select(' top 1 *');
        $this->db->from('sales_approval');
        $this->db->where('status', STATUS_NEW);
        $this->db->where('document_type', DOC_SO);
        $this->db->where('level >', $order);
        return $this->db->get();
    }

    public function get_last_approval()
    {
        $this->db->select(' max(level) as last_level ');
        $this->db->from('sales_approval');
        $this->db->where('status', STATUS_NEW);
        $this->db->where('document_type', DOC_SO);
        return $this->db->get();
    }

    public function get_detail_package($id)
    {
        $data = $this->db->query("SELECT
                         	pd.package_group_id,
                          ph.package_group_code,
                          ph.package_group_desc,
                          ph.price,
                          im.item_code,
                          im.item_desc,
                          pd.item_qty,
                          pd.status,
                          um.uom_code,
                          im.item_price
                        FROM
                          in_ms_package_detail pd
                          LEFT JOIN in_ms_package_header ph on ph.package_group_id = pd.package_group_id
                          LEFT JOIN in_ms_item im ON im.item_id = pd.item_id
                          LEFT JOIN in_ms_uom um ON um.uom_id = im.uom_id
                        WHERE
                          pd.package_group_id =  '" . $id . "' ");
        return $data;
    }


}