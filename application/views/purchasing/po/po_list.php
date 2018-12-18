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
                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                <!-- Begin: life time stats -->
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users"></i>PO List
                        </div>
                        <div class="actions">
                            <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                <a href="<?php echo base_url('purchasing/po/po_form/0.tpd');?>" class="btn default yellow-stripe">
                                    <i class="fa fa-plus"></i>
                                  <span class="hidden-480">
                                  New PO </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container table-responsive">
                            <div class="col-md-12" style="padding-bottom: 90px;">
                                <table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th width="1%"> # </th>
                                        <th width="14%"> PO Code </th>
                                        <th width="10%"> PO Date </th>
                                        <th width="10%"> PO Delivery Date </th>
                                        <th width="15%" class="text-center"> Supplier </th>
                                        <!--th width="10%" class="text-center"> Item </th-->
                                        <th width="15%"> PR Code </th>
                                        <th width="8%"> Status </th>
                                        <th width="8%"> Action </th>
                                    </tr>
                                    <tr role="row" class="filter bg-grey-steel">
                                        <td>&nbsp;</td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_pono">
                                        </td>
                                        <td>
                                            <input type="text" class="text-center form-control form-filter input-sm date date-picker margin-bottom-5" readonly name="filter_po_date_from" placeholder="From" data-date-format="dd-mm-yyyy">
                                            <input type="text" class="text-center form-control form-filter input-sm date date-picker" readonly name="filter_po_date_to" placeholder="To" data-date-format="dd-mm-yyyy">
                                        </td>
                                        <td>
                                            <input type="text" class="text-center form-control form-filter input-sm date date-picker margin-bottom-5" readonly name="filter_po_delivery_date_from" placeholder="From" data-date-format="dd-mm-yyyy">
                                            <input type="text" class="text-center form-control form-filter input-sm date date-picker" readonly name="filter_po_delivery_date_to" placeholder="To" data-date-format="dd-mm-yyyy">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_supplier_name">
                                        </td>
                                        <!--td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_itemdesc">
                                        </td-->
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_prno">
                                        </td>
                                        <td>
                                            <select name="filter_status" class="form-control form-filter input-sm select2me">
                                                <option value="">All</option>
                                                <option value="<?php echo STATUS_NEW;?>">New</option>
                                                <option value="<?php echo STATUS_APPROVE;?>">Approved</option>
                                                <option value="<?php echo STATUS_DISAPPROVE;?>">Disapproved</option>
                                                <option value="<?php echo STATUS_CLOSED;?>">Closed</option>
                                                <option value="<?php echo STATUS_CANCEL;?>">Canceled</option>
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

    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            rtl: Metronic.isRTL(),
            orientation: "right",
            autoclose: true
        });
        //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
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
                    { "sClass": "text-center" },
                    { "sClass": "text-center" },
                    null,
                    { "sClass": "text-center" },
                    { "sClass": "text-center" },
                    { "bSortable": false, "sClass": "text-center" }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('purchasing/po/po_list/'. get_menu_id());?>" // ajax source
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

    $('.btn-action-doc').live('click', function(){
        var po_id = $(this).attr('data-id');
        var po_code = $(this).attr('data-code');
        var action = $(this).attr('data-action');
        var action_code = $(this).attr('data-action-code');

        if(action == '<?php echo STATUS_CANCEL;?>'){
            bootbox.prompt({
                title: "Please enter Cancel reason for " + po_code + " :",
                value: "",
                buttons: {
                    cancel: {
                        label: "Cancel",
                        className: "btn-inverse"
                    },
                    confirm:{
                        label: "OK",
                        className: "btn-primary"
                    }
                },
                callback: function(result) {
                    if(result === null){ }
                    else if(result.length <= 5){
                        toastr["warning"]("Cancel reason must be filled to proceed, Minimum 5 character.", "Warning");
                    } else {
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('purchasing/po/ajax_po_action');?>",
                            dataType: "json",
                            data: {po_id: po_id, action: action, reason:result }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();

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
                                Metronic.unblockUI();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                }
            });
        } else if(action == '<?php echo STATUS_CLOSED;?>'){
            bootbox.prompt({
                title: "Please enter Complete reason for " + po_code + " :",
                value: "",
                buttons: {
                    cancel: {
                        label: "Cancel",
                        className: "btn-inverse"
                    },
                    confirm:{
                        label: "OK",
                        className: "btn-primary"
                    }
                },
                callback: function(result) {
                    if(result === null){ }
                    else if(result.length <= 5){
                        toastr["warning"]("Complete reason must be filled to proceed, Minimum 5 character.", "Warning");
                    } else {
                        //console.log(result);
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('purchasing/po/ajax_po_action');?>",
                            dataType: "json",
                            data: {po_id: po_id, action: action, reason:result }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();

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
                                Metronic.unblockUI();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                }
            });
        } else {
            bootbox.confirm("Are you sure want to " + action_code + " " + po_code + " ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        boxed: true
                    });

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('purchasing/po/ajax_po_action');?>",
                        dataType: "json",
                        data: {po_id: po_id, action: action}
                    })
                        .done(function (msg) {
                            Metronic.unblockUI();

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
                            Metronic.unblockUI();
                            toastr["error"]("Something has wrong, please try again later.", "Error");
                        });
                }
            });
        }
    });

    handleRecords();

    $('.btn-print').live('click', function() {
        var id = parseInt($(this).attr('data-id')) || 0;

        if (id > 0) {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('purchasing/po/ajax_check_coa');?>",
                dataType: "json",
                data: {po_id: id }
            })
                .done(function( msg ) {
                    if (msg.valid == '0' || msg.valid == '1') {
                        if (msg.valid == '1') {
                            window.open('<?php echo base_url("purchasing/po/pdf_po");?>/' + id + '.tpd', "_blank");
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
});
</script>