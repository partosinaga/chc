<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo($row['reservation_code']); ?></title>

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
                //$pdf->text($x, $y, $pageText, $font, $size);
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
        <td style="vertical-align:top; text-align:left;" ><h1><strong>Room Reference</strong></h1></td>
    </tr>
    <tr>
        <td width="250px" style="vertical-align:top; text-align:left;">
            <!-- -Jalan Asia Afrika No 1<br/>
            Gelora bung karno<br/>
            Jakarta<br/>
            Telp. 99999999<br/>
            Fax.8888888-->
        </td>
        <td></td>
        <td width="160px">
            <table>
                <tr>
                    <td>Folio #</td>
                    <td>:</td>
                    <td><?php echo($row['reservation_code']); ?></td>
                </tr>
                <tr>
                    <td>Cashier</td>
                    <td>:</td>
                    <td><?php echo(my_sess('user_fullname')); ?></td>
                </tr>
                <tr>
                    <td>Time</td>
                    <td>:</td>
                    <td><?php echo(date('d/m/Y H:m:s')); ?></td>
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
        <td style="vertical-align: top;">
            <table>
                <tr>
                    <td>Check In</td>
                    <td>:</td>
                    <td><?php echo(date('d-m-Y', strtotime($row['arrival_date']))); ?></td>
                </tr>
                <tr>
                    <td>Check Out</td>
                    <td>:</td>
                    <td><?php echo(date('d-m-Y', strtotime($row['departure_date']))); ?></td>
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
                    <tr>
                        <th >Room</th>
                        <th >Month</th>
                        <th class="text-right">Rate</th>
                    </tr>
                    <tbody>
                    <?php
                    $totalDebit = 0;
                    if(isset($ledger)){
                        $unit_code = '';
                        foreach($ledger as $data){
                            echo '<tr>';
                            if($unit_code != $data['unit_code']){
                                echo '<td class="text-center" width="100px;">' . $data['unit_code'] .'</td>';
                                $unit_code = $data['unit_code'];
                            }else{
                                echo '<td class="text-center" width="100px;">&nbsp;</td>';
                            }
                            echo '<td class="text-center" width="150px;">' . date('m', strtotime(ymd_from_db($data['room_date']))) . '</td>
                                <td class="text-right" width="150px;">' . format_num($data['rate'], 0) .'</td>
                                </tr>';
                            $totalDebit += $data['rate'];
                        }
                    }
                    ?>
                    <tr class="total">
                        <td colspan="2" class="border-top">TOTAL (IDR)</td>
                        <td class="border-top text-right"><?php echo (format_num($totalDebit, 0)) ?></td>
                    </tr>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
<!--div id="footer">
    <p><?php echo nl2br($profile['report_note']); ?></p>
    <hr class="hr_footer"/>
    <table>
        <tr >
            <td width="300px;" style="vertical-align: top;">
                Guest Signature <strong>X</strong> _______________________ <br/>
                <?php echo isset($profile['signature_note']) ? nl2br($profile['signature_note']) : ''; ?>
            </td>
            <td style="vertical-align: top;">
                I agree that my liability
            </td>
        </tr>
    </table>
</div -->
<div id="page_footer"><p><?php echo nl2br($profile['company_address']); ?></p></div></body></html>