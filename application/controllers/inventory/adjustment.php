<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adjustment extends CI_Controller {

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

    public function adj_form($adj_id = 0){
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

        $data['adj_id'] = $adj_id;

        if ($adj_id > 0) {
            $qry = $this->mdl_general->get('in_adjustment', array('adj_id' => $adj_id));
            $data['row'] = $qry->row();

            $data['qry_detail'] = $this->mdl_general->get('view_in_adjustment_detail', array('adj_id' => $adj_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('inventory/adjustment/adj_form', $data);
        $this->load->view('layout/footer');
    }

    public function adj_manage($type = 0, $adj_id = 0){
        if ($type == 0) {
            $this->adj_list(0);
        } else {
            $this->adj_form($adj_id);
        }
    }

    public function adj_history($type = 0, $adj_id = 0){
        if ($type == 0) {
            $this->adj_list(1);
        } else {
            $this->adj_form($adj_id);
        }
    }

    private function adj_list($type = 0) {
        /// 0 => Manage
        /// 1 => History

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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();
        $data['type'] = $type;

        $this->load->view('layout/header', $data_header);
        $this->load->view('inventory/adjustment/adj_list.php', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_adj_list($menu_id = 0, $type = 0){
        //$type : 0 => manage, 1 => History

        if($type == 0){
            $where['status'] = STATUS_NEW;
        } else {
            $where['status <>'] = STATUS_NEW;
        }

        $like = array();
        if(isset($_REQUEST['filter_adj_code'])){
            if($_REQUEST['filter_adj_code'] != ''){
                $like['adj_code'] = $_REQUEST['filter_adj_code'];
            }
        }
        if(isset($_REQUEST['filter_adj_date_from'])){
            if($_REQUEST['filter_adj_date_from'] != ''){
                $where['adj_date >='] = dmy_to_ymd($_REQUEST['filter_adj_date_from']);
            }
        }
        if(isset($_REQUEST['filter_adj_date_to'])){
            if($_REQUEST['filter_adj_date_to'] != ''){
                $where['adj_date <='] = dmy_to_ymd($_REQUEST['filter_adj_date_to']);
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $where['status'] = $_REQUEST['filter_status'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('in_adjustment', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'adj_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'adj_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'adj_date ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('in_adjustment', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $btn_action = '';
            $btn_action .= '<li><a href="' . base_url('inventory/adjustment/' . ($type == '0' ? 'adj_manage' : 'adj_history') . '/1/' . $row->adj_id) . '.tpd">View</a></li>';

            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_POSTED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_POSTED . '" data-id="' . $row->adj_id . '" data-code="' . $row->adj_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CANCEL . '" data-id="' . $row->adj_id . '" data-code="' . $row->adj_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li><a href="' . base_url('inventory/adjustment/pdf_adj/' . $row->adj_id) . '.tpd" target="_blank">Print</a></li>';
                }
            }
            if ($row->status == STATUS_POSTED) {
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li><a href="' . base_url('inventory/adjustment/pdf_adj/' . $row->adj_id) . '.tpd" target="_blank">Print</a></li>';
                }
            }

            $records["data"][] = array(
                $i . '.',
                $row->adj_code,
                ymd_to_dmy($row->adj_date),
                $row->remarks,
                get_status_name($row->status),
                '<div class="btn-group">
					<button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false" style="margin-right: 0px;">
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

    public function ajax_ms_item(){
        $this->load->view('inventory/adjustment/ajax_ms_item_list');
    }

    public function ajax_ms_item_list($exist_item_id = '-', $num_index = 0){
        $this->load->model('inventory/mdl_request');

        $where = array();
        $like = array();

        $exist_item_id = trim($exist_item_id);
        $isexist = false;
        if($exist_item_id != '-' && $exist_item_id != '0'){
            $isexist = true;
            $arr_id = explode('_', $exist_item_id);
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
        if(isset($_REQUEST['filter_uom_out_code'])){
            if($_REQUEST['filter_uom_out_code'] != ''){
                $like['uom_out_code'] = $_REQUEST['filter_uom_out_code'];
            }
        }
        if(isset($_REQUEST['filter_on_hand_qty'])){
            if($_REQUEST['filter_on_hand_qty'] != ''){
                $like['on_hand_qty'] = $_REQUEST['filter_on_hand_qty'];
            }
        }
        if(isset($_REQUEST['filter_unit_cost'])){
            if($_REQUEST['filter_unit_cost'] != ''){
                $like['unit_cost'] = $_REQUEST['filter_unit_cost'];
            }
        }

        $iTotalRecords = $this->mdl_request->get_item(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'item_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'uom_out_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'on_hand_qty ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'unit_cost ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_request->get_item(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $text = 'Select';
            $attr = '';
            $attr .= ' data-id="' . $row->item_id . '" ';
            $attr .= ' data-index="' . $num_index . '" ';
            $attr .= ' data-code="' . $row->item_code . '" ';
            $attr .= ' data-desc="' . $row->item_desc . '" ';
            $attr .= ' data-uom="' . $row->uom_out_code . '" ';
            $attr .= ' data-qty="' . $row->on_hand_qty . '" ';
            $attr .= ' data-price="' . $row->unit_cost . '" ';
            if ($row->is_process == 1) {
                $attr .= ' disabled="disabled" ';
                $text = 'In Process';
            }
            if ($isexist) {
                foreach ($arr_id as $key => $val) {
                    if ($val == $row->item_id) {
                        $attr .= ' disabled="disabled" ';
                        $text = 'Selected';
                    }
                }
            }


            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-item" ' . $attr . '><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $i . '.',
                $row->item_code,
                $row->item_desc,
                $row->uom_out_code,
                '<span class="mask_currency">' . $row->on_hand_qty . '</span>',
                '<span class="mask_currency">' . $row->unit_cost . '</span>',
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_adj_submit(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $data = array();

        if(isset($_POST)){
            $has_error = false;
            $this->db->trans_begin();
            $adj_id = $_POST['adj_id'];

            $data['adj_date'] = dmy_to_ymd(trim($_POST['adj_date']));
            $data['remarks'] = trim($_POST['remarks']);

            if ($result['valid'] == '1') {
                if ($adj_id > 0) {
                    $qry = $this->db->get_where('in_adjustment', array('adj_id' => $adj_id));
                    $row = $qry->row();

                    $arr_date = explode('-', $data['adj_date']);
                    $arr_date_old = explode('-', $row->adj_date);

                    if ($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]) {
                        $data['adj_code'] = $this->mdl_general->generate_code(Feature::FEATURE_STOCK_ADJUSTMENT, $data['adj_date']);

                        if ($data['adj_code'] == '') {
                            $has_error = true;

                            $result['valid'] = '0';
                            $result['message'] = 'Failed generating code.';
                        }
                    }

                    if ($has_error == false) {
                        $data['user_modified'] = my_sess('user_id');
                        $data['date_modified'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('in_adjustment', array('adj_id' => $adj_id), $data);

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Successfully update Stock Adjsutment.');
                    }
                } else {
                    $data['adj_code'] = $this->mdl_general->generate_code(Feature::FEATURE_STOCK_ADJUSTMENT, $data['adj_date']);

                    if ($data['adj_code'] != '') {
                        $data['user_created'] = my_sess('user_id');
                        $data['date_created'] = date('Y-m-d H:i:s');
                        $data['status'] = STATUS_NEW;

                        $this->db->insert('in_adjustment', $data);
                        $adj_id = $this->db->insert_id();

                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $adj_id;
                        $data_log['feature_id'] = Feature::FEATURE_STOCK_ADJUSTMENT;
                        $data_log['log_subject'] = 'Create Stock Adjustment (' . $data['adj_code'] . ')';
                        $data_log['action_type'] = STATUS_NEW;
                        $this->db->insert('app_log', $data_log);

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Successfully add Stock Adjustment.');
                    } else {
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }
            }

            if($has_error == false) {
                if (isset($_POST['detail_id'])) {
                    $i = 0;
                    foreach ($_POST['detail_id'] as $key => $val) {
                        $data_detail = array();

                        $status = $_POST['status'][$key];

                        $data_detail['adj_id'] = $adj_id;
                        $data_detail['item_id'] = $_POST['item_id'][$key];
                        $data_detail['qty'] = $_POST['qty'][$key];
                        $data_detail['price'] = $_POST['price'][$key];
                        $data_detail['adj_qty'] = $_POST['adj_qty'][$key];
                        $data_detail['adj_price'] = $_POST['adj_price'][$key];

                        if ($val > 0) {
                            $qry_det = $this->db->get_where('in_adjustment_detail', array('detail_id' => $val));
                            $row_det = $qry_det->row();
                            $this->mdl_general->update('in_ms_item', array('item_id' => $row_det->item_id), array('is_process' => 0));

                            if($status == STATUS_NEW) {
                                $this->mdl_general->update('in_ms_item', array('item_id' => $data_detail['item_id']), array('is_process' => 1));

                                $this->mdl_general->update('in_adjustment_detail', array('detail_id' => $val), $data_detail);
                            } else {
                                $this->db->delete('in_adjustment_detail', array('detail_id' => $val));
                            }
                        } else {
                            if($status == STATUS_NEW) {
                                $this->mdl_general->update('in_ms_item', array('item_id' => $data_detail['item_id']), array('is_process' => 1));

                                $this->db->insert('in_adjustment_detail', $data_detail);
                            }
                        }
                        $i++;
                    }
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'No Detail Stock Adjustment.';
                }
            }

            if($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    if (isset($_POST['save_close'])) {
                        $result['link'] = base_url('inventory/adjustment/adj_manage.tpd');
                    } else{
                        $result['link'] = base_url('inventory/adjustment/adj_manage/1/' . $adj_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function ajax_adj_action(){
        $result = array();

        $this->db->trans_begin();

        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = '';

        $adj_id = $_POST['adj_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $adj_id;
        $data_log['feature_id'] = Feature::FEATURE_STOCK_ADJUSTMENT;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($adj_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('in_adjustment', array('adj_id' => $adj_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_POSTED) {
                    if ($row->status == STATUS_POSTED) {
                        $result['valid'] = '0';
                        $result['message'] = 'Stock Adjustment already posted.';
                    } else {
                        if ($row->adj_date != date('Y-m-d')) {
                            $result['valid'] = '0';
                            $result['message'] = 'Adjustment date must be same with posting date.';
                        } else {
                            //POSTING ADJ
                            $valid = $this->posting_adj($adj_id);
                            $result['debug'] = $valid;

                            if ($valid['error'] == '0') {
                                $qry_detail = $this->mdl_general->get('in_adjustment_detail', array('adj_id' => $adj_id));
                                foreach ($qry_detail->result() as $row_detail) {
                                    $this->mdl_general->update('in_ms_item', array('item_id' => $row_detail->item_id), array('is_process' => 0));
                                }

                                $this->mdl_general->update('in_adjustment', array('adj_id' => $adj_id), $data);

                                $data_log['log_subject'] = 'Posting Stock Adjustment (' . $row->adj_code . ')';
                                $data_log['action_type'] = STATUS_POSTED;
                                $this->db->insert('app_log', $data_log);

                                $result['message'] = 'Successfully posting Stock Adjustment.';
                            } else {
                                $result['valid'] = '0';
                                $result['message'] = $valid['message'];
                            }
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Stock Adjustment already canceled.';
                    } else {
                        $qry_detail = $this->mdl_general->get('in_adjustment_detail', array('adj_id' => $adj_id));
                        foreach ($qry_detail->result() as $row_detail) {
                            $this->mdl_general->update('in_ms_item', array('item_id' => $row_detail->item_id), array('is_process' => 0));
                        }

                        $this->mdl_general->update('in_adjustment', array('adj_id' => $adj_id), $data);

                        $data_log['log_subject'] = 'Cancel Stock Adjustment (' . $row->adj_code . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel Stock Adjustment.';
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

    private function posting_adj($adj_id = 0){
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($adj_id > 0) {
            $qry_hd = $this->mdl_general->get('in_adjustment', array('adj_id' => $adj_id));
            if ($qry_hd->num_rows() > 0) {
                $row_hd = $qry_hd->row();

                $this->load->model('finance/mdl_finance');

                $detail = array();
                $stock = array();

                $totalDebit = 0;
                $totalCredit = 0;

                $totalDebit_det = 0;
                $totalCredit_det = 0;

                $qryDetails = $this->mdl_general->get('view_in_adjustment_detail', array('adj_id' => $adj_id));
                if ($qryDetails->num_rows() > 0) {
                    foreach ($qryDetails->result() as $det) {
                        if ($det->account_coa_id > 0) {
                            $total_stock = $det->qty * $det->price;
                            $total_adj = $det->adj_qty * $det->adj_price;

                            if ($total_stock != $total_adj) {
                                $rowdet = array();
                                $rowdet['coa_id'] = $det->account_coa_id;
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $det->item_desc;
                                if ($total_adj > $total_stock) {
                                    $rowdet['journal_debit'] = $total_adj - $total_stock;
                                    $rowdet['journal_credit'] = 0;
                                } else {
                                    $rowdet['journal_debit'] = 0;
                                    $rowdet['journal_credit'] = $total_stock - $total_adj;
                                }
                                $rowdet['reference_id'] = $det->detail_id;
                                $rowdet['transtype_id'] = 0;

                                array_push($detail, $rowdet);

                                $totalDebit += $rowdet['journal_debit'];
                                $totalCredit += $rowdet['journal_credit'];

                                $totalDebit_det += $rowdet['journal_debit'];
                                $totalCredit_det += $rowdet['journal_credit'];
                            }

                            $add_stock = array();
                            $add_stock['item_id']           = $det->item_id;
                            $add_stock['doc_qty']           = $det->adj_qty - $det->qty;
                            $add_stock['price']             = $det->adj_price - $det->price;
                            $add_stock['total_price']       = $total_adj - $total_stock;
                            $add_stock['stock_qty']         = $det->adj_qty;
                            $add_stock['avg_price']         = $det->adj_price;
                            $add_stock['total_avg_price']   = $det->adj_price * $det->adj_qty;
                            $add_stock['doc_id']            = $det->detail_id;
                            $add_stock['doc_type']          = Feature::FEATURE_STOCK_ADJUSTMENT;
                            $add_stock['created_date']      = date('Y-m-d H:i:s');
                            $add_stock['status']            = STATUS_NEW;

                            array_push($stock, $add_stock);

                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'COA ID not found for ' . $det->item_desc . ' (' . $det->item_code . ').';

                            break;
                        }
                    }
                }

                if ($result['error'] == '0') {
                    if ($totalDebit_det > 0 || $totalCredit_det > 0) {
                        $qry_trx = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_STOCK_ADJUSTMENT));
                        if ($qry_trx->num_rows() > 0) {
                            $row_trx = $qry_trx->row();

                            if ($row_trx->coa_id > 0) {
                                if ($totalDebit_det > 0) {
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $row_trx->coa_id;
                                    $rowdet['dept_id'] = 0;
                                    $rowdet['journal_note'] = $row_hd->remarks;
                                    $rowdet['journal_debit'] = 0;
                                    $rowdet['journal_credit'] = $totalDebit_det;
                                    $rowdet['reference_id'] = $row_hd->adj_id;
                                    $rowdet['transtype_id'] = $row_trx->transtype_id;

                                    array_push($detail, $rowdet);

                                    $totalDebit += $rowdet['journal_debit'];
                                    $totalCredit += $rowdet['journal_credit'];
                                }

                                if ($totalCredit_det > 0) {
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $row_trx->coa_id;
                                    $rowdet['dept_id'] = 0;
                                    $rowdet['journal_note'] = $row_hd->remarks;
                                    $rowdet['journal_debit'] = $totalCredit_det;
                                    $rowdet['journal_credit'] = 0;
                                    $rowdet['reference_id'] = $row_hd->adj_id;
                                    $rowdet['transtype_id'] = $row_trx->transtype_id;

                                    array_push($detail, $rowdet);

                                    $totalDebit += $rowdet['journal_debit'];
                                    $totalCredit += $rowdet['journal_credit'];
                                }
                            } else {
                                $result['error'] = '1';
                                $result['message'] = 'Spec AP Stock Adjustment COA ID is empty.';
                            }
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Spec AP Stock Adjustment Not Found.';
                        }
                    }
                }

                if ($result['error'] == '0') {
                    if ($totalDebit == $totalCredit) {
                        $header = array();
                        $header['journal_no'] = $row_hd->adj_code;
                        $header['journal_date'] = $row_hd->adj_date;
                        $header['journal_remarks'] = $row_hd->remarks;
                        $header['modul'] = GLMOD::GL_MOD_AP;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = strval($row_hd->adj_id);

                        if ($totalDebit > 0) {
                            $valid = $this->mdl_finance->postJournal($header, $detail);
                        } else {
                            $valid = true;
                        }

                        if ($valid) {
                            $this->update_stock($stock);
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Failed insert journal.';
                        }
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'Stock Adjustment not found.';
            }
        }

        return $result;
    }

    private function update_stock ($detail = array()) {
        if (count($detail) > 0) {
            foreach ($detail as $val) {
                $this->db->insert('in_ms_item_stock', $val);
            }
        }
    }

    public function pdf_adj($adj_id = 0) {
        if ($adj_id > 0) {
            $qry = $this->db->get_where('view_in_adjustment', array('adj_id' => $adj_id));
            if ($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_det'] =  $this->db->get_where('view_in_adjustment_detail', array('adj_id' => $adj_id));

                $this->load->view('inventory/adjustment/pdf_adj.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->adj_code . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

}

/* End of file adjustment.php */
/* Location: ./application/controllers/inventory/adjustment.php */