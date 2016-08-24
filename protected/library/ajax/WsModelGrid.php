<?php
// ajax action that shows grid from model
define('WsROOT', dirname(dirname(dirname(dirname(__FILE__)))));

$sSoftware = strtolower(filter_input(
    INPUT_SERVER, 'SERVER_SOFTWARE', FILTER_SANITIZE_STRING
));
if (strpos($sSoftware, "apache") !== false) {
    define('WsSERVER_ROOT', dirname(
        dirname(
            dirname(
                dirname(filter_input(
                    INPUT_SERVER, 'SCRIPT_NAME', FILTER_SANITIZE_STRING
                ))
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
require_once WsROOT.'/protected/library/WsUrl.class.php';
require_once WsROOT.'/protected/library/WsImage.class.php';


if (filter_input(INPUT_POST, 'model') !== NULL) {
    $model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
} else {
    trigger_error('Model must be provided.', E_USER_INFO);
}

// get custom grid actions if they are defined
if (filter_input(INPUT_POST, 'editAction') !== NULL
    and filter_input(INPUT_POST, 'editAction') !== '') {
    $edit_action = filter_input(INPUT_POST, 'editAction',
        FILTER_SANITIZE_STRING);
} else {
    $edit_action = WsSERVER_ROOT.'/protected/library/ajax/WsEditModel.php';
}
if (filter_input(INPUT_POST, 'deleteAction') !== NULL
    and filter_input(INPUT_POST, 'deleteAction') !== '') {
    $delete_action = filter_input(INPUT_POST, 'deleteAction',
        FILTER_SANITIZE_STRING);
} else {
    $delete_action = WsSERVER_ROOT
        .'/protected/library/ajax/WsDeleteFromModel.php';
}

// load model
$model_file = WsROOT.'/application/models/'.$model.'.php';
if (!file_exists($model_file)) {
    $model_file = WsROOT.'/protected/library/models/'.$model.'.php';
}
require_once $model_file;


$m = new $model;

// id
$gridId = filter_input(INPUT_POST, 'gridId', FILTER_SANITIZE_STRING) !== NULL
    ? filter_input(INPUT_POST, 'gridId', FILTER_SANITIZE_STRING)
    : uniqid('WsGridView_');
// values for pagination
$pageId = filter_input(INPUT_POST, 'pageId', FILTER_SANITIZE_STRING) !== NULL
    ? intval(filter_input(INPUT_POST, 'pageId', FILTER_SANITIZE_STRING))
    : 1;
$itemsPerPage = filter_input(
    INPUT_POST, 'itemsPerPage', FILTER_SANITIZE_NUMBER_INT) !== NULL
        ? intval(filter_input(
            INPUT_POST, 'itemsPerPage', FILTER_SANITIZE_NUMBER_INT))
        : 10;
// no data message
$noDataText = filter_input(
    INPUT_POST, 'noDataText', FILTER_SANITIZE_STRING) !== NULL
        ? filter_input(INPUT_POST, 'noDataText', FILTER_SANITIZE_STRING)
        : WsLocalize::msg('no data found');
// show edit controls
$showEdit = filter_input(
    INPUT_POST, 'showEdit', FILTER_SANITIZE_STRING) !== NULL
        && intval(filter_input(
            INPUT_POST, 'showEdit', FILTER_SANITIZE_STRING)) == 1
        ? true
        : false;
// data order
$order = filter_input(INPUT_POST, 'order', FILTER_SANITIZE_STRING) !== NULL
        && trim(filter_input(INPUT_POST, 'order', FILTER_SANITIZE_STRING)) != ''
    ? trim(filter_input(INPUT_POST, 'order', FILTER_SANITIZE_STRING))
    : 'id';
// id of form
$formId = filter_input(INPUT_POST, 'formId', FILTER_SANITIZE_STRING) !== NULL
        && trim(filter_input(INPUT_POST, 'formId',FILTER_SANITIZE_STRING)) != ''
    ? trim(filter_input(INPUT_POST, 'formId', FILTER_SANITIZE_STRING))
    : 'id';
// search string
$searchStr = filter_input(
    INPUT_POST, 'searchStr', FILTER_SANITIZE_STRING) !== NULL
        && trim(filter_input(
            INPUT_POST, 'searchStr', FILTER_SANITIZE_STRING)) != ''
        ? trim(filter_input(
            INPUT_POST, 'searchStr', FILTER_SANITIZE_STRING))
        : '';


// get results
$offset = ($pageId * $itemsPerPage);

if ($searchStr == '') {
    $results = $m->getAll($order, $itemsPerPage, $offset);
} else {
    // search for string in every column
    $condition = '(';
    if (WsConfig::get('db_driver') !== 'mysql') {
        foreach ($m->columns as $column) {
            if (isset($m->foreignKeys[$column])) {
                $foreign_id = isset($m->foreignKeys[$column]['column']) ?
                    $m->foreignKeys[$column]['column'] : 'id';
                $table_name = strtolower($m->foreignKeys[$column]['table']);
                $condition .= 'UPPER(CAST('.$table_name.'.'.$foreign_id
                    .' AS VARCHAR)) LIKE \'%'.strtoupper($searchStr).'%\' OR ';
            } else {
                $condition .= 'UPPER(CAST('.$m->tableName.'.'.$column
                    .' AS VARCHAR)) LIKE \'%'.strtoupper($searchStr).'%\' OR ';
            }
        }
    } else {
        foreach ($m->columns as $column) {
            if (isset($m->foreignKeys[$column])) {
                $foreign_id = isset($m->foreignKeys[$column]['column']) ?
                    $m->foreignKeys[$column]['column'] : 'id';
                $table_name = strtolower($m->foreignKeys[$column]['table']);
                $condition .= 'UPPER(CAST('.$table_name.'.'.$foreign_id
                    .' AS CHAR)) LIKE \'%'.strtoupper($searchStr).'%\' OR ';
            } else {
                $condition .= 'UPPER(CAST('.$m->tableName.'.'.$column
                    .' AS CHAR)) LIKE \'%'.strtoupper($searchStr).'%\' OR ';
            }
        }
    }
    // remove last OR clausule from condition
    $condition2 = substr($condition, 0, -4);
    $condition2 .= ')';
    $results = $m->search($condition2, $order, $itemsPerPage, $offset);
}

// table (grid)
if ($m->nRows < 1) {
    $table = '<tr class="ws_tr"><td class="ws_td">'.$noDataText.'</td></tr>';
} else {
    // set locale for date and time representation
    $lang = substr(
        filter_input(
            INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'
        ), 0,2
    );
    setlocale(LC_ALL, $lang,
        $lang.'_'.strtoupper($lang),
        $lang.'_'.strtoupper($lang).'.utf8'
    );
    $table = '';
    foreach ($results as $row) {
        $table .= '<tr class="ws_tr">';
        foreach ($row as $key => $value) {
            $value = htmlspecialchars($value);
            if (!in_array($key, $m->hiddenColumns)) {
                $table .= '<td class="ws_td">';
                if ($m->columnType[$key] == 'timestamp_type') {
                    $table .= strftime('%x %X', strtotime($value));
                } else if ($m->columnType[$key] == 'date_type') {
                    $table .= strftime('%x', strtotime($value));
                } else if ($m->columnType[$key] == 'time_type') {
                    $table .= strftime('%X', strtotime($value));
                } else if ($m->columnType[$key] == 'bool_type') {
                    if ($value === '1' or $value === true) {
                        $status = '&#x2714;';
                    } else {
                        $status = '&#x2718;';
                    }
                    $table .= '<i>'.$status.'</i>';
                } else if ($m->columnType[$key] == 'password_type') {
                    $table .= '*****';
                } else if ($m->columnType[$key] == 'mail_type') {
                    $table .= '<a href="mailto:'.$value.'">'.$value.'</a>';
                } else if ($m->columnType[$key] == 'url_type') {
                    $table .= '<a href="'.$value.'">'
                        .str_replace('http://','',$value)
                        .'</a>';
                } else if ($m->columnType[$key] == 'file_type') {
                    $url = WsROOT.'/runtime/'.$model.'/'.$value;
                    $link = WsSERVER_ROOT.'/runtime/'.$model.'/'.$value;
                    if (file_exists($url)) {
                        $table .= '<a href="'.$link.'" target="_blank">';
                        if(@is_array(getimagesize($url))) {
                            $table.='<img src="'.$link.'" width=40 height=40/>';
                        } else {
                            $table .= $value;
                        }
                        $table .= '</a>';
                        unset($link, $url);
                    } else {
                        $table .= $value;
                    }
                } else {
                    $table .= $value;
                }
                $table .= '</td>';
            }
        }
        if ($showEdit) {
            $id = $row['id'];
            $table .= '<td class="ws_td">';
            $table .= '<input class="action-button error" id="delete_'
                .$gridId.'_'.$id.'" type="button" value="&#x2718;"'
                .' onclick="WsdeleteModelID('
                .'\''.$formId.'\', '
                .'\''.get_class($m).'\', '
                .$id.', \''.$delete_action.'\''
                .', \''.WsLocalize::msg('Confirm').'\''
                .', \''.WsLocalize::msg('Yes').'\''
                .', \''.WsLocalize::msg('No').'\''
                .')"/>';
            $table .= '<input class="action-button primary" id="edit_'
                .$gridId
                .'_'.$id.'" type="button" value="&#x270e;"'
                .' onclick="WseditModelID('
                .'\''.$formId.'\', '
                .'\''.get_class($m).'\', '
                .$id.', \''.$edit_action.'\', \''.$m->metaName.'\')"/>';
            $table .= '</td>';
        }
        $table .= '</tr>';
    }
}

echo $table;
