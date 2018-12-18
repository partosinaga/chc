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
							<i class="fa fa-users"></i>Maintenance Billing (Ready to Invoice)
						</div>
						<div class="actions">
                            <a style="margin-left:20px;" href="javascript:;" class="btn-circle btn blue" id="create-invoice"><i class="fa fa-save"></i>&nbsp;Invoice</a>
						</div>
					</div>
					<div class="portlet-body table-responsive">
						<div class="table-container">
                            <div class="col-md-12" style="padding-bottom: 100px;">
                            <form action="#" method="post" id="form-entry" class="form-horizontal" >
                            <table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="table_manage">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="2%" >

                                    </th>
                                    <th width="25%">
                                        Client
                                    </th>
                                    <th width="7%">
                                        Bill Date
                                    </th>
                                    <th >
                                        Description
                                    </th>
                                    <th width="5%" >
                                        Trx
                                    </th>
                                    <th width="7%" >
                                        Amount
                                    </th>
                                    <th width="6%" >
                                        Tax
                                    </th>
                                    <th width="7%" >
                                        Subtotal
                                    </th>
                                    <th style="width:9%;">

                                    </th>
                                </tr>
                                <tr role="row" class="filter bg-grey-steel table-po-detail">
                                    <td style="vertical-align: middle;">

                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_name">
                                    </td>
                                    <td>

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
                                    <td>

                                    </td>
                                    <td>
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

<div id="ajax-calendar" class="modal fade"  data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4>Invoice Date</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <label class="control-label col-md-2 text-right" style="padding-top: 8px;">Invoice Date</label>
                    <div class="col-md-4">
                        <div class="input-group date date-picker" data-date-format="dd-mm-yyyy" id="inv_date">
                            <input type="text" class="form-control" name="c_inv_date" value="" readonly>
					<span class="input-group-btn">
						<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
					</span>
                        </div>
                    </div>
                    <!--input type="hidden" name="c_inv_due_date" value="" id="inv_due_date"-->

                    <label class="control-label col-md-2 text-right" style="padding-top: 8px;" >Due Date</label>
                    <div class="col-md-4">
                        <div class="input-group date date-picker" data-date-format="dd-mm-yyyy" id="inv_due_date">
                            <input type="text" class="form-control" name="c_inv_due_date" value="" readonly>
					<span class="input-group-btn">
						<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
					</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue" id="submit-invoice">Submit</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script src="<?php echo base_url('assets/custom/js/toaster.js'); ?>"></script>
<script src="<?php echo base_url('assets/custom/js/ar/corporate_bill_maintain.js'); ?>"></script>
<script>
	$(document).ready(function(){
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: 'right',
                startDate:'<?php echo ymd_to_dmy(min_input_date()); ?>',
                autoclose: true
            });
        }

        Toaster.init();
        FormJS.init({
            table_manage_ajax_url : "<?php echo base_url('ar/corporate_bill/get_bill_maintenance/' . get_menu_id());?>",
            remove_ajax_url : "<?php echo base_url('ar/corporate_bill/rollback_maintenance_bill');?>",
            submit_ajax_url : "<?php echo base_url('ar/corporate_bill/create_invoices.tpd');?>"
        });
    });

</script>