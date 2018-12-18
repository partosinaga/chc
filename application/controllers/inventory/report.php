<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {

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
        tpd_404();
    }

    public function list_item(){
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
        $this->load->view('inventory/report/list_item', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_item_list(){
        $where = array();
        $like = array();
        $where_string = "";

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
        if(isset($_REQUEST['filter_uom_id'])){
            if($_REQUEST['filter_uom_id'] != ''){
                $where['uom_id'] = $_REQUEST['filter_uom_id'];
            }
        }
        if(isset($_REQUEST['filter_factor_from'])){
            if($_REQUEST['filter_factor_from'] != ''){
                $where['qty_distribution >='] = $_REQUEST['filter_factor_from'];
            }
        }
        if(isset($_REQUEST['filter_factor_to'])){
            if($_REQUEST['filter_factor_to'] != ''){
                $where['qty_distribution <='] = $_REQUEST['filter_factor_to'];
            }
        }
        if(isset($_REQUEST['filter_dist_uom_id'])){
            if($_REQUEST['filter_dist_uom_id'] != ''){
                $where['uom_id_distribution'] = $_REQUEST['filter_dist_uom_id'];
            }
        }
        if(isset($_REQUEST['filter_qty_from'])){
            if($_REQUEST['filter_qty_from'] != ''){
                $where['on_hand_qty >='] = $_REQUEST['filter_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_qty_to'])){
            if($_REQUEST['filter_qty_to'] != ''){
                $where['on_hand_qty <='] = $_REQUEST['filter_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_min_stock_from'])){
            if($_REQUEST['filter_min_stock_from'] != ''){
                $where['min_stock >='] = $_REQUEST['filter_min_stock_from'];
            }
        }
        if(isset($_REQUEST['filter_min_stock_to'])){
            if($_REQUEST['filter_min_stock_to'] != ''){
                $where['min_stock <='] = $_REQUEST['filter_min_stock_to'];
            }
        }
        if(isset($_REQUEST['filter_max_stock_from'])){
            if($_REQUEST['filter_max_stock_from'] != ''){
                $where['max_stock >='] = $_REQUEST['filter_max_stock_from'];
            }
        }
        if(isset($_REQUEST['filter_max_stock_to'])){
            if($_REQUEST['filter_max_stock_to'] != ''){
                $where['max_stock <='] = $_REQUEST['filter_max_stock_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_in_get_item_stock', $where, $like, $where_string);

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
        }

        $qry = $this->mdl_general->get('view_in_get_item_stock', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->item_code,
                $row->item_desc,
                $row->uom_in_code,
                '<span class="mask_currency">' . $row->qty_distribution . '</span>',
                $row->uom_out_code,
                '<span class="mask_currency">' . $row->on_hand_qty . '</span>',
                '<span class="mask_currency">' . $row->min_stock . '</span>',
                '<span class="mask_currency">' . $row->max_stock . '</span>',
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

    public function stock_position(){
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
        $this->load->view('inventory/report/stock_position', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_stock_position(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $tgl = date('Y-m-d');

        if(isset($_REQUEST['filter_date'])){
            if($_REQUEST['filter_date'] != ''){
                $tgl = dmy_to_ymd($_REQUEST['filter_date']);
            }
        }

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
        if(isset($_REQUEST['filter_uom_id'])){
            if($_REQUEST['filter_uom_id'] != ''){
                $where['uom_code'] = $_REQUEST['filter_uom_id'];
            }
        }
        if(isset($_REQUEST['filter_qty_from'])){
            if($_REQUEST['filter_qty_from'] != ''){
                $where['stock_qty >='] = $_REQUEST['filter_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_qty_to'])){
            if($_REQUEST['filter_qty_to'] != ''){
                $where['stock_qty <='] = $_REQUEST['filter_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_price_from'])){
            if($_REQUEST['filter_price_from'] != ''){
                $where['avg_price >='] = $_REQUEST['filter_price_from'];
            }
        }
        if(isset($_REQUEST['filter_price_to'])){
            if($_REQUEST['filter_price_to'] != ''){
                $where['avg_price <='] = $_REQUEST['filter_price_to'];
            }
        }
        if(isset($_REQUEST['filter_total_from'])){
            if($_REQUEST['filter_total_from'] != ''){
                $where['total >='] = $_REQUEST['filter_total_from'];
            }
        }
        if(isset($_REQUEST['filter_total_to'])){
            if($_REQUEST['filter_total_to'] != ''){
                $where['total <='] = $_REQUEST['filter_total_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('fxn_stock_position("' . $tgl . '")', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'item_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('fxn_stock_position("' . $tgl . '")', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->item_code,
                $row->item_desc,
                $row->uom_code,
                '<span class="mask_currency">' . $row->stock_qty . '</span>',
                '<span class="mask_currency">' . $row->avg_price . '</span>',
                '<span class="mask_currency">' . $row->total . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function stock_mutation(){
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
        $this->load->view('inventory/report/stock_mutation', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_stock_mutation(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $tgl_end = date('Y-m-d');
        $tgl_start = date("Y-m-d",strtotime("-1 month"));

        if(isset($_REQUEST['filter_date_start'])){
            if($_REQUEST['filter_date_start'] != ''){
                $tgl_start = dmy_to_ymd($_REQUEST['filter_date_start']);
            }
        }
        if(isset($_REQUEST['filter_date_end'])){
            if($_REQUEST['filter_date_end'] != ''){
                $tgl_end = dmy_to_ymd($_REQUEST['filter_date_end']);
            }
        }

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
        if(isset($_REQUEST['filter_uom_code'])){
            if($_REQUEST['filter_uom_code'] != ''){
                $where['uom_code'] = $_REQUEST['filter_uom_code'];
            }
        }
        if(isset($_REQUEST['filter_start_qty_from'])){
            if($_REQUEST['filter_start_qty_from'] != ''){
                $where['stock_qty >='] = $_REQUEST['filter_start_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_start_qty_to'])){
            if($_REQUEST['filter_start_qty_to'] != ''){
                $where['stock_qty <='] = $_REQUEST['filter_start_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_in_qty_from'])){
            if($_REQUEST['filter_in_qty_from'] != ''){
                $where['total_doc_in >='] = $_REQUEST['filter_in_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_in_qty_to'])){
            if($_REQUEST['filter_in_qty_to'] != ''){
                $where['total_doc_in <='] = $_REQUEST['filter_in_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_out_qty_from'])){
            if($_REQUEST['filter_out_qty_from'] != ''){
                $where['total_doc_out >='] = $_REQUEST['filter_out_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_out_qty_to'])){
            if($_REQUEST['filter_out_qty_to'] != ''){
                $where['total_doc_out <='] = $_REQUEST['filter_out_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_end_qty_from'])){
            if($_REQUEST['filter_end_qty_from'] != ''){
                $where['end_stock_qty >='] = $_REQUEST['filter_end_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_end_qty_to'])){
            if($_REQUEST['filter_end_qty_to'] != ''){
                $where['end_stock_qty <='] = $_REQUEST['filter_end_qty_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('fxn_stock_mutation("' . $tgl_start . '", "' . $tgl_end . '")', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'item_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('fxn_stock_mutation("' . $tgl_start . '", "' . $tgl_end . '")', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->item_code,
                $row->item_desc,
                $row->uom_code,
                '<span class="mask_currency">' . $row->stock_qty . '</span>',
                '<span class="mask_currency">' . $row->total_doc_in . '</span>',
                '<span class="mask_currency">' . $row->total_doc_out . '</span>',
                '<span class="mask_currency">' . $row->end_stock_qty . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function stock_mutation_price(){
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
        $this->load->view('inventory/report/stock_mutation_price', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_stock_mutation_price(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $tgl_end = date('Y-m-d');
        $tgl_start = date("Y-m-d",strtotime("-1 month"));

        if(isset($_REQUEST['filter_date_start'])){
            if($_REQUEST['filter_date_start'] != ''){
                $tgl_start = dmy_to_ymd($_REQUEST['filter_date_start']);
            }
        }
        if(isset($_REQUEST['filter_date_end'])){
            if($_REQUEST['filter_date_end'] != ''){
                $tgl_end = dmy_to_ymd($_REQUEST['filter_date_end']);
            }
        }

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
        if(isset($_REQUEST['filter_uom_code'])){
            if($_REQUEST['filter_uom_code'] != ''){
                $where['uom_code'] = $_REQUEST['filter_uom_code'];
            }
        }
        if(isset($_REQUEST['filter_start_qty_from'])){
            if($_REQUEST['filter_start_qty_from'] != ''){
                $where['stock_qty >='] = $_REQUEST['filter_start_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_start_qty_to'])){
            if($_REQUEST['filter_start_qty_to'] != ''){
                $where['stock_qty <='] = $_REQUEST['filter_start_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_start_amount_from'])){
            if($_REQUEST['filter_start_amount_from'] != ''){
                $where['start_total_price >='] = $_REQUEST['filter_start_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_start_amount_to'])){
            if($_REQUEST['filter_start_amount_to'] != ''){
                $where['start_total_price <='] = $_REQUEST['filter_start_amount_to'];
            }
        }
        if(isset($_REQUEST['filter_mutation_qty_from'])){
            if($_REQUEST['filter_mutation_qty_from'] != ''){
                $where['mutation_qty >='] = $_REQUEST['filter_mutation_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_mutation_qty_to'])){
            if($_REQUEST['filter_mutation_qty_to'] != ''){
                $where['mutation_qty <='] = $_REQUEST['filter_mutation_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_mutation_amount_from'])){
            if($_REQUEST['filter_mutation_amount_from'] != ''){
                $where['mutation_price >='] = $_REQUEST['filter_mutation_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_mutation_amount_to'])){
            if($_REQUEST['filter_mutation_amount_to'] != ''){
                $where['mutation_price <='] = $_REQUEST['filter_mutation_amount_to'];
            }
        }
        if(isset($_REQUEST['filter_end_qty_from'])){
            if($_REQUEST['filter_end_qty_from'] != ''){
                $where['end_stock_qty >='] = $_REQUEST['filter_end_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_end_qty_to'])){
            if($_REQUEST['filter_end_qty_to'] != ''){
                $where['end_stock_qty <='] = $_REQUEST['filter_end_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_end_amount_from'])){
            if($_REQUEST['filter_end_amount_from'] != ''){
                $where['end_total_price >='] = $_REQUEST['filter_end_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_end_amount_to'])){
            if($_REQUEST['filter_end_amount_to'] != ''){
                $where['end_total_price <='] = $_REQUEST['filter_end_amount_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('fxn_stock_mutation("' . $tgl_start . '", "' . $tgl_end . '")', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'item_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('fxn_stock_mutation("' . $tgl_start . '", "' . $tgl_end . '")', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->item_code,
                $row->item_desc,
                $row->uom_code,
                '<span class="mask_currency">' . $row->stock_qty . '</span>',
                '<span class="mask_currency">' . $row->start_total_price . '</span>',
                '<span class="mask_currency">' . $row->mutation_qty . '</span>',
                '<span class="mask_currency">' . $row->mutation_price . '</span>',
                '<span class="mask_currency">' . $row->end_stock_qty . '</span>',
                '<span class="mask_currency">' . $row->end_total_price . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function stock_mutation_detail(){
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
        $this->load->view('inventory/report/stock_mutation_detail', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_stock_mutation_detail(){
        $where = array();
        $like = array();
        $where_string = "";
        $records = array();
        $where['created_date_date >='] = date("Y-m-d",strtotime("-1 month"));
        $where['created_date_date <='] = date('Y-m-d');

        if(isset($_REQUEST['filter_date_start'])){
            if($_REQUEST['filter_date_start'] != ''){
                $where['created_date_date >='] = dmy_to_ymd($_REQUEST['filter_date_start']);
            }
        }
        if(isset($_REQUEST['filter_date_end'])){
            if($_REQUEST['filter_date_end'] != ''){
                $where['created_date_date <='] = dmy_to_ymd($_REQUEST['filter_date_end']);
            }
        }

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
        if(isset($_REQUEST['filter_uom_code'])){
            if($_REQUEST['filter_uom_code'] != ''){
                $where['uom_code'] = $_REQUEST['filter_uom_code'];
            }
        }
        if(isset($_REQUEST['filter_doc_no'])){
            if($_REQUEST['filter_doc_no'] != ''){
                $like['doc_code'] = $_REQUEST['filter_doc_no'];
            }
        }
        if(isset($_REQUEST['filter_doc_date_from'])){
            if($_REQUEST['filter_doc_date_from'] != ''){
                $where['doc_date >='] = dmy_to_ymd($_REQUEST['filter_doc_date_from']);
            }
        }
        if(isset($_REQUEST['filter_doc_date_to'])){
            if($_REQUEST['filter_doc_date_to'] != ''){
                $where['doc_date <='] = dmy_to_ymd($_REQUEST['filter_doc_date_to']);
            }
        }
        if(isset($_REQUEST['filter_qty_from'])){
            if($_REQUEST['filter_qty_from'] != ''){
                $where['doc_qty >='] = $_REQUEST['filter_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_qty_to'])){
            if($_REQUEST['filter_qty_to'] != ''){
                $where['doc_qty <='] = $_REQUEST['filter_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_amount_from'])){
            if($_REQUEST['filter_amount_from'] != ''){
                $where['price >='] = $_REQUEST['filter_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_amount_to'])){
            if($_REQUEST['filter_amount_to'] != ''){
                $where['price <='] = $_REQUEST['filter_amount_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_in_stock_mutation_detail', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'doc_date desc, doc_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'doc_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'doc_date ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_in_stock_mutation_detail', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->item_code,
                $row->item_desc,
                $row->uom_code,
                $row->doc_code,
                ymd_to_dmy($row->doc_date),
                '<span class="mask_currency">' . $row->doc_qty . '</span>',
                '<span class="mask_currency">' . $row->price . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function stock_issue_return(){
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
        $this->load->view('inventory/report/stock_issue_return', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_stock_issue_return(){
        $where = array();
        $like = array();
        $where_string = " doc_type IN(" . Feature::FEATURE_RETURN . "," . Feature::FEATURE_STOCK_ISSUE . ") ";
        $records = array();
        $where['created_date_date >='] = date("Y-m-d",strtotime("-1 month"));
        $where['created_date_date <='] = date('Y-m-d');

        if(isset($_REQUEST['filter_date_start'])){
            if($_REQUEST['filter_date_start'] != ''){
                $where['created_date_date >='] = dmy_to_ymd($_REQUEST['filter_date_start']);
            }
        }
        if(isset($_REQUEST['filter_date_end'])){
            if($_REQUEST['filter_date_end'] != ''){
                $where['created_date_date <='] = dmy_to_ymd($_REQUEST['filter_date_end']);
            }
        }

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
        if(isset($_REQUEST['filter_uom_code'])){
            if($_REQUEST['filter_uom_code'] != ''){
                $where['uom_code'] = $_REQUEST['filter_uom_code'];
            }
        }
        if(isset($_REQUEST['filter_doc_no'])){
            if($_REQUEST['filter_doc_no'] != ''){
                $like['doc_code'] = $_REQUEST['filter_doc_no'];
            }
        }
        if(isset($_REQUEST['filter_doc_date_from'])){
            if($_REQUEST['filter_doc_date_from'] != ''){
                $where['doc_date >='] = dmy_to_ymd($_REQUEST['filter_doc_date_from']);
            }
        }
        if(isset($_REQUEST['filter_doc_date_to'])){
            if($_REQUEST['filter_doc_date_to'] != ''){
                $where['doc_date <='] = dmy_to_ymd($_REQUEST['filter_doc_date_to']);
            }
        }
        if(isset($_REQUEST['filter_req_code'])){
            if($_REQUEST['filter_req_code'] != ''){
                $like['request_code'] = $_REQUEST['filter_req_code'];
            }
        }
        if(isset($_REQUEST['filter_qty_from'])){
            if($_REQUEST['filter_qty_from'] != ''){
                $where['doc_qty_no_min >='] = $_REQUEST['filter_qty_from'];
            }
        }
        if(isset($_REQUEST['filter_qty_to'])){
            if($_REQUEST['filter_qty_to'] != ''){
                $where['doc_qty_no_min <='] = $_REQUEST['filter_qty_to'];
            }
        }
        if(isset($_REQUEST['filter_amount_from'])){
            if($_REQUEST['filter_amount_from'] != ''){
                $where['price_no_min >='] = $_REQUEST['filter_amount_from'];
            }
        }
        if(isset($_REQUEST['filter_amount_to'])){
            if($_REQUEST['filter_amount_to'] != ''){
                $where['price_no_min <='] = $_REQUEST['filter_amount_to'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_in_stock_mutation_detail_with_req', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'doc_date desc, doc_code asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'doc_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'doc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }

        }

        $qry = $this->mdl_general->get('view_in_stock_mutation_detail_with_req', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $row->doc_code,
                ymd_to_dmy($row->doc_date),
                $row->request_code,
                $row->item_code,
                $row->item_desc,
                $row->uom_code,
                '<span class="mask_currency">' . $row->doc_qty_no_min . '</span>',
                '<span class="mask_currency">' . $row->price_no_min . '</span>',
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

}