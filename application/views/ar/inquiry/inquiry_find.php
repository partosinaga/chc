<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE HEADER-->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <?php
                $breadcrumbs = get_menu_name($this->uri->segment(1), $this->uri->segment(2), $this->uri->segment(3));
                foreach($breadcrumbs as $breadcrumb){
                    echo $breadcrumb;
                }
                ?>
            </ul>
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="tab-content">
            <div class="tab-pane active">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
                        ?>
                        <!-- Begin: life time stats -->
                        <div class="panel ">
                            <!-- MASTER BANK LIST -->
                            <div class="row">
                                <div class="col-md-12 ">
                                    <!-- Begin: life time stats -->
                                    <div class="table-container">
                                        <table class="table table-striped table-bordered table-hover table-po-detail" id="table_folio">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th class="text-center">
                                                    Folio
                                                </th>
                                                <th>
                                                    Guest
                                                </th>
                                                <th>
                                                    Company
                                                </th>
                                                <th class="text-center">
                                                    Type
                                                </th>
                                                <th>
                                                    Room
                                                </th>
                                                <th class="text-center">
                                                    Check In
                                                </th>
                                                <th class="text-center">
                                                    Check Out
                                                </th>
                                                <th class="text-center">
                                                    Status
                                                </th>
                                                <th style="width:8%;">

                                                </th>
                                            </tr>
                                            <tr role="row" class="filter bg-grey-steel">
                                                <th class="text-center">
                                                    <input type="text" class="form-control form-filter input-sm" name="filter_code">
                                                </th>
                                                <th>
                                                    <input type="text" class="form-control form-filter input-sm" name="filter_name">
                                                </th>
                                                <th>
                                                    <input type="text" class="form-control form-filter input-sm" name="filter_company">
                                                </th>
                                                <th>

                                                </th>
                                                <th class="text-center">
                                                    <input type="text" class="form-control form-filter input-sm" name="filter_room">
                                                </th>
                                                <th class="text-center">

                                                </th>
                                                <th class="text-center">

                                                </th>
                                                <th class="text-center">

                                                </th>
                                                <th >
                                                    <div class="text-center">
                                                        <button class="btn btn-sm yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                                        <button class="btn btn-sm red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
                                                    </div>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- End: life time stats -->
                                </div>
                            </div>
                            <!-- END MASTER BANK LIST-->

                        <!-- End: life time stats -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CONTENT -->
<form action="javascript:;" id="form-entry" class="form-horizontal hide" method="post">
</form>

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    $(document).ready(function(){
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        var grid = new Datatable();
        var handleRecords = function () {
            grid.init({
                src: $("#table_folio"),
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
                    "aoColumns": [
                        { "sClass": "text-center", "sWidth" : "9%" , "bSortable": true},
                        null,
                        null,
                        { "sClass": "text-center", "sWidth" : "9%", "bSortable": false },
                        { "sClass": "text-left", "sWidth" : "9%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "8%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "8%", "bSortable": false },
                        { "sClass": "text-center", "sWidth" : "10%" },
                        { "bSortable": false, "sClass": "text-center", "sWidth" : "8%"}
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [50, 100, -1],
                        [50, 100, "All"] // change per page values here
                    ],
                    "pageLength": 50, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('ar/inquiry/get_inquiry_manage/' . get_menu_id());?>"
                    }
                }
            });

            var tableWrapper = $("#table_folio_wrapper");
            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });

        }

        handleRecords();

    });

</script>