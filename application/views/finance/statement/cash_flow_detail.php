<div class="page-content" style="margin-left:0px; min-height:700px;">
	<!-- BEGIN PAGE HEADER-->
	<div class="row hidden-print">
		<div class="col-md-12">
			<!-- BEGIN PAGE TITLE-->
			<h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;"><?php echo $profile['company_name']; ?></h3>
			<h3 class="page-title text-center" style="font-size:25px;margin-bottom:5px;">CASH FLOW BREAK DOWN</h3>
			<h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">Amounts in (IDR)</h3>
			<h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">From : <?php echo date("d/m/Y", mktime(0, 0, 0, $month, 1, $year));?> to <?php echo date("t/m/Y", strtotime($year . '-' . $month . '-01'));?></h3>
			<h3 class="page-title text-center" style="font-size:18px;margin-bottom:5px;"><?php echo url_clean($account_name);?></h3>
			<!-- END PAGE TITLE & BREADCRUMB-->
		</div>
	</div>
	<!-- END PAGE HEADER-->
	<!-- BEGIN PAGE CONTENT-->
	<div class="invoice">
		<div class="row">
			<div class="col-xs-12">
				<div class="table-responsive">
					<div class="loader"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- END PAGE CONTENT-->
</div>

<script>
	jQuery(document).ready(function(){
		$.post( "<?php echo base_url('finance/statement/ajax_cash_flow_detail/' . $month . '/' . $year . '/' . $sub_ctg_name . '/' . $account_name);?>", function( data ) {
			$( ".table-responsive" ).html( data );
		});

        $('#btn_print_statement').on('click', function(e){
            e.preventDefault();

            var print_url = "<?php echo base_url('finance/statement/cf_breakdown/' . $month . '/' . $year . '/' . $sub_ctg_name . '/' . $account_name . '/1'); ?>";
            //console.log(print_url);
            window.location.href= print_url;
        });
	});
</script>