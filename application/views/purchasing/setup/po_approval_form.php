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
							<i class="fa fa-user"></i><?php echo ($approval_id > 0 ? 'Edit' : 'New');?> Approval
						</div>
						<div class="actions">
							<a href="<?php echo base_url('purchasing/setup/po_approval_manage/1.tpd');?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="<?php echo base_url('purchasing/setup/po_approval_submit.tpd');?>" method="post" id="form_sample_icon" class="form-horizontal">
							<input type="hidden" name="approval_id" value="<?php echo $approval_id;?>" />
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
								<div class="form-group">
									<label class="control-label col-md-3">Approval Name <span class="required"> * </span>
									</label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="approval_name" value="<?php echo ($approval_id > 0 ? $row->approval_name : '');?>"/>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Position </label>
									<div class="col-md-4">
										<div class="input-icon right">
											<i class="fa"></i>
											<input type="text" class="form-control" name="position" value="<?php echo ($approval_id > 0 ? $row->position : '');?>"/>
										</div>
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
                                            <input type="text" class="form-control mask_currency" name="max_amount" value="<?php echo ($approval_id > 0 ? $row->max_amount : '');?>"/>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin-top:0px;"/>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Users</label>
                                    <div class="col-md-9">
                                        <a href="javascript:;" class="btn btn-xs default inverse add_user" ><i class="fa fa-user"></i>&nbsp;<i class="fa fa-plus"></i></a>
                                        <div>
                                            <input id="approval_users" name="approval_users" type="text" class="form-control tags medium" value="<?php echo isset($approval_users) ? implode(',', $approval_users) : '' ?>"/>
                                        </div>

                                    </div>
                                </div>
							</div>
							<div class="form-actions">
								<div class="row">
									<div class="col-md-offset-3 col-md-9">
										<button type="submit" class="btn green" name="save">Save</button>
										<button type="submit" class="btn blue-madison" name="save_close">Save & Close</button>
                                        <a href="<?php echo base_url('purchasing/setup/po_approval_manage/1.tpd');?>" class="btn red-sunglo">Back</a>
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

<div id="modal-user" class="modal fade" data-width="310" data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Users</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <select multiple="multiple" class="multi-select" id="multiselect_user" name="multiproject_user[]">
                        <?php
                        $qry = $this->db->query('SELECT * FROM ms_user WHERE status <> ' . STATUS_DELETE . ' ORDER BY user_fullname');
                        foreach($qry->result_array() as $user){
                            $selected = '';
                            if(in_array($user['user_name'], $approval_users)){
                                $selected = 'selected';
                            }
                            echo '<option value="' . $user['user_name'] . '" ' . $selected . '>' . $user['user_fullname'] . '</option>';
                        }
                        ?>

                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue" id="add-user-to-tag">Ok</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>
	$(document).ready(function(){
        $(".mask_currency").inputmask("decimal",{
            radixPoint:".",
            groupSeparator: ",",
            digits: 0,
            autoGroup: true,
            autoUnmask: true
        });

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
                    approval_name: {
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

        var handleTagsInput = function () {
            if (!jQuery().tagsInput) {
                return;
            }

            $('#approval_users').tagsInput({
                'height':'400px',
                'width':'auto',
                'defaultText':'',
                'placeholderColor' : '#666666',
                'onAddTag': function () {
                    //console.log($(this).val());
                    //console.log($('#project_users').val());
                },
                'onRemoveTag': function () {
                    //console.log('Remove ' + $(this).val());
                    $('#multiselect_user').multiSelect('deselect_all');

                    var arr = $(this).val().split(',');
                    $('#multiselect_user').multiSelect('select', arr);
                }
            });
        }

        handleTagsInput();

        $('.add_user').live('click', function (e) {
            e.preventDefault();

            var $modal_cal = $('#modal-user');

            if ($modal_cal.hasClass('bootbox') == false) {
                $modal_cal.addClass('modal-fix');
            }

            $('#modal-user').modal();
        });

        var handleMultiSelect = function () {
            $('#multiselect_user').multiSelect({
                selectableHeader: "<div class='control-label'><i class='fa fa-users'></i>&nbsp;<span style='font-weight:600;'>Available users</span></div>",
                selectionHeader: "<div class='control-label'><i class='fa fa-key'></i>&nbsp;<span style='font-weight:600;'>Project users</span></div>",
                selectableFooter: "<div class='control-label'><i class='fa fa-users'></i>&nbsp;<span style='font-weight:600;'>Available users</span></div>",
                selectionFooter: "<div class='control-label'><i class='fa fa-key'></i>&nbsp;<span style='font-weight:600;'>Project users</span></div>",
                dblClick:false
            });
        }
        handleMultiSelect();
	});

    $('#add-user-to-tag').click(function(e) {
        e.preventDefault();

        if($('#multiselect_user').val() != null){
            var selectedUsers = $('#multiselect_user').val();
            $('#approval_users').val(selectedUsers);
            //$('#project_users').addTag('add');
            //console.log('user .. ' + selectedUsers);
            $('#approval_users').importTags(selectedUsers.toString());
        }

        $('#modal-user').modal('hide');
    });

</script>