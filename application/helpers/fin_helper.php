<?php

define('FLAG_CASHBOOK',						88);
define('FLAG_BOOKING_RECEIPT',				77);
define('FLAG_DEPOSIT',				        55);
define('FLAG_REFUND',				        33);
define('FLAG_PENALTY',                      99);

define('PENALTY_GRACE',				        0);

///FINANCE RELATED
/*GL */
class GLClassType
{
    const ASSET = 1;
    const LIABILITY = 2;
    const CAPITAL = 3;
    const INCOME = 4;
    const EXPENSE = 5;

    /*COA Class Type*/
    function class_type_name($classType = 0){
        $result = '';

        switch ($classType)
        {
            case GLClassType::ASSET:
                $result = 'Asset';
                break;
            case GLClassType::LIABILITY:
                $result = 'Liability';
                break;
            case GLClassType::CAPITAL:
                $result = 'Capital';
                break;
            case GLClassType::INCOME:
                $result = 'Income';
                break;
            case GLClassType::EXPENSE:
                $result = 'Expense';
                break;
            default:
                $result='';
                break;
        }
        return $result;
    }
}

class GLMOD
{
    const GL_MOD_JE = 'GJE';
    const GL_MOD_CASH = 'CASH';
    const GL_MOD_AR = 'AR';
    const GL_MOD_AP = 'AP';
    const GL_MOD_POS = 'POS';
    const GL_MOD_DO = 'DO';
    const GL_MOD_INV = 'INV';
}

class BILLING_BASE
{
    const DAILY = 0;
    const MONTHLY = 1;
    const YEARLY = 2;
}

/*FN Spec*/
class FNSpec
{
    const STAMP_DUTY                = 100;
    const BANK_CHARGE               = 101;
    const FIN_AP_GRN_WIP            = 102;
    const FIN_AP_GRN                = 103;
    const FIN_AP_INVOICE            = 104;
    const FIN_AP_PREPAID_TAX        = 105;
    const FIN_AP_VAT_IN_EXP         = 106;
    const FIN_AP_DEBIT_NOTE         = 107;
    const FIN_AP_CREDIT_NOTE        = 108;
    const FIN_AP_FOREX_GAIN         = 109;
    const FIN_AP_PAYMENT_ADV        = 110;
    const FIN_AP_STOCK_ADJUSTMENT   = 111;
    const TRADE_RECEIVABLES         = 112;
    const TRADE_RECEIVABLES_CORP    = 120;
    //const AR_BRIDGING_ACCOUNT       = 121;
    const CREDIT_CARD               = 113;
    const POS_SUPPLIES              = 114;
    const POS_EXPENSE               = 115;
    const PAYMENT_GATEWAY_CHARGE    = 116;
    const SALES_RESERVATION         = 117;
    const SALES_BRIDGING_ACCOUNT    = 118;
    const SALES_HOUSEKEEPING        = 119;
    const CLOSE_MONTH               = 300;
    const CLOSE_YEAR                = 301;
    const TAX_VAT                   = 401;
    const INTEREST                  = 402;
    const ITEM_TRANSIT              = 403;
    const UNEARNED_INCOME           = 404;

    function get($key = 0) {
        $CI =& get_instance();

        $result = array();
        if($key > 0){
            $qry = $CI->db->query("SELECT fn_feature_spec.*, ISNULL(gl_coa.coa_code,'') as coa_code, ISNULL(gl_coa.coa_desc,'') as coa_desc FROM fn_feature_spec
                                   LEFT JOIN gl_coa ON fn_feature_spec.coa_id = gl_coa.coa_id
                                   WHERE fn_feature_spec.spec_key = " . $key);
            if($qry->num_rows() > 0){
                $row = $qry->row();
                array_push($result, array('feature_id'=>$row->feature_id, 'spec_key'=>$row->spec_key, 'transtype_id'=>$row->transtype_id,
                                          'coa_id'=>$row->coa_id, 'description'=>$row->description, 'coa_code' => $row->coa_code, 'coa_desc' => $row->coa_desc));
            }else{
                array_push($result, array('feature_id'=>0, 'spec_key'=>0, 'transtype_id'=>0,
                'coa_id'=>0, 'description'=>0, 'coa_code' => '', 'coa_desc' => ''));
            }
        }else{
            array_push($result, array('feature_id'=>0, 'spec_key'=>0, 'transtype_id'=>0,
                'coa_id'=>0, 'description'=>0, 'coa_code' => '', 'coa_desc' => ''));
        }

        return $result[0];
    }
}

class PAYMENT_TYPE
{
    const CREDIT_CARD = 10;
    const BANK_TRANSFER = 20;
    const CASH_ONLY = 30;
    const DEBIT_CARD = 40;
    const AR_TRANSFER = 50;
    const PAYMENT_GATEWAY = 60;

    function caption($status = 0){
        $result = '';

        if($status == PAYMENT_TYPE::CREDIT_CARD){
            $result = 'Credit Card';
        }else if($status == PAYMENT_TYPE::BANK_TRANSFER){
            $result = 'Bank';
        }else if($status == PAYMENT_TYPE::CASH_ONLY){
            $result = 'Cash';
        }else if($status == PAYMENT_TYPE::DEBIT_CARD){
            $result = 'Debit Card';
        }else if($status == PAYMENT_TYPE::AR_TRANSFER){
            $result = 'AR Transfer';
        }else if($status == PAYMENT_TYPE::PAYMENT_GATEWAY){
            $result = 'Payment Gateway';
        }

        return $result;
    }
}

class GLStatement
{
    const BALANCE_SHEET = 1;
    const PROFIT_LOSS = 2;
    const CASH_FLOW = 3;

    function get_layout_by_type($type = 0) {
        $CI =& get_instance();

        $result = array();
        if($type > 0){
            $qry = $CI->db->query("SELECT     st.*, ctg.category_caption, ctg.report_type, ctg.category_key, sub.subcategory_caption, sub.category_id
                                   FROM gl_financestatement_detail st
                                   LEFT JOIN gl_financestatement_subcategory sub ON st.subcategory_id = sub.subcategory_id
                                   LEFT JOIN gl_financestatement_category ctg ON sub.category_id = ctg.category_id
                                   WHERE ctg.report_type = " . $type . "
                                   ORDER BY st.account_pos, ctg.category_id, st.subcategory_id");
            if($qry->num_rows() > 0){
                foreach($qry->result() as $row){
                    array_push($result,
                        array('layout_id'=>$row->detail_id,
                            'subctg_id'=>$row->subcategory_id,
                            'account_name'=>$row->account_name,
                            'account_pos'=>$row->account_pos,
                            'is_cashflow'=>$row->iscashflow,
                            'is_rangedformula'=>$row->israngedformula,
                            'range_start' => $row->rangeformula_start,
                            'range_end' => $row->rangeformula_end,
                            'text_formula' => $row->text_formula,
                            'statement_type' => $row->report_type,
                            'ctg_caption' => $row->category_caption,
                            'subctg_caption' => $row->subcategory_caption,
                            'range_start' => $row->rangeformula_start,
                            'ctg_id' => $row->category_id,
                            'ctg_key' => $row->category_key
                        ));
                }
            }
        }

        return $result;
    }
}

Class REPORTTYPE
{
    const STATEMENT_TRIAL_BALANCE = 100;
    const STATEMENT_BALANCE_SHEET = 101;
    const STATEMENT_PROFIT_LOSS_STD = 102;
    const STATEMENT_PROFIT_LOSS_BUDGET = 103;
    const STATEMENT_PROFIT_LOSS_DEPT = 104;
    const STATEMENT_TRIAL_CASHFLOW = 105;
    const STATEMENT_CASHFLOW = 106;
}

Class PLGROUP {
    const GROSS_PROFIT = 'GROSS OPERATION PROFIT (GOP)';
    const NET_PROFIT = 'NET PROFIT (LOSS)';
}

Class PLTYPE{
    const OP_INCOME = 'OPERATING INCOME';
    const OP_EXPENSE = 'OPERATING EXPENSE';
    const NON_OP_INCOME = 'NON-OPERATING INCOME';
    const NON_OP_EXPENSE = 'NON-OPERATING EXPENSE';
}

function creditcard_paymenttypeid() {
    $CI =& get_instance();

    $creditCardId = 0;
    $paymentypes = $CI->db->query('SELECT paymenttype_id FROM ms_payment_type WHERE payment_type = ' . PAYMENT_TYPE::CREDIT_CARD . ' AND status = ' . STATUS_NEW);
    if($paymentypes->num_rows() > 0){
        $creditCardId = $paymentypes->row()->paymenttype_id;
    }

    return $creditCardId;
}

function bank_paymenttypeid() {
    $CI =& get_instance();

    $paymenttype_id = 0;
    $paymentypes = $CI->db->query('SELECT paymenttype_id FROM ms_payment_type WHERE payment_type = ' . PAYMENT_TYPE::BANK_TRANSFER . ' AND status = ' . STATUS_NEW);
    if($paymentypes->num_rows() > 0){
        $paymenttype_id = $paymentypes->row()->paymenttype_id;
    }

    return $paymenttype_id;
}

function tax_vat() {
    $CI =& get_instance();

    $vat = array();

    $qry = $CI->db->get_where('tax_type', array('is_charge_default >' => 0));
    if($qry->num_rows() > 0){
        return $qry->row_array();
    }

    return $vat;
}

function min_input_date() {
    $CI =& get_instance();

    $period = $CI->db->query('SELECT * FROM gl_period');
    $minDate = date('Y-m-d');
    if($period->num_rows() > 0){
        $periodYear = $period->row()->period_year;
        $periodMonth = $period->row()->period_month;

        $minDate = date('d-m-Y', mktime(0,0,0,$periodMonth,1,$periodYear));
    }else{
        $closing = $CI->db->query('SELECT TOP 1 * FROM gl_closing_header  where is_yearly <= 0  AND status =' . STATUS_NEW . ' order by closingyear DESC, closingmonth DESC');
        if($closing->num_rows() > 0){
            $periodMonth = ($closing->row()->closingmonth) + 1;
            $periodYear = $closing->row()->closingyear;

            if($periodMonth > 12){
                $periodMonth = 1;
                $periodYear++;
            }

            $minDate = date('d-m-Y', mktime(0,0,0,$periodMonth,1,$periodYear));
        }
    }
    return $minDate;
}

function max_input_date() {
    $maxDate = date('t-m-Y', strtotime(date('Y-m-d')));
    return $maxDate;
}

function picker_input_date($is_min_date = true, $is_max_date = true, $min_date_set = '', $class_name = 'date-picker', $orientation = 'right') {
    if ($is_min_date) {

        $CI =& get_instance();

        $period = $CI->db->query('SELECT * FROM gl_period');
        //$minDate = date('Y-m-d');
        if ($period->num_rows() > 0) {
            $periodYear = $period->row()->period_year;
            $periodMonth = $period->row()->period_month;

            $minDate_ = date('d-m-Y', mktime(0, 0, 0, $periodMonth, 1, $periodYear));
        } else {
            $closing = $CI->db->query('SELECT TOP 1 * FROM gl_closing_header  where is_yearly <= 0  AND status =' . STATUS_NEW . ' order by closingyear DESC, closingmonth DESC');
            if ($closing->num_rows() > 0) {
                $periodMonth = ($closing->row()->closingmonth) + 1;
                $periodYear = $closing->row()->closingyear;

                if ($periodMonth > 12) {
                    $periodMonth = 1;
                    $periodYear++;
                }

                $minDate_ = date('d-m-Y', mktime(0, 0, 0, $periodMonth, 1, $periodYear));
            } else {
                $is_min_date = false;
            }
        }

        if ($is_min_date) {
            if ($min_date_set == '') {
                $minDate = $minDate_;
            } else {
                if (strtotime($min_date_set) > strtotime($minDate_)) {
                    $minDate = $minDate_;
                } else {
                    $minDate = $min_date_set;
                }
            }
        }
    }
    if ($is_max_date) {
        $maxDate = date('t-m-Y', strtotime(date('Y-m-d')));
    }

    $out = "if (jQuery().datepicker) {" .
        "$('." . $class_name . "').datepicker({
            rtl: Metronic.isRTL(),
            orientation: '". $orientation . "',
            " . ($is_min_date ? "startDate:'" . $minDate . "'," : "") . "
            " . ($is_max_date ? "endDate:'" . $maxDate . "'," : "") .
            "autoclose: true
            });
        }";

    return $out;
}

?>