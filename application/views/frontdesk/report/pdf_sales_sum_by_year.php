<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Daily Cash Report</title>

    <link href="<?php echo FCPATH; ?>/assets/css/pdf_frontend.css" rel="stylesheet" type="text/css"/>
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

<!--div class="logo">
    <img src="<?php echo FCPATH; ?>assets/img/logo_dwijaya.png" alt="" width="60px">
</div -->
<div id="header">
    <table style="width:100%;">
        <tr>
            <td class="text-center" style="padding-top: 10px;font-size: 13pt;"><strong>SALES SUMMARY</strong><br>
                <span style="font-size: 10pt;"><?php echo $period_year ; ?></span>
            </td>
        </tr>
    </table>
</div>

<div id="content">
    <div id="body" >
        <div id="container" style="margin-top:5px;" >
            <div>
                <table style="width:100%;" class="table_mini">
                    <thead>
                    <tr>
                        <th class="text-left" width="150px">AGENT</th>
                        <th class="text-left" width="150px">PIC Name</th>
                        <th class="text-center" width="60px" colspan="2">JAN</th>
                        <th class="text-center" width="60px" colspan="2">FEB</th>
                        <th class="text-center" width="60px" colspan="2">MAR</th>
                        <th class="text-center" width="60px" colspan="2">APR</th>
                        <th class="text-center" width="60px" colspan="2">MAY</th>
                        <th class="text-center" width="60px" colspan="2">JUN</th>
                        <th class="text-center" width="60px" colspan="2">JUL</th>
                        <th class="text-center" width="60px" colspan="2">AUG</th>
                        <th class="text-center" width="60px" colspan="2">SEP</th>
                        <th class="text-center" width="60px" colspan="2">OCT</th>
                        <th class="text-center" width="60px" colspan="2">NOV</th>
                        <th class="text-center" width="60px" colspan="2">DEC</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $max = 30;
                    $total_line = 0;

                    $total_amount = array(0,0,0,0,0,0,0,0,0,0,0,0);
                    $total_unit = array(0,0,0,0,0,0,0,0,0,0,0,0);
                    if(isset($detail)){
                        $i = 1;
                        foreach($detail as $data){
                            $out = '<tr>
                                        <td class="text-left" width="150px">' . (trim($data['agent_name']) != '' ? $data['agent_name'] : '- No Agent -' ) . '</td>
                                        <td class="text-left" width="150px">' . $data['agent_pic'] . '</td>';
                            for($m=0;$m<12;$m++){
                                $_month = sprintf('%02d',$m+1);

                                $total_amount[$m] += $data[$_month];
                                $total_unit[$m] += $data[$_month . '_unit'];

                                $out .= '<td class="text-right" style="width:5px;">' . format_num($data[$_month . '_unit'],0) . '</td>';
                                $out .= '<td class="text-right" style="width:55px;">' . format_num($data[$_month],0) . '</td>';
                            }

                            $out .= '</tr>';

                            echo $out;

                            $i++;

                            $row_desc = wordwrap_string($data['agent_name'], 90);
                            $total_line = $total_line + $row_desc['line'];
                        }

                        if ($max > $total_line){
                            for($x = $total_line;$x <= $max;$x++){
                                //echo '<tr><td colspan="6">&nbsp;</td></tr>';
                            }
                        }
                    }

                    ?>
                    </tbody>
                    <tfoot>
                        <tr class="total">
                            <td class="border-top border-bottom text-right" colspan="2">TOTAL (IDR)</td>
                            <?php for($m=0;$m<12;$m++){
                                echo '<td class="border-top border-bottom text-right">' . format_num($total_unit[$m],0) . '</td>
                                <td class="border-top border-bottom text-right">' . format_num($total_amount[$m],0) . '</td>';
                            } ?>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>

</div>
<div id="page_footer"><p><?php //echo nl2br($profile['company_address']); ?></p></div></body></html>