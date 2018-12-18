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
							<i class="fa fa-user"></i><?php echo ($item_id > 0 ? 'Edit' : 'New');?> Item Material
						</div>
						<div class="actions">
							<a href="<?php echo base_url('purchasing/item/material_manage/1.tpd');?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="<?php echo base_url('purchasing/item/material_submit.tpd');?>" method="post" id="form_sample_icon" class="form-horizontal">
							<input type="hidden" name="item_id" value="<?php echo $item_id;?>" />
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_item'));?>
								 
								<div class="form-group">
									<label class="control-label col-md-3">Item Material Code <span class="required">
									* </span>
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" readonly name="item_code" value="<?php echo ($item_id > 0 ? $row->item_code : '');?>"/>
										</div>
									</div>
								</div> 

								<div class="form-group">
                                    <label class="control-label col-md-3" for="coa_id">Item material Group<span class="required">
									* </span></label>
                                    <div class="col-md-4">
                                        <select name="group_id" class="form-control select2me"  >
                                            <option value="">Select...</option>
                                            <?php
                                            $qry_group = $this->db->query('SELECT group_id,group_code,group_desc FROM in_ms_item_group WHERE    status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY group_code;');
                                            if($qry_group->num_rows() > 0){
                                                foreach($qry_group->result() as $row_group){
                                                    echo '<option value="' . $row_group->group_id . '" ' . ($item_id > 0 ? ($row_group->group_id == $row->group_id ? 'selected="selected"' : '') : '') . '>' . $row_group->group_code . ' - ' . $row_group->group_desc. '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group">
									<label class="control-label col-md-3">Item Material Description
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="item_desc" value="<?php echo ($item_id > 0 ? $row->item_desc : '');?>"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Remarks
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<textarea class="form-control" rows="5" name="remarks"><?php echo ($item_id > 0 ? $row->remarks : '');?></textarea>											
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">GL Account Code Supply
									</label>
									<div class="col-md-5">
											<select name="account_coa_id" class="form-control select2me"  >
                                            <option value="">Select...</option>
                                            <?php
                                            $qry_coa = $this->db->query('SELECT * FROM gl_coa WHERE is_display > 0 AND status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY coa_code;');
                                            if($qry_coa->num_rows() > 0){
                                                foreach($qry_coa->result() as $row_coa){
                                                    echo '<option value="' . $row_coa->coa_id . '" ' . ($item_id > 0 ? ($row_coa-> coa_id == $row->account_coa_id ? 'selected="selected"' : '') : '') . '>' . $row_coa->coa_code . ' - ' . $row_coa->coa_desc . '</option>';
                                                }
                                            }
                                            ?>
                                        </select> 
									</div>
								</div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">GL Account Code Issue
                                    </label>
                                    <div class="col-md-5">
                                        <select name="exp_coa_id" class="form-control select2me"  >
                                            <option value="">Select...</option>
                                            <?php
                                            $qry_coa = $this->db->query('SELECT * FROM gl_coa WHERE is_display > 0 AND status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY coa_code;');
                                            if($qry_coa->num_rows() > 0){
                                                foreach($qry_coa->result() as $row_coa){
                                                    echo '<option value="' . $row_coa->coa_id . '" ' . ($item_id > 0 ? ($row_coa-> coa_id == $row->exp_coa_id ? 'selected="selected"' : '') : '') . '>' . $row_coa->coa_code . ' - ' . $row_coa->coa_desc . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group">
									<label class="control-label col-md-3">UOM Supply
									</label>
									<div class="col-md-2">
                                        <select name="uom_id" class="form-control select2me"  >
                                            <option value="">Select...</option>
                                            <?php
                                            $qry_uom = $this->db->query('SELECT uom_id,uom_code,uom_desc FROM in_ms_uom WHERE    status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY uom_code;');
                                            if($qry_uom->num_rows() > 0){
                                                foreach($qry_uom->result() as $row_uom){
                                                    echo '<option value="' . $row_uom->uom_id . '" ' . ($item_id > 0 ? ($row_uom->uom_id == $row->uom_id ? 'selected="selected"' : '') : '') . '>' . $row_uom->uom_code . ' - ' . $row_uom->uom_desc. '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
									<label class="control-label col-md-1">Factor
									</label>
									<div class="col-md-2">
										<div class="input-icon right"> 
											<input type="text" class="form-control" name="qty_distribution" value="<?php echo ($item_id > 0 ? number_format($row->qty_distribution, 0 )   : '');?>"/>
										</div>
									</div> 
									<label class="control-label col-md-1">UOM Issue
									</label>
									<div class="col-md-2"> 
											<select name="uom_id_distribution" class="form-control select2me"  >
                                            <option value="">Select...</option>
                                            <?php
                                            $qry_uom = $this->db->query('SELECT uom_id,uom_code FROM in_ms_uom WHERE    status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY uom_code;');
                                            if($qry_uom->num_rows() > 0){
                                                foreach($qry_uom->result() as $row_uom){
                                                    echo '<option value="' . $row_uom->uom_id . '" ' . ($item_id > 0 ? ($row_uom->uom_id == $row->uom_id_distribution ? 'selected="selected"' : '') : '') . '>' . $row_uom->uom_code . '</option>';
                                                }
                                            }
                                            ?>
                                        </select> 
									</div>
								</div> 

								<div class="form-group">
									<label class="control-label col-md-3">Min Stock
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="min_stock" value="<?php echo ($item_id > 0 ? $row->min_stock   : '');?>"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Max Stock
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="max_stock" value="<?php echo ($item_id > 0 ? $row->max_stock : '');?>"/>
										</div>
									</div>
								</div>								
								<?php
								if($item_id > 0){
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
                                        <a href="<?php echo base_url('purchasing/item/material_manage/1.tpd');?>" class="btn red-sunglo">Back</a>
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
                    form.submit(); // submit the form
                }
            });
		}
		
		handleValidation();
	});
</script>