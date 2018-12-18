<?php
	class Feature {
		//////// 3 DIGIT
		/* PURCHASING  1-- */
		const FEATURE_PR 					= 100;
		const FEATURE_PO 					= 101;
		
		/* INVENTORY  2-- */
		const FEATURE_GRN 					= 200;
		const FEATURE_RETURN 				= 201;
		const FEATURE_STOCK_REQUEST 		= 203;
		const FEATURE_STOCK_ISSUE 			= 204;
		const FEATURE_STOCK_RECEIPT 		= 205;
		const FEATURE_STOCK_ADJUSTMENT 		= 206;
		
		/* AP  3-- */
		const FEATURE_AP_INVOICE 			= 300;
		const FEATURE_AP_DEBIT_NOTE 		= 301;
		const FEATURE_AP_CREDIT_NOTE 		= 302;
		const FEATURE_AP_PAYMENT 			= 303;

        /* AR */
        const FEATURE_AR_RECEIPT            = 700;
        const FEATURE_AR_INVOICE            = 701;
        const FEATURE_AR_REFUND             = 702;
        const FEATURE_AR_DEBIT_NOTE         = 703;
        const FEATURE_AR_CREDIT_NOTE        = 704;
        const FEATURE_AR_BILLING            = 705;
        const FEATURE_AR_TRANSFER           = 706;
        const FEATURE_AR_DEPOSIT            = 707;
        const FEATURE_AR_PROFORMA           = 708;
        const FEATURE_AR_ALLOC              = 709;
		const FEATURE_AR_DELIVERY_ORDER	    = 710;

        /* GL */
        const FEATURE_GL_ADJUSTMENT         = 600;
        const FEATURE_GL_ENTRY              = 601;
        const FEATURE_CASHBOOK              = 602;
        const FEATURE_GL_RECURRING          = 603;

        const FEATURE_CS_RESERVATION        = 800;
        const FEATURE_CS_CHECKIN            = 801;
        const FEATURE_CS_RESERVATION_BILL   = 802;
        const FEATURE_CS_RESERVATION_RECEIPT= 803;
        const FEATURE_CS_SRF                = 804;

        /*Get Feature Name by id*/
        function get_feature_name($featureId = 0){
            $result = '';

            switch ($featureId)
            {
                case Feature::FEATURE_AR_RECEIPT:
                    $result = 'AR Receipt';
                    break;
                case Feature::FEATURE_AR_CREDIT_NOTE:
                    $result = 'AR Credit Note';
                    break;
                case Feature::FEATURE_AR_BILLING:
                    $result = 'AR Billing';
                    break;
                case Feature::FEATURE_AR_INVOICE:
                    $result = 'AR Invoice';
                    break;
                case Feature::FEATURE_AR_DEBIT_NOTE:
                    $result = 'AR Debit Note';
                    break;
                case Feature::FEATURE_AR_REFUND:
                    $result = 'AR Refund';
                    break;
                case Feature::FEATURE_AR_TRANSFER:
                    $result = 'AR Transfer';
                    break;
                case Feature::FEATURE_AR_ALLOC:
                    $result = 'AR Allocation';
                    break;
                case Feature::FEATURE_AR_PROFORMA:
                    $result = 'AR Proforma Invoice';
                    break;
				case Feature::FEATURE_AR_DELIVERY_ORDER:
                    $result = 'AR Delivery Order';
                    break;
                case Feature::FEATURE_STOCK_REQUEST:
                    $result = 'Stock Request';
                    break;
                case Feature::FEATURE_GL_ADJUSTMENT:
                    $result = 'GL Adjustment';
                    break;
                case Feature::FEATURE_GL_ENTRY:
                    $result = 'GL Entry';
                    break;
                case Feature::FEATURE_CASHBOOK:
                    $result = 'Cash Book';
                    break;
                case Feature::FEATURE_GL_RECURRING:
                    $result = 'GL Recurring';
                    break;
                case Feature::FEATURE_PR:
                    $result = 'Purchase Requisition';
                    break;
                case Feature::FEATURE_PO:
                    $result = 'Purchase Order';
                    break;
                case Feature::FEATURE_GRN:
                    $result = "Goods Receipt";
                    break;
                case Feature::FEATURE_RETURN:
                    $result = "Goods Return";
                    break;
                case Feature::FEATURE_STOCK_ISSUE:
                    $result = "Stock Issue";
                    break;
                case Feature::FEATURE_STOCK_RECEIPT:
                    $result = "Stock Receipt";
                    break;
                case Feature::FEATURE_STOCK_ADJUSTMENT:
                    $result = "Stock Adjustment";
                    break;
                case Feature::FEATURE_AP_INVOICE:
                    $result = 'AP Invoice';
                    break;
                case Feature::FEATURE_AP_DEBIT_NOTE:
                    $result = 'AP Debit Note';
                    break;
                case Feature::FEATURE_AP_CREDIT_NOTE:
                    $result = 'AP Credit Note';
                    break;
                case Feature::FEATURE_AP_PAYMENT:
                    $result = 'AP Payment';
                    break;
                case Feature::FEATURE_CS_RESERVATION:
                    $result = "Reservation";
                    break;
                case Feature::FEATURE_CS_CHECKIN:
                    $result = "Check In";
                    break;
                case Feature::FEATURE_CS_SRF:
                    $result = "SRF";
                    break;
                default:
                    $result = "";
                    break;
            }

            return $result;
        }
	}
	
	
	function is_login(){
		$result = false;

		$CI =& get_instance();

		$session = $CI->session->userdata(SESSION_NAME);

		if($session['logged_in'] == true || $session['logged_in'] == '1'){
			$result = true;
		}

		return $result;
	}
	
	function my_sess($string_sess){
		$result = '';
		
		$CI =& get_instance();

		$session = $CI->session->userdata(SESSION_NAME);
		$result = trim($session[$string_sess]);

		return $result;
	}
	
	function ymd_to_dmy($date){
		$result = '';
		if($date != ''){
			//2016-05-18 12:05:56
			$exp1 = explode(" ", $date);
			
			$date = $exp1[0];
			
			if($date != null && $date != '' && $date != '0000-00-00'){
				$result = date('d-m-Y', strtotime($date));
			}
		}
		return $result;
	}
	
	function dmy_to_ymd($date){
		$result = '0000-00-00';
		if($date != ''){
			$exp1 = explode(" ", $date);
			
			$date = $exp1[0];
			
			if($date != null && $date != '' && $date != '00-00-0000'){
				$date_ = DateTime::createFromFormat('d-m-Y', $date);
				$result = $date_->format('Y-m-d');
			}
		}
		return $result;
	}

    function ymd_from_db($date){
        $result = '';
        if($date != null){
            $result = date('Y-m-d',strtotime($date));
        }
        return $result;
    }

    function dmy_from_db($date){
        $result = '';
        if($date != null){
            $result = date('d-m-Y',strtotime($date));
        }
        return $result;
    }

	function show_flash($message = '', $type = ''){
        $result = '';

        if($message != '' && $type != ''){
            $result .= '<div class="alert alert-' . $type . '">';

            if($type == 'success'){
                $result .= '<i class="fa fa-fw fa-check"></i>&nbsp;<strong>Success !&nbsp;</strong>';
            }
            else if($type == 'warning'){
                $result .= '<i class="fa fa-fw fa-exclamation-triangle"></i>&nbsp;<strong>Warning !&nbsp;</strong>';
            }
            else if($type == 'danger'){
                $result .= '<i class="fa fa-fw fa-times"></i>&nbsp;<strong>Warning !&nbsp;</strong>';
            }

            $result .= $message . '<button class="close" data-close="alert"></button></div>';
        }

        return $result;
    }

    function show_toastr($message = '', $type = ''){
        $result = '';

        if($message != '' && $type != ''){
            if($type == 'success'){
                $result = 'toastr["success"]("' . $message . '", "Success");';
            }
            else {
                $result = 'toastr["error"]("' . $message . '", "Error");';
            }
        }

        return $result;
    }

    function format_num($amount, $decimal){
        $result = '0,00';
        //if($amount != ''){
            $result = number_format($amount,$decimal,'.',',');
        //}
        return $result;
    }
	
	function show_badge($type = '', $message = ''){
		$result = '';
		
		if($type != ''){
			$result = '<span class="badge ' . $type . ' badge-roundless"> ' . $message . ' </span>';
		}
		
		return $result;
	}
	
	function get_status_active($status = 0){
		$result = '';
		
		if($status == STATUS_NEW){
			$result = show_badge('badge-primary', 'ACTIVE');
		}
		else if($status == STATUS_INACTIVE){
			$result = show_badge('badge-default', 'INACTIVE');
		}
		
		return $result;
	}
	function get_doc_sales_name($status = 0){
		$result = '';

		if($status == DOC_SO){
			$result = show_badge('badge-info', 'SALES ORDER');
		}
		else if($status == DOC_DO){
			$result = show_badge('badge-warning', 'DELIVERY ORDER');
	}

	return $result;
}

	function get_status_name($status = 0, $is_badges = true){
		$result = '';
		
		if($status == STATUS_NEW){
			if($is_badges){
				$result = show_badge('badge-default', 'NEW');
			}
			else {
				$result = 'NEW';
			}
		}
		else if($status == STATUS_EDIT){
			if($is_badges){
				$result = show_badge('badge-default', 'EDIT');
			}
			else {
				$result = 'EDIT';
			}
		}
		else if($status == STATUS_PROCESS){
			if($is_badges){
				$result = show_badge('badge-default', 'PROCESS');
			}
			else {
				$result = 'PROCESS';
			}
		}
		else if($status == STATUS_APPROVE){
			if($is_badges){
				$result = show_badge('bg-blue-hoki', 'APPROVED');
			}
			else {
				$result = 'APPROVED';
			}
		}
		else if($status == STATUS_DISAPPROVE){
			if($is_badges){
				$result = show_badge('bg-yellow-gold', 'DISAPPROVED');
			}
			else {
				$result = 'DISAPPROVED';
			}
		}
		else if($status == STATUS_CANCEL){
			if($is_badges){
				$result = show_badge('bg-red-thunderbird', 'CANCELED');
			}
			else {
				$result = 'CANCELED';
			}
		}
		else if($status == STATUS_POSTED){
			if($is_badges){
				$result = show_badge('bg-blue-steel', 'POSTED');
			}
			else {
				$result = 'POSTED';
			}
		}
		else if($status == STATUS_CLOSED){
			if($is_badges){
				$result = show_badge('bg-purple-plum', 'CLOSED');
			}
			else {
				$result = 'CLOSED';
			}
		}
		else if($status == STATUS_DELETE){
			if($is_badges){
				$result = show_badge('bg-red-thunderbird', 'DELETED');
			}
			else {
				$result = 'DELETED';
			}
		}
		else if($status == STATUS_VIEW){
			if($is_badges){
				$result = show_badge('badge-primary', 'VIEW');
			}
			else {
				$result = 'VIEW';
			}
		}
		else if($status == STATUS_AUDIT){
            if($is_badges){
                $result = show_badge('bg-grey-cascade', 'AUDIT');
            }
            else {
                $result = 'AUDIT';
            }
        }
		else if($status == STATUS_REJECT){
			if($is_badges){
				$result = show_badge('bg-grey-cascade', 'REJECTED');
			}
			else {
				$result = 'REJECTED';
			}
		}
		else if($status == STATUS_UNLOCK){
			if($is_badges){
				$result = show_badge('bg-grey-cascade', 'UNLOCK');
			}
			else {
				$result = 'UNLOCK';
			}
		}
        else if($status == STATUS_PRINT){
            if($is_badges){
                $result = show_badge('bg-grey-cascade', 'PRINT');
            }
            else {
                $result = 'PRINT';
            }
        }
		
		return $result;
	}
	
	function get_action_name($status = 0, $is_badges = true){
		$result = '';
		
		if($status == STATUS_NEW){
			if($is_badges){
				$result = show_badge('badge-default', 'CREATE');
			}
			else {
				$result = 'CREATE';
			}
		}
		else if($status == STATUS_EDIT){
			if($is_badges){
				$result = show_badge('badge-default', 'EDIT');
			}
			else {
				$result = 'EDIT';
			}
		}
		else if($status == STATUS_PROCESS){
			if($is_badges){
				$result = show_badge('badge-default', 'PROCESS');
			}
			else {
				$result = 'PROCESS';
			}
		}
		else if($status == STATUS_APPROVE){
			if($is_badges){
				$result = show_badge('bg-blue-hoki', 'APPROVE');
			}
			else {
				$result = 'APPROVE';
			}
		}
		else if($status == STATUS_DISAPPROVE){
			if($is_badges){
				$result = show_badge('bg-yellow-gold', 'DISAPPROVE');
			}
			else {
				$result = 'DISAPPROVE';
			}
		}
		else if($status == STATUS_CANCEL){
			if($is_badges){
				$result = show_badge('bg-red-thunderbird', 'CANCEL');
			}
			else {
				$result = 'CANCEL';
			}
		}
		else if($status == STATUS_POSTED){
			if($is_badges){
				$result = show_badge('bg-green-seagreen', 'POSTING');
			}
			else {
				$result = 'POSTING';
			}
		}
		else if($status == STATUS_CLOSED){
			if($is_badges){
				$result = show_badge('bg-purple-plum', 'CLOSE');
			}
			else {
				$result = 'CLOSE';
			}
		}
		else if($status == STATUS_DELETE){
			if($is_badges){
				$result = show_badge('bg-red-thunderbird', 'DELETE');
			}
			else {
				$result = 'DELETE';
			}
		}
		else if($status == STATUS_VIEW){
			if($is_badges){
				$result = show_badge('badge-primary', 'VIEW');
			}
			else {
				$result = 'VIEW';
			}
		}
		else if($status == STATUS_AUDIT){
			if($is_badges){
				$result = show_badge('bg-grey-cascade', 'AUDIT');
			}
			else {
				$result = 'AUDIT';
			}
		}
		else if($status == STATUS_REJECT){
			if($is_badges){
				$result = show_badge('bg-grey-cascade', 'REJECT');
			}
			else {
				$result = 'REJECT';
			}
		}
		else if($status == STATUS_UNLOCK){
			if($is_badges){
				$result = show_badge('bg-grey-cascade', 'UNLOCK');
			}
			else {
				$result = 'UNLOCK';
			}
		}
        else if($status == STATUS_PRINT){
            if($is_badges){
                $result = show_badge('bg-grey-cascade', 'PRINT');
            }
            else {
                $result = 'PRINT';
            }
        }
		
		return $result;
	}

	function get_menu_action_label($menu_id = 0, $action = 0) {
		$result = '';

		$CI =& get_instance();

		$qry = $CI->db->get_where('ms_menu_action', array('menu_id' => $menu_id, 'status_action' => $action));
		if ($qry->num_rows() > 0) {
			$row = $qry->row();

			if ($row->status_label != '') {
				$result = $row->status_label;
			} else {
				$CI->load->helper('tpd_helper');

				get_action_name($action, false);
			}
		}

		return $result;
	}
	
	function get_menu_name($module = '', $controller = '', $function = '', $parameter = ''){
		$result = array();
		
		if($module != '' || $controller != '' || $function != ''){
			$CI =& get_instance();
			
			if($module != ''){
				$where['module_name'] = $module;
			}
			if($controller != ''){
				$where['controller_name'] = $controller;
			}
			if($function != ''){
				$where['function_name'] = $function;
			}

            if($parameter != ''){
                $where['function_parameter'] = $parameter;
            }
			
			array_push($result, '<li><i class="fa fa-home"></i><a href="' . base_url('home/home/dashboard.tpd') . '">Home</a><i class="fa fa-angle-right"></i></li>');
			
			$qry = $CI->db->get_where('ms_menu', $where);
			if($qry->num_rows() > 0){
				$row = $qry->row();
				
				$qry2 = $CI->db->get_where('ms_menu', array('menu_id' => $row->parent_id));
				if($qry2->num_rows() > 0){
					$row2 = $qry2->row();
					
					$qry3 = $CI->db->get_where('ms_menu', array('menu_id' => $row2->parent_id));
					if($qry3->num_rows() > 0){
						$row3 = $qry3->row();
						
						array_push($result, '<li><a>' . $row3->menu_name . '</a><i class="fa fa-angle-right"></i></li>');
					}
					
					array_push($result, '<li><a>' . $row2->menu_name . '</a><i class="fa fa-angle-right"></i></li>');
				}
				
				array_push($result, '<li><a>' . $row->menu_name . '</a></li>');
			}
		}
		
		return $result;
	}
	
	function get_role_detail($role_id = 0, $menu_id = 0, $action_do = 0){
		$result = false;
		
		if($role_id > 0 && $menu_id > 0 && $action_do > 0){
			$CI =& get_instance();
			
			$count = $CI->mdl_general->count('ms_role_detail', array('role_id' => $role_id, 'menu_id' => $menu_id, 'action_do' => $action_do));
			
			if($count > 0){
				$result = true;
			}
		}
		
		return $result;
	}

	function session_role(){
		$result = array();
	
		$CI =& get_instance();
		
		$CI->load->helper('tpd_helper');

        $qry_role = $CI->mdl_general->get_role_by_user(my_sess('user_id'));
        foreach($qry_role->result() as $row_role){
            $result[$row_role->menu_id][$row_role->action_do] = 1;
        }

        return $result;
	}
	
	function check_session_menu($menu_id = 0){
		$result = false;
	
		$CI =& get_instance();
		
		$CI->load->helper('tpd_helper');
		
		if(count(session_role()) > 0){
			if (array_key_exists($menu_id, session_role())) {
				$result = true;
			}
		}
		
		return $result;
	}

	function get_dept_name($dept_id = 0) {
		$result = '';

		if ($dept_id > 0) {
			$CI =& get_instance();

			$qry = $CI->db->get_where('ms_department', array('department_id' => $dept_id));
			if ($qry->num_rows() > 0) {
				$row = $qry->row();

				$result = trim($row->department_name);
			}
		}

		return $result;
	}
	
	function check_session_action($menu_id = 0, $action_do = 0){
		$result = false;
	
		$CI =& get_instance();
		
		$CI->load->helper('tpd_helper');
		
		if(count(session_role()) > 0){
            if(isset(session_role()[$menu_id])){
                if (array_key_exists($action_do, session_role()[$menu_id])) {
                    $result = true;
                }
            }
		}
		
		return $result;
	}

    function check_controller_action($module_name = '' , $controller_name = '', $action_do = 0){
        $result = false;

        $CI =& get_instance();

        $CI->load->helper('tpd_helper');

        $count = $CI->db->query("SELECT Count(ms_role_detail.role_detail_id) as cid
                                    FROM  ms_role_detail
                                    JOIN ms_menu ON ms_menu.menu_id = ms_role_detail.menu_id
                                    JOIN ms_role ON ms_role_detail.role_id = ms_role.role_id
                                    JOIN ms_role_user ON ms_role.role_id = ms_role_user.role_id
                                    WHERE ms_role_user.user_id = " . my_sess('user_id') .
                                    " AND ms_menu.module_name ='" . $module_name . "'
                                      AND ms_menu.controller_name ='" . $controller_name . "'
                                      AND ms_role_detail.action_do = " . $action_do);

        if($count->num_rows() > 0){
            if($count->row()->cid > 0){
                $result = true;
            }
        }

        return $result;
    }

	function check_function_action($module_name = '' , $controller_name = '', $function = '', $action_do = 0){
		$result = false;

		$CI =& get_instance();

		$CI->load->helper('tpd_helper');

		$count = $CI->db->query("SELECT Count(ms_role_detail.role_detail_id) as cid
										FROM  ms_role_detail
										JOIN ms_menu ON ms_menu.menu_id = ms_role_detail.menu_id
										JOIN ms_role ON ms_role_detail.role_id = ms_role.role_id
										JOIN ms_role_user ON ms_role.role_id = ms_role_user.role_id
										WHERE ms_role_user.user_id = " . my_sess('user_id') .
										" AND ms_menu.module_name ='" . $module_name . "'
										  AND ms_menu.controller_name ='" . $controller_name . "'
										  AND ms_menu.function_name ='" . $function . "'
										  AND ms_role_detail.action_do = " . $action_do);

		if($count->num_rows() > 0){
			if($count->row()->cid > 0){
				$result = true;
			}
		}

		return $result;
	}

	function get_menu_id(){
		$result = 0;
		
		$CI =& get_instance();
		
		$uri1 = $CI->uri->segment(1);
		$uri2 = $CI->uri->segment(2);
		$uri3 = $CI->uri->segment(3);
		
		$where = array();
		
		if($uri1 != '' && $uri1 != null){
			$where['module_name'] = $uri1;
		}
		if($uri2 != '' && $uri2 != null){
			$where['controller_name'] = $uri2;
		}
		if($uri3 != '' && $uri3 != null){
			$where['function_name'] = $uri3;
		}
		
		if(count($where) > 0){
			$where['status'] = STATUS_NEW;
			
			$qry = $CI->db->get_where('ms_menu', $where);
			if($qry->num_rows() > 0){
				$row = $qry->row();
				
				$result = $row->menu_id;
			}
		}
		
		return $result;
	}

    #region Utils
    function neg_l($amount){
        if($amount < 0){
            return '(';
        }
    }

    function neg_r($amount){
        if($amount < 0){
            return ')';
        }else{
            return '&nbsp;';
        }
    }

    function amount_journal($amount){
        $result = '';
        $result .= neg_l($amount);
        $result .= number_format(abs($amount), 0, '.', ',');
        $result .= neg_r($amount);

        return $result;
    }

    function number_to_words($number){
        $result = '';

        $CI =& get_instance();

        $CI->load->library('tpdutil');

        //$result = $CI->tpdutil->convert_number_to_words($number);
		$obj = new currencyToWords($number);
		$result = $obj->words;

        return $result;
    }

    function tpd_404(){
        $CI =& get_instance();
        $CI->load->view('layout/not_found_404');
    }

    function btn_new($url = '', $text = '') {
        $btn = '<a href="' . $url . '" class="btn default yellow-stripe"><i class="fa fa-plus"></i><span class="hidden-480">&nbsp;&nbsp;' . $text . ' </span></a>';

        return $btn;
    }

    function btn_back($url = '', $text = 'Back', $class= 'default yellow-stripe', $class_icon = 'fa fa-arrow-circle-left') {
        $btn = '&nbsp;<a href="' . $url . '" class="btn ' . $class . '"><i class="' . $class_icon . '"></i><span class="hidden-480">&nbsp;&nbsp;' . $text . ' </span></a>';

        return $btn;
    }

    function btn_save($text = 'Save', $name = 'save', $class = 'btn blue-madison yellow-stripe', $class_icon = 'fa fa-save') {
        $btn = '&nbsp;<button type="submit" class="' . $class . '" name="' . $name . '"><i class="' . $class_icon . '"></i> &nbsp;&nbsp;' .  $text. ' </button>';

        return $btn;
    }

    function btn_save_close($text = 'Save & Close', $name = 'save_close', $class = 'btn blue-madison yellow-stripe', $class_icon = 'fa fa-sign-in') {
        $btn = '&nbsp;<button type="submit" class="' . $class . '" name="' . $name . '"><i class="' . $class_icon . '"></i> &nbsp;&nbsp;' .  $text. ' </button>';

        return $btn;
    }

    function btn_action($data_id = 0, $data_code = '', $action = STATUS_NEW, $text = '') {
        $class = 'fa';
        if ($action == STATUS_APPROVE) {
            $class = 'fa fa-check-square-o';
        } else if ($action == STATUS_CANCEL) {
            $class = 'fa fa-exclamation-triangle';
        } else if ($action == STATUS_DISAPPROVE) {
            $class = 'fa fa-reply-all';
        } else if ($action == STATUS_POSTED) {
            $class = 'fa fa-check-square-o';
        } else if ($action == STATUS_CLOSED) {
            $class = 'fa fa-check';
        } else if ($action == STATUS_PROCESS) {
					$class = 'fa fa-recycle';
				}

        $btn = '&nbsp;<a class="btn yellow-gold green-stripe btn-action" data-action="' . $action . '" data-id="' . $data_id . '" data-code="' . $data_code . '" data-action-code="' . ($text != '' ? $text : ucwords(strtolower(get_action_name($action, false)))) . '"><i class="' . $class . '"></i> &nbsp;&nbsp;' . ($text != '' ? $text : ucwords(strtolower(get_action_name($action, false)))) . '</a>';

        return $btn;
    }

    function btn_print($url = '', $text = 'Print', $class = '', $attr = 'target="_blank"') {
        $btn = '&nbsp;<a href="' . $url . '" ' . $attr . ' class="btn yellow-gold green-stripe ' . $class . '" target="_blank"><i class="fa fa-print"></i> &nbsp;&nbsp;' . $text . '</a>';

        return $btn;
    }

    function btn_add_detail($text = 'Add Detail', $id = 'btn_add_detail') {
        $btn = '<a class="btn btn-sm green-haze yellow-stripe" id="' . $id . '"><i class="fa fa-plus"></i><span> &nbsp;&nbsp;' . $text . ' </span></a>';

        return $btn;
    }

	function get_user_fullname($user_id){
		
		$CI =& get_instance();
		$where['user_id'] = $user_id;
		$qry = $CI->db->get_where('ms_user', $where);
			if($qry->num_rows() > 0){
				$row = $qry->row();
				
				$result = $row->user_fullname;
			}
		return $result;
	}

    function get_roman($number_){
        $result = '';

        $number = intval($number_);

        switch($number){
            case 1:
                $result = 'I';
                break;
            case 2:
                $result = 'II';
                break;
            case 3:
                $result = 'III';
                break;
            case 4:
                $result = 'IV';
                break;
            case 5:
                $result = 'V';
                break;
            case 6:
                $result = 'VI';
                break;
            case 7:
                $result = 'VII';
                break;
            case 8:
                $result = 'VIII';
                break;
            case 9:
                $result = 'IX';
                break;
            case 10:
                $result = 'X';
                break;
            case 11:
                $result = 'XI';
                break;
            case 12:
                $result = 'XXI';
                break;
            default:
                $result = "";
                break;
        }

        return $result;
    }

    function url_clean($string){
        $result = '';

        $string = str_replace("%2528", "(", $string);
        $string = str_replace("%2529", ")", $string);

        $result = urldecode($string);

        return $result;
    }

    #endregion

    function num_of_days($ymd_start, $ymd_end, $by_night = false){
        $startDate = DateTime::createFromFormat('Y-m-d', $ymd_start);
        $endDate = DateTime::createFromFormat('Y-m-d', $ymd_end);

        //Calculate days difference
        $diff_day = date_diff($startDate, $endDate, true);
        $totalDays = $diff_day->format('%a');
        if(!$by_night){
            $totalDays++;
        }

        return $totalDays;
    }

	function num_of_months($ymd_start, $ymd_end){
        $startDate = DateTime::createFromFormat('Y-m-d', $ymd_start);
        $endDate = DateTime::createFromFormat('Y-m-d', $ymd_end);
		$endDate->modify('+1 day');



        $diff =  $startDate->diff($endDate);
    	//$months = (($diff->y * 12) + $diff->m + $diff->d) / 30;
		//$months = ceil($startDate->diff($endDate)->m + ($startDate->diff($endDate)->y*12));
		$months= ($diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24);
		//echo '*Diff : ' . $startDate->diff($endDate)->m . ' + ' . $startDate->diff($endDate)->y*12 . ' = ' . $months . '* ';
   		return (int) round($months);
    }

    function days_in_month($month, $year)
    {
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

	function print_profile_inv() {
		$CI =& get_instance();

		$vat = array();

		$qry = $CI->db->get_where('print_profile', array('key_code' => 'INV'));
		if($qry->num_rows() > 0){
			return $qry->row_array();
		}

		return $vat;
	}

	function wkhtml_zoom(){
		$exist = strpos(strtolower(PHP_OS),'win');
        if ($exist === false) {
            $result = 0.99;
        }else{
			$result = 1.33;
		}

		return $result;
	}

	function wkhtml_print($params = array()){
		$CI =& get_instance();

		if(!isset($params['orientation'])){
			$params['orientation'] = 'portrait';
		}

		if(!isset($params['page-size'])){
			$params['page-size'] = 'A4';
		}

		if(!isset($params['zoom'])){
			$params['zoom'] = wkhtml_zoom();
		}

		$html = $CI->output->get_output();

		$CI->load->library('wkhtml');

		header('Content-Type: application/pdf');
		//header('Content-Disposition: attachment; filename="file.pdf"');
		header('Content-Disposition: inline; filename="file.pdf"');
		echo $CI->snappy->getOutputFromHtml($html, $params);
	}

    function wordwrap_string($string = '', $per_line = 100) {
        $result = array();
        $result['string'] = '';
        $result['line'] = 0;

        if ($string != '') {
            $string = str_replace("\n", "<br/>", $string);
            $array_line_string = explode("<br/>", $string);

            $line_number = count($array_line_string);

            if ($line_number > 0) {
                foreach ($array_line_string as $single_line_string) {
                    $length_string = strlen($single_line_string);

                    if ($length_string > $per_line) {
                        $parsed_text = wordwrap($single_line_string, $per_line, "<br/>");
                        $array_parsed_text = explode("<br/>", $parsed_text);

                        $result['string'] .= $parsed_text . "<br/>";
                        $result['line'] = $result['line'] + count($array_parsed_text);
                    } else {
                        $result['string'] .= $single_line_string . "<br/>";
                        $result['line']++;
                    }
                }
            } else {
                $result['line']++;
            }
        }

        return $result;
    }

    function table_to_col($table = array(), $key_column = ''){
        $result = array();

        if(count($table) > 0 && $key_column != ''){
            foreach($table as $row){
                array_push($result, $row[$key_column]);
            }
        }

        return $result;
    }

    function array_sum_by_col($table = array(), $sum_column = '', $key_column = '', $key_value = ''){
        $result = 0;

        if(count($table) > 0 && $sum_column != '' && $key_column != '' && $key_value != ''){
            foreach($table as $row){
                if(strtoupper($row[$key_column]) == strtoupper($key_value)){
                    $result += $row[$sum_column];
                }
            }
        }

        return $result;
    }

    #####  This function will proportionally resize image #####
    function normal_resize_image($source, $destination, $image_type, $max_size, $image_width, $image_height, $quality){
        //$CI =& get_instance();
        //$CI->load->helper('tpd_helper');

        if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize

        //do not resize if image is smaller than max size
        if($image_width <= $max_size && $image_height <= $max_size){
            if($this->save_image($source, $destination, $image_type, $quality)){
                return true;
            }
        }

        //Construct a proportional size of new image
        $image_scale    = min($max_size/$image_width, $max_size/$image_height);
        $new_width      = ceil($image_scale * $image_width);
        $new_height     = ceil($image_scale * $image_height);

        $new_canvas     = imagecreatetruecolor( $new_width, $new_height ); //Create a new true color image

        //Copy and resize part of an image with resampling
        if(imagecopyresampled($new_canvas, $source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height)){
            $this->save_image($new_canvas, $destination, $image_type, $quality); //save resized image
        }

        return true;
    }

    ##### This function corps image to create exact square, no matter what its original size! ######
    function crop_image_square($source, $destination, $image_type, $square_size, $image_width, $image_height, $quality){
        //$CI =& get_instance();
        //$CI->load->helper('tpd_helper');

        if($image_width <= 0 || $image_height <= 0){return false;} //return false if nothing to resize

        if( $image_width > $image_height )
        {
            $y_offset = 0;
            $x_offset = ($image_width - $image_height) / 2;
            $s_size     = $image_width - ($x_offset * 2);
        }else{
            $x_offset = 0;
            $y_offset = ($image_height - $image_width) / 2;
            $s_size = $image_height - ($y_offset * 2);
        }
        $new_canvas = imagecreatetruecolor( $square_size, $square_size); //Create a new true color image

        //Copy and resize part of an image with resampling
        if(imagecopyresampled($new_canvas, $source, 0, 0, $x_offset, $y_offset, $square_size, $square_size, $s_size, $s_size)){
            $this->save_image($new_canvas, $destination, $image_type, $quality);
        }

        return true;
    }

    ##### Saves image resource to file #####
    function save_image($source, $destination, $image_type, $quality){
        switch(strtolower($image_type)){//determine mime type
            case 'image/png':
                imagepng($source, $destination); return true; //save png file
                break;
            case 'image/gif':
                imagegif($source, $destination); return true; //save gif file
                break;
            case 'image/jpeg': case 'image/pjpeg':
            imagejpeg($source, $destination, $quality); return true; //save jpeg file
            break;
            default: return false;
        }
	}
	
	function gen_salt($data = '') {
		$alphabet = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
		$alpha_flip = array_flip($alphabet);
		$return_value = '';
		$length = strlen($data);
		for ($i = 0; $i < $length; $i++) {
			$return_value .= ($alpha_flip[$data[$i]] + 1);
		}
		return $return_value;
	}
?>