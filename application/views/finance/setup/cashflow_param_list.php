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
       <div class="tab-content">
           <div class="tab-pane active">
               <!-- MASTER CF PARAM LIST -->
               <div class="row">
                   <div class="col-md-7 ">
                       <!-- Begin: life time stats -->
                       <div class="portlet yellow-gold box">
                           <div class="portlet-title">
                               <div class="caption">
                                   <i class="fa fa-bank"></i>Cash Flow Account Parameter
                               </div>
                               <div class="actions">
                                   <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                       <a href="<?php echo base_url('finance/setup/cashflow_param_manage')?>/0.tpd" class="btn default grey-stripe">
                                           <i class="fa fa-plus"></i>
                                <span class="hidden-480">
                                Add Parameter </span>
                                       </a>
                                   <?php } ?>
                               </div>
                           </div>
                           <div class="portlet-body ">
                               <div class="table-container">
                                   <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                   <table class="table table-striped table-bordered table-hover table-po-detail" id="datatable_cf_param">
                                       <thead>
                                       <tr role="row" class="heading">
                                           <th>
                                               COA Code
                                           </th>
                                           <th>
                                               Description
                                           </th>
                                           <th style="width:9%;">
                                               Actions
                                           </th>
                                       </tr>
                                       </thead>
                                       <tbody>
                                       </tbody>
                                   </table>
                               </div>
                           </div>
                       </div>
                       <!-- End: life time stats -->
                   </div>
               </div>
               <!-- END MASTER BANK LIST-->
		<!-- END PAGE CONTENT-->
           </div>
       </div>
	</div>
</div>
<!-- END CONTENT -->

<script>
    $(document).ready(function(){
        var handleRecords = function () {
			var grid = new Datatable();

			grid.init({
				src: $("#datatable_cf_param"),
				onSuccess: function (grid) {
					// execute some code after table records loaded
				},
				onError: function (grid) {
					// execute some code on network or other general error  
				},
				onDataLoad: function(grid) {
					// execute some code on ajax data load
				},
                loadingMessage: 'Populating ...',
				dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 

					// Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
					// setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
					// So when dropdowns used the scrollable div should be removed. 
					//"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    paging: false,
                    info:false,
                    ordering:false,
					"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

					"lengthMenu": [
						[-1],
						["All"] // change per page values here
					],
                    "aoColumns" : [
                        {sWidth: '10%', "sClass":"text-center", bSortable:false},
                        {bSortable:false},
                        {sWidth: '11%' , "sClass":"text-center", bSortable:false}
                    ],
					"pageLength": 10, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('finance/setup/cashflow_param_list');?>"
					}
				}
			});
		}

        handleRecords();

        $('.btn-bootbox').live('click', function(){
            var link = $(this).attr('data-link');
            bootbox.confirm("Are you sure?", function(result) {
                if (result === true) {
                    window.location.assign(link);
                }
            });
        });
	});

</script>