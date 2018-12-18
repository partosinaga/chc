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
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-users"></i>Report List Item
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <table class="table table-striped table-bordered table-hover dataTable table-small table-po-detail" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center" width="120px"> ITEM CODE </th>
                                            <th class="text-center"> DESCRIPTION </th>
                                            <th class="text-center" width="100px"> UOM </th>
                                            <th class="text-center" width="60px"> FACTOR </th>
                                            <th class="text-center" width="100px"> UOM DIST. </th>
                                            <th class="text-center" width="100px"> QTY </th>
                                            <th class="text-center" width="100px"> MIN STOCK </th>
                                            <th class="text-center" width="100px"> MAX STOCK </th>
                                            <th class="text-center" width="20px">&nbsp;</th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_item_code">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_item_desc">
                                            </td>
                                            <td>
                                                <select name="filter_uom_id" class="form-control form-filter input-sm select2me">
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
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_factor_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_factor_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <select name="filter_dist_uom_id" class="form-control form-filter input-sm select2me">
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
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_qty_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_qty_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_min_stock_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_min_stock_to" placeholder="Max">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group margin-bottom-5" >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_max_stock_from" placeholder="Min">
                                                </div>
                                                <div class="input-group " >
                                                    <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_max_stock_to" placeholder="Max">
                                                </div>
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
                        { "bSortable": true, "sClass": "text-center td-height" },                      // Item Code
                        { "sClass": "td-height" },                                  // Description
                        { "bSortable": false, "sClass": "text-center td-height"  }, // UOM
                        { "bSortable": false, "sClass": "text-right td-height" },   // Factor
						{ "bSortable": false, "sClass": "text-center td-height" },  // UOM Dist.
                        { "bSortable": false, "sClass": "text-right td-height" },   // Qty
                        { "bSortable": false, "sClass": "text-right td-height"  },  // Min Stock
                        { "bSortable": false, "sClass": "text-right td-height" },   // Max Stock
                        { "bSortable": false, "sClass": "text-center td-height" }   // Action
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": 50, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('inventory/report/ajax_item_list/'. get_menu_id());?>"
					},
					"fnDrawCallback": function( oSettings ) {
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