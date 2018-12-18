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
                    <div class="col-md-12 ">
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
                                                    <i class="fa fa-slack"></i> Housekeeping Status
                                                </div>
                                                <div class="actions" style="padding-top: 12px;">
                                                    <input type="checkbox" id="checkall" class="form-control bold" />Check All
                                                </div>
                                            </div>
                                            <div class="portlet-body ">
                                                <div class="row" >
                                                    <div class="form-group" >
                                                        <div class="col-md-12">
                                                            <div class="row" id="find-table">
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::IS) ?>" ><span><?php echo '[' . HSK_STATUS::IS . '] <i>' . HSK_STATUS::caption(HSK_STATUS::IS) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::ISC) ?>" ><span><?php echo '[' . HSK_STATUS::ISC . '] <i>' . HSK_STATUS::caption(HSK_STATUS::ISC) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::OD) ?>" ><span><?php echo '[' . HSK_STATUS::OD . '] <i>' . HSK_STATUS::caption(HSK_STATUS::OD) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::OC) ?>" ><span><?php echo '[' . HSK_STATUS::OC . '] <i>' . HSK_STATUS::caption(HSK_STATUS::OC) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::VD) ?>" ><span><?php echo '[' . HSK_STATUS::VD . '] <i>' . HSK_STATUS::caption(HSK_STATUS::VD) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::VC) ?>" ><span><?php echo '[' . HSK_STATUS::VC . '] <i>' . HSK_STATUS::caption(HSK_STATUS::VC) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::ED) ?>" ><span><?php echo '[' . HSK_STATUS::ED . '] <i>' . HSK_STATUS::caption(HSK_STATUS::ED) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::ED_EA) ?>" ><span><?php echo '[' . HSK_STATUS::ED_EA . '] <i>' . HSK_STATUS::caption(HSK_STATUS::ED_EA) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::VD_EA) ?>" ><span><?php echo '[' . HSK_STATUS::VD_EA . '] <i>' . HSK_STATUS::caption(HSK_STATUS::VD_EA) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::VC_EA) ?>" ><span><?php echo '[' . HSK_STATUS::VC_EA . '] <i>' . HSK_STATUS::caption(HSK_STATUS::VC_EA) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::IS_EA) ?>" ><span><?php echo '[' . HSK_STATUS::IS_EA . '] <i>' . HSK_STATUS::caption(HSK_STATUS::IS_EA) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::OS) ?>" ><span><?php echo '[' . HSK_STATUS::OS . '] <i>' . HSK_STATUS::caption(HSK_STATUS::OS) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="checkbox" name="chk_hsk_status" value="<?php echo HSK_STATUS::stat_to_idx(HSK_STATUS::OO) ?>" ><span><?php echo '[' . HSK_STATUS::OO . '] <i>' . HSK_STATUS::caption(HSK_STATUS::OO) . '</i>'; ?></span>
                                                                </div>
                                                                <div class="col-md-6">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding-top: 10px;">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <button type="button" class="btn green " name="save" id="btn-submit" data-url="<?php echo base_url('housekeeping/report/pdf_room_status/'); ?>">Generate</button>
                                                            &nbsp;
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

            var hsk_status = $("#find-table input:checkbox:checked").map(function(){
                return $(this).val();
            }).get().join("_");

            if(hsk_status != ''){
                var url = $(this).attr('data-url');
                url += '/' + hsk_status ;

                window.open(encodeURI(url),'_blank');
            }else{
                toastr["warning"]("Please choose at least 1(one) status", "Warning");
            }
        });

        $("#checkall").click(function () {
            if ($("#checkall").is(':checked')) {
                $("#find-table input[type=checkbox]").each(function () {
                    //$(this).attr("checked", "checked");
                    $(this).prop("checked", true);
                    $(this).parent('span').addClass('checked');
                });

            } else {
                $("#find-table input[type=checkbox]").each(function () {
                    $(this).prop("checked", false);
                    //$(this).removeAttr("checked");
                    $(this).parent('span').removeClass('checked');
                });
            }
        });
    });




</script>