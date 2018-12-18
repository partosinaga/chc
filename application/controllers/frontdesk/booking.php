<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class booking extends CI_Controller {

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
		$this->booking_form();
	}
	
	public function booking_form(){
		$data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/qtip/jquery.qtip.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-imagemapster/jquery.imagemapster.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/qtip/jquery.qtip.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/qtip/imagesloaded.pkg.min.js');
		
		$data = array();

		$this->load->view('layout/header', $data_header);
		$this->load->view('frontdesk/booking/booking_form', $data);
		$this->load->view('layout/footer');
		
	}
	
	public function booking_list(){
		$data_header = $this->data_header;
		
		array_push($data_header['script'], base_url() . 'assets/global/plugins/flot/jquery.flot.min.js');
		array_push($data_header['script'], base_url() . 'assets/global/plugins/flot/jquery.flot.resize.min.js');
		array_push($data_header['script'], base_url() . 'assets/global/plugins/flot/jquery.flot.categories.min.js');
		array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery.pulsate.min.js');
		
		array_push($data_header['custom_script'], base_url() . 'assets/admin/pages/scripts/index.js');
		
		array_push($data_header['init_app'], 'Index.init();');
		array_push($data_header['init_app'], 'Index.initCharts();');
		
		$data = array();

		$this->load->view('layout/header', $data_header);
		$this->load->view('frontdesk/booking/booking_list', $data);
		$this->load->view('layout/footer');
		
	}
	
	
	
}

/* End of file booking.php */
/* Location: ./application/controllers/frondesk/booking.php */