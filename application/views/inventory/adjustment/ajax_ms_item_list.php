<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Master Item</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <table class="table table-striped table-hover table-bordered" id="datatable_item">
                    <thead>
                    <tr>
                        <th width="10px" class="text-center"> # </th>
                        <th width="120px" class="text-center"> Item Code </th>
                        <th> Description </th>
                        <th width="90px" class="text-center"> UOM Issue </th>
                        <th width="100px" class="text-right"> On Hand Qty </th>
                        <th width="100px" class="text-right"> Unit Cost </th>
                        <th width="80px"></th>
                    </tr>
                    <tr role="row" class="filter bg-grey-steel">
                        <td></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_item_code"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_item_desc"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_uom_out_code"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_on_hand_qty"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_unit_cost"></td>
                        <td>
                            <div class="text-center">
                                <button class="btn btn-sm yellow filter-submit margin-bottom tooltips" data-original-title="Filter" data-placement="top" data-container="body"><i class="fa fa-search"></i></button>
                                <button class="btn btn-sm red filter-cancel tooltips" data-original-title="Reset" data-placement="top" data-container="body"><i class="fa fa-times"></i></button>
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