<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <i class="fa fa-cube font-purple-soft"></i>
    <span class="caption-subject font-purple-soft bold uppercase">Items Inventory</span>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <div class="portlet-body">

                    <div class="tab-content">
                        <table class="table table-striped table-bordered table-hover dataTable"  id="item-datatable">
                            <thead>
                            <tr>
                                <th width="3"> # </th>
                                <th width="15%">Code</th>
                                <th>Description</th>
                                <th width=9%">UOM</th>
                                <th width=9%">Qty</th>
                                <th width=9%">Qty Remain</th>
                                <th width="11%">#</th>
                            </tr>
                            <tr role="row" class="filter bg-grey-steel">
                                <td></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="filter_code"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="filter_desc"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="filter_uom"></td>
                                <td><input type="number" class="form-control form-filter input-sm" name="filter_qty"></td>
                                <td><input type="number" class="form-control form-filter input-sm" name="filter_qty_remain"></td>
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
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
</div>