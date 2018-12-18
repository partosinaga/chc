<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Delivery extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!is_login()) {
            redirect(base_url('login/login_form.tpd'));
        }

        $this->data_header = array(
            'style' => array(),
            'script' => array(),
            'custom_script' => array(),
            'init_app' => array()
        );
    }

    public function index()
    {
        $this->deposit_manage();
    }

    #region Delivery Order

    public function get_delivery_manage($menu_id = 0)
    {
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_delivery_header.status'] = STATUS_NEW;
        $where['ar_delivery_header.company_id >'] = 0;

        $like = array();

        if (isset($_REQUEST['filter_no'])) {
            if ($_REQUEST['filter_no'] != '') {
                $like['ar_delivery_header.do_no'] = $_REQUEST['filter_no'];
            }
        }
        if (isset($_REQUEST['filter_date_from'])) {
            if ($_REQUEST['filter_date_from'] != '') {
                $where['CONVERT(date,ar_delivery_header.do_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if (isset($_REQUEST['filter_date_to'])) {
            if ($_REQUEST['filter_date_to'] != '') {
                $where['CONVERT(date,ar_delivery_header.do_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if (isset($_REQUEST['filter_name'])) {
            if ($_REQUEST['filter_name'] != '') {
                $like['ms_company.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array('ms_company' => 'ms_company.company_id = ar_delivery_header.company_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_delivery_header', $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_delivery_header.do_date DESC';
        if (isset($_REQUEST['order'])) {
            if ($_REQUEST['order'][0]['column'] == 1) {
                $order = 'ar_delivery_header.do_no ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 2) {
                $order = 'ar_delivery_header.do_date ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 3) {
                $order = 'ms_company.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_delivery_header.*, ms_company.company_name'
            , 'ar_delivery_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/delivery/3/' . $row->delivery_id) . '.tpd"><i class="fa fa-file"></i> Open</a> </li>';
            if ($row->status == STATUS_NEW) {
                $btn_action .= '<li> <a href="' . base_url('ar/delivery/pdf_delivery/' . $row->delivery_id) . '" class="blue-ebonyclay" target="_blank"><i class="fa fa-print"></i> Delivery Order</a> </li>';
                $btn_action .= '<li> <a href="' . base_url('ar/delivery/pdf_delivery/' . $row->delivery_id) . '/a4/portrait" class="blue-ebonyclay" target="_blank"><i class="fa fa-print"></i> Delivery Order A4</a> </li>';

                if (check_session_action($menu_id, STATUS_CANCEL) || check_session_action($menu_id, STATUS_DELETE)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->delivery_id . '" data-code="' . $row->do_no . '"><i class="fa fa-remove"></i>' . get_action_name(STATUS_CANCEL, false) . '</a> </li>';
                }
            }

            $records["data"][] = array(
                $i,
                $row->do_no,
                dmy_from_db($row->do_date),
                $row->company_name,
                $row->delivered_by,
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

    public function get_delivery_history($menu_id = 0)
    {
        $this->load->model('finance/mdl_finance');

        $where = array();
        $where['ar_delivery_header.company_id >'] = 0;

        $where_str = 'ar_delivery_header.status IN (' . STATUS_CLOSED . ',' . STATUS_POSTED . ') ';

        $like = array();

        if (isset($_REQUEST['filter_no'])) {
            if ($_REQUEST['filter_no'] != '') {
                $like['ar_delivery_header.do_no'] = $_REQUEST['filter_no'];
            }
        }
        if (isset($_REQUEST['filter_date_from'])) {
            if ($_REQUEST['filter_date_from'] != '') {
                $where['CONVERT(date,ar_delivery_header.do_date) >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }
        if (isset($_REQUEST['filter_date_to'])) {
            if ($_REQUEST['filter_date_to'] != '') {
                $where['CONVERT(date,ar_delivery_header.do_date) <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if (isset($_REQUEST['filter_name'])) {
            if ($_REQUEST['filter_name'] != '') {
                $like['ms_company.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array('ms_company' => 'ms_company.company_id = ar_delivery_header.company_id');
        $iTotalRecords = $this->mdl_finance->countJoin('ar_delivery_header', $joins, $where, $like, '', array(), $where_str);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'ar_delivery_header.do_date DESC';
        if (isset($_REQUEST['order'])) {
            if ($_REQUEST['order'][0]['column'] == 1) {
                $order = 'ar_delivery_header.do_no ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 2) {
                $order = 'ar_delivery_header.do_date ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 3) {
                $order = 'ms_company.company_name ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('ar_delivery_header.*, ms_company.company_name'
            , 'ar_delivery_header', $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $where_str);

        //$records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('ar/corporate_bill/delivery/3/' . $row->delivery_id) . '.tpd"><i class="fa fa-file"></i> Open</a> </li>';
            if ($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED) {
                $btn_action .= '<li> <a href="' . base_url('ar/delivery/pdf_delivery/' . $row->delivery_id) . '" class="blue-ebonyclay" target="_blank"><i class="fa fa-print"></i> Delivery Order</a> </li>';
                $btn_action .= '<li> <a href="' . base_url('ar/delivery/pdf_delivery/' . $row->delivery_id) . '/a4/portrait" class="blue-ebonyclay" target="_blank"><i class="fa fa-print"></i> Delivery Order A4</a> </li>';
            }

            $records["data"][] = array(
                $i,
                $row->do_no,
                dmy_from_db($row->do_date),
                $row->company_name,
                $row->delivered_by,
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

    public function delivery_form($id = 0)
    {
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
        //array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

        $data['delivery_id'] = $id;

        if ($id > 0) {
            $joins = array('ms_company' => 'ms_company.company_id = ar_delivery_header.company_id');
            $qry = $this->mdl_finance->getJoin('ar_delivery_header.*, ms_company.company_name ', 'ar_delivery_header', $joins, array('delivery_id' => $id));
            $data['delivery'] = $qry->row();

            $details = array();
            if ($data['delivery']->status == STATUS_NEW) {
                $where['bill.unpaid_grand >'] = 0;
                $where['bill.company_id'] = $data['delivery']->company_id;

                $joins = array();
                $qry = $this->mdl_finance->getJoin("bill.*", "fxnARInvoiceHeaderByStatus('" . STATUS_POSTED . "') AS bill", $joins, $where, array(), 'inv_no');

                if ($qry->num_rows() > 0) {
                    foreach ($qry->result_array() as $iv) {
                        $iv_details = $this->db->query('SELECT * FROM fxnARInvoiceDetailByInvID(' . $iv['inv_id'] . ')');

                        if ($iv_details->num_rows() > 0) {
                            foreach ($iv_details->result_array() as $bill) {
                                $checked = "";
                                $pickQry = $this->db->query('SELECT invdetail_id FROM ar_delivery_detail WHERE delivery_id = ' . $id . ' AND invdetail_id = ' . $bill['invdetail_id']);
                                if ($pickQry->num_rows() > 0) {
                                    $checked = 'checked';
                                }

                                array_push($details, array('invdetail_id' => $bill['invdetail_id'], 'inv_id' => $iv['inv_id'], 'inv_no' => $iv['inv_no'], 'description' => $bill['description'], 'unit_qty' => $bill['unit_qty'], 'unit_uom' => $bill['unit_uom'], 'checked' => $checked));
                            }
                        }
                    }
                }

            } else {
                $qry = $this->db->query('SELECT iv.inv_id, iv.inv_no, iv.inv_date, iv.inv_due_date, iv.total_grand, id.invdetail_id, id.description, id.amount, id.tax, cp.company_name, bill.item_qty, stok.stock_uom FROM ar_delivery_detail det
                            JOIN ar_invoice_detail id ON id.invdetail_id = det.invdetail_id
                            JOIN cs_corporate_bill csb ON csb.corporatebill_id = id.bill_id
                            JOIN cs_bill_detail bill ON bill.billdetail_id = csb.billdetail_id
                            JOIN view_pos_item_stock stok ON bill.item_id = stok.itemstock_id
                            JOIN ar_invoice_header iv ON iv.inv_id = id.inv_id
                            JOIN ms_company cp ON cp.company_id = iv.company_id
                            WHERE det.delivery_id = ' . $id);
                if ($qry->num_rows() > 0) {
                    foreach ($qry->result_array() as $bill) {
                        array_push($details, array('invdetail_id' => $bill['invdetail_id'], 'inv_id' => $bill['inv_id'], 'inv_no' => $bill['inv_no'], 'description' => $bill['description'], 'unit_qty' => $bill['item_qty'], 'unit_uom' => $bill['stock_uom'], 'checked' => 'checked'));
                    }
                }
            }

            $data['details'] = $details;
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ar/delivery/delivery_form', $data);
        $this->load->view('layout/footer');
    }

    public function xcorp_pending_invoice()
    {
        $result = '';

        $company_id = 0;
        $reservation_id = 0;
        if (isset($_POST['company_id'])) {
            $company_id = $_POST['company_id'];
        }
        if ($company_id > 0) {
            $this->load->model('finance/mdl_finance');

            $where['bill.unpaid_grand >'] = 0;
            $where['bill.company_id'] = $company_id;

            $joins = array();
            $qry = $this->mdl_finance->getJoin("bill.*", "fxnARInvoiceHeaderByStatus('" . STATUS_POSTED . "') AS bill", $joins, $where, array(), 'inv_no');

            //echo $this->db->last_query();
            if ($qry->num_rows() > 0) {
                foreach ($qry->result_array() as $iv) {
                    $iv_details = $this->db->query('SELECT * FROM fxnARInvoiceDetailByInvID(' . $iv['inv_id'] . ')');

                    if ($iv_details->num_rows() > 0) {
                        foreach ($iv_details->result_array() as $bill) {
                            $result .= '<tr id="parent_' . $bill['invdetail_id'] . '' . '">
                             <td style="vertical-align:middle;" class="text-center">
                                <input type="hidden" name="inv_id[]" value="' . $bill['inv_id'] . '">
                                <span class="text-center">' . $iv['inv_no'] . '</span>
                             </td>
                             <td style="vertical-align:middle;" class="text-left">
                                <span class="text-left">' . $bill['description'] . '</span>
                             </td>
                             <td style="vertical-align:middle;padding-right:10px;" class="text-right">
                                <span class="text-right ">' . format_num($bill['unit_qty'], 0) . '</span>
                             </td>
                             <td style="vertical-align:middle;" class="text-center">
                                <span class="text-center">' . $bill['unit_uom'] . '</span>
                             </td>
                             <td style="vertical-align:middle;" class="text-center">
                                <input type="checkbox" name="invdetail_id[]" value="' . $bill['invdetail_id'] . '" class="chk_inv_id">
                             </td>
                            </tr>';
                        }
                    }
                }
            }
        }

        echo $result;
    }

    public function submit_delivery()
    {
        $valid = true;

        if (isset($_POST)) {
            $deliveryId = $_POST['delivery_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $server_date = date('Y-m-d H:i:s');

            $data['do_date'] = dmy_to_ymd($_POST['do_date']);
            $data['company_id'] = $_POST['company_id'];
            $data['remark'] = isset($_POST['remark']) ? $_POST['remark'] : '';
            $data['delivered_by'] = isset($_POST['delivered_by']) ? $_POST['delivered_by'] : '';

            $invdetail_list = isset($_POST['invdetail_id']) ? $_POST['invdetail_id'] : array();

            if (count($invdetail_list) > 0) {
                if ($deliveryId > 0) {
                    $qry = $this->db->get_where('ar_delivery_header', array('delivery_id' => $deliveryId));
                    $row = $qry->row();

                    $arr_date = explode('-', $data['do_date']);
                    $arr_date_old = explode('-', ymd_from_db($row->do_date));

                    if ($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]) {
                        $data['do_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_DELIVERY_ORDER, $data['do_date']);

                        if ($data['do_no'] == '') {
                            $valid = false;

                            $this->session->set_flashdata('flash_message_class', 'danger');
                            $this->session->set_flashdata('flash_message', 'Failed generating code.');
                        }
                    }

                    if ($valid) {
                        $data['modified_by'] = my_sess('user_id');
                        $data['modified_date'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('ar_delivery_header', array('delivery_id' => $deliveryId), $data);

                        //update details
                        if ($valid) {
                            //echo '<br>step 4 update';
                            $this->db->query('DELETE FROM ar_delivery_detail WHERE delivery_id = ' . $deliveryId);

                            foreach ($invdetail_list as $invdetail_id) {
                                $detail = array();
                                $detail['delivery_id'] = $deliveryId;
                                $detail['invdetail_id'] = $invdetail_id;
                                $detail['status'] = STATUS_NEW;

                                $this->db->insert('ar_delivery_detail', $detail);
                                $deliverydetail_id = $this->db->insert_id();
                            }

                            //echo '<br>step 5 update';
                            $this->session->set_flashdata('flash_message_class', 'success');
                            $this->session->set_flashdata('flash_message', 'Transaction successfully updated.');
                        }
                    }
                } else {
                    $data['do_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_DELIVERY_ORDER, $data['do_date']);
                    $data['status'] = STATUS_NEW;
                    $data['created_by'] = my_sess('user_id');
                    $data['created_date'] = $server_date;

                    if ($data['do_no'] != '') {
                        $this->db->insert('ar_delivery_header', $data);
                        $deliveryId = $this->db->insert_id();

                        if ($deliveryId > 0) {

                            foreach ($invdetail_list as $invdetail_id) {
                                $detail = array();
                                $detail['delivery_id'] = $deliveryId;
                                $detail['invdetail_id'] = $invdetail_id;
                                $detail['status'] = STATUS_NEW;

                                $this->db->insert('ar_delivery_detail', $detail);
                                $deliverydetail_id = $this->db->insert_id();
                            }

                            if ($valid) {
                                $this->session->set_flashdata('flash_message_class', 'success');
                                $this->session->set_flashdata('flash_message', 'Transaction successfully created.');
                            }

                        } else {
                            //echo 'deposit id = 0 ' ;
                            $valid = false;
                        }
                    } else {
                        //echo 'deposit id null ' ;

                        $valid = false;
                    }
                }
            } else {
                $valid = false;
            }

            //COMMIT OR ROLLBACK
            if ($valid) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
                } else {
                    $this->db->trans_commit();
                }
            } else {
                $this->db->trans_rollback();

                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Transaction can not be saved. Please try again later.');
            }

            //FINALIZE
            if (!$valid) {
                redirect(base_url('ar/corporate_bill/delivery/3/' . $deliveryId . '.tpd'));
            } else {
                if (isset($_POST['save_close'])) {
                    redirect(base_url('ar/corporate_bill/delivery/1.tpd'), true);
                } else {
                    redirect(base_url('ar/corporate_bill/delivery/3/' . $deliveryId . '.tpd'));
                }
            }
        }
    }

    public function xposting_delivery_by_id()
    {
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';
        $result['redirect_link'] = '';

        $valid = true;
        $deliveryId = 0;

        if (isset($_POST['delivery_id'])) {
            $deliveryId = $_POST['delivery_id'];
        }

        if ($deliveryId > 0) {
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $delivery = $this->db->get_where('ar_delivery_header', array('delivery_id' => $deliveryId));
            if ($delivery->num_rows() > 0) {
                $delivery = $delivery->row_array();

                $data['status'] = STATUS_CLOSED;
                $this->mdl_general->update('ar_delivery_header', array('delivery_id' => $deliveryId), $data);

                //COMMIT OR ROLLBACK
                if ($valid) {
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();

                        $result['type'] = '0';
                        $result['message'] = 'Transaction can not be posted. Please try again later.';

                        //$this->session->set_flashdata('flash_message_class', 'danger');
                        //$this->session->set_flashdata('flash_message', 'Transaction can not be posted. Please try again later.');
                    } else {
                        $this->db->trans_commit();

                        $result['type'] = '1';
                        $result['message'] = $delivery['do_no'] . ' successfully posted.';
                        $result['redirect_link'] = base_url('ar/corporate_bill/delivery/3/' . $deliveryId . '.tpd');
                    }
                } else {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be posted. Please try again later.';
                }
            }
        }

        echo json_encode($result);
    }

    public function action_request()
    {
        $result = array();
        $result['type'] = '0';
        $result['message'] = '';

        $delivery_id = $_POST['delivery_id'];
        $data['status'] = $_POST['action'];
        $data['cancel_note'] = $_POST['reason'];

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_subject'] = get_action_name($data['status'], false) . ' Debit Note';
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $delivery_id;
        $data_log['feature_id'] = Feature::FEATURE_AR_DELIVERY_ORDER;

        if ($delivery_id > 0 && $data['status'] > 0) {
            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $qry = $this->db->get_where('ar_delivery_header', array('delivery_id' => $delivery_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['type'] = '0';
                        $result['message'] = 'Transaction already canceled.';
                    } else {
                        $this->mdl_general->update('ar_delivery_header', array('delivery_id' => $delivery_id), $data);

                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['type'] = '1';
                        $result['message'] = 'Transaction successfully canceled.';
                    }
                }

                //FINALIZE TRANSACTION
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    $result['type'] = '0';
                    $result['message'] = 'Transaction can not be processed.';
                } else {
                    $this->db->trans_commit();
                }
            }
        }

        echo json_encode($result);
    }

    private function create_delivery_order($inv_list = array())
    {
        $this->load->model('finance/mdl_finance');

        $result = array();
        if (count($inv_list) > 0) {
            $deliveries = array();
            foreach ($inv_list as $inv_id) {
                $inv = $this->db->get_where('ar_invoice_header', array('inv_id' => $inv_id));
                if ($inv->num_rows() > 0) {
                    $inv = $inv->row();

                    $exist = $this->db->query('SELECT head.delivery_id, head.do_no, head.do_date, head.company_id, head.delivered_by, det.inv_id FROM ar_delivery_detail det
                                            JOIN ar_delivery_header head ON head.delivery_id = det.delivery_id
                                            WHERE head.status NOT IN(' . STATUS_CANCEL . ',' . STATUS_DELETE . ') AND det.inv_id = ' . $inv_id);
                    if ($exist->num_rows() <= 0) {
                        $deliveries[] = $inv_id;
                    } else {
                        $row = $exist->row();
                        if (!isset($result[$row->delivery_id])) {
                            $result[$row->delivery_id] = array('delivery_id' => $row->delivery_id, 'do_no' => $row->do_no, 'do_date' => $row->do_date, 'company_id' => $row->company_id, 'delivered_by' => $row->delivered_by);
                        }
                    }
                }
            }

            if (count($deliveries) > 0) {
                $valid = true;

                $do_date = date('Y-m-d');

                //BEGIN TRANSACTION
                $this->db->trans_begin();

                //Create New DO
                $delivery_id = 0;
                $header = array();
                $header['do_no'] = $this->mdl_general->generate_code(Feature::FEATURE_AR_DELIVERY_ORDER, $do_date);
                if ($header['do_no'] != '') {
                    $header['do_date'] = $do_date;
                    $header['company_id'] = $inv->company_id;
                    $header['delivered_by'] = '';
                    $header['created_by'] = my_sess('user_id');
                    $header['created_date'] = date('Y-m-d H:i:s');
                    $header['status'] = STATUS_CLOSED;

                    if (count($deliveries) > 0) {
                        $this->db->insert('ar_delivery_header', $header);
                        $delivery_id = $this->db->insert_id();

                        if ($delivery_id > 0) {
                            foreach ($deliveries as $deliver) {
                                $detail = array();
                                $detail['delivery_id'] = $delivery_id;
                                $detail['inv_id'] = $deliver->inv_id;
                                $detail['status'] = STATUS_NEW;

                                $this->db->insert('ar_delivery_detail', $detail);
                                $deliverydetail_id = $this->db->insert_id();
                            }
                        } else {
                            $valid = false;
                        }
                    }
                }

                if ($valid) {
                    //END TRANSACTION
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                    } else {
                        $this->db->trans_commit();

                        $result[$delivery_id] = array('delivery_id' => $delivery_id, 'do_no' => $header['do_no'], 'do_date' => $header['do_date'], 'company_id' => $header['company_id'], 'delivered_by' => $header['delivered_by']);
                    }
                }
            }

        }

        return $result;
    }

    #endregion

    #region Modal

    public function xmodal_companies()
    {
        $this->load->view('ar/bill/ajax_modal_unpaid');
    }

    public function get_modal_companies($num_index = 0, $company_id = 0, $delivery_id = 0)
    {
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();

        $server_date = date('Y-m-d');

        $whereStr = ' (company_id > 0 )';

        if (isset($_REQUEST['filter_name'])) {
            if ($_REQUEST['filter_name'] != '') {
                $like['ar.company_name'] = $_REQUEST['filter_name'];
            }
        }

        $joins = array(); //array("view_cs_reservation"=>"view_cs_reservation.reservation_id = ar.reservation_id");
        $iTotalRecords = $this->mdl_finance->countJoin("view_ar_invoice_unpaid_sum AS ar", $joins, $where, $like, '', array(), $whereStr);

        $iDisplayLength = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 0;
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
        $sEcho = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

        $records = array();
        $records["data"] = array();

        $order = 'ar.company_name';
        if (isset($_REQUEST['order'])) {
            if ($_REQUEST['order'][0]['column'] == 0) {
                $order = 'ar.company_name ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 1) {
                $order = 'ar.sum_pending ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin("ar.* ", "view_ar_invoice_unpaid_sum AS ar", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart, false, '', array(), $whereStr);

        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $pending_amount = 0;

            $attr = '';
            $attr .= ' data-company-id="' . $row->company_id . '" ';
            $attr .= ' data-company-name="' . $row->company_name . '" ';
            $attr .= ' data-index="' . $num_index . '" ';

            $text = "";
            if ($company_id == $row->company_id) {
                $attr .= ' disabled="disabled" ';
                $text = 'selected';
            } else {
                $text = "Select";
            }

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-record" ' . $attr . ' ><i class="fa fa-check"></i>&nbsp;&nbsp;' . $text . '</button>';

            $records["data"][] = array(
                $row->company_name,
                format_num($row->sum_pending, 0),
                '',
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

    #region Print

    public function pdf_delivery($delivery_id = 0, $paper_size = 'A5', $paper_layout = 'landscape')
    {
        if ($delivery_id > 0) {
            $qry = $this->db->get_where('ar_delivery_header', array('delivery_id' => $delivery_id));
            if ($qry->num_rows() > 0) {
                $parent = $qry->row_array();

                //Company Profile
                $profile = $this->db->get_where('print_profile', array('key_code' => 'INV'));
                if ($profile->num_rows() > 0) {
                    $data['profile'] = $profile->row_array();
                }

                //Company
                $folio_caption = 'DELIVERY ORDER';

                $company = $this->db->get_where('ms_company', array('company_id' => $parent['company_id']));
                if ($company->num_rows() > 0) {
                    $company = $company->row_array();

                    $bill_info = $company['company_name'];
                    $bill_info .= trim($company['company_address']) != '' ? '<br>' . nl2br($company['company_address']) : '';
                    $bill_info .= trim($company['company_phone']) != '' ? '<br>' . $company['company_phone'] : '';
                    $bill_info .= trim($company['company_pic_name']) != '' ? '<br>Attn. ' . $company['company_pic_name'] : '';

                    $data['folio_title'] = $folio_caption;
                    $data['guest_info'] = $bill_info;
                }

                $data['row'] = $parent;

                //DETAILS
                $details = $this->db->query("SELECT stok.item_desc,stok.stock_uom, bill.item_qty FROM ar_delivery_detail dt
                                 JOIN ar_invoice_detail det ON det.invdetail_id = dt.invdetail_id
                                 JOIN cs_corporate_bill csb ON csb.corporatebill_id = det.bill_id
                                 JOIN cs_bill_detail bill ON bill.billdetail_id = csb.billdetail_id
                                 JOIN view_pos_item_stock stok ON stok.itemstock_id = bill.item_id
                                 WHERE dt.delivery_id = " . $delivery_id . "
                                 ORDER BY det.inv_id ");
                if ($details->num_rows() > 0) {
                    $data['detail'] = $details->result_array();
                }

                if (strtoupper(trim($paper_size)) == 'A4') {
                    $this->load->view('ar/delivery/pdf_delivery_a4', $data);
                } else {
                    $this->load->view('ar/delivery/pdf_delivery', $data);
                }

                $html = $this->output->get_output();

                $this->load->library('dompdf_gen');

                //DEFAULT A5 landscape
                $this->dompdf->set_paper($paper_size, $paper_layout);

                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($parent['do_no'] . ".pdf", array('Attachment' => 0));

            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

    #endregion
}