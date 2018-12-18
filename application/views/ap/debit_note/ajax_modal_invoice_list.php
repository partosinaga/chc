<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Invoice List</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <table class="table table-striped table-hover table-bordered" id="datatable_inv">
                    <thead>
                    <tr>
                        <th width="10px" class="text-center"> # </th>
                        <th width="90px" class="text-center"> Inv Code </th>
                        <th width="100px" class="text-center"> Inv Date </th>
                        <th width="110px" class="text-center"> Reff No </th>
                        <th width="60px" class="text-center"> Curr </th>
                        <th width="60px" class="text-center"> Rate </th>
                        <th width="110px" class="text-center"> Amount </th>
                        <th class="text-center"> Remarks </th>
                        <th width="80px" class="text-center"></th>
                    </tr>
                    <tr role="row" class="filter bg-grey-steel">
                        <td></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_inv_code"></td>
                        <td>
                            <input type="text" class="form-control form-filter input-sm date date-picker margin-bottom-5" readonly name="filter_inv_date_from" placeholder="From" data-date-format="dd-mm-yyyy">
                            <input type="text" class="form-control form-filter input-sm date date-picker" readonly name="filter_inv_date_to" placeholder="From" data-date-format="dd-mm-yyyy">
                        </td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_reff_no"></td>
                        <td>
                            <select class="form-control form-filter input-sm select2me" name="filter_curr">
                                <option value="">All</option>
                                <?php
                                $qry_curr = $this->db->get('currencytype');
                                foreach ($qry_curr->result() as $row_curr) {
                                    echo '<option value="' . $row_curr->currencytype_id . '">' . $row_curr->currencytype_code . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td></td>
                        <td><input type="text" class="form-control form-filter input-sm mask_currency" name="filter_reff_amount"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_remarks"></td>
                        <td>
                            <div class="text-center">
                                <button class="btn btn-xs yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                <button class="btn btn-xs red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>