<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends CI_Controller {

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
        $this->printprofile_manage();
    }

    #region PROFILE

    public function printprofile_manage($type = 1, $id = 0){
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
            $this->load->view('admin/printprofile_list', $data);
            $this->load->view('layout/footer');
        }
        else{ // Project Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/ckeditor/ckeditor.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js');

            if($id > 0){
                $qry = $this->mdl_general->get('print_profile', array('id' => $id));
                $data['row'] = $qry->row();
            }

            $data['printprofile_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('admin/printprofile_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function printprofile_delete($id = 0){
        if($id > 0)
        {
            $this->mdl_general->update('print_profile', array('id' => $id), array('status' => STATUS_DELETE));

            $this->session->set_flashdata('flash_message', 'Record successfully deleted.');
            $this->session->set_flashdata('flash_type', 'alert-success');
        }

        redirect(base_url('admin/profile/printprofile_manage/1.tpd'));
    }

    public function submit_printprofile(){
        if(isset($_POST)){
            $id = $_POST['printprofile_id'];
            $has_error = false;

            $data['key_code'] = strtoupper($_POST['key_code']);
            $data['company_name'] = $_POST['company_name'];
            $data['company_address'] = $_POST['company_address'];
            $data['signature_note'] = $_POST['signature_note'];
            $data['report_footer'] = $_POST['report_footer'];
            $data['report_footer2'] = $_POST['report_footer2'];
            $data['terms'] = $_POST['terms'];
            $data['report_note'] = $_POST['report_note'];
            $data['approver_name'] = $_POST['approver_name'];
            $data['approver_title'] = $_POST['approver_title'];

            //Save picture
            $prof = $this->db->get_where('print_profile', array('id' => $id));
            if($prof->num_rows() > 0){
                $data = $this->save_profile_logo($data, $prof->row()->picture_name);
            }else{
                $data = $this->save_profile_logo($data, '');
            }

            if($id > 0)
            {
                $exist = $this->mdl_general->count('print_profile', array('key_code' => $data['key_code'], 'id <>' => $id));
                if($exist <= 0){
                    $this->mdl_general->update('print_profile', array('id' => $id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Print Profile successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Print Profile already exist.');
                }
            }else {
                $data['modified_by'] = my_sess('user_id');
                $data['modified_date'] = date('Y-m-d H:i:s');
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('print_profile', array('key_code' => $data['key_code']));
                if($exist <= 0){
                    $this->db->insert('print_profile', $data);
                    $id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Print Profile successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Print Profile already exist.');
                }
            }

            if($has_error){
                redirect(base_url('admin/profile/printprofile_manage/' . $id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('admin/profile/printprofile_manage/1.tpd'));
                }
                else {
                    redirect(base_url('admin/profile/printprofile_manage/0/' . $id . '.tpd'));
                }
            }
        }
    }

    private function save_profile_logo($data = array(), $old_picture_path = ''){
        if(isset($data)){
            $destination_folder = '/assets/img/profile/';

            $new_picture_name = $this->insert_logo_picture($destination_folder);
            if(trim($new_picture_name) != '')
            {
                if(trim(substr($new_picture_name,0,1)) == '.'){
                    $new_picture_name = substr($new_picture_name, 1);
                    //$destination_folder = '.' . $destination_folder ;
                }

                //remove old picture
                //echo '<br>' . $new_picture_name . ' >< ' . $old_picture_path;
                if($new_picture_name != $old_picture_path){
                    if($old_picture_path != '' && $old_picture_path != null){
                        if(trim(substr($old_picture_path,0,1)) != '.'){
                            $old_picture_path = '.' . $old_picture_path;
                        }
                        if(file_exists($old_picture_path)){
                            unlink($old_picture_path);
                        }
                    }
                }

                $data['picture_name'] = $new_picture_name;
            }else{
                $data['picture_name'] = '';
            }
        }
        //END UPDATE/INSERT
        return $data;
    }

    public function printprofile_list($menuid = 0){
        $where['status <>'] = STATUS_DELETE;
        $like = array();

        if(isset($_REQUEST['filter_keycode'])){
            if($_REQUEST['filter_keycode'] != ''){
                $like['key_code'] = $_REQUEST['filter_keycode'];
            }
        }
        /*
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $where['status'] = $_REQUEST['filter_status'];
            }
        }
        */

        $qry_tot = $this->mdl_general->count('print_profile', $where, $like);

        $iTotalRecords = $qry_tot;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'print_profile.key_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'print_profile.key_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'print_profile.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('print_profile', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $hasDelete = false;

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $i . '.',
                $row->key_code,
                $row->company_name,
                nl2br($row->company_address),
                $row->approver_name,
                $row->approver_title,
                //get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('admin/profile/printprofile_manage/0/' . $row->id) . '.tpd"><i class="fa fa-edit"></i></a>'
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    #endregion

    #region Picture

    //enctype="multipart/form-data" must be added
    public function insert_logo_picture($folder_name = './assets/img/profile/'){
        $new_file_name = '';

        $validImage = true;

        ############ Configuration ##############
        $max_image_size = 500; //Maximum image size (height and width)
        $destination_folder = $folder_name; //upload directory ends with / (slash)
        $jpeg_quality = 90; //jpeg quality
        $fileUploaderName = 'picture_image';
        ##########################################

        if(trim(substr($destination_folder,0)) != '.'){
            $destination_folder = '.' . $destination_folder ;
        }

        //echo "[insert_logo_picture] A " . $destination_folder . ' _FILES ' . count($_FILES);

        if(isset($_POST)){
            if(!isset($_FILES[$fileUploaderName]) || !is_uploaded_file($_FILES[$fileUploaderName]['tmp_name'])){
                echo "<br> Image  NULL " ;
                //$this->session->set_flashdata('flash_message','Image file is Missing!');
                //$this->session->set_flashdata('flash_type', 'alert-danger');

                $validImage = false;

                //Exit function
                //die('Image file is Missing!'); // output error when above checks fail.
            }else{
                if($validImage){
                    //get uploaded file info before we proceed
                    $image_name = $_FILES[$fileUploaderName]['name']; //file name
                    $image_size = $_FILES[$fileUploaderName]['size']; //file size
                    $image_temp = $_FILES[$fileUploaderName]['tmp_name']; //file temp

                    $image_size_info    = getimagesize($image_temp); //gets image size info from valid image file

                    //echo "<br> Image  " . $image_name . " | " . $image_size . " | " . $image_temp ;

                    if($image_size_info){
                        $image_width        = $image_size_info[0]; //image width
                        $image_height       = $image_size_info[1]; //image height
                        $image_type         = $image_size_info['mime']; //image type
                    }else{
                        echo "<br> Make sure image file is valid!  " ;

                        //$this->session->set_flashdata('flash_message','Image file is not valid !');
                        //$this->session->set_flashdata('flash_type', 'alert-danger');

                        $validImage = false;

                        //die("Make sure image file is valid!");
                    }
                }

                if($validImage){
                    //switch statement below checks allowed image type
                    //as well as creates new image from given file
                    switch($image_type){
                        case 'image/png':
                            $image_res =  imagecreatefrompng($image_temp); break;
                        case 'image/gif':
                            $image_res =  imagecreatefromgif($image_temp); break;
                        case 'image/jpeg': case 'image/pjpeg':
                            $image_res = imagecreatefromjpeg($image_temp); break;
                        default:
                            $image_res = false;
                    }

                    echo "<br> res " . $image_res ;

                    if($image_res){
                        //Get file extension and name to construct new file name
                        $image_info = pathinfo($image_name);
                        $image_extension = strtolower($image_info["extension"]); //image extension
                        $image_name_only = strtolower($image_info["filename"]);//file name only, no extension

                        //create a random name for new image (Eg: fileName_293749.jpg) ;
                        $new_file_name = $image_name_only . '_' .  rand(0, 99999) . '.' . $image_extension;

                        //folder path to save resized images and thumbnails
                        //$thumb_save_folder  = $destination_folder . PREFIX_THUMB . $new_file_name;
                        $image_save_folder  = $destination_folder . $new_file_name;

                        //echo "<br> file name " . $new_file_name ;
                        //call normal_resize_image() function to proportionally resize image
                        if(normal_resize_image($image_res, $image_save_folder, $image_type, $max_image_size, $image_width, $image_height, $jpeg_quality))
                        {
                            //call crop_image_square() function to create square thumbnails
                        }

                        imagedestroy($image_res); //freeup memory

                        $new_file_name = $image_save_folder;
                        //echo '<br>COMPLETED';
                    }
                }
            }
        }else{
            $validImage = false;
            $new_file_name = '';
        }

        return $new_file_name;
    }


    #endregion
}

/* End of file printprofile.php */
/* Location: ./application/controllers/admin/profile.php */