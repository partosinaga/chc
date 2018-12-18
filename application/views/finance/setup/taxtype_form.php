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

                if($taxtype_id > 0){
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
                            <i class="fa fa-list"></i>Tax Type <?php echo ($taxtype_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('finance/setup/other_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('finance/setup/submit_taxtype.tpd');?>" class="form-horizontal" id="validate-form" method="post" autocomplete="off">
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="taxtype_id" name="taxtype_id" value="<?php echo ($taxtype_id > 0 ? $row->taxtype_id : '');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="taxtype_code">Code <span class="required">
									* </span></label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Code" type="text" class="form-control" name="taxtype_code" value="<?php echo ($taxtype_id > 0 ? $row->taxtype_code : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="taxtype_desc">Description <span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Description" type="text" class="form-control" name="taxtype_desc" value="<?php echo ($taxtype_id > 0 ? $row->taxtype_desc : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="currencytype_id">Type</label>
                                    <div class="col-md-2">
                                        <select name="taxtype_category" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="0" <?php echo ($taxtype_id > 0 ? ($row->taxtype_category <= 0 ? 'selected="selected"' : '') :'');?>>Excluded</option>
                                            <option value="1" <?php echo ($taxtype_id > 0 ? ($row->taxtype_category > 0 ? 'selected="selected"' : '') :'');?>>Included</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row ">
                                    <div class="col-md-4 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-6" for="taxtype_percent">VAT </label>
                                            <div class="col-md-4">
                                                <div class="input-icon right">
                                                    <i class="fa"></i>
                                                    <input placeholder="VAT" type="text" class="form-control" name="taxtype_percent" value="<?php echo ($taxtype_id > 0 ? number_format($row->taxtype_percent,2,'.',',') : number_format(0,2,'.',','));?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="coa_id">VAT COA<span class="required">
									* </span></label>
                                            <div class="col-md-6">
                                                <select name="coa_id" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                                    <option value="0" <?php echo ($taxtype_id > 0 ? ($row->coa_id_wht <= 0 ? 'selected="selected"' : '') : '');?> >None</option>
                                                    <?php
                                                    $qry_coa = $this->db->query('SELECT * FROM gl_coa WHERE is_display > 0 AND status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY coa_code;');
                                                    if($qry_coa->num_rows() > 0){
                                                        foreach($qry_coa->result() as $row_coa){
                                                            echo '<option value="' . $row_coa->coa_id . '" ' . ($taxtype_id > 0 ? ($row_coa->coa_id == $row->coa_id ? 'selected="selected"' : '') : '') . '>' . $row_coa->coa_code . ' - ' . $row_coa->coa_desc . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="is_charge_default">&nbsp;</label>
                                    <div class="col-md-6" style="padding-top: 8px;">
                                        <input type="checkbox" name="is_charge_default" value="1" <?php echo ($taxtype_id > 0 ? $row->is_charge_default > 0 ? 'checked' : '' : '') ?>><span for="chk_registered_tenant" class="label bg-yellow-casablanca">Item VAT</span>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-4 ">
                                        <div class="form-group">
                                            <label class="control-label col-md-6" for="taxtype_wht">WHT </label>
                                            <div class="col-md-4">
                                                <div class="input-icon right">
                                                    <i class="fa"></i>
                                                    <input placeholder="WHT" type="text" class="form-control" name="taxtype_wht" value="<?php echo ($taxtype_id > 0 ? number_format($row->taxtype_wht,2,'.',',') : number_format(0,2,'.',','));?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label col-md-2" for="coa_id_wht">WHT COA<span class="required">
									* </span></label>
                                            <div class="col-md-6">
                                                <select name="coa_id_wht" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                                    <option value="0" <?php echo ($taxtype_id > 0 ? ($row->coa_id_wht <= 0 ? 'selected="selected"' : '') : '');?>>None</option>
                                                    <?php
                                                    $qry_coa = $this->db->query('SELECT * FROM gl_coa WHERE is_display > 0 AND status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY coa_code;');
                                                    if($qry_coa->num_rows() > 0){
                                                        foreach($qry_coa->result() as $row_coa){
                                                            echo '<option value="' . $row_coa->coa_id . '" ' . ($taxtype_id > 0 ? ($row_coa->coa_id == $row->coa_id_wht ? 'selected="selected"' : '') : '') . '>' . $row_coa->coa_code . ' - ' . $row_coa->coa_desc . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if($taxtype_id > 0){
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
                                        <a href="<?php echo base_url('finance/setup/other_manage/1.tpd');?>" class="btn red-sunglo">Back</a>
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
                    taxtype_code: {
                        minlength: 2,
                        required: true
                    },
                    taxtype_desc: {
                        minlength: 5,
                        required: true
                    },
                    taxtype_category:{
                        required: true
                    },
                    taxtype_percent:{
                        number:true
                    },
                    taxtype_wht:{
                        number:true
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