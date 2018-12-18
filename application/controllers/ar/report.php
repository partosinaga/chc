<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        if(!is_login()){
            redirect(base_url('login/login_form.tpd'));
        }

        $this->data_header = array(
            'style' 	=> array(),
            'script' 	=> array(),
            'custom_script' => array(),
            'init_app'	=> array()
        );

        $this->data_footer = array(
            'footer_script' => array()
        );
    }

    public function index()
    {
        tpd_404();
    }

    public function sales_summary(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/css/report_group.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data['trx_type'] = $this->mdl_general->get('ms_transtype', array('coa_id > ' => 0), array(), 'iscompulsory DESC, transtype_name ASC');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ar/report/sales_summary', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_sales_summary(){
        $this->load->model('finance/mdl_finance');

        $where_string = "";
        $having_string = "";

        $records = array();
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d');

        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $startDate = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }

        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $endDate = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }

        if(isset($_REQUEST['filter_transtype_id'])){
            if($_REQUEST['filter_transtype_id'] != ''){
                //$where['transtype_id'] = $_REQUEST['filter_transtype_id'];
                $where_string .= ' AND ms_transtype.transtype_id = ' . $_REQUEST['filter_transtype_id'];
            }
        }

        if(isset($_REQUEST['filter_transtype_desc'])){
            if($_REQUEST['filter_transtype_desc'] != ''){
                //$like['transtype_desc'] = $_REQUEST['filter_transtype_desc'];
                $where_string .= " AND ms_transtype.transtype_desc LIKE '%" . $_REQUEST['filter_transtype_desc'] . "%' ";
            }
        }

        if(isset($_REQUEST['filter_amount_from'])){
            if($_REQUEST['filter_amount_from'] != ''){
                //$where['amount >='] = $_REQUEST['filter_amount_from'];
                $having_string .= " ISNULL(SUM(cs_bill_detail.amount),0) >= " . $_REQUEST['filter_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_amount_to'])){
            if($_REQUEST['filter_amount_to'] != ''){
                //$where['amount <='] = $_REQUEST['filter_amount_to'];
                if($having_string != ''){
                    $having_string .= ' AND ';
                }
                $having_string .= " ISNULL(SUM(cs_bill_detail.amount),0) <= " . $_REQUEST['filter_amount_to'];
            }
        }

        $sql = "SELECT ms_transtype.transtype_id, ms_transtype.transtype_name, ms_transtype.transtype_desc, ISNULL(SUM(cs_bill_detail.amount),0) as amount
                FROM cs_bill_detail
                JOIN ms_transtype ON ms_transtype.transtype_id = cs_bill_detail.transtype_id
                WHERE cs_bill_detail.date_start BETWEEN '" . $startDate ."' AND '" . $endDate . "' " . $where_string . "
                GROUP BY ms_transtype.transtype_id, ms_transtype.transtype_name, ms_transtype.transtype_desc ";

        $iTotalRecords = $this->db->query($sql)->num_rows();

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'ms_transtype.transtype_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ms_transtype.transtype_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ms_transtype.transtype_desc ' . $_REQUEST['order'][0]['dir'];
            }
        }

        if($having_string != ''){
            $having_string = ' HAVING (' . $having_string . ') ';
        }

        $qry = $this->db->query($sql . $having_string .' ORDER BY ' . $order);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                '<input type="hidden" name="transtype_id[]" value="' . $row->transtype_id . '"/>' .
                $row->transtype_name,
                $row->transtype_desc,
                '<span class="mask_currency">' . $row->amount . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function sales_detail($transtype_id = 0, $date_start = '', $date_to = ''){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/css/report_group.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        if($transtype_id > 0 && $date_start != '' && $date_to != ''){
            $where_string = ' AND cs_bill_detail.transtype_id = ' . $transtype_id;
            $startDate = dmy_to_ymd($date_start);
            $endDate =  dmy_to_ymd($date_to);

            //$data['transtype_id'] = $transtype_id;
            //$data['date_start'] = $date_start;
            //$data['date_end'] = $date_to;

            $trx = $this->db->get_where('ms_transtype', array('transtype_id' => $transtype_id))->row();

            $sql = "SELECT cs_bill_header.bill_date, cs_bill_header.journal_no, cs_bill_detail.*,view_cs_reservation.tenant_fullname,view_cs_reservation.reservation_code, ISNULL(ms_unit.unit_code,'') as room
                FROM cs_bill_detail
                JOIN cs_bill_header ON cs_bill_header.bill_id = cs_bill_detail.bill_id
                JOIN view_cs_reservation ON view_cs_reservation.reservation_id = cs_bill_header.reservation_id
                LEFT JOIN ms_unit ON ms_unit.unit_id = cs_bill_detail.unit_id
                WHERE cs_bill_detail.date_start BETWEEN '" . $startDate ."' AND '" . $endDate . "' " . $where_string . "
                ORDER BY cs_bill_header.bill_date, view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname";

            $data['qry'] = $this->db->query($sql);

            $data['report_title'] = '[' . $trx->transtype_name . '] ' . $trx->transtype_desc .
                                    ' per ' . $date_start . ' - ' . $date_to;

        }

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ar/report/sales_detail', $data);
        $this->load->view('layout/footer');
    }

    public function official_receipt(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/css/report_group.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        //Get & Combine payment type
        $types = $this->mdl_general->get('ms_payment_type', array('status ' => STATUS_NEW), array(), 'paymenttype_desc DESC');
        $payment_types = array();
        foreach($types->result_array() as $type){
            array_push($payment_types, array('paymenttype_id' => $type['paymenttype_id'], 'paymenttype_desc' => strtoupper($type['paymenttype_desc'])));
        }

        array_push($payment_types, array('paymenttype_id' => FLAG_CASHBOOK, 'paymenttype_desc' => 'CASH BOOK'));

        $data['payment_type'] = $payment_types;

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ar/report/o_receipt', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_official_receipt(){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $where_string = "";
        $date_start = isset($_REQUEST['filter_date_from']) ?  dmy_to_ymd($_REQUEST['filter_date_from']) : date('Y-m-d');
        $date_end = isset($_REQUEST['filter_date_to']) ?  dmy_to_ymd($_REQUEST['filter_date_to']) : date('Y-m-d');

        $records = array();
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['doc_date >='] = $date_start;
            }
        }

        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['doc_date <='] = $date_end;
            }
        }

        if(isset($_REQUEST['filter_doc_no'])){
            if($_REQUEST['filter_doc_no'] != ''){
                $like['doc_no'] = $_REQUEST['filter_doc_no'];
            }
        }

        if(isset($_REQUEST['filter_bankaccount_no'])){
            if($_REQUEST['filter_bankaccount_no'] != ''){
                $like['bankaccount_no'] = $_REQUEST['filter_bankaccount_no'];
            }
        }

        if(isset($_REQUEST['filter_paymenttype_id'])){
            if($_REQUEST['filter_paymenttype_id'] != ''){
                $where['receipt_type'] = $_REQUEST['filter_paymenttype_id'];
            }
        }

        if(isset($_REQUEST['filter_subject'])){
            if($_REQUEST['filter_subject'] != ''){
                $like['subject'] = $_REQUEST['filter_subject'];
            }
        }

        if(isset($_REQUEST['filter_description'])){
            if($_REQUEST['filter_description'] != ''){
                $like['receipt_desc'] = $_REQUEST['filter_description'];
            }
        }

        if(isset($_REQUEST['filter_bank_charge_from'])){
            if($_REQUEST['filter_bank_charge_from'] != ''){
                $where['bank_charge >='] = $_REQUEST['filter_bank_charge_from'];
            }
        }

        if(isset($_REQUEST['filter_bank_charge_to'])){
            if($_REQUEST['filter_bank_charge_to'] != ''){
                $where['bank_charge <='] = $_REQUEST['filter_bank_charge_to'];
            }
        }

        if(isset($_REQUEST['filter_receipt_amount_from'])){
            if($_REQUEST['filter_receipt_amount_from'] != ''){
                $where['receipt_amount >='] = $_REQUEST['filter_receipt_amount_from'];
            }
        }

        if(isset($_REQUEST['filter_receipt_amount_to'])){
            if($_REQUEST['filter_receipt_amount_to'] != ''){
                $where['receipt_amount <='] = $_REQUEST['filter_receipt_amount_to'];
            }
        }

        if(isset($_REQUEST['filter_subtotal_from'])){
            if($_REQUEST['filter_subtotal_from'] != ''){
                $where['subtotal >='] = $_REQUEST['filter_subtotal_from'];
            }
        }

        if(isset($_REQUEST['filter_subtotal_to'])){
            if($_REQUEST['filter_subtotal_to'] != ''){
                $where['subtotal <='] = $_REQUEST['filter_subtotal_to'];
            }
        }

        $iTotalRecords = $this->mdl_finance->countJoin("fxnAROfficialReceipt('" . $date_start . "','" . $date_end . "')", array(), $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'doc_date asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'doc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'doc_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'bankaccount_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'receipt_type_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'subject ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'receipt_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'bank_charge ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 7){
                $order = 'receipt_amount ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 8){
                $order = 'subtotal ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('o_rec.*',"fxnAROfficialReceipt('" . $date_start . "','" . $date_end . "') as o_rec ", array(), $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                dmy_from_db($row->doc_date),
                $row->doc_no,
                $row->bankaccount_no,
                strtoupper($row->receipt_type_desc),
                $row->subject,
                $row->receipt_desc,
                '<span class="mask_currency">' . $row->bank_charge . '</span>',
                '<span class="mask_currency">' . $row->receipt_amount . '</span>',
                '<span class="mask_currency">' . ($row->bank_charge + $row->receipt_amount) . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function unallocated_amount($type = 0){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/css/report_group.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $this->load->view('layout/header_no_sidebar', $data_header);
        if($type > 0){
            $this->load->view('ar/report/unallocated_corp', $data);
        }else{
            $this->load->view('ar/report/unallocated', $data);
        }
        $this->load->view('layout/footer');
    }

    public function ajax_unallocated_reservation(){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $where_string = "";
        $date_start = isset($_REQUEST['filter_date_from']) ?  dmy_to_ymd($_REQUEST['filter_date_from']) : date('Y-m-d');

        $records = array();
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                //$where['doc_date >='] = $date_start;
            }
        }

        if(isset($_REQUEST['filter_doc_no'])){
            if($_REQUEST['filter_doc_no'] != ''){
                $like['res.reservation_code'] = $_REQUEST['filter_doc_no'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['res.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_amount_from'])){
            if($_REQUEST['filter_amount_from'] != ''){
                $where['ua.pending_amount >='] = $_REQUEST['filter_amount_from'];
            }
        }

        if(isset($_REQUEST['filter_amount_to'])){
            if($_REQUEST['filter_amount_to'] != ''){
                $where['ua.pending_amount <='] = $_REQUEST['filter_amount_to'];
            }
        }

        $joins = array('view_cs_reservation res' => 'res.reservation_id = ua.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin("fxnARUnallocatedByDate('" . $date_start . "') as ua", $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'res.reservation_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'res.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'res.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ua.pending_amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ua.*, res.reservation_code, res.tenant_fullname',"fxnARUnallocatedByDate('" . $date_start . "') as ua ", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->reservation_code,
                $row->tenant_fullname,
                '<span class="mask_currency">' . $row->pending_amount . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_unallocated_corporate(){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $where_string = "";
        $date_start = isset($_REQUEST['filter_date_from']) ?  dmy_to_ymd($_REQUEST['filter_date_from']) : date('Y-m-d');

        $records = array();
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                //$where['doc_date >='] = $date_start;
            }
        }

        if(isset($_REQUEST['filter_doc_no'])){
            if($_REQUEST['filter_doc_no'] != ''){
                $like['ua.receipt_no'] = $_REQUEST['filter_doc_no'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ua.company_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_amount_from'])){
            if($_REQUEST['filter_amount_from'] != ''){
                $where['ua.unallocated_amount >='] = $_REQUEST['filter_amount_from'];
            }
        }

        if(isset($_REQUEST['filter_amount_to'])){
            if($_REQUEST['filter_amount_to'] != ''){
                $where['ua.unallocated_amount <='] = $_REQUEST['filter_amount_to'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin("fxnARUnallocatedByDateCorp('" . $date_start . "') as ua", $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'ua.receipt_no asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ua.receipt_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ua.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ua.unallocated_amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ua.*',"fxnARUnallocatedByDateCorp('" . $date_start . "') as ua ", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->receipt_no,
                $row->company_name,
                '<span class="mask_currency">' . $row->unallocated_amount . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_unallocated_deposit(){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $where_string = "";
        $date_start = isset($_REQUEST['filter_date_from']) ?  dmy_to_ymd($_REQUEST['filter_date_from']) : date('Y-m-d');

        $records = array();
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                //$where['doc_date >='] = $date_start;
            }
        }

        if(isset($_REQUEST['filter_doc_no'])){
            if($_REQUEST['filter_doc_no'] != ''){
                $like['deposit_no'] = $_REQUEST['filter_doc_no'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['company_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_desc'])){
            if($_REQUEST['filter_desc'] != ''){
                $like['deposit_desc'] = $_REQUEST['filter_desc'];
            }
        }

        if(isset($_REQUEST['filter_amount_from'])){
            if($_REQUEST['filter_amount_from'] != ''){
                $where['deposit_amount >='] = $_REQUEST['filter_amount_from'];
            }
        }

        if(isset($_REQUEST['filter_amount_to'])){
            if($_REQUEST['filter_amount_to'] != ''){
                $where['deposit_amount <='] = $_REQUEST['filter_amount_to'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin("fxnARUnDepositByDateCorp('" . $date_start . "') as ua", $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'deposit_no asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'deposit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'deposit_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'deposit_amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ua.*',"fxnARUnDepositByDateCorp('" . $date_start . "') as ua", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->deposit_no,
                $row->company_name,
                $row->deposit_desc,
                '<span class="mask_currency">' . $row->deposit_amount . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function generate_soa(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        $data = array();

        $data['report_title'] = "Statement Of Account";

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/report/find_soa', $data);
        $this->load->view('layout/footer');
    }

    #region Print

    public function pdf_soa($reservation_id = 0, $isMultiPages = 1) {
        if($reservation_id > 0){
            //Reservation
            $qry = $this->db->query('SELECT * FROM view_cs_reservation WHERE reservation_id = ' . $reservation_id);
            if($qry->num_rows() > 0) {
                $data['row'] = $qry->row_array();

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                //Tenant
                $tenant = $this->db->get_where('ms_tenant', array('tenant_id' => $data['row']['tenant_id']));
                if($tenant->num_rows() > 0){
                    $data['tenant'] = $tenant->row_array();
                }

                //Ledger
                $soa_ledger = array();
                $ledger = $this->db->query('SELECT * FROM fxnCS_ReservationLedger(' . $reservation_id . ') ORDER BY trx_date,order_date, type ');
                if($ledger->num_rows() > 0){
                    $balance = 0;
                    foreach($ledger->result_array() as $rec){
                        if($rec['debit'] > 0){
                            $balance += $rec['debit'];
                        }else{
                            $balance -= $rec['credit'];
                        }

                        array_push($soa_ledger, array('doc_no'=>$rec['doc_no'],'doc_date' => $rec['trx_date'], 'remark' => $rec['remark'], 'amount' => ($rec['debit'] > 0 ? $rec['debit'] : ($rec['credit'] * -1)),'balance' => $balance));
                    }
                }

                $data['ledger'] = $soa_ledger;

                //echo $this->db->last_query();
                $data['multi_pages'] = $isMultiPages > 0 ? true : false;

                $this->load->view('ar/report/pdf_soa', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']['reservation_code'] . ".pdf", array('Attachment'=>0));

            }
            else {
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    public function pdf_debitnote($debitnote_id = 0) {
        if ($debitnote_id > 0) {
            $qry = $this->db->get_where('ar_debitnote_header', array('debitnote_id' => $debitnote_id));
            if ($qry->num_rows() > 0) {
                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                $data['row'] = $qry->row();
                $data['qry_det'] =  $this->db->order_by('detail_id','ASC')->get_where('ar_debitnote_detail', array('debitnote_id' => $data['row']->debitnote_id));

                $this->load->view('ar/debit/pdf_debit_note.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->debit_no . ".pdf", array('Attachment'=>0));

            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

    public function pdf_debitvoucher($debitnote_id = 0) {
        if ($debitnote_id > 0) {
            $qry = $this->db->get_where('ar_debitnote_header', array('debitnote_id' => $debitnote_id));
            if ($qry->num_rows() > 0) {
                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                $data['row'] = $qry->row();
                $data['qry_det'] =  $this->db->order_by('journal_credit','ASC')->get_where('view_get_journal_detail', array('journal_no' => $data['row']->debit_no));

                $this->load->view('ar/debit/pdf_jv_debit.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->debit_no . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

    public function pdf_official_receipt($receipt_no = '') {
        if(trim($receipt_no) != ''){
            $ar_receipt = $this->db->get_where('ar_receipt', array('receipt_no' => $receipt_no));
            if($ar_receipt->num_rows() > 0){
                $rv = $ar_receipt->row_array();

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                //ATTRIBUTES
                $data['receipt_no'] = $receipt_no;
                $data['receipt_from'] = '';
                $data['receipt_address'] = '';
                $data['receipt_amount'] = 0;
                $data['receipt_desc'] = '';
                $data['receipt_date'] = '';

                if($rv['status'] == FLAG_BOOKING_RECEIPT){

                }else if($rv['status'] == FLAG_CASHBOOK){

                }else{
                    $data['receipt_desc'] = $rv['receipt_desc'];
                    if($rv['is_invoice'] > 0) {
                        if ($rv['company_id'] > 0) {
                            $company = $this->db->get_where('ms_company', array('company_id' => $rv['company_id']));
                            if ($company->num_rows() > 0) {
                                $company = $company->row_array();
                                $data['receipt_from'] = $company['company_name'];
                                $data['receipt_address'] = $company['company_address'];
                            }
                        } else {
                            $reservation = $this->db->get_where('view_cs_reservation', array('reservation_id' => $rv['reservation_id']));
                            if ($reservation->num_rows() > 0) {
                                $reservation = $reservation->row_array();

                                $data['receipt_from'] = $reservation['tenant_fullname'];
                                $data['receipt_address'] = $reservation['tenant_address'];
                            }
                        }
                    }else{
                        if($rv['deposit_id'] > 0){
                            //$deposit = $this->db->get_where('ar_deposit_header',array('deposit_id' => $rv['deposit_id']));
                            $deposit = $this->db->query("SELECT ar_deposit_header.reservation_id, ar_deposit_header.company_id, ar_deposit_header.deposit_desc, ISNULL(ms_tenant.tenant_fullname,'') as tenant_fullname, ISNULL(ms_tenant.tenant_address,'') as tenant_address, ISNULL(ms_company.company_name,'') as company_name, ISNULL(ms_company.company_address,'') as company_address FROM ar_deposit_header
                                      JOIN ar_receipt ON ar_deposit_header.deposit_no = ar_receipt.receipt_no
                                      LEFT JOIN ms_tenant ON ms_tenant.tenant_id = ar_receipt.tenant_id
                                      LEFT JOIN ms_company ON ms_company.company_id = ar_deposit_header.company_id
                                      WHERE ar_deposit_header.deposit_id = " . $rv['deposit_id']);
                            if($deposit->num_rows() > 0){
                                $deposit = $deposit->row();
                                $data['receipt_desc'] = $deposit->deposit_desc;

                                if($deposit->company_id > 0){
                                    $data['receipt_from'] = $deposit->company_name;
                                    $data['receipt_address'] = $deposit->company_address;
                                }else {
                                    if (trim($deposit->tenant_fullname) != '') {
                                        $data['receipt_from'] = $deposit->tenant_fullname;
                                        $data['receipt_address'] = $deposit->tenant_address;
                                    } else {
                                        $reservation = $this->db->get_where('view_cs_reservation', array('reservation_id' => $deposit->reservation_id));
                                        if ($reservation->num_rows() > 0) {
                                            $reservation = $reservation->row_array();

                                            $data['receipt_from'] = $reservation['tenant_fullname'];
                                            $data['receipt_address'] = $reservation['tenant_address'];
                                        }
                                    }
                                }

                            }
                        }else{
                            $reservation = $this->db->get_where('view_cs_reservation', array('reservation_id' => $rv['reservation_id']));
                            if ($reservation->num_rows() > 0) {
                                $reservation = $reservation->row_array();

                                if($reservation['reservation_type'] == RES_TYPE::CORPORATE){
                                    $data['receipt_from'] = $reservation['company_name'];
                                    $data['receipt_address'] = $reservation['company_address'];
                                }else{
                                    $data['receipt_from'] = $reservation['tenant_fullname'];
                                    $data['receipt_address'] = $reservation['tenant_address'];
                                }
                            }
                        }
                    }

                    $data['receipt_amount'] = $rv['receipt_bankcharges'] + $rv['receipt_paymentamount'];
                    $data['receipt_date'] = $rv['receipt_date'];
                }

                $created = $this->db->get_where("ms_user", array("user_id" => $rv['created_by']));
                if($created->num_rows() > 0){
                    $data['created_by_name'] = $created->row()->user_fullname;
                }

                $this->load->view('ar/report/pdf_officialreceipt', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($receipt_no . ".pdf", array('Attachment'=>0));

            }else{
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    public function pdf_official_voucher($receipt_no = '') {
        if($receipt_no != ''){
            $this->load->model('finance/mdl_finance');

            /*
            $qry = $this->db->get_where('cs_booking_receipt', array('bookingreceipt_id' => $doc_id));

            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                $journal = $this->db->get_where('gl_postjournal_header', array('journal_no' => $data['row']->receipt_no));
                if($journal->num_rows() > 0){
                    $journal = $journal->row();
                }

                $data['journal'] = $journal;

                $where['gl_postjournal_header.journal_no'] = $data['row']->receipt_no;
                $data['qry_det'] = $this->mdl_finance->getJoin('gl_postjournal_detail.*, gl_coa.coa_code, gl_coa.coa_desc ' ,
                    'gl_postjournal_detail',
                    array('gl_coa' => 'gl_coa.coa_id = gl_postjournal_detail.coa_id',
                        'gl_postjournal_header' => 'gl_postjournal_header.postheader_id = gl_postjournal_detail.postheader_id'),
                    $where);

                $data['doc_type_title'] = 'RECEIVE VOUCHER';
                $data['subject_title'] = 'Receive From';
                $data['cashbook_type'] = 3;
                $journalno = $data['row']->receipt_no;

                $this->load->view('ar/report/pdf_receive_voucher.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->journal_no . ".pdf", array('Attachment'=>0));
            }else{
                tpd_404();
            }
            */
        }else{
            tpd_404();
        }
    }

    public function pdf_aging($date_aging ,$company_name ='', $isMultiPages = 1) {
        if($date_aging !=''){
            //aging
			$aging = array();
			$data['date_aging'] = $date_aging;
			$date_aging = dmy_to_ymd($date_aging);
			$where  ="";
			if($company_name!= ''){
				$where  = "WHERE company_name like '%".$company_name."%'";
			}
            $qry = $this->db->query("SELECT * FROM fxnAR_InquiryAging( '" . $date_aging."') ".$where." order by company_name");

            if($qry->num_rows() > 0) {
				foreach($qry->result_array() as $row){
				array_push($aging, array('company_name'=> $row['company_name'],
											'D0'=> $row['D0'],
											'D31'=> $row['D31'],
											'D61'=> $row['D61'],
											'D91'=> $row['D91']
				));
				}

                $data['aging'] = $aging;

                //echo $this->db->last_query();

            }
			 $data['multi_pages'] = $isMultiPages > 0 ? true : false;

                $this->load->view('ar/report/pdf_aging', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream("aging".$date_aging . ".pdf", array('Attachment'=>0));

        }
        else {
            tpd_404();
        }
    }

        public function pdf_aging_detail($date_aging ,$company_name ='', $isMultiPages = 1) {
        if($date_aging !=''){
            //aging
			$aging = array();
			$data['date_aging'] = $date_aging;
			$date_aging = dmy_to_ymd($date_aging);
			$where  ="";
			if($company_name!= ''){
				$where  = "WHERE company_name like '%".$company_name."%'";
			}
            $qry = $this->db->query("SELECT * FROM fxnAR_InquiryAgingDetail( '" . $date_aging."') ".$where." order by company_name");

            if($qry->num_rows() > 0) {
				foreach($qry->result_array() as $row){
				array_push($aging, array('company_name'=> $row['company_name'],
											'inv_no'=> $row['inv_no'],
											'D0'=> $row['D0'],
											'D31'=> $row['D31'],
											'D61'=> $row['D61'],
											'D91'=> $row['D91']
				));
				}

                $data['aging'] = $aging;

                //echo $this->db->last_query();


            }
				$data['multi_pages'] = $isMultiPages > 0 ? true : false;

                $this->load->view('ar/report/pdf_aging_detail', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream("aging_detail_".$date_aging . ".pdf", array('Attachment'=>0));
        }
        else {
            tpd_404();
        }
    }

    #endregion

	public function generate_ar_aging(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        $data = array();

        $data['report_title'] = "AR Aging";

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/report/find_aging', $data);
        $this->load->view('layout/footer');
    }

	public function generate_ar_aging_detail(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        $data = array();

        $data['report_title'] = "AR Aging";

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/report/find_aging_detail', $data);
        $this->load->view('layout/footer');
    }

}