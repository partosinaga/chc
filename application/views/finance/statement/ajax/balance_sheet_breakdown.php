<table class="table table-report table-striped table-hover" style="margin-top:0px;margin-bottom:15px;background:#ffffff;">
	<thead>
		<tr>
			<th width="70%" class="text-left">ACCOUNT NAME</th>
			<th width="30%" class="text-right">BALANCE</th>
		</tr>
	</thead>
	<tbody>
<?php
$qry = $this->db->query("SELECT * FROM fxnStatementBSBreakdownYTD(" . $month . ", " . $year . ", '" . url_clean($ctg_name) . "', '" . url_clean($sub_ctg_name) . "', '" . url_clean($account_name) . "') ORDER BY AccountName");

$total = 0;
foreach($qry->result() as $row){
	echo '<tr>
			<td class="text-left">' . $row->AccountName . '</td>
			<td class="text-right">' . amount_journal($row->Balance) . '</td>
		</tr>';
		$total = $total + $row->Balance;
}
?>
	</tbody>
	<thead>
		<tr>
			<th class="total text-right"><strong>TOTAL</strong></th>
			<th class="text-right total r-border r-background"><strong><?php echo amount_journal($total);?></strong></th>
		</tr>
	</thead>
</table>