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
                $back_url = base_url('housekeeping/srf/srf_manage.tpd');
                if($srf_id > 0){
                    if($srf->status != STATUS_NEW && $srf->status != STATUS_APPROVE){
                        $form_mode = 'disabled';
                    }

                    if($srf->status == STATUS_CLOSED || $srf->status == STATUS_POSTED){
                        $back_url = base_url('housekeeping/srf/srf_history.tpd');
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
							<i class="fa fa-user"></i><?php echo ($srf_id > 0 ? '' : 'New');?> Service Request Form (SRF) &nbsp; <?php echo $srf_id > 0 ? get_status_name($srf->status) : ''; ?>
						</div>
						<div class="actions">
                            <?php
                            if($srf_id > 0){
                                if($srf->status == STATUS_APPROVE ){
                            ?>
                                    <a href="<?php echo base_url('housekeeping/srf/pdf_srf/'. ($srf_id > 0 ? $srf->srf_id : '0') .'.tpd');?>" class="btn btn-circle default purple-studio" target="_blank"><i class="fa fa-print"></i> SRF</a>

                            <?php
                                }
                            }
                            ?>
                            <a href="<?php echo (isset($srf) ? $back_url : base_url('housekeeping/srf/srf_manage.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="javascript:;" id="form-entry" class="form-horizontal" method="post" autocomplete="off">
							<input type="hidden" id="srf_id" name="srf_id" value="<?php echo $srf_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
                                        <?php
                                        if($srf_id > 0){
                                            if($srf->status == STATUS_NEW ){
                                                echo '<button type="button" class="btn btn-circle green" name="save" id="btn_save">Save</button>';
                                                if(check_session_action(get_menu_id(), STATUS_APPROVE)) {
                                        ?>
                                                <button type="button" class="btn btn-circle blue" id="approve-button" data-id="<?php echo $srf_id; ?>" ><i class="fa fa-save"></i>&nbsp;Approve</button>
                                        <?php
                                                }
                                            }else if($srf->status == STATUS_APPROVE){
                                        ?>
                                                <button type="button" class="btn btn-circle purple-studio" id="close-button" data-id="<?php echo $srf_id; ?>" ><i class="fa fa-save"></i>&nbsp;Close</button>
                                        <?php
                                            }
                                        } else {
                                            echo '<button type="button" class="btn btn-circle green" name="save" id="btn_save">Save</button>';
                                        }
                                        ?>
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
                                            <label class="control-label col-md-3">Date</label>
                                            <div class="col-md-4" >
                                                <div class="input-group date " data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="srf_date" value="<?php echo ($srf_id > 0 ? dmy_from_db($srf->srf_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn hide">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
										 <div class="form-group">
                                            <label class="control-label col-md-3">Type</label>
                                            <div class="col-md-6">
                                                <select name="srf_type" id="srf_type" class="select2me form-control">
                                                    <option value="" <?php echo $srf_id <= 0 ? 'selected="selected"' : '' ?>>-- Select --</option>													
													<option value="<?php echo SRF_TYPE::MINOR_OUT_OF_ORDER ?>" <?php echo $srf_id > 0 ? $srf->srf_type == SRF_TYPE::MINOR_OUT_OF_ORDER ? 'selected="selected"' : '' : '' ?> ><?php echo HSK_STATUS::MO . ' - '  . HSK_STATUS::caption(HSK_STATUS::MO); ?></option>
                                                    <?php //if(check_session_action(get_menu_id(), STATUS_APPROVE)) { ?>
													<option value="<?php echo SRF_TYPE::OUT_OF_ORDER ?>" <?php echo $srf_id > 0 ? $srf->srf_type == SRF_TYPE::OUT_OF_ORDER ? 'selected="selected"' : '' : '' ?> ><?php echo HSK_STATUS::OO . ' - '  . HSK_STATUS::caption(HSK_STATUS::OO); ?></option>
                                                    <option value="<?php echo SRF_TYPE::OUT_OF_SERVICE ?>" <?php echo $srf_id > 0 ? $srf->srf_type == SRF_TYPE::OUT_OF_SERVICE ? 'selected="selected"' : '' : '' ?> ><?php echo HSK_STATUS::OS . ' - ' . HSK_STATUS::caption(HSK_STATUS::OS); ?></option>
													<?php //} ?>
                                                </select>
                                            </div>
                                        </div>
										
									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">SRF No </label>
                                            <div class="col-md-6" >
                                                <span class="form-control"><?php echo $srf_id > 0 ? $srf->srf_no : 'New'; ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="requested_by">Request By</label>
                                            <div class="col-md-8">
                                                <input type="text" name="requested_by" class="form-control" value="<?php echo($srf_id > 0 ? $srf->requested_by : ''); ?>" >
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                            <label class="control-label col-md-3" ">Unit</label>
                                            <div class="col-md-3">
                                                <select name="unit_id" id="unit_id" class="select2me form-control">
                                                    <option value="" <?php echo $srf_id <= 0 ? 'selected="selected"' : '' ?>>-- Select --</option>
													 <?php
                                                    if($srf_id > 0) 
													{
                                                        $qry = $this->db->query("SELECT * FROM ms_unit
                                                              WHERE unit_id IN('" . $srf->unit_id. "')
                                                              ORDER BY unit_code");
                                                        foreach ($qry->result_array() as $unit) {
                                                            echo '<option value="' . $unit['unit_id'] . '" ' . ($srf_id > 0 ? $srf->unit_id == $unit['unit_id'] ? 'selected="selected"' : '' : '') . '>' . $unit['unit_code'] . '</option>';
                                                        }
                                                    }
                                                    ?>
													<option value="0" <?php if($srf_id > 0){ echo  $srf->unit_id == 0 ? 'selected="selected"' : '';} ?>>Public Area</option>
                                                </select>
                                            </div>
										</div>
									   
                                    </div>
                                    <div class="col-md-6 hide">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="is_booking_available">&nbsp;</label>
                                            <div class="col-md-7">
                                                <input type="checkbox" name="is_booking_available" value="1" <?php echo ($srf_id > 0 ? $srf->is_booking_available > 0 ? 'checked' : '' : '') ?>><span class="label bg-yellow-casablanca">Booking Enabled</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" style="padding-top: 2px;">Description</label>
                                            <div class="col-md-9">
                                                <textarea name="srf_note" rows="2" class="form-control" style="resize: vertical;"><?php echo ($srf_id > 0 ? $srf->srf_note : '') ;?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TABLE DETAIL -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="multi_date">Off Date</label>
                                            <div class="col-md-9">
                                                <textarea name="multi_date_display" id="multi_date_display" rows="2" class="form-control" style="resize: vertical;" readonly><?php echo(isset($multi_date) ? $multi_date : ''); ?></textarea>
                                                <?php if(check_session_action(get_menu_id(), STATUS_APPROVE)) { ?>
												<div class="input-group date col-md-9" id="multi_date_picker" >
                                                    <input type="hidden" class="form-control" name="multi_date" value="<?php echo(isset($multi_date) ? $multi_date : ''); ?>" readonly >
                                                	<span class="input-group-btn">													
														<button class="btn default" type="button" ><i class="fa fa-calendar" ></i></button>													
													</span>
                                                </div>
												<?php } ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- END TABLE DETAIL -->
							</div>
						</form>
						<!-- END FORM-->
					</div>
				</div>
			</div>
		</div>
		<!-- END PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php
                if($srf_id > 0){
                    $created = '';
                    $modified = '';

                    if ($srf->created_by > 0) {
                        $created .= "<div class='col-md-8'><h6>Created by " . get_user_fullname( $srf->created_by) . " (" . date_format(new DateTime($srf->created_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                    }

                    $approved = $this->db->get_where('app_log', array('reff_id' => $srf_id, 'feature_id' => Feature::FEATURE_CS_SRF, 'action_type' => STATUS_APPROVE));
                    if($approved->num_rows() > 0){
                        $approved = $approved->row();
                        $modified .= "<div class='col-md-4'><h6>Approved by " . get_user_fullname($approved->user_id) . " (" . date_format(new DateTime($approved->log_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                    }
                    /*
                    if ($srf->modified_by > 0) {
                        $modified .= "<div class='col-md-4'><h6>Last Modified by " . get_user_fullname( $srf->modified_by) . " (" . date_format(new DateTime($srf->modified_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                    }
                    */
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
</div>
<!-- END CONTENT -->

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;

    $(document).ready(function(){
        <?php echo picker_input_date() ;?>

        if(isedit > 0){
            $('#form-entry').block({
                message: null ,
                overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
            });
        }

        $('#multi_date_picker').datepicker({
                format:"dd-mm-yyyy",
                multidate: true,
                allowDeselection: true,
                autoClose:true,
                multidateSeparator:"|"
        });

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

        var handleValidation = function() {
            // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation
            var form1 = $('#form-entry');
            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    srf_date: {
                        required: true
                    },
                    unit_id:{
                        required: true
                    },
                    srf_note: {
                        required: true
                    },
                    srf_type:{
                        required: true
                    },
                    multi_date:{
                        required: true
                    }
                },
                messages: {
                    srf_date: "SRF Date must be selected",
                    unit_id: "Unit must be selected",
                    requested_by: "Request By must not empty",
                    srf_note: "Description must not empty",
                    srf_type: "Type must be selected",
                    multi_date: "Off date must not empty"
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.srf_date != null){
                        toastr["error"](validator.invalid.srf_date, "Warning");
                    }
                    if(validator.invalid.unit_id != null){
                        toastr["error"](validator.invalid.unit_id, "Warning");
                    }
                    if(validator.invalid.requested_by != null){
                        toastr["error"](validator.invalid.requested_by, "Warning");
                    }
                    if(validator.invalid.srf_type != null){
                        toastr["error"](validator.invalid.srf_type, "Warning");
                    }
                    if(validator.invalid.srf_note != null){
                        toastr["error"](validator.invalid.srf_note, "Warning");
                    }
                    if(validator.invalid.multi_date != null){
                        toastr["error"](validator.invalid.multi_date, "Warning");
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

        $('input[name="multi_date"').on('change', function () {
            $('#multi_date_display').val($(this).val());
            if($(this).val() != ''){
                $('input[name="multi_date"').closest('.form-group').removeClass('has-error');
            }else{
                $('input[name="multi_date"').closest('.form-group').addClass('has-error');
            }
        });

        $('button[name="save"]').on('click',function(e){
            e.preventDefault();
            $('input[name="multi_date"').closest('.form-group').removeClass('has-error');

            if($("#form-entry").valid()){
                var multi_date = $('input[name="multi_date"').val();
				var srf_type = $('select[name="srf_type"').val(); 
                if(multi_date != '' || srf_type == 0){
                    var url = '<?php echo base_url('housekeeping/srf/submit_srf.tpd');?>';
                    $("#form-entry").attr("method", "post");
                    $('#form-entry').attr('action', url).submit();
                    //$('#form-entry').submit();
                }else{
                    $('input[name="multi_date"').closest('.form-group').addClass('has-error');
                    toastr["error"]("Off date must not empty", "Warning");
                }
            }
        });

        $('button[name="btn_save_close"]').on('click',function(e){
            e.preventDefault();
            $('input[name="multi_date"').closest('.form-group').removeClass('has-error');

            if($("#form-entry").valid()){
                var multi_date = $('input[name="multi_date"]').val();				
				var srf_type = $('select[name="srf_type"]').val(); 
                if(multi_date != '' || srf_type == 0){ 
                    var url = '<?php echo base_url('housekeeping/srf/submit_srf.tpd');?>';
                    $("#form-entry").append('<input type="hidden" name="save_close" value="">');
                    $("#form-entry").attr("method", "post");
                    $('#form-entry').attr('action', url).submit();
                }else{
                    $('input[name="multi_date"').closest('.form-group').addClass('has-error');
                    toastr["error"]("Off date must not empty", "Warning");
                }
            }
        });

        $('#approve-button').on('click', function(){
            var id = $('input[name="srf_id"]').val();
            var status = '<?php echo STATUS_APPROVE ?>';

            bootbox.confirm("Approve this SRF ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        target: '.form-entry',
                        boxed: true,
                        message: 'Processing...'
                    });

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('housekeeping/srf/action_request');?>",
                        dataType: "json",
                        data: {srf_id : id, action : status, reason : ''}
                    })
                        .done(function (value) {
                            $('.form-entry').unblock();

                            if (value.type == '0' || value.type == '1') {
                                if (value.type == '1') {
                                    toastr["success"](value.message, "Success");
                                    if(value.redirect_link != ''){
                                        window.location.href = value.redirect_link;
                                    }
                                }
                                else {
                                    toastr["error"](value.message, "Warning");
                                }
                            }
                            else {
                                toastr["error"]("Close failed, please try again later.", "Error");
                            }
                        })
                        .fail(function () {
                            $('.form-entry').unblock();
                            toastr["error"]("Close failed, please try again later.", "Error");
                        });
                }
            });
        });

        $('#close-button').on('click', function(){
            var id = $('input[name="srf_id"]').val();
            var status = '<?php echo STATUS_CLOSED ?>';

            bootbox.confirm("Complete this SRF ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        target: '.form-entry',
                        boxed: true,
                        message: 'Processing...'
                    });

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('housekeeping/srf/action_request');?>",
                        dataType: "json",
                        data: {srf_id : id, action : status, reason : ''}
                    })
                        .done(function (value) {
                            $('.form-entry').unblock();

                            if (value.type == '0' || value.type == '1') {
                                if (value.type == '1') {
                                    toastr["success"](value.message, "Success");
                                    if(value.redirect_link != ''){
                                        window.location.href = value.redirect_link;
                                    }
                                }
                                else {
                                    toastr["error"](value.message, "Warning");
                                }
                            }
                            else {
                                toastr["error"]("Close failed, please try again later.", "Error");
                            }
                        })
                        .fail(function () {
                            $('.form-entry').unblock();
                            toastr["error"]("Close failed, please try again later.", "Error");
                        });
                }
            });
        });
		
		//Srf Type change
            $('#srf_type').on('change', function(){
                var srf_type = $(this).val();				
				var srf_id = $('input[name="srf_id"]').val(); 

                $.ajax({
                    url: "<?php echo base_url('housekeeping/srf/get_unit');?>/" + srf_type+"/"+srf_id
                }).done(function(retValue) {
                    $('#unit_id').html(retValue);

                    $('#unit_id').parent().children('.select2-container').children('.select2-choice').children('.select2-chosen').html('-- Select --');
                    $('#unit_id').val('');
                });
            });

	});

    function posting_record(debitnoteId){
        var $modal_cal = $('#ajax-posting');

        /*
        if ($modal_cal.hasClass('bootbox') == false) {
            $modal_cal.addClass('modal-fix');
        }

        if ($modal_cal.hasClass('modal-overflow') === false) {
            $modal_cal.addClass('modal-overflow');
        }
        */
        if($("#form-entry").valid()){
            $modal_cal.modal();
        }else{
            toastr["error"]("Please save your changes first !", "Warning");
        }
    }

</script>