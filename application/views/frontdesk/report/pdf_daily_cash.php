<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Daily Cash Report</title>

    <link href="<?php echo FCPATH; ?>/assets/css/pdf_frontend.css" rel="stylesheet" type="text/css"/>
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
    <img src="<?php echo FCPATH; ?>assets/img/logo_dwijaya.png" alt="" width="60px">
</div>
<table width="100%" id="report_header" >
    <tr>
        <td style="vertical-align:top; text-align:center;"><h1><strong>Daily Cash Report</strong></h1></td>
    </tr>
    <tr>
        <td style="vertical-align:top; text-align:center;"><h2><?php echo ($date_from . ' to ' . $date_until); ?></h2></td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:10px;" >
            <div>
                <table style="width:100%;" class="table_detail">
                    <thead>
                    <tr>
                        <th style="width:30px;">DOC NO</th>
                        <th style="width:40px;">DATE</th>
                        <th style="width:120px;" class="text-left">SUBJECT</th>
                        <th style="width:30px;" class="text-center">FOLIO NO</th>
                        <th style="width:50px;" class="text-center">ROOM</th>
                        <th style="width:70px;" class="text-right">AMOUNT</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $max = 30;
                    $total_line = 0;

                    $total_amount = 0;
                    if(isset($detail)){
                        $i = 1;
                        foreach($detail as $data){
                            $subject = $data['tenant_fullname'];
                            if($data['reservation_type'] == RES_TYPE::CORPORATE){
                                if($data['is_primary_debtor'] > 0){
                                    $subject = $data['company_name'];
                                }
                            }

                            $amount = ($data['receipt_amount'] + $data['receipt_bank_fee'] + $data['receipt_veritrans_fee']);

                            echo '<tr>
                                        <td class="text-center" style="width:30px;">' . $data['receipt_no'] . '</td>
                                        <td class="text-center" style="width:40px;">' . dmy_from_db($data['bookingreceipt_date']) .'</td>
                                        <td style="width:120px;">' . $subject .'</td>
                                        <td class="text-center" style="width:30px;">' . $data['reservation_code']  .'</td>
                                        <td class="text-center" style="width:50px;">' . $data['room'] .'</td>
                                        <td class="text-right" style="width:70px;">' . format_num($amount,0) .'</td>
                                  </tr>';

                            $i++;

                            $row_desc = wordwrap_string($subject, 90);
                            $total_line = $total_line + $row_desc['line'];

                            $total_amount += $amount;
                        }

                        if ($max > $total_line){
                            for($x = $total_line;$x <= $max;$x++){
                                //echo '<tr><td colspan="6">&nbsp;</td></tr>';
                            }
                        }
                    }

                    ?>
                    </tbody>
                    <tfoot>
                        <tr class="total">
                            <td class="border-top text-right" colspan="5">TOTAL (IDR)</td>
                            <td class="border-top text-right"><?php echo format_num($total_amount,0) ?></td>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>

</div>
<div>
    <table style="margin-top: 30px;">
        <tr ><td style="height: 50px;"></td></tr>
        <tr>
            <td width="65%" class="v_top text-center padding-6">
            </td>
            <td class="v_top text-center padding-6" style="border-bottom: 1px solid #000000; ">

            </td>
        </tr>
        <tr>
            <td width="65%" class="v_top text-center padding-6">
            </td>
            <td class="v_top text-center padding-6">
                <?php echo (my_sess('user_fullname'));?>
            </td>
        </tr>
    </table>
</div>
<div id="page_footer"><p><?php echo nl2br($profile['company_address']); ?></p></div></body></html>