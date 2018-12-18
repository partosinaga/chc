<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->grn_code;?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_grn.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<script type="text/php">
    if ( isset($pdf) ) {
        $font = Font_Metrics::get_font("helvetica", "");
    }
</script>

<img src="<?php echo FCPATH;?>assets/img/logo_dwijaya.png" style="height: 90px; position: absolute; top: 20px; left:5px;"/>

<table>
    <tr><td colspan="2"><h1><strong>GOODS RECEIVE NOTE</strong></h1></td></tr>
    <tr>
        <td width="80%"></td>
        <td>
            <table width="100%">
                <tr>
                    <td>No</td>
                    <td>:</td>
                    <td><?php echo $row->grn_code;?></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>:</td>
                    <td><?php echo ymd_to_dmy($row->grn_date);?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="container" style="margin-top:10px;">
    <table class="padding-3">
        <tr>
            <td width="65%" class="v_top">
                <table>
                    <tr>
                        <td width="20%">Supplier Name</td>
                        <td width="1%">:</td>
                        <td><?php echo $row->supplier_name;?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td width="40%">Supplier DO No</td>
                        <td width="1%">:</td>
                        <td><?php echo $row->do_no;?></td>
                    </tr>
                    <tr>
                        <td>Vehicle No</td>
                        <td>:</td>
                        <td><?php echo $row->vehicle_no;?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table>
        <tr style="height: 30px;">
            <th width="10%" class="border_no_left padding-5">PO NO</th>
            <th width="3%" class="border padding-5">NO</th>
            <th width="12%" class="border padding-5">ITEM CODE</th>
            <th class="border padding-5">DESCRIPTION</th>
            <th width="7%" class="border padding-5">UOM</th>
            <th width="12%" class="border padding-5">ORDER QTY</th>
            <th width="14%" class="border_no_right padding-5">DELIVERY QTY</th>
        </tr>
        <?php
        $i = 1;
        foreach($qry_det->result() as $row_det){
            echo '<tr style="height: 30px;">
                <td class="border_no_left padding-3 text-center">' . $row->po_code . '</td>
                <td class="border padding-3 text-center">' . $i . '</td>
                <td class="border padding-3 text-center">' . $row_det->item_code . '</td>
                <td class="border padding-3">' . $row_det->item_desc . '</td>
                <td class="border padding-3 text-center">' . $row_det->uom_code . '</td>
                <td class="border padding-3 text-right">' . $row_det->item_qty . '</td>
                <td class="border_no_right padding-3 text-right">' . $row_det->item_delivery_qty . '</td>
            </tr>';
            $i++;
        }
        ?>
    </table>
    <table>
        <tr>
            <td>
                <div style="height:80px;" class="padding-3">
                    Remarks:<br/>
                    <?php echo $row->remarks;?>
                </div>
            </td>
        </tr>
    </table>
    <table class="border_top">
        <tr>
            <td class="border_right" width="30%">&nbsp;</td>
            <td class="text-center padding-3">
                Approved By
            </td>
        </tr>
        <tr>
            <td class="border_right text-center">
                Receive By<br/><br/><br/><br/><br/><br/><br/><br/>
                <?php echo $user_posted;?><hr/>
                Storekeeper<br/>&nbsp;
            </td>
            <td>
                <table>
                    <tr>
                        <td width="50%" class="text-center">
                            Purchasing,<br/><br/><br/><br/><br/><br/><br/><br/>
                            <?php echo ($po_user != '' ? $po_user : '&nbsp;');?><hr/>
                            Date: <?php echo $po_date;?><br/>&nbsp;
                        </td>
                        <td class="text-center">
                            User,<br/><br/><br/><br/><br/><br/><br/><br/>
                            <?php echo ($pr_user != '' ? $pr_user : '&nbsp;');?><hr/>
                            (For Fixed Asset / Non Stock Items)<br/>
                            Date: <?php echo $pr_date;?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>