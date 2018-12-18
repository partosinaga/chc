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
                $back_url = base_url('ar/folio/refund/1.tpd');
                if($refund_id > 0){
                    if($row->status != STATUS_NEW){
                        $form_mode = 'disabled';
                    }

                    if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                        $back_url = base_url('ar/folio/refund/2.tpd');
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
							<i class="fa fa-user"></i><?php echo ($refund_id > 0 ? '' : 'New');?> Refund
						</div>
						<div class="actions">
                            <?php
                            if($refund_id > 0){
                                if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED ){
                            ?>
                                <a href="<?php echo base_url('ar/refund/pdf_refundvoucher/'. ($refund_id > 0 ? $row->refund_id : '0') .'.tpd');?>" class="btn default blue-ebonyclay" target="_blank"><i class="fa fa-print"></i></a>
                            <?php
                                }
                            }
                            ?>
                            <a href="<?php echo (isset($row) ? $back_url : base_url('ar/folio/refund/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="#" method="post" id="form-entry" class="form-horizontal">
							<input type="hidden" id="refund_id" name="refund_id" value="<?php echo $refund_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn btn-sm green" name="save" id ="btn_save">Save</button>
										<button type="button" class="btn btn-sm blue-madison" name="save_close" id ="btn_save_close">Save & Close</button>

                                        <?php
                                        if($refund_id > 0){
                                            if($row->status == STATUS_NEW && $row->refund_amount > 0){
                                        ?>
                                                &nbsp;
                                                <button type="button" class="btn btn-sm blue btn-circle" id="posting-button" onclick="posting_record(<?php echo $refund_id; ?>);"><i class="fa fa-save"></i>&nbsp;Posting</button>
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
                                                    <input type="text" class="form-control" name="refund_date" value="<?php echo ($refund_id > 0 ? dmy_from_db($row->refund_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label class="control-label col-md-3">Bank Account </label>
                                            <div class="col-md-6">
                                                <select name="bankaccount_id" class="select2me form-control input-medium">
                                                    <option value="">-- Select --</option>
                                                    <?php
                                                    $banks = $this->db->query('select * from fn_bank_account where status = '.STATUS_NEW . ' order by bank_id, bankaccount_code');
                                                    foreach($banks->result_array() as $bank){
                                                        echo '<option value="'. $bank['bankaccount_id'] .'" ';
                                                        if(isset($row)){
                                                            if($bank['bankaccount_id'] == $row->bankaccount_id)
                                                                echo 'selected';
                                                        }
                                                        echo '>' . $bank['bankaccount_desc'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
										</div>
									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Refund No</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="refund_no" value="<?php echo ($refund_id > 0 ? $row->refund_no : 'NEW');?>" disabled />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Folio No</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="reservation_id" value="<?php echo ($refund_id > 0 ? $row->reservation_id : '');?>">
                                                    <input type="text" class="form-control" name="guest_name" value="<?php echo ($refund_id > 0 ? $row->reservation_code . ' / ' . $row->tenant_fullname : '');?>" readonly />
                                                     <span class="input-group-btn">
                                                       <a id="btn_lookup_rsvt" class="btn btn-success" href="javascript:;" >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Remark</label>
                                            <div class="col-md-9">
                                                <textarea name="remark" rows="2" class="form-control" style="resize: vertical;"><?php echo ($refund_id > 0 ? $row->remark : '') ;?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Unallocated Amount</label>
                                            <div class="col-md-8">
                                                <div class="input-inline ">
                                                    <div class="input-group">
                                                <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                <input type="text" id="refund_baseamount" name="base_amount" value="<?php echo ($refund_id > 0 ? $detail->row()->base_amount : '0') ;?>" class="form-control text-right mask_currency input-medium" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Refund Amount</label>
                                            <div class="col-md-8">
                                                <div class="input-inline ">
                                                    <div class="input-group">
                                                        <input type="hidden" name="refunddetail_id" value="<?php echo (isset($detail) ? $detail->row()->refunddetail_id : '0');?>">
                                                        <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                        <input type="text" id="refund_paymentamount" name="refund_amount" value="<?php echo ($refund_id > 0 ? $detail->row()->refund_amount : '0') ;?>" class="form-control text-right mask_currency input-medium font-red-sunglo" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
        <div class="row">
            <div class="col-md-12">
                <?php
                if($refund_id > 0){
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
                <div class="input-group " data-date-format="dd-mm-yyyy">
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
            var form1 = $('#form-entry');
            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    refund_date: {
                        required: true
                    },
                    bankaccount_id:{
                        required: true
                    },
                    reservation_id:{
                        required: true
                    },
                    remark: {
                        required: true
                    },
                    refund_amount:{
                        required: true,
                        min: 1
                    }
                },
                messages: {
                    refund_date: "Date must be selected",
                    bankaccount_id: "Bank Account must be selected",
                    reservation_id: "Reservation/Guest must be selected",
                    remark: "Remark must not empty",
                    refund_amount: "Refund Amount must not 0"
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.refund_date != null){
                        toastr["error"](validator.invalid.refund_date, "Warning");
                    }
                    if(validator.invalid.bankaccount_id != null){
                        toastr["error"](validator.invalid.bankaccount_id, "Warning");
                    }
                    if(validator.invalid.reservation_id != null){
                        toastr["error"](validator.invalid.reservation_id, "Warning");
                    }
                    if(validator.invalid.remark != null){
                        toastr["error"](validator.invalid.remark, "Warning");
                    }
                    if(validator.invalid.refund_amount != null){
                        toastr["error"](validator.invalid.refund_amount, "Warning");
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
                removeMaskOnSubmit: true,
                autoUnmask: true
            });
        }

        handleMask();

        //Reservation
        var grid_rsvt = new Datatable();
        //COA
        var handleTableRsvt = function (num_index, reservation_id, refund_id) {
            // Start Datatable Item
            grid_rsvt.init({
                src: $("#datatable_reservation"),
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "bSortable": true, "sWidth" : '10%' },
                        { "sClass": "text-center", "bSortable": true, "sWidth" : '10%' },
                        null,
                        null,
                        { "sWidth" : '11%' ,"sClass": "text-right"},
                        { "sWidth" : '12%' ,"sClass": "text-right"},
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '11%' }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, -1],
                        [10, 20, 50, 100, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('ar/refund/get_modal_reservation');?>/" + num_index + "/" + reservation_id + "/" + refund_id // ajax source
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

        $('#btn_lookup_rsvt').on('click', function(){
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
                $modal.load('<?php echo base_url('ar/refund/ajax_unallocated_reservation');?>.tpd', '', function () {
                    $modal.modal();

                    var refund_id = $('input[name="refund_id"]').val();
                    var rsvt_id = $('input[name="reservation_id"]').val();
                    handleTableRsvt(num_index, rsvt_id, refund_id);

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

        $('.btn-select-reservation').live('click', function (e) {
            e.preventDefault();

            var reservation_id = parseInt($(this).attr('data-reservation-id')) || 0;
            var reservation_code = $(this).attr('data-reservation-code');
            var tenant_name = $(this).attr('data-tenant-name');
            var company_name = $(this).attr('data-company-name');
            var status = $(this).attr('data-status');
            var amount = $(this).attr('data-amount') || 0;

            var caption = tenant_name;
            if(company_name != ''){
                //caption = " / " + company_name;
            }
            $('input[name="reservation_id"]').val(reservation_id);
            $('input[name="guest_name"]').val(reservation_code + ' / ' + caption);
            $('input[name="base_amount"]').val(amount);

            if(status == '<?php echo(ORDER_STATUS::RESERVED); ?>'){
                $('#refund_paymentamount').val(amount);
                $('#refund_paymentamount').attr('readonly','readonly');
            }else{
                $('#refund_paymentamount').val(0);
                $('#refund_paymentamount').removeAttr('readonly');
            }

            $('#ajax-modal').modal('hide');
        });

        $('.d_amount').live('keyup', function(){
            //calculateTotal();
        });

        function validate_input(){
            var valid = true;

            var base_amount = parseFloat($('#refund_baseamount').val()) || 0;
            var refund_amount = parseFloat($('#refund_paymentamount').val()) || 0;

            if(refund_amount <= 0 || refund_amount > base_amount){
                valid = false;
                toastr["error"]("Refund amount must not bigger than Unallocated amount", "Warning");
            }

            if(valid){
                valid = $("#form-entry").valid();
            }

            return valid;
        }

        $('button[name="save"]').click(function(e){
            e.preventDefault();

            if(validate_input()){
                var url = '<?php echo base_url('ar/refund/submit_refund.tpd');?>';
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
                //$('#form-entry').submit();
            }
        });

        $('button[name="save_close"]').click(function(e){
            e.preventDefault();

            if(validate_input()){
                var url = '<?php echo base_url('ar/refund/submit_refund.tpd');?>';
                $("#form-entry").append('<input type="hidden" name="save_close" value="">');
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
            }
        });

        $('#submit-posting').click(function(e) {
            e.preventDefault();

            $('#ajax-posting').modal('hide');

            var refund_id = $('input[name="refund_id"]').val();
            var posting_date = $('input[name="c_posting_date"]').val();

            if(refund_id > 0){
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
                                url: "<?php echo base_url('ar/refund/ajax_posting_refund_by_id');?>",
                                dataType: "json",
                                data: { refund_id: refund_id, posting_date : posting_date}
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

    function posting_record(refundId){
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