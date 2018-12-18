<?php

class M_dp_invoice extends CI_Model
{
    public function get_inv_no($date)
    {
        $data = $this->db->query("SELECT dbo.fxnGetInvoiceCode('" . $date . "') AS inv_number");
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

    function get_so_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('h.*, a.customer_address, tx.taxtype_percent');
        $this->db->from('sales_order_header h');
        $this->db->join('ms_customer_address a', 'a.customer_address_id = h.delivery_address_id ');
        $this->db->join('tax_type tx', 'tx.taxtype_id = h.taxtype_id');

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

    public function get_invoice_type($so_id)
    {
        $this->db->select('sop.*, spt.sp_type_id, spt.sp_type_name, spt.coa_id');
        $this->db->from('sales_order_payment sop');
        $this->db->join('sales_payment_type spt', 'sop.paymenttype_id = spt.sp_type_id');
        $this->db->where('sop.so_id', $so_id);
        return $this->db->get();
    }

    public function get_in_words($num)
    {
        $data = $this->db->query("SELECT dbo.fxnNumberToWords('" . $num . "') AS in_words");
        return $data->row();
    }


    function get_do_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('doh.do_id,
                          doh.so_id,
                          doh.do_code,
                          doh.do_date,
                          doh.customer_id,
                          doh.remarks,
                          doh.status,
                          gph.journal_amount ');
        $this->db->from('delivery_order_header doh');
        $this->db->join('gl_postjournal_header gph', 'gph.journal_no = doh.do_code');

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

    public function into_words($date)
    {
        $data = $this->db->query("SELECT dbo.fxnNumberToWords('" . $date . "') AS in_to_words");
        return $data->row();
    }

    public function get_header_id($inv_code)
    {
        $data = $this->db->query("SELECT * FROM ar_invoice_header WHERE inv_no = '" . $inv_code . "' ");
        return $data->row();
    }

    function get_inv_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0)
    {

        $this->db->select('aih.inv_id,
                          aih.inv_no,
                          aih.inv_date,
                          aih.inv_due_date,
                          cst.customer_name,
                          soh.so_code,
                          aih.total_grand,
                          aih.invoice_type,
                          aih.status ');
        $this->db->from('ar_invoice_header aih');
        $this->db->join('ms_customer cst', 'cst.customer_id = aih.company_id', 'left');
        $this->db->join('sales_order_header soh', 'soh.so_id = aih.so_id', 'left');

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

    public function get_header($inv_id)
    {
        $this->db->select('DISTINCT 	aih.*,
                              cst.customer_id,
                              cst.customer_name,
                              soh.taxtype_id,
                              soh.so_code,
                              tx.taxtype_percent,
                              aid.coa_id
                              ');
        $this->db->from('ar_invoice_header aih');
        $this->db->join('ar_invoice_detail aid', 'aid.inv_id = aih.inv_id', 'left');
        $this->db->join('ms_customer cst', 'cst.customer_id = aih.company_id', 'left');
        $this->db->join('sales_order_header soh', 'soh.so_id = aih.so_id', 'left');
        $this->db->join('tax_type tx', 'tx.taxtype_id = soh.taxtype_id', 'left');
        $this->db->where('aih.inv_id', $inv_id);
        return $this->db->get();
    }


    public function get_detail($inv_id)
    {
        $this->db->select('	aid.*, doh.do_code ');
        $this->db->from('ar_invoice_detail aid');
        $this->db->join('delivery_order_header doh', 'doh.do_id = aid.do_id' , 'left');
        $this->db->where('aid.inv_id', $inv_id);

        return $this->db->get();

    }

    public function get_payment_invoice($inv_id)
    {
        $this->db->select('	aip.inv_payment_id,
                              aip.sp_type_id,
                              msp.sp_type_name,
                              aip.amount');
        $this->db->from('ar_invoice_payment aip');
        $this->db->join('sales_payment_type msp', 'msp.sp_type_id = aip.sp_type_id' , 'left');
        $this->db->where('aip.inv_id', $inv_id);

        return $this->db->get();
    }

    function update($table_name, $where = array(), $data = array())
    {
        $this->db->where($where);
        $this->db->update($table_name, $data);
    }
}