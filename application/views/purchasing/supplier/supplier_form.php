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
							<i class="fa fa-user"></i><?php echo ($supplier_id > 0 ? 'Edit' : 'New');?> Supplier
						</div>
						<div class="actions">
							<a href="<?php echo base_url('purchasing/supplier/supplier_manage/1.tpd');?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="<?php echo base_url('purchasing/supplier/supplier_submit.tpd');?>" method="post" id="form_sample_icon" class="form-horizontal">
							<input type="hidden" name="supplier_id" value="<?php echo $supplier_id;?>" />
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
								<div class="form-group">
									<label class="control-label col-md-3">Supplier Name <span class="required">
									* </span>
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="supplier_name" value="<?php echo ($supplier_id > 0 ? $row->supplier_name : '');?>"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Supplier Address
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<textarea class="form-control" rows="3" name="supplier_address"><?php echo ($supplier_id > 0 ? $row->supplier_address : '');?></textarea>		 											
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Post Code
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="supplier_postcode" value="<?php echo ($supplier_id > 0 ? $row->supplier_postcode : '');?>"/>
										</div>
									</div>
								</div>
								
								<div class="form-group">
									<label class="control-label col-md-3">District
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="supplier_distric" value="<?php echo ($supplier_id > 0 ? $row->supplier_distric : '');?>"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">City
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="supplier_city" value="<?php echo ($supplier_id > 0 ? $row->supplier_city : '');?>"/>
										</div>
									</div>
								</div> 
								<div class="form-group">
                                            <label class="control-label col-md-3">Country<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-4">                                                
												<select name="supplier_country" class="form-control form-filter input-sm select2me "  >
												<option value=""> -- Select -- </option>
												<?php  if ($supplier_id > 0)
														{
															$selected_country= $row->supplier_country;
														}
														else
														{
															$selected_country = 77;
														}
                                                        if(count($country_list) > 0){
                                                            foreach($country_list as $country){
                                                                echo  '<option value="' . $country['master_country_id'] . '" ' . ($selected_country > 0 ? ($selected_country == $country['master_country_id'] ? 'selected="selected"' : '') : '') . '>' . $country['country_name'] . '</option>';
                                                            }
                                                        }
												?>
												</select>
                                            </div>
                                        </div>
								<div class="form-group">
									<label class="control-label col-md-3">Telp
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="supplier_telephone" value="<?php echo ($supplier_id > 0 ? $row->supplier_telephone : '');?>"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Fax
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="supplier_fax" value="<?php echo ($supplier_id > 0 ? $row->supplier_fax : '');?>"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Term of Payment (days)
									</label>
									<div class="col-md-2">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control text-right input-sm  num_qty" name="supplier_term_payment" value="<?php echo ($supplier_id > 0 ? $row->supplier_term_payment	 : '');?>"/>											
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Bank Name
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="bank_name" value="<?php echo ($supplier_id > 0 ? $row->bank_name	 : '');?>"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Account Bank Name
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="account_bank_name" value="<?php echo ($supplier_id > 0 ? $row->account_bank_name	 : '');?>"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Account Bank No
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="account_bank_no" value="<?php echo ($supplier_id > 0 ? $row->account_bank_no	 : '');?>"/>
										</div>
									</div>
								</div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Contact Name</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" class="form-control" name="contact_name" value="<?php echo ($supplier_id > 0 ? $row->contact_name	 : '');?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Contact Phone</label>
                                    <div class="col-md-4">
                                        <div class="input-icon right">
                                            <i class="fa"></i>
                                            <input type="text" class="form-control" name="contact_phone" value="<?php echo ($supplier_id > 0 ? $row->contact_phone	 : '');?>"/>
                                        </div>
                                    </div>
                                </div>
								<div class="form-group">
									<label class="control-label col-md-3">Provide Item
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<textarea class="form-control" rows="3" name="provide_item"><?php echo ($supplier_id > 0 ? $row->provide_item : '');?></textarea>		 
										</div>
									</div>
								</div>
								
								<?php
								if($supplier_id > 0){
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
                                        <a href="<?php echo base_url('purchasing/supplier/supplier_manage/1.tpd');?>" class="btn red-sunglo">Back</a>
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
		 
		var handleMask = function() {
            $(".num_qty").inputmask("numeric",{
                radixPoint:".",
                autoGroup: true,
                groupSeparator: ",",
                digits: 2,
                groupSize: 3,
                removeMaskOnSubmit: true,
                autoUnmask: true
            });
 
        }

        handleMask();

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
                    supplier_name: {
                        minlength: 2,
                        required: true
                    },
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