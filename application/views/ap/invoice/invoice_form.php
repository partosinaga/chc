<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();
if($inv_id > 0) {
    if($row->status == STATUS_NEW){
        if(check_session_action(get_menu_id(), STATUS_EDIT)) {
            $btn_action .= $btn_save;
        } else {
            $isedit = false;
        }
        if(check_session_action(get_menu_id(), STATUS_POSTED)){
            $btn_action .= btn_action($inv_id, $row->inv_code, STATUS_POSTED);
        }
        if(check_session_action(get_menu_id(), STATUS_CANCEL)){
            $btn_action .= btn_action($inv_id, $row->inv_code, STATUS_CANCEL);
        }
    } else {
        $isedit = false;
        if($row->status == STATUS_POSTED) {
            if(check_session_action(get_menu_id(), STATUS_PRINT)) {
                $btn_action .= btn_print(base_url('inventory/inv/pdf_inv/' . $inv_id . '.tpd'));
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
							<i class="fa fa-user"></i><?php echo ($inv_id > 0 ? 'View' : 'New');?> Invoice
                            <?php
                            if($inv_id > 0){
                                echo '&nbsp;&nbsp;&nbsp;' . get_status_name($row->status);
                            }
                            ?>
						</div>
						<div class="actions">
                            <?php
                            $back_url = base_url('ap/invoice/invoice_manage.tpd');
                            if ($inv_id > 0) {
                                if ($row->status != STATUS_NEW) {
                                    $back_url = base_url('ap/invoice/invoice_history.tpd');
                                }
                            }
                            echo btn_back($back_url);
                            ?>
						</div>
					</div>
					<div class="portlet-body form">
						<form method="post" id="id_form_input" class="form-horizontal" onsubmit="return false">
							<input type="hidden" name="inv_id" value="<?php echo $inv_id;?>" />                            
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
											<label class="control-label col-md-4">Invoice No</label>
											<div class="col-md-6">
												<input type="text" class="form-control" value="<?php echo ($inv_id > 0 ? $row->inv_code : '');?>" readonly />
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Invoice Dates</label>
                                            <div class="col-md-4">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="inv_date" id="inv_date" value="<?php echo ($inv_id > 0 ? ymd_to_dmy($row->inv_date) : date('d-m-Y'));?>" readonly>
													<span class="input-group-btn">
														<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
											<label class="control-label col-md-4">Supplier</label>
											<div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="supplier_id" id="supplier_id" value="<?php echo ($inv_id > 0 ? $row->supplier_id : '0');?>" />
                                                    <input class="form-control" id="supplier_name" type="text" value="<?php echo ($inv_id > 0 ? $row->supplier_name : ''); ?>" readonly >
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_supplier" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Curr / Rate</label>
                                            <div class="col-md-3">
                                                <select name="currencytype_id" class="select2me form-control">
                                                    <?php
                                                    $qry_curr = $this->db->get('currencytype');
                                                    foreach($qry_curr->result() as $row_curr){
                                                        echo '<option value="' . $row_curr->currencytype_id . '" ' . ($inv_id > 0 ? ($row->currencytype_id == $row_curr->currencytype_id ? 'selected="selected"' : '') : '') . '>' . $row_curr->currencytype_code . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="curr_rate" id="curr_rate" class="form-control mask_currency num_rate" value="<?php echo ($inv_id > 0 ? $row->curr_rate : '1');?>" />
                                            </div>
                                        </div>
										<div class="form-group">
											<label class="control-label col-md-4">Total Amount</label>
											<div class="col-md-4">
												<input type="text" readonly name="totalgrand" id="totalgrand" class="form-control num_totalamount  mask_currency text-right" value="<?php echo ($inv_id > 0 ? $row->totalgrand : '');?>" />
												
											</div> 
										</div>
									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Ref No</label>
                                            <div class="col-md-6">
                                                <input type="text" name="inv_ref" class="form-control" value="<?php echo ($inv_id > 0 ? $row->inv_ref : '');?>" />
                                            </div>
                                        </div>
										<?php
										$tax = 0;
										$tax_id = 0;
                                        $tax_percent = 0;
                                        $totaltax_wht_percent = 0;
                                        $totaltax_wht = 0;
                                        $tax_code = '';
										if($inv_id > 0){
											$tax = $row->totaltax;
											$tax_id = $row->tax_id;
                                            $tax_percent = $row_tax->taxtype_percent;
                                            $totaltax_wht_percent = $row_tax->taxtype_wht;
                                            $totaltax_wht = $row->totaltax_wht;
                                            $tax_code = $row_tax->taxtype_code;
										}
										?>
										<div class="form-group">
                                            <label class="control-label col-md-4">Tax</label>
                                            <div class="col-md-8">
                                                <div class="input-group margin-bottom-5">
													<input type="hidden" class="c_tax" name="taxtype_id" value="<?php echo $tax_id;?>" />
                                                    <input type="hidden" class="c_tax_percent" name="c_tax_percent" value="<?php echo $tax_percent;?>" />
                                                    <input type="hidden" class="totaltax_wht_percent" name="totaltax_wht_percent" value="<?php echo $totaltax_wht_percent;?>" />
                                                    <input type="hidden" class="totaltax_wht" name="totaltax_wht" value="<?php echo $totaltax_wht;?>" />
													<input type="text" name="taxtype_amount" class="form-control mask_currency text-right" value="<?php echo $tax;?>" />
													<span class="input-group-btn">
														<button class="btn btn-success load_tax_h" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>
													</span>
                                                    <input type="text" name="taxtype_code" class="form-control" value="<?php echo $tax_code;?>" readonly />
												</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Tax Account</label>
                                            <div class="col-md-6">
												<select name="tax_account" class="select2me form-control">
												<option value="">-- Select Tax Account --</option>
                                                <?php
                                                    foreach ($qry_coa_tax->result() as $row_coa_tax) {
                                                        $coa_tax_text = '';
                                                        if ($row_coa_tax->spec_key == FNSpec::FIN_AP_PREPAID_TAX) {
                                                            $coa_tax_text = 'Prepaid Tax - VAT In';
                                                        } else if ($row_coa_tax->spec_key == FNSpec::FIN_AP_VAT_IN_EXP) {
                                                            $coa_tax_text = 'VAT In Expense';
                                                        }
                                                        $coa_tax_selected = '';
                                                        if ($inv_id > 0) {
                                                            if ($row->tax_account == $row_coa_tax->id) {
                                                                $coa_tax_selected = 'selected="selected"';
                                                            }
                                                        }
                                                        echo '<option value="' . $row_coa_tax->id . '" ' . $coa_tax_selected . '>[' . $row_coa_tax->coa_code . '] ' . $coa_tax_text . '</option>';
                                                    }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Term Of Payment</label>
                                            <div class="col-md-4">
                                                <input type="text" name="term_ofpayment" class="form-control mask_number text-right" value="<?php echo ($inv_id > 0 ? $row->term_ofpayment : '0');?>" />
                                            </div>
                                            <label class="control-label col-md-2" style="text-align:left;padding-left: 0px;">Days</label>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-10">
												<textarea class="form-control" rows="2" name="inv_desc"   ><?php echo ($inv_id > 0 ? $row->inv_desc : '');?></textarea>                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="row">
									<div class="col-md-12" style="margin-bottom: 10px;">
                                        <?php echo btn_add_detail();?>
									</div>
									<div class="col-md-12">
										<table class="table table-striped table-hover table-bordered" id="datatable_detail">
											<thead>
												<tr>
													<th class="text-center" width="150px"> A/C Type </th>
													<th class="text-center"> Charge To </th>
                                                    <th class="text-center" width="200px"> Dept </th>
													<th class="text-center" width="200px"> Amount </th>
													<th class="text-center" width="200px"> Local Amount </th>
                                                    <th class="text-center" width="40px"> Action </th>
												</tr>
											</thead>
											<tbody>
                                            <?php
                                            $total = 0;
                                            $local_total = 0;
                                            if($inv_id > 0){
                                                $i = 0;
                                                foreach($qry_detail->result() as $row_detail){
                                                    $dept_combo = '';
                                                    if(count($dept_list) > 0) {
                                                        foreach ($dept_list as $dept) {
                                                            $dept_combo .= '<option value="' . $dept['department_id'] . '" ' . ($row_detail->dept_id == $dept['department_id'] ? 'selected="selected"' : '') . '>' . $dept['department_name'] . '</option>';
                                                        }
                                                    }
                                                    echo '<tr>';
                                                    echo '<input data-index="' . $i . '" type="hidden" class="class_status" name="status[' . $i . ']" value="1"/>';
                                                    echo '<input type="hidden" name="inv_detid[' . $i . ']" value="' . $row_detail->inv_detid . '" />';
                                                    echo '<td>
                                                                <select data-index="' . $i . '" name="inv_actype[' . $i . ']" class="form-control input-sm select2me select_ac_type">
                                                                    <option value="0">-- Select --</option>
                                                                    <option value="' . AP::INV_TYPE_GRN . '" ' . ($row_detail->inv_actype == AP::INV_TYPE_GRN ? 'selected="selected"' : '') . '> GRN </option>
                                                                    <option value="' . AP::INV_TYPE_AC . '" ' . ($row_detail->inv_actype == AP::INV_TYPE_AC ? 'selected="selected"' : '') . '> A/C </option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input data-index="' . $i . '" class="' . ($row_detail->inv_actype == AP::INV_TYPE_GRN ? 'is_grn' : 'is_coa') . '" type="hidden" name="charge_to[' . $i . ']" value="' . $row_detail->charge_to . '" />
                                                                    <input data-index="' . $i . '" type="text" name="charge_to_code[' . $i . ']" class="form-control input-sm" value="' . ($row_detail->inv_actype == AP::INV_TYPE_GRN ? $row_detail->grn_code : $row_detail->coa_code) . '" readonly />
                                                                    <span class="input-group-btn">
                                                                        <button class="btn btn-sm green-haze load_charge" data-index="' . $i . '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <select name="dept_id[' . $i . ']" class="form-control input-sm select2me">
                                                                    <option value="0" >-- Select --</option>
                                                                    ' . $dept_combo . '
                                                                </select>
                                                            </td>
                                                            <td><input type="text" data-index="' . $i . '" name="tot_amount[' . $i . ']"   value="' . $row_detail->tot_amount . '" class="form-control input-sm mask_currency text-right amount_detail num_amount"></td>
                                                            <td><input type="text" readonly data-index="' . $i . '"  name="local_tot_amount[' . $i . ']" value="' . $row_detail->local_tot_amount . '" class="form-control text-right input-sm mask_currency"></td>
                                                            <td class="text-center text-middle">
                                                                <a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' . $i . '" style="margin-right:0px;margin-top:4px;"><i class="fa fa-times"></i></a>
                                                            </td>
                                                        </tr>';
                                                    $i++;

                                                    $total += $row_detail->tot_amount;
                                                    $local_total += $row_detail->local_tot_amount;
                                                }
                                            }

                                            ?>
											</tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-right" style="vertical-align: middle;">Total</th>
                                                    <th><input name="totalamount" type="text" class="form-control input-sm num_total text-right mask_currency" value="<?php echo $total;?>" readonly /></th>
                                                    <th><input type="text" class="form-control input-sm num_loc_total text-right mask_currency" value="<?php echo $local_total;?>" readonly /></th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </tfoot>
										</table>
									</div>
								</div>
							</div>

                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if($inv_id > 0){
                                        $log = '';
                                        $modified = '';
                                        $qry_log = $this->mdl_general->get('app_log', array('feature_id' => Feature::FEATURE_AP_INVOICE, 'reff_id' => $inv_id), array(), 'log_id asc');
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
                                            $modified .= "<div class='col-md-4'><h6>Last Modified by " . get_user_fullname( $row->user_modified ) . " (" . date_format(new DateTime($row->date_modified), 'd/m/Y H:i:s') . ")</h6></div>" ;
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

<script>
	$(document).ready(function(){
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        var $modal = $('#ajax-modal');

        <?php
        if($inv_id > 0){
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
            echo picker_input_date(true, true, ymd_to_dmy($row->inv_date));
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
                        "url": "<?php echo base_url('inventory/grn/ajax_modal_supplier_list');?>/" + $('#supplier_id').val() // ajax source
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

                        $('#po_id').val('0');
                        $('#po_code').val('');

                        set_flag_delete();
                    }
                });
            }
            else {
                $('#supplier_id').val(supplier_id);
                $('#supplier_name').val(supplier_name);

                $('#po_id').val('0');
                $('#po_code').val('');

                set_flag_delete();
            }

            $('#ajax-modal').modal('hide');
        });
        //----- END SUPLIER ------------//

        //----- START TAX ------------//
        var grid_tax = new Datatable();
        var datatableTax = function (tax_id) {
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
                        "url": "<?php echo base_url('purchasing/po/ajax_modal_tax_list');?>/" + tax_id // ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_tax_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });
        }

        $('.load_tax_h').live('click', function (e) {
            e.preventDefault();

            var tax_id = $(this).closest('.input-group').find('.c_tax').val();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');


            setTimeout(function () {
                $modal.load('<?php echo base_url('purchasing/po/ajax_modal_tax');?>', '', function () {

                    $modal.modal();
                    datatableTax(tax_id);

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if ($modal.hasClass('modal-overflow') == false) {
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});

                });
            }, 100);
        });

        $('.btn-select-tax').live('click', function (e) {
            e.preventDefault();

            var tax_id = $(this).attr('data-id');
            var tax_code = $(this).attr('data-code');
            var tax_vat = $(this).attr('data-percent');
            var tax_wht = $(this).attr('data-wht');

            var amount = parseFloat($('.num_total').val()) || 0;
            var vat_ = (amount * tax_vat / 100).toFixed(2);
            var vat = parseFloat(vat_) || 0;
            var tot_grand = amount + vat;

            var wht_ = (amount * tax_wht / 100).toFixed(2);
            var wht = parseFloat(wht_) || 0;

            $("input[name='taxtype_id']").val(tax_id);
            $("input[name='taxtype_amount']").val(vat);
            $("input[name='taxtype_code']").val(tax_code);
            $(".c_tax_percent").val(tax_vat);
            $(".totaltax_wht_percent").val(tax_wht);
            $(".totaltax_wht").val(wht);
            $('input[name="totalgrand"]').val(tot_grand);

            $('#ajax-modal').modal('hide');
        });
        //----- END TAX ------------//

        $('#btn_add_detail').live('click', function (e) {
            e.preventDefault();

            var supplier_id = $('#supplier_id').val();
            supplier_id = parseInt(supplier_id) || 0;

            if(supplier_id > 0) {
                var i = $('#datatable_detail tbody tr').length;

                var new_row = '<tr>' +
                    '<input data-index="' + i + '" type="hidden" class="class_status" name="status[' + i + ']" value="1"/>' +
                    '<input data-index="' + i + '" type="hidden" name="inv_detid[' + i + ']" value="0"/>' +
                    '<td>' +
                        '<select data-index="' + i + '" name="inv_actype[' + i + ']" class="form-control input-sm select2me select_ac_type">' +
                            '<option value="0">-- Select --</option>' +
                            '<option value="<?php echo AP::INV_TYPE_GRN;?>"> GRN </option>' +
                            '<option value="<?php echo AP::INV_TYPE_AC;?>"> A/C </option>' +
                        '</select>' +
                    '</td>' +
                    '<td>' +
                        '<div class="input-group">' +
                            '<input data-index="' + i + '" type="hidden" name="charge_to[' + i + ']" value="0" />' +
                            '<input data-index="' + i + '" type="text" name="charge_to_code[' + i + ']" class="form-control input-sm" value="" readonly />' +
                            '<span class="input-group-btn">' +
                                '<button class="btn btn-sm green-haze load_charge" data-index="' + i + '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                            '</span>' +
                        '</div>' +
                    '</td>' +
                    '<td>' +
                        '<select name="dept_id[' + i + ']" class="form-control input-sm select2me">' +
                        '<option value="0" >-- Select --</option>' +
                        <?php
                            if(count($dept_list) > 0){
                                foreach($dept_list as $dept){
                        ?>
                        '<option value="<?php echo $dept['department_id']; ?>" ><?php echo $dept['department_name']; ?></option>' +
                        <?php
                                }
                            }
                        ?>
                        '</select>' +
                    '</td>' +
                    '<td><input type="text" data-index="' + i + '" name="tot_amount[' + i + ']"   value="0" class="form-control   input-sm mask_currency text-right amount_detail num_amount"></td>' +
                    '<td><input type="text" readonly data-index="' + i + '"  name="local_tot_amount[' + i + ']" value="0" class="form-control text-right input-sm mask_currency"></td>' +
                    '<td class="text-center text-middle">' +
                        '<a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' + i + '" style="margin-right:0px;margin-top:4px;"><i class="fa fa-times"></i></a>' +
                    '</td>' +
                    '</tr>';

                $('#datatable_detail tbody').append(new_row);

                init_page();
            } else {
                toastr["warning"]("Please Select Supplier .", "Warning");
            }
        });

        $('.select_ac_type').live('change', function () {
            var val = parseInt($(this).val()) || 0;
            var i = $(this).attr('data-index');

            $('input[name="charge_to[' + i + ']"]').val('0');
            $('input[name="charge_to_code[' + i + ']"]').val('');

            $('input[name="charge_to[' + i + ']"]').removeClass('is_grn');
            $('input[name="charge_to[' + i + ']"]').removeClass('is_coa');
            if (val == <?php echo AP::INV_TYPE_GRN;?>) {
                $('input[name="charge_to[' + i + ']"]').addClass('is_grn');
            } else if (val == <?php echo AP::INV_TYPE_AC;?>) {
                $('input[name="charge_to[' + i + ']"]').addClass('is_coa');
            }

            $('input[name="tot_amount[' + i + ']"]').val('0');
            $('input[name="local_tot_amount[' + i + ']"]').val('0');
            $('select[name="dept_id[' + i + ']"]').val('0');

            $('select[name="dept_id[' + i + ']"]').parent().find('span.select2-chosen').html('-- Select --');
        });

        $('.load_charge').live('click', function(e) {
            var i = $(this).attr('data-index');

            var inv_type = parseInt($('select[name="inv_actype[' + i + ']"]').val()) || 0;
            if (inv_type > 0) {
                var load_modal = '';
                if (inv_type == <?php echo AP::INV_TYPE_GRN;?>) {
                    load_modal = '<?php echo base_url('ap/invoice/ajax_modal_grn');?>';
                } else if (inv_type == <?php echo AP::INV_TYPE_AC;?>) {
                    load_modal = '<?php echo base_url('purchasing/po/ajax_modal_coa');?>';
                }
                $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                    '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

                $('body').modalmanager('loading');

                setTimeout(function () {
                    $modal.load(load_modal, '', function () {

                        $modal.modal();

                        if (inv_type == <?php echo AP::INV_TYPE_GRN;?>) {
                            datatablegrn(i);
                        } else if (inv_type == <?php echo AP::INV_TYPE_AC;?>) {
                            handleTableCOA(i);
                        }

                        $.fn.modalmanager.defaults.resize = true;

                        if ($modal.hasClass('bootbox') == false) {
                            $modal.addClass('modal-fix');
                        }

                        if ($modal.hasClass('modal-overflow') == false) {
                            $modal.addClass('modal-overflow');
                        }

                        $modal.css({'margin-top': '0px'});

                    });
                }, 100);
            } else {
                toastr["warning"]("Please Select A/C Type .", "Warning");
            }
        });

        //------- START GRN -------------//
        var grid_grn = new Datatable();
        var datatablegrn = function (num_index) {
            var grn_id_exist = '-';
            var n = 0;
            $('#datatable_detail tbody tr').each(function () {
                if ($(this).hasClass('hide') == false) {
                    var grn_id = parseInt($(this).find("input.is_grn").val()) || 0;
                    if (grn_id > 0) {
                        if(grn_id_exist == '-'){
                            grn_id_exist = '';
                        }
                        if(n > 0) {
                            grn_id_exist += '_';
                        }

                        grn_id_exist += grn_id;
                        n++;
                    }
                }
            });
            var supplier = parseInt($('#supplier_id').val()) || 0;

            if (supplier > 0) {
                grid_grn.init({
                    src: $("#datatable_grn"),
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
                            {"sClass": "text-center"},
                            {"sClass": "text-center"},
                            {"bSortable": false},
                            {"bSortable": false, "sClass": "text-center"}
                        ],
                        "aaSorting": [],
                        "lengthMenu": [
                            [10, 20, 50, 100, 150, -1],
                            [10, 20, 50, 100, 150, "All"] // change per page values here
                        ],
                        "pageLength": 10, // default record count per page
                        "ajax": {
                            "url": "<?php echo base_url('ap/invoice/ajax_grn_list');?>/" + supplier + "/" + grn_id_exist + "/" + num_index
                        }
                    }
                });

                var tableWrapper = $("#datatable_grn_wrapper");

                tableWrapper.find(".dataTables_length select").select2({
                    showSearchInput: false //hide search box with special css class
                });
            } else {
                toastr["warning"]("Please Select Supplier .", "Warning");
            }
        }

        $('.btn-select-grn').live('click', function (e) {
            e.preventDefault();
            var grn_id = $(this).attr('data-id');
            var grn_code = $(this).attr('data-code');
            var num_index = parseInt($(this).attr('data-index')) || 0;
            var dept_id = parseInt($(this).attr('data-dept')) || 0;
            var dept_name = $(this).attr('data-dept-name');
            var amount = parseFloat($(this).attr('data-amount')) || 0;

            $('input[name="charge_to[' + num_index + ']"]').val(grn_id);
            $('input[name="charge_to_code[' + num_index + ']"]').val(grn_code);
            $('input[name="tot_amount[' + num_index + ']"]').val(amount);
            $('select[name="dept_id[' + num_index + ']"]').val(dept_id);

            $('select[name="dept_id[' + num_index + ']"]').parent().find('span.select2-chosen').html(dept_name);

            calculate_rate();
            calculate_tot_amount();
            init_page();

            $('#ajax-modal').modal('hide');
        });
        //------- END GRN -------------//

        //------- START COA -------------//
        var grid_coa = new Datatable();
        var handleTableCOA = function (num_index)   {
            var coa_id_exist = '-';
            var n = 0;
            $('#datatable_detail tbody tr').each(function () {
                if ($(this).hasClass('hide') == false) {
                    var coa_id = parseInt($(this).find("input.is_coa").val()) || 0;
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
                        { "bSortable": false, "sClass": "text-center" },
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('purchasing/po/ajax_modal_coa_list');?>/" + 0 + '/' + num_index + '/' + coa_id_exist
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

        $('.btn-select-coa').live('click', function (e) {
            e.preventDefault();

            var coa_id = parseInt($(this).attr('data-id')) || 0;
            var coa_code = $(this).attr('data-code');
            var num_index = parseInt($(this).attr('data-index')) || 0;

            $('input[name="charge_to[' + num_index + ']"]').val(coa_id);
            $('input[name="charge_to_code[' + num_index + ']"]').val(coa_code);

            $('#ajax-modal').modal('hide');
        });
        //------- END COA -------------//

        $('.amount_detail').live('keyup', function(){
            var val = parseFloat($(this).val().trim()) || 0;
			var i = $(this).attr('data-index');

            var curr_rate = parseFloat($('#curr_rate').val().trim()) || 0;
			
			if($(this).hasClass('num_amount')){
                var local_tot_amount_ = (val * curr_rate).toFixed(2);
                var local_tot_amount = parseFloat(local_tot_amount_) || 0;
                $('input[name="local_tot_amount[' + i + ']"]').val(local_tot_amount);

                if(local_tot_amount < 0){
                    toastr.clear();
                    $('input[name="local_tot_amount[' + i + ']"]').val(val);

                    toastr["warning"]("invalid local amount.", "Warning");
                }
                calculate_tot_amount();
			}
        });
		
        function calculate_tot_amount(){
            var len = $('#datatable_detail tbody tr').length;
            if(len > 0){
                var tot = 0;
                var loc_tot = 0;
                for(var i = 0; i < len; i++ ){
                    var stat = $('input[name="status[' + i + ']"]').val();

                    if(stat != '<?php echo STATUS_DELETE;?>') {
                        var val = parseFloat($('input[name="tot_amount[' + i + ']"]').val()) || 0;
                        tot += val;

                        var val_l = parseFloat($('input[name="local_tot_amount[' + i + ']"]').val()) || 0;
                        loc_tot += val_l;
                    }
                }
                $('.num_total').val(tot);
                $('.num_loc_total').val(loc_tot);

                var percent = parseInt($('.c_tax_percent').val()) || 0;
                var tax_ = (tot * percent / 100).toFixed(2);
                var tax = parseFloat(tax_);
                $("input[name='taxtype_amount']").val(tax);

                var percent_wht = parseInt($('.totaltax_wht_percent').val()) || 0;
                var tax_wht_ = (tot * percent_wht / 100).toFixed(2);
                var tax_wht = parseFloat(tax_wht_);
                $(".totaltax_wht").val(tax_wht);

                calculate_grand_amount();
            }
        }

        function calculate_grand_amount() {
            var amount = parseFloat($('.num_total').val()) || 0;
            var tax = parseFloat($('input[name="taxtype_amount"]').val()) || 0;

            var tot = amount + tax;
            $('#totalgrand').val(tot);
        }

        function calculate_rate(){
            var len = $('#datatable_detail tbody tr').length;
            var curr_rate = parseFloat($('#curr_rate').val().trim()) || 0;

            if(len > 0){
                var tot = 0;
                for(var i = 0; i < len; i++ ){
                    var stat = $('input[name="status[' + i + ']"]').val();

                    if(stat != '<?php echo STATUS_DELETE;?>') {
                        var val = parseFloat($('input[name="tot_amount[' + i + ']"]').val()) || 0;
                        var local_tot_amount_ = (val * curr_rate).toFixed(2);
                        var local_tot_amount = parseFloat(local_tot_amount_);
                        $('input[name="local_tot_amount[' + i + ']"]').val(local_tot_amount);
                        tot += val;
                    }
                }
                $('.num_loc_total').val(tot);
            }
        }

		$('.num_rate').live('keyup', function(){
			calculate_rate(); 
		});

        $("input[name='taxtype_amount']").live('keyup', function() {
            var tax = parseFloat($(this).val()) || 0;
            var amount = parseFloat($('.num_total').val()) || 0;

            var tot = amount + tax;
            $('#totalgrand').val(tot);
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
            var inv_date = $('#inv_date').val().trim();
            var curr_rate = parseFloat($('#curr_rate').val().trim()) || 0;
            var remarks = $('textarea[name="inv_desc"]').val().trim();
            var tax_id = parseInt($('input[name="taxtype_id"]').val()) || 0;
            var tax_account = parseInt($('select[name="tax_account"]').val()) || 0;
            var term_ofpayment = $('input[name="term_ofpayment"]').val().trim();
            var tax_code = $('input[name="taxtype_code"]').val().trim();
            var currencytype_code = $('select[name="currencytype_id"] :selected').text().trim();

            if(supplier_id <= 0){
                toastr["warning"]("Please select supplier.", "Warning!");
                $('#supplier_id').closest('.form-group').addClass('has-error');
                result = false;
            }
            if(inv_date == ''){
                toastr["warning"]("Please select Invoice Date.", "Warning!");
                $('#inv_date').closest('.form-group').addClass('has-error');
                result = false;
            }
            if(curr_rate <= 0){
                toastr["warning"]("Please input valid currency rate.", "Warning!");
                $('#curr_rate').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (remarks == '') {
                toastr["warning"]("Please input remarks.", "Warning!");
                $('textarea[name="inv_desc"]').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (tax_id <= 0) {
                toastr["warning"]("Please select tax.", "Warning!");
                $('input[name="taxtype_id"]').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (tax_code != '<?php echo AP::NO_TAX;?>') {
                if (tax_account <= 0) {
                    toastr["warning"]("Please select tax account.", "Warning!");
                    $('select[name="tax_account"]').closest('.form-group').addClass('has-error');
                    result = false;
                }
            }
            if (term_ofpayment == '') {
                toastr["warning"]("Please input term of payment.", "Warning!");
                $('input[name="term_ofpayment"]').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (currencytype_code != '<?php echo Purchasing::CURR_IDR;?>') {
                if(curr_rate <= 1){
                    toastr["warning"]("Please input valid currency rate (" + currencytype_code + ").", "Warning!");
                    $('#curr_rate').closest('.form-group').addClass('has-error');
                    result = false;
                }
            }

            var i = 0;
            var i_act = 0;
            $('#datatable_detail > tbody > tr ').each(function() {
                if(!$(this).hasClass('hide')) {
                    var inv_actype = parseInt($('select[name="inv_actype[' + i + ']"]').val()) || 0;
                    var charge_to = parseInt($('input[name="charge_to[' + i + ']"]').val()) || 0;
                    var charge_to_code = $('input[name="charge_to_code[' + i + ']"]').val().trim();
                    var dept_id = parseInt($('select[name="dept_id[' + i + ']"]').val()) || 0;
                    var tot_amount = parseFloat($('input[name="tot_amount[' + i + ']"]').val()) || 0;
                    var local_tot_amount = parseFloat($('input[name="local_tot_amount[' + i + ']"]').val()) || 0;
                    var desc = (charge_to_code != '' ? ' for ' + charge_to_code : '');

                    if(inv_actype <= 0){
                        toastr["warning"]("Please select A/C Type.", "Warning");
                        $('select[name="inv_actype[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if(charge_to <= 0){
                        toastr["warning"]("Please select Charge To.", "Warning");
                        $('input[name="charge_to[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if(dept_id <= 0){
                        toastr["warning"]("Please select Department.", "Warning");
                        $('select[name="dept_id[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if(tot_amount <= 0){
                        toastr["warning"]("Please input valid Amount" + desc + ".", "Warning");
                        $('input[name="tot_amount[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if(local_tot_amount <= 0){
                        toastr["warning"]("Please input valid Local Total Amount" + desc + ".", "Warning");
                        $('input[name="local_tot_amount[' + i + ']"]').closest('td').addClass('has-error');
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
                    url: "<?php echo base_url('ap/invoice/ajax_invoice_submit');?>",
                    dataType: "json",
                    data: form_data
                })
                    .done(function( msg ) {
                        if(msg.valid == '0' || msg.valid == '1'){
                            if(msg.valid == '1'){
                                window.location.assign(msg.link);
                            } else {
                                toastr["error"](msg.message, "Error");
                                $('#form-entry').unblock();
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
            var inv_code = $(this).attr('data-code');
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
                                url: "<?php echo base_url('ap/invoice/ajax_invoice_action');?>",
                                dataType: "json",
                                data: {inv_id: id, action: action, reason: result, is_redirect: true}
                            })
                                .done(function (msg) {
                                    console.log(msg);
                                    if (msg.valid == '0' || msg.valid == '1') {
                                        if (msg.valid == '1') {
                                            //location.reload();
                                            location.href = '<?php echo base_url('ap/invoice/invoice_history/1/' . $inv_id . '.tpd');?>';
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
                bootbox.confirm("Are you sure want to " + action_code + " " + inv_code + " ?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('ap/invoice/ajax_invoice_action');?>",
                            dataType: "json",
                            data: {inv_id: id, action: action, is_redirect: true}
                        })
                            .done(function (msg) {
                                console.log(msg);
                                if (msg.valid == '0' || msg.valid == '1') {
                                    if (msg.valid == '1') {
                                        location.reload();
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
                });
            }
        });
        //------- END ACTION -----------//
	});
</script>