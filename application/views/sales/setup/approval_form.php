<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <?php
                $breadcrumbs = get_menu_name($this->uri->segment(1), $this->uri->segment(2), $this->uri->segment(3));
                foreach ($breadcrumbs as $breadcrumb) {
                    echo $breadcrumb;
                }
                ?>
            </ul>
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box <?php echo BOX; ?>">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i><?php echo($approval_id > 0 ? 'Edit' : 'New'); ?> SO Approval
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('sales/setup/approval/0.tpd'); ?>"
                               class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
                              <span class="hidden-480">
                              Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form action="<?php echo base_url('sales/setup/approval_submit.tpd'); ?>" method="post" id="form_sample_icon" class="form-horizontal">
                            <input type="hidden" name="approval_id" value="<?php echo $approval_id; ?>"/>

                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class')); ?>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Type <span class="required"> * </span>
                                    </label>

                                    <div class="col-md-3">
                                        <select name="type" class="form-control form-filter select2me" required>
                                            <option value=""> -- Select --</option>
                                            <option value="<?php echo DOC_SO ?>"  <?php echo ($approval_id > 0 && $row->document_type == DOC_SO ? 'selected="selected"' : '' ) ?>  >SALES ORDER</option>
                                            <option value="<?php echo DOC_DO ?>"  <?php echo ($approval_id > 0 && $row->document_type == DOC_DO ? 'selected="selected"' : '' ) ?>  >DELIVERY ORDER</option>
                                            <option value="<?php echo DOC_INV ?>"  <?php echo ($approval_id > 0 && $row->document_type == DOC_INV ? 'selected="selected"' : '' ) ?>  >SALES ORDER INVOICE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Approval Name <span class="required"> * </span>
                                    </label>

                                    <div class="col-md-3">
                                        <select name="user_id" class="form-control form-filter select2me" required>
                                            <option value=""> -- Select --</option>
                                            <?php
                                            $query = $this->db->query("SELECT * FROM ms_user WHERE status = 1");
                                            foreach($query->result() as $rows ){
                                                echo '<option value="' . $rows->user_id . '"  ' . ($approval_id > 0 && $rows->user_id == $row->user_id ? 'selected="selected"' : '') . ' >' . $rows->user_fullname . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Min Amount </label>

                                    <div class="col-md-3">
                                        <div class="right">
                                            <input type="text" class="form-control mask_currency" name="min_amount" value="<?php echo ($approval_id > 0 ? $row->min_amount : '');?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Max Amount </label>

                                    <div class="col-md-3">
                                        <div class="right">
                                            <input type="text" class="form-control mask_currency" name="max_amount" value="<?php echo($approval_id > 0 ? $row->max_amount : ''); ?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Level <span class="required"> * </span></label>

                                    <div class="col-md-3">
                                        <select name="level" class="form-control form-filter select2me" required>
                                            <option value=""> -- Select --</option>
                                            <?php
                                            $level = 5;
                                            for($a = 1; $a <= $level; $a++)
                                                echo '<option value="' . $a . '"  ' . ($approval_id > 0 && $a == $row->level ? 'selected="selected"' : '') . ' >Level ' . $a . '</option>';
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                if ($approval_id > 0) {
                                    echo '<div class="form-group">
                                                <label class="control-label col-md-3">Status<span class="required" aria-required="true"> * </span></label>

                                                <div class="col-md-3">
                                                     <select name="status" class="form-control form-filter select2me">
                                                        <option value="' . STATUS_NEW . '"  ' . ($row->status == STATUS_NEW ? 'selected="selected"' : '') . ' >Active</option>
                                                        <option value="' . STATUS_INACTIVE . '"  ' . ($row->status == STATUS_INACTIVE ? 'selected="selected"' : '') . ' >Inactive</option>
                                                     </select>
                                                </div>
                                            </div>';
                                }else{
                                    echo '<input type="hidden" name="status" value="'.STATUS_NEW.'"/>';
                                }

                                ?>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" name="save">Save</button>
                                        <button type="submit" class="btn blue-madison" name="save_close">Save & Close</button>
                                        <a href="<?php echo base_url('sales/setup/approval/0.tpd'); ?>" class="btn red-sunglo">Back</a>
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
    $('document').ready(function(){
        $(".mask_currency").inputmask("numeric", {
            radixPoint: ".",
            autoGroup: true,
            groupSeparator: ",",
            digits: 0,
            groupSize: 3,
            removeMaskOnSubmit: false,
            autoUnmask: true
        });
    })

</script>