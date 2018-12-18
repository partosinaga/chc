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
							<i class="fa fa-users"></i>Report Creditor Allocation
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <table class="table table-striped table-bordered table-hover dataTable table-small table-po-detail" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center" rowspan="2"> SUPPLIER </th>
                                            <th class="text-center" colspan="3"> DEBIT </th>
                                            <th class="text-center" colspan="3"> CREDIT </th>
                                            <th class="text-center" width="20px">&nbsp;</th>
                                        </tr>
                                        <tr class="heading">
                                            <th class="text-center" width="110px" style="border-top: 1px solid #dddddd;"> DOC NO </th>
                                            <th class="text-center" width="110px" style="border-top: 1px solid #dddddd;"> DOC DATE </th>
                                            <th class="text-center" width="110px" style="border-top: 1px solid #dddddd;"> AMOUNT </th>
                                            <th class="text-center" width="110px" style="border-top: 1px solid #dddddd;"> DOC NO </th>
                                            <th class="text-center" width="110px" style="border-top: 1px solid #dddddd;"> DOC DATE </th>
                                            <th class="text-center" width="110px" style="border-top: 1px solid #dddddd;"> AMOUNT </th>
                                            <th></th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_creditor_name">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_doc_1">
                                            </td>
                                            <td>
                                                <div class="input-group date-picker input-daterange" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control form-filter input-sm text-center" readonly name="filter_doc_date_from" placeholder="From" value="<?php echo date("d-m-Y",strtotime("-1 month"));?>" style="margin-bottom: 5px;">
                                                    <input type="text" class="form-control form-filter input-sm text-center" readonly name="filter_doc_date_to" placeholder="To" value="<?php echo date('d-m-Y');?>">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_amount_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_amount_to" placeholder="Min">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_doc_2">
                                            </td>
                                            <td>
                                                <div class="input-group date-picker input-daterange" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control form-filter input-sm text-center" readonly name="filter_i_doc_date_from" placeholder="From" style="margin-bottom: 5px;">
                                                    <input type="text" class="form-control form-filter input-sm text-center" readonly name="filter_i_doc_date_to" placeholder="To" >
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_i_amount_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_i_amount_to" placeholder="Min">
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
                                            <th colspan="3">TOTAL</th>
                                            <th></th>
                                            <th colspan="2"></th>
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
                        { "bSortable": true, "sClass": "text-left td-height" },     // Creditor Name
                        { "sClass": "text-center td-height"  },                     // doc code
                        { "sClass": "text-center td-height" },                      // doc date
                        { "sClass": "text-right td-height"  },                      // amount
                        { "sClass": "text-center td-height"  },                     // doc code
                        { "sClass": "text-center td-height"  },                     // doc date
                        { "sClass": "text-right td-height"  },                      // amount
                        { "bSortable": false, "sClass": "text-center td-height" }   // Action
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": 150, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('ap/report/ajax_creditor_allocation/'. get_menu_id());?>"
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
                        var t_tot = 0;
                        var t_tot1 = 0;
                        for ( var i=0 ; i<aaData.length ; i++ )
                        {
                            var d0_ = aaData[i][3].replace('<span class="mask_currency">', '');
                            var d0 = d0_.replace('</span>', '');
                            t_tot += parseFloat((parseFloat(d0)).toFixed(2)) || 0;

                            var d31_ = aaData[i][6].replace('<span class="mask_currency">', '');
                            var d31 = d31_.replace('</span>', '');
                            t_tot1 += parseFloat((parseFloat(d31)).toFixed(2)) || 0;
                        }
                        t_tot = parseFloat(t_tot.toFixed(2));
                        t_tot1 = parseFloat(t_tot1.toFixed(2));

                        nRow.getElementsByTagName('th')[1].innerHTML = '<span class="mask_currency">' + t_tot + '</span>';
                        nRow.getElementsByTagName('th')[3].innerHTML = '<span class="mask_currency">' + t_tot1 + '</span>';
                    }
				}
			});

            var tableWrapper = $("#datatable_ajax_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false
            });
			
		}

		handleRecords();

	});
</script>