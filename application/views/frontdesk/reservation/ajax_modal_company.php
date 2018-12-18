<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Company List</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <table class="table table-striped table-hover table-bordered table-po-detail" id="datatable_company">
                    <thead>
                        <tr>
                            <th width="2%" class="text-center"> # </th>
                            <th class="text-center"> Company Name </th>
                            <th width="30%" class="text-center"> Address </th>
                            <th width="10%" class="text-center"> Phone </th>
                            <th width="10%" class="text-center"> Email </th>
                            <th width="70px"></th>
                        </tr>
                        <tr role="row" class="filter bg-grey-steel">
                            <td>&nbsp;</td>
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_name"></td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="filter_address">
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="filter_phone">
                            </td>
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_email"></td>
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