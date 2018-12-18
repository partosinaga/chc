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

                $form_mode = '';
                $var_title = '';
                $budgetType = 0;

                if($budget_id > 0){
                    if($row->status != STATUS_NEW){
                        $form_mode = 'disabled';
                    }

                    $budgetType = $row->budget_type;
                    if($row->budget_type == 1){
                        $var_title = 'Constant Amount';
                    }else if($row->budget_type == 3){
                        $var_title = 'Increase Amount';
                    }else if($row->budget_type == 4){
                        $var_title = 'Decrease Amount';
                    }
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
							<i class="fa fa-user"></i><?php echo ($budget_id > 0 ? 'Edit' : 'New');?> Budget Estimation
						</div>
						<div class="actions">
                            <a href="<?php echo base_url('finance/budget/budget_manage' . (isset($budget_year) ? '/' . $budget_year : '') . '.tpd') ; ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="<?php echo base_url('finance/budget/submit_budget.tpd');?>" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="budget_id" name="budget_id" value="<?php echo $budget_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="submit" class="btn green" name="save">Save</button>
										<button type="submit" class="btn blue-madison" name="save_close" >Save & Close</button>

                                    </div>
								</div>

							</div>
                            <?php
                            }
                            ?>
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
								<div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Year</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" maxlength="4" name="budget_year" readonly value="<?php echo $budget_id > 0 ? $row->budget_year : (isset($budget_year) ? $budget_year : date("Y")) ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">GL Code</label>
                                            <div class="col-md-8">
                                                <select name="coa_code" class="form-control select2me" >
                                                    <option value="">Select...</option>
                                                    <?php
                                                    $qry_coa = $this->db->query("SELECT * FROM gl_coa WHERE is_display > 0 AND status NOT IN(" . STATUS_DELETE . "," . STATUS_INACTIVE . ") AND coa_code NOT LIKE '1%' AND coa_code NOT LIKE '2%' AND coa_code NOT LIKE '3%' AND coa_code NOT LIKE '7%' ORDER BY coa_code;");
                                                    if($qry_coa->num_rows() > 0){
                                                        foreach($qry_coa->result() as $row_coa){
                                                            echo '<option value="' . $row_coa->coa_code . '" ' . ($budget_id > 0 ? ($row_coa->coa_code == $row->coa_code ? 'selected="selected"' : '') : '') . '>' . $row_coa->coa_code . ' - ' . $row_coa->coa_desc . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Type</label>
                                            <div class="col-md-6">
                                                <select name="budget_type" class="form-control select2me" id="budget_type">
                                                    <option value="1" <?php echo ($budget_id > 0 ? ($row->budget_type == 1 ? 'selected="selected"' : '') : '')?> >CONSTANT</option>
                                                    <option value="2" <?php echo ($budget_id > 0 ? ($row->budget_type == 2 ? 'selected="selected"' : '') : '')?> >MANUAL</option>
                                                    <option value="3" <?php echo ($budget_id > 0 ? ($row->budget_type == 3 ? 'selected="selected"' : '') : '')?> >INCREMENT</option>
                                                    <option value="4" <?php echo ($budget_id > 0 ? ($row->budget_type == 4 ? 'selected="selected"' : '') : '')?> >DECREMENT</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- div class="form-group">
                                            <label class="control-label col-md-3">Department</label>
                                            <div class="col-md-8">
                                                <select name="dept_id" class="form-control select2me" >
                                                    <option value="0">Select...</option>
                                                    <?php
                                                    if(isset($dept_list)){
                                                        if(count($dept_list) > 0){
                                                            foreach($dept_list as $dept){
                                                                echo '<option value="' . $dept['department_id'] . '" ' . ($budget_id > 0 ? ($dept['department_id'] == $row->department_id ? 'selected="selected"' : '') : '') . '>' . $dept['department_name'] . ' - ' . $dept['department_desc'] . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div -->
                                    </div>
                                    <div class="col-md-4">

                                        <div id="type_constant" class="portlet box blue-hoki <?php echo $budgetType == 2 ? 'hide' : ''; ?>">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-cogs"></i>
                                                    <span id="var_title"></span>
                                                </div>
                                                <div class="actions">

                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-5 label-sm text-right"><span id="var_amount_title"><?php echo ($budget_id > 0 ? $var_title : 'Constant Amount') ?></span></label>
                                                            <div class="col-md-7">
                                                                <input type="text" value="<?php echo ($budget_id > 0 ? $row->budget_variableamount : '0')?>" class="form-control text-right input-sm mask_number mask_currency" id="var_value" name="budget_variableamount">
                                                            </div>
                                                        </div>
                                                        <div class="form-group <?php echo ($budget_id > 0 ? ($row->budget_type = 3 || $row->budget_type = 4 ? '':'hide')  : 'hide')?>" id="div_start_amount">
                                                            <label class="control-label col-md-5 label-sm text-right">Start Amount</label>
                                                            <div class="col-md-7">
                                                                <input type="text" value="<?php echo (isset($qry_det) ? $qry_det[0]['budget_amount'] : 0)?>" class="form-control text-right input-sm mask_number mask_currency" id="var_start_value">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="portlet-title">
                                                <div class="actions">
                                                    <a class="btn btn-sm purple-intense" href="javascript:;" id="btn_generate">
                                                        <i class="fa fa-plus"></i>
                                                        Generate
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								</div>

								<div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-5">
										<table class="table table-striped table-hover table-bordered" id="budget_detail">
											<thead>
												<tr>
													<th class="text-center" style="width: 20%;"> Month </th>
													<th class="text-center" style="width: 30%;"> Budget Amount </th>
                                                    <th class="text-center" style="width: 20%;"> Month </th>
                                                    <th class="text-center" style="width: 30%;"> Budget Amount </th>
												</tr>
											</thead>
											<tbody>
                                            <?php for($i=0;$i<6;$i++){
                                                $nMonth = $i+1;
                                                $nMonth2 = $i+7;

                                            ?>
                                            <tr>
                                                <td class="text-center" style="padding-top: 12px;"><span class="control-label "><?php echo $nMonth; ?></span></td>
                                                <td><input type="text" name="budget_amount[<?php echo $i; ?>]" data-period = "<?php echo $nMonth; ?>" class="form-control text-right input-sm mask_number mask_currency"  value="<?php echo (isset($qry_det) ? $qry_det[$nMonth-1]['budget_amount'] : 0) ?>" readonly></td>
                                                <td class="text-center" style="padding-top: 12px;"><span class="control-label "><?php echo $nMonth2; ?></span></td>
                                                <td><input type="text" name="budget_amount[<?php echo $i+6; ?>]" data-period = "<?php echo $nMonth2; ?>" class="form-control text-right input-sm mask_number mask_currency" value="<?php echo (isset($qry_det) ? $qry_det[$nMonth2-1]['budget_amount'] : 0) ?>" readonly></td>
                                            </tr>
                                            <?php }?>
											</tbody>

										</table>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php
                                        if($budget_id > 0){
                                            $created = '';
                                            $modified = '';

                                            if ($row->created_by > 0) {
                                                $created .= "<div class='col-md-8'><h6>Created by " . get_user_fullname( $row->created_by) . " (" . date_format(new DateTime($row->created_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                                            }

                                            if ($row->modified_by > 0) {
                                                $modified .= "<div class='col-md-4'><h6>Last Modified by " . get_user_fullname( $row->modified_by) . " (" . date_format(new DateTime($row->modified_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                                            }
                                            echo '<div class="note note-info" style="margin:10px;">
                                                    ' . $created . '
                                                    ' . $modified . '
                                                    <div style="clear:both;"></div>
                                                </div>';
                                        }
                                        ?>
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
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;
    var rowIndex = <?php echo (isset($rowIndex) ? $rowIndex : 0) ; ?>;

    if(isedit > 0){
        $('#form-entry').block({
            message: null ,
            overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
        });
    }

    $(document).ready(function(){
        $(".mask_number").inputmask({
            "mask": "9",
            "repeat": 10,
            "greedy": false
        });

        $(".mask_currency").inputmask("decimal",{
            radixPoint:".",
            groupSeparator: ",",
            digits: 0,
            autoGroup: true,
            autoUnmask: true
        });

        var handleSpinner = function(){
            //$('#spinner1').spinner({value:<?php echo isset($budget_year) ? $budget_year : '2015' ?>, min: 2011, max: 2020, disabled: true});
        }

        //handleSpinner();

        var handleValidation = function() {
            // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

            toastr.options = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-bottom-right",
                "onclick": null,
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": "3000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }

            var form1 = $('#form-entry');

            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    budget_year: {
                        required: true,
                        digits:true
                    },
                    coa_code: {
                        required: true
                    },
                    budget_type: {
                        required: true
                    }
                },
                messages: {
                    budget_year: "Period must not empty",
                    coa_code: "GL Code must not empty",
                    budget_type: "Type must selected"
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.budget_year != null){
                        toastr["error"](validator.invalid.budget_year, "Warning");
                    }
                    if(validator.invalid.coa_code != null){
                        toastr["error"](validator.invalid.coa_code, "Warning");
                    }
                    if(validator.invalid.budget_type != null){
                        toastr["error"](validator.invalid.budget_type, "Warning");
                    }

                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});

                    //console.log('text err ' + error.text());
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group
                },
                success: function (label, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    icon.removeClass("fa-warning").addClass("fa-check");
                }
            });
        }

        //initiate validation
        handleValidation();

	});

    $('#budget_type').on('change', function(){
        var type = parseInt($(this).val());

        if(type == 1){
            $('#type_constant').removeClass('hide');
            $('#var_amount_title').html('Constant Amount');
            $('#div_start_amount').addClass('hide');
            $('#var_title').html('Constant');
            $('#budget_detail > tbody > tr ').each(function() {
                $(this).find('input[name*="budget_amount"]').attr('readonly',true);
                $(this).find('input[name*="budget_amount"]').val('0');
            });
        }else if(type == 3){
            $('#type_constant').removeClass('hide');
            $('#div_start_amount').removeClass('hide');
            $('#var_amount_title').html('Increase Amount');
            $('#var_title').html('Increase');
            $('#budget_detail > tbody > tr ').each(function() {
                $(this).find('input[name*="budget_amount"]').attr('readonly',true);
                $(this).find('input[name*="budget_amount"]').val('0');
            });
        }else if(type == 4){
            $('#type_constant').removeClass('hide');
            $('#div_start_amount').removeClass('hide');
            $('#var_amount_title').html('Decrease Amount');
            $('#var_title').html('Decrease');
            $('#budget_detail > tbody > tr ').each(function() {
                $(this).find('input[name*="budget_amount"]').attr('readonly',true);
                $(this).find('input[name*="budget_amount"]').val('0');
            });
        }else{
            $('#type_constant').addClass('hide');

            $('#budget_detail > tbody > tr ').each(function() {
                $(this).find('input[name*="budget_amount"]').attr('readonly',false);
            });
        }

    });

    $('#btn_generate').on('click', function(){
        var type = parseInt($('#budget_type').val());
        var amount = parseFloat($('#var_value').val());
        var startAmount = parseFloat($('#var_start_value').val());

        //console.log('generating ... ' + type + ' , ' + amount);

        if(type == 1){
            $('#budget_detail > tbody > tr ').each(function() {
                $(this).find('input[name*="budget_amount"]').val(amount);
            });
        }else if(type == 3){
            var month = 1;
            $('#budget_detail > tbody > tr ').each(function() {
                //$(this).find('input[name*="budget_amount"]').val(startAmount);
                $(this).find('input[data-period="' + month + '"]').val(startAmount);
                $(this).find('input[data-period="' + (month+6) + '"]').val(startAmount + (6*amount));
                month++;

                startAmount += amount;
            });
        }else if(type == 4){
            var month = 1;
            $('#budget_detail > tbody > tr ').each(function() {
                //$(this).find('input[name*="budget_amount"]').val(startAmount);
                $(this).find('input[data-period="' + month + '"]').val(startAmount);
                $(this).find('input[data-period="' + (month+6) + '"]').val(startAmount - (6*amount));
                month++;

                startAmount -= amount;
            });
        }
    });

</script>