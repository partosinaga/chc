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
							<i class="fa fa-users"></i>SALES SUMMARY
						</div>
                        <div class="actions pull-right">
                            <a href="javascript:;" class="btn green-meadow" id="print-button" style="margin-right: 15px;"><i class="fa fa-print"></i>&nbsp;&nbsp;Print</a>
                        </div>
					</div>
					<div class="portlet-body">
						<div class="table-container table-responsive" >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <div class="table-actions-wrapper">
                                    <div class="col-md-1 "><label class="control-label" style="padding-top: 8px;"><strong>YEAR</strong></label>
                                    </div>
                                    <div class="col-md-1 col-xs-1 input-small" style="padding-left: 28px;">
                                        <div id="spinner1">
                                            <div class="input-group input-small" >
                                                <input type="text" class="spinner-input text-center form-control" maxlength="4" name="period_year" id="period_year" >
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
                                <table class="table table-striped table-bordered dataTable table-small table-hover table-po-detail" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-left">AGENT</th>
                                            <th class="text-left" width="200px">PIC Name</th>
                                            <th class="text-center" width="70px" colspan="2">JAN</th>
                                            <th class="text-center" width="70px" colspan="2">FEB</th>
                                            <th class="text-center" width="70px" colspan="2">MAR</th>
                                            <th class="text-center" width="70px" colspan="2">APR</th>
                                            <th class="text-center" width="70px" colspan="2">MAY</th>
                                            <th class="text-center" width="70px" colspan="2">JUN</th>
                                            <th class="text-center" width="70px" colspan="2">JUL</th>
                                            <th class="text-center" width="70px" colspan="2">AUG</th>
                                            <th class="text-center" width="70px" colspan="2">SEP</th>
                                            <th class="text-center" width="70px" colspan="2">OCT</th>
                                            <th class="text-center" width="70px" colspan="2">NOV</th>
                                            <th class="text-center" width="70px" colspan="2">DEC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    <tr class="heading">
                                        <th class="text-right" colspan="2">TOTAL</th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                        <th ></th>
                                    </tr>
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

		var handleRecords = function (year) {
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
                        { "sClass": "text-left td-height" },
                        { "sClass": "text-left td-height" },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false  },
                        { "sClass": "text-right td-height",  "bSortable" : false }

					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, 150, -1],
						[10, 20, 50, 100, 150, "All"] // change per page values here
					],
					"pageLength": -1, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('frontdesk/report/xreport_sales_sum_by_year');?>" + '/' + year
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

					},
                    "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                        var atotal = new Array(0,0,0,0,0,0,0,0,0,0,0,0,
                                                0,0,0,0,0,0,0,0,0,0,0,0);
                        for ( var i=0 ; i<aaData.length ; i++ )
                        {
                            for(var idx=0; idx<24; idx++){
                                var total_ = aaData[i][idx+2].replace('<span class="mask_currency">', '');
                                var total = total_.replace('</span>', '');
                                atotal[idx] += parseFloat((parseFloat(total)).toFixed(0)) || 0;
                            }
                        }

                        for(var idx=0; idx<24; idx++){
                            nRow.getElementsByTagName('th')[1+idx].innerHTML = '<span class="mask_currency">' + atotal[idx] + '</span>';
                        }
                    }
				}
			});

            var tableWrapper = $("#datatable_ajax_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false
            });

            tableWrapper.find("#spinner1").spinner({
                value:year,
                min: 2015,
                max:(year + 1)
            });
			
		}

		handleRecords(<?php echo isset($period_year) ? ($period_year > 0 ? $period_year : date("Y")) : date("Y"); ?>);
        grid.submitFilter();

        var tableWrapper = $("#datatable_ajax_wrapper");
        tableWrapper.find('#spinner1').on('change',function() {
            var year = $('#spinner1').spinner('value');

            if ($.fn.dataTable.isDataTable('#datatable_ajax' )) {
                $('#datatable_ajax').dataTable().api().ajax.url("<?php echo base_url('frontdesk/report/xreport_sales_sum_by_year');?>" + '/' + year).load();
            }else{
                handleRecords(year);
            }
        });

        $('#print-button').on('click', function(){
            var year = $("#datatable_ajax_wrapper").find('#spinner1').spinner('value');
            //console.log('YEAR ' + year);
            window.open('<?php echo base_url('frontdesk/report/pdf_sales_sum_by_year')?>/' + year + '.tpd','_blank');
        });
	});

</script>