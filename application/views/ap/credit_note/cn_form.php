<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();
if($creditnote_id > 0) {
    if($row->status == STATUS_NEW){
        if(check_session_action(get_menu_id(), STATUS_EDIT)) {
            $btn_action .= $btn_save;
        } else {
            $isedit = false;
        }
        if(check_session_action(get_menu_id(), STATUS_POSTED)){
            $btn_action .= btn_action($creditnote_id, $row->creditnote_code, STATUS_POSTED);
        }
        if(check_session_action(get_menu_id(), STATUS_CANCEL)){
            $btn_action .= btn_action($creditnote_id, $row->creditnote_code, STATUS_CANCEL);
        }
    } else {
        $isedit = false;
        if($row->status == STATUS_POSTED) {
            if(check_session_action(get_menu_id(), STATUS_PRINT)) {
                $btn_action .= btn_print(base_url('ap/credit_note/pdf_cn/' . $creditnote_id . '.tpd'));
                $btn_action .= btn_print(base_url('ap/credit_note/pdf_jv_cn/' . $creditnote_id . '.tpd'), 'Print JV');
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
							<i class="fa fa-user"></i><?php echo ($creditnote_id > 0 ? 'View' : 'New');?> Credit Note
                            <?php
                            if($creditnote_id > 0){
                                echo '&nbsp;&nbsp;&nbsp;' . get_status_name($row->status);
                            }
                            ?>
						</div>
						<div class="actions">
                            <?php
                            $back_url = base_url('ap/credit_note/cn_manage.tpd');
                            if ($creditnote_id > 0) {
                                if ($row->status != STATUS_NEW) {
                                    $back_url = base_url('ap/credit_note/cn_history.tpd');
                                }
                            }
                            echo btn_back($back_url);
                            ?>
						</div>
					</div>
					<div class="portlet-body form">
						<form method="post" id="id_form_input" class="form-horizontal" onsubmit="return false">
							<input type="hidden" name="creditnote_id" value="<?php echo $creditnote_id;?>" />
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
											<label class="control-label col-md-4">CN No</label>
											<div class="col-md-6">
												<input type="text" class="form-control" value="<?php echo ($creditnote_id > 0 ? $row->creditnote_code : '');?>" readonly />
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-md-4">Supplier</label>
											<div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="supplier_id" id="supplier_id" value="<?php echo ($creditnote_id > 0 ? $row->supplier_id : '0');?>" />
                                                    <input class="form-control" id="supplier_name" type="text" value="<?php echo ($creditnote_id > 0 ? $row->supplier_name : ''); ?>" readonly >
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_supplier" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Invoice No</label>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <input type="hidden" name="inv_id" id="inv_id" value="<?php echo ($creditnote_id > 0 ? $row->inv_id : '0');?>" />
                                                    <input class="form-control" id="inv_code" type="text" value="<?php echo ($creditnote_id > 0 ? $row->inv_code : ''); ?>" readonly >
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_inv" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">CN Dates</label>
                                            <div class="col-md-4">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="creditnote_date" id="creditnote_date" value="<?php echo ($creditnote_id > 0 ? ymd_to_dmy($row->creditnote_date) : date('d-m-Y'));?>" readonly>
													<span class="input-group-btn">
														<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Ref No</label>
                                            <div class="col-md-6">
                                                <input type="text" name="ref_no" class="form-control" value="<?php echo ($creditnote_id > 0 ? $row->ref_no : '');?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Curr / Rate</label>
                                            <div class="col-md-3">
                                                <select name="currencytype_id" class="select2me form-control">
                                                    <?php
                                                    foreach($qry_curr->result() as $row_curr){
                                                        echo '<option value="' . $row_curr->currencytype_id . '" ' . ($creditnote_id > 0 ? ($row->currencytype_id == $row_curr->currencytype_id ? 'selected="selected"' : '') : '') . '>' . $row_curr->currencytype_code . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="curr_rate" id="curr_rate" class="form-control mask_currency num_rate" value="<?php echo ($creditnote_id > 0 ? $row->curr_rate : '1');?>" />
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-10">
												<textarea class="form-control" rows="2" name="remarks"   ><?php echo ($creditnote_id > 0 ? $row->remarks : '');?></textarea>
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
										<table class="table table-striped table-hover table-bordered" id="datatable_detail">
											<thead>
												<tr>
                                                    <th class="text-center" width="140px"> COA Code </th>
                                                    <th class="text-center" width="200px"> COA Desc </th>
                                                    <th class="text-center" width="90px"> Dept </th>
													<th class="text-center"> Note </th>
                                                    <th class="text-center" width="140px"> CN Amount </th>
                                                    <th class="text-center" width="140px"> CN Local Amount </th>
                                                    <th class="text-center" width="40px"> Action </th>
												</tr>
											</thead>
											<tbody>
                                            <?php
                                            $total = 0;
                                            $local_total = 0;
                                            if($creditnote_id > 0){
                                                $i = 0;
                                                foreach ($qry_detail->result() as $row_detail) {
                                                    $opt_dept = '';
                                                    foreach($qry_dept->result() as $row_dept){
                                                        $opt_dept .= '<option value="' . $row_dept->department_id . '" ' . ($row_detail->dept_id == $row_dept->department_id ? 'selected="selected"' : '') . '>' . $row_dept->department_name . '</option>';
                                                    }

                                                    $new_row = '<tr>
                                                        <input data-index="' . $i . '" type="hidden" class="class_status" name="status[' . $i . ']" value="1"/>
                                                        <input data-index="' . $i . '" type="hidden" name="detail_id['. $i . ']" value="' . $row_detail->detail_id . '"/>
                                                        <td>
                                                            <div class="input-group">
                                                                <input data-index="' . $i . '" type="hidden" name="coa_id[' . $i . ']" class="is_coa" value="' . $row_detail->coa_id . '" />
                                                                <input data-index="' . $i . '" type="text" name="coa_code[' . $i . ']" class="form-control input-sm" value="' . $row_detail->coa_code . '" readonly />
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-sm green-haze load_coa" data-index="' . $i . '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td style="padding-top:12px;" class="coa_desc_' . $i . '">' . $row_detail->coa_desc . '</td>
                                                        <td>
                                                            <select name="dept_id[' . $i . ']" class="form-control input-sm select2me">
                                                                <option value="0" >--Select--</option>
                                                                ' . $opt_dept . '
                                                            </select>
                                                        </td>
                                                        <td><textarea data-index="' . $i . '" name="notes[' . $i . ']" class="form-control input-sm">' . $row_detail->notes . '</textarea></td>
                                                        <td><input type="text" data-index="' . $i . '" name="amount[' . $i . ']" value="' . $row_detail->amount . '" class="form-control input-sm mask_currency amount_detail"></td>
                                                        <td><input type="text" readonly data-index="' . $i . '"  name="local_amount[' . $i . ']" value="' . $row_detail->local_amount . '" class="form-control text-right input-sm mask_currency amount_loc_detail"></td>
                                                        <td class="text-center text-middle">
                                                            <a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' . $i . '" style="margin-right:0px;margin-top:4px;"><i class="fa fa-times"></i></a>
                                                            </td>
                                                        </tr>';

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
                                                    <th colspan="4" class="text-right" style="vertical-align: middle;">Total</th>
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
                                    if($creditnote_id > 0){
                                        $log = '';
                                        $modified = '';
                                        $qry_log = $this->mdl_general->get('app_log', array('feature_id' => Feature::FEATURE_AP_CREDIT_NOTE, 'reff_id' => $creditnote_id), array(), 'log_id asc');
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
            "timeOut": "10000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        var $modal = $('#ajax-modal');

        <?php
        if($creditnote_id > 0){
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
            echo picker_input_date(true, true, ymd_to_dmy($row->creditnote_date));
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

        //----- START INVOICE ------------//
        var grid_inv = new Datatable();
        var datatableInvoice = function () {
            var supplier_id = $('#supplier_id').val();
            var inv_id = $('#inv_id').val();

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
                        "url": "<?php echo base_url('ap/debit_note/ajax_modal_invoice_list');?>/" + supplier_id + '/' + inv_id
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

        $('#btn_lookup_inv').live('click', function (e) {
            e.preventDefault();

            var supplier_id = parseInt($('#supplier_id').val()) || 0;

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
                        datatableInvoice();

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

            var old_inv_id = parseInt($('#inv_id').val()) || 0;
            if(old_inv_id > 0){
                bootbox.confirm("Old Invoice will be deleted, continue?", function(result) {
                    if (result == true) {
                        $('#inv_id').val(inv_id);
                        $('#inv_code').val(inv_code);

                        set_flag_delete();
                    }
                });
            } else {
                $('#inv_id').val(inv_id);
                $('#inv_code').val(inv_code);
            }

            $('#ajax-modal').modal('hide');
        });
        //----- END INVOICE ------------//

        //----- START DETAIL ------------//
        $('#btn_add_detail').live('click', function (e) {
            e.preventDefault();

            var inv_id = parseInt($('#inv_id').val()) || 0;

            if (inv_id > 0) {
                var i = $('#datatable_detail tbody tr').length;

                var new_row = '<tr>' +
                    '<input data-index="' + i + '" type="hidden" class="class_status" name="status[' + i + ']" value="1"/>' +
                    '<input data-index="' + i + '" type="hidden" name="detail_id[' + i + ']" value="0"/>' +
                    '<td>' +
                    '<div class="input-group">' +
                    '<input data-index="' + i + '" type="hidden" name="coa_id[' + i + ']" class="is_coa" value="0" />' +
                    '<input data-index="' + i + '" type="text" name="coa_code[' + i + ']" class="form-control input-sm" value="" readonly />' +
                    '<span class="input-group-btn">' +
                    '<button class="btn btn-sm green-haze load_coa" data-index="' + i + '" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                    '</span>' +
                    '</div>' +
                    '</td>' +
                    '<td style="padding-top:12px;" class="coa_desc_' + i + '"></td>' +
                    '<td>' +
                    '<select name="dept_id[' + i + ']" class="form-control input-sm select2me">' +
                    '<option value="0" >--Select--</option>' +
                    <?php
                        foreach($qry_dept->result() as $row_dept){
                    ?>
                    '<option value="<?php echo $row_dept->department_id; ?>" ><?php echo $row_dept->department_name; ?></option>' +
                    <?php
                        }
                    ?>
                    '</select>' +
                    '</td>' +
                    '<td><textarea data-index="' + i + '" name="notes[' + i + ']" class="form-control input-sm"></textarea></td>' +
                    '<td><input type="text" data-index="' + i + '" name="amount[' + i + ']" value="0" class="form-control input-sm mask_currency amount_detail"></td>' +
                    '<td><input type="text" readonly data-index="' + i + '"  name="local_amount[' + i + ']" value="0" class="form-control text-right input-sm mask_currency amount_loc_detail"></td>' +
                    '<td class="text-center text-middle">' +
                    '<a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' + i + '" style="margin-right:0px;margin-top:4px;"><i class="fa fa-times"></i></a>' +
                    '</td>' +
                    '</tr>';

                $('#datatable_detail tbody').append(new_row);

                init_page();
            } else {
                toastr["warning"]("Please select Invoice.", "Warning");
            }
        });
        //----- END DETAIL ------------//

        //------- START COA -------------//
        var grid_coa = new Datatable();
        var handleTableCOA = function (num_index, coa_id_exist) {
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

        $('.load_coa').live('click', function (e) {
            e.preventDefault();

            var num_index = $(this).attr('data-index');
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

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('purchasing/po/ajax_modal_coa');?>', '', function () {

                    $modal.modal();
                    handleTableCOA(num_index, coa_id_exist);

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

        $('.btn-select-coa').live('click', function (e) {
            e.preventDefault();

            var coa_id = parseInt($(this).attr('data-id')) || 0;
            var coa_code = $(this).attr('data-code');
            var coa_desc = $(this).attr('data-desc');
            var i = parseInt($(this).attr('data-index')) || 0;

            $('input[name="coa_id[' + i + ']"]').val(coa_id);
            $('input[name="coa_code[' + i + ']"]').val(coa_code);
            $('.coa_desc_' + i).html(coa_desc);

            init_page();

            $('#ajax-modal').modal('hide');
        });
        //------- END COA -------------//

        $('.amount_detail').live('keyup', function(){
            toastr.clear();

            var val = parseFloat($(this).val().trim()) || 0;
			var i = $(this).attr('data-index');

            var curr_rate = parseFloat($('#curr_rate').val().trim()) || 0;

            var local_tot_amount_ = (val * curr_rate).toFixed(2);
            var local_tot_amount = parseFloat(local_tot_amount_) || 0;
            $('input[name="local_amount[' + i + ']"]').val(local_tot_amount);

            if(local_tot_amount < 0){
                $('input[name="local_amount[' + i + ']"]').val(val);

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
            var creditnote_date = $('#creditnote_date').val().trim();
            var curr_rate = parseFloat($('#curr_rate').val().trim()) || 0;
            var remarks = $('textarea[name="remarks"]').val().trim();
            var inv_id = parseInt($('#inv_id').val()) || 0;

            if (supplier_id <= 0) {
                toastr["warning"]("Please select Supplier.", "Warning!");
                $('#supplier_id').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (inv_id <= 0) {
                toastr["warning"]("Please select Invoice.", "Warning!");
                $('#inv_id').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (creditnote_date == '') {
                toastr["warning"]("Please select Credit Note Date.", "Warning!");
                $('#creditnote_date').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (curr_rate <= 0) {
                toastr["warning"]("Please input valid currency rate.", "Warning!");
                $('#curr_rate').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (remarks == '') {
                toastr["warning"]("Please input remarks.", "Warning!");
                $('textarea[name="remarks"]').closest('.form-group').addClass('has-error');
                result = false;
            }

            var i = 0;
            var i_act = 0;
            $('#datatable_detail > tbody > tr ').each(function() {
                if(!$(this).hasClass('hide')) {
                    var coa_id = parseInt($('input[name="coa_id[' + i + ']"]').val()) || 0;
                    var det_desc_ = $('.coa_desc_' + i).text().trim();
                    var det_desc = '';
                    if (det_desc_ != '') {
                        det_desc = ' for ' + det_desc_;
                    }
                    var dept_id = parseInt($('select[name="dept_id[' + i + ']"]').val()) || 0;
                    var notes = $('textarea[name="notes[' + i + ']"]').val().trim();
                    var tot_amount = parseFloat($('input[name="amount[' + i + ']"]').val()) || 0;
                    var local_tot_amount = parseFloat($('input[name="local_amount[' + i + ']"]').val()) || 0;

                    if (coa_id <= 0) {
                        toastr["warning"]("Please select COA.", "Warning");
                        $('input[name="coa_id[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if (dept_id <= 0) {
                        toastr["warning"]("Please select Department" + det_desc + ".", "Warning");
                        $('select[name="dept_id[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if (notes == '') {
                        toastr["warning"]("Please input Notes" + det_desc + ".", "Warning");
                        $('textarea[name="notes[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if (tot_amount <= 0) {
                        toastr["warning"]("Please input valid Amount" + det_desc + ".", "Warning");
                        $('input[name="amount[' + i + ']"]').closest('td').addClass('has-error');
                        result = false;
                    }
                    if (local_tot_amount <= 0) {
                        toastr["warning"]("Please input valid Local Total Amount" + det_desc + ".", "Warning");
                        $('input[name="local_amount[' + i + ']"]').closest('td').addClass('has-error');
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
                    url: "<?php echo base_url('ap/credit_note/ajax_cn_submit');?>",
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
                                url: "<?php echo base_url('ap/credit_note/ajax_cn_action');?>",
                                dataType: "json",
                                data: {creditnote_id: id, action: action, reason: result, is_redirect: true}
                            })
                                .done(function (msg) {
                                    if (msg.valid == '0' || msg.valid == '1') {
                                        if (msg.valid == '1') {
                                            location.href = '<?php echo base_url('ap/credit_note/cn_history/1/' . $creditnote_id . '.tpd');?>';
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
                                }).fail(function() {
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                    Metronic.unblockUI();
                                });
                        }
                    }
                });
            } else {
                bootbox.confirm("Are you sure want to " + action_code + " " + data_code + " ?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('ap/credit_note/ajax_cn_action');?>",
                            dataType: "json",
                            data: {creditnote_id: id, action: action, is_redirect: true}
                        })
                            .done(function (msg) {
                                if (msg.valid == '0' || msg.valid == '1') {
                                    if (msg.valid == '1') {
                                        location.href = '<?php echo base_url('ap/credit_note/cn_history/1/' . $creditnote_id . '.tpd');?>';
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