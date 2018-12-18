<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Proforma_inv extends CI_Controller {

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
		$this->proforma_inv_manage();
	}

    #region proforma_inv

    public function proforma_inv_manage($type = 1, $pro_inv_id = 0){
        //$type = 1 => manage
        //$type = 2 => history
        //$type = 3 => form

        if ($type == 1 || $type == 2) {
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
            $this->load->view('ar/proforma_inv/proforma_inv_manage.php', $data);
            $this->load->view('layout/footer');
        } else if ($type == 3) {
            $this->proforma_inv_form($pro_inv_id);
        }
    }

    public function get_pro_inv_manage($menu_id = 0, $is_history = '0'){
        $this->load->model('finance/mdl_finance');

        $where = array();
        if ($is_history == '0') {
            $where['ar_proforma_inv_header.status'] = STATUS_NEW;
        } else {
            $where['ar_proforma_inv_header.status <>'] = STATUS_NEW;
        }

        $like = array();

        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['DATE(ar_proforma_inv_header.bill_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['DATE(ar_proforma_inv_header.bill_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_bill_no'])){
            if($_REQUEST['filter_bill_no'] != ''){
                $like['ar_proforma_inv_header.pro_inv_no'] = $_REQUEST['filter_bill_no'];
            }
        }
        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        } 
        if(isset($_REQUEST['filter_company_name'])){
            if($_REQUEST['filter_company_name'] != ''){
                $like['ms_company.company_name'] = $_REQUEST['filter_company_name'];
            }
        }

        $joins = array('ms_company' => 'ar_proforma_inv_header.company_id = ms_company.company_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_proforma_inv_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_proforma_inv_header.pro_inv_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_proforma_inv_header.pro_inv_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_proforma_inv_header.*, ms_company.company_name'
            ,'ar_proforma_inv_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/proforma_inv/proforma_inv_manage/3/' . $row->pro_inv_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_APPROVE)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_APPROVE . '" data-id="' . $row->pro_inv_id . '" data-code="' . $row->pro_inv_no . '">' . get_action_name(STATUS_APPROVE, false) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->pro_inv_id . '" data-code="' . $row->pro_inv_no . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }
			$btn_action .= '<li><a href="' . base_url('ar/proforma_inv/pdf_proforma_inv/' . $row->pro_inv_id) . '.tpd" target="_blank">Print</a> </li>';
 
            if($row->total_amount > 0){
                $input_attr = '';
                
                $records["data"][] = array(
                    '<input class="checked_posting" type="checkbox" value="' . $row->pro_inv_id . '" name="ischecked[' . $row->pro_inv_id . ']" ' . $input_attr . '/>',
                    $row->pro_inv_no, 
					dmy_from_db($row->pro_inv_date),
                    $row->company_name, 
                    format_num($row->total_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
                );
            } else {
                $records["data"][] = array(
                    '',
                    dmy_from_db($row->pro_inv_date),
                    $row->pro_inv_no,
                    $row->company_name,
                    //$bill_to,
                    format_num($row->total_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
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
        $records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function proforma_inv_form($id = 0){
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

        //HEADER
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        $data['pro_inv_id'] = $id;

        if($id > 0){
            $joins = array('ms_company' => 'ms_company.company_id = ar_proforma_inv_header.company_id');
            $qry = $this->mdl_finance->getJoin('ar_proforma_inv_header.* ,ms_company.company_name','ar_proforma_inv_header', $joins, array('pro_inv_id' => $id));
            $data['bill'] = $qry->row();

            $data['details'] = $this->db->get_where('ar_proforma_inv_detail', array('pro_inv_id' => $data['pro_inv_id']));
        }

        $taxtype = $this->db->get_where('tax_type', array('is_charge_default > ' => 0));
        if($taxtype->num_rows() > 0){
            $data['tax_type'] = $taxtype->row();
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/proforma_inv/proforma_inv_form', $data);
        $this->load->view('layout/footer');
    }

    #endregion

    #region Look up

    public function ajax_modal_company(){
        $this->load->view('frontdesk/reservation/ajax_modal_company');
    }

    public function get_modal_othercharge($num_index = 0, $reservation_id = 0){
        $where = array();
        $like = array();

        if(isset($_REQUEST['filter_code'])){
            if($_REQUEST['filter_code'] != ''){
                $like['tenant_account'] = $_REQUEST['filter_code'];
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company_name'])){
            if($_REQUEST['filter_company_name'] != ''){
                $like['company_name'] = $_REQUEST['filter_company_name'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_cs_tenant_checkin',$where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'reservation_code DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'reservation_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_cs_tenant_checkin',$where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $text = "Select";
            $attr = '';
            $attr .= ' data-reservation-id="' . $row->reservation_id . '" ';
            $attr .= ' data-reservation-type="' . $row->reservation_type . '" ';
            $attr .= ' data-reservation-code="' . $row->reservation_code . '" ';
            $attr .= ' data-tenant-id="' . $row->tenant_id . '" ';
            $attr .= ' data-tenant-name="' . $row->tenant_fullname . '" ';
            $attr .= ' data-company-id="' . $row->company_id . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            if ($reservation_id == $row->reservation_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-reservation" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $row->reservation_code,
                RES_TYPE::caption($row->reservation_type),
                $row->tenant_fullname,
                $row->company_name,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function select_tenant_unit(){
        $result ='';

        $reservationId = $_POST['reservation_id'];

        if($reservationId > 0){
            $qry = $this->db->query('select distinct ms_unit.unit_id, ms_unit.unit_code from ms_unit
                                     join cs_reservation_detail on cs_reservation_detail.unit_id = ms_unit.unit_id
                                     join cs_reservation_header on cs_reservation_detail.reservation_id = cs_reservation_header.reservation_id
                                     where cs_reservation_header.reservation_id = ' . $reservationId); //cs_reservation_header.status = ' . ORDER_STATUS::CHECKIN . '

            if($qry->num_rows() > 0){
                foreach($qry->result_array() as $unit){
                    $result .= '<option value="' . $unit['unit_id'] . '">' . $unit['unit_code'] . '</option>';
                }
            }
        }

        echo $result;
    }

    public function ajax_proforma_submit(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $data = array();

        if(isset($_POST)){
            $has_error = false;
            $this->db->trans_begin();
            $pro_inv_id = $_POST['pro_inv_id'];

            $debtor_type = isset($_POST['debtor_type']) ? $_POST['debtor_type'] : 1;
           	
			$data['company_id'] = $_POST['company_id'];		
			$data['pro_inv_due_date'] = dmy_to_ymd($_POST['pro_inv_due_date']);
			$data['pro_inv_date'] = dmy_to_ymd($_POST['pro_inv_date']); 
			$data['total_amount'] = $_POST['total_amount'];

            if($data['company_id'] > 0 ){
                if ($pro_inv_id > 0) {
                    $qry_proforma_inv = $this->db->get_where('ar_proforma_inv_header', array('pro_inv_id' => $pro_inv_id));
                    $row_proforma_inv = $qry_proforma_inv->row();

                    $arr_date = explode('-', $data['pro_inv_date']);
                    $arr_date_old = explode('-', ymd_from_db($row_proforma_inv->pro_inv_date));

                    if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                        $data['pro_inv_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_PROFORMA, $data['pro_inv_date']);
                        if($data['pro_inv_no'] == ''){
                            $has_error = true;

                            $this->session->set_flashdata('flash_message_class', 'danger');
                            $this->session->set_flashdata('flash_message', 'Failed generating code.');
                        }
                    }

                    if(!$has_error){
                        $this->mdl_general->update('ar_proforma_inv_header', array('pro_inv_id' => $pro_inv_id), $data);

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Successfully update Proforma Invoice.');
                    }

                } else {

                    $data['pro_inv_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_PROFORMA, $data['pro_inv_date']);
                    if ($data['pro_inv_no'] != '') {
                        $data['created_by'] = my_sess('user_id');
                        $data['created_date'] = date('Y-m-d H:i:s');
                        $data['status'] = STATUS_NEW;

                        $this->db->insert('ar_proforma_inv_header', $data);
                        $pro_inv_id = $this->db->insert_id();

                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $pro_inv_id;
                        $data_log['feature_id'] = Feature::FEATURE_AR_PROFORMA;
                        $data_log['log_subject'] = 'Create AR Proforma Invoice  (' . $data['pro_inv_no'] . ')';
                        $data_log['action_type'] = STATUS_NEW;
                        $this->db->insert('app_log', $data_log);

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Successfully add Proforma.');
                    } else {
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }
            }else{
                $has_error = true;
            }

            if($has_error == false) {
                if (isset($_POST['pro_invdetail_id'])) {
                    $i = 0;

                    foreach ($_POST['pro_invdetail_id'] as $key => $val) {
                        $data_detail = array();

                        $status = $_POST['status'][$key];

                        $data_detail['pro_inv_id'] = $pro_inv_id;
                        $data_detail['item_desc'] = $_POST['item_desc'][$key];
                        $data_detail['item_qty'] = $_POST['item_qty'][$key];
                        $data_detail['item_rate'] = $_POST['item_rate'][$key];
                        $data_detail['amount'] = $_POST['item_amount'][$key];
                        $data_detail['tax'] = $_POST['tax_amount'][$key];
                        $data_detail['status'] = STATUS_NEW;

                        if ($val > 0) {
                            if ($status == STATUS_NEW) {
                                $this->mdl_general->update('ar_proforma_inv_detail', array('pro_invdetail_id' => $val), $data_detail);
                            } else {
                                $this->db->delete('ar_proforma_inv_detail', array('pro_invdetail_id' => $val));
                            }
                        } else {
                            if ($status == STATUS_NEW) {
                                $this->db->insert('ar_proforma_inv_detail', $data_detail);
                            }
                        }
                        $i++;
                    }

                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'No Detail Found.';
                }
            }
			

            if($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('ar/proforma_inv/proforma_inv_manage/1.tpd');
                    } else{
                        $result['link'] = base_url('ar/proforma_inv/proforma_inv_manage/3/' . $pro_inv_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function ajax_action(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $this->db->trans_begin();

        $pro_inv_id = $_POST['pro_inv_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $pro_inv_id;
        $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION_BILL;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($pro_inv_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('ar_proforma_inv_header', array('pro_inv_id' => $pro_inv_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_APPROVE) {
                    if ($row->status == STATUS_APPROVE) {
                        $result['valid'] = '0';
                        $result['message'] = 'Proforma already posted.';
                    } else { 
							$this->mdl_general->update('ar_proforma_inv_header', array('pro_inv_id' => $pro_inv_id), $data);

							$data_log['log_subject'] = 'Approve Profoma INV (' . $row->pro_inv_no . ')';
							$data_log['action_type'] = STATUS_APPROVE;
							$this->db->insert('app_log', $data_log);

							$result['message'] = 'Successfully Approve Proforma inv.'; 
                         
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Profoma already canceled.';
                    } else {
                        $this->mdl_general->update('ar_proforma_inv_header', array('pro_inv_id' => $pro_inv_id), $data);

                        $data_log['log_subject'] = 'Cancel Proforma Inv (' . $row->pro_inv_no . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel Profoma.';
                    }
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            $result['valid'] = '0';
            $result['message'] = "Something error. Please try again later.";
        }
        else {
            $this->db->trans_commit();

            if($is_redirect){
                $this->session->set_flashdata('flash_message_class', ($result['valid'] == '1' ? 'success' : 'danger'));
                $this->session->set_flashdata('flash_message', $result['message']);
            }
        }

        echo json_encode($result);
    }

    private function posting_billing($pro_inv_id = 0){
        $this->load->model('finance/mdl_finance');
        $result = array();
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($pro_inv_id > 0) {
            $joins = array('view_cs_reservation' => 'ar_proforma_inv_header.reservation_id = view_cs_reservation.reservation_id');
            $qry_bill = $this->mdl_finance->getJoin('ar_proforma_inv_header.*, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, view_cs_reservation.reservation_code'
                ,'ar_proforma_inv_header', $joins, array('pro_inv_id' => $pro_inv_id));
            if ($qry_bill->num_rows() > 0) {
                $row_bill = $qry_bill->row();

                $detail = array();

                $totalDebit = 0;
                $totalCredit = 0;
                $journal_desc = $row_bill->pro_inv_no . ' / ' . $row_bill->tenant_fullname . ' / ' . $row_bill->reservation_code;
                if($row_bill->company_id > 0){
                    $journal_desc = $row_bill->pro_inv_no . ' / ' . $row_bill->company_name . ' / ' . $row_bill->reservation_code;
                }

                $qryDetails = $this->db->query("SELECT SUM (amount) AS total FROM ar_proforma_inv_detail WHERE pro_inv_id = " . $pro_inv_id);
                if ($qryDetails->num_rows() > 0) {
                    $rowDetail = $qryDetails->row();

                    if ($rowDetail->total > 0) {
                        $ar = FNSpec::get(FNSpec::SALES_RESERVATION);
                        if($ar['coa_id'] > 0){
                            $rowdet = array();
                            $rowdet['coa_id'] = $ar['coa_id'];
                            $rowdet['dept_id'] = 0;
                            $rowdet['pro_inv_note'] = $journal_desc;
                            $rowdet['journal_debit'] = 0;
                            $rowdet['journal_credit'] = $rowDetail->total;
                            $rowdet['reference_id'] = $pro_inv_id;
                            $rowdet['transtype_id'] = $ar['transtype_id'];

                            array_push($detail, $rowdet);

                            $totalCredit = $rowDetail->total;
                        }
                    }
                }

                if ($result['error'] == '0') {

                    if($totalCredit > 0 && count($detail) > 0){
                        $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                        if($ar['coa_id'] > 0){
                            $rowdet = array();
                            $rowdet['coa_id'] = $ar['coa_id'];
                            $rowdet['dept_id'] = 0;
                            $rowdet['pro_inv_note'] = $journal_desc;
                            $rowdet['journal_debit'] = $totalCredit;
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = $pro_inv_id;
                            $rowdet['transtype_id'] = $ar['transtype_id'];

                            array_push($detail, $rowdet);

                            $totalDebit = $totalCredit;
                        }
                    }
                }

                if ($result['error'] == '0') {
                    if ($totalDebit == $totalCredit) {
                        $header = array();
                        $header['pro_inv_no'] = $row_bill->pro_inv_no;
                        $header['journal_date'] = $row_bill->bill_date;
                        $header['journal_remarks'] = $journal_desc;
                        $header['modul'] = GLMOD::GL_MOD_AR;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = strval($pro_inv_id);

                        $valid = $this->mdl_finance->postJournal($header, $detail);

                        if ($valid == false) {
                            $result['error'] = '1';
                            $result['message'] = 'Failed insert journal.';
                        }
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'Bill not found.';
            }
        }

        return $result;
    }

    public function ajax_bill_multi_posting(){
        $has_error = false;

        $result = array();
        $result['valid'] = array();
        $result['message'] = array();
        $result['link'] = '';
        $result['debug'] = '';

        $this->db->trans_begin();

        foreach($_POST['ischecked'] as $key => $pro_inv_id) {
            $data['status'] = STATUS_APPROVE;

            $data_log['user_id'] = my_sess('user_id');
            $data_log['log_date'] = date('Y-m-d H:i:s');
            $data_log['reff_id'] = $pro_inv_id;
            $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION_BILL;

            if ($pro_inv_id > 0 && $data['status'] > 0) {
                $qry = $this->db->get_where('ar_proforma_inv_header', array('pro_inv_id' => $pro_inv_id));
                if ($qry->num_rows() > 0) {
                    $row = $qry->row();

                    if ($data['status'] == STATUS_APPROVE) {
                        if ($row->status == STATUS_APPROVE) {
                            array_push($result['valid'], '0');
                            array_push($result['message'], $row->pro_inv_no . ' already posted');
                        } else {
                                if (!$has_error) {
                                    $this->mdl_general->update('ar_proforma_inv_header', array('pro_inv_id' => $pro_inv_id), $data);

                                    $data_log['log_subject'] = 'Approve Profoma Invoice (' . $row->pro_inv_no . ')';
                                    $data_log['action_type'] = STATUS_APPROVE;
                                    $this->db->insert('app_log', $data_log);

                                    array_push($result['valid'], '1');
                                    array_push($result['message'], 'Successfully Approve ' . $row->pro_inv_no);
                                } else {
                                    $has_error = false;
                                    array_push($result['valid'], '0');
                                    //array_push($result['message'], $row->pro_inv_no . ' ' . $valid['message']);
                                }                           
                        }
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                array_push($result['valid'], '0');
                array_push($result['message'], $row->pro_inv_no . ' Something error. Please try again later.');
            } else {
                $this->db->trans_commit();
            }
        }
        echo json_encode($result);
    }

    public function ajax_inv_single_posting(){

        $result = array();
        $result['valid'] = array();
        $result['message'] = array();
        $result['link'] = '';
        $result['debug'] = '';

        $pro_inv_id = $_POST['pro_inv_id'];

        if($pro_inv_id > 0){
            $this->db->trans_begin();

            $data['status'] = STATUS_APPROVE;

            $data_log['user_id'] = my_sess('user_id');
            $data_log['log_date'] = date('Y-m-d H:i:s');
            $data_log['reff_id'] = $pro_inv_id;
            $data_log['feature_id'] = Feature::FEATURE_CS_RESERVATION_BILL;

            if ($pro_inv_id > 0 && $data['status'] > 0) {
                $qry = $this->db->get_where('ar_proforma_inv_header', array('pro_inv_id' => $pro_inv_id));
                if ($qry->num_rows() > 0) {
                    $row = $qry->row();

                    if ($data['status'] == STATUS_APPROVE) {
                        if ($row->status == STATUS_APPROVE) {
                            array_push($result['valid'], '0');
                            array_push($result['message'], $row->pro_inv_no . ' already posted');
                        } else {
								$this->mdl_general->update('ar_proforma_inv_header', array('pro_inv_id' => $pro_inv_id), $data);

								$data_log['log_subject'] = 'Posting Billing (' . $row->pro_inv_no . ')';
								$data_log['action_type'] = STATUS_APPROVE;
								$this->db->insert('app_log', $data_log);

								array_push($result['valid'], '1');
								array_push($result['message'], 'Successfully posting ' . $row->pro_inv_no);
                                                              
                        }
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                array_push($result['valid'], '0');
                array_push($result['message'], $row->pro_inv_no . ' Something error. Please try again later.');
            } else {
                $this->db->trans_commit();

                $result['link'] = base_url('ar/proforma_inv/proforma_inv_manage/3/' . $pro_inv_id . '.tpd');
            }
        }


        echo json_encode($result);
    }

    #endregion
	
    #region Print

    public function pdf_proforma_inv($pro_inv_id = 0, $is_unpaid_only = 0) {
        if($pro_inv_id > 0){
            //Reservation
			$this->load->model('finance/mdl_finance');
			$joins = array('ms_company' => 'ar_proforma_inv_header.company_id = ms_company.company_id');
			$where = array(); 
				$where['ar_proforma_inv_header.pro_inv_id'] = $pro_inv_id;
			
			$qry = $this->mdl_finance->getJoin('ar_proforma_inv_header.*, ms_company.company_name ,ms_company.company_address,ms_company.company_phone,ms_company.company_pic_name'
            ,'ar_proforma_inv_header', $joins, $where);
            //$qry = $this->db->get_where('ar_proforma_inv_header', array('pro_inv_id' => $pro_inv_id));
            if($qry->num_rows() > 0){
                 
                    $data['row'] = $qry->row_array();

                    //Company Profile
                    $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                    if($profile->num_rows() > 0){
                        $data['profile'] = $profile->row_array();
                    }

                    //Tenant
                    $folio_caption = 'Proforma Invoice';
                    $bill_info = '';

                    /*
                    $company = $this->db->get_where('ms_company', array('company_id' => $data['row']['company_id']));
                    if($company->num_rows() > 0){
                        $company = $company->row_array();
                        $bill_info = $company['company_name'];
                        $bill_info .= '<br>' . nl2br($company['company_address']);
                        $bill_info .= '<br>' . $company['company_phone'];
                        //$bill_info .= '<br>' . $company['company_pic_name'];
                    }
                    */

                    $bill_info = $data['row']['company_name'];
                    $bill_info .= '<br>' . nl2br($data['row']['company_address']);
                    $bill_info .= '<br>' . $data['row']['company_phone'];
                    $bill_info .= '<br>Attn. ' . $data['row']['company_pic_name'];

                    $data['folio_title'] = $folio_caption;
                    $data['guest_info'] = $bill_info;

                    //Ledger
                    $details = $this->db->query('SELECT * FROM ar_proforma_inv_detail WHERE pro_inv_id = ' . $pro_inv_id . ' ORDER BY pro_invdetail_id ASC');
                    //$details = $this->db->get_where('fxnARInvoiceDetailByInvID(' . $inv_id . ')');
                    if($details->num_rows() > 0){
                        $data['detail'] = $details->result_array();
                    }

                    //echo $this->db->last_query();
                    $data['is_unpaid_only'] = $is_unpaid_only > 0 ? true : false;

                    $this->load->view('ar/proforma_inv/pdf_proforma_inv', $data);

                    $html = $this->output->get_output();

                    $this->load->library('dompdf_gen');

                    $this->dompdf->set_paper("A4", "portrait");
                    $this->dompdf->load_html($html);
                    $this->dompdf->render();

                    $this->dompdf->stream($data['row']['pro_inv_no'] . ".pdf", array('Attachment'=>0));
                 
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