<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Confirm Housekeeping Status</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <form method="post" id="form-input" class="form-horizontal" onsubmit="return false">
                <input type="hidden" name="unit_id" value="<?php echo $unit_id;?>" />
                <input type="hidden" name="unit_code" value="<?php echo $row->unit_code;?>" />
                <input type="hidden" name="hsk_status" value="<?php echo $row->hsk_status;?>" />
                <div class="form-group">
                    <label class="control-label col-md-3">Unit</label>
                    <div class="col-md-5" style="padding-top: 5px;font-size: 12pt;">
                        <span class="control-label bold font-blue" ><?php echo $row->unit_code;?></span>
                    </div>
                </div>
                <div class="form-group" >
                    <label class="control-label col-md-3">Current</label>
                    <div class="col-md-8" style="padding-top: 9px;">
                        <span class="control-label" ><?php echo $row->hsk_status . ' (' . HSK_STATUS::caption($row->hsk_status) . ')';?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 bold font-red-sunglo">Change To</label>
                    <div class="col-md-8">
                        <select class="form-control select2me" name="next_hsk_status">
                            <?php if($row->hsk_status == HSK_STATUS::VC){ ?>
                                <option value="<?php echo HSK_STATUS::VD;?>"><?php echo HSK_STATUS::VD . ' (' . HSK_STATUS::caption(HSK_STATUS::VD) . ')';?></option>
                            <?php } ?>
                            <option value="<?php echo HSK_STATUS::next_status($row->hsk_status);?>"><?php echo HSK_STATUS::next_status($row->hsk_status) . ' (' . HSK_STATUS::caption(HSK_STATUS::next_status($row->hsk_status)) . ')';?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Note</label>
                    <div class="col-md-8">
                        <textarea class="form-control " rows="2" name="hsk_remark"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3"></label>
                    <div class="col-md-5">
                        <button class="btn yellow-stripe green-meadow" type="button" id="btn_hsk_change">
                            <i class="fa fa-save"></i>
                            Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>