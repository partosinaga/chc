<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class home extends CI_Controller {

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
		$this->dashboard();
	}
	
	public function dashboard(){
		$data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

		//array_push($data_header['script'], base_url() . 'assets/global/plugins/flot/jquery.flot.min.js');
		//array_push($data_header['script'], base_url() . 'assets/global/plugins/flot/jquery.flot.resize.min.js');
		//array_push($data_header['script'], base_url() . 'assets/global/plugins/flot/jquery.flot.categories.min.js');
		//array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery.pulsate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/amcharts/amcharts/amcharts.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/amcharts/amcharts/serial.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/amcharts/amcharts/themes/light.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');

		//array_push($data_header['custom_script'], base_url() . 'assets/admin/pages/scripts/index.js');
        //array_push($data_header['custom_script'], base_url() . 'assets/custom/dashboard_chart.js');

		//array_push($data_header['init_app'], 'Index.init();');
		//array_push($data_header['init_app'], 'DashboardChart.initCharts();');
        //array_push($data_header['init_app'], 'DashboardChart.fetchChart();');

		$data = array();

        $dept = $this->db->get_where('ms_department', array('department_id' => my_sess('department_id')));
        $data['dept'] = $dept->row();

        //$data['query'] = $this->db-last_query();

        /* Recent Activities */
        $data['activities'] = array();
        $activities = array();
        $count = 0;

        $data = $this->task_frontdesk($data);
        $data = $this->task_inventory($data);

		$this->load->view('layout/header', $data_header);
		$this->load->view('dashboard', $data);
		$this->load->view('layout/footer');
		
	}

    private function task_frontdesk($data = array()){
        if(isset($data)) {
            if (check_controller_action('frontdesk', 'reservation', STATUS_NEW)) {
                //Reservation  >= Arrival Date
                $qry = "SELECT reservation_id FROM cs_reservation_header
                    WHERE status IN(" . ORDER_STATUS::ONLINE_VALID . "," . ORDER_STATUS::RESERVED . ") " .
                    " AND CONVERT(date,arrival_date) <= CONVERT(date,GETDATE()) ";

                $count = $this->db->query($qry)->num_rows();
                if ($count > 0) {
                    unset($activities);
                    $activities['icon'] = 'fa fa-bell-o';
                    $activities['redirect_href'] = base_url('frontdesk/reservation/reservation_manage/1');
                    $activities['caption'] = 'You have ' . $count . ' Ready to Check In.';
                    $activities['class_type'] = 'yellow-casablanca pulsate_blink_yellow';

                    array_push($data['activities'], $activities);
                }
            }

            if (check_controller_action('housekeeping', 'housekeeping', STATUS_APPROVE) || check_controller_action('housekeeping', 'housekeeping', STATUS_POSTED) || check_controller_action('housekeeping', 'housekeeping', STATUS_CLOSED)) {
                $qry = "SELECT ISNULL(count(unit_id),0) as count_unit FROM ms_unit
                    WHERE hsk_status IN('" . HSK_STATUS::VD . "','" . HSK_STATUS::OD . "') " .
                    "  ";

                $count_unit = $this->db->query($qry);
                if ($count_unit->num_rows() > 0) {
                    $dirty_unit = $count_unit->row();
                    $count = $dirty_unit->count_unit;

                    if($count > 0) {
                        unset($activities);
                        $activities['icon'] = 'fa fa-bell-o';
                        $activities['redirect_href'] = base_url('housekeeping/housekeeping/home.tpd');
                        $activities['caption'] = $count . ' room(s) need cleaning.';
                        $activities['class_type'] = 'yellow-casablanca pulsate_blink_yellow';

                        array_push($data['activities'], $activities);
                    }
                }
            }

            if (check_controller_action('frontdesk', 'management', STATUS_NEW)) {
                //Reservation  >= Arrival Date
                $qry = "SELECT reservation_id FROM cs_reservation_header
                    WHERE status IN(" . ORDER_STATUS::CHECKIN . ") " .
                    " AND CONVERT(date,departure_date) <= CONVERT(date,GETDATE()) ";

                $count = $this->db->query($qry)->num_rows();
                if ($count > 0) {
                    unset($activities);
                    $activities['icon'] = 'fa fa-bell-o';
                    $activities['redirect_href'] = base_url('frontdesk/management/guest_manage/1');
                    $activities['caption'] = 'You have ' . $count . ' Ready to Check Out.';
                    $activities['class_type'] = 'red-sunglo pulsate_blink_yellow';
                    //$activities['span_class'] = 'pulsate-blink';

                    array_push($data['activities'], $activities);
                }
            }

        }
        return $data;
    }

    private function task_inventory($data = array()){
        if(isset($data)) {
            $my_dept_id = my_sess('department_id');

            if (check_controller_action('purchasing', 'pr', STATUS_APPROVE)) {
                //PR New
                $qry = "SELECT pr_id FROM in_pr
                    JOIN ms_user ON ms_user.user_id = in_pr.user_created
                    WHERE (ms_user.department_id = " . $my_dept_id .
                    " OR in_pr.department_id = " . $my_dept_id . ") " .
                    " AND in_pr.status = " . STATUS_NEW;

                //$count = $this->mdl_general->count('in_pr', array('status' => STATUS_NEW, 'department_id' => my_sess('department_id')));
                $count = $this->db->query($qry)->num_rows();
                if ($count > 0) {
                    unset($activities);
                    $activities['icon'] = 'fa fa-bell-o';
                    $activities['redirect_href'] = base_url('purchasing/pr/pr_manage/1');
                    $activities['caption'] = 'You have ' . $count . ' New PR.';
                    $activities['class_type'] = 'grey-salsa';

                    array_push($data['activities'], $activities);
                }

                //PR Disapproved
                $qry = "SELECT pr_id FROM in_pr
                    JOIN ms_user ON ms_user.user_id = in_pr.user_created
                    WHERE (ms_user.department_id = " . $my_dept_id .
                    " OR in_pr.department_id = " . $my_dept_id . ") " .
                    " AND in_pr.status = " . STATUS_DISAPPROVE;

                $count = $this->db->query($qry)->num_rows();
                if ($count > 0) {
                    unset($activities);
                    $activities['icon'] = 'fa fa-bell-o';
                    $activities['redirect_href'] = base_url('purchasing/pr/pr_manage/1');;
                    $activities['caption'] = 'You have ' . $count . ' Disapproved PR.';
                    $activities['class_type'] = 'grey-salsa';

                    array_push($data['activities'], $activities);
                }
            }

            if (check_controller_action('purchasing', 'po', STATUS_APPROVE)) {
                //PO New
                $qry = "SELECT po_id FROM in_po
                    JOIN ms_user ON ms_user.user_id = in_po.user_created
                    WHERE (ms_user.department_id = " . $my_dept_id .
                    " OR in_po.department_id = " . $my_dept_id . ") " .
                    " AND in_po.status = " . STATUS_NEW;

                $count = $this->db->query($qry)->num_rows();
                if ($count > 0) {
                    unset($activities);
                    $activities['icon'] = 'fa fa-bell-o';
                    $activities['redirect_href'] = base_url('purchasing/po/po_manage/1');
                    $activities['caption'] = 'You have ' . $count . ' New PO.';
                    $activities['class_type'] = 'grey-salsa';

                    array_push($data['activities'], $activities);
                }

                //PO Disapproved
                $qry = "SELECT po_id FROM in_po
                    JOIN ms_user ON ms_user.user_id = in_po.user_created
                    WHERE (ms_user.department_id = " . $my_dept_id .
                    " OR in_po.department_id = " . $my_dept_id . ") " .
                    " AND in_po.status = " . STATUS_DISAPPROVE;

                $count = $this->db->query($qry)->num_rows();
                if ($count > 0) {
                    unset($activities);
                    $activities['icon'] = 'fa fa-bell-o';
                    $activities['redirect_href'] = base_url('purchasing/po/po_manage/1');;
                    $activities['caption'] = 'You have ' . $count . ' Disapproved PO.';
                    $activities['class_type'] = 'grey-salsa';

                    array_push($data['activities'], $activities);
                }
            }

            if (check_controller_action('inventory', 'grn', STATUS_APPROVE)) {
                //GRN New
                $qry = "SELECT grn_id FROM in_grn
                    JOIN ms_user ON ms_user.user_id = in_grn.user_created
                    WHERE (ms_user.department_id = " . $my_dept_id .
                    " OR in_grn.department_id = " . $my_dept_id . ") " .
                    " AND in_grn.status = " . STATUS_NEW;

                $count = $this->db->query($qry)->num_rows();
                if ($count > 0) {
                    unset($activities);
                    $activities['icon'] = 'fa fa-bell-o';
                    $activities['redirect_href'] = base_url('inventory/grn/grn_manage/1');
                    $activities['caption'] = 'You have ' . $count . ' New GRN.';
                    $activities['class_type'] = 'grey-salsa';

                    array_push($data['activities'], $activities);
                }

                //GRN Disapproved
                $qry = "SELECT grn_id FROM in_grn
                    JOIN ms_user ON ms_user.user_id = in_grn.user_created
                    WHERE (ms_user.department_id = " . $my_dept_id .
                    " OR in_grn.department_id = " . $my_dept_id . ") " .
                    " AND in_grn.status = " . STATUS_DISAPPROVE;

                $count = $this->db->query($qry)->num_rows();
                if ($count > 0) {
                    unset($activities);
                    $activities['icon'] = 'fa fa-bell-o';
                    $activities['redirect_href'] = base_url('inventory/grn/grn_manage/1');;
                    $activities['caption'] = 'You have ' . $count . ' Disapproved GRN.';
                    $activities['class_type'] = 'grey-salsa';

                    array_push($data['activities'], $activities);
                }
            }
        }
        return $data;
    }

    public function profile(){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/icheck/skins/all.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/icheck/icheck.min.js');

        $data = array();

        $data['user_id'] = my_sess('user_id');
        if($data['user_id'] > 0){
            $qry = $this->db->get_where('ms_user', array('user_id' => $data['user_id']));
            $data['row'] = $qry->row();
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('profile', $data);
        $this->load->view('layout/footer');
	}

    public function user_submit(){
        if(isset($_POST)){
            $user_id = $_POST['user_id'];

            $data['user_fullname'] = trim($_POST['user_fullname']);
            $data['user_email'] = trim($_POST['user_email']);

            if($user_id > 0){
                if(trim($_POST['user_password']) != ''){
                    $data['user_password'] = md5(trim($_POST['user_password']));
                }

                $this->mdl_general->update('ms_user', array('user_id' => $user_id), $data);

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Success. To apply this changes please re-login.');
            }

            redirect(base_url('home/home/profile.tpd'));
        }
    }
	
}

/* End of file home.php */
/* Location: ./application/controllers/home/home.php */