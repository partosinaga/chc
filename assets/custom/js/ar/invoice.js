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
                    { "sClass": "text-center", },
                    null,
                    { "sClass": "text-center" , "bSortable": false},
                    { "sClass": "text-center", "bSortable": false},
                    { "sClass": "text-right",  "bSortable": false},
                    { "sClass": "text-right",  "bSortable": false},
                    { "sClass": "text-right", "bSortable": false},
                    { "sClass": "text-right",  "bSortable": false},
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

    return {
        //main function to initiate the module
        init: function (options) {
            params = options;

            handleRecords();
        }

    };
}();

