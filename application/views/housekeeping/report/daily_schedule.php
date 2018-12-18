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
            <!--div class="page-toolbar">
                <div class="btn-group pull-right">
                    <a data-close-others="true" data-hover="dropdown" data-toggle="dropdown" class="btn green-haze btn-sm dropdown-toggle" href="">
                        <i class="fa fa-print">&nbsp;&nbsp;
									</i> Report &nbsp;&nbsp;<span class="fa fa-angle-down">
									</span>
                    </a>
                </div>
            </div-->
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row table-responsive">
            <div class="col-md-12">
                <div class="portlet ">
                    <form id="form-search" onsubmit="return false;" style="display: inline-block; margin-bottom: 10px; margin-top: 15px;">
                        <div class="input-group date-picker input-daterange pull-left" id="datepicker-range" data-date-format="dd-mm-yyyy">
                            <input type="text" class="form-control form-filter-wrapper input-sm" name="filter_date_from" id="start_date" value="<?php echo $start_date;?>" readonly>
                                <span class="input-group-addon input-sm">to</span>
                            <input type="text" class="form-control form-filter-wrapper input-sm" name="filter_date_to" id="end_date" value="<?php echo $end_date;?>" readonly>
                        </div>
                        <input type="submit" class="btn btn-sm green yellow-stripe pull-left" value="Submit" style="margin-left: 10px;" />
                    </form>
                    <table class="table-hsk">
                        <tr>
                            <th>Unit</th>
                            <?php
                                $begin = new DateTime( dmy_to_ymd($start_date) );
                                $end = new DateTime( date('Y-m-d', strtotime(dmy_to_ymd($end_date) . '+1 days') ) );

                                $interval = DateInterval::createFromDateString('1 day');
                                $period = new DatePeriod($begin, $interval, $end);

                                foreach ( $period as $dt ){
                                    echo '<th>' . $dt->format( "D, d M Y" ) . '</th>';
                                }
                            ?>
                        </tr>
                        <?php
                        $qry_unit = $this->db->get_where('ms_unit', array('status' => STATUS_NEW));
                        foreach ($qry_unit->result() as $row_unit) {
                            $line = '<tr><td>' . $row_unit->unit_code . '</td>';

                            $sql_hsk = "SELECT A.unit_id, CONVERT (DATE, A.checkin_date) AS checkin_date, ISNULL(A.hsk, 0) AS hsk_status FROM cs_reservation_detail A LEFT JOIN cs_reservation_header B ON A.reservation_id = B.reservation_id WHERE A.unit_id = " . $row_unit->unit_id . " AND B.status IN (" . ORDER_STATUS::CHECKIN . ", " . ORDER_STATUS::CHECKOUT . ") AND ( CONVERT (DATE, A.checkin_date) BETWEEN '" . dmy_to_ymd($start_date) . "' AND '" . dmy_to_ymd($end_date) . "' )";

                            $qry_hsk = $this->db->query($sql_hsk);
                            if ($qry_hsk->num_rows() > 0) {
                                $begin_loop = $begin;

                                foreach ($qry_hsk->result() as $row_hsk) {
                                    $period_loop = new DatePeriod($begin_loop, $interval, $end);

                                    foreach ( $period_loop as $dt_loop ){
                                        if ($dt_loop->format( "Y-m-d" ) == $row_hsk->checkin_date) {
                                            if ($row_hsk->hsk_status == 1) {
                                                $line .= '<td class="fb">&#x2714;</td>';
                                            } else {
                                                $line .= '<td class="fb">&nbsp;</td>';
                                            }
                                            $begin_loop = date('Y-m-d', strtotime($dt_loop->format( "Y-m-d" ) . '+1 days'));
                                            $begin_loop = new DateTime( $begin_loop );
                                            break;
                                        } else {
                                            $line .= '<td class="fb">&nbsp;</td>';
                                            $begin_loop = date('Y-m-d', strtotime($dt_loop->format( "Y-m-d" ) . '+1 days'));
                                            $begin_loop = new DateTime( $begin_loop );
                                        }
                                    }
                                }
                            } else {
                                foreach ( $period as $dt ){
                                    $line .= '<td class="fb">&nbsp;</td>';
                                }
                            }

                            $line .= '</tr>';

                            echo $line;
                        }
                        ?>
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

        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "right",
                autoclose: true,
                todayHighlight: true,
                todayBtn: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        $('#form-search').on('submit', function(){
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            var start_ = start_date.split("-").reverse().join("-");
            var end_ = end_date.split("-").reverse().join("-");

            var start = new Date(start_);
            var end = new Date(end_);

            var diff = new Date(end - start);

            // get days
            var days = diff/1000/60/60/24;

            if (days > 7 || days < 0) {
                toastr["error"]("Search range must not exceed 7 days.", "Error");
            } else {
                window.location.href = "<?php echo base_url('housekeeping/report/daily_schedule');?>/" + start_date + '/' + end_date;
            }
        });
    });

</script>