//Request
$(document).ready(function () {
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
    //number format
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
    //end of number format


    var grid_req = new Datatable();
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
                    "url": js_base_url + 'sales/sales_order/ajax_customer_list' // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }
    var datatablePayment = function () {
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
                    {"bSortable": false, "sClass": "text-center"}
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": js_base_url + 'sales/sales_order/ajax_payment_list' // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }
    var datatableItems = function () {
        grid_req.init({
            src: $("#item-datatable"),
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
                    null,
                    {"bSortable": false, "sClass": "text-center"},
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
                    "url": js_base_url + 'sales/sales_order/ajax_items_list' // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }


//get selected customer
    $('.select').live('click', function () {
        var id = $(this).attr('customer-id');
        var name = $(this).attr('customer-name');
        document.getElementById("customer_id").value = id;
        document.getElementById("customer_name").value = name;

        document.getElementById("delivery-address-id").value = '';
        document.getElementById("delivery-address").value = '';

        document.getElementById("invoice-address-id").value = '';
        document.getElementById("invoice-address").value = '';

        $('#customer-modal').modal('hide');
    })
//end of seleceted customer

//get delivery address
    $('.delivery-address').live('click', function () {
        var id = document.getElementById("customer_id").value;
        var name = document.getElementById("customer_name").value;
        if (id == '') {
            toastr["warning"]("Please select customer.", "Warning!");
        } else {
            $.ajax({
                url: js_base_url + 'sales/sales_order/get_delivery_address',
                method: "POST",
                data: {
                    id
                },
                success: function (data) {
                    $('#address-list').html(data);
                    document.getElementById("header-title").innerHTML = name;
                    $('#delivery-address-modal').modal("show");
                }
            })
        }
    })

    $('.select-address').live('click', function () {
        var address_id = $(this).attr('address-id');
        var address_name = $(this).attr('address-name');
        document.getElementById("delivery-address-id").value = address_id;
        document.getElementById("delivery-address").value = address_name;
        $('#delivery-address-modal').modal("hide");
    })
//end of delivery address

//get invoice address
    $('.invoice-address').live('click', function () {
        var id = document.getElementById("customer_id").value;
        var name = document.getElementById("customer_name").value;
        if (id == '') {
            toastr["warning"]("Please select customer.", "Warning!");
        } else {
            $.ajax({
                url: js_base_url + 'sales/sales_order/get_invoice_address',
                method: "POST",
                data: {
                    id
                },
                success: function (data) {
                    $('#address-list').html(data);
                    document.getElementById("header-title").innerHTML = name;
                    $('#delivery-address-modal').modal("show");
                }
            })
        }
    })

    $('.select-invoice').live('click', function () {
        var address_id = $(this).attr('address-id');
        var address_name = $(this).attr('address-name');
        document.getElementById("invoice-address-id").value = address_id;
        document.getElementById("invoice-address").value = address_name;
        $('#delivery-address-modal').modal("hide");
    })
//end of invoice address


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
            $modal.load(js_base_url + 'sales/sales_order/ajax_modal_customer', '', function () {

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
//end of ajax modal customer

//ajax modal payment
    $('#btn_lookup_payment').live('click', function (e) {
        e.preventDefault();
        if ($('.total_amount').val() == '' || $('.total_amount').val() == 0) {
            toastr["warning"]("Please select item first and total amount can not zero.", "Warning!");
        } else {
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');


            setTimeout(function () {
                $modal.load(js_base_url + 'sales/sales_order/ajax_modal_payment', '', function () {

                        $modal.modal();
                        datatablePayment();
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
//end of ajax modal payment


//get selected payment list
    var tbody = $('#payment-list-table').children('tbody')
    var table = tbody.length ? tbody : $('#payment-list-table');
    $('.select-payment').live('click', function () {
        var sp_type_id = $(this).attr('sp-type-id');
        var sp_type_name = $(this).attr('sp-type-name');
        var a = $('#payment_list_table tbody tr').length;

        var newRowContent =
            '<tr>' +
            '<td> <input type=hidden name="sp_type_id[' + a + ']"  value= ' + sp_type_id + '><input type=hidden class="class_status" name="status_sp_type[' + a + ']"  value= "1">  ' + sp_type_name + ' </td>' +
            '<td> <input type="text" name="sp_type_amount[' + a + ']" class="form-control input input-sm payment mask_currency calcu"  min="0" > </td>' +
            '<td> <button type="button" class="btn btn-xs btn-danger remove-payment" ><i class="fa fa-remove"></i></button></td>' +
            '</tr>';
        $('#payment_list_table tbody').append(newRowContent);
        handleMask();
        $('#customer-modal').modal('hide');
    })
//end of get selected payment list

// remove item row
    $('.remove-payment').live('click', function () {
        var this_btn = $(this);
        bootbox.confirm("Are you sure want to delete?", function (result) {
            if (result == true) {
                this_btn.closest('tr').addClass('hide');
                this_btn.closest('tr').find('.class_status').val('9');
                calculate_tot_amount();
            }
        });
    });
//end of remove item row

//get so number
    $("#so-date").live('change', function () {
        var date = document.getElementById("so-date").value;
        $.ajax({
                url: js_base_url + 'sales/sales_order/generate_so_no',
                method: "POST",
                dataType: "json",
                data: {date},
            })
            .done(function (msg) {
                document.getElementById("so-no").value = msg.so_number;
            })
    })
//end of get so number


//ajax modal items
    var $modal = $('#customer-modal');
    $('#btn_lookup_items').live('click', function (e) {
        e.preventDefault();
        if ($('.customer').val() == '') {
            toastr["warning"]("Please select customer first.", "Warning!");
        } else {
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');
            setTimeout(function () {
                $modal.load(js_base_url + 'sales/sales_order/ajax_modal_items', '', function () {

                        $modal.modal();
                        datatableItems();
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
//end of ajax modal items


//get selected item list
    var tbody = $('#item_list_table').children('tbody')
    var table = tbody.length ? tbody : $('#item_list_table');
    $('.select-item').live('click', function () {
        var item_id = $(this).attr('item-id');
        var description = $(this).attr('description');
        var item_code = $(this).attr('item-code');
        var uom = $(this).attr('uom');
        var uom_id = $(this).attr('uom-id');
        var price = $(this).attr('price');
        var i = $('#item_list_table tbody tr').length;
        var newRowContent =
            '<tr>' +
            '<td align="center"> <input type=hidden name="item_id[' + i + ']"  value= ' + item_id + '> <input type=hidden class="class_status" name="status_detail[' + i + ']"  value= "1">  ' + item_code + ' </td>' +
            '<td> ' + description + ' </td>' +
            '<td> <input type="text" name="price[' + i + ']" class="form-control input-sm text-right calcu mask_currency" value = ' + price + ' > </td>' +
            '<td> <input type="text" name="qty[' + i + ']" class="form-control number_only input-sm text-right calcu qty"> </td>' +
            '<td align="center"> <input type="hidden" name="uom_id[]" class="form-control input input-sm text-right" value=' + uom_id + '> ' + uom + ' </td>' +
            '<td> <input type="text" name="discount[' + i + ']" class="form-control input input-sm text-right mask_currency calcu disc" value="0"> </td>' +
            '<td><input type="text" name="amount[' + i + ']"  class="form-control input input-sm text-right amount mask_currency" readonly value="0"></td>' +
            '<td align="center"> <button type="button" class="btn btn-xs btn-danger remove-item" item-id = ' + item_id + ' ><i class="fa fa-remove"></i></button></td>' +
            '</tr>';

        //Add row
        $('#item_list_table tbody').append(newRowContent);
        handleMask();
        $('#customer-modal').modal('hide');
    })
//end of get selected item list


// remove item row
    $('.remove-item').live('click', function () {
        var this_btn = $(this);
        bootbox.confirm("Are you sure want to delete?", function (result) {
            if (result == true) {
                this_btn.closest('tr').addClass('hide');
                this_btn.closest('tr').find('.class_status').val('9');
                calculate_tot_amount();

            }
        });
    });
//end of remove item row

//    validate form
    function validate_submit() {
        var result = true;

        if ($('.form-group').hasClass('has-error')) {
            $('.form-group').removeClass('has-error');
        }
        if ($('td').hasClass('has-error')) {
            $('td').removeClass('has-error');
        }

        var customer = $('#customer_id').val();
        var req_do_date = $('#req_do_date').val();
        var sales = $('#sales').val();
        var delivery_address_id = $('#delivery-address-id').val();
        var invoice_address_id = $('#invoice-address-id').val();
        var vat = $('#vat-option').val();
        if (customer <= 0) {
            toastr["warning"]("Please select customer.", "Warning!");
            $('#customer_id').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (req_do_date <= 0) {
            toastr["warning"]("Please fill Request DO Date.", "Warning!");
            $('#req_do_date').closest('.form-group').addClass('has-error');
            result = false;
        }

        if (sales <= 0) {
            toastr["warning"]("Please select sales.", "Warning!");
            $('#sales').closest('.form-group').addClass('has-error');
            result = false;
        }

        if (delivery_address_id <= 0) {
            toastr["warning"]("Please select delivery address.", "Warning!");
            $('#delivery-address-id').closest('.form-group').addClass('has-error');
            result = false;
        }

        if (invoice_address_id <= 0) {
            toastr["warning"]("Please select invoice address.", "Warning!");
            $('#invoice-address-id').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (vat <= 0) {
            toastr["warning"]("Please select VAT.", "Warning!");
            $('#vat-option').closest('.form-group').addClass('has-error');
            result = false;
        }
        var i = 0;
        var i_act = 0;
        $('#item_list_table > tbody > tr ').each(function () {
            if (!$(this).hasClass('hide')) {
                var price = parseInt($('input[name="price[' + i + ']"]').val()) || 0;
                var qty = parseInt($('input[name="qty[' + i + ']"]').val()) || 0;

                if (price <= 0) {
                    toastr["warning"]("Please fill price.", "Warning");
                    $('input[name="price[' + i + ']"]').closest('td').addClass('has-error');
                    result = false;
                }
                if (qty <= 0) {
                    toastr["warning"]("Please fill qty.", "Warning");
                    $('input[name="qty[' + i + ']"]').closest('td').addClass('has-error');
                    result = false;
                }
                i_act++;
            }
            i++;
        });

        if (i_act <= 0) {
            toastr["warning"]("Detail cannot be empty.", "Warning");
            result = false;
        }
        var a = 0;
        $('#payment_list_table > tbody > tr ').each(function () {

            if (!$(this).hasClass('hide')) {
                var sp_type_amount = $('input[name="sp_type_amount[' + a + ']"]').val();

                if (sp_type_amount == '') {
                    toastr["warning"]("Please fill payment type amount.", "Warning");
                    $('input[name="sp_type_amount[' + a + ']"]').closest('td').addClass('has-error');
                    result = false;
                }
            }
            a++;
        });
        return result;
    }

//    end of validate form

//    submit form
    $('#form-entry-so').on('submit', function () {
        Metronic.blockUI({
            target: '#form-entry-so',
            boxed: true,
            message: 'Processing...'
        });
        var btn = $(this).find("button[type=submit]:focus");


        if (validate_submit()) {
            var form_data = $('#form-entry-so').serializeArray();
            if (btn[0] == null) {
            } else {
                if (btn[0].name === 'save_close') {
                    form_data.push({name: "save_close", value: 'save_close'});
                }
            }

            $.ajax({
                    type: "POST",
                    url: js_base_url + 'sales/Sales_order/so_entry',
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
                    $('#form-entry-so').unblock();
                    toastr["error"]("Something has wrong, please try again later.", "Error");
                });
        } else {
            $('#form-entry-so').unblock();
        }
    });
//    end of submit form

})

// calculate amount
function calculate_tot_amount() {
    var len = $('#item_list_table tbody tr').length;
    if (len > 0) {
        var tot_amount = 0;
        for (var i = 0; i < len; i++) {
            var status = parseInt($('input[name="status_detail[' + i + ']"]').val()) || 0;
            if (status == 1) {
                var qty = parseFloat($('input[name="qty[' + i + ']"]').val()) || 0;
                var prc = parseFloat($('input[name="price[' + i + ']"]').val()) || 0;
                var disc = parseFloat($('input[name="discount[' + i + ']"]').val()) || 0;
                var amnt = (qty * prc) - disc;
                $('input[name="amount[' + i + ']"]').val(amnt);
                tot_amount += amnt;
            }
        }
        $('.total_amount').val(tot_amount);
        $('.do_payment').val(tot_amount);
    }
    var vat = parseInt($("#vat-option option:selected").attr("tax-value")) || 0; //get selected tax
    var tax = tot_amount * (vat / 100);
    $('.tax').val(tax); //for tax
    var tot_after_tax = tot_amount + (tot_amount * (vat / 100));
    $('.total_amount_after_tax').val(tot_after_tax); //for total amount after tax

    //for sales payment type
    var len2 = $('#payment_list_table tbody tr').length;
    if (len2 > 0) {
        var sp_type_tot_amount = 0;
        for (var a = 0; a < len2; a++) {
            var status = parseInt($('input[name="status_sp_type[' + a + ']"]').val()) || 0;
            if (status == 1) {
                var sp_type_amount = parseFloat($('input[name="sp_type_amount[' + a + ']"]').val()) || 0;
                sp_type_tot_amount += sp_type_amount;
            }
        }
        $('.do_payment').val(tot_amount - sp_type_tot_amount);

    }
    //end of for sales payment type
}

$('.calcu').live('keyup change', function () {
    calculate_tot_amount();
});
//end of calculate amount