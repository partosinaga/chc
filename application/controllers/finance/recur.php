<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recur extends CI_Controller {

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

        $this->data_footer = array(
            'footer_script' => array()
        );
    }

    public function index()
    {
        $this->recur_manage();
    }

    #region Entry Journal

    public function recur_form($type = 1, $id = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;
        $data_footer = $this->data_footer;

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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        //FOOTER
        array_push($data_footer['footer_script'], base_url() . 'assets/custom/journal_form.js');

        $data['entry_id'] = $id;

        $dept = $this->mdl_general->get('ms_department', array('status ' => STATUS_NEW), array());
        $data['dept_list'] = $dept->result_array();

        if($id > 0){
            $qry = $this->db->get_where('gl_scheduleentry_header', array('entry_id' => $id));
            $data['row'] = $qry->row();

            $qry2 = $this->mdl_finance->getJoin('gl_scheduleentry_detail.*, gl_coa.coa_code, gl_coa.coa_desc','gl_scheduleentry_detail', array('gl_coa' => 'gl_coa.coa_id = gl_scheduleentry_detail.coa_id'), array('entry_id' => $id), array());
            $data['qry_det'] = $qry2->result_array();
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/journal/recur_form', $data);
        $this->load->view('layout/footer', $data_footer);
    }

    public function submit_recur(){
        $valid = true;

        if(isset($_POST)){
            $entryID = $_POST['entry_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $data['transtype_id'] = 0;
            $data['entry_startdate'] = dmy_to_ymd(trim($_POST['entry_startdate']));
            $data['entry_enddate'] = dmy_to_ymd(trim($_POST['entry_enddate']));
            $data['entry_interval'] = $_POST['entry_interval'];
            $data['entry_runday'] = $_POST['entry_runday'];
            $data['entry_remark'] = $_POST['entry_remark'];
            $data['entry_amount'] =  $_POST['total_debit'];

            unset($_POST['entry_id']);

            $arr_date = explode('-', $data['entry_startdate']);

            $nextRunDate = $arr_date[0] . '-' . $arr_date[1] . '-' . $data['entry_runday'];
            if($arr_date[2] > $data['entry_runday']){
                //echo 'A ' . $nextRunDate;
                $nextRunDate = date('Y-m-d', strtotime(date("Y-m-d", strtotime($nextRunDate)) . " +". $data['entry_interval'] . " month"));
                //echo 'B ' . $nextRunDate;
            }

            //echo 'next month -> ' . $nextMonth;

            $data['entry_nextrundate'] = $nextRunDate;

            if($entryID > 0){
                $qry = $this->db->get_where('gl_scheduleentry_header', array('entry_id' => $entryID));
                $row = $qry->row();

                $arr_date_old = explode('-', $row->entry_startdate);

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    if($row->status != STATUS_CLOSED && $row->status != STATUS_POSTED){
                        $data['entry_code'] = $this->mdl_general->generate_code(Feature::FEATURE_GL_RECURRING, $data['entry_startdate']);
                            if($data['entry_code'] == ''){
                            $valid = false;

                            $this->session->set_flashdata('flash_message_class', 'danger');
                            $this->session->set_flashdata('flash_message', 'Failed generating code.');
                        }
                    }
                }

                if($valid){
                    $this->mdl_general->update('gl_scheduleentry_header', array('entry_id' => $entryID), $data);

                    //echo '<br>step 3 update';

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';

                        $valid = $this->insertDetailEntries($entryID, $data);

                        //echo '<br>step 5 update';

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }
                }
            }
            else {
                $data['entry_code'] = $this->mdl_general->generate_code(Feature::FEATURE_GL_RECURRING, $data['entry_startdate']);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = date('Y-m-d H:i:s');

                $this->db->insert('gl_scheduleentry_header', $data);
                $entryID = $this->db->insert_id();

                if($entryID > 0){
                    $valid = $this->insertDetailEntries($entryID, $data);

                    if($valid){
                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully created.');
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
                redirect(base_url('finance/recur/recur_form/1/' . $entryID . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/recur/recur_manage/1.tpd'));
                }
                else {
                    redirect(base_url('finance/recur/recur_form/1/' . $entryID . '.tpd'));
                }
            }
        }
    }

    public function insertDetailEntries($entryHeaderID = 0, $data = array()){
        $valid = true;

        if($entryHeaderID > 0 && isset($_POST) && isset($data)){
            $detail_ids = isset($_POST['detail_id']) ? $_POST['detail_id'] : array();
            $coa_ids = isset($_POST['coa_id']) ? $_POST['coa_id'] : array();
            $debits = isset($_POST['journal_debit']) ? $_POST['journal_debit'] : array();
            $credits = isset($_POST['journal_credit']) ? $_POST['journal_credit'] : array();
            $dept = isset($_POST['dept_id']) ? $_POST['dept_id'] : array();

            if(count($detail_ids) > 0){
                //echo '<br>Count detail ' . count($dept);

                for ($i = 0; $i <= max(array_keys($detail_ids)); $i++) {
                    if($valid){
                        if(isset($detail_ids[$i])){
                            if($detail_ids[$i] <= 0){
                                $detail['entry_id'] = $entryHeaderID;
                                $detail['coa_id'] = $coa_ids[$i];
                                $detail['journal_note'] = $data['journal_remarks'];
                                $detail['journal_debit'] = $debits[$i];
                                $detail['journal_credit'] = $credits[$i];
                                $detail['dept_id'] = $dept[$i];
                                $detail['status'] = STATUS_NEW;

                                $this->db->insert('gl_scheduleentry_detail', $detail);
                                $insertID = $this->db->insert_id();

                                if($insertID <= 0){
                                    $valid = false;
                                }
                            }else{
                                $detail['journal_note'] = $data['entry_remark'];
                                $detail['journal_debit'] = $debits[$i];
                                $detail['journal_credit'] = $credits[$i];
                                $detail['dept_id'] = $dept[$i];

                                $this->mdl_general->update('gl_scheduleentry_detail', array('detail_id' => $detail_ids[$i]), $detail);
                            }
                        }
                    }else{
                        break;
                    }
                }
            }
        }

        return $valid;
    }

    public function ajax_delete_detail(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        $detailId = 0;
        if(isset($_POST['detail_id'])){
            $detailId = $_POST['detail_id'];
        }

        if($detailId > 0){
            $this->db->delete('gl_scheduleentry_detail', array('detail_id' => $detailId));

            $result['type'] = '1';
            $result['message'] = 'Successfully delete record.';
        }

        echo json_encode($result);
    }

    public function action_request(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $entry_id = $_POST['entry_id'];
        $data['status'] = $_POST['action'];
        //$data['cancel_note'] = $_POST['reason'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Recurring Entry';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $entry_id;
        $data_log['feature_id'] = Feature::FEATURE_GL_RECURRING;

        if($entry_id > 0 && $data['status'] > 0){
            $qry = $this->db->get_where('gl_scheduleentry_header', array('entry_id' => $entry_id));
            if($qry->num_rows() > 0){
                $row = $qry->row();

                //BEGIN TRANSACTION
                $this->db->trans_begin();

                if($data['status'] == STATUS_APPROVE){
                    $this->mdl_general->update('gl_scheduleentry_header', array('entry_id' => $entry_id), $data);

                    $data_log['action_type'] = STATUS_APPROVE;
                    $this->db->insert('app_log', $data_log);

                    $result['type'] = '1';
                    $result['message'] = 'Transaction successfully approved.';
                }else if($data['status'] == STATUS_CANCEL){
                    if($row->status == STATUS_CANCEL){
                        $result['type'] = '0';
                        $result['message'] = 'Transaction already canceled.';
                    }
                    else {
                        $this->mdl_general->update('gl_scheduleentry_header', array('entry_id' => $entry_id), $data);

                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['type'] = '1';
                        $result['message'] = 'Transaction successfully canceled.';
                    }
                }else if($data['status'] == STATUS_DELETE){
                    if($row->status != STATUS_DELETE){
                        $this->mdl_general->update('gl_scheduleentry_header', array('entry_id' => $entry_id), $data);

                        $data_log['action_type'] = STATUS_DELETE;
                        $this->db->insert('app_log', $data_log);

                        $result['type'] = '1';
                        $result['message'] = 'Task successfully Stopped';
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

    #endregion

    #region Manage

    public function recur_manage($type = 1, $id = 0){
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
        $this->load->view('finance/journal/recur_manage', $data);
        $this->load->view('layout/footer');
    }

    public function get_recur_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['gl_scheduleentry_header.status'] = STATUS_NEW;

        $like = array();
        if(isset($_REQUEST['filter_journal_no'])){
            if($_REQUEST['filter_journal_no'] != ''){
                $like['gl_scheduleentry_header.entry_code'] = $_REQUEST['filter_journal_no'];
            }
        }
        if(isset($_REQUEST['filter_journal_date_from'])){
            if($_REQUEST['filter_journal_datefrom'] != ''){
                $where['DATE(gl_scheduleentry_header.entry_startdate) >='] = dmy_to_ymd($_REQUEST['entry_startdate']);
            }
        }
        if(isset($_REQUEST['filter_journal_date_to'])){
            if($_REQUEST['filter_journal_date_to'] != ''){
                $where['gl_scheduleentry_header.entry_enddate <='] = dmy_to_ymd($_REQUEST['entry_enddate']);
            }
        }
        if(isset($_REQUEST['filter_remark'])){
            if($_REQUEST['filter_remark'] != ''){
                $like['gl_scheduleentry_header.entry_remark'] = $_REQUEST['filter_remark'];
            }
        }
        if(isset($_REQUEST['filter_interval'])){
            if($_REQUEST['filter_interval'] != ''){
                $like['gl_journalentry_header.entry_interval'] = $_REQUEST['filter_interval'];
            }
        }
        if(isset($_REQUEST['filter_runday'])){
            if($_REQUEST['filter_runday'] != ''){
                $like['gl_journalentry_header.entry_runday'] = $_REQUEST['filter_runday'];
            }
        }

        $iTotalRecords = $this->mdl_finance->countJoin('gl_scheduleentry_header', array(), $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'gl_scheduleentry_header.entry_code DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'gl_scheduleentry_header.entry_code ' . $_REQUEST['order'][0]['dir'];
            }

            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'gl_scheduleentry_header.entry_remark ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'gl_scheduleentry_header.entry_startdate ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'gl_scheduleentry_header.entry_enddate ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'gl_scheduleentry_header.entry_interval ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'gl_scheduleentry_header.entry_runday ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 7){
                $order = 'gl_scheduleentry_header.entry_amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('gl_scheduleentry_header.*','gl_scheduleentry_header', array(), $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_EDIT)){
                    $btn_action .= '<li> <a href="' . base_url('finance/recur/recur_form/0/' . $row->entry_id) . '.tpd">Edit</a> </li>';
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->entry_id . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
                else {
                    $btn_action .= '<li> <a href="' . base_url('finance/recur/recur_form/0/' . $row->entry_id) . '.tpd">View</a> </li>';
                }
            }else if($row->status == STATUS_CANCEL){
                $btn_action .= '<li> <a href="' . base_url('finance/recur/recur_form/0/' . $row->entry_id) . '.tpd">View</a> </li>';
            }

            if($row->entry_amount > 0){
                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->entry_id . '" name="ischecked[]"/>',
                    $row->entry_code,
                    nl2br($row->entry_remark),
                    ymd_to_dmy($row->entry_startdate),
                    ymd_to_dmy($row->entry_enddate),
                    $row->entry_interval,
                    $row->entry_runday,
                    ymd_to_dmy($row->entry_nextrundate),
                    format_num($row->entry_amount,0),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					    </ul>
				    </div>'
                );
            }else{
                $records["data"][] = array(
                    '',
                    $row->entry_code,
                    nl2br($row->entry_remark),
                    ymd_to_dmy($row->entry_startdate),
                    ymd_to_dmy($row->entry_enddate),
                    $row->entry_interval,
                    $row->entry_runday,
                    ymd_to_dmy($row->entry_nextrundate),
                    format_num($row->entry_amount,0),
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

        echo json_encode($records);
    }

    public function posting_recur(){
        $valid = true;

        if(isset($_POST)){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $data['status']= STATUS_CLOSED;

            if(isset($_POST['ischecked'])){
                $rowcount = count($_POST['ischecked']);

                foreach( $_POST['ischecked'] as $val){
                    $this->mdl_general->update('gl_scheduleentry_header', array('entry_id' => $val), $data);
                }

                $this->session->set_flashdata('flash_message', 'Recurring transaction(s) successfully scheduled.');
                $this->session->set_flashdata('flash_message_class', 'success');
            }else{
                $this->session->set_flashdata('flash_message', 'No Recurring transaction(s) selected for scheduling.');
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
                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                redirect(base_url('finance/recur/recur_manage/1.tpd'));
            }
            else {
                redirect(base_url('finance/recur/recur_manage/1.tpd'));
            }
        }
    }

    public function ajax_posting_journal_by_id(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $entryID = 0;

        if(isset($_POST['entry_id'])){
            $entryID = $_POST['entry_id'];
        }

        if($entryID > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $data['status']= STATUS_CLOSED;
            $this->mdl_general->update('gl_scheduleentry_header', array('entry_id' => $entryID), $data);

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Recurring transaction can not be started. Please try again later.';

                    //$this->session->set_flashdata('flash_message_class', 'danger');
                    //$this->session->set_flashdata('flash_message', 'Transaction can not be posted. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = 'Recurring transaction successfully scheduled.';
                    $result['redirect_link'] = base_url('finance/recur/recur_form/0/'. $entryID .'.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Recurring transaction can not be started. Please try again later.';
            }

        }

        echo json_encode($result);
    }

    #endregion

    #region History

    public function recur_history($type = 1, $id = 0){
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
        $this->load->view('finance/journal/recur_list.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_recur_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['gl_scheduleentry_header.status'] = STATUS_CLOSED;

        $like = array();
        if(isset($_REQUEST['filter_journal_no'])){
            if($_REQUEST['filter_journal_no'] != ''){
                $like['gl_scheduleentry_header.entry_code'] = $_REQUEST['filter_journal_no'];
            }
        }
        if(isset($_REQUEST['filter_journal_date_from'])){
            if($_REQUEST['filter_journal_datefrom'] != ''){
                $where['DATE(gl_scheduleentry_header.entry_startdate) >='] = dmy_to_ymd($_REQUEST['entry_startdate']);
            }
        }
        if(isset($_REQUEST['filter_journal_date_to'])){
            if($_REQUEST['filter_journal_date_to'] != ''){
                $where['gl_scheduleentry_header.entry_enddate <='] = dmy_to_ymd($_REQUEST['entry_enddate']);
            }
        }
        if(isset($_REQUEST['filter_remark'])){
            if($_REQUEST['filter_remark'] != ''){
                $like['gl_scheduleentry_header.entry_remark'] = $_REQUEST['filter_remark'];
            }
        }
        if(isset($_REQUEST['filter_interval'])){
            if($_REQUEST['filter_interval'] != ''){
                $like['gl_journalentry_header.entry_interval'] = $_REQUEST['filter_interval'];
            }
        }
        if(isset($_REQUEST['filter_runday'])){
            if($_REQUEST['filter_runday'] != ''){
                $like['gl_journalentry_header.entry_runday'] = $_REQUEST['filter_runday'];
            }
        }

        $iTotalRecords = $this->mdl_finance->countJoin('gl_scheduleentry_header', array(), $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'gl_scheduleentry_header.entry_code DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'gl_scheduleentry_header.entry_code ' . $_REQUEST['order'][0]['dir'];
            }

            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'gl_scheduleentry_header.entry_remark ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'gl_scheduleentry_header.entry_startdate ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'gl_scheduleentry_header.entry_enddate ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'gl_scheduleentry_header.entry_interval ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'gl_scheduleentry_header.entry_runday ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 7){
                $order = 'gl_scheduleentry_header.entry_amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('gl_scheduleentry_header.*','gl_scheduleentry_header', array(), $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_EDIT)){
                    $btn_action .= '<li> <a href="' . base_url('finance/recur/recur_form/0/' . $row->entry_id) . '.tpd">Edit</a> </li>';
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->entry_id . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
                else {
                    $btn_action .= '<li> <a href="' . base_url('finance/recur/recur_form/0/' . $row->entry_id) . '.tpd">View</a> </li>';
                }
            }else if($row->status == STATUS_CANCEL){
                $btn_action .= '<li> <a href="' . base_url('finance/recur/recur_form/0/' . $row->entry_id) . '.tpd">View</a> </li>';
            }else if($row->status == STATUS_CLOSED){
                $btn_action .= '<li> <a href="' . base_url('finance/recur/recur_form/0/' . $row->entry_id) . '.tpd">View</a> </li>';
                $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_DELETE . '" data-id="' . $row->entry_id . '">' . 'STOP TASK' . '</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->entry_code,
                nl2br($row->entry_remark),
                ymd_to_dmy($row->entry_startdate),
                ymd_to_dmy($row->entry_enddate),
                $row->entry_interval,
                $row->entry_runday,
                ymd_to_dmy($row->entry_nextrundate),
                format_num($row->entry_amount,0),
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

        echo json_encode($records);
    }

    #endregion

}