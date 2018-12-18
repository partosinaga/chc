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
                            <i class="fa fa-user"></i><?php echo ($role_id > 0 ? 'Edit' : 'New');?> Role
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('admin/role/role_manage/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form action="<?php echo base_url('admin/role/role_submit.tpd');?>" method="post" id="form_sample_icon" class="form-horizontal" autocomplete="off">
                            <input type="hidden" name="role_id" value="<?php echo $role_id;?>" />
                            <div class="form-actions top">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" name="save">Save</button>
                                        <button type="submit" class="btn blue-madison" name="save_close">Save & Close</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Role Name <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" class="form-control" name="role_name" value="<?php echo ($role_id > 0 ? $row->role_name : '');?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Description
                                    </label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" class="form-control" name="role_desc" value="<?php echo ($role_id > 0 ? $row->role_desc : '');?>"/>
                                        </div>
                                    </div>
                                </div>
                                <h4>Role Detail</h4>
                                <hr style="margin-top:0px;"/>
                                <?php
                                $qry_menu = $this->mdl_general->get('ms_menu', array('parent_id' => 0, 'status' => STATUS_NEW), array(), 'sorting');
                                $action = array(
                                    STATUS_NEW          => STATUS_NEW,
                                    STATUS_EDIT         => STATUS_EDIT,
                                    STATUS_PROCESS      => STATUS_PROCESS,
                                    STATUS_APPROVE      => STATUS_APPROVE,
                                    STATUS_DISAPPROVE   => STATUS_DISAPPROVE,
                                    STATUS_CANCEL       => STATUS_CANCEL,
                                    STATUS_POSTED       => STATUS_POSTED,
                                    STATUS_CLOSED       => STATUS_CLOSED,
                                    STATUS_DELETE       => STATUS_DELETE,
                                    STATUS_AUDIT        => STATUS_AUDIT,
                                    STATUS_REJECT       => STATUS_REJECT,
                                    STATUS_PRINT        => STATUS_PRINT,
                                    STATUS_UNLOCK       => STATUS_UNLOCK
                                );

                                foreach($qry_menu->result() as $row_menu){
                                    $checked = '';

                                    if($role_id > 0){
                                        if(get_role_detail($role_id, $row_menu->menu_id, STATUS_VIEW)){
                                            $checked = 'checked="checked"';
                                        }
                                    }

                                    echo '<div class="main-menu-div">
												<div class="row">
													<div class="input-group margin-left-20">
														<div class="icheck-inline">
															<label> <input type="checkbox" class="main-menu" name="menu_id[' . $row_menu->menu_id . '][' . STATUS_VIEW . ']" value="' . $row_menu->menu_id . '" ' . $checked . '> ' . $row_menu->menu_name . ' </label>
														</div>
													</div>
												</div>';

                                    $has_child = false;

                                    $qry_menu2 = $this->mdl_general->get('ms_menu', array('parent_id' => $row_menu->menu_id, 'status' => STATUS_NEW), array(), 'sorting');
                                    foreach($qry_menu2->result() as $row_menu2){
                                        $has_child = true;

                                        $checked2 = '';

                                        if($role_id > 0){
                                            if(get_role_detail($role_id, $row_menu2->menu_id, STATUS_VIEW)){
                                                $checked2 = 'checked="checked"';
                                            }
                                        }

                                        echo '<div class="bg-grey menu-child-div ' . ($role_id > 0 ? ((get_role_detail($role_id, $row_menu->menu_id, STATUS_VIEW)) ? '' : 'hide') : 'hide') . '">
													<div class="row">
														<div class="input-group margin-left-40">
															<div class="icheck-inline">
																<label> <input type="checkbox" class="menu-child" name="menu_id[' . $row_menu2->menu_id . '][' . STATUS_VIEW . ']" value="' . $row_menu2->menu_id . '" ' . $checked2 . '> ' . $row_menu2->menu_name . ' </label>
															</div>
														</div>
													</div>';

                                        $has_child3 = false;

                                        $qry_menu3 = $this->mdl_general->get('ms_menu', array('parent_id' => $row_menu2->menu_id, 'status' => STATUS_NEW), array(), 'sorting');
                                        foreach($qry_menu3->result() as $row_menu3){
                                            $has_child3 = true;

                                            $checked3 = '';

                                            if($role_id > 0){
                                                if(get_role_detail($role_id, $row_menu3->menu_id, STATUS_VIEW)){
                                                    $checked3 = 'checked="checked"';
                                                }
                                            }

                                            $arr_menu_act3 = array();
                                            $qry_menu_act3 = $this->db->get_where('ms_menu_action', array('menu_id' => $row_menu3->menu_id));
                                            foreach ($qry_menu_act3->result() as $row_menu_act3) {
                                                array_push($arr_menu_act3, $row_menu_act3->status_action);
                                            }

                                            echo '<div class="bg-grey-silver menu-child-div ' . ($role_id > 0 ? ((get_role_detail($role_id, $row_menu2->menu_id, STATUS_VIEW)) ? '' : 'hide') : 'hide') . '">
														<div class="row">
															<div class="input-group margin-left-60">
																<div class="icheck-inline">
																	<label> <input type="checkbox" class="menu-child" name="menu_id[' . $row_menu3->menu_id . '][' . STATUS_VIEW . ']" value="' . $row_menu3->menu_id . '" ' . $checked3 . '> ' . $row_menu3->menu_name . ' </label>
																</div>
															</div>
														</div>';

                                            if (count($arr_menu_act3) > 0) {
                                                echo '<div class="bg-grey-cascade menu-action ' . ($role_id > 0 ? ((get_role_detail($role_id, $row_menu3->menu_id, STATUS_VIEW)) ? '' : 'hide') : 'hide') . '">
														<div class="input-group margin-left-80">
															<div class="icheck-inline">';

                                                foreach ($arr_menu_act3 as $act_key => $act_val) {
                                                    echo '<label> <input type="checkbox" class="" name="menu_id[' . $row_menu3->menu_id . '][' . $act_val . ']" ' . ($role_id > 0 ? ((get_role_detail($role_id, $row_menu3->menu_id, $act_val)) ? 'checked="checked"' : '') : '') . '> ' . get_menu_action_label($row_menu3->menu_id, $act_val) . ' </label>';
                                                }

                                                echo '		</div>
														</div>
													</div>';
                                            }

                                            echo '</div>';
                                        }

                                        if($has_child3 == false){
                                            $arr_menu_act2 = array();
                                            $qry_menu_act2 = $this->db->get_where('ms_menu_action', array('menu_id' => $row_menu2->menu_id));
                                            foreach ($qry_menu_act2->result() as $row_menu_act2) {
                                                array_push($arr_menu_act2, $row_menu_act2->status_action);
                                            }

                                            if (count($arr_menu_act2) > 0) {
                                                echo '<div class="bg-grey-cascade menu-action ' . ($role_id > 0 ? ((get_role_detail($role_id, $row_menu2->menu_id, STATUS_VIEW)) ? '' : 'hide') : 'hide') . '">
														<div class="input-group margin-left-60">
															<div class="icheck-inline">';

                                                foreach ($arr_menu_act2 as $act_key => $act_val) {
                                                    echo '<label> <input type="checkbox" class="" name="menu_id[' . $row_menu2->menu_id . '][' . $act_val . ']" ' . ($role_id > 0 ? ((get_role_detail($role_id, $row_menu2->menu_id, $act_val)) ? 'checked="checked"' : '') : '') . '> ' . get_menu_action_label($row_menu2->menu_id, $act_val) . ' </label>';
                                                }

                                                echo '			</div>
														</div>
													</div>';
                                            }
                                        }

                                        echo '</div>';
                                    }

                                    if($has_child == false){
                                        $arr_menu_act1 = array();
                                        $qry_menu_act1 = $this->db->get_where('ms_menu_action', array('menu_id' => $row_menu->menu_id));
                                        foreach ($qry_menu_act1->result() as $row_menu_act1) {
                                            array_push($arr_menu_act1, $row_menu_act1->status_action);
                                        }

                                        if (count($arr_menu_act1) > 0) {
                                            echo '<div class="bg-grey-cascade menu-action ' . ($role_id > 0 ? ((get_role_detail($role_id, $row_menu->menu_id, STATUS_VIEW)) ? '' : 'hide') : 'hide') . '">
													<div class="input-group margin-left-40">
														<div class="icheck-inline">';

                                            foreach ($arr_menu_act1 as $act_key => $act_val) {
                                                echo '<label> <input type="checkbox" class="" name="menu_id[' . $row_menu->menu_id . '][' . $act_val . ']" ' . ($role_id > 0 ? ((get_role_detail($role_id, $row_menu->menu_id, $act_val)) ? 'checked="checked"' : '') : '') . '> ' . get_menu_action_label($row_menu->menu_id, $act_val) . ' </label>';
                                            }


                                            echo '			</div>
													</div>
												</div>';
                                        }
                                    }

                                    echo '</div>';
                                }
                                ?>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn green" name="save">Save</button>
                                        <button type="submit" class="btn blue-madison" name="save_close">Save & Close</button>
                                        <a href="<?php echo base_url('admin/role/role_manage/1.tpd');?>" class="btn red-sunglo">Back</a>
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
                    role_name: {
                        minlength: 2,
                        required: true
                    }
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
                }
            });
        }

        handleValidation();

        $('.main-menu').on('ifChecked', function(event){
            if($(this).parent().parent().parent().parent().parent().parent('.main-menu-div').children('.menu-child-div').length > 0){
                $(this).parent().parent().parent().parent().parent().parent('.main-menu-div').children('.menu-child-div').removeClass('hide');
            }
            else {
                $(this).parent().parent().parent().parent().parent().parent('.main-menu-div').children('.menu-action').removeClass('hide');
            }
        });

        $('.main-menu').on('ifUnchecked', function(event){
            if($(this).parent().parent().parent().parent().parent().parent('.main-menu-div').children('.menu-child-div').length > 0){
                $(this).parent().parent().parent().parent().parent().parent('.main-menu-div').children('.menu-child-div').addClass('hide');
            }
            else {
                $(this).parent().parent().parent().parent().parent().parent('.main-menu-div').children('.menu-action').addClass('hide');
            }
        });

        $('.menu-child').on('ifChecked', function(event){

            if($(this).parent().parent().parent().parent().parent().parent('.menu-child-div').children('.menu-child-div').length > 0){
                $(this).parent().parent().parent().parent().parent().parent('.menu-child-div').children('.menu-child-div').removeClass('hide');
            }
            else {
                $(this).parent().parent().parent().parent().parent().parent('.menu-child-div').children('.menu-action').removeClass('hide');
            }
        });

        $('.menu-child').on('ifUnchecked', function(event){

            if($(this).parent().parent().parent().parent().parent().parent('.menu-child-div').children('.menu-child-div').length > 0){
                $(this).parent().parent().parent().parent().parent().parent('.menu-child-div').children('.menu-child-div').addClass('hide');
            }
            else {
                $(this).parent().parent().parent().parent().parent().parent('.menu-child-div').children('.menu-action').addClass('hide');
            }
        });

    });

    $(document).on('click','.menu-child', function(){
        var checked = $(this).is(':checked');

        if(checked){
            if ($(this).closest('.menu-child-div').children('.menu-child-div').length > 0) {
                $(this).closest('.menu-child-div').children('.menu-child-div').removeClass('hide');
            } else {
                $(this).closest('.menu-child-div').children('.menu-action').removeClass('hide');
            }
        }
        else {
            if ($(this).closest('.menu-child-div').children('.menu-child-div').length > 0) {
                $(this).closest('.menu-child-div').children('.menu-child-div').addClass('hide');
            } else {
                $(this).closest('.menu-child-div').children('.menu-action').addClass('hide');
            }
        }
    });

    $(document).on('click','.main-menu', function(){
        var checked = $(this).is(':checked');

        if(checked){
            if ($(this).closest('.main-menu-div').children('.menu-child-div').length > 0) {
                $(this).closest('.main-menu-div').children('.menu-child-div').removeClass('hide');
            }
            else if ($(this).closest('.main-menu-div').children('.menu-action').length > 0) {
                $(this).closest('.main-menu-div').children('.menu-action').removeClass('hide');
            }
        }
        else {
            if ($(this).closest('.main-menu-div').children('.menu-child-div').length > 0) {
                $(this).closest('.main-menu-div').children('.menu-child-div').addClass('hide');
            }
            else if ($(this).closest('.main-menu-div').children('.menu-action').length > 0) {
                $(this).closest('.main-menu-div').children('.menu-action').addClass('hide');
            }
        }
    });
</script>