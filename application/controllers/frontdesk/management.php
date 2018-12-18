<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class management extends CI_Controller {

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
		$this->checkin_form();
	}

    #region CheckIn Form
    public function checkin_form($reservation_id = 0){
        $this->load->model('frontdesk/mdl_frontdesk');

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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data['reservation_id'] = $reservation_id;

        $valid = true;
        if($reservation_id > 0){
            $qry = $this->db->get_where('view_cs_reservation', array('reservation_id' => $reservation_id));
            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                if($data['row']->tenant_id > 0){
                    $tenant = $this->db->get_where('ms_tenant', array('tenant_id' => $data['row']->tenant_id));
                    $data['tenant'] = $tenant->row();
                }else{
                    $tenant = $this->db->get_where('tmp_tenant', array('reservation_id' => $reservation_id));
                    $data['tenant'] = $tenant->row();
                }

                if($data['row']->company_id > 0){
                    $company = $this->db->get_where('ms_company', array('company_id' => $data['row']->company_id));
                    $data['company'] = $company->row();
                }

                if($data['row']->agent_id > 0){
                    $agent = $this->db->get_where('ms_agent', array('agent_id' => $data['row']->agent_id));
                    $data['agent'] = $agent->row();
                }

                $unit_prices = array();

                //CALC
                $qry = $this->db->query('select distinct cs_reservation_detail.unit_id, ms_unit.unit_code, ms_unit.unittype_id, ms_unit_type.unittype_desc, cs_reservation_header.reservation_type, cs_reservation_header.agent_id
                                             from cs_reservation_detail
                                             join cs_reservation_header on cs_reservation_header.reservation_id = cs_reservation_detail.reservation_id
                                             join ms_unit on ms_unit.unit_id = cs_reservation_detail.unit_id
                                             join ms_unit_type on ms_unit_type.unittype_id = ms_unit.unittype_id
                                             where cs_reservation_detail.reservation_id = ' . $reservation_id );

                if($qry->num_rows() > 0){
                    $calc_array = array();
                    foreach($qry->result_array() as $unit){
                        //Get Discount
                        $discount_per_unit = 0;
                        $discount = $this->db->query('select ISNULL(discount,0) as discount from cs_reservation_unit
                                                  where reservation_id = ' . $reservation_id . ' and unit_id = ' . $unit['unit_id']);
                        if($discount->num_rows() > 0){
                            $discount_per_unit = $discount->row()->discount;
                        }

                        $reservation_type = $unit['reservation_type'];
                        $calc = $this->mdl_frontdesk->calculate_booking($unit['unittype_id'], dmy_from_db($data['row']->arrival_date), dmy_from_db($data['row']->departure_date), $reservation_type, $data['row']->is_rate_yearly, $data['row']->billing_type);

                        array_push($calc_array, array('unit_id'=> $unit['unit_id'],'unit_code'=>$unit['unit_code'],'unittype_desc' => $unit['unittype_desc'], 'calc_data' => $calc, 'discount' => $discount_per_unit));

                        //var_dump($calc);
                    }

                    if(count($calc_array) > 0){
                        //Set Duration
                        $iMonth = $calc_array[0]['calc_data']['monthly_count'];
                        $iDay = $calc_array[0]['calc_data']['daily_count'];

                        $duration = '';
                        if( $iMonth > 0 ){
                            $duration .= ($iMonth > 1) ? $iMonth . ' months ' : $iMonth . ' month';
                        }

                        if($iDay > 0){
                            if($iMonth > 0){
                                $duration .= ' and ';
                            }

                            $duration .= $iDay; //($iDay > 1) ? $iDay . ' days ' : $iDay . ' day';
                        }

                        $data['room_duration'] = $calc['period_caption']; // $duration;

                        if($data['row']->status != ORDER_STATUS::CHECKIN){
                            //Split Duration of Unit by months and days
                            foreach($calc_array as $arr){
                                $calc = $arr['calc_data'];
                                $tax = ($calc['total_amount'] - $arr['discount']) * $calc['tax_rate'];

                                array_push($unit_prices, array('billdetail_id' => 0,'unit_id'=> $arr['unit_id'],'unit_code'=>$arr['unit_code'],'unittype_desc' => $unit['unittype_desc'], 'duration' => $calc['period_caption'], 'is_monthly' => $calc['billing_base'] , 'year_interval' => $calc['yearly_count'], 'month_interval' => $calc['monthly_count'] , 'rate' => $calc['daily_rate'], 'local_amount' => $calc['total_amount'], 'bill_start' => dmy_from_db($data['row']->arrival_date), 'bill_end' => dmy_from_db($data['row']->departure_date), 'transtype_id' => $calc['transtype_id'], 'tax_amount' => $tax, 'discount' => $arr['discount']));
                            }
                        }else{
                            //LOOK FROM DB
                            $bill_detail = $this->db->query('select cs_bill_detail.*, ms_unit.unit_code, ms_unit_type.unittype_desc
                                             from cs_bill_detail
                                             left join ms_unit on ms_unit.unit_id = cs_bill_detail.unit_id
                                             left join ms_unit_type on ms_unit.unittype_id = ms_unit_type.unittype_id
                                             where cs_bill_detail.reservation_id = ' . $reservation_id .'
                                             order by cs_bill_detail.billdetail_id');
                            if($bill_detail->num_rows() > 0){
                                foreach($bill_detail->result_array() as $arr){
                                    array_push($unit_prices, array('billdetail_id' => $arr['billdetail_id'], 'unit_id'=> $arr['unit_id'],'unit_code'=>$arr['unit_code'],'unittype_desc' => $arr['unittype_desc'], 'duration' => $arr['date_interval'], 'is_monthly' =>$arr['is_monthly'] , 'rate' => $arr['rate'], 'local_amount' => $arr['amount'], 'bill_start' => dmy_from_db($data['row']->arrival_date), 'bill_end' => dmy_from_db($data['row']->departure_date), 'transtype_id' => $arr['transtype_id'], 'tax_amount' => $arr['tax'], 'discount' => $arr['disc_amount']));
                                }
                            }
                        }
                    }
                }

                //Get Payments
                $pay_qry = $this->db->query('select * from fxnCS_ReservationLedger(' . $data['reservation_id'] . ')
                                         order by order_date');

                $payments = array();
                if($pay_qry->num_rows() > 0){
                    foreach($pay_qry->result_array() as $pay){
                        array_push($payments, array('doc_date' => $pay['trx_date'],
                            'doc_no' => $pay['doc_no'],
                            'type' => 'Official Receipt',
                            'remark' => $pay['remark'],
                            'amount' => ($pay['debit'] - $pay['credit'])
                        ));
                    }
                }

                $pay_qry = $this->db->query('select * from ar_deposit_header where reservation_id = ' . $data['reservation_id'] . '
                                         order by deposit_date');

                if($pay_qry->num_rows() > 0){
                    foreach($pay_qry->result_array() as $pay){
                        array_push($payments, array('doc_date' => $pay['deposit_date'],
                            'doc_no' => $pay['deposit_no'],
                            'type' => 'Deposit',
                            'remark' => $pay['deposit_desc'],
                            'amount' => ($pay['deposit_paymentamount'] - $pay['deposit_bankcharges']) * -1
                        ));
                    }
                }

                if(count($payments) > 0)
                    $data['payments'] = $payments;

                $data['unit_rates'] = $unit_prices;

            }else{
                $valid = false;
            }
        }else{
            $valid = false;
        }

        if($valid){
            $data['back_url'] = base_url('frontdesk/reservation/reservation_manage/1.tpd');

            $this->load->view('layout/header', $data_header);
            $this->load->view('frontdesk/management/checkin_form', $data);
            $this->load->view('layout/footer');
        }else{
            tpd_404();
        }
	}

    public function delete_booking_receipt(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        $bookingReceiptId = isset($_POST['bookingreceipt_id']) ? $_POST['bookingreceipt_id'] : 0;

        if($bookingReceiptId > 0){
            $data['status'] = STATUS_CANCEL;
            $this->mdl_general->update('cs_booking_receipt', array('bookingreceipt_id' => $bookingReceiptId, $data));

            $result['type'] = '1';
            $result['message'] = 'Successfully delete record.';
        }

        echo json_encode($result);
    }

    public function rollback_checkin(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $reservation_id = $_POST['reservation_id'];
        $reason = $_POST['reason'];

        $valid = true;
        if($reservation_id > 0){
            $rsv = $this->db->get_where('view_cs_reservation', array('reservation_id' => $reservation_id));
            if($rsv->num_rows() > 0){
                $reservation = $rsv->row();
                $reservation_array = $rsv->row_array();

                //VALIDATE FIRST
                $qry = $this->db->query('SELECT * FROM fxnCS_ReservationLedger(' . $reservation_id .') WHERE type NOT IN(1,2) ');
                if($qry->num_rows() <= 0){
                    //BEGIN TRANSACTION
                    $this->db->trans_begin();

                    //DELETE Post Journal IV Reservation , If any
                    $qry = $this->db->query('SELECT doc_no FROM fxnCS_ReservationLedger(' . $reservation_id .') WHERE type = 1');
                    if($qry->num_rows() > 0){
                        foreach($qry->result_array() as $ledger){
                            $header = $this->db->get_where('gl_postjournal_header', array('journal_no' => $ledger['doc_no']));
                            if($header->num_rows() > 0){
                                foreach($header->result() as $head){
                                    $deleted = $this->db->query('DELETE FROM gl_postjournal_detail WHERE postheader_id = ' . $head->postheader_id);
                                    $deleted = $this->db->query('DELETE FROM gl_postjournal_header WHERE postheader_id = ' . $head->postheader_id);
                                }
                            }
                        }
                    }

                    //ROLLBACK hsk_status
                    $this->update_hsk_status($reservation_array,false,'',HSK_STATUS::IS);

                    //DELETE INVOICE CHECKIN
                    if($reservation->reservation_type == RES_TYPE::CORPORATE){
                        $qry = $this->db->query('SELECT ar_invoice_header.inv_no, ar_invoice_header.inv_id FROM ar_invoice_header
                                                    JOIN ar_invoice_detail ON ar_invoice_header.inv_id = ar_invoice_detail.inv_id
                                                    JOIN cs_corporate_bill ON cs_corporate_bill.corporatebill_id = ar_invoice_detail.bill_id
                                                    WHERE cs_corporate_bill.reservation_id = ' . $reservation_id);
                    }else {
                        $qry = $this->db->query('SELECT inv_no, inv_id FROM ar_invoice_header WHERE reservation_id = ' . $reservation_id); //. ' AND is_full_paid > 0');
                    }
                    if($qry->num_rows() > 0){
                        foreach($qry->result_array() as $inv){
                            $header = $this->db->get_where('gl_postjournal_header', array('journal_no' => $inv['inv_no']));
                            if($header->num_rows() > 0){
                                foreach($header->result() as $head){
                                    $deleted = $this->db->query('DELETE FROM gl_postjournal_detail WHERE postheader_id = ' . $head->postheader_id);
                                    $deleted = $this->db->query('DELETE FROM gl_postjournal_header WHERE postheader_id = ' . $head->postheader_id);
                                }
                            }

                            $deleted = $this->db->query('DELETE FROM ar_invoice_detail WHERE inv_id = ' . $inv['inv_id']);
                            $deleted = $this->db->query('DELETE FROM ar_invoice_header WHERE inv_id = ' . $inv['inv_id']);
                        }
                    }

                    //DELETE cs_corporate_bill
                    $deleted = $this->db->query('DELETE FROM cs_corporate_bill WHERE reservation_id = ' . $reservation_id);

                    //DELETE Allocation
                    $details = $this->db->get_where('cs_bill_detail', array('reservation_id' => $reservation_id));
                    if($details->num_rows() > 0){
                        foreach($details->result_array() as $detail){
                            $deleted = $this->db->query('DELETE FROM ar_receipt_allocation WHERE billdetail_id = ' . $detail['billdetail_id']);
                        }
                    }

                    //DELETE CS Bill
                    $deleted = $this->db->query('DELETE FROM cs_bill_detail WHERE reservation_id = ' . $reservation_id);
                    $deleted = $this->db->query('DELETE FROM cs_bill_header WHERE reservation_id = ' . $reservation_id);

                    //DELETE CS Corporate
                    $deleted = $this->db->query('DELETE FROM cs_corporate_bill WHERE reservation_id = ' . $reservation_id . ' AND ISNULL(is_billed,0) <= 0');

                    //DELETE Schedule Close
                    $deleted = $this->db->query('DELETE FROM cs_sales_close WHERE close_status <= 0 AND reservation_id = ' . $reservation_id);
                    //UPDATE Schedule Close Closed to -> Cancel
                    $updated = $this->db->query('UPDATE cs_sales_close SET status = ' . STATUS_CANCEL . ' WHERE reservation_id = ' . $reservation_id . ' AND close_status > 0');

                    //UPDATE to Status -> Reserved
                    $updated = $this->db->query('UPDATE cs_reservation_header SET status = ' . ORDER_STATUS::RESERVED . ' WHERE reservation_id = ' . $reservation_id);

                    $updated = $this->db->query('UPDATE cs_reservation_detail SET close_status = 0 WHERE reservation_id = ' . $reservation_id);

                    //INSERT LOG
                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_subject'] = ' Rollback Check In';
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $reservation_id;
                    $data_log['remark'] = $reason;
                    $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION;
                    $data_log['action_type'] = STATUS_DELETE;
                    $this->db->insert('app_log', $data_log);

                    if($updated <= 0){
                        $valid = false;
                    }

                    //FINALIZE TRANSACTION
                    if($valid){
                        if ($this->db->trans_status() === FALSE)
                        {
                            $this->db->trans_rollback();

                            $result['type'] = '0';
                            $result['message'] = 'Rollback can not be processed.';
                        }
                        else
                        {
                            $this->db->trans_commit();

                            $result['type'] = '1';
                            $result['message'] = 'Rollback successfully processed.';
                        }
                    }else{
                        $this->db->trans_rollback();

                        $result['type'] = '0';
                        $result['message'] = 'Rollback can not be processed.';
                    }
                }else{
                    $result['type'] = '0';
                    $result['message'] = 'Rollback is not permitted.';
                }
            }

        }

        echo json_encode($result);
    }

    public function submit_checkin(){
        $valid = true;

        if(isset($_POST)){
            $reservationId = $_POST['reservation_id'];

            $checkin_ready = true;

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            if($reservationId > 0){
                $qry = $this->db->get_where('view_cs_reservation', array('reservation_id' => $reservationId));
                $row = $qry->row_array();

                if($row['status'] == ORDER_STATUS::RESERVED){
                    if($valid){

                        //Create Main Bill(s) & POSTING
                        $valid = $this->insertMainBills($row);

                        //Update HSK to ISC
                        if($valid){
                            $valid = $this->update_hsk_status($row);
                        }
                        //Create Closing Schedule
                        if($valid){
                            $valid = $this->insertCloseSchedules($row);
                        }
                    }

                    if($valid) {
                        //Update Folio
                        //Set Online Valid -> Checked In
                        $data['status'] = ORDER_STATUS::CHECKIN;
                        $data['modified_by'] = my_sess('user_id');
                        $data['modified_date'] = $server_date;
                        $data['checkin_date'] = $server_date;
                        if ($row['is_frontdesk'] <= 0) {
                            $data['is_walkin'] = 0;
                        } else {
                            $data['is_walkin'] = isset($_POST['is_walkin']) ? $_POST['is_walkin'] : 0;
                        }

                        $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $reservationId), $data);
                    }

                    //Create Booking Receipt if any
                    if($valid){
                        //echo '<br>step 4 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Check In successfully recorded.');
                    }
                }else{
                    $checkin_ready = false;
                }

            }

            //COMMIT OR ROLLBACK
            if($checkin_ready){
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
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please check unit type trx_type!');
                }
            }else{
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'warning');
                    $this->session->set_flashdata('flash_message', 'Reservation already checked in.');
                }
            }

            //FINALIZE
            if(!$valid){
               redirect(base_url('frontdesk/management/checkin_form/' . $reservationId . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('frontdesk/management/guest_manage/1.tpd'),true);
                }
            }
        }
    }

    private function insertMainBills($rsvt = array()){
        $billDetails = array();
        $valid = true;

        if(count($rsvt) > 0 && isset($_POST)){
            $reservationId = $rsvt['reservation_id'];

            try{
                $billdetail_id = isset($_POST['billdetail_id']) ? $_POST['billdetail_id'] : array();
                $unit_id = isset($_POST['unit_id']) ? $_POST['unit_id'] : array();
                $unit_duration = isset($_POST['unit_duration']) ? $_POST['unit_duration'] : array();
                $is_monthly = isset($_POST['is_monthly']) ? $_POST['is_monthly'] : array();
                $local_amount = isset($_POST['local_amount']) ? $_POST['local_amount'] : array();
                $transtype_id = isset($_POST['transtype_id']) ? $_POST['transtype_id'] : array();
                $bill_start_date = $_POST['bill_start_date'];
                $bill_end_date = $_POST['bill_end_date'];
                $tax_amount = isset($_POST['tax_amount']) ? $_POST['tax_amount'] : array();
                $disc_amount = isset($_POST['discount_amount']) ? $_POST['discount_amount'] : array();
                $year_interval = isset($_POST['year_interval']) ? $_POST['year_interval'] : array();
                $month_interval = isset($_POST['month_interval']) ? $_POST['month_interval'] : array();

                if(count($unit_id) > 0){
                    //echo '<br>Count detail ' . count($unit_id);
                    $totalAmount = 0;
                    for ($i = 0; $i < count($unit_id); $i++) {
                        if($valid){
                            if(isset($unit_duration[$i]) && isset($is_monthly[$i]) && isset($local_amount[$i])){
                                $detail = array();
                                if(isset($billdetail_id) && $billdetail_id[$i] > 0){
                                    //Edit
                                    $detail['billdetail_id'] = $billdetail_id[$i];
                                }else{
                                    //NEW
                                    //$detail['billdetail_id'] = 0;
                                    $detail['is_billed'] = 0;
                                    $detail['currencytype_id'] = 1;
                                    $detail['status'] = STATUS_NEW;
                                }

                                $detail['bill_id'] = 0;
                                $detail['reservation_id'] = $reservationId;
                                $detail['unit_id'] = $unit_id[$i];
                                $detail['item_id'] = 0;
                                $detail['masteritem_id'] = 0;
                                $detail['transtype_id'] = $transtype_id[$i];
                                $detail['is_monthly'] = $is_monthly[$i];
                                $detail['date_start'] = dmy_to_ymd($bill_start_date[$i]);
                                $detail['date_end'] = dmy_to_ymd($bill_end_date[$i]);
                                $detail['year_interval'] = $year_interval[$i]; //$unit_duration[$i];
                                $detail['month_interval'] = $month_interval[$i]; //$unit_duration[$i];
                                $detail['date_interval'] = 0; //$unit_duration[$i];
                                $totalMonth = (($detail['year_interval'] * 12) + $detail['month_interval']);
                                $baseRate = 0;
                                if($totalMonth > 0){
                                    $baseRate = round($local_amount[$i] / $totalMonth ,0);
                                }
                                $detail['rate'] = $baseRate;
                                $detail['amount'] = $local_amount[$i];
                                $detail['tax'] = round($tax_amount[$i],0);

                                $detail['disc_percent'] = 0;
                                $detail['disc_amount'] = 0;
                                if($local_amount[$i] > 0){
                                    $detail['disc_percent'] = ($disc_amount[$i] / $local_amount[$i]) * 100;
                                    $detail['disc_amount'] = $disc_amount[$i];
                                }
                                $detail['modified_date'] = date('Y-m-d H:i:s');

                                array_push($billDetails, $detail);

                                $totalAmount += ($detail['amount'] - $detail['disc_amount'] + $detail['tax']);
                            }
                        }
                    }

                    if($valid){
                        if(count($billDetails) > 0){
                            $billDate = date('Y-m-d');
                            $inv_no = $this->mdl_general->generate_code(Feature::FEATURE_AR_BILLING, $billDate);

                            if(trim($inv_no) != ''){
                                $billHeader['reservation_id'] = $reservationId;
                                $billHeader['bill_date'] = $billDate;
                                $billHeader['journal_no'] = $inv_no; //$rsvt['reservation_code'];

                                //SET BILLED TO
                                if($rsvt['reservation_type'] == RES_TYPE::CORPORATE){
                                    $billHeader['company_id'] = $rsvt['company_id'];
                                    $billHeader['tenant_id'] = 0;
                                }else{
                                    $billHeader['tenant_id'] = $rsvt['tenant_id'];
                                    $billHeader['company_id'] = 0;
                                }

                                $billHeader['amount'] = $totalAmount;
                                $billHeader['created_by'] = my_sess('user_id');
                                $billHeader['created_date'] = date('Y-m-d H:i:s');
                                $billHeader['status'] = STATUS_NEW;
                                $billHeader['is_other_charge'] = 0;
                                $billHeader['is_hsk'] = 0;

                                //echo '<br>inv : ' . $inv_no;
                                $this->db->insert('cs_bill_header', $billHeader);
                                $billId = $this->db->insert_id();
                                //echo '<br>valid header : ' . $billId;

                                if($billId <= 0){
                                    $valid = false;
                                }else{
                                    $bills = array();
                                    foreach($billDetails as $detail){
                                        if($valid){
                                            $detail['bill_id'] = $billId;
                                            if(isset($detail['billdetail_id']) && $detail['billdetail_id'] > 0){
                                                $this->mdl_general->update('cs_bill_detail', array('billdetail_id' => $detail['billdetail_id']), $detail);
                                            }else{
                                                $detail['is_billed'] = 99;

                                                $this->db->insert('cs_bill_detail', $detail);
                                                $insertID = $this->db->insert_id();

                                                $detail['billdetail_id'] = $insertID;

                                                if($insertID <= 0){
                                                    $valid = false;
                                                }
                                            }
                                            $total = ($detail['amount'] - $detail['disc_amount'] + $detail['tax']);
                                            array_push($bills, array('unit_id' => $detail['unit_id'],
                                                'billdetail_id'=>$detail['billdetail_id'],'total_bill_amount' => $total,
                                                'month_interval' => $detail['month_interval'],'year_interval' => $detail['year_interval'],
                                                'amount'=>$detail['amount'],'disc_amount'=>$detail['disc_amount'],'tax'=> $detail['tax'])
                                            );
                                        }
                                    }

                                    //POSTING FOR CHECKIN
                                    if($rsvt['status'] == ORDER_STATUS::RESERVED){
                                        //Generate Pending Bills & Posting if Full Paid
                                        if($rsvt['reservation_type'] != RES_TYPE::HOUSE_USE) {
                                            //INSERT MONTHLY BILLS
                                            if ($valid) {
                                                $valid = $this->insertARPendingBills($rsvt, $bills);
                                            }

                                            //ALLOCATE RECEIPT TO INVOICE (IF AVAILABLE)
                                            if($valid){
                                                //$valid = $this->allocateCheckInReceipt($rsvt, $bills);
                                            }
                                            //echo '<br>valid 2 : ' . ($valid ? 'true' :'false');
                                        }

                                        //UPDATE HEADER
                                        unset($billHeader);

                                        if($valid){
                                            $this->mdl_general->update('cs_bill_header', array('bill_id' => $billId), array('status' =>STATUS_POSTED));
                                        }
                                        //echo '<br>valid 4 : ' . ($valid ? 'true' :'false');
                                    }
                                }
                            }else{
                                $valid = false;
                                echo '<br>[insertMainBills][JournalNo can not be generated] ' . $inv_no;
                            }
                        }
                    }
                }
            }catch(Exception $e){
                $valid = false;
                echo '<br>[insertMainBills][Error] ' . $e;
            }
        }

        return $valid;
    }

    private function update_hsk_status($reservation = array(), $is_checkout = false, $checkout_ymd = '', $hsk_status = ''){
        $valid = true;

        if(isset($reservation)){
            $units = $this->db->get_where('cs_reservation_unit', array('reservation_id' => $reservation['reservation_id']));
            if($units->num_rows() > 0){
                try {
                    foreach ($units->result() as $unit) {
                        if($valid){
                            $valid = $this->update_hsk_by_unit($unit->unit_id, $is_checkout, $checkout_ymd, $reservation['reservation_id'], $hsk_status);
                        }else{
                            break;
                        }
                    }
                }catch(Exception $e){
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    private function update_hsk_by_unit($unit_id = 0, $is_checkout = false, $checkout_ymd = '', $reservation_id = 0, $hsk_status = '' , $remark = ''){
        $valid = true;

        if($unit_id > 0){
            $data = array();

            $data['unit_id'] = $unit_id;
            $data['created_by'] = my_sess('user_id');
            $data['created_date'] = ($is_checkout ? $checkout_ymd : date('Y-m-d H:i:s'));

            if($is_checkout && $checkout_ymd != ''){
                $exist_ea = $this->db->query("SELECT reservation_detail_id FROM cs_reservation_detail
                              WHERE unit_id = " . $unit_id . " AND CONVERT(date,checkin_date) = '" . $checkout_ymd . "' AND status = " . STATUS_NEW . " AND close_status <= 0 AND reservation_id <> " . $reservation_id);
                if($exist_ea->num_rows() > 0){
                    $data['hsk_status'] = HSK_STATUS::VD_EA;
                }else{
                    $data['hsk_status'] = HSK_STATUS::VD;
                }
                $data['remark'] = 'Check Out' . ($remark != '' ? ' (' . $remark . ')' : '');
            }else{
                if($hsk_status != ''){
                    $data['hsk_status'] = $hsk_status;
                }else{
                    $data['hsk_status'] = HSK_STATUS::ISC;
                }

                $data['remark'] = 'Check In' . ($remark != '' ? ' (' . $remark . ')' : '');
            }

            $this->mdl_general->update('ms_unit', array('unit_id' => $unit_id), array('hsk_status' => $data['hsk_status']));

            $this->db->insert('log_hsk', $data);
            $insertedID = $this->db->insert_id();

            if($insertedID <= 0){
                $valid = false;
            }
        }

        return $valid;
    }

    private function insertARPendingBills($reservation = array(), $bill_detail = array()){
        $valid = true;

        if(isset($reservation) && isset($bill_detail)){
            $this->load->model('finance/mdl_finance');

            $reservationID =  $reservation['reservation_id'];
            $tenantID = 0;
            $companyID = 0;
            if($reservation['reservation_type'] != RES_TYPE::CORPORATE ){
                $tenantID = $reservation['tenant_id'];
            }else{
                $companyID = $reservation['company_id'];
            }
            //$reservationType = $reservation['reservation_type'];

            //Temp AR Bills
            $bills = array();

            $joins = array('ms_unit' => 'ms_unit.unit_id = cs_reservation_unit.unit_id',
                           'ms_unit_type' => 'ms_unit_type.unittype_id = ms_unit.unittype_id',
                           'ms_transtype' => 'ms_transtype.transtype_id = ms_unit_type.transtype_id',
                           'gl_coa' => 'gl_coa.coa_id = ms_transtype.coa_id');
            $units = $this->mdl_finance->getJoin('cs_reservation_unit.*, ms_unit_type.transtype_id, ms_transtype.transtype_name, ms_transtype.transtype_desc, gl_coa.coa_id','cs_reservation_unit', $joins, array('reservation_id' => $reservationID));
            if ($units->num_rows() > 0) {
                foreach ($units->result() as $unit) {
                    $startDate = DateTime::createFromFormat('Y-m-d', $reservation['arrival_date']);
                    $endDate = DateTime::createFromFormat('Y-m-d', $reservation['departure_date']);
                    $totalMonths = num_of_months($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

                    $day = date('d', strtotime(ymd_from_db($reservation['arrival_date'])));

                    //echo 'insertARPendingBills [total_month] : ' . $totalMonths;

                    for ($i = 0; $i < $totalMonths; $i++) {
                        $res = $this->db->query("SELECT out_date FROM fxnCheckout_Date('" . $startDate->format('Y-m-d') . "'," . BILLING_BASE::MONTHLY . "," . 1 . ")");
                        if ($res->num_rows() > 0) {
                            $closeDate = DateTime::createFromFormat('Y-m-d', ymd_from_db($res->row()->out_date));
                            //echo '<br>[out_Date] ' . $closeDate->format('d-m-Y');
                            $month = $closeDate->format('m');
                            $year = $closeDate->format('Y');
                            $maxdays = days_in_month($month, $year);
                            if($day >= 30){
                                if($day <= $maxdays){
                                    //echo 'A ' . $closeDate->format('Y-m'). '-'. ($day-1);
                                    $closeDate = DateTime::createFromFormat('Y-m-d', $closeDate->format('Y-m') . '-' . ($day-1));
                                }else{
                                    //echo 'B ' . $closeDate->format('Y-m'). '-'. $maxdays;
                                    $closeDate = DateTime::createFromFormat('Y-m-d', $closeDate->format('Y-m') . '-' . ($maxdays-1));
                                }
                            }

                            if($i == ($totalMonths - 1)){
                                if ($closeDate < $endDate) {
                                    $closeDate = $endDate;
                                }
                            }

                            //echo '<br>[closeDate] ' . $closeDate->format('d-m-Y') . ' <= endDate ' . $endDate->format('d-m-Y');

                            if ($closeDate <= $endDate) {
                                $bill = array();
                                $bill['reservation_id'] = $reservationID;
                                $bill['company_id'] = $companyID;
                                $bill['tenant_id'] = $tenantID;
                                $bill['billdetail_id'] = 0;
                                $bill['unit_id'] = $unit->unit_id;
                                $bill['transtype_id'] = $unit->transtype_id;
                                $bill['bill_startdate'] = $startDate->format('Y-m-d');
                                $bill['bill_enddate'] = $closeDate->format('Y-m-d');

                                $amountPerMonth = 0;
                                $discountPerMonth = 0;
                                $taxPerMonth = 0;
                                foreach($bill_detail as $det){
                                    if($det['unit_id'] == $bill['unit_id']){
                                        $bill['billdetail_id'] = $det['billdetail_id'];

                                        $nMonth = ($det['month_interval'] + ($det['year_interval'] * 12));
                                        $amountPerMonth = round($det['amount'] / $nMonth,0);
                                        $discountPerMonth = ($det['disc_amount'] > 0 ? round($det['disc_amount'] / $nMonth,0) : 0);

                                        $tax = tax_vat();
                                        if($tax['taxtype_percent'] > 0){
                                            $taxPerMonth = round(($amountPerMonth - $discountPerMonth) * ($tax['taxtype_percent']/100),0);
                                        }
                                    }
                                }

                                $bill['amount'] = $amountPerMonth;
                                $bill['discount'] = $discountPerMonth;
                                $bill['tax'] = $taxPerMonth;
                                $bill['total_amount'] = round($amountPerMonth - $discountPerMonth + $taxPerMonth,0);

                                $bill['is_billed'] = 0;
                                $bill['is_othercharge'] = 0;
                                $bill['status'] = STATUS_NEW;
                                $bill['month'] = ($i+1);

                                $this->db->insert('cs_corporate_bill', $bill);
                                $insertID = $this->db->insert_id();

                                if ($insertID <= 0) {
                                    $valid = false;
                                } else {
                                    $month = $closeDate->format('m');
                                    $year = $closeDate->format('Y');

                                    $maxdays = days_in_month($month, $year);

                                    if($day <= $maxdays){
                                        $startDate = DateTime::createFromFormat('Y-m-d', $closeDate->format('Y-m') . '-' . $day);
                                    }else{
                                        $startDate = DateTime::createFromFormat('Y-m-d', $closeDate->format('Y-m') . '-' . $maxdays);
                                    }
                                    //$startDate = $closeDate->add(new DateInterval('P1D'));

                                    //Insert into temp bills
                                    array_push($bills, array('corporatebill_id' => $insertID, 'transtype_id' => $bill['transtype_id'], 'transtype_desc' => $unit->transtype_desc,'bill_startdate' => $bill['bill_startdate'], 'bill_enddate' => $bill['bill_enddate'],'coa_id' => $unit->coa_id,'amount' => $bill['amount'], 'discount' => $bill['discount'], 'tax' => $bill['tax']));
                                }
                            } else {
                                break;
                            }
                        }
                    }
                }

                //Only for full payment, Create Invoice
                if($reservation['billing_type'] == BILLING_TYPE::FULL_PAID && $valid){
                    //Create Full Room Invoice at Check In
                    $valid = $this->createNPostFullPaidInvoice($reservation, $bills);
                }else{
                    //Create Monthly Invoice at Check In
                    $valid = $this->createNPostFirstInvoice($reservation, $bills);
                }
            }
        }

        return $valid;
    }

    private function createNPostFullPaidInvoice($reservation = array(), $bills = array()){
        $valid = true;

        if(isset($reservation) && isset($bills)) {
            $reservationID = $reservation['reservation_id'];
            $reservationType = $reservation['reservation_type'];
            if ($reservation['billing_type'] == BILLING_TYPE::FULL_PAID && $reservationType != RES_TYPE::CORPORATE){
                //Header
                $current_date = new DateTime('');

                $inv_date = $current_date->format('Y-m-d');
                $inv_due_date = $current_date->modify('+19 day');
                $inv_due_date = $inv_due_date->format('Y-m-d');

                $header['inv_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_INVOICE, $inv_date);
                if ($header['inv_no'] != '') {
                    $header['inv_date'] = $inv_date;
                    $header['inv_due_date'] = $inv_due_date;
                    $header['is_full_paid'] = 1;
                    if ($reservation['reservation_type'] == RES_TYPE::CORPORATE) {
                        $header['company_id'] = $reservation['company_id'];
                        $header['reservation_id'] = 0;
                    } else {
                        $header['reservation_id'] = $reservationID;
                        $header['company_id'] = 0;
                    }

                    //Detail
                    $totalAmount = 0;
                    $totalTax = 0;

                    $details = array();
                    if (count($bills) > 0) {
                        foreach ($bills as $bill) {
                            $remark = $bill['transtype_desc'] . ' (' . date('d/m/Y', strtotime(ymd_to_dmy($bill['bill_startdate']))) . ' - ' . date('d/m/Y', strtotime(ymd_to_dmy($bill['bill_enddate']))) . ')';

                            if ($bill['coa_id'] > 0) {
                                $totalAmount += round($bill['amount'] - $bill['discount'], 0);
                                $totalTax += round($bill['tax'], 0);

                                array_push($details, array('bill_id' => $bill['corporatebill_id'],
                                        'description' => $remark,
                                        'amount' => round($bill['amount'] - $bill['discount'], 0), 'tax' => round($bill['tax'], 0),
                                        'discount' => $bill['discount'],
                                        'transtype_id' => $bill['transtype_id'],
                                        'paid_amount' => 0, 'paid_tax' => 0,
                                        'coa_id' => $bill['coa_id'],
                                        'status' => STATUS_NEW)
                                );
                            } else {
                                $valid = false;
                                break;
                            }
                        }
                    }

                    $header['total_amount'] = round($totalAmount, 0);
                    $header['total_tax'] = round($totalTax, 0);
                    $header['total_grand'] = ($header['total_amount'] + $header['total_tax']);
                    $header['created_by'] = my_sess('user_id');
                    $header['created_date'] = date('Y-m-d H:i:s');
                    $header['status'] = STATUS_POSTED; //STATUS_NEW;

                    if (count($details) > 0 && $valid) {
                        $this->db->insert('ar_invoice_header', $header);
                        $inv_id = $this->db->insert_id();

                        if ($inv_id > 0) {
                            foreach ($details as $detail) {
                                $detail['inv_id'] = $inv_id;

                                $this->db->insert('ar_invoice_detail', $detail);
                                $detail_id = $this->db->insert_id();
                            }
                        } else {
                            $valid = false;
                        }
                    } else {
                        $valid = false;
                    }

                    if($valid){
                        //POSTING AR to Sales
                        if($reservationType == RES_TYPE::CORPORATE) {
                            $fnAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                        }else{
                            $fnAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                        }

                        $taxVAT = FNSpec::get(FNSpec::TAX_VAT);

                        if($fnAR['coa_id'] > 0){
                            //----Main ----
                            //AR Personal / Corporate    8.706
                            //  - Sales Room                    7.000
                            //  - Sales Other                   1.000
                            //  - VAT                             700

                            $postDetail = array();

                            $totalDebit = 0;
                            $totalCredit = 0;

                            $reverseAmount = 0;
                            foreach($details as $det){
                                if($valid){
                                    if($det['coa_id'] > 0) {
                                        $desc = (trim($reservation['reservation_code']) != '' ? $reservation['reservation_code'] . ' - ' : '') . $det['description'];

                                        //Amount
                                        $detAmount = array();
                                        $detAmount['coa_id'] = $det['coa_id'];
                                        $detAmount['dept_id'] = 0;
                                        $detAmount['journal_note'] = $desc;
                                        $detAmount['journal_debit'] = 0;
                                        $detAmount['journal_credit'] = $det['amount'];
                                        $detAmount['reference_id'] = 0;
                                        $detAmount['transtype_id'] = 0;

                                        array_push($postDetail, $detAmount);

                                        //Tax
                                        if ($det['tax'] > 0) {
                                            if ($taxVAT['coa_id'] > 0) {
                                                $detTax = array();
                                                $detTax['coa_id'] = $taxVAT['coa_id'];
                                                $detTax['dept_id'] = 0;
                                                $detTax['journal_note'] = 'VAT ' . $desc;
                                                $detTax['journal_debit'] = 0;
                                                $detTax['journal_credit'] = $det['tax'];
                                                $detTax['reference_id'] = 0;
                                                $detTax['transtype_id'] = 0;

                                                array_push($postDetail, $detTax);
                                            } else {
                                                $valid = false;
                                                break;
                                            }
                                        }

                                        $totalCredit += ($det['amount'] + $det['tax']);

                                    }else{
                                        break;
                                    }
                                }else{
                                    break;
                                }
                            }

                            if($valid && $totalCredit > 0 && $header['total_grand'] == $totalCredit){
                                //AR
                                $detAR = array();
                                $detAR['coa_id'] = $fnAR['coa_id'];
                                $detAR['dept_id'] = 0;
                                $detAR['journal_note'] = $header['inv_no'] . ' - ' . $reservation['reservation_code'] . ' / ' . ($reservation['reservation_type'] == RES_TYPE::CORPORATE ? $reservation['company_name'] :  $reservation['tenant_fullname']);
                                $detAR['journal_debit'] = $totalCredit;
                                $detAR['journal_credit'] = 0;
                                $detAR['reference_id'] = 0;
                                $detAR['transtype_id'] = 0;

                                array_push($postDetail, $detAR);

                                $totalDebit = $totalCredit;
                            }else{
                                $valid = false;
                            }

                            if($valid && $totalDebit == $totalCredit){
                                $postHeader = array();
                                $postHeader['journal_no'] = $header['inv_no'];
                                $postHeader['journal_date'] = $header['inv_date'];
                                $postHeader['journal_remarks'] = $reservation['reservation_code'] . ' / ' . ($reservation['reservation_type'] == RES_TYPE::CORPORATE ? $reservation['company_name'] :  $reservation['tenant_fullname']);
                                $postHeader['modul'] = GLMOD::GL_MOD_AR;
                                $postHeader['journal_amount'] = $totalDebit;
                                $postHeader['reference'] = '';

                                $valid = $this->mdl_finance->postJournal($postHeader,$postDetail);

                                //echo 'POSTING ' . $totalDebit . ' -> ' . $valid;
                            }

                            if($valid){
                                //UPDATE cs_corporate_bill
                                $this->mdl_general->update('cs_corporate_bill', array('reservation_id' => $reservationID), array('is_billed' => 1));

                                //$data['status']= STATUS_POSTED;
                                //$this->mdl_general->update('ar_invoice_header', array('inv_id' => $inv_id), $data);
                            }
                        }else{
                            $valid = false;
                        }
                    }
                }
            }
        }else {
            $valid = false;
        }

        return $valid;
    }

    private function createNPostFirstInvoice($reservation = array(), $bills = array()){
        $valid = true;

        if(isset($reservation) && isset($bills)) {
            $reservationID = $reservation['reservation_id'];
            $reservationType = $reservation['reservation_type'];
            if ($reservation['billing_type'] == BILLING_TYPE::MONTHLY && $reservationType != RES_TYPE::CORPORATE) {
                //Header
                $current_date = new DateTime('');

                $inv_date = $current_date->format('Y-m-d');
                $inv_due_date = $current_date->modify('+19 day');
                $inv_due_date = $inv_due_date->format('Y-m-d');

                $header['inv_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_INVOICE, $inv_date);
                if ($header['inv_no'] != '') {
                    $header['inv_date'] = $inv_date;
                    $header['inv_due_date'] = $inv_due_date;
                    $header['is_full_paid'] = 0;
                    if ($reservation['reservation_type'] == RES_TYPE::CORPORATE) {
                        $header['company_id'] = $reservation['company_id'];
                        $header['reservation_id'] = 0;
                    } else {
                        $header['reservation_id'] = $reservationID;
                        $header['company_id'] = 0;
                    }

                    //Detail
                    $totalAmount = 0;
                    $totalTax = 0;

                    $details = array();
                    if (count($bills) > 0) {
                        $bill = $bills[0];

                        $remark = $bill['transtype_desc'] . ' (' . date('d/m/Y', strtotime(ymd_to_dmy($bill['bill_startdate']))) . ' - ' . date('d/m/Y', strtotime(ymd_to_dmy($bill['bill_enddate']))) . ')';

                        if ($bill['coa_id'] > 0) {
                            $totalAmount += round($bill['amount'] - $bill['discount'], 0);
                            $totalTax += round($bill['tax'], 0);

                            array_push($details, array('bill_id' => $bill['corporatebill_id'],
                                    'description' => $remark,
                                    'amount' => round($bill['amount'] - $bill['discount'], 0), 'tax' => round($bill['tax'], 0),
                                    'discount' => $bill['discount'],
                                    'transtype_id' => $bill['transtype_id'],
                                    'paid_amount' => 0, 'paid_tax' => 0,
                                    'coa_id' => $bill['coa_id'],
                                    'status' => STATUS_NEW)
                            );
                        } else {
                            $valid = false;
                        }
                    }

                    $header['total_amount'] = round($totalAmount, 0);
                    $header['total_tax'] = round($totalTax, 0);
                    $header['total_grand'] = ($header['total_amount'] + $header['total_tax']);
                    $header['created_by'] = my_sess('user_id');
                    $header['created_date'] = date('Y-m-d H:i:s');
                    $header['status'] = STATUS_POSTED; //STATUS_NEW;

                    if (count($details) > 0 && $valid) {
                        $this->db->insert('ar_invoice_header', $header);
                        $inv_id = $this->db->insert_id();

                        if ($inv_id > 0) {
                            foreach ($details as $detail) {
                                $detail['inv_id'] = $inv_id;

                                $this->db->insert('ar_invoice_detail', $detail);
                                $detail_id = $this->db->insert_id();
                            }
                        } else {
                            $valid = false;
                        }
                    } else {
                        $valid = false;
                    }

                    if($valid){
                        //POSTING AR to Sales
                        if($reservationType == RES_TYPE::CORPORATE) {
                            $fnAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                        }else{
                            $fnAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                        }

                        $taxVAT = FNSpec::get(FNSpec::TAX_VAT);

                        if($fnAR['coa_id'] > 0){
                            //----Main ----
                            //AR Personal / Corporate    8.706
                            //  - Sales Room                    7.000
                            //  - Sales Other                   1.000
                            //  - VAT                             700

                            $postDetail = array();

                            $totalDebit = 0;
                            $totalCredit = 0;

                            $reverseAmount = 0;
                            foreach($details as $det){
                                if($valid){
                                    if($det['coa_id'] > 0) {
                                        $desc = (trim($reservation['reservation_code']) != '' ? $reservation['reservation_code'] . ' - ' : '') . $det['description'];

                                        //Amount
                                        $detAmount = array();
                                        $detAmount['coa_id'] = $det['coa_id'];
                                        $detAmount['dept_id'] = 0;
                                        $detAmount['journal_note'] = $desc;
                                        $detAmount['journal_debit'] = 0;
                                        $detAmount['journal_credit'] = $det['amount'];
                                        $detAmount['reference_id'] = 0;
                                        $detAmount['transtype_id'] = 0;

                                        array_push($postDetail, $detAmount);

                                        //Tax
                                        if ($det['tax'] > 0) {
                                            if ($taxVAT['coa_id'] > 0) {
                                                $detTax = array();
                                                $detTax['coa_id'] = $taxVAT['coa_id'];
                                                $detTax['dept_id'] = 0;
                                                $detTax['journal_note'] = 'VAT ' . $desc;
                                                $detTax['journal_debit'] = 0;
                                                $detTax['journal_credit'] = $det['tax'];
                                                $detTax['reference_id'] = 0;
                                                $detTax['transtype_id'] = 0;

                                                array_push($postDetail, $detTax);
                                            } else {
                                                $valid = false;
                                                break;
                                            }
                                        }

                                        $totalCredit += ($det['amount'] + $det['tax']);

                                    }else{
                                        break;
                                    }
                                }else{
                                    break;
                                }
                            }

                            if($valid && $totalCredit > 0 && $header['total_grand'] == $totalCredit){
                                //AR
                                $detAR = array();
                                $detAR['coa_id'] = $fnAR['coa_id'];
                                $detAR['dept_id'] = 0;
                                $detAR['journal_note'] = $header['inv_no'] . ' - ' . $reservation['reservation_code'] . ' / ' . ($reservation['reservation_type'] == RES_TYPE::CORPORATE ? $reservation['company_name'] :  $reservation['tenant_fullname']);
                                $detAR['journal_debit'] = $totalCredit;
                                $detAR['journal_credit'] = 0;
                                $detAR['reference_id'] = 0;
                                $detAR['transtype_id'] = 0;

                                array_push($postDetail, $detAR);

                                $totalDebit = $totalCredit;
                            }else{
                                $valid = false;
                            }

                            if($valid && $totalDebit == $totalCredit){
                                $postHeader = array();
                                $postHeader['journal_no'] = $header['inv_no'];
                                $postHeader['journal_date'] = $header['inv_date'];
                                $postHeader['journal_remarks'] = $reservation['reservation_code'] . ' / ' . ($reservation['reservation_type'] == RES_TYPE::CORPORATE ? $reservation['company_name'] :  $reservation['tenant_fullname']);
                                $postHeader['modul'] = GLMOD::GL_MOD_AR;
                                $postHeader['journal_amount'] = $totalDebit;
                                $postHeader['reference'] = '';

                                $valid = $this->mdl_finance->postJournal($postHeader,$postDetail);

                                //echo 'POSTING ' . $totalDebit . ' -> ' . $valid;
                            }

                            if($valid){
                                //UPDATE cs_corporate_bill
                                $this->mdl_general->update('cs_corporate_bill', array('reservation_id' => $reservationID,'corporatebill_id' => $details[0]['bill_id']), array('is_billed' => 1));

                                //$data['status']= STATUS_POSTED;
                                //$this->mdl_general->update('ar_invoice_header', array('inv_id' => $inv_id), $data);
                            }
                        }else{
                            $valid = false;
                        }
                    }
                }
            }
        }else {
            $valid = false;
        }

        return $valid;
    }

    private function allocateCheckInReceipt($rsvt = array(), $billDetails = array()){
        $valid = true;

        if(count($rsvt) > 0 && count($billDetails) > 0){
            $reservationId = $rsvt['reservation_id'];

            try{
                $ofr = $this->db->query('SELECT * FROM ar_receipt
                                         WHERE reservation_id = ' . $reservationId . ' AND status IN(' . STATUS_POSTED . ',' . STATUS_CLOSED . ') AND is_invoice <= 0');

                if($ofr->num_rows() > 0){
                    foreach($ofr->result_array() as $rv){
                        $alloc = array();

                        $current_date = date('Y-m-d H:i:s');
                        $receipt_date = ymd_from_db($rv['receipt_date']);
                        $availableAmount = ($rv['receipt_bankcharges'] + $rv['receipt_paymentamount']);

                        $invoices = $this->db->get_where("fxnARInvoiceHeaderByStatus(" . STATUS_POSTED . ")", array('reservation_id' => $reservationId));
                        if($invoices->num_rows() > 0){
                            foreach($invoices->result() as $inv){
                                if($availableAmount >= $inv->unpaid_grand && $valid){
                                    //Allocate to CS Receipt Allocation
                                    $unpaids = $this->db->get_where('view_ar_unpaid_invoice', array('inv_id'=> $inv->inv_id));
                                    if($unpaids->num_rows() > 0){
                                        foreach($unpaids->result_array() as $bill){
                                            //AMOUNT
                                            if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                                                $detail = array();
                                                $detail['receipt_id'] = $rv['receipt_id'];
                                                $detail['invdetail_id'] = $bill['invdetail_id'];
                                                $detail['bookingreceipt_id'] = 0;
                                                $detail['billdetail_id'] = 0;
                                                $detail['allocation_date'] = $rv['receipt_date'];
                                                $detail['is_debitnote'] = 0;
                                                $detail['allocationheader_id'] = 0;
                                                $detail['created_by'] = my_sess('user_id');
                                                $detail['created_date'] = $current_date;
                                                $detail['status'] = STATUS_CLOSED;
                                                $detail['is_tax'] = 0;

                                                if($bill['pending_amount'] <= $availableAmount){
                                                    $detail['allocation_amount'] = $bill['pending_amount'];
                                                }else{
                                                    $detail['allocation_amount'] = $availableAmount;
                                                }

                                                if($detail['allocation_amount'] > 0){
                                                    $this->db->insert('ar_receipt_allocation', $detail);
                                                    if($this->db->insert_id() <= 0){
                                                        $valid = false;
                                                        break;
                                                    }

                                                    $availableAmount -= $detail['allocation_amount'];
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
                                                $detail['receipt_id'] = $rv['receipt_id'];
                                                $detail['invdetail_id'] = $bill['invdetail_id'];
                                                $detail['bookingreceipt_id'] = 0;
                                                $detail['billdetail_id'] = 0;
                                                $detail['allocation_date'] = $rv['receipt_date'];
                                                $detail['is_debitnote'] = 0;
                                                $detail['allocationheader_id'] = 0;
                                                $detail['created_by'] = my_sess('user_id');
                                                $detail['created_date'] = $current_date;
                                                $detail['status'] = STATUS_CLOSED;
                                                $detail['is_tax'] = 1;

                                                if($bill['pending_tax'] <= $availableAmount){
                                                    $detail['allocation_amount'] = $bill['pending_tax'];
                                                }else{
                                                    $detail['allocation_amount'] = $availableAmount;
                                                }

                                                if($detail['allocation_amount'] > 0){
                                                    $this->db->insert('ar_receipt_allocation', $detail);
                                                    if($this->db->insert_id() <= 0){
                                                        $valid = false;
                                                        break;
                                                    }

                                                    $availableAmount -= $detail['allocation_amount'];
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
                                            $this->mdl_general->update('ar_invoice_header',array('inv_id' => $inv->inv_id), array('status' => STATUS_CLOSED));
                                                //$this->mdl_general->update('ar_receipt_detail',array('inv_id' => $inv_id), array('status' => STATUS_POSTED));
                                            //}
                                            //---------------------------------------------------
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }catch(Exception $e){
                $valid = false;
                echo '<br>[allocateCheckInReceipt][Error] ' . $e;
            }
        }

        return $valid;
    }

    private function generateWifiVoucher($reservation = array()){
        $data = array();

        if(ALLOW_WIFI_GEN) {
            if (isset($reservation)) {
                try {
                    //create wifi user
                    $digitAlphabet = '1234567890';
                    $digitLength = 2;

                    $digitAlphabetLimit = strlen($digitAlphabet) - 1;
                    $digit_suffix = '';

                    /*
                    for ($i = 0; $i < $digitLength; ++$i) {
                        $digit_suffix .= $digitAlphabet[mt_rand(0, $digitAlphabetLimit)];
                    }*/

                    $strUser = explode(' ', $reservation['tenant_fullname']);
                    //$wifi_user =  strlen($reservation['tenant_fullname']) > 3 ? substr($reservation['tenant_fullname'],0,3) : $reservation['tenant_fullname'];
                    $wifi_user = strtolower($strUser[0]);
                    $rooms = explode(',', $reservation['room']);
                    $wifi_user = strtolower($wifi_user . $rooms[0] . $digit_suffix);

                    //create random wifi password
                    $passAlphabet = 'abcdefghikmnpqrstuvxyz'; //'abcdefghikmnpqrstuvxyz234567890';
                    $passLength = 4;

                    //Password generation procedure
                    $passAlphabetLimit = strlen($passAlphabet) - 1;
                    $wifi_pass = '';
                    for ($i = 0; $i < $passLength; ++$i) {
                        $wifi_pass .= $passAlphabet[mt_rand(0, $passAlphabetLimit)];
                    }

                    //Limit Uptime
                    $expiry_date = ymd_from_db($reservation['departure_date']);

                    $limit_profile = '';
                    //$duration = num_of_days(date('Y-m-d'), ymd_from_db($reservation['departure_date']), RENT_BY_NIGHT);
                    $duration = num_of_days(ymd_from_db($reservation['arrival_date']), ymd_from_db($reservation['departure_date']), RENT_BY_NIGHT);
                    if ($duration > 0) {
                        if ($duration <= 30) {
                            $duration = 30;
                        }

                        $limit_profile .= $duration . WIFI_ROUTER::PROFILE_SUFFIX;
                    }

                    //Old Wifi user
                    $prevWifiId = '';
                    $currentWifi = $this->db->get_where('cs_reservation_wifi', array('reservation_id' => $reservation['reservation_id']));
                    if ($currentWifi->num_rows() > 0) {
                        $currentWifi = $currentWifi->row();
                        $prevWifiId = $currentWifi->router_id;
                    }

                    if ($wifi_user != '' && $wifi_pass != '' && $limit_profile != '') {
                        require_once APPPATH . 'third_party/routeros/routeros_api.class.php';

                        $API = new RouterosAPI();
                        $API->debug = false;

                        //if ($API->connect(WIFI_ROUTER::SERVER_IP, 'IT', 'dwijaya2015'))
                        if ($API->connect(WIFI_ROUTER::SERVER_IP, WIFI_ROUTER::SERVER_USER, WIFI_ROUTER::SERVER_PASS)) {
                            $debugg = '';
                            if (trim($prevWifiId) != '') {
                                $deleted = $API->comm('/tool/user-manager/user/remove', array(
                                        ".id" => $prevWifiId
                                    )
                                );
                                //var_dump($deleted);
                            }

                            //$find = $API->comm('/tool/user-manager/user/get', array('name' => $wifi_user));
                            //var_dump($find);

                            $response = $API->comm('/tool/user-manager/user/add', array(
                                    "customer" => WIFI_ROUTER::CUSTOMER,
                                    "username" => $wifi_user,
                                    //"name" => $reservation['tenant_fullname'],
                                    "comment" => $reservation['tenant_fullname'],
                                    "password" => $wifi_pass,
                                    "copy-from" => $limit_profile
                                )
                            );

                            //var_dump($response);

                            //$READ = $API->read(false);
                            //$ARRAY = $API->parse_response($READ);

                            //print_r($ARRAY);
                            $API->disconnect();

                            //BEGIN TRANSACTION
                            $this->db->trans_begin();

                            //Insert to db
                            $wifi_unit_id = 0;
                            $units = $this->db->get_where('cs_reservation_unit', array('reservation_id' => $reservation['reservation_id']));
                            if ($units->num_rows() > 0) {
                                $wifi_unit_id = $units->row()->unit_id;
                            }

                            $data['reservation_id'] = $reservation['reservation_id'];
                            $data['user_id'] = $wifi_user;
                            $data['password'] = $wifi_pass;
                            $data['limit_uptime'] = $limit_profile;
                            $data['unit_id'] = $wifi_unit_id;
                            $data['expiry_date'] = $expiry_date;
                            $data['status'] = STATUS_NEW;
                            $data['router_id'] = isset($response) ? $response : '';

                            //UPDATE OLD wifi to closed
                            $this->mdl_general->update('cs_reservation_wifi', array('reservation_id' => $data['reservation_id'], 'unit_id' => $wifi_unit_id, 'status' => STATUS_NEW, 'expiry_date <' => $expiry_date), array('status' => STATUS_CLOSED));

                            $rows = $this->db->get_where('cs_reservation_wifi', array('reservation_id' => $data['reservation_id'], 'unit_id' => $wifi_unit_id, 'expiry_date' => $expiry_date, 'status' => STATUS_NEW));
                            if ($rows->num_rows() <= 0) {
                                $this->db->insert('cs_reservation_wifi', $data);
                                $insertID = $this->db->insert_id();

                                $data['voucher_id'] = $insertID;
                            } else {
                                $voucher_id = $rows->row()->voucher_id;
                                $this->mdl_general->update('cs_reservation_wifi', array('voucher_id' => $voucher_id), $data);
                            }

                            if ($this->db->trans_status() === FALSE) {
                                $this->db->trans_rollback();

                                $data['valid'] = 0;
                                $data['debug'] = 'Wifi can not be created.';
                            } else {
                                $this->db->trans_commit();
                                $data['valid'] = 1;
                                $data['debug'] = 'Wifi voucher created';
                            }
                            //$data['debug'] = $ARRAY;
                        } else {
                            $data['valid'] = 0;
                            $data['debug'] = 'Can not connect to wifi router.';
                        }
                    }
                } catch (Exception $e) {
                    if (isset($API)) {
                        $API->disconnect();
                    }

                    $data['valid'] = 0;
                    $data['debug'] = $e;
                }
            } else {
                $data['valid'] = 0;
                $data['debug'] = 'Reservation not found !';
            }
        }else{
            $data['valid'] = 0;
            $data['debug'] = 'Wifi not available !';
        }

        return $data;
    }

    private function insertCloseSchedules($reservation = array()){
        $valid = true;

        if(isset($reservation)){
            $reservationID =  $reservation['reservation_id'];
            $reservationType = $reservation['reservation_type'];

            if($reservationType != RES_TYPE::HOUSE_USE) {
                $units = $this->db->get_where('cs_reservation_unit', array('reservation_id' => $reservationID));
                if ($units->num_rows() > 0) {
                    foreach ($units->result() as $unit) {
                        $startDate = DateTime::createFromFormat('Y-m-d', $reservation['arrival_date']);
                        $endDate = DateTime::createFromFormat('Y-m-d', $reservation['departure_date']);
                        $totalMonths = num_of_months($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

                        $day = date('d', strtotime(ymd_from_db($reservation['arrival_date'])));

                        for ($i = 0; $i < $totalMonths; $i++) {
                            $res = $this->db->query("SELECT out_date FROM fxnCheckout_Date('" . $startDate->format('Y-m-d') . "'," . BILLING_BASE::MONTHLY . "," . 1 . ")");
                            if ($res->num_rows() > 0) {
                                $closeDate = DateTime::createFromFormat('Y-m-d', ymd_from_db($res->row()->out_date));
                                if($day >= 30){
                                    $month = $closeDate->format('m');
                                    $year = $closeDate->format('Y');
                                    $maxdays = days_in_month($month, $year);
                                    if($day <= $maxdays){
                                        //echo 'A ' . $closeDate->format('Y-m'). '-'. ($day-1);
                                        $closeDate = DateTime::createFromFormat('Y-m-d', $closeDate->format('Y-m') . '-' . ($day-1));
                                    }else{
                                        //echo 'B ' . $closeDate->format('Y-m'). '-'. $maxdays;
                                        $closeDate = DateTime::createFromFormat('Y-m-d', $closeDate->format('Y-m') . '-' . ($maxdays-1));
                                    }
                                }
                                if ($closeDate <= $endDate) {
                                    $schedule = array();
                                    $schedule['reservation_id'] = $reservationID;
                                    $schedule['unit_id'] = $unit->unit_id;
                                    $schedule['close_date'] = $closeDate->format('Y-m-d');
                                    $schedule['close_status'] = 0;
                                    $schedule['status'] = STATUS_NEW;

                                    $this->db->insert('cs_sales_close', $schedule);
                                    $insertID = $this->db->insert_id();

                                    if ($insertID <= 0) {
                                        $valid = false;
                                    } else {
                                        $month = $closeDate->format('m');
                                        $year = $closeDate->format('Y');

                                        $maxdays = days_in_month($month, $year);
                                        if($day <= $maxdays){
                                            $startDate = DateTime::createFromFormat('Y-m-d', $closeDate->format('Y-m') . '-' . $day);
                                        }else{
                                            $startDate = DateTime::createFromFormat('Y-m-d', $closeDate->format('Y-m') . '-' . $maxdays);
                                        }
                                        //$startDate = $closeDate->add(new DateInterval('P1D'));
                                    }
                                } else {
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $valid;
    }

    private function saveOrUpdateTenant(){
        $tenantId = 0;

        if(isset($_POST)){
            $server_date = date('Y-m-d');

            $tenantId = $_POST['tenant_id'];
            $tenant['tenant_salutation'] = $_POST['tenant_salutation'];
            $tenant['tenant_fullname'] = $_POST['tenant_name'];
            $tenant['tenant_type'] = $_POST['tenant_type'];
            $tenant['tenant_phone'] = $_POST['tenant_phone'];
            $tenant['tenant_cellular'] = $_POST['tenant_cellular'];
            $tenant['tenant_email'] = $_POST['tenant_email'];
            $tenant['tenant_sex'] = $_POST['tenant_sex'];
            $tenant['tenant_address'] = $_POST['tenant_address'];
            $tenant['tenant_city'] = $_POST['tenant_city'];
            $tenant['tenant_postalcode'] = $_POST['tenant_postalcode'];
            $tenant['tenant_country'] = $_POST['tenant_country'];
            $tenant['tenant_pob'] = $_POST['tenant_pob'];
            $tenant['tenant_dob'] = trim($_POST['tenant_dob']) != '' ? dmy_to_ymd($_POST['tenant_dob']) : $server_date;
            $tenant['tenant_occupation'] = $_POST['tenant_occupation'];
            $tenant['id_type'] = $_POST['id_type'];
            $tenant['passport_no'] = $_POST['passport_no'];
            $tenant['passport_issueddate'] = trim($_POST['passport_issueddate']) != '' ? dmy_to_ymd($_POST['passport_issueddate']) : $server_date;
            $tenant['passport_issuedplace'] = $_POST['passport_issuedplace'];

            //Save or Update Company
            $companyId = intval($_POST['company_id']);
            $companyName = $_POST['company_name'];
            $company['company_address'] = $_POST['company_address'];
            $company['company_phone'] = $_POST['company_phone'];
            //$company['company_cellular'] = $_POST['company_cellular'];
            $company['company_email'] = $_POST['company_email'];

            if(trim($companyName) != ''){
                $checkCompany = $this->db->query("select company_id from ms_company where company_name like '%" . $companyName . "%'");
                if($checkCompany->num_rows() <= 0){
                    if($companyId <= 0){
                        $company['company_name'] = $companyName;
                        $company['status'] = STATUS_NEW;

                        $this->db->insert('ms_company', $company);
                        $companyId = $this->db->insert_id();
                    }else{
                        $this->mdl_general->update('ms_company', array('company_id' => $companyId), $company);
                    }
                }else{
                    $companyId = $checkCompany->row()->company_id;
                    $this->mdl_general->update('ms_company', array('company_id' => $companyId), $company);
                }
            }
            $tenant['company_id'] = $companyId;

            if($tenantId <= 0){
                $tenant['tenant_account'] = $this->mdl_general->generateNewTenantCode();

                $tenant['created_by'] = my_sess('user_id');
                $tenant['created_date'] = $server_date;
                $tenant['modified_by'] = 0;
                $tenant['modified_date'] = $server_date;
                $tenant['status'] = STATUS_NEW;

                $this->db->insert('ms_tenant', $tenant);
                $tenantId = $this->db->insert_id();
            }else{
                $tenant['modified_by'] = my_sess('user_id');;
                $tenant['modified_date'] = $server_date;

                $this->mdl_general->update('ms_tenant', array('tenant_id' => $tenantId), $tenant);
            }
        }

        return $tenantId;
    }

    private function postingRoomBill($rsvt = array(), $billHeader = array(), $billDetails = array()){
        $valid = true;

        if(count($rsvt) > 0 && count($billHeader) > 0 && count($billDetails) > 0){
            $journalNo = trim($billHeader['journal_no']);
            //echo '<br>[postingRoomBill] ' . $journalNo;
            if($journalNo != ''){
                $this->load->model('finance/mdl_finance');

                //Post Journal
                //AR Personal / AR Corporate
                // Bridging Sales / Unearned Sales
                $tenantName = $rsvt['tenant_fullname'];

                $detail = array();
                $totalCredit = 0;

                $bridging_sales = FNSpec::get(FNSpec::SALES_BRIDGING_ACCOUNT);
                if($bridging_sales['coa_id'] > 0){
                    foreach($billDetails as $bill){
                        $units = $this->db->query("select ms_unit.unit_code, ms_unit_type.unittype_desc, ms_unit_type.transtype_id, ms_transtype.transtype_desc, ms_transtype.coa_id
                                                          from ms_unit
                                                          left join ms_unit_type on ms_unit_type.unittype_id = ms_unit.unittype_id
                                                          left join ms_transtype on ms_transtype.transtype_id = ms_unit_type.transtype_id
                                                          where ms_unit.unit_id = " . $bill['unit_id']);
                        if($units->num_rows() > 0){
                            $unit = $units->row();

                            if($unit->coa_id > 0){
                                //echo '<br>[postingRoomBill] cr : ' . $bill['amount'] . ' + '. $bill['disc_amount'] . ' + ' . $bill['tax'];
                                $amount = round($bill['amount'] - $bill['disc_amount'] + $bill['tax'], 0);

                                $rowdet = array();
                                $rowdet['coa_id'] = $bridging_sales['coa_id']; // $unit->coa_id;
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $tenantName . ' - ' . $unit->unit_code . ' (' . ymd_to_dmy($bill['date_start']) . ' to ' . ymd_to_dmy($bill['date_end']) . ')';
                                $rowdet['journal_debit'] = 0;
                                $rowdet['journal_credit'] = $amount;
                                $rowdet['transtype_id'] = $unit->transtype_id;
                                $rowdet['reference_id'] = $bill['reservation_id'];

                                array_push($detail, $rowdet);

                                $totalCredit += $amount;
                                //echo '<br>[postingRoomBill] cr : ' . $amount;
                            }
                        }
                    }
                }else{
                    $valid = false;
                }

                //echo '<br>[postingRoomBill] valid 1 : ' . ($valid ? 'true' :'false') . ' credit : ' . $totalCredit;

                $totalDebit = 0;
                if($valid){
                    $journal_desc = $rsvt['reservation_code'] . ' ' . $tenantName . ' / ' . $rsvt['room'];
                    if($totalCredit > 0 && count($detail) > 0){
                        $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                        if($rsvt['reservation_type'] == RES_TYPE::CORPORATE){
                            $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                        }
                        if($ar['coa_id'] > 0){
                            $rowdet = array();
                            $rowdet['coa_id'] = $ar['coa_id'];
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $journal_desc;
                            $rowdet['journal_debit'] = $totalCredit;
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = $rsvt['reservation_id'];
                            $rowdet['transtype_id'] = $ar['transtype_id'];

                            array_push($detail, $rowdet);

                            $totalDebit = $totalCredit;
                        }
                    }
                }

                //echo '<br>[postingRoomBill] valid 2 : ' . $totalDebit . ' == ' . $totalCredit;
                if(($totalDebit == $totalCredit) && $totalDebit > 0 && $valid){
                    $header = array();
                    $header['journal_no'] = $journalNo;
                    $header['journal_date'] = $billHeader['bill_date'];
                    $header['journal_remarks'] = $journal_desc;
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
        }

        return $valid;
    }

    public function xsave_reservation_remark(){
        $result = array();
        //Used to display notification
        $result['valid'] = '1';
        $result['message'] = '';

        if(isset($_POST)){
            $reservation_id = $_POST['reservation_id'];
            $data['remark'] = $_POST['remark'];
            if($reservation_id > 0){
                $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $reservation_id), $data);
                $result['message'] = $data['remark'];
            }else{
                $result['valid'] = '0';
                $result['message'] = 'Remark can not be saved !';
            }
        }

        echo json_encode($result);
    }

    public function xwifi_voucher_gen(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        if(isset($_POST)){
            $reservation_id = $_POST['reservation_id'];
            if($reservation_id > 0){
                if(ALLOW_WIFI_GEN) {
                    $reservation = $this->db->get_where('view_cs_reservation', array('reservation_id' => $reservation_id));
                    if ($reservation->num_rows() > 0) {
                        $reservation = $reservation->row_array();
                        if($reservation['status'] == ORDER_STATUS::CHECKIN) {
                            $wifi_voucher = $this->generateWifiVoucher($reservation);
                            if ($wifi_voucher['valid'] == 1) {
                                $result['wifi_user'] = $wifi_voucher['user_id'];
                                $result['wifi_pass'] = $wifi_voucher['password'];
                                $result['wifi_uptime'] = $wifi_voucher['limit_uptime'];
                                $result['wifi_expiry'] = ymd_to_dmy($wifi_voucher['expiry_date']);

                                $result['type'] = '1';
                                $result['message'] = $wifi_voucher['debug'];//'Wifi voucher created.';
                            } else {
                                $result['type'] = '0';
                                $result['message'] = $wifi_voucher['debug'];
                            }
                        }else{
                            $result['type'] = '0';
                            $result['message'] = 'Folio already Checked Out ! Wifi voucher no longer valid.';
                        }
                    } else {
                        $result['type'] = '0';
                        $result['message'] = 'Wifi voucher can not be created !';
                    }
                }else{
                    $result['type'] = '0';
                    $result['message'] = 'Wifi voucher not available !';
                }
            }else{
                $result['type'] = '0';
                $result['message'] = 'Wifi voucher can not be created !';
            }
        }

        echo json_encode($result);
    }

    #endregion

    #region Manage Official Receipt

    public function submit_booking_receipt(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';
        $result['document_link'] = '';
        $result['debug'] = '' ;

        $reservationId = $_POST['reservation_id'];

        $data['reservation_id'] = $reservationId;
        $data['bookingreceipt_date'] = dmy_to_ymd($_POST['receipt_date']);
        $data['paymenttype_id'] = $_POST['paymenttype_id'];
        $data['bankaccount_id'] = $_POST['bankaccount_id'];

        $is_invoice = isset($_POST['is_invoice']) ? $_POST['is_invoice'] : 0;
        $inv_ids = isset($_POST['inv_ids']) ? $_POST['inv_ids'] : array();
        $inv_amounts = isset($_POST['inv_amounts']) ? $_POST['inv_amounts'] : array();

        $data['is_primary_debtor']  = isset($_POST['is_primary_debtor']) ? $_POST['is_primary_debtor'] : 1;
        $data['is_ar_payment'] = 0;
        $data['bookingreceipt_desc'] = $_POST['desc'];
        $data['receipt_amount'] = doubleval($_POST['receipt_amount']);
        $data['receipt_bank_fee'] = 0;
        $data['receipt_veritrans_fee'] = 0;
        $data['ccard_name'] = isset($_POST['creditcard_name']) ? $_POST['creditcard_name'] : '';
        $data['ccard_no'] = isset($_POST['creditcard_no']) ? $_POST['creditcard_no'] : '';
        $data['ccard_expiry_month'] = isset($_POST['creditcard_expiry_month']) ? $_POST['creditcard_expiry_month'] : '';
        $data['ccard_expiry_year'] = isset($_POST['creditcard_expiry_year']) ? $_POST['creditcard_expiry_year'] : '';
        $data['status'] = STATUS_NEW;
        $data['is_refunded'] = 0;

        //SET Redirected URL by request
        $result['redirect_link'] = '';
        $requestType = isset($_POST['request_type']) ? $_POST['request_type'] : '';
        if($requestType != ''){
            if(strtolower(trim($requestType)) == 'guest'){
                $result['redirect_link'] = base_url('frontdesk/management/guest_form/' . $reservationId .'.tpd');
            }
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Official Receipt';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $reservationId;
        $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION_RECEIPT;

        if($reservationId > 0 && $data['status'] > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $valid = true;
            $qry = $this->db->get_where('cs_reservation_header', array('reservation_id' => $reservationId));
            if($qry->num_rows() > 0){
                $row = $qry->row();
                try{
                    //OBTAIN PENDING AMOUNT
                    $paymenttype = $this->db->query('SELECT * FROM ms_payment_type WHERE paymenttype_id = ' . $data['paymenttype_id']);
                    if($paymenttype->num_rows() > 0){
                        $paymenttype = $paymenttype->row_array();

                        $pending_amount = $this->get_pending_amount($reservationId);

                        if($paymenttype['payment_type'] != PAYMENT_TYPE::AR_TRANSFER){
                            if($paymenttype['payment_type'] == PAYMENT_TYPE::CREDIT_CARD){
                                $ccard = $this->db->query('SELECT * FROM ms_payment_type WHERE payment_type = ' . PAYMENT_TYPE::CREDIT_CARD . ' AND status = ' . STATUS_NEW);

                                if($ccard->num_rows() > 0){
                                    $card = $ccard->row();
                                    $bankPercent = $card->card_percent;

                                    if($row->is_frontdesk <= 0){
                                        if($bankPercent > 0){
                                            $data['receipt_bank_percent'] = $bankPercent;
                                            $data['receipt_bank_fee'] = $data['receipt_amount'] * ($bankPercent/100);
                                        }
                                        if($card->veritrans_fee > 0){
                                            $data['receipt_veritrans_fee'] = $card->veritrans_fee;
                                        }
                                    }else{
                                        if($paymenttype['payment_type'] == PAYMENT_TYPE::CREDIT_CARD){
                                            if($bankPercent > 0){
                                                $data['receipt_bank_percent'] = $bankPercent;
                                                $data['receipt_bank_fee'] = $data['receipt_amount'] * ($bankPercent/100);
                                            }
                                        }else{
                                            if($data['ccard_name'] == '' && $data['ccard_no']== ''){
                                                $data['ccard_expiry_month'] = 0;
                                                $data['ccard_expiry_year'] = 0;
                                            }
                                        }
                                    }
                                }
                            }else{
                                $data['ccard_expiry_month'] = 0;
                                $data['ccard_expiry_year'] = 0;
                            }

                            //Find Bank account by paymenttype_id
                            if($paymenttype['payment_type'] != PAYMENT_TYPE::BANK_TRANSFER){
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

                            //Re-Calculate receipt amount
                            $data['receipt_amount'] = $data['receipt_amount'] - $data['receipt_bank_fee'] - $data['receipt_veritrans_fee'];

                            $serverDate = date('Y-m-d H:i:s');

                            //Insert RV
                            $rv = array();
                            $rv['receipt_date'] = $data['bookingreceipt_date'];
                            $rv['created_by'] = my_sess('user_id');
                            $rv['created_date']  = date('Y-m-d H:i:s');
                            $rv['receipt_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_RECEIPT, $rv['receipt_date']);
                            $rv['status'] = STATUS_POSTED;

                            if($row->reservation_type == RES_TYPE::CORPORATE && $data['is_primary_debtor'] > 0){
                                $rv['company_id'] = $_POST['company_id'];
                                $rv['reservation_id'] = 0;
                                $rv['tenant_id'] = 0;
                            }else{
                                $rv['company_id'] = 0;
                                $rv['reservation_id'] = $_POST['reservation_id'];
                                $rv['tenant_id'] = $row->tenant_id;
                            }

                            $rv['deposit_id'] = 0;
                            $rv['is_invoice'] = $is_invoice;
                            $rv['paymenttype_id'] = $_POST['paymenttype_id'];
                            $rv['bankaccount_id'] = $_POST['bankaccount_id'];
                            $rv['receipt_desc'] =  $_POST['desc'];
                            $rv['receipt_bankcharges'] = $data['receipt_bank_fee'];
                            $rv['receipt_paymentamount'] = $data['receipt_amount'];

                            $rv['card_name'] = isset($_POST['creditcard_name']) ? $_POST['creditcard_name'] : '';
                            $rv['card_no'] = isset($_POST['creditcard_no']) ? $_POST['creditcard_no'] : '';
                            $rv['card_expiry_month'] = isset($_POST['creditcard_expiry_month']) ? $_POST['creditcard_expiry_month'] : '';
                            $rv['card_expiry_year'] = isset($_POST['creditcard_expiry_year']) ? $_POST['creditcard_expiry_year'] : '';
                            $rv['card_bank'] = '';

                            $this->db->insert('ar_receipt', $rv);
                            $receiptID = $this->db->insert_id();

                            $rv['receipt_id'] = $receiptID;

                            if($receiptID > 0 && trim($rv['receipt_no']) != ''){
                                $data['receipt_no'] = $rv['receipt_no'];
                            }else{
                                $valid = false;
                            }

                            if($valid){
                                //POST a Journal
                                //Bank
                                // - AR
                                $valid = $this->postingBookingReceipt($rv, $paymenttype);
                            }

                            if(count($inv_ids) > 0){
                                //Do Allocate to invoice
                                $valid = $this->allocateReceiptToBill($rv, $inv_ids);
                            }

                            if($valid){
                                $this->mdl_general->update('ar_receipt',array('receipt_id' => $receiptID), array('status' => STATUS_POSTED));

                                $result['document_link'] = base_url('ar/report/pdf_official_receipt/' . $data['receipt_no'] .'.tpd');
                            }
                        }else{
                            //ONLY FOR change Corporate Guest bill to Corporate AR ONLY
                            $receipt_amount = doubleval($_POST['receipt_amount']);

                            $bill_date = date('Y-m-d');

                            if($row->reservation_type == RES_TYPE::CORPORATE && $row->company_id > 0 && $receipt_amount > 0){
                                $tax_rate = 0;
                                $tax_type = $this->db->get_where('tax_type', array('is_charge_default > ' => 0));
                                if($tax_type->num_rows() > 0){
                                    $tax_rate = $tax_type->row()->taxtype_percent;
                                    if($tax_rate > 0){
                                        $tax_rate = $tax_rate / 100;
                                    }
                                }

                                //Get UNPAID
                                $total_amount = ($tax_rate > 0 ? $receipt_amount / (1 + $tax_rate) : $receipt_amount);
                                $total_tax = ($tax_rate > 0 ? $tax_rate * $total_amount : 0);

                                $units = $this->db->query('SELECT ms_unit.unit_id,ms_unit_type.transtype_id FROM cs_reservation_unit
                                                          JOIN ms_unit ON cs_reservation_unit.unit_id = ms_unit.unit_id
                                                          JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_unit.unittype_id
                                                          WHERE cs_reservation_unit.reservation_id = ' . $row->reservation_id);
                                if($units->num_rows() > 0){
                                    $unit = $units->row();

                                    $sales = FNSpec::get(FNSpec::SALES_RESERVATION);

                                    //TRANSFER TO CORPORATE
                                    $bill = array();
                                    $bill['reservation_id'] = $row->reservation_id;
                                    $bill['company_id'] = 0;
                                    $bill['tenant_id'] = $row->tenant_id ;
                                    $bill['billdetail_id'] = 0;
                                    $bill['unit_id'] = $unit->unit_id;
                                    $bill['transtype_id'] = $sales['transtype_id'];
                                    $bill['bill_startdate'] = $bill_date;
                                    $bill['bill_enddate'] = $bill_date;
                                    $bill['amount'] = $receipt_amount;
                                    $bill['discount'] = 0;
                                    $bill['tax'] = 0;
                                    $bill['total_amount'] = $receipt_amount;

                                    $bill['is_billed'] = 0;
                                    $bill['is_othercharge'] = 6;
                                    $bill['status'] = STATUS_NEW;
                                    $bill['month'] = 1;

                                    $this->db->insert('cs_corporate_bill', $bill);

                                    if($this->db->insert_id() <= 0){
                                        $valid = false;
                                    }

                                    if($valid){
                                        //POST a Reverse Journal
                                        //Sales
                                        // - AR Personal
                                        $bill['bill_no'] = $row->reservation_code;
                                        $bill['remark'] = $_POST['desc'];
                                        //$valid = $this->postingARTransfer($bill, $paymenttype);
                                    }

                                    if($valid){
                                        //ALLOCATE TRANSFER AR To PENDING BILL
                                        //$valid = $this->allocateARToBill($data, $row->reservation_type);
                                    }
                                }
                            }else{
                                $valid = false;
                            }
                        }

                        //$result['debug'] .= 'Posting -> ' . $valid;
                    }

                }catch(Exception $e){
                    $valid = false;
                }

                if($valid){
                    $data_log['action_type'] = STATUS_CLOSED;
                    $this->db->insert('app_log', $data_log);
                }

                //FINALIZE TRANSACTION
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Official Receipt can not be processed.';
                }
                else
                {
                    if($valid){
                        $this->db->trans_commit();

                        $result['type'] = '1';
                        $result['message'] = 'Official Receipt successfully submitted.';

                    }else{
                        $this->db->trans_rollback();

                        $result['type'] = '0';
                        $result['message'] = 'Official Receipt can not be processed.';
                    }
                }
            }
        }

        echo json_encode($result);
    }

    private function createInitialReceiptDetail($reservation_id = 0, $bookingreceipt_id = 0){
        $this->load->model('frontdesk/mdl_frontdesk');

        $valid = true;

        if($reservation_id > 0 && $bookingreceipt_id > 0){
            $cshead = $this->db->get_where('view_cs_reservation', array('reservation_id'=>$reservation_id));
            if($cshead->num_rows() > 0){
                $head = $cshead->row();

                $csdet = $this->db->query('SELECT DISTINCT cs_reservation_detail.unit_id, cs_reservation_unit.discount, ms_unit.unittype_id FROM cs_reservation_detail
                                           JOIN ms_unit ON ms_unit.unit_id = cs_reservation_detail.unit_id
                                           JOIN cs_reservation_unit ON cs_reservation_unit.reservation_id = cs_reservation_detail.reservation_id
                                           WHERE cs_reservation_detail.reservation_id = ' . $reservation_id);
                foreach($csdet->result() as $unit){
                    if($valid){
                        try{
                            $calc = $this->mdl_frontdesk->calculate_booking($unit->unittype_id, dmy_from_db($head->arrival_date), dmy_from_db($head->departure_date), $head->reservation_type, $head->is_rate_yearly, $head->is_rate_yearly);

                            $receipt_detail['bookingreceipt_id'] = $bookingreceipt_id;
                            $receipt_detail['unit_id'] = $unit->unit_id;
                            $baseAmount = ($calc['total_amount'] - $unit->discount);
                            $taxAmount = $calc['tax_rate'] * $baseAmount;
                            $receipt_detail['receipt_amount'] = round($baseAmount + $taxAmount,0);
                            $receipt_detail['status'] = STATUS_NEW;

                            $this->db->insert('cs_booking_receipt_detail', $receipt_detail);
                            if($this->db->insert_id() <= 0){
                                $valid = false;
                            }
                        }catch(Exception $e){
                            $valid = false;
                        }
                    }else{
                        break;
                    }
                }
            }
        }

        return $valid;
    }

    private function allocateReceiptToBill($rv = array(), $inv_ids = array()){
        $valid = true;

        if(count($rv) > 0 && count($inv_ids) > 0){
            try{
                $reservationId = $rv['reservation_id'];

                $alloc = array();

                $current_date = date('Y-m-d H:i:s');
                $receipt_date = ymd_from_db($rv['receipt_date']);
                $availableAmount = ($rv['receipt_bankcharges'] + $rv['receipt_paymentamount']);

                foreach($inv_ids as $inv_id){
                    //Allocate to CS Receipt Allocation
                    $unpaids = $this->db->get_where('view_ar_unpaid_invoice', array('inv_id'=> $inv_id));
                    if($unpaids->num_rows() > 0){
                        foreach($unpaids->result_array() as $bill){
                            //AMOUNT
                            if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                                $detail = array();
                                $detail['receipt_id'] = $rv['receipt_id'];
                                $detail['invdetail_id'] = $bill['invdetail_id'];
                                $detail['bookingreceipt_id'] = 0;
                                $detail['billdetail_id'] = 0;
                                $detail['allocation_date'] = $rv['receipt_date'];
                                $detail['is_debitnote'] = 0;
                                $detail['allocationheader_id'] = 0;
                                $detail['created_by'] = my_sess('user_id');
                                $detail['created_date'] = $current_date;
                                $detail['status'] = STATUS_CLOSED;
                                $detail['is_tax'] = 0;

                                if($bill['pending_amount'] <= $availableAmount){
                                    $detail['allocation_amount'] = $bill['pending_amount'];
                                }else{
                                    $detail['allocation_amount'] = $availableAmount;
                                }

                                if($detail['allocation_amount'] > 0){
                                    $this->db->insert('ar_receipt_allocation', $detail);
                                    if($this->db->insert_id() <= 0){
                                        $valid = false;
                                        break;
                                    }

                                    $availableAmount -= $detail['allocation_amount'];
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
                                $detail['receipt_id'] = $rv['receipt_id'];
                                $detail['invdetail_id'] = $bill['invdetail_id'];
                                $detail['bookingreceipt_id'] = 0;
                                $detail['billdetail_id'] = 0;
                                $detail['allocation_date'] = $rv['receipt_date'];
                                $detail['is_debitnote'] = 0;
                                $detail['allocationheader_id'] = 0;
                                $detail['created_by'] = my_sess('user_id');
                                $detail['created_date'] = $current_date;
                                $detail['status'] = STATUS_CLOSED;
                                $detail['is_tax'] = 1;

                                if($bill['pending_tax'] <= $availableAmount){
                                    $detail['allocation_amount'] = $bill['pending_tax'];
                                }else{
                                    $detail['allocation_amount'] = $availableAmount;
                                }

                                if($detail['allocation_amount'] > 0){
                                    $this->db->insert('ar_receipt_allocation', $detail);
                                    if($this->db->insert_id() <= 0){
                                        $valid = false;
                                        break;
                                    }

                                    $availableAmount -= $detail['allocation_amount'];
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
            }catch(Exception $e){
                $valid = false;
                echo '<br>[allocateReceiptToBill][Error] ' . $e;
            }
        }

        return $valid;
    }

    private function allocateReceiptToBill_v1($rv = array(), $reservation_type){
        $valid = true;

        if(count($rv) > 0){
            $this->load->model('frontdesk/mdl_frontdesk');

            $availableAmount = $rv['receipt_amount'] + $rv['receipt_veritrans_fee'] + $rv['receipt_bank_fee'];

            $current_date = date('Y-m-d H:i:s');

            //Allocate to CS Receipt Allocation
            $unpaids = $this->db->query('SELECT billdetail_id, company_id, tenant_id, pending_amount FROM fxnARUnpaidBillByID(' . $rv['reservation_id'] . ')');
            if($unpaids->num_rows() > 0){
                if($reservation_type == RES_TYPE::CORPORATE){
                    $is_primary_debtor = $rv['is_primary_debtor'] > 0 ? true : false;
                    foreach($unpaids->result_array() as $bill){
                        if($is_primary_debtor){
                            if($bill['company_id'] > 0){
                                if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                                    $detail['bookingreceipt_id'] = $rv['bookingreceipt_id'];
                                    $detail['allocation_date'] = $rv['bookingreceipt_date'];
                                    if($bill['pending_amount'] <= $availableAmount){
                                        $detail['allocation_amount'] = $bill['pending_amount'];
                                    }else{
                                        $detail['allocation_amount'] = $availableAmount;
                                    }
                                    $detail['billdetail_id'] = $bill['billdetail_id'];
                                    $detail['is_tax'] = 0;
                                    $detail['is_debitnote'] = 0;
                                    $detail['allocationheader_id'] = 0;
                                    $detail['created_by'] = my_sess('user_id');
                                    $detail['created_date'] = $current_date;
                                    $detail['status'] = STATUS_CLOSED;

                                    $availableAmount -= $detail['allocation_amount'];

                                    if($detail['allocation_amount'] > 0){
                                        $this->db->insert('ar_receipt_allocation', $detail);
                                        if($this->db->insert_id() <= 0){
                                            $valid = false;
                                            break;
                                        }
                                    }
                                }
                            }
                        }else{
                            if($bill['tenant_id'] > 0 && $bill['company_id'] <= 0){
                                if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                                    $detail['bookingreceipt_id'] = $rv['bookingreceipt_id'];
                                    $detail['allocation_date'] = $rv['bookingreceipt_date'];
                                    if($bill['pending_amount'] <= $availableAmount){
                                        $detail['allocation_amount'] = $bill['pending_amount'];
                                    }else{
                                        $detail['allocation_amount'] = $availableAmount;
                                    }
                                    $detail['billdetail_id'] = $bill['billdetail_id'];
                                    $detail['is_tax'] = 0;
                                    $detail['is_debitnote'] = 0;
                                    $detail['allocationheader_id'] = 0;
                                    $detail['created_by'] = my_sess('user_id');
                                    $detail['created_date'] = $current_date;
                                    $detail['status'] = STATUS_CLOSED;

                                    $availableAmount -= $detail['allocation_amount'];

                                    if($detail['allocation_amount'] > 0){
                                        $this->db->insert('ar_receipt_allocation', $detail);
                                        if($this->db->insert_id() <= 0){
                                            $valid = false;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }else{
                    foreach($unpaids->result_array() as $bill){
                        if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                            $detail['bookingreceipt_id'] = $rv['bookingreceipt_id'];
                            $detail['allocation_date'] = $rv['bookingreceipt_date'];
                            if($bill['pending_amount'] <= $availableAmount){
                                $detail['allocation_amount'] = $bill['pending_amount'];
                            }else{
                                $detail['allocation_amount'] = $availableAmount;
                            }
                            $detail['billdetail_id'] = $bill['billdetail_id'];
                            $detail['is_tax'] = 0;
                            $detail['is_debitnote'] = 0;
                            $detail['allocationheader_id'] = 0;
                            $detail['created_by'] = my_sess('user_id');
                            $detail['created_date'] = $current_date;
                            $detail['status'] = STATUS_CLOSED;

                            $availableAmount -= $detail['allocation_amount'];

                            if($detail['allocation_amount'] > 0){
                                $this->db->insert('ar_receipt_allocation', $detail);
                                if($this->db->insert_id() <= 0){
                                    $valid = false;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $valid;
    }

    private function allocateARToBill($rv = array(), $reservation_type){
        $valid = true;

        if(count($rv) > 0){
            $this->load->model('frontdesk/mdl_frontdesk');

            $availableAmount = $rv['receipt_amount'] + $rv['receipt_veritrans_fee'] + $rv['receipt_bank_fee'];

            $current_date = date('Y-m-d H:i:s');

            //Allocate to CS Receipt Allocation
            $unpaids = $this->db->query('SELECT billdetail_id, company_id, tenant_id, pending_amount FROM fxnARUnpaidBillByID(' . $rv['reservation_id'] . ')');
            if($unpaids->num_rows() > 0){
                if($reservation_type == RES_TYPE::CORPORATE){
                    $is_primary_debtor = $rv['is_primary_debtor'] > 0 ? true : false;
                    foreach($unpaids->result_array() as $bill){
                        //ONLY FOR PRIMARY DEBTOR (COMPANY)
                        if($is_primary_debtor){
                            if($bill['company_id'] > 0){
                                if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                                    $detail['bookingreceipt_id'] = 0;
                                    $detail['is_transfer_ar'] = 1;
                                    $detail['allocation_date'] = $rv['bookingreceipt_date'];
                                    if($bill['pending_amount'] <= $availableAmount){
                                        $detail['allocation_amount'] = $bill['pending_amount'];
                                    }else{
                                        $detail['allocation_amount'] = $availableAmount;
                                    }
                                    $detail['billdetail_id'] = $bill['billdetail_id'];
                                    $detail['is_tax'] = 0;
                                    $detail['is_debitnote'] = 0;
                                    $detail['allocationheader_id'] = 0;
                                    $detail['created_by'] = my_sess('user_id');
                                    $detail['created_date'] = $current_date;
                                    $detail['status'] = STATUS_CLOSED;

                                    $availableAmount -= $detail['allocation_amount'];

                                    if($detail['allocation_amount'] > 0){
                                        $this->db->insert('ar_receipt_allocation', $detail);
                                        if($this->db->insert_id() <= 0){
                                            $valid = false;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $valid;
    }

    public function postingBookingReceipt($rv = array(), $paymenttype = array()){
        $valid = true;

        if(count($rv) > 0 && count($paymenttype) > 0){
            $this->load->model('finance/mdl_finance');

            //Post Journal
            //Bank
            //VT Charge (if any)
            //Bank Charge (if any)
            // AR
            $detail = array();
            $totalDebit = 0;

            $totalCredit = $rv['receipt_bankcharges'] + $rv['receipt_paymentamount'];
            if($totalCredit > 0){
                //BANK
                $bank_coa_id = 0;
                if($rv['bankaccount_id'] > 0){
                    $bank = $this->mdl_finance->getBankAccount($rv['bankaccount_id']);
                    if(isset($bank['coa_id']))
                        $bank_coa_id = $bank['coa_id'];
                }else{
                    $coa = $this->db->get_where('gl_coa', array('coa_code' => $paymenttype['coa_code']));
                    if($coa->num_rows() > 0){
                        $bank_coa_id = $coa->row()->coa_id;
                    }
                }

                if($bank_coa_id > 0){
                    $rowdet = array();
                    $rowdet['coa_id'] = $bank_coa_id;
                    $rowdet['dept_id'] = 0;
                    $rowdet['journal_note'] = $rv['receipt_desc'];
                    $rowdet['journal_debit'] = $rv['receipt_paymentamount'];
                    $rowdet['journal_credit'] = 0;
                    $rowdet['reference_id'] = $rv['receipt_id'];
                    $rowdet['transtype_id'] = 0;

                    array_push($detail, $rowdet);

                    $totalDebit += $rowdet['journal_debit'];
                }else{
                    $valid = false;
                }

                if($valid){
                    //BANK CHARGE (if any)
                    if($rv['receipt_bankcharges'] > 0){
                        $bc = FNSpec::get(FNSpec::BANK_CHARGE);
                        if($bc['coa_id'] > 0){
                            $rowdet = array();
                            $rowdet['coa_id'] = $bc['coa_id'];
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $rv['receipt_desc'];
                            $rowdet['journal_debit'] = $rv['receipt_bankcharges'];
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = $rv['receipt_id'];
                            $rowdet['transtype_id'] = $bc['transtype_id'];

                            array_push($detail, $rowdet);

                            $totalDebit += $rowdet['journal_debit'];
                        }
                    }
                }
            }else{
                $valid = false;
            }

            //echo '\nA ...' . $valid . ' => ' . $totalCredit . ' == ' .  $totalDebit;

            if($valid){
                if($totalCredit > 0 && $totalCredit == $totalDebit && count($detail) > 0){
                    //AR
                    $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                    if($rv['company_id'] > 0){
                        $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                    }

                    if($ar['coa_id'] > 0){
                        $rowdet = array();
                        $rowdet['coa_id'] = $ar['coa_id'];
                        $rowdet['dept_id'] = 0;
                        $rowdet['journal_note'] = $rv['receipt_desc'];
                        $rowdet['journal_debit'] = 0;
                        $rowdet['journal_credit'] = $totalCredit;
                        $rowdet['reference_id'] = $rv['receipt_id'];
                        $rowdet['transtype_id'] = $ar['transtype_id'];

                        array_push($detail, $rowdet);
                    }
                }else{
                    $valid = false;
                }
            }

            if($valid && $totalDebit == $totalCredit && $totalDebit > 0 ){
                $header = array();
                $header['journal_no'] = $rv['receipt_no'];
                $header['journal_date'] = $rv['receipt_date'];
                $header['journal_remarks'] = $rv['receipt_desc'];
                $header['modul'] = GLMOD::GL_MOD_AR;
                $header['journal_amount'] = $totalDebit;
                $header['reference'] = '';
                //$header['reference_date'] = $reservation['reservation_date'];

                $valid = $this->mdl_finance->postJournal($header,$detail);
            }else{
                $valid = false;
            }
        }

        return $valid;
    }

    private function postingARTransfer($bill = array(), $paymenttype = array()){
        $valid = true;

        if(count($bill) > 0 && count($paymenttype) > 0){
            $this->load->model('finance/mdl_finance');

            //Post Journal
            //Sales
            // AR Personal
            $detail = array();
            $totalDebit = $bill['total_amount'];
            $totalCredit = 0;
            if($totalDebit > 0){
                //Sales
                $sales = FNSpec::get(FNSpec::SALES_RESERVATION);
                if ($sales['coa_id'] > 0) {
                    /*
                    $ar_bridging_coa_id = 0;
                    if($paymenttype['coa_code'] != ''){
                        $coa = $this->db->get_where('gl_coa',array('coa_code' => $paymenttype['coa_code']));
                        if($coa->num_rows() > 0){
                            $ar_bridging_coa_id = $coa->row()->coa_id;
                        }
                    }
                    */
                    $rowdet = array();
                    $rowdet['coa_id'] = $sales['coa_id'];
                    $rowdet['dept_id'] = 0;
                    $rowdet['journal_note'] = $bill['remark'];
                    $rowdet['journal_debit'] = $totalDebit;
                    $rowdet['journal_credit'] = 0;
                    $rowdet['reference_id'] = 0;
                    $rowdet['transtype_id'] = $sales['transtype_id'];

                    array_push($detail, $rowdet);

                    $totalCredit = $totalDebit;
                }
            }else{
                $valid = false;
            }

            //echo '\nA ...' . $valid . ' => ' . $totalCredit . ' == ' .  $totalDebit;

            if($valid){
                if($totalCredit > 0 && $totalCredit == $totalDebit && count($detail) > 0){
                    //AR Corporate
                    $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                    if($ar['coa_id'] > 0){
                        $rowdet = array();
                        $rowdet['coa_id'] = $ar['coa_id'];
                        $rowdet['dept_id'] = 0;
                        $rowdet['journal_note'] = $bill['remark'];
                        $rowdet['journal_debit'] = 0;
                        $rowdet['journal_credit'] = $totalCredit;
                        $rowdet['reference_id'] = 0;
                        $rowdet['transtype_id'] = $ar['transtype_id'];

                        array_push($detail, $rowdet);
                    }
                }else{
                    $valid = false;
                }
            }

            if($valid && $totalDebit == $totalCredit && $totalDebit > 0 ){
                $header = array();
                $header['journal_no'] = $bill['bill_no'];
                $header['journal_date'] = $bill['bill_startdate'];
                $header['journal_remarks'] = $paymenttype['paymenttype_desc'] . ' - ' . $bill['remark'];
                $header['modul'] = GLMOD::GL_MOD_AR;
                $header['journal_amount'] = $totalDebit;
                $header['reference'] = '';
                //$header['reference_date'] = $reservation['reservation_date'];

                $valid = $this->mdl_finance->postJournal($header,$detail);
            }
        }

        return $valid;
    }

    #endregion

    #region Guest

    public function guest_manage($type = 1){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/admin/layout/css/custom.css');

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
        $this->load->view('frontdesk/management/guest_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function guest_history($type = 1){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/admin/layout/css/custom.css');

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
        $this->load->view('frontdesk/management/guest_history.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_guest_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $whereIn = array(ORDER_STATUS::CHECKIN);

        $like = array();
        $whereString = "";
        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,view_cs_reservation.reservation_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,view_cs_reservation.reservation_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                //$whereString = "view_cs_reservation.tenant_fullname like '%" . $_REQUEST['filter_company'] . "%' " ;
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company'];
            }
        }
        if(isset($_REQUEST['filter_room'])){
            if($_REQUEST['filter_room'] != ''){
                $like['view_cs_reservation.room'] = $_REQUEST['filter_room'];
            }
        }
        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $like['view_cs_reservation.reservation_type'] = $_REQUEST['filter_type'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $like['view_cs_reservation.status'] = $_REQUEST['filter_status'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('view_cs_reservation', $joins, $where, $like, 'view_cs_reservation.status', $whereIn, $whereString);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'view_cs_reservation.reservation_code DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'view_cs_reservation.reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'view_cs_reservation.room ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('view_cs_reservation.*','view_cs_reservation', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, 'view_cs_reservation.status', $whereIn, $whereString);

        //$records["debug2"] = $this->db->last_query();

        //$today = new DateTime('');
        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            //$arrival_date = new DateTime();

            $rollback_enabled = false;
            $qry = $this->db->query('SELECT * FROM fxnCS_ReservationLedger(' . $row->reservation_id .') WHERE type NOT IN(1,2) ');
            if($qry->num_rows() <= 0){
                $interval = num_of_days(ymd_from_db($row->arrival_date), date('Y-m-d'), RENT_BY_NIGHT);
                if($interval <= 1){
                    $rollback_enabled = true;
                }
            }

            $btn_action = '';
            //$btn_action .= '<li> <a href="javascript:;" class="btn-view" data-id="' . $row->reservation_id . '">' . 'View' . '</a> </li>';
            $btn_action .= '<li> <a href="' . base_url('frontdesk/management/guest_form/' . $row->reservation_id) . '.tpd"><i class="fa fa-file"></i>&nbsp;Open</a> </li>';
            $btn_action .= '<li> <a href="' . base_url('frontdesk/reservation/pdf_reservation/' . $row->reservation_id) . '.tpd" target="_blank"><i class="fa fa-print"></i>&nbsp;Registration Form</a> </li>';
            $btn_action .= '<li> <a href="' . base_url('frontdesk/reservation/pdf_reservation_family/' . $row->reservation_id) . '.tpd" target="_blank"><i class="fa fa-print"></i>&nbsp;Family Registration</a> </li>';
                    $btn_action .= '<li> <a href="' . base_url('frontdesk/reservation/pdf_reservation_staff/' . $row->reservation_id) . '.tpd" target="_blank"><i class="fa fa-print"></i>&nbsp;Staff Registration</a> </li>';
            if($rollback_enabled){
                $paymentAmount = 0; //($row->payment_amount + $row->deposit_amount);
                if(check_controller_action('frontdesk','management',STATUS_DELETE) && $paymentAmount <= 0){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-rollback" data-id="' . $row->reservation_id . '"><i class="fa fa-remove font-red"></i>&nbsp;' . 'Rollback' . '</a> </li>';
                }
            }

            $res_caption = '<i class="' . RES_TYPE::css_icon($row->reservation_type) . ' tooltips" data-original-title="' . RES_TYPE::caption($row->reservation_type) . '"></i>';

            $class_action = ' green-meadow';
            $departure_date = new DateTime(ymd_from_db($row->departure_date));
            $today = new DateTime('');
            if($departure_date->format('Y-m-d') == $today->format('Y-m-d')){
                $class_action = ' yellow';
            }else if($departure_date->format('Y-m-d') < $today->format('Y-m-d')){
                $class_action = ' red-sunglo';
            }

            $status_caption = '<span class="badge badge-primary tooltips " data-original-title="' . ORDER_STATUS::get_status_name($row->status, false) . '">' . ORDER_STATUS::code($row->status) . '</span>';

            $records["data"][] = array(
                $i,
                $row->reservation_code,
                $res_caption,
                ($row->hidden_me > 0 ? '<i class="font-purple-studio">' . INCOGNITO . '</i>' : $row->tenant_fullname),
                $row->company_name,
                $row->room,
                //dmy_from_db($row->arrival_date),
                //dmy_from_db($row->departure_date),
                date('d-m-Y H:i:s',strtotime($row->checkin_date)),
                dmy_from_db($row->departure_date),
                '<div class="btn-group">
                    <button class="btn ' . $class_action . ' btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
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

    public function get_guest_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $whereIn = array(ORDER_STATUS::CHECKOUT);

        $like = array();
        $whereString = "";
        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,view_cs_reservation.reservation_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,view_cs_reservation.reservation_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                //$whereString = "view_cs_reservation.tenant_fullname like '%" . $_REQUEST['filter_name'] . "%' " ;
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                //$whereString = "view_cs_reservation.tenant_fullname like '%" . $_REQUEST['filter_name'] . "%' " ;
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company'];
            }
        }
        if(isset($_REQUEST['filter_room'])){
            if($_REQUEST['filter_room'] != ''){
                $like['view_cs_reservation.room'] = $_REQUEST['filter_room'];
            }
        }
        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $like['view_cs_reservation.reservation_type'] = $_REQUEST['filter_type'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $like['view_cs_reservation.status'] = $_REQUEST['filter_status'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('view_cs_reservation', $joins, $where, $like, 'view_cs_reservation.status', $whereIn, $whereString);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'view_cs_reservation.reservation_code DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'view_cs_reservation.reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'view_cs_reservation.room ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'view_cs_reservation.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('view_cs_reservation.*','view_cs_reservation', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, 'view_cs_reservation.status', $whereIn, $whereString);

        //$records["debug2"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            //$btn_action .= '<li> <a href="javascript:;" class="btn-view" data-id="' . $row->reservation_id . '">' . 'View' . '</a> </li>';
            $btn_action .= '<li> <a href="' . base_url('frontdesk/management/guest_form/' . $row->reservation_id) . '/1.tpd"><i class="fa fa-file"></i>&nbsp;Open</a> </li>';

            //$res_caption = strtoupper(RES_TYPE::caption($row->reservation_type));
            //$res_caption = '<span class="' . RES_TYPE::css_class($row->reservation_type). '">'. $res_caption . '</span>';

            $res_caption = '<i class="' . RES_TYPE::css_icon($row->reservation_type) . ' tooltips" data-original-title="' . RES_TYPE::caption($row->reservation_type) . '"></i>';

            $records["data"][] = array(
                $i,
                $row->reservation_code,
                $res_caption,
                ($row->hidden_me > 0 ? '<i class="font-purple-studio">'. INCOGNITO .'</i>' : $row->tenant_fullname),
                $row->company_name,
                $row->room,
                date('d-m-Y H:i:s',strtotime($row->checkin_date)),
                date('d-m-Y H:i:s',strtotime($row->checkout_date)),
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

    public function guest_form($reservation_id = 0, $isHistory = false, $isBill = 0){
        $this->load->model('frontdesk/mdl_frontdesk');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/admin/pages/css/invoice.css');
        array_push($data_header['style'], base_url() . 'assets/admin/layout/css/custom.css');

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
        array_push($data_header['style'], base_url() . 'assets/global/plugins/fancybox/source/jquery.fancybox.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/fancybox/source/jquery.fancybox.pack.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/util.js');

        $data['reservation_id'] = $reservation_id;

        $guest_form = "guest_form";
        $valid = true;
        if($reservation_id > 0){
            $qry = $this->db->get_where('view_cs_reservation', array('reservation_id' => $reservation_id));
            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                if($data['row']->tenant_id > 0){
                    $tenant = $this->db->get_where('ms_tenant', array('tenant_id' => $data['row']->tenant_id));
                    $data['tenant'] = $tenant->row();
                }else{
                    $tenant = $this->db->get_where('tmp_tenant', array('reservation_id' => $reservation_id));
                    $data['tenant'] = $tenant->row();
                }

                if($data['row']->company_id > 0){
                    $company = $this->db->get_where('ms_company', array('company_id' => $data['row']->company_id));
                    $data['company'] = $company->row();
                }

                if($data['row']->agent_id > 0){
                    $agent = $this->db->get_where('ms_agent', array('agent_id' => $data['row']->agent_id));
                    $data['agent'] = $agent->row();
                }

                $unit_prices = array();

                //CALC
                $qry = $this->db->query('select distinct cs_reservation_detail.unit_id, ms_unit.unit_code, ms_unit.unittype_id, ms_unit_type.unittype_desc
                                         from cs_reservation_detail
                                         join cs_reservation_header on cs_reservation_header.reservation_id = cs_reservation_detail.reservation_id
                                         join ms_unit on ms_unit.unit_id = cs_reservation_detail.unit_id
                                         join ms_unit_type on ms_unit_type.unittype_id = ms_unit.unittype_id
                                         where cs_reservation_detail.reservation_id = ' . $reservation_id );

                if($qry->num_rows() > 0){
                    $calc_array = array();
                    foreach($qry->result_array() as $unit){
                        $calc = $this->mdl_frontdesk->calculate_booking($unit['unittype_id'], dmy_from_db($data['row']->arrival_date), dmy_from_db($data['row']->departure_date), $data['row']->reservation_type, $data['row']->is_rate_yearly, $data['row']->billing_type);

                        array_push($calc_array, array('unit_id'=> $unit['unit_id'],'unit_code'=>$unit['unit_code'],'unittype_desc' => $unit['unittype_desc'], 'calc_data' => $calc));
                    }

                    if(count($calc_array) > 0){
                        //Set Duration
                        $iMonth = $calc_array[0]['calc_data']['monthly_count'];
                        $iDay = $calc_array[0]['calc_data']['daily_count'];

                        $duration = '';
                        if( $iMonth > 0 ){
                            $duration .= ($iMonth > 1) ? $iMonth . ' months ' : $iMonth . ' month';
                        }

                        if($iDay > 0){
                            if($iMonth > 0){
                                $duration .= ' and ';
                            }

                            $duration .= $iDay; //($iDay > 1) ? $iDay . ' days ' : $iDay . ' day';
                        }
                        $data['room_duration'] = $calc_array[0]['calc_data']['period_caption'];//$duration;

                        if($data['row']->status == ORDER_STATUS::RESERVED ||
                            $data['row']->status == ORDER_STATUS::ONLINE_VALID ||
                            $data['row']->status == ORDER_STATUS::ONLINE_NEW){
                            //Split Duration of Unit by months and days
                            foreach($calc_array as $arr){
                                $calc = $arr['calc_data'];
                                if($calc['yearly_count'] > 0){
                                    array_push($unit_prices, array('billdetail_id' => 0, 'unit_id'=> $arr['unit_id'],'unit_code'=>$arr['unit_code'],'unittype_desc' => $unit['unittype_desc'], 'duration' => $calc['yearly_count'], 'is_monthly' => BILLING_BASE::YEARLY , 'rate' => $calc['yearly_rate'], 'local_amount' => $calc['yearly_amount'], 'bill_start' => $calc['yearly_period_start'], 'bill_end' => $calc['yearly_period_end'], 'transtype_id' => $calc['transtype_id']));
                                }
                                if($calc['monthly_count'] > 0){
                                    array_push($unit_prices, array('billdetail_id' => 0, 'unit_id'=> $arr['unit_id'],'unit_code'=>$arr['unit_code'],'unittype_desc' => $unit['unittype_desc'], 'duration' => $calc['monthly_count'], 'is_monthly' => BILLING_BASE::MONTHLY , 'rate' => $calc['monthly_rate'], 'local_amount' => $calc['monthly_amount'], 'bill_start' => $calc['monthly_period_start'], 'bill_end' => $calc['monthly_period_end'], 'transtype_id' => $calc['transtype_id']));
                                }
                                if($calc['daily_count'] > 0){
                                    array_push($unit_prices, array('billdetail_id' => 0,'unit_id'=> $arr['unit_id'],'unit_code'=>$arr['unit_code'],'unittype_desc' => $unit['unittype_desc'], 'duration' => $calc['daily_count'], 'is_monthly' => 0 , 'rate' => $calc['daily_rate'], 'local_amount' => $calc['daily_amount'], 'bill_start' => $calc['daily_period_start'], 'bill_end' => $calc['daily_period_end'], 'transtype_id' => $calc['transtype_id']));
                                }
                            }
                        }else{
                            //LOOK FROM DB
                            $bill_detail = $this->db->query('SELECT cs_bill_detail.*, ms_unit.unit_code, ms_unit_type.unittype_desc
                                         FROM cs_bill_detail
                                         JOIN cs_bill_header ON cs_bill_header.bill_id = cs_bill_detail.bill_id
                                         LEFT JOIN ms_unit on ms_unit.unit_id = cs_bill_detail.unit_id
                                         LEFT JOIN ms_unit_type on ms_unit.unittype_id = ms_unit_type.unittype_id
                                         WHERE cs_bill_detail.reservation_id = ' . $reservation_id .' AND cs_bill_detail.item_id <= 0 AND ISNULL(cs_bill_header.is_hsk,0) <= 0 AND cs_bill_header.status IN(' . STATUS_POSTED .',' . STATUS_CLOSED. ')
                                         ORDER BY cs_bill_detail.billdetail_id');
                            if($bill_detail->num_rows() > 0){
                                foreach($bill_detail->result_array() as $arr){
                                    $room_duration = '';
                                    if($arr['year_interval'] > 0){
                                        $room_duration .= $arr['year_interval'] . 'Y';
                                    }

                                    if($arr['month_interval'] > 0){
                                        if($arr['year_interval'] > 0){
                                            $room_duration .= ' ' . $arr['month_interval'] . 'M';
                                        }else{
                                            $room_duration .= $arr['month_interval'] . 'M';
                                        }
                                    }

                                    array_push($unit_prices, array('billdetail_id' => $arr['billdetail_id'], 'unit_id'=> $arr['unit_id'],'unit_code'=>$arr['unit_code'],'unittype_desc' => $arr['unittype_desc'], 'duration' => $room_duration, 'is_monthly' =>$arr['is_monthly'] , 'rate' => $arr['rate'],'transtype_id' => $arr['transtype_id'],
                                        'bill_start' => dmy_from_db($arr['date_start']), 'bill_end' => dmy_from_db($arr['date_end']),
                                        'local_amount' => $arr['amount'], 'tax_amount' => $arr['tax'],'discount_amount' => $arr['disc_amount'],
                                        'subtotal' => ($arr['amount'] + $arr['tax'] - $arr['disc_amount']), 'status' => $arr['status']
                                    ));
                                }
                            }
                        }
                    }
                }

                //Get Reservation Ledger
                //1=room_charge, 2=official_receipt, 3=other_charge, 4=refund
                $ledger = $this->db->query('SELECT * FROM fxnCS_ReservationLedger(' . $reservation_id . ') ORDER BY order_date,type ');
                if($ledger->num_rows() > 0){
                    $data['ledger'] = $ledger->result_array();
                }else{
                    $data['ledger'] = array();
                }

                $data['unit_rates'] = $unit_prices;
                $data['reservation_type'] = $data['row']->reservation_type;

                $adjusts = array();

                $pending_amount = 0;
                if(isset($data['ledger'])) {
                    foreach ($data['ledger'] as $row) {
                        if ($row['debit'] > 0) {
                            $pending_amount += $row['debit'];
                        } else {
                            $pending_amount -= $row['credit'];
                        }

                        $balance = ($row['debit'] - $row['credit']);
                        if ($row['type'] == 4) {
                            //REFUND
                            array_push($adjusts, array('adjust_date' => $row['trx_date'], 'adjust_type' => 'Refund', 'adjust_remark' => $row['remark'], 'adjust_amount' => $balance, 'is_primary_debtor' => 1));
                        } else if ($row['type'] == 5) {
                            //CREDIT NOTE
                            array_push($adjusts, array('adjust_date' => $row['trx_date'], 'adjust_type' => 'Credit Note', 'adjust_remark' => $row['remark'], 'adjust_amount' => $balance, 'is_primary_debtor' => $row['is_primary_debtor']));
                        } else if ($row['type'] == 6) {
                            //DEBIT NOTE
                            array_push($adjusts, array('adjust_date' => $row['trx_date'], 'adjust_type' => 'Debit Note', 'adjust_remark' => $row['remark'], 'adjust_amount' => $balance, 'is_primary_debtor' => 1));
                        }
                    }
                }

                //CALCULATE PAYMENT CORPORATE (COMPANY & GUEST)
                $pending_amount_primary = 0;
                $pending_amount_other = 0;

                $sum_guest = $this->db->query("SELECT ISNULL(SUM(unpaid_grand),0) as sum_unpaid from fxnARInvoiceHeaderByStatus(" . STATUS_POSTED . ") where reservation_id = " . $data['row']->reservation_id);
                if($data['row']->reservation_type == RES_TYPE::CORPORATE){
                    $sum_company = $this->db->query("SELECT ISNULL(SUM(unpaid_grand),0) as sum_unpaid from fxnARInvoiceHeaderByStatus(" . STATUS_POSTED . ") where company_id = " . $data['row']->company_id);
                    if($sum_company->num_rows() > 0){
                        $pending_amount_primary = $sum_company->row()->sum_unpaid;
                    }

                    if($sum_guest->num_rows() > 0){
                        $pending_amount_other = $sum_guest->row()->sum_unpaid;
                        $pending_amount = $sum_guest->row()->sum_unpaid;
                    }
                }else{
                    if($sum_guest->num_rows() > 0){
                        $pending_amount_primary = $sum_guest->row()->sum_unpaid;
                        $pending_amount = $sum_guest->row()->sum_unpaid;
                    }
                }

                //Wifi
                $wifis = $this->db->get_where('cs_reservation_wifi', array('reservation_id' => $reservation_id, 'status' => STATUS_NEW));
                if($wifis->num_rows() > 0){
                    $data['wifi'] = $wifis->row();
                }

                $data['adjustments'] = $adjusts;
                $data['invoice_amount'] = round($pending_amount,0); //($amountBill - abs($amountReceipt));
                $data['pending_amount_primary'] = round($pending_amount_primary,0);
                $data['pending_amount_other'] = round($pending_amount_other,0);

                if($data['row']->reservation_type == RES_TYPE::CORPORATE){
                    $guest_form = "guest_form_corp";

                    $data['invoice_amount'] = 0;
                    $data['pending_amount_primary'] = 0;

                    //Get Payments
                    $pay_qry = $this->db->query('select distinct ar_allocation_header.alloc_no, ar_receipt.receipt_id, ar_receipt.receipt_no, ar_allocation_header.alloc_amount, ar_receipt.receipt_date, ar_receipt.paymenttype_id , ms_payment_type.paymenttype_code, ms_payment_type.paymenttype_desc from ar_allocation_header
                                             join ar_receipt_allocation ON ar_allocation_header.allocationheader_id = ar_receipt_allocation.allocationheader_id
                                             join ar_receipt ON ar_receipt_allocation.receipt_id = ar_receipt.receipt_id
                                             join gl_postjournal_header ON gl_postjournal_header.journal_no = ar_receipt.receipt_no
                                             left join ms_payment_type on ms_payment_type.paymenttype_id = ar_receipt.paymenttype_id
                                             left join ar_invoice_detail on ar_invoice_detail.invdetail_id = ar_receipt_allocation.invdetail_id
                                             join cs_corporate_bill on ar_invoice_detail.bill_id = cs_corporate_bill.corporatebill_id
                                             where ar_receipt.company_id = ' . $data['row']->company_id .' and cs_corporate_bill.reservation_id = ' . $reservation_id . ' and ar_receipt.status IN(' . STATUS_POSTED .',' . STATUS_CLOSED. ')
                                             order by ar_allocation_header.alloc_no
                                             ');

                    $amountReceipt = 0;
                    $payments = array();
                    if($pay_qry->num_rows() > 0){
                        foreach($pay_qry->result_array() as $pay){
                            $pay_amount = ($pay['alloc_amount']) * -1;
                            array_push($payments, array('bookingreceipt_id' => $pay['receipt_id'],
                                'bookingreceipt_date' => $pay['receipt_date'],
                                'paymenttype_id' => $pay['paymenttype_id'],
                                'paymenttype_code' => $pay['paymenttype_code'] . ' - ' . $pay['paymenttype_desc'],
                                'amount' => $pay_amount, 'is_primary_debtor' => 1,
                                'doc_no' => $pay['receipt_no'],
                                'doc_link' => base_url('ar/report/pdf_official_receipt/' . $pay['receipt_no']) . '.tpd'
                            ));
                            $amountReceipt += $pay_amount;
                        }
                    }

                    //Get Payments for Guest of Corporate
                    $pay_qry = $this->db->query('select ar_receipt.*, ms_payment_type.paymenttype_code, ms_payment_type.paymenttype_desc from ar_receipt
                                             left join ms_payment_type on ms_payment_type.paymenttype_id = ar_receipt.paymenttype_id
                                             where ar_receipt.reservation_id = ' . $data['reservation_id'] .' and ar_receipt.status IN(' . STATUS_POSTED .',' . STATUS_CLOSED. ')
                                             order by ar_receipt.receipt_date');

                    if($pay_qry->num_rows() > 0){
                        foreach($pay_qry->result_array() as $pay){
                            $pay_amount = ($pay['receipt_bankcharges'] + $pay['receipt_paymentamount']) * -1;
                            array_push($payments, array('bookingreceipt_id' => $pay['receipt_id'],
                                'bookingreceipt_date' => $pay['receipt_date'],
                                'paymenttype_id' => $pay['paymenttype_id'],
                                'paymenttype_code' => $pay['paymenttype_code'] . ' - ' . $pay['paymenttype_desc'],
                                'amount' => $pay_amount, 'is_primary_debtor' => 0,
                                'doc_no' => $pay['receipt_no'],
                                'doc_link' => base_url('ar/report/pdf_official_receipt/' . $pay['receipt_no']) . '.tpd'
                            ));
                            $amountReceipt += $pay_amount;
                        }
                    }

                    if(count($payments) > 0)
                        $data['payments'] = $payments;

                    //Deposit
                    $deposits = $this->db->query('SELECT depot.*, ms_tenant.tenant_fullname FROM fxnCS_DepositLedger(' . $reservation_id . ') as depot
                        LEFT JOIN ms_tenant ON ms_tenant.tenant_id = depot.tenant_id
                        ORDER BY depot.order_date ');
                    if($deposits->num_rows() > 0){
                        $data['deposits'] = $deposits->result_array();
                    }

                    //UNDEPOSIT
                    $unallocatedDeposit = 0;
                    /*
                    $undeposit = $this->db->query('SELECT depot.available_deposit FROM fxnARUnDepositByDateCorp(getdate()) as depot
                       JOIN cs_reservation_header cs ON cs.reservation_id = depot.reservation_id
                       WHERE depot.reservation_id = ' . $reservation_id . ' AND cs.tenant_id = ' . $data['row']->tenant_id);
                    if($undeposit->num_rows() > 0){
                        $undeposit = $undeposit->row();
                        $unallocatedDeposit = $undeposit->available_deposit;
                    }*/
                    $data['undeposit_amount'] = $unallocatedDeposit;

                }else{
                    $guest_form = "guest_form";

                    //Get Payments
                    $pay_qry = $this->db->query('select ar_receipt.*, ms_payment_type.paymenttype_code, ms_payment_type.paymenttype_desc from ar_receipt
                                             left join ms_payment_type on ms_payment_type.paymenttype_id = ar_receipt.paymenttype_id
                                             where ar_receipt.reservation_id = ' . $data['reservation_id'] .' and ar_receipt.status IN(' . STATUS_POSTED .',' . STATUS_CLOSED. ')
                                             order by ar_receipt.receipt_date');

                    $amountReceipt = 0;
                    $payments = array();
                    if($pay_qry->num_rows() > 0){
                        foreach($pay_qry->result_array() as $pay){
                            $pay_amount = ($pay['receipt_bankcharges'] + $pay['receipt_paymentamount']) * -1;
                            array_push($payments, array('bookingreceipt_id' => $pay['receipt_id'],
                                'bookingreceipt_date' => $pay['receipt_date'],
                                'paymenttype_id' => $pay['paymenttype_id'],
                                'paymenttype_code' => $pay['paymenttype_code'] . ' - ' . $pay['paymenttype_desc'],
                                'amount' => $pay_amount, 'is_primary_debtor' => 1,
                                'doc_no' => $pay['receipt_no'],
                                'doc_link' => base_url('ar/report/pdf_official_receipt/' . $pay['receipt_no']) . '.tpd'
                            ));
                            $amountReceipt += $pay_amount;
                        }
                    }

                    if(count($payments) > 0)
                        $data['payments'] = $payments;

                    //Deposit
                    $deposits = $this->db->query('SELECT * FROM fxnCS_DepositLedger(' . $reservation_id . ') WHERE tenant_id = ' . $data['row']->tenant_id . ' ORDER BY order_date ');
                    if($deposits->num_rows() > 0){
                        $data['deposits'] = $deposits->result_array();
                    }
                }
            }else{
                $valid = false;
            }
        }

        if($valid){
            if($isHistory){
                $data['back_url'] = base_url('frontdesk/management/guest_history/1.tpd');
            }else{
                $data['back_url'] = base_url('frontdesk/management/guest_manage/1.tpd');
            }

            if($isBill > 0){
                $data['back_url'] = base_url('ar/corporate_bill/bill_manage.tpd');
            }

            $this->load->view('layout/header', $data_header);
            $this->load->view('frontdesk/management/' . $guest_form, $data);
            $this->load->view('layout/footer');
        }else{
            tpd_404();
        }

    }

    //Process Checkout
    public function submit_checkout(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';
        $result['debug'] = '' ;

        $reservationId = $_POST['reservation_id'];
        $checkoutDate = date('Y-m-d H:i:s');

        $result['redirect_link'] = base_url('frontdesk/management/guest_form/' . $reservationId .'.tpd');

        if($reservationId > 0){
            //Check Pending Other Charges
            $reservation_type = RES_TYPE::PERSONAL;
            $tenant_id = 0;
            $reservation = $this->db->query('SELECT reservation_id, reservation_type, tenant_id FROM cs_reservation_header WHERE reservation_id = ' . $reservationId);
            if($reservation->num_rows() > 0){
                $row_srv = $reservation->row();

                $reservation_type = $row_srv->reservation_type;
                $tenant_id = $row_srv->tenant_id;
            }

            //---- Start update by syaiful on 22 jan 16
            /*if($reservation_type == RES_TYPE::CORPORATE){
                $countPending = $this->mdl_general->count('cs_bill_header',array('reservation_id' => $reservationId, 'company_id > ' => 0, 'status' => STATUS_NEW));
            }else{
                $countPending = $this->mdl_general->count('cs_bill_header',array('reservation_id' => $reservationId, 'status' => STATUS_NEW));
            }*/

            //$countPending = $this->mdl_general->count('cs_corporate_bill',array('reservation_id' => $reservationId, 'tenant_id' => $tenant_id));
            $countPending = $this->mdl_general->count('fxnCheckOut_Reservation(' . $reservationId . ',' . $tenant_id . ')');

            //---- End update by syaiful on 22 jan 16

            if($countPending > 0){
                $result['type'] = '0';
                $result['message'] = 'Pending bills found ! (Please create invoice).';

                $this->session->set_flashdata('flash_message_class', 'warning');
                $this->session->set_flashdata('flash_message', 'Pending bills exist ! Check Out can not be processed.');
            }else{
                $valid = true;
                if($row_srv->reservation_type == RES_TYPE::CORPORATE){
                    /*
                    $unallocatedDeposit = 0;
                    $undeposit = $this->db->query('SELECT depot.available_deposit FROM fxnARUnDepositByDateCorp(getdate()) as depot
                       JOIN cs_reservation_header cs ON cs.reservation_id = depot.reservation_id
                       WHERE depot.reservation_id = ' . $row_srv->reservation_id . ' AND cs.tenant_id = ' . $row_srv->tenant_id);
                    if($undeposit->num_rows() > 0){
                        $undeposit = $undeposit->row();
                        $unallocatedDeposit = $undeposit->available_deposit;
                        if($unallocatedDeposit > 0){
                            $valid = false;
                        }
                    }*/
                }

                if($valid) {
                    //Remove wifi voucher
                    @$this->removeWifiVoucher(array('reservation_id' => $reservationId));

                    //BEGIN TRANSACTION
                    $this->db->trans_begin();

                    $data['checkout_date'] = $checkoutDate;
                    $data['modified_by'] = my_sess('user_id');;
                    $data['modified_date'] = date('Y-m-d H:i:s');
                    $data['status'] = ORDER_STATUS::CHECKOUT;

                    //Update Reservation
                    $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $reservationId), $data);
                    $this->mdl_general->update('cs_reservation_detail', array('reservation_id' => $reservationId, 'CONVERT(date,checkin_date) <=' => date('Y-m-d')), array('status' => ORDER_STATUS::CHECKOUT));
                    $this->mdl_general->update('cs_reservation_detail', array('reservation_id' => $reservationId, 'CONVERT(date,checkin_date) >' => date('Y-m-d')), array('status' => STATUS_CANCEL, 'close_status' => STATUS_CANCEL));

                    $this->mdl_general->update('cs_sales_close', array('reservation_id' => $reservationId, 'close_status >' => 0, 'CONVERT(date,close_date) <=' => date('Y-m-d')), array('status' => ORDER_STATUS::CHECKOUT));

                    $this->mdl_general->update('cs_sales_close', array('reservation_id' => $reservationId, 'close_status <=' => 0, 'CONVERT(date,close_date) >' => date('Y-m-d')), array('status' => STATUS_CANCEL, 'close_status' => STATUS_CANCEL));

                    //Update Housekeeping Status
                    $rsvt = $reservation->row_array();
                    $this->update_hsk_status($rsvt, true, date('Y-m-d'));

                    //Update Bill->Closed
                    $this->mdl_general->update('cs_bill_header', array('reservation_id' => $reservationId), array('status' => STATUS_CLOSED));
                    $this->mdl_general->update('cs_bill_detail', array('reservation_id' => $reservationId), array('status' => STATUS_CLOSED));

                    //Update CS Corporate Bill > checkout date
                    //$this->mdl_general->update('cs_corporate_bill', array('reservation_id' => $reservationId), array('status'=>STATUS_CLOSED));
                    $this->db->delete('cs_corporate_bill', array('reservation_id' => $reservationId, 'tenant_id >' => 0, 'bill_startdate >' => date('Y-m-d'), 'is_billed <= ' => 0, 'ISNULL(is_othercharge,0) <= ' => 0));

                    $this->db->delete('cs_reservation_wifi', array('reservation_id' => $reservationId));

                    //FINALIZE TRANSACTION
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();

                        $result['type'] = '0';
                        $result['message'] = 'Check Out can not be processed.';

                        $this->session->set_flashdata('flash_message_class', 'error');
                        $this->session->set_flashdata('flash_message', 'Check Out can not be processed ! Please try again later.');
                    } else {
                        if ($valid) {
                            $this->db->trans_commit();

                            $result['type'] = '1';
                            $result['message'] = 'Check Out successfully submitted.';

                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Check Out successfully recorded.');
                        } else {
                            $this->db->trans_rollback();

                            $result['type'] = '0';
                            $result['message'] = 'Check Out can not be processed.';

                            $this->session->set_flashdata('flash_message_class', 'error');
                            $this->session->set_flashdata('flash_message', 'Check Out can not be processed ! Please try again later.');
                        }
                    }
                }else{
                    $result['type'] = '0';
                    $result['message'] = 'Guest Deposit found.';

                    $this->session->set_flashdata('flash_message_class', 'error');
                    $this->session->set_flashdata('flash_message', 'Guest Deposit found! Please refund or allocate to continue.');
                }
            }
        }

        echo json_encode($result);
    }

    public function removeWifiVoucher($reservation = array()){
        $data = array();

        $data['valid'] = 1;
        $data['debug'] = '-';

        if(isset($reservation)){
            try{
                //Old Wifi user
                $prevWifiId = '';
                $currentWifi = $this->db->get_where('cs_reservation_wifi', array('reservation_id' => $reservation['reservation_id']));
                if($currentWifi->num_rows() > 0) {
                    $currentWifi = $currentWifi->row();
                    $prevWifiId = $currentWifi->router_id;
                }

                if(trim($prevWifiId) != ''){
                    require_once APPPATH . 'third_party/routeros/routeros_api.class.php';

                    $API = new RouterosAPI();
                    $API->debug = false;

                    //if ($API->connect(WIFI_ROUTER::SERVER_IP, 'IT', 'dwijaya2015'))
                    if ($API->connect(WIFI_ROUTER::SERVER_IP, WIFI_ROUTER::SERVER_USER, WIFI_ROUTER::SERVER_PASS))
                    {
                        $deleted = $API->comm('/tool/user-manager/user/remove', array(
                                ".id" => $prevWifiId
                            )
                        );

                        //var_dump($response);

                        //$READ = $API->read(false);
                        //$ARRAY = $API->parse_response($READ);

                        //print_r($ARRAY);
                        $API->disconnect();

                        $data['valid'] = 1;
                        $data['debug'] = 'Wifi voucher removed';

                        //$data['debug'] = $ARRAY;
                    } else {
                        $data['valid'] = 0;
                        $data['debug'] = 'Can not connect to wifi router.';
                    }
                }
            }catch(Exception $e){
                if(isset($API)){
                    $API->disconnect();
                }

                $data['valid'] = 0;
                $data['debug'] = $e;
            }
        }else{
            $data['valid'] = 0;
            $data['debug'] = 'Reservation not found';
        }

        return $data;
    }

    public function xchange_checkin_room(){
        $result = array();
        $result['valid'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';
        $result['debug'] = '';

        $reservationId = $_POST['reservation_id'];
        $billdetail_id = $_POST['reff_bill_detail'];
        $unit_id = $_POST['unit_id'];
        $start_date = $_POST['change_date'];

        $result['redirect_link'] = base_url('frontdesk/management/guest_form/' . $reservationId .'.tpd');

        if($reservationId > 0 && $billdetail_id > 0){
            $this->load->model('frontdesk/mdl_frontdesk');

            //Re-calculate current bill
            $billdetail = $this->db->query("SELECT bill.*, unit.unittype_id, res.reservation_type, res.departure_date, res.agent_id FROM cs_bill_detail bill
                                            JOIN cs_reservation_header res ON res.reservation_id = bill.reservation_id
                                            JOIN ms_unit unit ON unit.unit_id = bill.unit_id
                                            WHERE bill.billdetail_id = " . $billdetail_id . " AND bill.reservation_id = " . $reservationId . "
                                            ORDER BY bill.reff_billdetail_id DESC ");

            if($billdetail->num_rows() > 0){
                $billdetail = $billdetail->row();

                $valid = true;

                //BEGIN TRANSACTION
                $this->db->trans_begin();

                try{
                    //Remove wifi voucher
                    @$this->removeWifiVoucher(array('reservation_id' => $reservationId));

                    $valid = $this->update_hsk_by_unit($billdetail->unit_id, true, $start_date, $billdetail->reservation_id,'','Change Room');

                    /*
                    if(ymd_from_db($billdetail->modified_date) != $start_date){
                        //UPDATE hsk status
                    }else{
                        $this->mdl_general->update('ms_unit', array('unit_id' => $billdetail->unit_id), array('hsk_status' => HSK_STATUS::IS));
                    }
                    */

                    //UPDATE cs_reservation_detail > change_date
                    if($valid){
                        //UPDATE res detail
                        $this->mdl_general->update('cs_reservation_detail',
                            array('reservation_id' => $reservationId, 'unit_id'=> $billdetail->unit_id, 'CONVERT(date,checkin_date) >=' => $start_date),
                            array('unit_id' => $unit_id));
                    }

                    //UPDATE cs_reservation_unit
                    if($valid){
                        //UPDATE res detail
                        $this->mdl_general->update('cs_reservation_unit',
                            array('reservation_id' => $reservationId, 'unit_id'=> $billdetail->unit_id),
                            array('unit_id' => $unit_id));
                    }

                    //UPDATE cs_corporate_bill
                    if($valid){
                        $this->mdl_general->update('cs_corporate_bill',
                                array('reservation_id' => $reservationId, 'unit_id' => $billdetail->unit_id, 'is_othercharge <= ' => 0),
                                array('unit_id' => $unit_id));
                    }

                    //UPDATE cs_reservation_package
                    if($valid){
                        //UPDATE res detail
                        //$this->mdl_general->update('cs_reservation_package',
                        //    array('reservation_id' => $reservationId, 'unit_id'=> $billdetail->unit_id),
                        //    array('unit_id' => $unit_id));
                    }

                    //UPDATE cs_reservation_wifi
                    if($valid){
                        //UPDATE res detail
                        $this->mdl_general->update('cs_reservation_wifi',
                            array('reservation_id' => $reservationId, 'unit_id'=> $billdetail->unit_id),
                            array('unit_id' => $unit_id));
                    }

                    //UPDATE cs_sales_close > change_date
                    if($valid){
                        //UPDATE res detail
                        $this->mdl_general->update('cs_sales_close',
                            array('reservation_id' => $reservationId, 'unit_id'=> $billdetail->unit_id, 'close_date >=' => $start_date, 'close_status <=' => 0),
                            array('unit_id' => $unit_id));
                    }

                    //Obtain new unittype id
                    $new_room = array();
                    $new_room['unit_id'] = $unit_id;
                    $new_room['modified_date'] = $start_date;
                    $new_room['reff_billdetail_id'] = 0;

                    $this->mdl_general->update('cs_bill_detail', array('billdetail_id' => $billdetail_id), $new_room);
                    //$result['debug'] = $new_room;

                    $valid = $this->update_hsk_by_unit($unit_id, false,'',0,'','Change Room');

                }catch(Exception $e){
                    $valid = false;
                    //$result['debug'] = $e;
                }

                //FINALIZE TRANSACTION
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['valid'] = '0';
                    $result['message'] = 'Change Room can not be processed. Please try again!';
                }
                else
                {
                    if($valid){
                        $this->db->trans_commit();

                        $result['valid'] = '1';
                        $result['message'] = 'Change Room success.';
                    }else{
                        $this->db->trans_rollback();

                        $result['valid'] = '0';
                        $result['message'] = 'Change Room can not be processed. Please try again!';
                    }
                }
            }

        }else{
            $result['valid'] = '0';
            $result['message'] = 'Change Room can not be processed.';
        }

        echo json_encode($result);
    }

    public function xchange_checkin_room_v1(){
        $result = array();
        $result['valid'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';
        $result['debug'] = '';

        $reservationId = $_POST['reservation_id'];
        $billdetail_id = $_POST['reff_bill_detail'];
        $unit_id = $_POST['unit_id'];
        $start_date = $_POST['change_date'];

        $result['redirect_link'] = base_url('frontdesk/management/guest_form/' . $reservationId .'.tpd');

        if($reservationId > 0 && $billdetail_id > 0){
            $this->load->model('frontdesk/mdl_frontdesk');

            //Re-calculate current bill
            $billdetail = $this->db->query("SELECT bill.*, unit.unittype_id, res.reservation_type, res.departure_date, res.agent_id, res.is_rate_yearly FROM cs_bill_detail bill
                                            JOIN cs_reservation_header res ON res.reservation_id = bill.reservation_id
                                            JOIN ms_unit unit ON unit.unit_id = bill.unit_id
                                            WHERE bill.billdetail_id = " . $billdetail_id . " AND bill.reservation_id = " . $reservationId . "
                                            ORDER BY bill.reff_billdetail_id DESC ");

            if($billdetail->num_rows() > 0){
                $billdetail = $billdetail->row();

                $res_detail = $this->db->get_where('cs_reservation_detail', array('reservation_id'=> $reservationId, 'unit_id'=> $billdetail->unit_id, 'CONVERT(date,checkin_date) >=' => $start_date, 'close_status >' => 0));

                if($res_detail->num_rows() <= 0){
                    $valid = true;

                    //BEGIN TRANSACTION
                    $this->db->trans_begin();

                    try{
                        $reservation_type = $billdetail->reservation_type;
                        $is_rate_yearly = $billdetail->is_rate_yearly;
                        $departure_date = ymd_from_db($billdetail->departure_date);

                        //Get Discount
                        $discount_per_unit = 0;
                        $discount = $this->db->query('select ISNULL(discount,0) as discount from cs_reservation_detail
                                                      where reservation_id = ' . $reservationId . ' and unit_id = ' . $billdetail->unit_id);
                        if($discount->num_rows() > 0){
                             $discount_per_unit = $discount->row()->discount;
                        }

                        if(ymd_from_db($billdetail->date_start) != $start_date){
                            $calc = $this->mdl_frontdesk->calculate_booking($billdetail->unittype_id, dmy_from_db($billdetail->date_start), ymd_to_dmy($start_date), $reservation_type, $is_rate_yearly, 0);

                            $old_room = array();
                            $old_room['date_end'] = $start_date;
                            $old_room['date_interval'] = $calc['daily_count'];
                            $old_room['amount'] = $calc['total_amount'];
                            $old_room['disc_amount'] = ($discount_per_unit * $calc['daily_count']);
                            $net_amount = round($old_room['amount'] - $old_room['disc_amount'],0);
                            $tax = ($calc['tax_rate'] > 0 ? $calc['tax_rate'] * $net_amount : 0);
                            $old_room['tax'] = $tax;
                            $old_room['reff_billdetail_id'] = 0;
                            $old_room['status'] = STATUS_CLOSED;

                            //UPDATE cs_bill_detail
                            $this->mdl_general->update('cs_bill_detail', array('billdetail_id' => $billdetail_id), $old_room);

                            //UPDATE hsk status
                            $valid = $this->update_hsk_by_unit($unit_id, true, $start_date, $reservationId);

                        }else{
                            $this->mdl_general->update('ms_unit', array('unit_id' => $billdetail->unit_id), array('hsk_status' => HSK_STATUS::IS));
                        }

                        //CALCULATE NEW CHECK IN bill
                        if($valid){
                            //UPDATE res detail
                            $this->mdl_general->update('cs_reservation_detail',
                                array('reservation_id' => $reservationId,'unit_id'=> $billdetail->unit_id, 'CONVERT(date,checkin_date) >=' => $start_date),
                                array('unit_id' => $unit_id));
                        }

                        //Obtain new unittype id
                        $new_unit = $this->db->get_where('ms_unit', array('unit_id' => $unit_id));

                        $calc = $this->mdl_frontdesk->calculate_booking($new_unit->row()->unittype_id, ymd_to_dmy($start_date), ymd_to_dmy($departure_date), $reservation_type, $is_rate_yearly, 0);

                        $num_of_days = $calc['daily_count'];
                        $amount = $calc['total_amount'];
                        $disc_amount = round($discount_per_unit * $num_of_days, 0);
                        $net_amount = round($amount - $disc_amount,0);

                        $tax_rate = $calc['tax_rate'];
                        $tax = ($tax_rate > 0 ? $tax_rate * $net_amount : 0);

                        $new_room = array();
                        $new_room['unit_id'] = $unit_id;
                        $new_room['date_start'] = $start_date;
                        $new_room['date_end'] = $departure_date;
                        $new_room['date_interval'] = $num_of_days;
                        $new_room['rate'] = round($amount / $num_of_days,0);
                        $new_room['disc_percent'] = $billdetail->disc_percent;
                        $new_room['disc_amount'] = $disc_amount;
                        $new_room['tax'] = $tax;
                        $new_room['amount'] = $amount;
                        $new_room['is_billed'] = 0;
                        $new_room['status'] = STATUS_NEW;

                        if(ymd_from_db($billdetail->date_start) != $start_date){
                            $new_room['reservation_id'] = $billdetail->reservation_id;
                            $new_room['bill_id'] = $billdetail->bill_id;
                            $new_room['reff_billdetail_id'] = $billdetail_id;
                            $new_room['item_id'] = 0;
                            $new_room['item_qty'] = 0;
                            $new_room['transtype_id'] = $billdetail->transtype_id;
                            $new_room['is_monthly'] = $billdetail->is_monthly;
                            $new_room['currencytype_id'] = $billdetail->currencytype_id;

                            $this->db->insert('cs_bill_detail', $new_room);
                            $insertID = $this->db->insert_id();

                            if($insertID <= 0){
                                $valid = false;
                            }
                        }else{
                            $new_room['reff_billdetail_id'] = 0;

                            $this->mdl_general->update('cs_bill_detail', array('billdetail_id' => $billdetail_id), $new_room);
                            $result['debug'] = $new_room;
                        }

                        $valid = $this->update_hsk_by_unit($unit_id, false);

                    }catch(Exception $e){
                        $valid = false;
                        //$result['debug'] = $e;
                    }

                    //FINALIZE TRANSACTION
                    if ($this->db->trans_status() === FALSE)
                    {
                        $this->db->trans_rollback();

                        $result['valid'] = '0';
                        $result['message'] = 'Change Room can not be processed. Please try again!';
                    }
                    else
                    {
                        if($valid){
                            $this->db->trans_commit();

                            $result['valid'] = '1';
                            $result['message'] = 'Change Room success.';
                        }else{
                            $this->db->trans_rollback();

                            $result['valid'] = '0';
                            $result['message'] = 'Change Room can not be processed. Please try again!';
                        }
                    }
                }else{
                    $result['valid'] = '0';
                    $result['message'] = 'Change Room is not allowed! Daily Closing has been executed.';
                }
            }

        }else{
            $result['valid'] = '0';
            $result['message'] = 'Change Room can not be processed.';
        }

        echo json_encode($result);
    }

    public function xchange_corporate_guest(){
        $result = array();
        $result['valid'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';
        $result['debug'] = '';

        $reservationId = $_POST['reservation_id'];
        $newTenantId = $_POST['tenant_id'];
        $start_date = date('Y-m-d H:i:s');

        $result['redirect_link'] = base_url('frontdesk/management/guest_form/' . $reservationId .'.tpd');

        if($reservationId > 0 && $newTenantId > 0){
            $this->load->model('frontdesk/mdl_frontdesk');

            $valid = true;

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $reservation = $this->db->get_where('cs_reservation_header',array('reservation_id' => $reservationId));
            if($reservation->num_rows() > 0){
                $reservation = $reservation->row();

                $oldTenantId = $reservation->tenant_id;

                //UPDATE RESERVATION
                $this->mdl_general->update('cs_reservation_header',
                                array('reservation_id' => $reservationId, 'tenant_id' => $oldTenantId),
                                array('tenant_id' => $newTenantId));

                //CREATE CHANGE GUEST LOG
                $guest_log = array();
                $guest_log['reservation_id'] = $reservationId;
                $guest_log['prev_tenant_id'] = $oldTenantId;
                $guest_log['new_tenant_id'] = $newTenantId;
                $guest_log['change_date'] = $start_date;
                $guest_log['status'] = STATUS_NEW;

                $this->db->insert('cs_corporate_guest', $guest_log);
                $insertedId = $this->db->insert_id();

                if($insertedId <= 0){
                    $valid = false;
                }

                if($valid){
                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_subject'] = 'Change Corporate Guest';
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $reservationId;
                    $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION;
                    $data_log['action_type'] = STATUS_EDIT;
                    $this->db->insert('app_log', $data_log);
                }

                //FINALIZE TRANSACTION
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['valid'] = '0';
                    $result['message'] = 'Change Guest can not be processed. Please try again!';
                }
                else
                {
                    if($valid){
                        $this->db->trans_commit();

                        $result['valid'] = '1';
                        $result['message'] = 'Change Guest completed.';
                    }else{
                        $this->db->trans_rollback();

                        $result['valid'] = '0';
                        $result['message'] = 'Change Guest can not be processed. Please try again!';
                    }
                }
            }
        }else{
            $result['valid'] = '0';
            $result['message'] = 'Change Guest can not be processed.';
        }

        echo json_encode($result);
    }

    private function get_pending_amount($reservation_id = 0){
        $result['reservation_id'] = $reservation_id;
        $result['invoice_amount'] = 0;
        $result['pending_amount_primary'] = 0;
        $result['pending_amount_other'] = 0;

        if($reservation_id > 0){
            $reservation = $this->db->get_where('cs_reservation_header', array('reservation_id' => $reservation_id));
            if($reservation->num_rows() > 0){
                $reservation = $reservation->row();

                $ledger = array();
                $ledger_qry = $this->db->query('SELECT * FROM fxnCS_ReservationLedger(' . $reservation_id . ') ORDER BY order_date,type ');
                if($ledger_qry->num_rows() > 0){
                    $ledger = $ledger_qry->result_array();
                }

                $pending_amount = 0;
                $pending_amount_primary = 0;
                $pending_amount_other = 0;

                if(count($ledger) > 0){
                    foreach($ledger as $row){
                        if($row['debit'] > 0){
                            $pending_amount += $row['debit'];
                        }else{
                            $pending_amount -= $row['credit'];
                        }
                    }

                    //CALCULATE PAYMENT CORPORATE (COMPANY & GUEST)
                    if($reservation->reservation_type == RES_TYPE::CORPORATE){
                        foreach($ledger as $row){
                            if($row['type'] == 1){
                                //IV Main
                                $pending_amount_primary += $row['debit'];
                                //CN
                                if($row['detail_id'] > 0){
                                    $cn = $this->db->query('SELECT ISNULL(SUM(ar_creditnote_detail.credit_amount),0) as cn_amount
                                                        FROM ar_creditnote_detail
                                                        JOIN ar_creditnote_header ON ar_creditnote_header.creditnote_id = ar_creditnote_detail.creditnote_id
                                                        WHERE ar_creditnote_header.status IN(' . STATUS_POSTED . ',' . STATUS_CLOSED . ') AND ar_creditnote_detail.billdetail_id = ' . $row['detail_id']);

                                    if($cn->num_rows() > 0){
                                        $pending_amount_primary -= $cn->row()->cn_amount;
                                    }
                                }
                            }else if($row['type'] == 3){
                                if($row['is_primary_debtor'] > 0){
                                    //IV Other
                                    $pending_amount_primary += $row['debit'];
                                    //CN
                                    if($row['detail_id'] > 0){
                                        $cn = $this->db->query('SELECT ISNULL(SUM(ar_creditnote_detail.credit_amount),0) as cn_amount
                                                            FROM ar_creditnote_detail
                                                            JOIN ar_creditnote_header ON ar_creditnote_header.creditnote_id = ar_creditnote_detail.creditnote_id
                                                            WHERE ar_creditnote_header.status IN(' . STATUS_POSTED . ',' . STATUS_CLOSED . ') AND ar_creditnote_detail.billdetail_id = ' . $row['detail_id']);

                                        if($cn->num_rows() > 0){
                                            $pending_amount_primary -= $cn->row()->cn_amount;
                                        }
                                    }
                                }else{
                                    //IV Other
                                    $pending_amount_other += $row['debit'];
                                    //CN
                                    if($row['detail_id'] > 0){
                                        $cn = $this->db->query('SELECT ISNULL(SUM(ar_creditnote_detail.credit_amount),0) as cn_amount
                                                            FROM ar_creditnote_detail
                                                            JOIN ar_creditnote_header ON ar_creditnote_header.creditnote_id = ar_creditnote_detail.creditnote_id
                                                            WHERE ar_creditnote_header.status IN(' . STATUS_POSTED . ',' . STATUS_CLOSED . ') AND ar_creditnote_detail.billdetail_id = ' . $row['detail_id']);

                                        if($cn->num_rows() > 0){
                                            $pending_amount_other -= $cn->row()->cn_amount;
                                        }
                                    }
                                }
                            }else if($row['type'] == 2){
                                //RV
                                if($row['is_primary_debtor'] > 0){
                                    $pending_amount_primary -= $row['credit'];
                                }else{
                                    $pending_amount_other -= $row['credit'];
                                }
                            }else if($row['type'] == 4 || $row['type'] == 6) {
                                //Refund & DN only to main
                                $pending_amount_primary +=  $row['debit'];
                            }
                        }
                    }else{
                        $pending_amount_primary = $pending_amount;
                    }
                }

                $result['invoice_amount'] = $pending_amount; //($amountBill - abs($amountReceipt));
                $result['pending_amount_primary'] = $pending_amount_primary;
                $result['pending_amount_other'] = $pending_amount_other;
            }
        }

        return $result;
    }

    public function ajax_modal_add_srf($reservation_id = 0){
        if ($reservation_id > 0) {
            $qry = $this->db->get_where('cs_reservation_unit', array('reservation_id' => $reservation_id, 'status' => STATUS_NEW));
            $row = $qry->row();
            $data['unit_id'] = $row->unit_id;
        }
        $this->load->view('frontdesk/management/ajax_modal_add_srf', $data);
    }

    public function ajax_modal_add_srf_submit(){
        $result = array(
            'err'       => '0',
            'message'   => '',
            'debug'     => ''
        );

        $data['srf_date'] = date('Y-m-d');
        $data['unit_id'] = $_POST['unit_id'];
        $data['is_booking_available'] = 0;
        $data['requested_by'] = $_POST['requested_by'];
        $data['srf_type'] = SRF_TYPE::MINOR_OUT_OF_ORDER;
        $data['srf_note'] = $_POST['srf_note'];
        $data['srf_no'] = $this->mdl_general->generate_code(Feature::FEATURE_CS_SRF, $data['srf_date']);
        $data['status'] = STATUS_NEW;
        $data['created_by'] = my_sess('user_id');
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['modified_by'] = 0;
        $data['modified_date'] = date('Y-m-d H:i:s');
        $this->db->insert('cs_srf_header', $data);
        $srfId = $this->db->insert_id();

        if ($srfId > 0) {
            $data_log['user_id'] = my_sess('user_id');
            $data_log['log_subject'] = 'CREATE SRF ' . $data['srf_no'];
            $data_log['log_date'] = date('Y-m-d H:i:s');
            $data_log['reff_id'] = $srfId;
            $data_log['feature_id'] = Feature::FEATURE_CS_SRF;
            $this->db->insert('app_log', $data_log);

            $result['message'] = 'Successfully add SRF.';
        } else {
            $result['err'] = '1';
            $result['message'] = 'Failed add SRF.';
        }

        echo json_encode($result);
    }

    public function pdf_folio($reservation_id = 0, $isMainFolio = 1, $isMultiPages = 1) {
        if($reservation_id > 0){
            //Reservation
            $qry = $this->db->query('SELECT * FROM view_cs_reservation WHERE reservation_id = ' . $reservation_id);
            if($qry->num_rows() > 0) {
                $data['row'] = $qry->row_array();

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                $isMainFolio = $isMainFolio > 0 ? true : false;

                //Tenant
                $folio_caption = 'Guest Folio';
                $bill_info = '';
                $reservation_type = $data['row']['reservation_type'];

                if($reservation_type == RES_TYPE::CORPORATE && $isMainFolio){
                    $folio_caption = 'Corporate Folio';

                    $company = $this->db->get_where('ms_company', array('company_id' => $data['row']['company_id']));
                    if($company->num_rows() > 0){
                        $company = $company->row_array();
                        $bill_info = $company['company_name'];
                        $bill_info .= trim($company['company_address']) != '' ? '<br>' . nl2br($company['company_address']) : '';
                        $bill_info .= trim($company['company_phone']) != '' ? '<br>' . $company['company_phone'] : '';
                        $bill_info .= trim($company['company_pic_name']) != '' ? '<br>Attn. ' . $company['company_pic_name'] : '';
                    }
                }else{
                    $folio_caption = 'Guest Folio';
                    if($data['row']['hidden_me'] <= 0){
                        $tenant = $this->db->get_where('ms_tenant', array('tenant_id' => $data['row']['tenant_id']));
                        if($tenant->num_rows() > 0){
                            $tenant = $tenant->row_array();
                            $bill_info = ((trim($tenant['tenant_salutation']) != '' ? $tenant['tenant_salutation'] . ' ' : '') . $tenant['tenant_fullname']);
                            $bill_info .= trim($tenant['tenant_address']) != '' ? '<br>' . nl2br($tenant['tenant_address']) : '';
                            $bill_info .= '<br>' . $tenant['tenant_city'] . ' ' . $tenant['tenant_postalcode'];
                            $bill_info .= '<br>' . $tenant['tenant_country'];
                            //$bill_info .= trim($tenant['tenant_phone']) != '' ? '<br>' . $tenant['tenant_phone'] : '';
                        }
                    }else{
                        $bill_info = '';
                    }
                }

                $data['folio_title'] = $folio_caption;
                $data['guest_info'] = $bill_info;
                $data['is_main_folio'] = $isMainFolio;

                //Ledger
                $ledger = $this->db->query('SELECT * FROM fxnCS_ReservationLedger(' . $reservation_id . ') ORDER BY order_date,type ');
                if($ledger->num_rows() > 0){
                    $data['ledger'] = $ledger->result_array();
                }

                //Deposit
                $deposit_in = 0;
                $deposit_al = 0;
                $deposit_out = 0;

                $deposit_ledger = $this->db->query('SELECT * FROM fxnCS_DepositLedger(' . $reservation_id . ') ORDER BY order_date,type ');

                if($deposit_ledger->num_rows() > 0){
                    foreach($deposit_ledger->result_array() as $trx){
                        if($trx['type'] == 1){
                            $deposit_in += $trx['debit'];
                        }else if($trx['type'] == 2){
                            $deposit_al += $trx['credit'];
                        }else if($trx['type'] == 3){
                            $deposit_out += $trx['credit'];
                        }
                    }
                }

                $data['deposits'] = array('deposit' => $deposit_in, 'alloc' => $deposit_al, 'refund' => $deposit_out);

                //echo $this->db->last_query();
                $data['multi_pages'] = $isMultiPages > 0 ? true : false;

                $this->load->view('frontdesk/management/pdf_invoice', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']['reservation_code'] . ".pdf", array('Attachment'=>0));

                //wkhtml_print(array('orientation'=>'portrait'));
            }
            else {
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    public function pdf_folio_reference($reservation_id = 0, $isMultiPages = 1) {
        if($reservation_id > 0){
            //Reservation
            $qry = $this->db->query('SELECT * FROM view_cs_reservation WHERE reservation_id = ' . $reservation_id);
            if($qry->num_rows() > 0) {
                $data['row'] = $qry->row_array();

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                //Tenant
                $tenant = $this->db->get_where('ms_tenant', array('tenant_id' => $data['row']['tenant_id']));
                if($tenant->num_rows() > 0){
                    $data['tenant'] = $tenant->row_array();
                }

                $bill_info = '';
                $reservation_type = $data['row']['reservation_type'];

                if($reservation_type == RES_TYPE::CORPORATE){
                    $company = $this->db->get_where('ms_company', array('company_id' => $data['row']['company_id']));
                    if($company->num_rows() > 0){
                        $company = $company->row_array();
                        $bill_info = $company['company_name'];
                        $bill_info .= trim($company['company_address']) != '' ? '<br>' . nl2br($company['company_address']) : '';
                        $bill_info .= trim($company['company_phone']) != '' ? '<br>' . $company['company_phone'] : '';
                        $bill_info .= trim($company['company_pic_name']) != '' ? '<br>Attn. ' . $company['company_pic_name'] : '';
                    }
                }else{
                    if($data['row']['hidden_me'] <= 0){
                        $tenant = $this->db->get_where('ms_tenant', array('tenant_id' => $data['row']['tenant_id']));
                        if($tenant->num_rows() > 0){
                            $tenant = $tenant->row_array();
                            $bill_info = ((trim($tenant['tenant_salutation']) != '' ? $tenant['tenant_salutation'] . ' ' : '') . $tenant['tenant_fullname']);
                            $bill_info .= '<br>' . nl2br($tenant['tenant_address']);
                            $bill_info .= '<br>' . $tenant['tenant_city'] . ' ' . $tenant['tenant_postalcode'];
                            $bill_info .= '<br>' . $tenant['tenant_country'];
                        }
                    }else{
                        $bill_info = '';
                    }
                }

                $data['guest_info'] = $bill_info;

                //Ledger
                $ledger = array();

                $bills = $this->db->query("SELECT cs_bill_header.journal_no, cs_bill_header.bill_id, cs_bill_detail.*, unit.unit_code, tax.taxtype_percent
                                              FROM cs_bill_detail
                                              JOIN cs_bill_header     ON cs_bill_header.bill_id = cs_bill_detail.bill_id
                                              JOIN ms_unit unit       ON unit.unit_id = cs_bill_detail.unit_id
                                              JOIN ms_unit_type typ   ON typ.unittype_id = unit.unittype_id
                                              JOIN tax_type tax       ON tax.taxtype_id = typ.taxtype_id
                                              WHERE cs_bill_header.reservation_id = " . $reservation_id . "
                                              AND cs_bill_header.is_other_charge <= 0 AND cs_bill_detail.item_id <= 0
                                              AND (cs_bill_detail.month_interval > 0 OR cs_bill_detail.year_interval > 0)
                                           ");
                if($bills->num_rows() > 0){
                    $rate_per_month = 0;
                    $disc_per_month = 0;
                    $totalMonth = 0;

                    foreach($bills->result_array() as $bill){
                        $tax_percent = $bill['taxtype_percent'] > 0 ? ($bill['taxtype_percent']/100) : 0;

                        $rate_per_month = $bill['rate'];
                        $nMonth = $bill['month_interval'];
                        $nYear = $bill['year_interval'];

                        $totalMonth = ($nYear * 12) + $nMonth;

                        $discount = $bill['disc_amount'];
                        $disc_per_month = round($discount/$totalMonth,0);

                        $amount = $rate_per_month - $disc_per_month;
                        $tax = $tax_percent > 0 ? round($amount * $tax_percent,0) : 0;
                        $subtotal = round($amount + $tax,0);

                        $sales = $this->db->query("SELECT *
                                              FROM cs_sales_close
                                              WHERE reservation_id = " . $reservation_id . "
                                              AND unit_id = " . $bill['unit_id'] . " ORDER BY unit_id, close_date
                                           ");
                        if($sales->num_rows() > 0){
                            foreach($sales->result_array() as $sale){
                                array_push($ledger, array('unit_code' => $bill['unit_code'],'room_date' => $sale['close_date'], 'rate' => $subtotal));
                            }
                        }
                    }
                }

                $data['ledger'] = $ledger;

                //echo $this->db->last_query();
                $data['multi_pages'] = $isMultiPages > 0 ? true : false;

                $this->load->view('frontdesk/management/pdf_invoice_room', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']['reservation_code'] . ".pdf", array('Attachment'=>0));

            }
            else {
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    #endregion

    #region Deposit

    public function deposit_manage($type = 1){
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
        $this->load->view('frontdesk/management/deposit_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function deposit_form($id = 0, $reservation_id = 0){
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
        array_push($data_header['custom_script'], base_url() . 'assets/custom/util.js');

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

        $deposittypes = $this->db->query('select * from ms_deposit_type where status = ' . STATUS_NEW . 'order by deposit_key');
        if($deposittypes->num_rows() > 0){
            $data['deposittypes'] = $deposittypes->result_array();
        }

        if($reservation_id > 0){
            $deposits = $this->db->get_where('ar_deposit_header', array('reservation_id' => $reservation_id, 'status' => STATUS_NEW));
            if($deposits->num_rows() > 0) {
                $id = $deposits->row()->deposit_id;
            }else{
                $reservation = $this->db->get_where('view_cs_reservation', array('reservation_id' => $reservation_id));
                if ($reservation->num_rows() > 0) {
                    $data['folio'] = $reservation->row();
                }
            }
        }

        $data['deposit_id'] = $id;
        if($id > 0){
            $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_deposit_header.reservation_id',
                           'ms_payment_type' => 'ms_payment_type.paymenttype_id = ar_deposit_header.paymenttype_id');
            $qry = $this->mdl_finance->getJoin('ar_deposit_header.*, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, ms_payment_type.payment_type ','ar_deposit_header', $joins, array('deposit_id' => $id));
            $data['deposit'] = $qry->row();

            $details = $this->db->get_where('ar_deposit_detail', array('deposit_id' => $data['deposit_id']));
            $data['row_det'] = $details->result_array();
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('frontdesk/management/deposit_form', $data);
        $this->load->view('layout/footer');
    }

    public function get_deposit_manage($menu_id = 0, $is_history = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();

        $where['ar_deposit_header.reservation_id > '] = 0;
        if($is_history > 0){
            $where['ar_deposit_header.status'] = STATUS_POSTED;
        }else{
            $where['ar_deposit_header.status'] = STATUS_NEW;
        }

        $like = array();
        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_deposit_header.deposit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['DATE(ar_deposit_header.deposit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['DATE(ar_deposit_header.deposit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_desc'])){
            if($_REQUEST['filter_desc'] != ''){
                $like['ar_deposit_header.deposit_desc'] = $_REQUEST['filter_desc'];
            }
        }
        if(isset($_REQUEST['filter_paymenttype_id'])){
            if($_REQUEST['filter_paymenttype_id'] != ''){
                $like['ar_deposit_header.paymenttype_id'] = $_REQUEST['filter_paymenttype_id'];
            }
        }
        if(isset($_REQUEST['filter_reservation'])){
            if($_REQUEST['filter_reservation'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation'];
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ms_tenant.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_deposit_header.reservation_id',
                       'ar_receipt' => 'ar_receipt.receipt_no = ar_deposit_header.deposit_no',
                       'ms_tenant' => 'ms_tenant.tenant_id = ar_receipt.tenant_id',
                       'fn_bank_account' => 'fn_bank_account.bankaccount_id = ar_deposit_header.bankaccount_id',
                       'ms_payment_type' => 'ms_payment_type.paymenttype_id = ar_deposit_header.paymenttype_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_deposit_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_deposit_header.deposit_no DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_deposit_header.deposit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_deposit_header.deposit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'ms_tenant.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'ar_deposit_header.paymenttype_id ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'ar_deposit_header.deposit_paymentamount ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 7){
                $order = 'ar_deposit_header.deposit_bankcharges ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 8){
                $order = 'ar_deposit_header.bankaccount_id ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_deposit_header.*, view_cs_reservation.reservation_code, ms_tenant.tenant_fullname, view_cs_reservation.company_name, fn_bank_account.bankaccount_code,fn_bank_account.bankaccount_desc, ms_payment_type.paymenttype_desc'
                                           ,'ar_deposit_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_EDIT)){
                    $btn_action .= '<li> <a href="' . base_url('frontdesk/management/deposit_form/' . $row->deposit_id) . '.tpd"><i class="fa fa-pencil"></i> Edit</a> </li>';
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->deposit_id . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
                else {
                    $btn_action .= '<li> <a href="' . base_url('frontdesk/management/deposit_form/' . $row->deposit_id) . '.tpd"><i class="fa fa-file"></i> Open</a> </li>';
                }
            }else if($row->status == STATUS_CANCEL){
                $btn_action .= '<li> <a href="' . base_url('frontdesk/management/deposit_form/' . $row->deposit_id) . '.tpd"><i class="fa fa-file"></i> Open</a> </li>';
            }else if($row->status == STATUS_POSTED || $row->status == STATUS_CLOSED){
                $btn_action .= '<li> <a href="' . base_url('frontdesk/management/deposit_form/' . $row->deposit_id) . '.tpd"><i class="fa fa-file"></i> Open</a> </li>';
                $btn_action .= '<li> <a href="' . base_url('ar/report/pdf_official_receipt/' . $row->deposit_no) . '.tpd" target="_blank"><i class="fa fa-print"></i> Receipt</a> </li>';
                $btn_action .= '<li> <a href="' . base_url('ar/deposit/pdf_depositvoucher/' . $row->deposit_id) . '.tpd" data-id="' . $row->deposit_id. '" target="_blank"><i class="fa fa-print"></i> Voucher</a> </li>';
            }

            if($row->deposit_paymentamount > 0 && $row->status == STATUS_NEW){
                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->deposit_id . '" name="ischecked[]"/>',
                    $row->deposit_no,
                    dmy_from_db($row->deposit_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    $row->paymenttype_desc,
                    format_num($row->deposit_paymentamount + $row->deposit_bankcharges,0),
                    //format_num($row->deposit_bankcharges,0),
                    //$row->deposit_desc,
                    //$row->bankaccount_desc,
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            }else{
                $records["data"][] = array(
                    '',
                    $row->deposit_no,
                    dmy_from_db($row->deposit_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    $row->paymenttype_desc,
                    format_num($row->deposit_paymentamount + $row->deposit_bankcharges,0),
                    //format_num($row->deposit_bankcharges,0),
                    //$row->deposit_desc,
                    //$row->bankaccount_desc,
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

    public function submit_deposit_folio(){
        $valid = true;

        if(isset($_POST)){
            $depositId = $_POST['deposit_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['deposit_date'] = dmy_to_ymd($_POST['deposit_date']);

            $data['reservation_id'] = $_POST['reservation_id'];;
            $data['company_id'] = 0;
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

            $this->load->library('../controllers/ar/deposit');
            if($depositId > 0){
                $qry = $this->db->get_where('ar_deposit_header', array('deposit_id' => $depositId));
                $row = $qry->row();

                $arr_date = explode('-', $data['deposit_date']);
                $arr_date_old = explode('-', ymd_from_db($row->deposit_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['deposit_no'] = $this->deposit->generate_depositno($data, $data['deposit_date']);

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
                $data['deposit_no'] = $this->deposit->generate_depositno($data, $data['deposit_date']);
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

            if($valid){
                $reservation = $this->db->get_where('cs_reservation_header',array('reservation_id' => $data['reservation_id']));
                if($reservation->num_rows() > 0){
                    $rv['tenant_id'] = $reservation->row()->tenant_id;
                    $this->mdl_general->update('ar_receipt', array('receipt_no' => $data['deposit_no'], 'status' => FLAG_DEPOSIT), $rv);
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
                redirect(base_url('frontdesk/management/deposit_form/' . $depositId . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    //redirect(base_url('frontdesk/management/deposit_manage/1.tpd'),true);
                    redirect(base_url('frontdesk/reservation/folio_manage/1.tpd'),true);
                }
                else {
                    redirect(base_url('frontdesk/management/deposit_form/' . $depositId . '.tpd'));
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

    public function xposting_deposit_folio_by_id(){
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
                    $result['redirect_link'] = base_url('frontdesk/management/deposit_form/'. $depositId .'.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be posted. Please try again later.';
            }

        }

        echo json_encode($result);
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
                //redirect(base_url('ar/corporate_bill/deposit/1.tpd'));
                redirect(base_url('frontdesk/reservation/folio_deposit/1.tpd'));
            }
            else {
                //redirect(base_url('ar/corporate_bill/deposit/1.tpd'));
                redirect(base_url('frontdesk/reservation/folio_deposit/1.tpd'));
            }
        }
    }

    #endregion

    #region Modal Lookup Form

    public function ajax_modal_reservation(){
        $this->load->view('frontdesk/management/ajax_modal_reservation');
    }

    public function get_modal_reservation($num_index = 0, $where_str = '', $reservation_id = 0){
        $where = array();
        $like = array();

        $where['view_cs_reservation.tenant_id > '] = 0;

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

        if(trim($where_str) == ''){
            $where_str = ' view_cs_reservation.status IN(' . ORDER_STATUS::RESERVED .',' . ORDER_STATUS::CHECKIN . ') ';
        }

        $iTotalRecords = $this->mdl_general->count('view_cs_reservation',$where, $like, '', '', array(), $where_str);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_cs_reservation',$where, $like, $order, $iDisplayLength, $iDisplayStart,'', '', array(), $where_str);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';
            $attr .= ' data-reservation-id="' . $row->reservation_id . '" ';
            $attr .= ' data-tenant-id="' . $row->tenant_id . '" ';
            $attr .= ' data-tenant-name="' . $row->tenant_fullname . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            if ($reservation_id == $row->reservation_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }

            //$text = "Select";
            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-tenant" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $row->reservation_code,
                $row->tenant_fullname,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_deposit_folio(){
        $this->load->view('frontdesk/management/ajax_modal_deposit_folio');
    }

    public function get_modal_deposit_folio($num_index = 0, $reservation_id = 0){
        $where = array();
        $like = array();

        $where['view_cs_reservation.tenant_id > '] = 0;
        //$where['view_cs_reservation.reservation_type '] = RES_TYPE::PERSONAL;

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

        $where_str = ' view_cs_reservation.status IN(' . ORDER_STATUS::ONLINE_VALID . ',' . ORDER_STATUS::RESERVED . ',' . ORDER_STATUS::CHECKIN .' ) ';

        $iTotalRecords = $this->mdl_general->count('view_cs_reservation',$where, $like, '', '', array(), $where_str);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_cs_reservation',$where, $like, $order, $iDisplayLength, $iDisplayStart,'', '', array(), $where_str);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';
            $attr .= ' data-reservation-id="' . $row->reservation_id . '" ';
            $attr .= ' data-reservation-code="' . $row->reservation_code . '" ';
            $attr .= ' data-min-amount="' . $row->min_deposit_amount . '" ';
            $attr .= ' data-tenant-id="' . $row->tenant_id . '" ';
            $attr .= ' data-tenant-name="' . $row->tenant_fullname . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            if ($reservation_id == $row->reservation_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }

            if ($reservation_id == $row->reservation_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }else{
                $text = "Select";
            }

            $res_caption = '<i class="' . RES_TYPE::css_icon($row->reservation_type) . ' tooltips" data-original-title="' . RES_TYPE::caption($row->reservation_type) . '"></i>';

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-folio" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $status_caption = '<span class="badge badge-primary tooltips " data-original-title="' . ORDER_STATUS::get_status_name($row->status, false) . '">' . ORDER_STATUS::code($row->status) . '</span>';

            $records["data"][] = array(
                $row->reservation_code,
                $row->tenant_fullname,
                $res_caption,
                $status_caption,
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
}

/* End of file registration.php */
/* Location: ./application/controllers/frondesk/registration.php */