<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->pr_code;?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_pr.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<script type = "text/php">
    if ( isset($pdf) ) {
        $pdf->page_script('
            if ($PAGE_COUNT > 1) {
                $font = Font_Metrics::get_font("Arial, Helvetica, sans-serif", "normal");
                $size = 7;
                $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $y = $pdf->get_height() - 24;
                $x = $pdf->get_width() - 35 - Font_Metrics::get_text_width($pageText, $font, $size);
                $pdf->text($x, $y, $pageText, $font, $size);
            }
        ');
    }
</script>

<table>
    <!--tr><td colspan="2"><h1><strong>PURCHASE ORDER</strong></h1></td></tr-->
    <tr>
        <td width="35%" style="vertical-align:top; text-align:left;">
            <img src="<?php echo FCPATH;?>assets/img/logo_dwijaya.png" style="height: 90px; position: absolute; top: -20px;" />
        </td>
        <td style="text-align:center;vertical-align:bottom;font-size:12px;"><strong>PURCHASE REQUISITION</strong></td>
        <td width="35%">
            <table width="100%">
                <tr>
					<td width="25%"></td>
                    <td>No</td>
                    <td width="1%">:</td>
                    <td width="55%"><?php echo $row->pr_code;?></td>
                </tr>
                <tr>
					<td></td>
                    <td>Date</td>
                    <td>:</td>
                    <td><?php echo ymd_to_dmy($row->date_prepare);?></td>
                </tr>
                <tr>
					<td></td>
                    <td>Dept.</td>
                    <td>:</td>
                    <td><?php echo $row->department_name;?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:10px;" >
            <div>
                <table style="width:100%;" class="table_detail">
                    <tr>
                        <th style="width:4%;padding:5px 3px;" class="text-center border_right border_bottom">NO</th>
                        <th style="padding:5px 3px;" class="text-center border_right border_bottom">DESCRIPTION</th>
                        <th style="width:9%;padding:5px 3px;" class="text-center border_right border_bottom">UOM</th>
                        <th style="width:8%;padding:5px 3px;" class="text-center border_right border_bottom">QTY</th>
                        <th style="width:28%;padding:5px 3px;border-right:0px;" class="text-center border_right border_bottom">SUPPLIER</th>
                    </tr>
                    <?php
                    $max = 10;
                    $i = 1;
                    if($qry_det->num_rows() > 0){
                        foreach($qry_det->result() as $row_det){
                            echo '<tr>
                                    <td class="text-right" style="width:4%;padding-right: 8px;border-left:0px;">' . $i . '.</td>
                                    <td class="text-left">' . $row_det->item_desc . '</td>
                                    <td class="text-center" style="width:7%;">' . $row_det->uom_code . '</td>
                                    <td class="text-right" style="width:6%;padding-right:8px;border-left:0px;">' . number_format($row_det->item_qty, 0, ',', '.') . '</td>
                                    <td class="text-right" style="width:13%;padding-right:8px;border-left:0px;border-right:0px;">' . $row_det->supplier_name . '</td>
                                </tr>';
                            $i++;
                        }
                    }

                    for($x = $i;$x < $max;$x++){
                        echo '<tr>
                                <td style="border-left:0px;">&nbsp;</td>
                                <td >&nbsp;</td>
                                <td>&nbsp;</td>
                                <td style="border-left:0px;">&nbsp;</td>
                                <td style="border-left:0px;border-right:0px;">&nbsp;</td>
                            </tr>';
                    }
                    ?>
                </table>
            </div>
        </div>
        <div style="margin-top:20px;page-break-inside:avoid;">
            <table>
                <tr>
                    <td width="35%">
                        <table style="border:1px solid #000000;">
                            <tr>
                                <td style="text-align:center;padding:7px;">Requested By</td>
                            </tr>
                            <tr><td><br/><br/><br/><br/></td></tr>
                            <tr>
                                <td style="text-align:center;padding:7px;"><?php echo $row->user_fullname;?></td>
                            </tr>
                        </table>
                    </td>
                    <td></td>
                    <td width="35%">
                        <table style="border:1px solid #000000;">
                            <tr>
                                <td style="text-align:center;padding:7px;">Approved By</td>
                            </tr>
                            <tr><td><br/><br/><br/><br/></td></tr>
                            <tr>
                                <td style="text-align:center;padding:7px;">
                                    <?php
                                    $app_by = '&nbsp;';
                                    if($row->user_approved > 0){
                                        $qry_app = $this->db->get_where('ms_user', array('user_id' => $row->user_approved));
                                        $row_app = $qry_app->row();
                                        $app_by = $row_app->user_fullname;
                                    }
                                    echo $app_by;
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

</body>
</html>