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
							<i class="fa fa-users"></i>Payment <?php echo ($type == '0' ? 'Manage' : 'History');?>
						</div>
						<div class="actions">
							<?php
                                if(check_session_action(get_menu_id(), STATUS_NEW)){
                                    echo btn_new(base_url('ap/payment/payment_form/0.tpd'), 'New Payment');
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
                                            <th width="70px" class="text-center"> Pay. Code </th>
                                            <th width="70px" class="text-center"> Pay. Date </th>
                                            <th width="130px" class="text-center"> Supplier </th>
                                            <th width="70px" class="text-center"> Bank </th>
                                            <th width="40px" class="text-center"> Curr </th>
                                            <th width="80px" class="text-center"> Amount </th>
                                            <th class="text-center"> Remarks </th>
                                            <th width="60px" class="text-center"> Status </th>
                                            <th width="50px" class="text-center"> Actions </th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td>&nbsp;</td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_payment_code">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm date date-picker margin-bottom-5" readonly name="filter_payment_date_from" placeholder="From" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm date date-picker" readonly name="filter_payment_date_to" placeholder="To" data-date-format="dd-mm-yyyy">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_supplier">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_bank_code">
                                            </td>
                                            <td>
                                                <select class="form-control form-filter input-sm select2me" name="filter_curr">
                                                    <option value="">All</option>
                                                    <?php
                                                    $qry_curr = $this->db->get('currencytype');
                                                    foreach ($qry_curr->result() as $row_curr) {
                                                        echo '<option value="' . $row_curr->currencytype_id . '">' . $row_curr->currencytype_code . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm mask_currency" name="filter_amount">
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

<div class="modal fade bs-modal-sm" id="ajax-modal-xtra-small" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Posting Date (<span class="m_payment_code"></span>)</h4>
            </div>
            <div class="modal-body">
                <form role="form" onsubmit="return false">
                    <div class="form-group">
                        <label>Posting Date</label> (<span class="m_payment_code"></span>)
                        <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                            <input type="text" class="form-control" name="posting_date" id="posting_date" value="" readonly>
                            <span class="input-group-btn">
                                <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn blue" id="btn_posting" data-dismiss="modal">Posting</button>
            </div>
        </div>
    </div>
</div>

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
						{ "sClass": "text-center" },                        //Payment Code
						{ "sClass": "text-center" },                        //Payment Date
                        null,                                               //Supplier
                        { "sClass": "text-center" },                        //Bank
                        { "sClass": "text-center" },                        //Curr
                        { "sClass": "text-right" },                         //Amount
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
						"url": "<?php echo base_url('ap/payment/ajax_payment_list/' . get_menu_id() . '/' . $type);?>"
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
            var data_date = $(this).attr('data-date');
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
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('ap/payment/ajax_payment_action');?>",
                                dataType: "json",
                                data: {payment_id: data_id, action: action, reason:result }
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
                $modal_xtra_small.modal('show');

                $('#posting_date').val(data_date);
                $('#btn_posting').attr('data-id', data_id);
                $('#btn_posting').attr('data-action', action);
                $('.m_payment_code').html(data_code)
            }
		});
			
		handleRecords();

        $('#btn_posting').live('click', function() {
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var action_date = $('#posting_date').val();

            Metronic.blockUI({
                boxed: true
            });

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('ap/payment/ajax_payment_action');?>",
                dataType: "json",
                data: {payment_id: id, action: action, action_date: action_date}
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
                }).fail(function(e){
                    Metronic.unblockUI();
                    toastr["error"]("Something has wrong, please try again later.", "Error");
                });
        });
	});
</script>