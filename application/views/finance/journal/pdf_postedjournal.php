<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo 'Posted Journal';?></title>

	<link href="<?php echo base_url(); ?>assets/css/report_gl.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	<script type="text/php">
        if ( isset($pdf) ) {
            $font = Font_Metrics::get_font("helvetica", "");
            $pdf->page_text(520, 810, "page {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
        }
    </script>
    <?php if($is_pdf){ ?>
        <div id="logo_container">
            <img class="logo_img" src="<?php echo base_url(); ?>assets/img/logo_dwijaya.png" style="width: 50px;"/>
        </div>
    <?php } ?>
	<div id="container">
        <div id="header">
            <table style="width:100%;">
                <tr>
                    <td class="text-center" style="padding-top: 10px;font-size: 16pt;"><strong>POSTED JOURNAL</strong></td>
                </tr>
                <tr>
                    <td class="text-center" style="font-size: 14pt;">(Amount In IDR)</td>
                </tr>
                <tr>
                    <td class="text-center"><?php echo $header['date_from'] != '-' ? str_replace('-','/',$header['date_from']) . ' - ' . str_replace('-','/',$header['date_to']) : ''; ?></td>
                </tr>
                <tr><td>&nbsp;</td></tr>
            </table>
        </div>
        <br/>
		<div id="content">
			<div id="body">
                <table style="width:100%;" class="table_detail">
                    <tr>
                        <th style="width:12%;">JOURNAL NO</th>
                        <th style="width:10%;">ACC. NO</th>
                        <th style="width:23%;border-right: 0px;" class="text-left" >DESCRIPTION</th>
                        <th style="width:23%;border-left: 0px;" class="text-left" ></th>
                        <th style="width:7%;">MOD </th>
                        <th style="width:12%;">Dr </th>
                        <th style="width:12%;">Cr </th>
                    </tr>
                <?php
                if(isset($qry_det)){
                    $grpJournalNo = '';
                    $i = 0;
                    foreach($qry_det as $record){
                        if($grpJournalNo != $record['journal_no']){
                            echo '<tr>
                                            <td class="text-center" style="border-top:solid 1px;border-bottom:dotted 1px;">' . $record['journal_no'] . '</td>
                                            <td class="text-center" style="border-top:solid 1px;border-bottom:dotted 1px;">' . $record['journal_date'] . '</td>
                                            <td class="text-left" colspan="2" style="border-top:solid 1px;border-bottom:dotted 1px;">' . nl2br($record['journal_remarks'], true) . '</td>
                                            <td class="text-center" style="border-top:solid 1px;border-bottom:dotted 1px;">' . $record['module'] . '</td>
                                            <td class="text-left" colspan="2" style="border-top:solid 1px;border-bottom:dotted 1px;">&nbsp;</td>
                                        </tr>';

                            $grpJournalNo = $record['journal_no'];
                        }

                        $i++;

                        if($i == count($qry_det)){
                            echo             '<tr>
                                            <td class="text-center" style="border-bottom:solid 1px;">' . '' . '</td>
                                            <td class="text-center" style="border-bottom:solid 1px;">' . $record['coa_code'] . '</td>
                                            <td class="text-left" style="border-bottom:solid 1px;">' . $record['coa_desc'] . '</td>
                                            <td class="text-left" style="border-bottom:solid 1px;">' . nl2br($record['journal_note'], true) . '</td>
                                            <td style="border-bottom:solid 1px;"></td>
                                            <td class="text-right" style="padding-right:5px;border-bottom:solid 1px;">' . number_format($record['journal_debit'],0,',','.') . '</td>
                                            <td class="text-right" style="padding-right:5px;border-bottom:solid 1px;">' . number_format($record['journal_credit'],0,',','.') . '</td>
                                          </tr>';
                        }else{
                            echo             '<tr>
                                            <td class="text-center">' . '' . '</td>
                                            <td class="text-center">' . $record['coa_code'] . '</td>
                                            <td class="text-left">' . $record['coa_desc'] . '</td>
                                            <td class="text-left">' . nl2br($record['journal_note'], true) . '</td>
                                            <td></td>
                                            <td class="text-right" style="padding-right:5px;">' . number_format($record['journal_debit'],0,',','.') . '</td>
                                            <td class="text-right" style="padding-right:5px;">' . number_format($record['journal_credit'],0,',','.') . '</td>
                                          </tr>';
                        }
                    }
                }
                ?>

                </table>

                </div>
                <div id="footer" style="margin-top:20px;">

                </div>
			</div>

		</div>
	</div>
</body>
</html>