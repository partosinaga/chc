/**
 * Created by Hendhi on 1/14/2016.
 */
var FormJS = function () {
    var params;
    var grid = new Datatable();

    var handleRecords = function (period_date) {

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
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "bFilter": false,
                "aoColumns": [
                    { "bSortable": false, "sClass": "text-left" },
                    { "bSortable": false , "sClass": "text-center" },
                    { "bSortable": false },
                    { "bSortable": false,"sClass": "text-right" },
                    { "bSortable": false, "sClass": "text-center"},
                    { "bSortable": false, "sClass": "text-center"},
                    { "bSortable": false, "sClass": "text-center"},
                    { "bSortable": false, "sClass": "text-center"},
                    { "bSortable": false, "sClass": "text-right"},
                    { "bSortable": false, "sClass": "text-center"}
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, -1],
                    [10, 20, 50, 100, "All"] // change per page values here
                ],
                "pageLength": 50, // default record count per page
                "ajax": {
                    "url": params.table_manage_ajax_url + "/" + period_date
                }
                ,
                "fnDrawCallback": function( oSettings ) {
                    var api = this.api();
                    var rows = api.rows( {page:'current'} ).nodes();
                    var last=null;

                    api.column(0, {page:'current'} ).data().each( function ( group, i ) {
                        if ( last !== group ) {
                            $(rows).eq( i ).before(
                                '<tr style="background-color: #FBE9E7;"><td colspan="10" class="bold uppercase">'+group+'</td></tr>'
                            );

                            last = group;
                        }
                    } );

                    Metronic.initUniform();
                }
            }
        });

        var tableWrapper = $("#table_manage_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });

    }

    var formEvents = function(){
        $('.btn_calculate').on('click', function(){
            var bill_date = $('input[name="period_date"]').val();

            //console.log('bill date ' + bill_date);
            bootbox.confirm({
                message: "Calculate late penalty until " + bill_date + " ?",
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
                    if(!result){

                    }else{
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: params.submit_ajax_url,
                            dataType: "json",
                            data: { period_dmy: bill_date}
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();

                                if(msg.type == '0' || msg.type == '1'){
                                    if(msg.type == '1'){
                                        if ( $.fn.dataTable.isDataTable('#table_manage' )) {

                                            $('#table_manage').dataTable().api().ajax.url(params.table_manage_ajax_url + "/" + bill_date).load();
                                        }

                                        toastr["success"](msg.message, "Success");

                                    }
                                    else {
                                        toastr["warning"](msg.message, "Warning");
                                    }
                                }
                                else {
                                    toastr["error"]("Process failed, please try again later.", "Error");
                                }
                            });

                    }
                }
            });
        });

        $('.btn-delete').live('click', function(){
            var bill_date = $('input[name="period_date"]').val();
            var interestChargeId = $(this).attr('data-id');

            console.log('interestChargeId ' + interestChargeId);

            bootbox.confirm({
                message: "Cancel to revise penalty ?",
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
                    if(!result){

                    }else{
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: params.cancel_ajax_url,
                            dataType: "json",
                            data: { interestcharge_id: interestChargeId, status : params.status_cancel}
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();

                                if(msg.type == '0' || msg.type == '1'){
                                    if(msg.type == '1'){
                                        if ( $.fn.dataTable.isDataTable('#table_manage' )) {
                                            $('#table_manage').dataTable().api().ajax.url(params.table_manage_ajax_url + "/" + bill_date).load();
                                        }

                                        toastr["success"](msg.message, "Success");

                                    }
                                    else {
                                        toastr["warning"](msg.message, "Warning");
                                    }
                                }
                                else {
                                    toastr["error"]("Process failed, please try again later.", "Error");
                                }
                            });

                    }
                }
            });
        });

        $('.btn-void').live('click', function(){
            var bill_date = $('input[name="period_date"]').val();
            var interestChargeId = $(this).attr('data-id');

            bootbox.confirm({
                message: "Delete penalty for current period only ?",
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
                    if(!result){

                    }else{
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: params.cancel_ajax_url,
                            dataType: "json",
                            data: { interestcharge_id: interestChargeId, status : params.status_void}
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();

                                if(msg.type == '0' || msg.type == '1'){
                                    if(msg.type == '1'){
                                        if ( $.fn.dataTable.isDataTable('#table_manage' )) {
                                            $('#table_manage').dataTable().api().ajax.url(params.table_manage_ajax_url + "/" + bill_date).load();
                                        }

                                        toastr["success"](msg.message, "Success");

                                    }
                                    else {
                                        toastr["warning"](msg.message, "Warning");
                                    }
                                }
                                else {
                                    toastr["error"]("Process failed, please try again later.", "Error");
                                }
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

            formEvents();
        },
        reloadGrid: function(period_date){
            handleRecords(period_date);
        }

    };

}();

