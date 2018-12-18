<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reservation extends CI_Controller {

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
		$this->find_room();
	}

    #region Find Room

    public function find_room(){
        //$this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/icheck/skins/all.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/fullcalendar/fullcalendar.min.css');

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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/icheck/icheck.min.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/moment.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/fullcalendar/fullcalendar.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('frontdesk/reservation/find_room.php', $data);
        $this->load->view('layout/footer');
    }

	public function ajax_find_room(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        $dateFrom = dmy_to_ymd($_POST['date_from']);
        $dateTo = dmy_to_ymd($_POST['date_to']);
        $unitType = $_POST['unit_type'];

        //$guests = intval($_POST['guests']);
        $num_adult = intval($_POST['num_adult']);
        $num_child = intval($_POST['num_child']);

		$str_guest = "";
		if ($num_adult > 0) { $str_guest = " and unittype_max_adult >= " . $num_adult; }
        $str_child = "";
        if ($num_child > 0) { $str_child = " and unittype_max_child >= " . $num_child; }

        if($dateFrom != '' && $dateTo != ''){
            $sOutput = '';
            $types = $this->db->query("select * from ms_unit_type where status = " . STATUS_NEW . "
                                       and unittype_id " . ($unitType > 0 ? " = " . $unitType : " > 0 ") . $str_guest . $str_child . "
                                       order by unittype_code "
                                     );

            $qry = $this->db->query("select * from ms_unit unt
                                     where unittype_id " . ($unitType > 0 ? " = " . $unitType : " > 0 ") . "
                                     order by floor_id, unit_code"
                                    );

            if($types->num_rows() > 0 && $qry->num_rows() > 0){
                $floor = $qry->row()->floor_id;
                foreach($types->result_array() as $typ){
                    $temp = '';
                    $header = '<h3>' . $typ['unittype_desc'] . '</h3>';
                    foreach($qry->result_array() as $res){
                        $unitTitle = '<strong>' . $res['unit_code'] . '</strong> / <i>' . $typ['unittype_desc'] . '</i>';

                        if(trim($res['remark']) != ''){
                            $unitTitle .= ' / ' . $res['remark'];
                        }

                        if($res['floor_id'] != $floor){
                            //$temp .= '<br>';
                            $floor = $res['floor_id'];
                        }
                        $unit = $this->db->query("SELECT DISTINCT cs_reservation_detail.unit_id,cs_reservation_header.status
                                                  FROM cs_reservation_detail
                                                  JOIN cs_reservation_header ON cs_reservation_header.reservation_id = cs_reservation_detail.reservation_id
                                                  WHERE cs_reservation_detail.unit_id = " . $res['unit_id'] . " AND CONVERT(date, cs_reservation_detail.checkin_date) BETWEEN '" . $dateFrom . "' and '" . $dateTo . "' AND cs_reservation_detail.status = " . STATUS_NEW );

                        if($unit->num_rows() > 0){
                            /*
                            if($typ['unittype_id'] == $res['unittype_id']){
                                if($unit->row()->status == ORDER_STATUS::RESERVED){
                                    $temp .= '<div class="btn-group tooltips" data-original-title="Reserved" style="margin-right:3px; margin-bottom:3px;">
                                      <div class="btn btn-lg grey-gallery dropdown-toggle" style="padding-right: 5px;padding-left: 10px;width:35px;margin-left:0px;" disabled>&nbsp;</div>
                                                <button type="button" style="width:65px;margin-left:0px;" class="btn btn-lg grey-gallery calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '" disabled>'. $res['unit_code'] .'</button>
                                                <button type="button" class="btn btn-lg grey-gallery dropdown-toggle calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '" data-toggle="dropdown" style="width:36px;margin-left:0px;"><i class="fa fa-bell-o" aria-hidden="true"></i></button>
                                      </div>';
                                }else{
                                    $temp .= '<div class="btn-group tooltips " data-original-title="Occupied" style="margin-right:3px; margin-bottom:3px;">
                                      <div class="btn btn-lg red-sunglo dropdown-toggle" style="padding-right: 5px;padding-left: 10px;width:35px;margin-left:0px;" disabled>&nbsp;</div>
                                                <button type="button" style="width:65px;margin-left:0px;" class="btn btn-lg red-sunglo calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '" disabled>'. $res['unit_code'] .'</button>
                                                <button type="button" class="btn btn-lg red-sunglo dropdown-toggle calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '" data-toggle="dropdown" style="width:36px;margin-left:0px;"><i class="icon-user" aria-hidden="true"></i></button>
                                      </div>';
                                }
                            }
                            */
                        }else{
                            //Check SRF
                            $unit = $this->db->query("SELECT DISTINCT h.srf_type FROM cs_srf_detail d
                                                      JOIN cs_srf_header h ON d.srf_id = h.srf_id And h.unit_id = " . $res['unit_id'] . "
                                                      WHERE h.status = " . STATUS_APPROVE ." And h.unit_id = " . $res['unit_id'] . "
                                                      AND d.work_date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'");
                            if($unit->num_rows() > 0){
                                /*
                                if($typ['unittype_id'] == $res['unittype_id']){
                                    //Not Ready
                                    $srf = $unit->row();
                                    $temp .= '<div class="btn-group tooltips " data-original-title="' . UNIT_STATUS::caption($srf->srf_type) .'" style="margin-right:3px; margin-bottom:3px;">
                                      <div class="btn btn-lg red-sunglo dropdown-toggle" style="padding-right: 5px;padding-left: 10px;width:35px;margin-left:0px;" disabled>&nbsp;</div>
                                                <button type="button" style="width:65px;margin-left:0px;" class="btn btn-lg red-sunglo calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '" disabled>'. $res['unit_code'] .'</button>
                                                <button type="button" class="btn btn-lg red-sunglo dropdown-toggle calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '" data-toggle="dropdown" style="width:36px;margin-left:0px;"><i class="icon-user" aria-hidden="true"></i></button>
                                      </div>';
                                }
                                */
                            }else{
                                if($typ['unittype_id'] == $res['unittype_id']){
                                    if($res['status'] == UNIT_STATUS::DIRTY){
                                        //Dirty
                                        $temp .= '<div class="btn-group tooltips" data-original-title="Dirty" style="margin-right:3px; margin-bottom:3px;">
                                                <div class="btn btn-lg grey-silver dropdown-toggle" style="padding-right: 5px;padding-left: 10px;"><input type="checkbox" class="icheck" name="chk_unit[]" value="' . $res['unit_id'] . '"></div>
                                                <button type="button" style="width:65px;" class="btn btn-lg btn-warning  calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '">'. $res['unit_code'] .'</button>
                                                <button type="button" class="btn btn-lg btn-warning dropdown-toggle calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '" data-toggle="dropdown" style="width:35px;"><i class="icon-info" aria-hidden="true"></i></button>
                                            </div>';
                                    }elseif($res['status'] == UNIT_STATUS::NOT_READY){
                                        //Not Ready
                                        $temp .= '<div class="btn-group tooltips" data-original-title="Not Ready" style="margin-right:3px; margin-bottom:3px;">
                                                <div class="btn btn-lg grey-silver dropdown-toggle" style="padding-right: 5px;padding-left: 10px;"><input type="checkbox" class="icheck" name="chk_unit[]" value="' . $res['unit_id'] . '"></div>
                                                <button type="button" style="width:65px;" class="btn btn-lg btn-danger calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '">'. $res['unit_code'] .'</button>
                                                <button type="button" class="btn btn-lg btn-danger dropdown-toggle calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '" data-toggle="dropdown" style="width:35px;"><i class="icon-shield" aria-hidden="true"></i></button>
                                            </div>';
                                    }else{
                                        //Ready
                                        $temp .= '<div class="btn-group tooltips" data-original-title="' . 'Available' . '" style="margin-right:3px; margin-bottom:3px;">
                                            <div class="btn btn-lg grey-silver dropdown-toggle" style="padding-right: 5px;padding-left: 10px;"><input type="checkbox" class="icheck" name="chk_unit[]" value="' . $res['unit_id'] . '" ></div>
                                            <button type="button" style="width:65px;" class="btn btn-lg btn-success calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '" >' . $res['unit_code'] . '</button>
                                            <button type="button" class="btn btn-lg btn-success dropdown-toggle calendar-info" unit-id="' . $res['unit_id'] . '" unit-type="' . $typ['unittype_id'] . '" unit-title="' . $unitTitle . '" data-toggle="dropdown" style="width:35px;"><i class="icon-star" aria-hidden="true"></i></button>
                                        </div>';
                                    }
                                }
                            }
                        }
                    }

                    if(strlen($temp) > 5){
                        $sOutput .= $header;
                        $sOutput .= $temp;
                    }
                }
                $result['type'] = 1;
                $result['message'] = $sOutput;
            }else{
                $result['type'] = 0;
                $result['message'] = 'No Room(s) Available';
            }
        }

        //echo $result;
        echo json_encode($result);
    }

    public function ajax_calendar_info(){
        $result = array();

        //Used to display notification
        $result['startDate'] = date('Y-m-d');
        $result['endDate'] = date('Y-m-d');
        $result['rates'] = array();

        $dateFrom = dmy_to_ymd($_POST['date_from']);
        $dateTo = dmy_to_ymd($_POST['date_to']);
        $unitTypeId = $_POST['unit_type'];
        $reservationType = $_POST['reservation_type'];
        $agentID = $_POST['agent_id'];

        $unitId = $_POST['unit_id'];

        $nDays = num_of_days($dateFrom,$dateTo, RENT_BY_NIGHT);
        $is_monthly = $nDays >= 30 ? true : false;

        if($dateFrom != '' && $dateTo != '' && $unitTypeId > 0){
            $date_until = DateTime::createFromFormat('Y-m-d', $dateTo);
            $date_until->sub(new DateInterval('P1D'));

            $rates = $this->db->query("select * from ms_rate
                                      where unittype_id = " . $unitTypeId . " and convert(date, rate_date) between '" . $dateFrom . "' and '" . $date_until->format('Y-m-d') . "'");

            if($rates->num_rows() > 0){
                for($i=0;$i<$rates->num_rows();$i++){
                    $rate = $rates->row($i);

                    $reserved_unit = $this->db->query("SELECT cs_reservation_header.status FROM cs_reservation_detail
                                               JOIN cs_reservation_header ON cs_reservation_header.reservation_id = cs_reservation_detail.reservation_id
                                               WHERE cs_reservation_detail.unit_id = " . $unitId . " AND convert(date, cs_reservation_detail.checkin_date) = '". ymd_from_db($rate->rate_date) . "' AND cs_reservation_detail.status = " . STATUS_NEW);

                    if($reserved_unit->num_rows() > 0){
                        $status = $reserved_unit->row()->status;
                        if($reservationType == RES_TYPE::PERSONAL){
                            array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_normal,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => ($status == ORDER_STATUS::RESERVED ? 2 : 3)
                            ));
                        }else{
                            array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_corporate,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => ($status == ORDER_STATUS::RESERVED ? 2 : 3)
                            ));
                        }
                    }else{
                        if($reservationType == RES_TYPE::PERSONAL){
                            if($agentID <= 0){
                                if($is_monthly){
                                    array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_normal_monthly,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                                }else{
                                    array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_normal,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                                }
                            }else{
                                if($is_monthly){
                                    array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_normal,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                                }
                            }
                        }else{
                            if($agentID <= 0){
                                if($is_monthly){
                                    array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_corporate_monthly,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                                }else{
                                    array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_corporate,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                                }
                            }else{
                                array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_corporate,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                            }
                        }
                    }
                }
            }

            //Get Spare 7 days before
            $dateFrom = DateTime::createFromFormat('Y-m-d', $dateFrom);
            $dateFrom->sub(new DateInterval('P7D'));

            $result['startDate'] = $dateFrom->format('Y-m-d');

            $previous = $this->getSpareRate($unitTypeId,$unitId, $reservationType ,$dateFrom, dmy_to_ymd($_POST['date_from']));
            if(count($previous) > 0){
                foreach($previous as $prev){
                    array_push($result["rates"], $prev);
                }
            }

            //Get Spare 7 days after
            $dateFrom = DateTime::createFromFormat('Y-m-d', $dateTo);
            //$dateFrom->add(new DateInterval('P1D'));

            $dateTo = DateTime::createFromFormat('Y-m-d', $dateTo);
            $dateTo->add(new DateInterval('P8D'));

            $result['endDate'] = $dateTo->format('Y-m-d');

            $after = $this->getSpareRate($unitTypeId,$unitId, $reservationType,$dateFrom, $dateTo->format('Y-m-d'));
            if(count($after) > 0){
                foreach($after as $rec){
                    array_push($result["rates"], $rec);
                }
            }

            $result['totalDays'] = $nDays;
        }

        //echo $result;
        echo json_encode($result);
    }

    public function ajax_calendar_info_v1(){
        $result = array();
        //Used to display notification
        $result['startDate'] = date('Y-m-d');
        $result['endDate'] = date('Y-m-d');
        $result['rates'] = array();

        $dateFrom = dmy_to_ymd($_POST['date_from']);
        $dateTo = dmy_to_ymd($_POST['date_to']);
        $unitTypeId = $_POST['unit_type'];
        $reservationType = $_POST['reservation_type'];
        $agentID = $_POST['agent_id'];

        $unitId = $_POST['unit_id'];

        $nDays = num_of_days($dateFrom,$dateTo, RENT_BY_NIGHT);
        $is_monthly = $nDays >= 30 ? true : false;

        if($dateFrom != '' && $dateTo != '' && $unitTypeId > 0){
            $rates = $this->db->query("select * from ms_rate
                                      where unittype_id = " . $unitTypeId . " and convert(date, rate_date) between '" . $dateFrom . "' and '" . $dateTo . "'");

            if($rates->num_rows() > 0){
                for($i=0;$i<$rates->num_rows();$i++){
                    $rate = $rates->row($i);

                    $reserved_unit = $this->db->query("SELECT cs_reservation_header.status FROM cs_reservation_detail
                                               JOIN cs_reservation_header ON cs_reservation_header.reservation_id = cs_reservation_detail.reservation_id
                                               WHERE cs_reservation_detail.unit_id = " . $unitId . " AND convert(date, cs_reservation_detail.checkin_date) = '". ymd_from_db($rate->rate_date) . "' AND cs_reservation_detail.status = " . STATUS_NEW);

                    if($reserved_unit->num_rows() > 0){
                        $status = $reserved_unit->row()->status;
                        if($reservationType == RES_TYPE::PERSONAL){
                            array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_normal,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => ($status == ORDER_STATUS::RESERVED ? 2 : 3)
                            ));
                        }else{
                            array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_corporate,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => ($status == ORDER_STATUS::RESERVED ? 2 : 3)
                            ));
                        }
                    }else{
                        if($reservationType == RES_TYPE::PERSONAL){
                            if($agentID <= 0){
                                if($is_monthly){
                                    array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_normal_monthly,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                                }else{
                                    array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_normal,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                                }
                            }else{
                                if($is_monthly){
                                    array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_normal,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                                }
                            }
                        }else{
                            if($agentID <= 0){
                                if($is_monthly){
                                    array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_corporate_monthly,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                                }else{
                                    array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_corporate,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                                }
                            }else{
                                array_push($result["rates"], array("title" => "IDR \n" . format_num($rate->rate_corporate,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", "type" => 0));
                            }
                        }
                    }
                }
            }

            //Get Spare 7 days before
            $dateFrom = DateTime::createFromFormat('Y-m-d', $dateFrom);
            $dateFrom->sub(new DateInterval('P7D'));

            $result['startDate'] = $dateFrom->format('Y-m-d');

            $previous = $this->getSpareRate($unitTypeId,$unitId, $reservationType ,$dateFrom, dmy_to_ymd($_POST['date_from']));
            if(count($previous) > 0){
                foreach($previous as $prev){
                    array_push($result["rates"], $prev);
                }
            }

            //Get Spare 7 days after
            $dateFrom = DateTime::createFromFormat('Y-m-d', $dateTo);
            $dateFrom->add(new DateInterval('P1D'));

            $dateTo = DateTime::createFromFormat('Y-m-d', $dateTo);
            $dateTo->add(new DateInterval('P8D'));

            $result['endDate'] = $dateTo->format('Y-m-d');

            $after = $this->getSpareRate($unitTypeId,$unitId, $reservationType,$dateFrom, $dateTo->format('Y-m-d'));
            if(count($after) > 0){
                foreach($after as $rec){
                    array_push($result["rates"], $rec);
                }
            }

            $result['totalDays'] = $nDays;
        }

        //echo $result;
        echo json_encode($result);
    }

    public function ajax_get_departure(){
        $result = array();

        //Used to display notification
        $result['valid'] = 0;
        $result['departure_date'] = '';

        $dateFrom = dmy_to_ymd($_POST['arrival_date']);
        $numMonth = floatval($_POST['num_month']);
        //$billingBase = BILLING_BASE::MONTHLY;

        if($dateFrom != '' && $numMonth > 0){
            $res = $this->db->query("SELECT out_date FROM fxnCheckout_Date('" . $dateFrom . "'," . BILLING_BASE::MONTHLY . "," . $numMonth . ")");
            if($res->num_rows() > 0){
                $result['departure_date'] = dmy_from_db($res->row()->out_date);
                $result['valid'] = 1;
            }
        }

        //echo $result;
        echo json_encode($result);
    }

    private function getSpareRate($unitTypeId, $unitId, $reservationType, $dateFrom, $dateTo){
        $result = array();

        if($unitTypeId > 0 && $unitId > 0 && isset($dateFrom)){
            $beforeQry = $this->db->query("select cs_reservation_detail.checkin_date,cs_reservation_header.status,ms_rate.rate_normal,ms_rate.rate_member, ms_rate.rate_corporate
                                         from cs_reservation_detail
                                         join cs_reservation_header on cs_reservation_header.reservation_id = cs_reservation_detail.reservation_id
                                         left join ms_rate on ms_rate.unittype_id = " . $unitTypeId . " and convert(date,ms_rate.rate_date) = convert(date,cs_reservation_detail.checkin_date)
                                      where cs_reservation_detail.unit_id = " . $unitId . " and convert(date, cs_reservation_detail.checkin_date) >= '" . $dateFrom->format('Y-m-d') . "' and convert(date, cs_reservation_detail.checkin_date) < '" . $dateTo . "'");

            //$result['debug'] = $this->db->last_query();

            if($beforeQry->num_rows() > 0){
                for($y=0;$y<7;$y++){
                    if($y>0){
                        $dateFrom = $dateFrom->modify('+1 day');
                    }

                    $found = array();
                    foreach($beforeQry->result_array() as $before){
                        if(ymd_from_db($before['checkin_date']) == $dateFrom->format('Y-m-d')){
                            $found = $before;
                            break;
                        }
                    }

                    if(count($found) > 0){
                        $type = ($found['status'] == ORDER_STATUS::RESERVED ? 2 : 3);

                        if($reservationType == RES_TYPE::PERSONAL){
                            array_push($result, array("title" => "IDR \n" . format_num($found['rate_normal'],0), "start" => $dateFrom->format('Y-m-d'), "backgroundColor" => "rgba(255,255,255,0)", "type" => $type));
                        }else{
                            array_push($result, array("title" => "IDR \n" . format_num($found['rate_corporate'],0), "start" => $dateFrom->format('Y-m-d'), "backgroundColor" => "rgba(255,255,255,0)", "type" => $type));
                        }
                    }else{
                        $rates = $this->db->query("select * from ms_rate
                                      where unittype_id = " . $unitTypeId . " and convert(date, rate_date) = '" . $dateFrom->format('Y-m-d') . "'");
                        if($rates->num_rows() > 0){
                            $rate = $rates->row();
                            if($reservationType == RES_TYPE::PERSONAL){
                                array_push($result, array("title" => "IDR \n" . format_num($rate->rate_normal,0), "start" => $dateFrom->format('Y-m-d'), "backgroundColor" => "rgba(255,255,255,0)", 'type' => 1));
                            }else{
                                array_push($result, array("title" => "IDR \n" . format_num($rate->rate_corporate,0), "start" => $dateFrom->format('Y-m-d'), "backgroundColor" => "rgba(255,255,255,0)", 'type' => 1));
                            }
                        }
                    }
                }
            }else{
                $rates = $this->db->query("select * from ms_rate
                                      where unittype_id = " . $unitTypeId . " and convert(date, rate_date) >= '" . $dateFrom->format('Y-m-d') . "' and convert(date, rate_date) < '" . $dateTo . "'");
                if($rates->num_rows() > 0){
                    foreach($rates->result() as $rate){
                        if($reservationType == RES_TYPE::PERSONAL){
                            array_push($result, array("title" => "IDR \n" . format_num($rate->rate_normal,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", 'type' => 1));
                        }else{
                            array_push($result, array("title" => "IDR \n" . format_num($rate->rate_corporate,0), "start" => ymd_from_db($rate->rate_date), "backgroundColor" => "rgba(255,255,255,0)", 'type' => 1));
                        }
                    }
                }
            }
        }

        return $result;
    }

    private function get_ready_room($dateFromYMD = '', $dateToYMD = '', $num_adult = 0, $num_child = 0, $unittype_id = 0){
        $result = array();

        $str_guest = "";
        if ($num_adult > 0) { $str_guest = " AND utype.unittype_max_adult >= " . $num_adult; }
        $str_child = "";
        if ($num_child > 0) { $str_child = " AND utype.unittype_max_child >= " . $num_child; }

        if($dateFromYMD != '' && $dateToYMD != ''){
            $qry = $this->db->query("SELECT unt.*, utype.unittype_desc FROM ms_unit unt
                                     JOIN ms_unit_type utype ON utype.unittype_id = unt.unittype_id
                                     WHERE unt.unittype_id = " . $unittype_id . " " . $str_guest . $str_child . "
                                     ORDER BY unt.floor_id, unt.unit_code"
            );

            if($qry->num_rows() > 0){
                foreach($qry->result_array() as $res) {
                    $unit = $this->db->query("SELECT DISTINCT cs_reservation_detail.unit_id,cs_reservation_header.status
                                                  FROM cs_reservation_detail
                                                  JOIN cs_reservation_header ON cs_reservation_header.reservation_id = cs_reservation_detail.reservation_id
                                                  WHERE cs_reservation_detail.unit_id = " . $res['unit_id'] . " AND CONVERT(date, cs_reservation_detail.checkin_date) BETWEEN '" . dmy_to_ymd($dateFromYMD) . "' and '" . dmy_to_ymd($dateToYMD) . "' AND cs_reservation_detail.status = " . STATUS_NEW);
                    if ($unit->num_rows() <= 0) {
                        //Check SRF
                        $unit = $this->db->query("SELECT DISTINCT h.srf_type FROM cs_srf_detail d
                                                      JOIN cs_srf_header h ON d.srf_id = h.srf_id And h.unit_id = " . $res['unit_id'] . "
                                                      WHERE h.status = " . STATUS_APPROVE ." And h.unit_id = " . $res['unit_id'] . "
                                                      AND d.work_date BETWEEN '" . $dateFromYMD . "' AND '" . $dateToYMD . "'");
                        if($unit->num_rows() <= 0){
                            $unit_id = $res['unit_id'];
                            array_push($result, array('unit_id' => $unit_id, 'unittype_id' => $res['unittype_id'], 'unit_code' => $res['unit_code'], 'unittype_desc' => $res['unittype_desc']));
                        }
                    }
                }

            }
        }else{
            unset($result);
            $result = null;
        }

        //echo $result;
        return $result;
    }

    #endregion

    #region Reservation Form

    public function ajax_change_reservation_type(){
        $this->load->model('frontdesk/mdl_frontdesk');

        $result = '';

        $reservationType = isset($_POST['reservation_type']) ? $_POST['reservation_type'] : 0;
        //$agentId = isset($_POST['agent_id']) ? $_POST['agent_id'] : 0;
        $unitIds = isset($_POST['unit_id']) ? $_POST['unit_id'] : array();
        $arrivalDates = isset($_POST['arrival_date']) ? $_POST['arrival_date'] : array();
        $departureDates = isset($_POST['departure_date']) ? $_POST['departure_date'] : array();
        $isYearlyRate = isset($_POST['is_rate_yearly']) ? $_POST['is_rate_yearly'] : 0;
        $billingType = isset($_POST['billing_type']) ? BILLING_TYPE::FULL_PAID : BILLING_TYPE::MONTHLY;
        $discounts = isset($_POST['discount_amount']) ? $_POST['discount_amount'] : array();

        if(count($unitIds) > 0 && count($arrivalDates) > 0 && count($departureDates) > 0){
            $qry = $this->db->query('select ms_unit.unit_id, ms_unit.unittype_id, ms_unit.unit_code,ms_unit_type.unittype_desc, ms_unit_type.unittype_max_adult, ms_unit_type.unittype_max_child from ms_unit
                                         join ms_unit_type on ms_unit_type.unittype_id = ms_unit.unittype_id
                                         where ms_unit.unit_id IN(' . implode(',', $unitIds). ') '
            );

            $num_month = 0;
            $room_unit = array();
            if($qry->num_rows() > 0){
                foreach($qry->result_array() as $unit){
                    $calc = $this->mdl_frontdesk->calculate_booking($unit['unittype_id'], $arrivalDates[0], $departureDates[0], $reservationType, $isYearlyRate, $billingType);

                    array_push($room_unit, array('unit_id'=> $unit['unit_id'],'unit_code'=>$unit['unit_code'],'unittype_id' => $unit['unittype_id'],'unittype_desc' => $unit['unittype_desc'],'checkin_date'=> $arrivalDates[0], 'checkout_date' => $departureDates[0], 'formula' => $calc['formula'], 'local_amount' => $calc['total_amount'], 'tax_rate' => $calc['tax_rate'], 'max_adult' => $unit['unittype_max_adult'], 'max_child' => $unit['unittype_max_child'], 'period_caption' => $calc['period_caption']));

                    $num_month = ($calc['yearly_count'] * 12) + $calc['monthly_count'];
                }
            }

            foreach($room_unit as $room){
                $taxAmount = $room['tax_rate'] * ($room['local_amount'] - 0);
                $subtotal = round(($room['local_amount'] - 0 + $taxAmount),0);

                $result .= '<tr>
                                                                        <td class="text-center" style="padding-top:12px;">
                                                                        <input type="hidden" name="unit_id[]" value="'. $room['unit_id'] . '">' . $room['unit_code']. '</td>
                                                                        <td style="padding-top:12px;">' . $room['unittype_desc']. '</td>
                                                                        <td class="text-center" style="padding-top:12px;"><input type="hidden" name="arrival_date[]" value="'. $room['checkin_date'] . '">' . $room['checkin_date'] . '</td>
                                                                        <td class="text-center" style="padding-top:12px;"><input type="hidden" name="departure_date[]" value="'. $room['checkout_date'] . '">' . $room['checkout_date'] . '</td>
                                                                        <td class="text-center" style="padding-top:12px;"><input type="hidden" name="days[]" value="'. 0 . '"><input type="hidden" name="num_month[]" value="'. $num_month . '">' . $room['period_caption'] . '</td>
                                                                        <td class="text-right" style="padding-top:12px;"><input type="hidden" name="local_amount[]" value="'. $room['local_amount'] . '">' . format_num($room['local_amount'],0) . '</td>
                                                                        <td class="text-right" ><input type="text" class="form-control input-sm text-right mask_currency" name="discount_amount[]" value="'. 0 . '"></td>
                                                                        <td class="text-right" >
                                                                        <input type="hidden" name="tax_rate[]" value="'. $room['tax_rate'] . '">
                                                                        <input type="text" class="form-control input-sm text-right mask_currency" name="tax_amount[]" value="'. $taxAmount . '" readonly></td>
                                                                        <td class="text-right" ><input type="text" class="form-control input-sm text-right mask_currency" name="subtotal_amount[]" value="'. $subtotal . '" readonly></td>';

                $result .=  '</tr>';
            }
        }

        echo $result;
    }

    public function pre_reservation_form(){
        if(isset($_POST)){
            $this->load->model('frontdesk/mdl_frontdesk');

            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-webcam/jquery.webcam.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            //HEADER
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

            $data['reservation_id'] = 0;
            $data['reservation_type'] = (isset($_POST['reservation_type']) ? $_POST['reservation_type'] : 0);
            $data['agent_id'] = (isset($_POST['agent_id']) ? $_POST['agent_id'] : 0);
            $data['arrival_date'] = isset($_POST['arrival_date']) ? $_POST['arrival_date'] : '';
            $data['departure_date'] = isset($_POST['departure_date']) ? $_POST['departure_date'] : '' ;
            $data['num_month'] = (isset($_POST['n_month']) ? $_POST['n_month'] : 0);
            $data['num_adult'] = (isset($_POST['n_adult']) ? $_POST['n_adult'] : 0);
            $data['num_child'] = (isset($_POST['n_child']) ? $_POST['n_child'] : 0);

            $checkedUnits = isset($_POST['chk_unit']) ? $_POST['chk_unit'] : array();

            if(count($checkedUnits) > 0 && $data['arrival_date'] != '' && $data['departure_date'] != ''){
                $qry = $this->db->query('select ms_unit.unit_id, ms_unit.unittype_id, ms_unit.unit_code, ms_unit_type.unittype_max_adult, ms_unit_type.unittype_max_child, ms_unit_type.unittype_desc from ms_unit
                                         join ms_unit_type on ms_unit_type.unittype_id = ms_unit.unittype_id
                                         where ms_unit.unit_id IN(' . implode(',', $checkedUnits). ') '
                );

                $room_unit = array();

                $errorMsg = '';
                if($qry->num_rows() > 0){
                    foreach($qry->result_array() as $unit){
                        $calc = $this->mdl_frontdesk->calculate_booking($unit['unittype_id'], $data['arrival_date'], $data['departure_date'], $data['reservation_type'], 0, BILLING_TYPE::FULL_PAID);

                        if($calc['total_amount'] > 0 || $data['reservation_type'] == RES_TYPE::HOUSE_USE){
                            array_push($room_unit, array('unit_id'=> $unit['unit_id'],'unit_code'=>$unit['unit_code'],'unittype_id' => $unit['unittype_id'],'unittype_desc' => $unit['unittype_desc'],'checkin_date'=> $data['arrival_date'], 'checkout_date' => $data['departure_date'], 'formula' => $calc['formula'], 'local_amount' => $calc['total_amount'], 'tax_rate' => $calc['tax_rate'], 'max_adult' => $unit['unittype_max_adult'], 'max_child' => $unit['unittype_max_child'], 'daily_count' => $calc['daily_count'], 'period_caption' => $calc['period_caption']));
                        }else{
                            $errorMsg .= '<br/>Room ' . $unit['unit_code'] . ' rate for selected period not available.';
                        }
                    }
                }

                if($data['agent_id'] > 0){
                    $agent_qry = $this->db->get_where('ms_agent', array('agent_id' => $data['agent_id']));
                    if($agent_qry->num_rows() > 0){
                        $data['agent'] = $agent_qry->row();
                    }
                }

                $data['room_unit'] = $room_unit;
                $data['back_url'] = base_url('frontdesk/reservation/find_room.tpd');

                if($data['reservation_type'] == RES_TYPE::HOUSE_USE){
                    $data['enable_discount'] = false;
                }else{
                     $data['enable_discount'] = true;
                }

                if($errorMsg == ''){
                    $this->load->view('layout/header', $data_header);
                    $this->load->view('frontdesk/reservation/reserve_form', $data);
                    $this->load->view('layout/footer');
                }else{
                    $this->session->set_flashdata('flash_message_class', 'warning');
                    $this->session->set_flashdata('flash_message', $errorMsg);

                    redirect(base_url('frontdesk/reservation/find_room.tpd'));
                }
            }else{
                redirect(base_url('frontdesk/reservation/find_room.tpd'));
            }
        }
    }

    public function reservation_form($id = 0, $ref_page = 0){
        $this->load->model('frontdesk/mdl_frontdesk');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-webcam/jquery.webcam.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        //HEADER
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/fancybox/source/jquery.fancybox.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/fancybox/source/jquery.fancybox.pack.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/moment.min.js');

        $data['reservation_id'] = $id;
        $data['enable_edit_rate'] = true;

        $valid = true;
        if($id > 0){
            $qry = $this->db->get_where('view_cs_reservation', array('reservation_id' => $id));
            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                $data['reservation_type'] = $data['row']->reservation_type;

                //SET ENABLE CHANGE RATES OR NOT
                if($data['row']->payment_amount > 0 || $data['row']->deposit_amount > 0){
                    $data['enable_edit_rate'] = false;
                }

                if($data['row']->tenant_id > 0){
                    $tenant = $this->db->get_where('ms_tenant', array('tenant_id' => $data['row']->tenant_id));
                    $data['tenant'] = $tenant->row();

                }else{
                    $tenant = $this->db->get_where('tmp_tenant', array('reservation_id' => $id));
                    $data['tenant'] = $tenant->row();
                }

                if($data['row']->company_id > 0){
                    $company = $this->db->get_where('ms_company', array('company_id' => $data['row']->company_id ));
                    $data['company'] = $company->row();
                }

                if($data['row']->agent_id > 0){
                    $agent_qry = $this->db->get_where('ms_agent', array('agent_id' => $data['row']->agent_id));
                    if($agent_qry->num_rows() > 0){
                        $data['agent'] = $agent_qry->row();
                    }
                }

                $qry = $this->db->query('select distinct cs_reservation_detail.unit_id, ms_unit.unit_code, ms_unit.unittype_id, ms_unit_type.unittype_desc, ms_unit_type.unittype_max_adult, ms_unit_type.unittype_max_child, cs_reservation_header.reservation_type
                                     from cs_reservation_detail
                                     join cs_reservation_header on cs_reservation_header.reservation_id = cs_reservation_detail.reservation_id
                                     join ms_unit on ms_unit.unit_id = cs_reservation_detail.unit_id
                                     join ms_unit_type on ms_unit_type.unittype_id = ms_unit.unittype_id
                                     where cs_reservation_detail.reservation_id = ' . $id );

                $room_unit = array();
                if($qry->num_rows() > 0){
                    foreach($qry->result_array() as $unit){
                        $reservation_type = $unit['reservation_type'];
                        $calc = $this->mdl_frontdesk->calculate_booking($unit['unittype_id'], dmy_from_db($data['row']->arrival_date), dmy_from_db($data['row']->departure_date), $reservation_type, $data['row']->is_rate_yearly, $data['row']->billing_type);

                        //Get Discount
                        $discount_per_unit = 0;
                        $discount = $this->db->query('select ISNULL(discount,0) as discount from cs_reservation_unit
                                              where reservation_id = ' . $id . ' and unit_id = ' . $unit['unit_id']);
                        if($discount->num_rows() > 0){
                            $discount_per_unit = $discount->row()->discount;
                        }
                        array_push($room_unit, array('unit_id'=> $unit['unit_id'],'unit_code'=>$unit['unit_code'],'unittype_desc' => $unit['unittype_desc'],'checkin_date'=> dmy_from_db($data['row']->arrival_date), 'checkout_date' => dmy_from_db($data['row']->departure_date), 'formula' => $calc['formula'], 'local_amount' => $calc['total_amount'] ,'tax_rate' => $calc['tax_rate'], 'max_adult' => $unit['unittype_max_adult'], 'max_child' => $unit['unittype_max_child'], 'daily_count' => $calc['daily_count'], 'period_caption' => $calc['period_caption'], 'discount' => $discount_per_unit));

                        $totalMonth = ($calc['yearly_count'] * 12) + $calc['monthly_count'];
                        $data['num_month'] = $totalMonth;
                    }
                }
                $data['room_unit'] = $room_unit;

                if($data['reservation_type'] == RES_TYPE::HOUSE_USE){
                    $data['enable_discount'] = false;
                }else{
                     $data['enable_discount'] = true;
                }

            }else{
                $valid = false;
            }
        }else{
            $valid = false;
        }

        if($valid){
            $data['back_url'] = base_url('frontdesk/reservation/reservation_manage/1.tpd');
            if($ref_page == 2){
                $data['back_url'] = base_url('frontdesk/reservation/online_manage.tpd');
            }

            $this->load->view('layout/header', $data_header);
            $this->load->view('frontdesk/reservation/reserve_form', $data);
            $this->load->view('layout/footer');
        }else{
            tpd_404();
        }
    }

    public function picture_submit($tenant_id = ''){
        $im = imagecreatefrompng($_POST['image']);

        $filename = "assets/img/tenant/" . $tenant_id . ".jpg";
        imagejpeg($im,$filename,100);
    }

    public function picture_submit_upload($tenant_id = '') {
        $result = array(
            'error'     => '0',
            'message'   => '',
            'debug'     => ''
        );

        if(isset($_FILES["upload_photo"]["type"]))
        {
            $validextensions = array("jpeg", "jpg", "png");
            $temporary = explode(".", $_FILES["upload_photo"]["name"]);
            $file_extension = end($temporary);
            if ((($_FILES["upload_photo"]["type"] == "image/png") || ($_FILES["upload_photo"]["type"] == "image/jpg") || ($_FILES["upload_photo"]["type"] == "image/jpeg")
                ) && in_array($file_extension, $validextensions)) {
                if ($_FILES["upload_photo"]["size"] < 500000) {
                    if ($_FILES["upload_photo"]["error"] > 0) {
                        $result['error'] = '1';
                        $result['message'] = 'Max file size 100kb.';
                    } else {
                        if (file_exists(FCPATH . "assets/img/tenant/" . $tenant_id . ".jpg")) {
                            unlink(FCPATH . "assets/img/tenant/" . $tenant_id . ".jpg");
                        }

                        $uploadedfile = $_FILES['upload_photo']['tmp_name'];

                        if ($_FILES["upload_photo"]["type"] == "image/jpeg" || $_FILES["upload_photo"]["type"] == "image/jpg"){
                            $src = imagecreatefromjpeg($uploadedfile);
                        } else if ($_FILES["upload_photo"]["type"] == "image/png"){
                            $src = imagecreatefrompng($uploadedfile);
                        }

                        list($width,$height)=getimagesize($uploadedfile);

                        $newwidth=320;
                        $newheight=($height/$width)*$newwidth;
                        $tmp=imagecreatetruecolor($newwidth,$newheight);

                        imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);

                        $filename = FCPATH . "assets/img/tenant/" . $tenant_id . ".jpg";

                        imagejpeg($tmp,$filename,100);

                        imagedestroy($src);
                        imagedestroy($tmp);

                        $resul['debug'] = 'berhasil';
                    }
                } else {
                    $result['error'] = '1';
                    $result['message'] = 'Max file size 100kb.';
                }
            }
            else
            {
                $result['error'] = '1';
                $result['message'] = 'File not allowed. Please select valid format. (.jpg, .png)';
            }
        } else {
            $result['error'] = '1';
            $result['message'] = 'Please select file.';
        }

        echo json_encode($result);
    }

    public function picture_submit_upload_member($picture_name = '') {
        $result = array(
            'error'     => '0',
            'message'   => '',
            'debug'     => ''
        );

        if(isset($_FILES["upload_photo_member"]["type"]))
        {
            $validextensions = array("jpeg", "jpg", "png");
            $temporary = explode(".", $_FILES["upload_photo_member"]["name"]);
            $file_extension = end($temporary);
            if ((($_FILES["upload_photo_member"]["type"] == "image/png") || ($_FILES["upload_photo_member"]["type"] == "image/jpg") || ($_FILES["upload_photo_member"]["type"] == "image/jpeg")
                ) && in_array($file_extension, $validextensions)) {
                if ($_FILES["upload_photo_member"]["size"] < 500000) {
                    if ($_FILES["upload_photo_member"]["error"] > 0) {
                        $result['error'] = '1';
                        $result['message'] = 'Max file size 100kb.';
                    } else {
                        if (file_exists(FCPATH . "assets/img/tenant/" . $picture_name . ".jpg")) {
                            unlink(FCPATH . "assets/img/tenant/" . $picture_name . ".jpg");
                        }

                        $uploadedfile = $_FILES['upload_photo_member']['tmp_name'];

                        if ($_FILES["upload_photo_member"]["type"] == "image/jpeg" || $_FILES["upload_photo_member"]["type"] == "image/jpg"){
                            $src = imagecreatefromjpeg($uploadedfile);
                        } else if ($_FILES["upload_photo_member"]["type"] == "image/png"){
                            $src = imagecreatefrompng($uploadedfile);
                        }

                        list($width,$height)=getimagesize($uploadedfile);

                        $newwidth=320;
                        $newheight=($height/$width)*$newwidth;
                        $tmp=imagecreatetruecolor($newwidth,$newheight);

                        imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);

                        $filename = FCPATH . "assets/img/tenant/" . $picture_name . ".jpg";

                        imagejpeg($tmp,$filename,100);

                        imagedestroy($src);
                        imagedestroy($tmp);

                        $resul['debug'] = 'berhasil';
                    }
                } else {
                    $result['error'] = '1';
                    $result['message'] = 'Max file size 100kb.';
                }
            }
            else
            {
                $result['error'] = '1';
                $result['message'] = 'File not allowed. Please select valid format. (.jpg, .png)';
            }
        } else {
            $result['error'] = '1';
            $result['message'] = 'Please select file.';
        }

        echo json_encode($result);
    }

    public function check_picture_exist($tenant_id = 0) {
        $result = '0';
        if ($tenant_id > 0) {
            if (file_exists(FCPATH."assets/img/tenant/" . $tenant_id . ".jpg")) {
                $result = '1';
            }
        }

        echo $result;
    }

    public function delete_reservation_room(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        $reservationId = isset($_POST['reservation_id']) ? $_POST['reservation_id'] : 0;
        $unitId = isset($_POST['unit_id']) ? $_POST['unit_id'] : 0;

        if($reservationId > 0 && $unitId > 0){
            $this->db->delete('cs_reservation_detail', array('reservation_id' => $reservationId,
                                                              'unit_id' => $unitId));

            $result['type'] = '1';
            $result['message'] = 'Successfully delete record.';
        }

        echo json_encode($result);
    }

    public function submit_reservation(){
        $isUnitAvailable = true;
        $valid = true;

        if(isset($_POST)){
            $reservationId = isset($_POST['reservation_id']) ? $_POST['reservation_id'] : 0;

            if($reservationId <= 0){
                //CHECK IF UNIT ALREADY RESERVED
                $isUnitAvailable = $this->isUnitsValidForSubmit();
            }

            $reff = '';
            if($isUnitAvailable){
                //BEGIN TRANSACTION
                $this->db->trans_begin();

                $server_date = date('Y-m-d H:i:s');
                if($reservationId <= 0){
                    $data['reservation_date'] = $server_date;
                    $data['currencytype_id'] = 1;
                    $data['rate'] = 1;
                    $data['amount'] = 0;
                    $data['local_amount'] = 0;
                }else{
                    $data['reservation_date'] = dmy_to_ymd($_POST['reservation_date']);
                }

                $data['agent_id'] = isset($_POST['agent_id']) ? $_POST['agent_id'] : 0;
                $data['reservation_type'] = isset($_POST['reservation_type']) ? $_POST['reservation_type'] : 0;
                $data['company_id'] = isset($_POST['company_id']) ? $_POST['company_id'] : 0;
                $data['qty_adult'] = $_POST['num_adult'];
                $data['qty_child'] = $_POST['num_child'];
                $data['remark'] =  $_POST['remark'];
                $data['hidden_me'] = isset($_POST['hidden_me']) ? $_POST['hidden_me'] : 0;
                $data['guest_type'] = isset($_POST['guest_type']) ? $_POST['guest_type'] : 0;
                $data['billing_type'] = isset($_POST['billing_type']) ? BILLING_TYPE::FULL_PAID : BILLING_TYPE::MONTHLY;
                $data['is_rate_yearly'] = isset($_POST['is_rate_yearly']) ? $_POST['is_rate_yearly'] : 0;
                $data['is_walkin'] = 0;

                if($data['reservation_type'] == RES_TYPE::PERSONAL){
                    $data['company_id'] = 0;
                }

                unset($_POST['reservation_id']);

                //Tenant
                $tenantId = $this->saveOrUpdateTenant();

                if($tenantId > 0){
                    $data['tenant_id'] = $tenantId;

                    if($reservationId > 0){
                        $qry = $this->db->get_where('view_cs_reservation', array('reservation_id' => $reservationId));
                        $row = $qry->row();

                        $arr_date = explode('-', $data['reservation_date']);
                        $arr_date_old = explode('-', ymd_from_db($row->reservation_date));

                        if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                            //DELETE OLD NUMBER
                            $data['reservation_code'] = $this->mdl_general->generate_code(Feature::FEATURE_CS_RESERVATION, $data['reservation_date']);

                            if($data['reservation_code'] == ''){
                                $valid = false;

                                $this->session->set_flashdata('flash_message_class', 'danger');
                                $this->session->set_flashdata('flash_message', 'Failed generating code.');
                            }
                        }

                        //Set Online Valid -> Booking
                        if($row->status == ORDER_STATUS::ONLINE_VALID && $row->payment_amount > 0){
                            $data['status'] = ORDER_STATUS::RESERVED;
                        }

                        if($row->status == ORDER_STATUS::ONLINE_NEW){
                            $reff = '/2';
                        }

                        if($valid){
                            $data['modified_by'] = my_sess('user_id');
                            $data['modified_date'] = $server_date;

                            $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $reservationId), $data);

                            //echo '<br>step 3 update';

                            //Update details
                            if($valid){
                                //echo '<br>step 4 update';
                                $rsv['reservation_id'] = $row->reservation_id;
                                $rsv['reservation_type'] = $data['reservation_type'];
                                $rsv['agent_id'] = $data['agent_id'];
                                $rsv['billing_type'] = $data['billing_type'];
                                $rsv['is_rate_yearly'] = $data['is_rate_yearly'];

                                $valid = $this->insertDetailEntries($rsv);

                                //echo '<br>step 5 update';
                                $this->session->set_flashdata('flash_message_class', 'success');
                                $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                            }
                        }
                    }else {
                        $data['reservation_code'] = $this->mdl_general->generate_code(Feature::FEATURE_CS_RESERVATION, $data['reservation_date']);
                        $data['status'] = ORDER_STATUS::RESERVED;
                        $data['created_by'] = my_sess('user_id');
                        $data['created_date'] = $server_date;
                        $data['modified_by'] = 0;
                        $data['modified_date'] = $server_date;
                        $data['is_frontdesk'] = 1;

                        //echo 'no id : ' . $data['journal_no'];
                        if($data['reservation_code'] != ''){
                            $this->db->insert('cs_reservation_header', $data);
                            $reservationId = $this->db->insert_id();

                            if($reservationId > 0){
                                $data['reservation_id'] = $reservationId;
                                $valid = $this->insertDetailEntries($data);

                                if($valid){
                                    $this->session->set_flashdata('flash_message_class', 'success');
                                    $this->session->set_flashdata('flash_message', 'Transaction successfully created.');
                                }
                            }else{
                                $valid = false;
                            }
                        }else{
                            $valid = false;
                        }

                        if($valid){
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = 'CREATE Reservation ' . $data['reservation_code'];
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $reservationId;
                            $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION;

                        }
                    }

                    //COMMIT OR ROLLBACK
                    if($valid){
                        if ($this->db->trans_status() === FALSE)
                        {
                            $this->db->trans_rollback();

                            $this->session->set_flashdata('flash_message_class', 'danger');
                            $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                        }
                        else
                        {
                            $this->db->trans_commit();
                        }
                    }else{
                        $this->db->trans_rollback();

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                    }

                    //FINALIZE
                    if(!$valid){
                        redirect(base_url('frontdesk/reservation/reservation_form/' . $reservationId . $reff . '.tpd'));
                    }
                    else {
                        if(isset($_POST['save_close'])){
                            redirect(base_url('frontdesk/reservation/reservation_manage/1.tpd'),true);
                        }
                        else {
                            redirect(base_url('frontdesk/reservation/reservation_form/' . $reservationId . $reff . '.tpd'));
                        }
                    }
                }
            }else{
                redirect(base_url('frontdesk/reservation/find_room.tpd'));
            }
        }else{
            redirect(base_url('frontdesk/reservation/find_room.tpd'));
        }
    }

    public function isUnitsValidForSubmit(){
        $valid = true;

        if(isset($_POST)){
            $unit_ids = isset($_POST['unit_id']) ? $_POST['unit_id'] : array();
            $unit_codes = isset($_POST['unit_code']) ? $_POST['unit_code'] : array();
            $arrival_dates = isset($_POST['arrival_date']) ? $_POST['arrival_date'] : array();
            $departure_dates = isset($_POST['departure_date']) ? $_POST['departure_date'] : array();

            if(count($unit_ids) > 0){
                for ($i = 0; $i < count($unit_ids); $i++) {
                    if($valid){
                        $result = $this->db->get_where("fxnunit_ready_by_id('" . dmy_to_ymd($arrival_dates[$i]) . "','". dmy_to_ymd($departure_dates[$i]) ."'," . $unit_ids[$i] . ")");
                        if($result->num_rows() <= 0){
                            $this->session->set_flashdata('flash_message_class', 'danger');
                            $this->session->set_flashdata('flash_message', 'Room ' . $unit_codes[$i] .' no longer available! Please find another room.');

                            $valid = false;
                            break;
                        }
                    }else{
                        break;
                    }
                }
            }else{
                $valid = false;
            }
        }else{
            $valid = false;
        }

        return $valid;
    }

    public function xIsUnitsValidForSubmit(){
        $result = array();
        //Used to display notification
        $result['valid'] = '0';

        if(isset($_POST)){
            try{
                $unit_id = isset($_POST['unit_id']) ? $_POST['unit_id'] : 0;
                $arrival_date = isset($_POST['arrival_date']) ? $_POST['arrival_date'] : '';
                $departure_date = isset($_POST['departure_date']) ? $_POST['departure_date'] : '';

                if($unit_id > 0 && $arrival_date != '' && $departure_date != ''){
                    $qry = $this->db->query("SELECT * FROM fxnunit_ready_by_id('" . dmy_to_ymd($arrival_date) . "','". dmy_to_ymd($departure_date) ."'," . $unit_id . ")");
                    if($qry->num_rows() > 0){
                        //$this->session->set_flashdata('flash_message_class', 'danger');
                        //$this->session->set_flashdata('flash_message', 'Selected room no longer available! Please find another room.');
                        $result['valid'] = '1';
                    }
                    //echo $this->db->last_query();
                }
            }catch(Exception $e){
                $result['valid'] = '0';
            }
        }

        echo json_encode($result);
    }

    private function insertDetailEntries($rsv = array()){
        $valid = true;

        if(count($rsv) > 0 && isset($_POST)){
            $this->load->model('frontdesk/mdl_frontdesk');

            $reservationId = $rsv['reservation_id'];
            $isRateYearly = $rsv['is_rate_yearly'];
            $reservationType = $rsv['reservation_type'];
            $billingType = $rsv['billing_type'];

            $unit_ids = isset($_POST['unit_id']) ? $_POST['unit_id'] : array();
            $arrival_dates = isset($_POST['arrival_date']) ? $_POST['arrival_date'] : array();
            $departure_dates = isset($_POST['departure_date']) ? $_POST['departure_date'] : array();
            $local_amounts = isset($_POST['local_amount']) ? $_POST['local_amount'] : array();
            $tax_amounts = isset($_POST['tax_amount']) ? $_POST['tax_amount'] : array();
            $discounts = isset($_POST['discount_amount']) ? $_POST['discount_amount'] : array();

            $subtotal = 0;
            $minReceiptAmount = 0;
            $minDepositAmount = 0;
            if(count($unit_ids) > 0){
                //echo '<br>Count detail ' . count($unit_ids);
                $max = 0;
                for ($i = 0; $i < count($unit_ids); $i++) {
                    if($valid){
                        if(isset($unit_ids[$i]) && isset($arrival_dates[$i]) && isset($departure_dates[$i]) && isset($tax_amounts[$i]) && isset($discounts[$i])){
                            //echo '<br>Arrival ' .$arrival_dates[$i] . ' Departure ' . $departure_dates[$i];
                            $startDate = DateTime::createFromFormat('d-m-Y', $arrival_dates[$i]); //->format('Y-m-d');
                            $endDate = DateTime::createFromFormat('d-m-Y', $departure_dates[$i]); //->format('Y-m-d');
                            //echo '<br>Arrival ' .$startDate->format('Y-m-d') . ' Departure ' . $endDate->format('Y-m-d');
                            $diff_day = date_diff($startDate, $endDate, true);
                            $max = $diff_day->format('%a');

                            //echo '<br>unit : ' . $unit_ids[$i] . ' = ' . $max;
                            $max++;
                            $discount_per_day = round($discounts[$i] / ($max),0);

                            for($y=0;$y<$max;$y++){
                                if($y>0){
                                    $startDate = $startDate->modify('+1 day');
                                }

                                $checkQry = $this->db->query("select unit_id from cs_reservation_detail
                                                              where reservation_id = " . $reservationId . " and
                                                              unit_id = " . $unit_ids[$i] . " and CONVERT(date,checkin_date) = '" . $startDate->format('Y-m-d') ."' ");

                                if($checkQry->num_rows() <= 0){
                                    $detail['reservation_id'] = $reservationId;
                                    $detail['unit_id'] = $unit_ids[$i];
                                    $detail['checkin_date'] = $startDate->format('Y-m-d');
                                    $detail['discount'] = $discount_per_day;
                                    $detail['status'] = STATUS_NEW;
                                    $detail['close_status'] = 0;
                                    $detail['hsk'] = 0;

                                    $this->db->insert('cs_reservation_detail', $detail);
                                    $insertID = $this->db->insert_id();

                                    if($insertID <= 0){
                                        $valid = false;
                                    }
                                }else{
                                    $detail['discount'] = $discount_per_day;
                                    $this->mdl_general->update('cs_reservation_detail', array('reservation_id' => $reservationId, 'unit_id' => $unit_ids[$i]), $detail);
                                }
                            }

                            //DELETE IF HAS ANY DATE BIGGER than last night
                            $deleted = $this->db->query("DELETE FROM cs_reservation_detail
                                                              WHERE reservation_id = " . $reservationId . " and
                                                              unit_id = " . $unit_ids[$i] . " and CONVERT(date,checkin_date) > '" . $endDate->format('Y-m-d') ."' ");

                            $subtotal += round(($local_amounts[$i] - $discounts[$i]) + $tax_amounts[$i], 0);

                            //Save Reservation Unit
                            $reserveUnit = $this->db->query("select reservation_unit_id from cs_reservation_unit
                                                              where reservation_id = " . $reservationId . " and
                                                              unit_id = " . $unit_ids[$i]);

                            $resunit['discount'] = $discounts[$i];
                            $resunit['arrival_date'] = dmy_to_ymd($arrival_dates[0]);
                            $resunit['departure_date'] = dmy_to_ymd($departure_dates[count($unit_ids)-1]);

                            if($reserveUnit->num_rows() > 0){
                                $this->mdl_general->update('cs_reservation_unit', array('reservation_id' => $reservationId, 'unit_id' => $unit_ids[$i]), $resunit);
                            }else{
                                $resunit['reservation_id'] = $reservationId;
                                $resunit['unit_id'] = $unit_ids[$i];
                                $resunit['status'] = STATUS_NEW;

                                $this->db->insert('cs_reservation_unit', $resunit);
                                $insertID = $this->db->insert_id();

                                if($insertID <= 0){
                                    $valid = false;
                                }
                            }

                            //Calculate 1 day charge for minimum receipt amount
                            $firstMonthCharge = 0;
                            $units = $this->db->get_where('ms_unit', array('unit_id'=> $unit_ids[$i]));
                            if($units->num_rows() > 0){
                                $unit = $units->row();

                                $until_date = DateTime::createFromFormat('d-m-Y', $arrival_dates[0]);
                                $until_date->modify('+1 day');

                                $calc = $this->mdl_frontdesk->calculate_booking($unit->unittype_id, $arrival_dates[0], $departure_dates[0], $reservationType, $isRateYearly, $billingType);

                                $nMonth = ($calc['yearly_count'] * 12) + $calc['monthly_count'];
                                $discount_per_month = round($discounts[$i] / $nMonth,0);
                                if($rsv['billing_type'] == BILLING_TYPE::FULL_PAID){
                                    $base_amount = ($calc['min_receipt_amount'] - $discounts[$i]);
                                }else{
                                    $base_amount = ($calc['min_receipt_amount'] - $discount_per_month);
                                }

                                $tax = $base_amount * $calc['tax_rate'];
                                $minReceiptAmount = $base_amount + $tax;
                                //echo 'min_deposit A ' . $minDepositAmount . ' & ' . $minReceiptAmount;

                                //Deposit
                                $minDepositAmount = $calc['min_deposit_amount'];
                                if($nMonth >= 6){
                                    if($rsv['billing_type'] == BILLING_TYPE::FULL_PAID){
                                        $monthly_rate = round($calc['min_receipt_amount'] / $nMonth,0);
                                    }else{
                                        $monthly_rate = $calc['min_receipt_amount'];
                                    }
                                    $base_amount = ($monthly_rate - $discount_per_month);
                                    $tax = $base_amount * $calc['tax_rate'];
                                    $minDepositAmount = $base_amount + $tax;
                                }
                            }
                        }

                    }else{
                        break;
                    }
                }

                if($valid){
                    if(count($unit_ids) > 0){
                        //update header arrival_date & departure_date
                        $header['arrival_date'] = dmy_to_ymd($arrival_dates[0]);
                        $header['departure_date'] = dmy_to_ymd($departure_dates[count($unit_ids)-1]);
                        //$header['rate'] = 1;
                        if(isset($rsv['rate'])){
                            if($rsv['rate'] == 1){
                                $header['amount'] = $subtotal;
                            }
                        }
                        $header['local_amount'] = $subtotal;
                        $header['min_receipt_amount'] = $minReceiptAmount;
                        $header['min_deposit_amount'] = round($minDepositAmount,0);

                        $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $reservationId), $header);
                    }
                }

            }
        }

        return $valid;
    }

    private function saveOrUpdateTenant(){
        $tenantId = 0;

        if(isset($_POST)){
            $server_date = date('Y-m-d');

            $empty_date = DateTime::createFromFormat('Y-m-d', '1900-01-01');
            $empty_date =  $empty_date->format('Y-m-d');

            $tenantId = $_POST['tenant_id'];
            $tenant['tenant_salutation'] = $_POST['tenant_salutation'];
            $tenant['tenant_fullname'] = $_POST['tenant_name'];
            $tenant['tenant_type'] = isset($_POST['tenant_type']) ? $_POST['tenant_type'] : 0;
            $tenant['tenant_phone'] = $_POST['tenant_phone'];
            $tenant['tenant_cellular'] = $_POST['tenant_cellular'];
            $tenant['tenant_email'] = $_POST['tenant_email'];
            $tenant['tenant_sex'] = $_POST['tenant_sex'];
            $tenant['tenant_address'] = $_POST['tenant_address'];
            $tenant['tenant_city'] = $_POST['tenant_city'];
            $tenant['tenant_postalcode'] = $_POST['tenant_postalcode'];
            $tenant['tenant_country'] = $_POST['tenant_country'];
            $tenant['tenant_nationality'] = isset($_POST['tenant_nationality']) ? $_POST['tenant_nationality'] : '';
            $tenant['tenant_pob'] = $_POST['tenant_pob'];
            $tenant['tenant_dob'] = trim($_POST['tenant_dob']) != '' ? dmy_to_ymd($_POST['tenant_dob']) : '';
            $tenant['tenant_occupation'] = $_POST['tenant_occupation'];
            $tenant['id_type'] = $_POST['id_type'];
            $tenant['passport_no'] = $_POST['passport_no'];
            $tenant['passport_issueddate'] = trim($_POST['passport_issueddate']) != '' ? dmy_to_ymd($_POST['passport_issueddate']) : '';
            $tenant['passport_issuedplace'] = $_POST['passport_issuedplace'];
            $tenant['has_stampduty'] = 0;
            $tenant['has_taxtype'] = 1;

            if($tenantId <= 0){
                $tenant['tenant_account'] = $this->mdl_general->generateNewTenantCode();

                $tenant['created_by'] = my_sess('user_id');
                $tenant['created_date'] = $server_date;
                $tenant['modified_by'] = 0;
                $tenant['modified_date'] = $server_date;
                $tenant['status'] = STATUS_NEW;

                $this->db->insert('ms_tenant', $tenant);
                $tenantId = $this->db->insert_id();
            }else{
                $tenant['modified_by'] = my_sess('user_id');;
                $tenant['modified_date'] = $server_date;

                $this->mdl_general->update('ms_tenant', array('tenant_id' => $tenantId), $tenant);
            }
        }

        return $tenantId;
    }

    #endregion

    #region Manage Online Booking

    public function online_manage($type = 1){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('frontdesk/reservation/online_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_online_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();

        $where['view_cs_reservation.status'] = ORDER_STATUS::ONLINE_NEW;

        $whereString = ''; //'DATEDIFF(day,CONVERT(date,view_cs_reservation.reservation_date), CONVERT(date,GETDATE())) >= 1';

        $like = array();
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
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_type'])){
            if($_REQUEST['filter_type'] != ''){
                $like['view_cs_reservation.reservation_type'] = $_REQUEST['filter_type'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $like['view_cs_reservation.status'] = $_REQUEST['filter_status'];
            }
        }

        $joins = array(); //array('tmp_reservation_finish as tmp' => 'tmp.order_id = cs_reservation_header.reservation_code');
        $iTotalRecords = $this->mdl_finance->countJoin('view_cs_reservation', $joins, $where, $like, '', array(), $whereString);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'view_cs_reservation.reservation_code DESC';
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
                $order = 'view_cs_reservation.reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'view_cs_reservation.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $vt_status = ""; //", (Select Top 1 transaction_status From tmp_reservation_finish Where order_id = view_cs_reservation.reservation_code
                       //Order By created_date DESC, reservation_finish_id DESC) as transaction_status";
        $qry = $this->mdl_finance->getJoin('view_cs_reservation.*' . $vt_status,'view_cs_reservation', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $whereString);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';

            if($row->tenant_id <= 0){
                $btn_action .= '<li> <a href="' . base_url('frontdesk/reservation/reservation_form/' . $row->reservation_id) . '.tpd"><span class="bold"><i class="fa fa-smile-o font-red"></i>&nbsp;Pick Guest</span></a> </li>';
            }else{
                $btn_action .= '<li> <a href="' . base_url('frontdesk/reservation/reservation_form/' . $row->reservation_id . '/2') . '.tpd"><i class="fa fa-edit"></i>&nbsp;Open</a> </li>';
            }

            $status_caption = ORDER_STATUS::vt_status_caption($row->vt_status);
            if($row->status == ORDER_STATUS::ONLINE_NEW ){ //&& $row->veritrans_code != ''
                if($row->veritrans_code != ''){
                    if($row->veritrans_code != '200' && $row->veritrans_code != '201'){
                        $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->reservation_id . '"><i class="fa fa-remove"></i>&nbsp;' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                    }else{
                        if($row->veritrans_code == '201'){
                            $status_caption = str_replace('info','bg-yellow-casablanca',ORDER_STATUS::vt_status_caption($row->vt_status));
                        }
                    }
                }else{
                    if(trim($row->vt_status) == ''){
                        $startDate = DateTime::createFromFormat('Y-m-d', ymd_from_db($row->reservation_date));
                        $endDate = DateTime::createFromFormat('Y-m-d', date('Y-m-d')); //'2015-11-14'
                        $diff_day = date_diff($startDate, $endDate);
                        //$records['date_diff'] = 'Val = ' . $diff_day->format('%a');
                        if($diff_day->format('%a') > 1){
                            $status_caption = ORDER_STATUS::vt_status_caption('failed');
                            $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->reservation_id . '"><i class="fa fa-remove"></i>&nbsp;' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                        }
                    }
                }
            }

            //if(!SERVER_IS_PRODUCTION) {
                $btn_action .= '<li> <a href="javascript:;" class="btn-check-vt" data-id="' . $row->reservation_id . '" data-code="' . $row->reservation_code . '"  is-frontdesk="' . $row->is_frontdesk . '" ><i class="fa fa-bug"></i>&nbsp;' . 'Veritrans' . '</a> </li>';
            //}

            $res_caption = strtoupper(RES_TYPE::caption($row->reservation_type));
            if($row->reservation_type == RES_TYPE::CORPORATE){
                $res_caption = '<span class="font-green-seagreen bold">'. $res_caption . '</span>';
            }

            if($row->tenant_id > 0){

                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->reservation_id . '" name="ischecked[]"/>',
                    $row->reservation_code,
                    dmy_from_db($row->reservation_date),
                    $row->tenant_fullname,
                    $res_caption,
                    $row->room,
                    dmy_from_db($row->arrival_date),
                    dmy_from_db($row->departure_date),
                    $status_caption, //ORDER_STATUS::get_status_name($row->status),
                    $row->veritrans_code,
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            ' . $btn_action . '
                        </ul>
                    </div>'
                );
            }else{

                $records["data"][] = array(
                    '',
                    $row->reservation_code,
                    dmy_from_db($row->reservation_date),
                    $row->tenant_fullname,
                    $res_caption,
                    $row->room,
                    dmy_from_db($row->arrival_date),
                    dmy_from_db($row->departure_date),
                    $status_caption, //ORDER_STATUS::get_status_name($row->status),
                    $row->veritrans_code,
                    '<div class="btn-group">
                        <button class="btn yellow-casablanca btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            ' . $btn_action . '
                        </ul>
                    </div>'
                );
            }

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function posting_booking(){
        $valid = true;

        if(isset($_POST)){
            $this->load->model('finance/mdl_finance');

            if(isset($_POST['ischecked'])){
                $rowcount = 0;

                $qry = $this->db->get_where('ms_payment_type', array('payment_type' => PAYMENT_TYPE::PAYMENT_GATEWAY));
                if($qry->num_rows() > 0){
                    $paymenttype = $qry->row_array();

                    //Find Bank account by paymenttype_id
                    $bank = $this->db->query("SELECT fn_bank_account.bankaccount_id FROM fn_bank_account
                                                 JOIN gl_coa on gl_coa.coa_id = fn_bank_account.coa_id
                                                 JOIN ms_payment_type on ms_payment_type.coa_code = gl_coa.coa_code
                                                 WHERE fn_bank_account.status = " . STATUS_NEW . "
                                                       AND ms_payment_type.paymenttype_id = " .$paymenttype['paymenttype_id']);
                    if($bank->num_rows() > 0){
                        $bankaccount_id = $bank->row()->bankaccount_id;
                    }else{
                        $bankaccount_id = 0;
                    }

                    foreach($_POST['ischecked'] as $val){
                        $qryHeader = $this->mdl_general->get('cs_reservation_header', array('reservation_id' => $val));
                        if($qryHeader->num_rows() > 0){
                            $header = $qryHeader->row();
                            $reservation_code = $header->reservation_code;
                            //$status = $this->vt_booking_status($reservation_code);
                            /*
                            $status = new stdClass();
                            $status->status_code = '200';
                            $status->transaction_status = 'settlement';
                            $status->transaction_id = 0;
                            $status->gross_amount = 1500000;
                            $status->masked_card = '1234-5678';
                            $status->transaction_time = '2016-02-25 11:21:28';
                            */

                            $status = $this->booking_online_status($reservation_code);

                            if(isset($status)){
                                $data = array();
                                $data['veritrans_code']= $status->status_code;
                                $data['veritrans_transaction_status']= $status->transaction_status;
                                $data['is_frontdesk']= 0;

                                if($status->status_code == '200'){
                                    //transaction_status (authorize -> capture -> settlement | cancel | get order | approve challenge transactions)
                                    $transaction_status = $status->transaction_status;

                                    if($transaction_status == 'settlement'){ //Only if 200 && settled
                                        //BEGIN TRANSACTION
                                        $this->db->trans_begin();

                                        $data['tenant_id'] = $header->tenant_id;
                                        $data['veritrans_transaction_id'] = $status->transaction_id;
                                        if($data['tenant_id'] > 0){
                                            $data['status']= ORDER_STATUS::RESERVED;
                                        }else{
                                            $data['status']= ORDER_STATUS::ONLINE_VALID;
                                        }

                                        $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $val), $data);

                                        //CREATE BOOKING RECEIPT
                                        $rv['valid'] = false;
                                        if($header->tenant_id > 0) {
                                            $rv = $this->createOnlineBookingReceipt($header, $status, $paymenttype, $bankaccount_id);
                                        }
                                        //POSTING BOOKING RECEIPT
                                        if($rv['valid']){
                                            if ($this->db->trans_status() === FALSE)
                                            {
                                                $this->db->trans_rollback();
                                            }
                                            else
                                            {
                                                $this->db->trans_commit();
                                                $rowcount++;
                                            }
                                        }else{
                                            echo 'FAILED ...';
                                        }
                                    }else if($transaction_status == 'cancel'){
                                        $data['status']= STATUS_CANCEL;

                                        $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $val), $data);

                                        $detail['status'] = STATUS_CANCEL;
                                        $this->mdl_general->update('cs_reservation_detail', array('reservation_id' => $val), $detail);
                                        $this->mdl_general->update('cs_reservation_unit', array('reservation_id' => $val), $detail);
                                    }else
                                    {
                                        //$data['status']= STATUS_CANCEL;
                                        $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $val), $data);
                                        //$rowcount++;
                                    }
                                }elseif($status->status_code == '202' || $status->status_code == '407'){
                                    $data['status']= STATUS_CANCEL;

                                    $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $val), $data);

                                    $detail['status'] = STATUS_CANCEL;
                                    $this->mdl_general->update('cs_reservation_detail', array('reservation_id' => $val), $detail);
                                    $this->mdl_general->update('cs_reservation_unit', array('reservation_id' => $val), $detail);

                                    //$rowcount++;
                                }else{
                                    $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $val), $data);
                                    //$rowcount++;
                                }
                            }
                        }
                    }
                }else{
                    $this->session->set_flashdata('flash_message', 'Veritrans payment type not found.');
                    $this->session->set_flashdata('flash_message_class', 'warning');
                }
            }else{
                $this->session->set_flashdata('flash_message', 'No orders selected for posting.');
                $this->session->set_flashdata('flash_message_class', 'warning');
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if($rowcount > 0){
                    $this->session->set_flashdata('flash_message', $rowcount . ' online settlement(s) submitted.');
                    $this->session->set_flashdata('flash_message_class', 'success');
                }else{
                    $this->session->set_flashdata('flash_message', 'No Settlement orders found !');
                    $this->session->set_flashdata('flash_message_class', 'warning');
                }
            }else{
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be validated. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                redirect(base_url('frontdesk/reservation/online_manage/1.tpd'));
            }
            else {
                redirect(base_url('frontdesk/reservation/online_manage/1.tpd'));
            }
        }
    }

    private function createOnlineBookingReceipt($reservation, $booking_status, $paymenttype = array(), $bankaccount_id = 0){
        $result = array();
        $result['valid'] = true;
        $result['message'] ='';

        if(isset($reservation) && isset($booking_status) && count($paymenttype) > 0){
            try{
                $receiptDate = $booking_status->transaction_time;
                $desc = 'Online Booking ' . $reservation->reservation_code;
                $receiptAmount = doubleval($booking_status->gross_amount);
                $ccard_no = $booking_status->masked_card;
                $serverDate = date('Y-m-d H:i:s');
                $tenantID = $reservation->tenant_id;

                if($tenantID > 0){
                    //Insert RV
                    $rv = array();
                    $rv['receipt_date'] = $receiptDate;
                    $rv['created_by'] = my_sess('user_id');
                    $rv['created_date']  = date('Y-m-d H:i:s');
                    $rv['receipt_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_RECEIPT, $rv['receipt_date']);
                    $rv['status'] = STATUS_POSTED;
                    $rv['reservation_id'] = $reservation->reservation_id;

                    if($reservation->reservation_type == RES_TYPE::PERSONAL){
                        $rv['company_id'] = 0;
                        $rv['tenant_id'] = $tenantID;
                    }

                    $rv['deposit_id'] = 0;
                    $rv['is_invoice'] = 0;
                    $rv['paymenttype_id'] = $paymenttype['paymenttype_id'];
                    $rv['bankaccount_id'] = $bankaccount_id;
                    $rv['receipt_desc'] =  $desc;
                    $rv['receipt_bankcharges'] = 0;
                    $rv['receipt_paymentamount'] = $receiptAmount;

                    $rv['card_name'] = '';
                    $rv['card_no'] = $ccard_no;
                    $rv['card_expiry_month'] = '';
                    $rv['card_expiry_year'] = '';
                    $rv['card_bank'] = '';

                    $this->db->insert('ar_receipt', $rv);
                    $receiptID = $this->db->insert_id();

                    $rv['receipt_id'] = $receiptID;

                    if($receiptID > 0 && trim($rv['receipt_no']) != ''){
                        $rv['receipt_no'] = $rv['receipt_no'];
                        $valid = true;
                    }else{
                        $valid = false;
                    }

                    if($valid){
                        //POST a Journal
                        //Bank
                        // - AR
                        $this->load->library('../controllers/frontdesk/management');
                        $valid = $this->management->postingBookingReceipt($rv, $paymenttype);
                        $result['valid'] = $valid;
                        if(!$valid){
                            $result['message'] = 'Posting Official Receipt failed.';
                        }
                    }else{
                        $result['valid'] = false;
                        $result['message'] = 'Official Receipt can not be created.';
                    }
                }else{
                    $result['valid'] = false;
                    $result['message'] = 'Guest ID not found! Please register Guest to continue.';
                }

            }catch(Exception $e){
                $result['valid'] = false;
                $result['message'] = $e;
            }
        }else{
            $result['valid'] = false;
            $result['message'] = 'Reservation not found.';
        }

        return $result;
    }

    #endregion

    #region Manage Reservation

    public function reservation_manage($type = 1){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/util.js');

        $data = array();

        //GET CREDIT CARD
        $payments = $this->db->get_where('ms_payment_type',array('payment_type '=> PAYMENT_TYPE::CREDIT_CARD, 'status' => STATUS_NEW));
        if($payments->num_rows() > 0)
        {
            $data['payment_creditcard'] = $payments->row();
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('frontdesk/reservation/reservation_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_reservation_manage($menu_id = 0){
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

        $order = 'view_cs_reservation.reservation_code DESC';
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

        //$records["debug2"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';

            $btn_registration = '<li> <a href="' . base_url('frontdesk/reservation/pdf_reservation/' . $row->reservation_id) . '.tpd" target="_blank"><i class="fa fa-print"></i>&nbsp;Registration Form</a> </li>';
                    $btn_registration .= '<li> <a href="' . base_url('frontdesk/reservation/pdf_reservation_family/' . $row->reservation_id) . '.tpd" target="_blank"><i class="fa fa-print"></i>&nbsp;Family Registration</a> </li>';
                    $btn_registration .= '<li> <a href="' . base_url('frontdesk/reservation/pdf_reservation_staff/' . $row->reservation_id) . '.tpd" target="_blank"><i class="fa fa-print"></i>&nbsp;Staff Registration</a> </li>';

            if($row->status == ORDER_STATUS::RESERVED ){
                if(check_session_action($menu_id, STATUS_EDIT)){
                    $btn_action .= '<li> <a href="' . base_url('frontdesk/reservation/reservation_form/' . $row->reservation_id) . '.tpd"><i class="fa fa-pencil"></i>&nbsp;Edit</a> </li>';
                    if($row->reservation_type == RES_TYPE::PERSONAL || $row->reservation_type == RES_TYPE::MEMBER){
                        if($currentDate >= ymd_from_db($row->arrival_date)){
                            $btn_action .= '<li> <a href="javascript:;" class="btn-checkin bold" data-action="' . ORDER_STATUS::CHECKIN . '" data-id="' . $row->reservation_id . '"><i class="fa fa-sign-in font-green"></i>' . 'CHECK IN' . '</a> </li>';
                        }

                        $btn_action .= $btn_registration;

                        if($row->payment_amount <= 0){

                            //$btn_action .= '<li> <a href="javascript:;" class="btn-print-rv" data-id="' . $row->reservation_id . '">' . 'Print Receipt' . '</a> </li>';
                            //if(check_session_action($menu_id, STATUS_APPROVE)){
                            if($row->billing_type == BILLING_TYPE::FULL_PAID){
                                $btn_action .= '<li> <a href="javascript:;" class="btn-receipt bold" data-id="' . $row->reservation_id . '" min-date="' . dmy_from_db($row->reservation_date). '"  is-frontdesk="' . $row->is_frontdesk . '" data-min-amount="' . $row->min_receipt_amount . '" data-full-amount="' . ($row->local_amount - $row->payment_amount) . '"><i class="fa fa-money"></i>&nbsp;' . 'RECEIPT' . '</a> </li>';
                            }else{
                                $btn_action .= '<li> <a href="javascript:;" class="btn-receipt bold" data-id="' . $row->reservation_id . '" min-date="' . dmy_from_db($row->reservation_date). '"  is-frontdesk="' . $row->is_frontdesk . '" data-min-amount="' . $row->min_receipt_amount . '" data-full-amount="' . $row->min_receipt_amount . '"><i class="fa fa-money"></i>&nbsp;' . 'RECEIPT' . '</a> </li>';
                            }
                            //}

                        }

                        if($row->deposit_amount <= 0){
                            //if(check_session_action($menu_id, STATUS_APPROVE)){
                            $btn_action .= '<li> <a href="'. base_url('frontdesk/management/deposit_form/0/' . $row->reservation_id).'" class="btn-deposit bold" data-id="' . $row->reservation_id . '" min-date="' . dmy_from_db($row->reservation_date). '"  is-frontdesk="' . $row->is_frontdesk . '" data-min-amount="' . $row->min_deposit_amount . '" data-full-amount="' . ($row->deposit_amount) . '"><i class="fa fa-money"></i>&nbsp;' . 'DEPOSIT' . '</a> </li>';
                            //}
                        }

                        if($row->payment_amount <= 0 && $row->deposit_amount <= 0) {
                            $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->reservation_id . '"><i class="fa fa-remove"></i>&nbsp;' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                        }

                    }else if($row->reservation_type == RES_TYPE::CORPORATE) {
                        //CORPORATE
                        if($currentDate >= ymd_from_db($row->arrival_date)){
                            $btn_action .= '<li> <a href="javascript:;" class="btn-checkin bold" data-action="' . ORDER_STATUS::CHECKIN . '" data-id="' . $row->reservation_id . '"><i class="fa fa-sign-in font-green"></i>&nbsp;' . 'CHECK IN' . '</a> </li>';
                        }

                        $btn_action .= $btn_registration;

                        if ($row->payment_amount <= 0) {
                            //if(check_session_action($menu_id, STATUS_APPROVE)){
                            if($row->billing_type == BILLING_TYPE::FULL_PAID){
                                $btn_action .= '<li> <a href="javascript:;" class="btn-receipt bold" data-id="' . $row->reservation_id . '" min-date="' . dmy_from_db($row->reservation_date). '"  is-frontdesk="' . $row->is_frontdesk . '" data-min-amount="' . $row->min_receipt_amount . '" data-full-amount="' . ($row->local_amount - $row->payment_amount) . '"><i class="fa fa-money"></i>&nbsp;' . 'RECEIPT' . '</a> </li>';
                            }else{
                                $btn_action .= '<li> <a href="javascript:;" class="btn-receipt bold" data-id="' . $row->reservation_id . '" min-date="' . dmy_from_db($row->reservation_date). '"  is-frontdesk="' . $row->is_frontdesk . '" data-min-amount="' . $row->min_receipt_amount . '" data-full-amount="' . $row->min_receipt_amount . '"><i class="fa fa-money"></i>&nbsp;' . 'RECEIPT' . '</a> </li>';
                            }

                            //}
                        }

                        if($row->deposit_amount <= 0){
                            //if(check_session_action($menu_id, STATUS_APPROVE)){
                            $btn_action .= '<li> <a href="'. base_url('frontdesk/management/deposit_form/0/' . $row->reservation_id).'" class="btn-deposit bold" data-id="' . $row->reservation_id . '" min-date="' . dmy_from_db($row->reservation_date). '"  is-frontdesk="' . $row->is_frontdesk . '" data-min-amount="' . $row->min_deposit_amount . '" data-full-amount="' . ($row->deposit_amount) . '"><i class="fa fa-money"></i>&nbsp;' . 'DEPOSIT' . '</a> </li>';
                            //}
                        }

                        if($row->payment_amount <= 0 && $row->deposit_amount <= 0) {
                            $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->reservation_id . '"><i class="fa fa-remove"></i>&nbsp;' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                        }

                    }else{
                        //HOUSE USE
                        if($currentDate >= ymd_from_db($row->arrival_date)){
                            $btn_action .= '<li> <a href="javascript:;" class="btn-checkin bold" data-action="' . ORDER_STATUS::CHECKIN . '" data-id="' . $row->reservation_id . '"><i class="fa fa-sign-in font-green"></i>&nbsp;' . 'CHECK IN' . '</a> </li>';
                        }
                        $btn_action .= $btn_registration;
                        $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->reservation_id . '"><i class="fa fa-remove"></i>&nbsp;' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                    }
                }
                else {
                    $btn_action .= '<li> <a href="' . base_url('frontdesk/reservation/reservation_form/' . $row->reservation_id) . '.tpd">View</a> </li>';
                }
            }else if($row->status == ORDER_STATUS::ONLINE_VALID){
                if(check_session_action($menu_id, STATUS_EDIT)){
                    $btn_action .= '<li> <a href="' . base_url('frontdesk/reservation/reservation_form/' . $row->reservation_id) . '.tpd"><span class="bold"><i class="fa fa-smile-o font-red"></i>&nbsp;Pick Guest</span></a> </li>';

                }
                else {
                    $btn_action .= '<li> <a href="' . base_url('frontdesk/reservation/reservation_form/' . $row->reservation_id) . '.tpd">View</a> </li>';
                }
            }else if($row->status == STATUS_CANCEL){
                $btn_action .= $btn_registration;
                $btn_action .= '<li> <a href="' . base_url('frontdesk/reservation/reservation_form/' . $row->reservation_id) . '.tpd">View</a> </li>';
            }

            $res_caption = '<i class="' . RES_TYPE::css_icon($row->reservation_type) . ' tooltips" data-original-title="' . RES_TYPE::caption($row->reservation_type) . '"></i>'; //strtoupper(RES_TYPE::caption($row->reservation_type));
            //$res_caption = '<span class="' . RES_TYPE::css_class($row->reservation_type). '">'. $res_caption . '</span>';

            $status_caption = '<span class="badge ' . ($row->status == ORDER_STATUS::RESERVED ? 'badge-primary' : 'badge-inverse') . ' tooltips " data-original-title="' . ORDER_STATUS::get_status_name($row->status, false) . '">' . ORDER_STATUS::code($row->status) . '</span>'; //'<i class="' . ORDER_STATUS::css_icon($row->status) . ' tooltips" data-original-title="' . ORDER_STATUS::get_status_name($row->status, false) . '"></i>&nbsp;'

            $billing_caption = '<span class="tooltips " data-original-title="' . BILLING_TYPE::caption($row->billing_type) . '">' . ($row->billing_type == BILLING_TYPE::FULL_PAID ? '<i class="fa fa-gift font-green"></i>' : '<i class="fa fa-chain "></i>') . '</span>';

            $arrival_date = dmy_from_db($row->arrival_date);
            if($row->guest_type > 0){
                $arrival_date = '<span class="font-red">' .$arrival_date . '</span>';
            }

            $records["data"][] = array(
                $i,
                $row->reservation_code,
                dmy_from_db($row->reservation_date), //date('d/m/y', strtotime(ymd_from_db($row->reservation_date)))
                $row->tenant_fullname,
                $row->company_name,
                $res_caption,
                $row->room,
                $arrival_date,
                dmy_from_db($row->departure_date),
                $billing_caption,
                format_num($row->local_amount,0),
                //(isset($rv_init) ? '<a href="' . base_url('frontdesk/management/pdf_rv/' . $rv_init->bookingreceipt_id) . '.tpd" class="badge bg-green badge-roundless" target="_blank">' . $rv_init->receipt_no . '</a>' : ''),
                $row->payment_amount > 0 ? '<span >' . format_num($row->payment_amount,0) .'</span>' : 0,
                $row->deposit_amount > 0 ? '<span >' . format_num($row->deposit_amount,0) .'</span>' : 0,
                $status_caption, //ORDER_STATUS::get_status_name($row->status),
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
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function action_request(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $reservation_id = $_POST['reservation_id'];
        $data['status'] = $_POST['action'];
        $data['cancel_note'] = $_POST['reason'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Cancel Reservation';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $reservation_id;
        $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION;

        if($reservation_id > 0 && $data['status'] > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $qry = $this->db->get_where('cs_reservation_header', array('reservation_id' => $reservation_id));
            if($qry->num_rows() > 0){
                $row = $qry->row();
                if($data['status'] == STATUS_CANCEL){
                    if($row->status == STATUS_CANCEL){
                        $result['type'] = '0';
                        $result['message'] = 'Reservation already canceled.';
                    }
                    else {
                        $data['modified_by'] = my_sess('user_id');
                        $data['modified_date'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $reservation_id), $data);

                        $detail['status'] = STATUS_CANCEL;
                        $this->mdl_general->update('cs_reservation_detail', array('reservation_id' => $reservation_id), $detail);
                        $this->mdl_general->update('cs_reservation_unit', array('reservation_id' => $reservation_id), $detail);

                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['type'] = '1';
                        $result['message'] = 'Reservation successfully canceled.';
                    }
                }

                //FINALIZE TRANSACTION
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Reservation can not be processed.';
                }
                else
                {
                    $this->db->trans_commit();
                }
            }
        }

        echo json_encode($result);
    }

    public function action_capture(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        $reservation_id = $_POST['reservation_id'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = ' Capture Online Booking';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $reservation_id;
        $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION;

        if($reservation_id > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $valid = true;
            $qry = $this->db->get_where('cs_reservation_header', array('reservation_id' => $reservation_id));
            if($qry->num_rows() > 0){
                $row = $qry->row();

                //Veritrans Capture
                if($row->veritrans_transaction_id != ''){
                    $status = $this->booking_online_capture($row->veritrans_transaction_id, $row->local_amount);
                    $result['debug'] = $status;

                    //transaction_status (authorize -> capture -> settlement | cancel | get order | approve challenge transactions)
                    $transaction_status = $status->transaction_status;
                    if($status->status_code == '200'){
                        if($transaction_status == 'capture'){
                            $data['status']= ORDER_STATUS::RESERVED;

                            //Create tenant if still 0

                            $this->mdl_general->update('cs_reservation_header', array('reservation_id' => $reservation_id), $data);

                            $result['type'] = '1';
                            $result['message'] = 'Transaction Capture successful.';
                        }else{
                            $valid = false;
                        }
                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = false;
                }

                if($valid){
                    //Post Journal
                    //Credit
                    //  Sales

                }

                if($valid){
                    //FINALIZE TRANSACTION
                    if ($this->db->trans_status() === FALSE)
                    {
                        $this->db->trans_rollback();

                        $result['type'] = '0';
                        $result['message'] = 'Transaction Capture can not be processed.';
                    }
                    else
                    {
                        $this->db->trans_commit();
                    }
                }else{
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction Capture can not be confirmed.';
                }
            }
        }

        echo json_encode($result);
    }

    public function veritrans_status(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $reservation_code = $_POST['reservation_code'];

        if($reservation_code != ''){
            $status = $this->booking_online_status($reservation_code);
            $result['type'] = '1';
            $result['message'] = $status;
        }

        echo json_encode($result);
    }

    #endregion

    #region Folio Deposit

    public function folio_deposit($type = 1){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        $data['is_history'] = ($type == 2 ? true : false);
        $this->load->view('layout/header', $data_header);
        $this->load->view('frontdesk/management/deposit_manage.php', $data);
        $this->load->view('layout/footer');
    }

    #endregion

    #region Print

    public function pdf_reservation($reservation_id = 0) {
        if($reservation_id > 0){
            //Reservation
            $qry = $this->db->query('SELECT * FROM view_cs_reservation WHERE reservation_id = ' . $reservation_id);
            if($qry->num_rows() > 0) {
                $data['row'] = $qry->row_array();

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'RSV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                //Unit Type
                $unittype_caption = "";
                $unittypes =  $this->db->query("select distinct ms_unit_type.unittype_desc
                                    from ms_unit
                                    join ms_unit_type on ms_unit_type.unittype_id = ms_unit.unittype_id
                                    where ms_unit.unit_code in('" . $data['row']['room'] . "')");
                if($unittypes->num_rows() > 0){
                    for($i=0;$i<$unittypes->num_rows();$i++){
                        $row = $unittypes->row_array($i);
                        if($i == $unittypes->num_rows()-1){
                            $unittype_caption .= $row['unittype_desc'];
                        }else{
                            $unittype_caption .= $row['unittype_desc'] . '<br>';
                        }
                    }
                }

                $data['unittype_list'] = $unittype_caption;

                if($data['row']['tenant_id'] > 0){
                    $guest = $this->db->get_where('ms_tenant', array('tenant_id' => $data['row']['tenant_id']));
                    if($guest->num_rows() > 0){
                        $data['guest'] = $guest->row_array();
                    }
                }

                if($data['row']['company_id'] > 0){
                    $company = $this->db->get_where('ms_company', array('company_id' => $data['row']['company_id']));
                    if($company->num_rows() > 0){
                        $data['company'] = $company->row_array();
                    }
                }

                $this->load->view('frontdesk/reservation/pdf_reservation', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']['reservation_code'] . ".pdf", array('Attachment'=>0));

                //wkhtml_print();
            }
            else {
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    public function pdf_reservation_family($reservation_id = 0) {
        if($reservation_id > 0){
            //Reservation
            $qry = $this->db->query('SELECT * FROM view_cs_reservation WHERE reservation_id = ' . $reservation_id);
            if($qry->num_rows() > 0) {
                $data['row'] = $qry->row_array();

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'RSV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                //Unit Type
                $unittype_caption = "";
                $unittypes =  $this->db->query("select distinct ms_unit_type.unittype_desc
                                    from ms_unit
                                    join ms_unit_type on ms_unit_type.unittype_id = ms_unit.unittype_id
                                    where ms_unit.unit_code in('" . $data['row']['room'] . "')");
                if($unittypes->num_rows() > 0){
                    for($i=0;$i<$unittypes->num_rows();$i++){
                        $row = $unittypes->row_array($i);
                        if($i == $unittypes->num_rows()-1){
                            $unittype_caption .= $row['unittype_desc'];
                        }else{
                            $unittype_caption .= $row['unittype_desc'] . '<br>';
                        }
                    }
                }

                $data['unittype_list'] = $unittype_caption;

                if($data['row']['tenant_id'] > 0){
                    $guest = $this->db->get_where('ms_tenant', array('tenant_id' => $data['row']['tenant_id']));
                    if($guest->num_rows() > 0){
                        $data['guest'] = $guest->row_array();
                    }

                    $family = $this->db->get_where('ms_tenant_member', array('tenant_id' => $data['row']['tenant_id'], 'member_type' => GUEST_MEMBER_TYPE::FAMILY));
                    if($family->num_rows() > 0){
                        $data['families'] = $family->result_array();
                    }
                }

                if($data['row']['company_id'] > 0){
                    $company = $this->db->get_where('ms_company', array('company_id' => $data['row']['company_id']));
                    if($company->num_rows() > 0){
                        $data['company'] = $company->row_array();
                    }
                }

                $this->load->view('frontdesk/reservation/pdf_reservation_family', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']['reservation_code'] . ".pdf", array('Attachment'=>0));

                //wkhtml_print();
            }
            else {
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    public function pdf_reservation_staff($reservation_id = 0) {
        if($reservation_id > 0){
            //Reservation
            $qry = $this->db->query('SELECT * FROM view_cs_reservation WHERE reservation_id = ' . $reservation_id);
            if($qry->num_rows() > 0) {
                $data['row'] = $qry->row_array();

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'RSV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                //Unit Type
                $unittype_caption = "";
                $unittypes =  $this->db->query("select distinct ms_unit_type.unittype_desc
                                    from ms_unit
                                    join ms_unit_type on ms_unit_type.unittype_id = ms_unit.unittype_id
                                    where ms_unit.unit_code in('" . $data['row']['room'] . "')");
                if($unittypes->num_rows() > 0){
                    for($i=0;$i<$unittypes->num_rows();$i++){
                        $row = $unittypes->row_array($i);
                        if($i == $unittypes->num_rows()-1){
                            $unittype_caption .= $row['unittype_desc'];
                        }else{
                            $unittype_caption .= $row['unittype_desc'] . '<br>';
                        }
                    }
                }

                $data['unittype_list'] = $unittype_caption;

                if($data['row']['tenant_id'] > 0){
                    $guest = $this->db->get_where('ms_tenant', array('tenant_id' => $data['row']['tenant_id']));
                    if($guest->num_rows() > 0){
                        $data['guest'] = $guest->row_array();
                    }

                    $family = $this->db->get_where('ms_tenant_member', array('tenant_id' => $data['row']['tenant_id'], 'member_type' => GUEST_MEMBER_TYPE::STAFF));
                    if($family->num_rows() > 0){
                        $data['families'] = $family->result_array();
                    }
                }

                if($data['row']['company_id'] > 0){
                    $company = $this->db->get_where('ms_company', array('company_id' => $data['row']['company_id']));
                    if($company->num_rows() > 0){
                        $data['company'] = $company->row_array();
                    }
                }

                $this->load->view('frontdesk/reservation/pdf_reservation_staff', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']['reservation_code'] . ".pdf", array('Attachment'=>0));

                //wkhtml_print();
            }
            else {
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    #endregion


    #region Modal Lookup Form

    public function ajax_modal_tenant(){
        $this->load->view('frontdesk/reservation/ajax_modal_tenant');
    }

    public function get_modal_tenant($num_index = 0, $tenant_id = 0){
        $where = array();
        $like = array();

        $where['ms_tenant.status <>'] = STATUS_DELETE;

        if(isset($_REQUEST['filter_code'])){
            if($_REQUEST['filter_code'] != ''){
                $like['ms_tenant.tenant_account'] = $_REQUEST['filter_code'];
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ms_tenant.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_passport_no'])){
            if($_REQUEST['filter_passport_no'] != ''){
                $like['ms_tenant.passport_no'] = $_REQUEST['filter_passport_no'];
            }
        }
        if(isset($_REQUEST['filter_country'])){
            if($_REQUEST['filter_country'] != ''){
                $like['ms_tenant.tenant_country'] = $_REQUEST['filter_country'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('ms_tenant',$where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ms_tenant.tenant_fullname asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ms_tenant.tenant_account ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ms_tenant.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ms_tenant.passport_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ms_tenant.tenant_country ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('ms_tenant',$where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $text = "Select";
            $attr = '';
            $attr .= ' data-id="' . $row->tenant_id . '" ';
            $attr .= ' data-code="' . $row->tenant_account . '" ';
            $attr .= ' data-desc="' . $row->tenant_fullname . '" ';
            $attr .= ' id-type="' . $row->id_type . '" ';
            $attr .= ' passport-no="' . $row->passport_no . '" ';
            $attr .= ' passport-place="' . $row->passport_issuedplace . '" ';
            $attr .= ' passport-date="' . dmy_from_db($row->passport_issueddate) . '" ';
            $attr .= ' tenant-address="' . nl2br($row->tenant_address) . '" ';
            $attr .= ' tenant-city="' . $row->tenant_city . '" ';
            $attr .= ' tenant-country="' . $row->tenant_country . '" ';
            $attr .= ' tenant-nationality="' . $row->tenant_nationality . '" ';
            $attr .= ' tenant-postcode="' . $row->tenant_postalcode . '" ';
            $attr .= ' tenant-phone="' . $row->tenant_phone . '" ';
            $attr .= ' tenant-cellular="' . $row->tenant_cellular . '" ';
            $attr .= ' tenant-email="' . $row->tenant_email . '" ';
            $attr .= ' tenant-sex="' . $row->tenant_sex . '" ';
            $attr .= ' tenant-occupation="' . $row->tenant_occupation . '" ';
            $attr .= ' tenant-nationality="' . $row->tenant_nationality . '" ';
            $attr .= ' tenant-birthdate="' . dmy_from_db($row->tenant_dob) . '" ';
            $attr .= ' tenant-birthplace="' . $row->tenant_pob . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            if ($tenant_id == $row->tenant_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-tenant" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $row->tenant_account,
                $row->tenant_fullname,
                $row->passport_no,
                $row->tenant_country,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_company(){
        $this->load->view('frontdesk/reservation/ajax_modal_company');
    }

    public function get_modal_company($num_index = 0){
        $where = array();
        $like = array();

        $where['ms_company.status <>'] = STATUS_DELETE;

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ms_company.company_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_address'])){
            if($_REQUEST['filter_address'] != ''){
                $like['ms_company.company_address'] = $_REQUEST['filter_address'];
            }
        }
        if(isset($_REQUEST['filter_phone'])){
            if($_REQUEST['filter_phone'] != ''){
                $like['ms_company.company_phone'] = $_REQUEST['filter_phone'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('ms_company',$where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ms_company.company_name asc';

        $qry = $this->mdl_general->get('ms_company',$where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $attr = '';
            $attr .= ' data-id="' . $row->company_id . '" ';
            $attr .= ' data-name="' . $row->company_name . '" ';
            $attr .= ' data-addr="' . $row->company_address . '" ';
            $attr .= ' data-phone="' . $row->company_phone . '" ';
            $attr .= ' data-fax="' . $row->company_fax . '" ';
            $attr .= ' data-email="' . $row->company_email . '" ';
            $attr .= ' data-pic-name="' . $row->company_pic_name . '" ';
            $attr .= ' data-pic-phone="' . $row->company_pic_phone . '" ';
            $attr .= ' data-pic-email="' . $row->company_pic_email . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            $text = "Select";

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-company" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $i,
                $row->company_name,
                $row->company_address,
                $row->company_phone,
                $row->company_email,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_agent(){
        $this->load->view('frontdesk/reservation/ajax_modal_agent');
    }

    public function get_modal_agent($num_index = 0){
        $where = array();
        $like = array();

        $where['ms_agent.status <>'] = STATUS_DELETE;

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ms_agent.agent_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_pic'])){
            if($_REQUEST['filter_pic'] != ''){
                $like['CONVERT(varchar(MAX),ms_agent.agent_pic)'] = $_REQUEST['filter_pic'];
            }
        }

        if(isset($_REQUEST['filter_phone'])){
            if($_REQUEST['filter_phone'] != ''){
                $like['CONVERT(varchar(MAX),ms_agent.agent_phone)'] = $_REQUEST['filter_phone'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('ms_agent',$where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ms_agent.agent_name asc';

        $qry = $this->mdl_general->get('ms_agent',$where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $attr = '';
            $attr .= ' data-id="' . $row->agent_id . '" ';
            $attr .= ' data-name="' . $row->agent_name . '" ';
            $attr .= ' data-pic="' . $row->agent_pic . '" ';
            $attr .= ' data-phone="' . $row->agent_phone . '" ';
            $attr .= ' data-email="' . $row->agent_email . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            $text = "Select";

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-agent" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $i,
                $row->agent_name,
                $row->agent_pic,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_room(){
        $this->load->view('frontdesk/reservation/ajax_modal_change_room');
    }

    public function get_change_room($num_index = 0, $reservation_id = 0){
        $this->load->model('frontdesk/mdl_frontdesk');

        $room_unit = array();

        $iTotalRecords = 0;

        if($reservation_id > 0){
            $reservation = $this->db->get_where('cs_reservation_header', array('reservation_id' => $reservation_id));
            if($reservation->num_rows() > 0){
                $reservation = $reservation->row();

                $unit = $this->db->query('SELECT unit.unittype_id FROM cs_reservation_unit cs JOIN ms_unit unit ON cs.unit_id = unit.unit_id
                                  WHERE cs.reservation_id = ' . $reservation_id);
                if($unit->num_rows() > 0){
                    $unittype_id =  $unit->row()->unittype_id;

                    $arrival_date = date('d-m-Y');
                    $departure_date = dmy_from_db($reservation->departure_date);

                    $ready_units = $this->get_ready_room($arrival_date, $departure_date, $reservation->qty_adult, $reservation->qty_child, $unittype_id);

                    if(isset($ready_units)){
                        $reservation_type = $reservation->reservation_type;

                        foreach($ready_units as $unit){
                            //Get Discount
                            $discount_per_unit = 0;
                            $discount = $this->db->query('select ISNULL(discount,0) as discount from cs_reservation_detail
                                                  where reservation_id = ' . $reservation_id );
                            if($discount->num_rows() > 0){
                                $discount_per_unit = $discount->row()->discount;
                            }

                            $calc = $this->mdl_frontdesk->calculate_booking($unit['unittype_id'], $arrival_date, $departure_date, $reservation_type, $reservation->is_rate_yearly, $reservation->billing_type);

                            array_push($room_unit, array('unit_id'=> $unit['unit_id'],'unit_code'=>$unit['unit_code'],'unittype_desc' => $unit['unittype_desc'],'checkin_date'=> dmy_to_ymd($arrival_date), 'checkout_date' => dmy_to_ymd($departure_date), 'formula' => $calc['formula'], 'local_amount' => $calc['total_amount'] ,'tax_rate' => $calc['tax_rate'], 'daily_count' => $calc['period_caption'], 'discount' => $discount_per_unit));

                        }
                    }
                }
            }
        }

        $iTotalRecords = count($room_unit);

        //$iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        //$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $i = $iDisplayStart + 1;
        foreach($room_unit as $row){
            $discount = ($row['discount'] * $row['daily_count']);
            $tax = ($row['local_amount'] - $discount) * $row['tax_rate'];
            $subtotal = round((($row['local_amount'] - $discount) + $tax),0);

            $attr = '';
            $attr .= ' data-unit-id="' . $row['unit_id'] . '" ';
            $attr .= ' data-unit-code="' . $row['unit_code'] . '" ';
            $attr .= ' data-unit-type-desc="' . $row['unittype_desc'] . '" ';
            $attr .= ' data-arrival="' . $row['checkin_date'] . '" ';
            $attr .= ' data-departure="' . $row['checkout_date'] . '" ';
            $attr .= ' data-days="' . $row['daily_count'] . '" ';
            $attr .= ' data-local-amount="' . $row['local_amount'] . '" ';
            $attr .= ' data-discount-amount="' . ($row['discount'] * $row['daily_count']) . '" ';
            $attr .= ' data-tax-rate="' . $row['tax_rate'] . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            $text = "";

            $btn = '<button class="btn red-intense btn-xs btn-select-room" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $row['unit_code'],
                $row['unittype_desc'],
                //date('d/m/y', strtotime($row['checkin_date'])),
                //date('d/m/y', strtotime($row['checkout_date'])),
                //$row['daily_count'],
                //format_num($row['local_amount'],0),
                //format_num($discount,0),
                //format_num($tax,0),
                //format_num($subtotal,0),
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    #endregion

    #region Check Code Online

    public function vt_booking_status($reservation_code){
        $result = null;

        if($reservation_code != ''){
            try{
                $vts = $this->db->query("SELECT * FROM tmp_reservation_finish WHERE order_id = '" . $reservation_code ."'
                                         ORDER BY created_date DESC, reservation_finish_id DESC");
                if($vts->num_rows() > 0){
                    $result = $vts->row();
                }
            }catch (Exception $e){
            }
        }
        return $result;
    }

    public function booking_online_status($reservation_code){
        $result = null;

        if($reservation_code != ''){
            include_once(APPPATH . 'third_party/veritrans/Veritrans.php');

            try{
                Veritrans_Config::$serverKey = VERITRANS_SERVER_KEY;
                Veritrans_Config::$isProduction = VERITRANS_IS_PRODUCTION;

                $result = Veritrans_Transaction::status($reservation_code);

                //transaction status == 200 (OK)
            }catch (Exception $e){
            }
        }
        return $result;
    }

    public function booking_online_capture($veritrans_trans_id = 0, $reservation_amount = 0){
        $result = array();

        if($veritrans_trans_id > 0 && $reservation_amount > 0){
            include_once(APPPATH . 'third_party/veritrans/Veritrans.php');

            try{
                Veritrans_Config::$serverKey = VERITRANS_SERVER_KEY;
                Veritrans_Config::$isProduction = VERITRANS_IS_PRODUCTION;

                $result = Veritrans_VtDirect::capture($veritrans_trans_id, $reservation_amount);

                //transaction status == 200 (OK)
            }catch (Exception $e){
            }
        }
        return $result;
    }

    #endregion

}

/* End of file registration.php */
/* Location: ./application/controllers/frondesk/registration.php */