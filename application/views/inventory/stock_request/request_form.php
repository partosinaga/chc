<?php
$isedit = true;

$btn_action = '';
$btn_save = '<button type="submit" class="btn blue-madison yellow-stripe" name="save"><i class="fa fa-save"></i> &nbsp;&nbsp;Save </button>';
$btn_save .= '&nbsp;<button type="submit" class="btn blue-madison yellow-stripe" name="save_close"><i class="fa fa-sign-in"></i> &nbsp;&nbsp;Save & Close </button>';

if($request_id > 0){
    if ($row->status == STATUS_NEW) {
        if (!check_session_action(get_menu_id(), STATUS_EDIT)) {
            $isedit = false;
        }
        if (check_session_action(get_menu_id(), STATUS_APPROVE)) {
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-bootbox btn-action" data-action="' . STATUS_APPROVE . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '"><i class="fa fa-thumbs-o-up"></i>&nbsp;&nbsp;' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '</a>';
        }
        if (check_session_action(get_menu_id(), STATUS_DISAPPROVE)) {
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-bootbox btn-action" data-action="' . STATUS_DISAPPROVE . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '"><i class="fa fa-thumbs-o-down"></i> &nbsp;&nbsp;' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '</a>';
        }
        if (check_session_action(get_menu_id(), STATUS_CANCEL)) {
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-bootbox btn-action" data-action="' . STATUS_CANCEL . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '"><i class="fa fa-exclamation-triangle "></i> &nbsp;&nbsp;' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a>';
        }
        if (check_session_action(get_menu_id(), STATUS_CLOSED)) {
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-bootbox btn-action" data-action="' . STATUS_CLOSED . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '"><i class="fa fa-check-square-o"></i> &nbsp;&nbsp; Complete</a>';
        }
    } else if ($row->status == STATUS_APPROVE) {
        $isedit = false;
        if (check_session_action(get_menu_id(), STATUS_DISAPPROVE)) {
            $btn_action .= '<a class="btn yellow-gold btn-bootbox btn-action" data-action="' . STATUS_DISAPPROVE . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '"><i class="fa fa-thumbs-o-up"></i> &nbsp;&nbsp;' . ucwords(strtolower(get_action_name(STATUS_DISAPPROVE, false))) . '</a>';
        }
        if (check_session_action(get_menu_id(), STATUS_CANCEL)) {
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-bootbox btn-action" data-action="' . STATUS_CANCEL . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '"><i class="fa fa-exclamation-triangle "></i> &nbsp;&nbsp;' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a>';
        }
        if (check_session_action(get_menu_id(), STATUS_CLOSED)) {
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-bootbox btn-action" data-action="' . STATUS_CLOSED . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '"><i class="fa fa-check-square-o"></i> &nbsp;&nbsp; Complete</a>';
        }
    } else if ($row->status == STATUS_DISAPPROVE) {
        if (!check_session_action(get_menu_id(), STATUS_EDIT)) {
            $isedit = false;
        }
        if (check_session_action(get_menu_id(), STATUS_APPROVE)) {
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-bootbox btn-action" data-action="' . STATUS_APPROVE . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '"><i class="fa fa-thumbs-o-up"></i> &nbsp;&nbsp;' . ucwords(strtolower(get_action_name(STATUS_APPROVE, false))) . '</a>';
        }
        if (check_session_action(get_menu_id(), STATUS_CANCEL)) {
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-bootbox btn-action" data-action="' . STATUS_CANCEL . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '"><i class="fa fa-exclamation-triangle "></i> &nbsp;&nbsp;' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a>';
        }
        if (check_session_action(get_menu_id(), STATUS_CLOSED)) {
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-bootbox btn-action" data-action="' . STATUS_CLOSED . '" data-id="' . $row->request_id . '" data-code="' . $row->request_code . '"><i class="fa fa-check-square-o"></i> &nbsp;&nbsp; Complete</a>';
        }
    } else if ($row->status == STATUS_CANCEL) {
        $isedit = false;
    } else if ($row->status == STATUS_CLOSED) {
        $isedit = false;
    }
}

$btn = ($isedit ? $btn_save : '') . $btn_action;
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
                            <i class="fa fa-user"></i><?php echo ($request_id > 0 ? 'View' : 'New');?> Stock Request
                            <?php
                            if($request_id > 0){
                                echo '&nbsp;&nbsp;&nbsp;' . get_status_name($row->status);
                            }
                            ?>
                        </div>
                        <div class="actions">
                            <?php echo btn_back(base_url('inventory/stock_request/stock_request_manage/1.tpd'));?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="input_form" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" name="request_id" value="<?php echo $request_id;?>" />
                            <div class="form-actions top">
                                <div class="row">
                                    <div class="col-md-9">
                                        <?php echo $btn;?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body" id="form-entry">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Request Code</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" value="<?php echo ($request_id > 0 ? $row->request_code : '');?>" disabled />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Request Date</label>
                                            <div class="col-md-6">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="request_date" value="<?php echo ($request_id > 0 ? ymd_to_dmy($row->request_date) : date('d-m-Y'));?>" readonly>
													<span class="input-group-btn">
														<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Department</label>
                                            <div class="col-md-7">
                                                <select class="form-control select2me" name="department_id">
                                                    <option value=""> -- Select -- </option>
                                                    <?php
                                                    foreach($qry_department->result() as $row_department){
                                                        $selected = '';
                                                        if($request_id > 0){
                                                            if($row->department_id == $row_department->department_id){
                                                                $selected = 'selected="selected"';
                                                            }
                                                        }
                                                        echo '<option value="' . $row_department->department_id . '" ' . $selected . '>' . $row_department->department_desc . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">&nbsp;</label>
                                            <div class="col-md-7">
                                                <div class="checkbox-list" style="margin-left: -5px;">
                                                    <label><input type="checkbox" class="form-control" value="1" name="is_pos" <?php echo ($request_id > 0 ? ($row->is_pos == '1' ? 'checked' : '') : '');?>/> Request to POS</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-10">
                                                <textarea class="form-control" rows="5" name="remarks"><?php echo ($request_id > 0 ? $row->remarks : '');?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="btn-group">
                                            <a class="btn green-turquoise btn-sm yellow-stripe add_detail"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Item</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-hover table-bordered" id="datatable_detail">
                                            <thead>
                                                <tr>
                                                    <th width="20%" class="text-center"> Item Code </th>
                                                    <th class="text-center"> Description </th>
                                                    <th width="10%" class="text-center"> UOM Issue </th>
                                                    <th width="10%" class="text-center"> Qty </th>
                                                    <th width="10%" class="text-center"> Qty Remain </th>
                                                    <th width="30px" class="text-center"> Action </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if($request_id > 0){
                                                $i = 1;

                                                foreach($qry_detail->result() as $row_detail){
                                                    echo '<tr>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input name="request_detail_id[' . $i . ']" type="hidden" value="' . $row_detail->request_detail_id . '" />
                                                                    <input class="class_status" name="status[' . $i . ']" type="hidden" value="1" />
                                                                    <input name="item_id[' . $i . ']" class="item_id_' . $i . '" type="hidden" value="' . $row_detail->item_id . '" />
                                                                    <input name="uom_id[' . $i . ']" class="uom_id_' . $i . '" type="hidden" value="' . $row_detail->uom_id . '" />
                                                                    <input name="on_hand_qty[' . $i . ']" class="on_hand_qty_' . $i . '" type="hidden" value="' . $row_detail->ms_on_hand_qty . '" />
                                                                    <input class="form-control input-sm item_code_' . $i . '" type="text" value="' . $row_detail->item_code . '" readonly />
                                                                    <span class="input-group-btn">
                                                                        <a class="btn btn-success input-sm lookup_item" data-index="' . $i . '" href="javascript:;"><i class="fa fa-arrow-up fa-fw"></i></a>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="padding-top-13"><span class="item_desc_' . $i . '">' . $row_detail->item_desc . '</span></td>
                                                            <td align="center" class="padding-top-13"><span class="uom_code_' . $i . '">' . $row_detail->uom_code . '</span></td>
                                                            <td><input class="form-control mask_number input-sm num_qty item_qty_' . $i . '" type="text" data-index="' . $i . '" name="item_qty[' . $i . ']" value="' . $row_detail->item_qty . '"></td>
                                                            <td align="right" class="padding-top-13"><span class="qty_remaining">' . $row_detail->item_qty_remain . '</span></td>
                                                            <td align="center" class="padding-top-13"><a class="btn btn-xs red tooltips btn-delete" href="javascript:;" data-container="body" data-placement="top" data-original-title="Delete" data-index="' . $i . '"><i class="fa fa-remove"></i></a></td>
                                                        </tr>';
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
                                if($request_id > 0){
                                    echo '<div class="note note-info" style="margin:10px;">';
                                    $qry_log = $this->mdl_general->get('app_log', array('feature_id' => Feature::FEATURE_STOCK_REQUEST, 'reff_id' => $request_id), array(), 'log_id asc');
                                    echo '<div class="col-md-8">';
                                    if($qry_log->num_rows() > 0){
                                        echo '<ul class="list-unstyled" style="margin-left:-15px;">';
                                        foreach($qry_log->result() as $row_log){
                                            $remark = '';
                                            if(trim($row_log->remark) != ''){
                                                $remark = '<h4 style="margin-left:10px;"><span class="label label-success">Remark : ' . trim($row_log->remark) . '</span></h4>';
                                            }
                                            echo '<li class="margin-bottom-5"><h6>' . $row_log->log_subject  . ' on ' . date_format(new DateTime($row_log->log_date), 'd/m/Y H:i:s') . ' by ' . get_user_fullname( $row_log->user_id ) . '</h6>' . $remark . '</li>';
                                        }
                                        echo '</ul>';
                                    }
                                    echo '</div>';
                                    if ($row->user_modified > 0) {
                                        echo "<div class='col-md-4'><h6>Last Modified by ".get_user_fullname( $row->user_modified )." (". date_format(new DateTime($row->date_modified), 'd/m/Y H:i:s') .")</h6></div>" ;
                                    }
                                    echo '<div style="clear:both;"></div>';
                                    echo '</div>';
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

<div id="ajax-modal-item" class="modal fade" data-width="760" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

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

    <?php
    if($request_id > 0){
        echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
            echo picker_input_date(true, true, ymd_to_dmy($row->request_date));
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

    function init_number(){
        $(".mask_number").inputmask("decimal", {
            radixPoint:".",
            groupSeparator: ",",
            digits: 2,
            autoGroup: true,
            autoUnmask: true
        });
    }

    init_number();

    autosize($('textarea'));

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
            }
            else {
                if (btn[0].name === 'save_close') {
                    form_data.push({name: "save_close", value: 'save_close'});
                }
            }

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('inventory/stock_request/ajax_stock_request_submit');?>",
                dataType: "json",
                data: form_data
            })
                .done(function (msg) {
                    if (msg.success == '1') {
                        window.location.assign(msg.link);
                    }
                    else {
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

        var request_date = $('input[name="request_date"]').val().trim();
        var dept_id = parseInt($('select[name="department_id"]').val().trim()) || 0;
        var remarks = $('textarea[name="remarks"]').val().trim();

        if(request_date == ''){
            toastr["warning"]("Please select Request Date.", "Warning");
            $('input[name="request_date"]').closest('.form-group').addClass('has-error');
            result = false;
        }
        if(dept_id == 0){
            toastr["warning"]("Please select Department.", "Warning");
            $('select[name="department_id"]').closest('.form-group').addClass('has-error');
            result = false;
        }
        if(remarks == ''){
            toastr["warning"]("Please input Remarks.", "Warning");
            $('textarea[name="remarks"]').closest('.form-group').addClass('has-error');
            result = false;
        }

        var i = 1;
        var i_act = 0;
        $('#datatable_detail > tbody > tr ').each(function() {
            if(!$(this).hasClass('hide')) {
                var item_id = parseInt($('.item_id_' + i).val()) || 0;
                var on_hand_qty = parseFloat($('.on_hand_qty_' + i).val()) || 0;
                var item_qty_val = $('.item_qty_' + i).val();
                var item_qty = parseFloat(item_qty_val) || 0;

                $('.item_code_' + i).removeClass('has-error');
                $('.item_qty_' + i).removeClass('has-error');

                if(item_id < 1){
                    toastr["warning"]("Please select Item.", "Warning");
                    $('.item_code_' + i).addClass('has-error');
                    result = false;
                }
                if(item_qty > on_hand_qty){
                    toastr["warning"]("Item Qty cannot bigger than On Hand Qty.", "Warning");
                    $('.item_qty_' + i).addClass('has-error');
                    result = false;
                }
                if(item_qty < 1){
                    toastr["warning"]("Please select Item Qty.", "Warning");
                    $('.item_qty_' + i).addClass('has-error');
                    result = false;
                }
                i_act++;
            }
            i++;
        });

        console.log(i_act);

        if(i_act <= 0 ){
            toastr["warning"]("Detail cannot be empty.", "Warning");
            result = false;
        }

        return result;
    }

    var grid_item = new Datatable();

    var handleTableItem = function () {
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
                    { "bSortable": false }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('inventory/stock_request/ajax_ms_item_list');?>" // ajax source
                },
                "drawCallback": function( settings ) {
                    Metronic.initUniform();

                    init_number();
                }
            }
        });

        var tableWrapper = $("#datatable_item_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        }); // initialize select2 dropdown

        // End Datatable Item

        // handle group actionsubmit button click
        grid_item.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            if (grid_item.getSelectedRowsCountRadio() > 0) {
                var result = grid_item.getSelectedRowsRadio();

                if(!check_item_exist(result[0], index_item)) {
                    var on_hand_qty = parseFloat(result[3]) || 0;
                    var qty = 1;
                    if(on_hand_qty < 1){
                        qty = on_hand_qty;
                    }

                    $('input.item_id_' + index_item).val(result[0]);
                    $('input.item_code_' + index_item).val(result[1]);
                    $('span.item_desc_' + index_item).html(result[2]);
                    $('span.uom_code_' + index_item).html(result[4]);
                    $('input.on_hand_qty_' + index_item).val(result[3]);
                    $('input.uom_id_' + index_item).val(result[5]);
                    $('input.item_qty_' + index_item).val(qty);

                    $('#ajax-modal-item').modal('hide');
                } else {
                    toastr.clear();
                    toastr["warning"]("Item already exist.", "Warning");
                }
            } else if (grid_item.getSelectedRowsCountRadio() === 0) {
                toastr.clear();
                toastr["warning"]("No item selected.", "Warning");
            }
        });
    }

    function check_item_exist(item_id, n){
        var result = false;
        var i = 1;
        $('#datatable_detail > tbody > tr ').each(function() {
            if (!$(this).hasClass('hide')) {
                var item_ids = parseInt($('.item_id_' + i).val()) || 0;

                if(i != n){
                    if(item_id == item_ids){
                        result = true;
                    }
                }
            }
            i++;
        });

        return result;
    }

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

    $('.add_detail').live('click', function (e) {
        e.preventDefault();

        var index = $('#datatable_detail tbody tr').length + 1;

        var append_html = '';
        append_html += '<tr><td><div class="input-group">';
        append_html +=      '<input name="request_detail_id[' + index + ']" type="hidden" value="0" />';
        append_html +=      '<input class="class_status" name="status[' + index + ']" type="hidden" value="1" />';
        append_html +=      '<input name="item_id[' + index + ']" class="item_id_' + index + '" type="hidden" value="0" />';
        append_html +=      '<input name="uom_id[' + index + ']" class="uom_id_' + index + '" type="hidden" value="0" />';
        append_html +=      '<input name="on_hand_qty[' + index + ']" class="on_hand_qty_' + index + '" type="hidden" value="0" />';
        append_html +=      '<input class="form-control input-sm item_code_' + index + '" type="text" readonly />';
        append_html +=      '<span class="input-group-btn">';
        append_html +=          '<a class="btn btn-success input-sm lookup_item" data-index="' + index + '" href="javascript:;"><i class="fa fa-arrow-up fa-fw"></i></a>';
        append_html +=      '</span></div></td>';
        append_html += '<td class="padding-top-13"><span class="item_desc_' + index + '"></span></td>';
        append_html += '<td align="center" class="padding-top-13"><span class="uom_code_' + index + '"></span></td>';
        append_html += '<td><input class="form-control mask_number input-sm num_qty item_qty_' + index + '" type="text" data-index="' + index + '" name="item_qty[' + index + ']"></td>';
        append_html += '<td align="right" class="padding-top-13"><span class="qty_remaining"></span></td>';
        append_html += '<td align="center" class="padding-top-13"><a class="btn btn-xs red tooltips btn-delete" href="javascript:;" data-container="body" data-placement="top" data-original-title="Delete" data-index="' + index + '"><i class="fa fa-remove"></i></a></td></tr>';

        $('#datatable_detail tbody').append(append_html);

        init_number();
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

        index_item = $(this).attr('data-index');

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';

        $('body').modalmanager('loading');

        setTimeout(function () {
            $modal_item.load('<?php echo base_url('inventory/stock_request/ajax_view_ms_item_list');?>', '', function () {

                $modal_item.modal();

                handleTableItem();

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

        if(action == '<?php echo STATUS_APPROVE;?>' || action == '<?php echo STATUS_DISAPPROVE;?>') {
            var act = '';
            if(action == '<?php echo STATUS_APPROVE;?>'){
                act = 'Approve';
            } else if(action == '<?php echo STATUS_DISAPPROVE;?>'){
                act = 'Disapprove';
            }
            bootbox.confirm("Are you sure want to " + act + " " + code + " ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        boxed: true
                    });

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('inventory/stock_request/ajax_action_request');?>",
                        dataType: "json",
                        data: {request_id: id, action: action, is_redirect: true}
                    })
                        .done(function (msg) {
                            Metronic.unblockUI();
                            if (msg.valid == '1') {
                                location.reload();
                            }
                            else if (msg.valid == '0'){
                                toastr["error"](msg.message, "Error");
                            }
                        })
                        .fail(function () {
                            Metronic.unblockUI();
                            toastr["error"]("Something has wrong, please try again later.", "Error");
                        });

                }
            });
        } else if(action == '<?php echo STATUS_CANCEL;?>' || action == '<?php echo STATUS_CLOSED;?>'){
            var act = '';
            if(action == '<?php echo STATUS_CANCEL;?>'){
                act = 'Cancel';
            } else if(action == '<?php echo STATUS_CLOSED;?>'){
                act = 'Complete';
            }
            bootbox.prompt({
                title: "Please enter Reason for " + act + " " + code + " :",
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
                            url: "<?php echo base_url('inventory/stock_request/ajax_action_request');?>",
                            dataType: "json",
                            data: {request_id: id, action: action, reason:result, is_redirect: true }
                        })
                            .done(function( msg ) {
                                Metronic.unblockUI();

                                if (msg.valid == '1') {
                                    location.reload();
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