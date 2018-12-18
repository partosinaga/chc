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

    var grid_coa = new Datatable();
    var handleTableCOA = function (coaId, detailId, uniqueId)   {
        var coa_id_exist = '-';
        var n = 0;
        $('#table_detail tbody tr').each(function () {
            if ($(this).hasClass('hide') == false) {
                var coa_id = parseInt($(this).find('input[name*="coa_id"]').val()) || 0;
                if (coa_id > 0) {
                    if(coa_id_exist == '-'){
                        coa_id_exist = '';
                    }
                    if(n > 0) {
                        coa_id_exist += '_';
                    }

                    coa_id_exist += coa_id;
                    n++;
                }
            }
        });

        grid_coa.init({
            src: $("#datatable_coa"),
            onSuccess: function (grid_coa) {
                // execute some code after table records loaded
            },
            onError: function (grid_coa) {
                // execute some code on network or other general error
            },
            onDataLoad: function(grid_coa) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Populating...',
            dataTable: {
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    { "sClass": "text-center", "bSortable": true, "sWidth" : '15%' },
                    null,
                    { "sClass": "text-center","sWidth" : '10%' },
                    { "sClass": "text-center","sWidth" : '10%' },
                    { "sClass": "text-center", "sWidth" : '6%' },
                    { "bSortable": false, "sClass": "text-center" }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": js_base_url + 'general/modalbook/get_coa_list_by_id/' + coaId + '/' + detailId + '/' + uniqueId + '/' + coa_id_exist + '/'
                }
            }
        });

        var tableWrapper = $("#datatable_coa_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        }); // initialize select2 dropdown

        $('select.select2me').select2();

        // End Datatable Item
    }

    var formEvents = function(){
        $('#btn_lookup_journal').on('click', function(){
            var journal_no = $('input[name="journal_no"]').val();

            if(journal_no != ''){
                $.ajax({
                type: "POST",
                url: js_base_url + 'finance/ledger/ajax_editposted_find',
                dataType: "json",
                data: { journal_no: journal_no}
            })
                .done(function( msg ) {
                    if(msg.type == '1'){
                        var journal = msg.journal;
                        $('input[name="journal_date"]').val(journal.journal_date);
                        $('textarea[name="journal_remarks"]').val(journal.journal_remark);
                        $('input[name="total_debit"]').val(journal.journal_amount);
                        $('input[name="total_credit"]').val(journal.journal_amount);

                        $('#datatable_detail tbody').empty();
                        $('#datatable_detail tbody').append(journal.details);

                        $('select.select2me').select2();

                        autosize($('textarea'));
                        handleMask();
                    }else{
                        toastr["warning"](msg.message, "Warning");
                    }
                })
                .fail(function(){
                    toastr["warning"]("Document not found ! Please try again.", "Warning");
                });
            }else{
                toastr["warning"]("Please type Journal No !", "Warning");
            }
        });

        $('.change_coa').live('click', function (e) {
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            var index = parseInt($(this).attr('data-id')) || 0;

            var coa_id = parseInt($('input[name="coa_id[' + index + ']"]').val()) || 0;
            var detail_id = parseInt($('input[name="postdetail_id[' + index + ']"]').val()) || 0;

            $('body').modalmanager('loading');

            var uninitialized = $('#datatable_coa').filter(function() {
                return !$.fn.DataTable.fnIsDataTable(this);
            });

            var $modal = $('#ajax-modal');

            grid_coa.resetFilter();

            setTimeout(function(){
                $modal.load(js_base_url + 'general/modalbook/ajax_coa_by_id.tpd', '', function(){
                    handleTableCOA(coa_id, detail_id, index);

                    $modal.modal();

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if($modal.hasClass('modal-overflow') === false){
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});
                });
            }, 150);
        });

        $('.btn-select-coa').live('click', function (e) {
            e.preventDefault();

            var coa_id = parseInt($(this).attr('coa-id')) || 0;
            var coa_code = $(this).attr('coa-code');
            var coa_desc = $(this).attr('coa-desc');
            var index = parseInt($(this).attr('unique-id')) || 0;

            if(coa_id > 0) {
                $('input[name="coa_id[' + index + ']"]').val(coa_id);
                $('input[name="coa_code[' + index + ']"]').val(coa_code);
                $('input[name="coa_desc[' + index + ']"]').val(coa_desc);
                $('input[name="status_edit[' + index + ']"]').val(1);
                $('input[name="coa_code[' + index + ']"]').addClass('font-red-sunglo');

                $('#btn_save').removeClass('hide');

                $('#ajax-modal').modal('hide');
            }else{
                toastr["warning"]("No COA is selected !", "Warning");
            }
        });

        $('button[name="save"]').click(function(e){
            e.preventDefault();

            bootbox.confirm({
                message: "Current COA will be modified<br><strong>Are you sure ?</strong>",
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

                        var form_data = $('#form-entry').serializeArray();

                        $.ajax({
                            type: "POST",
                            url: js_base_url + 'finance/ledger/ajax_editposted_submit',
                            dataType: "json",
                            data: form_data
                        })
                            .done(function( msg ) {
                                if(msg.valid == '0' || msg.valid == '1'){
                                    if(msg.valid == '1'){
                                        window.location.assign(msg.redirect_link);
                                    }
                                    else {
                                        toastr["error"](msg.message, "Error");
                                        $('#form-entry').unblock();
                                    }
                                }
                                else {
                                    toastr["error"]("Edit posted journal can not be submitted, please try again later.", "Failed");
                                    $('#form-entry').unblock();
                                }
                            })
                            .fail(function () {
                                $('#form-entry').unblock();
                                toastr["error"]("Edit posted journal can not be submitted, please try again later.", "Failed");
                            });
                    }
                }
            });
        });

    }

    return {
        //main function to initiate the module
        init: function (options) {
            params = options;

            handleMask();
            formEvents();
        }

    };
}();

