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
                            <i></i>&nbsp;Corporate Deposit
                            <div class="btn-group btn-group-devided">
                                <a href="javascript:;" class="btn btn-transparent grey-gallery btn-circle btn-sm active">Manage</a>
                                <a href="<?php echo base_url('ar/corporate_bill/deposit/2.tpd');?>" class="btn btn-transparent grey-gallery btn-circle btn-sm">History</a>
                            </div>
						</div>
						<div class="actions">
                            <!--a style="margin-left:20px;" href="javascript:;" class="btn-circle btn blue" id="posting-button"><i class="fa fa-save"></i>&nbsp;Posting</a-->
                            <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                <a href="<?php echo base_url('ar/corporate_bill/deposit/3/0.tpd');?>" class="btn default yellow-stripe">
                                    <i class="fa fa-plus"></i>
							<span class="hidden-480">
							New Deposit </span>
                                </a>
                            <?php } ?>

						</div>
					</div>
					<div class="portlet-body table-responsive">
						<div class="table-container">
                            <div class="col-md-12" style="padding-bottom: 100px;">
                          <form action="<?php echo base_url('ar/deposit/posting_deposits.tpd');?>" method="post" id="frmMain">
							<table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="table_deposit_manage">
							<thead>
							<tr role="row" class="heading">
								<th width="2%" >

								</th>
								<th >
									 Deposit No
								</th>
								<th >
									 Date
								</th>
                                <th >
                                     Client
                                </th>
                                <th >
                                     Payment Type
                                </th>
                                <th >
                                     Charges
                                </th>
                                <th >
                                     Amount
                                </th>
                                <th >
                                     Total
                                </th>
								<th style="width:9%;">
									 Actions
								</th>
							</tr>
							<tr role="row" class="filter bg-grey-steel">
								<td style="vertical-align: middle;">
                                    <input type="checkbox" id="checkall" />
								</td>
								<td>
									<input type="text" class="form-control form-filter input-sm" name="filter_no">
								</td>
								<td>

								</td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="filter_name">
                                </td>
                                <td>

                                </td>
                                <td>

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
                <div class="input-group date" data-date-format="dd-mm-yyyy">
                    <input type="text" class="form-control" name="c_posting_date" value="<?php echo (date('d-m-Y'));?>" readonly>
					<span class="input-group-btn hide">
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
				src: $("#table_deposit_manage"),
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
						{ "bSortable": false,"sWidth" : "3%" },
						{ "sClass": "text-center", "sWidth" : "9%" },
                        { "sClass": "text-center", "sWidth" : "8%" },
                        { "sClass": "text-left" },
                        { "sClass": "text-center", "sWidth" : "11%" },
						{ "sClass": "text-right", "sWidth" : "10%", "bSortable": false },
                        { "sClass": "text-right", "sWidth" : "9%" , "bSortable": false},
                        { "sClass": "text-right", "sWidth" : "10%" , "bSortable": false},
						{ "bSortable": false, "sClass": "text-center", "sWidth" : "9%"}
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, -1],
						[10, 20, 50, 100, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('ar/deposit/get_deposit_manage/' . get_menu_id());?>" // ajax source
					}
				}
			});
			
			var tableWrapper = $("#table_deposit_manage_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});
			
		}

		$('.btn-cancel').live('click', function(){
			var id = $(this).attr('data-id');
			var action = $(this).attr('data-action');

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
                            url: "<?php echo base_url('ar/deposit/action_request');?>",
                            dataType: "json",
                            data: { deposit_id: id, action: action, reason:result }
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
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                }
                            });
                    }
                }
            });
		});
			
		handleRecords();

	});

    $("#table_deposit_manage #checkall").click(function () {
        if ($("#table_deposit_manage #checkall").is(':checked')) {
            $("#table_deposit_manage input[type=checkbox]").each(function () {
                //$(this).attr("checked", "checked");
                $(this).prop("checked", true);
                $(this).parent('span').addClass('checked');
            });

        } else {
            $("#table_deposit_manage input[type=checkbox]").each(function () {
                $(this).prop("checked", false);
                //$(this).removeAttr("checked");
                $(this).parent('span').removeClass('checked');
            });
        }
    });

    $('#posting-button').click(function(e) {
        e.preventDefault();

        var checkedCount = $("#table_deposit_manage input[type=checkbox]:checked").length || 0;

        if(checkedCount > 0){
            $('#frmMain').submit();
        }

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