<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
    <title>MIT || Mail Notification</title>
    <style type="text/css">
        html { -webkit-text-size-adjust:none; -ms-text-size-adjust: none;}
        @media only screen and (max-device-width: 680px), only screen and (max-width: 680px) {
            *[class="table_width_100"] {
                width: 96% !important;
            }
            *[class="border-right_mob"] {
                border-right: 1px solid #dddddd;
            }
            *[class="mob_100"] {
                width: 100% !important;
            }
            *[class="mob_center"] {
                text-align: center !important;
            }
            *[class="mob_center_bl"] {
                float: none !important;
                display: block !important;
                margin: 0px auto;
            }
            .iage_footer a {
                text-decoration: none;
                color: #929ca8;
            }
            img.mob_display_none {
                width: 0px !important;
                height: 0px !important;
                display: none !important;
            }
            img.mob_width_50 {
                width: 40% !important;
                height: auto !important;
            }
        }
        .table_width_100 {
            width: 680px;
        }
    </style>
</head>

<body style="padding: 0px; margin: 0px;">
<div id="mailsub" class="notification" align="center">

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="min-width: 320px;">
        <tr>
            <td align="center" bgcolor="#eff3f8">

                <!--[if gte mso 10]>
                <table width="680" border="0" cellspacing="0" cellpadding="0">
                    <tr><td>
                <![endif]-->

                <table border="0" cellspacing="0" cellpadding="0" class="table_width_100" width="100%" style="max-width: 680px; min-width: 300px;">
                    <!--header -->
                    <tr>
                        <td align="center" bgcolor="#eff3f8">
                            <table width="96%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="left">
                                        <div class="mob_center_bl" style="float: left; display: inline-block; width: 115px;">
                                            <table class="mob_center" width="115" border="0" cellspacing="0" cellpadding="0" align="left" style="border-collapse: collapse;">
                                                <tr>
                                                    <td align="left" valign="middle">
                                                        <div style="height: 15px; line-height: 10px; font-size: 10px;">&nbsp;</div>
                                                        <table width="115" border="0" cellspacing="0" cellpadding="0" >
                                                            <tr>
                                                                <td align="left" valign="top" class="mob_center">
                                                                    <a href="<?php echo base_url();?>" style="color: #596167; font-family: Arial, Helvetica, sans-serif; font-size: 13px;">
                                                                        <font face="Arial, Helvetica, sans-seri; font-size: 13px;" size="3" color="#596167">
                                                                            <img src="<?php echo base_url('assets/admin/layout/img/logo_email.png');?>" alt="MIT" border="0" style="display: block;" /></font></a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <!--[if gte mso 10]>
                                        </td>
                                        <td align="right">
                                        <![endif]-->
                                    </td>
                                </tr>
                            </table>
                            <div style="height: 15px; line-height: 10px; font-size: 10px;">&nbsp;</div>
                        </td>
                    </tr>
                    <!--header END-->

                    <!--content 1 -->
                    <tr>
                        <td align="left" bgcolor="#ffffff" >
                            <table width="98%" border="0" cellspacing="0" cellpadding="0" style="margin-left:20px;margin-right:20px;">
                                <tr>
                                    <td align="left">
                                        <div style="height: 20px; line-height: 10px; font-size: 10px;">&nbsp;</div>
                                        <div style="line-height: 30px;">
                                            <font face="Arial, Helvetica, sans-serif" size="5" color="#57697e" style="font-size: 17px;">
												<span style="font-family: Arial, Helvetica, sans-serif; font-size: 17px; color: #57697e;">
													{title}
												</span></font>
                                        </div>
                                        <div style="height: 20px; line-height: 10px; font-size: 10px;">&nbsp;</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">
                                        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                                            <tr><td align="left">
                                                    <div style="line-height: 24px;">
                                                        <font face="Arial, Helvetica, sans-serif" size="4" color="#57697e" style="font-size: 16px;">
														<span style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #57697e;">
															{header}
														</span></font>
                                                    </div>
                                                    <div style="height: 20px; line-height:20px;">&nbsp;</div>
                                                    <div style="line-height: 24px;">
                                                        <font face="Arial, Helvetica, sans-serif" size="4" color="#57697e" style="font-size: 16px;">
														<span style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #57697e;">
															{detail}
														</span></font>
                                                    </div>
                                                </td></tr>
                                        </table>
                                        <div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left">
                                        <div style="line-height:10px;">
                                            {action}
                                        </div>
                                        <div style="height: 0px; ">&nbsp;</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!--content 1 END-->
                    <!--links END-->

                    <!--footer -->
                    <tr>
                        <td class="iage_footer" align="center" bgcolor="#eff3f8">
                            <div style="height: 20px; line-height: 20px; font-size: 10px;">&nbsp;</div>

                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <font face="Arial, Helvetica, sans-serif" size="3" color="#96a5b5" style="font-size: 13px;">
											<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #96a5b5;">
												<?php echo date('Y');?> &copy; Monstera Inti Teknologi.
											</span></font>
                                    </td>
                                </tr>
                            </table>
                            <div style="height: 50px; line-height: 20px; font-size: 10px;">&nbsp;</div>
                        </td>
                    </tr>
                    <!--footer END-->
                </table>
                <!--[if gte mso 10]>
                </td></tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </table>
</div>
</body>
</html>
