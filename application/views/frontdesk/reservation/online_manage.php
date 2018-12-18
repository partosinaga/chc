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
							<i class="fa fa-users"></i>Waiting for Settlement
						</div>
						<div class="actions">
                            <a style="margin-left:20px;" href="javascript:;" class="btn-circle btn green-seagreen" id="posting-button"><i class="fa fa-save"></i>&nbsp;Submit Reservation</a>
						</div>
					</div>
					<div class="portlet-body table-responsive">
                        <div class="col-md-12" style="padding-bottom: 70px;">
						<div class="table-container">
                          <form action="<?php echo base_url('frontdesk/reservation/posting_booking.tpd');?>" method="post" id="frmMain">
							<table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="table_booking_manage">
							<thead>
							<tr role="row" class="heading">
								<th width="2%" >

								</th>
                                <th>
                                    Booking No
                                </th>
                                <th >
                                    Date
                                </th>
                                <th >
                                    Guest
                                </th>
                                <th >
                                    Type
                                </th>
                                <th >
                                    Room
                                </th>
                                <th >
                                    Check In
                                </th>
                                <th >
                                    Check Out
                                </th>
                                <th >
                                    VT Status
                                </th>
                                <th >
                                    Code
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

<script>
	$(document).ready(function(){
		if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

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
				src: $("#table_booking_manage"),
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
                        { "sWidth" : "2%", "sClass": "text-center", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "9%" },
                        { "sClass": "text-center", "sWidth" : "8%" },
                        null,
                        { "sClass": "text-center", "sWidth" : "8%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "9%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "7%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "7%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "8%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "7%", "bSortable": false },
                        { "bSortable": false, "sClass": "text-center", "sWidth" : "9%"}
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, -1],
						[10, 20, 50, 100, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
                        "url": "<?php echo base_url('frontdesk/reservation/get_online_manage/' . get_menu_id());?>"
					}
				}
			});
			
			var tableWrapper = $("#table_booking_manage_wrapper");

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
                            url: "<?php echo base_url('frontdesk/reservation/action_request');?>",
                            dataType: "json",
                            data: { reservation_id: id, action: action, reason:result }
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
                                    toastr["error"]("Action can not be processed, please try again later.", "Error");
                                }
                            });
                    }
                }
            });
        });

        handleRecords();

        $('.btn-check-vt').live('click', function(){
            var id = $(this).attr('data-id');
            var code = $(this).attr('data-code');

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('frontdesk/reservation/veritrans_status');?>",
                dataType: "json",
                data: { reservation_code: code}
            })
                .done(function( msg ) {
                    //Metronic.unblockUI();
                    if(msg.type == '0' || msg.type == '1'){
                        console.log(msg.message);

                        var result = '';
                        var toastr_class = 'info';
                        $.each(msg.message, function( key, value ) {
                            if(key == 'transaction_status' || key == 'gross_amount' || key == 'status_code' ||
                               key == 'order_id' || key == 'transaction_time'){
                                result += '<span style="text-decoration: underline;">' + key + '</span> : <span style="text-decoration: underline;"><strong>' + value + '</strong><br></span>';

                            }else{
                                result += key + ' : ' + value + '<br>';
                            }

                            if(key == 'status_code' && value != '200'){
                                toastr_class = "error";
                            }
                        });

                        toastr[toastr_class](result, "Veritrans Response");

                    }
                    else {
                        toastr["error"]("Action can not be processed, please try again later.", "Error");
                    }
                });

        });

	});

    $("#table_booking_manage #checkall").click(function () {
        if ($("#table_booking_manage #checkall").is(':checked')) {
            $("#table_booking_manage input[type=checkbox]").each(function () {
                //$(this).attr("checked", "checked");
                $(this).prop("checked", true);
                $(this).parent('span').addClass('checked');
            });

        } else {
            $("#table_booking_manage input[type=checkbox]").each(function () {
                $(this).prop("checked", false);
                //$(this).removeAttr("checked");
                $(this).parent('span').removeClass('checked');
            });
        }
    });

    $('#posting-button').click(function(e) {
        e.preventDefault();

        var checkedCount = $("#table_booking_manage input[type=checkbox]:checked").length || 0;

        if(checkedCount > 0){
            $('#frmMain').submit();
        }else{
            toastr["warning"]("No record(s) selected.", "Warning");
        }
    });

</script>