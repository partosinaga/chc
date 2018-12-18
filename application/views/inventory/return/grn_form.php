<?php
$isedit = true;

$btn_action = '';
$btn_save = '<button type="submit" class="btn blue-madison" name="save"><i class="fa fa-save"></i> &nbsp;&nbsp;Save </button>
<button type="submit" class="btn blue-madison" name="save_close"><i class="fa fa-sign-in"></i> &nbsp;&nbsp; Save & Close </button>';
if($grn_id > 0) {
    if($row->status == STATUS_NEW){
        if(check_session_action(get_menu_id(), STATUS_EDIT)) {
            $btn_action .= $btn_save;
        }
        else {
            $isedit = false;
        }
        if(check_session_action(get_menu_id(), STATUS_POSTED)){
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-action" data-action="' . STATUS_POSTED . '" data-id="' . $row->grn_id . '"><i class="fa fa-check-square-o"></i> &nbsp;&nbsp;' . ucwords(strtolower(get_action_name(STATUS_POSTED, false))) . '</a>';
        }
        if(check_session_action(get_menu_id(), STATUS_CANCEL)){
            $btn_action .= '&nbsp;<a class="btn yellow-gold btn-action" data-action="' . STATUS_CANCEL . '" data-id="' . $row->grn_id . '"><i class="fa fa-exclamation-triangle"></i> &nbsp;&nbsp;' . ucwords(strtolower(get_action_name(STATUS_CANCEL, false))) . '</a>';
        }
    } else {
        $isedit = false;
        $btn_action .= '&nbsp;<a href="' . base_url('inventory/grn/pdf_grn/' . $grn_id . '.tpd') . '" target="_blank" class="btn yellow-gold"><i class="fa fa-print"></i> &nbsp;&nbsp;Print</a>';
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
							<a href="<?php echo ($grn_id > 0 ? ($row->status == STATUS_NEW ? base_url('inventory/grn/grn_manage.tpd') : base_url('inventory/grn/grn_history.tpd')) : base_url('inventory/grn/grn_manage.tpd'));?>" class="btn default yellow-stripe">
							<i class="fa fa-arrow-circle-left "></i><span class="hidden-480"> &nbsp;&nbsp;Back </span></a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="<?php echo base_url('inventory/grn/grn_submit.tpd');?>" method="post" id="id_form_input" class="form-horizontal" onsubmit="return false">
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
							<div class="form-body" id="form-entry">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label col-md-4">GRN Code</label>
											<div class="col-md-8">
												<input type="text" class="form-control" value="<?php echo ($grn_id > 0 ? $row->grn_code : '');?>" readonly />
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">GRN Dates <span class="required" aria-required="true"> * </span></label>
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
											<label class="control-label col-md-4">Supplier <span class="required" aria-required="true"> * </span></label>
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
                                            <label class="control-label col-md-4">PO Code <span class="required" aria-required="true"> * </span></label>
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
                                            <div class="col-md-8">
                                                <input type="text" name="vehicle_no" class="form-control" value="<?php echo ($grn_id > 0 ? $row->vehicle_no : '');?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">DO No</label>
                                            <div class="col-md-8">
                                                <input type="text" name="do_no"  class="form-control" value="<?php echo ($grn_id > 0 ? $row->do_no : '');?>" />
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-10">
                                                <input type="text" name="remarks" class="form-control" value="<?php echo ($grn_id > 0 ? $row->remarks : '');?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br/>
								<div class="row">
									<div class="col-md-12">
										<table class="table table-striped table-hover table-bordered" id="datatable_detail">
											<thead>
												<tr>
													<th class="text-center"> # </th>
													<th class="text-center"> Item Code </th>
													<th class="text-center"> Description </th>
                                                    <th class="text-center"> UOM </th>
													<th class="text-center"> Order Qty </th>
													<th class="text-center"> Delivery Qty </th>
												</tr>
											</thead>
											<tbody>
                                            <?php
                                            if($grn_id > 0){
                                                $i = 1;
                                                foreach($qry_detail->result() as $row_detail){
                                                    echo '<tr>';
                                                    echo '<input type="hidden" name="grn_detail_id[' . $i . ']" value="' . $row_detail->grn_detail_id . '" />';
                                                    echo '<input type="hidden" name="po_detail_id[' . $i . ']" value="' . $row_detail->po_detail_id . '" />';
                                                    echo '<input type="hidden" name="grn_item_type[' . $i . ']" value="' . $row_detail->grn_item_type . '" />';
                                                    echo '<input type="hidden" name="item_qty[' . $i . ']" value="' . $row_detail->item_qty . '" />';
                                                    echo '<input type="hidden" name="buy_uom_id[' . $i . ']" value="' . $row_detail->buy_uom_id . '" />';
                                                    echo '<input type="hidden" name="uom_factor[' . $i . ']" value="' . $row_detail->uom_factor . '" />';
                                                    echo '<input type="hidden" class="class_status" name="status[' . $i . ']" value="1" />';
                                                    echo '<td class="text-center">' . $i . '.</td>
                                                            <td class="text-center">' . $row_detail->item_code . '</td>
                                                            <td class="text-center">' . $row_detail->item_desc . '</td>
                                                            <td class="text-center">' . $row_detail->uom_code . ' (' . $row_detail->uom_factor . ')</td>
                                                            <td class="text-right">' . format_num($row_detail->item_qty, 0) . '</td>
                                                            <td><input type="text" class="form-control text-right delivery_qty mask_number" data-max="' . $row_detail->item_qty . '" name="item_delivery_qty[' . $i . ']" value="' . $row_detail->item_delivery_qty . '" /></td>
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

<div id="ajax-modal" class="modal fade" data-replace="true" data-width="900" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

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
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        var $modal = $('#ajax-modal');

        <?php
        if($grn_id > 0){
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
        }
        ?>

        var isedit = <?php echo ($isedit ? 0 : 1); ?>;

        if(isedit > 0){
            $('#form-entry').block({
                message: null ,
                overlayCSS: {backgroundColor: '#EDF5EB', opacity:0, cursor:'default'}
            });
        }

		if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        $(".mask_currency").inputmask("decimal",{
            radixPoint:",",
            groupSeparator: ".",
            digits: 2,
            autoGroup: true
        });

        //SUBMIT
        $('#id_form_input').on('submit', function(){
            Metronic.blockUI({
                target: '#id_form_input',
                boxed: true,
                message: 'Processing...'
            });


            var btn = $(this).find("button[type=submit]:focus" );

            var next = true;
            toastr.clear();

            var supplier_id = parseInt($('#supplier_id').val());
            var po_id = parseInt($('#po_id').val());
            var grn_date = $('#po_id').val().trim();
            var curr_rate = $('#curr_rate').val().trim();

            if(supplier_id <= 0){
                toastr["warning"]("Please select supplier.", "Warning!");
                next = false;
            }
            if(po_id <= 0){
                toastr["warning"]("Please select PO Code.", "Warning!");
                next = false;
            }
            if(grn_date == ''){
                toastr["warning"]("Please select GRN Date.", "Warning!");
                next = false;
            }
            if(curr_rate == ''){
                toastr["warning"]("Please input valid currency rate.", "Warning!");
                next = false;
            }

            if(next){
                var form_data = $('#id_form_input').serializeArray();
                if (btn[0] == null){ }
                else {
                    if(btn[0].name === 'save_close'){
                        form_data.push({name: "save_close", value: 'save_close'});
                    }
                }

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('inventory/grn/grn_submit');?>",
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
                                Metronic.unblockUI();
                            }
                        }
                        else {
                            toastr["error"]("Something has wrong, please try again later.", "Error");
                            Metronic.unblockUI();
                        }
                    });
            }
            else {
                Metronic.unblockUI();
            }
        });

        $(".mask_number").inputmask({
            "mask": "9",
            "repeat": 10,
            "greedy": false
        });

        $('.delivery_qty').live('keyup', function(){
            var val = $(this).val().trim();
            var max = parseInt($(this).attr('data-max'));
            if(val == ''){
                val = 0;
            }
            val = parseInt(val);

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
                        null,
                        null,
                        null,
                        { "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('inventory/grn/ajax_supplier_list');?>/" + $('#supplier_id').val() // ajax source
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
                        null,
                        null,
                        null,
                        null,
                        { "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('inventory/grn/ajax_po_list');?>/" + $('#supplier_id').val() + "/" + $('#po_id').val() // ajax source
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

            var supplier_id = $('#supplier_id').val();
            supplier_id = parseInt(supplier_id);

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

                        get_po_detail(po_id);
                    }
                    else {
                        next = false;
                    }
                });
            }
            else {
                $('#po_id').val(po_id);
                $('#po_code').val(po_code);

                get_po_detail(po_id);
            }

            $('#ajax-modal').modal('hide');
        });

        function get_po_detail(po_id){
            Metronic.blockUI({
                target: '#ajax-modal',
                boxed: true,
                message: 'Processing...'
            });

            var tr_row = $('#datatable_detail tbody tr').length;

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('inventory/grn/ajax_po_detail');?>/" + po_id + '/' + tr_row,
                dataType: "json"
            })
                .done(function( msg ) {
                    Metronic.unblockUI();
                    $('#ajax-modal').modal('hide');

                    if(msg.type == '0' || msg.type == '1'){

                        if(msg.type == '1'){
                            set_flag_delete();
                            $('#datatable_detail tbody').append(msg.message);

                            $(".mask_number").inputmask({
                                "mask": "9",
                                "repeat": 10,
                                "greedy": false
                            });
                        }
                        else {
                            toastr["error"](msg.message, "Error");
                        }
                    }
                    else {
                        toastr["danger"]("Something has wrong, please try again later.", "Error");
                    }
                });
        }

        function set_flag_delete(){
            $('#datatable_detail tbody tr').each(function(){
                if($(this).hasClass('hide') == false){
                    $(this).addClass('hide');
                }
            });

            $('#datatable_detail tbody tr input').each(function(){
                if($(this).hasClass('class_status')){
                    $(this).val('0');
                }
            });
        }

        $('.btn-action').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');

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
                                });
                        }
                    }
                });
            } else {
                bootbox.confirm("Are you sure?", function (result) {
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
                            });
                    }
                });
            }
        });
	});
</script>