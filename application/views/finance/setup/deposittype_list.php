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
                       <!-- Begin: life time stats -->
                       <div class="portlet blue-madison box">
                           <div class="portlet-title">
                               <div class="caption">
                                   Deposit Type
                               </div>
                               <div class="actions">
                                   <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                       <a href="<?php echo base_url('finance/setup/deposit_type_manage/1/')?>/0.tpd" class="btn default grey-stripe">
                                           <i class="fa fa-plus"></i>
                                <span class="hidden-480">
                                New Deposit Type </span>
                                       </a>
                                   <?php } ?>
                               </div>
                           </div>
                           <div class="portlet-body ">
                               <div class="table-container">
                                   <table class="table table-striped table-bordered table-hover" id="deposittype_ajax">
                                       <thead>
                                       <tr role="row" class="heading">
                                           <th>
                                               KEY
                                           </th>
                                           <th>
                                               DESCRIPTION
                                           </th>
                                           <th>
                                               COA CODE
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
                src: $("#deposittype_ajax"),
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
                dataTable: { //here you can define a typical datatable settings from http://datatables.net/usage/options

                    //Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                    //setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                    //So when dropdowns used the scrollable div should be removed.
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    paging: false,
                    info:false,
                    ordering:false,
                    "bStateSave": true, //save datatable state(pagination, sort, etc) in cookie.

                    "lengthMenu": [
                        [-1],
                        ["All"] //change per page values here
                    ],
                    "aoColumns" : [
                        {sWidth: '25%', "sClass":"text-left", bSortable:false},
                        {bSortable:false},
                        {bSortable:false, sWidth: '10%', "sClass":"text-center"},
                        {bSortable:false, sWidth: '10%', "sClass":"text-center"},
                        {sWidth: '9%' , "sClass":"text-center", bSortable:false}
                    ],
                    "pageLength": 100, //default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('finance/setup/deposit_type_list');?>"
                    }
                }
            });

            var tableWrapper = $("#deposittype_ajax_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            });

        }

        //initPickers();
        handleTable1();

        $('.btn-delete').live('click', function(){
            var link = $(this).attr('data-link');
            bootbox.confirm("Are you sure?", function(result) {
                if (result === true) {
                    window.location.assign(link);
                }
            });
        });
	});

</script>