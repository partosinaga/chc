<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo 'General Ledger';?></title>

	<link href="<?php echo FCPATH; ?>assets/css/report_gl.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	<script type="text/php">
        if ( isset($pdf) ) {
            $font = Font_Metrics::get_font("helvetica", "");
            //$pdf->page_text(520, 810, "page {PAGE_NUM} of {PAGE_COUNT}", $font, 5, array(0,0,0));
            //$pdf->page_text(520, 10, "page {PAGE_NUM} of {PAGE_COUNT}", $font, 5, array(0,0,0));
        }
    </script>
    <div id="logo_container">
        <img class="logo_img_left" src="<?php echo FCPATH ; ?>assets/img/logo_dwijaya.png" style="width: 50px;"/>
    </div>
    <div id="header_container" >
        <table style="width:100%;">
            <tr>
                <td class="text-center" style="padding-top: 10px;font-size: 14pt;"><strong>GENERAL LEDGER</strong></td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 12pt;">(Amount In IDR)</td>
            </tr>
            <tr>
                <td class="text-center"><?php echo str_replace('-','/',$header['date_from']) . ' - ' . str_replace('-','/',$header['date_to']) ; ?></td>
            </tr>
        </table>
    </div>
    <div id="container">
        <div id="content" style="margin-top: 0px;">
            <div id="body">
                <!-- <table style="width:100%;" class="table_detail"> -->
                <?php
                if(isset($qry_det)){
                $grpCOA = '';
                $i = 0;

                $sum_balance = 0;
                foreach($qry_det as $record){
                if($grpCOA != $record['coa_code']){
                $strGroupName = $record['coa_code'] . ' ' . $record['coa_desc'];
                if($grpCOA != ''){
                    echo '<tr>
                                            <th class="text-right" colspan="6" style="padding-right:20px;">' . $strGroupName . ' - Ending Balance' . '</th>
                                            <th class="text-right" style="padding-right:5px;">' . number_format($sum_balance,0,',','.') . '</th>
                                        </tr>';

                    echo '<tr><td colspan="7" style="border-right:0px;border-left: 0px;border-bottom: 0px;"></td></tr>
                                      </table>';

                    $sum_balance = 0;
                }
                ?>
                <table style="width:100%;margin-bottom: 20px;" class="table_detail">
                    <thead >
                        <tr >
                            <th style="text-align: left;padding-left: 10px;" colspan="7"><strong>ACCOUNT&nbsp;:&nbsp;<?php echo $strGroupName ;?></strong></th>
                        </tr>
                        <tr>
                            <th width="50px" >Date</th>
                            <th width="75px" >Doc No</th>
                            <th width="65px" >Filing No</th>
                            <th class="text-left">Description</th>
                            <th width="80px" >Debit </th>
                            <th width="80px" >Credit </th>
                            <th width="80px" >Balance</th>

                        </tr>
                    </thead>
                    <tbody >
                    <?php
                    $grpCOA = $record['coa_code'];
                    }

                    echo '<tr>
                                            <td width="50px" class="text-center">' . $record['journal_date'] . '</td>
                                            <td width="75px" class="text-center">' . $record['journal_no'] . '</td>
                                            <td width="65px" class="text-center">' . ($record['filing_no'] != '' ? $record['filing_no'] : '-') . '</td>
                                            <td >' . nl2br($record['journal_note'], true) . '</td>';
                    if($record['is_debit'] && $record['journal_credit'] > 0){
                        echo           '<td width="80px" class="text-right" style="padding-right:5px;">' . number_format($record['journal_debit'],0,',','.') . '</td>
                                        <td width="80px" class="text-right" style="padding-right:5px;color:red;">' . number_format($record['journal_credit'],0,',','.') . '</td>';
                    }else if(!$record['is_debit'] && $record['journal_debit'] > 0){
                        echo           '<td width="80px" class="text-right" style="padding-right:5px;color:red;">' . number_format($record['journal_debit'],0,',','.') . '</td>
                                        <td width="80px" class="text-right" style="padding-right:5px;">' . number_format($record['journal_credit'],0,',','.') . '</td>';
                    }else{
                        echo           '<td width="80px" class="text-right" style="padding-right:5px;">' . number_format($record['journal_debit'],0,',','.') . '</td>
                                        <td width="80px" class="text-right" style="padding-right:5px;">' . number_format($record['journal_credit'],0,',','.') . '</td>';
                    }
                    echo               '<td width="80px" class="text-right" style="padding-right:5px;">' . number_format($record['balance'],0,',','.') . '</td>
                                        </tr>';

                    $sum_balance = $record['balance'];
                    $i++;

                    if($i == count($qry_det)){
                        if($grpCOA != ''){
                            echo '<tr>
                                            <th class="text-right" colspan="6" style="padding-right:20px;">' . $strGroupName . ' - Ending Balance' . '</th>
                                            <th class="text-right" style="padding-right:5px;">' . number_format($sum_balance,0,',','.') . '</th>
                                        </tr>';
                            echo '<tr><td colspan="7" style="border-right:0px;border-left: 0px;border-bottom: 0px;"></td>
                                      </tr></tbody></table>';

                            $sum_balance = 0;
                        }
                    }
                }
            }
                    ?>

            </div>
        </div>
    </div></body></html><!-- /body></html -->