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
							<i class="fa fa-users"></i>EDIT POSTED JOURNAL LOG
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <div class="table-actions-wrapper">

                                </div>
                                <table class="table table-striped table-bordered dataTable table-small table-hover table-po-detail " id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center" width="7%" >DOC NO</th>
                                            <th class="text-center" width="10%">DATE </th>
                                            <th class="text-left" width="13%">EDITED BY </th>
                                            <th class="text-left" >DESCRIPTION </th>
                                            <th class="text-left" width="25%">PREV </th>
                                            <th class="text-left" width="25%">CHANGE TO </th>
                                            <th class="text-center" width="8%">&nbsp;</th>
                                        </tr>
                                        <tr role="row" class="filter bg-grey-steel">
                                            <td>
                                                <input type="text" class="form-control form-filter input-sm text-center" name="filter_doc_no">
                                            </td>
                                            <td>
                                                <input type="text" class="text-center form-control form-filter input-sm date-picker margin-bottom-5" name="filter_edit_from" data-date-format="dd-mm-yyyy" readonly />
                                                <input type="text" class="text-center form-control form-filter input-sm date-picker" name="filter_edit_to" data-date-format="dd-mm-yyyy" readonly />
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
                                                    <button data-container="body" data-placement="top" data-original-title="Filter" class="btn btn-sm yellow filter-submit margin-bottom tooltips"><i class="fa fa-search"></i></button>
                                                    <button data-container="body" data-placement="top" data-original-title="Reset" class="btn btn-sm red filter-cancel tooltips"><i class="fa fa-times"></i></button>

                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>

                                    </tfoot>
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
                todayBtn: false
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
                        { "sClass": "text-center td-height"},
                        { "sClass": "text-center td-height"},
                        { "sClass": "text-left td-height" },
                        { "sClass": "text-left td-height"},
                        { "sClass": "text-left td-height" },
                        { "sClass": "text-left td-height" },
                        { "bSortable": false, "sClass": "text-center td-height" }
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": -1, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('admin/report/xeditposted');?>"
					},
					"fnDrawCallback": function( oSettings ) {
                        //init_page();
					},
                    "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {

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