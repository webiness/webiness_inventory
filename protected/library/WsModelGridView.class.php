<?php
/**
 * WsModelGridView
 * Displays a list of data items in terms of a table. Each row of the table
 * represents the data of a single data item, and a column usually represents
 * an attribute of the item. WsModelGridView supports both CRUD and pagination
 * of the data items.
 *
 * @param WsModel $model Instance of WsModel class
 * @param string $order Sorting order for a grid
 *
 * Example usage:
 *
 * <code>
 * // class MyModel inherits from WsModel class
 * $model = new MyModel();
 * $grid = new WsModelGridView($model);
 *
 * // show grid
 * $grid->show();
 * </code>
 *
 */
class WsModelGridView
{
    /**
     * @var integer $itemsPerPage Number of items per page
     *
     */
    public $itemsPerPage = 10;
    /**
     * @var string $noDataText Text to display if model is empty
     *
     */
    public $noDataText;
    /**
     * @var boolean $showEdit Shows or hide controls for CRUD operations
     *
     */
    public $showEdit = true;
    /**
     * @var string $_id ID of grid view
     *
     */
    private $_id = '';
    /**
     * @var string $_modelName Name of model
     *
     */
    private $_modelName = '';
    /**
     * @var string $_tableName Table name
     *
     */
    private $_tableName;
    /**
     * @var string $_order Sorting order for items
     *
     */
    private $_order = '';
    /**
     * @var string $_action AJAX action for grid population
     *
     */
    private $_action = '';
    /**
     * @var string $_edit_action AJAX url for editing gridview item
     *
     */
    private $_edit_action = '';
    /**
     * @var string $_delete_action AJAX url for removing gridview item
     *
     */
    private $_delete_action = '';
    /**
     * ID of element that will show edit dialog
     *
     * @var string $_formId ID of CRUD form
     *
     */
    private $_formId = '';
    private $_model;


    public function __construct($model, $order='',
        $edit_action='', $delete_action='')
    {
        $this->_id = uniqid('WsGridView_');

        if (! $model instanceof WsModel) {
            return false;
        }

        $this->noDataText = WsLocalize::msg('no data found');

        $this->_modelName = get_class($model);
        $this->_model = $model;
        $this->_order = $order;

        $this->_edit_action = WsSERVER_ROOT
            .'/protected/library/ajax/WsEditModel.php';
        if (trim($edit_action) != '') {
            $this->_edit_action = $edit_action;
        }

        $this->_delete_action = WsSERVER_ROOT
            .'/protected/library/ajax/WsDeleteFromModel.php';
        if (trim($delete_action) != '') {
            $this->_delete_action = $delete_action;
        }

        $this->_action = WsSERVER_ROOT
            .'/protected/library/ajax/WsModelGrid.php';
        $this->_formId = $this->_id.'_edit_form';
    }


    public function __toString()
    {
        return $this->constructGrid();
    }


    /**
     * Pagination logic.
     *
     * @param integer $count Number of items per page.
     * @return integer $paginationCount
     *
     */
    protected function getPagination($count)
    {
        $paginationCount = floor($count / $this->itemsPerPage);
        $paginationModCount = $count % $this->itemsPerPage;
        if(!empty($paginationModCount)){
            $paginationCount++;
        }

        unset($count);
        return $paginationCount;
    }


    /**
     * construct model grid view
     *
     * @return string $table
     *
     */
    protected function constructGrid()
    {
        // jquery script for loading first page of grid
        $table = "
            <script type=\"text/javascript\">
                // load first page
                WschangeModelPagination(
                    '$this->_id',
                    '$this->_action',
                    '$this->_modelName',
                    '$this->noDataText',
                    $this->itemsPerPage,
                    $this->showEdit,
                    '$this->_order',
                    '$this->_formId',
                    0,
                    '$this->_id'+'_0',
                    '$this->_edit_action',
                    '$this->_delete_action'
                );
            </script>
        ";

        // container for edit dialog
        if ($this->showEdit) {
            $table .= '<div class="uk-modal" id="'.$this->_formId.'"></div>';
            $table .= '<div class="uk-modal" id="'.$this->_formId.'_new"></div>';
        }

        // title
        $table .= '<div class="uk-grid">';
        $table .= '<div class="uk-width-small-1-1 uk-width-medium-1-1">';
        $table .= '<h1>'.$this->_model->metaName.'</h1>';
        $table .= '</div>';
        $table .= '</div>';

        // add and search controls
        $table .= '<div class="uk-grid">';
        $table .= '<div class="uk-width-small-1-1 uk-width-medium-1-2">';
        $table .= '<form class="uk-form uk-form-horizontal">';
        $table .= '<fieldset data-uk-margin>';
        // new item button
        if ($this->showEdit) {
            $table .= '<button class="uk-button uk-button-success"'
                .' data-uk-modal="{target:\'#'.$this->_formId
                .'_new\', center:true}"'
                .' id="btn_create_'.$this->_id.'"'
                .' type="button" onclick="WseditModelID('
                .'\''.$this->_formId.'_new\', '
                .'\''.$this->_modelName.'\', '
                .'0, \''.$this->_edit_action.'\')">';
            $table .= '<i class="uk-icon-plus"></i>';
            $table .= '</button>';
        }
        // search control
        $table .= '<input';
        $table .= ' type="text" id="search_'.$this->_id.'"';
        $table .= '/>';
        $table .= '<button class="uk-button"'
            .' id="btn_search_'.$this->_id
            .'" type="button" onclick="WschangeModelPagination('
            .'\''.$this->_id.'\', '
            .'\''.$this->_action.'\', '
            .'\''.$this->_modelName.'\', '
            .'\''.$this->noDataText.'\', '
            .$this->itemsPerPage.', '
            .$this->showEdit.', '
            .'\''.$this->_order.'\', '
            .'\''.$this->_formId.'\', '
            .'0, \''.$this->_id.'\'+\'_0\', '
            .'\''.$this->_edit_action.'\', '
            .'\''.$this->_delete_action.'\')">';
        $table .= '<i class="uk-icon-search"></i>';
        $table .= '</button>';

        $table .= '</fieldset>';
        $table .= '</form>';
        $table .= '</div>';
        $table .= '</div>';

        // Grid View table
        $table .= '<div class="uk-grid">';
        $table .= '<div class="uk-width-1-1">';
        $table .= '<div class="uk-overflow-container">';
        $table .= '<table class="uk-table uk-table-hover uk-table-striped">';
        $table .= '<thead>';
        $table .= '<tr>';
        foreach ($this->_model->columns as $column) {
            if (!in_array($column, $this->_model->hiddenColumns)) {
                if (isset($this->_model->columnHeaders[$column])) {
                    $table .= '<th>'
                        .$this->_model->columnHeaders[$column];
                    $table .= '</th>';
                } else {
                    $table .= '<th>'.$column.'</th>';
                }
            }
        }
        if ($this->showEdit) {
            $table .= '<th></th>';
        }
        $table .= '</tr>';
        $table .= '</thead>';

        // container of table data loaded from AJAX request
        $table .= '<tbody id="'.$this->_id.'"></tbody>';

        // end of grid table
        $table .= '</table>';
        $table .= '</div>';
        $table .= '</div>';
        $table .= '</div>';

        // get number ow rows from query so that we can make pager
        $db = new WsDatabase();
        $countQuery = 'SELECT COUNT(*) AS nrows FROM '.$this->_model->tableName;
        $result = $db->query($countQuery);
        $this->nRows = intval($result[0]['nrows']);
        $db->close();

        // number of items in pager
        $nPages = $this->getPagination($this->nRows);

        // construct pager
        $table .= '<ul class="uk-pagination uk-pagination-left">';
        // links to pages
        for ($i = 0; $i < $nPages; $i++) {
            $table .= '<li>';
            $table .= '
                <a id="'.$this->_id.'_'.$i.'"
                    href="javascript:void(0)"
                    onclick="WschangeModelPagination('
                    .'\''.$this->_id.'\', '
                    .'\''.$this->_action.'\', '
                    .'\''.$this->_modelName.'\', '
                    .'\''.$this->noDataText.'\', '
                    .$this->itemsPerPage.', '
                    .$this->showEdit.', '
                    .'\''.$this->_order.'\', '
                    .'\''.$this->_formId.'\', '
                    .$i.',\''.$this->_id.'_'.$i.'\', '
                    .'\''.$this->_edit_action.'\', '
                    .'\''.$this->_delete_action.'\')"/>'
                    .($i+1).'</a>';
            $table .= '</li>';
        }
        // end of pager
        $table .= '</ul>';

        // end of master div element
        $table .= '<br/>';

        $table .= '<script type="text/javascript">'
            .'$("#search_'.$this->_id.'").keydown(function(event) {'
            .'    if(event.keyCode == 13) {'
            .'        event.preventDefault();'
            .'        $("#btn_search_'.$this->_id.'").click();'
            .'    }'
            .'});'
            .'</script>';

        unset($i, $nPages, $db, $result, $countQuery);
        return $table;
    }


    /**
     * Displays grid view on screen
     *
     */
    public function show()
    {
        echo $this->constructGrid();
    }
}
