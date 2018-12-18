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

                $form_mode = '';
                $back_url = base_url('ar/corporate_bill/receipt.tpd');
                if($receipt_id > 0){
                    if($row->status != STATUS_NEW){
                        $form_mode = 'disabled';
                        $back_url = base_url('ar/corporate_bill/receipt/2.tpd');
                    }
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
							<i class="fa fa-user"></i><?php echo ($receipt_id > 0 ? '' : 'New');?> Official Receipt
						</div>
						<div class="actions">
                            <?php
                            if($receipt_id > 0){
                                if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED ){
                            ?>
                                <a href="<?php echo base_url('ar/report/pdf_official_receipt/'. $row->receipt_no .'.tpd');?>" class="btn default blue-ebonyclay tooltips" target="_blank" data-original-title="Official Receipt"><i class="fa fa-print"></i></a>
                                <!-- a href="<?php echo base_url('ar/report/pdf_rv_voucher/'. $receipt_id .'.tpd');?>" class="btn default blue-ebonyclay tooltips" target="_blank" data-original-title="Voucher"><i class="fa fa-print"></i></a -->
                            <?php
                                }
                            }
                            ?>
                            <a href="<?php echo (isset($row) ? $back_url : base_url('ar/corporate_bill/receipt.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="#" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="receipt_id" name="receipt_id" value="<?php echo $receipt_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn btn-sm btn-circle blue" name="save" id ="btn_save"><i class="fa fa-save"></i>&nbsp;Save</button>
                                        <?php
                                        if($receipt_id > 0){
                                            if($row->status == STATUS_NEW ){
                                        ?>
                                                &nbsp;
                                                <button type="button" class="btn btn-sm purple btn-circle" id="submit-posting" ><i class="fa fa-slack"></i>&nbsp;Posting</button>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
								</div>

							</div>
                            <?php
                            }
                            ?>
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
								<div class="row">
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Receipt No</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="receipt_no" value="<?php echo ($receipt_id > 0 ? $row->receipt_no : 'NEW');?>" disabled />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="receipt_date">Date </label>
                                            <div class="col-md-4" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="receipt_date" value="<?php echo ($receipt_id > 0 ? dmy_from_db($row->receipt_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Company</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="company_id" value="<?php echo ($receipt_id > 0 ? $row->company_id : '');?>">
                                                    <input type="hidden" name="reservation_id" value="0">
                                                    <input type="hidden" name="is_invoice" value="<?php echo ($receipt_id > 0 ? $row->is_invoice : '');?>">
                                                    <input type="text" class="form-control" name="company_name" value="<?php echo ($receipt_id > 0 ? $row->company_id > 0 ? $row->company_name : $row->tenant_fullname : '');?>" readonly />
                                                     <span class="input-group-btn">
                                                       <a id="btn_lookup_company" class="btn btn-success" href="javascript:;" >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                            $show_pending = true;
                                            if($receipt_id > 0){
                                                if($row->status != STATUS_NEW){
                                                    $show_pending = false;
                                                }
                                            }

                                            if($show_pending){
                                        ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Pending Total</label>
                                            <div class="col-md-8">
                                                <div class="input-inline ">
                                                    <div class="input-group">
                                                        <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                        <input type="text" id="pending_amount" name="pending_amount" class="form-control text-right mask_currency" value="<?php echo(isset($pending_amount) ? $pending_amount : 0); ?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <div class="form-group ">
                                            <label class="control-label col-md-3">Payment</label>
                                            <div class="col-md-8">
                                                <div class="input-inline ">
                                                    <div class="input-group">
                                                        <input type="hidden" name="min_amount" value="">
                                                        <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                        <input type="text" id="payment_amount" name="payment_amount" value="<?php echo ($receipt_id > 0 ? ($row->receipt_bankcharges + $row->receipt_paymentamount) : '0') ;?>" class="form-control text-right mask_currency font-blue" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Payment Type </label>
                                            <div class="col-md-6">
                                                <select name="paymenttype_id" class="select2me form-control input-medium">
                                                    <?php
                                                    $payments = $this->db->query('select * from ms_payment_type where status = '.STATUS_NEW . ' and
                                                      payment_type NOT IN(' . PAYMENT_TYPE::AR_TRANSFER . ',' . PAYMENT_TYPE::PAYMENT_GATEWAY . ') order by pos ');
                                                    foreach($payments->result_array() as $payType){
                                                        echo '<option value="'. $payType['paymenttype_id'] .'" payment-type="' . $payType['payment_type'] . '" ' . ($receipt_id > 0 ? ($row->paymenttype_id == $payType['paymenttype_id'] ? 'selected="selected"' : '') : '') . '>' . $payType['paymenttype_code'] . ' - ' . $payType['paymenttype_desc'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="card_info" class="<?php echo ($receipt_id > 0 ? ($row->payment_type == PAYMENT_TYPE::CREDIT_CARD || $row->payment_type == PAYMENT_TYPE::DEBIT_CARD ? '' : 'hide') : 'hide'); ?>">
                                            <div class="form-group" >
                                                <label class="control-label col-md-3" style="padding-top: 6px;">Name</label>
                                                <div class="col-md-6">
                                                    <input type="text" name="creditcard_name" value="<?php echo $receipt_id > 0 ? $row->card_name : '' ?>" class="form-control input-medium">
                                                </div>
                                            </div>
                                            <div class="form-group" >
                                                <label class="control-label col-md-3" style="padding-top: 6px;">Card No</label>
                                                <div class="col-md-6">
                                                    <input type="text" name="creditcard_no" value="<?php echo $receipt_id > 0 ? $row->card_no : '' ?>" class="form-control input-medium mask_credit_card">
                                                </div>
                                            </div>
                                            <div class="form-group <?php echo(($receipt_id > 0 ? $row->payment_type == PAYMENT_TYPE::CREDIT_CARD ? '' : 'hide' : 'hide')) ?>" id="card_info_expiry">
                                                <label class="control-label col-md-3" style="padding-top: 6px;">Expiry</label>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="col-md-2 col-sm-2">
                                                            <div class="row">
                                                                <select name="creditcard_expiry_month" class="select2me form-control">
                                                                    <?php
                                                                    for($i=1;$i<=12;$i++){
                                                                        echo '<option value="'. $i .'" ' . ($receipt_id > 0 ? ($row->card_expiry_month == $i ? 'selected="selected"' : '') : '') . '>' . (strlen($i) == 1 ? '0'. $i : $i) . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-4">
                                                            <select name="creditcard_expiry_year" class="select2me form-control">
                                                                <?php
                                                                $current = date('Y');
                                                                for($i=$current;$i<=$current+10;$i++){
                                                                    echo '<option value="'. $i .'" ' . ($receipt_id > 0 ? ($row->card_expiry_year == $i ? 'selected="selected"' : '') : '') . '>' . (strlen($i) == 1 ? '0'. $i : $i) . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="form-group clear <?php echo(($receipt_id > 0 ? $row->payment_type != PAYMENT_TYPE::CREDIT_CARD ? '' : 'hide' : '')) ?>" id="bank_account_info">
                                            <label class="control-label col-md-3">Bank Account </label>
                                            <div class="col-md-6">
                                                <select name="bankaccount_id" class="select2me form-control input-medium bankaccount">
                                                    <option value="">-- Select --</option>
                                                    <?php
                                                    $banks = $this->db->query('select * from fn_bank_account where status = '.STATUS_NEW . ' order by bank_id, bankaccount_code');
                                                    foreach($banks->result_array() as $bank){
                                                        if($bank['iscash'] <= 0){
                                                            echo '<option value="'. $bank['bankaccount_id'] .'" class="option_bank" ';
                                                            if(isset($row)){
                                                                if($bank['bankaccount_id'] == $row->bankaccount_id)
                                                                    echo 'selected';
                                                            }
                                                            echo '>' . $bank['bankaccount_desc'] . '</option>';
                                                        }else{
                                                            echo '<option value="'. $bank['bankaccount_id'] .'" class="option_cash hide" ';
                                                            if(isset($row)){
                                                                if($bank['bankaccount_id'] == $row->bankaccount_id)
                                                                    echo 'selected';
                                                            }
                                                            echo '>' . $bank['bankaccount_desc'] . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Remark</label>
                                            <div class="col-md-8">
                                                <textarea name="remark" rows="2" class="form-control" style="resize: vertical;"><?php echo ($receipt_id > 0 ? $row->receipt_desc : '') ;?></textarea>
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row <?php echo $receipt_id > 0 ? isset($details) ? '' : 'hide' : 'hide'; ?>" id="panel_detail">
                                    <input type="hidden" name="total_allocated_valid" value="">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-6">
                                        <table class="table table-striped table-bordered table-hover table-po-detail " id="table_pending_detail">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th class="text-center">
                                                    Inv No
                                                </th>
                                                <th class="text-center" width="15%">
                                                    Date
                                                </th>
                                                <th class="text-center" width="15%">
                                                    Due Date
                                                </th>
                                                <th class="text-right " width="20%">
                                                    Pending Amount
                                                </th>
                                                <th class="text-right" width="20%">
                                                    Allocate Amount
                                                </th>
                                                <th class="text-center" width="3%" style="width:5%;padding-left:7px;">
                                                    <a href="javascript:;" id="btn_generate" class="btn btn-xs purple btn-circle tooltips" data-original-title="Auto Allocation"><i class="fa fa-flash"></i> </a>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if($receipt_id > 0){
                                                    if(isset($details)){
                                                        foreach($details as $bill){
                                                            $display = '<tr id="parent_' . $bill['inv_id'] . '' . '">
                                                                     <td style="vertical-align:middle;" class="text-center">
                                                                        <input type="hidden" name="invoice_id[]" value="' . $bill['inv_id'] . '">
                                                                        <span class="text-center">' . $bill['inv_no'] . '</span>
                                                                     </td>
                                                                     <td style="vertical-align:middle;" class="text-center">
                                                                        <span class="text-center">' . dmy_from_db($bill['inv_date']) . '</span>
                                                                     </td>
                                                                     <td style="vertical-align:middle;" class="text-center">
                                                                        <span class="text-center">' . dmy_from_db($bill['inv_due_date']) . '</span>
                                                                     </td>';

                                                            if($row->status == STATUS_NEW){
                                                                $display .= '
                                                                     <td style="vertical-align:middle;" class="control-label">
                                                                        <input type="hidden" name="base_amount[]" value="' . $bill['pending_amount'] . '">
                                                                        <input type="text" name="pending_amount[]" value="' . $bill['pending_amount'] . '" class="form-control text-right mask_currency input-sm" readonly>
                                                                     </td>
                                                                     <td style="vertical-align:middle;" class="control-label">
                                                                        <input type="text" name="alloc_amount[]" value="' . $bill['alloc_amount'] . '" class="form-control text-right mask_currency input-sm" >
                                                                     </td>
                                                                     <td style="vertical-align:middle;padding-top:8px;padding-left:7px;">
                                                                        <!-- input type="checkbox" name="inv_id[]" value="' . $bill['inv_id'] . '" ' . $bill['checked'] . ' class="chk_inv_id" -->
                                                                        <a inv-id="' . $bill['inv_id'] .'" inv-is-tax="' . 0 .'" data-placement="top" data-container="body" class="btn btn-xs green-meadow add_amount " href="javascript:;"><i class="fa fa-plus add_amount_plus ' . ($bill['alloc_amount'] > 0 ? 'hide' : '') . '"></i><i class="fa fa-minus add_amount_minus ' . ($bill['alloc_amount'] > 0 ? '' : 'hide') . '"></i>
                                                                    </td>';
                                                            }else{
                                                                $display .= '
                                                                     <td style="vertical-align:middle;" class="control-label">
                                                                        <input type="text" name="pending_amount[]" value="' . $bill['pending_amount'] . '" class="form-control text-right mask_currency input-sm" readonly>
                                                                     </td>
                                                                     <td style="vertical-align:middle;" class="control-label">
                                                                        <input type="text" name="alloc_amount[]" value="' . $bill['alloc_amount'] . '" class="form-control text-right mask_currency input-sm" readonly>
                                                                     </td>
                                                                     <td style="vertical-align:middle;padding-top:8px;padding-left:10px;">
                                                                        <i class="fa fa-check">
                                                                    </td>';
                                                            }

                                                            $display .= '</tr>';
                                                            echo $display;
                                                        }
                                                    }
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Allocation </label>
                                            <div class="col-md-8">
                                                <div class="input-inline ">
                                                <div class="input-group">
                                                        <span class="input-group-addon " style="font-size: 8pt;">IDR</span>
                                                        <input type="hidden" name="total_allocated_valid" value="">
                                                        <input type="text" id="total_allocated" name="total_allocated" value="<?php echo ($receipt_id > 0 ? (isset($alloc_total) ? $alloc_total : 0 ) : 0) ;?>" class="form-control text-right mask_currency font-red-sunglo input-sm" readonly>
                                                    </div>
                                                </div>
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
        <div class="row">
            <div class="col-md-12">
                <?php
                if($receipt_id > 0){
                    $created = '';
                    $modified = '';

                    if ($row->created_by > 0) {
                        $created .= "<div class='col-md-8'><h6>Created by " . get_user_fullname( $row->created_by) . " (" . date_format(new DateTime($row->created_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                    }
                    /*
                    if ($row->modified_by > 0) {
                        $modified .= "<div class='col-md-4'><h6>Last Modified by " . get_user_fullname( $row->modified_by) . " (" . date_format(new DateTime($row->modified_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                    }
                    */
                    echo '<div class="note note-info" style="margin:10px;">
                                                    ' . $created . '
                                                    ' . '' . '
                                                    <div style="clear:both;"></div>
                                                </div>';
                }
                ?>
            </div>
        </div>

	</div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<div id="ajax-posting" class="modal fade bs-modal-sm"  data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Posting date :</h4>
            </div>
            <div class="modal-body">
                <div class="input-group " data-date-format="dd-mm-yyyy">
                    <input type="text" class="form-control" name="c_posting_date" value="<?php echo (date('d-m-Y'));?>" readonly>
					<span class="input-group-btn hide">
						<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
					</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue" id="submit-posting">Posting</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script src="<?php echo base_url('assets/custom/js/toaster.js'); ?>"></script>
<script src="<?php echo base_url('assets/custom/js/ar/receipt_form.js'); ?>"></script>
<script>
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;

    $(document).ready(function(){
        <?php echo picker_input_date() ;?>
        Toaster.init();

        FormJS.init({
            is_edit : isedit,
            ptype_bank_transfer : "<?php echo PAYMENT_TYPE::BANK_TRANSFER; ?>",
            ptype_debit_card : "<?php echo PAYMENT_TYPE::DEBIT_CARD; ?>",
            ptype_cash_only : "<?php echo PAYMENT_TYPE::CASH_ONLY; ?>",
            ptype_credit_card : "<?php echo PAYMENT_TYPE::CREDIT_CARD; ?>",
            reservation_ajax_url : "<?php echo base_url('ar/corporate_bill/get_modal_corporate_unpaid');?>",
            lookup_company_ajax_url : "<?php echo base_url('ar/corporate_bill/xmodal_corporate_unpaid');?>",
            save_ajax_url : "<?php echo base_url('ar/corporate_bill/submit_ar_receipt.tpd');?>",
            posting_ajax_url : "<?php echo base_url('ar/corporate_bill/xpost_official_receipt');?>",
            lookup_pending_invoice_ajax_url : "<?php echo base_url('ar/corporate_bill/xcorp_pending_invoice');?>"
        });

	});

</script>