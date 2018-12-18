<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class billing extends CI_Controller {

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
		$this->bill_manage();
	}

    #region Billing

    public function bill_manage($type = 1, $bill_id = 0){
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
            $this->load->view('frontdesk/management/bill_manage.php', $data);
            $this->load->view('layout/footer');
        } else if ($type == 3) {
            $this->bill_form($bill_id);
        }
    }

    public function get_bill_manage($menu_id = 0, $is_history = '0'){
        $this->load->model('finance/mdl_finance');

        $where = array();
        if ($is_history == '0') {
            $where['cs_bill_header.status'] = STATUS_NEW;
            $whereStr = '';
        } else {
            $whereStr = 'cs_bill_header.status IN(' . STATUS_POSTED . ',' . STATUS_CLOSED . ')';
        }

        $where['cs_bill_header.company_id > '] = 0;
        $where['cs_bill_header.is_other_charge > '] = 0;
        $where['cs_bill_header.paymenttype_id <='] = 0;

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

        if(isset($_REQUEST['filter_company_name'])){
            if($_REQUEST['filter_company_name'] != ''){
                $like['ms_company.company_name'] = $_REQUEST['filter_company_name'];
            }
        }

        if(isset($_REQUEST['filter_subject'])){
            if($_REQUEST['filter_subject'] != ''){
                $like['cs_bill_header.subject'] = $_REQUEST['filter_subject'];
            }
        }

        $joins = array('ms_company' => 'ms_company.company_id = cs_bill_header.company_id');
        $iTotalRecords = $this->mdl_finance->countJoin('cs_bill_header', $joins, $where, $like, '',array(),$whereStr);

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

        $qry = $this->mdl_finance->getJoin('cs_bill_header.*, ms_company.company_name'
            ,'cs_bill_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false,'',array(),$whereStr);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/other_charge/3/' . $row->bill_id) . '.tpd"><i class="fa fa-pencil"></i>&nbsp;Open</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->bill_id . '" data-code="' . $row->journal_no . '"><i class="fa fa-remove"></i>&nbsp;' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->amount > 0){
                $input_attr = '';
                if($is_history == '1' || !check_session_action($menu_id, STATUS_POSTED) || $row->reservation_status != ORDER_STATUS::CHECKIN) {
                    $input_attr = 'disabled';
                }
                $records["data"][] = array(
                    //'<input class="checked_posting" type="checkbox" value="' . $row->bill_id . '" name="ischecked[' . $row->bill_id . ']" ' . $input_attr . '/>',
                    $i,
                    dmy_from_db($row->bill_date),
                    $row->journal_no,
                    $row->company_name,
                    $row->subject,
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
                    $row->subject,
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
        $records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function bill_form($id = 0){
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
            $joins = array('ms_company' => 'ms_company.company_id = cs_bill_header.company_id');
            $qry = $this->mdl_finance->getJoin('cs_bill_header.*, ms_company.company_name','cs_bill_header', $joins, array('bill_id' => $id));
            $data['bill'] = $qry->row();

            $data['details'] = $this->db->get_where('cs_bill_detail', array('bill_id' => $data['bill_id']));
        }

        $taxtype = $this->db->get_where('tax_type', array('is_charge_default > ' => 0));
        if($taxtype->num_rows() > 0){
            $data['tax_type'] = $taxtype->row();
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/charge/bill_form', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_bill_submit(){
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
            $data['is_other_charge'] = 1;
            $data['amount'] = $_POST['total_amount'];
            $data['subject'] = $_POST['subject'];
            $data['bankaccount_id'] = 0;

            //OBTAIN DEFAULT AR TRANSFER PAYMENT
            $paymenttype_id = 0;

            $data['paymenttype_id'] = $paymenttype_id;

            if($data['company_id'] > 0){
                if ($bill_id > 0) {
                    $qry_bill = $this->db->get_where('cs_bill_header', array('bill_id' => $bill_id));
                    $row_bill = $qry_bill->row();
                    $bill_date = $row_bill->bill_date;

                    $this->mdl_general->update('cs_bill_header', array('bill_id' => $bill_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully update Billing.');
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
                        $this->session->set_flashdata('flash_message', 'Successfully add Billing.');
                    } else {
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }
            }else{
                $has_error = true;
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

                                    //echo 'ADJUST QTY ' . $val . ' = ' . $adjust_qty;

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
                        $result['link'] = base_url('ar/corporate_bill/other_charge/1.tpd');
                    } else{
                        $result['link'] = base_url('ar/corporate_bill/other_charge/3/' . $bill_id . '.tpd');
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
        $is_post_supplies = isset($_POST['post_item_supplies']) ? $_POST['post_item_supplies'] <= 0 ? false : true : true;

        if($bill_id > 0){
            $this->db->trans_begin();

            $data['status'] = STATUS_POSTED;

            $data_log['user_id'] = my_sess('user_id');
            $data_log['log_date'] = date('Y-m-d H:i:s');
            $data_log['reff_id'] = $bill_id;
            $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION_BILL;

            if ($bill_id > 0 && $data['status'] > 0) {
                $qry = $this->db->get_where('cs_bill_header', array('bill_id' => $bill_id));
                if ($qry->num_rows() > 0) {
                    $row = $qry->row();

                    if ($data['status'] == STATUS_POSTED) {
                        if ($row->status == STATUS_POSTED) {
                            array_push($result['valid'], '0');
                            array_push($result['message'], $row->journal_no . ' already posted');
                        } else {
                            $this->load->model('finance/mdl_finance');

                            $valid = $this->insertARPendingBills($bill_id);

                            $validJournal = true;

                            if($is_post_supplies) {
                                if ($is_hsk <= 0) {
                                    //Supplies Expense
                                    //   Supplies
                                    $validJournal = $this->mdl_finance->posting_item_supplies_only($bill_id);
                                }
                            }

                            if ($valid['error'] == '0' && $validJournal) {
                                $this->mdl_general->update('cs_bill_header', array('bill_id' => $bill_id), $data);

                                $data_log['log_subject'] = 'Posting Company Charge (' . $row->journal_no . ')';
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
                    $result['link'] = base_url('ar/corporate_bill/other_charge/3/' . $bill_id . '.tpd');
                }else{
                    $result['link'] = base_url('ar/corporate_bill/other_charge/3/' . $bill_id . '.tpd');
                }
            }
        }


        echo json_encode($result);
    }

    #endregion

    #region Billing Housekeeping Package

    public function bill_hsk_manage($type = 1, $bill_id = 0){
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
            $this->load->view('frontdesk/billing/bill_hsk_manage.php', $data);
            $this->load->view('layout/footer');
        } else if ($type == 3) {
            $this->bill_hsk_form($bill_id);
        }
    }

    public function get_bill_hsk_manage($menu_id = 0, $is_history = '0'){
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
        $where['cs_bill_header.is_hsk > '] = 0;

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
        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company_name'])){
            if($_REQUEST['filter_company_name'] != ''){
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company_name'];
            }
        }

        $joins = array('view_cs_reservation' => 'cs_bill_header.reservation_id = view_cs_reservation.reservation_id',
                       'cs_bill_detail' => 'cs_bill_detail.bill_id = cs_bill_header.bill_id',
                       'pos_master_item' => 'pos_master_item.masteritem_id = cs_bill_detail.masteritem_id');
        $iTotalRecords = $this->mdl_finance->countJoin('cs_bill_header', $joins, $where, $like, '',array(),$whereStr);

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
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('cs_bill_header.*, pos_master_item.masteritem_code, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, view_cs_reservation.reservation_code, view_cs_reservation.reservation_date, view_cs_reservation.reservation_type as reservation_type,
        view_cs_reservation.status as reservation_status'
            ,'cs_bill_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '',array(),$whereStr);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('frontdesk/billing/bill_hsk_manage/3/' . $row->bill_id) . '.tpd"><i class="fa fa-pencil"></i>&nbsp;Edit</a> </li>';
            if($row->status == STATUS_NEW){
                /*if(check_session_action($menu_id, STATUS_POSTED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_POSTED . '" data-id="' . $row->bill_id . '" data-code="' . $row->journal_no . '">' . get_action_name(STATUS_POSTED, false) . '</a> </li>';
                }*/
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->bill_id . '" data-code="' . $row->journal_no . '"><i class="fa fa-remove"></i>&nbsp;' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            $bill_to = 'Guest';
            if($row->company_id > 0){
                $bill_to = 'Company';
            }
            if($row->amount > 0){
                $input_attr = '';
                if($is_history == '1' || !check_session_action($menu_id, STATUS_POSTED) || $row->reservation_status != ORDER_STATUS::CHECKIN) {
                    $input_attr = 'disabled';
                }
                $records["data"][] = array(
                    '<input class="checked_posting" type="checkbox" value="' . $row->bill_id . '" name="ischecked[' . $row->bill_id . ']" ' . $input_attr . '/>',
                    dmy_from_db($row->bill_date),
                    $row->journal_no,
                    $row->reservation_code,
                    RES_TYPE::caption($row->reservation_type),
                    $row->tenant_fullname,
                    $row->company_name,
                    $bill_to,
                    $row->masteritem_code,
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
                    '',
                    dmy_from_db($row->bill_date),
                    $row->journal_no,
                    $row->reservation_code,
                    RES_TYPE::caption($row->reservation_type),
                    $row->tenant_fullname,
                    $row->company_name,
                    $bill_to,
                    $row->masteritem_code,
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
        $records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function bill_hsk_form($id = 0){
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
            $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = cs_bill_header.reservation_id');
            $qry = $this->mdl_finance->getJoin('cs_bill_header.*, view_cs_reservation.reservation_code, view_cs_reservation.reservation_type,
                                view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, view_cs_reservation.departure_date, view_cs_reservation.status as res_status','cs_bill_header', $joins, array('bill_id' => $id));
            $data['bill'] = $qry->row();

            $data['details'] = $this->db->get_where('cs_bill_detail', array('bill_id' => $data['bill_id']));

            $hsk = $this->db->get_where('cs_bill_hsk', array('bill_id' => $data['bill_id']));
            if($hsk->num_rows() > 0) {
                $data['hsk'] = $hsk->row();
            }

            $max_month = num_of_months(date('Y-m-d'),ymd_from_db($data['bill']->departure_date));
            $data['max_month'] = $max_month;

            $package = $this->db->get_where('pos_master_item', array('masteritem_id'=>$data['hsk']->masteritem_id));
            if($package->num_rows() > 0){
                $data['max_day_of_week'] = $package->row()->no_of_week;
            }
        }

        $taxtype = $this->db->get_where('tax_type', array('is_charge_default > ' => 0));
        if($taxtype->num_rows() > 0){
            $data['tax_type'] = $taxtype->row();
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('frontdesk/billing/bill_hsk_form', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_bill_hsk_submit(){
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

            $debtor_type = isset($_POST['debtor_type']) ? $_POST['debtor_type'] : 1;
            $data['reservation_id'] = intval($_POST['reservation_id']);
            $data['is_other_charge'] = 1;
            $data['is_hsk'] = 1;
            $data['amount'] = $_POST['total_amount'];

            if($data['reservation_id'] > 0){
                $reservation = $this->db->get_where('cs_reservation_header',array('reservation_id' => $data['reservation_id']));
                if($reservation->num_rows() > 0){
                    $reservation = $reservation->row();
                    if($reservation->reservation_type == RES_TYPE::CORPORATE){
                        if($debtor_type > 0){
                            $data['company_id'] = $reservation->company_id;
                            $data['tenant_id'] = 0;
                        }else{
                            $data['tenant_id'] = $reservation->tenant_id;
                            $data['company_id'] = 0;
                        }
                    }else{
                        $data['tenant_id'] = $reservation->tenant_id;
                        $data['company_id'] = 0;
                    }

                    if($data['company_id'] > 0 || $data['tenant_id'] > 0){
                        if ($bill_id > 0) {
                            $qry_bill = $this->db->get_where('cs_bill_header', array('bill_id' => $bill_id));
                            $row_bill = $qry_bill->row();
                            $bill_date = $row_bill->bill_date;

                            $this->mdl_general->update('cs_bill_header', array('bill_id' => $bill_id), $data);

                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Successfully update Billing.');
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
                                $data_log['log_subject'] = 'Create AR Invoice Billing (' . $data['journal_no'] . ')';
                                $data_log['action_type'] = STATUS_NEW;
                                $this->db->insert('app_log', $data_log);

                                $this->session->set_flashdata('flash_message_class', 'success');
                                $this->session->set_flashdata('flash_message', 'Successfully add Billing.');
                            } else {
                                $has_error = true;

                                $result['valid'] = '0';
                                $result['message'] = 'Failed generating code.';
                            }
                        }
                    }else{
                        $has_error = true;
                    }

                    $masteritem_id = 0;
                    if($has_error == false) {
                        if (isset($_POST['billdetail_id'])) {
                            $i = 0;

                            $item_trx = FNSpec::get(FNSpec::SALES_HOUSEKEEPING);
                            if($item_trx['transtype_id'] > 0){
                                $transTypeId = $item_trx['transtype_id'];

                                foreach ($_POST['billdetail_id'] as $key => $val) {
                                    $data_detail = array();

                                    $status = $_POST['status'][$key];

                                    $data_detail['bill_id'] = $bill_id;
                                    $data_detail['unit_id'] = $_POST['unit_id'][$key];
                                    $data_detail['masteritem_id'] = $_POST['masteritem_id'][$key];
                                    $data_detail['item_id'] = 0;
                                    $data_detail['reservation_id'] = $data['reservation_id'];
                                    $data_detail['transtype_id'] = $transTypeId;
                                    $data_detail['is_monthly'] = 0;
                                    $data_detail['date_start'] = isset($_POST['hsk_start_date']) ? dmy_to_ymd($_POST['hsk_start_date']) : $bill_date;
                                    $data_detail['date_end'] = isset($_POST['hsk_end_date']) ? dmy_to_ymd($_POST['hsk_end_date']) : $bill_date;
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
                                    $data_detail['is_billed'] = 15;
                                    $data_detail['reff_billdetail_id'] = 0;
                                    $data_detail['modified_date'] = $bill_date;
                                    $data_detail['status'] = STATUS_NEW;

                                    if ($val > 0) {
                                        if ($status == STATUS_NEW) {
                                            $this->mdl_general->update('cs_bill_detail', array('billdetail_id' => $val), $data_detail);
                                        } else {
                                            $this->db->delete('cs_bill_detail', array('billdetail_id' => $val));
                                        }
                                    }else {
                                        if ($status == STATUS_NEW) {
                                            $this->db->insert('cs_bill_detail', $data_detail);
                                        }
                                    }

                                    $masteritem_id = $data_detail['masteritem_id'];
                                    $i++;
                                }
                            }else{
                                $has_error = true;

                                $result['valid'] = '0';
                                $result['message'] = 'No Housekeeping Trans Type in Posting Parameter.';
                            }
                        } else {
                            $has_error = true;

                            $result['valid'] = '0';
                            $result['message'] = 'No Detail Found.';
                        }
                    }

                    // INSERT/MODIFY cs_bill_hsk
                    if(!$has_error){
                        $chk_day = isset($_POST['chk_day']) ? $_POST['chk_day'] : array();
                        if($masteritem_id > 0 && count($chk_day) > 1) {
                            $data_hsk = array();
                            $data_hsk['bill_id'] = $bill_id;
                            $data_hsk['hsk_start_date'] = isset($_POST['hsk_start_date']) ? dmy_to_ymd($_POST['hsk_start_date']) : $bill_date;
                            $data_hsk['hsk_end_date'] = isset($_POST['hsk_end_date']) ? dmy_to_ymd($_POST['hsk_end_date']) : $bill_date;
                            $data_hsk['masteritem_id'] = $masteritem_id;
                            $data_hsk['day_of_week'] = implode(',',$chk_day);
                            $data_hsk['status'] = STATUS_NEW;

                            $cs_hsk = $this->db->get_where('cs_bill_hsk', array('bill_id' => $bill_id));
                            if ($cs_hsk->num_rows() > 0) {
                                $cs_hsk = $cs_hsk->row();

                                $this->mdl_general->update('cs_bill_hsk', array('id' => $cs_hsk->id), $data_hsk);
                            } else {
                                $this->db->insert('cs_bill_hsk', $data_hsk);
                                $id = $this->db->insert_id();
                                if($id <= 0){
                                    $has_error = true;

                                    $result['valid'] = '0';
                                    $result['message'] = 'Housekeeping bill can not be inserted.';
                                }
                            }
                        }else{
                            $has_error = true;

                            $result['valid'] = '0';
                            $result['message'] = 'No Package Found.';
                        }
                    }
                }
            }

            if($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('frontdesk/billing/bill_hsk_manage/1.tpd');
                    } else{
                        $result['link'] = base_url('frontdesk/billing/bill_hsk_manage/3/' . $bill_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    private function posting_billing_hsk_v1($bill_id = 0){
        $this->load->model('finance/mdl_finance');
        $result = array();
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($bill_id > 0) {
            $joins = array('view_cs_reservation' => 'cs_bill_header.reservation_id = view_cs_reservation.reservation_id');
            $qry_bill = $this->mdl_finance->getJoin('cs_bill_header.*, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, view_cs_reservation.reservation_code, view_cs_reservation.reservation_type '
                ,'cs_bill_header', $joins, array('bill_id' => $bill_id));
            if ($qry_bill->num_rows() > 0) {
                $row_bill = $qry_bill->row();

                $valid = $this->insertCorporateBills($bill_id);

                if($valid) {
                    if($row_bill->company_id <= 0){
                        $detail = array();

                        $totalDebit = 0;
                        $totalCredit = 0;
                        $journal_desc = $row_bill->journal_no . ' / ' . $row_bill->tenant_fullname . ' / ' . $row_bill->reservation_code;
                        if ($row_bill->company_id > 0) {
                            $journal_desc = $row_bill->journal_no . ' / ' . $row_bill->company_name . ' / ' . $row_bill->reservation_code;
                        }

                        $qryDetails = $this->db->query("SELECT SUM(amount - disc_amount + tax) AS total FROM cs_bill_detail WHERE bill_id = " . $bill_id);
                        if ($qryDetails->num_rows() > 0) {
                            $rowDetail = $qryDetails->row();
                            //echo 'Total ' . $rowDetail->total;
                            if ($rowDetail->total > 0) {
                                $coa_id_hsk = 0;

                                $ar = FNSpec::get(FNSpec::SALES_HOUSEKEEPING);
                                if ($ar['coa_id'] > 0) {
                                    $coa_id_hsk = $ar['coa_id'];
                                } else {
                                    $trxtype = $this->db->get_where('ms_transtype', array('transtype_id' => $ar['transtype_id']));
                                    if ($trxtype->num_rows() > 0) {
                                        $coa_id_hsk = $trxtype->row()->coa_id;
                                    }
                                }

                                if ($coa_id_hsk > 0) {
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $coa_id_hsk;
                                    $rowdet['dept_id'] = 0;
                                    $rowdet['journal_note'] = $journal_desc;
                                    $rowdet['journal_debit'] = 0;
                                    $rowdet['journal_credit'] = $rowDetail->total;
                                    $rowdet['reference_id'] = $bill_id;
                                    $rowdet['transtype_id'] = $ar['transtype_id'];

                                    array_push($detail, $rowdet);

                                    $totalCredit += $rowDetail->total;
                                } else {
                                    $result['error'] = '1';
                                    $result['message'] = 'COA for Housekeeping Sales not found. Please update posting parameter!';
                                }
                            }
                        }

                        if ($result['error'] == '0') {
                            if ($totalCredit > 0 && count($detail) > 0) {
                                $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                                if ($row_bill->reservation_type == RES_TYPE::CORPORATE) {
                                    if ($row_bill->company_id > 0) {
                                        $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                                    }
                                }

                                if ($ar['coa_id'] > 0) {
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $ar['coa_id'];
                                    $rowdet['dept_id'] = 0;
                                    $rowdet['journal_note'] = $journal_desc;
                                    $rowdet['journal_debit'] = $totalCredit;
                                    $rowdet['journal_credit'] = 0;
                                    $rowdet['reference_id'] = $bill_id;
                                    $rowdet['transtype_id'] = $ar['transtype_id'];

                                    array_push($detail, $rowdet);

                                    $totalDebit = $totalCredit;
                                }
                            }
                        }

                        if ($result['error'] == '0') {
                            if ($totalDebit == $totalCredit && $totalDebit != 0) {
                                $header = array();
                                $header['journal_no'] = $row_bill->journal_no;
                                $header['journal_date'] = $row_bill->bill_date;
                                $header['journal_remarks'] = $journal_desc;
                                $header['modul'] = GLMOD::GL_MOD_AR;
                                $header['journal_amount'] = $totalDebit;
                                $header['reference'] = strval($bill_id);

                                $valid = $this->mdl_finance->postJournal($header, $detail);

                                if ($valid == false) {
                                    $result['error'] = '1';
                                    $result['message'] = 'Failed insert journal.';
                                }
                            } else {
                                $result['error'] = '1';
                                $result['message'] = 'Amount is not valid ' . $totalDebit;
                            }
                        }
                    }
                }else{
                    $result['error'] = '1';
                    $result['message'] = 'Bill can not be created.';
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'Bill not found.';
            }
        }

        return $result;
    }

    private function update_reservation_hsk($bill_id = 0){
        $result = array();
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($bill_id > 0) {
            $this->load->model('finance/mdl_finance');

            $joins = array('cs_bill_hsk' => 'cs_bill_header.bill_id = cs_bill_hsk.bill_id');
            $qry_bill = $this->mdl_finance->getJoin('cs_bill_header.reservation_id, cs_bill_hsk.* ','cs_bill_header', $joins, array('cs_bill_header.bill_id' => $bill_id));
            if($qry_bill->num_rows() > 0){
                $bill = $qry_bill->row();

                //SQL DoW 1=Sunday, PHP DoW 1=Monday
                //Convert php DoW into SQL Server
                $dow = explode(',',$bill->day_of_week);
                $sql_dow = array();
                foreach($dow as $d){
                    if($d == 7){
                        //$sql_dow[] = 1;
                        array_push($sql_dow, 1);
                    }else{
                        //$sql_dow[] = $d++;
                        array_push($sql_dow, ($d+1));
                    }
                }

                if(count($sql_dow) > 0){
                    $where_dw = implode(',',$sql_dow);

                    $update_qry = "UPDATE cs_reservation_detail SET hsk = 1 WHERE reservation_id = " . $bill->reservation_id . "
                              AND CONVERT(date, checkin_date) BETWEEN '" . ymd_from_db($bill->hsk_start_date) . "' AND '" . ymd_from_db($bill->hsk_end_date) . "' AND ISNULL(hsk,0) <= 0 AND DATEPART(dw, checkin_date) IN(". $where_dw . ")";

                    $updated = $this->db->query($update_qry);

                    //$result['error'] = '1';
                    //echo $update_qry;
                    //$result['debug'] = $update_qry;
                }

            }else{
                $result['error'] = '1';
                $result['message'] = 'Bill not found';
            }
        }else{
            $result['error'] = '1';
            $result['message'] = 'Bill not exist ' . $bill_id;
        }

        return $result;
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
}

/* End of file registration.php */
/* Location: ./application/controllers/frondesk/registration.php */