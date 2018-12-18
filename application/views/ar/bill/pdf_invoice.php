<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo($row['inv_no']); ?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_frontend_invoice_a5.css" rel="stylesheet" type="text/css"/>

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
    <img src="<?php echo FCPATH;?>assets/img/logo_dwijaya.png" alt="" width="100px">
</div>
<table width="100%">
    <tr>
        <td style="vertical-align:top; text-align:left;" colspan="2"></td>
        <td style="vertical-align:top; text-align:left;padding-top: 20px;" ><h1><strong>INVOICE</strong></h1></td>
    </tr>
    <tr>
        <td colspan="3" style="height: 30px;">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="vertical-align: top;">
			<table>
				<tr>
					<td style="vertical-align: top; width: 28px;">TO : </td>
					<td>
						<?php echo(isset($guest_info) ? $guest_info : ''); ?>
					</td>
				</tr>
			</table>
        </td>
        <td width="140px" style="vertical-align: top;">
            <table style="margin-top: -5px;">
                <tr><td colspan="3"></td></tr>
                <tr>
                    <td>NO</td>
                    <td>:</td>
                    <td><?php echo($row['inv_no']); ?></td>
                </tr>
                <tr>
                    <td>DATE</td>
                    <td>:</td>
                    <td><?php echo(dmy_from_db($row['inv_date'])); ?></td>
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
                        <th style="" class="text-center">DESCRIPTION</th>
						 <th style="width:50px;padding-right: 10px;" class="text-right">QTY</th>
						 <th style="width:70px;" class="text-center">UNIT</th>
                        <th style="width:70px;padding-right: 12px;" class="text-right">PRICE</th>
                        <th style="width:70px;padding-right: 12px;" class="text-right">TOTAL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $max = 5;
                    $total_line = 0;

                    $grandamount = 0;
                    $grandtax = 0;
                    $grandtotal = 0;
					$grandprice = 0;
                    if(isset($detail)){
                        $i = 1;
                        foreach($detail as $data){
                            $desc = nl2br($data['description']);
                            echo '<tr>
                                        <td class="text-center" style="vertical-align:top;">' . $i . '</td>
                                        <td style="vertical-align:top;">' . $desc .'</td>
										<td class="text-right" style="vertical-align:top;">' . ($is_unpaid_only ? format_num($data['unit_qty'], 0) : format_num($data['unit_qty'], 0)) .'</td>
										<td class="text-center" style="vertical-align:top;">' . $data['unit_uom'] .'</td>
                                        <td class="text-right" style="vertical-align:top;">' . ($is_unpaid_only ? format_num($data['unit_price'], 0) : format_num($data['unit_price'], 0)) .'</td>
                                        <td class="text-right" style="vertical-align:top;">' . ($is_unpaid_only ? format_num($data['unit_amount'], 0)  : format_num($data['unit_amount'] + $data['tax'], 0)) .'</td>
                                  </tr>';

                            if($is_unpaid_only){
                                $grandamount += $data['unit_amount'];
                                $grandtax += ($data['tax'] - $data['paid_tax']);
								$grandprice += $data['unit_price'];
                            }else{
                                $grandamount += $data['unit_amount'];
                                $grandtax += $data['tax'];
								$grandprice += $data['unit_price'];
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
                                  </tr>';
                            }
                        }
                    }

                    $grandtotal = ($grandamount + $grandtax);

                    ?>
                    </tbody>
                    <tfoot>
                    <tr class="total">
                        <td class="border-top text-left" colspan="5">TOTAL (IDR)</td>
                        <!--td class="border-top text-right" ></td -->
                        <td class="border-top text-right" <?php echo ($grandamount < 0 ? 'style="padding-right:4px;"' : 'style="padding-right:1px"') ?>><?php echo (amount_journal($grandamount, 0)) ?></td>
                    </tr>
                    <tr class="total">
                        <td class="border-top text-left" colspan="6"><!--IN WORDS&nbsp;:&nbsp;<?php echo number_to_words($grandamount) . ' Rupiahs'; ?>--></td>
                    </tr>
                    </tfoot>
                </table>
                <table style="margin-top: 0px;">
                    <tr >
                        <td width="450px" style="vertical-align: top;">
                            <table>
                                <tr>
                                    <td style="height: 40px; vertical-align: top; width: 70px;">IN WORDS&nbsp;:</td>
                                    <td style="height: 40px; vertical-align: top;">
                                        <?php echo number_to_words($grandamount) . ' Rupiahs'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <?php
                                        $note = str_replace(" ", "&nbsp;", $profile['signature_note']);
                                        $note = nl2br($note);
                                        echo $note;
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td align="center" style="">
                            <table>
                                <tr>
                                    <td class="v_top text-center padding-6" style="height: 130px;vertical-align: top;">
                                        <?php echo ($profile['company_name']);?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="v_top text-center padding-6" style="border-bottom: 0px solid #000000; ">
                                        <?php echo ($profile['approver_name']);?>
                                        <hr style="border-bottom: 1px solid #000000; border-top: 0px; border-right: 0px; border-left: 0px; margin: 0px 55px;"/>
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
            </div>
        </div>
    </div>
</div><div id="page_footer"><p><?php echo nl2br($profile['report_footer']); ?></p></div></body></html>