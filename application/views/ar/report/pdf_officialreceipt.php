<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $receipt_no ?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_officialreceipt.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="logo">
    <!-- img src="<?php echo base_url(); ?>assets/img/logo_dwijaya.png" alt="" width="65px" -->
</div>
<div>
    <h2 style="text-align: center;margin-top:29px;">OFFICIAL RECEIPT</h2>
</div>
<div style="padding-left: 12px;">
    <table width="100%" class="table_main" style="margin-top: 30px;">
        <tbody>
            <tr>
                <td width="110px;" >
                    Receipt No
                </td>
                <td width="5px;" >
                    :
                </td>
                <td >
                    <?php echo $receipt_no;?>
                </td>
            </tr>
            <tr>
                <td width="110px;" >
                    Received From
                </td>
                <td width="5px;" >
                    :
                </td>
                <td >
                    <?php echo $receipt_from; ?>
                </td>
            </tr>
            <tr>
                <td width="110px;" >
                    Total Amount
                </td>
                <td width="5px;" >
                    :
                </td>
                <td >
                    <?php echo format_num($receipt_amount,0); ?>
                </td>
            </tr>
            <tr>
                <td width="110px;" >
                    In Words
                </td>
                <td width="5px;" >
                    :
                </td>
                <td >
                    # <?php echo number_to_words($receipt_amount) . ' Rupiah'; ?> #
                </td>
            </tr>
            <tr>
                <td width="110px;" >
                    Remark
                </td>
                <td width="5px;" >
                    :
                </td>
                <td >
                    <?php echo nl2br($receipt_desc); ?>
                </td>
            </tr>
        </tbody>
    </table>

    <table style="margin-top: 20px;padding-top: 20px;" >
        <tr >
            <td width="230px" class="receipt_amount" rowspan="2">&nbsp;</td>
            <td width="150px" class="text-center">Jakarta, <?php echo date("j F Y", strtotime(ymd_from_db($receipt_date))); ?></td>
        </tr>
        <tr>
            <td style="line-height: 80px;">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center">
                <!-- table class="table_sign">
                    <tr>
                        <td >The Receipt will be valid if the payment with Cheque/Giro have been transferred to our Bank Account.</td>
                    </tr>
                </table -->
            </td>
            <td width="150px" style="text-align: center;"><?php echo isset($created_by_name) ? $created_by_name : my_sess('user_fullname'); ?></td>
        </tr>
    </table>
</div></body></html>