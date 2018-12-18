<?php
$isedit = true;

$btn_action = '';
$btn_save = btn_save() . btn_save_close();

if ($pr_id > 0) {
    if ($row->status == STATUS_NEW || $row->status == STATUS_DISAPPROVE) {
        if (check_session_action(get_menu_id(), STATUS_EDIT)) {
            $btn_action .= $btn_save;
        } else {
            $isedit = false;
        }

        if (check_session_action(get_menu_id(), STATUS_APPROVE)) {
            $btn_action .= btn_action($pr_id, $row->pr_code, STATUS_APPROVE);
        }
        if (check_session_action(get_menu_id(), STATUS_CANCEL)) {
            $btn_action .= btn_action($pr_id, $row->pr_code, STATUS_CANCEL);
        }
    } else if ($row->status == STATUS_APPROVE) {
        $isedit = false;

        if (check_session_action(get_menu_id(), STATUS_DISAPPROVE)) {
            $btn_action .= btn_action($pr_id, $row->pr_code, STATUS_DISAPPROVE);
        }
        if (check_session_action(get_menu_id(), STATUS_CLOSED)) {
            $btn_action .= btn_action($pr_id, $row->pr_code, STATUS_CLOSED, 'COMPLETE');
        }
        if (check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(site_url('purchasing/pr/pdf_pr/' . $pr_id));
        }
    } else if ($row->status == STATUS_CLOSED) {
        if (check_session_action(get_menu_id(), STATUS_PRINT)) {
            $btn_action .= btn_print(site_url('purchasing/pr/pdf_pr/' . $pr_id));
        }
    } else {
        $isedit = false;
    }

} else {
    $btn_action .= $btn_save;
}
?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <?php
                $breadcrumbs = get_menu_name($this->uri->segment(1), $this->uri->segment(2), $this->uri->segment(3));
                foreach ($breadcrumbs as $breadcrumb) {
                    echo $breadcrumb;
                }
                ?>
            </ul>
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box <?php echo BOX; ?>">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i><?php echo($pr_id > 0 ? 'View' : 'New'); ?> PR
                            <?php
                            if ($pr_id > 0) {
                                echo '&nbsp;&nbsp;&nbsp; ' . get_status_name($row->status);
                            }
                            ?>
                        </div>
                        <div class="actions">
                            <?php echo btn_back(base_url('purchasing/pr/pr_manage/1.tpd')); ?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="form-entry" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" id="pr_id" name="pr_id" value="<?php echo $pr_id; ?>"/>

                            <div class="form-actions top">
                                <div class="row">
                                    <div class="col-md-9">
                                        <?php echo $btn_action; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body" id="form-body">
                                <div class="row block-input" style="margin-bottom: 15px;">
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">PR No</label>

                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="pr_code"
                                                       value="<?php echo($pr_id > 0 ? $row->pr_code : ''); ?>"
                                                       readonly/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Department<span class="required"
                                                                                                  aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <select name="dept_id" class="form-control form-filter select2me">
                                                    <option value=""> -- Select --</option>
                                                    <?php
                                                    if (count($dept_list) > 0) {
                                                        foreach ($dept_list as $dept) {
                                                            echo '<option value="' . $dept['department_id'] . '" ' . ($pr_id > 0 ? ($row->department_id == $dept['department_id'] ? 'selected="selected"' : '') : '') . '>' . $dept['department_name'] . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Date Prepare<span class="required"
                                                                                                    aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="date_prepare"
                                                           value="<?php echo($pr_id > 0 ? ymd_to_dmy($row->date_prepare) : date('d-m-Y')); ?>"
                                                           readonly/>
                                                      <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                                      </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Request Delivery Date <span
                                                    class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <div class="input-group date date-picker-nolimit"
                                                     data-date-format="dd-mm-yyyy">
                                                    <input type="text" class="form-control" name="delivery_date"
                                                           value="<?php echo($pr_id > 0 ? ymd_to_dmy($row->delivery_date) : date('d-m-Y')); ?>"
                                                           readonly/>
                                                      <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                                      </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Remark <span class="required"
                                                                                               aria-required="true"> * </span></label>

                                            <div class="col-md-9">
                                                <textarea class="form-control" rows="2"
                                                          name="remarks"><?php echo($pr_id > 0 ? $row->remarks : ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="table_detail_item" value="">
                                        <input type="hidden" name="table_detail_service" value="">

                                        <div class="portlet-title blue-ebonyclay">
                                            <ul class="nav nav-tabs">
                                                <li class="active">
                                                    <a href="#portlet_item" data-toggle="tab"><i class="fa fa-cube"></i>Items</a>
                                                </li>
                                                <li>
                                                    <a href="#portlet_service" data-toggle="tab"><i
                                                            class="fa fa-gavel"></i>Services </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="portlet-body ">
                                            <div class="tab-content">
                                                <div class="tab-pane active block-input" id="portlet_item">
                                                    <div class="portlet-title">
                                                        <div class="actions" style="margin-bottom: 10px;">
                                                            <a href="javascript:;"
                                                               class="btn default green-seagreen add_detail_item input-sm yellow-stripe">
                                                                <i class="fa fa-plus"></i><span class="hidden-480">&nbsp;&nbsp;Add Item </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <table class="table table-striped table-hover table-bordered"
                                                           id="datatable_detail_item">
                                                        <thead>
                                                        <tr>
                                                            <th width="20%" class="text-center">Recommended Supplier
                                                            </th>
                                                            <th class="text-left">Item Description</th>
                                                            <th width="10%" class="text-center"> Qty</th>
                                                            <th width="8%" class="text-center"> UOM</th>
                                                            <th width="20%" class="text-center"> Link</th>
                                                            <th width="60px" class="text-center">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $rowIndexItm = 0;
                                                        if ($pr_id > 0) {
                                                            if (count($qry_det) > 0) {
                                                                foreach ($qry_det as $row_det) {
                                                                    if ($row_det['item_type'] == Purchasing::ITEM_MATERIAL) {
                                                                        $combo_supplier = '';
                                                                        if (count($supplier_list) > 0) {
                                                                            foreach ($supplier_list as $supplier) {
                                                                                $combo_supplier .= '<option value="' . $supplier['supplier_id'] . '" ' . ($row_det['supplier_id'] > 0 ? ($row_det['supplier_id'] == $supplier['supplier_id'] ? 'selected="selected"' : '') : '') . ' >' . $supplier['supplier_name'] . '</option>';
                                                                            }
                                                                        }

                                                                        $combo_uom = '';
                                                                        if (count($uom_list) > 0) {
                                                                            foreach ($uom_list as $uom) {
                                                                                $combo_uom .= '<option value="' . $uom['uom_id'] . '" ' . ($row_det['uom_id'] > 0 ? ($row_det['uom_id'] == $uom['uom_id'] ? 'selected="selected"' : '') : '') . ' >' . $uom['uom_code'] . '</option>';
                                                                            }
                                                                        }
                                                                        echo '<tr data-index="' . $rowIndexItm . '">
                                                                                <td class="text-center">
                                                                                    <input type="hidden" name="pr_item_id[' . $rowIndexItm . ']" value="' . $row_det['pr_item_id'] . '" />
                                                                                    <input type="hidden" class="class_status" name="status[' . $rowIndexItm . ']" value="1" />
                                                                                    <input type="hidden" name="item_type[' . $rowIndexItm . ']" value="' . Purchasing::ITEM_MATERIAL . '" />
                                                                                    <select name="supplier_id[' . $rowIndexItm . ']" class="form-control form-filter input-sm select2me ">
                                                                                        <option value="0" >None</option>
                                                                                        ' . $combo_supplier . '
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <textarea class="form-control input-sm " rows="3" name="item_desc[' . $rowIndexItm . ']" style="min-height:50px;" >' . $row_det['item_desc'] . '</textarea>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" name="item_qty[' . $rowIndexItm . ']" value="' . $row_det['item_qty'] . '" class="form-control  text-right input-sm num_qty" item-id="' . $row_det['item_id'] . '">
                                                                                </td>
                                                                                <td>
                                                                                    <select name="uom_id[' . $rowIndexItm . ']" class="form-control form-filter input-sm select2me ">
                                                                                        <option value="0" >None</option>
                                                                                        ' . $combo_uom . '
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" name="item_url[' . $rowIndexItm . ']" value="' . $row_det['item_url'] . '" class="form-control input-sm">
                                                                                </td>
                                                                                <td class="padding-top-13 text-center">
                                                                                    <a class="btn btn-danger btn-xs tooltips btn-remove" data-id="' . $row_det['pr_item_id'] . '" data-original-title="Remove" href="javascript:;"><i class="fa fa-times"></i></a>
                                                                                </td>
                                                                            </tr>';
                                                                        $rowIndexItm++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane block-input" id="portlet_service">
                                                    <div class="portlet-title">
                                                        <div class="actions" style="margin-bottom: 10px;">
                                                            <a href="javascript:;"
                                                               class="btn default green-seagreen add_detail_service input-sm yellow-stripe">
                                                                <i class="fa fa-plus"></i><span class="hidden-480">&nbsp;&nbsp;Add Service </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <table class="table table-striped table-hover table-bordered"
                                                           id="datatable_detail_service">
                                                        <thead>
                                                        <tr>
                                                            <th width="20%" class="text-center">Recommended Supplier
                                                            </th>
                                                            <th class="text-left">Service Description</th>
                                                            <th width="10%" class="text-center"> Qty</th>
                                                            <th width="8%" class="text-center"> UOM</th>
                                                            <th width="20%" class="text-center"> Link</th>
                                                            <th width="60px" class="text-center">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $rowIndexSvc = $rowIndexItm;
                                                        if ($pr_id > 0) {
                                                            if (count($qry_det) > 0) {
                                                                foreach ($qry_det as $row_det) {
                                                                    $combo_supplier = '';
                                                                    if (count($supplier_list) > 0) {
                                                                        foreach ($supplier_list as $supplier) {
                                                                            $combo_supplier .= '<option value="' . $supplier['supplier_id'] . '" ' . ($row_det['supplier_id'] > 0 ? ($row_det['supplier_id'] == $supplier['supplier_id'] ? 'selected="selected"' : '') : '') . ' >' . $supplier['supplier_name'] . '</option>';
                                                                        }
                                                                    }

                                                                    $combo_uom = '';
                                                                    if (count($uom_list) > 0) {
                                                                        foreach ($uom_list as $uom) {
                                                                            $combo_uom .= '<option value="' . $uom['uom_id'] . '" ' . ($row_det['uom_id'] > 0 ? ($row_det['uom_id'] == $uom['uom_id'] ? 'selected="selected"' : '') : '') . ' >' . $uom['uom_code'] . '</option>';
                                                                        }
                                                                    }

                                                                    if ($row_det['item_type'] == Purchasing::ITEM_SERVICE) {
                                                                        echo '<tr data-index="' . $rowIndexSvc . '">
                                                                                <td class="text-center">
                                                                                    <input type="hidden" name="pr_item_id[' . $rowIndexSvc . ']" value="' . $row_det['pr_item_id'] . '">
                                                                                    <input type="hidden" class="class_status" name="status[' . $rowIndexSvc . ']" value="1" />
                                                                                    <input type="hidden" name="item_type[' . $rowIndexSvc . ']" value="' . Purchasing::ITEM_SERVICE . '">
                                                                                    <select name="supplier_id[' . $rowIndexSvc . ']" class="form-control form-filter input-sm select2me">
                                                                                        <option value="0" >None</option>
                                                                                        ' . $combo_supplier . '
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <textarea class="form-control input-sm " rows="3" name="item_desc[' . $rowIndexSvc . ']" style="min-height:50px;"  >' . $row_det['item_desc'] . '</textarea>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" name="item_qty[' . $rowIndexSvc . ']" value="' . $row_det['item_qty'] . '" class="form-control  text-right input-sm num_qty">
                                                                                </td>
                                                                                <td>
                                                                                    <select name="uom_id[' . $rowIndexSvc . ']" class="form-control form-filter input-sm select2me ">
                                                                                        ' . $combo_uom . '
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" name="item_url[' . $rowIndexSvc . ']" value="' . $row_det['item_url'] . '" class="form-control input-sm">
                                                                                </td>
                                                                                <td class="padding-top-13 text-center">
                                                                                    <a class="btn btn-danger btn-xs tooltips btn-remove" data-id="' . $row_det['pr_item_id'] . '" data-original-title="Remove" href="javascript:;"><i class="fa fa-times"></i></a>
                                                                                </td>
                                                                            </tr>';

                                                                        $rowIndexSvc++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php
                                        if ($pr_id > 0) {
                                            $log = '';
                                            $modified = '';
                                            $qry_log = $this->mdl_general->get('app_log', array('feature_id' => Feature::FEATURE_PR, 'reff_id' => $pr_id), array(), 'log_id asc');
                                            if ($qry_log->num_rows() > 0) {
                                                $log .= '<ul class="list-unstyled" style="margin-left:-15px;">';
                                                foreach ($qry_log->result() as $row_log) {
                                                    $remark = '';
                                                    if (trim($row_log->remark) != '') {
                                                        $remark = '<h4 style="margin-left:10px;"><span class="label label-success">Remark : ' . trim($row_log->remark) . '</span></h4>';
                                                    }
                                                    $log .= '<li class="margin-bottom-5"><h6>' . $row_log->log_subject . ' on ' . date_format(new DateTime($row_log->log_date), 'd/m/Y H:i:s') . ' by ' . get_user_fullname($row_log->user_id) . '</h6>' . $remark . '</li>';
                                                }
                                                $log .= '</ul>';
                                            }

                                            if ($row->user_modified > 0) {
                                                $modified .= "<div class='col-md-4'><h6>Last Modified by " . get_user_fullname($row->user_modified) . " (" . date_format(new DateTime($row->date_modified), 'd/m/Y H:i:s') . ")</h6></div>";
                                            }
                                            echo '<div class="note note-info" style="margin:10px;">
                                                    <div class="col-md-8">
                                                        ' . $log . '
                                                    </div>
                                                    ' . $modified . '
                                                    <div style="clear:both;"></div>
                                                </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static"
     tabindex="-1"></div>
<script>
    $(document).ready(function () {
        var isedit = <?php echo ($isedit ? 0 : 1); ?>;

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "50000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        if (isedit > 0) {
            $('.block-input').block({
                message: null,
                overlayCSS: {backgroundColor: '#EDF5EB', opacity: 0, cursor: 'default'}
            });
        }

        <?php
        if($pr_id > 0){
            echo show_toastr($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));
            echo picker_input_date(true, true, ymd_to_dmy($row->date_prepare));
        } else {
            echo picker_input_date();
        }
        //echo input_date('date-picker-nolimit', 'right', false, false);
        ?>

        Metronic.initDatePicker('date-picker-nolimit');

        autosize($('textarea'));

        function numeric_mask() {
            $(".num_qty").inputmask("decimal", {
                radixPoint: ".",
                groupSeparator: ",",
                digits: 2,
                autoGroup: true,
                autoUnmask: true
            });
        }

        numeric_mask();

        $('.num_qty').live('keyup', function () {
            var i = $(this).val();
            var val = parseFloat(i) || 0;

            if (val <= 0) {
                $(this).val(1);
            }
        });

        function validate_submit() {
            var result = true;

            if ($('.form-group').hasClass('has-error')) {
                $('.form-group').removeClass('has-error');
            }

            var dept_id = parseInt($('select[name="dept_id"]').val()) || 0;
            var date_prepare = $('input[name="date_prepare"]').val().trim();
            var delivery_date = $('input[name="delivery_date"]').val().trim();
            var remarks = $('textarea[name="remarks"]').val().trim();

            if (dept_id <= 0) {
                toastr["warning"]("Please select Department.", "Warning!");
                $('select[name="dept_id"]').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (date_prepare == '') {
                toastr["warning"]("Please select Date Prepare.", "Warning!");
                $('input[name="date_prepare"]').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (delivery_date == '') {
                toastr["warning"]("Please select Date Delivery.", "Warning!");
                $('input[name="delivery_date"]').closest('.form-group').addClass('has-error');
                result = false;
            }
            if (remarks == '') {
                toastr["warning"]("Please input remarks.", "Warning!");
                $('textarea[name="remarks"]').closest('.form-group').addClass('has-error');
                result = false;
            }

            //var i = 0;
            var i_act = 0;
            $('#datatable_detail_item > tbody > tr ').each(function () {
                if (!$(this).hasClass('hide')) {
                    var i = parseInt($(this).attr('data-index')) || 0;
                    var item_desc = $('textarea[name="item_desc[' + i + ']"]').val().trim();
                    var item_qty = parseFloat($('input[name="item_qty[' + i + ']"]').val()) || 0;

                    $('input[name="item_qty[' + i + ']"]').removeClass('has-error');
                    $('textarea[name="item_desc[' + i + ']"]').removeClass('has-error');

                    if (item_qty <= 0) {
                        toastr["warning"]("Please select Item Qty.", "Warning");
                        $('input[name="item_qty[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    if (item_desc == '') {
                        toastr["warning"]("Please input Item Description.", "Warning");
                        $('textarea[name="item_desc[' + i + ']"]').addClass('has-error');
                        result = false;
                    }
                    i_act++;
                }
                //i++;
            });

            //var s = 0;
            var s_act = 0;
            $('#datatable_detail_service > tbody > tr ').each(function () {
                if (!$(this).hasClass('hide')) {
                    var s = parseInt($(this).attr('data-index')) || 0;
                    var item_desc = $('textarea[name="item_desc[' + s + ']"]').val().trim();
                    var item_qty = parseFloat($('input[name="item_qty[' + s + ']"]').val()) || 0;

                    $('input[name="item_qty[' + s + ']"]').removeClass('has-error');
                    $('textarea[name="item_desc[' + s + ']"]').removeClass('has-error');

                    if (item_qty <= 0) {
                        toastr["warning"]("Please select Service Qty.", "Warning");
                        $('input[name="item_qty[' + s + ']"]').addClass('has-error');
                        result = false;
                    }
                    if (item_desc == '') {
                        toastr["warning"]("Please input Service Description.", "Warning");
                        $('textarea[name="item_desc[' + s + ']"]').addClass('has-error');
                        result = false;
                    }
                    s_act++;
                }
                //s++;
            });

            if (i_act <= 0 && s_act <= 0) {
                toastr["warning"]("Detail cannot be empty.", "Warning");
                result = false;
            }

            return result;
        }

        $('#form-entry').on('submit', function () {
            Metronic.blockUI({
                target: '#form-entry',
                boxed: true,
                message: 'Processing...'
            });

            var btn = $(this).find("button[type=submit]:focus");

            var next = true;
            toastr.clear();

            if (validate_submit()) {
                var form_data = $('#form-entry').serializeArray();
                if (btn[0] == null) {
                }
                else {
                    if (btn[0].name === 'save_close') {
                        form_data.push({name: "save_close", value: 'save_close'});
                    }
                }

                $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('purchasing/pr/submit_pr');?>",
                        dataType: "json",
                        data: form_data
                    })
                    .done(function (msg) {
                        if (msg.success == '1') {
                            window.location.assign(msg.link);
                            //$('#input_form').unblock();
                        }
                        else {
                            toastr["error"](msg.message, "Error");
                            $('#form-entry').unblock();
                        }
                    })
                    .fail(function () {
                        $('#form-entry').unblock();
                        toastr["error"]("Something has wrong, please try again later.", "Error");
                    });
            }
            else {
                $('#form-entry').unblock();
            }
        });

        function add_detail(type) {
            var rowIndexItem = $('#datatable_detail_item tbody tr').length;
            var rowIndexSrv = $('#datatable_detail_service tbody tr').length;
            var rowIndex = rowIndexItem + rowIndexSrv;
            var table_wrapper = null;
            if (type == '<?php echo Purchasing::ITEM_MATERIAL; ?>') {
                table_wrapper = $('#datatable_detail_item tbody');
            } else if (type == '<?php echo Purchasing::ITEM_SERVICE; ?>') {
                table_wrapper = $('#datatable_detail_service tbody');
            }

            var newRowContent = '<tr data-index="' + rowIndex + '">' +
                '<td class="text-center">' +
                '<input type="hidden" name="pr_item_id[' + rowIndex + ']" value="0">' +
                '<input class="class_status" type="hidden" name="status[' + rowIndex + ']" value="1">' +
                '<input type="hidden" name="item_type[' + rowIndex + ']" value="' + type + '">' +
                '<select name="supplier_id[' + rowIndex + ']" class="form-control form-filter input-sm select2me">' +
                '<option value="0">None</option>' +
                <?php
                if(count($supplier_list) > 0){
                    foreach($supplier_list as $supplier){
                ?>
                '<option value="<?php echo $supplier['supplier_id']; ?>"><?php echo $supplier['supplier_name']; ?></option>' +
                <?php
                    }
                }
                ?>
                '</select></td>' +
                '<td><textarea class="form-control input-sm" rows="3" name="item_desc[' + rowIndex + ']" style="min-height:50px;"  ></textarea></td>' +
                '<td><input type="text" name="item_qty[' + rowIndex + ']" value="1" class="form-control text-right input-sm num_qty"></td>' +
                '<td>' +
                '<select name="uom_id[' + rowIndex + ']" class="form-control form-filter input-sm select2me">' +
                <?php
                if(count($uom_list) > 0){
                    foreach($uom_list as $uom){
                ?>
                '<option value="<?php echo $uom['uom_id']; ?>" ><?php echo $uom['uom_code'] ; ?></option>' +
                <?php
                    }
                }
                ?>
                '</select></td>' +
                '<td><input type="text" name="item_url[' + rowIndex + ']" class="form-control input-sm"></td>' +
                '<td class="padding-top-13 text-center"><a class="btn-remove btn btn-danger btn-xs tooltips" data-original-title="Remove" href="javascript:;" data-id="0"><i class="fa fa-times"></i></a></td>' +
                '</tr>';

            table_wrapper.append(newRowContent);

            $('select.select2me').select2();

            numeric_mask();

            autosize($('textarea'));
        }

        $('.add_detail_item').live('click', function (e) {
            e.preventDefault();
            add_detail(<?php echo Purchasing::ITEM_MATERIAL; ?>);
        });

        $('.add_detail_service').live('click', function (e) {
            e.preventDefault();
            add_detail(<?php echo Purchasing::ITEM_SERVICE; ?>);
        });

        $('.btn-remove').live('click', function (e) {
            e.preventDefault();

            var this_btn = $(this);
            bootbox.confirm("Are you sure want to delete?", function (result) {
                if (result == true) {
                    this_btn.closest('tr').addClass('hide');
                    this_btn.closest('tr').find('.class_status').val('9');
                }
            });
        });

        $('.btn-action').live('click', function () {
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            var doc_code = $(this).attr('data-code');
            var action_code = $(this).attr('data-action-code');

            if (action == '<?php echo STATUS_CANCEL;?>') {
                bootbox.prompt({
                    title: "Please enter Cancel reason for " + doc_code + " :",
                    value: "",
                    buttons: {
                        cancel: {
                            label: "Cancel",
                            className: "btn-inverse"
                        },
                        confirm: {
                            label: "OK",
                            className: "btn-primary"
                        }
                    },
                    callback: function (result) {
                        if (result === null) {
                            //console.log('Empty reason');
                        } else if (result === '') {
                            toastr["warning"]("Cancel reason must be filled to proceed.", "Warning");
                        } else {
                            //console.log(result);
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('purchasing/pr/action_request');?>",
                                    dataType: "json",
                                    data: {pr_id: id, action: action, reason: result, is_redirect: true}
                                })
                                .done(function (msg) {
                                    if (msg.type == '0' || msg.type == '1') {
                                        if (msg.type == '1') {
                                            location.reload();
                                        }
                                        else {
                                            toastr["error"](msg.message, "Error");

                                            Metronic.unblockUI();
                                        }
                                    }
                                    else {
                                        toastr["danger"]("Something has wrong, please try again later.", "Error");

                                        Metronic.unblockUI();
                                    }
                                });
                        }
                    }
                });
            } else if (action == '<?php echo STATUS_CLOSED;?>') {
                bootbox.prompt({
                    title: "Please enter Complete reason for " + doc_code + " :",
                    value: "",
                    buttons: {
                        cancel: {
                            label: "Cancel",
                            className: "btn-inverse"
                        },
                        confirm: {
                            label: "OK",
                            className: "btn-primary"
                        }
                    },
                    callback: function (result) {
                        if (result === null) {
                            //console.log('Empty reason');
                        } else if (result === '') {
                            toastr["warning"]("Complete reason must be filled to proceed.", "Warning");
                        } else {
                            //console.log(result);
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('purchasing/pr/action_request');?>",
                                    dataType: "json",
                                    data: {pr_id: id, action: action, reason: result, is_redirect: true}
                                })
                                .done(function (msg) {
                                    if (msg.type == '0' || msg.type == '1') {
                                        if (msg.type == '1') {
                                            location.reload();
                                        }
                                        else {
                                            toastr["error"](msg.message, "Error");

                                            Metronic.unblockUI();
                                        }
                                    }
                                    else {
                                        toastr["danger"]("Something has wrong, please try again later.", "Error");

                                        Metronic.unblockUI();
                                    }
                                });
                        }
                    }
                });
            } else {
                if ($('#form-entry').valid()) {
                    bootbox.confirm("Are you sure want to " + action_code + " " + doc_code + " ?", function (result) {
                        if (result == true) {
                            Metronic.blockUI({
                                boxed: true
                            });

                            $.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url('purchasing/pr/action_request');?>",
                                    dataType: "json",
                                    data: {pr_id: id, action: action, is_redirect: true}
                                })
                                .done(function (msg) {
                                    if (msg.type == '0' || msg.type == '1') {
                                        if (msg.type == '1') {
                                            location.reload();
                                        }
                                        else {
                                            toastr["error"](msg.message, "Error");

                                            Metronic.unblockUI();
                                        }
                                    }
                                    else {
                                        toastr["danger"]("Something has wrong, please try again later.", "Error");

                                        Metronic.unblockUI();
                                    }
                                });
                        }
                    });
                } else {
                    toastr["error"]("Please fill all required data to continue.", "Warning");
                }
            }
        });
    });

</script>