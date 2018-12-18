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
							<i class="fa fa-users"></i>Report Purchase Status
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <table class="table table-striped table-bordered table-hover dataTable table-small table-po-detail" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center" width="75px"> PO NO </th>
                                            <th class="text-center" width="70px"> PO DATE </th>
                                            <th class="text-center" width="120px"> SUPPLIER </th>
                                            <th class="text-center"> ITEM </th>
                                            <th class="text-center" width="60px"> UOM </th>
                                            <th class="text-center" width="45px"> QTY </th>
                                            <th class="text-center" width="30px"> CURR </th>
                                            <th class="text-center" width="90px"> PRICE/UNIT </th>
                                            <th class="text-center" width="90px"> DISC </th>
                                            <th class="text-center" width="90px"> TAX </th>
                                            <th class="text-center" width="100px"> TOTAL </th>
                                            <th class="text-center" width="60px"> GRN QTY </th>
                                            <th class="text-center" width="80px"> PO STATUS </th>
                                            <th class="text-center" width="20px">&nbsp;</th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_po_no">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm date date-picker margin-bottom-5 text-center" data-date-format="dd-mm-yyyy" readonly name="filter_date_po_from" placeholder="From">
                                                <input type="text" class="form-control form-filter input-sm date date-picker text-center" data-date-format="dd-mm-yyyy" readonly name="filter_date_po_to" placeholder="To">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_supplier">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_item_desc">
                                            </td>
                                            <td>
                                                <select name="filter_po_uom_id" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                    foreach($qry_uom->result() as $uom){
                                                        echo '<option value="' . $uom->uom_id . '">' . $uom->uom_code . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_po_qty_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_po_qty_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <select name="filter_currencytype_id" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                    foreach($qry_curr->result() as $curr){
                                                        echo '<option value="' . $curr->currencytype_id . '">' . $curr->currencytype_code . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_po_price_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_po_price_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_po_disc_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_po_disc_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_po_tax_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_po_tax_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_po_tot_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_po_tot_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_grn_qty_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_grn_qty_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <select name="filter_po_status" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <option value="<?php echo STATUS_NEW;?>">New</option>
                                                    <option value="<?php echo STATUS_APPROVE;?>">Approved</option>
                                                    <option value="<?php echo STATUS_DISAPPROVE;?>">Disapproved</option>
                                                    <option value="<?php echo STATUS_CLOSED;?>">Completed</option>
                                                    <option value="<?php echo STATUS_CANCEL;?>">Canceled</option>
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
					
					"bStateSave": false,
					"aoColumns": [
                        { "sClass": "text-center td-height" },                      // PO No
                        { "sClass": "text-center td-height" },                      // PO Date
                        { "sClass": "td-height" },                                  // Supplier
                        { "sClass": "td-height" },                                  // Item
                        { "bSortable": false, "sClass": "text-center td-height"  }, // UOM
                        { "bSortable": false, "sClass": "text-right td-height" },   // QTY
						{ "bSortable": false, "sClass": "text-center td-height" },  // CURR
                        { "bSortable": false, "sClass": "text-right td-height" },   // Price
                        { "bSortable": false, "sClass": "text-right td-height"  },  // Disc
                        { "bSortable": false, "sClass": "text-right td-height" },   // Tax
                        { "bSortable": false, "sClass": "text-right td-height" },   // Total
                        { "bSortable": false, "sClass": "text-right td-height" },   // GRN Qty
                        { "bSortable": false, "sClass": "text-center td-height"  }, // Status
                        { "bSortable": false, "sClass": "text-center td-height" }   // Action
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": 50, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('purchasing/pr_report/ajax_purchase_status/'. get_menu_id());?>"
					},
					"fnDrawCallback": function( oSettings ) {
						$('.tooltips').tooltip();
                        init_page();
					}
				}
			});

            var tableWrapper = $("#datatable_ajax_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false
            });
			
		}

		handleRecords();

	});
</script>