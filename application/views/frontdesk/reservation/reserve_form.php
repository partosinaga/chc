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
                $is_frontdesk = 1;
                $enable_edit_rate = isset($enable_edit_rate) ? $enable_edit_rate : false;
                $enable_discount = isset($enable_discount) ? $enable_discount : true;
                if($reservation_id > 0){
                    if($row->status == ORDER_STATUS::ONLINE_NEW || $row->status == ORDER_STATUS::ONLINE_VALID ||
                        $row->status == ORDER_STATUS::RESERVED ){
                        $form_mode = '';
                    }else{
                        $form_mode = 'disabled';
                    }
                    $is_frontdesk = $row->is_frontdesk;
                }else{
                    $enable_edit_rate = true;
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
							<i class="fa fa-star"></i><?php echo ($reservation_id > 0 ? 'Reservation Form - ' . $row->reservation_code : 'Reservation Form');?>
						</div>
						<div class="actions">
                            <a href="<?php echo (isset($back_url) ? $back_url : base_url('frontdesk/reservation/reservation_manage/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="javascript:;" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="reservation_id" name="reservation_id" value="<?php echo $reservation_id;?>" />
                            <input type="hidden" name="reservation_date" value="<?php echo $reservation_id > 0 ? dmy_from_db($row->reservation_date) : '';?>" />
                            <input type="hidden" name="reservation_code" value="<?php echo $reservation_id > 0 ? $row->reservation_code : '';?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-4">
                                        <button type="button" class="btn purple-plum btn-circle" name="save">SAVE</button>
                                        <button type="button" class="btn purple-plum btn-circle hide" name="save_close" >SAVE & CLOSE</button>
                                        <?php
                                        if($reservation_id > 0){
                                            if($row->status == ORDER_STATUS::RESERVED && date('Y-m-d') >= ymd_from_db($row->arrival_date)){
                                                if(($row->reservation_type == RES_TYPE::PERSONAL ) ||
                                                    $row->reservation_type == RES_TYPE::CORPORATE || $row->reservation_type == RES_TYPE::HOUSE_USE){
                                                    ?>
                                                    <button class="btn blue-chambray btn-circle" name="btn_check_in" id="btn_check_in" data-id="<?php echo $reservation_id; ?>">CHECK IN</button>
                                                <?php
                                                }
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
                                <div class="row group-billing-type <?php echo ($reservation_id > 0 ? ($row->reservation_type == RES_TYPE::HOUSE_USE ? 'hide' : '') : '')?>">
                                    <label class="control-label col-md-1" style="text-align: left;padding-left:30px;"><strong>BILLING</strong></label>
                                    <div class="col-md-2" >
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label class="tooltips" data-original-title="Billing method Full Payment | Monthly Billing"><input type="checkbox" name="billing_type" value="<?php echo BILLING_TYPE::FULL_PAID; ?>" class="form-control "  <?php echo ($reservation_id > 0 ? ($row->billing_type == BILLING_TYPE::FULL_PAID ? 'checked' : '') : ($reservation_type == RES_TYPE::PERSONAL ? 'checked' : ''))?>>
                                                     FULL PAYMENT</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2" >
                                        <div class="input-group" id="group-is-yearly-rate">
                                                <div class="icheck-inline">
                                                    <label class="tooltips" data-original-title="Room charges Yearly | Monthly rate"><input type="checkbox" name="is_rate_yearly" value="1" class="form-control "  <?php echo ($reservation_id > 0 ? ($row->is_rate_yearly > 0 ? 'checked' : '') : ($num_month > 11 ? 'checked' : ''))?>>
                                                         YEARLY RATE </label>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="row group-room-detail">
                                    <div class="col-md-12">
                                        <div class="row" style="padding-top:15px;">
                                            <div class="col-md-12 table-responsive">
                                                <div class="table-container">
                                                <div class="col-md-12">
                                                    <table class="table table-striped table-hover table-bordered" id="table_room" name="table_room">
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center " width="7%">Room</th>
                                                            <th >Type</th>
                                                            <th class="text-center " width="8%">Check In</th>
                                                            <th class="text-center " width="8%">Check Out</th>
                                                            <th class="text-center " width="7%">Duration</th>
                                                            <th class="text-right " width="11%">Total Rate</th>
                                                            <th class="text-center " width="10%">Discount</th>
                                                            <th class="text-right " width="10%">Tax</th>
                                                            <th class="text-center " width="11%">Subtotal</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $totalCharge = 0;
                                                        $max_adult = 0;
                                                        $max_child = 0;
                                                        if(isset($room_unit)){
                                                            $count = count($room_unit);
                                                            foreach($room_unit as $room){
                                                                $discount = (isset($room['discount']) ? $room['discount'] : 0);
                                                                $tax = ($room['local_amount'] - $discount) * $room['tax_rate'];
                                                                $subtotal = round((($room['local_amount'] - $discount) + $tax),0);

                                                                echo '<tr>
                                                                        <td class="text-center" style="padding-top:12px;">
                                                                        <input type="hidden" name="unit_id[]" value="'. $room['unit_id'] . '">
                                                                        <input type="hidden" name="unit_code[]" value="'. $room['unit_code'] . '">' . $room['unit_code']. '</td>
                                                                        <td style="padding-top:12px;">' . $room['unittype_desc']. '</td>
                                                                        <td class="text-center" style="padding-top:12px;"><input type="hidden" name="arrival_date[]" value="'. $room['checkin_date'] . '">' . $room['checkin_date'] . '</td>
                                                                        <td class="text-center" style="padding-top:12px;"><input type="hidden" name="departure_date[]" value="'. $room['checkout_date'] . '">' . $room['checkout_date'] . '</td>
                                                                        <td class="text-center" style="padding-top:12px;"><input type="hidden" name="days[]" value="'. $room['daily_count'] . '">
                                                                        <input type="hidden" name="num_month[]" value="'. $num_month . '">' . $room['period_caption'] . '</td>
                                                                        <td class="text-right" style="padding-top:12px;"><input type="hidden" name="local_amount[]" value="'. $room['local_amount'] . '">' . format_num($room['local_amount'],0) . '</td>
                                                                        <td class="text-right" ><input type="text" class="form-control input-sm text-right mask_currency" name="discount_amount[]" value="'. $discount . '" ' . ($enable_edit_rate && $enable_discount ? '' : 'readonly') . '></td>
                                                                        <td class="text-right" >
                                                                        <input type="hidden" name="tax_rate[]" value="'. $room['tax_rate'] . '">
                                                                        <input type="text" class="form-control input-sm text-right mask_currency" name="tax_amount[]" value="'. $tax . '" readonly></td>
                                                                        <td class="text-right" ><input type="text" class="form-control input-sm text-right mask_currency" name="subtotal_amount[]" value="'. $subtotal . '" readonly></td>';

                                                                echo  '</tr>';
                                                                $max_adult += $room['max_adult'];
                                                                $max_child += $room['max_child'];
                                                                $totalCharge += $subtotal;
                                                            }
                                                        }

                                                        ?>
                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <?php if($reservation_id > 0) {
                                                                if($row->reservation_type != RES_TYPE::HOUSE_USE && check_controller_action('frontdesk','reservation', STATUS_PROCESS) && $row->status == ORDER_STATUS::RESERVED) {?>
                                                            <td style="padding-top: 12px;" class="text-center"><a id="btn_change_date" class="btn btn-xs btn-circle red-thunderbird tooltips " href="javascript:;" data-original-title="Change Check-In Date" title="">
                                    <i class="icon-calendar"></i></a>
                                                            </td>
                                                            <?php }else { ?>
                                                                <td>&nbsp;</td>
                                                            <?php }
                                                            }else{ ?>
                                                            <td>&nbsp;</td>
                                                            <?php } ?>
                                                            <td class="text-left ">
                                                                <div class="icheck-inline">
                                                                    <label class="tooltips font-red-sunglo" data-original-title="Check In date must be updated before checkin"><input type="checkbox" name="guest_type" value="1" class="form-control "  <?php echo ($reservation_id > 0 ? ($row->guest_type > 0 ? 'checked' : '') : '')?>>Tentative date (unconfirmed checkin date)</label>
                                                                </div>
                                                            </td>
                                                            <td class="text-right " colspan="6" style="padding-top: 12px;">Total Room Charge</td>
                                                            <td class="text-right font-green-seagreen"><input type="text" class="form-control input-sm text-right mask_currency font-green-seagreen" name="grand_total_amount" value="<?php echo $totalCharge ?>" readonly></td>
                                                        </tr>
                                                        <?php
                                                        if($reservation_id > 0)
                                                        {
                                                            if($row->payment_amount > 0){
                                                                echo '<tr>
                                                                        <td class="text-right " colspan="8">Official Receipt</td>
                                                                        <td class="text-right font-red-sunglo" style="padding-right:15px;">' . amount_journal($row->payment_amount * -1) . '</td>
                                                                      </tr>';
                                                            }
                                                        }
                                                        ?>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row" style="padding-top: 10px;">
                                    <div class="col-md-12">
                                        <!-- BEGIN TAB PORTLET-->
                                        <div class="box blue-ebonyclay" >
                                            <div class="portlet-title blue-ebonyclay" >
                                                <ul class="nav nav-tabs">
                                                    <li class="active" >
                                                        <a href="#portlet_main" data-toggle="tab" >
                                                            <i class="fa fa-tag"></i>
                                                            Main</a>
                                                    </li>
                                                    <li>
                                                        <a href="#portlet_agent" data-toggle="tab" >
                                                            <i class="fa fa-user"></i>
                                                            Agent</a>
                                                    </li>
                                                    <li>
                                                        <a href="#portlet_contact" data-toggle="tab" >
                                                            <i class="fa fa-phone"></i>
                                                            Guest</a>
                                                    </li>
                                                    <li>
                                                        <a href="#portlet_note" data-toggle="tab" >
                                                            <i class="fa fa-comment"></i>
                                                            Note</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="portlet-body ">
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="portlet_main" style="min-height:70px;">
                                                        <div class="row">
                                                            <div class="col-md-3" id="div_reservation_type">
                                                                <label class="control-label col-md-5"><strong>FOLIO TYPE</strong></label>
                                                                <div class="col-md-7" >
                                                                    <select name="reservation_type" class="form-control select2me" >
                                                                        <?php if (isset($reservation_type)) {
                                                                            if($reservation_type == RES_TYPE::HOUSE_USE){ ?>
                                                                                <option value="<?php echo RES_TYPE::HOUSE_USE?>" <?php echo (isset($reservation_type) ? $reservation_type == RES_TYPE::HOUSE_USE ? 'selected' : '' : ''); ?>>HOUSE USE</option>
                                                                        <?php  }else{ ?>
                                                                                <option value="<?php echo RES_TYPE::PERSONAL?>" <?php echo (isset($reservation_type) ? $reservation_type == RES_TYPE::PERSONAL ? 'selected' : '' : 'selected'); ?>>PERSONAL</option>
                                                                                <?php if($is_frontdesk > 0){ ?>
                                                                                    <option value="<?php echo RES_TYPE::CORPORATE?>" <?php echo (isset($reservation_type) ? $reservation_type == RES_TYPE::CORPORATE ? 'selected' : '' : ''); ?>>CORPORATE</option>
                                                                                <?php } ?>

                                                                        <?php    }
                                                                        }?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3 col-sm-3">Adult(s)</label>
                                                                    <div class="col-md-3 col-sm-3" >
                                                                        <select name="num_adult" class="form-control select2me">
                                                                            <?php
                                                                            if(isset($max_adult)){
                                                                                for($i=1;$i<=$max_adult;$i++){
                                                                                    echo '<option value="' . $i . '"' . ($reservation_id > 0 ? ($row->qty_adult == $i ? "selected" : "") : (isset($num_adult) ? $num_adult == $i ? "selected" : "": ($i==1 ? "selected" : ""))) . '>' . $i . '</option>';
                                                                                }
                                                                            }else{
                                                                                for($i=1;$i<=3;$i++){
                                                                                    echo '<option value="' . $i . '"' . ($reservation_id > 0 ? ($row->qty_adult == $i ? "selected" : "") : (isset($num_adult) ? $num_adult == $i ? "selected" : "": ($i==1 ? "selected" : ""))) . '>' . $i . '</option>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <label class="control-label col-md-2 col-sm-2">Child(s)</label>
                                                                    <div class="col-md-3 col-sm-3" >
                                                                        <select name="num_child" class="form-control select2me">
                                                                            <?php
                                                                            if(isset($max_child)){
                                                                                for($j=0;$j<=$max_child;$j++){
                                                                                    echo '<option value="' . $j . '"' . ($reservation_id > 0 ? ($row->qty_child == $j ? "selected" : "") : (isset($num_child) ? $num_child == $j ? "selected" : "": ($j==1 ? "selected" : ""))) . '>' . $j . '</option>';
                                                                                }
                                                                            }else{
                                                                                for($j=0;$j<=2;$j++){
                                                                                    echo '<option value="' . $j . '"' . ($reservation_id > 0 ? ($row->qty_child == $j ? "selected" : "") : (isset($num_child) ? $num_child == $j ? "selected" : "": ($j==1 ? "selected" : ""))) . '>' . $j . '</option>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row <?php echo (isset($reservation_type) ? $reservation_type == RES_TYPE::CORPORATE || $reservation_type == RES_TYPE::HOUSE_USE ? '' : 'hide' : 'hide') ?> " id="pnl_company">
                                                            <div class="col-md-6 " >
                                                                <div class="portlet box grey-gallery" >
                                                                    <div class="portlet-title">
                                                                        <input type="hidden" value="<?php echo isset($company) ? $company->company_id : 0; ?>" name="company_id" id="company_id">
                                                                        <div class="caption">
                                                                            Company Detail
                                                                        </div>
                                                                        <div class="actions">

                                                                        </div>
                                                                    </div>
                                                                    <div class="portlet-body ">
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2 col-sm-2">Name</label>
                                                                            <div class="col-md-9 col-sm-9" >
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control" name="company_name" value="<?php echo (isset($company) ? $company->company_name : '');?>" readonly/>
                                                                                <span class="input-group-btn">
                                                                                    <a id="btn_lookup_company" class="btn btn-success" href="javascript:;" <?php echo ($enable_edit_rate ? '' : 'disabled'); ?>>
                                                                                        <i class="fa fa-arrow-up fa-fw"></i>
                                                                                    </a>
                                                                                </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">Address</label>
                                                                            <div class="col-md-9">
                                                                                <textarea class="form-control" rows="2" name="company_address" style="resize: vertical;" readonly><?php echo (isset($company) ? $company->company_address : '');?></textarea>
                                                                            </div>

                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">Phone</label>
                                                                            <div class="col-md-9">
                                                                                <input type="text" class="form-control" name="company_phone" value="<?php echo (isset($company) ? $company->company_phone : '');?>" readonly/>
                                                                            </div>

                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">Fax</label>
                                                                            <div class="col-md-9">
                                                                                <input type="text" class="form-control" name="company_fax" value="<?php echo (isset($company) ? $company->company_fax : '');?>" readonly/>
                                                                            </div>

                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">e-Mail</label>
                                                                            <div class="col-md-9">
                                                                                <input type="text" class="form-control" name="company_email" value="<?php echo (isset($company) ? $company->company_email : '');?>" readonly/>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 " >
                                                                <div class="portlet box grey-gallery">
                                                                    <div class="portlet-title">
                                                                        <div class="caption">
                                                                            Contact Person
                                                                        </div>
                                                                        <div class="actions">

                                                                        </div>
                                                                    </div>
                                                                    <div class="portlet-body ">
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2 col-sm-2">Name</label>
                                                                            <div class="col-md-8 col-sm-8" >
                                                                                <input type="text" class="form-control" name="company_pic_name" value="<?php echo (isset($company) ? $company->company_pic_name : '');?>" readonly/>
                                                                            </div>

                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">Phone</label>
                                                                            <div class="col-md-8">
                                                                                <input type="text" class="form-control" name="company_pic_phone" value="<?php echo (isset($company) ? $company->company_pic_phone : '');?>" readonly/>
                                                                            </div>

                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">e-Mail</label>
                                                                            <div class="col-md-8">
                                                                                <input type="text" class="form-control" name="company_pic_email" value="<?php echo (isset($company) ? $company->company_pic_email : '');?>" readonly/>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="portlet_agent">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="portlet ">
                                                                    <div class="portlet-title">
                                                                        <input type="hidden" value="<?php echo isset($agent) ? $agent->agent_id : 0; ?>" name="agent_id" id="agent_id">
                                                                        <div class="caption">
                                                                            Agent Information
                                                                        </div>
                                                                        <div class="actions">
                                                                        </div>
                                                                    </div>
                                                                    <div class="portlet-body ">
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3 col-sm-3">Agent Name</label>
                                                                            <div class="col-md-9 col-sm-9" >
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control" name="agent_name" value="<?php echo isset($agent) ? $agent->agent_name : '';?>" readonly/>
                                                                                <span class="input-group-btn">
                                                                                    <a id="btn_lookup_agent" class="btn btn-success" href="javascript:;" >
                                                                                        <i class="fa fa-arrow-up fa-fw"></i>
                                                                                    </a>
                                                                                     <a id="btn_reset_agent" class="btn red-sunglo" href="javascript:;" >
                                                                                         <i class="fa fa-times fa-fw"></i>
                                                                                     </a>
                                                                                </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3">PIC</label>
                                                                            <div class="col-md-9">
                                                                                <input type="text" class="form-control" name="agent_pic" value="<?php echo (isset($agent) ? $agent->agent_pic : '');?>" readonly/>

                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3">Phone</label>
                                                                            <div class="col-md-9">
                                                                                <textarea class="form-control" rows="2" name="agent_phone" style="resize: vertical;" readonly><?php echo (isset($agent) ? nl2br($agent->agent_phone) : '');?></textarea>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="portlet ">
                                                                    <div class="portlet-title">

                                                                    </div>
                                                                    <div class="portlet-body ">
                                                                        <div class="form-group"></div>
                                                                        <div class="form-group"></div>
                                                                        <div class="form-group"></div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">e-Mail</label>
                                                                            <div class="col-md-8">
                                                                                <textarea class="form-control" rows="2" name="agent_email" style="resize: vertical;" readonly><?php echo (isset($agent) ? nl2br($agent->agent_email) : '');?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="portlet_contact">
                                                        <!-- CURRENCY LIST -->
                                                        <div class="row">
                                                            <div class="col-md-6 ">
                                                                <!-- Begin: life time stats -->
                                                                <div class="portlet ">
                                                                    <div class="portlet-title">
                                                                        <input type="hidden" value="<?php echo ($reservation_id > 0 ? $row->tenant_id : 0); ?>" name="tenant_id" id="tenant_id">
                                                                        <div class="caption">
                                                                            <input type="checkbox" name="chk_registered_tenant" value="1" <?php echo ($reservation_id > 0 ? $row->tenant_id > 0 ? 'checked' : '' : '');?> <?php echo (($reservation_id > 0 ? $row->status == ORDER_STATUS::RESERVED && $row->payment_amount > 0 ? 'disabled' : '' : '')) ?> ><span for="chk_registered_tenant">Already Registered</span>
                                                                            &nbsp;&nbsp;&nbsp;
                                                                            <input type="checkbox" name="hidden_me" value="1" <?php echo ($reservation_id > 0 ? $row->hidden_me > 0 ? 'checked' : '' : '');?> <?php echo (($reservation_id > 0 ? $row->status == ORDER_STATUS::CHECKIN ? 'disabled' : '' : '')) ?> ><span for="hidden_me" class="font-red-sunglo">Hide Guest after Check In</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="portlet-body ">
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2 col-sm-2">Name</label>
                                                                            <div class="col-md-2 col-sm-2" style="padding-right:0px;">
                                                                                <select name="tenant_salutation" class="form-control select2me">
                                                                                    <option value="Mr" <?php echo (isset($tenant->tenant_salutation) ? $tenant->tenant_salutation == 'Mr' ? 'selected' : '' : '') ;?>>Mr</option>
                                                                                    <option value="Mrs"  <?php echo (isset($tenant->tenant_salutation) ? $tenant->tenant_salutation == 'Mrs' ? 'selected' : '' : '') ;?>>Mrs</option>
                                                                                    <option value="Ms"  <?php echo (isset($tenant->tenant_salutation) ? $tenant->tenant_salutation == 'Ms' ? 'selected' : '' : '') ;?>>Ms</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-8 col-sm-8" >
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control" name="tenant_name" value="<?php echo ($reservation_id > 0 ? $tenant->tenant_fullname : '');?>" <?php echo ($reservation_id > 0 ? $tenant->tenant_id > 0 ? 'readonly' : '' : '');?>/>
                                                                                <span class="input-group-btn">
                                                                                    <a id="btn_lookup_tenant" class="btn btn-success tooltips" href="javascript:;" <?php echo ($reservation_id > 0 ? (($row->tenant_id > 0 && $row->payment_amount <= 0) || $row->status == ORDER_STATUS::ONLINE_VALID ? '' : 'disabled') : 'disabled'); ?> data-original-title="Pick Guest">
                                                                                        <i class="fa fa-arrow-up fa-fw"></i>
                                                                                    </a>
                                                                                    <?php if($reservation_id > 0 && $row->tenant_id > 0){ ?>
                                                                                    <a id="btn_edit_guest" class="btn grey tooltips" href="<?php echo base_url('frontdesk/setup/tenant_manage/0/' . $row->tenant_id . '/' . $reservation_id); ?>" data-original-title="Edit">
                                                                                        <i class="fa fa-pencil fa-fw"></i>
                                                                                    </a>
                                                                                    <?php } ?>
                                                                                </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">Mobile</label>
                                                                            <div class="col-md-10">
                                                                                <input type="text" class="form-control" name="tenant_cellular" value="<?php echo ($reservation_id > 0 ? isset($tenant->tenant_cellular) ? $tenant->tenant_cellular : '' : '');?>" readonly/>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">Phone</label>
                                                                            <div class="col-md-10">
                                                                                <input type="text" class="form-control" name="tenant_phone" value="<?php echo ($reservation_id > 0 ? $tenant->tenant_phone : '');?>" readonly/>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">e-Mail</label>
                                                                            <div class="col-md-10">
                                                                                <input type="text" class="form-control" name="tenant_email" value="<?php echo ($reservation_id > 0 ? $tenant->tenant_email : '');?>" readonly/>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">Address</label>
                                                                            <div class="col-md-10">
                                                                                <textarea class="form-control" rows="2" name="tenant_address" style="resize: vertical;" readonly><?php echo ($reservation_id > 0 ? $tenant->tenant_address : '');?></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">City</label>
                                                                            <div class="col-md-5" >
                                                                                <input type="text" class="form-control" name="tenant_city" value="<?php echo ($reservation_id > 0 ? $tenant->tenant_city : '');?>" readonly/>
                                                                            </div>
                                                                            <label class="control-label col-md-3">Postal Code</label>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" name="tenant_postalcode" value="<?php echo ($reservation_id > 0 ? $tenant->tenant_postalcode : '');?>" readonly/>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">Country</label>
                                                                            <div class="col-md-8">
                                                                                <select name="tenant_country" class="form-control select2me" id="id_tenant_country" readonly>
                                                                                    <?php
                                                                                    $countries = $this->db->query('select * from master_country order by country_name');
                                                                                    foreach ($countries->result_array() as $country) {
                                                                                        ?>
                                                                                        <option value="<?php echo $country['country_name']; ?>" <?php echo ($reservation_id > 0 ? (isset($tenant->tenant_country) ? $tenant->tenant_country == $country['country_name'] ? 'selected' : '' : (strtoupper($country['country_name']) == 'INDONESIA' ? 'selected' : '')) : (strtoupper($country['country_name']) == 'INDONESIA' ? 'selected' : '')) ?>><?php echo $country['country_name']; ?></option>
                                                                                    <?php
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- End: life time stats -->
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="portlet">
                                                                    <div class="portlet-title">
                                                                        <div class="caption">

                                                                        </div>
                                                                    </div>
                                                                    <div class="portlet-body">
                                                                        <div class="form-group">
                                                                            <div class="col-md-12">
                                                                                <div class="portlet box grey-cascade" style="margin-bottom: 0px;">
                                                                                    <div class="portlet-title" style="min-height: 30px;">
                                                                                        <div class="caption" style="padding-top: 0px;padding-bottom: 0px;">
                                                                                            <div class="radio-list" >
                                                                                                <label class="radio-inline" >
                                                                                                    <input type="radio" name="id_type" id="idtype_ktp" value="1" <?php echo $reservation_id > 0 ? isset($tenant->id_type) ? ($tenant->id_type == 1 ? 'checked' : '') : 'checked' : 'checked' ?>> KTP </label>
                                                                                                <label class="radio-inline" >
                                                                                                    <input type="radio" name="id_type" id="idtype_passport" value="2" <?php echo $reservation_id > 0 ? isset($tenant->id_type) ? ($tenant->id_type == 2 ? 'checked' : '') : '' : '' ?>> PASSPORT </label>
                                                                                                <label class="radio-inline" >
                                                                                                    <input type="radio" name="id_type" id="idtype_kitas" value="3" <?php echo $reservation_id > 0 ? isset($tenant->id_type) ? ($tenant->id_type == 3 ? 'checked' : '') : '' : '' ?>> KITAS </label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="portlet-body" style="padding-bottom: 2px;">
                                                                                        <div class="form-group">
                                                                                            <label class="control-label col-md-3">No</label>
                                                                                            <div class="col-md-8">
                                                                                                <input type="text" class="form-control" name="passport_no" value="<?php echo ($reservation_id > 0 ? (isset($tenant->passport_no) ? $tenant->passport_no : '') : '');?>" />
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="form-group">

                                                                                            <label class="control-label col-md-3">Issued Place</label>
                                                                                            <div class="col-md-8">
                                                                                                <input type="text" class="form-control" name="passport_issuedplace" value="<?php echo ($reservation_id > 0 ? isset($tenant->passport_issuedplace) ? $tenant->passport_issuedplace : '' : '');?>" <?php echo isset($tenant->id_type) ? ($tenant->id_type == 2 ? '' : 'readonly') : 'readonly' ?>>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="form-group">
                                                                                            <label class="control-label col-md-3">Issued Date </label>
                                                                                            <div class="col-md-5" >
                                                                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                                                                    <input type="text" class="form-control" name="passport_issueddate" value="<?php echo ($reservation_id > 0 ? isset($tenant->passport_issueddate) ? (trim($tenant->passport_issueddate) != '' && trim($tenant->passport_issuedplace) != '' ? (date('Y', strtotime(dmy_from_db($tenant->passport_issueddate))) > 1970 ? dmy_from_db($tenant->passport_issueddate) : '') : '') :'' : '');?>" <?php echo isset($tenant->id_type) ? ($tenant->id_type == 2 ? '' : 'readonly') : 'readonly' ?>>
                                                                                    <span class="input-group-btn <?php echo isset($tenant->id_type) ? ($tenant->id_type == 2 ? '' : 'hide') : 'hide'?>" id="id_type_picker">
                                                                                        <button class="btn default" type="button" ><i class="fa fa-calendar " ></i></button>
                                                                                    </span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group" style="margin-bottom: 0px;margin-top: 0px;"></div>
                                                                        <div class="form-group" >
                                                                            <label class="control-label col-md-2">Gender</label>
                                                                            <div class="col-md-8">
                                                                                <div class="radio-list" style="padding: 0px;">
                                                                                    <label class="radio-inline">
                                                                                        <input type="radio" name="tenant_sex" id="idgender_male" value="Male" <?php echo ($reservation_id > 0 ? isset($tenant->tenant_sex) ? (strtoupper($tenant->tenant_sex) == 'MALE' ? 'checked' : '') : 'checked' : 'checked')?> > Male </label>
                                                                                    <label class="radio-inline">
                                                                                        <input type="radio" name="tenant_sex" id="idgender_female" value="Female" <?php echo ($reservation_id > 0 ? isset($tenant->tenant_sex) ? (strtoupper($tenant->tenant_sex) == 'FEMALE' ? 'checked' : '') : '' : '') ?>> Female </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">Birth place</label>
                                                                            <div class="col-md-5">
                                                                                <input type="text" class="form-control" name="tenant_pob" value="<?php echo ($reservation_id > 0 ? isset($tenant->tenant_pob) ? $tenant->tenant_pob : '' : '');?>" />
                                                                            </div>
                                                                            <label class="control-label col-md-1">Date</label>
                                                                            <div class="col-md-4">
                                                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                                                    <input type="text" class="form-control" name="tenant_dob" value="<?php echo ($reservation_id > 0 ? isset($tenant->tenant_dob) ? dmy_from_db($tenant->tenant_dob) : '' : '');?>" >
                                                                                    <span class="input-group-btn">
                                                                                        <button class="btn default" type="button" ><i class="fa fa-calendar" ></i></button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-2">Nationality </label>
                                                                            <div class="col-md-7 col-sm-7">
                                                                                <select name="tenant_nationality" class="form-control select2me">
                                                                                    <option value="" <?php echo (isset($tenant->tenant_nationality) ? trim($tenant->tenant_nationality) == '' ? 'selected' : '' : '') ?>>-- not specified --</option>
                                                                                    <?php
                                                                                    $nationalities = $this->db->get_where('ms_nationality');
                                                                                    foreach ($nationalities->result() as $citizen) {
                                                                                        $selected = '';
                                                                                        if (isset($tenant->tenant_nationality)) {
                                                                                            if ($citizen->nationality == $tenant->tenant_nationality) {
                                                                                                $selected = 'selected="selected"';
                                                                                            }
                                                                                        }
                                                                                        echo '<option value="' . $citizen->nationality . '" ' . $selected . '>' . $citizen->nationality . '</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group hide">
                                                                            <label class="control-label col-md-2">Occupation</label>
                                                                            <div class="col-md-8">
                                                                                <input type="text" class="form-control" name="tenant_occupation" value="<?php echo ($reservation_id > 0 ? isset($tenant->tenant_occupation) ? $tenant->tenant_occupation : '' : '');?>" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="control-label col-md-1">Photo</label>
                                                                        <div class="col-md-11">
                                                                            <a class="btn green yellow-stripe btn-take-photo" href="javascript:void(0);">
                                                                                <i class="fa fa-camera "></i>
                                                                                <span> Take Photo </span>
                                                                            </a>
                                                                            <a class="btn green yellow-stripe btn-upload-photo" href="javascript:void(0);">
                                                                                <i class="fa fa-upload "></i>
                                                                                <span> Upload Photo </span>
                                                                            </a>
                                                                            <br/>
                                                                            <canvas style="margin-top: 10px; border: 1px solid #666666;" id="canvas" width="320" height="240"></canvas>
                                                                            <div id="flash"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="portlet box grey-cascade">
                                                                        <div class="portlet-title">
                                                                            <div class="caption">
                                                                                <i class="fa fa-users"></i>
                                                                            </div>
                                                                            <ul class="nav nav-tabs">
                                                                                <li class="active">
                                                                                    <a data-toggle="tab" href="#portlet_tab_family" aria-expanded="true" >Family</a>
                                                                                </li>
                                                                                <li class="">
                                                                                    <a data-toggle="tab" href="#portlet_tab_staff" aria-expanded="false" >Staff </a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="portlet-body">
                                                                            <div class="tab-content">
                                                                                <div id="portlet_tab_family" class="tab-pane active">
                                                                                    <table class="table table-striped table-hover table-bordered table-po-detail" id="table_member_family" name="table_member_family">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th class="text-left" >Name</th>
                                                                                            <th class="text-center" style="width: 12%;">ID Type</th>
                                                                                            <th class="text-left" style="width: 22%;">ID No</th>
                                                                                            <th class="text-center" style="width: 20%;">Mobile</th>
                                                                                            <th class="text-center" style="width: 3%;">&nbsp;</th>
                                                                                        </tr>

                                                                                        </thead>
                                                                                        <tbody>
                                                                                        <?php
                                                                                        if($reservation_id > 0) {
                                                                                            $members = $this->db->get_where('ms_tenant_member', array('tenant_id' => $row->tenant_id));
                                                                                            foreach ($members->result() as $mbr) {
                                                                                                if($mbr->member_type == GUEST_MEMBER_TYPE::FAMILY){
                                                                                                    $img_src = base_url('assets/img/no_image_available.png');
                                                                                                    if (file_exists(FCPATH . "assets/img/tenant/" . $mbr->tenant_id . '_' . $mbr->tenant_member_id . ".jpg")) {
                                                                                                        $img_src = base_url('assets/img/tenant/' . $mbr->tenant_id . '_' . $mbr->tenant_member_id . '.jpg');

                                                                                                    }

                                                                                                    echo '<tr>
                                                                                                    <td class="text-left" style="vertical-align:middle;">' . $mbr->member_name . '</td>
                                                                                                    <td class="text-center" style="vertical-align:middle;">' . GUEST_MEMBER_TYPE::id_caption($mbr->member_type_id) . '</td>
                                                                                                    <td class="text-left" style="vertical-align:middle;">' . $mbr->member_type_no . '</td>
                                                                                                    <td class="text-left" style="vertical-align:middle;">' . $mbr->member_mobile . '</td>
                                                                                                    <td class="text-center" style="vertical-align:middle;">' . '<a class="btn green btn-xs tooltips btn-member-photo fancybox-fast-view" data-original-title="Photo" href="javascript:;"  data-member-id="' . $mbr->tenant_member_id . '" img-src="' . $img_src . '"><i class="fa fa-photo"></i></a>' . '</td>
                                                                                                  </tr>';
                                                                                                }
                                                                                            }

                                                                                        }

                                                                                        ?>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                                <div id="portlet_tab_staff" class="tab-pane">
                                                                                    <table class="table table-striped table-hover table-bordered table-po-detail" id="table_member_staff" name="table_member_staff">
                                                                                        <thead>
                                                                                        <tr>
                                                                                            <th class="text-left" >Staff Name</th>
                                                                                            <th class="text-center" style="width: 12%;">ID Type</th>
                                                                                            <th class="text-left" style="width: 22%;">ID No</th>
                                                                                            <th class="text-center" style="width: 20%;">Mobile</th>
                                                                                            <th class="text-center" style="width: 3%;">&nbsp;</th>
                                                                                        </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                        <?php
                                                                                        if($reservation_id > 0) {
                                                                                            if(isset($members)) {
                                                                                                foreach ($members->result() as $mbr) {
                                                                                                    if ($mbr->member_type == GUEST_MEMBER_TYPE::STAFF) {
                                                                                                        $img_src = base_url('assets/img/no_image_available.png');
                                                                                                    if (file_exists(FCPATH . "assets/img/tenant/" . $mbr->tenant_id . '_' . $mbr->tenant_member_id . ".jpg")) {
                                                                                                        $img_src = base_url('assets/img/tenant/' . $mbr->tenant_id . '_' . $mbr->tenant_member_id . '.jpg');

                                                                                                    }

                                                                                                        echo '<tr>
                                                                                                    <td class="text-left" style="vertical-align:middle;">' . $mbr->member_name . '</td>
                                                                                                    <td class="text-center" style="vertical-align:middle;">' . GUEST_MEMBER_TYPE::id_caption($mbr->member_type_id) . '</td>
                                                                                                    <td class="text-left" style="vertical-align:middle;">' . $mbr->member_type_no . '</td>
                                                                                                    <td class="text-left" style="vertical-align:middle;">' . $mbr->member_mobile . '</td>
                                                                                                    <td class="text-center" style="vertical-align:middle;">' . '<a class="btn green btn-xs tooltips btn-member-photo fancybox-fast-view" data-original-title="Photo" href="javascript:;"  data-member-id="' . $mbr->tenant_member_id . '" img-src="' . $img_src . '"><i class="fa fa-photo"></i></a>' . '</td>
                                                                                                  </tr>';
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }

                                                                                        ?>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <!-- END CURRENCY LIST-->
                                                    </div>
                                                    <div class="tab-pane" id="portlet_note">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <textarea class="form-control" rows="3" name="remark" style="resize: vertical;"><?php echo ($reservation_id > 0 ? $row->remark : '');?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- END TAB PORTLET-->

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
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

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>
<div id="ajax-modal-take-photo" data-width="352" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Take Photo</h4>
    </div>
    <div class="modal-body modal-body-scroll">
        <div class="row">
            <div class="col-md-12">
                <div id="webcam"></div>
                <a style="margin-top:15px;" class="btn green yellow-stripe btn-webcam-photo" href="javascript:void(0);">Capture</a>
            </div>
        </div>
    </div>
</div>
<div id="ajax-modal-upload-photo" data-width="480" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Upload Photo</h4>
    </div>
    <div class="modal-body modal-body-scroll">
        <div class="row">
            <div class="col-md-12">
                <form action="javascript:;" method="post" id="form-upload-photo" class="form-horizontal" onsubmit="return false;" enctype="multipart/form-data">
                    <input id="exampleInputFile" type="file" name="upload_photo" class="">
                    <p class="help-block"> Please use resolution 320 X 240 px. </p>
                    <br/>
                    <input id="btn_submit_upload" type="submit" class="btn green yellow-stripe" value="Upload" name="submit_photo"/>

                    <div id="message_upload"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="ajax-change-room" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<div id="ajax-change-date" class="modal fade bs-modal-sm" data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4>Change Check-In Date</h4>
            </div>
            <div class="modal-body">
                <label class="control-label col-md-4 text-right" style="padding-top: 8px;">Change to</label>
                <div class="col-md-8">
                    <div class="input-group date date-picker" data-date-format="dd-mm-yyyy" id="id_arrival_date">
                        <input type="text" class="form-control" name="c_arrival_date" value="" readonly>
                <span class="input-group-btn">
                    <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue" id="submit-arrival-date">Submit</button>
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
    var rowIndex = <?php echo (isset($rowIndex) ? $rowIndex : 0) ; ?>;

    var bookingMaxAdult = <?php echo (isset($max_adult) ? $max_adult : 0) ; ?>;
    var bookingMaxChild = <?php echo (isset($max_child) ? $max_child : 0) ; ?>;

    if(isedit > 0){
        /*
        $('#form-entry').block({
            message: null ,
            overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
        });
        */
    }

    var handleMask = function() {
        $(".mask_currency").inputmask("numeric",{
            radixPoint:".",
            autoGroup: true,
            groupSeparator: ",",
            digits: 0,
            groupSize: 3,
            removeMaskOnSubmit: true,
            autoUnmask: true
        });
    }

    $(document).ready(function(){
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "right",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal

        };

        var blockUI = function(){
            var valid = '<?php echo ($enable_edit_rate ? 1 : 0) ?>';
            if(valid <= 0){
                $('#div_reservation_type').block({
                    message: null ,
                    overlayCSS: {backgroundColor: '#D6D3D2', opacity:0, cursor:'default'}
                });

                /*
                $('#portlet_agent').block({
                    message: null ,
                    overlayCSS: {backgroundColor: '#D6D3D2', opacity:0, cursor:'default'}
                });
                //$('#btn_lookup_agent').addClass('disabled');
                //$('#btn_reset_agent').addClass('disabled');
                */

                $('.group-billing-type').block({
                    message: null ,
                    overlayCSS: {backgroundColor: '#D6D3D2', opacity:0, cursor:'default'}
                });
                $('.group-room-detail').block({
                    message: null ,
                    overlayCSS: {backgroundColor: '#D6D3D2', opacity:0, cursor:'default'}
                });

            }
        };

        blockUI();

        handleMask();

        <?php
        if ($num_month > 11) {
        ?>
        function check_fullPaid(){
            if ($('input[name="billing_type"]').is(':checked')) {
                $('input[name="is_rate_yearly"]').prop('checked',true);
                Metronic.updateUniform();
                //block
                $('#group-is-yearly-rate').block({
                    message: null ,
                    overlayCSS: {backgroundColor: '#D6D3D2', opacity:0, cursor:'default'}
                });
            } else {
                //unblock
                $('#group-is-yearly-rate').unblock();
            }
        }
        check_fullPaid();
        $('input[name="billing_type"]').live('change', function(){
            check_fullPaid();
            change_rate();
        });
        <?php
        }
        ?>

        var handleValidation = function() {
            // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

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

            $.validator.addMethod("checkTenant", function ()
                {
                    var valid = true;
                    try{
                        //var checked = $('#chk_registered_tenant').val();
                        var checked = $('input:checked[name="chk_registered_tenant"]').length || 0;
                        if(checked > 0){
                            //console.log("checkTenant ..." + checked);
                            var tenantId = $('input[name="tenant_id"]').val() || 0;
                            if(tenantId <= 0){
                                valid = false;
                            }
                        }
                    }catch(e){
                        //console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Name must be not empty or must be selected."
            );

            $.validator.addMethod("checkRooms", function ()
                {
                    var valid = true;
                    //console.log("checkRooms ...");
                    try{
                        var i = 0;
                        $('#table_room > tbody > tr ').each(function() {
                            /*
                            var name_hidden = $(this).find('input[name*="unit_id"]').attr('name');
                            */
                            i++;
                        });

                        if(valid){
                            var rowCount = i;
                            //var rowCount = $("#datatable_item > tbody").children().length;
                            //console.log('checkRooms count = ' + rowCount);

                            if(rowCount <= 0 ){
                                valid = false;
                            }
                        }
                    }catch(e){
                        console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "No room selected. Reservation can not be processed."
            );

            $.validator.addMethod("checkIDType", function ()
                {
                    var valid = true;
                    try{
                        //var checked = $('#chk_registered_tenant').val();
                        var checked = $('input:radio[name="id_type"]:checked').length || 0;
                        //console.log('value ->' + $('input:radio[name="id_type"]:checked').val());
                        if(checked > 0){
                            var idType = $('input:radio[name="id_type"]:checked').val();
                            if(idType == 2){
                                var issuedPlace = $('input:text[name="passport_issuedplace"]').val();
                                var issuedDate = $('input:text[name="passport_issueddate"]').val();

                                if(issuedPlace.trim() == '' || issuedDate.trim() == ''){
                                    valid = false;
                                }
                            }else{

                            }
                        }else{
                            valid = false;
                        }
                    }catch(e){
                        //console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Name must be not empty or must be selected."
            );

            $.validator.addMethod("checkCompanyIfCorporate", function ()
                {
                    var valid = true;
                    try{
                        //var checked = $('#chk_registered_tenant').val();
                        var reservationType = parseInt($('select[name="reservation_type"]').val()) || 0;
                        if(reservationType.toString() == '<?php echo RES_TYPE::CORPORATE ?>' || reservationType.toString() == '<?php echo RES_TYPE::HOUSE_USE ?>'){
                            var companyId = $('input[name="company_id"]').val() || 0;
                            if(companyId <= 0){
                                valid = false;
                            }
                        }
                    }catch(e){
                        valid = false;
                    }

                    return valid;
                },
                "Company must not empty or must be selected."
            );

            var form1 = $('#form-entry');
            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    tenant_name: {
                        required: true,
                        "checkTenant" :{}
                    },
                    passport_no: {
                        required: true,
                        "checkIDType" : {}
                    },
                    passport_issuedplace :{
                        "checkIDType" : {}
                    },
                    passport_issueddate :{
                        "checkIDType" : {}
                    },
                    agent_id:{
                        required: true,
                        min:1
                    },
                    reservation_id:{
                        "checkRooms" :{}
                    },
                    company_id:{
                        "checkCompanyIfCorporate" : {}
                    }
                },
                messages: {
                    tenant_name: "Guest Name must be not empty or must be selected",
                    passport_no: "KTP/KITAS/Passport must not empty",
                    passport_issuedplace: "Passport issued place must not empty",
                    passport_issueddate: "Passport issued date must not empty",
                    tenant_phone: "Phone/Mobile must not empty",
                    agent_id: "Agent must be selected.",
                    reservation_id: "No room selected. Reservation can not be processed.",
                    company_id: "Company must not empty or must be selected."
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.tenant_name != null){
                        toastr["warning"](validator.invalid.tenant_name, "Warning");
                    }
                    if(validator.invalid.passport_no != null){
                        toastr["warning"](validator.invalid.passport_no, "Warning");
                    }
                    if(validator.invalid.passport_issuedplace != null){
                        toastr["warning"](validator.invalid.passport_issuedplace, "Warning");
                    }
                    if(validator.invalid.passport_issueddate != null){
                        toastr["warning"](validator.invalid.passport_issueddate, "Warning");
                    }
                    if(validator.invalid.tenant_phone != null){
                        toastr["warning"](validator.invalid.tenant_phone, "Warning");
                    }
                    if(validator.invalid.agent_id != null){
                        toastr["warning"](validator.invalid.agent_id, "Warning");
                    }
                    if(validator.invalid.reservation_id != null){
                        toastr["warning"](validator.invalid.reservation_id, "Warning");
                    }
                    if(validator.invalid.company_id != null){
                        toastr["warning"](validator.invalid.company_id, "Warning");
                    }

                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});

                    //console.log('text err ' + error.text());
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight

                },

                success: function (label, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    icon.removeClass("fa-warning").addClass("fa-check");
                }
            });
        }

        //initiate validation
        handleValidation();

        $('input[name="chk_registered_tenant"]').on('click', function(){
            var checked = $('input:checked[name="chk_registered_tenant"]').length;
            //console.log('checked ' + checked);
            if(checked > 0){
                $('input:text[name="tenant_name"]').prop('readonly', true);
                $('input:text[name="tenant_name"]').val('');
                $('input[name="tenant_id"]').val('0');
                $('#btn_lookup_tenant').removeAttr('disabled');
            }else{
                $('input:text[name="tenant_name"]').prop('readonly', false);
                $('input[name="tenant_id"]').val('0');
                $('#btn_lookup_tenant').attr('disabled','disabled');
            }
        })

        var grid_tenant = new Datatable();
        //COA
        var handleTableTenant = function (num_index, tenant_id) {
            // Start Datatable Item
            grid_tenant.init({
                src: $("#datatable_tenant"),
                onSuccess: function (grid_tenant) {
                    // execute some code after table records loaded
                },
                onError: function (grid_tenant) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_tenant) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "bSortable": true, "sWidth" : '10%' },
                        { "sWidth" : '40%' },
                        { "sWidth" : '20%' },
                        { "sClass": "text-center", "sWidth" : '15%' },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('frontdesk/reservation/get_modal_tenant');?>/" + num_index + "/" + tenant_id// ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_tenant_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('#btn_lookup_tenant').on('click', function(){
            var $modal = $('#ajax-modal');
            var num_index = parseInt($(this).attr('data-index')) || 0;

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('frontdesk/reservation/ajax_modal_tenant');?>.tpd', '', function () {
                    $modal.modal();
                    var tenant_id = $('input[name="tenant_id"]').val();
                    handleTableTenant(num_index, tenant_id);

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

        $('input[name="is_rate_yearly"]').on('click', function(){
            var isChecked = $(this).is(':checked');
            change_rate();
        });

        /*SET CHECKED RADIO OF ID TYPE*/
        function setIDType(idType){
            if(idType == 2){
                $('input:text[name="passport_issuedplace"]').prop('readonly',false);
                $('input:text[name="passport_issueddate"]').prop('readonly',false);
                $('#id_type_picker').removeClass('hide');
            }else{
                $('input:text[name="passport_issuedplace"]').prop('readonly',true);
                $('input:text[name="passport_issueddate"]').prop('readonly',true);
                $('#id_type_picker').addClass('hide');
            }
        }

        $('input[name="id_type"]').on('click', function(){
            var checked = $('input:radio[name="id_type"]:checked').length || 0;
            //console.log('id_type ->' + $('input:radio[name="id_type"]:checked').val());
            if(checked > 0){
                var idType = $('input:radio[name="id_type"]:checked').val();
                setIDType(idType);
            }
        });

        $('.btn-select-tenant').live('click', function (e) {
            e.preventDefault();

            var tenant_id = parseInt($(this).attr('data-id')) || 0;
            //var tenant_code = $(this).attr('data-code');
            var tenant_name = $(this).attr('data-desc');

            $('input[name="tenant_id"]').val(tenant_id);
            $('input[name="tenant_name"]').val(tenant_name);
            $('input[name="passport_no"]').val($(this).attr('passport-no'));

            var id_type = parseInt($(this).attr('id-type')) || 0;
            $('input:radio[name="id_type"]').prop('checked',false);
            if(id_type == 2){
                $('#idtype_passport').prop('checked', true);
            }else if(id_type == 3){
                $('#idtype_kitas').prop('checked', true);
            }else{
                $('#idtype_ktp').prop('checked', true);
            }
            $.uniform.update();

            setIDType(id_type);

            $('input[name="passport_issuedplace"]').val($(this).attr('passport-place'));
            if($(this).attr('passport-date') != '' && $(this).attr('passport-date') != null){
                $('input[name="passport_issueddate"]').val($(this).attr('passport-date'));
            }else{
                $('input[name="passport_issueddate"]').val('');
            }

            $('input[name="tenant_cellular"]').val($(this).attr('tenant-cellular'));
            $('input[name="tenant_phone"]').val($(this).attr('tenant-phone'));
            $('input[name="tenant_email"]').val($(this).attr('tenant-email'));
            $('textarea[name="tenant_address"]').html($(this).attr('tenant-address'));
            $('input[name="tenant_city"]').val($(this).attr('tenant-city'));
            $('input[name="tenant_postalcode"]').val($(this).attr('tenant-postcode'));

            /*Country*/
            var country = $(this).attr('tenant-country');
            //$('select[name="tenant_country"] option[value="' + country + '"]').attr("selected", true);
            //$('select[name="tenant_country"]').parent().children('.select2-container').children('.select2-choice').children('.select2-chosen').html('' + country + '');
            $('select[name="tenant_country"]').select2("val", country);

            /*Nationality*/
            var nationality = $(this).attr('tenant-nationality');
            $('select[name="tenant_nationality"]').select2("val", nationality);

            /*Sex*/
            var sex = $(this).attr('tenant-sex');
            $('input:radio[name="tenant_sex"]').prop('checked',false);
            if(sex.toLowerCase() == 'male'){
                $('#idgender_male').prop('checked', true);
            }else{
                $('#idgender_female').prop('checked', true);
            }
            $.uniform.update();

            /*Birthdate*/
            $('input[name="tenant_pob"]').val($(this).attr('tenant-birthplace'));
            if($(this).attr('tenant-birthdate') != '' && $(this).attr('tenant-birthdate') != null){
                $('input[name="passport_dob"]').val($(this).attr('tenant-birthdate'));
            }else{
                $('input[name="passport_dob"]').val('');
            }

            $('input[name="tenant_occupation"]').val($(this).attr('tenant-occupation'));

            //Ajax Populate Tenant
            $('#ajax-modal').modal('hide');
        });

        ///LOOKUP AGENT
        var grid_agent = new Datatable();
        //COA
        var handleTableAgent = function (num_index) {
            // Start Datatable Item
            grid_agent.init({
                src: $("#datatable_agent"),
                onSuccess: function (grid_agent) {
                    // execute some code after table records loaded
                },
                onError: function (grid_agent) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_agent) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "sWidth" : '3%' },
                        { "bSortable": true},
                        { "bSortable": false},
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('frontdesk/reservation/get_modal_agent');?>/" + num_index // ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_agent_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('#btn_lookup_agent').on('click', function(){
            var $modal = $('#ajax-modal');
            var num_index = parseInt($(this).attr('data-index')) || 0;

            //console.log('looking up ...');
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('frontdesk/reservation/ajax_modal_agent');?>.tpd', '', function () {
                    $modal.modal();
                    handleTableAgent(num_index);

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

        $('.btn-select-agent').live('click', function (e) {
            e.preventDefault();

            var agent_id = parseInt($(this).attr('data-id')) || 0;
            var name = $(this).attr('data-name');
            var pic = $(this).attr('data-pic');
            var phone = $(this).attr('data-phone');
            var email = $(this).attr('data-email');

            var agent_caption = name;
            if(pic.toLowerCase() != name.toLowerCase()){
                agent_caption += ' - ' + pic;
            }

            $('input[name="agent_id"]').val(agent_id);
            $('input[name="agent_name"]').val(agent_caption);
            $('input[name="agent_pic"]').val(pic);
            $('textarea[name="agent_phone"]').val(phone);
            $('textarea[name="agent_email"]').val(email);

            $('#ajax-modal').modal('hide');

            change_rate();
        });

        $('#btn_reset_agent').on('click', function(){
            $('input[name="agent_id"]').val(0);
            $('input[name="agent_name"]').val('');
            $('input[name="agent_pic"]').val('');
            $('textarea[name="agent_phone"]').val('');
            $('textarea[name="agent_email"]').val('');

            change_rate();
        });

        ///LOOKUP COMPANY
        var grid_company = new Datatable();
        //COA
        var handleTableCompany = function (num_index) {
            // Start Datatable Item
            grid_company.init({
                src: $("#datatable_company"),
                onSuccess: function (grid_company) {
                    // execute some code after table records loaded
                },
                onError: function (grid_company) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_company) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "sWidth" : '3%' },
                        { "bSortable": true},
                        { "bSortable": false},
                        { "sClass": "text-center", "sWidth" : '12%', "bSortable": false },
                        { "sClass": "text-center", "sWidth" : '12%', "bSortable": false },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('frontdesk/reservation/get_modal_company');?>/" + num_index // ajax source
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

        $('#btn_lookup_company').on('click', function(){
            var $modal = $('#ajax-modal');
            var num_index = parseInt($(this).attr('data-index')) || 0;

            //console.log('looking up ...');
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('frontdesk/reservation/ajax_modal_company');?>.tpd', '', function () {
                    $modal.modal();
                    handleTableCompany(num_index);

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

        $('.btn-select-company').live('click', function (e) {
            e.preventDefault();

            var company_id = parseInt($(this).attr('data-id')) || 0;
            var name = $(this).attr('data-name');
            var addr = $(this).attr('data-addr');
            var phone = $(this).attr('data-phone');
            var fax = $(this).attr('data-fax');
            var email = $(this).attr('data-email');
            var pic_name = $(this).attr('data-pic-name');
            var pic_phone = $(this).attr('data-pic-phone');
            var pic_email = $(this).attr('data-pic-email');

            $('input[name="company_id"]').val(company_id);
            $('input[name="company_name"]').val(name);
            $('textarea[name="company_address"]').val(addr);
            $('input[name="company_phone"]').val(phone);
            $('input[name="company_fax"]').val(fax);
            $('input[name="company_email"]').val(email);
            $('input[name="company_pic_name"]').val(pic_name);
            $('input[name="company_pic_phone"]').val(pic_phone);
            $('input[name="company_pic_email"]').val(pic_email);

            $('#ajax-modal').modal('hide');
        });

        $('select[name="reservation_type"]').on('click', function(){
            var corporate = '<?php echo RES_TYPE::CORPORATE ?>';
            var house_use = '<?php echo RES_TYPE::HOUSE_USE ?>';
            if($(this).val() == corporate){
                $('#pnl_company').removeClass('hide');
                $('.group-billing-type').removeClass('hide');
            }else if($(this).val() == house_use){
                $('#pnl_company').removeClass('hide');
                $('.group-billing-type').addClass('hide');
            }else{
                $('input[name="company_id"]').val(0);
                $('input[name="company_name"]').val('');
                $('input[name="company_email"]').val('');
                $('textarea[name="company_address"]').val('');
                $('input[name="company_phone"]').val('');
                $('input[name="company_fax"]').val('');
                $('input[name="company_pic_name"]').val('');
                $('input[name="company_pic_phone"]').val('');
                $('input[name="company_pic_email"]').val('');

                $('#pnl_company').addClass('hide');
                $('.group-billing-type').removeClass('hide');
            }

            change_rate();
        });

        $('input[name*="discount_amount"]').live('keyup', function(e){
           //console.log('Discount ' + $(this).val());
            calculate_discount($(this));
        });

        $('#btn_check_in').live('click', function(e){
            e.preventDefault();

            var id = $(this).attr('data-id');
            //console.log(id);
            bootbox.confirm({
                message: "Perform Check In ?<br><i class=\"font-red-sunglo\">Please make sure all changes has been saved before you continue</i>",
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
                            var url = "<?php echo base_url('frontdesk/management/checkin_form/');?>/" + id;
                            window.location.assign(url);
                        }
                    }
                }
            });
        });

        $('button[name="save"]').click(function(e){
            e.preventDefault();

            if(validateInput()){
                var url = '<?php echo base_url('frontdesk/reservation/submit_reservation.tpd');?>';
                $("#form-entry").append('<input type="hidden" name="save" value="">');
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
            }
        });

        $('button[name="save_close"]').click(function(e){
            e.preventDefault();

            if(validateInput()){
                var url = '<?php echo base_url('frontdesk/reservation/submit_reservation.tpd');?>';
                $("#form-entry").append('<input type="hidden" name="save_close" value="">');
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
            }
        });

        //---CHANGE DATE ---
        $('#btn_change_date').on('click', function(e) {
            e.preventDefault();

            bootbox.confirm({
                message: "Change Check-In date ?",
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
                    if(result === false){
                        //console.log('Empty reason');
                    }else{
                        //var arrival_date = $('input[name="arrival_date[]"]').val();
                        //var startDate = moment(arrival_date, "DD-MM-YYYY");
                        var startDate = moment();

                        $('#id_arrival_date').datepicker("setDate", startDate.format('DD-MM-YYYY'));

                        var $modal_cal = $('#ajax-change-date');

                        if ($modal_cal.hasClass('modal-overflow') === false) {
                            $modal_cal.addClass('modal-overflow');
                        }

                        $modal_cal.css({'margin-top': '0px'});

                        $modal_cal.modal();
                    }
                }
            });
        });

        $('#submit-arrival-date').on('click', function(){
            var reservation_id = parseFloat($('input[name="reservation_id"]').val()) || 0;
            var new_arrival_date = $('input[name="c_arrival_date"]').val();
            var num_month = parseInt($('input[name="num_month[]"]').val()) || 0;

            if(reservation_id > 0 && new_arrival_date != ''){

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('frontdesk/reservation/ajax_get_departure');?>",
                    dataType: "json",
                    async: false,
                    data: {arrival_date: new_arrival_date, num_month: num_month}
                })
                .done(function( msg ) {
                    if(msg.valid == '1') {
                        if (msg.departure_date != '') {
                            $('input[name="arrival_date[]"]').val(new_arrival_date);
                            $('input[name="departure_date[]"]').val(msg.departure_date);

                            change_rate();

                            //
                        }
                    }else{
                        toastr["error"]("Check In date cannot be changed !", "Warning");
                    }
                });

                $('#ajax-change-date').modal('hide');
            }else{
                toastr["warning"]('Please choose arrival date', "Warning");
            }

        });

        //------------------

        //load webcam
        var $modal_webcam = $('#ajax-modal-take-photo');

        $('.btn-take-photo').live('click', function(){
            var tenant_id = parseInt($('input[name="tenant_id"]').val()) || 0;
            if (tenant_id > 0) {
                $modal_webcam.modal();

                $.fn.modalmanager.defaults.resize = true;

                if ($modal_webcam.hasClass('bootbox') == false) {
                    $modal_webcam.addClass('modal-fix');
                }

                if ($modal_webcam.hasClass('modal-overflow') === false) {
                    $modal_webcam.addClass('modal-overflow');
                }

                $modal_webcam.css({'margin-top': '0px'});
            } else {
                toastr["warning"]("Please select Guest or save current form to continue!", "Warning");
            }
        });

        $('.btn-webcam-photo').on('click', function() {
            toastr.clear();
            //if (typeof document.getElementById('XwebcamXobjectX').capture() == 'function') {
                //webcam.capture();
                document.getElementById('XwebcamXobjectX').capture();
            //} else {
                //toastr["warning"]("Please wait while page loading or refresh page.", "Warning");
            //}
        });

        //upload photo
        var $modal_upload = $('#ajax-modal-upload-photo');

        $('.btn-upload-photo').live('click', function(){
            var tenant_id = parseInt($('input[name="tenant_id"]').val()) || 0;
            if (tenant_id > 0) {
                $modal_upload.modal();

                $.fn.modalmanager.defaults.resize = true;

                if ($modal_upload.hasClass('bootbox') == false) {
                    $modal_upload.addClass('modal-fix');
                }

                if ($modal_upload.hasClass('modal-overflow') === false) {
                    $modal_upload.addClass('modal-overflow');
                }

                $modal_upload.css({'margin-top': '0px'});
            } else {
                toastr["warning"]("Please select Guest or save current form to continue!", "Warning");
            }
        });

        $("#exampleInputFile").on('change', function() {
            $("#message_upload").empty(); // To remove the previous error message
            var file = this.files[0];
            var imagefile = file.type;
            var match= ["image/jpeg","image/png","image/jpg"];
            if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
            {
                $("#message_upload").html("<p id='error'>Please select a valid Image File</p>"+"<h4>Note</h4>"+"<span id='error_message'>Only jpeg, jpg and png Images type allowed</span>");
                return false;
            }
        });

        function load_img() {
            canvas = document.getElementById("canvas");

            if (canvas.getContext) {
                ctx = document.getElementById("canvas").getContext("2d");
                ctx.clearRect(0, 0, 320, 240);

                var img_src = '<?php echo base_url('assets/img/no_image_available.png');?>';

                var tenant_id = parseInt($('input[name="tenant_id"]').val()) || 0;

                $.ajax({
                    url: "<?php echo base_url('frontdesk/reservation/check_picture_exist');?>/" + tenant_id,
                    type: "POST",
                    async: false
                })
                .done(function(data)   // A function to be called if request succeeds
                {
                    if (data == '1') {
                        img_src  = '<?php echo base_url('assets/img/tenant/' . ($reservation_id > 0 ? $row->tenant_id : 0) . '.jpg');?>';
                    }
                });

                var img = new Image();
                img.src = img_src + "?" + new Date().getTime();
                img.onload = function() {
                    ctx.drawImage(img, 0, 0);
                    console.log(img.src);
                }
                image = ctx.getImageData(0, 0, 320, 240);
            }
        }

        $('#form-upload-photo').on('submit', function(e){
            e.preventDefault();

            $("#message_upload").empty();
            $('#btn_submit_upload').val('Saving...');

            var tenant_id = parseInt($('input[name="tenant_id"]').val()) || 0;

            $.ajax({
                url: "<?php echo base_url('frontdesk/reservation/picture_submit_upload');?>/" + tenant_id, // Url to which the request is send
                type: "POST",             // Type of request to be send, called as method
                data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                contentType: false,       // The content type used when sending data to the server.
                dataType: "json",
                cache: false,             // To unable request pages to be cached
                processData: false,        // To send DOMDocument or non processed data file it is set to false
                async: false
            })
                .done(function(data)   // A function to be called if request succeeds
                {
                    $('#btn_submit_upload').val('Upload');

                    if (data.error == '0') {
                        toastr["success"]("Success upload photo.", "Warning");

                        $('#ajax-modal-upload-photo').modal('hide');

                        setTimeout(load_img, 2000);

                    } else if (data.error == '1') {
                        toastr["error"](data.message, "Warning");
                    } else {
                        toastr["error"]("Photo can not be uploaded !", "Warning");
                    }
                })
                .fail(function(data)
                {
                    $('#btn_submit_upload').val('Upload');
                    toastr["error"]("Photo can not be uploaded !", "Warning");
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
                    helpers: {
                        title: {
                            type: 'inside'
                        }
                    }
                });
            }
        }

        handleFancybox();

        $('.btn-member-photo').live('click', function(){
            d = new Date();
            var url = $(this).attr('img-src') + "?" + d.getTime();
            //console.log(url);
            $('#img_member_photo').attr('src',url);
            //$("#Image").attr("src", "dummy.jpg");
            //$("#img_member_photo").attr("src", $('#img_member_photo').val()+"&"+Math.floor(Math.random()*1000));

            $.fancybox('#member-img-popup');
        });

    });

    function validateInput(){
        var valid = true;

        //CHECK SELECTED UNIT IS STILL AVAILABLE
        var reservation_id = parseInt($('input[name="reservation_id"]').val()) || 0;
        var res_type = parseInt($('select[name="reservation_type"]').val()) || 0;

        if(reservation_id <= 0){
            $('#table_room > tbody > tr ').each(function() {
                var unit_id = $(this).find('input[name*="unit_id[]"]').val();
                var unit_code = $(this).find('input[name*="unit_code[]"]').val();
                var arrival = $(this).find('input[name*="arrival_date[]"]').val();
                var departure = $(this).find('input[name*="departure_date[]"]').val();

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('frontdesk/reservation/xIsUnitsValidForSubmit');?>",
                    dataType: "json",
                    async: false,
                    data: {unit_id : unit_id, arrival_date : arrival, departure_date: departure}
                })
                    .done(function(result) {
                        if(result.valid == '0'){
                            valid = false;
                            toastr["error"]("Room " + unit_code + " no longer available !", "Warning");
                        }
                    });
            });
        }

        var grand_total = 0;
        $('#table_room > tbody > tr ').each(function() {
            grand_total += Math.round(parseFloat($(this).find('input[name*="subtotal_amount"]').val()) || 0);
        });

        if(grand_total > 0 || res_type == '<?php echo RES_TYPE::HOUSE_USE; ?>'){
            valid = $("#form-entry").valid();
        }else{
            valid = false;
            toastr["warning"]("Room Charges is not valid", "Warning");
        }

        return valid;
    }

    function change_rate(){
        //Obtain ajax for Reservation
        var form_data = $('#form-entry').serializeArray();
        var is_houseuse = false;
        var res_type = $('select[name="reservation_type"]').val();
        if(res_type == '<?php echo RES_TYPE::HOUSE_USE; ?>'){
            is_houseuse = true;
        }
        //form_data.push({name: "reservation_type", value: $('select[name="reservation_type"]').val()});
        //form_data.push({name: "agent_id", value: $('input[name="agent_id"]').val()});

        $.ajax({
            type: "POST",
            url: "<?php echo base_url('frontdesk/reservation/ajax_change_reservation_type');?>",
            async: false,
            data: form_data
        })
            .done(function(result) {
                $('#table_room tbody').html(result);
                calculate_discount(null);
                handleMask();
                $('input[name*="discount_amount"]').prop('readonly', is_houseuse);
                $('#btn_check_in').addClass('hide');
                //Metronic.unblockUI('.modal-content-edit');
            });
    }

    function calculate_discount(e){
        if(e != null){
            var disc_amount = Math.round(parseFloat(e.val()) || 0);
            var closest_row = e.closest('tr');
            var local_amount = Math.round(parseFloat(closest_row.find('input[name*="local_amount"]').val()) || 0);
            var tax_rate = parseFloat(closest_row.find('input[name*="tax_rate"]').val()) || 0;
            var tax_amount = Math.round((local_amount - disc_amount) * tax_rate);
            //console.log('source ' + local_amount + " - " + disc_amount + " * " + tax_rate + " | " + closest_row.find('input[name*="tax_rate"]').val());
            //console.log('calculate_discount ' + tax_amount);
            closest_row.find('input[name*="tax_amount"]').val(tax_amount);
            var subtotal = Math.round((local_amount - disc_amount) + tax_amount);
            closest_row.find('input[name*="subtotal_amount"]').val(subtotal);
        }

        var grand_total = 0;
        $('#table_room > tbody > tr ').each(function() {
            grand_total += Math.round(parseFloat($(this).find('input[name*="subtotal_amount"]').val()) || 0);
        });

        $('input[name="grand_total_amount"').val(grand_total);
    }

    function delete_record(unitId){
        if(unitId > 0){
            bootbox.confirm("Are you sure want to delete?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        target: '.modal-content-edit',
                        boxed: true,
                        message: 'Processing...'
                    });

                    var reservation_id = $('#reservation_id').val();
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('frontdesk/reservation/delete_reservation_room');?>",
                        dataType: "json",
                        data: { reservation_id : reservation_id, unit_id: unitId}
                    })
                        .done(function( msg ) {
                            if(msg.type != '0'){

                            }
                            else {
                                console.log('ajax delete unit.' + unitId);
                            }

                            delete_frontend(unitId);
                            Metronic.unblockUI('.modal-content-edit');
                        });
                }
            });
        }
    }

    function delete_frontend(unitId){
        try{
            $('#table_room > tbody > tr').find('input[name*="unit_id"][value="' + unitId + '"]').parent().parent().remove();
        }catch(e){
            console.log(e);
        }
    }

    //WEBCAM

    var pos = 0;
    var ctx = null;
    var cam = null;
    var image = null;

    jQuery("#webcam").webcam({

        width: 320,
        height: 240,
        mode: "callback",
        swffile: "<?php echo base_url(); ?>assets/global/plugins/jquery-webcam/jscam_canvas_only.swf",

        onTick: function(remain) {

            if (0 == remain) {
                jQuery("#status").text("Cheese!");
            } else {
                jQuery("#status").text(remain + " seconds remaining...");
            }
        },

        onSave: function(data) {
            // Work with the picture. Picture-data is encoded as an array of arrays... Not really nice, though =/

            $('.btn-webcam-photo').html('Saving...');
            var col = data.split(";");
            var img = image;

            var finished = false;

            for(var i = 0; i < 320; i++) {
                var tmp = parseInt(col[i]);
                img.data[pos + 0] = (tmp >> 16) & 0xff;
                img.data[pos + 1] = (tmp >> 8) & 0xff;
                img.data[pos + 2] = tmp & 0xff;
                img.data[pos + 3] = 0xff;
                pos+= 4;
            }

            if (pos >= 4 * 320 * 240) {
                var tenant_id = parseInt($('input[name="tenant_id"]').val()) || 0;
                ctx.putImageData(img, 0, 0);

                /*$.post("<?php echo base_url('frontdesk/reservation/picture_submit');?>/" + tenant_id, {type: "data", image: canvas.toDataURL("image/png")});*/

                $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('frontdesk/reservation/picture_submit/');?>" + '/' + tenant_id,
                            dataType: false,
                            data: {image: canvas.toDataURL("image/png")},
                            async: false
                        })
                            .done(function (msg) {
                                finished = true;
                                console.log('success upload');
                            });

                pos = 0;

                if (finished) {
                    $('#ajax-modal-take-photo').modal('hide');
                    toastr["success"]("Succeess capture photo.", "Succeess");

                    $('.btn-webcam-photo').html('Capture');
                } else {
                    toastr["error"]("Photo cannot be saved.", "Error");
                    console.log('failed');
                }
            }
        },

        onCapture: function () {
            try {
                //var save_me = webcam.save();
                document.getElementById('XwebcamXobjectX').save();
            } catch (e) {
                console.log(e);
                bootbox.alert("Error, cannot save photo, please refresh page !");
            }

            // Show a flash for example

        },

        debug: function (type, string) {
            // Write debug information to console.log() or a div, ...
        },

        onLoad: function () {
            // Page load
            var cams = webcam.getCameraList();
            for(var i in cams) {
                jQuery("#cams").append("<li>" + cams[i] + "</li>");
            }
        }
    });

    function getPageSize() {

        var xScroll, yScroll;

        if (window.innerHeight && window.scrollMaxY) {
            xScroll = window.innerWidth + window.scrollMaxX;
            yScroll = window.innerHeight + window.scrollMaxY;
        } else if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac
            xScroll = document.body.scrollWidth;
            yScroll = document.body.scrollHeight;
        } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
            xScroll = document.body.offsetWidth;
            yScroll = document.body.offsetHeight;
        }

        var windowWidth, windowHeight;

        if (self.innerHeight) { // all except Explorer
            if (document.documentElement.clientWidth) {
                windowWidth = document.documentElement.clientWidth;
            } else {
                windowWidth = self.innerWidth;
            }
            windowHeight = self.innerHeight;
        } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
            windowWidth = document.documentElement.clientWidth;
            windowHeight = document.documentElement.clientHeight;
        } else if (document.body) { // other Explorers
            windowWidth = document.body.clientWidth;
            windowHeight = document.body.clientHeight;
        }

        // for small pages with total height less then height of the viewport
        if (yScroll < windowHeight) {
            pageHeight = windowHeight;
        } else {
            pageHeight = yScroll;
        }

        // for small pages with total width less then width of the viewport
        if (xScroll < windowWidth) {
            pageWidth = xScroll;
        } else {
            pageWidth = windowWidth;
        }

        return [pageWidth, pageHeight];
    }

    window.addEventListener("load", function() {

        jQuery("body").append("<div id=\"flash\"></div>");

        var canvas = document.getElementById("canvas");

        if (canvas.getContext) {
            ctx = document.getElementById("canvas").getContext("2d");
            ctx.clearRect(0, 0, 320, 240);

            var img_src = '<?php echo base_url('assets/img/no_image_available.png');?>';

            var tenant_id = parseInt($('input[name="tenant_id"]').val()) || 0;

            $.ajax({
                url: "<?php echo base_url('frontdesk/reservation/check_picture_exist');?>/" + tenant_id,
                type: "POST",
                async: false
            })
            .done(function(data)   // A function to be called if request succeeds
            {
                if (data == '1') {
                    img_src  = '<?php echo base_url('assets/img/tenant');?>/' + tenant_id + '.jpg';
                }
            });

            var img = new Image();
            img.src = img_src + "?" + new Date().getTime();
            img.onload = function() {
                ctx.drawImage(img, 0, 0);
                console.log(img.src);
            }
            image = ctx.getImageData(0, 0, 320, 240);
        }

        var pageSize = getPageSize();
        jQuery("#flash").css({
            //height: pageSize[1] + "px"
        });

    }, false);

    window.addEventListener("resize", function() {

        var pageSize = getPageSize();
        jQuery("#flash").css({
            //height: pageSize[1] + "px"
        });

    }, false);

</script>