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
        <div class="tab-content">
            <div class="tab-pane active">
                <div class="row">
                    <div class="col-md-12 ">
                        <!-- BEGIN TAB PORTLET-->
                        <div class="box blue-ebonyclay" >
                            <div class="portlet-title blue-ebonyclay" >
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#portlet_layout_bs" data-toggle="tab" >
                                            <i class="fa fa-star"></i>
                                            Layout</a>
                                    </li>
                                    <li >
                                        <a href="<?php echo base_url('finance/statement/layout_setting/0');?>"  >
                                            <i class="fa fa-cogs"></i>
                                            Category </a>
                                    </li>
                                </ul>

                            </div>
                            <div class="portlet-body ">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="portlet_layout_bs">
                                        <!-- Begin: life time stats -->
                                        <div class="portlet blue-madison box">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    Balance Sheet
                                                </div>
                                                <div class="actions">
                                                    <div class="btn-group">
                                                        <button class="btn blue-chambray btn-xs dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
                                                            REPORT TYPE&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu">
                                                            <li><a href="javascript:;">Balance Sheet</a></li>
                                                            <li><a href="<?php echo base_url('finance/statement/layout_setting/' . GLStatement::PROFIT_LOSS)?>">Income Statement</a></li>
                                                            <li><a href="<?php echo base_url('finance/statement/layout_setting/' . GLStatement::CASH_FLOW)?>">Cash Flow</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="portlet-body ">
                                                <div class="table-container">
                                                    <form method="post" action="<?php echo base_url('finance/statement/submit_layout_bs')?>">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="text-left ">
                                                                <button type="submit" class="btn green" name="save" id="btn-submit" data-url="<?php echo base_url('cashier/bankbook/pdf_bankbook/'); ?>"><i class="fa fa-save"></i>&nbsp;Save</button>
                                                            </div><br/>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="text-right ">
                                                                <a href="<?php echo base_url('finance/statement/layout_form/' . GLStatement::BALANCE_SHEET . '/0')?>" class="btn default grey-stripe">
                                                                    <i class="fa fa-plus"></i>
                                                                    New Layout Account
                                                                </a>
                                                            </div><br/>
                                                        </div>
                                                    </div>
                                                    <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                                    <table class="table table-striped table-bordered table-hover table-po-detail" id="table_layout_bs">
                                                        <thead>
                                                        <tr role="row" >
                                                            <th >
                                                                CATEGORY
                                                            </th>
                                                            <th >
                                                                SUB CATEGORY
                                                            </th>
                                                            <th >
                                                                <span style="color:red;">CF</span>
                                                            </th>
                                                            <th>
                                                                ACCOUNT
                                                            </th>
                                                            <th >
                                                                RANGE
                                                            </th>
                                                            <th>
                                                                RANGE START
                                                            </th>
                                                            <th>
                                                                RANGE END
                                                            </th>
                                                            <th>
                                                                CUSTOM FORMULA
                                                            </th>
                                                            <th>

                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                            if(count($row_det) > 0){
                                                                foreach($row_det as $row){
                                                                    echo '<tr data-id="'. $row['detail_id'] .'">';
                                                                    echo '<td style="vertical-align:top;padding-top:5px;" class="control-label">
                                                                          <input type="hidden" name="f_detail_id[]" value="' . $row['detail_id'] . '">
                                                                          <input type="hidden" name="f_ctg_id[]" value="' . $row['category_id'] . '">' . $row['category_caption'] . '</td>
                                                                          <td style="vertical-align:top;padding-top:5px;" class="control-label">' . $row['subcategory_caption'] . '</td>';
                                                                    if($row['iscashflow'] > 0){
                                                                        echo '<td style="vertical-align:top;padding-top:5px;"><input type="checkbox" value="'. $row['detail_id'] .'" name="check_is_cf[]"  ' . 'checked="checked"' . '></td>';
                                                                    }else{
                                                                        echo '<td style="vertical-align:top;padding-top:5px;"><input type="checkbox" value="'. $row['detail_id'] .'" name="check_is_cf[]"  ' . '' . '></td>';
                                                                    }

                                                                    echo '<td><input type="text" name="f_account_name[]" value="' . $row['account_name'] . '" class="form-control input-sm"></td>';
                                                                    if($row['israngedformula'] > 0){
                                                                        echo '<td style="vertical-align:top;padding-top:5px;"><input type="checkbox" value="'. $row['detail_id'] .'" name="check_is_range[]"  ' . 'checked="checked"' . '></td>';
                                                                    }else{
                                                                        echo '<td style="vertical-align:top;padding-top:5px;"><input type="checkbox" value="'. $row['detail_id'] .'" name="check_is_range[]" ' . '' . '></td>';
                                                                    }

                                                                    echo '<td><input type="text" name="f_range_start[]" value="' . $row['rangeformula_start'] . '" class="form-control input-sm text-center"></td>
                                                                          <td><input type="text" name="f_range_end[]" value="' . $row['rangeformula_end'] . '" class="form-control input-sm text-center"></td>
                                                                          <td><textarea name="f_text_formula[]" rows="1" class="form-control input-sm" >'. $row['text_formula'] .'</textarea>';
                                                                    $exist = $this->db->query('SELECT category_id FROM gl_financestatement_subcategory WHERE category_id = ' . $row['category_id']);
                                                                    echo '<td style="vertical-align:top;padding-top:5px;"><a data-link="' . base_url('finance/statement/layout_delete/'. $row['detail_id'] . '/' . GLStatement::BALANCE_SHEET) . '" data-placement="top" data-container="body" class="btn btn-xs red-intense remove_detail" href="javascript:;"><i class="fa fa-remove"></i></a></td>' ;

                                                                    echo '</tr>';
                                                                }
                                                            }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End: life time stats -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END TAB PORTLET-->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CONTENT -->

<script>
    $(document).ready(function(){
        autosize($('textarea'));

        var initTable = function () {
            var table = $('#table_layout_bs');
            var oTable = table.dataTable({
                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": "No data available in table",
                    "infoEmpty": "No entries found",
                    "infoFiltered": "(filtered1 from _MAX_ total entries)",
                    "lengthMenu": "Show _MENU_ entries",
                    "zeroRecords": "No matching records found"
                },
                "search": false,
                "paging":false,
                "info": false,
                "filter":false,
                "lengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"] // change per page values here
                ],
                "aaSorting": [],
                "aoColumns": [
                    { "bSortable": false, "sWidth" : "10%" },
                    { "sClass": "text-center", "sWidth" : "10%" },
                    { "sClass": "text-center", "sWidth" : "4%", "bSortable": false },
                    { "sClass": "text-center", "sWidth" : "25%" },
                    { "sClass": "text-center", "sWidth" : "4%", "bSortable": false},
                    { "sClass": "text-center", "sWidth" : "7%", "bSortable": false },
                    { "sClass": "text-center", "sWidth" : "7%", "bSortable": false },
                    { "sClass": "text-left", "bSortable": false },
                    { "bSortable": false, "sClass": "text-center", "sWidth" : "4%"}
                ],
                // set the initial value
                "pageLength": -1
            });

            var tableWrapper = $('#table_layout_bs_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper
            var tableColumnToggler = $('#table_layout_bs_column_toggler');

            /* modify datatable control inputs */
            tableWrapper.find('.dataTables_length select').select2(); // initialize select2 dropdown

        }

        initTable();
    });

    $('.remove_detail').click(function(e){
        e.preventDefault();

        var data_link = $(this).attr("data-link");
        bootbox.confirm("Are you sure?", function(result) {
            if (result === true) {
                //console.log('delete ' + data_id);
                window.location.assign(data_link);
            }
        })
    });

</script>