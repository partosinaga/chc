<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller
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
        $this->uom_manage();
    }

    #region UOM

    public function uom_manage($type = 1, $id = 0)
    {
        $data_header = $this->data_header;

        if ($type == 1) { //UOM List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('purchasing/setup/uom_list', $data);
            $this->load->view('layout/footer');
        } else { // UOM Form
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            $data = array();

            $data['uom_id'] = $id;
            if ($id > 0) {
                $qry = $this->db->get_where('in_ms_uom', array('uom_id' => $id));
                $data['row'] = $qry->row();
            }

            $this->load->view('layout/header', $data_header);
            $this->load->view('purchasing/setup/uom_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function uom_submit()
    {
        if (isset($_POST)) {
            $has_error = false;
            $uom_id = $_POST['uom_id'];

            $data['uom_code'] = trim($_POST['uom_code']);
            $data['uom_desc'] = trim($_POST['uom_desc']);

            if ($uom_id > 0) {
                $data['user_modified'] = my_sess('user_id');
                $data['date_modified'] = date('Y-m-d H:i:s');
                $data['status'] = $_POST['status'];

                $exist = $this->mdl_general->count('in_ms_uom', array('uom_code' => $data['uom_code'], 'uom_id <>' => $uom_id));

                if ($exist == 0) {
                    $this->mdl_general->update('in_ms_uom', array('uom_id' => $uom_id), $data);

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successful update uom.');
                } else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'UOM code already exist.');
                }
            } else {
                $data['user_created'] = my_sess('user_id');
                $data['date_created'] = date('Y-m-d H:i:s');
                $data['status'] = STATUS_NEW;

                $exist = $this->mdl_general->count('in_ms_uom', array('uom_code' => $data['uom_code']));

                if ($exist == 0) {
                    $this->db->insert('in_ms_uom', $data);
                    $uom_id = $this->db->insert_id();

                    $this->session->set_flashdata('flash_message_class', 'success');
                    $this->session->set_flashdata('flash_message', 'Successful add uom.');
                } else {
                    $has_error = true;

                    $this->session->set_flashdata('flash_message_class', 'danger');
                    $this->session->set_flashdata('flash_message', 'UOM code already exist.');
                }
            }

            if ($has_error) {
                redirect(base_url('purchasing/setup/uom_manage/0/' . $uom_id . '.tpd'));
            } else {
                if (isset($_POST['save_close'])) {
                    redirect(base_url('purchasing/setup/uom_manage/1.tpd'));
                } else {
                    redirect(base_url('purchasing/setup/uom_manage/0/' . $uom_id . '.tpd'));
                }
            }
        }
    }

    public function uom_delete($uom_id = 0)
    {
        if ($uom_id > 0) {
            $this->mdl_general->update('in_ms_uom', array('uom_id' => $uom_id), array('status' => STATUS_DELETE));

            $this->session->set_flashdata('flash_message_class', 'success');
            $this->session->set_flashdata('flash_message', 'Successful delete uom.');
        }

        redirect(base_url('purchasing/setup/uom_manage/1.tpd'));
    }

    public function uom_list($menu_id = 0)
    {
        $where['status <>'] = STATUS_DELETE;
        $like = array();
        if (isset($_REQUEST['filter_uomcode'])) {
            if ($_REQUEST['filter_uomcode'] != '') {
                $like['uom_code'] = $_REQUEST['filter_uomcode'];
            }
        }
        if (isset($_REQUEST['filter_uomdesc'])) {
            if ($_REQUEST['filter_uomdesc'] != '') {
                $like['uom_desc'] = $_REQUEST['filter_uomdesc'];
            }
        }
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['status'] = $_REQUEST['filter_status'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('in_ms_uom', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'uom_code asc';
        if (isset($_REQUEST['order'])) {
            if ($_REQUEST['order'][0]['column'] == 1) {
                $order = 'uom_code ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 2) {
                $order = 'uom_desc ' . $_REQUEST['order'][0]['dir'];
            }
            if ($_REQUEST['order'][0]['column'] == 3) {
                $order = 'status ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('in_ms_uom', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $records["data"][] = array(
                $i . '.',
                $row->uom_code,
                $row->uom_desc,
                get_status_active($row->status),
                '<div class="btn-group btn-group-solid">
					' . (check_session_action($menu_id, STATUS_EDIT) ? '<a class="btn btn-xs green-meadow tooltips" data-original-title="View" data-placement="top" data-container="body" href="' . base_url('purchasing/setup/uom_manage/0/' . $row->uom_id) . '.tpd"> <i class="fa fa-search"></i> </a>' : '') . '
					' . (check_session_action($menu_id, STATUS_DELETE) ? '<a class="btn btn-xs red-thunderbird btn-bootbox tooltips" data-original-title="Delete" data-placement="top" data-container="body" href="javascript:;" data-id="' . $row->uom_id . '"> <i class="fa fa-times"></i> </a>' : '') . '
				</div>'
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    #endregion

    #region PO Approval

    public function po_approval_manage($type = 1, $id = 0)
    {
        $data_header = $this->data_header;

        if ($type == 1) { //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('purchasing/setup/po_approval_list', $data);
            $this->load->view('layout/footer');
        } else { // Form
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

            $data = array();

            $data['approval_id'] = $id;
            if ($id > 0) {
                $qry = $this->db->get_where('in_po_approval', array('approval_id' => $id));
                $data['row'] = $qry->row();
            }

            $this->load->view('layout/header', $data_header);
            $this->load->view('purchasing/setup/po_approval_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function ajax_po_approval_list($menu_id = 0)
    {
        $where = array();
        $like = array();

        $iTotalRecords = $this->mdl_general->count('in_po_approval', $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'approval_id asc';

        $qry = $this->mdl_general->get('in_po_approval', $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $records["data"][] = array(
                $i . '.',
                $row->approval_name,
                $row->position,
                format_num($row->min_amount, 0),
                format_num($row->max_amount, 0),
                '<div class="btn-group btn-group-solid">
					' . (check_session_action($menu_id, STATUS_EDIT) ? '<a class="btn btn-xs green-meadow tooltips" data-original-title="View" data-placement="top" data-container="body" href="' . base_url('purchasing/setup/po_approval_manage/0/' . $row->approval_id) . '.tpd"> <i class="fa fa-search"></i> </a>' : '') . '
					' . (check_session_action($menu_id, STATUS_DELETE) ? '<a class="btn btn-xs red-thunderbird btn-bootbox tooltips" data-original-title="Delete" data-placement="top" data-container="body" href="javascript:;" data-id="' . $row->approval_id . '"> <i class="fa fa-times"></i> </a>' : '') . '
				</div>'
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function po_approval_submit()
    {
        if (isset($_POST)) {
            $has_error = false;
            $approval_id = $_POST['approval_id'];
            $approvalusers = $_POST['approvalusers'];

            $data['approval_name'] = trim($_POST['approval_name']);
            $data['position'] = trim($_POST['position']);
            $data['min_amount'] = str_replace(',', '', trim($_POST['min_amount']));
            $data['max_amount'] = str_replace(',', '', trim($_POST['max_amount']));

            $usernames = explode(',', $approvalusers);
            $currentuserid = array();
            foreach ($usernames as $username) {
                if (trim($username) != '') {
                    $users = $this->db->get_where('ms_user', array('user_name' => $username));
                    if ($users->num_rows() > 0) {
                        $user = $users->row();
                        array_push($currentuserid, $user->user_id);
                    }
                }
            }

            $data['user_id'] = implode(';', $currentuserid);

            if ($approval_id > 0) {
                $this->mdl_general->update('in_po_approval', array('approval_id' => $approval_id), $data);

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful update PO Approval.');
            } else {
                $this->db->insert('in_po_approval', $data);
                $approval_id = $this->db->insert_id();

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful add PO Approval.');
            }

            if ($has_error) {
                redirect(base_url('purchasing/setup/po_approval_manage/0/' . $approval_id . '.tpd'));
            } else {
                if (isset($_POST['save_close'])) {
                    redirect(base_url('purchasing/setup/po_approval_manage/1.tpd'));
                } else {
                    redirect(base_url('purchasing/setup/po_approval_manage/0/' . $approval_id . '.tpd'));
                }
            }
        }
    }

    public function po_approval_delete($approval_id = 0)
    {
        if ($approval_id > 0) {
            $this->db->delete('in_po_approval', array('approval_id' => $approval_id));

            $this->session->set_flashdata('flash_message_class', 'success');
            $this->session->set_flashdata('flash_message', 'Successful delete PO Approval.');
        }

        redirect(base_url('purchasing/setup/po_approval_manage/1.tpd'));
    }

    #endregion

}

/* End of file setup.php */
/* Location: ./application/controllers/purchsing/setup.php */