<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo($row['journal_no']); ?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_frontend_invoice.css" rel="stylesheet" type="text/css"/>
    <style>
        @page {margin: 30px 30px 30px 50px;}
    </style>
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

<table width="100%">
    <tr>
        <td style="vertical-align:top; text-align:left;" colspan="2">
            <h1 style="font-size: 18px;"><strong><?php //echo $profile['company_name']?>MONSTERA INTI TEKNOLOGI</strong></h1>
        </td>
        <td style="vertical-align:top; text-align:left;" ><h1 style="font-size: 18px;"><strong><?php echo ($folio_title)?></strong></h1></td>
    </tr>
    <tr>
        <td colspan="3" height="5px;"></td>
    </tr>
    <tr>
        <td width="400px" style="vertical-align:top; text-align:left;">
            <table>
                <tr>
                    <td style="width: 60px;vertical-align:top;">To.</td>
                    <td style="width: 10px;vertical-align:top;">:</td>
                    <td><?php echo(isset($guest_info) ? $guest_info : ''); ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Address</td>
                    <td style="vertical-align: top;">:</td>
                    <td><?php echo nl2br($row['company_address']); ?></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Term</td>
                    <td style="vertical-align: top;">:</td>
                    <td><?php echo($row['paymenttype_desc']); ?></td>
                </tr>
            </table>
        </td>
        <td></td>
        <td width="160px">
            <table>
                <tr><td colspan="3"></td></tr>
                <tr>
                    <td>No</td>
                    <td>:</td>
                    <td><?php echo($row['journal_no']); ?></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>:</td>
                    <td><?php echo date('d F Y', strtotime($row['bill_date'])); ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:5px;" >
            <div>
                <table style="width:100%;" class="table_detail">
                    <thead>
                    <tr>
                        <th style="width:30px;">NO</th>
                        <th style="width:60px;" class="text-center">CODE</th>
                        <th style="" class="text-left">ITEMS</th>
                        <th style="width:50px;" class="text-right">QTY</th>
                        <th style="width:70px;" class="text-center">UOM</th>
                        <th style="width:80px;" class="text-right">PRICE</th>
                        <th style="width:80px;" class="text-right">AMOUNT</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $max = 6;
                    $total_line = 0;

                    $grandamount = 0;
                    $grandtax = 0;
                    $grandtotal = 0;
                    if(isset($detail)){
                        $i = 1;
                        foreach($detail as $data){
                            $desc = '[' . $data['item_code'] . '] ' . $data['item_desc'];
                            echo '<tr>
                                        <td class="text-center" style="vertical-align:top;">' . $i . '</td>
                                        <td class="text-center" style="vertical-align:top;">' . $data['item_code'] .'</td>
                                        <td style="vertical-align:top;">' . $data['item_desc'] .'</td>
                                        <td class="text-right" style="vertical-align:top;">' . format_num($data['item_qty'],0) .'</td>
                                        <td class="text-center" style="vertical-align:top;">' . $data['stock_uom'] .'</td>
                                        <td class="text-right" style="vertical-align:top;">' . ($is_unpaid_only ? format_num($data['amount'] - $data['paid_amount'], 0) : format_num($data['amount'], 0)) .'</td>
                                        <td class="text-right" style="vertical-align:top;">' . ($is_unpaid_only ? format_num($data['pending_amount'], 0)  : format_num($data['amount'] + $data['tax'], 0)) .'</td>
                                  </tr>';

                            if($is_unpaid_only){
                                $grandamount += ($data['amount'] - $data['paid_amount']) ;
                                $grandtax += ($data['tax'] - $data['paid_tax']);
                            }else{
                                $grandamount += $data['amount'];
                                $grandtax += $data['tax'];
                            }


                            $i++;

                            $row_desc = wordwrap_string($desc, 90);
                            $total_line = $total_line + $row_desc['line'];
                        }

                        if ($max > $total_line){
                            for($x = $total_line;$x <= $max;$x++){
                                echo '<tr>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                  </tr>';
                            }
                        }
                    }

                    $grandtotal = ($grandamount + $grandtax);

                    ?>
                    </tbody>
                    <tfoot>
                    <tr class="total">
                        <td class="border-top text-left" colspan="5">
                            IN WORDS&nbsp;:&nbsp;<?php echo number_to_words($grandtotal) . ' Rupiahs'; ?>
                        </td>
                        <td class="border-top text-left" style="border-bottom: 1px solid #000;">
                            TOTAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rp
                        </td>
                        <td class="border-top text-right" style="border-bottom: 1px solid #000;<?php echo ($grandtotal < 0 ? 'padding-right:4px;' : 'padding-right:1px;') ?>"><?php echo (amount_journal($grandtotal, 0)) ?></td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="3" style="padding-top: 20px; padding-left: 0px;">
                            <!--div style="border: 1px solid #000; padding: 8px 8px 5px; line-height: 16px;">
                                Note :<br/>
                                Purchased item is not refundable.
                            </div-->
                            <table>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 8px 8px 5px; line-height: 16px;">
                                        Note :<br/>
                                        Purchased item is not refundable.
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>&nbsp;</td>
                        <td colspan="3" style="padding-right: 0px;padding-top: 30px;">
                            <table>
                                <tr>
                                    <td width="50px">&nbsp;</td>
                                    <td align="center">
                                        <table>
                                            <tr>
                                                <td class="v_top text-center padding-6" style="height: 100px;vertical-align: top;">
                                                    <?php echo ($profile['company_name']);?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="v_top text-center padding-6" style="border-bottom: 1px solid #000000; ">
                                                    <?php echo ($profile['approver_name']);?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="v_top text-center padding-6">
                                                    <?php
                                                    echo ($profile['approver_title']);
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>