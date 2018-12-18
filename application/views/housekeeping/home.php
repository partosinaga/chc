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
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <a data-close-others="true" data-hover="dropdown" data-toggle="dropdown" class="btn green-haze btn-sm dropdown-toggle" href="">
                        <i class="fa fa-print">&nbsp;&nbsp;
									</i> Report &nbsp;&nbsp;<span class="fa fa-angle-down">
									</span>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?php echo base_url('housekeeping/report/room_assignment.tpd')?>" target="_blank">
                                <strong>Room Assignment</strong>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('housekeeping/report/find_room_status.tpd')?>">
                                <strong>Room Status</strong>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row table-responsive">
            <div class="col-md-12">
                <div class="portlet ">
                    <table class="table-hsk">
                        <?php
                        $output = '';

                        $qry_floor = $this->mdl_general->get('ms_unit_floor', array('status' => STATUS_NEW), array(), 'floor_id DESC');
                        $tot_unit = 14;

                        foreach ($qry_floor->result() as $row_floor) {
                            $n = 1;
                            $output .= '<tr>';
                            //$qry_unit = $this->mdl_general->get('ms_unit', array('floor_id' => $row_floor->floor_id), array(), 'unit_code ASC');
                            $qry_unit = $this->mdl_finance->getJoin('ms_unit.*, ms_unit_type.unittype_bedroom', 'ms_unit', array('ms_unit_type' => 'ms_unit.unittype_id = ms_unit_type.unittype_id'), array('floor_id' => $row_floor->floor_id), array(), 'unit_code ASC');
                            $i = 0;
                            foreach ($qry_unit->result() as $row_unit) {
                                if ($i == 0) {
                                    $output .= '<td style="width: 3px;"><strong>' . $row_floor->floor_name . '</strong></td>';
                                }

                                $output .= '<td id="unit_' . $row_unit->unit_id . '"><button type="button" class="btn ' . HSK_STATUS::hsk_class($row_unit->hsk_status) . ' btn-change-status" data-id="' . $row_unit->unit_id . '" data-code="' . $row_unit->hsk_status . '" status-next="' . HSK_STATUS::next_status($row_unit->hsk_status) . '" ' . (HSK_STATUS::next_status($row_unit->hsk_status) == '' ? ($row_unit->hsk_status == HSK_STATUS::OO ? '' : ' disabled') : '') . '><span class="small">' . $row_unit->unit_code . '</span><br/><span class="large">' . $row_unit->hsk_status . '</span><br/><span class="small">' . $row_unit->unittype_bedroom . '</span></button></td>';

                                $i++;
                                $n++;
                            }
                            while ($n <= $tot_unit) {
                                $output .= '<td>&nbsp;</td>';
                                $n++;
                            }
                            $output .= '</tr>';
                        }

                        echo $output;
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="row table-responsive">
            <div class="col-md-12">
                <div class="tab-content">
                    <span class="bold">Housekeeping status :</span>
                    <table >
                        <tr>
                            <td width="25%">[IS]&nbsp;<i>Inspected </i></td>
                            <td width="25%">[ISC]&nbsp;<i>Inspected - Check In</i></td>
                            <td width="25%">[OD]&nbsp;<i >Occupied Dirty</i></td>
                            <td width="25%">[OC]&nbsp;<i >Occupied Clean</i></td>
                        </tr>
                        <tr>
                            <td>[VD]&nbsp;<i >Vacant Dirty</i></td>
                            <td>[VC]&nbsp;<i >Vacant Clean</i></td>
                            <td>[ED]&nbsp;<i >Expected Departure</i></td>
                            <td>[ED/EA]&nbsp;<i >Expected Departure/Expected Arrival</i></td>
                        </tr>
                        <tr>
                            <td>[VD/EA]&nbsp;<i >Vacant Dirty/Expected Arrival</i></td>
                            <td>[VC/EA]&nbsp;<i >Vacant Clean/Expected Arrival</i></td>
                            <td>[IS/EA]&nbsp;<i >Inspected/Expected Arrival</i></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><span class="font-red-sunglo">[OS]&nbsp;<i>Out Of Service</i></span></td>
                            <td><span class="font-red-sunglo">[OO]&nbsp;<i>Out Of Order</i></span></td>
                            <td>&nbsp;</td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal" class="modal fade" data-replace="true" data-width="500" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    jQuery(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "10000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        var $modal = $('#ajax-modal');

        $('.btn-change-status').live('click', function (e) {
            e.preventDefault();

            var data_id = $(this).attr('data-id');
            var status_next = $(this).attr('status-next');
            var hsk_status = $(this).attr('data-code');

            //console.log(hsk_status);

            if(status_next.toString().trim() != ''){
                $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                    '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

                $('body').modalmanager('loading');

                setTimeout(function(){
                    $modal.load('<?php echo base_url('housekeeping/housekeeping/xmodal_change_status');?>/' + data_id, '', function(){

                        $modal.modal();

                        $.fn.modalmanager.defaults.resize = true;

                        if ($modal.hasClass('bootbox') == false) {
                            $modal.addClass('modal-fix');
                        }

                        if ($modal.hasClass('modal-overflow') == false) {
                            $modal.addClass('modal-overflow');
                        }

                        $modal.css({'margin-top' : '0px'});

                        $('.select2me').select2({
                            placeholder: "Select",
                            allowClear: true
                        });

                    });
                }, 100);
            } else {
                if (hsk_status == '<?php echo HSK_STATUS::OO;?>') {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('housekeeping/housekeeping/link_sfr');?>",
                        dataType: "json",
                        data: { unit_id: data_id}
                    })
                    .done(function( msg ) {
                        console.log(msg.debug);
                        if (msg.link != '') {
                            var url = msg.link;
                            window.location.assign(url);
                        }
                    });
                }
            }

        });

        $('#btn_hsk_change').live('click', function(){
            var unit_id = parseInt($('input[name="unit_id"]').val()) || 0;
            var unit_code = $('input[name="unit_code"]').val();
            var current_status = $('input[name="hsk_status"]').val();
            var next_status = $('select[name="next_hsk_status"]').val();
            var hsk_remark = $('textarea[name="hsk_remark"]').val();

            $modal.modal('hide');

            bootbox.confirm({
                message: "Confirm room " + unit_code + " status from " + current_status + " to " + next_status + "?",
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
                    if(result === null){

                    }else{
                        if(result){

                            $.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('housekeeping/housekeeping/xchange_hsk_status');?>",
                                    dataType: "json",
                                    data: { unit_id: unit_id, hsk_status : next_status, remark : hsk_remark}
                                })
                                .done(function( msg ) {
                                    //Metronic.unblockUI();
                                    if(msg.valid == '1'){
                                        if(msg.button != '') {
                                            $('#unit_' + unit_id).html(msg.button);
                                            toastr["success"](msg.message, "Info");
                                        }else{
                                            var url = "<?php echo base_url('housekeeping/housekeeping/home.tpd');?>" ;
                                            window.location.assign(url);
                                        }
                                    }else if(msg.valid == '0'){
                                        toastr["error"](msg.message, "Error");
                                    }
                                    else {
                                        toastr["error"]("Action can not be processed, please try again later.", "Error");
                                    }
                                });
                        }
                    }
                }
            });
        });
    });

</script>