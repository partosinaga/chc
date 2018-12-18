<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_general extends CI_Model{
	
	function get($table_name, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0, $where_or = '', $where_in_key = '', $where_in_array = array(), $where_string = '' ){
		$this->db->select('*');
		$this->db->from($table_name);
		if(count($where) > 0){
			$this->db->where($where);
		}
        if(trim($where_or) != ''){
            $this->db->where($where_or);
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
		return $this->db->get();
    }

	function update($table_name, $where = array(), $data = array()){
		$this->db->where($where);
		$this->db->update($table_name, $data);
	}
	
	function count($table_name, $where = array(), $like = array(), $where_or = '', $where_in_key = '', $where_in_array = array(),$where_string=''){
		$this->db->from($table_name);
		if(count($where) > 0){
			$this->db->where($where);
		}
        if(trim($where_or) != ''){
            $this->db->where($where_or);
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

	function get_role_by_user($user_id = 0){
		$this->db->select('*');
		$this->db->from('ms_role_detail');
		$this->db->join('ms_role', 'ms_role_detail.role_id = ms_role.role_id', 'left');
		$this->db->join('ms_role_user', 'ms_role.role_id = ms_role_user.role_id', 'left');
		$this->db->where('ms_role_user.user_id', $user_id);
		return $this->db->get();
	}
	
	function generate_code($feature = 0, $date = '0000-00-00'){ //YYYY-MM-DD
		$result = '';
		
		if($feature > 0 && $date != '' && $date != '0000-00-00'){
            $valid_date = false;
            $arr_date = explode('-', $date);

            if(count($arr_date) == 3){
                if(checkdate(intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0]))){
                    $valid_date = true;
                }
            }

            if($valid_date){
                $qry_doc = $this->db->get_where('document', array('feature_id' => $feature));
                if($qry_doc->num_rows() > 0) {
                    $row_doc = $qry_doc->row();

                    if($feature == Feature::FEATURE_STOCK_REQUEST){  //STOCK REQUEST
                        $next = 1;
                        $qry_list = $this->db->query("SELECT request_code FROM in_request WHERE MONTH(request_date) = " . $arr_date[1] . " AND YEAR(request_date) = " . $arr_date[0] . " ORDER BY request_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->request_code, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if($feature == Feature::FEATURE_GRN){  //GRN
                        $next = 1;
                        $qry_list = $this->db->query("SELECT grn_code FROM in_grn WHERE  MONTH(grn_date) = " . $arr_date[1] . " AND YEAR(grn_date) = " . $arr_date[0] . " ORDER BY grn_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->grn_code, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if($feature == Feature::FEATURE_RETURN){  //RETURN
                        $next = 1;
                        $qry_list = $this->db->query("SELECT return_code FROM in_return WHERE MONTH(return_date) = " . $arr_date[1] . " AND YEAR(return_date) = " . $arr_date[0] . " ORDER BY return_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->return_code, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if($feature == Feature::FEATURE_STOCK_ISSUE){  //STOCK ISSUE
                        $next = 1;
                        $qry_list = $this->db->query("SELECT gi_code FROM in_gi WHERE MONTH(gi_date) = " . $arr_date[1] . " AND YEAR(gi_date) = " . $arr_date[0] . " ORDER BY gi_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->gi_code, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if($feature == Feature::FEATURE_STOCK_RECEIPT){  //STOCK RECEIPT
                        $next = 1;
                        $qry_list = $this->db->query("SELECT sr_code FROM in_sr WHERE MONTH(sr_date) = " . $arr_date[1] . " AND YEAR(sr_date) = " . $arr_date[0] . " ORDER BY sr_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->sr_code, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if($feature == Feature::FEATURE_STOCK_ADJUSTMENT){  //STOCK ADJUSTMENT
                        $next = 1;
                        $qry_list = $this->db->query("SELECT adj_code FROM in_adjustment WHERE MONTH(adj_date) = " . $arr_date[1] . " AND YEAR(adj_date) = " . $arr_date[0] . " ORDER BY adj_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->adj_code, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if($feature == Feature::FEATURE_GL_ENTRY){  //GL ENTRY
                        $next = 1;
                        $qry_list = $this->db->query("SELECT journal_no as doc_no FROM gl_journalentry_header WHERE MONTH(journal_date) = " . $arr_date[1] . " AND YEAR(journal_date) = " . $arr_date[0] .
                            "  ORDER BY journal_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->doc_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if($feature == Feature::FEATURE_AP_INVOICE){
                        $next = 1;
                        $qry_list = $this->db->query("SELECT inv_code as doc_no FROM ap_invoiceheader WHERE MONTH(inv_date) = " . $arr_date[1] . " AND YEAR(inv_date) = " . $arr_date[0] . " ORDER BY inv_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->doc_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    } else if($feature == Feature::FEATURE_AP_DEBIT_NOTE){
                        $next = 1;
                        $qry_list = $this->db->query("SELECT debitnote_code as doc_no FROM ap_debitnote WHERE MONTH(debitnote_date) = " . $arr_date[1] . " AND YEAR(debitnote_date) = " . $arr_date[0] . " ORDER BY debitnote_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->doc_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    } else if($feature == Feature::FEATURE_AP_CREDIT_NOTE){
                        $next = 1;
                        $qry_list = $this->db->query("SELECT creditnote_code as doc_no FROM ap_creditnote WHERE MONTH(creditnote_date) = " . $arr_date[1] . " AND YEAR(creditnote_date) = " . $arr_date[0] . " ORDER BY creditnote_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->doc_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if($feature == Feature::FEATURE_AP_PAYMENT){
                        $next = 1;
                        $qry_list = $this->db->query("SELECT payment_code as doc_no FROM ap_payment WHERE MONTH(payment_date) = " . $arr_date[1] . " AND YEAR(payment_date) = " . $arr_date[0] . " ORDER BY payment_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->doc_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if($feature == Feature::FEATURE_AR_RECEIPT){
                        $next = 1;
                        $qry_list = $this->db->query("SELECT receipt_no as doc_no FROM ar_receipt WHERE MONTH(receipt_date) = " . $arr_date[1] . " AND YEAR(receipt_date) = " . $arr_date[0] . " ORDER BY receipt_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->doc_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;

                    }
                    else if($feature == Feature::FEATURE_GL_ADJUSTMENT){
                        $next = 1;
                        $qry_list = $this->db->query("SELECT journal_no as doc_no FROM gl_cashentry_header WHERE MONTH(journal_date) = " . $arr_date[1] . " AND YEAR(journal_date) = " . $arr_date[0] .
                            " ORDER BY journal_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->doc_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if($feature == Feature::FEATURE_GL_RECURRING){
                        $next = 1;
                        $qry_list = $this->db->query("SELECT entry_code as doc_no FROM gl_scheduleentry_header WHERE MONTH(entry_startdate) = " . $arr_date[1] . " AND YEAR(entry_startdate) = " . $arr_date[0] .
                            " ORDER BY entry_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->doc_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if($feature == Feature::FEATURE_CS_RESERVATION){
                        $next = 1;
                        $qry_list = $this->db->query("SELECT reservation_code as doc_no FROM cs_reservation_header WHERE MONTH(reservation_date) = " . $arr_date[1] . " AND YEAR(reservation_date) = " . $arr_date[0] . " AND status NOT IN(" . STATUS_DELETE . ") ORDER BY reservation_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->doc_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if ($feature == Feature::FEATURE_PR){  //PR

                        $next = 1;
                        $qry_list = $this->db->query("SELECT pr_code FROM in_pr WHERE MONTH(date_prepare) = " . $arr_date[1] . " AND YEAR(date_prepare) = " . $arr_date[0] . " ORDER BY pr_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->pr_code, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));

                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
					}
                    else if ($feature == Feature::FEATURE_PO){  //PO
                        $next = 1;
                        $qry_list = $this->db->query("SELECT po_code FROM in_po WHERE MONTH(po_date) = " . $arr_date[1] . " AND YEAR(po_date) = " . $arr_date[0] . " ORDER BY po_code");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->po_code, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if ($feature == Feature::FEATURE_AR_BILLING){  //AR INVOICE
                        $next = 1;
                        $qry_list = $this->db->query("SELECT journal_no FROM cs_bill_header WHERE MONTH(bill_date) = " . $arr_date[1] . " AND YEAR(bill_date) = " . $arr_date[0] . " ORDER BY journal_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->journal_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                    else if ($feature == Feature::FEATURE_AR_CREDIT_NOTE){  //AR CREDIT NOTE
                        $next = 1;
                        $qry_list = $this->db->query("SELECT credit_no as journal_no FROM ar_creditnote_header WHERE MONTH(credit_date) = " . $arr_date[1] . " AND YEAR(credit_date) = " . $arr_date[0] . " ORDER BY credit_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->journal_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if ($feature == Feature::FEATURE_AR_DEBIT_NOTE){  //AR DEBIT NOTE
                        $next = 1;
                        $qry_list = $this->db->query("SELECT debit_no as journal_no FROM ar_debitnote_header WHERE MONTH(debit_date) = " . $arr_date[1] . " AND YEAR(debit_date) = " . $arr_date[0] . " ORDER BY debit_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->journal_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if ($feature == Feature::FEATURE_CS_SRF){  //SRF
                        $next = 1;
                        $qry_list = $this->db->query("SELECT srf_no as journal_no FROM cs_srf_header WHERE MONTH(srf_date) = " . $arr_date[1] . " AND YEAR(srf_date) = " . $arr_date[0] . " ORDER BY srf_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->journal_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if ($feature == Feature::FEATURE_AR_TRANSFER){  //AR INVOICE
                        $next = 1;
                        $qry_list = $this->db->query("SELECT bill_no as journal_no FROM ar_corporate_bill WHERE MONTH(bill_date) = " . $arr_date[1] . " AND YEAR(bill_date) = " . $arr_date[0] . " ORDER BY bill_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->journal_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if ($feature == Feature::FEATURE_AR_INVOICE){  //AR INVOICE
                        $next = 1;
                        $qry_list = $this->db->query("SELECT inv_no as journal_no FROM ar_invoice_header WHERE MONTH(inv_date) = " . $arr_date[1] . " AND YEAR(inv_date) = " . $arr_date[0] . " ORDER BY inv_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->journal_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if ($feature == Feature::FEATURE_AR_PROFORMA){  //AR INVOICE
                        $next = 1;
                        $qry_list = $this->db->query("SELECT pro_inv_no as journal_no FROM ar_proforma_inv_header WHERE MONTH(pro_inv_date) = " . $arr_date[1] . " AND YEAR(pro_inv_date) = " . $arr_date[0] . " ORDER BY pro_inv_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->journal_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if ($feature == Feature::FEATURE_AR_INVOICE){  //AR INVOICE
                        $next = 1;
                        $qry_list = $this->db->query("SELECT inv_no as journal_no FROM ar_invoice_header WHERE MONTH(inv_date) = " . $arr_date[1] . " AND YEAR(inv_date) = " . $arr_date[0] . " ORDER BY inv_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->journal_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if ($feature == Feature::FEATURE_AR_ALLOC){  //AR ALLOCATION
                        $next = 1;
                        $qry_list = $this->db->query("SELECT alloc_no as journal_no FROM ar_allocation_header WHERE MONTH(alloc_date) = " . $arr_date[1] . " AND YEAR(alloc_date) = " . $arr_date[0] . " ORDER BY alloc_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->journal_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }else if ($feature == Feature::FEATURE_AR_DELIVERY_ORDER){  //AR DELIVERY ORDER
                        $next = 1;
                        $qry_list = $this->db->query("SELECT do_no as journal_no FROM ar_delivery_header WHERE MONTH(do_date) = " . $arr_date[1] . " AND YEAR(do_date) = " . $arr_date[0] . " ORDER BY do_no");

                        foreach($qry_list->result() as $row_list){
                            $str_last = substr($row_list->journal_no, (0 - $row_doc->doc_length));
                            $num_last = intval($str_last);

                            if($next != $num_last){
                                break;
                            }

                            $next++;
                        }

                        $next_code = $next;

                        $len = strlen($next);
                        if($len < $row_doc->doc_length){
                            $next_code = str_repeat('0', ($row_doc->doc_length - $len)) . $next;
                        }

                        $y = date("y", mktime(0, 0, 0, intval($arr_date[1]), intval($arr_date[2]), intval($arr_date[0])));
                        $result = $row_doc->doc_name . $y . $arr_date[1] . $next_code;
                    }
                }
            }
		}
		
		return $result;
	}

    function getJoin($selectfields = '', $maintable = '', $jointable = array(), $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0, $where_string = '', $wherein = array(), $wherenotin = array()){
        $this->db->select($selectfields);
        $this->db->from($maintable);
        if(count($jointable) > 0 ){
            foreach($jointable as $key => $val){
                $this->db->join($key, $val, 'left');
            }
        }
        if(count($where) > 0){
            $this->db->where($where);
        }
        if(trim($where_string) != ''){
            $this->db->where($where_string);
        }
        if(count($wherein) > 0){
            $this->db->where_in($wherein['key'], $wherein['value']);
        }
        if(count($wherenotin) > 0){
            $this->db->where_not_in($wherenotin['key'], $wherenotin['value']);
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

        return $this->db->get();
    }

    function countJoin($maintable = '', $jointable = array(),$where = array(), $like = array(), $where_string = '', $wherein = array(), $wherenotin = array()){
        $this->db->from($maintable);
        if(count($jointable) > 0 ){
            foreach($jointable as $key => $val){
                $this->db->join($key, $val, 'left');
            }
        }
        if(count($where) > 0){
            $this->db->where($where);
        }
        if(trim($where_string) != ''){
            $this->db->where($where_string);
        }
        if(count($wherein) > 0){
            $this->db->where_in($wherein['key'], $wherein['value']);
        }
        if(count($wherenotin) > 0){
            $this->db->where_not_in($wherenotin['key'], $wherenotin['value']);
        }
        if(count($like) > 0){
            $this->db->like($like);
        }

        return $this->db->count_all_results();
    }

    function generateNewTenantCode(){
        $result = '001';

        $arr_date = explode('-', date('y-m-d'));
        $prefix = $arr_date[0] . $arr_date[1];

        $qry = $this->db->query('select top 1 tenant_account from ms_tenant where status <> ' . STATUS_DELETE . ' order by tenant_account desc');

        if($qry->num_rows() > 0){
            $row = $qry->row();

            $arr_code = explode('/',$row->tenant_account);
            $lastno = intval($arr_code[1]);
            $lastno = strval(($lastno+1));
            $result = str_repeat('0', 3 - strlen($lastno)) . $lastno;
        }

        $result = $prefix . '/' . $result;

        return $result;
    }

}
?>