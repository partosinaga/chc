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
                $back_url = base_url('ar/corporate_bill/receipt_al/1.tpd');
                if($allocationheader_id > 0){
                    if($row->status != STATUS_NEW){
                        $form_mode = 'disabled';
                        $back_url = base_url('ar/corporate_bill/receipt_al/2.tpd');
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
							<i class="fa fa-user"></i><?php echo ($allocationheader_id > 0 ? '' : 'New');?> Receipt Allocation
						</div>
						<div class="actions">
                            <a href="<?php echo (isset($row) ? $back_url : base_url('ar/corporate_bill/receipt_al/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="#" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="allocationheader_id" name="allocationheader_id" value="<?php echo $allocationheader_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn btn-sm btn-circle blue" name="save" id ="btn_save"><i class="fa fa-save"></i>&nbsp;Save</button>
                                        <?php
                                        if($allocationheader_id > 0){
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
                                            <label class="control-label col-md-3">Doc No</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="alloc_no" value="<?php echo ($allocationheader_id > 0 ? $row->alloc_no : 'NEW');?>" disabled />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="receipt_date">Date </label>
                                            <div class="col-md-4" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="alloc_date" value="<?php echo ($allocationheader_id > 0 ? dmy_from_db($row->alloc_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Remark</label>
                                            <div class="col-md-8">
                                                <textarea name="alloc_desc" rows="2" class="form-control" style="resize: vertical;"><?php echo ($allocationheader_id > 0 ? $row->alloc_desc : '') ;?></textarea>
                                            </div>
                                        </div>

									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Official Receipt</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="receipt_id" value="<?php echo ($allocationheader_id > 0 ? $row->receipt_id : '');?>">
                                                    <input type="hidden" name="company_id" value="<?php echo ($allocationheader_id > 0 ? $row->company_id : '');?>">
                                                    <input type="hidden" name="reservation_id" value="<?php echo ($allocationheader_id > 0 ? $row->reservation_id : '');?>">
                                                    <input type="text" class="form-control" name="receipt_no" value="<?php echo ($allocationheader_id > 0 ? $row->receipt_no : '');?>" readonly />
                                                     <span class="input-group-btn">
                                                       <a id="btn_lookup_company" class="btn btn-success" href="javascript:;" >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">&nbsp;</label>
                                            <div class="col-md-8">
                                                <span id="company_name" class="control-label text-left bold"><?php echo ($allocationheader_id > 0 ? ($row->company_id > 0 ? $row->company_name : $row->tenant_fullname) : '');?></span>
                                            </div>
                                        </div>
                                        <?php
                                        $show_pending = true;
                                        if($allocationheader_id > 0){
                                            if($row->status != STATUS_NEW){
                                                $show_pending = false;
                                            }
                                        }

                                        if($show_pending){
                                            ?>
                                            <div class="form-group">
                                                <label class="control-label col-md-3">Unallocated</label>
                                                <div class="col-md-6">
                                                    <div class="input-inline ">
                                                        <div class="input-group">
                                                            <span class="input-group-addon " style="font-size: 9pt;">IDR</span>
                                                            <input type="text" id="available_amount" name="available_amount" class="form-control text-right mask_currency" value="<?php echo(isset($available_amount) ? $available_amount : 0); ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <table class="table table-striped table-bordered table-hover table-po-detail" id="table_pending_detail">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th class="text-center" style="width:10%;">
                                                    Inv No
                                                </th>
                                                <th class="text-left" >
                                                    Description
                                                </th>
                                                <th class="text-center" width="10%">
                                                    Trx Type
                                                </th>
                                                <th class="text-right <?php echo (isset($row->status) ? $row->status == STATUS_NEW ? '' : 'hide' : '') ?>" style="width:17%;">
                                                    Pending Amount
                                                </th>
                                                <th class="text-right" style="width:17%;">
                                                    Allocate Amount
                                                </th>
                                                <th class="text-center" style="width:5%;padding-left:7px;">
                                                    <a href="javascript:;" id="btn_generate" class="btn btn-xs purple btn-circle tooltips" data-original-title="Auto Allocation"><i class="fa fa-flash"></i> </a>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if($allocationheader_id > 0){
                                                    if(isset($details)){
                                                        foreach($details as $bill){
                                                            $display = '<tr id="parent_' . $bill['invdetail_id'] . '' . '">
                                                                     <td style="vertical-align:middle;" class="text-center">
                                                                        <input type="hidden" name="allocation_id[]" value="">
                                                                        <input type="hidden" name="invdetail_id[]" value="' . $bill['invdetail_id'] . '">
                                                                        <span class="text-center">' . $bill['inv_no'] . '</span>
                                                                     </td>
                                                                     <td style="vertical-align:middle;">
                                                                        <span class="text-center">' . $bill['description'] . '</span>
                                                                     </td>
                                                                     <td style="vertical-align:middle;" class="text-center">
                                                                        <input type="hidden" name="transtype_id[]" value="' . $bill['transtype_id'] . '">
                                                                        <span class="text-center">' . $bill['transtype_name'] . '</span>
                                                                     </td>';

                                                            if($row->status == STATUS_NEW){
                                                                $display .= '<td style="vertical-align:middle; " class="control-label">
                                                                        <input type="text" name="base_amount[]" value="' . $bill['base_amount'] .'" class="form-control text-right mask_currency input-sm " readonly>
                                                                     </td>
                                                                     <td style="vertical-align:middle;" class="control-label">
                                                                        <input type="text" name="alloc_amount[]" value="' . $bill['alloc_amount'] . '" class="form-control text-right mask_currency input-sm" >
                                                                     </td>
                                                                     <td style="vertical-align:middle;padding-top:8px;padding-left:7px;">
                                                                        <a inv-detail-id="' . $bill['invdetail_id'] .'" inv-is-tax="' . 0 .'" data-placement="top" data-container="body" class="btn btn-xs green-meadow add_amount " href="javascript:;"><i class="fa fa-plus add_amount_plus ' . ($bill['alloc_amount'] > 0 ? 'hide' : '') . '"></i><i class="fa fa-minus add_amount_minus ' . ($bill['alloc_amount'] > 0 ? '' : 'hide') . '"></i>
                                                                        </a>
                                                                    </td>';
                                                            }else{
                                                                $display .= '
                                                                     <td style="vertical-align:middle;" class="control-label">
                                                                        <input type="text" name="alloc_amount[]" value="' . $bill['alloc_amount'] . '" class="form-control text-right mask_currency input-sm" >
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
                                            <label class="control-label col-md-3">Total </label>
                                            <div class="col-md-8">
                                                <div class="input-inline ">
                                                <div class="input-group">
                                                        <span class="input-group-addon " style="font-size: 8pt;">IDR</span>
                                                        <input type="hidden" name="total_allocated_valid" value="">
                                                        <input type="text" id="total_allocated" name="total_allocated" value="<?php echo ($allocationheader_id > 0 ? ($row->alloc_amount) : '0') ;?>" class="form-control text-right mask_currency font-red-sunglo input-sm" readonly>
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
                if($allocationheader_id > 0){
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
<script src="<?php echo base_url('assets/custom/js/ar/receipt_alloc_form.js'); ?>"></script>
<script>
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;

    $(document).ready(function(){
        <?php echo picker_input_date() ;?>
        Toaster.init();

        FormJS.init({
            is_edit : isedit,
            modal_ajax_url : "<?php echo base_url('ar/corporate_bill/get_modal_unallocated_corp');?>",
            lookup_company_ajax_url : "<?php echo base_url('ar/corporate_bill/xmodal_unallocated_corp');?>",
            lookup_bill_detail_ajax_url : "<?php echo base_url('ar/corporate_bill/xcorp_pending_bill_detail');?>",
            save_ajax_url : "<?php echo base_url('ar/corporate_bill/submit_receipt_al.tpd');?>",
            posting_ajax_url : "<?php echo base_url('ar/corporate_bill/xposting_receipt_al');?>"
        });

	});

</script>