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
                if($deposit_id > 0){
                    if($deposit->status != STATUS_NEW){
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
							<i class="fa fa-user"></i><?php echo ($deposit_id > 0 ? '' : 'New');?> Deposit
						</div>
						<div class="actions">
                            <?php
                            if($deposit_id > 0){
                                if($deposit->status == STATUS_CLOSED || $deposit->status == STATUS_POSTED ){
                            ?>  <a href="<?php echo base_url('ar/report/pdf_official_receipt/'. ($deposit_id > 0 ? $deposit->deposit_no : '') .'.tpd');?>" class="btn default blue-ebonyclay" target="_blank"><i class="fa fa-print"></i>&nbsp;Receipt</a>
                                <a href="<?php echo base_url('ar/deposit/pdf_depositvoucher/'. ($deposit_id > 0 ? $deposit->deposit_id : '0') .'.tpd');?>" class="btn default blue-ebonyclay" target="_blank"><i class="fa fa-print"></i>&nbsp;Voucher</a>
                            <?php
                                }
                            }
                            ?>
                            <a href="<?php echo base_url('frontdesk/reservation/reservation_manage/1.tpd'); ?>" class="btn default green-stripe">
							    <i class="fa fa-slack "></i>
							    <span class="hidden-480"> Reservation </span>
							</a>
                            <a href="<?php echo(isset($deposit) ? ($deposit->status != STATUS_NEW ? base_url('frontdesk/reservation/folio_deposit/2.tpd') : base_url('frontdesk/reservation/folio_deposit/1.tpd')) : base_url('frontdesk/reservation/folio_deposit/1.tpd')); ?>"
                               class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
                        <span class="hidden-480">
                        Back </span>
                            </a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="<?php echo base_url('ar/deposit/submit_deposit.tpd');?>" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="deposit_id" name="deposit_id" value="<?php echo $deposit_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn btn-sm btn-circle blue" name="save" id ="btn_save">Save</button>

                                        <?php
                                        if($deposit_id > 0){
                                            if($deposit->status == STATUS_NEW && $deposit->deposit_paymentamount > 0){
                                        ?>
                                                &nbsp;
                                                <button type="button" class="btn btn-sm purple btn-circle" id="posting-button" ><i class="fa fa-save"></i>&nbsp;Posting</button>
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
											<label class="control-label col-md-3">Deposit No</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="deposit_no" value="<?php echo ($deposit_id > 0 ? $deposit->deposit_no : 'NEW');?>" disabled />
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Date </label>
                                            <div class="col-md-4" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="deposit_date" value="<?php echo ($deposit_id > 0 ? dmy_from_db($deposit->deposit_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Folio</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="reservation_id" value="<?php echo ($deposit_id > 0 ? $deposit->reservation_id : (isset($folio) ? $folio->reservation_id : ''));?>">
                                                    <input type="text" class="form-control" name="guest_name" value="<?php echo ($deposit_id > 0 ? $deposit->tenant_fullname : (isset($folio) ? $folio->tenant_fullname : ''));?>" readonly />
                                                     <span class="input-group-btn <?php echo ($deposit_id > 0 ? '' : (isset($folio) ? 'hide' : '')); ?>">
                                                       <a id="btn_lookup_folio" class="btn btn-success" href="javascript:;" >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Remark</label>
                                            <div class="col-md-9">
                                                <textarea name="deposit_desc" rows="2" class="form-control" style="resize: vertical;"><?php echo ($deposit_id > 0 ? $deposit->deposit_desc : '') ;?></textarea>
                                            </div>
                                        </div>

									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Payment Type </label>
                                            <div class="col-md-6">
                                                <select name="paymenttype_id" class="select2me form-control input-medium">
                                                    <?php
                                                    $payments = $this->db->query('select * from ms_payment_type where status = '.STATUS_NEW . ' and
                                                      payment_type NOT IN(' . PAYMENT_TYPE::AR_TRANSFER . ',' . PAYMENT_TYPE::PAYMENT_GATEWAY . ') order by pos ');
                                                    foreach($payments->result_array() as $payType){
                                                        echo '<option value="'. $payType['paymenttype_id'] .'" payment-type="' . $payType['payment_type'] . '" card-percent="' . $payType['card_percent'] . '" ' . ($deposit_id > 0 ? ($deposit->paymenttype_id == $payType['paymenttype_id'] ? 'selected="selected"' : '') : '') . '>' . $payType['paymenttype_code'] . ' - ' . $payType['paymenttype_desc'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="card_info" class="<?php echo ($deposit_id > 0 ? ($deposit->payment_type == PAYMENT_TYPE::CREDIT_CARD || $deposit->payment_type == PAYMENT_TYPE::DEBIT_CARD ? '' : 'hide') : 'hide'); ?>">
                                            <div class="form-group" >
                                                <label class="control-label col-md-3" style="padding-top: 6px;">Name</label>
                                                <div class="col-md-6">
                                                    <input type="text" name="creditcard_name" value="<?php echo $deposit_id > 0 ? $deposit->card_name : '' ?>" class="form-control input-medium">
                                                </div>
                                            </div>
                                            <div class="form-group" >
                                                <label class="control-label col-md-3" style="padding-top: 6px;">Card No</label>
                                                <div class="col-md-6">
                                                    <input type="text" name="creditcard_no" value="<?php echo $deposit_id > 0 ? $deposit->card_no : '' ?>" class="form-control input-medium mask_credit_card">
                                                </div>
                                            </div>
                                            <div class="form-group <?php echo(($deposit_id > 0 ? $deposit->payment_type == PAYMENT_TYPE::CREDIT_CARD ? '' : 'hide' : 'hide')) ?>" id="card_info_expiry">
                                                <label class="control-label col-md-3" style="padding-top: 6px;">Expiry</label>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="col-md-2 col-sm-2">
                                                            <div class="row">
                                                                <select name="creditcard_expiry_month" class="select2me form-control">
                                                                    <?php
                                                                    for($i=1;$i<=12;$i++){
                                                                        echo '<option value="'. $i .'" ' . ($deposit_id > 0 ? ($deposit->card_expiry_month == $i ? 'selected="selected"' : '') : '') . '>' . (strlen($i) == 1 ? '0'. $i : $i) . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-4">
                                                            <select name="creditcard_expiry_year" class="select2me form-control">
                                                                <?php
                                                                $current = date('Y');
                                                                for($i=$current;$i<=$current+10;$i++){
                                                                    echo '<option value="'. $i .'" ' . ($deposit_id > 0 ? ($deposit->card_expiry_year == $i ? 'selected="selected"' : '') : '') . '>' . (strlen($i) == 1 ? '0'. $i : $i) . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="form-group clear <?php echo(($deposit_id > 0 ? $deposit->payment_type != PAYMENT_TYPE::CREDIT_CARD ? '' : 'hide' : '')) ?>" id="bank_account_info">
                                            <label class="control-label col-md-3">Bank Account </label>
                                            <div class="col-md-6">
                                                <select name="bankaccount_id" class="select2me form-control input-medium bankaccount">
                                                    <option value="">-- Select --</option>

                                                    <?php
                                                    $banks = $this->db->query('select * from fn_bank_account where status = '.STATUS_NEW . ' order by bank_id, bankaccount_code');
                                                    foreach($banks->result_array() as $bank){
                                                        if($bank['iscash'] <= 0){
                                                            echo '<option value="'. $bank['bankaccount_id'] .'" class="option_bank" ';
                                                            if(isset($deposit)){
                                                                if($bank['bankaccount_id'] == $deposit->bankaccount_id){
                                                                    echo 'selected';
                                                                }
                                                            }
                                                            echo '>' . $bank['bankaccount_desc'] . '</option>';
                                                        }else{
                                                            echo '<option value="'. $bank['bankaccount_id'] .'" class="option_cash hide" ';
                                                            if(isset($deposit)){
                                                                if($bank['bankaccount_id'] == $deposit->bankaccount_id)
                                                                    echo 'selected';
                                                            }
                                                            echo '>' . $bank['bankaccount_desc'] . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Deposit Amount</label>
                                            <div class="col-md-8">
                                                <div class="input-inline ">
                                                    <div class="input-group">
                                                        <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                        <input type="text" id="deposit_grossamount" name="deposit_grossamount" value="<?php echo ($deposit_id > 0 ? ($deposit->deposit_paymentamount + $deposit->deposit_bankcharges): (isset($folio) ? $folio->min_deposit_amount : '0')) ;?>" class="form-control text-right mask_currency input-medium" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="hidden" name="deposit_min_amount" value="<?php echo ($deposit_id > 0 ? isset($deposit_min_amount) ? $deposit_min_amount : 0 : (isset($folio) ? $folio->min_deposit_amount : '0')) ;?>" >
                                            <input type="hidden" name="deposit_paymentamount" value="<?php echo ($deposit_id > 0 ? $deposit->deposit_paymentamount : '0') ;?>" >
                                            <div class="form-group <?php echo ($deposit_id > 0 ? $deposit->payment_type == PAYMENT_TYPE::CREDIT_CARD ? '' : 'hide' : 'hide') ; ?>" id="card_info_rate">
                                                <label class="control-label col-md-3">Bank Charge</label>
                                                <div class="col-md-2">
                                                    <div class="input-inline input-small" style="padding-right: 55px;">
                                                        <div class="input-group">

                                                            <input id="bank_charge_percent" class="form-control text-left mask_currency" type="text" name="bank_charge_percent" value="<?php echo ($deposit_id > 0 ? format_num($deposit->bank_charge_percent,2) : '2.00') ;?>" style="display: block;" readonly>
                                                            <span class="input-group-addon " style="font-size: 9pt;">%</span>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-inline " style="padding-left: 5px;">
                                                        <div class="input-group">
                                                            <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                            <input type="text" id="deposit_bankcharges" name="deposit_bankcharges" value="<?php echo ($deposit_id > 0 ? $deposit->deposit_bankcharges : '0') ;?>" class="form-control text-right input-small mask_currency" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="row">
									<div class="col-md-12">
										<table class="table table-striped table-hover table-bordered" id="deposit_detail">
											<thead>
												<tr>
													<th class="text-left" style="width: 30%;"> Type </th>
													<th class="text-left" > Description </th>
													<th class="text-center" style="width: 25%;"> Amount </th>
													<th class="text-center" style="width: 4%;"> </th>
												</tr>
											</thead>
											<tbody>
                                            <?php
                                            $sumDeposit = 0;
                                            if(isset($row_det)){
                                                if(count($row_det) > 0){
                                                    foreach($row_det as $deposit_det){
                                                        echo '<tr>';
                                                        echo '<td class="text-left" style="vertical-align:middle;">
                                                                <input type="hidden" name="detail_id[]" value="'. $deposit_det['detail_id'] . '"><input type="hidden" name="unique_id[]" value="">
                                                                <select name="deposittype_id[]" class="form-control form-filter input-sm select2me " >';
                                                        if($deposit_det['deposittype_id'] == 0){
                                                            //echo '<option value="0" selected="selected" >None</option>';
                                                        }else{
                                                            //echo '<option value="0" >None</option>';
                                                        }

                                                        if(count($deposittypes) > 0){
                                                            foreach($deposittypes as $control){
                                                                echo  '<option value="' . $control['deposittype_id'] . '" ' . ($deposit_det['deposittype_id'] > 0 ? ($deposit_det['deposittype_id'] == $control['deposittype_id'] ? 'selected="selected"' : '') : '') . '>' . $control['deposit_desc'] . '</option>';
                                                            }
                                                        }

                                                        echo  '</select>
                                                            </td>';

                                                        echo '<td style="vertical-align:middle;"><input type="text" name="detail_desc[]" value="' . $deposit_det['detail_desc'] . '" class="form-control" ></td>
                                                            <td ><input type="text" name="deposit_amount[]" value="' . number_format($deposit_det['deposit_amount'],0,'','') . '" class="form-control text-right mask_currency d_amount" '. $form_mode . '></td>';

                                                        if($deposit->status != STATUS_NEW){
                                                            echo   '<td style="vertical-align:middle;"></td>';
                                                        }else{
                                                            echo   '<td style="vertical-align:middle;"><a class="btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" onclick="delete_record(' . $deposit_det['detail_id'] . ',' . '' . ');" ><i class="fa fa-times"></i></a></td>';
                                                        }

                                                        echo   ' </tr>';
                                                        //$depositIndex++;

                                                        $sumDeposit += $deposit_det['deposit_amount'];
                                                    }
                                                }
                                            }
                                            ?>

											</tbody>
                                            <tfoot>
                                            <tr>
                                                <td >
                                                    <?php if($form_mode == '') { ?>
                                                    <a href="javascript:;" class="btn green-seagreen add_detail btn-circle btn-sm" ><i class="fa fa-plus"></i> Add</a>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right" disabled><span class="form-control-static">Total</span></td>
                                                <td ><input type="text" name ="total_deposit" id ="total_deposit" class="form-control text-right mask_currency" value="<?php echo number_format($sumDeposit,0,'','');?>" readonly/></td>
                                                <td colspan="2" >&nbsp;</td>
                                            </tr>
                                            </tfoot>
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
	</div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;
    var PAYMENT_CREDITCARD = <?php echo creditcard_paymenttypeid(); ?>;

    $(window).load(function(){
        //handleMask();
    });

    var tableDetail = $('#deposit_detail');
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
            $.validator.addMethod("validateBankAccount", function (value, element)
                {
                    var valid = true;
                    try{
                        var bankaccount_id = parseInt($('select[name="bankaccount_id"]').val()) || 0;

                        var element = $('select[name="paymenttype_id"]').find('option:selected');
                        var ptype = element.attr("payment-type");

                        if( ptype == '<?php echo PAYMENT_TYPE::BANK_TRANSFER; ?>' ||
                            ptype == '<?php echo PAYMENT_TYPE::DEBIT_CARD; ?>' ||
                            ptype == '<?php echo PAYMENT_TYPE::CASH_ONLY; ?>'){
                            if(bankaccount_id <= 0){
                                valid = false;

                            }
                        }

                        if(valid){

                        }
                    }catch(e){
                        //console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Bank Account must be selected."
            );

            $.validator.addMethod("validateDepositDetail", function (value, element)
                {
                    var valid = true;
                    try{
                        var i = 0;
                        $('#deposit_detail > tbody > tr ').each(function() {
                            var col2 = $(this).find('td:nth-child(3)');
                            var deposit_amount = parseFloat(col2.find('input[name*="deposit_amount"]').val()) || 0;

                            if(deposit_amount <= 0){
                                valid = false;
                                return;
                            }

                            i++;
                        });

                        if(valid){
                            var rowCount = i;
                            //console.log('checkDetail count = ' + rowCount);
                            if(rowCount <= 0 ){
                                valid = false;
                                toastr["error"]("Deposit detail must not empty", "Warning");
                            }
                        }

                        if(valid){
                            var gross = parseFloat($('#deposit_grossamount').val()) || 0;
                            var total = parseFloat($('#total_deposit').val()) || 0;
                            if(total != gross){
                                valid = false;
                                toastr["error"]("Detail total must equal with Deposit amount", "Warning");
                            }
                        }
                    }catch(e){
                        //console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Total Amount must not 0."
            );

            var form1 = $('#form-entry');
            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    deposit_date: {
                        required: true
                    },
                    bankaccount_id:{
                        validateBankAccount:true
                    },
                    paymenttype_id:{
                        required: true
                    },
                    reservation_id:{
                        required: true
                    },
                    deposit_desc: {
                        required: true
                    },
                    deposit_grossamount:{
                        required: true,
                        min: 1
                    },
                    deposit_paymentamount:{
                        validateDepositDetail:true
                    }
                },
                messages: {
                    deposit_date: "Date must be selected",
                    bankaccount_id: "Bank Account must be selected",
                    paymenttype_id: "Payment Type must be selected",
                    reservation_id: "Folio must be selected",
                    deposit_desc: "Remark must not empty",
                    deposit_grossamount: "Deposit Amount must not 0",
                    deposit_paymentamount:"Deposit detail amount must not 0."
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.deposit_date != null){
                        toastr["error"](validator.invalid.deposit_date, "Warning");
                    }
                    if(validator.invalid.bankaccount_id != null){
                        toastr["error"](validator.invalid.bankaccount_id, "Warning");
                    }
                    if(validator.invalid.paymenttype_id != null){
                        toastr["error"](validator.invalid.paymenttype_id, "Warning");
                    }
                    if(validator.invalid.reservation_id != null){
                        toastr["error"](validator.invalid.reservation_id, "Warning");
                    }
                    if(validator.invalid.deposit_desc != null){
                        toastr["error"](validator.invalid.deposit_desc, "Warning");
                    }
                    if(validator.invalid.deposit_grossamount != null){
                        toastr["error"](validator.invalid.deposit_grossamount, "Warning");
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
                digits: 2,
                groupSize: 3,
                removeMaskOnSubmit: true,
                autoUnmask: true
            });

            $(".mask_credit_card").inputmask("mask", {"mask": "9999-9999-9999-9999"});
        }

        handleMask();

        var grid1 = new Datatable();
        var handleTableModal = function (num_index, reservation_id) {
            // Start Datatable Item
            grid1.init({
                src: $("#datatable_reservation"),
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sWidth" : '15%' ,"sClass": "text-left"},
                        null,
                        { "sWidth" : '5%' ,"sClass": "text-center"},
                        { "sWidth" : '5%' ,"sClass": "text-center"},
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '11%' }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, -1],
                        [10, 20, 50, 100, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('frontdesk/management/get_modal_deposit_folio');?>/" + num_index + "/" + reservation_id // ajax source
                    },
                    "fnDrawCallback": function( oSettings ) {
                        $('.tooltips').tooltip();
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

        $('#btn_lookup_folio').on('click', function(){
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
                $modal.load('<?php echo base_url('frontdesk/management/ajax_modal_deposit_folio');?>.tpd', '', function () {
                    $modal.modal();

                    var deposit_id = $('input[name="deposit_id"]').val();
                    var reservation_id = $('input[name="reservation_id"]').val();
                    handleTableModal(num_index, reservation_id);

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

        $('.btn-select-folio').live('click', function (e) {
            e.preventDefault();

            var reservation_id = parseInt($(this).attr('data-reservation-id')) || 0;
            var folio_no = $(this).attr('data-reservation-code');
            var guest_name = $(this).attr('data-tenant-name');
            var min_amount = parseFloat($(this).attr('data-min-amount')) || 0;

            $('input[name="reservation_id"]').val(reservation_id);
            $('input[name="guest_name"]').val(folio_no + " - " + guest_name);
            $('input[name="deposit_min_amount"]').val(min_amount);
            $('#deposit_grossamount').val(min_amount);

            $('#ajax-modal').modal('hide');
        });

        $('select[name="paymenttype_id"]').on('change', function (e) {
            var element = $(this).find('option:selected');
            var ptype = element.attr("payment-type");
            default_form_receipt(ptype);
            $('input[name="creditcard_name"]').val('');
            $('input[name="creditcard_no"]').val('');
            $('input[name="deposit_bankcharges"]').val(0);
            var card_percent = element.attr("card-percent");
            $('input[name="bank_charge_percent"]').val(card_percent);
            var amount = $('input[name="deposit_grossamount"]').val();
            $('input[name="deposit_paymentamount"]').val(amount);
            if(amount > 0){
                calculateBankCharge();
            }
        });

        $('.add_detail').live('click', function (e) {
            e.preventDefault();

            var date = new Date();
            var uniqueId = date.toLocaleString(['ban', 'id']);

            //Add to temp
            var newRowContent = "<tr>" +
                "<td class=\"text-left\" style=\"vertical-align:middle;\"><input type=\"hidden\" name=\"detail_id[]\" value=\"0\"><input type=\"hidden\" name=\"unique_id[]\" value=\"" + uniqueId + "\"><select name=\"deposittype_id[]\" class=\"form-control form-filter input-sm select2me \">" +
                //"<option value=\"0\" >None</option>" +
                <?php
                 if(isset($deposittypes)){
                    foreach($deposittypes as $control){
                ?>
                "<option value=\"<?php echo $control['deposittype_id']; ?>\" ><?php echo $control['deposit_desc']; ?></option>" +
                <?php
                    }
                 }
                 ?>
                "</select></td>" +
                "<td style=\"vertical-align:middle;\"><input type=\"text\" name=\"detail_desc[]\" value=\"\" class=\"form-control \"></td>" +
                "<td ><input type=\"text\" name=\"deposit_amount[]\" value=\"0\" class=\"form-control text-right mask_currency d_amount\"></td>" +
                "<td style=\"vertical-align:middle;\"><a class=\"btn btn-danger btn-xs tooltips\" data-original-title=\"Remove\" href=\"javascript:;\" onclick=\"delete_record(0,'" + uniqueId + "');\" ><i class=\"fa fa-times\"></i></a></td>" +
                "</tr>";

            $('#deposit_detail tbody').append(newRowContent);

            $('select.select2me').select2();

            handleMask();
        });

        $('input[name="bank_charge_percent"]').live('keyup', function(){
            //console.log('keyup ' + $('input[name="bank_charge_percent"]').val());
            calculateBankCharge();
        });

        $('input[name="deposit_paymentamount"]').live('keyup', function(){
            calculateBankCharge();
        });

        $('.d_amount').live('keyup', function(){
            calculateTotal();
        });

        function validate_input(){
            var valid = true;

            var deposit_min_amount = parseFloat($('input[name="deposit_min_amount"]').val());
            var deposit_gross = parseFloat($('#deposit_grossamount').val());

            if(deposit_gross < deposit_min_amount){
                var maskedMinReceipt = deposit_min_amount.formatMoney(0, '.', ',');
                toastr["error"]("Deposit amount must not less than 1 month of room charges or " + maskedMinReceipt, "Warning");
                valid = false;
            }
            if(valid){
                valid = $("#form-entry").valid();
            }

            return valid;
        }

        $('button[name="save"]').click(function(e){
            e.preventDefault();

            if(validate_input()){
                //var url = '<?php echo base_url('ar/deposit/submit_deposit.tpd');?>';
                var url = '<?php echo base_url('frontdesk/management/submit_deposit_folio.tpd');?>';
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
            }
        });

        $('#posting-button').on('click', function(e) {
            e.preventDefault();

            $('#ajax-posting').modal('hide');

            var deposit_id = parseFloat($('input[name="deposit_id"]').val()) || 0;
            if(deposit_id > 0){
                bootbox.confirm({
                    message: "Posting this Deposit ?<br><strong>Please make sure any changes has been saved.</strong>",
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
                                url: "<?php echo base_url('frontdesk/management/xposting_deposit_folio_by_id');?>",
                                dataType: "json",
                                data: { deposit_id: deposit_id}
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

    function calculateBankCharge(){
        var percent = parseFloat($('input[name="bank_charge_percent"]').val());
        var amount = parseFloat($('input[name="deposit_grossamount"]').val());
        if(percent > 0 && amount > 0){
            var bankCharge = (percent / 100) * amount;
            bankCharge = bankCharge.toFixed(0);
            $('input[name="deposit_bankcharges"]').val(bankCharge);
            $('input[name="deposit_paymentamount"]').val(amount - bankCharge);
        }
    }

    function calculateTotal(){
        var sum = 0;
        //Calculate total
        $('#deposit_detail > tbody > tr ').each(function() {
            var val = $(this).find('input[name="deposit_amount[]"]').val();
            sum += parseFloat(val);
        });

        $('#total_deposit').val(sum);
    }

    function delete_record(detailId, uniqueId){
        if(detailId > 0 || uniqueId != ''){
            bootbox.confirm("Remove this payment ?", function (result) {
                if (result == true) {
                    if(detailId > 0){
                        Metronic.blockUI({
                            target: '.modal-content-edit',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('frontdesk/management/delete_deposit_detail');?>",
                            dataType: "json",
                            data: { bookingreceipt_id : detailId}
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

                    calculateTotal();
                }
            });
        }
    }

    function delete_frontend_payment(detailId,uniqueId){
        try{
            if(detailId > 0){
                $('#deposit_detail > tbody > tr').find('input[name*="detail_id"][value="' + detailId + '"]').parent().parent().remove();
            }else{
                $('#deposit_detail > tbody > tr').find('input[name*="unique_id"][value="' + uniqueId + '"]').parent().parent().remove();
            }
        }catch(e){
            console.log(e);
        }
    }

    function default_form_receipt(ptype){
        if(ptype == '<?php echo PAYMENT_TYPE::CREDIT_CARD; ?>'){
            $('#card_info').removeClass('hide');
            $('#card_info_expiry').removeClass('hide');
            $('#card_info_rate').removeClass('hide');
            $('#bank_account_info').addClass('hide');
            $('.option_bank').addClass('hide');
            $('.option_cash').addClass('hide');

            $('select[name="bankaccount_id"]').select2("val", "");
        }else if(ptype == '<?php echo PAYMENT_TYPE::BANK_TRANSFER; ?>'){
            $('#bank_account_info').removeClass('hide');
            $('#card_info').addClass('hide');
            $('#card_info_expiry').addClass('hide');
            $('#card_info_rate').addClass('hide');
            $('.option_bank').removeClass('hide');
            $('.option_cash').addClass('hide');

            var bank_id = $('select[name="bankaccount_id"]').find('option[class="option_bank"]').val();
            $('select[name="bankaccount_id"]').select2("val", bank_id);

        }else if(ptype == '<?php echo PAYMENT_TYPE::DEBIT_CARD; ?>'){
            $('#bank_account_info').removeClass('hide');
            $('#card_info').removeClass('hide');
            $('#card_info_expiry').addClass('hide');
            $('#card_info_rate').addClass('hide');
            $('.option_bank').removeClass('hide');
            $('.option_cash').addClass('hide');

            var bank_id = $('select[name="bankaccount_id"]').find('option[class="option_bank"]').val();
            $('select[name="bankaccount_id"]').select2("val", bank_id);
        }else if(ptype == '<?php echo PAYMENT_TYPE::CASH_ONLY; ?>'){
            $('#bank_account_info').removeClass('hide');
            $('#card_info').addClass('hide');
            $('#card_info_expiry').addClass('hide');
            $('#card_info_rate').addClass('hide');
            $('.option_bank').addClass('hide');
            $('.option_cash').removeClass('hide');

            var bank_id = $('select[name="bankaccount_id"]').find('option[class="option_cash"]').val();
            $('select[name="bankaccount_id"]').select2("val", bank_id);
        }else{
            $('#card_info').addClass('hide');
            $('#card_info_expiry').addClass('hide');
            $('#card_info_rate').addClass('hide');
            $('#bank_account_info').addClass('hide');
            $('.option_bank').addClass('hide');
            $('.option_cash').addClass('hide');

            $('select[name="bankaccount_id"]').select2("val", "");
        }
    }

</script>