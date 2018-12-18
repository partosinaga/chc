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
		$this->stock_list();
	}

    #region Item List

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
        $this->load->view('pos/item_list.php', $data);
        $this->load->view('layout/footer');
    }

    public function get_stocks($menu_id = 0){
        $this->load->model('finance/mdl_finance');

        $where = array();

        $where['view_pos_item_stock.is_service_item <='] = 0;

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
            $btn_action .= '<li> <a href="' . base_url('pos/setup/stock_form/' . $row->itemstock_id) . '.tpd"><i class="fa fa-pencil"></i>&nbsp;Edit</a> </li>';

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
        $this->load->view('pos/stock_form', $data);
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
                redirect(base_url('pos/setup/stock_form/' . $itemStockId. '.tpd'));
            }
            else {
                if(isset($_POST['save_close'])){
                    redirect(base_url('pos/setup/stock_list/1.tpd'));
                }
                else {
                    redirect(base_url('pos/setup/stock_form/' . $itemStockId . '.tpd'));
                }
            }
        }
    }
    #endregion
}

/* End of file registration.php */
/* Location: ./application/controllers/frondesk/registration.php */