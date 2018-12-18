<?php
/**
 * Created by PhpStorm.
 * User: Syaiful
 * Date: 03/12/2015
 * Time: 1:16 PM
 */

ini_set('display_errors', 1);

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grn extends CI_Controller {

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
        //$this->stock_request_manage();
    }

    public function grn_manage($type = 0, $grn_id = 0){
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
            $this->load->view('inventory/grn/grn_list.php', $data);
            $this->load->view('layout/footer');
        } else {
            $this->grn_form($grn_id);
        }
    }

    public function grn_history($type = 0, $grn_id = 0){
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
            $this->load->view('inventory/grn/grn_list.php', $data);
            $this->load->view('layout/footer');
        } else {
            $this->grn_form($grn_id);
        }
    }

    public function ajax_grn_list($menu_id = 0, $type = 0){
        //$type : 0 => manage, 1 => History
        $this->load->model('inventory/mdl_grn');

        if($type == 0){
            $where['in_grn.status'] = STATUS_NEW;
        } else {
            $where['in_grn.status <>'] = STATUS_NEW;
        }

        $like = array();
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
        if(isset($_REQUEST['filter_po_code'])){
            if($_REQUEST['filter_po_code'] != ''){
                $like['in_po.po_code'] = $_REQUEST['filter_po_code'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['in_grn.remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_supplier'])){
            if($_REQUEST['filter_supplier'] != ''){
                $like['in_supplier.supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }

        $iTotalRecords = $this->mdl_grn->get_grn(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_grn.grn_id desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_grn.grn_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_grn.grn_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'in_po.po_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'in_supplier.supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 7){
                $order = 'in_grn.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_grn->get_grn(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('inventory/grn/' . ($type == 0 ? 'grn_manage' : 'grn_history') . '/1/' . $row->grn_id) . '.tpd">View</a> </li>';

            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_POSTED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_POSTED . '" data-id="' . $row->grn_id . '" data-code="' . $row->grn_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CANCEL . '" data-id="' . $row->grn_id . '" data-code="' . $row->grn_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            } else if($row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('inventory/grn/pdf_grn/' . $row->grn_id . '.tpd') . '" target="_blank" class="" >Print</a> </li>';
            }

            $records["data"][] = array(
                $i . '.',
                $row->grn_code,
                ymd_to_dmy($row->grn_date),
                $row->po_code,
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

    public function grn_form($grn_id = 0){
        $this->load->model('inventory/mdl_grn');

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

        $data['grn_id'] = $grn_id;
        if($grn_id > 0){
            $qry = $this->mdl_grn->get_grn(false, array('in_grn.grn_id' => $grn_id));
            $data['row'] = $qry->row();

            $data['qry_detail'] = $this->mdl_grn->get_grn_detail(false, array('grn_id' => $grn_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('inventory/grn/grn_form', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_grn_submit(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $data = array();

        if(isset($_POST)){
            $has_error = false;
            $this->db->trans_begin();
            $grn_id = $_POST['grn_id'];

            $data['supplier_id'] = $_POST['supplier_id'];
            $data['po_id'] = $_POST['po_id'];
            $data['grn_date'] = dmy_to_ymd(trim($_POST['grn_date']));
            $data['currencytype_id'] = trim($_POST['currencytype_id']);
            $data['curr_rate'] = str_replace(',', '.', trim($_POST['curr_rate']));
            $data['vehicle_no'] = trim($_POST['vehicle_no']);
            $data['do_no'] = trim($_POST['do_no']);
            $data['remarks'] = trim($_POST['remarks']);

            $qry_po = $this->db->get_where('in_po', array('po_id' => $data['po_id']));
            $row_po = $qry_po->row();
            $data['wo_id'] = $row_po->wo_id;
            $data['department_id'] = $row_po->department_id;

            if($grn_id > 0){
                $qry = $this->db->get_where('in_grn', array('grn_id' => $grn_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['grn_date']);
                $arr_date_old = explode('-', $row->grn_date);

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['grn_code'] = $this->mdl_general->generate_code(Feature::FEATURE_GRN, $data['grn_date']);

                    if($data['grn_code'] == ''){
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }

                if($has_error == false){
                    $data['user_modified'] = my_sess('user_id');
                    $data['date_modified'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('in_grn', array('grn_id' => $grn_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully update GRN.');
                }
            }
            else {
                $data['grn_code'] = $this->mdl_general->generate_code(Feature::FEATURE_GRN, $data['grn_date']);

                if($data['grn_code'] != ''){
                    $data['user_created'] = my_sess('user_id');
                    $data['date_created'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_NEW;

                    $this->db->insert('in_grn', $data);
                    $grn_id = $this->db->insert_id();

                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $grn_id;
                    $data_log['feature_id'] = Feature::FEATURE_GRN;
                    $data_log['log_subject'] = 'Create GRN (' . $data['grn_code'] . ')';
                    $data_log['action_type'] = STATUS_NEW;
                    $this->db->insert('app_log', $data_log);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully add GRN.');
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'Failed generating code.';
                }
            }

            if($has_error == false) {
                if (isset($_POST['grn_detail_id'])) {
                    $i = 0;
                    foreach ($_POST['grn_detail_id'] as $key => $val) {
                        $data_detail = array();

                        $status = $_POST['status'][$key];

                        $data_detail['po_detail_id'] = $_POST['po_detail_id'][$key];
                        $data_detail['item_qty'] = $_POST['item_qty'][$key];
                        $data_detail['grn_item_type'] = $_POST['grn_item_type'][$key];
                        $data_detail['grn_id'] = $grn_id;
                        $data_detail['item_delivery_qty'] = $_POST['item_delivery_qty'][$key];
                        $data_detail['item_delivery_qty_remain'] = $data_detail['item_delivery_qty'];
                        $data_detail['uom_id'] = $_POST['uom_id'][$key];
                        $data_detail['uom_factor'] = $_POST['uom_factor'][$key];
                        $data_detail['status'] = STATUS_NEW;

                        $qry_po_detail = $this->db->get_where('in_po_detail', array('po_detail_id' => $data_detail['po_detail_id']));
                        $row_po_detail = $qry_po_detail->row();

                        if ($_POST['grn_detail_id'][$key] > 0) {
                            $qry_grn_det = $this->db->get_where('in_grn_detail', array('grn_detail_id' => $val));
                            $row_grn_det = $qry_grn_det->row();

                            if($status == STATUS_NEW) {
                                $this->mdl_general->update('in_po_detail', array('po_detail_id' => $data_detail['po_detail_id']), array('item_qty_remaining' => ($row_po_detail->item_qty_remaining + $row_grn_det->item_delivery_qty - $data_detail['item_delivery_qty'])));

                                $this->mdl_general->update('in_grn_detail', array('in_grn_detail.grn_detail_id' => $_POST['grn_detail_id'][$key]), $data_detail);
                            } else {
                                $this->mdl_general->update('in_po_detail', array('po_detail_id' => $data_detail['po_detail_id']), array('item_qty_remaining' => ($row_po_detail->item_qty_remaining + $row_grn_det->item_delivery_qty)));

                                $this->db->delete('in_grn_detail', array('in_grn_detail.grn_detail_id' => $_POST['grn_detail_id'][$key]));
                            }
                        } else {
                            if($status == STATUS_NEW) {
                                $this->mdl_general->update('in_po_detail', array('po_detail_id' => $data_detail['po_detail_id']), array('item_qty_remaining' => ($row_po_detail->item_qty_remaining - $data_detail['item_delivery_qty'])));

                                $this->db->insert('in_grn_detail', $data_detail);
                            }
                        }
                        $i++;
                    }
                }
                else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'No Detail GRN.';
                }
            }

            if($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
                else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('inventory/grn/grn_manage.tpd');
                    }
                    else{
                        $result['link'] = base_url('inventory/grn/grn_manage/1/' . $grn_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function ajax_grn_action(){
        $result = array();

        $this->load->model('inventory/mdl_grn');

        $this->db->trans_begin();

        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = '';

        $grn_id = $_POST['grn_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $grn_id;
        $data_log['feature_id'] = Feature::FEATURE_GRN;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($grn_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('in_grn', array('grn_id' => $grn_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_POSTED) {
                    if ($row->status == STATUS_POSTED) {
                        $result['valid'] = '0';
                        $result['message'] = 'GRN already posted.';
                    } else {
                        if ($row->grn_date != date('Y-m-d')) {
                            $result['valid'] = '0';
                            $result['message'] = 'GRN date must be same with posting date.';
                        } else {
                            //CHECK UOM FACTOR
                            $valid_factor = $this->check_uom_factor($grn_id);
                            if ($valid_factor['error'] == '0') {
                                //POSTING GRN
                                $valid = $this->posting_grn($grn_id);
                                $result['debug'] = $valid;

                                if ($valid['error'] == '0') {
                                    $data['user_posted'] = my_sess('user_id');
                                    $data['date_posted'] = date('Y-m-d H:i:s');

                                    $this->mdl_general->update('in_grn', array('grn_id' => $grn_id), $data);

                                    $num_po_detail = $this->mdl_general->count('in_po_detail', array('po_id' => $row->po_id, 'item_qty_remaining >' => 0, 'status <>' => STATUS_CANCEL));
                                    if ($num_po_detail <= 0) {
                                        $qry_po = $this->db->get_where('in_po', array('po_id' => $row->po_id));
                                        $row_po = $qry_po->row();

                                        if ($row_po->status != STATUS_CLOSED) {
                                            $this->mdl_general->update('in_po', array('po_id' => $row->po_id), array('status' => STATUS_CLOSED));

                                            $data_log_po['user_id'] = my_sess('user_id');
                                            $data_log_po['log_date'] = date('Y-m-d H:i:s');
                                            $data_log_po['reff_id'] = $row->po_id;
                                            $data_log_po['feature_id'] = Feature::FEATURE_PO;
                                            $data_log_po['log_subject'] = 'Closed PO (' . $row_po->po_code . ') automoatic by System (Posting ' . $row->grn_code . ')';
                                            $data_log_po['action_type'] = STATUS_CLOSED;
                                            $this->db->insert('app_log', $data_log_po);
                                        }
                                    }

                                    $data_log['log_subject'] = 'Posting GRN (' . $row->grn_code . ')';
                                    $data_log['action_type'] = STATUS_POSTED;
                                    $this->db->insert('app_log', $data_log);

                                    $result['message'] = 'Successfully posting GRN.';
                                } else {
                                    $result['valid'] = '0';
                                    $result['message'] = $valid['message'];
                                }
                            } else {
                                $result['valid'] = '0';
                                $result['message'] = $valid_factor['message'];
                            }
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'GRN already canceled.';
                    } else {
                        $data['cancel_note'] = $_POST['reason'];
                        $this->mdl_general->update('in_grn', array('grn_id' => $grn_id), $data);

                        $qry_det = $this->mdl_grn->get_grn_detail(false, array('grn_id' => $grn_id));
                        foreach ($qry_det->result() as $row_det) {
                            $qry_po_det = $this->db->get_where('in_po_detail', array('po_detail_id' => $row_det->po_detail_id));
                            $row_po_det = $qry_po_det->row();

                            $this->mdl_general->update('in_po_detail', array('po_detail_id' => $row_det->po_detail_id), array('item_qty_remaining' => ($row_po_det->item_qty_remaining + $row_det->item_delivery_qty)));
                        }

                        $data_log['log_subject'] = 'Cancel GRN (' . $row->grn_code . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel GRN.';
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

    private  function check_uom_factor($grn_id = 0) {
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($grn_id > 0) {
            $qry = $this->db->get_where('view_in_grn_detail', array('grn_id' => $grn_id));
            foreach ($qry->result() as $row) {
                if ($row->item_code != Purchasing::DIRECT_PURCHASE) {
                    if ($row->uom_id == $row->uom_id_stock && $row->uom_factor == $row->qty_distribution) {
                        //TRUE
                    } else {
                        $result['error'] = '1';
                        $result['message'] = 'UOM and Factor is different from UOM and Factor stock. Please check. (' . $row->item_code . ')';

                        break;
                    }
                }
            }
        }

        return $result;
    }

    private function posting_grn($grn_id = 0){
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($grn_id > 0) {
            $qry_grn = $this->db->get_where('in_grn', array('grn_id' => $grn_id));
            if ($qry_grn->num_rows() > 0) {
                $row_grn = $qry_grn->row();

                $this->load->model('inventory/mdl_grn');
                $this->load->model('finance/mdl_finance');

                $detail = array();
                $insert_stock = array();

                $totalDebit = 0;
                $totalCredit = 0;
                $is_wo = false;
                $qryDetails = $this->mdl_grn->get_grn_detail(false, array('grn_id' => $grn_id));
                if ($qryDetails->num_rows() > 0) {
                    foreach ($qryDetails->result() as $det) {
                        $amount = $det->item_delivery_qty * ($det->item_price - ($det->item_disc / $det->po_item_qty)) * $row_grn->curr_rate;
                        $amount_unit = (($det->item_price - ($det->item_disc / $det->po_item_qty)) * $row_grn->curr_rate) / $det->uom_factor;

                        if ($row_grn->wo_id > 0) {
                            $is_wo = true;

                            if($det->item_code != Purchasing::DIRECT_PURCHASE){
                                if($det->grn_item_type == Purchasing::ITEM_MATERIAL) {
                                    $new_stock = $this->mdl_grn->get_new_stock($det->item_id, ($det->item_delivery_qty * $det->uom_factor), $amount_unit);
                                    if ($new_stock['valid']) {
                                        unset($new_stock['valid']);

                                        $new_stock['doc_id'] = $det->grn_detail_id;
                                        $new_stock['doc_type'] = Feature::FEATURE_GRN;
                                        $new_stock['created_date'] = date('Y-m-d H:i:s');
                                        $new_stock['status'] = STATUS_NEW;

                                        //$this->db->insert('in_ms_item_stock', $new_stock);
                                        array_push($insert_stock, $new_stock);

                                        $result['debug'] .= 'wo insert stock ' . $det->item_id;
                                    } else {
                                        $result['error'] = '1';
                                        $result['message'] = 'Failed insert to stock.';
                                        break;
                                    }
                                }
                            }

                            $totalDebit += $amount;
                        } else {
                            if ($is_wo) {
                                $result['error'] = '1';
                                $result['message'] = 'Error WO ID.';
                                break;
                            } else {
                                if ($det->account_coa_id > 0) {
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $det->account_coa_id;
                                    $rowdet['dept_id'] = $row_grn->department_id;
                                    $rowdet['journal_note'] = ($det->item_code == Purchasing::DIRECT_PURCHASE ? $det->item_desc : $det->item_desc);
                                    $rowdet['journal_debit'] = $amount;
                                    $rowdet['journal_credit'] = 0;
                                    $rowdet['reference_id'] = $det->grn_detail_id;
                                    $rowdet['transtype_id'] = 0;

                                    $totalDebit += $rowdet['journal_debit'];
                                    $totalCredit += $rowdet['journal_credit'];

                                    array_push($detail, $rowdet);

                                    if($det->item_code != Purchasing::DIRECT_PURCHASE){
                                        $this->mdl_general->update('in_ms_item', array('item_id' => $det->item_id), array('item_latestprice' => $amount / $det->uom_factor));

                                        if($det->grn_item_type == Purchasing::ITEM_MATERIAL) {
                                            $new_stock = $this->mdl_grn->get_new_stock($det->item_id, ($det->item_delivery_qty * $det->uom_factor), $amount_unit);
                                            if ($new_stock['valid']) {
                                                unset($new_stock['valid']);

                                                $new_stock['doc_id'] = $det->grn_detail_id;
                                                $new_stock['doc_type'] = Feature::FEATURE_GRN;
                                                $new_stock['created_date'] = date('Y-m-d H:i:s');
                                                $new_stock['status'] = STATUS_NEW;

                                                $result['debug'] .= 'wo insert stock ' . $det->item_id;

                                                //$this->db->insert('in_ms_item_stock', $new_stock);
                                                array_push($insert_stock, $new_stock);
                                            } else {
                                                $result['error'] = '1';
                                                $result['message'] = 'Failed insert to stock.';
                                                break;
                                            }
                                        }
                                        else {
                                            $result['debug'] .= 'ITEM SERVICE';
                                        }
                                    }
                                } else {
                                    $result['error'] = '1';
                                    $result['message'] = 'COA ID is empty.';
                                    break;
                                }
                            }
                        }

                    }
                }

                if ($is_wo) {
                    if ($result['error'] == '0') {
                        $qry_key = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_GRN_WIP));
                        if ($qry_key->num_rows() > 0) {
                            $row_key = $qry_key->row();

                            if ($row_key->coa_id > 0) {
                                $rowdet = array();
                                $rowdet['coa_id'] = $row_key->coa_id;
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = ($row_grn->remarks != '' ? $row_grn->remarks : $row_grn->grn_code);
                                $rowdet['journal_debit'] = $totalDebit;
                                $rowdet['journal_credit'] = 0;
                                $rowdet['reference_id'] = $row_grn->wo_id;
                                $rowdet['transtype_id'] = $row_key->transtype_id;

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
                            $rowdet['journal_note'] = ($row_grn->remarks != '' ? $row_grn->remarks : $row_grn->grn_code);
                            $rowdet['journal_debit'] = 0;
                            $rowdet['journal_credit'] = $totalDebit;
                            $rowdet['reference_id'] = $row_grn->grn_id;
                            $rowdet['transtype_id'] = $row_key->transtype_id;

                            $totalDebit += $rowdet['journal_debit'];
                            $totalCredit += $rowdet['journal_credit'];

                            array_push($detail, $rowdet);
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Spec GRN is empty.';
                        }
                    } else {
                        $result['error'] = '1';
                        $result['message'] = 'Spec GRN not found.';
                    }
                }

                if ($result['error'] == '0') {
                    if ($totalDebit == $totalCredit) {
                        $header = array();
                        $header['journal_no'] = $row_grn->grn_code;
                        $header['journal_date'] = $row_grn->grn_date;
                        $header['journal_remarks'] = $row_grn->remarks;
                        $header['modul'] = GLMOD::GL_MOD_AP;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = strval($row_grn->grn_id);

                        $valid = $this->mdl_finance->postJournal($header, $detail);
                        $this->insert_stock($insert_stock);

                        if ($valid == false) {
                            $result['error'] = '1';
                            $result['message'] = 'Failed insert journal.';
                        }
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'GRN not found.';
            }
        }

        return $result;
    }

    private function insert_stock($new_stock = array()) {
        if (count($new_stock) > 0) {
            foreach ($new_stock as $detail) {
                $this->db->insert('in_ms_item_stock', $detail);

                if ($detail['item_id'] > 0) {
                    $this->mdl_general->update('in_ms_item', array('item_id' => $detail['item_id']), array('status_order' => 0));
                }
            }
        }
    }

    public function ajax_modal_supplier(){
        $this->load->view('inventory/grn/ajax_modal_supplier_list');
    }

    public function ajax_modal_supplier_list($supplier_id = 0){
        $like = array();
        $where = array();

        $where['status <>'] = STATUS_DELETE;

        if(isset($_REQUEST['filter_supplier_name'])){
            if($_REQUEST['filter_supplier_name'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_supplier_name'];
            }
        }
        if(isset($_REQUEST['filter_address'])){
            if($_REQUEST['filter_address'] != ''){
                $like['supplier_address'] = $_REQUEST['filter_address'];
            }
        }
        if(isset($_REQUEST['filter_city'])){
            if($_REQUEST['filter_city'] != ''){
                $like['supplier_city'] = $_REQUEST['filter_city'];
            }
        }
        if(isset($_REQUEST['filter_phone'])){
            if($_REQUEST['filter_phone'] != ''){
                $like['supplier_telephone'] = $_REQUEST['filter_phone'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('in_supplier', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'supplier_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('in_supplier', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-supplier" data-id="' . $row->supplier_id . '" data-name="' . $row->supplier_name . '"><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';
            if($supplier_id == $row->supplier_id){
                $btn = '<button class="btn green-meadow yellow-stripe btn-xs" disabled="disabled"><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }
            $records["data"][] = array(
                $i . '.',
                $row->supplier_name,
                $row->supplier_address,
                $row->supplier_city,
                $row->supplier_telephone,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_po(){
        $this->load->view('inventory/grn/ajax_modal_po_list');
    }

    public function ajax_modal_po_list($supplier_id = 0, $po_id = 0){
        $this->load->model('inventory/mdl_grn');

        $like = array();
        $where = array();

        $where['in_po.status'] = STATUS_APPROVE;
        $where['in_po.supplier_id'] = $supplier_id;

        if(isset($_REQUEST['filter_po_code'])){
            if($_REQUEST['filter_po_code'] != ''){
                $like['in_po.po_code'] = $_REQUEST['filter_po_code'];
            }
        }
        if(isset($_REQUEST['filter_po_date_from'])){
            if($_REQUEST['filter_po_date_from'] != ''){
                $where['in_po.po_date >='] = dmy_to_ymd($_REQUEST['filter_po_date_from']);
            }
        }
        if(isset($_REQUEST['filter_po_date_to'])){
            if($_REQUEST['filter_po_date_to'] != ''){
                $where['in_po.po_date <='] = dmy_to_ymd($_REQUEST['filter_po_date_to']);
            }
        }
        if(isset($_REQUEST['filter_supplier'])){
            if($_REQUEST['filter_supplier'] != ''){
                $like['in_supplier.supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['in_po.remarks'] = $_REQUEST['filter_remarks'];
            }
        }

        $iTotalRecords = $this->mdl_grn->get_po_by_supplier(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_po.po_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_po.po_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_po.po_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'in_supplier.supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'in_po.remarks ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_grn->get_po_by_supplier(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn = '<button class="btn green-meadow btn-xs btn-select-po" data-id="' . $row->po_id . '" data-code="' . $row->po_code . '"><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';
            if($po_id == $row->po_id){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }
            else if($row->status_grn == '1'){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-warning"></i>&nbsp;&nbsp;GRN in process</button>';
            }
            $records["data"][] = array(
                $i . '.',
                $row->po_code,
                ymd_to_dmy($row->po_date),
                $row->supplier_name,
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

    public function ajax_modal_po_detail(){
        $this->load->view('inventory/grn/ajax_modal_po_detail');
    }

    public function ajax_modal_po_detail_list($po_id = 0, $po_detail_id_exist = '-'){
        $this->load->model('inventory/mdl_grn');

        $like = array();
        $where = array();

        $where['in_po_detail.po_id'] = $po_id;
        $where['in_po_detail.status <>'] = STATUS_CANCEL;

        $iTotalRecords = $this->mdl_grn->get_po_detail(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_po_detail.po_detail_id desc';

        $qry = $this->mdl_grn->get_po_detail(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $po_detail_id_exist = trim($po_detail_id_exist);
        $isexist = false;
        if($po_detail_id_exist != '-'){
            $isexist = true;
            $arr_id = explode('_', $po_detail_id_exist);
        }

        $i = $iDisplayStart + 1;

        foreach($qry->result() as $row) {
            $attr = '';

            if ($row->item_qty_remaining <= 0) {
               $attr = 'selected="selected" disabled="disabled"';
            } else {
                if ($isexist) {
                    foreach ($arr_id as $key => $val) {
                        if ($val == $row->po_detail_id) {
                            $attr = 'selected="selected" disabled="disabled"';
                        }
                    }
                }
            }

            $attr_date = '';
            $attr_date .= ' data-other-1="' . $row->item_type . '" ';
            $attr_date .= ' data-other-2="' . $row->item_qty_remaining . '" ';
            $attr_date .= ' data-other-3="' . $row->uom_id . '" ';
            $attr_date .= ' data-other-4="' . $row->uom_factor . '" ';
            $attr_date .= ' data-other-5="' . $row->item_code . '" ';
            $attr_date .= ' data-other-6="' . ($row->item_code == Purchasing::DIRECT_PURCHASE ? $row->item_desc : $row->ms_item_desc) . '" ';
            $attr_date .= ' data-other-7="' . $row->uom_code . '" ';
            $attr_date .= ' data-other-8="' . $row->item_id . '" ';

            $records["data"][] = array(
               '<input type="checkbox" name="checkbox_po_detail" value="' . $row->po_detail_id . '" ' . $attr_date . ' ' . $attr . ' />',
               $row->item_code,
               ($row->item_code == Purchasing::DIRECT_PURCHASE ? $row->item_desc : $row->ms_item_desc),
               $row->uom_code . ' [' . $row->uom_factor . ']',
               '<span class="mask_currency">' . $row->item_qty . '</span>',
               '<span class="mask_currency">' . $row->item_qty_remaining . '</span>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_po_detail($po_id = 0, $tr_row = 0){
        $this->load->model('inventory/mdl_grn');

        $result = array();
        $result['type'] = '1';
        $result['message'] = '';

        if($po_id > 0){
            $qry = $this->mdl_grn->get_po_detail(false, array('in_po_detail.po_id' => $po_id, 'in_po_detail.status <>' => STATUS_DELETE));
            $i = $tr_row + 1;
            $no = 1;
            foreach($qry->result() as $row){

                $result['message'] .= '<tr>';
                $result['message'] .= '<input type="hidden" name="grn_detail_id[' . $i . ']" value="0" />';
                $result['message'] .= '<input type="hidden" name="po_detail_id[' . $i . ']" value="' . $row->po_detail_id . '" />';
                $result['message'] .= '<input type="hidden" name="grn_item_type[' . $i . ']" value="' . $row->item_type . '" />';
                $result['message'] .= '<input type="hidden" name="item_qty[' . $i . ']" value="' . $row->item_qty_remaining . '" />';
                $result['message'] .= '<input type="hidden" name="buy_uom_id[' . $i . ']" value="' . $row->buy_uom_id . '" />';
                $result['message'] .= '<input type="hidden" name="uom_factor[' . $i . ']" value="' . $row->uom_factor . '" />';
                $result['message'] .= '<input type="hidden" class="class_status" name="status[' . $i . ']" value="1" />';
                $result['message'] .= '<td class="text-center text-middle">' . $no . '.</td>
                                        <td class="text-center text-middle">' . $row->item_code . '</td>
                                        <td class="text-center text-middle">' . $row->item_desc . '</td>
                                        <td class="text-center text-middle">' . $row->uom_code . '</td>
                                        <td class="text-right text-middle">' . format_num($row->item_qty_remaining, 0) . '</td>
                                        <td><input type="text" class="form-control text-right delivery_qty mask_number" data-max="' . $row->item_qty_remaining . '" name="item_delivery_qty[' . $i . ']" value="' . $row->item_qty_remaining . '" /></td>
                                    </tr>';
                $i++;
                $no++;
            }
        }

        echo json_encode($result);
    }

    public function pdf_grn($grn_id = 0) {
        if($grn_id > 0){
            $this->load->model('inventory/mdl_grn');

            $qry = $this->mdl_grn->get_grn(false, array('in_grn.grn_id' => $grn_id));
            if($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_det'] =  $this->mdl_grn->get_grn_detail(false, array('grn_id' => $grn_id));

                //grn user posting
                $qry_approve = $this->db->get_where('ms_user', array('user_id' => $data['row']->user_posted));
                $row_approve = $qry_approve->row();
                $data['user_posted'] = $row_approve->user_fullname;

                //po & pr
                $this->db->select('ms_user.user_fullname, CONVERT(date, in_po.date_approved) as po_date, pr_user.user_fullname as pr_created, CONVERT(date, in_pr.date_created) as pr_date');
                $this->db->from('in_po');
                $this->db->join('ms_user', 'in_po.user_approved = ms_user.user_id', 'left');
                $this->db->join('in_pr', 'in_po.pr_id = in_pr.pr_id', 'left');
                $this->db->join('ms_user pr_user', 'in_pr.user_created = pr_user.user_id', 'left');
                $this->db->where(array('in_po.po_id' => $data['row']->po_id));
                $qry_po = $this->db->get();
                $row_po = $qry_po->row();
                $data['po_user'] = $row_po->user_fullname;
                $data['po_date'] = ymd_to_dmy($row_po->po_date);
                $data['pr_user'] = $row_po->pr_created;
                $data['pr_date'] = ymd_to_dmy($row_po->pr_date);

                $this->load->view('inventory/grn/pdf_grn.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->grn_code . ".pdf", array('Attachment'=>0));
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

/* End of file grn.php */
/* Location: ./application/controllers/inventory/grn.php */