<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();

if($inv_id > 0) {
    if($row->status == STATUS_NEW){
        if(check_session_action(get_menu_id(), STATUS_EDIT)) {
            $btn_action .= $btn_save;
        } else {
            $isedit = false;
        }
        if(check_session_action(get_menu_id(), STATUS_POSTED)){
            $btn_action .= btn_action($inv_id, $row->inv_no, STATUS_POSTED);
        }
        if(check_session_action(get_menu_id(), STATUS_CANCEL)){
            $btn_action .= btn_action($inv_id, $row->inv_no, STATUS_CANCEL);
        }
    } else {
        $isedit = false;
        if($row->status == STATUS_POSTED) {
            if(check_session_action(get_menu_id(), STATUS_PRINT)) {
                $btn_action .= btn_print(base_url('inventory/inv/pdf_inv/' . $inv_id . '.tpd'));
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
                            <i class="fa fa-user"></i> <?php echo($inv_id > 0 ? 'Edit' : 'New')?> Invoice
                        </div>
                        <div class="actions">
                            <?php
                            $back_url = base_url('home/home/dashboard.tpd');
                            if ($inv_id > 0) {
                                $back_url = base_url('sales/dp_invoice/menu_manage/1.tpd');
                            }
                            echo btn_back($back_url);
                            ?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="form-entry-inv" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" class="form-control" name="inv_id" value="<?php echo $inv_id?>" readonly/>
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
                                            <label class="control-label col-md-4">Inv No <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <input type="text" id="inv-code" class="form-control" name="inv_code" value="<?php echo $inv_id > 0 ? $row->inv_no : '' ?>" readonly/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Inv Date<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" id="inv-date" name="inv_date" value="<?php echo $inv_id > 0 ? ymd_to_dmy($row->inv_date) : '' ?>" readonly />
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
                                                    <input type="hidden" class="form-control customer" name="customer_id" id="customer-id" value="<?php echo $inv_id > 0 ? $row->customer_id : '' ?>"/>
                                                    <input class="form-control" id="customer-name" type="text" readonly="" value="<?php echo $inv_id > 0 ? $row->customer_name : '' ?>">
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
                                                    <input type="hidden" clas="form-control input-sm vat" id="vat" name="vat" value="<?php echo $inv_id > 0 ? $row->taxtype_percent : '' ?>">
                                                    <input type="hidden" class="form-control so_code" name="so_id" id="so-id" value="<?php echo $inv_id > 0 ? $row->so_id : '' ?>"/>
                                                    <input class="form-control" id="so-code" type="text" readonly="" value="<?php echo $inv_id > 0 ? $row->so_code : '' ?>">
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
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Due Date<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" id="due-date" name="due_date" value="<?php echo $inv_id > 0 ? ymd_to_dmy($row->inv_due_date) : '' ?>" readonly />
                                                      <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                                      </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Invoice Type<span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <select name="invoice_type" id="invoice-type" class="form-control form-filter select2me invoice-type ">
                                                    <?php
                                                    if($inv_id > 0){
                                                        $get_do_payment = $this->db->query("SELECT * FROM sales_payment_type WHERE sp_type_name LIKE 'DO %' ")->row();
                                                        echo '<option value="0"selected="selected" coa-id="' . $get_do_payment->coa_id . '" >DO Payment</option>';

                                                        $qry = "SELECT sop.*, spt.sp_type_id, spt.sp_type_name, spt.coa_id
                                                           from sales_order_payment sop
                                                           join sales_payment_type spt ON sop.paymenttype_id = spt.sp_type_id
                                                           where sop.so_id =  ".$row->so_id." ";
                                                        $payment = $this->db->query($qry);
                                                        foreach($payment->result() as $rows_ ){
                                                            echo '<option value=' . $rows_->sp_type_id . '   desc = "' . $rows_->sp_type_name . '" coa-id="' . $rows_->coa_id . '" amount = ' . $rows_->amount . '  '.($rows_->sp_type_id == $row->invoice_type ? 'selected="selected"' : '').'  >' . $rows_->sp_type_name . '</option>';


                                                        }
                                                    }

                                                    ?>
                                                </select>
                                                <input type="hidden" class="form-control coa_id" name="coa_id" value="<?php echo ($inv_id > 0 ? $row->coa_id : '') ?>" readonly />
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" rows="2" name="remarks"><?php echo ($inv_id > 0 ? $row->description : '') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-body invoice_detail">
                                    <div class="col-md-12">
                                        <div class="portlet-title blue-ebonyclay">
                                            <ul class="nav nav-tabs">
                                                <li class="do-tab <?php echo ($inv_id > 0 && $row->invoice_type == 0 ? 'active' : 'hide') ?> ">
                                                    <a href="#portlet_do" data-toggle="tab">Invoice DO List</a>
                                                </li>
                                                <li class="payment-tab <?php echo ($inv_id > 0 && $row->invoice_type != 0 ? 'active' : 'hide') ?>">
                                                    <a href="#portlet_payment" data-toggle="tab">Invoice Payment </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="portlet-body ">
                                            <div class="tab-content">
                                                <div class="tab-pane do-tab block-input <?php echo ($inv_id > 0 && $row->invoice_type == 0 ? 'active' : 'hide') ?> " id="portlet_do">
                                                    <div class="portlet-title">
                                                        <div class="actions" style="margin-bottom: 10px;">
                                                            <a href="javascript:;" class="btn default green-seagreen btn_lookup_do_list input-sm yellow-stripe ">
                                                                <i class="fa fa-plus"></i><span class="hidden-480">&nbsp;&nbsp;Add DO </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <table class="table table-striped table-hover table-bordered" id="table_do">
                                                        <thead>
                                                        <tr>
                                                            <th width="10%" class="text-center">DO Code</th>
                                                            <th>Description</th>
                                                            <th width="15%" class="text-right">Amount</th>
                                                            <th width="5%" class="text-center">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $tot_amount=0;
                                                            if($inv_id > 0 && $row->invoice_type == 0){
                                                                $i = 0;

                                                                foreach($row_detail->result() as $row_det){
                                                                    echo '<tr>
                                                                              <td align="center">'.$row_det->do_code.' <input type="hidden" class="form-control input-sm" name="do_id['.$i.']"  value='.$row_det->do_id.' readonly><input type=hidden class="class_status" name="status_detail['.$i.']"  value= "1"> </td>
                                                                               <td>'.$row_det->description.' <input type="hidden" class="form-control input-sm" name="desc['.$i.']"  value='.$row_det->do_code.' readonly> </td>
                                                                               <td align="right" ><input type="text"class="form-control input-sm mask_currency" name="amount['.$i.']" id="amount" value='.$row_det->amount.' readonly/></td>
                                                                               <td align="center"><a class="btn btn-danger btn-xs tooltips btn-remove"><i class="fa fa-times"></i></a></td>
                                                                           </tr>';
                                                                    $i++;
                                                                    $tot_amount += $row_det->amount;

                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <th colspan="2" class="text-right">TOTAL</th>
                                                            <th><input type="text" class="form-control input-sm tot_amount mask_currency" name="tot_amount" readonly value="<?php echo ($inv_id > 0 ? $tot_amount : '') ?>"></th>
                                                            <th></th>
                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                    <div class="portlet-body col-md-8">
                                                        In Words: <i class="into-words-pymt"></i>
                                                    </div>
                                                    <div class="row">
                                                        <div class="portlet-body col-md-4 pull-right">
                                                            <table class="table table-striped table-hover table-bordered" >
                                                                <thead>
                                                                <tr>
                                                                    <th width="37%">Payment Type</th>
                                                                    <th class="text-right">Amount</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="payment-list-table" >
                                                                <?php
                                                                if($inv_id > 0 && $row->invoice_type == 0){
                                                                    $i = 0;
                                                                    $tot_sp_amount = 0;
                                                                    foreach($row_payment as $row_pay){
                                                                    echo '<tr>
                                                                            <td>
                                                                                <input type="hidden" name="payment_type_id[' . $i . ']"  class="form-control input-sm mask_currency calcu" value="' . $row_pay->sp_type_id . '">' . $row_pay->sp_type_name . '
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="payment_type_amount[' . $i . ']"  class="form-control input-sm mask_currency calcu" value="' . $row_pay->amount . '">
                                                                                <input type="hidden" name="max_payment_type_amount[' . $i . ']"  class="form-control input-sm mask_currency calcu" >
                                                                            </td>
                                                                                <input type="hidden" name="payment_type_desc[' . $i . ']"  class="form-control input-sm" value="' . $row_pay->sp_type_name . '" >
                                                                            </td>
                                                                        </tr>';
                                                                        $i++;
                                                                        $tot_sp_amount += $row_pay->amount;
                                                                    }
                                                                }
                                                                ?>
                                                                </tbody>
                                                            </table>

                                                            <table>
                                                                <tr>
                                                                    <td width="140px"  >Sub Total
                                                                        <input type="hidden" name="subtotal" class="form-control input-sm mask_currency subtotal" value ="<?php echo ($inv_id > 0 ? $row->total_amount : '') ?>"   readonly/></td>
                                                                    <th width="245px" class="text-right  mask_currency subtotal"> <?php echo ($inv_id > 0 ? $row->total_amount : '') ?> </th>
                                                                </tr>
                                                                <tr>
                                                                    <td width="140px" >VAT
                                                                        <input type="hidden" name="total_tax" class="form-control input-sm mask_currency total_tax" value ="<?php echo ($inv_id > 0 ? $row->total_tax : '') ?>" readonly/>
                                                                    </td>
                                                                    <th width="245px" class="text-right vat_percent mask_currency"> <?php echo ($inv_id > 0 ? $row->total_tax : '')  ?> </th>
                                                                </tr>
                                                                <tr>
                                                                    <td width="140px" >Grand Total
                                                                        <input type="hidden" name="grand_total" class="form-control input-sm mask_currency grand_total" value="<?php echo ($inv_id > 0 ? $row->total_grand : '')  ?>"  readonly/></td>
                                                                    <th width="245px" class="text-right grand_total mask_currency"> <?php echo ($inv_id > 0 ? $row->total_grand : '')  ?> </th>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane payment-tab block-input <?php echo ($inv_id > 0 && $row->invoice_type != 0 ? 'active' : 'hide') ?> " id="portlet_payment">
                                                    <table class="table table-striped table-hover table-bordered" id="table_payment">
                                                        <thead>
                                                        <tr>
                                                            <th>Invoice Description</th>
                                                            <th width="15%" class="text-right">Amount</th>
                                                            <th width="10%">VAT</th>
                                                            <th width="15%" class="text-right">Total</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php if($inv_id > 0 && $row->invoice_type != 0){ ?>

                                                            <tr>
                                                                <td> <?php echo $row_detail->row()->description ?>
                                                                    <input type=hidden class="form-control input-sm" name="desc"  value= '<?php echo $row_detail->row()->description ?>'>
                                                                    <input type=hidden class="class_status" name="status_detail"  value= "1">
                                                                </td>
                                                                <td align="right" >
                                                                    <input type="text" name="tot_amount" class="form-control input-sm mask_currency" value='<?php echo $row_detail->row()->amount ?>' readonly />
                                                                </td>
                                                                <td>
                                                                    <select id="vat-type" class="form-control form-filter input-sm select2me tax-select" name="vat_detail">
                                                                        <option selected="selected" value="">--select--</option>
                                                                       <?php
                                                                       $sql = "SELECT * FROM tax_type WHERE status = " . STATUS_NEW . " ";
                                                                       $vat = $this->db->query($sql);
                                                                       foreach ($vat->result() as $vat_row) {
                                                                            echo'<option value=' . $vat_row->taxtype_id . ' tax-value=' . $vat_row->taxtype_percent . '   >' . $vat_row->taxtype_code . '</option>';
                                                                       }
                                                                       ?>
                                                                    </select>
                                                                    <input type="hidden" name="total_tax" class="form-control input-sm mask_currency total_tax" value="<?php echo ($inv_id > 0 ? $row->total_tax : '')  ?>" readonly/>
                                                                </td>
                                                                <td align="right"><input type="text" name="grand_total" class="form-control input-sm mask_currency tot_amount" readonly value="<?php echo $row->total_grand ?>"/>  </td>
                                                            </tr>


                                                       <?php } ?>
                                                        </tbody>
                                                    </table>
                                                    In Words: <i class="into-words-inv"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if($inv_id > 0){
                                        $log = '';
                                        $modified = '';
                                        $log .= '<ul class="list-unstyled" style="margin-left:-15px;">';
                                        $log .= '<li class="margin-bottom-5"><h6>Created on ' . date_format(new DateTime($row->created_date), 'd/m/Y H:i:s') . '</h6></li>';
                                        $log .= '</ul>';
                                        $qry = "SELECT * FROM sales_approved_log WHERE inv_id = '".$inv_id."' ";
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
</script>