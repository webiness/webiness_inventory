<?php
/**
 * WsModelForm generates HTML form for working with WsModel records.
 *
 * @param WsModel $model WsModel instance
 * @param string $dialog HTML ID of element which holds form
 *
 * Example usage:
 *
 * <code>
 * // add a new record to model
 * $m = new MyModel();
 * $form = new WsModelForm($m, 'form-name');
 * // change label for field
 * $form->fieldLabels['name'] = 'First Name';
 * // change text of submit button
 * $form->submitButtonText = "Save Me";
 * // show form
 * $form->show()
 *
 * // edit existing record
 * $m->getOne(1);
 * $form2 = new WsModelForm($m, 'form-name');
 * $form2->show();
 * </code>
 *
 */
class WsModelForm extends WsForm
{
    /**
     * @var array $fieldLabels Form field labels
     *
     */
    public $fieldLabels;
    /**
     * @var WsModel $_model Instance of WsModel class
     */
    private $_model;
    /**
     * @var string $_dialog ID of popup dialog container used for CRUD
     */
    private $_dialog;
    /**
     * @var string $_id ID of form
     */
    private $_id;


    function __construct($model, $dialog)
    {
        if (! $model instanceof WsModel) {
            return false;
        }

        $this->_model = $model;
        // headers
        $this->fieldLabels = $model->columnHeaders;
        // dialog
        $this->_dialog = $dialog;
        $this->_id = 'WsForm_'.uniqid();

        // submit button text
        $this->submitButtonText = WsLocalize::msg('Save');

        $this->_form = '<div class="row">'
            .'<div class="column column-12 text-left">';
        $this->_form .= '<form id="'.$this->_id.'" '
            .'class="ws_form" '
            .'method="POST" enctype="multipart/form-data">'
            .'<fieldset>';

        // model name
        $this->_form .= '<input type="hidden" name="model_name" value="'
            .get_class($this->_model).'"/>';

        // prevent CSRF attack
        if (isset($_SESSION['ws_auth_token'])) {
            $this->_form .= '<input type="hidden" name="csrf" value="'
                .$_SESSION["ws_auth_token"]
                .'">';
        }

        // parameters for form widget
        $params = array();

        // form items
        foreach ($this->_model->columns as $column) {
            // widget name
            $params['name'] = $column;
            // widget id
            $params['id'] = $this->_id.'_'.$column;
            // widget label
            isset($this->fieldLabels[$column]) ?
                $label = $this->fieldLabels[$column] :
                $label = $column;
            $params['label'] = $label;
            // widget value
            if (isset($this->_model->$column)) {
                if ($this->_model->columnType[$column] == 'bool_type') {
                    if ($this->_model->$column == true
                        or $this->_model->$column == 't'
                        or $this->_model->$column == 1
                    ) {
                        $params['checked'] = true;
                    } else {
                        $params['checked'] = false;
                    }
                } else if ($this->_model->columnType[$column] == 'date_type') {
                    // get locale settings
                    $lang = substr(
                        filter_input(
                            INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE',
                            FILTER_SANITIZE_STRING
                        ), 0,2
                    );
                    setlocale(LC_ALL, $lang,
                        $lang.'_'.strtoupper($lang),
                        $lang.'_'.strtoupper($lang).'.utf8'
                    );
                    $date = strftime('%x', strtotime($this->_model->$column));
                    $params['value'] = $date;
                    unset($lang, $date);
                } else if ($this->_model->columnType[$column] == 'time_type') {
                    // get locale settings
                    $lang = substr(
                        filter_input(
                            INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'
                        ), 0,2
                    );
                    setlocale(LC_ALL, $lang,
                        $lang.'_'.strtoupper($lang),
                        $lang.'_'.strtoupper($lang).'.utf8'
                    );
                    $date = strftime('%X', strtotime($this->_model->$column));
                    $params['value'] = $date;
                    unset($lang, $date);
                } else if ($this->_model->columnType[$column]
                    == 'timestamp_type') {
                    // get locale settings
                    $lang = substr(
                        filter_input(
                            INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'
                        ), 0,2
                    );
                    setlocale(LC_ALL, $lang,
                        $lang.'_'.strtoupper($lang),
                        $lang.'_'.strtoupper($lang).'.utf8'
                    );
                    $date = strftime('%x %X',strtotime($this->_model->$column));
                    $params['value'] = $date;
                    unset($lang, $date);
                } else {
                    $params['value'] = $this->_model->$column;
                }
            } else {
                $params['value'] = '';
            }
            // required
            if ($this->_model->columnCanBeNull[$column] == false) {
                $params['required'] = true;
            }
            // if widget is for ID column
            if ($column == 'id') {
                $params['readonly'] = true;
                if (intval($params['value']) < 1) {
                    $query = 'SELECT CASE WHEN max(id) IS NULL THEN 1'
                        .' ELSE max(id)+1 END AS next_id FROM '
                        .$this->_model->tableName;
                    $db = new WsDatabase();
                    $result = $db->query($query);

                    $params['value'] = $result[0]['next_id'];

                    unset($query, $result, $db);
                }
            }

            if (array_key_exists($column, $this->_model->foreignKeys)) {
                $foreign_table = $this->_model->foreignKeys[$column]['table'];
                // if self referecing foreign key
                if ($foreign_table === $this->_model->tableName) {
                    $query = 'SELECT '
                        .$this->_model->foreignKeys[$column]['column']
                        .' AS option, '
                        .$this->_model->foreignKeys[$column]['display']
                        .' AS display FROM '
                        .$this->_model->tableName
                        .' ORDER BY option';
                } else {
                    $query = 'SELECT '
                        .$this->_model->foreignKeys[$column]['column']
                        .' AS option, '
                        .$this->_model->foreignKeys[$column]['display']
                        .' AS display FROM '
                        .$this->_model->foreignKeys[$column]['table']
                        .' ORDER BY option';
                }

                $db = new WsDatabase();
                $result = $db->query($query);

                $this->selectInput($result, $params);

                unset($query, $result, $db);
            } else {
                switch ($this->_model->columnType[$column]) {
                    case 'bool_type':
                        $this->booleanInput($params);
                        break;
                    case 'textarea_type':
                        $this->textareaInput($params);
                        break;
                    case 'timestamp_type':
                        $params['type'] = 'datetime-local';
                        $this->textInput($params);
                        break;
                    case 'date_type':
                        $params['type'] = 'date';
                        $this->textInput($params);
                        break;
                    case 'time_type':
                        $params['type'] = 'time';
                        $this->textInput($params);
                        break;
                    case 'password_type':
                        $params['type'] = 'password';
                        $this->textInput($params);
                        break;
                    case 'int_type':
                        $params['type'] = 'number';
                        $this->textInput($params);
                        break;
                    case 'numeric_type':
                        $params['type'] = 'number';
                        $this->textInput($params);
                        break;
                    case 'url_type':
                        $params['type'] = 'url';
                        $this->textInput($params);
                        break;
                    case 'email_type':
                        $params['type'] = 'email';
                        $this->textInput($params);
                        break;
                    case 'phone_type':
                        $params['type'] = 'tel';
                        $this->textInput($params);
                        break;
                    case 'file_type':
                        $params['type'] = 'file';
                        $this->textInput($params);
                        break;
                    default:
                        $params['type'] = 'text';
                        $this->textInput($params);
                }
            }

            // clear parameters for next widget
            $params = array();
        }

        $this->_form .= '<br/>';
        $this->_form .= '<br/>';
        $this->_form .= '<div class="row">';
        $this->_form .= '<div class="column column-12 text-center">';
        $this->_form .= '<input type="submit" class="button success"'
            .' value="'.$this->submitButtonText.'"/>';
        $this->_form .= '</div></div>';
        $this->_form .= '<div class="row" id="form_status"></div>';
        $this->_form .= '</fieldset>';
        $this->_form .= '</form>';

        $this->_form .= '</div>';
        $this->_form .= '</div>';

        $this->_form .= '<script>';
        // for form validation
        $this->_form .= '$("#'.$this->_id.'").validate({';
        $this->_form .= 'submitHandler: function(form) {';
        $this->_form .= 'var form_object = $("#'.$this->_id.'");';
        $this->_form .= 'WssaveModel("'.$this->_id.'", "'.$this->_dialog.'"'
            .',"'.WsSERVER_ROOT.'/protected/library/ajax/WsSaveToModel.php");';
        $this->_form .= '}';
        $this->_form .= '});';
        $this->_form .= '</script>';
    }


    public function __toString()
    {
        return $this->_form;
    }


    /**
     * Displays generated model form on screen
     *
     */
    public function show()
    {
        echo $this->_form;
    }


    /*
     * Return name of model
     *
     * @return string Model name.
     *
     */
    public function getModelName()
    {
        return $this->_model->className;
    }
}
