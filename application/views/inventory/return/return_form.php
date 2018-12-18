<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();
if($return_id > 0) {
    if($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE){
        if(check_session_action(get_menu_id(), STATUS_EDIT)) {
            $btn_action .= $btn_save;
        } else {
            $isedit = false;
        }
        if(check_session_action(get_menu_id(), STATUS_APPROVE)){
            $btn_action .= btn_action($return_id, $row->return_code, STATUS_APPROVE);
        }
        if(check_session_action(get_menu_id(), STATUS_CANCEL)){
            $btn_action .= btn_action($return_id, $row->return_code, STATUS_CANCEL);
        }
    } else if($row->status == STATUS_APPROVE) {
        $isedit = false;
        if(check_session_action(get_menu_id(), STATUS_POSTED)){
            $btn_action .= btn_action($return_id, $row->return_code, STATUS_POSTED);
        }
        if(check_session_action(get_menu_id(), STATUS_DISAPPROVE)){
            $btn_action .= btn_action($return_id, $row->return_code, STATUS_DISAPPROVE);
        }
        if(check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(base_url('inventory/retur/pdf_return/' . $return_id . '.tpd'));
        }
    } else if($row->status == STATUS_POSTED) {
        if(check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(base_url('inventory/retur/pdf_return/' . $return_id . '.tpd'));
        }
        $isedit = false;
    } else {
        $isedit = false;
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
							<i class="fa fa-user"></i><?php echo ($return_id > 0 ? 'View' : 'New');?> Return
                            <?php
                            if($return_id > 0){
                                echo '&nbsp;&nbsp;&nbsp;' . get_status_name($row->status);
                            }
                            ?>
						</div>
						<div class="actions">
                            <?php
                            $back_url = base_url('inventory/retur/return_manage.tpd');
                            if ($return_id > 0) {
                                if ($row->status != STATUS_NEW && $row->status != STATUS_DISAPPROVE && $row->status != STATUS_APPROVE) {
                                    $back_url = base_url('inventory/retur/return_history.tpd');
                                }
                            }
                            echo btn_back($back_url);
                            ?>
						</div>
					</div>
					<div class="portlet-body form">
						<form method="post" id="id_form_input" class="form-horizontal" onsubmit="return false">
							<input type="hidden" name="return_id" value="<?php echo $return_id;?>" />
							<div class="form-actions top">
								<div class="row">
									<div class="col-md-9">
                                        <?php echo $btn_action; ?>
									</div>
								</div>
							</div>
							<div class="form-body" id="form-entry">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label col-md-4">Return Code</label>
											<div class="col-md-8">
												<input type="text" class="form-control" value="<?php echo ($return_id > 0 ? $row->return_code : '');?>" readonly />
											</div>
										</div>
									</div>
									<div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Return Date</label>
                                            <div class="col-md-4">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="return_date" id="return_date" value="<?php echo ($return_id > 0 ? ymd_to_dmy($row->return_date) : date('d-m-Y'));?>" readonly>
													<span class="input-group-btn">
														<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Supplier</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="supplier_id" id="supplier_id" value="<?php echo ($return_id > 0 ? $row->supplier_id : '0');?>" />
                                                    <input class="form-control" id="supplier_name" type="text" value="<?php echo ($return_id > 0 ? $row->supplier_name : ''); ?>" readonly >
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_supplier" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">GRN Code</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="grn_id" id="grn_id" value="<?php echo ($return_id > 0 ? $row->grn_id : '0');?>" />
                                                    <input class="form-control" id="grn_code" type="text" value="<?php echo ($return_id > 0 ? $row->grn_code : ''); ?>" readonly >
                                                    <span class="input-group-btn">
                                                        <a id="btn_lookup_grn" class="btn btn-success" href="javascript:;">
                                                            <i class="fa fa-arrow-up fa-fw"></i>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">PO Code</label>
                                            <div class="col-md-6">
                                                <input id="po_code" type="text" class="form-control" value="<?php echo ($return_id > 0 ? $row->po_code : '');?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remarks</label>
                                            <div class="col-md-10">
                                                <textarea name="remarks" class="form-control" rows="2"><?php echo ($return_id > 0 ? $row->remarks : '');?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="row">
                                    <div class="col-md-12" style="margin-bottom: 10px;">
                                        <?php echo btn_add_detail(); ?>
                                    </div>
									<div class="col-md-12">
										<table class="table table-striped table-hover table-bordered table-v-middle" id="datatable_detail">
											<thead>
												<tr>
													<th class="text-center" width="100px"> Item Code </th>
													<th class="text-center"> Description </th>
                                                    <th class="text-center" width="100px"> UOM </th>
                                                    <th class="text-center" width="100px"> GRN Qty </th>
													<th class="text-center" width="100px"> Qty </th>
													<th class="text-center" width="60px"> Action </th>
												</tr>
											</thead>
											<tbody>
                                            <?php
                                            if($return_id > 0){
                                                $i = 0;
                                                foreach($qry_detail->result() as $row_detail){
                                                    echo '<tr>';
                                                    echo '<input type="hidden" name="return_detail_id[' . $i . ']" value="' . $row_detail->return_detail_id . '" />';
                                                    echo '<input type="hidden" class="input_grn_detail_id" name="grn_detail_id[' . $i . ']" value="' . $row_detail->grn_detail_id . '" />';
                                                    echo '<input type="hidden" name="item_id[' . $i . ']" value="' . $row_detail->item_id . '" />';
                                                    echo '<input type="hidden" class="class_status" name="status[' . $i . ']" value="1" />';
                                                    echo '<td class="text-center text-middle" style="padding-top:13px;">' . $row_detail->item_code . '</td>
                                                            <td class="text-middle item_desc_' . $i . '" style="padding-top:13px;">' . $row_detail->item_desc . '</td>
                                                            <td class="text-center text-middle" style="padding-top:13px;">' . $row_detail->uom_code . '</td>
                                                            <td class="text-right text-middle" style="padding-top:13px;"><span class="mask_currency">' . ($row_detail->item_delivery_qty_remain + $row_detail->return_qty) . '</span></td>
                                                            <td><input type="text" class="form-control input-sm text-right return_qty mask_currency" data-max="' . $row_detail->item_delivery_qty . '" name="return_qty[' . $i . ']" value="' . $row_detail->return_qty . '" /></td>
                                                            <td class="text-center text-middle" style="padding-top:13px;"><a class="btn btn-danger btn-xs tooltips btn-remove" data-index="' . $i . '" href="javascript:;" data-original-title="Remove"><i class="fa fa-times"></i></a></td>
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
                                    if($return_id > 0){
                                        if(trim($row->cancel_note) != ''){
                                            echo '<div class="note note-warning" style="margin:10px;">Cancel Note : ' . $row->cancel_note . '</div>';
                                        }
                                        echo '<div class="note note-info" style="margin:10px;">';
                                        $qry_log = $this->mdl_general->get('app_log', array('feature_id' => Feature::FEATURE_RETURN, 'reff_id' => $return_id), array(), 'log_id asc');
                                        echo '<div class="col-md-8">';
                                        if($qry_log->num_rows() > 0){
                                            echo '<ul class="list-unstyled" style="margin-left:-15px;">';
                                            foreach($qry_log->result() as $row_log){
                                                echo '<li class="margin-bottom-5"><h6>' . $row_log->log_subject  . ' on ' . date_format(new DateTime($row_log->log_date), 'd/m/Y H:i:s') . ' by ' . get_user_fullname( $row_log->user_id ) . '</h6></li>';
                                            }
                                            echo '</ul>';
                                        }
                                        echo '</div>';
                                        if ($row->user_modified > 0) {
                                            echo "<div class='col-md-4'><h6>Last Modified by ".get_user_fullname( $row->user_modified )." (". date_format(new DateTime($row->date_modified), 'd/m/Y H:i:s') .")</h6></div>" ;
                                        }
                                        echo '<div style="clear:both;"></div>';
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
						</form>
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
            "timeOut": "50000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        var $modal = $('#ajax-modal');

        <?php
        if($return_id > 0){
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
            echo picker_input_date(true, true, ymd_to_dmy($row->return_date));
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

        //SUBMIT
        function validate_submit() {
            var result = true;

            if($('.form-group').hasClass('has-error')){
                $('.form-group').removeClass('has-error');
            }

            var supplier_id = parseInt($('#supplier_id').val()) || 0;
            var grn_id = parseInt($('#grn_id').val()) || 0;
            var return_date = $('#return_date').val().trim();
            var remarks = $('textarea[name="remarks"]').val().trim();

            if(supplier_id <= 0){
                toastr["warning"]("Please select supplier.", "Warning!");
                result = false;
            }
            if(grn_id <= 0){
                toastr["warning"]("Please select GRN Code.", "Warning!");
                result = false;
            }
            if(return_date == ''){
                toastr["warning"]("Please select Return Date.", "Warning!");
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
                    var return_qty = parseFloat($('input[name="return_qty[' + i + ']"]').val()) || 0;
                    var qty_max = parseFloat($('input[name="return_qty[' + i + ']"]').attr('data-max')) || 0;
                    var item_desc = $('.item_desc_' + i).html();

                    $('input[name="item_delivery_qty[' + i + ']"]').removeClass('has-error');

                    if(return_qty > qty_max){
                        toastr["warning"]("Item Qty cannot bigger than GRN Qty Remain (" + item_desc + ").", "Warning");
                        $('input[name="return_qty[' + i + ']"]').addClass('has-error');
                        $('input[name="return_qty[' + i + ']"]').val(qty_max)
                        result = false;
                    }
                    if(return_qty <= 0){
                        toastr["warning"]("Please input valid Return Qty (" + item_desc + ").", "Warning");
                        $('input[name="return_qty[' + i + ']"]').addClass('has-error');
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


            var btn = $(this).find("button[type=submit]:focus" );

            var next = true;
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
                    url: "<?php echo base_url('inventory/retur/ajax_return_submit');?>",
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
                    }).fail(function () {
                        $('#id_form_input').unblock();
                        toastr["error"]("Something has wrong, please try again later.", "Error");
                    });
            }
            else {
                $('#id_form_input').unblock();
            }
        });

        $('.return_qty').live('keyup', function(){
            var val = $(this).val().trim();
            var max = parseFloat($(this).attr('data-max')) || 0;
            if(val == ''){
                val = 0;
            }
            val = parseFloat(val);

            if(val > max){
                toastr.clear();

                $(this).val(max);
                toastr["warning"]("Return Qty can not bigger than GRN Qty.", "Warning!");
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

            var old_supplier_id = $('#supplier_id').val();
            var next = false;
            if(parseInt(old_supplier_id) > 0){
                bootbox.confirm("Old Supplier will be deleted, continue?", function(result) {
                    if (result == true) {
                        $('#supplier_id').val(supplier_id);
                        $('#supplier_name').val(supplier_name);

                        $('#grn_id').val('0');
                        $('#grn_code').val('');

                        set_flag_delete(0);
                    }
                });
            }
            else {
                $('#supplier_id').val(supplier_id);
                $('#supplier_name').val(supplier_name);

                $('#grn_id').val('0');
                $('#grn_code').val('');

                set_flag_delete(0);
            }

            $('#ajax-modal').modal('hide');
        });

        //GRN
        var grid_grn = new Datatable();

        var datatableGRN = function () {
            grid_grn.init({
                src: $("#datatable_grn"),
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
                        { "bSortable": false, "sClass": "text-center" },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('inventory/retur/ajax_modal_grn_list');?>/" + $('#supplier_id').val() + "/" + $('#grn_id').val()
                    }
                }
            });

            var tableWrapper = $("#datatable_grn_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });
        }

        $('#btn_lookup_grn').live('click', function (e) {
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
                    $modal.load('<?php echo base_url('inventory/retur/ajax_modal_grn');?>', '', function () {

                        $modal.modal();
                        datatableGRN();

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

        $('.btn-select-grn').live('click', function (e) {
            e.preventDefault();

            var grn_id = $(this).attr('data-id');
            var grn_code = $(this).attr('data-code');
            var po_code = $(this).attr('data-po-code');

            var old_grn_id = $('#grn_id').val();
            var next = true;
            if(parseInt(old_grn_id) > 0){
                bootbox.confirm("Old GRN will be deleted, continue?", function(result) {
                    if (result == true) {
                        $('#grn_id').val(grn_id);
                        $('#grn_code').val(grn_code);
                        $('#po_code').val(po_code);

                        set_flag_delete(0);
                    }
                    else {
                        next = false;
                    }
                });
            }
            else {
                $('#grn_id').val(grn_id);
                $('#grn_code').val(grn_code);
                $('#po_code').val(po_code);
            }

            $('#ajax-modal').modal('hide');
        });

        function set_flag_delete(num_index){
            if(num_index == 0) {
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
            else {
                if ($('#datatable_detail tbody tr:nth-child(' + num_index + ')').hasClass('hide') == false) {
                    $('#datatable_detail tbody tr:nth-child(' + num_index + ')').addClass('hide');
                }
                if ($('#datatable_detail tbody tr:nth-child(' + num_index + ')').hasClass('class_status')) {
                    $('#datatable_detail tbody tr:nth-child(' + num_index + ')').val('0');
                }
            }
        }

        //ADD DETAIL
        $('#btn_add_detail').live('click', function (e) {
            e.preventDefault();

            var grn_id = parseInt($('#grn_id').val()) || 0;

            if(grn_id > 0) {
                $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                    '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

                $('body').modalmanager('loading');

                setTimeout(function () {
                    $modal.load('<?php echo base_url('inventory/retur/ajax_modal_grn_detail');?>', '', function () {

                        $modal.modal();
                        datatableGRNDetail();

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
            } else {
                toastr["warning"]("Please Select GRN.", "Warning");
            }
        });

        var grid_grn_detail = new Datatable();

        var datatableGRNDetail = function () {

            var grn_detail_id_exist = '';
            var n = 0;
            $('#datatable_detail tbody tr').each(function () {
                if ($(this).hasClass('hide') == false) {
                    if(n > 0) {
                        grn_detail_id_exist += '_';
                    }
                    grn_detail_id_exist += $(this).find(".input_grn_detail_id").val();

                    n++;
                }
            });

            grid_grn_detail.init({
                src: $("#datatable_grn_detail"),
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
                        null,
                        { "bSortable": false, "sClass": "text-center" },
                        { "bSortable": false, "sClass": "text-right" },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('inventory/retur/ajax_modal_grn_detail_list');?>/" + $('#grn_id').val() + "/" + grn_detail_id_exist
                    }
                }
            });

            var tableWrapper = $("#datatable_grn_detail_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });

            grid_grn_detail.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();

                var tr_row = $('#datatable_detail tbody tr').length;

                if (grid_grn_detail.getSelectedRowsCount() > 0) {
                    var result = grid_grn_detail.getSelectedRowsCheckbox();

                    $.each( result, function( index, value ){

                        var max_qty = parseFloat(value[6]);
                        var def = 1;
                        if (max_qty < 1) {
                            def = max_qty;
                        }
                        var append_html = '';
                        append_html += '<tr>';
                        append_html += '<input type="hidden" name="return_detail_id[' + tr_row + ']" value="0" />';
                        append_html += '<input type="hidden" class="input_grn_detail_id" name="grn_detail_id[' + tr_row + ']" value="' + value[0] + '" />';
                        append_html += '<input type="hidden" name="item_id[' + tr_row + ']" value="' + value[1] + '" />';
                        append_html += '<input type="hidden" class="class_status" name="status[' + tr_row + ']" value="1" />';
                        append_html += '<td class="text-center text-middle" style="padding-top:13px;">' + value[2] + '</td>';
                        append_html += '<td class="text-middle item_desc_' + tr_row + '" style="padding-top:13px;">' + value[3] + '</td>';
                        append_html += '<td class="text-center text-middle" style="padding-top:13px;">' + value[5] + '</td>';
                        append_html += '<td class="text-right text-middle" style="padding-top:13px;">' + value[6] + '</td>';
                        append_html += '<td><input type="text" class="form-control input-sm text-right return_qty mask_currency" data-max="' + value[6] + '" name="return_qty[' + tr_row + ']" value="' + def + '" /></td>';
                        append_html += '<td class="text-center text-middle" style="padding-top:13px;"><a class="btn btn-danger btn-xs tooltips btn-remove" data-index="' + tr_row + '" href="javascript:;" data-original-title="Remove"><i class="fa fa-times"></i></a></td>';
                        append_html += '</tr>';

                        $('#datatable_detail tbody').append(append_html);

                        tr_row++;
                    });

                    init_page();

                    $('#ajax-modal').modal('hide');
                } else if (grid_grn_detail.getSelectedRowsCount() === 0) {
                    toastr["warning"]("Please Select Detail.", "Warning");
                }
            });
        }

        $('.btn-action').live('click', function(){
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var return_code = $(this).attr('data-code');
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
                                url: "<?php echo base_url('inventory/retur/ajax_return_action');?>",
                                dataType: "json",
                                data: {return_id: id, action: action, reason: result, is_redirect: true}
                            })
                                .done(function (msg) {
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
                                })
                                .fail(function(e){
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                    Metronic.unblockUI();
                                });
                        }
                    }
                });
            } else {
                bootbox.confirm("Are you sure want to " + action_code + " " + return_code + " ?", function (result) {
                    if (result == true) {
                        Metronic.blockUI({
                            boxed: true
                        });

                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('inventory/retur/ajax_return_action');?>",
                            dataType: "json",
                            data: {return_id: id, action: action, is_redirect: true}
                        })
                            .done(function (msg) {
                                if (msg.valid == '0' || msg.valid == '1') {
                                    if (msg.valid == '1') {
                                        if (action == '<?php echo STATUS_POSTED;?>' || action == '<?php echo STATUS_CANCEL;?>') {
                                            window.location = "<?php echo base_url('inventory/retur/return_history/1/' . $return_id);?>";
                                        } else {
                                            window.location = "<?php echo base_url('inventory/retur/return_manage/1/' . $return_id);?>";
                                        }
                                    } else {
                                        toastr["error"](msg.message, "Error");
                                        Metronic.unblockUI();
                                    }
                                } else {
                                    toastr["error"]("Something has wrong, please try again later.", "Error");
                                    Metronic.unblockUI();
                                }
                            })
                            .fail(function(e){
                                toastr["error"]("Something has wrong, please try again later.", "Error");
                                Metronic.unblockUI();
                            });
                    }
                });
            }
        });

        $('.btn-remove').live('click', function(){
            var this_btn = $(this);
            bootbox.confirm("Are you sure?", function (result) {
                if (result == true) {
                    this_btn.closest('tr').addClass('hide');
                    this_btn.closest('tr').find('.class_status').val('0');
                }
            });
        })
	});
</script>