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
                    echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
                ?>
                <!-- Begin: life time stats -->
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Menu <?php echo ($menu_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('admin/menu/menu_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('admin/menu/submit_menu.tpd');?>" class="form-horizontal" id="validate-form" method="post" autocomplete="off">
                            <div class="form-body">
                                <input type="hidden" id="menu_id" name="menu_id" value="<?php echo ($menu_id > 0 ? $row->menu_id : '0');?>"/>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Parent Menu
                                    </label>
                                    <div class="col-md-6">
                                        <select name="parent_id" class="form-control select2me">
                                            <option value="0" <?php ($menu_id > 0 ? ($row->parent_id == 0 ? 'selected="selected"' : '') : '') ?>> TOP </option>
                                            <?php
                                            $qry_menu = $this->mdl_general->get('ms_menu', array('status <>' => STATUS_DELETE, 'parent_id' => 0), array(), 'sorting');
                                            foreach($qry_menu->result() as $row_menu){
                                                echo '<option value="' . $row_menu->menu_id . '" ' . ($menu_id > 0 ? ($row->parent_id == $row_menu->menu_id ? 'selected="selected"' : '') : '') . '>TOP > ' . $row_menu->menu_name . '</option>';
												
												$qry_menu2 = $this->mdl_general->get('ms_menu', array('status <>' => STATUS_DELETE, 'parent_id' => $row_menu->menu_id), array(), 'sorting');
												foreach($qry_menu2->result() as $row_menu2){
													echo '<option value="' . $row_menu2->menu_id . '" ' . ($menu_id > 0 ? ($row->parent_id == $row_menu2->menu_id ? 'selected="selected"' : '') : '') . '>TOP > ' . $row_menu->menu_name . ' > ' . $row_menu2->menu_name . '</option>';
													
													$qry_menu3 = $this->mdl_general->get('ms_menu', array('status <>' => STATUS_DELETE, 'parent_id' => $row_menu2->menu_id), array(), 'sorting');
													foreach($qry_menu3->result() as $row_menu3){
														echo '<option value="' . $row_menu3->menu_id . '" ' . ($menu_id > 0 ? ($row->parent_id == $row_menu3->menu_id ? 'selected="selected"' : '') : '') . ' disabled>TOP > ' . $row_menu->menu_name . ' > ' . $row_menu2->menu_name . ' > ' . $row_menu3->menu_name . '</option>';
													}
												}
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="module_name">Module <span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Module" type="text" class="form-control" name="module_name" value="<?php echo ($menu_id > 0 ? $row->module_name : '');?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="controller_name">Controller</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Controller" type="text" class="form-control" name="controller_name" value="<?php echo ($menu_id > 0 ? $row->controller_name : '');?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="function_name">Function</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Function" type="text" class="form-control" name="function_name" value="<?php echo ($menu_id > 0 ? $row->function_name : '');?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="function_parameter">Parameter</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Function Parameter" type="text" class="form-control" name="function_parameter" value="<?php echo ($menu_id > 0 ? $row->function_parameter : '');?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="menu_name">Menu <span class="required">
									* </span></label></label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Menu name" type="text" class="form-control" name="menu_name" value="<?php echo ($menu_id > 0 ? $row->menu_name : '');?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="menu_desc">Description</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Description" type="text" class="form-control" name="menu_desc" value="<?php echo ($menu_id > 0 ? $row->menu_description : '');?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="menu_icon">Class Icon</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="Class for icon" type="text" class="form-control" name="menu_icon" value="<?php echo ($menu_id > 0 ? $row->menu_icon : '');?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="sorting">Sort</label>
                                    <div class="col-md-1">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input placeholder="" type="text" class="form-control" name="sorting" value="<?php echo ($menu_id > 0 ? $row->sorting : '0');?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="sorting">New Tab</label>
                                    <div class="col-md-1" style="padding-top: 8px;">
                                        <div class="checkbox-list">
                                            <label>
                                                <input type="checkbox" name="is_new_tab" value="1" <?php echo ($menu_id > 0 ? ($row->is_new_tab == 1 ? 'checked="checked"' : '') : '');?> /> &nbsp;
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="sorting">Action</label>
                                    <div class="col-md-5" style="padding-top: 8px;">
                                        <div class="input-group input-medium">
                                            <?php
                                            foreach ($action as $act_key => $act_val) {
                                                $checked = '';
                                                $action_name = get_action_name($act_val, false);
                                                if ($menu_id > 0) {
                                                    if (array_key_exists($act_val, $menu_act)) {
                                                        $checked = 'checked="checked"';

                                                        $act_name = $menu_act[$act_val];
                                                        if ($act_name != '') {
                                                            $action_name = $act_name;
                                                        }
                                                    }
                                                }
                                                echo '<div class="input-group input-medium margin-bottom-5">
											            <span class="input-group-addon">
											                <input type="checkbox" name="menu_action[' . $act_val . ']" value="' . $act_val . '" ' . $checked . ' />
                                                        </span>
                                                        <input type="text" name="status_label[' . $act_val . ']" class="form-control" value="' . $action_name . '"/>
                                                    </div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if($menu_id > 0){
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
                                        <button type="submit" class="btn green">Submit</button>
                                        <button type="submit" class="btn blue-madison" name="save_close">Save & Close</button>
                                        <a href="<?php echo base_url('admin/menu/menu_manage/1.tpd');?>" class="btn default">Back</a>
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
                    module_name: {
                        minlength: 2,
                        required: true
                    },
                    menu_name: {
                        minlength: 2,
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