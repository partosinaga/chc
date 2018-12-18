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

        <td style="vertical-align:bottom; text-align:left;" ><h1><strong><?php echo ($folio_title)?></strong></h1></td>
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
                <tr><td colspan="3"></td></tr>
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
        <div id="container" style="margin-top:10px;" >
            <div>
                <table style="width:100%;" class="table_detail">
                    <thead>
                    <tr>
                        <th style="width:60px;">Date</th>
                        <th style="width:40px;">Room</th>
                        <th style="" class="text-left">Description</th>
                        <th style="width:80px;" class="text-right">Total Rate</th>
                        <th style="width:60px;" class="text-right">Discount</th>
                        <th style="width:60px;" class="text-right">Tax</th>
                        <th style="width:60px;" class="text-right">Charges</th>
                        <!-- th style="width:60px;" class="text-right">Credit</th -->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $totalDebit = 0;
                    $totalCredit = 0;
                    if(isset($ledger)){
                        if($row['reservation_type'] == RES_TYPE::CORPORATE){
                            if($is_main_folio){
                                foreach($ledger as $data){
                                    if($data['is_primary_debtor'] > 0){
                                        $amount = $data['debit'] - $data['credit'];

                                        echo '<tr>
                                        <td class="text-center">' . dmy_from_db($data['trx_date']) . '</td>
                                        <td class="text-center">' . $data['unit_code'] .'</td>
                                        <td>' . $data['remark'] . '</td>
                                        <td class="text-right">' . format_num($data['amount'], 0) .'</td>
                                        <td class="text-right">' . format_num($data['discount'], 0) .'</td>
                                        <td class="text-right">' . format_num($data['tax'], 0) .'</td>
                                        <td class="text-right" ' . ($amount < 0 ? 'style="padding-right:4px;"' : '') . '>' . amount_journal($amount) .'</td>
                                    </tr>';
                                        $totalDebit += $data['debit'];
                                        $totalCredit += $data['credit'];
                                    }
                                }
                            }else{
                                foreach($ledger as $data){
                                    if($data['is_primary_debtor'] <= 0){
                                        $amount = $data['debit'] - $data['credit'];

                                        echo '<tr>
                                        <td class="text-center">' . dmy_from_db($data['trx_date']) . '</td>
                                        <td class="text-center">' . $data['unit_code'] .'</td>
                                        <td>' . $data['remark'] . '</td>
                                        <td class="text-right">' . format_num($data['amount'], 0) .'</td>
                                        <td class="text-right">' . format_num($data['discount'], 0) .'</td>
                                        <td class="text-right">' . format_num($data['tax'], 0) .'</td>
                                        <td class="text-right" ' . ($amount < 0 ? 'style="padding-right:4px;"' : '') . '>' . amount_journal($amount) .'</td>
                                    </tr>';
                                        $totalDebit += $data['debit'];
                                        $totalCredit += $data['credit'];
                                    }
                                }
                            }
                        }else{
                            foreach($ledger as $data){
                                $amount = $data['debit'] - $data['credit'];

                                echo '<tr>
                                    <td class="text-center">' . dmy_from_db($data['trx_date']) . '</td>
                                    <td class="text-center">' . $data['unit_code'] .'</td>
                                    <td>' . $data['remark'] . '</td>
                                    <td class="text-right">' . format_num($data['amount'], 0) .'</td>
                                    <td class="text-right">' . format_num($data['discount'], 0) .'</td>
                                    <td class="text-right">' . format_num($data['tax'], 0) .'</td>
                                    <td class="text-right" ' . ($amount < 0 ? 'style="padding-right:4px;"' : '') . '>' . amount_journal($amount) .'</td>
                                </tr>';
                                $totalDebit += $data['debit'];
                                $totalCredit += $data['credit'];
                            }
                        }
                    }
                    $balance = $totalDebit - $totalCredit;

                    //DEPOSIT
                    $deposit_in = 0;
                    $deposit_al = 0;
                    $deposit_out = 0;
                    if(isset($deposits)){
                        $deposit_in = ($deposits['deposit'] * -1);
                        $deposit_al = $deposits['alloc'];
                        $deposit_out = $deposits['refund'];
                    }
                    $deposit_unalloc = round($deposit_in + $deposit_al,0);

                    ?>
                    </tbody>
                    <tfoot>
                    <tr class="total">
                        <td colspan="6" class="border-top">BALANCE DUE (IDR)</td>
                        <td class="border-top text-right" <?php echo ($balance < 0 ? 'style="padding-right:4px;"' : '') ?>><?php echo (amount_journal($balance, 0)) ?></td>
                    </tr>
                    <?php if($row['reservation_type'] != RES_TYPE::CORPORATE || !$is_main_folio){ ?>
                    <tr class="total">
                        <td colspan="6" >DEPOSIT UNALLOCATED (IDR)</td>
                        <td class="text-right" <?php echo ($deposit_unalloc < 0 ? 'style="padding-right:4px;"' : '') ?>><?php echo (amount_journal($deposit_unalloc, 0)) ?></td>
                    </tr>
                    <tr class="total">
                        <td colspan="6" >DEPOSIT REFUND (IDR)</td>
                        <td class="text-right" <?php echo ($deposit_out < 0 ? 'style="padding-right:4px;"' : '') ?>><?php echo (amount_journal($deposit_out, 0)) ?></td>
                    </tr>
                    <?php  } ?>
                    </tfoot>
                    <!-- tr>
                        <td colspan="5" class="border-top">BALANCE DUE</td>
                        <td class="border-top text-right"><?php echo (format_num($totalDebit - $totalCredit, 0)) ?></td>
                        <td class="border-top text-right"></td>
                    </tr -->
                </table>
            </div>
        </div>
    </div>
</div>
<div id="footer">
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
</div>
<div id="page_footer"><p><?php echo nl2br($profile['company_address']); ?></p></div></body></html>