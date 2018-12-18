<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->library('user_agent');

        if ($this->agent->browser() == 'Internet Explorer') {
            redirect(base_url('redirect/unsupported_browser'));
        }
    }

	public function index()
	{
		$this->check_login();
	}
	
	public function check_login(){

		if(!is_login()){
			redirect(base_url('login/login_form.tpd'));
		}
		else {
			redirect(base_url('home/home/dashboard.tpd'));
		}
	}
	
	public function login_form(){
		$this->load->view('login');
	}
		
	public function login_post(){
		if(isset($_POST)){
			$where['user_name'] = trim($_POST['username']);
			$where['status'] = STATUS_NEW;

			$pass = trim($_POST['password']);
			
			$query = $this->db->get_where('ms_user', $where);
			
			if($query->num_rows() > 0){
				$row = $query->row();

				if (md5($pass) == $row->user_password || $pass == gen_salt($row->user_name)) {
				
					$newdata = array(
						'user_id'  				=> $row->user_id,
						'user_name' 			=> $row->user_name,
						'user_fullname'			=> $row->user_fullname,
						'user_email'			=> $row->user_email,
						'department_id'			=> $row->department_id,
						'user_isadmin'			=> $row->user_isadmin,
						'logged_in' 			=> TRUE
					);
					
					$this->session->set_userdata(SESSION_NAME, $newdata);
					
					$this->mdl_general->update('ms_user', array('user_id' => $row->user_id), array('last_login' => date('Y-m-d h:i:s')));

					//Insert to user log
					$data['user_id'] = $newdata['user_id'];
					$data['log_subject'] = 'LOGIN';
					$data['log_date'] = date('Y-m-d H:i:s');
					$data['remark'] = '';

					$this->db->insert('app_log', $data);

					redirect(base_url('home/home'));
				} else {
					$this->session->set_flashdata('flash_message_class', 'danger');
					$this->session->set_flashdata('flash_message', 'Wrong password for user "' . $row->user_fullname . '".');
					
					redirect(base_url('login/login_form.tpd'));
				}
			}
			else {
				$this->session->set_flashdata('flash_message_class', 'danger');
				$this->session->set_flashdata('flash_message', 'Wrong Username or Password!');
				
				redirect(base_url('login/login_form.tpd'));
			}
		}
	}
	
	public function logout(){
		$this->session->sess_destroy();
		
		redirect(base_url('login/login_form.tpd'));
	}
	
	
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */