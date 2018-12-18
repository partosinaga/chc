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
							<i class="fa fa-users"></i>Edit Listing (Ready to Posting)
						</div>
						<div class="actions">
                            <a style="margin-left:20px;" href="javascript:;" class="btn-circle btn blue" id="posting-button"><i class="fa fa-save"></i>&nbsp;Posting</a>
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
                                    <th width="8%">
                                        Inv No
                                    </th>
                                    <th >
                                        Client
                                    </th>
                                    <th width="8%">
                                        Date
                                    </th>
                                    <th width="9%">
                                        Due Date
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

                                    </th>
                                </tr>
                                <tr role="row" class="filter bg-grey-steel">
                                    <td style="vertical-align: middle;">
                                        <input type="checkbox" id="checkall" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_no">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_name">
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

<div id="ajax-calendar" class="modal fade bs-modal-sm"  data-keyboard="false" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Posting date :</h4>
            </div>
            <div class="modal-body">
                <div class="input-group date " data-date-format="dd-mm-yyyy"> <!--date-picker-->
                    <input type="text" class="form-control" name="c_posting_date" value="<?php echo (date('d-m-Y'));?>" readonly>
					<span class="input-group-btn hide">
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

<script src="<?php echo base_url('assets/custom/js/toaster.js'); ?>"></script>
<script src="<?php echo base_url('assets/custom/js/ar/edit_listing.js'); ?>"></script>
<script>
	$(document).ready(function(){
        Toaster.init();

        FormJS.init({
            table_manage_ajax_url : "<?php echo base_url('ar/corporate_bill/get_edit_listing/' . get_menu_id());?>",
            cancel_ajax_url : "<?php echo base_url('ar/corporate_bill/xcancel_edit_listing');?>",
            submit_ajax_url : "<?php echo base_url('ar/corporate_bill/xpost_edit_listing');?>"
        });

    });



</script>