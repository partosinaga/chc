<table class="table table-report" style="margin-top:15px;">
    <thead>
    <tr>
        <th width="1%" class="text-center"></th>
        <th width="5%" class="text-center">CODE</th>
        <th class="text-left">DESCRIPTION</th>
        <th width="11%" class="text-right">PREVIOUS</th>
        <th width="11%" class="text-right">DEBIT</th>
        <th width="11%" class="text-right">CREDIT</th>
        <th width="11%" class="text-right">BALANCE</th>
    </tr>
    </thead>
    <tbody>
    <?php

    $specMonth = FNSpec::get(FNSpec::CLOSE_MONTH);
    $specYear = FNSpec::get(FNSpec::CLOSE_YEAR);

    $qry = $this->db->query("SELECT * FROM fxnGL_TrialBalance(". $month .",". $year .") ORDER BY postedYear, postedMonth, coa_code");

    $sum_debit = 0;
    $sum_credit = 0;
    if($qry->num_rows() > 0){
        //COLLECTS FILTERING COA BALANCE SHEET
        $registeredBS = array();
        $layoutBS = GLStatement::get_layout_by_type(GLStatement::BALANCE_SHEET);
        foreach($layoutBS as $layout){
            if($layout['is_rangedformula'] > 0){
                $coas = $this->db->query('SELECT coa_code FROM gl_coa WHERE coa_code >= ' . $layout['range_start'] . ' AND coa_code <= ' . $layout['range_end']);
                if ($coas->num_rows() > 0) {
                    foreach ($coas->result() as $coa) {
                        if (!in_array($coa->coa_code, $registeredBS)) {
                            $registeredBS[] = $coa->coa_code;
                        }
                    }
                }
                /*
                for ($i = $layout['range_start']; $i <= $layout['range_end']; $i++)
                {
                    if (!in_array($i,$registeredBS)){
                        $registeredBS[] = $i;
                    }
                }*/
            }else{
                $splits = explode('+',trim($layout['text_formula']));
                foreach($splits as $coa_code){
                    $tcoacode = trim($coa_code);
                    if($tcoacode != ''){
                        if (!in_array($tcoacode,$registeredBS)){
                            $registeredBS[] = $tcoacode;
                        }
                    }
                }
            }
        }

        //COLLECTS FILTERING COA PROFIT LOSS
        $registeredPL = array();
        $layoutPL = GLStatement::get_layout_by_type(GLStatement::PROFIT_LOSS);
        foreach($layoutPL as $layout){
            if($layout['is_rangedformula'] > 0){
                $coas = $this->db->query('SELECT coa_code FROM gl_coa WHERE coa_code >= ' . $layout['range_start'] . ' AND coa_code <= ' . $layout['range_end']);
                if ($coas->num_rows() > 0) {
                    foreach ($coas->result() as $coa) {
                        if (!in_array($coa->coa_code, $registeredBS)) {
                            $registeredPL[] = $coa->coa_code;
                        }
                    }
                }
                /*
                for ($i = $layout['range_start']; $i <= $layout['range_end']; $i++)
                {
                    if (!in_array($i,$registeredPL)){
                        $registeredPL[] = $i;
                    }
                }*/
            }else{
                $splits = explode('+',trim($layout['text_formula']));
                foreach($splits as $coa_code){
                    if(trim($coa_code) != ''){
                        if (!in_array($coa_code,$registeredPL)){
                            $registeredPL[] = $coa_code;
                        }
                    }
                }
            }
        }

        //BIND DETAIL
        $trialTable = array();

        $listBS = array();
        $listPL = array();

        $i = 0;
        $closingDate = $year . '-' . $month . '-' . days_in_month($month, $year);
        $coa_code = '';
        foreach($qry->result_array() as $row){
            $coa_code = $row['coa_code'];
            $classType = $row['class_type'];

            if($coa_code != $specMonth['coa_code'] && $coa_code != $specYear['coa_code']){
                $balance = 0;
                $prev_balance = 0;
                $hasLayout = true;
                $prefix='';

                $prev = $this->db->query("SELECT ISNULL(SUM(balance),0) as balanceAmount FROM view_close_statement
                                          WHERE  closingdate < '" . $closingDate . "' AND coa_code = '" . $coa_code . "'");

                if($prev->num_rows() > 0){
                    $prev_balance = $prev->row()->balanceAmount;
                }

                if($row['is_debit'] > 0){
                    $balance = ($prev_balance + $row['debit']) - $row['credit'];
                }else{
                    $balance = ($prev_balance + $row['credit']) - $row['debit'];
                }

                if($classType == GLClassType::ASSET || $classType == GLClassType::LIABILITY || $classType == GLClassType::CAPITAL){
                    if(!in_array($coa_code,$listBS)){
                        if($balance != 0){
                            if(!in_array($coa_code,$registeredBS)){
                                $hasLayout = false;
                            }
                        }

                        if(!$hasLayout){
                            $prefix = '<span class="badge bg-blue-hoki" style="margin-top: -2px;">BS</span>';
                        }
                    }
                }else{
                    if(!in_array($coa_code,$listPL)){
                        $hasLayout = true;
                        if($balance != 0){
                            if(!in_array($coa_code,$registeredPL)){
                                $hasLayout = false;
                            }
                        }

                        $prefix='';
                        if(!$hasLayout){
                            $prefix = '<span class="badge bg-red-sunglo" style="margin-top: -2px;">PL</span>';
                        }
                    }
                }

                $row_class= 'link-detail';
                if(!$hasLayout){
                    $row_class .= ' link-attention';
                }

                echo '<tr class="'. $row_class .'">
                                <td class="text-right ">' . $prefix . '</td>
                                <td class="text-center">' . $coa_code . '</td>
                                <td>' . $row['coa_desc'] . '</td>
                                <td class="text-right">' . amount_journal($prev_balance) . '</td>
                                <td class="text-right">' . amount_journal($row['debit']) . '</td>
                                <td class="text-right">' . amount_journal($row['credit']) . '</td>
                                <td class="text-right">' . amount_journal($balance) . '</td>
                            </tr>';

                //echo '<br>' . $this->db->last_query();
                $i++;
                $sum_debit += $row['debit'];
                $sum_credit += $row['credit'];
            }
        }
    }


    ?>
    </tbody>
    <tfoot style="border-top: 2px solid #333;">
    <?php
    echo "<tr style='font-weight:bold;'>
                <td colspan='4' class='text-right' >TOTAL</td>
                <td class='text-right'>". amount_journal($sum_debit) .  "</td>
                <td class='text-right'>". amount_journal($sum_credit) .  "</td>
                <td >&nbsp;</td>
              </tr>";
    ?>
    </tfoot>
</table>
<table class="table table-report" style="margin-top:10px;">
    <thead>
    <tr>
        <th width="1%" class="text-center"></th>
        <th width="5%" class="text-center">CODE</th>
        <th class="text-left">DESCRIPTION</th>
        <th width="11%" class="text-right">PREVIOUS</th>
        <th width="11%" class="text-right">DEBIT</th>
        <th width="11%" class="text-right">CREDIT</th>
        <th width="11%" class="text-right">BALANCE</th>
    </tr>
    </thead>
    <tbody>
    <?php

    if($qry->num_rows() > 0){
        $coa_code = '';
        foreach($qry->result_array() as $row){
            $coa_code = $row['coa_code'];
            $classType = $row['class_type'];

            if($coa_code == $specMonth['coa_code'] || $coa_code == $specYear['coa_code']){
                $balance = 0;
                $prev_balance = 0;
                $hasLayout = true;
                $prefix='';

                $prev = $this->db->query("SELECT ISNULL(SUM(balance),0) as balanceAmount FROM view_close_statement
                                          WHERE  closingdate < '" . $closingDate . "' AND coa_code = '" . $coa_code . "'");

                if($prev->num_rows() > 0){
                    $prev_balance = $prev->row()->balanceAmount;
                }

                if($row['is_debit'] > 0){
                    $balance = ($prev_balance + $row['debit']) - $row['credit'];
                }else{
                    $balance = ($prev_balance + $row['credit']) - $row['debit'];
                }

                if($classType == GLClassType::ASSET || $classType == GLClassType::LIABILITY || $classType == GLClassType::CAPITAL){
                    if(!in_array($coa_code,$listBS)){
                        if($balance != 0){
                            if(!in_array($coa_code,$registeredBS)){
                                $hasLayout = false;
                            }
                        }

                        if(!$hasLayout){
                            $prefix = '<span class="badge bg-blue-hoki" style="margin-top: -2px;">BS</span>';
                        }
                    }
                }else{
                    if(!in_array($coa_code,$listPL)){
                        $hasLayout = true;
                        if($balance != 0){
                            if(!in_array($coa_code,$registeredPL)){
                                $hasLayout = false;
                            }
                        }

                        $prefix='';
                        if(!$hasLayout){
                            $prefix = '<span class="badge bg-red-sunglo" style="margin-top: -2px;">PL</span>';
                        }
                    }
                }

                $row_class= 'link-detail';
                if(!$hasLayout){
                    $row_class .= ' link-attention';
                }

                echo '<tr class="'. $row_class .'">
                                <td class="text-right " width="1%">' . $prefix . '</td>
                                <td class="text-center">' . $coa_code . '</td>
                                <td>' . $row['coa_desc'] . '</td>
                                <td class="text-right">' . amount_journal($prev_balance) . '</td>
                                <td class="text-right">' . amount_journal($row['debit']) . '</td>
                                <td class="text-right">' . amount_journal($row['credit']) . '</td>
                                <td class="text-right">' . amount_journal($balance) . '</td>
                            </tr>';

                //echo '<br>' . $this->db->last_query();
                $i++;
            }
        }
    }

    ?>
    </tbody>
    <tfoot style="border-top: 1px solid #333;">

    </tfoot>
</table>
