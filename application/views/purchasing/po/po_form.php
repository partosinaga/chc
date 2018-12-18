<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();

if ($po_id > 0) {
    if ($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE) {
        if (check_session_action(get_menu_id(), STATUS_EDIT)) {
            $btn_action .= $btn_save;
        } else {
            $isedit = false;
        }

        if (check_session_action(get_menu_id(), STATUS_APPROVE)) {
            $btn_action .= btn_action($po_id, $row->po_code, STATUS_APPROVE);
        }
        if (check_session_action(get_menu_id(), STATUS_CANCEL)) {
            $btn_action .= btn_action($po_id, $row->po_code, STATUS_CANCEL);
        }
        if (check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(base_url('purchasing/po/pdf_po/' . $po_id . '.tpd'));
        }
    } else if ($row->status == STATUS_APPROVE) {
        $isedit = false;

        if (check_session_action(get_menu_id(), STATUS_DISAPPROVE)) {
            $btn_action .= btn_action($po_id, $row->po_code, STATUS_DISAPPROVE);
        }
        if (check_session_action(get_menu_id(), STATUS_CLOSED)) {
            $btn_action .= btn_action($po_id, $row->po_code, STATUS_CLOSED, 'Complete');
        }
        if (check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(base_url('purchasing/po/pdf_po/' . $po_id . '.tpd'));
        }
    } else if ($row->status == STATUS_CLOSED) {
        if (check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(base_url('purchasing/po/pdf_po/' . $po_id . '.tpd'));
        }
    } else {
        $isedit = false;
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
                            <i class="fa fa-user"></i><?php echo ($po_id > 0 ? 'View' : 'New');?> PO
                            <?php
                            if ($po_id > 0) {
                                echo '&nbsp;&nbsp;&nbsp; ' . get_status_name($row->status);
                            }
                            ?>
                        </div>
                        <div class="actions">
                            <?php echo btn_back(base_url('purchasing/po/po_manage/0.tpd'));?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form action="#" method="post" id="form-entry" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" id="po_id" name="po_id" value="<?php echo $po_id;?>" />
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
                                            <label class="control-label col-md-2">PO No</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="po_code" value="<?php echo ($po_id > 0 ? $row->po_code : '');?>" readonly />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Supplier</label>
                                            <div class="col-md-8">
                                                <select name="supplier_id" class="form-control form-filter select2me ">
                                                    <option value="" >None</option>
                                                    <?php
                                                    if(count($supplier_list) > 0){
                                                        foreach($supplier_list as $supplier){
                                                            echo '<option value="' . $supplier['supplier_id'] . '" ' . ($po_id > 0 ? ($row->supplier_id == $supplier['supplier_id'] ? 'selected="selected"' : '') : '') . '>' . $supplier['supplier_name'] . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">PR Code</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="pr_id" id="pr_id" value="<?php echo ($po_id > 0 ? $row->pr_id : '');?>" />
                                                    <input class="form-control" id="pr_code" type="text" value="<?php echo ($po_id > 0 ? $row->pr_code : ''); ?>" readonly />
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_pr" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remark</label>
                                            <div class="col-md-10">
                                                <textarea class="form-control" rows="2" name="remarks" style="resize: vertical;"><?php echo ($po_id > 0 ? $row->remarks : '');?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">PO Date</label>
                                            <div class="col-md-6" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="po_date" value="<?php echo ($po_id > 0 ? ymd_to_dmy($row->po_date) : date('d-m-Y'));?>" readonly />
                                                    <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i class="fa fa-calendar" ></i></button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">PO Delivery Date</label>
                                            <div class="col-md-6" >
                                                <div class="input-group date date-picker-nolimit" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="po_delivery_date" value="<?php echo ($po_id > 0 ? ymd_to_dmy($row->po_delivery_date) : date('d-m-Y'));?>" readonly>
                                                    <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i class="fa fa-calendar" ></i></button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Term of payment</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="term_payment" value="<?php echo ($po_id > 0 ? $row->term_payment : '');?>"  />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Currency / Rate</label>
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <select name="currencytype_id" class="form-control form-filter select2me" style="margin-right:10px;">
                                                            <?php
                                                            if(count($currency_list) > 0){
                                                                foreach($currency_list as $currency){
                                                                    echo '<option value="' . $currency['currencytype_id'] . '" ' . ($po_id > 0 ? ($row->currencytype_id == $currency['currencytype_id'] ? 'selected="selected"' : '') : '') . '>' . $currency['currencytype_code'] . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control mask_currency" name="curr_rate" value="<?php echo ($po_id > 0 ? $row->curr_rate : '1');?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:10px;">
                                    <div class="col-md-12">
                                        <a class="btn btn-sm green-haze yellow-stripe" id="btn_add_detail" style="margin-bottom: 10px;">
                                            <i class="fa fa-plus"></i>
                                            <span> &nbsp;&nbsp;Add Detail </span>
                                        </a>
                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-striped table-hover table-bordered table-po-detail" id="datatable_detail">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="120px"> Item Code</th>
                                                    <th class="text-center"> Item Description </th>
                                                    <th class="text-center" width="100px"> Acc No. </th>
                                                    <th class="text-center" width="80px" class="text-center"> UOM </th>
                                                    <th class="text-center" width="70px" class="text-center"> Qty </th>
                                                    <th class="text-center" width="120px" class="text-center"> Price </th>
                                                    <th class="text-center" width="90px" class="text-center"> Discount </th>
                                                    <th class="text-center" width="100px" class="text-center"> Tax </th>
                                                    <th class="text-center" width="140px" class="text-center"> Amount</th>
                                                    <th class="text-center" width="20px" class="text-center">&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $total = 0;
                                                if($po_id > 0){
                                                    $i = 0;
                                                    foreach($qry_det->result() as $row_det){
                                                        $detail = '';

                                                        $detail .= '<tr>
                                                                        <input data-index="' . $i . '" type="hidden" name="po_detail_id[' . $i . ']" value="' . $row_det->po_detail_id . '" />
                                                                        <input data-index="' . $i . '" class="input_pr_detail_id" type="hidden" name="pr_item_id[' . $i . ']" value="' . $row_det->pr_item_id . '" />
                                                                        <input data-index="' . $i . '" type="hidden" class="class_status" name="status[' . $i . ']" value="1" />
                                                                        <input data-index="' . $i . '" type="hidden" name="item_type[' . $i .']" value="' . $row_det->item_type . '" />
                                                                        <td>
                                                                            <div class="input-group">
                                                                                <input data-index="' . $i . '" type="hidden" name="item_id[' . $i . ']" value="' . $row_det->item_id . '" />
                                                                                <input data-index="' . $i . '" type="text" name="item_code[' . $i . ']" class="form-control input-sm" value="' . $row_det->item_code . '" readonly />
                                                                                <span class="input-group-btn">
                                                                                    <button class="btn btn-sm green-haze load_item" style="padding-top:5px;margin-right:0px;" type="button" data-index="' . $i . '"><i class="fa fa-arrow-up fa-fw"></i></button>
                                                                                </span>
                                                                            </div>
                                                                        </td>
                                                                        <td style="padding-top:10px;" class="item_desc_' . $i . '">' . ($row_det->item_code == Purchasing::DIRECT_PURCHASE ? $row_det->item_desc : $row_det->ms_item_desc) . '</td>
                                                                        <td>
                                                                            <div class="input-group">
                                                                                <input data-index="' . $i . '" type="hidden" name="account_coa_id[' . $i .']" value="' . $row_det->account_coa_id . '" />
                                                                                <input data-index="' . $i . '" type="text" name="account_coa_code[' . $i .']" class="form-control input-sm" value="' . $row_det->account_coa_code . '" readonly />
                                                                                <span class="input-group-btn">
                                                                                    <button data-index="' . $i . '" class="btn btn-sm green-haze load_coa" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>
                                                                                </span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group">
                                                                                <input data-index="' . $i . '" type="hidden" name="uom_id[' . $i .']" value="' . $row_det->uom_id . '" />
                                                                                <input data-index="' . $i . '" type="hidden" name="uom_factor[' . $i .']" value="' . $row_det->uom_factor . '" />
                                                                                <input data-index="' . $i . '" type="text" name="uom_code[' . $i .']" class="form-control  input-sm" value="' . $row_det->uom_code . '[' . $row_det->uom_factor . ']" readonly />
                                                                                <span class="input-group-btn">
                                                                                    <button data-index="' . $i . '" class="btn btn-sm green-haze load_uom" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>
                                                                                </span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" data-index="' . $i . '" name="item_qty[' . $i . ']" data-max="' . ($row_det->qty_remain + $row_det->item_qty) . '" value="' . $row_det->item_qty . '" class="form-control text-right input-sm mask_number num_cal num_qty" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" data-index="' . $i . '" name="item_price[' . $i . ']" value="' . $row_det->item_price . '" class="form-control text-right input-sm mask_currency num_cal" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" data-index="' . $i . '" name="item_disc[' . $i . ']" value="' . $row_det->item_disc . '" class="form-control text-right input-sm mask_currency num_cal" />
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group">
                                                                                <input data-index="' . $i . '" type="hidden" name="tax_id[' . $i . ']" value="' . $row_det->tax_id . '" />
                                                                                <input data-index="' . $i . '" type="hidden" name="tax_amount_wht[' . $i . ']" value="' . $row_det->tax_amount_wht . '" />
                                                                                <input data-index="' . $i . '" type="text" name="tax_amount_vat[' . $i . ']" class="form-control input-sm mask_currency text-right" value="' . $row_det->tax_amount_vat . '" />
                                                                                <span class="input-group-btn">
                                                                                    <button data-index="' . $i . '" class="btn btn-sm green-haze load_tax" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>
                                                                                </span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" data-index="' . $i . '" name="amount[' . $i . ']" value="' . $row_det->item_tot_amount . '" class="form-control text-right input-sm mask_currency num_amount" readonly >
                                                                        </td>
                                                                        <td class="text-center text-middle">
                                                                            <a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' . $i . '" style="margin-right:0px;margin-top:4px;"><i class="fa fa-times"></i></a>
                                                                        </td>
                                                                    </tr>';

                                                        $total += $row_det->item_tot_amount;

                                                        echo $detail;
                                                        $i++;
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="8" class="text-right" style="vertical-align: middle;">Total</th>
                                                    <th><input type="text" class="form-control input-sm num_total text-right mask_currency" value="<?php echo $total;?>" readonly /></th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if($po_id > 0){
                                        $log = '';
                                        $modified = '';
                                        $qry_log = $this->mdl_general->get('app_log', array('feature_id' => Feature::FEATURE_PO, 'reff_id' => $po_id), array(), 'log_id asc');
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
                                            $modified .= "<div class='col-md-4'><h6>Last Modified by " . get_user_fullname( $row->user_modified ) . " (" . date_format(new DateTime($row->date_modified), 'd/m/Y H:i:s') . ")</h6></div>" ;
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

<div id="ajax-modal" data-width="1024" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>
<div id="ajax-modal-small" data-width="480" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
$(document).ready(function() {
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

    var isedit = <?php echo ($isedit == true ? 0 : 1); ?>;

    if (isedit > 0) {
        $('#form-body').block({
            message: null,
            overlayCSS: {backgroundColor: '#EDF5EB', opacity: 0, cursor: 'default'}
        });
    }

    <?php
        if($po_id > 0){
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
            echo picker_input_date(true, true, ymd_to_dmy($row->po_date));
        } else {
            echo picker_input_date();
        }
    ?>
    Metronic.initDatePicker('date-picker-nolimit');

    var digit = 2;
    function init_digit() {
        var curr = $("select[name='currencytype_id'] option:selected").text();

        if (curr == 'IDR') {
            digit = 0;
        } else {
            digit = 2;
        }
    }
    init_digit();

    var $modal = $('#ajax-modal');
    var $modal_small = $('#ajax-modal-small');

    function init_page(){
        autosize($('textarea'));

        $(".mask_number").inputmask("decimal", {
            radixPoint: ".",
            groupSeparator: ",",
            digits: 2,
            autoGroup: true,
            autoUnmask: true
        });

        $(".mask_currency").inputmask("decimal", {
            radixPoint: ".",
            groupSeparator: ",",
            digits: digit,
            autoGroup: true,
            autoUnmask: true
        });

        $('.tooltips').tooltip();

        $('select.select2me').select2();
    }

    init_page();

    $("select[name='currencytype_id']").on('change', function() {
        init_digit();

        init_page();

        calculate_row(-1);
    });

    //PR
    var grid_pr = new Datatable();
    var datatablePR = function () {
        grid_pr.init({
            src: $("#datatable_pr"),
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
                    {"bSortable": false, "sClass": "text-center"},
                    {"sClass": "text-center"},
                    {"sClass": "text-center"},
                    {"bSortable": false, "sClass": "text-center"},
                    {"bSortable": false},
                    {"bSortable": false, "sClass": "text-center"}
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('purchasing/po/ajax_modal_pr_list');?>/" + $('#pr_id').val() // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_pr_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }

    $('#btn_lookup_pr').live('click', function (e) {
        e.preventDefault();

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';

        $('body').modalmanager('loading');

        setTimeout(function () {
            $modal.load('<?php echo base_url('purchasing/po/ajax_modal_pr');?>', '', function () {

                $modal.modal();
                datatablePR();

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
    });

    $('.btn-select-pr').live('click', function (e) {
        e.preventDefault();

        var pr_id = $(this).attr('data-id');
        var pr_code = $(this).attr('data-code');
        var dept_id = $(this).attr('data-dept');

        var old_pr_id = $('#pr_id').val();
        var next = true;
        if (parseInt(old_pr_id) > 0) {
            bootbox.confirm("Old PR will be deleted, continue?", function (result) {
                if (result == true) {
                    $('#pr_id').val(pr_id);
                    $('#pr_code').val(pr_code);
                    $('#department_id').val(dept_id);

                    set_flag_delete(0);
                }
                else {
                    next = false;
                }
            });
        }
        else {
            $('#pr_id').val(pr_id);
            $('#pr_code').val(pr_code);
            $('#department_id').val(dept_id);

            set_flag_delete(0);
        }

        $('#ajax-modal').modal('hide');
    });

    var grid_pr_detail = new Datatable();
    var datatablePRDetail = function () {

        var pr_detail_id_exist = '-';
        var n = 0;
        $('#datatable_detail tbody tr').each(function () {
            if ($(this).hasClass('hide') == false) {
                if(pr_detail_id_exist == '-'){
                    pr_detail_id_exist = '';
                }
                if(n > 0) {
                    pr_detail_id_exist += '_';
                }
                pr_detail_id_exist += $(this).find(".input_pr_detail_id").val();

                n++;
            }
        });

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
                    { "bSortable": false },
                    { "bSortable": false },
                    { "bSortable": false },
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
                    "url": "<?php echo base_url('purchasing/po/ajax_modal_pr_detail_list');?>/" + $('#pr_id').val() + "/" + pr_detail_id_exist
                },
                "fnDrawCallback": function( oSettings ) {
                    init_page();
                    Metronic.initUniform();
                }
            }
        });

        var tableWrapper = $("#datatable_pr_detail_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });

        grid_pr_detail.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();

            var i = $('#datatable_detail tbody tr').length;

            if (grid_pr_detail.getSelectedRowsCount() > 0) {
                var result = grid_pr_detail.getSelectedRowsCheckbox();

                $.each( result, function( index, value ){
                    var append_html = '';

                    append_html = '<tr>' +
                    '<input data-index="' + i + '" type="hidden" name="po_detail_id[' + i + ']" value="" />' +
                    '<input data-index="' + i + '" class="input_pr_detail_id" type="hidden" name="pr_item_id[' + i + ']" value="' + value[0] + '" />' +
                    '<input data-index="' + i + '" type="hidden" class="class_status" name="status[' + i + ']" value="1" />' +
                    '<input data-index="' + i + '" type="hidden" name="item_type[' + i +']" value="' + value[7] + '" />' +
                    '<td>' +
                    '<div class="input-group">' +
                    '<input data-index="' + i + '" type="hidden" name="item_id[' + i + ']" value="' + value[1] + '" />' +
                    '<input data-index="' + i + '" type="text" name="item_code[' + i + ']" class="form-control form-filter input-sm" value="' + value[2] + '" readonly />' +
                    '<span class="input-group-btn">' +
                    '<button class="btn btn-sm green-haze load_item" data-index="' + i + '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                    '</span>' +
                    '</div>' +
                    '</td>' +
                    '<td style="padding-top:10px;" class="item_desc_' + i + '">' + value[3] + '</td>' +
                    '<td>' +
                    '<div class="input-group">' +
                    '<input data-index="' + i + '" type="hidden" name="account_coa_id[' + i + ']" value="' + value[8] + '" />' +
                    '<input data-index="' + i + '" type="text" name="account_coa_code[' + i + ']" class="form-control form-filter input-sm" value="' + value[9] + '" readonly />' +
                    '<span class="input-group-btn">' +
                    '<button class="btn btn-sm green-haze load_coa" data-index="' + i + '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                    '</span>' +
                    '</div>' +
                    '</td>' +
                    '<td>' +
                    '<div class="input-group">' +
                    '<input data-index="' + i + '" type="hidden" name="uom_id[' + i + ']" value="' + value[5] + '" />' +
                    '<input data-index="' + i + '" type="hidden" name="uom_factor[' + i + ']" value="' + value[10] + '" />' +
                    '<input data-index="' + i + '" type="text" name="uom_code[' + i + ']" class="form-control form-filter input-sm" value="' + value[6] + ' [' + value[10] + ']" readonly />' +
                    '<span class="input-group-btn">' +
                    '<button class="btn btn-sm green-haze load_uom" data-index="' + i + '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                    '</span>' +
                    '</div>' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" data-index="' + i + '" name="item_qty[' + i + ']" data-max="' + value[4] + '" value="' + value[4] + '" class="form-control text-right input-sm mask_number num_cal num_qty" />' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" data-index="' + i + '" name="item_price[' + i + ']" value="0" class="form-control text-right input-sm mask_currency num_cal" />' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" data-index="' + i + '" name="item_disc[' + i + ']" value="0" class="form-control text-right input-sm mask_currency num_cal" />' +
                    '</td>' +
                    '<td>' +
                    '<div class="input-group">' +
                    '<input data-index="' + i + '" type="hidden" name="tax_id[' + i + ']" value="0" />' +
                    '<input data-index="' + i + '" type="hidden" name="tax_amount_wht[' + i + ']" value="0" />' +
                    '<input data-index="' + i + '" type="text" name="tax_amount_vat[' + i + ']" value="0" class="form-control text-right input-sm mask_currency num_cal" />' +
                    '<span class="input-group-btn">' +
                    '<button class="btn btn-sm green-haze load_tax" data-index="' + i + '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                    '</span>' +
                    '</div>' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" data-index="' + i + '" name="amount[' + i + ']" value="0" class="form-control text-right input-sm mask_currency num_amount" readonly />' +
                    '</td>' +
                    '<td class="text-center text-middle">' +
                    '<a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' + i + '" style="margin-right:0px;margin-top:4px;"><i class="fa fa-times"></i></a>' +
                    '</td>' +
                    '</tr>';

                    $('#datatable_detail tbody').append(append_html);

                    init_page();

                    calculate_tot_amount();

                    i++;
                });

                $('#ajax-modal').modal('hide');
            } else if (grid_pr_detail.getSelectedRowsCount() === 0) {
                toastr["warning"]("Please Select Detail.", "Warning");
            }
        });
    }

    $('#btn_add_detail').live('click', function (e) {
        e.preventDefault();

        var pr_id = $('#pr_id').val();
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
                $modal.load('<?php echo base_url('purchasing/po/ajax_modal_pr_detail');?>', '', function () {

                    $modal.modal();
                    datatablePRDetail();

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

    var grid_item = new Datatable();
    var datatableItem = function (item_id, num_index, pr_item_id, item_type) {
        grid_item.init({
            src: $("#datatable_item"),
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
                    { "bSortable": true },
                    null,
                    null,
                    { "bSortable": false, "sClass": "text-center" },
                    { "bSortable": false, "sClass": "text-right" },
                    { "bSortable": false, "sClass": "text-center" }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('purchasing/po/ajax_modal_item_list');?>/" + item_id + '/' + num_index + '/' + pr_item_id + '/' + item_type // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_item_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });

        init_page();
    }

    $('.load_item').live('click', function (e) {
        e.preventDefault();

        var num_index = parseInt($(this).attr('data-index'));
        var item_id = parseInt($(this).closest('.input-group').find('input[name="item_id[' + num_index + ']"]').val()) || 0;
        var pr_item_id = parseInt($(this).closest('tr').find('input[name="pr_item_id[' + num_index + ']"]').val()) || 0;
        var item_type = parseInt($(this).closest('tr').find('input[name="item_type[' + num_index + ']"]').val()) || 0;

        if (num_index >= 0 && item_id > 0 && pr_item_id > 0 && item_type > 0) {
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');


            setTimeout(function () {
                $modal.load('<?php echo base_url('purchasing/po/ajax_modal_item');?>', '', function () {

                    $modal.modal();
                    datatableItem(item_id, num_index, pr_item_id, item_type);

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
        } else {
            toastr["error"]("Error datagrid!!!", "Error");
            console.log('num_index : ' + num_index);
            console.log('item_id : ' + item_id);
            console.log('pr_item_id : ' + pr_item_id);
            console.log('item_type : ' + item_type);
        }
    });

    $('.btn-select-item').live('click', function (e) {
        e.preventDefault();

        var item_id = parseInt($(this).attr('data-id')) || 0;
        var item_code = $(this).attr('data-code');
        var num_index = parseInt($(this).attr('data-index')) || 0;
        var item_desc = $(this).attr('data-desc');
        var account_coa_id = parseInt($(this).attr('data-supplies-id')) || 0;
        var account_coa_code = $(this).attr('data-supplies-code');
        var uom_id = parseInt($(this).attr('data-uom-id')) || 0;
        var uom_factor= parseInt($(this).attr('data-uom-factor')) || 0;
        var uom_code = $(this).attr('data-uom-code');

        var i = 0;
        var is_exist = false;
        $('#datatable_detail tbody tr').each(function () {
            if (!$(this).hasClass('hide')) {
                var item_exist = parseInt($('input[name="item_id[' + i + ']"]').val()) || 0;

                if(item_id == item_exist){
                    is_exist = true;
                }
            }
            i++;
        });

        if (is_exist) {
            toastr["warning"]("Item already exist.", "Warning");
        } else {
            $('input[name="item_id[' + num_index + ']"]').val(item_id);
            $('input[name="item_code[' + num_index + ']"]').val(item_code);
            $('.item_desc_' + num_index + '').html(item_desc);
            $('input[name="account_coa_id[' + num_index + ']"]').val(account_coa_id);
            $('input[name="account_coa_code[' + num_index + ']"]').val(account_coa_code);
            $('input[name="uom_id[' + num_index + ']"]').val(uom_id);
            $('input[name="uom_factor[' + num_index + ']"]').val(uom_factor);
            $('input[name="uom_code[' + num_index + ']"]').val(uom_code);

            $('#ajax-modal').modal('hide');
        }


    });

    var grid_coa = new Datatable();
    //COA
    var handleTableCOA = function (coa_id, num_index) {
        // Start Datatable Item
        grid_coa.init({
            src: $("#datatable_coa"),
            onSuccess: function (grid_coa) {
                // execute some code after table records loaded
            },
            onError: function (grid_coa) {
                // execute some code on network or other general error
            },
            onDataLoad: function(grid_coa) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Populating...',
            dataTable: {
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    { "sClass": "text-center", "bSortable": true, "sWidth" : '15%' },
                    null,
                    { "sClass": "text-center","sWidth" : '10%' },
                    { "sClass": "text-center","sWidth" : '10%' },
                    { "sClass": "text-center", "sWidth" : '6%' },
                    { "bSortable": false, "sClass": "text-center" }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('purchasing/po/ajax_modal_coa_list');?>/" + coa_id + '/' + num_index // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_coa_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        }); // initialize select2 dropdown

        $('select.select2me').select2();

        // End Datatable Item
    }

    $('.load_coa').live('click', function (e) {
        var num_index = parseInt($(this).attr('data-index'));
        var item_code = $(this).closest('tr').find('input[name="item_code[' + num_index + ']"]').val();
        var coa_id = parseInt($(this).closest('tr').find('input[name="account_coa_id[' + num_index + ']"]').val()) || 0;

        e.preventDefault();

        if (item_code == '<?php echo Purchasing::DIRECT_PURCHASE;?>') {
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('purchasing/po/ajax_modal_coa');?>.tpd', '', function () {
                    $modal.modal();
                    handleTableCOA(coa_id, num_index);

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if ($modal.hasClass('modal-overflow') === false) {
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});
                });
            }, 150);
        } else {
            toastr["warning"]("Cannot change COA.", "Warning");
        }
    });

    $('.btn-select-coa').live('click', function (e) {
        e.preventDefault();

        var coa_id = parseInt($(this).attr('data-id')) || 0;
        var coa_code = $(this).attr('data-code');
        var num_index = parseInt($(this).attr('data-index')) || 0;

        $('input[name="account_coa_id[' + num_index + ']"]').val(coa_id);
        $('input[name="account_coa_code[' + num_index + ']"]').val(coa_code);

        $('#ajax-modal').modal('hide');
    });

    var grid_uom = new Datatable();
    //UOM
    var handleTableUOM = function (num_index, item_id, uom_id, uom_factor, pr_item_id) {
        // Start Datatable Item
        grid_uom.init({
            src: $("#datatable_uom"),
            onSuccess: function (grid_uom) {
                // execute some code after table records loaded
            },
            onError: function (grid_uom) {
                // execute some code on network or other general error
            },
            onDataLoad: function(grid_uom) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Populating...',
            dataTable: {
                "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                "aoColumns": [
                    { "bSortable": false, "sClass": "text-center"},
                    { "bSortable": false, "sClass": "text-right"},
                    { "bSortable": false, "sClass": "text-center" }
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [-1],
                    ["All"] // change per page values here
                ],
                "pageLength": -1, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('purchasing/po/ajax_modal_uom_list');?>/" + num_index + "/" + item_id + "/" + uom_id + "/" + uom_factor + "/" + pr_item_id // ajax source
                }
            }
        });

        var tableWrapper = $("#datatable_uom_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        }); // initialize select2 dropdown

        $('select.select2me').select2();

        // End Datatable Item
    }

    $('.load_uom').live('click', function (e) {
        var num_index = parseInt($(this).attr('data-index'));
        var item_id = parseInt($(this).closest('tr').find('input[name="item_id[' + num_index + ']"]').val()) || 0;
        var uom_id = parseInt($(this).closest('tr').find('input[name="uom_id[' + num_index + ']"]').val()) || 0;
        var uom_factor = parseInt($(this).closest('tr').find('input[name="uom_factor[' + num_index + ']"]').val()) || 0;
        var pr_item_id = parseInt($(this).closest('tr').find('input[name="pr_item_id[' + num_index + ']"]').val()) || 0;

        e.preventDefault();

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
            '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
            '</div>' +
            '</div>';

        $('body').modalmanager('loading');

        setTimeout(function () {
            $modal_small.load('<?php echo base_url('purchasing/po/ajax_modal_uom');?>.tpd', '', function () {
                $modal_small.modal();
                handleTableUOM(num_index, item_id, uom_id, uom_factor, pr_item_id);

                $.fn.modalmanager.defaults.resize = true;

                if ($modal_small.hasClass('bootbox') == false) {
                    $modal_small.addClass('modal-fix');
                }

                if ($modal_small.hasClass('modal-overflow') === false) {
                    $modal_small.addClass('modal-overflow');
                }

                $modal_small.css({'margin-top': '0px'});
            });
        }, 150);
    });

    $('.btn-select-uom').live('click', function (e) {
        e.preventDefault();

        var uom_id = parseInt($(this).attr('data-id')) || 0;
        var uom_factor = parseInt($(this).attr('data-factor')) || 0;
        var uom_code = $(this).attr('data-code');
        var num_index = parseInt($(this).attr('data-index')) || 0;

        $('input[name="uom_id[' + num_index + ']"]').val(uom_id);
        $('input[name="uom_factor[' + num_index + ']"]').val(uom_factor);
        $('input[name="uom_code[' + num_index + ']"]').val(uom_code);

        $modal_small.modal('hide');
    });

    var grid_tax = new Datatable();
    var datatableTax = function (num_index) {
        grid_tax.init({
            src: $("#datatable_tax"),
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
                    {"bSortable": false, "sClass": "text-center"},
                    {"bSortable": false },
                    {"bSortable": false },
                    {"bSortable": false, "sClass": "text-center"}
                ],
                "aaSorting": [],
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "<?php echo base_url('purchasing/po/ajax_modal_tax_list');?>/" + num_index
                }
            }
        });

        var tableWrapper = $("#datatable_tax_wrapper");

        tableWrapper.find(".dataTables_length select").select2({
            showSearchInput: false //hide search box with special css class
        });
    }

    $('.load_tax').live('click', function (e) {
        e.preventDefault();

        var num_index = parseInt($(this).attr('data-index'));

        $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
            '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

        $('body').modalmanager('loading');

        setTimeout(function () {
            $modal.load('<?php echo base_url('purchasing/po/ajax_modal_tax');?>', '', function () {

                $modal.modal();
                datatableTax(num_index);

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
    });

    $('.btn-select-tax').live('click', function (e) {
        e.preventDefault();

        var tax_id = $(this).attr('data-id');
        var num_index = $(this).attr('data-index');
        var tax_vat = parseFloat($(this).attr('data-percent')) || 0;
        var tax_wht = parseFloat($(this).attr('data-wht')) || 0;

        var item_qty = parseFloat($('input[name="item_qty[' + num_index + ']"]').val()) || 0;
        var item_price = parseFloat($('input[name="item_price[' + num_index + ']"]').val()) || 0;
        var item_disc = parseFloat($('input[name="item_disc[' + num_index + ']"]').val()) || 0;

        var amount = ((item_qty * item_price) - item_disc).toFixed(digit);

        var vat = parseFloat((amount * tax_vat / 100).toFixed(digit)) || 0;
        var wht = parseFloat((amount * tax_wht / 100).toFixed(digit)) || 0;

        $('input[name="tax_id[' + num_index + ']"]').val(tax_id);
        $('input[name="tax_amount_vat[' + num_index + ']"]').val(vat);
        $('input[name="tax_amount_wht[' + num_index + ']"]').val(wht);

        $('#ajax-modal').modal('hide');

        calculate_row(num_index);
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
            if ($('#datatable_detail tbody tr:nth-child(' + num_index + ')').hasClass('class_status')) {
                $('#datatable_detail tbody tr:nth-child(' + num_index + ')').val('9');
            }
        }

        calculate_tot_amount();
    }

    $('.num_cal').live('keyup', function(){
        var i = $(this).attr('data-index');

        var val = $(this).val().trim();
        if(val == ''){
            $(this).val('0');
        }

        if($(this).hasClass('num_qty')){
            var qty = parseFloat($(this).val()) || 0;
            var max = parseFloat($(this).attr('data-max')) || 0;

            if(qty > max){
                $(this).val(max);

                toastr["warning"]("PO Item qty cannot bigger than PR qty.", "Warning");
            }
        }

        calculate_row(i);
    });

    function calculate_row(i){
        if (i >= 0) {
            var item_qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;
            var item_price = parseFloat($('input[name="item_price[' + i + ']"]').val()) || 0;
            var item_disc = parseFloat($('input[name="item_disc[' + i + ']"]').val()) || 0;
            var tax_amount_vat = parseFloat($('input[name="tax_amount_vat[' + i + ']"]').val()) || 0;

            var amount = parseFloat(((item_price * item_qty) - item_disc + tax_amount_vat).toFixed(digit)) || 0;

            $('input[name="amount[' + i + ']"]').val(amount);
        } else {
            var i = 0;
            $('#datatable_detail > tbody > tr ').each(function() {
                if(!$(this).hasClass('hide')) {
                    var item_qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;
                    var item_price = parseFloat($('input[name="item_price[' + i + ']"]').val()) || 0;
                    var item_disc = parseFloat($('input[name="item_disc[' + i + ']"]').val()) || 0;
                    var tax_amount_vat = parseFloat($('input[name="tax_amount_vat[' + i + ']"]').val()) || 0;

                    var amount = parseFloat(((item_price * item_qty) - item_disc + tax_amount_vat).toFixed(digit)) || 0;

                    $('input[name="amount[' + i + ']"]').val(amount);
                }
                i++;
            });
        }

        calculate_tot_amount();
    }

    function calculate_tot_amount(){
        var len = $('#datatable_detail tbody tr').length;
        if(len > 0){
            var tot = 0;
            for(var i=0; i<len; i++ ){
                var stat = $('input[name="status[' + i + ']"]').val();
                if(stat != '<?php echo STATUS_DELETE;?>') {
                    var val = $('input[name="amount[' + i + ']"]').val();
                    val = parseFloat(val) || 0;
                    tot += val;
                }
            }
            var total = parseFloat(tot.toFixed(digit)) || 0;
            $('.num_total').val(total);
        }
    }

    $('.btn-remove').live('click', function(){
        var this_btn = $(this);
        bootbox.confirm("Are you sure want to delete?", function (result) {
            if (result == true) {
                this_btn.closest('tr').addClass('hide');
                this_btn.closest('tr').find('.class_status').val('9');

                calculate_tot_amount();
            }
        });
    });

    function validate_submit(){
        var result = true;

        if($('.form-group').hasClass('has-error')){
            $('.form-group').removeClass('has-error');
        }

        var pr_id = parseInt($('input[name="pr_id"]').val()) || 0;
        var po_date = $('input[name="po_date"]').val().trim();
        var supplier_id = parseInt($('select[name="supplier_id"]').val()) || 0;
        var remarks = $('textarea[name="remarks"]').val().trim();

        if(pr_id <= 0){
            toastr["warning"]("Please select PR.", "Warning!");
            $('input[name="pr_code"]').closest('.form-group').addClass('has-error');
            result = false;
        }
        if(po_date == ''){
            toastr["warning"]("Please select Issue Date.", "Warning!");
            $('input[name="po_date"]').closest('.form-group').addClass('has-error');
            result = false;
        }
        if(supplier_id <= 0){
            toastr["warning"]("Please select Supplier.", "Warning!");
            $('select[name="supplier_id"]').closest('.form-group').addClass('has-error');
            result = false;
        }
        if(remarks == ''){
            toastr["warning"]("Please input remarks.", "Warning!");
            $('textarea[name="remarks"]').closest('.form-group').addClass('has-error');
            result = false;
        }

        var i = 0;
        var i_act = 0;
        $('#datatable_detail > tbody > tr ').each(function() {
            if(!$(this).hasClass('hide')) {
                var item_qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;
                var qty_max = parseFloat($('input[name="item_qty[' + i + ']"]').attr('data-max')) || 0;
                var amount = parseFloat($('input[name="amount[' + i + ']"]').val()) || 0;
                var item_desc = $('.item_desc_' + i).html();

                $('input[name="item_qty[' + i + ']"]').removeClass('has-error');
                $('input[name="amount[' + i + ']"]').removeClass('has-error');

                if(item_qty > qty_max){
                    toastr["warning"]("Item Qty cannot bigger than PR Qty Remain (" + item_desc + ").", "Warning");
                    $('input[name="item_qty[' + i + ']"]').addClass('has-error');
                    result = false;
                }
                if(item_qty <= 0){
                    toastr["warning"]("Please select Item Qty (" + item_desc + ").", "Warning");
                    $('input[name="item_qty[' + i + ']"]').addClass('has-error');
                    result = false;
                }
                if(amount <= 0){
                    toastr["warning"]("Please input valid price (" + item_desc + ").", "Warning");
                    $('input[name="amount[' + i + ']"]').addClass('has-error');
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

    $('#form-entry').on('submit', function(){
        Metronic.blockUI({
            target: '#form-entry',
            boxed: true,
            message: 'Processing...'
        });

        var btn = $(this).find("button[type=submit]:focus" );

        toastr.clear();

        if (validate_submit()) {
            var form_data = $('#form-entry').serializeArray();
            if (btn[0] == null){ }
            else {
                if(btn[0].name === 'save_close'){
                    form_data.push({name: "save_close", value: 'save_close'});
                }
            }

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('purchasing/po/ajax_po_submit');?>",
                dataType: "json",
                data: form_data
            })
                .done(function( msg ) {
                    if(msg.valid == '0' || msg.valid == '1'){
                        if(msg.valid == '1'){
                            window.location.assign(msg.link);
                            //console.log(msg);
                        }
                        else {
                            toastr["error"](msg.message, "Error");
                            $('#form-entry').unblock();
                        }
                    }
                    else {
                        toastr["error"]("Something has wrong, please try again later.", "Error");
                        $('#form-entry').unblock();
                    }
                })
                .fail(function () {
                    $('#form-entry').unblock();
                    toastr["error"]("Something has wrong, please try again later.", "Error");
                });
        }
        else {
            $('#form-entry').unblock();
        }
    });

    $('.btn_print').live('click', function(){
        if (check_print()) {
            window.open('<?php echo base_url('purchasing/po/pdf_po/' . $po_id . '.tpd');?>', '_blank');
        }
    });

    function check_print(){
        var result = true;

        var i = 0;
        $('#datatable_detail > tbody > tr ').each(function() {
            if(!$(this).hasClass('hide')) {
                var account_coa_id = parseFloat($('input[name="account_coa_id[' + i + ']"]').val()) || 0;
                var item_desc = $('.item_desc_' + i).html();

                if (account_coa_id <= 0) {
                    toastr["warning"]("Please select Acc No. For " + item_desc + ".", "Warning!");

                    result = false;
                }
            }
            i++;
        });

        return result;
    }

    function check_approve(action) {
        var result = true;

        if (action == '<?php echo STATUS_APPROVE;?>') {
            result = check_print();
        }

        return result;
    }

    $('.btn-action').live('click', function () {
        var po_id = $(this).attr('data-id');
        var po_code = $(this).attr('data-code');
        var action = $(this).attr('data-action');
        var action_code = $(this).attr('data-action-code');

        if (action == '<?php echo STATUS_CANCEL;?>') {
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
                        Metronic.blockUI({
                            target: '#form-entry',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('purchasing/po/ajax_po_action');?>",
                            dataType: "json",
                            data: {po_id: po_id, action: action, reason:result, is_redirect: true }
                        })
                            .done(function( msg ) {
                                $('#form-entry').unblock();

                                if (msg.valid == '0' || msg.valid == '1') {
                                    if (msg.valid == '1') {
                                        location.reload();
                                    } else {
                                        toastr["error"](msg.message, "Error");
                                    }
                                } else {
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                }
                            })
                            .fail(function () {
                                $('#form-entry').unblock();
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
                        Metronic.blockUI({
                            target: '#form-entry',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('purchasing/po/ajax_po_action');?>",
                            dataType: "json",
                            data: {po_id: po_id, action: action, reason:result, is_redirect: true }
                        })
                            .done(function( msg ) {
                                $('#form-entry').unblock();

                                if (msg.valid == '0' || msg.valid == '1') {
                                    if (msg.valid == '1') {
                                        location.reload();
                                    } else {
                                        toastr["error"](msg.message, "Error");
                                    }
                                } else {
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                }
                            })
                            .fail(function () {
                                $('#form-entry').unblock();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                }
            });
        } else {
            bootbox.confirm("Are you sure want to " + action_code + " " + po_code + " ?", function (result) {
                if (result == true) {

                    if (check_approve(action)) {
                        Metronic.blockUI({
                            target: '#form-entry',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('purchasing/po/ajax_po_action');?>",
                            dataType: "json",
                            data: {po_id: po_id, action: action, is_redirect: true}
                        })
                            .done(function (msg) {
                                $('#form-entry').unblock();

                                if (msg.valid == '0' || msg.valid == '1') {
                                    if (msg.valid == '1') {
                                        location.reload();
                                    } else {
                                        toastr["error"](msg.message, "Error");
                                    }
                                } else {
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                }
                            })
                            .fail(function () {
                                $('#form-entry').unblock();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                }
            });
        }

    });
});


</script>