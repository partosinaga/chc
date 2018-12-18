<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->debitnote_code;?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_jv_dn.css" rel="stylesheet" type="text/css"/>
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
    <tr><td><h1><strong>DEBIT NOTE</strong></h1></td></tr>
</table>
<hr/>
<?php
$dec = 0;
?>

<div id="container" style="margin-top: 5px;">
    <table>
        <tr>
            <td width="450px" class="v_top">
                <table class="t_header">
                    <tr>
                        <td width="90px" class="v_top">Journal ID</td>
                        <td width="10px" class="v_top">:</td>
                        <td class="v_top"><?php echo $row->debitnote_code;?></td>
                    </tr>
                    <tr>
                        <td class="v_top">Date</td>
                        <td class="v_top">:</td>
                        <td class="v_top"><?php echo ymd_to_dmy($row->debitnote_date);?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="t_header">
                    <tr>
                        <td width="90px" class="v_top">Reference No</td>
                        <td width="10px" class="v_top">:</td>
                        <td class="v_top"><?php echo $row->ref_no;?></td>
                    </tr>
                    <tr>
                        <td class="v_top">Reference Date</td>
                        <td class="v_top">:</td>
                        <td class="v_top">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="t_header" style="padding-top: -2px;">
                    <tr>
                        <td width="90px"  class="v_top">Description</td>
                        <td width="10px"  class="v_top">:</td>
                        <td class="v_top"><?php echo $row->remarks;?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table style="margin-top: 20px;" class="detail">
        <tr>
            <th width="80px" class="padding-6">A/C CODE</th>
            <th class="padding-6 text-left">DESCRIPTION</th>
            <th width="50px" class="padding-6">DEPT</th>
            <th width="110px" class="padding-6 text-right">DR (IDR)</th>
            <th width="110px" class="padding-6 text-right">CR (IDR)</th>
        </tr>
        <?php
        $i = 0;
        $total_debit = 0;
        $total_credit = 0;
        foreach($qry_det->result() as $row_det){
            echo '<tr>
                <td class="padding-4 text-center">' . $row_det->coa_code . '</td>
                <td class="padding-4">' . $row_det->coa_desc . '</td>
                <td class="padding-4 text-center">' . $row_det->department_name . '</td>
                <td class="padding-4 text-right">' . format_num($row_det->journal_debit, $dec) . '</td>
                <td class="padding-4 text-right">' . format_num($row_det->journal_credit, $dec) . '</td>
            </tr>';
            $i++;

            $total_debit += $row_det->journal_debit;
            $total_credit += $row_det->journal_credit;
        }
        ?>
    </table>
    <table class="detail" style="margin-top:30px;">
        <tr>
            <th align="right" class="padding-6">TOTAL</th>
            <th width="110px" align="right" class="padding-6"><?php echo format_num($total_debit, $dec);?></th>
            <th width="110px" align="right" class="padding-6"><?php echo format_num($total_credit, $dec);?></th>
        </tr>
    </table>
    <table style="margin-top: 10px;">
        <tr>
            <td width="50%" class="v_top text-center border padding-6" style="height: 100px;">
                <strong>Prepared by :</strong>
            </td>
            <td class="v_top text-center border padding-6">
                <strong>Approved by :</strong>
            </td>
        </tr>
    </table>
</div>
</body>
</html>