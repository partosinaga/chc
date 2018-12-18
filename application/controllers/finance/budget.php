<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget extends CI_Controller {

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
        $this->budget_manage();
    }

    #region Budget Entry
    public function budget_form($type = 1, $id = 0, $periodYear = ''){
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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/fuelux/js/spinner.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        //HEADER
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        //array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        //FOOTER
        array_push($data_footer['footer_script'], base_url() . 'assets/custom/journal_form.js');

        $data['budget_id'] = $id;

        $dept = $this->mdl_general->get('ms_department', array('status ' => STATUS_NEW), array());
        $data['dept_list'] = $dept->result_array();

        if($id > 0){
            $qry = $this->db->get_where('gl_budget_header', array('budget_id' => $id));
            $data['row'] = $qry->row();

            $qry2 = $this->mdl_finance->getJoin('gl_budget_detail.*','gl_budget_detail', array(), array('budget_id' => $id));
            $data['qry_det'] = $qry2->result_array();

        }

        $data['budget_year'] = date('Y');
        if($periodYear != '')
            $data['budget_year'] = $periodYear;

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/journal/budget_form', $data);
        $this->load->view('layout/footer', $data_footer);
    }

    public function submit_budget(){
        $valid = true;

        if(isset($_POST)){
            $budgetID = $_POST['budget_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $data['budget_year'] = $_POST['budget_year'];
            $data['budget_code'] = 'V'. $data['budget_year'];
            $data['coa_code'] = $_POST['coa_code'];
            $data['department_id'] = 0;
            $data['budget_desc'] = '';
            $data['budget_type'] = $_POST['budget_type'];
            $data['budget_variableamount'] = $_POST['budget_variableamount'];

            //unset($_POST['budget_id']);
            if($budgetID > 0){
                $data['modified_by'] = my_sess('user_id');
                $data['modified_date'] = date('Y-m-d H:i:s');
                $this->mdl_general->update('gl_budget_header', array('budget_id' => $budgetID), $data);

                //echo '<br>step 3 update';

                //update details
                if($valid){
                    //echo '<br>step 4 update';
                    $valid = $this->insertDetailEntries($budgetID, $data);

                    //echo '<br>step 5 update';

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                }
            }
            else {
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = date('Y-m-d H:i:s');

                $this->db->insert('gl_budget_header', $data);
                $budgetID = $this->db->insert_id();

                if($budgetID > 0){
                    $valid = $this->insertDetailEntries($budgetID, $data);

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
                redirect(base_url('finance/budget/budget_form/1/' . $budgetID . '/' . $data['budget_year'] . '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('finance/budget/budget_manage/'. $data['budget_year'] .'.tpd'));
                }
                else {
                    redirect(base_url('finance/budget/budget_form/1/' . $budgetID . '/' . $data['budget_year'] . '.tpd'));
                }
            }
        }
    }

    public function insertDetailEntries($budgetID = 0, $data = array()){
        $valid = true;

        if($budgetID > 0 && isset($_POST) && isset($data)){
            $budget_amount = isset($_POST['budget_amount']) ? $_POST['budget_amount'] : array();

            if(count($budget_amount) > 0){
                //echo '<br>Count detail ' . count($dept);
                for ($i = 0; $i <= max(array_keys($budget_amount)); $i++) {
                    $month = $i+1;
                    if($valid){
                        if(isset($budget_amount[$i])){
                            $qry = $this->db->get_where('gl_budget_detail',array('budget_id' => $budgetID, 'budget_month' => $month));
                            if($qry->num_rows() > 0){
                                $detail['budget_amount'] = $budget_amount[$i];

                                $this->mdl_general->update('gl_budget_detail', array('budgetdetail_id' => $qry->row()->budgetdetail_id), $detail);
                            }else{
                                $detail['budget_id'] = $budgetID;
                                $detail['budget_month'] = $month;
                                $detail['budget_amount'] = $budget_amount[$i];
                                $detail['status'] = STATUS_NEW;

                                $this->db->insert('gl_budget_detail', $detail);
                                $insertID = $this->db->insert_id();

                                if($insertID <= 0){
                                    $valid = false;
                                }
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

    public function ajax_delete_budget(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $budget_id = $_POST['budget_id'];
        $data['status'] = $_POST['action'];

        if($budget_id > 0 && $data['status'] > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            if($data['status'] == STATUS_DELETE){
                $this->db->delete('gl_budget_header', array('budget_id' => $budget_id));

                $data_log['action_type'] = STATUS_DELETE;
                $this->db->insert('app_log', $data_log);

                $result['type'] = '1';
                $result['message'] = 'Budget successfully deleted';
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

        echo json_encode($result);
    }

    #endregion

    #region Manage
    public function budget_manage($year = 0){
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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/fuelux/js/spinner.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        if($year < 2000)
            $year = date('Y');

        $data['budget_year'] = $year;
        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/journal/budget_manage', $data);
        $this->load->view('layout/footer');
    }

    public function get_budget_manage($menu_id = 0, $year = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['gl_budget_header.status'] = STATUS_NEW;
        if($year > 0){
            $where['gl_budget_header.budget_year'] = $year;
        }else{
            $where['gl_budget_header.budget_year'] = date('Y');
        }

        $like = array();

        if(isset($_REQUEST['filter_coa_code'])){
            if($_REQUEST['filter_coa_code'] != ''){
                $where['gl_coa.coa_code'] = $_REQUEST['filter_coa_code'];
            }
        }

        $joins = array('gl_coa' => 'gl_coa.coa_code = gl_budget_header.coa_code');
        $iTotalRecords = $this->mdl_finance->countJoin('gl_budget_header',$joins , $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'gl_budget_header.coa_code asc';
        if(isset($_REQUEST['order'])){
            /*
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'gl_budget_header.entry_remark ' . $_REQUEST['order'][0]['dir'];
            }
            */
        }

        $qry = $this->mdl_finance->getJoin('gl_budget_header.*, gl_coa.coa_desc, (select isnull(sum(budget_amount),0) from gl_budget_detail where budget_id = gl_budget_header.budget_id) as budget_amount','gl_budget_header', $joins,
                                            $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["last_query"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result_array() as $row){
            $btn_action = '';
            if($row['status'] == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_EDIT)){
                    $btn_action .= '<li> <a href="' . base_url('finance/budget/budget_form/0/' . $row['budget_id'] . '/'. $year) . '.tpd">Edit</a> </li>';
                    $btn_action .= '<li> <a href="javascript:;" class="btn-bootbox" data-action="' . STATUS_DELETE . '" data-id="' . $row['budget_id'] . '">' . get_action_name(STATUS_DELETE, false) . '</a> </li>';
                }
                else {
                    $btn_action .= '<li> <a href="' . base_url('finance/budget/budget_form/0/' . $row['budget_id'] . '/'. $year) . '.tpd">View</a> </li>';
                }
            }

            $records["data"][] = array(
                $row['budget_year'],
                $row['coa_code'],
                $row['coa_desc'],
                format_num($row['budget_amount'],0),
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

    public function ajax_copy_budget(){
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $year = $_POST['year'];
        if($year > 2000){
            $valid = true;

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            try{
                $this->db->query("DELETE gl_budget_detail FROM gl_budget_detail
                                  JOIN gl_budget_header ON gl_budget_detail.budget_id = gl_budget_header.budget_id
                                  WHERE gl_budget_header.budget_year = " . $year);
                $this->db->delete('gl_budget_header', array('budget_year' => $year));

                $prevBudget = $this->db->get_where('gl_budget_header', array('budget_year'=> $year - 1));
                foreach($prevBudget->result_array() as $prev){
                    $prev_budgetId = $prev['budget_id'];

                    unset ($prev['budget_id']);
                    $prev['budget_year'] = $year;
                    $prev['budget_code'] = 'V'.$year;
                    $prev['created_by'] = my_sess('user_id');
                    $prev['created_date'] = date('Y-m-d H:i:s');
                    $prev['modified_by'] = my_sess('user_id');
                    $prev['modified_date'] = date('Y-m-d H:i:s');

                    $this->db->insert('gl_budget_header', $prev);
                    $budgetId = $this->db->insert_id();

                    if($budgetId > 0){
                        $details = $this->db->get_where('gl_budget_detail',array('budget_id' => $prev_budgetId));
                        if($details->num_rows() > 0){
                            foreach($details->result_array() as $detail){
                                unset($detail['budgetdetail_id']);

                                $detail['budget_id'] = $budgetId;
                                $this->db->insert('gl_budget_detail', $detail);
                            }
                        }
                    }
                }
            }catch(Exception $e){
                $valid = false;
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
                if($valid){
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = 'Budget successfully Copied.';
                }else{
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be processed.';
                }
            }
        }

        echo json_encode($result);
    }

    #endregion

}