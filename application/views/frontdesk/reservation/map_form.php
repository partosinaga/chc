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
                $form_mode = '';
                ?>
			</ul>
		</div>
		<!-- END PAGE HEADER-->
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
				<div class="portlet box <?php echo BOX;?>">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-user"></i> Check-In
						</div>
					</div>
					<div class="portlet-body form">
                        <img id="lt_1" src="<?php echo base_url() . "assets/img/floor_plan/FL0.jpg"?>" usemap="#floor_1" class="img-responsive">
                        <map id="id_floor_1" name="floor_1">
                            <area coords="143,63,263,63,263,124,294,124,294,177,143,177" shape="poly"  data-key="A1" data-full="Room A1"  href="#" />
                            <area coords="295,115,263,115,263,63,434,63,434,177,294,177" shape="poly"  data-key="A2" data-full="Room A2"  href="#" />
                            <area coords="436,63,573,177" shape="rect"  data-key="A3" data-full="Room A3"  href="#" />
                            <area coords="606,63,819,177" shape="rect"  data-key="B1" data-full="Room B1"  href="#" />

                            <!--area coords="64,324,408,324,408,364,437,403,435,656,64,656" shape="poly"  data-key="A2" data-full="Room A2"  href="#" />
                            <area coords="66,1006,435,1257" shape="rect" data-key="A3" data-full="Room A3" href="#">
                            <area coords="954,714,926,713,930,611,1247,610,1249,714,1146,715,1144,851,953,851" shape="poly" data-key="B2" data-full="Room B2" href="#" -->
                        </map>
					</div>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-md-12">
                <input type="button" name="btn_submit" id="btn_submit" value="Submit">
            </div>
        </div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
<!-- END CONTENT -->

<script type="text/javascript" language="JavaScript">
    var isedit = <?php echo ($form_mode == '' ? 0 : 1); ?>;
    var rowIndex = <?php echo (isset($rowIndex) ? $rowIndex : 0) ; ?>;

    if(isedit > 0){
        $('#form-entry').block({
            message: null ,
            overlayCSS: {backgroundColor: '#EDF5EB', opacity:0,cursor:'default'}
        });
    }

    $(document).ready(function(){
        var image = $('#lt_1');
        image.mapster({
            fillOpacity: 0.75,
            render_highlight: {
                fillColor: '2aff00',
                stroke: true
                //altImage: 'examples/images/usa_map_720_alt_4.jpg'
            },
            render_select: {
                fillColor: '474747',
                stroke: false
                //altImage: '../../assets/img/tpd_small.png'
            },
            fadeInterval: 50,
            mapKey: 'data-key',
            mapValue: 'data-full',
            areas: [
                {
                    key: 'A1',
                    isSelectable: true
                    //includeKeys: 'B2'
                },
                {
                    key: 'A2',
                    isSelectable: true
                },
                {
                    key: 'A3',
                    isSelectable: true
                }
            ],
            onClick: function (e) {
                console.log(e.key + ' -> ' + e.selected);
                if (e.key==='B2') {
                    //image.mapster('zoom','B2');
                    return;
                }
            }
        });

        $('#lt_1').mapster('set',true,'A1,A2');
    });

    $('#btn_submit').click(function(e) {
        e.preventDefault();

        //$.uniform.restore();
        var vals = $('#lt_1').mapster('get','data-key');
        console.log(vals);

        $('#lt_1').mapster('set_options',{
            areas: [{key: 'A2',isSelectable: false,isMask:true}]
        });

    });

</script>