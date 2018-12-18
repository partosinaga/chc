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
				<!-- Begin: life time stats -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-users"></i>Stock Request List
						</div>
						<div class="actions">
							<?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
							<a href="<?php echo base_url('inventory/stock_request/stock_request_manage/0.tpd');?>" class="btn default yellow-stripe">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
							New Stock Request </span>
							</a>
							<?php } ?>
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive">
                            <div class="col-md-12" style="padding-bottom: 120px;">
                                <table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th width="2%"> # </th>
                                            <th width="10%"> Request Code </th>
                                            <th width="13%"> Request Date </th>
                                            <th width="10%"> Issue To </th>
                                            <th width="15%"> Request By </th>
                                            <th> Remarks </th>
                                            <th width="80px"> Status </th>
                                            <th width="80px"> Actions </th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td></td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_request_code">
                                            </td>
                                            <td>
                                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control form-filter input-sm" readonly name="filter_request_date_from" placeholder="From">
                                                    <span class="input-group-btn">
                                                    <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                    </span>
                                                </div>
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control form-filter input-sm" readonly name="filter_request_date_to" placeholder="To">
                                                    <span class="input-group-btn">
                                                    <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <select name="filter_dapertment_id" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                        foreach($qry_department->result() as $row_department){
                                                            echo '<option value="' . $row_department->department_id . '">' . $row_department->department_name . '</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_created_by">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_remarks">
                                            </td>
                                            <td>
                                                <select name="filter_status" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                        echo '<option value="' . STATUS_NEW . '">' . get_status_name(STATUS_NEW, false) . '</option>
                                                        <option value="' . STATUS_DISAPPROVE . '">' . get_status_name(STATUS_DISAPPROVE, false) . '</option>
                                                        <option value="' . STATUS_APPROVE . '">' . get_status_name(STATUS_APPROVE, false) . '</option>
                                                        <option value="' . STATUS_CLOSED . '">' . get_status_name(STATUS_CLOSED, false) . '</option>
                                                        <option value="' . STATUS_CANCEL . '">' . get_status_name(STATUS_CANCEL, false) . '</option>';
                                                    ?>
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

		if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "right",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
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
				dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 

					// Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
					// setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
					// So when dropdowns used the scrollable div should be removed. 
					"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
					
					"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
					"aoColumns": [
						{ "bSortable": false },
						{ "sClass": "text-center" },
						{ "sClass": "text-center" },
						null,
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
						"url": "<?php echo base_url('inventory/stock_request/ajax_stock_request_list/' . get_menu_id());?>", // ajax source
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
			var action = $(this).attr('data-action');
            var code = $(this).attr('data-code');

            if(action == '<?php echo STATUS_APPROVE;?>' || action == '<?php echo STATUS_DISAPPROVE;?>') {
                var act = '';
                if(action == '<?php echo STATUS_APPROVE;?>'){
                    act = 'Approve';
                } else if(action == '<?php echo STATUS_DISAPPROVE;?>'){
                    act = 'Disapprove';
                }
                bootbox.confirm("Are you sure want to " + act + " " + code + " ?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('inventory/stock_request/ajax_action_request');?>",
                            dataType: "json",
                            data: {request_id: id, action: action}
                        })
                            .done(function (msg) {
                                Metronic.unblockUI();

                                grid.getDataTable().ajax.reload();
                                grid.clearAjaxParams();

                                if (msg.valid == '1') {
                                    toastr["success"](msg.message, "Success");
                                }
                                else if (msg.valid == '0'){
                                    toastr["error"](msg.message, "Error");
                                }
                            })
                            .fail(function () {
                                Metronic.unblockUI();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });

                    }
                });
            } else if(action == '<?php echo STATUS_CANCEL;?>' || action == '<?php echo STATUS_CLOSED;?>'){
                var act = '';
                if(action == '<?php echo STATUS_CANCEL;?>'){
                    act = 'Cancel';
                } else if(action == '<?php echo STATUS_CLOSED;?>'){
                    act = 'Complete';
                }
                bootbox.prompt({
                    title: "Please enter Reason for " + act + " " + code + " :",
                    value: "",
                    buttons: {
                        cancel: {
                            label: "Cancel",
                            className: "btn-inverse"
                        },
                        confirm:{
                            label: "OK",
                            className: "btn-primary"
                        }
                    },
                    callback: function(result) {
                        if(result === null){ }
                        else if(result.length <= 5){
                            toastr["warning"]("Reason must be filled to proceed, Minimum 5 character.", "Warning");
                        } else {
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('inventory/stock_request/ajax_action_request');?>",
                                dataType: "json",
                                data: {request_id: id, action: action, reason:result }
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();

                                    grid.getDataTable().ajax.reload();
                                    grid.clearAjaxParams();

                                    if (msg.valid == '1') {
                                        toastr["success"](msg.message, "Success");
                                    } else if (msg.valid == '1') {
                                        toastr["error"](msg.message, "Error");
                                    }
                                })
                                .fail(function () {
                                    Metronic.unblockUI();
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                });
                        }
                    }
                });
            }
		});
			
		handleRecords();

        <?php
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
        ?>
	});
</script>