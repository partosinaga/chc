<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends CI_Controller {

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
        $this->project_manage();
    }

    #region PROJECT

    public function project_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if($type == 1){ //Project List
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
            $this->load->view('admin/project_list', $data);
            $this->load->view('layout/footer');
        }else if($type == 2){ //Project Users

            array_push($data_header['style'], base_url() . 'assets/global/plugins/icheck/skins/all.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/jquery-tags-input/jquery.tagsinput.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-select/bootstrap-select.min.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/jquery-multi-select/css/multi-select.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/icheck/icheck.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-select/bootstrap-select.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js');

            $data = array();

            $data['project_id'] = $id;
            if($id > 0){
                $qry = $this->db->get_where('ms_project', array('project_id' => $id));
                $data['row'] = $qry->row();

                $qry_users = $this->db->get_where('ms_project_user', array('project_id' => $id));
                $userarray = array();
                foreach($qry_users->result_array() as $user){
                    array_push($userarray, $user['user_name']);
                }
            }

            $data['project_user'] = $userarray;

            $this->load->view('layout/header', $data_header);
            $this->load->view('admin/project_user', $data);
            $this->load->view('layout/footer');
        }
        else{ // Project Form
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/ckeditor/ckeditor.js');

            if($id > 0){
                $qry = $this->mdl_general->get('ms_project', array('project_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['project_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('admin/project_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function project_delete($id = 0){
        if($id > 0)
        {
            $this->mdl_general->update('ms_project', array('project_id' => $id), array('status' => STATUS_DELETE));

            $this->session->set_flashdata('flash_message', 'Record successfully deleted.');
            $this->session->set_flashdata('flash_type', 'alert-success');
        }

        redirect(base_url('admin/project/project_manage/1.tpd'));
    }

    public function submit_project(){
        if(isset($_POST)){
            $project_id = $_POST['project_id'];
            $has_error = false;

            $data['project_initial'] = strtoupper($_POST['project_initial']);
            $data['project_name'] = strtoupper($_POST['project_name']);
            $data['project_address'] = $_POST['project_address'];
            $data['po_report_note'] = $_POST['po_report_note'];

            if($project_id > 0)
            {
                $exist = $this->mdl_general->count('ms_project', array('project_initial' => $data['project_initial'], 'project_id <>' => $project_id));

                if($exist <= 0){
                    $this->mdl_general->update('ms_project', array('project_id' => $project_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Project successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Project already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('ms_project', array('project_initial' => $data['project_initial']));

                if($exist == 0){
                    $this->db->insert('ms_project', $data);
                    $project_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Project successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Project already exist.');
                }
            }

            if($has_error){
                redirect(base_url('admin/project/project_manage/' . $project_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('admin/project/project_manage/1.tpd'));
                }
                else {
                    redirect(base_url('admin/project/project_manage/0/' . $project_id . '.tpd'));
                }
            }
        }
    }

    public function project_list($menuid = 0){
        $where['status <>'] = STATUS_DELETE;
        $like = array();

        if(isset($_REQUEST['filter_projectinitial'])){
            if($_REQUEST['filter_projectinitial'] != ''){
                $like['project_initial'] = $_REQUEST['filter_projectinitial'];
            }
        }
        if(isset($_REQUEST['filter_projectname'])){
            if($_REQUEST['filter_projectname'] != ''){
                $like['project_name'] = $_REQUEST['filter_projectname'];
            }
        }

        /*
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $where['status'] = $_REQUEST['filter_status'];
            }
        }
        */

        $qry_tot = $this->mdl_general->count('ms_project', $where, $like);

        $iTotalRecords = $qry_tot;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ms_project.project_initial asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ms_project.project_initial ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ms_project.project_name ' . $_REQUEST['order'][0]['dir'];
            }
            /*
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ms_project.status ' . $_REQUEST['order'][0]['dir'];
            }*/
        }

        $qry = $this->mdl_general->get('ms_project', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$hasDelete = check_session_action($menuid, STATUS_DELETE);
        $hasDelete = false;

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $i . '.',
                $row->project_initial,
                $row->project_name,
                nl2br($row->project_address),
                //get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('admin/project/project_manage/0/' . $row->project_id) . '.tpd"><i class="fa fa-edit"></i></a>'.
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs blue-chambray tooltips" href="' . base_url('admin/project/project_manage/2/' . $row->project_id) . '.tpd"><i class="fa fa-user"></i></a>'
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function submit_project_user(){
        if(isset($_POST)){
            $project_id = $_POST['project_id'];
            $has_error = false;

            $projectusers = $_POST['project_users'];

            if($project_id > 0){
                $usernames = explode(',',$projectusers);

                //BEGIN TRANSACTION
                $this->db->trans_begin();

                $currentids = array();
                $data = array();
                foreach($usernames as $username){
                    if(trim($username) != ''){
                        $users = $this->db->get_where('ms_user',array('user_name' => $username));
                        if($users->num_rows() > 0){
                            $user = $users->row();

                            $exist = $this->mdl_general->count('ms_project_user', array('user_id' => $user->user_id, 'project_id' => $project_id));
                            if($exist <= 0){

                                unset($data);
                                $data['project_id'] = $project_id;
                                $data['user_name'] = $username;
                                $data['user_id'] = $user->user_id;

                                $this->db->insert('ms_project_user', $data);
                                //$projectuser_id = $this->db->insert_id();
                            }

                            array_push($currentids, $user->user_id);
                        }
                    }
                }

                //Remove any other user id not in this project
                $str_delete = 'DELETE FROM ms_project_user WHERE project_id = ' . $project_id . ' AND user_id NOT IN(' . implode(',', $currentids) . ') ';

                $this->db->query($str_delete);

                //COMMIT OR ROLLBACK
                if(!$has_error){
                    if ($this->db->trans_status() === FALSE)
                    {
                        $this->db->trans_rollback();

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                    }
                    else
                    {
                        $this->db->trans_commit();

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Project users successfully saved.');
                    }
                }else{
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                }
            }

            if($has_error){
                redirect(base_url('admin/project/project_manage/2/' . $project_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('admin/project/project_manage/1.tpd'));
                }
                else {
                    redirect(base_url('admin/project/project_manage/2/' . $project_id . '.tpd'));
                }
            }
        }
    }

    #endregion
}

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */