<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends CI_Controller
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
        $this->invoice_manage();
    }

    public function invoice_manage($type = 0, $inv_id = 0)
    {
        if ($type == 0) {
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
            $data['type'] = '0';

            $this->load->view('layout/header', $data_header);
            $this->load->view('ap/invoice/invoice_list.php', $data);
            $this->load->view('layout/footer');
        } else {
            $this->invoice_form($inv_id);
        }
    }

    public function invoice_history($type = 0, $inv_id = 0)
    {
        if ($type == 0) {
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
            $data['type'] = '1';

            $this->load->view('layout/header', $data_header);
            $this->load->view('ap/invoice/invoice_list.php', $data);
            $this->load->view('layout/footer');
        } else {
            $this->invoice_form($inv_id);
        }
    }

    public function ajax_invoice_list($menu_id = 0, $type = 0)
    {
        //$type : 0 => manage, 1 => History
        $this->load->model('ap/mdl_inv');

        if ($type == 0) {
            $where['status'] = STATUS_NEW;
        } else {
            $where['status <>'] = STATUS_NEW;
        }

        $like = array();
        if (isset($_REQUEST['filter_inv_code'])) {
            if ($_REQUEST['filter_inv_code'] != '') {
                $like['inv_code'] = $_REQUEST['filter_inv_code'];
            }
        }
        if (isset($_REQUEST['filter_inv_date_from'])) {
            if ($_REQUEST['filter_inv_date_from'] != '') {
                $where['inv_date >='] = dmy_to_ymd($_REQUEST['filter_inv_date_from']);
            }
        }
        if (isset($_REQUEST['filter_inv_date_to'])) {
            if ($_REQUEST['filter_inv_date_to'] != '') {
                $where['inv_date <='] = dmy_to_ymd($_REQUEST['filter_inv_date_to']);
            }
        }
        if (isset($_REQUEST['filter_supplier'])) {
            if ($_REQUEST['filter_supplier'] != '') {
                $like['supplier_name'] = $_REQUEST['filter_supplier'];
            }
        }
        if (isset($_REQUEST['filter_curr'])) {
            if ($_REQUEST['filter_curr'] != '') {
                $where['currencytype_id'] = $_REQUEST['filter_curr'];
            }
        }
        if (isset($_REQUEST['filter_amount'])) {
            if ($_REQUEST['filter_amount'] != '') {
                $like['totalgrand'] = $_REQUEST['filter_amount'];
            }
        }
        if (isset($_REQUEST['filter_remarks'])) {
            if ($_REQUEST['filter_remarks'] != '') {
                $like['remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['status'] = $_REQUEST['filter_status'];
            }
        }

        $iTotalRecords = $this->mdl_inv->get_inv(true, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'inv_code desc';
        if (isset($_REQUEST['order'])) {
            if ($_REQUEST['order'][0]['column'] == 1) {
                $order = 'inv_code ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 2) {
                $order = 'inv_date ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 3) {
                $order = 'supplier_name ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 4) {
                $order = 'currencytype_code ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 5) {
                $order = 'totalgrand ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_inv->get_inv(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {

            $btn_action = '';
            $btn_action .= '<li><a href="' . base_url('ap/invoice/' . ($type == '0' ? 'invoice_manage' : 'invoice_history') . '/1/' . $row->inv_id) . '.tpd">View</a></li>';

            if ($row->status == STATUS_NEW) {
                if (check_session_action($menu_id, STATUS_POSTED)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_POSTED . '" data-id="' . $row->inv_id . '" data-code="' . $row->inv_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a> </li>';
                }
                if (check_session_action($menu_id, STATUS_CANCEL)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-action-doc" data-action="' . STATUS_CANCEL . '" data-id="' . $row->inv_id . '" data-code="' . $row->inv_code . '" data-action-code="' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            }

            $records["data"][] = array(
                $i . '.',
                $row->inv_code,
                ymd_to_dmy($row->inv_date),
                $row->supplier_name,
                $row->currencytype_code,
                '<span class="mask_currency">' . $row->totalgrand . '</span>',
                $row->inv_desc,
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

    public function invoice_form($inv_id = 0)
    {

        $this->load->model('ap/mdl_inv');

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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $data = array();

        $data['inv_id'] = $inv_id;

        $dept = $this->mdl_general->get('ms_department', array('status ' => STATUS_NEW), array());
        $data['dept_list'] = $dept->result_array();

        $data['qry_coa_tax'] = $this->db->query("SELECT a.*, b.coa_code FROM fn_feature_spec a LEFT JOIN gl_coa b ON a.coa_id = b.coa_id WHERE a.spec_key IN (" . FNSpec::FIN_AP_PREPAID_TAX . "," . FNSpec::FIN_AP_VAT_IN_EXP . ")");

        $fn_feature_spec = $this->mdl_general->get('fn_feature_spec', array('spec_key ' => 105, 'spec_key ' => 106), array());
        $data['fn_feature_spec_list'] = $fn_feature_spec->result_array();

        if ($inv_id > 0) {
            $qry = $this->mdl_inv->get_inv(false, array('inv_id' => $inv_id));
            $data['row'] = $qry->row();

            if ($data['row']->tax_id > 0) {
                $qry_tax = $this->db->get_where('tax_type', array('taxtype_id' => $data['row']->tax_id));
                $data['row_tax'] = $qry_tax->row();
            }

            $data['qry_detail'] = $this->mdl_inv->get_inv_detail(false, array('inv_id' => $inv_id));
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('ap/invoice/invoice_form', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_invoice_submit()
    {
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = '';

        $data = array();

        if (isset($_POST)) {
            $has_error = false;
            $this->db->trans_begin();
            $inv_id = $_POST['inv_id'];

            $data['inv_date'] = dmy_to_ymd(trim($_POST['inv_date']));
            $data['supplier_id'] = $_POST['supplier_id'];
            $data['currencytype_id'] = trim($_POST['currencytype_id']);
            $data['curr_rate'] = trim($_POST['curr_rate']);
            $data['totalamount'] = trim($_POST['totalamount']);
            $data['totalgrand'] = trim($_POST['totalgrand']);
            $data['term_ofpayment'] = trim($_POST['term_ofpayment']);
            $data['inv_ref'] = trim($_POST['inv_ref']);
            $data['tax_id'] = trim($_POST['taxtype_id']);
            $data['tax_account'] = trim($_POST['tax_account']);
            $data['totaltax'] = trim($_POST['taxtype_amount']);
            $data['totaltax_wht'] = trim($_POST['totaltax_wht']);
            $data['inv_desc'] = trim($_POST['inv_desc']);

            if ($inv_id > 0) {
                $qry = $this->db->get_where('ap_invoiceheader', array('inv_id' => $inv_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['inv_date']);
                $arr_date_old = explode('-', $row->inv_date);

                if ($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]) {
                    $data['inv_code'] = $this->mdl_general->generate_code(Feature::FEATURE_AP_INVOICE, $data['inv_date']);

                    if ($data['inv_code'] == '') {
                        $has_error = true;

                        $result['valid'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }

                if ($has_error == false) {
                    $data['user_modified'] = my_sess('user_id');
                    $data['date_modified'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $inv_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully update Invoice.');
                }
            } else {
                $data['inv_code'] = $this->mdl_general->generate_code(Feature::FEATURE_AP_INVOICE, $data['inv_date']);

                if ($data['inv_code'] != '') {
                    $data['user_created'] = my_sess('user_id');
                    $data['date_created'] = date('Y-m-d H:i:s');
                    $data['status'] = STATUS_NEW;

                    $this->db->insert('ap_invoiceheader', $data);
                    $inv_id = $this->db->insert_id();

                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $inv_id;
                    $data_log['feature_id'] = Feature::FEATURE_AP_INVOICE;
                    $data_log['log_subject'] = 'Create INVOICE (' . $data['inv_code'] . ')';
                    $data_log['action_type'] = STATUS_NEW;
                    $this->db->insert('app_log', $data_log);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successfully add Invoice.');
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'Failed generating code.';
                }
            }

            if ($has_error == false) {
                if (isset($_POST['inv_detid'])) {
                    $i = 0;
                    foreach ($_POST['inv_detid'] as $key => $val) {
                        $data_detail = array();

                        $status = $_POST['status'][$key];

                        $data_detail['inv_id'] = $inv_id;
                        $data_detail['inv_actype'] = $_POST['inv_actype'][$key];
                        $data_detail['charge_to'] = $_POST['charge_to'][$key];
                        $data_detail['dept_id'] = $_POST['dept_id'][$key];
                        $data_detail['tot_amount'] = $_POST['tot_amount'][$key];
                        $data_detail['local_tot_amount'] = $_POST['local_tot_amount'][$key];

                        $data_detail['status'] = STATUS_NEW;

                        if ($val > 0) {
                            $qry_inv_det = $this->db->get_where('ap_invoicedetail', array('inv_detid' => $val));
                            $row_inv_det = $qry_inv_det->row();

                            if ($row_inv_det->inv_actype == AP::INV_TYPE_GRN) {
                                $this->mdl_general->update('in_grn', array('grn_id' => $row_inv_det->charge_to), array('status_invoice' => 0));
                            }

                            if ($status == STATUS_NEW) {
                                if ($data_detail['inv_actype'] == AP::INV_TYPE_GRN) {
                                    $this->mdl_general->update('in_grn', array('grn_id' => $data_detail['charge_to']), array('status_invoice' => 1));
                                }

                                $this->mdl_general->update('ap_invoicedetail', array('ap_invoicedetail.inv_detid' => $_POST['inv_detid'][$key]), $data_detail);
                            } else {
                                $this->db->delete('ap_invoicedetail', array('ap_invoicedetail.inv_detid' => $_POST['inv_detid'][$key]));
                            }
                        } else {
                            if ($status == STATUS_NEW) {
                                if ($data_detail['inv_actype'] == AP::INV_TYPE_GRN) {
                                    $this->mdl_general->update('in_grn', array('grn_id' => $data_detail['charge_to']), array('status_invoice' => 1));
                                }

                                $this->db->insert('ap_invoicedetail', $data_detail);
                            }
                        }
                        $i++;
                    }
                } else {
                    $has_error = true;

                    $result['valid'] = '0';
                    $result['message'] = 'No Detail Invoice.';
                }
            }

            if ($has_error == false) {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    if (isset($_POST['save_close'])) {
                        $result['link'] = base_url('ap/invoice/invoice_manage.tpd');
                    } else {
                        $result['link'] = base_url('ap/invoice/invoice_manage/1/' . $inv_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function ajax_modal_grn($type = 0)
    {
        $data['type'] = $type;
        $this->load->view('ap/invoice/ajax_grn_list', $data);
    }

    public function ajax_grn_list($supplier_id = 0, $grn_id_exist = '-', $num_index = 0)
    {
        $this->load->model('ap/mdl_inv');

        $like = array();
        $where = array();

        $grn_id_exist = trim($grn_id_exist);
        $isexist = false;
        $where_string_exist = '';
        if ($grn_id_exist != '-') {
            $isexist = true;
            $arr_id = explode('_', $grn_id_exist);
            $where_string_exist = ' OR in_grn.grn_id IN (' . str_replace('_', ', ', $grn_id_exist) . ') ';
        }

        $where['in_grn.status'] = STATUS_POSTED;
        $where['in_grn.supplier_id'] = $supplier_id;
        $where_string = ' (in_grn.status_invoice is null OR in_grn.status_invoice = 0 ' . $where_string_exist . ') ';

        if (isset($_REQUEST['filter_grn_code'])) {
            if ($_REQUEST['filter_grn_code'] != '') {
                $like['in_grn.grn_code'] = $_REQUEST['filter_grn_code'];
            }
        }
        if (isset($_REQUEST['filter_grn_date_from'])) {
            if ($_REQUEST['filter_grn_date_from'] != '') {
                $where['in_grn.grn_date >='] = dmy_to_ymd($_REQUEST['filter_grn_date_from']);
            }
        }
        if (isset($_REQUEST['filter_grn_date_to'])) {
            if ($_REQUEST['filter_grn_date_to'] != '') {
                $where['in_grn.grn_date <='] = dmy_to_ymd($_REQUEST['filter_grn_date_to']);
            }
        }
        if (isset($_REQUEST['filter_remarks'])) {
            if ($_REQUEST['filter_remarks'] != '') {
                $like['in_grn.remarks'] = $_REQUEST['filter_remarks'];
            }
        }
        $iTotalRecords = $this->mdl_general->count('in_grn', $where, $like, $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_grn.grn_id desc';
        if (isset($_REQUEST['order'])) {
            if ($_REQUEST['order'][0]['column'] == 1) {
                $order = 'in_grn.grn_code ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 2) {
                $order = 'in_grn.grn_date ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 4) {
                $order = 'in_grn.remarks ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('in_grn', $where, $like, $order, $iDisplayLength, $iDisplayStart, $where_string);

        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $attr = '';
            $attr .= ' data-id="' . $row->grn_id . '" ';
            $attr .= ' data-index="' . $num_index . '" ';
            $attr .= ' data-code="' . $row->grn_code . '" ';
            if ($isexist) {
                foreach ($arr_id as $key => $val) {
                    if ($val == $row->grn_id) {
                        $attr .= 'selected="selected" disabled="disabled"';
                    }
                }
            }

            $qry_amount = $this->mdl_inv->get_grn_amount(false, array('grn_id' => $row->grn_id));
            $row_amount = $qry_amount->row();
            $attr .= ' data-dept="' . $row_amount->department_id . '" ';
            $attr .= ' data-dept-name="' . $row_amount->department_name . '" ';
            $attr .= ' data-amount="' . $row_amount->total_amount . '" ';

            $btn = '<button class="btn green-meadow yellow-stripe btn-xs btn-select-grn" ' . $attr . '><i class="fa fa-check"></i>&nbsp;&nbsp;Select</button>';

            $records["data"][] = array(
                $i . '.',
                $row->grn_code,
                ymd_to_dmy($row->grn_date),
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

    public function ajax_invoice_action()
    {
        $result = array();

        $this->load->model('ap/mdl_inv');

        $this->db->trans_begin();

        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = '';

        $inv_id = $_POST['inv_id'];
        $data['status'] = $_POST['action'];
        $is_redirect = false;
        if (isset($_POST['is_redirect'])) {
            $is_redirect = $_POST['is_redirect'];
        }

        $data_log['user_id'] = my_sess('user_id');
        $data_log['log_date'] = date('Y-m-d H:i:s');
        $data_log['reff_id'] = $inv_id;
        $data_log['feature_id'] = Feature::FEATURE_AP_INVOICE;
        $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

        if ($inv_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('ap_invoiceheader', array('inv_id' => $inv_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                if ($data['status'] == STATUS_POSTED) {
                    if ($row->status == STATUS_POSTED) {
                        $result['valid'] = '0';
                        $result['message'] = 'Invoice already posted.';
                    } else {
                        //POSTING INVOICE
                        $valid = $this->posting_inv($inv_id);
                        $result['debug'] = $valid;

                        if ($valid['error'] == '0') {
                            $data['date_posted'] = date('Y-m-d H:i:s');
                            $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $inv_id), $data);

                            $data_log['log_subject'] = 'Posting Invoice (' . $row->inv_code . ')';
                            $data_log['action_type'] = STATUS_POSTED;
                            $this->db->insert('app_log', $data_log);

                            $result['message'] = 'Successfully posting Invoice.';
                        } else {
                            $result['valid'] = '0';
                            $result['message'] = $valid['message'];
                        }
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    if ($row->status == STATUS_CANCEL) {
                        $result['valid'] = '0';
                        $result['message'] = 'Invoice already canceled.';
                    } else {
                        $this->mdl_general->update('ap_invoiceheader', array('inv_id' => $inv_id), $data);

                        $qry_det = $this->mdl_inv->get_inv_detail(false, array('ap_invoicedetail.inv_id' => $inv_id));
                        foreach ($qry_det->result() as $row_det) {
                            if ($row_det->inv_actype == AP::INV_TYPE_GRN) {
                                $this->mdl_general->update('in_grn', array('grn_id' => $row_det->charge_to), array('status_invoice' => 0));
                            }
                        }

                        $data_log['log_subject'] = 'Cancel INVOICE (' . $row->inv_code . ')';
                        $data_log['action_type'] = STATUS_CANCEL;
                        $this->db->insert('app_log', $data_log);

                        $result['message'] = 'Successfully cancel Invoice.';
                    }
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            $result['valid'] = '0';
            $result['message'] = "Something error. Please try again later.";
        } else {
            $this->db->trans_commit();

            if ($is_redirect) {
                $this->session->set_flashdata('flash_message_class', ($result['valid'] == '1' ? 'success' : 'danger'));
                $this->session->set_flashdata('flash_message', $result['message']);
            }
        }

        echo json_encode($result);
    }

    private function posting_inv($inv_id = 0)
    {
        $result['error'] = '0';
        $result['message'] = '';
        $result['debug'] = '';

        if ($inv_id > 0) {
            $qry_inv = $this->mdl_inv->get_inv(false, array('inv_id' => $inv_id));
            if ($qry_inv->num_rows() > 0) {
                $row_inv = $qry_inv->row();

                $this->load->model('ap/mdl_inv');
                $this->load->model('finance/mdl_finance');

                $detail = array();

                $totalDebit = 0;
                $totalCredit = 0;
                $totGRNAmount = 0;
                $totLocGRNAmount = 0;

                $is_wo = false;
                $qryDetails = $this->mdl_inv->get_inv_detail(false, array('ap_invoicedetail.inv_id' => $inv_id));
                if ($qryDetails->num_rows() > 0) {
                    foreach ($qryDetails->result() as $det) {
                        if (trim($row_inv->currencytype_code) == Purchasing::CURR_IDR) {
                            if ($det->inv_actype == AP::INV_TYPE_GRN) {
                                $qry_key = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_GRN));
                                if ($qry_key->num_rows() > 0) {
                                    $row_key = $qry_key->row();

                                    if ($row_key->coa_id > 0) {
                                        $rowdet = array();
                                        $rowdet['coa_id'] = $row_key->coa_id;
                                        $rowdet['dept_id'] = $det->dept_id;
                                        $rowdet['journal_note'] = $det->remarks;
                                        $rowdet['journal_debit'] = $det->tot_amount;
                                        $rowdet['journal_credit'] = 0;
                                        $rowdet['reference_id'] = $row_inv->inv_id;
                                        $rowdet['transtype_id'] = $row_key->transtype_id;

                                        $totalDebit += $rowdet['journal_debit'];
                                        $totalCredit += $rowdet['journal_credit'];

                                        array_push($detail, $rowdet);
                                    } else {
                                        $result['error'] = '1';
                                        $result['message'] = 'Spec Invoice GRN is empty.';
                                    }
                                } else {
                                    $result['error'] = '1';
                                    $result['message'] = 'Spec Invoice GRN not found.';
                                }
                            } else {
                                $rowdet = array();
                                $rowdet['coa_id'] = $det->charge_to;
                                $rowdet['dept_id'] = $det->dept_id;
                                $rowdet['journal_note'] = $det->coa_desc;
                                $rowdet['journal_debit'] = $det->tot_amount;
                                $rowdet['journal_credit'] = 0;
                                $rowdet['reference_id'] = $row_inv->inv_id;
                                $rowdet['transtype_id'] = 0;

                                $totalDebit += $rowdet['journal_debit'];
                                $totalCredit += $rowdet['journal_credit'];

                                array_push($detail, $rowdet);
                            }
                        } else {
                            //NOT IDR
                            if ($det->inv_actype == AP::INV_TYPE_GRN) {
                                $qry_key = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_GRN));
                                if ($qry_key->num_rows() > 0) {
                                    $row_key = $qry_key->row();

                                    if ($row_key->coa_id > 0) {
                                        $qry_grn = $this->db->get_where('in_grn', array('grn_id' => $det->charge_to));
                                        $row_grn = $qry_grn->row();

                                        if ($row_inv->currencytype_id == $row_grn->currencytype_id) {
                                            $rowdet = array();
                                            $rowdet['coa_id'] = $row_key->coa_id;
                                            $rowdet['dept_id'] = $det->dept_id;
                                            $rowdet['journal_note'] = $det->remarks;
                                            $rowdet['journal_debit'] = ($det->tot_amount * $row_grn->curr_rate);
                                            $rowdet['journal_credit'] = 0;
                                            $rowdet['reference_id'] = $row_inv->inv_id;
                                            $rowdet['transtype_id'] = $row_key->transtype_id;

                                            $totalDebit += $rowdet['journal_debit'];
                                            $totalCredit += $rowdet['journal_credit'];

                                            $totGRNAmount += $rowdet['journal_debit'];
                                            $totLocGRNAmount += $det->local_tot_amount;

                                            array_push($detail, $rowdet);
                                        } else {
                                            $result['error'] = '1';
                                            $result['message'] = 'Currency GRN not same with currency Invoice';
                                        }
                                    } else {
                                        $result['error'] = '1';
                                        $result['message'] = 'Spec Invoice GRN is empty.';
                                    }
                                } else {
                                    $result['error'] = '1';
                                    $result['message'] = 'Spec Invoice GRN not found.';
                                }
                            } else {
                                $rowdet = array();
                                $rowdet['coa_id'] = $det->charge_to;
                                $rowdet['dept_id'] = $det->dept_id;
                                $rowdet['journal_note'] = $det->coa_desc;
                                $rowdet['journal_debit'] = $det->local_tot_amount;
                                $rowdet['journal_credit'] = 0;
                                $rowdet['reference_id'] = $row_inv->inv_id;
                                $rowdet['transtype_id'] = 0;

                                $totalDebit += $rowdet['journal_debit'];
                                $totalCredit += $rowdet['journal_credit'];

                                array_push($detail, $rowdet);
                            }
                        }
                    }
                }

                if ($row_inv->taxtype_code != AP::NO_TAX) {
                    if (intval($row_inv->tax_account) > 0) {
                        $qry_tax_acc = $this->db->get_where('fn_feature_spec', array('id' => $row_inv->tax_account));
                        if ($qry_tax_acc->num_rows() > 0) {
                            $row_tax_acc = $qry_tax_acc->row();

                            $qry_coa = $this->db->get_where('gl_coa', array('coa_id' => $row_tax_acc->coa_id));
                            $row_coa = $qry_coa->row();

                            $tax_amount = $row_inv->totaltax;
                            if (trim($row_inv->currencytype_code) != Purchasing::CURR_IDR) {
                                $tax_amount = ($row_inv->totaltax * $row_inv->curr_rate);
                            }

                            $rowdet = array();
                            $rowdet['coa_id'] = $row_tax_acc->coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $row_coa->coa_desc;
                            $rowdet['journal_debit'] = $tax_amount;
                            $rowdet['journal_credit'] = 0;
                            $rowdet['reference_id'] = $row_inv->inv_id;
                            $rowdet['transtype_id'] = $row_tax_acc->transtype_id;

                            $totalDebit += $rowdet['journal_debit'];
                            $totalCredit += $rowdet['journal_credit'];

                            array_push($detail, $rowdet);
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Tax account not found.';
                        }
                    } else {
                        $result['error'] = '1';
                        $result['message'] = 'Tax account not found.';
                    }
                }

                if (trim($row_inv->currencytype_code) != Purchasing::CURR_IDR) {
                    if ($totGRNAmount != $totLocGRNAmount) {
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
                                if ($totLocGRNAmount > $totGRNAmount) {
                                    $rowdet['journal_debit'] = ($totLocGRNAmount - $totGRNAmount);
                                    $rowdet['journal_credit'] = 0;
                                } else {
                                    $rowdet['journal_debit'] = 0;
                                    $rowdet['journal_credit'] = ($totGRNAmount - $totLocGRNAmount);
                                }
                                $rowdet['reference_id'] = $row_inv->inv_id;
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
                    $qry_key = $this->db->get_where('fn_feature_spec', array('spec_key' => FNSpec::FIN_AP_INVOICE));
                    if ($qry_key->num_rows() > 0) {
                        $row_key = $qry_key->row();

                        if ($row_key->coa_id > 0) {
                            $rowdet = array();
                            $rowdet['coa_id'] = $row_key->coa_id;
                            $rowdet['dept_id'] = 0;
                            $rowdet['journal_note'] = $row_inv->inv_desc;
                            $rowdet['journal_debit'] = 0;
                            $rowdet['journal_credit'] = $totalDebit;
                            $rowdet['reference_id'] = $row_inv->inv_id;
                            $rowdet['transtype_id'] = $row_key->transtype_id;

                            $totalDebit += $rowdet['journal_debit'];
                            $totalCredit += $rowdet['journal_credit'];

                            array_push($detail, $rowdet);
                        } else {
                            $result['error'] = '1';
                            $result['message'] = 'Spec GRN is empty.';
                        }
                    } else {
                        $result['error'] = '1';
                        $result['message'] = 'Spec GRN not found.';
                    }
                }

                if ($result['error'] == '0') {
                    if ($totalDebit == $totalCredit) {
                        $header = array();
                        $header['journal_no'] = $row_inv->inv_code;
                        $header['journal_date'] = $row_inv->inv_date;
                        $header['journal_remarks'] = $row_inv->inv_desc;
                        $header['modul'] = GLMOD::GL_MOD_AP;
                        $header['journal_amount'] = $totalDebit;
                        $header['reference'] = strval($row_inv->inv_id);

                        $valid = $this->mdl_finance->postJournal($header, $detail);

                        if ($valid == false) {
                            $result['error'] = '1';
                            $result['message'] = 'Failed insert journal.';
                        }
                    }
                }
            } else {
                $result['error'] = '1';
                $result['message'] = 'Invoice not found.';
            }
        }

        return $result;
    }

}

/* End of file invoice.php */
/* Location: ./application/controllers/AP/invoice.php */
	