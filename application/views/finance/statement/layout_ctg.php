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
                                    <li >
                                        <a href="<?php echo base_url('finance/statement/layout_setting/'. GLStatement::BALANCE_SHEET)?>"  >
                                            <i class="fa fa-star"></i>
                                            Layout</a>
                                    </li>
                                    <li class="active">
                                        <a href="#portlet_layout_ctg" data-toggle="tab" >
                                            <i class="fa fa-cogs"></i>
                                            Category </a>
                                    </li>
                                </ul>

                            </div>
                            <div class="portlet-body ">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="portlet_layout_ctg">
                                        <!-- Begin: life time stats -->
                                        <div class="portlet blue-madison box">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    Financial Statement Categories
                                                </div>
                                                <div class="actions">
                                                    <!--
                                                    <?php if(check_session_action(get_menu_id(), STATUS_NEW)){ ?>
                                                        <a href="<?php echo base_url('finance/statement/layout_form/0/0')?>" class="btn default grey-stripe">
                                                            <i class="fa fa-plus"></i>
                                <span class="hidden-480">
                                New Category </span>
                                                        </a>
                                                    <?php } ?>
                                                    -->
                                                </div>
                                            </div>
                                            <div class="portlet-body ">
                                                <div class="table-container">
                                                    <form method="post" action="<?php echo base_url('finance/statement/submit_layout_ctg')?>">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="text-left ">
                                                                <button type="submit" class="btn green" name="save" id="btn-submit" ><i class="fa fa-save"></i>&nbsp;Save</button>&nbsp;
                                                            </div><br/>
                                                        </div>

                                                    </div>
                                                    <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                                    <table class="table table-striped table-bordered table-hover table-po-detail" id="table_layout_ctg">
                                                        <thead>
                                                        <tr role="row" >
                                                            <th style="width:5%;">
                                                                TYPE
                                                            </th>
                                                            <th>
                                                                CATEGORY
                                                            </th>
                                                            <th>
                                                                CAPTION
                                                            </th>
                                                            <th style="width:5%;">

                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                            if(count($row_ctg) > 0){
                                                                foreach($row_ctg as $row){
                                                                    echo '<tr data-id="'. $row['category_id'] .'">';

                                                                    $reportType = 'BS';
                                                                    if($row['report_type'] == GLStatement::BALANCE_SHEET){
                                                                        $reportType = 'BS';
                                                                    }else if($row['report_type'] == GLStatement::PROFIT_LOSS){
                                                                        $reportType = 'PL';
                                                                    }else if($row['report_type'] == GLStatement::CASH_FLOW){
                                                                        $reportType = 'CF';
                                                                    }

                                                                    echo '<td style="vertical-align:middle;" class="control-label"><input type="hidden" name="f_ctg_id[]" value="' . $row['category_id'] . '">' . $reportType . '</td>
                                                                          <td style="vertical-align:middle;" class="control-label">' . $row['category_key'] . '</td>
                                                                          <td><input type="text" name="f_ctg_caption[]" value="' . $row['category_caption'] . '" class="form-control input-sm"></td>';
                                                                    echo '<td style="vertical-align:middle;"><a data-id="' . $row['category_id'] . '" data-placement="top" data-container="body" class="btn btn-xs green-meadow add_sub_ctg" href="javascript:;"><i class="fa fa-plus"></i></a></td>' ;

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

        var initTableCtg = function () {
            var table = $('#table_layout_ctg');

            /* Formatting function for row expanded details */
            function fnFormatDetails(oTable, nTr, ctgId) {
                /*
                var aData = oTable.fnGetData(nTr);
                sOut += '<tr><td>Platform(s):</td><td>' + aData[2] + '</td></tr>';
                sOut += '<tr><td>Engine version:</td><td>' + aData[3] + '</td></tr>';
                sOut += '<tr><td>CSS grade:</td><td>' + aData[4] + '</td></tr>';
                sOut += '<tr><td>Others:</td><td>Could provide a link here</td></tr>';
                */

                var sOut = '';

                $.ajax({
                    url: "<?php echo base_url('finance/statement/ajax_ctg_by_id');?>/" + ctgId
                }).done(function(ret) {
                    sOut = '<table class="col-md-8">';
                    sOut += '<thead>';
                    sOut += '<tr class="heading"><th style="width:30%;">SUB CATEGORY</th><th >CAPTION</th></tr>';
                    sOut += '</thead>';
                    sOut += '<tbody>';
                    sOut += ret;
                    sOut += '</tbody></table>';

                    //console.log('A ' + sOut);

                    oTable.fnOpen(nTr, sOut, 'details');
                });

                //console.log('B ' + sOut);
            }

            /*
             * Insert a 'details' column to the table
             */
            var nCloneTh = document.createElement('th');
            nCloneTh.className = "table-checkbox";

            var nCloneTd = document.createElement('td');
            nCloneTd.style = "vertical-align: middle;";
            nCloneTd.innerHTML = '<span class="row-details row-details-close"></span>';

            table.find('thead tr').each(function () {
                this.insertBefore(nCloneTh, this.childNodes[0]);
            });

            table.find('tbody tr').each(function () {
                this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
            });

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
                "columnDefs": [{
                    "orderable": false,
                    "targets": [0]
                }],
                "order": [
                    [1, 'asc']
                ],
                "lengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"] // change per page values here
                ],
                // set the initial value
                "pageLength": -1
            });

            var tableWrapper = $('#table_layout_ctg_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper
            var tableColumnToggler = $('#table_layout_ctg_column_toggler');

            /* modify datatable control inputs */
            tableWrapper.find('.dataTables_length select').select2(); // initialize select2 dropdown

            /* Add event listener for opening and closing details
             * Note that the indicator for showing which row is open is not controlled by DataTables,
             * rather it is done here
             */
            table.on('click', ' tbody td .row-details', function () {
                var nTr = $(this).parents('tr')[0];
                var data_id = parseInt($(this).parents('tr').attr('data-id')) || 0;
                //console.log('data-id ' + data_id);
                if (oTable.fnIsOpen(nTr)) {
                    /* This row is already open - close it */
                    $(this).addClass("row-details-close").removeClass("row-details-open");
                    oTable.fnClose(nTr);
                } else {
                    /* Open this row */
                    $(this).addClass("row-details-open").removeClass("row-details-close");
                    //oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr, data_id), 'details');
                    fnFormatDetails(oTable, nTr, data_id);
                }
            });

            /* handle show/hide columns*/
            $('input[type="checkbox"]', tableColumnToggler).change(function () {
                /* Get the DataTables object again - this is not a recreation, just a get of the object */
                var iCol = parseInt($(this).attr("data-column"));
                var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
                oTable.fnSetColumnVis(iCol, (bVis ? false : true));
            });
        }

        initTableCtg();

        $('.add_sub_ctg').live('click', function (e) {
            e.preventDefault();

            var ctg_id = $(this).attr('data-id');
            console.log('ctg ' + ctg_id);
        });

    });

</script>