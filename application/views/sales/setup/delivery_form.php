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
                            <i class="fa fa-user"></i><?php echo($delivery_id > 0 ? 'Edit' : 'New'); ?> Delivery
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('sales/setup/delivery/0.tpd'); ?>"
                               class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i> <span class="hidden-480"> Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form action="<?php echo base_url('sales/setup/delivery_submit.tpd'); ?>" method="post" id="form_sample_icon" class="form-horizontal">
                            <input type="hidden" name="delivery_id" value="<?php echo $delivery_id; ?>"/>

                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class')); ?>

                                <div class="form-group">
                                    <label class="control-label col-md-3">Name <span class="required"> * </span></label>

                                    <div class="col-md-3">
                                        <div class="right">
                                            <input type="text" class="form-control " name="name" value="<?php echo ($delivery_id > 0 ? $row->delivery_type_name : '');?>" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Description <span class="required"> * </span></label>

                                    <div class="col-md-3">
                                        <div class="right">
                                            <textarea class="form-control" rows="3" name="desc" required><?php echo ($delivery_id > 0 ? $row->delivery_type_desc : '');?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if ($delivery_id > 0) {
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
                                        <a href="<?php echo base_url('sales/setup/delivery/0.tpd'); ?>" class="btn red-sunglo">Back</a>
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
