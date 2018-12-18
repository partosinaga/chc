<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo($row['pro_inv_no']); ?></title>

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
    <img src="<?php echo FCPATH; ?>assets/img/logo_dwijaya.png" alt="" width="60px">
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
                    <td nowrap>Proforma Inv. #</td>
                    <td>:</td>
                    <td><?php echo($row['pro_inv_no']); ?></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>:</td>
                    <td><?php echo(ymd_to_dmy($row['pro_inv_date'])); ?></td>
                </tr>
                <tr>
                    <td>Due Date</td>
                    <td>:</td>
                    <td><?php echo(ymd_to_dmy($row['pro_inv_due_date'])); ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">
            <?php echo(isset($guest_info) ? $guest_info : ''); ?>
        </td>
        <td>

        </td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:10px;" >
            <div>
                <table style="width:100%;" class="table_detail">
                    <thead>
                    <tr>
                        <th style="width:30px;">NO</th>
                        <th style="" class="text-left">DESCRIPTION</th>
                        <th style="width:90px;" class="text-right">AMOUNT</th>
                        <th style="width:80px;" class="text-right">TAX</th>
                        <th style="width:90px;" class="text-right">TOTAL</th>
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
                            echo '<tr>
                                        <td class="text-center">' . $i . '</td>
                                        <td>' . $data['item_desc'] .'</td>
                                        <td class="text-right">' .  format_num($data['amount'], 0) .'</td>
                                        <td class="text-right">' . ($is_unpaid_only ? format_num($data['tax'] - $data['paid_tax'], 0) : format_num($data['tax'], 0)) .'</td>
                                        <td class="text-right">' . ($is_unpaid_only ? format_num($data['pending_amount'], 0)  : format_num($data['amount'] + $data['tax'], 0)) .'</td>
                                  </tr>';

                            if($is_unpaid_only){
                                $grandamount += ($data['amount'] - $data['paid_amount']) ;
                                $grandtax += ($data['tax'] - $data['paid_tax']);
                            }else{
                                $grandamount += $data['amount'];
                                $grandtax += $data['tax'];
                            }


                            $i++;

                            $row_desc = wordwrap_string($data['item_desc'], 90);
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
                                  </tr>';
                            }
                        }
                    }

                    $grandtotal = ($grandamount + $grandtax);

                    ?>
                    </tbody>
                    <tfoot>
                    <tr class="total">
                        <td class="border-top text-left" colspan="2">TOTAL (IDR)</td>
                        <td class="border-top text-right" <?php echo ($grandamount < 0 ? 'style="padding-right:4px;"' : 'style="padding-right:1px;"') ?>><?php echo (amount_journal($grandamount, 0)) ?></td>
                        <td class="border-top text-right" <?php echo ($grandtax < 0 ? 'style="padding-right:4px;"' : 'style="padding-right:1px"') ?>><?php echo (amount_journal($grandtax, 0)) ?></td>
                        <td class="border-top text-right" <?php echo ($grandtotal < 0 ? 'style="padding-right:4px;"' : 'style="padding-right:1px"') ?>><?php echo (amount_journal($grandtotal, 0)) ?></td>
                    </tr> 
                    <tr class="total">
                        <td class="border-top text-left" colspan="5">IN WORDS&nbsp;:&nbsp;<?php echo number_to_words($grandtotal) . ' Rupiahs'; ?></td>
                    </tr>
					
                    </tfoot>
                </table>
				<br><br>
				
			    
                <table style="margin-top: 30px;">
                    <?php/* if($row['status'] == STATUS_POSTED){ */ ?>
                        <tr ><td style="height: 120px;">
								<table style="width:300%;"  class="table table-striped table-bordered  ">
									<tr>
										<td>Please Transfer The Payment To :</td>
									</tr>
									<tr>
										<td>Bank CIMB NIAGA</td>
									</tr>
									<tr>
										<td  ><b>Account No : 800134767700</b></td>
									</tr>
									<tr>
										<td>Name &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; : PT. Sudirman Jaya Makmur</td>
									</tr>
									
									<tr>
										<td>Swift code &nbsp; :  BNIAIDJA</td>
									</tr>				
								</table >
						</td></tr> 
                        <tr>
                            <td width="75%" class="v_top text-center padding-6">&nbsp;
                            </td>
                            <td class="v_top text-center padding-4" style="border-bottom: 1px solid #000000; ">
                               <b> <?php echo ($profile['approver_name']);?></b>
                            </td>
                        </tr>
                        <tr>
                            <td width="75%" class="v_top text-center padding-6">
                            </td>
                            <td class="v_top text-center padding-4">
                               <b> <?php echo ($profile['approver_title']);?></b>
                            </td>
                        </tr> 
                    <?php /*}*/ ?>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="page_footer"><p><?php echo nl2br($profile['company_address']); ?></p></div></body></html>