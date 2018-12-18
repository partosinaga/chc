<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();

if ($so_id > 0) {

    if ($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE) {

        if($row->so_status != 0){ //if not yet process
            if (check_session_action(get_menu_id(), STATUS_PROCESS)) {
                $btn_action .= btn_action($so_id, $row->so_code, STATUS_PROCESS);
            }
            if (check_session_action(get_menu_id(), STATUS_CANCEL)) {
                $btn_action .= btn_action($so_id, $row->so_code, STATUS_CANCEL);
            }
        } else {
            if (check_session_action(get_menu_id(), STATUS_EDIT)) {
                $btn_action .= $btn_save;
            } else {
                $isedit = false;
            }
        }
    } else if( $row->status == STATUS_APPROVE ){
        if (check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(base_url('purchasing/po/pdf_po/' . $so_id . '.tpd'));
        }
        if (check_session_action(get_menu_id(), STATUS_CLOSED)) {
            $btn_action .= btn_action($so_id, $row->so_code, STATUS_CLOSED);
        }
    } else if( $row->status == STATUS_PROCESS ){
        if (check_session_action(get_menu_id(), STATUS_APPROVE)) {
            $btn_action .= btn_action($so_id, $row->so_code, STATUS_APPROVE);
        }
        if (check_session_action(get_menu_id(), STATUS_DISAPPROVE)) {
            $btn_action .= btn_action($so_id, $row->so_code, STATUS_DISAPPROVE);
        }
    } else if ($row->status == STATUS_CLOSED) {
        if (check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(base_url('purchasing/po/pdf_po/' . $so_id . '.tpd'));
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
                            <i class="fa fa-user"></i> New SO Item
                        </div>
                        <div class="actions">
                            <?php
                            $back_url = base_url('home/home/dashboard.tpd');
                            if ($so_id > 0) {
                                $back_url = base_url('sales/sales_order/so_list/0.tpd');
                            }
                            echo btn_back($back_url);
                            ?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="form-entry-so" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" class="form-control" name="so_id" value="<?php echo $so_id?>" readonly/>
                            <div class="form-actions top">
                                <div class="row">
                                    <div class="col-md-9">
                                        <?php echo $btn_action; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body" id="form-body">
                                <div class="row block-input" style="margin-bottom: 15px;">
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">SO No <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <input type="text" id="so-no" class="form-control" name="so_code" value="<?php echo $so_id > 0 ? $row->so_code : '' ?>" readonly/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">SO Date<span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" id="so-date" name="so_date" value="<?php echo $so_id > 0 ? ymd_to_dmy($row->so_date) : date('d-m-Y') ?>" readonly />
                                                      <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                                      </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-4">Customer<span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <input type="hidden" class="form-control customer" name="customer" id="customer_id" value="<?php echo $so_id > 0 ? $row->customer_id : '' ?>"/>
                                                    <input class="form-control" id="customer_name" type="text" readonly="" value="<?php echo $so_id > 0 ? $row->customer_name : '' ?>">
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_customer" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Currency/Rate<span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-2">
                                                <select name="currency" id="currency" class="form-control form-filter select2me">
                                                    <?php
                                                    $query = $this->db->query("SELECT * FROM currencytype");
                                                    foreach($query->result() as $rows ){
                                                        echo '<option value="' . $rows->currencytype_id . '" ' . ($so_id > 0 ? ($rows->currencytype_id == $row->currency ? 'selected="selected"' : '') : '') . ' >' .  $rows->currencytype_code  . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" id="rate" class="form-control mask_currency" name="rate" value="<?php echo $so_id > 0 ? $row->rate : '' ?>">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Request DO Date<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="request_do_date" id="req_do_date" value="<?php echo $so_id > 0 ? ymd_to_dmy($row->so_date) : date('d-m-Y') ?>" readonly/>
                                                      <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i
                                                                class="fa fa-calendar"></i></button>
                                                      </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Sales<span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <select name="sales" id="sales" class="form-control form-filter select2me">
                                                    <option value=""> -- Select --</option>
                                                    <?php
                                                    $query = $this->db->query("SELECT * FROM ms_sales WHERE status= '".STATUS_NEW."'  ");
                                                    foreach($query->result() as $rows_ ){
                                                        echo '<option value="' . $rows_->sales_id . '" ' . ($so_id > 0 ? ($rows_->sales_id == $row->sales_id ? 'selected="selected"' : '') : '') . ' >' .  $rows_->sales_name  . '</option>';

                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Terms Of Payment</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="term" id="term" value="<?php echo $so_id > 0 ? $row->term_of_payment : '' ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Delivery Address <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="delivery_address" id="delivery-address-id" value="<?php echo $so_id > 0 ? $row->delivery_address_id : '' ?>">
                                                <textarea class="form-control" rows="2" id="delivery-address" readonly><?php echo $so_id > 0 ? $row->delivery_address : '' ?></textarea>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn delivery-address"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Invoice Address <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="invoice_address" id="invoice-address-id" value="<?php echo $so_id > 0 ? $row->invoice_address_id : '' ?>">
                                                <textarea class="form-control" rows="2" id="invoice-address" readonly><?php echo $so_id > 0 ? $row->invoice_address : '' ?></textarea>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn invoice-address"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" rows="2" name="remarks"><?php echo $so_id > 0 ? $row->remarks : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-body ">
                                    <div class="portlet-title">
                                        <div class="actions" style="margin-bottom: 10px;">
                                            <a href="javascript:;" class="btn default green-seagreen btn input-sm yellow-stripe" id="btn_lookup_items">
                                                <i class="fa fa-plus"></i><span
                                                    class="hidden-480">&nbsp;&nbsp;Add Items </span>
                                            </a>
                                        </div>
                                    </div>
                                    <table class="table table-striped table-hover table-bordered" id="item_list_table">
                                        <thead>
                                        <tr>
                                            <th width="8%" class="text-center">Code</th>
                                            <th>Description</th>
                                            <th width="12%" class="text-right">Price</th>
                                            <th width="8%" class="text-right">Qty</th>
                                            <th width="5%" class="text-center">UOM</th>
                                            <th width="10%" class="text-right">Discount</th>
                                            <th width="10%" class="text-right">Amount</th>
                                            <th width="5%" class="text-center">#</th>
                                        </tr>
                                        </thead>
                                        <tbody id="so-table-body">
                                        <?php
                                        if($so_id > 0){
                                            $i=0;
                                            $total_amount = 0;
                                            $amount = 0;
                                            foreach($detail->result() as $det){
                                                echo '<tr>
                                                    <td align="center"> <input type=hidden name="item_id['.$i.']" value="'.$det->item_id.'"> <input type=hidden class="class_status" name="status_detail['.$i.']"  value= "1">'.$det->item_code.'</td>
                                                    <td>'.$det->item_desc.'</td>
                                                    <td> <input type="text" name="price['.$i.']" class="form-control input-sm text-right calcu mask_currency" value="'.$det->price.'"> </td>
                                                    <td> <input type="text" name="qty['.$i.']" class="form-control number_only input-sm text-right calcu qty" value="'.$det->stock_qty.'"> </td>
                                                    <td align="center">'.$det->uom_code.'</td>
                                                    <td> <input type="text" name="discount['.$i.']" class="form-control input input-sm text-right mask_currency calcu disc" value="'.$det->discount.'"> </td>
                                                    <td><input type="text" name="amount['.$i.']"  class="form-control input input-sm text-right amount mask_currency" readonly value="'.$amount = (($det->stock_qty * $det->price) - $det->discount).'"></td>
                                                    <td align="center"> <button type="button" class="btn btn-xs btn-danger remove-item" item-id = "" ><i class="fa fa-remove"></i></button></td>
                                                    </tr>';
                                                $i++;
                                                $total_amount += $amount;
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="portlet-body col-md-4">
                                        <div class="portlet-title">
                                            <div class="actions" style="margin-bottom: 10px;">
                                                <a href="javascript:;" id="btn_lookup_payment" class="btn default green-seagreen  input-sm yellow-stripe">
                                                    <i class="fa fa-plus"></i><span class="hidden-480">&nbsp;&nbsp;Add Payment </span>
                                                </a>
                                            </div>
                                        </div>
                                        <table class="table table-striped table-hover table-bordered" id="payment_list_table">
                                            <thead>
                                            <tr>
                                                <th width="40%">Payment Type</th>
                                                <th class="text-right">Amount <small><i>exclude tax</i></small></th>
                                                <th class="text-center">#</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if($so_id > 0){
                                                $i = 0;
                                                $do_payment = 0;
                                                foreach($payment->result() as $pmt){
                                                    echo '<tr>
                                                        <td> <input type=hidden name="sp_type_id['.$i.']" value="'.$pmt->sp_type_id.'" ><input type=hidden class="class_status" name="status_sp_type['.$i.']"  value= "1"> '.$pmt->sp_type_name.' </td>
                                                        <td> <input type="text" name="sp_type_amount['.$i.']" class="form-control input input-sm payment mask_currency calcu"  min="0" value="'.$pmt->amount.'" > </td>
                                                        <td> <button type="button" class="btn btn-xs btn-danger remove-payment" ><i class="fa fa-remove"></i></button></td>
                                                        </tr>';
                                                    $i++;
                                                    $do_payment += $pmt->amount;
                                                }
                                            }
                                            ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td>DO Payment</td>
                                                <td><input type="text" class="form-control input-sm mask_currency do_payment" readonly value="<?php echo  $so_id > 0 ? $total_amount - $do_payment : '' ?>"/></td>
                                                <td></td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="portlet-body col-md-4 pull-right">
                                        <div class="form-group">
                                            <label class="control-label col-md-4" style="font-weight: bold">Total Amount</label>

                                            <div class="col-md-8">
                                                <input type="text" class="form-control mask_currency total_amount" readonly value="<?php echo $so_id > 0 ? $total_amount : ''; ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" style="font-weight: bold">VAT</label>

                                            <div class="col-md-4">

                                                <select name="taxtype" id="vat-option" class="form-control form-filter select2me calcu">
                                                    <option value=""> -- Select --</option>
                                                    <?php
                                                    $query = $this->db->query("SELECT * FROM tax_type");
                                                    foreach($query->result() as $rows ){
                                                        echo '<option tax-value="'.$rows->taxtype_percent.'" value="'.$rows->taxtype_id.'" ' . ($so_id > 0 ? ($row->taxtype_id == $rows->taxtype_id ? 'selected="selected"' : '') : '') . '>'.$rows->taxtype_code.'</option>';
                                                        $tax = $rows->taxtype_percent/100 * $total_amount;
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control mask_currency tax" readonly value="<?php echo $so_id > 0 ? $tax : '' ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" style="font-weight: bold">Total Amount <small><i>after tax</i></small></label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control text-right mask_currency total_amount_after_tax" readonly value="<?php echo $so_id > 0 ? $total_amount-$tax : '' ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if($so_id > 0){
                                        $log = '';
                                        $modified = '';
                                        $log .= '<ul class="list-unstyled" style="margin-left:-15px;">';
                                        $log .= '<li class="margin-bottom-5"><h6>Created on ' . date_format(new DateTime($row->date_created), 'd/m/Y H:i:s') . ' by ' . get_user_fullname( $row->user_created ) . '</h6></li>';
                                        $log .= '</ul>';
                                        $qry = "SELECT * FROM sales_approved_log WHERE so_id = '".$so_id."' ";
                                        $qry_log = $this->db->query($qry);
                                        if($qry_log->num_rows() > 0){
                                            $log .= '<ul class="list-unstyled" style="margin-left:-15px;">';
                                            foreach($qry_log->result() as $row_log){
                                                $remark = '';
                                                if(trim($row_log->remark) != ''){
                                                    $remark = '<h4 style="margin-left:10px;"><span class="label label-success">Remark : ' . trim($row_log->remark) . '</span></h4>';
                                                }
                                                $log .= '<li class="margin-bottom-5"><h6>' . $row_log->log_subject  . ' on ' . date_format(new DateTime($row_log->approved_date), 'd/m/Y H:i:s') . ' by ' . get_user_fullname( $row_log->approved_id ) . '</h6>' . $remark . '</li>';
                                            }
                                            $log .= '</ul>';
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
<!--CUSTOMER HEADER-->
<div id="customer-modal" class="modal fade" data-replace="true" data-width="900" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>
<!--END OF CUSTOEMR HEADER-->

<!--DELIVERY ADDRESS MODAL-->
<div id="delivery-address-modal" class="modal fade bs-modal-lg" id="large" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="header-title"> </h4>
            </div>
            <div class="modal-body" id="address-list">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- END OF DELIVERY ADDRESS MODAL-->
<script>
    $('.btn-action').live('click', function () {
        var so_code = $(this).attr('data-code');
        var so_id = $(this).attr('data-id');
        var action = $(this).attr('data-action');
        var action_code = $(this).attr('data-action-code');
        if( action == <?php echo STATUS_PROCESS ?>){
            bootbox.confirm("Are you sure want to " + action_code + " " + so_code + " ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        target: '.portlet-body',
                        boxed: true,
                        message: 'Processing...'
                    });

                    $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('sales/sales_order/process_ajax_action');?>",
                            dataType: "json",
                            data: {so_id: so_id}
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
                            $('#form-entry-so').unblock();
                            toastr["error"]("Something has wrong, please try again later.", "Error");
                        });
                }
            });
        } else if( action == <?php echo STATUS_APPROVE ?> ) {
            bootbox.confirm("Are you sure want to " + action_code + " " + so_code + " ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        target: '.portlet-body',
                        boxed: true,
                        message: 'Processing...'
                    });

                    Metronic.blockUI({
                        target: '.portlet-body',
                        boxed: true,
                        message: 'Processing...'
                    });

                    $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('sales/sales_order/approve_ajax_action');?>",
                            dataType: "json",
                            data: {so_id: so_id}
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
                            $('#form-entry-so').unblock();
                            toastr["error"]("Something has wrong, please try again later.", "Error");
                        });
                }
            });
        } else if (action == <?php echo STATUS_DISAPPROVE ?>){
            bootbox.prompt({
                title: "Please enter Disapproved reason for " + so_code + " :",
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
                        toastr["warning"]("Disapproved reason must be filled to proceed, Minimum 5 character.", "Warning");
                    } else {
                        Metronic.blockUI({
                            target: '#form-entry-so',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('sales/sales_order/disapprove_ajax_action');?>",
                                dataType: "json",
                                data: {so_id: so_id, reason:result}
                            })
                            .done(function( msg ) {
                                $('#form-entry-so').unblock();

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
                                $('#form-entry-so').unblock();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                }
            });
        } else if (action == <?php echo STATUS_CANCEL ?>) {
            bootbox.prompt({
                title: "Please enter Cancel reason for " + so_code + " :",
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
                            target: '#form-entry-so',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('sales/sales_order/cancel_ajax_action');?>",
                                dataType: "json",
                                data: {so_id: so_id, reason:result}
                            })
                            .done(function( msg ) {
                                $('#form-entry-so').unblock();

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
                                $('#form-entry-so').unblock();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                }
            });
        } else if (action == <?php echo STATUS_CLOSED ?>) {
            bootbox.prompt({
                title: "Please enter Closed reason for " + so_code + " :",
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
                            target: '#form-entry-so',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('sales/sales_order/closed_ajax_action');?>",
                                dataType: "json",
                                data: {so_id: so_id, reason:result}
                            })
                            .done(function( msg ) {
                                $('#form-entry-so').unblock();

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
                                $('#form-entry-so').unblock();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                }
            });
        }
    })
</script>