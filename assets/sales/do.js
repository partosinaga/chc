$(document).ready(function () {
    $(".number_only").inputmask("numeric");
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
    $("#do-date").live('change', function () {
        var date = document.getElementById("do-date").value;
        $.ajax({
                url: js_base_url + 'sales/delivery_order/generate_do_no',
                method: "POST",
                dataType: "json",
                data: {date},
            })
            .done(function (msg) {
                document.getElementById("do-code").value = msg.do_number;
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
            $modal.load(js_base_url + 'sales/delivery_order/ajax_modal_customer', '', function () {

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
                    "url": js_base_url + 'sales/sales_order/ajax_customer_list' // ajax source
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

        document.getElementById("so-id").value = '';
        document.getElementById("so-code").value = '';
        document.getElementById("delivery-address-id").value = '';
        document.getElementById("delivery-address").value = '';

        $('#customer-modal').modal('hide');
    })
    //end of seleceted customer


    //ajax modal so_no
    var $modal = $('#customer-modal');
    $('#btn_lookup_so').live('click', function (e) {
        e.preventDefault();

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';

        $('body').modalmanager('loading');


        setTimeout(function () {
            $modal.load(js_base_url + 'sales/delivery_order/ajax_modal_so', '', function () {

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
    });


    var datatableSO = function () {
        var cust_id =  document.getElementById("customer-id").value;
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
                    "url": js_base_url + 'sales/delivery_order/ajax_so_list/'+cust_id, // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }
    //end of ajax modal so_no


    //get selected so and deliv address
    $('.select-so').live('click', function () {
        var id = $(this).attr('so-id');
        var so_code= $(this).attr('so-code');
        var deliv_id = $(this).attr('deliv-address-id');
        var deliv = $(this).attr('deliv-address');
        document.getElementById("so-id").value = id;
        document.getElementById("so-code").value = so_code;
        document.getElementById("delivery-address-id").value = deliv_id;
        document.getElementById("delivery-address").value = deliv;

        $('#customer-modal').modal('hide');
    })
    //end of seleceted so and deliv address


    //ajax modal delivery by
    var $modal = $('#customer-modal');
    $('#btn_lookup_delivery').live('click', function (e) {
        e.preventDefault();

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';

        $('body').modalmanager('loading');


        setTimeout(function () {
            $modal.load(js_base_url + 'sales/delivery_order/ajax_modal_delivery', '', function () {

                    $modal.modal();
                    datatableDeliv();
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


    var datatableDeliv = function () {
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
                    {"bSortable": false, "sClass": "text-center"},
                    null,
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
                    "url": js_base_url + 'sales/delivery_order/ajax_delivery_list' // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }
    //end of ajax modal delivery by


    //get selected delivery by
    $('.select-deliv').live('click', function () {
        var deliv_id = $(this).attr('deliv-id');
        var deliv_name= $(this).attr('deliv-name');
        document.getElementById("delivery-by").value = deliv_id;
        document.getElementById("delivery-name").value = deliv_name;

        $('#customer-modal').modal('hide');
    })
    //end of seleceted delivery by


    //ajax modal items
    var $modal = $('#customer-modal');
    $('#btn_lookup_items').live('click', function (e) {
        e.preventDefault();
        if ($('.customer').val() == '') {
            toastr["warning"]("Please select customer first.", "Warning!");
        } else if($('.so_code').val() == ''){
            toastr["warning"]("Please select SO No. first.", "Warning!");
        } else {
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');
            setTimeout(function () {
                $modal.load(js_base_url + 'sales/delivery_order/ajax_modal_items', '', function () {

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
                    "url": js_base_url + 'sales/delivery_order/ajax_items_list/'+$('.so_code').val(), // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }
    //end of ajax modal items

    //get selected item
    $('.select-item').live('click', function () {
        var item_id = $(this).attr('item-id');
        var item_desc = $(this).attr('item-desc');
        var qty = $(this).attr('qty');
        var uom = $(this).attr('uom');
        var item_code = $(this).attr('item-code');
        var qty_remain = $(this).attr('qty-remain');
        var item_price = $(this).attr('item-price');
        var i = $('#item_list_table tbody tr').length;
        var newRowContent =
            '<tr>' +
            '<td align="center"> <input type=hidden name="item_id[' + i + ']"  value= ' + item_id + '> <input type=hidden class="class_status" name="status_detail[' + i + ']"  value= "1">  ' + item_code + ' </td>' +
            '<td> ' + item_desc + ' </td>' +
            '<td align="center"><input type="hidden" name="item_price[' + i + ']" class="form-control number_only input-sm number_only" value='+ item_price +'> ' + uom + ' </td>' +
            '<td align="right"> <input type="hidden" name="qty[' + i + ']" class="form-control number_only input-sm number_only" value='+ qty +'> '+ qty +' </td>' +
            '<td><input type="text" name="delivery_qty[' + i + ']"  class="form-control input input-sm number_only" value='+ qty_remain +' max-delivery='+ qty_remain +'></td>' +
            '<td align="center"> <button type="button" class="btn btn-xs btn-danger remove-item" item-id = ' + item_id + ' ><i class="fa fa-remove"></i></button></td>' +
            '</tr>';

        //Add row
        $('#item_list_table tbody').append(newRowContent);
        handleMask();
        $('#customer-modal').modal('hide');
    })
    //end of seleceted item


    //    validate form
    function validate_submit() {
        var result = true;

        if ($('.form-group').hasClass('has-error')) {
            $('.form-group').removeClass('has-error');
        }
        if ($('td').hasClass('has-error')) {
            $('td').removeClass('has-error');
        }
        var do_code = $('#do-code').val();
        var do_date = $('#do-date').val();
        var customer = $('#customer-id').val();
        var so_no = $('#so-code').val();
        var deliv_address = $('#delivery-address').val();
        var delivery = $('#delivery-by').val();

        if (do_code <= 0) {
            toastr["warning"]("Please enter DO No, choose DO Date first.", "Warning!");
            $('#do-code').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (do_date <= 0) {
            toastr["warning"]("Please select DO Date.", "Warning!");
            $('#do-date').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (customer <= 0) {
            toastr["warning"]("Please select customer.", "Warning!");
            $('#customer-id').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (so_no <= 0) {
            toastr["warning"]("Please select SO No.", "Warning!");
            $('#so-code').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (deliv_address <= 0) {
            toastr["warning"]("Please enter delivery address.", "Warning!");
            $('#delivery-address').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (delivery <= 0) {
            toastr["warning"]("Please select delivery by.", "Warning!");
            $('#delivery-name').closest('.form-group').addClass('has-error');
            result = false;
        }
        var i = 0;
        var i_act = 0;
        $('#item_list_table > tbody > tr ').each(function () {
            if (!$(this).hasClass('hide')) {
                var qty = parseInt($('input[name="qty[' + i + ']"]').val()) || 0;
                var delivery_qty = parseInt($('input[name="delivery_qty[' + i + ']"]').val()) || 0;
                var max_delivery_qty = parseInt($('input[name="delivery_qty[' + i + ']"]').attr('max-delivery')) || 0;

                if (delivery_qty <= 0) {
                    toastr["warning"]("Please enter delivery qty.", "Warning");
                    $('input[name="delivery_qty[' + i + ']"]').closest('td').addClass('has-error');
                    result = false;
                }

                if (delivery_qty > max_delivery_qty) {
                    toastr["warning"]("Delivery qty is "+max_delivery_qty+" remaining.", "Warning");
                    $('input[name="delivery_qty[' + i + ']"]').closest('td').addClass('has-error');
                    result = false;
                }

                if (delivery_qty > qty) {
                    toastr["warning"]("Delivery qty is bigger than qty.", "Warning");
                    $('input[name="delivery_qty[' + i + ']"]').closest('td').addClass('has-error');
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
        return result;
    }
    //    end of validate form


    //    submit form
    $('#form-entry-do').on('submit', function () {
        Metronic.blockUI({
            target: '#form-entry-do',
            boxed: true,
            message: 'Processing...'
        });
        var btn = $(this).find("button[type=submit]:focus");


        if (validate_submit()) {
            var form_data = $('#form-entry-do').serializeArray();
            if (btn[0] == null) {
            } else {
                if (btn[0].name === 'save_close') {
                    form_data.push({name: "save_close", value: 'save_close'});
                }
            }
            $.ajax({
                    type: "POST",
                    url: js_base_url + 'sales/delivery_order/do_entry',
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
                    $('#form-entry-do').unblock();
                    toastr["error"]("Something has wrong, please try again later.", "Error");
                });
        } else {
            $('#form-entry-do').unblock();
        }
    });
    //    end of submit form

    // remove item row
    $('.remove-item').live('click', function () {
        var this_btn = $(this);
        bootbox.confirm("Are you sure want to delete?", function (result) {
            if (result == true) {
                this_btn.closest('tr').addClass('hide');
                this_btn.closest('tr').find('.class_status').val('9');
            }
        });
    });
    //end of remove item row
})