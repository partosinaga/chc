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
                if($delivery_id > 0){
                    if($delivery->status != STATUS_NEW){
                        $form_mode = 'disabled';
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
							<i class="fa fa-user"></i><?php echo ($delivery_id > 0 ? '' : 'New');?> Delivery Order
						</div>
						<div class="actions">

                            <a href="<?php echo (isset($delivery) ? ($delivery->status != STATUS_NEW ? base_url('ar/corporate_bill/delivery/2.tpd') : base_url('ar/corporate_bill/delivery/1.tpd') ) : base_url('ar/corporate_bill/delivery/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="<?php echo base_url('ar/delivery/submit_delivery.tpd');?>" method="post" id="form-entry" class="form-horizontal">
							<input type="hidden" id="delivery_id" name="delivery_id" value="<?php echo $delivery_id;?>" />
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn btn-sm btn-circle blue <?php echo ($delivery_id > 0 ? ($delivery->status <> STATUS_NEW ? 'hide' : ''): '')?>" name="save" id ="btn_save">Save</button>

                                        <?php
                                        if($delivery_id > 0){
                                            if($delivery->status == STATUS_NEW ){
                                        ?>
                                                &nbsp;
                                                <button type="button" class="btn btn-sm purple btn-circle" id="posting-button" ><i class="fa fa-save"></i>&nbsp;Complete</button>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-4 ">
                                        <div class="btn-group pull-right <?php echo ($delivery_id > 0 ? '': 'hide')?>">
                                            <button class="btn blue-soft dropdown-toggle "
                                                    data-toggle="dropdown" type="button" aria-expanded="false">
                                                <i class="fa fa-print"></i>&nbsp;Delivery Order&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu pull-right" role="menu">
                                                <li><a href="<?php echo base_url('ar/delivery/pdf_delivery/'. ($delivery_id > 0 ? $delivery->delivery_id : '0') .'.tpd');?>" class="btn default blue-ebonyclay pull-right" target="_blank"><i class="fa fa-print"></i>&nbsp;Delivery Order A5</a></li>
                                                <li><a href="<?php echo base_url('ar/delivery/pdf_delivery/'. ($delivery_id > 0 ? $delivery->delivery_id : '0') .'/a4/portrait.tpd');?>" class="btn default blue-ebonyclay pull-right" target="_blank"><i class="fa fa-print"></i>&nbsp;Delivery Order A4</a></li>
                                            </ul>
                                        </div>

                                    </div>
								</div>

							</div>

							<div class="form-body" id="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label col-md-3">DO No</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="do_no" value="<?php echo ($delivery_id > 0 ? $delivery->do_no : 'NEW');?>" disabled />
											</div>
										</div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Date </label>
                                            <div class="col-md-4" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="do_date" value="<?php echo ($delivery_id > 0 ? dmy_from_db($delivery->do_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
													<span class="input-group-btn">
														<button class="btn default" type="button" <?php echo $form_mode; ?> ><i class="fa fa-calendar" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Company</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="hidden" name="company_id" value="<?php echo ($delivery_id > 0 ? $delivery->company_id : '');?>">
                                                    <input type="text" class="form-control" name="company_name" value="<?php echo ($delivery_id > 0 ? $delivery->company_name : '');?>" readonly />
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
                                            <label class="control-label col-md-3">PO</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="remark" value="<?php echo ($delivery_id > 0 ? $delivery->remark : '') ;?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
											<label class="control-label col-md-3">Delivered By</label>
											<div class="col-md-6">
												<input type="text" class="form-control" name="delivered_by" value="<?php echo ($delivery_id > 0 ? $delivery->delivered_by : '');?>" />
											</div>
										</div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-1"></div>
									<div class="col-md-10">
										<table class="table table-striped table-hover table-bordered table-po-detail" id="delivery_detail">
											<thead>
												<tr>
													<th class="text-center" style="width: 11%;"> INVOICE NO </th>
                                                    <th class="text-center" > DESCRIPTION </th>
													<th class="text-center " style="width: 10%;"> QTY </th>
													<th class="text-center" style="width: 10%;"> UOM </th>
													<th class="text-center" style="width: 5%;"><input type="checkbox" id="checkall" <?php echo ($delivery_id > 0 ? ($delivery->status <> STATUS_NEW ? 'class="hide"' : '') : ''); ?>/></th>
												</tr>
											</thead>
											<tbody>
                                            <?php
                                                if($delivery_id > 0){
                                                    if(isset($details)){
                                                        foreach($details as $bill){
                                                            $display = '<tr id="parent_' . $bill['invdetail_id'] . '' . '">
                                                                     <td style="vertical-align:middle;" class="text-center">
                                                                        <input type="hidden" name="inv_id[]" value="' . $bill['inv_id'] . '">
                                                                        <span class="text-center">' . $bill['inv_no'] . '</span>
                                                                     </td>
                                                                     <td style="vertical-align:middle;" class="text-left">
                                                                        <span class="text-left">' . $bill['description'] . '</span>
                                                                     </td>
                                                                     <td style="vertical-align:middle;padding-right:10px;" class="text-right">
                                                                        <span class="text-right ">' . format_num($bill['unit_qty'],0) . '</span>
                                                                     </td>
                                                                     <td style="vertical-align:middle;" class="text-center">
                                                                        <span class="text-center">' . $bill['unit_uom'] . '</span>
                                                                     </td>';

                                                            if($delivery->status == STATUS_NEW){
                                                                $display .= '
                                                                     <td style="vertical-align:middle;padding-top:8px;padding-left:10px;">
                                                                        <input type="checkbox" name="invdetail_id[]" value="' . $bill['invdetail_id'] . '" ' . $bill['checked'] . ' class="chk_inv_id">
                                                                    </td>';
                                                            }else{
                                                                $display .= '
                                                                     <td style="vertical-align:middle;padding-top:8px;padding-left:14px;">
                                                                        <i class="fa fa-check">
                                                                    </td>';
                                                            }

                                                            $display .= '</tr>';
                                                            echo $display;
                                                        }
                                                    }
                                                }
                                            ?>
											</tbody>
                                        </table>
									</div>
								</div>

                                <!-- END PAGE CONTENT-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php
                                        if($delivery_id > 0){
                                            $created = '';
                                            $modified = '';

                                            if ($delivery->created_by > 0) {
                                                $created .= "<div class='col-md-8'><h6>Created by " . get_user_fullname( $delivery->created_by) . " (" . date_format(new DateTime($delivery->created_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                                            }
                                            /*
                                            if ($row->modified_by > 0) {
                                                $modified .= "<div class='col-md-4'><h6>Last Modified by " . get_user_fullname( $row->modified_by) . " (" . date_format(new DateTime($row->modified_date), 'd/m/Y H:i:s') . ")</h6></div>" ;
                                            }
                                            */
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

<script src="<?php echo base_url('assets/custom/js/toaster.js'); ?>"></script>
<script>
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;

    $(document).ready(function(){
        autosize($('textarea'));
        <?php echo picker_input_date() ;?>
        Toaster.init();

        <?php echo picker_input_date() ;?>

        if(isedit > 0){
            $('#form-body').block({
                message: null ,
                overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
            });
        }

        var grid1 = new Datatable();
        var handleTableModal = function (num_index, company_id, delivery_id) {
            // Start Datatable Item
            grid1.init({
                src: $("#datatable_modal"),
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-left"},
                        { "sWidth" : '20%' ,"sClass": "text-right", "bSortable": false},
                        { "sWidth" : '5%' ,"sClass": "text-center", "bSortable": false},
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '11%' }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, -1],
                        [10, 20, 50, 100, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('ar/delivery/get_modal_companies');?>/" + num_index + "/" + company_id + "/" + delivery_id // ajax source
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
                $modal.load('<?php echo base_url('ar/delivery/xmodal_companies');?>.tpd', '', function () {
                    $modal.modal();

                    var delivery_id = $('input[name="delivery_id"]').val();
                    var company_id = $('input[name="company_id"]').val();
                    handleTableModal(num_index, company_id, delivery_id);

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

            $('input[name="company_id"]').val(company_id);
            $('input[name="company_name"]').val(company_name);

            //Looking Bills
            $.ajax({
                type: "POST",
                url: js_base_url + 'ar/delivery/xcorp_pending_invoice',
                data: { company_id : company_id},
                async:false
            })
                .done(function( msg ) {
                    //console.log(msg);
                    $('#delivery_detail > tbody').html(msg);
                });

            Metronic.initUniform();

            $('#ajax-modal').modal('hide');
        });

        function validate_input(){
            var valid = true;

            var do_date = $('input[name="do_date"]').val();
            var company_id = parseInt($('input[name="company_id"]').val()) || 0;

            //console.log('validate_input ' + company_id);

            if(do_date == ''){
                valid = false;
                toastr["error"]("Date must be selected", "Warning");
            }

            if(company_id <= 0){
                valid = false;
                toastr["error"]("Company must be selected", "Warning");
            }

            var checkedCount = $("#delivery_detail input[type=checkbox]:checked").length || 0;
            if(checkedCount <= 0){
                valid = false;
                toastr["error"]("Invoice detail must be selected", "Warning");
            }

            return valid;
        }

        $('button[name="save"]').click(function(e){
            e.preventDefault();

            if(validate_input()){
                var url = '<?php echo base_url('ar/delivery/submit_delivery.tpd');?>';
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
            }
        });

        $('#posting-button').on('click', function(e) {
            e.preventDefault();

            $('#ajax-posting').modal('hide');

            var delivery_id = parseFloat($('input[name="delivery_id"]').val()) || 0;
            if(delivery_id > 0){
                bootbox.confirm({
                    message: "Complete this Delivery Order ?<br><strong>Please make sure any changes has been saved.</strong>",
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
                                url: "<?php echo base_url('ar/delivery/xposting_delivery_by_id');?>",
                                dataType: "json",
                                data: { delivery_id: delivery_id}
                            })
                                .done(function( msg ) {
                                    Metronic.unblockUI();

                                    if(msg.type == '1'){
                                        toastr["success"](msg.message, "Success");
                                        if(msg.redirect_link != ''){
                                            window.location.assign(msg.redirect_link);
                                        }
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

    $("#delivery_detail #checkall").click(function () {
        if ($("#delivery_detail #checkall").is(':checked')) {
            $("#delivery_detail input[type=checkbox]").each(function () {
                //$(this).attr("checked", "checked");
                $(this).prop("checked", true);
                $(this).parent('span').addClass('checked');
            });

        } else {
            $("#delivery_detail input[type=checkbox]").each(function () {
                $(this).prop("checked", false);
                //$(this).removeAttr("checked");
                $(this).parent('span').removeClass('checked');
            });
        }
    });

</script>