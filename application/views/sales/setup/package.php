<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <?php
                $breadcrumbs = get_menu_name($this->uri->segment(1), $this->uri->segment(2), $this->uri->segment(3));
                foreach ($breadcrumbs as $breadcrumb) {
                    echo $breadcrumb;
                }
                ?>
            </ul>
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class')); ?>
                <!-- Begin: life time stats -->
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cube"></i>Package Inventory List
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('sales/setup/package_form/0.tpd'); ?>"
                               class="btn default yellow-stripe">
                                <i class="fa fa-plus"></i> <span class="hidden-480"> New Package Inventory </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container table-responsive">
                            <div class="col-md-12" style="padding-bottom: 90px;">
                                <table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th width="1%"> # </th>
                                        <th width="10%">Packcage Code</th>
                                        <th>Description</th>
                                        <th>Notes</th>
                                        <th width="10%">Price</th>
                                        <th width="8%">Status</th>
                                        <th width="8%">#</th>
                                    </tr>
                                    <tr role="row" class="filter bg-grey-steel">
                                        <td>&nbsp;</td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_code">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_desc">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_notes">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_price">
                                        </td>
                                        <td>
                                            <select name="filter_status" class="form-control form-filter input-sm select2me">
                                                <option value=""> -- Select --</option>
                                                <option value="<?php echo STATUS_NEW ?>">Active</option>
                                                <option value="<?php echo STATUS_INACTIVE ?>">Inactive</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <button class="btn btn-sm yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-sm red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End: life time stats -->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->
<!--MODAL DETAIL PACKAGE-->
<div class="modal fade bs-modal-lg" id="large" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="header-title"></h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th width="10%" class="text-right">ITEM CODE</th>
                        <th>Item</th>
                        <th width="10%" class="text-right">QTY</th>
                        <th width="10%" class="text-center">UOM</th>
                        <th width="10%" class="text-center">STATUS</th>
                    </tr>
                    </thead>
                    <tbody id="package-detail">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!--END OF MODAL DETAIL PACKAGE-->

<script>
    $(document).ready(function(){
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "50000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        var grid = new Datatable();

        var handleRecords = function () {
            grid.init({
                src: $("#datatable_ajax"),
                onSuccess: function (grid) {
                    // execute some code after table records loaded
                },
                onError: function (grid) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Loading...',
                dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                    // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                    // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                    // So when dropdowns used the scrollable div should be removed.
                    "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "bSortable": false },
                        { "sClass": "text-center" },
                        null,
                        null,
                        { "sClass": "text-right" },
                        { "sClass": "text-center" },
                        { "sClass": "text-center" },
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('sales/setup/package_ajax_list');?>" // ajax source
                    },
                    "fnDrawCallback": function( oSettings ) {
                        $('.tooltips').tooltip();
                    }
                }
            });

            var tableWrapper = $("#datatable_ajax_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });

            $('.view').live('click', function(){
                var id = $(this).attr('package-id');
                var desc = $(this).attr('package-desc');
                $.ajax({
                    url: "<?php echo site_url('sales/setup/detail_package'); ?>",
                    method: "POST",
                    data: {
                        id
                    },
                    success: function(data){
                        $('#package-detail').html(data);
                        document.getElementById("header-title").innerHTML = desc;
                        $('#large').modal("show");
                    }
                })


            })
        }
        handleRecords();
    })


</script>