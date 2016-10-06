<?php
// FRAMEWORK CONFIG VARIABLES
// database driver (pgsql, mysql or sqlite)
WsConfig::set('db_driver', 'mysql');
// database host address
WsConfig::set('db_host', 'localhost');
// database port number
//WsConfig::set('db_port', '5432');
// database name
WsConfig::set('db_name', 'wsinventory');
// database user name
WsConfig::set('db_user', 'wsinventory');
// database user password
WsConfig::set('db_password', 'wsinventory');
// application name
WsConfig::set('app_name', 'Webiness Inventory Managment');
// application development stage (development | production)
WsConfig::set('app_stage', 'development');
// default timezone
WsConfig::set('app_tz', 'Europe/Zagreb');
// default character encoding
WsConfig::set('html_encoding', 'utf-8');
// default layout file for HTML rendering. File is located in public/layouts
WsConfig::set('html_layout', 'default.php');
// site administrators email address - needed for WsAuth
WsConfig::set('auth_admin', 'bojan.kajfes@gmail.com');
// enable semantic (pretty) URLs
WsConfig::set('pretty_urls', 'no');
// END OF FRAMEWORK CONFIG VARIABLES

// CUSTOM CONFIG VARIABLES
// WsConfig::set('test_variable', 'this is test');
// END OF CUSTOM CONFIG VARIABLES

