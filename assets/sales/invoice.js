$('document').ready(function () {
    $(".mask_currency").inputmask("numeric", {
        radixPoint: ".",
        autoGroup: true,
        groupSeparator: ",",
        digits: 0,
        groupSize: 3,
        removeMaskOnSubmit: false,
        autoUnmask: true
    });
    $(".number_only").inputmask("numeric")
    var handleMask = function () {
        $(".mask_currency").inputmask("numeric", {
            radixPoint: ".",
            autoGroup: true,
            groupSeparator: ",",
            digits: 0,
            groupSize: 3,
            removeMaskOnSubmit: false,
            autoUnmask: true
        });
        $(".number_only").inputmask("numeric")
    }
    var grid_req = new Datatable();
    //get do number
    $("#inv-date").live('change', function () {
        var date = document.getElementById("inv-date").value;
        $.ajax({
                url: js_base_url + 'sales/Dp_invoice/generate_inv_no',
                method: "POST",
                dataType: "json",
                data: {date: date},
            })
            .done(function (msg) {
                document.getElementById("inv-code").value = msg.inv_number;
            })
    })
    //end of get do number


    //ajax modal customer
    var $modal = $('#customer-modal');
    $('#btn_lookup_customer').live('click', function (e) {
        e.preventDefault();

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';

        $('body').modalmanager('loading');


        setTimeout(function () {
            $modal.load(js_base_url + 'sales/dp_invoice/ajax_modal_customer', '', function () {

                    $modal.modal();
                    datatableCust();
                    $.fn.modalmanager.defaults.resize = true;
                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }
                    if ($modal.hasClass('modal-overflow') == false) {
                        $modal.addClass('modal-overflow');
                    }
                    $modal.css({'margin-top': '0px'})


                }
            )
            ;
        }, 100);
    });


    var datatableCust = function () {
        grid_req.init({
            src: $("#datatable_customer"),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function (grid) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Loading...',
            dataTable: {
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    {"bSortable": false},
                    null,
                    null,
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false, "sClass": "text-center"}
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": js_base_url + 'sales/dp_invoice/ajax_customer_list' // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }
    //end of ajax modal customer

    //get selected customer
    $('.select').live('click', function () {
        var id = $(this).attr('customer-id');
        var name = $(this).attr('customer-name');
        document.getElementById("customer-id").value = id;
        document.getElementById("customer-name").value = name;

        $('#customer-modal').modal('hide');
        $('#invoice_list_table_body').empty();
        document.getElementById("so-id").value = '';
        document.getElementById("so-code").value = '';

    })
    //end of seleceted customer

    //ajax modal so_no
    var $modal = $('#customer-modal');
    $('#btn_lookup_so').live('click', function (e) {
        var customer = $('.customer').val();
        if(customer == ''){
            toastr["warning"]("Please select customer first.", "Warning!");
        }else{
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');


            setTimeout(function () {
                $modal.load(js_base_url + 'sales/dp_invoice/ajax_modal_so', '', function () {

                        $modal.modal();
                        datatableSO();
                        $.fn.modalmanager.defaults.resize = true;
                        if ($modal.hasClass('bootbox') == false) {
                            $modal.addClass('modal-fix');
                        }
                        if ($modal.hasClass('modal-overflow') == false) {
                            $modal.addClass('modal-overflow');
                        }
                        $modal.css({'margin-top': '0px'})
                    }
                )
                ;
            }, 100);
        }

    });


    var datatableSO = function () {
        var cust_id = document.getElementById("customer-id").value;
        grid_req.init({
            src: $("#datatable_customer"),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function (grid) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Loading...',
            dataTable: {
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    {"bSortable": false},
                    {"bSortable": false, "sClass": "text-center"},
                    {"bSortable": false, "sClass": "text-center"},
                    {"bSortable": false, "sClass": "text-center"},
                    {"bSortable": false},
                    {"bSortable": false, "sClass": "text-center"}
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": js_base_url + 'sales/dp_invoice/ajax_so_list/' + cust_id, // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }
    //end of ajax modal so_no

    //get selected so
    $('.select-so').live('click', function () {
        var id = $(this).attr('so-id');
        var so_code = $(this).attr('so-code');
        var tax_percent = $(this).attr('tax-percent');
        document.getElementById("so-id").value = id;
        document.getElementById("so-code").value = so_code;
        $('#invoice_list_table_body').empty();
        $('#customer-modal').modal('hide');
        document.getElementById("vat").value = tax_percent;


        //get invoice type
        $.ajax({
            url: js_base_url + 'sales/dp_invoice/ajax_invoice_type/' + id,
            type: 'POST',
            dataType: 'JSON',
            data: {id: id},
            success: function (data) {
                $('#invoice-type').html(data);
            }
        })
        //end of invoice type
    })
    //end of seleceted so

    //get detail invoice
    $('#invoice-type').live('change', function () {
        $('.invoice_detail').removeClass('hide');
        var id = $("#invoice-type option:selected").val();
        var desc = $("#invoice-type option:selected").attr("desc");
        var amount = $("#invoice-type option:selected").attr("amount");
        var coa_id = $("#invoice-type option:selected").attr("coa-id");
        var so_id = document.getElementById("so-id").value;
        $('.coa_id').val(coa_id);
        if (id == 0) {//if DO Payment
            if ($('#table_do >tbody >tr').length > 0) {
                $('#table_do >tbody >tr').remove();
            }
            $('#table_payment >tbody >tr').remove();

            //go to add row invoice detail DO
            $('.tot_amount').val(0);
            $('.subtotal').html(0);
            $('.vat_percent').html(0);
            $('.grand_total').html(0);
            $('.do-tab').removeClass('hide').addClass('active');
            $('.payment-tab').removeClass('active').addClass('hide');
            //get payment type
            $.ajax({
                url: js_base_url + 'sales/dp_invoice/ajax_payment_type/' + so_id,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    $('#payment-list-table').html(data);
                }
            })
            //end of get payment type
        } else { //if not DO Payment
            $('.do-tab').removeClass('active').addClass('hide');
            $('.payment-tab').removeClass('hide').addClass('active');
            $('#table_payment tbody').empty();
            //get vat
            $.ajax({
                url: js_base_url + 'sales/dp_invoice/ajax_vat_list',
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    $('#vat-type').html(data);
                }
            })
            //end of get vat
            var newRowContent =
                '<tr>' +
                '<td>' +
                    '<input type=hidden class="form-control input-sm" name="desc"  value= "' + desc + '">' +
                    '<input type=hidden class="class_status" name="status_detail"  value= "1"> ' + desc + '' +
                '</td>' +
                '<td align="right" >' +
                    '<input type="text" name="tot_amount" class="form-control input-sm mask_currency" value=' + amount + ' readonly />' +
                '</td>' +
                '<td>' +
                    '<select id="vat-type" class="form-control form-filter input-sm select2me tax-select" name="vat_detail">' +
                    '</select>' +
                    '<input type="hidden" name="total_tax" class="form-control input-sm mask_currency total_tax" readonly/>' +
                '</td>' +
                '<td align="right"><input type="text" name="grand_total" class="form-control input-sm mask_currency tot_amount" readonly/>  </td>' +
                '</tr>';
            //Add row
            $('#table_payment tbody').append(newRowContent);
            $('select.select2me').select2();
            handleMask();
        }
    })
    //end of get detail invoice


    //sum tax
    $('#vat-type').live('change', function () {
        var tax_val = parseInt($("#vat-type option:selected").attr("tax-value")) || 0;
        var amount = parseFloat($("#invoice-type option:selected").attr("amount")) || 0;

        var tax = parseFloat((tax_val / 100) * amount) || 0;
        var tot_after_tax = parseFloat(tax + amount) || 0;
        $('.tot_amount').val(tot_after_tax);
        $('.total_tax').val(tax);

        //call to words
        $.ajax({
                url: js_base_url + 'sales/dp_invoice/ajax_into_words/' + tot_after_tax,
                type: 'POST',
                dataType: 'JSON',
            })
            .done(function (msg) {
                $('.into-words-inv').html(msg.in_words);
            })
        //end of call to words

    })
    //end of sum tax


    //add row invoice detail DO
    $('.select-do').live('click', function () {
        var id = $(this).attr('do-id');
        var code = $(this).attr('do-code');
        var desc = $(this).attr('desc');
        var amount = $(this).attr('amount');
        var i = $('#table_do tbody tr').length;
        var newRowContent =
            '<tr>' +
            '<td align="center"> ' +

            '<input type="hidden" class="form-control input-sm" name="do_id[' + i + ']"  value=' + id + ' readonly><input type=hidden class="class_status" name="status_detail[' + i + ']"  value= "1">' +
            ' ' + code + ' ' +
            '</td>' +
            '<td><input type="hidden" class="form-control input-sm" name="desc[' + i + ']"  value=' + code + ' readonly> Invoice ' + code + '</td>' +
            '<td align="right" ><input type="text"class="form-control input-sm mask_currency" name="amount[' + i + ']" id="amount" value=' + amount + ' readonly/></td>' +
            '<td align="center"><a class="btn btn-danger btn-xs tooltips btn-remove"><i class="fa fa-times"></i></a></td>' +
            '</tr>';
        $('#table_do tbody').append(newRowContent);
        $('select.select2me').select2();
        handleMask();
        calculate();
        calcualte_grand_total();
        $('#customer-modal').modal('hide');
    })
    //end of add row invoice detail DO

    function calculate() {
        var len = $('#table_do tbody tr').length;
        if (len > 0) {
            var tot_amount = 0;
            for (var i = 0; i < len; i++) {
                var status = parseInt($('input[name="status_detail[' + i + ']"]').val()) || 0;
                if (status == 1) {
                    var amount = parseFloat($('input[name="amount[' + i + ']"]').val()) || 0;
                    tot_amount += amount;
                    parseFloat($('.tot_amount').val(tot_amount)) || 0;
                }
            }
        }
    }

    function calcualte_grand_total() {
        //for payment type
        var len2 = $('#payment-list-table tr').length;
        if (len2 > 0) {
            var pt_tot_amount = 0;
            for (var i = 0; i < len2; i++) {
                var vat = parseFloat(parseFloat($('input[id="vat"]').val())) || 0;
                var tot_amount = parseFloat($('.tot_amount').val()) || 0;
                var desc = $('input[name="payment_type_desc[' + i + ']"]').val();
                var max = $('input[name="max_payment_type_amount[' + i + ']"]').val();
                var pt_amount = parseFloat($('input[name="payment_type_amount[' + i + ']"]').val()) || 0;
                if (pt_amount > max) {
                    toastr["warning"]("" + desc + " Cannot bigger than IDR " + max.replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ".", "Warning!");
                } else {
                    pt_tot_amount += pt_amount;
                }
            }
            var subtotal = parseFloat(tot_amount - pt_tot_amount) || 0;
            $('.subtotal').html(subtotal);
            $('.subtotal').val(subtotal);
            var tax = (vat / 100) * subtotal;
            $('.vat_percent').html(tax);
            $('.total_tax').val(tax);
            var grand_total = subtotal + tax;
            $('.grand_total').html(grand_total);
            $('.grand_total').val(grand_total);
            handleMask()

            //call to words
            $.ajax({
                    url: js_base_url + 'sales/dp_invoice/ajax_into_words/' + grand_total,
                    type: 'POST',
                    dataType: 'JSON',
                })
                .done(function (msg) {
                    $('.into-words-pymt').html(msg.in_words);
                })

            //end of call to words
        } else {

            var vat = parseFloat(parseFloat($('input[id="vat"]').val())) || 0;
            var tot_amount = parseFloat($('.tot_amount').val()) || 0;

            var subtotal = parseFloat(tot_amount) || 0;
            $('.subtotal').html(subtotal);
            $('.subtotal').val(subtotal);
            var tax = (vat / 100) * subtotal;
            $('.vat_percent').html(tax);
            $('.total_tax').val(tax);
            var grand_total = subtotal + tax;
            $('.grand_total').html(grand_total);
            $('.grand_total').val(grand_total);
            handleMask()

            //call to words
            $.ajax({
                    url: js_base_url + 'sales/dp_invoice/ajax_into_words/' + grand_total,
                    type: 'POST',
                    dataType: 'JSON',
                })
                .done(function (msg) {
                    $('.into-words-pymt').html(msg.in_words);
                })

            //end of call to words
        }
        //for payment type
    }

    $('.calcu').live('keyup', function () {
        calcualte_grand_total();
    });


    // remove item row
    $('.btn-remove').live('click', function () {
        var this_btn = $(this);
        bootbox.confirm("Are you sure want to delete?", function (result) {
            if (result == true) {
                this_btn.closest('tr').addClass('hide');
                this_btn.closest('tr').find('.class_status').val('9');
                $('.tot_amount').val(0);
                $('.total_tax').val(0);
                $('.subtotal').html(0);
                $('.vat_percent').html(0);
                $('.grand_total').html(0);
                calculate();
                calcualte_grand_total();
            }
        });
    });
//end of remove item row

    //ajax modal do list
    var $modal = $('#customer-modal');
    $('.btn_lookup_do_list').live('click', function (e) {
        e.preventDefault();

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';

        $('body').modalmanager('loading');


        setTimeout(function () {
            $modal.load(js_base_url + 'sales/dp_invoice/ajax_modal_do_list', '', function () {

                    $modal.modal();
                    datatableDOList();
                    $.fn.modalmanager.defaults.resize = true;
                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }
                    if ($modal.hasClass('modal-overflow') == false) {
                        $modal.addClass('modal-overflow');
                    }
                    $modal.css({'margin-top': '0px'})


                }
            )
            ;
        }, 100);
    });

    var datatableDOList = function () {
        var so_id = document.getElementById("so-id").value;
        grid_req.init({
            src: $("#datatable_customer"),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function (grid) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Loading...',
            dataTable: {
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    {"bSortable": false},
                    {"bSortable": false, "sClass": "text-center"},
                    {"bSortable": false, "sClass": "text-center"},
                    null,
                    {"bSortable": false, "sClass": "text-right"},
                    {"bSortable": false, "sClass": "text-center"}
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": js_base_url + 'sales/dp_invoice/ajax_do_invoice_list/' + so_id // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }
    //end of ajax modal do list


    function validate_submit() {
        var result = true;

        if ($('.form-group').hasClass('has-error')) {
            $('.form-group').removeClass('has-error');
        }
        if ($('td').hasClass('has-error')) {
            $('td').removeClass('has-error');
        }

        var inv_no = $('#inv-code').val();
        var inv_date = $('#inv-date').val();
        var cust = $('#customer-id').val();
        var so_id = $('#so-id').val();
        var due_date = $('#due-date').val();
        var inv_type = $('#invoice-type').val();

        if (inv_no == '') {
            toastr["warning"]("Please select Invoice Date to generate Invoice Date.", "Warning!");
            $('#inv-code').closest('.form-group').addClass('has-error');
            result = false;
        }

        if (inv_date <= 0) {
            toastr["warning"]("Please select invoice date.", "Warning!");
            $('#inv-date').closest('.form-group').addClass('has-error');
            result = false;
        }

        if (cust <= 0) {
            toastr["warning"]("Please select customer.", "Warning!");
            $('#customer-id').closest('.form-group').addClass('has-error');
            result = false;
        }

        if (so_id <= 0) {
            toastr["warning"]("Please select SO Number.", "Warning!");
            $('#so-id').closest('.form-group').addClass('has-error');
            result = false;
        }

        if (due_date <= 0) {
            toastr["warning"]("Please select due date.", "Warning!");
            $('#due-date').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (inv_type == '') {
            toastr["warning"]("Please select invoice type.", "Warning!");
            $('#invoice-type').closest('.form-group').addClass('has-error');
            result = false;
        }


        var is_detail_do = $('#table_do tbody tr').length;
        var is_detail_pymnt = $('#table_payment tbody tr').length;

        if (is_detail_do <= 0 && is_detail_pymnt <= 0) {
            toastr["warning"]("Detail cannot be empty.", "Warning");
            result = false;
        }

        if($('.payment-tab').is(":visible")){
            var vat = $('#vat-type').val();
            if (vat <= 0) {
                toastr["warning"]("Please select VAT.", "Warning!");
                $('#vat-type').closest('.form-group').addClass('has-error');
                result = false;
            }
        }

        return result
    }

    //    submit form
    $('#form-entry-inv').on('submit', function () {
        Metronic.blockUI({
            target: '#form-entry-inv',
            boxed: true,
            message: 'Processing...'
        });
        var btn = $(this).find("button[type=submit]:focus");


        if (validate_submit()) {
            var form_data = $('#form-entry-inv').serializeArray();
            if (btn[0] == null) {
            } else {
                if (btn[0].name === 'save_close') {
                    form_data.push({name: "save_close", value: 'save_close'});
                }
            }

            $.ajax({
                    type: "POST",
                    url: js_base_url + 'sales/dp_invoice/inv_entry',
                    dataType: "json",
                    data: form_data
                })
                .done(function (msg) {

                    if (msg.valid == '0' || msg.valid == '1') {
                        if (msg.valid == '1') {
                            window.location.assign(msg.link);
                        } else {
                            toastr["error"](msg.message, "Error");
                        }
                    } else {
                        toastr["error"]("Something has wrong, please try again later.", "Error");
                    }
                })
                .fail(function () {
                    $('#form-entry-inv').unblock();
                    toastr["error"]("Something has wrong, please try again later.", "Error");
                });
        } else {
            $('#form-entry-inv').unblock();
        }
    });
//    end of submit form




})

