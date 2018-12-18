<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">PR List</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <table class="table table-striped table-hover table-bordered" id="datatable_pr">
                    <thead>
                        <tr>
                            <th width="1%" class="text-center"> # </th>
                            <th width="15%" class="text-center"> PR Code </th>
                            <th width="15%" class="text-center"> PR Date </th>
                            <th width="12%" class="text-center"> Dept </th>
                            <th class="text-center"> Remarks </th>
                            <th width="70px"></th>
                        </tr>
                        <tr role="row" class="filter bg-grey-steel">
                            <td></td>
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_pr_code"></td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm date-picker margin-bottom-5" readonly name="filter_pr_date_from" placeholder="From" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control form-filter input-sm date-picke" readonly name="filter_pr_date_to" placeholder="To" data-date-format="dd-mm-yyyy">
                            </td>
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_dept"></td>
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_remarks"></td>
                            <td>
                                <div class="text-center">
                                    <button class="btn btn-xs yellow filter-submit tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
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