/**
 * Created by Hendhi on 1/14/2016.
 */
var FormJS = function () {
    var params;
    var grid = new Datatable();

    var handleRecords = function () {
        grid.init({
            src: $("#table_manage"),
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
                    { "sClass": "text-center"},
                    null,
                    { "sClass": "text-center", "bSortable": false},
                    { "sClass": "text-center", "bSortable": false},
                    { "sClass": "text-right", "bSortable": false},
                    { "sClass": "text-right", "bSortable": false},
                    { "sClass": "text-right", "bSortable": false},
                    { "bSortable": false, "sClass": "text-center"}
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, -1],
                    [10, 20, 50, 100, "All"] // change per page values here
                ],
                "pageLength": 20, // default record count per page
                "ajax": {
                    "url" : params.table_manage_ajax_url
                },
                "fnDrawCallback":function(oSet){
                    //console.log(oSet._iRecordsTotal);
                    var rowCount = oSet._iRecordsTotal;
                    if(rowCount > 0){
                        $('#posting-button').removeClass('hide');
                    }else{
                        $('#posting-button').addClass('hide');
                    }

                    Metronic.initUniform();
                    /*
                    $('.date-picker').datepicker({
                        rtl: Metronic.isRTL(),
                        orientation: "right",
                        autoclose: true
                    });*/
                }
            }
        });

        var tableWrapper = $("#table_manage_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });

    }

    var formEvents = function(){
        /*Events*/
        $('.btn-cancel').live('click', function(e) {
            e.preventDefault();

            var inv_id = parseFloat($(this).attr('data-inv-id')) || 0;
            if(inv_id > 0){
                bootbox.confirm({
                    message: "Cancel this Edit Listing Invoice ?",
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
                            $.ajax({
                                type: "POST",
                                url: params.cancel_ajax_url,
                                dataType: "json",
                                async: false,
                                data : {inv_id : inv_id}
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();

                                    if(msg.type == '1'){
                                        if ( $.fn.dataTable.isDataTable('#table_manage' )) {
                                            $('#table_manage').dataTable().api().ajax.url(params.table_manage_ajax_url).load();
                                        }
                                        toastr["success"](msg.message, "Success");
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

        $('#posting-button-1').click(function(e) {
            e.preventDefault();

            var checkedCount = $("#table_manage input[type=checkbox]:checked").length || 0;

            if(checkedCount > 0){
                var $modal_cal = $('#ajax-calendar');

                if ($modal_cal.hasClass('bootbox') == false) {
                    $modal_cal.addClass('modal-fix');
                }

                $modal_cal.modal();
            }

        });

        $('#posting-button').click(function(e) {
            e.preventDefault();

            var checkedCount = $("#table_manage input[type=checkbox]:checked").length || 0;

            if(checkedCount > 0){
                bootbox.confirm({
                    message: "Posting Invoice ?",
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

                            var posting_date = $('input[name="c_posting_date"]').val();
                            $('input[name="posting_date"]').val(posting_date);

                            var form_data = $('#form-entry').serializeArray();

                            $.ajax({
                                type: "POST",
                                url: params.submit_ajax_url,
                                dataType: "json",
                                data : form_data
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();

                                    if(msg.type == '1'){
                                        if ( $.fn.dataTable.isDataTable('#table_manage' )) {
                                            $('#table_manage').dataTable().api().ajax.url(params.table_manage_ajax_url).load();
                                        }
                                        toastr["success"](msg.message, "Success");
                                    }
                                    else {
                                        toastr["warning"](msg.message, "Warning");
                                    }
                                });

                        }
                    }
                });
            }else{
                toastr["warning"]("Please select at least 1(one) invoice to proceed !", "Warning");
            }
        });

        $("#table_manage #checkall").click(function () {
            if ($("#table_manage #checkall").is(':checked')) {
                $("#table_manage input[type=checkbox]").each(function () {
                    //$(this).attr("checked", "checked");
                    $(this).prop("checked", true);
                    $(this).parent('span').addClass('checked');
                });

            } else {
                $("#table_manage input[type=checkbox]").each(function () {
                    $(this).prop("checked", false);
                    //$(this).removeAttr("checked");
                    $(this).parent('span').removeClass('checked');
                });
            }
        });
    }

    return {
        //main function to initiate the module
        init: function (options) {
            params = options;

            handleRecords();
            formEvents();
        }

    };
}();

