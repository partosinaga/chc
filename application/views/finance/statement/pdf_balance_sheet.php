<div class="page-content" style="margin-left:0px; min-height:700px;">
    <!-- BEGIN PAGE HEADER-->
    <div class="row ">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE-->
            <h3 class="page-title text-center" style="font-size:13px;margin-bottom:5px;"><?php echo $profile['company_name']; ?></h3>
            <h3 class="page-title text-center" style="font-size:20px;margin-bottom:5px;">BALANCE SHEET</h3>
            <h3 class="page-title text-center" style="font-size:13px;margin-bottom:5px;">Amounts in (IDR)</h3>
            <h3 class="page-title text-center" style="font-size:13px;margin-bottom:5px;">As Of <?php echo date("F Y", mktime(0, 0, 0, $month, 1, $year));?></h3>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <!-- END PAGE HEADER-->
    <!-- BEGIN PAGE CONTENT-->
    <div class="invoice" >
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-report" style="margin-top:15px;">
                    <thead>
                    <tr>
                        <th width="400px" class="text-left">DESCRIPTION</th>
                        <th width="100px" class="text-right">YTD Current</th>
                        <th width="8px">&nbsp;</th>
                        <th width="100px" class="text-right">YTD Last</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $qry = $this->db->query("SELECT * FROM fxnStatementBS(" . $month . ", " . $year . ") WHERE LTRIM(RTRIM(AccountName)) != ''");

                    if($qry->num_rows() > 0){
                        $ctg_caption = '';
                        $sub_ctg_caption = '';
                        $tot_sub_ctg_current = 0;
                        $tot_sub_ctg_last = 0;
                        $tot_ctg_current = 0;
                        $tot_ctg_last = 0;

                        $prev_month = date('n', strtotime(date("Y-m-d", mktime(0, 0, 0, $month, 1, $year))." -1 month"));
                        $prev_year = date('Y', strtotime(date("Y-m-d", mktime(0, 0, 0, $month, 1, $year))." -1 month"));

                        $i = 0;
                        foreach($qry->result() as $row){

                            if($sub_ctg_caption != $row->SubCtgCaption){
                                if($sub_ctg_caption != ''){
                                    echo '<tr >
								<td style="padding-left:20px;"><strong>TOTAL ' . $sub_ctg_caption . '</strong></td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_current) . '</td>
								<td></td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_last) . '</td>
							</tr>';
                                }
                                $tot_sub_ctg_current = 0;
                                $tot_sub_ctg_last = 0;
                            }

                            if($ctg_caption != $row->CtgCaption){
                                if($ctg_caption != ''){
                                    echo '<tr >
								<td class="r-background r-border" style="padding-top:5px;padding-bottom:5px;"><strong>TOTAL ' . $ctg_caption . '</strong></td>
								<td class="text-right r-border r-background" style="padding-top:5px;padding-bottom:5px;"><strong>' . amount_journal($tot_ctg_current) . '</strong></td>
								<td class="r-background r-border" style="padding-top:5px;padding-bottom:5px;"></td>
								<td class="text-right r-border r-background" style="padding-top:5px;padding-bottom:5px;"><strong>' . amount_journal($tot_ctg_last) . '</strong></td>
							</tr>';

                                    $tot_ctg_current = 0;
                                    $tot_ctg_last = 0;
                                }
                            }

                            if($ctg_caption != $row->CtgCaption){
                                echo '<tr>
							<td colspan="4"><strong>' . $row->CtgCaption . '</strong></td>
						</tr>';

                                $ctg_caption = $row->CtgCaption;
                            }

                            if($sub_ctg_caption != $row->SubCtgCaption){

                                echo '<tr>
							<td colspan="4" style="padding-left:20px;"><strong>' . $row->SubCtgCaption . '</strong></td>
						</tr>';

                                $sub_ctg_caption = $row->SubCtgCaption;
                            }
                            echo '<tr >
						<td style="padding-left:40px;" >' . $row->AccountName . '</td>
						<td class="text-right " >' . amount_journal($row->YTDCurrent) . '</td>
						<td>&nbsp;</td>
						<td class="text-right ">' . amount_journal($row->YTDLast) . '</td>
					</tr>';

                            $tot_sub_ctg_current = $tot_sub_ctg_current + $row->YTDCurrent;
                            $tot_sub_ctg_last = $tot_sub_ctg_last + $row->YTDLast;

                            $tot_ctg_current = $tot_ctg_current + $row->YTDCurrent;
                            $tot_ctg_last = $tot_ctg_last + $row->YTDLast;

                            $i++;

                            if($i == count($qry->result())){
                                if($sub_ctg_caption != ''){
                                    echo '<tr style="margin-top:5px;margin-bottom:5px;">
								<td style="padding-left:20px;"><strong>TOTAL ' . $sub_ctg_caption . '</strong></td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_current) . '</td>
								<td>&nbsp;</td>
								<td class="text-right r-border">' . amount_journal($tot_sub_ctg_last) . '</td>
							</tr>';

                                    $tot_sub_ctg_current = 0;
                                    $tot_sub_ctg_last = 0;
                                }
                            }

                            if($i == count($qry->result())){
                                if($ctg_caption != ''){
                                    echo '<tr >
								<td class="r-background r-border" style="padding-top:5px;padding-bottom:5px;"><strong>TOTAL ' . $ctg_caption . '</strong></td>
								<td class="text-right r-border r-background" style="padding-top:5px;padding-bottom:5px;"><strong>' . amount_journal($tot_ctg_current) . '</strong></td>
								<td class="r-background r-border" style="padding-top:5px;padding-bottom:5px;">&nbsp;</td>
								<td class="text-right r-border r-background" style="padding-top:5px;padding-bottom:5px;"><strong>' . amount_journal($tot_ctg_last) . '</strong></td>
							</tr>';

                                    $tot_ctg_current = 0;
                                    $tot_ctg_last = 0;
                                }
                            }
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>

