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

                if($param_id > 0){
                    $hasEdit = check_session_action(get_menu_id(), STATUS_EDIT);
                }else{
                    $hasEdit = check_session_action(get_menu_id(), STATUS_NEW);
                }

                ?>
                <!-- Begin: life time stats -->
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Cash Flow Account Parameter <?php echo ($param_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('finance/setup/cashflow_param_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('finance/setup/submit_cashflow_param');?>" class="form-horizontal" id="validate-form" method="post" >
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="param_id" name="param_id" value="<?php echo ($param_id > 0 ? $param_id : '0');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="class_id">COA Code<span class="required">
									* </span></label>
                                    <div class="col-md-6">
                                        <select name="coa_code" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="">Select...</option>
                                            <?php
                                            $qry_coa = $this->db->query('select * from gl_coa where status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') and is_display > 0');
                                            if($qry_coa->num_rows() > 0){
                                                foreach($qry_coa->result() as $row_coa){
                                                    echo '<option value="' . $row_coa->coa_code . '" ' . ($param_id > 0 ? ($row_coa->coa_code == $row->coa_code ? 'selected="selected"' : '') : '') . '>' . $row_coa->coa_code . ' - ' . $row_coa->coa_desc .  '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-8">
                                      <?php if($hasEdit){ ?>
                                        <button type="submit" class="btn blue-madison" name="save_close">Submit</button>
                                      <?php } ?>
                                        <a href="<?php echo base_url('finance/setup/cashflow_param_manage/1.tpd');?>" class="btn red-sunglo">Back</a>
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
                    coa_code: {
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