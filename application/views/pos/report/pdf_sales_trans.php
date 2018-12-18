<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reservation Sales Report</title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_inhouse.css" rel="stylesheet" type="text/css"/>
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
        <td style="vertical-align:top; text-align:center;"><h1><strong>Point Of Sales Transactions</strong></h1></td>
    </tr>
    <tr>
        <td style="vertical-align:top; text-align:center;"><h2>Per&nbsp;<?php echo date('d/m/Y', strtotime(ymd_from_db($date_from))); ?>&nbsp;to&nbsp;<?php echo date('d/m/Y', strtotime(ymd_from_db($date_to))); ?></h2></td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:10px;" >
            <div>
                <table style="width:100%;margin-right: 15px;" class="table_detail">
                    <thead>
                    <tr>
                        <th class="text-center" width="40px">NO </th>
                        <th class="text-center" width="55px">DATE </th>
                        <th class="text-left" width="230px">CUSTOMER </th>
                        <th class="text-left" >ITEM DESCRIPTION</th>
                        <th class="text-center" width="40px"> QTY </th>
                        <th class="text-center" width="80px"> PRICE </th>
                        <th class="text-center" width="50px"> TAX </th>
                        <th class="text-center" width="90px">SUBTOTAL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $max = 30;
                    $total_line = 0;

                    $g_amount = 0;
                    $g_tax = 0;
                    $g_subtotal = 0;
                    if(isset($detail)){
                        $i = 1;
                        foreach($detail as $data){
                            $_amount = round($data['rate'],0);
                            $_tax = round($data['tax'],0);
                            $_subtotal = round($data['amount'] + $data['tax'],0);

							 echo '<tr>
                                        <td class="text-center" >' . $data['journal_no'] .'</td>
                                        <td class="text-center" >' .dmy_from_db($data['bill_date']) .'</td>
                                        <td >' . $data['customer_name'] .'</td>
                                        <td >' . $data['item_desc'] .'</td>
                                        <td class="text-right" >' . format_num($data['item_qty'],0) .'</td>
                                        <td class="text-right" >' . format_num($_amount,0) .'</td>
                                        <td class="text-right" >' . format_num($_tax,0) .'</td>
                                        <td class="text-right" >' . format_num($_subtotal,0) .'</td>
                                  </tr>';

                            $i++;

                            $row_desc = wordwrap_string($data['item_desc'], 90);
                            $total_line = $total_line + $row_desc['line'];

                            $g_amount += $_amount;
                            $g_tax += $_tax;
                            $g_subtotal += $_subtotal;
                        }

                        if ($max > $total_line){
                            for($x = $total_line;$x <= $max;$x++){
                                //echo '<tr><td colspan="7">&nbsp;</td></tr>';
                            }
                        }
                    }

                    ?>
                    </tbody>
                    <tfoot>
                        <tr class="total">
                            <td class="border-top text-right" colspan="5" style="border-top: 1px solid;border-bottom: 1px solid;">TOTAL (in IDR)</td>
                            <td class="border-top text-right" style="border-top: 1px solid;border-bottom: 1px solid; <?php echo ($g_amount < 0 ? ' ' : ''); ?>"><?php echo format_num($g_amount,0) ?></td>
                            <td class="border-top text-right" style="border-top: 1px solid;border-bottom: 1px solid; <?php echo ($g_tax < 0 ? ' ' : ''); ?>"><?php echo format_num($g_tax,0) ?></td>
                            <td class="border-top text-right" style="border-top: 1px solid;border-bottom: 1px solid; <?php echo ($g_subtotal < 0 ? ' ' : ''); ?>"><?php echo format_num($g_subtotal,0) ?></td>
                        </tr>
                    </tfoot>
                </table>
                <table style="margin-top: 10px;">
                    <tr>
                        <td width="50%" class="v_top text-center border padding-6" style="height: 50px;">
                            <strong>Prepared by :</strong>
                        </td>
                        <td class="v_top text-center border padding-6">
                            <strong>Received by :</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" class="v_top text-center border padding-6" style="height: 140px;">
                            (<span style="width: 200px;display: inline-block;">&nbsp;</span>)
                        </td>
                        <td class="v_top text-center border padding-6">
                            (<span style="width: 200px;display: inline-block;">&nbsp;</span>)
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="page_footer"><p><?php echo nl2br($profile['company_address']); ?></p></div></body></html>