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
                <div class="portlet box <?php echo BOX;?>">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i>My Profile
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form action="<?php echo base_url('home/home/user_submit.tpd');?>" method="post" id="form_sample_icon" class="form-horizontal">
                            <input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Username</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" class="form-control" name="user_name" value="<?php echo ($user_id > 0 ? $row->user_name : '');?>" disabled />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Full Name <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" class="form-control" name="user_fullname" value="<?php echo ($user_id > 0 ? $row->user_fullname : '');?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Password <?php echo ($user_id == 0 ? '<span class="required"> * </span>' : '');?>
                                    </label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="password" class="form-control" name="user_password" <?php echo ($user_id == 0 ? 'required' : '');?>/>
                                            <?php
                                            if($user_id > 0){
                                                echo '<span class="help-block"> Only for change password.</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Email
                                    </label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" class="form-control" name="user_email" value="<?php echo ($user_id > 0 ? $row->user_email : '');?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Department
                                    </label>
                                    <div class="col-md-4">
                                        <select name="department_id" class="form-control select2me" disabled>
                                            <?php
                                            $qry_dept = $this->mdl_general->get('ms_department', array('status <> ' => STATUS_INACTIVE, 'status <>' => STATUS_DELETE), array(), 'department_name');
                                            foreach($qry_dept->result() as $row_dept){
                                                echo '<option value="' . $row_dept->department_id . '" ' . ($user_id > 0 ? ($row->department_id == $row_dept->department_id ? 'selected="selected"' : '') : '') . '>' . $row_dept->department_name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" name="save">Save</button>
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

<script>
    $(document).ready(function(){
        var handleValidation = function() {
            // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

            var form2 = $('#form_sample_icon');

            form2.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    user_name: {
                        minlength: 2,
                        required: true
                    },
                    user_fullname: {
                        minlength: 2,
                        required: true
                    },
                    user_email: {
                        email: true
                    },
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form2, -200);
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
                    form[0].submit(); // submit the form
                }
            });
        }

        handleValidation();
    });
</script>