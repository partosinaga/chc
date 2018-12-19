<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Delivery_order extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sales/M_delivery_order');
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

    public function menu_manage($type = 0, $do_id =0)
    {
        $data = array();
        $data_header = $this->data_header;

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
        array_push($data_header['script'], base_url() . 'assets/sales/do.js');

        $data['do_id'] = 0;
        if ($type == 0) { //form
            $data['do_id'] = $do_id;
            if ($do_id > 0) {
                $qry_head = $this->M_delivery_order->get_header($do_id);
                $data['row'] = $qry_head->row();
                $data['so_stock_qty'] = $this->M_delivery_order->get_stock_qty($data['row']->so_id);
                $data['max_delivery'] = $this->M_delivery_order->get_max_do_qty($data['row']->so_id, $do_id);
                $data['detail'] = $this->M_delivery_order->get_detail($do_id);

            }
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/do/do_form', $data);
            $this->load->view('layout/footer');
        } else {
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/do/do_list', $data);
            $this->load->view('layout/footer');
        }

    }

    public function generate_do_no()
    {
        $parameter = $_POST['date'];
        $a = new DateTime($parameter);
        $date = $a->format('Y-m-d');

        $get_number = $this->M_delivery_order->get_so_no($date);
        $result['do_number'] = $get_number->do_number;
        echo json_encode($result);
    }

    public function ajax_modal_customer()
    {
        $this->load->view('sales/do/ajax_customer_list');
    }

    public function ajax_modal_so()
    {
        $this->load->view('sales/do/ajax_so_list');
    }

    public function ajax_modal_delivery()
    {
        $this->load->view('sales/do/ajax_delivery_list');
    }

    public function ajax_so_list($cust_id = 0)
    {
        $where['h.customer_id ='] = $cust_id;
        $where['h.status ='] = STATUS_APPROVE;
        $like = array();

        if (isset($_REQUEST['filter_code'])) {
            if ($_REQUEST['filter_code'] != '') {
                $like['h.so_code'] = $_REQUEST['filter_code'];
            }
        }
        if (isset($_REQUEST['filter_date'])) {
            if ($_REQUEST['filter_date'] != '') {
                $like['h.so_date'] = $_REQUEST['filter_date'];
            }
        }
        if (isset($_REQUEST['filter_req_date'])) {
            if ($_REQUEST['filter_req_date'] != '') {
                $like['h.request_do_date'] = $_REQUEST['filter_req_date'];
            }
        }
        if (isset($_REQUEST['filter_deliv_address'])) {
            if ($_REQUEST['filter_deliv_address'] != '') {
                $like['a.customer_address'] = $_REQUEST['filter_deliv_address'];
            }
        }

        $iTotalRecords = $this->M_delivery_order->get_so_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'h.so_id desc ';

        $qry = $this->M_delivery_order->get_so_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
//        echo $this->db->last_query();
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $records["data"][] = array(
                $i . '.',
                $row->so_code,
                dmy_from_db($row->so_date),
                dmy_from_db($row->request_do_date),
                $row->customer_address,
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs select-so" type="button" so-code="' . $row->so_code . '" so-id="' . $row->so_id . '" deliv-address="' . $row->customer_address . '" deliv-address-id="' . $row->delivery_address_id . '">
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


    public function ajax_delivery_list()
    {
        $where['status ='] = STATUS_NEW;
        $like = array();

        if (isset($_REQUEST['filter_name'])) {
            if ($_REQUEST['filter_name'] != '') {
                $like['delivery_type_name'] = $_REQUEST['filter_name'];
            }
        }
        if (isset($_REQUEST['filter_desc'])) {
            if ($_REQUEST['filter_desc'] != '') {
                $like['delivery_type_desc'] = $_REQUEST['filter_desc'];
            }
        }

        $iTotalRecords = $this->M_delivery_order->get_delivery_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'delivery_type_id desc ';

        $qry = $this->M_delivery_order->get_delivery_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $records["data"][] = array(
                $i . '.',
                $row->delivery_type_name,
                $row->delivery_type_desc,
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs select-deliv" type="button" deliv-id="' . $row->delivery_type_id . '" deliv-name="' . $row->delivery_type_name . '" >
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

    public function ajax_modal_items()
    {
        $this->load->view('sales/do/ajax_items_list');
    }

    public function ajax_items_list($so_id = 0)
    {
        $where['sod.status ='] = STATUS_NEW;
        $where['sod.so_id ='] = $so_id;
        $like = array();

        if (isset($_REQUEST['filter_code'])) {
            if ($_REQUEST['filter_code'] != '') {
                $like['im.item_code'] = $_REQUEST['filter_code'];
            }
        }
        if (isset($_REQUEST['filter_desc'])) {
            if ($_REQUEST['filter_desc'] != '') {
                $like['im.item_desc'] = $_REQUEST['filter_desc'];
            }
        }
        if (isset($_REQUEST['filter_qty'])) {
            if ($_REQUEST['filter_qty'] != '') {
                $like['sod.stock_qty'] = $_REQUEST['filter_qty'];
            }
        }
        if (isset($_REQUEST['filter_uom'])) {
            if ($_REQUEST['filter_uom'] != '') {
                $like['um.uom_code'] = $_REQUEST['filter_uom'];
            }
        }
        $iTotalRecords = $this->M_delivery_order->get_items_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'sod.so_id desc ';

        $qry = $this->M_delivery_order->get_items_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $get_exist_do = $this->M_delivery_order->get_existing_do($so_id);
//        echo $this->db->last_query();
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $qty_remain = $row->stock_qty;
            foreach ($get_exist_do->result() as $row_exist) {

                if ($row->stock_id == $row_exist->stock_id) {
                    $qty_remain = $row->stock_qty - $row_exist->stock_delivered;
                    break;
                }

            }
            $records["data"][] = array(
                $i . '.',
                $row->item_code,
                $row->item_desc,
                $row->uom_code,
                $row->stock_qty,
                $qty_remain,
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs select-item" type="button" item-id="' . $row->stock_id . '" item-code="' . $row->item_code . '" item-desc="' . $row->item_desc . '" qty="' . $row->stock_qty . '" uom="' . $row->uom_code . '" qty-remain="' . $qty_remain . '" item-price="'.$row->price.'">
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

    public function do_entry()
    {
        $this->db->trans_begin();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = array();
        if (isset($_POST)) {
            $do_id = $_POST['do_id'];
            $has_error = false;
            if ($do_id > 0) { //update
                if ($has_error == false) {
                    $data_header['do_code'] = $_POST['do_code'];
                    $data_header['do_date'] = dmy_to_ymd(trim($_POST['do_date']));
                    $data_header['customer_id'] = $_POST['customer'];
                    $data_header['so_id'] = $_POST['so_id'];
                    $data_header['delivery_by'] = $_POST['delivery'];;
                    $data_header['delivery_address'] = $_POST['delivery_address'];
                    $data_header['remarks'] = $_POST['remarks'];
                    $data_header['do_status'] = 0; //do approval, need ask do dimas add column
                    $data_header['user_created'] = my_sess('user_id');
                    $data_header['date_created'] = date('Y-m-d H:i:s.000');
                    $data_header['user_modified'] = my_sess('user_id');
                    $data_header['date_modified'] = date('Y-m-d H:i:s.000');
                    $data_header['status'] = STATUS_NEW;
                    $this->M_delivery_order->update('delivery_order_header',array('do_id' => $do_id),$data_header);//insert header


                    $this->db->delete('delivery_order_detail', array('do_id' => $do_id));
                    for ($a = 0; $a < count($_POST['status_detail']); $a++) {
                        if ($_POST['status_detail'][$a] == 1) {
                            $data_detail['do_id'] = $do_id;
                            $data_detail['stock_id'] = $_POST['item_id'][$a];
                            $data_detail['delivery_qty'] = $_POST['delivery_qty'][$a];
                            $data_detail['price'] = $_POST['item_price'][$a];
                            $this->db->insert('delivery_order_detail', $data_detail);//insert detail
                        }
                    }

                    $data_log['do_id'] = $do_id;
                    $data_log['log_subject'] = 'Modified ( '.$_POST['do_code'].' )';
                    $data_log['approved_id'] = my_sess('user_id');
                    $data_log['approved_date'] = date('Y-m-d H:i:s.000');
                    $data_log['status'] = STATUS_NEW;
                    $this->db->insert('sales_approved_log', $data_log);


                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Update delivery order here.');
                }
            } else { //entry
                $valid_header = $this->M_delivery_order->get_header_id($_POST['do_code']);

                if (count($valid_header) > 0) {
                    $has_error = true;
                    $result['valid'] = '0';
                    $result['message'] = 'Failed to submit, try again later.';
                } else {
                    $data_header['do_code'] = $_POST['do_code'];
                    $data_header['do_date'] = dmy_to_ymd(trim($_POST['do_date']));
                    $data_header['customer_id'] = $_POST['customer'];
                    $data_header['so_id'] = $_POST['so_id'];
                    $data_header['delivery_by'] = $_POST['delivery'];;
                    $data_header['delivery_address'] = $_POST['delivery_address'];
                    $data_header['remarks'] = $_POST['remarks'];
                    $data_header['do_status'] = 0; //do approval,
                    $data_header['user_created'] = my_sess('user_id');
                    $data_header['date_created'] = date('Y-m-d H:i:s.000');
                    $data_header['user_modified'] = my_sess('user_id');
                    $data_header['date_modified'] = date('Y-m-d H:i:s.000');
                    $data_header['status'] = STATUS_NEW;
                    $this->db->insert('delivery_order_header', $data_header);//insert header


                    $get_header_id = $this->M_delivery_order->get_header_id($_POST['do_code']);
                    for ($a = 0; $a < count($_POST['status_detail']); $a++) {
                        if ($_POST['status_detail'][$a] == 1) {
                            $data_detail['do_id'] = $get_header_id->do_id;
                            $data_detail['stock_id'] = $_POST['item_id'][$a];
                            $data_detail['delivery_qty'] = $_POST['delivery_qty'][$a];
                            $data_detail['price'] = $_POST['item_price'][$a];
                            $this->db->insert('delivery_order_detail', $data_detail);//insert detail
                        }
                    }
                }
                if ($has_error == false) {
                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully add delivery order.');
                }
            }
            if ($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Failed submit sales delivery, try again later.');
                } else {
                    $this->db->trans_commit();
                    if (isset($_POST['save_close'])) {
                        $result['link'] = base_url('sales/delivery_order/menu_manage/1.tpd');
                    } else {
                        $result['link'] = base_url('sales/delivery_order/menu_manage/0/' . $do_id . '.tpd');
                    }
                }
            }
        }
        echo json_encode($result);
    }

    public function do_ajax_list($menu_id = 0)
    {
        $where['h.status <>'] = STATUS_DELETE;
        $like = array();

        if (isset($_REQUEST['filter_do_code'])) {
            if ($_REQUEST['filter_do_code'] != '') {
                $like['h.do_code'] = $_REQUEST['filter_do_code'];
            }
        }
        if (isset($_REQUEST['filter_so_code'])) {
            if ($_REQUEST['filter_so_code'] != '') {
                $like['hs.so_code'] = $_REQUEST['filter_so_code'];
            }
        }
        if (isset($_REQUEST['filter_do_date_from'])) {
            if ($_REQUEST['filter_do_date_from'] != '') {
                $where['h.do_date >='] = dmy_to_ymd($_REQUEST['filter_do_date_from']);
            }
        }
        if (isset($_REQUEST['filter_do_date_to'])) {
            if ($_REQUEST['filter_do_date_to'] != '') {
                $where['h.do_date <='] = dmy_to_ymd($_REQUEST['filter_do_date_to']);
            }
        }
        if (isset($_REQUEST['filter_customer'])) {
            if ($_REQUEST['filter_customer'] != '') {
                $like['c.customer_name'] = $_REQUEST['filter_customer'];
            }
        }
        if (isset($_REQUEST['filter_delivery_address'])) {
            if ($_REQUEST['filter_delivery_address'] != '') {
                $like['ms.customer_address'] = $_REQUEST['filter_delivery_address'];
            }
        }
        if (isset($_REQUEST['filter_remarks'])) {
            if ($_REQUEST['filter_remarks'] != '') {
                $like['h.remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['h.status'] = $_REQUEST['filter_status'];
            }
        }
        $iTotalRecords = $this->M_delivery_order->get_do_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'h.do_id desc ';

        $qry = $this->M_delivery_order->get_do_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $i = $iDisplayStart + 1;

        foreach ($qry->result() as $row) {
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('sales/delivery_order/menu_manage/0/' . $row->do_id) . '.tpd">View</a> </li>';
            if ($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE) {
                if (check_session_action($menu_id, STATUS_PROCESS)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_PROCESS . '" data-id="' . $row->do_id . '" data-code="' . $row->do_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_PROCESS, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_PROCESS, false))) . '</a></li>';
                }
                if (check_session_action($menu_id, STATUS_CANCEL)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_CANCEL . '" data-id="' . $row->do_id . '" data-code="' . $row->do_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '" >' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            } else if ($row->status == STATUS_APPROVE) {
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li> <a href="' . site_url('purchasing/pr/pdf_pr/' . $row->do_id) . '" target="_blank">Print</a> </li>';
                }
                if (check_session_action($menu_id, STATUS_CLOSED)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_CLOSED . '" data-id="' . $row->do_id . '" data-code="' . $row->do_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CLOSED, false))) . '"  >' . ucwords(strtolower(get_action_name(STATUS_CLOSED, false))) . '</a> </li>';
                }
            }else if($row->status == STATUS_PROCESS) {
                if (check_session_action($menu_id, STATUS_APPROVE)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_APPROVE . '" data-id="' . $row->do_id . '" data-code="' . $row->do_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '</a> </li>';
                }
                if (check_session_action($menu_id, STATUS_DISAPPROVE)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_DISAPPROVE . '" data-id="' . $row->do_id . '" data-code="' . $row->do_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '" >' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '</a> </li>';
                }
            } else if ($row->status == STATUS_CLOSED) {
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li> <a href="' . site_url('purchasing/pr/pdf_pr/' . $row->do_id) . '" target="_blank">Print</a> </li>';
                }
            }
            $records["data"][] = array(
                $i . '.',
                $row->do_code,
                $row->so_code,
                dmy_from_db($row->do_date),
                $row->customer_name,
                $row->customer_address,
                $row->remarks,
                get_status_name($row->status),
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
        $do_id = $_POST['do_id'];

        if (isset($_POST)) {

            $check_approval_header = $this->M_delivery_order->check_so_status($do_id)->row();
            $get_next_approval = $this->M_delivery_order->get_approval($check_approval_header->do_status)->row();

            $data['do_status'] = $get_next_approval->level;
            $data['status'] = STATUS_PROCESS;
            $data['approved_by'] = my_sess('user_id');
            $data['approved_date'] = date('Y-m-d H:i:s.000');
            $this->M_delivery_order->update('delivery_order_header', array('do_id' => $do_id), $data);


            $data_log['do_id'] = $do_id;
            $data_log['log_subject'] = 'Process DO ('.$check_approval_header->do_code.') ';
            $data_log['approved_id'] = my_sess('user_id');
            $data_log['approved_date'] = date('Y-m-d H:i:s.000');
            $data_log['status'] = STATUS_NEW;
            $this->db->insert('sales_approved_log', $data_log);


            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed submit delivery order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/delivery_order/menu_manage/1.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully process delivery order.');
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
        $do_id = $_POST['do_id'];

        if (isset($_POST)) {
            //a = get last level from sales_approve
            //b = get so_status
            //if a == b ->update to approved
            //else update so_status to next approving
            $last_approval = $this->M_delivery_order->get_last_approval($do_id)->row();
            $current_approval_status = $this->M_delivery_order->check_so_status($do_id)->row();
            if ($last_approval->last_level == $current_approval_status->do_status) {
                $data['status'] = STATUS_APPROVE;
                $data['approved_by'] = my_sess('user_id');
                $data['approved_date'] = date('Y-m-d H:i:s.000');
                $this->M_delivery_order->update('delivery_order_header', array('do_id' => $do_id), $data);

                //get journal amount
                $qry = "SELECT SUM( delivery_qty * price ) as journal_amount FROM delivery_order_detail WHERE do_id = '".$do_id."' ";
                $journal_amount = $this->db->query($qry)->row();
                //end of get journal amount

                //create journal
                //journal header
                $data_journal_header['journal_date'] = date('Y-m-d H:i:s.000');
                $data_journal_header['journal_amount'] = $journal_amount->journal_amount;
                $data_journal_header['journal_remarks'] = $current_approval_status->remarks;
                $data_journal_header['journal_no'] = $current_approval_status->do_code;
                $data_journal_header['postedmonth'] = date('m');
                $data_journal_header['postedyear'] = date('Y');
                $data_journal_header['modul'] = GLMOD::GL_MOD_DO;
                $data_journal_header['created_by'] = my_sess('user_id');
                $data_journal_header['created_date'] = date('Y-m-d H:i:s.000');
                $data_journal_header['status'] = STATUS_NEW;
                $this->db->insert('gl_postjournal_header', $data_journal_header);
                // end of journal header

                //journal detail
                $postheader_id = $this->M_delivery_order->get_gl_postjournal_header_id($current_approval_status->do_code)->row();
                $debit_journal = $this->M_delivery_order->get_do_debit(FNSpec::ITEM_TRANSIT)->row();

                $data_journal_debit['postheader_id'] = $postheader_id->postheader_id;
                $data_journal_debit['coa_id'] = $debit_journal->coa_id;
                $data_journal_debit['coa_code'] = $debit_journal->coa_code;
                $data_journal_debit['journal_note'] = $postheader_id->journal_remarks;
                $data_journal_debit['journal_debit'] = $journal_amount->journal_amount;
                $data_journal_debit['journal_credit'] = 0;
                $data_journal_debit['dept_id'] = my_sess('department_id');
                $data_journal_debit['status'] = STATUS_NEW;
                $this->db->insert('gl_postjournal_detail', $data_journal_debit);//to debit

                $this->M_delivery_order->entry_data_journal_detail($postheader_id->postheader_id, $do_id); //to credit
                //end of journal detail
                //end of create journal

                $data_log['do_id'] = $do_id;
                $data_log['log_subject'] = 'Approved DO ( '.$current_approval_status->do_code.' )';
                $data_log['approved_id'] = my_sess('user_id');
                $data_log['approved_date'] = date('Y-m-d H:i:s.000');
                $data_log['status'] = STATUS_NEW;
                $this->db->insert('sales_approved_log', $data_log);
            } else {
                $check_approval_header = $this->M_delivery_order->check_so_status($do_id)->row();
                $get_next_approval = $this->M_delivery_order->get_approval($check_approval_header->do_status)->row();
                $data['approved_by'] = my_sess('user_id');
                $data['approved_date'] = date('Y-m-d H:i:s.000');
                $data['do_status'] = $get_next_approval->level;
                $this->M_delivery_order->update('delivery_order_header', array('do_id' => $do_id), $data);

                $data_log['do_id'] = $do_id;
                $data_log['log_subject'] = 'Approved DO ( '.$check_approval_header->do_code.' )';
                $data_log['approved_id'] = my_sess('user_id');
                $data_log['approved_date'] = date('Y-m-d H:i:s.000');
                $data_log['status'] = STATUS_NEW;
                $this->db->insert('sales_approved_log', $data_log);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed submit delivery order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/delivery_order/menu_manage/1.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully approved delivery order.');
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
        $do_id = $_POST['do_id'];
        $reason = $_POST['reason'];
        if (isset($_POST)) {
            $get_do_code = $this->M_delivery_order->check_so_status($do_id)->row();
            $data['approved_by'] = my_sess('user_id');
            $data['approved_date'] = date('Y-m-d H:i:s.000');
            $data['status'] = STATUS_DISAPPROVE;
            $data['do_status'] = 0;
            $this->M_delivery_order->update('delivery_order_header', array('do_id' => $do_id), $data);

            $data_log['do_id'] = $do_id;
            $data_log['log_subject'] = 'Disapproved DO ( '.$get_do_code->do_code.' )';
            $data_log['approved_id'] = my_sess('user_id');
            $data_log['approved_date'] = date('Y-m-d H:i:s.000');
            $data_log['remark'] = $reason;
            $data_log['status'] = STATUS_NEW;
            $this->db->insert('sales_approved_log', $data_log);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed process delivery order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/sales_order/so_list/0.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully disapproved delivery order.');
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
        $do_id = $_POST['do_id'];
        $reason = $_POST['reason'];
        if (isset($_POST)) {
            $get_do_code = $this->M_delivery_order->check_so_status($do_id)->row();
            $data['approved_by'] = my_sess('user_id');
            $data['approved_date'] = date('Y-m-d H:i:s.000');
            $data['status'] = STATUS_CANCEL;
            $data['do_status'] = 0;
            $this->M_delivery_order->update('delivery_order_header', array('do_id' => $do_id), $data);

            $data_log['do_id'] = $do_id;
            $data_log['log_subject'] = 'Cancel DO ( '.$get_do_code->do_code.' )';
            $data_log['approved_id'] = my_sess('user_id');
            $data_log['approved_date'] = date('Y-m-d H:i:s.000');
            $data_log['remark'] = $reason;
            $data_log['status'] = STATUS_NEW;
            $this->db->insert('sales_approved_log', $data_log);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed process delivery order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/sales_order/so_list/0.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully cancel delivery order.');
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
        $do_id = $_POST['do_id'];
        $reason = $_POST['reason'];
        if (isset($_POST)) {
            $get_do_code = $this->M_delivery_order->check_so_status($do_id)->row();
            $data['approved_by'] = my_sess('user_id');
            $data['approved_date'] = date('Y-m-d H:i:s.000');
            $data['status'] = STATUS_CLOSED;
            $this->M_delivery_order->update('delivery_order_header', array('do_id' => $do_id), $data);

            $data_log['do_id'] = $do_id;
            $data_log['log_subject'] = 'Closed DO ( '.$get_do_code->do_code.' )';
            $data_log['approved_id'] = my_sess('user_id');
            $data_log['approved_date'] = date('Y-m-d H:i:s.000');
            $data_log['remark'] = $reason;
            $data_log['status'] = STATUS_NEW;
            $this->db->insert('sales_approved_log', $data_log);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed process delivery order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/sales_order/so_list/0.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully cancel delivery order.');
            }

        }

        echo json_encode($result);
    }
}