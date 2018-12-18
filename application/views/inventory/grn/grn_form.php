<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();
if($grn_id > 0) {
    if($row->status == STATUS_NEW){
        if(check_session_action(get_menu_id(), STATUS_EDIT)) {
            $btn_action .= $btn_save;
        } else {
            $isedit = false;
        }
        if(check_session_action(get_menu_id(), STATUS_POSTED)){
            $btn_action .= btn_action($grn_id, $row->grn_code, STATUS_POSTED);
        }
        if(check_session_action(get_menu_id(), STATUS_CANCEL)){
            $btn_action .= btn_action($grn_id, $row->grn_code, STATUS_CANCEL);
        }
    } else {
        $isedit = false;
        if($row->status == STATUS_POSTED) {
            if(check_session_action(get_menu_id(), STATUS_PRINT)) {
                $btn_action .= btn_print(base_url('inventory/grn/pdf_grn/' . $grn_id . '.tpd'));
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
							<i class="fa fa-user"></i><?php echo ($grn_id > 0 ? 'View' : 'New');?> GRN
                            <?php
                            if($grn_id > 0){
                                echo '&nbsp;&nbsp;&nbsp;' . get_status_name($row->status);
                            }
                            ?>
						</div>
						<div class="actions">
                            <?php
                            $back_url = base_url('inventory/grn/grn_manage.tpd');
                            if ($grn_id > 0) {
                                if ($row->status != STATUS_NEW) {
                                    $back_url = base_url('inventory/grn/grn_history.tpd');
                                }
                            }
                            echo btn_back($back_url);
                            ?>
						</div>
					</div>
					<div class="portlet-body form">
						<form method="post" id="form-entry" class="form-horizontal" onsubmit="return false">
							<input type="hidden" name="grn_id" value="<?php echo $grn_id;?>" />
							<div class="form-actions top">
								<div class="row">
									<div class="col-md-9">
                                        <?php
                                            echo $btn_action;
                                        ?>
									</div>
								</div>
							</div>
							<div class="form-body" id="form-entry-input">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label col-md-4">GRN Code</label>
											<div class="col-md-8">
												<input type="text" class="form-control" value="<?php echo ($grn_id > 0 ? $row->grn_code : '');?>" readonly />
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">GRN Date</label>
                                            <div class="col-md-4">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="grn_date" id="grn_date" value="<?php echo ($grn_id > 0 ? ymd_to_dmy($row->grn_date) : date('d-m-Y'));?>" readonly>
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
                                                    <input type="hidden" name="supplier_id" id="supplier_id" value="<?php echo ($grn_id > 0 ? $row->supplier_id : '0');?>" />
                                                    <input class="form-control" id="supplier_name" type="text" value="<?php echo ($grn_id > 0 ? $row->supplier_name : ''); ?>" readonly >
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_supplier" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">PO Code</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="po_id" id="po_id" value="<?php echo ($grn_id > 0 ? $row->po_id : '0');?>" />
                                                    <input class="form-control" id="po_code" type="text" value="<?php echo ($grn_id > 0 ? $row->po_code : ''); ?>" readonly >
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_po" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Curr / Rate</label>
                                            <div class="col-md-3">
                                                <select name="currencytype_id" class="select2me form-control">
                                                    <?php
                                                    $qry_curr = $this->db->get('currencytype');
                                                    foreach($qry_curr->result() as $row_curr){
                                                        echo '<option value="' . $row_curr->currencytype_id . '" ' . ($grn_id > 0 ? ($row->currencytype_id == $row_curr->currencytype_id ? 'selected="selected"' : '') : '') . '>' . $row_curr->currencytype_code . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="curr_rate" id="curr_rate" class="form-control mask_currency" value="<?php echo ($grn_id > 0 ? $row->curr_rate : '1');?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Vehicle No</label>
                                            <div class="col-md-6">
                                                <input type="text" name="vehicle_no" class="form-control" value="<?php echo ($grn_id > 0 ? $row->vehicle_no : '');?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">DO No</label>
                                            <div class="col-md-6">
                                                <input type="text" name="do_no"  class="form-control" value="<?php echo ($grn_id > 0 ? $row->do_no : '');?>" />
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-9">
                                                <textarea name="remarks" class="form-control" rows="2"><?php echo ($grn_id > 0 ? $row->remarks : '');?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="row">
                                    <div class="col-md-12">
                                        <a class="btn btn-sm green-haze yellow-stripe" id="btn_add_detail" style="margin-bottom: 10px;">
                                            <i class="fa fa-plus"></i>
                                            <span> &nbsp;&nbsp;Add Detail </span>
                                        </a>
                                    </div>
									<div class="col-md-12">
										<table class="table table-striped table-hover table-bordered" id="datatable_detail">
											<thead>
												<tr>
													<th class="text-center" width="120px"> Item Code </th>
													<th class="text-center"> Description </th>
                                                    <th class="text-center" width="140px"> UOM </th>
													<th class="text-center" width="120px"> Order Qty </th>
													<th class="text-center" width="120px"> Delivery Qty </th>
                                                    <th class="text-center" width="40px"> &nbsp; </th>
												</tr>
											</thead>
											<tbody>
                                            <?php
                                            if($grn_id > 0){
                                                $i = 0;
                                                foreach($qry_detail->result() as $row_detail){
                                                    echo '<tr>';
                                                    echo '<input type="hidden" name="grn_detail_id[' . $i . ']" value="' . $row_detail->grn_detail_id . '" />';
                                                    echo '<input type="hidden" class="input_po_detail_id" name="po_detail_id[' . $i . ']" value="' . $row_detail->po_detail_id . '" />';
                                                    echo '<input type="hidden" name="grn_item_type[' . $i . ']" value="' . $row_detail->grn_item_type . '" />';
                                                    echo '<input type="hidden" name="item_id[' . $i . ']" value="' . $row_detail->item_id . '" />';
                                                    echo '<input type="hidden" name="item_qty[' . $i . ']" value="' . $row_detail->item_qty . '" />';
                                                    echo '<input type="hidden" name="uom_id[' . $i . ']" value="' . $row_detail->uom_id . '" />';
                                                    echo '<input type="hidden" name="uom_factor[' . $i . ']" value="' . $row_detail->uom_factor . '" />';
                                                    echo '<input type="hidden" class="class_status" name="status[' . $i . ']" value="1" />';
                                                    echo '<td class="text-center text-middle" style="padding-top:13px;">' . $row_detail->item_code . '</td>
                                                            <td class="text-middle" style="padding-top:13px;">' . $row_detail->item_desc . '</td>
                                                            <td class="text-center text-middle">
                                                                <div class="input-group">
                                                                    <input data-index="' . $i . '" type="text" name="uom_code[' . $i .']" class="form-control  input-sm" value="' . $row_detail->uom_code . '[' . $row_detail->uom_factor . ']" readonly />
                                                                    <span class="input-group-btn">
                                                                        <button data-index="' . $i . '" class="btn btn-sm green-haze load_uom" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="text-right text-middle" style="padding-top:13px;"><span class="mask_currency">' . $row_detail->item_qty . '</span></td>
                                                            <td><input type="text" class="form-control input-sm text-right delivery_qty mask_currency" data-max="' . ($row_detail->item_delivery_qty + $row_detail->po_qty_remaining) . '" name="item_delivery_qty[' . $i . ']" value="' . $row_detail->item_delivery_qty . '" /></td>
                                                            <td class="text-center text-middle"><a class="btn btn-danger btn-xs tooltips btn-remove" data-original-title="Remove" href="javascript:;" data-index="' . $i . '" style="margin-right:0px;margin-top:4px;"><i class="fa fa-times"></i></a></td>
                                                        </tr>';
                                                    $i++;
                                                }
                                            }
                                            ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>

                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if($grn_id > 0){
                                        $log = '';
                                        $modified = '';
                                        $qry_log = $this->mdl_general->get('app_log', array('feature_id' => Feature::FEATURE_GRN, 'reff_id' => $grn_id), array(), 'log_id asc');
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
<div id="ajax-modal-small" data-width="480" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
	$(document).ready(function(){
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "50000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        var $modal = $('#ajax-modal');
        var $modal_small = $('#ajax-modal-small');

        <?php
        if($grn_id > 0){
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
            echo picker_input_date(true, true, ymd_to_dmy($row->grn_date));
        } else {
            echo picker_input_date();
        }
        ?>

        var isedit = <?php echo ($isedit ? 0 : 1); ?>;

        if(isedit > 0){
            $('#form-entry-input').block({
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

        //SUBMIT
        $('#form-entry').on('submit', function(){
            Metronic.blockUI({
                target: '#form-entry',
                boxed: true,
                message: 'Processing...'
            });

            var btn = $(this).find("button[type=submit]:focus" );

            toastr.clear();

            if(validate_submit()){
                var form_data = $('#form-entry').serializeArray();
                if (btn[0] == null){ }
                else {
                    if(btn[0].name === 'save_close'){
                        form_data.push({name: "save_close", value: 'save_close'});
                    }
                }

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('inventory/grn/ajax_grn_submit');?>",
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
                            $('#form-entry').unblock();
                        }
                    }).fail(function () {
                        $('#form-entry').unblock();
                        toastr["error"]("Something has wrong, please try again later.", "Error");
                    });
            } else {
                $('#form-entry').unblock();
            }
        });

        function validate_submit() {
            var result = true;

            if($('.form-group').hasClass('has-error')){
                $('.form-group').removeClass('has-error');
            }

            var supplier_id = parseInt($('#supplier_id').val()) || 0;
            var po_id = parseInt($('#po_id').val()) || 0;
            var grn_date = $('#grn_date').val().trim();
            var curr_rate = parseFloat($('#curr_rate').val().trim()) || 0;
            var remarks = $('textarea[name="remarks"]').val().trim();

            if(supplier_id <= 0){
                toastr["warning"]("Please select supplier.", "Warning!");
                result = false;
            }
            if(po_id <= 0){
                toastr["warning"]("Please select PO Code.", "Warning!");
                result = false;
            }
            if(grn_date == ''){
                toastr["warning"]("Please select GRN Date.", "Warning!");
                result = false;
            }
            if(curr_rate <= 0){
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
                    var delivery_qty = parseFloat($('input[name="item_delivery_qty[' + i + ']"]').val()) || 0;
                    var qty_max = parseFloat($('input[name="item_delivery_qty[' + i + ']"]').attr('data-max')) || 0;
                    var item_desc = $('.item_desc_' + i).html();

                    $('input[name="item_delivery_qty[' + i + ']"]').removeClass('has-error');

                    if(delivery_qty > qty_max){
                        toastr["warning"]("Item Qty cannot bigger than PO Qty Remain (" + item_desc + ").", "Warning");
                        $('input[name="item_delivery_qty[' + i + ']"]').addClass('has-error');
                        $('input[name="item_delivery_qty[' + i + ']"]').val(qty_max)
                        result = false;
                    }
                    if(delivery_qty <= 0){
                        toastr["warning"]("Please input valid Delivery Qty (" + item_desc + ").", "Warning");
                        $('input[name="item_delivery_qty[' + i + ']"]').addClass('has-error');
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

        $('.delivery_qty').live('keyup', function(){
            var val = parseFloat($(this).val().trim()) || 0;
            var max = parseFloat($(this).attr('data-max')) || 0;

            if(val > max){
                toastr.clear();

                $(this).val(max);
                toastr["warning"]("Delivery Qty can not bigger than Order Qty.", "Warning!");
            }
        });

        //SUPLIER
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

                        set_flag_delete(-1);
                    }
                });
            }
            else {
                $('#supplier_id').val(supplier_id);
                $('#supplier_name').val(supplier_name);

                $('#po_id').val('0');
                $('#po_code').val('');

                set_flag_delete(-1);
            }

            $('#ajax-modal').modal('hide');
        });

        //PO
        var grid_po = new Datatable();

        var datatablePO = function () {
            grid_po.init({
                src: $("#datatable_po"),
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
                        "url": "<?php echo base_url('inventory/grn/ajax_modal_po_list');?>/" + $('#supplier_id').val() + "/" + $('#po_id').val() // ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_po_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });
        }

        $('#btn_lookup_po').live('click', function (e) {
            e.preventDefault();

            var supplier_id = parseInt($('#supplier_id').val()) || 0;

            if(supplier_id > 0) {
                $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                    '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

                $('body').modalmanager('loading');


                setTimeout(function () {
                    $modal.load('<?php echo base_url('inventory/grn/ajax_modal_po');?>', '', function () {

                        $modal.modal();
                        datatablePO();

                        $.fn.modalmanager.defaults.resize = true;

                        if ($modal.hasClass('bootbox') == false) {
                            $modal.addClass('modal-fix');
                        }

                        if ($modal.hasClass('modal-overflow') == false) {
                            $modal.addClass('modal-overflow');
                        }

                        $modal.css({'margin-top': '0px'})

                    });
                }, 100);
            }
            else {
                toastr["warning"]("Please Select Supplier.", "Warning");
            }
        });

        $('.btn-select-po').live('click', function (e) {
            e.preventDefault();

            var po_id = $(this).attr('data-id');
            var po_code = $(this).attr('data-code');

            var old_po_id = $('#po_id').val();
            var next = true;
            if(parseInt(old_po_id) > 0){
                bootbox.confirm("Old PO will be deleted, continue?", function(result) {
                    if (result == true) {
                        $('#po_id').val(po_id);
                        $('#po_code').val(po_code);
                        $('#project_id').val(project_id);

                        //get_po_detail(po_id);
                    }
                    else {
                        next = false;
                    }
                });
            }
            else {
                $('#po_id').val(po_id);
                $('#po_code').val(po_code);

                //get_po_detail(po_id);
            }

            $('#ajax-modal').modal('hide');
        });

        var grid_po_detail = new Datatable();
        var datatablePODetail = function (po_id) {

            var po_detail_id_exist = '-';
            var n = 0;
            $('#datatable_detail tbody tr').each(function () {
                if ($(this).hasClass('hide') == false) {
                    if(po_detail_id_exist == '-'){
                        po_detail_id_exist = '';
                    }
                    if(n > 0) {
                        po_detail_id_exist += '_';
                    }
                    po_detail_id_exist += $(this).find(".input_po_detail_id").val();

                    n++;
                }
            });

            grid_po_detail.init({
                src: $("#datatable_po_detail"),
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
                        { "bSortable": false, "sClass": "text-center" },
                        { "bSortable": false },
                        { "bSortable": false, "sClass": "text-center" },
                        { "bSortable": false, "sClass": "text-right" },
                        { "bSortable": false, "sClass": "text-right" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('inventory/grn/ajax_modal_po_detail_list');?>/" + po_id + "/" + po_detail_id_exist
                    },
                    "fnDrawCallback": function( oSettings ) {
                        init_page();
                        Metronic.initUniform();
                    }
                }
            });

            var tableWrapper = $("#datatable_po_detail_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });

            grid_po_detail.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                toastr.clear();

                var i = $('#datatable_detail tbody tr').length;

                if (grid_po_detail.getSelectedRowsCount() > 0) {
                    var result = grid_po_detail.getSelectedRowsCheckbox();

                    $.each( result, function( index, value ){
                        var append_html = '<tr>' +
                                '<input type="hidden" name="grn_detail_id[' + i + ']" value="0" />' +
                                '<input type="hidden" class="input_po_detail_id" name="po_detail_id[' + i + ']" value="' + value[0] + '" />' +
                                '<input type="hidden" name="grn_item_type[' + i + ']" value="' + value[1] + '" />' +
                                '<input type="hidden" name="item_qty['  + i + ']" value="' + value[2] + '" />' +
                                '<input type="hidden" name="item_id['  + i + ']" value="' + value[8] + '" />' +
                                '<input type="hidden" name="uom_id[' + i + ']" value="' + value[3] + '" />' +
                                '<input type="hidden" name="uom_factor[' + i + ']" value="' + value[4] + '" />' +
                                '<input type="hidden" class="class_status" name="status[' + i + ']" value="1" />' +
                                '<td class="text-center" style="padding-top:13px;">' + value[5] + '</td>' +
                                '<td class="item_desc_' + i + '" style="padding-top:13px;">' + value[6] + '</td>' +
                                '<td class="text-center">' +
                                    '<div class="input-group">' +
                                    '<input data-index="' + i + '" type="text" name="uom_code[' + i + ']" class="form-control  input-sm" value="' + value[7] + '[' + value[4] + ']" readonly />' +
                                    '<span class="input-group-btn">' +
                                    '<button data-index="' + i + '" class="btn btn-sm green-haze load_uom" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>' +
                                    '</span>' +
                                    '</div>' +
                                '</td>' +
                                '<td class="text-right" style="padding-top:13px;"><span class="mask_currency">' + value[2] + '</span></td>' +
                                '<td><input type="text" class="form-control input-sm text-right delivery_qty mask_currency" data-max="' + value[2] + '" name="item_delivery_qty[' + i + ']" value="' + value[2] + '" /></td>' +
                                '<td class="text-center text-middle"><a class="btn btn-danger btn-xs tooltips btn-remove" style="margin-right:0px;margin-top:4px;" data-index="0" href="javascript:;" data-original-title="Remove"><i class="fa fa-times"></i></a></td>' +
                            '</tr>';

                        $('#datatable_detail tbody').append(append_html);

                        i++;
                    });

                    init_page();

                    $('#ajax-modal').modal('hide');
                } else if (grid_po_detail.getSelectedRowsCount() === 0) {
                    toastr["warning"]("Please Select Detail.", "Warning");
                }
            });
        }

        $('#btn_add_detail').live('click', function (e) {
            e.preventDefault();

            var po_id = parseInt($('input[name="po_id"]').val()) || 0;

            if(po_id > 0) {
                $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                    '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

                $('body').modalmanager('loading');

                setTimeout(function () {
                    $modal.load('<?php echo base_url('inventory/grn/ajax_modal_po_detail');?>', '', function () {

                        $modal.modal();
                        datatablePODetail(po_id);

                        $.fn.modalmanager.defaults.resize = true;

                        if ($modal.hasClass('bootbox') == false) {
                            $modal.addClass('modal-fix');
                        }

                        if ($modal.hasClass('modal-overflow') == false) {
                            $modal.addClass('modal-overflow');
                        }

                        $modal.css({'margin-top': '0px'})

                    });
                }, 100);
            }
            else {
                toastr["warning"]("Please Select PO Code.", "Warning");
            }
        });

        var grid_uom = new Datatable();
        //UOM
        var handleTableUOM = function (num_index, item_id, uom_id, uom_factor) {
            // Start Datatable Item
            grid_uom.init({
                src: $("#datatable_uom"),
                onSuccess: function (grid_uom) {
                    // execute some code after table records loaded
                },
                onError: function (grid_uom) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_uom) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "bSortable": false, "sClass": "text-center"},
                        { "bSortable": false, "sClass": "text-right"},
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [-1],
                        ["All"] // change per page values here
                    ],
                    "pageLength": -1, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('purchasing/po/ajax_modal_uom_list');?>/" + num_index + "/" + item_id + "/" + uom_id + "/" + uom_factor
                    }
                }
            });

            var tableWrapper = $("#datatable_uom_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('.load_uom').live('click', function (e) {
            var num_index = parseInt($(this).attr('data-index'));
            var item_id = parseInt($(this).closest('tr').find('input[name="item_id[' + num_index + ']"]').val()) || 0;
            var uom_id = parseInt($(this).closest('tr').find('input[name="uom_id[' + num_index + ']"]').val()) || 0;
            var uom_factor = parseInt($(this).closest('tr').find('input[name="uom_factor[' + num_index + ']"]').val()) || 0;

            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
                '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal_small.load('<?php echo base_url('purchasing/po/ajax_modal_uom');?>.tpd', '', function () {
                    $modal_small.modal();
                    handleTableUOM(num_index, item_id, uom_id, uom_factor);

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal_small.hasClass('bootbox') == false) {
                        $modal_small.addClass('modal-fix');
                    }

                    if ($modal_small.hasClass('modal-overflow') === false) {
                        $modal_small.addClass('modal-overflow');
                    }

                    $modal_small.css({'margin-top': '0px'});
                });
            }, 150);
        });

        $('.btn-select-uom').live('click', function (e) {
            e.preventDefault();

            var uom_id = parseInt($(this).attr('data-id')) || 0;
            var uom_factor = parseInt($(this).attr('data-factor')) || 0;
            var uom_code = $(this).attr('data-code');
            var num_index = parseInt($(this).attr('data-index')) || 0;

            $('input[name="uom_id[' + num_index + ']"]').val(uom_id);
            $('input[name="uom_factor[' + num_index + ']"]').val(uom_factor);
            $('input[name="uom_code[' + num_index + ']"]').val(uom_code);

            $modal_small.modal('hide');
        });

        $('.btn-remove').live('click', function(){
            var this_btn = $(this);
            bootbox.confirm("Are you sure want to delete?", function (result) {
                if (result == true) {
                    this_btn.closest('tr').addClass('hide');
                    this_btn.closest('tr').find('.class_status').val('9');
                }
            });
        });

        function set_flag_delete(i){
            if (i >= 0) {
                if ($('#datatable_detail tbody tr:nth-child(' + i + ')').hasClass('hide') == false) {
                    $('#datatable_detail tbody tr:nth-child(' + i + ')').addClass('hide');
                }
                if ($('#datatable_detail tbody tr:nth-child(' + i + ')').hasClass('class_status')) {
                    $('#datatable_detail tbody tr:nth-child(' + i + ')').val('9');
                }
            } else {
                $('#datatable_detail tbody tr').each(function () {
                    if ($(this).hasClass('hide') == false) {
                        $(this).addClass('hide');
                    }
                });

                $('#datatable_detail tbody tr input').each(function () {
                    if ($(this).hasClass('class_status')) {
                        $(this).val('0');
                    }
                });
            }
        }

        $('.btn-action').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var grn_code = $(this).attr('data-code');
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
                                url: "<?php echo base_url('inventory/grn/ajax_grn_action');?>",
                                dataType: "json",
                                data: {grn_id: id, action: action, reason: result, is_redirect: true}
                            })
                                .done(function (msg) {
                                    if (msg.valid == '0' || msg.valid == '1') {
                                        if (msg.valid == '1') {
                                            window.location = "<?php echo base_url('inventory/grn/grn_history/1/' . $grn_id);?>";
                                        } else {
                                            toastr["error"](msg.message, "Error");

                                            Metronic.unblockUI();
                                        }
                                    } else {
                                        toastr["error"]("Something has wrong, please try again later.", "Error");
                                        Metronic.unblockUI();
                                    }
                                })
                                .fail(function () {
                                    Metronic.unblockUI();
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                });
                        }
                    }
                });
            } else {
                bootbox.confirm("Are you sure want to " + action_code + " " + grn_code + " ?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('inventory/grn/ajax_grn_action');?>",
                            dataType: "json",
                            data: {grn_id: id, action: action, is_redirect: true}
                        })
                            .done(function (msg) {
                                if (msg.valid == '0' || msg.valid == '1') {
                                    if (msg.valid == '1') {
                                        window.location = "<?php echo base_url('inventory/grn/grn_history/1/' . $grn_id);?>";
                                    } else {
                                        toastr["error"](msg.message, "Error");
                                        Metronic.unblockUI();
                                    }
                                } else {
                                    toastr["error"]("Something has wrong, please try again later.", "Error");

                                    Metronic.unblockUI();
                                }
                            })
                            .fail(function () {
                                Metronic.unblockUI();
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                            });
                    }
                });
            }
        });
	});
</script>