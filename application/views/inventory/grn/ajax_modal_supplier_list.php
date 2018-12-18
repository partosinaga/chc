<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Supplier List</h4>
    <!--div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit </button>
            </div>
        </div>
    </div-->
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <table class="table table-striped table-hover table-bordered" id="datatable_supplier">
                    <thead>
                    <tr>
                        <th class="text-center" width="20px"> # </th>
                        <th class="text-center"  width="180px"> Supplier Name </th>
                        <th class="text-center"> Address </th>
                        <th class="text-center" width="120px"> City </th>
                        <th class="text-center" width="120px"> Phone </th>
                        <th width="90px"></th>
                    </tr>
                    <tr role="row" class="filter bg-grey-steel">
                        <td></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_supplier_name"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_address"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_city"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_phone"></td>
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