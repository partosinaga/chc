<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

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
		$this->user_manage();
	}
	
	public function user_manage($type = 1, $id = 0){
		$data_header = $this->data_header;
		
		if($type == 1){ //User List
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
			array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
			
			array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
			
			$data = array();

			$this->load->view('layout/header', $data_header);
			$this->load->view('admin/user_list', $data);
			$this->load->view('layout/footer');
		}
		else{ // User Form
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			array_push($data_header['style'], base_url() . 'assets/global/plugins/icheck/skins/all.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/icheck/icheck.min.js');
			
			$data = array();
			
			$data['user_id'] = $id;
			if($id > 0){
				$qry = $this->db->get_where('ms_user', array('user_id' => $id));
				$data['row'] = $qry->row();
			}

			$this->load->view('layout/header', $data_header);
			$this->load->view('admin/user_form', $data);
			$this->load->view('layout/footer');
		}
	}
	
	public function user_submit(){
		if(isset($_POST)){
			$has_error = false;
			$user_id = $_POST['user_id'];
			
			$data['user_name'] = trim($_POST['user_name']);
			$data['user_fullname'] = trim($_POST['user_fullname']);
			$data['user_email'] = trim($_POST['user_email']);
			$data['department_id'] = trim($_POST['department_id']);
			if(isset($_POST['user_isadmin'])){
				$data['user_isadmin'] = trim($_POST['user_isadmin']);
			}
			else {
				$data['user_isadmin'] = 0;
			}
			
			if($user_id > 0){
				if(trim($_POST['user_password']) != ''){
					$data['user_password'] = md5(trim($_POST['user_password']));
				}
				$data['status'] = $_POST['status'];
				
				$exist = $this->mdl_general->count('ms_user', array('user_name' => $data['user_name'], 'user_id <>' => $user_id));
				
				if($exist == 0){
					$this->mdl_general->update('ms_user', array('user_id' => $user_id), $data);
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful update user.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Username already exist.');
				}
			}
			else {
				$data['user_password'] = md5(trim($_POST['user_password']));
				$data['status'] = STATUS_NEW;
				
				$exist = $this->mdl_general->count('ms_user', array('user_name' => $data['user_name']));
				
				if($exist == 0){
					$this->db->insert('ms_user', $data);
					$user_id = $this->db->insert_id();
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful add user.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Username already exist.');
				}
			}
			
			if($has_error){
				redirect(base_url('admin/user/user_manage/0/' . $user_id . '.tpd'));
			}
			else {
				if(isset($_POST['save_close'])){
					redirect(base_url('admin/user/user_manage/1.tpd'));
				}
				else {
					redirect(base_url('admin/user/user_manage/0/' . $user_id . '.tpd'));
				}
			}
		}
	}
	
	public function user_delete($user_id = 0){
		if($user_id > 0){
			$this->mdl_general->update('ms_user', array('user_id' => $user_id), array('status' => STATUS_DELETE));
			
			$this->session->set_flashdata('flash_message_class', 'success');
			$this->session->set_flashdata('flash_message', 'Successful delete user.');
		}
		
		redirect(base_url('admin/user/user_manage/1.tpd'));
	}
	
	public function user_list(){
		$this->load->model('admin/mdl_user');
		
		$where['ms_user.status <>'] = STATUS_DELETE;
		$like = array();
		if(isset($_REQUEST['filter_username'])){
			if($_REQUEST['filter_username'] != ''){
				$like['ms_user.user_name'] = $_REQUEST['filter_username'];
			}
		}
		if(isset($_REQUEST['filter_fullname'])){
			if($_REQUEST['filter_fullname'] != ''){
				$like['ms_user.user_fullname'] = $_REQUEST['filter_fullname'];
			}
		}
		if(isset($_REQUEST['filter_email'])){
			if($_REQUEST['filter_email'] != ''){
				$like['ms_user.user_email'] = $_REQUEST['filter_email'];
			}
		}
		if(isset($_REQUEST['filter_dept'])){
			if($_REQUEST['filter_dept'] != ''){
				$where['ms_user.department_id'] = $_REQUEST['filter_dept'];
			}
		}
		if(isset($_REQUEST['filter_status'])){
			if($_REQUEST['filter_status'] != ''){
				$where['ms_user.status'] = $_REQUEST['filter_status'];
			}
		}
		if(isset($_REQUEST['filter_date_from'])){
			if($_REQUEST['filter_date_from'] != ''){
				$where['ms_user.last_login >='] = dmy_to_ymd($_REQUEST['filter_date_from']) . ' 00:00:00';
			}
		}
		if(isset($_REQUEST['filter_date_to'])){
			if($_REQUEST['filter_date_to'] != ''){
				$where['ms_user.last_login <='] = dmy_to_ymd($_REQUEST['filter_date_to']) . ' 23:59:59';
			}
		}
		
		$iTotalRecords = $this->mdl_general->count('ms_user', $where, $like);
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);
	  
		$records = array();
		$records["data"] = array();
		
		$order = 'ms_user.user_name asc';
		if(isset($_REQUEST['order'])){
			if($_REQUEST['order'][0]['column'] == 1){
				$order = 'ms_user.user_name ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 2){
				$order = 'ms_user.user_fullname ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 3){
				$order = 'ms_user.user_email ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 4){
				$order = 'ms_department.department_name ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 5){
				$order = 'ms_user.user_isadmin ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 6){
				$order = 'ms_user.last_login ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 7){
				$order = 'ms_user.status ' . $_REQUEST['order'][0]['dir'];
			}
		}
		
		$qry = $this->mdl_user->get($where, $like, $order, $iDisplayLength, $iDisplayStart);
		
		$i = $iDisplayStart + 1;
		foreach($qry->result() as $row){
			$records["data"][] = array(
				$i,
				$row->user_name,
				$row->user_fullname,
				$row->user_email,
				$row->department_name,
				($row->user_isadmin == 1 ? 'Y' : 'N'),
				date('d/m/Y H:i:s', strtotime($row->last_login)),
				get_status_active($row->status),
				'<div class="btn-group btn-group-solid">
					<a class="btn btn-xs green-meadow tooltips" data-original-title="View" data-placement="top" data-container="body" href="' . base_url('admin/user/user_manage/0/' . $row->user_id) . '.tpd"> <i class="fa fa-search"></i> </a>
					<a class="btn btn-xs red-thunderbird btn-bootbox tooltips" data-original-title="Delete" data-placement="top" data-container="body" href="javascript:;" data-id="' . $row->user_id . '"> <i class="fa fa-times"></i> </a>
				</div>'
			);
			$i++;
		}

		$records["draw"] = $sEcho;
		$records["recordsTotal"] = $iTotalRecords;
		$records["recordsFiltered"] = $iTotalRecords;
	  
		echo json_encode($records);
	}
}

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */