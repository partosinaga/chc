<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">
		<!-- BEGIN PAGE HEADER-->
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<?php
					$breadcrumbs = get_menu_name($this->uri->segment(1), $this->uri->segment(2), $this->uri->segment(3));
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
							<i class="fa fa-users"></i>OFFICIAL RECEIPT REPORT
						</div>
					</div>
					<div class="portlet-body table-responsive">
						<div class="table-container " >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <div class="table-actions-wrapper">
                                    <div class="input-group date-picker input-daterange" id="datepicker-range" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control form-filter-wrapper input-sm" name="filter_date_from" id="filter_date_from" value="<?php echo date('d-m-Y', strtotime('-30 days'));?>">
                                                            <span class="input-group-addon input-sm">
                                                            to</span>
                                        <input type="text" class="form-control form-filter-wrapper input-sm" name="filter_date_to" id="filter_date_to" value="<?php echo date('d-m-Y');?>">
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered dataTable table-small table-hover table-po-detail" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center" >DATE </th>
                                            <th class="text-center" >DOC NO </th>
                                            <th class="text-center" >ACCOUNT NO </th>
                                            <th class="text-center" >TYPE </th>
                                            <th class="text-left" > SUBJECT </th>
                                            <th class="text-left" > DESCRIPTION </th>
                                            <th class="text-center" >BANK FEE </th>
                                            <th class="text-center" >AMOUNT </th>
                                            <th class="text-center" >TOTAL </th>
                                            <th class="text-center" >&nbsp;</th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td>

                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_doc_no">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_bankaccount_no">
                                            </td>
                                            <td>
                                                <select name="filter_paymenttype_id" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                    if(isset($payment_type)){
                                                        foreach($payment_type as $type) {
                                                            echo '<option value="' . $type['paymenttype_id'] . '">' . $type['paymenttype_desc'] . '</option>';
                                                        }
                                                    }

                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_subject">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_description">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_bank_charge_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_bank_charge_to" placeholder="Max">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_receipt_amount_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_receipt_amount_to" placeholder="Max">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_subtotal_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_subtotal_to" placeholder="Max">
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <button class="btn btn-sm yellow filter-submit margin-bottom tooltips margin-bottom-5" data-original-title="Filter" data-placement="top" data-container="body" style="margin-right:0px;"><i class="fa fa-search"></i></button>
                                                    <button class="btn btn-sm red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body" style="margin-right:0px;"><i class="fa fa-times"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr class="heading">
                                            <th colspan="7"></th>
                                            <th class="text-right">TOTAL</th>
                                            <th></th>
                                            <th></th>
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
                autoclose: true,
                todayHighlight: true,
                todayBtn: true
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

        init_page();

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
                        { "bSortable": true, "sClass": "text-center td-height", "sWidth" : "5%" },
                        { "sClass": "text-center td-height", "sWidth" : "7%" },
                        { "sClass": "text-left td-height" },
                        { "sClass": "text-center td-height", "sWidth" : "10%" },
                        { "sClass": "text-left td-height"  },
                        { "sClass": "text-left td-height"  },
                        { "sClass": "text-right td-height", "sWidth" : "6%"  },
                        { "sClass": "text-right td-height", "sWidth" : "8%"  },
                        { "sClass": "text-right td-height", "sWidth" : "8%"  },
                        { "bSortable": false, "sClass": "text-center td-height","sWidth" : "4%" }
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": -1, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('ar/report/ajax_official_receipt/'. get_menu_id());?>"
					},
					"fnDrawCallback": function( oSettings ) {
                        init_page();

                        $('.date-picker').datepicker({
                            rtl: Metronic.isRTL(),
                            orientation: "right",
                            autoclose: true
                        });
					},
                    "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                        var t_total = 0;
                        for ( var i=0 ; i<aaData.length ; i++ )
                        {
                            var total_ = aaData[i][8].replace('<span class="mask_currency">', '');
                            var total = total_.replace('</span>', '');
                            t_total += parseFloat((parseFloat(total)).toFixed(2)) || 0;
                        }
                        t_total = parseFloat(t_total.toFixed(2));

                        nRow.getElementsByTagName('th')[2].innerHTML = '<span class="mask_currency">' + t_total + '</span>';
                    }
				}
			});

            var tableWrapper = $("#datatable_ajax_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false
            });
			
		}

		handleRecords();
        grid.submitFilter();

        $('.input-daterange').live('change', function(e){
            //grid.clearAjaxParams();
            grid.submitFilter();
        });

	});

</script>