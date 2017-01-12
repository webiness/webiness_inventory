<?php
// ajax action that shows edit form of model

define('WsROOT', dirname(dirname(dirname(dirname(__FILE__)))));
$sSoftware = strtolower(filter_input(
    INPUT_SERVER, 'SERVER_SOFTWARE', FILTER_SANITIZE_STRING
));
if (strpos($sSoftware, "apache") !== false) {
    define('WsSERVER_ROOT', dirname(
        dirname(
            dirname(
                dirname(
                    filter_input(
                        INPUT_SERVER, 'SCRIPT_NAME', FILTER_SANITIZE_STRING
                    )
                )
            )
        )
    ));
} else {
    define('WsSERVER_ROOT', 'http://'.filter_input(
        INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING
    ));
}

require_once WsROOT.'/protected/library/WsConfig.class.php';
require_once WsROOT.'/protected/config/config.php';
require_once WsROOT.'/protected/library/WsLocalize.class.php';
require_once WsROOT.'/protected/library/WsDatabase.class.php';
require_once WsROOT.'/protected/library/WsModel.class.php';
require_once WsROOT.'/protected/library/WsForm.class.php';
require_once WsROOT.'/protected/library/WsModelForm.class.php';
require_once WsROOT.'/protected/library/WsImage.class.php';

// model
if (filter_input(INPUT_POST, 'model') !== NULL) {
    $model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
} else {
    trigger_error('Model must be provided.', E_USER_INFO);
}

// load model
$model_file = WsROOT.'/application/models/'.$model.'.php';
if (!file_exists($model_file)) {
    $model_file = WsROOT.'/protected/library/models/'.$model.'.php';
}
require_once $model_file;


echo '<div class="uk-modal-dialog">';
echo '<a class="uk-modal-close uk-close"></a>';

// if ID is set then show specific record from model, else show empty form
$m = new $model;
if (filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT) !== NULL) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($m->idExists($id)) {
        $m->getOne($id);
        echo '<div class="uk-modal-header">'
            .WsLocalize::msg('Editing: ').'<span class="uk-text-primary">'
            .$m->metaName.'</span>'
            .WsLocalize::msg(' - record: ').'<strong>'.$id.'</strong></div>';
    } else {
        $id = $m->getNextId();
        echo '<div class="uk-modal-header">'
            .WsLocalize::msg('New: ').'<span class="uk-text-primary">'
            .$m->metaName.'</span>'
            .WsLocalize::msg(' - record: ').'<strong>'.$id.'</strong></div>';
    }
} else {
    $id = $m->getNextId();
    echo '<div class="uk-modal-header">'
        .WsLocalize::msg('New: ').'<span class="uk-text-primary">'
        .$m->metaName.'</span>'
        .WsLocalize::msg(' - record: ').'<strong>'.$id.'</strong></div>';
}


$form = new WsModelForm($m);
$form->show();
echo '</div>';

unset($form, $m, $model_file, $model, $sSoftware);
