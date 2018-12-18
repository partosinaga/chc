<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class report extends CI_Controller {

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
		tpd_404();
	}

    public function room_assignment($type = 0){
        //Room Attendant Assignment
        $this->pdf_room_assignment();
    }

    public function find_room_status($type = 0){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/css/report_group.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        //Room by status
        $this->load->view('layout/header', $data_header);
        $this->load->view('housekeeping/report/find_by_status', $data);
        $this->load->view('layout/footer');
    }

    #region Print

    public function pdf_room_assignment() {
        $this->load->model('finance/mdl_finance');

        $data['report_date'] = date('d/m/Y');
        /*
        $where['status'] = ORDER_STATUS::RESERVED;
        $where['arrival_date >='] = dmy_to_ymd($dmy_from);
        $where['arrival_date <='] = dmy_to_ymd($dmy_to);
        */

        //Company Profile
        $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
        if($profile->num_rows() > 0){
            $data['profile'] = $profile->row_array();
        }

        $qry = $this->mdl_finance->getJoin('hsk.*',"view_hsk_assignment as hsk ", array(), array(), array(), ' floor_id ASC, unit_code ASC');
        if($qry->num_rows() > 0){
            $data['detail'] = $qry->result_array();
        }

        $this->load->view('housekeeping/report/pdf_room_assignment', $data);

        $html = $this->output->get_output();

        $this->load->library('dompdf_gen');

        $this->dompdf->set_paper("A4", "portrait");
        $this->dompdf->load_html($html);
        $this->dompdf->render();

        $this->dompdf->stream(date('Y_m_d_H_i_s') . ".pdf", array('Attachment' => 0));

    }

    public function pdf_room_status($hsk_index) {
        if($hsk_index != ''){
            $this->load->model('finance/mdl_finance');

            $data['report_date'] = date('d-m-Y H:i');

            //Company Profile
            $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
            if($profile->num_rows() > 0){
                $data['profile'] = $profile->row_array();
            }

            $hsk_idx = explode('_',$hsk_index);

            $hsk_stat = array();
            foreach($hsk_idx as $idx){
                if(trim($idx) != ''){
                    array_push($hsk_stat, "'" . HSK_STATUS::idx_to_stat($idx) . "'");
                }
            }

            $hsk_stat = implode(',',$hsk_stat);

            $where_str = ' hsk.hsk_status IN(' . $hsk_stat . ')';

            $qry = $this->mdl_finance->getJoin('hsk.*',"view_hsk_assignment as hsk ", array(), array(), array(), ' floor_id ASC, unit_code ASC', 0, 0 , false, '', array(), $where_str);

            if($qry->num_rows() > 0){
                $data['detail'] = $qry->result_array();
            }

            $this->load->view('housekeeping/report/pdf_room_status', $data);

            $html = $this->output->get_output();

            $this->load->library('dompdf_gen');

            $this->dompdf->set_paper("A4", "portrait");
            $this->dompdf->load_html($html);
            $this->dompdf->render();

            $this->dompdf->stream(date('Y_m_d_H_i_s') . ".pdf", array('Attachment' => 0));

        }
        else {
            tpd_404();
        }
    }

    #endregion

    public function daily_schedule($start_date = '', $end_date = ''){
        $data_header = $this->data_header;

        $data = array();

        if ($start_date == '') {
            $start_date = date('d-m-Y');
        }
        if ($end_date == '') {
            $end_date = date('d-m-Y', strtotime(date('Y/m/d') . '+7 days'));
        }

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        //Room by status
        $this->load->view('layout/header', $data_header);
        $this->load->view('housekeeping/report/daily_schedule', $data);
        $this->load->view('layout/footer');
    }
	
	public function find_srf($type = 0){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/css/report_group.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        //Room by status
        $this->load->view('layout/header', $data_header);
        $this->load->view('housekeeping/report/find_srf', $data);
        $this->load->view('layout/footer');
    }
	
    public function pdf_srf() {
        $dateFrom = '';
        $dateTo = ''; 
        $ispdf = true;
		$isMultiPages = 1;

        $dateFrom = isset($_POST['date_start']) ? $_POST['date_start'] : date('d-m-Y');
		$dateTo = isset($_POST['date_to']) ? $_POST['date_to'] : date('d-m-Y');
		//$ispdf = isset($_POST['is_pdf']) ? $_POST['is_pdf']: false;
		$unit_id = isset($_POST['unit_id']) ? $_POST['unit_id'] :'';
		$where = "";
		if ($unit_id !='')
		{
			$where = "where unit_id = ".$unit_id ;
		} 
        $data['report_date'] = date('d/m/Y');
		//Company Profile
        $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
        if($profile->num_rows() > 0){
            $data['profile'] = $profile->row_array();
        }

        if(trim($dateFrom) != '' && trim($dateTo) != '' ){
            $this->session->set_userdata('pdf_srf', array('date_start' => $dateFrom, 'date_to' => $dateTo, 'is_pdf' => $ispdf));

            $data['header'] = array('company_name'=>'TPD',
                  'date_from' => $dateFrom,
                  'date_to' => $dateTo);

            $data['date_start'] = $dateFrom;
            $data['date_to'] = $dateTo;
            $data['is_pdf'] = $ispdf;

            $dateFrom = dmy_to_ymd(trim($dateFrom));
            $dateTo = dmy_to_ymd(trim($dateTo));

            $this->load->model('finance/mdl_finance');
			$where_str = ' ';
			
			
            $qry =  $this->db->query("SELECT * FROM fxnsrf('" . $dateFrom ."','" .$dateTo. "')" . $where);

            if($qry->num_rows() > 0){
                $data['detail'] = $qry->result_array();
            }
			
			
            if($ispdf){ 
                $this->load->view('housekeeping/report/pdf_srf.php', $data);
            }else{
                $this->load->view('housekeeping/report/pdf_srf.php', $data);
            }

            if($data['is_pdf']){
                /*$html = $this->output->get_output();

                $this->load->library('wkhtml');

                header('Content-Type: application/pdf');
                //header('Content-Disposition: attachment; filename="file.pdf"');
                header('Content-Disposition: inline; filename="file.pdf"');
                echo $this->snappy->getOutputFromHtml($html, array('orientation' => 'portrait',
                                                                   'page-size' => 'A4'));
*/
				$data['multi_pages'] = $isMultiPages > 0 ? true : false; 

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream("pdf_srf_". date('Y_m_d_h_i_s') . ".pdf", array('Attachment'=>0));
            }

        }else{
            //tpd_404();
            $this->find_srf();
        }
    }


}

/* End of file booking.php */
/* Location: ./application/controllers/frondesk/booking.php */