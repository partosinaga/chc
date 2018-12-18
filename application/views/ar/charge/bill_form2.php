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
                $back_url = base_url('ar/corporate_bill/other_charge/1.tpd');
                if ($bill_id > 0) {
                    if ($bill->status != STATUS_NEW) {
                        $isedit = false;
                        $back_url = base_url('ar/corporate_bill/other_charge/2.tpd');
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
							<i class="fa fa-star"></i>Client Charges Form
						</div>
						<div class="actions">
                            <a href="<?php echo (isset($back_url) ? $back_url : base_url('ar/corporate_bill/other_charge/1.tpd')); ?>" class="btn default green-stripe">
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
                                            if($bill->status == STATUS_NEW) {
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
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-sm-2 text-right">Client</label>
                                            <div class="col-md-7 col-sm-7" >
                                                <div class="input-group">
                                                    <input type="hidden" name="company_id" value="<?php echo ($bill_id > 0 ? $bill->company_id : '0');?>">
                                                    <input type="hidden" name="taxtype_id" value="<?php echo (isset($tax_type) ? $tax_type->taxtype_id : 0);?>">
                                                    <input type="hidden" name="taxtype_vat" value="<?php echo (isset($tax_type) ? ($tax_type->taxtype_percent > 0 ? $tax_type->taxtype_percent / 100 : 0) : 0);?>">
                                                    <input type="text" class="form-control" name="company_name" value="<?php echo ($bill_id > 0 ? $bill->company_name : '');?>" readonly/>
                                                     <span class="input-group-btn">
                                                       <a id="btn_lookup_company" class="btn btn-success" href="javascript:;" >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 text-right" for="subject">Note </label>
                                            <div class="col-md-10">
                                                <div class="input-icon right">
                                                    <i class="fa"></i>
                                                    <input placeholder="Note" type="text" class="form-control" name="subject" value="<?php echo ($bill_id > 0 ? $bill->subject : '');?>" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box light bg-inverse" >
                                            <div class="portlet light bg-inverse">
                                                <div class="portlet-title" style="margin-bottom: 0px;">
                                                    <div class="caption font-purple-plum" style="padding-top: 0px;">
                                                        <i class="icon-speech font-purple-plum "></i>
                                                        <span class="caption-subject uppercase bold"> Charges</span>&nbsp;
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
                                                                    <th class="text-center" style="width: 12%;">Item</th>
                                                                    <th class="text-center" style="width: 6px;"></th>
                                                                    <th class="text-center">Description</th>
                                                                    <th class="text-center" style="width: 5%;">Qty</th>
                                                                    <th class="text-right" style="width: 8%;">Price</th>
                                                                    <th class="text-right" style="width: 10%;">Amount</th>
                                                                    <th class="text-right" style="width: 7%;">Tax</th>
                                                                    <th class="text-right" style="width: 10%;">Subtotal</th>
                                                                    <th style="width: 10px;"></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                if ($bill_id > 0) {

                                                                    $qry_item = $this->mdl_general->get('view_pos_item_stock', array(), array(), 'item_desc');

                                                                    $i = 0;
                                                                    foreach ($details->result() as $row_detail) {
                                                                        $unit = '';
                                                                        $item = '';
                                                                        /*
                                                                        foreach ($qry_unit->result() as $row_unit) {
                                                                            $unit .= '<option value="' . $row_unit->unit_id . '" ' . ($row_unit->unit_id == $row_detail->unit_id ? 'selected="selected"' : '') . '>' . $row_unit->unit_code . '</option>';
                                                                        }*/
                                                                        foreach ($qry_item->result() as $bill_item) {
                                                                            if($bill_item->itemstock_id == $row_detail->item_id){
                                                                                $item .= '<option value="' . $bill_item->itemstock_id . '" item-desc="' . $bill_item->item_desc . '" data-price-lock="' . $bill_item->price_lock . '" data-unit-price="' . $bill_item->unit_price . '" data-unit-discount="' . $bill_item->unit_discount . '" selected="selected" is-service="' . $bill_item->is_service_item . '" unit-max-qty="'. $bill_item->itemstock_current_qty . '">' . $bill_item->item_code . '</option>';
                                                                            }
                                                                            //$item .= '<option value="' . $bill_item->itemstock_id . '" item-desc="' . $bill_item->item_desc . '" data-price-lock="' . $bill_item->price_lock . '" data-unit-price="' . $bill_item->unit_price . '" data-unit-discount="' . $bill_item->unit_discount . '" ' . ($bill_item->itemstock_id == $bill_detail->item_id ? 'selected="selected" ' : '') . ' is-service="' . $bill_item->is_service_item . '" unit-max-qty="'. $bill_item->itemstock_current_qty . '">[ ' . $bill_item->item_code . ' ] ' . $bill_item->item_desc . '</option>';
                                                                        }

                                                                        $qry_item_detail = $this->db->get_where('pos_item_stock', array('itemstock_id' => $row_detail->item_id));
                                                                        $row_item_detail = $qry_item_detail->row();

                                                                        $maxAttr = '';
                                                                        $rowIconClass = 'fa fa-coffee font-green';
                                                                        if($row_item_detail->is_service_item <= 0){
                                                                            $maxAttr = ' qty-max="' . ($row_item_detail->itemstock_current_qty + $row_detail->item_qty) . '"';
                                                                            $rowIconClass = 'fa fa-cube font-blue';
                                                                        }

                                                                        echo '<tr>
                                                                            <input type="hidden" name="billdetail_id[' . $i . ']" value="' . $row_detail->billdetail_id . '">
                                                                            <input type="hidden" name="status[' . $i . ']" value="1" class="class_status">
                                                                            <td><select name="item_id[' . $i . ']" class="form-control form-filter input-sm select2me select_item" data-index="' . $i . '">
                                                                            ' . $item . '
                                                                            </select></td>
                                                                            <td><i id="icon_row_' . $i . '" style="padding-top:5px;" class="' . $rowIconClass . '"></i></td>
                                                                            <td><textarea name="item_desc['. $i .']" rows="2" class="form-control" style="resize: vertical;font-size:11px;">' . $row_detail->item_desc . '</textarea></td>
                                                                            <td ><input type="text" name="item_qty[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->item_qty . '" class="form-control text-right input-sm mask_currency num_cal" ' . $maxAttr .' ></td>
                                                                            <td ><input type="text" name="item_rate[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->rate . '" class="form-control text-right input-sm mask_currency num_cal" ' . '></td>
                                                                            <td ><input type="text" name="item_amount[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->amount . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td ><input type="text" name="tax_amount[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->tax . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td ><input type="text" name="subtotal_amount[' . $i . ']" data-index="' . $i . '" value="' . ($row_detail->amount - $row_detail->disc_amount + $row_detail->tax) . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td style="vertical-align:top;padding-top:7px;">
                                                                            <a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' . $i . '" style="margin-right:0px;"><i class="fa fa-times"></i></a></td>
                                                                            </tr>';
                                                                        $i++;
                                                                    }
                                                                }
                                                                ?>
                                                                </tbody>
                                                                <tfoot>
                                                                <tr>
                                                                    <th class="text-left" style="vertical-align: middle;">
                                                                        <?php if($form_mode == '') { ?>
                                                                            <a href="javascript:;" class="btn btn-circle green-meadow btn-sm charge_add">
                                                                                <i class="fa fa-plus"></i> Add </a>
                                                                        <?php } ?>
                                                                    </th>
                                                                    <th class="text-right" disabled colspan="6"><span class="form-control-static">&nbsp;</span></th>
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
                    company_name: {
                        required: true
                    }
                },
                messages: {
                    company_name: "Name must not empty or must be selected",
                    subject: "Note must not empty"
                },
                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.company_name != null){
                        toastr["warning"](validator.invalid.company_name, "Warning");
                    }
                    if(validator.invalid.subject != null){
                        toastr["warning"](validator.invalid.subject, "Warning");
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

        var grid1 = new Datatable();
        //COA
        var handleTableCompany = function (num_index, company_id, bill_id) {
            // Start Datatable Item
            grid1.init({
                src: $("#datatable_modal"),
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sWidth" : '40%' ,"sClass": "text-left"},
                        null,
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '11%' }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, -1],
                        [10, 20, 50, 100, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('ar/deposit/get_modal_companies');?>/" + num_index + "/" + company_id + "/" + bill_id // ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_modal_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('#btn_lookup_company').live('click', function(){
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
                $modal.load('<?php echo base_url('ar/deposit/xmodal_company');?>.tpd', '', function () {
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

        $('.btn-select-record').live('click', function (e) {
            e.preventDefault();

            var company_id = parseInt($(this).attr('data-company-id')) || 0;
            var company_name = $(this).attr('data-company-name');

            $('input[name="company_name"]').val(company_name);
            $('input[name="company_id"]').val(company_id);

            $('#ajax-modal').modal('hide');
        });

        $('.charge_add').live('click', function (e) {
            e.preventDefault();

            var companyId = parseInt($('input[name="company_id"]').val()) || 0;

            var myform = '#form-entry';

            if (companyId > 0) {
                //Doing Ajax
                var form_data = $(myform).serializeArray();

                Metronic.blockUI({
                    target: myform,
                    boxed: true,
                    message: 'Processing...'
                });

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('ar/corporate_bill/xother_charge_add');?>",
                    data: form_data
                })
                    .done(function( msg ) {
                        //console.log(msg);
                        $('#table_extra tbody').append(msg);

                        $(myform).unblock();
                        $('select.select2me').select2();
                        handleMask();
                    })
                    .fail(function() {
                        toastr["error"]("Please select an item", "Warning");
                        $(myform).unblock();
                    });
            } else {
                toastr["error"]("Please select a client.", "Warning");
                $(myform).unblock();
            }
        });

        $('.charge_add2').live('click', function (e) {
            e.preventDefault();

            var companyId = parseInt($('input[name="company_id"]').val()) || 0;

            if (companyId > 0) {
                var i = $('#table_extra tbody tr').length;

                var item = '';
                item += '<option value="0">-Select-</option>';
                <?php
                $qry_item = $this->mdl_general->get('get_pos_item_active', array('is_package <=' => 0), array(), 'item_desc');
                foreach($qry_item->result() as $row_item) {
                ?>
                item += '<option value="<?php echo $row_item->itemstock_id;?>" item-desc="<?php echo $row_item->item_desc;?>" data-price-lock="<?php echo $row_item->price_lock;?>" data-unit-price="<?php echo $row_item->unit_price;?>" data-unit-discount="<?php echo $row_item->unit_discount;?>" tax-type-percent="<?php echo $row_item->taxtype_percent;?>" tax-type-code="<?php echo $row_item->taxtype_code;?>"  is-service="<?php echo $row_item->is_service_item;?>" unit-max-qty="<?php echo $row_item->itemstock_current_qty;?>"><?php echo $row_item->item_code;?></option>';
                <?php
                }
                ?>

                //Add to temp
                var newRowContent = '<tr>' +
                    '<input type="hidden" name="billdetail_id[' + i + ']" value="0">' +
                    '<input type="hidden" name="status[' + i + ']" value="1" class="class_status">' +
                    '<td><select name="item_id[' + i + ']" class="form-control form-filter input-sm select2me select_item" data-index="' + i + '">' +
                    item +
                    '</select>' +
                    '</td>' +
                    '<td><i id="icon_row_' + i + '" class="fa " style="padding-top:5px;"></i></td>' +
                    '<td><textarea name="item_desc[' + i + ']" rows="2" class="form-control" style="resize: vertical;font-size: 11px;"></textarea></td>' +
                    '<td ><input type="text" name="item_qty[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency num_cal" ></td>' +
                    '<td ><input type="text" name="item_rate[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency num_cal" ></td>' +
                    '<td ><input type="text" name="item_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                    '<td ><input type="text" name="tax_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                    '<td ><input type="text" name="subtotal_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                    '<td style="vertical-align:middle;">' +
                    '<a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' + i + '" style="margin-right:0px;"><i class="fa fa-times"></i></a></td>' +
                    '</tr>';

                $('#table_extra tbody').append(newRowContent);

                $('select.select2me').select2();
                handleMask();
            } else {
                toastr["warning"]("Please select a client.", "Warning");
            }
        });

        $('.select_item').live('change', function(){
            var item_id = parseInt($(this).val()) || 0;
            var i = $(this).attr('data-index');
            if (item_id > 0) {
                var item_desc = $('option:selected', this).attr('item-desc');
                var price_lock = parseInt($('option:selected', this).attr('data-price-lock')) || 0;
                var unit_price = parseFloat($('option:selected', this).attr('data-unit-price')) || 0;
                var unit_discount = parseFloat($('option:selected', this).attr('data-unit-discount')) || 0;
                var tax_rate = parseFloat($('option:selected', this).attr('tax-type-percent')) || 0;
                var is_service = parseInt($('option:selected', this).attr('is-service')) || 0;
                var unit_max_qty = $('option:selected', this).attr('unit-max-qty');
                var tax_code = $('option:selected', this).attr('tax-type-code');
                var amount = unit_price - unit_discount;

                $('textarea[name="item_desc[' + i + ']"]').val(item_desc);
                $('input[name="item_qty[' + i + ']"]').val('1');

                if(is_service <= 0){
                    $('input[name="item_qty[' + i + ']"]').attr('qty-max',unit_max_qty);

                    $('#icon_row_' + i).removeClass('fa-coffee font-green');
                    $('#icon_row_' + i).addClass('fa-cube font-blue');
                }else{

                    $('#icon_row_' + i).removeClass('fa-cube font-blue');
                    $('#icon_row_' + i).addClass('fa-coffee font-green');
                }

                $('input[name="item_rate[' + i + ']"]').val(unit_price);
                $('input[name="item_amount[' + i + ']"]').val(amount);
                //$('input[name="taxtype_percent[' + i + ']"]').val(tax_rate);
                $('input[name="tax_code[' + i + ']"]').val(tax_code);

                $('input[name="item_qty[' + i + ']"]').removeAttr('readonly');
                if (price_lock > 0) {
                    $('input[name="item_rate[' + i + ']"]').attr('readonly');
                } else {
                    $('input[name="item_rate[' + i + ']"]').removeAttr('readonly');
                }
            } else {
                $('textarea[name="item_desc[' + i + ']"]').val('');
                $('input[name="item_qty[' + i + ']"]').val('0');
                $('input[name="item_qty[' + i + ']"]').removeAttr('qty-max');

                $('input[name="item_rate[' + i + ']"]').val('0');
                $('input[name="item_amount[' + i + ']"]').val('0');
                $('input[name="tax_amount[' + i + ']"]').val('0');
                //$('input[name="taxtype_percent[' + i + ']"]').val('0');
                $('input[name="subtotal_amount[' + i + ']"]').val('0');
                //$('input[name="tax_code[' + i + ']"]').val(tax_code);

                $('input[name="item_qty[' + i + ']"]').attr('readonly');
                $('input[name="item_rate[' + i + ']"]').attr('readonly');
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

            var company_id = parseInt($('input[name="company_id"]').val()) || 0;

            if(company_id <= 0){
                toastr["warning"]("Client not found.", "Warning!");
                $('input[name="company_name"]').closest('.form-group').addClass('has-error');
                result = false;
            }

            if(result) {
                result = $("#form-entry").valid();
            }

            var i = 0;
            var i_act = 0;
            var item_array = new Array();
            $('#table_extra > tbody > tr ').each(function() {
                var item_status = parseFloat($('input[name="status[' + i + ']"]').val());
                //console.log('index ' + i + ' = ' + item_status);
                if(!$(this).hasClass('hide') && item_status == '<?php echo STATUS_NEW; ?>') {
                    if (result) {
                        var item_id = parseFloat($('select[name="item_id[' + i + ']"]').val()) || 0;
                        var item_desc = $('textarea[name="item_desc[' + i + ']"]').val();
                        var item_qty = parseFloat($('input[name="item_qty[' + i + ']"]').val());
                        var item_qty_max = parseFloat($('input[name="item_qty[' + i + ']"]').attr('qty-max'));
                        var item_rate = parseFloat($('input[name="item_rate[' + i + ']"]').val()) || 0;
                        var item_amount = parseFloat($('input[name="item_amount[' + i + ']"]').val()) || 0;
                        var item_discount = 0;
                        var item_tax = parseFloat($('input[name="tax_amount[' + i + ']"]').val()) || 0;

                        $('select[name="item_id[' + i + ']"]').removeClass('has-error');
                        $('input[name="item_desc[' + i + ']"]').removeClass('has-error');
                        $('input[name="item_qty[' + i + ']"]').removeClass('has-error');
                        $('input[name="item_rate[' + i + ']"]').removeClass('has-error');
                        $('input[name="item_amount[' + i + ']"]').removeClass('has-error');
                        $('input[name="tax_amount[' + i + ']"]').removeClass('has-error');

                        if(item_id == '' || item_id <= 0){
                            toastr["error"]("Please select item description.", "Warning");
                            $('select[name="item_id[' + i + ']"]').addClass('has-error');
                            result = false;
                        }else{
                            if(item_array.indexOf(item_id) <= -1){
                                item_array.push(item_id);
                            }else{
                                toastr["error"]("Item duplicate found [" + item_desc + "]", "Warning");
                                $('select[name="item_id[' + i + ']"]').addClass('has-error');
                                result = false;
                            }
                        }

                        if (item_desc == '') {
                            toastr["warning"]("Please type item description.", "Warning");
                            $('textarea[name="item_desc[' + i + ']"]').addClass('has-error');
                            result = false;
                        }

                        if(item_qty <= 0){
                            toastr["warning"]("Please input Item Qty.", "Warning");
                            $('input[name="item_qty[' + i + ']"]').addClass('has-error');
                            result = false;
                        }else{
                            if(item_qty_max > 0){
                                if(item_qty > item_qty_max){
                                    toastr["error"]($('select[name="item_id[' + i + ']"]').select2('data').text + " Max qty is " + item_qty_max, "Warning");
                                    $('input[name="item_qty[' + i + ']"]').addClass('has-error');
                                    result = false;
                                }
                            }
                        }

                        if (item_rate <= 0) {
                            toastr["warning"]("Please input Item Price.", "Warning");
                            $('input[name="item_rate[' + i + ']"]').addClass('has-error');
                            result = false;
                        }
                        if (item_amount <= 0) {
                            toastr["warning"]("Please input valid amount.", "Warning");
                            $('input[name="item_amount[' + i + ']"]').addClass('has-error');
                            result = false;
                        }
                        i_act++;
                    }else{
                        console.log('not valid');
                    }
                }
                i++;
            });

            if(i_act <= 0 ){
                toastr["warning"]("Detail cannot be empty.", "Warning");
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
                    url: "<?php echo base_url('frontdesk/billing/ajax_bill_submit');?>",
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

            bootbox.confirm("Are you sure want to Posting selected Billing ?", function (result) {
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
                        url: "<?php echo base_url('frontdesk/billing/ajax_bill_single_posting');?>",
                        dataType: "json",
                        data: {bill_id : bill_id, post_item_supplies : 0}
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