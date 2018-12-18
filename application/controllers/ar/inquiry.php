<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inquiry extends CI_Controller {
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
    }

    public function index()
    {
        $this->inquiry_find();
    }

    #region Corporate Manage

    public function inquiry_find(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/inquiry/inquiry_find.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_inquiry_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $currentDate = date('Y-m-d');

        $where = array();
        $whereIn = array(ORDER_STATUS::CHECKIN, ORDER_STATUS::CHECKOUT);

        $like = array();
        $whereString = "";
        if(isset($_REQUEST['filter_code'])){
            if($_REQUEST['filter_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_code'];
            }
        }

        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,view_cs_reservation.reservation_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }

        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,view_cs_reservation.reservation_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_room'])){
            if($_REQUEST['filter_room'] != ''){
                $like['view_cs_reservation.room'] = $_REQUEST['filter_room'];
            }
        }

        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $where['view_cs_reservation.reservation_type'] = $_REQUEST['filter_type'];
            }
        }

        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['ms_company.company_name'] = $_REQUEST['filter_company'];
            }
        }

        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $like['view_cs_reservation.reservation_type'] = $_REQUEST['filter_type'];
            }
        }

        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $like['view_cs_reservation.status'] = $_REQUEST['filter_status'];
            }
        }

        $joins = array('ms_company' => 'ms_company.company_id = view_cs_reservation.company_id');
        if(count($like) > 0 || count($where) > 0){
            $iTotalRecords = $this->mdl_finance->countJoin('view_cs_reservation', $joins, $where, $like, 'view_cs_reservation.status', $whereIn, $whereString);
        }else{
            $iTotalRecords = 0;
        }

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'view_cs_reservation.reservation_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ms_company.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.room ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'view_cs_reservation.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        if(count($like) > 0 || count($where) > 0){
            $qry = $this->mdl_finance->getJoin('view_cs_reservation.*, ms_company.company_name','view_cs_reservation', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, 'view_cs_reservation.status', $whereIn, $whereString);

            //$records["debug2"] = $this->db->last_query();

            $i = $iDisplayStart + 1;
            foreach($qry->result() as $row){
                $btn_action = '';
                $btn_action .= '<li> <a href="' . base_url('frontdesk/management/guest_form/' . $row->reservation_id) . '.tpd">Open</a> </li>';

                $res_caption = strtoupper(RES_TYPE::caption($row->reservation_type));
                if($row->reservation_type == RES_TYPE::CORPORATE){
                    $res_caption = '<span class="font-green-seagreen ">'. $res_caption . '</span>';
                }

                $records["data"][] = array(
                    $row->reservation_code,
                    $row->tenant_fullname,
                    $row->company_name,
                    $res_caption,
                    $row->room,
                    //$res_caption,
                    dmy_from_db($row->arrival_date),
                    dmy_from_db($row->departure_date),
                    ORDER_STATUS::get_status_name($row->status),
                    '<a href="' . base_url('frontdesk/management/guest_form/' . $row->reservation_id) .'" class="btn btn-xs blue-ebonyclay add_coa" target="_blank"><i class="fa fa-search"></i></a>'
                );

                $i++;
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    #endregion

}