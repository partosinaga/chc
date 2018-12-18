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
							<i class="fa fa-users"></i>Report AP Threeway
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <table class="table table-striped table-bordered table-hover dataTable table-small table-po-detail" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center" width="100px"> SUPPLIER </th>
                                            <th class="text-center" width="70px"> GRN NO </th>
                                            <th class="text-center" width="70px"> GRN DATE </th>
                                            <th class="text-center"> ITEM DESC </th>
                                            <th class="text-center" width="50px"> QTY </th>
                                            <th class="text-center" width="70px"> AMOUNT </th>
                                            <th class="text-center" width="70px"> INV. NO </th>
                                            <th class="text-center" width="70px"> INV. DATE </th>
                                            <th class="text-center" width="60px"> CURR </th>
                                            <th class="text-center" width="70px"> AMOUNT </th>
                                            <th class="text-center" width="70px"> PAY. NO </th>
                                            <th class="text-center" width="70px"> PAY. DATE </th>
                                            <th class="text-center" width="60px"> CURR </th>
                                            <th class="text-center" width="70px"> AMOUNT </th>
                                            <th class="text-center" width="20px">&nbsp;</th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_supplier_name">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_grn_no">
                                            </td>
                                            <td>
                                                <div class="input-group date-picker input-daterange" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control form-filter input-sm text-center" readonly name="filter_grn_date_from" placeholder="From" style="margin-bottom: 5px;" value="<?php echo date("d-m-Y",strtotime("-1 month"));?>">
                                                    <input type="text" class="form-control form-filter input-sm text-center" readonly name="filter_grn_date_to" placeholder="To" value="<?php echo date('d-m-Y');?>">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_item_desc">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_grn_qty_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_grn_qty_to" placeholder="Max">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_grn_amount_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_grn_amount_to" placeholder="Max">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_inv_no">
                                            </td>
                                            <td>
                                                <div class="input-group date-picker input-daterange" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control form-filter input-sm text-center" readonly name="filter_inv_date_from" placeholder="From" style="margin-bottom: 5px;">
                                                    <input type="text" class="form-control form-filter input-sm text-center" readonly name="filter_inv_date_to" placeholder="To">
                                                </div>
                                            </td>
                                            <td>
                                                <select name="filter_inv_curr" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                    foreach($qry_curr->result() as $row) {
                                                        echo '<option value="' . $row->currencytype_code . '">' . $row->currencytype_code . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_inv_amount_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_inv_amount_to" placeholder="Max">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm" name="filter_pay_no">
                                            </td>
                                            <td>
                                                <div class="input-group date-picker input-daterange" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control form-filter input-sm text-center" readonly name="filter_pay_date_from" placeholder="From" style="margin-bottom: 5px;">
                                                    <input type="text" class="form-control form-filter input-sm text-center" readonly name="filter_pay_date_to" placeholder="To">
                                                </div>
                                            </td>
                                            <td>
                                                <select name="filter_pay_curr" class="form-control form-filter input-sm select2me">
                                                    <option value="">All</option>
                                                    <?php
                                                    foreach($qry_curr->result() as $row) {
                                                        echo '<option value="' . $row->currencytype_code . '">' . $row->currencytype_code . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency margin-bottom-5" name="filter_pay_amount_from" placeholder="Min">
                                                <input type="text" class="form-control form-filter input-sm text-center mask_currency" name="filter_pay_amount_to" placeholder="Max">
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
                orientation: "right",
                autoclose: true,
                todayHighlight: true,
                todayBtn: true
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
                        { "bSortable": true, "sClass": "text-left td-height" },     // supplier Name
                        { "sClass": "text-center td-height"  },                     // grn no
                        { "sClass": "text-center td-height"  },                     // grn date
                        { "bSortable": false, "sClass": "td-height"  },             // item desc
                        { "bSortable": false, "sClass": "text-right td-height" },   // grn qty
                        { "bSortable": false, "sClass": "text-right td-height"  },  // grn amount
                        { "bSortable": false, "sClass": "text-center td-height"  }, // inv no
                        { "bSortable": false, "sClass": "text-center td-height"  }, // inv date
                        { "bSortable": false, "sClass": "text-center td-height"  }, // inv curr
                        { "bSortable": false, "sClass": "text-right td-height"  },  // inv amount
                        { "bSortable": false, "sClass": "text-center td-height"  }, // pay no
                        { "bSortable": false, "sClass": "text-center td-height"  }, // pay date
                        { "bSortable": false, "sClass": "text-center td-height"  }, // pay curr
                        { "bSortable": false, "sClass": "text-right td-height"  },  // pay amount
                        { "bSortable": false, "sClass": "text-center td-height" }   // Action
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": 150, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('ap/report/ajax_ap_threeway/'. get_menu_id());?>"
					},
					"fnDrawCallback": function( oSettings ) {
                        init_page();

                        $('.date-picker').datepicker({
                            rtl: Metronic.isRTL(),
                            orientation: "right",
                            autoclose: true
                        });
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