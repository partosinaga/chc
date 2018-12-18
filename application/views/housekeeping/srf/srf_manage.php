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
							<i class="fa fa-users"></i>SRF Manage
						</div>
						<div class="actions">
                            <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
							<a href="<?php echo base_url('housekeeping/srf/srf_form/0.tpd');?>" class="btn default yellow-stripe">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
							New SRF </span>
							</a>
							<?php } ?>
						</div>
					</div>
					<div class="portlet-body table-responsive">
						<div class="table-container">
                            <div class="col-md-12" style="padding-bottom: 100px;">
							<table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="table_manage">
							<thead>
							<tr role="row" class="heading">
								<th width="2%" >

								</th>
								<th width="9%">
									 Doc No
								</th>
                                <th >
									 Date
								</th>
                                <th width="7%">
                                     Unit
                                </th>
								<th >
									 Request By
								</th>
								<th  >
									 Type
								</th>
                                <th>
                                    Status
                                </th>
                                <th style="width:9%;">
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
                                    <input type="text" class="form-control form-filter input-sm" name="filter_unit">
                                </td>
								<td>
                                    <input type="text" class="form-control form-filter input-sm" name="filter_request_by">
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
						{ "sClass": "text-center", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "9%" },
						{ "sClass": "text-center", "sWidth" : "9%" },
                        { "sClass": "text-center", "sWidth" : "10%" },
                        { "sClass": "text-left" },
                        { "sClass": "text-center", "sWidth" : "10%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "12%", "bSortable": false },
                        { "bSortable": false, "sClass": "text-center", "sWidth" : "9%"}
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, -1],
						[10, 20, 50, 100, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('housekeeping/srf/get_srf_manage/' . get_menu_id());?>"
					}
				}
			});
			
			var tableWrapper = $("#table_manage_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});
			
		}

		handleRecords();

        $('.btn-cancel').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            console.log('.btn-cancel '. id);

            bootbox.prompt({
                title: "Please enter cancel reason :",
                value: "",
                buttons: {
                    cancel: {
                        label: "No",
                        className: "btn-inverse"
                    },
                    confirm:{
                        label: "Yes",
                        className: "btn-primary"
                    }
                },
                callback: function(result) {
                    if(result === null){
                        //console.log('Empty reason');
                    }else if(result === ''){
                        toastr["warning"]("Cancel reason must be filled to proceed.", "Warning");
                    }else{
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('housekeeping/srf/action_request');?>",
                            dataType: "json",
                            data: { srf_id: id, action: action, reason:result }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();

                                if(msg.type == '0' || msg.type == '1'){

                                    grid.getDataTable().ajax.reload();
                                    grid.clearAjaxParams();

                                    if(msg.type == '1'){
                                        toastr["success"](msg.message, "Success");
                                    }
                                    else {
                                        toastr["warning"](msg.message, "Warning");
                                    }
                                }
                                else {
                                    toastr["error"]("Cancel failed, please try again later.", "Warning");
                                }
                            });
                    }
                }
            });
        });

        $('.btn-close').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var reason = '';

            bootbox.confirm("Complete this SRF ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        target: '.form-entry',
                        boxed: true,
                        message: 'Processing...'
                    });

                    var form_data = $('#form-entry').serializeArray();

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('housekeeping/srf/action_request');?>",
                        dataType: "json",
                        data: {srf_id : id, action : action, reason : reason}
                    })
                        .done(function (msg) {
                            $('.form-entry').unblock();
                            grid.getDataTable().ajax.reload();
                            grid.clearAjaxParams();

                            $.each(msg.valid, function (index, value) {
                                if (value == '0' || value == '1') {

                                    if (value == '1') {
                                        toastr["success"](msg.message[index], "Success");
                                    }
                                    else {
                                        toastr["error"](msg.message[index], "Error");
                                    }
                                }
                                else {
                                    toastr["error"]("Close failed, please try again later.", "Error");
                                }
                            });
                        })
                        .fail(function () {
                            $('.form-entry').unblock();
                            toastr["error"]("Close failed, please try again later.", "Error");
                        });
                }
            });
        });

	});

</script>