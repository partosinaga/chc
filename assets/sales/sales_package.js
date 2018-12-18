$(document).ready(function () {
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
    var datatablePackage = function () {
        grid_req.init({
            src: $("#package-datatable"),
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
                    "url": js_base_url + 'sales/sales_package/ajax_package_list' // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }


//ajax modal package
    var $modal = $('#customer-modal');
    $('#btn_lookup_package').live('click', function (e) {
        e.preventDefault();

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';

        $('body').modalmanager('loading');


        setTimeout(function () {
            $modal.load(js_base_url + 'sales/sales_package/ajax_modal_package', '', function () {

                    $modal.modal();
                    datatablePackage();
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
//end of ajax modal package

//get selected item list
    $('.select-package').live('click', function () {
        var item_id = $(this).attr('item-id');
        $.ajax({
            url: js_base_url + 'sales/sales_package/append_row',
            method: "POST",
            data: {
                item_id: item_id
            },
            success: function(data){
                $('#so-package-table-body').append(data);
                $('#customer-modal').modal('hide');
                handleMask();
            }
        })
    })
//end of get selected item list


// remove item row
    $('.remove-package').live('click', function () {
        var id = $(this).attr('grouping');
        bootbox.confirm("Are you sure want to delete?", function (result) {
            if (result == true) {
                $('tr[grouping= '+ id +']').remove();
            }
        });
    });
//end of remove item row
})


// calculate amount
function calculate_tot_amount() {
    var len = $('#package_list_table tbody tr .grouping').length;
    if (len > 0) {
        var tot_amount = 0;
        for (var i = 0; i < len; i++) {
            var status = parseInt($('input[name="status_detail[' + i + ']"]').val()) || 0;
            if (status == 1) {
                var qty = parseFloat($('input[name="qty[' + i + ']"]').val()) || 0;
                var prc = parseFloat($('input[name="package_price[' + i + ']"]').val()) || 0;
                var disc = parseFloat($('input[name="discount[' + i + ']"]').val()) || 0;
                var amnt = (qty * prc) - disc;
                $('input[name="amount[' + i + ']"]').val(amnt);
                tot_amount += amnt;

                console.log(prc);
            }
        }
    }
}

$('.calcu-pack').live('keyup change', function () {
    calculate_tot_amount();
});
//end of calculate amount