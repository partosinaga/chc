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
                            <i></i>&nbsp;Official Receipt
                            <div class="btn-group btn-group-devided">
                                <a href="javascript:;" class="btn btn-transparent grey-gallery btn-circle btn-sm active">Manage</a>
                                <a href="<?php echo base_url('ar/corporate_bill/receipt/2.tpd');?>" class="btn btn-transparent grey-gallery btn-circle btn-sm">History</a>
                            </div>
						</div>
						<div class="actions">
                            <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                <a href="<?php echo base_url('ar/corporate_bill/receipt/1/0.tpd');?>" class="btn default yellow-stripe">
                                    <i class="fa fa-plus"></i>
							<span class="hidden-480">
							Official Receipt </span>
                                </a>
                            <?php } ?>
						</div>
					</div>
					<div class="portlet-body table-responsive">
						<div class="table-container ">
                            <div class="col-md-12" style="padding-bottom: 100px;">
                            <table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="table_manage">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="2%" >

                                    </th>
                                    <th width="8%">
                                        Receipt No
                                    </th>
                                    <th width="7%">
                                        Date
                                    </th>
                                    <th >
                                        Client
                                    </th>
                                    <th width="11%">
                                        Payment Type
                                    </th>
                                    <th width="10%" >
                                        Receipt Amount
                                    </th>
                                    <th width="6%">
                                        Status
                                    </th>
                                    <th width="8%">
                                        Actions
                                    </th>
                                </tr>
                                <tr role="row" class="filter bg-grey-steel">
                                    <td style="vertical-align: middle;">

                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_no">
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_company">
                                    </td>
                                    <td>

                                    </td>
                                    <td>

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
                            <input type="hidden" name="posting_date" value="">
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
                        { "sClass": "text-center" },
                        { "sClass": "text-center"},
						{ "sClass": "text-left" },
                        { "sClass": "text-center"},
                        { "sClass": "text-right", "bSortable": false },
                        { "sClass": "text-center", "bSortable": false},
						{ "bSortable": false, "sClass": "text-center"}
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, -1],
						[10, 20, 50, 100, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('ar/corporate_bill/get_receipt_manage/' . get_menu_id());?>"
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