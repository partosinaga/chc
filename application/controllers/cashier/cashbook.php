<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cashbook extends CI_Controller {

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
        $this->cashbook_manage();
    }

    #region Form
    public function cashbook_form($type = 1, $id = 0){
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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

        $data['entryheader_id'] = $id;

        $dept = $this->mdl_general->get('ms_department', array('status ' => STATUS_NEW), array());
        $data['dept_list'] = $dept->result_array();

        $general_dept_id = 0;
        foreach($data['dept_list'] as $dept){
            if(strtolower($dept['department_name']) == 'gen'){
                $general_dept_id = $dept['department_id'];
            }
        }

        $data['general_dept_id'] = $general_dept_id;

        if($id > 0){
            //$qry = $this->db->get_where('gl_cashentry_header', array('cashentry_id' => $id));
            $qry = $this->db->query('SELECT gl_cashentry_header.*, ISNULL(document.feature_id,0) as feature_id, ms_transtype.doc_type FROM gl_cashentry_header
                  JOIN ms_transtype ON ms_transtype.transtype_id = gl_cashentry_header.transtype_id
                  LEFT JOIN document ON document.doc_name = ms_transtype.doc_type
                  WHERE gl_cashentry_header.cashentry_id = ' . $id);

            $data['row'] = $qry->row();

            $qry2 = $this->mdl_finance->getJoin('gl_cashentry_detail.*, gl_coa.coa_code, gl_coa.coa_desc','gl_cashentry_detail', array('gl_coa' => 'gl_coa.coa_id = gl_cashentry_detail.coa_id'), array('cashentry_id' => $id), array());
            $data['qry_det'] = $qry2->result_array();
        }

        if($type == 2){
            //READ ONLY
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/cashbook/cashbook_form.php', $data);
        $this->load->view('layout/footer', $data_footer);
    }

    public function submit_entry(){
        $valid = true;

        if(isset($_POST)){
            $entryHeaderID = $_POST['cashentry_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $doc_type = $_POST['doc_type'] ;

            //echo 'doc-type ... ' . $doc_type;

            unset($_POST['doc_type']);

            $data['journal_date'] = dmy_to_ymd(trim($_POST['journal_date']));
            $data['transtype_id'] = $_POST['transtype_id'];
            $data['subject'] = $_POST['journal_subject'];
            $data['reference'] = $_POST['journal_reff'];
            $data['journal_remarks'] = $_POST['journal_remarks'];
            //echo 'Amount ' . $_POST['total_debit'];
            $data['journal_amount'] = str_replace(",", "", $_POST['total_debit']);
            $data['currency_rate'] = 1;

            unset($_POST['entryheader_id']);

            $server_date = date('Y-m-d H:i:s');

            if($entryHeaderID > 0){
                $qry = $this->db->get_where('gl_cashentry_header', array('cashentry_id' => $entryHeaderID));
                $row = $qry->row();

                $qry_trx = $this->db->get_where('ms_transtype', array('transtype_id' => $row->transtype_id));
                $doc_type = $qry_trx->row()->doc_type;

                $arr_date = explode('-', $data['journal_date']);
                $arr_date_old = explode('-', ymd_from_db($row->journal_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    //DELETE OLD CASHBOOK NUMBER

                    $data['journal_no'] = $this->generate_journalno($doc_type,$data,$row->journal_no);
                    if($data['journal_no'] == ''){
                        //echo '<br>step 2 update EMPTY ';
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Failed generating code.');
                    }
                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('gl_cashentry_header', array('cashentry_id' => $entryHeaderID), $data);

                    //echo '<br>step 3 update';

                    //update details
                    if($valid){
                        //echo '<br>step 4 update';

                        $valid = $this->insertDetailEntries($entryHeaderID, $data);

                        //echo '<br>step 5 update';

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                    }
                }
            }
            else {
                $data['journal_no'] = $this->generate_journalno($doc_type,$data);
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = $server_date;

                //echo 'no id : ' . $data['journal_no'];
                if($data['journal_no'] != ''){
                    $this->db->insert('gl_cashentry_header', $data);
                    $entryHeaderID = $this->db->insert_id();

                    if($entryHeaderID > 0){
                        $valid = $this->insertDetailEntries($entryHeaderID, $data);

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
                redirect(base_url('cashier/cashbook/cashbook_form/1/' . $entryHeaderID . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('cashier/cashbook/cashbook_manage/1.tpd'),true);
                }
                else {
                    redirect(base_url('cashier/cashbook/cashbook_form/1/' . $entryHeaderID . '.tpd'));
                }
            }
        }
    }

    private function generate_journalno($doctype, $header = array(), $old_doc_no = ''){
        $result = '';

        //echo 'generating code ... ' . $doctype;
        if($doctype != '' && count($header) > 0 ){
            $qry_feature = $this->db->get_where('document', array('doc_name' => $doctype));
            if($qry_feature->num_rows() > 0){
                $feature = $qry_feature->row();
                if($feature->feature_id == Feature::FEATURE_AR_RECEIPT){
                    //Remove old doc no
                    if($old_doc_no != ''){
                        $this->db->delete('ar_receipt', array('receipt_no' => $old_doc_no, 'status' => FLAG_CASHBOOK));
                    }

                    //echo '<br/>generate_journalno RV';
                    $rv['receipt_date'] = $header['journal_date'];
                    $rv['created_by'] = my_sess('user_id');
                    $rv['created_date']  = date('Y-m-d H:i:s');
                    $rv['receipt_paymentamount'] = $header['journal_amount'];
                    $rv['receipt_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_RECEIPT, $header['journal_date']);
                    $rv['status'] = FLAG_CASHBOOK;

                    $this->db->insert('ar_receipt', $rv);
                    $newID = $this->db->insert_id();

                    if($newID > 0 && trim($rv['receipt_no']) != ''){
                        $result = $rv['receipt_no'];
                    }
                }else if($feature->feature_id == Feature::FEATURE_AP_PAYMENT){
                    //Remove old doc no
                    if($old_doc_no != ''){
                        $this->db->delete('ap_payment', array('payment_code' => $old_doc_no, 'status' => FLAG_CASHBOOK));
                    }

                    $pv['payment_date'] = $header['journal_date'];
                    $pv['user_created'] = my_sess('user_id');
                    $pv['date_created']  = date('Y-m-d H:i:s');
                    $pv['total_amount'] = $header['journal_amount'];
                    $pv['payment_code'] = $this->mdl_general->generate_code(Feature::FEATURE_AP_PAYMENT, $header['journal_date']);
                    $pv['status'] = FLAG_CASHBOOK;

                    $this->db->insert('ap_payment', $pv);
                    $newID = $this->db->insert_id();

                    if($newID > 0 && trim($pv['payment_code']) != ''){
                        $result = $pv['payment_code'];
                    }
                }else {
                    //echo '<br/>generate_journalno A';
                    $result = $this->mdl_general->generate_code(Feature::FEATURE_GL_ADJUSTMENT, $header['journal_date']);
                }
            }else{
                //echo '<br/>not valid';
            }
        }

        return $result;
    }

    private function insertDetailEntries($entryHeaderID = 0, $data = array()){
        $valid = true;

        if($entryHeaderID > 0 && isset($_POST) && isset($data)){
            $detail_ids = isset($_POST['detail_id']) ? $_POST['detail_id'] : array();
            $coa_ids = isset($_POST['coa_id']) ? $_POST['coa_id'] : array();
            $debits = isset($_POST['journal_debit']) ? $_POST['journal_debit'] : array();
            $credits = isset($_POST['journal_credit']) ? $_POST['journal_credit'] : array();
            $dept = isset($_POST['dept_id']) ? $_POST['dept_id'] : array();
            $notes = isset($_POST['journal_note']) ? $_POST['journal_note'] : array();

            if(count($detail_ids) > 0){
                //echo '<br>Count detail ' . count($dept);

                for ($i = 0; $i <= max(array_keys($detail_ids)); $i++) {
                    if($valid){
                        if(isset($detail_ids[$i])){
                            if($detail_ids[$i] <= 0){
                                $detail['cashentry_id'] = $entryHeaderID;
                                $detail['coa_id'] = $coa_ids[$i];
                                //$detail['journal_note'] = $data['journal_remarks'];
                                $detail['journal_note'] = (trim($notes[$i]) != '' ? $notes[$i] : $data['journal_remarks']);
                                $detail['journal_debit'] = ($debits[$i]);
                                $detail['journal_credit'] = ($credits[$i]);
                                $detail['dept_id'] = $dept[$i];
                                $detail['status'] = STATUS_NEW;

                                $this->db->insert('gl_cashentry_detail', $detail);
                                $insertID = $this->db->insert_id();

                                if($insertID <= 0){
                                    $valid = false;
                                }
                            }else{
                                //$detail['journal_note'] = $data['journal_remarks'];
                                $detail['journal_note'] = (trim($notes[$i]) != '' ? $notes[$i] : $data['journal_remarks']);
                                $detail['journal_debit'] = $debits[$i];
                                $detail['journal_credit'] = $credits[$i];
                                $detail['dept_id'] = $dept[$i];

                                $this->mdl_general->update('gl_cashentry_detail', array('entrydetail_id' => $detail_ids[$i]), $detail);
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
            $this->db->delete('gl_cashentry_detail', array('entrydetail_id' => $detailId));

            $result['type'] = '1';
            $result['message'] = 'Successfully delete record.';
        }

        echo json_encode($result);
    }

    public function action_request(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $entryheader_id = $_POST['cashentry_id'];
        $data['status'] = $_POST['action'];
        $data['cancel_note'] = $_POST['reason'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Cash Book';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $entryheader_id;
        $data_log['feature_id'] = Feature::FEATURE_CASHBOOK;

        if($entryheader_id > 0 && $data['status'] > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $qry = $this->db->get_where('gl_cashentry_header', array('cashentry_id' => $entryheader_id));
            if($qry->num_rows() > 0){
                $row = $qry->row();

                if($data['status'] == STATUS_APPROVE){
                    $data['approved_by'] = my_sess('user_id');
                    $data['approved_date'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('gl_cashentry_header', array('cashentry_id' => $entryheader_id), $data);

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
                        $data['modified_by'] = my_sess('user_id');
                        $data['modified_date'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('gl_cashentry_header', array('cashentry_id' => $entryheader_id), $data);

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

    #endregion

    #region Manage

    public function cashbook_manage($type = 1, $id = 0){
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
        $this->load->view('finance/cashbook/cashbook_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_cashbook_list($view_status = STATUS_NEW, $menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['gl_cashentry_header.status'] = $view_status;

        //$where['gl_cashentry_header.created_by'] = my_sess('user_id');

        $like = array();
        if(isset($_REQUEST['filter_journal_no'])){
            if($_REQUEST['filter_journal_no'] != ''){
                $like['gl_cashentry_header.journal_no'] = $_REQUEST['filter_journal_no'];
            }
        }
        if(isset($_REQUEST['filter_journal_date_from'])){
            if($_REQUEST['filter_journal_date_from'] != ''){
                $where['gl_cashentry_header.journal_date >='] = dmy_to_ymd($_REQUEST['filter_journal_date_from']);
            }
        }
        if(isset($_REQUEST['filter_journal_date_to'])){
            if($_REQUEST['filter_journal_date_to'] != ''){
                $where['gl_cashentry_header.journal_date <='] = dmy_to_ymd($_REQUEST['filter_journal_date_to']);
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['gl_cashentry_header.journal_remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_reff_no'])){
            if($_REQUEST['filter_reff_no'] != ''){
                $like['gl_cashentry_header.reference'] = $_REQUEST['filter_reff_no'];
            }
        }
        if(isset($_REQUEST['filter_trx_type'])){
            if($_REQUEST['filter_trx_type'] != ''){
                $where['gl_cashentry_header.transtype_id'] = $_REQUEST['filter_trx_type'];
            }
        }

        $jointable = array('ms_transtype' => 'gl_cashentry_header.transtype_id = ms_transtype.transtype_id');

        $iTotalRecords = $this->mdl_finance->countJoin('gl_cashentry_header', $jointable, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'gl_cashentry_header.journal_no DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'gl_cashentry_header.journal_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'gl_cashentry_header.journal_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'gl_cashentry_header.transtype_id ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'gl_cashentry_header.journal_remarks ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'gl_cashentry_header.journal_amount ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'gl_cashentry_header.reference ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('gl_cashentry_header.*, ms_transtype.transtype_name, ms_transtype.transtype_desc','gl_cashentry_header', $jointable, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $rv_doctype = '';
        $qry_feature = $this->db->get_where('document', array('feature_id' => Feature::FEATURE_AR_RECEIPT));
        if($qry_feature->num_rows() > 0){
            $rv_doctype = $qry_feature->row()->doc_name;
        }

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_EDIT)){
                    $btn_action .= '<li> <a href="' . base_url('cashier/cashbook/cashbook_form/0/' . $row->cashentry_id) . '.tpd">Edit</a> </li>';
                    $btn_action .= '<li> <a href="' . base_url('cashier/cashbook/pdf_cashbookvoucher/'. $row->cashentry_id .'.tpd') . '" target="_blank">Voucher</a> </li>';
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_CANCEL . '" data-id="' . $row->cashentry_id . '">' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
                else {
                    $btn_action .= '<li> <a href="' . base_url('cashier/cashbook/cashbook_form/2/' . $row->cashentry_id) . '.tpd">View</a> </li>';
                }
            }else if($row->status == STATUS_CANCEL){
                $btn_action .= '<li> <a href="' . base_url('cashier/cashbook/cashbook_form/2/' . $row->cashentry_id) . '.tpd">View</a> </li>';
            }else if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                $btn_action .= '<li> <a href="' . base_url('cashier/cashbook/cashbook_form/2/' . $row->cashentry_id) . '.tpd">View</a> </li>';
                $btn_action .= '<li> <a href="' . base_url('cashier/cashbook/pdf_cashbookvoucher/'. $row->cashentry_id .'.tpd') . '">Voucher</a> </li>';
                if(strpos($row->journal_no,$rv_doctype)){
                    $btn_action .= '<li> <a href="' . base_url('cashier/cashbook/pdf_cashbookreceipt/'. $row->cashentry_id .'.tpd') . '">Receipt</a> </li>';
                }
            }
            else{
                $btn_action .= '<li> <a href="' . base_url('cashier/cashbook/cashbook_form/2/' . $row->cashentry_id) . '.tpd">View</a> </li>';
            }

            if($row->journal_amount > 0){
                $records["data"][] = array(
                    '<input type="checkbox" value="' . $row->cashentry_id . '" name="ischecked[]"/>',
                    $row->journal_no,
                    dmy_from_db($row->journal_date),
                    $row->transtype_name,
                    $row->journal_remarks,
                    format_num($row->journal_amount,0),
                    $row->reference,
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
                    $row->journal_no,
                    ymd_to_dmy($row->journal_date),
                    $row->transtype_name,
                    $row->journal_remarks,
                    format_num($row->journal_amount,0),
                    $row->reference,
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

    public function posting_journals(){
        $valid = true;

        if(isset($_POST)){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : '';

            $data['status']= STATUS_CLOSED;

            if(isset($_POST['ischecked'])){
                $pv_doctype = '';
                $qry_feature = $this->db->get_where('document', array('feature_id' => Feature::FEATURE_AP_PAYMENT));
                if($qry_feature->num_rows() > 0){
                    $pv_doctype = $qry_feature->row()->doc_name;
                }

                $rowcount = count($_POST['ischecked']);

                foreach( $_POST['ischecked'] as $val){
                    //echo '[posting_journal] ... ' . $val;
                    $qryHeader = $this->db->get_where('gl_cashentry_header', array('cashentry_id' => $val));

                    if($qryHeader->num_rows() > 0){
                        $head = $qryHeader->row();

                        //echo '<br>[posting_journal]  1 ';

                        //insert filing no for PV
                        if(strpos($head->journal_no, $pv_doctype)){
                            echo 'create filing doc ' . $pv_doctype;

                        }

                        //echo '<br>[posting_journal]  2 ';

                        //insert post journal
                        $detail = array();

                        $totalDebit = 0;
                        $totalCredit = 0;
                        $qryDetails = $this->mdl_general->get('gl_cashentry_detail', array('cashentry_id' => $val));
                        if($qryDetails->num_rows() > 0){
                            foreach($qryDetails->result() as $det){
                                $rowdet = array();
                                $rowdet['coa_id'] = $det->coa_id;
                                $rowdet['dept_id'] = $det->dept_id;
                                $rowdet['journal_note'] = $det->journal_note;
                                $rowdet['journal_debit'] = $det->journal_debit;
                                $rowdet['journal_credit'] = $det->journal_credit;
                                $rowdet['reference_id'] = $head->cashentry_id;
                                $rowdet['transtype_id'] = $head->transtype_id;

                                array_push($detail, $rowdet);

                                $totalDebit += $det->journal_debit;
                                $totalCredit += $det->journal_credit;
                            }
                        }

                        if($totalDebit == $totalCredit){
                            //echo '<br>[posting_journal] B ... ' . $totalDebit;

                            $header = array();
                            $header['journal_no'] = $head->journal_no;
                            $header['journal_date'] = ($posting_date != '' ? $posting_date : $head->journal_date); //$head->journal_date;
                            $header['journal_remarks'] = $head->journal_remarks;
                            $header['modul'] = GLMOD::GL_MOD_CASH;
                            $header['journal_amount'] = $totalDebit;
                            $header['reference'] = $head->reference;
                            $header['reference_date'] = $head->reference_date;

                            $valid = $this->mdl_finance->postJournal($header,$detail);

                            if($valid){
                                $this->mdl_general->update('gl_cashentry_header', array('cashentry_id' => $val), $data);
                            }
                        }
                    }
                }

                $this->session->set_flashdata('flash_message', $rowcount . ' transaction(s) successfully posted.');
                $this->session->set_flashdata('flash_message_class', 'success');
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
                    $this->session->set_flashdata('flash_message', 'Transaction can not be posted. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be posted. Please try again later.');
            }

            //FINALIZE
            if(!$valid){
                redirect(base_url('cashier/cashbook/cashbook_manage/1.tpd'));
            }
            else {
                redirect(base_url('cashier/cashbook/cashbook_manage/1.tpd'));
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
        $val = 0;

        if(isset($_POST['entryheader_id'])){
            $val = $_POST['entryheader_id'];
        }

        if($val > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $pv_doctype = '';
            $qry_feature = $this->db->get_where('document', array('feature_id' => Feature::FEATURE_AP_PAYMENT));
            if($qry_feature->num_rows() > 0){
                $pv_doctype = $qry_feature->row()->doc_name;
            }

            $data['status']= STATUS_CLOSED;

            $qryHeader = $this->db->get_where('gl_cashentry_header', array('cashentry_id' => $val));

            if($qryHeader->num_rows() > 0){
                $head = $qryHeader->row();

                $posting_date = isset($_POST['posting_date']) ? dmy_to_ymd($_POST['posting_date']) : $head->journal_date;

                //insert filing no for PV
                if(strpos($head->journal_no, $pv_doctype)){

                }

                //insert post journal
                $detail = array();

                $totalDebit = 0;
                $totalCredit = 0;
                $qryDetails = $this->mdl_general->get('gl_cashentry_detail', array('cashentry_id' => $val));
                if($qryDetails->num_rows() > 0){
                    foreach($qryDetails->result() as $det){
                        $rowdet = array();
                        $rowdet['coa_id'] = $det->coa_id;
                        $rowdet['dept_id'] = $det->dept_id;
                        $rowdet['journal_note'] = $det->journal_note;
                        $rowdet['journal_debit'] = $det->journal_debit;
                        $rowdet['journal_credit'] = $det->journal_credit;
                        $rowdet['reference_id'] = $head->cashentry_id;
                        $rowdet['transtype_id'] = $head->transtype_id;

                        array_push($detail, $rowdet);

                        $totalDebit += $det->journal_debit;
                        $totalCredit += $det->journal_credit;
                    }
                }

                if($totalDebit == $totalCredit){
                    $header = array();
                    $header['journal_no'] = $head->journal_no;
                    $header['journal_date'] = $posting_date;
                    $header['journal_remarks'] = $head->journal_remarks;
                    $header['modul'] = GLMOD::GL_MOD_CASH;
                    $header['journal_amount'] = $totalDebit;
                    $header['reference'] = $head->reference;
                    $header['reference_date'] = $head->reference_date;

                    $valid = $this->mdl_finance->postJournal($header,$detail);

                    if($valid){
                        $this->mdl_general->update('gl_cashentry_header', array('cashentry_id' => $val), $data);
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
                    $result['redirect_link'] = base_url('cashier/cashbook/cashbook_form/0/'. $val .'.tpd');
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

    #region History

    public function cashbook_history($type = 1, $id = 0){
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
        $this->load->view('finance/cashbook/cashbook_list.php', $data);
        $this->load->view('layout/footer');
    }

    public function pdf_cashbookvoucher($doc_id = 0) {
        if($doc_id > 0){
            $this->load->model('finance/mdl_finance');

            $qry = $this->db->get_where('gl_cashentry_header', array('cashentry_id' => $doc_id));

            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                $where['gl_cashentry_detail.cashentry_id'] = $doc_id;
                $data['qry_det'] = $this->mdl_finance->getJoin('gl_cashentry_detail.*, gl_coa.coa_code, gl_coa.coa_desc , ms_department.department_name as dept_code' ,
                    'gl_cashentry_detail',array('gl_coa' => 'gl_coa.coa_id = gl_cashentry_detail.coa_id',
                        'ms_department' => 'ms_department.department_id = gl_cashentry_detail.dept_id'),
                    $where);

                $pv_doctype = '_';
                $qry_feature = $this->db->get_where('document', array('feature_id' => Feature::FEATURE_AP_PAYMENT));
                if($qry_feature->num_rows() > 0){
                    $pv_doctype = $qry_feature->row()->doc_name;
                }

                $rv_doctype = '_';
                $qry_feature = $this->db->get_where('document', array('feature_id' => Feature::FEATURE_AR_RECEIPT));
                if($qry_feature->num_rows() > 0){
                    $rv_doctype = $qry_feature->row()->doc_name;
                }

                $bank_account = array();
                $qry_bank = $this->db->get_where('fn_bank_account', array('status' => STATUS_NEW));
                foreach ($qry_bank->result() as $row_bank) {
                    array_push($bank_account, $row_bank->coa_id);
                }
                $data['bank_account'] = $bank_account;

                $data['doc_type_title'] = 'ADJUSTMENT VOUCHER';
                $data['subject_title'] = 'Subject';
                $data['cashbook_type'] = 1;
                $journalno = $data['row']->journal_no;
                if(strpos($journalno, $pv_doctype) > -1){
                    $data['doc_type_title'] = 'PAYMENT VOUCHER';
                    $data['subject_title'] = 'Pay To';
                    $data['cashbook_type'] = 2;
                }else if(strpos($journalno, $rv_doctype) > -1){
                    $data['doc_type_title'] = 'RECEIVE VOUCHER';
                    $data['subject_title'] = 'Receive From';
                    $data['cashbook_type'] = 3;
                }

                $this->load->view('finance/cashbook/pdf_voucher.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->journal_no . ".pdf", array('Attachment'=>0));
            }else{
                tpd_404();
            }

        }else{
            tpd_404();
        }
    }

    #endregion

}