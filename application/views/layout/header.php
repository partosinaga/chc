<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>The Pakubuwono Development</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta http-equiv="refresh" content="7200; <?php echo base_url('login/logout'); ?>">
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <!--link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/-->
    <link href="<?php echo base_url(); ?>assets/global/css/open-sans.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap/css/bootstrap.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"
          rel="stylesheet" type="text/css"/>

    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <?php
    if (isset($style)) {
        if (count($style) > 0) {
            foreach ($style as $row_style) {
                echo '<link rel="stylesheet" type="text/css" href="' . $row_style . '"/>';
            }
        }
    }
    ?>
    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?php echo base_url(); ?>assets/global/css/components.css" id="style_components" rel="stylesheet"
          type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link id="style_color" href="<?php echo base_url(); ?>assets/admin/layout/css/themes/light.css" rel="stylesheet"
          type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->

    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
    <!-- BEGIN CORE PLUGINS -->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url();?>assets/global/plugins/respond.min.js"></script>
    <script src="<?php echo base_url();?>assets/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
    <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js"
            type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap/js/bootstrap.min.js"
            type="text/javascript"></script>
    <script
        src="<?php echo base_url(); ?>/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"
        type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
            type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/uniform/jquery.uniform.min.js"
            type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"
            type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <?php
    if (isset($script)) {
        if (count($script) > 0) {
            foreach ($script as $row_script) {
                echo '<script type="text/javascript" src="' . $row_script . '"></script>';
            }
        }
    }
    ?>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script>
        var js_base_url = '<?php echo base_url();?>';
    </script>
    <script src="<?php echo base_url(); ?>assets/global/scripts/metronic.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/admin/layout/scripts/demo.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/admin/layout/scripts/demo.js" type="text/javascript"></script>
    <?php
    if (isset($custom_script)) {
        if (count($custom_script) > 0) {
            foreach ($custom_script as $row_custom_script) {
                echo '<script type="text/javascript" src="' . $row_custom_script . '"></script>';
            }
        }
    }
    ?>
    <!-- END PAGE LEVEL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            Metronic.init(); // init metronic core componets
            Layout.init(); // init layout
            //Demo.init(); // init demo features
            <?php
           if(isset($init_app)){
             if(count($init_app) > 0){
               foreach($init_app as $row_init_app){
                 echo $row_init_app;
               }
             }
           }
           ?>

            var resize_side_bar_menu = function () {
                var _height = $(window).height(); // window.screen.availHeight;
                var _width = $(window).width(); //window.screen.availWidth;
                //console.log(" _h " + _height + " _w " + _width);
                if (_width <= 1024) {
                    //console.log(" _h " + _height + " _w " + _width);
                    $('body').addClass("page-sidebar-closed");
                    $('ul.page-sidebar-menu').addClass("page-sidebar-menu-closed");
                    $('.sidebar-toggler').addClass("hide");
                }
            }

            resize_side_bar_menu();
        });
    </script>
    <!-- END JAVASCRIPTS -->

    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/tpd.ico"/>

    <script>
        $(document).ready(function (){
            $('.date-picker').datepicker({
                autoclose: true
            });

        })
    </script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-header-fixed page-quick-sidebar-over-content ">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="<?php echo base_url(); ?> ">
                <img src="<?php echo base_url(); ?>assets/admin/layout/img/logo_dwijaya.png" alt="logo"
                     class="logo-default"/>
            </a>

            <div class="menu-toggler sidebar-toggler hide">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse"
           data-target=".navbar-collapse">
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <!-- BEGIN NOTIFICATION DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <!-- <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                  <i class="icon-bell"></i>
                  <span class="badge badge-default">
                  7 </span>
                  </a>
                  <ul class="dropdown-menu">
                    <li class="external">
                      <h3><span class="bold">12 pending</span> notifications</h3>
                      <a href="extra_profile.html">view all</a>
                    </li>
                    <li>
                      <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                        <li>
                          <a href="javascript:;">
                          <span class="time">just now</span>
                          <span class="details">
                          <span class="label label-sm label-icon label-success">
                          <i class="fa fa-plus"></i>
                          </span>
                          New user registered. </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="time">3 mins</span>
                          <span class="details">
                          <span class="label label-sm label-icon label-danger">
                          <i class="fa fa-bolt"></i>
                          </span>
                          Server #12 overloaded. </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="time">10 mins</span>
                          <span class="details">
                          <span class="label label-sm label-icon label-warning">
                          <i class="fa fa-bell-o"></i>
                          </span>
                          Server #2 not responding. </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="time">14 hrs</span>
                          <span class="details">
                          <span class="label label-sm label-icon label-info">
                          <i class="fa fa-bullhorn"></i>
                          </span>
                          Application error. </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="time">2 days</span>
                          <span class="details">
                          <span class="label label-sm label-icon label-danger">
                          <i class="fa fa-bolt"></i>
                          </span>
                          Database overloaded 68%. </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="time">3 days</span>
                          <span class="details">
                          <span class="label label-sm label-icon label-danger">
                          <i class="fa fa-bolt"></i>
                          </span>
                          A user IP blocked. </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="time">4 days</span>
                          <span class="details">
                          <span class="label label-sm label-icon label-warning">
                          <i class="fa fa-bell-o"></i>
                          </span>
                          Storage Server #4 not responding dfdfdfd. </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="time">5 days</span>
                          <span class="details">
                          <span class="label label-sm label-icon label-info">
                          <i class="fa fa-bullhorn"></i>
                          </span>
                          System Error. </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="time">9 days</span>
                          <span class="details">
                          <span class="label label-sm label-icon label-danger">
                          <i class="fa fa-bolt"></i>
                          </span>
                          Storage server failed. </span>
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </li> -->
                <!-- END NOTIFICATION DROPDOWN -->
                <!-- BEGIN INBOX DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <!-- <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<i class="icon-envelope-open"></i>
					<span class="badge badge-default">
					4 </span>
					</a>
					<ul class="dropdown-menu">
						<li class="external">
							<h3>You have <span class="bold">7 New</span> Messages</h3>
							<a href="page_inbox.html">view all</a>
						</li>
						<li>
							<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
								<li>
									<a href="inbox.html?a=view">
									<span class="photo">
									<img src="<?php echo base_url(); ?>assets/admin/layout/img/avatar.png" class="img-circle" alt="">
									</span>
									<span class="subject">
									<span class="from">
									Lisa Wong </span>
									<span class="time">Just Now </span>
									</span>
									<span class="message">
									Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh... </span>
									</a>
								</li>
								<li>
									<a href="inbox.html?a=view">
									<span class="photo">
									<img src="<?php echo base_url(); ?>assets/admin/layout/img/avatar.png" class="img-circle" alt="">
									</span>
									<span class="subject">
									<span class="from">
									Richard Doe </span>
									<span class="time">16 mins </span>
									</span>
									<span class="message">
									Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
									</a>
								</li>
								<li>
									<a href="inbox.html?a=view">
									<span class="photo">
									<img src="<?php echo base_url(); ?>assets/admin/layout/img/avatar.png" class="img-circle" alt="">
									</span>
									<span class="subject">
									<span class="from">
									Bob Nilson </span>
									<span class="time">2 hrs </span>
									</span>
									<span class="message">
									Vivamus sed nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
									</a>
								</li>
								<li>
									<a href="inbox.html?a=view">
									<span class="photo">
									<img src="<?php echo base_url(); ?>assets/admin/layout/img/avatar.png" class="img-circle" alt="">
									</span>
									<span class="subject">
									<span class="from">
									Lisa Wong </span>
									<span class="time">40 mins </span>
									</span>
									<span class="message">
									Vivamus sed auctor 40% nibh congue nibh... </span>
									</a>
								</li>
								<li>
									<a href="inbox.html?a=view">
									<span class="photo">
									<img src="<?php echo base_url(); ?>assets/admin/layout/img/avatar.png" class="img-circle" alt="">
									</span>
									<span class="subject">
									<span class="from">
									Richard Doe </span>
									<span class="time">46 mins </span>
									</span>
									<span class="message">
									Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</li> -->
                <!-- END INBOX DROPDOWN -->
                <!-- BEGIN TODO DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <!-- <li class="dropdown dropdown-extended dropdown-tasks" id="header_task_bar">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                  <i class="icon-calendar"></i>
                  <span class="badge badge-default">
                  3 </span>
                  </a>
                  <ul class="dropdown-menu extended tasks">
                    <li class="external">
                      <h3>You have <span class="bold">12 pending</span> tasks</h3>
                      <a href="page_todo.html">view all</a>
                    </li>
                    <li>
                      <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                        <li>
                          <a href="javascript:;">
                          <span class="task">
                          <span class="desc">New release v1.2 </span>
                          <span class="percent">30%</span>
                          </span>
                          <span class="progress">
                          <span style="width: 40%;" class="progress-bar progress-bar-success" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">40% Complete</span></span>
                          </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="task">
                          <span class="desc">Application deployment</span>
                          <span class="percent">65%</span>
                          </span>
                          <span class="progress">
                          <span style="width: 65%;" class="progress-bar progress-bar-danger" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">65% Complete</span></span>
                          </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="task">
                          <span class="desc">Mobile app release</span>
                          <span class="percent">98%</span>
                          </span>
                          <span class="progress">
                          <span style="width: 98%;" class="progress-bar progress-bar-success" aria-valuenow="98" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">98% Complete</span></span>
                          </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="task">
                          <span class="desc">Database migration</span>
                          <span class="percent">10%</span>
                          </span>
                          <span class="progress">
                          <span style="width: 10%;" class="progress-bar progress-bar-warning" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">10% Complete</span></span>
                          </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="task">
                          <span class="desc">Web server upgrade</span>
                          <span class="percent">58%</span>
                          </span>
                          <span class="progress">
                          <span style="width: 58%;" class="progress-bar progress-bar-info" aria-valuenow="58" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">58% Complete</span></span>
                          </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="task">
                          <span class="desc">Mobile development</span>
                          <span class="percent">85%</span>
                          </span>
                          <span class="progress">
                          <span style="width: 85%;" class="progress-bar progress-bar-success" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">85% Complete</span></span>
                          </span>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:;">
                          <span class="task">
                          <span class="desc">New UI release</span>
                          <span class="percent">38%</span>
                          </span>
                          <span class="progress progress-striped">
                          <span style="width: 38%;" class="progress-bar progress-bar-important" aria-valuenow="18" aria-valuemin="0" aria-valuemax="100"><span class="sr-only">38% Complete</span></span>
                          </span>
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </li> -->
                <!-- END TODO DROPDOWN -->
                <!-- BEGIN USER LOGIN DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <li class="dropdown dropdown-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                       data-close-others="true">
                        <img alt="" class="img-circle"
                             src="<?php echo base_url(); ?>assets/admin/layout/img/avatar.png"/>
					<span class="username username-hide-on-mobile">
					<?php echo my_sess('user_fullname'); ?> </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <a href="<?php echo base_url('home/home/profile.tpd'); ?>">
                                <i class="icon-user"></i> My Profile </a>
                        </li>
                        <li class="divider">
                        </li>
                        <li>
                            <a href="<?php echo base_url('login/logout.tpd'); ?>">
                                <i class="icon-key"></i> Log Out </a>
                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapse">
            <!-- BEGIN SIDEBAR MENU -->
            <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
            <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
            <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
            <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                <li class="sidebar-toggler-wrapper" style="margin-bottom:10px;">
                    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                    <div class="sidebar-toggler">
                    </div>
                    <!-- END SIDEBAR TOGGLER BUTTON -->
                </li>
                <?php
                $menu = '';
                $where_in = ' AND menu_id = 0 ';

                if (count(session_role()) > 0) {
                    $where_in = ' AND menu_id IN (' . implode(',', array_keys(session_role())) . ' )';
                }

                $i = 1;
                $qry1 = $this->db->query("SELECT * FROM ms_menu WHERE parent_id = 0 AND status = " . STATUS_NEW . " " . $where_in . " ORDER BY sorting");
                $jml_row1 = $qry1->num_rows();
                foreach ($qry1->result() as $row1) {
                    $qry2 = $this->db->query("SELECT * FROM ms_menu WHERE parent_id = " . $row1->menu_id . " AND status = " . STATUS_NEW . " " . $where_in . " ORDER BY sorting");
                    $jml_row2 = $qry2->num_rows();

                    $attr1 = '';
                    if ($jml_row2 > 0) {
                        $link1 = 'javascript:;';
                    } else {
                        $link1 = base_url($row1->module_name . '/' . $row1->controller_name . '.tpd');
                        if ($row1->is_new_tab == 1) {
                            $attr1 = 'target="_blank"';
                        }
                    }

                    $open_it1 = false;
                    if ($this->uri->segment(1) == $row1->module_name) {
                        $open_it1 = true;
                    }

                    $menu .= '<li class="' . ($i == 1 ? 'start' : '') . ' ' . (($open_it1) ? 'active open' : '') . '">
									<a href="' . $link1 . '" ' . $attr1 . '>';
                    if ($row1->menu_icon != '') {
                        $menu .= '<i class="' . $row1->menu_icon . '"></i> ';
                    }

                    $menu .= '<span class="title">' . $row1->menu_name . '</span>';

                    if ($open_it1) {
                        $menu .= '<span class="selected"></span>';
                    }
                    if ($jml_row2 > 0) {
                        $menu .= '<span class="arrow ' . (($open_it1) ? 'open' : '') . '"></span>';
                    }
                    $menu .= '</a>';

                    if ($jml_row2 > 0) {
                        $menu .= '<ul class="sub-menu">';
                    }

                    foreach ($qry2->result() as $row2) {
                        $qry3 = $this->db->query("SELECT * FROM ms_menu WHERE parent_id = " . $row2->menu_id . " AND status = " . STATUS_NEW . " " . $where_in . " ORDER BY sorting");
                        $jml_row3 = $qry3->num_rows();

                        $attr2 = '';
                        if ($jml_row3 > 0) {
                            $link2 = 'javascript:;';
                        } else {
                            $link2 = base_url($row2->module_name . '/' . $row2->controller_name . '/' . $row2->function_name . (trim($row2->function_parameter) != '' ? '/' . trim($row2->function_parameter) : '') . '.tpd');
                            if ($row2->is_new_tab == 1) {
                                $attr2 = 'target="_blank"';
                            }
                        }

                        $open_it2 = false;
                        if ($this->uri->segment(1) == $row2->module_name && $this->uri->segment(2) == $row2->controller_name) {
                            $open_it2 = true;
                        }

                        $menu .= '<li class="' . (($open_it2) ? 'active open' : '') . '">
										    <a href="' . $link2 . '" ' . $attr2 . '>';
                        if ($row2->menu_icon != '') {
                            $menu .= '<i class="' . $row2->menu_icon . '"></i> ';
                        }

                        $menu .= '<span class="title">' . $row2->menu_name . '</span>';

                        if ($open_it2) {
                            $menu .= '<span class="selected"></span>';
                        }
                        if ($jml_row3 > 0) {
                            $menu .= '<span class="arrow ' . (($open_it2) == $row2->module_name ? 'open' : '') . '"></span>';
                        }
                        $menu .= '</a>';

                        if ($jml_row3 > 0) {
                            $menu .= '<ul class="sub-menu">';
                        }

                        foreach ($qry3->result() as $row3) {
                            $open_it3 = false;
                            if ($this->uri->segment(1) == $row3->module_name && $this->uri->segment(2) == $row3->controller_name && $this->uri->segment(3) == $row3->function_name) {
                                $open_it3 = true;
                            }

                            $link3 = base_url($row3->module_name . '/' . $row3->controller_name . '/' . $row3->function_name . (trim($row3->function_parameter) != '' ? '/' . trim($row3->function_parameter) : '') . '.tpd');
                            $attr3 = '';
                            if ($row3->is_new_tab == 1) {
                                $attr3 = 'target="_blank"';
                            }

                            $menu .= '<li class="' . (($open_it3) ? 'active' : '') . '">
											<a href="' . $link3 . '" ' . $attr3 . '>' . ($row3->menu_icon != '' ? '<i class="' . $row3->menu_icon . '"></i> ' : '') . ' ' . $row3->menu_name . '</a>
										</li>';
                        }

                        if ($jml_row3 > 0) {
                            $menu .= '</ul>';
                        }

                        $menu .= '</li>';
                    }

                    if ($jml_row2 > 0) {
                        $menu .= '</ul>';
                    }

                    $menu .= '</li>';

                    $i++;
                }

                echo $menu;
                ?>
            </ul>
            <!-- END SIDEBAR MENU -->
        </div>
    </div>
    <!-- END SIDEBAR -->