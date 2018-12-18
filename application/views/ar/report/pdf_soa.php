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
    <img src="<?php echo base_url('assets/img/logo_dwijaya.png'); ?>" alt="" width="60px">
</div>
<table width="100%">
    <tr>
        <td style="vertical-align:top; text-align:left;" colspan="3"></td>
    </tr>
    <tr>
        <td width="250px" style="vertical-align:top; text-align:left;">

        </td>
        <td></td>
        <td width="155px">
            <table style="padding-bottom: 6px;">
                <tr>
                    <td ><strong>STATEMENT OF ACCOUNT</strong></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>Folio #</td>
                    <td>:</td>
                    <td ><?php echo($row['reservation_code']); ?></td>
                </tr>
                <tr>
                    <td>Room</td>
                    <td>:</td>
                    <td><?php echo($row['room']); ?></td>
                </tr>
                <tr>
                    <td>Print time</td>
                    <td>:</td>
                    <td ><?php echo(date('d/m/y H:m:s')); ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">
            <?php echo((trim($row['tenant_salutation']) != '' ? $row['tenant_salutation'] . ' ' : '') . $row['tenant_fullname']); ?><br/>
            <?php echo(isset($tenant) ? nl2br($tenant['tenant_address']) : ''); ?><br/>
            <?php echo(isset($tenant) ? $tenant['tenant_city'] . ' ' . $tenant['tenant_postalcode'] : ''); ?><br/>
            <?php echo(isset($tenant) ? $tenant['tenant_country'] : ''); ?>
        </td>
        <td>
            <table>

            </table>
        </td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:10px;" >
            <div>
                <table style="width:100%;" class="table_detail">
                    <tr>
                        <th style="width:20px;">No</th>
                        <th style="width:80px;" class="text-center">Doc No</th>
                        <th style="width:70px;" class="text-center">Date</th>
                        <th class="text-left">Description</th>
                        <th style="width:70px;" class="text-right">Amount</th>
                        <th style="width:70px;" class="text-right">Balance</th>
                    </tr>
                    <?php
                    $total = 0;
                    if(isset($ledger)){
                        $i = 1;
                        foreach($ledger as $data){
                            echo '<tr>
                                <td class="text-right">' . $i . '</td>
                                <td class="text-center">' . $data['doc_no'] . '</td>
                                <td class="text-center">' . dmy_from_db($data['doc_date']) .'</td>
                                <td>' . nl2br($data['remark']) . '</td>
                                <td class="text-right">' . amount_journal($data['amount']) .'</td>
                                <td class="text-right">' . amount_journal($data['balance']) . '</td>
                            </tr>';
                            $total += $data['amount'];

                            $i++;
                        }
                    }
                    ?>
                    <tr class="total">
                        <td colspan="5" class="border-top text-right">ENDING BALANCE </td>
                        <td class="border-top text-right"><?php echo (amount_journal($total)) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div >
    <table style="margin-top: 150px;">
        <tr>
            <td class="v_top text-center padding-6" style="border-bottom: 1px solid #000000; ">
                <?php echo ($profile['approver_name']);?>
            </td>
            <td width="65%" class="v_top text-center padding-6">
            </td>
        </tr>
        <tr>
            <td class="v_top text-center padding-6">
                <?php echo ($profile['approver_title']);?>
            </td>
            <td width="65%" class="v_top text-center padding-6">
            </td>
        </tr>
    </table>
</div>

</body>
</html>