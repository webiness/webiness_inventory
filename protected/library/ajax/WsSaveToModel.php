<?php
// ajax action that save record to WsModel depending on ID

define('WsROOT', dirname(dirname(dirname(dirname(__FILE__)))));

require_once WsROOT.'/protected/library/WsConfig.class.php';
require_once WsROOT.'/protected/config/config.php';
require_once WsROOT.'/protected/library/WsLocalize.class.php';
require_once WsROOT.'/protected/library/WsDatabase.class.php';
require_once WsROOT.'/protected/library/WsModel.class.php';

// new model
if (filter_input(INPUT_POST, 'model_name', FILTER_SANITIZE_STRING) !== NULL) {
    $model = filter_input(INPUT_POST, 'model_name');
} else {
    http_response_code(204);
    echo 'ID of model record must be provided.';
    exit;
}

// prevent CSRF attack
if (filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_STRING) !== NULL
        && isset($_SESSION['ws_auth_token'])) {
    if (filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_STRING)
        != $_SESSION['ws_auth_token']) {
            http_response_code(204);
            echo 'CSRF attack prevented.';
            exit;
    }
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

$m = new $model();

foreach ($_FILES as $file) {
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $destDir = WsROOT.'/runtime/'.$model;

    $field = key($_FILES);

    // files are upload to "runtime" directory create destination directory
    // if not exist
    if (!file_exists($destDir)) {
        mkdir($destDir, 0777, true);
    }

    // allowed file size is 3MB
    if ($file['size'] > 3145728) {
        continue;
    }

    // remove old file with same name
    if (file_exists($destDir.'/'.$fileName)) {
        unlink($destDir.'/'.$fileName);
    }

    // upload file
    move_uploaded_file($fileTmp, $destDir.'/'.$fileName);
    $m->$field= $fileName;
}

foreach ($_POST as $column => $value) {
    if ($column === 'model_name') {
        continue;
    }

    // convert timestamp to database format of timestamp
    if ($m->columnType[$column] === 'timestamp_type') {
        $m->$column = date('Y-m-d H:i:s', strtotime($value));
    // convert date in database format of DATE type
    } else if ($m->columnType[$column] === 'date_type') {
        $m->$column = date('Y-m-d', strtotime($value));
    // convert boolean type
    // in MySQL/MariaDB TRUE=1 and FALSE=0; in PostgreSQL TRUE=t, FALSE=f
    } else if ($m->columnType[$column] === 'bool_type') {
        if ($value == 'true') {
            if (WsConfig::get('db_driver') === 'pgsql') {
                $m->$column = 't';
            } else {
                $m->$column = 1;
            }
        } else {
            if (WsConfig::get('db_driver') === 'pgsql') {
                $m->$column = 'f';
            } else {
                $m->$column = 0;
            }
        }
    } else {
        $m->$column = $value;
    }
}

if (!$m->save()) {
    http_response_code(204);
    echo 'Record ID: '.$id.' is not saved to model: '.$model.'.';
    exit;
} else {
    http_response_code(200);
}

unset($m);
