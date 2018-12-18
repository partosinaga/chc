<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo 'Bank Book';?></title>

	<link href="<?php echo FCPATH; ?>assets/css/report_gl.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	<script type="text/php">
        if ( isset($pdf) ) {
            $font = Font_Metrics::get_font("helvetica", "");
            $pdf->page_text(520, 810, "page {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
        }
    </script>
	<!--div class="logo">
    <img src="<?php echo base_url('assets/img/logo_dwijaya.png'); ?>" alt="" width="60px">
</div-->
	<div id="container">
        <div id="header">
            <table style="width:100%;">
                <tr>
                    <td class="text-center" style="padding-top: 10px;font-size: 16pt;"><strong>BANK BOOK</strong></td>
                </tr>
                <tr>
                    <td class="text-center"><?php echo str_replace('-','/',$header['date_from']) . ' - ' . str_replace('-','/',$header['date_to']) ; ?></td>
                </tr>
                <br/><br/>
            </table>
        </div>
        <br/>
		<div id="content">
			<div id="body">
                <table style="width:100%;" class="table_detail">
                <?php
                if(isset($qry_det)){
                    $grpBank = '';
                    $i = 0;

                    $sum_balance = 0;
                    foreach($qry_det as $record){
                        if($grpBank != $record['bank_account_no']){
                            $strGroupName = $record['bank_code'] . ' ' .$record['bank_account_no'] . ' (' . $record['currency'] . ')';
                            if($grpBank != ''){
                                echo '<tr>
                                            <th class="text-right" colspan="7" style="padding-right:20px;">' . $strGroupName . ' - Ending Balance' . '</th>
                                            <th class="text-right" style="padding-right:5px;">' . number_format($sum_balance,0,',','.') . '</th>
                                            <th class="text-right" style="padding-right:5px;">&nbsp;</th>
                                        </tr>';

                                echo '<tr><td colspan="7" style="border-right:0px;border-left: 0px;border-bottom: 0px;"></td></tr>';

                                $sum_balance = 0;
                            }
                ?>

                            <tr>
                                <th style="width:8%;text-align: left;padding-left: 10px;" colspan="9"><strong><?php echo $strGroupName ;?></strong></th>
                            </tr>
                            <tr>
                                <th style="width:9%;">Doc No</th>
                                <th style="width:9%;">Date</th>
                                <th style="width:20%;">Subject</th>
                                <th style="width:9%;">Reff No</th>
                                <th class="text-left">Description</th>
                                <th style="width:10%;">Debit </th>
                                <th style="width:10%;">Credit </th>
                                <th style="width:10%;">Balance</th>
                                <th style="width:6%;">Status</th>
                            </tr>
                <?php
                            $grpBank = $record['bank_account_no'];
                        }

                        echo '<tr>
                                            <td class="text-center">' . $record['doc_no'] . '</td>
                                            <td class="text-center">' . $record['doc_date'] . '</td>
                                            <td >' . $record['subject'] . '</td>
                                            <td class="text-center">' . $record['reff_no'] . '</td>
                                            <td >' . nl2br($record['description'], true) . '</td>';
                        echo               '<td class="text-right" style="padding-right:5px;">' . number_format($record['debit'],0,',','.') . '</td>
                                            <td class="text-right" style="padding-right:5px;">' . number_format($record['credit'],0,',','.') . '</td>
                                            <td class="text-right" style="padding-right:5px;">' . number_format($record['balance'],0,',','.') . '</td>
                                            <td class="text-center" style="padding-right:5px;">' . $record['status_caption'] . '</td>
                                        </tr>';

                        $sum_balance = $record['balance'];
                        $i++;

                        if($i == count($qry_det)){
                            if($grpBank != ''){
                                echo '<tr>
                                            <th class="text-right" colspan="7" style="padding-right:20px;">' . $strGroupName . ' - Ending Balance' . '</th>
                                            <th class="text-right" style="padding-right:5px;">' . number_format($sum_balance,0,',','.') . '</th>
                                            <th class="text-right" style="padding-right:5px;">&nbsp;</th>
                                        </tr>';
                                $sum_balance = 0;
                            }
                        }
                    }
                }
                ?>

                </table>

                </div>
			</div>

		</div>
	</div></body></html>