<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">PR Detail List</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <div class="table-actions-wrapper">
                    <?php if($type == 0){ ?>
                    <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit </button>
                    <?php } ?>
                </div>
                <table class="table table-striped table-hover table-bordered" id="datatable_pr_detail">
                    <thead>
                    <tr>
                        <th class="text-center"> # </th>
                        <th class="text-center"> Item Code </th>
                        <th class="text-center"> Item Description </th>
                        <th class="text-center"> UOM </th>
                        <th class="text-center"> Supplier </th>
                        <th class="text-center"> Qty </th>
                        <th class="text-center"> Qty Remain</th>
                    </tr>
                    </thead>
                    <tbody>
                </table>
            </div>
        </div>
    </div>
</div>