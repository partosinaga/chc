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
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Posting Parameter
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                <?php
                    $hasEdit = check_session_action(get_menu_id(), STATUS_EDIT);
                    echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));

                    $qry_coa = $this->db->query('SELECT * FROM gl_coa WHERE is_display > 0 AND status NOT IN(' . STATUS_DELETE . ',' . STATUS_INACTIVE . ') ORDER BY coa_code;');

                ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <td style="width:40%">
                            Feature
                        </td>
                        <td >
                            Trx Type
                        </td>
                        <td>
                            COA
                        </td>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="3" class="bg-grey-cascade bold"><i class="fa fa-lock"></i> CLOSING ONLY</td>
                    </tr>
                    <tr>
                        <td >
                            <span>Close Month</span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::CLOSE_MONTH; ?>" spec-id ="<?php echo isset($row[FNSpec::CLOSE_MONTH]) ? $row[FNSpec::CLOSE_MONTH]->id :''; ?>" data-type="text" data-desc="Closing Account Monthly">
                                <?php echo isset($row[FNSpec::CLOSE_MONTH]) ? (($row[FNSpec::CLOSE_MONTH]->coa_id > 0) ? $row[FNSpec::CLOSE_MONTH]->coa_code . ' - ' . $row[FNSpec::CLOSE_MONTH]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span>Close Year</span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::CLOSE_YEAR; ?>" spec-id ="<?php echo isset($row[FNSpec::CLOSE_YEAR]) ? $row[FNSpec::CLOSE_YEAR]->id :''; ?>" data-type="text" data-desc="Closing Account Yearly">
                                <?php echo isset($row[FNSpec::CLOSE_YEAR]) ? (($row[FNSpec::CLOSE_YEAR]->coa_id > 0) ? $row[FNSpec::CLOSE_YEAR]->coa_code . ' - ' . $row[FNSpec::CLOSE_YEAR]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="bg-grey-cascade bold"><i class="fa fa-cogs"></i> AR</td>
                    </tr>
                    <!-- tr>
                        <td >
                            Trade Receivable - Personal
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::TRADE_RECEIVABLES; ?>" spec-id ="<?php echo isset($row[FNSpec::TRADE_RECEIVABLES]) ? $row[FNSpec::TRADE_RECEIVABLES]->id :''; ?>" data-type="text" data-desc="Account Receivable">
                                <?php echo isset($row[FNSpec::TRADE_RECEIVABLES]) ? (($row[FNSpec::TRADE_RECEIVABLES]->coa_id > 0) ? $row[FNSpec::TRADE_RECEIVABLES]->coa_code . ' - ' . $row[FNSpec::TRADE_RECEIVABLES]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr -->
                    <tr>
                        <td >
                            Trade Receivable
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::TRADE_RECEIVABLES_CORP; ?>" spec-id ="<?php echo isset($row[FNSpec::TRADE_RECEIVABLES_CORP]) ? $row[FNSpec::TRADE_RECEIVABLES_CORP]->id :''; ?>" data-type="text" data-desc="Account Receivable">
                                <?php echo isset($row[FNSpec::TRADE_RECEIVABLES_CORP]) ? (($row[FNSpec::TRADE_RECEIVABLES_CORP]->coa_id > 0) ? $row[FNSpec::TRADE_RECEIVABLES_CORP]->coa_code . ' - ' . $row[FNSpec::TRADE_RECEIVABLES_CORP]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <!--tr>
                        <td >
                            Unearned Room Sales / Bridging <i class="font-red-sunglo">(on Check In)</i>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::SALES_BRIDGING_ACCOUNT; ?>" spec-id ="<?php echo isset($row[FNSpec::SALES_BRIDGING_ACCOUNT]) ? $row[FNSpec::SALES_BRIDGING_ACCOUNT]->id :''; ?>" data-type="text" data-desc="Sales Bridging">
                                <?php echo isset($row[FNSpec::SALES_BRIDGING_ACCOUNT]) ? (($row[FNSpec::SALES_BRIDGING_ACCOUNT]->coa_id > 0) ? $row[FNSpec::SALES_BRIDGING_ACCOUNT]->coa_code . ' - ' . $row[FNSpec::SALES_BRIDGING_ACCOUNT]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr -->
                    <tr>
                        <td >
                            Other Charge Sales
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_trxtype editable editable-click" spec-key="<?php echo FNSpec::SALES_RESERVATION; ?>" spec-id ="<?php echo isset($row[FNSpec::SALES_RESERVATION]) ? $row[FNSpec::SALES_RESERVATION]->id :''; ?>" data-type="text" data-desc="Reservation Sales">
                                <?php echo isset($row[FNSpec::SALES_RESERVATION]) ? (($row[FNSpec::SALES_RESERVATION]->transtype_id > 0) ? $row[FNSpec::SALES_RESERVATION]->transtype_name . ' - ' . $row[FNSpec::SALES_RESERVATION]->transtype_desc : '---') :'---'; ?> </a>
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::SALES_RESERVATION; ?>" spec-id ="<?php echo isset($row[FNSpec::SALES_RESERVATION]) ? $row[FNSpec::SALES_RESERVATION]->id :''; ?>" data-type="text" data-desc="Reservation Sales">
                                <?php echo isset($row[FNSpec::SALES_RESERVATION]) ? (($row[FNSpec::SALES_RESERVATION]->coa_id > 0) ? $row[FNSpec::SALES_RESERVATION]->coa_code . ' - ' . $row[FNSpec::SALES_RESERVATION]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <!-- tr>
                        <td >
                            Housekeeping Sales
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_trxtype editable editable-click" spec-key="<?php echo FNSpec::SALES_HOUSEKEEPING; ?>" spec-id ="<?php echo isset($row[FNSpec::SALES_HOUSEKEEPING]) ? $row[FNSpec::SALES_HOUSEKEEPING]->id :''; ?>" data-type="text" data-desc="Housekeeping Sales">
                                <?php echo isset($row[FNSpec::SALES_HOUSEKEEPING]) ? (($row[FNSpec::SALES_HOUSEKEEPING]->transtype_id > 0) ? $row[FNSpec::SALES_HOUSEKEEPING]->transtype_name . ' - ' . $row[FNSpec::SALES_HOUSEKEEPING]->transtype_desc : '---') :'---'; ?> </a>
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::SALES_HOUSEKEEPING; ?>" spec-id ="<?php echo isset($row[FNSpec::SALES_HOUSEKEEPING]) ? $row[FNSpec::SALES_HOUSEKEEPING]->id :''; ?>" data-type="text" data-desc="Housekeeping Sales">
                                <?php echo isset($row[FNSpec::SALES_HOUSEKEEPING]) ? (($row[FNSpec::SALES_HOUSEKEEPING]->coa_id > 0) ? $row[FNSpec::SALES_HOUSEKEEPING]->coa_code . ' - ' . $row[FNSpec::SALES_HOUSEKEEPING]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr -->
                    <!--tr>
                        <td >
                            Credit Card
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::CREDIT_CARD; ?>" spec-id ="<?php echo isset($row[FNSpec::CREDIT_CARD]) ? $row[FNSpec::CREDIT_CARD]->id :''; ?>" data-type="text" data-desc="Credit Card">
                                <?php echo isset($row[FNSpec::CREDIT_CARD]) ? (($row[FNSpec::CREDIT_CARD]->coa_id > 0) ? $row[FNSpec::CREDIT_CARD]->coa_code . ' - ' . $row[FNSpec::CREDIT_CARD]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    < tr>
                        <td >
                            Payment Gateway Charge (VT)
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::PAYMENT_GATEWAY_CHARGE; ?>" spec-id ="<?php echo isset($row[FNSpec::PAYMENT_GATEWAY_CHARGE]) ? $row[FNSpec::PAYMENT_GATEWAY_CHARGE]->id :''; ?>" data-type="text" data-desc="Payment Gateway">
                                <?php echo isset($row[FNSpec::PAYMENT_GATEWAY_CHARGE]) ? (($row[FNSpec::PAYMENT_GATEWAY_CHARGE]->coa_id > 0) ? $row[FNSpec::PAYMENT_GATEWAY_CHARGE]->coa_code . ' - ' . $row[FNSpec::PAYMENT_GATEWAY_CHARGE]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr -->
                    <tr>
                        <td >
                            Tax
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::TAX_VAT; ?>" spec-id ="<?php echo isset($row[FNSpec::TAX_VAT]) ? $row[FNSpec::TAX_VAT]->id :''; ?>" data-type="text" data-desc="Tax VAT">
                                <?php echo isset($row[FNSpec::TAX_VAT]) ? (($row[FNSpec::TAX_VAT]->coa_id > 0) ? $row[FNSpec::TAX_VAT]->coa_code . ' - ' . $row[FNSpec::TAX_VAT]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            Bank Charge
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_trxtype editable editable-click" spec-key="<?php echo FNSpec::BANK_CHARGE; ?>" spec-id ="<?php echo isset($row[FNSpec::BANK_CHARGE]) ? $row[FNSpec::BANK_CHARGE]->id :''; ?>" data-type="text" data-desc="Stamp Duty">
                                <?php echo isset($row[FNSpec::BANK_CHARGE]) ? (($row[FNSpec::BANK_CHARGE]->transtype_id > 0) ? $row[FNSpec::BANK_CHARGE]->transtype_name . ' - ' . $row[FNSpec::BANK_CHARGE]->transtype_desc : '---') :'---'; ?> </a>
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::BANK_CHARGE; ?>" spec-id ="<?php echo isset($row[FNSpec::BANK_CHARGE]) ? $row[FNSpec::BANK_CHARGE]->id :''; ?>" data-type="text" data-desc="Bank Charge">
                                <?php echo isset($row[FNSpec::BANK_CHARGE]) ? (($row[FNSpec::BANK_CHARGE]->coa_id > 0) ? $row[FNSpec::BANK_CHARGE]->coa_code . ' - ' . $row[FNSpec::BANK_CHARGE]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            Late Penalty Interest
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_trxtype editable editable-click" spec-key="<?php echo FNSpec::INTEREST; ?>" spec-id ="<?php echo isset($row[FNSpec::INTEREST]) ? $row[FNSpec::INTEREST]->id :''; ?>" data-type="text" data-desc="Penalty">
                                <?php echo isset($row[FNSpec::INTEREST]) ? (($row[FNSpec::INTEREST]->transtype_id > 0) ? $row[FNSpec::INTEREST]->transtype_name . ' - ' . $row[FNSpec::INTEREST]->transtype_desc : '---') :'---'; ?> </a>
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::INTEREST; ?>" spec-id ="<?php echo isset($row[FNSpec::INTEREST]) ? $row[FNSpec::INTEREST]->id :''; ?>" data-type="text" data-desc="Penalty">
                                <?php echo isset($row[FNSpec::INTEREST]) ? (($row[FNSpec::INTEREST]->coa_id > 0) ? $row[FNSpec::INTEREST]->coa_code . ' - ' . $row[FNSpec::INTEREST]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            Stamp Duty
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_trxtype editable editable-click" spec-key="<?php echo FNSpec::STAMP_DUTY; ?>" spec-id ="<?php echo isset($row[FNSpec::STAMP_DUTY]) ? $row[FNSpec::STAMP_DUTY]->id :''; ?>" data-type="text" data-desc="Stamp Duty">
                                <?php echo isset($row[FNSpec::STAMP_DUTY]) ? (($row[FNSpec::STAMP_DUTY]->transtype_id > 0) ? $row[FNSpec::STAMP_DUTY]->transtype_name . ' - ' . $row[FNSpec::STAMP_DUTY]->transtype_desc : '---') :'---'; ?> </a>
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::STAMP_DUTY; ?>" spec-id ="<?php echo isset($row[FNSpec::STAMP_DUTY]) ? $row[FNSpec::STAMP_DUTY]->id :''; ?>" data-type="text" data-desc="Stamp Duty">
                                <?php echo isset($row[FNSpec::STAMP_DUTY]) ? (($row[FNSpec::STAMP_DUTY]->coa_id > 0) ? $row[FNSpec::STAMP_DUTY]->coa_code . ' - ' . $row[FNSpec::STAMP_DUTY]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            Item Transit
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::STAMP_DUTY; ?>" spec-id ="<?php echo isset($row[FNSpec::STAMP_DUTY]) ? $row[FNSpec::STAMP_DUTY]->id :''; ?>" data-type="text" data-desc="Item Intransit">
                                <?php echo isset($row[FNSpec::ITEM_TRANSIT]) ? (($row[FNSpec::ITEM_TRANSIT]->coa_id > 0) ? $row[FNSpec::ITEM_TRANSIT]->coa_code . ' - ' . $row[FNSpec::ITEM_TRANSIT]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            Unearned Income
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::STAMP_DUTY; ?>" spec-id ="<?php echo isset($row[FNSpec::STAMP_DUTY]) ? $row[FNSpec::STAMP_DUTY]->id :''; ?>" data-type="text" data-desc="Item Intransit">
                                <?php echo isset($row[FNSpec::UNEARNED_INCOME]) ? (($row[FNSpec::UNEARNED_INCOME]->coa_id > 0) ? $row[FNSpec::UNEARNED_INCOME]->coa_code . ' - ' . $row[FNSpec::UNEARNED_INCOME]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="bg-grey-cascade bold"><i class="fa fa-lock"></i> AP</td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::FIN_AP_INVOICE]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::FIN_AP_INVOICE; ?>" spec-id ="<?php echo isset($row[FNSpec::FIN_AP_INVOICE]) ? $row[FNSpec::FIN_AP_INVOICE]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::FIN_AP_INVOICE]->description;?>">
                                <?php echo isset($row[FNSpec::FIN_AP_INVOICE]) ? (($row[FNSpec::FIN_AP_INVOICE]->coa_id > 0) ? $row[FNSpec::FIN_AP_INVOICE]->coa_code . ' - ' . $row[FNSpec::FIN_AP_INVOICE]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::FIN_AP_GRN]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::FIN_AP_GRN; ?>" spec-id ="<?php echo isset($row[FNSpec::FIN_AP_GRN]) ? $row[FNSpec::FIN_AP_GRN]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::FIN_AP_GRN]->description;?>">
                                <?php echo isset($row[FNSpec::FIN_AP_GRN]) ? (($row[FNSpec::FIN_AP_GRN]->coa_id > 0) ? $row[FNSpec::FIN_AP_GRN]->coa_code . ' - ' . $row[FNSpec::FIN_AP_GRN]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::FIN_AP_PREPAID_TAX]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::FIN_AP_PREPAID_TAX; ?>" spec-id ="<?php echo isset($row[FNSpec::FIN_AP_PREPAID_TAX]) ? $row[FNSpec::FIN_AP_PREPAID_TAX]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::FIN_AP_PREPAID_TAX]->description;?>">
                                <?php echo isset($row[FNSpec::FIN_AP_PREPAID_TAX]) ? (($row[FNSpec::FIN_AP_PREPAID_TAX]->coa_id > 0) ? $row[FNSpec::FIN_AP_PREPAID_TAX]->coa_code . ' - ' . $row[FNSpec::FIN_AP_PREPAID_TAX]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::FIN_AP_VAT_IN_EXP]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::FIN_AP_VAT_IN_EXP; ?>" spec-id ="<?php echo isset($row[FNSpec::FIN_AP_VAT_IN_EXP]) ? $row[FNSpec::FIN_AP_VAT_IN_EXP]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::FIN_AP_VAT_IN_EXP]->description;?>">
                                <?php echo isset($row[FNSpec::FIN_AP_VAT_IN_EXP]) ? (($row[FNSpec::FIN_AP_VAT_IN_EXP]->coa_id > 0) ? $row[FNSpec::FIN_AP_VAT_IN_EXP]->coa_code . ' - ' . $row[FNSpec::FIN_AP_VAT_IN_EXP]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::FIN_AP_DEBIT_NOTE]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::FIN_AP_DEBIT_NOTE; ?>" spec-id ="<?php echo isset($row[FNSpec::FIN_AP_DEBIT_NOTE]) ? $row[FNSpec::FIN_AP_DEBIT_NOTE]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::FIN_AP_DEBIT_NOTE]->description;?>">
                                <?php echo isset($row[FNSpec::FIN_AP_DEBIT_NOTE]) ? (($row[FNSpec::FIN_AP_DEBIT_NOTE]->coa_id > 0) ? $row[FNSpec::FIN_AP_DEBIT_NOTE]->coa_code . ' - ' . $row[FNSpec::FIN_AP_DEBIT_NOTE]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::FIN_AP_CREDIT_NOTE]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::FIN_AP_CREDIT_NOTE; ?>" spec-id ="<?php echo isset($row[FNSpec::FIN_AP_CREDIT_NOTE]) ? $row[FNSpec::FIN_AP_CREDIT_NOTE]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::FIN_AP_CREDIT_NOTE]->description;?>">
                                <?php echo isset($row[FNSpec::FIN_AP_CREDIT_NOTE]) ? (($row[FNSpec::FIN_AP_CREDIT_NOTE]->coa_id > 0) ? $row[FNSpec::FIN_AP_CREDIT_NOTE]->coa_code . ' - ' . $row[FNSpec::FIN_AP_CREDIT_NOTE]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::FIN_AP_FOREX_GAIN]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::FIN_AP_FOREX_GAIN; ?>" spec-id ="<?php echo isset($row[FNSpec::FIN_AP_FOREX_GAIN]) ? $row[FNSpec::FIN_AP_FOREX_GAIN]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::FIN_AP_FOREX_GAIN]->description;?>">
                                <?php echo isset($row[FNSpec::FIN_AP_FOREX_GAIN]) ? (($row[FNSpec::FIN_AP_FOREX_GAIN]->coa_id > 0) ? $row[FNSpec::FIN_AP_FOREX_GAIN]->coa_code . ' - ' . $row[FNSpec::FIN_AP_FOREX_GAIN]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::FIN_AP_PAYMENT_ADV]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::FIN_AP_PAYMENT_ADV; ?>" spec-id ="<?php echo isset($row[FNSpec::FIN_AP_PAYMENT_ADV]) ? $row[FNSpec::FIN_AP_PAYMENT_ADV]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::FIN_AP_PAYMENT_ADV]->description;?>">
                                <?php echo isset($row[FNSpec::FIN_AP_PAYMENT_ADV]) ? (($row[FNSpec::FIN_AP_PAYMENT_ADV]->coa_id > 0) ? $row[FNSpec::FIN_AP_PAYMENT_ADV]->coa_code . ' - ' . $row[FNSpec::FIN_AP_PAYMENT_ADV]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::FIN_AP_STOCK_ADJUSTMENT]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::FIN_AP_STOCK_ADJUSTMENT; ?>" spec-id ="<?php echo isset($row[FNSpec::FIN_AP_STOCK_ADJUSTMENT]) ? $row[FNSpec::FIN_AP_STOCK_ADJUSTMENT]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::FIN_AP_STOCK_ADJUSTMENT]->description;?>">
                                <?php echo isset($row[FNSpec::FIN_AP_STOCK_ADJUSTMENT]) ? (($row[FNSpec::FIN_AP_STOCK_ADJUSTMENT]->coa_id > 0) ? $row[FNSpec::FIN_AP_STOCK_ADJUSTMENT]->coa_code . ' - ' . $row[FNSpec::FIN_AP_STOCK_ADJUSTMENT]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::POS_SUPPLIES]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::POS_SUPPLIES; ?>" spec-id ="<?php echo isset($row[FNSpec::POS_SUPPLIES]) ? $row[FNSpec::POS_SUPPLIES]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::POS_SUPPLIES]->description;?>">
                                <?php echo isset($row[FNSpec::POS_SUPPLIES]) ? (($row[FNSpec::POS_SUPPLIES]->coa_id > 0) ? $row[FNSpec::POS_SUPPLIES]->coa_code . ' - ' . $row[FNSpec::POS_SUPPLIES]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <span><?php echo $row[FNSpec::POS_EXPENSE]->description;?></span>
                        </td>
                        <td >
                            ---
                        </td>
                        <td >
                            <a href="javascript:;"  class="get_coa editable editable-click" spec-key="<?php echo FNSpec::POS_EXPENSE; ?>" spec-id ="<?php echo isset($row[FNSpec::POS_EXPENSE]) ? $row[FNSpec::POS_EXPENSE]->id :''; ?>" data-type="text" data-desc="<?php echo $row[FNSpec::POS_EXPENSE]->description;?>">
                                <?php echo isset($row[FNSpec::POS_EXPENSE]) ? (($row[FNSpec::POS_EXPENSE]->coa_id > 0) ? $row[FNSpec::POS_EXPENSE]->coa_code . ' - ' . $row[FNSpec::POS_EXPENSE]->coa_desc : '---') : '---'; ?> </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal-trx" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>
<input id="modal_data_id" type="hidden" value="" readonly >
<input id="modal_data_code" type="hidden" value="" readonly >
<input id="modal_data_desc" type="hidden" value="" readonly >

<script>

    $(document).ready(function(){
        var grid_trx = new Datatable();
        var $modal = $('#ajax-modal-trx');

        //Trx Type
        var handleTableTrx = function (specid, speckey, desc) {
            // Start Datatable Item
            grid_trx.init({
                src: $("#datatable_item"),
                onSuccess: function (grid_trx) {
                    // execute some code after table records loaded
                },
                onError: function (grid_trx) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_trx) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                    // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                    // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                    // So when dropdowns used the scrollable div should be removed.
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "bSortable": false, "sClass": "text-center","sWidth" : '5%' },
                        { "sClass": "text-center","sWidth" : '5%' },
                        null,
                        { "sClass": "text-center", "sWidth" : '7%' },
                        { "sClass": "text-center","sWidth" : '4%' },
                        { "bSortable": false }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [-1],
                        ["All"] // change per page values here
                    ],
                    "pageLength": -1, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('finance/setup/json_trxtype');?>"
                    }
                }
            });

            var tableWrapper = $("#datatable_item_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            // End Datatable Item

        }

        $('.get_trxtype').live('click', function (e) {
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            var specid = $(this).attr('spec-id');
            var speckey = $(this).attr('spec-key');
            var desc = $(this).attr('data-desc');

            $('body').modalmanager('loading');

            var uninitialized = $('#datatable_item').filter(function() {
                return !$.fn.DataTable.fnIsDataTable(this);
            });

            setTimeout(function(){
                $modal.load('<?php echo base_url('finance/setup/book_trxtype');?>.tpd', '', function(){
                    $modal.modal();
                    handleTableTrx(specid, speckey, desc);

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if($modal.hasClass('modal-overflow') === false){
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});

                    handleSubmitTrx(specid, speckey, desc);
                });
            }, 150);

        });

        var handleSubmitTrx = function (specid, speckey, desc) {
            $('.table-trxtype-action-submit').live('click', function (e) {
                console.log('handleSubmitTrx ...');
                e.preventDefault();
                grid_trx.src = $("#datatable_item");
                if (grid_trx.getSelectedRowsCountRadio() > 0) {

                    var result = grid_trx.getSelectedRowsRadio();
                    //console.log(result);
                    $('#modal_data_id').val(result[0]);
                    $('#modal_data_code').val(result[1]);
                    $('#modal_data_desc').val(result[2]);

                    //console.log('spec id : ' + spec_id + ', key : '. speckey);

                    //Perform Save here
                    saveTrx(specid, speckey, result[0], desc);

                    $('.get_trxtype[spec-key=\'' + speckey + '\']').html(result[1] + ' - ' + result[2]);

                    $('#ajax-modal-trx').modal('hide');

                    window.location.assign('<?php echo base_url('finance/setup/posting_manage/1.tpd'); ?>');
                } else if (grid_trx.getSelectedRowsCountRadio() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: grid_trx.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            });
        };

        //COA
        var handleTableCOA = function (specid, speckey, desc) {
            // Start Datatable Item
            grid_trx.init({
                src: $("#datatable_coa"),
                onSuccess: function (grid_trx) {
                    // execute some code after table records loaded
                },
                onError: function (grid_trx) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_trx) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                    // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                    // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                    // So when dropdowns used the scrollable div should be removed.
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "bSortable": false, "sClass": "text-center", "sWidth" : '5%' },
                        { "sClass": "text-center", "sWidth" : '7%' },
                        null,
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center", "sWidth" : '6%' },
                        { "bSortable": false }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [-1],
                        ["All"] // change per page values here
                    ],
                    "pageLength": -1, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('finance/setup/json_coa');?>"
                    }
                }
            });

            var tableWrapper = $("#datatable_coa_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('.get_coa').live('click', function (e) {
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            var specid = $(this).attr('spec-id');
            var speckey = $(this).attr('spec-key');
            var desc = $(this).attr('data-desc');

            $('body').modalmanager('loading');

            var uninitialized = $('#datatable_coa').filter(function() {
                return !$.fn.DataTable.fnIsDataTable(this);
            });

            setTimeout(function(){
                $modal.load('<?php echo base_url('finance/setup/book_coa');?>.tpd', '', function(){
                    $modal.modal();
                    handleTableCOA(specid, speckey, desc);

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if($modal.hasClass('modal-overflow') === false){
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});

                    handleSubmitCOA(specid, speckey, desc);
                });
            }, 150);

        });

        var handleSubmitCOA = function (specid, speckey, desc) {
            $('.table-group-action-submit').live('click', function (e) {
                e.preventDefault();
                grid_trx.src = $("#datatable_coa");
                if (grid_trx.getSelectedRowsCountRadio() > 0) {
                    var result = grid_trx.getSelectedRowsRadio();
                    //console.log(result);
                    $('#modal_data_id').val(result[0]);
                    $('#modal_data_code').val(result[1]);
                    $('#modal_data_desc').val(result[2]);

                    //console.log('spec id : ' + spec_id + ', key : '. speckey);

                    //Perform Save here
                    saveCOA(specid, speckey, result[0], desc);

                    console.log('handleSubmitCOA ... ' + speckey);

                    $('.get_coa[spec-key="' + speckey + '"]').html(result[1] + ' - ' + result[2]);

                    $('#ajax-modal-trx').modal('hide');

                    window.location.assign('<?php echo base_url('finance/setup/posting_manage/1.tpd'); ?>');

                } else if (grid_trx.getSelectedRowsCountRadio() === 0) {
                    Metronic.alert({
                        type: 'danger',
                        icon: 'warning',
                        message: 'No record selected',
                        container: grid_trx.getTableWrapper(),
                        place: 'prepend'
                    });
                }
            })
        }
    });

    function saveTrx(specid, speckey, transtype_id, desc){
        if($('.modal-error-message').hasClass('hide')){} else {
            $('.modal-error-message').addClass('hide');
        }

        var is_valid = false;

        var spec_id = parseInt(specid) || 0;
        var spec_key = parseInt(speckey) || 0;
        var trx_id = parseInt(transtype_id) || 0;

        if(trx_id <= 0){
            $('.modal-error-message').find('span').html('Please select Trx Type.');
            $('.modal-error-message').removeClass('hide');
        }
        else if(spec_key == 0){
            $('.modal-error-message').find('span').html('ERROR.');
            $('.modal-error-message').removeClass('hide');
        }
        else {
            is_valid = true;
        }

        if(is_valid){
            Metronic.blockUI({
                target: '.modal-content-edit',
                boxed: true,
                message: 'Processing...'
            });

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('finance/setup/submit_fnspec');?>",
                dataType: "json",
                async:false,
                data: { spec_id: spec_id,spec_key:spec_key, transtype_id: trx_id, coa_id : 0, description : desc}
            })
                .done(function( msg ) {
                    if(msg.type == '1' || msg.type == '2'){
                        Metronic.unblockUI('.modal-content-edit');
                        $('#ajax-modal').modal('hide');

                        //grid.getDataTable().ajax.reload();
                        //grid.clearAjaxParams();

                        toastr.options = {
                            "closeButton": true,
                            "debug": false,
                            "positionClass": "toast-bottom-right",
                            "onclick": null,
                            "showDuration": "1000",
                            "hideDuration": "1000",
                            "timeOut": "2000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        }
                        if(msg.type == '1'){
                            toastr["success"](msg.message, "Success");
                        }
                        else {
                            toastr["success"](msg.message, "Success");
                        }
                    }
                    else if(msg.type == '0'){
                        Metronic.unblockUI('.modal-content-edit');
                        $('.modal-error-message').find('span').html(msg.message);
                        $('.modal-error-message').removeClass('hide');

                    }
                    else {
                        Metronic.unblockUI('.modal-content-edit');
                        $('.modal-error-message').find('span').html('Error! Something has wrong, please try again.');
                        $('.modal-error-message').removeClass('hide');
                    }
                });
        }
    }

    function saveCOA(specid, speckey, coaid, desc){
        if($('.modal-error-message').hasClass('hide')){} else {
            $('.modal-error-message').addClass('hide');
        }

        var is_valid = false;

        var spec_id = parseInt(specid) || 0;
        var spec_key = parseInt(speckey) || 0;
        var coa_id = parseInt(coaid) || 0;

        if(coa_id <= 0){
            $('.modal-error-message').find('span').html('Please select COA.');
            $('.modal-error-message').removeClass('hide');
        }
        else if(spec_key == 0){
            $('.modal-error-message').find('span').html('ERROR.');
            $('.modal-error-message').removeClass('hide');
        }
        else {
            is_valid = true;
        }

        if(is_valid){
            Metronic.blockUI({
                target: '.modal-content-edit',
                boxed: true,
                message: 'Processing...'
            });

            $.ajax({
                type: "POST",
                url: "<?php echo base_url('finance/setup/submit_fnspec');?>",
                dataType: "json",
                async:false,
                data: { spec_id: spec_id,spec_key:spec_key, coa_id: coa_id, transtype_id : 0, description : desc}
            })
                .done(function( msg ) {
                    if(msg.type == '1' || msg.type == '2'){
                        Metronic.unblockUI('.modal-content-edit');
                        $('#ajax-modal').modal('hide');

                        //grid.getDataTable().ajax.reload();
                        //grid.clearAjaxParams();

                        toastr.options = {
                            "closeButton": true,
                            "debug": false,
                            "positionClass": "toast-bottom-right",
                            "onclick": null,
                            "showDuration": "1000",
                            "hideDuration": "1000",
                            "timeOut": "2000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        }
                        if(msg.type == '1'){
                            toastr["success"](msg.message, "Success");
                        }
                        else {
                            toastr["success"](msg.message, "Success");
                        }
                    }
                    else if(msg.type == '0'){
                        Metronic.unblockUI('.modal-content-edit');
                        $('.modal-error-message').find('span').html(msg.message);
                        $('.modal-error-message').removeClass('hide');

                    }
                    else {
                        Metronic.unblockUI('.modal-content-edit');
                        $('.modal-error-message').find('span').html('Error! Something has wrong, please try again.');
                        $('.modal-error-message').removeClass('hide');
                    }
                });
        }
    }

</script>

