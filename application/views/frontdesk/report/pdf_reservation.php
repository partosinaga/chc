<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reservation  Report</title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_inhouse.css" rel="stylesheet" type="text/css"/>
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
        <td style="vertical-align:top; text-align:center;"><h1><strong>Reservation  Report</strong></h1></td>
    </tr>
    <tr>
        <td style="vertical-align:top; text-align:center;"><h2>Per&nbsp;<?php echo ($date_from); ?></h2></td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:10px;" >
            <div>
                <table style="width:100%;" class="table_detail">
                    <thead>
                    <tr>
                        <th style="width:5px;">NO</th>
                        <th style="width:20px;">FOLIO</th>
                        <th class="text-left">GUEST</th>
                        <th style="width:180px;" class="text-left">COMPANY</th>
                        <th style="width:70px;" class="text-center">TYPE</th>
                        <th style="width:70px;" class="text-center">ROOM</th>
                        <th style="width:70px;" class="text-center">UNIT TYPE</th>
                        <th style="width:50px;" class="text-center">ARRIVAL</th> 
                        <!-- th style="width:60px;" class="text-center">BALANCE</th -->
                        <!--th class="text-left">REMARK</th -->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $max = 30;
                    $total_line = 0;

                    $list_room = array();
                    $total_balance = 0;
                    if(isset($detail)){
                        $i = 1;
                        foreach($detail as $data){
							 if($data['guest_type']==0 ) 
								{$arival = dmy_from_db($data['arrival_date']);}
								else 
								{
									$arival = date('F', strtotime(ymd_from_db($data['arrival_date']))) ;
								}
                            echo '<tr>
                                        <td class="text-center" style="width:5px;">' . $i .'</td>
                                        <td class="text-center" style="width:20px;">' . $data['reservation_code'] .'</td>
                                        <td >' . $data['tenant_fullname'] .'</td>
                                        <td style="width:180px;">' . $data['company_name'] .'</td>
                                        <td class="text-center" style="width:60px;">' . RES_TYPE::caption($data['reservation_type']) .'</td>
                                        <td class="text-center" style="width:60px;">' . $data['room'] .'</td>
                                        <td class="text-center" style="width:60px;">' . $data['unittype_bedroom'] .'</td>
                                        <td class="text-center" style="width:50px;">' . $arival . '</td> 
                                  </tr>';

                            $i++;

                            $row_desc = wordwrap_string($data['tenant_fullname'], 90);
                            $total_line = $total_line + $row_desc['line'];
 

                            $rooms = explode(',', $data['room']);
                            foreach($rooms as $room){
                                if(!in_array($room,$list_room)){
                                    array_push($list_room,$room);
                                }
                            }
                        }

                        if ($max > $total_line){
                            for($x = $total_line;$x <= $max;$x++){
                                //echo '<tr><td colspan="7">&nbsp;</td></tr>';
                            }
                        }
                    }

                    ?>
                    </tbody>
                    <!--tfoot>
                        <tr class="total">
                            <td class="border-top text-right" colspan="5" style="border-top: 1px solid;border-bottom: 1px solid;">TOTAL ROOM&nbsp;&nbsp;<?php echo count($list_room); ?></td>
                            <td class="border-top text-right" colspan="3" style="border-top: 1px solid;border-bottom: 1px solid;">TOTAL (in IDR)</td>
                            <td class="border-top text-right" style="border-top: 1px solid;border-bottom: 1px solid; <?php echo ($total_balance < 0 ? ' padding-right:5px;' : ''); ?>"><?php echo amount_journal($total_balance) ?></td>
                        </tr>
                    </tfoot -->
                </table>
            </div>
        </div>
    </div>
</div>

<div id="page_footer"><p><?php echo nl2br($profile['company_address']); ?></p></div></body></html>