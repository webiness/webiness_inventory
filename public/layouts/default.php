<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo WsUrl::asset('img/favicon.png'); ?>">
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="description" content="Webiness Inventory is online stock inventory managment software"/>
    <meta name="Keywords" content="" />
    <meta name="robots" content="index, follow"/>
    <meta property="og:title" content="Webiness Inventory"/>
    <meta property="og:site_name" content=""/>
    <meta property="og:type" content="website"/>
    <title><?php echo $WsTitle; ?></title>
    
    <link type="text/css" rel="stylesheet" href="<?php echo WsUrl::asset('css/jquery-ui.min.css'); ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo WsUrl::asset('css/jquery-ui.theme.min.css'); ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo WsUrl::asset('css/bootstrap.min.css'); ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo WsUrl::asset('css/bootstrap-theme.min.css'); ?>" />
    <style>
        @media print {
            .no-print, .no-print *{
                display: none !important;
                height: 0;
            }
        }
    </style>
    
    <?php
        $lang = WsLocalize::getLang();
    ?>

    <script type="text/javascript" src="<?php echo WsUrl::asset('js/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/jquery.validate.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/Chart.bundle.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/jquery-ui.min.js'); ?>"></script>
    <?php
    if (WsUrl::asset('js/i18n/datepicker-'.$lang.'.js') !== '') {
    ?>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/i18n/datepicker-'.$lang.'.js'); ?>"></script>
    <?php
    }
    ?>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/webiness.js'); ?>"></script>
</head>

<body>
    
    <?php
        // initialize auth module
        $auth = new WsAuth();
    ?>
    
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">
                    <img width=25 height=25 
                        src="<?php echo WsUrl::asset('img/webiness-box.png'); ?>"
                        alt="<?php echo WsConfig::get('app_name'); ?>"/>
                </a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                <?php
                if ($auth->checkSession()) {
                ?>
                    <li>
                        <a href="<?php echo WsUrl::link('wsauth','edit') ?>">
                            <?php echo $auth->currentUser() ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo WsUrl::link('wsauth','logout') ?>">
                            <?php echo WsLocalize::msg('logout') ?>
                        </a>
                    </li>
                <?php
                } else {
                ?>
                    <li class="right">
                        <a href="<?php echo WsUrl::link('wsauth','login') ?>">
                            <?php echo WsLocalize::msg('login') ?>
                        </a>
                    </li>
                <?php
                }
                ?>
                </ul>
                <ul class="nav navbar-nav">
                    <li><a href="#">Home</a></li>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php echo WsLocalize::msg('Documents'); ?> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu multi-level">
                            <li>
                                <a href="<?php echo WsUrl::link('document', 'edit', array('id' => -1)); ?>">
                                    <?php echo WsLocalize::msg('New document'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo WsUrl::link('document', 'index'); ?>">
                                    <?php echo WsLocalize::msg('Manage documents'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php echo WsLocalize::msg('Inventory'); ?> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo WsUrl::link('product', 'categories'); ?>">
                                    <?php echo WsLocalize::msg('Product categories'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo WsUrl::link('product', 'products'); ?>">
                                    <?php echo WsLocalize::msg('Products'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo WsUrl::link('product', 'inventory_list'); ?>">
                                    <?php echo WsLocalize::msg('Inventory summary'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php echo WsLocalize::msg('Partners'); ?> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo WsUrl::link('partners', 'index'); ?>">
                                    <?php echo WsLocalize::msg('Manage partners'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php echo WsLocalize::msg('Settings'); ?> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo WsUrl::link('site', 'company'); ?>">
                                    <?php echo WsLocalize::msg('Edit company details'); ?>
                                </a>
                            </li>
                            <?php
                            if ($auth->hasPermission('admin')) {
                            ?>
                            <li>
                                <a href="<?php echo WsUrl::link('wsauth','register') ?>">
                                    <?php echo WsLocalize::msg('Add new user') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo WsUrl::link('wsauth','admin') ?>">
                                    <?php echo WsLocalize::msg('Users, roles, permissions') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo WsUrl::link('wsauth','rolePerms') ?>">
                                    <?php echo WsLocalize::msg('Role permissions') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo WsUrl::link('wsauth','userRoles') ?>">
                                    <?php echo WsLocalize::msg('User roles') ?>
                                </a>
                            </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>

    <br/>
    <br/>
    <br/>
    
    <div id="ws_image_preview"></div>
    
    <div class="container-fluid">
        <!-- BREADCRUMBS -->
        <div class="row no-print">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                <?php
                    if (isset($WsBreadcrumbs)) {
                        foreach($WsBreadcrumbs as $text => $url) {
                            if (next($WsBreadcrumbs) == '') {
                                echo '<li class="active">'.$text.'</li>';
                            } else {
                                echo '<li><a href="'
                                    .WsUrl::link($url[0], $url[1]).'">'.
                                    $text.'</a></li>';
                            }
                        }
                    }
                ?>
                </ol>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12">
                <?php echo $WsContent ?>
            </div>
        </div>
        
        <br/>
        <br/>
        
        <?php
            if (WsConfig::get('app_stage') == 'development') {
        ?>
        <div class="row no-print">
            <div class="col-sm-12 col-md-6">
                <div class="alert alert-info">
                    <?php echo
                    '<strong>MEMORY USAGE: </strong>'
                    .WsSTART_MEMORY_USAGE.' kb (s), '
                    .number_format(memory_get_peak_usage() / 1024, 2).' kb (p), '
                    .number_format(memory_get_usage() / 1024, 2).' kb (e)'
                    ?>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="alert alert-info">
                    <?php echo
                    '<strong>EXECUTION TIME: </strong>'
                    .number_format((microtime(true) - WsSTART_TIME), 4).' sec'
                    ?>
                </div>
            </div>
        </div>
        
        <br/>
        <br/>
        
        <?php
            }
        ?>
    </div>
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php echo WsUrl::asset('js/bootstrap.min.js'); ?>"></script>
        <!-- INITIALIZE JavaScript  functions -->
    <script type="text/javascript">
        jQuery("document").ready(function($) {

            jQuery('.webiness_datepicker').datepicker({
                    changeMonth: true,
                    changeYear: true,
                    gotoCurrent: true
                },
                "option", $.datepicker.regional["<?php echo $lang; ?>"]
            );
            
            $(".webiness_numericinput").keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }

                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

        });
    </script>
  </body>
</html>

