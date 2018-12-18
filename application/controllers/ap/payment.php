<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CI_Controller {

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
	
	public function index() {
		tpd_404();
	}

    public function payment_form($payment_id = 0){
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

        $data['payment_id'] = $payment_id;
        $data['qry_curr'] = $this->db->get('currencytype');

        if ($payment_id > 0) {
            $qry = $this->mdl_general->get('view_ap_payment_header', array('payment_id' => $payment_id));
            $data['row'] = $qry->row();

            $data['qry_detail'] = $this->mdl_general->get('view_ap_payment_detail', array('payment_id' => $payment_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ap/payment/payment_form', $data);
        $this->load->view('layout/footer');
    }
	
	public function payment_manage($type = 0, $payment_id = 0){
        if ($type == 0) {
            $this->payment_list(0);
        } else {
            $this->payment_form($payment_id);
        }
	}
	
    public function payment_history($type = 0, $payment_id = 0){
        if ($type == 0) {
            $this->payment_list(1);
        } else {
            $this->payment_form($payment_id);
        }
    }

    private function payment_list($type = 0) {
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
        $this->load->view('ap/payment/payment_list.php', $data);
        $this->load->view('layout/footer');
    }
	
    public function ajax_payment_list($menu_id = 0, $type = 0){
        //$type : 0 => manage, 1 => History

        if($type == 0){
            $where['status'] = STATUS_NEW;
        } else {
            $where['status <>'] = STATUS_NEW;
            $where['status !='] = FLAG_CASHBOOK;
        }

        $like = array();
        if(isset($_REQUEST['filter_payment_code'])){
            if($_REQUEST['filter_payment_code'] != ''){
                $like['payment_code'] = $_REQUEST['filter_payment_code'];
            }
        }
        if(isset($_REQUEST['filter_payment_date_from'])){
            if($_REQUEST['filter_payment_date_from'] != ''){
                $where['payment_date >='] = dmy_to_ymd($_REQUEST['filter_payment_date_from']);
            }
        }
        if(isset($_REQUEST['filter_payment_date_to'])){
            if($_REQUEST['filter_payment_date_to'] != ''){
                $where['payment_date <='] = dmy_to_ymd($_REQUEST['filter_payment_date_to']);
            }
        }
        if(isset($_REQUEST['filter_supplier'])){
            if($_REQUEST['filter_supplier'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }
        if(isset($_REQUEST['filter_bank_code'])){
            if($_REQUEST['filter_bank_code'] != ''){
                $like['bank_code'] = $_REQUEST['filter_bank_code'];
            }
        }
        if(isset($_REQUEST['filter_curr'])){
            if($_REQUEST['filter_curr'] != ''){
                $where['currencytype_id'] = $_REQUEST['filter_curr'];
            }
        }
        if(isset($_REQUEST['filter_amount'])){
            if($_REQUEST['filter_amount'] != ''){
                $like['total_amount'] = $_REQUEST['filter_amount'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['description'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $where['status'] = $_REQUEST['filter_status'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_ap_payment_header', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'payment_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'payment_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'payment_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'bank_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'currencytype_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'total_amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_ap_payment_header', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $btn_action = '';
            $btn_action .= '<li><a href="' . base_url('ap/payment/' . ($type == '0' ? 'payment_manage' : 'payment_history') . '/1/' . $row->payment_id) . '.tpd">View</a></li>';

            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_POSTED)){
                    /*$btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_POSTED . '" data-id="' . $row->payment_id . '" data-code="' . $row->payment_code . '" data-date="' . ymd_to_dmy($row->payment_date) . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';*/
					$btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_POSTED . '" data-id="' . $row->payment_id . '" data-code="' . $row->payment_code . '" data-date="' . ymd_to_dmy(date('d-m-Y')) . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CANCEL . '" data-id="' . $row->payment_id . '" data-code="' . $row->payment_code . '" data-date="' . ymd_to_dmy($row->payment_date) . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li><a href="' . base_url('ap/payment/pdf_payment/' . $row->payment_id) . '.tpd" target="_blank">Print</a></li>';
                }
            }
            if ($row->status == STATUS_POSTED) {
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li><a href="' . base_url('ap/payment/pdf_payment/' . $row->payment_id) . '.tpd" target="_blank">Print</a></li>';
                }
            }

            $records["data"][] = array(
                $i . '.',
                $row->payment_code,
                ymd_to_dmy($row->payment_date),
                $row->supplier_name,
                $row->bank_code,
                $row->currencytype_code,
                '<span class="mask_currency">' . $row->total_amount . '</span>',
                $row->description,
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

    public function ajax_modal_bank() {
        $this->load->view('ap/payment/ajax_modal_bank_list');
    }

    public function ajax_modal_bank_list($bankaccount_id = 0){
        $like = array();
        $where = array();

        $where['status'] = STATUS_NEW;

        $iTotalRecords =  $this->mdl_general->count('view_bank_account_detail', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'bank_code desc';

        $qry =  $this->mdl_general->get('view_bank_account_detail', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $text = 'Select';
            $attr = '';
            $attr .= ' data-id="' . $row->bankaccount_id . '" ';
            $attr .= ' data-code="' . $row->bank_code . '" ';
            $attr .= ' data-curr-id="' . $row->currencytype_id . '" ';
            $attr .= ' data-curr-code="' . $row->currencytype_code . '" ';
            if ($bankaccount_id == $row->bankaccount_id) {
                $attr .= 'selected="selected" disabled="disabled"';
                $text = 'Selected';
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-bank" ' . $attr . '><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $i . '.',
                $row->bank_code,
                $row->currencytype_code,
                $row->bankaccount_desc,
                $row->coa_code,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_payment_submit(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $data = array();

        if(isset($_POST)){
            $has_error = false;
            $this->db->trans_begin();
            $payment_id = $_POST['payment_id'];

            $data['payment_date'] = dmy_to_ymd(trim($_POST['payment_date']));
            $data['supplier_id'] = intval($_POST['supplier_id']);
            $data['bank_account_id'] = intval($_POST['bankaccount_id']);
            $data['ref_no'] = trim($_POST['ref_no']);
            $data['currencytype_id'] = intval($_POST['currencytype_id']);
            $data['curr_rate'] = trim($_POST['curr_rate']);
            $data['total_amount'] = trim($_POST['totalamount']);
            $data['description'] = trim($_POST['description']);

            if ($result['valid'] == '1') {
                if ($payment_id > 0) {
                    $qry = $this->db->get_where('ap_payment', array('payment_id' => $payment_id));
                    $row = $qry->row();

                    $arr_date = explode('-', $data['payment_date']);
                    $arr_date_old = explode('-', $row->payment_date);

                    if ($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]) {
                        $data['payment_code'] = $this->mdl_general->generate_code(Feature::FEATURE_AP_PAYMENT, $data['payment_date']);

                        if ($data['payment_code'] == '') {
                            $has_error = true;

                            $result['valid'] = '0';
                            $result['message'] = 'Failed generating code.';
                        }
                    }

                    if ($has_error == false) {
                        $data['user_modified'] = my_sess('user_id');
                        $data['date_modified'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('ap_payment', array('payment_id' => $payment_id), $data);

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Successfully update Payment.');
                    }
                } else {
                    $data['payment_code'] = $this->mdl_general->generate_code(Feature::FEATURE_AP_PAYMENT, $data['payment_date']);

                    if ($data['payment_code'] != '') {
                        $data['user_created'] = my_sess('user_id');
                        $data['date_created'] = date('Y-m-d H:i:s');
                        $data['status'] = STATUS_NEW;

                        $this->db->insert('ap_payment', $data);
                        $payment_id = $this->db->insert_id();

                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $payment_id;
                        $data_log['feature_id'] = Feature::FEATURE_AP_PAYMENT;
                        $data_log['log_subject'] = 'Create AP Payment (' . $data['payment_code'] . ')';
                        $data_log['action_type'] = STATUS_NEW;
                        $this->db->insert('app_log', $data_log);

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Successfully add Payment.');
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

                        $data_detail['payment_id'] = $payment_id;
                        $data_detail['inv_id'] = $_POST['inv_id'][$key];
                        $data_detail['amount'] = $_POST['amount'][$key];
                        $data_detail['local_amount'] = $_POST['local_amount'][$key];
                        $data_detail['tax_wht'] = $_POST['tax_wht'][$key];
                        $data_detail['inv_amount'] = $_POST['inv_amount'][$key];
                        $data_detail['inv_vat'] = $_POST['inv_vat'][$key];
                        $data_detail['curr_rate_wht'] = $_POST['curr_rate_wht'][$key];
                        $data_detail['taxtype_id'] = $_POST['taxtype_id'][$key];

                        if ($val > 0) {
                            $qry_det = $this->db->get_where('ap_payment_detail', array('detail_id' => $val));
                            $row_det = $qry_det->row();
                            $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $row_det->inv_id), array('is_process' => 0));

                            if($status == STATUS_NEW) {
                                $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $data_detail['inv_id']), array('is_process' => 1));

                                $this->mdl_general->update('ap_payment_detail', array('detail_id' => $val), $data_detail);
                            } else {
                                $this->db->delete('ap_payment_detail', array('detail_id' => $val));
                            }
                        } else {
                            if($status == STATUS_NEW) {
                                $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $data_detail['inv_id']), array('is_process' => 1));

                                $this->db->insert('ap_payment_detail', $data_detail);
                            }
                        }
                        $i++;
                    }
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'No Detail Payment.';
                }
            }

            if($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('ap/payment/payment_manage.tpd');
                    } else{
                        $result['link'] = base_url('ap/payment/payment_manage/1/' . $payment_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function ajax_payment_action(){
        $result = array();

        $this->db->trans_begin();

        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = '';

        $payment_id = $_POST['payment_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $payment_id;
        $data_log['feature_id'] = Feature::FEATURE_AP_PAYMENT;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($payment_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('ap_payment', array('payment_id' => $payment_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_POSTED) {
                    if ($row->status == STATUS_POSTED) {
                        $result['valid'] = '0';
                        $result['message'] = 'Payment already posted.';
                    } else {
                        //POSTING PAYMENT
                        $data['posting_date'] = dmy_to_ymd($_POST['action_date']);
                        $valid = $this->posting_payment($payment_id, $data['posting_date']);
                        $result['debug'] = $valid;

                        if ($valid['error'] == '0') {
                            $qry_detail = $this->mdl_general->get('view_ap_payment_detail', array('payment_id' => $payment_id));
                            foreach ($qry_detail->result() as $row_detail) {
                                $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $row_detail->inv_id), array('is_process' => 0));
                            }

                            $this->mdl_general->update('ap_payment', array('payment_id' => $payment_id), $data);

                            $data_log['log_subject'] = 'Posting AP Payment (' . $row->payment_code . ')';
                            $data_log['action_type'] = STATUS_POSTED;
                            $this->db->insert('app_log', $data_log);

                            $result['message'] = 'Successfully posting Payment.';
                        } else {
                            $result['valid'] = '0';
                            $result['message'] = $valid['message'];
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Payment already canceled.';
                    } else {
                        $qry_detail = $this->mdl_general->get('view_ap_payment_detail', array('payment_id' => $payment_id));
                        foreach ($qry_detail->result() as $row_detail) {
                            $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $row_detail->inv_id), array('is_process' => 0));
                        }

                        $this->mdl_general->update('ap_payment', array('payment_id' => $payment_id), $data);

                        $data_log['log_subject'] = 'Cancel AP Payment (' . $row->payment_code . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel Payment.';
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

    private function posting_payment($payment_id = 0, $date_posting = '', $is_view = false){
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($payment_id > 0) {
            $qry_hd = $this->mdl_general->get('view_ap_payment_header', array('payment_id' => $payment_id));
            if ($qry_hd->num_rows() > 0) {
                $row_hd = $qry_hd->row();

                $qry_tp = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_PAYMENT_ADV));
                if ($qry_tp->num_rows() > 0) {
                    $row_tp = $qry_tp->row();

                    $this->load->model('finance/mdl_finance');

                    $detail = array();
                    $update_inv = array();

                    $totalDebit = 0;
                    $totalCredit = 0;

                    $qryDetails = $this->mdl_general->get('view_ap_payment_detail', array('payment_id' => $payment_id));
                    if ($qryDetails->num_rows() > 0) {
                        foreach ($qryDetails->result() as $det) {
                            $paid_amount = $det->inv_paid_amount + $det->amount + $det->tax_wht;
                            if ($paid_amount > $det->inv_grand_total_amount_dn_cn) {
                                $result['error'] = '1';
                                $result['message'] = 'Payment amount is larger than Invoice Amount (' . $det->inv_code . ').';

                                break;
                            } else {
                                $update_inv[$det->inv_id] = array('paid_amount' => $paid_amount);

                                $totalDebit_detail = 0;
                                $totalCredit_detail = 0;

                                if ($row_tp->coa_id > 0) {
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $row_tp->coa_id;
                                    $rowdet['dept_id'] = 0;
                                    $rowdet['journal_note'] = $row_hd->description;
                                    $rowdet['journal_debit'] = (($det->amount + $det->tax_wht) * $det->curr_rate);
                                    $rowdet['journal_credit'] = 0;
                                    $rowdet['reference_id'] = $det->detail_id;
                                    $rowdet['transtype_id'] = $row_tp->transtype_id;

                                    array_push($detail, $rowdet);

                                    $totalDebit += $rowdet['journal_debit'];
                                    $totalCredit += $rowdet['journal_credit'];

                                    $totalDebit_detail += $rowdet['journal_debit'];
                                    $totalCredit_detail += $rowdet['journal_credit'];

                                    if ($det->curr_rate != $row_hd->curr_rate) {
                                        $qry_forex = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_FOREX_GAIN));
                                        if ($qry_forex->num_rows() > 0) {
                                            $row_forex = $qry_forex->row();
                                            $payment_amount = (($det->amount + $det->tax_wht) * $row_hd->curr_rate);

                                            if ($payment_amount > $totalDebit_detail) {
                                                $rowdet = array();
                                                $rowdet['coa_id'] = $row_forex->coa_id;
                                                $rowdet['dept_id'] = 0;
                                                $rowdet['journal_note'] = $row_hd->description;
                                                $rowdet['journal_debit'] = ($payment_amount - $totalDebit_detail);
                                                $rowdet['journal_credit'] = 0;
                                                $rowdet['reference_id'] = 0;
                                                $rowdet['transtype_id'] = $row_forex->transtype_id;

                                                array_push($detail, $rowdet);

                                                $totalDebit += $rowdet['journal_debit'];
                                                $totalCredit += $rowdet['journal_credit'];

                                                $totalDebit_detail += $rowdet['journal_debit'];
                                                $totalCredit_detail += $rowdet['journal_credit'];
                                            } else {
                                                $rowdet = array();
                                                $rowdet['coa_id'] = $row_forex->coa_id;
                                                $rowdet['dept_id'] = 0;
                                                $rowdet['journal_note'] = $row_hd->description;
                                                $rowdet['journal_debit'] = 0;
                                                $rowdet['journal_credit'] = ($totalDebit_detail - $payment_amount);
                                                $rowdet['reference_id'] = 0;
                                                $rowdet['transtype_id'] = $row_forex->transtype_id;

                                                array_push($detail, $rowdet);

                                                $totalDebit += $rowdet['journal_debit'];
                                                $totalCredit += $rowdet['journal_credit'];

                                                $totalDebit_detail += $rowdet['journal_debit'];
                                                $totalCredit_detail += $rowdet['journal_credit'];
                                            }
                                        } else {
                                            $result['error'] = '1';
                                            $result['message'] = 'Spec AP Forex Not Found.';

                                            break;
                                        }
                                    }

                                    if ($det->tax_wht > 0) {
                                        if ($det->coa_id_wht > 0) {
                                            $local_tax_wht = ($det->tax_wht * $det->curr_rate_wht);

                                            $rowdet = array();
                                            $rowdet['coa_id'] = $det->coa_id_wht;
                                            $rowdet['dept_id'] = 0;
                                            $rowdet['journal_note'] = $row_hd->description;
                                            $rowdet['journal_debit'] = 0;
                                            $rowdet['journal_credit'] = $local_tax_wht;
                                            $rowdet['reference_id'] = $det->detail_id;
                                            $rowdet['transtype_id'] = 0;

                                            array_push($detail, $rowdet);

                                            $totalDebit += $rowdet['journal_debit'];
                                            $totalCredit += $rowdet['journal_credit'];

                                            $totalDebit_detail += $rowdet['journal_debit'];
                                            $totalCredit_detail += $rowdet['journal_credit'];
                                        } else {
                                            $result['error'] = '1';
                                            $result['message'] = 'COA ID WHT is empty (' . $det->taxtype_code . ').';

                                            break;
                                        }
                                    }

                                    if ($row_hd->bank_coa_id > 0) {
                                        $rowdet = array();
                                        $rowdet['coa_id'] = $row_hd->bank_coa_id;
                                        $rowdet['dept_id'] = 0;
                                        $rowdet['journal_note'] = $row_hd->description;
                                        $rowdet['journal_debit'] = 0;
                                        $rowdet['journal_credit'] = $totalDebit_detail - $totalCredit_detail;
                                        $rowdet['reference_id'] = $row_hd->payment_id;
                                        $rowdet['transtype_id'] = 0;

                                        array_push($detail, $rowdet);

                                        $totalDebit += $rowdet['journal_debit'];
                                        $totalCredit += $rowdet['journal_credit'];

                                        $totalDebit_detail += $rowdet['journal_debit'];
                                        $totalCredit_detail += $rowdet['journal_credit'];
                                    } else {
                                        $result['error'] = '1';
                                        $result['message'] = 'Bank COA ID is empty (' . $row_hd->bankaccount_desc . ').';

                                        break;
                                    }
                                } else {
                                    $result['error'] = '1';
                                    $result['message'] = 'COA ID is empty.';

                                    break;
                                }
                            }
                        }
                    }

                    if ($result['error'] == '0') {
                        if ($totalDebit == $totalCredit) {
                            $header = array();
                            $header['journal_no'] = $row_hd->payment_code;
                            $header['journal_date'] = $date_posting;
                            $header['journal_remarks'] = $row_hd->description;
                            $header['modul'] = GLMOD::GL_MOD_AP;
                            $header['journal_amount'] = $totalDebit;
                            $header['reference'] = strval($row_hd->payment_id);

                            if ($is_view) {
                                $result['journal_detail'] = $detail;
                            } else {
                                $valid = $this->mdl_finance->postJournal($header, $detail);

                                if ($valid) {
                                    $this->update_invoice($update_inv);
                                } else {
                                    $result['error'] = '1';
                                    $result['message'] = 'Failed insert journal.';
                                }
                            }
                        }
                    }
                } else {
                    $result['error'] = '1';
                    $result['message'] = 'Spec AP Payment Not Found.';
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'Payment not found.';
            }
        }

        return $result;
    }

    private function update_invoice ($detail = array()) {
        if (count($detail) > 0) {
            foreach ($detail as $key => $val) {
                if ($key > 0) {
                    $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $key), $val);
                }
            }
        }
    }

    public function pdf_payment($payment_id = 0) {
        if ($payment_id > 0) {
            $qry = $this->db->get_where('view_ap_payment_header', array('payment_id' => $payment_id));
            if ($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_det'] =  $this->db->get_where('view_ap_payment_detail', array('payment_id' => $payment_id));

                $data['qry_journal'] =  $this->db->query("SELECT * FROM fxn_getjournal_from_ap_payment(" . $payment_id . ")");

                if ($data['row']->status == STATUS_NEW) {
                    $journal = $this->posting_payment($data['row']->payment_id, $data['row']->payment_date, true);

                    $data['journal'] = $journal['journal_detail'];
                }

                $this->load->view('ap/payment/pdf_payment.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->payment_code . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }
}

/* End of file payment.php */
/* Location: ./application/controllers/AP/payment.php */
	