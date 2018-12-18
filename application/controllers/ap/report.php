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

    public function aging_idr(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data['qry_uom'] = $this->mdl_general->get('in_ms_uom', array('status <> ' => STATUS_DELETE), array(), 'uom_code');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ap/report/aging_idr', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_aging_idr(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $tgl = date('Y-m-d');

        if(isset($_REQUEST['filter_date'])){
            if($_REQUEST['filter_date'] != ''){
                $tgl = dmy_to_ymd($_REQUEST['filter_date']);
            }
        }

        if(isset($_REQUEST['filter_creditor_name'])){
            if($_REQUEST['filter_creditor_name'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_creditor_name'];
            }
        }
        if(isset($_REQUEST['filter_31_from'])){
            if($_REQUEST['filter_31_from'] != ''){
                $where['d0 >='] = $_REQUEST['filter_31_from'];
            }
        }
        if(isset($_REQUEST['filter_31_to'])){
            if($_REQUEST['filter_31_to'] != ''){
                $where['d0 <='] = $_REQUEST['filter_31_to'];
            }
        }
        if(isset($_REQUEST['filter_31_60_from'])){
            if($_REQUEST['filter_31_60_from'] != ''){
                $where['d31 >='] = $_REQUEST['filter_31_60_from'];
            }
        }
        if(isset($_REQUEST['filter_31_60_to'])){
            if($_REQUEST['filter_31_60_to'] != ''){
                $where['d31 <='] = $_REQUEST['filter_31_60_to'];
            }
        }
        if(isset($_REQUEST['filter_61_90_from'])){
            if($_REQUEST['filter_61_90_from'] != ''){
                $where['d61 >='] = $_REQUEST['filter_61_90_from'];
            }
        }
        if(isset($_REQUEST['filter_61_90_to'])){
            if($_REQUEST['filter_61_90_to'] != ''){
                $where['d61 <='] = $_REQUEST['filter_61_90_to'];
            }
        }
        if(isset($_REQUEST['filter_91_from'])){
            if($_REQUEST['filter_91_from'] != ''){
                $where['d91 >='] = $_REQUEST['filter_91_from'];
            }
        }
        if(isset($_REQUEST['filter_91_to'])){
            if($_REQUEST['filter_91_to'] != ''){
                $where['d91 <='] = $_REQUEST['filter_91_to'];
            }
        }
        if(isset($_REQUEST['filter_total_from'])){
            if($_REQUEST['filter_total_from'] != ''){
                $where['total >='] = $_REQUEST['filter_total_from'];
            }
        }
        if(isset($_REQUEST['filter_total_to'])){
            if($_REQUEST['filter_total_to'] != ''){
                $where['total <='] = $_REQUEST['filter_total_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('fxnAP_EnquiryAging_idr("' . $tgl . '")', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'supplier_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'd0 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'd31 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'd61 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'd91 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'total ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('fxnAP_EnquiryAging_idr("' . $tgl . '")', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->supplier_name,
                '<span class="mask_currency">' . $row->d0 . '</span>',
                '<span class="mask_currency">' . $row->d31 . '</span>',
                '<span class="mask_currency">' . $row->d61 . '</span>',
                '<span class="mask_currency">' . $row->d91 . '</span>',
                '<span class="mask_currency">' . $row->total . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function aging(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data['qry_uom'] = $this->mdl_general->get('in_ms_uom', array('status <> ' => STATUS_DELETE), array(), 'uom_code');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ap/report/aging', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_aging(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $tgl = date('Y-m-d');

        if(isset($_REQUEST['filter_date'])){
            if($_REQUEST['filter_date'] != ''){
                $tgl = dmy_to_ymd($_REQUEST['filter_date']);
            }
        }

        if(isset($_REQUEST['filter_creditor_name'])){
            if($_REQUEST['filter_creditor_name'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_creditor_name'];
            }
        }
        if(isset($_REQUEST['filter_31_from'])){
            if($_REQUEST['filter_31_from'] != ''){
                $where['d0 >='] = $_REQUEST['filter_31_from'];
            }
        }
        if(isset($_REQUEST['filter_31_to'])){
            if($_REQUEST['filter_31_to'] != ''){
                $where['d0 <='] = $_REQUEST['filter_31_to'];
            }
        }
        if(isset($_REQUEST['filter_31_60_from'])){
            if($_REQUEST['filter_31_60_from'] != ''){
                $where['d31 >='] = $_REQUEST['filter_31_60_from'];
            }
        }
        if(isset($_REQUEST['filter_31_60_to'])){
            if($_REQUEST['filter_31_60_to'] != ''){
                $where['d31 <='] = $_REQUEST['filter_31_60_to'];
            }
        }
        if(isset($_REQUEST['filter_61_90_from'])){
            if($_REQUEST['filter_61_90_from'] != ''){
                $where['d61 >='] = $_REQUEST['filter_61_90_from'];
            }
        }
        if(isset($_REQUEST['filter_61_90_to'])){
            if($_REQUEST['filter_61_90_to'] != ''){
                $where['d61 <='] = $_REQUEST['filter_61_90_to'];
            }
        }
        if(isset($_REQUEST['filter_91_from'])){
            if($_REQUEST['filter_91_from'] != ''){
                $where['d91 >='] = $_REQUEST['filter_91_from'];
            }
        }
        if(isset($_REQUEST['filter_91_to'])){
            if($_REQUEST['filter_91_to'] != ''){
                $where['d91 <='] = $_REQUEST['filter_91_to'];
            }
        }
        if(isset($_REQUEST['filter_total_from'])){
            if($_REQUEST['filter_total_from'] != ''){
                $where['total >='] = $_REQUEST['filter_total_from'];
            }
        }
        if(isset($_REQUEST['filter_total_to'])){
            if($_REQUEST['filter_total_to'] != ''){
                $where['total <='] = $_REQUEST['filter_total_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('fxnAP_EnquiryAging("' . $tgl . '")', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'currencytype_code asc, supplier_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'd0 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'd31 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'd61 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'd91 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'total ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('fxnAP_EnquiryAging("' . $tgl . '")', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->supplier_name,
                $row->currencytype_code,
                '<span class="mask_currency">' . $row->d0 . '</span>',
                '<span class="mask_currency">' . $row->d31 . '</span>',
                '<span class="mask_currency">' . $row->d61 . '</span>',
                '<span class="mask_currency">' . $row->d91 . '</span>',
                '<span class="mask_currency">' . $row->total . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function aging_detail_idr(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ap/report/aging_detail_idr', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_aging_detail_idr(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $tgl = date('Y-m-d');

        if(isset($_REQUEST['filter_date'])){
            if($_REQUEST['filter_date'] != ''){
                $tgl = dmy_to_ymd($_REQUEST['filter_date']);
            }
        }

        if(isset($_REQUEST['filter_creditor_name'])){
            if($_REQUEST['filter_creditor_name'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_creditor_name'];
            }
        }
        if(isset($_REQUEST['filter_inv_code'])){
            if($_REQUEST['filter_inv_code'] != ''){
                $like['inv_code'] = $_REQUEST['filter_inv_code'];
            }
        }
        if(isset($_REQUEST['filter_31_from'])){
            if($_REQUEST['filter_31_from'] != ''){
                $where['d0 >='] = $_REQUEST['filter_31_from'];
            }
        }
        if(isset($_REQUEST['filter_31_to'])){
            if($_REQUEST['filter_31_to'] != ''){
                $where['d0 <='] = $_REQUEST['filter_31_to'];
            }
        }
        if(isset($_REQUEST['filter_31_60_from'])){
            if($_REQUEST['filter_31_60_from'] != ''){
                $where['d31 >='] = $_REQUEST['filter_31_60_from'];
            }
        }
        if(isset($_REQUEST['filter_31_60_to'])){
            if($_REQUEST['filter_31_60_to'] != ''){
                $where['d31 <='] = $_REQUEST['filter_31_60_to'];
            }
        }
        if(isset($_REQUEST['filter_61_90_from'])){
            if($_REQUEST['filter_61_90_from'] != ''){
                $where['d61 >='] = $_REQUEST['filter_61_90_from'];
            }
        }
        if(isset($_REQUEST['filter_61_90_to'])){
            if($_REQUEST['filter_61_90_to'] != ''){
                $where['d61 <='] = $_REQUEST['filter_61_90_to'];
            }
        }
        if(isset($_REQUEST['filter_91_from'])){
            if($_REQUEST['filter_91_from'] != ''){
                $where['d91 >='] = $_REQUEST['filter_91_from'];
            }
        }
        if(isset($_REQUEST['filter_91_to'])){
            if($_REQUEST['filter_91_to'] != ''){
                $where['d91 <='] = $_REQUEST['filter_91_to'];
            }
        }
        if(isset($_REQUEST['filter_total_from'])){
            if($_REQUEST['filter_total_from'] != ''){
                $where['total >='] = $_REQUEST['filter_total_from'];
            }
        }
        if(isset($_REQUEST['filter_total_to'])){
            if($_REQUEST['filter_total_to'] != ''){
                $where['total <='] = $_REQUEST['filter_total_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('fxnAP_EnquiryAging_idr_detail("' . $tgl . '")', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'supplier_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'inv_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'd0 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'd31 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'd61 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'd91 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'total ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('fxnAP_EnquiryAging_idr_detail("' . $tgl . '")', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->supplier_name,
                $row->inv_code,
                '<span class="mask_currency">' . $row->d0 . '</span>',
                '<span class="mask_currency">' . $row->d31 . '</span>',
                '<span class="mask_currency">' . $row->d61 . '</span>',
                '<span class="mask_currency">' . $row->d91 . '</span>',
                '<span class="mask_currency">' . $row->total . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function aging_detail(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ap/report/aging_detail', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_aging_detail(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $tgl = date('Y-m-d');

        if(isset($_REQUEST['filter_date'])){
            if($_REQUEST['filter_date'] != ''){
                $tgl = dmy_to_ymd($_REQUEST['filter_date']);
            }
        }

        if(isset($_REQUEST['filter_creditor_name'])){
            if($_REQUEST['filter_creditor_name'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_creditor_name'];
            }
        }
        if(isset($_REQUEST['filter_inv_code'])){
            if($_REQUEST['filter_inv_code'] != ''){
                $like['inv_code'] = $_REQUEST['filter_inv_code'];
            }
        }
        if(isset($_REQUEST['filter_curr'])){
            if($_REQUEST['filter_curr'] != ''){
                $like['currencytype_code'] = $_REQUEST['filter_curr'];
            }
        }
        if(isset($_REQUEST['filter_31_from'])){
            if($_REQUEST['filter_31_from'] != ''){
                $where['d0 >='] = $_REQUEST['filter_31_from'];
            }
        }
        if(isset($_REQUEST['filter_31_to'])){
            if($_REQUEST['filter_31_to'] != ''){
                $where['d0 <='] = $_REQUEST['filter_31_to'];
            }
        }
        if(isset($_REQUEST['filter_31_60_from'])){
            if($_REQUEST['filter_31_60_from'] != ''){
                $where['d31 >='] = $_REQUEST['filter_31_60_from'];
            }
        }
        if(isset($_REQUEST['filter_31_60_to'])){
            if($_REQUEST['filter_31_60_to'] != ''){
                $where['d31 <='] = $_REQUEST['filter_31_60_to'];
            }
        }
        if(isset($_REQUEST['filter_61_90_from'])){
            if($_REQUEST['filter_61_90_from'] != ''){
                $where['d61 >='] = $_REQUEST['filter_61_90_from'];
            }
        }
        if(isset($_REQUEST['filter_61_90_to'])){
            if($_REQUEST['filter_61_90_to'] != ''){
                $where['d61 <='] = $_REQUEST['filter_61_90_to'];
            }
        }
        if(isset($_REQUEST['filter_91_from'])){
            if($_REQUEST['filter_91_from'] != ''){
                $where['d91 >='] = $_REQUEST['filter_91_from'];
            }
        }
        if(isset($_REQUEST['filter_91_to'])){
            if($_REQUEST['filter_91_to'] != ''){
                $where['d91 <='] = $_REQUEST['filter_91_to'];
            }
        }
        if(isset($_REQUEST['filter_total_from'])){
            if($_REQUEST['filter_total_from'] != ''){
                $where['total >='] = $_REQUEST['filter_total_from'];
            }
        }
        if(isset($_REQUEST['filter_total_to'])){
            if($_REQUEST['filter_total_to'] != ''){
                $where['total <='] = $_REQUEST['filter_total_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('fxnAP_EnquiryAging_detail("' . $tgl . '")', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'currencytype_code asc, supplier_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'inv_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'currencytype_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'd0 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'd31 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'd61 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'd91 ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 7){
                $order = 'total ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('fxnAP_EnquiryAging_detail("' . $tgl . '")', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->supplier_name,
                $row->inv_code,
                $row->currencytype_code,
                '<span class="mask_currency">' . $row->d0 . '</span>',
                '<span class="mask_currency">' . $row->d31 . '</span>',
                '<span class="mask_currency">' . $row->d61 . '</span>',
                '<span class="mask_currency">' . $row->d91 . '</span>',
                '<span class="mask_currency">' . $row->total . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function creditor_allocation(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ap/report/creditor_allocation', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_creditor_allocation(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $where['doc_date >='] = date("Y-m-d",strtotime("-1 month"));
        $where['doc_date <='] = date("Y-m-d");

        if(isset($_REQUEST['filter_creditor_name'])){
            if($_REQUEST['filter_creditor_name'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_creditor_name'];
            }
        }
        if(isset($_REQUEST['filter_doc_1'])){
            if($_REQUEST['filter_doc_1'] != ''){
                $like['doc_code'] = $_REQUEST['filter_doc_1'];
            }
        }
        if(isset($_REQUEST['filter_doc_date_from'])){
            if($_REQUEST['filter_doc_date_from'] != ''){
                $where['doc_date >='] = dmy_to_ymd($_REQUEST['filter_doc_date_from']);
            }
        }
        if(isset($_REQUEST['filter_doc_date_to'])){
            if($_REQUEST['filter_doc_date_to'] != ''){
                $where['doc_date <='] = dmy_to_ymd($_REQUEST['filter_doc_date_to']);
            }
        }
        if(isset($_REQUEST['filter_amount_from'])){
            if($_REQUEST['filter_amount_from'] != ''){
                $where['amount >='] = $_REQUEST['filter_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_amount_to'])){
            if($_REQUEST['filter_amount_to'] != ''){
                $where['amount <='] = $_REQUEST['filter_amount_to'];
            }
        }
        if(isset($_REQUEST['filter_doc_2'])){
            if($_REQUEST['filter_doc_2'] != ''){
                $like['i_doc_code'] = $_REQUEST['filter_doc_2'];
            }
        }
        if(isset($_REQUEST['filter_i_doc_date_from'])){
            if($_REQUEST['filter_i_doc_date_from'] != ''){
                $where['i_doc_date >='] = dmy_to_ymd($_REQUEST['filter_i_doc_date_from']);
            }
        }
        if(isset($_REQUEST['filter_i_doc_date_to'])){
            if($_REQUEST['filter_i_doc_date_to'] != ''){
                $where['i_doc_date <='] = dmy_to_ymd($_REQUEST['filter_i_doc_date_to']);
            }
        }
        if(isset($_REQUEST['filter_i_amount_from'])){
            if($_REQUEST['filter_i_amount_from'] != ''){
                $where['i_amount >='] = $_REQUEST['filter_i_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_i_amount_to'])){
            if($_REQUEST['filter_i_amount_to'] != ''){
                $where['i_amount <='] = $_REQUEST['filter_i_amount_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_ap_creditor_allocation', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'supplier_name asc, doc_date desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'doc_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'doc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'amount ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'i_doc_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'i_doc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'i_amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_ap_creditor_allocation', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->supplier_name,
                $row->doc_code,
                ymd_to_dmy($row->doc_date),
                '<span class="mask_currency">' . $row->amount . '</span>',
                $row->i_doc_code,
                ymd_to_dmy($row->i_doc_date),
                '<span class="mask_currency">' . $row->i_amount . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function creditor_ledger(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ap/report/creditor_ledger', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_creditor_ledger(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $where['doc_date >='] = date("Y-m-d",strtotime("-1 month"));
        $where['doc_date <='] = date("Y-m-d");

        if(isset($_REQUEST['filter_creditor_name'])){
            if($_REQUEST['filter_creditor_name'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_creditor_name'];
            }
        }
        if(isset($_REQUEST['filter_doc_no'])){
            if($_REQUEST['filter_doc_no'] != ''){
                $like['doc_code'] = $_REQUEST['filter_doc_no'];
            }
        }
        if(isset($_REQUEST['filter_desc'])){
            if($_REQUEST['filter_desc'] != ''){
                $like['doc_desc'] = $_REQUEST['filter_desc'];
            }
        }
        if(isset($_REQUEST['filter_doc_date_from'])){
            if($_REQUEST['filter_doc_date_from'] != ''){
                $where['doc_date >='] = dmy_to_ymd($_REQUEST['filter_doc_date_from']);
            }
        }
        if(isset($_REQUEST['filter_doc_date_to'])){
            if($_REQUEST['filter_doc_date_to'] != ''){
                $where['doc_date <='] = dmy_to_ymd($_REQUEST['filter_doc_date_to']);
            }
        }
        if(isset($_REQUEST['filter_curr'])){
            if($_REQUEST['filter_curr'] != ''){
                $where['currency'] = $_REQUEST['filter_curr'];
            }
        }
        if(isset($_REQUEST['filter_amount_from'])){
            if($_REQUEST['filter_amount_from'] != ''){
                $where['amount >='] = $_REQUEST['filter_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_amount_to'])){
            if($_REQUEST['filter_amount_to'] != ''){
                $where['amount <='] = $_REQUEST['filter_amount_to'];
            }
        }
        if(isset($_REQUEST['filter_idr_amount_from'])){
            if($_REQUEST['filter_idr_amount_from'] != ''){
                $where['idr_amount >='] = $_REQUEST['filter_idr_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_idr_amount_to'])){
            if($_REQUEST['filter_idr_amount_to'] != ''){
                $where['idr_amount <='] = $_REQUEST['filter_idr_amount_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_ap_creditorledger', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'currency asc, supplier_name asc, doc_date desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'doc_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'doc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'currency ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'amount ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'idr_amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_ap_creditorledger', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->supplier_name,
                $row->doc_code,
                $row->doc_desc,
                ymd_to_dmy($row->doc_date),
                $row->currency,
                '<span class="mask_currency">' . $row->amount . '</span>',
                '<span class="mask_currency">' . $row->idr_amount . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ap_threeway(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data['qry_curr'] = $this->db->get('currencytype');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ap/report/ap_threeway', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_ap_threeway(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $where['grn_date >='] = date("Y-m-d",strtotime("-1 month"));
        $where['grn_date <='] = date("Y-m-d");

        if(isset($_REQUEST['filter_supplier_name'])){
            if($_REQUEST['filter_supplier_name'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_supplier_name'];
            }
        }
        if(isset($_REQUEST['filter_grn_no'])){
            if($_REQUEST['filter_grn_no'] != ''){
                $like['grn_code'] = $_REQUEST['filter_grn_no'];
            }
        }
        if(isset($_REQUEST['filter_grn_date_from'])){
            if($_REQUEST['filter_grn_date_from'] != ''){
                $where['grn_date >='] = dmy_to_ymd($_REQUEST['filter_grn_date_from']);
            }
        }
        if(isset($_REQUEST['filter_grn_date_to'])){
            if($_REQUEST['filter_grn_date_to'] != ''){
                $where['grn_date <='] = dmy_to_ymd($_REQUEST['filter_grn_date_to']);
            }
        }
        if(isset($_REQUEST['filter_item_desc'])){
            if($_REQUEST['filter_item_desc'] != ''){
                $like['item_desc'] = $_REQUEST['filter_item_desc'];
            }
        }
        if(isset($_REQUEST['filter_grn_qty_from'])){
            if($_REQUEST['filter_grn_qty_from'] != ''){
                $where['grn_qty >='] = $_REQUEST['filter_grn_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_grn_qty_to'])){
            if($_REQUEST['filter_grn_qty_to'] != ''){
                $where['grn_qty <='] = $_REQUEST['filter_grn_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_grn_amount_from'])){
            if($_REQUEST['filter_grn_amount_from'] != ''){
                $where['grn_amount >='] = $_REQUEST['filter_grn_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_grn_amount_to'])){
            if($_REQUEST['filter_grn_amount_to'] != ''){
                $where['grn_amount <='] = $_REQUEST['filter_grn_amount_to'];
            }
        }
        if(isset($_REQUEST['filter_inv_no'])){
            if($_REQUEST['filter_inv_no'] != ''){
                $like['inv_code'] = $_REQUEST['filter_inv_no'];
            }
        }
        if(isset($_REQUEST['filter_inv_date_from'])){
            if($_REQUEST['filter_inv_date_from'] != ''){
                $where['inv_date >='] = dmy_to_ymd($_REQUEST['filter_inv_date_from']);
            }
        }
        if(isset($_REQUEST['filter_inv_date_to'])){
            if($_REQUEST['filter_inv_date_to'] != ''){
                $where['inv_date <='] = dmy_to_ymd($_REQUEST['filter_inv_date_to']);
            }
        }
        if(isset($_REQUEST['filter_inv_curr'])){
            if($_REQUEST['filter_inv_curr'] != ''){
                $where['inv_curr'] = $_REQUEST['filter_inv_curr'];
            }
        }
        if(isset($_REQUEST['filter_inv_amount_from'])){
            if($_REQUEST['filter_inv_amount_from'] != ''){
                $where['inv_amount >='] = $_REQUEST['filter_inv_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_inv_amount_to'])){
            if($_REQUEST['filter_inv_amount_to'] != ''){
                $where['inv_amount <='] = $_REQUEST['filter_inv_amount_to'];
            }
        }
        if(isset($_REQUEST['filter_pay_no'])){
            if($_REQUEST['filter_pay_no'] != ''){
                $like['payment_code'] = $_REQUEST['filter_pay_no'];
            }
        }
        if(isset($_REQUEST['filter_pay_date_from'])){
            if($_REQUEST['filter_pay_date_from'] != ''){
                $where['payment_date >='] = dmy_to_ymd($_REQUEST['filter_pay_date_from']);
            }
        }
        if(isset($_REQUEST['filter_pay_date_to'])){
            if($_REQUEST['filter_pay_date_to'] != ''){
                $where['payment_date <='] = dmy_to_ymd($_REQUEST['filter_pay_date_to']);
            }
        }
        if(isset($_REQUEST['filter_pay_curr'])){
            if($_REQUEST['filter_pay_curr'] != ''){
                $where['payment_curr'] = $_REQUEST['filter_pay_curr'];
            }
        }
        if(isset($_REQUEST['filter_pay_amount_from'])){
            if($_REQUEST['filter_pay_amount_from'] != ''){
                $where['payment_amount >='] = $_REQUEST['filter_pay_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_pay_amount_to'])){
            if($_REQUEST['filter_pay_amount_to'] != ''){
                $where['payment_amount <='] = $_REQUEST['filter_pay_amount_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_ap_threeway', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'grn_date desc, item_desc asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'grn_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'grn_date ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_ap_threeway', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->supplier_name,
                $row->grn_code,
                ymd_to_dmy($row->grn_date),
                $row->item_desc,
                '<span class="mask_currency">' . $row->grn_qty . '</span>',
                '<span class="mask_currency">' . $row->grn_amount . '</span>',
                $row->inv_code,
                ymd_to_dmy($row->inv_date),
                $row->inv_curr,
                '<span class="mask_currency">' . $row->inv_amount . '</span>',
                $row->payment_code,
                ymd_to_dmy($row->payment_date),
                $row->payment_curr,
                '<span class="mask_currency">' . $row->payment_amount . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function return_dn(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ap/report/unclose_return', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_unclose_return(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();

        if(isset($_REQUEST['filter_return_code'])){
            if($_REQUEST['filter_return_code'] != ''){
                $like['return_code'] = $_REQUEST['filter_return_code'];
            }
        }
        if(isset($_REQUEST['filter_return_date_from'])){
            if($_REQUEST['filter_return_date_from'] != ''){
                $where['return_date >='] = dmy_to_ymd($_REQUEST['filter_return_date_from']);
            }
        }
        if(isset($_REQUEST['filter_return_date_to'])){
            if($_REQUEST['filter_return_date_to'] != ''){
                $where['return_date <='] = dmy_to_ymd($_REQUEST['filter_return_date_to']);
            }
        }
        if(isset($_REQUEST['filter_return_remarks'])){
            if($_REQUEST['filter_return_remarks'] != ''){
                $like['remarks'] = $_REQUEST['filter_return_remarks'];
            }
        }
        if(isset($_REQUEST['filter_grn_code'])){
            if($_REQUEST['filter_grn_code'] != ''){
                $like['grn_code'] = $_REQUEST['filter_grn_code'];
            }
        }
        if(isset($_REQUEST['filter_grn_date_from'])){
            if($_REQUEST['filter_grn_date_from'] != ''){
                $where['grn_date >='] = dmy_to_ymd($_REQUEST['filter_grn_date_from']);
            }
        }
        if(isset($_REQUEST['filter_grn_date_to'])){
            if($_REQUEST['filter_grn_date_to'] != ''){
                $where['grn_date <='] = dmy_to_ymd($_REQUEST['filter_grn_date_to']);
            }
        }
        if(isset($_REQUEST['filter_dn_code'])){
            if($_REQUEST['filter_dn_code'] != ''){
                $like['debitnote_code'] = $_REQUEST['filter_dn_code'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_ap_unclose_return', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'return_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'return_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'return_date ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_ap_unclose_return', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->return_code,
                ymd_to_dmy($row->return_date),
                $row->remarks,
                get_status_name($row->return_status),
                $row->grn_code,
                ymd_to_dmy($row->grn_date),
                $row->inv_curr,
                '<span class="mask_currency">' . $row->inv_amount . '</span>',
                $row->debitnote_code,
                get_status_name($row->debitnote_status),
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function uninvoice_report(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('ap/report/uninvoice_report', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_uninvoice_report(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();

        $tgl_from = date("Y-m-d",strtotime("-1 month"));
        $tgl_to = date("Y-m-d");

        if(isset($_REQUEST['filter_supplier'])){
            if($_REQUEST['filter_supplier'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }
        if(isset($_REQUEST['filter_doc_code'])){
            if($_REQUEST['filter_doc_code'] != ''){
                $like['journal_no'] = $_REQUEST['filter_doc_code'];
            }
        }
        if(isset($_REQUEST['filter_doc_date_from'])){
            if($_REQUEST['filter_doc_date_from'] != ''){
                $tgl_from = dmy_to_ymd($_REQUEST['filter_doc_date_from']);
            }
        }
        if(isset($_REQUEST['filter_doc_date_to'])){
            if($_REQUEST['filter_doc_date_to'] != ''){
                $tgl_to = dmy_to_ymd($_REQUEST['filter_doc_date_to']);
            }
        }
        if(isset($_REQUEST['filter_amount_from'])){
            if($_REQUEST['filter_amount_from'] != ''){
                $where['amount >='] = $_REQUEST['filter_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_amount_to'])){
            if($_REQUEST['filter_amount_to'] != ''){
                $where['amount <='] = $_REQUEST['filter_amount_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count("fxnUninvoiceReport(106, '" . $tgl_from . "', '" . $tgl_to . "')", $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'doc_date desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'journal_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'doc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get("fxnUninvoiceReport(106, '" . $tgl_from . "', '" . $tgl_to . "')", $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->supplier_name,
                $row->journal_no,
                ymd_to_dmy($row->doc_date),
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

}