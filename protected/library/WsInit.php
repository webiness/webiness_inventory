<?php
/* start or resume session */
session_start();

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
function __autoload($className)
{
    // load all framework classes
    if (file_exists(
        WsROOT.'/protected/library/'.$className.'.class.php')) {
        require_once WsROOT.'/protected/library/'.$className.'.class.php';
        return;
    }

    // autoload all internal Controllers and Models
    if (file_exists(
        WsROOT.'/protected/library/controllers/'.$className.'.php')) {
        require_once WsROOT.'/protected/library/controllers/'.$className.'.php';
        return;
    }
    if (file_exists(
        WsROOT.'/protected/library/models/'.$className.'.php')) {
        require_once WsROOT.'/protected/library/models/'.$className.'.php';
        return;
    }

    // load all application Controlers
    if (file_exists(
        WsROOT.'/application/controllers/'.$className.'.php')) {
        require_once WsROOT.'/application/controllers/'.$className.'.php';
        return;
    }

    // load all application Models
    if (file_exists(WsROOT.'/application/models/'.$className.'.php')) {
        require_once WsROOT.'/application/models/'.$className.'.php';
        return;
    }
}

// load config
require_once WsROOT.'/protected/config/config.php';

// set default timezone
date_default_timezone_set(WsConfig::get('app_tz'));

// track memory usage and script execution time if 'development'
if (WsConfig::get('app_stage') == 'development') {
    define('WsSTART_MEMORY_USAGE',
        number_format(memory_get_usage() / 1024, 2)
    );
    define('WsSTART_TIME', microtime(true));
}

// disable standard error reporting in production
if (WsConfig::get('app_stage') == 'development') {
    error_reporting(-1);
} else {
    error_reporting(0);
}

// user defined error handling function
function WsErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
{
    /* timestamp for the error entry */
    $dt = date('Y-m-d H:i:s (T)');

    // write error log
    $err = "****** ".$errno." ******\n";
    $err .= "\tdatetime: ".$dt."\n";
    $err .= "\terrormsg: ".$errmsg."\n";
    $err .= "\tscriptname: ".$filename."\n";
    $err .= "\tscriptlinenum: ".$linenum."\n";
    /*if (in_array($errno, $user_errors)) {
     *   $err .= "\tvariables: ".$vars."\n";
     *}
     */
    $err .= "*******************\n";
    // save to the error log
    try {
        error_log($err, 3, WsROOT.'/runtime/error.log');
    }   catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    // display error message
    // layout file
    $layoutFile = WsROOT.'/public/layouts/';
    $layoutFile .= WsConfig::get('html_layout');

    $WsContent = '<div class="row"><div class="col-sm-12">';

    switch($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
            $WsContent .= '<div class="alert alert-info">';
            break;
        case E_WARNING:
        case E_USER_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $WsContent .= '<div class="alwert alert-warning">';
            break;
        case E_ERROR:
        case E_PARSE:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            // e-mail the administrator if there is a critical user error
            mail(WsConfig::get('auth_admin'),
                WsConfig::get('app_name').' - Critical User Error',
                $err
            );
            $WsContent .= '<div class="alert alert-danger">';
    }

    // construc error message depending of WsAPP_STAGE
    if (WsConfig::get('app_stage') == 'development') {
        $WsContent .= $errmsg.'<br/>';
        $WsContent .= '<pre>'.$filename.'</pre><pre>line: '.$linenum.'</pre>';

    } else {
        $WsContent .= $errmsg;
    }
    $WsContent .= '</div></div></div>';

    // display error message
    if (is_file($layoutFile)) {
        include($layoutFile);
    } else {
        echo $WsContent;
    }

    // if we have critical error then stop execution of script
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
} else {
    $db_file = WsROOT.'/schema_mysql.sql';
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
