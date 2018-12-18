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
                    { "bSortable": false , "sClass": "text-center"},
                    { "sClass": "text-center" },
                    { "sClass": "text-center" },
                    { "sClass": "text-left" },
                    { "sClass": "text-center" , "bSortable": false},
                    { "sClass": "text-right", "bSortable": false},
                    { "sClass": "text-right","bSortable": false },
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
                }
            }
        });

        var tableWrapper = $("#table_manage_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });

    }

    var formEvents = function(){
        $('.btn-cancel').live('click', function(){
			var id = $(this).attr('data-id');
			var action = $(this).attr('data-action');

            bootbox.prompt({
                title: "Please enter cancel reason :",
                value: "",
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
                    if(result === null){
                        //console.log('Empty reason');
                    }else if(result === ''){
                        toastr["warning"]("Cancel reason must be filled to proceed.", "Warning");
                    }else{
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: params.cancel_ajax_url,
                            dataType: "json",
                            data: { allocationheader_id: id, reason:result }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();

                                if(msg.type == '0' || msg.type == '1'){
                                    //grid.getDataTable().ajax.reload();
                                    //grid.clearAjaxParams();
                                    console.log(msg);
                                    if(msg.type == '1'){
                                        toastr["success"](msg.message, "Success");
                                    }
                                    else {
                                        toastr["warning"](msg.message, "Warning");
                                    }

                                    $('#table_manage').dataTable().api().ajax.url(params.table_manage_ajax_url).load();
                                }
                                else {
                                    toastr["error"]("Action can not be processed, please try again later.", "Error");
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

            handleRecords();
            formEvents();
        }

    };
}();

