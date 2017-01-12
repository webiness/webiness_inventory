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

    <link type="text/css" rel="stylesheet" href="<?php echo WsUrl::asset('css/uikit.almost-flat.min.css'); ?>" />
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
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/uikit.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/jquery.validate.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/Chart.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/webiness.js'); ?>"></script>
</head>

<body>

    <?php
        // initialize auth module
        $auth = new WsAuth();
    ?>

    <nav class="uk-navbar no-print" style="top: 0; position: fixed; width: 100%;">
        <a href="<?php echo WsUrl::link('site', 'index'); ?>" class="uk-navbar-brand">
            <img width=25 height=25
                src="<?php echo WsUrl::asset('img/webiness-box.png'); ?>"
                alt="<?php echo WsConfig::get('app_name'); ?>"/>
        </a>
        <ul class="uk-navbar-nav uk-hidden-small">
            <li>
                <a href="<?php echo WsUrl::link('document', 'index'); ?>">
<?php echo WsLocalize::msg('Documents'); ?></a>
            </li>

            <li class="uk-parent" data-uk-dropdown>
                <a href="#"><?php echo WsLocalize::msg('Inventory'); ?></a>
                <div class="uk-dropdown uk-dropdown-navbar">
                    <ul class="uk-nav uk-nav-navbar">
                        <li>
                            <a href="<?php echo WsUrl::link('product', 'categories'); ?>">
<?php echo WsLocalize::msg('Product categories'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('product', 'products'); ?>">
<?php echo WsLocalize::msg('Products'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('product', 'inactive'); ?>">
<?php echo WsLocalize::msg('Inactive products'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('product', 'inventory_list'); ?>">
<?php echo WsLocalize::msg('Inventory summary'); ?></a>
                        </li>
                    </ul>
                </div>
            </li>

            <li>
                <a href="<?php echo WsUrl::link('partners', 'index'); ?>">
<?php echo WsLocalize::msg('Partners'); ?></a>
            </li>

            <li class="uk-parent" data-uk-dropdown>
                <a href="#"><?php echo WsLocalize::msg('Settings'); ?></a>
                <div class="uk-dropdown uk-dropdown-navbar">
                    <ul class="uk-nav uk-nav-navbar">
                        <li>
                            <a href="<?php echo WsUrl::link('site', 'company'); ?>">
<?php echo WsLocalize::msg('Edit company details'); ?></a>
                        </li>
                        <?php
                        if ($auth->hasPermission('admin')) {
                        ?>
                        <li>
                            <a href="<?php echo WsUrl::link('wsauth','register') ?>">
<?php echo WsLocalize::msg('Add new user') ?></a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('wsauth','admin') ?>">
<?php echo WsLocalize::msg('Users, roles, permissions') ?></a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('wsauth','rolePerms') ?>">
<?php echo WsLocalize::msg('Role permissions') ?></a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('wsauth','userRoles') ?>">
<?php echo WsLocalize::msg('User roles') ?></a>
                        </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
            </li>
        </ul>

        <div class="uk-navbar-flip">
            <ul class="uk-navbar-nav uk-hidden-small">
            <?php
            if ($auth->checkSession()) {
            ?>
                <li class="uk-parent" data-uk-dropdown>
                    <a href="#"><?php echo $auth->currentUser() ?></a>
                    <div class="uk-dropdown uk-dropdown-navbar">
                        <ul class="uk-nav uk-nav-navbar">
                            <li>
                                <a href="<?php echo WsUrl::link('wsauth','edit') ?>">
<?php echo WsLocalize::msg('Edit account') ?></a>
                            </li>
                            <li>
                                <a href="<?php echo WsUrl::link('wsauth','logout') ?>">
<?php echo WsLocalize::msg('logout') ?></a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php
            } else {
            ?>
                <li>
                    <a href="<?php echo WsUrl::link('wsauth','login') ?>">
<?php echo WsLocalize::msg('login') ?></a>
                </li>
            <?php
            }
            ?>
            </ul>
        </div>

        <a href="#small_menu" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
    </nav>

    <div id="small_menu" class="uk-offcanvas no-print">
        <div class="uk-offcanvas-bar">
            <ul class="uk-nav uk-nav-parent-icon" data-uk-nav>
                <li>
                    <a href="<?php echo WsUrl::link('document', 'index'); ?>">
<?php echo WsLocalize::msg('Documents'); ?></a>
                </li>

                <li class="uk-parent">
                    <a href="#"><?php echo WsLocalize::msg('Inventory'); ?></a>
                    <ul class="uk-nav-sub">
                        <li>
                            <a href="<?php echo WsUrl::link('product', 'categories'); ?>">
<?php echo WsLocalize::msg('Product categories'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('product', 'products'); ?>">
<?php echo WsLocalize::msg('Products'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('product', 'inactive'); ?>">
<?php echo WsLocalize::msg('Inactive products'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('product', 'inventory_list'); ?>">
<?php echo WsLocalize::msg('Inventory summary'); ?></a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="<?php echo WsUrl::link('partners', 'index'); ?>">
<?php echo WsLocalize::msg('Partners'); ?></a>
                </li>

                <li class="uk-parent">
                    <a href="#"><?php echo WsLocalize::msg('Settings'); ?></a>
                        <ul class="uk-nav-sub">
                            <li>
                                <a href="<?php echo WsUrl::link('site', 'company'); ?>">
<?php echo WsLocalize::msg('Edit company details'); ?></a>
                            </li>
                            <?php
                            if ($auth->hasPermission('admin')) {
                            ?>
                            <li>
                                <a href="<?php echo WsUrl::link('wsauth','register') ?>">
<?php echo WsLocalize::msg('Add new user') ?></a>
                            </li>
                            <li>
                                <a href="<?php echo WsUrl::link('wsauth','admin') ?>">
<?php echo WsLocalize::msg('Users, roles, permissions') ?></a>
                            </li>
                            <li>
                                <a href="<?php echo WsUrl::link('wsauth','rolePerms') ?>">
<?php echo WsLocalize::msg('Role permissions') ?></a>
                            </li>
                            <li>
                                <a href="<?php echo WsUrl::link('wsauth','userRoles') ?>">
<?php echo WsLocalize::msg('User roles') ?></a>
                            </li>
                            <?php
                            }
                            ?>
                        </ul>
                </li>

                <?php
                if ($auth->checkSession()) {
                ?>
                <li class="uk-parent">
                    <a href="#"><?php echo $auth->currentUser() ?></a>
                    <ul class="uk-nav-sub">
                        <li>
                            <a href="<?php echo WsUrl::link('wsauth','edit') ?>">
<?php echo WsLocalize::msg('Edit account') ?></a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('wsauth','logout') ?>">
<?php echo WsLocalize::msg('logout') ?></a>
                        </li>
                    </ul>
                </li>
                <?php
                } else {
                ?>
                <li>
                    <a href="<?php echo WsUrl::link('wsauth','login') ?>">
<?php echo WsLocalize::msg('login') ?></a>
                </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>

    <br/>
    <br/>
    <br/>

    <div id="ws_image_preview"></div>

    <div class="uk-grid uk-grid-small no-print">
        <div class="uk-width-small-1-1 uk-width-medium-9-10 uk-container-center">
            <ul class="uk-breadcrumb">
            <!-- BREADCRUMBS -->
                <?php
                    if (isset($WsBreadcrumbs)) {
                        foreach($WsBreadcrumbs as $text => $url) {
                            if (next($WsBreadcrumbs) == '') {
                                echo '<li class="uk-active"><span>'
                                    .$text
                                    .'</span></li>';
                            } else {
                                echo '<li><a href="'
                                    .WsUrl::link($url[0], $url[1]).'">'.
                                    $text.'</a></li>';
                            }
                        }
                    }
                ?>
            </ul>
            <hr/>
        </div>
    </div>

    <div class="uk-grid uk-grid-small">
        <div class="uk-width-small-1-1 uk-width-medium-9-10 uk-container-center">
            <?php
                echo $WsContent;
                unset ($lang, $auth, $WsTitle, $WsBreadcrumbs, $WsContent);
            ?>
        </div>
    </div>

    <?php
        if (WsConfig::get('app_stage') == 'development') {
    ?>
    <br/>
    <br/>
    <div class="uk-grid no-print uk-container-center uk-grid-small">
        <div class="uk-width-small-1-1 uk-width-medium-5-10">
            <div class="uk-alert uk-alert-warning">
                <?php echo
                '<strong>MEMORY USAGE: </strong>'
                .WsSTART_MEMORY_USAGE.' kb (s), '
                .number_format(memory_get_peak_usage(false) / 1024, 2).' kb (p), '
                .number_format(memory_get_usage(false) / 1024, 2).' kb (e)'
                ?>
            </div>
        </div>
        <div class="uk-width-small-1-1 uk-width-medium-5-10">
            <div class="uk-alert uk-alert-warning">
                <?php echo
                '<strong>EXECUTION TIME: </strong>'
                .number_format((microtime(true) - WsSTART_TIME), 4).' sec'
                ?>
            </div>
        </div>
    </div>
    <?php
        }
    ?>

    <!-- INITIALIZE JavaScript  functions -->
    <script type="text/javascript">
        jQuery("document").ready(function($) {
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

