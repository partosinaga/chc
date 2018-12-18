<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">
		<!-- BEGIN PAGE HEADER-->
		<div class="page-bar">
			<ul class="page-breadcrumb">
				<?php
					$breadcrumbs = get_menu_name($this->uri->segment(1), $this->uri->segment(2), $this->uri->segment(3), $this->uri->segment(4));
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
							<i class="fa fa-users"></i>EXPECTED DEPARTURE
						</div>
                        <div class="actions pull-right">
                            <a href="javascript:;" class="btn green-meadow" id="print-button" style="margin-right: 15px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Print</a>
                        </div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <div class="table-actions-wrapper">
                                    <div class="input-group date-picker input-daterange" id="datepicker-range" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control form-filter-wrapper input-sm" name="filter_date_from" id="filter_date_from" value="<?php echo date('d-m-Y');?>">
                                                            <span class="input-group-addon input-sm">
                                                            to</span>
                                        <input type="text" class="form-control form-filter-wrapper input-sm" name="filter_date_to" id="filter_date_to" value="<?php echo date('d-m-Y');?>">
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered dataTable table-small table-hover table-po-detail" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center">FOLIO NO </th>
                                            <th class="text-left" >GUEST </th>
                                            <th class="text-left" >COMPANY </th>
                                            <th class="text-type" > TYPE </th>
                                            <th class="text-left" > ROOM </th>
                                            <th class="text-center"> DEPARTURE </th>
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
        }

        //init_page();

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
                        { "sClass": "text-center td-height", "sWidth" : "8%" },
                        { "sClass": "text-left td-height" },
                        { "sClass": "text-left td-height" },
                        { "sClass": "text-center td-height", "sWidth" : "8%", "bSortable" : false  },
                        { "sClass": "text-center td-height", "sWidth" : "12%", "bSortable" : false  },
                        { "sClass": "text-center td-height", "sWidth" : "8%" , "bSortable" : false }
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": -1, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('frontdesk/report/xreport_expected_departure/'. get_menu_id());?>"
					},
					"fnDrawCallback": function( oSettings ) {
                        var rowCount = oSettings._iRecordsTotal;
                        if(rowCount > 0){
                            $('#print-button').removeClass('hide');
                        }else{
                            $('#print-button').addClass('hide');
                        }

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
        grid.submitFilter();

        $('.input-daterange').live('change', function(e){
            //grid.clearAjaxParams();
            grid.submitFilter();
        });

        $('#print-button').on('click', function(){
            var date_from = $('input[name="filter_date_from"]').val();
            var date_until = $('input[name="filter_date_to"]').val();

            window.open('<?php echo base_url('frontdesk/report/pdf_exp_departure')?>/' + date_from + '/' + date_until + '.tpd','_blank');
        });
	});

</script>