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
        $paginationCount= floor($count / $this->itemsPerPage);
        $paginationModCount= $count % $this->itemsPerPage;
        if(!empty($paginationModCount)){
            $paginationCount++;
        }
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

        // master div element
        $table .= '<div class="row">';

        // header
        //$table .= '<div class="table-dialog-header">'
        //    .'<strong>'.$this->_model->metaName.'</strong>'
        //    .'</div>';

        // container for edit dialog
        if ($this->showEdit) {
            $table .= '<div id="'.$this->_formId.'"></div>';
        }

        // grid
        $table .= '<div class="row grid-header">';
        // title
        $table .= '<div class="row">';
        $table .= '<div class="column column-6 text-left text-error">';
        $table .= '<div class="grid-title">';
        $table .= $this->_model->metaName;
        $table .= '</div>';
        $table .= '</div>';
        $table .= '</div>';
        // end of grid header
        $table .= '</div>';

        // control row
        $table .= '<div class="row">';
        $table .= '<div class="column column-6 text-left">';
        $table .= '<form class="search-form">';
        // new item button
        if ($this->showEdit) {
            $table .= '<input class="add-button"'
                .' id="btn_create_'.$this->_id.'"'
                .' value="+"'
                .' type="button" onclick="WseditModelID('
                .'\''.$this->_formId.'\', '
                .'\''.$this->_modelName.'\', '
                .'0, \''.$this->_edit_action.'\', \''
                .$this->_model->metaName.'\')"/>';
        }
        // search control
        $table .= '<input class="search-input"';
        $table .= ' type="text" id="search_'.$this->_id.'"';
        $table .= '/>';
        $table .= '<input class="search-button"'
            .' value="&#8981"'
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
            .'\''.$this->_delete_action.'\')"/>';
        $table .= '</form>';
        // end of control row
        $table .= '</div>';
        $table .= '</div>';

        // Grid View table
        $table .= '<div class="row">';
        $table .= '<div class="column column-12" ';
        $table .= ' style="overflow: auto;">';
        $table .= '<table class="grid">';
        $table .= '<thead>';
        $table .= '<tr class="ws_tr">';
        foreach ($this->_model->columns as $column) {
            if (!in_array($column, $this->_model->hiddenColumns)) {
                if (isset($this->_model->columnHeaders[$column])) {
                    $table .= '<th class="ws_th">'
                        .$this->_model->columnHeaders[$column];
                    $table .= '</th>';
                } else {
                    $table .= '<th class="ws_th">'.$column.'</th>';
                }
            }
        }
        if ($this->showEdit) {
            $table .= '<th class="ws_th"></th>';
        }
        $table .= '</tr>';
        $table .= '</thead>';

        // container of table data loaded from AJAX request
        $table .= '<tbody id="'.$this->_id.'"></tbody>';

        // end of grid table
        $table .= '</table>';
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
        $table .= '<div class="row">';
        $table .= '<ul class="pagination">';
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
        $table .= '</div>';

        // end of master div element
        $table .= '</div><br/>';

        $table .= '<script type="text/javascript">'
            .'$("#search_'.$this->_id.'").keydown(function(event) {'
            .'    if(event.keyCode == 13) {'
            .'        event.preventDefault();'
            .'        $("#btn_search_'.$this->_id.'").click();'
            .'    }'
            .'});'
            .'</script>';

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
