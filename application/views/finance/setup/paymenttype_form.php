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
            <div class="col-md-12">
                <?php

                if($paymenttype_id > 0){
                    $hasEdit = check_session_action(get_menu_id(), STATUS_EDIT);
                }else{
                    $hasEdit = check_session_action(get_menu_id(), STATUS_NEW);
                }

                ?>
                <!-- Begin: life time stats -->
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Payment Type <?php echo ($paymenttype_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('finance/setup/payment_type_manage/0/0.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('finance/setup/submit_payment_type.tpd');?>" class="form-horizontal" id="validate-form" method="post" autocomplete="off">
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="paymenttype_id" name="paymenttype_id" value="<?php echo ($paymenttype_id > 0 ? $row->paymenttype_id : '');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="paymenttype_code">Code <span class="required">
									* </span></label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Code" type="text" class="form-control" name="paymenttype_code" value="<?php echo ($paymenttype_id > 0 ? $row->paymenttype_code : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="paymenttype_desc">Description <span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Description" type="text" class="form-control" name="paymenttype_desc" value="<?php echo ($paymenttype_id > 0 ? $row->paymenttype_desc : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="payment_type">Type</label>
                                    <div class="col-md-3">
                                        <select name="payment_type" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="" <?php echo ($paymenttype_id > 0 ? ($row->payment_type <= 0 ? 'selected="selected"' : '') : '');?> >None</option>
                                            <option value="<?php echo(PAYMENT_TYPE::BANK_TRANSFER); ?>" <?php echo ($paymenttype_id > 0 ? ($row->payment_type == PAYMENT_TYPE::BANK_TRANSFER ? 'selected="selected"' : '') : '');?> ><?php echo strtoupper(PAYMENT_TYPE::caption(PAYMENT_TYPE::BANK_TRANSFER));?></option>
                                            <option value="<?php echo(PAYMENT_TYPE::CREDIT_CARD); ?>" <?php echo ($paymenttype_id > 0 ? ($row->payment_type == PAYMENT_TYPE::CREDIT_CARD ? 'selected="selected"' : '') : '');?> ><?php echo strtoupper(PAYMENT_TYPE::caption(PAYMENT_TYPE::CREDIT_CARD));?></option>
                                            <option value="<?php echo(PAYMENT_TYPE::CASH_ONLY); ?>" <?php echo ($paymenttype_id > 0 ? ($row->payment_type == PAYMENT_TYPE::CASH_ONLY ? 'selected="selected"' : '') : '');?> ><?php echo strtoupper(PAYMENT_TYPE::caption(PAYMENT_TYPE::CASH_ONLY));?></option>
                                            <option value="<?php echo(PAYMENT_TYPE::DEBIT_CARD); ?>" <?php echo ($paymenttype_id > 0 ? ($row->payment_type == PAYMENT_TYPE::DEBIT_CARD ? 'selected="selected"' : '') : '');?> ><?php echo strtoupper(PAYMENT_TYPE::caption(PAYMENT_TYPE::DEBIT_CARD));?></option>
                                            <option value="<?php echo(PAYMENT_TYPE::AR_TRANSFER); ?>" <?php echo ($paymenttype_id > 0 ? ($row->payment_type == PAYMENT_TYPE::AR_TRANSFER ? 'selected="selected"' : '') : '');?> ><?php echo strtoupper(PAYMENT_TYPE::caption(PAYMENT_TYPE::AR_TRANSFER));?></option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="coa_code">GL Code</label>
                                    <div class="col-md-4">
                                        <select name="coa_code" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="" <?php echo ($paymenttype_id > 0 ? ($row->coa_code <= 0 ? 'selected="selected"' : '') : '');?> >None</option>
                                            <?php
                                            $qry_coa = $this->db->query('SELECT coa_code, coa_desc FROM gl_coa WHERE is_display > 0 AND status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY coa_code;');
                                            if($qry_coa->num_rows() > 0){
                                                foreach($qry_coa->result() as $row_coa){
                                                    echo '<option value="' . $row_coa->coa_code . '" ' . ($paymenttype_id > 0 ? ($row_coa->coa_code == $row->coa_code ? 'selected="selected"' : '') : '') . '>' . $row_coa->coa_code . ' - ' . $row_coa->coa_desc . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group <?php echo ($paymenttype_id > 0 ? ($row->payment_type == PAYMENT_TYPE::CREDIT_CARD || $row->payment_type == PAYMENT_TYPE::PAYMENT_GATEWAY ? '' : 'hide') : 'hide')?>" id="credit_card_info">
                                    <label class="control-label col-md-2" for="card_percent">Bank Charge (%)</label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Bank Charge" type="text" class="form-control mask_currency" name="card_percent" value="<?php echo ($paymenttype_id > 0 ? $row->card_percent : '0');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                    <label class="control-label col-md-1" for="veritrans_fee">Veritrans</label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Veritrans Fee" type="text" class="form-control mask_currency" name="veritrans_fee" value="<?php echo ($paymenttype_id > 0 ? $row->veritrans_fee : '0');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>

                                <?php
                                if($paymenttype_id > 0){
                                    ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-2">Status
                                        </label>
                                        <div class="col-md-2">
                                            <select name="status" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                                <option value="<?php echo STATUS_NEW;?>" <?php echo ($row->status == STATUS_NEW ? 'selected="selected"' : '');?>>Active</option>
                                                <option value="<?php echo STATUS_INACTIVE;?>" <?php echo ($row->status == STATUS_INACTIVE ? 'selected="selected"' : '');?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-9">
                                      <?php if($hasEdit){ ?>
                                        <button type="submit" class="btn blue-madison" name="save_close">Submit</button>
                                      <?php } ?>
                                        <a href="<?php echo base_url('finance/setup/payment_type_manage/0/0.tpd');?>" class="btn red-sunglo">Back</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- END FORM-->
                    </div>
                </div>
                <!-- End: life time stats -->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<script>
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
    }

    $(document).ready(function(){
        handleMask();

        var handleValidation = function() {
            // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

            $.validator.addMethod("checkCOACode", function ()
                {
                    var valid = true;
                    try{
                        var payment_type = parseInt($('select[name="payment_type"]').val()) || 0;
                        if(payment_type.toString() == '<?php echo PAYMENT_TYPE::BANK_TRANSFER ?>'){
                            valid = true;
                        }else{
                            var coa_code = $('select[name="coa_code"]').val() || 0;
                            if(coa_code <= 0){
                                valid = false;
                            }
                        }
                    }catch(e){
                        valid = false;
                    }

                    return valid;
                },
                "GL Code must be selected."
            );

            var form1 = $('#validate-form');

            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    paymenttype_code: {
                        minlength: 2,
                        required: true
                    },
                    paymenttype_desc: {
                        minlength: 5,
                        required: true
                    },
                    payment_type: {
                        required: true
                    },
                    coa_code:{
                        "checkCOACode" : {}
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
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
                },

                submitHandler: function (form) {
                    form.submit(); // submit the form
                }
            });
        }

        handleValidation();

        $('select[name="payment_type"]').on('click', function(){
            var paymenttype = $('select[name="payment_type"]').val();

            if(paymenttype == '<?php echo PAYMENT_TYPE::CREDIT_CARD; ?>' || paymenttype == '<?php echo PAYMENT_TYPE::PAYMENT_GATEWAY; ?>'){
                $('#credit_card_info').removeClass('hide');
            }else{
                $('#credit_card_info').addClass('hide');
            }
        });
    });

</script>