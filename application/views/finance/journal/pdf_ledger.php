<html lang="en">
<head>
	<meta charset="utf-8">
	<title>General Ledger</title>

	<link href="<?php echo base_url(); ?>assets/css/report_gl.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
</head>
<body id="body_content">
	<script type="text/php">
        if ( isset($pdf) ) {
            $font = Font_Metrics::get_font("helvetica", "");
            //$pdf->page_text(520, 810, "page {PAGE_NUM} of {PAGE_COUNT}", $font, 5, array(0,0,0));
            //$pdf->page_text(520, 10, "page {PAGE_NUM} of {PAGE_COUNT}", $font, 5, array(0,0,0));
        }
    </script>

    <?php if(!$is_pdf){ ?>
        <a href="javascript:;" id="btn_print" title="PDF Format"><i class="fa fa-print fa-2x" ></i></a>&nbsp;&nbsp;
        <a href="javascript:;" id="btn_export_xls" title="Export Excel"><i class="fa fa-file-excel-o fa-2x font-green" ></i></a>
    <?php }else{ ?>
        <div id="logo_container">
            <img class="logo_img_left" src="<?php echo FCPATH; ?>assets/img/logo_dwijaya.png" style="width: 50px;"/>
        </div>
    <?php }?>
    <div id="<?php echo ($is_pdf ? 'header_container' : 'header') ?> " style="z-index:-100;">
        <table style="width:100%;">
            <tr>
                <td class="text-center" style="padding-top: 10px;font-size: 14pt;"><strong>GENERAL LEDGER</strong></td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 12pt;">(Amount In IDR)</td>
            </tr>
            <tr>
                <td class="text-center"><?php echo str_replace('-','/',$header['date_from']) . ' - ' . str_replace('-','/',$header['date_to']) ; ?></td>
            </tr>
        </table>
    </div>
    <div id="container">
        <input type="hidden" name="date_start" value="<?php echo(isset($date_start) ? $date_start : ''); ?>">
        <input type="hidden" name="date_to" value="<?php echo(isset($date_to) ? $date_to : ''); ?>">
        <input type="hidden" name="coa_code_list" value="<?php echo(isset($coa_code_list) ? $coa_code_list : ''); ?>">
        <form action="javascript:;" id="form-entry" class="hide" method="post"></form>

        <div id="content" style="margin-top: 0px;">
            <div id="body">
                <!-- <table style="width:100%;" class="table_detail"> -->
                <?php
                if(isset($qry_det)){
                $grpCOA = '';
                $grpDesc = '';
                $i = 0;

                $sum_balance = 0;
                foreach($qry_det as $record){
                if($grpCOA != $record['coa_code']){
                $strGroupName = $grpCOA . ' ' . $grpDesc;
                if($grpCOA != ''){
                    echo '<tr>
                                            <th class="text-right" colspan="6" style="padding-right:20px;">' . $strGroupName . ' - Ending Balance' . '</th>
                                            <th class="text-right" style="padding-right:5px;">' . number_format($sum_balance,0,',','.') . '</th>
                                        </tr>';

                    echo '<tr><td colspan="7" style="border-right:0px;border-left: 0px;border-bottom: 0px;"></td></tr>
                                      </table>';

                    $sum_balance = 0;
                }
                ?>
                <table style="width:100%;margin-bottom: 20px;" class="table_detail">
                    <thead>
                    <tr>
                        <th style="text-align: left;padding-left: 10px;" colspan="7"><strong>ACCOUNT&nbsp;:&nbsp;<?php echo $record['coa_code'] . ' ' . $record['coa_desc'] ;?></strong></th>
                    </tr>
                    <tr>
                        <th width="50px" >Date</th>
                        <th width="80px" >Doc No</th>
                        <th width="65px" >Filing No</th>
                        <th class="text-left">Description</th>
                        <th width="80px" >Debit </th>
                        <th width="80px" >Credit </th>
                        <th width="80px" >Balance</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $grpCOA = $record['coa_code'];
                    $grpDesc = $record['coa_desc'];
                    }

                    echo '<tr>
                                            <td width="50px" class="text-center">' . $record['journal_date'] . '</td>
                                            <td width="80px" class="text-center">' . $record['journal_no'] . '</td>
                                            <td width="65px" class="text-center">' . ($record['filing_no'] != '' ? $record['filing_no'] : '-') . '</td>
                                            <td >' . nl2br($record['journal_note'], true) . '</td>';
                    if($record['is_debit'] && $record['journal_credit'] > 0){
                        echo           '<td width="80px" class="text-right" style="padding-right:5px;">' . number_format($record['journal_debit'],0,',','.') . '</td>
                                        <td width="80px" class="text-right" style="padding-right:5px;color:red;">' . number_format($record['journal_credit'],0,',','.') . '</td>';
                    }else if(!$record['is_debit'] && $record['journal_debit'] > 0){
                        echo           '<td width="80px" class="text-right" style="padding-right:5px;color:red;">' . number_format($record['journal_debit'],0,',','.') . '</td>
                                        <td width="80px" class="text-right" style="padding-right:5px;">' . number_format($record['journal_credit'],0,',','.') . '</td>';
                    }else{
                        echo           '<td width="80px" class="text-right" style="padding-right:5px;">' . number_format($record['journal_debit'],0,',','.') . '</td>
                                        <td width="80px" class="text-right" style="padding-right:5px;">' . number_format($record['journal_credit'],0,',','.') . '</td>';
                    }
                    echo               '<td width="80px" class="text-right" style="padding-right:5px;">' . number_format($record['balance'],0,',','.') . '</td>
                                        </tr>';

                    $sum_balance = $record['balance'];
                    $i++;

                    if($i == count($qry_det)){
                        if($grpCOA != ''){
                            echo '<tr>
                                            <th class="text-right" colspan="6" style="padding-right:20px;">' . $record['coa_code'] . ' ' . $record['coa_desc'] . ' - Ending Balance' . '</th>
                                            <th class="text-right" style="padding-right:5px;">' . number_format($sum_balance,0,',','.') . '</th>
                                        </tr>';
                            echo '<tr><td colspan="7" style="border-right:0px;border-left: 0px;border-bottom: 0px;"></td>
                                      </tr></tbody></table>';

                            $sum_balance = 0;
                        }
                    }
                    }
                    }
                    ?>

            </div>
        </div>
        <script>
            $(document).ready(function(){
                $('#btn_print').on('click',function() {
                    var dateStart = $('input[name="date_start"]').val();
                    var dateTo = $('input[name="date_to"]').val();
                    var coa_code_list = $('input[name="coa_code_list"]').val();;
                    //console.log('COA ' + coa_code_list);
                    if(coa_code_list != '' && dateStart != '' && dateTo != ''){
                        var url = '<?php echo base_url('finance/ledger/pdf_ledger.tpd');?>';
                        var params = '<input type="hidden" name="date_start" value="' + dateStart + '">' +
                            '<input type="hidden" name="date_to" value="' + dateTo + '">' +
                            '<input type="hidden" name="coa_code_list" value="' + coa_code_list + '">' +
                            '<input type="hidden" name="is_pdf" value="1">';
                        $("#form-entry").append(params);
                        $("#form-entry").attr("method", "post");
                        //$("#form-entry").attr("target", "_blank");
                        $('#form-entry').attr('action', url).submit();
                    }else{

                    }
                });

                $("#btn_export_xls").on('click', function () {
                    //console.log('Exporting ...');
                    window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#body_content').html()));
                    //e.preventDefault();
                });
            });
        </script>
    </div></body></html><!-- /body></html -->