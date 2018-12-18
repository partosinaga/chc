<table class="table table-report" style="margin-top:15px;">
    <thead>
    <tr>
        <th width="55%" class="text-left">DESCRIPTION</th>
        <th width="15%" class="text-right">YTD Current</th>
        <th width="2">&nbsp;</th>
        <th width="15%" class="text-right">YTD Last</th>
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
                    echo '<tr>
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
                    echo '<tr>
								<td class="r-background r-border"><strong>TOTAL ' . $ctg_caption . '</strong></td>
								<td class="text-right r-border r-background"><strong>' . amount_journal($tot_ctg_current) . '</strong></td>
								<td class="r-background r-border"></td>
								<td class="text-right r-border r-background"><strong>' . amount_journal($tot_ctg_last) . '</strong></td>
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
            echo '<tr>
						<td class="' . (abs($row->YTDCurrent) > 0 ? 'link-detail' : '') . '" style="padding-left:40px;" onclick="open_detail_ytd(\'' . $month . '\', \'' . $year . '\', \'' . $row->CtgCaption . '\', \'' . $row->SubCtgCaption . '\', \'' . $row->AccountName . '\', ' . abs($row->YTDCurrent) . ');">' . $row->AccountName . '</td>
						<td class="text-right ' . (abs($row->YTDCurrent) > 0 ? 'link-detail' : '') . '" onclick="open_detail_ytd(\'' . $month . '\', \'' . $year . '\', \'' . $row->CtgCaption . '\', \'' . $row->SubCtgCaption . '\', \'' . $row->AccountName . '\', ' . abs($row->YTDCurrent) . ');">' . amount_journal($row->YTDCurrent) . '</td>
						<td>&nbsp;</td>
						<td class="text-right ' . (abs($row->YTDLast) > 0 ? 'link-detail' : '') . '" onclick="open_detail_ytd(\'' . $prev_month . '\', \'' . $prev_year . '\', \'' . $row->CtgCaption . '\', \'' . $row->SubCtgCaption . '\', \'' . $row->AccountName . '\', ' . abs($row->YTDCurrent) . ');">' . amount_journal($row->YTDLast) . '</td>
					</tr>';

            $tot_sub_ctg_current = $tot_sub_ctg_current + $row->YTDCurrent;
            $tot_sub_ctg_last = $tot_sub_ctg_last + $row->YTDLast;

            $tot_ctg_current = $tot_ctg_current + $row->YTDCurrent;
            $tot_ctg_last = $tot_ctg_last + $row->YTDLast;

            $i++;

            if($i == count($qry->result())){
                if($sub_ctg_caption != ''){
                    echo '<tr>
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
                    echo '<tr>
								<td class="r-background r-border"><strong>TOTAL ' . $ctg_caption . '</strong></td>
								<td class="text-right r-border r-background"><strong>' . amount_journal($tot_ctg_current) . '</strong></td>
								<td class="r-background r-border">&nbsp;</td>
								<td class="text-right r-border r-background"><strong>' . amount_journal($tot_ctg_last) . '</strong></td>
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