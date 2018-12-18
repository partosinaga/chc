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
        <div class="row" style="padding-top: 15px;">
            <div class="col-md-12 ">
                <form action="javascript:;" class="form-horizontal" id="validate-form" autocomplete="off">
                    <div class="form-body">
                        <!-- Begin: life time stats -->
                        <div class="form-group" >
                            <label class="control-label col-md-1"><strong>TYPE</strong></label>
                            <div class="col-md-2">
                                <select name="reserve_type" class="form-control select2me" >
                                    <option value="<?php echo RES_TYPE::PERSONAL?>" checked>PERSONAL</option>
                                    <option value="<?php echo RES_TYPE::CORPORATE?>">CORPORATE</option>
                                    <option value="<?php echo RES_TYPE::HOUSE_USE?>">HOUSE USE</option>
                                </select>
                            </div>
                            <label class="control-label col-md-1"><strong>AGENT</strong></label>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="agent_name" value="-- None --" readonly/>
                                    <span class="input-group-btn">
                                        <a id="btn_lookup_agent" class="btn btn-success" href="javascript:;" >
                                            <i class="fa fa-arrow-up fa-fw"></i>
                                        </a>
                                        <a id="btn_reset_agent" class="btn red-sunglo" href="javascript:;" >
                                            <i class="fa fa-times fa-fw"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="control-label col-md-1" ><strong>UNIT TYPE</strong></label>
                            <div class="col-md-2">
                                <select name="unit_type" class="form-control select2me" >
                                    <option value="">All</option>
                                    <?php
                                    $unit_types = $this->db->query('select * from ms_unit_type where status = ' . STATUS_NEW . ' order by unittype_code');
                                    if($unit_types->num_rows() > 0){
                                        foreach($unit_types->result_array() as $type){
                                            echo '<option value="' . $type['unittype_id'] . '" >' . $type['unittype_desc'] .  '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <label class="control-label col-md-1" ><strong>ADULT(S)</strong></label>
                            <div class="col-md-1" >
                                <!-- input type="text" class="form-control form-filter text-center" name="num_adult" id="num_adult" value="1" placeholder="#" -->
                                <select name="num_adult" class="form-control select2me" >
                                    <?php
                                    for($i=1;$i<=20;$i++){
                                        echo '<option value="' . $i . '" >' . $i .  '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <label class="control-label col-md-1" ><strong>KID(S)</strong></label>
                            <div class="col-md-1" >
                                <!-- input type="text" class="form-control form-filter text-center" name="num_child" id="num_child" value="0" placeholder="#" -->
                                <select name="num_child" class="form-control select2me" >
                                    <?php
                                    for($i=0;$i<=20;$i++){
                                        echo '<option value="' . $i . '" >' . $i .  '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <!--label class="control-label col-md-1" ><strong>Guest</strong></label>
                            <div class="col-md-1" >
								<input type="text" class="form-control form-filter text-center" name="guests" id="guests" value="1" placeholder="#" >
							</div-->
                        </div>
                        <div class="form-group" >
                            <label class="control-label col-md-1" ><strong>CHECK IN</strong></label>
                            <div class="col-md-2" >
                                <div class="input-group date date-picker ">
                                    <!-- i class="fa fa-calendar"></i -->
                                    <input type="text" name="arrival_date" data-date-viewmode="years" data-date-format="dd-mm-yyyy" value="<?php echo date('d-m-Y'); ?>" size="16" class="form-control date-picker text-center" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button" ><i class="fa fa-calendar" ></i></button>
                                    </span>
                                </div>
                            </div>
                            <label class="control-label col-md-1" ><strong>MONTH</strong></label>
                            <div class="col-md-1">
                                <select name="num_month" class="form-control select2me">
                                    <?php
                                    for($i=1;$i<=(5*12);$i++){
                                        echo '<option value="' . $i . '" >' . $i .  '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <label class="control-label col-md-1" style="padding-left: 0px;" ><strong>CHECK OUT</strong></label>
                            <div class="col-md-2">
                                <div class="input-group date " id="id_departure_date_group">
                                    <!-- i class="fa fa-calendar"></i -->
                                    <input type="text" name="departure_date" data-date-viewmode="years" data-date-format="dd-mm-yyyy" value="" size="16" class="form-control text-center" readonly>
                                    <span class="input-group-btn hide" id="id_departure_date_btn">
                                        <button class="btn default" type="button" ><i class="fa fa-calendar" ></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-2" >
                                <button type="button" class="btn green-seagreen" name="btnfind" id="btn-find" >SEARCH&nbsp;&nbsp;ROOM&nbsp;&nbsp;<i class="fa fa-check"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <form action="javascript:;" class="form-horizontal" id="form-entry" method="post">
        <div class="row">
            <!--?php
                //echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
                if($this->session->flashdata('flash_message') != ''){
                    echo '<div class="alert alert-danger alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
								<strong>Warning!</strong> ' . $this->session->flashdata('flash_message') . '
							</div>';
                }
            ?-->
            <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
            <hr style="padding-top: 0px;margin-top: 0px;" >
            <div class="col-md-3 hide" id="pnl_buttons" >
                <button type="button" class="btn purple-plum btn-circle" name="btnReservation" id="btn-reservation" data-url="<?php echo base_url('frontdesk/reservation/pre_reservation_form');?>"><i class="icon icon-note"></i> Reservation</button>
            </div>
        </div>
        <div class="row">
            <input type="hidden" name="reservation_type" id="reservation_type" value="">
            <input type="hidden" name="agent_id" id="agent_id" value="">
            <input type="hidden" name="arrival_date" id="arrival_date" value="">
            <input type="hidden" name="departure_date" id="departure_date" value="">
            <input type="hidden" name="n_month" id="n_month" value="">
            <input type="hidden" name="n_adult" id="n_adult" value="">
            <input type="hidden" name="n_child" id="n_child" value="">
            <div class="col-md-12" id="panel_result">
                <!-- RESULT -->
            </div>
        </div>
        </form>
    </div>
</div>
<!-- END CONTENT -->
<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<div id="calendar-modal" data-width="580" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1" >
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <!-- h4 class="modal-title" id="modal_total_days"></h4 -->
        </div>
        <div class="modal-body" style="padding-bottom: 5px;">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="portlet box green-meadow calendar" >
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-calendar"></i>
                                <span id="modal-unit-title" style="font-size: 15px;">Unit</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row" >
                                <div class="col-md-12 col-sm-12">
                                    <div id="calendar" >

                                    </div>
                                </div>
                            </div>
                            <hr style="margin-top: 5px;margin-bottom: 2px;">
                            <div class="row" style="margin-top: 3px;">
                                <div class="col-md-6">
                                    <div class="col-md-3 col-sm-3">
                                        <span class="label label-sm bg-green-haze small" style="font-size: 9px !important;"><strong>Selected</strong></span>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <span class="label label-sm bg-grey-cascade small" style="font-size: 9px !important;"><strong>Available</strong></span>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <span class="label label-sm bg-grey-gallery small" style="font-size: 9px !important;"><strong>Reserved</strong></span>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <span class="label label-sm bg-red-sunglo small" style="font-size: 9px !important;"><strong>Occupied</strong></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var rowIndex = 0;

    $(document).ready(function(){
        var initDate = function(){
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "right",
                format: 'dd-mm-yyyy',
                autoclose: true,
                startDate: "<?php echo date('d-m-Y');?>"
            });
        }

        var initLoad = function(){
            initDate();
            obtain_departure();
        }

        initLoad();

        var NowMoment = moment();
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

        var form1 = $('#validate-form');

        form1.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",  // validate all fields including form hidden input
            rules: {
                reserve_type:{
                    required:true
                },
                date_from:{
                    required:true
                },
                date_to: {
                    required: true
                }
            },
            messages: {
                reserve_type:"Reservation Type must be selected",
                date_from: "Start Date must be selected",
                date_to: "To date must be selected"
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                Metronic.scrollTo(form1, -200);

                if(validator.invalid.reserve_type != null){
                    toastr["error"](validator.invalid.reserve_type, "Warning");
                }
                if(validator.invalid.date_from != null){
                    toastr["error"](validator.invalid.date_from, "Warning");
                }
                if(validator.invalid.date_to != null){
                    toastr["error"](validator.invalid.date_to, "Warning");
                }
            },

            errorPlacement: function (error, element) { // render error placement for each input type
                var icon = $(element).parent('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");
                icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight

            },

            success: function (label, element) {
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                icon.removeClass("fa-warning").addClass("fa-check");
            }
        });

        ///LOOKUP AGENT
        var grid_agent = new Datatable();
        //COA
        var handleTableAgent = function (num_index) {
            // Start Datatable Item
            grid_agent.init({
                src: $("#datatable_agent"),
                onSuccess: function (grid_agent) {
                    // execute some code after table records loaded
                },
                onError: function (grid_agent) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_agent) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "sWidth" : '3%' },
                        { "bSortable": true},
                        { "bSortable": false},
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('frontdesk/reservation/get_modal_agent');?>/" + num_index // ajax source
                    }
                }
            });

            var tableWrapper = $("#datatable_agent_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('#btn_lookup_agent').on('click', function(){
            var $modal = $('#ajax-modal');
            var num_index = parseInt($(this).attr('data-index')) || 0;

            //console.log('looking up ...');
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            $('body').modalmanager('loading');

            setTimeout(function () {
                $modal.load('<?php echo base_url('frontdesk/reservation/ajax_modal_agent');?>.tpd', '', function () {
                    $modal.modal();
                    handleTableAgent(num_index);

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

        $('#btn_reset_agent').on('click', function(){
            $('input[name="agent_id"]').val('');
            $('input[name="agent_name"]').val('-- None --');
        })

        $('.btn-select-agent').live('click', function (e) {
            e.preventDefault();

            var agent_id = parseInt($(this).attr('data-id')) || 0;
            var name = $(this).attr('data-name');
            var pic = $(this).attr('data-pic');

            var agent_caption = name;
            if(pic.toLowerCase() != name.toLowerCase()){
                agent_caption = agent_caption + " - " + pic;
            }

            $('input[name="agent_id"]').val(agent_id);
            $('input[name="agent_name"]').val(agent_caption);

            $('#ajax-modal').modal('hide');
        });

        //Calendar
        var bindCalendar = function(startDate, endDate, rates) {
            if (!jQuery().fullCalendar) {
                return;
            }

            var h = {};

            if (Metronic.isRTL()) {
                $('#calendar').removeClass("mobile");
                h = {
                    right: 'title',
                    center: '',
                    left: 'month, prev,next'
                };
            } else {
                $('#calendar').removeClass("mobile");
                h = {
                    left: 'title',
                    center: '',
                    right: 'prev, next'
                };
            }

            $('#calendar').fullCalendar('destroy'); // destroy the calendar
            $('#calendar').fullCalendar({ //re-initialize the calendar
                header: h,
                defaultView: 'month', // change default view with available options from http://arshaw.com/fullcalendar/docs/views/Available_Views/
                defaultDate: startDate,
                //slotMinutes: 15,
                editable: false,
                droppable: false,
                events: rates,
                eventRender: function(event, element) {
                    element.find(".fc-event-title").remove();
                    element.find(".fc-event-time").remove();
                    if(event.type != null){
                        //Type 0=main, 1=prev/next 7 days, 2=booked, 3=checked in
                        var eventType = event.type;
                        //console.log('Type : ' + eventType);
                        var className = 'label bg-green-haze';
                        if(eventType == 1){
                            className = 'label bg-grey-cascade';
                        }else if(eventType == 2){
                            className = 'label bg-grey-gallery';
                        }else if(eventType == 3){
                            className = 'label bg-red-sunglo';
                        }

                        var new_description ='<span class="' + className + '" style="font-size: 9px !important;margin-top:10px;display:block;"><strong>' + event.title + '</strong></span>';

                        //element.append(new_description);
                        element.html(new_description);
                    }
                },
                viewRender: function(currentView){
                    var minDate = moment(startDate);
                    // Past
                    if (minDate >= currentView.start && minDate <= currentView.end) {
                        $(".fc-prev-button").prop('disabled', true);
                        $(".fc-prev-button").addClass('fc-state-disabled');
                    }
                    else {
                        $(".fc-prev-button").removeClass('fc-state-disabled');
                        $(".fc-prev-button").prop('disabled', false);
                    }

                    var maxDate = moment(endDate);
                    // Future
                    if (maxDate >= currentView.start && maxDate <= currentView.end) {
                        $(".fc-next-button").prop('disabled', true);
                        $(".fc-next-button").addClass('fc-state-disabled');
                    } else {
                        $(".fc-next-button").removeClass('fc-state-disabled');
                        $(".fc-next-button").prop('disabled', false);
                    }

                }
            });
        };

        $('#calendar-modal').on('shown.bs.modal', function () {
            $("#calendar").fullCalendar('render');
        });

        $('#calendar-modal').on('shown.bs.hide', function () {
            //$("#calendar").fullCalendar('destroy');
            //bindCalendar('2015-01-01',null);
            $("#calendar").fullCalendar('render');
        });

        $('.calendar-info').live('click', function (e) {
            //e.preventDefault();
            /*
            var unit_title = $(this).attr('unit-title');

            var unit_type = $(this).attr('unit-type');
            var reservation_type = $('select[name="reserve_type"]').val();
            //var isMember = $('input[name="tenant_type"]:checked').val();
            var dateStart = $('#date_from').val();
            var dateTo = $('#date_to').val();
            var unit_id = $(this).attr('unit-id');
			var guests = $('#guests').val();
            var agent_id = $('input[name="agent_id"]').val();

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('frontdesk/reservation/ajax_calendar_info');?>",
                dataType: "json",
                data: {unit_type: unit_type, date_from: dateStart, date_to:dateTo, reservation_type : reservation_type, unit_id :unit_id, agent_id : agent_id },
                async:false
            })
                .done(function(msg) {
                    var startDate = msg.startDate;
                    var endDate = msg.endDate;
                    var rates = msg.rates;
                    var totalDays = msg.totalDays;

                    bindCalendar(startDate,endDate, rates);

                    $('#modal-unit-title').html('Room #' + unit_title + ' / <strong>'+ totalDays + ' night(s)</strong>');

                    var $modal = $('#calendar-modal');

                    $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                        '<div class="loading-spinner" style="width: 200px; margin-left: 0px;">' +
                            '<div class="progress progress-striped active">' +
                            '<div class="progress-bar" style="width: 100%;"></div>' +
                            '</div>' +
                            '</div>';

                    $('body').modalmanager('loading');

                    $modal.modal();
                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if ($modal.hasClass('modal-overflow') === false) {
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});
                });
            */
        });

        $('select[name="reserve_type"]').live('change', function(e){
            e.preventDefault();

            var reserveType = $(this).val();
            if(reserveType == '<?php echo RES_TYPE::HOUSE_USE?>'){
                $('select[name="num_month"]').prop('disabled',true);
                //$('input[name="departure_date"]').prop('readonly',false);
                $('#id_departure_date_group').addClass('date-picker');
                $('#id_departure_date_btn').removeClass('hide');
                initDate();
            }else{
                $('select[name="num_month"]').prop('disabled',false);
                //$('input[name="departure_date"]').prop('readonly',true);
                $('#id_departure_date_group').removeClass('date-picker');
                $('#id_departure_date_btn').addClass('hide');
                obtain_departure();

            }

            $('#panel_result').html('');
            $('#pnl_buttons').addClass( "hide" );
        });

        $('select[name="num_month"]').live('change', function(e){
            e.preventDefault();
            obtain_departure();
            $('#panel_result').html('');
            $('#pnl_buttons').addClass( "hide" );
        });

        $('input[name="arrival_date"]').live('change', function(e){
            e.preventDefault();
            obtain_departure();
        });
    });

    var obtain_departure = function(){
        var dateStart = $('input[name="arrival_date"]').val();
        var num_month = $('select[name="num_month"]').val();

        $.ajax({
            type: "POST",
            url: "<?php echo base_url('frontdesk/reservation/ajax_get_departure');?>",
            dataType: "json",
            data: {arrival_date: dateStart, num_month: num_month}
        })
            .done(function( msg ) {
                if(msg.valid == '1') {
                    if (msg.departure_date != '') {
                        $('input[name="departure_date"]').val(msg.departure_date);
                    }
                }else{
                    $('input[name="departure_date"]').val('');
                }
            });
    };

    var validateFrontEnd = function(){
        var valid = true;

        var arrival_date = $('input[name="arrival_date"]').val();
        var end_date = $('input[name="departure_date"]').val();
        var arrivalDate = moment(arrival_date, 'DD-MM-YYYY');
        var departureDate = moment(end_date, 'DD-MM-YYYY');

        if(departureDate <=  arrivalDate){
            valid = false;
            toastr["warning"]("Arrival from must not be the same as departure date.", "Warning");
        }

        var reservation_type = parseInt($('select[name="reserve_type"]').val()) || 0;
        if(reservation_type == '<?php echo RES_TYPE::HOUSE_USE ?>') {
            var diffInDays = departureDate.diff(arrivalDate, 'days');
            if(diffInDays > 29){
                valid = false;
                toastr["warning"]("Departure for House Use must not exceed 30 days!", "Warning");
            }
        }

        return valid;
    };

    $('#btn-find').click(function(e) {
        e.preventDefault();

        //$('.alert').remove();

        findRoom();

    });

    var findRoom = function(){
        $('.alert').remove();

        if(validateFrontEnd()){
            var unitType = $('select[name="unit_type"]').val();
            var dateStart = $('input[name="arrival_date"]').val();
            var dateTo = $('input[name="departure_date"]').val();
            var num_month = $('select[name="num_month"]').val();
            var num_adult = $('select[name="num_adult"]').val();
            var num_child = $('select[name="num_child"]').val();

            if(dateStart != dateTo && dateTo != ''){
                //console.log('type = ' + unitType + ' from '+ dateStart + ' to ' + dateTo);
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('frontdesk/reservation/ajax_find_room');?>",
                    dataType: "json",
                    data: {unit_type: unitType, date_from: dateStart, date_to:dateTo , num_adult: num_adult , num_child: num_child}
                })
                    .done(function( msg ) {
                        if(msg.type == '0'){
                            $('#panel_result').html(msg.message);
                            $('#pnl_buttons').addClass( "hide" );
                        }else{
                            if(msg.message != ''){
                                $('#panel_result').html(msg.message);
                                Metronic.initIcheck();
                                $('.tooltips').tooltip();
                                $('#pnl_buttons').removeClass('hide');
                            }else{
                                $('#panel_result').html('');
                                $('#pnl_buttons').addClass( "hide" );
                            }
                        }
                    });
            }else{
                toastr["warning"]("Arrival from must not be the same as departure date.", "Warning");
            }
        }
    }

    $('#btn-reservation').click(function(e){
        e.preventDefault();

        if(validateFrontEnd()) {
            var selected = new Array();
            $('input:checkbox[name="chk_unit[]"]:checked').each(function () {
                //var val = $(this).closest("li").text();
                var val = $(this).val();
                selected.push(val);
                //console.log(val);
            });

            if (selected.length > 0) {
                if (selected.length == 1) {
                    var url = $(this).attr('data-url');
                    var reservation_type = parseInt($('select[name="reserve_type"]').val()) || 0;
                    var dateStart = $('input[name="arrival_date"]').val();
                    var dateTo = $('input[name="departure_date"]').val();
                    var num_month = $('select[name="num_month"]').val();
                    var num_adult = $('select[name="num_adult"]').val();
                    var num_child = $('select[name="num_child"]').val();

                    $('input[name="reservation_type"]').val(reservation_type);
                    $('#arrival_date').val(dateStart);
                    $('#departure_date').val(dateTo);
                    $('#n_month').val(num_month);
                    $('#n_adult').val(num_adult);
                    $('#n_child').val(num_child);

                    $('#form-entry').attr('action', url).submit();
                } else {
                    toastr["error"]("Only 1(one) room can be reserved.", "Warning");
                }
            } else {
                toastr["error"]("No room selected.", "Warning");
            }
        }
    })

</script>