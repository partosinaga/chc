<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!is_login()) {
            redirect(base_url('login/login_form.tpd'));
        }
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

    public function index()
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
        $this->load->view('sales/setup/setup');
        $this->load->view('layout/footer', $data_footer);
    }

    public function customer_form($id = 0)
    {
        $data_header = $this->data_header;
        $data_footer = $this->data_footer;
        $data = array();
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
//        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
//        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
//        array_push($data_header['script'], base_url() . 'assets/custom/form-validation.js');

        if ($id == 0) { //form
            $data['customer_id'] = 0;
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/customer_form', $data);
            $this->load->view('layout/footer', $data_footer);
        } else { //edit/view

            $data['customer_id'] = $id;
            if ($id > 0) {
                $qry_head = $this->M_setup->get_customer($id);
                $data['row'] = $qry_head->row();
                $data['address'] = $this->M_setup->get_detail_customer($id);

            }
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/customer_form', $data);
            $this->load->view('layout/footer', $data_footer);
        }

    }

    public function ajax_customer_submit()
    {
        $this->db->trans_begin();
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = array();

        $data = array();


        if (isset($_POST)) {
            $cust_id = $_POST['customer_id'];
            if ($cust_id > 0) {//update
                $data['customer_name'] = $_POST['name'];
                $data['email'] = $_POST['email'];
                $data['customer_phone'] = $_POST['phone'];
                $data['customer_cellular'] = $_POST['cellular'];
                $data['customer_fax'] = $_POST['fax'];
                $data['gender'] = $_POST['gender'];
                $data['dob'] = $_POST['dob'];
                $data['user_modified'] = my_sess('user_id');
                $data['date_modified'] = date('Y-m-d H:i:s.000');
                $data['status'] = $_POST['status'];;
                $this->M_setup->update('ms_customer', array('customer_id' => $cust_id), $data);


                $customer_address_id = $this->input->post('customer_address_id');
                $address_status = $this->input->post('address_status');

                $datab = array();
                for ($b = 0; $b < count($customer_address_id); $b++) {
                    if ($customer_address_id[$b] == 'a') {
                        $address = $this->input->post('address');
                        $postcode = $this->input->post('postcode');
                        $country = $this->input->post('country');
                        $district = $this->input->post('district');
                        $city = $this->input->post('city');
                        $datax = array();
                        for ($i = 0; $i < count($address); $i++) { //insert address
                            $datax[$i] = array(
                                'customer_id' => $cust_id,
                                'customer_address' => $address[$i],
                                'customer_postcode' => $postcode[$i],
                                'customer_country' => $country[$i],
                                'customer_district' => $district[$i],
                                'customer_city' => $city[$i],
                                'user_created' => my_sess('user_id'),
                                'date_created' => date('Y-m-d H:i:s.000'),
                                'user_modified' => my_sess('user_id'),
                                'date_modified' => date('Y-m-d H:i:s.000'),
                                'status' => STATUS_NEW,

                            );
                            $this->db->insert('ms_customer_address', $datax[$i]);
//                            break;
                        }
                    } else {
                        $datab[$b] = array(
                            'user_modified' => my_sess('user_id'),
                            'date_modified' => date('Y-m-d H:i:s.000'),
                            'status' => $address_status[$b],
                        );
                        $this->M_setup->update('ms_customer_address', array('customer_address_id' => $customer_address_id[$b]), $datab[$b]);
                    }
                }

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful update customer.');
            } else {//entry
                $data['customer_name'] = $_POST['name'];
                $data['email'] = $_POST['email'];
                $data['customer_phone'] = $_POST['phone'];
                $data['customer_cellular'] = $_POST['cellular'];
                $data['customer_fax'] = $_POST['fax'];
                $data['gender'] = $_POST['gender'];
                $data['dob'] = $_POST['dob'];
                $data['user_created'] = my_sess('user_id');
                $data['date_created'] = date('Y-m-d H:i:s.000');
                $data['user_modified'] = my_sess('user_id');
                $data['date_modified'] = date('Y-m-d H:i:s.000');
                $data['status'] = STATUS_NEW;
                $this->db->insert('ms_customer', $data);//insert customer


                $customer_id = $this->M_setup->get_id_customer($_POST['name'], $_POST['dob']);
                $address = $this->input->post('address');
                $postcode = $this->input->post('postcode');
                $country = $this->input->post('country');
                $district = $this->input->post('district');
                $city = $this->input->post('city');
                $datax = array();
                for ($i = 0; $i < count($address); $i++) { //insert address
                    $datax[$i] = array(
                        'customer_id' => $customer_id->customer_id,
                        'customer_address' => $address[$i],
                        'customer_postcode' => $postcode[$i],
                        'customer_country' => $country[$i],
                        'customer_district' => $district[$i],
                        'customer_city' => $city[$i],
                        'user_created' => my_sess('user_id'),
                        'date_created' => date('Y-m-d H:i:s.000'),
                        'user_modified' => my_sess('user_id'),
                        'date_modified' => date('Y-m-d H:i:s.000'),
                        'status' => STATUS_NEW,

                    );
                    $this->db->insert('ms_customer_address', $datax[$i]);
                }
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully add Customer.');
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed add Customer, try again later.');
            } else {
                $this->db->trans_commit();

                if (isset($_POST['save_close'])) {
                    $result['link'] = base_url('sales/setup/index.tpd');
                } else {
                    $result['link'] = base_url('sales/setup/customer_form/' . $cust_id . '.tpd');
                }
            }

        }

        echo json_encode($result);
    }

    public function customer_ajax_list()
    {
        $where['status !='] = 8;
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
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['status'] = $_REQUEST['filter_status'];
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
            $btn_action = '';
            $btn_action .= '<li> <a href="javascript:;" customer-id="' . $row->customer_id . '" customer-name="' . $row->customer_name . '" class="view"> View </a> </li>';
            $btn_action .= '<li> <a href="' . base_url('sales/setup/customer_form/' . $row->customer_id) . '.tpd"> Edit </a> </li>';
            $status = get_status_active($row->status);
            $records["data"][] = array(
                $i . '.',
                $row->customer_name,
                $row->email,
                $row->customer_phone,
                $row->customer_cellular,
                $row->customer_fax,
                $row->dob,
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

    public function detail_customer()
    {
        $id = $_POST['cust_id'];
        $query = $this->M_setup->get_detail_customer($id);
//        echo $this->db->last_query();
        $result = '';
        foreach ($query->result() as $row) {
            $result .= '<div class="card">
                          <div class="card-body">
                            <blockquote class="blockquote mb">
                              <p>' . $row->customer_address . '</p>
                              <footer class="blockquote-footer">' . $row->country_name . '| ' . $row->customer_district . ' | ' . $row->customer_city . ' | ' . $row->customer_postcode . ' | ' . get_status_active($row->status) . '</footer>
                            </blockquote>
                          </div>
                        </div>';
        }
        echo $result;


    }


    public function sales()
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
        $this->load->view('sales/setup/sales');
        $this->load->view('layout/footer', $data_footer);
    }

    public function sales_form($id = 0)
    {
        $data_header = $this->data_header;
        $data_footer = $this->data_footer;
        $data = array();
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        if ($id > 0) { //edit
            $data['sales_id'] = $id;

            $qry_head = $this->M_setup->get_sales($id);
            $data['row'] = $qry_head->row();

            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/sales_form', $data);
            $this->load->view('layout/footer', $data_footer);
        } else { //form
            $data['sales_id'] = 0;
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/sales_form', $data);
            $this->load->view('layout/footer', $data_footer);
        }


    }

    public function ajax_sales_submit()
    {
        $this->db->trans_begin();
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = array();

        $data = array();


        if (isset($_POST)) {
            $sales_id = $_POST['sales_id'];
            if ($sales_id > 0) {//update
                $data['sales_name'] = $_POST['name'];
                $data['email'] = $_POST['email'];
                $data['sales_phone'] = $_POST['phone'];
                $data['sales_cellular'] = $_POST['cellular'];
                $data['gender'] = $_POST['gender'];
                $data['dob'] = $_POST['dob'];
                $data['identity_number'] = $_POST['identity'];
                $data['user_modified'] = my_sess('user_id');
                $data['date_modified'] = date('Y-m-d H:i:s.000');
                $data['status'] = $_POST['status'];;
                $this->M_setup->update('ms_sales', array('sales_id' => $sales_id), $data);


                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful update sales.');
            } else {//entry
                $data['sales_name'] = $_POST['name'];
                $data['email'] = $_POST['email'];
                $data['sales_phone'] = $_POST['phone'];
                $data['sales_cellular'] = $_POST['cellular'];
                $data['gender'] = $_POST['gender'];
                $data['dob'] = $_POST['dob'];
                $data['identity_number'] = $_POST['identity'];
                $data['user_created'] = my_sess('user_id');
                $data['date_created'] = date('Y-m-d H:i:s.000');
                $data['user_modified'] = my_sess('user_id');
                $data['date_modified'] = date('Y-m-d H:i:s.000');
                $data['status'] = STATUS_NEW;
                $this->db->insert('ms_sales', $data);//insert customer


                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successfully add sales.');
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed add sales, try again.');
            } else {
                $this->db->trans_commit();

                if (isset($_POST['save_close'])) {
                    $result['link'] = base_url('sales/setup/sales.tpd');
                } else {
                    $result['link'] = base_url('sales/setup/sales_form/' . $sales_id . '.tpd');
                }
            }
        }
        echo json_encode($result);
    }

    public function sales_ajax_list()
    {
        $where['status !='] = 8;
        $like = array();

        if (isset($_REQUEST['filter_name'])) {
            if ($_REQUEST['filter_name'] != '') {
                $like['sales_name'] = $_REQUEST['filter_name'];
            }
        }

        if (isset($_REQUEST['filter_email'])) {
            if ($_REQUEST['filter_email'] != '') {
                $where['email'] = $_REQUEST['filter_email'];
            }
        }
        if (isset($_REQUEST['filter_phone'])) {
            if ($_REQUEST['filter_phone'] != '') {
                $where['sales_phone'] = $_REQUEST['filter_phone'];
            }
        }

        if (isset($_REQUEST['filter_cellular'])) {
            if ($_REQUEST['filter_cellular'] != '') {
                $where['sales'] = $_REQUEST['filter_cellular'];
            }
        }
        if (isset($_REQUEST['filter_identity'])) {
            if ($_REQUEST['filter_identity'] != '') {
                $where['identity'] = $_REQUEST['filter_identity'];
            }
        }
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['status'] = $_REQUEST['filter_status'];
            }
        }


        $iTotalRecords = $this->M_setup->get_sales_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'sales_id desc ';

        $qry = $this->M_setup->get_sales_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
//        echo $this->db->last_query();
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('sales/setup/sales_form/' . $row->sales_id) . '.tpd"> Edit </a> </li>';
            $status = get_status_active($row->status);
            $records["data"][] = array(
                $i . '.',
                $row->sales_name,
                $row->email,
                $row->sales_phone,
                $row->sales_cellular,
                $row->identity_number,
                $row->dob,
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


    public function payment_type()
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
        $this->load->view('sales/setup/payment_type');
        $this->load->view('layout/footer', $data_footer);
    }

    public function payment_form($id = 0)
    {
        $data_header = $this->data_header;
        $data_footer = $this->data_footer;
        $data = array();
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');

        if ($id > 0) {//edit
            $data['sp_type_id'] = $id;
            if ($id > 0) {
                $qry_head = $this->M_setup->get_sales_payment_type($id);
                $data['row'] = $qry_head->row();
            }
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/payment_form', $data);
            $this->load->view('layout/footer', $data_footer);
        } else { //form
            $data['sp_type_id'] = 0;
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/payment_form', $data);
            $this->load->view('layout/footer', $data_footer);
        }


    }

    public function ajax_payment_submit()
    {
        $this->db->trans_begin();
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = array();

        $data = array();


        if (isset($_POST)) {
            $sp_type_id = $_POST['sp_type_id'];
            if ($sp_type_id > 0) { //update
                $data['sp_type_name'] = $_POST['sp_type_name'];
                $data['coa_id'] = $_POST['coa_id'];
                $data['status'] = $_POST['status'];
                $this->M_setup->update('sales_payment_type', array('sp_type_id' => $sp_type_id), $data);

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful upadate sales payment type.');
            } else { //add new
                $data['sp_type_name'] = $_POST['sp_type_name'];
                $data['coa_id'] = $_POST['coa_id'];
                $data['status'] = STATUS_NEW;
                $this->db->insert('sales_payment_type', $data);
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful add new sales payment type.');
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed add sales payment type, try again.');
            } else {
                $this->db->trans_commit();

                if (isset($_POST['save_close'])) {
                    $result['link'] = base_url('sales/setup/payment_type.tpd');
                } else {
                    $result['link'] = base_url('sales/setup/payment_form/' . $sp_type_id . '.tpd');
                }
            }
        }
        echo json_encode($result);
    }

    public function payment_ajax_list()
    {
        $where['pt.status !='] = 8;
        $like = array();

        if (isset($_REQUEST['filter_name'])) {
            if ($_REQUEST['filter_name'] != '') {
                $like['pt.sp_type_name'] = $_REQUEST['filter_name'];
            }
        }
        if (isset($_REQUEST['filter_coa'])) {
            if ($_REQUEST['filter_coa'] != '') {
                $like['c.coa_code'] = $_REQUEST['filter_coa'];
            }
        }
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['pt.status'] = $_REQUEST['filter_status'];
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
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $coa = $row->coa_code .'|'. $row->coa_desc;
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('sales/setup/payment_form/' . $row->sp_type_id) . '.tpd"> Edit </a> </li>';
            $status = get_status_active($row->status);
            $records["data"][] = array(
                $i . '.',
                $row->sp_type_name,
                $coa,
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


    public function package()
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
        $this->load->view('sales/setup/package');
        $this->load->view('layout/footer', $data_footer);
    }

    public function package_form($id)
    {
        $data_header = $this->data_header;
        $data_footer = $this->data_footer;
        $data = array();
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

        if ($id == 0) { //form
            $data['package_id'] = 0;
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/package_form', $data);
            $this->load->view('layout/footer', $data_footer);
        } else { //edit/view

            $data['package_id'] = $id;
            if ($id > 0) {
                $qry_head = $this->M_setup->get_package_head($id);
                $data['row'] = $qry_head->row();
                $data['row_detail'] = $this->M_setup->get_detail_package($id);
            }
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/package_form', $data);
            $this->load->view('layout/footer', $data_footer);
        }
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
                    <button class="btn green-meadow btn-xs select-item" type="button" item-code="' . $row->item_code . '" item-id="' . $row->item_id . '" description="' . $row->item_desc . '" uom="' . $row->uom_code . '" >
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

    public function ajax_package_submit()
    {
        $this->db->trans_begin();
        $result = array();
        $result['valid'] = '1';
        $result['message'] = '';
        $result['link'] = '';
        $result['debug'] = array();

        $data = array();


        if (isset($_POST)) {
            $package_id = $_POST['package_id'];
            if ($package_id > 0) { //update
                $data['package_group_code'] = $_POST['code'];
                $data['package_group_desc'] = $_POST['desc'];
                $data['price'] = $_POST['price'];
                $data['notes'] = $_POST['notes'];
                $data['user_modified'] = my_sess('user_id');
                $data['date_modified'] = date('Y-m-d H:i:s.000');
                $data['status'] = $_POST['status_header'];
                $this->M_setup->update('in_ms_package_header', array('package_group_id' => $package_id), $data);


                $this->db->delete('in_ms_package_detail', array('package_group_id' => $package_id)); //delete detail exist
                $detail = array();
                $item_id = $_POST['item_id'];
                $qty = $_POST['qty'];
                $status = $_POST['status'];
//                print_r($status);
//                exit();
                for ($a = 0; $a < count($item_id); $a++) {
                    $detail[$a] = array(
                        'package_group_id' => $package_id,
                        'item_id' => $item_id[$a],
                        'item_qty' => $qty[$a],
                        'user_created' => my_sess('user_id'),
                        'date_created' => date('Y-m-d H:i:s.000'),
                        'user_modified' => my_sess('user_id'),
                        'date_modified' => date('Y-m-d H:i:s.000'),
                        'status' => $status[$a],
                    );
                    $this->db->insert('in_ms_package_detail', $detail[$a]); //update detail
//                    echo $this->db->last_query();
                }
                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful edit package inventory');
            } else { //add new

                $data['package_group_code'] = $_POST['code'];
                $data['package_group_desc'] = $_POST['desc'];
                $data['price'] = $_POST['price'];
                $data['notes'] = $_POST['notes'];
                $data['user_created'] = my_sess('user_id');
                $data['date_created'] = date('Y-m-d H:i:s.000');
                $data['user_modified'] = my_sess('user_id');
                $data['date_modified'] = date('Y-m-d H:i:s.000');
                $data['status'] = STATUS_NEW;
                $this->db->insert('in_ms_package_header', $data);

                $get_id = $this->M_setup->get_package_header($_POST['code'], $_POST['desc']);


                $detail = array();
                $item_id = $_POST['item_id'];
                $qty = $_POST['qty'];
                for ($a = 0; $a < count($item_id); $a++) {
                    $detail[$a] = array(
                        'package_group_id' => $get_id->package_group_id,
                        'item_id' => $item_id[$a],
                        'item_qty' => $qty[$a],
                        'user_created' => my_sess('user_id'),
                        'date_created' => date('Y-m-d H:i:s.000'),
                        'user_modified' => my_sess('user_id'),
                        'date_modified' => date('Y-m-d H:i:s.000'),
                        'status' => STATUS_NEW,

                    );
                    $this->db->insert('in_ms_package_detail', $detail[$a]);
                }

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful add new package inventory');
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flash_message_class', 'danger');
                $this->session->set_flashdata('flash_message', 'Failed add package, try again.');
            } else {
                $this->db->trans_commit();

                if (isset($_POST['save_close'])) {
                    $result['link'] = base_url('sales/setup/package.tpd');
                } else {
                    $result['link'] = base_url('sales/setup/package_form/' . $package_id . '.tpd');
                }
            }
        }
        echo json_encode($result);
    }

    public function package_ajax_list()
    {
        $where[' status !='] = 100;
        $like = array();

        if (isset($_REQUEST['filter_code'])) {
            if ($_REQUEST['filter_code'] != '') {
                $like['package_group_code'] = $_REQUEST['filter_code'];
            }
        }
        if (isset($_REQUEST['filter_desc'])) {
            if ($_REQUEST['filter_desc'] != '') {
                $like['package_group_desc'] = $_REQUEST['filter_desc'];
            }
        }
        if (isset($_REQUEST['filter_notes'])) {
            if ($_REQUEST['filter_notes'] != '') {
                $like['notes'] = $_REQUEST['filter_notes'];
            }
        }
        if (isset($_REQUEST['filter_price'])) {
            if ($_REQUEST['filter_price'] != '') {
                $like['price'] = $_REQUEST['filter_price'];
            }
        }
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['status'] = $_REQUEST['filter_status'];
            }
        }
        $iTotalRecords = $this->M_setup->get_package_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'package_group_id desc ';

        $qry = $this->M_setup->get_package_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
        $i = $iDisplayStart + 1;

        foreach ($qry->result() as $row) {
            $btn_action = '';
            $btn_action .= '<li> <a href="javascript:;" class="view" package-id="' . $row->package_group_id . '" package-desc="' . $row->package_group_desc . '"> View </a> </li>';
            $btn_action .= '<li> <a href="' . base_url('sales/setup/package_form/' . $row->package_group_id) . '.tpd"> Edit </a> </li>';
            $status = get_status_active($row->status);
            $records["data"][] = array(
                $i . '.',
                $row->package_group_code,
                $row->package_group_desc,
                $row->notes,
                number_format($row->price),
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

    public function detail_package()
    {
        $id = $_POST['id'];
        $query = $this->M_setup->get_detail_package($id);
        $result = '';
        foreach ($query->result() as $row) {
            $status = get_status_active($row->status);
            $result .= '<tr>
                        <td align="center">' . $row->item_code . '</td>
                        <td>' . $row->item_desc . '</td>
                        <td align="right">' . $row->item_qty . '</td>
                        <td align="center">' . $row->uom_code . '</td>
                        <td align="center">' . $status . '</td>
                    </tr>';
        }
        echo $result;
    }

    public function approval($type = 0, $so_approval_id = 0)
    {
        $data_header = $this->data_header;
        $data_footer = $this->data_footer;
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        if ($type == 0) { //table so approval
            $data['approval_id'] = 0;
            $data['approval'] = $this->M_setup->get_approval();
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/approval', $data);
            $this->load->view('layout/footer', $data_footer);
        } else { //form
            $data['approval_id'] = $so_approval_id;
            $qry_head = $this->M_setup->get_approval_by_id($so_approval_id);
            $data['row'] = $qry_head;

            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/approval_form', $data);
            $this->load->view('layout/footer', $data_footer);
        }
    }

    public function approval_submit()
    {
        if (isset($_POST)) {
            $has_error = false;
            $approval_id = $_POST['approval_id'];

            $data['user_id'] = trim($_POST['user_id']);
            $data['level'] = trim($_POST['level']);
            $data['status'] = $_POST['status'];
            $data['document_type'] = $_POST['type'];
            $data['min_amount'] = str_replace(',', '', trim($_POST['min_amount']));
            $data['max_amount'] = str_replace(',', '', trim($_POST['max_amount']));


            if ($approval_id > 0) {
                $this->M_setup->update('sales_approval', array('sales_approval_id' => $approval_id), $data);

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful update SO Approval.');
            } else {
                $this->db->insert('sales_approval', $data);
                $this->db->insert_id();

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful add SO Approval.');
            }

            if ($has_error) {
                redirect(base_url('sales/setup/approval/0/.tpd'));
            } else {
                if (isset($_POST['save_close'])) {
                    redirect(base_url('sales/setup/approval/0.tpd'));
                } else {
                    redirect(base_url('sales/setup/approval/1/' . $approval_id . '.tpd'));
                }
            }
        }
    }

    public function delivery($type = 0, $delivery_id = 0)
    {
        $data_header = $this->data_header;
        $data_footer = $this->data_footer;
        $data = array();
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


        if ($type == 0) {//table
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/delivery');
            $this->load->view('layout/footer', $data_footer);
        } else {//form
            $data['delivery_id'] = $delivery_id;
            if ($delivery_id > 0) {
                $qry_head = $this->M_setup->get_delivery($delivery_id);
                $data['row'] = $qry_head->row();
            }
            $this->load->view('layout/header', $data_header);
            $this->load->view('sales/setup/delivery_form', $data);
            $this->load->view('layout/footer', $data_footer);
        }
    }

    public function delivery_submit()
    {
        if (isset($_POST)) {
            $has_error = false;
            $delivery_id = $_POST['delivery_id'];
            $data['delivery_type_name'] = $_POST['name'];
            $data['delivery_type_desc'] = $_POST['desc'];
            $data['status'] = $_POST['status'];

            if ($delivery_id > 0) { //update
                $this->M_setup->update('ms_delivery_type', array('delivery_type_id' => $delivery_id), $data);

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful update delivery.');
            } else { //insert
                $this->db->insert('ms_delivery_type', $data);
                $this->db->insert_id();

                $this->session->set_flashdata('flash_message_class', 'success');
                $this->session->set_flashdata('flash_message', 'Successful add delivery.');
            }

            if ($has_error) {
                redirect(base_url('sales/setup/delivery/0/.tpd'));
            } else {
                if (isset($_POST['save_close'])) {
                    redirect(base_url('sales/setup/delivery/0.tpd'));
                } else {
                    redirect(base_url('sales/setup/delivery/1/' . $delivery_id . '.tpd'));
                }
            }
        }
    }

    public function delivery_ajax_list()
    {
        $where[' status !='] = STATUS_DELETE;
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
        if (isset($_REQUEST['filter_status'])) {
            if ($_REQUEST['filter_status'] != '') {
                $where['status'] = $_REQUEST['filter_status'];
            }
        }

        $iTotalRecords = $this->M_setup->get_delivery_list(true, $where, $like);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $order = 'delivery_type_id desc ';

        $qry = $this->M_setup->get_delivery_list(false, $where, $like, $order, $iDisplayLength, $iDisplayStart);
//        echo $this->db->last_query();
        $i = $iDisplayStart + 1;
        foreach ($qry->result() as $row) {
            $btn_action = '';
            $btn_action .= '<li> <a href="' . base_url('sales/setup/delivery/1/' . $row->delivery_type_id) . '.tpd"> Edit </a> </li>';
            $status = get_status_active($row->status);
            $records["data"][] = array(
                $i . '.',
                $row->delivery_type_name,
                $row->delivery_type_desc,
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


}