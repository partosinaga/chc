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

    public function reporter($type = 0){
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

        switch ($type)
        {
            case 1:
                //Expected Arrival
                $this->load->view('layout/header_no_sidebar', $data_header);
                $this->load->view('frontdesk/report/expected_arrival', $data);
                $this->load->view('layout/footer');
                break;
            case 2:
                //Expected Departure
                $this->load->view('layout/header_no_sidebar', $data_header);
                $this->load->view('frontdesk/report/expected_departure', $data);
                $this->load->view('layout/footer');
                break;
            case 3:
                //Daily Cash
                $this->load->view('layout/header_no_sidebar', $data_header);
                $this->load->view('frontdesk/report/daily_cash', $data);
                $this->load->view('layout/footer');
                break;
            case 4:
                //Guest In House
                $this->load->view('layout/header_no_sidebar', $data_header);
                $this->load->view('frontdesk/report/guest_inhouse', $data);
                $this->load->view('layout/footer');
                break;
            case 5:
                //Reservation Report
                $this->load->view('layout/header_no_sidebar', $data_header);
                $this->load->view('frontdesk/report/reservation_report', $data);
                $this->load->view('layout/footer');
                break;
            case 6:
                //Reservation Agent Report
                $this->load->view('layout/header_no_sidebar', $data_header);
                $this->load->view('frontdesk/report/reservation_agent_report', $data);
                $this->load->view('layout/footer');
                break;
            case 7:
                //Sales Summary
                array_push($data_header['script'], base_url() . 'assets/global/plugins/fuelux/js/spinner.min.js');

                $this->load->view('layout/header_no_sidebar', $data_header);
                $this->load->view('frontdesk/report/sales_sum_by_year', $data);
                $this->load->view('layout/footer');
                break;
            default:
                tpd_404();
                break;
        }
    }

    public function find_room_stat(){
        /*
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

        //Room Stat
        $this->load->view('layout/header', $data_header);
        $this->load->view('frontdesk/report/find_room_stat', $data);
        $this->load->view('layout/footer');
        */

        redirect(base_url('frontdesk/report/pdf_room_stat/'. date('d-m-Y') .'.tpd'));
    }

    #region Print

    public function pdf_exp_arrival($dmy_from = '', $dmy_to = '') {
        if($dmy_from != '' && $dmy_to != ''){
            $this->load->model('finance/mdl_finance');

            $data['date_from'] = $dmy_from;
            $data['date_until'] = $dmy_to;

            $where['status'] = ORDER_STATUS::RESERVED;
            $where['arrival_date >='] = dmy_to_ymd($dmy_from);
            $where['arrival_date <='] = dmy_to_ymd($dmy_to);

            //Company Profile
            $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
            if($profile->num_rows() > 0){
                $data['profile'] = $profile->row_array();
            }

            $qry = $this->mdl_finance->getJoin('view_cs_reservation.*',"view_cs_reservation ", array(), $where, array(), 'arrival_date ASC');
            if($qry->num_rows() > 0){
                $data['detail'] = $qry->result_array();
            }

            $this->load->view('frontdesk/report/pdf_expected_arrival', $data);

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

    public function pdf_exp_departure($dmy_from = '', $dmy_to = '') {
        if($dmy_from != '' && $dmy_to != ''){
            $this->load->model('finance/mdl_finance');

            $data['date_from'] = $dmy_from;
            $data['date_until'] = $dmy_to;

            //Company Profile
            $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
            if($profile->num_rows() > 0){
                $data['profile'] = $profile->row_array();
            }

            $qry = $this->mdl_finance->getJoin('*',"fxnCSExpectedDeparture('" . dmy_to_ymd($dmy_from) . "','" . dmy_to_ymd($dmy_to) . "')", array(), array(), array(), 'departure_date ASC');
            if($qry->num_rows() > 0){
                $data['detail'] = $qry->result_array();
            }

            $this->load->view('frontdesk/report/pdf_expected_departure', $data);

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

    public function pdf_daily_cash($dmy_from = '', $dmy_to = '') {
        if($dmy_from != '' && $dmy_to != ''){
            $this->load->model('finance/mdl_finance');

            $data['date_from'] = $dmy_from;
            $data['date_until'] = $dmy_to;

            //Company Profile
            $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
            if($profile->num_rows() > 0){
                $data['profile'] = $profile->row_array();
            }

            $where['pt.payment_type'] = PAYMENT_TYPE::CASH_ONLY;
            $where_string = "rv.status IN(" . STATUS_POSTED . "," . STATUS_CLOSED .")";

            $where['CONVERT(date, rv.bookingreceipt_date) >='] = dmy_to_ymd($dmy_from);
            $where['CONVERT(date, rv.bookingreceipt_date) <='] = dmy_to_ymd($dmy_to);

            $joins = array('view_cs_reservation cs' => 'rv.reservation_id = cs.reservation_id',
                        'ms_payment_type pt' => 'rv.paymenttype_id = pt.paymenttype_id');
            $qry = $this->mdl_finance->getJoin('rv.*, cs.reservation_type, cs.reservation_code, cs.tenant_fullname, cs.company_name, cs.room',"cs_booking_receipt as rv ", $joins, $where, array(), 'rv.bookingreceipt_date ASC', 0, 0, false, '', array(), $where_string);
            if($qry->num_rows() > 0){
                $data['detail'] = $qry->result_array();
            }

            $this->load->view('frontdesk/report/pdf_daily_cash', $data);

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

    public function pdf_guest_inhouse($dmy_from = '') {
        if($dmy_from != ''){
            $this->load->model('finance/mdl_finance');

            $data['date_from'] = $dmy_from;

            //Company Profile
            $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
            if($profile->num_rows() > 0){
                $data['profile'] = $profile->row_array();
            }

            $qry = $this->mdl_finance->getJoin('*',"fxnCSInhouseByDate('" . dmy_to_ymd($dmy_from) . "')", array(), array(), array(), 'reservation_code ASC');
            if($qry->num_rows() > 0){
                $data['detail'] = $qry->result_array();
            }

            $this->load->view('frontdesk/report/pdf_guest_inhouse', $data);

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

    public function pdf_reservation($dmy_from = '') {
        if($dmy_from != ''){
            $this->load->model('finance/mdl_finance');

            $data['date_from'] = $dmy_from;

            //Company Profile
            $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
            if($profile->num_rows() > 0){
                $data['profile'] = $profile->row_array();
            }
			
			$currentDate = date('Y-m-d');

			$where = array();
			$whereIn = array(ORDER_STATUS::ONLINE_VALID, ORDER_STATUS::RESERVED);

			$like = array();
			$whereString = ""; 
			$joins = array();
			

			$order = 'view_cs_reservation.guest_type ASC,view_cs_reservation.arrival_date ASC , view_cs_reservation.reservation_code DESC';
			$qry = $this->mdl_finance->getJoin('view_cs_reservation.*','view_cs_reservation', $joins, $where, $like, $order, 0, 0, false, 'view_cs_reservation.status', $whereIn);
				
            
            if($qry->num_rows() > 0){
                $data['detail'] = $qry->result_array();
            }

            $this->load->view('frontdesk/report/pdf_reservation', $data);

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

	public function pdf_reservation_agent($dmy_from = '') {
        if($dmy_from != ''){
            $this->load->model('finance/mdl_finance');

            $data['date_from'] = $dmy_from;

            //Company Profile
            $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
            if($profile->num_rows() > 0){
                $data['profile'] = $profile->row_array();
            }
			
			$currentDate = date('Y-m-d');

			$where = array();
			$whereIn = array(ORDER_STATUS::ONLINE_VALID, ORDER_STATUS::RESERVED,ORDER_STATUS::CHECKIN );

			$like = array();
			$where = "reservation_type != 4"; 
			$joins = array();
			

			$order = 'view_cs_reservation.guest_type ASC,view_cs_reservation.arrival_date ASC , view_cs_reservation.reservation_code DESC';
			$qry = $this->mdl_finance->getJoin('view_cs_reservation.*','view_cs_reservation', $joins, $where, $like, $order, 0, 0, false, 'view_cs_reservation.status', $whereIn);
				
            
            if($qry->num_rows() > 0){
                $data['detail'] = $qry->result_array();
				
				//$data['detail']['bln'] = num_of_months(ymd_from_db($data['detail']->arrival_date),ymd_from_db($data['detail']->departure_date));
            }

            $this->load->view('frontdesk/report/pdf_reservation_agent', $data);

            $html = $this->output->get_output();

            $this->load->library('dompdf_gen');

            $this->dompdf->set_paper("A4", "landscape");
            $this->dompdf->load_html($html);
            $this->dompdf->render();


            $this->dompdf->stream(date('Y_m_d_H_i_s') . ".pdf", array('Attachment' => 0));
			

        }
        else {
            tpd_404();
        }
    }

    public function pdf_room_stat($dmy = '') {
        if($dmy != ''){
            $this->load->model('finance/mdl_finance');

            $data['todate'] = $dmy;
            $data['today_header'] = "Today";
            if(date('d-m-Y') != $dmy){
                $data['today_header'] = date("d M 'y", strtotime(dmy_to_ymd($dmy)));
            }

            //Company Profile
            $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
            if($profile->num_rows() > 0){
                $data['profile'] = $profile->row_array();
            }

            $today = dmy_to_ymd($dmy);
            $mtd_date = new DateTime($today);
            $mtd_date = $mtd_date->modify('first day of this month')->format('Y-m-d');

            $ytd_date = new DateTime($today);
            //$ytd_date = $ytd_date->format('Y') . '-01-01';
            $ytd_year = $ytd_date->format('Y');

            //DAILY
            $col_today = array();
            $qry = $this->db->query("SELECT ISNULL(SUM(total_room),0) as total_room, ISNULL(SUM(hsk_is),0) as hsk_is, ISNULL(SUM(hsk_oo),0) as hsk_oo, ISNULL(SUM(res_occupied),0) as res_occupied, ISNULL(SUM(res_house_use),0) as res_house_use, ISNULL(SUM(res_compliment),0) as res_compliment, ISNULL(SUM(folio_reserve),0) as folio_reserve, ISNULL(SUM(folio_walk_in),0) as folio_walk_in, ISNULL(SUM(folio_cancel),0) as folio_cancel, ISNULL(SUM(revenue_room),0) as revenue_room, ISNULL(SUM(revenue_other),0) as revenue_other, ISNULL(SUM(num_of_guest),0) as num_of_guest
                        FROM daily_room_stat
                        WHERE stat_date = '" . $today . "' AND status = " . STATUS_NEW);

            if($qry->num_rows() > 0){
                $row = $qry->row();
                $col_today = $this->bind_stat($row);
            }

            $data['col_today'] = $col_today;

            //MTD
            $col_mtd = array();
            $qry = $this->db->query("SELECT ISNULL(SUM(total_room),0) as total_room, ISNULL(SUM(hsk_is),0) as hsk_is, ISNULL(SUM(hsk_oo),0) as hsk_oo, ISNULL(SUM(res_occupied),0) as res_occupied, ISNULL(SUM(res_house_use),0) as res_house_use, ISNULL(SUM(res_compliment),0) as res_compliment, ISNULL(SUM(folio_reserve),0) as folio_reserve, ISNULL(SUM(folio_walk_in),0) as folio_walk_in, ISNULL(SUM(folio_cancel),0) as folio_cancel, ISNULL(SUM(revenue_room),0) as revenue_room, ISNULL(SUM(revenue_other),0) as revenue_other, ISNULL(SUM(num_of_guest),0) as num_of_guest
                        FROM daily_room_stat
                        WHERE (stat_date BETWEEN '" . $mtd_date . "' AND '" . $today . "') AND status = " . STATUS_NEW);

            if($qry->num_rows() > 0){
                $row = $qry->row();
                $col_mtd = $this->bind_stat($row);
            }

            $data['col_month'] = $col_mtd;

            //YTD
            $col_ytd = array();
            $qry = $this->db->query("SELECT ISNULL(SUM(total_room),0) as total_room, ISNULL(SUM(hsk_is),0) as hsk_is, ISNULL(SUM(hsk_oo),0) as hsk_oo, ISNULL(SUM(res_occupied),0) as res_occupied, ISNULL(SUM(res_house_use),0) as res_house_use, ISNULL(SUM(res_compliment),0) as res_compliment, ISNULL(SUM(folio_reserve),0) as folio_reserve, ISNULL(SUM(folio_walk_in),0) as folio_walk_in, ISNULL(SUM(folio_cancel),0) as folio_cancel, ISNULL(SUM(revenue_room),0) as revenue_room, ISNULL(SUM(revenue_other),0) as revenue_other, ISNULL(SUM(num_of_guest),0) as num_of_guest
                        FROM daily_room_stat
                        WHERE year(stat_date) = " . $ytd_year . " AND status = " . STATUS_NEW);

            if($qry->num_rows() > 0){
                $row = $qry->row();
                $col_ytd = $this->bind_stat($row);
            }

            $data['col_year'] = $col_ytd;

            $this->load->view('frontdesk/report/pdf_room_stat', $data);

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

    public function pdf_sales_sum_by_year($period_year = 0) {
        if($period_year > 0){
            $this->load->model('finance/mdl_finance');

            $data['period_year'] = $period_year;

            //Company Profile
            $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
            if($profile->num_rows() > 0){
                $data['profile'] = $profile->row_array();
            }

			$currentDate = date('Y-m-d');

			$like = array();
			$where['vw.period_year'] = $period_year;
			$joins = array();
			$order = 'vw.agent_name ASC';
			$qry = $this->mdl_finance->getJoin('vw.*',"vw_agent_sales_yearly as vw ", $joins, $where, $like, $order);

            if($qry->num_rows() > 0){
                $data['detail'] = $qry->result_array();
            }

            $this->load->view('frontdesk/report/pdf_sales_sum_by_year', $data);

            wkhtml_print(array('orientation' => 'landscape'));
        }
        else {
            tpd_404();
        }
    }

    private function bind_stat($row){
        $col_today = array();
        $col_today['total_room'] = 0;
        $col_today['hsk_is'] = 0;
        $col_today['hsk_oo'] = 0;
        $col_today['res_occupied'] = 0;
        $col_today['res_house_use'] = 0;
        $col_today['res_compliment'] = 0;
        $col_today['folio_reserve'] = 0;
        $col_today['folio_walk_in'] = 0;
        $col_today['folio_cancel'] = 0;
        $col_today['revenue_room'] = 0;
        $col_today['revenue_other'] = 0;
        $col_today['num_of_guest'] = 0;
        $col_today['ready_room'] = 0;
        $col_today['percent_oo'] = 0;
        $col_today['percent_not_oo'] = 0;
        $col_today['percent_hu'] = 0;
        $col_today['percent_hu_comp'] = 0;
        $col_today['avg_sales_room'] = 0;
        $col_today['avg_sales_total'] = 0;
        $col_today['total_revenue'] = 0;

        if(isset($row)){
            $col_today['total_room'] = $row->total_room;
            $col_today['hsk_is'] = $row->hsk_is;
            $col_today['hsk_oo'] = $row->hsk_oo;
            $col_today['res_occupied'] = $row->res_occupied;
            $col_today['res_house_use'] = $row->res_house_use;
            $col_today['res_compliment'] = $row->res_compliment;
            $col_today['folio_reserve'] = $row->folio_reserve;
            $col_today['folio_walk_in'] = $row->folio_walk_in;
            $col_today['folio_cancel'] = $row->folio_cancel;
            $col_today['revenue_room'] = $row->revenue_room;
            $col_today['revenue_other'] = $row->revenue_other;
            $col_today['num_of_guest'] = $row->num_of_guest;

            //calculate//
            $col_today['ready_room'] = $col_today['total_room'] - $col_today['hsk_oo'];

            $cal1 = ($col_today['hsk_is'] + $col_today['hsk_oo']);
            $cal1 = $cal1 > 0 ? $cal1 : 1;
            $col_today['percent_oo'] = round(($col_today['res_occupied'] / $cal1 * 100),2);

            $cal2 = ($col_today['hsk_is'] - $col_today['hsk_oo']);
            $cal2 = $cal2 > 0 ? $cal2 : 1;
            $col_today['percent_not_oo'] = round(($col_today['res_occupied'] / $cal2 * 100),2);

            $cal3 = ($col_today['hsk_is'] - $col_today['res_house_use']);
            $cal3 = $cal3 > 0 ? $cal3 : 1;
            $col_today['percent_hu'] = round(($col_today['res_occupied'] / $cal3 * 100),2);

            $cal4 = ($col_today['hsk_is'] - $col_today['res_house_use'] - $col_today['res_compliment']);
            $cal4 = $cal4 > 0 ? $cal4 : 1;
            $col_today['percent_hu_comp'] = round(($col_today['res_occupied'] / $cal4 * 100),2);

            $cal5 = $col_today['res_occupied'] > 0 ? $col_today['res_occupied'] : 1;
            $col_today['avg_sales_room'] = round($col_today['revenue_room'] / $cal5,0);
            $col_today['total_revenue'] = round($col_today['revenue_room'] + $col_today['revenue_other'],0);
            $col_today['avg_sales_total'] = round($col_today['total_revenue'] / $cal5,0);
        }
        return $col_today;
    }
    #endregion

    public function ajax_unit_occupancy(){
        $result = array();

        $year = isset($_POST['period_year']) ? $_POST['period_year'] : date('Y');
        $iMonth = 12;
        if($year>=date('Y'))
        {
            $iMonth = date('m');
        }
        if($year > 0){
            //Get All Unit Count
            $all_unit_count = 0;
            $unit = $this->db->query('SELECT COUNT(unit_id) as _count FROM ms_unit ');
            if($unit->num_rows() > 0){
                $all_unit_count = $unit->row()->_count;
            }

            $unit_counter = $this->db->query("SELECT _year, _month, count(_count) as _stat
                             FROM (SELECT YEAR(det.checkin_date) as _year, MONTH(det.checkin_date) as _month, count(det.unit_id) as _count
                                    FROM cs_reservation_detail det
                                    GROUP BY YEAR(det.checkin_date), MONTH(det.checkin_date), det.unit_id
                                  ) A
                             WHERE _year = " . $year . "
                             GROUP BY _year, _month");

            $qry = "SELECT YEAR(det.checkin_date) as _year,
                              MONTH(det.checkin_date) as _month,
                              count(det.checkin_date) as _count
                    FROM cs_reservation_detail det
                    JOIN cs_reservation_header head ON head.reservation_id = det.reservation_id
                    WHERE YEAR(det.checkin_date) = " . $year . " AND head.status <> " . STATUS_CANCEL . "
                    GROUP BY YEAR(det.checkin_date), MONTH(det.checkin_date)";

            $stats = $this->db->query($qry);

            for($i=1;$i<=12;$i++){
                $counter = 0;
                foreach($stats->result_array() as $st){
                    if($st['_month'] == $i){
                        $counter = $st['_count'];
                        break;
                    }
                }

                $unit_occupancy = 0;
                foreach($unit_counter->result_array() as $unit){
                    if($unit['_month'] == $i){
                        $unit_occupancy = $unit['_stat'];
                        break;
                    }
                }

                $dateObj   = DateTime::createFromFormat('!m', $i);
                $monthName = $dateObj->format('F'); //
                $monthName = substr($monthName,0,3);

                //$result[$i . ' ' . $year] = $counter;
                $off_days = $this->db->query("SELECT DISTINCT det.work_date
                                              FROM cs_srf_detail det
                                              JOIN cs_srf_header head ON head.srf_id = det.srf_id
                                              WHERE MONTH(det.work_date) = " . $i . " AND YEAR(det.work_date) = " . $year . " AND head.status IN(" . STATUS_APPROVE . ")");
                $off_days = $off_days->num_rows();

                $all_count = ($all_unit_count * 30) - $off_days;

                $percentage = round(($counter/$all_count) * 100,2);
                if($i > $iMonth){
                    $result[] = array('month' => $monthName, 'occupancy' => $unit_occupancy, 'percentage' => $percentage ,'dashLengthLine' => 3, 'alpha' => 0.2, 'color' => '#99D658', 'additional' => "");
                }else{
                    $result[] = array('month' => $monthName, 'occupancy' => $unit_occupancy, 'percentage' => $percentage ,'color' => '#99D658');
                }
            }
        }

        echo json_encode($result);
    }

    public function xreport_expected_arrival(){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $where_string = "";

        $where['status'] = ORDER_STATUS::RESERVED;

        $date_start = isset($_REQUEST['filter_date_from']) ?  dmy_to_ymd($_REQUEST['filter_date_from']) : date('Y-m-d');
        $date_end = isset($_REQUEST['filter_date_to']) ?  dmy_to_ymd($_REQUEST['filter_date_to']) : date('Y-m-d');

        $records = array();
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['arrival_date >='] = $date_start;
            }
        }

        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['arrival_date <='] = $date_end;
            }
        }

        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['company_name'] = $_REQUEST['filter_company'];
            }
        }

        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $where['reservation_type'] = $_REQUEST['filter_type'];
            }
        }

        if(isset($_REQUEST['filter_room'])){
            if($_REQUEST['filter_room'] != ''){
                $like['room'] = $_REQUEST['filter_room'];
            }
        }

        $iTotalRecords = $this->mdl_finance->countJoin("view_cs_reservation", array(), $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'arrival_date ASC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'arrival_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'room ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('view_cs_reservation.*',"view_cs_reservation ", array(), $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->reservation_code,
                $row->tenant_fullname,
                $row->company_name,
                RES_TYPE::caption($row->reservation_type),
                $row->room,
                dmy_from_db($row->arrival_date)
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function xreport_expected_departure(){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        $where_str = ""; //" status IN(" . ORDER_STATUS::CHECKIN . "," . ORDER_STATUS::CHECKOUT . ")";

        $date_start = isset($_REQUEST['filter_date_from']) ?  dmy_to_ymd($_REQUEST['filter_date_from']) : date('Y-m-d');
        $date_end = isset($_REQUEST['filter_date_to']) ?  dmy_to_ymd($_REQUEST['filter_date_to']) : date('Y-m-d');

        $records = array();
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                //$where['arrival_date >='] = $date_start;
            }
        }

        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                //$where['arrival_date <='] = $date_end;
            }
        }

        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['company_name'] = $_REQUEST['filter_company'];
            }
        }

        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $where['reservation_type'] = $_REQUEST['filter_type'];
            }
        }

        if(isset($_REQUEST['filter_room'])){
            if($_REQUEST['filter_room'] != ''){
                $like['room'] = $_REQUEST['filter_room'];
            }
        }

        $iTotalRecords = $this->mdl_finance->countJoin("fxnCSExpectedDeparture('" . $date_start . "','" . $date_end . "')", array(), $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'departure_date ASC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'departure_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'room ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('*',"fxnCSExpectedDeparture('" . $date_start . "','" . $date_end . "')", array(), $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){


            $records["data"][] = array(
                $row->reservation_code,
                $row->hidden_me > 0 ? INCOGNITO : $row->tenant_fullname,
                $row->company_name,
                RES_TYPE::caption($row->reservation_type),
                $row->room,
                $row->status == ORDER_STATUS::CHECKIN ? (dmy_from_db($row->departure_date)) . '&nbsp;&nbsp;' : dmy_from_db($row->departure_date) . '&nbsp;*' //&#10004;
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function xreport_daily_cash(){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        $where['pt.payment_type'] = PAYMENT_TYPE::CASH_ONLY;
        $where_string = "rv.status IN(" . STATUS_POSTED . "," . STATUS_CLOSED .")";

        $date_start = isset($_REQUEST['filter_date_from']) ?  dmy_to_ymd($_REQUEST['filter_date_from']) : date('Y-m-d');
        $date_end = isset($_REQUEST['filter_date_to']) ?  dmy_to_ymd($_REQUEST['filter_date_to']) : date('Y-m-d');

        $records = array();
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date, rv.receipt_date) >='] = $date_start;
            }
        }

        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date, rv.receipt_date) <='] = $date_end;
            }
        }

        $joins = array('view_cs_reservation cs' => 'rv.reservation_id = cs.reservation_id',
                       'ms_payment_type pt' => 'rv.paymenttype_id = pt.paymenttype_id');
        $iTotalRecords = $this->mdl_finance->countJoin("ar_receipt as rv", $joins, $where, $like, '', array(), $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'rv.receipt_date ASC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'rv.breceipt_date ' . $_REQUEST['order'][0]['dir'];
            }

        }

        $qry = $this->mdl_finance->getJoin('rv.*, cs.reservation_type, cs.reservation_code, cs.tenant_fullname, cs.company_name, cs.room',"ar_receipt as rv ", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_string);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $subject = $row->tenant_fullname;
            if($row->reservation_type == RES_TYPE::CORPORATE){
                if($row->is_primary_debtor > 0){
                    $subject = $row->company_name;
                }
            }

            $records["data"][] = array(
                $row->receipt_no,
                dmy_from_db($row->receipt_date),
                $subject,
                $row->reservation_code,
                $row->room,
                '<span class="mask_currency">' . ($row->receipt_amount + $row->receipt_bank_fee + $row->receipt_veritrans_fee) . '</span>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function xreport_guest_inhouse(){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        $where_str = ""; //" status IN(" . ORDER_STATUS::CHECKIN . "," . ORDER_STATUS::CHECKOUT . ")";

        $date_start = isset($_REQUEST['filter_date_from']) ?  dmy_to_ymd($_REQUEST['filter_date_from']) : date('Y-m-d');
        //$date_end = isset($_REQUEST['filter_date_to']) ?  dmy_to_ymd($_REQUEST['filter_date_to']) : date('Y-m-d');

        $records = array();
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                //$where['arrival_date >='] = $date_start;
            }
        }

        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                //$where['arrival_date <='] = $date_end;
            }
        }

        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['company_name'] = $_REQUEST['filter_company'];
            }
        }

        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $where['reservation_type'] = $_REQUEST['filter_type'];
            }
        }

        if(isset($_REQUEST['filter_room'])){
            if($_REQUEST['filter_room'] != ''){
                $like['room'] = $_REQUEST['filter_room'];
            }
        }

        $iTotalRecords = $this->mdl_finance->countJoin("fxnCSInhouseByDate('" . $date_start . "')", array(), $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'reservation_code ASC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'room ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'arrival_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'departure_date ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('*',"fxnCSInhouseByDate('" . $date_start . "')", array(), $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $i,
                $row->reservation_code,
                $row->hidden_me > 0 ? INCOGNITO : $row->tenant_fullname,
                $row->company_name,
                RES_TYPE::caption($row->reservation_type),
                $row->room,
                dmy_from_db($row->arrival_date),
                dmy_from_db($row->departure_date),
                ($row->qty_adult + $row->qty_child)
                //,'<span class="mask_currency">' . $row->balance . '</span>' //,nl2br($row->remark)
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

	public function xreport_reservation(){
        $this->load->model('finance/mdl_finance');

        $currentDate = date('Y-m-d');

        $where = array();
        $whereIn = array(ORDER_STATUS::ONLINE_VALID, ORDER_STATUS::RESERVED);

        $like = array();
        $whereString = "";
        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,view_cs_reservation.reservation_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,view_cs_reservation.reservation_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                //$whereString = "view_cs_reservation.tenant_fullname like '%" . $_REQUEST['filter_name'] . "%' " ;
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                //$whereString = "view_cs_reservation.tenant_fullname like '%" . $_REQUEST['filter_name'] . "%' " ;
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company'];
            }
        }
        if(isset($_REQUEST['filter_room'])){
            if($_REQUEST['filter_room'] != ''){
                $like['view_cs_reservation.room'] = $_REQUEST['filter_room'];
            }
        }
        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                //$like['view_cs_reservation.is_vip'] = $_REQUEST['filter_type'];
                $like['view_cs_reservation.reservation_type'] = $_REQUEST['filter_type'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $like['view_cs_reservation.status'] = $_REQUEST['filter_status'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('view_cs_reservation', $joins, $where, $like, 'view_cs_reservation.status', $whereIn, $whereString);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'view_cs_reservation.guest_type ASC,view_cs_reservation.arrival_date ASC ,view_cs_reservation.reservation_code DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'view_cs_reservation.reservation_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'view_cs_reservation.reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'view_cs_reservation.room ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('view_cs_reservation.*','view_cs_reservation', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, 'view_cs_reservation.status', $whereIn, $whereString);
 
        $records["data"] = array(); 
        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
			 if($row->guest_type ==0 ) 
				{$arival = dmy_from_db($row->arrival_date);}
				else 
				{
					$arival = date('F', strtotime(ymd_from_db($row->arrival_date))) ;
				}
            $records["data"][] = array(
                $i,
                $row->reservation_code,
                $row->hidden_me > 0 ? INCOGNITO : $row->tenant_fullname,
                $row->company_name,
                RES_TYPE::caption($row->reservation_type),
                $row->room,
                $row->unittype_bedroom,
               $arival
                //dmy_from_db($row->departure_date),
                //($row->qty_adult + $row->qty_child)
                //,'<span class="mask_currency">' . $row->balance . '</span>' //,nl2br($row->remark)
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

	public function xreport_reservation_agent(){
        $this->load->model('finance/mdl_finance');

        $currentDate = date('Y-m-d');

        $where = array();
        $whereIn = array(ORDER_STATUS::ONLINE_VALID, ORDER_STATUS::RESERVED,ORDER_STATUS::CHECKIN );

        $like = array();
        $whereString = "reservation_type != 4";
        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,view_cs_reservation.reservation_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,view_cs_reservation.reservation_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                //$whereString = "view_cs_reservation.tenant_fullname like '%" . $_REQUEST['filter_name'] . "%' " ;
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                //$whereString = "view_cs_reservation.tenant_fullname like '%" . $_REQUEST['filter_name'] . "%' " ;
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company'];
            }
        }
        if(isset($_REQUEST['filter_room'])){
            if($_REQUEST['filter_room'] != ''){
                $like['view_cs_reservation.room'] = $_REQUEST['filter_room'];
            }
        }
        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                //$like['view_cs_reservation.is_vip'] = $_REQUEST['filter_type'];
                $like['view_cs_reservation.reservation_type'] = $_REQUEST['filter_type'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $like['view_cs_reservation.status'] = $_REQUEST['filter_status'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('view_cs_reservation', $joins, $where, $like, 'view_cs_reservation.status', $whereIn, $whereString);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'view_cs_reservation.guest_type ASC,view_cs_reservation.arrival_date ASC ,view_cs_reservation.reservation_code DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'view_cs_reservation.reservation_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'view_cs_reservation.reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'view_cs_reservation.room ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('view_cs_reservation.*','view_cs_reservation', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, 'view_cs_reservation.status', $whereIn, $whereString);
 
        $records["data"] = array(); 
        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
			//$d_start = DateTime::createFromFormat('Y-m-d',ymd_from_db($row->arrival_date));
			//s$d_end = DateTime::createFromFormat('Y-m-d',ymd_from_db($row->departure_date));
			if($row->guest_type ==0 ) 
				{$arival = dmy_from_db($row->arrival_date);}
			else 
			{
				$arival = date('F', strtotime(ymd_from_db($row->arrival_date))) ;
			}
			//strtotime(ymd_from_db($row->arrival_date)),strtotime(ymd_from_db($row->departure_date)),true	
				//$bln = date_diff($d_start,$d_end);
				$bln = num_of_months(ymd_from_db($row->arrival_date),ymd_from_db($row->departure_date));
            $records["data"][] = array(
                $i, 
                dmy_from_db($row->reservation_date),
                $row->hidden_me > 0 ? INCOGNITO : $row->tenant_fullname,
                $row->company_name,
                RES_TYPE::caption($row->reservation_type),
                $row->room,
                $row->unittype_bedroom,
               $arival,
                dmy_from_db($row->departure_date),
				format_num($row->local_amount / $bln  ,0),	
                format_num($row->local_amount,0),				
                $row->agent_pic
                //($row->qty_adult + $row->qty_child)
                //,'<span class="mask_currency">' . $row->balance . '</span>' //,nl2br($row->remark)
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function xreport_sales_sum_by_year($period_year = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        //$period_year = isset($_REQUEST['period_year']) ?  $_REQUEST['period_year'] : date('Y');
        $period_year = $period_year > 0 ? $period_year : date('Y');
        $where['vw.period_year'] = $period_year;
        $where_string = "";

        $records = array();

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin("vw_agent_sales_yearly as vw", $joins, $where, $like, '', array(), $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'vw.agent_name ASC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'vw.agent_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('vw.*',"vw_agent_sales_yearly as vw ", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_string);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result_array() as $row){
            $records["data"][] = array(
                (trim($row['agent_name']) != '' ? $row['agent_name'] : '- No Agent -'),
                (trim($row['agent_pic']) != '' ? $row['agent_pic'] : '---'),
                '<span class="mask_currency">' . $row['01_unit'] . '</span>',
                '<span class="mask_currency">' . $row['01'] . '</span>',
                '<span class="mask_currency">' . $row['02_unit'] . '</span>',
                '<span class="mask_currency">' . $row['02'] . '</span>',
                '<span class="mask_currency">' . $row['03_unit'] . '</span>',
                '<span class="mask_currency">' . $row['03'] . '</span>',
                '<span class="mask_currency">' . $row['04_unit'] . '</span>',
                '<span class="mask_currency">' . $row['04'] . '</span>',
                '<span class="mask_currency">' . $row['05_unit'] . '</span>',
                '<span class="mask_currency">' . $row['05'] . '</span>',
                '<span class="mask_currency">' . $row['06_unit'] . '</span>',
                '<span class="mask_currency">' . $row['06'] . '</span>',
                '<span class="mask_currency">' . $row['07_unit'] . '</span>',
                '<span class="mask_currency">' . $row['07'] . '</span>',
                '<span class="mask_currency">' . $row['08_unit'] . '</span>',
                '<span class="mask_currency">' . $row['08'] . '</span>',
                '<span class="mask_currency">' . $row['09_unit'] . '</span>',
                '<span class="mask_currency">' . $row['09'] . '</span>',
                '<span class="mask_currency">' . $row['10_unit'] . '</span>',
                '<span class="mask_currency">' . $row['10'] . '</span>',
                '<span class="mask_currency">' . $row['11_unit'] . '</span>',
                '<span class="mask_currency">' . $row['11'] . '</span>',
                '<span class="mask_currency">' . $row['12_unit'] . '</span>',
                '<span class="mask_currency">' . $row['12'] . '</span>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

	#region Find Ledger

    public function ledger_find(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('frontdesk/report/ledger_find_tenant.php', $data);
        $this->load->view('layout/footer');
    }
	
	
    private function bind_generalledger($dateFrom, $dateTo){
        $result = array();

        if($dateFrom != '' && $dateTo != ''  ){
                

                $where  ="";
				$journals = $this->db->query("SELECT * FROM fxnGL_POSTING_TENANT( '" . $dateFrom."','".$dateTo ."' ) ".$where." order by coa_code");
		
                $currentCOACode = '';
                foreach($journals->result_array() as $j){
					
                    if($currentCOACode != $j['coa_code']){
                        //PREVIOUS
                        $prev = array();
                        $prev['coa_code'] = $j['coa_code'];
                        $prev['coa_desc'] = $j['coa_desc'];  
                        $prev['is_debit'] = ($j['is_debit'] > 0) ? true : false;
                        $prev['journal_no'] = '';
                        $prev['filing_no'] = '';
						$prev['tenant_fullname'] = '';
						$prev['subject'] = '';
						$prev['room'] = $j['room'];
						$prev['user_name'] = '';
						$prev['created_date'] = ''; 					
                        $prev['journal_date'] = date('d-m-y',strtotime($j['journal_date']));
                        $prev['journal_note'] = 'Previous Balance';
                        $prev['journal_debit'] = 0;
                        $prev['journal_credit'] = 0; 
						$prev['journal_amount'] = 0;
						//OBTAIN 
						$prevs = $this->db->query("SELECT * FROM fxnGL_PrevBalance('" . $dateFrom ."','" .$j['coa_code']. "')");
						//echo "SELECT * FROM fxnGL_PrevBalance('" . $dateFrom ."','" .$j['coa_code']. "')<br>" ;

						$dictBalance = array();
						if($prevs->num_rows() > 0){
							foreach($prevs->result_array() as $prev_1){
								
								//array_push($dictBalance,array($prev['coa_code'] => $prev_1['balance']));
								$dictBalance[$prev['coa_code']] = $prev_1['balance'];
								//print_r  ($dictBalance);
								
							}
						} 
						 $balance = isset($dictBalance[$j['coa_code']]) ? $dictBalance[$j['coa_code']] : 0;
						  

                        $prev['balance'] = $balance;
                        array_push($result, $prev);		
							//print_r ( $prev);
                        $currentCOACode = $j['coa_code'];
                    }

                    //CURRENT
                    $newrow = array();
                    $newrow['coa_code'] = $j['coa_code'];
                    $newrow['coa_desc'] = $j['coa_desc'];					
                    $newrow['is_debit'] = ($j['is_debit'] > 0) ? true : false;
                    $newrow['journal_no'] = $j['journal_no'];
                    $newrow['filing_no'] = $j['filing_no'];
                    $newrow['tenant_fullname'] = $j['tenant_fullname'];
					$newrow['subject'] = $j['subject'];
					$newrow['room'] = $j['room'];
					$newrow['user_name'] = $j['user_name'];
					$newrow['created_date'] =  date('H:i',strtotime($j['created_date']));
                    $newrow['journal_date'] = date('d-m-y',strtotime($j['journal_date']));
                    $newrow['journal_note'] = (trim($j['journal_note']) == '') ? '-' : $j['journal_note'];
                    $newrow['journal_debit'] = $j['journal_debit'];
                    $newrow['journal_credit'] = $j['journal_credit']; 

                    //Get Ordered of Inserted Rows
                    $prevBalance = 0;
                    foreach($result as $row){
                        if($row['coa_code'] == $j['coa_code']){
                            $prevBalance = $row['balance'];
                        }
                    }

                    if($newrow['is_debit']){
                        $balance = $prevBalance + ($newrow['journal_debit'] - $newrow['journal_credit']);
						$newrow['journal_amount']=  ($newrow['journal_debit'] - $newrow['journal_credit']);
                    }else{
                        $balance = $prevBalance + ($newrow['journal_credit'] - $newrow['journal_debit']);
						$newrow['journal_amount'] =   ($newrow['journal_credit'] - $newrow['journal_debit']);
                    }

                    $newrow['balance'] = $balance;

                    array_push($result, $newrow);					
					 
                }
            
        }

        return $result;
    }
	
    public function pdf_ledger_tenant() {
        $dateFrom = '';
        $dateTo = ''; 
        $ispdf = true;
		$isMultiPages = 1;

        $dateFrom = isset($_POST['date_start']) ? $_POST['date_start'] : date('d-m-Y');
		$dateTo = isset($_POST['date_to']) ? $_POST['date_to'] : date('d-m-Y');
		$ispdf = isset($_POST['is_pdf']) ? $_POST['is_pdf']: false;

        if(trim($dateFrom) != '' && trim($dateTo) != '' ){
            $this->session->set_userdata('pdf_ledger', array('date_start' => $dateFrom, 'date_to' => $dateTo, 'is_pdf' => $ispdf));

            $data['header'] = array('company_name'=>'TPD',
                  'date_from' => $dateFrom,
                  'date_to' => $dateTo);

            $data['date_start'] = $dateFrom;
            $data['date_to'] = $dateTo;
            $data['is_pdf'] = $ispdf;

            $dateFrom = dmy_to_ymd(trim($dateFrom));
            $dateTo = dmy_to_ymd(trim($dateTo));

            $this->load->model('finance/mdl_finance');
			 
            $data['qry_det'] = $this->bind_generalledger($dateFrom,$dateTo);

            if($ispdf){ 
                $this->load->view('frontdesk/report/pdf_ledger_tenant.php', $data);
            }else{
                $this->load->view('frontdesk/report/pdf_ledger_tenant.php', $data);
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

                $this->dompdf->stream("pdf_ledger_tenant_". date('Y_m_d_h_i_s') . ".pdf", array('Attachment'=>0));
            }

        }else{
            //tpd_404();
            $this->ledger_find();
        }
    }

}

/* End of file booking.php */
/* Location: ./application/controllers/frondesk/booking.php */