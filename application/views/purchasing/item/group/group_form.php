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
							<i class="fa fa-user"></i><?php echo ($group_id > 0 ? 'Edit' : 'New');?> Group
						</div>
						<div class="actions">
							<a href="<?php echo base_url('purchasing/item/group_manage/1.tpd');?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="<?php echo base_url('purchasing/item/group_submit.tpd');?>" method="post" id="form_sample_icon" class="form-horizontal">
							<input type="hidden" name="group_id" value="<?php echo $group_id;?>" />
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
								<div class="form-group">
									<label class="control-label col-md-3">Class <span class="required"> * </span>
									</label>
									<div class="col-md-4">
										<select name="class_id" class="form-control form-filter input-sm select2me">
											<option value=""> -- Select Class -- </option>
											<?php
												foreach($qry_class->result() as $row_class){
													echo '<option value="' . $row_class->class_id . '" ' . ($group_id > 0 ? ($row->class_id == $row_class->class_id ? 'selected="selected"' : '') : '') . '>' . $row_class->class_desc . '</option>';
												}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Department <span class="required"> * </span>
									</label>
									<div class="col-md-4">
										<select name="department_id" class="form-control form-filter input-sm select2me">
											<option value=""> -- Select Department -- </option>
											<?php
												foreach($qry_department->result() as $row_department){
													echo '<option value="' . $row_department->department_id . '" ' . ($group_id > 0 ? ($row->department_id == $row_department->department_id ? 'selected="selected"' : '') : '') . '>' . $row_department->department_desc . '</option>';
												}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Group Code <span class="required">
									* </span>
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="group_code" value="<?php echo ($group_id > 0 ? $row->group_code : '');?>"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Description
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="group_desc" value="<?php echo ($group_id > 0 ? $row->group_desc : '');?>"/>
										</div>
									</div>
								</div>
								<?php
								if($group_id > 0){
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
										<button type="submit" class="btn green" name="save">Save</button>
										<button type="submit" class="btn blue-madison" name="save_close">Save & Close</button>
                                        <a href="<?php echo base_url('purchasing/item/group_manage/1.tpd');?>" class="btn red-sunglo">Back</a>
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
					class_id: {
                        required: true
                    },
					department_id: {
                        required: true
                    },
                    class_code: {
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
	});
</script>