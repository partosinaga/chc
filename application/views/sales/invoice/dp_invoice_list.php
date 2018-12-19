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
                            <i class="fa fa-list"></i>Invoice List
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container table-responsive">
                            <div class="col-md-12" style="padding-bottom: 90px;">
                                <table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th width="1%"> # </th>
                                        <th>Inv No.</th>
                                        <th>Date</th>
                                        <th>Due Date</th>
                                        <th>Customer</th>
                                        <th>SO No.</th>
                                        <th>Grand Total</th>
                                        <th>Invoice Type</th>
                                        <th width="8%">Status</th>
                                        <th width="8%">#</th>
                                    </tr>
                                    <tr role="row" class="filter bg-grey-steel">
                                        <td>&nbsp;</td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_inv_no">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm date date-picker margin-bottom-5" readonly name="filter_date_from" placeholder="From" data-date-format="dd-mm-yyyy">
                                            <input type="text" class="form-control form-filter input-sm date date-picker" readonly name="filter_date_to" placeholder="To" data-date-format="dd-mm-yyyy">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm date date-picker margin-bottom-5" readonly name="filter_due_date_from" placeholder="From" data-date-format="dd-mm-yyyy">
                                            <input type="text" class="form-control form-filter input-sm date date-picker" readonly name="filter_due_date_to" placeholder="To" data-date-format="dd-mm-yyyy">
                                        </td>

                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_customer">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_so_no">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_total">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-filter input-sm" name="filter_inv_type">
                                        </td>
                                        <td>
                                            <select name="filter_status" class="form-control form-filter input-sm select2me">
                                                <option value=""> -- Select --</option>
                                                <option value="<?php echo STATUS_NEW ?>">New</option>
                                                <option value="<?php echo STATUS_POSTED ?>">Posting</option>
                                                <option value="<?php echo STATUS_CLOSED ?>">Closed</option>
                                                <option value="<?php echo STATUS_CANCEL ?>">Cancel</option>
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
                        {"sClass": "text-center" },
                        {"sClass": "text-center" },
                        {"sClass": "text-center" },
                        null,
                        { "sClass": "text-center" },
                        { "sClass": "text-right" },
                        { "sClass": "text-center" },
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
                        "url": "<?php echo base_url('sales/dp_invoice/inv_ajax_list/'.get_menu_id());?>" // ajax source
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


        $('.btn-action').live('click', function () {
            var so_code = $(this).attr('data-code');
            var inv_id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var action_code = $(this).attr('data-action-code');
            if( action == <?php echo STATUS_POSTED ?>){
                bootbox.confirm("Are you sure want to " + action_code + " " + so_code + " ?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            target: '.portlet-body',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('sales/dp_invoice/posted_ajax_action');?>",
                                dataType: "json",
                                data: {inv_id: inv_id}
                            })
                            .done(function (msg) {
                                $('.portlet-body').unblock();

                                if (msg.valid == '0' || msg.valid == '1') {
                                    if (msg.valid == '1') {
                                        window.location.assign(msg.link);
                                    } else {
                                        toastr["error"](msg.message, "Error");
                                    }
                                } else {
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                }
                            })
                            .fail(function () {
                                $('.portlet-body').unblock();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                });
            } else if( action == <?php echo STATUS_CANCEL ?> ) {
                bootbox.prompt({
                    title: "Please enter canceled reason for " + so_code + " :",
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
                            toastr["warning"]("Closed reason must be filled to proceed, Minimum 5 character.", "Warning");
                        } else {
                            Metronic.blockUI({
                                target: '.portlet-body',
                                boxed: true,
                                message: 'Processing...'
                            });

                            $.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('sales/dp_invoice/cancel_ajax_action');?>",
                                    dataType: "json",
                                    data: {inv_id: inv_id, reason:result}
                                })
                                .done(function( msg ) {
                                    $('.portlet-body').unblock();

                                    if (msg.valid == '0' || msg.valid == '1') {
                                        if (msg.valid == '1') {
                                            window.location.assign(msg.link);
                                        } else {
                                            toastr["error"](msg.message, "Error");
                                        }
                                    } else {
                                        toastr["error"]("Something has wrong, please try again later.", "Error");
                                    }
                                })
                                .fail(function () {
                                    $('.portlet-body').unblock();
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                });
                        }
                    }
                });
            }
        })


    })

</script>