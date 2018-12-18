<?php
/**
 * Created by PhpStorm.
 * User: Syaiful
 * Date: 03/18/2015
 * Time: 3:11 PM
 */

ini_set('display_errors', 1);

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retur extends CI_Controller {

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

    public function return_manage($type = 0, $return_id = 0){
        if ($type == 0) {
            $data_header = $this->data_header;

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
            $this->load->view('inventory/return/return_list.php', $data);
            $this->load->view('layout/footer');
        } else {
            $this->return_form($return_id);
        }
    }

    public function return_history($type = 0, $return_id = 0){
        if ($type == 0) {
            $data_header = $this->data_header;

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
            $this->load->view('inventory/return/return_list.php', $data);
            $this->load->view('layout/footer');
        } else {
            $this->return_form($return_id);
        }
    }

    public function ajax_return_submit(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $data = array();

        if(isset($_POST)){
            $has_error = false;
            $this->db->trans_begin();
            $return_id = $_POST['return_id'];

            $data['supplier_id'] = $_POST['supplier_id'];
            $data['grn_id'] = $_POST['grn_id'];
            $data['return_date'] = dmy_to_ymd(trim($_POST['return_date']));
            $data['remarks'] = trim($_POST['remarks']);

            if($return_id > 0){
                $qry = $this->db->get_where('in_return', array('return_id' => $return_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['return_date']);
                $arr_date_old = explode('-', $row->return_date);

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['return_code'] = $this->mdl_general->generate_code(Feature::FEATURE_RETURN, $data['return_date']);

                    if($data['return_code'] == ''){
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }

                if($has_error == false){
                    $data['user_modified'] = my_sess('user_id');
                    $data['date_modified'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('in_return', array('return_id' => $return_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully update Return.');
                }
            }
            else {
                $data['return_code'] = $this->mdl_general->generate_code(Feature::FEATURE_RETURN, $data['return_date']);

                if($data['return_code'] != ''){
                    $data['user_created'] = my_sess('user_id');
                    $data['date_created'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_NEW;

                    $this->db->insert('in_return', $data);
                    $return_id = $this->db->insert_id();

                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $return_id;
                    $data_log['feature_id'] = Feature::FEATURE_RETURN;
                    $data_log['log_subject'] = 'Create Return (' . $data['return_code'] . ')';
                    $data_log['action_type'] = STATUS_NEW;
                    $this->db->insert('app_log', $data_log);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully add Return.');
                }
                else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'Failed generating code.';
                }
            }

            if($has_error == false) {
                if (isset($_POST['return_detail_id'])) {
                    $i = 0;
                    foreach ($_POST['return_detail_id'] as $key => $val) {
                        $data_detail = array();

                        $status = $_POST['status'][$key];

                        $data_detail['grn_detail_id'] = $_POST['grn_detail_id'][$key];
                        $data_detail['item_id'] = $_POST['item_id'][$key];
                        $data_detail['return_qty'] = $_POST['return_qty'][$key];
                        $data_detail['return_id'] = $return_id;
                        $data_detail['status'] = STATUS_NEW;

                        $qry_grn_detail = $this->db->get_where('in_grn_detail', array('grn_detail_id' => $data_detail['grn_detail_id']));
                        $row_grn_detail = $qry_grn_detail->row();

                        if ($_POST['return_detail_id'][$key] > 0) {
                            $qry_ret_det = $this->db->get_where('in_return_detail', array('return_detail_id' => $val));
                            $row_ret_det = $qry_ret_det->row();

                            if($status == STATUS_NEW) {
                                $this->mdl_general->update('in_grn_detail', array('grn_detail_id' => $data_detail['grn_detail_id']), array('item_delivery_qty_remain' => ($row_grn_detail->item_delivery_qty_remain + $row_ret_det->return_qty - $data_detail['return_qty'])));

                                $this->mdl_general->update('in_return_detail', array('return_detail_id' => $_POST['return_detail_id'][$key]), $data_detail);
                            }
                            else {
                                $this->mdl_general->update('in_grn_detail', array('grn_detail_id' => $data_detail['grn_detail_id']), array('item_delivery_qty_remain' => ($row_grn_detail->item_delivery_qty_remain + $row_ret_det->return_qty)));

                                $this->db->delete('in_return_detail', array('return_detail_id' => $_POST['return_detail_id'][$key]));
                            }
                        }
                        else {
                            if($status == STATUS_NEW) {
                                $this->mdl_general->update('in_grn_detail', array('grn_detail_id' => $data_detail['grn_detail_id']), array('item_delivery_qty_remain' => ($row_grn_detail->item_delivery_qty_remain - $data_detail['return_qty'])));

                                $this->db->insert('in_return_detail', $data_detail);
                            }
                        }
                        $i++;
                    }
                }
                else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'No Detail Return.';
                }
            }

            if($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
                else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('inventory/retur/return_manage.tpd');
                    } else{
                        $result['link'] = base_url('inventory/retur/return_manage/1/' . $return_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function ajax_return_list($menu_id = 0, $type = 0){
        //$type : 0 => manage, 1 => History
        $this->load->model('inventory/mdl_return');
        $like = array();
        $where = array();

        if($type == 0){
            $where_string = " in_return.status IN (" . STATUS_NEW . "," . STATUS_APPROVE . "," . STATUS_DISAPPROVE . ") ";
        } else {
            $where_string = " in_return.status IN (" . STATUS_POSTED . "," . STATUS_CANCEL . ") ";
        }

        $like = array();
        if(isset($_REQUEST['filter_return_code'])){
            if($_REQUEST['filter_return_code'] != ''){
                $like['in_return.return_code'] = $_REQUEST['filter_return_code'];
            }
        }
        if(isset($_REQUEST['filter_return_date_from'])){
            if($_REQUEST['filter_return_date_from'] != ''){
                $where['in_return.return_date >='] = dmy_to_ymd($_REQUEST['filter_return_date_from']);
            }
        }
        if(isset($_REQUEST['filter_return_date_to'])){
            if($_REQUEST['filter_return_date_to'] != ''){
                $where['in_return.return_date <='] = dmy_to_ymd($_REQUEST['filter_return_date_to']);
            }
        }
        if(isset($_REQUEST['filter_grn_code'])){
            if($_REQUEST['filter_grn_code'] != ''){
                $like['in_grn.grn_code'] = $_REQUEST['filter_grn_code'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['in_return.remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_supplier'])){
            if($_REQUEST['filter_supplier'] != ''){
                $like['in_supplier.supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }

        $iTotalRecords = $this->mdl_return->get_return(true, $where, $like, '', 0, 0, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_return.return_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_return.return_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_return.return_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'in_grn.grn_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'in_supplier.supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_return->get_return(false, $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);
        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('inventory/retur/' . ($type == 0 ? 'return_manage' : 'return_history') . '/1/' . $row->return_id) . '.tpd">View</a> </li>';

            if($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE){
                if(check_session_action($menu_id, STATUS_APPROVE)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_APPROVE . '" data-id="' . $row->return_id . '" data-code="' . $row->return_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CANCEL . '" data-id="' . $row->return_id . '" data-code="' . $row->return_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            } else if($row->status == STATUS_APPROVE){
                if(check_session_action($menu_id, STATUS_DISAPPROVE)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_DISAPPROVE . '" data-id="' . $row->return_id . '" data-code="' . $row->return_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_POSTED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_POSTED . '" data-id="' . $row->return_id . '" data-code="' . $row->return_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';
                }
                $btn_action .= '<li> <a href="' . base_url('inventory/retur/pdf_return/' . $row->return_id . '.tpd') . '" target="_blank" class="" >Print</a> </li>';
            } else if($row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('inventory/retur/pdf_return/' . $row->return_id . '.tpd') . '" target="_blank" class="" >Print</a> </li>';
            }

            $records["data"][] = array(
                $i . '.',
                $row->return_code,
                ymd_to_dmy($row->return_date),
                $row->grn_code,
                $row->supplier_name,
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

    public function return_form($return_id = 0){
        $this->load->model('inventory/mdl_return');

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

        $data['return_id'] = $return_id;
        if($return_id > 0){
            $qry = $this->mdl_return->get_return(false, array('in_return.return_id' => $return_id));
            $data['row'] = $qry->row();

            $data['qry_detail'] = $this->mdl_return->get_return_detail(false, array('return_id' => $return_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('inventory/return/return_form', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_modal_grn(){
        $this->load->view('inventory/return/ajax_modal_grn_list');
    }

    public function ajax_modal_grn_list($supplier_id = 0, $grn_id = 0){
        $this->load->model('inventory/mdl_grn');

        $like = array();
        $where = array();

        $where['in_grn.status'] = STATUS_POSTED;
        $where['in_grn.supplier_id'] = $supplier_id;

        if(isset($_REQUEST['filter_grn_code'])){
            if($_REQUEST['filter_grn_code'] != ''){
                $like['in_grn.grn_code'] = $_REQUEST['filter_grn_code'];
            }
        }
        if(isset($_REQUEST['filter_grn_date_from'])){
            if($_REQUEST['filter_grn_date_from'] != ''){
                $where['in_grn.grn_date >='] = dmy_to_ymd($_REQUEST['filter_grn_date_from']);
            }
        }
        if(isset($_REQUEST['filter_grn_date_to'])){
            if($_REQUEST['filter_grn_date_to'] != ''){
                $where['in_grn.grn_date <='] = dmy_to_ymd($_REQUEST['filter_grn_date_to']);
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['in_grn.remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_po_code'])){
            if($_REQUEST['filter_po_code'] != ''){
                $like['in_po.po_code'] = $_REQUEST['filter_po_code'];
            }
        }

        $iTotalRecords = $this->mdl_grn->get_grn(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_grn.grn_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_grn.grn_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_grn.date_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'in_po.po_code ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_grn->get_grn(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn = '<button class="btn green-meadow btn-xs btn-select-grn" data-id="' . $row->grn_id . '" data-code="' . $row->grn_code . '"data-po-code="' . $row->po_code . '" ><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';
            if($grn_id == $row->grn_id){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }
            else if($row->status_return == '1'){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-warning"></i>&nbsp;&nbsp;Return in process</button>';
            }
            $records["data"][] = array(
                $i . '.',
                $row->grn_code,
                ymd_to_dmy($row->grn_date),
                $row->remarks,
                $row->po_code,
                get_status_name($row->status),
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_grn_detail(){
        $this->load->view('inventory/return/ajax_modal_grn_detail_list');
    }

    public function ajax_modal_grn_detail_list($grn_id = 0, $grn_detail_id_exist = ''){
        $this->load->model('inventory/mdl_grn');

        $like = array();
        $where = array();

        $where['grn_id'] = $grn_id;

        if(isset($_REQUEST['filter_po_code'])){
            if($_REQUEST['filter_po_code'] != ''){
                $like['po_code'] = $_REQUEST['filter_po_code'];
            }
        }
        if(isset($_REQUEST['filter_item_code'])){
            if($_REQUEST['filter_item_code'] != ''){
                $like['item_code'] = $_REQUEST['filter_item_code'];
            }
        }
        if(isset($_REQUEST['filter_item_desc'])){
            if($_REQUEST['filter_item_desc'] != ''){
                $like['item_desc'] = $_REQUEST['filter_item_desc'];
            }
        }
        if(isset($_REQUEST['filter_uom'])){
            if($_REQUEST['filter_uom'] != ''){
                $like['uom_code'] = $_REQUEST['filter_uom'];
            }
        }
        if(isset($_REQUEST['filter_grn_qty'])){
            if($_REQUEST['filter_grn_qty'] != ''){
                $like['item_delivery_qty'] = $_REQUEST['filter_grn_qty'];
            }
        }

        $iTotalRecords = $this->mdl_grn->get_grn_detail(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'grn_detail_id asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'po_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_grn->get_grn_detail(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        //$records["debug"] = $this->db->last_query();

        $grn_detail_id_exist = trim($grn_detail_id_exist);
        $isexist = false;
        if($grn_detail_id_exist != ''){
            $isexist = true;
            $arr_id = explode('_', $grn_detail_id_exist);
        }

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';
            if($isexist){
                foreach($arr_id as $key=>$val){
                    if($val == $row->grn_detail_id){
                        $attr = 'selected="selected" disabled="disabled"';
                    }
                }
            }

            if($row->item_delivery_qty_remain <= 0){
                $attr = 'selected="selected" disabled="disabled"';
            }

            $records["data"][] = array(
                '<input type="checkbox" name="checkbox_grn_detail" value="' . $row->grn_detail_id . '" data-other-1="' . $row->item_id . '" data-other-2="' . $row->item_code . '" data-other-3="' . $row->item_desc . '" data-other-4="' . $row->po_code . '" data-other-5="' . $row->uom_code . '" data-other-6="' . $row->item_delivery_qty_remain . ' ' . $attr . '" />',
                $row->item_code,
                $row->item_desc,
                $row->uom_code,
                $row->item_delivery_qty_remain,
                ''
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_return_action(){
        $result = array();

        $this->db->trans_begin();

        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = '';

        $return_id = $_POST['return_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $return_id;
        $data_log['feature_id'] = Feature::FEATURE_RETURN;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($return_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('in_return', array('return_id' => $return_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_APPROVE) {
                    if ($row->status == STATUS_APPROVE) {
                        $result['valid'] = '0';
                        $result['message'] = 'Return already approved.';
                    } else {
                        $data['user_approved'] = my_sess('user_id');
                        $data['date_approved'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('in_return', array('return_id' => $return_id), $data);

                        $data_log['log_subject'] = 'Approve Return (' . $row->return_code . ')';
                        $data_log['action_type'] = STATUS_APPROVE;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully approve Return.';
                    }
                } else if ($data['status'] == STATUS_DISAPPROVE) {
                    if ($row->status == STATUS_DISAPPROVE) {
                        $result['valid'] = '0';
                        $result['message'] = 'Return already disapproved.';
                    } else {
                        $this->mdl_general->update('in_return', array('return_id' => $return_id), $data);

                        $data_log['log_subject'] = 'Disapprove Return (' . $row->return_code . ')';
                        $data_log['action_type'] = STATUS_DISAPPROVE;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully disapprove Return.';
                    }
                } else if ($data['status'] == STATUS_POSTED) {
                    if ($row->status == STATUS_POSTED) {
                        $result['valid'] = '0';
                        $result['message'] = 'Return already posted.';
                    } else {
                        if ($row->return_date != date('Y-m-d')) {
                            $result['valid'] = '0';
                            $result['message'] = 'Return date must be same with posting date.';
                        } else {
                            //POSTING GRN
                            $valid = $this->posting_return($return_id);
                            $result['debug'] = $valid;

                            if ($valid['error'] == '0') {
                                $this->mdl_general->update('in_return', array('return_id' => $return_id), $data);

                                $data_log['log_subject'] = 'Posting Return (' . $row->return_code . ')';
                                $data_log['action_type'] = STATUS_POSTED;
                                $this->db->insert('app_log', $data_log);

                                $result['message'] = 'Successfully posting Return.';
                            } else {
                                $result['valid'] = '0';
                                $result['message'] = $valid['message'];
                            }
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Return already canceled.';
                    } else {
                        $data['cancel_note'] = $_POST['reason'];
                        $this->mdl_general->update('in_return', array('return_id' => $return_id), $data);

                        $data_log['log_subject'] = 'Cancel Return (' . $row->return_code . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel Return.';
                    }
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            $result['valid'] = '0';
            $result['message'] = "Something error. Please try again later.";
        }
        else {
            $this->db->trans_commit();

            if($is_redirect){
                $this->session->set_flashdata('flash_message_class', ($result['valid'] == '1' ? 'success' : 'danger'));
                $this->session->set_flashdata('flash_message', $result['message']);
            }
        }

        echo json_encode($result);
    }

    private function posting_return($return_id = 0){
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($return_id > 0) {
            $qry = $this->db->get_where('in_return', array('return_id' => $return_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();
                $this->load->model('inventory/mdl_return');
                $this->load->model('inventory/mdl_grn');
                $this->load->model('finance/mdl_finance');

                $qry_grn = $this->db->get_where('in_grn', array('grn_id' => $row->grn_id));
                $row_grn = $qry_grn->row();

                $update_po_detail = array();

                $po_id = 0;
                $detail = array();
                $stock = array();
                $po_detail = array();

                $totalDebit = 0;
                $totalCredit = 0;
                $is_wo = false;
                $qryDetails = $this->mdl_return->get_return_detail(false, array('return_id' => $return_id));
                if ($qryDetails->num_rows() > 0) {
                    foreach ($qryDetails->result() as $det) {
                        $po_id = $det->po_id;

                        //update item remaining po
                        $po_detail[$det->po_detail_id] = array('item_qty_remaining' => ($det->po_qty_remaining + $det->return_qty));

                        $amount = $det->return_qty * ($det->item_price - ($det->item_disc / $det->po_item_qty)) * $row_grn->curr_rate;
                        $amount_unit = ($det->item_price - ($det->item_disc / $det->po_item_qty)) * $row_grn->curr_rate;

                        if (intval($det->wo_id) > 0) {
                            $is_wo = true;
                            $totalCredit += $amount;
                        } else {
                            if ($is_wo) {
                                $result['error'] = '1';
                                $result['message'] = 'Error WO ID.';
                                break;
                            } else {
                                if ($det->item_type == Purchasing::ITEM_MATERIAL) {
                                    if ($det->item_code != Purchasing::DIRECT_PURCHASE) {
                                        if (intval($det->account_coa_id) > 0) {
                                            $rowdet = array();
                                            $rowdet['coa_id'] = $det->account_coa_id;
                                            $rowdet['dept_id'] = $row_grn->department_id;
                                            $rowdet['journal_note'] = $det->item_desc;
                                            $rowdet['journal_debit'] = 0;
                                            $rowdet['journal_credit'] = $amount;
                                            $rowdet['reference_id'] = $det->return_detail_id;
                                            $rowdet['transtype_id'] = 0;

                                            $totalDebit += $rowdet['journal_debit'];
                                            $totalCredit += $rowdet['journal_credit'];

                                            array_push($detail, $rowdet);

                                            ///Stock
                                            $new_stock = $this->mdl_grn->get_new_stock($det->item_id, ($det->return_qty * $det->uom_factor * -1), $amount_unit);
                                            if ($new_stock['valid']) {
                                                unset($new_stock['valid']);

                                                $new_stock['doc_id'] = $det->return_detail_id;
                                                $new_stock['doc_type'] = Feature::FEATURE_RETURN;
                                                $new_stock['created_date'] = date('Y-m-d H:i:s');
                                                $new_stock['status'] = STATUS_NEW;

                                                array_push($stock, $new_stock);
                                            } else {
                                                $result['error'] = '1';
                                                $result['message'] = 'Failed insert to stock.';
                                                break;
                                            }
                                        } else {
                                            $result['error'] = '1';
                                            $result['message'] = $det->item_desc  . ' COA ID is empty.';
                                            break;
                                        }
                                    } else {
                                        if (intval($det->account_coa_id) > 0) {
                                            $rowdet = array();
                                            $rowdet['coa_id'] = $det->account_coa_id;
                                            $rowdet['dept_id'] = $row_grn->department_id;
                                            $rowdet['journal_note'] = $det->item_desc;
                                            $rowdet['journal_debit'] = 0;
                                            $rowdet['journal_credit'] = $amount;
                                            $rowdet['reference_id'] = $det->return_detail_id;
                                            $rowdet['transtype_id'] = 0;

                                            $totalDebit += $rowdet['journal_debit'];
                                            $totalCredit += $rowdet['journal_credit'];

                                            array_push($detail, $rowdet);
                                        } else {
                                            $result['error'] = '1';
                                            $result['message'] = $det->item_desc  . ' COA ID is empty.';
                                            break;
                                        }
                                    }
                                } else {
                                    if (intval($det->exp_coa_id) > 0) {
                                        $rowdet = array();
                                        $rowdet['coa_id'] = $det->exp_coa_id;
                                        $rowdet['dept_id'] = $row_grn->department_id;
                                        $rowdet['journal_note'] = $det->item_desc;
                                        $rowdet['journal_debit'] = 0;
                                        $rowdet['journal_credit'] = $amount;
                                        $rowdet['reference_id'] = $det->return_detail_id;
                                        $rowdet['transtype_id'] = 0;

                                        $totalDebit += $rowdet['journal_debit'];
                                        $totalCredit += $rowdet['journal_credit'];

                                        array_push($detail, $rowdet);
                                    } else {
                                        $result['error'] = '1';
                                        $result['message'] = $det->item_desc . ', Service EXP COA ID is empty.';
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if ($is_wo) {
                        if ($result['error'] = '0') {
                            $qry_key = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_GRN_WIP));
                            if ($qry_key->num_rows() > 0) {
                                $row_key = $qry_key->row();

                                if ($row_key->coa_id > 0) {
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $row_key->coa_id;
                                    $rowdet['dept_id'] = 0;
                                    $rowdet['journal_note'] = ($row->remarks != '' ? $row->remarks : $row->return_code);
                                    $rowdet['journal_debit'] = 0;
                                    $rowdet['journal_credit'] = $totalCredit;
                                    $rowdet['reference_id'] = $row_grn->wo_id;
                                    $rowdet['transtype_id'] = $row_key->transtype_id;

                                    $totalDebit += $rowdet['journal_debit'];
                                    $totalCredit += $rowdet['journal_credit'];

                                    array_push($detail, $rowdet);
                                } else {
                                    $result['error'] = '1';
                                    $result['message'] = 'Spec WIP is empty.';
                                }
                            } else {
                                $result['error'] = '1';
                                $result['message'] = 'Spec WIP not found.';
                            }
                        }
                    }

                    if ($result['error'] == '0') {
                        $qry_key = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_GRN));
                        if ($qry_key->num_rows() > 0) {
                            $row_key = $qry_key->row();

                            if ($row_key->coa_id > 0) {
                                $rowdet = array();
                                $rowdet['coa_id'] = $row_key->coa_id;
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = ($row->remarks != '' ? $row->remarks : $row->return_code);
                                $rowdet['journal_debit'] = $totalCredit;
                                $rowdet['journal_credit'] = 0;
                                $rowdet['reference_id'] = $row->return_id;
                                $rowdet['transtype_id'] = $row_key->transtype_id;

                                $totalDebit += $rowdet['journal_debit'];
                                $totalCredit += $rowdet['journal_credit'];

                                array_push($detail, $rowdet);
                            } else {
                                $result['error'] = '1';
                                $result['message'] = 'Spec Return is empty.';
                            }
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Spec Return not found.';
                        }
                    }

                    if ($result['error'] == '0') {
                        if ($totalDebit == $totalCredit) {
                            $header = array();
                            $header['journal_no'] = $row->return_code;
                            $header['journal_date'] = $row->return_date;
                            $header['journal_remarks'] = $row->remarks;
                            $header['modul'] = GLMOD::GL_MOD_AP;
                            $header['journal_amount'] = $totalDebit;
                            $header['reference'] = strval($row->return_id);
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Debit != Credit.';

                            $result['debug'] .= '||Debit : ' . $totalDebit;
                            $result['debug'] .= '||Credit : ' . $totalCredit;
                        }
                    }

                    if($po_id > 0) {
                        $qry_po = $this->db->get_where('in_po', array('po_id' => $po_id));
                        if ($qry_po->num_rows() > 0) {
                            $row_po = $qry_po->row();
                            if ($row_po->status == STATUS_CLOSED) {
                                $this->mdl_general->update('in_po', array('po_id' => $po_id), array('status' => STATUS_APPROVE));
                            }
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'PO not found.';
                        }
                    } else {
                        $result['error'] = '1';
                        $result['message'] = 'PO not found.';
                    }

                    if ($result['error'] == '0') {
                        $valid = $this->mdl_finance->postJournal($header, $detail);

                        if ($valid) {
                            if (count($stock) > 0) {
                                foreach ($stock as $row_stock) {
                                    $this->db->insert('in_ms_item_stock', $row_stock);

                                    if ($row_stock['item_id'] > 0) {
                                        $this->mdl_general->update('in_ms_item', array('item_id' => $row_stock['item_id']), array('status_order' => 1));
                                    }
                                }
                            }
                            //$result['debug'] .= print_r($po_detail);
                            if (count($po_detail) > 0) {
                                foreach ($po_detail as $po_key => $po_value) {
                                    $this->mdl_general->update('in_po_detail', array('po_detail_id' => $po_key), $po_value);
                                }
                            }
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Failed insert journal.';
                        }
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'Return not found.';
            }
        }

        return $result;
    }

    private function disapprove_return($return_id = 0){
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($return_id > 0) {
            $qry = $this->db->get_where('in_return', array('return_id' => $return_id));
            $po_id = 0;
            if ($qry->num_rows() > 0) {
                $this->load->model('inventory/mdl_return');

                $qryDetails = $this->mdl_return->get_return_detail(false, array('return_id' => $return_id));
                if ($qryDetails->num_rows() > 0) {
                    foreach ($qryDetails->result() as $det) {
                        //update item remaining po
                        $this->mdl_general->update('in_po_detail', array('po_detail_id' => $det->po_detail_id), array('item_qty_remaining' => ($det->po_qty_remaining - $det->return_qty)));

                        $po_id = $det->po_id;
                    }
                }

                if($po_id > 0) {
                    $qry_po = $this->mdl_general->get('in_po_detail', array('po_id' => $po_id, 'item_qty_remaining >' => 0));
                    if ($qry_po->num_rows() == 0) {
                        $this->mdl_general->update('in_po', array('po_id' => $po_id), array('status' => STATUS_CLOSED));
                    }
                } else {
                    $result['error'] = '1';
                    $result['message'] = 'PO not found.';
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'Return not found.';
            }
        }

        return $result;
    }

    public function pdf_return($return_id = 0) {
        if($return_id > 0){
            $this->load->model('inventory/mdl_return');

            $qry = $this->mdl_return->get_return(false, array('in_return.return_id' => $return_id));
            if($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_det'] =  $this->mdl_return->get_return_detail(false, array('return_id' => $return_id));

                $this->load->view('inventory/return/pdf_return.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->return_code . ".pdf", array('Attachment'=>0));
            }
            else {
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

}

/* End of file retur.php */
/* Location: ./application/controllers/inventory/retur.php */