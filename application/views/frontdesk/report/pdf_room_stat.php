<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Statistic Report</title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_frontend.css" rel="stylesheet" type="text/css"/>
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

<div class="logo">
    <img src="<?php echo FCPATH; ?>assets/img/logo_dwijaya.png" alt="" width="60px">
</div>
<table width="100%" id="report_header" >
    <tr>
        <td style="vertical-align:top; text-align:center;"><h1><strong>Hotel Statistics</strong></h1></td>
    </tr>
    <tr>
        <td style="vertical-align:top; text-align:center;"><h2><?php echo $todate; ?></h2></td>
    </tr>
</table>

<div id="content">
    <div id="body_v1">
        <div id="container" style="margin-top:10px;" >
            <div>
                <table style="width:100%;" class="table_detail">
                    <thead>
                    <tr>
                        <th class="text-left">&nbsp;</th>
                        <th style="width:100px;" class="text-center"><?php echo (isset($today_header) ? $today_header : 'TODAY')?></th>
                        <th style="width:100px;" class="text-center">MONTH TO DATE</th>
                        <th style="width:100px;" class="text-center">YEAR TO DATE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        if(isset($col_today) && isset($col_month) && isset($col_year)){
                    ?>
                    <tr>
                        <td style="padding-top: 10px;">TOTAL ROOMS</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['total_room']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['total_room']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['total_room']); ?></td>
                    </tr>
                    <tr>
                        <td >RENTABLE ROOMS (IS Only)</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['hsk_is']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['hsk_is']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['hsk_is']); ?></td>
                    </tr>
                    <tr>
                        <td >OUT OF ORDER (OO Only)</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['hsk_oo']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['hsk_oo']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['hsk_oo']); ?></td>
                    </tr>
                    <tr>
                        <td >AVAILABLE ROOMS</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['ready_room']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['ready_room']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['ready_room']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">OCCUPIED ROOMS</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['res_occupied']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['res_occupied']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['res_occupied']); ?></td>
                    </tr>
                    <tr>
                        <td >HOUSE USE ROOMS</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['res_house_use']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['res_house_use']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['res_house_use']); ?></td>
                    </tr>
                    <tr>
                        <td >COMPLIMENT ROOMS</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['res_compliment']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['res_compliment']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['res_compliment']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">HOTEL OCCUPANCY % + OO</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['percent_oo']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['percent_oo']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['percent_oo']); ?></td>
                    </tr>
                    <tr>
                        <td >HOTEL OCCUPANCY % - OO</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['percent_not_oo']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['percent_not_oo']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['percent_not_oo']); ?></td>
                    </tr>
                    <tr>
                        <td >HOTEL OCCUPANCY % + HU</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['percent_hu']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['percent_hu']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['percent_hu']); ?></td>
                    </tr>
                    <tr>
                        <td >HOTEL OCCUPANCY % - HU - COMPL</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['percent_hu_comp']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['percent_hu_comp']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['percent_hu_comp']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">AVERAGE RATE / ROOM</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['avg_sales_room']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['avg_sales_room']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['avg_sales_room']); ?></td>
                    </tr>
                    <tr>
                        <td >AVERAGE REVENUE / ROOM</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['avg_sales_total']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['avg_sales_total']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['avg_sales_total']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px;">NUMBER OF GUEST</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['num_of_guest']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['num_of_guest']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['num_of_guest']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">RESERVATION</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['folio_reserve']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['folio_reserve']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['folio_reserve']); ?></td>
                    </tr>
                    <tr>
                        <td >WALK IN</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['folio_walk_in']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['folio_walk_in']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['folio_walk_in']); ?></td>
                    </tr>
                    <tr>
                        <td >NO SHOW / CANCEL</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['folio_cancel']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['folio_cancel']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['folio_cancel']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 15px;">ROOM REVENUE</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['revenue_room']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['revenue_room']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['revenue_room']); ?></td>
                    </tr>
                    <tr>
                        <td >OTHER CHARGE REVENUE</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['revenue_other']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['revenue_other']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['revenue_other']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px;">TOTAL REVENUE</td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_today['total_revenue']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_month['total_revenue']); ?></td>
                        <td style="width:100px;" class="text-center"><?php echo amount_journal($col_year['total_revenue']); ?></td>
                    </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="border-top: 1px solid;">&nbsp;</td>
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