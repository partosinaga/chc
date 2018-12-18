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
                $isedit = true;
                $back_url = base_url('pos/pos/pos_manage/1.tpd');
                if ($bill_id > 0) {
                    if ($bill->status != STATUS_NEW) {
                        $isedit = false;
                        $back_url = base_url('pos/pos/pos_manage/2.tpd');
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
							<i class="fa fa-star"></i>POS Entry
						</div>
						<div class="actions">
                            <?php
                            if($bill_id > 0){
                                if($bill->status == STATUS_CLOSED || $bill->status == STATUS_POSTED ){
                            ?>
                                <a href="<?php echo base_url('pos/pos/pdf_salesreceipt/'. ($bill_id > 0 ? $bill->bill_id : '0') .'.tpd');?>" class="btn default blue-ebonyclay" target="_blank"><i class="fa fa-print"></i>&nbsp;Receipt</a>
                            <?php
                                }
                            }
                            ?>
                            <a href="<?php echo (isset($back_url) ? $back_url : base_url('pos/pos/pos_manage/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="#" onsubmit="return false;" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="bill_id" name="bill_id" value="<?php echo $bill_id;?>" />
                            <?php
                            if($isedit){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn blue-ebonyclay btn-sm btn-circle" name="save_close" id="save_close">Save</button>
                                        <?php
                                        if($bill_id > 0) {
                                            if($bill->status == STATUS_NEW) {
                                        ?>
                                        <button type="button" class="btn blue btn-sm btn-circle" name="btn_posting" id="btn-posting">Posting</button>
                                        <?php }
                                        } ?>
                                    </div>
								</div>
							</div>
                            <?php
                            }
                            ?>
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-sm-2 text-right">Client</label>
                                            <div class="col-md-7 col-sm-7" >
                                                <div class="input-group">
                                                    <input type="hidden" name="company_id" value="<?php echo ($bill_id > 0 ? $bill->company_id : '0');?>">
                                                    <input type="hidden" name="taxtype_id" value="<?php echo (isset($tax_type) ? $tax_type->taxtype_id : 0);?>">
                                                    <input type="hidden" name="taxtype_vat" value="<?php echo (isset($tax_type) ? ($tax_type->taxtype_percent > 0 ? $tax_type->taxtype_percent / 100 : 0) : 0);?>">
                                                    <input type="text" class="form-control" name="company_name" value="<?php echo ($bill_id > 0 ? $bill->company_name : '');?>" readonly/>
                                                     <span class="input-group-btn">
                                                       <a id="btn_lookup_company" class="btn btn-success " href="javascript:;" <?php echo($bill_id > 0 ? ($bill->company_id <= 0 ? 'disabled' : '') : '') ?> >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="icheck-inline">
                                                <label class="tooltips" data-original-title="Personal Client Only"><input type="checkbox" name="chk_is_personal" value="1" class="form-control "  <?php echo ($bill_id > 0 ? ($bill->company_id <= 0 ? 'checked' : '') : '')?>>
                                                     Personal</label>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 text-right" for="subject">Remark </label>
                                            <div class="col-md-10">
                                                <div class="input-icon right">
                                                    <i class="fa"></i>
                                                    <textarea name="remark" rows="2" class="form-control" style="resize: vertical;"><?php echo ($bill_id > 0 ? $bill->remark : '') ;?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Payment Type </label>
                                            <div class="col-md-6">
                                                <select name="paymenttype_id" class="select2me form-control input-medium">
                                                    <?php
                                                    $payments = $this->db->query('select * from ms_payment_type where status = '.STATUS_NEW . ' and
                                                      payment_type NOT IN(' . PAYMENT_TYPE::DEBIT_CARD . ',' .  PAYMENT_TYPE::CREDIT_CARD . ',' . PAYMENT_TYPE::PAYMENT_GATEWAY . ','. PAYMENT_TYPE::AR_TRANSFER .') order by pos ');
                                                    foreach($payments->result_array() as $payType){
                                                        if($payType['payment_type'] == PAYMENT_TYPE::AR_TRANSFER){
                                                            echo '<option value="'. $payType['paymenttype_id'] .'" payment-type="' . $payType['payment_type'] . '" card-percent="' . $payType['card_percent'] . '" ' . ($bill_id > 0 ? ($bill->paymenttype_id == $payType['paymenttype_id'] ? 'selected="selected"' : '') : '') . ' class="option_ar_transfer btn-primary bold' . ($bill_id > 0 ? ($bill->payment_type == PAYMENT_TYPE::AR_TRANSFER || $bill->company_id > 0 ? '' : 'hide') : '') . '">' . $payType['paymenttype_code'] . ' - ' . $payType['paymenttype_desc'] . '</option>';
                                                        }else{
                                                            echo '<option value="'. $payType['paymenttype_id'] .'" payment-type="' . $payType['payment_type'] . '" card-percent="' . $payType['card_percent'] . '" ' . ($bill_id > 0 ? ($bill->paymenttype_id == $payType['paymenttype_id'] ? 'selected="selected"' : '') : '') . '>' . $payType['paymenttype_code'] . ' - ' . $payType['paymenttype_desc'] . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="card_info" class="<?php echo ($bill_id > 0 ? ($bill->payment_type == PAYMENT_TYPE::CREDIT_CARD || $bill->payment_type == PAYMENT_TYPE::DEBIT_CARD ? '' : 'hide') : 'hide'); ?>">
                                            <div class="form-group" >
                                                <label class="control-label col-md-3" style="padding-top: 6px;">Name</label>
                                                <div class="col-md-6">
                                                    <input type="text" name="creditcard_name" value="<?php echo $bill_id > 0 ? $bill->card_name : '' ?>" class="form-control input-medium">
                                                </div>
                                            </div>
                                            <div class="form-group" >
                                                <label class="control-label col-md-3" style="padding-top: 6px;">Card No</label>
                                                <div class="col-md-6">
                                                    <input type="text" name="creditcard_no" value="<?php echo $bill_id > 0 ? $bill->card_no : '' ?>" class="form-control input-medium mask_credit_card">
                                                </div>
                                            </div>
                                            <div class="form-group <?php echo(($bill_id > 0 ? $bill->payment_type == PAYMENT_TYPE::CREDIT_CARD ? '' : 'hide' : 'hide')) ?>" id="card_info_expiry">
                                                <label class="control-label col-md-3" style="padding-top: 6px;">Expiry</label>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="col-md-3 col-sm-3">
                                                            <div class="row">
                                                                <select name="creditcard_expiry_month" class="select2me form-control">
                                                                    <?php
                                                                    for($i=1;$i<=12;$i++){
                                                                        echo '<option value="'. $i .'" ' . ($bill_id > 0 ? ($bill->card_expiry_month == $i ? 'selected="selected"' : '') : '') . '>' . (strlen($i) == 1 ? '0'. $i : $i) . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5 col-sm-5">
                                                            <select name="creditcard_expiry_year" class="select2me form-control">
                                                                <?php
                                                                $current = date('Y');
                                                                for($i=$current;$i<=$current+10;$i++){
                                                                    echo '<option value="'. $i .'" ' . ($bill_id > 0 ? ($deposit->card_expiry_year == $i ? 'selected="selected"' : '') : '') . '>' . (strlen($i) == 1 ? '0'. $i : $i) . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group clear <?php echo(($bill_id > 0 ? $bill->payment_type != PAYMENT_TYPE::CREDIT_CARD && $bill->payment_type != PAYMENT_TYPE::AR_TRANSFER ? '' : 'hide' : '')) ?>" id="bank_account_info">
                                            <label class="control-label col-md-3">Bank Account </label>
                                            <div class="col-md-6">
                                                <select name="bankaccount_id" class="select2me form-control input-medium bankaccount">
                                                    <option value="">-- Select --</option>

                                                    <?php
                                                    $banks = $this->db->query('select * from fn_bank_account where status = '. STATUS_NEW . ' order by bank_id, bankaccount_code');
                                                    $class_option_bank ='';
                                                    $class_option_cash = ' hide';

                                                    if($bill_id > 0) {
                                                        if ($bill->payment_type == PAYMENT_TYPE::BANK_TRANSFER) {
                                                            $class_option_cash = ' hide';
                                                            $class_option_bank = '';
                                                        } else {
                                                            $class_option_bank = ' hide';
                                                            $class_option_cash = '';
                                                        }
                                                    }

                                                    foreach($banks->result_array() as $bank){
                                                        if ($bank['iscash'] <= 0) {
                                                            echo '<option value="' . $bank['bankaccount_id'] . '" class="option_bank ' . $class_option_bank . '" ';
                                                            if ($bill_id > 0) {
                                                                if ($bank['bankaccount_id'] == $bill->bankaccount_id) {
                                                                    echo 'selected';
                                                                }
                                                            }
                                                            echo '>' . $bank['bankaccount_desc'] . '</option>';
                                                        } else {
                                                            echo '<option value="' . $bank['bankaccount_id'] . '" class="option_cash ' . $class_option_cash . '" ';
                                                            if ($bill_id > 0) {
                                                                if ($bank['bankaccount_id'] == $bill->bankaccount_id)
                                                                    echo 'selected';
                                                            }
                                                            echo '>' . $bank['bankaccount_desc'] . '</option>';
                                                        }
                                                    }

                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group clear <?php echo(($bill_id > 0 ? $bill->payment_type == PAYMENT_TYPE::AR_TRANSFER ? '' : 'hide' : 'hide')) ?>" id="ar_transfer_info">
                                            <div class="control-label col-md-10 text-center">
                                                <span class="label label-primary">
										Invoice will be made later through <strong>BILLING</strong> process</span></div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box light bg-inverse" >
                                            <div class="portlet light bg-inverse">
                                                <div class="portlet-body table-responsive">
                                                    <div class="col-md-12">
                                                            <!-- BEGIN TAB PORTLET-->
                                                            <table class="table table-striped table-hover table-bordered table-po-detail" id="table_extra" name="table_extra">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-left">Item</th>
                                                                    <th class="text-center" style="width: 10%;">Qty</th>
                                                                    <th class="text-right" style="width: 10%;">Price</th>
                                                                    <th class="text-right" style="width: 15%;">Amount</th>
                                                                    <th class="text-right" style="width: 10%;">Tax</th>
                                                                    <th class="text-right" style="width: 10%;">Subtotal</th>
                                                                    <th style="width: 10px;"></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                if ($bill_id > 0) {
                                                                    $qry_item = $this->mdl_general->get('view_pos_item_stock', array('is_service_item <=' => 0), array(), 'item_desc');

                                                                    $i = 0;
                                                                    foreach ($details->result() as $bill_detail) {
                                                                        $qry_i_det = $this->db->get_where('view_pos_item_stock', array('itemstock_id' => $bill_detail->item_id));
                                                                        $row_i_det = $qry_i_det->row();

                                                                        $unit = '';
                                                                        $item = '';

                                                                        $tax_percent = 0;
                                                                        $tax_code = '';
                                                                        $tax_exclude = 1;
                                                                        foreach ($qry_item->result() as $bill_item) {
                                                                            if($bill_item->itemstock_id == $bill_detail->item_id){
                                                                                $item .= '<option value="' . $bill_item->itemstock_id . '" item-desc="' . $bill_item->item_desc . '" data-price-lock="' . $bill_item->price_lock . '" data-unit-price="' . $bill_item->unit_price . '" data-unit-discount="' . $bill_item->unit_discount . '" tax-exclude="'. $bill_item->tax_exclude .'" tax-type-percent="' . $bill_item->taxtype_percent . '" tax-type-code="' . $bill_item->taxtype_code . '" selected="selected" is-service="' . $bill_item->is_service_item . '" unit-max-qty="'. $bill_item->itemstock_current_qty . '">[ ' . $bill_item->item_code . ' ] ' . $bill_item->item_desc . '</option>';
                                                                                $tax_code = $bill_item->taxtype_code;
                                                                                $tax_percent = $bill_item->taxtype_percent;
                                                                                $tax_exclude = $bill_item->tax_exclude;
                                                                            }

                                                                        }

                                                                        $qry_item_detail = $this->db->get_where('pos_item_stock', array('itemstock_id' => $bill_detail->item_id));
                                                                        $bill_item_detail = $qry_item_detail->row();

                                                                        $maxAttr = '';
                                                                        if($bill_item_detail->is_service_item <= 0){
                                                                            $maxAttr = ' max="' . ($bill_item_detail->itemstock_current_qty + $bill_detail->item_qty) . '"';
                                                                        }

                                                                        echo '<tr>
                                                                            <input type="hidden" name="billdetail_id[' . $i . ']" value="' . $bill_detail->billdetail_id . '">
                                                                            <input type="hidden" name="status[' . $i . ']" value="1" class="class_status">
                                                                            <td><select name="item_id[' . $i . ']" class="form-control form-filter input-sm select2me select_item" data-index="' . $i . '">
                                                                            ' . $item . '
                                                                            </select></td>
                                                                            <td >
                                                                            <textarea name="item_desc['. $i .']" rows="2" class="form-control hide" style="resize: vertical;font-size:11px;">' . $bill_detail->item_desc . '</textarea>
                                                                            <input type="text" name="item_qty[' . $i . ']" data-index="' . $i . '" value="' . $bill_detail->item_qty . '" class="form-control text-right input-sm mask_currency num_cal input_item_qty" ' . $maxAttr .'></td>
                                                                            <td ><input type="text" name="item_rate[' . $i . ']" data-index="' . $i . '" value="' . $bill_detail->rate . '" class="form-control text-right input-sm mask_currency num_cal" ' . ($row_i_det->price_lock > 0 ? 'readonly' : '') . '></td>
                                                                            <td ><input type="text" name="item_amount[' . $i . ']" data-index="' . $i . '" value="' . $bill_detail->amount . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td >
                                                                            <input type="hidden" name="taxtype_percent[' . $i . ']" value="' . $tax_percent . '" >
                                                                            <input type="hidden" name="tax_exclude[' . $i . ']" value="' . $tax_exclude . '" >
                                                                            <input type="text" name="tax_amount[' . $i . ']" data-index="' . $i . '" value="' . $bill_detail->tax . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td ><input type="text" name="subtotal_amount[' . $i . ']" data-index="' . $i . '" value="' . ($bill_detail->amount - $bill_detail->disc_amount + $bill_detail->tax) . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td style="vertical-align:middle;">
                                                                            <a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' . $i . '" style="margin-right:0px;"><i class="fa fa-times"></i></a></td>
                                                                            </tr>';
                                                                        $i++;
                                                                    }
                                                                }
                                                                ?>
                                                                </tbody>
                                                                <tfoot>
                                                                <tr>
                                                                    <th class="text-left" style="vertical-align: middle;">
                                                                        <?php if($form_mode == '') { ?>
                                                                            <a href="javascript:;" class="btn btn-circle green-meadow btn-sm charge_add">
                                                                                <i class="fa fa-plus"></i> Add </a>
                                                                        <?php } ?>
                                                                    </th>
                                                                    <th class="text-right" disabled colspan="4"><span class="form-control-static">&nbsp;</span></th>
                                                                    <th ><input type="text" name ="total_amount" id ="total_amount" class="form-control text-right mask_currency" value="<?php echo ($bill_id > 0 ? $bill->amount : 0);?>" readonly/></th>
                                                                    <th colspan="2" >&nbsp;</th>
                                                                </tr>
                                                                </tfoot>
                                                            </table>
                                                            <!-- END TAB PORTLET-->
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
	</div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    $(document).ready(function(){
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "right",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        };

        var isedit = <?php echo ($isedit ? 1 : 0); ?>;

        if(isedit == 0){
             $('#form-entry').block({
                 message: null ,
                 overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
             });
        }
        <?php
        if($bill_id > 0){
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
        }
        ?>

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
            $.validator.addMethod("checkIsPersonal", function ()
                {
                    var valid = true;
                    try{
                        //var checked = $('#chk_registered_tenant').val();
                        var checked = $('input:checked[name="chk_is_personal"]').length || 0;
                        if(checked <= 0){
                            //console.log("checkTenant ..." + checked);
                            var companyId = $('input[name="company_id"]').val() || 0;
                            if(companyId <= 0){
                                valid = false;
                                toastr["warning"]("Company must be selected.", "Warning");
                            }
                        }else{
                            var companyName = $('input[name="company_name"]').val();
                            if(companyName == ''){
                                valid = false;
                                toastr["warning"]("Client name not empty.", "Warning");
                            }
                        }
                    }catch(e){
                        //console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Client must be not empty or must be selected."
            );

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
                                toastr["warning"]("Bank Account must be selected.", "Warning");
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

            var form1 = $('#form-entry');
            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    company_name: {
                        checkIsPersonal:true
                    },
                    subject: {
                        required: true
                    },
                    bankaccount_id:{
                        validateBankAccount:true
                    },
                    remark:{
                        required: true
                    }
                },
                messages: {
                    company_name: "Client must not empty or must be selected",
                    subject: "Note must not empty",
                    bankaccount_id: "Bank Account must be selected",
                    remark : "Remark must not empty"
                },
                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.subject != null){
                        toastr["warning"](validator.invalid.subject, "Warning");
                    }

                    if(validator.invalid.remark != null){
                        toastr["warning"](validator.invalid.remark, "Warning");
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

        var grid1 = new Datatable();
        //COA
        var handleTableCompany = function (num_index, company_id, bill_id) {
            // Start Datatable Item
            grid1.init({
                src: $("#datatable_modal"),
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sWidth" : '40%' ,"sClass": "text-left"},
                        null,
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '11%' }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, -1],
                        [10, 20, 50, 100, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('ar/deposit/get_modal_companies');?>/" + num_index + "/" + company_id + "/" + bill_id // ajax source
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

        $('#btn_lookup_company').live('click', function(){
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
                $modal.load('<?php echo base_url('ar/deposit/xmodal_company');?>.tpd', '', function () {
                    $modal.modal();
                    handleTableCompany(num_index);

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
            var company_name = $(this).attr('data-company-name');

            $('input[name="company_name"]').val(company_name);
            $('input[name="company_id"]').val(company_id);

            $('#ajax-modal').modal('hide');
        });

        $('.charge_add').live('click', function (e) {
            e.preventDefault();

            var i = $('#table_extra tbody tr').length;

            var item = '';
            item += '<option value="0">-- Select Item --</option>';
            <?php
            $qry_item = $this->mdl_general->get('get_pos_item_active', array('is_package <=' => 0, 'is_service_item <=' => 0), array(), 'item_desc');
            foreach($qry_item->result() as $bill_item) {
            ?>
            item += '<option value="<?php echo $bill_item->itemstock_id;?>" item-desc="<?php echo $bill_item->item_desc;?>" data-price-lock="<?php echo $bill_item->price_lock;?>" data-unit-price="<?php echo $bill_item->unit_price;?>" data-unit-discount="<?php echo $bill_item->unit_discount;?>" tax-exclude="<?php echo $bill_item->tax_exclude;?>" tax-type-percent="<?php echo $bill_item->taxtype_percent;?>" tax-type-code="<?php echo $bill_item->taxtype_code;?>" is-service="<?php echo $bill_item->is_service_item;?>" unit-max-qty="<?php echo $bill_item->itemstock_current_qty;?>" ><?php echo '[ ' . $bill_item->item_code . ' ] ' . $bill_item->item_desc;?></option>';
            <?php
            }
            ?>

            //Add to temp
            var newRowContent = '<tr>' +
                '<input type="hidden" name="billdetail_id[' + i + ']" value="0">' +
                '<input type="hidden" name="status[' + i + ']" value="1" class="class_status">' +
                '<td><select name="item_id[' + i + ']" class="form-control form-filter input-sm select2me select_item" data-index="' + i + '">' +
                item +
                '</select>' +
                '</td>' +
                '<td >' +
                '<textarea name="item_desc[' + i + ']" rows="2" class="form-control hide" style="resize: vertical;font-size: 11px;"></textarea><input type="text" name="item_qty[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency num_cal input_item_qty" ></td>' +
                '<td ><input type="text" name="item_rate[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency num_cal" ></td>' +
                '<td ><input type="text" name="item_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                '<td >' +
                '<input type="hidden" name="taxtype_percent[' + i + ']" value="0">' +
                '<input type="hidden" name="tax_exclude[' + i + ']" value="1">' +
                '<input type="text" name="tax_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                '<td ><input type="text" name="subtotal_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                '<td style="vertical-align:middle;">' +
                '<a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' + i + '" style="margin-right:0px;"><i class="fa fa-times"></i></a></td>' +
                '</tr>';

            $('#table_extra tbody').append(newRowContent);

            $('select.select2me').select2();
            handleMask();
        });

        $('.select_item').live('change', function(){
            var item_id = parseInt($(this).val()) || 0;
            var i = $(this).attr('data-index');
            if (item_id > 0) {
                var item_desc = $('option:selected', this).attr('item-desc');
                var price_lock = parseInt($('option:selected', this).attr('data-price-lock')) || 0;
                var unit_price = parseFloat($('option:selected', this).attr('data-unit-price')) || 0;
                var unit_discount = parseFloat($('option:selected', this).attr('data-unit-discount')) || 0;
                var tax_rate = parseFloat($('option:selected', this).attr('tax-type-percent')) || 0;
                var tax_code = $('option:selected', this).attr('tax-type-code');
                var tax_exclude = $('option:selected', this).attr('tax-exclude');
                var is_service = parseInt($('option:selected', this).attr('is-service')) || 0;
                var unit_max_qty = $('option:selected', this).attr('unit-max-qty');
                var amount = unit_price - unit_discount;

                $('textarea[name="item_desc[' + i + ']"]').val(item_desc);

                $('input[name="item_qty[' + i + ']"]').val('1');

                if(is_service <= 0){
                    $('input[name="item_qty[' + i + ']"]').attr('max',unit_max_qty);
                }

                $('input[name="item_rate[' + i + ']"]').val(unit_price);
                $('input[name="item_amount[' + i + ']"]').val(amount);
                //$('input[name="taxtype_percent[' + i + ']"]').val(tax_rate);
                $('input[name="tax_code[' + i + ']"]').val(tax_code);
                $('input[name="tax_exclude[' + i + ']"]').val(tax_exclude);

                $('input[name="item_qty[' + i + ']"]').removeAttr('readonly');
                if (price_lock > 0) {
                    $('input[name="item_rate[' + i + ']"]').attr('readonly', 'readonly');
                } else {
                    $('input[name="item_rate[' + i + ']"]').removeAttr('readonly');
                }
            } else {
                $('textarea[name="item_desc[' + i + ']"]').val('');
                $('input[name="item_qty[' + i + ']"]').val('0');
                $('input[name="item_qty[' + i + ']"]').removeAttr('max');
                $('input[name="item_rate[' + i + ']"]').val('0');
                $('input[name="item_amount[' + i + ']"]').val('0');
                $('input[name="tax_amount[' + i + ']"]').val('0');
                //$('input[name="taxtype_percent[' + i + ']"]').val('0');
                $('input[name="subtotal_amount[' + i + ']"]').val('0');
                //$('input[name="tax_code[' + i + ']"]').val(tax_code);
                $('input[name="tax_exclude[' + i + ']"]').val(1);

                $('input[name="item_qty[' + i + ']"]').attr('readonly');
                $('input[name="item_rate[' + i + ']"]').attr('readonly');
            }

            calculate_row(i);
        });

        $('.num_cal').live('keyup', function(){
            var i = $(this).attr('data-index');

            calculate_row(i);
        });

        $(document.body).on('keyup', '.input_item_qty', function() {
            //console.log('qty pressed');

            var i = $(this).attr('data-index');

            var qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;
            var max = parseFloat($('select[name="item_id[' + i + ']"] option:selected').attr('unit-max-qty')) || 0;

            if (qty > max) {
                $('input[name="item_qty[' + i + ']"]').val(max);

                toastr["warning"]($('select[name="item_id[' + i + ']"]').select2('data').text + " Max qty is " + max, "Warning");

                calculate_row(i);
            }
        });

        $('input[name="chk_is_personal"]').on('click', function(){
            var checked = $('input:checked[name="chk_is_personal"]').length;
            //console.log('checked ' + checked);
            if(checked > 0){
                $('input:text[name="company_name"]').prop('readonly', false);
                $('input:text[name="company_name"]').val('');
                $('input[name="company_id"]').val(0);
                $('#btn_lookup_company').attr('disabled','disabled');
                $('.option_ar_transfer').addClass('hide');
            }else{
                $('input:text[name="company_name"]').prop('readonly', true);
                $('input:text[name="company_name"]').val('');
                $('input[name="company_id"]').val('0');
                $('#btn_lookup_company').removeAttr('disabled');
                $('.option_ar_transfer').removeClass('hide');
            }

            var option = $('select[name="paymenttype_id"]').find('option[payment-type="<?php echo PAYMENT_TYPE::BANK_TRANSFER; ?>"]');
            var paymenttype_id = option.val();
            $('select[name="paymenttype_id"]').select2("val", paymenttype_id);
            var ptype = "<?php echo PAYMENT_TYPE::BANK_TRANSFER; ?>";
            default_form_receipt(ptype);
        });

        $('select[name="paymenttype_id"]').on('change', function (e) {
            var element = $(this).find('option:selected');
            var ptype = element.attr("payment-type");
            default_form_receipt(ptype);
            $('input[name="creditcard_name"]').val('');
            $('input[name="creditcard_no"]').val('');
            //$('input[name="deposit_bankcharges"]').val(0);
            var card_percent = element.attr("card-percent");
            $('input[name="bank_charge_percent"]').val(card_percent);
            //if(amount > 0){
                //calculateBankCharge();
            //}
        });

        $('input[name="bank_charge_percent"]').live('keyup', function(){
            //console.log('keyup ' + $('input[name="bank_charge_percent"]').val());
            //calculateBankCharge();
        });

        function calculate_row(i) {
            var qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;
            var price = parseFloat($('input[name="item_rate[' + i + ']"]').val()) || 0;

            var amount = parseFloat((qty * price).toFixed(2)) || 0;

            var tax_exclude = $('input[name="tax_exclude[' + i + ']"]').val();
            var tax_rate = parseFloat($('input[name="taxtype_vat"]').val()) || 0;

            var tax_amount = 0;
            if(tax_rate > 0) {
                if (tax_exclude > 0) {
                    tax_amount = Math.round((tax_rate / 100) * amount);
                } else {
                    var division = (100 + tax_rate) / 100;
                    var base_amount = Math.round(amount / division);

                    tax_amount = Math.round(amount - base_amount);
                    amount = base_amount;
                }
            }

            var subtotal = amount + tax_amount;

            $('input[name="item_amount[' + i + ']"]').val(amount);
            $('input[name="tax_amount[' + i + ']"]').val(tax_amount);
            $('input[name="subtotal_amount[' + i + ']"]').val(subtotal);

            calculate_all();
        }

        function calculate_all(){
            var total_amount = 0;
            var ii = $('#table_extra tbody tr').length;
            for(var i=0;i<=ii;i++){
                var stat = $('input[name="status[' + i + ']"]').val();
                if(stat != '<?php echo STATUS_DELETE;?>') {
                    var amount = parseFloat($('input[name="subtotal_amount[' + i + ']"]').val()) || 0;
                    total_amount = amount + total_amount;
                }
            }

            $('input[name="total_amount').val(total_amount);
        }

        function calculateBankCharge(){
            /*
            var percent = parseFloat($('input[name="bank_charge_percent"]').val());
            var amount = parseFloat($('input[name="deposit_grossamount"]').val());
            if(percent > 0 && amount > 0){
                var bankCharge = (percent / 100) * amount;
                bankCharge = bankCharge.toFixed(0);
                $('input[name="deposit_bankcharges"]').val(bankCharge);
                $('input[name="deposit_paymentamount"]').val(amount - bankCharge);
            }
            */
        }

        $('.btn-remove').live('click', function(){
            var i = $(this).attr('data-index');
            var this_btn = $(this);

            bootbox.confirm("Are you sure want to delete?", function (result) {
                if (result == true) {
                    this_btn.closest('tr').addClass('hide');
                    this_btn.closest('tr').find('.class_status').val('9');

                    calculate_all();
                }
            });
        });

        function validate_submit(){
            var result = true;

            if($('.form-group').hasClass('has-error')){
                $('.form-group').removeClass('has-error');
            }

            if(result){
                result = $("#form-entry").valid();
            }

            var i = 0;
            var i_act = 0;
            var item_array = new Array();
            $('#table_extra > tbody > tr ').each(function() {
                if(!$(this).hasClass('hide')) {
                    var item_id = $('select[name="item_id[' + i + ']"]').val();
                    var item_desc = $('textarea[name="item_desc[' + i + ']"]').val();
                    var item_qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;
                    var item_qty_max = parseFloat($('input[name="item_qty[' + i + ']"]').attr('max')) || 0;
                    var item_rate = parseFloat($('input[name="item_rate[' + i + ']"]').val()) || 0;
                    var item_amount = parseFloat($('input[name="item_amount[' + i + ']"]').val()) || 0;
                    var item_discount = 0;
                    var item_tax = parseFloat($('input[name="tax_amount[' + i + ']"]').val()) || 0;

                    $('select[name="item_id[' + i + ']"]').removeClass('has-error');
                    $('input[name="item_qty[' + i + ']"]').removeClass('has-error');
                    $('input[name="item_rate[' + i + ']"]').removeClass('has-error');
                    $('input[name="item_amount[' + i + ']"]').removeClass('has-error');
                    $('input[name="tax_amount[' + i + ']"]').removeClass('has-error');

                    if(item_id == '' || item_id <= 0){
                        toastr["warning"]("Please select item description.", "Warning");
                        $('select[name="item_id[' + i + ']"]').addClass('has-error');
                        result = false;
                    }

                    /*
                    if (item_desc == '') {
                        toastr["warning"]("Please type item description.", "Warning");
                        $('textarea[name="item_desc[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    */

                    if(item_qty <= 0){
                        toastr["warning"]("Please input Item Qty.", "Warning");
                        $('input[name="item_qty[' + i + ']"]').addClass('has-error');
                        result = false;
                    }else{
                        if(item_qty_max > 0){
                            if(item_qty > item_qty_max){
                                toastr["error"]($('select[name="item_id[' + i + ']"]').select2('data').text + " Max qty is " + item_qty_max, "Warning");
                                $('input[name="item_qty[' + i + ']"]').addClass('has-error');
                                result = false;
                            }
                        }
                    }

                    if(item_rate <= 0){
                        toastr["warning"]("Please input Item Price.", "Warning");
                        $('input[name="item_rate[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    if(item_amount <= 0){
                        toastr["warning"]("Please input valid amount.", "Warning");
                        $('input[name="item_amount[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    i_act++;
                }
                i++;
            });

            if(i_act <= 0 ){
                toastr["warning"]("Detail cannot be empty.", "Warning");
                result = false;
            }

            return result;
        }

        $('#save_close').on('click', function(){

            Metronic.blockUI({
                target: '#form-entry',
                boxed: true,
                message: 'Processing...'
            });

            var btn = $(this).find("button[type=submit]:focus" );

            toastr.clear();

            if (validate_submit()) {
                var form_data = $('#form-entry').serializeArray();
                if (btn[0] == null){ }
                else {
                    if(btn[0].name === 'save_close'){
                        form_data.push({name: "save_close", value: 'save_close'});
                    }
                }

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('pos/pos/ajax_pos_submit');?>",
                    dataType: "json",
                    data: form_data
                })
                    .done(function( msg ) {
                        if(msg.valid == '0' || msg.valid == '1'){
                            if(msg.valid == '1'){
                                window.location.assign(msg.link);
                            }
                            else {
                                toastr["error"](msg.message, "Error");
                                $('#form-entry').unblock();
                            }
                        }
                        else {
                            toastr["error"]("Submit data failed, please try again later.", "Error");
                            $('#form-entry').unblock();
                        }
                    })
                    .fail(function () {
                        $('#form-entry').unblock();
                        toastr["error"]("Submit data failed, please try again later.", "Error");
                    });

            }
            else {
                $('#form-entry').unblock();
            }
        });

        $('#btn-posting').click(function(e) {
            e.preventDefault();

            bootbox.confirm("Are you sure want to Posting selected Billing ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        target: '.form-entry',
                        boxed: true,
                        message: 'Processing...'
                    });

                    //var form_data = $('#form-entry').serializeArray();
                    var bill_id = $('input[name="bill_id"]').val();

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('pos/pos/ajax_bill_single_posting');?>",
                        dataType: "json",
                        data: {bill_id : bill_id}
                    })
                        .done(function (msg) {
                            if(msg.valid == '0' || msg.valid == '1'){
                                if(msg.valid == '1'){
                                    window.location.assign(msg.link);
                                }
                                else {
                                    toastr["error"](msg.message, "Error");
                                    $('#form-entry').unblock();
                                }
                            }
                            else {
                                toastr["error"]("Posting failed, please try again later.", "Error");
                                $('#form-entry').unblock();
                            }
                        })
                        .fail(function () {
                            $('.form-entry').unblock();
                            toastr["error"]("Something has wrong, please try again later.", "Error");
                        });
                }
            });

        });

    });

    function default_form_receipt(ptype){
        if(ptype == '<?php echo PAYMENT_TYPE::CREDIT_CARD; ?>'){
            $('#card_info').removeClass('hide');
            $('#card_info_expiry').removeClass('hide');
            $('#card_info_rate').removeClass('hide');
            $('#bank_account_info').addClass('hide');
            $('.option_bank').addClass('hide');
            $('.option_cash').addClass('hide');
            $('#ar_transfer_info').addClass('hide');

            $('select[name="bankaccount_id"]').select2("val", "");
        }else if(ptype == '<?php echo PAYMENT_TYPE::BANK_TRANSFER; ?>'){
            $('#bank_account_info').removeClass('hide');
            $('#card_info').addClass('hide');
            $('#card_info_expiry').addClass('hide');
            $('#card_info_rate').addClass('hide');
            $('.option_bank').removeClass('hide');
            $('.option_cash').addClass('hide');
            $('#ar_transfer_info').addClass('hide');

            var bank_id = $('select[name="bankaccount_id"]').find('option[class="option_bank"]').val();
            $('select[name="bankaccount_id"]').select2("val", bank_id);

        }else if(ptype == '<?php echo PAYMENT_TYPE::DEBIT_CARD; ?>'){
            $('#bank_account_info').removeClass('hide');
            $('#card_info').removeClass('hide');
            $('#card_info_expiry').addClass('hide');
            $('#card_info_rate').addClass('hide');
            $('.option_bank').removeClass('hide');
            $('.option_cash').addClass('hide');
            $('#ar_transfer_info').addClass('hide');

            var bank_id = $('select[name="bankaccount_id"]').find('option[class="option_bank"]').val();
            $('select[name="bankaccount_id"]').select2("val", bank_id);
        }else if(ptype == '<?php echo PAYMENT_TYPE::CASH_ONLY; ?>'){
            $('#bank_account_info').removeClass('hide');
            $('#card_info').addClass('hide');
            $('#card_info_expiry').addClass('hide');
            $('#card_info_rate').addClass('hide');
            $('.option_bank').addClass('hide');
            $('.option_cash').removeClass('hide');
            $('#ar_transfer_info').addClass('hide');

            var bank_id = $('select[name="bankaccount_id"]').find('option[class="option_cash"]').val();
            $('select[name="bankaccount_id"]').select2("val", bank_id);
        }else{
            $('#card_info').addClass('hide');
            $('#card_info_expiry').addClass('hide');
            $('#card_info_rate').addClass('hide');
            $('#bank_account_info').addClass('hide');
            $('.option_bank').addClass('hide');
            $('.option_cash').addClass('hide');
            $('#ar_transfer_info').removeClass('hide');

            $('select[name="bankaccount_id"]').select2("val", "");
        }
    }

</script>