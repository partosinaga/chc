<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo($row['reservation_code']); ?></title>

    <link href="<?php echo FCPATH; ?>assets/css/pdf_reservation.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="logo">
   <img src="<?php echo FCPATH; ?>assets/img/logo_dwijaya.png" alt="" width="60px">
</div>
<table width="100%">
    <tr>
        <td style="vertical-align:top; text-align:center;" colspan="3"><h1><strong>REGISTRATION FORM</strong></h1></td>
    </tr>
    <tr>
        <td colspan="3">
            <table class="table_header">
                <tr>
                    <th>RESERVATION NO</th>
                    <th>ARRIVAL DATE</th>
                    <th>DEPARTURE DATE</th>
                    <th>ROOM TYPE</th>
                    <th>NO OF GUEST</th>
                    <th>ROOM</th>
                </tr>
                <tr>
                    <td><?php echo $row['reservation_code']; ?></td>
                    <td><?php echo ymd_to_dmy($row['arrival_date']); ?></td>
                    <td><?php echo ymd_to_dmy($row['departure_date']); ?></td>
                    <td><?php echo $unittype_list ?></td>
                    <td><?php echo $row['qty_adult'] . ' adult(s)' . ($row['qty_child'] > 0 ? '<br>' . $row['qty_child'] . ' child(s)' : ''); ?></td>
                    <td><?php echo $row['room']; ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div id="container">
    <table width="100%" class="table_main">
        <thead>
            <tr>
                <th colspan="7" class="border-top border-right v-middle"><h2>GENERAL INFORMATION</h2></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="v-middle border-top border-right" width="140px;">
                    NAME
                </td>
                <td colspan="6" class="border-top">
                    <?php echo $row['tenant_fullname']; ?>
                </td>
            </tr>
            <tr>
                <td class="border-top border-right v-middle" rowspan="2">
                    ADDRESS
                </td>
                <td colspan="6">
                    <?php echo isset($guest['tenant_address']) ? trim($guest['tenant_address']) != '' ? $guest['tenant_address'] : '&nbsp;'  : '&nbsp;'; ?>
                </td>
            </tr>
            <tr>
                <td class="border-right" colspan="2">CITY :&nbsp;<?php echo isset($guest) ? $guest['tenant_city'] : ''; ?></td>
                <td class="border-right" colspan="2">POSTAL CODE :&nbsp;<?php echo isset($guest) ? $guest['tenant_postalcode'] : ''; ?></td>
                <td colspan="2">COUNTRY :&nbsp;<?php echo isset($guest) ? $guest['tenant_country'] : ''; ?></td>
            </tr>
            <tr>
                <td class="v-middle border-top border-right">
                    PHONE
                </td>
                <td colspan="6" class="border-top">
                    <?php echo isset($guest) ? nl2br($guest['tenant_phone']) : ''; ?>
                </td>
            </tr>
            <tr>
                <td class="v-middle border-top border-right">
                    MOBILE
                </td>
                <td colspan="6" class="border-top">
                    <?php echo isset($guest) ? nl2br($guest['tenant_cellular']) : ''; ?>
                </td>
            </tr>
            <tr>
                <td class="v-middle border-top border-right">
                    EMAIL
                </td>
                <td colspan="6" class="border-top">
                    <?php echo isset($guest) ? nl2br($guest['tenant_email']) : ''; ?>
                </td>
            </tr>
            <tr>
                <td class="v-middle border-top border-right">
                    NATIONALITY
                </td>
                <td colspan="6" class="border-top">
                    <?php echo isset($guest) ? $guest['tenant_nationality'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td class="v-middle border-top border-right">
                    DATE OF BIRTH
                </td>
                <td colspan="6" class="border-top">
                    <?php
                    if(isset($guest)){
                        if(date('Y', strtotime(ymd_from_db($guest['tenant_dob']))) > 1970 && trim($guest['tenant_pob']) != ''){
                            $birth_caption = '';
                            if(trim($guest['tenant_pob']) != ''){
                                $birth_caption .= $guest['tenant_pob'] . ', ';
                            }
                            echo $birth_caption . dmy_from_db($guest['tenant_dob']);
                        }
                    }
                    ?>
                </td>
            </tr>
        </tbody>

    </table>

    <table width="100%" class="table_main">
        <tbody>
            <tr>
                <td class="border-top border-right v-middle" rowspan="2" width="140px;">
                    <?php
                        if(isset($guest)){
                            if($guest['id_type'] == 1){
                                echo 'ID Card No.';
                            }else if($guest['id_type'] == 2){
                                echo 'PASSPORT';
                            }else if($guest['id_type'] == 3){
                                echo 'KITAS';
                            }else{
                                echo 'KTP / ID NO.';
                            }
                        }else{
                            echo 'Passport/KITAS/ID Card No.';
                        }
                    ?>
                </td>
                <td colspan="6">
                    <?php echo isset($guest) ? $guest['passport_no'] : ''; ?>
                </td>
            </tr>
            <tr>
                <td class="border-right" colspan="3">PLACE OF ISSUE :&nbsp;<?php echo isset($guest) ? $guest['passport_issuedplace'] : ''; ?></td>
                <td class="border-right" colspan="3">DATE OF ISSUE :&nbsp;<?php echo isset($guest) ? trim($guest['passport_issueddate']) != '' && trim($guest['passport_issuedplace']) != '' ? dmy_from_db($guest['passport_issueddate']) : '' : ''; ?></td>
            </tr>
        </tbody>
    </table>

    <table width="100%" class="table_main">
        <thead>
        <tr>
            <th colspan="7" class="border-top border-right v-middle"><h2>CORPORATE</h2></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="v-middle border-top border-right" width="140px;">
                COMPANY
            </td>
            <td colspan="6" class="border-top">
                <?php echo isset($company['company_name']) ? $company['company_name'] : ''; ?>
            </td>
        </tr>
        <tr>
            <td class="border-top border-right v-middle" >
                ADDRESS
            </td>
            <td colspan="6">
                <?php echo isset($company['company_address']) ? nl2br($company['company_address']) : '' ; ?>
            </td>
        </tr>
        <tr>
            <td class="border-top border-right v-middle" >
                PHONE
            </td>
            <td colspan="3">
                <?php echo isset($company) ? nl2br($company['company_phone']) . (trim($company['company_cellular']) != '' ? '<br>' . nl2br($company['company_cellular']) : '') : ''; ?>
            </td>
            <td colspan="3" class="border-top border-right v-middle">
                FAX&nbsp;:&nbsp;<?php echo isset($company) ? nl2br($company['company_fax']) : ''; ?>
            </td>
        </tr>
        <tr>
            <td class="border-top border-right v-middle" >
                EMAIL
            </td>
            <td colspan="6">
                <?php echo isset($company) ? nl2br($company['company_email']) : '' ; ?>
            </td>
        </tr>
        <tr>
            <td class="border-top border-right v-middle" >
                CONTACT PERSON
            </td>
            <td colspan="6">
                <?php echo isset($company) ? nl2br($company['company_pic_name']) : '' ; ?>
            </td>
        </tr>

        </tbody>
    </table>

    <!--table width="100%" class="table_main">
        <thead>
        <tr>
            <th colspan="7" class="border-top border-right v-middle"><h2>PERSONAL STAFF REGISTRATION</h2></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="border-top border-right v-middle" width="140px;">
                NAME
            </td>
            <td colspan="6">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td class="border-top border-right v-middle" >
                ID CARD NO
            </td>
            <td colspan="6">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td class="border-top border-right v-middle" >
                POSITION
            </td>
            <td colspan="6">
                &nbsp;
            </td>
        </tr>
        </tbody>
    </table -->

    <table style="margin-top: 5px;" id="remark_note">
        <!--tr>
            <td><i><strong>TERMS AND CONDITIONS :</strong></i></td>
        </tr-->
        <tr>
            <td width="480px" style="padding-right: 20px;text-align: justify;font-weight: bold" >
                <p><?php echo $profile['signature_note']; ?></p>
            </td>
        </tr>

    </table>
    <table style="margin: 5px!important;padding: 5px;!important" >
        <tr>
            <td class="text-center"><i>Thank you for staying with us</i></td>
        </tr>
    </table>
    <table style="margin-top: 10px;padding-top: 10px;" class="table_sign">
        <tr>
            <td width="100px" class="text-center"><?php echo 'Jakarta, ' . date('j F Y'); ?></td>
            <td width="200px" class="text-center">&nbsp;</td>
            <td width="100px" class="text-center">&nbsp;</td>
        </tr>
        <tr>
            <th width="100px" class="text-center">Guest Signature</th>
            <th width="200px" class="text-center">&nbsp;</th>
            <th width="100px" class="text-center"></th>
        </tr>
        <tr>
            <td colspan="3" style="line-height: 100px;">&nbsp;</td>
        </tr>
        <tr>
            <td width="100px" class="text-center"><?php echo isset($guest) ? $guest['tenant_fullname'] : '( ' . str_repeat('.',35) . ' )' ?></td>
            <td width="200px" class="text-center">&nbsp;</td>
            <td width="100px" class="text-center"><?php echo ''; ?></td>
        </tr>
    </table>
</div>
<div id="page_footer"><p><?php echo nl2br($profile['company_address']); ?></p></div></body></html>