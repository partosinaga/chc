<?php
$btn_action = '';
$btn_save = btn_save() . btn_save_close();

$btn_action .= $btn_save;
?>
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
        <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class')); ?>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box <?php echo BOX; ?>">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cube"></i> <?php echo($package_id > 0 ? 'Edit' : 'New'); ?> Package
                        </div>
                        <div class="actions">
                            <?php echo btn_back(base_url('sales/setup/package.tpd')); ?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="form-entry" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" class="form-control" name="package_id" value="<?php echo $package_id; ?>"/>
                            <div class="form-actions top">
                                <div class="row">
                                    <div class="col-md-9">
                                        <?php echo $btn_action; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body" id="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Package Code<span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="code" value="<?php echo($package_id > 0 ? $row->package_group_code : ''); ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Description<span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="desc" value="<?php echo($package_id > 0 ? $row->package_group_desc : ''); ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Package Price<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control mask_currency" name="price" value="<?php echo($package_id > 0 ? $row->price : ''); ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Notes</label>

                                            <div class="col-md-8">
                                                <textarea class="form-control" rows="2" name="notes"><?php echo($package_id > 0 ? $row->notes : ''); ?></textarea>
                                            </div>
                                        </div>
                                        <?php
                                        if ($package_id > 0) {
                                            echo '<div class="form-group">
                                                <label class="control-label col-md-4">Status<span class="required" aria-required="true"> * </span></label>

                                                <div class="col-md-8">
                                                     <select name="status_header" class="form-control form-filter select2me">
                                                        <option value="' . STATUS_NEW . '"  ' . ($row->status == STATUS_NEW ? 'selected="selected"' : '') . ' >Active</option>
                                                        <option value="' . STATUS_INACTIVE . '"  ' . ($row->status == STATUS_INACTIVE ? 'selected="selected"' : '') . ' >Inactive</option>
                                                     </select>
                                                </div>
                                            </div>';
                                        }

                                        ?>
                                    </div>
                                </div>
                                <div class="portlet-body ">
                                    <div class="portlet-title">
                                        <div class="actions" style="margin-bottom: 10px;">
                                            <a class="btn default green-seagreen btn-sm yellow-stripe"
                                               id="btn_lookup_items"> <i class="fa fa-plus"></i> Add Detail </a>
                                        </div>
                                    </div>
                                    <table class="table  table-striped table-hover table-bordered" id="items-table">
                                        <thead>
                                        <tr>
                                            <th width="10%" class="text-center">Item Code</th>
                                            <th>Item Description</th>
                                            <th width="10%" class="text-center">UOM</th>
                                            <th width="10%" class="text-right">QTY</th>
                                            <th width="4%" class="text-center">#</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if($package_id > 0){
                                            foreach($row_detail->result() as $detail){
                                                echo '<tr>
                                                    <td align="center"><input type="hidden" class="form-control input-sm text-right" name="item_id[]" value="'.$detail->item_id.'" min="0">'.$detail->item_code.'</td>
                                                    <td>'.$detail->item_desc.'</td>
                                                    <td align="center">'.$detail->uom_code.'</td>
                                                    <td align="right"><input type="number" class="form-control input-sm text-right" name="qty[]" value="'.$detail->item_qty.'" min="0"></td>
                                                    <td>
                                                    <select name="status[]" class="form-control form-filter select2me input-sm">
                                                        <option value="' . STATUS_NEW . '"  ' . ($detail->status == STATUS_NEW ? 'selected="selected"' : '') . ' >Active</option>
                                                        <option value="' . STATUS_INACTIVE . '"  ' . ($detail->status == STATUS_INACTIVE ? 'selected="selected"' : '') . ' >Inactive</option>
                                                     </select>
                                                    </td>
                                                    </tr>';
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </form>
                        <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->
<!--CUSTOMER HEADER-->
<div id="customer-modal" class="modal fade" data-replace="true" data-width="900" data-keyboard="false"
     data-backdrop="static" tabindex="-1"></div>
<!--END OF CUSTOEMR HEADER-->
<script>
    //number format
    $(document).ready(function(){
        $('.mask_currency').inputmask("numeric", {
            radixPoint: ".",
            autoGroup: true,
            groupSeparator: ",",
            digits: 0,
            groupSize: 3,
            removeMaskOnSubmit: false,
            autoUnmask: true
        });
    })

    //end of number format

    var grid_req = new Datatable();
    var datatableItems = function () {
        grid_req.init({
            src: $("#item-datatable"),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            onDataLoad: function (grid) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Loading...',
            dataTable: {
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    {"bSortable": false},
                    {"bSortable": false, "sClass": "text-center"},
                    null,
                    {"bSortable": false, "sClass": "text-center"},
                    {"bSortable": false, "sClass": "text-right"},
                    {"bSortable": false, "sClass": "text-center"}
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": js_base_url + 'sales/setup/ajax_items_list' // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_request_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }
    var $modal = $('#customer-modal');
    $('#btn_lookup_items').live('click', function (e) {
        e.preventDefault();

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';

        $('body').modalmanager('loading');


        setTimeout(function () {
            $modal.load(js_base_url + 'sales/setup/ajax_modal_items', '', function () {

                    $modal.modal();
                    datatableItems();
                    $.fn.modalmanager.defaults.resize = true;
                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }
                    if ($modal.hasClass('modal-overflow') == false) {
                        $modal.addClass('modal-overflow');
                    }
                    $modal.css({'margin-top': '0px'})


                }
            )
            ;
        }, 100);
    });
    //get selected item list
    var tbody = $('#items-table').children('tbody')
    var table = tbody.length ? tbody : $('#items-table');
    var status_new = <?php echo STATUS_NEW ?>;
    $('.select-item').live('click', function () {
        var item_id = $(this).attr('item-id');
        var description = $(this).attr('description');
        var item_code = $(this).attr('item-code');
        var uom = $(this).attr('uom');
        var newRowContent =
            "<tr>" +
            "<td align=\"center\"> <input type=hidden name=\"item_id[]\"  value= " + item_id + ">  " + item_code + " </td>" +
            "<td> " + description + " </td>" +
            "<td align=\"center\"> " + uom + " </td>" +
            "<td> <input type=hidden name=\"status[]\" class=\"form-control input input-sm text-right\" value=" + status_new + "> <input type=number name=\"qty[]\" class=\"form-control input input-sm text-right\" min=\"0\" > </td>" +
            "<td align=\"center\"> <button type=\"button\" class=\"btn btn-sm btn-danger remove-item\" item-id = " + item_id + " ><i class=\"fa fa-remove\"></i></button></td>" +
            "</tr>";

        //Add row
        $('#items-table tbody').append(newRowContent);
        $('#customer-modal').modal('hide');
    })
    //end of get selected item list
    // remove item row
    $('.remove-item').live('click', function () {
        var this_btn = $(this);
        bootbox.confirm("Are you sure want to delete?", function (result) {
            if (result == true) {
                this_btn.closest('tr').addClass('hide');
                this_btn.closest('tr').find('.class_status').val('9');
            }
        });
    });
    //end of remove item row

    $(document).ready(function(){
        //form validation
        function validate_submit(){
            var result = true;

            if($('.form-group').hasClass('has-error')){
                $('.form-group').removeClass('has-error');
            }

            var code = $('input[name="code"]').val().trim();
            var desc = $('input[name="desc"]').val().trim();
            var price = $('input[name="price"]').val().trim();

            if(code == ''){
                toastr["warning"]("Please enter code.", "Warning!");
                $('input[name="code"]').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (desc == '') {
                toastr["warning"]("Please enter description.", "Warning!");
                $('input[name="desc"]').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (price == '') {
                toastr["warning"]("Please enter price.", "Warning!");
                $('input[name="price"]').closest('.form-group').addClass('has-error');
                result = false;
            }
            return result;
        }
        //end of form validation
        //submit form
        $('#form-entry').on('submit', function () {
            Metronic.blockUI({
                target: '#form-entry',
                boxed: true,
                message: 'Processing...'
            });
            var btn = $(this).find("button[type=submit]:focus");


            if (validate_submit()) {
                var form_data = $('#form-entry').serializeArray();
                if (btn[0] == null) {
                } else {
                    if (btn[0].name === 'save_close') {
                        form_data.push({name: "save_close", value: 'save_close'});
                    }
                }

                $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('sales/setup/ajax_package_submit');?>",
                        dataType: "json",
                        data: form_data
                    })
                    .done(function (msg) {
                        window.location.assign(msg.link);
                    })
                    .fail(function () {
                        $('#form-entry').unblock();
                        toastr["error"]("Something has wrong, please try again later.", "Error");
                    });
            }else{
                $('#form-entry').unblock();
            }
        });
        //end of submit form
    })


</script>