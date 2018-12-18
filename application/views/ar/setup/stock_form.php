<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <?php
                $breadcrumbs = get_menu_name($this->uri->segment(1), $this->uri->segment(2), $this->uri->segment(3));
                foreach($breadcrumbs as $breadcrumb){
                    echo $breadcrumb;
                }
                ?>
            </ul>
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php

                if($itemstock_id > 0){
                    $hasEdit = check_session_action(get_menu_id(), STATUS_EDIT);
                }else{
                    $hasEdit = check_session_action(get_menu_id(), STATUS_NEW);
                }

                ?>
                <!-- Begin: life time stats -->
                <div class="portlet box <?php echo BOX ?>" >
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-list"></i>Product / Service <?php echo ($itemstock_id > 0 ? 'Edit' : 'New');?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo base_url('ar/setup/stock_list/1.tpd');?>" class="btn default green-stripe">
                                <i class="fa fa-arrow-circle-left "></i>
							<span class="hidden-480">
							Back </span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->
                        <form action="<?php echo base_url('ar/setup/submit_stock.tpd');?>" class="form-horizontal" id="validate-form" method="post" autocomplete="off">
                            <div class="form-body">
                                <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class'));?>
                                <input type="hidden" id="itemstock_id" name="itemstock_id" value="<?php echo ($itemstock_id > 0 ? $row->itemstock_id : '0');?>"/>
                                <input type="hidden" name="is_service_item" value="<?php echo ($itemstock_id > 0 ? $row->is_service_item : '0');?>"/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="portlet">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-gift"></i>
                                                    Item information
                                                </div>
                                            </div>
                                            <div class="portlet-body portlet-empty">
                                                <h4 >Code</h4>
                                                <p style="padding-left: 10px;" class="bold"><?php echo ($itemstock_id > 0 ? $row->item_code : '');?></p>
                                                <h4 >Description</h4>
                                                <input type="text" name="item_desc" class="form-control bold" value="<?php echo ($itemstock_id > 0 ? $row->item_desc : '');?>" <?php echo ($itemstock_id > 0 ? ($row->is_service_item > 0 ? '' : 'readonly') : '') ?>>
                                                <div class="row">
                                                    <div class="col-md-12" >
                                                        <div class="col-md-4" style="padding-left: 0px;">
                                                            <h4 >Qty</h4>
                                                            <span class="font-green-seagreen font-lg bold" style="padding-left: 30px;"><?php echo ($itemstock_id > 0 ? format_num($row->itemstock_current_qty,0) : '0');?></span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <h4 >Stock UOM</h4>
                                                            <select name="itemstock_uom" class="form-control select2me ">
                                                            <?php
                                                                if(count($uoms) > 0){
                                                                    foreach($uoms as $uom){
                                                                        echo '<option value="' . $uom['uom_id'] . '" ' . ($itemstock_id > 0 ? ($uom['uom_id'] == $row->itemstock_uom ? 'selected="selected"' : '') : '') . '>' . $uom['uom_code'] .  '</option>';
                                                                    }
                                                                }
                                                            ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <h4 >&nbsp;</h4>
                                                            <div class="checkbox-list" style="padding-top: 8px;">
                                                                <input type="checkbox" <?php echo ($itemstock_id > 0 ? ($row->price_lock > 0 ? 'checked' : '') : 'checked');?> value="1" name="price_lock" class="checker"><span class="bold">PRICE LOCK</span>
                                                                &nbsp;<!-- input type="checkbox" value="1" name="enable_ar_bill" class="checker"><span class="bold">AR Billed</span -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                if($itemstock_id > 0){
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h4 >Status</h4>
                                                            <select name="status" class="form-control select2me" >
                                                                <option value="<?php echo STATUS_NEW;?>" <?php echo ($row->status == STATUS_NEW ? 'selected="selected"' : '');?>>ACTIVE</option>
                                                                <option value="<?php echo STATUS_INACTIVE;?>" <?php echo ($row->status == STATUS_INACTIVE ? 'selected="selected"' : '');?>>INACTIVE</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="portlet box blue-hoki">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="icon-share"></i>
                                                    Specifications
                                                </div>
                                                <div class="tools">
                                                </div>
                                            </div>
                                            <div class="portlet-body portlet-empty">
                                                <div class="form-body" style="margin-left: 15px;">
                                                    <div class="row hide">
                                                        <div class="col-md-12" style="padding-left: 0px;">
                                                            <div class="col-md-2">
                                                                <div class="form-group ">
                                                                    <label >Min Stock</label>
                                                                    <input type="text" placeholder="Min" name="itemstock_min" class="form-control text-center mask_currency" value="<?php echo ($itemstock_id > 0 ? format_num($row->itemstock_min,0) : '0');?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2"></div>
                                                            <div class="col-md-2">
                                                                <div class="form-group ">
                                                                    <label >Max Stock</label>
                                                                    <input type="text" placeholder="Max" name="itemstock_max" class="form-control mask_currency text-center" value="<?php echo ($itemstock_id > 0 ? format_num($row->itemstock_max,0) : '0');?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2"></div>
                                                            <div class="col-md-2">
                                                                <div class="form-group ">
                                                                    <label >Factor</label>
                                                                    <input type="text" placeholder="Factor" name="itemstock_factor" class="form-control text-center mask_currency" value="<?php echo ($itemstock_id > 0 ? format_num($row->itemstock_factor,0) : '0');?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6" >
                                                            <div class="form-group ">
                                                                <label>Distribution UOM</label>
                                                                <select name="itemstock_uom_distribution" class="form-control select2me ">
                                                                    <?php
                                                                    if(count($uoms) > 0){
                                                                        foreach($uoms as $uom){
                                                                            echo '<option value="' . $uom['uom_id'] . '" ' . ($itemstock_id > 0 ? ($uom['uom_id'] == $row->itemstock_uom_distribution ? 'selected="selected"' : '') : '') . '>' . $uom['uom_code'] .  '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6" style="padding-left: 0px;">
                                                            <div class="form-group col-md-10">
                                                                <label>Price&nbsp;(IDR)</label>
                                                                <div class="input-icon right">
                                                                    <i class="fa"></i>
                                                                    <input type="text" placeholder="Price" name="unit_price" class="form-control text-center mask_currency bold" value="<?php echo ($itemstock_id > 0 ? format_num($row->unit_price,0) : '');?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group col-md-10">
                                                                <label>Discount&nbsp;(%)</label>
                                                                <input type="text" placeholder="Discount" name="unit_discount" class="form-control text-center mask_currency" value="<?php echo ($itemstock_id > 0 ? format_num($row->unit_discount,2) : '0.00');?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4" style="padding-left: 0px;">
                                                            <div class="form-group col-md-12">
                                                                <label>COA Code&nbsp;</label>
                                                                <div class="input-icon right pull-left">
                                                                    <i class="fa"></i>
                                                                    <div class="input-group">
                                                                        <input type="hidden" name="coa_valid">
                                                                        <input type="text" name="coa_code" class="form-control input-sm text-center" value="<?php echo ($itemstock_id > 0 ? $row->coa_code > 0 ? $row->coa_code  : '' : '');?>" readonly />
                                                                        <span class="input-group-btn">
                                                                        <button class="btn btn-sm green-haze find_coa" unique-id="" style="padding-top:5px;margin-right:0px;" type="button"><i class="fa fa-arrow-up fa-fw"></i></button>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8" style="padding-left: 0px;">
                                                            <div class="form-group col-md-12" style="padding-top: 28px;">
                                                                <span id="id_coa_desc"><?php echo ($itemstock_id > 0 ? $row->coa_code > 0 ? $row->coa_desc  : '' : '');?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn blue-madison" name="save_close">Submit</button>
                            </div>
                        </form>
                        <!-- END FORM-->
                    </div>
                </div>
                <!-- End: life time stats -->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<div id="ajax-modal-trx" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>
<div id="ajax-modal" data-width="710" class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"></div>

<script>
    $(document).ready(function(){
        var handleValidation = function() {
            // for more info visit the official plugin documentation:
            // http://docs.jquery.com/Plugins/Validation
            $(".mask_currency").inputmask("decimal", {
                radixPoint: ".",
                groupSeparator: ",",
                digits: 2,
                autoGroup: true,
                autoUnmask: true
            });

            $.validator.addMethod("validatePrice", function (value, element)
                {
                    var valid = true;
                    try{
                        var price = parseFloat($('input[name="unit_price"]').val()) || 0;
                        if(price <= 0){
                            toastr["error"]("Price must not 0.", "Warning");
                            valid = false;
                        }
                    }catch(e){
                        console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Price must not 0."
            );

            $.validator.addMethod("validateCOA", function (value, element)
                {
                    var valid = true;
                    try{
                        var coa_code = parseFloat($('input[name="coa_code"]').val()) || 0;

                        if(coa_code <= 0){
                            toastr["error"]("Please select COA Code.", "Warning");
                            valid = false;
                        }

                    }catch(e){
                        console.log(e);
                        valid = false;
                    }

                    return valid;
                },
                "Please select COA Code."
            )

            var form1 = $('#validate-form');
            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    item_desc: {
                        required: true
                    },
                    itemstock_uom: {
                        required: true
                    },
                    itemstock_uom_distribution: {
                        required: true
                    },
                    itemstock_min: {
                        required: true,
                        min:1
                    },
                    itemstock_max: {
                        required: true,
                        min:1
                    },
                    itemstock_factor: {
                        required: true,
                        min:1
                    },
                    unit_price: {
                        //required: true,
                        //min:1,
                        validatePrice:true
                    },
                    coa_valid:{
                        validateCOA: true
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    Metronic.scrollTo(form1, -200);
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight

                },

                success: function (label, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    icon.removeClass("fa-warning").addClass("fa-check");
                },

                submitHandler: function (form) {
                    form.submit(); // submit the form
                }
            });
        }

        handleValidation();

        var grid_coa = new Datatable();
        var handleTableCOA = function (coaCode)   {

            grid_coa.init({
                src: $("#datatable_coa"),
                onSuccess: function (grid_coa) {
                    // execute some code after table records loaded
                },
                onError: function (grid_coa) {
                    // execute some code on network or other general error
                },
                onDataLoad: function(grid_coa) {
                    // execute some code on ajax data load
                },
                loadingMessage: 'Populating...',
                dataTable: {
                    "bStateSave": false, // save datatable state(pagination, sort, etc) in cookie.
                    "aoColumns": [
                        { "sClass": "text-center", "bSortable": true, "sWidth" : '15%' },
                        null,
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center","sWidth" : '10%' },
                        { "sClass": "text-center", "sWidth" : '6%' },
                        { "bSortable": false, "sClass": "text-center" }
                    ],
                    "aaSorting": [],
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "<?php echo base_url('general/modalbook/get_coa_list_by_code');?>/" + coaCode
                    }
                }
            });

            var tableWrapper = $("#datatable_coa_wrapper");

            tableWrapper.find(".dataTables_length select").select2({
                showSearchInput: false //hide search box with special css class
            }); // initialize select2 dropdown

            $('select.select2me').select2();

            // End Datatable Item
        }

        $('.find_coa').on('click', function(e){
            e.preventDefault();

            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner =
                '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                    '<div class="progress progress-striped active">' +
                    '<div class="progress-bar" style="width: 100%;"></div>' +
                    '</div>' +
                    '</div>';

            var coa_code = $('input[name="coa_code"]').val();

            //console.log('UID COA ' + unique_id);
            $('body').modalmanager('loading');

            var uninitialized = $('#datatable_coa').filter(function() {
                return !$.fn.DataTable.fnIsDataTable(this);
            });

            var $modal = $('#ajax-modal');

            setTimeout(function(){
                $modal.load('<?php echo base_url('general/modalbook/ajax_coa_by_id');?>.tpd', '', function(){
                    handleTableCOA(coa_code);
                    $modal.modal();

                    $.fn.modalmanager.defaults.resize = true;

                    if ($modal.hasClass('bootbox') == false) {
                        $modal.addClass('modal-fix');
                    }

                    if($modal.hasClass('modal-overflow') === false){
                        $modal.addClass('modal-overflow');
                    }

                    $modal.css({'margin-top': '0px'});
                });
            }, 150);
        })

        $('.btn-select-coa').live('click', function (e) {
            e.preventDefault();

            //var coa_id = parseInt($(this).attr('coa-id')) || 0;
            var coa_code = $(this).attr('coa-code');
            var coa_desc = $(this).attr('coa-desc');

            $('input[name="coa_code"]').val(coa_code);
            $('#id_coa_desc').html(coa_desc);

            $('#ajax-modal').modal('hide');
        });
    });

</script>