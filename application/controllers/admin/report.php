<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {

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

    public function reporter($type = 0){
        $data_header = $this->data_header;

        $data = array();

        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');
        array_push($data_header['style'], base_url() . 'assets/css/report_group.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        switch ($type)
        {
            case 1:
                //Log Edit Posted Journal
                $this->load->view('layout/header_no_sidebar', $data_header);
                $this->load->view('admin/report/edit_posted', $data);
                $this->load->view('layout/footer');
                break;
            default:
                tpd_404();
                break;
        }
    }

    public function xeditposted(){
        $this->load->model('finance/mdl_finance');

        $where = array();
        $like = array();
        $where_string = "";

        if(isset($_REQUEST['filter_doc_no'])){
            if($_REQUEST['filter_doc_no'] != ''){
                $like['UPPER(log.journal_no)'] = $_REQUEST['filter_doc_no'];
            }
        }

        if(isset($_REQUEST['filter_edit_from'])){
            if($_REQUEST['filter_edit_from'] != ''){
                $where['CONVERT(date,log.created_date) >='] = dmy_to_ymd($_REQUEST['filter_edit_from']);
            }
        }

        if(isset($_REQUEST['filter_edit_to'])){
            if($_REQUEST['filter_edit_to'] != ''){
                $where['CONVERT(date,log.created_date) <='] = dmy_to_ymd($_REQUEST['filter_edit_to']);
            }
        }

        if(count($like) <= 0 && count($where) <= 0){
            $where['log.created_by'] = 0;
        }

        $joins = array('gl_postjournal_detail' => 'gl_postjournal_detail.postdetail_id = log.postdetail_id',
                       'gl_coa prev' => 'prev.coa_code = log.prev_coacode',
                       'gl_coa last' => 'last.coa_code = log.current_coacode',
                       'ms_user' => 'ms_user.user_id = log.created_by');
        $iTotalRecords = $this->mdl_finance->countJoin("gl_editjournallog as log", $joins, $where, $like);

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records["data"] = array();

        $order = 'log.created_date asc';
        if(isset($_REQUEST['order'])){
            if($_REQUEST['order'][0]['column'] == 1){
                $order = 'log.created_date ' . $_REQUEST['order'][0]['dir'];
            }
        }

        $qry = $this->mdl_finance->getJoin('log.*, gl_postjournal_detail.journal_note, prev.coa_desc as prev_desc, last.coa_desc as current_desc, ms_user.user_fullname',"gl_editjournallog as log ", $joins, $where, $like, $order, $iDisplayLength, $iDisplayStart);

        $records["debug"] = $this->db->last_query();

        $i = $iDisplayStart + 1;
        foreach($qry->result() as $row){
            $records["data"][] = array(
                '<span class="bold">' . strtoupper($row->journal_no) . '</span>',
                date('d/m/Y H:i:s',strtotime($row->created_date)),
                $row->user_fullname,
                nl2br($row->journal_note),
                '<span class="bold font-red-sunglo">'. $row->prev_coacode . '</span> - ' . $row->prev_desc,
                '<span class="bold font-blue-madison">'. $row->current_coacode . '</span> - ' . $row->current_desc,
                ''
            );

            $i++;
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }

}

/* End of file booking.php */
/* Location: ./application/controllers/frondesk/booking.php */