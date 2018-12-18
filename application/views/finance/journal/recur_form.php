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

                $form_mode = '';
                if($entry_id > 0){
                    if($row->status != STATUS_NEW){
                        $form_mode = 'disabled';
                    }
                }
				?>
			</ul>
		</div>
		<!-- END PAGE HEADER-->
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
                <div class="portlet box <?php echo BOX;?>">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-user"></i><?php echo ($entry_id > 0 ? 'Edit' : 'New');?> Recurring Entry
						</div>
						<div class="actions">
                            <a href="<?php echo (isset($row) ? ($row->status != STATUS_NEW ? base_url('finance/recur/recur_history/1.tpd') : base_url('finance/recur/recur_manage/1.tpd') ) : base_url('finance/recur/recur_manage/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="<?php echo base_url('finance/recur/submit_recur.tpd');?>" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="entry_id" name="entry_id" value="<?php echo $entry_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="submit" class="btn green" name="save">Save</button>
										<button type="submit" class="btn blue-madison" name="save_close" >Save & Close</button>

                                        <?php
                                        if($entry_id > 0){
                                            if($row->status == STATUS_NEW && $row->entry_amount > 0){
                                        ?>
                                                &nbsp;
                                                <button type="button" class="btn btn-primary green-seagreen" id="posting-button" onclick="posting_record(<?php echo $entry_id; ?>);"><i class="fa fa-save"></i>&nbsp;Start Task</button>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
								</div>

							</div>
                            <?php
                            }
                            ?>
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
								<div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Journal No</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="journal_no" value="<?php echo ($entry_id > 0 ? $row->entry_code : '');?>" disabled />
                                            </div>
                                        </div>
                                    </div>
								</div>
                                <div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label col-md-4">Start Date <span class="required" aria-required="true"> * </span></label>
											<div class="col-md-6" >
												<div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
													<input type="text" class="form-control" name="entry_startdate" value="<?php echo ($entry_id > 0 ? ymd_to_dmy($row->entry_startdate) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
												</div>
											</div>
										</div>

									</div>
									<div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">End Date <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="entry_enddate" value="<?php echo ($entry_id > 0 ? ymd_to_dmy($row->entry_enddate) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Interval</label>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control text-center" name="entry_interval" value="<?php echo ($entry_id > 0 ? $row->entry_interval : '1');?>" />
                                            </div>
                                            <div class="col-md-5">
                                                <label class="control-label">(in months)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Run Day</label>
                                            <div class="col-md-2">
                                                <input type="text" name ="entry_runday" class="form-control" value="<?php echo ($entry_id > 0 ? $row->entry_runday : '20');?>" <?php echo $form_mode; ?> />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remark <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-10">
                                                <input type="text" name ="entry_remark" class="form-control" value="<?php echo ($entry_id > 0 ? $row->entry_remark : '');?>" <?php echo $form_mode; ?> />
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="row">
									<div class="col-md-12">
										<table class="table table-striped table-hover table-bordered" id="datatable_detail">
											<thead>
												<tr>
													<th class="text-center" style="width: 10%;"> A/C Code </th>
													<th class="text-left" > Description </th>
													<th class="text-center" style="width: 15%;"> Debit </th>
													<th class="text-center" style="width: 15%;"> Credit </th>
													<th class="text-center" style="width: 10%;"> Dept </th>
													<th class="text-center" style="width: 9%;"> </th>
												</tr>
											</thead>
											<tbody>
                                            <?php
                                            $sumDebit = 0;
                                            $sumCredit = 0;

                                            $rowIndex = 0;
                                            if($entry_id > 0){
                                                if(count($qry_det) > 0){

                                                    foreach($qry_det as $row_det){
                                                        echo '<tr>
                                                            <td class="text-center" style="vertical-align:middle;"><input type="hidden" name="detail_id[' . $rowIndex . ']" value="'. $row_det['detail_id'] . '">'. $row_det['coa_code'] . '</td>
                                                            <td style="vertical-align:middle;">' . $row_det['coa_desc'] . '</td>
                                                            <td ><input type="text" name="journal_debit[' . $rowIndex . ']" value="' . number_format($row_det['journal_debit'],0,'','') . '" class="form-control text-right j_debit mask_currency" '. $form_mode . '></td>
                                                            <td ><input type="text" name="journal_credit[' . $rowIndex . ']" value="' . number_format($row_det['journal_credit'],0,'','') . '" class="form-control text-right j_credit mask_currency" '. $form_mode . '></td>
                                                            <td class="text-center" style="vertical-align:middle;">' .
                                                            '<select name="dept_id[' . $rowIndex . ']" class="form-control form-filter input-sm select2me " '. $form_mode . '>';

                                                        if($row_det['dept_id'] == 0){
                                                            echo '<option value="0" selected="selected" >None</option>';
                                                        }else{
                                                            echo '<option value="0" >None</option>';
                                                        }

                                                        if(count($dept_list) > 0){
                                                            foreach($dept_list as $dept){
                                                                echo  '<option value="' . $dept['department_id'] . '" ' . ($row_det['dept_id'] > 0 ? ($row_det['dept_id'] == $dept['department_id'] ? 'selected="selected"' : '') : '') . '>' . $dept['department_name'] . '</option>';
                                                            }
                                                        }

                                                        echo  '</select>
                                                            </td>';

                                                        if($row->status != STATUS_NEW){
                                                            echo   '<td style="vertical-align:middle;"></td>';
                                                        }else{
                                                            echo   ' <td style="vertical-align:middle;"><a class="btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" onclick="delete_record(' . $row_det['detail_id'] . ',' . $rowIndex . ');" ><i class="fa fa-times"></i></a></td>';
                                                        }

                                                        echo   ' </tr>';
                                                        $rowIndex++;

                                                        $sumDebit += $row_det['journal_debit'];
                                                        $sumCredit += $row_det['journal_credit'];
                                                    }
                                                }
                                            }
                                            ?>

											</tbody>
                                            <tfoot>
                                            <tr>
                                                <td >
                                                    <?php if($form_mode == '') { ?>
                                                    <a href="javascript:;" class="btn green-seagreen add_detail" ><i class="fa fa-plus"></i> Add COA</a>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right" disabled><span class="form-control-static">Total</span></td>
                                                <td ><input type="text" name ="total_debit" id ="total_debit" class="form-control text-right mask_currency" value="<?php echo $sumDebit;?>" readonly/></td>
                                                <td ><input type="text" name ="total_credit" id ="total_credit" class="form-control text-right mask_currency" value="<?php echo $sumCredit;?>" readonly/></td>
                                                <td colspan="2" >&nbsp;</td>
                                            </tr>
                                            </tfoot>
										</table>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php
                                        if($entry_id > 0){
                                            $created = '';
                                            $modified = '';

                                            if ($row->created_by > 0) {
                                                $created .= "<div class='col-md-8'><h6>Created by " . get_user_fullname( $row->created_by) . " (" . date_format(new DateTime($row->created_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                                            }


                                            echo '<div class="note note-info" style="margin:10px;">
                                                    ' . $created . '
                                                    ' . $modified . '
                                                    <div style="clear:both;"></div>
                                                </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
							</div>
						</form>
						<!-- END FORM-->
					</div>
				</div>
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;
    var rowIndex = <?php echo (isset($rowIndex) ? $rowIndex : 0) ; ?>;

    if(isedit > 0){
        $('#form-entry').block({
            message: null ,
            overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
        });
    }

    var handleMask = function() {
        $(".mask_currency").inputmask("numeric",{
            radixPoint:".",
            autoGroup: true,
            groupSeparator: ",",
            digits: 0,
            groupSize: 3,
            autoUnmask: true
        });
    }

    $(window).load(function(){
        handleMask();
    });

    $(document).ready(function(){
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal

        };

        //handleMask();

        var handleValidation = function() {
            // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

            toastr.options = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-bottom-right",
                "onclick": null,
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": "3000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }

            var form1 = $('#form-entry');

            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    entry_startdate: {
                        required: true
                    },
                    entry_enddate: {
                        required: true
                    },
                    entry_interval: {
                        required: true,
                        digits:true
                    },
                    entry_runday: {
                        required: true,
                        digits:true,
                        max: 28
                    },
                    entry_remark: {
                        required: true
                    },
                    total_credit:{
                        equalTo:"#total_debit"
                    }
                },
                messages: {
                    entry_startdate: "Start date must not empty",
                    entry_enddate: "End date must not empty",
                    entry_interval: "Interval must not empty",
                    entry_runday: "Run on day must be filled and not exceed 28",
                    entry_remark: "Journal remark must not empty",
                    total_credit: "Total Debit must equal with Total Credit"
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.entry_startdate != null){
                        toastr["error"](validator.invalid.entry_startdate, "Warning");
                    }
                    if(validator.invalid.entry_enddate != null){
                        toastr["error"](validator.invalid.entry_enddate, "Warning");
                    }
                    if(validator.invalid.entry_interval != null){
                        toastr["error"](validator.invalid.entry_interval, "Warning");
                    }
                    if(validator.invalid.entry_runday != null){
                        toastr["error"](validator.invalid.entry_runday, "Warning");
                    }
                    if(validator.invalid.entry_remark != null){
                        toastr["error"](validator.invalid.entry_remark, "Warning");
                    }
                    if(validator.invalid.total_credit != null){
                        toastr["error"](validator.invalid.total_credit, "Warning");
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});

                    //console.log('text err ' + error.text());
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight

                },

                success: function (label, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    icon.removeClass("fa-warning").addClass("fa-check");
                }
            });
        }

        //initiate validation
        handleValidation();

        var grid_trx = new Datatable();
        var $modal = $('#ajax-modal');

        //COA
        var handleTableCOA = function () {
            // Start Datatable Item
            grid_trx.init({
                src: $("#datatable_coa"),
                onSuccess: function (grid_trx) {
                    // execute some code after table records loaded
                },
                onError: function (grid_trx) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_trx) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                    // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                    // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                    // So when dropdowns used the scrollable div should be removed.
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '5%' },
                        { "sClass": "text-center", "sWidth" : '7%' },
                        null,
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center", "sWidth" : '6%' },
                        { "bSortable": false }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [-1],
                        ["All"] // change per page values here
                    ],
                    "pageLength": -1, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('finance/setup/json_coa');?>"
                    }
                }
            });

            var tableWrapper = $("#datatable_coa_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        handleCalculation();

        $('.add_detail').live('click', function (e) {
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            var uninitialized = $('#datatable_coa').filter(function() {
                return !$.fn.DataTable.fnIsDataTable(this);
            });

            setTimeout(function(){
                $modal.load('<?php echo base_url('finance/setup/book_coa');?>.tpd', '', function(){
                    $modal.modal();
                    handleTableCOA();

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if($modal.hasClass('modal-overflow') === false){
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});
                });
            }, 150);
        });

        $('.table-group-action-submit').live('click', function (e) {
            e.preventDefault();
            grid_trx.src = $("#datatable_coa");
            if (grid_trx.getSelectedRowsCountRadio() > 0) {
                var result = grid_trx.getSelectedRowsRadio();
                //console.log(result);
                var coaID = result[0];
                var entryHeaderID = $('#entry_id').val() || 0;

                //console.log("add row ... " + rowIndex)

                //Add to temp
                var newRowContent = "<tr>" +
                    "<td class=\"text-center\" style=\"vertical-align:middle;\"><input type=\"hidden\" name=\"detail_id[" + rowIndex + "]\" value=\"0\"><input type=\"hidden\" name=\"coa_id[" + rowIndex + "]\" value=\"" + coaID + "\">" + result[1] + "</td>" +
                    "<td style=\"vertical-align:middle;\"><span class=\"control-label\">" + result[2] + "</span></td>" +
                    "<td ><input type=\"text\" name=\"journal_debit[" + rowIndex + "]\" value=\"0\" class=\"form-control text-right mask_currency j_debit\" coa-id=\"" + coaID + "\"></td>" +
                    "<td ><input type=\"text\" name=\"journal_credit[" + rowIndex + "]\" value=\"0\" class=\"form-control text-right mask_currency j_credit\" coa-id=\"" + coaID + "\"></td>" +
                    "<td class=\"text-center\" style=\"vertical-align:middle;\"><select name=\"dept_id[" + rowIndex + "]\" class=\"form-control form-filter input-sm select2me \">" +
                    "<option value=\"0\" >None</option>" +
                    <?php
                     if(count($dept_list) > 0){
                        foreach($dept_list as $dept){
                    ?>
                    "<option value=\"<?php echo $dept['department_id']; ?>\" ><?php echo $dept['department_name']; ?></option>" +
                    <?php
                        }
                     }
                     ?>
                    "</select></td>" +
                    "<td style=\"vertical-align:middle;\"><a class=\"btn btn-danger btn-xs tooltips\" data-original-title=\"Remove\" href=\"javascript:;\" onclick=\"delete_record(" + entryHeaderID + "," + rowIndex + ");\" ><i class=\"fa fa-times\"></i></a></td>" +
                    "</tr>";

                //$('#datatable_detail tbody').prepend(newRowContent);
                $('#datatable_detail tbody').append(newRowContent);

                $('select.select2me').select2();

                rowIndex++;

                handleCalculation();
                handleMask();
                //$('.get_coa[spec-key=\'' + speckey + '\']').html(result[1] + ' - ' + result[2]);

                $('#ajax-modal').modal('hide');
            } else if (grid_trx.getSelectedRowsCountRadio() === 0) {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record selected',
                    container: grid_trx.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });
	});

    function deleteDetail(detailId, rowIndex){
        if(detailId > 0){
            Metronic.blockUI({
                target: '.modal-content-edit',
                boxed: true,
                message: 'Processing...'
            });

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('finance/recur/ajax_delete_detail');?>",
                dataType: "json",
                data: { detail_id: detailId}
            })
                .done(function( msg ) {
                    if(msg.type != '0'){
                        Metronic.unblockUI('.modal-content-edit');
                        //$('#ajax-modal').modal('hide');

                        delete_frontend(rowIndex);
                    }
                    else {
                        Metronic.unblockUI('.modal-content-edit');

                        console.log('ajax delete detail failed.' + detailId);
                    }
                });
        }
    }

    function posting_record(headerId){
        bootbox.confirm({
            message: "Start this Recurring Task ?",
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
                if(result === false){
                    //console.log('Empty reason');
                }else{
                    //console.log(result);
                    Metronic.blockUI({
                        boxed: true
                    });

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('finance/recur/ajax_posting_journal_by_id');?>",
                        dataType: "json",
                        data: { entry_id: headerId}
                    })
                        .done(function( msg ) {
                            Metronic.unblockUI();

                            if(msg.type == '1'){
                                toastr["success"](msg.message, "Success");

                                window.location.assign(msg.redirect_link);
                            }
                            else {
                                toastr["warning"](msg.message, "Warning");
                            }
                        });
                }
            }
        });
    }

</script>