<div class="page-content" style="margin-left:0px; min-height:700px;">
	<!-- BEGIN PAGE HEADER-->
	<div class="row hidden-print">
		<div class="col-md-12">
			<!-- BEGIN PAGE TITLE-->
			<h3 class="page-title text-center" style="font-size:13px;margin-bottom:5px;"><?php echo $profile['company_name']; ?></h3>
			<h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">CASH FLOW</h3>
			<h3 class="page-title text-center" style="font-size:13px;margin-bottom:5px;">Amounts in (IDR)</h3>
			<h3 class="page-title text-center" style="font-size:13px;margin-bottom:5px;">As Of <?php echo date("F Y", mktime(0, 0, 0, $month, 1, $year));?></h3>
			<!-- END PAGE TITLE & BREADCRUMB-->
		</div>
	</div>
	<!-- END PAGE HEADER-->
	<!-- BEGIN PAGE CONTENT-->
	<div class="table_small">
		<div class="row">
			<div class="col-xs-12">
            <table class="table table-report" style="margin-top:15px;">
            <thead>
            <tr>
                <th width="300px" class="text-left">DESCRIPTION</th>
                <th width="100px" class="text-right"><?php echo strtoupper(date("M Y", mktime(0, 0, 0, $month, 1, $year)));?></th>
                <th width="2px">&nbsp;</th>
                <th width="100px" class="text-right"><?php echo strtoupper(date('M Y', strtotime(date("Y-m-d", mktime(0, 0, 0, $month, 1, $year))." -1 month")));?></th>
                <th width="2">&nbsp;</th>
                <th width="100px" class="text-right">YTD</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $qry = $this->db->query("SELECT * FROM fxnStatementCF(" . $month . ", " . $year . ") WHERE LTRIM(RTRIM(AccountName)) != '' ORDER BY CtgID, SubCtgID");
            if($qry->num_rows() > 0){
                $group_name = '';
                $ctg_caption = '';
                $sub_ctg_caption = '';
                $sub_ctg_caption_no = 0;
                $tot_sub_ctg_current = 0;
                $tot_sub_ctg_last_month = 0;
                $tot_sub_ctg_ytd = 0;
                $tot_ctg_current = 0;
                $tot_ctg_last_month = 0;
                $tot_ctg_ytd = 0;
                $tot_grp_current = 0;
                $tot_grp_last_month = 0;
                $tot_grp_ytd = 0;
                $tot_net_current = 0;
                $tot_net_last_month = 0;
                $tot_net_ytd = 0;

                $prev_month = date('n', strtotime(date("Y-m-d", mktime(0, 0, 0, $month, 1, $year))." -1 month"));
                $prev_year = date('Y', strtotime(date("Y-m-d", mktime(0, 0, 0, $month, 1, $year))." -1 month"));

                $prev_month2 = date('n', strtotime(date("Y-m-d", mktime(0, 0, 0, $month, 1, $year))." -2 month"));
                $prev_year2 = date('Y', strtotime(date("Y-m-d", mktime(0, 0, 0, $month, 1, $year))." -2 month"));

                $i = 0;
                foreach($qry->result() as $row){

                    if($sub_ctg_caption != $row->SubCtgCaption){
                        if($sub_ctg_caption_no > 1){
                            if(abs($tot_sub_ctg_current) > 0 || abs($tot_sub_ctg_last_month) > 0 || abs($tot_sub_ctg_ytd) > 0){
                                echo '<tr>
									<td style="padding-left:40px;"><strong>' . $sub_ctg_caption . '</strong></td>
									<td class="text-right r-border ">' . amount_journal($tot_sub_ctg_current) . '</td>
									<td>&nbsp;</td>
									<td class="text-right r-border">' . amount_journal($tot_sub_ctg_last_month) . '</td>
									<td>&nbsp;</td>
									<td class="text-right r-border">' . amount_journal($tot_sub_ctg_ytd) . '</td>
								</tr>';
                            }
                        }
                        $tot_sub_ctg_current = 0;
                        $tot_sub_ctg_last_month = 0;
                        $tot_sub_ctg_ytd = 0;

                        $sub_ctg_caption_no = 0;
                    }

                    if($ctg_caption != $row->CtgCaption){
                        if($ctg_caption != ''){
                            echo '<tr>
								<td style="padding-left: 20px;"><strong>Total ' . $ctg_caption . '</strong></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_current) . '</td>
								<td>&nbsp;</td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_last_month) . '</td>
								<td>&nbsp;</td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_ytd) . '</td>
							</tr>';

                            $tot_ctg_current = 0;
                            $tot_ctg_last_month = 0;
                            $tot_ctg_ytd = 0;
                        }
                    }

                    if($group_name != $row->GroupName){
                        if($group_name != ''){
                            echo '<tr>
								<td><strong>NET ' . $group_name . '</strong></td>
								<td class="text-right r-border">' . amount_journal($tot_grp_current) . '</td>
								<td>&nbsp;</td>
								<td class="text-right r-border">' . amount_journal($tot_grp_last_month) . '</td>
								<td>&nbsp;</td>
								<td class="text-right r-border">' . amount_journal($tot_grp_ytd) . '</td>
							</tr>';

                            $tot_grp_current = 0;
                            $tot_grp_last_month = 0;
                            $tot_grp_ytd = 0;

                        }
                    }

                    if($group_name != $row->GroupName){
                        echo '<tr>
							<td colspan="6"><strong>' . $row->GroupName . '</strong></td>
						</tr>';

                        $group_name = $row->GroupName;
                    }

                    if($ctg_caption != $row->CtgCaption){
                        echo '<tr>
							<td colspan="6" style="padding-left:20px;"><strong>' . $row->CtgCaption . '</strong></td>
						</tr>';

                        $ctg_caption = $row->CtgCaption;
                    }

                    if($sub_ctg_caption != $row->SubCtgCaption){
                        $sub_ctg_caption = $row->SubCtgCaption;
                    }

                    echo '<tr class="' . (abs($row->CurrentBalance) > 0 ? 'link-detail' : '') . '" onclick="open_detail(\'' . $month . '\', \'' . $year . '\', \'' . $row->SubCtgCaption . '\', \'' . $row->AccountName . '\', ' . abs($row->CurrentBalance) . ');">
						<td style="padding-left:60px;">' . $row->AccountName . '</td>
						<td class="text-right">' . amount_journal($row->CurrentBalance) . '</td>
						<td>&nbsp;</td>
						<td class="text-right">' . amount_journal($row->LastBalance) . '</td>
						<td>&nbsp;</td>
						<td class="text-right">' . amount_journal($row->YTDCurrent) . '</td>
					</tr>';

                    $tot_sub_ctg_current = $tot_sub_ctg_current + $row->CurrentBalance;
                    $tot_sub_ctg_last_month = $tot_sub_ctg_last_month + $row->LastBalance;
                    $tot_sub_ctg_ytd = $tot_sub_ctg_ytd + $row->YTDCurrent;

                    $tot_ctg_current = $tot_ctg_current + $row->CurrentBalance;
                    $tot_ctg_last_month = $tot_ctg_last_month + $row->LastBalance;
                    $tot_ctg_ytd = $tot_ctg_ytd + $row->YTDCurrent;

                    $tot_grp_current = $tot_grp_current + $row->CurrentBalance;
                    $tot_grp_last_month = $tot_grp_last_month + $row->LastBalance;
                    $tot_grp_ytd = $tot_grp_ytd + $row->YTDCurrent;

                    $tot_net_current = $tot_net_current + $row->CurrentBalance;
                    $tot_net_last_month = $tot_net_last_month + $row->LastBalance;
                    $tot_net_ytd = $tot_net_ytd + $row->YTDCurrent;

                    $i++;
                    $sub_ctg_caption_no++;

                    if($i == count($qry->result())){

                        if($sub_ctg_caption != ''){
                            if(abs($tot_sub_ctg_current) > 0 || abs($tot_sub_ctg_last_month) > 0 || abs($tot_sub_ctg_ytd) > 0){
                                echo '<tr>
									<td style="padding-left:40px;"><strong>' . $sub_ctg_caption . '</strong></td>
									<td class="text-right r-border">' . amount_journal($tot_sub_ctg_current) . '</td>
									<td>&nbsp;</td>
									<td class="text-right r-border">' . amount_journal($tot_sub_ctg_last_month) . '</td>
									<td>&nbsp;</td>
									<td class="text-right r-border">' . amount_journal($tot_sub_ctg_ytd) . '</td>
								</tr>';

                                $tot_sub_ctg_current = 0;
                                $tot_sub_ctg_last_month = 0;
                                $tot_sub_ctg_ytd = 0;
                            }
                        }

                        if($ctg_caption != ''){
                            echo '<tr>
								<td style="padding-left:20px;"><strong>Total ' . $ctg_caption . '</strong></td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_current) . '</td>
								<td>&nbsp;</td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_last_month) . '</td>
								<td>&nbsp;</td>
								<td class="text-right r-border">' . amount_journal($tot_ctg_ytd) . '</td>
							</tr>';

                            $tot_ctg_current = 0;
                            $tot_ctg_last_month = 0;
                            $tot_ctg_ytd = 0;
                        }

                        if($group_name != ''){
                            echo '<tr>
								<td><strong>NET ' . $group_name . '</strong></td>
								<td class="text-right r-border">' . amount_journal($tot_grp_current) . '</td>
								<td>&nbsp;</td>
								<td class="text-right r-border">' . amount_journal($tot_grp_last_month) . '</td>
								<td>&nbsp;</td>
								<td class="text-right r-border">' . amount_journal($tot_grp_ytd) . '</td>
							</tr>';

                            $tot_grp_current = 0;
                            $tot_grp_last_month = 0;
                            $tot_grp_ytd = 0;
                        }
                    }
                }
            }

            //total net
            echo '<tr>
					<td><strong>NET INCREASE (DECREASE) IN CASH & EQUIVALENTS</strong></td>
					<td class="text-right r-border r-background">' . amount_journal($tot_net_current) . '</td>
					<td>&nbsp;</td>
					<td class="text-right r-border r-background">' . amount_journal($tot_net_last_month) . '</td>
					<td>&nbsp;</td>
					<td class="text-right r-border r-background">' . amount_journal($tot_net_ytd) . '</td>
				</tr>';

            $qry_beg = $this->db->query("SELECT Text_Formula FROM GL_FinanceStatement_Detail WHERE IsCashFlow > 0");

            $tot_beg_current = 0;
            $tot_beg_last_month = 0;
            $tot_beg_ytd = 0;
            if($qry_beg->num_rows() > 0){
                foreach($qry_beg->result() as $row_beg){
                    //-1 month
                    $qry_beg1 = $this->db->query("select dbo.fxnStatementBSByFormula(" . $prev_month . "," . $prev_year . ",'" . $row_beg->Text_Formula . "') as formula");
                    $row_beg1 = $qry_beg1->row();
                    $tot_beg_current = $tot_beg_current + $row_beg1->formula;

                    //-2 month
                    $qry_beg2 = $this->db->query("select dbo.fxnStatementBSByFormula(" . $prev_month2 . "," . $prev_year2 . ",'" . $row_beg->Text_Formula . "') as formula");
                    $row_beg2 = $qry_beg2->row();
                    $tot_beg_last_month = $tot_beg_last_month + $row_beg2->formula;

                    //-1 year
                    $qry_beg3 = $this->db->query("select dbo.fxnStatementBSByFormula(" . $month . "," . ($year - 1) . ",'" . $row_beg->Text_Formula . "') as formula");
                    $row_beg3 = $qry_beg3->row();
                    $tot_beg_ytd = $tot_beg_ytd + $row_beg3->formula;;
                }
            }
            //total net begin
            echo '<tr>
					<td><strong>CASH & CASH EQUIVALENT AT BEGINNING OF PERIOD</strong></td>
					<td class="text-right r-border">' . amount_journal($tot_beg_current) . '</td>
					<td>&nbsp;</td>
					<td class="text-right r-border">' . amount_journal($tot_beg_last_month) . '</td>
					<td>&nbsp;</td>
					<td class="text-right r-border">' . amount_journal($tot_beg_ytd) . '</td>
				</tr>';

            //total net end
            echo '<tr>
					<td><strong>CASH & CASH EQUIVALENT AT THE END OF THE PERIOD</strong></td>
					<td class="text-right r-border r-background">' . amount_journal($tot_beg_current + $tot_net_current) . '</td>
					<td>&nbsp;</td>
					<td class="text-right r-border r-background">' . amount_journal($tot_beg_last_month + $tot_net_last_month) . '</td>
					<td>&nbsp;</td>
					<td class="text-right r-border r-background">' . amount_journal($tot_beg_ytd + $tot_net_ytd) . '</td>
				</tr>';
            ?>
            </tbody>
            </table>
			</div>
		</div>
	</div>
	<!-- END PAGE CONTENT-->
</div>

