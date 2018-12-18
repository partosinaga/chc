<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Chart Of Accounts (COA)</h4>
</div>
<div class="modal-content-edit">
    <div class="modal-body modal-body-scroll">
        <div class="row">
            <div class="col-md-12">
                <div class="table-container">
                    <table class="table table-striped table-hover table-bordered table-po-detail" id="datatable_coa">
                        <thead>
                        <tr>
                            <th class="text-center"> Code </th>
                            <th> Description </th>
                            <th class="text-center"> Class </th>
                            <th class="text-center"> Type </th>
                            <th class="text-right"> Balance </th>
                            <th width="14%"></th>
                        </tr>
                        <tr role="row" class="filter bg-grey-steel">
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_coacode"></td>
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_coadesc"></td>
                            <td><select name="filter_classid" class="form-control form-filter input-sm select2me ">
                                    <option value="">All</option>
                                    <?php
                                    $qry_class = $this->db->query('select * from gl_class where status <> ' . STATUS_DELETE);
                                    if($qry_class->num_rows() > 0){
                                        foreach($qry_class->result() as $row_class){
                                            echo '<option value="' . $row_class->class_id . '">' . $row_class->class_code . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td><select name="filter_classtype" class="form-control form-filter input-sm select2me">
                                    <option value="">All</option>
                                    <option value="<?php echo GLClassType::ASSET; ?>">Asset</option>
                                    <option value="<?php echo GLClassType::LIABILITY;?>">Liability</option>
                                    <option value="<?php echo GLClassType::CAPITAL;?>">Capital</option>
                                    <option value="<?php echo GLClassType::INCOME;?>">Income</option>
                                    <option value="<?php echo GLClassType::EXPENSE;?>">Expense</option>
                                </select>
                            </td>
                            <td></td>
                            <td>
                                <div class="text-center">
                                    <button class="btn btn-sm yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-sm red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
                                </div>
                            </td>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>