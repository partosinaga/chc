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
			<div class="col-md-12" >
				<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
				<!-- Begin: life time stats -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-users"></i>Report Purchase Progress
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <table class="table table-striped table-bordered table-hover dataTable table-small table-po-detail" id="datatable_pr_progress" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center" width="75px"> PR NO </th>
                                            <th class="text-center" width="80px"> PR DATE </th>
                                            <th class="text-center"> ITEM </th>
                                            <th class="text-center" width="60px"> UOM </th>
                                            <th class="text-center" width="45px"> QTY </th>
                                            <th class="text-center" width="75px"> PO NO </th>
                                            <th class="text-center" width="80px"> PO DATE </th>
                                            <th class="text-center" width="60px"> UOM </th>
                                            <th class="text-center" width="45px"> QTY </th>
                                            <th class="text-center" width="75px"> GRN NO </th>
                                            <th class="text-center" width="80px"> GRN DATE </th>
                                            <th class="text-center" width="60px"> UOM </th>
                                            <th class="text-center" width="45px"> QTY </th>
                                            <th class="text-center" width="80px"> PR STATUS </th>
                                            <th class="text-center" width="20px">&nbsp;</th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_pr_no">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy" readonly name="filter_date_prepare_from" placeholder="From">
                                                <input type="text" class="form-control form-filter input-sm date date-picker" data-date-format="dd-mm-yyyy" readonly name="filter_date_prepare_to" placeholder="To">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_item_name">
                                            </td>
                                            <td>
                                                <select name="filter_pr_uom_id" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                    foreach($qry_uom->result_array() as $uom){
                                                        echo '<option value="' . $uom['uom_code'] . '">' . $uom['uom_code'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center" name="filter_pr_qty_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center" name="filter_pr_qty_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_po_no">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm date-picker margin-bottom-5" data-date-format="dd-mm-yyyy" readonly name="filter_po_date_from" placeholder="From">
                                                <input type="text" class="form-control form-filter input-sm date date-picker" readonly name="filter_po_date_to" placeholder="To" data-date-format="dd-mm-yyyy">
                                            </td>
                                            <td>
                                                <select name="filter_po_uom_id" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                    foreach($qry_uom->result_array() as $uom){
                                                        echo '<option value="' . $uom['uom_code'] . '">' . $uom['uom_code'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center" name="filter_po_qty_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center" name="filter_po_qty_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_grn_no">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm date date-picker margin-bottom-5" readonly name="filter_grn_date_from" placeholder="From" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control form-filter input-sm date date-picker" readonly name="filter_grn_date_to" placeholder="To" data-date-format="dd-mm-yyyy">
                                            </td>
                                            <td>
                                                <select name="filter_grn_uom_id" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                    foreach($qry_uom->result_array() as $uom){
                                                        echo '<option value="' . $uom['uom_code'] . '">' . $uom['uom_code'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>

                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center" name="filter_grn_qty_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center" name="filter_grn_qty_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <select name="filter_pr_status" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <option value="<?php echo STATUS_NEW;?>">New</option>
                                                    <option value="<?php echo STATUS_APPROVE;?>">Approved</option>
                                                    <option value="<?php echo STATUS_DISAPPROVE;?>">Disapproved</option>
                                                    <option value="<?php echo STATUS_CLOSED;?>">Completed</option>																	    <option value="<?php echo STATUS_CANCEL;?>">Canceled</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <button class="btn btn-sm yellow filter-submit margin-bottom tooltips margin-bottom-5" data-original-title="Filter" data-placement="top" data-container="body" style="margin-right:0px;"><i class="fa fa-search"></i></button>
                                                    <button class="btn btn-sm red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body" style="margin-right:0px;"><i class="fa fa-times"></i></button>
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
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        function init_page(){
            $(".mask_currency").inputmask("decimal", {
                radixPoint: ".",
                groupSeparator: ",",
                digits: 2,
                autoGroup: true,
                autoUnmask: true
            });
        }

        init_page();

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
				src: $("#datatable_pr_progress"),
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
					
					"bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
					"aoColumns": [
                        { "bSortable": false, "sClass": "text-center td-height" },
                        { "bSortable": false, "sClass": "text-center td-height" },
                        { "bSortable": false, "sClass": "td-height" },
                        { "bSortable": false, "sClass": "text-center td-height" },
                        { "bSortable": false, "sClass": "text-right td-height"  },
                        { "bSortable": false, "sClass": "text-center td-height" },
						{ "bSortable": false, "sClass": "text-center td-height" },
                        { "bSortable": false, "sClass": "text-center td-height" },
                        { "bSortable": false, "sClass": "text-right td-height"  },
                        { "bSortable": false, "sClass": "text-center td-height" },
                        { "bSortable": false, "sClass": "text-center td-height" },
                        { "bSortable": false, "sClass": "text-center td-height" },
                        { "bSortable": false, "sClass": "text-right td-height"  },
                        { "bSortable": false, "sClass": "text-center td-height" },
                        { "bSortable": false, "sClass": "text-center td-height" }
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": 50, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('purchasing/pr_report/get_pr_progress/'. get_menu_id());?>" // ajax source
					},
					"fnDrawCallback": function( oSettings ) {
						$('.tooltips').tooltip();
                        init_page();
					}
				}
			});

            var tableWrapper = $("#datatable_pr_progress_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });
			
		}

		handleRecords();

	});
</script>