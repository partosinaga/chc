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
							<i class="fa fa-users"></i>PR List
						</div>
						<div class="actions">
							<?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
							<a href="<?php echo base_url('purchasing/pr/pr_manage/0.tpd');?>" class="btn default yellow-stripe">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
							New PR </span>
							</a>
							<?php } ?>
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 90px;">
                                <table class="table table-striped table-bordered table-hover dataTable" id="datatable_pr" >
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th class="text-center" width="1%"> # </th>
                                            <th class="text-center" width="10%"> PR No </th>
                                            <th class="text-center" width="11%"> Prepare Date </th>
                                            <th class="text-center"> Item Description </th>
                                            <th class="text-center" width="11%"> Delivery Req </th>
                                            <th class="text-center" width="10%"> Dept. </th>
                                            <th class="text-center" width="10%"> Status </th>
                                            <th class="text-center" width="60px"> Action </th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td></td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_prno">
                                            </td>
                                            <td>
                                                <input type="text" class="text-center form-control form-filter input-sm date-picker margin-bottom-5" name="filter_preparedate_from" data-date-format="dd-mm-yyyy" readonly />
                                                <input type="text" class="text-center form-control form-filter input-sm date-picker" name="filter_preparedate_to" data-date-format="dd-mm-yyyy" readonly />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_itemdesc">
                                            </td>
                                            <td>
                                                <input type="text" class="text-center form-control form-filter input-sm date-picker margin-bottom-5" name="filter_deliveryreq_from" data-date-format="dd-mm-yyyy" readonly />
                                                <input type="text" class="text-center form-control form-filter input-sm date-picker" name="filter_deliveryreq_to" data-date-format="dd-mm-yyyy" readonly />
                                            </td>
                                            <td>
                                                <select name="filter_department_id" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                        foreach($qry_department->result() as $row_dept){
                                                            echo '<option value="' . $row_dept->department_id . '">' . $row_dept->department_name . '</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="filter_status" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <option value="<?php echo STATUS_NEW;?>">New</option>
                                                    <option value="<?php echo STATUS_APPROVE;?>">Approved</option>
                                                    <option value="<?php echo STATUS_DISAPPROVE;?>">Disapproved</option>
                                                    <option value="<?php echo STATUS_CLOSED;?>">Closed</option>
                                                    <option value="<?php echo STATUS_CANCEL;?>">Canceled</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <button class="btn btn-xs yellow filter-submit tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                                    <button class="btn btn-xs red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
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
        };

        var grid = new Datatable();

		var handleRecords = function () {
			grid.init({
				src: $("#datatable_pr"),
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
                        { "bSortable": false },
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
					"pageLength": 10, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('purchasing/pr/pr_list/'. get_menu_id());?>" // ajax source
					},
					"fnDrawCallback": function( oSettings ) {
						$('.tooltips').tooltip();
					}
				}
			});

            var tableWrapper = $("#datatable_pr_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });
			
		}

		handleRecords();

        $('.btn-cancel').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var doc_no = $(this).attr('data-code');

            bootbox.prompt({
                title: "Please enter Cancel reason for " + doc_no + " :",
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
                            url: "<?php echo base_url('purchasing/pr/action_request');?>",
                            dataType: "json",
                            data: { pr_id: id, action: action, reason:result }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();
                                if(msg.type == '0' || msg.type == '1'){
                                    //console.log(msg.type);
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

        $('.btn-approve').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var doc_no = $(this).attr('data-code');

            bootbox.confirm({
                message: "Approve Purchase Request " + doc_no + " ?",
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
                    console.log(result);
                    if(result === false){
                        //console.log('Empty reason');
                    }else{
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('purchasing/pr/action_request');?>",
                            dataType: "json",
                            data: { pr_id: id, action: action }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();
                                if(msg.type == '0' || msg.type == '1'){
                                    //console.log(msg.type);
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

        $('.btn-reject').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var doc_no = $(this).attr('data-code');

            bootbox.confirm({
                message: "Disapprove Purchase Request " + doc_no + " ?",
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
                    console.log(result);
                    if(result === false){
                        //console.log('Empty reason');
                    }else{
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('purchasing/pr/action_request');?>",
                            dataType: "json",
                            data: { pr_id: id, action: action }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();
                                if(msg.type == '0' || msg.type == '1'){
                                    //console.log(msg.type);
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

        $('.btn-close').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var doc_no = $(this).attr('data-code');

            bootbox.prompt({
                title: "Please enter Complete reason for " + doc_no + " :",
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
                        toastr["warning"]("Complete reason must be filled to proceed.", "Warning");
                    }else{
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('purchasing/pr/action_request');?>",
                            dataType: "json",
                            data: { pr_id: id, action: action, reason:result }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();
                                if(msg.type == '0' || msg.type == '1'){
                                    //console.log(msg.type);
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

	});
</script>