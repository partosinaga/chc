<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->return_code;?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_return.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<script type="text/php">
    if ( isset($pdf) ) {
        $font = Font_Metrics::get_font("helvetica", "");
    }
</script>

<img src="<?php echo FCPATH;?>assets/img/logo_dwijaya.png" style="height: 90px; position: absolute; top: -30px; left:5px;"/>

<table>
    <tr><td colspan="9"><h1><strong>GOODS RETURN NOTE</strong></h1></td></tr>
    <tr>
        <td width="10%">SUPPLIER</td>
        <td width="1%">:</td>
        <td width="28%"><?php echo $row->supplier_name;?></td>
        <td width="8%">GRN NO</td>
        <td width="1%">:</td>
        <td width="25%"><?php echo $row->grn_code;?></td>
        <td width="7%">NO</td>
        <td width="1%">:</td>
        <td><?php echo $row->return_code;?></td>
    </tr>
    <tr>
        <td>PO NO</td>
        <td>:</td>
        <td><?php echo $row->po_code;?></td>
        <td>DO NO</td>
        <td>:</td>
        <td><?php echo $row->do_no;?></td>
        <td>DATE</td>
        <td>:</td>
        <td><?php echo ymd_to_dmy($row->return_date);?></td>
    </tr>
</table>

<table style="margin-top: 10px;">
    <tr>
        <th width="3%" class="border padding-5">NO</th>
        <th width="13%" class="border padding-5">ITEM CODE</th>
        <th class="border padding-5">DESCRIPTION</th>
        <th width="7%" class="border padding-5">UOM</th>
        <th width="14%" class="border padding-5">DELIVERY QTY</th>
        <th width="14%" class="border padding-5">RETURN QTY</th>
    </tr>
    <?php
    $i = 1;
    foreach($qry_det->result() as $row_det){
        echo '<tr>
            <td class="border padding-3 text-center">' . $i . '</td>
            <td class="border padding-3 text-center">' . $row_det->item_code . '</td>
            <td class="border padding-3">' . $row_det->item_desc . '</td>
            <td class="border padding-3 text-center">' . $row_det->uom_code . '</td>
            <td class="border padding-3 text-right">' . $row_det->item_delivery_qty . '</td>
            <td class="border padding-3 text-right">' . $row_det->return_qty . '</td>
        </tr>';
        $i++;
    }
    ?>

</table>
<table>
    <tr>
        <td class="border_no_top">
            <div style="height:80px;" class="padding-3">
                Remarks:<br/>
                <?php echo $row->remarks;?>
            </div>
        </td>
    </tr>
</table>
<table style="margin-top: 30px;">
    <tr>
        <td width="33%" class="text-center">RETURN BY :</td>
        <td width="33%" class="text-center">PURCHASING :</td>
        <td width="33%" class="text-center">SUPPLIER :</td>
    </tr>
</table>
</body>
</html>