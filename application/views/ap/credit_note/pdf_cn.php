<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->creditnote_code;?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_dn.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<script type="text/php">
    if ( isset($pdf) ) {
        $font = Font_Metrics::get_font("helvetica", "");
    }
</script>

<table>
    <tr>
        <td align="center" style="height: 65px;">
            <img src="<?php echo FCPATH;?>assets/img/logo_dwijaya.png" style="height: 90px; position: absolute; top: 0px; left:312px;"/>
        </td>
    </tr>
    <tr><td><h1><strong>CREDIT NOTE</strong></h1></td></tr>
</table>

<?php
$dec = 0;
if ($row->currencytype_code != Purchasing::CURR_IDR) {
    $dec = 2;
}
?>

<div id="container">
    <table class="t_header">
        <tr>
            <td width="480px" class="v_top">
                <table>
                    <tr>
                        <td width="90px" class="v_top">To</td>
                        <td width="10px" class="v_top">:</td>
                        <td class="v_top"><?php echo $row->supplier_name . '<br/><div style="padding-top:3px;">' . $row->supplier_address;?></div></td>
                    </tr>
                    <tr>
                        <td class="v_top">Invoice No</td>
                        <td class="v_top">:</td>
                        <td class="v_top"><?php echo $row->inv_code;?></td>
                    </tr>
                    <tr>
                        <td class="v_top">Description</td>
                        <td class="v_top">:</td>
                        <td class="v_top"><?php echo $row->remarks;?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td width="90px" class="v_top">Document No</td>
                        <td width="10px" class="v_top">:</td>
                        <td class="v_top"><?php echo $row->creditnote_code;?></td>
                    </tr>
                    <tr>
                        <td class="v_top">Currency</td>
                        <td class="v_top">:</td>
                        <td class="v_top"><?php echo $row->currencytype_code;?></td>
                    </tr>
                    <tr>
                        <td class="v_top">Rate</td>
                        <td class="v_top">:</td>
                        <td class="v_top"><?php echo format_num($row->curr_rate, 2);?></td>
                    </tr>
                    <tr>
                        <td class="v_top">Date</td>
                        <td class="v_top">:</td>
                        <td class="v_top"><?php echo ymd_to_dmy($row->creditnote_date);?></td>
                    </tr>
                    <tr>
                        <td class="v_top">Reference</td>
                        <td class="v_top">:</td>
                        <td class="v_top"><?php echo $row->ref_no;?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div style="margin-top: 10px; display: block;">We Would like to Debit your account with the following details :</div>
    <table style="margin-top: 10px;" class="detail">
        <tr>
            <th class="padding-6" align="left">DESCRIPTION</th>
            <th width="150px" class="padding-6" align="right">LOCAL AMOUNT</th>
            <th width="150px" class="padding-6" align="right">CN AMOUNT</th>
        </tr>
        <?php
        $i = 0;
        foreach($qry_det->result() as $row_det){
            echo '<tr>
                <td class="padding-4">' . $row_det->coa_desc . '</td>
                <td class="padding-4 text-right">' . format_num($row_det->local_amount, $dec) . '</td>
                <td class="padding-4 text-right">' . format_num($row_det->amount, $dec) . '</td>
            </tr>';
            $i++;
        }
        ?>
    </table>
    <table class="detail" style="margin-top:30px;">
        <tr>
            <th align="right" class="padding-6"><?php echo format_num($row->amount, $dec);?></th>
        </tr>
    </table>
    <table style="margin-top: 10px;">
        <tr>
            <td width="470px" class="v_top" style="padding-right: 20px;">
                <?php echo number_to_words($row->amount) . ' ' . $row->currencytype_desc;?>
            </td>
            <td style="border-bottom: 1px solid #000000; height: 120px;">&nbsp;</td>
        </tr>
    </table>
</div>
</body>
</html>