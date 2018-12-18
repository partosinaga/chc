<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <?php
                $breadcrumbs = get_menu_name($this->uri->segment(1), $this->uri->segment(2), $this->uri->segment(3));
                foreach ($breadcrumbs as $breadcrumb) {
                    echo $breadcrumb;
                }
                ?>
            </ul>
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class')); ?>
                <!-- Begin: life time stats -->
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-check-square-o"></i>SO Approval
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('sales/setup/approval/1.tpd'); ?>"
                               class="btn default yellow-stripe">
                                <i class="fa fa-plus"></i> <span class="hidden-480"> New Approval </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container table-responsive">
                            <div class="col-md-12" style="padding-bottom: 90px;">
                                <table class="table table-striped table-bordered table-hover dataTable" id="datatable_ajax">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th width="1%"> # </th>
                                        <th>Approval name</th>
                                        <th class="text-center" width="12%">Department</th>
                                        <th class="text-right" width="12%">Min Amount</th>
                                        <th class="text-right" width="12%">Max Amount</th>
                                        <th class="text-center" width="12%">Level</th>
                                        <th class="text-center" width="8%">Status</th>
                                        <th class="text-center" width="8%">Type</th>
                                        <th class="text-center" width="8%">#</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach($approval as $row):?>
                                            <tr>
                                                <td><?php echo $i++  ?></td>
                                                <td><?php echo $row->user_fullname ?></td>
                                                <td align="center"><?php echo $row->department_desc ?></td>
                                                <td align="right"><?php echo number_format($row->min_amount) ?></td>
                                                <td align="right"><?php echo number_format($row->max_amount) ?></td>
                                                <td align="center">Level <?php echo number_format($row->level) ?></td>
                                                <td align="center"><?php echo  get_doc_sales_name($row->document_type); ?></td>
                                                <td align="center"><?php echo  get_status_active($row->status); ?></td>
                                                <td align="center">
                                                    <div class="btn-group">
                                                        <a href="<?php echo site_url('sales/setup/approval/1/'.$row->sales_approval_id) ?>"><button class="btn green-meadow btn-xs dropdown-toggle" type="button" aria-expanded="false"> View&nbsp;&nbsp;<i class="fa fa-search"></i>
                                                        </button> </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
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
