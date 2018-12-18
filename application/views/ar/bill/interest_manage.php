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
                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
				<!-- Begin: life time stats -->
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-users"></i>Penalty Calculation
						</div>
						<div class="actions">
                            <div class="col-md-12">
                                <label class="control-label text-right col-md-2" for="filter_date" style="padding-top: 5px;width:150px;" ><strong>BILLING PERIOD</strong> </label>
                                <div style="width: 140px;float:left;padding-left:10px;margin-right: 10px;">
                                    <div class="input-group date date-picker " id="filter_period_date" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control text-center input-sm" name="period_date" value="<?php echo date('d-m-Y');?>" readonly >
                                    <span class="input-group-btn">
                                        <button class="btn default btn-sm" type="button" ><i class="fa fa-calendar" ></i></button>
                                    </span>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn_calculate margin-bottom red-flamingo" data-original-title="Calculate Penalty" data-placement="top" data-container="body" style="margin-top: 1px;width:150px;"><i class="fa fa-copy"></i> Calculate Penalty</button>
                            </div>

                        </div>
					</div>
					<div class="portlet-body table-responsive">
                        <div class="table-container">
                            <div class="col-md-12" style="padding-bottom: 100px;">
                            <div class="table-actions-wrapper ">

                            </div>
                            <table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="table_manage">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="20%">
                                        NAME
                                    </th>
                                    <th class="text-center" width="7%">
                                        INV NO
                                    </th>
                                    <th class="text-center" >
                                        DESCRIPTION
                                    </th>
                                    <th class="text-center" width="8%">
                                        AMOUNT
                                    </th>
                                    <th class="text-center" width="7%">
                                        FROM
                                    </th>
                                    <th class="text-center" width="7%">
                                        TO
                                    </th>
                                    <th class="text-center" width="4%">
                                        DAYS
                                    </th>
                                    <th class="text-center" width="3%">
                                        %
                                    </th>
                                    <th class="text-center" width="8%">
                                        INTEREST
                                    </th>
                                    <th style="width:6%;">
                                        &nbsp;
                                    </th>
                                </tr>
                                <tr role="row" class="filter bg-grey-steel">
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_name">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_inv_no">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_desc">
                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <button class="btn btn-xs yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                            <button class="btn btn-xs red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
						</div>
                        </div>
					</div>
				</div>
				<!-- End: life time stats -->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
<!-- END CONTENT -->
<script src="<?php echo base_url('assets/custom/js/toaster.js'); ?>"></script>
<script src="<?php echo base_url('assets/custom/js/ar/interest_manage.js'); ?>"></script>
<script>
	$(document).ready(function(){
        <?php echo picker_input_date();?>

        Toaster.init();

        FormJS.init({
            status_cancel : "<?php echo STATUS_CANCEL; ?>",
            status_void : "<?php echo STATUS_DELETE; ?>",
            table_manage_ajax_url : "<?php echo base_url('ar/corporate_bill/get_interest_manage'); ?>",
            submit_ajax_url : "<?php echo base_url('ar/corporate_bill/ajax_submit_interest');?>",
            cancel_ajax_url : "<?php echo base_url('ar/corporate_bill/xcancel_penalty');?>"
        });

        FormJS.reloadGrid("<?php echo date('d-m-Y'); ?>");

	});

</script>