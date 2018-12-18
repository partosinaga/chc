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
                if ($pro_inv_id > 0) {
                    if ($bill->status != STATUS_NEW) {
                        $isedit = false;
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
							<i class="fa fa-star"></i>Proforma Invoice Form
						</div>
						<div class="actions">
                            <a href="<?php echo (isset($back_url) ? $back_url : base_url('ar/corporate_bill/proforma/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="#" onsubmit="return false;" method="post" id="form-entry" class="form-horizontal" >
							<input type="hidden" id="pro_inv_id" name="pro_inv_id" value="<?php echo $pro_inv_id;?>" />
                            <div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
                                        <?php
                                        if($pro_inv_id <= 0 || $isedit) { ?>
                                            <button type="button" class="btn blue-ebonyclay btn-sm btn-circle"
                                                    name="save_close" id="save_close">Save
                                            </button>
                                            <?php
                                        }
                                        if($pro_inv_id > 0) {
                                            if($bill->status == STATUS_NEW && $isedit) {
                                        ?>
                                        <button type="button" class="btn blue btn-sm btn-circle" name="btn_approve" id="btn-approve">Approve</button>
                                        <?php }else { ?>
                                        <button type="button" class="btn btn-circle purple-studio btn-sm btn-circle" name="save_close" id="save_close"><i class="fa fa-print"></i> &nbsp; Print</button>
                                        <?php
                                            }
                                        } ?>

                                    </div>
								</div>
							</div>
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3">Company </label>
                                            <div class="col-md-8 col-sm-8" >
                                                <div class="input-group">
                                                    
                                                    <input type="hidden" name="company_id" value="<?php echo ($pro_inv_id > 0 ? $bill->company_id : '0');?>">
                                                    <input type="hidden" name="taxtype_id" value="<?php echo (isset($tax_type) ? $tax_type->taxtype_id : 0);?>">
                                                    <input type="hidden" name="taxtype_vat" value="<?php echo (isset($tax_type) ? ($tax_type->taxtype_percent > 0 ? $tax_type->taxtype_percent / 100 : 0) : 0);?>">
                                                    <input type="text" class="form-control" name="company_name" value="<?php echo ($pro_inv_id > 0 ? $bill->company_name : '');?>" readonly/>
                                                     <span class="input-group-btn">
                                                       <a id="btn_lookup_company" class="btn btn-success" href="javascript:;" >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label class="control-label col-md-3" for="pro_inv_date">Proforma Date </label>
                                            <div class="col-md-6" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="pro_inv_date" value="<?php echo ($pro_inv_id > 0 ? dmy_from_db($bill->pro_inv_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Proforma No</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="pro_inv_no" value="<?php echo ($pro_inv_id > 0 ? $bill->pro_inv_no : 'NEW');?>" disabled />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="pro_inv_due_date">Due Date </label>
                                            <div class="col-md-6" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="pro_inv_due_date" value="<?php echo ($pro_inv_id > 0 ? dmy_from_db($bill->pro_inv_due_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
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
                                                                    <th class="text-center">Description</th>
                                                                    <th class="text-center" style="width: 10%;">Qty</th>
                                                                    <th class="text-right" style="width: 10%;">Price</th>
                                                                    <th class="text-right" style="width: 15%;">Amount</th>
                                                                    <th class="text-right" style="width: 10%;">Tax</th>
                                                                    <th class="text-right" style="width: 10%;">Subtotal</th>
                                                                    <th style="width: 10px;"></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                if ($pro_inv_id > 0) {
                                                                     
                                                                    $i = 0;
                                                                    foreach ($details->result() as $row_detail) {
                                                                        
                                                                        $item = '';
                                                                          
                                                                        echo '<tr>
                                                                            <input type="hidden" name="pro_invdetail_id[' . $i . ']" value="' . $row_detail->pro_invdetail_id . '">
                                                                            <input type="hidden" name="status[' . $i . ']" value="1" class="class_status">                                                                            
																			<td ><input type="text" name="item_desc[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->item_desc . '" class="form-control  input-sm  "></td>
                                                                            <td ><input type="text" name="item_qty[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->item_qty . '" class="form-control text-right input-sm mask_currency num_cal"></td>
                                                                            <td ><input type="text" name="item_rate[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->item_rate . '" class="form-control text-right input-sm mask_currency num_cal" ></td>
                                                                            <td ><input type="text" name="item_amount[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->amount . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td ><input type="text" name="tax_amount[' . $i . ']" data-index="' . $i . '" value="' . $row_detail->tax . '" class="form-control text-right input-sm mask_currency num_cal_tax" ></td>
                                                                            <td ><input type="text" name="subtotal_amount[' . $i . ']" data-index="' . $i . '" value="' . ($row_detail->amount + $row_detail->tax) . '" class="form-control text-right input-sm mask_currency" readonly></td>
                                                                            <td style="vertical-align:middle;">
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
                                                                    <th class="text-right" disabled colspan="4"><span class="form-control-static">&nbsp;</span></th>
                                                                    <th ><input type="text" name ="total_amount" id ="total_amount" class="form-control text-right mask_currency" value="<?php echo ($pro_inv_id > 0 ? $bill->total_amount : 0);?>" readonly/></th>
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
        if($pro_inv_id > 0){
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
                        { "sWidth" : '20%' ,"bSortable": true},
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
                $modal.load('<?php echo base_url('ar/proforma_inv/ajax_modal_company');?>.tpd', '', function () {
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

        $('.charge_add').live('click', function (e) {
            e.preventDefault();

            var companyId = parseInt($('input[name="company_id"]').val()) || 0;

            if (companyId > 0) {
                var i = $('#table_extra tbody tr').length; 

                //Add to temp
                var newRowContent = '<tr>' +
                    '<input type="hidden" name="pro_invdetail_id[' + i + ']" value="0">' +
                    '<input type="hidden" name="status[' + i + ']" value="1" class="class_status">' + 
                    '<td><input type="text" name="item_desc[' + i + ']" data-index="' + i + '" value="" class="form-control text-left input-sm   " >' +
                    '</td>' +
                    '<td ><input type="text" name="item_qty[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency num_cal" ></td>' +
                    '<td ><input type="text" name="item_rate[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency num_cal" ></td>' +
                    '<td ><input type="text" name="item_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                    '<td ><input type="text" name="tax_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency num_cal_tax" ></td>' +
                    '<td ><input type="text" name="subtotal_amount[' + i + ']" data-index="' + i + '" value="0" class="form-control text-right input-sm mask_currency" readonly></td>' +
                    '<td style="vertical-align:middle;">' +
                    '<a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' + i + '" style="margin-right:0px;"><i class="fa fa-times"></i></a></td>' +
                    '</tr>';

                $('#table_extra tbody').append(newRowContent);
                handleMask();
            } else {
                toastr["warning"]("Please select Company.", "Warning");
            }
        });

        $('.select_item').live('change', function(){
            var item_id = parseInt($(this).val()) || 0;
            var i = $(this).attr('data-index');
            if (item_id > 0) {
                var price_lock = parseInt($('option:selected', this).attr('data-price-lock')) || 0;
                var unit_price = parseFloat($('option:selected', this).attr('data-unit-price')) || 0;
                var unit_discount = parseFloat($('option:selected', this).attr('data-unit-discount')) || 0;
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
            } else {
                $('input[name="item_qty[' + i + ']"]').val('0');
                $('input[name="item_rate[' + i + ']"]').val('0');
                $('input[name="item_amount[' + i + ']"]').val('0');
                $('input[name="tax_amount[' + i + ']"]').val('0');
                $('input[name="subtotal_amount[' + i + ']"]').val('0');

                $('input[name="item_qty[' + i + ']"]').attr('readonly');
                $('input[name="item_rate[' + i + ']"]').attr('readonly');
            }

            calculate_row(i);
        });

        $('.num_cal').live('keyup', function(){
            var i = $(this).attr('data-index');

            calculate_row(i);
        });
		$('.num_cal_tax').live('keyup', function(){
            var i = $(this).attr('data-index');

            calculate_row_tax(i);
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
		
        function calculate_row_tax(i) {
            var qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;
            var price = parseFloat($('input[name="item_rate[' + i + ']"]').val()) || 0;

            var amount = parseFloat((qty * price).toFixed(2)) || 0;

            var tax_rate = parseFloat($('input[name="taxtype_vat"]').val()) || 0;
            
			
            var tax_amount_val = parseFloat($('input[name="tax_amount[' + i + ']"]').val()) || 0;
			if(tax_amount_val != 0)
			{
				var tax_amount = tax_rate * amount;
				var subtotal = amount + tax_amount;
			}
			else
			{
				var tax_amount = 0;
				var subtotal = amount;
			}
				

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
                toastr["warning"]("Company  not found.", "Warning!");
                $('input[name="company_name"]').closest('.form-group').addClass('has-error');
                result = false;
            }

            var i = 0;
            var i_act = 0;
            var item_array = new Array();
            $('#table_extra > tbody > tr ').each(function() {
                if(!$(this).hasClass('hide')) { 
                    var item_desc = $('input[name="item_desc[' + i + ']"]').val();
                    var item_qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;
                    var item_rate = parseFloat($('input[name="item_rate[' + i + ']"]').val()) || 0;
                    var item_amount = parseFloat($('input[name="item_amount[' + i + ']"]').val()) || 0;
                    var item_discount = 0;
                    var item_tax = parseFloat($('input[name="tax_amount[' + i + ']"]').val()) || 0;

                    if (item_array.indexOf(i, 0) > -1) {
                        toastr["warning"]("Item is duplicate.", "Warning");
                        result = false;
                    } else {
                        item_array.push(i);
                    }
 
                    $('select[name="item_desc[' + i + ']"]').removeClass('has-error');
                    $('input[name="item_qty[' + i + ']"]').removeClass('has-error');
                    $('input[name="item_rate[' + i + ']"]').removeClass('has-error');
                    $('input[name="item_amount[' + i + ']"]').removeClass('has-error');
                    $('input[name="tax_amount[' + i + ']"]').removeClass('has-error');

                   
                    if(item_desc == ''){
                        toastr["warning"]("Please input Item Desc.", "Warning");
                        $('input[name="item_desc[' + i + ']"]').addClass('has-error');
                        result = false;
                    }

                    if(item_qty <= 0){
                        toastr["warning"]("Please input Item Qty.", "Warning");
                        $('input[name="item_qty[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    if(item_rate == 0){
                        toastr["warning"]("Please input Item Price.", "Warning");
                        $('input[name="item_rate[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    if(item_amount == 0){
                        toastr["warning"]("Please input valid amount.", "Warning");
                        $('input[name="item_amount[' + i + ']"]').addClass('has-error');
                        result = false;
                    }

                    i_act++;
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
                    url: "<?php echo base_url('ar/proforma_inv/ajax_proforma_submit');?>",
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
                        toastr["error"]("Submit data failed, please try again later...", "Error");
                    });

            }
            else {
                $('#form-entry').unblock();
            }
        });

        $('#btn-approve').click(function(e) {
            e.preventDefault();

            bootbox.confirm("Are you sure want to Approve this Proforma Invoice ?", function (result) {
                if (result == true) {
                    Metronic.blockUI({
                        target: '.form-entry',
                        boxed: true,
                        message: 'Processing...'
                    });

                    //var form_data = $('#form-entry').serializeArray();
                    var pro_inv_id = $('input[name="pro_inv_id"]').val();

                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('ar/proforma_inv/ajax_inv_single_posting');?>",
                        dataType: "json",
                        data: {pro_inv_id : pro_inv_id}
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
                                toastr["error"]("Approve failed, please try again later.", "Error");
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