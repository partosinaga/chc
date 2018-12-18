<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Debit extends CI_Controller {
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
        $this->debit_manage();
    }

    #region Debit Manage

    public function debit_manage($type = 1){
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
        $this->load->view('ar/debit/debit_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_debit_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_debitnote_header.status'] = STATUS_NEW;
        $where['ar_debitnote_header.receipt_id <='] = 0;
        $where['ar_debitnote_header.company_id <='] = 0;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_debitnote_header.debit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_debitnote_header.debit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_debitnote_header.debit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        }

        $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_debitnote_header.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_debitnote_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_debitnote_header.debit_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_debitnote_header.debit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_debitnote_header.debit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_debitnote_header.*, view_cs_reservation.tenant_fullname, view_cs_reservation.reservation_code, view_cs_reservation.reservation_date, view_cs_reservation.status as reservation_status'
            ,'ar_debitnote_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/folio/debit/3/' . $row->debitnote_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL) || check_session_action($menu_id, STATUS_DELETE)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->debitnote_id . '" data-code="' . $row->debit_no . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->debit_amount > 0){
                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->debitnote_id . '" name="ischecked[]" ' . (check_session_action($menu_id, STATUS_POSTED) ? ($row->status == STATUS_NEW ? '' : 'disabled') : 'disabled') . '/>',
                    $row->debit_no,
                    dmy_from_db($row->debit_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    format_num($row->debit_amount,0),
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
                    $row->debit_no,
                    dmy_from_db($row->debit_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    format_num($row->debit_amount,0),
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

    public function debit_form($debitnote_id = 0){
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

        $data['debitnote_id'] = $debitnote_id;

        if($debitnote_id > 0){
            $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_debitnote_header.reservation_id'
            );
            $qry = $this->mdl_finance->getJoin('ar_debitnote_header.*, view_cs_reservation.tenant_fullname, view_cs_reservation.reservation_code, view_cs_reservation.reservation_date, view_cs_reservation.status as reservation_status'
                ,'ar_debitnote_header', $joins, array('debitnote_id' => $debitnote_id));

            $data['row'] = $qry->row();
            //$data['detail'] = $this->db->get_where('ar_debitnote_detail', array('debitnote_id' => $debitnote_id));
            $data['detail'] = $this->db->query('SELECT det.*, coa.coa_id, coa.coa_desc FROM ar_debitnote_detail det
                                                JOIN gl_coa coa ON coa.coa_code = det.coa_code
                                                WHERE det.debitnote_id = ' . $debitnote_id);

            //Get Current Available Amount
            $available_amount = 0;
            if($data['row']->status == STATUS_NEW){
                $sql = $this->db->query("SELECT pending_amount FROM fxnARUnallocatedByDate('" . ymd_from_db($data['row']->debit_date) . "')
                                     WHERE reservation_id = " . $data['row']->reservation_id);
                if($sql->num_rows() > 0){
                    $available_amount = $sql->row()->pending_amount;
                }

                $sql = $this->db->query("SELECT ISNULL(SUM(debit_amount),0) as sum_debit FROM ar_debitnote_header
                                         WHERE reservation_id = " . $data['row']->reservation_id . ' AND status = ' . STATUS_NEW . '
                                         AND debitnote_id <> ' . $debitnote_id);
                if($sql->num_rows() > 0){
                    $available_amount = $available_amount - $sql->row()->sum_debit;
                }
            }else{
                $available_amount = $data['row']->debit_amount;
            }

            $data['available_debit'] = $available_amount;
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/debit/debit_form', $data);
        $this->load->view('layout/footer');
    }

    public function action_request(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $debitnote_id = $_POST['debitnote_id'];
        $data['status'] = $_POST['action'];
        $data['cancel_note'] = $_POST['reason'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Debit Note';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $debitnote_id;
        $data_log['feature_id'] = Feature::FEATURE_AR_REFUND;

        if($debitnote_id > 0 && $data['status'] > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $qry = $this->db->get_where('ar_debitnote_header', array('debitnote_id' => $debitnote_id));
            if($qry->num_rows() > 0){
                $row = $qry->row();

                if($data['status'] == STATUS_APPROVE){
                    $this->mdl_general->update('ar_debitnote_header', array('debitnote_id' => $debitnote_id), $data);

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
                        $this->mdl_general->update('ar_debitnote_header', array('debitnote_id' => $debitnote_id), $data);

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

    public function submit_debit(){
        $valid = true;

        if(isset($_POST)){
            $debitnoteId = $_POST['debitnote_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['debit_date'] = dmy_to_ymd($_POST['debit_date']);

            $data['reservation_id'] = $_POST['reservation_id'];
            $data['debit_amount'] = $_POST['total_debit'];
            $data['debit_remark'] = $_POST['debit_remark'];

            if($debitnoteId > 0){
                $qry = $this->db->get_where('ar_debitnote_header', array('debitnote_id' => $debitnoteId));
                $row = $qry->row();

                $arr_date = explode('-', $data['debit_date']);
                $arr_date_old = explode('-', ymd_from_db($row->debit_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['debit_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_DEBIT_NOTE, $data['debit_date']);

                    if($data['debit_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Failed generating code.');
                    }
                }

                if($valid){
                    $this->mdl_general->update('ar_debitnote_header', array('debitnote_id' => $debitnoteId), $data);

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';
                        $valid = $this->insertDebitEntries($debitnoteId, $row->debit_remark);

                        //echo '<br>step 5 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }

                    if($valid){
                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_subject'] = get_action_name(STATUS_EDIT, false) . ' AR Debit Note';
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $debitnoteId;
                        $data_log['feature_id'] = Feature::FEATURE_AR_DEBIT_NOTE;
                        $data_log['action_type'] = STATUS_EDIT;
                        $this->db->insert('app_log', $data_log);
                    }
                }
            }
            else {
                $data['debit_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_DEBIT_NOTE, $data['debit_date']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                if($data['debit_no'] != ''){
                    $this->db->insert('ar_debitnote_header', $data);
                    $debitnoteId = $this->db->insert_id();

                    if($debitnoteId > 0){

                        $valid = $this->insertDebitEntries($debitnoteId, $data['debit_remark']);
                        if($valid){
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Debit Note';
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $debitnoteId;
                            $data_log['feature_id'] = Feature::FEATURE_AR_DEBIT_NOTE;
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
                redirect(base_url('ar/folio/debit/3/' . $debitnoteId . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('ar/folio/debit/1.tpd'),true);
                }
                else {
                    redirect(base_url('ar/folio/debit/3/' . $debitnoteId . '.tpd'));
                }
            }
        }
    }

    private function insertDebitEntries($debitnoteId = 0, $remark = ''){
        $valid = true;

        if($debitnoteId > 0 && isset($_POST)){
            $ids = isset($_POST['detail_id']) ? $_POST['detail_id'] : array();
            $coa_ids = isset($_POST['coa_id']) ? $_POST['coa_id'] : array();
            $coa_codes = isset($_POST['coa_code']) ? $_POST['coa_code'] : array();
            $detail_descs = isset($_POST['detail_desc']) ? $_POST['detail_desc'] : array();
            $debit_amounts = isset($_POST['debit_value']) ? $_POST['debit_value'] : array();

            if(count($coa_ids) > 0 && count($coa_codes) > 0){
                for ($i = 0; $i < count($coa_ids); $i++) {
                    if($valid){
                        if(isset($coa_codes[$i]) && isset($debit_amounts[$i])){
                            $detail['debitnote_id'] = $debitnoteId;
                            $detail['coa_code'] = $coa_codes[$i];
                            $detail['description'] = $detail_descs[$i] != '' ? $detail_descs[$i] : $remark;
                            $detail['amount'] = $debit_amounts[$i];
                            $detail['tax'] = 0;
                            $detail['subtotal'] = $detail['amount'] + $detail['tax'];
                            $detail['taxtype_id'] = 0;
                            $detail['paid_amount'] = 0;
                            $detail['paid_tax'] = 0;

                            if($ids[$i] <= 0){
                                $detail['status'] = STATUS_NEW;

                                $this->db->insert('ar_debitnote_detail', $detail);
                                $insertID = $this->db->insert_id();

                                if($insertID <= 0){
                                    $valid = false;
                                }
                            }else{
                                $this->mdl_general->update('ar_debitnote_detail', array('detail_id' => $ids[$i]), $detail);
                            }
                        }
                    }
                }
            }
        }

        return $valid;
    }

    public function posting_debits(){
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

                foreach( $_POST['ischecked'] as $debitnoteId){
                    //echo '[posting_refunds] ... ' . $debitnoteId;
                    if($debitnoteId > 0){
                        //AR
                        //  Sales
                        $detail = array();

                        $totalDebit = 0;
                        $totalCredit = 0;

                        $qryDetails = $this->db->query('SELECT det.*, coa.coa_id, coa.coa_desc FROM ar_debitnote_detail det
                                            JOIN gl_coa coa ON coa.coa_code = det.coa_code
                                            WHERE det.debitnote_id = ' . $debitnoteId);

                        if($qryDetails->num_rows() > 0){
                            foreach($qryDetails->result_array() as $det){
                                $rowdet = array();
                                $rowdet['coa_id'] = $det['coa_id'];
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $det['description'];
                                $rowdet['journal_debit'] = 0;
                                $rowdet['journal_credit'] = $det['subtotal'];
                                $rowdet['reference_id'] = 0;
                                $rowdet['transtype_id'] = 0;

                                array_push($detail, $rowdet);

                                $totalCredit += $det['subtotal'];

                                //UPDATE DN Detail Paid
                                $dn_detail = array();
                                $dn_detail['paid_amount'] = $det['amount'];
                                $dn_detail['paid_tax'] = $det['tax'];
                                $this->mdl_general->update('ar_debitnote_detail', array('detail_id' => $det['detail_id']), $dn_detail);
                            }
                        }

                        $qryHeader = $this->db->get_where('ar_debitnote_header', array('debitnote_id' => $debitnoteId));

                        if($totalCredit > 0 && $qryHeader->num_rows() > 0){
                            $head = $qryHeader->row();

                            //$reservation = $this->db->get_where('cs_reservation_header', array('reservation_id' => $head->reservation_id));
                            //AR
                            $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                            if($head->company_id > 0){
                                $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                            }
                            /*
                            if($reservation->num_rows() > 0){
                                $reservation = $reservation->row();
                                if($reservation->reservation_type == RES_TYPE::CORPORATE){
                                    $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                                }
                            }*/

                            if($specAR['coa_id'] > 0){
                                $rowdet = array();
                                $rowdet['coa_id'] = $specAR['coa_id'];
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $head->debit_remark;
                                $rowdet['journal_debit'] = $totalCredit;
                                $rowdet['journal_credit'] = 0;
                                $rowdet['reference_id'] = 0;
                                $rowdet['transtype_id'] = $specAR['transtype_id'];

                                array_push($detail, $rowdet);

                                $totalDebit = $totalCredit;
                            }

                            //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                            if($totalDebit == $totalCredit){
                                //$posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : ymd_from_db($head->debit_date);
                                $posting_date = ymd_from_db($head->debit_date);

                                $header = array();
                                $header['journal_no'] = $head->debit_no;
                                $header['journal_date'] = $posting_date;
                                $header['journal_remarks'] = $head->debit_remark;
                                $header['modul'] = GLMOD::GL_MOD_AR;
                                $header['journal_amount'] = $totalDebit;
                                $header['reference'] = '';

                                $valid = $this->mdl_finance->postJournal($header,$detail);

                                if($valid){
                                    $data['status']= STATUS_CLOSED;
                                    $this->mdl_general->update('ar_debitnote_header', array('debitnote_id' => $debitnoteId), $data);

                                    $rowcount++;
                                }
                            }
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
                if($isCorporate > 0){
                    redirect(base_url('ar/corporate_bill/debit/1.tpd'));
                }else{
                    redirect(base_url('ar/folio/debit/1.tpd'));
                }
            }
            else {
                if($isCorporate > 0){
                    redirect(base_url('ar/corporate_bill/debit/1.tpd'));
                }else{
                    redirect(base_url('ar/folio/debit/1.tpd'));
                }
            }
        }
    }

    public function ajax_posting_debit_by_id(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;

        $isCorporate = 0;
        $debitnoteId = 0;

        if(isset($_POST['debitnote_id'])){
            $debitnoteId = $_POST['debitnote_id'];
        }

        if(isset($_POST['is_corporate'])){
            $isCorporate = $_POST['is_corporate'];
        }

        if($debitnoteId > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            //AR
            //  Sales
            $detail = array();

            $totalDebit = 0;
            $totalCredit = 0;

            //$qryDetails = $this->db->get_where('ar_debitnote_detail', array('debitnote_id'=> $debitnoteId));
            $qryDetails = $this->db->query('SELECT det.*, coa.coa_id, coa.coa_desc FROM ar_debitnote_detail det
                                            JOIN gl_coa coa ON coa.coa_code = det.coa_code
                                            WHERE det.debitnote_id = ' . $debitnoteId);

            if($qryDetails->num_rows() > 0){
                foreach($qryDetails->result_array() as $det){
                    $rowdet = array();
                    $rowdet['coa_id'] = $det['coa_id'];
                    $rowdet['dept_id'] = 0;
                    $rowdet['journal_note'] = $det['description'];
                    $rowdet['journal_debit'] = 0;
                    $rowdet['journal_credit'] = $det['subtotal'];
                    $rowdet['reference_id'] = 0;
                    $rowdet['transtype_id'] = 0;

                    array_push($detail, $rowdet);

                    $totalCredit += $det['subtotal'];

                    //UPDATE DN Detail Paid
                    $dn_detail = array();
                    $dn_detail['paid_amount'] = $det['amount'];
                    $dn_detail['paid_tax'] = $det['tax'];
                    $this->mdl_general->update('ar_debitnote_detail', array('detail_id' => $det['detail_id']), $dn_detail);
                }
            }

            $qryHeader = $this->db->get_where('ar_debitnote_header', array('debitnote_id' => $debitnoteId));

            if($totalCredit > 0 && $qryHeader->num_rows() > 0){
                $head = $qryHeader->row();

                //$reservation = $this->db->get_where('cs_reservation_header', array('reservation_id' => $head->reservation_id));
                //AR
                $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                if($head->company_id > 0){
                    $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                }
                /*
                if($reservation->num_rows() > 0){
                    $reservation = $reservation->row();
                    if($reservation->reservation_type == RES_TYPE::CORPORATE){
                        $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                    }
                }*/

                if($specAR['coa_id'] > 0){
                    $rowdet = array();
                    $rowdet['coa_id'] = $specAR['coa_id'];
                    $rowdet['dept_id'] = 0;
                    $rowdet['journal_note'] = $head->debit_remark;
                    $rowdet['journal_debit'] = $totalCredit;
                    $rowdet['journal_credit'] = 0;
                    $rowdet['reference_id'] = 0;
                    $rowdet['transtype_id'] = $specAR['transtype_id'];

                    array_push($detail, $rowdet);

                    $totalDebit = $totalCredit;
                }

                //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                if($totalDebit == $totalCredit){
                    //$posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : ymd_from_db($head->debit_date);
                    $posting_date = ymd_from_db($head->debit_date);

                    $header = array();
                    $header['journal_no'] = $head->debit_no;
                    $header['journal_date'] = $posting_date;
                    $header['journal_remarks'] = $head->debit_remark;
                    $header['modul'] = GLMOD::GL_MOD_AR;
                    $header['journal_amount'] = $totalDebit;
                    $header['reference'] = '';

                    $valid = $this->mdl_finance->postJournal($header,$detail);

                    if($valid){
                        $data['status']= STATUS_CLOSED;
                        $this->mdl_general->update('ar_debitnote_header', array('debitnote_id' => $debitnoteId), $data);
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
                }
                else
                {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = $header['journal_no'] . ' successfully posted.';
                    if($isCorporate > 0){
                        $result['redirect_link'] = base_url('ar/corporate_bill/debit/3/'. $debitnoteId .'.tpd');
                    }else{
                        $result['redirect_link'] = base_url('ar/folio/debit/3/'. $debitnoteId .'.tpd');
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

    #endregion

    #region Debit History

    public function debit_history($type = 1){
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
        $this->load->view('ar/debit/debit_history.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_debit_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_debitnote_header.status'] = STATUS_CLOSED;
        $where['ar_debitnote_header.receipt_id <='] = 0;
        $where['ar_debitnote_header.company_id <='] = 0;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_debitnote_header.debit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_debitnote_header.debit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_debitnote_header.debit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        }

        $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_debitnote_header.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_debitnote_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_debitnote_header.debit_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_debitnote_header.debit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_debitnote_header.debit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_debitnote_header.*, view_cs_reservation.tenant_fullname, view_cs_reservation.reservation_code, view_cs_reservation.reservation_date, view_cs_reservation.status as reservation_status'
            ,'ar_debitnote_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/folio/debit/3/' . $row->debitnote_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('ar/report/pdf_debitnote/' . $row->debitnote_id) . '" class="blue-ebonyclay" target="_blank">Debit Note</a> </li>';
                $btn_action .= '<li> <a href="' . base_url('ar/report/pdf_debitvoucher/' . $row->debitnote_id) . '" class="blue-ebonyclay" target="_blank">Voucher</a> </li>';
            }

            if($row->debit_amount > 0){
                $records["data"][] = array(
                    $i,
                    $row->debit_no,
                    dmy_from_db($row->debit_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    format_num($row->debit_amount,0),
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

    #endregion

    #region Corporate Debit Note

    public function get_corp_debit_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();

        $where['ar_debitnote_header.receipt_id >'] = 0;
        $where['ar_debitnote_header.status'] = STATUS_NEW;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_debitnote_header.debit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_debitnote_header.debit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_debitnote_header.debit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_receipt'])){
            if($_REQUEST['filter_receipt'] != ''){
                $like['ar_debitnote_header.receipt_no'] = $_REQUEST['filter_receipt'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['ar_debitnote_header.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_debitnote_header as ar_debitnote_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_debitnote_header.debit_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_debitnote_header.debit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_debitnote_header.debit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ar_debitnote_header.receipt_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'ar_debitnote_header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_debitnote_header.*'
            ,'vw_ar_debitnote_header as ar_debitnote_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/debit/3/' . $row->debitnote_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL) || check_session_action($menu_id, STATUS_DELETE)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->debitnote_id . '" data-code="' . $row->debit_no . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->debit_amount > 0){
                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->debitnote_id . '" name="ischecked[]" ' . (check_session_action($menu_id, STATUS_POSTED) ? ($row->status == STATUS_NEW ? '' : 'disabled') : 'disabled') . '/>',
                    $row->debit_no,
                    dmy_from_db($row->debit_date),
                    $row->receipt_no,
                    $row->company_name,
                    format_num($row->debit_amount,0),
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
                    $row->debit_no,
                    dmy_from_db($row->debit_date),
                    $row->receipt_no,
                    $row->company_name,
                    format_num($row->debit_amount,0),
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

    public function get_corp_debit_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();

        $where['ar_debitnote_header.receipt_id >'] = 0;
        $where_str = 'ar_debitnote_header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_debitnote_header.debit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_debitnote_header.debit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_debitnote_header.debit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_receipt'])){
            if($_REQUEST['filter_receipt'] != ''){
                $like['ar_debitnote_header.receipt_no'] = $_REQUEST['filter_receipt'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['ar_debitnote_header.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_debitnote_header as ar_debitnote_header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_debitnote_header.debit_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_debitnote_header.debit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_debitnote_header.debit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ar_debitnote_header.receipt_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'ar_debitnote_header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_debitnote_header.*'
            ,'vw_ar_debitnote_header as ar_debitnote_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart,false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/debit/3/' . $row->debitnote_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('ar/report/pdf_debitnote/' . $row->debitnote_id) . '/7.tpd" class="blue-ebonyclay" target="_blank">Debit Note</a> </li>';
                $btn_action .= '<li> <a href="' . base_url('ar/report/pdf_debitvoucher/' . $row->debitnote_id) . '/7.tpd" class="blue-ebonyclay" target="_blank">Voucher</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->debit_no,
                dmy_from_db($row->debit_date),
                $row->receipt_no,
                $row->company_name,
                format_num($row->debit_amount,0),
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

    public function corp_debit_form($debitnote_id = 0){
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

        $data['debitnote_id'] = $debitnote_id;

        if($debitnote_id > 0){
            $joins = array();
            $qry = $this->mdl_finance->getJoin('ar_debitnote_header.*'
                ,'vw_ar_debitnote_header as ar_debitnote_header', $joins, array('debitnote_id' => $debitnote_id));

            $data['row'] = $qry->row();

            $data['detail'] = $this->db->query('SELECT det.*, coa.coa_id, coa.coa_desc FROM ar_debitnote_detail det
                                                JOIN gl_coa coa ON coa.coa_code = det.coa_code
                                                WHERE det.debitnote_id = ' . $debitnote_id);

            //Get Current Available Amount
            $available_amount = 0;
            if($data['row']->status == STATUS_NEW){
                $sql = $this->db->query("SELECT unallocated_amount as pending_amount FROM fxnARUnallocatedByDateCorp('" . ymd_from_db($data['row']->debit_date) . "')
                                     WHERE receipt_id = " . $data['row']->receipt_id);
                if($sql->num_rows() > 0){
                    $available_amount = $sql->row()->pending_amount;
                }

                $sql = $this->db->query("SELECT ISNULL(SUM(debit_amount),0) as sum_debit FROM ar_debitnote_header
                                         WHERE reservation_id <= 0 AND receipt_id = " . $data['row']->receipt_id . ' AND status = ' . STATUS_NEW . '
                                         AND debitnote_id <> ' . $debitnote_id);
                if($sql->num_rows() > 0){
                    $available_amount = $available_amount - $sql->row()->sum_debit;
                }
            }else{
                $available_amount = $data['row']->debit_amount;
            }

            $data['available_debit'] = $available_amount;
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/debit/corp_debit_form', $data);
        $this->load->view('layout/footer');
    }

    public function submit_corp_debit(){
        $valid = true;

        if(isset($_POST)){
            $debitnoteId = $_POST['debitnote_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['debit_date'] = dmy_to_ymd($_POST['debit_date']);

            $data['receipt_id'] = $_POST['receipt_id'];
            $data['company_id'] = $_POST['company_id'];
            $data['reservation_id'] = $_POST['reservation_id'];
            $data['debit_amount'] = $_POST['total_debit'];
            $data['debit_remark'] = $_POST['debit_remark'];

            if($debitnoteId > 0){
                $qry = $this->db->get_where('ar_debitnote_header', array('debitnote_id' => $debitnoteId));
                $row = $qry->row();

                $arr_date = explode('-', $data['debit_date']);
                $arr_date_old = explode('-', ymd_from_db($row->debit_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['debit_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_DEBIT_NOTE, $data['debit_date']);

                    if($data['debit_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Failed generating code.');
                    }
                }

                if($valid){
                    $this->mdl_general->update('ar_debitnote_header', array('debitnote_id' => $debitnoteId), $data);

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';
                        $valid = $this->insertDebitEntries($debitnoteId, $row->debit_remark);

                        //echo '<br>step 5 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }

                    if($valid){
                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_subject'] = get_action_name(STATUS_EDIT, false) . ' AR Debit Note';
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $debitnoteId;
                        $data_log['feature_id'] = Feature::FEATURE_AR_DEBIT_NOTE;
                        $data_log['action_type'] = STATUS_EDIT;
                        $this->db->insert('app_log', $data_log);
                    }
                }
            }
            else {
                $data['debit_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_DEBIT_NOTE, $data['debit_date']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                if($data['debit_no'] != ''){
                    $this->db->insert('ar_debitnote_header', $data);
                    $debitnoteId = $this->db->insert_id();

                    if($debitnoteId > 0){

                        $valid = $this->insertDebitEntries($debitnoteId, $data['debit_remark']);
                        if($valid){
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Debit Note';
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $debitnoteId;
                            $data_log['feature_id'] = Feature::FEATURE_AR_DEBIT_NOTE;
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
                redirect(base_url('ar/corporate_bill/debit/3/' . $debitnoteId . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('ar/corporate_bill/debit/1.tpd'),true);
                }
                else {
                    redirect(base_url('ar/corporate_bill/debit/3/' . $debitnoteId . '.tpd'));
                }
            }
        }
    }

    #endregion

    #region Modal Lookup Form

    public function ajax_unallocated_reservation(){
        $this->load->view('ar/refund/ajax_modal_unallocated');
    }

    public function get_modal_reservation($num_index = 0, $reservation_id = 0, $debitnote_id = 0){
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

        $where_str = ' view_cs_reservation.status IN(' . ORDER_STATUS::CHECKOUT . ',' .  ORDER_STATUS::CHECKIN . ') ';

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

        $qry = $this->mdl_finance->getJoin("ar.*, view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname, view_cs_reservation.reservation_type, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, view_cs_reservation.status as reservation_status ","fxnARUnallocatedByDate('" . $server_date . "') AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, "", array(), $where_str);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $pending_refund = 0;

            $existdebit = $this->db->query('SELECT ISNULL(SUM(debit_amount),0) as debit_total FROM ar_debitnote_header
                                               WHERE reservation_id = ' . $row->reservation_id . ' AND status = ' . STATUS_NEW . '
                                               AND debitnote_id <> ' . $debitnote_id);
            if($existdebit->num_rows() > 0){
                $pending_refund = $existdebit->row()->debit_total;
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

    public function get_modal_unallocated_corp($num_index = 0, $receipt_id = 0, $debitnote_id = 0){
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
            $pending_debit = 0;

            $existdebit = $this->db->query('SELECT ISNULL(SUM(debit_amount),0) as debit_total FROM ar_debitnote_header
                                               WHERE reservation_id <= 0 AND receipt_id = ' . $row->receipt_id . ' AND status = ' . STATUS_NEW . '
                                               AND debitnote_id <> ' . $debitnote_id);
            if($existdebit->num_rows() > 0){
                $pending_debit = $existdebit->row()->debit_total;
            }

            $unallocated = $row->unallocated_amount - $pending_debit;

            $attr = '';
            $attr .= ' data-receipt-id="' . $row->receipt_id . '" ';
            $attr .= ' data-receipt-no="' . $row->receipt_no . '" ';
            $attr .= ' data-company-id="' . $row->company_id . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-amount="' . $unallocated . '"';
            $attr .= ' data-index="' . $num_index . '" ';

            if ($receipt_id == $row->receipt_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }else{
                $text = "Select";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-company" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

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

    #endregion

}