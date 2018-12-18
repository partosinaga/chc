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
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-users"></i>Stock Issue <?php echo ($type == '0' ? 'Manage' : 'History');?>
						</div>
						<div class="actions">
							<?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                <a href="<?php echo base_url('inventory/stock_issue/issue_form/0.tpd');?>" class="btn default yellow-stripe">
                                    <i class="fa fa-plus"></i>
                                    <span class="hidden-480"> New Stock Issue </span>
                                </a>
							<?php } ?>
						</div>
					</div>
					<div class="portlet-body table-responsive">
                        <div class="col-md-12" style="padding-bottom: 80px;">
						    <div class="table-container">
							<table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th width="1%" class="text-center"> # </th>
                                        <th width="12%" class="text-center"> Issue Code </th>
                                        <th width="15%" class="text-center"> Issue Date </th>
                                        <th width="12%" class="text-center"> Request Code </th>
                                        <th width="9%" class="text-center"> Dept. </th>
                                        <th class="text-center"> Remarks </th>
                                        <th width="9%" class="text-center"> Status </th>
                                        <th width="80px"> Actions </th>
                                    </tr>
                                    <tr role="row" class="filter bg-grey-steel">
                                        <td>&nbsp;</td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_gi_code">
                                        </td>
                                        <td>
                                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm" readonly name="filter_gi_date_from" placeholder="From">
                                                <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm" readonly name="filter_gi_date_to" placeholder="To">
                                                <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_request_code">
                                        </td>
                                        <td>
                                            <select class="form-control form-filter input-sm select2me" name="filter_dept">
                                                <option value="">All</option>
                                                <?php
                                                $qry_dept = $this->mdl_general->get('ms_department', array('status' => STATUS_NEW), array(), 'department_name ASC');
                                                foreach($qry_dept->result() as $row_dept){
                                                    echo '<option value="' . $row_dept->department_id . '">' . $row_dept->department_name . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_remarks">
                                        </td>
                                        <td>&nbsp;</td>
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
            "timeOut": "50000",
            //"extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        <?php echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>

		if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "right",
                autoclose: true
            });
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
					"bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
					"aoColumns": [
						{ "bSortable": false },
						{ "sClass": "text-center" },
						{ "sClass": "text-center" },
                        { "sClass": "text-center" },
                        { "sClass": "text-center", "bSortable": false },
                        { "bSortable": false },
						{ "sClass": "text-center", "bSortable": false },
						{ "bSortable": false, "sClass": "text-center" }
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('inventory/stock_issue/ajax_issue_list/' . get_menu_id() . '/' . $type);?>" // ajax source
					}
				}
			});
			
			var tableWrapper = $("#datatable_ajax_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});
		}

		$('.btn-action-doc').live('click', function(){
			var doc_id = $(this).attr('data-id');
			var action = $(this).attr('data-action');
            var doc_code = $(this).attr('data-code');
            var action_code = $(this).attr('data-action-code');

            if(action == '<?php echo STATUS_CANCEL;?>'){
                bootbox.prompt({
                    title: "Please enter cancel reason :",
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
                            toastr["warning"]("Cancel reason must be filled to proceed, Minimum 5 character.", "Warning");
                        } else {
                            //console.log(result);
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('inventory/stock_issue/ajax_action_issue');?>",
                                dataType: "json",
                                data: {gi_id: doc_id, action: action, reason:result }
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();
                                    console.log(msg);
                                    if (msg.valid == '0' || msg.valid == '1') {
                                        grid.getDataTable().ajax.reload();
                                        grid.clearAjaxParams();

                                        if (msg.valid == '1') {
                                            toastr["success"](msg.message, "Success");
                                        } else {
                                            toastr["error"](msg.message, "Error");
                                        }
                                    } else {
                                        toastr["error"]("Something has wrong, please try again later.", "Error");
                                    }
                                })
                                .fail(function () {
                                    Metronic.unblockUI();
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                });
                        }
                    }
                });
            } else {
                bootbox.confirm("Are you sure want to " + action_code + " " + doc_code + " ?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('inventory/stock_issue/ajax_action_issue');?>",
                            dataType: "json",
                            data: {gi_id: doc_id, action: action}
                        })
                            .done(function (msg) {
                                Metronic.unblockUI();
                                console.log(msg);
                                if (msg.valid == '0' || msg.valid == '1') {
                                    grid.getDataTable().ajax.reload();
                                    grid.clearAjaxParams();

                                    if (msg.valid == '1') {
                                        toastr["success"](msg.message, "Success");
                                    } else {
                                        toastr["error"](msg.message, "Error");
                                    }
                                } else {
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                }
                            })
                            .fail(function () {
                                Metronic.unblockUI();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                });
            }
		});
			
		handleRecords();
	});
</script>