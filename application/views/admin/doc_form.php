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
                    if($doc_id > 0){
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
                            <i class="fa fa-list"></i>Document <?php echo ($doc_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('admin/document/doc_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('admin/document/submit_doc.tpd');?>" class="form-horizontal" id="validate-form" method="post" autocomplete="off">
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="doc_id" name="doc_id" value="<?php echo ($doc_id > 0 ? $row->doc_id : '0');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="doc_name">Document Code <span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Code" type="text" class="form-control" name="doc_name" value="<?php echo ($doc_id > 0 ? $row->doc_name : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="doc_desc">Description</label>
                                    <div class="col-md-5">
                                        <input placeholder="Description" type="text" class="form-control" name="doc_desc" value="<?php echo ($doc_id > 0 ? $row->doc_desc : '');?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="doc_desc">Doc Length</label>
                                    <div class="col-md-5">
                                        <input placeholder="Length" type="text" class="form-control mask_number" name="doc_length" value="<?php echo ($doc_id > 0 ? $row->doc_length : 3);?>" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="feature_id">Feature<span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <select name="feature_id" class="form-control select2me" <?php echo ($hasEdit ? '' : 'disabled'); ?> >
                                            <option value="">Select...</option>
                                            <option value="<?php echo Feature::FEATURE_AR_RECEIPT;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AR_RECEIPT ? 'selected="selected"' : '') : '');?>>AR Receipt</option>
                                            <option value="<?php echo Feature::FEATURE_AR_BILLING;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AR_BILLING ? 'selected="selected"' : '') : '');?>>AR Billing</option>
                                            <option value="<?php echo Feature::FEATURE_AR_INVOICE;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AR_INVOICE ? 'selected="selected"' : '') : '');?>>AR Invoice</option>
                                            <option value="<?php echo Feature::FEATURE_AR_TRANSFER;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AR_TRANSFER ? 'selected="selected"' : '') : '');?>>AR Transfer</option>
                                            <option value="<?php echo Feature::FEATURE_AR_DEBIT_NOTE;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AR_DEBIT_NOTE ? 'selected="selected"' : '') : '');?>>AR Debit Note</option>
                                            <option value="<?php echo Feature::FEATURE_AR_CREDIT_NOTE;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AR_CREDIT_NOTE ? 'selected="selected"' : '') : '');?>>AR Credit Note</option>
                                            <option value="<?php echo Feature::FEATURE_AR_ALLOC;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AR_ALLOC ? 'selected="selected"' : '') : '');?>>AR Allocation</option>
                                            <option value="<?php echo Feature::FEATURE_AR_PROFORMA;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AR_PROFORMA ? 'selected="selected"' : '') : '');?>>AR Proforma Invoice</option>
                                            <option value="<?php echo Feature::FEATURE_AR_DELIVERY_ORDER;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AR_DELIVERY_ORDER ? 'selected="selected"' : '') : '');?>>AR Delivery Order</option>
                                            <option value="<?php echo Feature::FEATURE_CS_RESERVATION;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_CS_RESERVATION ? 'selected="selected"' : '') : '');?>>CS Reservation</option>
                                            <option value="<?php echo Feature::FEATURE_CS_CHECKIN;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_CS_CHECKIN ? 'selected="selected"' : '') : '');?>>CS Check In</option>
                                            <option value="<?php echo Feature::FEATURE_CS_SRF;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_CS_SRF ? 'selected="selected"' : '') : '');?>>CS SRF</option>
                                            <option value="<?php echo Feature::FEATURE_GL_ENTRY;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_GL_ENTRY ? 'selected="selected"' : '') : '');?>>GL Entry</option>
                                            <option value="<?php echo Feature::FEATURE_GL_ADJUSTMENT;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_GL_ADJUSTMENT ? 'selected="selected"' : '') : '');?>>GL Adjustment</option>
                                            <option value="<?php echo Feature::FEATURE_GL_RECURRING;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_GL_RECURRING ? 'selected="selected"' : '') : '');?>>GL Recurring</option>
                                            <option value="<?php echo Feature::FEATURE_PR;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_PR ? 'selected="selected"' : '') : '');?>>Purchase Requisition</option>
                                            <option value="<?php echo Feature::FEATURE_PO;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_PO ? 'selected="selected"' : '') : '');?>>Purchase Order</option>
                                            <option value="<?php echo Feature::FEATURE_GRN;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_GRN ? 'selected="selected"' : '') : '');?>>Goods Receive Note</option>
                                            <option value="<?php echo Feature::FEATURE_RETURN;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_RETURN ? 'selected="selected"' : '') : '');?>>Goods Return</option>
                                            <option value="<?php echo Feature::FEATURE_STOCK_REQUEST;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_STOCK_REQUEST ? 'selected="selected"' : '') : '');?>>Stock Request</option>
                                            <option value="<?php echo Feature::FEATURE_STOCK_ISSUE;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_STOCK_ISSUE ? 'selected="selected"' : '') : '');?>>Stock Issue</option>
                                            <option value="<?php echo Feature::FEATURE_STOCK_RECEIPT;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_STOCK_RECEIPT ? 'selected="selected"' : '') : '');?>>Stock Receipt</option>
                                            <option value="<?php echo Feature::FEATURE_STOCK_ADJUSTMENT;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_STOCK_ADJUSTMENT ? 'selected="selected"' : '') : '');?>>Stock Adjustment</option>
											<option value="<?php echo Feature::FEATURE_AP_INVOICE;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AP_INVOICE ? 'selected="selected"' : '') : '');?>>AP invoice</option>
                                            <option value="<?php echo Feature::FEATURE_AP_DEBIT_NOTE;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AP_DEBIT_NOTE ? 'selected="selected"' : '') : '');?>>AP Debit Note</option>
                                            <option value="<?php echo Feature::FEATURE_AP_CREDIT_NOTE;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AP_CREDIT_NOTE ? 'selected="selected"' : '') : '');?>>AP Credit Note</option>
                                            <option value="<?php echo Feature::FEATURE_AP_PAYMENT;?>" <?php echo ($doc_id > 0 ? ($row->feature_id == Feature::FEATURE_AP_PAYMENT ? 'selected="selected"' : '') : '');?>>AP Payment</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                       <?php if($hasEdit){ ?>
                                        <button type="submit" class="btn green">Submit</button>
                                        <button type="submit" class="btn blue-madison" name="save_close">Save & Close</button>
                                       <?php } ?>
                                        <a href="<?php echo base_url('admin/document/doc_manage/1.tpd');?>" class="btn default">Back</a>
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
        $(".mask_number").inputmask("mask", {"mask": "9"});

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
                    doc_name: {
                        minlength: 2,
                        required: true
                    },
                    feature_id: {
                        required: true
                    },
                    doc_length: {
                        min:3,
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