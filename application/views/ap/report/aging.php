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
							<i class="fa fa-users"></i>Report Aging
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <div class="table-actions-wrapper">
                                    <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control form-filter-wrapper" name="filter_date" id="filter_date" value="<?php echo date('d-m-Y');?>" style="width: 130px;" readonly>
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered table-hover dataTable table-small table-po-detail" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center"> CREDITOR NAME </th>
                                            <th class="text-center" width="80px"> CURR </th>
                                            <th class="text-center" width="150px"> < 31 DAYS </th>
                                            <th class="text-center" width="150px"> 31 - 60 DAYS </th>
                                            <th class="text-center" width="150px"> 61 - 90 DAYS </th>
                                            <th class="text-center" width="150px"> > 90 DAYS </th>
                                            <th class="text-center" width="150px"> TOTAL </th>
                                            <th class="text-center" width="20px">&nbsp;</th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_creditor_name">
                                            </td>
                                            <td></td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_31_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_31_to" placeholder="Max">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_31_60_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_31_60_to" placeholder="Max">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_61_90_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_61_90_to" placeholder="Max">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_91_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_91_to" placeholder="Max">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_total_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_total_to" placeholder="Max">
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
                        { "bSortable": true, "sClass": "text-left td-height" },   // Creditor Name
                        { "sClass": "text-center td-height"  },                     // curr
                        { "sClass": "text-right td-height"  },                      // <31
                        { "sClass": "text-right td-height" },                       // 31-60
                        { "sClass": "text-right td-height"  },                      // 61-90
                        { "sClass": "text-right td-height"  },                      // >90
                        { "sClass": "text-right td-height"  },                      // TOTAL
                        { "bSortable": false, "sClass": "text-center td-height" }   // Action
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": -1, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('ap/report/ajax_aging/'. get_menu_id());?>"
					},
					"fnDrawCallback": function( oSettings ) {
                        init_page();

                        $('.date-picker').datepicker({
                            rtl: Metronic.isRTL(),
                            orientation: "right",
                            autoclose: true
                        });
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