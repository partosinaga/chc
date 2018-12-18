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
                            <!-- Begin: life time stats -->
                            <div class="portlet grey-mint box">
                                <div class="portlet-title" >
                                    <h4><strong>Accounting Period</strong></h4>
                                </div>
                                <div class="portlet-body">
                                    <!-- MASTER BANK LIST -->
                                    <div class="row form-horizontal">
                                        <form action="<?php echo base_url('finance/setup/submit_gl_period.tpd');?>" method="post" id="form-entry" class="form-horizontal">
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label class="control-label col-md-1" for="period_year">Month</label>
                                                <div class="col-md-2">
                                                    <select name="period_month" id="period_month" class="form-control select2me" >
                                                        <?php
                                                        /*
                                                        if(isset($list_year)){
                                                            foreach($list_year as $year){
                                                                echo '<option value="' . $year . '" >' . $year .  '</option>';
                                                            }
                                                        }*/
                                                        ?>
                                                    </select>
                                                </div>
                                                <label class="control-label col-md-1" for="period_year">Year</label>
                                                <div class="col-md-2">
                                                    <select name="period_year" id="period_year" class="form-control select2me" >
                                                        <?php
                                                        if(isset($list_year)){
                                                            for($i=0;$i<count($list_year);$i++){
                                                                if(isset($gl_period)){
                                                                    echo '<option value="' . $list_year[$i] . '" ' . ($gl_period->period_year == $list_year[$i] ? 'selected':'') . '>' . $list_year[$i] .  '</option>';
                                                                }else{
                                                                    if($i > 0){
                                                                        echo '<option value="' . $list_year[$i] . '" >' . $list_year[$i] .  '</option>';
                                                                    }else{
                                                                        echo '<option value="' . $list_year[$i] . '" selected>' . $list_year[$i] .  '</option>';
                                                                    }
                                                                }
                                                            }

                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 pull-right"  style="vertical-align: middle;">
                                                    <button type="button" class="btn btn-primary" name="apply" id="btn-apply" data-url="<?php echo base_url('finance/setup/gl_period/'); ?>">Apply</button>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                    <!-- END MASTER BANK LIST-->

                                </div>
                            </div>
                            <!-- End: life time stats -->
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

        var handleValidation = function() {
            var form1 = $('#form-entry');

            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    period_month: {
                        required: true
                    },
                    period_year: {
                        required: true
                    }
                },
                messages: {
                    period_month: "Period Month must not empty",
                    period_year: "Period Year must not empty"
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);

                    if(validator.invalid.period_month != null){
                        toastr["error"](validator.invalid.period_month, "Warning");
                    }
                    if(validator.invalid.period_year != null){
                        toastr["error"](validator.invalid.period_year, "Warning");
                    }

                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});

                    //console.log('text err ' + error.text());
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group
                },
                success: function (label, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    icon.removeClass("fa-warning").addClass("fa-check");
                }
            });
        }

        //initiate validation
        handleValidation();

        var initLoad = function(){
            var year = $('#period_year').val();
            changePeriodMonth(year);
        }

        initLoad();
    });

    $('#period_year').on("change", function() {
        console.log('period_year ' + $(this).val());
        var year = $(this).val();
        changePeriodMonth(year);
    });

    function changePeriodMonth(year){
        if(year != ''){
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('finance/setup/ajax_period_month');?>",
                //dataType: "json",
                async: false,
                data: { period_year: year }
            })
                .done(function( msg ) {
                    //Metronic.unblockUI();

                    //console.log('** ' + msg);
                    $('#period_month').html(msg);
                    $('select.select2me').select2();
                });
        }
    }

    $('#btn-apply').on("click", function(){
        bootbox.confirm({
            message: "Change Period ?",
            buttons: {
                cancel: {
                    label: "No",
                    className: "btn-inverse"
                },
                confirm:{
                    label: "Yes",
                    className: "btn-primary"
                }
            },
            callback: function(result) {
                if(result === false){
                    //console.log('Empty reason');
                }else{
                    //$('#form-entry').submit();
                    $("form:first").submit();

                }
            }
        });
    });

</script>