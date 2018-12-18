<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->adj_code;?></title>
    <link href="<?php echo FCPATH; ?>assets/css/pdf_adj.css" rel="stylesheet" type="text/css"/>
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
        <td><h1><strong>STOCK ADJUSTMENT</strong></h1></td>
        <td width="25%">
            <table style="margin-left: 45px;margin-top: 33px;">
                <tr>
                    <td>No</td>
                    <td>:</td>
                    <td><?php echo $row->adj_code;?></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>:</td>
                    <td><?php echo ymd_to_dmy($row->adj_date);?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="container">
    <div id="container-detail">
        <table class="payment-detail">
            <thead>
                <tr>
                    <th width="10px" rowspan="2" class="text-center">NO</th>
                    <th width="70px" rowspan="2" class="text-center">STOCK CODE</th>
                    <th width="150px" rowspan="2" class="text-center">DESCRIPTION</th>
                    <th width="30px" rowspan="2" class="text-center">UNIT</th>
                    <th colspan="3" class="text-center">BEFORE</th>
                    <th colspan="3" class="text-center">AFTER</th>
                </tr>
                <tr>
                    <th>QTY</th>
                    <th>PRICE</th>
                    <th>TOTAL</th>
                    <th>QTY</th>
                    <th>PRICE</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            foreach ($qry_det->result() as $row_det) {
                echo '<tr>
                      <td class="text-center">' . $i . '.</td>
                      <td class="text-center">' . $row_det->item_code . '</td>
                      <td>' . $row_det->item_desc. '</td>
                      <td class="text-center">' . $row_det->uom_code . '</td>
                      <td class="text-right">' . format_num($row_det->qty, 2). '</td>
                      <td class="text-right">' . format_num($row_det->price, 2). '</td>
                      <td class="text-right">' . format_num(($row_det->qty * $row_det->price), 2). '</td>
                      <td class="text-right">' . format_num($row_det->adj_qty, 2). '</td>
                      <td class="text-right">' . format_num($row_det->adj_price, 2). '</td>
                      <td class="text-right">' . format_num(($row_det->adj_qty * $row_det->adj_price), 2). '</td>
                    </tr>';
                $i++;
            }
            ?>
            </tbody>
        </table>
        <table style="margin-top: 20px;">
            <tr>
                <td width="30%" class="text-center v_top" style="height: 100px;">Adjusted by :</td>
                <td width="40%" class="text-center"></td>
                <td width="30%" class="text-center v_top">Approved by :</td>
            </tr>
            <tr>
                <td class="text-center"><?php echo $row->created_name;?></td>
                <td></td>
                <td class="text-center"><?php echo $row->posted_name;?></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>