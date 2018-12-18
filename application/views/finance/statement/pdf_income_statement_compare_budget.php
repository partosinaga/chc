<div class="page-content" style="margin-left:0px; min-height:700px;">
	<!-- BEGIN PAGE HEADER-->
	<div class="row hidden-print">
		<div class="col-md-12">
			<!-- BEGIN PAGE TITLE-->
			<h3 class="page-title text-center" style="font-size:13px;margin-bottom:5px;"><?php echo $profile['company_name']; ?></h3>
			<h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">INCOME STATEMENT</h3>
			<h3 class="page-title text-center" style="font-size:13px;margin-bottom:5px;">Amounts in (IDR)</h3>
			<h3 class="page-title text-center" style="font-size:13px;margin-bottom:5px;">As Of <?php echo date("F Y", mktime(0, 0, 0, $month, 1, $year));?></h3>
			<!-- END PAGE TITLE & BREADCRUMB-->
		</div>
	</div>
	<!-- END PAGE HEADER-->
	<!-- BEGIN PAGE CONTENT-->
	<div class="table_small">
		<div class="row">
        <table class="table table-report" id="rpt-table" style="margin-top:15px;">
        <thead>
        <tr>
            <th width="180px" class="text-left r-top border_top" rowspan="2" style="vertical-align:middle;">DESCRIPTION</th>
            <th class="text-right r-top border_top" rowspan="2" style="vertical-align:middle;">PREVIOUS<br>MONTH</th>
            <th class="r-top border_top" rowspan="2"></th>
            <th class="text-center r-top border_top" colspan="7"><?php echo strtoupper(date("F Y", mktime(0, 0, 0, $month, 1, $year)));?></th>
            <th class="r-top border_top" ></th>
            <th class="text-center r-top border_top" colspan="7">YTD <?php echo $year;?></th>
        </tr>
        <tr>
            <th class="text-right">ACTUAL</th>
            <td class="space border_bottom"></td>
            <th class="text-right">BUDGET</th>
            <td class="space border_bottom"></td>
            <th class="text-right">VAR</th>
            <td class="space border_bottom"></td>
            <th class="text-right">%</th>
            <td class="space border_bottom"></td>
            <th class="text-right r-top">ACTUAL</th>
            <td class="space border_bottom"></td>
            <th class="text-right r-top">BUDGET</th>
            <td class="space border_bottom"></td>
            <th class="text-right r-top">VAR</th>
            <td class="space border_bottom"></td>
            <th class="text-right">%</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $qry = $this->db->query("SELECT * FROM fxnStatementPLToBudget(" . $month . ", " . $year . ") WHERE LTRIM(RTRIM(AccountName)) != ''");
        if($qry->num_rows() > 0){
            $group_name = '';
            $ctg_caption = '';
            $sub_ctg_caption = '';
            $sub_ctg_caption_no = 0;

            $tot_sub_ctg_last_month = 0;
            $tot_sub_ctg_current = 0;
            $tot_sub_ctg_current_budget = 0;
            $tot_sub_ctg_current_var = 0;
            $tot_sub_ctg_ytd_current = 0;
            $tot_sub_ctg_ytd_budget = 0;
            $tot_sub_ctg_ytd_var = 0;

            $tot_ctg_last_month = 0;
            $tot_ctg_current = 0;
            $tot_ctg_current_budget = 0;
            $tot_ctg_current_var = 0;
            $tot_ctg_ytd_current = 0;
            $tot_ctg_ytd_budget = 0;
            $tot_ctg_ytd_var = 0;

            $tot_grp_last_month = 0;
            $tot_grp_current = 0;
            $tot_grp_current_budget = 0;
            $tot_grp_current_var = 0;
            $tot_grp_ytd_current = 0;
            $tot_grp_ytd_budget = 0;
            $tot_grp_ytd_var = 0;

            $prev_month = date('n', strtotime(date("Y-m-d", mktime(0, 0, 0, $month, 1, $year))." -1 month"));
            $prev_year = date('Y', strtotime(date("Y-m-d", mktime(0, 0, 0, $month, 1, $year))." -1 month"));

            $i = 0;
            foreach($qry->result() as $row){
                if($sub_ctg_caption != $row->SubCtgCaption){
                    if($sub_ctg_caption_no > 1){
                        echo '<tr>
								<td style="padding-left:20px;"><strong>TOTAL ' . $sub_ctg_caption . '</strong></td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_last_month) . '</td>
								<td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_current) . '</td>
								<td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_current_budget) . '</td>
								<td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_current_var) . '</td>
								<td class="space"></td>
								<td class="text-right r-border">' . (abs($tot_sub_ctg_current_budget) > 0 ? number_format(round(($tot_sub_ctg_current / $tot_sub_ctg_current_budget) * 100, 0), 0, ",", ",") : '0') . '%</td>
								<td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_ytd_current) . '</td>
								<td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_ytd_budget) . '</td>
								<td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_ytd_var) . '</td>
								<td class="space"></td>
								<td class="text-right r-border">' . (abs($tot_sub_ctg_ytd_budget) > 0 ? number_format(round(($tot_sub_ctg_ytd_current / $tot_sub_ctg_ytd_budget) * 100, 0), 0, ",", ",") : '0') . '%</td>
							</tr>';
                    }
                    $tot_sub_ctg_last_month = 0;
                    $tot_sub_ctg_current = 0;
                    $tot_sub_ctg_current_budget = 0;
                    $tot_sub_ctg_current_var = 0;
                    $tot_sub_ctg_ytd_current = 0;
                    $tot_sub_ctg_ytd_budget = 0;
                    $tot_sub_ctg_ytd_var = 0;

                    $sub_ctg_caption_no = 0;
                }

                if($ctg_caption != $row->CtgCaption){
                    if($ctg_caption != ''){
                        echo '<tr>
								<td><strong>TOTAL ' . $ctg_caption . '</strong></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_last_month) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_current) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_current_budget) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_current_var) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . (abs($tot_ctg_current_budget) > 0 ? number_format(round(($tot_ctg_current / $tot_ctg_current_budget) * 100, 0), 0, ",", ",") : '0') . '%</td>
								<td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_ytd_current) . '</td>
								<td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_ytd_budget) . '</td>
								<td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_ytd_var) . '</td>
								<td class="space"></td>
								<td class="text-right r-border">' . (abs($tot_ctg_ytd_budget) > 0 ? number_format(round(($tot_ctg_ytd_current / $tot_ctg_ytd_budget) * 100, 0), 0, ",", ",") : '0') . '%</td>
							</tr>';

                        $tot_ctg_last_month = 0;
                        $tot_ctg_current = 0;
                        $tot_ctg_current_budget = 0;
                        $tot_ctg_current_var = 0;
                        $tot_ctg_ytd_current = 0;
                        $tot_ctg_ytd_budget = 0;
                        $tot_ctg_ytd_var = 0;
                    }
                }

                if($group_name != $row->GroupName){
                    if($group_name != ''){
                        echo '<tr>
								<td><strong>' . $group_name . '</strong></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_last_month) . '</td>
								<td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_current) . '</td>
								<td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_current_budget) . '</td>
								<td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_current_var) . '</td>
								<td class="space"></td>
								<td class="text-right r-border r-background">' . (abs($tot_grp_current_budget) > 0 ? number_format(round(($tot_grp_current / $tot_grp_current_budget) * 100, 0), 0, ",", ",") : '0') . '%</td>
								<td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_ytd_current) . '</td>
								<td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_ytd_budget) . '</td>
								<td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_ytd_var) . '</td>
								<td class="space"></td>
								<td class="text-right r-border r-background">' . (abs($tot_grp_ytd_budget) > 0 ? number_format(round(($tot_grp_ytd_current / $tot_grp_ytd_budget) * 100, 0), 0, ",", ",") : '0') . '%</td>
							</tr>';
                    }

                    $group_name = $row->GroupName;
                }

                if($ctg_caption != $row->CtgCaption){
                    echo '<tr>
							<td colspan="18"><strong>' . $row->CtgCaption . '</strong></td>
						</tr>';

                    $ctg_caption = $row->CtgCaption;
                }

                if($sub_ctg_caption != $row->SubCtgCaption){

                    echo '<tr>
							<td colspan="18"  style="padding-left:20px;"><strong>' . $row->SubCtgCaption . '</strong></td>
						</tr>';

                    $sub_ctg_caption = $row->SubCtgCaption;
                }

                echo '<tr class="" >
						<td  style="padding-left:40px;" >' . $row->AccountName . '</td>
						<td class="text-right">' . amount_journal($row->LastBalance) . '</td>
                        <td class="space"></td>
						<td class="text-right">' . amount_journal($row->CurrentBalance) . '</td>
                        <td class="space"></td>
						<td class="text-right">' . amount_journal($row->CurrentBudget) . '</td>
                        <td class="space"></td>
						<td class="text-right">' . amount_journal($row->CurrentVariant) . '</td>
                        <td class="space"></td>
						<td class="text-right">' . (abs($row->CurrentBudget) > 0 ? number_format(round(($row->CurrentBalance / $row->CurrentBudget) * 100, 0), 0, ",", ",") : '0') . '%</td>
                        <td class="space"></td>
						<td class="text-right " >' . amount_journal($row->YTDCurrent) . '</td>
                        <td class="space"></td>
						<td class="text-right">' . amount_journal($row->YTDBudget) . '</td>
                        <td class="space"></td>
						<td class="text-right">' . amount_journal($row->YTDVariant) . '</td>
                        <td class="space"></td>
						<td class="text-right">' . (abs($row->YTDBudget) > 0 ? number_format(round(($row->YTDCurrent / $row->YTDBudget) * 100, 0), 0, ",", ",") : '0') . '%</td>
					</tr>';

                $tot_sub_ctg_last_month = $tot_sub_ctg_last_month + $row->LastBalance;
                $tot_sub_ctg_current = $tot_sub_ctg_current + $row->CurrentBalance;
                $tot_sub_ctg_current_budget = $tot_sub_ctg_current_budget + $row->CurrentBudget;
                $tot_sub_ctg_current_var = $tot_sub_ctg_current_var + $row->CurrentVariant;
                $tot_sub_ctg_ytd_current = $tot_sub_ctg_ytd_current + $row->YTDCurrent;
                $tot_sub_ctg_ytd_budget = $tot_sub_ctg_ytd_budget + $row->YTDBudget;
                $tot_sub_ctg_ytd_var = $tot_sub_ctg_ytd_var + $row->YTDVariant;

                $tot_ctg_last_month = $tot_ctg_last_month + $row->LastBalance;
                $tot_ctg_current = $tot_ctg_current + $row->CurrentBalance;
                $tot_ctg_current_budget = $tot_ctg_current_budget + $row->CurrentBudget;
                $tot_ctg_current_var = $tot_ctg_current_var + $row->CurrentVariant;
                $tot_ctg_ytd_current = $tot_ctg_ytd_current + $row->YTDCurrent;
                $tot_ctg_ytd_budget = $tot_ctg_ytd_budget + $row->YTDBudget;
                $tot_ctg_ytd_var = $tot_ctg_ytd_var + $row->YTDVariant;

                $pos = strpos(strtolower($row->CtgCaption), 'income');

                if($pos === false){
                    $tot_grp_last_month = $tot_grp_last_month - $row->LastBalance;
                    $tot_grp_current = $tot_grp_current - $row->CurrentBalance;
                    $tot_grp_current_budget = $tot_grp_current_budget - $row->CurrentBudget;
                    $tot_grp_current_var = $tot_grp_current_var - $row->CurrentVariant;
                    $tot_grp_ytd_current = $tot_grp_ytd_current - $row->YTDCurrent;
                    $tot_grp_ytd_budget = $tot_grp_ytd_budget - $row->YTDBudget;
                    $tot_grp_ytd_var = $tot_grp_ytd_var - $row->YTDVariant;
                }
                else {
                    $tot_grp_last_month = $tot_grp_last_month + $row->LastBalance;
                    $tot_grp_current = $tot_grp_current + $row->CurrentBalance;
                    $tot_grp_current_budget = $tot_grp_current_budget + $row->CurrentBudget;
                    $tot_grp_current_var = $tot_grp_current_var + $row->CurrentVariant;
                    $tot_grp_ytd_current = $tot_grp_ytd_current + $row->YTDCurrent;
                    $tot_grp_ytd_budget = $tot_grp_ytd_budget + $row->YTDBudget;
                    $tot_grp_ytd_var = $tot_grp_ytd_var + $row->YTDVariant;
                }

                $i++;
                $sub_ctg_caption_no++;

                if($i == count($qry->result())){
                    if($ctg_caption != ''){
                        echo '<tr>
								<td><strong>TOTAL ' . $ctg_caption . '</strong></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_last_month) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_current) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_current_budget) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_current_var) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . (abs($tot_ctg_current_budget) > 0 ? number_format(round(($tot_ctg_current / $tot_ctg_current_budget) * 100, 0), 0, ",", ",") : '0') . '%</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_ytd_current) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_ytd_budget) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_ytd_var) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border">' . (abs($tot_ctg_ytd_budget) > 0 ? number_format(round(($tot_ctg_ytd_current / $tot_ctg_ytd_budget) * 100, 0), 0, ",", ",") : '0') . '%</td>
							</tr>';

                        $tot_ctg_last_month = 0;
                        $tot_ctg_current = 0;
                        $tot_ctg_current_budget = 0;
                        $tot_ctg_current_var = 0;
                        $tot_ctg_ytd_current = 0;
                        $tot_ctg_ytd_budget = 0;
                        $tot_ctg_ytd_var = 0;
                    }

                    if($group_name != ''){
                        echo '<tr>
								<td><strong>' . $group_name . '</strong></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_last_month) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_current) . '</td>
                                <td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_current_budget) . '</td>
                                 <td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_current_var) . '</td>
                                 <td class="space"></td>
								<td class="text-right r-border r-background">' . (abs($tot_grp_current_budget) > 0 ? number_format(round(($tot_grp_current / $tot_grp_current_budget) * 100, 0), 0, ",", ",") : '0') . '%</td>
                                 <td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_ytd_current) . '</td>
                                 <td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_ytd_budget) . '</td>
                                 <td class="space"></td>
								<td class="text-right r-border r-background">' . amount_journal($tot_grp_ytd_var) . '</td>
                                 <td class="space"></td>
								<td class="text-right r-border r-background">' . (abs($tot_grp_ytd_budget) > 0 ? number_format(round(($tot_grp_ytd_current / $tot_grp_ytd_budget) * 100, 0), 0, ",", ",") : '0') . '%</td>
							</tr>';

                        $tot_grp_last_month = 0;
                        $tot_grp_current = 0;
                        $tot_grp_current_budget = 0;
                        $tot_grp_current_var = 0;
                        $tot_grp_ytd_current = 0;
                        $tot_grp_ytd_budget = 0;
                        $tot_grp_ytd_var = 0;
                    }
                }
            }
        }
        ?>
        </tbody>
        </table>
		</div>
	</div>
	<!-- END PAGE CONTENT-->
</div>

