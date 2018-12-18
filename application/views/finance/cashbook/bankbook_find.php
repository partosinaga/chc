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
                                                    <i class="fa fa-slack"></i> Bank Book
                                                </div>
                                            </div>
                                            <div class="portlet-body ">
                                                <div class="row" >
                                                    <div class="col-md-12">
                                                        <div class="form-group" >

                                                            <label class="col-md-3 control-label text-right" style="vertical-align: middle;padding-top: 6px;">Period</label>
                                                            <div class="col-md-7">
                                                                <div class="input-group date-picker input-daterange " id="datepicker-range" data-date-format="dd-mm-yyyy">
                                                                    <input type="text" class="form-control date_me" name="date_from" id="date_from" value="<?php echo date('d-m-Y');?>">
                                                            <span class="input-group-addon">
                                                            to </span>
                                                                    <input type="text" class="form-control date_me" name="date_to" id="date_to" value="<?php echo date('d-m-Y', strtotime('+30 days'));?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group"></div>
                                                </div>
                                                <div class="row" >
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label text-right" style="vertical-align: middle;padding-top: 6px;">Bank Account</label>
                                                            <div class="col-md-9">
                                                                <select name="f_bankaccount_id" id="f_bankaccount_id" class="form-control form-filter input-sm select2me ">
                                                                    <?php
                                                                    $qry_bank = $this->db->query('select distinct B.bankaccount_id, B.bankaccount_code, B.bankaccount_desc, I.bank_code, C.coa_code
                                                                                                from fn_bank_account B
                                                                                                left join fn_bank I on I.bank_id = B.bank_id
                                                                                                left join gl_coa C on C.coa_id = B.coa_id
                                                                                                where B.iscash <= 0');

                                                                    if($qry_bank->num_rows() > 0){
                                                                        foreach($qry_bank->result() as $row){
                                                                            echo '<option value="' . $row->bankaccount_id . '">' . '' . $row->bankaccount_code . ' | ' . $row->bankaccount_desc .  '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label text-right" style="vertical-align: middle;padding-top: 6px;">Status</label>
                                                            <div class="col-md-4">
                                                                <select name="f_status" id="f_status" class="form-control form-filter input-sm select2me ">
                                                                    <option value="1">POSTED</option>
                                                                    <option value="0">UNPOSTED</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">

                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="form-group" style="padding-left: 8px;">
                                                            <button type="button" class="btn green " name="save" id="btn-submit" data-url="<?php echo base_url('cashier/bankbook/pdf_bankbook/'); ?>">Generate</button>
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
        var bankAcctId = $('#f_bankaccount_id').val();
        var status = $('#f_status').val();

        if(bankAcctId != '' || status != ''){
            e.preventDefault();

            var url = $(this).attr('data-url');

            var dateStart = $('#date_from').val();
            var dateTo = $('#date_to').val();

            url += '/' + dateStart + '/' + dateTo + '/' + bankAcctId + '/' + status;

            window.open(encodeURI(url),'_blank');
        }

    });

</script>