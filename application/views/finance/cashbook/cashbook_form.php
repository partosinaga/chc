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
                    //echo '<!-- DATE ' . dmy_from_db($row->journal_date) . ' -->';
                }

                if($form_mode == ''){
                        if(!check_controller_action('cashier','cashbook', STATUS_NEW) && !check_controller_action('cashier','cashbook', STATUS_EDIT)) {
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
                                if($row->status == STATUS_NEW || $row->status == STATUS_CLOSED || $row->status == STATUS_POSTED){
                                    if($row->feature_id == Feature::FEATURE_AR_RECEIPT && ($row->status == STATUS_CLOSED || $row->status == STATUS_POSTED)) {
                                ?>

                                <?php
                                    }
                                    ?>
                                    <a href="<?php echo base_url('cashier/cashbook/pdf_cashbookvoucher/'. ($entryheader_id > 0 ? $entryheader_id : '0') .'.tpd');?>" data-original-title="Voucher" class="btn default blue-ebonyclay tooltips" target="_blank"><i class="fa fa-print"></i></a>
									<a href="<?php echo base_url('finance/ledger/pdf_postedjournal/-/-/'. $row->journal_no .'.tpd');?>" class="btn btn-circle blue-ebonyclay tooltips" data-original-title="Journal" target="_blank"><i class="fa fa-print"></i></a>
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
										<button type="button" class="btn green btn-circle" name="save"><i class="fa fa-save"></i> Save</button>
										<!-- button type="button" class="btn blue-madison" name="save_close" >Save & Close</button -->
                                        <!-- <a href="<?php echo base_url('cashier/cashbook/cashbook_manage/1.tpd');?>" class="btn red-sunglo">Cancel</a> -->
                                        <?php
                                        if($entryheader_id > 0){
                                            if($row->status == STATUS_NEW && $row->journal_amount > 0){
                                                ?>
                                                &nbsp;
                                                <button type="button" class="btn purple btn-circle" id="posting-button" onclick="posting_record(<?php echo $entryheader_id; ?>);"><i class="fa fa-save"></i>&nbsp;Posting</button>
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
                                                    <input type="hidden" name="feature_id" value="<?php echo ($entryheader_id > 0 ? $row->feature_id : '0');?>">
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
                                            <?php
                                                $label_subject = 'Receive / Payment';
                                                if($entryheader_id > 0){
                                                    if($row->doc_type == 'RV'){
                                                        $label_subject = 'Receive From';
                                                    }else if($row->doc_type == 'PV'){
                                                        $label_subject = 'Payment To';
                                                    }else if($row->doc_type == 'CA'){
                                                        $label_subject = 'Adjust From';
                                                    }
                                                }
                                            ?>
                                            <label class="control-label col-md-2"><span id="label_subject"><?php echo $label_subject ?></span></label>
                                            <div class="col-md-5">
                                                <input type="text" name ="journal_subject" class="form-control" value="<?php echo ($entryheader_id > 0 ? $row->subject : '');?>" <?php echo $form_mode; ?> />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remark </label>
                                            <div class="col-md-8">
                                                <!--input type="text" name ="journal_remarks" class="form-control" value="<?php //echo ($entryheader_id > 0 ? $row->journal_remarks : '');?>" <?php //echo $form_mode; ?> /-->
												<textarea class="form-control" rows="2" name="journal_remarks"  <?php echo $form_mode; ?>  ><?php echo ($entryheader_id > 0 ? $row->journal_remarks : '')?></textarea>                                                
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Reff No</label>
                                            <div class="col-md-3">
                                                <input type="text" name ="journal_reff" class="form-control" value="<?php echo ($entryheader_id > 0 ? $row->reference : '');?>" <?php echo $form_mode; ?> />
                                            </div>
                                        </div>
                                    </div>
                                </div>

								<div class="row">
									<div class="col-md-12">
										<table class="table table-striped table-hover table-bordered table-po-detail" id="datatable_detail">
											<thead>
												<tr>
													<th class="text-center" style="width: 10%;"> COA CODE </th>
													<th class="text-center" > DESCRIPTION </th>
                                                    <th class="text-center" > REMARK </th>
													<th class="text-center" style="width: 15%;"> DEBIT </th>
													<th class="text-center" style="width: 15%;"> CREDIT </th>
													<th class="text-center" style="width: 10%;"> DEPT </th>
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
                                                    //List of Cashflow COA
                                                    $listCF = array();
                                                    $cfAccounts = $this->db->query('SELECT coa_id,coa_code FROM gl_cashflow_parameter WHERE status = ' . STATUS_NEW);
                                                    if($cfAccounts->num_rows() > 0){
                                                        foreach($cfAccounts->result() as $cf){
                                                            if(!in_array($cf->coa_code,$listCF)) {
                                                                $listCF[] = $cf->coa_code;
                                                            }
                                                        }
                                                    }

                                                    foreach($qry_det as $row_det){
                                                        echo '<tr>
                                                            <input type="hidden" name="is_cf[' . $rowIndex . ']" value="' . (in_array($row_det['coa_code'],$listCF) ? 1 : 0) . '">
                                                            <td class="text-center" style="padding-top: 10px;"><input type="hidden" name="detail_id[' . $rowIndex . ']" value="'. $row_det['entrydetail_id'] . '"><input type="hidden" name="coa_id[' . $rowIndex . ']" value="'. $row_det['coa_id'] . '">'. $row_det['coa_code'] . '</td>
                                                            <td style="padding-top: 10px;">' . $row_det['coa_desc'] . '</td>
                                                            <td><textarea name="journal_note['. $rowIndex .']" rows="2" class="form-control" style="resize: vertical;font-size:11px;">' . $row_det['journal_note'] . '</textarea></td>
                                                            <td ><input type="text" name="journal_debit[' . $rowIndex . ']" value="' . $row_det['journal_debit'] . '" class="form-control input-sm text-right mask_currency j_debit" '. $form_mode . '></td>
                                                            <td ><input type="text" name="journal_credit[' . $rowIndex . ']" value="' . $row_det['journal_credit'] . '" class="form-control input-sm text-right mask_currency j_credit" '. $form_mode . '></td>
                                                            <td class="text-center" >' .
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
                                                            echo   '<td style="padding-top: 5px;"></td>';
                                                        }else{
                                                            echo   ' <td style="padding-top: 5px;"><a class="btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" onclick="delete_record(' . $row_det['entrydetail_id'] . ',' . $rowIndex . ');" ><i class="fa fa-times"></i></a></td>';
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
                                                <th >
                                                    <?php if($form_mode == '') { ?>
                                                    <a href="javascript:;" class="btn btn-sm blue-chambray yellow-stripe add_detail" ><i class="fa fa-plus"></i> Add COA</a>
                                                    <?php } ?>
                                                </th>
                                                <th ></th>
                                                <th class="text-right" disabled><span  class="form-control-static">Total</span></th>
                                                <th ><input type="text" name="total_debit" id="total_debit" class="form-control mask_currency input-sm text-right " value="<?php echo $sumDebit;?>" readonly/></th>
                                                <th ><input type="text" name="total_credit" id="total_credit" class="form-control mask_currency input-sm text-right " value="<?php echo $sumCredit; ?>" readonly/></th>
                                                <th colspan="2" >&nbsp;</th>
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
                                    $qry_trxtype = $this->db->query('select ms_transtype.*,doc.feature_id from ms_transtype
                                            join document doc on doc.doc_name = ms_transtype.doc_type
                                            where ms_transtype.is_cash_basis > 0 and ms_transtype.status <> ' . STATUS_DELETE);
                                    if($qry_trxtype->num_rows() > 0){
                                        foreach($qry_trxtype->result() as $row_trx){
                                            echo '<tr>'.
                                                    '<td class="text-center"><input type="radio" name="radio_item" value="' . $row_trx->transtype_id . '" data-code="' . $row_trx->transtype_name . '" data-other-1="' . $row_trx->doc_type . '" data-other-2="' . $row_trx->feature_id . '" /></td>' .
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

<div id="ajax-posting" class="modal fade bs-modal-sm"  data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Posting date :</h4>
            </div>
            <div class="modal-body">
                <div class="input-group <?php echo isset($row) ? $row->feature_id == Feature::FEATURE_GL_ADJUSTMENT ? 'date date-picker' : '' : ''?>" data-date-format="dd-mm-yyyy" >
                    <input type="text" class="form-control" name="c_posting_date" value="<?php echo (date('d-m-Y'));?>" readonly>
					<span class="input-group-btn <?php echo isset($row) ? $row->feature_id == Feature::FEATURE_GL_ADJUSTMENT ? '' : 'hide' : 'hide'?>"">
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
		
        autosize($('textarea'));
        var initPickerDate = function(){
            <?php echo picker_input_date(); ?>
        }

        initPickerDate();

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
                    journal_subject: "Receive / Payment must not empty",
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
        var grid_coa = new Datatable();
        var handleTableCOA = function (coaId, detailId, uniqueId, showCF)   {
            var coa_id_exist = '-';
            var n = 0;
            $('#table_detail tbody tr').each(function () {
                if ($(this).hasClass('hide') == false) {
                    var coa_id = parseInt($(this).find('input[name*="coa_id"]').val()) || 0;
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
                        "url": "<?php echo base_url('general/modalbook/get_coa_list_by_id');?>/" + coaId + '/' + detailId + '/' + uniqueId + '/0/' + coa_id_exist + '/-/-/' + showCF + '/0'
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

        handleCalculation();

        $('.add_detail').live('click', function (e) {
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            var coa_id = 0 //parseInt($(this).closest('td').find('input[name*="coa_id"]').val()) || 0;
            var detail_id = 0; //parseInt($(this).closest('td').find('input[name*="detail_id"]').val()) || 0;
            var unique_id = 0; //parseInt($(this).closest('td').find('input[name*="unique_id"]').val()) || 0;
            //console.log('UID COA ' + unique_id);
            $('body').modalmanager('loading');

            var uninitialized = $('#datatable_coa').filter(function() {
                return !$.fn.DataTable.fnIsDataTable(this);
            });

            var $modal = $('#ajax-modal');

            setTimeout(function(){
                $modal.load('<?php echo base_url('general/modalbook/ajax_coa_by_id');?>.tpd', '', function(){
                    var showCF = isCFExist();
                    handleTableCOA(coa_id, detail_id, unique_id, showCF);

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
        });

        $('.btn-select-coa').live('click', function (e) {
            e.preventDefault();

            var coa_id = parseInt($(this).attr('coa-id')) || 0;
            var coa_code = $(this).attr('coa-code');
            var coa_desc = $(this).attr('coa-desc');

            var detail_id = parseInt($(this).attr('detail-id')) || 0;
            var unique_id = $(this).attr('unique-id');
            var general_dept_id = '<?php echo isset($general_dept_id) ? $general_dept_id : 0?>';
            var is_cf = $(this).attr('parent-is-cf');

            var entryHeaderID = $('#entryheader_id').val() || 0;
            if(coa_id > 0) {
                //Add to temp
                var newRowContent = "<tr>" +
                    "<input type=\"hidden\" name=\"is_cf[" + rowIndex + "]\" value=\"" + is_cf + "\">" +
                    "<td class=\"text-center\" style=\"padding-top: 10px;\"><input type=\"hidden\" name=\"detail_id[" + rowIndex + "]\" value=\"0\"><input type=\"hidden\" name=\"coa_id[" + rowIndex + "]\" value=\"" + coa_id + "\">" + coa_code + "</td>" +
                    "<td style=\"padding-top: 10px;\"><span class=\"control-label\">" + coa_desc + "</span></td>" +
                    "<td><textarea name=\"journal_note[" + rowIndex + "]\" rows=\"2\" class=\"form-control\" style=\"resize: vertical;font-size: 11px;\"></textarea></td>" +
                    "<td ><input type=\"text\" name=\"journal_debit[" + rowIndex + "]\" value=\"0\" class=\"form-control text-right mask_currency input-sm j_debit\" coa-id=\"" + coa_id + "\"></td>" +
                    "<td ><input type=\"text\" name=\"journal_credit[" + rowIndex + "]\" value=\"0\" class=\"form-control text-right mask_currency input-sm j_credit\" coa-id=\"" + coa_id + "\"></td>" +
                    "<td class=\"text-center\" ><select name=\"dept_id[" + rowIndex + "]\" class=\"form-control form-filter input-sm select2me \">" +
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
                    "<td style=\"padding-top: 5px;\"><a class=\"btn btn-danger btn-xs tooltips\" data-original-title=\"Remove\" href=\"javascript:;\" onclick=\"delete_record(" + entryHeaderID + "," + rowIndex + ");\" ><i class=\"fa fa-times\"></i></a></td>" +
                    "</tr>";

                //$('#datatable_detail tbody').prepend(newRowContent);
                $('#datatable_detail tbody').append(newRowContent);

                $('select.select2me').select2();
                $('select[name="dept_id[' + rowIndex +']"]').select2('val',general_dept_id);

                rowIndex++;

                handleCalculation();
                handleMask();

                $('#ajax-modal').modal('hide');
            }else{
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No COA selected',
                    container: grid_trx.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

        var isSaved = false;
        $('button[name="save"]').on('click', function(e){
            e.preventDefault();

            if(validateInput()){
                if(!isSaved) {
                    isSaved = true;

                    var url = '<?php echo base_url('cashier/cashbook/submit_entry.tpd');?>';
                    $("#form-entry").attr("method", "post");
                    $('#form-entry').attr('action', url).submit();
                }
                //$('#form-entry').submit();
            }
        });

        $('button[name="save_close"]').on('click', function(e){
            e.preventDefault();

            if(validateInput()){
                if(!isSaved) {
                    isSaved = true;

                    var url = '<?php echo base_url('cashier/cashbook/submit_entry.tpd');?>';
                    $("#form-entry").append('<input type="hidden" name="save_close" value="">');
                    $("#form-entry").attr("method", "post");
                    $('#form-entry').attr('action', url).submit();
                }
            }
        });

        var isPosted = false;
        $('#submit-posting').click(function(e) {
            e.preventDefault();

            $('#ajax-posting').modal('hide');

            var cashEntryId = parseInt($('input[name="cashentry_id"]').val()) || 0;
            var posting_date = $('input[name="c_posting_date"]').val();

            //console.log('entryHeaderID ' + cashEntryId);
            if(cashEntryId > 0){
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
                            if(!isPosted) {
                                //console.log(result);
                                Metronic.blockUI({
                                    boxed: true
                                });

                                isPosted = true;

                                $.ajax({
                                        type: "POST",
                                        url: "<?php echo base_url('cashier/cashbook/ajax_posting_journal_by_id');?>",
                                        dataType: "json",
                                        data: {entryheader_id: cashEntryId, posting_date: posting_date},
                                        async:false
                                    })
                                    .done(function (msg) {
                                        Metronic.unblockUI();

                                        if (msg.type == '1') {
                                            //console.log('Debug ' + msg.debug);
                                            toastr["success"](msg.message, "Success");
                                            if (msg.redirect_link != '') {
                                                window.location.assign(msg.redirect_link);
                                            }
                                        }
                                        else {
                                            toastr["error"](msg.message, "Warning");
                                        }
                                    });
                            }else{
                                toastr["warning"]("In Processing ! Please wait ...", "Warning");
                            }
                        }
                    }
                });
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
            //console.log(result);
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

            //data-other-2
            if(result[5] != null)
            {
                $('input[name="feature_id"]').val(result[5]);
            }

            if(result[4] == 'RV'){
                $('#label_subject').html("Receive From");
            }else if(result[4] == 'PV'){
                $('#label_subject').html("Payment To");
            }else if(result[4] == 'CA'){
                $('#label_subject').html("Adjust From");
            }else{
                $('#label_subject').html('Receive / Payment');
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

    function posting_record(entryHeaderId){
        var $modal_cal = $('#ajax-posting');

        if($("#form-entry").valid()){

            $modal_cal.modal();
        }else{
            toastr["error"]("Please save your changes first !", "Warning");
        }
    }

    function isCFExist(){
        var showCF = 1;

        var counter = $('#datatable_detail > tbody > tr ').length;
        if(counter > 0){
            $('#datatable_detail > tbody > tr ').each(function() {
                var debitAmount = parseInt($(this).find('input[name*="is_cf"]').val());

                if(debitAmount > 0){
                    showCF = 0;
                }
            });
        }

        return showCF;
    }

</script>