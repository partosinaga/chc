<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_finance extends CI_Model{
	
	function getCOA($where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0, $where_string = ''){
		$this->db->select('gl_coa.*, gl_class.class_code, gl_class.class_desc, gl_class.class_type');
		$this->db->from('gl_coa');
		$this->db->join('gl_class', 'gl_class.class_id = gl_coa.class_id');
		if(count($where) > 0){
			$this->db->where($where);
		}
        if($where_string != ''){
            $this->db->where($where_string);
        }
		if(count($like) > 0){
			$this->db->like($like);
		}
		if($order_by != ""){
			$this->db->order_by($order_by);
		}
		if($limit_row > 0){
			$this->db->limit($limit_row, $limit_start);
		}

        $this->db->distinct();

		return $this->db->get();
    }

    function countCOA($where = array(), $like = array(), $where_string = ''){
        $this->db->from('gl_coa');
        $this->db->join('gl_class', 'gl_class.class_id = gl_coa.class_id');
        if(count($where) > 0){
            $this->db->where($where);
        }
        if($where_string != ''){
            $this->db->where($where_string);
        }
        if(count($like) > 0){
            $this->db->like($like);
        }

        return $this->db->count_all_results();
    }

    function getJoin($selectfields = '', $maintable = '', $jointable = array(), $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0, $isdistinct = false, $where_in_key = '', $where_in_array = array(), $where_string = ''){
        //$this->db->select('ms_transtype.*, gl_coa.coa_code');
        $this->db->select($selectfields);
        $this->db->from($maintable);
        //$this->db->join('gl_class', 'gl_class.class_id = gl_coa.class_id');
        if(count($jointable) > 0 ){
            foreach($jointable as $key => $val){
                $this->db->join($key, $val, 'left');
            }
        }
        if(count($where) > 0){
            $this->db->where($where);
        }
        if(trim($where_in_key) != '' && count($where_in_array) > 0){
            $this->db->where_in(trim($where_in_key), $where_in_array);
        }
        if(trim($where_string) != ''){
            $this->db->where($where_string);
        }
        if(count($like) > 0){
            $this->db->like($like);
        }
        if($order_by != ""){
            $this->db->order_by($order_by);
        }
        if($limit_row > 0){
            $this->db->limit($limit_row, $limit_start);
        }
        if($isdistinct){
            $this->db->distinct();
        }

        return $this->db->get();
    }

    function countJoin($maintable = '', $jointable = array(),$where = array(), $like = array(), $where_in_key = '', $where_in_array = array(), $where_string = ''){
        $this->db->from($maintable);
        //$this->db->join('gl_coa', 'gl_class.class_id = gl_coa.class_id', 'left');
        if(count($jointable) > 0 ){
            foreach($jointable as $key => $val){
                $this->db->join($key, $val, 'left');
            }
        }
        if(count($where) > 0){
            $this->db->where($where);
        }
        if(trim($where_in_key) != '' && count($where_in_array) > 0){
            $this->db->where_in(trim($where_in_key), $where_in_array);
        }
        if(trim($where_string) != ''){
            $this->db->where($where_string);
        }
        if(count($like) > 0){
            $this->db->like($like);
        }

        return $this->db->count_all_results();
    }

    #region Bank

    function getBankAccount($bankaccount_id = 0){
        $result = array();

        if($bankaccount_id > 0){
            $qry = $this->db->query('SELECT fn_bank_account.*, fn_bank.bank_code, fn_bank.bank_name
                                     FROM fn_bank_account
                                     LEFT JOIN fn_bank ON fn_bank.bank_id = fn_bank_account.bank_id
                                     WHERE fn_bank_account.bankaccount_id = ' . $bankaccount_id);
            if($qry->num_rows() > 0){
               $result = $qry->row_array();
            }
        }

        return $result;
    }

    #endregion

    #region STATEMENT

    public function getCloseBalanceByCOACode($_coaCode = '', $_month = 0, $_year = 0, $_is_ytd = false, $_is_yearly_close = false){
        $result = array();

        if($_coaCode != '' && $_month > 0 && $_year > 0){
            $qry = "SELECT COA_Code as coa_code, ISNULL(SUM(Balance),0) as balance_amount FROM view_close_statement ";
            if($_is_ytd){
                $qry .= " WHERE PostedMonth <= " . $_month;
            }else{
                $qry .= " WHERE PostedMonth = " . $_month;
            }

            /*
            if($_is_yearly_close){
                if($_is_ytd){
                    $qry .= " AND PostedYear <= " . $_year;
                }else{
                    $qry .= " WHERE PostedYear <= " . $_year;
                }
            }else{
                $qry .= " WHERE PostedYear = " . $_year;
            }*/
            $qry .= " AND PostedYear = " . $_year;

            $qry .= " AND COA_Code IN(" . $_coaCode . ")
                      GROUP BY COA_Code";

            $sql = $this->db->query($qry);
            foreach($sql->result_array() as $row){
                array_push($result, array($row['coa_code'] => $row['balance_amount']));
            }
        }

        return $result;
    }

    ///GET LAYOUT BALANCE By Range
    function getBalanceByRange($layout = array(), $month = 0, $year = 0, $is_ytd = false, $is_beforeclosing = false, $group_dept = ''){
        $result = 0;

        if(count($layout) > 0 && $month > 0 && $year > 0){
            if($layout['is_rangedformula'] > 0){
                if($layout['range_start'] > 0 && $layout['range_end'] > 0){
                    if($is_beforeclosing){
                        $qry = "SELECT ISNULL(SUM(balance),0) as balanceamount FROM view_gl_statement ";
                        $coa_codes = "(coa_code >= " . $layout['range_start'] . " AND
                                       coa_code <= " . $layout['range_start'] . ") ";
                    }else{
                        $qry = "SELECT ISNULL(SUM(balance),0) as balanceamount FROM view_close_statement ";
                        $coa_codes = "(CONVERT(int,coa_code) >= " . $layout['range_start'] . " AND
                                       CONVERT(int,coa_code) <= " . $layout['range_start'] . ") ";
                    }

                    if($is_ytd){
                        $qry .= " WHERE postedmonth <= " . $month;
                    }else{
                        $qry .= " WHERE postedmonth = " . $month;
                    }

                    $qry .= " AND postedyear = " . $year . "
                              AND " . $coa_codes;

                    if($is_beforeclosing){
                        if(trim($group_dept) != ''){
                            $qry .= " AND Dept_ID IN(" . $group_dept .") ";
                        }
                    }

                    $sum = $this->db->query($qry);
                    if($sum->num_rows() > 0){
                        $result = $sum->row()->balanceamount;
                    }
                }
            }
        }

        return $result;
    }

    ///GET LAYOUT BALANCE By Text Formula
    function getBalanceByFormula($layout = array(), $month = 0, $year = 0, $is_ytd = false, $is_beforeclosing = false, $group_dept = ''){
        $result = 0;

        if(count($layout) > 0 && $month > 0 && $year > 0){
            if($layout['is_rangedformula'] <= 0){
                if($layout['text_formula'] != ''){
                    $pattern = '(-|\+|\*|\/)';
                    $replacement = '-';
                    $formula = preg_replace($pattern, $replacement, trim($layout['text_formula']));

                    $arr = explode('-',$formula);
                    $new_arr = array();
                    foreach($arr as $str){
                        if(trim($str) != '' && strlen($str) > 4){
                            $new_arr[] = $str;
                        }
                    }

                    $coa_comma = count($new_arr) > 0 ? implode(',',$new_arr) : '';
                    if($coa_comma != ''){
                        if($is_beforeclosing){
                            $qry = "SELECT ISNULL(SUM(balance),0) as balanceamount FROM view_gl_statement ";
                        }else{
                            $qry = "SELECT ISNULL(SUM(balance),0) as balanceamount FROM view_close_statement ";
                        }

                        if($is_ytd){
                            $qry .= " WHERE postedmonth <= " . $month;
                        }else{
                            $qry .= " WHERE postedmonth = " . $month;
                        }

                        $qry .= " AND postedyear = " . $year . "
                              AND coa_code IN(" . $coa_comma . ") ";

                        if($is_beforeclosing){
                            if(trim($group_dept) != ''){
                                $qry .= " AND Dept_ID IN(" . $group_dept .") ";
                            }
                        }

                        $sum = $this->db->query($qry);
                        if($sum->num_rows() > 0){
                            $result = $sum->row()->balanceamount;
                        }
                    }

                }
            }
        }

        return $result;
    }

    ///GET CF Balance By Range
    function getBalanceCashFlowByRange($layout = array(), $month = 0, $year = 0, $is_ytd = false){
        $result = 0;

        if(count($layout) > 0 && $month > 0 && $year > 0){
            if($layout['is_rangedformula'] > 0){
                if($layout['range_start'] > 0 && $layout['range_end'] > 0){
                    $coa_codes = " (coa_code >= " . $layout['range_start'] . " AND
                                    coa_code <= " . $layout['range_start'] . ")";

                    if($is_ytd){
                        $qry = "SELECT (
                                        (select ISNULL(SUM(local_amount),0) from view_closecf_statement where is_inflow > 0 and postedmonth <= " . $month . " and postedyear = " . $year . " and " . $coa_codes . ") -
                                        (select ISNULL(SUM(local_amount),0) from view_closecf_statement where is_inflow <= 0 and postedmonth <= " . $month . " and postedyear = " . $year . " and " . $coa_codes . ")
                                        ) AS BalanceAmount
                                FROM view_closecf_statement
                                WHERE postedmonth <= " . $month . " ";
                    }else{
                        $qry = "SELECT (
                                        (select ISNULL(SUM(local_amount),0) from view_closecf_statement where is_inflow > 0 and postedmonth = " . $month . " and postedyear = " . $year . " and " . $coa_codes . ") -
                                        (select ISNULL(SUM(local_amount),0) from view_closecf_statement where is_inflow <= 0 and postedmonth = " . $month . " and postedyear = " . $year . " and " . $coa_codes . ")
                                        ) AS BalanceAmount
                                FROM view_closecf_statement
                                WHERE postedmonth = " . $month . " ";
                    }

                    $qry .= " AND postedyear = " . $year . "
                              AND " . $coa_codes;

                    $sum = $this->db->query($qry);
                    if($sum->num_rows() > 0){
                        $result = $sum->row()->BalanceAmount;
                    }
                }
            }
        }

        return $result;
    }

    ///GET CF Balance By Formula
    function getBalanceCashFlowByFormula($layout = array(), $month = 0, $year = 0, $is_ytd = false){
        $result = 0;

        if(count($layout) > 0 && $month > 0 && $year > 0){
            if($layout['is_rangedformula'] <= 0){
                if(trim($layout['text_formula']) != ''){
                    $pattern = '(-|\+|\*|\/)';
                    $replacement = '-';
                    $formula = preg_replace($pattern, $replacement, trim($layout['text_formula']));

                    $arr = explode('-',$formula);
                    $new_arr = array();
                    foreach($arr as $str){
                        if(trim($str) != '' && strlen($str) > 4){
                            $new_arr[] = $str;
                        }
                    }

                    $coa_comma = count($new_arr) > 0 ? implode(',',$new_arr) : '';
                    if($coa_comma != ''){
                        if($is_ytd){
                            $qry = "SELECT (
                                        (select ISNULL(SUM(local_amount),0) from view_closecf_statement where is_inflow > 0 and postedmonth <= " . $month . " and postedyear = " . $year . " and coa_code IN(" . $coa_comma . ")) -
                                        (select ISNULL(SUM(local_amount),0) from view_closecf_statement where is_inflow <= 0 and postedmonth <= " . $month . " and postedyear = " . $year . " and coa_code IN(" . $coa_comma . "))
                                        ) AS BalanceAmount
                                FROM view_closecf_statement
                                WHERE postedmonth <= " . $month . " ";
                        }else{
                            $qry = "SELECT (
                                        (select ISNULL(SUM(local_amount),0) from view_closecf_statement where is_inflow > 0 and postedmonth = " . $month . " and postedyear = " . $year . " and coa_code IN(" . $coa_comma . ")) -
                                        (select ISNULL(SUM(local_amount),0) from view_closecf_statement where is_inflow <= 0 and postedmonth = " . $month . " and postedyear = " . $year . " and coa_code IN(" . $coa_comma . "))
                                        ) AS BalanceAmount
                                FROM view_closecf_statement
                                WHERE postedmonth = " . $month . " ";
                        }

                        $qry .= " AND postedyear = " . $year . "
                                  AND coa_code IN(" . $coa_comma . ") ";

                        $sum = $this->db->query($qry);
                        if($sum->num_rows() > 0){
                            $result = $sum->row()->BalanceAmount;
                        }
                    }
                }
            }
        }

        return $result;
    }

    ///GET BALANCE SHEET By Text Formula
    function getBalanceSheetByFormula($layout = array(), $month = 0, $year = 0, $is_beforeclosing = false){
        $result = 0;

        if(count($layout) > 0 && $month > 0 && $year > 0){
            if($layout['is_rangedformula'] <= 0){
                if($layout['text_formula'] != ''){
                    $pattern = '(-|\+|\*|\/)';
                    $replacement = '-';
                    $formula = preg_replace($pattern, $replacement, trim($layout['text_formula']));

                    $arr = explode('-',$formula);
                    $new_arr = array();
                    foreach($arr as $str){
                        if(trim($str) != '' && strlen($str) > 4){
                            $new_arr[] = $str;
                        }
                    }

                    $coa_comma = count($new_arr) > 0 ? implode(',',$new_arr) : '';
                    if($coa_comma != ''){
                        if($is_beforeclosing){
                            $qry = "SELECT SUM(
                                        CASE
                                            WHEN coa.is_debit <> cls.is_debit THEN
                                                ISNULL(st.balance, 0) * -1
                                            ELSE
                                                ISNULL(st.balance, 0)
                                        END
		                                   ) as balanceamount FROM view_gl_statement st
                                    JOIN gl_coa coa ON st.coa_code = coa.coa_code
                                    JOIN gl_class cls ON cls.class_id = coa.class_id ";
                        }else{
                            $qry = "SELECT SUM(
                                        CASE
                                            WHEN coa.is_debit <> cls.is_debit THEN
                                                ISNULL(st.balance, 0) * -1
                                            ELSE
                                                ISNULL(st.balance, 0)
                                        END
		                                   ) as balanceamount FROM view_close_statement st
                                    JOIN gl_coa coa ON st.coa_code = coa.coa_code
                                    JOIN gl_class cls ON cls.class_id = coa.class_id ";
                        }

                        $closingDate = $year . '-' . $month . '-' . days_in_month($month,$year);

                        $qry .= " WHERE st.closingdate <= '" . $closingDate . "'
                                  AND st.coa_code IN(" . $coa_comma . ") ";

                        $sum = $this->db->query($qry);
                        if($sum->num_rows() > 0){
                            $row = $sum->row();
                            $result = $row->balanceamount;
                        }
                    }

                }
            }
        }

        return $result;
    }

    ///GET BALANCE SHEET By Range
    function getBalanceSheetByRange($layout = array(), $month = 0, $year = 0, $is_beforeclosing = false){
        $result = 0;

        if(count($layout) > 0 && $month > 0 && $year > 0){
            if($layout['is_rangedformula'] > 0){
                if($layout['range_start'] > 0 && $layout['range_end'] > 0){
                    if($is_beforeclosing){
                        $coa_codes = "(st.coa_code >= " . $layout['range_start'] . " AND
                                       st.coa_code <= " . $layout['range_start'] . ") ";

                        $qry = "SELECT SUM(
                                        CASE
                                            WHEN coa.is_debit <> cls.is_debit THEN
                                                ISNULL(st.balance, 0) * -1
                                            ELSE
                                                ISNULL(st.balance, 0)
                                        END
		                                   ) as balanceamount FROM view_gl_statement st
                                    JOIN gl_coa coa ON st.coa_code = coa.coa_code
                                    JOIN gl_class cls ON cls.class_id = coa.class_id ";
                    }else{
                        $coa_codes = "(CONVERT(int,st.coa_code) >= " . $layout['range_start'] . " AND
                                       CONVERT(int,st.coa_code) <= " . $layout['range_start'] . ") ";

                        $qry = "SELECT SUM(
                                        CASE
                                            WHEN coa.is_debit <> cls.is_debit THEN
                                                ISNULL(st.balance, 0) * -1
                                            ELSE
                                                ISNULL(st.balance, 0)
                                        END
		                                   ) as balanceamount FROM view_close_statement st
                                    JOIN gl_coa coa ON st.coa_code = coa.coa_code
                                    JOIN gl_class cls ON cls.class_id = coa.class_id ";
                    }

                    $closingDate = $year . '-' . $month . '-' . days_in_month($month,$year);

                    $qry .= " WHERE st.closingdate <= '" . $closingDate . "'
                                  AND " . $coa_codes;

                    $sum = $this->db->query($qry);
                    if($sum->num_rows() > 0){
                        $row = $sum->row();
                        $result = $row->balanceamount;
                    }

                }
            }
        }

        return $result;
    }

    function getProfitLossSummary($period_month = 0, $period_year = 0, $is_ytd = false){
        $result = array();

        $profit_loss = array();
        $layout_pnl = GLStatement::get_layout_by_type(GLStatement::PROFIT_LOSS);
        foreach($layout_pnl as $pl){
            if($pl['account_name'] != ''){
                $detail=array();

                $detail['group_name'] = substr(strtoupper($pl['ctg_key']),8) == 'OPERATING' ? PLGROUP::GROSS_PROFIT : PLGROUP::NET_PROFIT;
                $detail['ctg_key'] = $pl['ctg_key'];
                $detail['account_name'] = $pl['account_name'];
                if($pl['is_rangedformula'] > 0){
                    $detail['current_balance'] = $this->getBalanceByRange($pl,$period_month, $period_year, $is_ytd, true);
                }else{
                    $detail['current_balance'] = $this->getBalanceByFormula($pl,$period_month, $period_year, $is_ytd, true);
                }

                array_push($profit_loss, $detail);
            }
        }

        $incomeAmount = array_sum_by_col($profit_loss,'current_balance','ctg_key', PLTYPE::OP_INCOME);
        $expenseAmount = array_sum_by_col($profit_loss,'current_balance','ctg_key', PLTYPE::OP_EXPENSE);
        $otherIncomeAmount = array_sum_by_col($profit_loss,'current_balance','ctg_key', PLTYPE::NON_OP_INCOME);
        $otherExpenseAmount = array_sum_by_col($profit_loss,'current_balance','ctg_key', PLTYPE::NON_OP_EXPENSE);

        $result = array(PLTYPE::OP_INCOME => $incomeAmount, PLTYPE::OP_EXPENSE => $expenseAmount, PLTYPE::NON_OP_INCOME => $otherIncomeAmount , PLTYPE::NON_OP_EXPENSE => $otherExpenseAmount);

        return $result;
    }

    #endregion

    #region POSTING

    //POSTING JOURNAL
    function postJournal($header = array(), $detail = array()){
        $valid = true;

        if ($this->db->trans_status() === FALSE){
            $valid = false;

            echo 'No transaction detected.';
        }else{
            if(isset($header) && isset($detail)){
                $server_date = date('Y-m-d H:i:s');

                $header['postedmonth'] = date('m',strtotime($header['journal_date']));
                $header['postedyear'] = date('Y',strtotime($header['journal_date']));
                $header['status'] = STATUS_NEW;
                $header['created_by'] = my_sess('user_id');
                $header['created_date'] = $server_date;

                $this->db->insert('gl_postjournal_header', $header);
                $newId = $this->db->insert_id();

                if($newId > 0){
                    for ($i = 0; $i < count($detail); $i++)
                    {
                        //echo '<br />coa id : ' . $detail[$i]['coa_id'];
                        $row = $detail[$i];

                        $row['postheader_id'] = $newId;
                        $row['status'] = STATUS_NEW;

                        if($row['journal_note'] === ''){
                            $row['journal_note'] = $header['journal_remarks'];
                        }

                        if(!isset($row['coa_code']) || $row['coa_code'] === ''){
                            $coa = $this->db->get_where('gl_coa', array('coa_id' => $row['coa_id']));
                            if($coa->num_rows() > 0){
                                $row['coa_code'] = $coa->row()->coa_code;
                            }
                        }

                        $this->db->insert('gl_postjournal_detail', $row);
                        $newDetailId = $this->db->insert_id();

                        if($newDetailId <= 0){
                            $valid = false;
                            break;
                        }
                    }
                }else{
                    $valid = false;
                }
            }else{
                $valid = false;
            }
        }

        return $valid;
    }

    //POSTING CASHFLOW
    function postCashFlow($closingheader_id = 0, $coa_code = '', $journal_ymd = '', $bankaccount_id = 0, $doc_no = '', $reff_no = '', $is_inflow = true, $currencytype_id = 0, $forex_rate = 1, $forex_amount = 0, $local_amount = 0, $subject = ''){
        $valid = true;

        if ($this->db->trans_status() === FALSE){
            $valid = false;

            echo 'No transaction detected.';
        }else{
            if($closingheader_id > 0 && $coa_code != '' && $journal_ymd != '' && $doc_no != '' && $local_amount > 0){
                $server_date = date('Y-m-d H:i:s');

                $data['closingheader_id'] = $closingheader_id;
                $data['coa_code'] = $coa_code;
                $data['activity_date'] = $journal_ymd;
                $data['bankaccount_id'] = $bankaccount_id;
                $data['doc_no'] = $doc_no;

                if($reff_no == ''){
                    $reff_no = $doc_no;
                }

                if(strlen($reff_no) > 250){
                    $reff_no = $doc_no;
                }
                $data['reff_no'] = $reff_no;
                $data['is_inflow'] = $is_inflow ? 1 : 0;
                $data['currencytype_id'] = $currencytype_id;
                $data['forex_rate'] = $forex_rate;
                $data['foreign_amount'] = $forex_amount;
                $data['local_amount'] = $local_amount;
                $data['subject'] = $subject;
                $data['prev_balance'] = $this->getCashFlowBalanceByCOACode($coa_code);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                $this->db->insert('gl_cf_journal', $data);
                $newId = $this->db->insert_id();

                if($newId <= 0){
                    $valid = false;
                }
            }else{
                $valid = false;
            }
        }

        return $valid;
    }

    public function getCashFlowBalanceByCOACode($coa_code = ''){
        $result = 0;

        if($coa_code != ''){
            $qry = "SELECT ISNULL((Local_Amount + Prev_Balance),0) as Balance FROM gl_cf_journal
                    WHERE coa_code = " . $coa_code . " AND status = " . STATUS_NEW . " ORDER BY activity_date DESC, id DESC";
            $cashflow = $this->db->query($qry);
            if($cashflow->num_rows() > 0){
                $result = $cashflow->row()->Balance;
            }
        }

        return $result;
    }

    public function posting_item_supplies_only($bill_id = 0){
        $detail = array();

        $validJournal = true;

        if($bill_id > 0) {
            $billdetails = $this->db->query('SELECT cs_bill_header.journal_no,cs_bill_header.bill_date, pos_item_stock.itemstock_id, cs_bill_detail.rate,cs_bill_detail.disc_amount, cs_bill_detail.item_qty, pos_item_stock.itemstock_current_qty, pos_item_stock.base_avg_price
                FROM cs_bill_detail
                JOIN cs_bill_header ON cs_bill_header.bill_id = cs_bill_detail.bill_id
                JOIN pos_item_stock ON pos_item_stock.itemstock_id = cs_bill_detail.item_id
                WHERE pos_item_stock.is_service_item <= 0 AND cs_bill_detail.bill_id = ' . $bill_id);

            if ($billdetails->num_rows() > 0) {
                $totalExpense = 0;

                $journalNo = '';
                $billDate = date('Y-m-d H:i:s');
                foreach ($billdetails->result() as $det) {
                    $journalNo = $det->journal_no;
                    $billDate = ymd_from_db($det->bill_date);
                    //Req by budi on 22 April 16
                    //$totalExpense += round(($det->rate - $det->disc_amount) * $det->item_qty, 0);
                    $totalExpense += $det->base_avg_price * $det->item_qty;

                    //Deduct qty of item stock
                    /*
                    $update = array();
                    if ($det->itemstock_current_qty >= $det->item_qty) {
                        $update['itemstock_current_qty'] = $det->itemstock_current_qty - $det->item_qty;
                    }else{
                        $update['itemstock_current_qty'] = 0;
                    }

                    $this->mdl_general->update('pos_item_stock', array('itemstock_id' => $det->itemstock_id), $update);
                    */
                }

                if ($totalExpense > 0) {
                    $totalCredit = 0;
                    $totalDebit = 0;

                    $supExpense = FNSpec::get(FNSpec::POS_EXPENSE);
                    if ($supExpense['coa_id'] > 0) {
                        $rowdet = array();
                        $rowdet['coa_id'] = $supExpense['coa_id'];
                        $rowdet['dept_id'] = 0;
                        $rowdet['journal_note'] = $journalNo;
                        $rowdet['journal_debit'] = $totalExpense;
                        $rowdet['journal_credit'] = 0;
                        $rowdet['reference_id'] = 0;
                        $rowdet['transtype_id'] = $supExpense['transtype_id'];

                        array_push($detail, $rowdet);

                        $totalDebit = $totalExpense;
                    }

                    $supplies = FNSpec::get(FNSpec::POS_SUPPLIES);
                    if ($supplies['coa_id'] > 0) {
                        $rowdet = array();
                        $rowdet['coa_id'] = $supplies['coa_id'];
                        $rowdet['dept_id'] = 0;
                        $rowdet['journal_note'] = $journalNo;
                        $rowdet['journal_debit'] = 0;
                        $rowdet['journal_credit'] = $totalExpense;
                        $rowdet['reference_id'] = 0;
                        $rowdet['transtype_id'] = $supplies['transtype_id'];

                        array_push($detail, $rowdet);

                        $totalCredit = $totalExpense;
                    }

                    if ($totalDebit == $totalCredit && $totalDebit > 0) {
                        $header = array();
                        $header['journal_no'] = $journalNo;
                        $header['journal_date'] = $billDate;
                        $header['journal_remarks'] = $journalNo;
                        $header['modul'] = GLMOD::GL_MOD_POS;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = '';

                        $validJournal = $this->postJournal($header, $detail);
                    } else {
                        $validJournal = false;
                    }
                }
            }
        }else{
            $validJournal = false;
        }

        return $validJournal;
    }

    #endregion

}
?>