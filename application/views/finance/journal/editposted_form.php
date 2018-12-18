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
							<i class="icon-note "></i>Edit Posted Journal
						</div>
						<div class="actions">

						</div>
					</div>
					<div class="portlet-body form">
						<form action="javascript:;" method="post" id="form-entry" class="form-horizontal" autocomplete="off">
							<input type="hidden" id="postheader_id" name="postheader_id" value="" />
                            <div class="form-actions top">
                                <div class="row">
									<div class="col-md-8">
										<button type="button" class="btn btn-circle purple hide" name="save" id="btn_save">Submit</button>
                                    </div>
								</div>
							</div>
							<div class="form-body">
								<?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
								<div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Journal No <span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="journal_no" value="" />
                                                    <span class="input-group-btn">
														<button class="btn blue-chambray" type="button" id="btn_lookup_journal"><i class="fa fa-search" ></i></button>
													</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
										<div class="form-group">
											<label class="control-label col-md-4">Date </label>
											<div class="col-md-6" >
												<input type="text" class="form-control" name="journal_date" value="" disabled/>
												</div>
											</div>
										</div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remark </label>
                                            <div class="col-md-10">
                                                <textarea name="journal_remarks" rows="3" class="form-control" readonly style="resize:vertical;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="row">
									<div class="col-md-12">
										<table class="table table-striped table-hover table-bordered table-po-detail" id="datatable_detail">
											<thead>
												<tr>
													<th class="text-center" style="width: 8%;"> COA</th>
													<th class="text-left" style="width: 30%;"> Description </th>
                                                    <th class="text-left" > Note </th>
													<th class="text-center" style="width: 9%;"> Debit </th>
													<th class="text-center" style="width: 9%;"> Credit </th>
													<th class="text-center" style="width: 5%;"> Dept </th>
												</tr>
											</thead>
											<tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th class="text-right" colspan="2"><span class="form-control-static">TOTAL</span></th>
                                                    <th ><input type="text" name ="total_debit" id ="total_debit" class="form-control text-right input-sm mask_currency" value="0" readonly/></th>
                                                    <th ><input type="text" name ="total_credit" id ="total_credit" class="form-control input-sm text-right mask_currency" value="0" readonly/></th>
                                                    <th >&nbsp;</th>
                                                </tr>
                                            </tfoot>
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

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script src="<?php echo base_url('assets/custom/js/toaster.js'); ?>"></script>
<script src="<?php echo base_url('assets/custom/js/journal/editposted_form.js'); ?>"></script>

<script>
    var rowIndex = <?php echo (isset($rowIndex) ? $rowIndex : 0) ; ?>;

    $(document).ready(function(){
        autosize($('textarea'));
        Toaster.init();
        FormJS.init();

	});

</script>