<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();
if($adj_id > 0) {
    if($row->status == STATUS_NEW){
        if (check_session_action(get_menu_id(), STATUS_EDIT)) {
            $btn_action .= $btn_save;
        } else {
            $isedit = false;
        }
        if (check_session_action(get_menu_id(), STATUS_POSTED)) {
            $btn_action .= btn_action($adj_id, $row->adj_code, STATUS_POSTED);
        }
        if (check_session_action(get_menu_id(), STATUS_CANCEL)) {
            $btn_action .= btn_action($adj_id, $row->adj_code, STATUS_CANCEL);
        }
        if(check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(base_url('inventory/adjustment/pdf_adj/' . $adj_id . '.tpd'));
        }
    } else {
        $isedit = false;
        if ($row->status == STATUS_POSTED) {
            if(check_session_action(get_menu_id(), STATUS_PRINT)) {
                $btn_action .= btn_print(base_url('inventory/adjustment/pdf_adj/' . $adj_id . '.tpd'));
            }
        }
    }
} else {
    $btn_action .= $btn_save;
}
?>

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
                <div class="portlet box <?php echo BOX;?>">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i><?php echo ($adj_id > 0 ? 'View' : 'New');?> Stock Adjustment
                            <?php
                            if($adj_id > 0){
                                echo '&nbsp;&nbsp;&nbsp;' . get_status_name($row->status);
                            }
                            ?>
                        </div>
                        <div class="actions">
                            <?php
                            $back_url = base_url('inventory/adjustment/adj_manage.tpd');
                            if ($adj_id > 0) {
                                if ($row->status != STATUS_NEW) {
                                    $back_url = base_url('inventory/adjustment/adj_history.tpd');
                                }
                            }
                            echo btn_back($back_url);
                            ?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="input_form" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" name="adj_id" value="<?php echo $adj_id;?>" />
                            <div class="form-actions top">
                                <div class="row">
                                    <div class="col-md-9">
                                        <?php echo $btn_action;?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body" id="form-entry">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Adj. Code</label>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" value="<?php echo ($adj_id > 0 ? $row->adj_code : '');?>" disabled />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Adj. Date</label>
                                            <div class="col-md-3">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="adj_date" value="<?php echo ($adj_id > 0 ? ymd_to_dmy($row->adj_date) : date('d-m-Y'));?>" readonly>
													<span class="input-group-btn">
														<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-10">
                                                <textarea class="form-control" rows="2" name="remarks"><?php echo ($adj_id > 0 ? $row->remarks : '');?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="margin-bottom: 10px;">
                                        <?php echo btn_add_detail(); ?>
                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-striped table-hover table-bordered" id="datatable_detail">
                                            <thead>
                                                <tr>
                                                    <th width="180px" class="text-center"> Item Code </th>
                                                    <th class="text-center"> Description </th>
                                                    <th width="80px" class="text-center"> UOM </th>
                                                    <th width="100px" class="text-center"> Qty </th>
                                                    <th width="100px" class="text-center"> Price </th>
                                                    <th width="120px" class="text-center"> Adj Qty </th>
                                                    <th width="120px" class="text-center"> Adj Price </th>
                                                    <th width="30px" class="text-center"> Action </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if($adj_id > 0){
                                                $i = 0;
                                                foreach($qry_detail->result() as $row_detail){
                                                    $append_html = '<tr>' .
                                                        '<input name="detail_id[' . $i . ']" type="hidden" value="' . $row_detail->detail_id . '" />' .
                                                        '<input class="class_status" name="status[' . $i . ']" type="hidden" value="1" />' .
                                                        '<td>' .
                                                        '<div class="input-group">' .
                                                        '<input name="item_id[' . $i . ']" class="is_item_id item_id_' . $i . '" type="hidden" value="' . $row_detail->item_id . '" />' .
                                                        '<input class="form-control input-sm item_code_' . $i . '" type="text" value="' . $row_detail->item_code . '" readonly />' .
                                                        '<span class="input-group-btn">' .
                                                        '<a class="btn btn-success input-sm lookup_item" data-index="' . $i . '" href="javascript:;"><i class="fa fa-arrow-up fa-fw"></i></a>' .
                                                        '</span>' .
                                                        '</div>' .
                                                        '</td>' .
                                                        '<td class="padding-top-13"><span class="item_desc_' . $i . '">' . $row_detail->item_desc . '</span></td>' .
                                                        '<td align="center" class="padding-top-13"><span class="uom_code_' . $i . '">' . $row_detail->uom_code . '</span></td>' .
                                                        '<td align="right" class="padding-top-13">' .
                                                        '<input name="qty[' . $i . ']" type="hidden" value="' . $row_detail->qty . '" />' .
                                                        '<span class="mask_currency qty_' . $i . '">' . $row_detail->qty . '</span>' .
                                                        '</td>' .
                                                        '<td align="right" class="padding-top-13">' .
                                                        '<input name="price[' . $i . ']" type="hidden" value="' . $row_detail->price . '" />' .
                                                        '<span class="mask_currency price_' . $i . '">' . $row_detail->price . '</span>' .
                                                        '</td>' .
                                                        '<td><input class="form-control mask_currency input-sm adj_qty_' . $i . '" type="text" data-index="' . $i . '" name="adj_qty[' . $i . ']" value="' . $row_detail->adj_qty . '" /></td>' .
                                                        '<td><input class="form-control mask_currency input-sm adj_price_' . $i . '" type="text" data-index="' . $i . '" name="adj_price[' . $i . ']" value="' . $row_detail->adj_price . '" /></td>' .
                                                        '<td align="center" class="padding-top-13"><a class="btn btn-xs red tooltips btn-delete" href="javascript:;" data-container="body" data-placement="top" data-original-title="Delete" data-index="' . $i . '" style="margin-right:0px;"><i class="fa fa-remove"></i></a></td>' .
                                                        '</tr>';

                                                    echo $append_html;
                                                    $i++;
                                                }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- END FORM-->
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                if($adj_id > 0){
                                    $log = '';
                                    $modified = '';
                                    $qry_log = $this->mdl_general->get('app_log', array('feature_id' => Feature::FEATURE_STOCK_ADJUSTMENT, 'reff_id' => $adj_id), array(), 'log_id asc');
                                    if($qry_log->num_rows() > 0){
                                        $log .= '<ul class="list-unstyled" style="margin-left:-15px;">';
                                        foreach($qry_log->result() as $row_log){
                                            $remark = '';
                                            if(trim($row_log->remark) != ''){
                                                $remark = '<h4 style="margin-left:10px;"><span class="label label-success">Remark : ' . trim($row_log->remark) . '</span></h4>';
                                            }
                                            $log .= '<li class="margin-bottom-5"><h6>' . $row_log->log_subject  . ' on ' . date_format(new DateTime($row_log->log_date), 'd/m/Y H:i:s') . ' by ' . get_user_fullname( $row_log->user_id ) . '</h6>' . $remark . '</li>';
                                        }
                                        $log .= '</ul>';
                                    }

                                    if ($row->user_modified > 0) {
                                        $modified .= "<div class='col-md-4'><h6>Last Modified on " . date_format(new DateTime($row->date_modified), 'd/m/Y H:i:s') . " by " . get_user_fullname( $row->user_modified ) . "</h6></div>" ;
                                    }
                                    echo '<div class="note note-info" style="margin:10px;">
                                                    <div class="col-md-8">
                                                        ' . $log . '
                                                    </div>
                                                    ' . $modified . '
                                                    <div style="clear:both;"></div>
                                                </div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal-item" class="modal fade" data-width="1024" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

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

    <?php
    if($adj_id > 0){
        echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
        echo picker_input_date(true, true, ymd_to_dmy($row->adj_date));
    } else {
        echo picker_input_date();
    }
    ?>

    var index_item = 0;

    var isedit = <?php echo ($isedit ? 0 : 1); ?>;
    if(isedit > 0){
        $('#form-entry').block({
            message: null ,
            overlayCSS: {backgroundColor: '#EDF5EB', opacity:0, cursor:'default'}
        });
    }

    function init_page() {
        $(".mask_currency").inputmask("decimal", {
            radixPoint: ".",
            groupSeparator: ",",
            digits: 2,
            autoGroup: true,
            autoUnmask: true,
            allowPlus: false,
            allowMinus: false
        });

        $(".mask_number").inputmask({
            "mask": "9",
            "repeat": 10,
            "greedy": false
        });

        $('.tooltips').tooltip();

        $('select.select2me').select2();

        autosize($('textarea'));
    }
    init_page();

    $('#btn_add_detail').live('click', function (e) {
        e.preventDefault();

        var index = $('#datatable_detail tbody tr').length;

        var append_html = '<tr>' +
            '<input name="detail_id[' + index + ']" type="hidden" value="0" />' +
            '<input class="class_status" name="status[' + index + ']" type="hidden" value="1" />' +
            '<td>' +
                '<div class="input-group">' +
                    '<input name="item_id[' + index + ']" class="is_item_id item_id_' + index + '" type="hidden" value="0" />' +
                    '<input class="form-control input-sm item_code_' + index + '" type="text" readonly />' +
                    '<span class="input-group-btn">' +
                        '<a class="btn btn-success input-sm lookup_item" data-index="' + index + '" href="javascript:;"><i class="fa fa-arrow-up fa-fw"></i></a>' +
                    '</span>' +
                '</div>' +
            '</td>' +
            '<td class="padding-top-13"><span class="item_desc_' + index + '"></span></td>' +
            '<td align="center" class="padding-top-13"><span class="uom_code_' + index + '"></span></td>' +
            '<td align="right" class="padding-top-13">' +
                '<input name="qty[' + index + ']" type="hidden" value="0" />' +
                '<span class="mask_currency qty_' + index + '"></span>' +
            '</td>' +
            '<td align="right" class="padding-top-13">' +
                '<input name="price[' + index + ']" type="hidden" value="0" />' +
                '<span class="mask_currency price_' + index + '"></span>' +
            '</td>' +
            '<td><input class="form-control mask_currency input-sm adj_qty_' + index + '" type="text" data-index="' + index + '" name="adj_qty[' + index + ']" value="0" /></td>' +
            '<td><input class="form-control mask_currency input-sm adj_price_' + index + '" type="text" data-index="' + index + '" name="adj_price[' + index + ']" value="0" /></td>' +
            '<td align="center" class="padding-top-13"><a class="btn btn-xs red tooltips btn-delete" href="javascript:;" data-container="body" data-placement="top" data-original-title="Delete" data-index="' + index + '" style="margin-right:0px;"><i class="fa fa-remove"></i></a></td>' +
        '</tr>';

        $('#datatable_detail tbody').append(append_html);

        init_page();
    });

    $('.num_qty').live('keyup', function(){
        var val = $(this).val().trim();
        var val_num = parseFloat(val) || 0;
        var index = $(this).attr('data-index');
        var on_hand_qty = parseFloat($('.on_hand_qty_' + index).val()) || 0;

        if(val_num > on_hand_qty){
            toastr.clear();

            $(this).val(on_hand_qty);
            toastr["warning"]("Item Qty can not bigger than On Hand Qty.", "Warning!");
        }
    });

    var $modal_item = $('#ajax-modal-item');
    $('.lookup_item').live('click', function(e) {
        e.preventDefault();

        var num_index = $(this).attr('data-index');

        var item_id_exist = '-';
        var n = 0;
        $('#datatable_detail tbody tr').each(function () {
            if ($(this).hasClass('hide') == false) {
                var item_id = parseInt($(this).find("input.is_item_id").val()) || 0;
                if (item_id > 0) {
                    if(item_id_exist == '-'){
                        item_id_exist = '';
                    }
                    if(n > 0) {
                        item_id_exist += '_';
                    }

                    item_id_exist += item_id;
                    n++;
                }
            }
        });

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';

        $('body').modalmanager('loading');

        setTimeout(function () {
            $modal_item.load('<?php echo base_url('inventory/adjustment/ajax_ms_item');?>', '', function () {

                $modal_item.modal();

                handleTableItem(item_id_exist, num_index);

                $.fn.modalmanager.defaults.resize = true;

                if ($modal_item.hasClass('bootbox') == false) {
                    $modal_item.addClass('modal-fix');
                }

                if ($modal_item.hasClass('modal-overflow') == false) {
                    $modal_item.addClass('modal-overflow');
                }

                $modal_item.css({'margin-top': '0px'})

            });
        }, 100);
    });

    var grid_item = new Datatable();
    var handleTableItem = function (item_id_exist, num_index) {
        // Start Datatable Item
        grid_item.init({
            src: $("#datatable_item"),
            onSuccess: function (grid_item) {
                // execute some code after table records loaded
            },
            onError: function (grid_item) {
                // execute some code on network or other general error
            },
            onDataLoad: function(grid_item) {
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
                    null,
                    { "sClass": "text-center" },
                    { "sClass": "text-right" },
                    { "sClass": "text-right" },
                    { "bSortable": false, "sClass": "text-center" }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('inventory/adjustment/ajax_ms_item_list');?>/" + item_id_exist + "/" + num_index
                },
                "drawCallback": function( settings ) {
                    init_page();
                }
            }
        });

        var tableWrapper = $("#datatable_item_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        }); // initialize select2 dropdown
    }

    $('.btn-select-item').live('click', function(e) {
        e.preventDefault();

        var i = $(this).attr('data-index');
        var item_id = $(this).attr('data-id');
        var item_code = $(this).attr('data-code');
        var item_desc = $(this).attr('data-desc');
        var uom = $(this).attr('data-uom');
        var qty = $(this).attr('data-qty');
        var price = $(this).attr('data-price');

        $('.item_code_' + i).val(item_code);
        $('.item_id_' + i).val(item_id);
        $('.item_desc_' + i).html(item_desc);
        $('.uom_code_' + i).html(uom);
        $('input[name="qty[' + i + ']"]').val(qty);
        $('.qty_' + i).html(qty);
        $('input[name="price[' + i + ']"]').val(price);
        $('.price_' + i).html(price);
        $('.adj_qty_' + i).val(qty);
        $('.adj_price_' + i).val(price);

        $modal_item.modal('hide');

        init_page();
    });

    function set_flag_delete(num_index) {
        if (num_index == 0) {
            $('#datatable_detail tbody tr').each(function () {
                if ($(this).hasClass('hide') == false) {
                    $(this).addClass('hide');
                }
            });

            $('#datatable_detail tbody tr input').each(function () {
                if ($(this).hasClass('class_status')) {
                    $(this).val('9');
                }
            });
        }
        else {
            if ($('#datatable_detail tbody tr:nth-child(' + num_index + ')').hasClass('hide') == false) {
                $('#datatable_detail tbody tr:nth-child(' + num_index + ')').addClass('hide');
            }
            $('#datatable_detail tbody tr:nth-child(' + num_index + ') input.class_status').val('9');
        }
    }

    //SUBMIT
    $('#input_form').on('submit', function(){
        toastr.clear();

        if(validate_submit()) {
            Metronic.blockUI({
                target: '#input_form',
                boxed: true,
                message: 'Processing...'
            });


            var btn = $(this).find("button[type=submit]:focus");

            var next = true;
            toastr.clear();

            var form_data = $('#input_form').serializeArray();
            if (btn[0] == null) {
            } else {
                if (btn[0].name === 'save_close') {
                    form_data.push({name: "save_close", value: 'save_close'});
                }
            }

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('inventory/adjustment/ajax_adj_submit');?>",
                dataType: "json",
                data: form_data
            })
                .done(function (msg) {
                    if (msg.valid == '1') {
                        window.location.assign(msg.link);
                    } else {
                        toastr["error"](msg.message, "Error");
                        $('#input_form').unblock();
                    }
                })
                .fail(function () {
                    $('#input_form').unblock();
                    toastr["error"]("Something has wrong, please try again later.", "Error");
                });
        }
    });

    function validate_submit(){
        var result = true;

        if($('.form-group').hasClass('has-error')){
            $('.form-group').removeClass('has-error');
        }
        if($('td').hasClass('has-error')){
            $('td').removeClass('has-error');
        }

        var adj_date = $('input[name="adj_date"]').val().trim();
        var remarks = $('textarea[name="remarks"]').val().trim();

        if(adj_date == ''){
            toastr["warning"]("Please select Adjustment Date.", "Warning");
            $('input[name="adj_date"]').closest('.form-group').addClass('has-error');
            result = false;
        }
        if(remarks == ''){
            toastr["warning"]("Please input Remarks.", "Warning");
            $('textarea[name="remarks"]').closest('.form-group').addClass('has-error');
            result = false;
        }

        var i = 0;
        var i_act = 0;
        $('#datatable_detail > tbody > tr ').each(function() {
            if(!$(this).hasClass('hide')) {
                var item_id = parseInt($('.item_id_' + i).val()) || 0;
                var item_desc_ = $('.item_desc_' + i).val();
                var qty = parseFloat($('input[name="qty[' + i + ']"]').val()) || 0;
                var price = parseFloat($('input[name="price[' + i + ']"]').val()) || 0;
                var adj_qty = parseFloat($('input[name="adj_qty[' + i + ']"]').val()) || 0;
                var adj_price = parseFloat($('input[name="adj_price[' + i + ']"]').val()) || 0;

                if (item_desc_ != '') {
                    var item_desc = ' for ' + item_desc_;
                } else {
                    var item_desc = '';
                }

                if(item_id < 1){
                    toastr["warning"]("Please select Item.", "Warning");
                    $('.item_id_' + i).closest('td').addClass('has-error');
                    result = false;
                }
                if(qty == adj_qty && price == adj_price){
                    toastr["warning"]("Adjustment Qty and Price is same.", "Warning");
                    $('input[name="adj_qty[' + i + ']"]').closest('td').addClass('has-error');
                    $('input[name="adj_price[' + i + ']"]').closest('td').addClass('has-error');
                    result = false;
                }
                if(adj_qty == 0 && adj_price > 0) {
                    toastr["warning"]("Adjustment Qty cannot be 0 (Zero).", "Warning");
                    $('input[name="adj_qty[' + i + ']"]').closest('td').addClass('has-error');
                    result = false;
                }
                if(adj_price == 0 && adj_qty > 0) {
                    toastr["warning"]("Adjustment Price cannot be 0 (Zero).", "Warning");
                    $('input[name="adj_price[' + i + ']"]').closest('td').addClass('has-error');
                    result = false;
                }
                i_act++;
            }
            i++;
        });

        if(i_act <= 0 ){
            toastr["warning"]("Detail cannot be empty.", "Warning");
            result = false;
        }

        return result;
    }

    $('.btn-delete').live('click', function(){
        var item_index = $(this).attr('data-index');
        bootbox.confirm("Are you sure want to delete?", function(result) {
            if(result == true){
                set_flag_delete(item_index);
            }
        });
    });

    $('.btn-action').live('click', function(){
        var id = $(this).attr('data-id');
        var action = $(this).attr('data-action');
        var code = $(this).attr('data-code');

        if(action == '<?php echo STATUS_POSTED;?>') {
            bootbox.confirm("Are you sure want to Posting " + code + " ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        boxed: true
                    });

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('inventory/adjustment/ajax_adj_action');?>",
                        dataType: "json",
                        data: {adj_id: id, action: action, is_redirect: true}
                    })
                        .done(function (msg) {
                            Metronic.unblockUI();
                            if (msg.valid == '1') {
                                location.href = '<?php echo base_url('inventory/adjustment/adj_history/1/' . $adj_id . '.tpd');?>';
                            } else if (msg.valid == '0'){
                                toastr["error"](msg.message, "Error");
                            }
                        })
                        .fail(function () {
                            Metronic.unblockUI();
                            toastr["error"]("Something has wrong, please try again later.", "Error");
                        });

                }
            });
        } else if(action == '<?php echo STATUS_CANCEL;?>'){
            bootbox.prompt({
                title: "Please enter Reason for Cancel " + code + " :",
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
                        toastr["warning"]("Reason must be filled to proceed, Minimum 5 character.", "Warning");
                    } else {
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('inventory/adjustment/ajax_adj_action');?>",
                            dataType: "json",
                            data: {adj_id: id, action: action, reason:result, is_redirect: true }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();

                                if (msg.valid == '1') {
                                    location.href = '<?php echo base_url('inventory/adjustment/adj_history/1/' . $adj_id . '.tpd');?>';
                                } else if (msg.valid == '0') {
                                    toastr["error"](msg.message, "Error");
                                }
                            })
                            .fail(function () {
                                Metronic.unblockUI();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                }
            });
        }
    });

});
</script>