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
                    { "sClass": "text-center", "bSortable": false , "sWidth" : "3%"},
                    null,
                    //{ "sClass": "text-center", "sWidth" : "9%" },
                    { "sClass": "text-center" , "bSortable": false },
                    { "sClass": "text-left" },
                    { "sClass": "text-center" , "bSortable": false},
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
                    "url": params.table_manage_ajax_url
                },
                "fnDrawCallback":function(oSet){
                    //console.log(oSet._iRecordsTotal);
                    var rowCount = oSet._iRecordsTotal;
                    if(rowCount > 0){
                        $('#create-invoice').removeClass('hide');
                    }else{
                        $('#create-invoice').addClass('hide');
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
        $('input[name="c_inv_date"]').live('change', function(){
            var inv_date = $(this).val();
            //console.log(inv_date);
            if(inv_date != ''){
                var currentDate = moment($(this).val(), "DD-MM-YYYY");
                var dueDate = moment(inv_date, "DD-MM-YYYY").add(19, 'days');

                $('#inv_due_date').datepicker("setDate", dueDate.format('DD-MM-YYYY'));
                //$('#inv_due_date').val(dueDate.format('DD-MM-YYYY'));
            }
        });

        $('.btn-remove').live('click', function(e) {
            e.preventDefault();

            var bill_id = parseFloat($(this).attr('data-bill-id')) || 0;
            if(bill_id > 0){
                $.ajax({
                    type: "POST",
                    url: params.remove_ajax_url,
                    dataType: "json",
                    data : {bill_id : bill_id}
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
        });

        $('#create-invoice').on('click', function(e) {
            e.preventDefault();

            bootbox.confirm({
                message: "Create Invoice ?",
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
                        var currentDate = moment();
                        var startDate = moment(); //moment(currentDate.format('YYYY-MM') + '-01');
                        var dueDate = moment(startDate.format('YYYY-MM-DD')).add(19, 'days');

                        $('#inv_date').datepicker("setDate", startDate.format('DD-MM-YYYY'));
                        $('#inv_due_date').datepicker("setDate", dueDate.format('DD-MM-YYYY'));

                        var $modal_cal = $('#ajax-calendar');

                        if ($modal_cal.hasClass('bootbox') == false) {
                            $modal_cal.addClass('modal-fix');
                        }

                        $modal_cal.modal();
                    }
                }
            });
        });

        $('#submit-invoice').on('click', function(e) {
            e.preventDefault();
            var inv_date = $('input[name="c_inv_date"]').val();
            var inv_due = $('input[name="c_inv_due_date"]').val();

            var startDate = moment(inv_date, "DD-MM-YYYY");
            var dueDate = moment(inv_due, "DD-MM-YYYY");

            if(dueDate > startDate){
                var url = params.submit_ajax_url;
                var days = parseInt(dueDate.format('DD')) - parseInt(startDate.format('DD'));
                if(days < 10){
                    //inv_due = startDate.add(19, 'days').format("DD-MM-YYYY");
                }

                //console.log(inv_date + ' - ' + inv_due);
                $("#form-entry").append('<input type="hidden" name="inv_date" value="' + inv_date + '">' +
                    '<input type="hidden" name="inv_due_date" value="' + inv_due + '">');
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();

                $('#ajax-calendar').modal('hide');
            }else{
                toastr["error"]("Invoice Due Date is not valid", "Warning");
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

