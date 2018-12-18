<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_frontdesk extends CI_Model{
	
	#region Booking Calculation

    ///Calculate Booking Fees by days
    public function calculate_booking($unitTypeID = 0, $startDateDMY = '', $endDateDMY = '', $reservationType, $isYearlyRate = 0, $billingType = 0){
        $result = array();

        $result['transtype_id'] = 0;
        $result['daily_count'] = 0;
        $result['daily_rate'] = 0;
        $result['daily_amount'] = 0;

        $result['monthly_count'] = 0;
        $result['monthly_rate'] = 0;
        $result['monthly_amount'] = 0;

        $result['yearly_count'] = 0;
        $result['yearly_rate'] = 0;
        $result['yearly_amount'] = 0;

        $result['daily_period_start']='';
        $result['daily_period_end'] ='';
        $result['monthly_period_start'] ='';
        $result['monthly_period_end']='';
        $result['yearly_period_start'] ='';
        $result['yearly_period_end']='';

        $result['formula'] = '';
        $result['total_amount'] = 0;
        $result['tax_rate'] = 0;
        $result['period_caption'] = '';
        $result['min_deposit_amount'] = 0;
        $result['min_receipt_amount'] = 0;
        $result['billing_base'] = BILLING_BASE::DAILY;

        //echo '<br>type : ' . $unitTypeID . ' / ' . $startDateDMY . ' / ' . $endDateDMY;

        if($unitTypeID > 0 && $startDateDMY != '' && $endDateDMY != '' && $reservationType > 0){
            $startDate = DateTime::createFromFormat('d-m-Y', $startDateDMY);
            $endDate = DateTime::createFromFormat('d-m-Y', $endDateDMY);

            $result['daily_period_start']= $startDate->format('Y-m-d');
            $result['daily_period_end'] = $endDate->format('Y-m-d');

            //Calculate days difference
            $diff_day = date_diff($startDate, $endDate, true);
            $totalDays = $diff_day->format('%a');

            $totalYears = 0;
            $totalMonths = num_of_months(dmy_to_ymd($startDateDMY),dmy_to_ymd($endDateDMY));

            $totalYears = floor($totalMonths / 12);
            if($totalYears > 0){
                $totalMonths = $totalMonths - ($totalYears * 12);
            }

            $baseRate = 0;
            $rateTotal = 0;

            $monthly_rate = 0;
            $tax_rate = 0;
            if($totalYears > 0){
                if($billingType == BILLING_TYPE::FULL_PAID) {
                    $rates = $this->db->query("SELECT * FROM fxnRate_Yearly('" . $startDate->format('Y-m-d') . "', " . $totalYears . ", " . $unitTypeID . ")");
                    if ($rates->num_rows() > 0) {
                        $rate = $rates->row();
                        if ($reservationType == RES_TYPE::CORPORATE) {
                            $baseRate = round($rate->rate_corporate_year, 0);
                            $rateTotal = round($rate->total_rate_corporate, 0);
                        } else if ($reservationType == RES_TYPE::PERSONAL) {
                            $baseRate = round($rate->rate_personal_year, 0);
                            $rateTotal = round($rate->total_rate_personal, 0);
                        }

                        $result['yearly_count'] = $totalYears;
                        $result['yearly_rate'] = $baseRate;
                        $result['yearly_amount'] = $rateTotal;
                        $result['yearly_period_start'] = $startDate->format('Y-m-d');
                        $result['yearly_period_end'] = ymd_from_db($rate->end_date);

                        $startDate = DateTime::createFromFormat('Y-m-d', $result['yearly_period_end']);
                        $startDate = $startDate->modify('+1 day');

                        $monthly_rate = round($baseRate / 12, 0);
                        $tax_rate = ($rate->taxtype_percent > 0 ? ($rate->taxtype_percent / 100) : 0);
                        $result['transtype_id'] = $rate->transtype_id;

                        $result['period_caption'] .= $totalYears . "Y";
                        $result['billing_base'] = BILLING_BASE::YEARLY;
                    }
                } else {
                    //Check if Monthly rate or not
                    if($isYearlyRate){
                        //YEARLY
                        $rates = $this->db->query("SELECT * FROM fxnRate_Yearly('" . $startDate->format('Y-m-d') . "', " . $totalYears . ", " . $unitTypeID . ")");
                        if ($rates->num_rows() > 0) {
                            $rate = $rates->row();
                            if ($reservationType == RES_TYPE::CORPORATE) {
                                $baseRate = round($rate->rate_corporate_year, 0);
                                $rateTotal = round($rate->total_rate_corporate, 0);
                            } else if ($reservationType == RES_TYPE::PERSONAL) {
                                $baseRate = round($rate->rate_personal_year, 0);
                                $rateTotal = round($rate->total_rate_personal, 0);
                            }

                            $result['yearly_count'] = $totalYears;
                            $result['yearly_rate'] = $baseRate;
                            $result['yearly_amount'] = $rateTotal;
                            $result['yearly_period_start'] = $startDate->format('Y-m-d');
                            $result['yearly_period_end'] = ymd_from_db($rate->end_date);

                            $startDate = DateTime::createFromFormat('Y-m-d', $result['yearly_period_end']);
                            $startDate = $startDate->modify('+1 day');

                            $monthly_rate = round($baseRate / 12, 0);
                            $tax_rate = ($rate->taxtype_percent > 0 ? ($rate->taxtype_percent / 100) : 0);
                            $result['transtype_id'] = $rate->transtype_id;

                            $result['period_caption'] .= $totalYears . "Y";
                            $result['billing_base'] = BILLING_BASE::YEARLY;
                        }
                    }else{
                        //MONTHLY
                        $rates = $this->db->query("SELECT * FROM fxnRate_Monthly('" . $startDate->format('Y-m-d') . "', " . ($totalYears * 12) . ", " . $unitTypeID . ")");

                        if($rates->num_rows() > 0) {
                            $rate = $rates->row();
                            if($reservationType == RES_TYPE::CORPORATE){
                                $baseRate = round($rate->rate_corporate_month,0);
                                $rateTotal = round($rate->total_rate_corporate, 0);
                            }else if($reservationType == RES_TYPE::PERSONAL){
                                $baseRate = round($rate->rate_personal_month,0);
                                $rateTotal = round($rate->total_rate_personal, 0);
                            }

                            $result['yearly_count'] = $totalYears;
                            $result['yearly_rate'] = round($baseRate * 12,0);
                            $result['yearly_amount'] = $rateTotal;
                            $result['yearly_period_start'] = $startDate->format('Y-m-d');
                            $result['yearly_period_end'] = ymd_from_db($rate->end_date);

                            $startDate = DateTime::createFromFormat('Y-m-d', $result['yearly_period_end']);
                            $startDate = $startDate->modify('+1 day');

                            $monthly_rate = $baseRate;
                            $tax_rate = ($rate->taxtype_percent > 0 ? ($rate->taxtype_percent / 100) : 0);
                            $result['transtype_id'] = $rate->transtype_id;

                            $result['period_caption'] .= $totalYears . "Y";
                            $result['billing_base'] = BILLING_BASE::YEARLY;

                        }
                    }
                }
            }

            $min_deposit_u6month = 0;
            //$baseRate = 0;
            $rateTotal = 0;
            if($totalMonths > 0 && $totalMonths < 12){
                $min_deposit_u6month = 0;
                if($monthly_rate > 0 && $totalYears > 0){
                    $baseRate = $monthly_rate;
                    $rateTotal = round($monthly_rate * $totalMonths, 0);

                    $result['monthly_period_start'] = $startDate->format('Y-m-d');
                    $result['monthly_period_end']= $endDate->format('Y-m-d');
                }else{
                    if($isYearlyRate > 0){
                        $rates = $this->db->query("SELECT * FROM fxnRate_Yearly('" . $startDate->format('Y-m-d') . "', " . $totalYears . ", " . $unitTypeID . ")");
                        if($rates->num_rows() > 0) {
                            $rate = $rates->row();
                            if ($reservationType == RES_TYPE::CORPORATE) {
                                $rateYearly = round($rate->rate_corporate_year, 0);
                                $baseRate = round($rateYearly/12,0);
                            } else if ($reservationType == RES_TYPE::PERSONAL) {
                                $rateYearly = round($rate->rate_personal_year, 0);
                                $baseRate = round($rateYearly/12,0);
                            }

                            $rateTotal = round($totalMonths * $baseRate, 0);

                            $tax_rate = ($rate->taxtype_percent > 0 ? ($rate->taxtype_percent / 100) : 0);
                            $result['transtype_id'] = $rate->transtype_id;

                            $result['monthly_period_start'] = $startDate->format('Y-m-d');
                            $result['monthly_period_end']= ymd_from_db($rate->end_date);
                            $result['billing_base'] = BILLING_BASE::MONTHLY;

                            if($totalMonths < 6){
                                $min_deposit_u6month = $rate->deposit_u6month;
                            }

                            $monthly_rate = $baseRate;
                        }
                    }else {
                        $rates = $this->db->query("SELECT * FROM fxnRate_Monthly('" . $startDate->format('Y-m-d') . "', " . $totalMonths . ", " . $unitTypeID . ")");

                        if($rates->num_rows() > 0) {
                             $rate = $rates->row();

                            if($reservationType == RES_TYPE::CORPORATE){
                                $baseRate = round($rate->rate_corporate_month,0);
                                $rateTotal = round($rate->total_rate_corporate, 0);
                            }else if($reservationType == RES_TYPE::PERSONAL){
                                $baseRate = round($rate->rate_personal_month,0);
                                $rateTotal = round($rate->total_rate_personal, 0);
                            }

                            $tax_rate = ($rate->taxtype_percent > 0 ? ($rate->taxtype_percent / 100) : 0);
                            $result['transtype_id'] = $rate->transtype_id;

                            $result['monthly_period_start'] = $startDate->format('Y-m-d');
                            $result['monthly_period_end']= ymd_from_db($rate->end_date);
                            $result['billing_base'] = BILLING_BASE::MONTHLY;

                            if($totalMonths < 6){
                                $min_deposit_u6month = $rate->deposit_u6month;
                            }

                            $monthly_rate = $baseRate;
                        }
                    }
                }

                $result['monthly_count'] = $totalMonths;
                $result['monthly_rate'] = $baseRate;
                $result['monthly_amount'] = $rateTotal;

                $result['period_caption'] .= ($totalYears > 0 ? ' ' : '') . $totalMonths . "M";

            }

            if($totalYears > 0 || $totalMonths >= 6) {
                $result['min_deposit_amount'] = $monthly_rate;//round(($tax_rate * $monthly_rate) + $monthly_rate,0);;
            }else{
                $result['min_deposit_amount'] = $min_deposit_u6month;
            }

            if($billingType == BILLING_TYPE::FULL_PAID){
                $result['min_receipt_amount'] = round($result['monthly_amount'] + $result['yearly_amount'],0);
                //$result['min_receipt_amount'] = round(($tax_rate * $result['min_receipt_amount']) + $result['min_receipt_amount'],0);
            }else{
                $result['min_receipt_amount'] = $monthly_rate; //round($baseRate + ($tax_rate * $baseRate),0);
            }

            if($reservationType == RES_TYPE::HOUSE_USE){
                $result['monthly_rate'] = 0;
                $result['monthly_amount'] = 0;
                $result['yearly_rate'] = 0;
                $result['yearly_amount'] = 0;
                $result['min_deposit_amount'] = 0;
                $result['min_receipt_amount'] = 0;
                $tax_rate = 0;

                $result['period_caption'] = $totalDays . 'D';
            }

            $result['formula'] = '';
            //$result['formula'] = '(' . $iMonth . ' m x ' . format_num($rate->monthly_rate_member,0) . ') +
            //                                      (' . $iDays . ' d x ' . format_num($rate->daily_rate_member,0) . ')';
            $result['total_amount'] = round($result['monthly_amount'] + $result['yearly_amount'], 0);
            $result['tax_rate'] = $tax_rate;

            //echo 'Qry ' . $this->db->last_query();
        }

        return $result;
    }

    public function calculate_booking_v1($unitTypeID = 0, $startDateDMY = '', $endDateDMY = '', $reservationType, $agentID = 0){
        $result = array();

        $result['transtype_id'] = 0;
        $result['daily_count'] = 0;
        $result['daily_rate'] = 0;
        $result['daily_amount'] = 0;

        $result['monthly_count'] = 0;
        $result['monthly_rate'] = 0;
        $result['monthly_amount'] = 0;

        $result['monthly_period_start'] ='';
        $result['monthly_period_end']='';
        $result['daily_period_start']='';
        $result['daily_period_end'] ='';

        $result['formula'] = '';
        $result['total_amount'] = 0;
        $result['tax_rate'] = 0;

        //echo '<br>type : ' . $unitTypeID . ' / ' . $startDateDMY . ' / ' . $endDateDMY;

        if($unitTypeID > 0 && $startDateDMY != '' && $endDateDMY != '' && $reservationType > 0){
            $startDate = DateTime::createFromFormat('d-m-Y', $startDateDMY);
            $endDate = DateTime::createFromFormat('d-m-Y', $endDateDMY);

            //Calculate days difference
            //$diff_day = date_diff($startDate, $endDate, true);
            //$totalDays = $diff_day->format('%a');
            $totalDays = num_of_days(dmy_to_ymd($startDateDMY),dmy_to_ymd($endDateDMY), RENT_BY_NIGHT);

            if($totalDays > 0 ){
                //IF CALCULATED BY NIGHT End Date must be substract by 1, to eliminate last rate
                if(RENT_BY_NIGHT){
                    $endDate->sub(new DateInterval('P1D'));
                }

                if($agentID > 0){
                    if($reservationType == RES_TYPE::CORPORATE){
                        $rates = $this->db->query("SELECT ISNULL(SUM(ms_rate.rate_corporate),0) as rate,ms_unit_type.transtype_id, ISNULL(tax_type.taxtype_percent, 0) as taxtype_percent
                                           FROM ms_rate
                                           JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_rate.unittype_id
                                           LEFT JOIN tax_type ON tax_type.taxtype_id = ms_unit_type.taxtype_id
                                           WHERE ms_rate.unittype_id = " . $unitTypeID . " AND (CONVERT(date, ms_rate.rate_date) BETWEEN '" . $startDate->format('Y-m-d') . "' AND '" . $endDate->format('Y-m-d') . "')
                                           GROUP BY ms_unit_type.transtype_id, tax_type.taxtype_percent ");
                    }else if($reservationType == RES_TYPE::MEMBER){
                        $rates = $this->db->query("SELECT ISNULL(SUM(ms_rate.rate_member),0) as rate,ms_unit_type.transtype_id, ISNULL(tax_type.taxtype_percent, 0) as taxtype_percent
                                           FROM ms_rate
                                           JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_rate.unittype_id
                                           LEFT JOIN tax_type ON tax_type.taxtype_id = ms_unit_type.taxtype_id
                                           WHERE ms_rate.unittype_id = " . $unitTypeID . " AND (CONVERT(date, ms_rate.rate_date) BETWEEN '" . $startDate->format('Y-m-d') . "' AND '" . $endDate->format('Y-m-d') . "')
                                           GROUP BY ms_unit_type.transtype_id, tax_type.taxtype_percent ");
                    }else{
                        $rates = $this->db->query("SELECT ISNULL(SUM(ms_rate.rate_normal),0) as rate,ms_unit_type.transtype_id, ISNULL(tax_type.taxtype_percent, 0) as taxtype_percent
                                           FROM ms_rate
                                           JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_rate.unittype_id
                                           LEFT JOIN tax_type ON tax_type.taxtype_id = ms_unit_type.taxtype_id
                                           WHERE ms_rate.unittype_id = " . $unitTypeID . " AND (CONVERT(date, ms_rate.rate_date) BETWEEN '" . $startDate->format('Y-m-d') . "' AND '" . $endDate->format('Y-m-d') . "')
                                           GROUP BY ms_unit_type.transtype_id, tax_type.taxtype_percent ");
                    }
                }else{
                    if($totalDays >= 30){
                        if($reservationType == RES_TYPE::CORPORATE){
                            $rates = $this->db->query("SELECT ISNULL(SUM(ms_rate.rate_corporate_monthly),0) as rate,ms_unit_type.transtype_id, ISNULL(tax_type.taxtype_percent, 0) as taxtype_percent
                                           FROM ms_rate
                                           JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_rate.unittype_id
                                           LEFT JOIN tax_type ON tax_type.taxtype_id = ms_unit_type.taxtype_id
                                           WHERE ms_rate.unittype_id = " . $unitTypeID . " AND (CONVERT(date, ms_rate.rate_date) BETWEEN '" . $startDate->format('Y-m-d') . "' AND '" . $endDate->format('Y-m-d') . "')
                                           GROUP BY ms_unit_type.transtype_id, tax_type.taxtype_percent ");
                        }else if($reservationType == RES_TYPE::MEMBER){
                            $rates = $this->db->query("SELECT ISNULL(SUM(ms_rate.rate_member_monthly),0) as rate,ms_unit_type.transtype_id, ISNULL(tax_type.taxtype_percent, 0) as taxtype_percent
                                           FROM ms_rate
                                           JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_rate.unittype_id
                                           LEFT JOIN tax_type ON tax_type.taxtype_id = ms_unit_type.taxtype_id
                                           WHERE ms_rate.unittype_id = " . $unitTypeID . " AND (CONVERT(date, ms_rate.rate_date) BETWEEN '" . $startDate->format('Y-m-d') . "' AND '" . $endDate->format('Y-m-d') . "')
                                           GROUP BY ms_unit_type.transtype_id, tax_type.taxtype_percent ");
                        }else{
                            $rates = $this->db->query("SELECT ISNULL(SUM(ms_rate.rate_normal_monthly),0) as rate,ms_unit_type.transtype_id, ISNULL(tax_type.taxtype_percent, 0) as taxtype_percent
                                           FROM ms_rate
                                           JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_rate.unittype_id
                                           LEFT JOIN tax_type ON tax_type.taxtype_id = ms_unit_type.taxtype_id
                                           WHERE ms_rate.unittype_id = " . $unitTypeID . " AND (CONVERT(date, ms_rate.rate_date) BETWEEN '" . $startDate->format('Y-m-d') . "' AND '" . $endDate->format('Y-m-d') . "')
                                           GROUP BY ms_unit_type.transtype_id, tax_type.taxtype_percent ");
                        }
                    }else{
                        if($reservationType == RES_TYPE::CORPORATE){
                            $rates = $this->db->query("SELECT ISNULL(SUM(ms_rate.rate_corporate),0) as rate,ms_unit_type.transtype_id, ISNULL(tax_type.taxtype_percent, 0) as taxtype_percent
                                           FROM ms_rate
                                           JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_rate.unittype_id
                                           LEFT JOIN tax_type ON tax_type.taxtype_id = ms_unit_type.taxtype_id
                                           WHERE ms_rate.unittype_id = " . $unitTypeID . " AND (CONVERT(date, ms_rate.rate_date) BETWEEN '" . $startDate->format('Y-m-d') . "' AND '" . $endDate->format('Y-m-d') . "')
                                           GROUP BY ms_unit_type.transtype_id, tax_type.taxtype_percent ");
                        }else if($reservationType == RES_TYPE::MEMBER){
                            $rates = $this->db->query("SELECT ISNULL(SUM(ms_rate.rate_member),0) as rate,ms_unit_type.transtype_id, ISNULL(tax_type.taxtype_percent, 0) as taxtype_percent
                                           FROM ms_rate
                                           JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_rate.unittype_id
                                           LEFT JOIN tax_type ON tax_type.taxtype_id = ms_unit_type.taxtype_id
                                           WHERE ms_rate.unittype_id = " . $unitTypeID . " AND (CONVERT(date, ms_rate.rate_date) BETWEEN '" . $startDate->format('Y-m-d') . "' AND '" . $endDate->format('Y-m-d') . "')
                                           GROUP BY ms_unit_type.transtype_id, tax_type.taxtype_percent ");
                        }else{
                            $rates = $this->db->query("SELECT ISNULL(SUM(ms_rate.rate_normal),0) as rate,ms_unit_type.transtype_id, ISNULL(tax_type.taxtype_percent, 0) as taxtype_percent
                                           FROM ms_rate
                                           JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_rate.unittype_id
                                           LEFT JOIN tax_type ON tax_type.taxtype_id = ms_unit_type.taxtype_id
                                           WHERE ms_rate.unittype_id = " . $unitTypeID . " AND (CONVERT(date, ms_rate.rate_date) BETWEEN '" . $startDate->format('Y-m-d') . "' AND '" . $endDate->format('Y-m-d') . "')
                                           GROUP BY ms_unit_type.transtype_id, tax_type.taxtype_percent ");
                        }
                    }
                }
            }

            //echo 'Qry ' . $this->db->last_query();
            if (isset($rates)) {
                if ($rates->num_rows() > 0) {
                    $rate = $rates->row();

                    $rateTotal = round($rate->rate);
                    $tax_rate = ($rate->taxtype_percent > 0 ? ($rate->taxtype_percent / 100) : 0);

                    $result['transtype_id'] = $rate->transtype_id;

                    if($reservationType == RES_TYPE::HOUSE_USE){
                        $rateTotal = 0;
                        $tax_rate = 0;
                    }

                    //echo '<br>Arrival ' .$startDate->format('Y-m-d') . ' Departure ' . $endDate->format('Y-m-d');
                    $result['daily_period_start'] = $startDate->format('d-m-Y');
                    $result['daily_period_end'] = $endDate->format('d-m-Y');

                    $result['daily_count'] = $totalDays;
                    $result['daily_rate'] = 0;
                    $result['daily_amount'] = $rateTotal;

                    $result['formula'] = '';
                    //$result['formula'] = '(' . $iMonth . ' m x ' . format_num($rate->monthly_rate_member,0) . ') +
                    //                                      (' . $iDays . ' d x ' . format_num($rate->daily_rate_member,0) . ')';
                    $result['total_amount'] = $rateTotal;
                    $result['tax_rate'] = $tax_rate;
                }
            }
        }

        return $result;
    }

    #endregion

    #region Close Day
    public function exec_close_day($is_auto = true){
        $valid = true;

        $this->load->model('finance/mdl_finance');

        $closing_date = date('Y-m-d');

        //CRONJOB LOG
        $cron_log['function_name'] = 'auto_close_day';
        if($is_auto){
            $cron_log['created_date'] = date('Y-m-d H:i:s');
            $cron_log['last_executed_date'] = $cron_log['created_date'];
        }else{
            $cron_log['last_executed_date'] = date('Y-m-d H:i:s');
        }

        $cron_log['is_commit'] = 0;
        $cron_log['affected_rows'] = 0;

        //---------------------------------
        // Only For Personal Reservation
        //---------------------------------
        $sales = $this->db->query("SELECT un.unittype_id, h.reservation_code, h.reservation_type, h.arrival_date, h.departure_date, h.agent_id, h.tenant_fullname, d.*, trx.transtype_id, trx.coa_id, un.unit_code, tax.taxtype_percent
                            FROM cs_sales_close d
                            JOIN view_cs_reservation h  ON h.reservation_id = d.reservation_id
                            JOIN ms_unit un             ON un.unit_id = d.unit_id
                            LEFT JOIN ms_unit_type typ  ON typ.unittype_id = un.unittype_id
                            LEFT JOIN ms_transtype trx  ON trx.transtype_id = typ.transtype_id
                            LEFT JOIN tax_type tax      ON tax.taxtype_id = typ.taxtype_id
                            WHERE h.reservation_type = " . RES_TYPE::PERSONAL . " AND d.close_status <= 0 AND h.status IN(" . ORDER_STATUS::CHECKIN . "," . ORDER_STATUS::CHECKOUT . ")
                            AND CONVERT(date, d.close_date) <= '" . $closing_date . "'
                            ORDER BY d.reservation_id, d.unit_id, d.close_date ");

        if($sales->num_rows() > 0){
            //$cron_log['affected_rows'] = $sales->num_rows();

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            foreach($sales->result_array() as $sale){
                if($valid){
                    $tax_percent = $sale['taxtype_percent'] > 0 ? ($sale['taxtype_percent']/100) : 0;
                    $reservationdetail_id = $sale['schedule_detail_id'];

                    $bill = $this->db->query("SELECT cs_bill_header.journal_no, cs_bill_header.bill_id, cs_bill_detail.rate as monthly_rate, cs_bill_detail.disc_amount, cs_bill_detail.month_interval, cs_bill_detail.year_interval
                                              FROM cs_bill_detail
                                              JOIN cs_bill_header ON cs_bill_header.bill_id = cs_bill_detail.bill_id
                                              WHERE cs_bill_header.reservation_id = " . $sale['reservation_id'] . "
                                              AND cs_bill_header.is_other_charge <= 0 AND cs_bill_detail.item_id <= 0
                                              AND (cs_bill_detail.month_interval > 0 OR cs_bill_detail.year_interval > 0) AND cs_bill_detail.unit_id = " . $sale['unit_id'] . "
                                              ");
                    $bill_no ='';
                    $bill_id = 0;
                    $rate_per_month = 0;
                    $disc_per_month = 0;
                    $totalMonth = 0;
                    if($bill->num_rows() > 0){
                        $bill_no = $bill->row()->journal_no;
                        $bill_id = $bill->row()->bill_id;
                        $rate_per_month = $bill->row()->monthly_rate;

                        $nMonth = $bill->row()->month_interval;
                        $nYear = $bill->row()->year_interval;

                        $totalMonth = ($nYear * 12) + $nMonth;

                        $discount = $bill->row()->disc_amount;
                        $disc_per_month = round($discount/$totalMonth,0);
                    }

                    if($bill_no != '' && $bill_id > 0 && $rate_per_month > 0 && $totalMonth > 0){
                        $detail = array();
                        $totalCredit = 0;

                        $journal_desc = $sale['reservation_code'] . ' - ' . $sale['unit_code'] . ' (' . dmy_from_db($sale['close_date']) . ')';

                        //SALES (cr)
                        if($sale['coa_id'] > 0){
                            $amount = $rate_per_month - $disc_per_month;
                            $tax = $tax_percent > 0 ? round($amount * $tax_percent,0) : 0;
                            $subtotal = round($amount + $tax,0);

                            $rowdet = array();
                            $rowdet['coa_id'] = $sale['coa_id'];
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $journal_desc;
                            $rowdet['journal_debit'] = 0;
                            $rowdet['journal_credit'] = $subtotal;
                            $rowdet['transtype_id'] = $sale['transtype_id'];
                            //$rowdet['reference_id'] = '';

                            array_push($detail, $rowdet);

                            $totalCredit += $subtotal;
                        }

                        //BRIDGING
                        $totalDebit = 0;
                        if($totalCredit > 0 && count($detail) > 0){
                            $bridging_sales = FNSpec::get(FNSpec::SALES_BRIDGING_ACCOUNT);
                            if($bridging_sales['coa_id'] > 0){
                                $rowdet = array();
                                $rowdet['coa_id'] = $bridging_sales['coa_id'];
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $journal_desc;
                                $rowdet['journal_debit'] = $totalCredit;
                                $rowdet['journal_credit'] = 0;
                                //$rowdet['reference_id'] = $rsvt['reservation_id'];
                                $rowdet['transtype_id'] = $bridging_sales['transtype_id'];

                                array_push($detail, $rowdet);

                                $totalDebit += $totalCredit;
                            }
                        }

                        if(($totalDebit == $totalCredit) && $totalDebit > 0 && $valid){
                            $header = array();
                            $header['journal_no'] = $bill_no;
                            $header['journal_date'] = $closing_date;
                            $header['journal_remarks'] = $journal_desc;
                            $header['modul'] = GLMOD::GL_MOD_AR;
                            $header['journal_amount'] = $totalDebit;
                            $header['reference'] = '';
                            //$header['reference_date'] = $reservation['reservation_date'];

                            $valid = $this->mdl_finance->postJournal($header,$detail);
                        }else{
                            $valid = false;
                        }

                        //UPDATE Close_Status
                        if($valid){
                            $updated = $this->db->query("UPDATE cs_sales_close SET close_status = " . STATUS_CLOSED . "
                                                   WHERE schedule_detail_id = " . $reservationdetail_id);

                            $cron_log['affected_rows']++;
                        }
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

        return $cron_log;
    }

    public function exec_daily_hsk_count(){
        $valid = true;

        $today = new DateTime('');

        //CRONJOB LOG
        $cron_log['function_name'] = 'auto_daily_hsk_count';
        $cron_log['created_date'] = $today->format('Y-m-d H:i:s');
        $cron_log['last_executed_date'] = $cron_log['created_date'];
        $cron_log['is_commit'] = 0;
        $cron_log['affected_rows']  = 0;

        $today = $today->format('Y-m-d');

        //BEGIN TRANSACTION
        $this->db->trans_begin();

        $units = $this->db->query("SELECT hsk_status, SUM(unit_id) as count_unit FROM ms_unit GROUP BY hsk_status ");

        if($units->num_rows() > 0){
            foreach($units->result_array() as $unit){
                if($valid){
                    $exist = $this->db->get_where('daily_hsk_count', array('created_date' => $today, 'hsk_status' => $unit['hsk_status']));
                    if($exist->num_rows() > 0){
                        $row = $exist->row_array();
                        $this->mdl_general->update('daily_hsk_count', array('id' => $row['id']), array('unit_count' => $unit['count_unit']));
                    }else{
                        $newrow = array();
                        $newrow['hsk_status'] = $unit['hsk_status'];
                        $newrow['created_date'] = $today;
                        $newrow['unit_count'] = $unit['count_unit'];
                        $newrow['status'] = STATUS_NEW;

                        $this->db->insert('daily_hsk_count', $newrow);
                        if($this->db->insert_id() <= 0){
                            $valid = false;
                            break;
                        }
                    }

                    if($valid)
                        $cron_log['affected_rows']++;
                }
            }

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
                $cron_log['is_commit'] = 1;
            }
        }else{
            $this->db->trans_rollback();
        }

        return $cron_log;
    }

    public function exec_daily_room_stat($today = ''){
        $valid = true;

        if($today != ''){
            $this->load->model('finance/mdl_finance');

            $stat['total_room'] = $this->mdl_finance->countJoin('ms_unit',array());
            $stat['hsk_is'] = $this->mdl_finance->countJoin('ms_unit', array(), array('hsk_status' =>HSK_STATUS::IS));
            $stat['hsk_oo'] = $this->mdl_finance->countJoin('ms_unit', array(), array('hsk_status' =>HSK_STATUS::OO));

            //Guest type
            $qry = "SELECT head.reservation_type, count(det.reservation_detail_id) as count_room
                    FROM cs_reservation_detail det
                    JOIN cs_reservation_header head ON head.reservation_id = det.reservation_id
                    WHERE head.status = " . ORDER_STATUS::CHECKIN . " AND CONVERT(date,det.checkin_date) = '" . $today . "'
                    GROUP BY head.reservation_type";

            $rows = $this->db->query($qry);

            $iGuest = 0;
            $iHouseUsed = 0;
            $iCompliment = 0;
            if($rows->num_rows() > 0){
                foreach($rows->result() as $row){
                    if($row->reservation_type == RES_TYPE::HOUSE_USE){
                        $iHouseUsed += $row->count_room;
                    }else {
                        $iGuest += $row->count_room;
                    }
                }
            }
            $stat['res_occupied'] = $iGuest;
            $stat['res_house_use'] = $iHouseUsed;
            $stat['res_compliment'] = $iCompliment;

            //num_of_guest
            $iNumOfGuest = 0;
            $qry = "SELECT ISNULL(SUM(qty_adult + qty_child),0) as sum_qty FROM cs_reservation_header WHERE status = " . ORDER_STATUS::CHECKIN;
            $rows = $this->db->query($qry);
            if($rows->num_rows() > 0){
                $iNumOfGuest = $rows->row()->sum_qty;
            }
            $stat['num_of_guest'] = $iNumOfGuest;

            //Folio Walkin
            $qry = "SELECT head.is_walkin, count(det.reservation_detail_id) as count_room
                    FROM cs_reservation_detail det
                    JOIN cs_reservation_header head ON head.reservation_id = det.reservation_id
                    WHERE head.status = " . ORDER_STATUS::CHECKIN . " AND CONVERT(date,det.checkin_date) = '" . $today . "'
                    GROUP BY head.is_walkin";

            $rows = $this->db->query($qry);

            $iWalkin = 0;
            $iReservation = 0;
            if($rows->num_rows() > 0){
                foreach($rows->result() as $row){
                    if($row->is_walkin > 0){
                        $iWalkin += $row->count_room;
                    }else {
                        $iReservation += $row->count_room;
                    }
                }
            }
            $stat['folio_reserve'] = $iReservation;
            $stat['folio_walk_in'] = $iWalkin;

            $iCancel = 0;

            $qry = "SELECT count(log_id) as count_room FROM app_log WHERE action_type = " . STATUS_CANCEL . "
                    AND feature_id = " . Feature::FEATURE_CS_RESERVATION . " AND CONVERT(date, log_date) = '" . $today . "' ";
            $rows = $this->db->query($qry);
            if($rows->num_rows() > 0){
                $iCancel = $rows->row()->count_room;
            }
            $stat['folio_cancel'] = $iCancel;

            //Room Revenue
            $iRevenueRoom = 0;
            $qry = "SELECT ISNULL(SUM(postdet.journal_credit),0) as sum_credit FROM cs_bill_header bill
                    JOIN gl_postjournal_header head ON head.journal_no = bill.journal_no
                    JOIN gl_postjournal_detail postdet ON postdet.postheader_id = head.postheader_id
                    JOIN cs_sales_close cd ON cd.reservation_id = bill.reservation_id
                    WHERE CONVERT(date,cd.close_date) = '" . $today . "' AND CONVERT(date,head.journal_date) = '" . $today . "'
                    AND postdet.journal_credit > 0 AND bill.is_other_charge <= 0 ";
            $rows = $this->db->query($qry);
            if($rows->num_rows() > 0){
                $iRevenueRoom += $rows->row()->sum_credit;
            }
            $stat['revenue_room'] = $iRevenueRoom;

            //Other Revenue
            $iRevenueOther = 0;
            $qry = "SELECT SUM(postdet.journal_credit) as sum_credit
                    FROM cs_bill_header bill
                    JOIN gl_postjournal_header head ON head.journal_no = bill.journal_no
                    JOIN gl_postjournal_detail postdet ON postdet.postheader_id = head.postheader_id
                    WHERE  CONVERT(date,head.journal_date) = '" . $today . "' AND postdet.journal_credit > 0
                    AND bill.is_other_charge > 0 ";
            $rows = $this->db->query($qry);
            if($rows->num_rows() > 0){
                $iRevenueOther += $rows->row()->sum_credit;
            }
            $stat['revenue_other'] = $iRevenueOther;

            //var_dump($stat);
            $exist = $this->db->get_where('daily_room_stat',array('stat_date' => $today));
            if($exist->num_rows() > 0){
                $stat_id = $exist->row()->stat_id;

                $this->mdl_general->update('daily_room_stat', array('stat_id'=>$stat_id), $stat);
            }else{
                $stat['status'] = STATUS_NEW;
                $this->db->insert('daily_room_stat', $stat);
                if($this->db->insert_id() <= 0){
                    $valid = false;
                }
            }

            //var_dump($stat);
        }else{
            $valid = false;
        }

        return $valid;
    }

    #endregion

}
?>