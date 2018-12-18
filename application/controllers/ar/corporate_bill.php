<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Corporate_bill extends CI_Controller {
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
        $this->bill_manage();
    }

     #region Invoice Late Interest

    public function interest_manage($type = 1){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/util.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/bill/interest_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_interest_manage($period_dmy = ''){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        $period_date = new DateTime('');
        if($period_dmy != ''){
            $period_date = DateTime::createFromFormat('d-m-Y', $period_dmy);
        }

        $where['vw.month'] = $period_date->format('m');
        $where['vw.year'] = $period_date->format('Y');

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['vw.company_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_inv_no'])){
            if($_REQUEST['filter_inv_no'] != ''){
                $like['vw.inv_no'] = $_REQUEST['filter_inv_no'];
            }
        }

        if(isset($_REQUEST['filter_desc'])){
            if($_REQUEST['filter_desc'] != ''){
                $like['vw.interest_desc'] = $_REQUEST['filter_desc'];
            }
        }

        if(isset($_REQUEST['filter_start_from'])){
            if($_REQUEST['filter_start_from'] != ''){
                $where['CONVERT(date,vw.interest_start) >='] = dmy_to_ymd($_REQUEST['filter_start_from']);
            }
        }

        if(isset($_REQUEST['filter_start_end'])){
            if($_REQUEST['filter_start_end'] != ''){
                $where['CONVERT(date,vw.interest_start) <='] = dmy_to_ymd($_REQUEST['filter_start_end']);
            }
        }

        if(isset($_REQUEST['filter_until_from'])){
            if($_REQUEST['filter_until_from'] != ''){
                $where['CONVERT(date,vw.interest_end) >='] = dmy_to_ymd($_REQUEST['filter_until_from']);
            }
        }

        if(isset($_REQUEST['filter_until_from'])){
            if($_REQUEST['filter_until_from'] != ''){
                $where['CONVERT(date,vw.interest_end) <='] = dmy_to_ymd($_REQUEST['filter_until_from']);
            }
        }

        if(isset($_REQUEST['filter_days_start'])){
            if($_REQUEST['filter_days_start'] != ''){
                $where['CONVERT(date,vw.interest_days) >='] = dmy_to_ymd($_REQUEST['filter_days_start']);
            }
        }

        if(isset($_REQUEST['filter_days_end'])){
            if($_REQUEST['filter_days_end'] != ''){
                $where['CONVERT(date,vw.interest_days) <='] = dmy_to_ymd($_REQUEST['filter_days_end']);
            }
        }

        $joins = array(); //array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_debitnote_header.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_interest_charge as vw', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'vw.company_name, vw.inv_id ASC, vw.interest_start ASC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'vw.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'vw.inv_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'vw.interest_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'vw.interest_pendingamount ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'vw.interest_start ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'vw.interest_end ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('vw.*'
            ,'vw_ar_interest_charge as vw', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';

            if($row->status == STATUS_NEW) {
                $btn_action .= '<li> <a href="javascript:;" class="btn-delete tooltips" data-original-title="Cancel to Revise" data-id="' . $row->interestcharge_id . '" ><i class="fa fa-remove font-red-sunglo"></i> Delete</a> </li>';
                $btn_action .= '<li> <a href="javascript:;" class="btn-void tooltips" data-original-title="Delete/skip from Current Period" data-id="' . $row->interestcharge_id . '" ><i class="fa fa-check"></i> Skip</a> </li>';
            }

            $records["data"][] = array(
                $row->company_name,
                $row->inv_no,
                $row->interest_desc,
                format_num($row->interest_pendingamount,0),
                dmy_from_db($row->interest_start),
                dmy_from_db($row->interest_end),
                format_num($row->interest_days,0),
                format_num($row->interest_percent,2),
                format_num($row->interest_amount,0),
                ($btn_action != '' ? '<div class="btn-group">
                    <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                        Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        ' . $btn_action . '
					</ul>
				    </div>' : '<i class="fa fa-check"></i>')
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function ajax_submit_interest(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';
        $result['debug'] = '';

        $valid = true;

        $period_date = dmy_to_ymd($_POST['period_dmy']);

        if($period_date != ''){
            $this->load->model('finance/mdl_finance');

            $bill_date = DateTime::createFromFormat('Y-m-d', $period_date);
            $bill_date = $bill_date->format('Y-m-d');

            $specPenalty = FNSpec::get(FNSpec::INTEREST);

            $msg_warning = '';
            if($specPenalty['coa_id'] > 0 && $specPenalty['transtype_id'] > 0){
                $penalties = array();
                $trxPenalty = $this->db->get_where('ms_transtype', array('transtype_id' => $specPenalty['transtype_id']));
                if($trxPenalty->num_rows() > 0){
                    $trxPenalty = $trxPenalty->row();
                    $interestRate = $trxPenalty->due_interestrate;

                    if($interestRate > 0){
                        $unpaids = $this->db->query(@"SELECT DATEDIFF(day,inv_due_date,'". $bill_date ."') AS diff_date,*
                            FROM fxnARInvoiceHeaderByStatus(". STATUS_POSTED .")
                            WHERE DATEADD(day," . PENALTY_GRACE . ",inv_due_date) < '" . $bill_date . "'
                                  AND DATEDIFF(day,inv_due_date,'". $bill_date ."') > 0 ");

                        if($unpaids->num_rows() > 0){
                            foreach($unpaids->result() as $iv){
                                $nDays = $iv->diff_date;
                                $interestTotal = 0;

                                $invID = $iv->inv_id;
                                $invCode = $iv->inv_no;
                                $baseInvAmount = ($iv->unpaid_grand);

                                $dueDate = DateTime::createFromFormat('Y-m-d', ymd_from_db($iv->inv_due_date));
                                $invDueDate = $dueDate->format('Y-m-d');
                                $maxPaymentDate = $dueDate->modify('+' . PENALTY_GRACE . ' day');

                                if($bill_date > $maxPaymentDate->format('Y-m-d')){
                                    $penaltyStartDate = $invDueDate;

                                    //Check previous penalty from temp
                                    $prevInterestEnd = '';
                                    foreach($penalties as $p){
                                        if($p['inv_id'] == $invID){
                                            if($p['interest_end'] > $penaltyStartDate){
                                                $prevInterestEnd = $p['interest_end'];
                                            }
                                        }
                                    }

                                    if($prevInterestEnd == '') {
                                        //Check Last Period Penalty
                                        $prevDBPenalty = $this->db->query(@"SELECT * FROM ar_interestcharge WHERE inv_id = " . $invID . " AND status = " . STATUS_NEW . " ORDER BY interest_end DESC");
                                        if ($prevDBPenalty->num_rows() > 0) {
                                            $prevDBPenalty = $prevDBPenalty->row();
                                            $prevInterestEnd = ymd_from_db($prevDBPenalty->interest_end);
                                        }
                                    }

                                    //Set Interest Start from previous penalty end
                                    if($prevInterestEnd != ''){
                                        $prevInterestEnd = DateTime::createFromFormat('Y-m-d', $prevInterestEnd);
                                        $prevInterestEnd = $prevInterestEnd->modify("+1 day");

                                        $penaltyStartDate = $prevInterestEnd->format('Y-m-d');
                                    }

                                    //Days of interest
                                    $nDays = num_of_days($penaltyStartDate, $bill_date);

                                    if($nDays > 0){
                                        $interestTotal = ($interestRate / 100) * ($baseInvAmount) * $nDays;
                                    }

                                    //$result['debug'] .= '000 ...' . $interestTotal;
                                    if($interestTotal > 0){
                                        $interestTotal = round($interestTotal,0);

                                        $pos = -1;
                                        $found = false;
                                        foreach($penalties as $p){
                                            $pos++;
                                            if($p['inv_id'] == $invID && $p['interest_end'] == $bill_date){
                                                $penalties[$pos]['interest_amount'] = $interestTotal;
                                                $penalties[$pos]['interest_pendingamount'] = $baseInvAmount;
                                                $found = true;
                                                break;
                                            }
                                        }

                                        if(!$found){
                                            $createdDate = date('Y-m-d H:i:s');
                                            $createdBy = my_sess('user_id');
                                            $desc = $trxPenalty->transtype_desc . ' - ' . $invCode;

                                            $newPenalty = array();
                                            $newPenalty['inv_id'] = $invID;
                                            $newPenalty['billing_date'] = $bill_date;
                                            $newPenalty['transtype_id'] = $trxPenalty->transtype_id;
                                            $newPenalty['interest_start'] = $penaltyStartDate;
                                            $newPenalty['interest_end'] = $bill_date;
                                            $newPenalty['interest_days'] = $nDays;
                                            $newPenalty['interest_percent'] = $interestRate;
                                            $newPenalty['interest_pendingamount'] = $baseInvAmount;
                                            $newPenalty['interest_desc'] = $desc;
                                            $newPenalty['interest_amount'] = $interestTotal;
                                            $newPenalty['is_monthly'] = 1;
                                            $newPenalty['created_date'] = $createdDate;
                                            $newPenalty['created_by'] = $createdBy;
                                            $newPenalty['status'] = STATUS_NEW;

                                            array_push($penalties,$newPenalty);
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $valid = false;
                        $msg_warning = 'Penalty rate in [TRX_TYPE] ' . $trxPenalty->transtype_name . ' must not 0 !';
                    }
                }

                //$result['debug'] .= 'AAA ...' . ($valid ? ' true' : 'false');
                if(count($penalties) > 0 && $valid){
                    //$result['debug'] .= $nDays . ' -> ' . $penaltyStartDate;

                    //BEGIN TRANSACTION
                    $this->db->trans_begin();

                    foreach($penalties as $data){
                        if($valid) {
                            $exist = $this->mdl_general->count('ar_interestcharge',array('inv_id' => $data['inv_id'], 'interest_end' => $data['interest_end'], 'is_monthly' => $data['is_monthly']));

                            if($exist <= 0) {
                                $this->db->insert('ar_interestcharge', $data);
                                $interestcharge_id = $this->db->insert_id();
                                if ($interestcharge_id <= 0) {
                                    $valid = false;
                                    break;
                                }

                                //INSERT cs_corporate_bill -> FLAG_PENALTY
                                $joins = array('cs_reservation_header' => 'cs_reservation_header.reservation_id = iv.reservation_id',
                                               'cs_reservation_unit' => 'cs_reservation_unit.reservation_id = cs_reservation_header.reservation_id');
                                $invoice = $this->mdl_finance->getJoin('iv.*,ISNULL(cs_reservation_header.tenant_id,0) as tenant_id, ISNULL(cs_reservation_unit.unit_id,0) as unit_id'
            ,'ar_invoice_header as iv', $joins, array('inv_id' => $data['inv_id']));

                                if($invoice->num_rows() > 0){
                                    $iv = $invoice->row();

                                    $bill = array();
                                    $bill['reservation_id'] = $iv->reservation_id;

                                    if($iv->company_id > 0){
                                        $bill['company_id'] = $iv->company_id;
                                        $bill['tenant_id'] = 0;
                                    }else{
                                        $bill['company_id'] = 0;
                                        $bill['tenant_id'] = $iv->tenant_id;
                                    }

                                    $bill['billdetail_id'] = $interestcharge_id;
                                    $bill['unit_id'] = $iv->unit_id;
                                    $bill['transtype_id'] = $data['transtype_id'];
                                    $bill['bill_startdate'] = $data['interest_end'];
                                    $bill['bill_enddate'] = $data['interest_end'];

                                    $discountPerMonth = 0;
                                    $taxPerMonth = 0;
                                    $tax = tax_vat();
                                    if($tax['taxtype_percent'] > 0){
                                        $taxPerMonth = round($data['interest_amount'] * ($tax['taxtype_percent']/100),0);
                                    }

                                    $bill['amount'] = $data['interest_amount'];
                                    $bill['discount'] = $discountPerMonth;
                                    $bill['tax'] = $taxPerMonth;
                                    $bill['total_amount'] = round($bill['amount'] - $discountPerMonth + $taxPerMonth,0);

                                    $bill['is_billed'] = 0;
                                    $bill['is_othercharge'] = FLAG_PENALTY;
                                    $bill['status'] = STATUS_NEW;
                                    $bill['month'] = 0;

                                    $this->db->insert('cs_corporate_bill', $bill);
                                    $insertID = $this->db->insert_id();

                                    if ($insertID <= 0) {
                                        $valid = false;
                                        break;
                                    }
                                }

                            }
                        }else{
                            break;
                        }
                    }

                    //COMMIT OR ROLLBACK
                    if($valid){
                        if ($this->db->trans_status() === FALSE)
                        {
                            $this->db->trans_rollback();

                            $result['type'] = '0';
                            $result['message'] = 'Transaction can not be submitted. Please try again later.';
                        }
                        else
                        {
                            $this->db->trans_commit();

                            $result['type'] = '1';
                            $result['message'] = 'Late Penalties successfully submitted.';
                            //$result['redirect_link'] = base_url('ar/corporate_bill/interest_manage.tpd');
                        }
                    }else{
                        $this->db->trans_rollback();

                        $result['type'] = '0';
                        $result['message'] = 'Transaction can not be submitted. Please try again later.';
                    }
                }else{
                    if($msg_warning != ''){
                        $result['type'] = '0';
                        $result['message'] = $msg_warning;
                    }else{
                        $result['type'] = '1';
                        $result['message'] = 'No penalties found !';
                        //$result['redirect_link'] = base_url('ar/corporate_bill/interest_manage.tpd');
                    }
                }
            }else{
                $result['type'] = '0';
                $result['message'] = 'Posting parameter for Interest of late penalty not found !';
            }
        }

        echo json_encode($result);
    }

    public function xcancel_penalty(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        $interestChargeId = isset($_POST['interestcharge_id']) ? $_POST['interestcharge_id'] : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : 0;

        if($interestChargeId > 0 && $status > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $this->db->delete('cs_corporate_bill', array('billdetail_id' => $interestChargeId, 'is_billed <=' => 0,'is_othercharge' => FLAG_PENALTY));

            if($status == STATUS_CANCEL){
                $this->db->delete('ar_interestcharge', array('interestcharge_id' => $interestChargeId));
                $msg_warning = 'Penalty has been canceled.';
            }else if($status == STATUS_DELETE){
                $this->mdl_general->update('ar_interestcharge', array('interestcharge_id' => $interestChargeId), array('status' => STATUS_DELETE));
                $msg_warning = 'Current period penalty has been removed.';
            }

            //COMMIT OR ROLLBACK
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Penalty can not be canceled. Please try again later !';

                //$this->session->set_flashdata('flash_message_class', 'danger');
                //$this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }
            else
            {
                $this->db->trans_commit();

                $result['type'] = '1';
                $result['message'] = $msg_warning;

                //$this->session->set_flashdata('flash_message_class', 'success');
                //$this->session->set_flashdata('flash_message', 'Invoice successfully created.');
            }
        }

        echo json_encode($result);
    }

    #endregion

    #region Other Charge

    public function other_charge($type = 1, $id = 0){
        if ($type == 1 || $type == 2) {
            $this->load->model('finance/mdl_finance');

            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $data['is_history'] = ($type == 2 ? true : false);
            $this->load->view('layout/header', $data_header);
            $this->load->view('ar/charge/bill_manage.php', $data);
            $this->load->view('layout/footer');
        }else{
            $this->load->library('../controllers/frontdesk/billing');
            $this->billing->bill_form($id);
        }
    }

    public function xother_charge_add(){
        $result = '';

        if(isset($_POST)){
            $options = '<option value="0">-Select-</option>';

            $item_ids = isset($_POST['item_id']) ? $_POST['item_id'] : array();
            $item_exception = '';

            $i = count($item_ids);
            if($i > 0){
                $exc_item = array();
                foreach ($_POST['item_id'] as $key => $val) {
                    $status = $_POST['status'][$key];
                    if($status == STATUS_NEW){
                        array_push($exc_item,$_POST['item_id'][$key]);
                    }
                }
                if(count($exc_item) > 0){
                    $item_exception = ' AND itemstock_id NOT IN(' . implode(',',$exc_item) . ') ';
                }
            }
            //$qry_item = $this->mdl_general->get('get_pos_item_active', array('is_package <=' => 0), array(), 'item_desc');
            $qry_item = $this->db->query('SELECT * FROM get_pos_item_active WHERE is_package <= 0 ' . $item_exception . ' ORDER BY item_desc');
            foreach($qry_item->result() as $row_item) {
                $options .= '<option value="' . $row_item->itemstock_id . '" item-desc="' . $row_item->item_desc . '" data-price-lock="' . $row_item->price_lock . '" data-unit-price="' . $row_item->unit_price . '" data-unit-discount="' . $row_item->unit_discount . '" tax-type-percent="' . $row_item->taxtype_percent . '" tax-type-code="' . $row_item->taxtype_code . '"  is-service="' . $row_item->is_service_item . '" unit-max-qty="' . $row_item->itemstock_current_qty . '">' . $row_item->item_code . '</option>';
            }

            $result = '<tr>' .
                    '<input type="hidden" name="billdetail_id[' . $i . ']" value="0">' .
                    '<input type="hidden" name="status[' . $i . ']" value="1" class="class_status">' .
                    '<td><div class="input-group">
                    <input type="hidden" name="item_id[' . $i . ']" value="">
                    <input type="text" name="item_code[' . $i . ']" class="form-control input-sm text-center" value="" readonly />' .
                    '<span class="input-group-btn">' .
                    '<button class="btn btn-sm green-haze input-sm select_item " unique-id="" data-index="' . $i . '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' .
                    '</span>' .
                    '</div></td>' .
                    '<td><i id="icon_row_' . $i . '" class="fa " style="padding-top:5px;"></i></td>' .
                    '<td><textarea name="item_desc[' . $i . ']" rows="2" class="form-control" style="resize: vertical;font-size: 11px;"></textarea></td>' .
                    '<td ><input type="text" name="item_qty[' . $i . ']" data-index="' . $i . '" value="0" class="form-control text-right input-sm mask_currency num_cal" ></td>' .
                    '<td ><input type="text" name="item_rate[' . $i . ']" data-index="' . $i . '" value="0" class="form-control text-right input-sm mask_currency num_cal" ></td>' .
                    '<td ><input type="text" name="item_amount[' . $i . ']" data-index="' . $i . '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' .
                    '<td ><input type="text" name="tax_amount[' . $i . ']" data-index="' . $i . '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' .
                    '<td ><input type="text" name="subtotal_amount[' . $i . ']" data-index="' . $i . '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' .
                    '<td style="vertical-align:top;padding-top:7px;">' .
                    '<a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' . $i . '" style="margin-right:0px;"><i class="fa fa-times"></i></a></td>' .
                    '</tr>';
        }

        echo $result;
    }

    #endregion

    #region Pending Bills

    public function bill_manage($type = 1){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/util.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/bill/corporate_bill_manage.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_bill_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['view_ar_corporate_bill.status'] = STATUS_NEW;
        $where['view_ar_corporate_bill.is_billed <= '] = 0;

        $like = array();

        $where['CONVERT(date,view_ar_corporate_bill.bill_startdate) <='] = date('Y-m-d');
        if(isset($_REQUEST['filter_date'])){
            if($_REQUEST['filter_date'] != ''){
                $where['CONVERT(date,view_ar_corporate_bill.bill_startdate) <='] = dmy_to_ymd($_REQUEST['filter_date']);
            }
        }

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['view_ar_corporate_bill.bill_no'] = $_REQUEST['filter_no'];
            }
        }

        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,view_ar_corporate_bill.bill_startdate) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_ar_corporate_bill.company_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_subject'])){
            if($_REQUEST['filter_subject'] != ''){
                $like['view_ar_corporate_bill.subject'] = $_REQUEST['filter_subject'];
            }
        }

        $joins = array(); //array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_debitnote_header.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin('view_ar_corporate_bill', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'view_ar_corporate_bill.bill_startdate ASC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'view_ar_corporate_bill.bill_startdate ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'view_ar_corporate_bill.reservation_code ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_ar_corporate_bill.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'view_ar_corporate_bill.subject ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('view_ar_corporate_bill.*'
            ,'view_ar_corporate_bill', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                '<input type="checkbox" value="' . $row->corporatebill_id . '" name="ischecked[]" />',
                dmy_from_db($row->bill_startdate),
                //$row->bill_no,
                $row->company_name,
                nl2br($row->subject),
                format_num($row->amount,0),
                format_num($row->tax,0),
                format_num($row->total_amount,0),
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function ajax_submit_bill(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';
        $result['debug'] = '';

        $valid = true;

        if(isset($_POST['ischecked'])){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $data['is_billed'] = 1;
            foreach( $_POST['ischecked'] as $bill_id){
                $this->mdl_general->update('cs_corporate_bill', array('corporatebill_id' => $bill_id, 'is_billed <='=> 0), $data);

                //Update Interest Charge if any
                $bill_detail = $this->db->get_where('cs_corporate_bill', array('corporatebill_id' => $bill_id));
                if($bill_detail->num_rows() > 0){
                    $bill_detail = $bill_detail->row();
                    if($bill_detail->is_othercharge == FLAG_PENALTY){
                        $this->mdl_general->update('ar_interestcharge', array('interestcharge_id' => $bill_detail->billdetail_id), array('status' => STATUS_CLOSED));
                        //$this->db->query('UPDATE ar_interestcharge SET status = ' . STATUS_CLOSED . ' WHERE interestcharge_id = ' . $bill_detail->billdetail_id);

                    }
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be submitted. Please try again later.';

                }
                else
                {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = 'Bills successfully submitted.';
                    $result['redirect_link'] = base_url('ar/corporate_bill/bill_manage.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be submitted. Please try again later.';
            }
        }

        echo json_encode($result);
    }

    #endregion

    #region Maintenance (is_billed > 0)

    public function bill_maintenance($type = 1){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/moment.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/util.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/bill/corporate_bill_maintain.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_bill_maintenance($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        //$where['view_ar_corporate_bill.status'] = STATUS_NEW;
        //$where['view_ar_corporate_bill.is_billed > '] = 0;

        $like = array();

        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,view_corporate_maintenance.bill_startdate) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,view_corporate_maintenance.bill_startdate) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['view_corporate_maintenance.company_name'] = $_REQUEST['filter_name'];
            }
        }

        if(isset($_REQUEST['filter_subject'])){
            if($_REQUEST['filter_subject'] != ''){
                $like['view_corporate_maintenance.item_remark'] = $_REQUEST['filter_subject'];
            }
        }

        $joins = array(); //array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_debitnote_header.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin('view_corporate_maintenance', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'view_corporate_maintenance.company_name ASC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'view_corporate_maintenance.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'view_corporate_maintenance.bill_startdate ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'view_corporate_maintenance.item_remark ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('view_corporate_maintenance.*'
            ,'view_corporate_maintenance', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<a data-original-title="Remove" class="btn btn-xs red-thunderbird tooltips btn-remove" href="javascript:;" data-bill-id="' . $row->corporatebill_id . '"><i class="fa fa-remove"></i></a>';
            //$btn_action .= '<li> <a href="javascript:;" class="btn-receipt bold" data-id="' . $row->reservation_id . '" data-min-amount="' . 6000 . '" data-full-amount="' . $row->pending_amount . '">' . 'RECEIPT' . '</a> </li>';

            $records["data"][] = array(
                $i,
                $row->company_name,
                //$row->bill_no,
                dmy_from_db($row->bill_startdate),
                nl2br($row->item_remark),
                $row->transtype_name,
                format_num($row->amount,0),
                format_num($row->tax,0),
                format_num($row->total_amount,0),
                $btn_action
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function rollback_maintenance_bill(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        $corporateBillId = isset($_POST['bill_id']) ? $_POST['bill_id'] : 0;

        if($corporateBillId > 0){
            $this->mdl_general->update('cs_corporate_bill', array('corporatebill_id' => $corporateBillId), array('status' => STATUS_NEW , 'is_billed' => 0));

            //Update Interest Charge if any
            $bill_detail = $this->db->get_where('cs_corporate_bill', array('corporatebill_id' => $corporateBillId));
            if($bill_detail->num_rows() > 0){
                $bill_detail = $bill_detail->row();
                if($bill_detail->is_othercharge == FLAG_PENALTY){
                    @$this->mdl_general->update('ar_interestcharge', array('interestcharge_id' => $bill_detail->billdetail_id), array('status' => STATUS_NEW));

                }
            }

            $result['type'] = '1';
            $result['message'] = 'Bill has been canceled and moved to Pending Bill(s).';
        }

        echo json_encode($result);
    }

    public function create_invoices(){
        $valid = true;

        if(isset($_POST)){
            $inv_date = dmy_to_ymd($_POST['inv_date']);
            $inv_due_date = dmy_to_ymd($_POST['inv_due_date']);
            $server_date = date('Y-m-d H:i:s');

            if($inv_date != '' && $inv_due_date != ''){
                //BEGIN TRANSACTION
                $this->db->trans_begin();

                //CORPORATE
                $companies = array();
                $qry = $this->db->query('select company_id from view_corporate_maintenance where company_id > 0 GROUP BY company_id');
                if($qry->num_rows() > 0){
                    foreach($qry->result() as $row){
                        array_push($companies,$row->company_id);
                    }
                }

                $specPenalty = FNSpec::get(FNSpec::INTEREST);
                $penaltyCOAID = $specPenalty['coa_id'];
                if($penaltyCOAID <= 0){
                    $trxPenalty = $this->db->get_where('ms_transtype', array('transtype_id' => $specPenalty['transtype_id']));
                    if($trxPenalty->num_rows() > 0){
                        $penaltyCOAID = $trxPenalty->coa_id;
                    }
                }

                if(count($companies) > 0){
                    foreach($companies as $company_id){
                        if($valid){
                            //Header
                            $header['inv_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_INVOICE, $inv_date);
                            if($header['inv_no'] != ''){
                                $header['inv_date'] = $inv_date;
                                $header['inv_due_date'] = $inv_due_date;
                                $header['company_id'] = $company_id;
                                $header['reservation_id'] = 0;
                                $header['is_full_paid'] = 0;

                                //Detail
                                $totalAmount= 0;
                                $totalTax = 0;

                                $details = array();
                                $bills = $this->db->order_by('is_othercharge', 'ASC')->get_where('view_corporate_maintenance',array('company_id' => $company_id));
                                if($bills->num_rows() > 0){
                                    foreach($bills->result() as $bill){
                                        $coa_id = $bill->coa_id;

                                        if($bill->is_othercharge > 0){
                                            if($bill->is_othercharge == FLAG_PENALTY){
                                                $coa_id = $penaltyCOAID;

                                                $interest = $this->db->get_where('ar_interestcharge',array('interestcharge_id' => $bill->billdetail_id));
                                                $remark = 'Interest Charge';
                                                if($interest->num_rows() > 0){
                                                    $interest = $interest->row();
                                                    $remark = $bill->transtype_desc . ' - ' . $interest->interest_desc;
                                                }

                                            }else {
                                                //$remark = $bill->transtype_desc . ' - ' . $remark = $bill->item_remark;
                                                $remark = $remark = $bill->item_remark;
                                            }
                                        }else{
                                            $remark = $bill->transtype_desc . ' - ' . $bill->company_name . ' (' . date('d/m/y', strtotime(dmy_from_db($bill->bill_startdate))) . ' - ' . date('d/m/y', strtotime(dmy_from_db($bill->bill_enddate))) . ')';

                                        }

                                        if($coa_id > 0){
                                            $totalAmount += round($bill->amount - $bill->discount,0);
                                            $totalTax += round($bill->tax,0);

                                            array_push($details, array('bill_id' => $bill->corporatebill_id,
                                                'description' => $remark,
                                                'amount' => round($bill->amount - $bill->discount,0), 'tax' => round($bill->tax,0),
                                                'discount' => $bill->discount,
                                                'transtype_id' => $bill->transtype_id,
                                                'paid_amount' => 0, 'paid_tax' => 0,
                                                'coa_id' => $coa_id,
                                                'status' => STATUS_NEW)
                                            );
                                        }else{
                                            $valid = false;
                                            break;
                                        }
                                    }
                                }

                                $header['total_amount'] = round($totalAmount,0);
                                $header['total_tax'] = round($totalTax,0);
                                $header['total_grand'] = ($header['total_amount'] + $header['total_tax']);
                                $header['created_by'] = my_sess('user_id');
                                $header['created_date'] = $server_date;
                                $header['status'] = STATUS_NEW;

                                if(count($details) > 0 && $valid){
                                    $this->db->insert('ar_invoice_header', $header);
                                    $inv_id = $this->db->insert_id();

                                    if($inv_id > 0){
                                        foreach($details as $detail){
                                            $detail['inv_id'] = $inv_id;

                                            $this->db->insert('ar_invoice_detail', $detail);
                                            $detail_id = $this->db->insert_id();
                                        }
                                    }else{
                                        $valid = false;
                                        break;
                                    }
                                }else{
                                    $valid = false;
                                    break;
                                }
                            }else{
                                $valid = false;
                                break;
                            }
                        }else{
                            break;
                        }
                    }
                }
                //END CORPORATE

                //COMMIT OR ROLLBACK
                if($valid){
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
                        $this->session->set_flashdata('flash_message', 'Invoice successfully created.');
                    }
                }else{
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                }
            }

            //FINALIZE
            if(!$valid){
                redirect(base_url('ar/corporate_bill/bill_maintenance.tpd'));
            }
            else {
                redirect(base_url('ar/corporate_bill/bill_maintenance.tpd'));
            }
        }
    }

    #endregion

    #region Edit Listing

    public function edit_listing($type = 1){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/moment.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/util.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/bill/edit_listing.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_edit_listing($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        //$where['ar_invoice_header.status'] = STATUS_NEW;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['inv.inv_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,inv.inv_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,inv.inv_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['inv.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array(); //array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_debitnote_header.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin('fxnARInvoiceHeaderByStatus(' . STATUS_NEW . ') as inv ', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'inv.inv_no ASC, inv.inv_date ASC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'inv.inv_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'inv.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'inv.inv_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'inv.inv_due_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'inv.total_amount ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'inv.total_tax ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 7){
                $order = 'inv.total_grand ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('inv.*'
            ,'fxnARInvoiceHeaderByStatus(' . STATUS_NEW . ') as inv', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/pdf_invoice/' . $row->inv_id) . '.tpd" target="_blank">Open</a> </li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL) || check_session_action($menu_id, STATUS_DELETE)){
                    $btn_action .= '<li><a data-original-title="Cancel" class="btn-cancel" href="javascript:;" data-inv-id="' . $row->inv_id . '" >' . get_action_name(STATUS_CANCEL, false) . '</a></li>';
                }
            }

            $records["data"][] = array(
                '<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->inv_no,
                $row->company_name,
                dmy_from_db($row->inv_date),
                dmy_from_db($row->inv_due_date),
                format_num($row->total_amount,0),
                format_num($row->total_tax,0),
                format_num($row->total_grand,0),
                '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function xcancel_edit_listing(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        $invId = isset($_POST['inv_id']) ? $_POST['inv_id'] : 0;

        if($invId > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $inv_details = $this->db->get_where('ar_invoice_detail', array('inv_id' => $invId));
            if($inv_details->num_rows() > 0){
                foreach($inv_details->result_array() as $detail){
                    if($detail['bill_id'] > 0) {
                        $this->mdl_general->update('cs_corporate_bill', array('corporatebill_id' => $detail['bill_id']), array('is_billed' => 0));

                        //Update Interest Charge if any
                        $bill_detail = $this->db->get_where('cs_corporate_bill', array('corporatebill_id' => $detail['bill_id']));
                        if($bill_detail->num_rows() > 0){
                            $bill_detail = $bill_detail->row();
                            if($bill_detail->is_othercharge == FLAG_PENALTY){
                                @$this->mdl_general->update('ar_interestcharge', array('interestcharge_id' => $bill_detail->billdetail_id), array('status' => STATUS_NEW));

                            }
                        }
                    }
                }
            }

            $this->db->delete('ar_invoice_detail', array('inv_id' => $invId));
            $this->db->delete('ar_invoice_header', array('inv_id' => $invId));

            //COMMIT OR ROLLBACK
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Invoice can not be canceled. Please try again later !';

                //$this->session->set_flashdata('flash_message_class', 'danger');
                //$this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }
            else
            {
                $this->db->trans_commit();

                $result['type'] = '1';
                $result['message'] = 'Invoice has been canceled.';

                //$this->session->set_flashdata('flash_message_class', 'success');
                //$this->session->set_flashdata('flash_message', 'Invoice successfully created.');
            }
        }

        echo json_encode($result);
    }

    public function xpost_edit_listing(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;

        if(isset($_POST['ischecked'])){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            foreach( $_POST['ischecked'] as $inv_id){
                if($valid){
                    $valid = $this->posting_invoice($inv_id);
                }else{
                    break;
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be submitted. Please try again later.';

                }
                else
                {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = 'Corporate Invoice successfully posted.';
                    $result['redirect_link'] = base_url('ar/corporate_bill/bill_manage.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be submitted. Please try again later.';
            }
        }

        echo json_encode($result);
    }

    private function posting_invoice($inv_id){
        $valid = true;

        if($inv_id > 0){
            $inv = $this->db->query('SELECT * FROM fxnARInvoiceHeaderByStatus(' . STATUS_NEW . ') WHERE inv_id = ' . $inv_id);
            if($inv->num_rows() > 0){
                $invoice = $inv->row_array();

                //$fnAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                if($invoice['company_id'] > 0){
                    $fnAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                }else {
                    $fnAR = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                }

                if($fnAR['coa_id'] > 0){
                    //----Main ----
                    //AR Corporate    8.706
                    //  - Sales Room        7.000
                    //  - Sales Other       1.000
                    //  - VAT                 700
                    //  - Stamp Duty            6

                    $detail = array();

                    $totalDebit = $invoice['total_grand'];
                    $totalCredit = 0;

                    $taxVAT = tax_vat();

                    if($valid){
                        //AR
                        $detAR = array();
                        $detAR['coa_id'] = $fnAR['coa_id'];
                        $detAR['dept_id'] = 0;
                        $detAR['journal_note'] = $invoice['inv_no'] . ' - ' . $invoice['company_name'];
                        $detAR['journal_debit'] = $totalDebit;
                        $detAR['journal_credit'] = 0;
                        $detAR['reference_id'] = 0;
                        $detAR['transtype_id'] = 0;

                        array_push($detail, $detAR);

                    }else{
                        $valid = false;
                    }

                    $reverseAmount = 0;
                    $details = $this->db->get_where('fxnARInvoiceDetailByInvID(' . $inv_id . ')');
                    foreach($details->result_array() as $det){
                        if($valid){
                            if($det['coa_id'] > 0) {
                                $desc = $det['description'];

                                //Amount
                                $detAmount = array();
                                $detAmount['coa_id'] = $det['coa_id'];
                                $detAmount['dept_id'] = 0;
                                $detAmount['journal_note'] = $desc;
                                $detAmount['journal_debit'] = 0;
                                $detAmount['journal_credit'] = $det['amount'];
                                $detAmount['reference_id'] = 0;
                                $detAmount['transtype_id'] = 0;

                                array_push($detail, $detAmount);

                                //Tax
                                if ($det['tax'] > 0) {
                                    if (isset($taxVAT['coa_id'])) {
                                        $detTax = array();
                                        $detTax['coa_id'] = $taxVAT['coa_id'];
                                        $detTax['dept_id'] = 0;
                                        $detTax['journal_note'] = 'VAT ' . $desc;
                                        $detTax['journal_debit'] = 0;
                                        $detTax['journal_credit'] = $det['tax'];
                                        $detTax['reference_id'] = 0;
                                        $detTax['transtype_id'] = 0;

                                        array_push($detail, $detTax);
                                    } else {
                                        $valid = false;
                                        break;
                                    }
                                }

                                //$totalCredit += ($det['amount'] + $det['tax']);

                                //Add Room Charge to reverse list
                                if ($det['is_othercharge'] <= 0) {
                                    $reverseAmount += round($det['amount'] + $det['tax'], 0);
                                }
                            }else{
                                break;
                            }
                        }else{
                            break;
                        }
                    }

                    if($valid){
                        //Posting supplies if ANY
                        $detail = $this->posting_item_supplies_only($inv_id, $detail);

                        $totalDebit = 0;
                        $totalCredit = 0;

                        foreach($detail as $ivd){
                            $totalDebit += $ivd['journal_debit'];
                            $totalCredit += $ivd['journal_credit'];
                        }
                    }

                    if($valid && $totalDebit == $totalCredit){
                        $header = array();
                        $header['journal_no'] = $invoice['inv_no'];
                        $header['journal_date'] = $invoice['inv_date'];
                        $header['journal_remarks'] = $invoice['inv_no'] . ' - ' . $invoice['company_name'];
                        $header['modul'] = GLMOD::GL_MOD_AR;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = '';

                        $valid = $this->mdl_finance->postJournal($header,$detail);

                        //echo 'POSTING ' . $totalDebit . ' -> ' . $valid;
                    }

                    if($valid){
                        $data['status']= STATUS_POSTED;
                        $this->mdl_general->update('ar_invoice_header', array('inv_id' => $inv_id), $data);
                    }
                }else{
                    $valid = false;
                }
            }else{
                $valid = false;
            }
        }else{
            $valid = false;
        }

        return $valid;
    }

    private function posting_item_supplies_only($inv_id = 0, $detail = array()){
        if($inv_id > 0 && isset($detail)) {
            $supExpense = FNSpec::get(FNSpec::POS_EXPENSE);
            $supplies = FNSpec::get(FNSpec::POS_SUPPLIES);

            $billdetails = $this->db->query('SELECT cs_bill_header.journal_no,cs_bill_header.bill_date, pos_item_stock.itemstock_id, cs_bill_detail.rate,cs_bill_detail.disc_amount, cs_bill_detail.item_qty, pos_item_stock.itemstock_current_qty, pos_item_stock.base_avg_price
                FROM cs_bill_detail
                JOIN cs_corporate_bill ON cs_corporate_bill.billdetail_id = cs_bill_detail.billdetail_id
                JOIN ar_invoice_detail ON ar_invoice_detail.bill_id = cs_corporate_bill.corporatebill_id
                JOIN cs_bill_header ON cs_bill_header.bill_id = cs_bill_detail.bill_id
                JOIN pos_item_stock ON pos_item_stock.itemstock_id = cs_bill_detail.item_id
                WHERE pos_item_stock.is_service_item <= 0 AND ar_invoice_detail.inv_id = ' . $inv_id);

            if ($billdetails->num_rows() > 0) {
                $totalExpense = 0;

                $journalNo = '';
                $billDate = date('Y-m-d H:i:s');
                foreach ($billdetails->result() as $det) {
                    $journalNo = $det->journal_no;
                    $billDate = ymd_from_db($det->bill_date);
                    //Req by budi on 22 April 16
                    //$totalExpense += round(($det->rate - $det->disc_amount) * $det->item_qty, 0);
                    $totalExpense += $det->base_avg_price * $det->item_qty;
                }

                if ($totalExpense > 0) {
                    if ($supExpense['coa_id'] > 0) {
                        $rowdet = array();
                        $rowdet['coa_id'] = $supExpense['coa_id'];
                        $rowdet['dept_id'] = 0;
                        $rowdet['journal_note'] = $journalNo;
                        $rowdet['journal_debit'] = $totalExpense;
                        $rowdet['journal_credit'] = 0;
                        $rowdet['reference_id'] = 0;
                        $rowdet['transtype_id'] = $supExpense['transtype_id'];

                        array_push($detail, $rowdet);
                    }

                    if ($supplies['coa_id'] > 0) {
                        $rowdet = array();
                        $rowdet['coa_id'] = $supplies['coa_id'];
                        $rowdet['dept_id'] = 0;
                        $rowdet['journal_note'] = $journalNo;
                        $rowdet['journal_debit'] = 0;
                        $rowdet['journal_credit'] = $totalExpense;
                        $rowdet['reference_id'] = 0;
                        $rowdet['transtype_id'] = $supplies['transtype_id'];

                        array_push($detail, $rowdet);

                    }

                }
            }
        }

        return $detail;

    }

    #endregion

    #region Invoice

    public function invoice($type = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/moment.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/util.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        if($type == 1){
            $this->load->view('ar/bill/invoice_history.php', $data);
        }else{
            $this->load->view('ar/bill/invoice_unpaid.php', $data);
        }

        $this->load->view('layout/footer');
    }

    public function get_invoice_unpaid_list($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $STATUS = STATUS_POSTED;

        $where = array();
        $where['inv.unpaid_grand >'] = 0;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['inv.inv_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,inv.inv_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,inv.inv_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['inv.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array(); //array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_debitnote_header.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin('fxnARInvoiceHeaderByStatus(' . $STATUS . ') as inv ', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'inv.inv_no DESC, inv.inv_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'inv.inv_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'inv.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'inv.inv_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'inv.total_amount ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'inv.total_tax ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'inv.total_grand ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 7){
                $order = 'inv.unpaid_grand ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('inv.*'
            ,'fxnARInvoiceHeaderByStatus(' . $STATUS . ') as inv', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/pdf_invoice/' . $row->inv_id) . '.tpd" target="_blank">Invoice</a> </li>';
            $btn_action .= '<li > <a href="' . base_url('ar/corporate_bill/pdf_invoice/' . $row->inv_id) . '/1.tpd" target="_blank" class="">Unpaid</a> </li>';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/pdf_invoice/' . $row->inv_id) . '/0/A4/portrait.tpd" target="_blank">Invoice A4</a> </li>';

            $records["data"][] = array(
                $i, //'<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->inv_no,
                $row->company_name,
                dmy_from_db($row->inv_date),
                dmy_from_db($row->inv_due_date),
                format_num($row->total_amount,0),
                format_num($row->total_tax,0),
                format_num($row->total_grand,0),
                '<span class="font-red">' . format_num($row->unpaid_grand,0) . '</span>',
                '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Print&nbsp;&nbsp;<i class="fa fa-print"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function get_invoice_history_list($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $STATUS = STATUS_CLOSED;

        $where = array();
        $where['inv.unpaid_grand <='] = 0;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['inv.inv_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,inv.inv_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,inv.inv_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['inv.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array(); //array('view_cs_reservation' => 'view_cs_reservation.reservation_id = ar_debitnote_header.reservation_id');
        $iTotalRecords = $this->mdl_finance->countJoin('fxnARInvoiceHeaderByStatus(' . $STATUS . ') as inv ', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'inv.inv_no DESC, inv.inv_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'inv.inv_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'inv.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'inv.inv_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'inv.total_amount ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'inv.total_tax ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 6){
                $order = 'inv.total_grand ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('inv.*'
            ,'fxnARInvoiceHeaderByStatus(' . $STATUS . ') as inv', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/pdf_invoice/' . $row->inv_id) . '.tpd" target="_blank">Open</a> </li>';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/pdf_invoice/' . $row->inv_id) . '/0/A4/portrait.tpd" target="_blank">Invoice A4</a> </li>';

            $records["data"][] = array(
                $i, //'<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->inv_no,
                $row->company_name,
                dmy_from_db($row->inv_date),
                dmy_from_db($row->inv_due_date),
                format_num($row->total_amount,0),
                format_num($row->total_tax,0),
                format_num($row->total_grand,0),
                0,
                '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    #endregion

    #region Official Receipt

    public function receipt($type = 0, $receipt_id = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/moment.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/util.js');

        $data = array();

        if($type == 1){
            $this->receipt_form($receipt_id);
        }else if($type == 2){
            $this->load->view('layout/header', $data_header);
            $this->load->view('ar/bill/receipt_history.php', $data);
            $this->load->view('layout/footer');
        }else {
            $this->load->view('layout/header', $data_header);
            $this->load->view('ar/bill/receipt_manage.php', $data);
            $this->load->view('layout/footer');
        }
    }

    public function get_receipt_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_receipt.status'] = STATUS_NEW;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_receipt.receipt_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_receipt.receipt_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_receipt.receipt_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['ar_receipt.company_name'] = $_REQUEST['filter_company'];
            }
        }
        if(isset($_REQUEST['filter_payment_type'])){
            if($_REQUEST['filter_payment_type'] != ''){
                $like['ar_receipt.paymenttype_id'] = $_REQUEST['filter_payment_type'];
            }
        }


        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_receipt as ar_receipt', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_receipt.receipt_no DESC, ar_receipt.receipt_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_receipt.receipt_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_receipt.receipt_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ar_receipt.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'ar_receipt.paymenttype_id ' . $_REQUEST['order'][0]['dir'];
            }

        }

        $qry = $this->mdl_finance->getJoin('ar_receipt.*'
            ,'vw_ar_receipt as ar_receipt', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/receipt/1/' . $row->receipt_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a></li>';

            $records["data"][] = array(
                $i, //'<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->receipt_no,
                dmy_from_db($row->receipt_date),
                $row->company_name,
                $row->paymenttype_desc,
                format_num($row->receipt_bankcharges + $row->receipt_paymentamount,0),
                get_status_name($row->status, true),
                '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function get_receipt_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_receipt.status'] = STATUS_POSTED;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar_receipt.receipt_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,ar_receipt.receipt_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,ar_receipt.receipt_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_company'])){
            if($_REQUEST['filter_company'] != ''){
                $like['ar_receipt.company_name'] = $_REQUEST['filter_company'];
            }
        }
        if(isset($_REQUEST['filter_payment_type'])){
            if($_REQUEST['filter_payment_type'] != ''){
                $like['ar_receipt.paymenttype_id'] = $_REQUEST['filter_payment_type'];
            }
        }


        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_receipt as ar_receipt', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_receipt.receipt_no DESC, ar_receipt.receipt_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar_receipt.receipt_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'ar_receipt.receipt_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ar_receipt.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'ar_receipt.paymenttype_id ' . $_REQUEST['order'][0]['dir'];
            }

        }

        $qry = $this->mdl_finance->getJoin('ar_receipt.*'
            ,'vw_ar_receipt as ar_receipt', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/receipt/1/' . $row->receipt_id) . '.tpd"><i class="fa fa-file"></i> Open</a> </li>';
            $btn_action .= '<li> <a href="' . base_url('ar/report/pdf_official_receipt/' . $row->receipt_no) . '.tpd" target="_blank"><i class="fa fa-print"></i> Receipt</a> </li>';
            //$btn_action .= '<li > <a href="' . base_url('ar/report/pdf_rv_voucher/' . $row->receipt_id) . '.tpd" target="_blank" ><i class="fa fa-print"></i> Voucher</a> </li>';

            $records["data"][] = array(
                $i, //'<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->receipt_no,
                dmy_from_db($row->receipt_date),
                $row->company_name,
                $row->paymenttype_desc,
                format_num($row->receipt_bankcharges + $row->receipt_paymentamount,0),
                get_status_name($row->status, true),
                '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            ' . $btn_action . '
					</ul>
				    </div>'
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function receipt_form($receipt_id = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['custom_script'], base_url() . 'assets/custom/util.js');

        $data = array();

        //HEADER
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        $data['receipt_id'] = $receipt_id;

        if($receipt_id > 0){
            $joins = array('ms_company' => 'ms_company.company_id = ar_receipt.company_id',
                           'ms_payment_type' => 'ms_payment_type.paymenttype_id = ar_receipt.paymenttype_id',
                           'fn_bank_account' => 'fn_bank_account.bankaccount_id = ar_receipt.bankaccount_id'
            );
            $qry = $this->mdl_finance->getJoin('ar_receipt.*, ms_company.company_name, ms_payment_type.paymenttype_code, ms_payment_type.paymenttype_desc, ms_payment_type.payment_type, fn_bank_account.bankaccount_code, fn_bank_account.bankaccount_desc'
                ,'ar_receipt', $joins, array('ar_receipt.receipt_id' => $receipt_id));
            $data['row'] = $qry->row();

            $pending_amount = 0;

            $company_pending = $this->db->get_where('view_ar_invoice_unpaid_sum', array('company_id' => $data['row']->company_id));
            if($company_pending->num_rows() > 0){
                $unposted_amount = 0;
                $existreceipt = $this->db->query('SELECT ISNULL(SUM(receipt_bankcharges + receipt_paymentamount),0) as receipt_total FROM ar_receipt
                                               WHERE company_id = ' . $data['row']->company_id . ' AND status = ' . STATUS_NEW . '
                                               AND receipt_id <> ' . $receipt_id . ' AND receipt_id < ' . $receipt_id);

                if($existreceipt->num_rows() > 0){
                    $unposted_amount = $existreceipt->row()->receipt_total;
                }

                $pending_amount = $company_pending->row()->sum_pending - $unposted_amount;
            }

            $data['pending_amount'] = $pending_amount;

            //Get Details
            if($data['row']->is_invoice > 0) {
                $totalAllocated = 0;
                $invoices = array();

                $rv_details = $this->db->query("SELECT ar_receipt_detail.*, ar_invoice_header.inv_no,ar_invoice_header.inv_date, ar_invoice_header.inv_due_date FROM ar_receipt_detail
                    JOIN ar_invoice_header ON ar_invoice_header.inv_id = ar_receipt_detail.inv_id
                    WHERE ar_receipt_detail.receipt_id = " . $receipt_id);
                if ($rv_details->num_rows() > 0) {
                    foreach ($rv_details->result_array() as $det) {
                        array_push($invoices, array('inv_id' => $det['inv_id'], 'inv_no' => $det['inv_no'], 'inv_date' => $det['inv_date'], 'inv_due_date' => $det['inv_due_date'], 'pending_amount' => $det['base_amount'], 'alloc_amount' => $det['receipt_amount'], 'checked' => 'checked'));

                        $totalAllocated += $det['receipt_amount'];
                    }
                }

                if($data['row']->status == STATUS_NEW) {
                    $where['bill.unpaid_grand >'] = 0;
                    if ($data['row']->company_id > 0) {
                        $where['bill.company_id'] = $data['row']->company_id;
                    }

                    $joins = array();
                    $qry = $this->mdl_finance->getJoin("bill.*", "fxnARInvoiceHeaderByStatus('" . STATUS_POSTED . "') AS bill", $joins, $where, array(), 'inv_no');
                    //echo $this->db->last_query();
                    if ($qry->num_rows() > 0) {
                        foreach ($qry->result_array() as $det) {
                            $existed = false;
                            foreach ($invoices as $inv) {
                                if ($inv['inv_id'] == $det['inv_id']) {
                                    $existed = true;
                                    break;
                                }
                            }

                            if (!$existed) {
                                array_push($invoices, array('inv_id' => $det['inv_id'], 'inv_no' => $det['inv_no'], 'inv_date' => $det['inv_date'], 'inv_due_date' => $det['inv_due_date'], 'pending_amount' => $det['unpaid_grand'], 'alloc_amount' => 0, 'checked' => ''));
                            }
                        }
                    }
                }

                if (count($invoices) > 0) {
                    $data['details'] = $invoices;
                }

                $data['alloc_total'] = $totalAllocated;
            }
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/bill/receipt_form', $data);
        $this->load->view('layout/footer');
    }

    public function submit_ar_receipt(){
        $valid = true;

        if(isset($_POST)){
            $receiptID = $_POST['receipt_id'];
            $receiptAmount = floatval($_POST['payment_amount']);

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $data['receipt_date'] = dmy_to_ymd($_POST['receipt_date']);
            $data['company_id'] = $_POST['company_id'];
            $data['reservation_id'] = 0;
            $data['tenant_id'] = 0;
            $data['deposit_id'] = 0;
            $data['is_invoice'] = $_POST['is_invoice'];
            $data['paymenttype_id'] = $_POST['paymenttype_id'];
            $data['bankaccount_id'] = $_POST['bankaccount_id'];
            $data['receipt_desc'] = $_POST['remark'];
            $data['receipt_bankcharges'] = 0;

            $data['card_name'] = isset($_POST['creditcard_name']) ? $_POST['creditcard_name'] : '';
            $data['card_no'] = isset($_POST['creditcard_no']) ? $_POST['creditcard_no'] : '';
            $data['card_expiry_month'] = isset($_POST['creditcard_expiry_month']) ? $_POST['creditcard_expiry_month'] : '';
            $data['card_expiry_year'] = isset($_POST['creditcard_expiry_year']) ? $_POST['creditcard_expiry_year'] : '';
            $data['card_bank'] = '';

            //OBTAIN PENDING AMOUNT
            $paymenttype = $this->db->query('SELECT * FROM ms_payment_type WHERE paymenttype_id = ' . $data['paymenttype_id']);
            if($paymenttype->num_rows() > 0){
                $paymenttype = $paymenttype->row_array();

                if($paymenttype['payment_type'] != PAYMENT_TYPE::AR_TRANSFER){
                    if($paymenttype['payment_type'] == PAYMENT_TYPE::CREDIT_CARD){
                        $ccard = $this->db->query('SELECT * FROM ms_payment_type WHERE payment_type = ' . PAYMENT_TYPE::CREDIT_CARD . ' AND status = ' . STATUS_NEW);

                        if($ccard->num_rows() > 0){
                            $card = $ccard->row();
                            $bankPercent = $card->card_percent;

                            if($paymenttype['payment_type'] == PAYMENT_TYPE::CREDIT_CARD){
                                if($bankPercent > 0){
                                    $data['bank_charge_percent'] = $bankPercent;
                                    $data['receipt_bankcharges'] = round($receiptAmount * ($bankPercent/100),0);
                                }
                            }else{
                                if($data['ccard_name'] == '' && $data['ccard_no']== ''){
                                    $data['card_expiry_month'] = 0;
                                    $data['card_expiry_year'] = 0;
                                }
                            }
                        }
                    }else{
                        $data['card_expiry_month'] = 0;
                        $data['card_expiry_year'] = 0;
                    }

                    //Find Bank account by paymenttype_id
                    if($data['bankaccount_id'] <= 0){
                        if($paymenttype['payment_type'] != PAYMENT_TYPE::BANK_TRANSFER){
                            $qry = $this->db->query("SELECT fn_bank_account.bankaccount_id FROM fn_bank_account
                                                 JOIN gl_coa on gl_coa.coa_id = fn_bank_account.coa_id
                                                 JOIN ms_payment_type on ms_payment_type.coa_code = gl_coa.coa_code
                                                 WHERE fn_bank_account.status = " . STATUS_NEW . "
                                                       AND ms_payment_type.paymenttype_id = " . $data['paymenttype_id']);
                            if($qry->num_rows() > 0){
                                $data['bankaccount_id'] = $qry->row()->bankaccount_id;
                            }else{
                                $data['bankaccount_id'] = 0;
                            }
                        }
                    }

                    //Re-Calculate receipt amount
                    $data['receipt_paymentamount'] = $receiptAmount - $data['receipt_bankcharges'];

                    if($receiptID > 0){
                        $qry = $this->db->get_where('ar_receipt', array('receipt_id' => $receiptID));
                        $row = $qry->row();

                        $arr_date = explode('-', $data['receipt_date']);
                        $arr_date_old = explode('-', ymd_from_db($row->receipt_date));

                        if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                            //DELETE OLD NUMBER
                            $data['receipt_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_RECEIPT, $data['receipt_date']);
                            if($data['receipt_no'] == ''){
                                $valid = false;

                                $this->session->set_flashdata('flash_message_class', 'danger');
                                $this->session->set_flashdata('flash_message', 'Unique No can not be generated. Please check document type!');
                            }
                        }

                        if($valid){
                            $data['modified_by'] = my_sess('user_id');
                            $data['modified_date'] = date('Y-m-d H:i:s');
                            $data['status'] = STATUS_NEW;

                            $this->mdl_general->update('ar_receipt', array('receipt_id' => $receiptID), $data);

                            $data['receipt_id'] = $row->receipt_id;

                            //SAVE ar_receipt_detail
                            $valid = $this->insertReceiptDetails($data);
                        }
                    }else{
                        $data['receipt_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_RECEIPT, $data['receipt_date']);
                        $data['created_by'] = my_sess('user_id');
                        $data['created_date'] = date('Y-m-d H:i:s');
                        $data['modified_by'] = 0;
                        $data['modified_date'] = date('Y-m-d H:i:s');
                        $data['status'] = STATUS_NEW;

                        if($data['receipt_no'] != ''){
                            $this->db->insert('ar_receipt', $data);
                            $receiptID = $this->db->insert_id();
                            if($receiptID <= 0){
                                $valid = false;
                            }

                            $data['receipt_id'] = $receiptID;

                            //SAVE ar_receipt_detail
                            $valid = $this->insertReceiptDetails($data);

                        }else{
                            $valid = false;
                            $this->session->set_flashdata('flash_message_class', 'danger');
                            $this->session->set_flashdata('flash_message', 'Unique No can not be generated. Please check document type!');
                        }
                    }

                    //LOG
                    if($valid){
                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Official Receipt';
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $receiptID;
                        $data_log['feature_id'] = Feature::FEATURE_AR_RECEIPT;
                        $data_log['action_type'] = STATUS_CLOSED;
                        $this->db->insert('app_log', $data_log);
                    }
                }else{
                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'AR Transfer is not supported.');
                }
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Official Receipt can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Official Receipt successfully saved.');
                }
            }else{
                $this->db->trans_rollback();
            }
        }

        //FINALIZE
        if(!$valid){
            redirect(base_url('ar/corporate_bill/receipt/1/' . $receiptID . '.tpd'));
        }
        else {
            redirect(base_url('ar/corporate_bill/receipt/1/' . $receiptID . '.tpd'), true);
        }
    }

    private function insertReceiptDetails($receipt = array()){
        $valid = true;

        if(isset($receipt) && isset($_POST)){
            //$checked_ids = isset($_POST['inv_id']) ? $_POST['inv_id'] : array();

            $invoice_ids = isset($_POST['invoice_id']) ? $_POST['invoice_id'] : array();
            $amounts = isset($_POST['pending_amount']) ? $_POST['pending_amount'] : array();
            $alloc_amounts = isset($_POST['alloc_amount']) ? $_POST['alloc_amount'] : array();

            if(count($invoice_ids) > 0){
                $details = array();
                for($i=0;$i<count($invoice_ids);$i++){
                    if($alloc_amounts[$i] > 0){
                        $detail = array();
                        $detail['receipt_id'] = $receipt['receipt_id'];
                        $detail['inv_id'] = $invoice_ids[$i];
                        $detail['base_amount'] = $amounts[$i];
                        $detail['receipt_amount'] = $alloc_amounts[$i];
                        $detail['status'] = STATUS_NEW;

                        array_push($details, $detail);
                    }
                }

                if(count($details) > 0){
                    $invids = array();
                    foreach($details as $detail){
                        $exist = $this->db->get_where('ar_receipt_detail', array('receipt_id' => $detail['receipt_id'], 'inv_id' => $detail['inv_id']));
                        if($exist->num_rows() > 0){
                            $detail_id = $exist->row()->id;
                            $this->mdl_general->update('ar_receipt_detail', array('id' => $detail_id), $detail);
                        }else{
                            $detail['status'] = STATUS_NEW;

                            $this->db->insert('ar_receipt_detail', $detail);
                            $insert_id = $this->db->insert_id();

                            if($insert_id <= 0){
                                $valid = false;
                                break;
                            }
                        }

                        $invids[] = $detail['inv_id'];
                    }

                    //DELETE OTHER INV_ID in detail
                    $str_inv_id = implode(',',$invids);
                    if(trim($str_inv_id) != '' && $receipt['receipt_id'] > 0){
                        $deleted = $this->db->query("DELETE FROM ar_receipt_detail WHERE receipt_id = " . $receipt['receipt_id'] . " AND inv_id NOT IN(" . $str_inv_id . ")");
                    }
                }else{
                    $deleted = $this->db->query("DELETE FROM ar_receipt_detail WHERE receipt_id = " . $receipt['receipt_id']);
                }
            }else{
                $deleted = $this->db->query("DELETE FROM ar_receipt_detail WHERE receipt_id = " . $receipt['receipt_id']);
            }
        }

        return $valid;
    }

    public function xpost_official_receipt(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $receipt_id = 0;

        if(isset($_POST['receipt_id'])){
            $receipt_id = $_POST['receipt_id'];
        }

        if($receipt_id > 0){
            $this->load->model('finance/mdl_finance');

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $rv = $this->db->get_where('ar_receipt', array('receipt_id'=> $receipt_id));
            if($rv->num_rows() > 0){
                $rv = $rv->row_array();

                if($valid){
                    //POST a Journal
                    //Bank
                    //Bank Charge
                    // - AR
                    $valid = $this->postingOfficialReceipt($rv);
                }

                if($valid){
                    //Allocate
                    $valid = $this->allocateReceiptToInvoice($rv);
                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_POSTED;

                    $this->mdl_general->update('ar_receipt', array('receipt_id' => $receipt_id), $data);
                }
            }else{
                $valid = false;
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be posted. Please try again later.';

                    //$this->session->set_flashdata('flash_message_class', 'danger');
                    //$this->session->set_flashdata('flash_message', 'Transaction can not be posted. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = $rv['receipt_no'] . ' successfully posted.';
                    $result['redirect_link'] = base_url('ar/corporate_bill/receipt/1/' . $receipt_id . '.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be posted. Please try again later.';
            }

        }

        echo json_encode($result);
    }

    public function postingOfficialReceipt($rv = array()){
        $valid = true;

        if(count($rv) > 0){
            $this->load->model('finance/mdl_finance');

            try{
                $qry = $this->db->get_where('ms_payment_type', array('paymenttype_id' => $rv['paymenttype_id']));
                if($qry->num_rows() > 0){
                    $paymenttype = $qry->row_array();

                    //Post Journal
                    //Bank
                    //VT Charge (if any)
                    //Bank Charge (if any)
                    // AR
                    $detail = array();
                    $totalDebit = 0;

                    $totalCredit = $rv['receipt_bankcharges'] + $rv['receipt_paymentamount'];
                    if($totalCredit > 0){
                        //BANK
                        $bank_coa_id = 0;

                        $coa = $this->db->get_where('gl_coa', array('coa_code' => $paymenttype['coa_code']));
                        if($coa->num_rows() > 0){
                            $bank_coa_id = $coa->row()->coa_id;
                        }

                        if($bank_coa_id <= 0 && $rv['bankaccount_id'] > 0){
                            $bank = $this->mdl_finance->getBankAccount($rv['bankaccount_id']);
                            if(isset($bank['coa_id']))
                                $bank_coa_id = $bank['coa_id'];
                        }

                        if($bank_coa_id > 0){
                            $rowdet = array();
                            $rowdet['coa_id'] = $bank_coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $rv['receipt_desc'];
                            $rowdet['journal_debit'] = $rv['receipt_paymentamount'];
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = $rv['receipt_id'];
                            $rowdet['transtype_id'] = 0;

                            array_push($detail, $rowdet);

                            $totalDebit += $rv['receipt_paymentamount'];
                        }else{
                            $valid = false;
                        }

                        if($valid){
                            //BANK CHARGE (if any)
                            if($rv['receipt_bankcharges'] > 0){
                                $bc = FNSpec::get(FNSpec::BANK_CHARGE);
                                if($bc['coa_id'] > 0){
                                    $rowdet = array();
                                    $rowdet['coa_id'] = $bc['coa_id'];
                                    $rowdet['dept_id'] = 0;
                                    $rowdet['journal_note'] = $rv['receipt_desc'];
                                    $rowdet['journal_debit'] = $rv['receipt_bankcharges'];
                                    $rowdet['journal_credit'] = 0;
                                    $rowdet['reference_id'] = $rv['receipt_id'];
                                    $rowdet['transtype_id'] = $bc['transtype_id'];

                                    array_push($detail, $rowdet);

                                    $totalDebit += $rv['receipt_bankcharges'];
                                }
                            }
                        }
                    }else{
                        $valid = false;
                    }

                    if($valid){
                        if($totalCredit > 0 && $totalCredit == $totalDebit && count($detail) > 0){
                            //AR
                            if($rv['company_id'] > 0){
                                $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES_CORP);
                            }else{
                                $ar = FNSpec::get(FNSpec::TRADE_RECEIVABLES);
                            }

                            if($ar['coa_id'] > 0){
                                $rowdet = array();
                                $rowdet['coa_id'] = $ar['coa_id'];
                                $rowdet['dept_id'] = 0;
                                $rowdet['journal_note'] = $rv['receipt_desc'];
                                $rowdet['journal_debit'] = 0;
                                $rowdet['journal_credit'] = $totalCredit;
                                $rowdet['reference_id'] = $rv['receipt_id'];
                                $rowdet['transtype_id'] = $ar['transtype_id'];

                                array_push($detail, $rowdet);
                            }
                        }else{
                            $valid = false;
                        }
                    }

                    if($valid && $totalDebit == $totalCredit && $totalDebit > 0 ){
                        $header = array();
                        $header['journal_no'] = $rv['receipt_no'];
                        $header['journal_date'] = $rv['receipt_date'];
                        $header['journal_remarks'] = $rv['receipt_desc'];
                        $header['modul'] = GLMOD::GL_MOD_AR;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = '';
                        //$header['reference_date'] = $reservation['reservation_date'];

                        $valid = $this->mdl_finance->postJournal($header,$detail);
                    }else{
                        $valid = false;
                    }
                }else{
                    $valid = false;
                }
            }catch(Exception $e){
                $valid = false;
            }
        }

        return $valid;
    }

    private function allocateReceiptToInvoice($rv = array()){
        $valid = true;

        if(count($rv) > 0){
            //$availableAmount = $rv['receipt_bankcharges'] + $rv['receipt_paymentamount'];
            $current_date = date('Y-m-d H:i:s');

            $receipt_details = $this->db->get_where('ar_receipt_detail', array('receipt_id'=>$rv['receipt_id']));
            if($receipt_details->num_rows() > 0){
                $inv_ids = array();

                foreach($receipt_details->result() as $det){
                    $inv_id = $det->inv_id;
                    $availableAmount = $det->receipt_amount;

                    //Allocate to CS Receipt Allocation
                    $unpaids = $this->db->get_where('view_ar_unpaid_invoice', array('inv_id'=>$inv_id));
                    if($unpaids->num_rows() > 0){
                        $paidAmount = 0;
                        $paidTax = 0;
                        foreach($unpaids->result_array() as $bill){
                            //AMOUNT
                            if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                                $detail = array();
                                $detail['receipt_id'] = $rv['receipt_id'];
                                $detail['invdetail_id'] = $bill['invdetail_id'];
                                $detail['bookingreceipt_id'] = 0;
                                $detail['billdetail_id'] = 0;
                                $detail['allocation_date'] = $rv['receipt_date'];
                                $detail['is_debitnote'] = 0;
                                $detail['allocationheader_id'] = 0;
                                $detail['created_by'] = my_sess('user_id');
                                $detail['created_date'] = $current_date;
                                $detail['status'] = STATUS_CLOSED;
                                $detail['is_tax'] = 0;

                                if($bill['pending_amount'] <= $availableAmount){
                                    $detail['allocation_amount'] = $bill['pending_amount'];
                                }else{
                                    $detail['allocation_amount'] = $availableAmount;
                                }

                                $paidAmount += $detail['allocation_amount'];

                                $availableAmount -= $detail['allocation_amount'];

                                if($detail['allocation_amount'] > 0){
                                    $this->db->insert('ar_receipt_allocation', $detail);
                                    if($this->db->insert_id() <= 0){
                                        $valid = false;
                                        break;
                                    }
                                }

                                if($valid){
                                    //---------------------
                                    //UPDATE INVOICE DETAIL
                                    //---------------------
                                    $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']));
                                    if($invdetail->num_rows() > 0){
                                        $invdetail = $invdetail->row();
                                        $alloc_amount = $invdetail->paid_amount + $detail['allocation_amount'];

                                        $update = array();
                                        if($alloc_amount <= $invdetail->amount){
                                            $update['paid_amount'] = $alloc_amount;
                                        }else{
                                            $update['paid_amount'] = $invdetail->amount;
                                        }
                                        $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']), $update);

                                    }
                                    //---------------------
                                }
                            }

                            //TAX
                            if($bill['pending_tax'] > 0 && $availableAmount > 0 && $valid){
                                $detail = array();
                                $detail['receipt_id'] = $rv['receipt_id'];
                                $detail['invdetail_id'] = $bill['invdetail_id'];
                                $detail['bookingreceipt_id'] = 0;
                                $detail['billdetail_id'] = 0;
                                $detail['allocation_date'] = $rv['receipt_date'];
                                $detail['is_debitnote'] = 0;
                                $detail['allocationheader_id'] = 0;
                                $detail['created_by'] = my_sess('user_id');
                                $detail['created_date'] = $current_date;
                                $detail['status'] = STATUS_CLOSED;
                                $detail['is_tax'] = 1;

                                if($bill['pending_tax'] <= $availableAmount){
                                    $detail['allocation_amount'] = $bill['pending_tax'];
                                }else{
                                    $detail['allocation_amount'] = $availableAmount;
                                }

                                $paidTax += $detail['allocation_amount'];
                                $availableAmount -= $detail['allocation_amount'];

                                if($detail['allocation_amount'] > 0){
                                    $this->db->insert('ar_receipt_allocation', $detail);
                                    if($this->db->insert_id() <= 0){
                                        $valid = false;
                                        break;
                                    }
                                }

                                if($valid){
                                    //---------------------
                                    //UPDATE INVOICE DETAIL
                                    //---------------------
                                    $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']));
                                    if($invdetail->num_rows() > 0){
                                        $invdetail = $invdetail->row();
                                        $alloc_tax = $invdetail->paid_tax + $detail['allocation_amount'];

                                        $update = array();
                                        if($alloc_tax <= $invdetail->tax){
                                            $update['paid_tax'] = $alloc_tax;
                                        }else{
                                            $update['paid_tax'] = $invdetail->tax;
                                        }
                                        $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']), $update);
                                    }
                                    //---------------------
                                }
                            }
                        }

                        if($valid){
                            $found = false;
                            foreach($inv_ids as $key => $value){
                                if($key == $inv_id){
                                    $found = true;
                                    break;
                                }
                            }
                            if(!$found){
                                $inv_ids[$inv_id] = round($paidAmount + $paidTax);
                            }else {
                                $inv_ids[$inv_id] += round($paidAmount + $paidTax);
                            }

                            //---------------------------------------------------
                            //UPDATE INVOICE HEADER STATUS TO CLOSED IF FULL PAID
                            //---------------------------------------------------
                            //$invoice = $this->db->get_where('view_ar_unpaid_invoice',array('inv_id'=> $inv_id));
                            //if($invoice->num_rows() <= 0){
                            //$this->mdl_general->update('ar_invoice_header',array('inv_id' => $inv_id), array('status' => STATUS_CLOSED));
                                //$this->mdl_general->update('ar_receipt_detail',array('inv_id' => $inv_id), array('status' => STATUS_POSTED));
                            //}
                            //---------------------------------------------------
                        }
                    }
                }

                //---------------------------------------------------
                //UPDATE INVOICE HEADER STATUS TO CLOSED IF FULL PAID
                //---------------------------------------------------
                if(count($inv_ids) > 0){
                    foreach ($inv_ids as $key => $value){
                        $invoice = $this->db->get_where('view_ar_unpaid_invoice',array('inv_id'=> $key));
                        //echo 'PENDING A ' . $key;
                        if($invoice->num_rows() > 0){
                            $invoice = $invoice->row();
                            $pending = round($invoice->pending_amount + $invoice->pending_tax,0);
                            //echo 'PENDING A ' . $this->db->last_query();
                            //echo 'PENDING B ' . $invoice->amount . ' | ' . $invoice->pending_amount . ' + ' . $invoice->pending_tax;
                            //echo 'PENDING C ' . $key . ' = ' . $pending . ' - ' . $value;
                            //$pending = round($pending - $value);
                            //echo 'BALANCE ' . $pending;
                            if($pending <= 0){
                                $this->mdl_general->update('ar_invoice_header',array('inv_id' => $key), array('status' => STATUS_CLOSED));
                            }
                        }else{
                            $this->mdl_general->update('ar_invoice_header',array('inv_id' => $key), array('status' => STATUS_CLOSED));
                        }
                    }
                }
                //---------------------------------------------------

            }
        }

        return $valid;
    }

    private function allocateReceiptToBill($rv = array()){
        $valid = true;

        if(count($rv) > 0){
            $availableAmount = $rv['receipt_bankcharges'] + $rv['receipt_paymentamount'];
            $current_date = date('Y-m-d H:i:s');

            $invoice_list = array();

            //Allocate to CS Receipt Allocation
            $unpaids = $this->db->query('SELECT * FROM fxnARUnpaidInvoiceDetail(' . $rv['company_id'] . ')');
            if($unpaids->num_rows() > 0){
                foreach($unpaids->result_array() as $bill){
                    //AMOUNT
                    if($bill['pending_amount'] > 0 && $availableAmount > 0 && $valid){
                        $detail = array();
                        $detail['receipt_id'] = $rv['receipt_id'];
                        $detail['invdetail_id'] = $bill['invdetail_id'];
                        $detail['bookingreceipt_id'] = 0;
                        $detail['billdetail_id'] = 0;
                        $detail['allocation_date'] = $rv['receipt_date'];
                        $detail['is_debitnote'] = 0;
                        $detail['allocationheader_id'] = 0;
                        $detail['created_by'] = my_sess('user_id');
                        $detail['created_date'] = $current_date;
                        $detail['status'] = STATUS_CLOSED;
                        $detail['is_tax'] = 0;

                        if($bill['pending_amount'] <= $availableAmount){
                            $detail['allocation_amount'] = $bill['pending_amount'];
                        }else{
                            $detail['allocation_amount'] = $availableAmount;
                        }

                        $availableAmount -= $detail['allocation_amount'];

                        if($detail['allocation_amount'] > 0){
                            $this->db->insert('ar_receipt_allocation', $detail);
                            if($this->db->insert_id() <= 0){
                                $valid = false;
                                break;
                            }
                        }

                        if($valid){
                            //---------------------
                            //UPDATE INVOICE DETAIL
                            //---------------------
                            $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']));
                            if($invdetail->num_rows() > 0){
                                $invdetail = $invdetail->row();
                                $alloc_amount = $invdetail->paid_amount + $detail['allocation_amount'];

                                $update = array();
                                if($alloc_amount <= $invdetail->amount){
                                    $update['paid_amount'] = $alloc_amount;
                                }else{
                                    $update['paid_amount'] = $invdetail->amount;
                                }
                                $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']), $update);

                                $found = false;
                                foreach($invoice_list as $inv){
                                    if($inv == $bill['inv_id']){
                                        $found = true;
                                        break;
                                    }
                                }

                                if(!$found){
                                    array_push($invoice_list, $bill['inv_id']);
                                }
                            }
                            //---------------------
                        }
                    }

                    //TAX
                    if($bill['pending_tax'] > 0 && $availableAmount > 0 && $valid){
                        $detail = array();
                        $detail['receipt_id'] = $rv['receipt_id'];
                        $detail['invdetail_id'] = $bill['invdetail_id'];
                        $detail['bookingreceipt_id'] = 0;
                        $detail['billdetail_id'] = 0;
                        $detail['allocation_date'] = $rv['receipt_date'];
                        $detail['is_debitnote'] = 0;
                        $detail['allocationheader_id'] = 0;
                        $detail['created_by'] = my_sess('user_id');
                        $detail['created_date'] = $current_date;
                        $detail['status'] = STATUS_CLOSED;
                        $detail['is_tax'] = 1;

                        if($bill['pending_tax'] <= $availableAmount){
                            $detail['allocation_amount'] = $bill['pending_tax'];
                        }else{
                            $detail['allocation_amount'] = $availableAmount;
                        }

                        $availableAmount -= $detail['allocation_amount'];

                        if($detail['allocation_amount'] > 0){
                            $this->db->insert('ar_receipt_allocation', $detail);
                            if($this->db->insert_id() <= 0){
                                $valid = false;
                                break;
                            }
                        }

                        if($valid){
                            //---------------------
                            //UPDATE INVOICE DETAIL
                            //---------------------
                            $invdetail = $this->db->get_where('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']));
                            if($invdetail->num_rows() > 0){
                                $invdetail = $invdetail->row();
                                $alloc_tax = $invdetail->paid_tax + $detail['allocation_amount'];

                                $update = array();
                                if($alloc_tax <= $invdetail->tax){
                                    $update['paid_tax'] = $alloc_tax;
                                }else{
                                    $update['paid_tax'] = $invdetail->tax;
                                }
                                $this->mdl_general->update('ar_invoice_detail',array('invdetail_id' => $detail['invdetail_id']), $update);

                                $found = false;
                                foreach($invoice_list as $inv){
                                    if($inv == $bill['inv_id']){
                                        $found = true;
                                        break;
                                    }
                                }

                                if(!$found){
                                    array_push($invoice_list, $bill['inv_id']);
                                }
                            }
                            //---------------------
                        }
                    }
                }

                //---------------------------------------------------
                //UPDATE INVOICE HEADER STATUS TO CLOSED IF FULL PAID
                //---------------------------------------------------
                if(count($invoice_list) > 0){
                    foreach($invoice_list as $inv_id){
                        $invoice = $this->db->get_where('view_ar_unpaid_invoice',array('inv_id'=>$inv_id));
                        if($invoice->num_rows() <= 0){
                            $this->mdl_general->update('ar_invoice_header',array('inv_id' => $inv_id), array('status' => STATUS_CLOSED));
                            //$this->mdl_general->update('ar_receipt_detail',array('inv_id' => $inv_id), array('status' => STATUS_POSTED));
                        }
                    }
                }
                //---------------------------------------------------
            }
        }

        return $valid;
    }

    public function xcorp_pending_invoice(){
        $result = '';

        $company_id = 0;
        $reservation_id = 0;
        if(isset($_POST['company_id'])){
            $company_id = $_POST['company_id'];
        }
        if(isset($_POST['reservation_id'])){
            $reservation_id = $_POST['reservation_id'];
        }

        if($company_id > 0 || $reservation_id > 0){
            $this->load->model('finance/mdl_finance');

            $where['bill.unpaid_grand >'] = 0;
            if($company_id > 0){
                $where['bill.company_id'] = $company_id;
            }else{
                 $where['bill.reservation_id'] = $reservation_id;
            }

            $joins = array();
            $qry = $this->mdl_finance->getJoin("bill.*", "fxnARInvoiceHeaderByStatus('" . STATUS_POSTED . "') AS bill", $joins, $where, array(), 'inv_no');

            //echo $this->db->last_query();
            if($qry->num_rows() > 0){
                foreach($qry->result_array() as $bill){
                    $existcredit = $this->db->query('SELECT ISNULL(SUM(ar_receipt_detail.receipt_amount),0) as credit_amount FROM ar_receipt_detail
                                               JOIN ar_receipt ON ar_receipt.receipt_id = ar_receipt_detail.receipt_id
                                               WHERE ar_receipt_detail.inv_id = ' . $bill['inv_id'] . ' AND ar_receipt.status = ' . STATUS_NEW );
                    if($existcredit->num_rows() > 0){
                        $pending_credit = $existcredit->row()->credit_amount;
                    }

                    $pending_amount = $bill['unpaid_grand'] - $pending_credit;

                    if($pending_amount > 0){
                        $result .= '<tr id="parent_' . $bill['inv_id'] . '' . '">
                             <td style="vertical-align:middle;" class="text-center">
                                <input type="hidden" name="invoice_id[]" value="' . $bill['inv_id'] . '">
                                <span class="text-center">' . $bill['inv_no'] . '</span>
                             </td>
                             <td style="vertical-align:middle;" class="text-center">
                                <span class="text-center">' . dmy_from_db($bill['inv_date']) . '</span>
                             </td>
                             <td style="vertical-align:middle;" class="text-center">
                                <span class="text-center">' . dmy_from_db($bill['inv_due_date']) . '</span>
                             </td>
                             <td style="vertical-align:middle;" >
                                <input type="hidden" name="base_amount[]" value="' . $pending_amount .'" >
                                <input type="text" name="pending_amount[]" value="' . $pending_amount .'" class="form-control text-right mask_currency input-sm" readonly>
                             </td>
                             <td style="vertical-align:middle;" >
                                <input type="text" name="alloc_amount[]" value="' . 0 .'" class="form-control text-right mask_currency input-sm">
                             </td>
                             <td style="vertical-align:middle;" class="text-center">
                                <a inv-id="' . $bill['inv_id'] .'" data-placement="top" data-container="body" class="btn btn-xs green-meadow add_amount " href="javascript:;"><i class="fa fa-plus add_amount_plus "></i><i class="fa fa-minus add_amount_minus hide"></i>
                             </td>
                            </tr>';
                    }
                }
            }
        }

        echo $result;
    }

    #endregion

    #region Receipt Allocation

    public function receipt_al($type = 1, $id = 0){
        if ($type == 1 || $type == 2) {
            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

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
            if($type == 1){
                $this->load->view('ar/bill/receipt_alloc_manage.php', $data);
            }else{
                $this->load->view('ar/bill/receipt_alloc_history.php', $data);
            }
            $this->load->view('layout/footer');
        }else{
            //$this->load->library('../controllers/ar/deposit');
            $this->receipt_al_form($id);
        }
    }

    public function get_receipt_al_manage($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['header.receipt_id >'] = 0;
        $where['header.depositdetail_id <='] = 0;
        $where['header.status'] = STATUS_NEW;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['header.alloc_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,header.alloc_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,header.alloc_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['header.company_name'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_receipt_no'])){
            if($_REQUEST['filter_receipt_no'] != ''){
                $like['header.receipt_no'] = $_REQUEST['filter_receipt_no'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_allocation_header as header', $joins, $where, $like, true);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'header.alloc_no DESC, header.alloc_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'header.alloc_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'header.alloc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'header.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('header.*'
            ,'vw_ar_allocation_header as header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, true);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/receipt_al/3/' . $row->allocationheader_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a></li>';
            if($row->status == STATUS_NEW){
                if(check_session_action($menu_id, STATUS_CANCEL) || check_session_action($menu_id, STATUS_DELETE)){
                    $btn_action .= '<li><a data-original-title="Cancel" class="btn-cancel" href="javascript:;" data-id="' . $row->allocationheader_id . '" ><i class="fa fa-remove"></i> ' . get_action_name(STATUS_CANCEL, false) . '</a></li>';
                }
            }

            $records["data"][] = array(
                $i, //'<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->alloc_no,
                dmy_from_db($row->alloc_date),
                $row->company_name,
                $row->receipt_no,
                format_num(round($row->receipt_amount,0),0),
                format_num($row->alloc_amount,0),
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
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function get_receipt_al_history($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['header.receipt_id >'] = 0;
        $where['header.depositdetail_id <='] = 0;
        $where['header.status'] = STATUS_CLOSED;

        $like = array();

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['header.alloc_no'] = $_REQUEST['filter_no'];
            }
        }
        if(isset($_REQUEST['filter_date_from'])){
            if($_REQUEST['filter_date_from'] != ''){
                $where['CONVERT(date,header.alloc_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if(isset($_REQUEST['filter_date_to'])){
            if($_REQUEST['filter_date_to'] != ''){
                $where['CONVERT(date,header.alloc_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['header.company_name'] = $_REQUEST['filter_name'];
            }
        }
        if(isset($_REQUEST['filter_receipt_no'])){
            if($_REQUEST['filter_receipt_no'] != ''){
                $like['header.receipt_no'] = $_REQUEST['filter_receipt_no'];
            }
        }

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin('vw_ar_allocation_header as header', $joins, $where, $like, true);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'header.alloc_no DESC, header.alloc_date DESC';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'header.alloc_no ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'header.alloc_date ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'ms_company.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('header.*'
            ,'vw_ar_allocation_header as header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, true);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/receipt_al/3/' . $row->allocationheader_id) . '.tpd"><i class="fa fa-pencil"></i>  Open</a></li>';

            $records["data"][] = array(
                $i, //'<input type="checkbox" value="' . $row->inv_id . '" name="ischecked[]" />',
                $row->alloc_no,
                dmy_from_db($row->alloc_date),
                $row->company_name,
                $row->receipt_no,
                format_num(round($row->receipt_amount,0),0),
                format_num($row->alloc_amount,0),
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
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function receipt_al_form($allocationheader_id = 0){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        //HEADER
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

        $data['allocationheader_id'] = $allocationheader_id;

        if($allocationheader_id > 0){
            $joins = array('ms_company' => 'header.company_id = ms_company.company_id',
                'ar_receipt' => 'ar_receipt.receipt_id = header.receipt_id'
                );
            $qry = $this->mdl_finance->getJoin('header.*, ms_company.company_name, ar_receipt.receipt_no'
                ,'ar_allocation_header as header', $joins, array('header.allocationheader_id' => $allocationheader_id));

            if($qry->num_rows() > 0){
                $data['row'] = $qry->row();

                //Detail
                $tempTable = array();
                if($data['row']->status == STATUS_NEW){
                    $allocs = $this->db->query("SELECT al.invdetail_id, ISNULL(SUM(al.allocation_amount),0) as allocation_total FROM ar_receipt_allocation al WHERE al.allocationheader_id = " . $allocationheader_id . "
                                            GROUP BY al.invdetail_id");
                    if($allocs->num_rows() > 0){
                        $pendings = $this->mdl_finance->getJoin("bill.*", "fxnARUnpaidInvoiceDetail(" . $data['row']->company_id . "," . $data['row']->reservation_id  . ") AS bill", array(), array(), array(), 'inv_no');

                        foreach($pendings->result() as $bill){
                            $allocated = 0;
                            foreach($allocs->result() as $alloc){
                                if($alloc->invdetail_id == $bill->invdetail_id){
                                    $allocated = $alloc->allocation_total;
                                }
                            }
                            array_push($tempTable, array('invdetail_id' => $bill->invdetail_id, 'inv_no' => $bill->inv_no,'description' => $bill->description, 'transtype_id' => $bill->transtype_id, 'transtype_name' => $bill->transtype_name, 'base_amount' => ($bill->pending_amount + $bill->pending_tax), 'alloc_amount' => $allocated));
                        }
                    }
                }else{
                    $pendings = $this->db->query("SELECT iv.invdetail_id, hd.inv_no, iv.description, iv.transtype_id, trx.transtype_name, ISNULL(SUM(al.allocation_amount),0) as allocation_total FROM ar_receipt_allocation al
                                  JOIN ar_invoice_detail iv ON iv.invdetail_id = al.invdetail_id
                                  JOIN ar_invoice_header hd ON hd.inv_id = iv.inv_id
                                  LEFT JOIN ms_transtype trx ON trx.transtype_id = iv.transtype_id
                                  WHERE al.allocationheader_id = " . $allocationheader_id . "
                                  GROUP BY iv.invdetail_id, hd.inv_no, iv.description, iv.transtype_id, trx.transtype_name");
                    //echo '<!--QUERY' . $this->db->last_query() .'-->';
                    foreach($pendings->result() as $bill){
                        array_push($tempTable, array('invdetail_id' => $bill->invdetail_id, 'inv_no' => $bill->inv_no,'description' => $bill->description, 'transtype_id' => $bill->transtype_id, 'transtype_name' => $bill->transtype_name, 'base_amount' => $bill->allocation_total, 'alloc_amount' => $bill->allocation_total));
                    }
                }

                $data['details'] = $tempTable;

                //Get Unposted amount
                $unposted_amount = 0;
                $existreceipt = $this->db->query('SELECT ISNULL(SUM(alloc_amount),0) as receipt_total FROM ar_allocation_header
                          WHERE receipt_id = ' . $data['row']->receipt_id . ' AND status = ' . STATUS_NEW . '
                                AND allocationheader_id <> ' . $allocationheader_id . ' AND allocationheader_id < ' . $allocationheader_id);

                if($existreceipt->num_rows() > 0){
                    $unposted_amount = $existreceipt->row()->receipt_total;
                }

                //Pending Unpaid
                $pending_amount = 0;

                $company_pending = $this->db->get_where('view_ar_invoice_unpaid_sum', array('company_id' => $data['row']->company_id));
                if($company_pending->num_rows() > 0){
                    $pending_amount = $company_pending->row()->sum_pending - $unposted_amount;
                }
                $data['pending_amount'] = $pending_amount;

                //Available amount
                $unallocated = 0;
                $undeposit = $this->db->get_where("fxnARUnallocatedByDateCorp('" . ymd_from_db($data['row']->created_date). "')", array('company_id' => $data['row']->company_id, 'receipt_id' => $data['row']->receipt_id));
                if($undeposit->num_rows() > 0){
                    $unallocated = $undeposit->row()->unallocated_amount - $unposted_amount;
                }

                $data['available_amount'] = $unallocated;
            }else{
                unset($data);
            }
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/bill/receipt_alloc_form', $data);
        $this->load->view('layout/footer');
    }

    public function xcorp_pending_bill_detail(){
        $result = '';

        $company_id = 0;
        if(isset($_POST['company_id'])){
            $company_id = $_POST['company_id'];
        }

        $reservation_id = 0;
        if(isset($_POST['reservation_id'])){
            $reservation_id = $_POST['reservation_id'];
        }

        if($company_id > 0 || $reservation_id > 0){
            $this->load->model('finance/mdl_finance');

            $joins = array();
            $qry = $this->mdl_finance->getJoin("bill.*", "fxnARUnpaidInvoiceDetail(" . $company_id . "," . $reservation_id . ") AS bill", $joins, array(), array(), 'inv_no');

            if($qry->num_rows() > 0){
                foreach($qry->result_array() as $bill){
                    $pending_total = $bill['pending_amount'] + $bill['pending_tax'];

                    if($pending_total > 0){
                        $existcredit = $this->db->query('SELECT ISNULL(SUM(allocation_amount),0) as credit_amount FROM ar_receipt_allocation
                                               WHERE invdetail_id = ' . $bill['invdetail_id'] . ' AND status = ' . STATUS_NEW );
                        if($existcredit->num_rows() > 0){
                            $pending_credit = $existcredit->row()->credit_amount;
                        }

                        $pending_amount = $pending_total - $pending_credit;

                        if($pending_amount > 0){
                            $result .= '<tr id="parent_' . $bill['invdetail_id'] . '' . '">
                                 <td style="vertical-align:middle;" class="text-center">
                                    <input type="hidden" name="allocation_id[]" value="">
                                    <input type="hidden" name="invdetail_id[]" value="' . $bill['invdetail_id'] . '">
                                    <span class="text-center">' . $bill['inv_no'] . '</span>
                                 </td>
                                 <td style="vertical-align:middle;">
                                    <span class="text-center">' . $bill['description'] . '</span>
                                 </td>
                                 <td style="vertical-align:middle;" class="text-center">
                                    <input type="hidden" name="transtype_id[]" value="' . $bill['transtype_id'] . '">
                                    <span class="text-center">' . $bill['transtype_name'] . '</span>
                                 </td>
                                 <td style="vertical-align:middle;" class="control-label">
                                    <input type="text" name="base_amount[]" value="' . $pending_amount .'" class="form-control text-right mask_currency input-sm" readonly>
                                 </td>
                                 <td style="vertical-align:middle;" class="control-label">
                                    <input type="text" name="alloc_amount[]" value="0" class="form-control text-right mask_currency input-sm" >
                                 </td>
                                 <td style="vertical-align:middle;padding-top:8px;padding-left:7px;">
                                    <a inv-detail-id="' . $bill['invdetail_id'] .'" inv-is-tax="' . 0 .'" data-placement="top" data-container="body" class="btn btn-xs green-meadow add_amount " href="javascript:;"><i class="fa fa-plus add_amount_plus"></i><i class="fa fa-minus add_amount_minus hide"></i>
                                    </a>
                                </td>
                                </tr>';
                        }
                    }
                }
            }
        }

        echo $result;
    }

    public function xcancel_receipt_al(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        $allocationHeaderId = isset($_POST['allocationheader_id']) ? $_POST['allocationheader_id'] : 0;
        $data['cancel_note'] = $_POST['reason'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = 'Cancel Receipt Allocation';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $allocationHeaderId;
        $data_log['feature_id'] = Feature::FEATURE_AR_ALLOC;

        if($allocationHeaderId > 0){
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $valid = true;

            try {
                $this->db->delete('ar_receipt_allocation', array('allocationheader_id' => $allocationHeaderId));

                $data['status'] = STATUS_CANCEL;
                $data['modified_by'] = my_sess('user_id');
                $data['modified_date'] = date('Y-m-d H:i:s');
                $this->mdl_general->update('ar_allocation_header', array('allocationheader_id' => $allocationHeaderId), $data);

                $this->db->delete('ar_allocation_deposit_detail', array('allocationheader_id' => $allocationHeaderId));

                $data_log['action_type'] = STATUS_CANCEL;
                $this->db->insert('app_log', $data_log);
            }catch(Exception $e){
                $valid = false;
            }
            //COMMIT OR ROLLBACK
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Allocation can not be canceled. Please try again later !';

                //$this->session->set_flashdata('flash_message_class', 'danger');
                //$this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }
            else
            {
                if($valid) {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] = 'Allocation has been canceled.';

                    //$this->session->set_flashdata('flash_message_class', 'success');
                    //$this->session->set_flashdata('flash_message', 'Invoice successfully created.');
                }else{
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Allocation can not be canceled. Please try again later !';
                }
            }
        }

        echo json_encode($result);
    }

    public function submit_receipt_al(){
        $valid = true;

        if(isset($_POST)){
            $allocationheader_id = $_POST['allocationheader_id'];
            $allocAmount = floatval($_POST['total_allocated']);

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $data['alloc_date'] = dmy_to_ymd($_POST['alloc_date']);
            $data['receipt_id'] = $_POST['receipt_id'];
            $data['company_id'] = $_POST['company_id'];
            $data['reservation_id'] = $_POST['reservation_id'];
            $data['depositdetail_id'] = 0;
            $data['alloc_desc'] = $_POST['alloc_desc'];
            $data['alloc_amount'] = $allocAmount;
            $data['status'] = STATUS_NEW;

            if($allocationheader_id > 0){
                $qry = $this->db->get_where('ar_allocation_header', array('allocationheader_id' => $allocationheader_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['alloc_date']);
                $arr_date_old = explode('-', ymd_from_db($row->alloc_date));

                if($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]){
                    //DELETE OLD NUMBER
                    $data['alloc_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_ALLOC, $data['alloc_date']);
                    if($data['alloc_no'] == ''){
                        $valid = false;

                        $this->session->set_flashdata('flash_message_class', 'danger');
                        $this->session->set_flashdata('flash_message', 'Unique No can not be generated. Please check document type!');
                    }
                }

                if($valid){
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('ar_allocation_header', array('allocationheader_id' => $allocationheader_id), $data);

                    //Insert ar_receipt_allocation
                    $data['allocationheader_id'] = $allocationheader_id;
                    $valid = $this->insertAllocationDetails($data);
                }
            }else{
                $data['alloc_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_ALLOC, $data['alloc_date']);
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = date('Y-m-d H:i:s');
                $data['modified_by'] = 0;
                $data['modified_date'] = date('Y-m-d H:i:s');

                if($data['alloc_no'] != ''){
                    $this->db->insert('ar_allocation_header', $data);
                    $allocationheader_id = $this->db->insert_id();
                    if($allocationheader_id <= 0){
                        $valid = false;
                    }

                    //Insert ar_receipt_allocation
                    $data['allocationheader_id'] = $allocationheader_id;
                    $valid = $this->insertAllocationDetails($data);
                }else{
                    $valid = false;
                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Unique No can not be generated. Please check document type!');
                }
            }

            //LOG
            if($valid){
                $data_log['user_id'] = my_sess('user_id');
                $data_log['log_subject'] = get_action_name($data['status'], false) . ' Receipt Allocation';
                $data_log['log_date'] = date('Y-m-d H:i:s');
                $data_log['reff_id'] = $allocationheader_id;
                $data_log['feature_id'] = Feature::FEATURE_AR_ALLOC;
                $data_log['action_type'] = STATUS_CLOSED;
                $this->db->insert('app_log', $data_log);
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Allocation can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Allocation successfully saved.');
                }
            }else{
                $this->db->trans_rollback();
            }
        }

        //FINALIZE
        if(!$valid){
            redirect(base_url('ar/corporate_bill/receipt_al/3/' . $allocationheader_id . '.tpd'));
        }
        else {
            redirect(base_url('ar/corporate_bill/receipt_al/3/' . $allocationheader_id . '.tpd'), true);
        }
    }

    private function insertAllocationDetails($allocHeader = array()){
        $valid = true;

        if(isset($allocHeader) && isset($_POST)){
            $receipt = $this->db->get_where('ar_receipt', array('receipt_id' => $allocHeader['receipt_id']));

            if($receipt->num_rows() > 0){
                $receipt = $receipt->row();

                //$allocdetail_ids = isset($_POST['allocation_id']) ? $_POST['allocation_id'] : array();
                $invdetail_ids = isset($_POST['invdetail_id']) ? $_POST['invdetail_id'] : array();
                //$transtype_ids = isset($_POST['transtype_id']) ? $_POST['transtype_id'] : array();
                $base_amounts = isset($_POST['base_amount']) ? $_POST['base_amount'] : array();
                $alloc_amounts = isset($_POST['alloc_amount']) ? $_POST['alloc_amount'] : array();

                $current_user_id = my_sess('user_id');
                $created_date = date('Y-m-d H:i:s');

                if(count($invdetail_ids) > 0 && count($base_amounts) > 0){
                    for($i=0;$i<count($invdetail_ids);$i++){
                        if($valid){
                            $invdetail = $this->db->get_where('ar_invoice_detail', array('invdetail_id'=> $invdetail_ids[$i]));
                            if($invdetail->num_rows() > 0){
                                $invdetail = $invdetail->row();
                                $availableAmount = floatval($alloc_amounts[$i]);

                                if($availableAmount > 0){
                                    //ALLOCATED AMOUNT
                                    $detail = array();
                                    $detail['allocationheader_id'] =  $allocHeader['allocationheader_id'];
                                    $detail['receipt_id'] = $allocHeader['receipt_id'];
                                    $detail['invdetail_id'] = $invdetail_ids[$i];
                                    $detail['bookingreceipt_id'] =  0;
                                    $detail['billdetail_id'] = 0;
                                    $detail['is_tax'] = 0;
                                    $detail['is_debitnote'] = 0;
                                    $detail['is_transfer_ar'] = 0;
                                    $detail['allocation_date'] = ymd_from_db($receipt->receipt_date);
                                    $detail['created_by'] = $current_user_id;
                                    $detail['created_date'] = $created_date;

                                    if($invdetail->amount >= $availableAmount){
                                        $detail['allocation_amount'] = $availableAmount;
                                        $availableAmount = 0;
                                    }else{
                                        $detail['allocation_amount'] = $invdetail->amount;
                                        $availableAmount = ($availableAmount - $invdetail->amount);
                                    }

                                    $currentAlloc = $this->db->get_where('ar_receipt_allocation', array('allocationheader_id' => $allocHeader['allocationheader_id'], 'invdetail_id' => $invdetail_ids[$i], 'is_tax <=' => 0));
                                    if($currentAlloc->num_rows() > 0){
                                        $allocdetail_id = $currentAlloc->row()->allocation_id;

                                        $this->mdl_general->update('ar_receipt_allocation', array('allocation_id' => $allocdetail_id), $detail);
                                    }else{
                                        $detail['status'] = STATUS_NEW;

                                        $this->db->insert('ar_receipt_allocation', $detail);
                                        $insert_id = $this->db->insert_id();

                                        if($insert_id <= 0){
                                            $valid = false;
                                            break;
                                        }
                                    }

                                    //ALLOCATED TAX
                                    if($availableAmount > 0 && $valid){
                                        $detail = array();
                                        $detail['allocationheader_id'] =  $allocHeader['allocationheader_id'];
                                        $detail['receipt_id'] = $allocHeader['receipt_id'];
                                        $detail['invdetail_id'] = $invdetail_ids[$i];
                                        $detail['bookingreceipt_id'] =  0;
                                        $detail['billdetail_id'] = 0;
                                        $detail['is_tax'] = 1;
                                        $detail['is_debitnote'] = 0;
                                        $detail['is_transfer_ar'] = 0;
                                        $detail['allocation_date'] = ymd_from_db($receipt->receipt_date);
                                        $detail['created_by'] = $current_user_id;
                                        $detail['created_date'] = $created_date;

                                        if($invdetail->tax >= $availableAmount){
                                            $detail['allocation_amount'] = $availableAmount;
                                            $availableAmount = 0;
                                        }else{
                                            $detail['allocation_amount'] = $invdetail->tax;
                                            $availableAmount = ($availableAmount - $invdetail->tax);
                                        }

                                        $currentAlloc = $this->db->get_where('ar_receipt_allocation', array('allocationheader_id' => $allocHeader['allocationheader_id'], 'invdetail_id' => $invdetail_ids[$i], 'is_tax >' => 0));
                                        if($currentAlloc->num_rows() > 0){
                                            $allocdetail_id = $currentAlloc->row()->allocation_id;

                                            $this->mdl_general->update('ar_receipt_allocation', array('allocation_id' => $allocdetail_id), $detail);
                                        }else{
                                            $detail['status'] = STATUS_NEW;

                                            $this->db->insert('ar_receipt_allocation', $detail);
                                            $insert_id = $this->db->insert_id();

                                            if($insert_id <= 0){
                                                $valid = false;
                                                break;
                                            }
                                        }

                                    }
                                }else{
                                    //DELETE ALLOCATION

                                    $this->db->delete('ar_receipt_allocation', array('allocationheader_id' => $allocHeader['allocationheader_id'],'invdetail_id' => $invdetail_ids[$i]));
                                }
                            }
                        }
                    }
                }
            }

        }

        return $valid;
    }

    public function xposting_receipt_al(){
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $allocationheader_id = 0;

        if(isset($_POST['allocationheader_id'])){
            $allocationheader_id = $_POST['allocationheader_id'];
        }

        if($allocationheader_id > 0){
            $allocHeader = $this->db->get_where('ar_allocation_header', array('allocationheader_id' => $allocationheader_id));
            if($allocHeader->num_rows() > 0){
                $header = $allocHeader->row_array();

                //BEGIN TRANSACTION
                $this->db->trans_begin();

                if($valid){
                    try {
                        //Update Invoice Detail
                        $allocs = $this->db->get_where('ar_receipt_allocation', array('allocationheader_id' => $allocationheader_id));
                        if($allocs->num_rows() > 0){
                            $inv_ids = array();

                            foreach($allocs->result() as $al){
                                $paidAmount = 0;
                                $paidTax = 0;

                                $invdetail = $this->db->get_where('ar_invoice_detail', array('invdetail_id'=>$al->invdetail_id));
                                if($invdetail->num_rows() > 0){
                                    $invdetail = $invdetail->row();
                                    if($valid){
                                        //UPDATE invoice detail
                                        if($al->is_tax <= 0) {
                                            $paidAmount = $invdetail->paid_amount + $al->allocation_amount;
                                            if ($paidAmount < 0) {
                                                $paidAmount = 0;
                                            } else {
                                                if ($paidAmount > $invdetail->amount) {
                                                    $paidAmount = $invdetail->amount;
                                                }
                                            }
                                            $this->mdl_general->update('ar_invoice_detail', array('invdetail_id' => $al->invdetail_id), array('paid_amount' => $paidAmount));

                                            //Update invoice header

                                        }else{
                                            $paidTax = $invdetail->paid_tax + $al->allocation_amount;
                                            if ($paidTax < 0) {
                                                $paidTax = 0;
                                            } else {
                                                if ($paidTax > $invdetail->tax) {
                                                    $paidTax = $invdetail->tax;
                                                }
                                            }
                                            $this->mdl_general->update('ar_invoice_detail', array('invdetail_id' => $al->invdetail_id), array('paid_tax' => $paidTax));
                                            //Update invoice header

                                        }
                                    }

                                    $found = false;
                                    foreach($inv_ids as $key => $value){
                                        if($key == $invdetail->inv_id){
                                            $found = true;
                                            break;
                                        }
                                    }
                                    if(!$found){
                                        $inv_ids[$invdetail->inv_id] = round($paidAmount + $paidTax);
                                    }else {
                                        $inv_ids[$invdetail->inv_id] += round($paidAmount + $paidTax);
                                    }
                                }
                            }

                            //---------------------------------------------------
                            //UPDATE INVOICE HEADER STATUS TO CLOSED IF FULL PAID
                            //---------------------------------------------------
                            if(count($inv_ids) > 0){
                                foreach ($inv_ids as $key => $value){
                                    $invoice = $this->db->get_where('view_ar_unpaid_invoice',array('inv_id'=> $key));
                                    if($invoice->num_rows() > 0){
                                        $invoice = $invoice->row();
                                        $pending = round($invoice->pending_amount + $invoice->pending_tax,0);
                                        $pending = round($pending - $value);
                                        if($pending <= 0){
                                            $this->mdl_general->update('ar_invoice_header',array('inv_id' => $key), array('status' => STATUS_CLOSED));
                                        }
                                    }else{
                                        $this->mdl_general->update('ar_invoice_header',array('inv_id' => $key), array('status' => STATUS_CLOSED));
                                    }
                                }
                            }
                            //---------------------------------------------------
                        }

                        //UPDATE ar_receipt_allocation
                        $this->mdl_general->update('ar_receipt_allocation', array('allocationheader_id' => $allocationheader_id), array('status' => STATUS_CLOSED));

                        //UPDATE HEADER
                        $data['modified_by'] = my_sess('user_id');
                        $data['modified_date'] = date('Y-m-d H:i:s');
                        $data['status'] = STATUS_CLOSED;
                        $this->mdl_general->update('ar_allocation_header', array('allocationheader_id' => $allocationheader_id), $data);

                        //Insert Log
                        $data_log['user_id'] = my_sess('user_id');
                        $data_log['log_subject'] = get_action_name($data['status'], false) . ' AR Receipt Allocation';
                        $data_log['log_date'] = date('Y-m-d H:i:s');
                        $data_log['reff_id'] = $allocationheader_id;
                        $data_log['feature_id'] = Feature::FEATURE_AR_ALLOC;
                        $data_log['action_type'] = STATUS_CLOSED;
                        $this->db->insert('app_log', $data_log);
                    }catch(Exception $e){
                        $valid = false;
                    }
                }
            }else{
                $valid = false;
            }

            //COMMIT OR ROLLBACK
            if($valid){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be posted. Please try again later.';

                    //$this->session->set_flashdata('flash_message_class', 'danger');
                    //$this->session->set_flashdata('flash_message', 'Transaction can not be posted. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();

                    $result['type'] = '1';
                    $result['message'] ='Allocation successfully posted.'; //$header['alloc_no'] . ' successfully posted.';
                    $result['redirect_link'] = base_url('ar/corporate_bill/receipt_al/3/'. $allocationheader_id .'.tpd');
                }
            }else{
                $this->db->trans_rollback();

                $result['type'] = '0';
                $result['message'] = 'Transaction can not be posted. Please try again later.';
            }

        }

        echo json_encode($result);
    }

    #endregion

    #region Credit Note

    public function credit($type = 1, $id = 0){
        if ($type == 1 || $type == 2) {
            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $data['is_history'] = ($type == 2 ? true : false);
            $this->load->view('layout/header', $data_header);
            if($type == 1){
                $this->load->view('ar/credit/corp_credit_manage.php', $data);
            }else{
                $this->load->view('ar/credit/corp_credit_history.php', $data);
            }
            $this->load->view('layout/footer');
        }else{
            $this->load->library('../controllers/ar/credit');
            $this->credit->corp_credit_form($id);
        }
    }

    #endregion

    #region Debit Note

    public function debit($type = 1, $id = 0){
        if ($type == 1 || $type == 2) {
            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $data['is_history'] = ($type == 2 ? true : false);
            $this->load->view('layout/header', $data_header);
            if($type == 1){
                $this->load->view('ar/debit/corp_debit_manage.php', $data);
            }else{
                $this->load->view('ar/debit/corp_debit_history.php', $data);
            }
            $this->load->view('layout/footer');
        }else{
            $this->load->library('../controllers/ar/debit');
            $this->debit->corp_debit_form($id);
        }
    }

    #endregion

    #region Corporate Refund

    public function refund($type = 1, $id = 0){
        if ($type == 1 || $type == 2) {
            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $data['is_history'] = ($type == 2 ? true : false);
            $this->load->view('layout/header', $data_header);
            if($type == 1){
                $this->load->view('ar/refund/corp_refund_manage.php', $data);
            }else{
                $this->load->view('ar/refund/corp_refund_history.php', $data);
            }
            $this->load->view('layout/footer');
        }else{
            $this->load->library('../controllers/ar/refund');
            $this->refund->corp_refund_form($id);
        }
    }

    #endregion

    #region Corporate Deposit

    public function deposit($type = 1, $id = 0){
        if ($type == 1 || $type == 2) {
            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

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
            if($type == 1){
                $this->load->view('ar/deposit/deposit_manage.php', $data);
            }else{
                $this->load->view('ar/deposit/deposit_history.php', $data);
            }

            $this->load->view('layout/footer');
        }else{
            $this->load->library('../controllers/ar/deposit');
            $this->deposit->deposit_form($id);
        }
    }

    #endregion

    #region Proforma Invoice

    public function proforma($type = 1, $id = 0){
        if ($type == 1 || $type == 2) {
            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $data['is_history'] = ($type == 2 ? true : false);
            $this->load->view('layout/header', $data_header);
            $this->load->view('ar/proforma_inv/proforma_inv_manage.php', $data);
            $this->load->view('layout/footer');
        }else{
            $this->load->library('../controllers/ar/proforma_inv');
            $this->proforma_inv->proforma_inv_form($id);
        }
    }

    #endregion

    #region Deposit Allocation

    public function deposit_al($type = 1, $id = 0){
        if ($type == 1 || $type == 2) {
            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

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
            if($type == 1){
                $this->load->view('ar/deposit/deposit_alloc_manage.php', $data);
            }else{
                $this->load->view('ar/deposit/deposit_alloc_history.php', $data);
            }
            $this->load->view('layout/footer');
        }else{
            $this->load->library('../controllers/ar/deposit');
            $this->deposit->deposit_al_form($id);
        }
    }

    #endregion

    #region Deposit Refund

    public function deposit_refund($type = 1, $id = 0){
        if ($type == 1 || $type == 2) {
            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

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
            if($type == 1){
                $this->load->view('ar/refund/deposit_refund_manage.php', $data);
            }else{
                $this->load->view('ar/refund/deposit_refund_history.php', $data);
            }
            $this->load->view('layout/footer');
        }else{
            $this->load->library('../controllers/ar/refund');
            $this->refund->deposit_refund_form($id);
        }
    }

    #endregion

    #region Delivery Order

    public function delivery($type = 1, $id = 0){
        if ($type == 1 || $type == 2) {
            $data_header = $this->data_header;

            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

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
            if($type == 1){
                $this->load->view('ar/delivery/delivery_manage.php', $data);
            }else{
                $this->load->view('ar/delivery/delivery_history.php', $data);
            }

            $this->load->view('layout/footer');
        }else{
            $this->load->library('../controllers/ar/delivery');
            $this->delivery->delivery_form($id);
        }
    }

    #endregion

    #region Print

    public function pdf_invoice($inv_id = 0, $is_unpaid_only = 0, $paper_size = 'A5', $paper_layout = 'portrait') {
        if($inv_id > 0){
            //Reservation
            $qry = $this->db->get_where('ar_invoice_header', array('inv_id' => $inv_id));
            if($qry->num_rows() > 0){
                $parent = $qry->row();
                $qry = $this->db->query('SELECT * FROM fxnARInvoiceHeaderByStatus(' . $parent->status . ') WHERE inv_id = ' . $inv_id);
                if($qry->num_rows() > 0) {
                    $data['row'] = $qry->row_array();

                    //Company Profile
                    $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                    if($profile->num_rows() > 0){
                        $data['profile'] = $profile->row_array();
                    }

                    //Tenant
                    $folio_caption = 'INVOICE';

                    $bill_info = $data['row']['company_name'];
                    $bill_info .= trim($data['row']['company_address']) != '' ? '<br>' . nl2br($data['row']['company_address']) : '';
                    $bill_info .= trim($data['row']['company_phone']) != '' ? '<br>' . $data['row']['company_phone'] : '';
                    $bill_info .= trim($data['row']['company_pic_name']) != '' ? '<br>Attn. ' . $data['row']['company_pic_name'] : '';

                    $data['folio_title'] = $folio_caption;
                    $data['guest_info'] = $bill_info;

                    //Invoice detail
                    $room_caption = '';
                    $details = $this->db->get_where('fxnARInvoiceDetailByInvID(' . $inv_id . ')');
                    if($details->num_rows() > 0){
                        $data['detail'] = $details->result_array();

                    }

                    $data['room_caption'] = '';//$room_caption;

                    //echo $this->db->last_query();
                    $data['is_unpaid_only'] = $is_unpaid_only > 0 ? true : false;

                    if($paper_size == 'A4'){
                        $this->load->view('ar/bill/pdf_invoice_a4', $data);
                    }else{
                        //$this->load->view('ar/bill/pdf_invoice', $data);
                        $this->load->view('ar/bill/pdf_invoice_a5', $data);
                        //$paper_layout = 'portrait';
                    }

                    $html = $this->output->get_output();

                    $this->load->library('dompdf_gen');

                    //DEFAULT A5 landscape
                    $this->dompdf->set_paper($paper_size, $paper_layout);

                    //$this->dompdf->set_paper("A5", "landscape");

                    $trigger = "<script type='text/javascript'>
                                    this.print();
                                    //this.closeDoc(true);
                                </script>";

                    $this->dompdf->load_html($html);
                    $this->dompdf->render();

                    $this->dompdf->stream($data['row']['inv_no'] . ".pdf", array('Attachment'=>1));

                }
                else {
                    tpd_404();
                }
            }else{
                tpd_404();
            }
        }
        else {
            tpd_404();
        }
    }

    #endregion

    #region Modal

    public function xmodal_corporate_unpaid(){
        $this->load->view('ar/bill/ajax_modal_unpaid');
    }

    public function get_modal_corporate_unpaid($num_index = 0, $company_id = 0, $receipt_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $server_date = date('Y-m-d');

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ar.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array(); //array("view_cs_reservation"=>"view_cs_reservation.reservation_id = ar.reservation_id");
        $iTotalRecords = $this->mdl_finance->countJoin("view_ar_invoice_unpaid_sum AS ar", $joins, $where, $like);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ar.company_name';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ar.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar.sum_pending ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin("ar.* ","view_ar_invoice_unpaid_sum AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $pending_amount = 0;

            $existreceipt = $this->db->query('SELECT ISNULL(SUM(receipt_bankcharges + receipt_paymentamount),0) as receipt_total FROM ar_receipt
                                               WHERE company_id = ' . $row->company_id . ' AND status = ' . STATUS_NEW . '
                                               AND receipt_id <> ' . $receipt_id);
            if($existreceipt->num_rows() > 0){
                $pending_amount = $existreceipt->row()->receipt_total;
            }

            $unallocated = $row->sum_pending - $pending_amount;

            $attr = '';
            $attr .= ' data-company-id="' . $row->company_id . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-amount="' . $unallocated . '"';
            $attr .= ' data-is-invoice="' . $row->is_invoice . '"';
            $attr .= ' data-index="' . $num_index . '" ';

            $text = "";
            if ($company_id == $row->company_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }else{
                $text = "Select";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-record" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            if($unallocated > 0){
                $records["data"][] = array(
                    $row->company_name,
                    format_num($unallocated,0),
                    ($row->is_invoice ? 'Total Invoice' : 'Reserved'),
                    $btn
                );
                $i++;
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function xmodal_unallocated_corp(){
        $this->load->view('ar/refund/ajax_modal_unallocated_corp');
    }

    public function get_modal_unallocated_corp($num_index = 0, $receipt_id = 0, $allocationheader_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $server_date = date('Y-m-d');

        if(isset($_REQUEST['filter_no'])){
            if($_REQUEST['filter_no'] != ''){
                $like['ar.receipt_no'] = $_REQUEST['filter_no'];
            }
        }

        if(isset($_REQUEST['filter_name'])){
            if($_REQUEST['filter_name'] != ''){
                $like['ar.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $where_str = '' ;

        $joins = array();
        $iTotalRecords = $this->mdl_finance->countJoin("fxnARUnallocatedByDateCorp(getdate()) AS ar", $joins, $where, $like, $where_str);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ar.receipt_no asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'ar.receipt_no ' . $_REQUEST['order'][0]['dir'];
            }

            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ar.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin("ar.*","fxnARUnallocatedByDateCorp(getdate()) AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, "", array(), $where_str);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $pending_alloc = 0;

            $existdebit = $this->db->query('SELECT ISNULL(SUM(alloc_amount),0) as total_alloc FROM ar_allocation_header
                                               WHERE receipt_id = ' . $row->receipt_id . ' AND status = ' . STATUS_NEW . '
                                               AND allocationheader_id <> ' . $allocationheader_id);
            if($existdebit->num_rows() > 0){
                $pending_alloc = $existdebit->row()->total_alloc;
            }

            $unallocated = $row->unallocated_amount - $pending_alloc;

            $attr = '';
            $attr .= ' data-receipt-id="' . $row->receipt_id . '" ';
            $attr .= ' data-receipt-no="' . $row->receipt_no . '" ';
            $attr .= ' data-company-id="' . $row->company_id . '" ';
            $attr .= ' data-reservation-id="' . 0 . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-is-invoice="' . $row->is_invoice . '" ';
            $attr .= ' data-amount="' . $unallocated . '"';
            $attr .= ' data-index="' . $num_index . '" ';

            if ($receipt_id == $row->receipt_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            }else{
                $text = "Select";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-receipt" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            if($unallocated > 0){
                $records["data"][] = array(
                    $row->receipt_no,
                    $row->company_name,
                    format_num($unallocated,0),
                    //($row->is_invoice > 0 ? 'Total Invoice' : 'Reserved Payment'),
                    $btn
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