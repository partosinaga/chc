<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE HEADER-->
			<h3 class="page-title">
			Dashboard <small>Watch List & News</small>
			</h3>
			<!-- div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="fa fa-home"></i>
						<a href="javascript:;">Home</a>

					</li>
					< li>
					    <i class="fa fa-angle-right"></i>
						<a href="javascript:;">Dashboard</a>
					</li>
                    < li>
                        <a href="javascript:;">DEPT</a>
                    </li >
				</ul>
			</div -->
			<!-- END PAGE HEADER-->
			<!-- BEGIN DASHBOARD STATS -->

			<!-- END DASHBOARD STATS -->
			<div class="clearfix">
			</div>
            <div class="row">
                <div class="col-md-6 col-sm-6 hide">
                    <!-- BEGIN CHART PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-bar-chart font-green-haze"></i>
                                <span class="caption-subject bold uppercase font-green-haze"> Occupancy</span>
                                <span class="caption-helper">By Month</span>
                            </div>
                            <div class="tools">
                                <span class="label-sm">Year&nbsp;</span>
                                <select name="period_year" class="select2me">
                                    <?php
                                    $y = date('Y'); //+1
                                    for($i=$y;$i>2014;$i--){
                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                    }
                                    ?>
                                </select>
                                <!-- a href="javascript:;" class="collapse">
                                </a>
                                <a href="#portlet-config" data-toggle="modal" class="config">
                                </a>
                                <a href="javascript:;" class="reload">
                                </a>
                                <a href="javascript:;" class="fullscreen">
                                </a>
                                <a href="javascript:;" class="remove">
                                </a -->
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div id="chart_1" class="chart">
                            </div>
                        </div>
                    </div>
                    <!-- END CHART PORTLET-->
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-bell-o"></i>Recent Activities
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="scroller" data-always-visible="1" data-rail-visible="0">
                                <ul class="feeds">
                                    <?php
                                    if(isset($activities)){
                                        foreach($activities as $act){
                                            ?>
                                            <li>
                                                <div class="col1">
                                                    <div class="cont">
                                                        <div class="cont-col1">
                                                            <div class="label label-sm label-info ">
                                                                <i class="<?php echo $act['icon']; ?>"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cont-col2">
                                                            <div class="desc ">
                                                                <?php echo $act['caption']; ?> <span class="label label-sm">
														<a href="<?php echo $act['redirect_href']; ?>" class="btn btn-xs btn-circle <?php echo $act['class_type']; ?>">Take action <i class="fa fa-share"></i></a>
														</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col2">
                                                    <div class="date">
                                                        <!-- Just now -->
                                                    </div>
                                                </div>
                                            </li>
                                        <?php
                                        }
                                    }
                                    ?>

                                </ul>
                            </div>
                            <div class="scroller-footer">
                                <!--
								<div class="btn-arrow-link pull-right">
									<a href="#">See All Records</a>
									<i class="icon-arrow-right"></i>
								</div>
								-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="row ">
				<div class="col-md-12 col-sm-12">

				</div>
			</div>
		</div>
	</div>
	<!-- END CONTENT -->
<script>
    jQuery(document).ready(function() {
        //amChart
        var initChart2 = function(data) {
            var chart = AmCharts.makeChart("chart_1", {
                "type": "serial",
                "theme": "light",
                //"pathToImages": Metronic.getGlobalPluginsPath() + "amcharts/amcharts/images/",
                "autoMargins": false,
                "marginLeft": 45,
                "marginRight": 5,
                "marginTop": 5,
                "marginBottom": 25,
                "minorGridAlpha": 0.05,
                "fontFamily": 'Open Sans',
                "color": '#44b6ae',//'#888',
                "dataProvider": data,
                "valueAxes": [{
                    "axisAlpha": 0,
                    "position": "left",
                    "title": "Unit Count"
                }],
                "startDuration": 1,
                "chartCursor": {
                    "cursorAlpha": 0.1,
                    "cursorColor": "#000000",
                    "fullWidth": true,
                    "valueBalloonsEnabled": false,
                    "zoomable": true
                },
                "graphs": [{
                    "alphaField": "alpha",
                    "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]] " + $('select[name="period_year"]').val() + " : <b>[[value]]</b> % </span>", //([[occupancy]] unit)
                    "dashLengthField": "dashLengthColumn",
                    "fillAlphas": 1,
                    "title": "Occupancy",
                    "type": "column",
                    //"columnWidth":0.65,
                    "labelPosition": "middle",
                    "labelText": "[[value]]",
                    //"fillColors": ["#99D658"], //"#99D658"
                    "fillColorsField": "color",
                    "valueField": "percentage", //"occupancy"
                    "lineColor": "#99D658"
                }],
                "categoryField": "month",
                "categoryAxis": {
                    "gridPosition": "start",
                    "axisAlpha": 0,
                    "tickLength": 0,
                    //"labelRotation" : 45,
                    "autoGridCount": false,
                    "axisColor": "#99D658",
                    "gridCount": 50
                }

            });

            chart.addListener("rendered", function (event) {
                setTimeout(function () {
                    // this code will get executed when animation is finished
                    //$('#chart_1').find('a[href*="www.amcharts.com"]').hide();
                    //$('#chart_1').find('a[href*="www.amcharts.com"]').remove();
                    removeAMChartLink();
                }, event.chart.startDuration * 1);
            });

            $('#chart_1').closest('.portlet').find('.fullscreen').click(function() {
                chart.invalidateSize();
                removeAMChartLink();
            });
        }

        var init_page = function() {
            var period_year = parseFloat($('select[name="period_year"]').val()) || 0;
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('frontdesk/report/ajax_unit_occupancy');?>",
                dataType: "json",
                data: { period_year: period_year},
                async:false
            })
                .done(function(result) {
                    initChart2(result);
                    removeAMChartLink();
                });
        }

        function removeAMChartLink(){
            $('#chart_1').find('a[href*="www.amcharts.com"]').hide();
            $('#chart_1').find('a[href*="www.amcharts.com"]').remove();
        }

        //init_page();

        $('select[name="period_year"]').on('change', function(){
            var period_year = parseFloat($(this).val()) || 0;
            if(period_year > 0){
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('frontdesk/report/ajax_unit_occupancy');?>",
                    dataType: "json",
                    data: { period_year: period_year},
                    async:false
                })
                    .done(function(result) {
                        initChart2(result);
                        removeAMChartLink();
                    });
            }
        });
    });

</script>