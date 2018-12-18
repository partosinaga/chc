<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->gi_code;?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_issue.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<script type="text/php">
    if ( isset($pdf) ) {
        $font = Font_Metrics::get_font("helvetica", "");
    }
</script>

<img src="<?php echo FCPATH;?>assets/img/logo_dwijaya.png" style="height: 90px; position: absolute; top: -30px; left:5px;"/>

<table>
    <tr><td colspan="2"><h1><strong>STOCK ISSUE</strong></h1></td></tr>
</table>
<table style="margin-bottom: 5px;">
    <tr>
        <td width="280px">
            <table>
                <tr>
                    <td width="50px">DEPT</td>
                    <td width="10px">:</td>
                    <td><?php echo $row->department_name;?></td>
                </tr>
                <tr>
                    <td>REQ NO</td>
                    <td>:</td>
                    <td><?php echo $row->request_code;?></td>
                </tr>
            </table>
        </td>
        <td width="250px">
            <table>
                <tr>
                    <td width="50px">WO NO</td>
                    <td width="10px">:</td>
                    <td></td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td width="50px">NO</td>
                    <td width="10px">:</td>
                    <td><?php echo $row->gi_code;?></td>
                </tr>
                <tr>
                    <td>DATE</td>
                    <td>:</td>
                    <td><?php echo ymd_to_dmy($row->gi_date);?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="container" style="margin-top:10px;">
    <table>
        <tr style="height: 30px;">
            <th width="20px" class="border_no_left padding-5">NO</th>
            <th width="100px" class="border padding-5">STOCK CODE</th>
            <th class="border padding-5">DESCRIPTION</th>
            <th width="50px" class="border padding-5">UOM</th>
            <th width="50px" class="border_no_right padding-5">QTY</th>
        </tr>
        <?php
        $i = 1;
        foreach($qry_det->result() as $row_det){
            echo '<tr style="height: 30px;">
                <td class="border_no_left padding-3 text-center">' . $i . '</td>
                <td class="border padding-3 text-center">' . $row_det->item_code . '</td>
                <td class="border padding-3">' . $row_det->item_desc . '</td>
                <td class="border padding-3 text-center">' . $row_det->uom_code . '</td>
                <td class="border_no_right padding-3 text-right">' . $row_det->item_qty . '</td>
            </tr>';
            $i++;
        }
        ?>
    </table>
    <table>
        <tr>
            <td class="text-center padding-3" width="30%" style="padding-top: 20px;">
                ISSUE TO :
            </td>
            <td style="padding-top: 20px;">&nbsp;</td>
            <td class="text-center padding-3" style="padding-top: 20px;">
                WAREHOUSE :
            </td>
        </tr>
        <tr>
            <td colspan="3" style="height: 100px;"></td>
        </tr>
    </table>
</div>
</body>
</html>