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
		<div class="row">
			<div class="col-md-12">
				<div class="portlet box <?php echo BOX;?>">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-user"></i>Booking Form
						</div>
						<div class="actions">

						</div>
					</div>
					<div class="portlet-body form">
                        <div class="row">
                            <div class="col-md-12">
                                <img id="my_image" class="img-responsive" src="<?php echo base_url('assets/img/floor_plan/lt-2.jpg');?>" usemap="#Map">

                                <map name="Map" id="Map">
                                    <area class="my_tooltip" data-tooltip="syaiful isnaini" data-key="AZ" alt="" title="" href="#" shape="poly" coords="53,55,1163,57,1165,595,441,596,437,663,54,662" />
                                </map>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal" data-width="860" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    $('#my_image')
        .mapster({
            mapKey: 'data-key'
        })
        .mapster('set',true,'AZ');

    $('.my_tooltip').qtip({
        content: {
            text: 'My common piece of text here'
        },
        style: {
            classes: 'qtip-bootstrap'
        },
        position: {
            my: 'top center',
            at: 'bottom center',
            target: 'mouse',
            adjust: { x: 15, y: 20 }
        }
    })
</script>