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
                    if($agent_id > 0){
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
                            <i class="fa fa-list"></i>Agent <?php echo ($agent_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('frontdesk/setup/agent_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('frontdesk/setup/submit_agent.tpd');?>" class="form-horizontal" id="validate-form" method="post" onsubmit="return false;" autocomplete="off">
                            <div class="form-body">
                                <input type="hidden" id="agent_id" name="agent_id" value="<?php echo ($agent_id > 0 ? $row->agent_id : '0');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="agent_name">Agent Name <span class="required">
									* </span></label>
                                    <div class="col-md-6">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Agent Name" type="text" class="form-control" name="agent_name" value="<?php echo ($agent_id > 0 ? $row->agent_name : '');?>" <?php echo ($hasEdit ? ($agent_id > 0 ? ($row->status != STATUS_NEW ? 'readonly' : '') : '') : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="agent_pic">PIC <span class="required">
									* </span></label>
                                    <div class="col-md-6">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Person In Charge" type="text" class="form-control" name="agent_pic" value="<?php echo ($agent_id > 0 ? $row->agent_pic : '');?>" <?php echo ($hasEdit ? ($agent_id > 0 ? ($row->status != STATUS_NEW ? 'readonly' : '') : '') : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="agent_phone">Phone</label>
                                    <div class="col-md-6">
                                        <textarea name="agent_phone" rows="2" class="form-control" style="resize: vertical;" <?php echo ($hasEdit ? ($agent_id > 0 ? ($row->status != STATUS_NEW ? 'readonly' : '') : '') : 'readonly'); ?> ><?php echo ($agent_id > 0 ? $row->agent_phone : ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="agent_email">Email</label>
                                    <div class="col-md-6">
                                        <textarea name="agent_email" rows="2" class="form-control" style="resize: vertical;" <?php echo ($hasEdit ? ($agent_id > 0 ? ($row->status != STATUS_NEW ? 'readonly' : '') : '') : 'readonly'); ?> ><?php echo ($agent_id > 0 ? $row->agent_email : ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="remark">Remark</label>
                                    <div class="col-md-6">
                                        <textarea name="remark" rows="2" class="form-control" style="resize: vertical;" <?php echo ($hasEdit ? ($agent_id > 0 ? ($row->status != STATUS_NEW ? 'readonly' : '') : '') : 'readonly'); ?> ><?php echo ($agent_id > 0 ? $row->remark : ''); ?></textarea>
                                    </div>
                                </div>
                                <?php
                                if($agent_id > 0){
                                    ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Status
                                        </label>
                                        <div class="col-md-4">
                                            <select name="status" class="form-control select2me">
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
                                        <a href="<?php echo base_url('frontdesk/setup/agent_manage/1.tpd');?>" class="btn default">Back</a>
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
                    agent_name: {
                        minlength: 2,
                        required: true
                    },
                    agent_pic: {
                        minlength: 5,
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