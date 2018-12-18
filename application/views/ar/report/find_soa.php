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
                                                    <i class="fa fa-slack"></i> Statement Of Account
                                                </div>
                                            </div>
                                            <div class="portlet-body ">
                                                <div class="row" >
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label text-right" style="vertical-align: middle;padding-top: 6px;">Reservation #</label>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="hidden" name="reservation_id" value="">
                                                                <input type="text" class="form-control" name="reservation_code" value="" readonly />
                                                                 <span class="input-group-btn">
                                                                   <a id="btn_lookup_rsvt" class="btn btn-success" href="javascript:;" >
                                                                       <i class="fa fa-arrow-up fa-fw"></i>
                                                                   </a>
                                                                 </span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="form-group">&nbsp;</div>
                                                    <div class="form-group" >
                                                        <label class="col-md-3 control-label text-right" style="vertical-align: middle;padding-top: 6px;">Guest</label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="tenant_fullname" value="" class="form-control" readonly>
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

                                                            <button type="button" class="btn green " name="save" id="btn-submit" data-url="<?php echo base_url('ar/report/pdf_soa/'); ?>">Generate</button>

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

        //Reservation
        var grid_rsvt = new Datatable();

        var handleTableRsvt = function (num_index, reservation_id) {
            // Start Datatable Item
            grid_rsvt.init({
                src: $("#datatable_reservation"),
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center text-middle", "bSortable": true, "sWidth" : '20%' },
                        { "sClass": "text-left text-middle" },
                        { "bSortable": false, "sClass": "text-center text-middle", "sWidth" : '11%' }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, -1],
                        [10, 20, 50, 100, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('general/modalbook/get_modal_reservation');?>/" + num_index + "/" + reservation_id // ajax source
                    },
                    "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {

                    }

                }
            });

            var tableWrapper = $("#datatable_reservation_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('#btn_lookup_rsvt').on('click', function(){
            var $modal = $('#ajax-modal');
            var num_index = parseInt($(this).attr('data-index')) || 0;

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('general/modalbook/ajax_reservation');?>.tpd', '', function () {
                    $modal.modal();

                    var rsvt_id = parseFloat($('input[name="reservation_id"]').val()) || 0;

                    handleTableRsvt(num_index, rsvt_id);

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if ($modal.hasClass('modal-overflow') === false) {
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});
                });
            }, 150);
        });

        $('.btn-select-reservation').live('click', function (e) {
            e.preventDefault();

            var reservation_id = parseInt($(this).attr('data-reservation-id')) || 0;
            var reservation_code = $(this).attr('data-reservation-code');
            var tenant_name = $(this).attr('data-tenant-name');

            $('input[name="reservation_id"]').val(reservation_id);
            $('input[name="reservation_code"]').val(reservation_code);
            $('input[name="tenant_fullname"]').val(tenant_name);

            $('#ajax-modal').modal('hide');
        });

        $('#btn-submit').on('click',function(e) {
             var reservationId = $('input[name="reservation_id"]').val();

             if(reservationId > 0){
                 e.preventDefault();

                 var url = $(this).attr('data-url');
                 url += '/' + reservationId ;

                 window.open(encodeURI(url),'_blank');

                 //window.location = encodeURI(url);
                 //window.location.reload();
             }
        });
    });




</script>