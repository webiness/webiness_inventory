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
     * @var string $_id ID of form
     */
    private $_id;


    function __construct($model)
    {
        if (! $model instanceof WsModel) {
            return false;
        }

        $this->_model = $model;
        // headers
        $this->fieldLabels = $model->columnHeaders;
        $this->_id = 'WsForm_'.uniqid();

        // submit button text
        $this->submitButtonText = WsLocalize::msg('Save');

        $this->_form = '<div class="uk-grid">'
            .'<div class="uk-width-small-1-1">';
        $this->_form .= '<form id="'.$this->_id.'" '
            .'class="uk-form uk-form-horizontal" '
            .'method="POST" enctype="multipart/form-data">';

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

        // get locale settings
        $lang = WsLocalize::getLang();
        setlocale(LC_ALL, $lang,
            $lang.'_'.strtoupper($lang),
            $lang.'_'.strtoupper($lang).'.utf8'
        );

        // database connection
        $db = new WsDatabase();

        // form items
        foreach ($this->_model->columns as $column) {
            // widget name
            $params['name'] = $column;
            // widget id
            $params['id'] = $this->_id.'_'.$column;
            // widget label
            if (isset($this->fieldLabels[$column])) {
                $label = $this->fieldLabels[$column];
            } else {
                $label = $column;
            }
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
                    $date = strftime('%F', strtotime($this->_model->$column));
                    $params['value'] = $date;
                    unset($lang, $date);
                } else if ($this->_model->columnType[$column] == 'time_type') {
                    $date = strftime('%R', strtotime($this->_model->$column));
                    $params['value'] = $date;
                    unset($lang, $date);
                } else if ($this->_model->columnType[$column]
                    == 'timestamp_type') {
                    $date = strftime('%F %R',strtotime($this->_model->$column));
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
                    $params['value'] = $this->_model->getNextId();
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

                $result = $db->query($query);
                $this->selectInput($result, $params);

                unset($query, $result);
            } else {
                switch ($this->_model->columnType[$column]) {
                    case 'hidden_type':
                        $this->hiddenInput($params);
                        break;
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

        $this->_form .= '<br/><br/>'
            .'<button type="submit" '
            .'class="uk-button uk-button-success">';
        $this->_form .= $this->submitButtonText;
        $this->_form .= '</button>';
        $this->_form .= '</form>';

        $this->_form .= '</div>';
        $this->_form .= '</div>';

        $this->_form .= '<script>';
        // for form validation
        $this->_form .= '$("#'.$this->_id.'").validate({';
        $this->_form .= 'submitHandler: function(form) {';
        $this->_form .= 'WssaveModel("'.$this->_id.'", '
            .'"'.WsSERVER_ROOT.'/protected/library/ajax/WsSaveToModel.php");';
        $this->_form .= '}';
        $this->_form .= '});';
        $this->_form .= '</script>';

        unset($params, $lang, $label, $date, $db);
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
