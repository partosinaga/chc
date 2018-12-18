<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_request extends CI_Controller {

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
		$this->stock_request_manage();
	}
	
	#region Stock_request  
	
	public function stock_request_manage($type = 1, $id = 0){
		$data_header = $this->data_header;
		
		array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
		array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
		array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
		array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
		
		array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
		array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
		array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
		array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
		array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
		array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
		
		array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
		
		$data = array();
		
		$data['qry_department'] = $this->mdl_general->get('ms_department', array('status' => STATUS_NEW), array(), 'department_name');
		
		if($type == 1){ //Request List
			$this->load->view('layout/header', $data_header);
			$this->load->view('inventory/stock_request/request_list.php', $data);
			$this->load->view('layout/footer');
		}
		else{ // Request Form
			array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
			array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
			
			$data['request_id'] = $id;
			if($id > 0){
				$qry = $this->db->get_where('in_request', array('request_id' => $id));
				$data['row'] = $qry->row();

                $this->load->model('inventory/mdl_request');
                $data['qry_detail'] = $this->mdl_request->get_request_detail(false, array('request_id' => $id));

                //echo $this->db->last_query();
			}

			$this->load->view('layout/header', $data_header);
			$this->load->view('inventory/stock_request/request_form', $data);
			$this->load->view('layout/footer');
		}
	}

    public function ajax_stock_request_submit(){
        $result = array();
        $result['success'] = '1';

        if(isset($_POST)){
            $this->db->trans_begin();

            $request_id = $_POST['request_id'];

            //header
            $data['request_date'] = dmy_to_ymd(trim($_POST['request_date']));
            $data['department_id'] = $_POST['department_id'];
            $data['is_pos'] = isset($_POST['is_pos']) ? '1' : '0';
            $data['remarks'] = trim($_POST['remarks']);

            if($request_id > 0){
                $qry = $this->db->get_where('in_request', array('request_id' => $request_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['request_date']);
                $arr_date_old = explode('-', $row->request_date);

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['request_code'] = $this->mdl_general->generate_code(Feature::FEATURE_STOCK_REQUEST, $data['request_date']);

                    if($data['request_code'] == ''){
                        $result['success'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }

                if($result['success'] == '1'){
                    $data['user_modified'] = my_sess('user_id');
                    $data['date_modified'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('in_request', array('request_id' => $request_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully update Stock Request.');
                }
            } else {
                $data['request_code'] = $this->mdl_general->generate_code(Feature::FEATURE_STOCK_REQUEST, $data['request_date']);

                if($data['request_code'] != ''){
                    $data['user_created'] = my_sess('user_id');
                    $data['date_created'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_NEW;

                    $this->db->insert('in_request', $data);
                    $request_id = $this->db->insert_id();

                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $request_id;
                    $data_log['feature_id'] = Feature::FEATURE_STOCK_REQUEST;
                    $data_log['log_subject'] = 'Create Stock Request (' . $data['request_code'] . ')';
                    $data_log['action_type'] = STATUS_NEW;
                    $this->db->insert('app_log', $data_log);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully add Stock Request.');
                } else {
                    $result['success'] = '0';
                    $result['message'] = 'Failed generating code.';
                }
            }

            //detail
            if (isset($_POST['request_detail_id'])) {
                foreach ($_POST['request_detail_id'] as $key => $val) {
                    $data_detail = array();

                    $status = $_POST['status'][$key];

                    if($status == STATUS_NEW) {
                        $data_detail['request_id'] = $request_id;
                        $data_detail['item_id'] = $_POST['item_id'][$key];
                        $data_detail['item_qty'] = $_POST['item_qty'][$key];
                        $data_detail['item_qty_remain'] = $data_detail['item_qty'];
                        $data_detail['uom_id'] = $_POST['uom_id'][$key];
                        $data_detail['on_hand_qty'] = $_POST['on_hand_qty'][$key];
                        $data_detail['status'] = STATUS_NEW;

                        if ($val > 0) {
                            $this->mdl_general->update('in_request_detail', array('request_detail_id' => $val), $data_detail);
                        } else {
                            $this->db->insert('in_request_detail', $data_detail);
                        }
                    } else {
                        if ($_POST['request_detail_id'][$key] > 0) {
                            $this->db->delete('in_request_detail', array('request_detail_id' => $val));
                        }
                    }
                }
            }

            if($result['success'] == '1') {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
                else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('inventory/stock_request/stock_request_manage/1.tpd');
                    }
                    else{
                        $result['link'] = base_url('inventory/stock_request/stock_request_manage/0/' . $request_id . '.tpd');
                    }
                }
            }

        } else {
            $result['success'] = '0';
            $result['message'] = 'No Post.';
        }

        echo json_encode($result);
    }
	
	public function ajax_action_request(){
		$result = array();

        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = array();

        $request_id = $_POST['request_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $request_id;
        $data_log['feature_id'] = Feature::FEATURE_STOCK_REQUEST;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($request_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('in_request', array('request_id' => $request_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_APPROVE) {
                    if ($row->status == STATUS_APPROVE) {
                        $result['valid'] = '0';
                        $result['message'] = 'Stock Request already approved.';
                    } else {
                        $this->mdl_general->update('in_request', array('request_id' => $request_id), $data);

                        $data_log['log_subject'] = 'Approve Stock Request (' . $row->request_code . ')';
                        $data_log['action_type'] = STATUS_APPROVE;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully approve stock request.';
                    }
                } else if ($data['status'] == STATUS_DISAPPROVE) {
                    if ($row->status == STATUS_DISAPPROVE) {
                        $result['valid'] = '0';
                        $result['message'] = 'Stock Request already disapprove.';
                    } else {
                        $stock_issue = $this->mdl_general->count('in_gi', array('request_id' => $request_id, 'status <>' => STATUS_CANCEL));
                        if($stock_issue > 0){
                            $result['valid'] = '0';
                            $result['message'] = 'Stock Request already have Stock Issue.<br/>Cancel Stock Issue First to Continue.';
                        } else {
                            $this->mdl_general->update('in_request', array('request_id' => $request_id), $data);

                            $data_log['log_subject'] = 'Disapprove Stock Request (' . $row->request_code . ')';
                            $data_log['action_type'] = STATUS_DISAPPROVE;
                            $this->db->insert('app_log', $data_log);

                            $result['message'] = 'Successfully disaprove stock request.';
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Stock Request already canceled.';
                    } else {
                        $stock_issue = $this->mdl_general->count('in_gi', array('request_id' => $request_id, 'status <>' => STATUS_CANCEL));
                        if($stock_issue > 0){
                            $result['valid'] = '0';
                            $result['message'] = 'Stock Request already have Stock Issue.<br/>Cancel Stock Issue First to Continue.';
                        } else {
                            $this->mdl_general->update('in_request', array('request_id' => $request_id), $data);

                            $data_log['log_subject'] = 'Cancel Stock Request (' . $row->request_code . ')';
                            $data_log['action_type'] = STATUS_CANCEL;
                            $this->db->insert('app_log', $data_log);

                            $result['message'] = 'Successfully cancel stock request.';
                        }
                    }
                } else if ($data['status'] == STATUS_CLOSED) {
                    if ($row->status == STATUS_CLOSED) {
                        $result['valid'] = '0';
                        $result['message'] = 'Stock Request already completed.';
                    } else {
                        $this->mdl_general->update('in_request', array('request_id' => $request_id), $data);

                        $data_log['log_subject'] = 'Complete Stock Request (' . $row->request_code . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully complete stock request.';
                    }
                }
            }
        }

        if($is_redirect){
            $this->session->set_flashdata('flash_message_class', ($result['valid'] == '1' ? 'success' : 'error'));
            $this->session->set_flashdata('flash_message', $result['message']);
        }

        echo json_encode($result);
	}

	public function ajax_stock_request_list($menu_id = 0){
		$this->load->model('inventory/mdl_request');
	 
		$where['in_request.status <>'] = STATUS_DELETE;
		$where_or = '';
		if (get_dept_name(my_sess('department_id')) != Purchasing::DEPT_GEN) {
			$where_or = "(in_request.user_created = " . my_sess('user_id') . " OR in_request.department_id = " . my_sess('department_id') . " OR ms_user.department_id = " . my_sess('department_id') . ")";
		}

		$like = array();
		if(isset($_REQUEST['filter_request_code'])){
			if($_REQUEST['filter_request_code'] != ''){
				$like['in_request.request_code'] = $_REQUEST['filter_request_code'];
			}
		}
		if(isset($_REQUEST['filter_request_date_from'])){
			if($_REQUEST['filter_request_date_from'] != ''){
				$where['in_request.request_date >='] = dmy_to_ymd($_REQUEST['filter_request_date_from']);
			}
		}
		if(isset($_REQUEST['filter_request_date_to'])){
			if($_REQUEST['filter_request_date_to'] != ''){
				$where['in_request.request_date <='] = dmy_to_ymd($_REQUEST['filter_request_date_to']);
			}
		}
		if(isset($_REQUEST['filter_dapertment_id'])){
			if($_REQUEST['filter_dapertment_id'] != ''){
				$where['in_request.department_id'] = $_REQUEST['filter_dapertment_id'];
			}
		}
		if(isset($_REQUEST['filter_created_by'])){
			if($_REQUEST['filter_created_by'] != ''){
				$like['ms_user.user_fullname'] = $_REQUEST['filter_created_by'];
			}
		}
		if(isset($_REQUEST['filter_remarks'])){
			if($_REQUEST['filter_remarks'] != ''){
				$like['in_request.remarks'] = $_REQUEST['filter_remarks'];
			}
		}
		if(isset($_REQUEST['filter_status'])){
			if($_REQUEST['filter_status'] != ''){
				$where['in_request.status'] = $_REQUEST['filter_status'];
			}
		}
		
		$iTotalRecords = $this->mdl_request->get_request(true, $where, $like, '', 0, 0, $where_or);
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);
	  
		$records = array();
		$records["data"] = array();
		
		$order = 'in_request.request_code asc';
		if(isset($_REQUEST['order'])){
			if($_REQUEST['order'][0]['column'] == 1){
				$order = 'in_request.request_code ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 2){
				$order = 'in_request.request_date ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 3){
				$order = 'ms_department.department_name ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 4){
				$order = 'ms_user.user_fullname ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 5){
				$order = 'in_request.remarks ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 6){
				$order = 'in_request.status ' . $_REQUEST['order'][0]['dir'];
			}
		}
		
		$qry = $this->mdl_request->get_request(false, $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_or);
        $records["debug"] = $this->db->last_query();
		
		$i = $iDisplayStart + 1;
		foreach($qry->result() as $row){
			
			$btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('inventory/stock_request/stock_request_manage/0/' . $row->request_id) . '.tpd">View</a> </li>';

			if($row->status == STATUS_NEW){
				if(check_session_action($menu_id, STATUS_APPROVE)){
					$btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_APPROVE . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '">' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '</a> </li>';
				}
				if(check_session_action($menu_id, STATUS_DISAPPROVE)){
					$btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_DISAPPROVE . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '">' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '</a> </li>';
				}
				if(check_session_action($menu_id, STATUS_CANCEL)){
					$btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
				}
                if(check_session_action($menu_id, STATUS_CLOSED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CLOSED . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '">Complete</a> </li>';
                }
			}
			
			if($row->status == STATUS_APPROVE){
				if(check_session_action($menu_id, STATUS_DISAPPROVE)){
					$btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_DISAPPROVE . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '">' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '</a> </li>';
				}
				if(check_session_action($menu_id, STATUS_CANCEL)){
					$btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
				}
                if(check_session_action($menu_id, STATUS_CLOSED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CLOSED . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '">Complete</a> </li>';
                }
			}
			
			if($row->status == STATUS_DISAPPROVE){
				if(check_session_action($menu_id, STATUS_APPROVE)){
					$btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_APPROVE . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '">' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '</a> </li>';
				}
				if(check_session_action($menu_id, STATUS_CANCEL)){
					$btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
				}
                if(check_session_action($menu_id, STATUS_CLOSED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CLOSED . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '">Complete</a> </li>';
                }
			}
				
			$records["data"][] = array(
				$i . '.',
				$row->request_code,
				ymd_to_dmy($row->request_date),
				$row->department_name,
				$row->user_fullname,
				$row->remarks,
				get_status_name($row->status),
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
	  
		echo json_encode($records);
	}

	public function ajax_ms_item_list(){
		$this->load->model('inventory/mdl_request');
		
		$where = array();
		$like = array();
		
		if(isset($_REQUEST['filter_item_code'])){
			if($_REQUEST['filter_item_code'] != ''){
				$like['item_code'] = $_REQUEST['filter_item_code'];
			}
		}
		if(isset($_REQUEST['filter_item_desc'])){
			if($_REQUEST['filter_item_desc'] != ''){
				$like['item_desc'] = $_REQUEST['filter_item_desc'];
			}
		}
		if(isset($_REQUEST['filter_uom_out_code'])){
			if($_REQUEST['filter_uom_out_code'] != ''){
				$like['uom_out_code'] = $_REQUEST['filter_uom_out_code'];
			}
		}
		if(isset($_REQUEST['filter_on_hand_qty'])){
			if($_REQUEST['filter_on_hand_qty'] != ''){
				$like['on_hand_qty'] = $_REQUEST['filter_on_hand_qty'];
			}
		}
		
		$iTotalRecords = $this->mdl_request->get_item(true, $where, $like);
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);
	  
		$records = array();
		$records["data"] = array();
		
		$order = 'item_code asc';
		if(isset($_REQUEST['order'])){
			if($_REQUEST['order'][0]['column'] == 1){
				$order = 'item_code ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 2){
				$order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 3){
				$order = 'uom_out_code ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 4){
				$order = 'on_hand_qty ' . $_REQUEST['order'][0]['dir'];
			}
		}
		
		$qry = $this->mdl_request->get_item(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $records["debug"] = $this->db->last_query();
		
		$i = $iDisplayStart + 1;
		foreach($qry->result() as $row){
			$records["data"][] = array(
				'<input style="margin-left:-10px;" type="radio" name="radio_item" value="' . $row->item_id . '" data-code="' . $row->item_code . '" data-desc="' . $row->item_desc . '" data-other-1="' . $row->uom_out_code . '" data-other-2="' . $row->uom_id_distribution . '" data-qty="' . $row->on_hand_qty . '" ' . ($row->on_hand_qty == 0 ? 'disabled="disabled"' : '') . ' />',
				$row->item_code,
				$row->item_desc,
				$row->uom_out_code,
				$row->on_hand_qty,
				''
			);
			$i++;
		}

		$records["draw"] = $sEcho;
		$records["recordsTotal"] = $iTotalRecords;
		$records["recordsFiltered"] = $iTotalRecords;
	  
		echo json_encode($records);
	}

    public function ajax_view_ms_item_list(){
        $this->load->view('inventory/stock_request/ajax_ms_item_list');
    }

}

/* End of file stock_request.php */
/* Location: ./application/controllers/inventory/stock_request.php */