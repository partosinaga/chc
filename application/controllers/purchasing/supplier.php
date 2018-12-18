<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Supplier extends CI_Controller {

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
		$this->supplier_manage();
	}
	
	#region supplier
	
	public function supplier_manage($type = 1, $id = 0){
		$data_header = $this->data_header;
		$data = array();
		
		if($type == 1){ //supplier  List
			array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
			array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
			
			array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
			
			array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
			

			$this->load->view('layout/header', $data_header);
			$this->load->view('purchasing/supplier/supplier_list', $data);
			$this->load->view('layout/footer');
		}
		else{ // suplier Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');

			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
			array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
			 array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
			
			$data['supplier_id'] = $id;
			if($id > 0){
				$qry = $this->db->get_where('in_supplier', array('supplier_id' => $id));
				$data['row'] = $qry->row();
			}
			
			$country = $this->mdl_general->get('master_country',  array() , array(), 'country_name');
			$data['country_list'] = $country->result_array();
			//echo $this->db->last_query() ; 

			$this->load->view('layout/header', $data_header);
			$this->load->view('purchasing/supplier/supplier_form', $data);
			$this->load->view('layout/footer');
		}
	}
	
	public function supplier_submit(){
		if(isset($_POST)){
			$has_error = false;
			$supplier_id = $_POST['supplier_id'];
			
			$data['supplier_name'] = trim($_POST['supplier_name']);
			$data['supplier_address'] = trim($_POST['supplier_address']);
			$data['supplier_postcode'] = trim($_POST['supplier_postcode']);
			$data['supplier_distric'] = trim($_POST['supplier_distric']);
			$data['supplier_city'] = trim($_POST['supplier_city']);
			$data['supplier_country'] = trim($_POST['supplier_country']);
			$data['supplier_telephone'] = trim($_POST['supplier_telephone']);
			$data['supplier_fax'] = trim($_POST['supplier_fax']);
			$data['supplier_term_payment'] = trim($_POST['supplier_term_payment']);
			$data['bank_name'] = trim($_POST['bank_name']);
			$data['account_bank_name'] = trim($_POST['account_bank_name']);
			$data['account_bank_no'] = trim($_POST['account_bank_no']);
            $data['contact_name'] = trim($_POST['contact_name']);
            $data['contact_phone'] = trim($_POST['contact_phone']);
			$data['provide_item'] = trim($_POST['provide_item']);
			
			if($supplier_id > 0){
				$data['user_modified'] = my_sess('user_id');
				$data['date_modified'] = date('Y-m-d H:i:s');
				$data['status'] = $_POST['status'];
				
				$exist = $this->mdl_general->count('in_supplier', array('supplier_name' => $data['supplier_name'], 'supplier_id <>' => $supplier_id));
				
				if($exist == 0){
					$this->mdl_general->update('in_supplier', array('supplier_id' => $supplier_id), $data);
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful update supplier.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'supplier code already exist.');
				}
			}
			else {
				$data['user_created'] = my_sess('user_id');
				$data['date_created'] = date('Y-m-d H:i:s');
				$data['status'] = STATUS_NEW;
				
				$exist = $this->mdl_general->count('in_supplier', array('supplier_id' => $data['supplier_id']));
				
				if($exist == 0){
					$this->db->insert('in_supplier', $data);
					$supplier_id = $this->db->insert_id();
					
					$this->session->set_flashdata('flash_message_class', 'success');
					$this->session->set_flashdata('flash_message', 'Successful add supplier.');
				}
				else {
					$has_error = true;
					
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Supplier code already exist.');
				}
			}
			
			if($has_error){
				redirect(base_url('purchasing/supplier/supplier_manage/0/' . $supplier_id . '.tpd'));
			}
			else {
				if(isset($_POST['save_close'])){
					redirect(base_url('purchasing/supplier/supplier_manage/1.tpd'));
				}
				else {
					redirect(base_url('purchasing/supplier/supplier_manage/0/' . $supplier_id . '.tpd'));
				}
			}
		}
	}
	
	public function supplier_delete($supplier_id = 0){
		if($supplier_id > 0){
			$this->mdl_general->update('in_supplier', array('supplier_id' => $supplier_id), array('status' => STATUS_DELETE));
			
			$this->session->set_flashdata('flash_message_class', 'success');
			$this->session->set_flashdata('flash_message', 'Successful delete supplier.');
		}
		
		redirect(base_url('purchasing/supplier/supplier_manage/1.tpd'));
	}
	
	public function supplier_list($menu_id = 0){
		$where['status <>'] = STATUS_DELETE; 
		$like = array();
		if(isset($_REQUEST['filter_supplier_name'])){
			if($_REQUEST['filter_supplier_name'] != ''){
				$like['supplier_name'] = $_REQUEST['filter_supplier_name'];
			}
		}
		if(isset($_REQUEST['filter_supplier_address'])){
			if($_REQUEST['filter_supplier_address'] != ''){
				$like['supplier_address'] = $_REQUEST['filter_supplier_address'];
			}
		}
		if(isset($_REQUEST['filter_supplier_telephone'])){
			if($_REQUEST['filter_supplier_telephone'] != ''){
				$like['supplier_telephone'] = $_REQUEST['filter_supplier_telephone'];
			}
		}
		if(isset($_REQUEST['filter_supplier_fax'])){
			if($_REQUEST['filter_supplier_fax'] != ''){
				$like['supplier_fax'] = $_REQUEST['filter_supplier_fax'];
			}
		}
        if(isset($_REQUEST['filter_supplier_contact'])){
            if($_REQUEST['filter_supplier_contact'] != ''){
                $like['contact_name'] = $_REQUEST['filter_supplier_contact'];
            }
        }
		if(isset($_REQUEST['filter_status'])){
			if($_REQUEST['filter_status'] != ''){
				$where['status'] = $_REQUEST['filter_status'];
			}
		}
		
		$iTotalRecords = $this->mdl_general->count('in_supplier', $where, $like);
		
		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);
	  
		$records = array();
		$records["data"] = array();
		
		$order = 'Supplier_Name asc';
		if(isset($_REQUEST['order'])){
			if($_REQUEST['order'][0]['column'] == 1){
				$order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 2){
				$order = 'supplier_address ' . $_REQUEST['order'][0]['dir'];
			}
			if($_REQUEST['order'][0]['column'] == 3){
				$order = 'supplier_telephone ' . $_REQUEST['order'][0]['dir'];
			}
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'supplier_fax ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'contact_name ' . $_REQUEST['order'][0]['dir'];
            }
		}
		
		$qry = $this->mdl_general->get('in_supplier', $where, $like, $order, $iDisplayLength, $iDisplayStart);
		
		$i = $iDisplayStart + 1;
		foreach($qry->result() as $row){
			$records["data"][] = array(
				$i . '.',
				$row->supplier_name,
				$row->supplier_address,
				$row->supplier_telephone,
				$row->supplier_fax,
                $row->contact_name,
				get_status_active($row->status),
				'<div class="btn-group btn-group-solid">
					' . (check_session_action($menu_id, STATUS_EDIT) ? '<a class="btn btn-xs green-meadow tooltips" data-original-title="View" data-placement="top" data-container="body" href="' . base_url('purchasing/supplier/supplier_manage/0/' . $row->supplier_id) . '.tpd"> <i class="fa fa-search"></i> </a>' : '') . '
					' . (check_session_action($menu_id, STATUS_DELETE) ? '<a class="btn btn-xs red-thunderbird btn-bootbox tooltips" data-original-title="Delete" data-placement="top" data-container="body" href="javascript:;" data-id="' . $row->supplier_id . '"> <i class="fa fa-times"></i> </a>' : '') . '
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

}

/* End of file supplier.php */
/* Location: ./application/controllers/purchsing/supplier.php */