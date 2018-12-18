<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modalbook extends CI_Controller {

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

    #region COA

    public function ajax_coa_by_id(){
        $this->load->view('general/book_coa_by_id');
    }

    public function get_coa_list_by_id($coa_id, $detail_id = 0, $unique_id = '', $parentrow_id = 0, $coa_id_exist = '-', $arr_coa = '-', $str_tax = '-', $showCFCOA = 1, $showARAP = 1){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        $where['gl_coa.status <>'] = STATUS_DELETE;
        $where['gl_coa.is_display >'] = 0;

        $where_string = '';

        if($arr_coa == '-')
            $arr_coa = '';
        if($str_tax == '-')
            $str_tax = '';

        if ($arr_coa != '' && strlen($arr_coa) > 1) {
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

        if($showCFCOA <= 0){
            if($where_string != ''){
                $where_string .= ' AND ';
            }

            $where_string .= ' gl_coa.coa_code NOT IN(Select coa_code From gl_cashflow_parameter Where status = ' . STATUS_NEW . ') ';
        }

        if($showARAP <= 0){
            $ar_coa = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
            $ar_coa_corp = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
            $ap_coa = FNSpec::get(FNSpec::FIN_AP_INVOICE);
            $ap2_coa = FNSpec::get(FNSpec::FIN_AP_PAYMENT_ADV);

            $list = array();
            if ($ar_coa['coa_id'] > 0) {
                if(!in_array($ar_coa['coa_id'],$list)){
                    $list[] = $ar_coa['coa_id'];
                }
            }

            if ($ar_coa_corp['coa_id'] > 0) {
                if(!in_array($ar_coa_corp['coa_id'],$list)){
                    $list[] = $ar_coa_corp['coa_id'];
                }
            }

            if ($ap_coa['coa_id'] > 0) {
                if(!in_array($ap_coa['coa_id'],$list)){
                    $list[] = $ap_coa['coa_id'];
                }
            }

            if ($ap2_coa['coa_id'] > 0) {
                if(!in_array($ap2_coa['coa_id'],$list)){
                    $list[] = $ap2_coa['coa_id'];
                }
            }

            if(count($list) > 0){
                if($where_string != ''){
                    $where_string .= ' AND ';
                }

                $where_string .= ' gl_coa.coa_id NOT IN(' . implode(',',$list) . ') ';
            }
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
        //$records["debug"] = $showARAP . ' -> ' . $this->db->last_query();
        $coa_id_exist = trim($coa_id_exist);
        $isexist = false;
        if($coa_id_exist != '-'){
            $isexist = true;
            $arr_id = explode('_', $coa_id_exist);
        }

        //List of Cashflow COA
        $listCF = array();
        $cfAccounts = $this->db->query('SELECT coa_id,coa_code FROM gl_cashflow_parameter WHERE status = ' . STATUS_NEW);
        if($cfAccounts->num_rows() > 0){
            foreach($cfAccounts->result() as $cf){
                if(!in_array($cf->coa_code,$listCF)) {
                    $listCF[] = $cf->coa_code;
                }
            }
        }

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';
            $attr .= ' coa-id="' . $row->coa_id . '" ';
            $attr .= ' coa-code="' . $row->coa_code . '" ';
            $attr .= ' coa-desc="' . $row->coa_desc . '" ';
            $attr .= ' detail-id="' . $detail_id . '" ';
            $attr .= ' unique-id="' . $unique_id . '" ';
            $attr .= ' parent-row-id="' . $parentrow_id . '" ';
            $attr .= ' parent-is-tax="' . ($str_tax != '' ? 1 : 0) . '" ';

            $isCFCOA = false;
            if(in_array($row->coa_code,$listCF)){
                $isCFCOA = true;
            }else{
                $isCFCOA = false;
            }
            $attr .= ' parent-is-cf="' . ($isCFCOA ? 1 : 0) . '" ';

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
        //$records["debug"] = $unique_id;

        echo json_encode($records);
    }

    public function get_coa_list_by_code($coa_code = ''){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        $where['gl_coa.status <>'] = STATUS_DELETE;
        $where['gl_coa.is_display >'] = 0;

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

        $iTotalRecords = $this->mdl_finance->countCOA($where, $like, "");

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

        $qry = $this->mdl_finance->getCOA($where, $like, $order, $iDisplayLength, $iDisplayStart, "");

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';
            $attr .= ' coa-id="' . $row->coa_id . '" ';
            $attr .= ' coa-code="' . $row->coa_code . '" ';
            $attr .= ' coa-desc="' . $row->coa_desc . '" ';

            $text = "Select";
            if ($row->coa_code == $coa_code) {
                $attr .= 'selected="selected" disabled="disabled"';
                $text = "Selected";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-coa" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';
            if($coa_code == $row->coa_code){
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

    #endregion

    #region Modal Reservation

    public function ajax_reservation(){
        $this->load->view('general/book_reservation');
    }

    public function get_modal_reservation($num_index = 0, $reservation_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $server_date = date('Y-m-d');

        if(isset($_REQUEST['filter_code'])){
            if($_REQUEST['filter_code'] != ''){
                $like['view_cs_reservation.reservation_code'] = $_REQUEST['filter_code'];
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_cs_reservation.tenant_fullname'] = $_REQUEST['filter_name'];
            }
        }

        $where_str = ' status NOT IN(' . STATUS_DELETE . ',' .  STATUS_CANCEL . ') ';

        $joins = array(); //array("view_cs_reservation"=>"view_cs_reservation.reservation_id = ar.reservation_id");
        $iTotalRecords = $this->mdl_finance->countJoin("view_cs_reservation", $joins, $where, $like, $where_str);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'reservation_code, tenant_fullname asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'tenant_fullname ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin("view_cs_reservation.* ","view_cs_reservation", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, "", array(), $where_str);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $attr = '';
            $attr .= ' data-reservation-id="' . $row->reservation_id . '" ';
            $attr .= ' data-reservation-code="' . $row->reservation_code . '" ';
            $attr .= ' data-tenant-name="' . $row->tenant_fullname . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            if ($reservation_id == $row->reservation_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }else{
                $text = "Select";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-reservation" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $row->reservation_code,
                $row->tenant_fullname,
                $btn
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