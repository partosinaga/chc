<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();
if($payment_id > 0) {
    if($row->status == STATUS_NEW){
        if (check_session_action(get_menu_id(), STATUS_EDIT)) {
            $btn_action .= $btn_save;
        } else {
            $isedit = false;
        }
        if (check_session_action(get_menu_id(), STATUS_POSTED)) {
            $btn_action .= btn_action($payment_id, $row->payment_code, STATUS_POSTED);
        }
        if (check_session_action(get_menu_id(), STATUS_CANCEL)) {
            $btn_action .= btn_action($payment_id, $row->payment_code, STATUS_CANCEL);
        }
        if(check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(base_url('ap/payment/pdf_payment/' . $payment_id . '.tpd'));
        }
    } else {
        $isedit = false;
        if ($row->status == STATUS_POSTED) {
            if(check_session_action(get_menu_id(), STATUS_PRINT)) {
                $btn_action .= btn_print(base_url('ap/payment/pdf_payment/' . $payment_id . '.tpd'));
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
					foreach($breadcrumbs as $breadcrumb){
						echo $breadcrumb;
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
							<i class="fa fa-user"></i><?php echo ($payment_id > 0 ? 'View' : 'New');?> Payment
                            <?php
                            if($payment_id > 0){
                                echo '&nbsp;&nbsp;&nbsp;' . get_status_name($row->status);
                            }
                            ?>
						</div>
						<div class="actions">
                            <?php
                            $back_url = base_url('ap/payment/payment_manage.tpd');
                            if ($payment_id > 0) {
                                if ($row->status != STATUS_NEW) {
                                    $back_url = base_url('ap/payment/payment_history.tpd');
                                }
                            }
                            echo btn_back($back_url);
                            ?>
						</div>
					</div>
					<div class="portlet-body form">
						<form method="post" id="id_form_input" class="form-horizontal" onsubmit="return false">
							<input type="hidden" name="payment_id" value="<?php echo $payment_id;?>" />
							<div class="form-actions top">
								<div class="row">
									<div class="col-md-9">
                                        <?php
                                            echo $btn_action;
                                        ?>
									</div>
								</div>
							</div>
							<div class="form-body" id="form-entry">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label col-md-4">Payment No</label>
											<div class="col-md-6">
												<input type="text" class="form-control" value="<?php echo ($payment_id > 0 ? $row->payment_code : '');?>" readonly />
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-md-4">Supplier</label>
											<div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="supplier_id" id="supplier_id" value="<?php echo ($payment_id > 0 ? $row->supplier_id : '0');?>" />
                                                    <input class="form-control" id="supplier_name" type="text" value="<?php echo ($payment_id > 0 ? $row->supplier_name : ''); ?>" readonly >
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_supplier" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Bank</label>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <input type="hidden" name="bankaccount_id" id="bankaccount_id" value="<?php echo ($payment_id > 0 ? $row->bank_account_id : '0');?>" />
                                                    <input class="form-control" id="bank_code" type="text" value="<?php echo ($payment_id > 0 ? $row->bank_code : ''); ?>" readonly >
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_bank" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Payment Dates</label>
                                            <div class="col-md-4">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="payment_date" id="payment_date" value="<?php echo ($payment_id > 0 ? ymd_to_dmy($row->payment_date) : date('d-m-Y'));?>" readonly>
													<span class="input-group-btn">
														<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Ref No</label>
                                            <div class="col-md-6">
                                                <input type="text" name="ref_no" class="form-control" value="<?php echo ($payment_id > 0 ? $row->ref_no : '');?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Curr / Rate</label>
                                            <div class="col-md-3">
                                                <input type="hidden" name="currencytype_id" id="currencytype_id" value="<?php echo ($payment_id > 0 ? $row->currencytype_id : 0);?>"/>
                                                <select name="currencytype_id_" id="currencytype_id_" class="select2me form-control" disabled>
                                                    <?php
                                                    foreach($qry_curr->result() as $row_curr){
                                                        echo '<option value="' . $row_curr->currencytype_id . '" ' . ($payment_id > 0 ? ($row->currencytype_id == $row_curr->currencytype_id ? 'selected="selected"' : '') : '') . '>' . $row_curr->currencytype_code . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="curr_rate" id="curr_rate" class="form-control mask_currency num_rate" value="<?php echo ($payment_id > 0 ? $row->curr_rate : '1');?>" />
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-10">
												<textarea class="form-control" rows="2" name="description"   ><?php echo ($payment_id > 0 ? $row->description : '');?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="row">
									<div class="col-md-12" style="margin-bottom: 10px;">
                                        <?php
                                            echo btn_add_detail();
                                        ?>
									</div>
									<div class="col-md-12">
										<table class="table table-striped table-hover table-bordered table-po-detail" id="datatable_detail">
											<thead>
												<tr>
													<th class="text-center"width="110px"> Inv Code </th>
                                                    <th class="text-center" width="80px"> Inv Date </th>
                                                    <th class="text-center" width="40px"> Curr </th>
                                                    <th class="text-center" width="110px"> Inv Amount </th>
                                                    <th class="text-center" width="60px"> Inv Rate </th>
													<th class="text-center" width="110px"> Inv Local Amount </th>
                                                    <th class="text-center" width="130px"> Pay. Amount </th>
                                                    <th class="text-center" width="130px"> Pay. Loc. Amount </th>
                                                    <th class="text-center" width="90px"> Tax </th>
                                                    <th class="text-center" width="60px"> Rate WHT </th>
                                                    <th class="text-center" width="100px"> Tax WHT </th>
                                                    <th class="text-center" width="30px">&nbsp;</th>
												</tr>
											</thead>
											<tbody>
                                            <?php
                                            $total = 0;
                                            $local_total = 0;
                                            if($payment_id > 0){
                                                $i = 0;
                                                foreach($qry_detail->result() as $row_detail){
                                                    $new_row = '<tr>' .
                                                        '<input data-index="' . $i . '" type="hidden" class="class_status" name="status[' . $i . ']" value="1"/>' .
                                                        '<input data-index="' . $i . '" type="hidden" name="detail_id[' . $i . ']" value="' . $row_detail->detail_id . '"/>' .
                                                        '<td>' .
                                                            '<div class="input-group">' .
                                                                '<input data-index="' . $i . '" type="hidden" name="inv_id[' . $i . ']" class="is_inv" value="' . $row_detail->inv_id . '" />' .
                                                                '<input data-index="' . $i . '" type="text" name="inv_code[' . $i . ']" class="form-control input-sm" value="' . $row_detail->inv_code . '" readonly />' .
                                                                '<span class="input-group-btn">' .
                                                                    '<button class="btn btn-sm green-haze load_invoice" data-index="' . $i . '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' .
                                                                '</span>' .
                                                            '</div>' .
                                                        '</td>' .
                                                        '<td style="padding-top:9px;" class="text-center"><span class="inv_date_' . $i . '">' . ymd_to_dmy($row_detail->inv_date) . '</span></td>' .
                                                        '<td style="padding-top:9px;" class="text-center"><span class="inv_curr_' . $i . '">' . $row_detail->currencytype_code . '</span></td>' .
                                                        '<td style="padding-top:9px;" class="text-right">' .
                                                            '<span class="mask_currency inv_amount_' . $i . '">' . $row_detail->inv_amount . '</span>' .
                                                            '<input type="hidden" name="inv_amount[' . $i . ']" value="' . $row_detail->inv_amount . '" />' .
                                                            '<input type="hidden" name="inv_vat[' . $i . ']" value="' . $row_detail->inv_vat . '" />' .
                                                        '</td>' .
                                                        '<td style="padding-top:9px;" class="text-right"><span class="mask_currency inv_rate_' . $i . '">' . $row_detail->curr_rate . '</span></td>' .
                                                        '<td style="padding-top:9px;" class="text-right"><span class="mask_currency inv_loc_amount_' . $i . '">' . ($row_detail->inv_amount * $row_detail->curr_rate) . '</span></td>' .
                                                        '<td><input type="text" data-index="' . $i . '" data-max="0" name="amount[' . $i . ']" value="' . $row_detail->amount . '" class="form-control input-sm mask_currency text-right amount_detail payment_amount"></td>' .
                                                        '<td><input type="text" readonly data-index="' . $i . '"  name="local_amount[' . $i . ']" value="' . $row_detail->local_amount . '" class="form-control text-right input-sm mask_currency amount_loc_detail"></td>' .
                                                        '<td>' .
                                                            '<div class="input-group">' .
                                                                '<input data-index="' . $i . '" type="hidden" name="taxtype_id[' . $i . ']" value="' . $row_detail->taxtype_id . '" />' .
                                                                '<input data-index="' . $i . '" type="hidden" name="tax_wht_percent[' . $i . ']" value="' . $row_detail->taxtype_wht . '" />' .
                                                                '<input data-index="' . $i . '" type="text" name="taxtype_code[' . $i . ']" class="form-control input-sm" value="' . $row_detail->taxtype_code . '" readonly />' .
                                                                '<span class="input-group-btn">' .
                                                                    '<button class="btn btn-sm green-haze load_tax" data-index="' . $i . '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' .
                                                                '</span>' .
                                                            '</div>' .
                                                        '</td>' .
                                                        '<td><input type="text" data-index="' . $i . '"  name="curr_rate_wht[' . $i . ']" value="' . $row_detail->curr_rate_wht . '" class="form-control text-right input-sm mask_currency curr_rate_wht" ' . ($row_detail->currencytype_code != Purchasing::CURR_IDR ? '' : 'readonly') . '></td>' .
                                                        '<td><input type="text" data-index="' . $i . '"  name="tax_wht[' . $i . ']" value="' . $row_detail->tax_wht . '" class="form-control text-right input-sm mask_currency amount_detail tax_wht" ' . ($row_detail->taxtype_wht > 0 ? '' : 'readonly') . ' /></td>' .
                                                        '<td class="text-center text-middle">' .
                                                        '<a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' . $i . '" style="margin-right:0px;margin-top:4px;"><i class="fa fa-times"></i></a>' .
                                                        '</td>' .
                                                        '</tr>';

                                                    echo $new_row;

                                                    $i++;

                                                    $total += $row_detail->amount;
                                                    $local_total += $row_detail->local_amount;
                                                }
                                            }
                                            ?>
											</tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="6" class="text-right" style="vertical-align: middle;">Total</th>
                                                    <th><input name="totalamount" type="text" class="form-control input-sm num_total text-right mask_currency" value="<?php echo $total;?>" readonly /></th>
                                                    <th><input type="text" class="form-control input-sm num_loc_total text-right mask_currency" value="<?php echo $local_total;?>" readonly /></th>
                                                    <th colspan="4">&nbsp;</th>
                                                </tr>
                                            </tfoot>
										</table>
									</div>
								</div>
							</div>

                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if($payment_id > 0){
                                        $log = '';
                                        $modified = '';
                                        $qry_log = $this->mdl_general->get('app_log', array('feature_id' => Feature::FEATURE_AP_PAYMENT, 'reff_id' => $payment_id), array(), 'log_id asc');
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

                                        if ($row->user_modified > 0) {
                                            $modified .= "<div class='col-md-4'><h6>Last Modified on " . date_format(new DateTime($row->date_modified), 'd/m/Y H:i:s') . " by " . get_user_fullname( $row->user_modified ) . "</h6></div>" ;
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

<div id="ajax-modal" class="modal fade" data-replace="true" data-width="1024" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>
<div id="ajax-modal-small" class="modal fade" data-replace="true" data-width="768" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>
<div class="modal fade bs-modal-sm" id="ajax-modal-xtra-small" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Posting Date</h4>
            </div>
            <div class="modal-body">
                <form role="form" onsubmit="return false">
                    <div class="form-group">
                        <label>Posting Date</label>
                        <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                            <input type="text" class="form-control" name="posting_date" id="posting_date" value="<?php echo date('d-m-Y'); /* ($payment_id > 0 ? ymd_to_dmy($row->payment_date) : date('d-m-Y'));*/?>" readonly>
                            <span class="input-group-btn">
                                <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn blue" id="btn_posting" data-dismiss="modal" data-id="<?php echo $payment_id;?>" data-action="<?php echo STATUS_POSTED;?>">Posting</button>
            </div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function(){
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "10000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        var $modal = $('#ajax-modal');
        var $modal_small = $('#ajax-modal-small');
        var $modal_xtra_small = $('#ajax-modal-xtra-small');

        <?php
        if($payment_id > 0){
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
            echo picker_input_date(true, true, ymd_to_dmy($row->payment_date));
        } else {
            echo picker_input_date();
        }
        ?>

        var isedit = <?php echo ($isedit ? 0 : 1); ?>;

        if(isedit > 0){
            $('#form-entry').block({
                message: null ,
                overlayCSS: {backgroundColor: '#EDF5EB', opacity:0, cursor:'default'}
            });
        }
        function init_page() {
            $(".mask_currency").inputmask("decimal", {
                radixPoint: ".",
                groupSeparator: ",",
                digits: 2,
                autoGroup: true,
                autoUnmask: true
            });

            $(".mask_number").inputmask({
                "mask": "9",
                "repeat": 10,
                "greedy": false
            });

            $('.tooltips').tooltip();

            $('select.select2me').select2();

            autosize($('textarea'));
        }
        init_page();

        //----- START SUPLIER ------------//
        var grid_supplier = new Datatable();
        var datatableSupplier = function () {
            var supplier_id = $('#supplier_id').val();

            grid_supplier.init({
                src: $("#datatable_supplier"),
                onSuccess: function (grid) {
                    // execute some code after table records loaded
                },
                onError: function (grid) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "bSortable": false, "sClass": "text-center" },
                        null,
                        { "bSortable": false },
                        { "bSortable": false },
                        { "bSortable": false },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('inventory/grn/ajax_modal_supplier_list');?>/" + supplier_id
                    }
                }
            });

            var tableWrapper = $("#datatable_supplier_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });
        }

        $('#btn_lookup_supplier').live('click', function (e) {
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');

            setTimeout(function(){
                $modal.load('<?php echo base_url('inventory/grn/ajax_modal_supplier');?>', '', function(){

                    $modal.modal();
                    datatableSupplier();

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if ($modal.hasClass('modal-overflow') == false) {
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top' : '0px'})

                });
            }, 100);
        });

        $('.btn-select-supplier').live('click', function (e) {
            e.preventDefault();

            var supplier_id = $(this).attr('data-id');
            var supplier_name = $(this).attr('data-name');

            var old_supplier_id_id = $('#supplier_id').val();
            var next = false;
            if(parseInt(old_supplier_id_id) > 0){
                bootbox.confirm("Old Supplier will be deleted, continue?", function(result) {
                    if (result == true) {
                        $('#supplier_id').val(supplier_id);
                        $('#supplier_name').val(supplier_name);

                        $('#inv_id').val('0');
                        $('#inv_code').val('');
                        $('#inv_remain_amount').val('0');
                        $('#return_id').val('0');
                        $('#return_code').val('');

                        set_flag_delete();
                    }
                });
            }
            else {
                $('#supplier_id').val(supplier_id);
                $('#supplier_name').val(supplier_name);

                $('#inv_id').val('0');
                $('#inv_code').val('');
                $('#return_id').val('0');
                $('#return_code').val('');

                set_flag_delete();
            }

            $('#ajax-modal').modal('hide');
        });
        //----- END SUPLIER ------------//

        //----- START BANK ------------//
        var grid_bank = new Datatable();
        var datatableBank = function () {
            var bankaccount_id = $('#bankaccount_id').val();

            grid_bank.init({
                src: $("#datatable_bank"),
                onSuccess: function (grid) {
                    // execute some code after table records loaded
                },
                onError: function (grid) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "bSortable": false, "sClass": "text-center" },    //No
                        { "bSortable": false, "sClass": "text-center" },    //Bank Code
                        { "bSortable": false, "sClass": "text-center" },    //Currency
                        { "bSortable": false },                             //Bank Account desc
                        { "bSortable": false, "sClass": "text-center" },    //COA Code
                        { "bSortable": false, "sClass": "text-center" }     //Action
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('ap/payment/ajax_modal_bank_list');?>/" + bankaccount_id
                    }
                }
            });

            var tableWrapper = $("#datatable_bank_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });
        }

        $('#btn_lookup_bank').live('click', function (e) {
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');

            setTimeout(function(){
                $modal_small.load('<?php echo base_url('ap/payment/ajax_modal_bank');?>', '', function(){

                    $modal_small.modal();
                    datatableBank();

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal_small.hasClass('bootbox') == false) {
                        $modal_small.addClass('modal-fix');
                    }

                    if ($modal_small.hasClass('modal-overflow') == false) {
                        $modal_small.addClass('modal-overflow');
                    }

                    $modal_small.css({'margin-top' : '0px'})

                });
            }, 100);
        });

        $('.btn-select-bank').live('click', function (e) {
            e.preventDefault();

            var bankaccount_id = $(this).attr('data-id');
            var bank_code = $(this).attr('data-code');
            var curr_id = $(this).attr('data-curr-id');
            var curr_code = $(this).attr('data-curr-code');

            $('#bankaccount_id').val(bankaccount_id);
            $('#bank_code').val(bank_code);
            $('#currencytype_id').val(curr_id);
            $('#currencytype_id').parent().find('.select2-chosen').html(curr_code);
            $('#currencytype_id_').val(curr_id);

            $modal_small.modal('hide');
        });
        //----- END BANK ------------//

        //----- START ADD DETAIL ------------//
        $('#btn_add_detail').live('click', function (e) {
            e.preventDefault();

            var i = $('#datatable_detail tbody tr').length;

            var new_row = '<tr>' +
                '<input data-index="' + i + '" type="hidden" class="class_status" name="status[' + i + ']" value="1"/>' +
                '<input data-index="' + i + '" type="hidden" name="detail_id[' + i + ']" value="0"/>' +
                '<td>' +
                    '<div class="input-group">' +
                        '<input data-index="' + i + '" type="hidden" name="inv_id[' + i + ']" class="is_inv" value="0" />' +
                        '<input data-index="' + i + '" type="text" name="inv_code[' + i + ']" class="form-control input-sm" value="" readonly />' +
                        '<span class="input-group-btn">' +
                            '<button class="btn btn-sm green-haze load_invoice" data-index="' + i + '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                        '</span>' +
                    '</div>' +
                '</td>' +
                '<td style="padding-top:9px;" class="text-center"><span class="inv_date_' + i + '"></span></td>' +
                '<td style="padding-top:9px;" class="text-center"><span class="inv_curr_' + i + '"></span></td>' +
                '<td style="padding-top:9px;" class="text-right">' +
                    '<span class="mask_currency inv_amount_' + i + '"></span>' +
                    '<input type="hidden" name="inv_amount[' + i + ']" value="0" />' +
                    '<input type="hidden" name="inv_vat[' + i + ']" value="0" />' +
                '</td>' +
                '<td style="padding-top:9px;" class="text-right"><span class="mask_currency inv_rate_' + i + '"></span></td>' +
                '<td style="padding-top:9px;" class="text-right"><span class="mask_currency inv_loc_amount_' + i + '"></span></td>' +
                '<td><input type="text" data-index="' + i + '" data-max="0" name="amount[' + i + ']" value="0" class="form-control input-sm mask_currency text-right amount_detail payment_amount"></td>' +
                '<td><input type="text" readonly data-index="' + i + '"  name="local_amount[' + i + ']" value="0" class="form-control text-right input-sm mask_currency amount_loc_detail"></td>' +
                '<td>' +
                    '<div class="input-group">' +
                        '<input data-index="' + i + '" type="hidden" name="taxtype_id[' + i + ']" value="0" />' +
                        '<input data-index="' + i + '" type="hidden" name="tax_wht_percent[' + i + ']" value="0" />' +
                        '<input data-index="' + i + '" type="text" name="taxtype_code[' + i + ']" class="form-control input-sm" value="" readonly />' +
                        '<span class="input-group-btn">' +
                            '<button class="btn btn-sm green-haze load_tax" data-index="' + i + '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                        '</span>' +
                    '</div>' +
                '</td>' +
                '<td><input type="text" data-index="' + i + '"  name="curr_rate_wht[' + i + ']" value="1" class="form-control text-right input-sm mask_currency curr_rate_wht" readonly></td>' +
                '<td><input type="text" data-index="' + i + '"  name="tax_wht[' + i + ']" value="0" class="form-control text-right input-sm mask_currency amount_detail tax_wht" readonly></td>' +
                '<td class="text-center text-middle">' +
                    '<a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' + i + '" style="margin-right:0px;margin-top:4px;"><i class="fa fa-times"></i></a>' +
                '</td>' +
                '</tr>';

            $('#datatable_detail tbody').append(new_row);

            init_page();

            $('#ajax-modal').modal('hide');
        });
        //----- END ADD DETAIL------------//

        //----- START INVOICE ------------//
        var grid_inv = new Datatable();
        var datatableInvoice = function (num_index, inv_id_exist) {
            var supplier_id = $('#supplier_id').val();

            grid_inv.init({
                src: $("#datatable_inv"),
                onSuccess: function (grid) {
                    // execute some code after table records loaded
                },
                onError: function (grid) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "bSortable": false, "sClass": "text-center" },
                        { "sClass": "text-center" },
                        { "sClass": "text-center" },
                        { "bSortable": false },
                        { "sClass": "text-center" },
                        { "sClass": "text-right" },
                        { "sClass": "text-right" },
                        { "bSortable": false },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('ap/debit_note/ajax_modal_invoice_list');?>/" + supplier_id + '/' + inv_id_exist + '/' + num_index
                    },
                    "fnDrawCallback": function( oSettings ) {
                        init_page();
                    }
                }
            });

            var tableWrapper = $("#datatable_inv_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false
            });
        }

        $('.load_invoice').live('click', function (e) {
            e.preventDefault();

            var supplier_id = parseInt($('#supplier_id').val()) || 0;
            var num_index = $(this).attr('data-index');

            var inv_id_exist = '-';
            var n = 0;
            $('#datatable_detail tbody tr').each(function () {
                if ($(this).hasClass('hide') == false) {
                    var inv_id = parseInt($(this).find("input.is_inv").val()) || 0;
                    if (inv_id > 0) {
                        if(inv_id_exist == '-'){
                            inv_id_exist = '';
                        }
                        if(n > 0) {
                            inv_id_exist += '_';
                        }

                        inv_id_exist += inv_id;
                        n++;
                    }
                }
            });

            if (supplier_id > 0) {
                $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                    '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

                $('body').modalmanager('loading');

                setTimeout(function () {
                    $modal.load('<?php echo base_url('ap/debit_note/ajax_modal_invoice');?>', '', function () {

                        $modal.modal();
                        datatableInvoice(num_index, inv_id_exist);

                        $.fn.modalmanager.defaults.resize = true;

                        if ($modal.hasClass('bootbox') == false) {
                            $modal.addClass('modal-fix');
                        }

                        if ($modal.hasClass('modal-overflow') == false) {
                            $modal.addClass('modal-overflow');
                        }

                        $modal.css({'margin-top': '0px'});

                        init_page();
                    });
                }, 100);
            } else {
                toastr["warning"]("Please select Supplier.", "Warning");
            }
        });

        $('.btn-select-inv').live('click', function (e) {
            e.preventDefault();

            var inv_id = $(this).attr('data-id');
            var inv_code = $(this).attr('data-code');
            var inv_date = $(this).attr('data-inv-date');
            var inv_curr = $(this).attr('data-curr-code');
            var inv_rate = $(this).attr('data-rate');
            var inv_remain_amount = parseFloat($(this).attr('data-inv-remain-amount')) || 0;
            var inv_vat = parseFloat($(this).attr('data-vat')) || 0;
            var taxtype_id = $(this).attr('data-tax-id');
            var taxtype_code = $(this).attr('data-tax-code');
            var tax_wht_percent = parseFloat($(this).attr('data-tax-wht-percent')) || 0;
            var i = $(this).attr('data-index');

            var local_ = (inv_remain_amount * inv_rate).toFixed(2);
            var inv_loc_amount = parseFloat(local_) || 0;

            $('input[name="inv_id[' + i + ']"]').val(inv_id);
            $('input[name="inv_code[' + i + ']"]').val(inv_code);
            $('.inv_date_' + i).html(inv_date);
            $('.inv_curr_' + i).html(inv_curr);
            $('.inv_amount_' + i).html(inv_remain_amount);
            $('input[name="inv_amount[' + i + ']"]').val(inv_remain_amount);
            $('input[name="inv_vat[' + i + ']"]').val(inv_vat);
            $('.inv_rate_' + i).html(inv_rate);
            $('.inv_loc_amount_' + i).html(inv_loc_amount);
            $('input[name="taxtype_id[' + i + ']"]').val(taxtype_id);
            $('input[name="taxtype_code[' + i + ']"]').val(taxtype_code);
            $('input[name="tax_wht_percent[' + i + ']"]').val(tax_wht_percent);

            if (inv_curr != '<?php echo Purchasing::CURR_IDR;?>') {
                if (tax_wht_percent > 0) {
                    $('input[name="curr_rate_wht[' + i + ']"]').prop('readonly', false);
                    $('input[name="curr_rate_wht[' + i + ']"]').val(inv_rate);
                } else {
                    $('input[name="curr_rate_wht[' + i + ']"]').prop('readonly', true);
                    $('input[name="curr_rate_wht[' + i + ']"]').val('1');
                }
            } else {
                $('input[name="curr_rate_wht[' + i + ']"]').prop('readonly', true);
                $('input[name="curr_rate_wht[' + i + ']"]').val('1');
            }

            if (tax_wht_percent > 0) {
                $('input[name="tax_wht[' + i + ']"]').prop('readonly', false);

                var wht_ = ((inv_remain_amount * tax_wht_percent) / 100).toFixed(2);
                var wht = parseFloat(wht_) || 0;
                $('input[name="tax_wht[' + i + ']"]').val(wht);
            } else {
                $('input[name="tax_wht[' + i + ']"]').prop('readonly', true);
                $('input[name="tax_wht[' + i + ']"]').val('0');
            }

            $modal.modal('hide');

            init_page();
        });
        //----- END INVOICE ------------//

        //----- START TAX ------------//
        var grid_tax = new Datatable();
        var datatableTax = function (num_index) {
            grid_tax.init({
                src: $("#datatable_tax"),
                onSuccess: function (grid) {
                    // execute some code after table records loaded
                },
                onError: function (grid) {
                    // execute some code on network or other general error
                },
                onDataLoad: function (grid) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Loading...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        {"bSortable": false, "sClass": "text-center"},
                        {"bSortable": false },
                        {"bSortable": false },
                        {"bSortable": false, "sClass": "text-center"}
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('purchasing/po/ajax_modal_tax_list');?>/" + num_index
                    }
                }
            });

            var tableWrapper = $("#datatable_tax_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });
        }

        $('.load_tax').live('click', function (e) {
            e.preventDefault();

            var num_index = parseInt($(this).attr('data-index'));

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal_small.load('<?php echo base_url('purchasing/po/ajax_modal_tax');?>', '', function () {

                    $modal_small.modal();
                    datatableTax(num_index);

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal_small.hasClass('bootbox') == false) {
                        $modal_small.addClass('modal-fix');
                    }

                    if ($modal_small.hasClass('modal-overflow') == false) {
                        $modal_small.addClass('modal-overflow');
                    }

                    $modal_small.css({'margin-top': '0px'});
                });
            }, 100);
        });

        $('.btn-select-tax').live('click', function (e) {
            e.preventDefault();

            var tax_id = $(this).attr('data-id');
            var i = $(this).attr('data-index');
            var tax_wht = parseFloat($(this).attr('data-wht')) || 0;
            var tax_code = $(this).attr('data-code');

            var inv_amount = parseFloat($('input[name="inv_amount[' + i + ']"]').val()) || 0;
            var amount_wht = parseFloat((inv_amount * tax_wht / 100).toFixed(2)) || 0;

            var inv_curr = $('.inv_curr_' + i).text().trim();

            $('input[name="taxtype_id[' + i + ']"]').val(tax_id);
            $('input[name="tax_wht_percent[' + i + ']"]').val(tax_wht);
            $('input[name="taxtype_code[' + i + ']"]').val(tax_code);
            $('input[name="tax_wht[' + i + ']"]').val(amount_wht);

            if (inv_curr != '<?php echo Purchasing::CURR_IDR;?>') {
                if (tax_wht > 0) {
                    $('input[name="curr_rate_wht[' + i + ']"]').prop('readonly', false);
                } else {
                    $('input[name="curr_rate_wht[' + i + ']"]').prop('readonly', true);
                }
            } else {
                $('input[name="curr_rate_wht[' + i + ']"]').prop('readonly', true);
            }

            if (tax_wht > 0) {
                $('input[name="tax_wht[' + i + ']"]').prop('readonly', false);
            } else {
                $('input[name="tax_wht[' + i + ']"]').prop('readonly', true);
            }

            $modal_small.modal('hide');
        });
        //----- END TAX ------------//

        $('.amount_detail').live('keyup', function(){
            toastr.clear();

            var i = $(this).attr('data-index');

            var amount = parseFloat($('input[name="amount[' + i + ']"]').val().trim()) || 0;
            var max = parseFloat($('input[name="inv_amount[' + i + ']"]').val().trim()) || 0;
            var wht = parseFloat($('input[name="tax_wht[' + i + ']"]').val().trim()) || 0;
            var tot_ = (amount + wht).toFixed(2);
            var tot = parseFloat(tot_) || 0;

            if (max > 0) {
                if (tot > max) {
                    if ($(this).hasClass('payment_amount')) {
                        var val_me_ = (max - wht).toFixed(2);
                        var val_me = parseFloat(val_me_) || 0;
                        $(this).val(val_me);
                        amount = val_me;
                    } else {
                        var val_me_ = (max - amount).toFixed(2);
                        var val_me = parseFloat(val_me_) || 0;
                        $(this).val(val_me);
                    }

                    toastr["warning"]("Amount cannot bigger than Invoice amount.", "Warning");
                }
            }

            var curr_rate = parseFloat($('#curr_rate').val().trim()) || 0;

            var local_tot_amount_ = (amount * curr_rate).toFixed(2);
            var local_tot_amount = parseFloat(local_tot_amount_) || 0;
            $('input[name="local_amount[' + i + ']"]').val(local_tot_amount);

            if(local_tot_amount < 0){
                $('input[name="local_amount[' + i + ']"]').val(amount);

                toastr["warning"]("invalid local amount.", "Warning");
            }
            calculate_tot_amount();
        });
		
        function calculate_tot_amount(){
            var len = $('#datatable_detail tbody tr').length;
            if(len > 0){
                var tot = 0;
                var loc_tot = 0;
                for(var i = 0; i < len; i++ ){
                    var stat = $('input[name="status[' + i + ']"]').val();

                    if(stat != '<?php echo STATUS_DELETE;?>') {
                        var val = parseFloat($('input[name="amount[' + i + ']"]').val()) || 0;
                        tot += val;

                        var val_l = parseFloat($('input[name="local_amount[' + i + ']"]').val()) || 0;
                        loc_tot += val_l;
                    }
                }
                $('.num_total').val(tot);
                $('.num_loc_total').val(loc_tot);
            }
        }

        function calculate_rate(){
            var len = $('#datatable_detail tbody tr').length;
            var curr_rate = parseFloat($('#curr_rate').val().trim()) || 0;

            if(len > 0){
                var tot = 0;
                for(var i = 0; i < len; i++ ){
                    var stat = $('input[name="status[' + i + ']"]').val();

                    if(stat != '<?php echo STATUS_DELETE;?>') {
                        var val = parseFloat($('input[name="amount[' + i + ']"]').val()) || 0;
                        var local_tot_amount_ = (val * curr_rate).toFixed(2);
                        var local_tot_amount = parseFloat(local_tot_amount_);
                        $('input[name="local_amount[' + i + ']"]').val(local_tot_amount);
                        tot += local_tot_amount;
                    }
                }
                $('.num_loc_total').val(tot);
            }
        }

		$('.num_rate').live('keyup', function(){
			calculate_rate(); 
		});

        function set_flag_delete(){
            $('#datatable_detail tbody tr').each(function(){
                if($(this).hasClass('hide') == false){
                    $(this).addClass('hide');
                }
            });

            $('#datatable_detail tbody tr input').each(function(){
                if($(this).hasClass('class_status')){
                    $(this).val('9');
                }
            });

            calculate_tot_amount();
        }

        $('.btn-remove').live('click', function(){
            var this_btn = $(this);
            bootbox.confirm("Are you sure want to delete?", function (result) {
                if (result == true) {
                    this_btn.closest('tr').addClass('hide');
                    this_btn.closest('tr').find('.class_status').val('9');

                    calculate_tot_amount();
                }
            });
        });

        //----- START SUBMIT -------------//
        function validate_submit() {
            var result = true;

            if($('.form-group').hasClass('has-error')){
                $('.form-group').removeClass('has-error');
            }
            if($('td').hasClass('has-error')){
                $('td').removeClass('has-error');
            }

            var supplier_id = parseInt($('#supplier_id').val()) || 0;
            var payment_date = $('#payment_date').val().trim();
            var curr_rate = parseFloat($('#curr_rate').val().trim()) || 0;
            var bankaccount_id = parseInt($('#bankaccount_id').val()) || 0;
            var description = $('textarea[name="description"]').val().trim();
            var currencytype_id = parseInt($('#currencytype_id').val()) || 0;
            var currencytype_code = $('#currencytype_id').parent().find('.select2-chosen').text().trim();

            if (supplier_id <= 0) {
                toastr["warning"]("Please select Supplier.", "Warning!");
                $('#supplier_id').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (payment_date == '') {
                toastr["warning"]("Please select Payment Date.", "Warning!");
                $('#payment_date').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (bankaccount_id <= 0) {
                toastr["warning"]("Please select Bank.", "Warning!");
                $('#bankaccount_id').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (currencytype_id <= 0) {
                toastr["warning"]("Please select Currency.", "Warning!");
                $('#currencytype_id').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (curr_rate <= 0) {
                toastr["warning"]("Please input valid currency rate.", "Warning!");
                $('#curr_rate').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (description == '') {
                toastr["warning"]("Please input remarks.", "Warning!");
                $('textarea[name="description"]').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (currencytype_code != '<?php echo Purchasing::CURR_IDR;?>') {
                if (curr_rate <= 1) {
                    toastr["warning"]("Please input valid currency rate. Payment using " + currencytype_code + ".", "Warning!");
                    $('#curr_rate').closest('.form-group').addClass('has-error');
                    result = false;
                }
            }

            var i = 0;
            var i_act = 0;
            var true_curr = true;
            var inv_curr_ = '';
            $('#datatable_detail > tbody > tr ').each(function() {
                if(!$(this).hasClass('hide')) {
                    var inv_code_ = $('input[name="inv_code[' + i + ']"]').val().trim();
                    var inv_id = parseInt($('input[name="inv_id[' + i + ']"]').val().trim()) || 0;
                    var taxtype_id = parseInt($('input[name="taxtype_id[' + i + ']"]').val().trim()) || 0;
                    var amount = parseFloat($('input[name="amount[' + i + ']"]').val()) || 0;
                    var local_amount = parseFloat($('input[name="local_amount[' + i + ']"]').val()) || 0;
                    var curr_rate_wht = parseFloat($('input[name="curr_rate_wht[' + i + ']"]').val()) || 0;
                    var inv_curr = $('.inv_curr_' + i).text().trim();

                    if (inv_curr_ != '') {
                        if (inv_curr_ != inv_curr) {
                            true_curr = false;
                        }
                    } else {
                        inv_curr_ = inv_curr;
                    }

                    if (inv_code_ != '') {
                        var inv_code = ' for ' + inv_code_;
                    } else {
                        var inv_code = '';
                    }

                    if(inv_id <= 0){
                        toastr["warning"]("Please select Invoice.", "Warning");
                        $('input[name="inv_id[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if(taxtype_id <= 0){
                        toastr["warning"]("Please select Tax.", "Warning");
                        $('input[name="taxtype_id[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if(amount <= 0){
                        toastr["warning"]("Please input valid Amount" + inv_code + ".", "Warning");
                        $('input[name="amount[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if(local_amount <= 0){
                        toastr["warning"]("Please input valid Local Total Amount" + inv_code + ".", "Warning");
                        $('input[name="local_amount[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if(curr_rate_wht <= 0){
                        toastr["warning"]("Please input valid Rate WHT" + inv_code + ".", "Warning");
                        $('input[name="curr_rate_wht[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    i_act++;
                }
                i++;
            });

            if (true_curr == false) {
                toastr["warning"]("Invoice has different currency.", "Warning");
                result = false;
            }

            if(i_act <= 0 ){
                toastr["warning"]("Detail cannot be empty.", "Warning");
                result = false;
            }

            if(i_act > 0 ) {
                if (inv_curr_ != '<?php echo Purchasing::CURR_IDR;?>') {
                    if (curr_rate <= 1) {
                        toastr["warning"]("Please input valid currency rate. Invoice using " + inv_curr_ + ".", "Warning!");
                        $('#curr_rate').closest('.form-group').addClass('has-error');
                        result = false;
                    }
                }
            }

            return result;
        }

        $('#id_form_input').on('submit', function(){
            Metronic.blockUI({
                target: '#id_form_input',
                boxed: true,
                message: 'Processing...'
            });

            var btn = $(this).find("button[type=submit]:focus");

            toastr.clear();

            if(validate_submit()){
                var form_data = $('#id_form_input').serializeArray();
                if (btn[0] == null){ }
                else {
                    if(btn[0].name === 'save_close'){
                        form_data.push({name: "save_close", value: 'save_close'});
                    }
                }

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('ap/payment/ajax_payment_submit');?>",
                    dataType: "json",
                    data: form_data
                })
                    .done(function( msg ) {
                        if(msg.valid == '0' || msg.valid == '1'){
                            if(msg.valid == '1'){
                                window.location.assign(msg.link);
                            } else {
                                toastr["error"](msg.message, "Error");
                                $('#id_form_input').unblock();
                            }
                        } else {
                            toastr["error"]("Something has wrong, please try again later.", "Error");
                            $('#id_form_input').unblock();
                        }
                    })
                    .fail(function(e) {
                        toastr["error"]("Something has wrong, please try again later.", "Error");
                        $('#id_form_input').unblock();
                    });
            } else {
                $('#id_form_input').unblock();
            }
        });
        //----- END SUBMIT -------------//

        //------- START ACTION -----------//
        $('.btn-action').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var data_code = $(this).attr('data-code');
            var action_code = $(this).attr('data-action-code');

            if(action == '<?php echo STATUS_CANCEL;?>'){
                bootbox.prompt({
                    title: "Please enter cancel reason :",
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
                            toastr["warning"]("Cancel reason must be filled to proceed, Minimum 5 character.", "Warning");
                        } else {
                            //console.log(result);
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                type: "POST",
                                url: "<?php echo base_url('ap/payment/ajax_payment_action');?>",
                                dataType: "json",
                                data: {payment_id: id, action: action, reason: result, is_redirect: true}
                            })
                                .done(function (msg) {
                                    if (msg.valid == '0' || msg.valid == '1') {
                                        if (msg.valid == '1') {
                                            location.href = '<?php echo base_url('ap/payment/payment_history/1/' . $payment_id . '.tpd');?>';
                                        } else {
                                            toastr["error"](msg.message, "Error");
                                            Metronic.unblockUI();
                                        }
                                    } else {
                                        toastr["error"]("Something has wrong, please try again later.", "Error");
                                        Metronic.unblockUI();
                                    }
                                }).fail(function(e) {
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                    Metronic.unblockUI();
                                });
                        }
                    }
                });
            } else {
                $modal_xtra_small.modal('show');
            }
        });

        $('#btn_posting').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var action_date = $('#posting_date').val();

            Metronic.blockUI({
                boxed: true
            });

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('ap/payment/ajax_payment_action');?>",
                dataType: "json",
                data: {payment_id: id, action: action, is_redirect: true, action_date: action_date}
            })
                .done(function (msg) {
                    if (msg.valid == '0' || msg.valid == '1') {
                        if (msg.valid == '1') {
                            location.href = '<?php echo base_url('ap/payment/payment_history/1/' . $payment_id . '.tpd');?>';
                        } else {
                            toastr["error"](msg.message, "Error");
                            Metronic.unblockUI();
                        }
                    } else {
                        toastr["error"]("Something has wrong, please try again later.", "Error");
                        Metronic.unblockUI();
                    }
                }).fail(function(e) {
                    toastr["error"]("Something has wrong, please try again later.", "Error");
                    Metronic.unblockUI();
                });
        });
        //------- END ACTION -----------//
	});
</script>