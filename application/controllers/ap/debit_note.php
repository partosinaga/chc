<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Debit_Note extends CI_Controller {

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
	
	public function index() {
		tpd_404();
	}

    public function dn_form($debitnote_id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/icheck/skins/all.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/icheck/icheck.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        $data['debitnote_id'] = $debitnote_id;
        $data['qry_curr'] = $this->db->get('currencytype');

        if ($debitnote_id > 0) {
            $qry = $this->mdl_general->get('view_ap_dn_header', array('debitnote_id' => $debitnote_id));
            $data['row'] = $qry->row();

            $data['qry_detail'] = $this->mdl_general->get('view_ap_dn_detail', array('debitnote_id' => $debitnote_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ap/debit_note/dn_form', $data);
        $this->load->view('layout/footer');
    }
	
	public function dn_manage($type = 0, $debitnote_id = 0){
        if ($type == 0) {
            $this->dn_list(0);
        } else {
            $this->dn_form($debitnote_id);
        }
	}
	
    public function dn_history($type = 0, $debitnote_id = 0){
        if ($type == 0) {
            $this->dn_list(1);
        } else {
            $this->dn_form($debitnote_id);
        }
    }

    private function dn_list($type = 0) {
        /// 0 => Manage
        /// 1 => History

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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();
        $data['type'] = $type;

        $this->load->view('layout/header', $data_header);
        $this->load->view('ap/debit_note/dn_list.php', $data);
        $this->load->view('layout/footer');
    }
	
    public function ajax_dn_list($menu_id = 0, $type = 0){
        //$type : 0 => manage, 1 => History
        $this->load->model('ap/mdl_inv');

        if($type == 0){
            $where['status'] = STATUS_NEW;
        } else {
            $where['status <>'] = STATUS_NEW;
        }

        $like = array();
        if(isset($_REQUEST['filter_dn_code'])){
            if($_REQUEST['filter_dn_code'] != ''){
                $like['debitnote_code'] = $_REQUEST['filter_dn_code'];
            }
        }
        if(isset($_REQUEST['filter_dn_date_from'])){
            if($_REQUEST['filter_dn_date_from'] != ''){
                $where['debitnote_date >='] = dmy_to_ymd($_REQUEST['filter_dn_date_from']);
            }
        }
        if(isset($_REQUEST['filter_dn_date_to'])){
            if($_REQUEST['filter_dn_date_to'] != ''){
                $where['debitnote_date <='] = dmy_to_ymd($_REQUEST['filter_dn_date_to']);
            }
        }
        if(isset($_REQUEST['filter_supplier'])){
            if($_REQUEST['filter_supplier'] != ''){
                $like['supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }
        if(isset($_REQUEST['filter_inv_code'])){
            if($_REQUEST['filter_inv_code'] != ''){
                $like['inv_code'] = $_REQUEST['filter_inv_code'];
            }
        }
        if(isset($_REQUEST['filter_curr'])){
            if($_REQUEST['filter_curr'] != ''){
                $where['currencytype_id'] = $_REQUEST['filter_curr'];
            }
        }
        if(isset($_REQUEST['filter_amount'])){
            if($_REQUEST['filter_amount'] != ''){
                $like['amount'] = $_REQUEST['filter_amount'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $where['status'] = $_REQUEST['filter_status'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_ap_dn_header', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'debitnote_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'debitnote_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'debitnote_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'inv_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'currencytype_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'amount ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_ap_dn_header', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){

            $btn_action = '';
            $btn_action .= '<li><a href="' . base_url('ap/debit_note/' . ($type == '0' ? 'dn_manage' : 'dn_history') . '/1/' . $row->debitnote_id) . '.tpd">View</a></li>';

            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_POSTED)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_POSTED . '" data-id="' . $row->debitnote_id . '" data-code="' . $row->debitnote_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';
                }
                if(check_session_action($menu_id, STATUS_CANCEL)){
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CANCEL . '" data-id="' . $row->debitnote_id . '" data-code="' . $row->debitnote_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            }
            if ($row->status == STATUS_POSTED) {
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li><a href="' . base_url('ap/debit_note/pdf_dn/' . $row->debitnote_id) . '.tpd" target="_blank">Print</a></li>';
                    $btn_action .= '<li><a href="' . base_url('ap/debit_note/pdf_jv_dn/' . $row->debitnote_id) . '.tpd" target="_blank">Print JV</a></li>';
                }
            }

            $records["data"][] = array(
                $i . '.',
                $row->debitnote_code,
                ymd_to_dmy($row->debitnote_date),
                $row->supplier_name,
                $row->inv_code,
                $row->currencytype_code,
                '<span class="mask_currency">' . $row->amount . '</span>',
                $row->remarks,
                get_status_name($row->status),
                '<div class="btn-group">
					<button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false" style="margin-right: 0px;">
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

    public function ajax_modal_invoice() {
        $this->load->view('ap/debit_note/ajax_modal_invoice_list');
    }

    public function ajax_modal_invoice_list($supplier_id = 0, $exist_inv_id = '-', $num_index = 0){
        $this->load->model('ap/mdl_inv');

        $like = array();
        $where = array();

        $exist_inv_id = trim($exist_inv_id);
        $isexist = false;
        if($exist_inv_id != '-' && $exist_inv_id != '0'){
            $isexist = true;
            $arr_id = explode('_', $exist_inv_id);
        }

        $where['status'] = STATUS_POSTED;
        $where['supplier_id'] = $supplier_id;
        $where['inv_remain_amount >'] = 0;
        $where_string = '';
        if ($isexist) {
            $where_string = ' (inv_id IN(' . ($isexist ? implode(',', $arr_id) : 0) . ') OR supplier_id = ' . $supplier_id . ') ';
        }

        if(isset($_REQUEST['filter_inv_code'])){
            if($_REQUEST['filter_inv_code'] != ''){
                $like['inv_code'] = $_REQUEST['filter_inv_code'];
            }
        }
        if(isset($_REQUEST['filter_inv_date_from'])){
            if($_REQUEST['filter_inv_date_from'] != ''){
                $where['inv_date >='] = dmy_to_ymd($_REQUEST['filter_inv_date_from']);
            }
        }
        if(isset($_REQUEST['filter_inv_date_to'])){
            if($_REQUEST['filter_inv_date_to'] != ''){
                $where['inv_date <='] = dmy_to_ymd($_REQUEST['filter_inv_date_to']);
            }
        }
        if(isset($_REQUEST['filter_reff_no'])){
            if($_REQUEST['filter_reff_no'] != ''){
                $like['inv_ref'] = $_REQUEST['filter_reff_no'];
            }
        }
        if(isset($_REQUEST['filter_curr'])){
            if($_REQUEST['filter_curr'] != ''){
                $where['currencytype_id'] = $_REQUEST['filter_curr'];
            }
        }
        if(isset($_REQUEST['filter_reff_amount'])){
            if($_REQUEST['filter_reff_amount'] != ''){
                $like['totalgrand'] = $_REQUEST['filter_reff_amount'];
            }
        }
        if(isset($_REQUEST['filter_remarks'])){
            if($_REQUEST['filter_remarks'] != ''){
                $like['inv_desc'] = $_REQUEST['filter_remarks'];
            }
        }

        $iTotalRecords =  $this->mdl_inv->get_inv(true, $where, $like, '', 0, 0, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'inv_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'inv_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'inv_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'currencytype_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'curr_rate ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'totalgrand ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry =  $this->mdl_inv->get_inv(false, $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);
        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $vat = $row->totaltax;
            if ($vat > 0) {
                $qry_dn = $this->db->get_where('ap_debitnote', array('inv_id' => $row->inv_id, 'status' => STATUS_POSTED));
                foreach ($qry_dn->result() as $row_dn) {
                    if ($row_dn->include_tax == '1') {
                        $vat = $vat - (($row_dn->amount / $row->inv_remain_amount) * $vat);
                    }
                }

                $qry_cn = $this->db->get_where('ap_creditnote', array('inv_id' => $row->inv_id, 'status' => STATUS_POSTED));
                foreach ($qry_cn->result() as $row_cn) {
                    $vat = $vat + (($row_cn->amount / $row->inv_remain_amount) * $vat);
                }

                $qry_pv = $this->db->get_where('view_ap_payment_detail', array('inv_id' => $row->inv_id, 'payment_status' => STATUS_POSTED));
                foreach ($qry_pv->result() as $row_pv) {
                    $vat = $vat - ((($row_pv->amount + $row_pv->tax_wht) / $row->inv_remain_amount) * $vat);
                }
            }

            $text = 'Select';
            $attr = '';
            $attr .= ' data-id="' . $row->inv_id . '" ';
            $attr .= ' data-index="' . $num_index . '" ';
            $attr .= ' data-code="' . $row->inv_code . '" ';
            $attr .= ' data-inv-date="' . ymd_to_dmy($row->inv_date) . '" ';
            $attr .= ' data-curr-code="' . $row->currencytype_code . '" ';
            $attr .= ' data-inv-remain-amount="' . $row->inv_remain_amount . '" ';
            $attr .= ' data-vat="' . $vat . '" ';
            $attr .= ' data-rate="' . $row->curr_rate . '" ';
            $attr .= ' data-tax-id="' . $row->tax_id . '" ';
            $attr .= ' data-tax-code="' . $row->taxtype_code . '" ';
            $attr .= ' data-tax-wht-percent="' . $row->tax_wht_percent . '" ';
            if ($row->is_process == '1') {
                $attr .= ' disabled="disabled" ';
                $text = 'In Process';
            }
            if ($isexist) {
                foreach ($arr_id as $key => $val) {
                    if ($val == $row->inv_id) {
                        $attr .= ' disabled="disabled" ';
                        $text = 'Selected';
                    }
                }
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-inv" ' . $attr . '><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $i . '.',
                $row->inv_code,
                ymd_to_dmy($row->inv_date),
                $row->inv_ref,
                $row->currencytype_code,
                '<span class="mask_currency">' . $row->curr_rate . '</span>',
                '<span class="mask_currency">' . $row->inv_remain_amount . '</span>',
                $row->inv_desc,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_return() {
        $this->load->view('ap/debit_note/ajax_modal_return_list');
    }

    public function ajax_modal_return_list($supplier_id = 0, $return_id = 0){
        $this->load->model('inventory/mdl_return');

        $like = array();
        $where = array();

        $where['in_return.status'] = STATUS_POSTED;
        $where['in_return.supplier_id'] = $supplier_id;

        $iTotalRecords =  $this->mdl_return->get_return(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_return.return_code desc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'in_return.return_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'in_return.return_date ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry =  $this->mdl_return->get_return(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $text = 'Select';
            $attr = '';
            $attr .= ' data-id="' . $row->return_id . '" ';
            $attr .= ' data-code="' . $row->return_code . '" ';
            if ($return_id == $row->return_id) {
                $attr .= 'selected="selected" disabled="disabled"';
                $text = 'Selected';
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-return" ' . $attr . '><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $i . '.',
                $row->return_code,
                ymd_to_dmy($row->return_date),
                $row->grn_code,
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

    public function ajax_modal_dn_detail() {
        $this->load->view('ap/debit_note/ajax_modal_dn_detail_list');
    }

    public function ajax_modal_dn_detail_list($inv_code = '', $exist_detail_id = '-'){
        $this->load->model('inventory/mdl_return');

        $like = array();
        $where = array();

        $where['journal_no'] = $inv_code;
        $exist_detail_id = trim($exist_detail_id);
        $isexist = false;
        if($exist_detail_id != '-'){
            $isexist = true;
            $arr_id = explode('_', $exist_detail_id);
        }

        $iTotalRecords =  $this->mdl_general->count('view_ap_dn_get_invoice_journal', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'postdetail_id asc';

        $qry =  $this->mdl_general->get('view_ap_dn_get_invoice_journal', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $text = 'Select';
            $attr = '';
            $attr .= ' data-id="' . $row->postdetail_id . '" ';
            $attr .= ' data-coa-id="' . $row->coa_id . '" ';
            $attr .= ' data-charge-to="' . $row->coa_desc . '" ';
            $attr .= ' data-dept="' . $row->department_desc . '" ';
            $attr .= ' data-curr="' . $row->currencytype_code . '" ';
            $attr .= ' data-local-amount="' . $row->journal_debit . '" ';

            $amount = $row->journal_debit / floatval($row->curr_rate);
            $attr .= ' data-amount="' . $amount . '" ';
            if ($isexist) {
                foreach ($arr_id as $key => $val) {
                    if ($val == $row->postdetail_id) {
                        $attr .= ' disabled="disabled" ';
                        $text = 'Selected';
                    }
                }
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-dn-detail" ' . $attr . '><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $i . '.',
                $row->coa_desc,
                $row->department_desc,
                '<span class="mask_currency">' . $amount . '</span>',
                '<span class="mask_currency">' . $row->journal_debit . '</span>',
                $row->currencytype_code,
                $btn
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_dn_submit(){
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $data = array();

        if(isset($_POST)){
            $has_error = false;
            $this->db->trans_begin();
            $debitnote_id = $_POST['debitnote_id'];

            $data['debitnote_date'] = dmy_to_ymd(trim($_POST['debitnote_date']));
            $data['supplier_id'] = intval($_POST['supplier_id']);
            $data['inv_id'] = intval($_POST['inv_id']);
            $data['ref_no'] = trim($_POST['ref_no']);
            $data['return_id'] = intval($_POST['return_id']);
            $data['currencytype_id'] = intval($_POST['currencytype_id']);
            $data['curr_rate'] = trim($_POST['curr_rate']);
            $data['amount'] = trim($_POST['totalamount']);
            $data['remarks'] = trim($_POST['remarks']);
            $data['include_tax'] = (isset($_POST['include_tax']) ? '1' : '0');

            $qry_inv = $this->db->get_where('ap_invoiceheader', array('inv_id' => $data['inv_id']));
            $row_inv = $qry_inv->row();
            $qry_curr_inv = $this->db->get_where('currencytype', array('currencytype_id' => $row_inv->currencytype_id));
            $row_curr_inv = $qry_curr_inv->row();

            if ($row_curr_inv->currencytype_code != Purchasing::CURR_IDR) {
                if ($data['curr_rate'] <= 1) {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'Invoice ' . $row_inv->inv_code . ' in currency ' . $row_curr_inv->currencytype_code . ', Debit Note need currency rate.';
                }
            }

            if ($result['valid'] == '1') {
                if ($debitnote_id > 0) {
                    $qry = $this->db->get_where('ap_debitnote', array('debitnote_id' => $debitnote_id));
                    $row = $qry->row();

                    $arr_date = explode('-', $data['debitnote_date']);
                    $arr_date_old = explode('-', $row->debitnote_date);

                    if ($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]) {
                        $data['debitnote_code'] = $this->mdl_general->generate_code(Feature::FEATURE_AP_DEBIT_NOTE, $data['debitnote_date']);

                        if ($data['debitnote_code'] == '') {
                            $has_error = true;

                            $result['valid'] = '0';
                            $result['message'] = 'Failed generating code.';
                        }
                    }

                    if ($has_error == false) {
                        $data['user_modified'] = my_sess('user_id');
                        $data['date_modified'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $row->inv_id), array('is_process' => 0));

                        $this->mdl_general->update('ap_debitnote', array('debitnote_id' => $debitnote_id), $data);

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Successfully update Debit Note.');
                    }
                } else {
                    $data['debitnote_code'] = $this->mdl_general->generate_code(Feature::FEATURE_AP_DEBIT_NOTE, $data['debitnote_date']);

                    if ($data['debitnote_code'] != '') {
                        $data['user_created'] = my_sess('user_id');
                        $data['date_created'] = date('Y-m-d H:i:s');
                        $data['status'] = STATUS_NEW;

                        $this->db->insert('ap_debitnote', $data);
                        $debitnote_id = $this->db->insert_id();

                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $debitnote_id;
                        $data_log['feature_id'] = Feature::FEATURE_AP_DEBIT_NOTE;
                        $data_log['log_subject'] = 'Create AP Debit Note (' . $data['debitnote_code'] . ')';
                        $data_log['action_type'] = STATUS_NEW;
                        $this->db->insert('app_log', $data_log);

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', 'Successfully add Debit Note.');
                    } else {
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }
            }

            if($has_error == false) {
                $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $data['inv_id']), array('is_process' => 1));

                if (isset($_POST['detail_id'])) {
                    $i = 0;
                    foreach ($_POST['detail_id'] as $key => $val) {
                        $data_detail = array();

                        $status = $_POST['status'][$key];

                        $data_detail['debitnote_id'] = $debitnote_id;
                        $data_detail['coa_id'] = $_POST['coa_id'][$key];
                        $data_detail['journal_detail_id'] = $_POST['journal_detail_id'][$key];
                        $data_detail['amount'] = $_POST['amount'][$key];
                        $data_detail['local_amount'] = $_POST['local_amount'][$key];

                        if ($val > 0) {
                            if($status == STATUS_NEW) {
                                $this->mdl_general->update('ap_debitnote_detail', array('detail_id' => $val), $data_detail);
                            } else {
                                $this->db->delete('ap_debitnote_detail', array('detail_id' => $val));
                            }
                        } else {
                            if($status == STATUS_NEW) {
                                $this->db->insert('ap_debitnote_detail', $data_detail);
                            }
                        }
                        $i++;
                    }
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'No Detail Debit Note.';
                }
            }

            if($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    if(isset($_POST['save_close'])) {
                        $result['link'] = base_url('ap/debit_note/dn_manage.tpd');
                    } else{
                        $result['link'] = base_url('ap/debit_note/dn_manage/1/' . $debitnote_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function ajax_dn_action(){
        $result = array();

        $this->db->trans_begin();

        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = '';

        $debitnote_id = $_POST['debitnote_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if(isset($_POST['is_redirect'])){
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $debitnote_id;
        $data_log['feature_id'] = Feature::FEATURE_AP_DEBIT_NOTE;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($debitnote_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('ap_debitnote', array('debitnote_id' => $debitnote_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_POSTED) {
                    if ($row->status == STATUS_POSTED) {
                        $result['valid'] = '0';
                        $result['message'] = 'Debit Note already posted.';
                    } else {
                        //POSTING INVOICE
                        $valid = $this->posting_dn($debitnote_id);
                        $result['debug'] = $valid;

                        if ($valid['error'] == '0') {
                            $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $row->inv_id), array('is_process' => 0));

                            $data['date_posted'] = date('Y-m-d H:i:s');
                            $this->mdl_general->update('ap_debitnote', array('debitnote_id' => $debitnote_id), $data);

                            $data_log['log_subject'] = 'Posting AP Debit Note (' . $row->debitnote_code . ')';
                            $data_log['action_type'] = STATUS_POSTED;
                            $this->db->insert('app_log', $data_log);

                            $result['message'] = 'Successfully posting Debit Note.';
                        } else {
                            $result['valid'] = '0';
                            $result['message'] = $valid['message'];
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Debit Note already canceled.';
                    } else {
                        $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $row->inv_id), array('is_process' => 0));

                        $this->mdl_general->update('ap_debitnote', array('debitnote_id' => $debitnote_id), $data);

                        $data_log['log_subject'] = 'Cancel AP Debit Note (' . $row->debitnote_code . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel Debit Note.';
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

    private function posting_dn($debitnote_id = 0){
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if($debitnote_id > 0) {
            $qry_hd = $this->mdl_general->get('view_ap_dn_header', array('debitnote_id' => $debitnote_id));
            if ($qry_hd->num_rows() > 0) {
                $row_hd = $qry_hd->row();

                $this->load->model('finance/mdl_finance');

                $detail = array();

                $totalDebit = 0;
                $totalCredit = 0;
                $totalInvRate = 0;
                $qryDetails = $this->mdl_general->get('view_ap_dn_detail', array('debitnote_id' => $debitnote_id));
                if ($qryDetails->num_rows() > 0) {
                    foreach ($qryDetails->result() as $det) {
                        if ($det->coa_id > 0) {
                            $rowdet = array();
                            $rowdet['coa_id'] = $det->coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $det->coa_desc;
                            $rowdet['journal_debit'] = 0;
                            $rowdet['journal_credit'] = $det->local_amount;
                            $rowdet['reference_id'] = $det->detail_id;
                            $rowdet['transtype_id'] = 0;

                            $totalDebit += $rowdet['journal_debit'];
                            $totalCredit += $rowdet['journal_credit'];

                            $inv_rate = $det->amount * $row_hd->inv_curr_rate;
                            $totalInvRate += $inv_rate;

                            array_push($detail, $rowdet);
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'COA ID is empty.';

                            break;
                        }

                    }
                }

                if (trim($row_hd->inv_currencytype_code) != Purchasing::CURR_IDR) {
                    if ($totalInvRate != $totalCredit) {
                        $qry_key = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_FOREX_GAIN));
                        if ($qry_key->num_rows() > 0) {
                            $row_key = $qry_key->row();

                            if ($row_key->coa_id > 0) {
                                $qry_coa = $this->db->get_where('gl_coa', array('coa_id' => $row_key->coa_id));
                                $row_coa = $qry_coa->row();

                                $rowdet = array();
                                $rowdet['coa_id'] = $row_key->coa_id;
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $row_coa->coa_desc;
                                if ($totalCredit > $totalInvRate) {
                                    $rowdet['journal_debit'] = ($totalCredit - $totalInvRate);
                                    $rowdet['journal_credit'] = 0;
                                } else {
                                    $rowdet['journal_debit'] = 0;
                                    $rowdet['journal_credit'] = ($totalInvRate - $totalCredit);
                                }
                                $rowdet['reference_id'] = $row_hd->debitnote_id;
                                $rowdet['transtype_id'] = $row_key->transtype_id;

                                $totalDebit += $rowdet['journal_debit'];
                                $totalCredit += $rowdet['journal_credit'];

                                array_push($detail, $rowdet);
                            } else {
                                $result['error'] = '1';
                                $result['message'] = 'Spec AP FOREX not found.';
                            }
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Spec AP FOREX not found.';
                        }
                    }
                }

                if ($result['error'] == '0') {
                    $qry_key = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_DEBIT_NOTE));
                    if ($qry_key->num_rows() > 0) {
                        $row_key = $qry_key->row();

                        if ($row_key->coa_id > 0) {
                            $rowdet = array();
                            $rowdet['coa_id'] = $row_key->coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $row_hd->remarks;
                            $rowdet['journal_debit'] = ($totalCredit - $totalDebit);
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = $row_hd->debitnote_id;
                            $rowdet['transtype_id'] = $row_key->transtype_id;

                            $totalDebit += $rowdet['journal_debit'];
                            $totalCredit += $rowdet['journal_credit'];

                            array_push($detail, $rowdet);
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Spec Debit Note is empty.';
                        }
                    } else {
                        $result['error'] = '1';
                        $result['message'] = 'Spec Debit Note not found.';
                    }
                }

                if ($result['error'] == '0') {
                    if ($totalDebit == $totalCredit) {
                        $header = array();
                        $header['journal_no'] = $row_hd->debitnote_code;
                        $header['journal_date'] = $row_hd->debitnote_date;
                        $header['journal_remarks'] = $row_hd->remarks;
                        $header['modul'] = GLMOD::GL_MOD_AP;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = strval($row_hd->debitnote_id);

                        $valid = $this->mdl_finance->postJournal($header, $detail);

                        if ($valid == false) {
                            $result['error'] = '1';
                            $result['message'] = 'Failed insert journal.';
                        }
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'Debit Note not found.';
            }
        }

        return $result;
    }

    public function pdf_dn($debitnote_id = 0) {
        if ($debitnote_id > 0) {
            $qry = $this->db->get_where('view_ap_dn_header', array('debitnote_id' => $debitnote_id));
            if ($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_det'] =  $this->db->get_where('view_ap_dn_detail', array('debitnote_id' => $debitnote_id));

                $this->load->view('ap/debit_note/pdf_dn.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->debitnote_code . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

    public function pdf_jv_dn($debitnote_id = 0) {
        if ($debitnote_id > 0) {
            $qry = $this->db->get_where('view_ap_dn_header', array('debitnote_id' => $debitnote_id));
            if ($qry->num_rows() > 0) {
                $data['row'] = $qry->row();

                $data['qry_det'] =  $this->db->get_where('view_get_journal_detail', array('journal_no' => $data['row']->debitnote_code));

                $this->load->view('ap/debit_note/pdf_jv_dn.php', $data);

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->debitnote_code . ".pdf", array('Attachment'=>0));
            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

}

/* End of file debit_note.php */
/* Location: ./application/controllers/AP/debit_note.php */
	