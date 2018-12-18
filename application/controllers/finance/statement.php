<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statement extends CI_Controller {

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
        $this->layout_setting();
    }

    #region Setting

    public function layout_form($layout_type = 0, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data = array();

        $data['report_type'] = $layout_type;
        $data['doc_id'] = $id;

        $this->load->view('layout/header', $data_header);

        if ($layout_type == 0){
            $this->load->view('finance/statement/layout_ctg_form.php', $data);
        }else {
            $qry = $this->db->query('SELECT * FROM gl_financestatement_category WHERE report_type = ' . $layout_type );
            $data['row_ctg'] = $qry->result_array();

            $this->load->view('finance/statement/layout_detail_form.php', $data);
        }

        $this->load->view('layout/footer');
    }

    public function layout_setting($layout_type = 0){
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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

        $data = array();

        $data['report_type'] = $layout_type;

        $this->load->view('layout/header', $data_header);

        if ($layout_type == 0){
            $ctg = $this->mdl_general->get('gl_financestatement_category', array(), array(), 'report_type');
            $data['row_ctg'] = $ctg->result_array();

            $subctg = $this->mdl_general->get('gl_financestatement_subcategory', array(), array(), 'category_id');
            $data['row_subctg'] = $subctg->result_array();

            $this->load->view('finance/statement/layout_ctg.php', $data);
        }else {
            $this->load->model('finance/mdl_finance');

            $order = 'gl_financestatement_detail.subcategory_id asc';
            $joins = array('gl_financestatement_subcategory'=>'gl_financestatement_subcategory.subcategory_id = gl_financestatement_detail.subcategory_id',
                'gl_financestatement_category'=>'gl_financestatement_category.category_id = gl_financestatement_subcategory.category_id');
            $select = 'gl_financestatement_detail.*, gl_financestatement_category.category_id, gl_financestatement_category.category_key, gl_financestatement_category.category_caption,gl_financestatement_subcategory.subcategory_key,gl_financestatement_subcategory.subcategory_caption';

            $qry = $this->mdl_finance->getJoin($select,'gl_financestatement_detail', $joins, array('gl_financestatement_category.report_type' => $layout_type), array(), $order);
            $data['row_det'] = $qry->result_array();

            if($layout_type == GLStatement::BALANCE_SHEET){
                $this->load->view('finance/statement/layout_bs.php', $data);
            }else{
                $this->load->view('finance/statement/layout_other.php', $data);
            }
        }

        $this->load->view('layout/footer');
    }

    public function ajax_ctg_by_id($ctgId = 0){
        $result = '';

        if($ctgId > 0){
            $this->load->model('finance/mdl_finance');

            $jointable = array('gl_financestatement_category' => 'gl_financestatement_category.category_id = gl_financestatement_subcategory.category_id');

            $qry = $this->mdl_finance->getJoin('gl_financestatement_subcategory.*, gl_financestatement_category.category_key, gl_financestatement_category.category_caption',
            'gl_financestatement_subcategory', $jointable, array('gl_financestatement_category.category_id' => $ctgId));

            if($qry->num_rows() > 0){
                foreach($qry->result() as $row){
                    $result .= '<tr>
                                <td><input type="hidden" name="sub_ctg_id[]" value="' . $row->subcategory_id . '">' . $row->subcategory_key . '</td>
                                <td class="text-left"><input type="text" name="sub_ctg_caption[]" class="form-control input-sm" value="' . $row->subcategory_caption . '"></td>
                                </tr>';
                }
            }
        }

        echo $result;
    }

    public function register_ctg(){
        $valid = true;

        if(isset($_POST)){
            $ctg_id = $_POST['ctg_id'];
            $data['report_type'] = $_POST['report_type'];
            $data['category_key'] = $_POST['category_key'];
            $data['category_caption'] = $_POST['category_caption'];

            if($ctg_id <= 0){
                //BEGIN TRANSACTION
                $this->db->trans_begin();

                $this->db->insert('gl_financestatement_category', $data);
                $ctg_id = $this->db->insert_id();

                //COMMIT OR ROLLBACK
                if($valid){
                    if ($this->db->trans_status() === FALSE)
                    {
                        $this->db->trans_rollback();

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Category can not be saved. Please try again later.');
                    }
                    else
                    {
                        $this->db->trans_commit();

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Category successfully registered.');
                    }
                }else{
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Category can not be saved. Please try again later.');
                }
            }

            if(!$valid){
                redirect(base_url('finance/statement/layout_setting/0.tpd' ));
            }
            else {
                redirect(base_url('finance/statement/layout_setting/0.tpd'));
            }
        }
    }

    public function submit_layout_ctg(){
        $valid = true;

        if(isset($_POST)){
            $ctg_ids = isset($_POST['f_ctg_id']) ? $_POST['f_ctg_id'] : array();
            $ctg_captions = isset($_POST['f_ctg_caption']) ? $_POST['f_ctg_caption'] : array();

            $sub_ctg_ids = isset($_POST['sub_ctg_id']) ? $_POST['sub_ctg_id'] : array();
            $sub_ctg_captions = isset($_POST['sub_ctg_caption']) ? $_POST['sub_ctg_caption'] : array();

            if(count(ctg_ids) > 0){
                //BEGIN TRANSACTION
                $this->db->trans_begin();

                $data = array();
                //UPDATING Layout Category
                for($i = 0;$i < count($ctg_ids);$i++){
                    unset($data);
                    if(isset($ctg_ids[$i]) && isset($ctg_captions[$i])){
                        $data['category_caption'] = $ctg_captions[$i];
                        $this->mdl_general->update('gl_financestatement_category', array('category_id' => $ctg_ids[$i]), $data);
                    }
                }

                //UPDATING Sub category
                for($i = 0;$i < count($sub_ctg_ids);$i++){
                    unset($data);
                    if(isset($sub_ctg_ids[$i]) && isset($sub_ctg_captions[$i])){
                        $data['subcategory_caption'] = $sub_ctg_captions[$i];
                        $this->mdl_general->update('gl_financestatement_subcategory', array('subcategory_id' => $sub_ctg_ids[$i]), $data);
                    }
                }

                //COMMIT OR ROLLBACK
                if($valid){
                    if ($this->db->trans_status() === FALSE)
                    {
                        $this->db->trans_rollback();

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Layout can not be saved. Please try again later.');
                    }
                    else
                    {
                        $this->db->trans_commit();

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Layout successfully updated.');
                    }
                }else{
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Layout can not be saved. Please try again later.');
                }

                //FINALIZE
                if(!$valid){
                    redirect(base_url('finance/statement/layout_setting/0.tpd'));
                }
                else {
                    redirect(base_url('finance/statement/layout_setting/0.tpd'));
                }
            }
        }
    }

    #endregion

    #region Layout
    public function get_subcategory($ctgId = 0)
    {
        if($ctgId > 0){
            echo '<option value="">-- Select --</option>';

            $this->db->select('gl_financestatement_subcategory.*');
            $this->db->from('gl_financestatement_subcategory');
            $this->db->where(array('gl_financestatement_subcategory.category_id' => $ctgId));

            $qry = $this->db->get();

            if($qry->num_rows() > 0){
                foreach($qry->result() as $row){
                    echo '<option value="' . $row->subcategory_id . '">' . $row->subcategory_caption . '</option>';
                }
            }
        }
    }

    public function register_detail($report_type = 0){
        $valid = true;

        if(isset($_POST)){
            //$data['report_type'] = $_POST['report_type'];
            $data['subcategory_id'] = $_POST['subcategory_id'];
            $data['account_name'] = $_POST['account_name'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $this->db->insert('gl_financestatement_detail', $data);
            $detail_id = $this->db->insert_id();

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Layout account can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Layout account successfully registered.');
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Layout account can not be saved. Please try again later.');
            }

            if(!$valid){
                redirect(base_url('finance/statement/layout_setting/' . $report_type . '.tpd' ));
            }
            else {
                redirect(base_url('finance/statement/layout_setting/' . $report_type . '.tpd'));
            }
        }
    }

    public function submit_layout_bs(){
        $valid = true;

        if(isset($_POST)){
            $detail_id = isset($_POST['f_detail_id']) ? $_POST['f_detail_id'] : array();
            $check_is_cf = isset($_POST['check_is_cf']) ? $_POST['check_is_cf'] : array();
            $accounts = isset($_POST['f_account_name']) ? $_POST['f_account_name'] : array();
            $check_is_range = isset($_POST['check_is_range']) ? $_POST['check_is_range'] : array();
            $range_start = isset($_POST['f_range_start']) ? $_POST['f_range_start'] : array();
            $range_end = isset($_POST['f_range_end']) ? $_POST['f_range_end'] : array();
            $text_formula = isset($_POST['f_text_formula']) ? $_POST['f_text_formula'] : array();

            if(count($detail_id) > 0){
                $data = array();
                foreach($detail_id as $id){
                    unset($data);
                    $data = array('iscashflow' => 0,'israngedformula' => 0);
                    $this->mdl_general->update('gl_financestatement_detail', array('detail_id' => $id), $data);
                }

                //BEGIN TRANSACTION
                $this->db->trans_begin();

                //CF reference
                foreach($check_is_cf as $iscf){
                    unset($data);
                    $data['iscashflow'] = 1;
                    $this->mdl_general->update('gl_financestatement_detail', array('detail_id' => $iscf), $data);
                }

                //Is using range formula
                foreach($check_is_range as $isranged){
                    unset($data);
                    $data['israngedformula'] = 1;
                    $this->mdl_general->update('gl_financestatement_detail', array('detail_id' => $isranged), $data);
                }

                //UPDATING Layout Category
                for($i = 0;$i < count($detail_id);$i++){
                    unset($data);
                    if(isset($detail_id) && isset($accounts[$i]) && isset($range_start[$i]) && isset($range_end[$i]) && isset($text_formula[$i])){
                        $data['account_name'] = $accounts[$i];
                        $data['rangeformula_start'] = $range_start[$i];
                        $data['rangeformula_end'] = $range_end[$i];
                        $data['text_formula'] = $text_formula[$i];
                        $this->mdl_general->update('gl_financestatement_detail', array('detail_id' => $detail_id[$i]), $data);
                    }
                }

                //COMMIT OR ROLLBACK
                if($valid){
                    if ($this->db->trans_status() === FALSE)
                    {
                        $this->db->trans_rollback();

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Layout can not be saved. Please try again later.');
                    }
                    else
                    {
                        $this->db->trans_commit();

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Layout successfully updated.');
                    }
                }else{
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Layout can not be saved. Please try again later.');
                }

                //FINALIZE
                if(!$valid){
                    redirect(base_url('finance/statement/layout_setting/1.tpd'));
                }
                else {
                    redirect(base_url('finance/statement/layout_setting/1.tpd'));
                }

            }
        }
    }

    public function submit_layout_by_type($type = GLStatement::PROFIT_LOSS){
        $valid = true;

        if(isset($_POST)){
            $detail_id = isset($_POST['f_detail_id']) ? $_POST['f_detail_id'] : array();
            $accounts = isset($_POST['f_account_name']) ? $_POST['f_account_name'] : array();
            $check_is_range = isset($_POST['check_is_range']) ? $_POST['check_is_range'] : array();
            $range_start = isset($_POST['f_range_start']) ? $_POST['f_range_start'] : array();
            $range_end = isset($_POST['f_range_end']) ? $_POST['f_range_end'] : array();
            $text_formula = isset($_POST['f_text_formula']) ? $_POST['f_text_formula'] : array();

            if(count($detail_id) > 0){
                $data = array();
                foreach($detail_id as $id){
                    unset($data);
                    $data = array('israngedformula' => 0);
                    $this->mdl_general->update('gl_financestatement_detail', array('detail_id' => $id), $data);
                }

                //BEGIN TRANSACTION
                $this->db->trans_begin();

                //Is using range formula
                foreach($check_is_range as $isranged){
                    unset($data);
                    $data['israngedformula'] = 1;
                    $this->mdl_general->update('gl_financestatement_detail', array('detail_id' => $isranged), $data);
                }

                //UPDATING Layout Category
                for($i = 0;$i < count($detail_id);$i++){
                    unset($data);
                    if(isset($detail_id) && isset($accounts[$i]) && isset($range_start[$i]) && isset($range_end[$i]) && isset($text_formula[$i])){
                        $data['account_name'] = $accounts[$i];
                        $data['rangeformula_start'] = $range_start[$i];
                        $data['rangeformula_end'] = $range_end[$i];
                        $data['text_formula'] = $text_formula[$i];
                        $this->mdl_general->update('gl_financestatement_detail', array('detail_id' => $detail_id[$i]), $data);
                    }
                }

                //COMMIT OR ROLLBACK
                if($valid){
                    if ($this->db->trans_status() === FALSE)
                    {
                        $this->db->trans_rollback();

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Layout can not be saved. Please try again later.');
                    }
                    else
                    {
                        $this->db->trans_commit();

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Layout successfully updated.');
                    }
                }else{
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Layout can not be saved. Please try again later.');
                }

                //FINALIZE
                if(!$valid){
                    redirect(base_url('finance/statement/layout_setting/' . $type . '.tpd'));
                }
                else {
                    redirect(base_url('finance/statement/layout_setting/' . $type . '.tpd'));
                }
            }
        }
    }

    public function layout_delete($id = 0, $report_type = 0){
        if($id > 0)
        {
            $this->db->delete('gl_financestatement_detail', array('detail_id' => $id));

            $this->session->set_flashdata('flash_message', 'Record successfully deleted.');
            $this->session->set_flashdata('flash_message_class', 'success');
        }

        redirect(base_url('finance/statement/layout_setting/' . $report_type . '.tpd'));
    }

    #endregion

    #region Statement

    public function generate_bs(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

        $data = array();

        $data['report_title'] = "Balance Sheet";
        $data['report_type'] = REPORTTYPE::STATEMENT_BALANCE_SHEET;

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/statement/report_find.php', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_bs_main($month = 0, $year = 0){
        //Company Profile
        $data['profile'] = print_profile_inv();
        $data['month'] = $month;
        $data['year'] = $year;
        $this->load->view('finance/statement/ajax/balance_sheet_main', $data);
    }

    public function bs_detail_ytd($month = 0, $year = 0, $ctg_name = '', $sub_ctg_name = '', $account_name = '', $is_pdf = false){
        if($year > 0 && $month > 0 && $ctg_name != '' && $sub_ctg_name != '' && $account_name != ''){
            $data_header['title'] = 'Balance Sheet Breakdown YTD';

            $data['profile'] = print_profile_inv();
            $data['month'] = $month;
            $data['year'] = $year;
            $data['ctg_name'] = $ctg_name;
            $data['sub_ctg_name'] = $sub_ctg_name;
            $data['account_name'] = $account_name;

            if(!$is_pdf){
                $this->load->view('finance/statement/layout/header', $data_header);
                $this->load->view('finance/statement/balance_sheet_detail_ytd', $data);
                $this->load->view('finance/statement/layout/footer');
            }else{
                $this->load->view('finance/statement/layout/header_print', $data_header);
                $this->load->view('finance/statement/pdf_balance_sheet_detail_ytd', $data);
                $this->load->view('finance/statement/layout/footer_print');

                $html = $this->output->get_output();

                $this->load->library('wkhtml');

                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="file.pdf"');
                echo $this->snappy->getOutputFromHtml($html, array('orientation' => 'portrait',
                    'page-size' => 'A4'));
            }

        }
        else {
            redirect('home');
        }
    }

    public function ajax_bs_detail_ytd($month = 0, $year = 0, $ctg_name = '', $sub_ctg_name = '', $account_name = ''){
        $data['month'] = $month;
        $data['year'] = $year;
        $data['ctg_name'] = $ctg_name;
        $data['sub_ctg_name'] = $sub_ctg_name;
        $data['account_name'] = $account_name;

        $this->load->view('finance/statement/ajax/balance_sheet_breakdown', $data);
    }

    public function ajax_trial_bs($month = 0, $year = 0){
        $data['month'] = $month;
        $data['year'] = $year;
        $this->load->view('finance/statement/ajax/trial_balance_main', $data);
    }

    public function generate_pl(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

        $data = array();

        $data['report_title'] = "Income Statement";
        $data['report_type'] = REPORTTYPE::STATEMENT_PROFIT_LOSS_STD;

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/statement/report_find.php', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_pl_std_main($month = 0, $year = 0){
        $data['month'] = $month;
        $data['year'] = $year;
        $this->load->view('finance/statement/ajax/income_statement_std_main', $data);
    }

    public function pl_std_breakdown($month = 0, $year = 0, $ctg_name = '', $sub_ctg_name = '', $account_name = '', $is_pdf = false){
        if($year > 0 && $month > 0 && $ctg_name != '' && $sub_ctg_name != '' && $account_name != ''){
            $data_header['title'] = 'Income Statement Standard Detail';

            $data['profile'] = print_profile_inv();
            $data['month'] = $month;
            $data['year'] = $year;
            $data['ctg_name'] = $ctg_name;
            $data['sub_ctg_name'] = $sub_ctg_name;
            $data['account_name'] = $account_name;

            if(!$is_pdf){
                $this->load->view('finance/statement/layout/header', $data_header);
                $this->load->view('finance/statement/income_statement_std_detail', $data);
                $this->load->view('finance/statement/layout/footer');
            }else{
                $this->load->view('finance/statement/layout/header_print', $data_header);
                $this->load->view('finance/statement/pdf_income_statement_std_detail', $data);
                $this->load->view('finance/statement/layout/footer_print');

                $html = $this->output->get_output();

                $this->load->library('wkhtml');

                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="file.pdf"');
                echo $this->snappy->getOutputFromHtml($html, array('orientation' => 'portrait',
                    'page-size' => 'A4'));
            }
        }
        else {
            redirect('home');
        }
    }

    public function pl_std_breakdown_ytd($month = 0, $year = 0, $ctg_name = '', $sub_ctg_name = '', $account_name = '' , $is_pdf = false){
        if($year > 0 && $month > 0 && $ctg_name != '' && $sub_ctg_name != '' && $account_name != ''){
            $data_header['title'] = 'Income Statement Standard YTD';

            $data['profile'] = print_profile_inv();
            $data['month'] = $month;
            $data['year'] = $year;
            $data['ctg_name'] = $ctg_name;
            $data['sub_ctg_name'] = $sub_ctg_name;
            $data['account_name'] = $account_name;
            if(!$is_pdf){
                $this->load->view('finance/statement/layout/header', $data_header);
                $this->load->view('finance/statement/income_statement_std_detail_ytd', $data);
                $this->load->view('finance/statement/layout/footer');
            }else{
                $this->load->view('finance/statement/layout/header_print', $data_header);
                $this->load->view('finance/statement/pdf_income_statement_std_detail_ytd', $data);
                $this->load->view('finance/statement/layout/footer_print');

                $html = $this->output->get_output();

                $this->load->library('wkhtml');

                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="file.pdf"');
                echo $this->snappy->getOutputFromHtml($html, array('orientation' => 'portrait',
                    'page-size' => 'A4'));
            }
        }
        else {
            redirect('home');
        }
    }

    public function ajax_pl_compare_budget_main($month = 0, $year = 0){
        $data['month'] = $month;
        $data['year'] = $year;
        $this->load->view('finance/statement/ajax/income_statement_compare_budget', $data);
    }

    public function generate_cf(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

        $data = array();

        $data['report_title'] = "Cash Flow";
        $data['report_type'] = REPORTTYPE::STATEMENT_CASHFLOW;

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/statement/report_find.php', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_cf_main($month = 0, $year = 0){
        $data['month'] = $month;
        $data['year'] = $year;
        $this->load->view('finance/statement/ajax/cash_flow_main', $data);
    }

    public function cf_breakdown($month = 0, $year = 0, $sub_ctg_name = '', $account_name = '', $is_pdf = false){
        if($year > 0 && $month > 0 && $sub_ctg_name != '' && $account_name != ''){
            $data_header['title'] = 'Income Statement Standard Detail';

            $data['profile'] = print_profile_inv();
            $data['month'] = $month;
            $data['year'] = $year;
            $data['sub_ctg_name'] = $sub_ctg_name;
            $data['account_name'] = $account_name;

            if(!$is_pdf){
                $this->load->view('finance/statement/layout/header', $data_header);
                $this->load->view('finance/statement/cash_flow_detail', $data);
                $this->load->view('finance/statement/layout/footer');
            }else{
                $this->load->view('finance/statement/layout/header_print', $data_header);
                $this->load->view('finance/statement/pdf_cash_flow_detail', $data);
                $this->load->view('finance/statement/layout/footer_print');

                $html = $this->output->get_output();

                $this->load->library('wkhtml');

                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="file.pdf"');
                echo $this->snappy->getOutputFromHtml($html, array('orientation' => 'portrait',
                    'page-size' => 'A4'));
            }
        }
        else {
            redirect('home');
        }
    }

    public function ajax_cash_flow_detail($month = 0, $year = 0, $sub_ctg_name = '', $account_name = ''){
        $data['month'] = $month;
        $data['year'] = $year;
        $data['sub_ctg_name'] = $sub_ctg_name;
        $data['account_name'] = $account_name;
        $this->load->view('finance/statement/ajax/cash_flow_breakdown', $data);
    }

    public function ajax_trial_cf($month = 0, $year = 0){
        $data['month'] = $month;
        $data['year'] = $year;
        $this->load->view('finance/statement/ajax/trial_cf_main', $data);
    }

    public function generate_statement($reportType = 0, $periodMonth = 0, $periodYear = 0) {
        if($reportType > 0 && $periodMonth > 0 && $periodYear > 0){
            $data['header'] = array('company_name'=>'TPD',
                'period' => $periodMonth . ' - ' . $periodYear
                );

            $this->load->model('finance/mdl_finance');

            $data['report_type'] = $reportType;
            $data['month'] = $periodMonth;
            $data['year'] = $periodYear;
            $data['profile'] = print_profile_inv();

            switch($reportType){
                case REPORTTYPE::STATEMENT_TRIAL_BALANCE:
                    $data_header['title'] = 'Trial Balance';

                    //$data_header['no_print'] = false;
                    $this->load->view('finance/statement/layout/header', $data_header);
                    $this->load->view('finance/statement/trial_balance.php', $data);
                    $this->load->view('finance/statement/layout/footer');

                    break;
                case REPORTTYPE::STATEMENT_BALANCE_SHEET:
                    $data_header['title'] = 'Balance Sheet';

                    $this->load->view('finance/statement/layout/header', $data_header);
                    $this->load->view('finance/statement/balance_sheet.php', $data);
                    $this->load->view('finance/statement/layout/footer');
                    break;
                case REPORTTYPE::STATEMENT_PROFIT_LOSS_STD:
                    $data_header['title'] = 'Income Statement';

                    $this->load->view('finance/statement/layout/header', $data_header);
                    $this->load->view('finance/statement/income_statement_std.php', $data);
                    $this->load->view('finance/statement/layout/footer');
                    break;
                case REPORTTYPE::STATEMENT_PROFIT_LOSS_BUDGET:
                    $data_header['title'] = 'Income Statement Compare To Budget';

                    $this->load->view('finance/statement/layout/header', $data_header);
                    $this->load->view('finance/statement/income_statement_compare_budget.php', $data);
                    $this->load->view('finance/statement/layout/footer');
                    break;
                case REPORTTYPE::STATEMENT_CASHFLOW:
                    $data_header['title'] = 'Cash Flow';

                    $this->load->view('finance/statement/layout/header', $data_header);
                    $this->load->view('finance/statement/cash_flow.php', $data);
                    $this->load->view('finance/statement/layout/footer');
                    break;
                case REPORTTYPE::STATEMENT_TRIAL_CASHFLOW:
                    $data_header['title'] = 'Trial CashFlow';

                    $this->load->view('finance/statement/layout/header', $data_header);
                    $this->load->view('finance/statement/trial_cashflow.php', $data);
                    $this->load->view('finance/statement/layout/footer');
                    break;
                default:
                    break;
            }

        }else{
            tpd_404();
        }
    }

    #endregion

    #region Print

    public function print_statement($reportType = 0, $periodMonth = 0, $periodYear = 0){
        if($reportType > 0 && $periodMonth > 0 && $periodYear > 0){
            $data['header'] = array('company_name'=>'TPD',
                'period' => $periodMonth . ' - ' . $periodYear
            );

            //$this->load->model('finance/mdl_finance');

            $data['month'] = $periodMonth;
            $data['year'] = $periodYear;
            $data['profile'] = print_profile_inv();

            switch($reportType){
                case REPORTTYPE::STATEMENT_TRIAL_BALANCE:
                    $data_header['title'] = 'Trial Balance';

                    $this->load->view('finance/statement/layout/header_print', $data_header);
                    $this->load->view('finance/statement/pdf_trial_balance.php', $data);
                    $this->load->view('finance/statement/layout/footer_print');

                    break;
                case REPORTTYPE::STATEMENT_BALANCE_SHEET:
                    $data_header['title'] = 'Balance Sheet';

                    $this->load->view('finance/statement/layout/header_print', $data_header);
                    $this->load->view('finance/statement/pdf_balance_sheet.php', $data);
                    $this->load->view('finance/statement/layout/footer_print');

                    break;
                case REPORTTYPE::STATEMENT_PROFIT_LOSS_STD:
                    $data_header['title'] = 'Income Statement';

                    $this->load->view('finance/statement/layout/header_print', $data_header);
                    $this->load->view('finance/statement/pdf_income_statement_std.php', $data);
                    $this->load->view('finance/statement/layout/footer_print');
                    break;
                case REPORTTYPE::STATEMENT_PROFIT_LOSS_BUDGET:
                    $data_header['title'] = 'Income Statement Compare To Budget';

                    $this->load->view('finance/statement/layout/header_print', $data_header);
                    $this->load->view('finance/statement/pdf_income_statement_compare_budget.php', $data);
                    $this->load->view('finance/statement/layout/footer_print');
                    break;
                case REPORTTYPE::STATEMENT_CASHFLOW:
                    $data_header['title'] = 'Cash Flow';

                    $this->load->view('finance/statement/layout/header_print', $data_header);
                    $this->load->view('finance/statement/pdf_cash_flow.php', $data);
                    $this->load->view('finance/statement/layout/footer_print');
                    break;
                case REPORTTYPE::STATEMENT_TRIAL_CASHFLOW:
                    $data_header['title'] = 'Trial CashFlow';

                    $this->load->view('finance/statement/layout/header_print', $data_header);
                    $this->load->view('finance/statement/pdf_trial_cashflow.php', $data);
                    $this->load->view('finance/statement/layout/footer_print');
                    break;
                default:
                    break;
            }

            //Call Print

            $html = $this->output->get_output();

            $this->load->library('dompdf_gen');

            $this->dompdf->set_paper("A4", "portrait");
            $this->dompdf->load_html($html);
            $this->dompdf->render();

            $this->dompdf->stream($reportType . date('Y_m_d_H_i_s'), array('Attachment'=>0));

            /**
            $this->load->library('wkhtml');

            header('Content-Type: application/pdf');
            //header('Content-Disposition: attachment; filename="file.pdf"');
            header('Content-Disposition: inline; filename="file.pdf"');
            echo $this->snappy->getOutputFromHtml($html, array('orientation' => 'portrait',
                'page-size' => 'A4', 'disable-smart-shrinking' => false));
             **/
        }else{
            tpd_404();
        }
    }

    #endregion
}