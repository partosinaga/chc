<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();

if ($do_id > 0) {

    if ($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE) {

        if($row->do_status != 0){ //if not yet process
            if (check_session_action(get_menu_id(), STATUS_PROCESS)) {
                $btn_action .= btn_action($do_id, $row->do_code, STATUS_PROCESS);
            }
            if (check_session_action(get_menu_id(), STATUS_CANCEL)) {
                $btn_action .= btn_action($do_id, $row->do_code, STATUS_CANCEL);
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
            $btn_action .= btn_print(base_url('purchasing/po/pdf_po/' . $do_id . '.tpd'));
        }
        if (check_session_action(get_menu_id(), STATUS_CLOSED)) {
            $btn_action .= btn_action($do_id, $row->do_code, STATUS_CLOSED);
        }
    } else if( $row->status == STATUS_PROCESS ){
        if (check_session_action(get_menu_id(), STATUS_APPROVE)) {
            $btn_action .= btn_action($do_id, $row->do_code, STATUS_APPROVE);
        }
        if (check_session_action(get_menu_id(), STATUS_DISAPPROVE)) {
            $btn_action .= btn_action($do_id, $row->do_code, STATUS_DISAPPROVE);
        }
    } else if ($row->status == STATUS_CLOSED) {
        if (check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(base_url('purchasing/po/pdf_po/' . $do_id . '.tpd'));
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
                            <i class="fa fa-user"></i> <?php echo($do_id > 0 ? 'Edit' : 'New'); ?> DO
                        </div>
                        <div class="actions">
                            <?php
                            $back_url = base_url('home/home/dashboard.tpd');
                            if ($do_id > 0) {
                                $back_url = base_url('sales/delivery_order/menu_manage/1.tpd');
                            }
                            echo btn_back($back_url);
                            ?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="form-entry-do" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" class="form-control" name="do_id" value="<?php echo $do_id?>" readonly/>
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
                                            <label class="control-label col-md-4">DO No <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <input type="text" id="do-code" class="form-control" name="do_code" value="<?php echo $do_id > 0 ? $row->do_code : '' ?>" readonly/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">DO Date<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" id="do-date" name="do_date" value="<?php echo $do_id > 0 ? ymd_to_dmy($row->do_date) : '' ?>" readonly />
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
                                                    <input type="hidden" class="form-control customer" name="customer" id="customer-id" value="<?php echo $do_id > 0 ? $row->customer_id : '' ?>"/>
                                                    <input class="form-control" id="customer-name" type="text" readonly="" value="<?php echo $do_id > 0 ? $row->customer_name : '' ?>">
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_customer" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">SO No.<span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <input type="hidden" class="form-control so_code" name="so_id" id="so-id" value="<?php echo $do_id > 0 ? $row->so_id : '' ?>"/>
                                                    <input class="form-control" id="so-code" type="text" readonly="" value="<?php echo $do_id > 0 ? $row->so_code : '' ?>">
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_so" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-6 col-sm-6">
                                        <label class="control-label col-md-4">Delivery by<span class="required" aria-required="true"> * </span></label>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="hidden" class="form-control customer" name="delivery" id="delivery-by" value="<?php echo $do_id > 0 ? $row->delivery_by : '' ?>"/>
                                                <input class="form-control" id="delivery-name" type="text" readonly="" value="<?php echo $do_id > 0 ? $row->delivery_type_name : '' ?>">
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_delivery" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Delivery Address <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="delivery_address" id="delivery-address-id" value="<?php echo $do_id > 0 ? $row->customer_address_id : '' ?>">
                                                <textarea class="form-control" rows="2" id="delivery-address" readonly><?php echo $do_id > 0 ? $row->customer_address : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" rows="2" name="remarks"><?php echo $do_id > 0 ? $row->remarks : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-body ">
                                    <div class="portlet-title">
                                        <div class="actions" style="margin-bottom: 10px;">
                                            <a href="javascript:;" class="btn default green-seagreen btn input-sm yellow-stripe" id="btn_lookup_items">
                                                <i class="fa fa-plus"></i><span class="hidden-480">&nbsp;&nbsp;Add Items </span>
                                            </a>
                                        </div>
                                    </div>
                                    <table class="table table-striped table-hover table-bordered" id="item_list_table">
                                        <thead>
                                        <tr>
                                            <th width="8%" class="text-center">Code</th>
                                            <th>Description</th>
                                            <th width="5%" class="text-center">UOM</th>
                                            <th width="8%" class="text-right">Qty</th>
                                            <th width="10%" class="text-right">Delivery Qty</th>
                                            <th width="5%" class="text-center">#</th>
                                        </tr>
                                        </thead>
                                        <tbody id="so-table-body">
                                            <?php
                                                if($do_id > 0){
                                                    $i=0;
                                                    $stock_qty = 0;
                                                    $max_deliv = 0;
                                                    foreach($detail->result() as $row_det){
                                                        foreach($so_stock_qty->result() as $so_row_qty){
                                                            if($row_det->stock_id == $so_row_qty->stock_id){
                                                                $stock_qty = $so_row_qty->stock_qty;
                                                                break;
                                                            }
                                                        }
                                                        foreach($max_delivery->result() as $row_max_deliv){
                                                            if($row_det->stock_id == $row_max_deliv->stock_id){
                                                                $max_deliv = $row_max_deliv->stock_delivered;
                                                                break;
                                                            }
                                                        }
                                                        $max = $stock_qty-$max_deliv;
                                                        echo '<tr>
                                                            <td align="center"><input type=hidden name="item_id['.$i.']" value="'.$row_det->item_id.'"> <input type=hidden class="class_status" name="status_detail['.$i.']"  value= "1">'.$row_det->item_code.'</td>
                                                            <td>'.$row_det->item_desc.'</td>
                                                            <td align="center"> <input type="hidden" name="item_price['.$i.']" class="form-control number_only input-sm number_only" value="'.$row_det->item_price.'"> '.$row_det->uom_code.'</td>
                                                            <td align="right"> <input type="hidden" name="qty['.$i.']" class="form-control input-sm number_only" value='.$stock_qty.'> '.$stock_qty.'</td>
                                                            <td align="right"><input type="text" name="delivery_qty['.$i.']"  class="form-control input input-sm number_only" value="'.$row_det->delivery_qty.'" max-delivery="'.$max.'"></td>
                                                            <td align="center"> <button type="button" class="btn btn-xs btn-danger remove-item" ><i class="fa fa-remove"></i></button></td>
                                                            </tr>';
                                                        $i++;
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if($do_id > 0){
                                        $log = '';
                                        $modified = '';
                                        $log .= '<ul class="list-unstyled" style="margin-left:-15px;">';
                                        $log .= '<li class="margin-bottom-5"><h6>Created on ' . date_format(new DateTime($row->date_created), 'd/m/Y H:i:s') . ' by ' . get_user_fullname( $row->user_created ) . '</h6></li>';
                                        $log .= '</ul>';
                                        $qry = "SELECT * FROM sales_approved_log WHERE do_id = '".$do_id."' ";
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
        var do_code = $(this).attr('data-code');
        var do_id = $(this).attr('data-id');
        var action = $(this).attr('data-action');
        var action_code = $(this).attr('data-action-code');
        if( action == <?php echo STATUS_PROCESS ?>){
            bootbox.confirm("Are you sure want to " + action_code + " " + do_code + " ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        target: '.portlet-body',
                        boxed: true,
                        message: 'Processing...'
                    });

                    $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('sales/delivery_order/process_ajax_action');?>",
                            dataType: "json",
                            data: {do_id: do_id}
                        })
                        .done(function (msg) {
                            $('.portlet-body').unblock();

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
            });
        } else if( action == <?php echo STATUS_APPROVE ?> ) {
            bootbox.confirm("Are you sure want to " + action_code + " " + do_code + " ?", function (result) {
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
                            url: "<?php echo base_url('sales/delivery_order/approve_ajax_action');?>",
                            dataType: "json",
                            data: {do_id: do_id}
                        })
                        .done(function (msg) {
                            $('.portlet-body').unblock();

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
            });
        } else if (action == <?php echo STATUS_DISAPPROVE ?>){
            bootbox.prompt({
                title: "Please enter Disapproved reason for " + do_code + " :",
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
                            target: '#form-entry',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('sales/delivery_order/disapprove_ajax_action');?>",
                                dataType: "json",
                                data: {do_id: do_id, reason:result}
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
        } else if (action == <?php echo STATUS_CANCEL ?>) {
            bootbox.prompt({
                title: "Please enter Cancel reason for " + do_code + " :",
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
                                url: "<?php echo base_url('sales/delivery_order/cancel_ajax_action');?>",
                                dataType: "json",
                                data: {do_id: do_id, reason:result}
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
        } else if (action == <?php echo STATUS_CLOSED ?>) {
            bootbox.prompt({
                title: "Please enter Closed reason for " + do_code + " :",
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
                            target: '#form-entry',
                            boxed: true,
                            message: 'Processing...'
                        });

                        $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('sales/delivery_order/closed_ajax_action');?>",
                                dataType: "json",
                                data: {do_id: do_id, reason:result}
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
        }
    })
</script>