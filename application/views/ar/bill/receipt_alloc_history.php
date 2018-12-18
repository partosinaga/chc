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
                            <i></i>&nbsp;Receipt Allocation
                            <div class="btn-group btn-group-devided">
                                <a href="<?php echo base_url('ar/corporate_bill/receipt_al/1.tpd');?>" class="btn btn-transparent grey-gallery btn-circle btn-sm">Manage</a>
                                <a href="javascript:;" class="btn btn-transparent grey-gallery btn-circle btn-sm active">History</a>
                            </div>
						</div>
						<div class="actions">
                            <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                <a href="<?php echo base_url('ar/corporate_bill/receipt_al/3/0.tpd');?>" class="btn default yellow-stripe">
                                    <i class="fa fa-plus"></i>
							<span class="hidden-480">
							Allocation </span>
                                </a>
                            <?php } ?>
						</div>
					</div>
					<div class="portlet-body table-responsive">
						<div class="table-container">
                            <div class="col-md-12" style="padding-bottom: 100px;">
                            <table class="table table-striped table-bordered table-hover dataTable table-po-detail" id="table_manage">
                                <thead>
                                <tr role="row" class="heading">
                                    <th width="3%" >

                                    </th>
                                    <th width="9%">
                                        Doc No
                                    </th>
                                    <th width="8%">
                                        Date
                                    </th>
                                    <th >
                                        Client
                                    </th>
                                    <th width="8%">
                                        Receipt No
                                    </th>
                                    <th width="12%" >
                                        Amount
                                    </th>
                                    <th width="12%" >
                                        Allocated
                                    </th>
                                    <th style="width:9%;">
                                        Actions
                                    </th>
                                </tr>
                                <tr role="row" class="filter bg-grey-steel">
                                    <td style="vertical-align: middle;">

                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_no">
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_company">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="filter_receipt_no">
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
                            <input type="hidden" name="posting_date" value="">
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
<script src="<?php echo base_url('assets/custom/js/ar/receipt_alloc.js'); ?>"></script>
<script>
	$(document).ready(function(){
        Toaster.init();

        FormJS.init({
            table_manage_ajax_url : "<?php echo base_url('ar/corporate_bill/get_receipt_al_history/' . get_menu_id());?>"
        });

	});

</script>