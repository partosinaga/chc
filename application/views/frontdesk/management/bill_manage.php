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
                <?php //echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
				<!-- Begin: life time stats -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
                            <i></i>&nbsp;Other Charges
                            <div class="btn-group btn-group-devided">
                                <a href="<?php echo base_url('frontdesk/billing/bill_manage/1.tpd');?>" class="btn btn-transparent grey-gallery btn-circle btn-sm <?php echo($is_history ? '' : 'active'); ?>">Manage</a>
                                <a href="<?php echo base_url('frontdesk/billing/bill_manage/2.tpd');?>" class="btn btn-transparent grey-gallery btn-circle btn-sm <?php echo($is_history ? 'active' : ''); ?>">History</a>
                            </div>


						</div>
						<div class="actions">
                            <?php
                                if(!$is_history && check_session_action(get_menu_id(), STATUS_POSTED)){
                                    echo '<a style="margin-left:20px;" href="javascript:;" class="btn-primary btn blue btn-circle" id="posting-button"><i class="fa fa-save"></i>&nbsp;Posting</a>&nbsp;';
                                }
                                if(check_session_action(get_menu_id(), STATUS_NEW)) {
                                    //ORDER_STATUS::CHECKIN
                                    echo '<a href="' . base_url('frontdesk/billing/bill_manage/3/0.tpd') . '" class="btn default yellow-stripe">
                                    <i class="fa fa-plus"></i>
                                    <span class="hidden-480"> New Bill </span>
                                    </a>';
                                }
							?>
						</div>
					</div>
					<div class="portlet-body table-responsive">
						<div class="table-container">
                            <div class="col-md-12" style="padding-bottom: 100px;">
                            <form id="form-entry" onsubmit="return false;">
                            <table class="table table-striped table-bordered table-hover dataTable form-entry table-po-detail" id="table_bill_manage">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="2%"></th>
                                    <th>Bill Date</th>
                                    <th>Bill No</th>
                                    <th>Folio</th>
                                    <th>Type</th>
                                    <th>Guest</th>
                                    <th>Company</th>
                                    <th>Charge To</th>
                                    <th>Amount</th>
                                    <th style="width:90px;">Actions</th>
                                </tr>
                                <tr role="row" class="filter bg-grey-steel">
                                    <td style="vertical-align: middle;">
                                        <?php
                                        if (!$is_history){
                                            echo '<input type="checkbox" id="checkall" ' . (check_session_action(get_menu_id(), STATUS_POSTED) ? '' : 'disabled') . ' />';
                                        }
                                        ?>
                                    </td>
                                    <td></td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_bill_no">
                                    </td>
                                    <td><input type="text" class="form-control form-filter input-sm" name="filter_reservation_code"></td>
                                    <td></td>
                                    <td><input type="text" class="form-control form-filter input-sm" name="filter_name"></td>
                                    <td><input type="text" class="form-control form-filter input-sm" name="filter_company_name"></td>
                                    <td></td>
                                    <td></td>
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
                            </form>
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

<div id="ajax-calendar" class="modal fade bs-modal-sm"  data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Posting date :</h4>
            </div>
            <div class="modal-body">
                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                    <input type="text" class="form-control" name="c_posting_date" value="<?php echo (date('d-m-Y'));?>" readonly>
					<span class="input-group-btn">
						<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
					</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue" id="submit-posting">Posting</button>
            </div>
        </div>
        <!-- /.modal-content -->
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
            "extendedTimeOut": "10000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        <?php
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
        ?>

		var grid = new Datatable();
		
		var handleRecords = function () {

			grid.init({
				src: $("#table_bill_manage"),
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
					
					"bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
					"aoColumns": [
						{ "bSortable": false },
                        { "sClass": "text-center" },
                        { "sClass": "text-center" , "sWidth" : "9%"},
                        { "sClass": "text-center", "sWidth" : "10%"},
                        { "sClass": "text-center", "bSortable": false },
                        { "bSortable": false },
                        { "bSortable": false },
                        { "sClass": "text-center", "bSortable": false },
                        { "sClass": "text-right" , "bSortable": false ,"sWidth" : "10%"},
                        { "bSortable": false, "sClass": "text-center", "sWidth" : "9%"}
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, -1],
						[10, 20, 50, 100, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('frontdesk/billing/get_bill_manage/' . get_menu_id() . '/' . ($is_history ? '1' : '0'));?>" // ajax source
					}
				}
			});
			
			var tableWrapper = $("#table_bill_manage_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});
			
		}

		$('.btn-bootbox').live('click', function(){
			var id = $(this).attr('data-id');
			var action = $(this).attr('data-action');
            var code = $(this).attr('data-code');

            if (action == '<?php echo STATUS_CANCEL;?>') {
                bootbox.prompt({
                    title: "Please enter cancel reason :",
                    value: "",
                    buttons: {
                        cancel: {
                            label: "No",
                            className: "btn-inverse"
                        },
                        confirm: {
                            label: "Yes",
                            className: "btn-primary"
                        }
                    },
                    callback: function (result) {
                        if (result === null) {
                            //console.log('Empty reason');
                        } else if (result === '') {
                            toastr["warning"]("Cancel reason must be filled to proceed.", "Warning");
                        } else {
                            //console.log(result);
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('frontdesk/billing/ajax_action');?>",
                                dataType: "json",
                                data: {bill_id: id, action: action, reason: result}
                            })
                                .done(function (msg) {
                                    Metronic.unblockUI();

                                    if (msg.valid == '0' || msg.valid == '1') {

                                        grid.getDataTable().ajax.reload();
                                        grid.clearAjaxParams();

                                        if (msg.valid == '1') {
                                            toastr["success"](msg.message, "Success");
                                        }
                                        else {
                                            toastr["warning"](msg.message, "Warning");
                                        }
                                    }
                                    else {
                                        toastr["error"]("Something has wrong, please try again later.", "Error");
                                    }
                                })
                                .fail(function(){
                                    Metronic.unblockUI();
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                });
                        }
                    }
                });
            } else if (action == '<?php echo STATUS_POSTED;?>') {
                bootbox.confirm("Are you sure want to Posting " + code + " ?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('frontdesk/billing/ajax_action');?>",
                            dataType: "json",
                            data: {bill_id: id, action: action}
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
                            .fail(function(){
                                Metronic.unblockUI();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                });
            }
		});
			
		handleRecords();

        $("#table_bill_manage #checkall").click(function () {
            if ($("#table_bill_manage #checkall").is(':checked')) {
                $("#table_bill_manage input[type=checkbox]").each(function () {
                    //$(this).attr("checked", "checked");
                    $(this).prop("checked", true);
                    $(this).parent('span').addClass('checked');
                });

            } else {
                $("#table_bill_manage input[type=checkbox]").each(function () {
                    $(this).prop("checked", false);
                    //$(this).removeAttr("checked");
                    $(this).parent('span').removeClass('checked');
                });
            }
        });

        $('#posting-button').click(function(e) {
            var tot = $('.checked_posting:checked').size();

            if (tot > 0) {
                bootbox.confirm("Are you sure want to Posting selected Billing ?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            target: '.form-entry',
                            boxed: true,
                            message: 'Processing...'
                        });

                        var form_data = $('#form-entry').serializeArray();

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('frontdesk/billing/ajax_bill_multi_posting');?>",
                            dataType: "json",
                            data: form_data
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
                                        toastr["error"]("Something has wrong, please try again later.", "Error");
                                    }
                                });
                            })
                            .fail(function () {
                                $('.form-entry').unblock();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                });
            } else {
                toastr["warning"]("Please check bill.", "Warning");
            }

            e.preventDefault();
        });

	});



    $('#posting-button').click(function(e) {
        e.preventDefault();

        $('#frmMain').submit();
    });

    $('#submit-posting').click(function(e) {
        e.preventDefault();

        var $modal_cal = $('#ajax-calendar');

        if ($modal_cal.hasClass('bootbox') == false) {
            $modal_cal.addClass('modal-fix');
        }

        $modal_cal.modal();

        //$('#frmMain').submit();
    });

</script>