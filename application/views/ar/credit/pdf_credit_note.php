<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->credit_no;?></title>

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
    <tr><td><h1><strong>CREDIT NOTE</strong></h1></td></tr>
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
                        <td class="v_top"><?php echo $row->credit_no;?></td>
                    </tr>
                    <tr>
                        <td class="v_top">Date</td>
                        <td class="v_top">:</td>
                        <td class="v_top"><?php echo dmy_from_db($row->credit_date);?></td>
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
                        <td class="v_top"><?php echo nl2br($row->credit_remark);?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table style="margin-top: 20px;" class="detail">
        <tr><td colspan="5"><p>We would like to Credit your account with the following details :</p></td></tr>
        <tr>
            <th width="30px" class="padding-6">NO</th>
            <th class="padding-6 text-left">DESCRIPTION</th>
            <th width="100px" class="padding-6 text-right">AMOUNT</th>
            <th width="80px" class="padding-6 text-right">TAX</th>
            <th width="100px" class="padding-6 text-right">TOTAL</th>
        </tr>
        <?php
        $i = 1;
        $total= 0;
        $max = 30;
        $total_line = 0;
        foreach($qry_det->result() as $row_det){
            $desc = '';
            $desc = ($row_det->is_tax > 0 ? '(VAT) ' : '') . $row_det->description;
                if($row_det->is_tax <= 0 ){
                    echo '<tr>
                <td class="padding-4 text-center">' . $i . '</td>
                <td class="padding-4 text-left">' . $desc . '</td>
                <td class="padding-4 text-right">' . format_num($row_det->credit_amount, $dec) . '</td>
                <td class="padding-4 text-right">' . format_num(0, $dec) . '</td>
                <td class="padding-4 text-right">' . format_num($row_det->credit_amount, $dec) . '</td>
            </tr>';
                }else{
                    echo '<tr>
                <td class="padding-4 text-center">' . $i . '</td>
                <td class="padding-4 text-left">' . $desc . '</td>
                <td class="padding-4 text-right">' . format_num(0, $dec) . '</td>
                <td class="padding-4 text-right">' . format_num($row_det->credit_amount, $dec) . '</td>
                <td class="padding-4 text-right">' . format_num($row_det->credit_amount, $dec) . '</td>
            </tr>';
                }

            $i++;

            $total += $row_det->credit_amount;

            //calculate line
            $row_desc = wordwrap_string($desc, 70);
            $total_line = $total_line + $row_desc['line'];
        }

        if ($max > $total_line){
            for($x = $total_line;$x <= $max;$x++){
                echo '<tr >
                            <td style="border-left:0px;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="border-left:0px;">&nbsp;</td>
                            <td style="border-left:0px;">&nbsp;</td>
                            <td style="border-left:0px;border-right:0px;">&nbsp;</td>
                       </tr>';
            }
        }

        ?>
    </table>
    <table class="detail" style="margin-top:30px;">
        <tr>
            <th align="right" class="padding-6">TOTAL</th>
            <th width="110px" align="right" class="padding-6"><?php echo format_num($total, $dec);?></th>
        </tr>
    </table>
    <table style="margin-top: 10px;">
        <tr>
            <td width="20px" class="v_top">SAY :</td>
            <td width="470px" class="v_top" style="padding-right: 20px;">
                <?php echo number_to_words($total);?>
            </td>
        </tr>
    </table>
    <table style="margin-top: 10px;">
        <tr>
            <td width="470px" class="v_top" style="padding-right: 20px;">
                <p>Please take a note that this credit can be used to deduct the amount on the current bills.
                    If a payment has already been made, the credit will be automatically apply for the next billing cycle.
                    Thank you.</p>
            </td>
        </tr>
    </table>
    <table style="margin-top: 30px;">
        <tr>
            <td width="65%" class="v_top text-center padding-6">
            </td>
            <td class="v_top text-center padding-6" style="border-bottom: 1px solid #000000; ">
                <?php echo ($profile['approver_name']);?>
            </td>
        </tr>
        <tr>
            <td width="65%" class="v_top text-center padding-6">
            </td>
            <td class="v_top text-center padding-6">
                <?php echo ($profile['approver_title']);?>
            </td>
        </tr>
    </table>
</div>
</body>
</html>