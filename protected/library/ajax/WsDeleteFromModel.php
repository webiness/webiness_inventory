<?php
// ajax action that delete record from WsModel depending on ID

define('WsROOT', dirname(dirname(dirname(dirname(__FILE__)))));

require_once WsROOT.'/protected/library/WsConfig.class.php';
require_once WsROOT.'/protected/config/config.php';
require_once WsROOT.'/protected/library/WsLocalize.class.php';
require_once WsROOT.'/protected/library/WsDatabase.class.php';
require_once WsROOT.'/protected/library/WsModel.class.php';

if (filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT) !== NULL
        and filter_input(INPUT_POST, 'model') !== NULL) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
} else {
    http_response_code(204);
    echo 'ID of model record must be provided.';
    exit;
}



// load model
$model_file = WsROOT.'/application/models/'.$model.'.php';
if (!file_exists($model_file)) {
    $model_file = WsROOT.'/protected/library/models/'.$model.'.php';
}
if (!file_exists($model_file)) {
    http_response_code(400);
    echo 'Given model name does not exist.';
    exit;
}
require_once $model_file;

$m = new $model;
$m->delete($id);

if (!$m->delete($id)) {
    http_response_code(204);
    echo 'Given ID: '.$id.' in model: '.$model.' does not exist.';
    exit;
} else {
    http_response_code(200);
}

unset($m);
