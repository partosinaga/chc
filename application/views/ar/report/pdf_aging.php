<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DEBTOR AGING SUMMARY</title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_frontend_invoice.css" rel="stylesheet" type="text/css"/>
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

<table width="100%"> 
    <tr> 
        <td  style="vertical-align:top; text-align:center;">
            <table style="padding-bottom: 6px; text-align:center;" width="100%"  >
                <tr>
                    <td ><strong>DEBTOR AGING SUMMARY (IDR)</strong></td>
                </tr>
				 <tr>
                    <td >Period of <?php echo $date_aging; ?></td>
                </tr>
            </table>
             
        </td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
     
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:10px;" >
            <div>
                <table style="width:100%;" class="table_detail">
				<thead>
                    <tr>
                        <th width="5px">No</th>
                        <th width="25px" nowrap class="text-left">NAME</th>
                        <th width="30px" class="text-right"> &lt; 31</th>
                        <th width="30px" class="text-right">31 - 60</th>
                        <th width="30px" class="text-right">61 - 90</th>
                        <th width="30px" class="text-right"> > 90 </th> 
						<th width="30px" class="text-right"> Total</th>
                    </tr>
					</thead>
					<tbody>
                    <?php
                    $total = 0;
                    $grandtotal = 0;
                    $total_D0 = 0;
					$total_D31  = 0;
					$total_D61  = 0;
					$total_D91 = 0; 
                    if(isset($aging)){
                        $i = 1; 
						foreach($aging as $data){
							$total = $data['D0']+$data['D31']+$data['D61']+$data['D91'];
                        
                            echo '<tr>
                                <td width="5px"  class="text-right">' . $i . '</td>
                                <td width="25px" class="text-left" nowrap>' . $data['company_name'] . '</td>
                                <td width="30px"  class="text-right">' . amount_journal($data['D0']) . '</td>
                                <td width="30px"  class="text-right">' . amount_journal($data['D31']) . '</td>
                                <td width="30px"  class="text-right">' . amount_journal($data['D61']) . '</td>
                                <td width="30px"  class="text-right">' . amount_journal($data['D91']) . '</td>
								 <td width="30px"  class="text-right">' . amount_journal($total) . '</td> 
                            </tr>';
                            $total_D0 += $data['D0'];
                            $total_D31 += $data['D31'];
                            $total_D61 += $data['D61'];
                            $total_D91 += $data['D91']; 
							$grandtotal += $total;

                            $i++;
                        }
                    }
                    ?>
                    <tr class="total">
                        <td colspan="2" class="border-top text-right">&nbsp;</td>
                        <td class="border-top text-right"><?php echo (amount_journal($total_D0)) ?></td>
                        <td class="border-top text-right"><?php echo (amount_journal($total_D31)) ?></td>
                        <td class="border-top text-right"><?php echo (amount_journal($total_D61)) ?></td>
                        <td class="border-top text-right"><?php echo (amount_journal($total_D91)) ?></td>
                        <td class="border-top text-right"><?php echo (amount_journal($grandtotal)) ?></td>
                    </tr>
					</tbody>
                </table>
            </div>
        </div>
    </div>
</div> 

</body>
</html>