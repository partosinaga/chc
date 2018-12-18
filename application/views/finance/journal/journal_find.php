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
                                <div >
                                    <div class="form-group">
                                        <div class="col-md-2"  style="vertical-align: middle;">
                                            <label class="btn ">Period</label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group date-picker input-daterange" id="datepicker-range" data-date-format="dd-mm-yyyy">
                                                <input type="text" class="form-control date_me" name="date_from" id="date_from" value="<?php echo date('d-m-Y');?>">
                                                            <span class="input-group-addon">
                                                            to </span>
                                                <input type="text" class="form-control date_me" name="date_to" id="date_to" value="<?php echo date('d-m-Y', strtotime('+30 days'));?>">

                                            </div>

                                        </div>
                                        <div class="col-md-4" style="vertical-align: middle;padding-top: 8px;" >
                                            <input type="checkbox" id="chk_include_date" class="form-control" >Include
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MASTER BANK LIST -->
                            <div class="row">
                                <div class="col-md-12 ">
                                    <!-- Begin: life time stats -->
                                    <div class="portlet grey-mint box">
                                        <div class="portlet-title" >
                                            <div class="caption">
                                                <i class="fa fa-slack"></i> Posted Journal
                                            </div>
                                        </div>
                                        <div class="portlet-body ">
                                            <div class="row" >
                                                <div class="col-md-8">
                                                    <label class="col-md-3 control-label" style="vertical-align: middle;padding-top: 6px;">Journal No</label>
                                                    <div class="col-md-6">
                                                        <input class="form-control" type="text" id="f_journal_no" placeholder="Journal No">
                                                    </div>
                                                </div>
                                            </div><br/>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" style="vertical-align: middle;padding-top: 6px;">Module</label>
                                                        <div class="col-md-9">
                                                            <select name="f_module" id="f_module" class="form-control form-filter input-sm select2me ">
                                                                <option value="">- ALL -</option>
                                                                <?php
                                                                $qry_module = $this->db->query('select distinct modul as modul from gl_postjournal_header where status <> ' . STATUS_DELETE);
                                                                if($qry_module->num_rows() > 0){
                                                                    foreach($qry_module->result() as $row){
                                                                        echo '<option value="' . $row->modul . '">' . $row->modul . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
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
                                        <button type="button" class="btn green" name="save" id="btn-submit" data-url="<?php echo base_url('finance/ledger/pdf_postedjournal/'); ?>">Find Journal</button>
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
        var isIncludeDate = $('#chk_include_date').prop('checked');
        var journalNo = $('#f_journal_no').val();
        var module = $('#f_module').val();

        if(isIncludeDate || journalNo != '' || module != ''){
            e.preventDefault();

            var url = $(this).attr('data-url');

            var dateStart = $('#date_from').val();
            var dateTo = $('#date_to').val();

            if(!isIncludeDate){
                dateStart ='-';
                dateTo = '-';
            }

            if(journalNo == ''){
                journalNo = '-';
            }

            url += '/' + dateStart + '/' + dateTo + '/' + journalNo + '/' + module;

            window.open(encodeURI(url),'_blank');
        }

    });

</script>