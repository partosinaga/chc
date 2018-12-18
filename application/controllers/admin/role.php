<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Role extends CI_Controller {

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
		$this->role_manage();
	}
	
	public function role_manage($type = 1, $id = 0){
		$data_header = $this->data_header;
		
		if($type == 1){ //Role List
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
			
			array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
			
			$data = array();

			$this->load->view('layout/header', $data_header);
			$this->load->view('admin/role_list', $data);
			$this->load->view('layout/footer');
		}
		else if($type == 2){ //User Role
			array_push($data_header['style'], base_url() . 'assets/global/plugins/icheck/skins/all.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/icheck/icheck.min.js');
			
			$data = array();
			
			$data['role_id'] = $id;
			if($id > 0){
				$qry = $this->db->get_where('ms_role', array('role_id' => $id));
				$data['row'] = $qry->row();
			}

			$this->load->view('layout/header', $data_header);
			$this->load->view('admin/role_user', $data);
			$this->load->view('layout/footer');
		}
		else{ // Role Form
			array_push($data_header['style'], base_url() . 'assets/global/plugins/icheck/skins/all.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/icheck/icheck.min.js');
			
			$data = array();
			
			$data['role_id'] = $id;
			if($id > 0){
				$qry = $this->db->get_where('ms_role', array('role_id' => $id));
				$data['row'] = $qry->row();
			}

			$this->load->view('layout/header', $data_header);
			$this->load->view('admin/role_form', $data);
			$this->load->view('layout/footer');
		}
	}
	
	public function role_submit(){
		if(isset($_POST)){
			$has_error = false;
			
			$role_id = $_POST['role_id'];
			$data['role_name'] = trim($_POST['role_name']);
			$data['role_desc'] = trim($_POST['role_desc']);
			$data['status'] = STATUS_NEW;
			
			if($role_id > 0){
				$exist = $this->mdl_general->count('ms_role', array('role_name' => $data['role_name'], 'role_id <>' => $role_id));
				
				if($exist == 0){
					$this->mdl_general->update('ms_role', array('role_id' => $role_id), $data);
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful update role.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Role name already exist.');
				}
			}
			else {
				$exist = $this->mdl_general->count('ms_role', array('role_name' => $data['role_name']));
				
				if($exist == 0){
					$this->db->insert('ms_role', $data);
					$role_id = $this->db->insert_id();
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful add role.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Role name already exist.');
				}
			}
			
			if($has_error == false){
				$this->db->delete('ms_role_detail', array('role_id' => $role_id));
				
				if(isset($_POST['menu_id'])) {
					$menu_id = 0;
					foreach($_POST['menu_id'] as $key => $val) {
						$is_input = false;
						foreach($_POST['menu_id'][$key] as $key_d => $val_d) {
							if($key != $menu_id){
								if($key_d == STATUS_VIEW){
									$is_input = true;
									
									$menu_id = $key;
								}
								
							}
							if($is_input){
								$this->db->insert('ms_role_detail', array('role_id' => $role_id, 'menu_id' => $key, 'action_do' => $key_d));
							}
						}
					}
				}
			}
			
			if($has_error){
				redirect(base_url('admin/role/role_manage/0/' . $role_id . '.tpd'));
			}
			else {
				if(isset($_POST['save_close'])){
					redirect(base_url('admin/role/role_manage/1.tpd'));
				}
				else {
					redirect(base_url('admin/role/role_manage/0/' . $role_id . '.tpd'));
				}
			}
		}
	}
	
	public function role_user_submit(){
		if(isset($_POST)){
			$role_id = $_POST['role_id'];
			
			$this->db->delete('ms_role_user', array('role_id' => $role_id));
			
			if(isset($_POST['user_role'])) {
				foreach($_POST['user_role'] as $key => $val) {
					$this->db->insert('ms_role_user', array('role_id' => $role_id, 'user_id' => $key));
				}
			}
			
			$this->session->set_flashdata('flash_message_class', 'success');
			$this->session->set_flashdata('flash_message', 'Successful update user role.');
			
			if(isset($_POST['save_close'])){
				redirect(base_url('admin/role/role_manage/1.tpd'));
			}
			else {
				redirect(base_url('admin/role/role_manage/2/' . $role_id . '.tpd'));
			}
		}
	}
	
	public function role_delete($role_id = 0){
		if($role_id > 0){
			$this->mdl_general->update('ms_role', array('role_id' => $role_id), array('status' => STATUS_DELETE));
			$this->db->delete('ms_role_detail', array('role_id' => $role_id));
			
			$this->session->set_flashdata('flash_message_class', 'success');
			$this->session->set_flashdata('flash_message', 'Successful delete role.');
		}
		
		redirect(base_url('admin/role/role_manage/1.tpd'));
	}
	
	public function role_list(){
		$where['status <>'] = STATUS_DELETE;
		$like = array();
		if(isset($_REQUEST['filter_role_name'])){
			if($_REQUEST['filter_role_name'] != ''){
				$like['role_name'] = $_REQUEST['filter_role_name'];
			}
		}
		if(isset($_REQUEST['filter_role_desc'])){
			if($_REQUEST['filter_role_desc'] != ''){
				$like['role_desc'] = $_REQUEST['filter_role_desc'];
			}
		}
		
		if(isset($_REQUEST['filter_status'])){
			if($_REQUEST['filter_status'] != ''){
				$where['status'] = $_REQUEST['filter_status'];
			}
		}
		
		$iTotalRecords = $this->mdl_general->count('ms_role', $where, $like);
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);
	  
		$records = array();
		$records["data"] = array();
		
		$order = 'role_name asc';
		if(isset($_REQUEST['order'])){
			if($_REQUEST['order'][0]['column'] == 1){
				$order = 'role_name ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 2){
				$order = 'role_desc ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 3){
				$order = 'status ' . $_REQUEST['order'][0]['dir'];
			}
		}
		
		$qry = $this->mdl_general->get('ms_role', $where, $like, $order, $iDisplayLength, $iDisplayStart);
		
		$i = $iDisplayStart + 1;
		foreach($qry->result() as $row){
			$records["data"][] = array(
				$i . '.',
				$row->role_name,
				$row->role_desc,
				get_status_active($row->status),
				'<div class="btn-group btn-group-solid">
					<a class="btn btn-xs green-meadow tooltips" data-original-title="View" data-placement="top" data-container="body" href="' . base_url('admin/role/role_manage/0/' . $row->role_id) . '.tpd"> <i class="fa fa-search"></i> </a>
					<a class="btn btn-xs blue-chambray tooltips" data-original-title="User" data-placement="top" data-container="body" href="' . base_url('admin/role/role_manage/2/' . $row->role_id) . '.tpd"> <i class="fa fa-user"></i> </a>
					<a class="btn btn-xs red-thunderbird btn-bootbox tooltips" data-original-title="Delete" data-placement="top" data-container="body" href="javascript:;" data-id="' . $row->role_id . '"> <i class="fa fa-times"></i> </a>
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

/* End of file role.php */
/* Location: ./application/controllers/admin/role.php */