<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Refund extends CI_Controller {

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
		$this->refund_manage();
	}

    #region Refund Manage

    public function refund_manage($type = 1, $refund_id = 0){
        //$type = 1 => manage
        //$type = 2 => history
        //$type = 3 => form

        if ($type == 1 || $type == 2) {
            $this->load->model('finance/mdl_finance');

            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $data['is_history'] = ($type == 2 ? true : false);
            $this->load->view('layout/header', $data_header);
            $this->load->view('ar/refund/refund_manage.php', $data);
            $this->load->view('layout/footer');
        } else if ($type == 3) {
            $this->refund_form($refund_id);
        }
    }

    public function get_refund_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_receiptrefund_header.status'] = STATUS_NEW;
        $where['ar_receiptrefund_header.receipt_id <='] = 0;
        $where['ar_receiptrefund_header.reservation_id > '] = 0;
        //if($is_history)
        //    $where['ar_receiptrefund_header.status'] = STATUS_CLOSED;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_receiptrefund_header.refund_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_receiptrefund_header.refund_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_receiptrefund_header.refund_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company'];
            }
        }
        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        }

        $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_receiptrefund_header.reservation_id',
                       );
        $iTotalRecords = $this->mdl_finance->countJoin('ar_receiptrefund_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_receiptrefund_header.refund_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_receiptrefund_header.refund_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_receiptrefund_header.refund_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'view_cs_reservation.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_receiptrefund_header.*, view_cs_reservation.tenant_fullname, view_cs_reservation.reservation_code, view_cs_reservation.reservation_date, view_cs_reservation.company_name, view_cs_reservation.status as reservation_status'
            ,'ar_receiptrefund_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/folio/refund/3/' . $row->refund_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->refund_id . '" data-code="' . $row->refund_no . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->refund_amount > 0){

                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->refund_id . '" name="ischecked[]" ' . (check_session_action($menu_id, STATUS_POSTED) ? ($row->status == STATUS_NEW ? '' : 'disabled') : 'disabled') . '/>',
                    $row->refund_no,
                    dmy_from_db($row->refund_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    $row->company_name,
                    format_num($row->refund_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            } else {
                $records["data"][] = array(
                    '',
                    $row->refund_no,
                    dmy_from_db($row->refund_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    $row->company_name,
                    format_num($row->refund_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            }

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function refund_form($refund_id = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        //HEADER
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        $data['refund_id'] = $refund_id;

        if($refund_id > 0){
            $joins = array('cs_reservation_header' => 'cs_reservation_header.reservation_id = ar_receiptrefund_header.reservation_id',
                'ms_tenant' => 'ms_tenant.tenant_id = cs_reservation_header.tenant_id'
            );
            $qry = $this->mdl_finance->getJoin('ar_receiptrefund_header.*, ms_tenant.tenant_fullname, cs_reservation_header.reservation_code, cs_reservation_header.reservation_date, cs_reservation_header.status as reservation_status'
                ,'ar_receiptrefund_header', $joins, array('refund_id' => $refund_id));

            $data['row'] = $qry->row();
            $data['detail'] = $this->db->get_where('ar_receiptrefund_detail', array('refund_id' => $refund_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/refund/refund_form', $data);
        $this->load->view('layout/footer');
    }

    public function action_request(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $refund_id = $_POST['refund_id'];
        $data['status'] = $_POST['action'];
        $data['cancel_note'] = $_POST['reason'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Receipt Refund';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $refund_id;
        $data_log['feature_id'] = Feature::FEATURE_AR_REFUND;

        if($refund_id > 0 && $data['status'] > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $qry = $this->db->get_where('ar_receiptrefund_header', array('refund_id' => $refund_id));
            if($qry->num_rows() > 0){
                $row = $qry->row();

                if($data['status'] == STATUS_APPROVE){
                    $this->mdl_general->update('ar_receiptrefund_header', array('refund_id' => $refund_id), $data);

                    $data_log['action_type'] = STATUS_APPROVE;
                    $this->db->insert('app_log', $data_log);

                    $result['type'] = '1';
                    $result['message'] = 'Transaction successfully approved.';
                }
                else if($data['status'] == STATUS_CANCEL){
                    if($row->status == STATUS_CANCEL){
                        $result['type'] = '0';
                        $result['message'] = 'Transaction already canceled.';
                    }
                    else {
                        $this->mdl_general->update('ar_receiptrefund_header', array('refund_id' => $refund_id), $data);

                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['type'] = '1';
                        $result['message'] = 'Transaction successfully canceled.';
                    }
                }

                //FINALIZE TRANSACTION
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be processed.';
                }
                else
                {
                    $this->db->trans_commit();
                }
            }
        }

        echo json_encode($result);
    }

    public function submit_refund(){
        $valid = true;

        if(isset($_POST)){
            $refundId = $_POST['refund_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['refund_date'] = dmy_to_ymd($_POST['refund_date']);

            $data['reservation_id'] = $_POST['reservation_id'];
            $data['bankaccount_id'] = $_POST['bankaccount_id'];
            $data['refund_amount'] = $_POST['refund_amount'];
            $data['remark'] = $_POST['remark'];

            if($refundId > 0){
                $qry = $this->db->get_where('ar_receiptrefund_header', array('refund_id' => $refundId));
                $row = $qry->row();

                $arr_date = explode('-', $data['refund_date']);
                $arr_date_old = explode('-', ymd_from_db($row->refund_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['refund_no'] = $this->generate_refundno($data, $data['refund_date'], $row->refund_no);

                    if($data['refund_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Failed generating code.');
                    }
                }

                if($valid){
                    $this->mdl_general->update('ar_receiptrefund_header', array('refund_id' => $refundId), $data);

                    //echo '<br>step 3 update ' . $data['deposit_date'];

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';
                        $valid = $this->insertRefundEntries($refundId);

                        //echo '<br>step 5 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }

                    if($valid){
                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Refund';
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $refundId;
                        $data_log['feature_id'] = Feature::FEATURE_AR_REFUND;
                        $data_log['action_type'] = $data['status'];
                        $this->db->insert('app_log', $data_log);
                    }
                }
            }
            else {
                $data['refund_no'] = $this->generate_refundno($data, $data['refund_date']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                if($data['refund_no'] != ''){
                    $this->db->insert('ar_receiptrefund_header', $data);
                    $refundId = $this->db->insert_id();

                    if($refundId > 0){
                        $valid = $this->insertRefundEntries($refundId);

                        if($valid){
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Refund';
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $refundId;
                            $data_log['feature_id'] = Feature::FEATURE_AR_REFUND;
                            $data_log['action_type'] = STATUS_NEW;
                            $this->db->insert('app_log', $data_log);

                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Transaction successfully created.');
                        }

                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = false;
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                //redirect(base_url('ar/refund/refund_form/' . $refundId . '.tpd'));
                redirect(base_url('ar/folio/refund/3/' . $refundId . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    //redirect(base_url('ar/refund/refund_manage/1.tpd'),true);
                    redirect(base_url('ar/folio/refund/1.tpd'),true);
                }
                else {
                    //redirect(base_url('ar/refund/refund_form/' . $refundId . '.tpd'));
                    redirect(base_url('ar/folio/refund/3/' . $refundId . '.tpd'));
                }
            }
        }
    }

    private function insertRefundEntries($refundId = 0){
        $valid = true;

        if($refundId > 0 && isset($_POST)){
            $refunddetail_id = $_POST['refunddetail_id'];
            $base_amount = $_POST['base_amount'];
            $refund_amount = $_POST['refund_amount'];
            $journal_note = isset($_POST['guest_name']) ? $_POST['guest_name'] : 'Refund';

            if($base_amount > 0 && $refund_amount > 0){
                $detail['refund_id'] = $refundId;
                $detail['journal_note'] = $journal_note;
                $detail['base_amount'] = $base_amount;
                $detail['refund_amount'] = $refund_amount;

                if($refunddetail_id > 0){
                    $this->mdl_general->update('ar_receiptrefund_detail', array('refunddetail_id' => $refunddetail_id), $detail);
                }else{
                    $detail['status'] = STATUS_NEW;

                    $this->db->insert('ar_receiptrefund_detail', $detail);
                    $insertID = $this->db->insert_id();

                    if($insertID <= 0){
                        $valid = false;
                    }
                }
            }
        }

        return $valid;
    }

    private function generate_refundno($header = array(), $serverdate, $old_doc_no = ''){
        $result = '';

        if(count($header) > 0 && isset($serverdate)){
            //Remove old doc no
            if($old_doc_no != ''){
                $this->db->delete('ap_payment', array('payment_code' => $old_doc_no, 'status' => FLAG_REFUND));
            }

            $pv['payment_date'] = $serverdate;
            $pv['user_created'] = my_sess('user_id');
            $pv['date_created']  = date('Y-m-d H:i:s');
            $pv['total_amount'] = $header['refund_amount'];
            $pv['payment_code'] = $this->mdl_general->generate_code(Feature::FEATURE_AP_PAYMENT, $serverdate);
            $pv['status'] = FLAG_REFUND;

            $this->db->insert('ap_payment', $pv);
            $newID = $this->db->insert_id();

            if($newID > 0 && trim($pv['payment_code']) != ''){
                $result = $pv['payment_code'];
            }
        }

        return $result;
    }

    /*POSTING REFUNDS*/
    public function posting_refunds(){
        $valid = true;

        if(isset($_POST)){
            $this->load->model('finance/mdl_finance');

            $isCorporate = isset($_POST['is_corporate']) ? $_POST['is_corporate'] : 0;

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            //AR
            //  BANK
            if(isset($_POST['ischecked'])){
                $rowcount = 0;

                foreach( $_POST['ischecked'] as $val){
                    //echo '[posting_refunds] ... ' . $val;

                    //insert post journal
                    $detail = array();

                    $totalDebit = 0;
                    $totalCredit = 0;

                    $qryDetails = $this->db->get_where('ar_receiptrefund_detail', array('refund_id'=> $val));

                    $journal_note = '';
                    if($qryDetails->num_rows() > 0){
                        foreach($qryDetails->result() as $det){
                            $totalCredit += $det->refund_amount;
                            $journal_note = $det->journal_note;
                        }
                    }

                    $qryHeader = $this->db->get_where('ar_receiptrefund_header', array('refund_id' => $val));

                    if($totalCredit > 0 && $qryHeader->num_rows() > 0){
                        $head = $qryHeader->row();
                        if($head->company_id > 0){
                            $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                        }

                        //AR
                        /*
                        $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                        if($isCorporate > 0){
                            $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                        }else{
                            $reservation = $this->db->get_where('cs_reservation_header', array('reservation_id' => $head->reservation_id));
                            if($reservation->num_rows() > 0){
                                $reservation = $reservation->row();
                                if($reservation->reservation_type == RES_TYPE::CORPORATE){
                                    $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                                }
                            }
                        }*/

                        if($specAR['coa_id'] > 0){
                            $rowdet = array();
                            $rowdet['coa_id'] = $specAR['coa_id'];
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $journal_note;
                            $rowdet['journal_debit'] = $totalCredit;
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = 0;
                            $rowdet['transtype_id'] = $specAR['transtype_id'];

                            array_push($detail, $rowdet);
                        }

                        //Add (C) Bank
                        if($head->bankaccount_id > 0){
                            $bank = $this->mdl_finance->getBankAccount($head->bankaccount_id);
                            if(isset($bank['coa_id'])){
                                $rowdet = array();
                                $rowdet['coa_id'] = $bank['coa_id'];
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $journal_note;
                                $rowdet['journal_debit'] = 0;
                                $rowdet['journal_credit'] = $totalCredit;
                                $rowdet['reference_id'] = 0;
                                $rowdet['transtype_id'] = 0;

                                array_push($detail, $rowdet);

                                $totalDebit += $totalCredit;
                            }
                        }

                        //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                        if($totalDebit == $totalCredit){
                            $posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : ymd_from_db($head->refund_date);

                            $header = array();
                            $header['journal_no'] = $head->refund_no;
                            $header['journal_date'] = $posting_date;
                            $header['journal_remarks'] = $head->remark;
                            $header['modul'] = GLMOD::GL_MOD_AR;
                            $header['journal_amount'] = $totalDebit;
                            $header['reference'] = '';

                            $valid = $this->mdl_finance->postJournal($header,$detail);

                            if($valid){
                                $data['status']= STATUS_CLOSED;
                                $this->mdl_general->update('ar_receiptrefund_header', array('refund_id' => $val), $data);
                            }

                            $rowcount++;
                        }
                    }
                }

                $this->session->set_flashdata('flash_message', $rowcount . ' transaction(s) successfully posted.');
                $this->session->set_flashdata('flash_message_class', 'success');
            }else{
                $this->session->set_flashdata('flash_message', 'No transactions selected for posting.');
                $this->session->set_flashdata('flash_message_class', 'warning');
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                //redirect(base_url('ar/refund/refund_manage/1.tpd'));
                if($isCorporate > 0){
                    redirect(base_url('ar/corporate_bill/refund/1.tpd'));
                }else{
                    redirect(base_url('ar/folio/refund/1.tpd'));
                }
            }
            else {
                //redirect(base_url('ar/refund/refund_manage/1.tpd'));
                if($isCorporate > 0){
                    redirect(base_url('ar/corporate_bill/refund/1.tpd'));
                }else{
                    redirect(base_url('ar/folio/refund/1.tpd'));
                }
            }
        }
    }

    public function ajax_posting_refund_by_id(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $refundId = 0;
        $isCorporate = 0;

        if(isset($_POST['refund_id'])){
            $refundId = $_POST['refund_id'];
        }

        if(isset($_POST['is_corporate'])){
            $isCorporate = $_POST['is_corporate'];
        }

        if($refundId > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            //AR
            //  BANK
            $detail = array();

            $totalDebit = 0;
            $totalCredit = 0;

            $qryDetails = $this->db->get_where('ar_receiptrefund_detail', array('refund_id'=> $refundId));

            $journal_note = '';
            if($qryDetails->num_rows() > 0){
                foreach($qryDetails->result() as $det){
                    $totalCredit += $det->refund_amount;
                    $journal_note = $det->journal_note;
                }
            }

            $qryHeader = $this->db->get_where('ar_receiptrefund_header', array('refund_id' => $refundId));

            if($totalCredit > 0 && $qryHeader->num_rows() > 0){
                $head = $qryHeader->row();

                //AR
                $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                if($head->company_id > 0){
                    $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                }

                /*
                if($isCorporate > 0){
                    $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                }else{
                    $reservation = $this->db->get_where('cs_reservation_header', array('reservation_id' => $head->reservation_id));
                    if($reservation->num_rows() > 0){
                        $reservation = $reservation->row();
                        if($reservation->reservation_type == RES_TYPE::CORPORATE){
                            $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                        }
                    }
                }
                */
                if($specAR['coa_id'] > 0){
                    $rowdet = array();
                    $rowdet['coa_id'] = $specAR['coa_id'];
                    $rowdet['dept_id'] = 0;
                    $rowdet['journal_note'] = $journal_note;
                    $rowdet['journal_debit'] = $totalCredit;
                    $rowdet['journal_credit'] = 0;
                    $rowdet['reference_id'] = 0;
                    $rowdet['transtype_id'] = $specAR['transtype_id'];

                    array_push($detail, $rowdet);
                }

                //Add (C) Bank
                if($head->bankaccount_id > 0){
                    $bank = $this->mdl_finance->getBankAccount($head->bankaccount_id);
                    if(isset($bank['coa_id'])){
                        $rowdet = array();
                        $rowdet['coa_id'] = $bank['coa_id'];
                        $rowdet['dept_id'] = 0;
                        $rowdet['journal_note'] = $journal_note;
                        $rowdet['journal_debit'] = 0;
                        $rowdet['journal_credit'] = $totalCredit;
                        $rowdet['reference_id'] = 0;
                        $rowdet['transtype_id'] = 0;

                        array_push($detail, $rowdet);

                        $totalDebit += $totalCredit;
                    }
                }

                //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                if($totalDebit == $totalCredit){
                    $posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : ymd_from_db($head->refund_date);

                    $header = array();
                    $header['journal_no'] = $head->refund_no;
                    $header['journal_date'] = $posting_date;
                    $header['journal_remarks'] = $head->remark;
                    $header['modul'] = GLMOD::GL_MOD_AR;
                    $header['journal_amount'] = $totalDebit;
                    $header['reference'] = '';

                    $valid = $this->mdl_finance->postJournal($header,$detail);

                    if($valid){
                        $data['status']= STATUS_CLOSED;
                        $this->mdl_general->update('ar_receiptrefund_header', array('refund_id' => $refundId), $data);
                    }
                }

            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be posted. Please try again later.';

                    //$this->session->set_flashdata('flash_message_class', 'danger');
                    //$this->session->set_flashdata('flash_message', 'Transaction can not be posted. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = $header['journal_no'] . ' successfully posted.';
                    //$result['redirect_link'] = base_url('ar/refund/refund_form/'. $refundId .'.tpd');
                    if($isCorporate > 0){
                        $result['redirect_link'] = base_url('ar/corporate_bill/refund/3/'. $refundId .'.tpd');
                    }else{
                        $result['redirect_link'] = base_url('ar/folio/refund/3/'. $refundId .'.tpd');
                    }
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be posted. Please try again later.';
            }

        }

        echo json_encode($result);
    }

    public function pdf_refundvoucher($refund_id = 0, $is_deposit = 0) {
        if ($refund_id > 0) {
            if($is_deposit <= 0){
                $qry = $this->db->get_where('ar_receiptrefund_header', array('refund_id' => $refund_id));
            }else{
                $qry = $this->db->get_where('ar_depositrefund_header', array('refund_id' => $refund_id));
            }

            if ($qry->num_rows() > 0) {
                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                $data['row'] = $qry->row();
                $data['qry_det'] =  $this->db->order_by('journal_credit','ASC')->get_where('view_get_journal_detail', array('journal_no' => $data['row']->refund_no));

                $this->load->view('ar/refund/pdf_jv_refund.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->refund_no . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

    #endregion

    #region Refund History

    public function refund_history(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/refund/refund_history.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_refund_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_receiptrefund_header.receipt_id <='] = 0;
        $where['ar_receiptrefund_header.reservation_id > '] = 0;

        $where_str = 'ar_receiptrefund_header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_receiptrefund_header.refund_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_receiptrefund_header.refund_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_receiptrefund_header.refund_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company'];
            }
        }
        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        }

        $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_receiptrefund_header.reservation_id'
        );
        $iTotalRecords = $this->mdl_finance->countJoin('ar_receiptrefund_header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_receiptrefund_header.refund_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_receiptrefund_header.refund_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_receiptrefund_header.refund_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'view_cs_reservation.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_receiptrefund_header.*, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name,, view_cs_reservation.reservation_code, view_cs_reservation.reservation_date, view_cs_reservation.status as reservation_status'
            ,'ar_receiptrefund_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/folio/refund/3/' . $row->refund_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    //$btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->refund_id . '" data-code="' . $row->refund_no . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }else if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('ar/refund/pdf_refundvoucher/' . $row->refund_id) . '" class="blue-ebonyclay" target="_blank">Voucher</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->refund_no,
                dmy_from_db($row->refund_date),
                $row->reservation_code,
                $row->tenant_fullname,
                $row->company_name,
                format_num($row->refund_amount,0),
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                        Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    #endregion

    #region Corporate Official Receipt Refund

    public function get_corp_refund_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_receiptrefund_header.status'] = STATUS_NEW;
        $where['ar_receiptrefund_header.receipt_id > '] = 0;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_receiptrefund_header.refund_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_receiptrefund_header.refund_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_receiptrefund_header.refund_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }

        if(isset($_REQUEST['filter_receipt'])){
            if($_REQUEST['filter_receipt'] != ''){
                $like['ar_receiptrefund_header.receipt_no'] = $_REQUEST['filter_receipt'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['ar_receiptrefund_header.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_receiptrefund_header as ar_receiptrefund_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_receiptrefund_header.refund_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_receiptrefund_header.refund_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_receiptrefund_header.refund_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ar_receiptrefund_header.receipt_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'ar_receiptrefund_header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_receiptrefund_header.*'
            ,'vw_ar_receiptrefund_header as ar_receiptrefund_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/refund/3/' . $row->refund_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->refund_id . '" data-code="' . $row->refund_no . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->refund_amount > 0){

                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->refund_id . '" name="ischecked[]" ' . (check_session_action($menu_id, STATUS_POSTED) ? ($row->status == STATUS_NEW ? '' : 'disabled') : 'disabled') . '/>',
                    $row->refund_no,
                    dmy_from_db($row->refund_date),
                    $row->receipt_no,
                    $row->company_name,
                    format_num($row->refund_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            } else {
                $records["data"][] = array(
                    '',
                    $row->refund_no,
                    dmy_from_db($row->refund_date),
                    $row->receipt_no,
                    $row->company_name,
                    format_num($row->refund_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            }

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function get_corp_refund_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_receiptrefund_header.receipt_id > '] = 0;

        $where_str = 'ar_receiptrefund_header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_receiptrefund_header.refund_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_receiptrefund_header.refund_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_receiptrefund_header.refund_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }

        if(isset($_REQUEST['filter_receipt'])){
            if($_REQUEST['filter_receipt'] != ''){
                $like['ar_receiptrefund_header.receipt_no'] = $_REQUEST['filter_receipt'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['ar_receiptrefund_header.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_receiptrefund_header as ar_receiptrefund_header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_receiptrefund_header.refund_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_receiptrefund_header.refund_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_receiptrefund_header.refund_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ar_receiptrefund_header.receipt_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'ar_receiptrefund_header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_receiptrefund_header.*'
            ,'vw_ar_receiptrefund_header as ar_receiptrefund_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/refund/3/' . $row->refund_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    //$btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->refund_id . '" data-code="' . $row->refund_no . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }else if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('ar/refund/pdf_refundvoucher/' . $row->refund_id) . '" class="blue-ebonyclay" target="_blank">Voucher</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->refund_no,
                dmy_from_db($row->refund_date),
                $row->receipt_no,
                $row->company_name,
                format_num($row->refund_amount,0),
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                        Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function corp_refund_form($refund_id = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        //HEADER
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        $data['refund_id'] = $refund_id;

        if($refund_id > 0){
            $joins = array();
            $qry = $this->mdl_finance->getJoin('ar_receiptrefund_header.*'
                ,'vw_ar_receiptrefund_header as ar_receiptrefund_header', $joins, array('refund_id' => $refund_id));

            $data['row'] = $qry->row();
            $data['detail'] = $this->db->get_where('ar_receiptrefund_detail', array('refund_id' => $refund_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/refund/corp_refund_form', $data);
        $this->load->view('layout/footer');
    }

    public function submit_corp_refund(){
        $valid = true;

        if(isset($_POST)){
            $refundId = $_POST['refund_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['refund_date'] = dmy_to_ymd($_POST['refund_date']);

            $data['receipt_id'] = $_POST['receipt_id'];
            $data['company_id'] = $_POST['company_id'];
            $data['reservation_id'] = $_POST['reservation_id'];
            $data['bankaccount_id'] = $_POST['bankaccount_id'];
            $data['refund_amount'] = $_POST['refund_amount'];
            $data['remark'] = $_POST['remark'];

            if($refundId > 0){
                $qry = $this->db->get_where('ar_receiptrefund_header', array('refund_id' => $refundId));
                $row = $qry->row();

                $arr_date = explode('-', $data['refund_date']);
                $arr_date_old = explode('-', ymd_from_db($row->refund_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['refund_no'] = $this->generate_refundno($data, $data['refund_date'], $row->refund_no);

                    if($data['refund_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Failed generating code.');
                    }
                }

                if($valid){
                    $this->mdl_general->update('ar_receiptrefund_header', array('refund_id' => $refundId), $data);

                    //echo '<br>step 3 update ' . $data['deposit_date'];

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';
                        $valid = $this->insertRefundEntries($refundId);

                        //echo '<br>step 5 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }

                    if($valid){
                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Refund';
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $refundId;
                        $data_log['feature_id'] = Feature::FEATURE_AR_REFUND;
                        $data_log['action_type'] = $data['status'];
                        $this->db->insert('app_log', $data_log);
                    }
                }
            }
            else {
                $data['refund_no'] = $this->generate_refundno($data, $data['refund_date']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                if($data['refund_no'] != ''){
                    $this->db->insert('ar_receiptrefund_header', $data);
                    $refundId = $this->db->insert_id();

                    if($refundId > 0){
                        $valid = $this->insertRefundEntries($refundId);

                        if($valid){
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Refund';
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $refundId;
                            $data_log['feature_id'] = Feature::FEATURE_AR_REFUND;
                            $data_log['action_type'] = STATUS_NEW;
                            $this->db->insert('app_log', $data_log);

                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Transaction successfully created.');
                        }

                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = false;
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                //redirect(base_url('ar/refund/refund_form/' . $refundId . '.tpd'));
                redirect(base_url('ar/corporate_bill/refund/3/' . $refundId . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    //redirect(base_url('ar/refund/refund_manage/1.tpd'),true);
                    redirect(base_url('ar/corporate_bill/refund/1.tpd'),true);
                }
                else {
                    //redirect(base_url('ar/refund/refund_form/' . $refundId . '.tpd'));
                    redirect(base_url('ar/corporate_bill/refund/3/' . $refundId . '.tpd'));
                }
            }
        }
    }

    #endregion

    #region Corporate Deposit Refund

    public function get_deposit_refund_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['header.status'] = STATUS_NEW;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['header.refund_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,header.refund_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,header.refund_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }

        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['header.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_depositrefund_header as header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'header.refund_no DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'header.refund_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'header.refund_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('header.*'
            ,'vw_ar_depositrefund_header as header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/deposit_refund/3/' . $row->refund_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->refund_id . '" data-code="' . $row->refund_no . '"><i class="fa fa-remove"></i> ' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->refund_amount > 0){
                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->refund_id . '" name="ischecked[]" ' . (check_session_action($menu_id, STATUS_POSTED) ? ($row->status == STATUS_NEW ? '' : 'disabled') : 'disabled') . '/>',
                    $row->refund_no,
                    dmy_from_db($row->refund_date),
                    $row->company_name,
                    format_num($row->refund_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            } else {
                $records["data"][] = array(
                    '',
                    $row->refund_no,
                    dmy_from_db($row->refund_date),
                    $row->company_name,
                    format_num($row->refund_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            }

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function get_deposit_refund_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where_str = 'header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['header.refund_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,header.refund_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,header.refund_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }

        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['header.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_depositrefund_header as header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'header.refund_no DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'header.refund_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'header.refund_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('header.*'
            ,'vw_ar_depositrefund_header as header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/deposit_refund/3/' . $row->refund_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a> </li>';
            if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('ar/refund/pdf_refundvoucher/' . $row->refund_id) . '/2.tpd" class="blue-ebonyclay" target="_blank"><i class="fa fa-print"></i> Voucher</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->refund_no,
                dmy_from_db($row->refund_date),
                $row->company_name,
                format_num($row->refund_amount,0),
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                        Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function deposit_refund_form($refund_id = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        //HEADER
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        $data['refund_id'] = $refund_id;

        if($refund_id > 0){
            $joins = array('ms_company' => 'ms_company.company_id = header.company_id');
            $qry = $this->mdl_finance->getJoin('header.*, ms_company.company_name'
                ,'ar_depositrefund_header as header', $joins, array('refund_id' => $refund_id));

            $data['row'] = $qry->row();
            $details = $this->mdl_finance->getJoin('detail.*, ar_deposit_header.deposit_no, ms_deposit_type.deposit_key, ms_deposit_type.deposit_desc'
                ,'ar_depositrefund_detail as detail',
                array('ar_deposit_detail' => 'ar_deposit_detail.detail_id = detail.depositdetail_id',
                      'ar_deposit_header' => 'ar_deposit_detail.deposit_id = ar_deposit_header.deposit_id',
                      'ms_deposit_type' => 'ar_deposit_detail.deposittype_id = ms_deposit_type.deposittype_id')
                , array('refund_id' => $refund_id));
            if($details->num_rows() > 0){
                $data['detail'] = $details->row();
            }
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/refund/deposit_refund_form', $data);
        $this->load->view('layout/footer');
    }

    public function submit_deposit_refund(){
        $valid = true;

        if(isset($_POST)){
            $refundId = $_POST['refund_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['refund_date'] = dmy_to_ymd($_POST['refund_date']);

            $data['company_id'] = $_POST['company_id'];
            $data['reservation_id'] = $_POST['reservation_id'];
            $data['bankaccount_id'] = $_POST['bankaccount_id'];
            $data['refund_amount'] = $_POST['refund_amount'];
            $data['remark'] = $_POST['remark'];

            if($refundId > 0){
                $qry = $this->db->get_where('ar_depositrefund_header', array('refund_id' => $refundId));
                $row = $qry->row();

                $arr_date = explode('-', $data['refund_date']);
                $arr_date_old = explode('-', ymd_from_db($row->refund_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['refund_no'] = $this->generate_refundno($data, $data['refund_date'], $row->refund_no);

                    if($data['refund_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Failed generating code.');
                    }
                }

                if($valid){
                    $this->mdl_general->update('ar_depositrefund_header', array('refund_id' => $refundId), $data);

                    //echo '<br>step 3 update ' . $data['deposit_date'];

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';
                        $valid = $this->insertDepositRefundEntries($refundId);

                        //echo '<br>step 5 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }

                    if($valid){
                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Deposit Refund';
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $refundId;
                        $data_log['feature_id'] = Feature::FEATURE_AR_REFUND;
                        $data_log['action_type'] = $data['status'];
                        $this->db->insert('app_log', $data_log);
                    }
                }
            }
            else {
                $data['refund_no'] = $this->generate_refundno($data, $data['refund_date']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                if($data['refund_no'] != ''){
                    $this->db->insert('ar_depositrefund_header', $data);
                    $refundId = $this->db->insert_id();

                    if($refundId > 0){
                        $valid = $this->insertDepositRefundEntries($refundId);

                        if($valid){
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = get_action_name($data['status'], false) . ' Deposit Refund';
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $refundId;
                            $data_log['feature_id'] = Feature::FEATURE_AR_REFUND;
                            $data_log['action_type'] = STATUS_NEW;
                            $this->db->insert('app_log', $data_log);

                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Transaction successfully created.');
                        }

                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = false;
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                //redirect(base_url('ar/refund/refund_form/' . $refundId . '.tpd'));
                redirect(base_url('ar/corporate_bill/deposit_refund/3/' . $refundId . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    //redirect(base_url('ar/refund/refund_manage/1.tpd'),true);
                    redirect(base_url('ar/corporate_bill/deposit_refund/1.tpd'),true);
                }
                else {
                    //redirect(base_url('ar/refund/refund_form/' . $refundId . '.tpd'));
                    redirect(base_url('ar/corporate_bill/deposit_refund/3/' . $refundId . '.tpd'));
                }
            }
        }
    }

    private function insertDepositRefundEntries($refundId = 0){
        $valid = true;

        if($refundId > 0 && isset($_POST)){
            $refunddetail_id = $_POST['refunddetail_id'];
            $depositdetail_id = $_POST['depositdetail_id'];
            $base_amount = $_POST['base_amount'];
            $refund_amount = $_POST['refund_amount'];
            $journal_note = isset($_POST['remark']) ? $_POST['remark'] : '-';

            if($base_amount > 0 && $refund_amount > 0){
                $detail['refund_id'] = $refundId;
                $detail['depositdetail_id'] = $depositdetail_id;
                $detail['journal_note'] = $journal_note;
                $detail['base_amount'] = $base_amount;
                $detail['refund_amount'] = $refund_amount;

                if($refunddetail_id > 0){
                    $this->mdl_general->update('ar_depositrefund_detail', array('refunddetail_id' => $refunddetail_id), $detail);
                }else{
                    $detail['status'] = STATUS_NEW;

                    $this->db->insert('ar_depositrefund_detail', $detail);
                    $insertID = $this->db->insert_id();

                    if($insertID <= 0){
                        $valid = false;
                    }
                }
            }
        }

        return $valid;
    }

    public function xposting_deposit_refund_by_id($isFolio = 0){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $refundId = 0;

        if(isset($_POST['refund_id'])){
            $refundId = $_POST['refund_id'];
        }

        if($refundId > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            //Security Deposit
            //  BANK
            $detail = array();

            $totalDebit = 0;
            $totalCredit = 0;

            $qryDetails = $this->mdl_finance->getJoin('detail.*, gl_coa.coa_id'
                ,'ar_depositrefund_detail as detail',
                array('ar_deposit_detail' => 'ar_deposit_detail.detail_id = detail.depositdetail_id',
                      'ms_deposit_type' => 'ar_deposit_detail.deposittype_id = ms_deposit_type.deposittype_id',
                      'gl_coa' => 'gl_coa.coa_code = ms_deposit_type.coa_code')
                , array('refund_id' => $refundId));
            if($qryDetails->num_rows() > 0){
                //SEC DEPOSIT
                foreach($qryDetails->result() as $det){
                    $rowdet = array();
                    $rowdet['coa_id'] = $det->coa_id;
                    $rowdet['dept_id'] = 0;
                    $rowdet['journal_note'] = $det->journal_note;
                    $rowdet['journal_debit'] = $det->refund_amount;
                    $rowdet['journal_credit'] = 0;
                    //$rowdet['reference_id'] = 0;
                    $rowdet['transtype_id'] = 0;

                    array_push($detail, $rowdet);

                    $totalCredit += $det->refund_amount;
                }
            }

            $qryHeader = $this->db->get_where('ar_depositrefund_header', array('refund_id' => $refundId));

            if($totalCredit > 0 && $qryHeader->num_rows() > 0){
                $head = $qryHeader->row();

                //Add (C) Bank
                if($head->bankaccount_id > 0){
                    $bank = $this->mdl_finance->getBankAccount($head->bankaccount_id);
                    if(isset($bank['coa_id'])){
                        $rowdet = array();
                        $rowdet['coa_id'] = $bank['coa_id'];
                        $rowdet['dept_id'] = 0;
                        $rowdet['journal_note'] = $head->remark;
                        $rowdet['journal_debit'] = 0;
                        $rowdet['journal_credit'] = $totalCredit;
                        $rowdet['reference_id'] = 0;
                        $rowdet['transtype_id'] = 0;

                        array_push($detail, $rowdet);

                        $totalDebit = $totalCredit;
                    }
                }

                //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                if($totalDebit == $totalCredit){
                    $posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : ymd_from_db($head->refund_date);

                    $header = array();
                    $header['journal_no'] = $head->refund_no;
                    $header['journal_date'] = $posting_date;
                    $header['journal_remarks'] = $head->remark;
                    $header['modul'] = GLMOD::GL_MOD_AR;
                    $header['journal_amount'] = $totalDebit;
                    $header['reference'] = '';

                    $valid = $this->mdl_finance->postJournal($header,$detail);

                    if($valid){
                        $data['status']= STATUS_CLOSED;
                        $this->mdl_general->update('ar_depositrefund_header', array('refund_id' => $refundId), $data);
                    }
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be posted. Please try again later.';

                    //$this->session->set_flashdata('flash_message_class', 'danger');
                    //$this->session->set_flashdata('flash_message', 'Transaction can not be posted. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = $header['journal_no'] . ' successfully posted.';
                    //$result['redirect_link'] = base_url('ar/refund/refund_form/'. $refundId .'.tpd');
                    if($isFolio > 0) {
                        $result['redirect_link'] = base_url('ar/folio/deposit_refund/3/'. $refundId .'.tpd');
                    }else{
                        $result['redirect_link'] = base_url('ar/corporate_bill/deposit_refund/3/'. $refundId .'.tpd');
                    }

                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be posted. Please try again later.';
            }

        }

        echo json_encode($result);
    }

    public function posting_deposit_refunds($isFolio = 0){
        $valid = true;

        if(isset($_POST)){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            //AR
            //  BANK
            if(isset($_POST['ischecked'])){
                $rowcount = 0;

                foreach( $_POST['ischecked'] as $val){
                    //echo '[posting_refunds] ... ' . $val;

                    //insert post journal
                    $detail = array();

                    $totalDebit = 0;
                    $totalCredit = 0;

                    $qryDetails = $this->mdl_finance->getJoin('detail.*, gl_coa.coa_id','ar_depositrefund_detail as detail',
                        array('ar_deposit_detail' => 'ar_deposit_detail.detail_id = detail.depositdetail_id',
                            'ms_deposit_type' => 'ar_deposit_detail.deposittype_id = ms_deposit_type.deposittype_id',
                            'gl_coa' => 'gl_coa.coa_code = ms_deposit_type.coa_code')
                        , array('refund_id' => $val));

                    if($qryDetails->num_rows() > 0){
                        //SEC DEPOSIT
                        foreach($qryDetails->result() as $det){
                            $rowdet = array();
                            $rowdet['coa_id'] = $det->coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $det->journal_note;
                            $rowdet['journal_debit'] = $det->refund_amount;
                            $rowdet['journal_credit'] = 0;
                            //$rowdet['reference_id'] = 0;
                            $rowdet['transtype_id'] = 0;

                            array_push($detail, $rowdet);

                            $totalCredit += $det->refund_amount;
                        }
                    }

                    $qryHeader = $this->db->get_where('ar_depositrefund_header', array('refund_id' => $val));

                    if($totalCredit > 0 && $qryHeader->num_rows() > 0){
                        $head = $qryHeader->row();

                        //Add (C) Bank
                        if($head->bankaccount_id > 0){
                            $bank = $this->mdl_finance->getBankAccount($head->bankaccount_id);
                            if(isset($bank['coa_id'])){
                                $rowdet = array();
                                $rowdet['coa_id'] = $bank['coa_id'];
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $head->remark;
                                $rowdet['journal_debit'] = 0;
                                $rowdet['journal_credit'] = $totalCredit;
                                $rowdet['reference_id'] = 0;
                                $rowdet['transtype_id'] = 0;

                                array_push($detail, $rowdet);

                                $totalDebit = $totalCredit;
                            }
                        }

                        //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                        if($totalDebit == $totalCredit){
                            $posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : ymd_from_db($head->refund_date);

                            $header = array();
                            $header['journal_no'] = $head->refund_no;
                            $header['journal_date'] = $posting_date;
                            $header['journal_remarks'] = $head->remark;
                            $header['modul'] = GLMOD::GL_MOD_AR;
                            $header['journal_amount'] = $totalDebit;
                            $header['reference'] = '';

                            $valid = $this->mdl_finance->postJournal($header,$detail);

                            if($valid){
                                $data['status']= STATUS_CLOSED;
                                $this->mdl_general->update('ar_depositrefund_header', array('refund_id' => $val), $data);
                            }

                            $rowcount++;
                        }
                    }
                }

                $this->session->set_flashdata('flash_message', $rowcount . ' transaction(s) successfully posted.');
                $this->session->set_flashdata('flash_message_class', 'success');
            }else{
                $this->session->set_flashdata('flash_message', 'No transactions selected for posting.');
                $this->session->set_flashdata('flash_message_class', 'warning');
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                //redirect(base_url('ar/refund/refund_manage/1.tpd'));
                if($isFolio > 0) {
                    redirect(base_url('ar/folio/deposit_refund/1.tpd'));
                }else{
                    redirect(base_url('ar/corporate_bill/deposit_refund/1.tpd'));
                }
            }
            else {
                //redirect(base_url('ar/refund/refund_manage/1.tpd'));
                if($isFolio > 0) {
                    redirect(base_url('ar/folio/deposit_refund/1.tpd'));
                }else{
                    redirect(base_url('ar/corporate_bill/deposit_refund/1.tpd'));
                }
            }
        }
    }


    #endregion

    #region Reservation Deposit Refund

    public function get_deposit_folio_refund_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['header.status'] = STATUS_NEW;
        $where['header.reservation_id >'] = 0;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['header.refund_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,header.refund_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,header.refund_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }

        if(isset($_REQUEST['filter_reservation'])){
            if($_REQUEST['filter_reservation'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = header.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_depositrefund_header as header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'header.refund_no DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'header.refund_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'header.refund_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('header.*, view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name'
            ,'ar_depositrefund_header as header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/folio/deposit_refund/3/' . $row->refund_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->refund_id . '" data-code="' . $row->refund_no . '"><i class="fa fa-remove"></i> ' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->refund_amount > 0){
                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->refund_id . '" name="ischecked[]" ' . (check_session_action($menu_id, STATUS_POSTED) ? ($row->status == STATUS_NEW ? '' : 'disabled') : 'disabled') . '/>',
                    $row->refund_no,
                    dmy_from_db($row->refund_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    format_num($row->refund_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            } else {
                $records["data"][] = array(
                    '',
                    $row->refund_no,
                    dmy_from_db($row->refund_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    format_num($row->refund_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            }

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function get_deposit_folio_refund_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where_str = 'header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';
        $where['header.reservation_id > '] = 0;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['header.refund_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,header.refund_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,header.refund_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }

        if(isset($_REQUEST['filter_reservation'])){
            if($_REQUEST['filter_reservation'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = header.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_depositrefund_header as header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'header.refund_no DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'header.refund_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'header.refund_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('header.*, view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name'
            ,'ar_depositrefund_header as header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/folio/deposit_refund/3/' . $row->refund_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a> </li>';
            if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('ar/refund/pdf_refundvoucher/' . $row->refund_id) . '/2.tpd" class="blue-ebonyclay" target="_blank"><i class="fa fa-print"></i> Voucher</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->refund_no,
                dmy_from_db($row->refund_date),
                $row->reservation_code,
                $row->tenant_fullname,
                format_num($row->refund_amount,0),
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                        Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function deposit_folio_refund_form($refund_id = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        //HEADER
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        $data['refund_id'] = $refund_id;

        if($refund_id > 0){
            $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = header.reservation_id');
            $qry = $this->mdl_finance->getJoin('header.*, view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name'
                ,'ar_depositrefund_header as header', $joins, array('refund_id' => $refund_id));

            $data['row'] = $qry->row();
            $details = $this->mdl_finance->getJoin('detail.*, ar_deposit_header.deposit_no, ms_deposit_type.deposit_key, ms_deposit_type.deposit_desc'
                ,'ar_depositrefund_detail as detail',
                array('ar_deposit_detail' => 'ar_deposit_detail.detail_id = detail.depositdetail_id',
                      'ar_deposit_header' => 'ar_deposit_detail.deposit_id = ar_deposit_header.deposit_id',
                      'ms_deposit_type' => 'ar_deposit_detail.deposittype_id = ms_deposit_type.deposittype_id')
                , array('refund_id' => $refund_id));
            if($details->num_rows() > 0){
                $data['detail'] = $details->row();
            }
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/refund/deposit_folio_refund_form', $data);
        $this->load->view('layout/footer');
    }

    public function submit_deposit_folio_refund(){
        $valid = true;

        if(isset($_POST)){
            $refundId = $_POST['refund_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['refund_date'] = dmy_to_ymd($_POST['refund_date']);

            $data['reservation_id'] = $_POST['reservation_id'];
            $data['company_id'] = 0;
            $data['bankaccount_id'] = $_POST['bankaccount_id'];
            $data['refund_amount'] = $_POST['refund_amount'];
            $data['remark'] = $_POST['remark'];

            if($refundId > 0){
                $qry = $this->db->get_where('ar_depositrefund_header', array('refund_id' => $refundId));
                $row = $qry->row();

                $arr_date = explode('-', $data['refund_date']);
                $arr_date_old = explode('-', ymd_from_db($row->refund_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['refund_no'] = $this->generate_refundno($data, $data['refund_date'], $row->refund_no);

                    if($data['refund_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Failed generating code.');
                    }
                }

                if($valid){
                    $this->mdl_general->update('ar_depositrefund_header', array('refund_id' => $refundId), $data);

                    //echo '<br>step 3 update ' . $data['deposit_date'];

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';
                        $valid = $this->insertDepositRefundEntries($refundId);

                        //echo '<br>step 5 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }

                    if($valid){
                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Deposit Refund';
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $refundId;
                        $data_log['feature_id'] = Feature::FEATURE_AR_REFUND;
                        $data_log['action_type'] = $data['status'];
                        $this->db->insert('app_log', $data_log);
                    }
                }
            }
            else {
                $data['refund_no'] = $this->generate_refundno($data, $data['refund_date']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                if($data['refund_no'] != ''){
                    $this->db->insert('ar_depositrefund_header', $data);
                    $refundId = $this->db->insert_id();

                    if($refundId > 0){
                        $valid = $this->insertDepositRefundEntries($refundId);

                        if($valid){
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = get_action_name($data['status'], false) . ' Deposit Refund';
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $refundId;
                            $data_log['feature_id'] = Feature::FEATURE_AR_REFUND;
                            $data_log['action_type'] = STATUS_NEW;
                            $this->db->insert('app_log', $data_log);

                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Transaction successfully created.');
                        }

                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = false;
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                //redirect(base_url('ar/refund/refund_form/' . $refundId . '.tpd'));
                redirect(base_url('ar/folio/deposit_refund/3/' . $refundId . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    //redirect(base_url('ar/refund/refund_manage/1.tpd'),true);
                    redirect(base_url('ar/folio/deposit_refund/1.tpd'),true);
                }
                else {
                    //redirect(base_url('ar/refund/refund_form/' . $refundId . '.tpd'));
                    redirect(base_url('ar/folio/deposit_refund/3/' . $refundId . '.tpd'));
                }
            }
        }
    }

    #endregion

    #region Modal Lookup Form

    public function ajax_unallocated_reservation(){
        $this->load->view('ar/refund/ajax_modal_unallocated');
    }

    public function get_modal_reservation($num_index = 0, $reservation_id = 0, $refund_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $server_date = date('Y-m-d');

        if(isset($_REQUEST['filter_code'])){
            if($_REQUEST['filter_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_code'];
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $where_str = ' view_cs_reservation.status IN(' . ORDER_STATUS::RESERVED . ',' . ORDER_STATUS::CHECKOUT . ') ';

        $joins = array("view_cs_reservation"=>"view_cs_reservation.reservation_id = ar.reservation_id");
        $iTotalRecords = $this->mdl_finance->countJoin("fxnARUnallocatedByDate('" . $server_date ."') AS ar", $joins, $where, $like, $where_str);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'view_cs_reservation.reservation_code';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'view_cs_reservation.reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin("ar.*, view_cs_reservation.reservation_code, view_cs_reservation.reservation_type, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, view_cs_reservation.status as reservation_status ","fxnARUnallocatedByDate('" . $server_date . "') AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, "", array(), $where_str);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $pending_refund = 0;

            $existrefund = $this->db->query('SELECT ISNULL(SUM(refund_amount),0) as refund_total FROM ar_receiptrefund_header
                                               WHERE reservation_id = ' . $row->reservation_id . ' AND status = ' . STATUS_NEW . '
                                               AND refund_id <> ' . $refund_id);
            if($existrefund->num_rows() > 0){
                $pending_refund = $existrefund->row()->refund_total;
            }

            $unallocated = $row->pending_amount - $pending_refund;

            $attr = '';
            $attr .= ' data-reservation-id="' . $row->reservation_id . '" ';
            $attr .= ' data-reservation-code="' . $row->reservation_code . '" ';
            $attr .= ' data-tenant-name="' . $row->tenant_fullname . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-status="' . $row->reservation_status . '" ';
            $attr .= ' data-amount="' . $unallocated . '"';
            $attr .= ' data-index="' . $num_index . '" ';

            $text = "";
            if ($reservation_id == $row->reservation_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }else{
                $text = "Select";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-reservation" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            if($unallocated > 0){
                $records["data"][] = array(
                    $row->reservation_code,
                    RES_TYPE::caption($row->reservation_type),
                    $row->tenant_fullname,
                    $row->company_name,
                    format_num($unallocated,0),
                    ORDER_STATUS::get_status_name($row->reservation_status),
                    $btn
                );
                $i++;
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_unallocated_corporate(){
        $this->load->view('ar/refund/ajax_modal_unallocated_corp');
    }

    public function xmodal_undeposit(){
        $this->load->view('ar/refund/ajax_modal_undeposit');
    }

    public function get_modal_unallocated_corp($num_index = 0, $receipt_id = 0, $refund_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $server_date = date('Y-m-d');

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar.receipt_no'] = $_REQUEST['filter_no'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ar.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $where_str = '' ; //' view_cs_reservation.status IN(' . ORDER_STATUS::CHECKOUT . ',' .  ORDER_STATUS::CHECKIN . ') ';

        $joins = array(); //array("view_cs_reservation"=>"view_cs_reservation.reservation_id = ar.reservation_id");
        $iTotalRecords = $this->mdl_finance->countJoin("fxnARUnallocatedByDateCorp(getdate()) AS ar", $joins, $where, $like, $where_str);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ar.receipt_no asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ar.receipt_no ' . $_REQUEST['order'][0]['dir'];
            }

            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin("ar.*","fxnARUnallocatedByDateCorp(getdate()) AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, "", array(), $where_str);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $pending_refund = 0;

            $existdebit = $this->db->query('SELECT ISNULL(SUM(refund_amount),0) as refund_total FROM ar_receiptrefund_header
                                               WHERE reservation_id <= 0 AND receipt_id = ' . $row->receipt_id . ' AND status = ' . STATUS_NEW . '
                                               AND refund_id <> ' . $refund_id);
            if($existdebit->num_rows() > 0){
                $pending_refund = $existdebit->row()->refund_total;
            }

            $unallocated = $row->unallocated_amount - $pending_refund;

            $attr = '';
            $attr .= ' data-receipt-id="' . $row->receipt_id . '" ';
            $attr .= ' data-receipt-no="' . $row->receipt_no . '" ';
            $attr .= ' data-company-id="' . $row->company_id . '" ';
            //$attr .= ' data-reservation-id="' . $row->reservation_id . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-amount="' . $unallocated . '"';
            $attr .= ' data-index="' . $num_index . '" ';

            if ($receipt_id == $row->receipt_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }else{
                $text = "Select";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-receipt" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            if($unallocated > 0){
                $records["data"][] = array(
                    $row->receipt_no,
                    $row->company_name,
                    format_num($unallocated,0),
                    $btn
                );
                $i++;
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function get_modal_undeposit_by_refund_id($num_index = 0, $depositdetail_id = 0, $refund_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $server_date = date('Y-m-d');

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar.deposit_no'] = $_REQUEST['filter_no'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ar.company_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $like['ar.deposit_desc'] = $_REQUEST['filter_type'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin("fxnARUnDepositByDateCorp(getdate()) AS ar", $joins, $where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ar.deposit_no';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ar.deposit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar.deposit_desc ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin("ar.*","fxnARUnDepositByDateCorp(getdate()) AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            if($row->available_deposit > 0){
                $available = 0;

                $existreceipt = $this->db->query('SELECT ISNULL(SUM(ar_depositrefund_detail.refund_amount),0) as refund_amount FROM ar_depositrefund_header
                                               JOIN ar_depositrefund_detail ON ar_depositrefund_header.refund_id = ar_depositrefund_detail.refund_id
                                               WHERE ar_depositrefund_detail.depositdetail_id = ' . $row->depositdetail_id . ' AND ar_depositrefund_header.status = ' . STATUS_NEW . '
                                               AND ar_depositrefund_header.refund_id <> ' . $refund_id);
                if($existreceipt->num_rows() > 0){
                    $available = $existreceipt->row()->refund_amount;
                }

                $unallocated = $row->available_deposit - $available;

                $companyName = $row->company_name;

                $attr = '';
                $attr .= ' data-detail-id="' . $row->depositdetail_id . '" ';
                $attr .= ' data-company-id="' . $row->company_id . '" ';
                $attr .= ' data-company-name="' . $companyName . '" ';
                $attr .= ' data-deposit-no="' . $row->deposit_no . '" ';
                $attr .= ' data-deposit-key="' . $row->deposit_key . '" ';
                $attr .= ' data-available-amount="' . $unallocated . '"';
                $attr .= ' data-index="' . $num_index . '" ';

                if ($depositdetail_id == $row->depositdetail_id) {
                    $attr .= ' disabled="disabled" ';
                    $text = 'selected';
                }else{
                    $text = "Select";
                }

                $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-record" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

                if($unallocated > 0){
                    $records["data"][] = array(
                        $row->deposit_no,
                        $companyName,
                        $row->deposit_desc,
                        format_num($unallocated,0),
                        $btn
                    );
                    $i++;
                }
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function xmodal_undeposit_folio(){
        $this->load->view('ar/refund/ajax_modal_undeposit_folio');
    }

    public function get_undeposit_folio_by_refund_id($num_index = 0, $depositdetail_id = 0, $refund_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $server_date = date('Y-m-d');

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar.deposit_no'] = $_REQUEST['filter_no'];
            }
        }

        if(isset($_REQUEST['filter_reservation'])){
            if($_REQUEST['filter_reservation'] != ''){
                $like['ar.reservation_code'] = $_REQUEST['filter_reservation'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ar.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $like['ar.deposit_desc'] = $_REQUEST['filter_type'];
            }
        }

        $joins = array(); // array("view_ar_invoice_unpaid_sum _sum"=>"_sum.company_id = ar.company_id");
        $iTotalRecords = $this->mdl_finance->countJoin("fxnARUnDepositByDateFolio(getdate()) AS ar", $joins, $where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ar.deposit_no';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ar.deposit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ar.deposit_desc ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin("ar.*","fxnARUnDepositByDateFolio(getdate()) AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            if($row->available_deposit > 0){
                $available = 0;

                $existreceipt = $this->db->query('SELECT ISNULL(SUM(ar_depositrefund_detail.refund_amount),0) as refund_amount FROM ar_depositrefund_header
                                               JOIN ar_depositrefund_detail ON ar_depositrefund_header.refund_id = ar_depositrefund_detail.refund_id
                                               WHERE ar_depositrefund_detail.depositdetail_id = ' . $row->depositdetail_id . ' AND ar_depositrefund_header.status = ' . STATUS_NEW . '
                                               AND ar_depositrefund_header.refund_id <> ' . $refund_id);
                if($existreceipt->num_rows() > 0){
                    $available = $existreceipt->row()->refund_amount;
                }

                $unallocated = $row->available_deposit - $available;

                $attr = '';
                $attr .= ' data-detail-id="' . $row->depositdetail_id . '" ';
                $attr .= ' data-reservation-id="' . $row->reservation_id . '" ';
                $attr .= ' data-reservation-code="' . $row->reservation_code . '" ';
                $attr .= ' data-tenant-name="' . $row->tenant_fullname . '" ';
                $attr .= ' data-deposit-no="' . $row->deposit_no . '" ';
                $attr .= ' data-deposit-key="' . $row->deposit_key . '" ';
                $attr .= ' data-available-amount="' . $unallocated . '"';
                $attr .= ' data-index="' . $num_index . '" ';

                if ($depositdetail_id == $row->depositdetail_id) {
                    $attr .= ' disabled="disabled" ';
                    $text = 'selected';
                }else{
                    $text = "Select";
                }

                $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-record" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

                if($unallocated > 0){
                    $records["data"][] = array(
                        $row->deposit_no,
                        $row->reservation_code,
                        $row->tenant_fullname,
                        $row->deposit_desc,
                        format_num($unallocated,0),
                        $btn
                    );
                    $i++;
                }
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }


    #endregion
}

/* End of file registration.php */
/* Location: ./application/controllers/frondesk/registration.php */