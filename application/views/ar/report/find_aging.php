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
                <div class="row">
                    <div class="col-md-8 ">
                        <?php
                        echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
                        ?>
                        <!-- Begin: life time stats -->
                        <div class="panel">
                            <div class="panel-body">
                                <!-- MASTER BANK LIST -->
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <!-- Begin: life time stats -->
                                        <div class="portlet grey-mint box">
                                            <div class="portlet-title" >
                                                <div class="caption">
                                                    <i class="fa fa-slack"></i>AR Aging
                                                </div>
                                            </div>
                                            <div class="portlet-body ">
                                                <div class="row" >
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label text-right" style="vertical-align: middle;padding-top: 6px;">Report date As Of</label>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                               <div class="input-group date-picker input-daterange " id="datepicker-range" data-date-format="dd-mm-yyyy">
                                                                    <input type="text" class="form-control date_me" name="date_aging" id="date_aging" value="<?php echo date('d-m-Y');?>">
																</div>
                                                                  
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="form-group">&nbsp;</div>
                                                    <div class="form-group" >
                                                        <label class="col-md-3 control-label text-right" style="vertical-align: middle;padding-top: 6px;">Company Name</label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="company_name" value="" class="form-control" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group">&nbsp;</div>
                                                </div>

                                                <div class="row">
                                                    <br>
                                                    <div class="form-group">
                                                        <div class="col-md-3">

                                                        </div>
                                                        <div class="col-md-7">

                                                            <button type="button" class="btn green " name="save" id="btn-submit" data-url="<?php echo base_url('ar/report/pdf_aging/'); ?>">Generate AR Aging</button>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End: life time stats -->
                                    </div>
                                </div>
                                <!-- END MASTER BANK LIST-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="text-center ">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        <!-- End: life time stats -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    $(document).ready(function(){
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true
            });
        }

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        

        $('#btn-submit').on('click',function(e) {
             var date_aging = $('input[name="date_aging"]').val();
             var company_name = $('input[name="company_name"]').val();

             if(date_aging != ''){
                 e.preventDefault();

                 var url = $(this).attr('data-url');
                 url += '/' + date_aging ;
                 url += '/' + company_name ;

                 window.open(encodeURI(url),'_blank');

                 //window.location = encodeURI(url);
                 //window.location.reload();
             }
        });
    });




</script>