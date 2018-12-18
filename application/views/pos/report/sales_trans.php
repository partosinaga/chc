<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">
		<!-- BEGIN PAGE HEADER-->
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<?php
					$breadcrumbs = get_menu_name($this->uri->segment(1), $this->uri->segment(2), $this->uri->segment(3), $this->uri->segment(4));
					foreach($breadcrumbs as $breadcrumb){
						echo $breadcrumb;
					}
				?>
			</ul>
		</div>
		<!-- END PAGE HEADER-->
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12" >
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-users"></i>Sales Transactions
						</div>
                        <div class="actions pull-right">
                            <a href="javascript:;" class="btn green-meadow" id="print-button" style="margin-right: 15px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Print</a>
                        </div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <div class="table-actions-wrapper">
                                    <div class="col-md-4 "></div>
                                    <div class="col-md-8" style="padding-left: 0px;padding-right: 0px;">
                                        <div class="input-group date-picker input-daterange" id="datepicker-range" data-date-format="dd-mm-yyyy">
                                            <input type="text" class="form-control form-filter-wrapper input-sm" name="filter_date_from" id="filter_date_from" value="<?php echo date('d-m-Y');?>">
                                                                <span class="input-group-addon input-sm">
                                                                to</span>
                                            <input type="text" class="form-control form-filter-wrapper input-sm" name="filter_date_to" id="filter_date_to" value="<?php echo date('d-m-Y');?>">
                                        </div>

                                        <!--div class="input-group date date-picker " data-date-format="dd-mm-yyyy">
                                            <input type="text" class="form-control form-filter-wrapper input-sm text-center" name="filter_date_from" id="filter_date_from" value="<?php echo date('d-m-Y');?>" readonly>
                                        <span class="input-group-btn">
										    <button class="btn default input-sm" type="button" ><i class="fa fa-calendar" ></i></button>
										</span>
                                        </div -->
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered dataTable table-small table-hover table-po-detail" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center" width="7%">NO </th>
                                            <th class="text-left" width="7%">DATE </th>
                                            <th class="text-left" width="20%">CUSTOMER </th>
                                            <th class="text-left" >ITEM DESCRIPTION</th>
                                            <th class="text-center" width="7%"> QTY </th>
                                            <th class="text-center" width="8%"> PRICE </th>
                                            <th class="text-center" width="7%"> TAX </th>
                                            <th class="text-center" width="8%">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    <tr class="heading">
                                        <th colspan="4"></th>
                                        <th class="text-right">TOTAL</th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
<!-- END CONTENT -->

<script>
	$(document).ready(function(){
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "right",
                todayHighlight: true,
                todayBtn: true,
                autoclose: true
            });

            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        function init_page(){
            $(".mask_currency").inputmask("decimal", {
                radixPoint: ".",
                groupSeparator: ",",
                digits: 2,
                autoGroup: true,
                autoUnmask: true
            });

        }

        //init_page();

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        var grid = new Datatable();

		var handleRecords = function () {
			grid.init({
				src: $("#datatable_ajax"),
				onSuccess: function (grid) {
					// execute some code after table records loaded
				},
				onError: function (grid) {
					// execute some code on network or other general error  
				},
				onDataLoad: function(grid) {
					// execute some code on ajax data load
				},
				loadingMessage: 'Loading...',
				dataTable: {
					"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
					
					"bStateSave": false,
					"aoColumns": [
                        { "sClass": "text-center td-height"},
                        { "sClass": "text-center td-height"  },
                        { "sClass": "text-left td-height" , "bSortable" : false },
                        { "sClass": "text-left td-height" , "bSortable" : false},
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false }
                    ],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": -1, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('pos/report/xreport_sales_trans');?>"
					},
					"fnDrawCallback": function( oSettings ) {
                        var rowCount = oSettings._iRecordsTotal;
                        if(rowCount > 0){
                            $('#print-button').removeClass('hide');
                        }else{
                            $('#print-button').addClass('hide');
                        }

                        init_page();
                        //initDateChanged();

                        $('.date-picker').datepicker({
                            rtl: Metronic.isRTL(),
                            orientation: "right",
                            autoclose: true
                        });
					},
                    "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                        var n_amount = 0;
                        var n_tax = 0;
                        var n_subtotal = 0;
                        for ( var i=0 ; i<aaData.length ; i++ )
                        {
                            var total_ = aaData[i][5].replace('<span class="mask_currency">', '');
                            var total = total_.replace('</span>', '');

                            n_amount += parseFloat((parseFloat(total)).toFixed(0)) || 0;

                            total_ = aaData[i][6].replace('<span class="mask_currency">', '');
                            total = total_.replace('</span>', '');
                            n_tax += parseFloat((parseFloat(total)).toFixed(0)) || 0;

                            total_ = aaData[i][7].replace('<span class="mask_currency">', '');
                            total = total_.replace('</span>', '');
                            n_subtotal += parseFloat((parseFloat(total)).toFixed(0)) || 0;
                        }

                        n_amount = parseFloat(n_amount.toFixed(0));
                        n_tax = parseFloat(n_tax.toFixed(0));
                        n_subtotal = parseFloat(n_subtotal.toFixed(0));

                        nRow.getElementsByTagName('th')[2].innerHTML = '<span class="mask_currency">' + n_amount + '</span>';
                        nRow.getElementsByTagName('th')[3].innerHTML = '<span class="mask_currency">' + n_tax + '</span>';
                        nRow.getElementsByTagName('th')[4].innerHTML = '<span class="mask_currency">' + n_subtotal + '</span>';
                    }
				}
			});

            var tableWrapper = $("#datatable_ajax_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false
            });

		}

		handleRecords();

        var data_from;
        var data_to;

        var initDateChanged = function(){
            var tableWrapper = $("#datatable_ajax_wrapper");

            $("#datepicker-range").datepicker().on('changeDate', function(e){
                //e.preventDefault();
                //grid.clearAjaxParams();
                //console.log('date changed ...');
                if (data_from != $('#filter_date_from').val() || data_to != $('#filter_date_to').val()) {
                    data_from = $('#filter_date_from').val();
                    data_to = $('#filter_date_to').val();
                    //console.log(data_ida + ' - ' + data_volta);
                    $('.datepicker').remove();
                    grid.submitFilter();
                }
            });

        }

        initDateChanged();

        $('#print-button').on('click', function(){
            var date_from = $('input[name="filter_date_from"]').val();
            var date_to = $('input[name="filter_date_to"]').val();
            //var date_until = $('input[name="filter_date_to"]').val();

            window.open('<?php echo base_url('pos/report/pdf_sales_trans')?>/' + date_from + '/' + date_to + '.tpd','_blank');
        });
	});

</script>