<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ledger extends CI_Controller {
    var $params;
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

        $this->params = array();
    }

    public function index()
    {
        $this->ledger_find();

    }

    #region Find Ledger

    public function ledger_find(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/journal/ledger_find.php', $data);
        $this->load->view('layout/footer');
    }

    public function all_coa_tr(){
        $this->load->model('finance/mdl_finance');

        $where['gl_coa.status <>'] = STATUS_DELETE;
        $where['gl_coa.is_display >'] = 0;

        $qry = $this->mdl_finance->getCOA($where, array());

        $data = '';

        $i = 0;
        foreach($qry->result() as $row){
            $data .= '<tr>'.
                     '<td class="text-center" style="vertical-align:middle;"><input type="hidden" name="detail_id[' . $i . ']" value="0"><input type="hidden" name="coa_id[' . $i . ']" value="' . $row->coa_code . '">' . $row->coa_code . '</td>' .
                     '<td style="vertical-align:middle;"><span class="control-label">' . $row->coa_desc . '</span></td>' .
                     '<td class="text-center" style="vertical-align:middle;"><a class="btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" onclick="delete_frontend(' . $i . ');" ><i class="fa fa-times"></i></a></td>' .
                     '</tr>';
            $i++;
        }

        echo json_encode($data);
    }

    public function coa_tr_by_range(){
        $this->load->model('finance/mdl_finance');

        $rangeStart = isset($_POST['filter_range_from']) ? $_POST['filter_range_from'] : 0 ;
        $rangeEnd = isset($_POST['filter_range_to']) ? $_POST['filter_range_to'] : 0 ;

        $where['gl_coa.status <>'] = STATUS_DELETE;
        $where['gl_coa.is_display >'] = 0;
        $where['gl_coa.coa_code >='] = $rangeStart;
        $where['gl_coa.coa_code <='] = $rangeEnd;

        $qry = $this->mdl_finance->getCOA($where, array());

        $data = '';

        $i = 0;
        foreach($qry->result() as $row){
            $data .= '<tr>'.
                '<td class="text-center" style="vertical-align:middle;"><input type="hidden" name="detail_id[' . $i . ']" value="0"><input type="hidden" name="coa_id[' . $i . ']" value="' . $row->coa_code . '">' . $row->coa_code . '</td>' .
                '<td style="vertical-align:middle;"><span class="control-label">' . $row->coa_desc . '</span></td>' .
                '<td class="text-center" style="vertical-align:middle;"><a class="btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" onclick="delete_frontend(' . $i . ');" ><i class="fa fa-times"></i></a></td>' .
                '</tr>';
            $i++;
        }

        echo json_encode($data);
    }

    private function bind_generalledger($dateFrom, $dateTo, $coacode = array()){
        $result = array();

        if($dateFrom != '' && $dateTo != '' && count($coacode) > 0){
            $coacomma = implode(',', $coacode);

            if(strlen($coacomma) > 4){
                //OBTAIN
                $prevs = $this->db->query("SELECT * FROM fxnGL_PrevBalance('" . $dateFrom ."','" . $coacomma . "')");

                $dictBalance = array();
                if($prevs->num_rows() > 0){
                    foreach($prevs->result_array() as $prev){
                        $dictBalance[$prev['coa_code']] = $prev['balance'];
                    }
                }

                $qry = "SELECT     det.*, coa.coa_code, coa.coa_desc, coa.is_debit, head.journal_date, head.journal_no, head.filing_no
                        FROM gl_postjournal_detail det
                        LEFT JOIN gl_coa coa ON coa.coa_id = det.coa_id
                        LEFT JOIN gl_postjournal_header head ON head.postheader_id = det.postheader_id
                        WHERE coa.coa_code IN(" . $coacomma .") AND CONVERT(date,head.journal_date) >= '" . $dateFrom . "'
                        AND CONVERT(date,head.journal_date) <= '" . $dateTo . "'
                        ORDER BY coa.coa_code, head.journal_date, head.created_date ";

                $journals = $this->db->query($qry);

                $currentCOACode = '';
                foreach($journals->result_array() as $j){
                    if($currentCOACode != $j['coa_code']){
                        //PREVIOUS
                        $prev = array();
                        $prev['coa_code'] = $j['coa_code'];
                        $prev['coa_desc'] = $j['coa_desc'];
                        $prev['is_debit'] = ($j['is_debit'] > 0) ? true : false;
                        $prev['journal_no'] = '';
                        $prev['filing_no'] = '';
                        $prev['journal_date'] = date('d-m-y',strtotime($j['journal_date']));
                        $prev['journal_note'] = 'Previous Balance';
                        $prev['journal_debit'] = 0;
                        $prev['journal_credit'] = 0;

                        $balance = isset($dictBalance[$j['coa_code']]) ? $dictBalance[$j['coa_code']] : 0;

                        $prev['balance'] = $balance;

                        array_push($result, $prev);

                        $currentCOACode = $j['coa_code'];
                    }

                    //CURRENT
                    $newrow = array();
                    $newrow['coa_code'] = $j['coa_code'];
                    $newrow['coa_desc'] = $j['coa_desc'];
                    $newrow['is_debit'] = ($j['is_debit'] > 0) ? true : false;
                    $newrow['journal_no'] = $j['journal_no'];
                    $newrow['filing_no'] = $j['filing_no'];
                    $newrow['journal_date'] = date('d-m-y',strtotime($j['journal_date']));
                    $newrow['journal_note'] = (trim($j['journal_note']) == '') ? '-' : $j['journal_note'];
                    $newrow['journal_debit'] = $j['journal_debit'];
                    $newrow['journal_credit'] = $j['journal_credit'];

                    //Get Ordered of Inserted Rows
                    $prevBalance = 0;
                    foreach($result as $row){
                        if($row['coa_code'] == $j['coa_code']){
                            $prevBalance = $row['balance'];
                        }
                    }

                    if($newrow['is_debit']){
                        $balance = $prevBalance + ($newrow['journal_debit'] - $newrow['journal_credit']);
                    }else{
                        $balance = $prevBalance + ($newrow['journal_credit'] - $newrow['journal_debit']);
                    }

                    $newrow['balance'] = $balance;

                    array_push($result, $newrow);
                }
            }
        }

        return $result;
    }

    private function bind_generalledger_v1($dateFrom, $dateTo, $coa = array()){
        $result = array();

        if($dateFrom != '' && $dateTo != '' && count($coa) > 0){
            foreach($coa as $row) {
                if(trim($row) != ''){
                    $balance = 0;
                    //echo '<br>[bind_generalledger] A ' . $row . ' from ' . $dateFrom . ' - ' . $dateTo;

                    $jointable = array('gl_postjournal_header' => 'gl_postjournal_header.postheader_id = gl_postjournal_detail.postheader_id',
                        'gl_coa' => 'gl_coa.coa_id = gl_postjournal_detail.coa_id');
                    //$where['UPPER(gl_coa.coa_code)'] = $row;
                    $where = array('gl_coa.coa_id' => $row,
                        'gl_postjournal_header.journal_date >=' => $dateFrom,
                        'gl_postjournal_header.journal_date <=' => $dateTo);

                    $order = 'gl_postjournal_header.journal_date asc, gl_postjournal_header.created_date asc';

                    $qry = $this->mdl_finance->getJoin('gl_postjournal_detail.*, gl_coa.coa_code, gl_coa.coa_desc, gl_coa.is_debit, gl_postjournal_header.journal_date, gl_postjournal_header.journal_no, gl_postjournal_header.filing_no',
                        'gl_postjournal_detail', $jointable, $where, array(), $order);

                    //echo '<br>A2 ' . $this->db->last_query();

                    if($qry->num_rows() > 0){
                        $rowprev = $qry->row();

                        //Previous Balance
                        $prev = array();

                        $prev['coa_code'] = $rowprev->coa_code;
                        $prev['coa_desc'] = $rowprev->coa_desc;
                        $prev['is_debit'] = ($rowprev->is_debit > 0) ? true : false;
                        $prev['journal_no'] = '';
                        $prev['filing_no'] = '';
                        $prev['journal_date'] = date('d-m-y',strtotime($rowprev->journal_date));
                        $prev['journal_note'] = 'Previous Balance';
                        $prev['journal_debit'] = 0;
                        $prev['journal_credit'] = 0;

                        $where = array('gl_coa.coa_id' => $row,
                            'gl_postjournal_header.journal_date <' => $dateFrom);

                        $qryPrev = $this->mdl_finance->getJoin('IsNull(SUM(gl_postjournal_detail.Journal_Debit),0) As debit, IsNull(SUM(gl_postjournal_detail.Journal_Credit),0) As credit',
                            'gl_postjournal_detail', $jointable, $where, array());

                        //Get Prev Balance
                        if($qryPrev->num_rows() > 0){
                            $prevblc = $qryPrev->row();
                            if($prev['is_debit']){
                                $balance = $prevblc->debit - $prevblc->credit;
                            }else{
                                $balance = $prevblc->credit - $prevblc->debit;
                            }
                        }else{
                            $balance = 0;
                        }

                        $prev['balance'] = $balance;

                        array_push($result, $prev);

                        //echo '<br>[bind_generalledger] D ' . $prev['coa_code'] . ' - ' . $prev['balance'];

                        //Content Row
                        foreach($qry->result() as $res){
                            $newrow = array();

                            $newrow['coa_code'] = $res->coa_code;
                            $newrow['coa_desc'] = $res->coa_desc;
                            $newrow['is_debit'] = ($res->is_debit > 0) ? true : false;
                            $newrow['journal_no'] = $res->journal_no;
                            $newrow['filing_no'] = $res->filing_no;
                            $newrow['journal_date'] = date('d-m-y',strtotime($res->journal_date));
                            $newrow['journal_note'] = (trim($res->journal_note) == '') ? '-' : $res->journal_note;
                            $newrow['journal_debit'] = $res->journal_debit;
                            $newrow['journal_credit'] = $res->journal_credit;

                            if($newrow['is_debit']){
                                $balance += ($newrow['journal_debit'] - $newrow['journal_credit']);
                            }else{
                                $balance += ($newrow['journal_credit'] - $newrow['journal_debit']);
                            }

                            $newrow['balance'] = $balance;

                            array_push($result, $newrow);

                        }
                    }else{
                        //echo '<br>[bind_generalledger] NULL ' ;
                    }
                }
            }
        }

        return $result;
    }

    public function pdf_ledger() {
        $dateFrom = '';
        $dateTo = '';
        $coacodes = '';
        $ispdf = false;

        if(!isset($_POST['coa_code_list'])){
            if(count($this->session->userdata('pdf_ledger')) > 0){
                $param = $this->session->userdata('pdf_ledger');
                $dateFrom = $param['date_start'];
                $dateTo = $param['date_to'];
                $coacodes = $param['coa_code_list'];
                $ispdf = $param['is_pdf'];
            }
        }else{
            $dateFrom = isset($_POST['date_start']) ? $_POST['date_start'] : date('d-m-Y');
            $dateTo = isset($_POST['date_to']) ? $_POST['date_to'] : date('d-m-Y');
            $coacodes = isset($_POST['coa_code_list']) ? $_POST['coa_code_list'] : '';
            $ispdf = isset($_POST['is_pdf']) ? true : false;
        }

        if(trim($dateFrom) != '' && trim($dateTo) != '' && trim($coacodes) != ''){
            $this->session->set_userdata('pdf_ledger', array('date_start' => $dateFrom, 'date_to' => $dateTo, 'coa_code_list' => $coacodes, 'is_pdf' => $ispdf));

            $data['header'] = array('company_name'=>'TPD',
                'date_from' => $dateFrom,
                'date_to' => $dateTo);

            $data['date_start'] = $dateFrom;
            $data['date_to'] = $dateTo;
            $data['coa_code_list'] = $coacodes;
            $data['is_pdf'] = $ispdf;

            $dateFrom = dmy_to_ymd(trim($dateFrom));
            $dateTo = dmy_to_ymd(trim($dateTo));

            if (substr($coacodes, -1) == '-'){
                //Remove last char
                $coacodes = substr($coacodes,0,-1);
            }

            $coa_comma = explode("-", trim($coacodes));

            $this->load->model('finance/mdl_finance');

            $data['qry_det'] = $this->bind_generalledger($dateFrom,$dateTo,$coa_comma);

            if($ispdf){
                //$this->load->view('finance/journal/pdf_ledger_print.php', $data);
                //Using FCPATH
                $this->load->view('finance/journal/pdf_ledger2.php', $data);
            }else{
                $this->load->view('finance/journal/pdf_ledger.php', $data);
            }

            if($data['is_pdf']){
                //$html = $this->output->get_output();

                wkhtml_print(array('orientation' => 'portrait','page-size' => 'A4'));

                /*
                $this->load->library('dompdf_gen');

                $this->dompdf->set_paper("A4", "portrait");
                $this->dompdf->load_html($html);
                $this->dompdf->render();

                $this->dompdf->stream('gl_' . date('Y_m_d_H_i_s') . ".pdf", array('Attachment'=>0));
                */
            }

        }else{
            //tpd_404();
            $this->ledger_find();
        }
    }

    #endregion

    #region Find Posted Journal

    public function postedjournal_find(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/journal/journal_find.php', $data);
        $this->load->view('layout/footer');
    }

    public function pdf_postedjournal($dateFrom = '', $dateTo = '', $journalno = '', $module = '', $ispdf = 0) {
        if((trim($dateFrom) != '-' && trim($dateTo) != '-') || trim($journalno) != '-' || trim($module) != ''){
            $data['header'] = array('company_name'=>'TPD',
                'date_from' => $dateFrom,
                'date_to' => $dateTo);

            if(trim($dateFrom) != '-' && trim($dateTo) != '-'){
                $dateFrom = dmy_to_ymd(trim($dateFrom));
                $dateTo = dmy_to_ymd(trim($dateTo));
            }else{
                $dateFrom = '';
                $dateTo = '';
            }

            $journalno = urldecode($journalno) != '-' ? urldecode($journalno) : '';

            $data['qry_det'] = $this->bind_postedjournal($dateFrom,$dateTo, $journalno , urldecode($module));
            $data['is_pdf'] = $ispdf > 0 ? true : false;

            $this->load->view('finance/journal/pdf_postedjournal.php', $data);

            /*
            $html = $this->output->get_output();

            $this->load->library('dompdf_gen');

            $this->dompdf->set_paper("A4", "portrait");
            $this->dompdf->load_html($html);
            $this->dompdf->render();

            $this->dompdf->stream('glposted_' . date('Y_m_d_H_i_s') . ".pdf", array('Attachment'=>0));
            */

        }else{
            tpd_404();
        }
    }

    private function bind_postedjournal($dateFrom = '', $dateTo = '', $journalNo = '', $module = ''){
        $result = array();

        if(($dateFrom != '' && $dateTo != '') || $journalNo != '' || $module != ''){
            $this->load->model('finance/mdl_finance');

            $jointable = array('gl_postjournal_header' => 'gl_postjournal_header.postheader_id = gl_postjournal_detail.postheader_id',
                'gl_coa' => 'gl_coa.coa_id = gl_postjournal_detail.coa_id');

            $where = array();
            if($dateFrom != ''&& $dateTo != ''){
                $where = array('CONVERT(date,gl_postjournal_header.journal_date) >=' => $dateFrom,
                    'CONVERT(date,gl_postjournal_header.journal_date) <=' => $dateTo);
            }

            $likes = array();
            if(trim($journalNo) != ''){
                $likes = array('gl_postjournal_header.journal_no' => $journalNo);
            }

            if(trim($module) != ''){
                $likes = array('gl_postjournal_header.modul' => $module);
            }

            $order = 'CONVERT(date,gl_postjournal_header.journal_date) asc, gl_postjournal_header.created_date asc, gl_postjournal_detail.postdetail_id';

            $qry = $this->mdl_finance->getJoin('gl_postjournal_detail.*, gl_coa.coa_code, gl_coa.coa_desc, gl_coa.is_debit, CONVERT(date,gl_postjournal_header.journal_date) as journal_date, gl_postjournal_header.journal_no, gl_postjournal_header.filing_no, gl_postjournal_header.modul, gl_postjournal_header.journal_remarks',
                'gl_postjournal_detail', $jointable, $where, $likes, $order);

            //echo '<br>A2 ' . $this->db->last_query();

            if($qry->num_rows() > 0){
                //Content Row
                foreach($qry->result() as $res){
                    $newrow = array();

                    $newrow['coa_code'] = $res->coa_code;
                    $newrow['coa_desc'] = $res->coa_desc;
                    $newrow['is_debit'] = ($res->is_debit > 0) ? true : false;
                    $newrow['journal_no'] = $res->journal_no;
                    $newrow['filing_no'] = $res->filing_no;
                    $newrow['journal_date'] = date('d-m-Y',strtotime($res->journal_date));
                    $newrow['journal_note'] = (trim($res->journal_note) == '') ? '-' : $res->journal_note;
                    $newrow['journal_debit'] = $res->journal_debit;
                    $newrow['journal_credit'] = $res->journal_credit;
                    $newrow['module'] = $res->modul;
                    $newrow['journal_remarks'] = $res->journal_remarks;

                    array_push($result, $newrow);

                }
            }else{
                //echo '<br>[bind_postedjournal] NULL ' ;
            }
        }

        return $result;
    }

    #endregion

    #region Edit Posted Journal

    public function postedjournal_edit(){
        $this->load->model('finance/mdl_finance');

        $data_header = $this->data_header;

        array_push($data_header['style'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/select2/select2.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-datepicker/css/datepicker3.css');

        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
        array_push($data_header['style'], base_url() . 'assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/select2/select2.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/media/js/jquery.dataTables.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootbox/bootbox.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-toastr/toastr.min.js');

        array_push($data_header['custom_script'], base_url() . 'assets/global/scripts/datatable.js');

        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/jquery.validate.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-validation/js/additional-methods.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/bootstrap-modal/js/bootstrap-modal.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js');
        array_push($data_header['script'], base_url() . 'assets/global/plugins/autosize.js');

        $data = array();

        $this->load->view('layout/header', $data_header);
        $this->load->view('finance/journal/editposted_form', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_editposted_find(){
        $result = array();

        //Used to display notification
        $result['type'] = 0;
        $result['message'] = "";
        $result['debug'] = "";
        $result['journal'] = array();

        $journalNo = $_POST['journal_no'];

        if($journalNo != ''){
            $postHeader = $this->db->get_where('gl_postjournal_header',array('journal_no' => $journalNo));
            if($postHeader->num_rows() > 0){
                $postHeader = $postHeader->row();
                $journalDate = DateTime::createFromFormat('Y-m-d', ymd_from_db($postHeader->journal_date));
                $minInputDate = DateTime::createFromFormat('d-m-Y', min_input_date());
                if($journalDate >= $minInputDate){
                    $journal = array();
                    $journal['journal_date'] = $journalDate->format('d-m-Y');
                    $journal['journal_remark'] = $postHeader->journal_remarks;
                    $journal['journal_amount'] = $postHeader->journal_amount;

                    //DETAILS
                    $journal['details'] = '';
                    $postDetails = $this->db->query('SELECT d.*, coa.coa_desc, ISNULL(dept.department_name,\'\') as dept_name FROM gl_postjournal_detail as d
                                    JOIN gl_coa coa ON coa.coa_id = d.coa_id
                                    LEFT JOIN ms_department dept ON dept.department_id = d.dept_id WHERE postheader_id = ' . $postHeader->postheader_id);
                    if($postDetails->num_rows() > 0){
                        $i = 0;
                        foreach($postDetails->result() as $detail){
                            $isEdit = true;

                            $bankAcct = $this->db->query('SELECT bankaccount_id FROM fn_bank_account WHERE coa_id = ' . $detail->coa_id);
                            if($bankAcct->num_rows() > 0){
                                $isEdit = false;
                            }

                            $journal['details'] .= '<tr>
                                                        <input type="hidden" name="status_edit[' . $i . ']" value="' . 0 . '">
                                                        <input type="hidden" name="postdetail_id[' . $i . ']" value="' . $detail->postdetail_id . '">
                                                        <input type="hidden" name="index[' . $i . ']" value="' . $i . '">
                                                        <td style="vertical-align:top;">
                                                            <div class="input-group">
                                                                <input type="hidden" name="coa_id[' . $i . ']" value="' . $detail->coa_id . '">
                                                                <input type="text" name="coa_code[' . $i . ']" value="' . $detail->coa_code . '" class="form-control text-left input-sm" readonly>';

                            if($isEdit){
                                $journal['details'] .= '<span class="input-group-btn">
                                                                    <button data-index="0" type="button" style="padding-top:5px;margin-right:0px;" class="btn btn-sm green-haze change_coa" data-id = "' . $i . '"><i class="fa fa-arrow-up fa-fw"></i></button>
                                                                </span>';
                            }

                            $journal['details'] .= '</div>

                                                        </td>
                                                        <td>
                                                            <input type="text" name="coa_desc[' . $i . ']" value="' . $detail->coa_desc . '" class="form-control  input-sm" readonly>
                                                        </td>
                                                        <td>
                                                            <textarea name="journal_note[' . $i . ']" class="form-control  input-sm" rows="2" style="resize:vertical;" readonly >' . $detail->journal_note . '</textarea>
                                                        </td>
                                                        <td >
                                                            <input type="text" name="journal_debit[' . $i . ']" value="' . $detail->journal_debit . '" class="form-control text-right input-sm mask_currency num_cal" readonly>
                                                        </td>
                                                        <td >
                                                            <input type="text" name="journal_credit[' . $i . ']" value="' . $detail->journal_credit . '" class="form-control text-right input-sm mask_currency num_cal" readonly>
                                                        </td>
                                                        <td ><input type="text" name="dept_name[' . $i . ']" value="' . $detail->dept_name . '" class="form-control  input-sm" readonly>
                                                        </td>
                                                      </tr>';
                            $i++;
                        }
                    }

                    $result['type'] = 1;
                    $result['journal'] = $journal;
                }else{
                    $result['type'] = 0;
                    $result['message'] = "Edit closed transactions are not allowed !";
                }
            }else{
                $result['type'] = 0;
                $result['message'] = "Journal No not found !";
            }

        }else{
            $result['type'] = 0;
            $result['message'] = "Journal No must not empty.";
        }

        //echo $result;
        echo json_encode($result);
    }

    public function ajax_editposted_submit(){
        $result = array();

        //Used to display notification
        $result['valid'] = 0;
        $result['message'] = "";
        $result['redirect_link'] = base_url('finance/ledger/postedjournal_edit');
        $result['debug'] = "";

        $journalNo = $_POST['journal_no'];

        if($journalNo != ''){
            if(isset($_POST['postdetail_id'])) {
                $valid = true;

                //BEGIN TRANSACTION
                $this->db->trans_begin();

                foreach ($_POST['postdetail_id'] as $key => $val) {
                    if($valid){
                        $postdetail_id = floatval($_POST['postdetail_id'][$key]);
                        $coa_id = $_POST['coa_id'][$key];
                        $coa_code = $_POST['coa_code'][$key];

                        $status = $_POST['status_edit'][$key];
                        if ($status > 0) {
                            $prev_coacode = '';
                            $postDetail = $this->db->query('SELECT coa.coa_code FROM gl_postjournal_detail det
                                      JOIN gl_coa coa ON coa.coa_id = det.coa_id
                                      WHERE det.postdetail_id = ' . $postdetail_id);
                            if ($postDetail->num_rows() > 0) {
                                $postDetail = $postDetail->row();
                                $prev_coacode = $postDetail->coa_code;
                            }

                            $data_detail = array();

                            $qry = $this->db->get_where('gl_coa', array('coa_id' => $coa_id));
                            if ($qry->num_rows() > 0) {
                                $coa_code = $qry->row()->coa_code;
                            }

                            $data_detail['coa_id'] = $coa_id;
                            $data_detail['coa_code'] = $coa_code;

                            //UPDATE POSTDETAIL COA CODE
                            $this->mdl_general->update('gl_postjournal_detail', array('postdetail_id' => $postdetail_id), $data_detail);

                            //CREATE LOG
                            $log = array();
                            $log['created_by'] = my_sess('user_id');
                            $log['created_date'] = date('Y-m-d H:i:s');
                            $log['postdetail_id'] = $postdetail_id;
                            $log['journal_no'] = strtoupper($journalNo);
                            $log['current_coacode'] = $coa_code;
                            $log['prev_coacode'] = $prev_coacode;

                            $this->db->insert('gl_editjournallog', $log);
                            $log_id = $this->db->insert_id();
                            if($log_id <= 0){
                                $valid = false;
                                break;
                            }
                        }
                    }
                }

                //COMMIT OR ROLLBACK
                if($valid){
                    if ($this->db->trans_status() === FALSE)
                    {
                        $this->db->trans_rollback();

                        $result['valid'] = 0;
                        $result['message'] = "Transaction can not be saved. Please try again later !";
                    }
                    else
                    {
                        $this->db->trans_commit();

                        $result['valid'] = 1;
                        $result['message'] = $journalNo . " successfully modified.";

                        $this->session->set_flashdata('flash_message_class', 'success');
                        $this->session->set_flashdata('flash_message', $result['message']);
                    }
                }else{
                    $this->db->trans_rollback();

                    $result['valid'] = 0;
                    $result['message'] = "Transaction can not be saved. Please try again later !";
                }
            }else{
                $result['valid'] = 0;
                $result['message'] = "Journal No must not empty.";
            }
        }else{
            $result['valid'] = 0;
            $result['message'] = "Journal No must not empty.";
        }

        //echo $result;
        echo json_encode($result);
    }

    #endregion


}