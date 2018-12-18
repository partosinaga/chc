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
                $isedit = true;
                $back_url = base_url('frontdesk/billing/bill_hsk_manage/1.tpd');
                if ($bill_id > 0) {
                    if ($bill->status != STATUS_NEW) {
                        $isedit = false;
                        $back_url = base_url('frontdesk/billing/bill_hsk_manage/2.tpd');
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
							<i class="fa fa-star"></i>Housekeeping
						</div>
						<div class="actions">
                            <a href="<?php echo (isset($back_url) ? $back_url : base_url('frontdesk/billing/bill_hsk_manage/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="#" onsubmit="return false;" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="bill_id" name="bill_id" value="<?php echo $bill_id;?>" />
                            <?php
                            if($isedit){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn blue-ebonyclay btn-sm btn-circle" name="save_close" id="save_close">Save</button>
                                        <?php
                                        if($bill_id > 0) {
                                            if($bill->status == STATUS_NEW && $bill->res_status == ORDER_STATUS::CHECKIN) {
                                        ?>
                                        <button type="button" class="btn blue btn-sm btn-circle" name="btn_posting" id="btn-posting">Posting</button>
                                        <?php }
                                        } ?>
                                    </div>
								</div>
							</div>
                            <?php
                            }
                            ?>
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3">Folio #</label>
                                            <div class="col-md-8 col-sm-8" >
                                                <div class="input-group">
                                                    <input type="hidden" name="reservation_id" value="<?php echo ($bill_id > 0 ? $bill->reservation_id : '0');?>">
                                                    <input type="hidden" name="tenant_id" value="<?php echo ($bill_id > 0 ? $bill->tenant_id : '0');?>">
                                                    <input type="hidden" name="company_id" value="<?php echo ($bill_id > 0 ? $bill->company_id : '0');?>">
                                                    <input type="hidden" name="taxtype_id" value="<?php echo (isset($tax_type) ? $tax_type->taxtype_id : 0);?>">
                                                    <input type="hidden" name="taxtype_vat" value="<?php echo (isset($tax_type) ? ($tax_type->taxtype_percent > 0 ? $tax_type->taxtype_percent / 100 : 0) : 0);?>">
                                                    <input type="hidden" name="max_month" value="<?php echo (isset($max_month) > 0 ? $max_month : '0');?>" >
                                                    <input type="text" class="form-control" name="reservation_code" value="<?php echo ($bill_id > 0 ? $bill->reservation_code : '');?>" readonly/>
                                                     <span class="input-group-btn">
                                                       <a id="btn_lookup_reservation" class="btn btn-success" href="javascript:;" >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="padding-top: 10px;">
                                            <span class="control-label"  id="label-info-1"><?php echo($bill_id > 0 ? trim($bill->company_name) != '' ? 'Company : ' . $bill->company_name : 'Guest : ' . $bill->tenant_fullname : ''); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="padding-top: 10px;">
                                            <span class="control-label"  id="label-info-2"><?php echo($bill_id > 0 ? $bill->reservation_type == RES_TYPE::CORPORATE || $bill->reservation_type == RES_TYPE::HOUSE_USE ? 'Guest : ' . $bill->tenant_fullname : '' : ''); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row <?php echo(($bill_id > 0 ? ($bill->reservation_type == RES_TYPE::CORPORATE ? '' : 'hide') : 'hide')) ?>" id="panel_bill_to">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3">Bill to</label>
                                            <div class="radio-list col-md-8 col-sm-8" >
                                                <label class="radio-inline" >
                                                    <input type="radio" name="debtor_type" id="debtor_type_main" value="1" <?php echo ($bill_id > 0 ? ($bill->reservation_type == RES_TYPE::CORPORATE ? ($bill->company_id > 0 ? 'checked' : '') : 'checked') : 'checked'); ?>> Company </label>
                                                <label class="radio-inline" >
                                                    <input type="radio" name="debtor_type" id="debtor_type_other" value="0" <?php echo ($bill_id > 0 ? ($bill->reservation_type == RES_TYPE::CORPORATE ? ($bill->company_id <= 0 ? 'checked' : '') : '') : ''); ?>> Guest </label>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box light bg-inverse" >
                                            <div class="portlet light bg-inverse">
                                                <div class="portlet-title" style="margin-bottom: 0px;">
                                                    <div class="caption font-purple-plum" style="padding-top: 0px;">
                                                        <i class="icon-speech font-purple-plum "></i>
                                                        <span class="caption-subject bold"> Package</span>&nbsp;
                                                    </div>

                                                    <div class="actions" style="padding-top: 0px;margin-bottom: 0px;">
                                                        <!-- a href="javascript:;" class="btn btn-circle green-meadow btn-sm charge_add">
                                                            <i class="fa fa-plus"></i> Add </a -->

                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <!-- BEGIN TAB PORTLET-->
                                                            <table class="table table-striped table-hover table-bordered table-po-detail" id="table_extra" name="table_extra">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center" style="width: 10%;">Unit</th>
                                                                    <th class="text-center">Housekeeping Package</th>
                                                                    <th class="text-center" style="width: 10%;">Month</th>
                                                                    <th class="text-right" style="width: 10%;">Price</th>
                                                                    <th class="text-right" style="width: 15%;">Amount</th>
                                                                    <th class="text-right" style="width: 10%;">Tax</th>
                                                                    <th class="text-right" style="width: 10%;">Subtotal</th>
                                                                    <th style="width: 10px;"></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                if ($bill_id > 0) {
                                                                    $qry_unit = $this->db->query('select distinct ms_unit.unit_id, ms_unit.unit_code from ms_unit join cs_reservation_detail on cs_reservation_detail.unit_id = ms_unit.unit_id join cs_reservation_header on cs_reservation_detail.reservation_id = cs_reservation_header.reservation_id where cs_reservation_header.reservation_id = ' . $bill->reservation_id);
                                                                    $qry_item = $this->mdl_general->get('pos_master_item', array('is_package >' => 0, 'status' => STATUS_NEW), array(), 'masteritem_code');

                                                                    $i = 0;
                                                                    foreach ($details->result() as $row_detail) {
                                                                        $unit = '';
                                                                        $item = '';
                                                                        foreach ($qry_unit->result() as $row_unit) {
                                                                            $unit .= '<option value="' . $row_unit->unit_id . '" ' . ($row_unit->unit_id == $row_detail->unit_id ? 'selected="selected"' : '') . '>' . $row_unit->unit_code . '</option>';
                                                                        }
                                                                        foreach ($qry_item->result() as $row_item) {
                                                                            $item .= '<option value="' . $row_item->masteritem_id . '" data-price-lock="' . 1 . '" data-unit-price="' . $row_item->masteritem_price . '" data-unit-discount="' . 0 . '" data-week="' . $row_item->no_of_week . '"' . ($row_item->masteritem_id == $row_detail->masteritem_id ? 'selected="selected"' : '') . '>[ ' . $row_item->masteritem_code . ' ] ' . $row_item->masteritem_title . '</option>';
                                                                        }
                                                                        //$qry_item_detail = $this->db->get_where('pos_master_item', array('itemstock_id' => $row_detail->masteritem_id));
                                                                        //$row_item_detail = $qry_item_detail->row();
                                                                        echo '<tr>
                                                                            <input type="hidden" name="billdetail_id[' . $i . ']" value="' . $row_detail->billdetail_id . '">
                                                                            <input type="hidden" name="status[' . $i . ']" value="1" class="class_status">
                                                                            <td class="text-center" style="vertical-align:middle;">
                                                                            <select name="unit_id[' . $i . ']" class="form-control form-filter input-sm select2me">
                                                                            <option value="0">-- Select Unit --</option>
                                                                            ' . $unit . '
                                                                            </select></td>
                                                                            <td><select name="masteritem_id[' . $i . ']" class="form-control form-filter input-sm select2me select_item" data-index="' . $i . '">
                                                                            <option value="0">-- Select Item --</option>
                                                                            ' . $item . '
                                                                            </select></td>
                                                                            <td ><input type="text" name="item_qty[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->item_qty . '" class="form-control text-right input-sm mask_currency num_cal"></td>
                                                                            <td ><input type="text" name="item_rate[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->rate . '" class="form-control text-right input-sm mask_currency num_cal" readonly></td>
                                                                            <td ><input type="text" name="item_amount[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->amount . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td ><input type="text" name="tax_amount[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->tax . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td ><input type="text" name="subtotal_amount[' . $i . ']" data-index="' . $i . '" value="' . ($row_detail->amount - $row_detail->disc_amount + $row_detail->tax) . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td style="vertical-align:middle;">
                                                                            <!-- a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' . $i . '" style="margin-right:0px;"><i class="fa fa-times"></i></a --></td>
                                                                            </tr>';
                                                                        $i++;
                                                                    }
                                                                }
                                                                ?>
                                                                </tbody>
                                                                <tfoot>
                                                                <tr>
                                                                    <th class="text-center" style="vertical-align: middle;">
                                                                        <?php if($form_mode == '') { ?>
                                                                            <a href="javascript:;" class="btn btn-circle green-meadow btn-sm charge_add hide">
                                                                                <i class="fa fa-plus"></i> Add </a>
                                                                        <?php } ?>
                                                                    </th>
                                                                    <th class="text-right" disabled colspan="5"><span class="form-control-static">&nbsp;</span></th>
                                                                    <th ><input type="text" name ="total_amount" id ="total_amount" class="form-control text-right mask_currency" value="<?php echo ($bill_id > 0 ? $bill->amount : 0);?>" readonly/></th>
                                                                    <th colspan="2" >&nbsp;</th>
                                                                </tr>
                                                                </tfoot>
                                                            </table>
                                                            <!-- END TAB PORTLET-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box light " >
                                            <div class="portlet light">
                                                <div class="portlet-body">
                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="hidden" name="max_day_of_week" value="<?php echo isset($max_day_of_week) ? $max_day_of_week : ''; ?>">
                                                            <label class="control-label col-md-2 col-sm-2">Start</label>
                                                            <div class="radio-list col-md-4 col-sm-4" >
                                                                <div class="input-group date date-picker ">
                                                                    <!-- i class="fa fa-calendar"></i -->
                                                                    <input type="text" name="hsk_start_date" data-date-viewmode="years" data-date-format="dd-mm-yyyy" value="<?php echo $bill_id > 0 ? dmy_from_db($hsk->hsk_start_date) : date('d-m-Y') ?>" size="16" class="form-control date-picker text-center" readonly>
                                                                    <span class="input-group-btn">
                                                                        <button class="btn default" type="button" ><i class="fa fa-calendar" ></i></button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-2 col-sm-2">Until</label>
                                                            <div class="radio-list col-md-4 col-sm-4" >
                                                                <input type="text" name="hsk_end_date" data-date-viewmode="years" data-date-format="dd-mm-yyyy" value="<?php echo $bill_id > 0 ? dmy_from_db($hsk->hsk_end_date) : '' ?>" size="16" class="form-control text-center" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-1 col-sm-1">Every </label>
                                                                <div class="col-md-9 ">
                                                                    <div class="input-group" style="padding-top: 9px;">
                                                                        <input type="checkbox" class="form-control" value="1" name="chk_day[]" <?php echo isset($hsk->day_of_week) ? (strpos($hsk->day_of_week, '1') !== false ? 'checked="checked"' : '') : '';?> >Monday&nbsp;
                                                                        <input type="checkbox" class="form-control" value="2" name="chk_day[]" <?php echo isset($hsk->day_of_week) ? (strpos($hsk->day_of_week, '2') !== false ? 'checked="checked"' : '') : '';?> >Tuesday&nbsp;
                                                                        <input type="checkbox" class="form-control" value="3" name="chk_day[]" <?php echo isset($hsk->day_of_week) ? (strpos($hsk->day_of_week, '3') !== false ? 'checked="checked"' : '') : '';?>>Wednesday&nbsp;
                                                                        <input type="checkbox" class="form-control hide" value="4" name="chk_day[]" checked="checked" ><i class="fa fa-check"></i>&nbsp;<span class="font-blue tooltips" data-original-title="">Thursday</span>&nbsp;
                                                                        <input type="checkbox" class="form-control" value="5" name="chk_day[]" <?php echo isset($hsk->day_of_week) ? (strpos($hsk->day_of_week, '5') !== false ? 'checked="checked"' : '') : '';?> >Friday&nbsp;
                                                                        <input type="checkbox" class="form-control" value="6" name="chk_day[]" <?php echo isset($hsk->day_of_week) ? (strpos($hsk->day_of_week, '6') !== false ? 'checked="checked"' : '') : '';?> >Saturday&nbsp;
                                                                        <input type="checkbox" class="form-control" value="7" name="chk_day[]" <?php echo isset($hsk->day_of_week) ? (strpos($hsk->day_of_week, '7') !== false ? 'checked="checked"' : '') : '';?>>Sunday&nbsp;
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
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
	</div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    $(document).ready(function(){
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "right",
                format: 'dd-mm-yyyy',
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        };

        var isedit = <?php echo ($isedit ? 1 : 0); ?>;

        if(isedit == 0){
             $('#form-entry').block({
                 message: null ,
                 overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
             });
        }
        <?php
        if($bill_id > 0){
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
        }
        ?>

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
        }

        handleMask();

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

        var handleValidation = function() {
            // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation

            var form1 = $('#form-entry');
            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    reservation_code: {
                        required: true
                    }
                },
                messages: {
                    tenant_name: "Name must be not empty or must be selected"

                },
                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.tenant_name != null){
                        toastr["warning"](validator.invalid.tenant_name, "Warning");
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
        //handleValidation();

        var grid_1 = new Datatable();
        //COA
        var handleTableReservation = function (num_index) {
            var reservation_id = parseInt($('input[name="reservation_id"]').val()) || 0;
            // Start Datatable Item
            grid_1.init({
                src: $("#datatable_guest"),
                onSuccess: function (grid_1) {
                    // execute some code after table records loaded
                },
                onError: function (grid_1) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_1) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "bSortable": true, "sWidth" : '10%' },
                        { "sClass": "text-center", "sWidth" : '10%' },
                        null,
                        null,
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('frontdesk/billing/get_modal_othercharge');?>/" + num_index + "/" + reservation_id
                    }
                }
            });

            var tableWrapper = $("#datatable_guest_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('#btn_lookup_reservation').live('click', function(){
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
                $modal.load('<?php echo base_url('frontdesk/billing/ajax_modal_othercharge');?>.tpd', '', function () {
                    $modal.modal();
                    handleTableReservation(num_index);

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

        $('.btn-select-reservation').live('click', function (e) {
            e.preventDefault();

            var reservation_id = parseInt($(this).attr('data-reservation-id')) || 0;
            var reservation_type = parseInt($(this).attr('data-reservation-type')) || 0;
            var reservation_code = $(this).attr('data-reservation-code');
            var tenant_id = parseInt($(this).attr('data-tenant-id')) || 0;
            var company_id = $(this).attr('data-company-id');
            var company_name = $(this).attr('data-company-name');
            var tenant_name = $(this).attr('data-tenant-name');
            var max_month = parseInt($(this).attr('data-max-month')) || 0;

            if(reservation_type == '<?php echo(RES_TYPE::CORPORATE);?>'){
                $('#label-info-1').html('Company : ' + company_name);
                $('#label-info-2').html("Guest : " + tenant_name);

                $('#panel_bill_to').removeClass('hide');
            }else if(reservation_type == '<?php echo(RES_TYPE::HOUSE_USE);?>'){
                $('#label-info-1').html('Company : ' + company_name);
                $('#label-info-2').html("Guest : " + tenant_name);

                $('#panel_bill_to').addClass('hide');
            }else{
                $('#label-info-1').html("Guest : " + tenant_name);
                $('#label-info-2').html('');

                $('#panel_bill_to').addClass('hide');
            }

            $('input[name="reservation_id"]').val(reservation_id);
            $('input[name="reservation_code"]').val(reservation_code);
            $('input[name="tenant_id"]').val(tenant_id);
            $('input[name="company_id"]').val(company_id);
            $('input[name="max_month"]').val(max_month);

            $('#ajax-modal').modal('hide');

            add_charge();
        });

        $('.charge_add').live('click', function (e) {
            e.preventDefault();

            add_charge();
        });

        var add_charge = function(){
            var reservationId = parseInt($('input[name="reservation_id"]').val()) || 0;
            if (reservationId > 0) {
                var i = $('#table_extra tbody tr').length;

                var selectUnit = '';
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('frontdesk/billing/select_tenant_unit');?>",
                    async: false,
                    data: {reservation_id: reservationId}
                })
                    .done(function (msg) {
                        selectUnit = msg;
                    });

                var item = '';
                item += '<option value="0">-- Select Item --</option>';
                <?php
                $qry_item = $this->mdl_general->get('pos_master_item', array('is_package > ' => 0), array(), 'masteritem_code');
                foreach($qry_item->result() as $row_item) {
                ?>
                item += '<option value="<?php echo $row_item->masteritem_id;?>" data-price-lock="1" data-unit-price="<?php echo $row_item->masteritem_price;?>" data-unit-discount="<?php echo 0;?>" data-week="<?php echo $row_item->no_of_week;?>"><?php echo '[ ' . $row_item->masteritem_code . ' ] ' . $row_item->masteritem_title;?></option>';
                <?php
                }
                ?>

                //Add to temp
                var newRowContent = '<tr>' +
                    '<input type="hidden" name="billdetail_id[' + i + ']" value="0">' +
                    '<input type="hidden" name="status[' + i + ']" value="1" class="class_status">' +
                    '<td class="text-center" style="vertical-align:middle;">' +
                    '<select name="unit_id[' + i + ']" class="form-control form-filter input-sm select2me">' +
                    '<option value="0">-- Select Unit --</option>' +
                    selectUnit +
                    '</select></td>' +
                    '<td><select name="masteritem_id[' + i + ']" class="form-control form-filter input-sm select2me select_item" data-index="' + i + '">' +
                    item +
                    '</select></td>' +
                    '<td ><input type="text" name="item_qty[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency num_cal" readonly></td>' +
                    '<td ><input type="text" name="item_rate[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency num_cal" readonly></td>' +
                    '<td ><input type="text" name="item_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                    '<td ><input type="text" name="tax_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                    '<td ><input type="text" name="subtotal_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                    '<td style="vertical-align:middle;">' +
                    '<a class="btn btn-danger btn-xs tooltips btn-remove hide" data-original-title="Remove" href="javascript:;" data-index="' + i + '" style="margin-right:0px;"><i class="fa fa-times"></i></a></td>' +
                    '</tr>';

                $('#table_extra tbody').append(newRowContent);

                $('select.select2me').select2();
                handleMask();

                $('.charge_add').addClass('hide');
            } else {
                toastr["warning"]("Please select Tenant.", "Warning");
            }
        }

        $('.select_item').live('change', function(){
            var item_id = parseInt($(this).val()) || 0;
            var i = $(this).attr('data-index');
            if (item_id > 0) {
                var price_lock = parseInt($('option:selected', this).attr('data-price-lock')) || 0;
                var unit_price = parseFloat($('option:selected', this).attr('data-unit-price')) || 0;
                var unit_discount = parseFloat($('option:selected', this).attr('data-unit-discount')) || 0;
                var max_day_of_week = parseFloat($('option:selected', this).attr('data-week')) || 0;
                var amount = unit_price - unit_discount;

                $('input[name="item_qty[' + i + ']"]').val('1');
                $('input[name="item_rate[' + i + ']"]').val(unit_price);
                $('input[name="item_amount[' + i + ']"]').val(amount);

                $('input[name="item_qty[' + i + ']"]').removeAttr('readonly');
                if (price_lock > 0) {
                    $('input[name="item_rate[' + i + ']"]').attr('readonly');
                } else {
                    $('input[name="item_rate[' + i + ']"]').removeAttr('readonly');
                }

                $('input[name="max_day_of_week"]').val(max_day_of_week);
            } else {
                $('input[name="item_qty[' + i + ']"]').val('0');
                $('input[name="item_rate[' + i + ']"]').val('0');
                $('input[name="item_amount[' + i + ']"]').val('0');
                $('input[name="tax_amount[' + i + ']"]').val('0');
                $('input[name="subtotal_amount[' + i + ']"]').val('0');

                $('input[name="item_qty[' + i + ']"]').attr('readonly');
                $('input[name="item_rate[' + i + ']"]').attr('readonly');
                $('input[name="max_day_of_week"]').val(0);
            }

            calculate_row(i);
        });

        $('.num_cal').live('keyup', function(){
            var i = $(this).attr('data-index');

            calculate_row(i);
        });

        function calculate_row(i) {
            var qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;
            var price = parseFloat($('input[name="item_rate[' + i + ']"]').val()) || 0;

            var amount = parseFloat((qty * price).toFixed(2)) || 0;

            var tax_rate = parseFloat($('input[name="taxtype_vat"]').val()) || 0;
            var tax_amount = tax_rate * amount;
            var subtotal = amount + tax_amount;

            $('input[name="item_amount[' + i + ']"]').val(amount);
            $('input[name="tax_amount[' + i + ']"]').val(tax_amount);
            $('input[name="subtotal_amount[' + i + ']"]').val(subtotal);

            calculate_all();
            obtain_departure();
        }

        function calculate_all(){
            var total_amount = 0;
            var ii = $('#table_extra tbody tr').length;
            for(var i=0;i<=ii;i++){
                var stat = $('input[name="status[' + i + ']"]').val();
                if(stat != '<?php echo STATUS_DELETE;?>') {
                    var amount = parseFloat($('input[name="subtotal_amount[' + i + ']"]').val()) || 0;
                    total_amount = amount + total_amount;
                }
            }

            $('input[name="total_amount').val(total_amount);
        }

        var obtain_departure = function(){
            var dateStart = $('input[name="hsk_start_date"]').val();
            var num_month = parseInt($('input[name*="item_qty"]').val()) || 0;

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('frontdesk/reservation/ajax_get_departure');?>",
                dataType: "json",
                data: {arrival_date: dateStart, num_month: num_month}
            })
                .done(function( msg ) {
                    if(msg.valid == '1') {
                        if (msg.departure_date != '') {
                            $('input[name="hsk_end_date"]').val(msg.departure_date);
                        }
                    }else{
                        $('input[name="hsk_end_date"]').val('');
                    }
                });
        };

        $('.btn-remove').live('click', function(){
            var i = $(this).attr('data-index');
            var this_btn = $(this);

            bootbox.confirm("Are you sure want to delete?", function (result) {
                if (result == true) {
                    this_btn.closest('tr').addClass('hide');
                    this_btn.closest('tr').find('.class_status').val('9');

                    calculate_all();
                }
            });
        });

        function validate_submit(){
            var result = true;

            if($('.form-group').hasClass('has-error')){
                $('.form-group').removeClass('has-error');
            }

            var tenant_id = parseInt($('input[name="tenant_id"]').val()) || 0;
            var company_id = parseInt($('input[name="company_id"]').val()) || 0;
            var reservation_id = parseInt($('input[name="reservation_id"]').val()) || 0;

            if(reservation_id <= 0){
                toastr["warning"]("Reservation id not found.", "Warning!");
                $('input[name="reservation_code"]').closest('.form-group').addClass('has-error');
                result = false;
            }

            var hsk_end_date = $('input[name="hsk_end_date"]').val();
            if(hsk_end_date == ''){
                toastr["warning"]("Housekeeping end date must not empty.", "Warning!");
                $('input[name="hsk_end_date"]').closest('.form-group').addClass('has-error');
                result = false;
            }

            var i = 0;
            var i_act = 0;
            var item_array = new Array();
            $('#table_extra > tbody > tr ').each(function() {
                if(!$(this).hasClass('hide')) {
                    var unit_id = parseFloat($('select[name="unit_id[' + i + ']"]').val()) || 0;
                    var masteritem_id = parseFloat($('select[name="masteritem_id[' + i + ']"]').val()) || 0;
                    var item_qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;
                    var item_rate = parseFloat($('input[name="item_rate[' + i + ']"]').val()) || 0;
                    var item_amount = parseFloat($('input[name="item_amount[' + i + ']"]').val()) || 0;
                    var item_discount = 0;
                    var item_tax = parseFloat($('input[name="tax_amount[' + i + ']"]').val()) || 0;
                    var max_month = parseFloat($('input[name="max_month"]').val()) || 0;
                    var max_days = parseFloat($('input[name="max_day_of_week"]').val()) || 0;

                    if (item_array.indexOf(masteritem_id, 0) > -1) {
                        toastr["warning"]("Item is duplicate.", "Warning");
                        result = false;
                    } else {
                        item_array.push(masteritem_id);
                    }

                    $('select[name="unit_id[' + i + ']"]').removeClass('has-error');
                    $('select[name="masteritem_id[' + i + ']"]').removeClass('has-error');
                    $('input[name="item_qty[' + i + ']"]').removeClass('has-error');
                    $('input[name="item_rate[' + i + ']"]').removeClass('has-error');
                    $('input[name="item_amount[' + i + ']"]').removeClass('has-error');
                    $('input[name="tax_amount[' + i + ']"]').removeClass('has-error');

                    if(unit_id <= 0){
                        toastr["warning"]("Please select unit.", "Warning");
                        $('select[name="unit_id[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    if(masteritem_id <= 0){
                        toastr["warning"]("Please select Item.", "Warning");
                        $('select[name="masteritem_id[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    if(item_qty <= 0){
                        toastr["warning"]("Please input Month.", "Warning");
                        $('input[name="item_qty[' + i + ']"]').addClass('has-error');
                        result = false;
                    }else{
                        if(item_qty > max_month){
                            toastr["warning"]("Max Month must not exceed " + max_month + ".", "Warning");
                            $('input[name="item_qty[' + i + ']"]').addClass('has-error');
                            result = false;
                        }
                    }

                    var checkedCount = $('input[name="chk_day[]"]:checked').length || 0;
                    //console.log('hsk ' + checkedCount + ' = ' + max_days);
                    max_days++;
                    if(checkedCount != max_days){
                        toastr["warning"]("Please choose " + max_days + " days in a week.", "Warning");
                        //$('select[name="masteritem_id[' + i + ']"]').addClass('has-error');
                        result = false;
                    }

                    if(item_rate <= 0){
                        toastr["warning"]("Please input Rate.", "Warning");
                        $('input[name="item_rate[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    if(item_amount <= 0){
                        toastr["warning"]("Please input valid amount.", "Warning");
                        $('input[name="item_amount[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    i_act++;
                }
                i++;
            });

            if(i_act <= 0 ){
                toastr["warning"]("Package must be selected.", "Warning");
                result = false;
            }

            return result;
        }

        $('#save_close').on('click', function(){
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
                    url: "<?php echo base_url('frontdesk/billing/ajax_bill_hsk_submit');?>",
                    dataType: "json",
                    data: form_data
                })
                    .done(function( msg ) {
                        if(msg.valid == '0' || msg.valid == '1'){
                            if(msg.valid == '1'){
                                window.location.assign(msg.link);
                            }
                            else {
                                toastr["error"](msg.message, "Error");
                                $('#form-entry').unblock();
                            }
                        }
                        else {
                            toastr["error"]("Submit data failed, please try again later.", "Error");
                            $('#form-entry').unblock();
                        }
                    })
                    .fail(function () {
                        $('#form-entry').unblock();
                        toastr["error"]("Submit data failed, please try again later.", "Error");
                    });
            }
            else {
                $('#form-entry').unblock();
            }
        });

        $('#btn-posting').click(function(e) {
            e.preventDefault();

            bootbox.confirm("Are you sure want to Posting selected Housekeeping Package ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        target: '.form-entry',
                        boxed: true,
                        message: 'Processing...'
                    });

                    //var form_data = $('#form-entry').serializeArray();
                    var bill_id = $('input[name="bill_id"]').val();

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('frontdesk/billing/ajax_bill_single_posting/1');?>",
                        dataType: "json",
                        data: {bill_id : bill_id}
                    })
                        .done(function (msg) {
                            if(msg.valid == '0' || msg.valid == '1'){
                                if(msg.valid == '1'){
                                    window.location.assign(msg.link);
                                }
                                else {
                                    toastr["error"](msg.message, "Error");
                                    $('#form-entry').unblock();
                                }
                            }
                            else {
                                toastr["error"]("Posting failed, please try again later.", "Error");
                                $('#form-entry').unblock();
                            }
                        })
                        .fail(function () {
                            $('.form-entry').unblock();
                            toastr["error"]("Something has wrong, please try again later.", "Error");
                        });
                }
            });

        });

    });
</script>