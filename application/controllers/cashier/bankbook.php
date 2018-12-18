<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bankbook extends CI_Controller {
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
        $this->bankbook_report();
    }

    #region Find Bank Book

    public function bankbook_report(){
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
        $this->load->view('finance/cashbook/bankbook_find.php', $data);
        $this->load->view('layout/footer');
    }

    public function pdf_bankbook($dateFrom = '', $dateTo = '', $bankaccountId = '', $status = '') {
        if((trim($dateFrom) != '' && trim($dateTo) != '') && trim($bankaccountId) != '' || trim($status) != ''){
            $data['header'] = array('company_name'=>'TPD',
                'date_from' => $dateFrom,
                'date_to' => $dateTo);

            $dateFrom = dmy_to_ymd(trim($dateFrom));
            $dateTo = dmy_to_ymd(trim($dateTo));

            $data['qry_det'] = $this->bind_bankbook($dateFrom,$dateTo, urldecode($bankaccountId) , urldecode($status));

            $this->load->view('finance/cashbook/pdf_bankbook.php', $data);

            //$html = $this->output->get_output();
/*
            $this->load->library('dompdf_gen');

            $this->dompdf->set_paper("A4", "landscape");
            $this->dompdf->load_html($html);
            $this->dompdf->render();

            $this->dompdf->stream('bankbook_' . date('Y_m_d_H_i_s') . ".pdf", array('Attachment'=>0));
*/
            wkhtml_print(array('orientation' => 'landscape'));
        }else{
            tpd_404();
        }
    }

    private function bind_bankbook($dateFrom = '', $dateTo = '', $bankAccountId = '', $status = ''){
        $result = array();

        if(($dateFrom != '' && $dateTo != '') && $bankAccountId != '' && $status != ''){
            $this->load->model('finance/mdl_finance');

            $where = array();
            if($dateFrom != ''&& $dateTo != ''){
                $where = array('view_bankbook.docdate >=' => $dateFrom,
                    'view_bankbook.docdate <=' => $dateTo);
            }

            $likes = array();

            if(trim($bankAccountId) != ''){
                $where['view_bankbook.bankaccountid'] = $bankAccountId;
            }

            if(trim($status) != ''){
                if($status > 0){
                    $where['view_bankbook.isposted > '] = 0;
                }else{
                    $where['view_bankbook.isposted <= '] = 0;
                }
            }

            ///GET CURRENT TRANSACTIONS
            $jointable = array();
            $order = 'view_bankbook.coacode asc, view_bankbook.docdate asc, view_bankbook.docno asc';

            $qry = $this->mdl_finance->getJoin('view_bankbook.*',
                'view_bankbook', $jointable, $where, $likes, $order);

            //echo '<br>A1 ' . $this->db->last_query();

            if($qry->num_rows() > 0){
                $balance = 0;

                //GET PREVIOUS BALANCE
                $strqry = "SELECT ISNULL(SUM(debit),0) - ISNULL(SUM(credit),0) as balance  FROM view_bankbook WHERE bankaccountid = " .
                           $bankAccountId . " AND Convert(date,docdate) < '" . $dateFrom . "' AND isposted " . ($status > 0 ? " > 0 " : " <= 0 ");

                $qryPrev = $this->db->query($strqry);

                //echo '<br>B2 ' . $this->db->last_query();

                if($qryPrev->num_rows() > 0){
                    $res = $qryPrev->row();

                    $firstRow = $qry->row();

                    //Content Row
                    $newrow = array();

                    $newrow['bank_account_no'] = $firstRow->bankaccountno;
                    $newrow['bank_code'] = $firstRow->bankcode;
                    $newrow['currency'] = $firstRow->currency;
                    $newrow['coa_code'] = $firstRow->coacode;
                    $newrow['subject'] = 'PREVIOUS BALANCE';
                    $newrow['doc_no'] = '';
                    $newrow['doc_date'] = ymd_to_dmy($dateFrom);
                    $newrow['reff_no'] = '';
                    $newrow['description'] = '';
                    $newrow['debit'] = 0;
                    $newrow['credit'] = 0;

                    $balance = $res->balance;
                    $newrow['balance'] = $balance;
                    $newrow['status_caption'] = '';

                    array_push($result, $newrow);
                }

                //Content Row
                foreach($qry->result() as $res){
                    $newrow = array();

                    $newrow['bank_account_no'] = $res->bankaccountno;
                    $newrow['bank_code'] = $res->bankcode;
                    $newrow['currency'] = $res->currency;
                    $newrow['coa_code'] = $res->coacode;
                    $newrow['subject'] = $res->subject;
                    $newrow['doc_no'] = $res->docno;
                    $newrow['doc_date'] = dmy_from_db($res->docdate);
                    $newrow['reff_no'] = $res->reffno;
                    $newrow['description'] = $res->description;
                    $newrow['debit'] = $res->debit;
                    $newrow['credit'] = $res->credit;

                    $balance += ($res->debit - $res->credit);
                    $newrow['balance'] = $balance;
                    $newrow['status_caption'] = ($status > 0) ? "Posted" : "Unposted";

                    array_push($result, $newrow);

                }
            }else{
                //echo '<br>[bind_postedjournal] NULL ' ;
            }
        }

        return $result;
    }

    #endregion
}