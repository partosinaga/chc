/**
 * Created by Hendhi on 1/14/2016.
 */
var FormJS = function () {
    var params;
    var grid1 = new Datatable();

    var handleValidation = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation
        $.validator.addMethod("validateBankAccount", function (value, element)
            {
                var valid = true;
                try{
                    var bankaccount_id = parseInt($('select[name="bankaccount_id"]').val()) || 0;

                    var element = $('select[name="paymenttype_id"]').find('option:selected');
                    var ptype = element.attr("payment-type");

                    if( ptype == params.ptype_bank_transfer ||
                        ptype == params.ptype_debit_card ||
                        ptype == params.ptype_cash_only){
                        if(bankaccount_id <= 0){
                            valid = false;
                        }
                    }
                }catch(e){
                    console.log(e);
                    valid = false;
                }

                return valid;
            },
            "Bank Account must be selected."
        )

        $.validator.addMethod("validateInvoice", function (value, element)
            {
                var valid = true;
                try{
                    var isInvoice = $('input[name="is_invoice"]').val();
                    var allocated = parseFloat($('#payment_amount').val()) || 0;
                    var min_allocated = parseFloat($('input[name="min_amount"]').val()) || 0;

                    /*
                    if(isInvoice > 0){
                        var checked = $('input[name="inv_id[]"]:checked').length; //$(this).find
                        if(checked <= 0){
                            valid = false;
                            toastr["error"]("At least 1(one) invoice must be selected.", "Warning");
                        }


                        if(valid) {
                            if (allocated <= 0) {
                                valid = false;
                                toastr["error"]("At least 1(one) invoice must be selected.", "Warning");
                            }
                        }
                    }
                    */

                    if(allocated < min_allocated){
                        valid = false;
                        var maskedMinReceipt = parseFloat(min_allocated).formatMoney(0, '.', ',');
                        toastr["error"]("Payment amount must not less than " + maskedMinReceipt, "Warning");
                    }
                }catch(e){
                    console.log(e);
                    valid = false;
                    toastr["error"]("Invoice must be selected.", "Warning");
                }

                return valid;
            },
            "Invoice must be selected."
        )

        $.validator.addMethod("validateCheckID", function (value, element)
            {
                var valid = true;
                try{
                    var company_id = parseFloat($('input[name="company_id"]').val()) || 0;
                    var reservation_id = parseFloat($('input[name="reservation_id"]').val()) || 0;

                    if(company_id <= 0 && reservation_id <= 0){
                        valid = false;
                        toastr["error"]("Please choose company or guest.", "Warning");
                    }
                }catch(e){
                    console.log(e);
                    valid = false;
                    toastr["error"]("Please choose company or guest.", "Warning");
                }

                return valid;
            },
            "Please choose company or guest."
        )

        $.validator.addMethod("validateAlloc", function (value, element)
            {
                var valid = true;
                try{
                    var alloc_amount = parseFloat($('input[name="total_allocated"]').val()) || 0;
                    var available_amount = parseFloat($('input[name="payment_amount"]').val()) || 0;

                    if(alloc_amount > available_amount){
                        valid = false;
                        toastr["error"]("Allocation amount must not bigger than Unallocated", "Warning");
                        $('input[name="total_allocated"]').closest('.form-group').removeClass("has-success").addClass('has-error');
                        $('input[name*="alloc_amount"]').removeClass("has-success").addClass('has-error');
                    }

                }catch(e){
                    console.log(e);
                    valid = false;
                }

                return valid;
            }
        )

        var form1 = $('#form-entry');
        form1.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",  // validate all fields including form hidden input
            rules: {
                receipt_date: {
                    required: true
                },
                company_id:{
                    validateCheckID: true
                },
                bankaccount_id:{
                    validateBankAccount:true
                },
                remark: {
                    required: true
                },
                payment_amount:{
                    required: true,
                    min: 1
                },
                total_allocated_valid :{
                    validateInvoice : true,
                    validateAlloc : true
                }
            },
            messages: {
                receipt_date: "Date must be selected",
                bankaccount_id: "Bank Account must be selected",
                remark: "Remark must not empty",
                payment_amount: "Payment amount must be filled"
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                Metronic.scrollTo(form1, -200);

                if(validator.invalid.receipt_date != null){
                    toastr["error"](validator.invalid.receipt_date, "Warning");
                }
                if(validator.invalid.bankaccount_id != null){
                    toastr["error"](validator.invalid.bankaccount_id, "Warning");
                }
                if(validator.invalid.remark != null){
                    toastr["error"](validator.invalid.remark, "Warning");
                }
                if(validator.invalid.payment_amount != null){
                    toastr["error"](validator.invalid.payment_amount, "Warning");
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

    var handleMask = function() {
        $(".mask_currency").inputmask("numeric",{
            radixPoint:".",
            autoGroup: true,
            groupSeparator: ",",
            digits: 0,
            groupSize: 3,
            removeMaskOnSubmit: true,
            autoUnmask: true
        });

        $(".mask_credit_card").inputmask("mask", {"mask": "9999-9999-9999-9999"});
    }

    var handleTableModal = function (num_index, company_id, receipt_id, reservation_id) {
        // Start Datatable Item
        grid1.init({
            src: $("#datatable_modal"),
            loadingMessage: 'Populating...',
            dataTable: {
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    null,
                    { "sWidth" : '15%' ,"sClass": "text-center", "bSortable" : false},
                    //{ "sWidth" : '13%' ,"sClass": "text-right", "bSortable" : false},
                    { "sWidth" : '18%' ,"sClass": "text-left", "bSortable" : false},
                    { "bSortable": false, "sClass": "text-center", "sWidth" : '11%' }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, -1],
                    [10, 20, 50, 100, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": params.reservation_ajax_url + "/" + num_index + "/" + company_id + "/" + receipt_id + "/" + reservation_id // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_modal_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        }); // initialize select2 dropdown

        $('select.select2me').select2();

        // End Datatable Item
    }

    var formEvents = function(){
        if(params.is_edit > 0){
            $('#form-entry').block({
                message: null ,
                overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
            });
        }

        $('#btn_lookup_company').on('click', function(){
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
                $modal.load(params.lookup_company_ajax_url, '', function () {
                    $modal.modal();

                    var receipt_id = $('input[name="receipt_id"]').val();
                    var company_id = $('input[name="company_id"]').val();
                    var reservation_id = $('input[name="reservation_id"]').val();
                    handleTableModal(num_index, company_id, receipt_id, reservation_id);

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

        $('.btn-select-record').live('click', function (e) {
            e.preventDefault();

            var company_id = parseInt($(this).attr('data-company-id')) || 0;
            var reservation_id = parseInt($(this).attr('data-reservation-id')) || 0;
            var company_name = $(this).attr('data-company-name');
            var amount = parseFloat($(this).attr('data-amount')) || 0;
            var is_invoice = parseInt($(this).attr('data-is-invoice')) || 0;

            $('input[name="company_id"]').val(company_id);
            $('input[name="reservation_id"]').val(reservation_id);
            $('input[name="company_name"]').val(company_name);
            $('input[name="is_invoice"]').val(is_invoice);
            $('input[name="pending_amount"]').val(amount);
            if(is_invoice > 0){
                $('input[name="payment_amount"]').val(0);
                $('input[name="min_amount"]').val(0);
            }else{
                $('input[name="payment_amount"]').val(amount);
                $('input[name="min_amount"]').val(amount);
            }

            if(is_invoice > 0){
                $('#panel_detail').removeClass('hide');
            }else{
                $('#panel_detail').addClass('hide');
            }

            //Looking Bills
            $.ajax({
                type: "POST",
                url: js_base_url + "ar/corporate_bill/xcorp_pending_invoice", //params.lookup_pending_invoice_ajax_url,
                data: { company_id : company_id, reservation_id : reservation_id},
                async:false
            })
                .done(function( msg ) {
                    //console.log(msg);
                    $('#table_pending_detail > tbody').html(msg);
                });

            handleMask();
            //$.uniform.update();
            Metronic.initUniform();

            $('#ajax-modal').modal('hide');
        });

        $('select[name="paymenttype_id"]').on('change', function (e) {
            var element = $(this).find('option:selected');
            var ptype = element.attr("payment-type");
            default_form_receipt(ptype);
            $('input[name="creditcard_name"]').val('');
            $('input[name="creditcard_no"]').val('');
        });

        $('input[name*="inv_id"]').live('click', function(){
            /*
            var isChecked = $(this).is(':checked');
            var tr = $(this).closest('tr');
            var paymentAmount = parseFloat($('#payment_amount').val()) || 0;

            if(isChecked){
                var pendingAmount = parseFloat(tr.find('input[name*="pending_amount"]').val()) || 0;
                var amount = (paymentAmount + pendingAmount);
                $('#payment_amount').val(amount);
                $('input[name="min_amount"]').val(amount);
            }else{
                var pendingAmount = parseFloat(tr.find('input[name*="pending_amount"]').val()) || 0;
                var amount = (paymentAmount - pendingAmount);
                if(amount < 0){
                     amount = 0;
                }
                $('input[name="min_amount"]').val(amount);
                $('#payment_amount').val(amount);
            }
            */
        });

        $('.add_amount').live('click', function(e){
            e.preventDefault();

            var availableAmount = parseFloat($('input[name="payment_amount"]').val());
            var alloc = parseFloat($('input[name="total_allocated"]').val());

            var tr = $(this).closest('tr');
            var credit = tr.find('input[name*="alloc_amount"]').val();
            var base_amount = tr.find('input[name*="base_amount"]').val();

            if(credit <= 0){
                availableAmount = availableAmount - alloc;
                var maxAllocated = base_amount;
                if(availableAmount < maxAllocated){
                    maxAllocated = availableAmount;
                }

                tr.find('input[name*="alloc_amount"]').val(maxAllocated);
                show_plus_button(false, tr);
            }else{
                tr.find('input[name*="alloc_amount"]').val(0);
                show_plus_button(true, tr);
            }

            handleMask();
        });

        $('#btn_generate').live('click', function(e){
            e.preventDefault();

            var show_plus = true;

            $('input[name*="alloc_amount"]').val(0);

            var availableAmount = parseFloat($('input[name="payment_amount"]').val());
            var credit = parseFloat($('input[name="total_allocated"]').val());
            $('#table_pending_detail > tbody > tr ').each(function() {
                if(availableAmount > 0) {
                    var tr = $(this).closest('tr');
                    var base_amount = parseFloat(tr.find('input[name*="base_amount"]').val());

                    if (credit <= 0) {
                        if(base_amount <= availableAmount){
                            tr.find('input[name*="alloc_amount"]').val(base_amount);
                        }else{
                            tr.find('input[name*="alloc_amount"]').val(availableAmount);
                        }

                        availableAmount = (availableAmount  - base_amount);

                        tr.find('.add_amount_minus').removeClass('hide');
                        tr.find('.add_amount_plus').addClass('hide');
                    } else {
                        tr.find('input[name*="alloc_amount"]').val(0);

                        tr.find('.add_amount_minus').addClass('hide');
                        tr.find('.add_amount_plus').removeClass('hide');
                    }
                }
            });

            calculateCredit();
            handleMask();
        });

        function calculateCredit(){
            var total = 0;
            $('#table_pending_detail > tbody > tr ').each(function() {
                var credit_amount = parseFloat($(this).find('input[name*="alloc_amount"]').val()) || 0;
                total += credit_amount;
            });
            $('input[name="total_allocated"]').val(total);
        }

        $('input[name="alloc_amount[]"]').live('keyup', function(e){
            e.preventDefault();
            var baseAmount = parseFloat($(this).closest('tr').find('input[name*="base_amount"]').val()) || 0;
            var val = parseFloat($(this).val()) || 0;

            var tr = $(this).closest('tr');
            if(val > 0){
                if(val > baseAmount){
                    $(this).val(baseAmount);
                }
                //$('.add_amount').removeClass('hide');
                show_plus_button(false,tr);
            }else{
                //$('.add_amount').addClass('hide');
                show_plus_button(true,tr);
            }
        });

        function show_plus_button(valid, parent_tr){
            if(valid){
                parent_tr.find('.add_amount_minus').addClass('hide');
                parent_tr.find('.add_amount_plus').removeClass('hide');

            }else{
                parent_tr.find('.add_amount_plus').addClass('hide');
                parent_tr.find('.add_amount_minus').removeClass('hide');
            }

            calculateCredit();
        }

        function validate_input(){
            var valid = true;

            if(valid){
                valid = $("#form-entry").valid();
            }

            return valid;
        }

        $('button[name="save"]').click(function(e){
            e.preventDefault();

            if(validate_input()){
                var url = params.save_ajax_url;
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
            }
        });

        $('#submit-posting').click(function(e) {
            e.preventDefault();

            $('#ajax-posting').modal('hide');

            var receipt_id = parseFloat($('input[name="receipt_id"]').val()) || 0;
            if(receipt_id > 0){
                bootbox.confirm({
                    message: "Posting this Official Receipt ?<br><strong>Please make sure any changes has been saved.</strong>",
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
                            //console.log(result);
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                type: "POST",
                                url: params.posting_ajax_url ,
                                dataType: "json",
                                data: { receipt_id: receipt_id}
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();

                                    if(msg.type == '1'){
                                        toastr["success"](msg.message, "Success");
                                        if(msg.redirect_link != ''){
                                            window.location.assign(msg.redirect_link);
                                        }
                                    }
                                    else {
                                        toastr["warning"](msg.message, "Warning");
                                    }
                                });

                        }
                    }
                });
            }
        });

        function default_form_receipt(ptype){
            if(ptype == params.ptype_credit_card){
                $('#card_info').removeClass('hide');
                $('#card_info_expiry').removeClass('hide');
                $('#bank_account_info').addClass('hide');
                $('.option_bank').addClass('hide');
                $('.option_cash').addClass('hide');

                $('select[name="bankaccount_id"]').select2("val", "");
            }else if(ptype == params.ptype_bank_transfer){
                $('#bank_account_info').removeClass('hide');
                $('#card_info').addClass('hide');
                $('#card_info_expiry').addClass('hide');
                $('.option_bank').removeClass('hide');
                $('.option_cash').addClass('hide');

                var bank_id = $('select[name="bankaccount_id"]').find('option[class="option_bank"]').val();
                $('select[name="bankaccount_id"]').select2("val", bank_id);

            }else if(ptype == params.ptype_debit_card){
                $('#bank_account_info').removeClass('hide');
                $('#card_info').removeClass('hide');
                $('#card_info_expiry').addClass('hide');
                $('.option_bank').removeClass('hide');
                $('.option_cash').addClass('hide');

                var bank_id = $('select[name="bankaccount_id"]').find('option[class="option_bank"]').val();
                $('select[name="bankaccount_id"]').select2("val", bank_id);
            }else if(ptype == params.ptype_cash_only){
                $('#bank_account_info').removeClass('hide');
                $('#card_info').addClass('hide');
                $('#card_info_expiry').addClass('hide');
                $('.option_bank').addClass('hide');
                $('.option_cash').removeClass('hide');

                var bank_id = $('select[name="bankaccount_id"]').find('option[class="option_cash"]').val();
                $('select[name="bankaccount_id"]').select2("val", bank_id);
            }else{
                $('#card_info').addClass('hide');
                $('#card_info_expiry').addClass('hide');
                $('#bank_account_info').addClass('hide');
                $('.option_bank').addClass('hide');
                $('.option_cash').addClass('hide');

                $('select[name="bankaccount_id"]').select2("val", "");
            }
        }
    }

    return {
        //main function to initiate the module
        init: function (options) {
            params = options;

            handleMask();
            formEvents();
            handleValidation();
        }

    };
}();

