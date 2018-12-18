<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">PR Detail List</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <div class="table-actions-wrapper">
                    <?php
                    if($type == 0){
                        echo '<button class="btn btn-sm green-haze table-group-action-submit yellow-stripe"><i class="fa fa-check"></i> Submit </button>';
                    } else {
                        echo '<button class="btn btn-sm green-haze table-action-export-excel yellow-stripe"><i class="fa fa-check"></i> Excel </button>';
                    }
                    ?>
                </div>
                <div id="export_data">
                    <table class="table table-striped table-hover table-bordered" id="datatable_pr_detail">
                        <thead>
                        <tr>
                            <th width="10px" class="text-center"> # </th>
                            <th class="text-center"> Item Description </th>
                            <th width="8%" class="text-center"> UOM </th>
                            <th width="15%" class="text-center"> Supplier </th>
                            <th width="10%" class="text-center"> Qty </th>
                            <th width="10%" class="text-center"> Qty Remain</th>
                            <th width="20%" class="text-center"> Link </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>