<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Add SRF</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <form action="#" method="post" id="form-entry-srf" class="form-horizontal" onsubmit="return false;" >
                <input type="hidden" name="unit_id" value="<?php echo $unit_id;?>" />
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label col-md-3" for="requested_by">Request By</label>
                                <div class="col-md-6">
                                    <input type="text" name="requested_by" class="form-control" value="" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" style="padding-top: 2px;">Description</label>
                                <div class="col-md-9">
                                    <textarea name="srf_note" rows="2" class="form-control" style="resize: vertical;"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-9 col-md-offset-3">
                                    <input type="submit" class="btn green-haze yellow-stripe" value="Submit" />
                                </div>
                            </div>
                        </div>
                </div>
            </form>
        </div>
    </div>
</div>