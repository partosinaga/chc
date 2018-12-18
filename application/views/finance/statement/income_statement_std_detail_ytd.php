<div class="page-content" style="margin-left:0px; min-height:700px;">
    <!-- BEGIN PAGE HEADER-->
    <div class="row hidden-print">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE-->
            <h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;"><?php echo $profile['company_name']; ?></h3>
            <h3 class="page-title text-center" style="font-size:25px;margin-bottom:5px;">INCOME STATEMENT BREAK DOWN</h3>
            <h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">Amounts in (IDR)</h3>
            <h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">From : <?php echo date("d/m/Y", mktime(0, 0, 0, 1, 1, $year));?> to <?php echo date("t/m/Y", strtotime($year . '-' . $month . '-01'));?></h3>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <!-- END PAGE HEADER-->
    <!-- BEGIN PAGE CONTENT-->
    <div class="invoice">
        <div class="row" style="font: 10pt normal Helvetica, Arial, sans-serif;">
            <div class="col-xs-12">
<table class="table table-report table-striped table-hover" style="margin-top:10px;margin-bottom:15px;background:#ffffff;">
	<thead>
		<tr>
			<th width="50%" class="text-left">ACCOUNT NAME</th>
			<th width="25%" class="text-left">MONTH</th>
			<th width="25%" class="text-right">BALANCE</th>
		</tr>
	</thead>
	<tbody>
<?php
$qry = $this->db->query("SELECT * FROM fxnStatementPLBreakdownYTD(" . $month . ", " . $year . ", '" . url_clean($ctg_name) . "', '" . url_clean($sub_ctg_name) . "', '" . url_clean($account_name) . "') ORDER BY AccountName");

$total = 0;
foreach($qry->result() as $row){
	echo '<tr>
			<td class="text-left">' . $row->AccountName . '</td>
			<td class="text-left">' . (date("F", mktime(0, 0, 0, $row->PeriodMonth, 1, 2014))) . '</td>
			<td class="text-right">' . amount_journal($row->Balance) . '</td>
		</tr>';
		$total = $total + $row->Balance;
}
?>
	</tbody>
	<thead>
		<tr>
			<th colspan="2" class="total text-right"><strong>TOTAL</strong></th>
			<th class="text-right total r-border r-background"><strong><?php echo amount_journal($total);?></strong></th>
		</tr>
	</thead>
</table>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function(){
            $('#btn_print_statement').on('click', function(e){
                e.preventDefault();

                var print_url = "<?php echo base_url('finance/statement/pl_std_breakdown_ytd/' . $month . '/' . $year . '/' . $ctg_name . '/' . $sub_ctg_name . '/' . $account_name . '/1'); ?>";
                //console.log(print_url);
                window.location.href= print_url;
            });
        });
    </script>