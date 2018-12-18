<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller {

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
        $this->unit_manage();
    }

    #region Manage Item / Service

    public function item_form($id = 0){
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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data['item_id'] = $id;

        if($id > 0){
            /*
            $qry = $this->db->query('select ms_currency_rate.*,ms_transtype.transtype_name, ms_transtype.transtype_desc from ms_currency_rate
                                     join ms_transtype on ms_transtype.transtype_id = ms_currency_rate.transtype_id
                                     where rate_id = ' . $id);

            $data['row'] = $qry->row();
            */
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/setup/item_form', $data);
        $this->load->view('layout/footer');
    }

    public function submit_item(){
        if(isset($_POST)){
            $has_error = false;

            $data['masteritem_code'] = $_POST['item_code'];
            $data['masteritem_desc'] = $_POST['item_desc'];
            $data['masteritem_uom'] = $_POST['uom_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $item_id = 0;
            if($data['masteritem_code'] != ''){
                //Insert Service as Item
                $exist = $this->mdl_general->count('pos_master_item', array('status' => STATUS_NEW,
                    'masteritem_code' => $data['masteritem_code']));

                if($exist <= 0){
                    $data['masteritem_price'] = 0;
                    $data['user_created'] = my_sess('user_id');
                    $data['date_created'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_NEW;

                    $this->db->insert('pos_master_item', $data);
                    $item_id = $this->db->insert_id();
                }
            }

            $itemstock_id = 0;
            if($item_id > 0){
                //Insert Stock
                $stock['masteritem_id'] = $item_id;
                $stock['is_service_item'] = 1;
                $stock['itemstock_uom'] = $data['masteritem_uom'];
                $stock['itemstock_uom_distribution'] = $data['masteritem_uom'];
                $stock['itemstock_current_qty'] = 1;
                $stock['itemstock_min'] = 1;
                $stock['itemstock_max'] = 1;
                $stock['price_lock'] = 1;
                $stock['enable_ar_bill'] = 0;
                $stock['unit_price'] = 0;
                $stock['unit_discount'] = 0;
                $stock['taxtype_id'] = 0;
                $stock['coa_code'] = 0;
                $stock['itemstock_factor'] = 1;
                $stock['created_by'] = my_sess('user_id');
                $stock['created_date'] = date('Y-m-d H:i:s');
                $stock['status'] = STATUS_NEW;

                $this->db->insert('pos_item_stock', $stock);
                $itemstock_id = $this->db->insert_id();
            }

            if($itemstock_id > 0){
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Service successfully added.');
            }else{
                $has_error = true;
            }

            //COMMIT
            if(!$has_error){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();
                }
            }else{
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            if($has_error){
                redirect(base_url('ar/setup/item_form/' . 0 . '.tpd'));
            }
            else {
                redirect(base_url('ar/setup/stock_list/1.tpd'));
            }
        }
    }

    public function stock_list($type = 1){
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
        $this->load->view('ar/setup/stock_list.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_stocks($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();

        $where['view_pos_item_stock.is_service_item >'] = 0;

        $like = array();

        if(isset($_REQUEST['filter_itemcode'])){
            if($_REQUEST['filter_itemcode'] != ''){
                $like['view_pos_item_stock.item_code'] = $_REQUEST['filter_itemcode'];
            }
        }

        if(isset($_REQUEST['filter_itemdesc'])){
            if($_REQUEST['filter_itemdesc'] != ''){
                $like['view_pos_item_stock.item_desc'] = $_REQUEST['filter_itemdesc'];
            }
        }

        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $like['view_pos_item_stock.status'] = $_REQUEST['filter_status'];
            }
        }

        //$joins = array('in_ms_uom'=>'in_ms_uom.uom_id = view_pos_item_stock.itemstock_uom',
        //    'ms_transtype' => 'ms_transtype.transtype_id = ms_currency_rate.transtype_id');
        $iTotalRecords = $this->mdl_finance->countJoin('view_pos_item_stock', array(), $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'view_pos_item_stock.status DESC, view_pos_item_stock.unit_price, view_pos_item_stock.item_code';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                //$order = 'view_pos_item_stock.item_code ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('view_pos_item_stock.*','view_pos_item_stock', array(), $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug2"] = $this->db->last_query();

        //$i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/setup/stock_form/' . $row->itemstock_id) . '.tpd"><i class="fa fa-pencil"></i>&nbsp;Edit</a> </li>';

            if($row->status == STATUS_INACTIVE){
                $records["data"][] = array(
                    '<i class="font-grey-silver">' . $row->item_code . '</i>',
                    '<i class="font-grey-silver">' . $row->item_desc . '</i>',
                    '<i class="font-grey-silver">' . $row->stock_uom . '</i>',
                    //'<i class="font-grey-silver">' . 'x' . $row->itemstock_factor . '</i>',
                    //'<i class="font-grey-silver">' . $row->dist_uom . '</i>',
                    '<i class="font-grey-silver">' . ($row->coa_code > 0 ? $row->coa_code : '')  . '</i>',
                    '<i class="font-grey-silver">' . format_num($row->itemstock_current_qty,0) . '</i>',
                    //'<i class="font-grey-silver">' . format_num($row->itemstock_max,0) . '</i>',
                    '<i class="font-grey-silver">' . format_num($row->unit_price,0) . '</i>',
                    '<i class="font-grey-silver">' . format_num($row->unit_discount,2) . '</i>',
                    '<i class="font-grey-silver">' . ($row->price_lock > 0 ? '<i class="fa fa-lock"/>' : '') . '</i>',
                    //'<i class="font-grey-silver">' . ($row->enable_ar_bill > 0 ? 'YES' : '-') . '</i>',
                    get_status_active($row->status),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            ' . $btn_action . '
                        </ul>
                    </div>');
            }else{
                $records["data"][] = array(
                    $row->item_code,
                    $row->item_desc,
                    $row->stock_uom,
                    //'x' . $row->itemstock_factor,
                    //$row->dist_uom,
                    ($row->coa_code > 0 ? $row->coa_code : '<span class="badge badge-danger">---</span>'),
                    format_num($row->itemstock_current_qty,0),
                    //format_num($row->itemstock_max,0),
                    ($row->unit_price <= 0 ? '<span class="badge badge-danger">' . format_num($row->unit_price,0) . '</span>' : '<span class="badge">' . format_num($row->unit_price,0)) . '</span>',
                    format_num($row->unit_discount,2),
                    ($row->price_lock > 0 ? '<i class="fa fa-lock"/>' : ''),
                    //($row->enable_ar_bill > 0 ? 'YES' : '-'),
                    get_status_active($row->status),
                    '<div class="btn-group">
                        <button class="btn green-meadow btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                            Action&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            ' . $btn_action . '
                        </ul>
                    </div>');
            }
            //$i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function stock_form($id = 0){
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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data['itemstock_id'] = $id;

        $uoms = $this->mdl_general->get('in_ms_uom', array('status ' => STATUS_NEW), array());
        $data['uoms'] = $uoms->result_array();

        $taxtypes = $this->mdl_general->get('tax_type', array('taxtype_wht ' => 0), array());
        $data['taxtypes'] = $taxtypes->result_array();

        if($id > 0){
            $qry = $this->db->query('select * from view_pos_item_stock
                                     where itemstock_id = ' . $id);

            $data['row'] = $qry->row();
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/setup/stock_form', $data);
        $this->load->view('layout/footer');
    }

    public function submit_stock(){
        if(isset($_POST)){
            $itemStockId = $_POST['itemstock_id'];
            $has_error = false;

            $isService = $_POST['is_service_item'];
            $itemDesc = $_POST['item_desc'];

            $data['itemstock_uom'] = $_POST['itemstock_uom'];
            $data['price_lock'] = isset($_POST['price_lock']) ? $_POST['price_lock'] : 0;
            $data['itemstock_min'] = $_POST['itemstock_min'];
            $data['itemstock_max'] = $_POST['itemstock_max'];
            $data['itemstock_factor'] = $_POST['itemstock_factor'];
            $data['itemstock_uom_distribution'] = $_POST['itemstock_uom_distribution'];
            $data['unit_price'] = $_POST['unit_price'];
            $data['unit_discount'] = $_POST['unit_discount'];
            $data['coa_code'] = $_POST['coa_code'];
            $data['status'] = $_POST['status'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            if($itemStockId > 0)
            {
                $stocks = $this->db->get_where('pos_item_stock', array('itemstock_id' => $itemStockId));
                if($stocks->num_rows() > 0){
                    $stock = $stocks->row();

                    //Update Service if necessary
                    if($isService){
                        $service['masteritem_desc'] = $itemDesc;
                        $this->mdl_general->update('pos_master_item', array('masteritem_id' => $stock->masteritem_id), $service);
                    }

                    //Update Stock
                    $data['modified_by'] = my_sess('user_id');
                    $data['modified_date'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('pos_item_stock', array('itemstock_id' => $itemStockId), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Item Stock successfully updated.');
                }
            }else {
                $has_error = true;
            }

            //COMMIT
            if(!$has_error){
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                }
                else
                {
                    $this->db->trans_commit();
                }
            }else{
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            if($has_error){
                redirect(base_url('ar/setup/stock_form/' . $itemStockId. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('ar/setup/stock_list/1.tpd'));
                }
                else {
                    redirect(base_url('ar/setup/stock_form/' . $itemStockId . '.tpd'));
                }
            }
        }
    }
    #endregion

    #region AGENT

    public function agent_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');

        if($type == 1){ //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('frontdesk/setup/agent_list', $data);
            $this->load->view('layout/footer');
        }
        else{ //Form
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('ms_agent', array('agent_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['agent_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('frontdesk/setup/agent_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function agent_delete($id = 0){
        if($id > 0)
        {
            $this->mdl_general->update('ms_agent', array('agent_id' => $id), array('status' => STATUS_DELETE));

            $this->session->set_flashdata('flash_message', 'Record successfully deleted.');
            $this->session->set_flashdata('flash_type', 'alert-success');
        }

        redirect(base_url('frontdesk/setup/agent_manage/1.tpd'));
    }

    public function submit_agent(){
        if(isset($_POST)){
            $agent_id = $_POST['agent_id'];
            $has_error = false;

            $data['agent_name'] = strtoupper($_POST['agent_name']);
            $data['agent_pic'] = $_POST['agent_pic'];
            $data['agent_phone'] = $_POST['agent_phone'];
            $data['agent_email'] = $_POST['agent_email'];
            $data['remark'] = $_POST['remark'];

            if($agent_id > 0)
            {
                $exist = $this->mdl_general->count('ms_agent', array('agent_name' => $data['agent_name'], 'CONVERT(varchar(max),agent_pic)' => $data['agent_pic'], 'agent_id <>' => $agent_id));

                if($exist <= 0){
                    $data['status'] = $_POST['status'];
                    $this->mdl_general->update('ms_agent', array('agent_id' => $agent_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Agent successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Agent already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;
                $data['created_by'] = my_sess('user_id');
                $data['created_date'] = date('Y-m-d H:i:s');

                $exist = $this->mdl_general->count('ms_agent', array(
                    'agent_name' => $data['agent_name'], 'CONVERT(varchar(max),agent_pic)' => $data['agent_pic'])
                );

                if($exist == 0){
                    $this->db->insert('ms_agent', $data);
                    $agent_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Agent successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Agent already exist.');
                }
            }

            if($has_error){
                redirect(base_url('frontdesk/setup/agent_manage/0/' . $agent_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('frontdesk/setup/agent_manage/1.tpd'));
                }
                else {
                    redirect(base_url('frontdesk/setup/agent_manage/0/' . $agent_id . '.tpd'));
                }
            }
        }
    }

    public function agent_list($menuid = 0){
        $where['status <>'] = STATUS_DELETE;
        $like = array();

        if(isset($_REQUEST['filter_agent_name'])){
            if($_REQUEST['filter_agent_name'] != ''){
                $like['agent_name'] = $_REQUEST['filter_agent_name'];
            }
        }
        if(isset($_REQUEST['filter_agent_pic'])){
            if($_REQUEST['filter_agent_pic'] != ''){
                $like['CONVERT(varchar(max),agent_pic)'] = $_REQUEST['filter_agent_pic'];
            }
        }
        /*
        if(isset($_REQUEST['filter_status'])){
            if($_REQUEST['filter_status'] != ''){
                $where['status'] = $_REQUEST['filter_status'];
            }
        }
        */

        $qry_tot = $this->mdl_general->count('ms_agent', $where, $like);

        $iTotalRecords = $qry_tot;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ms_agent.agent_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ms_agent.agent_name ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'CONVERT(varchar(max), ms_agent.agent_pic) ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'CONVERT(varchar(max), ms_agent.agent_phone) ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 4){
                $order = 'CONVERT(varchar(max), ms_agent.agent_email) ' . $_REQUEST['order'][0]['dir'];
            }
            if($_REQUEST['order'][0]['column'] == 5){
                $order = 'ms_agent.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('ms_agent', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $hasDelete = check_session_action($menuid, STATUS_DELETE);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $i . '.',
                $row->agent_name,
                $row->agent_pic,
                nl2br($row->agent_phone),
                nl2br($row->agent_email),
                get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('frontdesk/setup/agent_manage/0/' . $row->agent_id) . '.tpd"><i class="fa fa-search"></i></a>'.
                ($hasDelete ? '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs red-thunderbird tooltips btn-remove" href="javascript:;" data-link="' . base_url('frontdesk/setup/agent_delete/' . $row->agent_id) . '.tpd"><i class="fa fa-times"></i></a>' : '')
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    #endregion

    #region Client

    public function company_manage($type = 1, $id = 0){
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');

        if($type == 1){ //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('ar/setup/company_list', $data);
            $this->load->view('layout/footer');
        }
        else{ //Form
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            if($id > 0){
                $qry = $this->mdl_general->get('ms_company', array('company_id' => $id));
                $data['row'] = $qry->row();
            }

            $data['company_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('ar/setup/company_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function company_delete($id = 0){
        if($id > 0)
        {
            $this->mdl_general->update('ms_company', array('company_id' => $id), array('status' => STATUS_DELETE));

            $this->session->set_flashdata('flash_message', 'Record successfully deleted.');
            $this->session->set_flashdata('flash_type', 'alert-success');
        }

        redirect(base_url('ar/setup/company_manage/1.tpd'));
    }

    public function submit_company(){
        if(isset($_POST)){
            $company_id = $_POST['company_id'];
            $has_error = false;

            $data['company_name'] = $_POST['company_name'];
            $data['company_address'] = $_POST['company_address'];
            $data['company_phone'] = $_POST['company_phone'];
            $data['company_fax'] = $_POST['company_fax'];
            $data['company_cellular'] = $_POST['company_cellular'];
            $data['company_email'] = $_POST['company_email'];
            $data['company_pic_name'] = $_POST['company_pic_name'];
            $data['company_pic_phone'] = $_POST['company_pic_phone'];
            $data['company_pic_email'] = $_POST['company_pic_email'];

            if($company_id > 0)
            {
                $exist = $this->mdl_general->count('ms_company', array('company_name' => $data['company_name'], 'company_id <>' => $company_id));

                if($exist <= 0){
                    $data['status'] = $_POST['status'];
                    $this->mdl_general->update('ms_company', array('company_id' => $company_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Company successfully updated.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Company already exist.');
                }
            }else {
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('ms_company', array(
                        'company_name' => $data['company_name'])
                );

                if($exist == 0){
                    $this->db->insert('ms_company', $data);
                    $company_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Company successfully registered.');
                }
                else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Company already exist.');
                }
            }

            if($has_error){
                redirect(base_url('ar/setup/company_manage/0/' . $company_id. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('ar/setup/company_manage/1.tpd'));
                }
                else {
                    redirect(base_url('ar/setup/company_manage/0/' . $company_id . '.tpd'));
                }
            }
        }
    }

    public function company_list($menuid = 0){
        $where['status <>'] = STATUS_DELETE;
        $like = array();

        if(isset($_REQUEST['filter_company_name'])){
            if($_REQUEST['filter_company_name'] != ''){
                $like['company_name'] = $_REQUEST['filter_company_name'];
            }
        }
        if(isset($_REQUEST['filter_company_pic_name'])){
            if($_REQUEST['filter_company_pic_name'] != ''){
                $like['company_pic_name'] = $_REQUEST['filter_company_pic_name'];
            }
        }
        if(isset($_REQUEST['filter_company_phone'])){
            if($_REQUEST['filter_company_phone'] != ''){
                $like['company_phone'] = $_REQUEST['filter_company_phone'];
            }
        }

        $qry_tot = $this->mdl_general->count('ms_company', $where, $like);

        $iTotalRecords = $qry_tot;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ms_company.company_name asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'ms_company.company_name ' . $_REQUEST['order'][0]['dir'];
            }

        }

        $qry = $this->mdl_general->get('ms_company', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                $i . '.',
                $row->company_name,
                $row->company_phone,
                $row->company_fax,
                $row->company_email,
                $row->company_pic_name,
                $row->company_pic_phone,
                $row->company_pic_email,
                get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('ar/setup/company_manage/0/' . $row->company_id) . '.tpd"><i class="fa fa-search"></i></a>'
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

/* End of file registration.php */
/* Location: ./application/controllers/frondesk/setup.php */