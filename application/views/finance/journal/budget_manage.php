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
							<i class="fa fa-users"></i>Budget Estimation
						</div>
						<div class="actions">
                            <button class="btn btn-sm btn_copy margin-bottom red-flamingo" data-original-title="Copy Budget" data-placement="top" data-container="body"><i class="fa fa-copy"></i> Copy Budget</button>
                            <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
							<a href="javascript:;" class="btn default yellow-stripe btn_add_new">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
							Add Budget </span>
							</a>
							<?php } ?>
						</div>
					</div>
					<div class="portlet-body table-responsive">
                        <div class="table-container">
                            <div class="col-md-12" style="padding-bottom: 100px;">
                            <div class="table-actions-wrapper ">
                                <div class="col-md-1 "><label class="control-label" style="padding-top: 8px;"><strong>YEAR</strong></label>
                                </div>
                                <div class="col-md-1 col-xs-1 input-small" style="padding-left: 28px;">
                                    <div id="spinner1">
                                        <div class="input-group input-small" >
                                            <input type="text" class="spinner-input form-control" maxlength="4" name="budget_year" id="budget_year" readonly >
                                            <div class="spinner-buttons input-group-btn btn-group-vertical">
                                                <button type="button" class="btn spinner-up btn-xs blue">
                                                    <i class="fa fa-angle-up"></i>
                                                </button>
                                                <button type="button" class="btn spinner-down btn-xs blue">
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-bordered table-hover dataTable" id="table_budget">
                                <thead>
                                <tr role="row" class="heading">
                                    <th >
                                        Year
                                    </th>
                                    <th >
                                        GL Code
                                    </th>
                                    <th >
                                        Description
                                    </th>
                                    <th class="text-center">
                                        Budget Amount
                                    </th>
                                    <th style="width:9%;">
                                        Actions
                                    </th>
                                </tr>
                                <tr role="row" class="filter bg-grey-steel">
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <!-- div class="text-center">
                                            <button class="btn btn-xs yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                            <button class="btn btn-xs red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
                                        </div -->
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
		var handleRecords = function (year) {
            //year = $('#spinner1').spinner('value');
			grid.init({
				src: $("#table_budget"),
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
					//"bDestroy" : true,
					"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
						{ "bSortable": false, "sClass": "text-center", "sWidth" : "7%" },
                        { "bSortable": false , "sClass": "text-center" , "sWidth" : "11%"},
                        { "bSortable": false },
                        { "bSortable": false,"sClass": "text-right", "sWidth" : "12%" },
						{ "bSortable": false, "sClass": "text-center", "sWidth" : "9%"}
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, -1],
						[10, 20, 50, 100, "All"] // change per page values here
					],
					"pageLength": 20, // default record count per page
					"ajax": {
                        "url": "<?php echo base_url('finance/budget/get_budget_manage/' . get_menu_id()); ?>/" + year
					}
				}
			});

            var tableWrapper = $("#table_budget_wrapper");

			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});

            tableWrapper.find("#spinner1").spinner({
                value:year,
                min: 2013,
                max:(year + 1)
            });
		}

        handleRecords(<?php echo isset($budget_year) ? ($budget_year > 0 ? $budget_year : date("Y")) : date("Y"); ?>);

        $('.btn-bootbox').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');

            bootbox.confirm({
                message: "Are you sure to Delete ?",
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
                    if(!result){

                    }else{
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('finance/budget/ajax_delete_budget');?>",
                            dataType: "json",
                            data: { budget_id: id, action: action}
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

        var tableWrapper = $("#table_budget_wrapper");

        tableWrapper.find('#spinner1').on('change',function() {
            var year = $('#spinner1').spinner('value');
            //console.log('switch ' + year);

            if ( $.fn.dataTable.isDataTable('#table_budget' )) {
                $('#table_budget').dataTable().api().ajax.url("<?php echo base_url('finance/budget/get_budget_manage/' . get_menu_id()); ?>/" + year).load();
            }else{
                handleRecords(year);
            }

        });
	});

    $('.btn_add_new').on('click', function(){
        var year = $('#spinner1').spinner('value');
        var url="<?php echo base_url('finance/budget/budget_form');?>" + "/1/0/" + year + ".tpd";
        window.location.assign(url);
    })

    $('.btn_copy').on('click', function(){
        var year = $("#table_budget_wrapper").find('#spinner1').spinner('value');
        //console.log('copy ' + year);

        bootbox.confirm({
            message: "Are you sure to Copy Budget from " + (year-1) + " ?",
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
                if(!result){

                }else{
                    //console.log(result);
                    Metronic.blockUI({
                        boxed: true
                    });

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('finance/budget/ajax_copy_budget');?>",
                        dataType: "json",
                        data: { year: year}
                    })
                        .done(function( msg ) {
                            Metronic.unblockUI();

                            if(msg.type == '0' || msg.type == '1'){
                                if(msg.type == '1'){
                                    if ( $.fn.dataTable.isDataTable('#table_budget' )) {
                                        $('#table_budget').dataTable().api().ajax.url("<?php echo base_url('finance/budget/get_budget_manage/' . get_menu_id()); ?>/" + year).load();
                                    }

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
    })

</script>