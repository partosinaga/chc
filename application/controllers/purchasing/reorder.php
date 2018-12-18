<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reorder extends CI_Controller {

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

        $this->data_footer = array(
            'footer_script' => array()
        );
    }

    public function index()
    {
        tpd_404();
    }

    public function reorder_list(){
        $data_header = $this->data_header;

        $data = array();
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        $this->load->view('layout/header', $data_header);
        $this->load->view('purchasing/reorder/reorder_list', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_reorder_list($menu_id = 0){
        $where = array();
        $like = array();

        $where_string = ' on_hand_qty < min_stock ';
        if(isset($_REQUEST['filter_item_code'])){
            if($_REQUEST['filter_item_code'] != ''){
                $like['item_code'] = $_REQUEST['filter_item_code'];
            }
        }
        if(isset($_REQUEST['filter_item_desc'])){
            if($_REQUEST['filter_item_desc'] != ''){
                $like['item_desc'] = $_REQUEST['filter_item_desc'];
            }
        }
        if(isset($_REQUEST['filter_uom1'])){
            if($_REQUEST['filter_uom1'] != ''){
                $like['uom_in_code'] = $_REQUEST['filter_uom1'];
            }
        }
        if(isset($_REQUEST['filter_uom2'])){
            if($_REQUEST['filter_uom2'] != ''){
                $like['uom_out_code'] = $_REQUEST['filter_uom2'];
            }
        }

        $iTotalRecords = $this->mdl_general->count('view_in_get_item_stock', $where, $like, '', '', array(), $where_string);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'item_code asc ';

        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 0){
                $order = 'item_code ' . $_REQUEST['order'][0]['dir'];
            }
        }
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'item_desc ' . $_REQUEST['order'][0]['dir'];
            }
        }
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 2){
                $order = 'uom_in_code ' . $_REQUEST['order'][0]['dir'];
            }
        }
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 3){
                $order = 'uom_out_code ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_general->get('view_in_get_item_stock', $where, $like, $order, $iDisplayLength, $iDisplayStart, '', '', array(), $where_string);

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $status = '';
            if ($row->status_order == 1) {
                $status = '<span class="badge badge-primary badge-roundless"> ORDERED </span>';
            } else {
                if (check_session_action($menu_id, STATUS_NEW)) {
                    $status = '<input type="checkbox" name="order[' . $row->item_id . ']" value="' . $row->item_id . '" />';
                }
            }

            $records["data"][] = array(
                $row->item_code,
                $row->item_desc,
                $row->uom_in_code,
                $row->qty_distribution,
                $row->uom_out_code,
                '<span class="mask_currency">' . $row->on_hand_qty . '</span>',
                '<span class="mask_currency">' . $row->min_stock . '</span>',
                '<span class="mask_currency">' . $row->max_stock . '</span>',
                '<span class="mask_currency">' . ($row->max_stock - $row->on_hand_qty) . '</span>',
                $status
            );
            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

    public function ajax_reorder_submit() {
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['debug'] = '1';

        $array_ids = $_POST['id'];

        if (count($array_ids) > 0) {
            $this->db->trans_begin();

            $data['date_prepare'] = date('Y-m-d');
            $data['remarks'] = 'Reorder Item';
            $data['delivery_date'] = date('Y-m-d');
            $data['department_id'] = my_sess('department_id');

            $data['pr_code'] = $this->mdl_general->generate_code(Feature::FEATURE_PR, $data['date_prepare']);

            $data['status'] = STATUS_APPROVE;
            $data['user_created'] = my_sess('user_id');
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['user_approved'] = my_sess('user_id');
            $data['date_approved'] = date('Y-m-d H:i:s');;

            if($data['pr_code'] != ''){
                $this->db->insert('in_pr', $data);
                $pr_id = $this->db->insert_id();

                $data_log = array();
                $data_log['user_id']        = my_sess('user_id');
                $data_log['log_date']       = date('Y-m-d H:i:s');
                $data_log['reff_id']        = $pr_id;
                $data_log['feature_id']     = Feature::FEATURE_PR;
                $data_log['log_subject']    = 'Create Reorder PR (' . $data['pr_code'] . ')';
                $data_log['action_type']    = STATUS_NEW;
                $this->db->insert('app_log', $data_log);

                $data_log_approve = array();
                $data_log_approve['user_id']        = my_sess('user_id');
                $data_log_approve['log_date']       = date('Y-m-d H:i:s');
                $data_log_approve['reff_id']        = $pr_id;
                $data_log_approve['feature_id']     = Feature::FEATURE_PR;
                $data_log_approve['log_subject']    = 'Approve Reorder PR (' . $data['pr_code'] . ')';
                $data_log_approve['action_type']    = STATUS_APPROVE;
                $this->db->insert('app_log', $data_log_approve);
            } else {
                $result['valid'] = '0';
                $result['message'] = 'Failed generating code.';
            }

            if($result['valid'] == '1'){
                //detail
                foreach ($array_ids as $item_id) {
                    $data_detail = array();

                    $qry_item = $this->db->get_where('view_in_get_item_stock', array('item_id' => $item_id));
                    $row_item = $qry_item->row();

                    $item_qty = ($row_item->max_stock - $row_item->on_hand_qty) / $row_item->qty_distribution;

                    $data_detail['pr_id']       = $pr_id;
                    $data_detail['item_id']     = $item_id;
                    $data_detail['item_desc']   = $row_item->item_desc;
                    $data_detail['item_qty']    = $item_qty;
                    $data_detail['qty_remain']  = $item_qty;
                    $data_detail['item_type']   = Purchasing::ITEM_MATERIAL;
                    $data_detail['uom_id']      = $row_item->uom_id;
                    $data_detail['supplier_id'] = 0;
                    $data_detail['item_url']    = '';
                    $data_detail['status']      = STATUS_NEW;

                    $this->db->insert('in_pr_item', $data_detail);

                    $this->mdl_general->update('in_ms_item', array('item_id' => $item_id), array('status_order' => 1));
                }
            }

            if($result['valid'] == '1') {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    $result['valid'] = '0';
                    $result['message'] = 'Something has wrong, please try again later.';
                } else {
                    $this->db->trans_commit();

                    $result['message'] = 'Successfully Reorder item and sent as Approved PR.';
                }
            }
        } else {
            $result['valid'] = '0';
            $result['message'] = 'Please select Item.';
        }

        echo json_encode($result);
    }

}