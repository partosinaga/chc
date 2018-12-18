<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">SO List</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <table class="table table-striped table-bordered table-hover dataTable"  id="datatable_customer">
                    <thead>
                    <tr>
                        <th> # </th>
                        <th width="15%">SO Code</th>
                        <th width="15%">SO Date</th>
                        <th width="15%">Request DO Date</th>
                        <th>Delivery address</th>
                        <th width="11%">#</th>
                    </tr>
                    <tr role="row" class="filter bg-grey-steel">
                        <td></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_code"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_date"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_req_date"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_deliv_address"></td>
                        <td>
                            <div class="text-center">
                                <button class="btn btn-sm yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                <button class="btn btn-sm red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<div class="modal-footer">

    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
</div>