<?php
define('WsROOT', dirname(__FILE__));
if (dirname(filter_input(INPUT_SERVER, 'SCRIPT_NAME')) == '/') {
    define('WsSERVER_ROOT', filter_input(INPUT_SERVER, 'SERVER_HOST'));
} else {
    define('WsSERVER_ROOT', dirname(filter_input(INPUT_SERVER, 'SCRIPT_NAME')));
}

require_once WsROOT.'/protected/library/WsInit.php';
