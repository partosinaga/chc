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
                                                <input type="text" class="form-control date_me" name="date_from" id="date_from" value="<?php echo date('d-m-Y', strtotime('-30 days'));?>">
                                                            <span class="input-group-addon">
                                                            to </span>
                                                <input type="text" class="form-control date_me" name="date_to" id="date_to" value="<?php echo date('d-m-Y');?>">

                                            </div>
                                        </div>
                                        <div class="col-md-2"  style="vertical-align: middle;">
                                            <button type="button" class="btn green" name="save" id="btn-submit" data-url="<?php echo base_url('finance/ledger/pdf_ledger/'); ?>">Generate</button>
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
                                            <div class="row margin-bottom-5" style="margin-top: 5px;">
                                                <div class="col-md-12 ">
                                                    <a href="javascript:;" class="btn  blue-ebonyclay add_coa" style="float: left;">
                                                        <i class="fa fa-search"></i>
                                                    </a>
                                                    <a href="javascript:;" class="btn blue-ebonyclay add_all_coa" style="float: left;margin-left: 5px;">
                                                        <i class="fa fa-asterisk"></i>&nbsp;ALL
                                                    </a>
                                                    <div class="input-group margin-bottom-4" style="float: left;margin-left: 10px;">
                                                        <input type="text" class="form-control input-small" style="width: 100px !important;" name="filter_range_from" id="filter_range_from" placeholder="COA from" value="">
                                                        <span class="control-label " style="float: left;">&nbsp;-&nbsp;</span>
                                                        <input type="text" class="form-control input-small" style="width: 100px !important;" name="filter_range_to" id="filter_range_to" placeholder="COA to" value="">
                                                    </div>
                                                    <a href="javascript:;" class="btn  blue-ebonyclay add_range_coa" style="float: left;margin-left: 2px;">
                                                        <i class="fa fa-search"></i>&nbsp;Range
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="portlet-body ">
                                            <div class="table-container">
                                                <table class="table table-striped table-bordered table-hover table-po-detail" id="dt_ledger_coa">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th style="width: 15%;" class="text-center">
                                                            CODE
                                                        </th>
                                                        <th>
                                                            DESCRIPTION
                                                        </th>
                                                        <th style="width:5%;">

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
                            <!-- END MASTER BANK LIST-->

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

        //COA
        var handleTableCOA = function () {
            // Start Datatable Item
            grid_trx.init({
                src: $("#datatable_coa"),
                onSuccess: function (grid_coa) {
                    // execute some code after table records loaded
                },
                onError: function (grid_coa) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_coa) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "bSortable": true, "sWidth" : '15%' },
                        null,
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center", "sWidth" : '6%' },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('general/modalbook/get_coa_list_by_code');?>"
                    }
                }
            });

            var tableWrapper = $("#datatable_coa_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item

        }

        $('.add_coa').live('click', function (e) {
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: 150px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            var uninitialized = $('#datatable_coa').filter(function() {
                return !$.fn.DataTable.fnIsDataTable(this);
            });

            grid_trx.resetFilter();
            setTimeout(function(){
                $modal.load('<?php echo base_url('general/modalbook/ajax_coa_by_id');?>.tpd', '', function(){
                    $modal.modal();
                    handleTableCOA();

                    //$.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if($modal.hasClass('modal-overflow') === false){
                        $modal.addClass('modal-overflow');
                    }

                    if($modal.hasClass('modal-scrollable') === true){
                        $modal.removeClass('modal-scrollable');
                    }

                    $modal.css({'margin-top': '0px'});
                });
            }, 150);
        });

        $('.btn-select-coa').live('click', function (e) {
            e.preventDefault();

            var coa_id = parseInt($(this).attr('coa-id')) || 0;
            var coa_code = $(this).attr('coa-code');
            var coa_desc = $(this).attr('coa-desc');

            if(coa_id > 0) {
                //Add to temp
                var newRowContent = "<tr>" +
                    "<td class=\"text-center\" style=\"vertical-align:middle;\"><input type=\"hidden\" name=\"detail_id[" + rowIndex + "]\" value=\"0\"><input type=\"hidden\" name=\"coa_id[" + rowIndex + "]\" value=\"" + coa_code + "\">" + coa_code + "</td>" +
                    "<td style=\"vertical-align:middle;\"><span class=\"control-label\">" + coa_desc + "</span></td>" +
                    "<td class=\"text-center\" style=\"vertical-align:middle;\"><a class=\"btn btn-danger btn-xs tooltips\" data-original-title=\"Remove\" href=\"javascript:;\" onclick=\"delete_frontend(" + rowIndex + ");\" ><i class=\"fa fa-times\"></i></a></td>" +
                    "</tr>";

                //$('#datatable_detail tbody').prepend(newRowContent);
                $('#dt_ledger_coa tbody').append(newRowContent);

                $('select.select2me').select2();

                rowIndex++;

                $('#ajax-modal').modal('hide');
            }else{
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No COA selected',
                    container: grid_trx.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

    });

    function delete_frontend(rowIndex){
        $('#dt_ledger_coa > tbody > tr').find('input[name="detail_id[' + rowIndex + ']"]').parent().parent().remove();
    };

    $('.add_all_coa').click(function(e) {
        var iCount = $('#dt_ledger_coa > tbody > tr').length;
        if(iCount > 0){
            //Remove all coa
            $('#dt_ledger_coa > tbody').html('');
        }else{

            //Add all coa
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('finance/ledger/all_coa_tr');?>",
                dataType: "json"
            })
            .done(function( msg ) {
                    $('#dt_ledger_coa > tbody').html(msg);
            });
        }
    });

    $('.add_range_coa').click(function(e) {
        $('#dt_ledger_coa > tbody').html('');

        var rangeStart = $('#filter_range_from').val();
        var rangeTo = $('#filter_range_to').val();

        if(rangeStart != '' && rangeTo != ''){
            //Add all coa
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('finance/ledger/coa_tr_by_range');?>",
                dataType: "json",
                data: { filter_range_from: rangeStart,filter_range_to:rangeTo}
            })
                .done(function( msg ) {
                    $('#dt_ledger_coa > tbody').html(msg);
                });
        }else{
            toastr["error"]("COA range must not empty !", "Warning");
        }
    });

    $('#btn-submit').click(function(e) {
        var iCount = $('#dt_ledger_coa > tbody > tr').length;
        if(iCount > 0){
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
            var url = '<?php echo base_url('finance/ledger/pdf_ledger.tpd');?>';
            var params = '<input type="hidden" name="date_start" value="' + dateStart + '">' +
                         '<input type="hidden" name="date_to" value="' + dateTo + '">' +
                         '<input type="hidden" name="coa_code_list" value="' + coa_code_list + '">';
            $("#form-entry").append(params);
            $("#form-entry").attr("method", "post");
            $("#form-entry").attr("target", "_blank");
            $('#form-entry').attr('action', url).submit();

        }else{
            toastr["error"]("COA must be selected !", "Warning");
        }
    });

</script>