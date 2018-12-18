<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SRF</title>

    <link href="<?php echo base_url(); ?>assets/css/pdf_frontend.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<script type = "text/php">
    if ( isset($pdf) ) {
        $pdf->page_script('
            if ($PAGE_COUNT > 1) {
                $font = Font_Metrics::get_font("Arial, Helvetica, sans-serif", "normal");
                $size = 7;
                $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $y = $pdf->get_height() - 24;
                $x = $pdf->get_width() - 35 - Font_Metrics::get_text_width($pageText, $font, $size);
                $pdf->text($x, $y, $pageText, $font, $size);
            }
        ');
    }
</script>

<div class="logo">
    <img src="<?php echo base_url('assets/img/logo_dwijaya.png'); ?>" alt="" width="60px">
</div>
<table width="100%" id="report_header" >
    <tr><td>&nbsp;</td></tr>
    <tr>
        <td style="vertical-align:top; text-align:center;"><h1><strong>SERVICE REQUEST FORM</strong></h1></td>
    </tr>
</table>

<div id="document_normal" style="padding-top: 50px;">
    <table style="width:93%;">
        <tr>
            <td style="width: 15px;">No</td>
            <td style="width: 1px;">:</td>
            <td style="width: 140px;"><?php echo $row->srf_no; ?></td>
        </tr>
        <tr>
            <td >Date</td>
            <td >:</td>
            <td ><?php echo dmy_from_db($row->srf_date); ?></td>
        </tr>
        <tr>
            <td >Requested By</td>
            <td >:</td>
            <td ><?php echo $row->requested_by; ?></td>
        </tr>
        <tr>
            <td >Room</td>
            <td >:</td>
            <td ><?php echo $row->unit_code; ?></td>
        </tr>
        <tr>
            <td >Type</td>
            <td >:</td>
            <td >
                <?php

                if($row->srf_type == SRF_TYPE::OUT_OF_ORDER){
                    echo '(OO) OUT OF ORDER';
                }else if($row->srf_type == SRF_TYPE::OUT_OF_SERVICE){
                    echo '(OS) OUT OF SERVICE';
                }

                ?>
            </td>
        </tr>
        <tr>
            <td >Work Date</td>
            <td >:</td>
            <td >
                <?php
                $work_date = array();
                $detail = $this->db->get_where('cs_srf_detail', array('srf_id'=>$row->srf_id));
                if($detail->num_rows() > 0){
                    foreach($detail->result() as $detail){
                        array_push($work_date, dmy_from_db($detail->work_date));
                    }
                }

                if(count($work_date) > 0){
                    echo implode(', ', $work_date);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td >Description</td>
            <td >:</td>
            <td ><?php echo nl2br($row->srf_note); ?></td>
        </tr>
        <tr>
            <td colspan="3"><i><?php echo $row->is_booking_available > 0 ? 'Room can still be booked' : '<strong>Room can not be booked for selected working date</strong>'?></i></td>
        </tr>
    </table>
</div>
<div>
    <table style="margin-top: 80px;">
        <tr >
            <td width="30%" class="v_top text-center padding-6">
                Prepared By,
            </td>
            <td width="40%"></td>
            <td width="30%" class="v_top text-center padding-6" >
                Approved By,
            </td>
        </tr>
        <tr>
            <td colspan="3" style="line-height: 80px;">&nbsp;</td>
        </tr>
        <tr>
            <td width="30%" class="v_top text-center padding-6" style="border-top: 1px solid #000000; ">
                <?php echo (my_sess('user_fullname'));?>
            </td>
            <td width="40%"></td>
            <td class="v_top text-center padding-6" style="border-top: 1px solid #000000; ">

            </td>
        </tr>
        <tr>
            <td width="30%" class="v_top text-center padding-6" >
            </td>
            <td></td>
            <td class="v_top text-center padding-6" >

            </td>
        </tr>
    </table>
</div>
<div id="page_footer"><p><?php echo nl2br($profile['company_address']); ?></p></div></body></html>