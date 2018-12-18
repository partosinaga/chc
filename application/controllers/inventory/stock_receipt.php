<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_receipt extends CI_Controller {

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

    public function receipt_form($sr_id = 0){
        $this->load->model('inventory/mdl_receipt');

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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        $data['sr_id'] = $sr_id;
        if($sr_id > 0){
            $qry = $this->mdl_receipt->get_sr(false, array('in_sr.sr_id' => $sr_id));
            $data['row'] = $qry->row();

            $data['qry_detail'] = $this->mdl_receipt->get_sr_detail(false, array('in_sr_detail.sr_id' => $sr_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('inventory/stock_receipt/receipt_form', $data);
        $this->load->view('layout/footer');
    }

    public function receipt_manage($type = 0, $sr_id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        if($type == 0) { //LIST
            $data = array();
            $data['type'] = '0';

            $this->load->view('layout/header', $data_header);
            $this->load->view('inventory/stock_receipt/receipt_list.php', $data);
            $this->load->view('layout/footer');
        } else { //FORM
            $this->receipt_form($sr_id);
            /*$this->load->model('inventory/mdl_receipt');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

            $data = array();

            $data['sr_id'] = $sr_id;
            if($sr_id > 0){
                $qry = $this->mdl_receipt->get_sr(false, array('in_sr.sr_id' => $sr_id));
                $data['row'] = $qry->row();

                $data['qry_detail'] = $this->mdl_receipt->get_sr_detail(false, array('in_sr_detail.sr_id' => $sr_id));
            }

            $this->load->view('layout/header', $data_header);
            $this->load->view('inventory/stock_receipt/receipt_form', $data);
            $this->load->view('layout/footer');*/
        }
    }

    public function receipt_history($type = 0, $sr_id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        if($type == 0) { //LIST
            $data = array();
            $data['type'] = '1';

            $this->load->view('layout/header', $data_header);
            $this->load->view('inventory/stock_receipt/receipt_list.php', $data);
            $this->load->view('layout/footer');
        } else { //FORM
            $this->receipt_form($sr_id);
            /*$this->load->model('inventory/mdl_receipt');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

            $data = array();

            $data['sr_id'] = $sr_id;
            if($sr_id > 0){
                $qry = $this->mdl_receipt->get_sr(false, array('in_sr.sr_id' => $sr_id));
                $data['row'] = $qry->row();

                $data['qry_detail'] = $this->mdl_receipt->get_sr_detail(false, array('in_sr_detail.sr_id' => $sr_id));
            }

            $this->load->view('layout/header', $data_header);
            $this->load->view('inventory/stock_receipt/receipt_form', $data);
            $this->load->view('layout/footer');*/
        }
    }

    public function ajax_receipt_list($menu_id = 0, $type = 0){
        //$type : 0 => manage, 1 => History
        $this->load->model('inventory/mdl_receipt');
        $like = array();
        $where = array();

        if($type == 0){
            $where['in_sr.status'] = STATUS_NEW;
            $menu = 'receipt_manage';
        } else {
            $where['in_sr.status <>'] = STATUS_NEW;
            $menu = 'receipt_history';
        }

        if(isset($_REQUEST['filter_sr_code'])){
            if($_REQUEST['filter_sr_code'] != ''){
                $like['in_sr.sr_code'] = $_REQUEST['filter_sr_code'];
            }
        }
        if(isset($_REQUEST['filter_sr_date_from'])){
            if($_REQUEST['filter_sr_date_from'] != ''){
                $where['in_sr.sr_date >='] = dmy_to_ymd($_REQUEST['filter_sr_date_from']);
            }
        }
        if(isset($_REQUEST['filter_sr_date_to'])){
            if($_REQUEST['filter_sr_date_to'] != ''){
                $where['in_sr.sr_date <='] = dmy_to_ymd($_REQUEST['filter_sr_date_to']);
            }
        }
        if(isset($_REQUEST['filter_gi_code'])){
            if($_REQUEST['filter_gi_code'] != ''){
                $like['in_gi.gi_code'] = $_REQUEST['filter_gi_code'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['in_sr.remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_dept'])){
            if($_REQUEST['filter_dept'] != ''){
                $where['in_sr.department_id'] = $_REQUEST['filter_dept'];
            }
        }

        $iTotalRecords = $this->mdl_receipt->get_sr(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_sr.sr_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_sr.sr_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_sr.sr_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'in_gi.gi_code ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_receipt->get_sr(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('inventory/stock_receipt/' . $menu . '/1/' . $row->sr_id) . '.tpd">View</a> </li>';

            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_POSTED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_POSTED . '" data-id="' . $row->sr_id . '" data-code="' . $row->sr_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CANCEL . '" data-id="' . $row->sr_id . '" data-code="' . $row->sr_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            } else if($row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('inventory/stock_receipt/pdf_receipt/' . $row->sr_id . '.tpd') . '" target="_blank" class="" >Print</a> </li>';
            }

            $records["data"][] = array(
                $i . '.',
                $row->sr_code,
                ymd_to_dmy($row->sr_date),
                $row->gi_code,
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

    public function ajax_modal_gi(){
        $this->load->view('inventory/stock_receipt/ajax_gi_list');
    }

    public function ajax_gi_list($gi_id = 0){
        $this->load->model('inventory/mdl_issue');

        $like = array();
        $where = array();

        $where['in_gi.status'] = STATUS_POSTED;

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
                $like['in_gi.remarks'] = $_REQUEST['filter_remarks'];
            }
        }

        $iTotalRecords = $this->mdl_issue->get_issue(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_gi.gi_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_gi.gi_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_gi.gi_date ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_issue->get_issue(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn = '<button class="btn green-meadow btn-xs btn-select-gi" data-id="' . $row->gi_id . '" data-code="' . $row->gi_code . '" ><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';
            if($gi_id == $row->gi_id){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }
            $records["data"][] = array(
                $i . '.',
                $row->gi_code,
                ymd_to_dmy($row->gi_date),
                $row->department_name,
                $row->user_created_name,
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

    public function ajax_modal_gi_detail($type = 0){
        $data['type'] = $type;
        $this->load->view('inventory/stock_receipt/ajax_modal_gi_detail', $data);
    }

    public function ajax_gi_detail_list($gi_id = 0, $gi_detail_id_exist = '-'){
        $this->load->model('inventory/mdl_issue');

        $like = array();
        $where = array();

        $where['in_gi_detail.gi_id'] = $gi_id;

        $iTotalRecords = $this->mdl_issue->get_gi_detail(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_gi_detail.gi_detail_id asc';

        $qry = $this->mdl_issue->get_gi_detail(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $gi_detail_id_exist = trim($gi_detail_id_exist);
        $isexist = false;
        if($gi_detail_id_exist != '-'){
            $isexist = true;
            $arr_id = explode('_', $gi_detail_id_exist);
        }

        //$records["debug"] = $gi_detail_id_exist;

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';

            if ($row->item_qty_remain <= 0) {
                $attr = 'selected="selected" disabled="disabled"';
            } else {
                if ($isexist) {
                    foreach ($arr_id as $key => $val) {
                        if ($val == $row->gi_detail_id) {
                            $attr = 'selected="selected" disabled="disabled"';
                        }
                    }
                }
            }

            $records["data"][] = array(
                '<input type="checkbox" name="checkbox_request_detail" value="' . $row->gi_detail_id . '" data-other-1="' . $row->item_id . '" data-other-2="' . $row->item_code . '" data-other-3="' . $row->item_desc . '" data-other-4="' . $row->uom_code . '" data-other-5="' . $row->item_qty . '" data-other-6="' . $row->item_qty_remain . '" data-other-7="' . $row->unit_cost . '" ' . $attr . '" />',
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

    public function ajax_stock_receipt_submit(){
        $result = array();
        $result['success'] = '1';
        $result['debug'] = array();

        if(isset($_POST)){
            $this->db->trans_begin();

            $sr_id = $_POST['sr_id'];

            //header
            $data['sr_date'] = dmy_to_ymd(trim($_POST['sr_date']));
            $data['gi_id'] = $_POST['gi_id'];
            $data['remarks'] = trim($_POST['remarks']);

            $qry_gi = $this->db->get_where('in_gi', array('gi_id' => $data['gi_id']));
            $row_gi = $qry_gi->row();
            $data['wo_id'] = $row_gi->wo_id;
            $data['department_id'] = $row_gi->department_id;
            $data['facility_id'] = $row_gi->facility_id;
            $data['is_pos'] = $row_gi->is_pos;

            if($sr_id > 0){
                $qry = $this->db->get_where('in_sr', array('sr_id' => $sr_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['sr_date']);
                $arr_date_old = explode('-', $row->sr_date);

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['sr_code'] = $this->mdl_general->generate_code(Feature::FEATURE_STOCK_RECEIPT, $data['sr_date']);

                    if($data['sr_code'] == ''){
                        $result['success'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }

                if($result['success'] == '1'){
                    $data['user_modified'] = my_sess('user_id');
                    $data['date_modified'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('in_sr', array('sr_id' => $sr_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully update Stock Receipt.');
                }
            } else {
                $data['sr_code'] = $this->mdl_general->generate_code(Feature::FEATURE_STOCK_RECEIPT, $data['sr_date']);

                if($data['sr_code'] != ''){
                    $data['user_created'] = my_sess('user_id');
                    $data['date_created'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_NEW;

                    $this->db->insert('in_sr', $data);
                    $sr_id = $this->db->insert_id();

                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $sr_id;
                    $data_log['feature_id'] = Feature::FEATURE_STOCK_RECEIPT;
                    $data_log['log_subject'] = 'Create Stock Issue (' . $data['sr_code'] . ')';
                    $data_log['action_type'] = STATUS_NEW;
                    $this->db->insert('app_log', $data_log);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully add Stock Receipt.');
                } else {
                    $result['success'] = '0';
                    $result['message'] = 'Failed generating code.';
                }
            }

            //detail
            if (isset($_POST['sr_detail_id'])) {
                foreach ($_POST['sr_detail_id'] as $key => $val) {
                    $data_detail = array();

                    $status = $_POST['status'][$key];

                    if($val > 0){
                        $qry_sr_det = $this->db->get_where('in_sr_detail', array('sr_detail_id' => $val));
                        $row_sr_det = $qry_sr_det->row();
                    }
                    $qry_gi_det = $this->db->get_where('in_gi_detail', array('gi_detail_id' => $_POST['gi_detail_id'][$key]));
                    $row_gi_det = $qry_gi_det->row();

                    if($status == STATUS_NEW) {
                        $data_detail['sr_id'] = $sr_id;
                        $data_detail['item_id'] = $_POST['item_id'][$key];
                        $data_detail['item_qty'] = $_POST['item_qty'][$key];
                        $data_detail['gi_detail_id'] = $_POST['gi_detail_id'][$key];
                        $data_detail['unit_cost'] = $_POST['unit_cost'][$key];

                        if ($val > 0) {
                            $this->mdl_general->update('in_sr_detail', array('sr_detail_id' => $val), $data_detail);

                            //update remaining gi
                            $this->mdl_general->update('in_gi_detail', array('gi_detail_id' => $_POST['gi_detail_id'][$key]), array('item_qty_remain' => ($row_gi_det->item_qty_remain + $row_sr_det->item_qty - $_POST['item_qty'][$key])));
                        } else {
                            $this->db->insert('in_sr_detail', $data_detail);

                            //update remaining gi
                            $this->mdl_general->update('in_gi_detail', array('gi_detail_id' => $_POST['gi_detail_id'][$key]), array('item_qty_remain' => ($row_gi_det->item_qty_remain - $data_detail['item_qty'])));
                        }
                    } else {
                        if ($val > 0) {
                            $this->db->delete('in_sr_detail', array('sr_detail_id' => $val));

                            //update remaining gi
                            $this->mdl_general->update('in_gi_detail', array('gi_detail_id' => $_POST['gi_detail_id'][$key]), array('item_qty_remain' => ($row_gi_det->item_qty_remain + $_POST['item_qty'][$key])));
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
                        $result['link'] = base_url('inventory/stock_receipt/receipt_manage.tpd');
                    }
                    else{
                        $result['link'] = base_url('inventory/stock_receipt/receipt_manage/1/' . $sr_id . '.tpd');
                    }
                }
            }

        } else {
            $result['success'] = '0';
            $result['message'] = 'No Post.';
        }

        echo json_encode($result);
    }

    public function ajax_action_receipt(){
        $result = array();

        $this->load->model('inventory/mdl_receipt');
        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = array();

        $sr_id = $_POST['sr_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $sr_id;
        $data_log['feature_id'] = Feature::FEATURE_STOCK_RECEIPT;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($sr_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('in_sr', array('sr_id' => $sr_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_POSTED) {
                    if ($row->status == STATUS_POSTED) {
                        $result['valid'] = '0';
                        $result['message'] = 'Stock Receipt already posted.';
                    } else {
                        if ($row->sr_date != date('Y-m-d')) {
                            $result['valid'] = '0';
                            $result['message'] = 'Receipt date must be same with posting date.';
                        } else {
                            $posting = $this->posting_receipt($row);

                            if ($posting['error'] == '0') {
                                $data['user_posted'] = my_sess('user_id');
                                $data['date_posted'] = date('Y-m-d H:i:s');
                                $this->mdl_general->update('in_sr', array('sr_id' => $sr_id), $data);

                                $data_log['log_subject'] = 'Posting Stock Receipt (' . $row->sr_code . ')';
                                $data_log['action_type'] = STATUS_POSTED;
                                $this->db->insert('app_log', $data_log);

                                $result['message'] = 'Successfully posting stock receipt.';
                            } else {
                                $result['valid'] = '0';
                                $result['message'] = $posting['message'];
                            }
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Stock Receipt already canceled.';
                    } else {
                        $this->mdl_general->update('in_sr', array('sr_id' => $sr_id), $data);

                        //update remaining gi
                        $qryDetails = $this->mdl_receipt->get_sr_detail(false, array('in_sr_detail.sr_id' => $sr_id));
                        if ($qryDetails->num_rows() > 0) {
                            foreach ($qryDetails->result() as $det) {
                                $qry_gi_det = $this->db->get_where('in_gi_detail', array('gi_detail_id' => $det->gi_detail_id));
                                $row_gi_det = $qry_gi_det->row();

                                $this->mdl_general->update('in_gi_detail', array('gi_detail_id' => $det->gi_detail_id), array('item_qty_remain' => ($det->item_qty + $row_gi_det->item_qty_remain)));
                            }
                        }

                        $data_log['log_subject'] = 'Cancel Stock Receipt (' . $row->sr_code . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel stock receipt.';
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

    private function posting_receipt($row){
        $this->load->model('inventory/mdl_grn');
        $this->load->model('inventory/mdl_receipt');
        $this->load->model('finance/mdl_finance');

        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = array();

        if($row->sr_id > 0) {
            $detail = array();
            $stock = array();
            $pos = array();
            $totalDebit = 0;
            $totalCredit = 0;
            $is_wo = false;

            $qryDetails = $this->mdl_receipt->get_sr_detail(false, array('in_sr_detail.sr_id' => $row->sr_id));
            if ($qryDetails->num_rows() > 0) {
                foreach ($qryDetails->result() as $det) {
                    $new_stock = array();

                    $new_stock = $this->mdl_grn->get_new_stock($det->item_id, $det->item_qty, $det->unit_cost);
                    unset($new_stock['valid']);
                    $new_stock['doc_id'] = $det->sr_detail_id;
                    $new_stock['doc_type'] = Feature::FEATURE_STOCK_RECEIPT;
                    $new_stock['created_date'] = date('Y-m-d H:i:s');
                    $new_stock['status'] = STATUS_NEW;

                    array_push($stock, $new_stock);

                    if ($row->wo_id > 0) {
                        $is_wo = true;

                        if ($det->account_coa_id > 0) {
                            $rowdet = array();
                            $rowdet['coa_id'] = $det->account_coa_id;
                            $rowdet['dept_id'] = $row->department_id;
                            $rowdet['journal_note'] = ($row->remarks != '' ? $row->remarks : $row->sr_code);
                            $rowdet['journal_debit'] = $det->item_qty * $det->unit_cost;
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = $det->sr_detail_id;
                            $rowdet['transtype_id'] = 0;

                            array_push($detail, $rowdet);

                            $result['debug'][] = $rowdet;

                            $totalDebit += $rowdet['journal_debit'];
                            $totalCredit += $rowdet['journal_credit'];

                        } else {
                            $result['error'] = '1';
                        $result['message'] = $row->sr_code . ' - ' . $det->item_code . ' - ' . $det->item_desc . ' - Account COA is empty.';
                            break;
                        }
                    } else {
                        if ($row->is_pos > 0) {
                            //POSTING TO POS
                            $qry_key_pos = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::POS_EXPENSE));
                            if ($qry_key_pos->num_rows() > 0) {
                                $row_key_pos = $qry_key_pos->row();

                                if ($row_key_pos->coa_id > 0) {
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $row_key_pos->coa_id;
                                    $rowdet['dept_id'] = $row->department_id;
                                    $rowdet['journal_note'] = $det->item_code . ' - ' . $det->item_desc;
                                    $rowdet['journal_debit'] = 0;
                                    $rowdet['journal_credit'] = $det->item_qty * $det->unit_cost;
                                    $rowdet['reference_id'] = $det->sr_detail_id;
                                    $rowdet['transtype_id'] = 0;

                                    array_push($detail, $rowdet);

                                    $totalCredit += $rowdet['journal_credit'];

                                    if ($det->account_coa_id > 0) {
                                        //POSTING SUPPLIES
                                        $rowdet = array();
                                        $rowdet['coa_id'] = $det->account_coa_id;
                                        $rowdet['dept_id'] = $row->department_id;
                                        $rowdet['journal_note'] = $det->item_code . ' - ' . $det->item_desc;
                                        $rowdet['journal_debit'] = $det->item_qty * $det->unit_cost;
                                        $rowdet['journal_credit'] = 0;
                                        $rowdet['reference_id'] = $det->sr_detail_id;
                                        $rowdet['transtype_id'] = 0;

                                        array_push($detail, $rowdet);

                                        $result['debug'][] = $rowdet;

                                        $totalDebit += $rowdet['journal_debit'];

                                        //POS
                                        $new_pos = array();
                                        $new_pos['masteritem_id'] = $det->item_id;
                                        $new_pos['is_service_item'] = 0;
                                        $new_pos['itemstock_current_qty'] = $det->item_qty;

                                        array_push($pos, $new_pos);
                                    } else {
                                        $result['error'] = '1';
                                        $result['message'] = $row->sr_code . ' - ' . $det->item_code . ' - ' . $det->item_desc . ' - Account COA is empty.';
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
                                //POSTING AXPENSE
                                $rowdet = array();
                                $rowdet['coa_id'] = $det->exp_coa_id;
                                $rowdet['dept_id'] = $row->department_id;
                                $rowdet['journal_note'] = $det->item_code . ' - ' . $det->item_desc;
                                $rowdet['journal_debit'] = 0;
                                $rowdet['journal_credit'] = $det->item_qty * $det->unit_cost;
                                $rowdet['reference_id'] = $det->sr_detail_id;
                                $rowdet['transtype_id'] = 0;

                                array_push($detail, $rowdet);

                                $result['debug'][] = $rowdet;

                                $totalDebit += $rowdet['journal_debit'];
                                $totalCredit += $rowdet['journal_credit'];

                                if ($det->account_coa_id > 0) {
                                    //POSTING SUPPLIES
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $det->account_coa_id;
                                    $rowdet['dept_id'] = $row->department_id;
                                    $rowdet['journal_note'] = $det->item_code . ' - ' . $det->item_desc;
                                    $rowdet['journal_debit'] = $det->item_qty * $det->unit_cost;
                                    $rowdet['journal_credit'] = 0;
                                    $rowdet['reference_id'] = $det->sr_detail_id;
                                    $rowdet['transtype_id'] = 0;

                                    array_push($detail, $rowdet);

                                    $result['debug'][] = $rowdet;

                                    $totalDebit += $rowdet['journal_debit'];
                                    $totalCredit += $rowdet['journal_credit'];
                                } else {
                                    $result['error'] = '1';
                                $result['message'] = $row->sr_code . ' - ' . $det->item_code . ' - ' . $det->item_desc . ' - Account COA is empty.';
                                    break;
                                }
                            } else {
                                $result['error'] = '1';
                            $result['message'] = $row->sr_code . ' - ' . $det->item_code . ' - ' . $det->item_desc . ' - Expense COA is empty.';
                                break;
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
                            $rowdet['dept_id'] = $row->department_id;
                            $rowdet['journal_note'] = ($row->remarks != '' ? $row->remarks : $row->sr_code);
                            $rowdet['journal_debit'] = 0;
                            $rowdet['journal_credit'] = $totalDebit;
                            $rowdet['reference_id'] = $row->sr_id;
                            $rowdet['transtype_id'] = $row_key->transtype_id;

                            $totalDebit += $rowdet['journal_debit'];
                            $totalCredit += $rowdet['journal_credit'];

                            array_push($detail, $rowdet);

                            $result['debug'][] = $rowdet;
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Spec Receipt Issue WIP is empty.';
                        }
                    } else {
                        $result['error'] = '1';
                        $result['message'] = 'Spec Stock Receipt WIP not found.';
                    }
                }
            }

            if ($result['error'] == '0') {
                $valid_stock = $this->mdl_grn->postStock($stock);
                $valid_pos = true;
                if ($row->is_pos > 0) {
                    $valid_pos = $this->post_pos($pos);
                }

                if($valid_stock && $valid_pos) {
                    if ($totalDebit == $totalCredit) {
                        $header = array();
                        $header['journal_no'] = $row->sr_code;
                        $header['journal_date'] = $row->sr_date;
                        $header['journal_remarks'] = ($row->remarks != '' ? $row->remarks : $row->sr_code);
                        $header['modul'] = GLMOD::GL_MOD_AP;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = strval($row->sr_id);

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
            $result['message'] = 'Stock Receipt is empty.';
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

                        if ($row_exist->itemstock_current_qty - $row['itemstock_current_qty'] >= 0) {
                            $update['itemstock_current_qty'] = $row_exist->itemstock_current_qty - $row['itemstock_current_qty'];
                            $update['modified_by'] = my_sess('user_id');
                            $update['modified_date'] = date('Y-m-d H:i:s');

                            $this->mdl_general->update('pos_item_stock', array('itemstock_id' => $row_exist->itemstock_id), $update);
                        } else {
                            $valid = false;
                            break;
                        }
                    } else {
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

    public function pdf_receipt($sr_id = 0) {
        if($sr_id > 0){
            $this->load->model('inventory/mdl_receipt');

            $qry = $this->mdl_receipt->get_sr(false, array('in_sr.sr_id' => $sr_id));
            if ($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_det'] =  $this->mdl_receipt->get_sr_detail(false, array('in_sr_detail.sr_id' => $sr_id));

                $this->load->view('inventory/stock_receipt/pdf_receipt.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->sr_code . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

}

/* End of file stock_receipt.php */
/* Location: ./application/controllers/inventory/stock_receipt.php */