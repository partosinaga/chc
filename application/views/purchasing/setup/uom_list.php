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
							<i class="fa fa-users"></i>UOM List
						</div>
						<div class="actions">
							<?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
							<a href="<?php echo base_url('purchasing/setup/uom_manage/0.tpd');?>" class="btn default yellow-stripe">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
							New UOM </span>
							</a>
							<?php } ?>
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container">
							
							<table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
							<thead>
							<tr role="row" class="heading">
								<th>
									 #
								</th>
								<th>
									 UOM Code
								</th>
								<th>
									 Description
								</th>
								<th style="width:9%;">
									 Status
								</th>
								<th style="width:9%;">
									 Actions
								</th>
							</tr>
							<tr role="row" class="filter bg-grey-steel">
								<td>
								</td>
								<td>
									<input type="text" class="form-control form-filter input-sm" name="filter_uomcode">
								</td>
								<td>
									<input type="text" class="form-control form-filter input-sm" name="filter_uomdesc">
								</td>
								<td>
									<select name="filter_status" class="form-control form-filter input-sm select2me">
										<option value="">All</option>
										<option value="<?php echo STATUS_NEW;?>">Active</option>
										<option value="<?php echo STATUS_INACTIVE;?>">Inactive</option>
									</select>
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
		var handleRecords = function () {

			var grid = new Datatable();

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
				dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 

					// Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
					// setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
					// So when dropdowns used the scrollable div should be removed. 
					//"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
					
					"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
					"aoColumns": [
						{ "bSortable": false },
						null,
						null,
						{ "sClass": "text-center" },
						{ "bSortable": false, "sClass": "text-center" }
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": 10, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('purchasing/setup/uom_list/' . get_menu_id());?>", // ajax source
					},
					"fnDrawCallback": function( oSettings ) {
						$('.tooltips').tooltip();
					}
				}
			});
			
			var tableWrapper = $("#datatable_ajax_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});
			
		}
		
		
		$('.btn-bootbox').live('click', function(){
			var id = $(this).attr('data-id');
			bootbox.confirm("Are you sure?", function(result) {
				if(result == true){
					window.location.assign('<?php echo base_url('purchasing/setup/uom_delete');?>/' + id + '.tpd');
				}
			});
		});
			
		handleRecords();
	});
</script>