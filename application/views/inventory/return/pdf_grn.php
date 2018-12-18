<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->grn_code;?></title>

    <link href="<?php echo base_url(); ?>assets/css/pdf_grn.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<script type="text/php">
    if ( isset($pdf) ) {
        $font = Font_Metrics::get_font("helvetica", "");
    }
</script>

<img class="logo" src="<?php echo base_url();?>assets/img/tpd_small.jpg"/>

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
        <tr>
            <th width="10%" class="border_no_left padding-5">PO NO</th>
            <th width="3%" class="border padding-5">NO</th>
            <th width="10%" class="border padding-5">ITEM CODE</th>
            <th class="border padding-5">DESCRIPTION</th>
            <th width="7%" class="border padding-5">UOM</th>
            <th width="12%" class="border padding-5">ORDER QTY</th>
            <th width="14%" class="border_no_right padding-5">DELIVERY QTY</th>
        </tr>
        <?php
        $i = 1;
        foreach($qry_det->result() as $row_det){
            echo '<tr>
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
                            <?php echo $po_user;?><hr/>
                            Date: <?php echo $po_date;?><br/>&nbsp;
                        </td>
                        <td class="text-center">
                            User,<br/><br/><br/><br/><br/><br/><br/><br/>
                            <?php echo $pr_user;?><hr/>
                            (For Fixed Asset / Non Stock Items)<br/>
                            Date: <?php echo $pr_date;?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<!--div id="container">
    <div id="header">
        <h1><strong>GOODS RECEIVE NOTE</strong></h1>
    </div>
    <div id="content">
        <div id="body">
            <table style="width:100%;" class="table_head">
                <tr>
                    <td style="width:80px;"><strong>Journal No.</strong></td>
                    <td style="width:3px;">:</td>
                    <td style="width:55px;" class="border_bottom"><?php //echo $row->journal_no;?></td>
                    <td style="width:200px;">&nbsp;</td>
                    <td style="width:80px;"><strong>Reference</strong></td>
                    <td style="width:3px;">:</td>
                    <td style="width:150px;" class="border_bottom"><?php //echo ($row->reference == '') ? '&nbsp;' : $row->reference;?></td>
                </tr>
                <tr>
                    <td><strong>Date</strong></td>
                    <td style="width:3px;">:</td>
                    <td class="border_bottom"><?php //echo ymd_to_dmy($row->journal_date);?></td>
                    <td>&nbsp;</td>
                    <td><strong>Reff. Date</strong></td>
                    <td style="width:3px;">:</td>
                    <td class="border_bottom"><strong></strong></td>
                </tr>
                <tr>
                    <td><strong>Description</strong></td>
                    <td style="width:3px;">:</td>
                    <td colspan="5" class="border_bottom"><?php //echo nl2br($row->journal_remarks, true) ;?></td>
                </tr>
            </table>
            <br/><br/><br/>
            <div >
                <table style="width:100%;" class="table_detail">
                    <tr>
                        <th style="width:13%;">AC. CODE</th>
                        <th style="width:47%;" class="text-left">DESCRIPTION</th>
                        <th style="width:10%;">DEPT</th>
                        <th style="width:15%;">Dr (IDR)</th>
                        <th style="width:15%;">Cr (IDR)</th>
                    </tr>
                    <?php
                    /*$max = 25;
                    $i = 0;
                    if($qry_det->num_rows() > 0){
                        foreach($qry_det->result() as $row_det){
                            echo '<tr>
										<td class="text-center">' . $row_det->coa_code . '</td>
										<td >' . nl2br($row_det->coa_desc, true) . '</td>
										<td class="text-center">' . $row_det->dept_code . '</td>
										<td class="text-right" style="padding-right:5px;">' . number_format($row_det->journal_debit,0,',','.') . '</td>
										<td class="text-right" style="padding-right:5px;">' . number_format($row_det->journal_credit,0,',','.') . '</td>
									</tr>';
                            $i++;
                        }
                    }

                    for($x = $i;$x < $max;$x++){
                        echo '<tr>
										<td >&nbsp;</td>
										<td >&nbsp;</td>
										<td >&nbsp;</td>
										<td >&nbsp;</td>
										<td >&nbsp;</td>
									</tr>';
                    }*/
                    ?>

                </table>
            </div>
            <table style="width:100%" class="table_detail">
                <tr >
                    <td width="50%" class="text-right" style="border-right: 0px;border-top: 1px solid #000000;border-bottom: 1px solid #000000;"><strong>Total Debits</strong></td>
                    <td width="15%" class="text-right" style="border-right: 0px; border-left: 0px;border-top: 1px solid #000000;border-bottom: 1px solid #000000;"> <?php //echo number_format($row->journal_amount,0,',','.'); ?></td>
                    <td width="20%" class="text-right" style="border-right: 0px; 0px; border-left: 0px;border-top: 1px solid #000000;border-bottom: 1px solid #000000;"><strong>Total Credits</strong></td>
                    <td width="15%" class="text-right" style="border-left: 0px;border-top: 1px solid #000000;border-bottom: 1px solid #000000;"> <?php //echo number_format($row->journal_amount,0,',','.'); ?></td>
                </tr>
            </table>
        </div>
        <div id="footer" style="margin-top:20px;">
            <table style="width:100%"  >
                <tr>
                    <td width="50%" style="padding:5px 10px;border-bottom:0px;" class="bordered text-center"><strong>Prepared By :</strong></td>
                    <td width="50%" style="padding:5px 10px;border-bottom:0px;" class="bordered text-center"><strong>Approved By :</strong></td>
                </tr>
                <tr>
                    <td class="bordered" style="padding:0px 10px;height: 150px;border-top:0px;"></td>
                    <td class="bordered" style="padding:0px 10px;height: 150px;border-top:0px;"></td>
                </tr>

            </table>
        </div>
    </div>
</div-->
</body>
</html>