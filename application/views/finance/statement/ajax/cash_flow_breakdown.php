<table class="table table-report table-striped table-hover" style="margin-top:0px;margin-bottom:15px;background:#ffffff;">		
	<?php
		$qry = $this->db->query("SELECT * FROM fxnStatementCFBreakdown(" . $month . ", " . $year . ", '" . url_clean($sub_ctg_name) . "', '" . url_clean($account_name) . "') ORDER BY BankAccountNo, AccountName, JournalDate");
		
		$bank_acc = array();

		foreach ( $qry->result() as $row1 ) {
			array_push($bank_acc, $row1->BankAccountNo);
		}
		
		$t_bank_acc = array_unique($bank_acc);
		
		$tot_debit = 0;
		$tot_credit = 0;
		
		$tot_all_debit = 0;
		$tot_all_credit = 0;
		
		$i = 0;
		foreach($t_bank_acc as $bank_name){
			echo '<thead><tr>
				<th colspan="7" class="text-left"><h3 style="font-size:17px;font-style:italic;"><strong>' . $bank_name . '</strong></h3></th>
			</tr></thead>';
			$i++;
	?>
        <thead>
					<tr>
						<th width="10%">Document No</th>
						<th width="10%">Date</th>
						<th width="30%" class="text-left">Reference</th>
						<th width="10%" class="text-right">Debit</th>
						<th width="1" class="text-right"></th>
						<th width="10%" class="text-right">Credit</th>
						<th width="29%" class="text-left">Paid To / Received From</th>
					</tr>
		</thead>
	    <tbody>
					<?php
						$acc_name = '';
						foreach ( $qry->result() as $row ) {
							if($bank_name == $row->BankAccountNo){
								$date_ = new DateTime($row->JournalDate);
								$date = $date_->format('d-m-Y');
								
								if($acc_name != $row->AccountName){
									echo '<tr>
										<td colspan="7" class="text-left"><strong><i>' . $row->AccountName . '</i></strong></td>
									</tr>';
									$acc_name = $row->AccountName;
								}
								
								echo '<tr>
										<td class="text-center">' . $row->JournalNo . '</td>
										<td class="text-center">' . $date . '</td>
										<td>' . $row->ReffNo . '</td>
										<td class="text-right">' . amount_journal($row->Debit) . '</td>
										<td></td>
										<td class="text-right">' . amount_journal($row->Credit) . '</td>
										<td>' . $row->Subject . '</td>
									</tr>';
									
								$tot_debit = $tot_debit + $row->Debit;
								$tot_credit = $tot_credit + $row->Credit;
							}
						}
					?>
		</tbody>
		<tbody>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="text-right"><strong>SUB TOTAL</strong></td>
						<td class="text-right r-border"><strong><?php echo amount_journal($tot_debit);?></strong></td>
						<td class="text-right total"></td>
						<td class="text-right r-border"><strong><?php echo amount_journal($tot_credit);?></strong></td>
						<td class="total"></td>
					</tr>
		</tbody>
	<?php
				$tot_all_debit = $tot_all_debit + $tot_debit;
				$tot_all_credit = $tot_all_credit + $tot_credit;
				
				$tot_debit = 0;
				$tot_credit = 0;
			}
	?>
		<thead>
				<tr>
					<th class="total">&nbsp;</th>
					<th class="total">&nbsp;</th>
					<th class="text-right total"><strong>GRAND TOTAL</strong></th>
					<th class="text-right total r-border r-background"><strong><?php echo amount_journal($tot_all_debit);?></strong></th>
					<th class="text-right total"></th>
					<th class="text-right total r-border r-background"><strong><?php echo amount_journal($tot_all_credit);?></strong></th>
					<th class="total"></th>
				</tr>
		</thead>
</table>