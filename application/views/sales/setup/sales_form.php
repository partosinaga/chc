<?php
$btn_action = '';
$btn_save = btn_save() . btn_save_close();

$btn_action .= $btn_save;
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
        <?php echo show_flash($this->session->flashdata('flash_message'), $this->session->flashdata('flash_message_class')); ?>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box <?php echo BOX; ?>">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i> <?php echo ($sales_id > 0 ? 'Edit' : 'New');?> Sales
                        </div>
                        <div class="actions">
                            <?php echo btn_back(base_url('sales/setup/sales.tpd')); ?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="form-entry" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" class="form-control" name="sales_id" value="<?php echo $sales_id?>"/>
                            <div class="form-actions top">
                                <div class="row">
                                    <div class="col-md-9">
                                        <?php echo $btn_action; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body" id="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Name<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="name" value="<?php echo ($sales_id > 0 ? $row->sales_name : '');?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Email</label>

                                            <div class="col-md-6">
                                                <input type="email" class="form-control" name="email" value="<?php echo ($sales_id > 0 ? $row->email : '');?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Phone</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="phone" value="<?php echo ($sales_id > 0 ? $row->sales_phone : '');?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Cellular</label>

                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="cellular" value="<?php echo ($sales_id > 0 ? $row->sales_cellular : '');?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Identity Number <span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <input type="number" class="form-control" name="identity" value="<?php echo ($sales_id > 0 ? $row->identity_number : '');?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Gender <span class="required" aria-required="true"> * </span></label>

                                            <div class="col-md-6">
                                                <select name="gender" class="form-control form-filter select2me">
                                                    <option value=""> -- Select --</option>
                                                    <option value="m" <?php echo $row->gender == 'm' ? 'selected="selected"' : '' ?>> Male</option>
                                                    <option value="f"  <?php echo $row->gender == 'f' ? 'selected="selected"' : '' ?>> Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">D.O.B<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                    <input type="text" class="form-control" name="dob" readonly value="<?php echo ($sales_id > 0 ? $row->dob : '');?>"/>
                                                      <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i
                                                                class="fa fa-calendar"></i></button>
                                                      </span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if($sales_id > 0){
                                            echo '<div class="form-group">
                                                <label class="control-label col-md-2">Status<span class="required" aria-required="true"> * </span></label>

                                                <div class="col-md-6">
                                                     <select name="status" class="form-control form-filter select2me">
                                                        <option value="' . STATUS_NEW . '"  ' . ($row->status == STATUS_NEW ? 'selected="selected"' : '') . ' >Active</option>
                                                        <option value="' . STATUS_INACTIVE . '"  ' . ($row->status == STATUS_INACTIVE ? 'selected="selected"' : '') . ' >Inactive</option>
                                                     </select>
                                                </div>
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
<script>
    function validate_submit(){
        var result = true;

        if($('.form-group').hasClass('has-error')){
            $('.form-group').removeClass('has-error');
        }

        var name = $('input[name="name"]').val().trim();
        var gender = $('select[name="gender"]').val().trim();
        var dob = $('input[name="dob"]').val().trim();
        var idtt = $('input[name="identity"]').val().trim();

        if(name == ''){
            toastr["warning"]("Please enter name.", "Warning!");
            $('input[name="name"]').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (gender == '') {
            toastr["warning"]("Please select gender.", "Warning!");
            $('select[name="gender"]').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (dob == '') {
            toastr["warning"]("Please choode DOB.", "Warning!");
            $('input[name="dob"]').closest('.form-group').addClass('has-error');
            result = false;
        }
        if (idtt == '') {
            toastr["warning"]("Please enter Identity Number.", "Warning!");
            $('input[name="identity"]').closest('.form-group').addClass('has-error');
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


        if (validate_submit()) {
            var form_data = $('#form-entry').serializeArray();
            if (btn[0] == null) {
            } else {
                if (btn[0].name === 'save_close') {
                    form_data.push({name: "save_close", value: 'save_close'});
                }
            }

            $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('sales/setup/ajax_sales_submit');?>",
                    dataType: "json",
                    data: form_data
                })
                .done(function (msg) {
                    window.location.assign(msg.link);
                })
                .fail(function () {
                    $('#form-entry').unblock();
                    toastr["error"]("Something has wrong, please try again later.", "Error");
                });
        }else{
            $('#form-entry').unblock();
        }
    });
</script>