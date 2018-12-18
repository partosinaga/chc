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
                if($entryheader_id > 0){
                    if($row->status != STATUS_NEW){
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
							<i class="fa fa-user"></i><?php echo ($entryheader_id > 0 ? 'Edit' : 'New');?> Cash Book Entry
						</div>
						<div class="actions">
                            <?php
                            if($entryheader_id > 0){
                                if($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED  || $row->status == STATUS_NEW ){
                                    ?>
                                    <a href="<?php echo base_url('cashier/cashbook/pdf_cashbookvoucher/'. ($entryheader_id > 0 ? $entryheader_id : '0') .'.tpd');?>" class="btn default blue-ebonyclay" target="_blank"><i class="fa fa-print"></i></a>
                                <?php
                                }
                            }
                            ?>
							<a href="<?php echo (isset($row) ? ($row->status != STATUS_NEW ? base_url('cashier/cashbook/cashbook_history/1.tpd') : base_url('cashier/cashbook/cashbook_manage/1.tpd') ) : base_url('cashier/cashbook/cashbook_manage/1.tpd')); ?>" class="btn default green-stripe">
							<i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
							</a>
						</div>
					</div>
					<div class="portlet-body form">
						<form action="javascript:;" method="post" id="form-entry" class="form-horizontal">
							<input type="hidden" id="cashentry_id" name="cashentry_id" value="<?php echo $entryheader_id;?>" />
                            <?php
                            if($form_mode == ''){
                            ?>
							<div class="form-actions top">
                                <div class="row">
									<div class="col-md-9">
										<button type="button" class="btn green" name="save">Save</button>
										<button type="button" class="btn blue-madison" name="save_close" >Save & Close</button>
                                        <!-- <a href="<?php echo base_url('cashier/cashbook/cashbook_manage/1.tpd');?>" class="btn red-sunglo">Cancel</a> -->
                                        <?php
                                        if($entryheader_id > 0){
                                            if($row->status == STATUS_NEW && $row->journal_amount > 0){
                                                ?>
                                                &nbsp;
                                                <button type="button" class="btn btn-primary green-seagreen" id="posting-button" onclick="posting_record(<?php echo $entryheader_id; ?>);"><i class="fa fa-save"></i>&nbsp;Posting</button>
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
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label col-md-6">Document No </label>
											<div class="col-md-6">
                                                <div class="input-group">
                                                    <input type="hidden" name="transtype_id" id="transtype_id" value="<?php echo ($entryheader_id > 0 ? $row->transtype_id : '');?>">
                                                    <input type="hidden" name="doc_type" id="doc_type" value="">
                                                    <input type="text" class="form-control" name="journal_no" id="journal_no" value="<?php echo ($entryheader_id > 0 ? $row->journal_no : '');?>" disabled />
                                                    <?php if($entryheader_id <= 0 ) { ?>
                                                    <span class="input-group-btn">
                                                            <button class="btn default" data-toggle="modal" data-target="#ajax-transtype"  type="button" id="btn_find_trx" <?php echo $form_mode; ?> ><i class="fa fa-cog" ></i></button>
                                                    </span>
                                                    <?php } ?>
                                                </div>

										    </div>
										</div>
									</div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Date </label>
                                            <div class="col-md-7" >
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="journal_date" value="<?php echo ($entryheader_id > 0 ? dmy_from_db($row->journal_date) : date('d-m-Y'));?>" readonly <?php echo $form_mode; ?> >
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
                                        <div class="form-group">
                                            <label class="control-label col-md-2"><span id="label_subject">Receive / Payment From</span></label>
                                            <div class="col-md-5">
                                                <input type="text" name ="journal_subject" class="form-control" value="<?php echo ($entryheader_id > 0 ? $row->subject : '');?>" <?php echo $form_mode; ?> />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remark </label>
                                            <div class="col-md-8">
                                                <input type="text" name ="journal_remarks" class="form-control" value="<?php echo ($entryheader_id > 0 ? $row->journal_remarks : '');?>" <?php echo $form_mode; ?> />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Reff. No</label>
                                            <div class="col-md-3">
                                                <input type="text" name ="journal_reff" class="form-control" value="<?php echo ($entryheader_id > 0 ? $row->reference : '');?>" <?php echo $form_mode; ?> />
                                            </div>
                                        </div>
                                    </div>
                                </div>

								<div class="row">
									<div class="col-md-12">
										<table class="table table-striped table-hover table-bordered" id="datatable_detail">
											<thead>
												<tr>
													<th class="text-center" style="width: 10%;"> A/C Code </th>
													<th class="text-left" > Description </th>
													<th class="text-center" style="width: 15%;"> Debit </th>
													<th class="text-center" style="width: 15%;"> Credit </th>
													<th class="text-center" style="width: 10%;"> Dept </th>
													<th class="text-center" style="width: 9%;"> </th>
												</tr>
											</thead>
											<tbody>
                                            <?php
                                            $sumDebit = 0;
                                            $sumCredit = 0;

                                            $rowIndex = 0;
                                            if($entryheader_id > 0){
                                                if(count($qry_det) > 0){

                                                    foreach($qry_det as $row_det){
                                                        echo '<tr>
                                                            <td class="text-center" style="vertical-align:middle;"><input type="hidden" name="detail_id[' . $rowIndex . ']" value="'. $row_det['entrydetail_id'] . '">'. $row_det['coa_code'] . '</td>
                                                            <td style="vertical-align:middle;">' . $row_det['coa_desc'] . '</td>
                                                            <td ><input type="text" name="journal_debit[' . $rowIndex . ']" value="' . $row_det['journal_debit'] . '" class="form-control text-right mask_currency j_debit" '. $form_mode . '></td>
                                                            <td ><input type="text" name="journal_credit[' . $rowIndex . ']" value="' . $row_det['journal_credit'] . '" class="form-control text-right mask_currency j_credit" '. $form_mode . '></td>
                                                            <td class="text-center" style="vertical-align:middle;">' .
                                                            '<select name="dept_id[' . $rowIndex . ']" class="form-control form-filter input-sm select2me " '. $form_mode . '>';

                                                        if($row_det['dept_id'] == 0){
                                                            echo '<option value="0" selected="selected" >None</option>';
                                                        }else{
                                                            echo '<option value="0" >None</option>';
                                                        }

                                                        if(count($dept_list) > 0){
                                                            foreach($dept_list as $dept){
                                                                echo  '<option value="' . $dept['department_id'] . '" ' . ($row_det['dept_id'] > 0 ? ($row_det['dept_id'] == $dept['department_id'] ? 'selected="selected"' : '') : '') . '>' . $dept['department_name'] . '</option>';
                                                            }
                                                        }

                                                        echo  '</select>
                                                            </td>';

                                                        if($row->status != STATUS_NEW){
                                                            echo   '<td style="vertical-align:middle;"></td>';
                                                        }else{
                                                            echo   ' <td style="vertical-align:middle;"><a class="btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" onclick="delete_record(' . $row_det['entrydetail_id'] . ',' . $rowIndex . ');" ><i class="fa fa-times"></i></a></td>';
                                                        }

                                                        echo   ' </tr>';
                                                        $rowIndex++;

                                                        $sumDebit += $row_det['journal_debit'];
                                                        $sumCredit += $row_det['journal_credit'];
                                                    }
                                                }
                                            }
                                            ?>

											</tbody>
                                            <tfoot>
                                            <tr>
                                                <td >
                                                    <?php if($form_mode == '') { ?>
                                                    <a href="javascript:;" class="btn green-seagreen add_detail" ><i class="fa fa-plus"></i> Add COA</a>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right" disabled><span  class="form-control-static">Total</span></td>
                                                <td ><input type="text" name="total_debit" id="total_debit" class="form-control mask_currency text-right " value="<?php echo $sumDebit;?>" readonly/></td>
                                                <td ><input type="text" name="total_credit" id="total_credit" class="form-control mask_currency text-right " value="<?php echo $sumCredit; ?>" readonly/></td>
                                                <td colspan="2" >&nbsp;</td>
                                            </tr>
                                            </tfoot>
										</table>
									</div>
								</div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <?php
                                        if($entryheader_id > 0){
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
                                                    ' . $modified . '
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
<div id="ajax-transtype" class="modal fade bs-modal-sm" data-keyboard="false" data-backdrop="static" tabindex="-1" >
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="alert alert-danger hide modal-error-message">
                    <button class="close" aria-hidden="true" data-dismiss="alert" type="button"></button>
                    <i class="fa-lg fa fa-warning"></i> <span></span>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-container">
                            <div class="table-actions-wrapper">
                                <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit </button>
                            </div>
                            <table class="table table-striped table-hover table-bordered" id="datatable_trx">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 5%;"></th>
                                        <th class="text-center" style="width: 12%;"> Trx</th>
                                        <th > Description </th>
                                        <th class="text-center" style="width: 10%;"> Doc </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $qry_trxtype = $this->db->query('select * from ms_transtype where is_cash_basis > 0 and status <> ' . STATUS_DELETE);
                                    if($qry_trxtype->num_rows() > 0){
                                        foreach($qry_trxtype->result() as $row_trx){
                                            echo '<tr>'.
                                                    '<td class="text-center"><input type="radio" name="radio_item" value="' . $row_trx->transtype_id . '" data-code="' . $row_trx->transtype_name . '" data-other-1="' . $row_trx->doc_type . '" /></td>' .
                                                    '<td class="text-center"><span><strong>' . $row_trx->transtype_name . '</strong></span></td>'.
                                                    '<td >' . $row_trx->transtype_desc . '</td>'.
                                                    '<td class="text-center">' . $row_trx->doc_type . '</td>'.
                                                 '</tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn blue" id="submit-trx-ok">OK</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<div id="ajax-calendar" class="modal fade bs-modal-sm"  data-keyboard="false" data-backdrop="static" tabindex="-1">
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

<script language="JavaScript">
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;
    var rowIndex = <?php echo (isset($rowIndex) ? $rowIndex : 0) ; ?>;

    if(isedit > 0){
        $('#form-entry').block({
            message: null ,
            overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
        });
    }

    var handleMask = function() {
        $(".mask_currency").inputmask("numeric",{
            radixPoint:".",
            autoGroup: true,
            groupSeparator: ",",
            digits: 0,
            groupSize: 3,
            removeMaskOnSubmit:true,
            autoUnmask: true
        });
    }

    $(document).ready(function(){
        <?php echo picker_input_date(); ?>

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
                    transtype_id:{
                        required:true
                    },
                    journal_date: {
                        required: true
                    },
                    journal_subject: {
                        required: true
                    },
                    journal_remarks: {
                        required: true
                    },
                    total_credit:{
                        equalTo:"#total_debit"
                        //"checkBalance" :{fieldName: "total_debit"}
                    }
                },
                messages: {
                    transtype_id: "Document type must be selected",
                    journal_date: "Journal date must be selected",
                    journal_subject: "Receive / Payment from must not empty",
                    journal_remarks: "Journal remark must not empty",
                    total_credit: "Total Debit must equal with Total Credit"
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.transtype_id != null){
                        toastr["error"](validator.invalid.transtype_id, "Warning");
                    }
                    if(validator.invalid.journal_date != null){
                        toastr["error"](validator.invalid.journal_date, "Warning");
                    }
                    if(validator.invalid.journal_subject != null){
                        toastr["error"](validator.invalid.journal_subject, "Warning");
                    }
                    if(validator.invalid.journal_remarks != null){
                        toastr["error"](validator.invalid.journal_remarks, "Warning");
                    }
                    if(validator.invalid.total_credit != null){
                        toastr["error"](validator.invalid.total_credit, "Warning");
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

        //initiate validation
        handleValidation();
        handleMask();

        var grid_trx = new Datatable();
        var $modal = $('#ajax-modal');

        //COA
        var handleTableCOA = function () {
            // Start Datatable Item
            grid_trx.init({
                src: $("#datatable_coa"),
                onSuccess: function (grid_trx) {
                    // execute some code after table records loaded
                },
                onError: function (grid_trx) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_trx) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                    // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                    // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                    // So when dropdowns used the scrollable div should be removed.
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '5%' },
                        { "sClass": "text-center", "sWidth" : '7%' },
                        null,
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center", "sWidth" : '6%' },
                        { "bSortable": false }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [-1],
                        ["All"] // change per page values here
                    ],
                    "pageLength": -1, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('finance/setup/json_coa');?>" // ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_coa_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

        }

        handleCalculation();

        $('.add_detail').live('click', function (e) {
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            var uninitialized = $('#datatable_coa').filter(function() {
                return !$.fn.DataTable.fnIsDataTable(this);
            });

            setTimeout(function(){
                $modal.load('<?php echo base_url('finance/setup/book_coa');?>.tpd', '', function(){
                    $modal.modal();
                    handleTableCOA();

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
        });

        $('.table-group-action-submit').live('click', function (e) {
            e.preventDefault();
            grid_trx.src = $("#datatable_coa");
            if (grid_trx.getSelectedRowsCountRadio() > 0) {
                var result = grid_trx.getSelectedRowsRadio();
                //console.log(result);
                var coaID = result[0];
                var entryHeaderID = $('#entryheader_id').val() || 0;

                //console.log("add row ... " + rowIndex)

                //Add to temp
                var newRowContent = "<tr>" +
                    "<td class=\"text-center\" style=\"vertical-align:middle;\"><input type=\"hidden\" name=\"detail_id[" + rowIndex + "]\" value=\"0\"><input type=\"hidden\" name=\"coa_id[" + rowIndex + "]\" value=\"" + coaID + "\">" + result[1] + "</td>" +
                    "<td style=\"vertical-align:middle;\"><span class=\"control-label\">" + result[2] + "</span></td>" +
                    "<td ><input type=\"text\" name=\"journal_debit[" + rowIndex + "]\" value=\"0\" class=\"form-control text-right mask_currency j_debit\" coa-id=\"" + coaID + "\"></td>" +
                    "<td ><input type=\"text\" name=\"journal_credit[" + rowIndex + "]\" value=\"0\" class=\"form-control text-right mask_currency j_credit\" coa-id=\"" + coaID + "\"></td>" +
                    "<td class=\"text-center\" style=\"vertical-align:middle;\"><select name=\"dept_id[" + rowIndex + "]\" class=\"form-control form-filter input-sm select2me \">" +
                    "<option value=\"0\" >None</option>" +
                    <?php
                     if(count($dept_list) > 0){
                        foreach($dept_list as $dept){
                    ?>
                    "<option value=\"<?php echo $dept['department_id']; ?>\" ><?php echo $dept['department_name']; ?></option>" +
                    <?php
                        }
                     }
                     ?>
                    "</select></td>" +
                    "<td style=\"vertical-align:middle;\"><a class=\"btn btn-danger btn-xs tooltips\" data-original-title=\"Remove\" href=\"javascript:;\" onclick=\"delete_record(" + entryHeaderID + "," + rowIndex + ");\" ><i class=\"fa fa-times\"></i></a></td>" +
                    "</tr>";

                //$('#datatable_detail tbody').prepend(newRowContent);
                $('#datatable_detail tbody').append(newRowContent);

                $('select.select2me').select2();

                rowIndex++;

                handleCalculation();
                handleMask();

                $('#ajax-modal').modal('hide');
            } else if (grid_trx.getSelectedRowsCountRadio() === 0) {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record selected',
                    container: grid_trx.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

        $('button[name="save"]').on('click', function(e){
            e.preventDefault();

            /*
            if($("#form-entry").valid()){
                var total_debit = $('#total_debit').val();
                var total_credit = $('#total_credit').val();
                console.log('D ' + total_debit + " C " + total_credit);
                if(total_debit == total_credit){

                }else{
                    toastr["error"]("Total Debit must equal with Total Credit", "Warning");
                }
            }
            */

            if(validateInput()){
                var url = '<?php echo base_url('cashier/cashbook/submit_entry.tpd');?>';
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
                //$('#form-entry').submit();
            }
        });

        $('button[name="save_close"]').on('click', function(e){
            e.preventDefault();

            /*
            if($("#form-entry").valid()){
                var total_debit = $('#total_debit').val();
                var total_credit = $('#total_credit').val();
                if(total_debit == total_credit){

                }else{
                    toastr["error"]("Total Debit must equal with Total Credit", "Warning");
                }
            }
            */

            if(validateInput()){
                var url = '<?php echo base_url('cashier/cashbook/submit_entry.tpd');?>';
                $("#form-entry").append('<input type="hidden" name="save_close" value="">');
                $("#form-entry").attr("method", "post");
                $('#form-entry').attr('action', url).submit();
            }
        });

    });

    $('#btn_find_trx').click(function(e) {
        e.preventDefault();

        $.uniform.restore();
    });

    $('#submit-trx-ok').click(function(e) {
        e.preventDefault();

        var grid = new Datatable();
        grid.src = "#datatable-trx";
        if (grid.getSelectedRowsCountRadio() > 0) {
            var result = grid.getSelectedRowsRadio();

            //value
            if(result[0] != null)
            {
                $('#transtype_id').val(result[0]);
            }

            //data-code
            if(result[1] != null)
            {
                $('#journal_no').val(result[1]);
            }

            //data-other-1
            if(result[4] != null)
            {
                $('#doc_type').val(result[4]);
            }

            $('#ajax-transtype').modal('hide');
        } else if (grid.getSelectedRowsCountRadio() === 0) {
            Metronic.alert({
                type: 'danger',
                icon: 'warning',
                message: 'No record selected',
                container: grid.getTableWrapper(),
                place: 'prepend'
            });
        }
    });

    function validateInput(){
        var valid = true;

        if($("#form-entry").valid()){
            var total_debit = $('#total_debit').val();
            var total_credit = $('#total_credit').val();
            if(total_debit == 0 || total_credit == 0){
                valid = false;
                toastr["error"]("Detail must not empty", "Warning");
            }else{
                if(total_debit != total_credit){
                    valid = false;
                    toastr["error"]("Total Debit must equal with Total Credit", "Warning");
                }
            }

        }else{
            valid = false;
        }

        return valid;
    }

    function deleteDetail(detailId, rowIndex){
        if(detailId > 0){
            Metronic.blockUI({
                target: '.modal-content-edit',
                boxed: true,
                message: 'Processing...'
            });

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('cashier/cashbook/ajax_delete_detail');?>",
                dataType: "json",
                data: { detail_id: detailId}
            })
                .done(function( msg ) {
                    if(msg.type != '0'){
                        Metronic.unblockUI('.modal-content-edit');
                        //$('#ajax-modal').modal('hide');

                        delete_frontend(rowIndex);
                    }
                    else {
                        Metronic.unblockUI('.modal-content-edit');

                        //console.log('ajax delete detail failed.' + detailId);
                    }
                });
        }
    }

    function posting_record(headerId){
        if(validateInput()){

            bootbox.confirm({
                message: "Posting this transaction ?",
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
                        var $modal_cal = $('#ajax-calendar');

                        $.fn.modalmanager.defaults.resize = true;

                        $modal_cal.css({'margin-top': '0px'});
                        $modal_cal.modal();
                    }
                }
            });


        }
    }

    $('#submit-posting').click(function(e) {
        e.preventDefault();

        var headerId = parseFloat($('input[name="cashentry_id"]').val()) || 0;
		var posting_date = $('input[name="c_posting_date"]').val();

        if(headerId > 0){
            //console.log(result);
            Metronic.blockUI({
                boxed: true
            });

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('cashier/cashbook/ajax_posting_journal_by_id');?>",
                dataType: "json",
                data: { entryheader_id: headerId, posting_date :posting_date}
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

    });

</script>