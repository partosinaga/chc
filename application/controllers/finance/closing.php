<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Closing extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_login()) {
            redirect(base_url('login/login_form.tpd'));
        }

        $this->data_header = array(
            'style' => array(),
            'script' => array(),
            'custom_script' => array(),
            'init_app' => array()
        );

        $this->data_footer = array(
            'footer_script' => array()
        );
    }

    public function index()
    {
        $this->closing_form();
    }

    #region CLOSING ----

    public function closing_form()
    {
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

        $data = array();

        $data['close_month'] = $this->closeMonthList();
        $data['close_year'] = $this->closeYearList();

        //YEARLY CLOSING

        $data['close_account'] = array('month' => FNSpec::get(FNSpec::CLOSE_MONTH), 'year' => FNSpec::get(FNSpec::CLOSE_YEAR));

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/closing/closing_form', $data);
        $this->load->view('layout/footer');
    }

    private function closeMonthList()
    {
        $qry = "SELECT * FROM gl_closing_header
                WHERE is_yearly <= 0 AND status = " . STATUS_NEW . "
                ORDER BY closingyear DESC, closingmonth DESC ";
        $monthClose = $this->db->query($qry);

        $m = 1;
        $y = date('Y');
        if ($monthClose->num_rows() > 0) {
            $m = $monthClose->row()->closingmonth + 1;
            $y = $monthClose->row()->closingyear;

            if ($m > 12) {
                $m = 1;
                $y++;
            }
        }

        $minCloseDate = '01' . '-' . $m . '-' . $y;

        $startDate = DateTime::createFromFormat('d-m-Y', $minCloseDate);

        $minMonth = $startDate->format('m');
        $minYear = $startDate->format('Y');

        //MONTH
        $listMonth = array();
        $maxMonth = 12;
        if ($minYear == date('Y')) {
            $maxMonth = date('m');
        }

        for ($i = $minMonth; $i <= $maxMonth; $i++) {
            $sMonth = (strlen($i) > 1 ? $i : '0' . $i);
            $listMonth[] = $sMonth;
        }

        //YEAR
        $listYear = array();

        if ($monthClose->num_rows() <= 0) {
            array_push($listYear, $minYear);
            array_push($listYear, $minYear - 1);
            array_push($listYear, $minYear - 2);
        } else {
            array_push($listYear, $minYear);
        }

        $result = array('month' => $listMonth, 'year' => $listYear);

        return $result;
    }

    private function closeYearList()
    {
        $result = array();

        $currentYear = date('Y');

        $qry = "SELECT * FROM gl_closing_header
                WHERE is_yearly > 0 AND status = " . STATUS_NEW . "
                ORDER BY closingyear DESC, closingmonth DESC ";
        $yearClose = $this->db->query($qry);
        if ($yearClose->num_rows() > 0) {
            $row = $yearClose->row();
            array_push($result, $row->closingyear);
        } else {
            $qry = "SELECT closingheader_id, closingyear FROM gl_closing_header
                WHERE is_yearly <= 0 AND status = " . STATUS_NEW . "
                ORDER BY closingyear DESC, closingmonth DESC ";
            $monthClose = $this->db->query($qry);

            if ($monthClose->num_rows() > 0) {
                array_push($result, $monthClose->row()->closingyear);
            } else {
                array_push($result, $currentYear);
                array_push($result, $currentYear - 1);
            }
        }

        return $result;
    }

    /*
    private function closeMonthList(){
        $qry = "SELECT * FROM gl_closing_header
                WHERE is_yearly <= 0 AND status = " . STATUS_NEW . "
                ORDER BY closingyear DESC, closingmonth DESC ";
        $monthClose = $this->db->query($qry);

        $m = 1;
        $y = date('Y');
        if($monthClose->num_rows() > 0){
            $m = $monthClose->row()->closingmonth + 1;
            $y = $monthClose->row()->closingyear;

            if($m > 12){
                $m = 1;
                $y++;
            }
        }

        $minCloseDate = '01'. '-' .$m . '-' . $y;

        $startDate = DateTime::createFromFormat('d-m-Y', $minCloseDate);

        $minMonth = $startDate->format('m');
        $minYear = $startDate->format('Y');

        //MONTH
        $listMonth = array();
        $maxMonth = 12;
        if($minYear == date('Y')){
            $maxMonth = date('m');
        }

        for($i=$minMonth;$i<=$maxMonth;$i++){
            $sMonth = (strlen($i) > 1 ? $i : '0'. $i);
            $listMonth[] = $sMonth;
        }

        //YEAR
        $listYear = array();

        if($monthClose->num_rows() <= 0){
            array_push($listYear, $minYear);
            array_push($listYear, $minYear - 1);
            array_push($listYear, $minYear - 2);
        }else{
            array_push($listYear, $minYear);
        }

        $result=array('month' => $listMonth, 'year' => $listYear);

        return $result;
    }

    private function closeYearList(){
        $result = array();

        $currentYear = date('Y');

        $qry = "SELECT * FROM gl_closing_header
                WHERE is_yearly > 0 AND status = " . STATUS_NEW . "
                ORDER BY closingyear DESC, closingmonth DESC ";
        $yearClose = $this->db->query($qry);
        if($yearClose->num_rows() > 0){
            $row = $yearClose->row();
            array_push($result, $row->closingyear);
        }else{
            $qry = "SELECT closingheader_id, closingyear FROM gl_closing_header
                WHERE is_yearly <= 0 AND status = " . STATUS_NEW . "
                ORDER BY closingyear DESC, closingmonth DESC ";
            $monthClose = $this->db->query($qry);

            if($monthClose->num_rows() > 0){
                array_push($result, $monthClose->row()->closingyear);
            }else{
                array_push($result, $currentYear);
                array_push($result, $currentYear-1);
            }
        }

        return $result;
    }*/

    #region MONTHLY
    //CLOSING MONTH
    public function submit_close_month()
    {
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';
        $debug = '';

        $valid = true;
        $periodYear = 0;
        $periodMonth = 0;

        if (isset($_POST['period_year'])) {
            $periodYear = $_POST['period_year'];
        }
        if (isset($_POST['period_month'])) {
            $periodMonth = $_POST['period_month'];
        }

        if ($periodYear > 0 && $periodMonth > 0) {
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $specMonth = FNSpec::get(FNSpec::CLOSE_MONTH);
            if ($specMonth['coa_id'] > 0) {
                $currentDate = date('Y-m-d');

                $closingDate = DateTime::createFromFormat('Y-m-d', $periodYear . '-' . $periodMonth . '-' . days_in_month($periodMonth, $periodYear));
                $posting_date = $closingDate->format('Y-m-d');

                try {
                    $plAmount = $this->getProfitLossAmount($periodMonth, $periodYear, false);
                    $debug .= 'PL ' . $plAmount;

                    $header['closingdate'] = $closingDate->format('Y-m-d');
                    $header['closingyear'] = $closingDate->format('Y');
                    $header['closingmonth'] = $closingDate->format('m');
                    $header['coa_code'] = $specMonth['coa_code'];
                    $header['is_yearly'] = 0;
                    $header['balance'] = $plAmount;
                    $header['status'] = STATUS_NEW;

                    $this->db->insert('gl_closing_header', $header);
                    $header['closingheader_id'] = $this->db->insert_id();

                    if ($header['closingheader_id'] > 0) {
                        //POSTING PROFIT LOSS
                        $detail = array();

                        $journal_note = "Closing Month - " . $periodMonth . '/' . $periodYear;

                        //PROFIT/LOSS detail
                        $profitloss = array();
                        $profitloss['coa_id'] = $specMonth['coa_id'];
                        $profitloss['coa_code'] = $specMonth['coa_code'];
                        $profitloss['journal_note'] = $journal_note;
                        $profitloss['dept_id'] = 0;
                        if ($plAmount < 0) {
                            //LOSS
                            $profitloss['journal_debit'] = abs($plAmount);
                            $profitloss['journal_credit'] = 0;
                        } else {
                            //PROFIT
                            $profitloss['journal_credit'] = abs($plAmount);
                            $profitloss['journal_debit'] = 0;
                        }

                        $profitloss['reference_id'] = $header['closingheader_id'];
                        $profitloss['transtype_id'] = $specMonth['transtype_id'];

                        array_push($detail, $profitloss);

                        //PROFIT/LOSS header
                        $plheader = array();
                        $plheader['journal_no'] = '-';
                        $plheader['journal_date'] = $posting_date;
                        $plheader['journal_remarks'] = $journal_note;
                        $plheader['modul'] = GLMOD::GL_MOD_JE;
                        $plheader['journal_amount'] = abs($plAmount);
                        $plheader['reference'] = '';

                        $valid = $this->mdl_finance->postJournal($plheader, $detail);
                    } else {
                        $valid = false;
                    }

                    //CLOSE EVERY COA
                    if ($valid) {
                        $valid = $this->closeMonthCOAEntries($header);
                    }

                    //CLOSE CASH FLOW
                    if ($valid) {
                        $closeCF = $this->closeMonthCFEntries($header);
                        $valid = $closeCF['valid'];
                        if (!$valid) {
                            $debug = $closeCF['debug'];
                        }
                    }

                    //UPDATE GL PERIOD
                    if ($valid) {
                        $this->db->query("UPDATE gl_period SET period_year = " . date('Y') . ", period_month = " . date('m'));
                    }

                } catch (Exception $e) {
                    $valid = false;
                }
            } else {
                $debug = '\nCOA Code not found';
                $valid = false;
            }

            $result['debug'] = $debug;

            //COMMIT OR ROLLBACK
            if ($valid) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Close Month can not be posted. Please try again later.';
                } else {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = 'Close Month ' . $periodMonth . '-' . $periodYear . ' successfully posted.';
                    $result['redirect_link'] = base_url('finance/closing/closing_form.tpd');

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', $result['message']);
                }
            } else {
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Close Month can not be posted. Please try again later.';
            }
        }

        echo json_encode($result);
    }

    private function closeMonthCOAEntries($closing_header = array())
    {
        $valid = true;

        if (count($closing_header) > 0) {
            $qry = $this->db->query("SELECT DISTINCT Convert(varchar(max),STUFF((SELECT ',' + a.coa_code FROM gl_coa a FOR XML PATH ('')), 1, 1, '')) as coa_code_comma FROM gl_coa ");
            $coa_comma = '';
            if ($qry->num_rows() > 0) {
                $coa_comma = $qry->row()->coa_code_comma;
            }

            if ($coa_comma != '') {
                $prev = $this->db->query("SELECT * FROM fxnGL_COA_Prev('" . $closing_header['closingdate'] . "','" . $coa_comma . "')");
                $prev_table = $prev->result_array();

                $current = $this->db->query("SELECT * FROM fxnGL_COA_Balance_By_Month(" . $closing_header['closingmonth'] . "," . $closing_header['closingyear'] . ",'" . $coa_comma . "') ");
                $current_table = $current->result_array();

                foreach ($current_table as $current_row) {
                    if ($valid) {
                        $current_coacode = $current_row['coa_code'];
                        $current_balance = $current_row['balance'];

                        //Save
                        $close['closingheader_id'] = $closing_header['closingheader_id'];
                        $close['coa_code'] = $current_coacode;
                        $close['debit'] = $current_row['debit'];
                        $close['credit'] = $current_row['credit'];
                        $close['balance'] = $current_balance;
                        $close['status'] = STATUS_NEW;

                        $this->db->insert('gl_closing_detail', $close);
                        if ($this->db->insert_id() <= 0) {
                            $valid = false;
                            break;
                        }
                    }
                }
            }
        }

        return $valid;
    }

    private function closeMonthCFEntries($closing_header = array())
    {
        $valid = true;
        $message = array();
        $debug = array();

        if (count($closing_header) > 0) {
            try {
                $closingHeaderID = $closing_header['closingheader_id'];

                $specARPersonal = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                $specARCorporate = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                if ($specARPersonal['coa_id'] > 0 && $specARCorporate['coa_id'] > 0) {
                    $curr = $this->db->get_where('currencytype', array('currencytype_code' => 'IDR'));
                    $currencytype_id = 0;
                    if ($curr->num_rows() > 0) {
                        $currencytype_id = $curr->row()->currencytype_id;
                    }

                    $qry = "SELECT PostHeader_ID, Journal_Date, Modul, Journal_No, COA_ID, COA_Code, Debit, Credit
                        FROM _tempCashFlowJournal
                        WHERE PostedYear = " . $closing_header['closingyear'] . " AND PostedMonth = " . $closing_header['closingmonth'];

                    $temp = $this->db->query($qry);

                    //array_push($debug, 'CF Closing . ' .$this->db->last_query());

                    if ($temp->num_rows() > 0) {
                        //Official Receipt
                        $doc_RV_length = 2;
                        $doc_RV = $this->db->get_where('document', array('feature_id' => Feature::FEATURE_AR_RECEIPT));
                        if ($doc_RV->num_rows() > 0) {
                            $doc_RV = $doc_RV->row()->doc_name;
                            $doc_RV_length = strlen($doc_RV);
                        }

                        //Payment Voucher
                        $doc_PV_length = 2;
                        $doc_PV = $this->db->get_where('document', array('feature_id' => Feature::FEATURE_AP_PAYMENT));
                        if ($doc_PV->num_rows() > 0) {
                            $doc_PV = $doc_PV->row()->doc_name;
                            $doc_PV_length = strlen($doc_PV);
                        }

                        $specBankCharge = FNSpec::get(FNSpec::BANK_CHARGE);

                        $temp_cf = $temp->result_array();
                        foreach ($temp_cf as $v) {
                            if ($valid) {
                                $postCOACode = $v['COA_Code'];
                                $postCOAID = $v['COA_ID'];
                                $postModul = strtoupper($v['Modul']);
                                $postHeaderID = $v['PostHeader_ID'];
                                $postDate = ymd_from_db($v['Journal_Date']);
                                $journalNo = $v['Journal_No'];
                                $postDebit = $v['Debit'];
                                $postCredit = $v['Credit'];

                                if ($postModul == GLMOD::GL_MOD_AR) {
                                    $doc_initial = substr($journalNo, 0, $doc_RV_length);
                                    if ($doc_initial == $doc_RV) {
                                        $ar_receipt = $this->db->get_where('ar_receipt', array('receipt_no' => $journalNo));
                                        if ($ar_receipt->num_rows() > 0) {
                                            $ar_receipt = $ar_receipt->row();
                                            if ($ar_receipt->status == FLAG_DEPOSIT) {
                                                //--- MAIN DEPOSIT RECEIPT ---
                                                $deposit_rv = $this->db->query("SELECT det.detail_id, ISNULL(head.reservation_id,0) as reservation_id, head.bankaccount_id, head.deposit_desc, ISNULL(tn.company_name,'-') as company_name , dty.coa_code, ISNULL(cs.tenant_fullname,'-') as guest_name, ISNULL(cs.company_name,'-') as company_name2
                                                                FROM ar_deposit_detail det
                                                                JOIN ms_deposit_type dty ON dty.deposittype_id = det.deposittype_id
                                                                JOIN ar_deposit_header head ON det.deposit_id = head.deposit_id
                                                                LEFT JOIN view_cs_reservation cs ON cs.reservation_id = head.reservation_id
                                                                LEFT JOIN ms_company tn ON tn.company_id = head.company_id
                                                                WHERE head.deposit_no = '" . $journalNo . "'");

                                                if ($deposit_rv->num_rows() > 0) {
                                                    $deposit_rv = $deposit_rv->row();
                                                    if ($postDebit > 0) {
                                                        if ($deposit_rv->reservation_id > 0) {
                                                            $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $deposit_rv->coa_code, $postDate, $deposit_rv->bankaccount_id, $journalNo, $journalNo, true, $currencytype_id, 1, $postDebit, $postDebit, $deposit_rv->company_name2);
                                                        } else {
                                                            $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $deposit_rv->coa_code, $postDate, $deposit_rv->bankaccount_id, $journalNo, $journalNo, true, $currencytype_id, 1, $postDebit, $postDebit, $deposit_rv->company_name);
                                                        }
                                                    }
                                                }

                                                //---END DEPOSIT RECEIPT ---
                                            } else {
                                                //--- MAIN CORPORATE RECEIPT ---
                                                if ($postDebit > 0) {
                                                    $corporate_rv = $this->db->query("SELECT det.coa_code, det.journal_credit, rec.bankaccount_id, ISNULL(tn.company_name,'-') as company_name, ISNULL(cs.tenant_fullname,'-') as guest_name, rec.company_id
                                                    FROM gl_postjournal_detail det
                                                    JOIN gl_postjournal_header head ON head.postheader_id = det.postheader_id
                                                    JOIN ar_receipt rec ON rec.receipt_no = head.journal_no
                                                    LEFT JOIN ms_company tn ON tn.company_id = rec.company_id
                                                    LEFT JOIN view_cs_reservation cs ON cs.reservation_id = rec.reservation_id
                                                    WHERE det.journal_credit > 0 AND head.journal_no = '" . $journalNo . "'");

                                                    if ($corporate_rv->num_rows() > 0) {
                                                        $corporate_rv = $corporate_rv->row();

                                                        $subjectName = $corporate_rv->company_name;
                                                        if ($corporate_rv->company_id <= 0) {
                                                            $subjectName = $corporate_rv->guest_name;
                                                        }

                                                        $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $corporate_rv->coa_code, $postDate, $corporate_rv->bankaccount_id, $journalNo, $journalNo, true, $currencytype_id, 1, $postDebit, $postDebit, $subjectName);
                                                    }
                                                }

                                                //--- END CORPORATE RECEIPT ---
                                            }
                                        } else {
                                            $valid = false;
                                            break;
                                        }
                                    } else {
                                        //PV For Refund
                                        if ($postCredit > 0) {
                                            array_push($debug, "A4. OFR Refund " . $valid);

                                            //REFUND RECEIPT
                                            $refunds = $this->db->query("SELECT d.*, pv.bankaccount_id, pv.company_id,  ISNULL(cs.tenant_fullname,'') as tenant_fullname, ISNULL(cp.company_name,'') as company_name
                                                            FROM gl_postjournal_detail d
                                                            JOIN gl_postjournal_header h ON h.postheader_id = d.postheader_id
                                                            JOIN ar_receiptrefund_header pv ON h.journal_no = pv.refund_no
                                                            LEFT JOIN view_cs_reservation cs ON cs.reservation_id = pv.reservation_id
                                                            LEFT JOIN ms_company cp ON cp.company_id = pv.company_id
                                                            WHERE h.journal_no = '" . $journalNo . "' AND d.journal_debit > 0");

                                            if ($refunds->num_rows() > 0) {
                                                array_push($debug, "A4.1 OFR Refund " . $journalNo . ' - ' . $refunds->num_rows());

                                                foreach ($refunds->result_array() as $receipt_pv) {
                                                    if ($valid) {
                                                        $tenantSubject = $receipt_pv['company_id'] > 0 ? $receipt_pv['company_name'] : $receipt_pv['tenant_fullname'];
                                                        $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $receipt_pv['coa_code'], $postDate, $receipt_pv['bankaccount_id'], $journalNo, $receipt_pv['journal_note'], false, $currencytype_id, 1, $receipt_pv['journal_debit'], $receipt_pv['journal_debit'], $tenantSubject);
                                                    } else {
                                                        break;
                                                    }
                                                }
                                            } else {
                                                array_push($debug, "A4.2 OFR Refund " . $journalNo . ' - ' . $refunds->num_rows());

                                                //REFUND DEPOSIT
                                                $refunds = $this->db->query("SELECT d.*, pv.bankaccount_id, pv.company_id, ISNULL(cs.tenant_fullname,'') as tenant_fullname, ISNULL(cp.company_name,'') as company_name
                                                            FROM gl_postjournal_detail d
                                                            JOIN gl_postjournal_header h ON h.postheader_id = d.postheader_id
                                                            JOIN ar_depositrefund_header pv ON pv.refund_no = h.journal_no
                                                            LEFT JOIN view_cs_reservation cs ON cs.reservation_id = pv.reservation_id
                                                            LEFT JOIN ms_company cp ON cp.company_id = pv.company_id
                                                            WHERE h.journal_no = '" . $journalNo . "' AND d.journal_debit > 0");
                                                if ($refunds->num_rows() > 0) {
                                                    foreach ($refunds->result_array() as $deposit_pv) {
                                                        if ($valid) {
                                                            $tenantSubject = $deposit_pv['company_id'] > 0 ? $deposit_pv['company_name'] : $deposit_pv['tenant_fullname'];

                                                            $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $deposit_pv['coa_code'], $postDate, $deposit_pv['bankaccount_id'], $journalNo, $deposit_pv['journal_note'], false, $currencytype_id, 1, $deposit_pv['journal_debit'], $deposit_pv['journal_debit'], $tenantSubject);
                                                        } else {
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else if ($postModul == GLMOD::GL_MOD_CASH) {
                                    //CASH BOOK
                                    $postDetails = $this->db->get_where('gl_postjournal_detail', array('postheader_id' => $postHeaderID));
                                    $cashHeaders = $this->db->get_where('gl_cashentry_header', array('journal_no' => $journalNo));
                                    $bankAccount = $this->db->get_where('fn_bank_account', array('coa_id' => $postCOAID, 'status' => STATUS_NEW));

                                    if ($bankAccount->num_rows() > 0) {
                                        $bankAccountID = $bankAccount->row()->bankaccount_id;
                                    }

                                    array_push($debug, "B. CashBook " . $valid);

                                    if ($cashHeaders->num_rows() > 0) {
                                        $cashHeader = $cashHeaders->row();
                                        if ($postDebit > 0) {
                                            //If Bank side is bigger than opposite
                                            $sumCredit = 0;
                                            foreach ($postDetails->result_array() as $credit) {
                                                if ($credit['journal_credit'] > 0) {
                                                    $sumCredit += $credit['journal_credit'];
                                                }
                                            }

                                            array_push($debug, "B1. Debit > 0 " . $sumCredit . ' ' . $valid);

                                            if ($postDebit >= $sumCredit) {
                                                foreach ($postDetails->result_array() as $credit) {
                                                    if ($valid) {
                                                        if ($credit['journal_credit'] > 0) {
                                                            $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $credit['coa_code'], $postDate, $bankAccountID, $journalNo, $credit['journal_note'], true, $currencytype_id, 1, $credit['journal_credit'], $credit['journal_credit'], $cashHeader->subject);
                                                        }
                                                    } else {
                                                        break;
                                                    }
                                                }
                                            } else {
                                                if ($postDetails->num_rows() > 0) {
                                                    foreach ($postDetails->result_array() as $cr) {
                                                        if ($cr['journal_credit'] > 0) {
                                                            $credit = $cr;
                                                            break;
                                                        }
                                                    }

                                                    if (isset($credit)) {
                                                        $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $credit['coa_code'], $postDate, $bankAccountID, $journalNo, $credit['journal_note'], true, $currencytype_id, 1, $postDebit, $postDebit, $cashHeader->subject);
                                                    }
                                                }
                                            }
                                        } else {
                                            $sumDebit = 0;
                                            foreach ($postDetails->result_array() as $debit) {
                                                if ($debit['journal_debit'] > 0) {
                                                    $sumDebit += $debit['journal_debit'];
                                                }
                                            }

                                            array_push($debug, "B2. Credit > 0 " . $sumDebit . ' ' . $valid);

                                            if ($postCredit >= $sumDebit) {
                                                foreach ($postDetails->result_array() as $debit) {
                                                    if ($valid) {
                                                        if ($debit['journal_debit'] > 0) {
                                                            $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $debit['coa_code'], $postDate, $bankAccountID, $journalNo, $debit['journal_note'], false, $currencytype_id, 1, $debit['journal_debit'], $debit['journal_debit'], $cashHeader->subject);
                                                        }
                                                    } else {
                                                        break;
                                                    }
                                                }
                                            } else {
                                                if ($postDetails->num_rows() > 0) {
                                                    foreach ($postDetails->result_array() as $dr) {
                                                        if ($dr['journal_debit'] > 0) {
                                                            $debit = $dr;
                                                            break;
                                                        }
                                                    }

                                                    if (isset($debit)) {
                                                        $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $debit['coa_code'], $postDate, $bankAccountID, $journalNo, $debit['journal_note'], false, $currencytype_id, 1, $postCredit, $postCredit, $cashHeader->subject);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $valid = false;
                                        array_push($debug, "B. Cash Header not found " . $journalNo . ' ' . $valid);
                                        array_push($message, 'Cash Header not found ' . $journalNo);
                                        break;
                                    }
                                } else if ($postModul == GLMOD::GL_MOD_AP) {
                                    $specAP = FNSpec::get(FNSpec::FIN_AP_INVOICE);
                                    $specForex = FNSpec::get(FNSpec::FIN_AP_FOREX_GAIN);

                                    if ($specAP['coa_id'] > 0) {
                                        $postDetails = $this->db->get_where('gl_postjournal_detail', array('postheader_id' => $postHeaderID));

                                        $payments = $this->db->query("SELECT   ap.payment_id, ap.bank_account_id as bankaccount_id, sp.supplier_name
                                                                  FROM ap_payment ap
                                                                  LEFT JOIN in_supplier sp ON ap.supplier_id = sp.supplier_id
                                                                  WHERE ap.payment_code = '" . $journalNo . "' AND ap.supplier_id > 0 AND ap.status <> " . FLAG_CASHBOOK);

                                        if ($payments->num_rows() > 0) {
                                            $row = $payments->row();
                                            $paymentID = $row->payment_id;
                                            $bankAccountID = $row->bankaccount_id;
                                            $supplierName = $row->supplier_name;
                                            $fn_bankAccount = $this->mdl_finance->getBankAccount($bankAccountID);

                                            if ($postCredit > 0) {
                                                $invoices = $this->db->query("SELECT   ap.inv_id, head.inv_code, head.isadvance
                                                                          FROM ap_payment_detail ap LEFT JOIN ap_invoiceheader head ON ap.inv_id = head.inv_id
                                                                          WHERE ap.payment_id= " . $paymentID);
                                                if ($invoices->num_rows() > 0) {
                                                    $row = $invoices->row();
                                                    $inv_id = $row->inv_id;
                                                    $inv_code = $row->inv_code;
                                                    $is_advance = $row->isadvance;

                                                    $inv_post = $this->db->query("SELECT   head.postheader_id FROM gl_postjournal_header
                                                                              journal_no = '" . $inv_code . "'");
                                                    if ($inv_post->num_rows() > 0) {
                                                        $inv_postheader_id = $inv_post->row()->postheader_id;
                                                        $inv_postdetails = $this->db->get_where('gl_postjournal_detail', array('postheader_id' => $inv_postheader_id));
                                                        if ($is_advance > 0) {
                                                            array_push($debug, '[D] Advance ' . $journalNo);

                                                            $sumBankPaymentCredit = 0;
                                                            $sumBankPaymentDebit = 0;
                                                            foreach ($inv_postdetails->result_array() as $detail) {
                                                                if ($detail['coa_code'] == $specAP['coa_code']) {
                                                                    $sumBankPaymentCredit += $detail['journal_credit'];
                                                                }
                                                                $sumBankPaymentDebit += $detail['journal_debit'];
                                                            }

                                                            if ($sumBankPaymentCredit >= $sumBankPaymentDebit) {
                                                                foreach ($inv_postdetails->result_array() as $detail) {
                                                                    if ($valid) {
                                                                        if ($detail['journal_debit'] > 0) {
                                                                            $debit_amount = $detail['journal_debit'];

                                                                            $dn = $this->db->query("SELECT   ISNULL(dn.local_amount,0) as dn_amount
                                                                                            FROM ap_debitnote_detail dn
                                                                                            WHERE dn.journal_detail_id =" . $detail['postdetail_id'] . " AND dn.local_amount > 0 ");
                                                                            if ($dn->num_rows() > 0) {
                                                                                $debit_amount -= $dn->row()->dn_amount;
                                                                            }

                                                                            $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $detail['coa_code'], $postDate, $bankAccountID, $journalNo, $detail['journal_note'], false, $currencytype_id, 1, $debit_amount, $debit_amount, $supplierName);
                                                                        }
                                                                    } else {
                                                                        $valid = false;
                                                                        break;
                                                                    }

                                                                }
                                                            } else {
                                                                //Forex Gain on Credit PI
                                                                foreach ($inv_postdetails->result_array() as $detail) {
                                                                    if ($detail['coa_code'] != $specAP['coa_code'] && $detail['journal_credit'] > 0) {
                                                                        $gain = $detail;
                                                                        break;
                                                                    }
                                                                }

                                                                $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $gain['coa_code'], $postDate, $bankAccountID, $journalNo, $gain['journal_note'], true, $currencytype_id, 1, $gain['journal_credit'], $gain['journal_credit'], $supplierName);

                                                            }

                                                            if ($valid) {
                                                                //Check CN to addition
                                                                $cn = $this->db->query("SELECT   cn.creditnote_id, cn.creditnote_code FROM ap_creditnote cn
                                                                                    WHERE cn.inv_id = " . $inv_id);
                                                                if ($cn->num_rows() > 0) {
                                                                    $cn = $cn->row();
                                                                    $cn_id = $cn->creditnote_id;
                                                                    $cn_no = $cn->creditnote_code;

                                                                    $cn_detail = $this->db->query("SELECT   det.coa_id, det.local_amount, coa.coa_code
                                                                                               FROM ap_creditnote_detail det
                                                                                               LEFT JOIN gl_coa coa ON det.coa_id = coa.coa_id
                                                                                               WHERE det.creditnote_id=" . $cn_id . " AND det.status = " . STATUS_NEW);
                                                                    if ($cn_detail->num_rows() > 0) {
                                                                        foreach ($cn_detail->result_array() as $cnd) {
                                                                            if ($valid) {
                                                                                $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $cnd['coa_code'], $postDate, $bankAccountID, $journalNo, $cn_no, false, $currencytype_id, 1, $cnd['local_amount'], $cnd['local_amount'], $supplierName);
                                                                            } else {
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            array_push($debug, '[D] Direct Payment ' . $journalNo);

                                                            foreach ($postDetails->result_array() as $detail) {
                                                                if ($valid) {
                                                                    if ($detail['coa_code'] == $specAP['coa_code'] && $detail['journal_debit'] > 0) {
                                                                        $apDeduct = 0;
                                                                        foreach ($postDetails->result_array() as $rec) {
                                                                            if ($rec['reference_id'] = $detail['reference_id'] && $rec['journal_credit'] > 0 &&
                                                                                $rec['coa_id'] != $specForex['coa_id'] && $rec['coa_id'] != $fn_bankAccount['coa_id']
                                                                            ) {
                                                                                $apDeduct += $rec['journal_credit'];
                                                                            }
                                                                        }

                                                                        $postCredit = ($detail['journal_debit'] - $apDeduct);

                                                                        $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $detail['coa_code'], $postDate, $bankAccountID, $journalNo, $inv_code, false, $currencytype_id, 1, $postCredit, $postCredit, $supplierName);
                                                                    }
                                                                } else {
                                                                    break;
                                                                }

                                                            }
                                                        }
                                                    }
                                                }

                                                if ($valid) {
                                                    //Check Cash Flow IN Forex from PV
                                                    //GAIN
                                                    foreach ($postDetails->result_array() as $detail) {
                                                        if ($detail['journal_credit'] > 0 && $detail['coa_id'] != $fn_bankAccount['coa_id'] &&
                                                            $detail['coa_id'] == $specForex['coa_id']
                                                        ) {
                                                            //Insert Cash Flow IN From Forex
                                                            //Bank
                                                            // -- Forex
                                                            $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $detail['coa_code'], $postDate, $bankAccountID, $journalNo, $detail['journal_note'], true, $currencytype_id, 1, $detail['journal_credit'], $detail['journal_credit'], $supplierName);

                                                            //break;
                                                        }
                                                    }
                                                }

                                                if ($valid) {
                                                    //Check Cash Flow OUT Forex from PV
                                                    //LOSS
                                                    foreach ($postDetails->result_array() as $detail) {
                                                        if ($detail['journal_debit'] > 0 && $detail['coa_id'] != $specAP['coa_id'] &&
                                                            $detail['coa_id'] == $specForex['coa_id']
                                                        ) {
                                                            //Insert Cash Flow OUT From Forex
                                                            //Forex
                                                            // -- Bank
                                                            $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $detail['coa_code'], $postDate, $bankAccountID, $journalNo, $detail['journal_note'], false, $currencytype_id, 1, $detail['journal_debit'], $detail['journal_debit'], $supplierName);

                                                            //break;
                                                        }
                                                    }
                                                }
                                            } else {

                                            }
                                        } else {
                                            $valid = false;
                                            array_push($debug, "C. AP Payment not found " . $journalNo . ' ' . $valid);
                                            array_push($message, 'AP Payment not found ' . $journalNo);
                                            break;
                                        }
                                    } else {
                                        $valid = false;
                                        array_push($message, 'Posting Param AP not found ' . $journalNo);
                                        break;
                                    }
                                } else if ($postModul == GLMOD::GL_MOD_JE) {
                                    $postDetails = $this->db->get_where('gl_postjournal_detail', array('postheader_id' => $postHeaderID));
                                    $entryHeaders = $this->db->get_where('gl_journalentry_header', array('journal_no' => $journalNo));
                                    if ($entryHeaders->num_rows() > 0) {
                                        $entry_header = $entryHeaders->row();
                                        $sumCredit = 0;
                                        $sumDebit = 0;
                                        foreach ($postDetails->result_array() as $rec) {
                                            if ($rec['journal_credit'] > 0) {
                                                $sumCredit += $rec['journal_credit'];
                                            }
                                            if ($rec['journal_debit'] > 0) {
                                                $sumDebit += $rec['journal_debit'];
                                            }
                                        }

                                        if ($postDebit > 0) {
                                            if ($postDebit >= $sumCredit) {
                                                foreach ($postDetails->result_array() as $detail) {
                                                    if ($valid) {
                                                        if ($detail['journal_credit'] > 0) {
                                                            $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $detail['coa_code'], $postDate, (isset($bankAccountID) ? $bankAccountID : 0), $journalNo, $detail['journal_note'], true, $currencytype_id, 1, $detail['journal_credit'], $detail['journal_credit'], $entry_header->journal_remarks);
                                                        }
                                                    } else {
                                                        break;
                                                    }

                                                }
                                            } else {
                                                foreach ($postDetails->result_array() as $rec) {
                                                    if ($rec['journal_credit'] > 0) {
                                                        $detail = $rec;
                                                        break;
                                                    }
                                                }

                                                if (isset($detail)) {
                                                    $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $detail['coa_code'], $postDate, (isset($bankAccountID) ? $bankAccountID : 0), $journalNo, $detail['journal_note'], true, $currencytype_id, 1, $postDebit, $postDebit, $entry_header->journal_remarks);
                                                } else {
                                                    $valid = false;
                                                }
                                            }
                                        } else {
                                            if ($postCredit >= $sumDebit) {
                                                foreach ($postDetails->result_array() as $detail) {
                                                    if ($valid) {
                                                        if ($detail['journal_debit'] > 0) {
                                                            $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $detail['coa_code'], $postDate, $bankAccountID, $journalNo, $detail['journal_note'], false, $currencytype_id, 1, $detail['journal_debit'], $detail['journal_debit'], $entry_header->journal_remarks);
                                                        }
                                                    } else {
                                                        break;
                                                    }
                                                }
                                            } else {
                                                foreach ($postDetails->result_array() as $rec) {
                                                    if ($rec['journal_debit'] > 0) {
                                                        $detail = $rec;
                                                        break;
                                                    }
                                                }

                                                if (isset($detail)) {
                                                    $valid = $this->mdl_finance->postCashFlow($closingHeaderID, $detail['coa_code'], $postDate, $bankAccountID, $journalNo, $detail['journal_note'], false, $currencytype_id, 1, $postCredit, $postCredit, $entry_header->journal_remarks);
                                                } else {
                                                    $valid = false;
                                                }
                                            }
                                        }

                                    } else {
                                        $valid = false;
                                        array_push($message, 'GL Entry Header not found.');
                                        break;
                                    }
                                }
                            } else {
                                break;
                            }
                        }
                    }

                } else {
                    $valid = false;
                    array_push($message, 'Posting Param COA Code for AP not found');
                }
            } catch (Exception $e) {
                $valid = false;
                array_push($debug, 'Err ' . $e);
            }

        }

        return array('valid' => $valid, 'message' => $message, 'debug' => $debug);
    }

    private function getProfitLossAmount($period_month = 0, $period_year = 0, $is_ytd = false)
    {
        $this->load->model('finance/mdl_finance');
        $profit_loss = array();
        $layout_pnl = GLStatement::get_layout_by_type(GLStatement::PROFIT_LOSS);
        foreach ($layout_pnl as $pl) {
            if ($pl['account_name'] != '') {
                $detail = array();

                $detail['group_name'] = substr(strtoupper($pl['ctg_key']), 8) == 'OPERATING' ? PLGROUP::GROSS_PROFIT : PLGROUP::NET_PROFIT;
                $detail['ctg_key'] = $pl['ctg_key'];
                $detail['account_name'] = $pl['account_name'];
                if ($pl['is_rangedformula'] > 0) {
                    $detail['current_balance'] = $this->mdl_finance->getBalanceByRange($pl, $period_month, $period_year, $is_ytd, true);
                } else {
                    $detail['current_balance'] = $this->mdl_finance->getBalanceByFormula($pl, $period_month, $period_year, $is_ytd, true);
                }

                array_push($profit_loss, $detail);
            }
        }

        //print_r($profit_loss);

        //Calculate ProfitLoss
        //MAIN INCOME + OTHER INCOME - (DIRECT EXPENSE + INDIRECT EXPENSE) + (Other Income - Other Expense)
        $incomeAmount = array_sum_by_col($profit_loss, 'current_balance', 'ctg_key', PLTYPE::OP_INCOME);
        $expenseAmount = array_sum_by_col($profit_loss, 'current_balance', 'ctg_key', PLTYPE::OP_EXPENSE);
        $otherIncomeAmount = array_sum_by_col($profit_loss, 'current_balance', 'ctg_key', PLTYPE::NON_OP_INCOME);
        $otherExpenseAmount = array_sum_by_col($profit_loss, 'current_balance', 'ctg_key', PLTYPE::NON_OP_EXPENSE);

        //echo 'Op Income' . $incomeAmount . ' - Op Expense ' . $expenseAmount . ' | Non Income ' . $otherIncomeAmount . ' - Non Expense ' . $otherExpenseAmount;
        $profitLossAmount = ($incomeAmount - $expenseAmount) + ($otherIncomeAmount - $otherExpenseAmount);

        return $profitLossAmount;
    }

    #endregion

    #region YEARLY

    //CLOSING YEAR
    public function submit_close_year()
    {
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $periodYear = 0;
        //$periodMonth = 0;

        if (isset($_POST['period_year'])) {
            $periodYear = $_POST['period_year'];
        }
        if (isset($_POST['period_month'])) {
            //$periodMonth = $_POST['period_month'];
        }

        if ($periodYear > 0) {
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $specMonth = FNSpec::get(FNSpec::CLOSE_MONTH);
            $specYear = FNSpec::get(FNSpec::CLOSE_YEAR);

            if ($specMonth['coa_id'] > 0 && $specYear['coa_id'] > 0) {
                $currentDate = date('Y-m-d');

                $balance = 0;
                $close_array = $this->mdl_finance->getCloseBalanceByCOACode($specMonth['coa_code'], 12, $periodYear, true, true);
                if (count($close_array) > 0) {
                    if (isset($close_array[$specMonth['coa_code']])) {
                        $balance = $close_array[$specMonth['coa_code']];
                    }
                }

                //if($balance != 0){
                $closingDate = DateTime::createFromFormat('Y-m-d', ($periodYear + 1) . '-' . '01-01');

                $header['closingdate'] = $closingDate->format('Y-m-d');
                $header['closingyear'] = $closingDate->format('Y');
                $header['closingmonth'] = $closingDate->format('m');
                $header['coa_code'] = $specYear['coa_code'];
                $header['is_yearly'] = 1;
                $header['balance'] = $balance;
                $header['status'] = STATUS_NEW;

                $this->db->insert('gl_closing_header', $header);
                $closingHeaderID = $this->db->insert_id();

                if ($closingHeaderID > 0) {
                    ///Set Balance to Closing Year, Post Journal
                    ///D = LOSS, C = PROFIT
                    ///Posting to EQUITY
                    ///Reset Balance of Closing Month, Post Journal
                    $detail = array();

                    $journal_note = "Closing Year - " . ($header['closingyear'] - 1);

                    //EQUITY YEAR
                    $equity1 = array();
                    $equity1['coa_id'] = $specYear['coa_id'];
                    $equity1['coa_code'] = $specYear['coa_code'];
                    $equity1['journal_note'] = $journal_note;
                    $equity1['dept_id'] = 0;
                    if ($balance < 0) {
                        //LOSS
                        $equity1['journal_debit'] = abs($balance);
                        $equity1['journal_credit'] = 0;
                    } else {
                        //PROFIT
                        $equity1['journal_credit'] = abs($balance);
                        $equity1['journal_debit'] = 0;
                    }

                    $equity1['reference_id'] = 0;
                    $equity1['transtype_id'] = $specYear['transtype_id'];

                    array_push($detail, $equity1);

                    //EQUITY MONTH
                    $equity2 = array();
                    $equity2['coa_id'] = $specMonth['coa_id'];
                    $equity2['coa_code'] = $specMonth['coa_code'];
                    $equity2['journal_note'] = $journal_note;
                    $equity2['dept_id'] = 0;
                    if ($balance < 0) {
                        //LOSS
                        $equity2['journal_debit'] = 0;
                        $equity2['journal_credit'] = abs($balance);
                    } else {
                        //PROFIT
                        $equity2['journal_credit'] = 0;
                        $equity2['journal_debit'] = abs($balance);
                    }

                    $equity2['reference_id'] = 0;
                    $equity2['transtype_id'] = $specMonth['transtype_id'];

                    array_push($detail, $equity2);

                    if (abs($equity1['journal_debit'] + $equity1['journal_credit']) ==
                        abs($equity2['journal_debit'] + $equity2['journal_credit']) &&
                        abs($balance) > 0
                    ) {

                        $posting_date = DateTime::createFromFormat('Y-m-d', $header['closingyear'] . '-' . $header['closingmonth'] . '-' . '01');
                        $posting_date = $posting_date->format('Y-m-d');

                        $header = array();
                        $header['journal_no'] = '-';
                        $header['journal_date'] = $posting_date;
                        $header['journal_remarks'] = $journal_note;
                        $header['modul'] = GLMOD::GL_MOD_JE;
                        $header['journal_amount'] = abs($balance);
                        $header['reference'] = '';

                        $valid = $this->mdl_finance->postJournal($header, $detail);
                    }

                    if ($valid) {
                        //Insert Log
                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_subject'] = $journal_note;
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $closingHeaderID;
                        $data_log['feature_id'] = Feature::FEATURE_GL_ENTRY;
                        $data_log['action_type'] = STATUS_NEW;
                        $this->db->insert('app_log', $data_log);
                    }

                } else {
                    $debug = '\nClosing Header ID = 0';
                    $valid = false;
                }
                //}else{
                //$debug = '\nBalance is 0';
                //$valid = false;
                //}
            } else {
                $debug = '\nCOA Code not found';
                $valid = false;
            }

            if (isset($debug)) {
                $result['debug'] = $debug;
            }

            //COMMIT OR ROLLBACK
            if ($valid) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Close Year can not be posted. Please try again later.';
                } else {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = 'Close Year ' . $periodYear . ' successfully posted.';
                    $result['redirect_link'] = base_url('finance/closing/closing_form.tpd');

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', $result['message']);
                }
            } else {
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Close Year can not be posted. Please try again later.';
            }
        }

        echo json_encode($result);
    }
    #endregion

    #endregion -----

    #region UNCLOSE ----

    public function unclose_form()
    {
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

        $data = array();

        $data['close_month'] = $this->uncloseMonthList();
        $data['close_year'] = $this->uncloseYearList();

        //YEARLY CLOSING

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/closing/unclose_form', $data);
        $this->load->view('layout/footer');
    }

    private function uncloseMonthList()
    {
        $qry = "SELECT * FROM gl_closing_header
                WHERE is_yearly <= 0 AND status = " . STATUS_NEW . "
                ORDER BY closingyear DESC, closingmonth DESC ";
        $monthClose = $this->db->query($qry);

        //YEAR
        $listYear = array();

        $closeYear = 0;
        if ($monthClose->num_rows() > 0) {
            $row = $monthClose->row_array();
            array_push($listYear, $row['closingyear']);
            $closeYear = $row['closingyear'];
        } else {
            //array_push($listYear, $minYear);
        }

        //MONTH
        $qry = "SELECT * FROM gl_closing_header
                WHERE is_yearly <= 0 AND status = " . STATUS_NEW . "
                AND closingyear = " . $closeYear . "
                ORDER BY closingdate DESC ";
        $monthClose = $this->db->query($qry);

        $listMonth = array();
        if ($monthClose->num_rows() > 0) {
            $row = $monthClose->row_array();
            $sMonth = (strlen($row['closingmonth']) > 1 ? $row['closingmonth'] : '0' . $row['closingmonth']);
            array_push($listMonth, $sMonth);
        }

        $result = array('month' => $listMonth, 'year' => $listYear);

        return $result;
    }

    private function uncloseYearList()
    {
        $result = array();
        $qry = "SELECT * FROM gl_closing_header
                WHERE status = " . STATUS_NEW . "
                ORDER BY closingyear DESC, closingmonth DESC ";
        $monthClose = $this->db->query($qry);

        //YEAR
        if ($monthClose->num_rows() > 0) {
            $row = $monthClose->row_array();
            array_push($result, $row['closingyear'] - 1);
        } else {
            //array_push($listYear, $minYear);
        }

        return $result;
    }

    /*
    private function uncloseMonthList(){
        $qry = "SELECT * FROM gl_closing_header
                WHERE is_yearly <= 0 AND status = " . STATUS_NEW . "
                ORDER BY closingyear DESC, closingmonth DESC ";
        $monthClose = $this->db->query($qry);

        //YEAR
        $listYear = array();

        $closeYear = 0;
        if($monthClose->num_rows() > 0){
            $row = $monthClose->row_array();
            array_push($listYear, $row['closingyear']);
            $closeYear = $row['closingyear'];
        }else{
            //array_push($listYear, $minYear);
        }

        //MONTH
        $qry = "SELECT * FROM gl_closing_header
                WHERE is_yearly <= 0 AND status = " . STATUS_NEW . "
                AND closingyear = " . $closeYear . "
                ORDER BY closingdate DESC ";
        $monthClose = $this->db->query($qry);

        $listMonth = array();
        if($monthClose->num_rows() > 0){
            $row = $monthClose->row_array();
            $sMonth = (strlen($row['closingmonth']) > 1 ? $row['closingmonth'] : '0'. $row['closingmonth']);
            array_push($listMonth, $sMonth);
        }

        $result=array('month' => $listMonth, 'year' => $listYear);

        return $result;
    }

    private function uncloseYearList(){
        $result = array();
        $qry = "SELECT * FROM gl_closing_header
                WHERE status = " . STATUS_NEW . "
                ORDER BY closingyear DESC, closingmonth DESC ";
        $monthClose = $this->db->query($qry);

        //YEAR
        if($monthClose->num_rows() > 0){
            $row = $monthClose->row_array();
            array_push($result, $row['closingyear']-1);
        }else{
            //array_push($listYear, $minYear);
        }

        return $result;
    }

    */

    public function submit_unclose_month()
    {
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $periodYear = 0;
        $periodMonth = 0;

        if (isset($_POST['period_year'])) {
            $periodYear = $_POST['period_year'];
        }
        if (isset($_POST['period_month'])) {
            $periodMonth = $_POST['period_month'];
        }

        if ($periodYear > 0 && $periodMonth > 0) {
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $closingDate = DateTime::createFromFormat('Y-m-d', $periodYear . '-' . $periodMonth . '-' . 1);

            try {
                $canceled = $this->db->query("SELECT * FROM gl_closing_header WHERE closingdate >= '" . $closingDate->format('Y-m-d') . "' AND is_yearly <= 0 AND status = " . STATUS_NEW . " ORDER BY closingyear, closingmonth");

                if ($canceled->num_rows() > 0) {
                    foreach ($canceled->result_array() as $row) {
                        $cancelDetail = $this->db->query("UPDATE gl_closing_detail SET status=" . STATUS_CANCEL . " WHERE closingheader_id=" . $row['closingheader_id']);
                        $cancelCFDetail = $this->db->query("UPDATE gl_cf_journal SET status=" . STATUS_CANCEL . " WHERE closingheader_id=" . $row['closingheader_id']);

                        //Delete Post Journal of Closing
                        //WHERE Journal_Amount && Journal_Date && PostedMonth && PostedYear && Modul
                        $journalDate = DateTime::createFromFormat('Y-m-d', $row['closingyear'] . '-' . $row['closingmonth'] . '-' . days_in_month($row['closingmonth'], $row['closingyear']));
                        $journalDate = $journalDate->format('Y-m-d');

                        $qry = "SELECT postheader_id FROM gl_postjournal_header
                                WHERE journal_amount = " . abs($row['balance']) . " AND journal_date = '" . $journalDate . "'
                                AND postedmonth = " . $row['closingmonth'] . " AND postedyear = " . $row['closingyear'] . "
                                AND modul = '" . GLMOD::GL_MOD_JE . "'";

                        $postHeaders = $this->db->query($qry);
                        if ($postHeaders->num_rows() > 0) {
                            foreach ($postHeaders->result_array() as $postHeader) {
                                $deleted = $this->db->query("DELETE FROM gl_postjournal_detail WHERE postheader_id = " . $postHeader['postheader_id']);
                                $deleted = $this->db->query("DELETE FROM gl_postjournal_header WHERE postheader_id = " . $postHeader['postheader_id']);
                            }
                        }
                    }

                    //UPDATE GL PERIOD
                    if ($valid) {
                        $this->db->query("UPDATE gl_closing_header SET status = " . STATUS_CANCEL . " WHERE closingheader_id = " . $row['closingheader_id']);
                    }
                }

            } catch (Exception $e) {
                $valid = false;
            }

            //COMMIT OR ROLLBACK
            if ($valid) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Unclose Month can not be posted. Please try again later.';
                } else {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = 'Unclose Month ' . $periodMonth . '-' . $periodYear . ' successfully executed.';
                    $result['redirect_link'] = base_url('finance/closing/unclose_form.tpd');

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', $result['message']);
                }
            } else {
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Unclose Month can not be posted. Please try again later.';
            }
        }

        echo json_encode($result);
    }

    public function submit_unclose_year()
    {
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $periodYear = 0;

        if (isset($_POST['period_year'])) {
            $periodYear = $_POST['period_year'];
        }

        $periodYear++;

        if ($periodYear > 0) {
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            //$closingDate = DateTime::createFromFormat('Y-m-d', $periodYear . '-' . $periodMonth . '-' . 1);
            try {
                $canceled = $this->db->query("SELECT * FROM gl_closing_header WHERE closingyear >= '" . $periodYear . "' AND is_yearly > 0 AND status = " . STATUS_NEW . " ORDER BY closingyear, closingmonth");

                if ($canceled->num_rows() > 0) {
                    foreach ($canceled->result_array() as $row) {
                        $cancelDetail = $this->db->query("UPDATE gl_closing_detail SET status=" . STATUS_CANCEL . " WHERE closingheader_id=" . $row['closingheader_id']);
                        $cancelCFDetail = $this->db->query("UPDATE gl_cf_journal SET status=" . STATUS_CANCEL . " WHERE closingheader_id=" . $row['closingheader_id']);

                        //Delete Post Journal of Closing
                        //WHERE Journal_Amount && Journal_Date && PostedMonth && PostedYear && Modul
                        $journalDate = DateTime::createFromFormat('Y-m-d', $row['closingyear'] . '-' . $row['closingmonth'] . '-' . 1);
                        $journalDate = $journalDate->format('Y-m-d');

                        $qry = "SELECT postheader_id FROM gl_postjournal_header
                                WHERE journal_amount = " . abs($row['balance']) . " AND journal_date = '" . $journalDate . "'
                                AND postedmonth = " . 1 . " AND postedyear = " . $row['closingyear'] . "
                                AND modul = '" . GLMOD::GL_MOD_JE . "'";

                        $postHeaders = $this->db->query($qry);
                        if ($postHeaders->num_rows() > 0) {
                            foreach ($postHeaders->result_array() as $postHeader) {
                                $deleted = $this->db->query("DELETE FROM gl_postjournal_detail WHERE postheader_id = " . $postHeader['postheader_id']);
                                $deleted = $this->db->query("DELETE FROM gl_postjournal_header WHERE postheader_id = " . $postHeader['postheader_id']);
                            }
                        }
                    }

                    //UPDATE GL PERIOD
                    if ($valid) {
                        $this->db->query("UPDATE gl_closing_header SET status = " . STATUS_CANCEL . " WHERE closingheader_id = " . $row['closingheader_id']);
                    }
                }

            } catch (Exception $e) {
                $valid = false;
            }

            //COMMIT OR ROLLBACK
            if ($valid) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Unclose Month can not be posted. Please try again later.';
                } else {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = 'Unclose Year ' . $periodYear . ' successfully executed.';
                    $result['redirect_link'] = base_url('finance/closing/unclose_form.tpd');

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', $result['message']);
                }
            } else {
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Unclose Month can not be posted. Please try again later.';
            }
        }

        echo json_encode($result);
    }

    #endregion

}