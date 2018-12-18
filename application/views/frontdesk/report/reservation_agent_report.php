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
							<i class="fa fa-users"></i>Sales Report
						</div>
                        <div class="actions pull-right">
                            <a href="javascript:;" class="btn green-meadow" id="print-button" style="margin-right: 15px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Print</a>
                        </div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <div class="table-actions-wrapper">
                                    <div class="col-md-5 "></div>
                                    <div class="col-md-7" style="padding-right: 0px;">
                                        <div class="input-group date date-picker " data-date-format="dd-mm-yyyy">
                                            <input type="text" class="form-control form-filter-wrapper input-sm text-center" name="filter_date_from" id="filter_date_from" value="<?php echo date('d-m-Y');?>" readonly>
                                        <span class="input-group-btn">
										    <button class="btn default input-sm" type="button" ><i class="fa fa-calendar" ></i></button>
										</span>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered dataTable table-small table-hover table-po-detail" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center"># </th> 
                                            <th class="text-left" >Date </th>
                                            <th class="text-left" >GUEST </th>
                                            <th class="text-left" >COMPANY </th>
                                            <th class="text-center" > TYPE </th>
                                            <th class="text-left" > ROOM </th>
                                            <th class="text-left" > UNIT TYPE </th>
                                            <th class="text-center">ARRIVAL</th>
                                            <th class="text-center">DEPARTURE</th>
                                            <th class="text-center">AMOUNT (monthly)</th>
                                            <th class="text-center">AMOUNT CONTRACT</th>
                                            <th class="text-center">AGENT</th>
                                            <!--th class="text-right">BALANCE</th -->
                                            <!--th class="text-left">REMARK</th -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <!--tfoot>
                                    <tr class="heading">
                                        <th colspan="7"></th>
                                        <th class="text-right">TOTAL</th>
                                        <th></th>
                                    </tr>
                                    </tfoot-->
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
                        { "sClass": "text-center td-height", "sWidth" : "3%" }, 
                        { "sClass": "text-left td-height" },
                        { "sClass": "text-left td-height" },
                        { "sClass": "text-left td-height" }, 
                        { "sClass": "text-center td-height", "sWidth" : "7%", "bSortable" : false  },
                        { "sClass": "text-center td-height", "sWidth" : "4%", "bSortable" : false  },
                        { "sClass": "text-center td-height", "sWidth" : "4%", "bSortable" : false  },
                        { "sClass": "text-center td-height", "sWidth" : "7%" , "bSortable" : false },
                        { "sClass": "text-center td-height", "sWidth" : "7%" , "bSortable" : false },
                        { "sClass": "text-right td-height", "sWidth" : "7%" , "bSortable" : false },
                        { "sClass": "text-right td-height", "sWidth" : "8%" , "bSortable" : false },
                        { "sClass": "text-left td-height", "sWidth" : "8%" , "bSortable" : false }
                    ],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": -1, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('frontdesk/report/xreport_reservation_agent/'. get_menu_id());?>"
					},
					"fnDrawCallback": function( oSettings ) {
                        var rowCount = oSettings._iRecordsTotal;
                        if(rowCount > 0){
                            $('#print-button').removeClass('hide');
                        }else{
                            $('#print-button').addClass('hide');
                        }

                        init_page();
                        //initDateChanged();

                        $('.date-picker').datepicker({
                            rtl: Metronic.isRTL(),
                            orientation: "right",
                            autoclose: true
                        });
					},
                    "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                        var t_total = 0;
                        /*
                        for ( var i=0 ; i<aaData.length ; i++ )
                        {
                            var total_ = aaData[i][8].replace('<span class="mask_currency">', '');
                            var total = total_.replace('</span>', '');
                            t_total += parseFloat((parseFloat(total)).toFixed(0)) || 0;
                        }
                        t_total = parseFloat(t_total.toFixed(0));

                        nRow.getElementsByTagName('th')[2].innerHTML = '<span class="mask_currency">' + t_total + '</span>';
                        */
                    }
				}
			});

            var tableWrapper = $("#datatable_ajax_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false
            });

		}

		handleRecords();

        var initDateChanged = function(){
            var tableWrapper = $("#datatable_ajax_wrapper");
            $(".date-picker").datepicker().on('changeDate', function(e){
                //grid.clearAjaxParams();
                //console.log('date changed ...');
                grid.submitFilter();
                $(this).datepicker('hide');
            });
        }



        initDateChanged();

        $('#print-button').on('click', function(){
            var date_from = $('input[name="filter_date_from"]').val();
            //var date_until = $('input[name="filter_date_to"]').val();

            window.open('<?php echo base_url('frontdesk/report/pdf_reservation_agent')?>/' + date_from + '.tpd','_blank');
        });
	});

</script>