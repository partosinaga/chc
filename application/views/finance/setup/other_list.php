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
               <?php
               echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
               ?>

               <!-- TAX TYPE LIST-->
               <div class="row">
                   <div class="col-md-12 ">
                       <!-- BEGIN TAB PORTLET-->
                       <div class="box blue-ebonyclay" >
                           <div class="portlet-title blue-ebonyclay" >
                               <ul class="nav nav-tabs">
                                   <li class="active" >
                                       <a href="#portlet_taxtype" data-toggle="tab" >
                                           <i class="fa fa-star"></i>
                                           Tax Type</a>
                                   </li>
                                   <li>
                                       <a href="#portlet_currency" data-toggle="tab" >
                                           <i class="fa fa-money"></i>
                                           Currency </a>
                                   </li>
                               </ul>

                           </div>
                           <div class="portlet-body ">
                               <div class="tab-content">
                                   <div class="tab-pane active" id="portlet_taxtype">
                                       <!-- Begin: life time stats -->
                                       <div class="portlet blue-madison box">
                                           <div class="portlet-title">
                                               <div class="caption">
                                                   Tax Type
                                               </div>
                                               <div class="actions">
                                                   <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                                       <a href="<?php echo base_url('finance/setup/other_manage')?>/2.tpd" class="btn default grey-stripe">
                                                           <i class="fa fa-plus"></i>
                                <span class="hidden-480">
                                New Tax Type </span>
                                                       </a>
                                                   <?php } ?>
                                               </div>
                                           </div>
                                           <div class="portlet-body ">
                                               <div class="table-container">
                                                   <table class="table table-striped table-bordered table-hover table-po-detail" id="taxtype_ajax">
                                                       <thead>
                                                       <tr role="row" class="heading">
                                                           <th>
                                                               CODE
                                                           </th>
                                                           <th>
                                                               DESCRIPTION
                                                           </th>
                                                           <th>

                                                           </th>
                                                           <th>
                                                               TYPE
                                                           </th>
                                                           <th>
                                                               TAX (%)
                                                           </th>
                                                           <th>
                                                               TAX COA
                                                           </th>
                                                           <th>
                                                               WHT (%)
                                                           </th>
                                                           <th>
                                                               WHT COA
                                                           </th>
                                                           <th>
                                                               STATUS
                                                           </th>
                                                           <th style="width:9%;">

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
                                   <div class="tab-pane" id="portlet_currency">
                                       <!-- CURRENCY LIST -->
                                       <div class="row">
                                           <div class="col-md-6 ">
                                               <!-- Begin: life time stats -->
                                               <div class="portlet blue-madison box">
                                                   <div class="portlet-title">
                                                       <div class="caption">
                                                           Currency
                                                       </div>
                                                       <div class="actions">
                                                           <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                                               <a href="<?php echo base_url('finance/setup/other_manage')?>/0.tpd" class="btn default grey-stripe">
                                                                   <i class="fa fa-plus"></i>
                                <span class="hidden-480">
                                New Currency </span>
                                                               </a>
                                                           <?php } ?>
                                                       </div>
                                                   </div>
                                                   <div class="portlet-body ">
                                                       <div class="table-container">
                                                           <table class="table table-striped table-bordered table-hover table-po-detail" id="currency_ajax">
                                                               <thead>
                                                               <tr role="row" class="heading">
                                                                   <th>
                                                                       CODE
                                                                   </th>
                                                                   <th>
                                                                       DESCRIPTION
                                                                   </th>
                                                                   <th style="width:9%;">

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
                                       <!-- END CURRENCY LIST-->
                                   </div>

                               </div>
                           </div>
                       </div>
                       <!-- END TAB PORTLET-->


                   </div>
               </div>
               <!-- END TAX TYPE LIST-->

		<!-- END PAGE CONTENT-->
           </div>
       </div>
	</div>
</div>
<!-- END CONTENT -->

<script>
    $(document).ready(function(){
        var handleTable1 = function () {
            var grid = new Datatable();

            grid.init({
                src: $("#taxtype_ajax"),
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
                        {sWidth: '7%', "sClass":"text-center", bSortable:false},
                        {bSortable:false},
                        {bSortable:false, sWidth: '9%', "sClass":"text-center"},
                        {bSortable:false, sWidth: '6%', "sClass":"text-center"},
                        {bSortable:false, sWidth: '6%', "sClass":"text-center"},
                        {bSortable:false, sWidth: '7%', "sClass":"text-center"},
                        {bSortable:false, sWidth: '6%', "sClass":"text-center"},
                        {bSortable:false, sWidth: '7%', "sClass":"text-center"},
                        {bSortable:false, sWidth: '8%', "sClass":"text-center"},
                        {sWidth: '5%' , "sClass":"text-center", bSortable:false}
                    ],
                    "pageLength": 100, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('finance/setup/taxtype_list');?>"
                    }
                }
            });

            // handle group actionsubmit button click
            grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
                e.preventDefault();
                var action = $(".table-group-action-input", grid.getTableWrapper());
                if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                    grid.setAjaxParam("customActionType", "group_action");
                    grid.setAjaxParam("customActionName", action.val());
                    grid.setAjaxParam("id", grid.getSelectedRows());
                    grid.getDataTable().ajax.reload();
                    grid.clearAjaxParams();
                } else if (action.val() == "") {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'Please select an action',
                        container: grid.getTableWrapper(),
                        place: 'prepend'
                    });
                } else if (grid.getSelectedRowsCount() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: grid.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });
        }

		var handleRecords = function () {
			var grid = new Datatable();

			grid.init({
				src: $("#currency_ajax"),
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
                        {sWidth: '7%', "sClass":"text-center", bSortable:false},
                        {bSortable:false},
                        {sWidth: '5%' , "sClass":"text-center", bSortable:false}
                    ],
					"pageLength": 10, // default record count per page
					"ajax": {
						"url": "<?php echo base_url('finance/setup/currency_list');?>",
					}
				}
			});

			// handle group actionsubmit button click
			grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
				e.preventDefault();
				var action = $(".table-group-action-input", grid.getTableWrapper());
				if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
					grid.setAjaxParam("customActionType", "group_action");
					grid.setAjaxParam("customActionName", action.val());
					grid.setAjaxParam("id", grid.getSelectedRows());
					grid.getDataTable().ajax.reload();
					grid.clearAjaxParams();
				} else if (action.val() == "") {
					Metronic.alert({
						type: 'danger',
						icon: 'warning',
						message: 'Please select an action',
						container: grid.getTableWrapper(),
						place: 'prepend'
					});
				} else if (grid.getSelectedRowsCount() === 0) {
					Metronic.alert({
						type: 'danger',
						icon: 'warning',
						message: 'No record selected',
						container: grid.getTableWrapper(),
						place: 'prepend'
					});
				}
			});
		}

        //initPickers();
        handleTable1();
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