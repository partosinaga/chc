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
                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
				<!-- Begin: life time stats -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-users"></i>Reservation Manage
						</div>
						<div class="actions">
                            <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
							<a href="<?php echo base_url('frontdesk/reservation/find_room.tpd');?>" class="btn btn-circle purple-plum">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
							New Reservation </span>
							</a>
							<?php } ?>
						</div>
					</div>
					<div class="portlet-body table-responsive">
                        <input type="hidden" name="cc_percentage" value="<?php echo (isset($payment_creditcard) ? $payment_creditcard->card_percent : '0'); ?>">
                        <input type="hidden" name="cc_veritrans" value="<?php echo (isset($payment_creditcard) ? $payment_creditcard->veritrans_fee : '0'); ?>">
                        <input type="hidden" name="cc_other_fee" value="<?php echo (isset($payment_creditcard) ? $payment_creditcard->other_fee : '0'); ?>">
                        <div class="table-container" >
                          <div class="col-md-12" style="padding-bottom:300px;">
							<table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="table_reservation">
							<thead>
							<tr role="row" class="heading">
								<th width="2%" >
                                     #
								</th>
								<th width="8%">
									 Folio No
								</th>
								<th >
									 Date
								</th>
								<th >
									 Guest
								</th>
                                <th >
                                     Company
                                </th>
                                <th >

                                </th>
                                <th >
                                    Room
                                </th>
                                <th >
                                    Check In
                                </th>
                                <th >
                                    Check Out
                                </th>
                                <th >

                                </th>
                                <th class="text-center">
                                    Amount
                                </th>
                                <th class="text-center">
                                    Payment
                                </th>
                                <th class="text-center">
                                    Deposit
                                </th>
								<th >

								</th>
                                <th style="width:9%;">
									 Actions
								</th>
							</tr>
							<tr role="row" class="filter bg-grey-steel">
								<td style="vertical-align: middle;">

								</td>
								<td>
									<input type="text" class="form-control form-filter input-sm" name="filter_no">
								</td>
								<td>

								</td>
								<td>
                                    <input type="text" class="form-control form-filter input-sm" name="filter_name">
								</td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="filter_company">
                                </td>
								<td>

								</td>
                                <td>
                                    <input type="text" class="form-control form-filter input-sm" name="filter_room">
                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
								<td>

								</td>
                                <td>

								</td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
								<td>
									<div class="text-center">
										<button class="btn btn-sm yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
										<button class="btn btn-sm red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
									</div>
								</td>
							</tr>
							</thead>
							<tbody>
							</tbody>
							</table>
						  </div>
                        </div>
					</div>
				</div>
				<!-- End: life time stats -->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
<!-- END CONTENT -->

<div id="ajax-receipt" class="modal fade bs-modal-sm"  data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content col-md-10 note note-success" style="padding: 0px;">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title bold">Official Receipt :</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Receipt Date</label>
                    <div class="input-group col-md-3 col-sm-3" data-date-format="dd-mm-yyyy">
                        <input type="text" class="form-control" name="receipt_date" value="<?php echo (date('d-m-Y'));?>" readonly>
                        <span class="input-group-btn hide">
                            <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="reservation_id" value="0">
                    <input type="hidden" name="is_frontdesk" value="0">
                    <input type="hidden" name="is_credit_card" value="0">
                    <input type="hidden" name="full_payment_amount" value="0">
                    <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Payment Type</label>
                    <select name="paymenttype_id" class="select2me form-control input-medium">
                        <?php
                        $payments = $this->db->query('select * from ms_payment_type where status = '.STATUS_NEW . ' and
                                                      payment_type NOT IN(' . PAYMENT_TYPE::AR_TRANSFER . ',' . PAYMENT_TYPE::PAYMENT_GATEWAY . ') order by pos ');
                        foreach($payments->result_array() as $payType){
                            echo '<option value="'. $payType['paymenttype_id'] .'" payment-type="' . $payType['payment_type'] . '">' . $payType['paymenttype_code'] . ' - ' . $payType['paymenttype_desc'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div id="card_info" >
                    <div class="form-group" >
                        <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Name</label>
                        <input type="text" name="creditcard_name" value="" class="form-control input-medium">
                    </div>
                    <div class="form-group" >
                        <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Card No</label>
                        <input type="text" name="creditcard_no" value="" class="form-control input-medium mask_credit_card">
                    </div>
                    <div class="form-group" id="card_info_expiry">
                        <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Expiry</label>
                        <div class="row">
                        <div class="col-md-2 col-sm-2">
                            <div class="row">
                                <select name="creditcard_expiry_month" class="select2me form-control">
                                    <?php
                                    for($i=1;$i<=12;$i++){
                                        echo '<option value="'. $i .'">' . (strlen($i) == 1 ? '0'. $i : $i) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                                <select name="creditcard_expiry_year" class="select2me form-control">
                                    <?php
                                    $current = date('Y');
                                    for($i=$current;$i<=$current+10;$i++){
                                        echo '<option value="'. $i .'">' . (strlen($i) == 1 ? '0'. $i : $i) . '</option>';
                                    }
                                    ?>
                                </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="form-group clear hide" id="bank_account_info">
                    <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Bank Account</label>
                    <select name="bankaccount_id" class="select2me form-control input-medium">
                        <?php
                        $banks = $this->db->query('select * from fn_bank_account where status = '.STATUS_NEW . ' and iscash <= 0 order by bank_id, bankaccount_code');
                        foreach($banks->result_array() as $bank){
                            echo '<option value="'. $bank['bankaccount_id'] .'">' . $bank['bankaccount_desc'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Amount</label>
                    <input type="hidden" name="min_receipt_amount" id="min_receipt_amount" value="0" class="mask_currency">
                    <input type="text" name="receipt_amount" value="0" class="form-control text-right mask_currency input-small font-blue bold" readonly>
                </div>
                <div class="form-group hide" id="id_bank_fee">
                    <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Bank Charge</label>
                    <input type="text" name="receipt_bank_fee" value="0" class="form-control text-right mask_currency input-small" readonly>
                </div>
                <div class="form-group hide" id="id_veritrans_fee">
                    <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Veritrans</label>
                    <input type="text" name="receipt_veritrans_fee" value="0" class="form-control text-right mask_currency input-small" readonly>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Description</label>
                    <textarea class="form-control input-medium" rows="2" name="bookingreceipt_desc"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn grey-cascade" data-dismiss="modal" id="cancel-receipt">Close</button>
                <button type="button" class="btn green-seagreen bold" id="submit-receipt">Submit</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>
    //var PAYMENT_CREDITCARD = <?php echo creditcard_paymenttypeid(); ?>;

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

        $(".mask_credit_card").inputmask("mask", {"mask": "9999-9999-9999-9999"});
    }

    $(document).ready(function(){
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

		var grid = new Datatable();
		
		var handleRecords = function () {
			grid.init({
				src: $("#table_reservation"),
				onSuccess: function (grid) {
					// execute some code after table records loaded
				},
				onError: function (grid) {
					// execute some code on network or other general error  
				},
				onDataLoad: function(grid) {
					// execute some code on ajax data load
				},
				loadingMessage: 'Populating...',
				dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 
					// Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
					// setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
					// So when dropdowns used the scrollable div should be removed. 
					"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
					
					"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
					"aoColumns": [
						{ "sClass": "text-center", "bSortable": false },
						{ "sClass": "text-center" },
						{ "sClass": "text-center", "sWidth" : "7%" },
                        {"bSortable": false},
                        {"bSortable": false},
                        { "sClass": "text-center", "sWidth" : "2%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "5%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "7%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "8%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "2%", "bSortable": false  },
                        { "sClass": "text-right", "sWidth" : "7%", "bSortable": false },
                        { "sClass": "text-right", "sWidth" : "7%", "bSortable": false },
                        { "sClass": "text-right", "sWidth" : "7%", "bSortable": false },
						{ "sClass": "text-center", "sWidth" : "3%", "bSortable": false },
                        { "bSortable": false, "sClass": "text-center", "sWidth" : "8%"}
					],
					"aaSorting": [],
					"lengthMenu": [
						[10, 20, 50, 100, -1],
						[10, 20, 50, 100, "All"] // change per page values here
					],
					"pageLength": 10, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('frontdesk/reservation/get_reservation_manage/' . get_menu_id());?>"
					},
					"fnDrawCallback": function( oSettings ) {
                        $('.tooltips').tooltip();
					}
				}
			});
			
			var tableWrapper = $("#table_reservation_wrapper");
			tableWrapper.find(".dataTables_length select").select2({
				showSearchInput: false //hide search box with special css class
			});
			
		}

        $('.btn-cancel').live('click', function(){
			var id = $(this).attr('data-id');
			var action = $(this).attr('data-action');

            bootbox.prompt({
                title: "Please enter cancel reason :",
                value: "",
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
                    if(result === null){
                        //console.log('Empty reason');
                    }else if(result === ''){
                        toastr["warning"]("Cancel reason must be filled to proceed.", "Warning");
                    }else{
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('frontdesk/reservation/action_request');?>",
                            dataType: "json",
                            data: { reservation_id: id, action: action, reason:result }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();

                                if(msg.type == '0' || msg.type == '1'){
                                    //grid.getDataTable().ajax.reload();
                                    //grid.clearAjaxParams();
                                    $('#table_reservation').dataTable().api().ajax.url("<?php echo base_url('frontdesk/reservation/get_reservation_manage/' . get_menu_id());?>").load();

                                    if(msg.type == '1'){
                                        toastr["success"](msg.message, "Success");
                                    }
                                    else {
                                        toastr["warning"](msg.message, "Warning");
                                    }
                                }
                                else {
                                    toastr["error"]("Action can not be processed, please try again later.", "Error");
                                }
                            });
                    }
                }
            });
		});

		handleRecords();

        $('.btn-checkin').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');

            bootbox.confirm({
                message: "Perform Check In ?",
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
                    if(result === null){

                    }else{
                        if(result){
                            var url = "<?php echo base_url('frontdesk/management/checkin_form/');?>/" + id;
                            window.location.assign(url);
                        }
                    }
                }
            });
        });

        $('select[name="paymenttype_id"]').on('change', function (e) {
            var element = $(this).find('option:selected');
            var ptype = element.attr("payment-type");
            default_form_receipt(ptype);
            $('input[name="creditcard_name"]').val('');
            $('input[name="creditcard_no"]').val('');
        });

        $('.btn-receipt').live('click', function(e){
            e.preventDefault();

            var id = $(this).attr('data-id');
            $('input[name="reservation_id"]').val(id);
            var minDate = $(this).attr('min-date');
            $('#min_receipt_amount').val($(this).attr('data-min-amount'));
            //$('input[name="receipt_amount"]').val($('#min_receipt_amount').val());
            var full_amount = $(this).attr('data-full-amount');
            $('input[name="full_payment_amount"]').val(full_amount);
            $('input[name="receipt_amount"]').val(full_amount);
            $('input[name="receipt_amount"]').removeAttr('readonly');
            $('input[name="is_frontdesk"]').val($(this).attr('is-frontdesk'));
            if($(this).attr('is-frontdesk') <= 0){
                $('select[name="paymenttype_id"]').attr('disabled','disabled');
            }else{
                $('select[name="paymenttype_id"]').removeAttr('disabled');
            }

            //Set default hide or show Card Info
            var element = $('select[name="paymenttype_id"]').find('option:selected');
            var ptype = element.attr("payment-type");
            default_form_receipt(ptype);

            var $modal = $('#ajax-receipt');

            $('.date-picker').datepicker('remove');
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                startDate:minDate,
                autoclose: true
            });

            handleMask();

            $modal.modal();
        });

        $('#submit-receipt').live('click', function(e){
            e.preventDefault();

            var min_receipt = parseFloat($('#min_receipt_amount').val()) || 0;

            var reservation_id = $('input[name="reservation_id"]').val();
            var receipt_date = $('input[name="receipt_date"]').val();
            var paymenttype_id = $('select[name="paymenttype_id"]').val();

            var is_credit_card = 0;
            var element = $('select[name="paymenttype_id"]').find('option:selected');
            var ptype = element.attr("payment-type");
            if(ptype == '<?php echo (PAYMENT_TYPE::CREDIT_CARD); ?>'){
                is_credit_card = 1;
            }

            var bankaccount_id = $('select[name="bankaccount_id"]').val();
            var receipt_amount = parseFloat($('input[name="receipt_amount"]').inputmask('unmaskedvalue')) || 0;
            var bank_fee = $('input[name="receipt_bank_fee"]').inputmask('unmaskedvalue');
            var veritrans_fee = $('input[name="receipt_veritrans_fee"]').inputmask('unmaskedvalue');
            var desc = $('textarea[name="bookingreceipt_desc"]').val();
            var ccard_name = $('input[name="creditcard_name"]').val();
            var ccard_no = $('input[name="creditcard_no"]').val();
            var expiry_month = parseInt($('select[name="creditcard_expiry_month"]').val()) || 0;
            var expiry_year = parseInt($('select[name="creditcard_expiry_year"]').val()) || 0;

            var valid = true;
            if(is_credit_card > 0){
                if(ccard_name == '' || ccard_no == '' || expiry_month <= 0 || expiry_year <= 0){
                    valid = false;
                    toastr["error"]("Credit Card Information must be filled.", "Warning");
                }
            }

            if(valid){
                if(receipt_amount >= 0){ //min_receipt
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('frontdesk/management/submit_booking_receipt');?>",
                        dataType: "json",
                        data: { reservation_id: reservation_id, receipt_date: receipt_date, paymenttype_id:paymenttype_id,
                            bankaccount_id:bankaccount_id, receipt_amount:receipt_amount,bank_fee:bank_fee,veritrans_fee:veritrans_fee,
                            desc:desc, creditcard_name : ccard_name, creditcard_no : ccard_no, is_primary_debtor : 1,
                            creditcard_expiry_month : expiry_month, creditcard_expiry_year : expiry_year, is_ar_transfer : false, is_invoice : 0}
                    })
                        .done(function( msg ) {
                            Metronic.unblockUI();
                            if(msg.type == '0' || msg.type == '1'){
                                //grid.getDataTable().ajax.reload();
                                //grid.clearAjaxParams();
                                if ( $.fn.dataTable.isDataTable('#table_reservation' )) {
                                    $('#table_reservation').dataTable().api().ajax.url("<?php echo base_url('frontdesk/reservation/get_reservation_manage/' . get_menu_id());?>").load();
                                }

                                if(msg.type == '1'){
                                    toastr["success"](msg.message, "Success");
                                }
                                else {
                                    toastr["warning"](msg.message, "Warning");
                                }

                                if(msg.document_link != ''){
                                    bootbox.confirm({
                                        message: "Print Official Receipt ?",
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
                                                window.open(msg.document_link, '_blank');
                                            }
                                        }
                                    });
                                }
                            }
                            else {
                                toastr["error"]("Action can not be processed, please try again later.", "Error");
                            }

                            resetReceiptForm();
                        });

                    $('#ajax-receipt').modal('hide');
                }else{
                    var maskedMinReceipt = parseFloat(min_receipt).formatMoney(0, '.', ',');
                    toastr["error"]("Receipt amount must not less than " + maskedMinReceipt, "Warning");
                }
            }
        });

        $('#cancel-receipt').live('click', function(e){
            resetReceiptForm();
        });

        $('.btn-check-vt').live('click', function(){
            var id = $(this).attr('data-id');
            var code = $(this).attr('data-code');

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('frontdesk/reservation/veritrans_status');?>",
                dataType: "json",
                data: { reservation_code: code}
            })
                .done(function( msg ) {
                    //Metronic.unblockUI();
                    if(msg.type == '0' || msg.type == '1'){
                        console.log(msg.message);
                    }
                    else {
                        toastr["error"]("Action can not be processed, please try again later.", "Error");
                    }
                });

        });
	});

    function resetReceiptForm(){
        $('select[name="paymenttype_id"]').removeAttr('disabled');
        //$('select[name="paymenttype_id"]').parent().children('.select2-container').children('.select2-choice').children('.select2-chosen').html('CCBC - Credit Card');
        //$('select[name="paymenttype_id"]').val($('input[name="cc_paymenttype_id"]').val());
        //$('select[name="bankaccount_id"] option').eq(1).prop('selected', true);

        //Set default hide or show Card Info
        var element = $('select[name="paymenttype_id"]').find('option:selected');
        var ptype = element.attr("payment-type");
        default_form_receipt(ptype);

        $('#min_receipt_amount').val('0');
        $('input[name="receipt_amount"]').val('0');
        $('textarea[name="bookingreceipt_desc"]').val('');
        $('input[name="creditcard_name"]').val('');
        $('input[name="creditcard_no"]').val('');
    }

    function default_form_receipt(ptype){
        var reservation_type = $('input[name="reservation_type"]').val();
        if(reservation_type == '<?php echo(RES_TYPE::CORPORATE)?>'){
            var debtor_type = $('input:radio[name="debtor_type"]:checked').val();
            //console.log('debtor_type ' + debtor_type);
            if(debtor_type > 0){
                $('#paymenttype_ar').removeClass('hide');
            }else{
                $('#paymenttype_ar').addClass('hide');
            }
        }else{
            $('#paymenttype_ar').addClass('hide');
        }

        if(ptype == '<?php echo PAYMENT_TYPE::CREDIT_CARD; ?>'){
            $('#card_info').removeClass('hide');
            $('#card_info_expiry').removeClass('hide');
            $('#bank_account_info').addClass('hide');

            //$(".mask_credit_card").inputmask("mask", {"mask": "9999-9999-9999-9999"});
        }else if(ptype == '<?php echo PAYMENT_TYPE::BANK_TRANSFER; ?>'){
            $('#bank_account_info').removeClass('hide');
            $('#card_info').addClass('hide');
            $('#card_info_expiry').addClass('hide');
        }else if(ptype == '<?php echo PAYMENT_TYPE::DEBIT_CARD; ?>'){
            $('#bank_account_info').addClass('hide');
            $('#card_info').removeClass('hide');
            $('#card_info_expiry').addClass('hide');

            //$(".mask_credit_card").inputmask();
        }else{
            $('#card_info').addClass('hide');
            $('#card_info_expiry').addClass('hide');
            $('#bank_account_info').addClass('hide');
        }
    }

</script>