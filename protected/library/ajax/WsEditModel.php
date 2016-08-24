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


// id of modal dialog that shows model form
if (filter_input(INPUT_POST, 'form_id', FILTER_SANITIZE_STRING) !== NULL) {
    $form_id = filter_input(INPUT_POST, 'form_id', FILTER_SANITIZE_STRING);
} else {
    $form_id = '';
}

// if ID is set then show specific record from model, else show empty form
$m = new $model;
if (filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT) !== NULL) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $m->getOne($id);
}

$form = new WsModelForm($m, $form_id);
$form->show();

$lang = substr(filter_input(
    INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING), 0,2);
?>


<script type="text/javascript">
    jQuery('document').ready(function($) {
        $('.webiness_select').select2();
        $('.webiness_datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            gotoCurrent: true
            },
            "option", $.datepicker.regional["<?php echo $lang; ?>"]
        );
    });
</script>
