<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit extends CI_Controller {

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
		$this->credit_manage();
	}

    #region Credit Manage

    public function credit_manage($type = 1){
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

        //$data['is_history'] = ($type == 2 ? true : false);
        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/credit/credit_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_credit_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_creditnote_header.company_id <='] = 0;
        $where['ar_creditnote_header.status'] = STATUS_NEW;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_creditnote_header.credit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_creditnote_header.credit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_creditnote_header.credit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company'];
            }
        }
        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        }

        $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_creditnote_header.reservation_id'
                       );
        $iTotalRecords = $this->mdl_finance->countJoin('ar_creditnote_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_creditnote_header.credit_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_creditnote_header.credit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_creditnote_header.credit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'view_cs_reservation.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_creditnote_header.*, view_cs_reservation.tenant_fullname,view_cs_reservation.company_name, view_cs_reservation.reservation_code, view_cs_reservation.reservation_date, view_cs_reservation.status as reservation_status'
            ,'ar_creditnote_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/folio/credit/3/' . $row->creditnote_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL) || check_session_action($menu_id, STATUS_DELETE)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->creditnote_id . '" data-code="' . $row->credit_no . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->credit_amount > 0){
                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->creditnote_id . '" name="ischecked[]" ' . (check_session_action($menu_id, STATUS_POSTED) ? ($row->status == STATUS_NEW ? '' : 'disabled') : 'disabled') . '/>',
                    $row->credit_no,
                    dmy_from_db($row->credit_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    $row->company_name,
                    format_num($row->credit_amount,0),
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
                    $row->credit_no,
                    dmy_from_db($row->credit_date),
                    $row->reservation_code,
                    $row->tenant_fullname,
                    $row->company_name,
                    format_num($row->credit_amount,0),
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
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function credit_form($creditnote_id = 0){
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

        $data['creditnote_id'] = $creditnote_id;

        if($creditnote_id > 0){
            $joins = array('ms_company' => 'ms_company.company_id = ar_creditnote_header.company_id');
            $qry = $this->mdl_finance->getJoin('ar_creditnote_header.*, ms_company.company_name'
                ,'ar_creditnote_header', $joins, array('creditnote_id' => $creditnote_id));

            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                $detail_joins = array('ar_invoice_header'=>'ar_invoice_header.inv_id = bill.inv_id');
                $data['detail']  = $this->mdl_finance->getJoin("bill.*, ar_invoice_header.inv_no ","ar_creditnote_detail AS bill", $detail_joins, array('creditnote_id' => $creditnote_id));
            }else{
                $data['creditnote_id'] = 0;
            }
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/credit/credit_form', $data);
        $this->load->view('layout/footer');
    }

    public function action_request(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $creditnote_id = $_POST['creditnote_id'];
        $data['status'] = $_POST['action'];
        $data['cancel_note'] = $_POST['reason'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Credit Note';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $creditnote_id;
        $data_log['feature_id'] = Feature::FEATURE_AR_CREDIT_NOTE;

        if($creditnote_id > 0 && $data['status'] > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $qry = $this->db->get_where('ar_creditnote_header', array('creditnote_id' => $creditnote_id));
            if($qry->num_rows() > 0){
                $row = $qry->row();

                if($data['status'] == STATUS_APPROVE){
                    $this->mdl_general->update('ar_creditnote_header', array('creditnote_id' => $creditnote_id), $data);

                    $data_log['action_type'] = STATUS_APPROVE;
                    $this->db->insert('app_log', $data_log);

                    $result['type'] = '1';
                    $result['message'] = 'Transaction successfully approved.';
                }
                else if($data['status'] == STATUS_CANCEL){
                    if($row->status == STATUS_CANCEL){
                        $result['type'] = '0';
                        $result['message'] = 'Transaction already canceled.';
                    }
                    else {
                        $this->mdl_general->update('ar_creditnote_header', array('creditnote_id' => $creditnote_id), $data);
                        $this->mdl_general->update('ar_creditnote_detail', array('creditnote_id' => $creditnote_id), array('status'=>STATUS_CANCEL));

                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['type'] = '1';
                        $result['message'] = 'Transaction successfully canceled.';
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
                    $this->db->trans_commit();
                }
            }
        }

        echo json_encode($result);
    }

    public function delete_credit_detail_sub(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $subdetail_id = $_POST['sub_detail_id'];

        if($subdetail_id > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $this->db->delete('ar_creditnote_detail_sub', array('sub_detail_id' => $subdetail_id));

            //FINALIZE TRANSACTION
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be processed.';
            }
            else
            {
                $this->db->trans_commit();

                $result['type'] = '1';
                $result['message'] = 'Successfully delete record.';
            }
        }

        echo json_encode($result);
    }

    public function submit_credit(){
        $valid = true;

        if(isset($_POST)){
            $creditnote_id = $_POST['creditnote_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['credit_date'] = dmy_to_ymd($_POST['credit_date']);
            $data['reservation_id'] = $_POST['reservation_id'];
            $data['credit_remark'] = $_POST['credit_remark'];
            $data['credit_amount'] = $_POST['grandtotal_credit'];
            $data['company_id'] = 0;

            if($creditnote_id > 0){
                $qry = $this->db->get_where('ar_creditnote_header', array('creditnote_id' => $creditnote_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['credit_date']);
                $arr_date_old = explode('-', ymd_from_db($row->credit_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['credit_no'] = $this->generate_no($data, $data['credit_date']);

                    if($data['credit_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Failed generating code.');
                    }
                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = $server_date;

                    $this->mdl_general->update('ar_creditnote_header', array('creditnote_id' => $creditnote_id), $data);

                    //echo '<br>step 3 update ' . $data['deposit_date'];

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';
                        $valid = $this->insertCreditEntries($creditnote_id);

                        //echo '<br>step 5 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }
                }
            }
            else {
                $data['credit_no'] = $this->generate_no($data, $data['credit_date']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                if($data['credit_no'] != ''){
                    $this->db->insert('ar_creditnote_header', $data);
                    $creditnote_id = $this->db->insert_id();

                    if($creditnote_id > 0){
                        $valid = $this->insertCreditEntries($creditnote_id);

                        if($valid){
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Credit Note';
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $creditnote_id;
                            $data_log['feature_id'] = Feature::FEATURE_AR_CREDIT_NOTE;
                            $data_log['action_type'] = STATUS_NEW;
                            $this->db->insert('app_log', $data_log);

                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Transaction successfully created.');
                        }

                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = false;
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
                redirect(base_url('ar/folio/credit/3/' . $creditnote_id . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('ar/folio/credit/1.tpd'),true);
                }
                else {
                    redirect(base_url('ar/folio/credit/3/' . $creditnote_id . '.tpd'));
                }
            }
        }
    }

    private function insertCreditEntries($creditnote_id = 0, $is_corporate = false){
        $valid = true;

        if($creditnote_id > 0 && isset($_POST)){
            $cn_detail_ids = isset($_POST['cn_detail_id']) ? $_POST['cn_detail_id'] : array();
            $base_amounts = isset($_POST['base_amount']) ? $_POST['base_amount'] : array();
            $credit_amounts = isset($_POST['credit_amount']) ? $_POST['credit_amount'] : array();

            if(!$is_corporate){
                //RESERVATION CN
                $billdetail_ids = isset($_POST['billdetail_id']) ? $_POST['billdetail_id'] : array();
                $transtype_ids = isset($_POST['transtype_id']) ? $_POST['transtype_id'] : array();

                if(count($billdetail_ids) > 0 && count($base_amounts) > 0 && count($credit_amounts) > 0){
                    $list_cndetail = array();
                    for($i=0;$i<count($billdetail_ids);$i++){
                        if($valid){
                            $cndetail_id = $cn_detail_ids[$i];
                            if($credit_amounts[$i] > 0){
                                $detail['creditnote_id'] =  $creditnote_id;
                                $detail['base_amount'] = $base_amounts[$i];
                                $detail['credit_amount'] = $credit_amounts[$i];
                                $detail['billdetail_id'] = $billdetail_ids[$i];
                                $detail['invdetail_id'] = 0;
                                $detail['is_tax'] = 0;
                                $detail['transtype_id'] = $transtype_ids[$i];
                                $detail['is_dn'] = 0;

                                if($cndetail_id > 0){
                                    $this->mdl_general->update('ar_creditnote_detail', array('cn_detail_id' => $cndetail_id), $detail);

                                }else{
                                    $detail['status'] = STATUS_NEW;

                                    $this->db->insert('ar_creditnote_detail', $detail);
                                    $cndetail_id = $this->db->insert_id();

                                    if($cndetail_id <= 0){
                                        $valid = false;
                                        break;
                                    }
                                }

                                array_push($list_cndetail, array('cn_detail_id' => $cndetail_id, 'billdetail_id' => $detail['billdetail_id']));
                            }else{
                                //Delete
                                $this->db->delete('ar_creditnote_detail', array('cn_detail_id' => $cndetail_id));
                            }
                        }
                    }

                    if($valid){
                        $detail_ids = isset($_POST['detail_id']) ? $_POST['detail_id'] : array();
                        $subbilldetail_ids = isset($_POST['sub_billdetail_id']) ? $_POST['sub_billdetail_id'] : array();
                        $subcoa_ids = isset($_POST['sub_coa_id']) ? $_POST['sub_coa_id'] : array();
                        $subcoa_codes = isset($_POST['sub_coa_code']) ? $_POST['sub_coa_code'] : array();
                        $subcredit_values = isset($_POST['sub_credit_value']) ? $_POST['sub_credit_value'] : array();

                        if(count($subcoa_ids) > 0){
                            for ($i = 0; $i < count($subcoa_ids); $i++) {
                                if($valid){
                                    if(isset($subcoa_codes[$i]) && isset($subcredit_values[$i])){
                                        if($subcredit_values[$i] > 0 && $subcoa_codes[$i] != '' && $subbilldetail_ids[$i] > 0){
                                            $sub['coa_id'] = $subcoa_ids[$i];
                                            $sub['coa_code'] = $subcoa_codes[$i];
                                            $sub['credit_amount'] = $subcredit_values[$i];

                                            if($detail_ids[$i] <= 0){
                                                foreach($list_cndetail as $cnd){
                                                    if($cnd['billdetail_id'] == $subbilldetail_ids[$i]){
                                                        $sub['cn_detail_id'] = $cnd['cn_detail_id'];
                                                        break;
                                                    }
                                                }
                                                $sub['status'] = STATUS_NEW;

                                                $this->db->insert('ar_creditnote_detail_sub', $sub);
                                                $insertID = $this->db->insert_id();

                                                if($insertID <= 0){
                                                    $valid = false;
                                                }
                                            }else{
                                                $this->mdl_general->update('ar_creditnote_detail_sub', array('sub_detail_id' => $detail_ids[$i]), $sub);
                                            }
                                        }
                                    }
                                }else{
                                    break;
                                }
                            }
                        }
                    }
                }
            }else{
                //CORPORATE CN
                $invdetail_ids = isset($_POST['invdetail_id']) ? $_POST['invdetail_id'] : array();
                $is_taxes = isset($_POST['is_tax']) ? $_POST['is_tax'] : array();
                //$transtype_ids = isset($_POST['transtype_id']) ? $_POST['transtype_id'] : array();

                if(count($invdetail_ids) > 0 && count($base_amounts) > 0 && count($credit_amounts) > 0){
                    $list_cndetail = array();
                    for($i=0;$i<count($invdetail_ids);$i++){
                        if($valid){
                            $cndetail_id = $cn_detail_ids[$i];
                            if($credit_amounts[$i] > 0){
                                $detail['creditnote_id'] =  $creditnote_id;
                                $detail['base_amount'] = $base_amounts[$i];
                                $detail['credit_amount'] = $credit_amounts[$i];
                                $detail['invdetail_id'] = $invdetail_ids[$i];
                                $detail['billdetail_id'] = 0;
                                $detail['is_tax'] = $is_taxes[$i];
                                $detail['transtype_id'] = 0;
                                $detail['is_dn'] = 0;

                                if($cndetail_id > 0){
                                    $this->mdl_general->update('ar_creditnote_detail', array('cn_detail_id' => $cndetail_id), $detail);
                                }else{
                                    $detail['status'] = STATUS_NEW;

                                    $this->db->insert('ar_creditnote_detail', $detail);
                                    $cndetail_id = $this->db->insert_id();

                                    if($cndetail_id <= 0){
                                        $valid = false;
                                        break;
                                    }
                                }

                                array_push($list_cndetail, array('cn_detail_id' => $cndetail_id, 'invdetail_id' => $detail['invdetail_id'], 'is_tax' => $detail['is_tax']));
                            }else{
                                //Delete
                                $this->db->delete('ar_creditnote_detail', array('cn_detail_id' => $cndetail_id));
                            }
                        }
                    }

                    if($valid){
                        $detail_ids = isset($_POST['detail_id']) ? $_POST['detail_id'] : array();
                        $subbinvdetail_ids = isset($_POST['sub_invdetail_id']) ? $_POST['sub_invdetail_id'] : array();
                        $subcoa_ids = isset($_POST['sub_coa_id']) ? $_POST['sub_coa_id'] : array();
                        $subis_taxes = isset($_POST['sub_is_tax']) ? $_POST['sub_is_tax'] : array();
                        $subcoa_codes = isset($_POST['sub_coa_code']) ? $_POST['sub_coa_code'] : array();
                        $subcredit_values = isset($_POST['sub_credit_value']) ? $_POST['sub_credit_value'] : array();

                        if(count($subcoa_ids) > 0){
                            for ($i = 0; $i < count($subcoa_ids); $i++) {
                                if($valid){
                                    if(isset($subcoa_codes[$i]) && isset($subcredit_values[$i])){
                                        if($subcredit_values[$i] > 0 && $subcoa_codes[$i] != '' && $subbinvdetail_ids[$i] > 0){
                                            $sub['coa_id'] = $subcoa_ids[$i];
                                            $sub['coa_code'] = $subcoa_codes[$i];
                                            $sub['credit_amount'] = $subcredit_values[$i];

                                            if($detail_ids[$i] <= 0){
                                                foreach($list_cndetail as $cnd){
                                                    if($cnd['invdetail_id'] == $subbinvdetail_ids[$i] && $cnd['is_tax'] == $subis_taxes[$i]){
                                                        $sub['cn_detail_id'] = $cnd['cn_detail_id'];
                                                        break;
                                                    }
                                                }
                                                $sub['status'] = STATUS_NEW;

                                                $this->db->insert('ar_creditnote_detail_sub', $sub);
                                                $insertID = $this->db->insert_id();

                                                if($insertID <= 0){
                                                    $valid = false;
                                                }
                                            }else{
                                                $this->mdl_general->update('ar_creditnote_detail_sub', array('sub_detail_id' => $detail_ids[$i]), $sub);
                                            }
                                        }
                                    }
                                }else{
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $valid;
    }

    private function generate_no($header = array(), $serverdate){
        $result = '';

        if(count($header) > 0 && isset($serverdate)){
            $result = $this->mdl_general->generate_code(Feature::FEATURE_AR_CREDIT_NOTE, $serverdate);
        }

        return $result;
    }

    /*POSTING CREDITS*/
    public function posting_credits(){
        $valid = true;

        if(isset($_POST)){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            //AR
            //  BANK
            if(isset($_POST['ischecked'])){
                $rowcount = 0;

                foreach( $_POST['ischecked'] as $creditnote_id){
                    //echo '[posting_credits] ... ' . $creditnote_id;

                    if($valid){
                        //SALES
                        //  AR
                        $detail = array();

                        $totalDebit = 0;
                        $totalCredit = 0;

                        $qryHeader = $this->db->get_where('ar_creditnote_header', array('creditnote_id' => $creditnote_id));
                        if($qryHeader->num_rows() > 0){
                            $head = $qryHeader->row();

                            //Reservation
                            $reservation = $this->db->get_where('cs_reservation_header', array('reservation_id' => $head->reservation_id));

                            $journal_note = $head->credit_remark;

                            $qryDetails = $this->db->query('SELECT det.*, ms_transtype.coa_id FROM ar_creditnote_detail det
                                            LEFT JOIN ms_transtype ON ms_transtype.transtype_id = det.transtype_id
                                            WHERE det.creditnote_id = ' . $creditnote_id);

                            if($qryDetails->num_rows() > 0){
                                foreach($qryDetails->result_array() as $det){
                                    $cndetail_id = $det['cn_detail_id'];

                                    $subs =  $this->db->get_where('ar_creditnote_detail_sub', array('cn_detail_id' => $cndetail_id));

                                    if($subs->num_rows() > 0){
                                        foreach($subs->result_array() as $sub){
                                            $rowdet = array();
                                            $rowdet['coa_id'] = $sub['coa_id'];
                                            $rowdet['coa_code'] = $sub['coa_code'];
                                            $rowdet['dept_id'] = 0;
                                            $rowdet['journal_note'] = $journal_note;
                                            $rowdet['journal_debit'] = $sub['credit_amount'];
                                            $rowdet['journal_credit'] = 0;
                                            $rowdet['reference_id'] = 0;
                                            $rowdet['transtype_id'] = $det['transtype_id'];

                                            array_push($detail, $rowdet);

                                            $totalDebit += $sub['credit_amount'];
                                        }
                                    }else{
                                        $rowdet = array();
                                        $rowdet['coa_id'] = $det['coa_id'];
                                        $rowdet['dept_id'] = 0;
                                        $rowdet['journal_note'] = $journal_note;
                                        $rowdet['journal_debit'] = $det['credit_amount'];
                                        $rowdet['journal_credit'] = 0;
                                        $rowdet['reference_id'] = 0;
                                        $rowdet['transtype_id'] = $det['transtype_id'];

                                        array_push($detail, $rowdet);

                                        $totalDebit += $det['credit_amount'];
                                    }
                                }
                            }

                            if($totalDebit > 0){
                                if($totalDebit == $head->credit_amount){
                                    //AR
                                    $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                                    if($reservation->num_rows() > 0){
                                        $reservation = $reservation->row();
                                        if($reservation->reservation_type == RES_TYPE::CORPORATE){
                                            $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                                        }
                                    }

                                    if($specAR['coa_id'] > 0){
                                        $rowdet = array();
                                        $rowdet['coa_id'] = $specAR['coa_id'];
                                        $rowdet['dept_id'] = 0;
                                        $rowdet['journal_note'] = $journal_note;
                                        $rowdet['journal_debit'] = 0;
                                        $rowdet['journal_credit'] = $totalDebit;
                                        $rowdet['reference_id'] = 0;
                                        $rowdet['transtype_id'] = $specAR['transtype_id'];

                                        array_push($detail, $rowdet);

                                        $totalCredit = $totalDebit;
                                    }
                                }

                                //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                                if($totalDebit == $totalCredit){
                                    $posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : ymd_from_db($head->credit_date);

                                    $header = array();
                                    $header['journal_no'] = $head->credit_no;
                                    $header['journal_date'] = $posting_date;
                                    $header['journal_remarks'] = $head->credit_remark;
                                    $header['modul'] = GLMOD::GL_MOD_AR;
                                    $header['journal_amount'] = $totalDebit;
                                    $header['reference'] = '';

                                    $valid = $this->mdl_finance->postJournal($header,$detail);

                                    if($valid){
                                        $data['modified_by'] = my_sess('user_id');
                                        $data['modified_date'] = date('Y-m-d H:i:s');
                                        $data['status']= STATUS_CLOSED;
                                        $this->mdl_general->update('ar_creditnote_header', array('creditnote_id' => $creditnote_id), $data);

                                        //Insert Log
                                        $data_log['user_id'] = my_sess('user_id');
                                        $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Credit Note ';
                                        $data_log['log_date'] = date('Y-m-d H:i:s');
                                        $data_log['reff_id'] = $creditnote_id;
                                        $data_log['feature_id'] = Feature::FEATURE_AR_CREDIT_NOTE;
                                        $data_log['action_type'] = STATUS_NEW;
                                        $this->db->insert('app_log', $data_log);

                                        $rowcount++;
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                $this->session->set_flashdata('flash_message', 'No transactions selected for posting.');
                $this->session->set_flashdata('flash_message_class', 'warning');
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
                    $this->session->set_flashdata('flash_message', $rowcount . ' transaction(s) successfully posted.');
                    $this->session->set_flashdata('flash_message_class', 'success');

                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                redirect(base_url('ar/folio/credit/1.tpd'));
            }
            else {
                redirect(base_url('ar/folio/credit/1.tpd'));
            }
        }
    }

    public function ajax_posting_credit_by_id(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $creditnote_id = 0;

        if(isset($_POST['creditnote_id'])){
            $creditnote_id = $_POST['creditnote_id'];
        }

        if($creditnote_id > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            //SALES
            //  AR
            $detail = array();

            $totalDebit = 0;
            $totalCredit = 0;

            $qryHeader = $this->db->get_where('ar_creditnote_header', array('creditnote_id' => $creditnote_id));
            if($qryHeader->num_rows() > 0){
                $head = $qryHeader->row();
                $journal_note = $head->credit_remark;

                $qryDetails = $this->db->query('SELECT det.*, ms_transtype.coa_id FROM ar_creditnote_detail det
                                            LEFT JOIN ms_transtype ON ms_transtype.transtype_id = det.transtype_id
                                            WHERE det.creditnote_id = ' . $creditnote_id);

                if($qryDetails->num_rows() > 0){
                    foreach($qryDetails->result_array() as $det){
                        $cndetail_id = $det['cn_detail_id'];

                        $subs =  $this->db->get_where('ar_creditnote_detail_sub', array('cn_detail_id' => $cndetail_id));

                        if($subs->num_rows() > 0){
                            foreach($subs->result_array() as $sub){
                                $rowdet = array();
                                $rowdet['coa_id'] = $sub['coa_id'];
                                $rowdet['coa_code'] = $sub['coa_code'];
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $journal_note;
                                $rowdet['journal_debit'] = $sub['credit_amount'];
                                $rowdet['journal_credit'] = 0;
                                $rowdet['reference_id'] = 0;
                                $rowdet['transtype_id'] = $det['transtype_id'];

                                array_push($detail, $rowdet);

                                $totalDebit += $sub['credit_amount'];
                            }
                        }else{
                            $rowdet = array();
                            $rowdet['coa_id'] = $det['coa_id'];
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $journal_note;
                            $rowdet['journal_debit'] = $det['credit_amount'];
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = 0;
                            $rowdet['transtype_id'] = $det['transtype_id'];

                            array_push($detail, $rowdet);

                            $totalDebit += $det['credit_amount'];
                        }
                    }
                }

                if($totalDebit > 0){
                    if($totalDebit == $head->credit_amount){
                        //Reservation
                        $reservation = $this->db->get_where('cs_reservation_header', array('reservation_id' => $head->reservation_id));
                        //AR
                        $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                        if($reservation->num_rows() > 0){
                            $reservation = $reservation->row();
                            if($reservation->reservation_type == RES_TYPE::CORPORATE){
                                $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                            }
                        }

                        if($specAR['coa_id'] > 0){
                            $rowdet = array();
                            $rowdet['coa_id'] = $specAR['coa_id'];
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $journal_note;
                            $rowdet['journal_debit'] = 0;
                            $rowdet['journal_credit'] = $totalDebit;
                            $rowdet['reference_id'] = 0;
                            $rowdet['transtype_id'] = $specAR['transtype_id'];

                            array_push($detail, $rowdet);

                            $totalCredit = $totalDebit;
                        }
                    }

                    //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                    if($totalDebit == $totalCredit){
                        $posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : ymd_from_db($head->credit_date);

                        $header = array();
                        $header['journal_no'] = $head->credit_no;
                        $header['journal_date'] = $posting_date;
                        $header['journal_remarks'] = $head->credit_remark;
                        $header['modul'] = GLMOD::GL_MOD_AR;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = '';

                        $valid = $this->mdl_finance->postJournal($header,$detail);

                        if($valid){
                            $data['modified_by'] = my_sess('user_id');
                            $data['modified_date'] = date('Y-m-d H:i:s');
                            $data['status']= STATUS_CLOSED;
                            $this->mdl_general->update('ar_creditnote_header', array('creditnote_id' => $creditnote_id), $data);

                            //Insert Log
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Credit Note';
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $creditnote_id;
                            $data_log['feature_id'] = Feature::FEATURE_AR_CREDIT_NOTE;
                            $data_log['action_type'] = STATUS_NEW;
                            $this->db->insert('app_log', $data_log);
                        }
                    }
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be posted. Please try again later.';

                    //$this->session->set_flashdata('flash_message_class', 'danger');
                    //$this->session->set_flashdata('flash_message', 'Transaction can not be posted. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = $header['journal_no'] . ' successfully posted.';
                    $result['redirect_link'] = base_url('ar/folio/credit/3/'. $creditnote_id .'.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be posted. Please try again later.';
            }

        }

        echo json_encode($result);
    }

    #endregion

    #region Credit Note History

    public function credit_history(){
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
        $this->load->view('ar/credit/credit_history.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_credit_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_creditnote_header.company_id <='] = 0;

        $where_str = 'ar_creditnote_header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_creditnote_header.credit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_creditnote_header.credit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_creditnote_header.credit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company'];
            }
        }
        if(isset($_REQUEST['filter_reservation_code'])){
            if($_REQUEST['filter_reservation_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_reservation_code'];
            }
        }

        $joins = array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_creditnote_header.reservation_id'
        );
        $iTotalRecords = $this->mdl_finance->countJoin('ar_creditnote_header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_creditnote_header.credit_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_creditnote_header.credit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_creditnote_header.credit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'view_cs_reservation.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_creditnote_header.*, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, view_cs_reservation.reservation_code, view_cs_reservation.reservation_date, view_cs_reservation.status as reservation_status'
            ,'ar_creditnote_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart,false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/folio/credit/3/' . $row->creditnote_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('ar/credit/pdf_creditnote/' . $row->creditnote_id) . '" class="blue-ebonyclay" target="_blank">Credit Note</a> </li>';
                $btn_action .= '<li> <a href="' . base_url('ar/credit/pdf_creditvoucher/' . $row->creditnote_id) . '" class="blue-ebonyclay" target="_blank">Voucher</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->credit_no,
                dmy_from_db($row->credit_date),
                $row->reservation_code,
                $row->tenant_fullname,
                $row->company_name,
                format_num($row->credit_amount,0),
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
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    #endregion

    #region Corporate Credit Note

    public function corp_credit_manage($type = 1){
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
        $this->load->view('ar/credit/corp_credit_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_corp_credit_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();

        //$where['ar_creditnote_header.company_id >'] = 0;
        $where['ar_creditnote_header.status'] = STATUS_NEW;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_creditnote_header.credit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_creditnote_header.credit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_creditnote_header.credit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['ar_creditnote_header.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_creditnote_header as ar_creditnote_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_creditnote_header.credit_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_creditnote_header.credit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_creditnote_header.credit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ar_creditnote_header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_creditnote_header.*'
            ,'vw_ar_creditnote_header as ar_creditnote_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/credit/3/' . $row->creditnote_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL) || check_session_action($menu_id, STATUS_DELETE)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->creditnote_id . '" data-code="' . $row->credit_no . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            if($row->credit_amount > 0){
                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->creditnote_id . '" name="ischecked[]" ' . (check_session_action($menu_id, STATUS_POSTED) ? ($row->status == STATUS_NEW ? '' : 'disabled') : 'disabled') . '/>',
                    $row->credit_no,
                    dmy_from_db($row->credit_date),
                    $row->company_name,
                    format_num($row->credit_amount,0),
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
                    $row->credit_no,
                    dmy_from_db($row->credit_date),
                    $row->company_name,
                    format_num($row->credit_amount,0),
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
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function get_corp_credit_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        //$where['ar_creditnote_header.company_id >'] = 0;

        $where_str = 'ar_creditnote_header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_creditnote_header.credit_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_creditnote_header.credit_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_creditnote_header.credit_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['ms_company.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_creditnote_header as ar_creditnote_header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_creditnote_header.credit_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_creditnote_header.credit_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_creditnote_header.credit_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ms_company.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_creditnote_header.*'
            ,'vw_ar_creditnote_header as ar_creditnote_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart,false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/credit/3/' . $row->creditnote_id) . '.tpd">View</a> </li>';
            if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('ar/credit/pdf_creditnote/' . $row->creditnote_id) . '/7.tpd" class="blue-ebonyclay" target="_blank">Credit Note</a> </li>';
                $btn_action .= '<li> <a href="' . base_url('ar/credit/pdf_creditvoucher/' . $row->creditnote_id) . '/7.tpd" class="blue-ebonyclay" target="_blank">Voucher</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->credit_no,
                dmy_from_db($row->credit_date),
                $row->company_name,
                format_num($row->credit_amount,0),
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
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function corp_credit_form($creditnote_id = 0){
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

        $data['creditnote_id'] = $creditnote_id;

        if($creditnote_id > 0){
            $joins = array('ms_company' => 'ms_company.company_id = ar_creditnote_header.company_id');
            $qry = $this->mdl_finance->getJoin('ar_creditnote_header.*, ms_company.company_name'
                ,'ar_creditnote_header', $joins, array('creditnote_id' => $creditnote_id));

            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                $detail_joins = array('ar_invoice_detail' => 'ar_invoice_detail.invdetail_id = bill.invdetail_id',
                                      'ar_invoice_header' => 'ar_invoice_detail.inv_id = ar_invoice_header.inv_id');
                $data['detail']  = $this->mdl_finance->getJoin("bill.*, ar_invoice_header.inv_no, ar_invoice_detail.description ","ar_creditnote_detail AS bill", $detail_joins, array('creditnote_id' => $creditnote_id));
            }else{
                $data['creditnote_id'] = 0;
            }
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/credit/corp_credit_form', $data);
        $this->load->view('layout/footer');
    }

    public function submit_corp_credit(){
        $valid = true;

        if(isset($_POST)){
            $creditnote_id = $_POST['creditnote_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['credit_date'] = dmy_to_ymd($_POST['credit_date']);
            $data['company_id'] = $_POST['company_id'];
            $data['reservation_id'] = $_POST['reservation_id'];;
            $data['credit_remark'] = $_POST['credit_remark'];
            $data['credit_amount'] = $_POST['grandtotal_credit'];

            if($creditnote_id > 0){
                $qry = $this->db->get_where('ar_creditnote_header', array('creditnote_id' => $creditnote_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['credit_date']);
                $arr_date_old = explode('-', ymd_from_db($row->credit_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['credit_no'] = $this->generate_no($data, $data['credit_date']);

                    if($data['credit_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Unique no can not be generated.');
                    }
                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = $server_date;

                    $this->mdl_general->update('ar_creditnote_header', array('creditnote_id' => $creditnote_id), $data);

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';
                        $valid = $this->insertCreditEntries($creditnote_id, true);

                        //echo '<br>step 5 update';
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }
                }
            }
            else {
                $data['credit_no'] = $this->generate_no($data, $data['credit_date']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                if($data['credit_no'] != ''){
                    $this->db->insert('ar_creditnote_header', $data);
                    $creditnote_id = $this->db->insert_id();

                    if($creditnote_id > 0){
                        $valid = $this->insertCreditEntries($creditnote_id, true);

                        if($valid){
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Credit Note';
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $creditnote_id;
                            $data_log['feature_id'] = Feature::FEATURE_AR_CREDIT_NOTE;
                            $data_log['action_type'] = STATUS_NEW;
                            $this->db->insert('app_log', $data_log);

                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Transaction successfully created.');
                        }

                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = false;
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
                redirect(base_url('ar/corporate_bill/credit/3/' . $creditnote_id . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('ar/corporate_bill/credit/1.tpd'),true);
                }
                else {
                    redirect(base_url('ar/corporate_bill/credit/3/' . $creditnote_id . '.tpd'));
                }
            }
        }
    }

    public function xposting_corp_credit_by_id(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $creditnote_id = 0;

        if(isset($_POST['creditnote_id'])){
            $creditnote_id = $_POST['creditnote_id'];
        }

        if($creditnote_id > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            //SALES
            //  AR
            $detail = array();

            $totalDebit = 0;
            $totalCredit = 0;

            $qryHeader = $this->db->get_where('ar_creditnote_header', array('creditnote_id' => $creditnote_id));
            if($qryHeader->num_rows() > 0){
                $head = $qryHeader->row();
                $journal_note = $head->credit_remark;

                $qryDetails = $this->db->query('SELECT det.*,iv.coa_id FROM ar_creditnote_detail det
                                            JOIN ar_invoice_detail iv On iv.invdetail_id = det.invdetail_id
                                            WHERE det.creditnote_id = ' . $creditnote_id);

                if($qryDetails->num_rows() > 0){
                    $taxVAT = tax_vat();

                    $invoice_list = array();
                    foreach($qryDetails->result_array() as $det){
                        $cndetail_id = $det['cn_detail_id'];

                        $subs =  $this->db->get_where('ar_creditnote_detail_sub', array('cn_detail_id' => $cndetail_id));
                        if($subs->num_rows() > 0){
                            foreach($subs->result_array() as $sub){
                                $rowdet = array();
                                $rowdet['coa_id'] = $sub['coa_id'];
                                $rowdet['coa_code'] = $sub['coa_code'];
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $journal_note;
                                $rowdet['journal_debit'] = $sub['credit_amount'];
                                $rowdet['journal_credit'] = 0;
                                $rowdet['reference_id'] = 0;
                                $rowdet['transtype_id'] = $det['transtype_id'];

                                array_push($detail, $rowdet);

                                $totalDebit += $sub['credit_amount'];
                            }
                        }else{
                            $rowdet = array();
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $journal_note;
                            $rowdet['journal_debit'] = $det['credit_amount'];
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = 0;
                            $rowdet['transtype_id'] = $det['transtype_id'];

                            if($det['is_tax'] <= 0){
                                $rowdet['coa_id'] = $det['coa_id'];
                            }else{
                                if(isset($taxVAT['coa_id'])){
                                    $rowdet['coa_id'] = $taxVAT['coa_id'];
                                }
                            }

                            if($rowdet['coa_id'] > 0){
                                array_push($detail, $rowdet);
                                $totalDebit += $det['credit_amount'];
                            }
                        }

                        //UPDATE INV_DETAIL PAID
                        $res = $this->allocateCreditNoteToInvoiceDetail($det, $head->company_id, $head->reservation_id);
                        $valid = $res['valid'];
                        $invoice_list = $res['invoice_list'];
                    }

                    //---------------------------------------------------
                    //UPDATE INVOICE HEADER STATUS TO CLOSED IF FULL PAID
                    //---------------------------------------------------
                    if(count($invoice_list) > 0){
                        foreach ($invoice_list as $key => $value){
                            //$invoice = $this->db->get_where('view_ar_unpaid_invoice',array('inv_id'=> $key));
                            $invoice = $this->db->query('SELECT ISNULL(SUM(pending_amount),0) as pending_amount, ISNULL(SUM(pending_tax),0) as pending_tax FROM view_ar_unpaid_invoice WHERE inv_id = ' . $key);
                            if($invoice->num_rows() > 0){
                                $invoice = $invoice->row();
                                $pending = round($invoice->pending_amount + $invoice->pending_tax,0);
                                //$debug .= '<<pending>> ' . $pending;
                                //echo '<<pending>> ' . $key . ' => ' . $pending . ' -' . $value;
                                $pending = round($pending - $value);
                                if($pending <= 0){
                                    $this->mdl_general->update('ar_invoice_header',array('inv_id' => $key), array('status' => STATUS_CLOSED));
                                }
                            }else{
                                $this->mdl_general->update('ar_invoice_header',array('inv_id' => $key), array('status' => STATUS_CLOSED));
                            }
                        }
                    }
                    //---------------------------------------------------
                }

                if($totalDebit > 0 && $valid){
                    if($totalDebit == $head->credit_amount){
                        //AR
                        $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                        if($head->reservation_id > 0){
                            $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                        }
                        if($specAR['coa_id'] > 0){
                            $rowdet = array();
                            $rowdet['coa_id'] = $specAR['coa_id'];
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $journal_note;
                            $rowdet['journal_debit'] = 0;
                            $rowdet['journal_credit'] = $totalDebit;
                            $rowdet['reference_id'] = 0;
                            $rowdet['transtype_id'] = $specAR['transtype_id'];

                            array_push($detail, $rowdet);

                            $totalCredit = $totalDebit;
                        }
                    }

                    //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                    if($totalDebit == $totalCredit){
                        $posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : ymd_from_db($head->credit_date);

                        $header = array();
                        $header['journal_no'] = $head->credit_no;
                        $header['journal_date'] = $posting_date;
                        $header['journal_remarks'] = $head->credit_remark;
                        $header['modul'] = GLMOD::GL_MOD_AR;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = '';

                        $valid = $this->mdl_finance->postJournal($header,$detail);

                        if($valid){
                            $data['modified_by'] = my_sess('user_id');
                            $data['modified_date'] = date('Y-m-d H:i:s');
                            $data['status']= STATUS_CLOSED;
                            $this->mdl_general->update('ar_creditnote_header', array('creditnote_id' => $creditnote_id), $data);

                            //Insert Log
                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Credit Note';
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $creditnote_id;
                            $data_log['feature_id'] = Feature::FEATURE_AR_CREDIT_NOTE;
                            $data_log['action_type'] = STATUS_NEW;
                            $this->db->insert('app_log', $data_log);
                        }
                    }
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be posted. Please try again later.';

                    //$this->session->set_flashdata('flash_message_class', 'danger');
                    //$this->session->set_flashdata('flash_message', 'Transaction can not be posted. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = $header['journal_no'] . ' successfully posted.';
                    $result['redirect_link'] = base_url('ar/corporate_bill/credit/3/'. $creditnote_id .'.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be posted. Please try again later.';
            }

        }

        echo json_encode($result);
    }

    private function allocateCreditNoteToInvoiceDetail($cndet = array(), $company_id = 0, $reservation_id = 0, $invoice_list = array()){
        $result = array();
        $result['valid'] = false;
        $result['invoice_list'] = array();
        $result['debug'] = '';

        $valid = true;
        $debug = '';

        if(isset($cndet) && ($company_id > 0 || $reservation_id > 0)){
            $availableAmount = $cndet['credit_amount'];
            $isTax = $cndet['is_tax'] > 0 ? true : false;

            //Allocate to CS Receipt Allocation
            $unpaids = $this->db->query('SELECT * FROM fxnARUnpaidInvoiceDetail(' . $company_id . ',' . $reservation_id . ') WHERE invdetail_id = ' . $cndet['invdetail_id']);
            if($unpaids->num_rows() > 0){
                foreach($unpaids->result_array() as $bill){
                    $paidAmount = 0;
                    $paidTax = 0;

                    //AMOUNT
                    if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                        if(!$isTax){
                            if($valid){
                                $paidAmount = $availableAmount;
                                //---------------------
                                //UPDATE INVOICE DETAIL
                                //---------------------
                                $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $cndet['invdetail_id']));
                                if($invdetail->num_rows() > 0){
                                    if($bill['pending_amount'] <= $availableAmount){
                                        $alloc_amount = $bill['pending_amount'];
                                    }else{
                                        $alloc_amount = $availableAmount;
                                    }
                                    //Deduct available amount
                                    $availableAmount -= $alloc_amount;

                                    $invdetail = $invdetail->row();
                                    $alloc_amount = $invdetail->paid_amount + $alloc_amount;

                                    $update = array();
                                    if($alloc_amount <= $invdetail->amount){
                                        $update['paid_amount'] = $alloc_amount;
                                    }else{
                                        $update['paid_amount'] = $invdetail->amount;
                                    }
                                    $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $cndet['invdetail_id']), $update);

                                    if(!isset($invoice_list[$bill['inv_id']])){
                                        //echo 'A '. $paidAmount;
                                        $invoice_list[$bill['inv_id']] = round($paidAmount);
                                    }else {
                                        $invoice_list[$bill['inv_id']] += round($paidAmount);
                                    }

                                }
                                //---------------------
                            }
                        }
                    }

                    //TAX
                    if($bill['pending_tax'] > 0 && $availableAmount > 0 && $valid){
                        if($isTax){
                            if($valid){
                                $paidTax = $availableAmount;
                                //---------------------
                                //UPDATE INVOICE TAX DETAIL
                                //---------------------
                                $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $cndet['invdetail_id']));
                                if($invdetail->num_rows() > 0){
                                    if($bill['pending_tax'] <= $availableAmount){
                                        $alloc_amount = $bill['pending_tax'];
                                    }else{
                                        $alloc_amount = $availableAmount;
                                    }

                                    //Deduct available amount
                                    $availableAmount -= $alloc_amount;

                                    $invdetail = $invdetail->row();
                                    $alloc_amount = $invdetail->paid_tax + $alloc_amount;

                                    $update = array();
                                    if($alloc_amount <= $invdetail->tax){
                                        $update['paid_tax'] = $alloc_amount;
                                    }else{
                                        $update['paid_tax'] = $invdetail->tax;
                                    }
                                    $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $cndet['invdetail_id']), $update);

                                    /*
                                    $found = false;
                                    foreach($invoice_list as $key => $value){
                                        if($key == $bill['inv_id']){
                                            $found = true;
                                            break;
                                        }
                                    }
                                    */

                                    if(!isset($invoice_list[$bill['inv_id']])){
                                        //echo 'B '. $paidTax;
                                        $invoice_list[$bill['inv_id']] = round($paidTax);
                                    }else {
                                        $invoice_list[$bill['inv_id']] += round($paidTax);
                                    }

                                }
                                //---------------------
                            }
                        }
                    }
                }

                //---------------------------------------------------
                //UPDATE INVOICE HEADER STATUS TO CLOSED IF FULL PAID
                //---------------------------------------------------
                /*
                if(count($invoice_list) > 0){
                    foreach($invoice_list as $inv_id){
                        $invoice = $this->db->get_where('view_ar_unpaid_invoice',array('inv_id'=>$inv_id));
                        if($invoice->num_rows() <= 0){
                            $this->mdl_general->update('ar_invoice_header',array('inv_id' => $inv_id), array('status' => STATUS_CLOSED));
                        }
                    }
                }
                */
                //---------------------------------------------------

            }
        }

        $result['invoice_list'] = $invoice_list;
        $result['valid'] = $valid;
        $result['debug'] = $debug;

        return $result;
    }

    private function allocateCreditNoteToInvoiceDetail_v1($cndet = array(), $company_id = 0, $reservation_id = 0){
        $result = array();
        $result['valid'] = false;
        $result['invoice_list'] = array();
        $result['debug'] = '';

        $valid = true;
        $debug = '';
        if(isset($cndet) && $company_id > 0){
            $availableAmount = $cndet['credit_amount'];
            $isTax = $cndet['is_tax'] > 0 ? true : false;

            $invoice_list = array();

            //Allocate to CS Receipt Allocation
            $unpaids = $this->db->query('SELECT * FROM fxnARUnpaidInvoiceDetail(' . $company_id . ',' . $reservation_id . ')');
            if($unpaids->num_rows() > 0){
                foreach($unpaids->result_array() as $bill){
                    $paidAmount = 0;
                    $paidTax = 0;

                    //AMOUNT
                    if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                        if(!$isTax){
                            if($valid){
                                $paidAmount = $availableAmount;

                                //---------------------
                                //UPDATE INVOICE DETAIL
                                //---------------------
                                $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $cndet['invdetail_id']));
                                if($invdetail->num_rows() > 0){
                                    if($bill['pending_amount'] <= $availableAmount){
                                        $alloc_amount = $bill['pending_amount'];
                                    }else{
                                        $alloc_amount = $availableAmount;
                                    }
                                    //Deduct available amount
                                    $availableAmount -= $alloc_amount;

                                    $invdetail = $invdetail->row();
                                    $alloc_amount = $invdetail->paid_amount + $alloc_amount;

                                    $update = array();
                                    if($alloc_amount <= $invdetail->amount){
                                        $update['paid_amount'] = $alloc_amount;
                                    }else{
                                        $update['paid_amount'] = $invdetail->amount;
                                    }
                                    $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $cndet['invdetail_id']), $update);

                                    if(!isset($invoice_list[$bill['inv_id']])){
                                        //echo 'A '. $paidAmount;
                                        $invoice_list[$bill['inv_id']] = round($paidAmount);
                                    }else {
                                        $invoice_list[$bill['inv_id']] += round($paidAmount);
                                    }
                                }
                                //---------------------
                            }
                        }
                    }

                    //TAX
                    if($bill['pending_tax'] > 0 && $availableAmount > 0 && $valid){
                        if($isTax){
                            if($valid){
                                $paidTax = $availableAmount;

                                //---------------------
                                //UPDATE INVOICE TAX DETAIL
                                //---------------------
                                $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $cndet['invdetail_id']));
                                if($invdetail->num_rows() > 0){
                                    if($bill['pending_tax'] <= $availableAmount){
                                        $alloc_amount = $bill['pending_tax'];
                                    }else{
                                        $alloc_amount = $availableAmount;
                                    }

                                    //Deduct available amount
                                    $availableAmount -= $alloc_amount;

                                    $invdetail = $invdetail->row();
                                    $alloc_amount = $invdetail->paid_tax + $alloc_amount;

                                    $update = array();
                                    if($alloc_amount <= $invdetail->tax){
                                        $update['paid_tax'] = $alloc_amount;
                                    }else{
                                        $update['paid_tax'] = $invdetail->tax;
                                    }
                                    $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $cndet['invdetail_id']), $update);

                                    if(!isset($invoice_list[$bill['inv_id']])){
                                        //echo 'B '. $paidTax;
                                        $invoice_list[$bill['inv_id']] = round($paidTax);
                                    }else {
                                        $invoice_list[$bill['inv_id']] += round($paidTax);
                                    }
                                }
                                //---------------------
                            }
                        }
                    }

                }
            }
        }

        $result['invoice_list'] = $invoice_list;
        $result['valid'] = $valid;
        $result['debug'] = $debug;

        return $result;
    }

    /*POSTING CREDITS*/
    public function posting_corp_credits(){
        $valid = true;

        if(isset($_POST)){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            //AR
            //  BANK
            if(isset($_POST['ischecked'])){
                $rowcount = 0;

                foreach( $_POST['ischecked'] as $creditnote_id){
                    //echo '[posting_credits] ... ' . $creditnote_id;

                    if($valid){
                        //SALES
                        //  AR
                        $detail = array();

                        $totalDebit = 0;
                        $totalCredit = 0;

                        $qryHeader = $this->db->get_where('ar_creditnote_header', array('creditnote_id' => $creditnote_id));
                        if($qryHeader->num_rows() > 0){
                            $head = $qryHeader->row();
                            $journal_note = $head->credit_remark;

                            $qryDetails = $this->db->query('SELECT det.*,iv.coa_id FROM ar_creditnote_detail det
                                            JOIN ar_invoice_detail iv On iv.invdetail_id = det.invdetail_id
                                            WHERE det.creditnote_id = ' . $creditnote_id);

                            if($qryDetails->num_rows() > 0){
                                $taxVAT = tax_vat();

                                foreach($qryDetails->result_array() as $det){
                                    $cndetail_id = $det['cn_detail_id'];

                                    $subs =  $this->db->get_where('ar_creditnote_detail_sub', array('cn_detail_id' => $cndetail_id));
                                    if($subs->num_rows() > 0){
                                        foreach($subs->result_array() as $sub){
                                            $rowdet = array();
                                            $rowdet['coa_id'] = $sub['coa_id'];
                                            $rowdet['coa_code'] = $sub['coa_code'];
                                            $rowdet['dept_id'] = 0;
                                            $rowdet['journal_note'] = $journal_note;
                                            $rowdet['journal_debit'] = $sub['credit_amount'];
                                            $rowdet['journal_credit'] = 0;
                                            $rowdet['reference_id'] = 0;
                                            $rowdet['transtype_id'] = $det['transtype_id'];

                                            array_push($detail, $rowdet);

                                            $totalDebit += $sub['credit_amount'];
                                        }
                                    }else{
                                        $rowdet = array();
                                        $rowdet['dept_id'] = 0;
                                        $rowdet['journal_note'] = $journal_note;
                                        $rowdet['journal_debit'] = $det['credit_amount'];
                                        $rowdet['journal_credit'] = 0;
                                        $rowdet['reference_id'] = 0;
                                        $rowdet['transtype_id'] = $det['transtype_id'];

                                        if($det['is_tax'] <= 0){
                                            $rowdet['coa_id'] = $det['coa_id'];
                                        }else{
                                            if(isset($taxVAT['coa_id'])){
                                                $rowdet['coa_id'] = $taxVAT['coa_id'];
                                            }
                                        }

                                        if($rowdet['coa_id'] > 0){
                                            array_push($detail, $rowdet);
                                            $totalDebit += $det['credit_amount'];
                                        }
                                    }

                                    //UPDATE INV_DETAIL PAID
                                    $valid = $this->allocateCreditNoteToInvoiceDetail($det, $head->company_id, $head->reservation_id);
                                }
                            }

                            if($totalDebit > 0 && $valid){
                                if($totalDebit == $head->credit_amount){
                                    //AR
                                    $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                                    if($head->reservation_id > 0){
                                        $specAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                                    }
                                    if($specAR['coa_id'] > 0){
                                        $rowdet = array();
                                        $rowdet['coa_id'] = $specAR['coa_id'];
                                        $rowdet['dept_id'] = 0;
                                        $rowdet['journal_note'] = $journal_note;
                                        $rowdet['journal_debit'] = 0;
                                        $rowdet['journal_credit'] = $totalDebit;
                                        $rowdet['reference_id'] = 0;
                                        $rowdet['transtype_id'] = $specAR['transtype_id'];

                                        array_push($detail, $rowdet);

                                        $totalCredit = $totalDebit;
                                    }
                                }

                                //echo '<br>[posting_deposits] B ... ' . $totalDebit;
                                if($totalDebit == $totalCredit){
                                    $posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : ymd_from_db($head->credit_date);

                                    $header = array();
                                    $header['journal_no'] = $head->credit_no;
                                    $header['journal_date'] = $posting_date;
                                    $header['journal_remarks'] = $head->credit_remark;
                                    $header['modul'] = GLMOD::GL_MOD_AR;
                                    $header['journal_amount'] = $totalDebit;
                                    $header['reference'] = '';

                                    $valid = $this->mdl_finance->postJournal($header,$detail);

                                    if($valid){
                                        $data['modified_by'] = my_sess('user_id');
                                        $data['modified_date'] = date('Y-m-d H:i:s');
                                        $data['status']= STATUS_CLOSED;
                                        $this->mdl_general->update('ar_creditnote_header', array('creditnote_id' => $creditnote_id), $data);

                                        //Insert Log
                                        $data_log['user_id'] = my_sess('user_id');
                                        $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Credit Note ';
                                        $data_log['log_date'] = date('Y-m-d H:i:s');
                                        $data_log['reff_id'] = $creditnote_id;
                                        $data_log['feature_id'] = Feature::FEATURE_AR_CREDIT_NOTE;
                                        $data_log['action_type'] = STATUS_NEW;
                                        $this->db->insert('app_log', $data_log);

                                        $rowcount++;
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                $this->session->set_flashdata('flash_message', 'No transactions selected for posting.');
                $this->session->set_flashdata('flash_message_class', 'warning');
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
                    $this->session->set_flashdata('flash_message', $rowcount . ' transaction(s) successfully posted.');
                    $this->session->set_flashdata('flash_message_class', 'success');

                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                redirect(base_url('ar/corporate_bill/credit/1.tpd'));
            }
            else {
                redirect(base_url('ar/corporate_bill/credit/1.tpd'));
            }
        }
    }

    #endregion

    #region Print

    public function pdf_creditvoucher($creditnote_id = 0) {
        if ($creditnote_id > 0) {
            $qry = $this->db->get_where('ar_creditnote_header', array('creditnote_id' => $creditnote_id));
            if ($qry->num_rows() > 0) {
                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                $data['row'] = $qry->row();
                $data['qry_det'] =  $this->db->order_by('journal_credit','ASC')->get_where('view_get_journal_detail', array('journal_no' => $data['row']->credit_no));

                $this->load->view('ar/credit/pdf_jv_credit.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->credit_no . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

    public function pdf_creditnote($creditnote_id = 0) {
        if ($creditnote_id > 0) {
            $qry = $this->db->get_where('ar_creditnote_header', array('creditnote_id' => $creditnote_id));
            if ($qry->num_rows() > 0) {
                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if($profile->num_rows() > 0){
                    $data['profile'] = $profile->row_array();
                }

                $data['row'] = $qry->row();

                $qry = "SELECT ar_creditnote_detail.*, ar_invoice_detail.description  FROM ar_creditnote_detail
                        JOIN ar_invoice_detail ON ar_invoice_detail.invdetail_id = ar_creditnote_detail.invdetail_id
                        WHERE ar_creditnote_detail.creditnote_id = " . $creditnote_id;
                    $data['qry_det'] =  $this->db->query($qry);

                $this->load->view('ar/credit/pdf_credit_note.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->credit_no . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

    #endregion


    #region Modal Lookup Form

    public function ajax_pending_bill(){
        $this->load->view('ar/credit/ajax_modal_bill');
    }

    public function get_modal_bill($num_index = 0, $reservation_id = 0, $creditnote_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $server_date = date('Y-m-d');

        if(isset($_REQUEST['filter_code'])){
            if($_REQUEST['filter_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_code'];
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['view_cs_reservation.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $where_str = ''; //' view_cs_reservation.status IN(' . ORDER_STATUS::CHECKOUT . ') ';

        $joins = array("view_cs_reservation"=>"view_cs_reservation.reservation_id = ar.reservation_id");
        $iTotalRecords = $this->mdl_finance->countJoin("fxnARPendingByDate('" . $server_date ."') AS ar", $joins, $where, $like, $where_str);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'view_cs_reservation.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'view_cs_reservation.tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_cs_reservation.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin("ar.*, view_cs_reservation.reservation_code, view_cs_reservation.tenant_fullname, view_cs_reservation.company_name, view_cs_reservation.reservation_type ","fxnARPendingByDate('" . $server_date . "') AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, "", array(), $where_str);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $pending_credit = 0;

            $existcredit = $this->db->query('SELECT ISNULL(SUM(credit_amount),0) as credit_total FROM ar_creditnote_header
                                               WHERE reservation_id = ' . $row->reservation_id . ' AND status = ' . STATUS_NEW . '
                                               AND creditnote_id <> ' . $creditnote_id);
            if($existcredit->num_rows() > 0){
                $pending_credit = $existcredit->row()->credit_total;
            }

            $pending_total = $row->pending_amount - $pending_credit;

            $attr = '';
            $attr .= ' data-reservation-id="' . $row->reservation_id . '" ';
            $attr .= ' data-reservation-code="' . $row->reservation_code . '" ';
            $attr .= ' data-tenant-name="' . $row->tenant_fullname . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-amount="' . $pending_total . '"';
            $attr .= ' data-index="' . $num_index . '" ';

            $text = "";
            if ($reservation_id == $row->reservation_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }else{
                $text = "Select";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-reservation" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            if($pending_total > 0){
                $records["data"][] = array(
                    $row->reservation_code,
                    RES_TYPE::caption($row->reservation_type),
                    $row->tenant_fullname,
                    $row->company_name,
                    format_num($pending_total,0),
                    $btn
                );
                $i++;
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_pending_bill_detail(){
        $result = '';

        $reservation_id = 0;

        if(isset($_POST['reservation_id'])){
            $reservation_id = $_POST['reservation_id'];
        }

        if($reservation_id > 0){
            $this->load->model('finance/mdl_finance');

            $joins = array("ms_transtype"=>"ms_transtype.transtype_id = bill.transtype_id",
                           "ms_unit" => "ms_unit.unit_id = bill.unit_id");
            $qry = $this->mdl_finance->getJoin("bill.*, ms_transtype.transtype_name, ms_transtype.transtype_desc,ms_unit.unit_code ","fxnARUnpaidBillByID('" . $reservation_id . "') AS bill", $joins, array('pending_amount > ' => 0), array(), 'bill_date, is_other_charge');

            if($qry->num_rows() > 0){
                foreach($qry->result_array() as $bill){
                    $result .= '<tr id="parent_' . $bill['billdetail_id'] .'">
                                 <td style="vertical-align:middle;" class="text-center">
                                    <input type="hidden" name="cn_detail_id[]" value="">
                                    <input type="hidden" name="billdetail_id[]" value="' . $bill['billdetail_id'] . '">
                                    <input type="hidden" name="transtype_id[]" value="' . $bill['transtype_id'] . '">
                                    <span class="text-center">' . $bill['journal_no'] . '</span>
                                 </td>
                                 <td style="vertical-align:middle;" class="text-center">
                                    <span class="text-center">' . $bill['unit_code'] . '</span>
                                 </td>
                                 <td style="vertical-align:middle;">
                                    <span class="text-center">' . $bill['transtype_desc'] . '</span>
                                 </td>
                                 <td style="vertical-align:middle;" class="control-label">
                                    <input type="text" name="base_amount[]" value="' . $bill['pending_amount'] .'" class="form-control text-right mask_currency" readonly>
                                 </td>
                                 <td style="vertical-align:middle;" class="control-label">
                                    <input type="text" name="credit_amount[]" value="0" class="form-control text-right mask_currency" >
                                 </td>
                                 <td style="vertical-align:middle;">
                                    <a bill-detail-id="' . $bill['billdetail_id'] .'" data-placement="top" data-container="body" class="btn btn-xs green-meadow add_amount " href="javascript:;"><i class="fa fa-plus add_amount_plus "></i><i class="fa fa-minus add_amount_minus hide"></i>
                                    </a>
                                    <a bill-detail-id="' . $bill['billdetail_id'] .'" data-placement="top" data-container="body" class="btn btn-xs purple-plum add_sub_detail hide" href="javascript:;"><i class="fa fa-share-alt"></i>
                                    </a>
                                </td>
                                </tr>';

                }
            }
        }

        echo $result;
    }

    public function ajax_corp_pending_bill(){
        $this->load->view('ar/credit/ajax_corp_modal_bill');
    }

    public function get_corp_modal_bill($num_index = 0, $company_id = 0, $creditnote_id = 0, $reservation_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $server_date = date('Y-m-d');

        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['ms_company.company_name'] = $_REQUEST['filter_company'];
            }
        }

        $where_str = ''; //' view_cs_reservation.status IN(' . ORDER_STATUS::CHECKOUT . ') ';

        $joins = array("ms_company"=>"ms_company.company_id = ar.company_id");
        $iTotalRecords = $this->mdl_finance->countJoin("fxnARPendingByDateCorp(GETDATE()) AS ar", $joins, $where, $like, $where_str);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ms_company.company_name';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ms_company.company_name ' . $_REQUEST['order'][0]['dir'];
            }

        }

        $qry = $this->mdl_finance->getJoin("ar.*, ms_company.company_name","fxnARPendingByDateCorp(GETDATE()) AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, "", array(), $where_str);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $pending_credit = 0;

            $existcredit = $this->db->query('SELECT ISNULL(SUM(credit_amount),0) as credit_total FROM ar_creditnote_header
                                               WHERE company_id = ' . $row->company_id . ' AND status = ' . STATUS_NEW . '
                                               AND creditnote_id <> ' . $creditnote_id);
            if($existcredit->num_rows() > 0){
                $pending_credit = $existcredit->row()->credit_total;
            }

            $pending_total = $row->pending_total - $pending_credit;

            $attr = '';
            $attr .= ' data-company-id="' . $row->company_id . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-amount="' . $pending_total . '"';
            $attr .= ' data-index="' . $num_index . '" ';

            $text = "";
            if ($company_id == $row->company_id ) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }else{
                $text = "Select";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-company" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            if($pending_total > 0){
                $records["data"][] = array(
                    $row->company_name,
                    format_num($pending_total,0),
                    $btn
                );
                $i++;
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function xcorp_pending_bill_detail(){
        $result = '';

        $company_id = 0;
        if(isset($_POST['company_id'])){
            $company_id = $_POST['company_id'];
        }
        $reservation_id = 0;

        if($company_id > 0 || $reservation_id > 0){
            $this->load->model('finance/mdl_finance');

            $joins = array();
            $qry = $this->mdl_finance->getJoin("bill.*","fxnARUnpaidInvoiceDetail(" . $company_id . "," . $reservation_id . ") AS bill", $joins, array('pending_amount > ' => 0), array(), 'inv_no');

            if($qry->num_rows() > 0){
                foreach($qry->result_array() as $bill){
                    if($bill['pending_amount'] > 0){
                        $existcredit = $this->db->query('SELECT ISNULL(SUM(ar_creditnote_detail.credit_amount),0) as credit_amount FROM ar_creditnote_detail
                                               JOIN ar_creditnote_header ON ar_creditnote_detail.creditnote_id = ar_creditnote_header.creditnote_id
                                               WHERE ar_creditnote_detail.invdetail_id = ' . $bill['invdetail_id'] . ' AND ar_creditnote_detail.is_tax <= 0 AND ar_creditnote_header.status = ' . STATUS_NEW );
                        if($existcredit->num_rows() > 0){
                            $pending_credit = $existcredit->row()->credit_amount;
                        }

                        $pending_amount = $bill['pending_amount'] - $pending_credit;

                        if($pending_amount > 0){
                            $result .= '<tr id="parent_' . $bill['invdetail_id'] . '' . '">
                                 <td style="vertical-align:middle;" class="text-center">
                                    <input type="hidden" name="cn_detail_id[]" value="">
                                    <input type="hidden" name="invdetail_id[]" value="' . $bill['invdetail_id'] . '">
                                    <input type="hidden" name="transtype_id[]" value="">
                                    <input type="hidden" name="is_tax[]" value="0">
                                    <span class="text-center">' . $bill['inv_no'] . '</span>
                                 </td>
                                 <td style="vertical-align:middle;">
                                    <span class="text-center">' . $bill['description'] . '</span>
                                 </td>
                                 <td style="vertical-align:middle;" class="control-label">
                                    <input type="text" name="base_amount[]" value="' . $pending_amount .'" class="form-control text-right mask_currency" readonly>
                                 </td>
                                 <td style="vertical-align:middle;" class="control-label">
                                    <input type="text" name="credit_amount[]" value="0" class="form-control text-right mask_currency" >
                                 </td>
                                 <td style="vertical-align:middle;">
                                    <a inv-detail-id="' . $bill['invdetail_id'] .'" inv-is-tax="' . 0 .'" data-placement="top" data-container="body" class="btn btn-xs green-meadow add_amount " href="javascript:;"><i class="fa fa-plus add_amount_plus "></i><i class="fa fa-minus add_amount_minus hide"></i>
                                    </a>
                                    <a inv-detail-id="' . $bill['invdetail_id'] .'" inv-is-tax="' . 0 .'" data-placement="top" data-container="body" class="btn btn-xs purple-plum add_sub_detail hide" href="javascript:;"><i class="fa fa-share-alt"></i>
                                    </a>
                                </td>
                                </tr>';
                        }
                    }

                    if($bill['pending_tax'] > 0){
                        $existtax = $this->db->query('SELECT ISNULL(SUM(credit_amount),0) as credit_amount FROM ar_creditnote_detail
                                               WHERE invdetail_id = ' . $bill['invdetail_id'] . ' AND is_tax > 0 AND status = ' . STATUS_NEW );
                        if($existtax->num_rows() > 0){
                            $pending_credit = $existtax->row()->credit_amount;
                        }

                        $pending_tax = $bill['pending_tax'] - $pending_credit;

                        if($pending_tax > 0){
                            $result .= '<tr id="parent_' . $bill['invdetail_id'] . '_tax' . '">
                                 <td style="vertical-align:middle;" class="text-center">
                                    <input type="hidden" name="cn_detail_id[]" value="">
                                    <input type="hidden" name="invdetail_id[]" value="' . $bill['invdetail_id'] . '">
                                    <input type="hidden" name="transtype_id[]" value="">
                                    <input type="hidden" name="is_tax[]" value="1">
                                    <span class="text-center">' . $bill['inv_no'] . '</span>
                                 </td>
                                 <td style="vertical-align:middle;">
                                    <span class="text-center">' . '(VAT) ' . $bill['description'] . '</span>
                                 </td>
                                 <td style="vertical-align:middle;" class="control-label">
                                    <input type="text" name="base_amount[]" value="' . $pending_tax .'" class="form-control text-right mask_currency" readonly>
                                 </td>
                                 <td style="vertical-align:middle;" class="control-label">
                                    <input type="text" name="credit_amount[]" value="0" class="form-control text-right mask_currency" >
                                 </td>
                                 <td style="vertical-align:middle;">
                                    <a inv-detail-id="' . $bill['invdetail_id'] .'" inv-is-tax="' . 1 .'" data-placement="top" data-container="body" class="btn btn-xs green-meadow add_amount " href="javascript:;"><i class="fa fa-plus add_amount_plus "></i><i class="fa fa-minus add_amount_minus hide"></i>
                                    </a>
                                    <a inv-detail-id="' . $bill['invdetail_id'] .'" inv-is-tax="' . 1 .'" data-placement="top" data-container="body" class="btn btn-xs purple-plum add_sub_detail hide" href="javascript:;"><i class="fa fa-share-alt"></i>
                                    </a>
                                </td>
                                </tr>';
                        }
                    }
                }
            }
        }

        echo $result;
    }

    #endregion
}

/* End of file registration.php */
/* Location: ./application/controllers/frondesk/registration.php */