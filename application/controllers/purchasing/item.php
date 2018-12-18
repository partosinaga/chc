<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Item extends CI_Controller {

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
		$this->class_manage();
	}
	
	#region Class Item
	
	public function class_manage($type = 1, $id = 0){
		$data_header = $this->data_header;
		
		if($type == 1){ //Class List
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
			
			array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
			
			$data = array();

			$this->load->view('layout/header', $data_header);
			$this->load->view('purchasing/item/class/class_list', $data);
			$this->load->view('layout/footer');
		}
		else{ // Class Form
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
			
			$data = array();
			
			$data['class_id'] = $id;
			if($id > 0){
				$qry = $this->db->get_where('in_ms_item_class', array('class_id' => $id));
				$data['row'] = $qry->row();
			}

			$this->load->view('layout/header', $data_header);
			$this->load->view('purchasing/item/class/class_form', $data);
			$this->load->view('layout/footer');
		}
	}
	
	public function class_submit(){
		if(isset($_POST)){
			$has_error = false;
			$class_id = $_POST['class_id'];
			
			$data['class_code'] = trim($_POST['class_code']);
			$data['class_desc'] = trim($_POST['class_desc']);
			
			if($class_id > 0){
				$data['user_modified'] = my_sess('user_id');
				$data['date_modified'] = date('Y-m-d H:i:s');
				$data['status'] = $_POST['status'];
				
				$exist = $this->mdl_general->count('in_ms_item_class', array('class_code' => $data['class_code'], 'class_id <>' => $class_id, 'status <>' => STATUS_DELETE));
				
				if($exist == 0){
					$this->mdl_general->update('in_ms_item_class', array('class_id' => $class_id), $data);
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful update class.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Class code already exist.');
				}
			}
			else {
				$data['user_created'] = my_sess('user_id');
				$data['date_created'] = date('Y-m-d H:i:s');
				$data['status'] = STATUS_NEW;
				
				$exist = $this->mdl_general->count('in_ms_item_class', array('class_code' => $data['class_code'], 'status <>' => STATUS_DELETE));
				
				if($exist == 0){
					$this->db->insert('in_ms_item_class', $data);
					$class_id = $this->db->insert_id();
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful add class.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Class code already exist.');
				}
			}
			
			if($has_error){
				redirect(base_url('purchasing/item/class_manage/0/' . $class_id . '.tpd'));
			}
			else {
				if(isset($_POST['save_close'])){
					redirect(base_url('purchasing/item/class_manage/1.tpd'));
				}
				else {
					redirect(base_url('purchasing/item/class_manage/0/' . $class_id . '.tpd'));
				}
			}
		}
	}
	
	public function class_delete($class_id = 0){
		if($class_id > 0){
			$jml_group = $this->mdl_general->count('in_ms_item_group', array('class_id' => $class_id, 'status' => STATUS_NEW), array());
			
			if($jml_group > 0){
				$this->session->set_flashdata('flash_message_class', 'danger');
				$this->session->set_flashdata('flash_message', 'Cannot delete class. Item class already used in item group.');
			}
			else {
				$this->mdl_general->update('in_ms_item_class', array('class_id' => $class_id), array('status' => STATUS_DELETE));
				
				$this->session->set_flashdata('flash_message_class', 'success');
				$this->session->set_flashdata('flash_message', 'Successful delete class.');
			}
		}
		
		redirect(base_url('purchasing/item/class_manage/1.tpd'));
	}
	
	public function class_list($menu_id = 0){
		$where['status <>'] = STATUS_DELETE;
		$like = array();
		if(isset($_REQUEST['filter_classcode'])){
			if($_REQUEST['filter_classcode'] != ''){
				$like['class_code'] = $_REQUEST['filter_classcode'];
			}
		}
		if(isset($_REQUEST['filter_classdesc'])){
			if($_REQUEST['filter_classdesc'] != ''){
				$like['class_desc'] = $_REQUEST['filter_classdesc'];
			}
		}
		if(isset($_REQUEST['filter_status'])){
			if($_REQUEST['filter_status'] != ''){
				$where['status'] = $_REQUEST['filter_status'];
			}
		}
		
		$iTotalRecords = $this->mdl_general->count('in_ms_item_class', $where, $like);
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);
	  
		$records = array();
		$records["data"] = array();
		
		$order = 'class_code asc';
		if(isset($_REQUEST['order'])){
			if($_REQUEST['order'][0]['column'] == 1){
				$order = 'class_code ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 2){
				$order = 'class_desc ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 3){
				$order = 'status ' . $_REQUEST['order'][0]['dir'];
			}
		}
		
		$qry = $this->mdl_general->get('in_ms_item_class', $where, $like, $order, $iDisplayLength, $iDisplayStart);
		
		$i = $iDisplayStart + 1;
		foreach($qry->result() as $row){
			$records["data"][] = array(
				$i . '.',
				$row->class_code,
				$row->class_desc,
				get_status_active($row->status),
				'<div class="btn-group btn-group-solid">
					' . (check_session_action($menu_id, STATUS_EDIT) ? '<a class="btn btn-xs green-meadow tooltips" data-original-title="View" data-placement="top" data-container="body" href="' . base_url('purchasing/item/class_manage/0/' . $row->class_id) . '.tpd"> <i class="fa fa-search"></i> </a>' : '') . '
					' . (check_session_action($menu_id, STATUS_DELETE) ? '<a class="btn btn-xs red-thunderbird btn-bootbox tooltips" data-original-title="Delete" data-placement="top" data-container="body" href="javascript:;" data-id="' . $row->class_id . '"> <i class="fa fa-times"></i> </a>' : '') . '
				</div>'
			);
			$i++;
		}

		$records["draw"] = $sEcho;
		$records["recordsTotal"] = $iTotalRecords;
		$records["recordsFiltered"] = $iTotalRecords;
	  
		echo json_encode($records);
	}
	
	#endregion

	#region Group Item
	
	public function group_manage($type = 1, $id = 0){
		$data_header = $this->data_header;
		
		$data = array();
		
		$data['qry_class'] = $this->mdl_general->get('in_ms_item_class', array('status' => STATUS_NEW), array(), 'class_code');
		$data['qry_department'] = $this->mdl_general->get('ms_department', array('status' => STATUS_NEW), array(), 'department_name');
		
		if($type == 1){ //Group List
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
			
			array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
			
			$this->load->view('layout/header', $data_header);
			$this->load->view('purchasing/item/group/group_list', $data);
			$this->load->view('layout/footer');
		}
		else{ //Group Form
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			
			$data['group_id'] = $id;
			if($id > 0){
				$qry = $this->db->get_where('in_ms_item_group', array('group_id' => $id));
				$data['row'] = $qry->row();
			}

			$this->load->view('layout/header', $data_header);
			$this->load->view('purchasing/item/group/group_form', $data);
			$this->load->view('layout/footer');
		}
	}
	
	public function group_submit(){
		if(isset($_POST)){
			$has_error = false;
			$group_id = $_POST['group_id'];
			
			$data['class_id'] = trim($_POST['class_id']);
			$data['department_id'] = trim($_POST['department_id']);
			$data['group_code'] = trim($_POST['group_code']);
			$data['group_desc'] = trim($_POST['group_desc']);
			
			if($group_id > 0){
				$data['user_modified'] = my_sess('user_id');
				$data['date_modified'] = date('Y-m-d H:i:s');
				$data['status'] = $_POST['status'];
				
				$exist = $this->mdl_general->count('in_ms_item_group', array('class_id' => $data['class_id'], 'group_id <>' => $group_id, 'group_code' => $data['group_code'], 'status <>' => STATUS_DELETE, ''));
				
				if($exist == 0){
					$this->mdl_general->update('in_ms_item_group', array('group_id' => $group_id), $data);
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful update group.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Group code already exist.');
				}
			}
			else {
				$data['user_created'] = my_sess('user_id');
				$data['date_created'] = date('Y-m-d H:i:s');
				$data['status'] = STATUS_NEW;
				
				$exist = $this->mdl_general->count('in_ms_item_group', array('class_id' => $data['class_id'], 'group_code' => $data['group_code'], 'status <>' => STATUS_DELETE));
				
				if($exist == 0){
					$this->db->insert('in_ms_item_group', $data);
					$group_id = $this->db->insert_id();
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful add group.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Group code already exist.');
				}
			}
			
			if($has_error){
				redirect(base_url('purchasing/item/group_manage/0/' . $group_id . '.tpd'));
			}
			else {
				if(isset($_POST['save_close'])){
					redirect(base_url('purchasing/item/group_manage/1.tpd'));
				}
				else {
					redirect(base_url('purchasing/item/group_manage/0/' . $group_id . '.tpd'));
				}
			}
		}
	}
	
	public function group_delete($group_id = 0){
		if($group_id > 0){
			$jml_item = $this->mdl_general->count('in_ms_item', array('group_id' => $group_id, 'status' => STATUS_NEW), array());
			
			if($jml_item > 0){
				$this->session->set_flashdata('flash_message_class', 'danger');
				$this->session->set_flashdata('flash_message', 'Cannot delete group. Item group already used in item.');
			}
			else {
				$this->mdl_general->update('in_ms_item_group', array('group_id' => $group_id), array('status' => STATUS_DELETE));
				
				$this->session->set_flashdata('flash_message_class', 'success');
				$this->session->set_flashdata('flash_message', 'Successful delete group.');
			}
		}
		
		redirect(base_url('purchasing/item/group_manage/1.tpd'));
	}
	
	public function group_list($menu_id = 0){
		$this->load->model('purchasing/mdl_purchasing');
		
		$where['in_ms_item_group.status <>'] = STATUS_DELETE;
		$like = array();
		if(isset($_REQUEST['filter_groupcode'])){
			if($_REQUEST['filter_groupcode'] != ''){
				$like['in_ms_item_group.group_code'] = $_REQUEST['filter_groupcode'];
			}
		}
		if(isset($_REQUEST['filter_groupdesc'])){
			if($_REQUEST['filter_groupdesc'] != ''){
				$like['in_ms_item_group.group_desc'] = $_REQUEST['filter_groupdesc'];
			}
		}
		if(isset($_REQUEST['filter_class_id'])){
			if($_REQUEST['filter_class_id'] != ''){
				$where['in_ms_item_group.class_id'] = $_REQUEST['filter_class_id'];
			}
		}
		if(isset($_REQUEST['filter_department_id'])){
			if($_REQUEST['filter_department_id'] != ''){
				$where['in_ms_item_group.department_id'] = $_REQUEST['filter_department_id'];
			}
		}
		if(isset($_REQUEST['filter_status'])){
			if($_REQUEST['filter_status'] != ''){
				$where['in_ms_item_group.status'] = $_REQUEST['filter_status'];
			}
		}
		
		$iTotalRecords = $this->mdl_general->count('in_ms_item_group', $where, $like);
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);
	  
		$records = array();
		$records["data"] = array();
		
		$order = 'in_ms_item_class.class_code asc, in_ms_item_group.group_code asc';
		if(isset($_REQUEST['order'])){
			if($_REQUEST['order'][0]['column'] == 1){
				$order = 'class_code ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 2){
				$order = 'in_ms_item_group.group_code ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 3){
				$order = 'in_ms_item_group.group_desc ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 4){
				$order = 'ms_department.department_name ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 5){
				$order = 'in_ms_item_group.status ' . $_REQUEST['order'][0]['dir'];
			}
		}
		
		$qry = $this->mdl_purchasing->get_item_group($where, $like, $order, $iDisplayLength, $iDisplayStart);
		
		$i = $iDisplayStart + 1;
		foreach($qry->result() as $row){
			$records["data"][] = array(
				$i . '.',
				$row->class_desc,
				$row->group_code,
				$row->group_desc,
				$row->department_desc,
				get_status_active($row->status),
				'<div class="btn-group btn-group-solid">
					' . (check_session_action($menu_id, STATUS_EDIT) ? '<a class="btn btn-xs green-meadow tooltips" data-original-title="View" data-placement="top" data-container="body" href="' . base_url('purchasing/item/group_manage/0/' . $row->group_id) . '.tpd"> <i class="fa fa-search"></i> </a>' : '') . '
					' . (check_session_action($menu_id, STATUS_DELETE) ? '<a class="btn btn-xs red-thunderbird btn-bootbox tooltips" data-original-title="Delete" data-placement="top" data-container="body" href="javascript:;" data-id="' . $row->group_id . '"> <i class="fa fa-times"></i> </a>' : '') . '
				</div>'
			);
			$i++;
		}

		$records["draw"] = $sEcho;
		$records["recordsTotal"] = $iTotalRecords;
		$records["recordsFiltered"] = $iTotalRecords;
	  
		echo json_encode($records);
	}
	
	#endregion	
	
	#region service Item
	
	public function service_manage($type = 1, $id = 0){
		$data_header = $this->data_header;
		
		$data = array();
		
		$data['qry_uom'] = $this->mdl_general->get('in_ms_uom', array('status' => STATUS_NEW), array(), 'uom_code');
		 
		if($type == 1){ //Group List
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
			
			array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
			
			$this->load->view('layout/header', $data_header);
			$this->load->view('purchasing/item/service/service_list', $data);
			$this->load->view('layout/footer');
		}
		else{ //Group Form
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			
			$data['item_id'] = $id;
			if($id > 0){
				$qry = $this->db->get_where('in_ms_item', array('item_id' => $id));
				$data['row'] = $qry->row();
			}

			$this->load->view('layout/header', $data_header);
			$this->load->view('purchasing/item/service/service_form', $data);
			$this->load->view('layout/footer');
		}
	}
	public function service_list($menu_id = 0){
		$this->load->model('purchasing/mdl_purchasing');
		
		$where['in_ms_item.status <>'] = STATUS_DELETE;
		$where['in_ms_item.item_type ='] = "2";
		$like = array();
		if(isset($_REQUEST['filter_servicecode'])){
			if($_REQUEST['filter_servicecode'] != ''){
				$like['in_ms_item.item_code'] = $_REQUEST['filter_servicecode'];
			}
		} 
		if(isset($_REQUEST['filter_servicedesc'])){
			if($_REQUEST['filter_servicedesc'] != ''){
				$like['in_ms_item.item_desc'] = $_REQUEST['filter_servicedesc'];
			}
		}  
		if(isset($_REQUEST['filter_uom_id'])){
			if($_REQUEST['filter_uom_id'] != ''){
				$where['in_ms_item.uom_id'] = $_REQUEST['filter_uom_id'];
			}
		} 
		if(isset($_REQUEST['filter_status'])){
			if($_REQUEST['filter_status'] != ''){
				$where['in_ms_item.status'] = $_REQUEST['filter_status'];
			}
		}
		
		$iTotalRecords = $this->mdl_general->count('in_ms_item', $where, $like);
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);
	  
		$records = array();
		$records["data"] = array();
		
		$order = 'in_ms_item.item_code asc ';
		if(isset($_REQUEST['order'])){
			if($_REQUEST['order'][0]['column'] == 1){
				$order = 'item_code ' . $_REQUEST['order'][0]['dir'];
			} 
		}
		$qry = $this->mdl_purchasing->get_item_service($where, $like, $order, $iDisplayLength, $iDisplayStart);
		
		$i = $iDisplayStart + 1;
		foreach($qry->result() as $row){
			$records["data"][] = array(
				$i . '.',
				$row->item_code,
				$row->item_desc,
				$row->uom_code ,
				get_status_active($row->status),
				'<div class="btn-group btn-group-solid">
					' . (check_session_action($menu_id, STATUS_EDIT) ? '<a class="btn btn-xs green-meadow tooltips" data-original-title="View" data-placement="top" data-container="body" href="' . base_url('purchasing/item/service_manage/0/' . $row->item_id) . '.tpd"> <i class="fa fa-search"></i> </a>' : '') . '
					' . (check_session_action($menu_id, STATUS_DELETE) ? '<a class="btn btn-xs red-thunderbird btn-bootbox tooltips" data-original-title="Delete" data-placement="top" data-container="body" href="javascript:;" data-id="' . $row->item_id . '"> <i class="fa fa-times"></i> </a>' : '') . '
				</div>'
			);
			$i++;
		}

		$records["draw"] = $sEcho;
		$records["recordsTotal"] = $iTotalRecords;
		$records["recordsFiltered"] = $iTotalRecords;
	  
		echo json_encode($records);
	}
	
	public function service_submit(){
	
		$this->load->model('purchasing/mdl_purchasing');
		
		if(isset($_POST)){
			$has_error = false;
			$item_id = $_POST['item_id'];
			
			$data['item_code'] = trim($_POST['item_code']);
			$data['group_id'] = trim($_POST['group_id']);
			$data['item_desc'] = trim($_POST['item_desc']);
			$data['remarks'] = trim($_POST['remarks']);
			//$data['account_coa_id'] = trim($_POST['account_coa_id']);
			$data['exp_coa_id'] = trim($_POST['exp_coa_id']);
			$data['uom_id'] = trim($_POST['uom_id']);
			$data['qty_distribution'] = trim($_POST['qty_distribution']);
			$data['uom_id_distribution'] = trim($_POST['uom_id_distribution']); 
			$data['item_type'] = "2";
			if($item_id > 0){
				$data['user_modified'] = my_sess('user_id');
				$data['date_modified'] = date('Y-m-d H:i:s');
				$data['status'] = $_POST['status'];
				
				$exist = $this->mdl_general->count('in_ms_item', array('item_code' => $data['item_code'], 'item_id <>' => $item_id));
				
				if($exist == 0){
					$this->mdl_general->update('in_ms_item', array('item_id' => $item_id), $data);
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful update class.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Class code already exist.');
				}
			}
			else {
				$data['user_created'] = my_sess('user_id');
				$data['date_created'] = date('Y-m-d H:i:s');
				$data['status'] = STATUS_NEW; 
				
				$qry = $this->mdl_purchasing->get_item_code($data['group_id']);
				//echo $this->db->last_query() ; 
				$row = $qry->row(); 
				$data['item_code'] = $row->nomer ;
				
				$exist = $this->mdl_general->count('in_ms_item', array('item_code' => $data['item_code']));
				
				if($exist == 0){
					$this->db->insert('in_ms_item', $data);
					$item_id = $this->db->insert_id();
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful add class.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Class code already exist.');
				}
			}
			
			if($has_error){
				redirect(base_url('purchasing/item/service_manage/0/' . $item_id . '.tpd'));
			}
			else {
				if(isset($_POST['save_close'])){
					redirect(base_url('purchasing/item/service_manage/1.tpd'));
				}
				else {
					redirect(base_url('purchasing/item/service_manage/0/' . $item_id . '.tpd'));
				}
			}
		}
	}
	 
	#endregion	service item
	
	
	#region material Item
	
	public function material_manage($type = 1, $id = 0){
		$data_header = $this->data_header;
		
		$data = array();
		
		$data['qry_uom'] = $this->mdl_general->get('in_ms_uom', array('status' => STATUS_NEW), array(), 'uom_code');
		 
		if($type == 1){ //Group List
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
			
			array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
			
			$this->load->view('layout/header', $data_header);
			$this->load->view('purchasing/item/material/material_list', $data);
			$this->load->view('layout/footer');
		}
		else{ //Group Form
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			
			$data['item_id'] = $id;
			if($id > 0){
				$qry = $this->db->get_where('in_ms_item', array('item_id' => $id));
				$data['row'] = $qry->row();
			}

			$this->load->view('layout/header', $data_header);
			$this->load->view('purchasing/item/material/material_form', $data);
			$this->load->view('layout/footer');
		}
	}
	public function material_list($menu_id = 0){
		$this->load->model('purchasing/mdl_purchasing');
		
		$where['in_ms_item.status <>'] = STATUS_DELETE;
		$where['in_ms_item.item_type ='] = "1";
		$like = array();
		if(isset($_REQUEST['filter_materialcode'])){
			if($_REQUEST['filter_materialcode'] != ''){
				$like['in_ms_item.item_code'] = $_REQUEST['filter_materialcode'];
			}
		} 
		if(isset($_REQUEST['filter_materialdesc'])){
			if($_REQUEST['filter_materialdesc'] != ''){
				$like['in_ms_item.item_desc'] = $_REQUEST['filter_materialdesc'];
			}
		}  
		if(isset($_REQUEST['filter_uom_id'])){
			if($_REQUEST['filter_uom_id'] != ''){
				$where['in_ms_item.uom_id'] = $_REQUEST['filter_uom_id'];
			}
		} 
		if(isset($_REQUEST['filter_factor'])){
			if($_REQUEST['filter_factor'] != ''){
				$where['in_ms_item.qty_distribution'] = $_REQUEST['filter_factor'];
			}
		} 
		
		if(isset($_REQUEST['filter_uom_id_issue'])){
			if($_REQUEST['filter_uom_id_issue'] != ''){
				$where['in_ms_item.uom_id_distribution'] = $_REQUEST['filter_uom_id_issue'];
			}
		} 
		if(isset($_REQUEST['filter_status'])){
			if($_REQUEST['filter_status'] != ''){
				$where['in_ms_item.status'] = $_REQUEST['filter_status'];
			}
		}
		if(isset($_REQUEST['filter_min_stock'])){
			if($_REQUEST['filter_min_stock'] != ''){
				$where['in_ms_item.min_stock'] = $_REQUEST['filter_min_stock'];
			}
		}
		if(isset($_REQUEST['filter_max_stock'])){
			if($_REQUEST['filter_max_stock'] != ''){
				$where['in_ms_item.max_stock'] = $_REQUEST['filter_max_stock'];
			}
		}
		
		$iTotalRecords = $this->mdl_general->count('in_ms_item', $where, $like);
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);
	  
		$records = array();
		$records["data"] = array();
		
		$order = 'in_ms_item.item_code asc ';
		if(isset($_REQUEST['order'])){
			if($_REQUEST['order'][0]['column'] == 1){
				$order = 'item_code ' . $_REQUEST['order'][0]['dir'];
			} 
		}
		$qry = $this->mdl_purchasing->get_item_material($where, $like, $order, $iDisplayLength, $iDisplayStart);
		
		$i = $iDisplayStart + 1;
		foreach($qry->result() as $row){
			$act = '';
			if ($row->item_code != Purchasing::DIRECT_PURCHASE) {
				$act = '<div class="btn-group btn-group-solid">
					' . (check_session_action($menu_id, STATUS_EDIT) ? '<a class="btn btn-xs green-meadow tooltips" data-original-title="View" data-placement="top" data-container="body" href="' . base_url('purchasing/item/material_manage/0/' . $row->item_id) . '.tpd"> <i class="fa fa-search"></i> </a>' : '') . '
					' . (check_session_action($menu_id, STATUS_DELETE) ? '<a class="btn btn-xs red-thunderbird btn-bootbox tooltips" data-original-title="Delete" data-placement="top" data-container="body" href="javascript:;" data-id="' . $row->item_id . '"> <i class="fa fa-times"></i> </a>' : '') . '
				</div>';
			}
			$records["data"][] = array(
				$i . '.',
				$row->item_code,
				$row->item_desc,
				$row->uom_code ,
				number_format($row->qty_distribution, 0, '.', ',') , 
				$row->uom_code_issue ,
				number_format($row->min_stock, 0, '.', ',') ,				
				number_format($row->max_stock, 0, '.', ',') ,				
				get_status_active($row->status),
				$act
			);
			$i++;
		}

		$records["draw"] = $sEcho;
		$records["recordsTotal"] = $iTotalRecords;
		$records["recordsFiltered"] = $iTotalRecords;
	  
		echo json_encode($records);
	}
	
	public function material_submit(){
	
		$this->load->model('purchasing/mdl_purchasing');
		
		if(isset($_POST)){
			$has_error = false;
			$item_id = $_POST['item_id'];
			
			$data['item_code'] = trim($_POST['item_code']);
			$data['group_id'] = intval(trim($_POST['group_id']));
			$data['item_desc'] = trim($_POST['item_desc']);
			$data['remarks'] = trim($_POST['remarks']);
			$data['account_coa_id'] = intval(trim($_POST['account_coa_id']));
			$data['exp_coa_id'] = intval(trim($_POST['exp_coa_id']));
			$data['uom_id'] = intval(trim($_POST['uom_id']));
			$data['qty_distribution'] = intval(trim($_POST['qty_distribution']));
			$data['uom_id_distribution'] = intval(trim($_POST['uom_id_distribution']));
			$data['min_stock'] = intval(trim($_POST['min_stock']));
			$data['max_stock'] = intval(trim($_POST['max_stock']));
			$data['item_type'] = 1;
			if($item_id > 0){
				$qry = $this->db->get_where('in_ms_item', array('item_id' => $item_id));
				$row = $qry->row();

				$data['user_modified'] = my_sess('user_id');
				$data['date_modified'] = date('Y-m-d H:i:s');
				$data['status'] = $_POST['status'];

				if ($data['group_id'] != $row->group_id) {
					/*$qry_code = $this->mdl_purchasing->get_item_code($data['group_id']);
					$row_code = $qry_code->row();

					$data['item_code'] = $row_code->nomer;*/

                    $data['item_code'] = $this->mdl_purchasing->get_item_code($data['group_id']);
				}

				$this->mdl_general->update('in_ms_item', array('item_id' => $item_id), $data);

				$this->session->set_flashdata('flash_message_class', 'success');
				$this->session->set_flashdata('flash_message', 'Successful update class.');
			}
			else {
				$data['user_created'] = my_sess('user_id');
				$data['date_created'] = date('Y-m-d H:i:s');
				$data['status'] = STATUS_NEW; 
				
				/*$qry = $this->mdl_purchasing->get_item_code($data['group_id']);

				$row = $qry->row(); 
				$data['item_code'] = $row->nomer ;*/
                $data['item_code'] = $this->mdl_purchasing->get_item_code($data['group_id']);
				
				$exist = $this->mdl_general->count('in_ms_item', array('item_code' => $data['item_code']));
				
				if($exist == 0){
					$this->db->insert('in_ms_item', $data);
					$item_id = $this->db->insert_id();
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful add class.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Class code already exist.');
				}
			}
			
			if($has_error){
				redirect(base_url('purchasing/item/material_manage/0/' . $item_id . '.tpd'));
			}
			else {
				if(isset($_POST['save_close'])){
					redirect(base_url('purchasing/item/material_manage/1.tpd'));
				}
				else {
					redirect(base_url('purchasing/item/material_manage/0/' . $item_id . '.tpd'));
				}
			}
		}
	}
	
    public function book_item(){ 
	
		$data = array();		
		$data['qry_uom'] = $this->mdl_general->get('in_ms_uom', array('status <> ' => STATUS_DELETE), array(), 'uom_code');		

        echo $this->load->view('general/book_item', $data);
    } 
	
    public function json_item(){
       $this->load->model('purchasing/mdl_purchasing');
		
        $where = array();
        $like = array();
		$where['in_ms_item.status <>'] = STATUS_DELETE;  
		
		if(isset($_REQUEST['filter_item_code'])){
			if($_REQUEST['filter_item_code'] != ''){
				$like['in_ms_item.item_code'] = $_REQUEST['filter_item_code'];
			}
		} 
		if(isset($_REQUEST['filter_item_desc'])){
			if($_REQUEST['filter_item_desc'] != ''){
				$like['in_ms_item.item_desc'] = $_REQUEST['filter_item_desc'];
			}
		}  
		if(isset($_REQUEST['filter_uom_id'])){
			if($_REQUEST['filter_uom_id'] != ''){
				$where['in_ms_item.uom_id'] = $_REQUEST['filter_uom_id'];
			}
		} 
		if(isset($_REQUEST['filter_factor'])){
			if($_REQUEST['filter_factor'] != ''){
				$where['in_ms_item.qty_distribution'] = $_REQUEST['filter_factor'];
			}
		} 
		
		if(isset($_REQUEST['filter_uom_id_issue'])){
			if($_REQUEST['filter_uom_id_issue'] != ''){
				$where['in_ms_item.uom_id_distribution'] = $_REQUEST['filter_uom_id_issue'];
			}
		}  
		
		$iTotalRecords = $this->mdl_general->count('in_ms_item', $where, $like);
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);
	  
		$records = array();
		$records["data"] = array();
		
		$order = 'in_ms_item.item_code asc ';
		if(isset($_REQUEST['order'])){
			if($_REQUEST['order'][0]['column'] == 1){
				$order = 'item_code ' . $_REQUEST['order'][0]['dir'];
			} 
		}
		$qry = $this->mdl_purchasing->get_item_material($where, $like, $order, $iDisplayLength, $iDisplayStart);
		
		$i = $iDisplayStart + 1;
		foreach($qry->result() as $row){
			$records["data"][] = array(
				'<input type="radio" name="radio_item" value="' . $row->item_id . '" data-code="' . $row->item_code . '" data-desc="' . $row->item_desc . '"   data-other-1="' . $row->uom_code . '" />',
                $row->item_code,
				$row->item_desc,
				$row->uom_code ,
				number_format($row->qty_distribution, 0, '.', ',') , 
				$row->uom_code_issue , 
				''
			);
			$i++;
		}

		$records["draw"] = $sEcho;
		$records["recordsTotal"] = $iTotalRecords;
		$records["recordsFiltered"] = $iTotalRecords;
	  
		echo json_encode($records);
    }

	#endregion	material item
	
}

/* End of file item.php */
/* Location: ./application/controllers/purchsing/item.php */