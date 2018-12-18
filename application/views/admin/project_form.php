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
                    if($project_id > 0){
                        $hasEdit = check_session_action(get_menu_id(), STATUS_EDIT);
                    }else{
                        $hasEdit = check_session_action(get_menu_id(), STATUS_NEW);
                    }
                ?>
                <!-- Begin: life time stats -->
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Project <?php echo ($project_id > 0 ? 'Edit' : 'Register');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('admin/project/project_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('admin/project/submit_project.tpd');?>" class="form-horizontal" id="validate-form" method="post" autocomplete="off">
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="project_id" name="project_id" value="<?php echo ($project_id > 0 ? $row->project_id : '0');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="project_initial">Code <span class="required">
									* </span></label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Code" type="text" class="form-control" name="project_initial" value="<?php echo ($project_id > 0 ? $row->project_initial : '');?>" <?php echo ($hasEdit ? ($project_id > 0 ? ($row->status != STATUS_NEW ? 'disabled' : '') : '') : 'disabled'); ?> <?php echo ($project_id > 0 ? 'readonly' : '');?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="project_name">Project Name</label>
                                    <div class="col-md-6">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                        <input placeholder="Project Name" type="text" class="form-control" name="project_name" value="<?php echo ($project_id > 0 ? $row->project_name : '');?>" <?php echo ($hasEdit ? ($project_id > 0 ? ($row->status != STATUS_NEW ? 'disabled' : '') : '') : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="project_address">Address</label>
                                    <div class="col-md-8">
                                        <textarea name="project_address" class="form-control" style="resize: vertical;" rows    ="4"><?php echo ($project_id > 0 ? $row->project_address : ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="po_report_note">PO Report Note</label>
                                    <div class="col-md-8">
                                        <textarea name="po_report_note" class="form-control " style="resize: vertical;" rows    ="8"><?php echo ($project_id > 0 ? $row->po_report_note : ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-10">
                                       <?php if($hasEdit){ ?>
                                        <button type="submit" class="btn blue-madison" name="save_close">Submit</button>
                                       <?php } ?>
                                        <a href="<?php echo base_url('admin/project/project_manage/1.tpd');?>" class="btn default">Back</a>
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
                    project_initial: {
                        minlength: 2,
                        required: true
                    },
                    project_name: {
                        minlength: 2,
                        required: true
                    },
                    project_address: {
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