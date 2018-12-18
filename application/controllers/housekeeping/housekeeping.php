<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class housekeeping extends CI_Controller {

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
        $this->home();
    }

    public function home(){
        $this->load->model('finance/mdl_finance');
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

        $data = array();
        $this->load->view('layout/header', $data_header);
        $this->load->view('housekeeping/home', $data);
        $this->load->view('layout/footer');

    }

    public function xmodal_change_status($unit_id = 0) {
        $data['unit_id'] = $unit_id;
        $qry = $this->db->get_where('ms_unit', array('unit_id' => $unit_id));
        $data['row'] = $qry->row();
        $this->load->view('housekeeping/ajax_modal_change_status', $data);
    }

    public function xchange_hsk_status() {
        $result = array();
        //Used to display notification
        $result['valid'] = '0';
        $result['message'] = '';

        if(isset($_POST)){
            $this->load->model('finance/mdl_finance');

            $unit_id = $_POST['unit_id'];

            $data['unit_id'] = $unit_id;
            $data['hsk_status'] = $_POST['hsk_status'];
            $data['remark'] = $_POST['remark'];
            if($unit_id > 0){
                $this->mdl_general->update('ms_unit', array('unit_id' => $unit_id), array('hsk_status' => $data['hsk_status']));

                //Insert log
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = date('Y-m-d H:i:s');

                $this->db->insert('log_hsk', $data);
                $insertedID = $this->db->insert_id();

                if($insertedID > 0){
                    $unit = $this->mdl_finance->getJoin('ms_unit.*, ms_unit_type.unittype_bedroom', 'ms_unit', array('ms_unit_type' => 'ms_unit.unittype_id = ms_unit_type.unittype_id'), array('ms_unit.unit_id' => $unit_id), array(), 'unit_code ASC');
                    if($unit->num_rows() > 0){
                        $unit = $unit->row();

                        $unit_button = '<button type="button" class="btn ' . HSK_STATUS::hsk_class($data['hsk_status']) . ' btn-change-status" data-id="' . $unit_id . '" status-next="' . HSK_STATUS::next_status($data['hsk_status']) . '" ' . (HSK_STATUS::next_status($data['hsk_status']) == '' ? ' disabled' : '') . '><span class="small">' . $unit->unit_code . '</span><br/><span class="large">' . $data['hsk_status'] . '</span><br/><span class="small">' . $unit->unittype_bedroom . '</span></button>';
                        $result['button'] = $unit_button;
                    }

                    $result['valid'] = '1';
                    $result['message'] = 'Status successfully changed.';
                }else{
                    $result['valid'] = '0';
                    $result['message'] = 'Status can not be changed !';
                }
            }else{
                $result['valid'] = '0';
                $result['message'] = 'Status can not be changed !';
            }
        }

        echo json_encode($result);
    }

    public function link_sfr() {
        $result = array(
            'link'  => '',
            'debug' => ''
        );

        $unit_id = 0;
        if (isset($_POST['unit_id'])) {
            $unit_id = intval($_POST['unit_id']);
        }

        if ($unit_id > 0) {
            //SRF_TYPE::OUT_OF_ORDER
            $qry = $this->db->get_where('cs_srf_header', array('unit_id' => $unit_id, 'srf_type' => SRF_TYPE::OUT_OF_ORDER, 'status' => STATUS_APPROVE));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                $result['link'] = base_url('housekeeping/srf/srf_form/' . $row->srf_id . '.tpd');
            } else {
                $result['debug'] = 'No srf found.';
            }
        } else {
            $result['debug'] = 'No post data.';
        }

        echo json_encode($result);
    }

}

/* End of file home.php */
/* Location: ./application/controllers/home/home.php */