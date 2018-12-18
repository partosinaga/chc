<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller {

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
        $this->class_manage();
    }

    #region CLASS

    public function class_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if($type == 1){ //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/class_list', $data);
            $this->load->view('layout/footer');
        }
        else{ // Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');


            if($id > 0){
                $qry = $this->mdl_general->get('gl_class', array('class_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['class_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/class_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function submit_class(){
        if(isset($_POST)){
            $class_id = $_POST['class_id'];
            $has_error = false;

            $data['class_code'] = $_POST['class_code'];
            $data['class_desc'] = $_POST['class_desc'];
            $data['class_type'] = $_POST['class_type'];
            $data['is_debit'] = $_POST['is_debit'];

            if($class_id > 0)
            {
                if(isset($_POST['status'])){
                    $data['status'] =  $_POST['status'];
                }

                $exist = $this->mdl_general->count('gl_class', array('class_code' => $data['class_code'], 'class_id <>' => $class_id));

                if($exist <= 0){
                    $this->mdl_general->update('gl_class', array('class_id' => $class_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'COA Class successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'COA Class already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('gl_class', array('class_code' => $data['class_code']));

                if($exist == 0){
                    $this->db->insert('gl_class', $data);
                    $dept_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'COA Class successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'COA Class already exist.');
                }
            }

            if($has_error){
                redirect(base_url('finance/setup/class_manage/' . $class_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/setup/class_manage/1.tpd'));
                }
                else {
                    redirect(base_url('finance/setup/class_manage/0/' . $class_id . '.tpd'));
                }
            }
        }
    }

    public function class_list(){
        $where['status <>'] = STATUS_DELETE;
        $like = array();

        if(isset($_REQUEST['filter_classcode'])){
            if($_REQUEST['filter_classcode'] != ''){
                $like['class_code'] = $_REQUEST['filter_classcode'];
            }
        }
        if(isset($_REQUEST['filter_classdesc'])){
            if($_REQUEST['filter_classdesc'] != ''){
                $like['class_desc'] = $_REQUEST['filter_classdesc'];
            }
        }
        if(isset($_REQUEST['filter_classtype'])){
            if($_REQUEST['filter_classtype'] != ''){
                $like['class_type'] = $_REQUEST['filter_classtype'];
            }
        }
        if(isset($_REQUEST['filter_isdebit'])){
            if($_REQUEST['filter_isdebit'] != ''){
                $like['is_debit'] = $_REQUEST['filter_isdebit'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $where['status'] = $_REQUEST['filter_status'];
            }
        }

        $qry_tot = $this->mdl_general->count('gl_class', $where, $like);

        $iTotalRecords = $qry_tot;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'gl_class.class_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'gl_class.class_code ' . $_REQUEST['order'][0]['dir'];
            }
            /*
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'gl_class.class_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ms_department.status ' . $_REQUEST['order'][0]['dir'];
            }*/
        }

        $qry = $this->mdl_general->get('gl_class', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->class_code,
                $row->class_desc,
                GLClassType::class_type_name($row->class_type),
                ($row->is_debit > 0) ? 'D' : 'C',
                get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('finance/setup/class_manage/0/' . $row->class_id) . '.tpd"><i class="fa fa-search"></i></a>'
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    #endregion

    #region COA

    public function coa_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if($type == 1){ //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/coa_list', $data);
            $this->load->view('layout/footer');
        }
        else{ // Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('gl_coa', array('coa_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['coa_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/coa_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function submit_coa(){
        if(isset($_POST)){
            $coa_id = $_POST['coa_id'];
            $has_error = false;

            $data['class_id'] = $_POST['class_id'];
            $data['coa_code'] = $_POST['coa_code'];
            $data['coa_desc'] = $_POST['coa_desc'];
            $data['is_debit'] = $_POST['is_debit'];
            $data['is_display'] = $_POST['is_display'];

            if($coa_id > 0)
            {
                if(isset($_POST['status'])){
                    $data['status'] =  $_POST['status'];
                }

                $exist = $this->mdl_general->count('gl_coa', array('coa_code' => $data['coa_code'], 'coa_id <>' => $coa_id));

                if($exist <= 0){
                    $this->mdl_general->update('gl_coa', array('coa_id' => $coa_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'COA ' . $data['coa_code'] . ' successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'COA ' . $data['coa_code'] .' already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('gl_coa', array('coa_code' => $data['coa_code']));

                if($exist == 0){
                    $this->db->insert('gl_coa', $data);
                    $dept_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'COA ' . $data['coa_code'] .' successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'COA ' . $data['coa_code'] . ' already exist.');
                }
            }

            if($has_error){
                redirect(base_url('finance/setup/coa_manage/' . $coa_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/setup/coa_manage/1.tpd'));
                }
                else {
                    redirect(base_url('finance/setup/coa_manage/0/' . $coa_id . '.tpd'));
                }
            }
        }
    }

    public function coa_list(){
        $this->load->model('finance/mdl_finance');

        $where['gl_coa.status <>'] = STATUS_DELETE;
        $like = array();

        if(isset($_REQUEST['filter_coacode'])){
            if($_REQUEST['filter_coacode'] != ''){
                $like['gl_coa.coa_code'] = $_REQUEST['filter_coacode'];
            }
        }
        if(isset($_REQUEST['filter_coadesc'])){
            if($_REQUEST['filter_coadesc'] != ''){
                $like['gl_coa.coa_desc'] = $_REQUEST['filter_coadesc'];
            }
        }
        if(isset($_REQUEST['filter_classid'])){
            if($_REQUEST['filter_classid'] != ''){
                $like['gl_class.class_id'] = $_REQUEST['filter_classid'];
            }
        }
        if(isset($_REQUEST['filter_classtype'])){
            if($_REQUEST['filter_classtype'] != ''){
                $like['gl_class.class_type'] = $_REQUEST['filter_classtype'];
            }
        }
        if(isset($_REQUEST['filter_isdebit'])){
            if($_REQUEST['filter_isdebit'] != ''){
                $like['gl_coa.is_debit'] = $_REQUEST['filter_isdebit'];
            }
        }
        if(isset($_REQUEST['filter_isdisplay'])){
            if($_REQUEST['filter_isdisplay'] != ''){
                $like['gl_coa.is_display'] = $_REQUEST['filter_isdisplay'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $where['gl_coa.status'] = $_REQUEST['filter_status'];
            }
        }

        $qry_tot = $this->mdl_finance->countCOA($where, $like);

        $iTotalRecords = $qry_tot;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'gl_coa.coa_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'gl_coa.coa_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'gl_coa.coa_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'gl_class.class_id ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'gl_class.class_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'gl_coa.is_display ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'gl_coa.is_debit ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'gl_coa.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getCOA($where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->coa_code,
                $row->coa_desc,
                $row->class_code,
                GLClassType::class_type_name($row->class_type),
                ($row->is_debit > 0) ? 'D' : 'C',
                ($row->is_display > 0) ? 'Yes' : '',
                get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('finance/setup/coa_manage/0/' . $row->coa_id) . '.tpd"><i class="fa fa-search"></i></a>'
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    #endregion

    #region BANK

    public function bank_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if($type == 1){ //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/bank_list', $data);
            $this->load->view('layout/footer');
        }
        else if($type == 2){
            //Bank Account Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('fn_bank_account', array('bankaccount_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['bankaccount_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/bankaccount_form', $data);
            $this->load->view('layout/footer');
        }
        else{
            //Bank Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('fn_bank', array('bank_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['bank_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/bank_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function submit_bank(){
        if(isset($_POST)){
            $bank_id = $_POST['bank_id'];
            $has_error = false;

            $data['bank_code'] = $_POST['bank_code'];
            $data['bank_name'] = $_POST['bank_name'];

            if($bank_id > 0)
            {
                if(isset($_POST['status'])){
                    $data['status'] =  $_POST['status'];
                }

                $exist = $this->mdl_general->count('fn_bank', array('bank_code' => $data['bank_code'], 'bank_id <>' => $bank_id));

                if($exist <= 0){
                    $this->mdl_general->update('fn_bank', array('bank_id' => $bank_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Bank ' . $data['bank_code'] . ' successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Bank ' . $data['bank_code'] .' already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('fn_bank', array('bank_code' => $data['bank_code']));

                if($exist == 0){
                    $this->db->insert('fn_bank', $data);
                    $bank_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Bank ' . $data['bank_code'] .' successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Bank ' . $data['bank_code'] . ' already exist.');
                }
            }

            if($has_error){
                redirect(base_url('finance/setup/bank_manage/' . $bank_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/setup/bank_manage/1.tpd'));
                }
            }
        }
    }

    public function bank_list(){
        $where['fn_bank.status <>'] = STATUS_DELETE;

        $records["data"] = array();

        $qry = $this->mdl_general->get('fn_bank',$where, array());

        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->bank_code,
                $row->bank_name,
                get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('finance/setup/bank_manage/0/' . $row->bank_id) . '.tpd"><i class="fa fa-search"></i></a>'
            );
        }

        $records["recordsTotal"] = 0;

        echo json_encode($records);
    }

    public function submit_bankaccount(){
        if(isset($_POST)){
            $bankaccount_id = $_POST['bankaccount_id'];
            $has_error = false;

            $data['bank_id'] = $_POST['bank_id'];
            $data['bankaccount_code'] = $_POST['bankaccount_code'];
            $data['bankaccount_desc'] = $_POST['bankaccount_desc'];
            $data['coa_id'] = $_POST['coa_id'];
            $data['iscash'] = $_POST['is_cash'];
            $data['currencytype_id'] = $_POST['currencytype_id'];
            $data['is_veritrans_account'] = isset($_POST['is_veritrans_account']) ? $_POST['is_veritrans_account'] : 0;

            if($bankaccount_id > 0)
            {
                if(isset($_POST['status'])){
                    $data['status'] =  $_POST['status'];
                }

                $exist = $this->mdl_general->count('fn_bank_account', array('bankaccount_code' => $data['bankaccount_code'], 'bankaccount_id <>' => $bankaccount_id));

                if($exist <= 0){
                    $this->mdl_general->update('fn_bank_account', array('bankaccount_id' => $bankaccount_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'A/C ' . $data['bankaccount_code'] . ' successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'A/C ' . $data['bankaccount_code'] .' already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('fn_bank_account', array('bankaccount_code' => $data['bankaccount_code']));

                if($exist == 0){
                    $this->db->insert('fn_bank_account', $data);
                    $bankaccount_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'A/C ' . $data['bankaccount_code'] .' successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'A/C ' . $data['bankaccount_code'] . ' already exist.');
                }
            }

            if($has_error){
                redirect(base_url('finance/setup/bank_manage/2/' . $bankaccount_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/setup/bank_manage/1.tpd'));
                }
            }
        }
    }

    public function bankaccount_list(){
        $records["data"] = array();

        $qry = $this->db->query('SELECT ac.*, bn.bank_code, bn.bank_name, cur.currencytype_code, coa.coa_code
                                 FROM fn_bank_account ac
                                 JOIN fn_bank bn ON ac.bank_id = bn.bank_id
                                 LEFT JOIN currencytype cur ON ac.currencytype_id = cur.currencytype_id
                                 LEFT JOIN gl_coa coa ON coa.coa_id = ac.coa_id
                                 WHERE bn.status NOT IN (' . STATUS_DELETE . ') AND ac.status <> ' . STATUS_DELETE .
                                ' ORDER BY bn.bank_code, ac.bankaccount_code');

        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->bank_code,
                $row->bankaccount_code,
                $row->currencytype_code,
                $row->coa_code,
                ($row->iscash > 0 ? '<span style="color:red;">CASH</span>' : 'BANK'),
                $row->bankaccount_desc,
                //($row->is_veritrans_account > 0 ? 'YES' : ''),
                get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('finance/setup/bank_manage/2/' . $row->bankaccount_id) . '.tpd"><i class="fa fa-search"></i></a>'
            );
        }

        $records["recordsTotal"] = 0;

        echo json_encode($records);
    }

    #endregion

    #region TAX , CURRENCY

    public function other_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if($type == 1){ //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/other_list', $data);
            $this->load->view('layout/footer');
        }
        else if($type == 2){
            //Tax Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('tax_type', array('taxtype_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['taxtype_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/taxtype_form', $data);
            $this->load->view('layout/footer');
        }
        else{
            //Currency Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('currencytype', array('currencytype_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['currencytype_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/currency_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function submit_currency(){
        if(isset($_POST)){
            $currencytype_id = $_POST['currencytype_id'];
            $has_error = false;

            $data['currencytype_code'] = $_POST['currencytype_code'];
            $data['currencytype_desc'] = $_POST['currencytype_desc'];

            if($currencytype_id > 0)
            {
                $exist = $this->mdl_general->count('currencytype', array('currencytype_code' => $data['currencytype_code'], 'currencytype_id <>' => $currencytype_id));

                if($exist <= 0){
                    $this->mdl_general->update('currencytype', array('currencytype_id' => $currencytype_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Currency ' . $data['currencytype_code'] . ' successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Currency ' . $data['currencytype_code'] .' already exist.');
                }
            }else {
                $exist = $this->mdl_general->count('currencytype', array('currencytype_code' => $data['currencytype_code']));

                if($exist == 0){
                    $this->db->insert('currencytype', $data);
                    $bank_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Currency ' . $data['currencytype_code'] .' successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Currency ' . $data['currencytype_code'] . ' already exist.');
                }
            }

            if($has_error){
                redirect(base_url('finance/setup/other_manage/0/' . $currencytype_id. '.tpd' ));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/setup/other_manage/1.tpd'. '#portlet_currency'));
                }
            }
        }
    }

    public function currency_list(){
        $records["data"] = array();

        $qry = $this->mdl_general->get('currencytype',array(), array());

        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->currencytype_code,
                $row->currencytype_desc,
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('finance/setup/other_manage/0/' . $row->currencytype_id) . '.tpd"><i class="fa fa-search"></i></a>'
            );
        }

        $records["recordsTotal"] = 0;

        echo json_encode($records);
    }

    public function submit_taxtype(){
        if(isset($_POST)){
            $taxtype_id = $_POST['taxtype_id'];
            $has_error = false;

            $data['taxtype_code'] = $_POST['taxtype_code'];
            $data['taxtype_desc'] = $_POST['taxtype_desc'];
            $data['taxtype_category'] = $_POST['taxtype_category'];
            $data['taxtype_percent'] = $_POST['taxtype_percent'];
            $data['is_charge_default'] = isset($_POST['is_charge_default']) ? $_POST['is_charge_default'] : 0;
            $data['coa_id'] = $_POST['coa_id'];
            $data['taxtype_wht'] = $_POST['taxtype_wht'];
            $data['coa_id_wht'] = $_POST['coa_id_wht'];

            if($taxtype_id > 0)
            {
                if(isset($_POST['status'])){
                    $data['status'] =  $_POST['status'];
                }

                $exist = $this->mdl_general->count('tax_type', array('taxtype_code' => $data['taxtype_code'], 'taxtype_id <>' => $taxtype_id));

                if($exist <= 0){
                    $this->mdl_general->update('tax_type', array('taxtype_id' => $taxtype_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Tax Type ' . $data['taxtype_code'] . ' successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Tax Type ' . $data['taxtype_code'] .' already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('tax_type', array('taxtype_code' => $data['taxtype_code']));

                if($exist == 0){
                    $this->db->insert('tax_type', $data);
                    $taxtype_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Tax Type ' . $data['taxtype_code'] .' successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Tax Type ' . $data['taxtype_code'] . ' already exist.');
                }
            }

            if($has_error){
                redirect(base_url('finance/setup/other_manage/2/' . $taxtype_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/setup/other_manage/1.tpd'));
                }
            }
        }
    }

    public function taxtype_list(){
        $records["data"] = array();

        $qry = $this->db->query('SELECT tax.*, coa1.coa_code as coa_code_tax, coa2.coa_code as coa_code_wht
                                 FROM tax_type tax
                                 LEFT JOIN gl_coa coa1 ON tax.coa_id = coa1.coa_id
                                 LEFT JOIN gl_coa coa2 ON tax.coa_id_wht = coa2.coa_id
                                 WHERE tax.status NOT IN (' . STATUS_DELETE . ')
                                 ORDER BY tax.taxtype_code');

        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->taxtype_code,
                $row->taxtype_desc,
                ($row->is_charge_default > 0 ? '<label class="label bg-yellow-casablanca">ITEM VAT</label>' : ''),
                ($row->taxtype_category > 0 ? 'INCLUDED' : 'EXCLUDED'),
                $row->taxtype_percent,
                $row->coa_code_tax,
                $row->taxtype_wht,
                $row->coa_code_wht,
                get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('finance/setup/other_manage/2/' . $row->taxtype_id) . '.tpd"><i class="fa fa-search"></i></a>'
            );
        }

        $records["recordsTotal"] = 0;

        echo json_encode($records);
    }

    #endregion

    #region TRX TYPE

    public function trx_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if($type == 1){ //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/trx_list', $data);
            $this->load->view('layout/footer');
        }
        else{
            //Trx Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('ms_transtype', array('transtype_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['transtype_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/trx_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function submit_trx(){
        if(isset($_POST)){
            $transtype_id = $_POST['transtype_id'];
            $has_error = false;

            $data['transtype_name'] = $_POST['transtype_name'];
            $data['transtype_desc'] = $_POST['transtype_desc'];
            $data['feature_id'] = $_POST['feature_id'];
            $data['due_interestrate'] = $_POST['due_interestrate'];
            $data['has_stamp_duty'] = $_POST['has_stamp_duty'];
            $data['coa_id'] = $_POST['coa_id'];
            $data['doc_type'] = $_POST['doc_type'];

            if($transtype_id > 0)
            {
                if(isset($_POST['status'])){
                    $data['status'] =  $_POST['status'];
                }

                $exist = $this->mdl_general->count('ms_transtype', array('transtype_name' => $data['transtype_name'], 'transtype_id <>' => $transtype_id));

                if($exist <= 0){
                    $this->mdl_general->update('ms_transtype', array('transtype_id' => $transtype_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Trx Type ' . $data['transtype_name'] . ' successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Trx Type ' . $data['transtype_name'] .' already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('ms_transtype', array('transtype_name' => $data['transtype_name']));

                if($exist == 0){
                    $this->db->insert('ms_transtype', $data);
                    $transtype_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Trx Type ' . $data['transtype_name'] .' successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Trx Type ' . $data['transtype_name'] . ' already exist.');
                }
            }

            if($has_error){
                redirect(base_url('finance/setup/trx_manage/0/' . $transtype_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/setup/trx_manage/1.tpd'));
                }
            }
        }
    }

    public function trx_list(){
        $this->load->model('finance/mdl_finance');

        $where['ms_transtype.status <>'] = STATUS_DELETE;
        $like = array();

        if(isset($_REQUEST['filter_featureid'])){
            if($_REQUEST['filter_featureid'] != ''){
                $like['ms_transtype.feature_id'] = $_REQUEST['filter_featureid'];
            }
        }
        if(isset($_REQUEST['filter_transtypename'])){
            if($_REQUEST['filter_transtypename'] != ''){
                $like['ms_transtype.transtype_name'] = $_REQUEST['filter_transtypename'];
            }
        }
        if(isset($_REQUEST['filter_transtypedesc'])){
            if($_REQUEST['filter_transtypedesc'] != ''){
                $like['ms_transtype.transtype_desc'] = $_REQUEST['filter_transtypedesc'];
            }
        }
        if(isset($_REQUEST['filter_coacode'])){
            if($_REQUEST['filter_coacode'] != ''){
                $like['gl_coa.coa_code'] = $_REQUEST['filter_coacode'];
            }
        }
        if(isset($_REQUEST['filter_doctype'])){
            if($_REQUEST['filter_doctype'] != ''){
                $like['ms_transtype.doc_type'] = $_REQUEST['filter_doctype'];
            }
        }
        if(isset($_REQUEST['filter_interestrate'])){
            if($_REQUEST['filter_interestrate'] != ''){
                $like['ms_transtype.due_interestrate'] = $_REQUEST['filter_interestrate'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $like['ms_transtype.status'] = $_REQUEST['filter_status'];
            }
        }

        $jointable = array('gl_coa' => 'gl_coa.coa_id = ms_transtype.coa_id');

        $qry_tot = $this->mdl_finance->countJoin('ms_transtype', $jointable ,$where, $like);

        $iTotalRecords = $qry_tot;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ms_transtype.transtype_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ms_transtype.feature_id ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ms_transtype.transtype_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ms_transtype.transtype_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'gl_coa.coa_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'ms_transtype.doc_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'ms_transtype.due_interestrate ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'ms_transtype.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ms_transtype.*, gl_coa.coa_code','ms_transtype', $jointable, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        if($qry->num_rows() > 0){
            foreach($qry->result() as $row){
                $records["data"][] = array(
                    Feature::get_feature_name($row->feature_id),
                    $row->transtype_name,
                    $row->transtype_desc,
                    $row->coa_code,
                    $row->doc_type,
                    format_num($row->due_interestrate,2),
                    get_status_active($row->status),
                    '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('finance/setup/trx_manage/0/' . $row->transtype_id) . '.tpd"><i class="fa fa-search"></i></a>'
                );
                $i++;
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    #endregion

    #region POSTING

    public function posting_manage($type = 1, $id = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');

        $data = array();

        $jointable = array('ms_transtype' => 'ms_transtype.transtype_id = fn_feature_spec.transtype_id',
                           'gl_coa' => 'gl_coa.coa_id = fn_feature_spec.coa_id');
        $qry = $this->mdl_finance->getJoin('fn_feature_spec.*, ms_transtype.transtype_name, ms_transtype.transtype_desc, gl_coa.coa_code, gl_coa.coa_desc ','fn_feature_spec', $jointable);

        if($qry->num_rows() > 0){
            foreach($qry->result() as $rowresult){
                $data["row"][$rowresult->spec_key] = $rowresult;
            }
        }

        //$data['id'] = $id;
        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/setup/posting_form', $data);
        $this->load->view('layout/footer');
    }

    public function book_trxtype(){
        //$this->load->model('finance/mdl_finance');

        echo $this->load->view('general/book_trxtype');
    }

    public function json_trxtype($has_coa_only = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        if($has_coa_only > 0){
            $where['ms_transtype.coa_id > '] = 0;
        }

        if(isset($_REQUEST['filter_trx_code'])){
            if($_REQUEST['filter_trx_code'] != ''){
                $like['transtype_name'] = $_REQUEST['filter_trx_code'];
            }
        }
        if(isset($_REQUEST['filter_trx_desc'])){
            if($_REQUEST['filter_trx_desc'] != ''){
                $like['transtype_desc'] = $_REQUEST['filter_trx_desc'];
            }
        }
        if(isset($_REQUEST['filter_coa_code'])){
            if($_REQUEST['filter_coa_code'] != ''){
                $like['coa_code'] = $_REQUEST['filter_coa_code'];
            }
        }

        $jointable = array('gl_coa' => 'gl_coa.coa_id = ms_transtype.coa_id');

        $iTotalRecords = $this->mdl_finance->countJoin('ms_transtype', $jointable ,$where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'transtype_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'transtype_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'transtype_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'coa_code ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ms_transtype.*, gl_coa.coa_code','ms_transtype', $jointable, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                '<input type="radio" name="radio_item" value="' . $row->transtype_id . '" data-code="' . $row->transtype_name . '" data-desc="' . $row->transtype_desc . '" data-other-1="' . $row->transtype_name . '" />',
                $row->transtype_name,
                $row->transtype_desc,
                $row->coa_code,
                ($row->has_stamp_duty ? 'Yes' :''),
                ''
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function book_coa(){
        $this->load->view('general/book_coa');
    }

    public function json_coa(){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        $where['gl_coa.status <>'] = STATUS_DELETE;
        $where['gl_coa.is_display >'] = 0;

        if(isset($_REQUEST['filter_coacode'])){
            if($_REQUEST['filter_coacode'] != ''){
                $like['gl_coa.coa_code'] = $_REQUEST['filter_coacode'];
            }
        }
        if(isset($_REQUEST['filter_coadesc'])){
            if($_REQUEST['filter_coadesc'] != ''){
                $like['gl_coa.coa_desc'] = $_REQUEST['filter_coadesc'];
            }
        }
        if(isset($_REQUEST['filter_classid'])){
            if($_REQUEST['filter_classid'] != ''){
                $like['gl_class.class_id'] = $_REQUEST['filter_classid'];
            }
        }
        if(isset($_REQUEST['filter_classtype'])){
            if($_REQUEST['filter_classtype'] != ''){
                $like['gl_class.class_type'] = $_REQUEST['filter_classtype'];
            }
        }

        //$jointable = array('gl_coa' => 'gl_coa.coa_id = ms_transtype.coa_id');

        //$iTotalRecords = $this->mdl_finance->countJoin('ms_transtype', $jointable ,$where, $like);
        $iTotalRecords = $this->mdl_finance->countCOA($where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'gl_coa.coa_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'gl_coa.coa_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'gl_coa.coa_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'gl_coa.class_id ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'gl_class.class_type ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'gl_coa.is_debit ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                //$order = 'gl_coa.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getCOA($where, $like, $order);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                '<input type="radio" name="radio_item" value="' . $row->coa_id . '" data-code="' . $row->coa_code . '" data-desc="' . $row->coa_desc . '" data-other-1="' . $row->coa_code . '" />',
                $row->coa_code,
                $row->coa_desc,
                $row->class_code,
                GLClassType::class_type_name($row->class_type),
                ($row->is_debit > 0) ? 'D' : 'C',
                ''
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function submit_fnspec(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        $specid = $_POST['spec_id'];
        $data['spec_key'] = $_POST['spec_key'];

        if(isset($_POST['transtype_id'])){
            if($_POST['transtype_id'] > 0){
                $data['transtype_id'] = $_POST['transtype_id'];
            }
        }

        if(isset($_POST['coa_id'])){
            if($_POST['coa_id'] > 0){
                $data['coa_id'] = $_POST['coa_id'];
            }
        }

        $data['description'] = $_POST['description'];

        if($specid > 0){
            $this->mdl_general->update('fn_feature_spec', array('id' => $specid), $data);

            $result['type'] = '2';
            $result['message'] = 'Successfully update posting parameter.';
        }
        else {
            $jml = $this->mdl_general->count('fn_feature_spec', array('spec_key' => $data['spec_key']));
            if($jml > 0){
                $result['type'] = '0';
                $result['message'] = 'Parameter already exist.';
            }
            else {
                $data['transtype_id'] = isset($_POST['transtype_id']) ? $_POST['transtype_id'] : 0;
                $data['coa_id'] = isset($_POST['coa_id']) ? $_POST['coa_id'] : 0;
                $data['feature_id'] = 0;

                $this->db->insert('fn_feature_spec', $data);
                $result['type'] = '1';
                $result['message'] = 'Successfully add posting parameter.';
            }
        }

        echo json_encode($result);
    }

    #endregion

    #region CASHFLOW PARAMETER

    public function cashflow_param_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if($type == 1){ //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/cashflow_param_list.php', $data);
            $this->load->view('layout/footer');
        }
        else{
            //Cash Flow Parameter Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('gl_cashflow_parameter', array('param_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['param_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/cashflow_param_form.php', $data);
            $this->load->view('layout/footer');
        }
    }

    public function submit_cashflow_param(){
        if(isset($_POST)){
            $param_id = $_POST['param_id'];
            $has_error = false;

            $data['coa_code'] = $_POST['coa_code'];

            if($param_id > 0)
            {
                $exist = $this->mdl_general->count('gl_cashflow_parameter', array('coa_code' => $data['coa_code'], 'status <>' => STATUS_DELETE));

                if($exist <= 0){
                    $coas = $this->db->get_where('gl_coa',array('coa_code' => $data['coa_code']));

                    if($coas->num_rows() > 0){
                        $coa = $coas->row();
                        $data['coa_id'] = $coa->coa_id;
                        $data['param_desc'] = $coa->coa_desc;

                        $this->mdl_general->update('gl_cashflow_parameter', array('param_id' => $param_id), $data);
                    }

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Parameter ' . $data['coa_code'] . ' successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'warning');
                    $this->session->set_flashdata('flash_message', 'Parameter ' . $data['coa_code'] .' already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('gl_cashflow_parameter', array('coa_code' => $data['coa_code'], 'status <>' => STATUS_DELETE));

                if($exist <= 0){
                    $coas = $this->db->get_where('gl_coa',array('coa_code' => $data['coa_code']));

                    if($coas->num_rows() > 0){
                        $coa = $coas->row();
                        $data['coa_id'] = $coa->coa_id;
                        $data['param_desc'] = $coa->coa_desc;

                        $this->db->insert('gl_cashflow_parameter', $data);
                        $param_id = $this->db->insert_id();
                    }

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Parameter ' . $data['coa_code'] .' successfully added.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Parameter ' . $data['coa_code'] . ' already exist.');
                }
            }

            if($has_error){
                redirect(base_url('finance/setup/cashflow_param_manage/0/' . $param_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/setup/cashflow_param_manage/1.tpd'));
                }
            }
        }
    }

    public function cashflow_param_list(){
        $where['gl_cashflow_parameter.status <>'] = STATUS_DELETE;

        $records["data"] = array();

        $qry = $this->mdl_general->get('gl_cashflow_parameter',$where);

        foreach($qry->result_array() as $row){
            $records["data"][] = array(
                $row['coa_code'],
                $row['param_desc'],
                '<div class="btn-group btn-group-solid">
                 <a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('finance/setup/cashflow_param_manage/0/' . $row['param_id']) . '.tpd"><i class="fa fa-search"></i></a>
                <a class="btn btn-xs red-thunderbird btn-bootbox tooltips" data-original-title="Delete" data-placement="top" data-container="body" href="javascript:;" data-id="' . $row['param_id'] . '" data-link="' . base_url('finance/setup/cashflow_param_delete/' . $row['param_id']). '"> <i class="fa fa-times"></i> </a>
                 </div>'
            );
        }

        $records["recordsTotal"] = 0;

        echo json_encode($records);
    }

    public function cashflow_param_delete($param_id = 0){
        if($param_id > 0){
            $this->mdl_general->update('gl_cashflow_parameter', array('param_id' => $param_id), array('status' => STATUS_DELETE));

            $this->session->set_flashdata('flash_message_class', 'success');
            $this->session->set_flashdata('flash_message', 'Parameter successfully deleted.');
        }

        redirect(base_url('finance/setup/cashflow_param_manage/1.tpd'));
    }

    #endregion

    #region ACCOUNTING PERIOD

    public function gl_period_manage($type = 1)
    {
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

        $qry = $this->db->query("select * from gl_period");

        $closePeriod = $this->db->query("select top 1 * from gl_closing_header
                                             where status NOT IN(0,6) and is_yearly <= 0
                                             order by closingdate desc");

        $list_year = array();
        if ($qry->num_rows() <= 0) {
            //Insert GL Period
            if ($closePeriod->num_rows() > 0) {
                $row = $closePeriod->row();

                $nextYear = date("Y", strtotime(date("Y-m-d", ymd_from_db($row->closingdate)), " +10 day"));
                $nextMonth = date("m", strtotime(date("Y-m-d", ymd_from_db($row->closingdate)), " +10 day"));

                $data['period_year'] = $nextYear;
                $data['period_month'] = $nextMonth;

                $this->db->insert('gl_period', $data);

                array_push($list_year, $data['period_year']);

                if ($row->closingyear != $nextYear) {
                    array_push($list_year, $row->closingyear);
                }
            } else {
                $data['period_year'] = date('Y');
                $data['period_month'] = date('m');

                $this->db->insert('gl_period', $data);

                array_push($list_year, date('Y'));
            }
        } else {
            $row = $qry->row();

            $data['gl_period'] = $row;
            $data['period_year'] = $row->period_year;
            $data['period_month'] = $row->period_month;

            if (!in_array(date('Y'), $list_year)) {
                array_push($list_year, date('Y'));
            }

            if (!in_array($data['period_year'], $list_year)) {
                array_push($list_year, $data['period_year']);
            }

            if ($closePeriod->num_rows() > 0) {
                $row = $closePeriod->row();
                if ($row->closingyear != $data['period_year']) {
                    if (!in_array($row->closingyear, $list_year)) {
                        array_push($list_year, $row->closingyear);
                    }
                }
            } else {
                //array_push($list_year,$data['period_year']-1);
            }
        }

        //Bind Dropdown Year & Month
        $data['list_year'] = $list_year;

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/setup/gl_period', $data);
        $this->load->view('layout/footer');
    }

    /*
    public function gl_period_manage_v1($type = 1){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

        $qry = $this->db->query("select * from gl_period");

        $closePeriod = $this->db->query("select top 1 * from gl_closing_header
                                             where status > 0 and is_yearly <= 0
                                             order by closingdate desc");

        $list_year = array();
        if($qry->num_rows() <= 0){
            //Insert GL Period
            if($closePeriod->num_rows() > 0){
                $row = $closePeriod->row();

                $nextYear = date("Y", strtotime(date("Y-m-d", $row->closingdate) . " +10 day"));
                $nextMonth = date("m", strtotime(date("Y-m-d", $row->closingdate) . " +10 day"));

                $data['period_year'] = $nextYear;
                $data['period_month'] = $nextMonth;

                $this->db->insert('gl_period', $data);

                array_push($list_year,$data['period_year']);

                if($row->closingyear != $nextYear){
                    array_push($list_year,$row->closingyear);
                }
            }else{
                $data['period_year'] = date('Y');
                $data['period_month'] = date('m');

                $this->db->insert('gl_period', $data);

                array_push($list_year,date('Y'));
            }
        }else{
            $row = $qry->row();

            $data['gl_period'] = $row;
            $data['period_year'] = $row->period_year;
            $data['period_month'] = $row->period_month;

            array_push($list_year,$data['period_year']);

            if($closePeriod->num_rows() > 0){
                $row = $closePeriod->row();
                if($row->closingyear != $data['period_year'])
                    array_push($list_year,$row->closingyear);

            }else{

            }
        }

        //Bind Dropdown Year & Month
        $data['list_year'] = $list_year;

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/setup/gl_period', $data);
        $this->load->view('layout/footer');
    }
    */

    public function submit_gl_period(){
        if(isset($_POST)){
            $has_error = false;

            $data['period_year'] = $_POST['period_year'];
            $data['period_month'] = $_POST['period_month'];

            $this->db->update('gl_period', $data, array('period_id <>' => 0));

            $this->session->set_flashdata('flash_message_class', 'success');
            $this->session->set_flashdata('flash_message', 'Period successfully updated.');

            if($has_error){
                redirect(base_url('finance/setup/gl_period_manage/0.tpd'));
            }
            else {
                redirect(base_url('finance/setup/gl_period_manage/0.tpd'));
            }
        }
    }

    public function ajax_period_month()
    {
        $result = '';

        $year = $_POST['period_year'];

        if ($year != '') {
            $currentYear = date('Y');
            $month = date('m');

            $currentPeriod = $this->db->query("select * from gl_period");
            $current = $currentPeriod->row();

            $closePeriod = $this->db->query("select top 1 * from gl_closing_header
                                             where status not in(0,6) and is_yearly <= 0
                                             order by closingdate desc");

            if ($closePeriod->num_rows() > 0) {
                $row = $closePeriod->row();
                if ($row->closingyear == $currentYear) {
                    //echo 'A1';
                    for ($i = $row->closingmonth; $i <= $month; $i++) {
                        $result .= "<option value='" . $i . "' ";
                        if ($current->period_year == $year) {
                            if ($current->period_month == $i) {
                                $result .= " selected ";
                            }
                        }
                        $result .= ">" . $i . "</option>";
                    }
                } else {
                    if ($currentYear != $year) {
                        //echo 'A2A';
                        for ($i = $row->closingmonth; $i <= 12; $i++) {
                            $result .= "<option value='" . $i . "' ";
                            if ($current->period_month == $i) {
                                $result .= " selected ";
                            }
                            $result .= ">" . $i . "</option>";
                        }
                    } else {
                        //echo 'A2B';
                        //for ($i = $row->closingmonth; $i <= 12; $i++) {
                        for ($i = 1; $i <= $month; $i++) {
                            $result .= "<option value='" . $i . "' ";
                            if ($current->period_year == $year) {
                                if ($current->period_month == $i) {
                                    $result .= " selected ";
                                }
                            }
                            $result .= ">" . $i . "</option>";
                        }
                    }
                }
            } else {
                echo 'B1';
                for ($i = 1; $i <= $month; $i++) {
                    $result .= "<option value='" . $i . "' ";
                    if ($current->period_year == $year) {
                        if ($current->period_month == $i) {
                            $result .= " selected ";
                        }
                    }
                    $result .= ">" . $i . "</option>";
                }
            }
        }

        echo $result;
    }

    /*
    public function ajax_period_month(){
        $result = '';

        $year = $_POST['period_year'];

        if($year != ''){
            $currentPeriod = $this->db->query("select * from gl_period");
            $current = $currentPeriod->row();

            $currentYear = date('Y');
            $month = date('m');

            $closePeriod = $this->db->query("select top 1 * from gl_closing_header
                                             where status not in(0,6) and is_yearly <= 0
                                             order by closingdate desc");

            if($closePeriod->num_rows() > 0){
                $row = $closePeriod->row();
                if($row->closingyear == $currentYear){
                    for($i=$row->closingmonth;$i<=$month;$i++){
                        $result .= "<option value='" . $i . "' ";
                        if($current->period_year == $year){
                            if($current->period_month == $i){
                                $result .= " selected ";
                            }                        }
                        $result .= ">" . $i . "</option>";
                    }
                }else{
                    for($i=$row->closingmonth;$i<=12;$i++){
                        $result .= "<option value='" . $i . "' ";
                        if($current->period_year == $year){
                            if($current->period_month == $i){
                                $result .= " selected ";
                            }                        }
                        $result .= ">" . $i . "</option>";
                    }
                }
            }else{
                for($i=1;$i<=$month;$i++){
                    $result .= "<option value='" . $i . "' ";
                    if($current->period_year == $year){
                        if($current->period_month == $i){
                            $result .= " selected ";
                        }                        }
                    $result .= ">" . $i . "</option>";
                }
            }
        }

        echo $result;
    }

    */

    #endregion

    #region PAYMENT TYPE

    public function payment_type_manage($type = 0, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if($type <= 0){ //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/paymenttype_list', $data);
            $this->load->view('layout/footer');
        }else{
            //Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('ms_payment_type', array('paymenttype_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['paymenttype_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/paymenttype_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function submit_payment_type(){
        if(isset($_POST)){
            $paymenttype_id = $_POST['paymenttype_id'];
            $has_error = false;

            $data['paymenttype_code'] = $_POST['paymenttype_code'];
            $data['paymenttype_desc'] = $_POST['paymenttype_desc'];
            $data['coa_code'] = $_POST['coa_code'];
            $data['payment_type'] = isset($_POST['payment_type']) ? $_POST['payment_type'] : 0;
            $data['card_percent'] = $_POST['card_percent'];
            $data['veritrans_fee'] = $_POST['veritrans_fee'];

            if($paymenttype_id > 0)
            {
                $paytype = $this->db->get_where('ms_payment_type', array('paymenttype_id' => $paymenttype_id));
                if($paytype->num_rows() > 0){
                    $paytype = $paytype->row();
                    if($paytype->payment_type == $data['payment_type'] || $paytype->payment_type <= 0){
                        $exist = $this->mdl_general->count('ms_payment_type', array('paymenttype_code' => $data['paymenttype_code'], 'paymenttype_id <>' => $paymenttype_id));

                        if($exist <= 0){
                            $this->mdl_general->update('ms_payment_type', array('paymenttype_id' => $paymenttype_id), $data);

                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Payment Type ' . $data['paymenttype_code'] . ' successfully updated.');
                        }
                        else {
                            $has_error = true;

                            $this->session->set_flashdata('flash_message_class', 'danger');
                            $this->session->set_flashdata('flash_message', 'Payment Type ' . $data['paymenttype_code'] .' already exist.');
                        }
                    }else{
                        $has_error = true;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Payment Type ' . PAYMENT_TYPE::caption($data['payment_type']) . ' already registered. Please modify existing Payment Type');
                    }
                }

            }else {
                $isexist = $this->db->get_where('ms_payment_type', array('payment_type' => $data['payment_type']));
                if($isexist->num_rows() <= 0){
                    $data['status'] = STATUS_NEW;

                    $exist = $this->mdl_general->count('ms_payment_type', array('paymenttype_code' => $data['paymenttype_code']));
                    if($exist <= 0){
                        $this->db->insert('ms_payment_type', $data);
                        $paymenttype_id = $this->db->insert_id();

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Payment Type ' . $data['paymenttype_code'] .' successfully registered.');
                    }
                    else {
                        $has_error = true;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Payment Type ' . $data['paymenttype_code'] . ' already exist.');
                    }
                }else{
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Payment Type ' . PAYMENT_TYPE::caption($data['payment_type']) . ' already registered. Please modify existing Payment Type');
                }
            }

            if($has_error){
                redirect(base_url('finance/setup/payment_type_manage/1/' . $paymenttype_id. '.tpd' ));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/setup/payment_type_manage/0/0.tpd'. ''));
                }
            }
        }
    }

    public function payment_type_list(){
        $records["data"] = array();

        $qry = $this->db->get_where('ms_payment_type', array('status <>' => STATUS_DELETE));

        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->paymenttype_code,
                $row->paymenttype_desc,
                $row->coa_code,
                PAYMENT_TYPE::caption($row->payment_type),
                format_num($row->card_percent,2) . ' %',
                format_num($row->veritrans_fee,0),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('finance/setup/payment_type_manage/1/' . $row->paymenttype_id) . '.tpd"><i class="fa fa-search"></i></a>'
            );
        }

        $records["recordsTotal"] = 0;

        echo json_encode($records);
    }

   #endregion

    #region DEPOSIT TYPE

    public function deposit_type_manage($type = 0, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if($type <= 0){ //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/deposittype_list', $data);
            $this->load->view('layout/footer');
        }else{
            //Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('ms_deposit_type', array('deposittype_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['deposittype_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('finance/setup/deposittype_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function submit_deposit_type(){
        if(isset($_POST)){
            $deposittype_id = $_POST['deposittype_id'];
            $has_error = false;

            $data['deposit_key'] = $_POST['deposit_key'];
            $data['deposit_desc'] = $_POST['deposit_desc'];
            $data['coa_code'] = $_POST['coa_code'];

            if($deposittype_id > 0)
            {
                $exist = $this->mdl_general->count('ms_deposit_type', array('deposit_key' => $data['deposit_key'], 'deposittype_id <>' => $deposittype_id));

                if($exist <= 0){
                    $data['status'] = $_POST['status'];
                    $this->mdl_general->update('ms_deposit_type', array('deposittype_id' => $deposittype_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Deposit Type ' . $data['deposit_desc'] . ' successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Deposit Type ' . $data['deposit_desc'] .' already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('ms_deposit_type', array('deposit_key' => $data['deposit_key']));

                if($exist == 0){
                    $this->db->insert('ms_deposit_type', $data);
                    $deposittype_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Deposit Type ' . $data['deposit_desc'] .' successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Deposit Type ' . $data['deposit_desc'] . ' already exist.');
                }
            }

            if($has_error){
                redirect(base_url('finance/setup/deposit_type_manage/1/' . $deposittype_id. '.tpd' ));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/setup/deposit_type_manage/0/0.tpd'. ''));
                }
            }
        }
    }

    public function deposit_type_list(){
        $records["data"] = array();

        $qry = $this->db->get_where('ms_deposit_type', array('status <>' => STATUS_DELETE));

        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->deposit_key,
                $row->deposit_desc,
                $row->coa_code,
                get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('finance/setup/deposit_type_manage/1/' . $row->deposittype_id) . '.tpd"><i class="fa fa-search"></i></a>'
            );

        }

        $records["recordsTotal"] = 0;

        echo json_encode($records);
    }

    #endregion

}

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */