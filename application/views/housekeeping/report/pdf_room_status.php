<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Room Status Report</title>

    <link href="<?php echo base_url(); ?>assets/css/pdf_hsk_report.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<!-- script type = "text/php">
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
        <td style="vertical-align:top; text-align:center;"><h1><strong>Room Status Report</strong></h1></td>
    </tr>
    <tr>
        <td style="vertical-align:top; text-align:center;"><h2></h2></td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:10px;" >
            <div>
                <table>
                    <tr>
                        <td width="70%"></td>
                        <td class="pull-right" style="text-align: right"><i style="font-size: 8pt;">Print on&nbsp;<?php echo $report_date; ?></i></td>
                    </tr>
                </table>
                <table style="width:100%;" class="table_detail_bordered">
                    <thead>
                    <tr>
                        <th style="width:70px;" class="text-center">ROOM</th>
                        <th style="width:70px;" class="text-center">STATUS</th>
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
                                        <td class="text-center" style="width:70px;">' . $data['unit_code'] . '</td>
                                        <td style="width:70px;" class="text-center">' . $data['hsk_status'] .'</td>
                                        <td class="text-left" >' . $data['srf_note'] . '</td>
                                  </tr>';

                            $i++;

                            $row_desc = wordwrap_string($data['srf_note'], 90);
                            $total_line = $total_line + $row_desc['line'];
                        }

                        if ($max > $total_line){
                            for($x = $total_line;$x <= $max;$x++){
                                //echo '<tr><td colspan="7">&nbsp;</td></tr>';
                            }
                        }

                        echo '<tr><td colspan="3" style="border-top 1px solid;">
                                <div id="legend">
                                <table class="table_legend">
                                <thead>
                                    <tr >
                                        <th colspan="4" style="padding-top: 8px;"> <strong>Room Status :</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td width="25%">[IS]&nbsp;<i>Inspected </i></td>
                                    <td width="25%">[ISC]&nbsp;<i>Inspected - Check In</i></td>
                                    <td width="25%">[OD]&nbsp;<i >Occupied Dirty</i></td>
                                    <td width="25%">[OC]&nbsp;<i >Occupied Clean</i></td>
                                </tr>
                                <tr>
                                    <td>[VD]&nbsp;<i >Vacant Dirty</i></td>
                                    <td>[VC]&nbsp;<i >Vacant Clean</i></td>
                                    <td>[ED]&nbsp;<i >Expected Departure</i></td>
                                    <td>[ED/EA]&nbsp;<i >Expected Departure/Expected Arrival</i></td>
                                </tr>
                                <tr>
                                    <td>[VD/EA]&nbsp;<i >Vacant Dirty/Expected Arrival</i></td>
                                    <td>[VC/EA]&nbsp;<i >Vacant Clean/Expected Arrival</i></td>
                                    <td>[IS/EA]&nbsp;<i >Inspected/Expected Arrival</i></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><span class="font-red-sunglo">[OS]&nbsp;<i>Out Of Service</i></span></td>
                                    <td><span class="font-red-sunglo">[OO]&nbsp;<i>Out Of Order</i></span></td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table></div>
                            </td></tr>';
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
    <table >
        <tr>
            <td width="25%">[IS]&nbsp;<i>Inspected </i></td>
            <td width="25%">[ISC]&nbsp;<i>Inspected - Check In</i></td>
            <td width="25%">[OD]&nbsp;<i >Occupied Dirty</i></td>
            <td width="25%">[OC]&nbsp;<i >Occupied Clean</i></td>
        </tr>
        <tr>
            <td>[VD]&nbsp;<i >Vacant Dirty</i></td>
            <td>[VC]&nbsp;<i >Vacant Clean</i></td>
            <td>[ED]&nbsp;<i >Expected Departure</i></td>
            <td>[ED/EA]&nbsp;<i >Expected Departure/Expected Arrival</i></td>
        </tr>
        <tr>
            <td>[VD/EA]&nbsp;<i >Vacant Dirty/Expected Arrival</i></td>
            <td>[VC/EA]&nbsp;<i >Vacant Clean/Expected Arrival</i></td>
            <td>[IS/EA]&nbsp;<i >Inspected/Expected Arrival</i></td>
            <td></td>
        </tr>
        <tr>
            <td><span class="font-red-sunglo">[OS]&nbsp;<i>Out Of Service</i></span></td>
            <td><span class="font-red-sunglo">[OO]&nbsp;<i>Out Of Order</i></span></td>
            <td>&nbsp;</td>
            <td></td>
        </tr>
    </table>
</div -->
<!-- div>
    <table style="margin-top: 5px;">
        <tr ><td style="height: 30px;"></td></tr>
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