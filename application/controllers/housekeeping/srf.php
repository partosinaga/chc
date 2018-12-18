<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class srf extends CI_Controller {

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
		$this->srf_manage();
	}

    #region Service Request Form

    public function srf_manage(){
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

        $this->load->view('layout/header', $data_header);
        $this->load->view('housekeeping/srf/srf_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_srf_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();

        //$where['cs_srf_header.status'] = STATUS_NEW;
        $where_str = 'cs_srf_header.status IN(' . STATUS_NEW . ',' . STATUS_APPROVE . ')';

        $like = array();

        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['DATE(cs_srf_header.srf_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['DATE(cs_srf_header.srf_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['cs_srf_header.srf_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_unit'])){
            if($_REQUEST['filter_unit'] != ''){
                $like['ms_unit.unit_code'] = $_REQUEST['filter_unit'];
            }
        }

        if(isset($_REQUEST['filter_request_by'])){
            if($_REQUEST['filter_request_by'] != ''){
                $like['cs_srf_header.requested_by'] = $_REQUEST['filter_request_by'];
            }
        }

        $joins = array('ms_unit' => 'ms_unit.unit_id = cs_srf_header.unit_id');
        $iTotalRecords = $this->mdl_finance->countJoin('cs_srf_header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'cs_srf_header.status ASC  ,cs_srf_header.srf_id DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'cs_srf_header.srf_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'cs_srf_header.srf_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ms_unit.unit_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'cs_srf_header.requested_by ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('cs_srf_header.*,  case cs_srf_header.unit_id WHEN 0 then "Public" else ms_unit.unit_code end as unit_code'
            ,'cs_srf_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('housekeeping/srf/srf_form/' . $row->srf_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_NEW){
                $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->srf_id . '" data-code="' . $row->srf_no . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
            }else if($row->status == STATUS_APPROVE){
                $btn_action .= '<li> <a href="' . base_url('housekeeping/srf/pdf_srf/' . $row->srf_id . '.tpd') .  '" target="_blank"> Print</a> </li>';
                $btn_action .= '<li> <a href="javascript:;" class="btn-close" data-action="' . STATUS_CLOSED . '" data-id="' . $row->srf_id . '" data-code="' . $row->srf_no . '">' . get_action_name(STATUS_CLOSED, false) . '</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->srf_no,
                dmy_from_db($row->srf_date),
                $row->unit_code,
                $row->requested_by,
                SRF_TYPE::caption($row->srf_type),
                get_status_name($row->status),
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                        Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        $records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function srf_form($id = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        $data = array();

        //HEADER
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');

        $data['srf_id'] = $id;

        if($id > 0){
            $joins = array('ms_unit' => 'ms_unit.unit_id = cs_srf_header.unit_id');
            $qry = $this->mdl_finance->getJoin('cs_srf_header.*, ms_unit.unit_code','cs_srf_header', $joins, array('srf_id' => $id));
            $data['srf'] = $qry->row();

            $detail = array();
            $qry = $this->db->query('SELECT * FROM cs_srf_detail WHERE srf_id = ' . $data['srf_id'] . ' ORDER BY work_date ');
            if($qry->num_rows() > 0){
                foreach($qry->result() as $srf_det){
                    array_push($detail, dmy_from_db($srf_det->work_date));
                }
            }

            $result = '';
            if(count($detail) > 0){
                $result = implode('|', $detail);
            }

            $data['multi_date'] = $result;
        }


        $this->load->view('layout/header', $data_header);
        $this->load->view('housekeeping/srf/srf_form', $data);
        $this->load->view('layout/footer');
    }

    #region unit srf
    public function get_unit($srf_type = 0,$srf_id=0)
    { 
        if($srf_type > 0){
			if($srf_id > 0){
				if($srf->status == STATUS_CLOSED || $srf->status == STATUS_APPROVE) {
					$qry = $this->db->query("SELECT * FROM ms_unit
					  WHERE unit_id = " . $srf->unit_id);

					foreach ($qry->result_array() as $unit) {
						echo '<option value="' . $unit['unit_id'] . '" ' . ($srf_id > 0 ? $srf->unit_id == $unit['unit_id'] ? 'selected="selected"' : '' : '') . '>' . $unit['unit_code'] . '</option>';
					}
				}else{
					$qry = $this->db->query("SELECT * FROM ms_unit
					  WHERE hsk_status IN('" . HSK_STATUS::VD . "','" . HSK_STATUS::IS . "')
					  ORDER BY unit_code");
					foreach ($qry->result_array() as $unit) {
						echo '<option value="' . $unit['unit_id'] . '" ' . ($srf_id > 0 ? $srf->unit_id == $unit['unit_id'] ? 'selected="selected"' : '' : '') . '>' . $unit['unit_code'] . '</option>';
					}
				}
			}else{
				$qry = $this->db->query("SELECT * FROM ms_unit
					  WHERE hsk_status IN('" . HSK_STATUS::VD . "','" . HSK_STATUS::IS . "')
					  ORDER BY unit_code");
				foreach ($qry->result_array() as $unit) {
					echo '<option value="' . $unit['unit_id'] . '" ' . ($srf_id > 0 ? $srf->unit_id == $unit['unit_id'] ? 'selected="selected"' : '' : '') . '>' . $unit['unit_code'] . '</option>';
				}
			}
		}
		else
		{
			$qry = $this->db->query("SELECT * FROM ms_unit ORDER BY unit_code");
				foreach ($qry->result_array() as $unit) {
					echo '<option value="' . $unit['unit_id'] . '" ' . ($srf_id > 0 ? $srf->unit_id == $unit['unit_id'] ? 'selected="selected"' : '' : '') . '>' . $unit['unit_code'] . '</option>';
				} 
        }
		
		echo '<option value="0" ' . ($srf_id > 0 ? $srf->unit_id == $unit['unit_id'] ? 'selected="selected"' : '' : '') . '> Public Area </option>';
    }
    public function submit_srf(){
        $valid = true;

        if(isset($_POST)){
            $srfId = $_POST['srf_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['srf_date'] = dmy_to_ymd($_POST['srf_date']);
            $data['unit_id'] = $_POST['unit_id'];
            $data['is_booking_available'] = isset($_POST['is_booking_available']) ? $_POST['is_booking_available'] : 0;
            $data['requested_by'] = $_POST['requested_by'];
            $data['srf_type'] = $_POST['srf_type'];
            $data['srf_note'] = $_POST['srf_note'];

            if($srfId > 0){
                $qry = $this->db->get_where('cs_srf_header', array('srf_id' => $srfId));
                $row = $qry->row();

                $arr_date = explode('-', $data['srf_date']);
                $arr_date_old = explode('-', ymd_from_db($row->srf_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    //DELETE OLD NUMBER
                    $data['srf_no'] = $this->mdl_general->generate_code(Feature::FEATURE_CS_SRF, $data['srf_date']);

                    if($data['srf_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Failed generating code.');
                    }
                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = $server_date;

                    $this->mdl_general->update('cs_srf_header', array('srf_id' => $srfId), $data);

                    //Update details
                    if($valid){
                        //echo '<br>step 4 update';
                        $srf['srf_id'] = $row->srf_id;
                        $srf['unit_id'] = $row->unit_id;

                        $valid = $this->insertDetailEntries($srf);

                        //echo '<br>step 5 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }
                }
            }else {
                $data['srf_no'] = $this->mdl_general->generate_code(Feature::FEATURE_CS_SRF, $data['srf_date']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;
                $data['modified_by'] = 0;
                $data['modified_date'] = $server_date;

                //echo 'no id : ' . $data['journal_no'];
                if($data['srf_no'] != ''){
                    $this->db->insert('cs_srf_header', $data);
                    $srfId = $this->db->insert_id();

                    if($srfId > 0){
                        $data['srf_id'] = $srfId;
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
                    $data_log['log_subject'] = 'CREATE SRF ' . $data['srf_no'];
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $srfId;
                    $data_log['feature_id'] = Feature::FEATURE_CS_SRF;
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
                redirect(base_url('housekeeping/srf/srf_form/' . $srfId . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('housekeeping/srf/srf_manage/1.tpd'),true);
                }
                else {
                    redirect(base_url('housekeeping/srf/srf_form/' . $srfId . '.tpd'));
                }
            }
        }
    }

    private function insertDetailEntries($srf = array()){
        $valid = true;
		$srf_type = $srf['srf_type'];

        if(count($srf) > 0 && isset($_POST) && $srf_type >0){
            $this->load->model('frontdesk/mdl_frontdesk');

            $srfId = $srf['srf_id'];
            $unitId = $srf['unit_id'];

            $multi_dates = isset($_POST['multi_date']) ? $_POST['multi_date'] : '';
            if($multi_dates != ''){
                $multi_dates = explode('|', $multi_dates);

                //Delete differ unit
                $deleted = $this->db->query('DELETE FROM cs_srf_detail WHERE srf_id = ' . $srfId . ' AND unit_id <> ' . $unitId);

                foreach($multi_dates as $dmy){
                    if($valid){
                        if($dmy != ''){
                            $detail = array();
                            $detail['srf_id'] = $srfId;
                            $detail['unit_id'] = $unitId;
                            $detail['work_date'] = dmy_to_ymd($dmy);
                            $detail['status'] = STATUS_NEW;

                            $rows = $this->db->get_where('cs_srf_detail', array('srf_id' => $srfId , 'unit_id' => $unitId, 'work_date' => $detail['work_date']));
                            if($rows->num_rows() <= 0){
                                $this->db->insert('cs_srf_detail', $detail);
                                if($this->db->insert_id() <= 0) {
                                    $valid = false;
                                }
                            }else{
                                $this->mdl_general->update('cs_srf_detail', array('srf_detail_id' => $rows->row()->srf_detail_id), $detail);
                            }
                        }
                    }else{
                        break;
                    }
                }

                //DELETE REMOVED detail id
                $delkey = array();
                $rows = $this->db->get_where('cs_srf_detail', array('srf_id' => $srfId , 'unit_id' => $unitId));
                if($rows->num_rows() > 0){
                    foreach($rows->result_array() as $key){
                        $found = false;
                        foreach($multi_dates as $dmy){
                            if(dmy_from_db($key['work_date']) == $dmy){
                                $found = true;
                                break;
                            }
                        }
                        if(!$found){
                            array_push($delkey, $key['srf_detail_id']);
                        }
                    }
                }

                if(count($delkey) > 0){
                    $keys = implode(',',$delkey);
                    if($keys != ''){
                        $this->db->query('DELETE FROM cs_srf_detail WHERE srf_detail_id IN(' . $keys . ')');
                    }
                }

            }else{
                $valid = false;
            }
        }

        return $valid;
    }

    public function action_request(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] ='';

        $srf_id = $_POST['srf_id'];
        $data['status'] = $_POST['action'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = get_action_name($data['status'], false) . ' SRF';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $srf_id;
        $data_log['feature_id'] = Feature::FEATURE_CS_SRF;

        if($srf_id > 0 && $data['status'] > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $qry = $this->db->get_where('cs_srf_header', array('srf_id' => $srf_id));
            if($qry->num_rows() > 0){
                $row = $qry->row();
                if($data['status'] == STATUS_CLOSED){
                    $data['action_note'] = $_POST['reason'];
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('cs_srf_header', array('srf_id' => $srf_id), $data);

                    //Update Unit to VD  
					$data_hsk = array();
					$data_hsk['unit_id'] = $row->unit_id;
					$data_hsk['hsk_status'] = HSK_STATUS::VD;
					$data_hsk['remark'] = 'SRF Close '. $srf_id ;
					$data_hsk['created_by'] = my_sess('user_id');
					$data_hsk['created_date'] = date('Y-m-d H:i:s');
					if ($row->srf_type != 0 ){
                    $this->mdl_general->update('ms_unit', array('unit_id' => $row->unit_id), array('hsk_status' => HSK_STATUS::VD));
					}
					$this->db->insert('log_hsk', $data_hsk);

                    $data_log['action_type'] = STATUS_CLOSED;
                    $this->db->insert('app_log', $data_log);

                    $result['type'] = '1';
                    $result['message'] = 'Transaction successfully processed.';
                }
                else if($data['status'] == STATUS_CANCEL){
                    $data['action_note'] = $_POST['reason'];
                    if($row->status == STATUS_CANCEL){
                        $result['type'] = '0';
                        $result['message'] = 'Transaction already canceled.';
                    }
                    else {
                        $this->mdl_general->update('cs_srf_header', array('srf_id' => $srf_id), $data);

                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['type'] = '1';
                        $result['message'] = 'Transaction successfully canceled.';
                    }
                }
                else if($data['status'] == STATUS_APPROVE){
                    $data['action_note'] = $_POST['reason'];
                    if($row->status == STATUS_APPROVE){
                        $result['type'] = '0';
                        $result['message'] = 'Transaction already approved.';
                    }
                    else {
                        $this->mdl_general->update('cs_srf_header', array('srf_id' => $srf_id), $data);
						if ($row->srf_type != 0 ){
                        //Update Unit to OO/OS
                        $this->mdl_general->update('ms_unit', array('unit_id' => $row->unit_id), array('hsk_status' => SRF_TYPE::caption($row->srf_type)));
						}

                        $data_log['action_type'] = STATUS_APPROVE;
                        $this->db->insert('app_log', $data_log);

                        $result['type'] = '1';
                        $result['message'] = 'Transaction successfully approved.';
                    }
                }

                //FINALIZE TRANSACTION
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be processed.';
                }
                else
                {
                    $result['redirect_link'] = base_url('housekeeping/srf/srf_form/' . $srf_id . '.tpd');
                    $this->db->trans_commit();
                }
            }
        }

        echo json_encode($result);
    }

    public function srf_history(){
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

        $this->load->view('layout/header', $data_header);
        $this->load->view('housekeeping/srf/srf_history.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_srf_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();

        $where['cs_srf_header.status'] = STATUS_CLOSED;

        $like = array();

        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['DATE(cs_srf_header.srf_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['DATE(cs_srf_header.srf_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['cs_srf_header.srf_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_unit'])){
            if($_REQUEST['filter_unit'] != ''){
                $like['ms_unit.unit_code'] = $_REQUEST['filter_unit'];
            }
        }

        if(isset($_REQUEST['filter_request_by'])){
            if($_REQUEST['filter_request_by'] != ''){
                $like['cs_srf_header.requested_by'] = $_REQUEST['filter_request_by'];
            }
        }

        $joins = array('ms_unit' => 'ms_unit.unit_id = cs_srf_header.unit_id');
        $iTotalRecords = $this->mdl_finance->countJoin('cs_srf_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'cs_srf_header.srf_no DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'cs_srf_header.srf_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'cs_srf_header.srf_date ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('cs_srf_header.*, case cs_srf_header.unit_id WHEN 0 then "Public" else ms_unit.unit_code end as unit_code '
            ,'cs_srf_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('housekeeping/srf/srf_form/' . $row->srf_id) . '.tpd">View</a> </li>';

            $records["data"][] = array(
                $i,
                $row->srf_no,
                dmy_from_db($row->srf_date),
                $row->unit_code,
                $row->requested_by,
                SRF_TYPE::caption($row->srf_type),
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                        Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        $records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    #endregion

    #region Print

    public function pdf_srf($srf_id) {
        if($srf_id > 0){
            $qry = $this->db->query('SELECT srf.*, unit.unit_code
                        FROM cs_srf_header srf
                        LEFT JOIN ms_unit unit ON unit.unit_id = srf.unit_id
                        WHERE srf.srf_id = '. $srf_id);
            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                $this->load->view('housekeeping/srf/pdf_srf', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($srf_id . ".pdf", array('Attachment' => 0));
            }else{
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    #endregion

}

/* End of file registration.php */
/* Location: ./application/controllers/frondesk/registration.php */