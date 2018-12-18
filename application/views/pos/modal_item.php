<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">POS Items</h4>
</div>
<div class="modal-content-edit">
    <div class="modal-body modal-body-scroll">
        <div class="row">
            <div class="col-md-12">
                <div class="table-container">
                    <table class="table table-striped table-hover table-bordered table-po-detail" id="datatable_item">
                        <thead>
                        <tr>
                            <th class="text-center"> CODE </th>
                            <th> DESCRIPTION </th>
                            <th class="text-center"> TYPE </th>
                            <th class="text-center"> UOM </th>
                            <th class="text-right"> QTY </th>
                            <th width="14%"></th>
                        </tr>
                        <tr role="row" class="filter bg-grey-steel">
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_code"></td>
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_desc"></td>
                            <td><select name="filter_is_service" class="form-control form-filter input-sm select2me ">
                                    <option value="">All</option>
                                    <option value="A">[I]ssued Item</option>
                                    <option value="B">[S]ervice</option>
                                </select>
                            </td>
                            <td><input type="text" class="form-control form-filter input-sm" name="filter_uom"></td>
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