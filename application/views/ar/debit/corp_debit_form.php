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
                $back_url = base_url('ar/corporate_bill/debit/1.tpd');
                if($debitnote_id > 0){
                    if($row->status != STATUS_NEW){
                        $form_mode = 'disabled';
                    }

                    if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                        $back_url = base_url('ar/corporate_bill/debit/2.tpd');
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
							<i class="fa fa-user"></i><?php echo ($debitnote_id > 0 ? '' : 'New');?> Debit Note
						</div>
						<div class="actions">
                            <?php
                            if($debitnote_id > 0){
                                if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED ){
                            ?>
                                    <a href="<?php echo base_url('ar/report/pdf_debitnote/'. ($debitnote_id > 0 ? $row->debitnote_id : '0') .'.tpd');?>" class="btn btn-circle default purple-studio" target="_blank"><i class="fa fa-print"></i> Debit Note</a>
                                    <a href="<?php echo base_url('ar/report/pdf_debitvoucher/'. ($debitnote_id > 0 ? $row->debitnote_id : '0') .'.tpd');?>" class="btn btn-circle default purple-studio" target="_blank"><i class="fa fa-print"></i> Voucher</a>

                            <?php
                                }
                            }
                            ?>
                            <a href="<?php echo (isset($row) ? $back_url : base_url('ar/corporate_bill/debit/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="javascript:;" id="form-entry" class="form-horizontal" method="post">
							<input type="hidden" id="debitnote_id" name="debitnote_id" value="<?php echo $debitnote_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn btn-sm green btn-circle" name="save" id="btn_save"><i class="fa fa-save"></i>&nbsp;Save</button>
										<!-- button type="button" class="btn btn-sm blue-madison" name="btn_save_close" >Save & Close</button -->
                                        <?php
                                        if($debitnote_id > 0){
                                            if($row->status == STATUS_NEW && $row->debit_amount > 0){
                                        ?>
                                                &nbsp;
                                                <button type="button" class="btn btn-sm blue btn-circle" id="posting-button" onclick="javascript:;"><i class="fa fa-check"></i>&nbsp;Posting</button>
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
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Date </label>
                                            <div class="col-md-4" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="debit_date" value="<?php echo ($debitnote_id > 0 ? dmy_from_db($row->debit_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label class="control-label col-md-3">Company</label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <input type="hidden" name="company_id" value="<?php echo ($debitnote_id > 0 ? $row->company_id : '');?>">
                                                    <input type="hidden" name="reservation_id" value="0">
                                                    <input type="hidden" name="receipt_id" value="<?php echo ($debitnote_id > 0 ? $row->receipt_id : '');?>">
                                                    <input type="text" class="form-control" name="company_name" value="<?php echo ($debitnote_id > 0 ? $row->company_name : '');?>" readonly />
                                                     <span class="input-group-btn">
                                                       <a id="btn_lookup_company" class="btn btn-success" href="javascript:;" >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
										</div>
									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">DN No</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="refund_no" value="<?php echo ($debitnote_id > 0 ? $row->debit_no : 'NEW');?>" disabled />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Receipt No</label>
                                            <div class="col-md-4">
                                                <input type="text" name="receipt_no" value="<?php echo ($debitnote_id > 0 ? $row->receipt_no : '') ;?>" class="form-control text-center" readonly>
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Remark</label>
                                            <div class="col-md-9">
                                                <textarea name="debit_remark" rows="2" class="form-control" style="resize: vertical;"><?php echo ($debitnote_id > 0 ? $row->debit_remark : '') ;?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Unallocated</label>
                                            <div class="col-md-4">
                                                <div class="input-inline ">
                                                    <div class="input-group">
                                                        <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                        <input type="text" id="debit_amount" name="debit_amount" value="<?php echo ($debitnote_id > 0 ? ($available_debit) : '0') ;?>" class="form-control text-right mask_currency input-small" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- TABLE DETAIL -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-hover" id="table_detail">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th class="text-center" style="width:13%;">
                                                    GL Code
                                                </th>
                                                <th class="text-left" style="width:30%;">
                                                    Account
                                                </th>
                                                <th class="text-left" >
                                                    Description
                                                </th>
                                                <th class="text-center" style="width:12%;">
                                                    Debit Amount
                                                </th>
                                                <th style="width:3%;">

                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $sumDebit = 0;
                                            if($debitnote_id > 0){
                                                if(isset($detail)){
                                                    foreach($detail->result_array() as $det){
                                                        echo '<tr>' .
                                                            '<td class="text-center" style="vertical-align:middle;"><input type="hidden" name="detail_id[]" value="' . $det['detail_id'] . '"><input type="hidden" name="unique_id[]" value="">' .
                                                            '<div class="input-group">' .
                                                                '<input type="hidden" name="coa_id[]" value="' . $det['coa_id'] . '" />' .
                                                                '<input type="text" name="coa_code[]" class="form-control input-sm text-center" value="' . $det['coa_code'] . '" readonly />' .
                                                                '<span class="input-group-btn">' .
                                                                '<button class="btn btn-sm green-haze find_coa" unique-id="" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' .
                                                                '</span>' .
                                                            '</div>' .
                                                            '</td>' .
                                                            '<td style="vertical-align:middle;"><input type="text" name="coa_desc[]" value="' . $det['coa_desc'] .'" class="form-control" readonly></td>' .
                                                            '<td style="vertical-align:middle;"><input type="text" name="detail_desc[]" value="' . $det['description'] . '" class="form-control"></td>' .
                                                            '<td ><input type="text" name="debit_value[]" value="' . $det['amount'] . '" class="form-control text-right mask_currency sub_debit_change"></td>' .
                                                            '<td style="vertical-align:middle;"><a class="btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" onclick="delete_record(' . $det['detail_id'] . ',\'\');"><i class="fa fa-times"></i></a></td>' .
                                                            '</tr>';
                                                        $sumDebit+=$det['amount'];
                                                    }
                                                }
                                            }
                                            ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td >
                                                    <?php if($form_mode == '') { ?>
                                                        <a href="javascript:;" class="btn btn-sm green-haze yellow-stripe add_detail" ><i class="fa fa-plus"></i> Add detail</a>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right" colspan="2"><span class="form-control-static">Total</span></td>
                                                <td ><input type="hidden" name="detail_total">
                                                    <input type="text" name ="total_debit" id ="total_debit" class="form-control text-right mask_currency" value="<?php echo number_format($sumDebit,0,'','');?>" readonly/></td>
                                                <td >&nbsp;</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <!-- END TABLE DETAIL -->
							</div>
						</form>
						<!-- END FORM-->
					</div>
				</div>
			</div>
		</div>
		<!-- END PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php
                if($debitnote_id > 0){
                    $created = '';
                    $modified = '';

                    if ($row->created_by > 0) {
                        $created .= "<div class='col-md-8'><h6>Created by " . get_user_fullname( $row->created_by) . " (" . date_format(new DateTime($row->created_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                    }
                    /*
                    if ($row->modified_by > 0) {
                        $modified .= "<div class='col-md-4'><h6>Last Modified by " . get_user_fullname( $row->modified_by) . " (" . date_format(new DateTime($row->modified_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                    }
                    */
                    echo '<div class="note note-info" style="margin:10px;">
                                                    ' . $created . '
                                                    ' . '' . '
                                                    <div style="clear:both;"></div>
                                                </div>';
                }
                ?>
            </div>
        </div>

	</div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<div id="ajax-posting" class="modal fade bs-modal-sm"  data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Posting date :</h4>
            </div>
            <div class="modal-body">
                <div class="input-group date " data-date-format="dd-mm-yyyy">
                    <input type="text" class="form-control" name="c_posting_date" value="<?php echo (date('d-m-Y'));?>" readonly>
					<span class="input-group-btn hide">
						<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
					</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue" id="submit-posting">Posting</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;

    $(document).ready(function(){
        <?php echo picker_input_date() ;?>

        if(isedit > 0){
            $('#form-entry').block({
                message: null ,
                overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
            });
        }

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

        var handleValidation = function() {
            // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation
            $.validator.addMethod("checkDetail", function (value, element)
                {
                    var valid = true;
                    try{
                        var counter = $('#table_detail > tbody > tr ').length;
                        if(counter > 0){
                            $('#table_detail > tbody > tr ').each(function() {
                                var debitAmount = parseFloat($(this).find('input[name*="debit_value"]').val()) || 0;

                                if(debitAmount <= 0){
                                    toastr["error"]("Debit amount in detail must not 0.", "Warning");
                                    valid = false;
                                    return;
                                }

                                if(valid){
                                    var tableId = '#table_detail';
                                    var rowCount = $(tableId + ' > tbody > tr ').length;
                                    if(rowCount > 0){
                                        var amount = getAvailableDebit();
                                        if(amount < 0){
                                            valid = false;
                                            return;
                                        }

                                        if(valid){
                                            $(tableId + ' > tbody > tr ').each(function() {
                                                var col1 = $(this).find('td:nth-child(1)');
                                                var coa_id = parseFloat(col1.find('input[name*="coa_id"]').val()) || 0;
                                                if(coa_id <= 0){
                                                    toastr["error"]("GL Code must be selected.", "Warning");
                                                    valid = false;
                                                    return;
                                                }
                                            });
                                        }
                                    }
                                }
                            })
                        }else{
                            toastr["error"]("Debit Note detail must not empty.", "Warning");
                            valid = false;
                        }
                    }catch(e){
                        console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Debit total must equal unallocated amount."
            )

            $.validator.addMethod("validateCheckID", function (value, element)
                {
                    var valid = true;
                    try{
                        var company_id = parseFloat($('input[name="company_id"]').val()) || 0;
                        var reservation_id = parseFloat($('input[name="reservation_id"]').val()) || 0;

                        if(company_id <= 0 && reservation_id <= 0){
                            toastr["error"]("Please select Company or Guest.", "Warning");
                            valid = false;
                        }

                    }catch(e){
                        console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Please select Company or Guest."
            )

            var form1 = $('#form-entry');
            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    debit_date: {
                        required: true
                    },
                    reservation_id:{
                        validateCheckID: true
                    },
                    debit_remark: {
                        required: true
                    },
                    total_debit:{
                        required: true,
                        min: 1
                    },
                    detail_total:{
                        checkDetail:true
                    }
                },
                messages: {
                    debit_date: "Date must be selected",
                    debit_remark: "Remark must not empty",
                    total_debit: "Debit Amount must not 0",
                    detail_total: ""
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.debit_date != null){
                        toastr["error"](validator.invalid.debit_date, "Warning");
                    }
                    if(validator.invalid.debit_remark != null){
                        toastr["error"](validator.invalid.debit_remark, "Warning");
                    }
                    if(validator.invalid.total_debit != null){
                        toastr["error"](validator.invalid.total_debit, "Warning");
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

        handleValidation();

        var handleMask = function() {
            $(".mask_currency").inputmask("numeric",{
                radixPoint:".",
                autoGroup: true,
                groupSeparator: ",",
                digits: 0,
                groupSize: 3,
                removeMaskOnSubmit:true,
                autoUnmask: true
            });
        }

        handleMask();

        //Reservation
        var grid_comp = new Datatable();
        //COA
        var handleTableReceipt = function (num_index, receipt_id, debitnote_id) {
            // Start Datatable Item
            grid_comp.init({
                src: $("#datatable_reservation"),
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sWidth" : '15%' ,"sClass": "text-center"},
                        null,
                        { "sWidth" : '30%' ,"sClass": "text-right", "bSortable": false},
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '11%' }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, -1],
                        [10, 20, 50, 100, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('ar/debit/get_modal_unallocated_corp');?>/" + num_index + "/" + receipt_id + "/" + debitnote_id // ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_reservation_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('#btn_lookup_company').on('click', function(){
            var $modal = $('#ajax-modal');
            var num_index = parseInt($(this).attr('data-index')) || 0;

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('ar/debit/ajax_unallocated_corporate');?>.tpd', '', function () {
                    $modal.modal();

                    var debitnote_id = $('input[name="debitnote_id"]').val();
                    var receipt_id = $('input[name="receipt_id"]').val();
                    handleTableReceipt(num_index, receipt_id, debitnote_id);

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if ($modal.hasClass('modal-overflow') === false) {
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});
                });
            }, 150);
        });

        $('.btn-select-company').live('click', function (e) {
            e.preventDefault();

            var receipt_id = parseInt($(this).attr('data-receipt-id')) || 0;
            var receipt_no = $(this).attr('data-receipt-no');
            var company_id = parseInt($(this).attr('data-company-id')) || 0;
            var reservation_id = parseFloat($(this).attr('data-reservation-id')) || 0;
            var company_name = $(this).attr('data-company-name');
            var amount = $(this).attr('data-amount') || 0;

            $('input[name="company_id"]').val(company_id);
            $('input[name="reservation_id"]').val(reservation_id);
            $('input[name="company_name"]').val(company_name);
            $('input[name="receipt_id"]').val(receipt_id);
            $('input[name="receipt_no"]').val(receipt_no);
            $('input[name="debit_amount"]').val(amount);

            $('#ajax-modal').modal('hide');
        });

        $('.add_detail').live('click', function(e){
            e.preventDefault();

            var date = new Date();
            var uniqueId = (date.toLocaleString(['ban', 'id'])).replace(/([.*+?^=!:${}()|\[\]\/\\])/g,'');
            uniqueId = uniqueId.replace(/ /g,'');

            //Add to temp
            var newRowContent = '<tr>' +
                '<td class="text-center" style="vertical-align:middle;"><input type="hidden" name="detail_id[]" value="0"><input type="hidden" name="unique_id[]" value="' + uniqueId + '">' +
                '<div class="input-group">' +
                '<input type="hidden" name="coa_id[]" value="0" />' +
                '<input type="text" name="coa_code[]" class="form-control input-sm text-center" value="" readonly />' +
                '<span class="input-group-btn">' +
                '<button class="btn btn-sm green-haze find_coa" unique-id="' + uniqueId + '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                '</span>' +
                '</div>' +
                '</td>' +
                '<td style="vertical-align:middle;"><input type="text" name="coa_desc[]" value="" class="form-control" readonly></td>' +
                '<td style="vertical-align:middle;"><input type="text" name="detail_desc[]" value="" class="form-control"></td>' +
                '<td ><input type="text" name="debit_value[]" value="0" class="form-control text-right mask_currency sub_debit_change"></td>' +
                '<td style="vertical-align:middle;"><a class="btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" onclick="delete_record(0,\'' + uniqueId + '\');"><i class="fa fa-times"></i></a></td>' +
                '</tr>';

            //$('#table_credit_detail tbody').append(newRowContent);
            var tbody = $(this).closest('table').find('tbody');
            tbody.append(newRowContent);

            $('select.select2me').select2();

            handleMask();
        });

        var grid_coa = new Datatable();
        var handleTableCOA = function (coaId, detailId, uniqueId)   {
            var coa_id_exist = '-';
            var n = 0;
            $('#table_detail tbody tr').each(function () {
                if ($(this).hasClass('hide') == false) {
                    var coa_id = parseInt($(this).find('input[name*="coa_id"]').val()) || 0;
                    if (coa_id > 0) {
                        if(coa_id_exist == '-'){
                            coa_id_exist = '';
                        }
                        if(n > 0) {
                            coa_id_exist += '_';
                        }

                        coa_id_exist += coa_id;
                        n++;
                    }
                }
            });

            grid_coa.init({
                src: $("#datatable_coa"),
                onSuccess: function (grid_coa) {
                    // execute some code after table records loaded
                },
                onError: function (grid_coa) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_coa) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "bSortable": true, "sWidth" : '15%' },
                        null,
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center", "sWidth" : '6%' },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('general/modalbook/get_coa_list_by_id');?>/" + coaId + '/' + detailId + '/' + uniqueId + '/' + coa_id_exist + '/'
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

        $('.find_coa').live('click', function(e){
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            var coa_id = parseInt($(this).closest('td').find('input[name*="coa_id"]').val()) || 0;
            var detail_id = parseInt($(this).closest('td').find('input[name*="detail_id"]').val()) || 0;
            var unique_id = parseInt($(this).closest('td').find('input[name*="unique_id"]').val()) || 0;
            //console.log('UID COA ' + unique_id);
            $('body').modalmanager('loading');

            var uninitialized = $('#datatable_coa').filter(function() {
                return !$.fn.DataTable.fnIsDataTable(this);
            });

            var $modal = $('#ajax-modal');

            setTimeout(function(){
                $modal.load('<?php echo base_url('general/modalbook/ajax_coa_by_id');?>.tpd', '', function(){
                    handleTableCOA(coa_id, detail_id, unique_id);

                    $modal.modal();

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
        })

        $('.btn-select-coa').live('click', function (e) {
            e.preventDefault();

            var coa_id = parseInt($(this).attr('coa-id')) || 0;
            var coa_code = $(this).attr('coa-code');
            var coa_desc = $(this).attr('coa-desc');

            var detail_id = parseInt($(this).attr('detail-id')) || 0;
            var unique_id = $(this).attr('unique-id');

            var debit = getAvailableDebit();
            console.log(debit + ' - ' + coa_code + ' - ' + unique_id);
            if(detail_id > 0){
                var closest_td = $('#table_detail > tbody > tr').find('input[name*="detail_id"][value="' + detail_id + '"]').closest('td');
                closest_td.find('input[name*="coa_id"]').val(coa_id);
                closest_td.find('input[name*="coa_code"]').val(coa_code);
                closest_td.closest('tr').find('input[name*="coa_desc"]').val(coa_desc);
                closest_td.closest('tr').find('input[name*="debit_value"]').val(debit);
            }else{
                var closest_td = $('#table_detail > tbody > tr').find('input[name*="unique_id"][value="' + unique_id + '"]').closest('td');
                closest_td.find('input[name*="coa_id"]').val(coa_id);
                closest_td.find('input[name*="coa_code"]').val(coa_code);
                closest_td.closest('tr').find('input[name*="coa_desc"]').val(coa_desc);
                closest_td.closest('tr').find('input[name*="debit_value"]').val(debit);
            }

            calculateDebit();

            $('#ajax-modal').modal('hide');
        });

        $('.sub_debit_change').live('keyup', function(){
            calculateDebit();
        });

        $('button[name="save"]').on('click', function(e){
            e.preventDefault();

            if($("#form-entry").valid()){
                var url = '<?php echo base_url('ar/debit/submit_corp_debit.tpd');?>';
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
                //$('#form-entry').submit();
            }
        });

        $('button[name="btn_save_close"]').on('click', function(e){
            e.preventDefault();

            if($("#form-entry").valid()){
                var url = '<?php echo base_url('ar/debit/submit_corp_debit.tpd');?>';
                $("#form-entry").append('<input type="hidden" name="save_close" value="">');
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
            }
        });

        //$('#submit-posting').click(function(e) {
        $('#posting-button').click(function(e) {
            e.preventDefault();

            $('#ajax-posting').modal('hide');

            var debitnote_id = $('input[name="debitnote_id"]').val();
            var posting_date = $('input[name="c_posting_date"]').val();

            if(debitnote_id > 0){
                bootbox.confirm({
                    message: "Posting this transaction ?<br><strong>Please make sure any changes has been saved.</strong>",
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
                                url: "<?php echo base_url('ar/debit/ajax_posting_debit_by_id');?>",
                                dataType: "json",
                                data: { debitnote_id: debitnote_id, posting_date : posting_date, is_corporate: 1}
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
        });
	});

    function getAvailableDebit()
    {
        var debit = 0;

        var parentDebit = parseFloat($('input[name="debit_amount"]').val()) || 0;
        if(parentDebit > 0){
            var alloc = 0;
            $('#table_detail > tbody > tr ').each(function() {
                var col2 = $(this).find('td:nth-child(4)');
                var debit_amount = parseFloat(col2.find('input[name*="debit_value"]').val()) || 0;
                alloc += debit_amount;
            });
            debit = parentDebit - alloc;
        }

        return debit;
    }

    function calculateDebit(){
        var total = 0;
        $('#table_detail > tbody > tr ').each(function() {
            var _amount = parseFloat($(this).find('input[name*="debit_value"]').val()) || 0;
            total += _amount;
        });
        $('input[name="total_debit"]').val(total);

    }

    function delete_record(detailId, uniqueId){
        if(detailId > 0 || uniqueId != ''){
            bootbox.confirm("Remove this record ?", function (result) {
                if (result == true) {
                    if(detailId > 0){
                        Metronic.blockUI({
                            target: '.modal-content-edit',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('ar/debit/delete_debit_detail');?>",
                            dataType: "json",
                            data: {detail_id : detailId}
                        })
                            .done(function( msg ) {
                                if(msg.type != '0'){

                                }
                                else {
                                    console.log('ajax delete unit.' + detailId);
                                }

                                delete_frontend_payment(detailId, '');
                                Metronic.unblockUI('.modal-content-edit');
                            });
                    }else{
                        delete_frontend_payment(0, uniqueId);
                    }
                }
            });
        }
    }

    function delete_frontend_payment(detailId,uniqueId){
        try{
            if(detailId > 0){
                $('#table_detail > tbody > tr').find('input[name*="detail_id"][value="' + detailId + '"]').parent().parent().remove();
            }else{
                $('#table_detail > tbody > tr').find('input[name*="unique_id"][value="' + uniqueId + '"]').parent().parent().remove();
            }

            calculateDebit();
        }catch(e){
            console.log(e);
        }
    }

    function posting_record(debitnoteId){
        var $modal_cal = $('#ajax-posting');

        /*
        if ($modal_cal.hasClass('bootbox') == false) {
            $modal_cal.addClass('modal-fix');
        }

        if ($modal_cal.hasClass('modal-overflow') === false) {
            $modal_cal.addClass('modal-overflow');
        }
        */
        if($("#form-entry").valid()){
            $modal_cal.modal();
        }else{
            toastr["error"]("Please save your changes first !", "Warning");
        }
    }

</script>