<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PO extends CI_Controller {

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
        $this->po_manage();
    }

    #region po

    public function po_manage($type = 0, $po_id = 0){
        if ($type == 0) {
            $data_header = $this->data_header;

            $data = array();
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');


            $data['qry_department'] = $this->mdl_general->get('ms_department', array('status <> ' => STATUS_DELETE), array(), 'department_name');
            $data['qry_project'] = $this->mdl_general->get('ms_project', array('status <> ' => STATUS_DELETE), array(), 'project_initial');

            $this->load->view('layout/header', $data_header);
            $this->load->view('purchasing/po/po_list', $data);
            $this->load->view('layout/footer');
        } else {
            $this->po_form($po_id);
        }
    }

    public function po_list($menu_id = 0){
        $this->load->model('purchasing/mdl_purchasing');
        $where['in_po.status <>'] = STATUS_DELETE;
        $like = array();

        if(isset($_REQUEST['filter_pono'])){
            if($_REQUEST['filter_pono'] != ''){
                $like['in_po.po_code'] = $_REQUEST['filter_pono'];
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
        if(isset($_REQUEST['filter_po_delivery_date_from'])){
            if($_REQUEST['filter_po_delivery_date_from'] != ''){
                $where['in_po.po_delivery_date >='] = dmy_to_ymd($_REQUEST['filter_po_delivery_date_from']);
            }
        }
        if(isset($_REQUEST['filter_po_delivery_date_to'])){
            if($_REQUEST['filter_po_delivery_date_to'] != ''){
                $where['in_po.po_delivery_date <='] = dmy_to_ymd($_REQUEST['filter_po_delivery_date_to']);
            }
        }
        if(isset($_REQUEST['filter_supplier_name'])){
            if($_REQUEST['filter_supplier_name'] != ''){
                $like['in_supplier.supplier_name'] = $_REQUEST['filter_supplier_name'];
            }
        }
        if(isset($_REQUEST['filter_prno'])){
            if($_REQUEST['filter_prno'] != ''){
                $like['in_pr.pr_code'] = $_REQUEST['filter_prno'];
            }
        }

        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $where['in_po.status'] = $_REQUEST['filter_status'];
            }
        }

        $iTotalRecords = $this->mdl_purchasing->get_po_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_po.po_date desc, in_po.po_id desc ';

        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_po.po_code ' . $_REQUEST['order'][0]['dir'];
            }
        }
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_po.po_date ' . $_REQUEST['order'][0]['dir'];
            }
        }
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'in_po.po_delivery_date ' . $_REQUEST['order'][0]['dir'];
            }
        }
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'in_supplier.supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
        }
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'in_pr.pr_code ' . $_REQUEST['order'][0]['dir'];
            }
        }
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'in_po.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_purchasing->get_po_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('purchasing/po/po_manage/1/' . $row->po_id) . '.tpd"> View </a> </li>';

            if($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE){
                if(check_session_action($menu_id, STATUS_APPROVE)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_APPROVE . '" data-id="' . $row->po_id . '" data-code="' . $row->po_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CANCEL . '" data-id="' . $row->po_id . '" data-code="' . $row->po_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-print" data-id="' . $row->po_id . '" >Print PO</a> </li>';
                }
            } else if($row->status == STATUS_APPROVE){
                if(check_session_action($menu_id, STATUS_DISAPPROVE)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_DISAPPROVE . '" data-id="' . $row->po_id . '" data-code="' . $row->po_code . '"  data-action-code="' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CLOSED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CLOSED . '" data-id="' . $row->po_id . '" data-code="' . $row->po_code . '"  data-action-code="Complete">Complete</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-print" data-id="' . $row->po_id . '" >Print PO</a> </li>';
                }
            } else if($row->status == STATUS_CLOSED){
                if(check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-print" data-id="' . $row->po_id . '" >Print PO</a> </li>';
                }
            }

            $status = get_status_name($row->status);

            $records["data"][] = array(
                $i . '.',
                $row->po_code,
                ymd_to_dmy($row->po_date),
                ymd_to_dmy($row->po_delivery_date),
                $row->supplier_name,
                $row->pr_code ,
                $status,
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                        Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
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

    public function po_form($id = 0){
        $this->load->model('purchasing/mdl_purchasing');

        $data_header = $this->data_header;
        $data_footer = $this->data_footer;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        $data['po_id'] = $id;
        $supplier = $this->mdl_general->get('in_supplier', array('status <> ' => STATUS_DELETE), array(), 'supplier_name asc');
        $data['supplier_list'] = $supplier->result_array();

        $currency = $this->mdl_general->get('currencytype',   array());
        $data['currency_list'] = $currency->result_array();

        $uom = $this->mdl_general->get('in_ms_uom', array('status <> ' => STATUS_DELETE), array());
        $data['uom_list'] = $uom->result_array();

        if($id > 0){
            $qry = $this->mdl_purchasing->get_po_list(false, array('po_id' => $id));
            $data['row'] = $qry->row();

            $data['qry_det'] = $this->mdl_purchasing->get_po_detail(false, array('in_po_detail.po_id' => $id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('purchasing/po/po_form', $data);
        $this->load->view('layout/footer', $data_footer);
    }

    public function ajax_modal_pr(){
        $this->load->view('purchasing/po/ajax_modal_pr');
    }

    public function ajax_modal_pr_list($pr_id = 0){
        $this->load->model('purchasing/mdl_purchasing');

        $like = array();
        $where = array();

        $where['in_pr.status'] = STATUS_APPROVE;

        if(isset($_REQUEST['filter_pr_code'])){
            if($_REQUEST['filter_pr_code'] != ''){
                $like['in_pr.pr_code'] = $_REQUEST['filter_pr_code'];
            }
        }
        if(isset($_REQUEST['filter_pr_date_from'])){
            if($_REQUEST['filter_pr_date_from'] != ''){
                $where['in_pr.date_prepare >='] = dmy_to_ymd($_REQUEST['filter_pr_date_from']);
            }
        }
        if(isset($_REQUEST['filter_pr_date_to'])){
            if($_REQUEST['filter_pr_date_to'] != ''){
                $where['in_pr.date_prepare <='] = dmy_to_ymd($_REQUEST['filter_pr_date_to']);
            }
        }
        if(isset($_REQUEST['filter_supplier'])){
            if($_REQUEST['filter_supplier'] != ''){
                $like['in_supplier.supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }
        if(isset($_REQUEST['filter_dept'])){
            if($_REQUEST['filter_dept'] != ''){
                $like['ms_department.department_name'] = $_REQUEST['filter_dept'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['in_pr.remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        $iTotalRecords = $this->mdl_purchasing->get_pr_by_project(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_pr.pr_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_pr.pr_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_pr.date_prepare ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_purchasing->get_pr_by_project(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-pr" data-id="' . $row->pr_id . '" data-code="' . $row->pr_code . '" data-dept="' . $row->department_id . '"><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';
            if($pr_id == $row->pr_id){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }

            $records["data"][] = array(
                $i . '.',
                $row->pr_code,
                ymd_to_dmy($row->date_prepare),
                $row->department_name,
                $row->remarks,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_pr_detail($type = 0){
        $data['type'] = $type;
        $this->load->view('purchasing/po/ajax_modal_pr_detail', $data);
    }

    public function ajax_modal_pr_detail_list($pr_id = 0, $pr_detail_id_exist = '-', $type = 0){
        $this->load->model('purchasing/mdl_purchasing');

        $like = array();
        $where = array();

        $where['in_pr_item.pr_id'] = $pr_id;

        $iTotalRecords = $this->mdl_purchasing->get_pr_detail(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'pr_item_id asc';

        $qry = $this->mdl_purchasing->get_pr_detail(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $pr_detail_id_exist = trim($pr_detail_id_exist);
        $isexist = false;
        if($pr_detail_id_exist != '-'){
            $isexist = true;
            $arr_id = explode('_', $pr_detail_id_exist);
        }

        $records["debug"] = $pr_detail_id_exist;

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';

            if($type == 0) {
                if ($row->qty_remain <= 0) {
                    $attr = 'selected="selected" disabled="disabled"';
                } else {
                    if ($isexist) {
                        foreach ($arr_id as $key => $val) {
                            if ($val == $row->pr_item_id) {
                                $attr = 'selected="selected" disabled="disabled"';
                            }
                        }
                    }
                }
            } else {
                $attr = 'selected="selected" disabled="disabled"';
            }

            $attr_date = '';
            $attr_date .= ' data-other-1="' . $row->item_id . '" ';
            $attr_date .= ' data-other-2="' . $row->item_code . '" ';
            $attr_date .= ' data-other-3="' . $row->item_desc . '" ';
            $attr_date .= ' data-other-4="' . $row->qty_remain . '" ';
            $attr_date .= ' data-other-5="' . $row->uom_id . '" ';
            $attr_date .= ' data-other-6="' . $row->uom_code . '" ';
            $attr_date .= ' data-other-7="' . $row->item_type . '" ';
            $attr_date .= ' data-other-8="' . $row->account_coa_id . '" ';
            $attr_date .= ' data-other-9="' . $row->coa_code . '" ';
            $attr_date .= ' data-other-10="' . $row->item_factor . '" ';

            $records["data"][] = array(
                '<input type="checkbox" name="checkbox_pr_detail" value="' . $row->pr_item_id . '" ' . $attr_date . ' ' . $attr . '" />',
                $row->item_desc,
                $row->uom_code,
                $row->supplier_name,
                '<span class="mask_number">' . $row->item_qty . '</span>',
                '<span class="mask_number">' . $row->qty_remain . '</span>',
                (trim($row->item_url) != '' ? '<a href="' . $row->item_url . '" target="_blank">' . $row->item_url . '</a>' : '')
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_item(){
        $this->load->view('purchasing/po/ajax_modal_item');
    }

    public function ajax_modal_item_list($item_id = 0, $index = 0, $pr_item_id = 0, $item_type = 0){

        $like = array();
        $where = array();
        $where_string = '';

        if(isset($_REQUEST['filter_item_code'])){
            if($_REQUEST['filter_item_code'] != ''){
                $like['item_code'] = $_REQUEST['filter_item_code'];
            }
        }
        if(isset($_REQUEST['filter_item_desc'])){
            if($_REQUEST['filter_item_desc'] != ''){
                $like['item_desc'] = $_REQUEST['filter_item_desc'];
            }
        }
        if(isset($_REQUEST['filter_group'])){
            if($_REQUEST['filter_group'] != ''){
                $where['group_id'] = $_REQUEST['filter_group'];
            }
        }
        if(isset($_REQUEST['filter_uom'])){
            if($_REQUEST['filter_uom'] != ''){
                $where_string .= " (uom_in_code LIKE '%" . $_REQUEST['filter_uom'] . "%' OR qty_distribution LIKE '%" . $_REQUEST['filter_uom'] . "%' OR uom_out_code LIKE '%" . $_REQUEST['filter_uom'] . "%') AND ";
            }
        }

        $where['status'] = STATUS_NEW;
        $where_string .= " (item_type = " . $item_type . " OR item_code = '" . Purchasing::DIRECT_PURCHASE . "') ";

        $iTotalRecords = $this->mdl_general->count('view_in_get_item_list', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'item_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'group_code ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_in_get_item_list', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';
            $attr .= ' data-id="' . $row->item_id . '" ';
            $attr .= ' data-code="' . $row->item_code . '" ';
            $attr .= ' data-index="' . $index . '" ';
            $attr .= ' data-supplies-id="' . $row->account_coa_id . '" ';
            $attr .= ' data-supplies-code="' . $row->supplies_coa_code . '" ';


            if ($row->item_code == Purchasing::DIRECT_PURCHASE) {
                $qry_pr_det = $this->db->get_where('in_pr_item', array('pr_item_id' => $pr_item_id));
                $row_pr_det = $qry_pr_det->row();

                $attr .= ' data-desc="' . $row_pr_det->item_desc . '" ';
                $attr .= ' data-uom-id="' . $row_pr_det->uom_id . '" ';
                $attr .= ' data-uom-factor="1" ';
                if ($row_pr_det->uom_id > 0) {
                    $qry_uom = $this->db->get_where('in_ms_uom', array('uom_id' => $row_pr_det->uom_id));
                    $row_uom = $qry_uom->row();
                    $attr .= ' data-uom-code="' . $row_uom->uom_code . ' [1]" ';
                } else {
                    $attr .= ' data-uom-code="" ';
                }

            } else {
                $attr .= ' data-desc="' . $row->item_desc . '" ';
                $attr .= ' data-uom-id="' . $row->uom_id . '" ';
                $attr .= ' data-uom-factor="' . $row->qty_distribution . '" ';
                $attr .= ' data-uom-code="' . $row->uom_in_code . ' [' . $row->qty_distribution . ']" ';
            }



            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-item" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';
            if($item_id == $row->item_id){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }

            $records["data"][] = array(
                $row->item_code,
                $row->item_desc,
                $row->group_code,
                $row->uom_in_code . ' ( ' . $row->qty_distribution . ' ' . $row->uom_out_code . ' )',
                $row->on_hand_qty,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_coa(){
        $this->load->view('purchasing/po/ajax_modal_coa');
    }

    public function ajax_modal_coa_list($coa_id = 0, $num_index = 0, $coa_id_exist = '-', $arr_coa = ''){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        $where['gl_coa.status <>'] = STATUS_DELETE;
        $where['gl_coa.is_display >'] = 0;

        $where_string = '';
        if ($arr_coa != '') {
            $arr_coa_e = explode('_', $arr_coa);
            $where_string .= ' (';
            $i = 0;
            foreach ($arr_coa_e as $val) {
                if ($i > 0) {
                    $where_string .= ' OR ';
                }
                $where_string .= ' gl_coa.coa_code LIKE "' . $val . '%" ';
                $i++;
            }
            $where_string .= ') ';
        }

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
                $where['gl_class.class_id'] = $_REQUEST['filter_classid'];
            }
        }
        if(isset($_REQUEST['filter_classtype'])){
            if($_REQUEST['filter_classtype'] != ''){
                $where['gl_class.class_type'] = $_REQUEST['filter_classtype'];
            }
        }

        $iTotalRecords = $this->mdl_finance->countCOA($where, $like, $where_string);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

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
                $order = 'gl_coa.is_debit ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'gl_coa.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getCOA($where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $coa_id_exist = trim($coa_id_exist);
        $isexist = false;
        if($coa_id_exist != '-'){
            $isexist = true;
            $arr_id = explode('_', $coa_id_exist);
        }

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $attr = '';
            $attr .= ' data-id="' . $row->coa_id . '" ';
            $attr .= ' data-code="' . $row->coa_code . '" ';
            $attr .= ' data-desc="' . $row->coa_desc . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            $text = "Select";
            if ($isexist) {
                foreach ($arr_id as $key => $val) {
                    if ($val == $row->coa_id) {
                        $attr .= 'selected="selected" disabled="disabled"';
                        $text = "Selected";
                    }
                }
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-coa" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';
            if($coa_id == $row->coa_id){
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }

            $records["data"][] = array(
                $row->coa_code,
                $row->coa_desc,
                $row->class_code,
                GLClassType::class_type_name($row->class_type),
                ($row->is_debit > 0) ? 'D' : 'C',
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_uom(){
        $this->load->view('purchasing/po/ajax_modal_uom');
    }

    public function ajax_modal_uom_list($num_index = 0, $item_id = 0, $uom_id = 0, $uom_factor = 0){
        $this->load->model('finance/mdl_finance');

        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $qry = $this->db->get_where('view_in_get_item_list', array('item_id' => $item_id));
        $row = $qry->row();

        $iTotalRecords = 0;
        if ($row->item_code == Purchasing::DIRECT_PURCHASE) {
            $qry_uom = $this->db->get_where('in_ms_uom', array('status' => STATUS_NEW));
            foreach ($qry_uom->result() as $row_uom) {
                $attr = '';
                $attr .= ' data-id="' . $row_uom->uom_id . '" ';
                $attr .= ' data-code="' . $row_uom->uom_code . ' [1]" ';
                $attr .= ' data-factor="1" ';
                $attr .= ' data-index="' . $num_index . '" ';

                $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-uom" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';
                if ($uom_id == $row_uom->uom_id && $uom_factor == '1') {
                    $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
                }

                $records["data"][] = array(
                    $row_uom->uom_code,
                    '1',
                    $btn
                );
                $iTotalRecords++;
            }
        } else {
            $attr = '';
            $attr .= ' data-id="' . $row->uom_id . '" ';
            $attr .= ' data-code="' . $row->uom_in_code . ' [' . $row->qty_distribution . ']" ';
            $attr .= ' data-factor="' . $row->qty_distribution . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-uom" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';
            if ($uom_id == $row->uom_id && $uom_factor == $row->qty_distribution) {
                $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
            }

            $records["data"][] = array(
                $row->uom_in_code,
                $row->qty_distribution,
                $btn
            );
            $iTotalRecords++;

            $qry_uom = $this->mdl_general->getJoin('a.uom_id, a.factor, b.uom_code', 'in_ms_item_conversion_uom a', array('in_ms_uom b' => 'a.uom_id = b.uom_id'), array('a.status' => STATUS_NEW, 'a.item_id' => $item_id));
            foreach ($qry_uom->result() as $row_uom) {
                $attr = '';
                $attr .= ' data-id="' . $row_uom->uom_id . '" ';
                $attr .= ' data-code="' . $row_uom->uom_code . ' [' . $row_uom->factor . ']" ';
                $attr .= ' data-factor="' . $row_uom->factor . '" ';
                $attr .= ' data-index="' . $num_index . '" ';

                $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-uom" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';
                if ($uom_id == $row_uom->uom_id && $uom_factor == $row_uom->factor) {
                    $btn = '<button class="btn btn-xs" disabled><i class="fa fa-check"></i>&nbsp;&nbsp;Selected</button>';
                }

                $records["data"][] = array(
                    $row_uom->uom_code,
                    $row_uom->factor,
                    $btn
                );
                $iTotalRecords++;
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_tax(){
        $this->load->view('purchasing/po/ajax_tax_list');
    }

    public function ajax_modal_tax_list($index = 0){

        $like = array();
        $where = array();

        $where['status <>'] = STATUS_DELETE;
        $iTotalRecords = $this->mdl_general->count('tax_type', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'taxtype_id asc';
        $qry = $this->mdl_general->get('tax_type', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn = '<button class="btn green-meadow btn-xs btn-select-tax" data-id="' . $row->taxtype_id . '" data-index="' . $index . '" data-percent="' . $row->taxtype_percent . '" data-wht="' . $row->taxtype_wht . '" data-code="' . $row->taxtype_code . '"><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';

            $records["data"][] = array(
                $i . '.',
                $row->taxtype_code,
                $row->taxtype_desc,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_po_submit(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = array();

        $data = array();

        if(isset($_POST)){
            $has_error = false;
            $this->db->trans_begin();
            $po_id = $_POST['po_id'];

            $data['po_code'] = $_POST['po_code'];
            $data['pr_id'] = $_POST['pr_id'];
            $data['supplier_id'] = $_POST['supplier_id'];
            $data['po_date'] = dmy_to_ymd(trim($_POST['po_date']));
            $data['po_delivery_date'] = dmy_to_ymd(trim($_POST['po_delivery_date']));
            $data['currencytype_id'] = $_POST['currencytype_id'];
            $data['curr_rate'] = $_POST['curr_rate'];
            $data['remarks'] = trim($_POST['remarks']);
            $data['term_payment'] = trim($_POST['term_payment']);

            $qry_pr = $this->db->get_where('in_pr', array('pr_id' => $data['pr_id']));
            $row_pr = $qry_pr->row();
            $data['wo_id'] = $row_pr->wo_id;
            $data['department_id'] = $row_pr->department_id;

            if($po_id > 0){
                $qry = $this->db->get_where('in_po', array('po_id' => $po_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['po_date']);
                $arr_date_old = explode('-', $row->po_date);

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    $data['po_code'] = $this->mdl_general->generate_code(Feature::FEATURE_PO, $data['po_date']);

                    if($data['po_code'] == ''){
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }

                if($has_error == false){
                    $data['user_modified'] = my_sess('user_id');
                    $data['date_modified'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('in_po', array('po_id' => $po_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully update PO.');
                }
            }
            else {
                $data['po_code'] = $this->mdl_general->generate_code(Feature::FEATURE_PO, $data['po_date']);

                if($data['po_code'] != ''){
                    $data['user_created'] = my_sess('user_id');
                    $data['date_created'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_NEW;

                    $this->db->insert('in_po', $data);
                    $po_id = $this->db->insert_id();

                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $po_id;
                    $data_log['feature_id'] = Feature::FEATURE_PO;
                    $data_log['log_subject'] = 'Create PO (' . $data['po_code'] . ')';
                    $data_log['action_type'] = STATUS_NEW;
                    $this->db->insert('app_log', $data_log);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully add PO.');
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'Failed generating code.';
                }
            }

            if($has_error == false) {
                if (isset($_POST['po_detail_id'])) {
                    $i = 0;
                    foreach ($_POST['po_detail_id'] as $key => $val) {
                        $data_detail = array();

                        $status = $_POST['status'][$key];

                        $data_detail['po_id'] = $po_id;
                        $data_detail['pr_item_id'] = $_POST['pr_item_id'][$key];
                        $data_detail['item_id'] = $_POST['item_id'][$key];
                        $data_detail['item_type'] = $_POST['item_type'][$key];
                        $data_detail['account_coa_id'] = $_POST['account_coa_id'][$key];
                        $data_detail['item_qty'] = $_POST['item_qty'][$key];
                        $data_detail['item_qty_remaining'] = $data_detail['item_qty'];
                        $data_detail['uom_id'] = $_POST['uom_id'][$key];
                        $data_detail['uom_factor'] = $_POST['uom_factor'][$key];
                        $data_detail['item_price'] = $_POST['item_price'][$key];
                        $data_detail['item_disc'] = $_POST['item_disc'][$key];
                        $data_detail['tax_id'] = $_POST['tax_id'][$key];
                        $data_detail['tax_amount_vat'] = $_POST['tax_amount_vat'][$key];
                        $data_detail['tax_amount_wht'] = $_POST['tax_amount_wht'][$key];
                        $data_detail['item_tot_amount'] = $_POST['amount'][$key];

                        $data_detail['status'] = STATUS_NEW;

                        if ($_POST['po_detail_id'][$key] > 0) {
                            if($status == STATUS_DELETE) {
                                $qry_det = $this->db->get_where('in_po_detail', array('po_detail_id' => $_POST['po_detail_id'][$key]));
                                $row_det = $qry_det->row();

                                $qry_det2 = $this->db->get_where('in_pr_item', array('pr_item_id' => $data_detail['pr_item_id']));
                                $row_det2 = $qry_det2->row();

                                $this->mdl_general->update('in_pr_item', array('pr_item_id' => $data_detail['pr_item_id']), array('qty_remain' => ($row_det2->qty_remain + $row_det->item_qty)));

                                if ($row_det2->item_id > 0) {

                                } else {
                                    $this->mdl_general->update('in_ms_item', array('item_id' => $row_det->item_id), array('status_order' => 0));
                                }

                                $this->db->delete('in_po_detail', array('po_detail_id' => $_POST['po_detail_id'][$key]));
                            } else {
                                $qry_det = $this->db->get_where('in_po_detail', array('po_detail_id' => $_POST['po_detail_id'][$key]));
                                $row_det = $qry_det->row();

                                $qry_det2 = $this->db->get_where('in_pr_item', array('pr_item_id' => $data_detail['pr_item_id']));
                                $row_det2 = $qry_det2->row();

                                $this->mdl_general->update('in_pr_item', array('pr_item_id' => $data_detail['pr_item_id']), array('qty_remain' => ($row_det2->qty_remain - $data_detail['item_qty'] + $row_det->item_qty)));

                                if ($row_det->item_id != $data_detail['item_id']) {
                                    $this->mdl_general->update('in_ms_item', array('item_id' => $row_det->item_id), array('status_order' => 0));
                                    $this->mdl_general->update('in_ms_item', array('item_id' => $data_detail['item_id']), array('status_order' => 1));
                                }

                                $this->mdl_general->update('in_po_detail', array('po_detail_id' => $_POST['po_detail_id'][$key]), $data_detail);
                            }
                        } else {
                            if($status == STATUS_NEW) {
                                $qry_det2 = $this->db->get_where('in_pr_item', array('pr_item_id' => $data_detail['pr_item_id']));
                                $row_det2 = $qry_det2->row();

                                $this->mdl_general->update('in_ms_item', array('item_id' => $data_detail['item_id']), array('status_order' => 1));

                                $this->mdl_general->update('in_pr_item', array('pr_item_id' => $data_detail['pr_item_id']), array('qty_remain' => ($row_det2->qty_remain - $data_detail['item_qty'])));

                                $this->db->insert('in_po_detail', $data_detail);
                            }
                        }
                        $i++;
                    }
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'No Detail PO.';
                }
            }

            if($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
                else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('purchasing/po/po_manage.tpd');
                    }
                    else{
                        $result['link'] = base_url('purchasing/po/po_manage/1/' . $po_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function ajax_po_action(){
        $result = array();

        $this->db->trans_begin();

        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = '';

        $po_id = $_POST['po_id'];
        $data['status'] = $_POST['action'];

        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $po_id;
        $data_log['feature_id'] = Feature::FEATURE_PO;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($po_id > 0) {
            $qry = $this->db->get_where('in_po', array('po_id' => $po_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_APPROVE) {
                    if ($row->status == STATUS_APPROVE) {
                        $result['valid'] = '0';
                        $result['message'] = 'PO already approved.';
                    } else {
                        //APPROVE PO
                        $check_coa = $this->check_coa_po($po_id);

                        if ($check_coa['error'] == '0') {
                            $valid = $this->approve_po($po_id);
                            $result['debug'] = $valid;

                            if ($valid['error'] == '0') {
                                $data['user_approved'] = my_sess('user_id');
                                $data['date_approved'] = date('Y-m-d H:i:s');

                                $this->mdl_general->update('in_po', array('po_id' => $po_id), $data);

                                $data_log['log_subject'] = 'Approve PO (' . $row->po_code . ')';
                                $data_log['action_type'] = STATUS_APPROVE;
                                $this->db->insert('app_log', $data_log);

                                $result['message'] = 'Successfully approve PO.';
                            } else {
                                $result['valid'] = '0';
                                $result['message'] = $valid['message'];
                            }
                        } else {
                            $result['valid'] = '0';
                            $result['message'] = $check_coa['message'];
                        }
                    }
                } else if ($data['status'] == STATUS_DISAPPROVE) {
                    if ($row->status == STATUS_DISAPPROVE) {
                        $result['valid'] = '0';
                        $result['message'] = 'PO already disapproved.';
                    } else {
                        //DISAPPROVE PO
                        $valid = $this->disapprove_po($po_id);
                        $result['debug'] = $valid;

                        if ($valid['error'] == '0') {
                            $this->mdl_general->update('in_po', array('po_id' => $po_id), $data);

                            $data_log['log_subject'] = 'Dissapprove PO (' . $row->po_code . ')';
                            $data_log['action_type'] = STATUS_DISAPPROVE;

                            $this->db->insert('app_log', $data_log);

                            $result['message'] = 'Successfully disapprove PO.';
                        } else {
                            $result['valid'] = '0';
                            $result['message'] = $valid['message'];
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'PO already canceled.';
                    } else {
                        //CANCEL PO
                        $valid = $this->cancel_po($po_id);
                        $result['debug'] = $valid;

                        if ($valid['error'] == '0') {
                            $data['cancel_note'] = $_POST['reason'];
                            $this->mdl_general->update('in_po', array('po_id' => $po_id), $data);

                            $data_log['log_subject'] = 'Cancel PO (' . $row->po_code . ')';
                            $data_log['action_type'] = STATUS_CANCEL;
                            $this->db->insert('app_log', $data_log);

                            $result['message'] = 'Successfully cancel PO.';
                        } else {
                            $result['valid'] = '0';
                            $result['message'] = $valid['message'];
                        }
                    }
                } else if ($data['status'] == STATUS_CLOSED) {
                    if ($row->status == STATUS_CLOSED) {
                        $result['valid'] = '0';
                        $result['message'] = 'PO already complete.';
                    } else {
                        $data['cancel_note'] = $_POST['reason'];
                        $this->mdl_general->update('in_po', array('po_id' => $po_id), $data);

                        $qry_det = $this->db->get_where('in_po_detail', array('po_id' => $po_id));
                        foreach ($qry_det->result() as $row_det) {
                            if ($row_det->item_qty == $row_det->item_qty_remaining) {
                                $qry_det2 = $this->db->get_where('in_pr_item', array('pr_item_id' => $row_det->pr_item_id));
                                $row_det2 = $qry_det2->row();

                                //Change status order
                                if ($row_det2->item_id > 0) {

                                } else {
                                    $this->mdl_general->update('in_ms_item', array('item_id' => $row_det->item_id), array('status_order' => 0));
                                }
                            }
                        }

                        $data_log['log_subject'] = 'Complete PO (' . $row->po_code . ')';
                        $data_log['action_type'] = STATUS_CLOSED;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully complete PO.';
                    }
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            $result['valid'] = '0';
            $result['message'] = "Something error. Please try again later.";
        }
        else {
            $this->db->trans_commit();

            if($is_redirect){
                $this->session->set_flashdata('flash_message_class', ($result['valid'] == '1' ? 'success' : 'danger'));
                $this->session->set_flashdata('flash_message', $result['message']);
            }
        }

        echo json_encode($result);
    }

    public function ajax_check_coa() {
        $po_id = $_POST['po_id'];

        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';

        $check = $this->check_coa_po($po_id);

        if ($check['error'] == '1') {
            $result['valid'] = '0';
            $result['message'] = $check['message'];
        }

        echo json_encode($result);
    }

    private function check_coa_po($po_id = 0) {
        $result = array();
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if ($po_id > 0) {
            $this->load->model('purchasing/mdl_purchasing');

            $qry = $this->mdl_purchasing->get_po_detail(false, array('in_po_detail.po_id' => $po_id, 'in_po_detail.status' => STATUS_NEW));
            foreach ($qry->result() as $row) {
                if ($row->account_coa_id > 0) {}
                else {
                    $result['error'] = '1';
                    $result['message'] = 'Please input COA Code.';
                }
            }
        } else {
            $result['error'] = '1';
            $result['message'] = 'PO ID is empty.';
        }

        return $result;
    }

    private function approve_po($po_id = 0){
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($po_id > 0) {
            $qry = $this->db->get_where('in_po', array('po_id' => $po_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                $count_pr_item = $this->mdl_general->count('in_pr_item', array('pr_id' => $row->pr_id, 'qty_remain >' => 0));
                if($count_pr_item == 0){
                    $qry_pr = $this->db->get_where('in_pr', array('pr_id' => $row->pr_id));
                    $row_pr = $qry_pr->row();

                    if($row_pr->status != STATUS_CLOSED) {
                        $this->mdl_general->update('in_pr', array('pr_id' => $row->pr_id), array('status' => STATUS_CLOSED));

                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $row->pr_id;
                        $data_log['feature_id'] = Feature::FEATURE_PR;
                        $data_log['log_subject'] = 'Close PR (' . $row_pr->pr_code . ') Automatic by system (Approve ' . $row->po_code . ')';
                        $data_log['action_type'] = STATUS_CLOSED;
                        $this->db->insert('app_log', $data_log);
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'PO not found.';
            }
        }

        return $result;
    }

    private function disapprove_po($po_id = 0){
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($po_id > 0) {
            $qry = $this->db->get_where('in_po', array('po_id' => $po_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                $count_grn = $this->mdl_general->count('in_grn', array('po_id' => $po_id, 'status <>' => STATUS_CANCEL));
                if($count_grn > 0){
                    $result['error'] = '1';
                    $result['message'] = 'There is an active GRN from this PO. Cancel GRN first.';
                } else {
                    $qry_pr = $this->db->get_where('in_pr', array('pr_id' => $row->pr_id));
                    $row_pr = $qry_pr->row();

                    if($row_pr->status != STATUS_APPROVE) {

                        /*$qry_po_detail = $this->db->get_where('in_po_detail', array('po_id' => $po_id));
                        foreach($qry_po_detail->result() as $row_po_detail) {
                            $qry_pr_detail = $this->db->get_where('in_pr_item', array('pr_item_id' => $row_po_detail->pr_item_id));
                            if ($qry_pr_detail->num_rows() > 0) {
                                $row_pr_detail = $qry_pr_detail->row();

                                $data_pr_detail = array();
                                if (($row_po_detail->item_qty + $row_pr_detail->qty_remain) <= $row_pr_detail->item_qty) {
                                    $data_pr_detail['qty_remain'] = ($row_po_detail->item_qty + $row_pr_detail->qty_remain);
                                } else {
                                    $data_pr_detail['qty_remain'] = $row_pr_detail->item_qty;
                                }

                                $this->mdl_general->update('in_pr_item', array('pr_item_id' => $row_pr_detail->pr_item_id), $data_pr_detail);
                            }
                        }*/

                        $this->mdl_general->update('in_pr', array('pr_id' => $row->pr_id), array('status' => STATUS_APPROVE));

                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $row->pr_id;
                        $data_log['feature_id'] = Feature::FEATURE_PR;
                        $data_log['log_subject'] = 'Approve PR (' . $row_pr->pr_code . ') Automatic by system (Disapprove ' . $row->po_code . ')';
                        $data_log['action_type'] = STATUS_APPROVE;
                        $this->db->insert('app_log', $data_log);
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'PO not found.';
            }
        }

        return $result;
    }

    private function cancel_po($po_id = 0){
        $this->load->model('purchasing/mdl_purchasing');

        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($po_id > 0) {
            $qry = $this->db->get_where('in_po', array('po_id' => $po_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                $count_grn = $this->mdl_general->count('in_grn', array('po_id' => $po_id, 'status <>' => STATUS_CANCEL));
                if($count_grn > 0){
                    $result['error'] = '1';
                    $result['message'] = 'There is an active GRN from this PO. Cancel GRN first.';
                } else {
                    $qry_det = $this->mdl_purchasing->get_po_detail(false, array('in_po_detail.po_id' => $po_id));
                    foreach($qry_det->result() as $row_det){
                        $qry_det2 = $this->db->get_where('in_pr_item', array('pr_item_id' => $row_det->pr_item_id));
                        $row_det2 = $qry_det2->row();

                        $this->mdl_general->update('in_pr_item', array('pr_item_id' => $row_det->pr_item_id), array('qty_remain' => ($row_det2->qty_remain + $row_det->item_qty)));

                        //Change status order
                        if ($row_det2->item_id > 0) {

                        } else {
                            $this->mdl_general->update('in_ms_item', array('item_id' => $row_det->item_id), array('status_order' => 0));
                        }
                    }

                    $count_pr_item = $this->mdl_general->count('in_pr_item', array('pr_id' => $row->pr_id, 'qty_remain >' => 0));
                    if($count_pr_item > 0){
                        $qry_pr = $this->db->get_where('in_pr', array('pr_id' => $row->pr_id));
                        $row_pr = $qry_pr->row();

                        if($row_pr->status != STATUS_APPROVE) {
                            $this->mdl_general->update('in_pr', array('pr_id' => $row->pr_id), array('status' => STATUS_APPROVE));

                            $data_log['user_id'] = my_sess('user_id');
                            $data_log['log_date'] = date('Y-m-d H:i:s');
                            $data_log['reff_id'] = $row->pr_id;
                            $data_log['feature_id'] = Feature::FEATURE_PR;
                            $data_log['log_subject'] = 'Approve PR (' . $row_pr->pr_code . ') Automatic by system (Cancel ' . $row->po_code . ')';
                            $data_log['action_type'] = STATUS_APPROVE;
                            $this->db->insert('app_log', $data_log);
                        }
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'PO not found.';
            }
        }

        return $result;
    }

    public function pdf_po($po_id = 0, $isMultiPages = 1) {

        if($po_id > 0){
            $this->load->model('purchasing/mdl_purchasing');

            $qry = $this->mdl_purchasing->get_po_list(false, array('in_po.po_id' => $po_id));
            if($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $qry_project = $this->db->get_where('ms_project', array('project_initial' => Purchasing::PROJECT_MIT));
                $data['row_project'] = $qry_project->row();

                $data['qry_det'] =  $this->mdl_purchasing->get_po_detail(false, array('in_po_detail.po_id' => $po_id));

                //echo $this->db->last_query();
                $data['multi_pages'] = $isMultiPages > 0 ? true : false;

                $this->load->view('purchasing/po/pdf_po', $data);


                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->po_code . ".pdf", array('Attachment'=>0));

            }
            else {
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    public function pdf_po_cs($po_id = 0) {
        if($po_id > 0){
            $this->load->model('purchasing/mdl_purchasing');

            $qry = $qry_po = $this->db->get_where('view_in_po_amount', array('po_id' => $po_id));
            if($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_workflow'] = $this->mdl_general->get('in_po_workflow', array('project_id' => $data['row']->project_id, 'min_amount <=' => $data['row']->total_amount, 'max_amount >=' => $data['row']->total_amount), array(), 'pos DESC');

                $this->load->view('purchasing/po/pdf_po_cs.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->po_code . ".pdf", array('Attachment'=>0));
            }
            else {
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    public function pr_list_active(){
        $data_header = $this->data_header;

        $data = array();
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/jquery.base64.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/tableExport.js');

        $data['qry_department'] = $this->mdl_general->get('ms_department', array('status <> ' => STATUS_DELETE), array(), 'department_name');
        $data['qry_project'] = $this->mdl_general->get('ms_project', array('status <> ' => STATUS_DELETE), array(), 'project_initial');

        $this->load->view('layout/header', $data_header);
        $this->load->view('purchasing/po/pr_list_active', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_pr_list_active($menu_id = 0){
        $this->load->model('purchasing/mdl_purchasing');
        $like = array();
        $where = array();
        $where['status'] = STATUS_APPROVE;

        if(isset($_REQUEST['filter_pr_code'])){
            if($_REQUEST['filter_pr_code'] != ''){
                $like['pr_code'] = $_REQUEST['filter_pr_code'];
            }
        }
        if(isset($_REQUEST['filter_pr_date_from'])){
            if($_REQUEST['filter_pr_date_from'] != ''){
                $where['date_prepare >='] = dmy_to_ymd($_REQUEST['filter_pr_date_from']);
            }
        }
        if(isset($_REQUEST['filter_pr_date_to'])){
            if($_REQUEST['filter_pr_date_to'] != ''){
                $where['date_prepare <='] = dmy_to_ymd($_REQUEST['filter_pr_date_to']);
            }
        }
        if(isset($_REQUEST['filter_department_id'])){
            if($_REQUEST['filter_department_id'] != ''){
                $like['department_id'] = $_REQUEST['filter_department_id'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_po_code'])){
            if($_REQUEST['filter_po_code'] != ''){
                $like['all_po_code'] = $_REQUEST['filter_po_code'];
            }
        }
        if(isset($_REQUEST['filter_grn_code'])){
            if($_REQUEST['filter_grn_code'] != ''){
                $like['all_grn_code'] = $_REQUEST['filter_grn_code'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_pr_po_grn_stuff_remain', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'pr_id desc ';

        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'pr_code ' . $_REQUEST['order'][0]['dir'];
            }
        }
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'date_prepare ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_pr_po_grn_stuff_remain', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $num_po = $this->mdl_general->count('in_po', array('pr_id' => $row->pr_id, 'status <>' => STATUS_CANCEL));
            $btn_disapprove_pr = '';
            if($num_po == 0){
                $btn_disapprove_pr = '<button class="btn btn-xs red tooltips btn-disapprove" data-id="' . $row->pr_id . '" data-original-title="Disapprove PR" data-placement="top" data-container="body" data-action="' . STATUS_DISAPPROVE. '" data-title="' . $row->pr_code . '"><i class="fa fa-times-circle "></i></button>';
            }

            $records["data"][] = array(
                $i . '.',
                $row->pr_code,
                ymd_to_dmy($row->date_prepare),
                $row->department_name,
                $row->remarks,
                $row->all_po_code,
                $row->all_po_status,
                $row->all_grn_code,
                $row->all_grn_status,
                '<div class="btn-group btn-group-solid"><button class="btn btn-xs green tooltips btn-view" data-id="' . $row->pr_id . '" data-original-title="View" data-placement="top" data-container="body" data-title="' . $row->pr_code . '"><i class="fa fa-search"></i></button>' . $btn_disapprove_pr . '</div>'
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