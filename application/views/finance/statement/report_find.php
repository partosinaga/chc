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
                    <div class="col-md-7 ">
                        <?php
                        echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
                        ?>
                        <!-- Begin: life time stats -->
                        <div class="panel">
                            <div class="panel-body">
                                <!-- MASTER BANK LIST -->
                                <div class="row">
                                    <div class="col-md-11 ">
                                        <!-- Begin: life time stats -->
                                        <div class="portlet grey-mint box">
                                            <div class="portlet-title" >
                                                <div class="caption">
                                                    <i class="fa fa-slack"></i> Financial Statement
                                                </div>
                                            </div>
                                            <div class="portlet-body ">
                                                <div class="row" >
                                                    <div class="form-group">
                                                        <label class="col-md-4 control-label text-right" style="vertical-align: middle;padding-top: 6px;"><?php echo $report_title; ?> Type</label>
                                                        <div class="col-md-5">
                                                            <select name="statement_type" id="statement_type" class="form-control form-filter input-sm select2me ">
                                                                <?php
                                                                if($report_type == REPORTTYPE::STATEMENT_BALANCE_SHEET ||
                                                                    $report_type == REPORTTYPE::STATEMENT_TRIAL_BALANCE){
                                                                    echo '<option value="' . REPORTTYPE::STATEMENT_BALANCE_SHEET . '">' . 'Balance Sheet' .  '</option>
                                                                      <option value="' . REPORTTYPE::STATEMENT_TRIAL_BALANCE . '">' . 'Trial Balance' .  '</option>';
                                                                }else if($report_type == REPORTTYPE::STATEMENT_PROFIT_LOSS_STD ||
                                                                    $report_type == REPORTTYPE::STATEMENT_PROFIT_LOSS_BUDGET ||
                                                                    $report_type == REPORTTYPE::STATEMENT_PROFIT_LOSS_DEPT){
                                                                    echo '<option value="' . REPORTTYPE::STATEMENT_PROFIT_LOSS_STD . '">' . 'Standard' .  '</option>
                                                                      <option value="' . REPORTTYPE::STATEMENT_PROFIT_LOSS_BUDGET . '">' . 'Compare to Budget' .  '</option>';
                                                                      //'<option value="' . REPORTTYPE::STATEMENT_PROFIT_LOSS_DEPT . '">' . 'By Department' .  '</option>';

                                                                }else if($report_type == REPORTTYPE::STATEMENT_TRIAL_CASHFLOW ||
                                                                    $report_type == REPORTTYPE::STATEMENT_CASHFLOW) {
                                                                    echo '<option value="' . REPORTTYPE::STATEMENT_CASHFLOW . '">' . 'Cash Flow' .  '</option>
                                                                      <option value="' . REPORTTYPE::STATEMENT_TRIAL_CASHFLOW . '">' . 'Trial Cash Flow (Close)' .  '</option>';
                                                                }

                                                                ?>
                                                            </select>
                                                        </div>

                                                    </div>
                                                    <div class="form-group">&nbsp;</div>
                                                    <div class="form-group" >
                                                        <label class="col-md-4 control-label text-right" style="vertical-align: middle;padding-top: 6px;">Period as of</label>
                                                        <div class="col-md-2">
                                                            <select class="form-control select2me" name="month" id="select_month">
                                                                <?php
                                                                for($i = 1; $i <= 12; $i++){
                                                                    echo '<option value="' . $i . '" ' . ($i == date('n') ? 'selected' : '') . '>' . $i . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select class="form-control select2me" name="year" id="select_year">
                                                                <?php
                                                                $qry = $this->db->query("SELECT distinct closingyear FROM gl_closing_header WHERE is_yearly <= 0 AND status = 1 ORDER BY closingyear");
                                                                if($qry->num_rows() > 0){
                                                                    foreach($qry->result() as $row){
                                                                        echo '<option value="' . $row->closingyear . '" ' . ($row->closingyear == date('Y') ? 'selected' : '') . '>' . $row->closingyear . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">&nbsp;</div>
                                                </div>

                                                <div class="row">
                                                    <br>
                                                    <div class="form-group">
                                                        <div class="col-md-4">

                                                        </div>
                                                        <div class="col-md-7">

                                                            <button type="button" class="btn green " name="save" id="btn-submit" data-url="<?php echo base_url('finance/statement/generate_statement/'); ?>">Generate</button>

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

    });

    $('#btn-submit').click(function(e) {
        var reportType = $('#statement_type').val();
        var periodMonth = $('#select_month').val();
        var periodYear = $('#select_year').val();

        if(periodMonth != '' || periodYear != ''){
            e.preventDefault();

            var url = $(this).attr('data-url');

            url += '/' + reportType + '/' + periodMonth + '/' + periodYear ;

            window.open(encodeURI(url),'_blank');

            //window.location = encodeURI(url);
            //window.location.reload();
        }

    });

</script>