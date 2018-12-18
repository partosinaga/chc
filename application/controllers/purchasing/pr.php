<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pr extends CI_Controller
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

        $this->data_footer = array(
            'footer_script' => array()
        );
    }

    public function index()
    {
        $this->pr_manage();
    }

    #region pr

    public function pr_manage($type = 1, $id = 0)
    {
        $data_header = $this->data_header;

        $data = array();

        if ($type == 1) { //pr List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

            $data['qry_department'] = $this->mdl_general->get('ms_department', array('status <> ' => STATUS_DELETE), array(), 'department_name');
            $data['qry_project'] = $this->mdl_general->get('ms_project', array('status <> ' => STATUS_DELETE), array(), 'project_initial');

            $this->load->view('layout/header', $data_header);
            $this->load->view('purchasing/pr/pr_list', $data);
            $this->load->view('layout/footer');
        } else { //pr Form
            $this->pr_form($type, $id);
        }
    }

    public function pr_form($type = 1, $id = 0)
    {
        $this->load->model('purchasing/mdl_purchasing');

        $data_header = $this->data_header;
        $data_footer = $this->data_footer;

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
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

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

        $data['pr_id'] = $id;
        $supplier = $this->mdl_general->get('in_supplier', array('status' => STATUS_NEW), array(), 'supplier_name asc');
        $data['supplier_list'] = $supplier->result_array();

        $dept = $this->mdl_general->get('ms_department', array('status' => STATUS_NEW), array());
        $data['dept_list'] = $dept->result_array();

        $uom = $this->mdl_general->get('in_ms_uom', array('status' => STATUS_NEW), array());
        $data['uom_list'] = $uom->result_array();

        if ($id > 0) {
            $qry = $this->db->get_where('in_pr', array('pr_id' => $id));
            $data['row'] = $qry->row();

            $qry2 = $this->db->get_where('in_pr_item', array('pr_id' => $id));
            $data['qry_det'] = $qry2->result_array();
        }

        if ($type == 2) {
            //READ ONLY
        }

        $this->load->view('layout/header', $data_header);
        $this->load->view('purchasing/pr/pr_form', $data);
        $this->load->view('layout/footer', $data_footer);
    }

    public function pr_list($menu_id = 0)
    {
        $this->load->model('purchasing/mdl_purchasing');

        $where['in_pr.status <>'] = STATUS_DELETE;

        if (get_dept_name(my_sess('department_id')) != Purchasing::DEPT_GEN) {
            $where_or = " ( in_pr.department_id = " . my_sess('department_id');
            $where_or .= " OR ms_user.department_id = " . my_sess('department_id');
            $where_or .= " ) ";
        } else {
            $where_or = "";
        }

        $like = array();
        if (isset($_REQUEST['filter_prno'])) {
            if ($_REQUEST['filter_prno'] != '') {
                $like['in_pr.pr_code'] = $_REQUEST['filter_prno'];
            }
        }
        if (isset($_REQUEST['filter_preparedate_from'])) {
            if ($_REQUEST['filter_preparedate_from'] != '') {
                $where['in_pr.date_prepare >='] = dmy_to_ymd($_REQUEST['filter_preparedate_from']);
            }
        }
        if (isset($_REQUEST['filter_preparedate_to'])) {
            if ($_REQUEST['filter_preparedate_to'] != '') {
                $where['in_pr.date_prepare <='] = dmy_to_ymd($_REQUEST['filter_preparedate_to']);
            }
        }
        if (isset($_REQUEST['filter_itemdesc'])) {
            if ($_REQUEST['filter_itemdesc'] != '') {
                $like['pr_detail.item_desc'] = $_REQUEST['filter_itemdesc'];
            }
        }
        if (isset($_REQUEST['filter_deliveryreq_from'])) {
            if ($_REQUEST['filter_deliveryreq_from'] != '') {
                $where['in_pr.delivery_date >='] = dmy_to_ymd($_REQUEST['filter_deliveryreq_from']);
            }
        }
        if (isset($_REQUEST['filter_deliveryreq_to'])) {
            if ($_REQUEST['filter_deliveryreq_to'] != '') {
                $where['in_pr.delivery_date <='] = dmy_to_ymd($_REQUEST['filter_deliveryreq_to']);
            }
        }
        if (isset($_REQUEST['filter_department_id'])) {
            if ($_REQUEST['filter_department_id'] != '') {
                $like['in_pr.department_id'] = $_REQUEST['filter_department_id'];
            }
        }
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['in_pr.status'] = $_REQUEST['filter_status'];
            }
        }

        $iTotalRecords = $this->mdl_purchasing->count_pr('in_pr', $where, $like, $where_or);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'in_pr.pr_code desc';

        if (isset($_REQUEST['order'])) {
            if ($_REQUEST['order'][0]['column'] == 1) {
                $order = 'in_pr.pr_code ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 2) {
                $order = 'in_pr.date_prepare ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 4) {
                $order = 'in_pr.delivery_date ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 5) {
                $order = 'ms_department.department_name ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 6) {
                $order = 'in_pr.status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_purchasing->get_pr_list($where, $like, $order, $iDisplayLength, $iDisplayStart, $where_or);

        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $btn_action = '<li> <a href="' . base_url('purchasing/pr/pr_form/0/' . $row->pr_id) . '.tpd">View</a> </li>';
            if ($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE) {
                if (check_session_action($menu_id, STATUS_APPROVE)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-approve" data-action="' . STATUS_APPROVE . '" data-id="' . $row->pr_id . '" data-code="' . $row->pr_code . '">' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '</a></li>';
                }
                if (check_session_action($menu_id, STATUS_CANCEL)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-cancel" data-action="' . STATUS_CANCEL . '" data-id="' . $row->pr_id . '" data-code="' . $row->pr_code . '">' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a> </li>';
                }
            } else if ($row->status == STATUS_APPROVE) {
                if (check_session_action($menu_id, STATUS_DISAPPROVE)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-reject" data-action="' . STATUS_DISAPPROVE . '" data-id="' . $row->pr_id . '" data-code="' . $row->pr_code . '">' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '</a> </li>';
                }
                if (check_session_action($menu_id, STATUS_CLOSED)) {
                    $btn_action .= '<li> <a href="javascript:;" class="btn-close" data-action="' . STATUS_CLOSED . '" data-id="' . $row->pr_id . '" data-code="' . $row->pr_code . '">' . 'Complete' . '</a> </li>';
                }
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li> <a href="' . site_url('purchasing/pr/pdf_pr/' . $row->pr_id) . '" target="_blank">Print</a> </li>';
                }
            } else if ($row->status == STATUS_CLOSED) {
                if (check_session_action($menu_id, STATUS_PRINT)) {
                    $btn_action .= '<li> <a href="' . site_url('purchasing/pr/pdf_pr/' . $row->pr_id) . '" target="_blank">Print</a> </li>';
                }
            }

            $item_desc = $row->item_desc;
            if (strlen($row->item_desc) > 100) {
                $item_desc = substr($row->item_desc, 0, 100) . '....';
            }

            $records["data"][] = array(
                $i . '.',
                $row->pr_code,
                dmy_from_db($row->date_prepare),
                $item_desc,
                dmy_from_db($row->delivery_date),
                $row->department_name,
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
        //$records["debug"] = $this->db->last_query();

        echo json_encode($records);
    }

    public function submit_pr()
    {
        $result = array();
        $result['success'] = '1';

        $send_email = false;

        if (isset($_POST)) {
            $pr_id = $_POST['pr_id'];

            //BEGIN TRANSACTION
            $this->db->trans_begin();

            $data['date_prepare'] = dmy_to_ymd(trim($_POST['date_prepare']));
            $data['pr_code'] = $_POST['pr_code'];
            $data['remarks'] = $_POST['remarks'];
            $data['delivery_date'] = dmy_to_ymd(trim($_POST['delivery_date']));
            $data['department_id'] = $_POST['dept_id'];

            unset($_POST['pr_id']);

            if ($pr_id > 0) {
                $qry = $this->db->get_where('in_pr', array('pr_id' => $pr_id));
                $row = $qry->row();

                $arr_date = explode('-', $data['date_prepare']);
                $arr_date_old = explode('-', $row->date_prepare);

                if ($arr_date[0] != $arr_date_old[0] || $arr_date[1] != $arr_date_old[1]) {
                    $data['pr_code'] = $this->mdl_general->generate_code(Feature::FEATURE_PR, $data['date_prepare']);

                    if ($data['pr_code'] == '') {
                        $result['success'] = '0';
                        $result['message'] = 'Failed generating code.';
                    }
                }

                if ($result['success'] == '1') {
                    $data['user_modified'] = my_sess('user_id');
                    $data['date_modified'] = date('Y-m-d H:i:s');

                    $this->mdl_general->update('in_pr', array('pr_id' => $pr_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'PR successfully updated.');
                }
            } else {
                $data['pr_code'] = $this->mdl_general->generate_code(Feature::FEATURE_PR, $data['date_prepare']);

                $data['status'] = STATUS_NEW;
                $data['user_created'] = my_sess('user_id');
                $data['date_created'] = date('Y-m-d H:i:s');

                if ($data['pr_code'] != '') {
                    $this->db->insert('in_pr', $data);
                    $pr_id = $this->db->insert_id();

                    $send_email = true;

                    $data_log['user_id'] = my_sess('user_id');
                    $data_log['log_date'] = date('Y-m-d H:i:s');
                    $data_log['reff_id'] = $pr_id;
                    $data_log['feature_id'] = Feature::FEATURE_PR;
                    $data_log['log_subject'] = 'Create PR (' . $data['pr_code'] . ')';
                    $data_log['action_type'] = STATUS_NEW;
                    $this->db->insert('app_log', $data_log);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'PR successfully created.');
                } else {
                    $result['success'] = '0';
                    $result['message'] = 'Failed generating code.';
                }
            }

            if ($result['success'] == '1') {
                //detail
                if (isset($_POST['pr_item_id'])) {
                    foreach ($_POST['pr_item_id'] as $key => $val) {
                        $data_detail = array();

                        $status = $_POST['status'][$key];

                        if ($status == STATUS_NEW) {
                            $data_detail['pr_id'] = $pr_id;
                            $data_detail['item_desc'] = $_POST['item_desc'][$key];
                            $data_detail['item_qty'] = $_POST['item_qty'][$key];
                            $data_detail['qty_remain'] = $_POST['item_qty'][$key];
                            $data_detail['item_type'] = $_POST['item_type'][$key];
                            $data_detail['uom_id'] = $_POST['uom_id'][$key];
                            $data_detail['supplier_id'] = $_POST['supplier_id'][$key];
                            $data_detail['item_url'] = $_POST['item_url'][$key];
                            $data_detail['status'] = STATUS_NEW;

                            if ($val > 0) {
                                $this->mdl_general->update('in_pr_item', array('pr_item_id' => $val), $data_detail);
                            } else {
                                $this->db->insert('in_pr_item', $data_detail);
                            }
                        } else {
                            if ($val > 0) {
                                $this->db->delete('in_pr_item', array('pr_item_id' => $val));
                            }
                        }
                    }
                }
            }

            if ($result['success'] == '1') {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    if ($send_email) {
                        $this->send_email($pr_id, STATUS_NEW);
                    }

                    if (isset($_POST['save_close'])) {
                        $result['link'] = base_url('purchasing/pr/pr_manage/1.tpd');
                    } else {
                        $result['link'] = base_url('purchasing/pr/pr_manage/0/' . $pr_id . '.tpd');
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function insertDetailItemEntries($pr_id = 0, $data = array())
    {
        $valid = true;

        if ($pr_id > 0 && isset($_POST) && isset($data)) {
            $detail_ids = isset($_POST['detail_id']) ? $_POST['detail_id'] : array();
            $supplier_ids = isset($_POST['supplier_id']) ? $_POST['supplier_id'] : array();
            $item_descs = isset($_POST['item_desc']) ? $_POST['item_desc'] : array();
            $item_qtys = isset($_POST['item_qty']) ? $_POST['item_qty'] : array();
            $item_types = isset($_POST['item_type']) ? $_POST['item_type'] : array();
            $uom_ids = isset($_POST['uom_id']) ? $_POST['uom_id'] : array();

            if (count($detail_ids) > 0) {
                //echo '<br>Count detail ' . count($dept);

                for ($i = 0; $i <= max(array_keys($detail_ids)); $i++) {
                    if ($valid) {
                        $detail['supplier_id'] = $supplier_ids[$i];
                        $detail['item_desc'] = $item_descs[$i];
                        $detail['item_qty'] = $item_qtys[$i];
                        $detail['qty_remain'] = $detail['item_qty'];
                        $detail['item_type'] = $item_types[$i];
                        $detail['uom_id'] = $uom_ids[$i];
                        $detail['status'] = STATUS_NEW;
                        if (isset($detail_ids[$i])) {
                            if ($detail_ids[$i] <= 0) {
                                $detail['pr_id'] = $pr_id;

                                $data['user_created'] = my_sess('user_id');
                                $data['date_created'] = date('Y-m-d H:i:s');

                                $this->db->insert('in_pr_item', $detail);
                                $insertID = $this->db->insert_id();

                                if ($insertID <= 0) {
                                    $valid = false;
                                }
                            } else {

                                $data['user_modified'] = my_sess('user_id');
                                $data['date_modified'] = date('Y-m-d H:i:s');

                                $this->mdl_general->update('in_pr_item', array('pr_item_id' => $detail_ids[$i]), $detail);
                            }
                        }
                    } else {
                        break;
                    }
                }
            }
        }

        return $valid;
    }

    public function insertDetailServiceEntries($pr_id = 0, $data = array())
    {
        $valid = true;

        if ($pr_id > 0 && isset($_POST) && isset($data)) {
            $detail_ids = isset($_POST['detail_id_svc']) ? $_POST['detail_id_svc'] : array();
            $supplier_ids = isset($_POST['supplier_id_svc']) ? $_POST['supplier_id_svc'] : array();
            $item_descs = isset($_POST['item_desc_svc']) ? $_POST['item_desc_svc'] : array();
            $item_qtys = isset($_POST['item_qty_svc']) ? $_POST['item_qty_svc'] : array();
            $item_types = isset($_POST['item_type_svc']) ? $_POST['item_type_svc'] : array();
            $uom_ids = isset($_POST['uom_id_svc']) ? $_POST['uom_id_svc'] : array();

            if (count($detail_ids) > 0) {
                //echo '<br>Count detail ' . count($dept);

                for ($i = 0; $i <= max(array_keys($detail_ids)); $i++) {
                    if ($valid) {
                        $detail['supplier_id'] = $supplier_ids[$i];
                        $detail['item_desc'] = $item_descs[$i];
                        $detail['item_qty'] = $item_qtys[$i];
                        $detail['qty_remain'] = $detail['item_qty'];
                        $detail['item_type'] = Purchasing::ITEM_SERVICE;
                        $detail['uom_id'] = $uom_ids[$i];
                        $detail['status'] = STATUS_NEW;
                        if (isset($detail_ids[$i])) {
                            if ($detail_ids[$i] <= 0) {
                                $detail['pr_id'] = $pr_id;

                                $data['user_created'] = my_sess('user_id');
                                $data['date_created'] = date('Y-m-d H:i:s');

                                $this->db->insert('in_pr_item', $detail);
                                $insertID = $this->db->insert_id();

                                if ($insertID <= 0) {
                                    $valid = false;
                                }
                            } else {

                                $data['user_modified'] = my_sess('user_id');
                                $data['date_modified'] = date('Y-m-d H:i:s');

                                $this->mdl_general->update('in_pr_item', array('pr_item_id' => $detail_ids[$i]), $detail);
                            }
                        }
                    } else {
                        break;
                    }
                }
            }
        }

        return $valid;
    }

    public function ajax_delete_detail()
    {
        $result = array();
        //Used to display notification
        $result['type'] = '0';
        $result['message'] = '';

        $detailId = 0;
        if (isset($_POST['detail_id'])) {
            $detailId = $_POST['detail_id'];
        }

        if ($detailId > 0) {
            $this->db->delete('in_pr_item', array('pr_item_id' => $detailId));

            $result['type'] = '1';
            $result['message'] = 'Successfully delete record.';
        }

        echo json_encode($result);
    }

    public function action_request()
    {
        $result = array();

        $result['type'] = '0';
        $result['message'] = '';

        $pr_id = $_POST['pr_id'];
        $data['status'] = $_POST['action'];

        $is_redirect = false;
        if (isset($_POST['is_redirect'])) {
            $is_redirect = $_POST['is_redirect'];
        }

        if ($pr_id > 0 && $data['status'] > 0) {
            $qry = $this->db->get_where('in_pr', array('pr_id' => $pr_id));
            if ($qry->num_rows() > 0) {
                $row = $qry->row();

                $data_log['user_id'] = my_sess('user_id');
                $data_log['log_subject'] = ucwords(strtolower(get_action_name($data['status'], false))) . ' PR (' . $row->pr_code . ')';
                $data_log['action_type'] = $data['status'];
                $data_log['log_date'] = date('Y-m-d H:i:s');
                $data_log['reff_id'] = $pr_id;
                $data_log['feature_id'] = Feature::FEATURE_PR;
                $data_log['remark'] = isset($_POST['reason']) ? $_POST['reason'] : '';

                if ($data['status'] == STATUS_APPROVE) {
                    if ($row->status == STATUS_APPROVE) {
                        $result['type'] = '0';
                        $result['message'] = 'PR already approved.';
                    } else {
                        $data['user_modified'] = my_sess('user_id');
                        $data['date_modified'] = date('Y-m-d H:i:s');

                        $data['user_approved'] = $data['user_modified'];
                        $data['date_approved'] = $data['date_modified'];

                        $this->mdl_general->update('in_pr', array('pr_id' => $pr_id), $data);

                        $qry_item = $this->db->get_where('in_ms_item', array('item_code' => Purchasing::DIRECT_PURCHASE));
                        $row_item = $qry_item->row();

                        $this->mdl_general->update('in_pr_item', array('pr_id' => $pr_id), array('item_id' => $row_item->item_id));

                        $this->db->insert('app_log', $data_log);

                        $this->send_email($pr_id, $data['status']);

                        $result['type'] = '1';
                        $result['message'] = 'Successfully approve PR.';
                    }
                } else if ($data['status'] == STATUS_DISAPPROVE) {
                    $check_po = $this->db->get_where('in_po', array('pr_id' => $pr_id, 'status <> ' => STATUS_CANCEL));

                    if ($check_po->num_rows() <= 0) {
                        if ($row->status == STATUS_DISAPPROVE) {
                            $result['type'] = '0';
                            $result['message'] = 'Request already disapproved.';
                        } else {
                            $data['user_modified'] = my_sess('user_id');
                            $data['date_modified'] = date('Y-m-d H:i:s');

                            $this->mdl_general->update('in_pr', array('pr_id' => $pr_id), $data);

                            $this->db->insert('app_log', $data_log);

                            $this->send_email($pr_id, $data['status']);

                            $result['type'] = '1';
                            $result['message'] = 'Successfully disapprove PR.';
                        }
                    } else {
                        $result['type'] = '0';
                        $result['message'] = 'PR cannot be Disapproved. Related PO already in process.';
                    }
                } else if ($data['status'] == STATUS_CANCEL) {
                    $check_po = $this->db->get_where('in_po', array('pr_id' => $pr_id, 'status <> ' => STATUS_CANCEL));

                    if ($check_po->num_rows() <= 0) {
                        if ($row->status == STATUS_CANCEL) {
                            $result['type'] = '0';
                            $result['message'] = 'PR already canceled.';
                        } else {
                            $data['user_modified'] = my_sess('user_id');
                            $data['date_modified'] = date('Y-m-d H:i:s');

                            $this->mdl_general->update('in_pr', array('pr_id' => $pr_id), $data);

                            $this->db->insert('app_log', $data_log);

                            $qry_det = $this->db->get_where('in_pr_item', array('pr_id' => $pr_id));
                            foreach ($qry_det->result() as $row_det) {
                                if ($row_det->item_id > 0) {
                                    $this->mdl_general->update('in_ms_item', array('item_id' => $row_det->item_id), array('status_order' => 0));
                                }
                            }

                            $this->send_email($pr_id, $data['status']);

                            $result['type'] = '1';
                            $result['message'] = 'Successfully cancel PR.';
                        }
                    } else {
                        $result['type'] = '0';
                        $result['message'] = 'PR cannot be Canceled. Related PO already in process.';
                    }
                } else if ($data['status'] == STATUS_CLOSED) {
                    if ($row->status == STATUS_CLOSED) {
                        $result['type'] = '0';
                        $result['message'] = 'PR already completed.';
                    } else {
                        $data['user_modified'] = my_sess('user_id');
                        $data['date_modified'] = date('Y-m-d H:i:s');

                        $this->mdl_general->update('in_pr', array('pr_id' => $pr_id), $data);

                        $qry_det = $this->db->get_where('in_pr_item', array('pr_id' => $pr_id));
                        foreach ($qry_det->result() as $row_det) {
                            if ($row_det->item_id > 0) {
                                if ($row_det->item_qty == $row_det->qty_remain) {
                                    $this->mdl_general->update('in_ms_item', array('item_id' => $row_det->item_id), array('status_order' => 0));
                                }
                            }
                        }

                        $this->db->insert('app_log', $data_log);

                        $this->send_email($pr_id, $data['status']);

                        $result['type'] = '1';
                        $result['message'] = 'Successfully complete PR.';
                    }
                }
            }
        }

        if ($is_redirect) {
            $this->session->set_flashdata('flash_message_class', ($result['type'] == '1' ? 'success' : 'danger'));
            $this->session->set_flashdata('flash_message', $result['message']);
        }

        echo json_encode($result);
    }

    public function pdf_pr($pr_id = 0)
    {
        if ($pr_id > 0) {
            $this->load->model('purchasing/mdl_purchasing');

            $qry = $this->mdl_purchasing->get_pr_list(array('in_pr.pr_id' => $pr_id));
            if ($qry->num_rows() > 0) {
                $this->load->library('dompdf_gen');

                $data['row'] = $qry->row();

                $data['qry_det'] = $this->mdl_purchasing->get_pr_detail(false, array('in_pr_item.pr_id' => $pr_id, 'in_pr_item.status <>' => STATUS_CANCEL));

                $this->load->view('purchasing/pr/pdf_pr.php', $data);

                $html = $this->output->get_output();

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream($data['row']->pr_code . ".pdf", array('Attachment' => 0));

            } else {
                tpd_404();
            }
        } else {
            tpd_404();
        }
    }

    public function send_email($pr_id = 0, $status = 0)
    {
        $result = array();
        $result['success'] = true;
        $result['message'] = '';

        if ($pr_id > 0 && $status > 0) {
            $qry_notif = $this->db->get_where('ms_mail_notification', array('feature_id' => Feature::FEATURE_PR, 'action_id' => $status));
            if ($qry_notif->num_rows() > 0) {
                $row_notif = $qry_notif->row();

                $this->load->model('purchasing/mdl_purchasing');
                $this->load->model('mdl_email');
                $this->load->helper('email');

                $qry = $this->mdl_purchasing->get_pr_list(array('in_pr.pr_id' => $pr_id));
                $row = $qry->row();

                $subject = '';
                $title = '';
                $header = '';
                $detail = '';
                $action = '';
                $to = array();
                $cc = array();
                $bcc = array();

                if (trim($row_notif->mail_to) != '') {
                    $to = explode(";", $row_notif->mail_to);
                }
                if (trim($row_notif->mail_cc) != '') {
                    $cc = explode(";", $row_notif->mail_cc);
                }
                if (trim($row_notif->mail_bcc) != '') {
                    $bcc = explode(";", $row_notif->mail_bcc);
                }

                if ($status == STATUS_NEW) {
                    //approver PR
                    $qry_app = $this->db->query("SELECT user_email FROM view_get_role_detail WHERE department_id = '" . $row->department_id . "' AND action_do = '" . STATUS_APPROVE . "' AND module_name = 'purchasing' AND controller_name = 'pr' AND function_name IN ('pr_form', 'pr_manage') GROUP BY user_email");
                    if ($qry_app->num_rows() > 0) {
                        foreach ($qry_app->result() as $row_app) {
                            if (trim($row_app->user_email) != '') {
                                if (valid_email(trim($row_app->user_email))) {
                                    array_push($to, trim($row_app->user_email));
                                }
                            }
                        }
                    }

                    //created PR
                    if (trim($row->user_email) != '') {
                        if (valid_email(trim($row->user_email))) {
                            if (!in_array(trim($row->user_email), $to) && !in_array(trim($row->user_email), $cc)) {
                                array_push($cc, $row->user_email);
                            }
                        }
                    }
                } else if ($status == STATUS_REJECT) {
                    //created PR
                    if (trim($row->user_email) != '') {
                        if (valid_email(trim($row->user_email))) {
                            array_push($to, $row->user_email);
                        }
                    }
                } else if ($status == STATUS_CANCEL) {
                    //created PR
                    if (trim($row->user_email) != '') {
                        if (valid_email(trim($row->user_email))) {
                            if (!in_array(trim($row->user_email), $to) && !in_array(trim($row->user_email), $cc)) {
                                array_push($to, $row->user_email);
                            }
                        }
                    }
                } else if ($status == STATUS_DISAPPROVE) {
                    //created PR
                    if (trim($row->user_email) != '') {
                        if (valid_email(trim($row->user_email))) {
                            if (!in_array(trim($row->user_email), $to) && !in_array(trim($row->user_email), $cc)) {
                                array_push($to, $row->user_email);
                            }
                        }
                    }
                } else if ($status == STATUS_CLOSED) {
                    //created PR
                    if (trim($row->user_email) != '') {
                        if (valid_email(trim($row->user_email))) {
                            if (!in_array(trim($row->user_email), $to) && !in_array(trim($row->user_email), $cc)) {
                                array_push($to, $row->user_email);
                            }
                        }
                    }
                } else if ($status == STATUS_APPROVE) {
                    //purchasing
                    $qry_purc = $this->db->query("SELECT user_email FROM view_get_role_detail WHERE action_do = '" . STATUS_NEW . "' AND module_name = 'purchasing' AND controller_name = 'po' AND function_name IN ('po_form', 'po_manage') GROUP BY user_email");
                    if ($qry_purc->num_rows() > 0) {
                        foreach ($qry_purc->result() as $row_purc) {
                            if (trim($row_purc->user_email) != '') {
                                if (valid_email(trim($row_purc->user_email))) {
                                    array_push($to, trim($row_purc->user_email));
                                }
                            }
                        }
                    }

                    //created PR
                    if (trim($row->user_email) != '') {
                        if (valid_email(trim($row->user_email))) {
                            if (!in_array(trim($row->user_email), $to) && !in_array(trim($row->user_email), $cc)) {
                                array_push($cc, $row->user_email);
                            }
                        }
                    }
                }

                $subject .= $row_notif->subject;
                $title .= $row_notif->title;
                $header .= '<table style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #57697e;">
                        <tr>
                            <td> PR Code </td>
                            <td> : </td>
                            <td> ' . $row->pr_code . ' </td>
                        </tr>
                        <tr>
                            <td> Date Prepare </td>
                            <td> : </td>
                            <td> ' . ymd_to_dmy($row->date_prepare) . ' </td>
                        </tr>
                        <tr>
                            <td> Delivery Date </td>
                            <td> : </td>
                            <td> ' . ymd_to_dmy($row->delivery_date) . ' </td>
                        </tr>
                        <tr>
                            <td> Remarks </td>
                            <td> : </td>
                            <td> ' . $row->remarks . ' </td>
                        </tr>
                    </table>';

                $qry_detail = $this->mdl_purchasing->get_pr_detail(false, array('in_pr_item.pr_id' => $pr_id, 'in_pr_item.status <>' => STATUS_CANCEL));

                if ($qry_detail->num_rows() > 0) {
                    $detail .= '<table width="96%" style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #57697e; border: 1px solid #57697e; border-collapse: collapse;" border="1" cellspacing="0" cellpadding="0">
                            <tr>
                                <th width="1%" style="padding: 5px 10px;"> No. </th>
                                <th style="padding: 5px 10px;"> Supplier </th>
                                <th style="padding: 5px 10px;"> Description </th>
                                <th style="padding: 5px 10px;"> UOM </th>
                                <th style="padding: 5px 10px;"> Qty </th>
                            </tr>';
                    $i = 1;
                    foreach ($qry_detail->result() as $row_detail) {
                        $detail .= '<tr>
                                    <td style="padding: 3px 7px; text-align:center; vertical-align: top;"> ' . $i . '. </td>
                                    <td style="padding: 3px 7px; vertical-align: top;"> ' . $row_detail->supplier_name . ' </td>
                                    <td style="padding: 3px 7px; vertical-align: top;"> ' . nl2br($row_detail->item_desc) . ' </td>
                                    <td style="padding: 3px 7px; text-align:center; vertical-align: top;"> ' . $row_detail->uom_code . ' </td>
                                    <td style="padding: 3px 7px; text-align:right; vertical-align: top;"> ' . $row_detail->item_qty . ' </td>
                                </tr>';
                        $i++;
                    }
                    $detail .= '</table>';
                }

                $body = $this->load->view('layout/email.php', '', true);

                $body = str_replace("{title}", $title, $body);
                $body = str_replace("{header}", $header, $body);
                $body = str_replace("{detail}", $detail, $body);
                $body = str_replace("{action}", $action, $body);

                $send = $this->mdl_email->sendEmailSmtp($to, $cc, $bcc, $subject, $body);

                if ($send['success'] == true) {
                    $result['message'] = 'Successfull send email';
                } else {
                    $result['success'] = false;
                    $result['message'] = 'Failed send email.<br/>' . $send['message'];
                }
            } else {
                $result['success'] = false;
                $result['message'] = 'Empty mail notification';
            }
        } else {
            $result['success'] = false;
            $result['message'] = 'Empty PR ID or Status';
        }

        return $result;
        //print_r($result);
    }

    #endregion

}


/* End of file pr.php */
/* Location: ./application/controllers/purchsing/pr.php */