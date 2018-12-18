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
                                <table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th width="10px"> # </th>
                                        <th width="11%"> PR Code </th>
                                        <th width="10%"> PR Date </th>
                                        <th width="7%"> Department </th>
                                        <th> Remarks </th>
                                        <th width="11%"> PO Code </th>
                                        <th width="11%"> PO Status </th>
                                        <th width="11%"> GRN Code </th>
                                        <th width="11%"> GRN Status </th>
                                        <th width="60px"> Action </th>
                                    </tr>
                                    <tr role="row" class="filter bg-grey-steel">
                                        <td>&nbsp;</td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_pr_code">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm date date-picker margin-bottom-5" readonly name="filter_pr_date_from" data-date-format="dd-mm-yyyy" placeholder="From">
                                            <input type="text" class="form-control form-filter input-sm date date-picker" readonly name="filter_pr_date_to" data-date-format="dd-mm-yyyy" placeholder="From">
                                        </td>
                                        <td>
                                            <select name="filter_department_id" class="form-control form-filter input-sm select2me">
                                                <option value="">All</option>
                                                <?php
                                                foreach($qry_department->result() as $row_dept){
                                                    echo '<option value="' . $row_dept->department_id . '">' . $row_dept->department_name . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>

                                        <td><input type="text" class="form-control form-filter input-sm" name="filter_remarks"></td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="filter_po_code"></td>
                                        <td>&nbsp;</td>
                                        <td><input type="text" class="form-control form-filter input-sm" name="filter_grn_code"></td>
                                        <td>&nbsp;</td>
                                        <td>
                                            <div class="text-center">
                                                <button class="btn btn-xs yellow filter-submit tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-xs red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
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

<div id="ajax-modal" data-width="1024" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>
<input type="hidden" id="pr_code" value="">

<script>
$(document).ready(function(){
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-bottom-right",
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            rtl: Metronic.isRTL(),
            orientation: "right",
            autoclose: true
        });
        //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
    }

    var $modal = $('#ajax-modal');

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

                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    { "bSortable": false, "sClass": "text-center" },
                    { "sClass": "text-center" },
                    { "sClass": "text-center" },
                    { "sClass": "text-center", "bSortable": false },
                    { "bSortable": false },
                    { "bSortable": false, "sClass": "text-center" },
                    { "bSortable": false, "sClass": "text-center" },
                    { "bSortable": false, "sClass": "text-center" },
                    { "bSortable": false, "sClass": "text-center" },
                    { "bSortable": false, "sClass": "text-center" }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('purchasing/po/ajax_pr_list_active/'. get_menu_id());?>" // ajax source
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

    }

    handleRecords();

    $('.btn-view').live('click', function (e) {
        e.preventDefault();

        var pr_code = $(this).attr('data-title');
        $('#pr_code').val(pr_code);
        var pr_id = $(this).attr('data-id');
        pr_id = parseInt(pr_id) || 0;

        if(pr_id > 0) {
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('purchasing/po/ajax_modal_pr_detail/1');?>', '', function () {

                    $modal.modal();
                    datatablePRDetail(pr_id);

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if ($modal.hasClass('modal-overflow') == false) {
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'})

                });
            }, 100);
        }
        else {
            toastr["warning"]("Please Select PR.", "Warning");
        }
    });

    var grid_pr_detail = new Datatable();

    var datatablePRDetail = function (pr_id) {

        var pr_detail_id_exist = '-';

        grid_pr_detail.init({
            src: $("#datatable_pr_detail"),
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
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    { "bSortable": false, "sClass": "text-center" },
                    { "bSortable": false },
                    { "bSortable": false, "sClass": "text-center" },
                    { "bSortable": false },
                    { "bSortable": false, "sClass": "text-right" },
                    { "bSortable": false, "sClass": "text-right" },
                    { "bSortable": false }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('purchasing/po/ajax_modal_pr_detail_list');?>/" + pr_id + "/" + pr_detail_id_exist + '/1' // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_pr_detail_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }

    $(".table-action-export-excel").live('click', function (e) {
        var title = $('#pr_code').val();
        $('#datatable_pr_detail').tableExport({type:'excel',escape:'false', tableName:'export_pr_item', tableTitle:title});

        e.preventDefault();
    });

    $('.btn-disapprove').live('click', function(){
        var id = $(this).attr('data-id');
        var action = $(this).attr('data-action');
        var doc_no = $(this).attr('data-title');

        bootbox.confirm({
            message: "Disapprove Purchase Request " + doc_no + " ?",
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
                console.log(result);
                if(result === false){
                    //console.log('Empty reason');
                }else{
                    //console.log(result);
                    Metronic.blockUI({
                        boxed: true
                    });

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('purchasing/pr/action_request');?>",
                        dataType: "json",
                        data: { pr_id: id, action: action }
                    })
                        .done(function( msg ) {
                            Metronic.unblockUI();
                            if(msg.type == '0' || msg.type == '1'){
                                //console.log(msg.type);
                                grid.getDataTable().ajax.reload();
                                grid.clearAjaxParams();

                                if(msg.type == '1'){
                                    toastr["success"](msg.message, "Success");
                                } else {
                                    toastr["warning"](msg.message, "Warning");
                                }
                            } else {
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            }
                        });
                }
            }
        });
    });
});

</script>