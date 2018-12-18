<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cronjob extends CI_Controller {

    #region AUTO CHECKIN
    public function auto_checkin(){
        $valid = false;
        //$valid = $this->auto_checkin_on();
        echo 'AUTO CHECKIN -> ' . ($valid ? 'ON' : 'OFF');
    }

    public function auto_checkin_on(){
        $valid = true;

        //CRONJOB LOG
        $cron_log['function_name'] = 'auto_checkin';
        $cron_log['created_date'] = date('Y-m-d H:i:s');
        $cron_log['last_executed_date'] = $cron_log['created_date'];
        $cron_log['is_commit'] = 0;
        $cron_log['affected_rows']  = 0;

        $reservations = $this->db->query("SELECT * FROM view_cs_reservation WHERE status IN(" . ORDER_STATUS::RESERVED . ") AND arrival_date <= '" . date('Y-m-d') . "' ");
        if($reservations->num_rows() > 0){
            //$cron_log['affected_rows'] = $reservations->num_rows();

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            foreach($reservations->result_array() as $reservation){
                if($valid){
                    $today = new DateTime('');
                    $checkOutDate = new DateTime($reservation['departure_date']);

                    if($checkOutDate->format('Y-m-d') > $today->format('Y-m-d')){
                        if($reservation['reservation_type'] == RES_TYPE::CORPORATE){
                            $valid = $this->submit_checkin($reservation);

                            if($valid)
                                $cron_log['affected_rows']++;
                        }else{
                            if($reservation['payment_amount'] > $reservation['min_receipt_amount'] ) {
                                $valid = $this->submit_checkin($reservation);

                                if($valid)
                                    $cron_log['affected_rows']++;
                            }
                        }
                    }else{
                        $data['status'] = STATUS_CANCEL;
                        $data['cancel_note'] = 'Check Out date is no longer valid.';
                        $data['modified_by'] = my_sess('user_id');
                        $data['modified_date'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $reservation['reservation_id']), $data);

                        $detail['status'] = STATUS_CANCEL;
                        $this->mdl_general->update('cs_reservation_detail', array('reservation_id' => $reservation['reservation_id']), $detail);
                        $this->mdl_general->update('cs_reservation_unit', array('reservation_id' => $reservation['reservation_id']), $detail);

                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Cancel Reservation Check Out is Off Date';
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $reservation['reservation_id'];
                        $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION;
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $cron_log['affected_rows']++;
                    }
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                }
                else
                {
                    $this->db->trans_commit();
                    $cron_log['is_commit'] = 1;
                }
            }else{
                $this->db->trans_rollback();
            }

            //Insert Log
            $this->db->insert('cronjob_log', $cron_log);
        }

        echo 'COMPLETE ROWS -> ' . $cron_log['affected_rows'];
	}

    private function submit_checkin($reservation = array()){
        $valid = true;

        $server_date = date('Y-m-d H:i:s');

        if(count($reservation) > 0){
            if($reservation['status'] == ORDER_STATUS::RESERVED){
                //Set Online Valid -> Checked In
                $data['status'] = ORDER_STATUS::CHECKIN;
                $data['modified_by'] = my_sess('user_id');
                $data['modified_date'] = $server_date;
                $data['checkin_date'] = $server_date;

                $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $reservation['reservation_id']), $data);

                //Create Main Bills & POSTING
                $valid = $this->insertMainBills($reservation);

                 //Update HSK to ISC
                if($valid){
                    $valid = $this->update_hsk_status($reservation);
                }

                //Create Closing Schedule
                if($valid){
                    $valid = $this->insertCloseSchedules($reservation);
                }
            }
        }else{
            $valid = false;
        }

        return $valid;
    }

    private function update_hsk_status($reservation = array()){
        $valid = true;

        if(isset($reservation)){
            $units = $this->db->get_where('cs_reservation_unit', array('reservation_id' => $reservation['reservation_id']));
            if($units->num_rows() > 0){
                try {
                    foreach ($units->result() as $unit) {
                        $data = array();
                        $data['unit_id'] = $unit->unit_id;
                        $data['hsk_status'] = HSK_STATUS::ISC;
                        $data['remark'] = 'Auto Check In';
                        $data['created_by'] = my_sess('user_id');
                        $data['created_date'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('ms_unit', array('unit_id' => $unit->unit_id), array('hsk_status' => $data['hsk_status']));

                        $this->db->insert('log_hsk', $data);
                        $insertedID = $this->db->insert_id();
                    }
                }catch(Exception $e){
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    private function insertMainBills($rsvt = array()){
        $this->load->model('frontdesk/mdl_frontdesk');

        $billDetails = array();
        $valid = true;

        if(count($rsvt) > 0 && isset($_POST)){
            $reservationId = $rsvt['reservation_id'];

            try{
                //rates
                $unit_rates = array();

                $qry = $this->db->query('select distinct cs_reservation_detail.unit_id, ms_unit.unit_code, ms_unit.unittype_id, ms_unit_type.unittype_desc, cs_reservation_header.reservation_type, cs_reservation_header.agent_id
                                         from cs_reservation_detail
                                         join cs_reservation_header on cs_reservation_header.reservation_id = cs_reservation_detail.reservation_id
                                         join ms_unit on ms_unit.unit_id = cs_reservation_detail.unit_id
                                         join ms_unit_type on ms_unit_type.unittype_id = ms_unit.unittype_id
                                         where cs_reservation_detail.reservation_id = ' . $reservationId );

                if($qry->num_rows() > 0){
                    $calc_array = array();
                    foreach($qry->result_array() as $unit){
                        //Get Discount
                        $discount_per_unit = 0;
                        $discount = $this->db->query('select ISNULL(discount,0) as discount from cs_reservation_unit
                                              where reservation_id = ' . $reservationId . ' and unit_id = ' . $unit['unit_id']);
                        if($discount->num_rows() > 0){
                            $discount_per_unit = $discount->row()->discount;
                        }

                        $reservation_type = $unit['reservation_type'];
                        $calc = $this->mdl_frontdesk->calculate_booking($unit['unittype_id'], dmy_from_db($rsvt['arrival_date']), dmy_from_db($rsvt['departure_date']), $reservation_type, $rsvt['is_rate_yearly'], $rsvt['billing_type']);

                        array_push($calc_array, array('unit_id'=> $unit['unit_id'],'unit_code'=>$unit['unit_code'],'unittype_desc' => $unit['unittype_desc'], 'calc_data' => $calc, 'discount' => $discount_per_unit));
                    }

                    if(count($calc_array) > 0){
                        if($rsvt['status'] != ORDER_STATUS::CHECKIN){
                            //Split Duration of Unit by months and days
                            foreach($calc_array as $arr){
                                $calc = $arr['calc_data'];

                                if($calc['daily_count'] > 0){
                                    $tax = ($calc['daily_amount'] - $arr['discount']) * $calc['tax_rate'];

                                    array_push($unit_rates, array('billdetail_id' => 0, 'unit_id'=> $arr['unit_id'],'unit_code'=>$arr['unit_code'],'unittype_desc' => $unit['unittype_desc'], 'duration' => $calc['period_caption'], 'month_interval' => $calc['monthly_count'], 'year_interval' => $calc['yearly_count'], 'is_monthly' => $calc['billing_base'], 'rate' => $calc['monthly_rate'], 'local_amount' => $calc['total_amount'], 'bill_start' => dmy_from_db($rsvt['arrival_date']), 'bill_end' => dmy_from_db($rsvt['departure_date']), 'transtype_id' => $calc['transtype_id'], 'tax_amount' => $tax, 'discount' => $arr['discount']));
                                }
                            }
                        }
                    }
                }

                $totalAmount = 0;
                foreach($unit_rates as $rate){
                    if($valid){
                        $detail = array();
                        $detail['is_billed'] = 0;
                        $detail['currencytype_id'] = 1;
                        $detail['status'] = STATUS_NEW;

                        $detail['bill_id'] = 0;
                        $detail['reservation_id'] = $reservationId;
                        $detail['unit_id'] = $rate['unit_id'];
                        $detail['item_id'] = 0;
                        $detail['masteritem_id'] = 0;
                        $detail['transtype_id'] = $rate['transtype_id'];
                        $detail['is_monthly'] = $rate['is_monthly'];
                        $detail['date_start'] = dmy_to_ymd($rate['bill_start']);
                        $detail['date_end'] = dmy_to_ymd($rate['bill_end']);
                        $detail['date_interval'] = 0; //$rate['duration'];
                        $detail['month_interval'] = $rate['month_interval'];
                        $detail['year_interval'] = $rate['year_interval'];
                        $totalMonth = (($detail['year_interval'] * 12) + $detail['month_interval']);
                        $detail['rate'] = round($rate['local_amount'] / $totalMonth,0);
                        $detail['amount'] = $rate['local_amount'];
                        $detail['tax'] = round($rate['tax_amount'],0);
                        $detail['disc_percent'] = 0;
                        $detail['disc_amount'] = 0;
                        if($detail['amount'] > 0){
                            $detail['disc_percent'] = ($rate['discount'] / $detail['amount']) * 100;
                            $detail['disc_amount'] = $rate['discount'];
                        }
                        $detail['modified_date'] = date('Y-m-d H:i:s');

                        array_push($billDetails, $detail);

                        $totalAmount += ($detail['amount'] - $detail['disc_amount'] + $detail['tax']);
                    }
                }

                if($valid){
                    if(count($billDetails) > 0 && $totalAmount > 0){
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

                            $this->db->insert('cs_bill_header', $billHeader);
                            $billId = $this->db->insert_id();

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
                                            $detail['is_billed'] = 100;
                                            $this->db->insert('cs_bill_detail', $detail);
                                            $insertID = $this->db->insert_id();

                                            $detail['billdetail_id'] = $insertID;


                                            if($insertID <= 0){
                                                $valid = false;
                                            }
                                        }
                                        $total = ($detail['amount'] - $detail['disc_amount'] + $detail['tax']);
                                        array_push($bills, array('unit_id' => $detail['unit_id'],'billdetail_id'=>$detail['billdetail_id'], 'total_bill_amount' => $total));
                                    }
                                }

                                //POSTING FOR CHECKIN
                                if($rsvt['status'] == ORDER_STATUS::RESERVED){
                                    //Generate Pending Bills & Posting if Full Paid
                                    if ($valid) {
                                        $valid = $this->insertARPendingBills($rsvt, $bills);
                                    }

                                    if($rsvt['reservation_type'] != RES_TYPE::HOUSE_USE) {
                                        $valid = $this->allocateCheckInReceipt($rsvt, $bills);
                                            //echo '<br>valid 2 : ' . ($valid ? 'true' :'false');
                                    }

                                    //UPDATE HEADER
                                    unset($billHeader);

                                    if($valid){
                                        $this->mdl_general->update('cs_bill_header', array('bill_id' => $billId), array('status' =>STATUS_POSTED));
                                    }
                                }
                            }
                        }else{
                            $valid = false;
                            //echo '<br>[insertMainBills][JournalNo can not be generated] ' . $inv_no;
                        }
                    }
                }

            }catch(Exception $e){
                $valid = false;
                //echo '<br>[insertMainBills][Error] ' . $e;
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
            if($reservation['reservation_type'] != RES_TYPE::CORPORATE ){
                $tenantID = $reservation['tenant_id'];
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
                                $bill = array();
                                $bill['reservation_id'] = $reservationID;
                                $bill['company_id'] = $reservation['company_id'];
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
                }
            }
        }

        return $valid;
    }

    private function createNPostFullPaidInvoice($reservation = array(), $bills = array()){
        $valid = true;

        if(isset($reservation) && isset($bills)) {
            if ($reservation['billing_type'] == BILLING_TYPE::FULL_PAID) {
                $reservationID = $reservation['reservation_id'];
                $reservationType = $reservation['reservation_type'];

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

    #endregion

    #region AUTO CLOSE DAY

    public function auto_close_day(){
        //$this->load->model('frontdesk/mdl_frontdesk');

        //$cron_log = $this->mdl_frontdesk->exec_close_day();

        //echo 'COMPLETE ROWS -> ' . $cron_log['affected_rows'];
    }

    #endregion

    #region CLEAN UNIT

    public function auto_unit_clean(){
        $valid = true;

        //CRONJOB LOG
        $cron_log['function_name'] = 'auto_unit_clean';
        $cron_log['created_date'] = date('Y-m-d H:i:s');
        $cron_log['last_executed_date'] = $cron_log['created_date'];
        $cron_log['is_commit'] = 0;
        $cron_log['affected_rows']  = 0;

        $today = new DateTime('');

        $checkin_units = $this->db->query("SELECT DISTINCT unit.unit_id, res.departure_date, res.reservation_id FROM cs_reservation_detail det
                  JOIN cs_reservation_header res ON res.reservation_id = det.reservation_id
                  JOIN cs_reservation_unit unit ON unit.reservation_id = res.reservation_id
                  WHERE res.status = " . ORDER_STATUS::CHECKIN . " AND (ISNULL(det.hsk,0) > 0 OR LOWER(DATENAME(dw, det.checkin_date)) = 'thursday') AND CONVERT(date, det.checkin_date) = '" . $today->format('Y-m-d') . "' ");
        //echo '<br>' . $this->db->last_query();
        if($checkin_units->num_rows() > 0){

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            foreach($checkin_units->result_array() as $unit){
                if($valid){
                    //Check whether today is Departure date or not
                    $departureDate = new DateTime(ymd_from_db($unit['departure_date']));
                    if($departureDate <= $today){
                        $exist_ea = $this->db->query("SELECT reservation_detail_id FROM cs_reservation_detail
                              WHERE unit_id = " . $unit['unit_id'] . " AND CONVERT(date,checkin_date) = '" . $departureDate->format('Y-m-d') . "'
                              AND reservation_id <> " . $unit['reservation_id']);
                        if($exist_ea->num_rows() > 0){
                            $this->mdl_general->update('ms_unit', array('unit_id' => $unit['unit_id']), array('hsk_status' => HSK_STATUS::ED_EA));
                        }else{
                            $this->mdl_general->update('ms_unit', array('unit_id' => $unit['unit_id']), array('hsk_status' => HSK_STATUS::ED));
                        }
                    }else{
                        $this->mdl_general->update('ms_unit', array('unit_id' => $unit['unit_id']), array('hsk_status' => HSK_STATUS::OD));
                    }

                    if($valid)
                        $cron_log['affected_rows']++;
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                }
                else
                {
                    $this->db->trans_commit();
                    $cron_log['is_commit'] = 1;
                }
            }else{
                $this->db->trans_rollback();
            }

            //Insert Log
            $this->db->insert('cronjob_log', $cron_log);
        }

        echo 'COMPLETE ROWS -> ' . $cron_log['affected_rows'];
    }

    public function auto_srf_close(){
        $valid = true;

        $today = new DateTime('');

        //CRONJOB LOG
        $cron_log['function_name'] = 'auto_srf_close';
        $cron_log['created_date'] = $today->format('Y-m-d H:i:s');
        $cron_log['last_executed_date'] = $cron_log['created_date'];
        $cron_log['is_commit'] = 0;
        $cron_log['affected_rows']  = 0;

        //$today = $today->format('Y-m-d');

        //BEGIN TRANSACTION
        $this->db->trans_begin();

        $srfs = $this->db->query("SELECT DISTINCT srf.srf_id, srf.unit_id
                FROM cs_srf_detail det
                JOIN cs_srf_header srf ON det.srf_id = srf.srf_id
                WHERE CONVERT(date,det.work_date) < CONVERT(date,GETDATE()) AND srf.status = " . STATUS_APPROVE);

        if($srfs->num_rows() > 0){
            foreach($srfs->result_array() as $srf){
                if($valid){
                    $this->mdl_general->update('cs_srf_header', array('srf_id' => $srf['srf_id']), array('status' => STATUS_CLOSED));

                    $this->mdl_general->update('ms_unit', array('unit_id' => $srf['unit_id']), array('hsk_status' => HSK_STATUS::VD));

                    if($valid)
                        $cron_log['affected_rows']++;
                }
            }
        }

        //COMMIT OR ROLLBACK
        if($valid){
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
            }
            else
            {
                //Insert Log
                $cron_log['is_commit'] = 1;
                $this->db->insert('cronjob_log', $cron_log);

                $this->db->trans_commit();
            }
        }else{
            $this->db->trans_rollback();
        }

        echo 'COMPLETE ROWS -> ' . $cron_log['affected_rows'];
    }

    #endregion

    #region CLOSE HSK STAT

    public function auto_daily_hsk_count(){
        $this->load->model('frontdesk/mdl_frontdesk');

        $cron_log = $this->mdl_frontdesk->exec_daily_hsk_count();

        echo 'COMPLETE ROWS -> ' . $cron_log['affected_rows'];
    }

    public function auto_daily_room_stat(){
        $valid = true;

        //CRONJOB LOG
        $cron_log['function_name'] = 'auto_daily_room_stat';
        $cron_log['created_date'] = date('Y-m-d H:i:s');
        $cron_log['last_executed_date'] = $cron_log['created_date'];
        $cron_log['is_commit'] = 0;
        $cron_log['affected_rows']  = 0;

        $today = new DateTime('now');
        $today = $today->format('Y-m-d');

        $this->load->model('frontdesk/mdl_frontdesk');

        //BEGIN TRANSACTION
        $this->db->trans_begin();

        $stat=array();
        $stat['stat_date'] = $today;

        $valid = $this->mdl_frontdesk->exec_daily_room_stat($today);

        if($valid){
            $cron_log['affected_rows']++;
            $cron_log['is_commit'] = 1;

            //Insert Log
            $this->db->insert('cronjob_log', $cron_log);
        }

        //COMMIT OR ROLLBACK
        if($valid){
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
            }
            else
            {
                $this->db->trans_commit();
            }
        }else{
            $this->db->trans_rollback();
        }

        echo 'COMPLETE ROWS -> ' . $cron_log['affected_rows'];
    }

    #endregion
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */