<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $row->po_code;?></title>

    <link href="<?php echo base_url(); ?>assets/css/pdf_po_cs.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<script type = "text/php">
    if ( isset($pdf) ) {
        $pdf->page_script('
            if ($PAGE_COUNT > 1) {
                $font = Font_Metrics::get_font("Arial, Helvetica, sans-serif", "normal");
                $size = 7;
                $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $y = $pdf->get_height() - 24;
                $x = $pdf->get_width() - 35 - Font_Metrics::get_text_width($pageText, $font, $size);
                $pdf->text($x, $y, $pageText, $font, $size);
            }
        ');
    }
</script>

<table>
    <tr>
        <td><h1 style=""><strong>Control Sheet For Document Approval</h1></td>
    </tr>
    <tr>
        <td><h1 style=""><strong><?php echo $row->project_name;?></strong></h1></td>
    </tr>
</table>

<div id="content">
    <div id="body">
        <div id="container" style="margin-top:8px;" >
            <table class="border">
                <tr>
                    <th width="38%">Description</th>
                    <th width="8%">Initial</th>
                    <th width="25%">Sign</th>
                    <th>Notes</th>
                </tr>
                <?php
                if($qry_workflow->num_rows() > 0) {
                    $i = 0;
                    foreach($qry_workflow->result() as $row_workflow) {
                        ?>
                        <tr>
                            <?php if($i == 0){ ?>
                            <td rowspan="<?php echo $qry_workflow->num_rows(); ?>" class="small_padding">
                                <h3><?php echo $row->project_name;?></h3>

                                <p style="text-decoration: underline;">Contents</p>
                                <table class="no_border tbl_small_padding">
                                    <tr>
                                        <td width="60px">PR</td>
                                        <td width="10px">:</td>
                                        <td><?php echo $row->pr_code;?></td>
                                    </tr>
                                    <tr>
                                        <td>PO</td>
                                        <td>:</td>
                                        <td><?php echo $row->po_code;?></td>
                                    </tr>
                                    <tr>
                                        <td>Remarks</td>
                                        <td>:</td>
                                        <td><?php echo $row->remarks;?></td>
                                    </tr>
                                    <tr>
                                        <td>Supplier</td>
                                        <td>:</td>
                                        <td><?php echo $row->supplier_name;?></td>
                                    </tr>
                                </table>
                            </td>
                            <?php } ?>
                            <td align="center"><?php echo $row_workflow->user_initial;?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>