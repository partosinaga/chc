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

    public function sales_trans(){
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
        $this->load->view('pos/report/sales_trans', $data);
        $this->load->view('layout/footer');
    }

    public function xreport_sales_trans(){
        $this->load->model('finance/mdl_finance');

        $currentDate = date('Y-m-d');

        $where = array();
        $whereIn = array();

        $where['vw.bankaccount_id >'] = 0;

        $like = array();
        $whereString = "";
        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['vw.journal_no'] = $_REQUEST['filter_no'];
            }
        }

        $filter_date_from = date('Y-m-d');
        $filter_date_to = date('Y-m-d');

        if(isset($_REQUEST['filter_date_from'])){
            $filter_date_from = dmy_to_ymd($_REQUEST['filter_date_from']);
        }

        if(isset($_REQUEST['filter_date_to'])){
            $filter_date_to = dmy_to_ymd($_REQUEST['filter_date_to']);
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                //$whereString = "view_cs_reservation.tenant_fullname like '%" . $_REQUEST['filter_name'] . "%' " ;
                $like['vw.customer_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_itemdesc'])){
            if($_REQUEST['filter_itemdesc'] != ''){
                //$like['view_cs_reservation.is_vip'] = $_REQUEST['filter_type'];
                $like['vw.item_desc'] = $_REQUEST['filter_itemdesc'];
            }
        }

        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $like['vw.status'] = $_REQUEST['filter_status'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin("fxnPOSTransaction('". $filter_date_from ."','" . $filter_date_to ."') as vw", $joins, $where, $like, "", $whereIn, $whereString);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'vw.bill_date ASC,vw.journal_no ASC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'vw.journal_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'vw.bill_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'vw.customer_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'vw.item_desc ' . $_REQUEST['order'][0]['dir'];
            }

        }

        $qry = $this->mdl_finance->getJoin("vw.*","fxnPOSTransaction('". $filter_date_from ."','" . $filter_date_to ."') as vw", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, "", $whereIn, $whereString);

        $records["data"] = array();
        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
			$records["data"][] = array(
                $row->journal_no,
                dmy_from_db($row->bill_date),
                $row->customer_name,
                $row->item_desc,
                $row->item_qty,
                '<span class="mask_currency">' . round($row->rate,0) . '</span>',
                '<span class="mask_currency">' . round($row->tax,0) . '</span>',
                '<span class="mask_currency">' . round($row->amount + $row->tax,0) . '</span>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    #region Print

    public function pdf_sales_trans($dmy_from = '',$dmy_to = '') {
        if($dmy_from != ''){
            $this->load->model('finance/mdl_finance');

            $data['date_from'] = $dmy_from;
            $data['date_to'] = $dmy_to;

            //Company Profile
            $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
            if($profile->num_rows() > 0){
                $data['profile'] = $profile->row_array();
            }

			$currentDate = date('Y-m-d');

			$where = array();
			$whereIn = "";

            $where['vw.bankaccount_id >'] = 0;

			$like = array();

            $filter_date_from = date('Y-m-d');
            $filter_date_to = date('Y-m-d');

            if($dmy_from != ''){
                $filter_date_from = dmy_to_ymd($dmy_from);
            }

            if($dmy_to != ''){
                $filter_date_to = dmy_to_ymd($dmy_to);
            }

			$joins = array();

			$order = 'vw.bill_date ASC,vw.journal_no ASC';
			$qry = $this->mdl_finance->getJoin("vw.*","fxnPOSTransaction('". $filter_date_from ."','" . $filter_date_to ."') as vw", $joins, $where, $like, $order);

            if($qry->num_rows() > 0){
                $data['detail'] = $qry->result_array();

            }

            $this->load->view('pos/report/pdf_sales_trans', $data);

            $html = $this->output->get_output();

            $this->load->library('dompdf_gen');

            $this->dompdf->set_paper("A4", "landscape");
            $this->dompdf->load_html($html);
            $this->dompdf->render();


            $this->dompdf->stream(date('Y_m_d_H_i_s') . ".pdf", array('Attachment' => 0));

            //wkhtml_print(array('page-size' =>'A4','orientation'=>'landscape'));
        }
        else {
            tpd_404();
        }
    }

    #endregion

}