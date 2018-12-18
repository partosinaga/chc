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
							<i class="fa fa-users"></i>Corporate Pending Bill (Checked Out)
						</div>
						<div class="actions">

						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container">
                            <table class="table table-striped table-bordered table-hover dataTable" id="table_manage">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="2%" >

                                    </th>
                                    <th >
                                        Bill Date
                                    </th>
                                    <th width="9%">
                                        Folio No
                                    </th>
                                    <th >
                                        Client
                                    </th>
                                    <th width="15%" >
                                        Amount
                                    </th>
                                    <th style="width:9%;">
                                        Actions
                                    </th>
                                </tr>
                                <tr role="row" class="filter bg-grey-steel">
                                    <td style="vertical-align: middle;">

                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_no">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_name">
                                    </td>
                                    <td>

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
				<!-- End: life time stats -->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
<!-- END CONTENT -->


<script>
	$(document).ready(function(){
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
				src: $("#table_manage"),
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
                        { "sClass": "text-center", "sWidth" : "9%" },
						{ "sClass": "text-center", "sWidth" : "12%" },
                        { "sClass": "text-left" },
                        { "sClass": "text-right", "sWidth" : "11%" },
						{ "bSortable": false, "sClass": "text-center", "sWidth" : "9%"}
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, -1],
						[10, 20, 50, 100, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('ar/corporate_bill/get_bill_manage/' . get_menu_id());?>"
					}
				}
			});
			
			var tableWrapper = $("#table_manage_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});
			
		}

		handleRecords();

	});

</script>