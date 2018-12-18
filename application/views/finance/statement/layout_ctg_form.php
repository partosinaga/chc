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
                    $hasEdit = true;
                ?>
                <!-- Begin: life time stats -->
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Financial Statement Category<?php echo ($doc_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('finance/statement/layout_setting/0.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('finance/statement/register_ctg.tpd');?>" class="form-horizontal" id="validate-form" method="post" >
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="ctg_id" name="ctg_id" value="<?php echo ($doc_id > 0 ? $row->category_id : '0');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="report_type">Type</label>
                                    <div class="col-md-4">
                                        <select name="report_type" id="report_type" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="">-- SELECT --</option>
                                            <option value="<?php echo GLStatement::BALANCE_SHEET?>">BALANCE SHEET</option>
                                            <option value="<?php echo GLStatement::PROFIT_LOSS?>">INCOME STATEMENT</option>
                                            <option value="<?php echo GLStatement::CASH_FLOW?>">CASH FLOW</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="category_key">Category <span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <select name="category_key" id="category_key" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="">Select...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="category_caption">Description<span class="required">
									* </span></label>
                                    <div class="col-md-5">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Caption" type="text" class="form-control" id ="category_caption" name="category_caption" value="<?php echo ($doc_id > 0 ? $row->category_caption : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                      <?php if($hasEdit){ ?>
                                        <button type="submit" class="btn blue-madison" name="save_close">Submit</button>
                                      <?php } ?>
                                        <a href="<?php echo base_url('finance/statement/layout_setting/0.tpd');?>" class="btn red-sunglo">Back</a>
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
                    report_type: {
                        required: true
                    },
                    category_key: {
                        required: true
                    },
                    category_caption: {
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

            //Category change
            $('#report_type').on('change', function(){
                var id = $(this).val();
                //console.log(id);
                var html = '';
                if(id == 1){
                    html = '<option value="ASSET">ASSET</option>';
                    html += '<option value="LIABILITIES & EQUITY">LIABILITIES & EQUITY</option>';

                    $('#category_key').parent().children('.select2-container').children('.select2-choice').children('.select2-chosen').html('ASSET');
                    $('#category_key').val('ASSET');
                }else if(id == 2){
                    html = '<option value="OPERATING INCOME">OPERATING INCOME</option>';
                    html += '<option value="OPERATING EXPENSE">OPERATING EXPENSE</option>';
                    html += '<option value="NON-OPERATING INCOME">OTHER INCOME</option>';
                    html += '<option value="NON-OPERATING EXPENSE">OTHER EXPENSE</option>';

                    $('#category_key').parent().children('.select2-container').children('.select2-choice').children('.select2-chosen').html('OPERATING INCOME');
                    $('#category_key').val('OPERATING INCOME');
                }else{
                    html = '<option value="OPERATING INFLOW">OPERATING INFLOW</option>';
                    html += '<option value="OPERATING OUTFLOW">OPERATING OUTFLOW</option>';
                    html += '<option value="INVESTING INFLOW">INVESTING INFLOW</option>';
                    html += '<option value="INVESTING OUTFLOW">INVESTING OUTFLOW</option>';
                    html += '<option value="FINANCING INFLOW">FINANCING INFLOW</option>';
                    html += '<option value="FINANCING OUTFLOW">FINANCING OUTFLOW</option>';

                    $('#category_key').parent().children('.select2-container').children('.select2-choice').children('.select2-chosen').html('OPERATING INFLOW');
                    $('#category_key').val('OPERATING INFLOW');
                }

                $('#category_key').html(html);

            });
        }

        handleValidation();
    });


</script>