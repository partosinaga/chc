<?php
/**
 * Created by PhpStorm.
 * User: Syaiful
 * Date: 03/12/2015
 * Time: 1:16 PM
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_issue extends CI_Controller {

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
        tpd_404();
    }

    public function issue_form($gi_id = 0){
        $this->load->model('inventory/mdl_issue');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        $data['gi_id'] = $gi_id;
        if($gi_id > 0){
            $qry = $this->mdl_issue->get_issue(false, array('in_gi.gi_id' => $gi_id));
            $data['row'] = $qry->row();

            $data['qry_detail'] = $this->mdl_issue->get_gi_detail(false, array('in_gi_detail.gi_id' => $gi_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('inventory/stock_issue/issue_form', $data);
        $this->load->view('layout/footer');
    }

    public function issue_manage($type = 0, $gi_id = 0){
        $data_header = $this->data_header;

        if($type == 0) { //LIST
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();
            $data['type'] = '0';

            $this->load->view('layout/header', $data_header);
            $this->load->view('inventory/stock_issue/issue_list.php', $data);
            $this->load->view('layout/footer');
        } else { //FORM
            $this->load->model('inventory/mdl_issue');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $data['gi_id'] = $gi_id;
            if($gi_id > 0){
                $qry = $this->mdl_issue->get_issue(false, array('in_gi.gi_id' => $gi_id));
                $data['row'] = $qry->row();

                $data['qry_detail'] = $this->mdl_issue->get_gi_detail(false, array('in_gi_detail.gi_id' => $gi_id));
            }

            $this->load->view('layout/header', $data_header);
            $this->load->view('inventory/stock_issue/issue_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function issue_history($type = 0, $gi_id = 0){
        $data_header = $this->data_header;

        if($type == 0) { //LIST
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();
            $data['type'] = '1';

            $this->load->view('layout/header', $data_header);
            $this->load->view('inventory/stock_issue/issue_list.php', $data);
            $this->load->view('layout/footer');
        } else { //FORM
            $this->load->model('inventory/mdl_issue');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $data['gi_id'] = $gi_id;
            if($gi_id > 0){
                $qry = $this->mdl_issue->get_issue(false, array('in_gi.gi_id' => $gi_id));
                $data['row'] = $qry->row();

                $data['qry_detail'] = $this->mdl_issue->get_gi_detail(false, array('in_gi_detail.gi_id' => $gi_id));
            }

            $this->load->view('layout/header', $data_header);
            $this->load->view('inventory/stock_issue/issue_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function ajax_issue_list($menu_id = 0, $type = 0){
        //$type : 0 => manage, 1 => History
        $this->load->model('inventory/mdl_issue');
        $like = array();
        $where = array();

        if($type == 0){
            $where['in_gi.status'] = STATUS_NEW;
            $menu = 'issue_manage';
        } else {
            $where['in_gi.status <>'] = STATUS_NEW;
            $menu = 'issue_history';
        }

        if(isset($_REQUEST['filter_gi_code'])){
            if($_REQUEST['filter_gi_code'] != ''){
                $like['in_gi.gi_code'] = $_REQUEST['filter_gi_code'];
            }
        }
        if(isset($_REQUEST['filter_gi_date_from'])){
            if($_REQUEST['filter_gi_date_from'] != ''){
                $where['in_gi.gi_date >='] = dmy_to_ymd($_REQUEST['filter_gi_date_from']);
            }
        }
        if(isset($_REQUEST['filter_gi_date_to'])){
            if($_REQUEST['filter_gi_date_to'] != ''){
                $where['in_gi.gi_date <='] = dmy_to_ymd($_REQUEST['filter_gi_date_to']);
            }
        }
        if(isset($_REQUEST['filter_request_code'])){
            if($_REQUEST['filter_request_code'] != ''){
                $like['in_request.request_code'] = $_REQUEST['filter_request_code'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['in_gi.remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_dept'])){
            if($_REQUEST['filter_dept'] != ''){
                $where['in_request.department_id'] = $_REQUEST['filter_dept'];
            }
        }

        $iTotalRecords = $this->mdl_issue->get_issue(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_gi.gi_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_gi.gi_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_gi.gi_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'in_request.request_code ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_issue->get_issue(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('inventory/stock_issue/' . $menu . '/1/' . $row->gi_id) . '.tpd">View</a> </li>';

            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_POSTED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_POSTED . '" data-id="' . $row->gi_id . '" data-code="' . $row->gi_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CANCEL . '" data-id="' . $row->gi_id . '" data-code="' . $row->gi_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            } else if($row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('inventory/stock_issue/pdf_issue/' . $row->gi_id . '.tpd') . '" target="_blank" class="" >Print</a> </li>';
            }

            $records["data"][] = array(
                $i . '.',
                $row->gi_code,
                ymd_to_dmy($row->gi_date),
                $row->request_code,
                $row->department_name,
                $row->remarks,
                get_status_name($row->status),
                '<div class="btn-group">
					<button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
						Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
						' . $btn_action . '
					</ul>
				</div>'
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_request(){
        $this->load->view('inventory/stock_issue/ajax_request_list');
    }

    public function ajax_request_list($request_id = 0){
        $this->load->model('inventory/mdl_request');

        $like = array();
        $where = array();

        $where['in_request.status'] = STATUS_APPROVE;

        if(isset($_REQUEST['filter_request_code'])){
            if($_REQUEST['filter_request_code'] != ''){
                $like['in_request.request_code'] = $_REQUEST['filter_request_code'];
            }
        }
        if(isset($_REQUEST['filter_request_date_from'])){
            if($_REQUEST['filter_request_date_from'] != ''){
                $where['in_request.request_date >='] = dmy_to_ymd($_REQUEST['filter_request_date_from']);
            }
        }
        if(isset($_REQUEST['filter_request_date_to'])){
            if($_REQUEST['filter_request_date_to'] != ''){
                $where['in_request.request_date <='] = dmy_to_ymd($_REQUEST['filter_request_date_to']);
            }
        }
        if(isset($_REQUEST['filter_department_id'])){
            if($_REQUEST['filter_department_id'] != ''){
                $where['in_request.department_id'] = $_REQUEST['filter_department_id'];
            }
        }
        if(isset($_REQUEST['filter_request_by'])){
            if($_REQUEST['filter_request_by'] != ''){
                $like['ms_user.user_fullname'] = $_REQUEST['filter_request_by'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['in_request.remarks'] = $_REQUEST['filter_remarks'];
            }
        }

        $iTotalRecords = $this->mdl_request->get_request(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_request.request_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_request.request_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_request.request_date ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_request->get_request(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn = '<button class="btn green-meadow btn-xs btn-select-request" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '" ><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';
            if($request_id == $row->request_id){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }
            else if($row->status_gi == '1'){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-warning"></i>&nbsp;&nbsp;REQ in process</button>';
            }
            $records["data"][] = array(
                $i . '.',
                $row->request_code,
                ymd_to_dmy($row->request_date),
                $row->department_name,
                $row->user_fullname,
                $row->remarks,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_request_detail($type = 0){
        $data['type'] = $type;
        $this->load->view('inventory/stock_issue/ajax_modal_request_detail', $data);
    }

    public function ajax_request_detail_list($request_id = 0, $request_detail_id_exist = '-'){
        $this->load->model('inventory/mdl_request');

        $like = array();
        $where = array();

        $where['in_request_detail.request_id'] = $request_id;

        $iTotalRecords = $this->mdl_request->get_request_detail(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_request_detail.request_detail_id asc';

        $qry = $this->mdl_request->get_request_detail(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $request_detail_id_exist = trim($request_detail_id_exist);
        $isexist = false;
        if($request_detail_id_exist != '-'){
            $isexist = true;
            $arr_id = explode('_', $request_detail_id_exist);
        }

        $records["debug"] = $request_detail_id_exist;

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';

            if ($row->item_qty_remain <= 0) {
                $attr = 'selected="selected" disabled="disabled"';
            } else {
                if ($isexist) {
                    foreach ($arr_id as $key => $val) {
                        if ($val == $row->request_detail_id) {
                            $attr = 'selected="selected" disabled="disabled"';
                        }
                    }
                }
            }

            $records["data"][] = array(
                '<input type="checkbox" name="checkbox_request_detail" value="' . $row->request_detail_id . '" data-other-1="' . $row->item_id . '" data-other-2="' . $row->item_code . '" data-other-3="' . $row->item_desc . '" data-other-4="' . $row->uom_code . '" data-other-5="' . $row->item_qty . '" data-other-6="' . $row->item_qty_remain . '" data-other-7="' . $row->unit_cost . '" ' . $attr . '" />',
                $row->item_code,
                $row->item_desc,
                $row->uom_code,
                $row->item_qty,
                $row->item_qty_remain
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_stock_issue_submit(){
        $result = array();
        $result['success'] = '1';

        if(isset($_POST)){
            $this->db->trans_begin();

            $gi_id = $_POST['gi_id'];

            //header
            $data['gi_date'] = dmy_to_ymd(trim($_POST['gi_date']));
            $data['request_id'] = $_POST['request_id'];
            $data['remarks'] = trim($_POST['remarks']);

            //request
            $qry_req = $this->db->get_where('in_request', array('request_id' => $data['request_id']));
            $row_req = $qry_req->row();
            $data['wo_id'] = $row_req->wo_id;
            $data['department_id'] = $row_req->department_id;
            $data['facility_id'] = $row_req->facility_id;
            $data['is_pos'] = $row_req->is_pos;

            if($gi_id > 0){
                $qry = $this->db->get_where('in_gi', array('gi_id' => $gi_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['gi_date']);
                $arr_date_old = explode('-', $row->gi_date);

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['gi_code'] = $this->mdl_general->generate_code(Feature::FEATURE_STOCK_ISSUE, $data['gi_date']);

                    if($data['gi_code'] == ''){
                        $result['success'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }

                if($result['success'] == '1'){
                    $data['user_modified'] = my_sess('user_id');
                    $data['date_modified'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('in_gi', array('gi_id' => $gi_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully update Stock Issue.');
                }
            } else {
                $data['gi_code'] = $this->mdl_general->generate_code(Feature::FEATURE_STOCK_ISSUE, $data['gi_date']);

                if($data['gi_code'] != ''){
                    $data['user_created'] = my_sess('user_id');
                    $data['date_created'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_NEW;

                    $this->db->insert('in_gi', $data);
                    $gi_id = $this->db->insert_id();

                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $gi_id;
                    $data_log['feature_id'] = Feature::FEATURE_STOCK_ISSUE;
                    $data_log['log_subject'] = 'Create Stock Issue (' . $data['gi_code'] . ')';
                    $data_log['action_type'] = STATUS_NEW;
                    $this->db->insert('app_log', $data_log);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully add Stock Issue.');
                } else {
                    $result['success'] = '0';
                    $result['message'] = 'Failed generating code.';
                }
            }

            //detail
            if (isset($_POST['gi_detail_id'])) {
                foreach ($_POST['gi_detail_id'] as $key => $val) {
                    $data_detail = array();

                    $status = $_POST['status'][$key];

                    if($val > 0){
                        $qry_gi_det = $this->db->get_where('in_gi_detail', array('gi_detail_id' => $val));
                        $row_gi_det = $qry_gi_det->row();
                    }
                    $qry_req_det = $this->db->get_where('in_request_detail', array('request_detail_id' => $_POST['request_detail_id'][$key]));
                    $row_req_det = $qry_req_det->row();

                    if($status == STATUS_NEW) {
                        $data_detail['gi_id'] = $gi_id;
                        $data_detail['item_id'] = $_POST['item_id'][$key];
                        $data_detail['item_qty'] = $_POST['item_qty'][$key];
                        $data_detail['item_qty_remain'] = $_POST['item_qty'][$key];
                        $data_detail['request_detail_id'] = $_POST['request_detail_id'][$key];
                        $data_detail['unit_cost'] = $_POST['unit_cost'][$key];

                        if ($val > 0) {
                            $this->mdl_general->update('in_gi_detail', array('gi_detail_id' => $val), $data_detail);

                            //update remaining request
                            $this->mdl_general->update('in_request_detail', array('request_detail_id' => $_POST['request_detail_id'][$key]), array('item_qty_remain' => ($row_req_det->item_qty_remain + $row_gi_det->item_qty - $_POST['item_qty'][$key])));
                        } else {
                            $this->db->insert('in_gi_detail', $data_detail);

                            //update remaining request
                            $this->mdl_general->update('in_request_detail', array('request_detail_id' => $_POST['request_detail_id'][$key]), array('item_qty_remain' => ($row_req_det->item_qty_remain - $data_detail['item_qty'])));
                        }
                    } else {
                        if ($val > 0) {
                            $this->db->delete('in_gi_detail', array('gi_detail_id' => $val));

                            //update remaining request
                            $this->mdl_general->update('in_request_detail', array('request_detail_id' => $_POST['request_detail_id'][$key]), array('item_qty_remain' => ($row_req_det->item_qty_remain + $_POST['item_qty'][$key])));
                        }
                    }
                }
            }

            if($result['success'] == '1') {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('inventory/stock_issue/issue_manage.tpd');
                    }
                    else{
                        $result['link'] = base_url('inventory/stock_issue/issue_manage/1/' . $gi_id . '.tpd');
                    }
                }
            }

        } else {
            $result['success'] = '0';
            $result['message'] = 'No Post.';
        }

        echo json_encode($result);
    }

    public function ajax_action_issue(){
        $result = array();

        $this->load->model('inventory/mdl_issue');
        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = array();

        $gi_id = $_POST['gi_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $gi_id;
        $data_log['feature_id'] = Feature::FEATURE_STOCK_ISSUE;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($gi_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('in_gi', array('gi_id' => $gi_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_POSTED) {
                    if ($row->status == STATUS_POSTED) {
                        $result['valid'] = '0';
                        $result['message'] = 'Stock Issue already posted.';
                    } else {
                        if ($row->gi_date != date('Y-m-d')) {
                            $result['valid'] = '0';
                            $result['message'] = 'Issue date must be same with posting date.';
                        } else {
                            $posting = $this->posting_issue($gi_id);

                            if ($posting['error'] == '0') {
                                //check status request
                                $req_detail = $this->mdl_general->count('in_request_detail', array('request_id' => $row->request_id, 'item_qty_remain > ' => 0));
                                if ($req_detail == 0) {
                                    $this->mdl_general->update('in_request', array('request_id' => $row->request_id), array('status' => STATUS_CLOSED));
                                }

                                $data['user_posted'] = my_sess('user_id');
                                $data['date_posted'] = date('Y-m-d H:i:s');
                                $this->mdl_general->update('in_gi', array('gi_id' => $gi_id), $data);

                                $data_log['log_subject'] = 'Posting Stock Issue (' . $row->gi_code . ')';
                                $data_log['action_type'] = STATUS_POSTED;
                                $this->db->insert('app_log', $data_log);

                                $result['message'] = 'Successfully posting stock issue.';
                            } else {
                                $result['valid'] = '0';
                                $result['message'] = $posting['message'];
                            }
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Stock Issue already canceled.';
                    } else {
                        $this->mdl_general->update('in_gi', array('gi_id' => $gi_id), $data);

                        //update remaining request
                        $qryDetails = $this->mdl_issue->get_gi_detail(false, array('in_gi_detail.gi_id' => $gi_id));
                        if ($qryDetails->num_rows() > 0) {
                            foreach ($qryDetails->result() as $det) {
                                $qry_req_det = $this->db->get_where('in_request_detail', array('request_detail_id' => $det->request_detail_id));
                                $row_req_det = $qry_req_det->row();

                                $this->mdl_general->update('in_request_detail', array('request_detail_id' => $det->request_detail_id), array('item_qty_remain' => ($det->item_qty + $row_req_det->item_qty_remain)));
                            }
                        }

                        $data_log['log_subject'] = 'Cancel Stock Issue (' . $row->gi_code . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel stock issue.';
                    }
                }
            }
        }

        if($is_redirect){
            $this->session->set_flashdata('flash_message_class', ($result['valid'] == '1' ? 'success' : 'error'));
            $this->session->set_flashdata('flash_message', $result['message']);
        }

        echo json_encode($result);
    }

    private function posting_issue($gi_id = 0){
        $this->load->model('inventory/mdl_issue');
        $this->load->model('inventory/mdl_grn');
        $this->load->model('finance/mdl_finance');

        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = array();

        if($gi_id > 0) {
            $qry_gi = $this->mdl_issue->get_issue(false, array('in_gi.gi_id' => $gi_id));
            if ($qry_gi->num_rows() > 0) {
                $row_gi = $qry_gi->row();

                $detail = array();
                $stock = array();
                $pos = array();
                $totalDebit = 0;
                $totalCredit = 0;
                $is_wo = false;

                $qryDetails = $this->mdl_issue->get_gi_detail(false, array('in_gi_detail.gi_id' => $gi_id));
                if ($qryDetails->num_rows() > 0) {
                    foreach ($qryDetails->result() as $det) {
                        $new_stock = array();

                        $new_stock = $this->mdl_grn->get_new_stock($det->item_id, ($det->item_qty * -1), $det->unit_cost);
                        unset($new_stock['valid']);
                        $new_stock['doc_id'] = $det->gi_detail_id;
                        $new_stock['doc_type'] = Feature::FEATURE_STOCK_ISSUE;
                        $new_stock['created_date'] = date('Y-m-d H:i:s');
                        $new_stock['status'] = STATUS_NEW;

                        array_push($stock, $new_stock);

                        if($det->item_qty > $det->ms_on_hand_qty){
                            $result['error'] = '1';
                            $result['message'] = $row_gi->gi_code . ' - ' . $det->item_code . ' - ' . $det->item_desc . ' - Qty is bigger than stock qty.';
                        } else {
                            if ($row_gi->wo_id > 0) {
                                $is_wo = true;

                                if ($det->account_coa_id > 0) {
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $det->account_coa_id;
                                    $rowdet['dept_id'] = $row_gi->department_id;
                                    $rowdet['journal_note'] = ($row_gi->remarks != '' ? $row_gi->remarks : $row_gi->gi_code);
                                    $rowdet['journal_debit'] = 0;
                                    $rowdet['journal_credit'] = $det->item_qty * $det->unit_cost;
                                    $rowdet['reference_id'] = $det->gi_detail_id;
                                    $rowdet['transtype_id'] = 0;

                                    array_push($detail, $rowdet);

                                    $result['debug'][] = $rowdet;

                                    $totalCredit += $rowdet['journal_credit'];

                                } else {
                                    $result['error'] = '1';
                                    $result['message'] = $row_gi->gi_code . ' - ' . $det->item_code . ' - ' . $det->item_desc . ' - Account COA is empty.';
                                    break;
                                }
                            } else {
                                if ($row_gi->is_pos > 0) {
                                    //POSTING TO POS
                                    $qry_key_pos = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::POS_SUPPLIES));
                                    if ($qry_key_pos->num_rows() > 0) {
                                        $row_key_pos = $qry_key_pos->row();

                                        if ($row_key_pos->coa_id > 0) {
                                            $rowdet = array();
                                            $rowdet['coa_id'] = $row_key_pos->coa_id;
                                            $rowdet['dept_id'] = $row_gi->department_id;
                                            $rowdet['journal_note'] = $det->item_code . ' - ' . $det->item_desc;
                                            $rowdet['journal_debit'] = $det->item_qty * $det->unit_cost;
                                            $rowdet['journal_credit'] = 0;
                                            $rowdet['reference_id'] = $det->gi_detail_id;
                                            $rowdet['transtype_id'] = 0;

                                            array_push($detail, $rowdet);

                                            $totalDebit += $rowdet['journal_debit'];

                                            if ($det->account_coa_id > 0) {
                                                //POSTING SUPPLIES
                                                $rowdet = array();
                                                $rowdet['coa_id'] = $det->account_coa_id;
                                                $rowdet['dept_id'] = $row_gi->department_id;
                                                $rowdet['journal_note'] = $det->item_code . ' - ' . $det->item_desc;
                                                $rowdet['journal_debit'] = 0;
                                                $rowdet['journal_credit'] = $det->item_qty * $det->unit_cost;
                                                $rowdet['reference_id'] = $det->gi_detail_id;
                                                $rowdet['transtype_id'] = 0;

                                                array_push($detail, $rowdet);

                                                $result['debug'][] = $rowdet;

                                                $totalCredit += $rowdet['journal_credit'];

                                                //POS
                                                $new_pos = array();
                                                $new_pos['masteritem_id'] = $det->item_id;
                                                $new_pos['is_service_item'] = 0;
                                                $new_pos['itemstock_uom'] = $det->uom_id;
                                                $new_pos['itemstock_uom_distribution'] = $det->uom_id;
                                                $new_pos['itemstock_current_qty'] = $det->item_qty;
                                                $new_pos['itemstock_min'] = 1;
                                                $new_pos['itemstock_max'] = 1;
                                                $new_pos['price_lock'] = 1;
                                                $new_pos['enable_ar_bill'] = 0;
                                                $new_pos['unit_price'] = 0;
                                                $new_pos['unit_discount'] = 0;
                                                $new_pos['base_avg_price'] = $det->unit_cost;
                                                $new_pos['taxtype_id'] = 0;
                                                $new_pos['coa_code'] = 0;
                                                $new_pos['itemstock_factor'] = 1;
                                                $new_pos['status'] = STATUS_NEW;
                                                $new_pos['created_by'] = my_sess('user_id');
                                                $new_pos['created_date'] = date('Y-m-d H:i:s');

                                                array_push($pos, $new_pos);
                                            } else {
                                                $result['error'] = '1';
                                                $result['message'] = $row_gi->gi_code . ' - ' . $det->item_code . ' - ' . $det->item_desc . ' - Account COA is empty.';
                                                break;
                                            }
                                        } else {
                                            $result['error'] = '1';
                                            $result['message'] = 'POS Supplies Account COA is empty.';
                                            break;
                                        }
                                    } else {
                                        $result['error'] = '1';
                                        $result['message'] = 'POS Supplies Account COA not found.';
                                        break;
                                    }
                                } else {
                                    if ($det->exp_coa_id > 0) {
                                        //POSTING EXPENSE
                                        $rowdet = array();
                                        $rowdet['coa_id'] = $det->exp_coa_id;
                                        $rowdet['dept_id'] = $row_gi->department_id;
                                        $rowdet['journal_note'] = $det->item_code . ' - ' . $det->item_desc;
                                        $rowdet['journal_debit'] = $det->item_qty * $det->unit_cost;
                                        $rowdet['journal_credit'] = 0;
                                        $rowdet['reference_id'] = $det->gi_detail_id;
                                        $rowdet['transtype_id'] = 0;

                                        array_push($detail, $rowdet);

                                        $result['debug'][] = $rowdet;

                                        $totalDebit += $rowdet['journal_debit'];

                                        if ($det->account_coa_id > 0) {
                                            //POSTING SUPPLIES
                                            $rowdet = array();
                                            $rowdet['coa_id'] = $det->account_coa_id;
                                            $rowdet['dept_id'] = $row_gi->department_id;
                                            $rowdet['journal_note'] = $det->item_code . ' - ' . $det->item_desc;
                                            $rowdet['journal_debit'] = 0;
                                            $rowdet['journal_credit'] = $det->item_qty * $det->unit_cost;
                                            $rowdet['reference_id'] = $det->gi_detail_id;
                                            $rowdet['transtype_id'] = 0;

                                            array_push($detail, $rowdet);

                                            $result['debug'][] = $rowdet;

                                            $totalCredit += $rowdet['journal_credit'];
                                        } else {
                                            $result['error'] = '1';
                                            $result['message'] = $row_gi->gi_code . ' - ' . $det->item_code . ' - ' . $det->item_desc . ' - Account COA is empty.';
                                            break;
                                        }
                                    } else {
                                        $result['error'] = '1';
                                        $result['message'] = $row_gi->gi_code . ' - ' . $det->item_code . ' - ' . $det->item_desc . ' - Expense COA is empty.';
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($result['error'] == '0') {
                    if($is_wo) {
                        $qry_key = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_GRN_WIP));
                        if ($qry_key->num_rows() > 0) {
                            $row_key = $qry_key->row();

                            if ($row_key->coa_id > 0) {
                                $rowdet = array();
                                $rowdet['coa_id'] = $row_key->coa_id;
                                $rowdet['dept_id'] = $row_gi->department_id;
                                $rowdet['journal_note'] = ($row_gi->remarks != '' ? $row_gi->remarks : $row_gi->gi_code);
                                $rowdet['journal_debit'] = $totalDebit;
                                $rowdet['journal_credit'] = 0;
                                $rowdet['reference_id'] = $row_gi->gi_id;
                                $rowdet['transtype_id'] = $row_key->transtype_id;

                                $totalCredit += $rowdet['journal_credit'];

                                array_push($detail, $rowdet);

                                $result['debug'][] = $rowdet;
                            } else {
                                $result['error'] = '1';
                                $result['message'] = 'Spec Stock Issue WIP is empty.';
                            }
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Spec Stock Issue WIP not found.';
                        }
                    }
                }

                if ($result['error'] == '0') {
                    $valid_stock = $this->mdl_grn->postStock($stock);
                    $valid_pos = true;
                    if ($row_gi->is_pos > 0) {
                        $valid_pos = $this->post_pos($pos);
                    }

                    if($valid_stock && $valid_pos) {
                        if ($totalDebit == $totalCredit) {
                            $header = array();
                            $header['journal_no'] = $row_gi->gi_code;
                            $header['journal_date'] = $row_gi->gi_date;
                            $header['journal_remarks'] = ($row_gi->remarks != '' ? $row_gi->remarks : $row_gi->gi_code);
                            $header['modul'] = GLMOD::GL_MOD_AP;
                            $header['journal_amount'] = $totalDebit;
                            $header['reference'] = strval($row_gi->gi_id);

                            $result['debug'][] = $header;

                            $valid = $this->mdl_finance->postJournal($header, $detail);

                            if ($valid == false) {
                                $result['error'] = '1';
                                $result['message'] = 'Failed insert journal.';
                            }
                        }
                    } else {
                        $result['error'] = '1';
                        $result['message'] = 'Failed insert to stock.';
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'Stock Issue not found.';
            }
        } else {
            $result['error'] = '1';
            $result['message'] = 'Stock Issue is empty.';
        }

        return $result;
    }

    private function post_pos($detail = array()){
        $valid = true;

        if ($this->db->trans_status() === FALSE){
            $valid = false;
        } else {
            if(isset($detail)){
                for ($i = 0; $i < count($detail); $i++)
                {
                    $row = $detail[$i];

                    $qry_exist = $this->db->get_where('pos_item_stock', array('masteritem_id' => $row['masteritem_id'], 'is_service_item' => 0, 'status' => STATUS_NEW));
                    if ($qry_exist->num_rows() > 0) {
                        $row_exist = $qry_exist->row();

                        $bf_stock = $row_exist->itemstock_current_qty * $row_exist->base_avg_price;
                        $af_stock = $row['base_avg_price'] * $row['itemstock_current_qty'];
                        $tot_stock = $row_exist->itemstock_current_qty + $row['itemstock_current_qty'];

                        $update['base_avg_price'] = ($bf_stock + $af_stock) / $tot_stock;
                        $update['itemstock_current_qty'] = $tot_stock;
                        $update['modified_by'] = my_sess('user_id');
                        $update['modified_date'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('pos_item_stock', array('itemstock_id' => $row_exist->itemstock_id), $update);
                    } else {
                        $this->db->insert('pos_item_stock', $row);
                        $newDetailId = $this->db->insert_id();

                        if ($newDetailId <= 0) {
                            $valid = false;
                            break;
                        }
                    }
                }
            } else {
                $valid = false;
            }
        }

        return $valid;
    }

    public function pdf_issue($gi_id = 0) {
        if($gi_id > 0){
            $this->load->model('inventory/mdl_issue');

            $qry = $this->mdl_issue->get_issue(false, array('in_gi.gi_id' => $gi_id));
            if ($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_det'] =  $this->mdl_issue->get_gi_detail(false, array('in_gi_detail.gi_id' => $gi_id));

                $this->load->view('inventory/stock_issue/pdf_issue.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->gi_code . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }


}

/* End of file stock_issue.php */
/* Location: ./application/controllers/inventory/stock_issue.php */