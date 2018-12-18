<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo($row['do_no']); ?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_frontend_invoice_a5v1.css" rel="stylesheet" type="text/css"/>

</head>
<body>
<script type="text/php">
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
    <img src="<?php echo FCPATH; ?>assets/img/logo_dwijaya.png" alt="" width="100px">
</div>
<table width="100%">
    <tr>
        <td style="vertical-align:top; text-align:left;" colspan="2"></td>
        <td style="vertical-align:top; text-align:left;padding-top: 20px;"><h1 style="font-size: 20px;"><strong>DELIVERY ORDER</strong></h1></td>
    </tr>
    <tr>
        <td colspan="3" style="height: 30px;">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="vertical-align: top;">
            <table>
                <tr>
                    <td style="vertical-align: top; width: 28px;">TO :</td>
                    <td>
                        <?php echo(isset($guest_info) ? $guest_info : ''); ?>
                    </td>
                </tr>
            </table>
        </td>
        <td width="180px" style="vertical-align: top;">
            <table style="margin-top: -5px;">
                <tr>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td width="50px;">NO</td>
                    <td width="3px;">:</td>
                    <td><?php echo($row['do_no']); ?></td>
                </tr>
                <tr>
                    <td width="50px;">DATE</td>
                    <td width="3px;">:</td>
                    <td><?php echo(dmy_from_db($row['do_date'])); ?></td>
                </tr>
                <tr>
                    <td width="50px;">PO</td>
                    <td width="3px;">:</td>
                    <td><?php echo $row['remark']; ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:5px;">
            <div>
                <table style="width:100%;" class="table_detail">
                    <thead>
                    <tr>
                        <th style="width:30px;">NO</th>
                        <th style="" class="text-center">DESCRIPTION</th>
                        <th style="width:100px;padding-right: 10px;" class="text-right">QTY</th>
                        <th style="width:100px;" class="text-center">UOM</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $max = 5;
                    $total_line = 0;

                    if (isset($detail)) {
                        $i = 1;
                        foreach ($detail as $data) {
                            $desc = nl2br($data['item_desc']);
                            echo '<tr>
                                        <td class="text-center" style="vertical-align:top;">' . $i . '</td>
                                        <td style="vertical-align:top;">' . $desc . '</td>
										<td class="text-right" style="vertical-align:top;">' . format_num($data['item_qty'], 0) . '</td>
										<td class="text-center" style="vertical-align:top;">' . $data['stock_uom'] . '</td>
                                  </tr>';

                            $i++;

                            $row_desc = wordwrap_string($desc, 90);
                            $total_line = $total_line + $row_desc['line'];
                        }

                        if ($max > $total_line) {
                            for ($x = $total_line; $x <= $max; $x++) {
                                echo '<tr>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                        <td >&nbsp;</td>
                                  </tr>';
                            }
                        }
                    }

                    ?>
                    </tbody>
                    <tfoot>
                    <tr class="total">
                        <td class="border-top text-left" colspan="4"></td>
                    </tr>
                    </tfoot>
                </table>
                <table style="margin-top: 0px;">
                    <tr>
                        <td width="40%" style="vertical-align: top;">
                            <table>
                                <tr>
                                    <td class="v_top text-center padding-6" style="height: 130px;vertical-align: top;">
                                        Delivered By,
                                    </td>
                                </tr>
                                <tr>
                                    <td class="v_top text-center padding-6" style="border-bottom: 0px solid #000000; ">
                                        <?php echo $row['delivered_by']; ?>
                                        <hr style="border-bottom: 1px solid #000000; border-top: 0px; border-right: 0px; border-left: 0px; margin: 0px 55px;"/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="20%" style="vertical-align: top;">
                        <td width="40%" align="center" style="">
                            <table>
                                <tr>
                                    <td class="v_top text-center padding-6" style="height: 130px;vertical-align: top;">
                                        Received By,
                                    </td>
                                </tr>
                                <tr>
                                    <td class="v_top text-center padding-6" style="border-bottom: 0px solid #000000; ">
                                        <?php echo (trim($row['delivered_by']) != '' ? '&nbsp;' : ''); ?>
                                        <hr style="border-bottom: 1px solid #000000; border-top: 0px; border-right: 0px; border-left: 0px; margin: 0px 55px;"/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="page_footer"><p><?php echo nl2br($profile['report_footer']); ?></p></div>
</body>
</html>