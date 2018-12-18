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
                if($reservation_id > 0){
                    if($row->status == ORDER_STATUS::RESERVED){
                        $form_mode = '';
                    }else{
                        $form_mode = 'disabled';
                    }
                }

				?>
			</ul>
		</div>
		<!-- END PAGE HEADER-->
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
                <div class="portlet ">
					<div class="portlet-title bg-inverse" >
                        <div class="caption">
                            <i class="fa fa-star font-blue-hoki"></i>Check In Verification #<strong><?php echo ($reservation_id > 0 ? $row->reservation_code : '');?></strong>&nbsp;&nbsp;<span class="badge badge-primary bold"><?php echo ($reservation_id > 0 ? strtoupper(RES_TYPE::caption($row->reservation_type)) : ''); ?> </span>
						</div>
						<div class="actions">
                            <?php
                            if($reservation_id > 0){
                                if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED ){
                            ?>
                                <a href="<?php echo base_url('frontdesk/reservation/pdf_reservation/'. ($reservation_id > 0 ? $reservation_id : '0') .'.tpd');?>" class="btn default blue-ebonyclay"><i class="fa fa-print"></i></a>
                            <?php
                                }
                            }
                            ?>
                            <a href="<?php echo (isset($back_url) ? $back_url : base_url('frontdesk/management/guest_list/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="<?php echo base_url('frontdesk/management/submit_checkin.tpd');?>" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="reservation_id" name="reservation_id" value="<?php echo $reservation_id;?>" />
                            <input type="hidden" name="reservation_code" value="<?php echo ($reservation_id > 0 ? $row->reservation_code : '');?>" />
                            <input type="hidden" name="reservation_type" value="<?php echo ($reservation_id > 0 ? $row->reservation_type : '');?>" />
                            <input type="hidden" name="tenant_id" value="<?php echo ($reservation_id > 0 ? $row->tenant_id : 0);?>" />

                            <?php
                            if($form_mode == ''){
                                if(($row->reservation_type == RES_TYPE::PERSONAL) ||
                                                    $row->reservation_type == RES_TYPE::CORPORATE || $row->reservation_type == RES_TYPE::HOUSE_USE) {
                                    ?>
                                    <div class="form-actions top">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button type="submit" class="btn blue-ebonyclay btn-circle"
                                                        name="save_close">Submit Check In
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <?php if($reservation_id > 0){ ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-2 col-sm-2">Guest</label>
                                                <label class="control-label bold uppercase"><?php echo ($reservation_id > 0 ? $tenant->tenant_salutation . ' ' . $tenant->tenant_fullname : '');?></label>
                                            </div>
                                        </div>
                                        <?php if($row->agent_id > 0 && isset($agent)){ ?>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-2 col-sm-2">Agent</label>
                                                    <label class="control-label bold uppercase"><?php echo ($agent->agent_name . ' - ' . $agent->agent_pic);?></label>
                                                </div>
                                            </div>
                                        <?php }?>
                                    </div>
                                    <?php if($row->reservation_type == RES_TYPE::CORPORATE && isset($company)){ ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label col-md-1 col-sm-1">Company</label>
                                                    <label class="control-label bold uppercase"><?php echo ($company->company_name);?></label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <div class="row">
                                        <div class="col-md-4" >
                                            <div class="form-group" style="margin-left:0px;">
                                                <input type="checkbox" name="is_walkin"  value="1" <?php echo ($reservation_id > 0 ? $row->is_frontdesk > 0 ? 'checked' : ($row->time_diff <= 30 && $row->time_diff >= 0 ? 'checked' : '') : '');?> <?php echo $row->is_frontdesk > 0 ? 'readonly' : ''; ?> ><span for="is_walkin" class="font-red-sunglo">Walk-In Reservation</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box light bg-inverse" >
                                            <div class="portlet-title blue-ebonyclay" >
                                                <ul class="nav nav-tabs">
                                                    <li class="active" >
                                                        <a href="#portlet_main" data-toggle="tab" >
                                                            <i class="fa fa-tag"></i>
                                                            Room Charges</a>
                                                    </li>
                                                    <li>
                                                        <a href="#portlet_payment" data-toggle="tab" >
                                                            <i class="fa fa-money"></i>
                                                            Payments</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="portlet-body ">
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="portlet_main">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <!-- BEGIN TAB PORTLET-->
                                                                <table class="table table-striped table-hover table-bordered" id="table_room" name="table_room">
                                                                    <thead>
                                                                    <tr>
                                                                        <th class="text-center " width="6%">Unit</th>
                                                                        <th >Type</th>
                                                                        <th class="text-center " width="8%">Check In</th>
                                                                        <th class="text-center " width="8%">Check Out</th>
                                                                        <th class="text-center " width="9%">Duration</th>
                                                                        <th class="text-right " width="10%">Amount</th>
                                                                        <th class="text-right " width="9%">Discount</th>
                                                                        <th class="text-right " width="9%">Tax</th>
                                                                        <th class="text-right " width="10%">Subtotal</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    if(isset($unit_rates)){
                                                                        $count = count($unit_rates);

                                                                        $grandtotal = 0;
                                                                        foreach($unit_rates as $room){
                                                                            $subtotal = round($room['local_amount'] + $room['tax_amount'] - $room['discount'],0);
                                                                            echo '<tr>
                                                                        <td class="text-center" >
                                                                            <input type="hidden" name="billdetail_id[]" value="' . $room['billdetail_id'] . '">
                                                                            <input type="hidden" name="transtype_id[]" value="'. $room['transtype_id'] . '"><input type="hidden" name="unit_id[]" value="'. $room['unit_id'] . '">' . $room['unit_code']. '</td>
                                                                        <td>' . $room['unittype_desc']. '</td>
                                                                        <td class="text-center">
                                                                            <input type="hidden" name="bill_start_date[]" value="' . $room['bill_start'] . '">' . ymd_to_dmy($room['bill_start']) . '</td>
                                                                        <td class="text-center">
                                                                            <input type="hidden" name="bill_end_date[]" value="' . $room['bill_end'] . '">' . ymd_to_dmy($room['bill_end']) . '</td>
                                                                        <td class="text-center ">
                                                                        <input type="hidden" name="is_monthly[]" value="' . $room['is_monthly'] . '">
                                                                        <input type="hidden" name="year_interval[]" value="'. $room['year_interval'] . '">
                                                                        <input type="hidden" name="month_interval[]" value="'. $room['month_interval'] . '">
                                                                        <input type="hidden" name="unit_duration[]" value="'. $room['duration'] . '">' . $room['duration'] . '</td>
                                                                        <td class="text-right">
                                                                        <input type="hidden" name="local_amount[]" value="'. $room['local_amount'] . '">' . format_num($room['local_amount'],0) . '</td>
                                                                        <td class="text-right">
                                                                        <input type="hidden" name="discount_amount[]" value="'. $room['discount'] . '">' . format_num($room['discount'],0) . '</td>
                                                                        <td class="text-right">
                                                                        <input type="hidden" name="tax_amount[]" value="'. $room['tax_amount'] . '">' . format_num($room['tax_amount'],0) . '</td>
                                                                        <td class="text-right">
                                                                        <input type="hidden" name="subtotal_amount[]" value="'. $subtotal . '">' . format_num($subtotal,0) . '</td>';

                                                                            echo  '</tr>';
                                                                            $grandtotal += $subtotal;
                                                                        }
                                                                    }
                                                                    ?>
                                                                    </tbody>
                                                                    <tfoot>
                                                                    <tr>
                                                                        <td class="text-right " colspan="8" >Total Room Charge</td>
                                                                        <td class="text-right font-green-seagreen"><?php echo format_num($grandtotal,0); ?></td>
                                                                    </tr>
                                                                    </tfoot>
                                                                </table>
                                                                <!-- END TAB PORTLET-->

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="portlet_payment" style="height: 150px;overflow-y: scroll;overflow-x:hidden;">
                                                        <div class="row">
                                                            <div class="col-md-10">
                                                                <div id="pnl_payment_list">
                                                                    <table class="table table-striped table-hover table-bordered " id="table_payment" name="table_payment">
                                                                        <thead>
                                                                        <tr>
                                                                            <th class="text-center" width="10%">Date</th>
                                                                            <th class="text-center" width="10%">Doc No</th>
                                                                            <th class="text-center" width="20%">Type</th>
                                                                            <th class="text-left" >Description</th>
                                                                            <th class="text-center" width="15%">Amount</th>
                                                                        </tr>

                                                                        </thead>
                                                                        <tbody>
                                                                        <?php
                                                                        $totalPayment = 0;
                                                                        if(isset($payments)){
                                                                            foreach($payments as $payment){
                                                                                echo '<tr>
                                                                            <td class="text-center" style="vertical-align:middle;">' . dmy_from_db($payment['doc_date']) .'</td>
                                                                            <td class="text-center" style="vertical-align:middle;"><input type="hidden" name="bookingreceipt_id[]" value="' . 0 . '"><input type="hidden" name="unique_id[]" value=""><input type="hidden" name="paymenttype_id[]" value="' . 0 . '">' . $payment['doc_no'] . '</td>
                                                                            <td class="text-left" style="vertical-align:middle;">' . $payment['type'] .'</td>
                                                                            <td class="text-left" style="vertical-align:middle;">' . $payment['remark'] .'</td>
                                                                            <td class="text-right" ' . ($payment['amount'] < 0 ? 'style="padding-right:7px;"' :'') . '>' . amount_journal($payment['amount']) .'</td>
                                                                          </tr>';
                                                                                $totalPayment += $payment['amount'];
                                                                            }
                                                                        }
                                                                        ?>
                                                                        </tbody>
                                                                        <tfoot>
                                                                        <th colspan="4" class="text-center">Total Payment</th>
                                                                        <th class="text-right" <?php echo($totalPayment < 0 ? 'style="padding-right:7px;"' :''); ?>><?php echo amount_journal($totalPayment); ?></th>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">

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

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;
    var PAYMENT_CREDITCARD = <?php echo creditcard_paymenttypeid(); ?>;

    if(isedit > 0){
        /*
        $('#form-entry').block({
            message: null ,
            overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
        });
        */
    }

    var handleMask = function() {
        $(".mask_currency").inputmask("numeric",{
            radixPoint:".",
            autoGroup: true,
            groupSeparator: ",",
            digits: 2,
            groupSize: 3,
            removeMaskOnSubmit: true,
            autoUnmask: true
        });
    }

    $(window).load(function(){
        handleMask();
    });

    $(document).ready(function(){
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "right",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal

        };

        //handleMask();

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
                    tenant_name: {
                        required: true
                    }
                },
                messages: {
                    tenant_name: "Name must be not empty or must be selected",
                    passport_no: "KTP/KITAS/Passport must not empty",
                    passport_issuedplace: "Passport issued place must not empty",
                    passport_issueddate: "Passport issued date must not empty",
                    tenant_phone: "Phone/Mobile must not empty",
                    reservation_id: "No room selected. Reservation can not be processed."
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.tenant_name != null){
                        toastr["warning"](validator.invalid.tenant_name, "Warning");
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

        //initiate validation
        handleValidation();

        var grid_tenant = new Datatable();
        //COA
        var handleTableTenant = function (num_index) {
            // Start Datatable Item
            grid_tenant.init({
                src: $("#datatable_tenant"),
                onSuccess: function (grid_tenant) {
                    // execute some code after table records loaded
                },
                onError: function (grid_tenant) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_tenant) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "bSortable": true, "sWidth" : '10%' },
                        { "sWidth" : '40%' },
                        { "sWidth" : '20%' },
                        { "sClass": "text-center", "sWidth" : '15%' },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('frontdesk/reservation/get_modal_tenant');?>/" + num_index // ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_tenant_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('#btn_lookup_tenant').on('click', function(){
            var $modal = $('#ajax-modal');
            var num_index = parseInt($(this).attr('data-index')) || 0;

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('frontdesk/reservation/ajax_modal_tenant');?>.tpd', '', function () {
                    $modal.modal();
                    handleTableTenant(num_index);

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if ($modal.hasClass('modal-overflow') === false) {
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});
                });
            }, 150);
        });

        $('.btn-select-tenant').live('click', function (e) {
            e.preventDefault();

            var tenant_id = parseInt($(this).attr('data-id')) || 0;
            //var tenant_code = $(this).attr('data-code');
            var tenant_name = $(this).attr('data-desc');

            $('input[name="tenant_id"]').val(tenant_id);
            $('input[name="tenant_name"]').val(tenant_name);

            $('#ajax-modal').modal('hide');
        });

        $('input[name="tenant_type"]').live('click', function (e) {
            //e.preventDefault();
            var tenant_type = $(this).val() || 0;
            var ids = [];
            var start_dates = [];
            var end_dates = [];
            $('#table_room > tbody > tr ').each(function() {
                var name_val = $(this).find('input[name*="unit_id"]').val();
                ids.push(name_val);
                var arrival_val = $(this).find('input[name*="arrival_date"]').val();
                var departure_val = $(this).find('input[name*="departure_date"]').val();
                start_dates.push(arrival_val);
                end_dates.push(departure_val);
            });

            if(ids.length > 0){
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('frontdesk/reservation/ajax_change_tenant_type');?>",
                    //dataType: "json",
                    data: { is_member : tenant_type, unit_ids: ids, arrival_dates : start_dates, departure_dates : end_dates}
                })
                    .done(function(msg) {
                        if(msg != ''){
                            $('#table_room > tbody ').html(msg);
                        }
                    });
            }
        });

        $('.payment_add').live('click', function (e) {
            //console.log('payment add ');
            $('#pnl_payment_list').addClass('hide');
            $('#pnl_payment_form').removeClass('hide');
        });
        $('#btn_cancel_payment').live('click', function (e) {
            $('#pnl_payment_form').addClass('hide');
            $('#pnl_payment_list').removeClass('hide');

            resetPaymentForm();
        });
        $('select[name="payment_type"]').on('change', function (e) {
            var ptype = $(this).val() || 0;
            if(ptype == PAYMENT_CREDITCARD){
                $('#card_info').removeClass('hide');
            }else{
                $('#card_info').addClass('hide');
            }
        });

        $('#btn_submit_payment').live('click', function (e) {
            e.preventDefault();

            if(validatePayment()){
                var paymentType = $('select[name="payment_type"]').val();
                var paymentAmount = $('input[name="payment_amount"]').val();
                var cardName =  $('input[name="payment_card_name"]').val();
                var cardNo =  $('input[name="payment_card_no"]').val();

                var date = new Date();
                var uniqueId = date.toLocaleString(['ban', 'id']);
                //console.log('type ' + paymentType + ' amount ' + paymentAmount + " name " + cardName + " no " + cardNo);

                //Add to temp
                var newRowContent = "<tr>" +
                    "<td class=\"text-center\" style=\"vertical-align:middle;\"><input type=\"hidden\" name=\"bookingreceipt_id[]\" value=\"0\"><input type=\"hidden\" name=\"unique_id[]\" value=\"" + uniqueId + "\"><input type=\"hidden\" name=\"paymenttype_id[]\" value=\"" + paymentType + "\">" + $('select[name="payment_type"] option:selected').text() + "</td>" +
                    "<td ><input type=\"text\" name=\"payment_amount[]\" value=\""+ paymentAmount + "\" class=\"form-control text-right mask_currency pay_amount\" readonly></td>" +
                    "<td style=\"vertical-align:middle;\">" +
                    "<input type=\"hidden\" name=\"payment_card_name[]\" value=\"" + cardName + "\" >" +
                    "<input type=\"hidden\" name=\"payment_card_no[]\" value=\"" + cardNo + "\" >" +
                    "<a class=\"btn btn-danger btn-xs tooltips\" data-original-title=\"Remove\" href=\"javascript:;\" onclick=\"delete_receipt(" + 0 + ",'" + uniqueId + "');\" ><i class=\"fa fa-times\"></i></a></td>" +
                    "</tr>";

                $('#table_payment tbody').append(newRowContent);

                resetPaymentForm();

                $('#pnl_payment_form').addClass('hide');
                $('#pnl_payment_list').removeClass('hide');
                $('#card_info').removeClass('hide');

                //handleCalculation();
                handleMask();
            }

        });

        function validatePayment(){
            var valid = true;

            var paymentType = $('select[name="payment_type"]').val();
            var paymentAmount = $('input[name="payment_amount"]').val();
            var cardName = $('input[name="payment_card_name"]').val();
            var cardNo = $('input[name="payment_card_no"]').val();

            if(paymentAmount <= 0){
                //toastr["warning"]("Paid amount must not 0.", "Warning");
                var icon = $('input[name="payment_amount"]').parent('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");
                icon.attr("data-original-title", "Paid amount must not 0.").tooltip({'container': 'body'});

                $('input[name="payment_amount"]').closest('.form-group').removeClass("has-success").addClass('has-error');

                valid = false;
            }

            if(paymentType == PAYMENT_CREDITCARD){
                if(cardName.trim() == ''){
                    //toastr["warning"]("Credit Card name must not empty.", "Warning");
                    var icon = $('input[name="payment_card_name"]').parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", "Credit Card name must not empty.").tooltip({'container': 'body'});

                    $('input[name="payment_card_name"]').closest('.form-group').removeClass("has-success").addClass('has-error');

                    valid = false;
                }

                if(cardNo.trim() == ''){
                    //toastr["warning"]("Credit Card no must not empty.", "Warning");
                    var icon = $('input[name="payment_card_no"]').parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", "Credit Card no must not empty.").tooltip({'container': 'body'});

                    $('input[name="payment_card_no"]').closest('.form-group').removeClass("has-success").addClass('has-error');

                    valid = false;
                }
            }
            return valid;
        }

    });

    function resetPaymentForm(){
        $('select[name="payment_type"]').val(1);
        $('input[name="payment_amount"]').val(0);
        $('input[name="payment_card_name"]').val('');
        $('input[name="payment_card_no"]').val('');

        $('input[name="payment_amount"]').closest('.form-group').removeClass("has-error")
        $('input[name="payment_card_name"]').closest('.form-group').removeClass("has-error")
        $('input[name="payment_card_no"]').closest('.form-group').removeClass("has-error")
    }

    function delete_receipt(detailId, uniqueId){
        if(detailId > 0 || uniqueId != ''){
            bootbox.confirm("Remove this payment ?", function (result) {
                if (result == true) {
                    if(detailId > 0){
                        Metronic.blockUI({
                            target: '.modal-content-edit',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('frontdesk/management/delete_booking_receipt');?>",
                            dataType: "json",
                            data: { bookingreceipt_id : detailId}
                        })
                            .done(function( msg ) {
                                if(msg.type != '0'){

                                }
                                else {
                                    console.log('ajax delete unit.' + detailId);
                                }

                                delete_frontend_payment(detailId, '');
                                Metronic.unblockUI('.modal-content-edit');
                            });
                    }else{
                        delete_frontend_payment(0, uniqueId);
                    }
                }
            });
        }
    }

    function delete_frontend_payment(detailId,uniqueId){
        try{
            if(detailId > 0){
                $('#table_payment > tbody > tr').find('input[name*="bookingreceipt_id"][value="' + detailId + '"]').parent().parent().remove();
            }else{
                $('#table_payment > tbody > tr').find('input[name*="unique_id"][value="' + uniqueId + '"]').parent().parent().remove();
            }
        }catch(e){
            console.log(e);
        }
    }

    function posting_record(headerId){
        bootbox.confirm({
            message: "Posting this transaction ?",
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
                    /*
                    //console.log(result);
                    Metronic.blockUI({
                        boxed: true
                    });

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('finance/journal/ajax_posting_journal_by_id');?>",
                        dataType: "json",
                        data: { reservation_id: headerId}
                    })
                        .done(function( msg ) {
                            Metronic.unblockUI();

                            if(msg.type == '1'){
                                toastr["success"](msg.message, "Success");

                                window.location.assign(msg.redirect_link);
                            }
                            else {
                                toastr["warning"](msg.message, "Warning");
                            }
                        });
                    */
                }
            }
        });
    }


</script>