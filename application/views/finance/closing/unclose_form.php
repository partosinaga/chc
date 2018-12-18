<?php

?>
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
			<div class="col-md-6">
				<div class="portlet box <?php echo BOX;?>">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-user"></i>Un-Close Period
						</div>
						<div class="actions">

						</div>
					</div>
					<div class="portlet-body form">
						<form method="post" id="id_form_input" class="form-horizontal" onsubmit="return false">
							<div class="form-body" id="form-entry">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
								<div class="row">
									<div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Un-Close Type</label>
                                            <div class="col-md-5">
                                                <select name="closing_type" class="select2me form-control">
                                                    <option value="month">Closed Month</option>
                                                    <option value="year">Closed Year</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="not_available" class="hide">
                                            <div class="form-group">
                                                <div class="col-md-3">&nbsp;</div>
                                                <div class="col-md-1"><span class="label label-warning" >NOT AVAILABLE</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="close_month" class="hide">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">Period</label>
                                                <div class="col-md-2">
                                                    <select name="close_month_month" class="select2me form-control">
                                                        <?php
                                                        if(isset($close_month)){
                                                            foreach($close_month['month'] as $m){
                                                                echo '<option value="'. $m .'">' . $m . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="close_month_year" class="select2me form-control">
                                                        <?php
                                                        if(isset($close_month)){
                                                            foreach($close_month['year'] as $y){
                                                                echo '<option value="'. $y .'">' . $y . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="close_year" class="hide">
                                            <div class="form-group">
                                                <label class="control-label col-md-3">Period</label>
                                                <div class="col-md-3">
                                                    <select name="close_year_year" class="select2me form-control">
                                                        <?php
                                                        if(isset($close_year)){
                                                            foreach($close_year as $y){
                                                                echo '<option value="'. $y .'">' . $y . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">&nbsp;</label>
                                            <div class="col-md-3">
                                                <input type="button" class="btn btn-circle btn-sm green-seagreen <?php echo(isset($close_account['month']) && count($close_month['month']) > 0 ? ($close_account['month']['coa_id'] > 0 ? '' : 'hide') : 'hide'); ?>" name="submit" value="UNCLOSE" id="btn_submit">
                                            </div>
                                        </div>
									</div>
								</div>
								<div class="row">
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

<div id="ajax-modal" class="modal fade" data-replace="true" data-width="1024" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
	$(document).ready(function(){
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "10000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        function init_page() {
            $('select.select2me').select2();
        }
        init_page();

        function init_closing(){
            var type = $('select[name="closing_type"] option:selected').val();
            if (type == 'month') {
                var valid = $('select[name="close_month_month"] option').length > 0 ? true : false;

                if(valid){
                    $('#close_month').removeClass('hide');
                    $('#close_year').addClass('hide');
                    $('#not_available').addClass('hide');

                    $('#btn_submit').removeClass('hide');
                }else{
                    $('#close_month').addClass('hide');
                    $('#close_year').addClass('hide');
                    $('#not_available').removeClass('hide');

                    $('#btn_submit').addClass('hide');
                }
            } else if (type == 'year') {
                var valid = $('select[name="close_year_year"] option').length > 0 ? true : false;

                if(valid){
                    $('#close_year').removeClass('hide');
                    $('#close_month').addClass('hide');
                    $('#not_available').addClass('hide');

                    $('#btn_submit').removeClass('hide');
                }else{
                    $('#close_month').addClass('hide');
                    $('#close_year').addClass('hide');
                    $('#not_available').removeClass('hide');

                    $('#btn_submit').addClass('hide');
                }
            }
        }

        init_closing();

        $('select[name="closing_type"]').on('change', function(){
            init_closing();
        });

        $('select[name="close_month_year"]').on('change', function(){
            var currentYear = '<?php echo date('Y'); ?>';
            var currentMonth = '<?php echo date('m'); ?>';
            var opt = '';
            if($(this).val() != currentYear){
                for(var i=1;i<=12;i++){
                    opt += '<option value="' + i + '">' + i + '</option>';
                }
            }else{
                for(var i=1;i<=currentMonth;i++){
                    opt += '<option value="' + i + '">' + i + '</option>';
                }
            }
            $('select[name="close_month_month"]').html(opt);
        });

        function validate_submit() {
            var result = true;

            if($('.form-group').hasClass('has-error')){
                $('.form-group').removeClass('has-error');
            }

            return result;
        }

        $('#btn_submit').on('click', function(){
            var closingType = $('select[name="closing_type"]').val();

            var message = "Unclose all closed entries of period ";
            if(closingType == 'month'){
                message += "[" + $('select[name="close_month_year"]').val() + "-" + $('select[name="close_month_month"]').val() + "]";
            }else{
                message += "[" + $('select[name="close_year_year"]').val() + "]";
            }

            bootbox.confirm({
                message: message,
                buttons: {
                    cancel: {
                        label: "No",
                        className: "btn-inverse"
                    },
                    confirm:{
                        label: "Yes",
                        className: "btn-primary"
                    }
                },
                callback: function(result) {
                    if(result === false){
                        //console.log('Empty reason');
                    }else{
                        Metronic.blockUI({
                            target: '#id_form_input',
                            boxed: true,
                            message: 'Processing...'
                        });

                        toastr.clear();

                        if(closingType == 'month'){
                            var periodMonth = $('select[name="close_month_month"]').val();
                            var periodYear = $('select[name="close_month_year"]').val();
                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('finance/closing/submit_unclose_month');?>",
                                dataType: "json",
                                data: { period_year : periodYear, period_month : periodMonth}
                            })
                                .done(function( msg ) {
                                    if(msg.type == '0' || msg.type == '1'){
                                        if(msg.type == '1'){
                                            window.location.assign(msg.redirect_link);
                                        } else {
                                            toastr["error"](msg.message, "Error");
                                            $('#id_form_input').unblock();
                                        }
                                    } else {
                                        toastr["error"]("Unclosing failed, please try again later.", "Error");
                                        $('#id_form_input').unblock();
                                    }
                                })
                                .fail(function(e) {
                                    toastr["error"]("Unclosing failed, please try again later.", "Error");
                                    $('#id_form_input').unblock();
                                });
                        }else{
                            var periodYear = $('select[name="close_year_year"]').val();

                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('finance/closing/submit_unclose_year');?>",
                                dataType: "json",
                                data: { period_year : periodYear}
                            })
                                .done(function( msg ) {
                                    if(msg.type == '0' || msg.type == '1'){
                                        if(msg.type == '1'){
                                            window.location.assign(msg.redirect_link);
                                        } else {
                                            toastr["error"](msg.message, "Error");
                                            $('#id_form_input').unblock();
                                        }
                                    } else {
                                        toastr["error"]("Unclosing failed, please try again later.", "Error");
                                        $('#id_form_input').unblock();
                                    }
                                })
                                .fail(function(e) {
                                    toastr["error"]("Unclosing failed, please try again later.", "Error");
                                    $('#id_form_input').unblock();
                                });
                        }
                    }
                }
            });
        });
        //----- END SUBMIT -------------//

	});
</script>