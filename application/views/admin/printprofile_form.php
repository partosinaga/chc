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
                    if($printprofile_id > 0){
                        $hasEdit = check_session_action(get_menu_id(), STATUS_EDIT);
                    }else{
                        $hasEdit = check_session_action(get_menu_id(), STATUS_NEW);
                    }
                ?>
                <!-- Begin: life time stats -->
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Print Profile <?php echo ($printprofile_id > 0 ? 'Edit' : 'Register');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('admin/profile/printprofile_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('admin/profile/submit_printprofile.tpd');?>" class="form-horizontal" id="validate-form" enctype="multipart/form-data" method="post" autocomplete="off">
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="printprofile_id" name="printprofile_id" value="<?php echo ($printprofile_id > 0 ? $row->id : '0');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="key_code">Code <span class="required">
									* </span></label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Key" type="text" class="form-control" name="key_code" value="<?php echo ($printprofile_id > 0 ? $row->key_code : '');?>" <?php echo ($hasEdit ? ($printprofile_id > 0 ? ($row->status != STATUS_NEW ? 'readonly' : '') : '') : 'readonly'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="company_name">Company Name</label>
                                    <div class="col-md-6">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                        <input placeholder="Company Name" type="text" class="form-control" name="company_name" value="<?php echo ($printprofile_id > 0 ? $row->company_name : '');?>" <?php echo ($hasEdit ? ($printprofile_id > 0 ? ($row->status != STATUS_NEW ? 'readonly' : '') : '') : 'readonly'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="company_address">Address</label>
                                    <div class="col-md-8">
                                        <textarea name="company_address" class="form-control" style="resize: vertical;" rows="4"><?php echo ($printprofile_id > 0 ? $row->company_address : ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="approver_name">Approver Name</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                        <input placeholder="Approver Name" type="text" class="form-control" name="approver_name" value="<?php echo ($printprofile_id > 0 ? $row->approver_name : '');?>" <?php echo ($hasEdit ? ($printprofile_id > 0 ? ($row->status != STATUS_NEW ? 'readonly' : '') : '') : 'readonly'); ?> >
                                        </div>
                                    </div>
                                    <label class="control-label col-md-1" for="approver_title">Title</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                        <input placeholder="Approver Title" type="text" class="form-control" name="approver_title" value="<?php echo ($printprofile_id > 0 ? $row->approver_title : '');?>" <?php echo ($hasEdit ? ($printprofile_id > 0 ? ($row->status != STATUS_NEW ? 'readonly' : '') : '') : 'readonly'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="report_note">Note</label>
                                    <div class="col-md-8">
                                        <textarea name="report_note" class="ckeditor form-control" style="resize: vertical;" rows="4"><?php echo ($printprofile_id > 0 ? $row->report_note : ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="company_address">Terms</label>
                                    <div class="col-md-8">
                                        <textarea name="terms" class="form-control" style="resize: vertical;" rows="6"><?php echo ($printprofile_id > 0 ? $row->terms : ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="company_address">Footer 1</label>
                                    <div class="col-md-8">
                                        <textarea name="report_footer" class="form-control" style="resize: vertical;" rows="4"><?php echo ($printprofile_id > 0 ? $row->report_footer : ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="company_address">Footer 2</label>
                                    <div class="col-md-8">
                                        <textarea name="report_footer2" class="form-control" style="resize: vertical;" rows="4"><?php echo ($printprofile_id > 0 ? $row->report_footer2 : ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="company_address">Signature Note</label>
                                    <div class="col-md-8">
                                        <textarea name="signature_note" class="form-control" style="resize: vertical;" rows="4"><?php echo ($printprofile_id > 0 ? $row->signature_note : ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="picture_name">Image/Logo</label>
                                    <div class="col-md-9">
                                        <div class="fileinput fileinput-new" data-provides="fileinput" id="id_picture_image">
                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 400px; height: 100px;" >
                                                <?php
                                                if($printprofile_id > 0){
                                                    if(trim($row->picture_name) != '' && $row->picture_name != null){
                                                        echo '<img src="' . base_url($row->picture_name) . '" />';
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div>
													<span class="btn default btn-file">
													<span class="fileinput-new">
													Select image </span>
													<span class="fileinput-exists">
													Change </span>
													<input type="file" name="picture_image" >
													</span>
                                                <a href="#" class="btn red fileinput-<?php echo ($printprofile_id > 0 ? ($row->picture_name != '' ? 'new' : 'exists'):'exists'); ?>" data-dismiss="fileinput">
                                                    Remove </a>
                                            </div>
                                        </div>
                                        <div class="clearfix margin-top-10">
												<span class="label label-danger">
												NOTE! </span>
                                            Image preview only works in IE10+, FF3.6+, Safari6.0+, Chrome6.0+ and Opera11.1+. In older browsers the filename is shown instead.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-10">
                                       <?php if($hasEdit){ ?>
                                        <button type="submit" class="btn blue-madison" name="save_close">Submit</button>
                                       <?php } ?>
                                        <a href="<?php echo base_url('admin/profile/printprofile_manage/1.tpd');?>" class="btn default">Back</a>
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
        $("#id_picture_image").fileinput({'showRemove':true});

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
                    key_code: {
                        minlength: 2,
                        required: true
                    },
                    company_name: {
                        minlength: 2,
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