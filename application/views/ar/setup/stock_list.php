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
							<i class="fa fa-list"></i>Product List(s) <span class="font-blue bold">Service Only</span>
						</div>
						<div class="actions ">
                            <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                <a href="<?php echo base_url('ar/setup/item_form')?>/0.tpd" class="btn default yellow-stripe">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-480">
                                New Service </span>
                                </a>
                            <?php } ?>
						</div>
					</div>
					<div class="portlet-body">
                        <div class="table-container">
							<table class="table table-striped table-bordered table-hover table-po-detail" id="datatable_stock">
							<thead>
							<tr role="row" class="heading">
                                <th>
									 Code
								</th>
                                <th>
                                     Description
                                </th>
								<th>
                                     Stock<br>UOM
								</th>
                                <!-- th>
                                     Dist.<br>UOM
                                </th -->
                                <th>
                                     COA
                                </th>
                                <th style="text-align: center;">
                                     Qty
                                </th>
                                <!-- th style="text-align: center;">
                                     Min
                                </th>
                                <th style="text-align: center;">
                                     Max
                                </th -->
                                <th style="text-align: center;">
                                     Price
                                </th>
                                <th>
                                     Disc<br>(%)
                                </th>
                                <th>
                                     Lock Price
                                </th>
                                <!-- th>
                                     AR Billed
                                </th -->
								<th style="width:9%;">
									 Status
								</th>
                                <th style="width:9%;">
									 Actions
								</th>
							</tr>
							<tr role="row" class="filter bg-grey-steel">
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="filter_itemcode">
								</td>
								<td>
                                    <input type="text" class="form-control form-filter input-sm" name="filter_itemdesc">
                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <!--td>

                                </td -->
                                <td>

                                </td>
                                <!-- td>

                                </td>
                                <td>

                                </td -->
                                <td >

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <!-- td>

                                </td -->
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
				src: $("#datatable_stock"),
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
					
					//"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
                    "aoColumns" : [
                        {sWidth: '10%', "sClass":"text-center", bSortable:true},
                        {"sClass":"text-left"},
                        {sWidth: '7%', "sClass":"text-center", bSortable:false},
                        //{sWidth: '4%', "sClass":"text-center", bSortable:false},
                        {sWidth: '7%' , "sClass":"text-center", bSortable:false},
                        {sWidth: '6%', "sClass":"text-center", bSortable:false},
                        //{sWidth: '4%' , "sClass":"text-center", bSortable:false},
                        //{sWidth: '4%' , "sClass":"text-center", bSortable:false},
                        {sWidth: '9%' , "sClass":"text-right", bSortable:false},
                        {sWidth: '6%' , "sClass":"text-center", bSortable:false},
                        {sWidth: '4%' , "sClass":"text-center", bSortable:false},
                        //{sWidth: '4%' , "sClass":"text-center", bSortable:false},
                        {sWidth: '8%' , "sClass":"text-center", bSortable:false},
                        {sWidth: '8%' , "sClass":"text-center", bSortable:false}
                    ],
					"pageLength": 20, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('ar/setup/get_stocks');?>"
					},
					"order": [
						[0, "asc"]
					]// set first column as a default sort by asc
				}
			});

            var tableWrapper = $("#datatable_stock_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            //$('select.select2me').select2();
		}

        //initPickers();
		handleRecords();

        $('.btn-delete').live('click', function(){
            var link = $(this).attr('data-link');
            bootbox.confirm("Are you sure?", function(result) {
                if (result === true) {
                    window.location.assign(link);
                }
            });
        });
	});

</script>