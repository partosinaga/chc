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

                if($deposittype_id > 0){
                    $hasEdit = check_session_action(get_menu_id(), STATUS_EDIT);
                }else{
                    $hasEdit = check_session_action(get_menu_id(), STATUS_NEW);
                }

                echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
                ?>
                <!-- Begin: life time stats -->
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Deposit Type <?php echo ($deposittype_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('finance/setup/deposit_type_manage/0/0.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('finance/setup/submit_deposit_type.tpd');?>" class="form-horizontal" id="validate-form" method="post" autocomplete="off">
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="deposittype_id" name="deposittype_id" value="<?php echo ($deposittype_id > 0 ? $row->deposittype_id : '');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="deposit_key">Key <span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Key" type="text" class="form-control" name="deposit_key" value="<?php echo ($deposittype_id > 0 ? $row->deposit_key : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="deposit_desc">Description <span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Description" type="text" class="form-control" name="deposit_desc" value="<?php echo ($deposittype_id > 0 ? $row->deposit_desc : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="coa_code">Type</label>
                                    <div class="col-md-4">
                                        <select name="coa_code" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="" <?php echo ($deposittype_id > 0 ? ($row->coa_code <= 0 ? 'selected="selected"' : '') : '');?> >None</option>
                                            <?php
                                            $qry_coa = $this->db->query('SELECT coa_code, coa_desc FROM gl_coa WHERE is_display > 0 AND status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') AND (coa_code LIKE \'2%\' or coa_code LIKE \'3%\') ORDER BY coa_code;');
                                            if($qry_coa->num_rows() > 0){
                                                foreach($qry_coa->result() as $row_coa){
                                                    echo '<option value="' . $row_coa->coa_code . '" ' . ($deposittype_id > 0 ? ($row_coa->coa_code == $row->coa_code ? 'selected="selected"' : '') : '') . '>' . $row_coa->coa_code . ' - ' . $row_coa->coa_desc . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                if($deposittype_id > 0){
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
                                        <a href="<?php echo base_url('finance/setup/deposit_type_manage/0/0.tpd');?>" class="btn red-sunglo">Back</a>
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
            digits: 0,
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

            var form1 = $('#validate-form');

            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    deposittype_key: {
                        minlength: 2,
                        required: true
                    },
                    deposittype_desc: {
                        minlength: 5,
                        required: true
                    },
                    coa_code:{
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