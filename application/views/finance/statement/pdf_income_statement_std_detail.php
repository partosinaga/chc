<div class="page-content" style="margin-left:0px; min-height:700px;">
    <!-- BEGIN PAGE HEADER-->
    <div class="row hidden-print">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE-->
            <h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;"><?php echo $profile['company_name']; ?></h3>
            <h3 class="page-title text-center" style="font-size:25px;margin-bottom:5px;">INCOME STATEMENT BREAK DOWN</h3>
            <h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">Amounts in (IDR)</h3>
            <h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">From : <?php echo date("d/m/Y", mktime(0, 0, 0, 1, 1, $year));?> to <?php echo date("t/m/Y", strtotime($year . '-' . $month . '-01'));?></h3>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <!-- END PAGE HEADER-->
    <!-- BEGIN PAGE CONTENT-->
    <div class="invoice">
        <div class="row" style="font: 10pt normal Helvetica, Arial, sans-serif;">
            <div class="col-xs-12">
                <table class="table table-report table-striped table-hover" style="margin-top:0px;margin-bottom:15px;background:#ffffff;">
                    <?php
                    $qry = $this->db->query("SELECT * FROM fxnStatementPLBreakdown(" . $month . ", " . $year . ", '" . url_clean($ctg_name) . "', '" . url_clean($sub_ctg_name) . "', '" . url_clean($account_name) . "') ORDER BY AccountName, JournalDate");

                    $group_acc_name = array();

                    foreach ( $qry->result() as $row1 ) {
                        array_push($group_acc_name, $row1->AccountName);
                    }

                    $t_group_acc_name = array_unique($group_acc_name);

                    $tot_debit = 0;
                    $tot_credit = 0;

                    $i = 0;
                    foreach($t_group_acc_name as $acc_name){
                        echo '<thead><tr>
			<th colspan="6" class="text-left"><h3 style="font-size:17px;font-style:italic;"><strong>' . $acc_name . '</strong></h3></th>
		</tr></thead>';
                        $i++;
                        ?>			<thead>
                        <tr>
                            <th width="15%">Document No</th>
                            <th width="15%">Date</th>
                            <th width="40%" class="text-left">Reference</th>
                            <th width="15%" class="text-right">Debit</th>
                            <th width="1" class="text-right"></th>
                            <th width="15%" class="text-right">Credit</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ( $qry->result() as $row ) {
                            if($acc_name == $row->AccountName){
                                $date_ = new DateTime($row->JournalDate);
                                $date = $date_->format('d-m-Y');

                                echo '<tr>
									<td class="text-center">' . $row->JournalNo . '</td>
									<td class="text-center">' . $date . '</td>
									<td>' . $row->ReffNo . '</td>
									<td class="text-right">' . number_format($row->Debit, 0, ',', ',') . '</td>
									<td></td>
									<td class="text-right">' . number_format($row->Credit, 0, ',', ',') . '</td>
								</tr>';

                                $tot_debit = $tot_debit + $row->Debit;
                                $tot_credit = $tot_credit + $row->Credit;
                            }
                        }
                        ?>
                        </tbody>
                    <?php
                    }
                    ?>
                    <thead>
                    <tr>
                        <th class="total">&nbsp;</th>
                        <th class="total">&nbsp;</th>
                        <th class="text-right total"><strong>GRAND TOTAL</strong></th>
                        <th class="text-right total r-border r-background"><strong><?php echo number_format($tot_debit, 0, ',', ',');?></strong></th>
                        <th class="text-right total"></th>
                        <th class="text-right total r-border r-background"><strong><?php echo number_format($tot_credit, 0, ',', ',');?></strong></th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
</div>