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
							<i class="fa fa-users"></i>Control Sheet
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive">
                            <div class="col-md-12">
							    <table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th> # </th>
                                        <th> PO Code </th>
                                        <th> PO Date </th>
                                        <th> Supplier </th>
                                        <th class="text-center"> Remarks </th>
                                        <th> PO Approval Date Receipt  </th>
                                        <th> Purchasing Date Receipt  </th>
                                        <th> Status </th>
                                        <th width="80px"> Actions </th>
                                    </tr>
                                    <tr role="row" class="filter bg-grey-steel">
                                        <td>&nbsp;</td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_po_code">
                                        </td>
                                        <td>
                                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm" readonly name="filter_po_date_from" placeholder="From">
                                                <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm" readonly name="filter_po_date_to" placeholder="To">
                                                <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_supplier">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_remarks">
                                        </td>
                                        <td>
                                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm" readonly name="filter_po_approval_date_receipt_from" placeholder="From">
                                                <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm" readonly name="filter_po_approval_date_receipt_to" placeholder="To">
                                                <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm" readonly name="filter_purchasing_date_receipt_from" placeholder="From">
                                                <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm" readonly name="filter_purchasing_date_receipt_to" placeholder="To">
                                                <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
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
            "timeOut": "5000",
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
                orientation: "right",
                todayHighlight: true,
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
                        { "bSortable": false },
                        { "sClass": "text-center" },
                        { "sClass": "text-center" },
						{ "sClass": "text-center" },
                        { "sClass": "text-center" },
						{ "bSortable": false, "sClass": "text-center" }
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('control_sheet/control_sheet/ajax_po_list/' . get_menu_id());?>" // ajax source
					},
                    "fnDrawCallback":function(){
                        $('.date-picker').datepicker({
                            rtl: Metronic.isRTL(),
                            orientation: "right",
                            todayHighlight: true,
                            autoclose: true
                        });
                    }
				}
			});
			
			var tableWrapper = $("#datatable_ajax_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});


		}

		$('.btn-save').live('click', function(){
            toastr.clear();
			var data_save = $(this).attr('data-save');
            var po_id = $(this).attr('data-id');

            var val = $(this).closest('tr').find("input[type='text']").val().trim();

            if(val != '') {
                bootbox.confirm("Are you sure?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('control_sheet/control_sheet/ajax_save');?>",
                            dataType: "json",
                            data: { data_save: data_save, po_id: po_id, val: val }
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
                                }
                            });
                    }
                });
            } else {
                toastr["warning"]("Please select date.", "Error");
            }
		});
			
		handleRecords();
	});
</script>