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
							<i class="fa fa-users"></i>User List
						</div>
						<div class="actions">
							<a href="<?php echo base_url('admin/user/user_manage/0.tpd');?>" class="btn default yellow-stripe">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
							New User </span>
							</a>
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container">
							
							<table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="datatable_ajax">
							<thead>
							<tr role="row" class="heading">
								<th width="2%">
									 #
								</th>
								<th width="10%">
									 Username
								</th>
								<th>
									 Full Name
								</th>
								<th width="15%">
									 Email
								</th>
								<th width="6%">
									 Dept.
								</th>
								<th style="width:6%;">
									 Admin
								</th>
								<th width="12%">
									 Last Login
								</th>
								<th style="width:9%;">
									 Status
								</th>
								<th style="width:8%;">
									 Actions
								</th>
							</tr>
							<tr role="row" class="filter bg-grey-steel">
								<td>
								</td>
								<td>
									<input type="text" class="form-control form-filter input-sm" name="filter_username">
								</td>
								<td>
									<input type="text" class="form-control form-filter input-sm" name="filter_fullname">
								</td>
								<td>
									<input type="text" class="form-control form-filter input-sm" name="filter_email">
								</td>
								<td>
									<select name="filter_dept" class="form-control form-filter input-sm select2me">
										<option value="">All</option>
										<?php
											$qry_dept = $this->mdl_general->get('ms_department', array('status >' => 0, 'status <>' => STATUS_DELETE), array(), 'department_name');
											foreach($qry_dept->result() as $row_dept){
												echo '<option value="' . $row_dept->department_id . '">' . $row_dept->department_name . '</option>';
											}
										?>
									</select>
								</td>
								<td>
								</td>
								<td>
									<div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter_date_from" placeholder="From">
										<span class="input-group-btn">
										<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
									<div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter_date_to" placeholder="To">
										<span class="input-group-btn">
										<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
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
			
		var initPickers = function () {
			//init date pickers
			$('.date-picker').datepicker({
				rtl: Metronic.isRTL(),
				autoclose: true
			});
		}

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
						{ "bSortable": false , "sClass": "text-center"},
						null,
						null,
						null,
						{ "sClass": "text-center" },
						{ "sClass": "text-center" },
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
						"url": "<?php echo base_url('admin/user/user_list');?>", // ajax source
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
					window.location.assign('<?php echo base_url('admin/user/user_delete');?>/' + id + '.tpd');
				}
			});
		});
			
		initPickers();
		handleRecords();
	});
</script>