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
                            <i class="fa fa-user"></i> <?php echo ($customer_id > 0 ? 'Edit' : 'New');?> Customer
                        </div>
                        <div class="actions">
                            <?php echo btn_back(base_url('sales/setup/index.tpd')); ?>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form method="post" id="form-entry" class="form-horizontal" onsubmit="return false;">
                            <input type="hidden" class="form-control" name="customer_id" value="<?php echo $customer_id;?>"/>
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
                                                    <input type="text" class="form-control" name="name" value="<?php echo ($customer_id > 0 ? $row->customer_name : '');?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Email</label>

                                                <div class="col-md-6">
                                                    <input type="email" class="form-control" name="email" value="<?php echo ($customer_id > 0 ? $row->email : '');?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Phone</label>

                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="phone" value="<?php echo ($customer_id > 0 ? $row->customer_phone : '');?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Cellular</label>

                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="cellular" value="<?php echo ($customer_id > 0 ? $row->customer_cellular : '');?>"/>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Fax</label>

                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="fax" value="<?php echo ($customer_id > 0 ? $row->customer_fax : '');?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Gender<span class="required" aria-required="true"> * </span></label>

                                                <div class="col-md-6">
                                                    <select name="gender" class="form-control form-filter select2me">
                                                        <option value=""> -- Select --</option>
                                                        <option value="m" <?php echo ($customer_id > 0 && $row->gender == 'm' ? 'selected="selected"' : '') ?>> Male</option>
                                                        <option value="f" <?php echo ($customer_id > 0 && $row->gender == 'f' ? 'selected="selected"' : '') ?>> Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">D.O.B<span class="required" aria-required="true"> * </span></label>
                                                <div class="col-md-6">
                                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                        <input type="text" class="form-control" name="dob" readonly value="<?php echo ($customer_id > 0 ? $row->dob : '');?>"/>
                                                      <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i
                                                                class="fa fa-calendar"></i></button>
                                                      </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            if($customer_id > 0){
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
                                    <div class="portlet-body ">
                                        <div class="portlet-title">
                                            <div class="actions" style="margin-bottom: 10px;">
                                                <a class="btn default green-seagreen btn-sm yellow-stripe"
                                                   data-toggle="modal" href="#large"> <i class="fa fa-plus"></i> Add
                                                    Address </a>
                                            </div>
                                        </div>
                                        <table class="table  table-striped table-hover table-bordered"
                                               id="address-table">
                                            <thead>
                                            <tr>
                                                <th>Address</th>
                                                <th width="10%">Postcode</th>
                                                <th width="15%">Country</th>
                                                <th width="15%">District</th>
                                                <th width="15%">City</th>
                                                <th width="3%">#</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if($customer_id > 0){
                                                    foreach($address->result() as $row_det){
                                                        echo '<tr>
                                                            <td class="hidden"><input type="text" class="form-control" name="customer_address_id[]" value="'.$row_det->customer_address_id.'"/> '.$row_det->customer_address_id.'</td>
                                                            <td> '.$row_det->customer_address.'</td>
                                                            <td> '.$row_det->customer_postcode.'</td>
                                                            <td> '.$row_det->country_name.'</td>
                                                            <td> '.$row_det->customer_district.'</td>
                                                            <td> '.$row_det->customer_city.'</td>
                                                            <td align="center">
                                                                <select name="address_status[]" class="form-control form-filter input-sm select2me">
                                                                    <option value="'.STATUS_NEW.'"  '.($row_det->status == '1' ? 'selected="selected"' : '').' >Active</option>
                                                                    <option value="'.STATUS_INACTIVE.'"  '.($row_det->status == '0' ? 'selected="selected"' : '').' >Inactive</option>
                                                                </select>
                                                            </td>
                                                        </tr>';
                                                    }

                                                }
                                            ?>
                                            </tbody>
                                        </table>
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
<!--MODAL ADD ADDRESS-->
<div class="modal fade bs-modal-lg" id="large" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Address</h4>
            </div>
            <div class="modal-body">
                <form id="add-address-form" method="POST" action="javascript:;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Postcode</label>

                                <div class="">
                                    <input type="number" class="form-control" name="postcode"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Country</label>

                                <div class="">
                                    <select name="country" id="country" class="form-control form-filter select2me">
                                        <option value=""> -- Select --</option>
                                        <?php
                                        $query = $this->db->query("SELECT * FROM master_country");
                                        foreach ($query->result() as $row) {
                                            if($row->master_country_id == 77){
                                                echo '<option value="' . $row->master_country_id . '" selected desc="'.$row->country_name.'">' . $row->country_name . '</option>';
                                            }else{
                                                echo '<option value="' . $row->master_country_id . '" desc="'.$row->country_name.'">' . $row->country_name . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">District</label>

                                <div class="">
                                    <input type="text" class="form-control" name="disctrict"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">City</label>

                                <div class="">
                                    <input type="text" class="form-control" name="city"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">

                            <div class="form-group">
                                <label class="control-label">Address <span class="required">* </span></label>

                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <textarea class="form-control" rows="5" name="address"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                <button type="submit" class="btn green add-address">Save</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!--END OF MODAL ADD ADDRESS-->
<script>
    $(document).ready(function () {
        var address = $('textarea[name="address"]').val();
        var rowIndex = <?php echo (isset($rowIndex) ? $rowIndex : 0) ; ?>;
        var tbody = $('#address-table').children('tbody');
        var table = tbody.length ? tbody : $('#address-table');
        $('.add-address').on('click', function () {
            var postcode = $('input[name="postcode"]').val();
            var disctrict = $('input[name="disctrict"]').val();
            var city = $('input[name="city"]').val();
            var country = $('select[name="country"]').val();
            var address = $('textarea[name="address"]').val();
            var country_desc = $('#country').find(':selected').attr('desc');

            if (!address) {
                toastr["warning"]("Please enter address.", "Warning!");
                $('textarea[name="address"]').closest('.form-group').addClass('has-error');
            } else {
                var newRow =
                    "<tr>" +
                    "<td class=\"hidden\"> <input type=\"\" class=\"form-control input input-sm\" name=\"customer_address_id[]\" value=\"a\"></td>" +
                    "<td> <input type=\"hidden\" class=\"form-control input input-sm\" name=\"address[]\" value='" +address+ "'> " + address + " </td>" +
                    "<td> <input type=\"hidden\" class=\"form-control input input-sm\" name=\"postcode[]\" value= '" + postcode + "'> " + postcode + "</td>" +
                    "<td> <input type=\"hidden\" class=\"form-control input input-sm\" name=\"country[]\" value= " + country + "> " + country_desc + "</td>" +
                    "<td> <input type=\"hidden\" class=\"form-control input input-sm\" name=\"district[]\" value= '" + disctrict + "'> " + disctrict + "</td>" +
                    "<td> <input type=\"hidden\" class=\"form-control input input-sm\" name=\"city[]\" value= '" + city + "'> " + city + "</td>" +
                    "<td align=\"center\"> <input type=\"hidden\" class=\"form-control input input-sm\" name=\"address_status[]\" value= \"1\"> <button onclick=\"delete_row(this)\" class=\"btn red btn-sm text-center\" ><i class=\"fa fa-remove\"></i></button> </td>" +
                    "</tr>";

                $('#address-table tbody').append(newRow);
                document.getElementById("add-address-form").reset();
                rowIndex++;
                $('#large').modal('hide');
            }


        })
    })

    function delete_row(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }

    function validate_submit(){
        var result = true;

        if($('.form-group').hasClass('has-error')){
            $('.form-group').removeClass('has-error');
        }

        var name = $('input[name="name"]').val().trim();
        var gender = $('select[name="gender"]').val().trim();
        var dob = $('input[name="dob"]').val().trim();

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
            toastr["warning"]("Please choose DOB.", "Warning!");
            $('input[name="dob"]').closest('.form-group').addClass('has-error');
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
                    url: "<?php echo base_url('sales/setup/ajax_customer_submit');?>",
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