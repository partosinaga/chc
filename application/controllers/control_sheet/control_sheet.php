<?php
ini_set('display_errors', 1);

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Control_sheet extends CI_Controller {

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
        $this->control_sheet_manage();
    }

    public function control_sheet_manage(){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
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
        $this->load->view('control_sheet/control_list.php', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_po_list($menu_id = 0){
        $this->load->model('control_sheet/mdl_control_sheet');
        $like = array();
        $where = array();

        if(my_sess('user_isadmin') == '1'){
            $where['in_po.status <>'] = STATUS_CANCEL;
        } else {
            $where['in_po.status <>'] = STATUS_CANCEL;
            $where['in_po.status !='] = STATUS_CLOSED;
        }

        if(isset($_REQUEST['filter_po_code'])){
            if($_REQUEST['filter_po_code'] != ''){
                $like['in_po.po_code'] = $_REQUEST['filter_po_code'];
            }
        }
        if(isset($_REQUEST['filter_po_date_from'])){
            if($_REQUEST['filter_po_date_from'] != ''){
                $where['in_po.po_date >='] = dmy_to_ymd($_REQUEST['filter_po_date_from']);
            }
        }
        if(isset($_REQUEST['filter_po_date_to'])){
            if($_REQUEST['filter_po_date_to'] != ''){
                $where['in_po.po_date <='] = dmy_to_ymd($_REQUEST['filter_po_date_to']);
            }
        }
        if(isset($_REQUEST['filter_supplier'])){
            if($_REQUEST['filter_supplier'] != ''){
                $like['in_supplier.supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['in_po.remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_po_approval_date_receipt_from'])){
            if($_REQUEST['filter_po_approval_date_receipt_from'] != ''){
                $where['in_po.approval_date_receipt >='] = dmy_to_ymd($_REQUEST['filter_po_approval_date_receipt_from']);
            }
        }
        if(isset($_REQUEST['filter_po_approval_date_receipt_to'])){
            if($_REQUEST['filter_po_approval_date_receipt_to'] != ''){
                $where['in_po.approval_date_receipt <='] = dmy_to_ymd($_REQUEST['filter_po_approval_date_receipt_to']);
            }
        }
        if(isset($_REQUEST['filter_purchasing_date_receipt_from'])){
            if($_REQUEST['filter_purchasing_date_receipt_from'] != ''){
                $where['in_po.purchasing_date_receipt >='] = dmy_to_ymd($_REQUEST['filter_purchasing_date_receipt_from']);
            }
        }
        if(isset($_REQUEST['filter_purchasing_date_receipt_to'])){
            if($_REQUEST['filter_purchasing_date_receipt_to'] != ''){
                $where['in_po.purchasing_date_receipt <='] = dmy_to_ymd($_REQUEST['filter_purchasing_date_receipt_to']);
            }
        }

        $iTotalRecords = $this->mdl_control_sheet->get_po(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_po.po_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_po.po_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_po.po_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'in_supplier.supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'in_po.approval_date_receipt ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'in_po.purchasing_date_receipt ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 7){
                $order = 'in_po.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_control_sheet->get_po(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $dept = '';
        $qry_dept = $this->db->get_where('ms_department', array('department_id' => my_sess('department_id')));
        if($qry_dept->num_rows() > 0){
            $row_dept = $qry_dept->row();
            $dept = trim($row_dept->department_name);
        }


        $i = $iDisplayStart + 1;

        foreach($qry->result() as $row){

            $app_date_receipt = ymd_to_dmy($row->approval_date_receipt);
            $purc_date_receipt = ymd_to_dmy($row->purchasing_date_receipt);
            $btn_save = '';

            $isedit = false;
            $isfin = false;
            if($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE || $row->status == STATUS_APPROVE) {
                if ($dept == Purchasing::DEPT_FIN) {
                    $app_date_receipt = '<div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control input-sm" name="approval_date_receipt" value="' . $app_date_receipt . '" readonly>
                                        <span class="input-group-btn">
                                        <button class="btn btn-sm default set_fin" type="button" style="padding-top:5px;"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>';
                    $isfin = true;
                    $isedit = true;
                } else if ($dept == Purchasing::DEPT_PRC) {
                    $purc_date_receipt = '<div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control input-sm" name="purchasing_date_receipt" value="' . $purc_date_receipt . '" readonly>
                                        <span class="input-group-btn">
                                        <button class="btn btn-sm default set_purc" type="button" style="padding-top:5px;"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>';
                    $isedit = true;
                }

                if ($isedit) {
                    $btn_save = '<button type="button" class="btn btn-sm green-haze btn-save" data-id="' . $row->po_id . '" data-save="' . ($isfin ? 'fin' : 'prc') . '"> Save </button>';
                }
            }

            $records["data"][] = array(
                $i . '.',
                $row->po_code,
                ymd_to_dmy($row->po_date),
                $row->supplier_name,
                $row->remarks,
                $app_date_receipt,
                $purc_date_receipt,
                get_status_name($row->status),
                $btn_save
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_save(){
        $result = array();

        $this->db->trans_begin();

        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = '';

        $po_id = $_POST['po_id'];
        $data_save = $_POST['data_save'];
        $val = dmy_to_ymd($_POST['val']);

        if ($po_id > 0 && $data_save != '' && $val != '') {
            if($data_save == 'fin'){
                $data['approval_date_receipt'] = $val;
                $this->mdl_general->update('in_po', array('po_id' => $po_id), $data);
            } else if($data_save == 'prc'){
                $data['purchasing_date_receipt'] = $val;
                $this->mdl_general->update('in_po', array('po_id' => $po_id), $data);
            } else {
                $result['valid'] = '0';
                $result['message'] = "Data save not found.";
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            $result['valid'] = '0';
            $result['message'] = "Something error. Please try again later.";
        }
        else {
            $this->db->trans_commit();

            $result['message'] = "Successfully update data.";
        }

        echo json_encode($result);
    }

}

/* End of file control_sheet.php */
/* Location: ./application/controllers/control_sheet/control_sheet.php */