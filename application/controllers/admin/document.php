<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Document extends CI_Controller {

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
        $this->doc_manage();
    }

    #region DOCUMENT

    public function doc_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if($type == 1){ //Department List
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
            $this->load->view('admin/doc_list', $data);
            $this->load->view('layout/footer');
        }
        else{ // Doc Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('document', array('doc_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['doc_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('admin/doc_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function submit_doc(){
        if(isset($_POST)){
            $doc_id = $_POST['doc_id'];
            $has_error = false;

            $data['doc_name'] = strtoupper($_POST['doc_name']);
            $data['doc_desc'] = $_POST['doc_desc'];
            $data['doc_length'] = isset($_POST['doc_length']) ? $_POST['doc_length'] : 3;
            $data['feature_id'] = $_POST['feature_id'];
            //$data['last_code'] = '';

            if($doc_id > 0)
            {
                $exist = $this->mdl_general->count('document', array('doc_name' => $data['doc_name'], 'doc_id <>' => $doc_id));

                if($exist <= 0){
                    $this->mdl_general->update('document', array('doc_id' => $doc_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Document successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Document already exist.');
                }
            }else {
               $exist = $this->mdl_general->count('document', array('doc_name' => $data['doc_name']));

                if($exist == 0){
                    $this->db->insert('document', $data);
                    $doc_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Document successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Document already exist.');
                }
            }

            if($has_error){
                redirect(base_url('admin/document/doc_manage/' . $doc_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('admin/document/doc_manage/1.tpd'));
                }
                else {
                    redirect(base_url('admin/document/doc_manage/0/' . $doc_id . '.tpd'));
                }
            }
        }
    }

    public function doc_list(){
        $where['doc_id >'] = 0;

        $like = array();

        if(isset($_REQUEST['filter_docname'])){
            if($_REQUEST['filter_docname'] != ''){
                $like['doc_name'] = $_REQUEST['filter_docname'];
            }
        }

        /*
        if(isset($_REQUEST['filter_featureid'])){
            if($_REQUEST['filter_featureid'] != ''){
                $like['feature_id'] = $_REQUEST['filter_featureid'];
            }
        }
        */

        $qry_tot = $this->mdl_general->count('document', $where, $like);

        $iTotalRecords = $qry_tot;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'feature_id, doc_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'doc_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('document', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //echo 'qry count ' . $qry->num_rows();

        $i = $iDisplayStart + 1;
        if(isset($qry)){
            foreach($qry->result() as $row){
                $records["data"][] = array(
                    $i . '.',
                    $row->doc_name,
                    $row->doc_desc,
                    Feature::get_feature_name($row->feature_id),
                    $row->doc_length,
                    '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('admin/document/doc_manage/0/' . $row->doc_id) . '.tpd"><i class="fa fa-search"></i></a>'
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

}

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */