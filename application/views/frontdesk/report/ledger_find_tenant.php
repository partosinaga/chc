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
                        <div class="panel ">
                            <div class="panel-title">
                                <!--
                                <div class="caption">
                                    <i>Ledger Report</i>
                                </div> -->
                            </div>
                            <div class="panel-body">
                                <div class="input-group date date-picker input-daterange " data-date-format="dd-mm-yyyy">
                                    <div class="form-group">
                                        <div class="col-md-2"  style="vertical-align: middle;">
                                            <label class="btn ">Period</label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="input-group date-picker input-daterange" id="datepicker-range">
                                                <input type="text" class="form-control date_me" name="date_from" id="date_from" value="<?php echo date('d-m-Y');?>">
                                                            <span class="input-group-addon">
                                                            to </span>
                                                <input type="text" class="form-control date_me" name="date_to" id="date_to" value="<?php echo date('d-m-Y', strtotime('+30 days'));?>">

                                            </div>
                                        </div>
                                        <div class="col-md-2"  style="vertical-align: middle;">
                                            <button type="button" class="btn green" name="save" id="btn-submit" data-url="<?php echo base_url('finance/ledger/pdf_ledger/'); ?>">Generate</button>
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
<form action="javascript:;" id="form-entry" class="form-horizontal hide" method="post">
</form>

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    var rowIndex = 0;

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

        var grid_trx = new Datatable();
        var $modal = $('#ajax-modal');
 
  
    });
 

    $('#btn-submit').click(function(e) {
         
            e.preventDefault();

            //var url = $(this).attr('data-url');

            var dateStart = $('#date_from').val();
            var dateTo = $('#date_to').val();
            var coa_code_list = '';
            $('#dt_ledger_coa > tbody > tr').find('input').each(function(){
                if($(this).attr('name').lastIndexOf('coa_id') > -1 ){
                    coa_code_list += $(this).attr('value') + '-';
                }
            });
            console.log('COA ' + coa_code_list);
            var url = '<?php echo base_url('frontdesk/report/pdf_ledger_tenant.tpd');?>';
            var params = '<input type="hidden" name="date_start" value="' + dateStart + '">' +
                         '<input type="hidden" name="date_to" value="' + dateTo + '">' +
                         '<input type="hidden" name="is_pdf" value="0">';
            $("#form-entry").append(params);
            $("#form-entry").attr("method", "post");
            $("#form-entry").attr("target", "_blank");
            $('#form-entry').attr('action', url).submit();
 
    });

</script>