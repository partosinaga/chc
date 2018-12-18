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
			<div class="col-md-12" >
				<div class="portlet">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-users"></i><?php echo (isset($report_title) ? $report_title : 'Sales Detail'); ?>
						</div>
					</div>
					<div class="portlet-body table-responsive">
						<div class="table-container " >
                            <div class="col-md-12" style="padding-bottom: 20px; ">
                                <table class="table table-striped table-bordered table-hover report" id="datatable_ajax" >
                                    <thead>
                                        <tr class="heading">
                                            <th class="text-center" width="30px"> # </th>
                                            <th class="text-center" width="130px"> RESERVATION </th>
                                            <th class="text-left" > GUEST NAME </th>
                                            <th class="text-center" width="100px"> BILL NO </th>
                                            <th class="text-center" width="100px"> ROOM </th>
                                            <th class="text-right" width="150px"> AMOUNT </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if(isset($qry)){
                                        $total = 0;
                                        $i = 1;
                                        $bill_date = '';
                                        foreach($qry->result_array() as $row){
                                            if($bill_date != dmy_from_db($row['bill_date'])){
                                                $bill_date = dmy_from_db($row['bill_date']);

                                                echo '<tr>
                                                        <td colspan="6" class="bg-group1">Per - <strong>' . $bill_date . '</strong></td>
                                                      </tr>';
                                                $i = 1;
                                            }

                                            echo '<tr style="font-size:12px;">
                                                    <td class="text-center">
                                                      ' . $i . '.
                                                    </td>
                                                    <td class="text-center">
                                                      ' . $row['reservation_code'] . '
                                                    </td>
                                                    <td class="text-left">
                                                      ' . $row['tenant_fullname'] . '
                                                    </td>
                                                    <td class="text-center" >
                                                      ' . $row['journal_no'] . '
                                                    </td>
                                                    <td class="text-center" >
                                                      ' . $row['room'] . '
                                                    </td>
                                                    <td class="text-right mask_currency" style="padding-right:10px;">
                                                      ' . $row['amount'] . '
                                                    </td>
                                                  </tr>';
                                            $i++;

                                            $total += $row['amount'];
                                        }
                                    }
                                    ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="heading">
                                            <th class="text-right" colspan="5">TOTAL</th>
                                            <th class="text-right mask_currency" style="padding-right:10px;"><?php echo $total; ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
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

<script>
	$(document).ready(function(){
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

        function init_page(){
            $(".mask_currency").inputmask("decimal", {
                radixPoint: ".",
                groupSeparator: ",",
                digits: 2,
                autoGroup: true,
                autoUnmask: true
            });
        }

        init_page();


	});

</script>