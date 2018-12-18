<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales_package extends CI_Controller
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

    public function so_package_form()
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
        array_push($data_header['script'], base_url() . 'assets/sales/sales_package.js');

        $date = date('Y-m-d');
        $query = $this->M_sales_order->get_so_no($date);

        $data['so_number'] = $query->so_number;
        $this->load->view('layout/header', $data_header);
        $this->load->view('sales/sales_order/so_package_form', $data);
        $this->load->view('layout/footer', $data_footer);
    }

    public function ajax_package_list()
    {
        $where['status ='] = STATUS_NEW;
        $like = array();

        if (isset($_REQUEST['filter_name'])) {
            if ($_REQUEST['filter_name'] != '') {
                $like['package_group_desc'] = $_REQUEST['filter_name'];
            }
        }
        if (isset($_REQUEST['filter_price'])) {
            if ($_REQUEST['filter_price'] != '') {
                $like['price'] = $_REQUEST['filter_price'];
            }
        }
        if (isset($_REQUEST['filter_code'])) {
            if ($_REQUEST['filter_code'] != '') {
                $like['package_group_code'] = $_REQUEST['filter_code'];
            }
        }

        $iTotalRecords = $this->M_sales_order->get_package_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'package_group_id desc ';

        $qry = $this->M_sales_order->get_package_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $records["data"][] = array(
                $i . '.',
                $row->package_group_code,
                $row->package_group_desc,
                number_format($row->price),
                '<div class="btn-group">
                    <button class="btn green-meadow btn-xs select-package" type="button" item-code="' . $row->package_group_code . '" item-id="' . $row->package_group_id . '" description="' . $row->package_group_desc . '" uom="-" uom-id="-" price="' . $row->price . '" >
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

    public function ajax_modal_package()
    {
        $this->load->view('sales/sales_order/ajax_package_list');
    }

    public function append_row()
    {
        $id = $_POST['item_id'];
        $query = $this->M_sales_order->get_detail_package($id);
        $result = '';
        $group = '';
        foreach ($query->result() as $row) {
            if ($group != $row->package_group_id) {
                $result .= '<tr style="font-weight: bold; background-color: #eeeeee" grouping="' . $row->package_group_id . '">
                         <td align="center" >' . $row->package_group_code . '</td>
                         <td>' . $row->package_group_desc . '</td>
                         <td align="right"><input type="text" class="form-control input-sm mask_currency calcu-pack" name="package_price[]" value="' . $row->price . '"> <input type=hidden class="class_status" name="status_detail[]"  value= "1"> </td>
                         <td><input type="text" class="form-control input-sm number_only calcu-pack" name="qty[]"></td>
                         <td>PACKAGE</td>
                         <td><input type="text" class="form-control input-sm mask_currency calcu-pack" name="discount[]"></td>
                         <td><input type="text" class="form-control input-sm mask_currency" name="amount" readonly></td>
                         <td align="center"><button class="btn red btn-xs remove-package" grouping="' . $row->package_group_id . '"><i class="fa fa-remove"></i></button></td>
                        </tr>';
                $group = $row->package_group_id;
            } else {
                $result .= '';
            }
            $result .= '<tr class="package-list" grouping="' . $row->package_group_id . '">
                        <td align="center">' . $row->item_code . '</td>
                        <td style="padding-left: 3em;">' . $row->item_desc . '</td>
                        <td align="right"><input type="text" class="form-control input-sm mask_currency" name="item_price" value="' . $row->item_price . '"></td>
                        <td align="right">' . $row->item_qty . '</td>
                        <td align="center">' . $row->uom_code . '</td>
                        <td colspan="3" rowspan=""></td>
                    </tr>';
        }

        echo $result;
    }
}