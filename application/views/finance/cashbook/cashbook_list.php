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
			<div class="col-md-12">
                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
				<!-- Begin: life time stats -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-users"></i>Posted Cash Book
						</div>
						<div class="actions">
							<?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                <!--
							<a href="<?php echo base_url('cashier/cashbook/cashbook_form/1.tpd');?>" class="btn default yellow-stripe">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
							New Journal Entry </span>
							</a> -->
							<?php } ?>
						</div>
					</div>
					<div class="portlet-body table-responsive">
						<div class="table-container">
							<div class="col-md-12" style="padding-bottom: 100px;">
							<table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="table_cash_list">
							<thead>
							<tr role="row" class="heading">
								<th width="2%">
									 #
								</th>
								<th width="9%">
									 Entry No
								</th>
								<th >
									 Date
								</th>
                                <th >
                                     Type
                                </th>
								<th >
									 Description
								</th>
								<th width="15%" style="text-align: center">
									 Amount
								</th>
								<th width="10%">
									 Reff.
								</th>
								<th style="width:9%;">
									 Actions
								</th>
							</tr>
							<tr role="row" class="filter bg-grey-steel">
								<td>
								</td>
								<td>
									<input type="text" class="form-control form-filter input-sm" name="filter_journal_no">
								</td>
								<td>
									<div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter_journal_date_from" placeholder="From">
										<span class="input-group-btn">
										<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
									<div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter_journal_date_to" placeholder="To">
										<span class="input-group-btn">
										<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
								</td>
                                <td>
                                </td>
								<td>
                                    <input type="text" class="form-control form-filter input-sm" name="filter_remarks">
								</td>
								<td>

								</td>
								<td>
									<input type="text" class="form-control form-filter input-sm" name="filter_reff_no">
								</td>
								<td>
									<div class="text-center">
										<button class="btn btn-sm yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
										<button class="btn btn-sm red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
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
				<!-- End: life time stats -->
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
                orientation: "left",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

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
				src: $("#table_cash_list"),
				onSuccess: function (grid) {
					// execute some code after table records loaded
				},
				onError: function (grid) {
					// execute some code on network or other general error  
				},
				onDataLoad: function(grid) {
					// execute some code on ajax data load
				},
				loadingMessage: 'Populating...',
				dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 

					// Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
					// setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
					// So when dropdowns used the scrollable div should be removed. 
					"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
					
					"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
					"aoColumns": [
						{ "bSortable": false },
						{ "sClass": "text-center", "sWidth" : "10%" },
						{ "sClass": "text-center", "sWidth" : "13%" },
                        { "sClass": "text-center", "sWidth" : "8%" },
                        {"bSortable": false},
                        { "sClass": "text-right", "sWidth" : "15%" },
						{ "sClass": "text-center", "sWidth" : "12%" },
						{ "bSortable": false, "sClass": "text-center", "sWidth" : "9%"}
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, -1],
						[10, 20, 50, 100, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('cashier/cashbook/get_cashbook_list/' . STATUS_CLOSED . '/' . get_menu_id());?>", // ajax source
					}
				}
			});
			
			var tableWrapper = $("#table_cash_list_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});
			
		}
			
		handleRecords();

	});
</script>