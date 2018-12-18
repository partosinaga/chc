<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Company List</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <table class="table table-striped table-hover table-bordered table-po-detail" id="datatable_modal">
                    <thead>
                        <tr>
                            <th > Company </th>
                            <th > Address </th>
                            <th width="80px"></th>
                        </tr>
                        <tr role="row" class="filter bg-grey-steel">
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_name"></td>
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_addr"></td>
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