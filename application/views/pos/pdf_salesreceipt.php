<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo($row['journal_no']); ?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_frontend_invoice.css" rel="stylesheet" type="text/css"/>
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
    <img src="<?php echo FCPATH;?>assets/img/logo_dwijaya.png" alt="" width="60px">
</div>
<table width="100%">
    <tr>
        <td style="vertical-align:top; text-align:left;" colspan="2"></td>

        <td style="vertical-align:top; text-align:left;" ><h1><strong><?php echo ($folio_title)?></strong></h1></td>
    </tr>
    <tr>
        <td width="250px" style="vertical-align:top; text-align:left;">
            <!--?php echo(isset($profile) ? nl2br($profile['company_address']) : '-Jalan Asia Afrika No 1<br/>
            Gelora bung karno<br/>
            Jakarta<br/>
            Telp. 99999999<br/>
            Fax.8888888-'); ?-->
        </td>
        <td></td>
        <td width="160px">
            <table>
                <tr><td colspan="3"></td></tr>
                <tr>
                    <td>POS No</td>
                    <td>:</td>
                    <td><?php echo($row['journal_no']); ?></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>:</td>
                    <td><?php echo(dmy_from_db($row['bill_date'])); ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="vertical-align: top;">
            <?php echo(isset($guest_info) ? $guest_info : ''); ?>
        </td>
        <td width="160px" style="vertical-align: top;">
            <table>
                <tr>
                    <td><strong>Payment Type</strong></td>
                </tr>
                <tr>
                    <td><?php echo($row['paymenttype_desc']); ?></td>
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
                        <th style="" class="text-left">DESCRIPTION</th>
                        <th style="width:50px;" class="text-right">QTY</th>
                        <th style="width:70px;" class="text-center">UOM</th>
                        <th style="width:80px;" class="text-right">AMOUNT</th>
                        <th style="width:70px;" class="text-right">TAX</th>
                        <th style="width:80px;" class="text-right">TOTAL</th>
                        <!-- th style="width:60px;" class="text-right">Credit</th -->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $max = 15;
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
                                        <td style="vertical-align:top;">' . $desc .'</td>
                                        <td class="text-right" style="vertical-align:top;">' . format_num($data['item_qty'],0) .'</td>
                                        <td class="text-center" style="vertical-align:top;">' . $data['stock_uom'] .'</td>
                                        <td class="text-right" style="vertical-align:top;">' . ($is_unpaid_only ? format_num($data['amount'] - $data['paid_amount'], 0) : format_num($data['amount'], 0)) .'</td>
                                        <td class="text-right" style="vertical-align:top;">' . ($is_unpaid_only ? format_num($data['tax'] - $data['paid_tax'], 0) : format_num($data['tax'], 0)) .'</td>
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
                        <td class="border-top text-left" colspan="4">TOTAL (IDR)</td>
                        <td class="border-top text-right" <?php echo ($grandamount < 0 ? 'style="padding-right:4px;"' : 'style="padding-right:1px;"') ?>><?php echo (amount_journal($grandamount, 0)) ?></td>
                        <td class="border-top text-right" <?php echo ($grandtax < 0 ? 'style="padding-right:4px;"' : 'style="padding-right:1px"') ?>><?php echo (amount_journal($grandtax, 0)) ?></td>
                        <td class="border-top text-right" <?php echo ($grandtotal < 0 ? 'style="padding-right:4px;"' : 'style="padding-right:1px"') ?>><?php echo (amount_journal($grandtotal, 0)) ?></td>
                    </tr>
                    <tr class="total">
                        <td class="border-top text-left" colspan="7">IN WORDS&nbsp;:&nbsp;<?php echo number_to_words($grandtotal) . ' Rupiahs'; ?></td>
                    </tr>
                    </tfoot>
                </table>
                <table style="margin-top: 30px;">
                    <?php if($row['status'] == STATUS_POSTED){ ?>
                        <tr >
                            <td width="450px" style="vertical-align: top;">

						    </td>
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

                    <?php }else { ?>
                        <tr ><td style="height: 120px;">
								<?php
                                $note = str_replace(" ", "&nbsp;", $profile['signature_note']);
                                $note = nl2br($note);
                                echo $note;
                                //echo nl2br($profile['signature_note']);
                                ?>
						</td></tr>

                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="page_footer"><p><?php echo nl2br($profile['company_address']); ?></p></div></body></html>