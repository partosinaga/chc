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
							<i class="fa fa-users"></i>Stock Adjustment <?php echo ($type == '0' ? 'Manage' : 'History');?>
						</div>
						<div class="actions">
							<?php
                                if(check_session_action(get_menu_id(), STATUS_NEW)){
                                    echo btn_new(base_url('inventory/adjustment/adj_form/0.tpd'), 'New Stock Adjustment');
                                }
                            ?>
						</div>
					</div>
					<div class="portlet-body table-responsive">
                        <div class="col-md-12" style="padding-bottom: 90px;">
						    <div class="table-container">
							    <table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th width="10px" class="text-center"> # </th>
                                            <th width="120px" class="text-center"> Adj. Code </th>
                                            <th width="120px" class="text-center"> Adj. Date </th>
                                            <th class="text-center"> Remarks </th>
                                            <th width="60px" class="text-center"> Status </th>
                                            <th width="50px" class="text-center"> Actions </th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td>&nbsp;</td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_adj_code">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm date date-picker margin-bottom-5" readonly name="filter_adj_date_from" placeholder="From" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm date date-picker" readonly name="filter_adj_date_to" placeholder="To" data-date-format="dd-mm-yyyy">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm " name="filter_remarks">
                                            </td>
                                            <td>
                                                <select class="form-control form-filter input-sm select2me" name="filter_status">
                                                    <option value="">All</option>
                                                    <?php
                                                    if ($type == '0') {
                                                        echo '<option value="' . STATUS_NEW . '">' . ucwords(strtolower(get_status_name(STATUS_NEW, false))) . '</option>';
                                                    } else {
                                                        echo '<option value="' . STATUS_POSTED . '">' . ucwords(strtolower(get_status_name(STATUS_POSTED, false))) . '</option>
                                                        <option value="' . STATUS_CANCEL . '">' . ucwords(strtolower(get_status_name(STATUS_CANCEL, false))) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <button class="btn btn-xs yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                                    <button class="btn btn-xs red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body" style="margin-right:0px;"><i class="fa fa-times"></i></button>
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
            "timeOut": "10000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        <?php echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>

		if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true
            });
        }

        var $modal_xtra_small = $('#ajax-modal-xtra-small');

        function init_page() {
            $(".mask_currency").inputmask("decimal", {
                radixPoint: ".",
                groupSeparator: ",",
                digits: 2,
                autoGroup: true,
                autoUnmask: true
            });
        }
        init_page();
		
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
						{ "bSortable": false, "sClass": "text-center" },    //No
						{ "sClass": "text-center" },                        //Adj Code
						{ "sClass": "text-center" },                        //Adj Date
                        { "bSortable": false },                             //Remarks
						{ "bSortable": false, "sClass": "text-center" },    //Status
						{ "bSortable": false, "sClass": "text-center" }     //Action
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('inventory/adjustment/ajax_adj_list/' . get_menu_id() . '/' . $type);?>"
					},
                    "fnDrawCallback": function( oSettings ) {
                        init_page();
                    }
				}
			});
			
			var tableWrapper = $("#datatable_ajax_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});
		}

		$('.btn-action-doc').live('click', function(){
			var data_id = $(this).attr('data-id');
			var action = $(this).attr('data-action');
            var data_code = $(this).attr('data-code');
            var action_code = $(this).attr('data-action-code');

            if(action == '<?php echo STATUS_CANCEL;?>'){
                bootbox.prompt({
                    title: "Please enter cancel reason for " + data_code + " :",
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
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('inventory/adjustment/ajax_adj_action');?>",
                                dataType: "json",
                                data: {adj_id: data_id, action: action, reason:result }
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();
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
                                        Metronic.unblockUI();
                                    }
                                }).fail(function(e){
                                    Metronic.unblockUI();
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                });
                        }
                    }
                });
            } else {
                bootbox.confirm("Are you sure want to Approve " + data_code + " ?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('inventory/adjustment/ajax_adj_action');?>",
                            dataType: "json",
                            data: {adj_id: data_id, action: action}
                        })
                            .done(function (msg) {
                                Metronic.unblockUI();
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
                                    Metronic.unblockUI();
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