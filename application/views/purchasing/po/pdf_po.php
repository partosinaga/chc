<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->po_code;?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_po.css" rel="stylesheet" type="text/css"/>
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

<table>
    <tr>
        <td width="25%" style="vertical-align:top; text-align:left;">
            <!--h1 style=""><strong><?php echo $row_project->project_name;?></strong></h1-->
            <img src="<?php echo FCPATH;?>assets/img/logo_dwijaya.png" style="height: 80px; position: absolute; top: -18px;" />
        </td>
        <td style="text-align:center;vertical-align:bottom;font-size:12px;padding-left: 30px;"><strong>PURCHASE ORDER</strong></td>
        <td width="25%">
            <table width="100%">
                <tr>
                    <td width="150px">&nbsp;</td>
                    <td width="40px">No</td>
                    <td width="10px">:</td>
                    <td align="right"><?php echo $row->po_code;?></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Date</td>
                    <td>:</td>
                    <td align="right"><?php echo ymd_to_dmy($row->po_date);?></td>
                </tr>
                <tr>
                    <td></td>
                    <td>PR No.</td>
                    <td>:</td>
                    <td align="right"><?php echo $row->pr_code;?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="content">
<div id="body">
<div id="container" style="margin-top:8px;" >
    <table >
        <tr >
            <td style="width:60%;padding: 8px;">
                <table style="width:100%;line-height:15px;" >
                    <tr>
                        <td style="width:60px;"><strong>To.</strong></td>
                        <td style="width:10px;">:</td>
                        <td ><?php echo $row->supplier_name;?></td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td ><strong>Address</strong></td>
                        <td >:</td>
                        <td ><?php echo nl2br($row->supplier_address, true);?></td>
                    </tr>
                    <tr >
                        <td ><strong>HP</strong></td>
                        <td >:</td>
                        <td ><?php echo nl2br($row->contact_phone);?></td>
                    </tr>
                    <tr >
                        <td ><strong>Phone</strong></td>
                        <td >:</td>
                        <td ><?php echo nl2br($row->supplier_telephone);?></td>
                    </tr>
                    <tr >
                        <td ><strong>Fax</strong></td>
                        <td >:</td>
                        <td ><?php echo nl2br($row->supplier_fax);?></td>
                    </tr>
                    <tr >
                        <td ><strong>Attn.</strong></td>
                        <td >:</td>
                        <td ><?php echo nl2br($row->contact_name);?></td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;border-left: 1px solid;vertical-align: top;padding:8px;">
                <table style="width:100%;" >
                    <tr>
                        <td style="width:80px;"><strong>Delivery Date</strong></td>
                        <td style="width:10px;">:</td>
                        <td ><?php echo ymd_to_dmy($row->po_delivery_date);?></td>
                    </tr>
                    <tr>
                        <td><strong>Currency</strong></td>
                        <td>:</td>
                        <td ><?php echo $row->currencytype_code;?></td>
                    </tr>
                    <tr>
                        <td ><strong>Ship To</strong></td>
                        <td >:</td>
                        <td ></td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td colspan="3"><?php echo $row_project->project_name . "<br>" . nl2br($row_project->project_address, true);?></td>
                    </tr>
                </table>
            </td>
        </tr >
    </table>
</div>
<div id="container" style="margin-top:10px;" >
    <div >
        <table style="width:100%;" class="table_detail table_detail_item">
            <tr>
                <th style="width:6%;padding:5px 3px;" class="text-center border_right border_bottom">NO</th>
                <th style="padding:5px 3px;" class="text-left border_right border_bottom">DESCRIPTION</th>
                <th style="width:6%;padding:5px 3px;" class="text-center border_right border_bottom">QTY</th>
                <th style="width:7%;padding:5px 3px;" class="text-center border_right border_bottom">UOM</th>
                <th style="width:13%;padding:5px 3px;" class="text-center border_right border_bottom" >UNIT PRICE</th>
                <th style="width:13%;padding:5px 3px;border-right:0px;" class="text-center border_right border_bottom" >AMOUNT</th>
            </tr>
            <?php
            $subdiscount = 0;
            $subtax = 0;
            $subtotal = 0;
            $max = 31;
            $i = 1;
            $total_line = 0;
            if($qry_det->num_rows() > 0){
                foreach($qry_det->result() as $row_det){
                    $itm_code = ($row_det->item_code == Purchasing::DIRECT_PURCHASE ? $row_det->item_desc : $row_det->ms_item_desc);
                    $description = wordwrap_string($itm_code, 70);

                    $total_line = $total_line + $description['line'];
                    echo '<tr ' . ($i == 1 ? 'class="first"' : '') . '>
                            <td class="text-center" style="width:6%;padding-right: 8px;border-left:0px;">' . $i . '</td>
                            <td class="text-left">' . $description['string'] . '</td>
                            <td class="text-right" style="width:6%;padding-right:5px;">' . number_format($row_det->item_qty, 2, ',', '.') . '</td>
                            <td class="text-center" style="width:7%;">' . $row_det->uom_code . '</td>
                            <td class="text-right" style="width:13%;padding-right:5px;border-left:0px;border_right;">' . number_format($row_det->item_price,($row->currencytype_code == 'IDR' ? 0 : 2),',','.') . '</td>
                            <td class="text-right" style="width:13%;padding-right:5px;border-left:0px;border-right:0px;">' . number_format(($row_det->item_qty * $row_det->item_price),($row->currencytype_code == 'IDR' ? 0 : 2),',','.') . '</td>
                        </tr>';
                    $i++;
                    $subtax += $row_det->tax_amount_vat;
                    $subdiscount += $row_det->item_disc;
                    $subtotal += ($row_det->item_qty * $row_det->item_price);
                }
            }

            if ($max > $total_line){
                for($x = $total_line;$x <= $max;$x++){
                    echo '<tr >
                            <td style="border-left:0px;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="border-left:0px;">&nbsp;</td>
                            <td style="border-left:0px;border-right:0px;">&nbsp;</td>
                        </tr>';
                }
            }
            ?>
        </table>
        <div style="page-break-inside:avoid;">
            <table style="width:100%;" class="table_detail">
                <tr class="border" style="border-bottom:0px;border-right: 0px;border-left:0px;">
                    <td colspan="4" style="padding-top: 0px;vertical-align: top; border-top: 1px solid #000000;border-left: 0px;" >
                        <table>
                            <tr>
                                <td style="width:50px;border: 0px;">Say</td>
                                <td style="width:3px;border: 0px;">:</td>
                                <td style="border: 0px;">(&nbsp;<?php echo number_to_words((($subtotal-$subdiscount) + $subtax)) . ' ' . $row->currencytype_desc ;?>&nbsp;)</td>
                            </tr>
                            <tr>
                                <td style="border: 0px;">Note</td>
                                <td style="border: 0px;">:</td>
                                <td style="border: 0px;"><?php echo ucwords(nl2br($row->remarks, true)) ;?></td>
                            </tr>
                        </table>
                    </td>
                    <td colspan="2" style="width:26%;padding-top: 0px;vertical-align: top; border-top: 1px solid #000000;" >
                        <table >
                            <tr class="text-right">
                                <td style="width:50px;border: 0px;">Total</td>
                                <td style="width:3px;border: 0px;">:</td>
                            </tr>
                            <tr class="text-right">
                                <td style="width:50px;border: 0px;">Total Discount</td>
                                <td style="width:3px;border: 0px;">:</td>
                            </tr>
                            <tr class="text-right">
                                <td style="width:50px;border: 0px;">Tax</td>
                                <td style="width:3px;border: 0px;">:</td>
                            </tr>
                            <tr class="text-right">
                                <td style="width:50px;border: 0px;">Grand Total</td>
                                <td style="width:3px;border: 0px;">:</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:13%;padding-top: 0px;padding-left: 0px;padding-right: 0px;border-right: 0px;vertical-align: top; border-top: 1px solid #000000;">
                        <table>
                            <tr>
                                <td style="border: 0px;border-bottom: solid 1px ;">&nbsp;</td>
                                <td class="text-right" style="border: 0px;border-bottom: solid 1px ;padding-right:5px;" ><?php echo number_format($subtotal,($row->currencytype_code == 'IDR' ? 0 : 2),',','.'); ?></td>
                            </tr>
                            <tr >
                                <td style="border: 0px;">&nbsp;</td>
                                <td class="text-right" style="border: 0px;padding-right:5px;"><?php echo number_format($subdiscount,($row->currencytype_code == 'IDR' ? 0 : 2),',','.'); ?></td>
                            <tr >
                                <td style="border: 0px;">&nbsp;</td>
                                <td class="text-right" style="border: 0px;padding-right:5px;"><?php echo number_format($subtax,($row->currencytype_code == 'IDR' ? 0 : 2),',','.'); ?></td>
                            </tr>
                            <tr style="">
                                <td style="border: 0px;border-top: solid 1px ;">&nbsp;</td>
                                <td class="text-right" style="border: 0px;border-top: solid 1px ;padding-right:5px;"><?php echo number_format((($subtotal - $subdiscount) + $subtax),($row->currencytype_code == 'IDR' ? 0 : 2),',','.'); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="border_top" style="border-right: 0px;border-left: 0px;">
                        <?php
                        $note = str_replace("{term_payment}", $row->term_payment, $row_project->po_report_note);
                        $note = str_replace(" ", "&nbsp;", $note);
                        $note = nl2br($note);
                        echo $note;

                        $app_name = '----';
                        $app_pos = '----';
                        $sign = '';
                        $rate = 1;
                        if($row->curr_rate > 1){
                            $rate = $row->curr_rate;
                        }
                        $total_amount = ($subtotal - $subdiscount) * $rate;
                        $qry_po_approval = $this->db->get_where('in_po_approval', array('min_amount <=' => $total_amount, 'max_amount >' => $total_amount));
                        if($qry_po_approval->num_rows() > 0){
                            $row_po_approval = $qry_po_approval->row();
                            $app_name = $row_po_approval->approval_name;
                            $app_pos = $row_po_approval->position;
                            $sign = $row_po_approval->sign;
                        }
                        ?>
                    </td>
                    <td colspan=3" class="border_top text-center" style="width:36%;border-right: 0px; border-left: 0px;">
                        Jakarta, <?php echo date('d-m-Y');?>
                        <table style="border: 0px;">
                            <tr>
                                <td style="height: 130px;border-left:0px; border-right: 0px;">
                                    <?php
                                    if ($row->status == STATUS_APPROVE || $row->status == STATUS_CLOSED) {
                                        if ($sign != '') {
                                            echo '<img src="' . base_url('assets/img/po_sign/' . $sign) . '" style="height:130px;" />';
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center" style="border-left:0px; border-right: 0px; border-bottom: 1px solid #000000;"><?php echo $app_name;?></td>
                            </tr>
                            <tr>
                                <td class="text-center" style="border-left:0px; border-right: 0px;"><?php echo $app_pos;?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</div>
</div>

</body>
</html>