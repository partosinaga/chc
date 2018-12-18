<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dp_invoice extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data_header = array(
            'style' => array(),
            'script' => array(),
            'custom_script' => array(),
            'init_app' => array()
        );
        $this->load->model('sales/M_dp_invoice');

        $this->data_footer = array(
            'footer_script' => array()
        );
    }

    public function menu_manage($type = 0, $inv_id = 0)
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
        array_push($data_header['script'], base_url() . 'assets/sales/invoice.js');

        if ($type == 0 || $inv_id != 0) { //form

            $data['inv_id'] = $inv_id;
            if ($inv_id > 0) {
                $qry_head = $this->M_dp_invoice->get_header($inv_id);
                $data['row'] = $qry_head->row();
//                echo $this->db->last_query();
                $qry_det = $this->M_dp_invoice->get_detail($inv_id);
                $data['row_detail'] = $qry_det;
                $qry_payment = $this->M_dp_invoice->get_payment_invoice($inv_id);
                $data['row_payment'] = $qry_payment->result();
            }
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/invoice/dp_invoice_form', $data);
            $this->load->view('layout/footer');
        } else {
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/invoice/dp_invoice_list', $data);
            $this->load->view('layout/footer');
        }

    }

    public function generate_inv_no()
    {
        $parameter = $_POST['date'];
        $a = new DateTime($parameter);
        $date = $a->format('Y-m-d');

        $get_number = $this->M_dp_invoice->get_inv_no($date);
        $result['inv_number'] = $get_number->inv_number;
        echo json_encode($result);
    }

    public function ajax_modal_customer()
    {
        $this->load->view('sales/invoice/ajax_customer_list');
    }

    public function ajax_customer_list()
    {
        $where['status ='] = STATUS_NEW;
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


        $iTotalRecords = $this->M_dp_invoice->get_customer_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'customer_id desc ';

        $qry = $this->M_dp_invoice->get_customer_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
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

    public function ajax_modal_so()
    {
        $this->load->view('sales/invoice/ajax_so_list');
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

        $iTotalRecords = $this->M_dp_invoice->get_so_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'h.so_id desc ';

        $qry = $this->M_dp_invoice->get_so_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
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
                    <button class="btn green-meadow btn-xs select-so" type="button" so-code="' . $row->so_code . '" so-id="' . $row->so_id . '" deliv-address="' . $row->customer_address . '" deliv-address-id="' . $row->delivery_address_id . '" tax-percent="' . $row->taxtype_percent . '">
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

    public function ajax_invoice_type($so_id = 0)
    {
        $get_invoice = $this->M_dp_invoice->get_invoice_type($so_id);
        $output = null;
        $output .= '<option value="">--select--</option>';
        foreach ($get_invoice->result() as $row) {
            $output .= '<option value=' . $row->sp_type_id . ' coa-id="' . $row->coa_id . '"  desc = "' . $row->sp_type_name . '" amount = ' . $row->amount . '>' . $row->sp_type_name . '</option>';
        }
        $get_do_payment = $this->db->query("SELECT * FROM sales_payment_type WHERE sp_type_name LIKE 'DO %' ")->row();
        $output .= '<option value="0" coa-id="' . $get_do_payment->coa_id . '"  >DO Payment</option>';
        echo json_encode($output);
    }

    public function ajax_vat_list()
    {
        $sql = "SELECT * FROM tax_type WHERE status = " . STATUS_NEW . " ";
        $vat = $this->db->query($sql);
        $output = null;
        $output .= '<option value="" selected="selected">--select--</option>';
        foreach ($vat->result() as $row) {
            $output .= '<option value=' . $row->taxtype_id . ' tax-value=' . $row->taxtype_percent . '>' . $row->taxtype_code . '</option>';
        }
        echo json_encode($output);
    }

    public function ajax_do_list($so_id = 0)
    {
        $get_do = $this->M_dp_invoice->get_do_list($so_id)->result();
        $output = null;
        $output .= '<option value="" >--select--</option>';
        foreach ($get_do as $row) {
            $output .= '<option value=' . $row->do_id . ' do-code="' . $row->do_code . '" amount="' . $row->journal_amount . '">' . $row->do_code . '</option>';
        }
        echo json_encode($output);
    }

    public function ajax_modal_do_list()
    {
        $this->load->view('sales/invoice/ajax_do_list');
    }

    public function ajax_do_invoice_list($so_id = 0)
    {
        $where['doh.so_id ='] = $so_id;
        $where['doh.status ='] = STATUS_APPROVE;
        $like = array();

        if (isset($_REQUEST['filter_code'])) {
            if ($_REQUEST['filter_code'] != '') {
                $like['doh.do_code'] = $_REQUEST['filter_code'];
            }
        }
        if (isset($_REQUEST['filter_desc'])) {
            if ($_REQUEST['filter_desc'] != '') {
                $like['doh.remarks'] = $_REQUEST['filter_desc'];
            }
        }
        if (isset($_REQUEST['filter_date'])) {
            if ($_REQUEST['filter_date'] != '') {
                $like['doh.do_date'] = $_REQUEST['filter_date'];
            }
        }
        if (isset($_REQUEST['filter_amount'])) {
            if ($_REQUEST['filter_amount'] != '') {
                $like['gph.journal_amount'] = $_REQUEST['filter_amount'];
            }
        }

        $iTotalRecords = $this->M_dp_invoice->get_do_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'doh.do_id desc ';

        $qry = $this->M_dp_invoice->get_do_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
//        echo $this->db->last_query();
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $records["data"][] = array(
                $i . '.',
                $row->do_code,
                ymd_to_dmy($row->do_date),
                $row->remarks,
                number_format($row->journal_amount),
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs select-do" type="button" do-code="' . $row->do_code . '" do-id="' . $row->do_id . '" desc="' . $row->remarks . '" amount="' . $row->journal_amount . '">
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

    public function ajax_payment_type($so_id)
    {
        $get_payment = $this->M_dp_invoice->get_payment($so_id)->result();
        $output = null;
        $i = 0;
        foreach ($get_payment as $row) {
            $output .= '<tr>
                        <td><input type="hidden" name="payment_type_id[' . $i . ']"  class="form-control input-sm mask_currency calcu" value="' . $row->sp_type_id . '">' . $row->sp_type_name . '</td>
                        <td><input type="text" name="payment_type_amount[' . $i . ']"  class="form-control input-sm mask_currency calcu">
                        <input type="hidden" name="max_payment_type_amount[' . $i . ']"  class="form-control input-sm mask_currency calcu" value="' . $row->amount . '"></td>
                        <input type="hidden" name="payment_type_desc[' . $i . ']"  class="form-control input-sm" value="' . $row->sp_type_name . '" ></td>
                        </tr>';
            $i++;
        }
        echo json_encode($output);
    }

    public function ajax_into_words($grand_total = 0)
    {
        $words = $this->M_dp_invoice->into_words($grand_total);
        $result['in_words'] = $words->in_to_words;
        echo json_encode($result);
    }


    public function inv_entry()
    {
        $this->db->trans_begin();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = array();
        if (isset($_POST)) {
            $inv_id = $_POST['inv_id'];
            $has_error = false;
            if ($inv_id > 0) { //update

                $inv_id = $_POST['inv_id'];

                $data['inv_no'] = $_POST['inv_code'];
                $data['inv_date'] = dmy_to_ymd(trim($_POST['inv_date']));
                $data['inv_due_date'] = dmy_to_ymd(trim($_POST['due_date']));
                $data['company_id'] = $_POST['customer_id'];
                $data['so_id'] = $_POST['so_id'];
                $data['invoice_type'] = $_POST['invoice_type'];
                $data['description'] = $_POST['remarks'];
                $data['total_amount'] = $_POST['tot_amount'];
                $data['total_tax'] = $_POST['total_tax'];
                $data['total_grand'] = $_POST['total_tax'] + $_POST['tot_amount'];
                $this->M_dp_invoice->update('ar_invoice_header', array('inv_id' => $inv_id), $data);

                $data_log['inv_id'] = $inv_id;
                $data_log['log_subject'] = 'Modified ( ' . $_POST['inv_code'] . ' )';
                $data_log['approved_id'] = my_sess('user_id');
                $data_log['approved_date'] = date('Y-m-d H:i:s.000');
                $data_log['status'] = STATUS_NEW;
                $this->db->insert('sales_approved_log', $data_log);


                if ($_POST['invoice_type'] == 0) { //if DO Payment

                    $this->db->delete('ar_invoice_detail', array('inv_id' => $inv_id)); //delete detail
                    $this->db->delete('ar_invoice_payment', array('inv_id' => $inv_id)); //delete payment

                    for ($a = 0; $a < count($_POST['status_detail']); $a++) {
                        if ($_POST['status_detail'][$a] == 1) {
                            $data_detail['inv_id'] = $inv_id;
                            $data_detail['do_id'] = $_POST['do_id'][$a];
                            $data_detail['description'] = "Invoice " . $_POST['desc'][$a];
                            $data_detail['amount'] = $_POST['amount'][$a];
                            $data_detail['coa_id'] = $_POST['coa_id'];
                            $data_detail['status'] = STATUS_NEW;
                            $this->db->insert('ar_invoice_detail', $data_detail); //reinsert detail
                        }
                    }

                    for ($i = 0; $i < count($_POST['payment_type_id']); $i++) {
                        $data_payment['inv_id'] = $inv_id;
                        $data_payment['sp_type_id'] = $_POST['payment_type_id'][$i];
                        $data_payment['amount'] = $_POST['payment_type_amount'][$i];
                        $data_payment['status'] = STATUS_NEW;
                        $this->db->insert('ar_invoice_payment', $data_payment); //reinsert payment method invoices

                    }
                } else {
                    $this->db->delete('ar_invoice_detail', array('inv_id' => $inv_id)); //delete detail

                    if ($_POST['status_detail'] == 1) {
                        $data_detail['inv_id'] = $inv_id;
                        $data_detail['description'] = $_POST['desc'];
                        $data_detail['amount'] = $_POST['tot_amount'];
                        $data_detail['tax'] = $_POST['total_tax'];
                        $data_detail['coa_id'] = $_POST['coa_id'];
                        $data_detail['status'] = STATUS_NEW;
                        $this->db->insert('ar_invoice_detail', $data_detail); // re insert detail if not DO Invoice
                    }
                }


                if ($has_error == false) {
                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully update invoice.');
                }
            } else { //entry
                $valid_header = $this->M_dp_invoice->get_header_id($_POST['inv_code']);

                if (count($valid_header) > 0) {
                    $has_error = true;
                    $result['valid'] = '0';
                    $result['message'] = 'Failed to submit, try again later.';
                } else {

                    $data['inv_no'] = $_POST['inv_code'];
                    $data['inv_date'] = dmy_to_ymd(trim($_POST['inv_date']));
                    $data['inv_due_date'] = dmy_to_ymd(trim($_POST['due_date']));
                    $data['company_id'] = $_POST['customer_id'];
                    $data['so_id'] = $_POST['so_id'];
                    $data['invoice_type'] = $_POST['invoice_type'];
                    $data['total_amount'] = $_POST['tot_amount'];
                    $data['total_tax'] = $_POST['total_tax'];
                    $data['description'] = $_POST['remarks'];
                    $data['total_grand'] = $_POST['total_tax'] + $_POST['tot_amount'];
                    $data['created_by'] = my_sess('user_id');
                    $data['created_date'] = date('Y-m-d H:i:s.000');
                    $data['status'] = STATUS_NEW;
                    $this->db->insert('ar_invoice_header', $data);//insert header
                    $get_header_id = $this->M_dp_invoice->get_header_id($_POST['inv_code']);

                    if ($_POST['invoice_type'] == 0) { //if DO Payment
                        for ($a = 0; $a < count($_POST['status_detail']); $a++) {
                            if ($_POST['status_detail'][$a] == 1) {
                                $data_detail['inv_id'] = $get_header_id->inv_id;
                                $data_detail['do_id'] = $_POST['do_id'][$a];
                                $data_detail['description'] = "Invoice " . $_POST['desc'][$a];
                                $data_detail['amount'] = $_POST['amount'][$a];
                                $data_detail['coa_id'] = $_POST['coa_id'];
                                $data_detail['status'] = STATUS_NEW;
                                $this->db->insert('ar_invoice_detail', $data_detail); //insert detail
                            }
                        }

                        for ($i = 0; $i < count($_POST['payment_type_id']); $i++) {
                            $data_payment['inv_id'] = $get_header_id->inv_id;
                            $data_payment['sp_type_id'] = $_POST['payment_type_id'][$i];
                            $data_payment['amount'] = $_POST['payment_type_amount'][$i];
                            $data_payment['status'] = STATUS_NEW;
                            $this->db->insert('ar_invoice_payment', $data_payment); //insert payment method invoice

                        }
                    } else {
                        if ($_POST['status_detail'] == 1) {
                            $data_detail['inv_id'] = $get_header_id->inv_id;
                            $data_detail['description'] = $_POST['desc'];
                            $data_detail['amount'] = $_POST['tot_amount'];
                            $data_detail['tax'] = $_POST['total_tax'];
                            $data_detail['coa_id'] = $_POST['coa_id'];
                            $data_detail['status'] = STATUS_NEW;
                            $this->db->insert('ar_invoice_detail', $data_detail); //insert detail ig not DO Invoice
                        }
                    }


                }

                if ($has_error == false) {
                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully create invoice.');
                }
                $this->session->set_flashdata('flash_message', 'Successfully create invoice.');

            }
            if ($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'Failed submit invoice, try again later.');
                } else {
                    $this->db->trans_commit();
                    if (isset($_POST['save_close'])) {
                        $result['link'] = base_url('sales/Dp_invoice/menu_manage/1.tpd');
                    } else {
                        $result['link'] = base_url('sales/Dp_invoice/menu_manage/0/' . $inv_id . '.tpd');
                    }
                }
            }
        }
        echo json_encode($result);
    }

    public function inv_ajax_list($menu_id = 0)
    {
        $where['aih.status <>'] = STATUS_DELETE;
        $like = array();

        if (isset($_REQUEST['filter_inv_no'])) {
            if ($_REQUEST['filter_inv_no'] != '') {
                $like['aih.inv_no'] = $_REQUEST['filter_inv_no'];
            }
        }
        if (isset($_REQUEST['filter_date_from'])) {
            if ($_REQUEST['filter_date_from'] != '') {
                $where['aih.inv_date >='] = dmy_to_ymd($_REQUEST['filter_date_from']);
            }
        }

        if (isset($_REQUEST['filter_date_to'])) {
            if ($_REQUEST['filter_date_to'] != '') {
                $where['aih.inv_date  <='] = dmy_to_ymd($_REQUEST['filter_date_to']);
            }
        }
        if (isset($_REQUEST['filter_due_date_from'])) {
            if ($_REQUEST['filter_due_date_from'] != '') {
                $where['aih.inv_date >='] = dmy_to_ymd($_REQUEST['filter_due_date_from']);
            }
        }
        if (isset($_REQUEST['filter_due_date_to'])) {
            if ($_REQUEST['filter_due_date_to'] != '') {
                $where['aih.inv_date  <='] = dmy_to_ymd($_REQUEST['filter_due_date_to']);
            }
        }

        if (isset($_REQUEST['filter_customer'])) {
            if ($_REQUEST['filter_customer'] != '') {
                $like['cst.customer_name'] = $_REQUEST['filter_customer'];
            }
        }
        if (isset($_REQUEST['filter_so_no'])) {
            if ($_REQUEST['filter_so_no'] != '') {
                $like['soh.so_code'] = $_REQUEST['filter_so_no'];
            }
        }
        if (isset($_REQUEST['filter_total'])) {
            if ($_REQUEST['filter_total'] != '') {
                $like['aih.total_grand'] = $_REQUEST['filter_total'];
            }
        }
        if (isset($_REQUEST['filter_inv_type'])) {
            if ($_REQUEST['filter_inv_type'] != '') {
                $where['aih.invoice_type'] = $_REQUEST['filter_inv_type'];
            }
        }
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['aih.status'] = $_REQUEST['filter_status'];
            }
        }
        $iTotalRecords = $this->M_dp_invoice->get_inv_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'aih.inv_id desc ';

        $qry = $this->M_dp_invoice->get_inv_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $i = $iDisplayStart + 1;

        foreach ($qry->result() as $row) {
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('sales/dp_invoice/menu_manage/0/' . $row->inv_id) . '.tpd">View</a> </li>';
            if ($row->status == STATUS_NEW) {
                if (check_session_action($menu_id, STATUS_POSTED)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_POSTED . '" data-id="' . $row->inv_id . '" data-code="' . $row->inv_no . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';
                }
                if (check_session_action($menu_id, STATUS_CANCEL)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action" data-action="' . STATUS_CANCEL . '" data-id="' . $row->inv_id . '" data-code="' . $row->inv_no . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            }
            if ($row->invoice_type == 0) { //if invoice DO Payment
                $invoice_type = '<span class="badge badge-primary "> DO Invoice </span>';
            } else {
                $invoice_type = '<span class="badge badge-danger "> DP Invoice </span>';

            }
            $status = get_status_name($row->status);
            $records["data"][] = array(
                $i . '.',
                $row->inv_no,
                dmy_from_db($row->inv_date),
                dmy_from_db($row->inv_due_date),
                $row->customer_name,
                $row->so_code,
                number_format($row->total_grand),
                $invoice_type,
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


    public function posted_ajax_action()
    {
        $this->db->trans_begin();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $inv_id = $_POST['inv_id'];

        if (isset($_POST)) {

            $data['status'] = STATUS_POSTED;
            //$this->M_dp_invoice->update('ar_invoice_header', array('inv_id' => $inv_id), $data);


            $qry = "SELECT * FROM ar_invoice_header WHERE inv_id = '".$inv_id."'  ";
            $head = $this->db->query($qry)->row();
            $journal_header['journal_date'] = date('Y-m-d H:i:s.000');
            $journal_header['journal_amount'] = $head->total_grand;
            $journal_header['journal_remarks'] = $head->description;
            $journal_header['journal_no'] = $head->inv_no;
            $journal_header['postedmonth'] = date('m');
            $journal_header['postedyear'] = date('Y');
            $journal_header['modul'] = GLMOD::GL_MOD_INV;
            $journal_header['created_by'] = my_sess('user_id');
            $journal_header['created_date'] = date('Y-m-d H:i:s.000');
            $journal_header['status'] = STATUS_NEW;
           $this->db->insert('gl_postjournal_header', $journal_header); //journal header


            $data_journal_debit['postheader_id'] = $postheader_id->postheader_id;
            $data_journal_debit['coa_id'] = $debit_journal->coa_id;
            $data_journal_debit['coa_code'] = $debit_journal->coa_code;
            $data_journal_debit['journal_note'] = $postheader_id->journal_remarks;
            $data_journal_debit['journal_debit'] = $journal_amount->journal_amount;
            $data_journal_debit['journal_credit'] = 0;
            $data_journal_debit['dept_id'] = my_sess('department_id');
            $data_journal_debit['status'] = STATUS_NEW;
            $this->db->insert('gl_postjournal_detail', $data_journal_debit);//to debit

            $data_log['inv_id'] = $inv_id;
            $data_log['log_subject'] = 'Posted Invoice';
            $data_log['approved_id'] = my_sess('user_id');
            $data_log['approved_date'] = date('Y-m-d H:i:s.000');
            $data_log['status'] = STATUS_NEW;
            //$this->db->insert('sales_approved_log', $data_log);

            exit();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed submit sales order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/dp_invoice/menu_manage/1.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully process sales order.');
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
        $inv_id = $_POST['inv_id'];
        $reason = $_POST['reason'];

        if (isset($_POST)) {

            $data['status'] = STATUS_CANCEL;
            $this->M_dp_invoice->update('ar_invoice_header', array('inv_id' => $inv_id), $data);

            $data_log['inv_id'] = $inv_id;
            $data_log['log_subject'] = 'Posted Invoice';
            $data_log['approved_id'] = my_sess('user_id');
            $data_log['approved_date'] = date('Y-m-d H:i:s.000');
            $data_log['remark'] = $reason;
            $data_log['status'] = STATUS_NEW;
            $this->db->insert('sales_approved_log', $data_log);


            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed submit sales order, try again later.');
            } else {
                $this->db->trans_commit();
                $result['link'] = base_url('sales/dp_invoice/menu_manage/1.tpd');
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully process sales order.');
            }

        }

        echo json_encode($result);

    }
}