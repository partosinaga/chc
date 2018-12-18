<?php
$isedit = true;

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
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box <?php echo BOX; ?>">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i> New SO Package
                        </div>
                        <div class="actions">
                            <?php echo btn_back(base_url('#')); ?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="form-entry-package" class="form-horizontal" onsubmit="return false;">
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
                                                <input type="text" id="so-no" class="form-control" name="so_no" value="<?php echo $so_number ?>" readonly/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">SO Date<span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" id="so-date" name="so_date" value="<?php echo(date('d-m-Y')); ?>" readonly />
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
                                                    <input type="hidden" name="customer" id="customer_id"/>
                                                    <input class="form-control" id="customer_name" type="text" readonly="">
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
                                                <select name="currency" class="form-control form-filter select2me">
                                                    <option value=""> -- Select --</option>
                                                    <?php
                                                    $query = $this->db->query("SELECT * FROM currencytype");
                                                    foreach($query->result() as $row ){
                                                        if($row->currencytype_id == 1){
                                                            echo '<option value="'.$row->currencytype_id.'" selected="selected">'.$row->currencytype_code.'</option>';
                                                        }else{
                                                            echo '<option value="'.$row->currencytype_id.'" >'.$row->currencytype_code.'</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control mask_currency" name="rate">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Request DO Date<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="date_prepare"
                                                           readonly/>
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
                                                <select name="sales" class="form-control form-filter select2me">
                                                    <option value=""> -- Select --</option>
                                                    <?php
                                                    $query = $this->db->query("SELECT * FROM ms_sales WHERE status= '".STATUS_NEW."'  ");
                                                    foreach($query->result() as $row ){
                                                        echo '<option value="'.$row->sales_id.'" >'.$row->sales_name.'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Terms Of Payment</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="term"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Delivery Address <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="delivery_address" id="delivery-address-id">
                                                <textarea class="form-control" rows="2" id="delivery-address"></textarea>
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn delivery-address"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Invoice Address <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="invoice_address" id="invoice-address-id">
                                                <textarea class="form-control" rows="2" id="invoice-address"></textarea>
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn invoice-address"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>

                                            <div class="col-md-9">
                                                <textarea class="form-control" rows="2" name="remarks"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-body ">
                                    <div class="portlet-title">
                                        <div class="actions" style="margin-bottom: 10px;">
                                            <a href="javascript:;" class="btn default green-seagreen btn input-sm yellow-stripe" id="btn_lookup_package">
                                                <i class="fa fa-plus"></i><span
                                                    class="hidden-480">&nbsp;&nbsp;Add Package </span>
                                            </a>
                                        </div>
                                    </div>
                                    <table class="table table-hover table-bordered" id="package_list_table">
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
                                        <tbody id="so-package-table-body">

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
                                        <table class="table table-striped table-hover table-bordered" id="payment-list-table">
                                            <thead>
                                            <tr>
                                                <th width="40%">Paymen Type</th>
                                                <th class="text-right">Amount <small><i>exclude tax</i></small></th>
                                                <th class="text-center">#</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td>DO Payment</td>
                                                <td><input type="text" class="form-control input-sm text-right" readonly value="1.000.000"/></td>
                                                <td></td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="portlet-body col-md-4 pull-right">
                                        <div class="form-group">
                                            <label class="control-label col-md-4" style="font-weight: bold">Total Amount</label>

                                            <div class="col-md-8">
                                                <input type="text" class="form-control mask_currency total_amount" readonly value=""/>
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
                                                <input type="text" class="form-control mask_currency tax" readonly value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4" style="font-weight: bold">Total Amount <small><i>after tax</i></small></label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control text-right mask_currency total_amount_after_tax" readonly value=""/>
                                            </div>
                                        </div>
                                    </div>
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
