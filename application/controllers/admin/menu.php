<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends CI_Controller
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
        $this->menu_manage();
    }

    #region MENU

    public function menu_manage($type = 1, $id = 0)
    {
        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');

        if ($type == 1) { //List
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
            array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');

            array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

            $data = array();

            $this->load->view('layout/header', $data_header);
            $this->load->view('admin/menu_list', $data);
            $this->load->view('layout/footer');
        } else { // Department Form
            array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');

            array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
            array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');

            $data['action'] = array(
                STATUS_NEW => STATUS_NEW,
                STATUS_EDIT => STATUS_EDIT,
                STATUS_PROCESS => STATUS_PROCESS,
                STATUS_APPROVE => STATUS_APPROVE,
                STATUS_DISAPPROVE => STATUS_DISAPPROVE,
                STATUS_CANCEL => STATUS_CANCEL,
                STATUS_POSTED => STATUS_POSTED,
                STATUS_CLOSED => STATUS_CLOSED,
                STATUS_DELETE => STATUS_DELETE,
                STATUS_AUDIT => STATUS_AUDIT,
                STATUS_REJECT => STATUS_REJECT,
                STATUS_PRINT => STATUS_PRINT,
                STATUS_UNLOCK => STATUS_UNLOCK
            );

            if ($id > 0) {
                $qry = $this->mdl_general->get('ms_menu', array('menu_id' => $id));
                $data['row'] = $qry->row();

                $data['menu_act'] = array();
                $qry_menu_act = $this->db->get_where('ms_menu_action', array('menu_id' => $id));
                foreach ($qry_menu_act->result() as $row_act) {
                    $data['menu_act'][$row_act->status_action] = $row_act->status_label;
                }
            }

            $data['menu_id'] = $id;
            $this->load->view('layout/header', $data_header);
            $this->load->view('admin/menu_form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function submit_menu()
    {
        if (isset($_POST)) {
            $menu_id = $_POST['menu_id'];
            $has_error = false;

            $data['parent_id'] = isset($_POST['parent_id']) ? $_POST['parent_id'] : 0;
            $data['module_name'] = strtolower($_POST['module_name']);
            $data['controller_name'] = strtolower($_POST['controller_name']);
            $data['function_name'] = strtolower($_POST['function_name']);
            $data['function_parameter'] = trim($_POST['function_parameter']);
            $data['menu_name'] = trim($_POST['menu_name']);
            $data['menu_description'] = trim($_POST['menu_desc']);
            $data['menu_icon'] = trim($_POST['menu_icon']);
            $data['sorting'] = trim($_POST['sorting']);
            $data['is_new_tab'] = (isset($_POST['is_new_tab']) ? 1 : 0);
            $data['status'] = $_POST['status'];

            if ($menu_id > 0) {
                $this->mdl_general->update('ms_menu', array('menu_id' => $menu_id), $data);

                $this->db->delete('ms_menu_action', array('menu_id' => $menu_id));

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Menu successfully updated.');
            } else {
                $data['status'] = STATUS_NEW;

                $this->db->insert('ms_menu', $data);
                $menu_id = $this->db->insert_id();

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Menu successfully created.');
            }

            foreach ($_POST['menu_action'] as $act_key => $act_val) {
                $this->db->insert('ms_menu_action', array('menu_id' => $menu_id, 'status_action' => $act_val, 'status_label' => trim($_POST['status_label'][$act_val])));
            }

            if ($has_error) {
                redirect(base_url('admin/menu/menu_manage/' . $menu_id . '.tpd'));
            } else {
                if (isset($_POST['save_close'])) {
                    redirect(base_url('admin/menu/menu_manage/1.tpd'));
                } else {
                    redirect(base_url('admin/menu/menu_manage/0/' . $menu_id . '.tpd'));
                }
            }
        }
    }

    public function menu_list()
    {
        $this->load->model('admin/mdl_menu');

        $where['mn1.status <>'] = STATUS_DELETE;
        $like = array();

        //COUNT WHERE
        $whereCount['status <>'] = STATUS_DELETE;
        //COUNT LIKE
        $likeCount = array();

        $qry_tot = $this->mdl_general->count('ms_menu', $whereCount, $likeCount);

        $iTotalRecords = $qry_tot;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'mn1.parent_id asc, mn1.sorting asc';

        //$qry = $this->mdl_menu->get($where, $like, $order, $iDisplayLength, $iDisplayStart);
        $qry = $this->mdl_general->get('ms_menu', array('status <>' => STATUS_DELETE, 'parent_id' => 0), $likeCount, 'sorting');
        foreach ($qry->result() as $row) {
            $records["data"][] = array(
                $row->menu_name,
                $row->menu_description,
                $row->menu_icon,
                $row->sorting,
                $row->module_name,
                $row->controller_name,
                $row->function_name,
                $row->function_parameter,
                get_status_active($row->status),
                '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('admin/menu/menu_manage/0/' . $row->menu_id) . '.tpd"><i class="fa fa-search"></i></a>'
            );

            $qry2 = $this->mdl_general->get('ms_menu', array('status <>' => STATUS_DELETE, 'parent_id' => $row->menu_id), $likeCount, 'sorting');
            foreach ($qry2->result() as $row2) {
                $records["data"][] = array(
                    '-- ' . $row2->menu_name,
                    $row2->menu_description,
                    $row2->menu_icon,
                    $row2->sorting,
                    $row2->module_name,
                    $row2->controller_name,
                    $row2->function_name,
                    $row2->function_parameter,
                    get_status_active($row2->status),
                    '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('admin/menu/menu_manage/0/' . $row2->menu_id) . '.tpd"><i class="fa fa-search"></i></a>'
                );

                $qry3 = $this->mdl_general->get('ms_menu', array('status <>' => STATUS_DELETE, 'parent_id' => $row2->menu_id), $likeCount, 'sorting');
                foreach ($qry3->result() as $row3) {
                    $records["data"][] = array(
                        '-- -- ' . $row3->menu_name,
                        $row3->menu_description,
                        $row3->menu_icon,
                        $row3->sorting,
                        $row3->module_name,
                        $row3->controller_name,
                        $row3->function_name,
                        $row3->function_parameter,
                        get_status_active($row3->status),
                        '<a data-original-title="Filter" data-placement="top" data-container="body" class="btn btn-xs green-meadow tooltips" href="' . base_url('admin/menu/menu_manage/0/' . $row3->menu_id) . '.tpd"><i class="fa fa-search"></i></a>'
                    );
                }
            }
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    #endregion

}

/* End of file user.php */
/* Location: ./application/controllers/admin/user.php */