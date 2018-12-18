<html lang="en">
<head>
    <meta charset="utf-8">
    <title></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_payment.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<script type="text/php">
    if ( isset($pdf) ) {
        $font = Font_Metrics::get_font("helvetica", "");
    }
</script>

<table>
    <tr>
        <td width="25%">
            <img src="<?php echo FCPATH;?>assets/img/logo_dwijaya.png" style="height: 90px; position: absolute; top: 0px; left:5px;"/>
        </td>
        <td><h1><strong>BANK PAYMENT VOUCHER</strong></h1></td>
        <td width="25%">
            <table style="margin-left: 45px;margin-top: 33px;">
                <tr>
                    <td>No</td>
                    <td>:</td>
                    <td><?php echo $row->payment_code;?></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>:</td>
                    <td><?php echo ymd_to_dmy($row->payment_date);?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="container">
    <div id="container-detail">
        <table>
            <tr>
                <td width="402.5px">
                    <table class="header-payment">
                        <tr>
                            <td width="80px">Pay To</td>
                            <td width="5px">:</td>
                            <td><?php echo $row->supplier_name;?></td>
                        </tr>
                        <tr>
                            <td>Bank</td>
                            <td>:</td>
                            <td><?php echo $row->bank_code;?></td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td>:</td>
                            <td><?php echo $row->currencytype_code . ' ' . format_num($row->total_amount, decimal_curr($row->currencytype_code));?></td>
                        </tr>
                        <tr>
                            <td>Say</td>
                            <td>:</td>
                            <td><?php echo number_to_words($row->total_amount) . ' ' . $row->currencytype_desc;?></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td>:</td>
                            <td><?php echo $row->description;?></td>
                        </tr>
                    </table>
                </td>
                <td style="border-left: 1px solid #000000;">
                    <table class="header-payment">
                        <tr>
                            <td width="100px">Account Name</td>
                            <td width="5px">:</td>
                            <td><?php echo $row->account_bank_name;?></td>
                        </tr>
                        <tr>
                            <td>Account Number</td>
                            <td>:</td>
                            <td><?php echo $row->account_bank_no;?></td>
                        </tr>
                        <tr>
                            <td>Bank Name</td>
                            <td>:</td>
                            <td><?php echo $row->supplier_bank_name;?></td>
                        </tr>
                        <tr>
                            <td>Refference No</td>
                            <td>:</td>
                            <td><?php echo $row->ref_no;?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="payment-detail">
            <thead>
                <tr>
                    <th width="10px" class="text-center" style="border-left: 0px;">NO</th>
                    <th width="110px" class="text-center">DOCUMENT NO</th>
                    <th width="145px" class="text-center">DESCRIPTION</th>
                    <th width="90px" class="text-right">AMOUNT</th>
                    <th width="75px" class="text-right">TAX</th>
                    <th width="75px" class="text-right">WHT</th>
                    <th class="text-right" style="border-right: 0px;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            $total_amount = 0;
            $total_vat = 0;
            $total_wht = 0;
            $total_grand = 0;
            foreach ($qry_det->result() as $row_det) {
                $tmp_amount = $row_det->amount + $row_det->tax_wht;
                $vat = $row_det->inv_vat;
                if ($vat > 0) {
                    $vat = ($tmp_amount / $row_det->inv_amount) * $vat;
                }
                $amount = $row_det->amount - $vat;

                echo '<tr>
                      <td class="text-center" style="border-left: 0px;">' . $i . '.</td>
                      <td class="text-center">' . $row_det->inv_code . '</td>
                      <td>' . $row_det->inv_desc. '</td>
                      <td class="text-right">' . format_num($amount, decimal_curr($row->currencytype_code)). '</td>
                      <td class="text-right">' . format_num($vat, decimal_curr($row->currencytype_code)). '</td>
                      <td class="text-right">' . format_num($row_det->tax_wht, decimal_curr($row->currencytype_code)). '</td>
                      <td class="text-right" style="border-right: 0px;">' . format_num(($amount + $row_det->tax_wht + $vat), decimal_curr($row->currencytype_code)). '</td>
                    </tr>';
                $total_amount += $amount;
                $total_vat += $vat;
                $total_wht += $row_det->tax_wht;
                $total_grand += ($amount + $row_det->tax_wht + $vat);

                $i++;
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-right" style="border-left: 0px;" colspan="3">TOTAL</th>
                    <th class="text-right"><?php echo format_num($total_amount, decimal_curr($row->currencytype_code));?></th>
                    <th class="text-right"><?php echo format_num($total_vat, decimal_curr($row->currencytype_code));?></th>
                    <th class="text-right"><?php echo format_num($total_wht, decimal_curr($row->currencytype_code));?></th>
                    <th class="text-right" style="border-right: 0px;"><?php echo format_num($total_grand, decimal_curr($row->currencytype_code));?></th>
                </tr>
            </tfoot>
            <tbody>
                <tr>
                    <td colspan="2" style="height: 120px;" class="text-center v_top">
                        Prepared by,
                    </td>
                    <td class="text-center v_top">
                        Checked by,
                    </td>
                    <td colspan="2" class="text-center v_top">
                        Approved by,
                    </td>
                    <td colspan="2" class="text-center v_top">
                        Received by,
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="border_top">
                        Date :
                    </td>
                    <td class="border_top">
                        Date :
                    </td>
                    <td colspan="2" class="border_top">
                        Date :
                    </td>
                    <td colspan="2" class="border_top">
                        Date :
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="container-journal">
        <table class="payment-detail">
            <thead>
                <tr>
                    <th width="80px" style="border-left: 0px;">Document No.</th>
                    <th width="80px">Account No.</th>
                    <th width="60px">Dept</th>
                    <th>Account Name</th>
                    <th width="100px" class="text-right">Debit</th>
                    <th width="100px" class="text-right">Credit</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $code = '';
            foreach ($qry_journal->result() as $row_journal) {
                if ($code != $row_journal->payment_code) {
                    $wr_code = $row_journal->payment_code;
                    $code = $row_journal->payment_code;
                } else {
                    $wr_code = '';
                }
                echo '<tr>
                        <td class="text-center">' . $wr_code . '</td>
                        <td class="text-center">' . $row_journal->coa_code . '</td>
                        <td class="text-center">' . $row_journal->department_name . '</td>
                        <td>' . $row_journal->coa_desc . '</td>
                        <td class="text-right">' . format_num($row_journal->journal_debit, 0) . '</td>
                        <td class="text-right">' . format_num($row_journal->journal_credit, 0) . '</td>' .
                    '
                    </tr>';
            }
            if ($row->status == STATUS_NEW) {
               foreach ($journal as $row_journal) {
                   $qry_coa = $this->db->get_where('gl_coa', array('coa_id' => $row_journal['coa_id']));
                   $row_coa = $qry_coa->row();

                   if ($code != $row->payment_code) {
                       $wr_code = $row->payment_code;
                       $code = $row->payment_code;
                   } else {
                       $wr_code = '';
                   }

                   echo '<tr>
                        <td class="text-center">' . $wr_code . '</td>
                        <td class="text-center">' . $row_coa->coa_code . '</td>
                        <td class="text-center">&nbsp;</td>
                        <td>' . $row_coa->coa_desc . '</td>
                        <td class="text-right">' . format_num($row_journal['journal_debit'], 0) . '</td>
                        <td class="text-right">' . format_num($row_journal['journal_credit'], 0) . '</td>' .
                       '
                    </tr>';
               }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>