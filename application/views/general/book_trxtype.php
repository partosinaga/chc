<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Transaction Types</h4>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-sm yellow table-trxtype-action-submit"><i class="fa fa-check"></i> Submit </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-content-edit">
<div class="modal-body modal-body-scroll">
    <div class="alert alert-danger hide modal-error-message">
        <button class="close" aria-hidden="true" data-dismiss="alert" type="button"></button>
        <i class="fa-lg fa fa-warning"></i> <span></span>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <table class="table table-striped table-hover table-bordered" id="datatable_item">
                    <thead>
                    <tr>
                        <th class="text-center"> # </th>
                        <th class="text-center"> Code </th>
                        <th> Description </th>
                        <th class="text-right"> COA </th>
                        <th class="text-center"> Stamp </th>
                        <th width="14%"></th>
                    </tr>
                    <tr role="row" class="filter bg-grey-steel">
                        <td></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_trx_code"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_trx_desc"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="filter_coa_code"></td>
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