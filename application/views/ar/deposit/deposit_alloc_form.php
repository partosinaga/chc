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
                $back_url = base_url('ar/corporate_bill/deposit_al/1.tpd');
                if($allocationheader_id > 0){
                    if($row->status != STATUS_NEW){
                        $form_mode = 'disabled';
                        $back_url = base_url('ar/corporate_bill/deposit_al/2.tpd');
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
							<i class="fa fa-user"></i><?php echo ($allocationheader_id > 0 ? '' : 'New');?> Guest Deposit Allocation
						</div>
						<div class="actions">
                            <a href="<?php echo (isset($row) ? $back_url : base_url('ar/corporate_bill/deposit_al/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="#" method="post" id="form-entry" class="form-horizontal">
							<input type="hidden" id="allocationheader_id" name="allocationheader_id" value="<?php echo $allocationheader_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn btn-sm btn-circle blue" name="save" id ="btn_save"><i class="fa fa-save"></i>&nbsp;Save</button>
                                        <?php
                                        if($allocationheader_id > 0){
                                            if($row->status == STATUS_NEW ){
                                        ?>
                                                &nbsp;
                                                <button type="button" class="btn btn-sm purple btn-circle" id="submit-posting" ><i class="fa fa-slack"></i>&nbsp;Posting</button>
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
                                            <label class="control-label col-md-3">Doc No</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="alloc_no" value="<?php echo ($allocationheader_id > 0 ? $row->alloc_no : 'NEW');?>" disabled />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="receipt_date">Date </label>
                                            <div class="col-md-4" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="alloc_date" value="<?php echo ($allocationheader_id > 0 ? dmy_from_db($row->alloc_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Remark</label>
                                            <div class="col-md-8">
                                                <textarea name="alloc_desc" rows="2" class="form-control" style="resize: vertical;"><?php echo ($allocationheader_id > 0 ? $row->alloc_desc : '') ;?></textarea>
                                            </div>
                                        </div>

									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Deposit</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="depositdetail_id" value="<?php echo ($allocationheader_id > 0 ? $row->depositdetail_id : '');?>">
                                                    <input type="hidden" name="company_id" value="<?php echo ($allocationheader_id > 0 ? $row->company_id : '');?>">
                                                    <input type="hidden" name="reservation_id" value="0">
                                                    <input type="text" class="form-control" name="deposit_no" value="<?php echo ($allocationheader_id > 0 ? $row->deposit_no . ' / ' . $row->deposit_key : '');?>" readonly />
                                                     <span class="input-group-btn">
                                                       <a id="btn_lookup_company" class="btn btn-success" href="javascript:;" >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">&nbsp;</label>
                                            <div class="col-md-8">
                                                <span id="company_name" class="control-label text-left bold"><?php echo ($allocationheader_id > 0 ? $row->company_name : '');?></span>
                                            </div>
                                        </div>
                                        <?php
                                        $show_pending = true;
                                        if($allocationheader_id > 0){
                                            if($row->status != STATUS_NEW){
                                                $show_pending = false;
                                            }
                                        }

                                        if($show_pending){
                                            ?>
                                            <!--div class="form-group">
                                                <label class="control-label col-md-3">Unpaid Amount</label>
                                                <div class="col-md-8">
                                                    <div class="input-inline ">
                                                        <div class="input-group">
                                                            <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                            <input type="text" id="pending_amount" name="pending_amount" class="form-control text-right mask_currency input-medium" value="<?php echo(isset($pending_amount) ? $pending_amount : 0); ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div-->
                                            <div class="form-group">
                                                <label class="control-label col-md-3">Deposit Amount</label>
                                                <div class="col-md-8">
                                                    <div class="input-inline ">
                                                        <div class="input-group">
                                                            <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                            <input type="text" id="available_amount" name="available_amount" class="form-control text-right mask_currency input-medium" value="<?php echo(isset($available_amount) ? $available_amount : 0); ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="form-group">
                                            <label class="control-label col-md-3">Allocate</label>
                                            <div class="col-md-8">
                                                <div class="input-inline ">
                                                    <div class="input-group">
                                                        <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                        <input type="text" id="alloc_amount" name="alloc_amount" value="<?php echo ($allocationheader_id > 0 ? ($row->alloc_amount) : '0') ;?>" class="form-control text-right mask_currency input-medium font-red-sunglo" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row " id="panel_detail">
                                    <input type="hidden" name="total_allocated_valid" value="">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-6">
                                        <table class="table table-striped table-bordered table-hover table-po-detail " id="table_pending_detail">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th class="text-center" width="30%">
                                                    Inv No
                                                </th>
                                                <th class="text-center" width="15%">
                                                    Date
                                                </th>
                                                <th class="text-center" width="15%">
                                                    Due Date
                                                </th>
                                                <th class="text-right " width="25%">
                                                    Pending Amount
                                                </th>
                                                <th class="text-center" width="3%" style="width:5%;padding-left:7px;">

                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if($allocationheader_id > 0){
                                                    if(isset($details)){
                                                        foreach($details as $bill){
                                                            $display = '<tr id="parent_' . $bill['inv_id'] . '' . '">
                                                                     <td style="vertical-align:middle;" class="text-center">
                                                                        <input type="hidden" name="invoice_id[]" value="' . $bill['inv_id'] . '">
                                                                        <span class="text-center">' . $bill['inv_no'] . '</span>
                                                                     </td>
                                                                     <td style="vertical-align:middle;" class="text-center">
                                                                        <span class="text-center">' . dmy_from_db($bill['inv_date']) . '</span>
                                                                     </td>
                                                                     <td style="vertical-align:middle;" class="text-center">
                                                                        <span class="text-center">' . dmy_from_db($bill['inv_due_date']) . '</span>
                                                                     </td>';

                                                            if($row->status == STATUS_NEW){
                                                                $display .= '
                                                                     <td style="vertical-align:middle;" class="control-label">
                                                                        <input type="text" name="pending_amount[]" value="' . $bill['pending_amount'] . '" class="form-control text-right mask_currency input-sm" readonly>
                                                                     </td>
                                                                     <td style="vertical-align:middle;padding-top:8px;padding-left:7px;">
                                                                        <input type="checkbox" name="inv_id[]" value="' . $bill['inv_id'] . '" ' . $bill['checked'] . ' class="chk_inv_id">
                                                                    </td>';
                                                            }else{
                                                                $display .= '
                                                                     <td style="vertical-align:middle;" class="control-label">
                                                                        <input type="text" name="pending_amount[]" value="' . $bill['pending_amount'] . '" class="form-control text-right mask_currency input-sm" readonly>
                                                                     </td>
                                                                     <td style="vertical-align:middle;padding-top:8px;padding-left:10px;">
                                                                        <i class="fa fa-check">
                                                                    </td>';
                                                            }

                                                            $display .= '</tr>';
                                                            echo $display;
                                                        }
                                                    }
                                                }
                                            ?>
                                            </tbody>
                                        </table>
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
                if($allocationheader_id > 0){
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
            $.validator.addMethod("validateAlloc", function (value, element)
                {
                    var valid = true;
                    try{
                        var alloc_amount = parseFloat($('input[name="alloc_amount"]').val()) || 0;
                        var available_amount = parseFloat($('input[name="available_amount"]').val()) || 0;
                        //var unpaid_amount = parseFloat($('input[name="pending_amount"]').val()) || 0;

                        if(alloc_amount <= 0){
                            valid = false;
                            toastr["error"]("Allocation amount must not empty", "Warning");
                        }

                        if(alloc_amount > available_amount){
                            valid = false;
                            toastr["error"]("Allocation amount must not bigger than Deposit", "Warning");
                        }

                    }catch(e){
                        console.log(e);
                        valid = false;
                    }

                    return valid;
                }
            )

            var form1 = $('#form-entry');
            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    receipt_date: {
                        required: true
                    },
                    depositdetail_id:{
                        required: true
                    },
                    alloc_desc: {
                        required: true
                    },
                    total_allocated_valid:{
                        validateAlloc : true
                    }
                },
                messages: {
                    receipt_date: "Date must be selected",
                    depositdetail_id: "Deposit must be selected",
                    alloc_desc: "Remark must not empty"
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.receipt_date != null){
                        toastr["error"](validator.invalid.receipt_date, "Warning");
                    }
                    if(validator.invalid.depositdetail_id != null){
                        toastr["error"](validator.invalid.depositdetail_id, "Warning");
                    }
                    if(validator.invalid.alloc_desc != null){
                        toastr["error"](validator.invalid.alloc_desc, "Warning");
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
        var grid1 = new Datatable();
        //COA
        var handleTableModal = function (num_index, depositdetail_id, allocationheader_id) {
            // Start Datatable Item
            grid1.init({
                src: $("#datatable_modal"),
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sWidth" : '12%' ,"sClass": "text-center"},
                        null,
                        null,
                        { "sWidth" : '12%' ,"sClass": "text-right", "bSortable": false},
                        { "sWidth" : '12%' ,"sClass": "text-right", "bSortable": false},
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '12%' }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, -1],
                        [10, 20, 50, 100, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('ar/deposit/get_modal_undeposit');?>/" + num_index + "/" + depositdetail_id + "/" + allocationheader_id // ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_modal_wrapper");

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
                $modal.load('<?php echo base_url('ar/deposit/xmodal_undeposit');?>.tpd', '', function () {
                    $modal.modal();

                    var allocationheader_id = $('input[name="allocationheader_id"]').val();
                    var depositdetail_id = $('input[name="depositdetail_id"]').val();
                    handleTableModal(num_index, depositdetail_id, allocationheader_id);

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

        $('.btn-select-record').live('click', function (e) {
            e.preventDefault();

            var company_id = parseInt($(this).attr('data-company-id')) || 0;
            var reservation_id = parseInt($(this).attr('data-reservation-id')) || 0;
            var company_name = $(this).attr('data-company-name');
            var depositdetail_id = $(this).attr('data-detail-id');
            var deposit_no = $(this).attr('data-deposit-no');
            var deposittype_key = $(this).attr('data-deposit-key');
            var pending_amount = $(this).attr('data-pending-amount');
            var available_amount = parseFloat($(this).attr('data-available-amount')) || 0;

            $('input[name="depositdetail_id"]').val(depositdetail_id);
            $('input[name="company_id"]').val(company_id);
            $('input[name="reservation_id"]').val(reservation_id);
            $('input[name="pending_amount"]').val(0);
            $('input[name="available_amount"]').val(available_amount);
            $('input[name="pending_amount"]').val(pending_amount);
            $('input[name="alloc_amount"]').val(0);
            $('input[name="deposit_no"]').val(deposit_no + ' / ' + deposittype_key);
            $('#company_name').html(company_name);

            //Looking Bills
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('ar/corporate_bill/xcorp_pending_invoice');?>",
                data: { company_id : company_id, reservation_id : reservation_id},
                async:false
            })
                .done(function( msg ) {
                    //console.log(msg);
                    $('#table_pending_detail > tbody').html(msg);
                });

            handleMask();
            //$.uniform.update();
            Metronic.initUniform();

            $('#ajax-modal').modal('hide');
        });

        $('input[name*="inv_id"]').live('click', function(){
            var isChecked = $(this).is(':checked');
            var tr = $(this).closest('tr');
            var paymentAmount = parseFloat($('#alloc_amount').val()) || 0;

            if(isChecked){
                var pendingAmount = parseFloat(tr.find('input[name*="pending_amount"]').val()) || 0;
                var amount = (paymentAmount + pendingAmount);
                $('#alloc_amount').val(amount);
                //$('input[name="min_amount"]').val(amount);
            }else{
                var pendingAmount = parseFloat(tr.find('input[name*="pending_amount"]').val()) || 0;
                var amount = (paymentAmount - pendingAmount);
                if(amount < 0){
                     amount = 0;
                }
                //$('input[name="min_amount"]').val(amount);
                $('#alloc_amount').val(amount);
            }
        });

        function validate_input(){
            var valid = true;

            if(valid){
                valid = $("#form-entry").valid();
            }

            return valid;
        }

        $('button[name="save"]').click(function(e){
            e.preventDefault();

            if(validate_input()){
                var url = '<?php echo base_url('ar/deposit/submit_deposit_al.tpd');?>';
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
            }
        });

        $('#submit-posting').click(function(e) {
            e.preventDefault();

            $('#ajax-posting').modal('hide');

            var alloc_id = parseFloat($('input[name="allocationheader_id"]').val()) || 0;
            if(alloc_id > 0){
                bootbox.confirm({
                    message: "Posting this Allocation ?<br><strong>Please make sure any changes has been saved.</strong>",
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
                                url: "<?php echo base_url('ar/deposit/xpost_deposit_al');?>",
                                dataType: "json",
                                data: { allocationheader_id: alloc_id}
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();

                                    if(msg.type == '1'){
                                        toastr["success"](msg.message, "Success");
                                        if(msg.redirect_link != ''){
                                            window.location.assign(msg.redirect_link);
                                        }
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

</script>