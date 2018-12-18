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
                $back_url = base_url('ar/corporate_bill/credit/1.tpd');

                if($creditnote_id > 0){
                    if($row->status != STATUS_NEW){
                        $form_mode = 'disabled';
                    }

                    if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                        $back_url = base_url('ar/corporate_bill/credit/2.tpd');
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
							<i class="fa fa-user"></i><?php echo ($creditnote_id > 0 ? '' : 'New');?> Credit Note
						</div>
						<div class="actions">
                            <?php
                            if($creditnote_id > 0){
                                if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED ){
                            ?>
                                <a href="<?php echo base_url('ar/credit/pdf_creditnote/'. ($creditnote_id > 0 ? $row->creditnote_id : '0') .'/7.tpd');?>" class="btn default btn-circle purple-studio" target="_blank"><i class="fa fa-print"></i> Credit Note</a>
                                <a href="<?php echo base_url('ar/credit/pdf_creditvoucher/'. ($creditnote_id > 0 ? $row->creditnote_id : '0') .'/7.tpd');?>" class="btn default btn-circle purple-studio" target="_blank"><i class="fa fa-print"></i> Voucher</a>
                            <?php
                                }
                            }
                            ?>
                            <a href="<?php echo (isset($row) ? $back_url : base_url('ar/corporate_bill/credit/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="#" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="creditnote_id" name="creditnote_id" value="<?php echo $creditnote_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn btn-sm green btn-circle" name="save"><i class="fa fa-save"></i>&nbsp;Save</button>
										<!-- button type="button" class="btn btn-sm blue-madison" name="save_close" >Save & Close</button -->

                                        <?php
                                        if($creditnote_id > 0){
                                            if($row->status == STATUS_NEW && $row->credit_amount > 0){
                                        ?>
                                                &nbsp;
                                                <button type="button" class="btn btn-sm blue btn-circle" id="posting-button" onclick="posting_record(<?php echo $creditnote_id; ?>);"><i class="fa fa-check"></i>&nbsp;Posting</button>
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
                                            <label class="control-label col-md-3">Date </label>
                                            <div class="col-md-4" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="credit_date" value="<?php echo ($creditnote_id > 0 ? dmy_from_db($row->credit_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Credit Note No</label>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="credit_no" value="<?php echo ($creditnote_id > 0 ? $row->credit_no : 'NEW');?>" disabled />
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Company</label>
                                            <div class="col-md-9">
                                                <div class="input-group">
                                                    <input type="hidden" name="company_id" value="<?php echo ($creditnote_id > 0 ? $row->company_id : '');?>">
                                                    <input type="hidden" name="reservation_id" value="0">
                                                    <input type="text" class="form-control" name="company_name" value="<?php echo ($creditnote_id > 0 ? ($row->company_id > 0 ? $row->company_name : $row->tenant_fullname) : '');?>" readonly />
                                                     <span class="input-group-btn">
                                                       <a id="btn_lookup_company" class="btn btn-success" href="javascript:;" >
                                                           <i class="fa fa-arrow-up fa-fw"></i>
                                                       </a>
                                                     </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Remark</label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="sub_credit_total">
                                                <textarea name="credit_remark" rows="2" class="form-control" style="resize: vertical;"><?php echo ($creditnote_id > 0 ? $row->credit_remark : ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <table class="table table-striped table-bordered table-hover" id="table_pending_detail">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th class="text-center" style="width:11%;">
                                                    Inv No
                                                </th>
                                                <th class="text-left" >
                                                    Description
                                                </th>
                                                <th class="text-right" style="width:18%;">
                                                    Pending Amount
                                                </th>
                                                <th class="text-right" style="width:18%;">
                                                    Credit Amount
                                                </th>
                                                <th class="text-center" style="width:10%;">

                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if($creditnote_id > 0){
                                                    foreach($detail->result_array() as $bill){
                                                        echo '<tr id="parent_' . $bill['invdetail_id'] . ($bill['is_tax'] > 0 ? '_tax' : '') .'">
                                                                <td style="vertical-align:middle;" class="text-center">
                                                                    <input type="hidden" name="cn_detail_id[]" value="' . $bill['cn_detail_id'] . '">
                                                                    <input type="hidden" name="invdetail_id[]" value="' . $bill['invdetail_id'] . '">
                                                                    <input type="hidden" name="transtype_id[]" value="">
                                                                    <input type="hidden" name="is_tax[]" value="' . $bill['is_tax'] . '">
                                                                    <span class="text-center">' . $bill['inv_no'] . '</span>
                                                                </td>
                                                                <td style="vertical-align:middle;">
                                                                    <span class="text-center">' . ($bill['is_tax'] > 0 ? '(VAT) ' : '') . $bill['description'] . '</span>
                                                                </td>
                                                                <td style="vertical-align:middle;" class="control-label">
                                                                    <input type="text" name="base_amount[]" value="' . $bill['base_amount'] .'" class="form-control text-right mask_currency" readonly>
                                                                </td>
                                                                <td style="vertical-align:middle;" class="control-label">
                                                                    <input type="text" name="credit_amount[]" value="' . $bill['credit_amount'] .'" class="form-control text-right mask_currency" >
                                                                </td>
                                                                <td style="vertical-align:middle;">
                                                                    <a inv-detail-id="' . $bill['invdetail_id'] .'" inv-is-tax="' . $bill['is_tax'] .'" data-placement="top" data-container="body" class="btn btn-xs green-meadow add_amount " href="javascript:;"><i class="fa fa-plus add_amount_plus hide"></i><i class="fa fa-minus add_amount_minus "></i>
                                                                    </a>
                                                                    <a inv-detail-id="' . $bill['invdetail_id'] .'" inv-is-tax="' . $bill['is_tax'] .'" data-placement="top" data-container="body" class="btn btn-xs purple-plum add_sub_detail " href="javascript:;"><i class="fa fa-share-alt"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>';

                                                        $subs = $this->db->query('SELECT gl_coa.coa_desc, sub.*, det.is_tax FROM ar_creditnote_detail_sub sub
                                                                          JOIN ar_creditnote_detail det ON det.cn_detail_id = sub.cn_detail_id
                                                                          JOIN gl_coa ON gl_coa.coa_code = sub.coa_code
                                                                          WHERE sub.cn_detail_id = ' . $bill['cn_detail_id']);
                                                        if($subs->num_rows() > 0){
                                                            echo '<tr id="sub_row_' . $bill['invdetail_id'] . ($bill['is_tax'] > 0 ? '_tax' : '') . '" style="background-color:#E1F4FA;"><td colspan="6">' .
                                                                '<div class=col-md-12">' .
                                                                '<div class="col-md-1 font-blue"><i class="fa fa-level-up fa-2x fa-rotate-90 font-blue-dark" style="margin-top: 10px;"></i></div>' .
                                                                '<div class="col-md-11">' .
                                                                '<table class="table table-striped table-bordered table-hover sub_table_detail table-po-detail" id="table_sub_' . $bill['invdetail_id'] . ($bill['is_tax'] > 0 ? '_tax' : '') . '">' .
                                                                '<thead>' .
                                                                '<tr role="row" class="heading">' .
                                                                '<th style="width:20%;" class="text-center">COA</th>' .
                                                                '<th class="text-left" >Description</th>' .
                                                                '<th class="text-right" style="width:25%;">Credit</th>' .
                                                                '<th style="width:6%;"></th>' .
                                                                '</tr>' .
                                                                '</thead>' .
                                                                '<tbody>' ;

                                                            foreach($subs->result_array() as $sub){
                                                                echo '<tr>' .
                                                                    '<td class="text-center" style="vertical-align:middle;"><input type="hidden" name="detail_id[]" value="' . $sub['sub_detail_id'] . '"><input type="hidden" name="unique_id[]" value="">' .
                                                                    '<div class="input-group">' .
                                                                    '<input type="hidden" name="sub_invdetail_id[]" value="' . $bill['invdetail_id'] . '" />' .
                                                                    '<input type="hidden" name="sub_coa_id[]" value="' . $sub['coa_id'] .'" />' .
                                                                    '<input type="hidden" name="sub_is_tax[]" value="' . $bill['is_tax'] . '" />
                                                                    <input type="text" name="sub_coa_code[]" class="form-control input-sm text-center" value="' . $sub['coa_code'] . '" readonly />' .
                                                                    '<span class="input-group-btn">' .
                                                                    '<button class="btn btn-sm green-haze find_coa" unique-id="" inv-is-tax="' . $bill['is_tax'] . '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' .
                                                                    '</span>' .
                                                                    '</div>' .
                                                                    '</td>' .
                                                                    '<td style="vertical-align:middle;"><input type="text" name="sub_coa_desc[]" value="' . $sub['coa_desc'] . '" class="form-control" readonly></td>' .
                                                                    '<td ><input type="text" name="sub_credit_value[]" value="' . $sub['credit_amount'] . '" class="form-control text-right mask_currency sub_credit_change"></td>' .
                                                                    '<td style="vertical-align:middle;"><a class="btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" onclick="delete_record(' . $sub['sub_detail_id'] . ',\'\',\'' . $bill['invdetail_id'] . ($bill['is_tax'] > 0 ? '_tax' : '') . '\');"><i class="fa fa-times"></i></a></td>' .
                                                                    '</tr>';
                                                            }

                                                            echo '</tbody>' .
                                                                '<tfoot>' .
                                                                '<tr>' .
                                                                '<td><input type="hidden" name="parent_invdetail_id[]" value="' . $bill['invdetail_id'] . ($bill['is_tax'] > 0 ? '_tax' : '') . '">' .
                                                                '<a class="btn btn-sm green-haze yellow-stripe add_sub_coa" inv-detail-id="' . $bill['invdetail_id'] . '" inv-is-tax="' . $bill['is_tax'] .'"><i class="fa fa-plus"></i><span> &nbsp;&nbsp;Add Detail </span></a>' .
                                                                '</td><td ></td>' .
                                                                '<td ></td>' .
                                                                '<td ></td>' .
                                                                '</tr>' .
                                                                '</tfoot>' .
                                                                '</table>' .
                                                                '</div>' .
                                                                '</div>' .
                                                                '</td></tr>';
                                                        }
                                                    }
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-4 bold">Total Credit </label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control mask_currency bold font-red-sunglo " name="grandtotal_credit" value="<?php echo ($creditnote_id > 0 ? $row->credit_amount : '0');?>" readonly />
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
                if($creditnote_id > 0){
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
                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                    <input type="text" class="form-control" name="c_posting_date" value="<?php echo (date('d-m-Y'));?>" readonly>
					<span class="input-group-btn">
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

<script>
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;

    $(document).ready(function(){

        <?php echo picker_input_date() ;?>

        if(isedit > 0){
            $('#form-entry').block({
                message: null ,
                overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
            });
        }

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
            $.validator.addMethod("checkCredit", function (value, element)
                {
                    var valid = true;
                    try{
                        var counter = $('#table_pending_detail > tbody > tr ').length;
                        if(counter > 0){
                            $('#table_pending_detail > tbody > tr ').each(function() {
                                var baseAmount = parseFloat($(this).find('input[name*="base_amount"]').val()) || 0;
                                var creditAmount = parseFloat($(this).find('input[name*="credit_amount"]').val()) || 0;

                                if(creditAmount > 0){
                                    if(creditAmount > baseAmount){
                                        toastr["error"]("Credit must not bigger than Pending amount", "Warning");
                                        valid = false;
                                        return;
                                    }
                                }

                                if(valid){
                                    var invdetail_id = parseInt($(this).find('input[name*="invdetail_id"]').val()) || 0;
                                    if(invdetail_id > 0){
                                        var is_tax = parseInt($(this).find('input[name*="is_tax"]').val()) || 0;
                                        if(is_tax > 0)
                                            invdetail_id = invdetail_id + '_tax';
                                        var tableId = '#table_sub_' + invdetail_id;
                                        var rowCount = $(tableId + ' > tbody > tr ').length;

                                        if(rowCount > 0){
                                            if(valid){
                                                $(tableId + ' > tbody > tr ').each(function() {
                                                    var col2 = $(this).find('td:nth-child(1)');
                                                    var coa_id = parseFloat(col2.find('input[name*="sub_coa_id"]').val()) || 0;

                                                    if(coa_id <= 0){
                                                        toastr["error"]("Substitute detail COA must be selected.", "Warning");
                                                        valid = false;
                                                        return;
                                                    }
                                                });

                                                var amount = getAvailableCredit(invdetail_id);
                                                if(amount != 0){
                                                    valid = false;
                                                    toastr["error"]("Substitute detail total must equal with credited amount.", "Warning");
                                                    return;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }else{
                            valid = false;
                        }

                        if(valid){
                            var gtotal = parseFloat($('input[name="grandtotal_credit"]').val()) || 0;

                            if(gtotal <= 0){
                                valid = false;
                                toastr["error"]("Total Credit must not empty.", "Warning");
                            }
                        }
                    }catch(e){
                        console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Substitute credit total must equal parent Credit Amount."
            )

            $.validator.addMethod("validateID", function (value, element)
                {
                    var valid = true;
                    try{
                        var company_id = parseInt($('input[name="company_id"]').val()) || 0;
                        var reservation_id = parseInt($('input[name="reservation_id"]').val()) || 0;
                        if(company_id <= 0 && reservation_id <= 0){
                            valid = false;
                            toastr["error"]("Please choose company or guest.", "Warning");
                        }
                    }catch(e){
                        console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Please choose company or guest."
            )

            var form1 = $('#form-entry');
            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    credit_date: {
                        required: true
                    },
                    company_id:{
                        validateID: true
                    },
                    credit_remark: {
                        required: true
                    },
                    grandtotal_credit:{
                        required: true,
                        min: 1
                    },
                    sub_credit_total:{
                        checkCredit:true
                    }
                },
                messages: {
                    credit_date: "Date must be selected",
                    credit_remark: "Remark must not empty",
                    grandtotal_credit: "Credit Amount must not 0.",
                    sub_credit_total:"Substitute COA must not empty"
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.credit_date != null){
                        toastr["error"](validator.invalid.credit_date, "Warning");
                    }
                    if(validator.invalid.credit_remark != null){
                        toastr["error"](validator.invalid.credit_remark, "Warning");
                    }
                    if(validator.invalid.grandtotal_credit != null){
                        toastr["error"](validator.invalid.grandtotal_credit, "Warning");
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
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

        handleValidation();

        var handleMask = function() {
            $(".mask_currency").inputmask("numeric",{
                radixPoint:".",
                autoGroup: true,
                groupSeparator: ",",
                digits: 0,
                groupSize: 3,
                //removeMaskOnSubmit: true,
                autoUnmask: true
            });
        }

        handleMask();

        //Company
        var grid_company = new Datatable();
        //COA
        var handleTableCompany = function (num_index, company_id, creditnote_id, reservation_id) {
            // Start Datatable Item
            grid_company.init({
                src: $("#datatable_reservation"),
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        null,
                        { "sWidth" : '12%' ,"sClass": "text-right", "bSortable": false},
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '10%' }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, -1],
                        [10, 20, 50, 100, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('ar/credit/get_corp_modal_bill');?>/" + num_index + "/" + company_id + "/" + creditnote_id + "/" + reservation_id// ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_reservation_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('#btn_lookup_company').on('click', function(){
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
                $modal.load('<?php echo base_url('ar/credit/ajax_corp_pending_bill');?>.tpd', '', function () {
                    $modal.modal();

                    var creditnote_id = $('input[name="creditnote_id"]').val();
                    var company_id = $('input[name="company_id"]').val();
                    var reservation_id = $('input[name="reservation_id"]').val();
                    handleTableCompany(num_index, company_id, creditnote_id, reservation_id);

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

            var company_id = parseInt($(this).attr('data-company-id')) || 0;
            var reservation_id = parseInt($(this).attr('data-reservation-id')) || 0;
            var company_name = $(this).attr('data-company-name');
            var amount = parseFloat($(this).attr('data-amount')) || 0;

            $('input[name="company_id"]').val(company_id);
            $('input[name="reservation_id"]').val(reservation_id);
            $('input[name="company_name"]').val(company_name);

            $('.add_amount').removeClass('hide');
            $('input[name*="credit_amount"]').prop('readonly',false);

            //Looking Reservation Detail
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('ar/credit/xcorp_pending_bill_detail');?>",
                data: { company_id : company_id, reservation_id : reservation_id},
                async:false
            })
                .done(function( msg ) {
                    $('#table_pending_detail > tbody').html(msg);
                });

            handleMask();

            $('#ajax-modal').modal('hide');
        });

        $('.add_amount').live('click', function(e){
            e.preventDefault();

            var tr = $(this).closest('tr');
            var credit = tr.find('input[name*="credit_amount"]').val();
            var base_amount = tr.find('input[name*="base_amount"]').val();

            if(credit <= 0){
                tr.find('input[name*="credit_amount"]').val(base_amount);
                show_plus_button(false, tr);
            }else{
                tr.find('input[name*="credit_amount"]').val(0);
                show_plus_button(true, tr);
            }

            handleMask();
        });

        function show_plus_button(valid, parent_tr){
            if(valid){
                parent_tr.find('.add_amount_minus').addClass('hide');
                parent_tr.find('.add_amount_plus').removeClass('hide');
                parent_tr.find('.add_sub_detail').addClass('hide');

                //Remove sub_row_#ID
                var invdetail_id = parent_tr.find('input[name*="invdetail_id"]').val();
                var is_tax = parseInt(parent_tr.find('input[name*="is_tax"]').val()) || 0;
                var body = parent_tr.closest('tbody');
                if(is_tax > 0)
                    invdetail_id = invdetail_id + '_tax';
                body.find('#sub_row_' + invdetail_id).remove();
            }else{
                parent_tr.find('.add_amount_plus').addClass('hide');
                parent_tr.find('.add_amount_minus').removeClass('hide');
                parent_tr.find('.add_sub_detail').removeClass('hide');
            }

            calculateCredit();
        }

        $('.add_sub_detail').live('click', function(e){
            //e.preventDefault();
            var invdetail_id = $(this).attr('inv-detail-id');
            var is_tax = parseInt($(this).attr('inv-is-tax')) || 0;
            var str_tax = '';
            if(is_tax > 0)
                str_tax = '_tax';

            var rowid = "sub_row_" + invdetail_id;

            if($("#" + rowid).length <= 0) {
                var newSubRowTable = '<tr id="' + rowid + str_tax + '" style="background-color:#E1F4FA;"><td colspan="6">' +
                    '<div class=col-md-12">' +
                    '<div class="col-md-1 font-blue"><i class="fa fa-level-up fa-2x fa-rotate-90 font-blue-dark" style="margin-top: 10px;"></i></div>' +
                    '<div class="col-md-11">' +
                    '<table class="table table-striped table-bordered table-hover sub_table_detail table-po-detail" id="table_sub_' + invdetail_id + str_tax + '">' +
                    '<thead>' +
                    '<tr role="row" class="heading">' +
                    '<th style="width:20%;" class="text-center">COA</th>' +
                    '<th class="text-left" >Description</th>' +
                    '<th class="text-right" style="width:25%;">Credit</th>' +
                    '<th style="width:6%;"></th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>' +
                    '</tbody>' +
                    '<tfoot>' +
                    '<tr>' +
                    '<td><input type="hidden" name="parent_invdetail_id[]" value="' + invdetail_id + str_tax + '">' +
                    '<a class="btn btn-sm green-haze yellow-stripe add_sub_coa" inv-detail-id="' + invdetail_id + '" inv-is-tax="' + is_tax + '"><i class="fa fa-plus"></i><span> &nbsp;&nbsp;Add Detail </span></a>' +
                    '</td><td ></td>' +
                    '<td ></td>' +
                    '<td ></td>' +
                    '</tr>' +
                    '</tfoot>' +
                    '</table>' +
                    '</div>' +
                    '</div>' +
                    '</td></tr>';

                var tr = $(this).closest('tr');
                tr.after(newSubRowTable);
            }
        });

        $('input[name="credit_amount[]"]').live('keyup', function(e){
            e.preventDefault();
            var baseAmount = parseFloat($(this).closest('tr').find('input[name*="base_amount"]').val()) || 0;
            var val = parseFloat($(this).val()) || 0;

            var tr = $(this).closest('tr');
            if(val > 0){
                if(val > baseAmount){
                    $(this).val(baseAmount);
                }
                //$('.add_amount').removeClass('hide');
                show_plus_button(false,tr);
            }else{
                //$('.add_amount').addClass('hide');
                show_plus_button(true,tr);
            }
        });

        $('.add_sub_coa').live('click', function(e){
            e.preventDefault();

            var invdetailId = $(this).attr('inv-detail-id');
            var is_tax = parseInt($(this).attr('inv-is-tax')) || 0;
            var str_tax = '';
            if(is_tax > 0){
                str_tax = '_tax';
            }

            var date = new Date();
            var uniqueId = (date.toLocaleString(['ban', 'id'])).replace(/([.*+?^=!:${}()|\[\]\/\\])/g,'');
            //var randomStr = (Math.floor(Math.random() * 8999 + 1000)).toString().replace(/([.*+?^=!:${}()|\[\]\/\\])/g,'');
            uniqueId = uniqueId.replace(/ /g,''); //+ randomStr;

            //Add to temp
            var newRowContent = '<tr>' +
                '<td class="text-center" style="vertical-align:middle;"><input type="hidden" name="detail_id[]" value="0"><input type="hidden" name="unique_id[]" value="' + uniqueId + '">' +
                '<div class="input-group">' +
                '<input type="hidden" name="sub_invdetail_id[]" value="' + invdetailId + '" />' +
                '<input type="hidden" name="sub_coa_id[]" value="0" />' +
                '<input type="hidden" name="sub_is_tax[]" value="' + is_tax + '" />' +
                '<input type="text" name="sub_coa_code[]" class="form-control input-sm text-center" value="" readonly />' +
                '<span class="input-group-btn">' +
                '<button class="btn btn-sm green-haze find_coa" unique-id="' + uniqueId + '" inv-is-tax="' + is_tax + '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                '</span>' +
                '</div>' +
                '</td>' +
                '<td style="vertical-align:middle;"><input type="text" name="sub_coa_desc[]" value="" class="form-control" readonly></td>' +
                '<td ><input type="text" name="sub_credit_value[]" value="0" class="form-control text-right mask_currency sub_credit_change"></td>' +
                '<td style="vertical-align:middle;"><a class="btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" onclick="delete_record(0,\'' + uniqueId + '\',\'' + invdetailId + str_tax + '\');"><i class="fa fa-times"></i></a></td>' +
                '</tr>';

            //$('#table_credit_detail tbody').append(newRowContent);
            var tbody = $(this).closest('table').find('tbody');
            tbody.append(newRowContent);

            $('select.select2me').select2();

            handleMask();
        });

        var grid_coa = new Datatable();
        var handleTableCOA = function (coaId, detailId, uniqueId, invDetailId, strTax)   {
            var coa_id_exist = '-';
            var n = 0;
            $('#table_sub_' + invDetailId + strTax + ' tbody tr').each(function () {
                if ($(this).hasClass('hide') == false) {
                    var coa_id = parseInt($(this).find('input[name*="sub_coa_id"]').val()) || 0;
                    if (coa_id > 0) {
                        if(coa_id_exist == '-'){
                            coa_id_exist = '';
                        }
                        if(n > 0) {
                            coa_id_exist += '_';
                        }

                        coa_id_exist += coa_id;
                        n++;
                    }
                }
            });

            grid_coa.init({
                src: $("#datatable_coa"),
                onSuccess: function (grid_coa) {
                    // execute some code after table records loaded
                },
                onError: function (grid_coa) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_coa) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "bSortable": true, "sWidth" : '15%' },
                        null,
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center", "sWidth" : '6%' },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('general/modalbook/get_coa_list_by_id');?>/" + coaId + '/' + detailId + '/' + uniqueId + '/' + invDetailId + '/' + coa_id_exist + '/-/' + strTax
                    }
                }
            });

            var tableWrapper = $("#datatable_coa_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('.find_coa').live('click', function(e){
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            var coa_id = parseInt($(this).closest('td').find('input[name*="sub_coa_id"]').val()) || 0;
            var invdetail_id = parseInt($(this).closest('td').find('input[name*="sub_invdetail_id"]').val()) || 0;
            var is_tax = parseInt($(this).attr('inv-is-tax')) || 0;
            var detail_id = parseInt($(this).closest('td').find('input[name*="detail_id"]').val()) || 0;
            var unique_id = parseInt($(this).closest('td').find('input[name*="unique_id"]').val()) || 0;

            var str_tax = '';
            if(is_tax > 0)
                str_tax = '_tax';

            $('body').modalmanager('loading');

            var uninitialized = $('#datatable_coa').filter(function() {
                return !$.fn.DataTable.fnIsDataTable(this);
            });

            var $modal = $('#ajax-modal');

            setTimeout(function(){
                $modal.load('<?php echo base_url('general/modalbook/ajax_coa_by_id');?>.tpd', '', function(){
                    handleTableCOA(coa_id, detail_id, unique_id, invdetail_id, str_tax);

                    $modal.modal();

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if($modal.hasClass('modal-overflow') === false){
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});
                });
            }, 150);
        })

        $('.btn-select-coa').live('click', function (e) {
            e.preventDefault();

            var coa_id = parseInt($(this).attr('coa-id')) || 0;
            var coa_code = $(this).attr('coa-code');
            var coa_desc = $(this).attr('coa-desc');

            var detail_id = parseInt($(this).attr('detail-id')) || 0;
            var unique_id = $(this).attr('unique-id');
            var invdetail_id = $(this).attr('parent-row-id');
            var is_tax = parseInt($(this).attr('parent-is-tax')) || 0;

            var str_tax = '';
            if(is_tax > 0){
                str_tax = '_tax';
            }

            var tableId = '#table_sub_' + invdetail_id + str_tax;
            var credit = getAvailableCredit(invdetail_id + str_tax);
            if(detail_id > 0){
                var closest_td = $(tableId + ' > tbody > tr').find('input[name*="detail_id"][value="' + detail_id + '"]').closest('td');
                closest_td.find('input[name*="sub_coa_id"]').val(coa_id);
                closest_td.find('input[name*="sub_coa_code"]').val(coa_code);
                closest_td.closest('tr').find('input[name*="sub_coa_desc"]').val(coa_desc);
                closest_td.closest('tr').find('input[name*="sub_credit_value"]').val(credit);
            }else{
                var closest_td = $(tableId + ' > tbody > tr').find('input[name*="unique_id"][value="' + unique_id + '"]').closest('td');
                closest_td.find('input[name*="sub_coa_id"]').val(coa_id);
                closest_td.find('input[name*="sub_coa_code"]').val(coa_code);
                closest_td.closest('tr').find('input[name*="sub_coa_desc"]').val(coa_desc);
                closest_td.closest('tr').find('input[name*="sub_credit_value"]').val(credit);
            }

            calculateCredit();

            $('#ajax-modal').modal('hide');
        });

        $('.sub_credit_change').live('keyup', function(e){
            var val = parseFloat($(this).val()) || 0;

            //calculateCredit();
        });

        $('button[name="save"]').click(function(e){
            e.preventDefault();

            if($("#form-entry").valid()){
                var url = '<?php echo base_url('ar/credit/submit_corp_credit.tpd');?>';
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
                //$('#form-entry').submit();
            }
        });

        $('button[name="save_close"]').click(function(e){
            e.preventDefault();

            if($("#form-entry").valid()){
                var url = '<?php echo base_url('ar/credit/submit_corp_credit.tpd');?>';
                $("#form-entry").append('<input type="hidden" name="save_close" value="">');
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
            }
        });

        $('#submit-posting').click(function(e) {
            e.preventDefault();

            $('#ajax-posting').modal('hide');

            var creditnote_id = $('input[name="creditnote_id"]').val();
            var posting_date = $('input[name="c_posting_date"]').val();

            if(creditnote_id > 0){
                bootbox.confirm({
                    message: "Posting this transaction ?<br><strong>Please make sure any changes has been saved.</strong>",
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
                            //console.log(result);
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('ar/credit/xposting_corp_credit_by_id');?>",
                                dataType: "json",
                                data: { creditnote_id: creditnote_id, posting_date : posting_date}
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();

                                    if(msg.type == '1'){
                                        toastr["success"](msg.message, "Success");

                                        window.location.assign(msg.redirect_link);
                                    }
                                    else {
                                        toastr["warning"](msg.message, "Warning");
                                    }
                                });

                        }
                    }
                });
            }
        });
	});

    function getAvailableCredit(invDetailId)
    {
        var credit = 0;

        var parentRow = $('#parent_' + invDetailId);
        var parentCredit = parseFloat(parentRow.find('input[name*="credit_amount"]').val()) || 0;
        if(parentCredit > 0){
            var alloc = 0;
            $('#table_sub_' + invDetailId + ' > tbody > tr ').each(function() {
                var col2 = $(this).find('td:nth-child(3)');
                var credit_amount = parseFloat(col2.find('input[name*="sub_credit_value"]').val()) || 0;
                alloc += credit_amount;
            });
            credit = parentCredit - alloc;
        }

        return credit;
    }

    function calculateCredit(){
        var total = 0;
        $('#table_pending_detail > tbody > tr ').each(function() {
            var credit_amount = parseFloat($(this).find('input[name*="credit_amount"]').val()) || 0;
            total += credit_amount;
        });
        $('input[name="grandtotal_credit"]').val(total);

    }

    function delete_record(detailId, uniqueId, invdetailId){
        if(invdetailId != ''){
            if(detailId > 0 || uniqueId != ''){
                bootbox.confirm("Remove this substitute COA ?", function (result) {
                    if (result == true) {
                        if(detailId > 0){
                            Metronic.blockUI({
                                target: '.modal-content-edit',
                                boxed: true,
                                message: 'Processing...'
                            });

                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('ar/credit/delete_credit_detail_sub');?>",
                                dataType: "json",
                                data: {sub_detail_id : detailId}
                            })
                                .done(function( msg ) {
                                    if(msg.type != '0'){

                                    }
                                    else {
                                        console.log('ajax delete unit.' + detailId);
                                    }

                                    delete_frontend_payment(detailId, '', invdetailId);
                                    Metronic.unblockUI('.modal-content-edit');
                                });
                        }else{
                            delete_frontend_payment(0, uniqueId, invdetailId);
                        }
                    }
                });
            }
        }

    }

    function delete_frontend_payment(detailId,uniqueId, invdetailId){
        try{
            if(detailId > 0){
                $('#table_sub_' + invdetailId + ' > tbody > tr').find('input[name*="detail_id"][value="' + detailId + '"]').parent().parent().remove();
            }else{
                $('#table_sub_' + invdetailId + ' > tbody > tr').find('input[name*="unique_id"][value="' + uniqueId + '"]').parent().parent().remove();
            }

            calculateCredit();
        }catch(e){
            console.log(e);
        }
    }

    function posting_record(creditnoteId){
        var $modal_cal = $('#ajax-posting');

        /*
        if ($modal_cal.hasClass('bootbox') == false) {
            $modal_cal.addClass('modal-fix');
        }

        if ($modal_cal.hasClass('modal-overflow') === false) {
            $modal_cal.addClass('modal-overflow');
        }
        */
        if($("#form-entry").valid()){
            $modal_cal.modal();
        }else{
            toastr["error"]("Please save your changes first !", "Warning");
        }
     }

</script>