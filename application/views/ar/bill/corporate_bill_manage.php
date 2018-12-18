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
							<i class="fa fa-users"></i>Pending Bills
						</div>
						<div class="actions">
                            <a style="margin-left:20px;" href="javascript:;" class="btn-circle btn blue" id="submit-bill"><i class="fa fa-save"></i>&nbsp;Submit</a>
						</div>
					</div>
					<div class="portlet-body table-responsive">
						<div class="table-container">
                            <div class="col-md-12" style="padding-bottom: 100px;">
                            <form action="#" onsubmit="return false;" method="post" id="form-entry" class="form-horizontal" >
                            <div class="table-actions-wrapper">
                                <label class="control-label col-md-4" for="filter_date">Period </label>
                                <div class="col-md-8">
                                    <div class="input-group date date-picker" id="filter_period_date" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control text-center form-filter-wrapper input-sm" name="filter_date" value="<?php echo date('d-m-Y');?>" readonly >
                                        <span class="input-group-btn">
                                            <button class="btn default btn-sm" type="button" ><i class="fa fa-calendar" ></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="table_manage">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="2%" >

                                    </th>
                                    <th width="8%">
                                        Bill Date
                                    </th>
                                    <!--th >
                                        Bill No
                                    </th-->
                                    <th width="25%">
                                        Client
                                    </th>
                                    <th >
                                        Description
                                    </th>
                                    <th width="9%" >
                                        Amount
                                    </th>
                                    <th width="9%" >
                                        Tax
                                    </th>
                                    <th width="9%" >
                                        Subtotal
                                    </th>
                                    <th width="8%">
                                        Actions
                                    </th>
                                </tr>
                                <tr role="row" class="filter bg-grey-steel">
                                    <td style="vertical-align: middle;">
                                        <input type="checkbox" id="checkall" />
                                    </td>
                                    <td>

                                    </td>
                                    <!--td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_no">
                                    </td-->
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_name">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_subject">
                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td >
                                        <div class="text-center">
                                            <button class="btn btn-sm yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                            <button class="btn btn-sm red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
						    </form>
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
<script src="<?php echo base_url('assets/custom/js/ar/corporate_bill_manage.js'); ?>"></script>
<script>
    $(document).ready(function(){
        <?php echo picker_input_date(true,false);?>
        Toaster.init();
        FormJS.init({
            table_manage_ajax_url : "<?php echo base_url('ar/corporate_bill/get_bill_manage/' . get_menu_id());?>",
            submit_ajax_url : "<?php echo base_url('ar/corporate_bill/ajax_submit_bill');?>"
        });
    });
</script>