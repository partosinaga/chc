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
                "bFilter": false,
                "aoColumns": [
                    { "bSortable": false , "sClass": "text-center"},
                    { "sClass": "text-center" , "bSortable": false  },
                    //{ "sClass": "text-center", "sWidth" : "9%" },
                    { "sClass": "text-left" },
                    { "sClass": "text-left" },
                    { "sClass": "text-right", "bSortable": false},
                    { "sClass": "text-right", "bSortable": false},
                    { "sClass": "text-right", "bSortable": false},
                    { "bSortable": false, "sClass": "text-center"}
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [-1],
                    ["All"] // change per page values here
                ],
                "pageLength": -1, // default record count per page
                "ajax": {
                    "url": params.table_manage_ajax_url
                },
                "fnDrawCallback": function( oSettings ) {
                    Metronic.initUniform();

                    $('.date-picker').datepicker({
                        rtl: Metronic.isRTL(),
                        orientation: "right",
                        autoclose: true
                    });
                }
            }
        });

        var tableWrapper = $("#table_manage_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });

    }

    var submitClick = function(){
        $('#submit-bill').click(function(e) {
            e.preventDefault();

            var checkedCount = $("#table_manage input[type=checkbox]:checked").length || 0;
            if(checkedCount > 0){
                bootbox.confirm({
                    message: "Submit bills ?",
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
                            var form_data = $('#form-entry').serializeArray();

                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                type: "POST",
                                url: params.submit_ajax_url,
                                dataType: "json",
                                data : form_data
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();

                                    if(msg.type == '1'){
                                        toastr["success"](msg.message, "Success");
                                        //window.location.assign(msg.redirect_link);
                                        if ( $.fn.dataTable.isDataTable('#table_manage' )) {
                                            $('#table_manage').dataTable().api().ajax.url(params.table_manage_ajax_url).load();
                                        }
                                    }
                                    else {
                                        toastr["warning"](msg.message, "Warning");
                                    }
                                });

                        }
                    }
                });
            }else{
                toastr["warning"]("Please check at least 1 bill to continue !", "Warning");
            }
        });
    }

    var checkAll = function(){
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

    var dateChanged = function(){
        $('#filter_period_date').datepicker().on('changeDate', function(e){
            //grid.clearAjaxParams();
            grid.submitFilter();
            $(this).datepicker('hide');
        })
    }

    return {
        //main function to initiate the module
        init: function (options) {
            params = options;

            handleRecords();
            submitClick();
            checkAll();
            dateChanged();
        }

    };

}();

