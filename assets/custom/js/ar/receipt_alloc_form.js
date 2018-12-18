/**
 * Created by Hendhi on 1/14/2016.
 */
var FormJS = function () {
    var params;
    var grid1 = new Datatable();

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
    }

    var handleValidation = function() {
        // for more info visit the official plugin documentation:
        // http://docs.jquery.com/Plugins/Validation
        $.validator.addMethod("validateAlloc", function (value, element)
            {
                var valid = true;
                try{
                    var alloc_amount = parseFloat($('input[name="total_allocated"]').val()) || 0;
                    var available_amount = parseFloat($('input[name="available_amount"]').val()) || 0;

                    if(alloc_amount <= 0){
                        valid = false;
                        toastr["error"]("Allocation amount must not empty", "Warning");
                    }

                    if(alloc_amount > available_amount){
                        valid = false;
                        toastr["error"]("Allocation amount must not bigger than Unallocated", "Warning");
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
                alloc_date: {
                    required: true
                },
                receipt_id:{
                    required: true
                },
                alloc_desc: {
                    required: true
                },
                total_allocated_valid:{
                    validateAlloc : true
                }
            },
            messages: {
                alloc_date: "Date must be selected",
                receipt_id: "Official Receipt must be selected",
                alloc_desc: "Remark must not empty"
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                Metronic.scrollTo(form1, -200);

                if(validator.invalid.alloc_date != null){
                    toastr["error"](validator.invalid.alloc_date, "Warning");
                }
                if(validator.invalid.receipt_id != null){
                    toastr["error"](validator.invalid.receipt_id, "Warning");
                }
                if(validator.invalid.alloc_desc != null){
                    toastr["error"](validator.invalid.alloc_desc, "Warning");
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

    var handleTableModal = function (num_index, receipt_id, allocationheader_id) {
        // Start Datatable Item
        grid1.init({
            src: $("#datatable_reservation"),
            loadingMessage: 'Populating...',
            dataTable: {
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    { "sWidth" : '12%' ,"sClass": "text-center"},
                    null,
                    { "sWidth" : '15%' ,"sClass": "text-right", "bSortable": false},
                    //{ "sWidth" : '25%' ,"bSortable": false},
                    { "bSortable": false, "sClass": "text-center", "sWidth" : '12%' }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, -1],
                    [10, 20, 50, 100, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": params.modal_ajax_url + "/" + num_index + "/" + receipt_id + "/" + allocationheader_id // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_reservation_wrapper");

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

                    var allocationheader_id = $('input[name="allocationheader_id"]').val();
                    var receipt_id = $('input[name="receipt_id"]').val();
                    handleTableModal(num_index, receipt_id, allocationheader_id);

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

        $('.btn-select-receipt').live('click', function (e) {
            e.preventDefault();

            var receipt_id = parseInt($(this).attr('data-receipt-id')) || 0;
            var receipt_no = $(this).attr('data-receipt-no');
            var company_id = parseInt($(this).attr('data-company-id')) || 0;
            var reservation_id = parseInt($(this).attr('data-reservation-id')) || 0;
            var company_name = $(this).attr('data-company-name');
            var amount = $(this).attr('data-amount') || 0;

            $('input[name="receipt_id"]').val(receipt_id);
            $('input[name="company_id"]').val(company_id);
            $('input[name="reservation_id"]').val(reservation_id);
            $('input[name="available_amount"]').val(amount);
            $('input[name="total_allocated"]').val(0);
            $('input[name="receipt_no"]').val(receipt_no);
            $('#company_name').html(company_name);

            //Looking Reservation Detail
            $.ajax({
                type: "POST",
                url: params.lookup_bill_detail_ajax_url,
                data: { company_id : company_id, reservation_id : reservation_id},
                async:false
            })
                .done(function( msg ) {
                    //console.log(msg);
                    $('#table_pending_detail > tbody').html(msg);
                });

            handleMask();

            $('#ajax-modal').modal('hide');
        });

        $('.add_amount').live('click', function(e){
            e.preventDefault();

            var availableAmount = parseFloat($('input[name="available_amount"]').val());
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

                //tr.find('input[name*="alloc_amount"]').val(base_amount);
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

            var availableAmount = parseFloat($('input[name="available_amount"]').val());
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

            var alloc_id = parseFloat($('input[name="allocationheader_id"]').val()) || 0;
            if(alloc_id > 0){
                bootbox.confirm({
                    message: "Posting this Allocation ?<br><strong>Please make sure any changes has been saved.</strong>",
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
                                url: params.posting_ajax_url,
                                dataType: "json",
                                data: { allocationheader_id: alloc_id}
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

