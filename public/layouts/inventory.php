<!DOCTYPE html>
<html>

<head profile="http://www.w3.org/2005/10/profile">
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

    <link type="text/css" rel="stylesheet" href="<?php echo WsUrl::asset('css/webiness.css'); ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo WsUrl::asset('css/jquery-ui.min.css'); ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo WsUrl::asset('css/jquery-ui.theme.min.css'); ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo WsUrl::asset('css/select2.min.css'); ?>" />

    <?php
        $lang = substr(filter_input(INPUT_SERVER,
            'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING), 0,2);
    ?>

    <script type="text/javascript" src="<?php echo WsUrl::asset('js/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/jquery.validate.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/Chart.bundle.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/jquery-ui.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/i18n/datepicker-'.$lang.'.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/select2.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/i18n/'.$lang.'.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo WsUrl::asset('js/webiness.js'); ?>"></script>
</head>

<body>
    <?php
        // initialize auth module
        $auth = new WsAuth();
    ?>
    <!-- TITLE -->
    <div class="row no-print">
        <div class="column column-12 site-title">
            <div style="display: table;" class="column column-10 column-offset-1">
                <img
                    width=80
                    height=80
                    style="vertical-align: middle; display: table-cell; margin: 15px;"
                    src="<?php echo WsUrl::asset('img/webiness.png'); ?>"/>
                <div
                    style="vertical-align: middle; display: table-cell;">
                    <h1 style="color: #d2dde2">
                        <?php echo WsLocalize::msg('Webiness Inventory'); ?>
                    </h1>
                    <h3 style="color: #d2dde2">
                        <?php echo WsLocalize::msg('- easely manage stock inventory -'); ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>
    <!-- HEADER -->
    <div class="row no-print">
        <div class="column column-10 column-offset-1 ws-header">
            <label for="show-menu" class="show-menu">
                <?php echo WsConfig::get('app_name') ?>
            </label>
            <input type="checkbox" id="show-menu" role="button">
            <ul>
                <li>
                    <a href="<?php echo WsUrl::link('document', 'index'); ?>">
                        <?php echo WsLocalize::msg('Documents'); ?>
                    </a>
                    <ul>
                        <li>
                            <a href="<?php echo WsUrl::link('document', 'edit', array('id' => -1)); ?>">
                                <?php echo WsLocalize::msg('New document'); ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="#">
                        <?php echo WsLocalize::msg('Inventory'); ?>
                    </a>
                    <ul>
                        <li>
                            <a href="<?php echo WsUrl::link('item', 'categories'); ?>">
                                <?php echo WsLocalize::msg('Categories of items'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('item', 'items'); ?>">
                                <?php echo WsLocalize::msg('Stock items'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo WsUrl::link('item', 'inventory_list'); ?>">
                                <?php echo WsLocalize::msg('Inventory list'); ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="<?php echo WsUrl::link('partners', 'index'); ?>">
                        <?php echo WsLocalize::msg('Partners'); ?>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="<?php echo WsUrl::link('site', 'company'); ?>">
                        <?php echo WsLocalize::msg('My Company'); ?>
                    </a>
                </li>
            </ul>
            <ul class="right">
            <?php
                if ($auth->checkSession()) {
                ?>
                <li class="right">
                    <a href="<?php echo WsUrl::link('wsauth','edit') ?>">
                        <?php echo $auth->currentUser() ?>
                    </a>
                    <ul>
                        <li>
                            <a href="<?php echo WsUrl::link('wsauth','logout') ?>">
                                <?php echo WsLocalize::msg('logout') ?>
                            </a>
                        </li>
                        <?php
                        if ($auth->hasPermission('admin')) {
                        ?>
                        <li>
                            <a href="<?php echo WsUrl::link('wsauth','admin') ?>">
                                <?php echo WsLocalize::msg('User Accounts') ?>
                            </a>
                        </li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
                } else {
                ?>
                <li class="right">
                    <a href="<?php echo WsUrl::link('wsauth','login') ?>">
                        <?php echo WsLocalize::msg('login') ?>
                    </a>
                </li>
                <li class="right">
                    <a href="<?php echo WsUrl::link('wsauth','register') ?>">
                        <?php echo WsLocalize::msg('register') ?>
                    </a>
                </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>

    <div id="ws_image_preview"></div>

    <!-- CONTENT -->
    <section class="content">
        <div class="row no-print">
            <div class="column column-10 column-offset-1">
                <?php
                    if (isset($WsBreadcrumbs)) {
                        foreach($WsBreadcrumbs as $text => $url) {
                            if (next($WsBreadcrumbs) == '') {
                                echo $text;
                            } else {
                                echo '<a href="'
                                    .WsUrl::link($url[0], $url[1]).'">'.
                                    $text.'</a>'.' / ';
                            }
                        }
                    }
                ?>
            </div>
        </div>

        <div class="row">
            <div class="column column-12">
                <?php echo $WsContent ?>
            </div>
        </div>

        <!-- DEBUG -->
        <?php
            if (WsConfig::get('app_stage') == 'development') {
        ?>
        <br/>
        <br/>
        <div class="row no-print">
            <div class="column column-6">
                <div class="callout warning">
                    <?php echo
                    '<strong>MEMORY USAGE: </strong>'
                    .WsSTART_MEMORY_USAGE.' kb (s), '
                    .number_format(memory_get_peak_usage() / 1024, 2).' kb (p), '
                    .number_format(memory_get_usage() / 1024, 2).' kb (e)'
                    ?>
                </div>
            </div>
            <div class="column column-6">
                <div class="callout warning">
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

        <!-- FOOTER -->
        <br/>
        <div class="row no-print">
            <div class="column column-12 text-center">
                <small>&copy; <?php echo date('Y') ?>. Webiness ltd.</small>
                <small> | technical support: <a mailto="bojan.kajfes@gmail.com">Bojan Kajfeš</a></small>
            </div>
        </div>
    </section>

    <!-- INITIALIZE JavaScript  functions -->
    <script type="text/javascript">
        jQuery("document").ready(function($) {

            jQuery('.webiness_datepicker').datepicker({
                    changeMonth: true,
                    changeYear: true,
                    gotoCurrent: true,
                },
                "option", $.datepicker.regional["<?php echo $lang; ?>"]
            );

            var nav = $('.ws-header');
            var top = nav.position().top;
            var orig_width = nav.width();
            var orig_offset = nav.css("margin-left");
            $(window).scroll(function () {
                if ($(this).scrollTop() > top) {
                    nav.css({
                        top: 0,
                        left: 0,
                        'z-index': 999,
                        position: 'fixed',
                        display: 'block',
                        'margin-left': 0,
                        'width': '100%',
                        'transition': '0.3s all',
                        '-moz-transition': '0.3s all',
                        '-webkit-transition': '0.3s all',
                        'opacity': '.90'
                    });
                } else {
                    nav.css({
                        top: '',
                        left: '',
                        'z-index': '',
                        position: 'relative',
                        display: 'inline-block',
                        'margin-left': orig_offset,
                        'width': orig_width,
                        'transition': '0.3s all',
                        '-moz-transition': '0.3s all',
                        '-webkit-transition': '0.3s all',
                        'opacity': '1'
                    });

                }
            });

            $(".webiness_numericinput").keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
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
