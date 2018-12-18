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
                if($coa_id > 0){
                    $hasEdit = check_session_action(get_menu_id(), STATUS_EDIT);
                }else{
                    $hasEdit = check_session_action(get_menu_id(), STATUS_NEW);
                }

                ?>
                <!-- Begin: life time stats -->
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Chart Of Account<?php echo ($coa_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('finance/setup/coa_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('finance/setup/submit_coa.tpd');?>" class="form-horizontal" id="validate-form" method="post" autocomplete="off">
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="coa_id" name="coa_id" value="<?php echo ($coa_id > 0 ? $row->coa_id : '0');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="class_id">Class<span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <select name="class_id" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="">Select...</option>
                                            <?php
                                            $qry_class = $this->db->query('select * from gl_class where status <> ' . STATUS_DELETE);
                                            if($qry_class->num_rows() > 0){
                                                foreach($qry_class->result() as $row_class){
                                                    echo '<option value="' . $row_class->class_id . '" ' . ($coa_id > 0 ? ($row_class->class_id == $row->class_id ? 'selected="selected"' : '') : '') . '>' . $row_class->class_code . ' ' . $row_class->class_desc .  '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="coa_code">COA Code <span class="required">
									* </span></label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Code" type="text" class="form-control" name="coa_code" value="<?php echo ($coa_id > 0 ? $row->coa_code : '');?>" <?php echo ($hasEdit ? ($coa_id > 0 ? ($row->status != STATUS_NEW ? 'disabled' : '') : '') : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="coa_desc">Description<span class="required">
									* </span></label>
                                    <div class="col-md-5">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Description" type="text" class="form-control" name="coa_desc" value="<?php echo ($coa_id > 0 ? $row->coa_desc : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="is_debit">Normal Balance<span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <div class="radio-list" >
                                                <label class="radio-inline">
                                                    <input type="radio" name="is_debit" id="isDebitYes" value="1" <?php echo ($coa_id > 0 ? ($row->is_debit > 0 ? 'checked' : '') : '');?> <?php echo ($hasEdit ? '' : 'disabled'); ?> > Debit </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="is_debit" id="isDebitNo" value="0" <?php echo ($coa_id > 0 ? ($row->is_debit <= 0 ? 'checked' : '') : '');?> <?php echo ($hasEdit ? '' : 'disabled'); ?> > Credit </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="is_display">Visible</label>
                                    <div class="col-md-2">
                                        <select name="is_display" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="1" <?php echo ($coa_id > 0 ? ($row->is_display > 0 ? 'selected="selected"' : '') : ''); ?> >Yes</option>
                                            <option value="0" <?php echo ($coa_id > 0 ? ($row->is_display <= 0 ? 'selected="selected"' : '') : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                if($coa_id > 0){
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
                                        <button type="submit" class="btn green">Submit</button>
                                        <button type="submit" class="btn blue-madison" name="save_close">Save & Close</button>
                                      <?php } ?>
                                        <a href="<?php echo base_url('finance/setup/coa_manage/1.tpd');?>" class="btn red-sunglo">Back</a>
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
                    class_id: {
                        required: true
                    },
                    coa_code: {
                        minlength: 5,
                        required: true,
                        digits: true
                    },
                    coa_desc: {
                        minlength: 5,
                        required: true
                    },
                    is_debit: {
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