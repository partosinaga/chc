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
                if($reservation_id > 0){
                    if($row->status == ORDER_STATUS::CHECKIN ||
                        $row->status == ORDER_STATUS::RESERVED){
                        $form_mode = '';
                    }else{
                        $form_mode = 'disabled';
                    }
                }

                $is_supervisor = false;
                if(check_function_action('frontdesk','reservation', 'reservation_manage',STATUS_AUDIT)) {
                    $is_supervisor = true;
                }

				?>
			</ul>
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <a href="<?php echo (isset($back_url) ? $back_url : base_url('frontdesk/management/guest_manage/1.tpd')); ?>" class="btn btn-sm default green-stripe">
                        <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                    </a>
                </div>
            </div>
		</div>
        <div class="invoice">
		<!-- END PAGE HEADER-->
        <div class="row " style="margin-top: 0px;">
            <?php echo show_flash($this->session->flashdata('flash_message'),$this->session->flashdata('flash_message_class'));?>
            <div class="col-md-6 col-xs-6 " >
                <p><h3 style="margin-top: 0px;">#<?php echo ($reservation_id > 0 ? $row->reservation_code : '');?>&nbsp;
                <small class="font-green-seagreen bold"><?php echo ($reservation_id > 0 ? $row->hidden_me <= 0 ? strtoupper($tenant->tenant_fullname) : '' : ''); ?></small>&nbsp;&nbsp;<span class="badge badge-primary bold" ><?php echo ($reservation_id > 0 ? (strtoupper(RES_TYPE::caption($row->reservation_type))) : ''); ?> </span></h3>
                </p>
            </div>
        </div>
        <hr style="margin-top: 0px;margin-bottom: 2px;">
        <div class="row" style="margin-top: 0px;padding-top: 0px;">
            <?php if(isset($company)){ ?>
                <div class="col-md-3 col-xs-3">
                    <h3>Corporate </h3>
                    <ul class="list-unstyled">
                        <li>
                            <?php echo $company->company_name ; ?>
                        </li>
                        <li>
                            <?php echo nl2br($company->company_address); ?>
                        </li>
                        <li>
                            <?php echo nl2br($company->company_phone); ?>
                        </li>
                        <li>
                            <?php echo 'Attn : ' . $company->company_pic_name; ?>
                        </li>
                        <li>
                            <?php echo $company->company_pic_phone; ?>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3 col-xs-3">
                    <h3>Guest</h3>
                    <ul class="list-unstyled">
                        <?php if($reservation_id > 0) {
                                if($row->hidden_me <= 0 ) { ?>
                        <li>
                            <?php echo ($reservation_id > 0 ? $tenant->tenant_fullname : ''); ?>
                        </li>
                        <li>
                            <?php echo ($reservation_id > 0 ? (trim($tenant->tenant_address) != '' ? $tenant->tenant_address : '-'): '-'); ?>
                        </li>
                        <li>
                            <?php echo ($reservation_id > 0 ? (trim($tenant->tenant_city) != '' ? $tenant->tenant_city : '-'): '-'); ?>&nbsp;<?php echo ($reservation_id > 0 ? $tenant->tenant_postalcode: ''); ?>
                        </li>
                        <li>
                            <?php echo ($reservation_id > 0 ? (trim($tenant->tenant_country) != '' ? $tenant->tenant_country : '-'): '-'); ?>
                        </li>
                        <?php }else{ ?>
                          <li><?php echo INCOGNITO; ?></li>
                        <?php }
                        }?>
                    </ul>
                </div>
            <?php }else { ?>
                <div class="col-md-6 col-xs-6">
                    <h3>Guest</h3>
                    <ul class="list-unstyled">
                        <?php if($reservation_id > 0) {
                        if($row->hidden_me <= 0 ) { ?>
                        <li>
                            <?php echo ($reservation_id > 0 ? $tenant->tenant_fullname : ''); ?>
                        </li>
                        <li>
                            <?php echo ($reservation_id > 0 ? (trim($tenant->tenant_address) != '' ? $tenant->tenant_address : '-'): '-'); ?>
                        </li>
                        <li>
                            <?php echo ($reservation_id > 0 ? (trim($tenant->tenant_city) != '' ? $tenant->tenant_city : '-'): '-'); ?>&nbsp;<?php echo ($reservation_id > 0 ? $tenant->tenant_postalcode: ''); ?>
                        </li>
                        <li>
                            <?php echo ($reservation_id > 0 ? (trim($tenant->tenant_country) != '' ? $tenant->tenant_country : '-'): '-'); ?>
                        </li>
                        <?php }else{ ?>
                            <li>
                                <?php echo INCOGNITO; ?>
                            </li>
                        <?php    }
                        }?>
                    </ul>
                </div>
            <?php } ?>

            <div class="col-md-2 col-xs-2">
                <h3>Reservation </h3>
                <ul class="list-unstyled">
                    <li>
                        <strong>Check In :</strong> <?php echo ($reservation_id > 0 ? dmy_from_db($row->arrival_date) : '');?>
                    </li>
                    <li>
                        <strong>Check Out :</strong> <?php echo ($reservation_id > 0 ? dmy_from_db($row->departure_date) : '');?>
                    </li>
                    <li>
                        <strong>Duration :</strong> <?php echo (isset($room_duration) ? $room_duration : '');?>
                    </li>
                    <li>
                        <strong>No of guest :</strong> <?php echo format_num($row->qty_adult,0) . ' (' . $row->qty_child . ')'; ?>
                    </li>
                    <li>
                        <strong>Payment Type :</strong> <?php echo ($row->billing_type == BILLING_TYPE::MONTHLY ? 'Monthly' : 'Full Paid');?>
                    </li>
                    <li>
                        <strong>Rate Type :</strong> <?php echo ($row->is_rate_yearly > 0 ? 'Yearly' : 'Monthly');?>
                    </li>
                </ul>
            </div>  
            <div class="col-md-2 col-xs-2">
                <h3>Contact </h3>
                <ul class="list-unstyled">
                    <?php
                    if($reservation_id > 0) {
                        if ($row->hidden_me <= 0) {
                            ?>
                            <li>
                                <strong>ID # :</strong>
                                <?php
                                if ($tenant->id_type == 2) {
                                    echo 'Passport / ' . $tenant->passport_no;
                                } else if ($tenant->id_type == 3) {
                                    echo 'KITAS / ' . $tenant->passport_no;
                                } else {
                                    echo 'KTP / ' . $tenant->passport_no;
                                }
                                ?>
                            </li>
                            <li>
                                <strong>Issued place
                                    :</strong> <?php echo(trim($tenant->passport_issuedplace) != '' ? $tenant->passport_issuedplace . ' / ' . dmy_from_db($tenant->passport_issueddate) : '-') ?>
                            </li>
                            <li>
                                <strong>Phone
                                    :</strong> <?php echo $tenant->tenant_cellular . ' / ' . $tenant->tenant_phone; ?>
                            </li>
                            <li>
                                <strong>Email :</strong> <?php echo $tenant->tenant_email; ?>
                            </li>
                            <li><a href="<?php echo base_url('frontdesk/setup/tenant_manage/0/' . $row->tenant_id . '.tpd');?>" target="_blank">See Detail</a></li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="col-md-2 col-xs-2">
                <ul class="list-unstyled">
                    <?php
                    if($row->status == ORDER_STATUS::CHECKOUT){
                    ?>
                        <li>
                            <div class="well bg-red-sunglo" style="margin-bottom: 0px;margin-top: 20px;padding-top:10px;padding-bottom:10px;padding-right:10px;">
                                <span class="bold font-lg">CHECKED OUT</span><br><strong>date :</strong>&nbsp;<?php echo date('d-m-Y',strtotime($row->checkout_date)); ?><br><strong>on :</strong><?php echo date('H:i:s',strtotime($row->checkout_date)); ?>
                            </div>
                        </li>
                    <?php
                    }else{
                    ?>
                        <li>
                            <button type="button" class="btn red-intense pull-right <?php echo (isset($invoice_amount) ? ($invoice_amount > 0 ? ' hide ' : ' btn-lg ') : ' btn-lg ') ?>" style="margin-top:10px;" name="btnCheckout" id="btn-checkout"  is-frontdesk="<?php echo ($row->is_frontdesk); ?>">Check Out <i class="fa fa-check"></i></button>
                        </li>
                    <?php
                    }
                    ?>
                    <li>
                        <button type="button" class="btn green pull-right <?php echo (isset($invoice_amount) ? ($invoice_amount <= 0 ? ' hide ' : ' btn-lg ') : ' btn-lg ') ?>" style="margin-top:10px;" name="btnReceipt" id="btn-receipt"  pay-amount="<?php echo (isset($invoice_amount) ? $invoice_amount : 0) ?>" is-frontdesk="<?php echo ($row->is_frontdesk); ?>">Pay <i class="fa fa-slack"></i> <?php echo format_num($invoice_amount,0); ?></button>
                    </li>
                    <li>
                        <?php if($row->reservation_type == RES_TYPE::CORPORATE) {?>
                        <div class="btn-group pull-right" style="padding-top: 10px;">
                            <button class="btn blue-chambray btn-sm btn-circle dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                               Corporate Folio&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li >
                                    <a target="_blank" href="<?php echo base_url('frontdesk/management/pdf_folio/'. ($reservation_id > 0 ? $row->reservation_id : '0') .'/1.tpd');?>"  class="font-blue-hoki" >Corporate Folio <i class="fa fa-print"></i></a>
                                </li>
                                <li >
                                    <a target="_blank" href="<?php echo base_url('frontdesk/management/pdf_folio/'. ($reservation_id > 0 ? $row->reservation_id : '0') .'/0.tpd');?>" class="font-blue-hoki" >Guest Folio <i class="fa fa-print"></i></a>
                                </li>
                            </ul>
                        </div>
                        <?php }else{ ?>
                            <a target="_blank" href="<?php echo base_url('frontdesk/management/pdf_folio/'. ($reservation_id > 0 ? $row->reservation_id : '0') .'/1.tpd');?>" class="btn btn-circle blue-chambray pull-right" style="margin-top:10px;">Guest Folio <i class="fa fa-print"></i></a>
                        <?php } ?>
                    </li>
                    <li>
                        <div class="btn-group pull-right" style="padding-top: 10px;">
                            <button class="btn blue-chambray btn-sm btn-circle dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                                Other&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <?php if($row->status == ORDER_STATUS::CHECKIN){ ?>
                                <li>
                                    <a href="javascript:;" class="inverse btn-add-srf text-right" style="margin-top:10px;">Add SRF <i class="fa fa-gavel"></i></a>
                                </li>
                                 <?php } ?>
                                <li>
                                    <a target="_blank" href="<?php echo base_url('frontdesk/reservation/pdf_reservation/'. ($reservation_id > 0 ? $row->reservation_id : '0') .'.tpd');?>" class="inverse text-right" style="margin-top:10px;">Registration Form <i class="fa fa-print"></i></a>
                                </li>
                                <li>
                                    <a target="_blank" href="<?php echo base_url('frontdesk/reservation/pdf_reservation_family/'. ($reservation_id > 0 ? $row->reservation_id : '0') .'.tpd');?>" class="inverse text-right" style="margin-top:10px;">Family Registration<i class="fa fa-print"></i></a>
                                </li>
                                <li>
                                    <a target="_blank" href="<?php echo base_url('frontdesk/reservation/pdf_reservation_staff/'. ($reservation_id > 0 ? $row->reservation_id : '0') .'.tpd');?>" class="inverse text-right" style="margin-top:10px;">Staff Registration<i class="fa fa-print"></i></a>
                                </li>
                                <li>
                                    <a target="_blank" href="<?php echo base_url('frontdesk/management/pdf_folio_reference/'. ($reservation_id > 0 ? $row->reservation_id : '0') .'.tpd');?>" class="inverse text-right" style="margin-top:10px;">Room Reference <i class="fa fa-print"></i></a>
                                </li>

                            </ul>
                        </div>
                    </li>
                </ul>

            </div>
        </div>
		
		<div class="row clearfix" style="padding-top: 0px; margin-top: 15px;padding-bottom: 0px!important;margin-bottom: 0px!important;">
			<div class="col-md-8">
                <div class="col-md-9">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-speech"></i>
                                <span class="caption-subject bold uppercase"> Note</span>
                            </div>
                            <div class="actions">
                                <a class="btn btn-circle btn-default" href="javascript:;" id="btn_note_edit">
                                    <i class="fa fa-pencil" ></i> Edit </a>
                                <a class="btn btn-circle red-flamingo hide" href="javascript:;" id="btn_note_save">
                                    <i class="fa fa-save " ></i> Save </a>
                                <a href="#" class="btn btn-circle btn-icon-only btn-default fullscreen" data-original-title="" title="">
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body" style="height: auto;">
                            <textarea class="form-control"  readonly rows="3" name="remark" style="resize: vertical; "><?php echo ($reservation_id > 0 ? $row->remark : '');?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <h3>Wifi</h3>
                    <ul class="list-unstyled">
                        <li>
                            <strong>User :</strong>&nbsp; <span id="wifi_user"><?php echo(isset($wifi) ? $wifi->user_id : '') ?></span>
                        </li>
                        <li>
                            <strong>Password :</strong>&nbsp; <span id="wifi_pass"><?php echo(isset($wifi) ? $wifi->password : '') ?></span>
                        </li>
                        <li>
                            <strong>Expired on :</strong>&nbsp; <span id="wifi_expiry"><?php echo(isset($wifi) ? dmy_from_db($wifi->expiry_date) : '') ?></span>
                        </li>
                        <?php if($row->status == ORDER_STATUS::CHECKIN){ ?>
                            <li>
                            <a href="javascript:;" class="btn btn-circle btn-xs red-intense" id="wifi_voucher_gen" data-original-title="" title="">Create Voucher</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
			</div>
            <div class="col-md-4">
                <h3>Agent</h3>
                <ul class="list-unstyled">
                    <li>
                        <strong>Name :</strong>&nbsp; <span id="agent_name"><?php echo(isset($agent) ? $agent->agent_name : '') ?></span>
                    </li>
                    <li>
                        <strong>PIC :</strong>&nbsp; <span id="wifi_pass"><?php echo(isset($agent) ? $agent->agent_pic : '') ?></span>
                    </li>

                </ul>
            </div>
		</div>
		<!-- BEGIN PAGE CONTENT-->
		<div class="row" style="margin-top: 0px;padding-top: 0px;">
			<div class="col-md-12">
                <div class="tabbable tabbable-custom">
                    <form action="#" method="post" id="form-entry" class="form-horizontal" >
                        <input type="hidden" id="reservation_id" name="reservation_id" value="<?php echo $reservation_id;?>" />
                        <input type="hidden" name="reservation_type" value="<?php echo ($reservation_id > 0 ? $row->reservation_type : '0');?>" />
                        <div class="tab-content">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="light bg-inverse" >
                                        <div class="tabbable tabbable-line" >
                                            <ul class="nav nav-tabs">
                                                <li class="active" >
                                                    <a href="#portlet_main" data-toggle="tab" >
                                                        <i class="fa fa-tag"></i>
                                                        Room Charges</a>
                                                </li>
                                                <li>
                                                    <a href="#portlet_extra" data-toggle="tab" >
                                                        <i class="fa fa-cutlery"></i>
                                                        Other Charges</a>
                                                </li>
                                                <li>
                                                    <a href="#portlet_payment" data-toggle="tab" >
                                                        <i class="fa fa-credit-card"></i>
                                                        Payments</a>
                                                </li>
                                                <li>
                                                    <a href="#portlet_adjust" data-toggle="tab" >
                                                        <i class="fa fa-exchange"></i>
                                                        Adjustments</a>
                                                </li>
                                                <li>
                                                    <a href="#portlet_deposit" data-toggle="tab" >
                                                        <i class="fa fa-suitcase "></i>
                                                        Deposit</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="portlet_main" style="">
                                                    <div class="row table-responsive">
                                                        <div class="col-md-12" style="padding-bottom: 50px;">
                                                            <!-- BEGIN TAB PORTLET-->
                                                            <table class="table table-striped table-hover table-bordered table-po-detail" id="table_room" name="table_room" style="padding-top: 0px;margin-top: 0px;">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center">Unit</th>
                                                                    <th>Type</th>
                                                                    <th class="text-center">From</th>
                                                                    <th class="text-center">To</th>
                                                                    <th class="text-right">Total Rate</th>
                                                                    <th class="text-right">Discount</th>
                                                                    <th class="text-right">Tax</th>
                                                                    <th class="text-right">Total</th>
                                                                    <th class="text-center" width="30px">Bill</th>
                                                                    <?php echo ($is_supervisor ? '<th width="2px"></th>' : '')?>

                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php

                                                                $totalRoomAmount = 0;
                                                                if($row->reservation_type == RES_TYPE::HOUSE_USE){
                                                                    $sql_bill = "SELECT cs_bill_detail.billdetail_id, cs_bill_detail.unit_id, cs_bill_detail.date_start as bill_startdate, cs_bill_detail.date_end as bill_enddate, cs_bill_detail.transtype_id, cs_bill_detail.amount as amount, cs_bill_detail.disc_amount as discount, cs_bill_detail.tax as tax, (cs_bill_detail.amount - cs_bill_detail.disc_amount + cs_bill_detail.tax) as total_amount, cs_bill_detail.is_billed, ms_unit.unit_code, ms_unit_type.unittype_desc FROM cs_bill_detail JOIN cs_bill_header ON cs_bill_header.bill_id = cs_bill_detail.bill_id JOIN ms_unit ON ms_unit.unit_id = cs_bill_detail.unit_id JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_unit.unittype_id WHERE cs_bill_header.reservation_id = " . $reservation_id . " AND cs_bill_header.is_other_charge <= 0 ORDER BY cs_bill_detail.unit_id, cs_bill_detail.date_start";
                                                                }else {
                                                                    if ($row->billing_type == BILLING_TYPE::FULL_PAID) {
                                                                        $sql_bill = "SELECT B.unit_code, C.unittype_desc, A.* FROM( SELECT MIN (billdetail_id) AS billdetail_id, MIN (unit_id) AS unit_id, MIN (transtype_id) AS transtype_id, MIN (bill_startdate) AS bill_startdate, MAX (bill_enddate) AS bill_enddate, SUM (amount) AS amount, SUM (discount) AS discount, SUM (tax) AS tax, SUM (total_amount) AS total_amount, MIN (is_billed) AS is_billed FROM cs_corporate_bill WHERE reservation_id = " . $reservation_id . ") A LEFT JOIN ms_unit B ON A.unit_id = B.unit_id LEFT JOIN ms_unit_type C ON B.unittype_id = c.unittype_id";
                                                                    } else {
                                                                        $sql_bill = "SELECT cs_corporate_bill.*, ms_unit.unit_code, ms_unit_type.unittype_desc FROM cs_corporate_bill JOIN ms_unit ON ms_unit.unit_id = cs_corporate_bill.unit_id JOIN ms_unit_type ON ms_unit_type.unittype_id = ms_unit.unittype_id WHERE reservation_id = " . $reservation_id . " AND is_othercharge <= 0 ORDER BY unit_id, bill_startdate";
                                                                    }
                                                                }
                                                                $rooms = $this->db->query($sql_bill);
                                                                //echo $this->db->last_query();
                                                                $has_btn_change = false;
                                                                foreach($rooms->result_array() as $room) {
                                                                    $btn_change = '';
                                                                    if ($is_supervisor) {
                                                                        if (!$has_btn_change) {
                                                                            if (ymd_from_db($room['bill_enddate']) > date('Y-m-d')
                                                                            ) {
                                                                                $btn_change = '<td><a data-original-title="Change Room" id="change_room" bill-detail-id="' . $room['billdetail_id'] . '" class="btn btn-circle btn-xs red-intense tooltips" href="javascript:;"><i class="fa fa-exchange "></i></a></td>';
                                                                                $has_btn_change = true;
                                                                            }
                                                                        } else {
                                                                            $btn_change = "<td>&nbsp;</td>";
                                                                        }
                                                                    }

                                                                    echo '<tr>
                                                                    <td class="text-center" ><input type="hidden" name="billdetail_id[]" value="' . $room['billdetail_id'] . '"><input type="hidden" name="transtype_id[]" value="' . $room['transtype_id'] . '"><input type="hidden" name="unit_id[]" value="' . $room['unit_id'] . '">' . $room['unit_code'] . '</td>
                                                                    <td>' . $room['unittype_desc'] . '</td>
                                                                    <td class="text-center"><input type="hidden" name="bill_start_date[]" value="' . ymd_from_db($room['bill_startdate']) . '">' . date('d/m/Y', strtotime(ymd_from_db($room['bill_startdate']))) . '</td>

                                                                    <td class="text-center"><input type="hidden" name="bill_end_date[]" value="' . ymd_from_db($room['bill_enddate']) . '">' . date('d/m/Y', strtotime(ymd_from_db($room['bill_enddate']))) . '</td>
                                                                    <td class="text-right"><input type="hidden" name="local_amount[]" value="' . $room['amount'] . '">' . format_num($room['amount'], 0) . '</td>
                                                                    <td class="text-right"><input type="hidden" name="discount_amount[]" value="' . $room['discount'] . '">' . format_num($room['discount'], 0) . '</td>
                                                                    <td class="text-right"><input type="hidden" name="tax_amount[]" value="' . $room['tax'] . '">' . format_num($room['tax'], 0) . '</td>
                                                                    <td class="text-right"><input type="hidden" name="subtotal_amount[]" value="' . $room['total_amount'] . '">' . format_num($room['total_amount'], 0) . '</td>
                                                                    <td class="text-center"><i class="' . ($room['is_billed'] > 0 ? 'fa fa-check font-green' : '') . '"></i></td>';

                                                                    echo $btn_change;

                                                                    echo '</tr>';
                                                                    $totalRoomAmount += $room['total_amount'];
                                                                }

                                                                /*$totalRoomAmount = 0;
                                                                if(isset($unit_rates)){
                                                                    $count = count($unit_rates);

                                                                    foreach($unit_rates as $room){
                                                                        $btn_change = '';
                                                                        if($is_supervisor){
                                                                            if($room['status'] == STATUS_NEW) {
                                                                                $btn_change = '<td><a data-original-title="Change Room" id="change_room" bill-detail-id="' . $room['billdetail_id'] . '" class="btn btn-circle btn-xs red-intense tooltips" href="javascript:;"><i class="fa fa-exchange "></i></a></td>';
                                                                            }
                                                                        }

                                                                        echo '<tr>
                                                                        <td class="text-center" ><input type="hidden" name="billdetail_id[]" value="' . $room['billdetail_id'] . '"><input type="hidden" name="transtype_id[]" value="'. $room['transtype_id'] . '"><input type="hidden" name="unit_id[]" value="'. $room['unit_id'] . '">' . $room['unit_code']. '</td>
                                                                        <td><input type="hidden" name="is_monthly[]" value="' . $room['is_monthly'] . '"><input type="hidden" name="unit_duration[]" value="'. $room['duration'] . '">' . $room['unittype_desc']. '</td>
                                                                        <td class="text-center"><input type="hidden" name="bill_start_date[]" value="' . $room['bill_start'] . '">' . date('d/m/y', strtotime(dmy_to_ymd($room['bill_start']))) . '</td>

                                                                        <td class="text-center"><input type="hidden" name="bill_end_date[]" value="' . $room['bill_end'] . '">' . date('d/m/y', strtotime(dmy_to_ymd($room['bill_end']))) . '</td>
                                                                        <td class="text-center">' . $room['duration'] . '</td>
                                                                        <td class="text-right"><input type="hidden" name="local_amount[]" value="'. $room['local_amount'] . '">' . format_num($room['local_amount'],0) . '</td>
                                                                        <td class="text-right"><input type="hidden" name="discount_amount[]" value="'. $room['discount_amount'] . '">' . format_num($room['discount_amount'],0) . '</td>
                                                                        <td class="text-right"><input type="hidden" name="tax_amount[]" value="'. $room['tax_amount'] . '">' . format_num($room['tax_amount'],0) . '</td>
                                                                        <td class="text-right"><input type="hidden" name="subtotal_amount[]" value="'. $room['subtotal'] . '">' . format_num($room['subtotal'],0) . '</td>';
                                                                        echo $btn_change;

                                                                        echo  '</tr>';
                                                                        $totalRoomAmount += $room['subtotal'];
                                                                    }
                                                                }*/
                                                                ?>
                                                                </tbody>
                                                                <tfoot>
                                                                <th colspan="7" class="text-center">Total Room Charges</th>
                                                                <th class="text-right"><?php echo format_num($totalRoomAmount,0); ?></th>
                                                                <?php echo $is_supervisor ? '<th colspan="2"></th>' : '<th></th>' ;?>
                                                                </tfoot>
                                                            </table>
                                                            <!-- END TAB PORTLET-->

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="portlet_extra" >
                                                    <div class="row ">
                                                        <div class="col-md-11">
                                                            <!-- BEGIN TAB PORTLET-->
                                                            <table class="table table-striped table-hover table-bordered table-po-detail" id="table_extra" name="table_extra" style="padding-top: 0px;margin-top: 0px;">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center" style="width: 10%;">Doc No</th>
                                                                    <th class="text-center" style="width: 8%;">Unit</th>
                                                                    <th class="text-center">Item</th>
                                                                    <th class="text-right" style="width: 6%;">Qty</th>
                                                                    <th class="text-right" style="width: 10%;">Price</th>
                                                                    <th class="text-right" style="width: 12%;">Amount</th>
                                                                    <th class="text-right" style="width: 10%;">Tax</th>
                                                                    <th class="text-right" style="width: 12%;">Subtotal</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                //MAIN
                                                                $totalOtherCharge = 0;
                                                                $totalPrimary = 0;
                                                                $totalOther = 0;
                                                                    $otherCharges = $this->db->query('SELECT cs_bill_detail.*, view_pos_item.item_code, view_pos_item.item_desc, ms_unit.unit_code, cs_bill_header.journal_no, cs_bill_header.company_id, pos_master_item.masteritem_title, pos_master_item.masteritem_code
                                                                                               FROM cs_bill_detail
                                                                                               JOIN cs_bill_header ON cs_bill_detail.bill_id = cs_bill_header.bill_id
                                                                                               LEFT JOIN view_pos_item ON view_pos_item.item_id = cs_bill_detail.item_id
                                                                                               LEFT JOIN pos_master_item ON pos_master_item.masteritem_id = cs_bill_detail.masteritem_id
                                                                                               LEFT JOIN ms_unit ON ms_unit.unit_id = cs_bill_detail.unit_id
                                                                                               WHERE cs_bill_header.is_other_charge > 0 AND (cs_bill_detail.item_id > 0 OR cs_bill_detail.masteritem_id > 0) AND cs_bill_header.reservation_id = ' . $reservation_id . ' AND cs_bill_header.status IN(' . STATUS_POSTED . ',' . STATUS_CLOSED .')
                                                                                               ORDER BY cs_bill_header.company_id DESC ');
                                                                    if($otherCharges->num_rows() > 0){
                                                                        foreach($otherCharges->result_array() as $oc){
                                                                            $desc = $oc['item_code'] . ' - ' . $oc['item_desc'];
                                                                            if($oc['masteritem_id'] > 0){
                                                                                $desc = $oc['masteritem_code'] . ' - ' . $oc['masteritem_title'];
                                                                            }
                                                                            if($row->reservation_type == RES_TYPE::CORPORATE){
                                                                                if($oc['company_id'] > 0){
                                                                                    echo '<tr>
                                                                            <td class="text-center">' . $oc['journal_no'] . '</td>
                                                                            <td class="text-center" ><input type="hidden" name="billdetail_id[]" value="' . $oc['billdetail_id'] . '"><input type="hidden" name="transtype_id[]" value="'. $oc['transtype_id'] . '">' . $oc['unit_code']. '</td>
                                                                            <td><input type="hidden" name="is_monthly[]" value="' . $oc['is_monthly'] . '">' . $desc . '</td>
                                                                            <td class="text-right">' . format_num($oc['item_qty'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['rate'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['amount'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['tax'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['amount']-$oc['disc_amount']+$oc['tax'],0) . '</td>
                                                                                    </tr>';
                                                                                    $totalPrimary += ($oc['amount']-$oc['disc_amount']+$oc['tax']);
                                                                                }
                                                                            }else{
                                                                                echo '<tr>
                                                                            <td class="text-center">' . $oc['journal_no'] . '</td>
                                                                            <td class="text-center" ><input type="hidden" name="billdetail_id[]" value="' . $oc['billdetail_id'] . '"><input type="hidden" name="transtype_id[]" value="'. $oc['transtype_id'] . '">' . $oc['unit_code']. '</td>
                                                                            <td><input type="hidden" name="is_monthly[]" value="' . $oc['is_monthly'] . '">' . $desc . '</td>
                                                                            <td class="text-right">' . format_num($oc['item_qty'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['rate'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['amount'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['tax'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['amount']-$oc['disc_amount']+$oc['tax'],0) . '</td>
                                                                                    </tr>';
                                                                                $totalPrimary += ($oc['amount']-$oc['disc_amount']+$oc['tax']);
                                                                            }
                                                                        }
                                                                    }

                                                                if($row->reservation_type == RES_TYPE::CORPORATE){
                                                                    echo '<tr style="background-color:#E3F2FD;">
                                                                        <td colspan="7" class="text-right">Total Company Charges</td>
                                                                        <td class="text-right ">' . format_num($totalPrimary,0) . '</td>
                                                                      </tr>';
                                                                }

                                                                //SECONDARY
                                                                if($row->reservation_type == RES_TYPE::CORPORATE){
                                                                    if($otherCharges->num_rows() > 0){
                                                                        foreach($otherCharges->result_array() as $oc){
                                                                            $desc = $oc['item_code'] . ' - ' . $oc['item_desc'];
                                                                            if($oc['masteritem_id'] > 0){
                                                                                $desc = $oc['masteritem_code'] . ' - ' . $oc['masteritem_title'];
                                                                            }

                                                                            if($oc['company_id'] <= 0){
                                                                                echo '<tr>
                                                                            <td class="text-center">' . $oc['journal_no'] . '</td>
                                                                            <td class="text-center" ><input type="hidden" name="billdetail_id[]" value="' . $oc['billdetail_id'] . '"><input type="hidden" name="transtype_id[]" value="'. $oc['transtype_id'] . '">' . $oc['unit_code']. '</td>
                                                                            <td><input type="hidden" name="is_monthly[]" value="' . $oc['is_monthly'] . '">' . $desc . '</td>
                                                                            <td class="text-right">' . format_num($oc['item_qty'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['rate'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['amount'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['tax'],0) . '</td>
                                                                            <td class="text-right">' . format_num($oc['amount']-$oc['disc_amount']+$oc['tax'],0) . '</td>
                                                                                    </tr>';
                                                                                $totalOther += ($oc['amount']-$oc['disc_amount']+$oc['tax']);
                                                                            }
                                                                        }
                                                                    }

                                                                    echo '<tr style="background-color:#E3F2FD;">
                                                                        <td colspan="7" class="text-right">Total Guest Charges</td>
                                                                        <td class="text-right">' . format_num($totalOther,0) . '</td>
                                                                      </tr>';
                                                                }

                                                                $totalOtherCharge = ($totalPrimary + $totalOther);
                                                                ?>
                                                                </tbody>
                                                                <tfoot>
                                                                <th colspan="7" class="text-center">Total Other Charges</th>
                                                                <th class="text-right"><?php echo format_num($totalOtherCharge,0); ?></th>
                                                                </tfoot>
                                                            </table>
                                                            <!-- END TAB PORTLET-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="portlet_payment" > <!-- style="height: 200px;overflow-y: scroll;overflow-x:hidden;" -->
                                                    <div class="row">
                                                        <div class="col-md-11">
                                                        <div id="pnl_payment_list">
                                                            <table class="table table-striped table-hover table-bordered table-po-detail" id="table_payment" name="table_payment" style="padding-top: 0px;margin-top: 0px;">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center" style="width: 12%;">Date</th>
                                                                    <th class="text-center" style="width: 12%;">Doc No</th>
                                                                    <th class="text-left" >Type</th>
                                                                    <th class="text-center" style="width: 20%;">Amount</th>
                                                                </tr>

                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                $totalPayment = 0;
                                                                $paymentMain = 0;
                                                                $paymentOther = 0;
                                                                if(isset($payments)){
                                                                    foreach($payments as $payment){
                                                                        $doc_link = $payment['doc_no'];
                                                                        if($payment['doc_link'] != ''){
                                                                            $doc_link = '<a href="' . $payment['doc_link'] .'" target="_blank">' . $payment['doc_no'] . '</a>';
                                                                        }

                                                                        if($row->reservation_type == RES_TYPE::CORPORATE){
                                                                            if($payment['is_primary_debtor'] > 0){
                                                                                echo '<tr>
                                                                            <td class="text-center" style="vertical-align:middle;">' . dmy_from_db($payment['bookingreceipt_date']) .'</td>
                                                                            <td class="text-center" style="vertical-align:middle;">' . $doc_link .'</td>
                                                                            <td class="text-left" style="vertical-align:middle;"><input type="hidden" name="bookingreceipt_id[]" value="' . $payment['bookingreceipt_id'] . '"><input type="hidden" name="unique_id[]" value=""><input type="hidden" name="paymenttype_id[]" value="' . $payment['paymenttype_id'] . '">' . $payment['paymenttype_code'] . '</td>
                                                                            <td class="text-right">' . amount_journal($payment['amount']) .'</td>
                                                                          </tr>';
                                                                                $paymentMain += $payment['amount'];
                                                                            }
                                                                        }else{
                                                                            echo '<tr>
                                                                            <td class="text-center" style="vertical-align:middle;">' . dmy_from_db($payment['bookingreceipt_date']) .'</td>
                                                                            <td class="text-center" style="vertical-align:middle;">' . $doc_link .'</td>
                                                                            <td class="text-left" style="vertical-align:middle;"><input type="hidden" name="bookingreceipt_id[]" value="' . $payment['bookingreceipt_id'] . '"><input type="hidden" name="unique_id[]" value=""><input type="hidden" name="paymenttype_id[]" value="' . $payment['paymenttype_id'] . '">' . $payment['paymenttype_code'] . '</td>
                                                                            <td class="text-right">' . amount_journal($payment['amount']) .'</td>
                                                                          </tr>';
                                                                            $paymentMain += $payment['amount'];
                                                                        }
                                                                    }

                                                                    if($row->reservation_type == RES_TYPE::CORPORATE){
                                                                        echo '<tr style="background-color:#E3F2FD;">
                                                                        <td colspan="3" class="text-right">Total Company Payment</td>
                                                                        <td class="text-right ">' . amount_journal($paymentMain) . '</td>
                                                                      </tr>';
                                                                    }

                                                                    //SECONDARY
                                                                    if($row->reservation_type == RES_TYPE::CORPORATE){
                                                                        foreach($payments as $payment){
                                                                            if($payment['is_primary_debtor'] <= 0){
                                                                                echo '<tr>
                                                                            <td class="text-center" style="vertical-align:middle;">' . dmy_from_db($payment['bookingreceipt_date']) .'</td>
                                                                            <td class="text-center" style="vertical-align:middle;">' . $doc_link .'</td>
                                                                            <td class="text-left" style="vertical-align:middle;"><input type="hidden" name="bookingreceipt_id[]" value="' . $payment['bookingreceipt_id'] . '"><input type="hidden" name="unique_id[]" value=""><input type="hidden" name="paymenttype_id[]" value="' . $payment['paymenttype_id'] . '">' . $payment['paymenttype_code'] . '</td>
                                                                            <td class="text-right">' . amount_journal($payment['amount']) .'</td>
                                                                          </tr>';
                                                                                $paymentOther += $payment['amount'];
                                                                            }
                                                                        }

                                                                        echo '<tr style="background-color:#E3F2FD;">
                                                                        <td colspan="3" class="text-right">Total Guest Payment</td>
                                                                        <td class="text-right">' . amount_journal($paymentOther) . '</td>
                                                                      </tr>';
                                                                    }

                                                                    $totalPayment = ($paymentMain + $paymentOther);
                                                                }

                                                                ?>
                                                                </tbody>
                                                                <tfoot>
                                                                    <th colspan="3" class="text-center">Total Payment</th>
                                                                    <th class="text-right" ><?php echo amount_journal($totalPayment); ?></th>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="portlet_adjust">
                                                    <div class="row">
                                                        <div class="col-md-11">
                                                            <table class="table table-striped table-hover table-bordered table-po-detail" id="table_payment" name="table_payment" style="padding-top: 0px;margin-top: 0px;">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center" style="width: 12%;">Date</th>
                                                                    <th class="text-center" style="width: 12%;">Type</th>
                                                                    <th class="text-left" >Description</th>
                                                                    <th class="text-right" style="width: 20%;">Amount</th>
                                                                </tr>

                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                $totalAdjustment = 0;
                                                                $totalAdjustMain = 0;
                                                                $totalAdjustOther = 0;
                                                                if(isset($adjustments)){
                                                                    foreach($adjustments as $adj){
                                                                        if($row->reservation_type == RES_TYPE::CORPORATE){
                                                                            if($adj['is_primary_debtor'] > 0){
                                                                                echo '<tr>
                                                                                <td class="text-center" style="vertical-align:middle;">' . dmy_from_db($adj['adjust_date']) .'</td>
                                                                                <td class="text-center" style="vertical-align:middle;">' . $adj['adjust_type'] .'</td>
                                                                                <td class="text-left" style="vertical-align:middle;">' . $adj['adjust_remark'] . '</td>
                                                                                <td class="text-right">' . amount_journal($adj['adjust_amount']) .'</td>
                                                                              </tr>';

                                                                                $totalAdjustMain += $adj['adjust_amount'];
                                                                            }
                                                                        }else{
                                                                            echo '<tr>
                                                                            <td class="text-center" style="vertical-align:middle;">' . dmy_from_db($adj['adjust_date']) .'</td>
                                                                            <td class="text-center" style="vertical-align:middle;">' . $adj['adjust_type'] .'</td>
                                                                            <td class="text-left" style="vertical-align:middle;">' . $adj['adjust_remark'] . '</td>
                                                                            <td class="text-right">' . amount_journal($adj['adjust_amount']) .'</td>
                                                                          </tr>';
                                                                            $totalAdjustMain += $adj['adjust_amount'];
                                                                        }
                                                                    }
                                                                }

                                                                if($row->reservation_type == RES_TYPE::CORPORATE){
                                                                    echo '<tr style="background-color:#E3F2FD;">
                                                                        <td colspan="3" class="text-right">Total Company Adjustment</td>
                                                                        <td class="text-right ">' . amount_journal($totalAdjustMain) . '</td>
                                                                      </tr>';
                                                                }

                                                                //SECONDARY
                                                                if($row->reservation_type == RES_TYPE::CORPORATE){
                                                                    foreach($adjustments as $adj){
                                                                        if($adj['is_primary_debtor'] <= 0){
                                                                            echo '<tr>
                                                                                <td class="text-center" style="vertical-align:middle;">' . dmy_from_db($adj['adjust_date']) .'</td>
                                                                                <td class="text-center" style="vertical-align:middle;">' . $adj['adjust_type'] .'</td>
                                                                                <td class="text-left" style="vertical-align:middle;">' . $adj['adjust_remark'] . '</td>
                                                                                <td class="text-right">' . amount_journal($adj['adjust_amount']) .'</td>
                                                                              </tr>';

                                                                            $totalAdjustOther += $adj['adjust_amount'];
                                                                        }
                                                                    }

                                                                    echo '<tr style="background-color:#E3F2FD;">
                                                                        <td colspan="3" class="text-right">Total Guest Adjustment</td>
                                                                        <td class="text-right">' . amount_journal($totalAdjustOther) . '</td>
                                                                      </tr>';
                                                                }

                                                                $totalAdjustment = ($totalAdjustMain + $totalAdjustOther);

                                                                ?>
                                                                </tbody>
                                                                <tfoot>
                                                                <th colspan="3" class="text-center">Total Adjustment</th>
                                                                <th class="text-right"><?php echo amount_journal($totalAdjustment); ?></th>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="portlet_deposit" > <!-- style="height: 200px;overflow-y: scroll;overflow-x:hidden;" -->
                                                    <div class="row">
                                                        <div class="col-md-11">
                                                        <div id="pnl_payment_list">
                                                            <table class="table table-striped table-hover table-bordered table-po-detail" id="table_payment" name="table_payment" style="padding-top: 0px;margin-top: 0px;">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center" style="width: 12%;">Date</th>
                                                                    <th class="text-center" style="width: 12%;">Doc No</th>
                                                                    <th class="text-left" >Description</th>
                                                                    <th class="text-center" style="width: 20%;">Amount</th>
                                                                </tr>

                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                $totalDeposit = 0;
                                                                $totalAllocation = 0;
                                                                if(isset($deposits)){
                                                                    foreach($deposits as $deposit){
                                                                        $depositAmount = round($deposit['debit'] - $deposit['credit'],0);
                                                                        $docLink = '<a href="' . base_url('ar/report/pdf_official_receipt/' . $deposit['doc_no'] . '.tpd') .'" target="_blank">' . $deposit['doc_no'] . '</a>';
                                                                        echo '<tr>
                                                                            <td class="text-center" style="vertical-align:middle;">' . dmy_from_db($deposit['trx_date']) .'</td>
                                                                            <td class="text-center" style="vertical-align:middle;">' . $docLink .'</td>
                                                                            <td class="text-left" style="vertical-align:middle;">' . $deposit['trx_desc'] . '</td>
                                                                            <td class="text-right">' . amount_journal($depositAmount) .'</td>
                                                                          </tr>';

                                                                        if($deposit['type'] == 2){
                                                                            $totalAllocation += $depositAmount;
                                                                        }

                                                                        $totalDeposit += $depositAmount;
                                                                    }
                                                                }

                                                                $pending_amount = ($totalRoomAmount+$totalOtherCharge+$totalPayment+$totalAdjustment+$totalAllocation);

                                                                ?>
                                                                </tbody>
                                                                <tfoot>
                                                                    <th colspan="3" class="text-center">Total Deposit</th>
                                                                    <th class="text-right" ><?php echo amount_journal($totalDeposit); ?></th>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="portlet bordered ">
                                        <div class="portlet-title">
                                        </div>
                                        <div class="portlet-body ">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 text-right">
                                                    <label class="control-label "><strong>Total Room Charges</strong> </label>
                                                </div>
                                                <div class="col-md-5 col-sm-5">
                                                    <input type="hidden" name="total_room_charge" value="<?php echo $totalRoomAmount; ?>">
                                                    <span class="form-control text-right"><?php echo amount_journal($totalRoomAmount); ?></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 text-right">
                                                    <label class="control-label "><strong>Total Other Charges</strong> </label>
                                                </div>
                                                <div class="col-md-5 col-sm-5">
                                                    <input type="hidden" name="total_other_charge" value="<?php echo $totalOtherCharge; ?>" >
                                                    <span class="form-control text-right"><?php echo amount_journal($totalOtherCharge); ?></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 text-right">
                                                    <label class="control-label "><strong>Total Adjustment</strong> </label>
                                                </div>
                                                <div class="col-md-5 col-sm-5">
                                                    <input type="hidden" name="total_adjustment" value="<?php echo $totalAdjustment; ?>" >
                                                    <span class="form-control text-right <?php echo ($totalAdjustment < 0 ? 'font-red-sunglo' : '')?>" <?php echo ($totalAdjustment < 0 ? 'style="padding-right:11px;"' : '') ?>><?php echo amount_journal($totalAdjustment); ?></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 text-right">
                                                    <label class="control-label "><strong>Total Payment</strong> </label>
                                                </div>
                                                <div class="col-md-5 col-sm-5">
                                                    <input type="hidden" name="total_payment"  value="<?php echo $totalPayment; ?>" >
                                                    <span class="form-control text-right <?php echo ($totalPayment != 0 ? 'font-red-sunglo' : '')?>" <?php echo ($totalPayment < 0 ? 'style="padding-right:11px;"' : '') ?>><?php echo amount_journal($totalPayment); ?></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 text-right">
                                                    <label class="control-label "><strong>Deposit Allocation</strong> </label>
                                                </div>
                                                <div class="col-md-5 col-sm-5">
                                                    <input type="hidden" name="total_deposit_allocation"  value="<?php echo $totalAllocation; ?>" >
                                                    <span class="form-control text-right <?php echo ($totalAllocation != 0 ? 'font-red-sunglo' : '')?>" <?php echo ($totalAllocation < 0 ? 'style="padding-right:11px;"' : '') ?>><?php echo amount_journal($totalAllocation); ?></span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 text-right">
                                                    <label class="control-label "><strong>Pending Amount</strong> </label>
                                                </div>
                                                <div class="col-md-5 col-sm-5">
                                                    <input type="hidden" name="total_pending" value="<?php echo $pending_amount; ?>" >
                                                    <span class="form-control text-right bold <?php echo ($pending_amount >= 0 ? 'font-blue-chambray' : 'font-red-sunglo')?>" <?php echo ($pending_amount < 0 ? 'style="padding-right:11px;"' : '') ?>><?php echo amount_journal($pending_amount); ?></span>
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
		<!-- END PAGE CONTENT-->

        <!-- FOOTER -->
        <div class="row" >
            <div class="col-md-12" style="padding-bottom: 0px;margin-bottom:0px;">
                <?php
                if($reservation_id > 0){
                    $created = '';
                    $modified = '';

                    if ($row->created_by > 0) {
                        $created .= "<div class='col-md-8'><h6>Created by " . get_user_fullname( $row->created_by) . " (" . date_format(new DateTime($row->created_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                    }

                    if ($row->modified_by > 0) {
                        $modified .= "<div class='col-md-4'><h6>Last Modified by " . get_user_fullname( $row->modified_by) . " (" . date_format(new DateTime($row->modified_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                    }
                    echo '<div class="note note-info" style="margin:10px;">
                                                    ' . $created . '
                                                    ' . $modified . '
                                                    <div style="clear:both;"></div>
                                                </div>';
                }
                ?>
            </div>
        </div>
        <div class="row hide" >
            <div class="col-md-12" style="padding-top: 0px;margin-top:0px;">
                <?php
                if($reservation_id > 0){
                    $log = '';
                    $modified = '';
                    $qry_log = $this->mdl_general->get('app_log', array('feature_id' => Feature::FEATURE_CS_RESERVATION, 'reff_id' => $reservation_id), array(), 'log_id asc');
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

                    if ($row->modified_by > 0) {
                        $modified .= "<div class='col-md-4'><h6>Last Modified on " . date_format(new DateTime($row->modified_date), 'd/m/Y H:i:s') . " by " . get_user_fullname( $row->modified_by ) . "</h6></div>" ;
                    }

                    //$modified = '';
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

        </div >
        <!-- END FOOTER -->
	</div>
</div>
<!-- END CONTENT -->
<input type="hidden" name="change_bill_detail_id" value="" id="change_bill_detail_id">
<div id="ajax-add-srf" data-width="560" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>
<div id="ajax-change-room" data-width="350" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>
<div id="ajax-receipt" class="modal fade"  data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog" style="width: 720px;">
        <div class="modal-content col-md-12 note note-success" style="padding: 0px;">
            <input type="hidden" name="pending_amount_primary" value="<?php echo(isset($pending_amount_primary) ? $pending_amount_primary : 0); ?>">
            <input type="hidden" name="pending_amount_other" value="<?php echo(isset($pending_amount_other) ? $pending_amount_other : 0); ?>">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title bold">Official Receipt :</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <?php if($row->reservation_type == RES_TYPE::CORPORATE) { ?>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4">&nbsp;</label>
                                <div class="radio-list" >
                                    <label class="radio-inline" >
                                        <input type="radio" name="debtor_type" id="debtor_type_corporate" value="1" checked> Corporate Bill</label>
                                    <label class="radio-inline" >
                                        <input type="radio" name="debtor_type" id="debtor_type_personal" value="0" > Personal Bill</label>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Receipt Date</label>
                                <div class="input-group col-md-3 col-sm-3 date " data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control" name="receipt_date" value="<?php echo (date('d-m-Y'));?>" readonly>
                                    <span class="input-group-btn hide">
                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="is_frontdesk" value="0">
                                <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Payment Type</label>
                                <select name="paymenttype_id" class="select2me form-control input-medium">
                                    <?php
                                    $payments = $this->db->query('select * from ms_payment_type where status = '.STATUS_NEW . ' AND payment_type <> ' . PAYMENT_TYPE::PAYMENT_GATEWAY . ' order by pos ');
                                    foreach($payments->result_array() as $payType){
                                        if($payType['payment_type'] == PAYMENT_TYPE::AR_TRANSFER){
                                            echo '<option value="'. $payType['paymenttype_id'] .'" payment-type="' . $payType['payment_type'] . '" id="paymenttype_ar" class="hide">' . $payType['paymenttype_code'] . ' - ' . $payType['paymenttype_desc'] . '</option>';
                                        }else{
                                            echo '<option value="'. $payType['paymenttype_id'] .'" payment-type="' . $payType['payment_type'] . '">' . $payType['paymenttype_code'] . ' - ' . $payType['paymenttype_desc'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div id="card_info">
                                <div class="form-group" >
                                    <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Name</label>
                                    <input type="text" name="creditcard_name" value="" class="form-control input-medium">
                                </div>
                                <div class="form-group" >
                                    <label class="control-label col-md-4 col-sm-4">Card No</label>
                                    <input type="text" name="creditcard_no" value="" class="form-control input-medium mask_credit_card">
                                </div>
                                <div class="form-group hide" id="card_info_expiry">
                                    <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Expiry</label>
                                    <div class="row">
                                        <div class="col-md-2 col-sm-2">
                                            <div class="row">
                                                <select name="creditcard_expiry_month" class="select2me form-control">
                                                    <?php
                                                    for($i=1;$i<=12;$i++){
                                                        echo '<option value="'. $i .'">' . (strlen($i) == 1 ? '0'. $i : $i) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-3">
                                            <select name="creditcard_expiry_year" class="select2me form-control">
                                                <?php
                                                $current = date('Y');
                                                for($i=$current;$i<=$current+10;$i++){
                                                    echo '<option value="'. $i .'">' . (strlen($i) == 1 ? '0'. $i : $i) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group hide" id="bank_account_info">
                                <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Bank Account</label>
                                <select name="bankaccount_id" class="select2me form-control input-medium">
                                    <?php
                                    $banks = $this->db->query('select * from fn_bank_account where status = '.STATUS_NEW . ' and iscash <= 0 order by bank_id, bankaccount_code');
                                    foreach($banks->result_array() as $bank){
                                        echo '<option value="'. $bank['bankaccount_id'] .'">' . $bank['bankaccount_desc'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Amount</label>
                                <input type="hidden" name="min_receipt_amount" id="min_receipt_amount" value="0" class="mask_currency">
                                <input type="text" name="receipt_amount" value="0" class="form-control text-right mask_currency input-small" readonly>
                            </div>
                            <div class="form-group hide" id="id_bank_fee">
                                <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Bank Charge</label>
                                <input type="text" name="receipt_bank_fee" value="0" class="form-control text-right mask_currency input-small" readonly>
                            </div>
                            <div class="form-group hide" id="id_veritrans_fee">
                                <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Veritrans</label>
                                <input type="text" name="receipt_veritrans_fee" value="0" class="form-control text-right mask_currency input-small" readonly>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="min_amount" value="0">
                                <table class="table table-striped table-bordered table-hover table-po-detail " id="table_pending_detail">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th class="text-center" width="25%">
                                            Inv No
                                        </th>
                                        <th class="text-center" width="15%">
                                            Date
                                        </th>
                                        <!--th class="text-center" width="15%">
                                            Due Date
                                        </th -->
                                        <th class="text-right " width="30%">
                                            Pending
                                        </th>
                                        <th class="text-center" width="10%" style="width:5%;padding-left:7px;">

                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $details = $this->db->get_where("fxnARInvoiceHeaderByStatus(" . STATUS_POSTED . ")", array('reservation_id' => $reservation_id));
                                    if(isset($details)){
                                        foreach($details->result_array() as $bill){
                                            $display = '<tr id="parent_' . $bill['inv_id'] . '' . '">
                                                     <td style="vertical-align:middle;" class="text-center" >
                                                        <input type="hidden" name="invoice_id[]" value="' . $bill['inv_id'] . '">
                                                        <span class="text-center">' . $bill['inv_no'] . '</span>
                                                     </td>
                                                     <td style="vertical-align:middle;" class="control-label">
                                                        <span class="text-center">' . dmy_from_db($bill['inv_date']) . '</span>
                                                     </td>
                                                     <td style="vertical-align:middle;" class="control-label" >
                                                        <input type="text" name="pending_amount[]" value="' . $bill['unpaid_grand'] . '" class="form-control text-right mask_currency input-sm" readonly>
                                                     </td>
                                                     <td style="vertical-align:middle;padding-top:8px;padding-left:7px;">
                                                        <input type="checkbox" name="inv_id[]" value="' . $bill['inv_id'] . '" checked class="chk_inv_id">
                                                    </td>';
                                            $display .= '</tr>';
                                            echo $display;
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                    <div class="col-md-5 ">
                        <div class="form-group">
                            <label class="control-label col-md-4 col-sm-4" style="padding-top: 6px;">Description</label>
                            <textarea class="form-control" rows="3" name="bookingreceipt_desc"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn grey-cascade" data-dismiss="modal" id="cancel-receipt">Close</button>
                <button type="button" class="btn green-seagreen bold" id="submit-receipt">Submit</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<!-- BEGIN fast view of a product -->
<div id="member-img-popup" style="display: none; width: 320px;">
    <img src="<?php echo base_url('assets/img/no_image_available.png');?>" alt="Photo" class="img-responsive" id="img_member_photo">
</div>
<!-- END fast view of a product -->

<script>
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;

    var handleMask = function() {
        $(".mask_currency").inputmask("numeric",{
            radixPoint:".",
            autoGroup: true,
            groupSeparator: ",",
            digits: 2,
            groupSize: 3,
            removeMaskOnSubmit: true,
            autoUnmask: true
        });
        $(".mask_credit_card").inputmask("mask", {"mask": "9999-9999-9999-9999"});
    }

    $(document).ready(function(){
        handleMask();

        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "right",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal

        };

        //handleMask();

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        $('select[name="paymenttype_id"]').on('change', function (e) {
            var element = $(this).find('option:selected');
            var ptype = element.attr("payment-type");

            defaultFormReceipt(ptype);
        });

        $('input[name="debtor_type"]').on('click', function (e) {
            var is_primary = $(this).val();
            //console.log('debtor_type ' + is_primary);
            defaultAmount(is_primary);
        });

        function defaultAmount(is_primary){
            var element = $('select[name="paymenttype_id"]').find('option:selected');
            var ptype = element.attr("payment-type");
            defaultFormReceipt(ptype);

            if(is_primary > 0){
                var defaultAmount = parseFloat($('input[name="pending_amount_primary"]').val()) || 0;
                $('input[name="receipt_amount"]').val(defaultAmount);
                if(defaultAmount <= 0){
                    $('#submit-receipt').addClass('disabled');
                }else{
                    $('#submit-receipt').removeClass('disabled');
                }
            }else{
                var defaultAmount = parseFloat($('input[name="pending_amount_other"]').val()) || 0;
                $('input[name="receipt_amount"]').val(defaultAmount);
                if(defaultAmount <= 0){
                    $('#submit-receipt').addClass('disabled');
                }else{
                    $('#submit-receipt').removeClass('disabled');
                }
            }
        }

        $('#btn-receipt').live('click', function(e){
            e.preventDefault();

            $('#min_receipt_amount').val(6000);
            $('input[name="receipt_amount"]').val($(this).attr('pay-amount'));
            $('input[name="receipt_amount"]').removeAttr('readonly');
            $('input[name="is_frontdesk"]').val($(this).attr('is-frontdesk'));
            if($(this).attr('is-frontdesk') <= 0){
                $('select[name="paymenttype_id"]').attr('disabled','disabled');
            }else{
                $('select[name="paymenttype_id"]').removeAttr('disabled');
            }

            var reservation_type = $('input[name="reservation_type"]').val();
            if(reservation_type == '<?php echo(RES_TYPE::CORPORATE)?>'){
                var is_primary = $('input:radio[name="debtor_type"]:checked').val();
                defaultAmount(is_primary);
            }

            //Set default hide or show Card Info
            var element = $('select[name="paymenttype_id"]').find('option:selected');
            var ptype = element.attr("payment-type");
            defaultFormReceipt(ptype);

            var $modal = $('#ajax-receipt');

            handleMask();

            $modal.modal();
        });

        $('.chk_inv_id').live('click', function(){
            var isChecked = $(this).is(':checked');
            var tr = $(this).closest('tr');
            var paymentAmount = parseFloat($('input[name="receipt_amount"]').val()) || 0;

            if(isChecked){
                var pendingAmount = parseFloat(tr.find('input[name*="pending_amount"]').val()) || 0;
                var amount = (paymentAmount + pendingAmount);
                $('input[name="receipt_amount"]').val(amount);
                $('#min_receipt_amount').val(amount);
            }else{
                var pendingAmount = parseFloat(tr.find('input[name*="pending_amount"]').val()) || 0;
                var amount = (paymentAmount - pendingAmount);
                if(amount < 0){
                     amount = 0;
                }
                $('input[name="receipt_amount"]').val(amount);
                $('#min_receipt_amount').val(amount);
            }
        });

        $('#submit-receipt').live('click', function(e){
            e.preventDefault();

            var min_receipt = parseFloat($('#min_receipt_amount').val()) || 0;

            var reservation_id = $('input[name="reservation_id"]').val();
            var receipt_date = $('input[name="receipt_date"]').val();
            var paymenttype_id = $('select[name="paymenttype_id"]').val();

            var is_credit_card = 0;
            var element = $('select[name="paymenttype_id"]').find('option:selected');
            var ptype = element.attr("payment-type");
            if(ptype == '<?php echo (PAYMENT_TYPE::CREDIT_CARD); ?>'){
                is_credit_card = 1;
            }

            var bankaccount_id = $('select[name="bankaccount_id"]').val();
            var receipt_amount = parseFloat($('input[name="receipt_amount"]').inputmask('unmaskedvalue')) || 0;
            var bank_fee = $('input[name="receipt_bank_fee"]').inputmask('unmaskedvalue');
            var veritrans_fee = $('input[name="receipt_veritrans_fee"]').inputmask('unmaskedvalue');
            var desc = $('textarea[name="bookingreceipt_desc"]').val();
            var ccard_name = $('input[name="creditcard_name"]').val();
            var ccard_no = $('input[name="creditcard_no"]').val();
            var expiry_month = parseInt($('select[name="creditcard_expiry_month"]').val()) || 0;
            var expiry_year = parseInt($('select[name="creditcard_expiry_year"]').val()) || 0;

            var valid = true;
            if(is_credit_card > 0){
                if(ccard_name == '' || ccard_no == '' || expiry_month <= 0 || expiry_year <= 0){
                    valid = false;
                    toastr["error"]("Credit Card Information must be filled.", "Warning");
                }
            }

            if(receipt_amount <= 0){
                valid = false;
                toastr["error"]("Receipt amount must not 0.", "Warning");
            }

            var is_primary_debtor = 1;
            var reservation_type = $('input[name="reservation_type"]').val();
            if(reservation_type == '<?php echo(RES_TYPE::CORPORATE)?>'){
                var debtor_type = $('input:radio[name="debtor_type"]:checked').val();
                //console.log('debtor_type ' + debtor_type);
                if(debtor_type <= 0){
                    is_primary_debtor = 0;
                }
            }

            var inv_ids = [];
            var inv_amounts = [];

            $("#table_pending_detail input[type=checkbox]").each(function () {
                //$(this).attr("checked", "checked");
                var tr = $(this).closest("tr");
                if($(this).is(":checked")){
                    inv_ids.push($(this).val());
                    inv_amounts.push(tr.find('input[name*="pending_amount"]').val());
                }
            });

            if(valid){
                if(receipt_amount >= min_receipt){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('frontdesk/management/submit_booking_receipt');?>",
                        dataType: "json",
                        data: { reservation_id: reservation_id, receipt_date: receipt_date, paymenttype_id:paymenttype_id,
                                bankaccount_id:bankaccount_id, receipt_amount:receipt_amount,bank_fee:bank_fee, veritrans_fee:veritrans_fee,
                                desc:desc, creditcard_name : ccard_name, creditcard_no : ccard_no,
                                creditcard_expiry_month : expiry_month, creditcard_expiry_year : expiry_year,
                                request_type: 'guest', is_primary_debtor : is_primary_debtor, is_invoice : 1,
                                inv_ids : inv_ids, inv_amounts : inv_amounts
                              },
                        async:false
                    })
                        .done(function( msg ) {
                            Metronic.unblockUI();
                            if(msg.type == '1'){
                                toastr["success"](msg.message, "Complete");
                                if(msg.redirect_link != ''){
                                    window.location.href = msg.redirect_link;
                                }
                            }
                            else {
                                toastr["error"]("Action can not be processed, please try again later.", "Error");
                            }

                            resetReceiptForm();
                        });

                    $('#ajax-receipt').modal('hide');
                }else{
                    var maskedMinReceipt = parseFloat(min_receipt).formatMoney(0, '.', ',');
                    toastr["error"]("Receipt amount must not less than " + maskedMinReceipt, "Warning");
                }
            }
        });

        $('#cancel-receipt').live('click', function(e){
            resetReceiptForm();
        });

        function resetReceiptForm(){
            $('select[name="paymenttype_id"]').removeAttr('disabled');
            //$('select[name="paymenttype_id"]').parent().children('.select2-container').children('.select2-choice').children('.select2-chosen').html('CCBC - Credit Card');
            //$('select[name="paymenttype_id"]').val($('input[name="cc_paymenttype_id"]').val());
            //$('select[name="bankaccount_id"] option').eq(1).prop('selected', true);

            //Set default hide or show Card Info
            var element = $('select[name="paymenttype_id"]').find('option:selected');
            var ptype = element.attr("payment-type");
            defaultFormReceipt(ptype);

            //$('#min_receipt_amount').val('0');
            $('input[name="receipt_amount"]').val('0');
            $('input[name="credit_card_name"]').val('');
            $('input[name="credit_card_no"]').val('');

            $('textarea[name="bookingreceipt_desc"]').val('');
        };

        $('#wifi_voucher_gen').live('click', function(){
            var reservation_id = parseInt($('input[name="reservation_id"]').val()) || 0;
            if(reservation_id > 0){
                $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('frontdesk/management/xwifi_voucher_gen');?>",
                        dataType: "json",
                        data: { reservation_id: reservation_id}
                    })
                    .done(function(msg) {
                        if(msg.type == '1'){
                            if (typeof msg.wifi_user != 'undefined' && msg.wifi_user != null) {
                                $('#wifi_user').html(msg.wifi_user);
                            }

                            if (typeof msg.wifi_pass != 'undefined' && msg.wifi_pass != null) {
                                $('#wifi_pass').html(msg.wifi_pass);
                            }

                            if (typeof msg.wifi_expiry != 'undefined' && msg.wifi_expiry != null) {
                                $('#wifi_expiry').html(msg.wifi_expiry);
                            }
                        }
                        else if(msg.type == '0') {
                            toastr["error"](msg.message, "Error");
                        }
                        else {
                            toastr["error"]("Action can not be processed, please try again later.", "Error");
                        }
                    });
            }
        });

        $('#btn-checkout').live('click', function(){
            var reservation_id = $('input[name="reservation_id"]').val();

            bootbox.confirm({
                message: "Check Out ?",
                buttons: {
                    cancel: {
                        label: "No",
                        className: "btn-inverse"
                    },
                    confirm:{
                        label: "Yes",
                        className: "btn-primary"
                    }
                },
                callback: function(result) {
                    if(result === null){
                        return;
                    }else{
                        if(result){
                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('frontdesk/management/submit_checkout');?>",
                                dataType: "json",
                                data: { reservation_id: reservation_id},
                                async:false
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();
                                    if(msg.type == '1'){
                                        if(msg.redirect_link != ''){
                                            window.location.assign(msg.redirect_link);
                                        }
                                    }
                                    else if(msg.type == '0') {
                                        if(msg.redirect_link != ''){
                                            //window.location.assign(msg.redirect_link);
                                            toastr["error"](msg.message, "Warning");
                                        }
                                    }
                                    else {
                                        toastr["error"]("Action can not be processed, please try again later.", "Error");
                                    }
                                });
                        }
                    }
                }
            });
        });

        /*EDITOR NOTE*/
        $('#btn_note_edit').on('click', function(){
            $('textarea[name="remark"]').prop('readonly',false);

            $('#btn_note_edit').addClass('hide');
            $('#btn_note_save').removeClass('hide');
        });

        $('#btn_note_save').on('click', function(){
            var reservation_id = $('input[name="reservation_id"]').val();
            var remark = $('textarea[name="remark"]').val();

            //Metronic.blockUI($('textarea[name="remark"]'));
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('frontdesk/management/xsave_reservation_remark');?>",
                dataType: "json",
                data: { reservation_id: reservation_id, remark : remark},
                async:false
            })
                .done(function( msg ) {
                    //Metronic.unblockUI($('textarea[name="remark"]'));
                    if(msg.valid == '1'){
                        $('textarea[name="remark"]').prop('readonly',true);

                        $('#btn_note_edit').removeClass('hide');
                        $('#btn_note_save').addClass('hide');

                        toastr["success"]("Note saved", "Information");
                    }
                    else {
                        toastr["error"](msg.message, "Warning");
                    }
                });


        });

        // Change Room
        var grid_room = new Datatable();
        var handleTableRoom = function (num_index, reservation_id) {
            // Start Datatable Item
            grid_room.init({
                src: $("#datatable_room"),
                onSuccess: function (grid_room) {
                    // execute some code after table records loaded
                },
                onError: function (grid_room) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_room) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "sWidth" : '15%', "bSortable": false },
                        { "bSortable": false},
                        /*{ "sClass": "text-center", "sWidth" : '9%', "bSortable": false },
                        { "sClass": "text-center", "sWidth" : '9%', "bSortable": false },
                        { "sClass": "text-center", "sWidth" : '8%', "bSortable": false },
                        { "sClass": "text-right", "sWidth" : '10%', "bSortable": false },
                        { "sClass": "text-right", "sWidth" : '9%', "bSortable": false },
                        { "sClass": "text-right", "sWidth" : '10%', "bSortable": false },
                        { "sClass": "text-right", "sWidth" : '12%', "bSortable": false },*/
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '3%' }
                    ],
                    "aaSorting": [],
                    "bInfo":false,
                    "paging":   false,
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": -1, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('frontdesk/reservation/get_change_room');?>/" + num_index + '/' + reservation_id // ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_company_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item

        }

        $('#change_room').on('click', function(e){
            e.preventDefault();

            var $modal = $('#ajax-change-room');
            var num_index = parseInt($(this).attr('data-index')) || 0;
            var reservation_id = $('input[name="reservation_id"]').val();
            var change_bill_detail_id = parseFloat($(this).attr('bill-detail-id')) || 0;
            $('#change_bill_detail_id').val(change_bill_detail_id);

            //console.log('looking up ...');
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('frontdesk/reservation/ajax_modal_room');?>.tpd', '', function () {
                    $modal.modal();
                    handleTableRoom(num_index, reservation_id);

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
        });

        $('.btn-select-room').live('click', function (e) {
            e.preventDefault();

            var reservation_id = $('input[name="reservation_id"]').val();
            var reff_bill_detail = $('#change_bill_detail_id').val();

            var unit_id = parseInt($(this).attr('data-unit-id')) || 0;
            var unit_code = $(this).attr('data-unit-code');
            var arrival = $(this).attr('data-arrival');
            //var departure = $(this).attr('data-departure');
            //var num_of_days = parseInt($(this).attr('data-days')) || 0;
            //var tax_rate = parseFloat($(this).attr('data-tax-rate')) || 0;
            //var disc_amount = parseFloat($(this).attr('data-discount-amount')) || 0;
            //var local_amount = parseFloat($(this).attr('data-local-amount')) || 0;

            $('#ajax-change-room').modal('hide');

            bootbox.confirm({
                message: "Change to room " + unit_code + " ?",
                buttons: {
                    cancel: {
                        label: "No",
                        className: "btn-inverse"
                    },
                    confirm:{
                        label: "Yes",
                        className: "btn-primary"
                    }
                },
                callback: function(result) {
                    if(result === null){

                    }else{
                        if(result){
                            $.ajax({
                                 type: "POST",
                                 url: "<?php echo base_url('frontdesk/management/xchange_checkin_room');?>",
                                 dataType: "json",
                                 data: { reservation_id: reservation_id, reff_bill_detail : reff_bill_detail, unit_id : unit_id, change_date : arrival}
                             })
                             .done(function( msg ) {
                                 //Metronic.unblockUI();
                                 if(msg.valid == '1'){
                                     toastr["success"](msg.message, "Info");
                                     if(msg.redirect_link != ''){
                                         //var url = "<?php echo base_url('housekeeping/housekeeping/home.tpd');?>" ;
                                         window.location.assign(msg.redirect_link);
                                     }
                                 }else if(msg.valid == '0'){
                                    toastr["error"](msg.message, "Warning");
                                 }
                                 else {
                                    toastr["error"]("Action can not be processed, please try again later.", "Error");
                                 }
                             });

                        }
                    }
                }
            });

        });

        var handleFancybox = function () {
            if (!jQuery.fancybox) {
                return;
            }

            jQuery(".fancybox-fast-view").fancybox();

            if (jQuery(".fancybox-button").size() > 0) {
                jQuery(".fancybox-button").fancybox({
                    groupAttr: 'data-rel',
                    prevEffect: 'none',
                    nextEffect: 'none',
                    closeBtn: true,
                    scrolling   : 'hidden',
                    helpers: {
                        title: {
                            type: 'inside'
                        },
                        overlay: {
                          locked: true
                        }
                    },
                    beforeShow: function(){
                        $("body").css({'overflow-y':'hidden'});
                    },
                    afterClose: function(){
                        $("body").css({'overflow-y':'visible'});
                    }
                });
            }
        }

        handleFancybox();

        $('.btn-member-photo').live('click', function(){
            d = new Date();
            var url = $(this).attr('img-src') + "?" + d.getTime();
            console.log(url);
            $('#img_member_photo').attr('src',url);
            //$("#Image").attr("src", "dummy.jpg");
            //$("#img_member_photo").attr("src", $('#img_member_photo').val()+"&"+Math.floor(Math.random()*1000));

            $.fancybox('#member-img-popup');
        });


        $('.btn-add-srf').live('click', function(e){
            e.preventDefault();

            var $modal = $('#ajax-add-srf');
            var reservation_id = $('input[name="reservation_id"]').val();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('frontdesk/management/ajax_modal_add_srf');?>/' + reservation_id, '', function () {
                    $modal.modal();

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
        });

        $('#form-entry-srf').live('submit', function(){
            var unit_id = parseInt($('#form-entry-srf input[name="unit_id"]').val()) || 0;
            var request_by = $('#form-entry-srf input[name="requested_by"]').val().trim();
            var note = $('#form-entry-srf textarea[name="srf_note"]').val().trim();
            var valid = true;

            $('#form-entry-srf .form-group').removeClass('has-error');
            toastr.clear();

            if (unit_id <= 0) {
                toastr["warning"]("Unit ID is empty.", "Warning");
                valid = false;
            }
            if (request_by == '') {
                toastr["warning"]("Please input Requested By.", "Warning");
                $('#form-entry-srf input[name="requested_by"]').closest('.form-group').addClass('has-error');
                valid = false;
            }
            if (note == '') {
                toastr["warning"]("Please input Description.", "Warning");
                $('#form-entry-srf textarea[name="srf_note"]').closest('.form-group').addClass('has-error');
                valid = false;
            }

            if (valid) {
                var dataForm = $('#form-entry-srf').serializeArray();

                $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('frontdesk/management/ajax_modal_add_srf_submit');?>",
                        dataType: "json",
                        data: dataForm
                    })
                    .done(function (msg) {
                        if (msg.err == '0') {
                            toastr["success"](msg.message, "Success");

                            $('#ajax-add-srf').modal('hide');
                        } else if (msg.err == '1') {
                            toastr["error"](msg.message, "Warning");
                        } else {
                            toastr["error"]("Action can not be processed, please try again later.", "Error");
                        }
                    })
                    .fail(function () {
                        toastr["error"]("Action can not be processed, please try again later.", "Error");
                    });
            }
        });

    });

    function defaultFormReceipt(ptype){
        var reservation_type = $('input[name="reservation_type"]').val();
        if(reservation_type == '<?php echo(RES_TYPE::CORPORATE)?>'){
            var debtor_type = $('input:radio[name="debtor_type"]:checked').val();
            //console.log('debtor_type ' + debtor_type);
            if(debtor_type > 0){
                $('#paymenttype_ar').removeClass('hide');
            }else{
                $('#paymenttype_ar').addClass('hide');
            }
        }else{
            $('#paymenttype_ar').addClass('hide');
        }

        if(ptype == '<?php echo PAYMENT_TYPE::CREDIT_CARD; ?>'){
            $('#card_info').removeClass('hide');
            $('#card_info_expiry').removeClass('hide');
            $('#bank_account_info').addClass('hide');

            //$(".mask_credit_card").inputmask("mask", {"mask": "9999-9999-9999-9999"});
        }else if(ptype == '<?php echo PAYMENT_TYPE::BANK_TRANSFER; ?>'){
            $('#bank_account_info').removeClass('hide');
            $('#card_info').addClass('hide');
            $('#card_info_expiry').addClass('hide');
        }else if(ptype == '<?php echo PAYMENT_TYPE::DEBIT_CARD; ?>'){
            $('#bank_account_info').addClass('hide');
            $('#card_info').removeClass('hide');
            $('#card_info_expiry').addClass('hide');

            //$(".mask_credit_card").inputmask();
        }else{
            $('#card_info').addClass('hide');
            $('#card_info_expiry').addClass('hide');
            $('#bank_account_info').addClass('hide');
        }
    }

</script>