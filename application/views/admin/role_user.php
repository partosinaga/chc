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
							<i class="fa fa-user"></i>User Role
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
						<form action="<?php echo base_url('admin/role/role_user_submit.tpd');?>" method="post" id="form_sample_icon" class="form-horizontal">
							<input type="hidden" name="role_id" value="<?php echo $role_id;?>" />
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
								<div class="form-group">
									<label class="control-label col-md-3">Role Name</label>
									<div class="col-md-4">
										<p class="form-control-static">: <?php echo $row->role_name;?></p>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Description</label>
									<div class="col-md-4">
										<p class="form-control-static">: <?php echo $row->role_desc;?></p>
									</div>
								</div>
								<hr style="margin-top:0px;"/>
								<div class="form-group">
									<label class="control-label col-md-1"></label>
									<div class="col-md-11">
										<label>User</label>
										<div class="">
											<div class="icheck-list">
											<?php
												$qry_user = $this->db->query("SELECT ms_user.user_id, ms_user.user_fullname, (SELECT role_user_id FROM ms_role_user WHERE role_id = " . $role_id . " AND user_id = ms_user.user_id) AS is_user FROM ms_user WHERE status = " . STATUS_NEW . " ORDER BY user_fullname ");
												
												foreach($qry_user->result() as $row_user){
													$checked = '';
													
													if($row_user->is_user != null && $row_user->is_user > 0){
														$checked = 'checked="checked"';
													}
													
													echo '<div class="col-md-4"><label><input type="checkbox" class="icheck" name="user_role[' . $row_user->user_id . ']" ' . $checked . '> ' . $row_user->user_fullname . ' </label></div>';
													
												}
											?>
											</div>
										</div>
									</div>
								</div>
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
                },

                submitHandler: function (form) {
                    form[0].submit(); // submit the form
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
			$(this).parent().parent('.menu-child-div').children('.menu-action').show();
		}
		else {
			$(this).parent().parent('.menu-child-div').children('.menu-action').hide();
		}
	});
</script>