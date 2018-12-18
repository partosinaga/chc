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
                            <i class="fa fa-user"></i> <?php echo ($sp_type_id > 0 ? 'Edit' : 'New');?> Sales Payment Type
                        </div>
                        <div class="actions">
                            <?php echo btn_back(base_url('sales/setup/payment_type.tpd')); ?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="form-entry" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" class="form-control" name="sp_type_id" value="<?php echo $sp_type_id;?>"/>
                            <div class="form-actions top">
                                <div class="row">
                                    <div class="col-md-9">
                                        <?php echo $btn_action; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body" id="form-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Name<span class="required" aria-required="true"> * </span></label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="sp_type_name" value="<?php echo ($sp_type_id > 0 ? $row->sp_type_name : '');?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Status<span class="required" aria-required="true"> * </span></label>

                                    <div class="col-md-6">
                                        <select name="coa_id" class="form-control form-filter select2me">
                                            <option value="">--select--</option>
                                            <?php
                                            $sql = "SELECT * FROM gl_coa WHERE  status= '".STATUS_NEW."' and is_display = '1'   ";
                                            $coa = $this->db->query($sql);
                                            foreach($coa->result() as $rows){
                                                echo ' <option value="'.$rows->coa_id.'"  '.($sp_type_id > 0 && $row->coa_id == $rows->coa_id ? 'selected="selected"' : ''  ).' >'.$rows->coa_code.' | '.$rows->coa_desc .'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                                if($sp_type_id > 0){
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

        var sp_type_name = $('input[name="sp_type_name"]').val().trim();

        if(sp_type_name == ''){
            toastr["warning"]("Please enter payment type name.", "Warning!");
            $('input[name="sp_type_name"]').closest('.form-group').addClass('has-error');
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
                    url: "<?php echo base_url('sales/setup/ajax_payment_submit');?>",
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