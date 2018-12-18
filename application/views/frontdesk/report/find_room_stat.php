<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE HEADER-->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <?php
                $breadcrumbs = get_menu_name($this->uri->segment(1), $this->uri->segment(2), $this->uri->segment(3), $this->uri->segment(4));
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
                    <div class="col-md-4 ">
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
                                                    <i class="fa fa-slack"></i> Room Statictic
                                                </div>
                                            </div>
                                            <div class="portlet-body ">
                                                <div class="row" >
                                                    <div class="form-group" >
                                                        <div class="col-md-7">
                                                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                                <input type="text" class="form-control" name="report_date" value="<?php echo date('d-m-Y');?>" readonly >
                                                                <span class="input-group-btn">
                                                                    <button class="btn default" type="button" ><i class="fa fa-calendar" ></i></button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <button type="button" class="btn green " name="save" id="btn-submit" data-url="<?php echo base_url('frontdesk/report/pdf_room_stat/'); ?>">Generate</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End: life time stats -->
                                    </div>
                                </div>
                                <!-- END MASTER BANK LIST-->
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
            e.preventDefault();

            var report_date = $('input[name="report_date"]').val();
            var url = $(this).attr('data-url');
            url += '/' + report_date;

            window.open(encodeURI(url),'_blank');
        });
    });

</script>