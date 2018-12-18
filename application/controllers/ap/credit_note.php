<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit_Note extends CI_Controller {

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

    public function cn_form($creditnote_id = 0){
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

        $data['creditnote_id'] = $creditnote_id;
        $data['qry_curr'] = $this->db->get('currencytype');

        $data['qry_dept'] = $this->mdl_general->get('ms_department', array('status' => STATUS_NEW), array(), 'department_name ASC');

        if ($creditnote_id > 0) {
            $qry = $this->mdl_general->get('view_ap_cn_header', array('creditnote_id' => $creditnote_id));
            $data['row'] = $qry->row();

            $data['qry_detail'] = $this->mdl_general->get('view_ap_cn_detail', array('creditnote_id' => $creditnote_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ap/credit_note/cn_form', $data);
        $this->load->view('layout/footer');
    }
	
	public function cn_manage($type = 0, $creditnote_id = 0){
        if ($type == 0) {
            $this->cn_list(0);
        } else {
            $this->cn_form($creditnote_id);
        }
	}
	
    public function cn_history($type = 0, $creditnote_id = 0){
        if ($type == 0) {
            $this->cn_list(1);
        } else {
            $this->cn_form($creditnote_id);
        }
    }

    private function cn_list($type = 0) {
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
        $this->load->view('ap/credit_note/cn_list.php', $data);
        $this->load->view('layout/footer');
    }
	
    public function ajax_cn_list($menu_id = 0, $type = 0){
        //$type : 0 => manage, 1 => History

        if($type == 0){
            $where['status'] = STATUS_NEW;
        } else {
            $where['status <>'] = STATUS_NEW;
        }

        $like = array();
        if(isset($_REQUEST['filter_cn_code'])){
            if($_REQUEST['filter_cn_code'] != ''){
                $like['creditnote_code'] = $_REQUEST['filter_cn_code'];
            }
        }
        if(isset($_REQUEST['filter_cn_date_from'])){
            if($_REQUEST['filter_cn_date_from'] != ''){
                $where['creditnote_date >='] = dmy_to_ymd($_REQUEST['filter_cn_date_from']);
            }
        }
        if(isset($_REQUEST['filter_cn_date_to'])){
            if($_REQUEST['filter_cn_date_to'] != ''){
                $where['creditnote_date <='] = dmy_to_ymd($_REQUEST['filter_cn_date_to']);
            }
        }
        if(isset($_REQUEST['filter_supplier'])){
            if($_REQUEST['filter_supplier'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }
        if(isset($_REQUEST['filter_inv_code'])){
            if($_REQUEST['filter_inv_code'] != ''){
                $like['inv_code'] = $_REQUEST['filter_inv_code'];
            }
        }
        if(isset($_REQUEST['filter_curr'])){
            if($_REQUEST['filter_curr'] != ''){
                $where['currencytype_id'] = $_REQUEST['filter_curr'];
            }
        }
        if(isset($_REQUEST['filter_amount'])){
            if($_REQUEST['filter_amount'] != ''){
                $like['amount'] = $_REQUEST['filter_amount'];
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

        $iTotalRecords = $this->mdl_general->count('view_ap_cn_header', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'creditnote_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'creditnote_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'creditnote_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'inv_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'currencytype_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_ap_cn_header', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $btn_action = '';
            $btn_action .= '<li><a href="' . base_url('ap/credit_note/' . ($type == '0' ? 'cn_manage' : 'cn_history') . '/1/' . $row->creditnote_id) . '.tpd">View</a></li>';

            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_POSTED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_POSTED . '" data-id="' . $row->creditnote_id . '" data-code="' . $row->creditnote_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CANCEL . '" data-id="' . $row->creditnote_id . '" data-code="' . $row->creditnote_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            }
            if ($row->status == STATUS_POSTED) {
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li><a href="' . base_url('ap/credit_note/pdf_cn/' . $row->creditnote_id) . '.tpd" target="_blank">Print</a></li>';
                    $btn_action .= '<li><a href="' . base_url('ap/credit_note/pdf_jv_cn/' . $row->creditnote_id) . '.tpd" target="_blank">Print JV</a></li>';
                }
            }

            $records["data"][] = array(
                $i . '.',
                $row->creditnote_code,
                ymd_to_dmy($row->creditnote_date),
                $row->supplier_name,
                $row->inv_code,
                $row->currencytype_code,
                '<span class="mask_currency">' . $row->amount . '</span>',
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

    public function ajax_cn_submit(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $data = array();

        if(isset($_POST)){
            $has_error = false;
            $this->db->trans_begin();
            $creditnote_id = $_POST['creditnote_id'];

            $data['creditnote_date'] = dmy_to_ymd(trim($_POST['creditnote_date']));
            $data['supplier_id'] = intval($_POST['supplier_id']);
            $data['inv_id'] = intval($_POST['inv_id']);
            $data['ref_no'] = trim($_POST['ref_no']);
            $data['currencytype_id'] = intval($_POST['currencytype_id']);
            $data['curr_rate'] = trim($_POST['curr_rate']);
            $data['amount'] = trim($_POST['totalamount']);
            $data['remarks'] = trim($_POST['remarks']);

            $qry_inv = $this->db->get_where('ap_invoiceheader', array('inv_id' => $data['inv_id']));
            $row_inv = $qry_inv->row();
            $qry_curr_inv = $this->db->get_where('currencytype', array('currencytype_id' => $row_inv->currencytype_id));
            $row_curr_inv = $qry_curr_inv->row();

            if ($data['currencytype_id'] != $row_inv->currencytype_id) {
                $has_error = true;

                $result['valid'] = '0';
                $result['message'] = 'Invoice ' . $row_inv->inv_code . ' in currency ' . $row_curr_inv->currencytype_code . ', Credit Note need same currency.';
            } else {
                if ($row_curr_inv->currencytype_code != Purchasing::CURR_IDR) {
                    if ($data['curr_rate'] <= 1) {
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'Invoice ' . $row_inv->inv_code . ' in currency ' . $row_curr_inv->currencytype_code . ', Credit Note need currency rate.';
                    }
                }
            }

            if ($result['valid'] == '1') {
                if ($creditnote_id > 0) {
                    $qry = $this->db->get_where('ap_creditnote', array('creditnote_id' => $creditnote_id));
                    $row = $qry->row();

                    $arr_date = explode('-', $data['creditnote_date']);
                    $arr_date_old = explode('-', $row->creditnote_date);

                    if ($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]) {
                        $data['creditnote_code'] = $this->mdl_general->generate_code(Feature::FEATURE_AP_CREDIT_NOTE, $data['creditnote_date']);

                        if ($data['creditnote_code'] == '') {
                            $has_error = true;

                            $result['valid'] = '0';
                            $result['message'] = 'Failed generating code.';
                        }
                    }

                    if ($has_error == false) {
                        $data['user_modified'] = my_sess('user_id');
                        $data['date_modified'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $row->inv_id), array('is_process' => 0));

                        $this->mdl_general->update('ap_creditnote', array('creditnote_id' => $creditnote_id), $data);

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Successfully update Credit Note.');
                    }
                } else {
                    $data['creditnote_code'] = $this->mdl_general->generate_code(Feature::FEATURE_AP_CREDIT_NOTE, $data['creditnote_date']);

                    if ($data['creditnote_code'] != '') {
                        $data['user_created'] = my_sess('user_id');
                        $data['date_created'] = date('Y-m-d H:i:s');
                        $data['status'] = STATUS_NEW;

                        $this->db->insert('ap_creditnote', $data);
                        $creditnote_id = $this->db->insert_id();

                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $creditnote_id;
                        $data_log['feature_id'] = Feature::FEATURE_AP_CREDIT_NOTE;
                        $data_log['log_subject'] = 'Create AP Credit Note (' . $data['creditnote_code'] . ')';
                        $data_log['action_type'] = STATUS_NEW;
                        $this->db->insert('app_log', $data_log);

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Successfully add Credit Note.');
                    } else {
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }
            }

            if($has_error == false) {
                $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $data['inv_id']), array('is_process' => 1));

                if (isset($_POST['detail_id'])) {
                    $i = 0;
                    foreach ($_POST['detail_id'] as $key => $val) {
                        $data_detail = array();

                        $status = $_POST['status'][$key];

                        $data_detail['creditnote_id'] = $creditnote_id;
                        $data_detail['coa_id'] = $_POST['coa_id'][$key];
                        $data_detail['dept_id'] = $_POST['dept_id'][$key];
                        $data_detail['notes'] = $_POST['notes'][$key];
                        $data_detail['amount'] = $_POST['amount'][$key];
                        $data_detail['local_amount'] = $_POST['local_amount'][$key];

                        if ($val > 0) {
                            if($status == STATUS_NEW) {
                                $this->mdl_general->update('ap_creditnote_detail', array('detail_id' => $val), $data_detail);
                            } else {
                                $this->db->delete('ap_creditnote_detail', array('detail_id' => $val));
                            }
                        } else {
                            if($status == STATUS_NEW) {
                                $this->db->insert('ap_creditnote_detail', $data_detail);
                            }
                        }
                        $i++;
                    }
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'No Detail Credit Note.';
                }
            }

            if($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('ap/credit_note/cn_manage.tpd');
                    } else{
                        $result['link'] = base_url('ap/credit_note/cn_manage/1/' . $creditnote_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function ajax_cn_action(){
        $result = array();

        $this->db->trans_begin();

        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = '';

        $creditnote_id = $_POST['creditnote_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $creditnote_id;
        $data_log['feature_id'] = Feature::FEATURE_AP_CREDIT_NOTE;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($creditnote_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('ap_creditnote', array('creditnote_id' => $creditnote_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_POSTED) {
                    if ($row->status == STATUS_POSTED) {
                        $result['valid'] = '0';
                        $result['message'] = 'Credit Note already posted.';
                    } else {
                        //POSTING INVOICE
                        $valid = $this->posting_cn($creditnote_id);
                        $result['debug'] = $valid;

                        if ($valid['error'] == '0') {
                            $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $row->inv_id), array('is_process' => 0));

                            $data['date_posted'] = date('Y-m-d H:i:s');
                            $this->mdl_general->update('ap_creditnote', array('creditnote_id' => $creditnote_id), $data);

                            $data_log['log_subject'] = 'Posting AP Credit Note (' . $row->creditnote_code . ')';
                            $data_log['action_type'] = STATUS_POSTED;
                            $this->db->insert('app_log', $data_log);

                            $result['message'] = 'Successfully posting Credit Note.';
                        } else {
                            $result['valid'] = '0';
                            $result['message'] = $valid['message'];
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Credit Note already canceled.';
                    } else {
                        $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $row->inv_id), array('is_process' => 0));

                        $this->mdl_general->update('ap_creditnote', array('creditnote_id' => $creditnote_id), $data);

                        $data_log['log_subject'] = 'Cancel AP Credit Note (' . $row->creditnote_code . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel Credit Note.';
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

    private function posting_cn($creditnote_id = 0){
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($creditnote_id > 0) {
            $qry_hd = $this->mdl_general->get('view_ap_cn_header', array('creditnote_id' => $creditnote_id));
            if ($qry_hd->num_rows() > 0) {
                $row_hd = $qry_hd->row();

                $this->load->model('finance/mdl_finance');

                $detail = array();

                $totalDebit = 0;
                $totalCredit = 0;
                $qryDetails = $this->mdl_general->get('view_ap_cn_detail', array('creditnote_id' => $creditnote_id));
                if ($qryDetails->num_rows() > 0) {
                    foreach ($qryDetails->result() as $det) {
                        if ($det->coa_id > 0) {
                            $rowdet = array();
                            $rowdet['coa_id'] = $det->coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $det->coa_desc;
                            $rowdet['journal_debit'] = $det->local_amount;
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = $det->detail_id;
                            $rowdet['transtype_id'] = 0;

                            $totalDebit += $rowdet['journal_debit'];
                            $totalCredit += $rowdet['journal_credit'];

                            array_push($detail, $rowdet);
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'COA ID is empty.';

                            break;
                        }
                    }
                }

                if ($result['error'] == '0') {
                    $qry_key = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_CREDIT_NOTE));
                    if ($qry_key->num_rows() > 0) {
                        $row_key = $qry_key->row();

                        if ($row_key->coa_id > 0) {
                            $rowdet = array();
                            $rowdet['coa_id'] = $row_key->coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $row_hd->remarks;
                            $rowdet['journal_debit'] = 0;
                            $rowdet['journal_credit'] = $totalDebit;
                            $rowdet['reference_id'] = $row_hd->creditnote_id;
                            $rowdet['transtype_id'] = $row_key->transtype_id;

                            $totalDebit += $rowdet['journal_debit'];
                            $totalCredit += $rowdet['journal_credit'];

                            array_push($detail, $rowdet);
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Spec Credit Note is empty.';
                        }
                    } else {
                        $result['error'] = '1';
                        $result['message'] = 'Spec Credit Note not found.';
                    }
                }

                if ($result['error'] == '0') {
                    if ($totalDebit == $totalCredit) {
                        $header = array();
                        $header['journal_no'] = $row_hd->creditnote_code;
                        $header['journal_date'] = $row_hd->creditnote_date;
                        $header['journal_remarks'] = $row_hd->remarks;
                        $header['modul'] = GLMOD::GL_MOD_AP;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = strval($row_hd->creditnote_id);

                        $valid = $this->mdl_finance->postJournal($header, $detail);

                        if ($valid == false) {
                            $result['error'] = '1';
                            $result['message'] = 'Failed insert journal.';
                        }
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'Credit Note not found.';
            }
        }

        return $result;
    }

    public function pdf_cn($creditnote_id = 0) {
        if ($creditnote_id > 0) {
            $qry = $this->db->get_where('view_ap_cn_header', array('creditnote_id' => $creditnote_id));
            if ($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_det'] =  $this->db->get_where('view_ap_cn_detail', array('creditnote_id' => $creditnote_id));

                $this->load->view('ap/credit_note/pdf_cn.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->creditnote_code . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

    public function pdf_jv_cn($creditnote_id = 0) {
        if ($creditnote_id > 0) {
            $qry = $this->db->get_where('view_ap_cn_header', array('creditnote_id' => $creditnote_id));
            if ($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_det'] =  $this->db->get_where('view_get_journal_detail', array('journal_no' => $data['row']->creditnote_code));

                $this->load->view('ap/credit_note/pdf_jv_cn.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->creditnote_code . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

}

/* End of file credit_note.php */
/* Location: ./application/controllers/AP/credit_note.php */
	