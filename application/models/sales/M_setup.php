<?php

class M_setup extends CI_Model
{
    public function get_id_customer($name, $dob)
    {
        $data = $this->db->query("SELECT * FROM ms_customer WHERE customer_name = '" . $name . "' AND dob = '" . $dob . "'  ");
        return $data->row();
    }

    function get_customer_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('*');
        $this->db->from('ms_customer');

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

    public function get_detail_customer($id)
    {
        $this->db->select('ms_customer_address.*, master_country.country_name');
        $this->db->from('ms_customer_address ');
        $this->db->join("master_country", 'ms_customer_address.customer_country = master_country.master_country_id ');
        $this->db->where('ms_customer_address.customer_id', $id);
        return $this->db->get();
    }

    public function get_customer($id)
    {
        $this->db->select('*');
        $this->db->from('ms_customer');
        $this->db->where('customer_id', $id);
        return $this->db->get();
    }

    function update($table_name, $where = array(), $data = array())
    {
        $this->db->where($where);
        $this->db->update($table_name, $data);
    }


    function get_sales_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('*');
        $this->db->from('ms_sales');

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

    public function get_sales($id)
    {
        $this->db->select('*');
        $this->db->from('ms_sales');
        $this->db->where('sales_id', $id);
        return $this->db->get();
    }

    function get_payment_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('	pt.*, c.coa_code, c.coa_desc');
        $this->db->from('sales_payment_type pt');
        $this->db->join('gl_coa c', 'c.coa_id = pt.coa_id', 'LEFT');


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

    public function get_sales_payment_type($id)
    {
        $this->db->select('*');
        $this->db->from('sales_payment_type');
        $this->db->where('sp_type_id', $id);
        return $this->db->get();
    }

    public function get_package_header($code, $desc)
    {
        $data = $this->db->query("SELECT * FROM in_ms_package_header WHERE package_group_code = '" . $code . "' AND package_group_desc LIKE '%" . $desc . "%'  ");
        return $data->row();
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

    public function get_package_head($id)
    {
        $this->db->select('*');
        $this->db->from('in_ms_package_header');
        $this->db->where('package_group_id', $id);
        return $this->db->get();
    }

    public function get_approval()
    {
        $data = $this->db->query("SELECT
                                  ap.sales_approval_id,
                                  us.user_fullname,
                                  dp.department_desc,
                                  ap.min_amount,
                                  ap.max_amount,
                                  ap.document_type,
                                  ap.level,
                                  ap.status
                                FROM
                                  sales_approval ap
                                  LEFT JOIN ms_user us ON us.user_id = ap.user_id
                                  LEFT JOIN ms_department dp ON dp.department_id = us.department_id");
        return $data->result();
    }
    public function get_approval_by_id($so_approval_id)
    {
        $data = $this->db->query("SELECT
                                  ap.sales_approval_id,
                                  us.user_id,
                                  us.user_fullname,
                                  dp.department_desc,
                                  ap.min_amount,
                                  ap.max_amount,
                                  ap.document_type,
                                  ap.level,
                                  ap.status
                                FROM
                                  sales_approval ap
                                  LEFT JOIN ms_user us ON us.user_id = ap.user_id
                                  LEFT JOIN ms_department dp ON dp.department_id = us.department_id
                                WHERE ap.sales_approval_id = '".$so_approval_id."'  ");
        return $data->row();
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

    public function get_delivery($id)
    {
        $this->db->select('*');
        $this->db->from('ms_delivery_type');
        $this->db->where('delivery_type_id', $id);
        return $this->db->get();
    }
}