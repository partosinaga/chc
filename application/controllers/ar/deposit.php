<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deposit extends CI_Controller {
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
        $this->deposit_manage();
    }

    #region Debit Manage

    public function get_deposit_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_deposit_header.status'] = STATUS_NEW;
        $where['ar_deposit_header.reservation_id <='] = 0;
        $where['ar_deposit_header.company_id >'] = 0;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_deposit_header.deposit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_deposit_header.deposit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_deposit_header.deposit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ms_company.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array('ms_company' => 'ms_company.company_id = ar_deposit_header.company_id',
                       'ms_payment_type' => 'ms_payment_type.paymenttype_id = ar_deposit_header.paymenttype_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_deposit_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_deposit_header.deposit_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_deposit_header.deposit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_deposit_header.deposit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ms_company.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'ar_deposit_header.paymenttype_id ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_deposit_header.*, ms_company.company_name, ms_payment_type.payment_type'
            ,'ar_deposit_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/deposit/3/' . $row->deposit_id) . '.tpd"><i class="fa fa-file"></i> Open</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL) || check_session_action($menu_id, STATUS_DELETE)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->deposit_id . '" data-code="' . $row->deposit_no . '"><i class="fa fa-remove"></i>' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->deposit_paymentamount > 0){
                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->deposit_id . '" name="ischecked[]" ' . (check_session_action($menu_id, STATUS_POSTED) ? ($row->status == STATUS_NEW ? '' : 'disabled') : 'disabled') . '/>',
                    $row->deposit_no,
                    dmy_from_db($row->deposit_date),
                    $row->company_name,
                    PAYMENT_TYPE::caption($row->payment_type),
                    format_num($row->deposit_bankcharges,0),
                    format_num($row->deposit_paymentamount,0),
                    format_num(round($row->deposit_paymentamount + $row->deposit_bankcharges,0),0),
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
                    $row->deposit_no,
                    dmy_from_db($row->deposit_date),
                    $row->company_name,
                    PAYMENT_TYPE::caption($row->payment_type),
                    format_num($row->deposit_bankcharges,0),
                    format_num($row->deposit_paymentamount,0),
                    format_num(round($row->deposit_paymentamount + $row->deposit_bankcharges,0),0),
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

    public function get_deposit_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        //$where['ar_deposit_header.status'] = STATUS_POSTED;
        $where['ar_deposit_header.reservation_id <='] = 0;
        $where['ar_deposit_header.company_id >'] = 0;

        $where_str = 'ar_deposit_header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_deposit_header.deposit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_deposit_header.deposit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_deposit_header.deposit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ms_company.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array('ms_company' => 'ms_company.company_id = ar_deposit_header.company_id',
            'ms_payment_type' => 'ms_payment_type.paymenttype_id = ar_deposit_header.paymenttype_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_deposit_header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_deposit_header.deposit_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_deposit_header.deposit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_deposit_header.deposit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ms_company.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'ar_deposit_header.paymenttype_id ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_deposit_header.*, ms_company.company_name, ms_payment_type.payment_type'
            ,'ar_deposit_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/deposit/3/' . $row->deposit_id) . '.tpd"><i class="fa fa-file"></i> Open</a> </li>';
            if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('ar/report/pdf_official_receipt/' . $row->deposit_no) . '.tpd" target="_blank"><i class="fa fa-print"></i> Receipt</a> </li>';

                $btn_action .= '<li> <a href="' . base_url('ar/deposit/pdf_depositvoucher/' . $row->deposit_id) . '" class="blue-ebonyclay" target="_blank"><i class="fa fa-print"></i> Voucher</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->deposit_no,
                dmy_from_db($row->deposit_date),
                $row->company_name,
                PAYMENT_TYPE::caption($row->payment_type),
                format_num($row->deposit_bankcharges,0),
                format_num($row->deposit_paymentamount,0),
                format_num(round($row->deposit_paymentamount + $row->deposit_bankcharges,0),0),
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

    public function deposit_form($id = 0){
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
        //array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        $data['deposit_id'] = $id;

        $deposittypes = $this->db->query('select * from ms_deposit_type where status = ' . STATUS_NEW . 'order by deposit_key');
        if($deposittypes->num_rows() > 0){
            $data['deposittypes'] = $deposittypes->result_array();
        }
        if($id > 0){
            $joins = array('ms_company' => 'ms_company.company_id = ar_deposit_header.company_id',
                           'ms_payment_type' => 'ms_payment_type.paymenttype_id = ar_deposit_header.paymenttype_id');
            $qry = $this->mdl_finance->getJoin('ar_deposit_header.*, ms_company.company_name, ms_payment_type.payment_type ','ar_deposit_header', $joins, array('deposit_id' => $id));
            $data['deposit'] = $qry->row();

            $details = $this->db->get_where('ar_deposit_detail', array('deposit_id' => $data['deposit_id']));
            $data['row_det'] = $details->result_array();
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/deposit/deposit_form', $data);
        $this->load->view('layout/footer');
    }

    public function submit_deposit(){
        $valid = true;

        if(isset($_POST)){
            $depositId = $_POST['deposit_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['deposit_date'] = dmy_to_ymd($_POST['deposit_date']);

            $data['reservation_id'] = 0;
            $data['company_id'] = $_POST['company_id'];
            $data['bankaccount_id'] = $_POST['bankaccount_id'];
            $data['paymenttype_id'] = $_POST['paymenttype_id'];
            $gross_amount = $_POST['deposit_grossamount'];
            $data['deposit_paymentamount'] = $_POST['deposit_paymentamount'];
            $data['deposit_bankcharges'] = $_POST['deposit_bankcharges'];
            $data['bank_charge_percent'] = $_POST['bank_charge_percent'];
            $data['deposit_desc'] = $_POST['deposit_desc'];
            $data['card_name'] = isset($_POST['creditcard_name']) ? $_POST['creditcard_name'] : '';
            $data['card_no'] = isset($_POST['creditcard_no']) ? $_POST['creditcard_no'] : '';
            $data['card_expiry_month'] = isset($_POST['creditcard_expiry_month']) ? $_POST['creditcard_expiry_month'] : '';
            $data['card_expiry_year'] = isset($_POST['creditcard_expiry_year']) ? $_POST['creditcard_expiry_year'] : '';

            //Find Bank account by paymenttype_id
            $paymenttype = $this->db->query('SELECT * FROM ms_payment_type WHERE paymenttype_id = ' . $data['paymenttype_id']);
            if($paymenttype->num_rows() > 0){
                $paymenttype = $paymenttype->row_array();

                if($data['bankaccount_id'] <= 0){
                    if($paymenttype['payment_type'] != PAYMENT_TYPE::BANK_TRANSFER && $paymenttype['payment_type'] != PAYMENT_TYPE::CASH_ONLY){
                        $qry = $this->db->query("SELECT fn_bank_account.bankaccount_id FROM fn_bank_account
                                                     JOIN gl_coa on gl_coa.coa_id = fn_bank_account.coa_id
                                                     JOIN ms_payment_type on ms_payment_type.coa_code = gl_coa.coa_code
                                                     WHERE fn_bank_account.status = " . STATUS_NEW . "
                                                           AND ms_payment_type.paymenttype_id = " . $data['paymenttype_id']);
                        if($qry->num_rows() > 0){
                            $data['bankaccount_id'] = $qry->row()->bankaccount_id;
                        }else{
                            $data['bankaccount_id'] = 0;
                        }
                    }
                }

                //Re-calculate payment charges
                if($paymenttype['payment_type'] == PAYMENT_TYPE::CREDIT_CARD){
                    if($data['bank_charge_percent'] <= 0 && $data['deposit_bankcharges'] > 0){
                        $data['bank_charge_percent'] = round(($data['deposit_bankcharges'] / $gross_amount),2);
                    }

                    if($data['deposit_bankcharges'] <= 0 && $data['bank_charge_percent'] > 0){
                        $data['deposit_bankcharges'] = round(($data['bank_charge_percent']/100)* $gross_amount,0);
                    }
                }else{
                    $data['bank_charge_percent'] = 0;
                    $data['deposit_bankcharges'] = 0;
                }

                $data['deposit_paymentamount'] = round($gross_amount - $data['deposit_bankcharges']);
            }

            if($depositId > 0){
                $qry = $this->db->get_where('ar_deposit_header', array('deposit_id' => $depositId));
                $row = $qry->row();

                $arr_date = explode('-', $data['deposit_date']);
                $arr_date_old = explode('-', ymd_from_db($row->deposit_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['deposit_no'] = $this->generate_depositno($data, $data['deposit_date']);

                    if($data['deposit_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Failed generating code.');
                    }
                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('ar_deposit_header', array('deposit_id' => $depositId), $data);

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';
                        $valid = $this->insertDepositEntries($depositId);

                        //echo '<br>step 5 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }
                }
            }
            else {
                $data['deposit_no'] = $this->generate_depositno($data, $data['deposit_date']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                if($data['deposit_no'] != ''){
                    $this->db->insert('ar_deposit_header', $data);
                    $depositId = $this->db->insert_id();

                    if($depositId > 0){
                        $valid = $this->insertDepositEntries($depositId);

                        //SET DepositID on ar_receipt
                        $update['deposit_id'] = $depositId;
                        $this->mdl_general->update('ar_receipt', array('receipt_no' => $data['deposit_no'], 'status' => FLAG_DEPOSIT), $update);

                        if($valid){
                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Transaction successfully created.');
                        }

                    }else{
                        //echo 'deposit id = 0 ' ;
                        $valid = false;
                    }
                }else{
                    //echo 'deposit id null ' ;

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
                redirect(base_url('ar/corporate_bill/deposit/3/' . $depositId . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('ar/corporate_bill/deposit/1.tpd'),true);
                }
                else {
                    redirect(base_url('ar/corporate_bill/deposit/3/' . $depositId . '.tpd'));
                }
            }
        }
    }

    private function insertDepositEntries($depositId = 0){
        $valid = true;

        if($depositId > 0 && isset($_POST)){
            $detail_ids = isset($_POST['detail_id']) ? $_POST['detail_id'] : array();
            $deposittype_ids = isset($_POST['deposittype_id']) ? $_POST['deposittype_id'] : array();
            $detail_descs = isset($_POST['detail_desc']) ? $_POST['detail_desc'] : array();
            $deposit_amounts = isset($_POST['deposit_amount']) ? $_POST['deposit_amount'] : array();

            if(count($deposittype_ids) > 0){
                //echo '<br>Count detail ' . count($unit_ids);
                for ($i = 0; $i < count($deposittype_ids); $i++) {
                    if($valid){
                        if(isset($detail_ids[$i]) && isset($deposittype_ids[$i]) && isset($detail_descs[$i]) && isset($deposit_amounts[$i])){
                            $detail['deposittype_id'] = $deposittype_ids[$i];
                            $detail['detail_desc'] = $detail_descs[$i];
                            $detail['deposit_amount'] = $deposit_amounts[$i];

                            if($detail_ids[$i] <= 0){
                                $detail['deposit_id'] = $depositId;
                                $detail['status'] = STATUS_NEW;

                                $this->db->insert('ar_deposit_detail', $detail);
                                $insertID = $this->db->insert_id();

                                if($insertID <= 0){
                                    $valid = false;
                                }
                            }else{
                                $this->mdl_general->update('ar_deposit_detail', array('detail_id' => $detail_ids[$i]), $detail);
                            }
                        }
                    }else{
                        break;
                    }
                }
            }
        }

        return $valid;
    }

    public function generate_depositno($header = array(), $serverdate, $old_doc_no = ''){
        $result = '';

        if(count($header) > 0 && isset($serverdate)){
            if($old_doc_no != ''){
                $this->db->delete('ar_receipt', array('receipt_no' => $old_doc_no, 'status' => FLAG_DEPOSIT));
            }
            //echo '<br/>generate_journalno RV';
            $rv['receipt_date'] = $header['deposit_date'];
            $rv['created_by'] = my_sess('user_id');
            $rv['created_date']  = date('Y-m-d H:i:s');
            $rv['receipt_paymentamount'] = $header['deposit_paymentamount'];
            $rv['bank_charge_percent'] = $header['bank_charge_percent'];
            $rv['receipt_bankcharges'] = $header['deposit_bankcharges'];
            $rv['receipt_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_RECEIPT, $header['deposit_date']);
            $rv['status'] = FLAG_DEPOSIT;

            $this->db->insert('ar_receipt', $rv);
            $newID = $this->db->insert_id();

            if($newID > 0 && trim($rv['receipt_no']) != ''){
                $result = $rv['receipt_no'];
            }
        }

        return $result;
    }

    public function posting_deposits(){
        $valid = true;

        if(isset($_POST)){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            if(isset($_POST['ischecked'])){
                $rowcount = 0;

                $joins = array('ms_deposit_type'=>'ms_deposit_type.deposittype_id = ar_deposit_detail.deposittype_id',
                    'gl_coa' => 'gl_coa.coa_code = ms_deposit_type.coa_code');

                foreach( $_POST['ischecked'] as $val){
                    //insert post journal
                    $detail = array();

                    $totalDebit = 0;
                    $totalCredit = 0;
                    $qryDetails = $this->mdl_finance->getJoin('ar_deposit_detail.*, ms_deposit_type.coa_code, gl_coa.coa_id'
                        ,'ar_deposit_detail', $joins , array('deposit_id' => $val));

                    if($qryDetails->num_rows() > 0){
                        foreach($qryDetails->result() as $det){
                            $rowdet = array();
                            $rowdet['coa_id'] = $det->coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $det->detail_desc;
                            $rowdet['journal_debit'] = 0;
                            $rowdet['journal_credit'] = $det->deposit_amount;
                            $rowdet['reference_id'] = 0;
                            $rowdet['transtype_id'] = 0;

                            array_push($detail, $rowdet);

                            $totalCredit += $rowdet['journal_credit'];
                        }
                    }

                    $qryHeader = $this->db->get_where('ar_deposit_header', array('deposit_id' => $val));

                    if($totalCredit > 0 && $qryHeader->num_rows() > 0){
                        $head = $qryHeader->row();

                        $payments = $this->db->get_where('ms_payment_type', array('paymenttype_id' => $head->paymenttype_id));
                        if($payments->num_rows() > 0){
                            $payment_type = $payments->row();

                            //Add (C) Bank Charge (if any)
                            if($head->deposit_bankcharges > 0 && $payment_type->payment_type == PAYMENT_TYPE::CREDIT_CARD){
                                $specCharge = FNSpec::get(FNSpec::BANK_CHARGE);

                                if($specCharge['coa_id'] > 0){
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $specCharge['coa_id'];
                                    $rowdet['dept_id'] = 0;
                                    $rowdet['journal_note'] = 'Bank Charge ' . $head->deposit_no;
                                    $rowdet['journal_debit'] = 0;
                                    $rowdet['journal_credit'] = $head->deposit_bankcharges;
                                    $rowdet['reference_id'] = 0;
                                    $rowdet['transtype_id'] = $specCharge['transtype_id'];

                                    array_push($detail, $rowdet);

                                    $totalCredit += $rowdet['journal_credit'];
                                }
                            }

                            //Add (D) Bank
                            $bank_coa_id = 0;
                            $coa = $this->db->get_where('gl_coa', array('coa_code' => $payment_type->coa_code));
                            if($coa->num_rows() > 0){
                                $bank_coa_id = $coa->row()->coa_id;
                            }

                            if($bank_coa_id <= 0 && $head->bankaccount_id > 0){
                                $bank = $this->mdl_finance->getBankAccount($head->bankaccount_id);
                                if(isset($bank['coa_id']))
                                    $bank_coa_id = $bank['coa_id'];
                            }

                            if($bank_coa_id > 0){
                                $totalDebit = $totalCredit;

                                $rowdet = array();
                                $rowdet['coa_id'] = $bank_coa_id;
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $head->deposit_desc;
                                $rowdet['journal_debit'] = $totalDebit;
                                $rowdet['journal_credit'] = 0;
                                $rowdet['reference_id'] = 0;
                                $rowdet['transtype_id'] = 0;

                                array_push($detail, $rowdet);
                            }

                            //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                            if($totalDebit == $totalCredit){
                                $header = array();
                                $header['journal_no'] = $head->deposit_no;
                                $header['journal_date'] = $head->deposit_date;
                                $header['journal_remarks'] = $head->deposit_desc;
                                $header['modul'] = GLMOD::GL_MOD_AR;
                                $header['journal_amount'] = $totalDebit;
                                $header['reference'] = '';

                                $valid = $this->mdl_finance->postJournal($header,$detail);

                                if($valid){
                                    $data['status']= STATUS_POSTED;
                                    $this->mdl_general->update('ar_deposit_header', array('deposit_id' => $val), $data);
                                }

                                $rowcount++;
                            }
                        }else{
                            $valid = false;
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
                redirect(base_url('ar/corporate_bill/deposit/1.tpd'));
            }
            else {
                redirect(base_url('ar/corporate_bill/deposit/1.tpd'));
            }
        }
    }

    public function xposting_deposit_by_id(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $depositId = 0;

        if(isset($_POST['deposit_id'])){
            $depositId = $_POST['deposit_id'];
        }

        if($depositId > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $joins = array('ms_deposit_type'=>'ms_deposit_type.deposittype_id = ar_deposit_detail.deposittype_id',
                'gl_coa' => 'gl_coa.coa_code = ms_deposit_type.coa_code');

            //insert post journal
            $detail = array();

            $totalDebit = 0;
            $totalCredit = 0;
            $qryDetails = $this->mdl_finance->getJoin('ar_deposit_detail.*, ms_deposit_type.coa_code, gl_coa.coa_id'
                ,'ar_deposit_detail', $joins , array('deposit_id' => $depositId));

            if($qryDetails->num_rows() > 0){
                foreach($qryDetails->result() as $det){
                    $rowdet = array();
                    $rowdet['coa_id'] = $det->coa_id;
                    $rowdet['dept_id'] = 0;
                    $rowdet['journal_note'] = $det->detail_desc;
                    $rowdet['journal_debit'] = 0;
                    $rowdet['journal_credit'] = $det->deposit_amount;
                    $rowdet['reference_id'] = 0;
                    $rowdet['transtype_id'] = 0;

                    array_push($detail, $rowdet);

                    $totalCredit += $rowdet['journal_credit'];
                }
            }

            $qryHeader = $this->db->get_where('ar_deposit_header', array('deposit_id' => $depositId));

            if($totalCredit > 0 && $qryHeader->num_rows() > 0){
                $head = $qryHeader->row();

                $payments = $this->db->get_where('ms_payment_type', array('paymenttype_id' => $head->paymenttype_id));
                if($payments->num_rows() > 0){
                    $payment_type = $payments->row();

                    //Add (C) Bank Charge (if any)
                    if($head->deposit_bankcharges > 0 && $payment_type->payment_type == PAYMENT_TYPE::CREDIT_CARD){
                        $specCharge = FNSpec::get(FNSpec::BANK_CHARGE);
                        if($specCharge['coa_id'] > 0){
                            $rowdet = array();
                            $rowdet['coa_id'] = $specCharge['coa_id'];
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = 'Bank Charge ' . $head->deposit_no;
                            $rowdet['journal_debit'] = $head->deposit_bankcharges;
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = 0;
                            $rowdet['transtype_id'] = $specCharge['transtype_id'];

                            array_push($detail, $rowdet);

                            $totalDebit += $rowdet['journal_debit'];
                        }
                    }

                    //Add (D) Bank
                    $bank_coa_id = 0;
                    $coa = $this->db->get_where('gl_coa', array('coa_code' => $payment_type->coa_code));
                    if($coa->num_rows() > 0){
                        $bank_coa_id = $coa->row()->coa_id;
                    }

                    if($bank_coa_id <= 0 && $head->bankaccount_id > 0){
                        $bank = $this->mdl_finance->getBankAccount($head->bankaccount_id);
                        if(isset($bank['coa_id']))
                            $bank_coa_id = $bank['coa_id'];
                    }

                    if($bank_coa_id > 0){
                        $rowdet = array();
                        $rowdet['coa_id'] = $bank_coa_id;
                        $rowdet['dept_id'] = 0;
                        $rowdet['journal_note'] = $head->deposit_desc;
                        $rowdet['journal_debit'] = $head->deposit_paymentamount;
                        $rowdet['journal_credit'] = 0;
                        $rowdet['reference_id'] = 0;
                        $rowdet['transtype_id'] = 0;

                        array_push($detail, $rowdet);

                        $totalDebit += $rowdet['journal_debit'];
                    }

                    //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                    if($totalDebit == $totalCredit){
                        $header = array();
                        $header['journal_no'] = $head->deposit_no;
                        $header['journal_date'] = $head->deposit_date;
                        $header['journal_remarks'] = $head->deposit_desc;
                        $header['modul'] = GLMOD::GL_MOD_AR;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = '';

                        $valid = $this->mdl_finance->postJournal($header,$detail);

                        if($valid){
                            $data['status']= STATUS_POSTED;
                            $this->mdl_general->update('ar_deposit_header', array('deposit_id' => $depositId), $data);
                        }
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
                    $result['redirect_link'] = base_url('ar/corporate_bill/deposit/3/'. $depositId .'.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be posted. Please try again later.';
            }

        }

        echo json_encode($result);
    }

    public function action_request(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $deposit_id = $_POST['deposit_id'];
        $data['status'] = $_POST['action'];
        $data['cancel_note'] = $_POST['reason'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Debit Note';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $deposit_id;
        $data_log['feature_id'] = Feature::FEATURE_AR_DEPOSIT;

        if($deposit_id > 0 && $data['status'] > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $qry = $this->db->get_where('ar_deposit_header', array('deposit_id' => $deposit_id));
            if($qry->num_rows() > 0){
                $row = $qry->row();

                if($data['status'] == STATUS_APPROVE){
                    $this->mdl_general->update('ar_deposit_header', array('deposit_id' => $deposit_id), $data);

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
                        $this->mdl_general->update('ar_deposit_header', array('deposit_id' => $deposit_id), $data);

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

    public function pdf_depositvoucher($doc_id = 0) {
        if($doc_id > 0){
            $this->load->model('finance/mdl_finance');

            $qry = $this->mdl_finance->getJoin('ar_deposit_header.*, ms_company.company_name as company_name,view_cs_reservation.tenant_fullname ' , 'ar_deposit_header',
                array('ms_company' => 'ms_company.company_id = ar_deposit_header.company_id',
                      'view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_deposit_header.reservation_id'),
                array('ar_deposit_header.deposit_id' => $doc_id));

            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();
                $data['journal_amount'] = ($data['row']->deposit_paymentamount + $data['row']->deposit_bankcharges);

                $where['gl_postjournal_header.journal_no'] = $data['row']->deposit_no;
                $data['qry_det'] = $this->mdl_finance->getJoin('gl_postjournal_detail.journal_note, gl_postjournal_detail.journal_debit,gl_postjournal_detail.journal_credit, gl_coa.coa_code, gl_coa.coa_desc, ms_department.department_name as dept_code' ,
                    'gl_postjournal_detail',array('gl_coa' => 'gl_coa.coa_id = gl_postjournal_detail.coa_id',
                        'gl_postjournal_header' => 'gl_postjournal_header.postheader_id = gl_postjournal_detail.postheader_id',
                        'ms_department' => 'ms_department.department_id = gl_postjournal_detail.dept_id'),
                    $where, array(),'gl_postjournal_detail.journal_debit DESC');

                $data['doc_type_title'] = 'RECEIVE VOUCHER';
                $data['subject_title'] = 'Receive From';
                $data['cashbook_type'] = 3;

                $this->load->view('ar/deposit/pdf_depositvoucher.php', $data);

                $html = $this->output->get_output();


                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->deposit_no . ".pdf", array('Attachment'=>0));

            }else{
                tpd_404();
            }

        }else{
            tpd_404();
        }
    }

    #endregion

    #region Corporate Allocation

    public function get_deposit_al_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        //$where['ar_receipt.reservation_id <='] = 0;
        $where['header.receipt_id <='] = 0;
        $where['header.depositdetail_id >'] = 0;
        $where['header.status'] = STATUS_NEW;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['header.alloc_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,header.alloc_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,header.alloc_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['header.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $joins = array('ar_deposit_detail' => 'ar_deposit_detail.detail_id = header.depositdetail_id',
                       'ar_deposit_header' => 'ar_deposit_header.deposit_id = ar_deposit_detail.deposit_id',
                       'ms_deposit_type' => 'ms_deposit_type.deposittype_id = ar_deposit_detail.deposittype_id');
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_allocation_header as header', $joins, $where, $like, true);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'header.alloc_no DESC, header.alloc_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'header.alloc_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'header.alloc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('header.*, ar_deposit_header.deposit_no, ms_deposit_type.deposit_key, ms_deposit_type.deposit_desc'
            ,'vw_ar_allocation_header as header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, true);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/deposit_al/3/' . $row->allocationheader_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a></li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL) || check_session_action($menu_id, STATUS_DELETE)){
                    $btn_action .= '<li><a data-original-title="Cancel" class="btn-cancel" href="javascript:;" data-id="' . $row->allocationheader_id . '" ><i class="fa fa-remove"></i> ' . get_action_name(STATUS_CANCEL, false) . '</a></li>';
                }
            }

            $records["data"][] = array(
                $i, //'<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->alloc_no,
                dmy_from_db($row->alloc_date),
                $row->company_name,
                $row->deposit_key,
                format_num($row->alloc_amount,0),
                $row->deposit_no,
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

    public function get_deposit_al_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['header.receipt_id <='] = 0;
        $where['header.depositdetail_id >'] = 0;
        //$where['header.status'] = STATUS_CLOSED;
        $where_str = 'header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['header.alloc_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,header.alloc_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,header.alloc_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['header.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $joins = array('ar_deposit_detail' => 'ar_deposit_detail.detail_id = header.depositdetail_id',
            'ar_deposit_header' => 'ar_deposit_header.deposit_id = ar_deposit_detail.deposit_id',
            'ms_deposit_type' => 'ms_deposit_type.deposittype_id = ar_deposit_detail.deposittype_id');
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_allocation_header as header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'header.alloc_no DESC, header.alloc_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'header.alloc_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'header.alloc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('header.*, ar_deposit_header.deposit_no, ms_deposit_type.deposit_key, ms_deposit_type.deposit_desc'
            ,'vw_ar_allocation_header as header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/deposit_al/3/' . $row->allocationheader_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a></li>';

            $records["data"][] = array(
                $i, //'<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->alloc_no,
                dmy_from_db($row->alloc_date),
                $row->company_name,
                $row->deposit_key,
                format_num($row->alloc_amount,0),
                $row->deposit_no,
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
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function deposit_al_form($allocationheader_id = 0){
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

        $data['allocationheader_id'] = $allocationheader_id;

        if($allocationheader_id > 0){
            $joins = array('ar_deposit_detail' => 'ar_deposit_detail.detail_id = header.depositdetail_id',
                'ar_deposit_header' => 'ar_deposit_header.deposit_id = ar_deposit_header.deposit_id',
                'ms_deposit_type' => 'ms_deposit_type.deposittype_id = ar_deposit_detail.deposittype_id');
            $qry = $this->mdl_finance->getJoin('header.*, ar_deposit_header.deposit_no, ms_deposit_type.deposit_key, ms_deposit_type.deposit_desc'
                ,'vw_ar_allocation_header as header', $joins, array('header.allocationheader_id' => $allocationheader_id));

            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                //Get Unposted amount
                $unposted_amount = 0;
                $existreceipt = $this->db->query('SELECT ISNULL(SUM(alloc_amount),0) as receipt_total FROM ar_allocation_header
                          WHERE depositdetail_id = ' . $data['row']->depositdetail_id . ' AND status = ' . STATUS_NEW . '
                                AND allocationheader_id <> ' . $allocationheader_id . ' AND allocationheader_id < ' . $allocationheader_id);

                if($existreceipt->num_rows() > 0){
                    $unposted_amount = $existreceipt->row()->receipt_total;
                }

                //Pending Unpaid
                $pending_amount = 0;

                $company_pending = $this->db->get_where('view_ar_invoice_unpaid_sum', array('company_id' => $data['row']->company_id, 'reservation_id' => $data['row']->reservation_id));
                if($company_pending->num_rows() > 0){
                    $pending_amount = $company_pending->row()->sum_pending - $unposted_amount;
                }
                $data['pending_amount'] = $pending_amount;

                //Available deposit
                $deposit = 0;
                $undeposit = $this->db->get_where("fxnARUnDepositByDateCorp('" . ymd_from_db($data['row']->created_date). "')", array('depositdetail_id' => $data['row']->depositdetail_id));
                if($undeposit->num_rows() > 0){
                    $deposit = $undeposit->row()->available_deposit - $unposted_amount;
                }

                $data['available_amount'] = $deposit;

                //DETAILS
                $invoices = array();

                $rv_details = $this->db->query("SELECT ar_allocation_deposit_detail.*, ar_invoice_header.inv_no,ar_invoice_header.inv_date, ar_invoice_header.inv_due_date FROM ar_allocation_deposit_detail
                    JOIN ar_invoice_header ON ar_invoice_header.inv_id = ar_allocation_deposit_detail.inv_id
                    WHERE ar_allocation_deposit_detail.allocationheader_id = " . $allocationheader_id);
                if ($rv_details->num_rows() > 0) {
                    foreach ($rv_details->result_array() as $det) {
                        array_push($invoices, array('inv_id' => $det['inv_id'], 'inv_no' => $det['inv_no'], 'inv_date' => $det['inv_date'], 'inv_due_date' => $det['inv_due_date'], 'pending_amount' => $det['receipt_amount'], 'checked' => 'checked'));

                    }

                }

                if($data['row']->status == STATUS_NEW){
                    $where['bill.unpaid_grand >'] = 0;
                    if ($data['row']->company_id > 0) {
                        $where['bill.company_id'] = $data['row']->company_id;
                    } else {
                        $where['bill.reservation_id'] = $data['row']->reservation_id;
                    }

                    $joins = array();
                    $qry = $this->mdl_finance->getJoin("bill.*", "fxnARInvoiceHeaderByStatus('" . STATUS_POSTED . "') AS bill", $joins, $where, array(), 'inv_no');
                    //echo $this->db->last_query();
                    if ($qry->num_rows() > 0) {
                        foreach ($qry->result_array() as $det) {
                            $existed = false;
                            foreach ($invoices as $inv) {
                                if ($inv['inv_id'] == $det['inv_id']) {
                                    $existed = true;
                                    break;
                                }
                            }

                            if (!$existed) {
                                array_push($invoices, array('inv_id' => $det['inv_id'], 'inv_no' => $det['inv_no'], 'inv_date' => $det['inv_date'], 'inv_due_date' => $det['inv_due_date'], 'pending_amount' => $det['unpaid_grand'], 'checked' => ''));
                            }
                        }
                    }
                }

                if (count($invoices) > 0) {
                    $data['details'] = $invoices;
                }

            }else{
                unset($data);
            }
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/deposit/deposit_alloc_form', $data);
        $this->load->view('layout/footer');
    }

    public function submit_deposit_al(){
        $valid = true;

        if(isset($_POST)){
            $allocationheader_id = $_POST['allocationheader_id'];
            $allocAmount = floatval($_POST['alloc_amount']);

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $data['alloc_date'] = dmy_to_ymd($_POST['alloc_date']);
            $data['depositdetail_id'] = $_POST['depositdetail_id'];
            $data['company_id'] = $_POST['company_id'];
            $data['reservation_id'] = $_POST['reservation_id'];
            $data['receipt_id'] = 0;
            $data['alloc_desc'] = $_POST['alloc_desc'];
            $data['alloc_amount'] = $allocAmount;
            $data['status'] = STATUS_NEW;

            if($allocationheader_id > 0){
                $qry = $this->db->get_where('ar_allocation_header', array('allocationheader_id' => $allocationheader_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['alloc_date']);
                $arr_date_old = explode('-', ymd_from_db($row->alloc_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    //DELETE OLD NUMBER
                    $data['alloc_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_ALLOC, $data['alloc_date']);
                    if($data['alloc_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Unique No can not be generated. Please check document type!');
                    }
                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('ar_allocation_header', array('allocationheader_id' => $allocationheader_id), $data);

                    $data['allocationheader_id'] = $row->allocationheader_id;

                    //SAVE ar_allocation_deposit_detail
                    if($valid)
                        $valid = $this->insertDepositAllocationDetails($data);
                }
            }else{
                $data['alloc_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_ALLOC, $data['alloc_date']);
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = date('Y-m-d H:i:s');
                $data['modified_by'] = 0;
                $data['modified_date'] = date('Y-m-d H:i:s');

                if($data['alloc_no'] != ''){
                    $this->db->insert('ar_allocation_header', $data);
                    $allocationheader_id = $this->db->insert_id();
                    if($allocationheader_id <= 0){
                        $valid = false;
                    }

                    $data['allocationheader_id'] = $allocationheader_id;

                    //SAVE ar_allocation_deposit_detail
                    if($valid)
                        $valid = $this->insertDepositAllocationDetails($data);
                }else{
                    $valid = false;
                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Unique No can not be generated. Please check document type!');
                }
            }

            //LOG
            if($valid){
                $data_log['user_id'] = my_sess('user_id');
                $data_log['log_subject'] = get_action_name($data['status'], false) . ' Deposit Allocation';
                $data_log['log_date'] = date('Y-m-d H:i:s');
                $data_log['reff_id'] = $allocationheader_id;
                $data_log['feature_id'] = Feature::FEATURE_AR_ALLOC;
                $data_log['action_type'] = STATUS_CLOSED;
                $this->db->insert('app_log', $data_log);
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Allocation can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Allocation successfully saved.');
                }
            }else{
                $this->db->trans_rollback();
            }
        }

        //FINALIZE
        if(!$valid){
            redirect(base_url('ar/corporate_bill/deposit_al/3/' . $allocationheader_id . '.tpd'));
        }
        else {
            redirect(base_url('ar/corporate_bill/deposit_al/3/' . $allocationheader_id . '.tpd'), true);
        }
    }

    private function insertDepositAllocationDetails($alloc = array()){
        $valid = true;

        if(isset($alloc) && isset($_POST)){
            $checked_ids = isset($_POST['inv_id']) ? $_POST['inv_id'] : array();

            $invoice_ids = isset($_POST['invoice_id']) ? $_POST['invoice_id'] : array();
            $amounts = isset($_POST['pending_amount']) ? $_POST['pending_amount'] : array();
            //echo 'count ' . count($checked_ids);
            if(count($checked_ids) > 0 && count($invoice_ids) > 0){
                $details = array();
                for($i=0;$i<count($checked_ids);$i++){
                    for($x=0;$x<count($invoice_ids);$x++){
                        if($invoice_ids[$x] == $checked_ids[$i]){
                            $detail = array();
                            $detail['allocationheader_id'] = $alloc['allocationheader_id'];
                            $detail['inv_id'] = $checked_ids[$i];
                            $detail['base_amount'] = $amounts[$x];
                            $detail['receipt_amount'] = $amounts[$x];
                            $detail['status'] = STATUS_NEW;

                            array_push($details, $detail);
                        }
                    }
                }

                $invids = array();
                foreach($details as $detail){
                    $exist = $this->db->get_where('ar_allocation_deposit_detail', array('allocationheader_id' => $detail['allocationheader_id'], 'inv_id' => $detail['inv_id']));
                    if($exist->num_rows() > 0){
                        $detail_id = $exist->row()->id;
                        $this->mdl_general->update('ar_allocation_deposit_detail', array('id' => $detail_id), $detail);
                    }else{
                        $detail['status'] = STATUS_NEW;

                        $this->db->insert('ar_allocation_deposit_detail', $detail);
                        $insert_id = $this->db->insert_id();

                        if($insert_id <= 0){
                            $valid = false;
                            break;
                        }
                    }

                    $invids[] = $detail['inv_id'];
                }

                //DELETE OTHER INV_ID in detail
                $str_inv_id = implode(',',$invids);
                if(trim($str_inv_id) != '' && $alloc['allocationheader_id'] > 0){
                    $deleted = $this->db->query("DELETE FROM ar_allocation_deposit_detail WHERE allocationheader_id = " . $alloc['allocationheader_id'] . " AND inv_id NOT IN(" . $str_inv_id . ")");
                }

            }else{
                $deleted = $this->db->query("DELETE FROM ar_allocation_deposit_detail WHERE allocationheader_id = " . $alloc['allocationheader_id']);
            }
        }

        return $valid;
    }

    public function xpost_deposit_al(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $allocationheader_id = 0;

        if(isset($_POST['allocationheader_id'])){
            $allocationheader_id = $_POST['allocationheader_id'];
        }

        if($allocationheader_id > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $alloc = $this->db->get_where('ar_allocation_header', array('allocationheader_id'=> $allocationheader_id));
            if($alloc->num_rows() > 0){
                $alloc = $alloc->row_array();
                if($valid){
                    //Allocate
                    $valid = $this->allocateDepositToInvoice($alloc);

                    //Posting
                    //SEC DEP
                    //   AR
                    if($valid){
                        $valid = $this->postingDepositAllocation($alloc);
                    }

                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_CLOSED;

                    $this->mdl_general->update('ar_allocation_header', array('allocationheader_id' => $allocationheader_id), $data);
                }
            }else{
                $valid = false;
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
                    $result['message'] = $alloc['alloc_no'] . ' successfully posted.';
                    $result['redirect_link'] = base_url('ar/corporate_bill/deposit_al/3/' . $allocationheader_id . '.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be posted. Please try again later.';
            }

        }

        echo json_encode($result);
    }

    private function allocateDepositToInvoice($alloc = array()){
        $valid = true;

        if(isset($alloc)){
            $current_date = date('Y-m-d H:i:s');

            $receipt_details = $this->db->get_where('ar_allocation_deposit_detail', array('allocationheader_id'=>$alloc['allocationheader_id']));
            if($receipt_details->num_rows() > 0){
                foreach($receipt_details->result() as $det){
                    $inv_id = $det->inv_id;
                    $availableAmount = $det->receipt_amount;

                    //Allocate to CS Receipt Allocation
                    $unpaids = $this->db->get_where('view_ar_unpaid_invoice', array('inv_id'=>$inv_id));
                    if($unpaids->num_rows() > 0){
                        foreach($unpaids->result_array() as $bill){
                            //AMOUNT
                            if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                                $detail = array();
                                $detail['allocationheader_id'] = $alloc['allocationheader_id'];
                                $detail['depositdetail_id'] = $alloc['depositdetail_id'];
                                $detail['invdetail_id'] = $bill['invdetail_id'];
                                $detail['allocation_date'] = $current_date;
                                $detail['is_debitnote'] = 0;
                                $detail['created_by'] = my_sess('user_id');
                                $detail['created_date'] = $current_date;
                                $detail['status'] = STATUS_CLOSED;
                                $detail['is_tax'] = 0;

                                if($bill['pending_amount'] <= $availableAmount){
                                    $detail['allocation_amount'] = $bill['pending_amount'];
                                }else{
                                    $detail['allocation_amount'] = $availableAmount;
                                }

                                $availableAmount -= $detail['allocation_amount'];

                                if($detail['allocation_amount'] > 0){
                                    $this->db->insert('ar_deposit_allocation', $detail);
                                    if($this->db->insert_id() <= 0){
                                        $valid = false;
                                        break;
                                    }
                                }

                                if($valid){
                                    //---------------------
                                    //UPDATE INVOICE DETAIL
                                    //---------------------
                                    $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']));
                                    if($invdetail->num_rows() > 0){
                                        $invdetail = $invdetail->row();
                                        $alloc_amount = $invdetail->paid_amount + $detail['allocation_amount'];

                                        $update = array();
                                        if($alloc_amount <= $invdetail->amount){
                                            $update['paid_amount'] = $alloc_amount;
                                        }else{
                                            $update['paid_amount'] = $invdetail->amount;
                                        }
                                        $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']), $update);

                                    }
                                    //---------------------
                                }
                            }

                            //TAX
                            if($bill['pending_tax'] > 0 && $availableAmount > 0 && $valid){
                                $detail = array();
                                $detail['allocationheader_id'] = $alloc['allocationheader_id'];
                                $detail['depositdetail_id'] = $alloc['depositdetail_id'];
                                $detail['invdetail_id'] = $bill['invdetail_id'];
                                $detail['allocation_date'] = $current_date;
                                $detail['is_debitnote'] = 0;
                                $detail['created_by'] = my_sess('user_id');
                                $detail['created_date'] = $current_date;
                                $detail['status'] = STATUS_CLOSED;
                                $detail['is_tax'] = 1;

                                if($bill['pending_tax'] <= $availableAmount){
                                    $detail['allocation_amount'] = $bill['pending_tax'];
                                }else{
                                    $detail['allocation_amount'] = $availableAmount;
                                }

                                $availableAmount -= $detail['allocation_amount'];

                                if($detail['allocation_amount'] > 0){
                                    $this->db->insert('ar_deposit_allocation', $detail);
                                    if($this->db->insert_id() <= 0){
                                        $valid = false;
                                        break;
                                    }
                                }

                                if($valid){
                                    //---------------------
                                    //UPDATE INVOICE DETAIL
                                    //---------------------
                                    $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']));
                                    if($invdetail->num_rows() > 0){
                                        $invdetail = $invdetail->row();
                                        $alloc_tax = $invdetail->paid_tax + $detail['allocation_amount'];

                                        $update = array();
                                        if($alloc_tax <= $invdetail->tax){
                                            $update['paid_tax'] = $alloc_tax;
                                        }else{
                                            $update['paid_tax'] = $invdetail->tax;
                                        }
                                        $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']), $update);
                                    }
                                    //---------------------
                                }
                            }
                        }

                        if($valid){
                            //---------------------------------------------------
                            //UPDATE INVOICE HEADER STATUS TO CLOSED IF FULL PAID
                            //---------------------------------------------------
                            //$invoice = $this->db->get_where('view_ar_unpaid_invoice',array('inv_id'=> $inv_id));
                            //if($invoice->num_rows() <= 0){
                            $this->mdl_general->update('ar_invoice_header',array('inv_id' => $inv_id), array('status' => STATUS_CLOSED));
                                //$this->mdl_general->update('ar_receipt_detail',array('inv_id' => $inv_id), array('status' => STATUS_POSTED));
                            //}
                            //---------------------------------------------------
                        }
                    }
                }
            }
        }

        return $valid;
    }

    private function allocateDepositToBill($alloc = array()){
        $valid = true;

        if(isset($alloc)){
            $availableAmount = $alloc['alloc_amount'];
            $current_date = date('Y-m-d H:i:s');

            $invoice_list = array();

            try{
                //Allocate to CS Receipt Allocation
                $unpaids = $this->db->query('SELECT * FROM fxnARUnpaidInvoiceDetail(' . $alloc['company_id'] . ',' . $alloc['reservation_id'] . ')');
                if($unpaids->num_rows() > 0){
                    foreach($unpaids->result_array() as $bill){
                        //AMOUNT
                        if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                            $detail = array();
                            $detail['allocationheader_id'] = $alloc['allocationheader_id'];
                            $detail['depositdetail_id'] = $alloc['depositdetail_id'];
                            $detail['invdetail_id'] = $bill['invdetail_id'];
                            $detail['allocation_date'] = $current_date;
                            $detail['is_tax'] = 0;
                            $detail['is_debitnote'] = 0;
                            $detail['status'] = STATUS_CLOSED;
                            $detail['created_by'] = my_sess('user_id');
                            $detail['created_date'] = $current_date;

                            if($bill['pending_amount'] <= $availableAmount){
                                $detail['allocation_amount'] = $bill['pending_amount'];
                            }else{
                                $detail['allocation_amount'] = $availableAmount;
                            }

                            $availableAmount -= $detail['allocation_amount'];

                            if($detail['allocation_amount'] > 0){
                                $this->db->insert('ar_deposit_allocation', $detail);
                                if($this->db->insert_id() <= 0){
                                    $valid = false;
                                    break;
                                }
                            }

                            if($valid){
                                //---------------------
                                //UPDATE INVOICE DETAIL
                                //---------------------
                                $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']));
                                if($invdetail->num_rows() > 0){
                                    $invdetail = $invdetail->row();
                                    $alloc_amount = $invdetail->paid_amount + $detail['allocation_amount'];

                                    $update = array();
                                    if($alloc_amount <= $invdetail->amount){
                                        $update['paid_amount'] = $alloc_amount;
                                    }else{
                                        $update['paid_amount'] = $invdetail->amount;
                                    }
                                    $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']), $update);

                                    $found = false;
                                    foreach($invoice_list as $inv){
                                        if($inv == $bill['inv_id']){
                                            $found = true;
                                            break;
                                        }
                                    }

                                    if(!$found){
                                        array_push($invoice_list, $bill['inv_id']);
                                    }
                                }
                                //---------------------
                            }
                        }

                        //TAX
                        if($bill['pending_tax'] > 0 && $availableAmount > 0 && $valid){
                            $detail = array();
                            $detail['allocationheader_id'] = $alloc['allocationheader_id'];
                            $detail['depositdetail_id'] = $alloc['depositdetail_id'];
                            $detail['invdetail_id'] = $bill['invdetail_id'];
                            $detail['allocation_date'] = $current_date;
                            $detail['is_tax'] = 1;
                            $detail['is_debitnote'] = 0;
                            $detail['status'] = STATUS_CLOSED;
                            $detail['created_by'] = my_sess('user_id');
                            $detail['created_date'] = $current_date;

                            if($bill['pending_tax'] <= $availableAmount){
                                $detail['allocation_amount'] = $bill['pending_tax'];
                            }else{
                                $detail['allocation_amount'] = $availableAmount;
                            }

                            $availableAmount -= $detail['allocation_amount'];

                            if($detail['allocation_amount'] > 0){
                                $this->db->insert('ar_deposit_allocation', $detail);
                                if($this->db->insert_id() <= 0){
                                    $valid = false;
                                    break;
                                }
                            }

                            if($valid){
                                //---------------------
                                //UPDATE INVOICE DETAIL
                                //---------------------
                                $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']));
                                if($invdetail->num_rows() > 0){
                                    $invdetail = $invdetail->row();
                                    $alloc_tax = $invdetail->paid_tax + $detail['allocation_amount'];

                                    $update = array();
                                    if($alloc_tax <= $invdetail->tax){
                                        $update['paid_tax'] = $alloc_tax;
                                    }else{
                                        $update['paid_tax'] = $invdetail->tax;
                                    }
                                    $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']), $update);

                                    $found = false;
                                    foreach($invoice_list as $inv){
                                        if($inv == $bill['inv_id']){
                                            $found = true;
                                            break;
                                        }
                                    }

                                    if(!$found){
                                        array_push($invoice_list, $bill['inv_id']);
                                    }
                                }
                                //---------------------
                            }
                        }
                    }

                    //---------------------------------------------------
                    //UPDATE INVOICE HEADER STATUS TO CLOSED IF FULL PAID
                    //---------------------------------------------------
                    if(count($invoice_list) > 0){
                        foreach($invoice_list as $inv_id){
                            $invoice = $this->db->get_where('view_ar_unpaid_invoice',array('inv_id'=>$inv_id));
                            if($invoice->num_rows() <= 0){
                                $this->mdl_general->update('ar_invoice_header',array('inv_id' => $inv_id), array('status' => STATUS_CLOSED));
                            }
                        }
                    }
                    //---------------------------------------------------
                }
            }catch(Exception $e){
                $valid = false;
            }
        }

        return $valid;
    }

    private function postingDepositAllocation($alloc = array()){
        $valid = true;

        if(isset($alloc)){
            $this->load->model('finance/mdl_finance');

            try{
                $current_date = date('Y-m-d H:i:s');

                $qry = $this->db->query('SELECT coa.coa_id, ISNULL(res.reservation_type,0) as reservation_type FROM ar_deposit_detail det
                                         JOIN ar_deposit_header hd ON hd.deposit_id = det.deposit_id
                                         JOIN ms_deposit_type tp ON tp.deposittype_id = det.deposittype_id
                                         JOIN gl_coa coa ON coa.coa_code = tp.coa_code
                                         LEFT JOIN cs_reservation_header res ON res.reservation_id = hd.reservation_id
                                         WHERE det.detail_id = ' . $alloc['depositdetail_id']);

                if($qry->num_rows() > 0){
                    $secdep = $qry->row_array();

                    //Post Journal
                    //Sec Dep
                    // AR
                    $detail = array();

                    $totalDebit = $alloc['alloc_amount'];
                    $secdep_coa_id = $secdep['coa_id'];
                    if($totalDebit > 0){
                        if($secdep_coa_id > 0){
                            $rowdet = array();
                            $rowdet['coa_id'] = $secdep_coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $alloc['alloc_desc'];
                            $rowdet['journal_debit'] = $totalDebit;
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = 0;
                            $rowdet['transtype_id'] = 0;

                            array_push($detail, $rowdet);

                            $totalCredit = $totalDebit;
                        }else{
                            $valid = false;
                        }
                    }else{
                        $valid = false;
                    }

                    if($valid){
                        if($totalCredit > 0 && $totalCredit == $totalDebit && count($detail) > 0){
                            //AR
                            $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                            if($alloc['reservation_id'] > 0){
                                if($secdep['reservation_type'] == RES_TYPE::CORPORATE){
                                    //Undo allocation if Folio not Personal
                                    //$totalCredit = 0;
                                }
                            }else{
                                $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                            }

                            if($totalCredit > 0) {
                                if ($ar['coa_id'] > 0) {
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $ar['coa_id'];
                                    $rowdet['dept_id'] = 0;
                                    $rowdet['journal_note'] = $alloc['alloc_desc'];
                                    $rowdet['journal_debit'] = 0;
                                    $rowdet['journal_credit'] = $totalCredit;
                                    $rowdet['reference_id'] = 0;
                                    $rowdet['transtype_id'] = $ar['transtype_id'];

                                    array_push($detail, $rowdet);
                                }
                            }

                        }else{
                            $valid = false;
                        }
                    }

                    if($valid && $totalDebit == $totalCredit && $totalDebit > 0 ){
                        $header = array();
                        $header['journal_no'] = $alloc['alloc_no'];
                        $header['journal_date'] = $current_date;
                        $header['journal_remarks'] = $alloc['alloc_desc'];
                        $header['modul'] = GLMOD::GL_MOD_AR;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = '';
                        //$header['reference_date'] = $reservation['reservation_date'];

                        $valid = $this->mdl_finance->postJournal($header,$detail);
                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = false;
                }
            }catch(Exception $e){
                $valid = false;
            }
        }

        return $valid;
    }

    #endregion

    #region Folio Deposit Allocation

    public function get_folio_al_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        //$where['ar_receipt.reservation_id <='] = 0;
        $where['header.depositdetail_id >'] = 0;
        $where['header.reservation_id >'] = 0;
        $where['header.status'] = STATUS_NEW;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['header.alloc_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,header.alloc_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,header.alloc_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
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

        $joins = array('view_cs_reservation' => 'header.reservation_id = view_cs_reservation.reservation_id',
                       'ar_deposit_detail' => 'ar_deposit_detail.detail_id = header.depositdetail_id',
                       'ar_deposit_header' => 'ar_deposit_header.deposit_id = ar_deposit_detail.deposit_id',
                       'ms_deposit_type' => 'ms_deposit_type.deposittype_id = ar_deposit_detail.deposittype_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_allocation_header as header', $joins, $where, $like, true);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'header.alloc_no DESC, header.alloc_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'header.alloc_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'header.alloc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('header.*, view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, ar_deposit_header.deposit_no, ms_deposit_type.deposit_key, ms_deposit_type.deposit_desc'
            ,'ar_allocation_header as header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, true);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/folio/deposit_al/3/' . $row->allocationheader_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a></li>';

            $records["data"][] = array(
                $i, //'<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->alloc_no,
                dmy_from_db($row->alloc_date),
                $row->reservation_code,
                $row->tenant_fullname,
                $row->deposit_key,
                format_num($row->alloc_amount,0),
                $row->deposit_no,
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

    public function get_folio_al_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['header.depositdetail_id >'] = 0;
        $where['header.reservation_id >'] = 0;
        //$where['header.status'] = STATUS_CLOSED;
        $where_str = 'header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['header.alloc_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,header.alloc_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,header.alloc_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
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

        $joins = array('view_cs_reservation' => 'header.reservation_id = view_cs_reservation.reservation_id',
            'ar_deposit_detail' => 'ar_deposit_detail.detail_id = header.depositdetail_id',
            'ar_deposit_header' => 'ar_deposit_header.deposit_id = ar_deposit_detail.deposit_id',
            'ms_deposit_type' => 'ms_deposit_type.deposittype_id = ar_deposit_detail.deposittype_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_allocation_header as header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'header.alloc_no DESC, header.alloc_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'header.alloc_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'header.alloc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('header.*, view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, ar_deposit_header.deposit_no, ms_deposit_type.deposit_key, ms_deposit_type.deposit_desc'
            ,'ar_allocation_header as header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/folio/deposit_al/3/' . $row->allocationheader_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a></li>';

            $records["data"][] = array(
                $i, //'<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->alloc_no,
                dmy_from_db($row->alloc_date),
                $row->reservation_code,
                $row->company_name,
                $row->deposit_key,
                format_num($row->alloc_amount,0),
                $row->deposit_no,
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
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function folio_al_form($allocationheader_id = 0){
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

        $data['allocationheader_id'] = $allocationheader_id;

        $valid = true;
        if($allocationheader_id > 0){
            $joins = array('view_cs_reservation' => 'header.reservation_id = view_cs_reservation.reservation_id',
                'ar_deposit_detail' => 'ar_deposit_detail.detail_id = header.depositdetail_id',
                'ar_deposit_header' => 'ar_deposit_header.deposit_id = ar_deposit_header.deposit_id',
                'ms_deposit_type' => 'ms_deposit_type.deposittype_id = ar_deposit_detail.deposittype_id');
            $qry = $this->mdl_finance->getJoin('header.*, view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, ar_deposit_header.deposit_no, ms_deposit_type.deposit_key, ms_deposit_type.deposit_desc'
                ,'ar_allocation_header as header', $joins, array('header.allocationheader_id' => $allocationheader_id));

            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                //Get Unposted amount
                $unposted_amount = 0;
                $existreceipt = $this->db->query('SELECT ISNULL(SUM(alloc_amount),0) as receipt_total FROM ar_allocation_header
                          WHERE depositdetail_id = ' . $data['row']->depositdetail_id . ' AND status = ' . STATUS_NEW . '
                                AND allocationheader_id <> ' . $allocationheader_id . ' AND allocationheader_id < ' . $allocationheader_id);

                if($existreceipt->num_rows() > 0){
                    $unposted_amount = $existreceipt->row()->receipt_total;
                }

                //Pending Unpaid
                $pending_amount = 0;

                $folio_pending = $this->db->get_where('fxnARPendingByDate(GETDATE())', array('reservation_id' => $data['row']->reservation_id));
                if($folio_pending->num_rows() > 0){
                    $pending_amount = $folio_pending->row()->pending_amount - $unposted_amount;
                }
                $data['pending_amount'] = $pending_amount;

                //Available deposit
                $deposit = 0;
                $undeposit = $this->db->get_where("fxnARUnDepositByDateFolio('" . ymd_from_db($data['row']->created_date). "')", array('depositdetail_id' => $data['row']->depositdetail_id));
                if($undeposit->num_rows() > 0){
                    $deposit = $undeposit->row()->available_deposit - $unposted_amount;
                }

                $data['available_amount'] = $deposit;

            }else{
                tpd_404();
                $valid = false;
            }
        }

        if($valid) {
            $this->load->view('layout/header', $data_header);
            $this->load->view('ar/deposit/folio_alloc_form', $data);
            $this->load->view('layout/footer');
        }

    }

    public function submit_folio_al(){
        $valid = true;

        if(isset($_POST)){
            $allocationheader_id = $_POST['allocationheader_id'];
            $allocAmount = floatval($_POST['alloc_amount']);

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $data['alloc_date'] = dmy_to_ymd($_POST['alloc_date']);
            $data['reservation_id'] = $_POST['reservation_id'];
            $data['company_id'] = 0;
            $data['depositdetail_id'] = $_POST['depositdetail_id'];
            $data['alloc_desc'] = $_POST['alloc_desc'];
            $data['alloc_amount'] = $allocAmount;
            $data['status'] = STATUS_NEW;

            if($allocationheader_id > 0){
                $qry = $this->db->get_where('ar_allocation_header', array('allocationheader_id' => $allocationheader_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['alloc_date']);
                $arr_date_old = explode('-', ymd_from_db($row->alloc_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    //DELETE OLD NUMBER
                    $data['alloc_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_ALLOC, $data['alloc_date']);
                    if($data['alloc_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Unique No can not be generated. Please check document type!');
                    }
                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('ar_allocation_header', array('allocationheader_id' => $allocationheader_id), $data);
                }
            }else{
                $data['alloc_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_ALLOC, $data['alloc_date']);
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = date('Y-m-d H:i:s');
                $data['modified_by'] = 0;
                $data['modified_date'] = date('Y-m-d H:i:s');

                if($data['alloc_no'] != ''){
                    $this->db->insert('ar_allocation_header', $data);
                    $allocationheader_id = $this->db->insert_id();
                    if($allocationheader_id <= 0){
                        $valid = false;
                    }

                    $data['allocationheader_id'] = $allocationheader_id;
                }else{
                    $valid = false;
                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Unique No can not be generated. Please check document type!');
                }
            }

            //LOG
            if($valid){
                $data_log['user_id'] = my_sess('user_id');
                $data_log['log_subject'] = get_action_name($data['status'], false) . ' Deposit Folio Allocation';
                $data_log['log_date'] = date('Y-m-d H:i:s');
                $data_log['reff_id'] = $allocationheader_id;
                $data_log['feature_id'] = Feature::FEATURE_AR_ALLOC;
                $data_log['action_type'] = STATUS_CLOSED;
                $this->db->insert('app_log', $data_log);
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Allocation can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Allocation successfully saved.');
                }
            }else{
                $this->db->trans_rollback();
            }
        }

        //FINALIZE
        if(!$valid){
            redirect(base_url('ar/folio/deposit_al/3/' . $allocationheader_id . '.tpd'));
        }
        else {
            redirect(base_url('ar/folio/deposit_al/3/' . $allocationheader_id . '.tpd'), true);
        }
    }

    public function xpost_folio_al(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $allocationheader_id = 0;

        if(isset($_POST['allocationheader_id'])){
            $allocationheader_id = $_POST['allocationheader_id'];
        }

        if($allocationheader_id > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $alloc = $this->db->get_where('ar_allocation_header', array('allocationheader_id'=> $allocationheader_id));
            if($alloc->num_rows() > 0){
                $alloc = $alloc->row_array();
                if($valid){
                    //Allocate
                    $valid = $this->allocateDepositFolioToBill($alloc);

                    //Posting
                    //SEC DEP
                    //   AR
                    if($valid){
                        $valid = $this->postingDepositAllocation($alloc);
                    }
                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_POSTED;

                    $this->mdl_general->update('ar_allocation_header', array('allocationheader_id' => $allocationheader_id), $data);
                }
            }else{
                $valid = false;
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
                    $result['message'] = $alloc['alloc_no'] . ' successfully posted.';
                    $result['redirect_link'] = base_url('ar/folio/deposit_al/3/' . $allocationheader_id . '.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be posted. Please try again later.';
            }

        }

        echo json_encode($result);
    }

    private function allocateDepositFolioToBill($alloc = array()){
        $valid = true;

        if(isset($alloc)){
            $availableAmount = $alloc['alloc_amount'];
            $current_date = date('Y-m-d H:i:s');

            try{
                //Allocate to CS Receipt Allocation
                $unpaids = $this->db->query('SELECT * FROM fxnARUnpaidBillByID(' . $alloc['reservation_id'] . ')');
                if($unpaids->num_rows() > 0){
                    foreach($unpaids->result_array() as $bill){
                        //AMOUNT
                        if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                            $detail = array();
                            $detail['allocationheader_id'] = $alloc['allocationheader_id'];
                            $detail['depositdetail_id'] = $alloc['depositdetail_id'];
                            $detail['invdetail_id'] = 0;
                            $detail['reservation_id'] = $bill['reservation_id'];
                            $detail['allocation_date'] = $current_date;
                            $detail['is_tax'] = 0;
                            $detail['is_debitnote'] = 0;
                            $detail['status'] = STATUS_CLOSED;
                            $detail['created_by'] = my_sess('user_id');
                            $detail['created_date'] = $current_date;

                            if($bill['pending_amount'] <= $availableAmount){
                                $detail['allocation_amount'] = $bill['pending_amount'];
                            }else{
                                $detail['allocation_amount'] = $availableAmount;
                            }

                            $availableAmount -= $detail['allocation_amount'];

                            if($detail['allocation_amount'] > 0){
                                $this->db->insert('ar_deposit_allocation', $detail);
                                if($this->db->insert_id() <= 0){
                                    $valid = false;
                                    break;
                                }
                            }

                        }
                    }

                }
            }catch(Exception $e){
                $valid = false;
            }
        }

        return $valid;
    }

    #endregion

    #region Modal

    public function xmodal_company(){
        $this->load->view('general/book_company');
    }

    public function get_modal_companies($num_index = 0, $company_id = 0, $deposit_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['status'] = 1;

        $like = array();
        $server_date = date('Y-m-d');

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ar.company_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_addr'])){
            if($_REQUEST['filter_addr'] != ''){
                $like['ar.company_address'] = $_REQUEST['filter_addr'];
            }
        }

        $joins = array(); //array("view_ar_invoice_unpaid_sum _sum"=>"_sum.company_id = ar.company_id");
        $iTotalRecords = $this->mdl_finance->countJoin("ms_company AS ar", $joins, $where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ar.company_name';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ar.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar.company_address ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin("ar.* ","ms_company AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';
            $attr .= ' data-company-id="' . $row->company_id . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            $text = "";
            if ($company_id == $row->company_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }else{
                $text = "Select";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-record" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $row->company_name,
                $row->company_address,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function xmodal_undeposit(){
        $this->load->view('ar/deposit/ajax_modal_undeposit');
    }

    public function get_modal_undeposit($num_index = 0, $depositdetail_id = 0, $allocationheader_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ISNULL(_sum.sum_pending,0) >'] = 0;

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

        $joins = array("view_ar_invoice_unpaid_sum _sum"=>"_sum.company_id = ar.company_id ");
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

        $qry = $this->mdl_finance->getJoin("ar.*, ISNULL(_sum.sum_pending,0) as pending_amount ","fxnARUnDepositByDateCorp(getdate()) AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            if($row->pending_amount > 0){
                $available = 0;

                $existreceipt = $this->db->query('SELECT ISNULL(SUM(alloc_amount),0) as alloc_total FROM ar_allocation_header
                                               WHERE depositdetail_id = ' . $row->depositdetail_id . ' AND status = ' . STATUS_NEW . '
                                               AND allocationheader_id <> ' . $allocationheader_id);
                if($existreceipt->num_rows() > 0){
                    $available = $existreceipt->row()->alloc_total;
                }

                $unallocated = $row->available_deposit - $available;

                $attr = '';
                $attr .= ' data-detail-id="' . $row->depositdetail_id . '" ';
                $attr .= ' data-company-id="' . $row->company_id . '" ';
                $attr .= ' data-company-name="' . $row->company_name . '" ';
                $attr .= ' data-deposit-no="' . $row->deposit_no . '" ';
                $attr .= ' data-deposit-key="' . $row->deposit_key . '" ';
                $attr .= ' data-pending-amount="' . $row->pending_amount . '"';
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
                            $row->company_name,
                            $row->deposit_desc,
                            format_num($row->pending_amount, 0),
                            format_num($unallocated, 0),
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
        $this->load->view('ar/deposit/ajax_modal_undeposit_folio');
    }

    public function get_modal_undeposit_folio($num_index = 0, $depositdetail_id = 0, $allocationheader_id = 0){
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
                $like['ar.company_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $like['ar.deposit_desc'] = $_REQUEST['filter_type'];
            }
        }

        $joins = array("fxnARPendingByDate(GETDATE()) _sum"=>"_sum.reservation_id = ar.reservation_id");
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

        $qry = $this->mdl_finance->getJoin("ar.*, ISNULL(_sum.pending_amount,0) as pending_amount ","fxnARUnDepositByDateFolio(getdate()) AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $available = 0;

            if($row->pending_amount > 0){
                $existreceipt = $this->db->query('SELECT ISNULL(SUM(alloc_amount),0) as alloc_total FROM ar_allocation_header
                                           WHERE depositdetail_id = ' . $row->depositdetail_id . ' AND status = ' . STATUS_NEW . '
                                           AND allocationheader_id <> ' . $allocationheader_id);
                if($existreceipt->num_rows() > 0){
                    $available = $existreceipt->row()->alloc_total;
                }

                $unallocated = $row->available_deposit - $available;

                $attr = '';
                $attr .= ' data-detail-id="' . $row->depositdetail_id . '" ';
                $attr .= ' data-reservation-id="' . $row->reservation_id . '" ';
                $attr .= ' data-reservation-code="' . $row->reservation_code . '" ';
                $attr .= ' data-tenant-name="' . $row->tenant_fullname . '" ';
                $attr .= ' data-deposit-no="' . $row->deposit_no . '" ';
                $attr .= ' data-deposit-key="' . $row->deposit_key . '" ';
                $attr .= ' data-pending-amount="' . $row->pending_amount . '"';
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
                        format_num($row->pending_amount,0),
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