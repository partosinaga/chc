<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Room Attendant Assignment</title>

    <link href="<?php echo base_url(); ?>assets/css/pdf_hsk_report.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<!--script type = "text/php">
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
</script -->

<div class="logo">
    <img src="<?php echo base_url('assets/img/logo_dwijaya.png'); ?>" alt="" width="60px">
</div>
<table width="100%" id="report_header" >
    <tr>
        <td style="vertical-align:top; text-align:center;"><h1><strong>Room Attendant Assignment</strong></h1></td>
    </tr>
    <tr>
        <td style="vertical-align:top; text-align:center;"><h2><?php echo 'Per-' . $report_date; ?></h2></td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div  style="margin-top:10px;" >
            <div>
                <table style="width:100%;" class="table_detail_bordered">
                    <thead>
                    <tr>
                        <th style="width:20px;">ROOM</th>
                        <th style="width:130px;" class="text-left">GUEST</th>
                        <th style="width:130px;" class="text-left">COMPANY</th>
                        <th style="width:70px;" class="text-center">CHECK IN</th>
                        <th style="width:80px;" class="text-center">CHECK OUT</th>
                        <!--th style="width:50px;" class="text-center">STATUS</th -->
                        <th  class="text-left">REMARK</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $max = 42;
                    $total_line = 0;

                    if(isset($detail)){
                        $i = 1;
                        foreach($detail as $data){
                            echo '<tr>
                                        <td class="text-center" style="width:20px;">' . $data['unit_code'] . '</td>
                                        <td style="width:130px;">' . $data['guest_name'] .'</td>
                                        <td style="width:130px;">' . $data['company_name'] .'</td>
                                        <td class="text-center" style="width:70px;">' . (trim($data['arrival_date']) != '' ? date("d/m/y", strtotime($data['arrival_date'])) : '') . '</td>
                                        <td class="text-center" style="width:80px;">' . (trim($data['departure_date']) != '' ? date("d/m/y", strtotime($data['departure_date'])) : '') . '</td>
                                        <td class="text-left" >' . nl2br($data['remark']) . '</td>
                                  </tr>';

                            $i++;

                            $row_desc = wordwrap_string($data['remark'], 90);
                            $total_line = $total_line + $row_desc['line'];
                        }

                        if ($max > $total_line){
                            for($x = $total_line;$x <= $max;$x++){
                                //echo '<tr><td colspan="7">&nbsp;</td></tr>';
                            }
                        }

                        echo '<tr><td colspan="6" style="border-top 1px solid;">&nbsp;</td></tr>';
                    }

                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <!--td colspan="7" style="border-top 1px solid;">&nbsp;</td -->
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- div>
    <table style="margin-top: 30px;">
        <tr ><td style="height: 50px;"></td></tr>
        <tr>
            <td width="65%" class="v_top text-center padding-6">
            </td>
            <td class="v_top text-center padding-6" style="border-bottom: 1px solid #000000; ">

            </td>
        </tr>
        <tr>
            <td width="65%" class="v_top text-center padding-6">
            </td>
            <td class="v_top text-center padding-6">
                <?php echo (my_sess('user_fullname'));?>
            </td>
        </tr>
    </table>
</div -->

<div id="page_footer"><p><?php echo nl2br($profile['company_address']); ?></p></div></body></html>