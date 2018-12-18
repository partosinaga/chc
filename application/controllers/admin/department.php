<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Department extends CI_Controller {

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
        $this->department_manage();
    }

    #region DEPARTMENT

    public function department_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');

        if($type == 1){ //Department List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('admin/department_list', $data);
            $this->load->view('layout/footer');
        }
        else{ // Department Form
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('ms_department', array('department_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['dept_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('admin/department_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function department_delete($id = 0){
        if($id > 0)
        {
            $this->mdl_general->update('ms_department', array('department_id' => $id), array('status' => STATUS_DELETE));

            $this->session->set_flashdata('flash_message', 'Record successfully deleted.');
            $this->session->set_flashdata('flash_type', 'alert-success');
        }

        redirect(base_url('admin/department/department_manage/1.tpd'));
    }

    public function submit_department(){
        if(isset($_POST)){
            $dept_id = $_POST['dept_id'];
            $has_error = false;

            $data['department_name'] = strtoupper($_POST['dept_name']);
            $data['department_desc'] = $_POST['dept_desc'];

            if($dept_id > 0)
            {
                $exist = $this->mdl_general->count('ms_department', array('department_name' => $data['department_name'], 'department_id <>' => $dept_id));

                if($exist <= 0){
                    $data['status'] = $_POST['status'];
                    $this->mdl_general->update('ms_department', array('department_id' => $dept_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Department successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Department already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('ms_department', array('department_name' => $data['department_name']));

                if($exist == 0){
                    $this->db->insert('ms_department', $data);
                    $dept_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Department successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Department already exist.');
                }
            }

            if($has_error){
                redirect(base_url('admin/department/department_manage/' . $dept_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('admin/department/department_manage/1.tpd'));
                }
                else {
                    redirect(base_url('admin/department/department_manage/0/' . $dept_id . '.tpd'));
                }
            }
        }
    }

    public function department_list($menuid = 0){
        $where['status <>'] = STATUS_DELETE;
        $like = array();

        if(isset($_REQUEST['filter_deptname'])){
            if($_REQUEST['filter_deptname'] != ''){
                $like['department_name'] = $_REQUEST['filter_deptname'];
            }
        }
        if(isset($_REQUEST['filter_deptdesc'])){
            if($_REQUEST['filter_deptdesc'] != ''){
                $like['department_desc'] = $_REQUEST['filter_deptdesc'];
            }
        }
        /*
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $where['status'] = $_REQUEST['filter_status'];
            }
        }
        */

        $qry_tot = $this->mdl_general->count('ms_department', $where, $like);

        $iTotalRecords = $qry_tot;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ms_department.department_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ms_department.department_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ms_department.department_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ms_department.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('ms_department', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $hasDelete = check_session_action($menuid, STATUS_DELETE);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $i . '.',
                $row->department_name,
                $row->department_desc,
                get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('admin/department/department_manage/0/' . $row->department_id) . '.tpd"><i class="fa fa-search"></i></a>'.
                ($hasDelete ? '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs red-thunderbird tooltips btn-bootbox" href="javascript:;" data-link="' . base_url('admin/department/department_delete/' . $row->department_id) . '.tpd"><i class="fa fa-times"></i></a>' : '')
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

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */