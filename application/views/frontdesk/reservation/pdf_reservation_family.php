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
        <td style="vertical-align:top; text-align:center;" colspan="3"><h1><strong>FAMILY REGISTRATION FORM</strong></h1></td>
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
                <th colspan="7" class="border-top border-right v-middle"><h2>GUEST INFORMATION</h2></th>
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
        </tbody>
    </table>

    <table width="100%" class="table_family">
        <thead>
        <tr>
            <th colspan="4" class="border-top border-right v-middle" ><h2>FAMILY MEMBER(S)</h2></th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($families)){
            $i = 1;
            foreach($families as $family){

        ?>
        <tr>
            <td class="v-middle border-top border-right" width="20px">
                <?php echo $i . '.' ?>
            </td>
            <td class="v-middle border-top border-right" width="113px">
                Name
            </td>
            <td class="border-top " >
                <?php echo $family['member_name']; ?>
            </td>
            <td class="v-middle border-top border-right" width="60px">
                <?php echo '[ ' . $family['member_sex'] . ' ]'; ?>
            </td>
        </tr>
        <tr>
            <td class="v-middle border-right" width="20px">
                &nbsp;
            </td>
            <td class="v-middle border-right" width="113px">
                Date Of Birth
            </td>
            <td class="" >
                <?php echo (trim($family['member_pob']) != '' ? $family['member_pob'] . ', ' : '') . (isset($family['member_dob']) ? dmy_from_db($family['member_dob']) : ''); ?>
            </td>
            <td class="v-middle border-right" width="60px">

            </td>
        </tr>
        <tr>
            <td class="v-middle border-right" width="20px">
                &nbsp;
            </td>
            <td class="v-middle border-right" width="113px">
                Relationship
            </td>
            <td class="" colspan="2" >
                <?php echo $family['relationship']; ?>
            </td>
        </tr>
        <?php
                $i++;
            }
        }?>
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