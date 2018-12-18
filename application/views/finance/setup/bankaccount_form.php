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

                if($bankaccount_id > 0){
                    $hasEdit = check_session_action(get_menu_id(), STATUS_EDIT);
                }else{
                    $hasEdit = check_session_action(get_menu_id(), STATUS_NEW);
                }

                ?>
                <!-- Begin: life time stats -->
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Banks <?php echo ($bankaccount_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('finance/setup/bank_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('finance/setup/submit_bankaccount.tpd');?>" class="form-horizontal" id="validate-form" method="post" autocomplete="off">
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="bankaccount_id" name="bankaccount_id" value="<?php echo ($bankaccount_id > 0 ? $row->bankaccount_id : '0');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="class_id">Bank<span class="required">
									* </span></label>
                                    <div class="col-md-2">
                                        <select name="bank_id" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="">Select...</option>
                                            <?php
                                            $qry_bank = $this->db->query('select * from fn_bank where status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ')');
                                            if($qry_bank->num_rows() > 0){
                                                foreach($qry_bank->result() as $row_bank){
                                                    echo '<option value="' . $row_bank->bank_id . '" ' . ($bankaccount_id > 0 ? ($row_bank->bank_id == $row->bank_id ? 'selected="selected"' : '') : '') . '>' . $row_bank->bank_code .  '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="bankaccount_code">Account No <span class="required">
									* </span></label>
                                    <div class="col-md-3">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Account No" type="text" class="form-control" name="bankaccount_code" value="<?php echo ($bankaccount_id > 0 ? $row->bankaccount_code : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="currencytype_id">Currency<span class="required">
									* </span></label>
                                    <div class="col-md-3">
                                        <select name="currencytype_id" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="">Select...</option>
                                            <?php
                                            $qry_curr = $this->db->query('select * from currencytype ');
                                            if($qry_curr->num_rows() > 0){
                                                foreach($qry_curr->result() as $row_curr){
                                                    echo '<option value="' . $row_curr->currencytype_id . '" ' . ($bankaccount_id > 0 ? ($row_curr->currencytype_id == $row->currencytype_id ? 'selected="selected"' : '') : '') . '>' . $row_curr->currencytype_code .  '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="coa_id">COA Code<span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <select name="coa_id" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="">Select...</option>
                                            <?php
                                            $qry_coa = $this->db->query('SELECT * FROM gl_coa WHERE is_display > 0 AND status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY coa_code;');
                                            if($qry_coa->num_rows() > 0){
                                                foreach($qry_coa->result() as $row_coa){
                                                    echo '<option value="' . $row_coa->coa_id . '" ' . ($bankaccount_id > 0 ? ($row_coa->coa_id == $row->coa_id ? 'selected="selected"' : '') : '') . '>' . $row_coa->coa_code . ' - ' . $row_coa->coa_desc . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="is_cash">Type
                                    </label>
                                    <div class="col-md-4">
                                        <select name="is_cash" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="0" <?php echo ($bankaccount_id > 0 ? ($row->iscash <= 0 ? 'selected="selected"' : '') :'');?>>BANK</option>
                                            <option value="1" <?php echo ($bankaccount_id > 0 ? ($row->iscash > 0 ? 'selected="selected"' : '') :'');?>>CASH</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="bankaccount_desc">Description</label>
                                    <div class="col-md-6">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Description" type="text" class="form-control" name="bankaccount_desc" value="<?php echo ($bankaccount_id > 0 ? $row->bankaccount_desc : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="is_veritrans_account"></label>
                                    <div class="col-md-6">
                                        <input type="checkbox" name="is_veritrans_account" value="1" <?php echo ($bankaccount_id > 0 ? $row->is_veritrans_account > 0 ? 'checked' : '' : '');?> class="form-control"><span>Linked to Veritrans payment</span>
                                    </div>
                                </div>
                                <?php
                                if($bankaccount_id > 0){
                                    ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Status
                                        </label>
                                        <div class="col-md-4">
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
                                    <div class="col-md-offset-3 col-md-9">
                                      <?php if($hasEdit){ ?>
                                        <button type="submit" class="btn blue-madison" name="save_close">Submit</button>
                                      <?php } ?>
                                        <a href="<?php echo base_url('finance/setup/bank_manage/1.tpd');?>" class="btn red-sunglo">Back</a>
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
    $(document).ready(function(){
        var handleValidation = function() {
            // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

            var form1 = $('#validate-form');

            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    bank_id: {
                        required: true
                    },
                    bankaccount_code: {
                        minlength: 2,
                        required: true
                    },
                    currencytype_id:{
                        required: true
                    },
                    coa_id:{
                        required: true
                    },
                    is_cash:{
                        required: true
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
    });

</script>