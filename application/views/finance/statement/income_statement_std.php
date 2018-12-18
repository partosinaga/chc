<div class="page-content" style="margin-left:0px; min-height:700px;">
	<!-- BEGIN PAGE HEADER-->
	<div class="row hidden-print">
		<div class="col-md-12">
			<!-- BEGIN PAGE TITLE-->
			<h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;"><?php echo $profile['company_name']; ?></h3>
			<h3 class="page-title text-center" style="font-size:25px;margin-bottom:5px;">INCOME STATEMENT</h3>
			<h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">Amounts in (IDR)</h3>
			<h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">As Of <?php echo date("F Y", mktime(0, 0, 0, $month, 1, $year));?></h3>
			<!-- END PAGE TITLE & BREADCRUMB-->
		</div>
	</div>
	<!-- END PAGE HEADER-->
	<!-- BEGIN PAGE CONTENT-->
	<div class="invoice">
		<!--div class="row invoice-logo">
			<div class="col-xs-6 invoice-logo-space" style="margin-top:-90px;">
				<img style="width:250px;" src="<?php echo base_url();?>assets/img/logo_<?php echo strtolower(PROJECT_SHORT);?>.png" alt="<?php echo PROJECT;?>"/>
			</div>
		</div-->
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
		
	});
	
	function open_detail(month, year, ctg_name, sub_ctg_name, account_name, value){
		if(value > 0){
			window.open('<?php echo base_url('finance/statement/pl_std_breakdown');?>/' + month + '/' + year + '/' + encode_string(ctg_name) + '/' + encode_string(sub_ctg_name) + '/' + encode_string(account_name) + '.tpd','_blank');
		}
	}
	
	function open_detail_ytd(month, year, ctg_name, sub_ctg_name, account_name, value){
		if(value > 0){
			window.open('<?php echo base_url('finance/statement/pl_std_breakdown_ytd');?>/' + month + '/' + year + '/' + encode_string(ctg_name) + '/' + encode_string(sub_ctg_name) + '/' + encode_string(account_name) + '.tpd','_blank');
		}
	}
	
	function encode_string(string_){
		var string = string_.replace("(", "%28");
		string = string.replace(")", "%29");
		
		string = encodeURIComponent(string);
		
		return string;
	}
	
	jQuery(document).ready(function(){
		$.post( "<?php echo base_url('finance/statement/ajax_pl_std_main/' . $month . '/' . $year);?>", function( data ) {
			$( ".table-responsive" ).html( data );
		});

        $('#btn_print_statement').on('click', function(e){
            e.preventDefault();

            var print_url = "<?php echo base_url('finance/statement/print_statement/' . $report_type . '/'. $month . '/' . $year); ?>";
            //console.log(print_url);
            window.location.href= print_url;
        });
	});
</script>