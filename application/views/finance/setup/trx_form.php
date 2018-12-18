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
                if($transtype_id > 0){
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
                            <i class="fa fa-list"></i>Transaction Type<?php echo ($transtype_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('finance/setup/trx_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('finance/setup/submit_trx.tpd');?>" class="form-horizontal" id="validate-form" method="post" autocomplete="off">
                            <div class="form-body">
                                <input type="hidden" id="transtype_id" name="transtype_id" value="<?php echo ($transtype_id > 0 ? $row->transtype_id : '0');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="class_id">Feature<span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <select name="feature_id" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="">Select...</option>
                                            <option value="<?php echo Feature::FEATURE_AR_RECEIPT;?>" <?php echo ($transtype_id > 0 ? ($row->feature_id == Feature::FEATURE_AR_RECEIPT ? 'selected="selected"' : '') : '');?>>AR Receipt</option>
                                            <option value="<?php echo Feature::FEATURE_AP_PAYMENT;?>" <?php echo ($transtype_id > 0 ? ($row->feature_id == Feature::FEATURE_AP_PAYMENT ? 'selected="selected"' : '') : '');?>>AP Payment</option>
                                            <option value="<?php echo Feature::FEATURE_GL_ENTRY;?>" <?php echo ($transtype_id > 0 ? ($row->feature_id == Feature::FEATURE_GL_ENTRY ? 'selected="selected"' : '') : '');?>>GL Entry</option>
                                            <option value="<?php echo Feature::FEATURE_GL_ADJUSTMENT;?>" <?php echo ($transtype_id > 0 ? ($row->feature_id == Feature::FEATURE_GL_ADJUSTMENT ? 'selected="selected"' : '') : '');?>>GL Adjustment</option>
                                            <option value="<?php echo Feature::FEATURE_STOCK_REQUEST;?>" <?php echo ($transtype_id > 0 ? ($row->feature_id == Feature::FEATURE_STOCK_REQUEST ? 'selected="selected"' : '') : '');?>>Stock Request</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="transtype_name">Trx Code <span class="required">
									* </span></label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Trx Code" type="text" class="form-control" name="transtype_name" value="<?php echo ($transtype_id > 0 ? $row->transtype_name : '');?>" <?php echo ($hasEdit ? ($transtype_id > 0 ? ($row->status != STATUS_NEW ? 'disabled' : '') : '') : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="transtype_desc">Description<span class="required">
									* </span></label>
                                    <div class="col-md-5">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Description" type="text" class="form-control" name="transtype_desc" value="<?php echo ($transtype_id > 0 ? $row->transtype_desc : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 for="coa_id">Linked COA</label>
                                    <div class="col-md-6">
                                        <select name="coa_id" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="0" <?php echo ($transtype_id > 0 ? ($row->coa_id <= 0 ? 'selected="selected"' : '') : '');?> >None</option>
                                            <?php
                                            $qry_coa = $this->db->query('SELECT * FROM gl_coa WHERE is_display > 0 AND status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY coa_code;');
                                            if($qry_coa->num_rows() > 0){
                                                foreach($qry_coa->result() as $row_coa){
                                                    echo '<option value="' . $row_coa->coa_id . '" ' . ($transtype_id > 0 ? ($row_coa->coa_id == $row->coa_id ? 'selected="selected"' : '') : '') . '>' . $row_coa->coa_code . ' - ' . $row_coa->coa_desc . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="due_interestrate">Interest / Penalty (%)</label>
                                    <div class="col-md-2">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="%" type="text" class="form-control" name="due_interestrate" value="<?php echo ($transtype_id > 0 ? $row->due_interestrate : '0.2');?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="has_stamp_duty">Stamp Duty</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <div class="radio-list" >
                                                <label class="radio-inline">
                                                    <input type="radio" name="has_stamp_duty" id="isStampYes" value="1" <?php echo ($transtype_id > 0 ? ($row->has_stamp_duty > 0 ? 'checked' : '') : '');?> <?php echo ($hasEdit ? '' : 'disabled'); ?> > Yes </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="has_stamp_duty" id="isStampNo" value="0" <?php echo ($transtype_id > 0 ? ($row->has_stamp_duty <= 0 ? 'checked' : '') : 'checked');?> <?php echo ($hasEdit ? '' : 'disabled'); ?> > No </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="doc_type">Prefix</label>
                                    <div class="col-md-3">
                                        <select name="doc_type" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="" <?php echo ($transtype_id > 0 ? ($row->doc_type == '' ? 'selected="selected"' : '') : '');?> >None</option>
                                            <?php
                                            $qry_doc = $this->db->query('SELECT * FROM document ORDER BY feature_id, doc_name;');
                                            if($qry_doc->num_rows() > 0){
                                                foreach($qry_doc->result() as $row_doc){
                                                    echo '<option value="' . $row_doc->doc_name . '" ' . ($transtype_id > 0 ? ($row_doc->doc_name == $row->doc_type ? 'selected="selected"' : '') : '') . '>' . $row_doc->doc_name . ' - ' . $row_doc->doc_desc . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                if($transtype_id > 0){
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
                                        <a href="<?php echo base_url('finance/setup/trx_manage/1.tpd');?>" class="btn red-sunglo">Back</a>
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
                    feature_id: {
                        required: true
                    },
                    transtype_name: {
                        minlength: 2,
                        required: true
                    },
                    transtype_desc: {
                        minlength: 5,
                        required: true
                    },
                    has_stamp_duty: {
                        required: true
                    },
                    due_interestrate: {
                        required: true,
                        number: true
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