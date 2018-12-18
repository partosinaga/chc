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
                <?php
                    echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
                ?>
				<!-- Begin: life time stats -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-list"></i>Agent List
						</div>
						<div class="actions">
							<a href="<?php echo base_url('frontdesk/setup/agent_manage')?>/0.tpd" class="btn default yellow-stripe">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
							New Agent </span>
							</a>

						</div>
					</div>
					<div class="portlet-body">
                        <div class="table-container">
							<table class="table table-striped table-bordered table-hover table-po-detail" id="datatable_ajax">
							<thead>
							<tr role="row" class="heading">
								<th>
									 #
								</th>
								<th>
									 Agent
								</th>
                                <th>
                                     PIC
                                </th>
                                <th>
                                     Phone
                                </th>
								<th>
									 Email
								</th>
                                <th>
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
									<input type="text" class="form-control form-filter input-sm" name="filter_agent_name">
								</td>
								<td>
									<input type="text" class="form-control form-filter input-sm" name="filter_agent_pic">
								</td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>&nbsp;</td>
                                <!--<td>
									<select name="filter_status" class="form-control form-filter input-sm">
										<option value="">Select...</option>
										<option value="<?php echo STATUS_NEW;?>">Active</option>
										<option value="<?php echo STATUS_INACTIVE;?>">Inactive</option>
									</select>
								</td>-->
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
                loadingMessage: 'Populating ...',
				dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 

					// Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
					// setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
					// So when dropdowns used the scrollable div should be removed. 
					"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
					
					"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
                    "aoColumns" : [
                        {sWidth: '4%', bSortable:false, sClass:"text-center"},
                        null,
                        null,
                        {sWidth: '20%', bSortable:false},
                        {sWidth: '20%', bSortable:false},
                        {sClass:"text-center"},
                        {sWidth: '9%' , "sClass":"text-center", bSortable:false}
                    ],
					"pageLength": 10, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('frontdesk/setup/agent_list/'. get_menu_id());?>"
					},
					"order": [
						[1, "asc"]
					]// set first column as a default sort by asc
				}
			});
			
			var tableWrapper = $("#datatable_ajax_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});

		}

        //initPickers();
		handleRecords();

        $('.btn-remove').live('click', function(){
            var link = $(this).attr('data-link');
            bootbox.confirm("Are you sure?", function(result) {
                if (result === true) {
                    window.location.assign(link);
                }
            });
        });

	});

</script>