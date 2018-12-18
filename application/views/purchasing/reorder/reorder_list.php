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
        <div class="row">
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users"></i>PR List Active
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container table-responsive">
                            <div class="col-md-12" style="padding-bottom: 70px;">
                                <div class="table-actions-wrapper">
                                    <?php
                                    if (check_session_action(get_menu_id(), STATUS_NEW)) {
                                        echo '<button class="btn btn-sm green-haze yellow-stripe table-group-action-submit"><i class="fa fa-check"></i>&nbsp;&nbsp;Submit </button>';
                                    }
                                    ?>
                                </div>
                                <table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th width="100px" class="text-center"> Item Code </th>
                                        <th class="text-center"> Description </th>
                                        <th width="60px" class="text-center"> UOM </th>
                                        <th width="60px" class="text-center"> Factor </th>
                                        <th width="60px" class="text-center"> UOM Dist. </th>
                                        <th width="80px" class="text-center"> On Hand Qty </th>
                                        <th width="60px" class="text-center"> Min Stock </th>
                                        <th width="60px" class="text-center"> Max Stock </th>
                                        <th width="60px" class="text-center"> Order Qty </th>
                                        <th width="50px" class="text-center"> Action </th>
                                    </tr>
                                    <tr role="row" class="filter bg-grey-steel">
                                        <td><input type="text" class="form-control form-filter input-sm" name="filter_item_code"></td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_item_desc">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_uom1">
                                        </td>
                                        <td>&nbsp;</td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="filter_uom2"></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>
                                            <div class="text-center">
                                                <button class="btn btn-xs yellow filter-submit tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-xs red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body" style="margin-right: 0px;"><i class="fa fa-times"></i></button>
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
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    function init_page() {
        $(".mask_currency").inputmask("decimal", {
            radixPoint: ".",
            groupSeparator: ",",
            digits: 2,
            autoGroup: true,
            autoUnmask: true
        });
    }
    init_page();

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
            dataTable: {
                "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",

                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    { "sClass": "text-center" },    // item code
                    null,                                               // description
                    { "sClass": "text-center" },                        // uom
                    { "sClass": "text-center", "bSortable": false },    // factor
                    { "sClass": "text-center" },    // uom dist
                    { "bSortable": false, "sClass": "text-right" },    // on hand qty
                    { "bSortable": false, "sClass": "text-right" },    // min stock
                    { "bSortable": false, "sClass": "text-right" },    // max stock
                    { "bSortable": false, "sClass": "text-right" },    // order qty
                    { "bSortable": false, "sClass": "text-center" }     // action
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 100, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('purchasing/reorder/ajax_reorder_list/'. get_menu_id());?>"
                },
                "fnDrawCallback": function( oSettings ) {
                    init_page();
                    Metronic.initUniform();
                }
            }
        });

        var tableWrapper = $("#datatable_ajax_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });

        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();

            //console.log(grid);

            if (grid.getSelectedRowsCountByParam(10) > 0) {
                bootbox.confirm("Are you sure want to Reorder?", function (result) {
                    if (result == true) {
                        var result = grid.getSelectedRowsCheckboxByParam(10);

                        var data = [];
                        $.each(result, function (index, value) {
                            data.push(value[0]);
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('purchasing/reorder/ajax_reorder_submit');?>",
                            dataType: "json",
                            data: {id: data}
                        })
                            .done(function (msg) {
                                if (msg.valid == '0' || msg.valid == '1') {
                                    grid.getDataTable().ajax.reload();
                                    grid.clearAjaxParams();

                                    if (msg.valid == '1') {
                                        toastr["success"](msg.message, "Success");
                                    } else {
                                        toastr["error"](msg.message, "Error");
                                    }
                                } else {
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                }
                            })
                            .fail(function () {
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                toastr["warning"]("Please Select Detail.", "Warning");
            }
        });

    }

    handleRecords();

});

</script>