<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pr_report extends CI_Controller {

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
        $this->report_progress();
    }


    #region PR Progress
    public function report_progress(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data['qry_uom'] = $this->mdl_general->get('in_ms_uom', array('status <> ' => STATUS_DELETE), array(), 'uom_code');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('purchasing/pr/report_pr_progress', $data);
        $this->load->view('layout/footer');
    }

    public function get_pr_progress(){
        $where = array();
        $like = array();

        $where_or = '';
        if (get_dept_name(my_sess('department_id')) != Purchasing::DEPT_GEN) {
            $where_or .= " ( department_id = " . my_sess('department_id');
            $where_or .= " OR pr_dept_created = " . my_sess('department_id');
            $where_or .= " OR po_dept_created = " . my_sess('department_id');
            $where_or .= " OR grn_dept_created = " . my_sess('department_id');
            $where_or .= " ) ";
        }

        if(isset($_REQUEST['filter_pr_no'])){
            if($_REQUEST['filter_pr_no'] != ''){
                $like['pr_code'] = $_REQUEST['filter_pr_no'];
            }
        }
        if(isset($_REQUEST['filter_date_prepare_from'])){
            if($_REQUEST['filter_date_prepare_from'] != ''){
                $where['pr_date_prepare >='] = dmy_to_ymd($_REQUEST['filter_date_prepare_from']);
            }
        }
        if(isset($_REQUEST['filter_date_prepare_to'])){
            if($_REQUEST['filter_date_prepare_to'] != ''){
                $where['pr_date_prepare <='] = dmy_to_ymd($_REQUEST['filter_date_prepare_to']);
            }
        }
        if(isset($_REQUEST['filter_date_approve_from'])){
            if($_REQUEST['filter_date_approve_from'] != ''){
                $where['pr_date_approved >='] = dmy_to_ymd($_REQUEST['filter_date_approve_from']);
            }
        }
        if(isset($_REQUEST['filter_date_approve_to'])){
            if($_REQUEST['filter_date_approve_to'] != ''){
                $where['pr_date_approved <='] = dmy_to_ymd($_REQUEST['filter_date_approve_to']);
            }
        }
        if(isset($_REQUEST['filter_item_name'])){
            if($_REQUEST['filter_item_name'] != ''){
                $like['item_desc'] = $_REQUEST['filter_item_name'];
            }
        }
        if(isset($_REQUEST['filter_pr_uom_id'])){
            if($_REQUEST['filter_pr_uom_id'] != ''){
                $where['uom_pr'] = $_REQUEST['filter_pr_uom_id'];
            }
        }
        if(isset($_REQUEST['filter_pr_qty_from'])){
            if($_REQUEST['filter_pr_qty_from'] != ''){
                $where['pr_qty >='] = $_REQUEST['filter_pr_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_pr_qty_to'])){
            if($_REQUEST['filter_pr_qty_to'] != ''){
                $where['pr_qty <='] = $_REQUEST['filter_pr_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_pr_status'])){
            if($_REQUEST['filter_pr_status'] != ''){
                $where['pr_status'] = $_REQUEST['filter_pr_status'];
            }
        }
        //PO
        if(isset($_REQUEST['filter_po_no'])){
            if($_REQUEST['filter_po_no'] != ''){
                $like['po_code'] = $_REQUEST['filter_po_no'];
            }
        }
        if(isset($_REQUEST['filter_po_date_from'])){
            if($_REQUEST['filter_po_date_from'] != ''){
                $where['po_date >='] = dmy_to_ymd($_REQUEST['filter_po_date_from']);
            }
        }
        if(isset($_REQUEST['filter_po_date_to'])){
            if($_REQUEST['filter_po_date_to'] != ''){
                $where['po_date <='] = dmy_to_ymd($_REQUEST['filter_po_date_to']);
            }
        }
        if(isset($_REQUEST['filter_po_approve_from'])){
            if($_REQUEST['filter_po_approve_from'] != ''){
                $where['po_date_approved >='] = dmy_to_ymd($_REQUEST['filter_po_approve_from']);
            }
        }
        if(isset($_REQUEST['filter_po_approve_to'])){
            if($_REQUEST['filter_po_approve_to'] != ''){
                $where['po_date_approved <='] = dmy_to_ymd($_REQUEST['filter_po_approve_to']);
            }
        }
        if(isset($_REQUEST['filter_po_uom_id'])){
            if($_REQUEST['filter_po_uom_id'] != ''){
                $where['uom_po'] = $_REQUEST['filter_po_uom_id'];
            }
        }
        if(isset($_REQUEST['filter_po_qty_from'])){
            if($_REQUEST['filter_po_qty_from'] != ''){
                $where['po_qty >='] = $_REQUEST['filter_po_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_po_qty_to'])){
            if($_REQUEST['filter_po_qty_to'] != ''){
                $where['po_qty <='] = $_REQUEST['filter_po_qty_to'];
            }
        }
        //GRN
        if(isset($_REQUEST['filter_grn_no'])){
            if($_REQUEST['filter_grn_no'] != ''){
                $like['grn_code'] = $_REQUEST['filter_grn_no'];
            }
        }
        if(isset($_REQUEST['filter_grn_date_from'])){
            if($_REQUEST['filter_grn_date_from'] != ''){
                $where['grn_date >='] = dmy_to_ymd($_REQUEST['filter_grn_date_from']);
            }
        }
        if(isset($_REQUEST['filter_grn_date_to'])){
            if($_REQUEST['filter_grn_date_to'] != ''){
                $where['grn_date <='] = dmy_to_ymd($_REQUEST['filter_grn_date_to']);
            }
        }
        if(isset($_REQUEST['filter_grn_uom_id'])){
            if($_REQUEST['filter_grn_uom_id'] != ''){
                $where['uom_grn'] = $_REQUEST['filter_grn_uom_id'];
            }
        }
        if(isset($_REQUEST['filter_grn_qty_from'])){
            if($_REQUEST['filter_grn_qty_from'] != ''){
                $where['grn_qty >='] = $_REQUEST['filter_grn_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_grn_qty_to'])){
            if($_REQUEST['filter_grn_qty_to'] != ''){
                $where['grn_qty <='] = $_REQUEST['filter_grn_qty_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_purchase_progress', $where, $like, $where_or);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'pr_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'pr_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'pr_date_prepare ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'pr_date_approved ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'uom_pr ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'pr_qty ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'po_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 7){
                $order = 'po_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 8){
                $order = 'po_date_approved ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 9){
                $order = 'uom_po ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 10){
                $order = 'po_qty ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 11){
                $order = 'grn_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 12){
                $order = 'grn_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 13){
                $order = 'uom_grn ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 14){
                $order = 'grn_qty ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_purchase_progress', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_or);

        $i = $iDisplayStart + 1;
        foreach($qry->result_array() as $row){
            $records["data"][] = array(
                $row['pr_code'],
                ymd_to_dmy($row['pr_date_prepare']),
                $row['item_desc'],
                $row['uom_pr'],
                '<span class="mask_currency">' . $row['pr_qty'] . '</span>',
                $row['po_code'],
                ymd_to_dmy($row['po_date']),
                $row['uom_po'],
                '<span class="mask_currency">' . $row['po_qty'] . '</span>',
                $row['grn_code'],
                ymd_to_dmy($row['grn_date']),
                $row['uom_grn'],
                '<span class="mask_currency">' . $row['grn_qty'] . '</span>',
                get_status_name($row['pr_status']),
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        $records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    #endregion

    public function purchase_status(){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data['qry_uom'] = $this->mdl_general->get('in_ms_uom', array('status <> ' => STATUS_DELETE), array(), 'uom_code');
        $data['qry_curr'] = $this->db->get('currencytype');

        $this->load->view('layout/header_no_sidebar', $data_header);
        $this->load->view('purchasing/pr/purchase_status', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_purchase_status(){
        $where = array();
        $like = array();
        $where_string = "";

        if(isset($_REQUEST['filter_po_no'])){
            if($_REQUEST['filter_po_no'] != ''){
                $like['po_code'] = $_REQUEST['filter_po_no'];
            }
        }
        if(isset($_REQUEST['filter_date_po_from'])){
            if($_REQUEST['filter_date_po_from'] != ''){
                $where['po_date >='] = dmy_to_ymd($_REQUEST['filter_date_po_from']);
            }
        }
        if(isset($_REQUEST['filter_date_po_to'])){
            if($_REQUEST['filter_date_po_to'] != ''){
                $where['po_date <='] = dmy_to_ymd($_REQUEST['filter_date_po_to']);
            }
        }
        if(isset($_REQUEST['filter_supplier'])){
            if($_REQUEST['filter_supplier'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }
        if(isset($_REQUEST['filter_item_desc'])){
            if($_REQUEST['filter_item_desc'] != ''){
                $like['item_desc'] = $_REQUEST['filter_item_desc'];
            }
        }
        if(isset($_REQUEST['filter_po_uom_id'])){
            if($_REQUEST['filter_po_uom_id'] != ''){
                $where['po_uom_id'] = $_REQUEST['filter_po_uom_id'];
            }
        }
        if(isset($_REQUEST['filter_po_qty_from'])){
            if($_REQUEST['filter_po_qty_from'] != ''){
                $where['item_qty >='] = $_REQUEST['filter_po_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_po_qty_to'])){
            if($_REQUEST['filter_po_qty_to'] != ''){
                $where['item_qty <='] = $_REQUEST['filter_po_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_currencytype_id'])){
            if($_REQUEST['filter_currencytype_id'] != ''){
                $where['currencytype_id'] = $_REQUEST['filter_currencytype_id'];
            }
        }
        if(isset($_REQUEST['filter_po_price_from'])){
            if($_REQUEST['filter_po_price_from'] != ''){
                $where['item_price >='] = $_REQUEST['filter_po_price_from'];
            }
        }
        if(isset($_REQUEST['filter_po_price_to'])){
            if($_REQUEST['filter_po_price_to'] != ''){
                $where['item_price <='] = $_REQUEST['filter_po_price_to'];
            }
        }
        if(isset($_REQUEST['filter_po_disc_from'])){
            if($_REQUEST['filter_po_disc_from'] != ''){
                $where['item_disc >='] = $_REQUEST['filter_po_disc_from'];
            }
        }
        if(isset($_REQUEST['filter_po_disc_to'])){
            if($_REQUEST['filter_po_disc_to'] != ''){
                $where['item_disc <='] = $_REQUEST['filter_po_disc_to'];
            }
        }
        if(isset($_REQUEST['filter_po_tax_from'])){
            if($_REQUEST['filter_po_tax_from'] != ''){
                $where['item_tax >='] = $_REQUEST['filter_po_tax_from'];
            }
        }
        if(isset($_REQUEST['filter_po_tax_to'])){
            if($_REQUEST['filter_po_tax_to'] != ''){
                $where['item_tax <='] = $_REQUEST['filter_po_tax_to'];
            }
        }
        if(isset($_REQUEST['filter_po_tot_from'])){
            if($_REQUEST['filter_po_tot_from'] != ''){
                $where['total_amount >='] = $_REQUEST['filter_po_tot_from'];
            }
        }
        if(isset($_REQUEST['filter_po_tot_to'])){
            if($_REQUEST['filter_po_tot_to'] != ''){
                $where['total_amount <='] = $_REQUEST['filter_po_tot_to'];
            }
        }
        if(isset($_REQUEST['filter_grn_qty_from'])){
            if($_REQUEST['filter_grn_qty_from'] != ''){
                $where['grn_qty >='] = $_REQUEST['filter_grn_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_grn_qty_to'])){
            if($_REQUEST['filter_grn_qty_to'] != ''){
                $where['grn_qty <='] = $_REQUEST['filter_grn_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_po_status'])){
            if($_REQUEST['filter_po_status'] != ''){
                $where['status'] = $_REQUEST['filter_po_status'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_purchase_status', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'po_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'po_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'po_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_purchase_status', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->po_code,
                ymd_to_dmy($row->po_date),
                $row->supplier_name,
                $row->item_desc,
                $row->po_uom,
                '<span class="mask_currency">' . $row->item_qty . '</span>',
                $row->currencytype_code,
                '<span class="mask_currency">' . $row->item_price . '</span>',
                '<span class="mask_currency">' . $row->item_disc . '</span>',
                '<span class="mask_currency">' . $row->item_tax . '</span>',
                '<span class="mask_currency">' . $row->total_amount . '</span>',
                '<span class="mask_currency">' . ($row->grn_qty > 0 ? $row->grn_qty : '') . '</span>',
                get_status_name($row->status),
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        $records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }
}