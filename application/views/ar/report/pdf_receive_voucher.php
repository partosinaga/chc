<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $row->journal_no;?></title>

	<link href="<?php echo FCPATH; ?>assets/css/voucher.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	<script type="text/php">
        if ( isset($pdf) ) {
            $font = Font_Metrics::get_font("helvetica", "");
            //$pdf->page_text(520, 810, "page {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
        }
    </script>
	<div id="logo_container">
		<img class="logo_img" src="<?php echo base_url();?>assets/img/tpd_small.jpg"/>
	</div>
	<div id="container">
        <div id="header">
            <h1><strong><?php echo isset($doc_type_title) ? $doc_type_title : 'Voucher'; ?></strong></h1>
        </div>
		<div id="content">
			<div id="body">
				<table style="width:100%;" class="table_head">
					<tr>
						<td style="width:12%;"><strong>Journal No.</strong></td>
                        <td style="width:0.5%;">:</td>
						<td style="width:15%;" class="border_bottom"><?php echo $row->receipt_no;?></td>
						<td style="width:22%;">&nbsp;</td>
						<td style="width:15%;"><strong>Reference</strong></td>
                        <td style="width:0.5%;">:</td>
						<td style="width:35%;" class="border_bottom"></td>
					</tr>
					<tr>
						<td><strong>Date</strong></td>
                        <td>:</td>
						<td class="border_bottom"><?php echo dmy_from_db($journal->journal_date);?></td>
						<td>&nbsp;</td>
						<td><strong>Reff. Date</strong></td>
                        <td>:</td>
						<td class="border_bottom"><strong></strong></td>
					</tr>
                    <tr>
                        <td><strong>Amount</strong></td>
                        <td>:</td>
                        <td class="border_bottom"><?php echo number_format($journal->journal_amount,0,',','.');?></td>
                        <td>&nbsp;</td>
                        <td><strong><?php echo isset($subject_title) ? $subject_title : 'Subject'; ?></strong></td>
                        <td>:</td>
                        <td class="border_bottom"><?php echo $row->subject ;?></td>
                    </tr>
                    <tr>
                        <td><strong>Say</strong></td>
                        <td>:</td>
                        <td colspan="5" class="border_bottom"><?php echo number_to_words($journal->journal_amount) ;?></td>
                    </tr>
					<tr>
						<td><strong>Description</strong></td>
                        <td>:</td>
						<td colspan="5" class="border_bottom"><?php echo nl2br($row->journal_remarks, true) ;?></td>
					</tr>
				</table>
				<br/><br/><br/>
                <div >
				<table style="width:100%;" class="table_detail">
					<tr>
                        <th style="width:13%;">AC. CODE</th>
                        <th style="width:47%;" class="text-left">DESCRIPTION</th>
                        <th style="width:10%;">DEPT</th>
                        <th style="width:15%;">Dr (IDR)</th>
                        <th style="width:15%;">Cr (IDR)</th>
					</tr>
					<?php
                        $max = 25;
                        $i = 0;
						if($qry_det->num_rows() > 0){
							foreach($qry_det->result() as $row_det){
								echo '<tr>
										<td class="text-center">' . $row_det->coa_code . '</td>
										<td >' . nl2br($row_det->coa_desc, true) . '</td>
										<td class="text-center">' . $row_det->dept_code . '</td>
										<td class="text-right" style="padding-right:5px;">' . number_format($row_det->journal_debit,0,'.',',') . '</td>
										<td class="text-right" style="padding-right:5px;">' . number_format($row_det->journal_credit,0,'.',',') . '</td>
									</tr>';
                               $i++;
							}
						}

                        for($x = $i;$x < $max;$x++){
                            echo '<tr>
										<td >&nbsp;</td>
										<td >&nbsp;</td>
										<td >&nbsp;</td>
										<td >&nbsp;</td>
										<td >&nbsp;</td>
									</tr>';
                        }
					?>

				</table>
                </div>
                <table style="width:100%" class="table_detail">
                    <tr >
                        <td width="50%" class="text-right" style="border-right: 0px;border-top: 1px solid #000000;border-bottom: 1px solid #000000;"><strong>Total Debits</strong></td>
                        <td width="15%" class="text-right" style="border-right: 0px; border-left: 0px;border-top: 1px solid #000000;border-bottom: 1px solid #000000;"> <?php echo number_format($row->journal_amount,0,',','.'); ?></td>
                        <td width="20%" class="text-right" style="border-right: 0px; 0px; border-left: 0px;border-top: 1px solid #000000;border-bottom: 1px solid #000000;"><strong>Total Credits</strong></td>
                        <td width="15%" class="text-right" style="border-left: 0px;border-top: 1px solid #000000;border-bottom: 1px solid #000000;"> <?php echo number_format($row->journal_amount,0,',','.'); ?></td>
                    </tr>
                </table>
			</div>
            <div id="footer" style="margin-top:20px;">
                <table style="width:100%"  >
                    <?php
                        if($cashbook_type == 3){
                    ?>
                            <tr>
                                <td width="33%" style="padding:5px 10px;border-bottom:0px;" class="bordered text-center"><strong>Prepared By :</strong></td>
                                <td width="33%" style="padding:5px 10px;border-bottom:0px;" class="bordered text-center"><strong>Checked By :</strong></td>
                                <td width="33%" style="padding:5px 10px;border-bottom:0px;" class="bordered text-center"><strong>Approved By :</strong></td>
                            </tr>
                            <tr>
                                <td class="bordered" style="padding:0px 10px;height: 120px;border-top:0px;"></td>
                                <td class="bordered" style="padding:0px 10px;height: 120px;border-top:0px;"></td>
                                <td class="bordered" style="padding:0px 10px;height: 120px;border-top:0px;"></td>
                            </tr>
                            <tr>
                                <td style="padding:5px 10px;" class="bordered">Date :</td>
                                <td style="padding:5px 10px;" class="bordered">Date :</td>
                                <td style="padding:5px 10px;" class="bordered">Date :</td>
                            </tr>
                    <?php
                        }else {
                    ?>
                            <tr>
                                <td width="25%" style="padding:5px 10px;border-bottom:0px;" class="bordered text-center"><strong>Prepared By :</strong></td>
                                <td width="25%" style="padding:5px 10px;border-bottom:0px;" class="bordered text-center"><strong>Checked By :</strong></td>
                                <td width="25%" style="padding:5px 10px;border-bottom:0px;" class="bordered text-center"><strong>Approved By :</strong></td>
                                <td width="25%" style="padding:5px 10px;border-bottom:0px;" class="bordered text-center"><strong>Received By :</strong></td>
                            </tr>
                            <tr>
                                <td class="bordered" style="padding:0px 10px;height: 120px;border-top:0px;"></td>
                                <td class="bordered" style="padding:0px 10px;height: 120px;border-top:0px;"></td>
                                <td class="bordered" style="padding:0px 10px;height: 120px;border-top:0px;"></td>
                                <td class="bordered" style="padding:0px 10px;height: 120px;border-top:0px;"></td>
                            </tr>
                            <tr>
                                <td style="padding:5px 10px;" class="bordered">Date :</td>
                                <td style="padding:5px 10px;" class="bordered">Date :</td>
                                <td style="padding:5px 10px;" class="bordered">Date :</td>
                                <td style="padding:5px 10px;" class="bordered">Date :</td>
                            </tr>
                    <?php
                        }
                    ?>
                </table>
            </div>
		</div>
	</div>
</body>
</html>