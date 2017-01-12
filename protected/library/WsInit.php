<?php
/* define some framework constants */
/**
 * WS_AUTH_USER_EXISTS => user for WsAuth module allready exists
 */
define('WS_AUTH_USER_EXISTS', 101);
/**
 * WS_AUTH_NOT_VERIFIED => user account not verified
 */
define('WS_AUTH_NOT_VERIFIED', 102);
/**
 * WS_AUTH_NO_MATCH => user name and paswword did not match
 */
define('WS_AUTH_NO_MATCH', 103);
/**
 * WS_AUTH_NOT_ACTIVE => user account not active
 */
define('WS_AUTH_NOT_ACTIVE', 104);
/**
 * WS_AUTH_LOGIN_OK => user succesfuly loged in
 */
define('WS_AUTH_LOGIN_OK', 105);


/**
 * autoload all neded classes from framework and web application
 */
function wsAutoloader($class)
{
    // load all framework classes
    if (file_exists(
        WsROOT.'/protected/library/'.$class.'.class.php')) {
        require WsROOT.'/protected/library/'.$class.'.class.php';
        return;
    }

    // autoload all internal Controllers and Models
    if (file_exists(
        WsROOT.'/protected/library/controllers/'.$class.'.php')) {
        require WsROOT.'/protected/library/controllers/'.$class.'.php';
        return;
    }
    if (file_exists(
        WsROOT.'/protected/library/models/'.$class.'.php')) {
        require WsROOT.'/protected/library/models/'.$class.'.php';
        return;
    }

    // load all application Controlers
    if (file_exists(
        WsROOT.'/application/controllers/'.$class.'.php')) {
        require WsROOT.'/application/controllers/'.$class.'.php';
        return;
    }

    // load all application Models
    if (file_exists(WsROOT.'/application/models/'.$class.'.php')) {
        require WsROOT.'/application/models/'.$class.'.php';
        return;
    }
}
spl_autoload_register('wsAutoloader');

/* start or resume session */
session_start();

// load config
require_once WsROOT.'/protected/config/config.php';

// set default timezone
date_default_timezone_set(WsConfig::get('app_tz'));

// track memory usage and script execution time if 'development'
if (WsConfig::get('app_stage') == 'development') {
    define('WsSTART_MEMORY_USAGE',
        number_format(memory_get_usage(false) / 1024, 2)
    );
    define('WsSTART_TIME', microtime(true));

    // enable error reporting
    error_reporting(-1);
} else {
    error_reporting(0);
}

// user defined error handling function
function WsErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }
    switch ($errno) {
        case E_USER_ERROR:
            $WsContent = '<div class="uk-allert uk-alert-danger">';
            $WsContent .= '<strong>ERROR:</strong> ['.$errno.'] ';
            $WsContent .= $errstr.'<br/>';
            $WsContent .= '  Fatal error on line '.$errline;
            $WsContent .= ' in file '.$errfile;
            $WsContent .= ', PHP '.PHP_VERSION.' ('.PHP_OS.')<br/>';
            $WsContent .= 'Aborting...<br/>';
            $WsContent .= '</div>';
            $WsTitle = WsConfig::get('app_name')
                .WsLocalize::msg(' - Error');
            $WsBreadcrumbs = array(
            WsLocalize::msg('Error') => array(
                'site',
                'index'
            )
        );
            break;

        case E_USER_WARNING:
            $WsContent = '<div class="uk-allert uk-alert-warning">';
            $WsContent .= '<strong>WARNING:</strong> [';
            $WsContent .= $errno.'] '.$errstr.'<br/>';
            $WsContent .= '</div>';
            break;

        case E_USER_NOTICE:
            $WsContent = '<div class="uk-allert uk-alert-primary">';
            $WsContent .= '<strong>NOTICE:</strong> ['.$errno.'] ';
            $WsContent .= $errstr.'<br/>';
            $WsContent .= '</div>';
            break;

        default:
            $WsContent = '<div class="uk-allert">';
            $WsContent .= 'Unknown error type: ['.$errno.'] '.$errstr.'<br/>';
            $WsContent .= '</div>';
            break;
    }

    // display error message in layout file if it's possible
    $layoutFile = WsROOT.'/public/layouts/';
    $layoutFile .= WsConfig::get('html_layout');
    // display error message
    if (is_file($layoutFile)) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
            ) {
            echo $WsContent;
        } else {
            include($layoutFile);
        }
    } else {
        echo $WsContent;
    }

    switch($errno) {
        case E_ERROR:
        case E_PARSE:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            if (gc_enabled()) {
                gc_collect_cycles();
                gc_disable();
            }
            die();
    }

    return true;
}
set_error_handler('WsErrorHandler');


/*
 * main function that calls controller and action and also forward parameters to
 * call.
 */
function callHook()
{
    gc_enable();

    // search for controler name in request
    if (!isset($_REQUEST['request'])) { // no parameters
        $controller = 'site';
        $action = 'index';
        $params = array();
    } else {
        $request = explode('/', $_REQUEST['request']);
        $params = array();
        if (count($request) == 1) {
            // we have one parameter, it's controller
            $controller = $request[0];
            $action = 'index';
        } else if (count($request) >= 2) {
            /* first parameter is controller, second is action and all others
             * are parameters for action
             */

            $controller = $request[0];
            $action = $request[1];
            // remove controler from array
            unset($request[0]);
            // remove action from array
            unset($request[1]);

            if (WsConfig::get('pretty_urls') == 'yes') {
                foreach ($request as $r) {
                    array_push($params, urldecode($r));
                }
            } else {
                $params = array_map('urldecode', $request);
            }
        }
    }

    $controller = ucwords($controller);
    $controller .= 'Controller';

    // check if controller class exists
    if (class_exists($controller)) {
        $dispatch = new $controller();
    } else {
        header('HTTP/1.1 404 Not Found');
        trigger_error('Invalid call to non-existent controller: <strong>'
            .$controller.'</strong>', E_USER_ERROR);
    }

    try {
        // check if action method exist
        if (method_exists($dispatch, $action)) {
            // call action
            call_user_func_array(array($dispatch, $action), $params);
        } else {
            header('HTTP/1.1 404 Not Found');
            trigger_error('Invalid call to non-existent action: <strong>'
                .$controller.'::'.$action.'</strong>', E_USER_ERROR);
        }
    } catch (Exception $e) {
        ob_end_clean();
        trigger_error($e->getMessage(), E_USER_ERROR);
    }

    gc_collect_cycles();
    gc_disable();
}


// check if runtime directory is writable
if (!is_writable(WsROOT.'/runtime')) {
    header('HTTP/1.1 500 Internal Server Error');
    trigger_error('Directory <strong>/runtime</strong> must be writable!',
        E_USER_ERROR);
}

// remove image files older then 1 hour from runtime directory
$files = glob(WsROOT."/runtime/wsimg_*.png");
$now   = time();

foreach ($files as $file) {
    if (is_file($file)) {
        if ($now - filemtime($file) >= 3600) {// 1 hour
            unlink($file);
        }
    }
}
unset($now, $files);

// create database tables if they are not exists
if (WsConfig::get('db_driver') == 'pgsql') {
    $db_file = WsROOT.'/schema_pgsql.sql';
} else if (WsConfig::get('db_driver') == 'mysql') {
    $db_file = WsROOT.'/schema_mysql.sql';
} else {
    $db_file = WsROOT.'/schema_sqlite.sql';
}
if (file_exists($db_file)) {
    $auth = new WsAuth();
    $sql = file_get_contents($db_file);
    $db = new WsDatabase();
    $db->execute_batch($sql);
    $db->close();
    unset ($db, $auth, $sql, $db_file);
} else {
    unset ($db_file);
}

// call controller/action
callHook();
