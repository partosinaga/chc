<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Master Item List</h4>
</div>
<div class="modal-body modal-body-scroll">
    <div class="row">
        <div class="col-md-12">
            <div class="table-container">
                <table class="table table-striped table-hover table-bordered" id="datatable_item">
                    <thead>
                        <tr role="row">
                            <th width="120px" class="text-center"> Item Code </th>
                            <th class="text-center"> Item Description </th>
                            <th width="100px" class="text-center"> Group </th>
                            <th width="100px" class="text-center"> UOM </th>
                            <th width="100px" class="text-center"> On Hand Qty </th>
                            <th width="80px"></th>
                        </tr>
                        <tr role="row" class="filter bg-grey-steel">
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="filter_item_code">
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="filter_item_desc">
                            </td>
                            <td>
                                <select name="filter_group" class="form-control form-filter input-sm select2me">
                                    <option value="">All</option>
                                    <?php
                                    $qry_group = $this->mdl_general->get('in_ms_item_group', array('status' => STATUS_NEW), array(), 'group_code');
                                    foreach ($qry_group->result() as $row) {
                                        echo '<option value="' . $row->group_id . '">' . $row->group_code . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="filter_uom">
                            </td>
                            <td></td>
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