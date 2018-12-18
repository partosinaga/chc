<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">GRN Detail List</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <div class="table-actions-wrapper">
                    <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit </button>
                </div>
                <table class="table table-striped table-hover table-bordered" id="datatable_grn_detail">
                    <thead>
                    <tr>
                        <th class="text-center"> # </th>
                        <th class="text-center"> PO Code </th>
                        <th class="text-center"> Item Code </th>
                        <th class="text-center"> Item Description </th>
                        <th class="text-center"> UOM </th>
                        <th class="text-center"> GRN Qty </th>
                        <th width="14%"></th>
                    </tr>
                    <tr role="row" class="filter bg-grey-steel">
                        <td></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_po_code"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_item_code"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_item_desc"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_uom"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_grn_qty"></td>
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