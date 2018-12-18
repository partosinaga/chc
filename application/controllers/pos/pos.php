<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos extends CI_Controller {

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
		$this->pos_manage();
	}

    #region Billing

    public function pos_manage($type = 1, $bill_id = 0){
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
            $this->load->view('pos/pos_manage.php', $data);
            $this->load->view('layout/footer');
        } else if ($type == 3) {
            $this->pos_form($bill_id);
        }
    }

    public function get_pos_manage($menu_id = 0, $is_history = '0'){
        $this->load->model('finance/mdl_finance');

        $where = array();
        if ($is_history == '0') {
            $where['cs_bill_header.status'] = STATUS_NEW;
            $whereStr = '';
        } else {
            //$where['cs_bill_header.status'] = STATUS_POSTED;
            $whereStr = 'cs_bill_header.status IN(' . STATUS_POSTED . ',' . STATUS_CLOSED . ')';
        }

        $where['cs_bill_header.is_other_charge > '] = 0;
        $where['cs_bill_header.paymenttype_id >'] = 0;

        $like = array();

        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['DATE(cs_bill_header.bill_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['DATE(cs_bill_header.bill_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_bill_no'])){
            if($_REQUEST['filter_bill_no'] != ''){
                $like['cs_bill_header.journal_no'] = $_REQUEST['filter_bill_no'];
            }
        }

        if(isset($_REQUEST['filter_client'])){
            if($_REQUEST['filter_client'] != ''){
                $like['cs_bill_header.company_name'] = $_REQUEST['filter_client'];
            }
        }

        if(isset($_REQUEST['filter_subject'])){
            if($_REQUEST['filter_subject'] != ''){
                $like['cs_bill_header.paymenttype_code'] = $_REQUEST['filter_subject'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_bill_header as cs_bill_header', $joins, $where, $like, '',array(),$whereStr);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'cs_bill_header.bill_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'cs_bill_header.bill_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'cs_bill_header.journal_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'cs_bill_header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('cs_bill_header.*'
            ,'vw_bill_header as cs_bill_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false,'',array(),$whereStr);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('pos/pos/pos_manage/3/' . $row->bill_id) . '.tpd"><i class="fa fa-pencil"></i>&nbsp;Open</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->bill_id . '" data-code="' . $row->journal_no . '"><i class="fa fa-remove"></i>&nbsp;' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->amount > 0){
                $input_attr = '';
                if($is_history == '1' || !check_session_action($menu_id, STATUS_POSTED)) {
                    $input_attr = 'disabled';
                }
                $records["data"][] = array(
                    //'<input class="checked_posting" type="checkbox" value="' . $row->bill_id . '" name="ischecked[' . $row->bill_id . ']" ' . $input_attr . '/>',
                    '',
                    dmy_from_db($row->bill_date),
                    $row->journal_no,
                    $row->company_name,
                    //$row->subject,
                    $row->paymenttype_code,
                    format_num($row->amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            } else {
                $records["data"][] = array(
                    $i,
                    dmy_from_db($row->bill_date),
                    $row->journal_no,
                    $row->company_name,
                    //$row->subject,
                    $row->paymenttype_code,
                    format_num($row->amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
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

    public function pos_form($id = 0){
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

        $data['bill_id'] = $id;

        if($id > 0){
            $joins = array();
            $qry = $this->mdl_finance->getJoin('cs_bill_header.*','vw_bill_header as cs_bill_header', $joins, array('bill_id' => $id));
            $data['bill'] = $qry->row();

            $data['details'] = $this->db->get_where('cs_bill_detail', array('bill_id' => $data['bill_id']));
        }

        $taxtype = $this->db->get_where('tax_type', array('is_charge_default > ' => 0));
        if($taxtype->num_rows() > 0){
            $data['tax_type'] = $taxtype->row();
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('pos/pos_form', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_pos_submit(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $data = array();

        if(isset($_POST)){
            $has_error = false;
            $this->db->trans_begin();
            $bill_id = $_POST['bill_id'];

            $data['company_id'] = intval($_POST['company_id']);
            $data['client_name'] = isset($_POST['company_name']) ? $_POST['company_name'] : '';
            $data['paymenttype_id'] = intval($_POST['paymenttype_id']);
            $data['bankaccount_id'] =  isset($_POST['bankaccount_id']) ? $_POST['bankaccount_id'] : 0;
            $data['remark'] = $_POST['remark'];
            $data['is_other_charge'] = 1;
            $data['amount'] = $_POST['total_amount'];

            if ($bill_id > 0) {
                $qry_bill = $this->db->get_where('cs_bill_header', array('bill_id' => $bill_id));
                $row_bill = $qry_bill->row();
                $bill_date = $row_bill->bill_date;

                $this->mdl_general->update('cs_bill_header', array('bill_id' => $bill_id), $data);

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully update bill.');
            } else {
                $data['bill_date'] = date('Y-m-d');
                $bill_date = $data['bill_date'];
                $data['journal_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_BILLING, $data['bill_date']);

                if ($data['journal_no'] != '') {
                    $data['created_by'] = my_sess('user_id');
                    $data['created_date'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_NEW;

                    $this->db->insert('cs_bill_header', $data);
                    $bill_id = $this->db->insert_id();

                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $bill_id;
                    $data_log['feature_id'] = Feature::FEATURE_AR_BILLING;
                    $data_log['log_subject'] = 'Create AR Charge (' . $data['journal_no'] . ')';
                    $data_log['action_type'] = STATUS_NEW;
                    $this->db->insert('app_log', $data_log);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully add pos bill.');
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'Failed generating code.';
                }
            }

            if($has_error == false) {
                $temp_billdetail = array();

                if (isset($_POST['billdetail_id'])) {
                    $i = 0;

                    $item_trx = FNSpec::get(FNSpec::SALES_RESERVATION);
                    if($item_trx['transtype_id'] > 0){
                        $transTypeId = $item_trx['transtype_id'];

                        foreach ($_POST['billdetail_id'] as $key => $val) {
                            $data_detail = array();

                            $status = $_POST['status'][$key];

                            $data_detail['bill_id'] = $bill_id;
                            $data_detail['unit_id'] = 0; //;
                            $data_detail['item_desc'] = $_POST['item_desc'][$key];
                            $data_detail['item_id'] = $_POST['item_id'][$key];
                            $data_detail['masteritem_id'] = 0;
                            $data_detail['reservation_id'] = 0;
                            $data_detail['transtype_id'] = $transTypeId;
                            $data_detail['is_monthly'] = 0;
                            $data_detail['date_start'] = $bill_date;
                            $data_detail['date_end'] = $bill_date;
                            $data_detail['date_interval'] = 0;
                            $data_detail['month_interval'] = 0;
                            $data_detail['year_interval'] = 0;
                            $data_detail['item_qty'] = $_POST['item_qty'][$key];
                            $data_detail['rate'] = $_POST['item_rate'][$key];
                            $data_detail['disc_percent'] = 0;
                            $data_detail['disc_amount'] = 0 ; //$_POST['item_discount'][$key];
                            $data_detail['amount'] = $_POST['item_amount'][$key];
                            $data_detail['tax'] = $_POST['tax_amount'][$key];
                            $data_detail['currencytype_id'] = 1;
                            $data_detail['is_billed'] = 1;
                            $data_detail['reff_billdetail_id'] = 0;
                            $data_detail['modified_date'] = $bill_date;
                            $data_detail['status'] = STATUS_NEW;

                            $status_record = STATUS_NEW;
                            if ($val > 0) {
                                $old_cs_detail = $this->db->get_where('cs_bill_detail',array('billdetail_id'=>$val));
                                if($old_cs_detail->num_rows() > 0){
                                    $old_cs_detail = $old_cs_detail->row();

                                    $adjust_qty = 0;
                                    if ($status == STATUS_NEW) {
                                        if($old_cs_detail->item_qty != $data_detail['item_qty']){
                                            $adjust_qty = $data_detail['item_qty'] - $old_cs_detail->item_qty;
                                        }
                                        $this->mdl_general->update('cs_bill_detail', array('billdetail_id' => $val), $data_detail);

                                        $status_record = STATUS_NEW;
                                    } else {
                                        $adjust_qty = $old_cs_detail->item_qty * -1;
                                        $this->db->delete('cs_bill_detail', array('billdetail_id' => $val));

                                        $status_record = STATUS_DELETE;
                                    }

                                    //echo 'ADJUST QTY ' . $adjust_qty;

                                    if($adjust_qty != 0) {
                                        //CHECK WHETHER item is PO ITEM, to adjust qty
                                        $stock = $this->db->get_where('pos_item_stock', array('itemstock_id' => $data_detail['item_id']));
                                        if ($stock->num_rows() > 0) {
                                            $stock = $stock->row();
                                            if ($stock->is_service_item <= 0) {
                                                //UPDATE QTY OF PO ITEM
                                                $update_stock = array();
                                                $update_stock['itemstock_current_qty'] = round($stock->itemstock_current_qty - $adjust_qty, 0);

                                                $this->mdl_general->update('pos_item_stock', array('itemstock_id' => $data_detail['item_id']), $update_stock);
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($status == STATUS_NEW) {
                                    $this->db->insert('cs_bill_detail', $data_detail);
                                    $val = $this->db->insert_id();
                                    $status_record = STATUS_NEW;

                                    //CHECK WHETHER item is PO ITEM, to adjust qty
                                    $stock = $this->db->get_where('pos_item_stock',array('itemstock_id' => $data_detail['item_id']));
                                    if($stock->num_rows() > 0){
                                        $stock = $stock->row();
                                        if($stock->is_service_item <= 0){
                                            //UPDATE QTY OF PO ITEM
                                            $update_stock = array();
                                            if($stock->itemstock_current_qty >= $data_detail['item_qty']){
                                                $update_stock['itemstock_current_qty'] = round($stock->itemstock_current_qty - $data_detail['item_qty'],0);
                                            }else{
                                                $update_stock['itemstock_current_qty'] = 0;
                                            }

                                            $this->mdl_general->update('pos_item_stock', array('itemstock_id' => $data_detail['item_id']), $update_stock);

                                        }
                                    }

                                }
                            }

                            array_push($temp_billdetail, array('status' => $status_record, 'billdetail_id' => $val, 'item_desc' => $data_detail['item_desc'], 'transtype_id' => $data_detail['transtype_id'], 'amount' =>  $data_detail['amount'], 'discount' => $data_detail['disc_amount'], 'tax' => $data_detail['tax']));

                            $i++;
                        }
                    }else{
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'No Trans Type.';
                    }
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'No Detail Found.';
                }
            }

            if($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('pos/pos/pos_manage/1.tpd');
                    } else{
                        $result['link'] = base_url('pos/pos/pos_manage/3/' . $bill_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    private function insertARPendingBills($bill_id = 0){
        $valid = true;
        $result = array();
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($bill_id > 0) {
            //$this->load->model('finance/mdl_finance');

            try {
                $joins = array('ms_company' => 'ms_company.company_id = cs_bill_header.company_id');
                $qry_bill = $this->mdl_finance->getJoin('cs_bill_header.*, ms_company.company_name'
                    , 'cs_bill_header', $joins, array('bill_id' => $bill_id));
                if ($qry_bill->num_rows() > 0) {
                    $row_bill = $qry_bill->row();

                    $bill_details = $this->db->get_where('cs_bill_detail', array('bill_id' => $bill_id));
                    if ($bill_details->num_rows() > 0) {
                        //INSERT TO CORPORATE IF ANY
                        foreach ($bill_details->result_array() as $tbill) {
                            $billdetailid = $tbill['billdetail_id'];

                            $charges = $this->db->get_where('cs_corporate_bill', array('billdetail_id' => $billdetailid, 'is_billed <= ' => 0));
                            if ($charges->num_rows() > 0) {
                                $update_bill = array();
                                $update_bill['company_id'] = $row_bill->company_id;
                                $update_bill['tenant_id'] = 0;
                                $update_bill['billdetail_id'] = $billdetailid;
                                $update_bill['transtype_id'] = $tbill['transtype_id'];
                                $update_bill['bill_startdate'] = $tbill['date_start'];
                                $update_bill['bill_enddate'] = $tbill['date_end'];
                                $update_bill['amount'] = $tbill['amount'];
                                $update_bill['discount'] = $tbill['disc_amount'];
                                $update_bill['tax'] = $tbill['tax'];
                                $update_bill['total_amount'] = round($tbill['amount'] - $tbill['disc_amount'] + $tbill['tax'], 0);

                                $this->mdl_general->update('cs_corporate_bill', array('billdetail_id' => $billdetailid), $update_bill);
                            } else {
                                $bill = array();
                                $bill['reservation_id'] = 0;
                                $bill['company_id'] = $row_bill->company_id;
                                $bill['tenant_id'] = 0;
                                $bill['billdetail_id'] = $billdetailid;
                                $bill['unit_id'] = 0;
                                $bill['transtype_id'] = $tbill['transtype_id'];
                                $bill['bill_startdate'] = $tbill['date_start'];
                                $bill['bill_enddate'] = $tbill['date_end'];
                                $bill['amount'] = $tbill['amount'];
                                $bill['discount'] = $tbill['disc_amount'];
                                $bill['tax'] = $tbill['tax'];
                                $bill['total_amount'] = round($tbill['amount'] - $tbill['disc_amount'] + $tbill['tax'], 0);
                                $bill['is_billed'] = 0;
                                $bill['is_othercharge'] = 1;
                                $bill['status'] = STATUS_NEW;
                                $bill['month'] = 0;

                                $this->db->insert('cs_corporate_bill', $bill);
                                $insertID = $this->db->insert_id();

                            }
                        }
                    }
                }
            } catch (Exception $e) {
                $valid = false;
                $result['error'] = '1';
                $result['message'] = 'AR company charges can not be created';
                $result['debug'] = '';
            }
        }else{
            $result['error'] = '1';
            $result['message'] = 'Bill id must not empty';
            $result['debug'] = '';
        }

        return $result; //$valid;
    }

    private function insertBankPayment($bill){
        $valid = true;
        $result = array();
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if(isset($bill)) {
            //$this->load->model('finance/mdl_finance');

            try {
                if($bill->payment_type == PAYMENT_TYPE::CREDIT_CARD){
                    $ccard = $this->db->query('SELECT * FROM ms_payment_type WHERE payment_type = ' . PAYMENT_TYPE::CREDIT_CARD . ' AND status = ' . STATUS_NEW);

                    if($ccard->num_rows() > 0){
                        $card = $ccard->row();
                        $bankPercent = $card->card_percent;
                    }
                }

                //Find Bank account by paymenttype_id
                $bankAccountId = $bill->bankaccount_id;
                if($bankAccountId <= 0) {
                    if ($bill->payment_type != PAYMENT_TYPE::BANK_TRANSFER) {
                        $qry = $this->db->query("SELECT fn_bank_account.bankaccount_id FROM fn_bank_account
                                     JOIN gl_coa on gl_coa.coa_id = fn_bank_account.coa_id
                                     JOIN ms_payment_type on ms_payment_type.coa_code = gl_coa.coa_code
                                     WHERE fn_bank_account.status = " . STATUS_NEW . "
                                           AND ms_payment_type.paymenttype_id = " . $bill->paymenttype_id);
                        if ($qry->num_rows() > 0) {
                            $bankAccountId = $qry->row()->bankaccount_id;
                        } else {
                            $bankAccountId = 0;
                        }
                    }
                }

                $taxVAT = tax_vat();
                if($bankAccountId > 0){

                    $detail = array();
                    $totalCredit = 0;
                    $totalDebit = 0;

                    //SALES
                    $billDetails = $this->db->get_where('fxnBillDetail('. $bill->bill_id .')',array('bill_id' => $bill->bill_id));
                    if($billDetails->num_rows() > 0){
                        foreach($billDetails->result_array() as $det){
                            if($det['coa_id'] > 0){
                                $desc = $bill->company_name . ' [' . $det['item_code'] . '] '. $det['item_desc'];

                                //Amount
                                $detAmount = array();
                                $detAmount['coa_id'] = $det['coa_id'];
                                $detAmount['dept_id'] = 0;
                                $detAmount['journal_note'] = $desc;
                                $detAmount['journal_debit'] = 0;
                                $detAmount['journal_credit'] = $det['amount'];
                                $detAmount['reference_id'] = 0;
                                $detAmount['transtype_id'] = $det['transtype_id'];

                                array_push($detail, $detAmount);

                                //Tax
                                if ($det['tax'] > 0) {
                                    if (isset($taxVAT['coa_id'])) {
                                        $detTax = array();
                                        $detTax['coa_id'] = $taxVAT['coa_id'];
                                        $detTax['dept_id'] = 0;
                                        $detTax['journal_note'] = 'Tax ' . $desc;
                                        $detTax['journal_debit'] = 0;
                                        $detTax['journal_credit'] = $det['tax'];
                                        $detTax['reference_id'] = 0;
                                        $detTax['transtype_id'] = 0;

                                        array_push($detail, $detTax);
                                    } else {
                                        $valid = false;
                                        break;
                                    }
                                }

                                $totalCredit += round($det['amount'] + $det['tax'],0);

                            }else{
                                $valid = false;
                                break;
                            }
                        }
                    }

                    if($valid && $totalCredit > 0 && round($bill->amount,0) == $totalCredit){
                        //BANK
                        $bank_coa_id = 0;
                        $bank = $this->mdl_finance->getBankAccount($bankAccountId);
                        if (isset($bank['coa_id']))
                            $bank_coa_id = $bank['coa_id'];

                        if ($bank_coa_id > 0) {
                            $rowdet = array();
                            $rowdet['coa_id'] = $bank_coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $bill->company_name . ' - ' . $bill->journal_no;
                            $rowdet['journal_debit'] = $totalCredit;
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = 0;
                            $rowdet['transtype_id'] = 0;

                            array_push($detail, $rowdet);

                            $totalDebit = $totalCredit;
                        } else {
                            $valid = false;
                        }
                    }else{
                        $valid = false;
                    }

                    //echo 'Balance ' . $totalDebit . ' = ' . $totalCredit;

                    if($valid && $totalDebit == $totalCredit){
                        $header = array();
                        $header['journal_no'] = $bill->journal_no;
                        $header['journal_date'] = date('Y-m-d H:i:s');
                        $header['journal_remarks'] = $bill->company_name;
                        $header['modul'] = GLMOD::GL_MOD_AR;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = '';

                        $valid = $this->mdl_finance->postJournal($header,$detail);

                        //echo 'POSTING ' . $totalDebit . ' -> ' . $valid;
                    }

                    if($valid){
                        $result['error'] = '0';
                        $result['message'] = 'Success';
                    }
                }else{
                    $valid = false;
                    $result['error'] = '1';
                    $result['message'] = 'Bank payment can not be created';
                }
            } catch (Exception $e) {
                $valid = false;
                $result['error'] = '1';
                $result['message'] = 'Bank payment can not be created';
                $result['debug'] = '';
            }
        }else{
            $result['error'] = '1';
            $result['message'] = 'Bill id must not empty';
            $result['debug'] = '';
        }

        return $result; //$valid;
    }

    public function ajax_bill_multi_posting($is_hsk = 0){

        $result = array();
        $result['valid'] = array();
        $result['message'] = array();
        $result['link'] = '';
        $result['debug'] = '';

        $this->db->trans_begin();

        foreach($_POST['ischecked'] as $key => $bill_id) {
            $data['status'] = STATUS_POSTED;

            $data_log['user_id'] = my_sess('user_id');
            $data_log['log_date'] = date('Y-m-d H:i:s');
            $data_log['reff_id'] = $bill_id;
            $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION_BILL;

            if ($bill_id > 0 && $data['status'] > 0) {
                $qry = $this->db->get_where('cs_bill_header', array('bill_id' => $bill_id));
                if ($qry->num_rows() > 0) {
                    $row = $qry->row();

                    if($row->tenant_id <= 0 && $row->company_id <= 0) {
                        $reservation = $this->db->get_where('view_cs_reservation', array('reservation_id' => $row->reservation_id));
                        if ($reservation->num_rows() > 0) {
                            $reservation = $reservation->row();
                            if($reservation->reservation_type != RES_TYPE::CORPORATE){
                                $update['tenant_id'] = $reservation->tenant_id;
                                $update['company_id'] = 0;
                                $this->mdl_general->update('cs_bill_header', array('bill_id' => $bill_id), $update);
                            }
                        }
                    }

                    if ($data['status'] == STATUS_POSTED) {
                        if ($row->status == STATUS_POSTED) {
                            array_push($result['valid'], '0');
                            array_push($result['message'], $row->journal_no . ' already posted');
                        } else {
                            $qry_res = $this->db->get_where('cs_reservation_header', array('reservation_id' => $row->reservation_id));
                            $row_res = $qry_res->row();
                            if ($row_res->status == ORDER_STATUS::CHECKIN) {
                                //POSTING BILLING
                                if($is_hsk <= 0) {
                                    $valid = $this->insertARPendingBills($bill_id);
                                }else{
                                    $valid = $this->insertARPendingBills($bill_id);

                                    //Create schedule
                                    if ($valid['error'] == '0') {
                                        $valid = $this->update_reservation_hsk($bill_id);
                                    }
                                }

                                if ($valid['error'] == '0') {
                                    $this->mdl_general->update('cs_bill_header', array('bill_id' => $bill_id), $data);

                                    $data_log['log_subject'] = 'Posting Billing (' . $row->journal_no . ')';
                                    $data_log['action_type'] = STATUS_POSTED;
                                    $this->db->insert('app_log', $data_log);

                                    array_push($result['valid'], '1');
                                    array_push($result['message'], 'Successfully posting ' . $row->journal_no);
                                } else {
                                    array_push($result['valid'], '0');
                                    array_push($result['message'], $row->journal_no . ' ' . $valid['message']);
                                }
                            } else {
                                array_push($result['valid'], '0');
                                array_push($result['message'], $row->journal_no . ' Reservation status is not allowed');
                            }
                        }
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                array_push($result['valid'], '0');
                array_push($result['message'], $row->journal_no . ' Something error. Please try again later.');
            } else {
                $this->db->trans_commit();
            }
        }
        echo json_encode($result);
    }

    public function ajax_bill_single_posting($is_hsk = 0){

        $result = array();
        $result['valid'] = array();
        $result['message'] = array();
        $result['link'] = '';
        $result['debug'] = '';

        $bill_id = $_POST['bill_id'];

        if($bill_id > 0){
            $this->db->trans_begin();

            $data['status'] = STATUS_POSTED;

            $data_log['user_id'] = my_sess('user_id');
            $data_log['log_date'] = date('Y-m-d H:i:s');
            $data_log['reff_id'] = $bill_id;
            $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION_BILL;

            if ($bill_id > 0 && $data['status'] > 0) {
                $qry = $this->db->get_where('fxnBillHeader('. $bill_id .')');
                if ($qry->num_rows() > 0) {
                    $row = $qry->row();

                    if ($data['status'] == STATUS_POSTED) {
                        if ($row->status == STATUS_POSTED) {
                            array_push($result['valid'], '0');
                            array_push($result['message'], $row->journal_no . ' already posted');
                        } else {
                            $this->load->model('finance/mdl_finance');

                            if($row->payment_type == PAYMENT_TYPE::AR_TRANSFER){
                                $valid = $this->insertARPendingBills($bill_id);
                            }else{
                                $valid = $this->insertBankPayment($row);
                            }

                            $validJournal = true;
                            if($is_hsk <= 0) {
                                //Supplies Expense
                                //   Supplies
                                $validJournal = $this->mdl_finance->posting_item_supplies_only($bill_id);
                            }

                            //echo $valid['error'] . ' -> ' . $valid['message'];
                            if ($valid['error'] == '0' && $validJournal) {
                                $this->mdl_general->update('cs_bill_header', array('bill_id' => $bill_id), $data);

                                $data_log['log_subject'] = 'Posting Charge (' . $row->journal_no . ')';
                                $data_log['action_type'] = STATUS_POSTED;
                                $this->db->insert('app_log', $data_log);

                                array_push($result['valid'], '1');
                                array_push($result['message'], 'Successfully posting ' . $row->journal_no);
                            } else {
                                array_push($result['valid'], '0');
                                array_push($result['message'], $row->journal_no . ' ' . $valid['message']);
                            }
                        }
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                array_push($result['valid'], '0');
                array_push($result['message'], $row->journal_no . ' posting failed. Please try again later.');
            } else {
                $this->db->trans_commit();

                if($is_hsk <= 0) {
                    $result['link'] = base_url('pos/pos/pos_manage/3/' . $bill_id . '.tpd');
                }else{
                    $result['link'] = base_url('pos/pos/pos_manage/3/' . $bill_id . '.tpd');
                }
            }
        }


        echo json_encode($result);
    }

    #endregion

    #region Look up

    public function ajax_modal_othercharge(){
        $this->load->view('frontdesk/reservation/ajax_modal_othercharge');
    }

    public function get_modal_othercharge($num_index = 0, $reservation_id = 0){
        $where = array();
        $like = array();

        if(isset($_REQUEST['filter_code'])){
            if($_REQUEST['filter_code'] != ''){
                $like['tenant_account'] = $_REQUEST['filter_code'];
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company_name'])){
            if($_REQUEST['filter_company_name'] != ''){
                $like['company_name'] = $_REQUEST['filter_company_name'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_cs_tenant_checkin',$where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'reservation_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_cs_tenant_checkin',$where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $max_month = num_of_months(date('Y-m-d'),ymd_from_db($row->departure_date));

            $startDate = new DateTime('');
            $endDate = DateTime::createFromFormat('Y-m-d', ymd_from_db($row->departure_date));
		    $endDate->modify('+1 day');

		    $diff =  $startDate->diff($endDate);
            if($diff->d > 0){
                $max_month++;
            }

            $text = "Select";
            $attr = '';
            $attr .= ' data-reservation-id="' . $row->reservation_id . '" ';
            $attr .= ' data-reservation-type="' . $row->reservation_type . '" ';
            $attr .= ' data-reservation-code="' . $row->reservation_code . '" ';
            $attr .= ' data-tenant-id="' . $row->tenant_id . '" ';
            $attr .= ' data-tenant-name="' . $row->tenant_fullname . '" ';
            $attr .= ' data-company-id="' . $row->company_id . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-max-month="' . $max_month . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            if ($reservation_id == $row->reservation_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-reservation" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $row->reservation_code,
                RES_TYPE::caption($row->reservation_type),
                $row->tenant_fullname,
                $row->company_name,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function select_tenant_unit(){
        $result ='';

        $reservationId = $_POST['reservation_id'];

        if($reservationId > 0){
            $qry = $this->db->query('select distinct ms_unit.unit_id, ms_unit.unit_code from ms_unit
                                     join cs_reservation_detail on cs_reservation_detail.unit_id = ms_unit.unit_id
                                     join cs_reservation_header on cs_reservation_detail.reservation_id = cs_reservation_header.reservation_id
                                     where cs_reservation_header.reservation_id = ' . $reservationId); //cs_reservation_header.status = ' . ORDER_STATUS::CHECKIN . '

            if($qry->num_rows() > 0){
                foreach($qry->result_array() as $unit){
                    $result .= '<option value="' . $unit['unit_id'] . '">' . $unit['unit_code'] . '</option>';
                }
            }
        }

        echo $result;
    }

    public function ajax_modal_item(){
        $this->load->view('pos/modal_item');
    }

    public function get_item_list_by_id($itemstock_id, $index = 0, $unique_id = '', $item_id_exist){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        if(isset($_REQUEST['filter_code'])){
            if($_REQUEST['filter_code'] != ''){
                $like['vw.item_code'] = $_REQUEST['filter_code'];
            }
        }
        if(isset($_REQUEST['filter_desc'])){
            if($_REQUEST['filter_desc'] != ''){
                $like['vw.item_desc'] = $_REQUEST['filter_desc'];
            }
        }
        if(isset($_REQUEST['filter_uom'])){
            if($_REQUEST['filter_uom'] != ''){
                $like['stk.stock_uom'] = $_REQUEST['filter_uom'];
            }
        }

        if(isset($_REQUEST['filter_is_service'])){
            if($_REQUEST['filter_is_service'] != ''){
                if($_REQUEST['filter_is_service'] == 'A'){
                    $where['vw.is_service_item <='] = 0;
                }else if($_REQUEST['filter_is_service'] == 'B'){
                    $where['vw.is_service_item >'] = 0;
                }
            }
        }

        $joins = array("view_pos_item_stock stk"=>"stk.itemstock_id = vw.itemstock_id");
        $iTotalRecords = $this->mdl_finance->countJoin("get_pos_item_active AS vw", $joins, $where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'vw.item_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'vw.item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'vw.item_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'vw.stock_uom ' . $_REQUEST['order'][0]['dir'];
            }

        }

        $qry = $this->mdl_finance->getJoin("vw.*,stk.stock_uom ","get_pos_item_active AS vw", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false);

        //$records["debug"] = $this->db->last_query();
        $item_id_exist = trim($item_id_exist);
        $isexist = false;

        $arr_id = array();
        if($item_id_exist != '-'){
            $isexist = true;
            $arr_id = explode('_', $item_id_exist);
        }

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';
            $attr .= ' item-stock-id="' . $row->itemstock_id . '" ';
            $attr .= ' item-code="' . $row->item_code . '" ';
            $attr .= ' item-desc="' . $row->item_desc . '" ';
            $attr .= ' price-lock="' . $row->price_lock . '" ';
            $attr .= ' is-service="' . $row->is_service_item . '" ';
            $attr .= ' unit-price="' . $row->unit_price . '" ';
            $attr .= ' unit-discount="' . $row->unit_discount . '" ';
            $attr .= ' max-qty="' . $row->itemstock_current_qty . '" ';
            $attr .= ' tax-type-percent="' . $row->taxtype_percent . '" ';
            $attr .= ' tax-type-code="' . $row->taxtype_code . '" ';
            $attr .= ' unique-id="' . $unique_id . '" ';
            $attr .= ' data-index="' . $index .'"';

            $text = "Select";
            if ($isexist) {
                foreach ($arr_id as $val) {
                    if ($val == $row->itemstock_id) {
                        $attr .= 'selected="selected" disabled="disabled"';
                        $text = "Selected";
                    }
                }
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-item" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';
            if($itemstock_id == $row->itemstock_id){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }

            $records["data"][] = array(
                $row->item_code,
                $row->item_desc,
                ($row->is_service_item > 0 ? 'S' : 'I'),
                $row->stock_uom,
                format_num($row->itemstock_current_qty,0),
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $unique_id;

        echo json_encode($records);
    }

    public function get_coa_list_by_code($coa_code = ''){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        $where['gl_coa.status <>'] = STATUS_DELETE;
        $where['gl_coa.is_display >'] = 0;

        if(isset($_REQUEST['filter_coacode'])){
            if($_REQUEST['filter_coacode'] != ''){
                $like['gl_coa.coa_code'] = $_REQUEST['filter_coacode'];
            }
        }
        if(isset($_REQUEST['filter_coadesc'])){
            if($_REQUEST['filter_coadesc'] != ''){
                $like['gl_coa.coa_desc'] = $_REQUEST['filter_coadesc'];
            }
        }
        if(isset($_REQUEST['filter_classid'])){
            if($_REQUEST['filter_classid'] != ''){
                $where['gl_class.class_id'] = $_REQUEST['filter_classid'];
            }
        }
        if(isset($_REQUEST['filter_classtype'])){
            if($_REQUEST['filter_classtype'] != ''){
                $where['gl_class.class_type'] = $_REQUEST['filter_classtype'];
            }
        }

        $iTotalRecords = $this->mdl_finance->countCOA($where, $like, "");

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'gl_coa.coa_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'gl_coa.coa_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'gl_coa.coa_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'gl_class.class_id ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'gl_class.class_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'gl_coa.is_debit ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'gl_coa.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getCOA($where, $like, $order, $iDisplayLength, $iDisplayStart, "");

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';
            $attr .= ' coa-id="' . $row->coa_id . '" ';
            $attr .= ' coa-code="' . $row->coa_code . '" ';
            $attr .= ' coa-desc="' . $row->coa_desc . '" ';

            $text = "Select";
            if ($row->coa_code == $coa_code) {
                $attr .= 'selected="selected" disabled="disabled"';
                $text = "Selected";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-coa" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';
            if($coa_code == $row->coa_code){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }

            $records["data"][] = array(
                $row->coa_code,
                $row->coa_desc,
                $row->class_code,
                GLClassType::class_type_name($row->class_type),
                ($row->is_debit > 0) ? 'D' : 'C',
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    #endregion

    #region Main

    public function ajax_action(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $this->db->trans_begin();

        $bill_id = $_POST['bill_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $bill_id;
        $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION_BILL;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($bill_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('cs_bill_header', array('bill_id' => $bill_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_POSTED) {
                    if ($row->status == STATUS_POSTED) {
                        $result['valid'] = '0';
                        $result['message'] = 'Bill already posted.';
                    } else {
                        $qry_res = $this->db->get_where('cs_reservation_header', array('reservation_id' => $row->reservation_id));
                        $row_res = $qry_res->row();
                        if ($row_res->status == ORDER_STATUS::CHECKIN) {
                            //POSTING GRN
                            $valid = $this->posting_billing($bill_id);
                            $result['debug'] = $valid;

                            if ($valid['error'] == '0') {
                                $this->mdl_general->update('cs_bill_header', array('bill_id' => $bill_id), $data);

                                $data_log['log_subject'] = 'Posting Billing (' . $row->journal_no . ')';
                                $data_log['action_type'] = STATUS_POSTED;
                                $this->db->insert('app_log', $data_log);

                                $result['message'] = 'Successfully posting Billing.';
                            } else {
                                $result['valid'] = '0';
                                $result['message'] = $valid['message'];
                            }
                        } else {
                            $result['valid'] = '0';
                            $result['message'] = 'Reservation status is not allowed.';
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Bill already canceled.';
                    } else {
                        $this->mdl_general->update('cs_bill_header', array('bill_id' => $bill_id), $data);

                        $data_log['log_subject'] = 'Cancel Billing (' . $row->journal_no . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel Billing.';
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

    #endregion

    #region Print

    public function pdf_salesreceipt($bill_id = 0) {
        if($bill_id > 0){
            //Reservation
            $qry = $this->db->get_where('fxnBillHeader('. $bill_id .')', array('bill_id' => $bill_id));
            if($qry->num_rows() > 0){
                $data['row'] = $qry->row_array();

                $folio_caption = 'BILL';

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                $data['folio_title'] = $folio_caption;
                $data['guest_info'] = $data['row']['company_name'];

                //Invoice detail
                $details = $this->db->get_where('fxnBillDetail(' . $bill_id . ')');
                if($details->num_rows() > 0){
                    $data['detail'] = $details->result_array();
                }

                $data['is_unpaid_only'] = false;

                $this->load->view('pos/pdf_salesreceipt_a5', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A5", "landscape");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']['journal_no'] . ".pdf", array('Attachment'=>0));

            }else{
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    public function pdf_salesreceipt_old($bill_id = 0) {
        if($bill_id > 0){
            //Reservation
            $qry = $this->db->get_where('fxnBillHeader('. $bill_id .')', array('bill_id' => $bill_id));
            if($qry->num_rows() > 0){
                $data['row'] = $qry->row_array();

                $folio_caption = 'Sales Receipt';

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                $data['folio_title'] = $folio_caption;
                $data['guest_info'] = $data['row']['company_name'];

                //Invoice detail
                $details = $this->db->get_where('fxnBillDetail(' . $bill_id . ')');
                if($details->num_rows() > 0){
                    $data['detail'] = $details->result_array();
                }

                $data['is_unpaid_only'] = false;

                $this->load->view('pos/pdf_salesreceipt', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']['journal_no'] . ".pdf", array('Attachment'=>0));

            }else{
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    #endregion

}

/* End of file registration.php */
/* Location: ./application/controllers/frondesk/registration.php */