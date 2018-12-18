<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales_order extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sales/M_setup');
        $this->load->model('sales/M_sales_order');
        $this->data_header = array(
            'style' => array(),
            'script' => array(),
            'custom_script' => array(),
            'init_app' => array()
        );

        $this->data_footer = array(
            'footer_script' => array()
        );
    }

    public function so_item_form($type = 0, $so_id = 0)
    {
        $data = array();
        $data_header = $this->data_header;
        $data_footer = $this->data_footer;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
        array_push($data_header['script'], base_url() . 'assets/sales/sales.js');

        $date = date('Y-m-d');

        $data['so_id'] = $so_id;
        if ($so_id > 0) {
            $qry_head = $this->M_sales_order->get_header($so_id);
            $data['row'] = $qry_head->row();
            $data['detail'] = $this->M_sales_order->get_detail($so_id);
            $data['payment'] = $this->M_sales_order->get_payment($so_id);
        }


        $query = $this->M_sales_order->get_so_no($date);
        $data['so_number'] = $query->so_number;
        $this->load->view('layout/header', $data_header);
        $this->load->view('sales/sales_order/so_item_form', $data);
        $this->load->view('layout/footer', $data_footer);
    }

    public function ajax_modal_customer()
    {
        $this->load->view('sales/sales_order/ajax_customer_list');
    }

    public function ajax_customer_list()
    {
        $where['status ='] = 1;
        $like = array();

        if (isset($_REQUEST['filter_name'])) {
            if ($_REQUEST['filter_name'] != '') {
                $like['customer_name'] = $_REQUEST['filter_name'];
            }
        }
        if (isset($_REQUEST['filter_email'])) {
            if ($_REQUEST['filter_email'] != '') {
                $where['email'] = $_REQUEST['filter_email'];
            }
        }
        if (isset($_REQUEST['filter_phone'])) {
            if ($_REQUEST['filter_phone'] != '') {
                $where['customer_phone'] = $_REQUEST['filter_phone'];
            }
        }

        if (isset($_REQUEST['filter_cellular'])) {
            if ($_REQUEST['filter_cellular'] != '') {
                $where['customer_cellular'] = $_REQUEST['filter_cellular'];
            }
        }
        if (isset($_REQUEST['filter_fax'])) {
            if ($_REQUEST['filter_fax'] != '') {
                $where['customer_fax'] = $_REQUEST['filter_fax'];
            }
        }


        $iTotalRecords = $this->M_setup->get_customer_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'customer_id desc ';

        $qry = $this->M_setup->get_customer_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
//        echo $this->db->last_query();
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $records["data"][] = array(
                $i . '.',
                $row->customer_name,
                $row->email,
                $row->customer_phone,
                $row->customer_cellular,
                $row->customer_fax,
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs select" type="button" customer-id="' . $row->customer_id . '" customer-name="' . $row->customer_name . '">
                        Select   <i class="fa fa-plus-square"></i>
                    </button>
                </div>'
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function get_delivery_address()
    {
        $id = $_POST['id'];
        $query = $this->M_sales_order->get_detail_customer($id);
        $result = '';
        foreach ($query->result() as $row) {
            $result .= '<div class="card select-address" style="background-color: #a2ece2; cursor: pointer;}" address-id="' . $row->customer_address_id . '" address-name="' . $row->customer_address . '">
                          <div class="card-body">
                            <blockquote class="blockquote mb">
                              <p>' . $row->customer_address . '</p>
                              <footer class="blockquote-footer">' . $row->country_name . '| ' . $row->customer_district . ' | ' . $row->customer_city . ' | ' . $row->customer_postcode . '</footer>
                            </blockquote>
                          </div>
                        </div>';
        }
        echo $result;
    }

    public function get_invoice_address()
    {
        $id = $_POST['id'];
        $query = $this->M_sales_order->get_detail_customer($id);
        $result = '';
        foreach ($query->result() as $row) {
            $result .= '<div class="card select-invoice" style="background-color: #a2ece2; cursor: pointer;}" address-id="' . $row->customer_address_id . '" address-name="' . $row->customer_address . '">
                          <div class="card-body">
                            <blockquote class="blockquote mb">
                              <p>' . $row->customer_address . '</p>
                              <footer class="blockquote-footer">' . $row->country_name . '| ' . $row->customer_district . ' | ' . $row->customer_city . ' | ' . $row->customer_postcode . '</footer>
                            </blockquote>
                          </div>
                        </div>';
        }
        echo $result;
    }

    public function generate_so_no()
    {
        $parameter = $_POST['date'];
        $a = new DateTime($parameter);
        $date = $a->format('Y-m-d');

        $get_number = $this->M_sales_order->get_so_no($date);
        $result['so_number'] = $get_number->so_number;
        echo json_encode($result);
    }

    public function ajax_payment_list()
    {
        $where['pt.status ='] = 1;
        $like = array();

        if (isset($_REQUEST['filter_name'])) {
            if ($_REQUEST['filter_name'] != '') {
                $like['pt.sp_type_name'] = $_REQUEST['filter_name'];
            }
        }


        $iTotalRecords = $this->M_setup->get_payment_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'pt.sp_type_id desc ';

        $qry = $this->M_setup->get_payment_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
//        echo $this->db->last_query();
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $records["data"][] = array(
                $i . '.',
                $row->sp_type_name,
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs select-payment" type="button" sp-type-id="' . $row->sp_type_id . '" sp-type-name="' . $row->sp_type_name . '">
                        Select   <i class="fa fa-plus-square"></i>
                    </button>
                </div>'
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_modal_payment()
    {
        $this->load->view('sales/sales_order/ajax_payment_list');
    }

    public function ajax_modal_items()
    {
        $this->load->view('sales/sales_order/ajax_items_list');
    }


    public function ajax_items_list()
    {
        $where['im.status ='] = 1;
        $like = array();

        if (isset($_REQUEST['filter_name'])) {
            if ($_REQUEST['filter_name'] != '') {
                $like['im.item_desc'] = $_REQUEST['filter_name'];
            }
        }
        if (isset($_REQUEST['filter_uom'])) {
            if ($_REQUEST['filter_uom'] != '') {
                $like['iu.uom_code'] = $_REQUEST['filter_uom'];
            }
        }
        if (isset($_REQUEST['filter_code'])) {
            if ($_REQUEST['filter_code'] != '') {
                $like['im.item_code'] = $_REQUEST['filter_code'];
            }
        }
        if (isset($_REQUEST['filter_price'])) {
            if ($_REQUEST['filter_price'] != '') {
                $like['im.item_price'] = $_REQUEST['filter_price'];
            }
        }
        $iTotalRecords = $this->M_sales_order->get_items_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'im.item_id desc ';

        $qry = $this->M_sales_order->get_items_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $records["data"][] = array(
                $i . '.',
                $row->item_code,
                $row->item_desc,
                $row->uom_code,
                number_format($row->item_price),
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs select-item" type="button" item-code="' . $row->item_code . '" item-id="' . $row->item_id . '" description="' . $row->item_desc . '" uom="' . $row->uom_code . '" uom-id="' . $row->uom_id . '" price="' . $row->item_price . '" >
                        Select   <i class="fa fa-plus-square"></i>
                    </button>
                </div>'
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function so_entry()
    {
        $this->db->trans_begin();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = array();
        if (isset($_POST)) {
            $so_id = $_POST['so_id'];
            $has_error = false;
            if ($so_id > 0) { //update
                $data_header['so_code'] = $_POST['so_code'];
                $data_header['so_date'] = dmy_to_ymd(trim($_POST['so_date']));
                $data_header['customer_id'] = $_POST['customer'];
                $data_header['currency'] = $_POST['currency'];
                $data_header['rate'] = $_POST['rate'];
                $data_header['request_do_date'] = dmy_to_ymd(trim($_POST['request_do_date']));
                $data_header['sales_id'] = $_POST['sales'];
                $data_header['term_of_payment'] = $_POST['term'];
                $data_header['delivery_address_id'] = $_POST['delivery_address'];
                $data_header['invoice_address_id'] = $_POST['invoice_address'];
                $data_header['remarks'] = $_POST['remarks'];
                $data_header['taxtype_id'] = $_POST['taxtype'];
                $data_header['user_modified'] = my_sess('user_id');
                $data_header['date_modified'] = date('Y-m-d H:i:s.000');
                $this->M_sales_order->update('sales_order_header', array('so_id' => $so_id), $data_header);

                $data_log['so_id'] = $so_id;
                $data_log['log_subject'] = 'Modified ( '.$_POST['so_code'].' )';
                $data_log['approved_id'] = my_sess('user_id');
                $data_log['approved_date'] = date('Y-m-d H:i:s.000');
                $data_log['status'] = STATUS_NEW;
                $this->db->insert('sales_approved_log', $data_log);

                $this->db->delete('sales_order_detail', array('so_id' => $so_id)); //delete detail
                $this->db->delete('sales_order_payment', array('so_id' => $so_id)); //delete payment

                for ($a = 0; $a < count($_POST['status_detail']); $a++) {
                    if ($_POST['status_detail'][$a] == 1) {
                        $data_detail['so_id'] = $so_id;
                        $data_detail['stock_id'] = $_POST['item_id'][$a];
                        $data_detail['stock_qty'] = $_POST['qty'][$a];
                        $data_detail['discount'] = $_POST['discount'][$a];
                        $data_detail['price'] = $_POST['price'][$a];
                        $data_detail['status'] = STATUS_NEW;
                        $this->db->insert('sales_order_detail', $data_detail);//re insert detail
                    }
                }

                if (isset($_POST['status_sp_type'])) {
                    for ($i = 0; $i < count($_POST['status_sp_type']); $i++) {
                        if ($_POST['status_sp_type'][$i] == 1) {
                            $data_payment['so_id'] = $so_id;
                            $data_payment['paymenttype_id'] = $_POST['sp_type_id'][$i];
                            $data_payment['amount'] = $_POST['sp_type_amount'][$i];
                            $data_payment['status'] = STATUS_NEW;
                            $this->db->insert('sales_order_payment', $data_payment); //re insert payment
                        }
                    }
                }

                if ($has_error == false) {
                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully update sales order.');
                }
            } else { //entry
                $valid_header = $this->M_sales_order->get_header_id($_POST['so_code']);

                if (count($valid_header) > 0) {
                    $has_error = true;
                    $result['valid'] = '0';
                    $result['message'] = 'Failed to submit, try again later.';
                } else {
                    $data['so_code'] = $_POST['so_code'];
                    $data['so_date'] = dmy_to_ymd(trim($_POST['so_date']));
                    $data['customer_id'] = $_POST['customer'];
                    $data['currency'] = $_POST['currency'];
                    $data['rate'] = $_POST['rate'];
                    $data['request_do_date'] = dmy_to_ymd(trim($_POST['request_do_date']));
                    $data['sales_id'] = $_POST['sales'];
                    $data['term_of_payment'] = $_POST['term'];
                    $data['delivery_address_id'] = $_POST['delivery_address'];
                    $data['invoice_address_id'] = $_POST['invoice_address'];
                    $data['remarks'] = $_POST['remarks'];
                    $data['taxtype_id'] = $_POST['taxtype'];
                    $data['so_status'] = 0; //so approval
                    $data['user_created'] = my_sess('user_id');
                    $data['date_created'] = date('Y-m-d H:i:s.000');
                    $data['user_modified'] = my_sess('user_id');
                    $data['date_modified'] = date('Y-m-d H:i:s.000');
                    $data['status'] = STATUS_NEW;
                    $this->db->insert('sales_order_header', $data);//insert header

                    $get_header_id = $this->M_sales_order->get_header_id($_POST['so_code']);

                    for ($a = 0; $a < count($_POST['status_detail']); $a++) {
                        if ($_POST['status_detail'][$a] == 1) {
                            $data_detail['so_id'] = $get_header_id->so_id;
                            $data_detail['stock_id'] = $_POST['item_id'][$a];
                            $data_detail['stock_qty'] = $_POST['qty'][$a];
                            $data_detail['discount'] = $_POST['discount'][$a];
                            $data_detail['price'] = $_POST['price'][$a];
                            $data_detail['status'] = STATUS_NEW;
                            $this->db->insert('sales_order_detail', $data_detail);//insert detail
                        }
                    }
                    if (isset($_POST['status_sp_type'])) {
                        for ($i = 0; $i < count($_POST['status_sp_type']); $i++) {
                            if ($_POST['status_sp_type'][$i] == 1) {
                                $data_payment['so_id'] = $get_header_id->so_id;
                                $data_payment['paymenttype_id'] = $_POST['sp_type_id'][$i];
                                $data_payment['amount'] = $_POST['sp_type_amount'][$i];
                                $data_payment['status'] = STATUS_NEW;
                                $this->db->insert('sales_order_payment', $data_payment);//insert header
                            }
                        }
                    }
                }
                if ($has_error == false) {
                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully add sales order.');
                }
            }
            if ($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Failed submit sales order, try again later.');
                } else {
                    $this->db->trans_commit();
                    if (isset($_POST['save_close'])) {
                        $result['link'] = base_url('sales/sales_order/so_list/0.tpd');
                    } else {
                        $result['link'] = base_url('sales/sales_order/so_item_form/0/' . $so_id . '.tpd');
                    }
                }
            }
        }
        echo json_encode($result);

    }

    public function so_list()
    {
        $data_header = $this->data_header;
        $data_footer = $this->data_footer;
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');


        $this->load->view('layout/header', $data_header);
        $this->load->view('sales/sales_order/so_list');
        $this->load->view('layout/footer', $data_footer);
    }

    public function so_ajax_list($menu_id = 0)
    {
        $where['sh.status <>'] = STATUS_DELETE;
        $like = array();

        if (isset($_REQUEST['filter_code'])) {
            if ($_REQUEST['filter_code'] != '') {
                $like['sh.so_code'] = $_REQUEST['filter_code'];
            }
        }
        if (isset($_REQUEST['filter_date_from'])) {
            if ($_REQUEST['filter_date_from'] != '') {
                $where['sh.so_date >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if (isset($_REQUEST['filter_date_to'])) {
            if ($_REQUEST['filter_date_to'] != '') {
                $where['sh.so_date <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }

        if (isset($_REQUEST['filter_req_do_date_from'])) {
            if ($_REQUEST['filter_req_do_date_from'] != '') {
                $where['sh.request_do_date >='] = dmy_to_ymd($_REQUEST['filter_req_do_date_from']);
            }
        }
        if (isset($_REQUEST['filter_req_do_date_to'])) {
            if ($_REQUEST['filter_req_do_date_to'] != '') {
                $where['sh.request_do_date <='] = dmy_to_ymd($_REQUEST['filter_req_do_date_to']);
            }
        }
        if (isset($_REQUEST['filter_customer'])) {
            if ($_REQUEST['filter_customer'] != '') {
                $like['cst.customer_name'] = $_REQUEST['filter_customer'];
            }
        }
        if (isset($_REQUEST['filter_sales'])) {
            if ($_REQUEST['filter_sales'] != '') {
                $like['sls.sales_name'] = $_REQUEST['filter_sales'];
            }
        }
        if (isset($_REQUEST['filter_remarks'])) {
            if ($_REQUEST['filter_remarks'] != '') {
                $like['sh.remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['sh.status'] = $_REQUEST['filter_status'];
            }
        }
        $iTotalRecords = $this->M_sales_order->get_so_ajax_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'sh.so_id desc ';

        $qry = $this->M_sales_order->get_so_ajax_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $i = $iDisplayStart + 1;

        foreach ($qry->result() as $row) {
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('sales/sales_order/so_item_form/0/' . $row->so_id) . '.tpd">View</a> </li>';
            if ($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE) {
                if (check_session_action($menu_id, STATUS_PROCESS)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_PROCESS . '" data-id="' . $row->so_id . '" data-code="' . $row->so_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_PROCESS, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_PROCESS, false))) . '</a></li>';
                }
                if (check_session_action($menu_id, STATUS_CANCEL)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_CANCEL . '" data-id="' . $row->so_id . '" data-code="' . $row->so_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '" >' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            } else if ($row->status == STATUS_APPROVE) {
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li> <a href="' . site_url('purchasing/pr/pdf_pr/' . $row->so_id) . '" target="_blank">Print</a> </li>';
                }
                if (check_session_action($menu_id, STATUS_CLOSED)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_CLOSED . '" data-id="' . $row->so_id . '" data-code="' . $row->so_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CLOSED, false))) . '"  >' . ucwords(strtolower(get_action_name(STATUS_CLOSED, false))) . '</a> </li>';
                }
            }else if($row->status == STATUS_PROCESS) {
                if (check_session_action($menu_id, STATUS_APPROVE)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_APPROVE . '" data-id="' . $row->so_id . '" data-code="' . $row->so_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '</a> </li>';
                }
                if (check_session_action($menu_id, STATUS_DISAPPROVE)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_DISAPPROVE . '" data-id="' . $row->so_id . '" data-code="' . $row->so_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '" >' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '</a> </li>';
                }
            } else if ($row->status == STATUS_CLOSED) {
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li> <a href="' . site_url('purchasing/pr/pdf_pr/' . $row->so_id) . '" target="_blank">Print</a> </li>';
                }
            }
            $status = get_status_name($row->status);
            $records["data"][] = array(
                $i . '.',
                $row->so_code,
                dmy_from_db($row->so_date),
                dmy_from_db($row->request_do_date),
                $row->customer_name,
                $row->sales_name,
                $row->remarks,
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

    public function process_ajax_action()
    {
        $this->db->trans_begin();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $so_id = $_POST['so_id'];

        if (isset($_POST)) {

            $check_approval_header = $this->M_sales_order->check_so_status($so_id)->row();
            $get_next_approval = $this->M_sales_order->get_approval($check_approval_header->so_status)->row();
            $data['approved_by'] = my_sess('user_id');
            $data['approved_date'] = date('Y-m-d H:i:s.000');
            $data['so_status'] = $get_next_approval->level;
            $data['status'] = STATUS_PROCESS;
            $this->M_sales_order->update('sales_order_header', array('so_id' => $so_id), $data);


            $data_log['so_id'] = $so_id;
            $data_log['log_subject'] = 'Process SO ('.$check_approval_header->so_code.') ';
            $data_log['approved_id'] = my_sess('user_id');
            $data_log['approved_date'] = date('Y-m-d H:i:s.000');
            $data_log['status'] = STATUS_NEW;
            $this->db->insert('sales_approved_log', $data_log);


            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed submit sales order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/sales_order/so_list/0.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully process sales order.');
            }

        }

        echo json_encode($result);

    }

    public function approve_ajax_action()
    {
        $this->db->trans_begin();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $so_id = $_POST['so_id'];

        if (isset($_POST)) {
            //a = get last level from sales_approve
            //b = get so_status
            //if a == b ->update to approved
            //else update so_status to next approving
            $last_approval = $this->M_sales_order->get_last_approval($so_id)->row();
            $current_approval_status = $this->M_sales_order->check_so_status($so_id)->row();
            if ($last_approval->last_level == $current_approval_status->so_status) {
                $data['approved_by'] = my_sess('user_id');
                $data['approved_date'] = date('Y-m-d H:i:s.000');
                $data['status'] = STATUS_APPROVE;
                $this->M_sales_order->update('sales_order_header', array('so_id' => $so_id), $data);


                //insert journal


                $data_log['so_id'] = $so_id;
                $data_log['log_subject'] = 'Approved SO ( '.$current_approval_status->so_code.' )';
                $data_log['approved_id'] = my_sess('user_id');
                $data_log['approved_date'] = date('Y-m-d H:i:s.000');
                $data_log['status'] = STATUS_NEW;
                $this->db->insert('sales_approved_log', $data_log);
            } else {
                $check_approval_header = $this->M_sales_order->check_so_status($so_id)->row();
                $get_next_approval = $this->M_sales_order->get_approval($check_approval_header->so_status)->row();
                $data['approved_by'] = my_sess('user_id');
                $data['approved_date'] = date('Y-m-d H:i:s.000');
                $data['so_status'] = $get_next_approval->level;
                $this->M_sales_order->update('sales_order_header', array('so_id' => $so_id), $data);

                $data_log['so_id'] = $so_id;
                $data_log['log_subject'] = 'Approved SO ( '.$check_approval_header->so_code.' )';
                $data_log['approved_id'] = my_sess('user_id');
                $data_log['approved_date'] = date('Y-m-d H:i:s.000');
                $data_log['status'] = STATUS_NEW;
                $this->db->insert('sales_approved_log', $data_log);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed submit sales order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/sales_order/so_list/0.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully approved sales order.');
            }

        }

        echo json_encode($result);
    }

    public function disapprove_ajax_action()
    {
        $this->db->trans_begin();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $so_id = $_POST['so_id'];
        $reason = $_POST['reason'];
        if (isset($_POST)) {
            $get_so_code = $this->M_sales_order->check_so_status($so_id)->row();
            $data['approved_by'] = my_sess('user_id');
            $data['approved_date'] = date('Y-m-d H:i:s.000');
            $data['status'] = STATUS_DISAPPROVE;
            $data['so_status'] = 0;
            $this->M_sales_order->update('sales_order_header', array('so_id' => $so_id), $data);

            $data_log['so_id'] = $so_id;
            $data_log['log_subject'] = 'Disapproved SO ( '.$get_so_code->so_code.' )';
            $data_log['approved_id'] = my_sess('user_id');
            $data_log['approved_date'] = date('Y-m-d H:i:s.000');
            $data_log['remark'] = $reason;
            $data_log['status'] = STATUS_NEW;
            $this->db->insert('sales_approved_log', $data_log);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed process sales order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/sales_order/so_list/0.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully disapproved sales order.');
            }

        }

        echo json_encode($result);
    }

    public function cancel_ajax_action()
    {
        $this->db->trans_begin();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $so_id = $_POST['so_id'];
        $reason = $_POST['reason'];
        if (isset($_POST)) {
            $get_so_code = $this->M_sales_order->check_so_status($so_id)->row();
            $data['approved_by'] = my_sess('user_id');
            $data['approved_date'] = date('Y-m-d H:i:s.000');
            $data['status'] = STATUS_CANCEL;
            $data['so_status'] = 0;
            $this->M_sales_order->update('sales_order_header', array('so_id' => $so_id), $data);

            $data_log['so_id'] = $so_id;
            $data_log['log_subject'] = 'Cancel SO ( '.$get_so_code->so_code.' )';
            $data_log['approved_id'] = my_sess('user_id');
            $data_log['approved_date'] = date('Y-m-d H:i:s.000');
            $data_log['remark'] = $reason;
            $data_log['status'] = STATUS_NEW;
            $this->db->insert('sales_approved_log', $data_log);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed process sales order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/sales_order/so_list/0.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully cancel sales order.');
            }

        }

        echo json_encode($result);
    }

    public function closed_ajax_action()
    {
        $this->db->trans_begin();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $so_id = $_POST['so_id'];
        $reason = $_POST['reason'];
        if (isset($_POST)) {
            $get_so_code = $this->M_sales_order->check_so_status($so_id)->row();
            $data['approved_by'] = my_sess('user_id');
            $data['approved_date'] = date('Y-m-d H:i:s.000');
            $data['status'] = STATUS_CLOSED;
            $this->M_sales_order->update('sales_order_header', array('so_id' => $so_id), $data);

            $data_log['so_id'] = $so_id;
            $data_log['log_subject'] = 'Closed SO ( '.$get_so_code->so_code.' )';
            $data_log['approved_id'] = my_sess('user_id');
            $data_log['approved_date'] = date('Y-m-d H:i:s.000');
            $data_log['remark'] = $reason;
            $data_log['status'] = STATUS_NEW;
            $this->db->insert('sales_approved_log', $data_log);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed process sales order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/sales_order/so_list/0.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully cancel sales order.');
            }

        }

        echo json_encode($result);
    }

}