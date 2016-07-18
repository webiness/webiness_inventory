<?php
/**
 * WsModel
 * Is model part of Model-View-Controller. WsModel is the base
 * class providing the common features needed by data model objects.
 *
 * Example usage:
 *
 * <code>
 * class TestModel extends WsModel
 * {
 *     public function __construct()
 *     {
 *         # autodetect columns, column types, etc
 *         parent::__construct();
 *
 *         # change some column types
 *         # change column type for column
 *         $this->columnType['enter_date'] = 'date_type';
 *         # change column type for other column
 *         $this->columnType['is_checked'] = 'bool_type';
 *         # leave other column types unchanged (autodetected)
 *     }
 * }
 *
 * # use model
 * $m = new TestModel();
 *
 * # get one record with ID 100 from database
 * $m->getOne(100);
 * # show field value for retrived record
 * echo $m->name.'\n';
 *
 * # change value of one column in record with ID 100
 * $m->name = 'New Name';
 * # save changes
 * $m->save();
 *
 * # delete record with ID 100 from database
 * $m->id = 100;
 * $m->delete();
 * # same as above
 * $m->delete(100);
 * </code>
 *
 */
class WsModel extends WsDatabase
{
    /**
     * @var string $className Name of model
     *
     */
    public $className = '';
    /**
     * @var string $tableName Name of database table
     *
     */
    public $tableName = '';
    /**
     * @var string $metaName Long name (description) of database table
     * @see setTableName()
     *
     */
    public $metaName = '';
    /**
     * List of table columns. This list is automaticaly populated during the
     * class initialization.
     *
     * @var array $columns List of table columns
     *
     */
    public $columns = array();
    /**
     * @var string $primary_key Column that is primary key
     *
     */
    public $primary_key = 'id';
    /**
     * @var array $columnCanBeNull List of columns that can store NULL values
     *
     */
    public $columnCanBeNull = array();
    /**
     * @var array $columnType List of column types
     * @todo MySQL/MariaDB don't detect boolean type propperly so you must set
     * type for these columns by hand.
     *
     */
    public $columnType = array();
    /**
     * @var array $columnHeaders List of column headers
     * @see WsModelForm
     * @see WsModelGridView
     *
     */
    public $columnHeaders = array();
    /**
     * List of arrays wich represents relations between two WsModel classes.
     * This list is automaticaly populated during the class initialization
     * with regresion that display properti is referencing to foreign column
     * name.
     *
     * Usage:
     * \code{.php}
     * $foreignKeys = array(
     *     'column_name' => array(
     *         'table' => 'foreign_table_name',
     *         'column' => 'foreign_column_name',
     *         'display' => 'foreign_column_that_would_be_used_for_display'
     *     )
     * );
     * \endcode
     * @var array $foreignKeys
     *
     */
    public $foreignKeys = array();
    /**
     * @var array $hiddenColumns List of columns that are hidden from user
     * @see WsModelForm
     * @see WsModelGridView
     *
     */
    public $hiddenColumns = array();


    public function __construct()
    {
        parent::__construct();

        /*
         * get default database table name from Model name. Table name would
         * be same as model without "Model" suffix.
         */
        $this->className = get_class($this);
        $name = strtolower(substr($this->className, 0,
            strpos($this->className, 'Model')));

        // get colums and set default names for columns
        $this->setTableName($name);

        // at beginning metaName is same as tableName
        $this->metaName = $name;
    }


    /**
     * Sets name of database table on which model references. Also updates all
     * propertyes that hold informations about table columns:
     *      $column
     *      $columnCanBeNull
     *      $columnHeaders
     *      $foreignKeys
     *      $primaryKey
     *
     * @param string $name Name of database table
     *
     */
    public function setTableName($name)
    {
        $this->tableName = $name;

        // get foreign keys from table in PostgreSQL
        if (WsConfig::get('db_driver') === 'pgsql') {
            $query = '
SELECT
    att2.attname AS "child_column",
    cl.relname AS "parent_table",
    att.attname AS "parent_column"
FROM
    (SELECT
        UNNEST(con1.conkey) AS "parent",
        UNNEST(con1.confkey) AS "child",
        con1.confrelid,
        con1.conrelid
    FROM
        pg_class cl
    JOIN pg_namespace ns ON cl.relnamespace = ns.oid
    JOIN pg_constraint con1 ON con1.conrelid = cl.oid
    WHERE
        cl.relname = :table_name
            AND con1.contype = \'f\'
    ) con
    JOIN pg_attribute att on
        att.attrelid = con.confrelid AND att.attnum = con.child
    JOIN pg_class cl ON cl.oid = con.confrelid
    JOIN pg_attribute att2 ON
        att2.attrelid = con.conrelid AND att2.attnum = con.parent;
';
        // get forign keys from table in MySQL or MariaDB
        } else if (WsConfig::get('db_driver') === 'mysql') {
            $query = '
SELECT
    column_name AS "child_column",
    referenced_table_name AS "parent_table",
    referenced_column_name AS "parent_column"
FROM information_schema.key_column_usage
WHERE table_name = :table_name
AND referenced_table_name IS NOT NULL;
';
        } else if (WsConfig::get('db_driver') === 'sqlite') {
            $query = 'PRAGMA foreign_key_list(:table_name)';
        }

        // clear array of foreign keys
        $this->foreignKeys = array();

        $results = $this->query($query, array(
            'table_name' => $this->tableName
        ));

        // foreign key detection for PostgreSQL and MySQL/MariaDB
        if (WsConfig::get('db_driver') !== 'sqlite') {
            foreach ($results as $result) {
                $this->foreignKeys[$result['child_column']] = array(
                    'table' => $result['parent_table'],
                    'column' => $result['parent_column'],
                    'display' => $result['parent_column']
                );
            }
        }

        // get column names, types, nulls
        if (WsConfig::get('db_driver') === 'sqlite') {
            $query = 'PRAGMA table_info(:table_name)';
        } else {
            $query = '
SELECT column_name, is_nullable, data_type
FROM information_schema.columns
WHERE table_name= :table_name';
        }

        $results2 = $this->query($query, array(
            'table_name' => $this->tableName
        ));

        // clear all values that are defined before
        $this->columns = array();
        $this->columnHeaders = array();
        $this->columnIsNull = array();
        $this->columnType = array();

        foreach ($results2 as $result) {
            array_push($this->columns, $result['column_name']);
            // create all model variables from column
            $this->columnCanBeNull[$result['column_name']] =
                (($result['is_nullable'] =='YES') ? true: false);
            // column headers
            $this->columnHeaders[$result['column_name']] =
                $this->tableName.'-'.$result['column_name'];

            // column type by database column type
            if (stripos($result['data_type'], 'int') !== false) {
                // INTEGER, SMALLINT, BIGINT, TINYINT, MEDIUMINT
                $this->columnType[$result['column_name']] = 'int_type';
            } else if (stripos($result['data_type'], 'float') !== false
                or stripos($result['data_type'], 'real') !== false
                or stripos($result['data_type'], 'double') !== false
                or stripos($result['data_type'], 'dec') !== false
                or stripos($result['data_type'], 'fixed') !== false
                or stripos($result['data_type'], 'numeric') !== false
            ) {
                // NUMERIC, DOUBLE, REAL FLOAT, DECIMAL, FIXED numeric types
                $this->columnType[$result['column_name']] = 'numeric_type';
            } else if (stripos($result['data_type'], 'datetime') !== false
                or stripos($result['data_type'], 'timestamp') !== false
            ) {
                // DATETIME or TIMESTAMP type
                $this->columnType[$result['column_name']] = 'timestamp_type';
            } else if (stripos($result['data_type'], 'date') !== false) {
                // DATE type
                $this->columnType[$result['column_name']] = 'date_type';
            } else if (stripos($result['data_type'], 'time') !== false) {
                // TIME type
                $this->columnType[$result['column_name']] = 'time_type';
            } else if (stripos($result['data_type'], 'text') !== false) {
                // TEXT type
                $this->columnType[$result['column_name']] = 'textarea_type';
            } else if (stripos($result['data_type'], 'bool') !== false) {
                // BOOLEAN type
                $this->columnType[$result['column_name']] = 'bool_type';
            } else {
                // column type by column name
                if (stripos($result['column_name'], 'pass') !== false) {
                    // field is password
                    $this->columnType[$result['column_name']] = 'password_type';
                } else if (stripos($result['column_name'], 'url') !== false) {
                    // field is url address
                    $this->columnType[$result['column_name']] = 'url_type';
                } else if (stripos($result['column_name'], 'mail') !== false) {
                    // field is e-mail address
                    $this->columnType[$result['column_name']] = 'mail_type';
                } else if (stripos($result['column_name'], 'phone') !== false) {
                    // field is phone number
                    $this->columnType[$result['column_name']] = 'phone_type';
                } else {
                    // column type can't be determinate
                    $this->columnType[$result['column_name']] = 'misc_type';
                }
            }
        }

        unset($query, $results, $results2);
    }


    /**
     * Check if record, which primary key has value of parameter $id, exists
     * in database.
     *
     * @param integer $id Value of primary key
     * @return boolean True if record exists or false if not.
     *
     */
    public function idExists($id)
    {
        /*
         * fetch only record with specific ID
         */
        if (func_num_args() == 1) {
            $id = func_get_arg(0);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error($this->className
                .': invalid number of parameters in idExists()',
                E_USER_ERROR);
            return false;
        }

        // construct query string
        $query = 'SELECT '.$this->primary_key;
        $from = $this->tableName;
        $where = $this->tableName.'.'.$this->primary_key.' = :id_parameter';

        // final query
        $query .= ' FROM '.$from.' WHERE '.$where.' LIMIT 1';

        $result = $this->query($query, array(
            'id_parameter' => intval($id)
        ));

        // check for one result
        if ($this->nRows == 1) {
            $result = true;
        } else {
            $result = false;
        }

        unset($query);

        return $result;
    }


    /**
     * This method is invoked before deleting a record from database.
     *
     * @return boolean True on success or false otherwise
     *
     */
    public function beforeDelete()
    {
        return true;
    }


    /**
     * Delete record from database.
     *
     * @return boolean True on success or false otherwise
     *
     */
    public function delete()
    {
        /*
         * delete only record with specific ID
         */
        if (func_num_args() == 1) {
            $id = func_get_arg(0);
        } else if(property_exists($this, 'id')) {
            if (isset($this->id)) {
                $id = $this->id;
            } else {
                $id = '-1';
            }
        } else {
            $id = '-1';
        }

        if ($id != '-1') {
            $this->getOne($id);
        }

        if (!$this->beforeDelete()) {
            return false;
        }

        if ($id != '-1') {
            // prepare query for delete operation
            $query = 'DELETE FROM '.$this->tableName.' WHERE '
                .$this->primary_key.'=:id';
            if($this->execute($query, array('id' => $id))) {
                unset($query, $id);
                return true;
            } else {
                header('HTTP/1.1 500 Internal Server Error');
                trigger_error($this->className
                    .': error occurred while deleting record from model',
                    E_USER_ERROR);
                unset($query, $id);
                return false;
            }
        }
    }


    /**
     * This method is invoked before saving a record.
     *
     * @return boolean True on success or false otherwise
     *
     */
    public function beforeSave()
    {
        return true;
    }


    /**
     * Saves record to database. If primary_key column with specified value
     * allready exists then perform update of record and if not then perform
     * insert of new record.
     *
     * @return boolean True on success or false on error
     *
     */
    public function save()
    {
        $query = '';
        $values = array(); // values for fields
        $fields = '';
        $field_val = '';

        /*
         * fetch only record with specific ID
         */
        if(property_exists($this, 'id')) {
            if (isset($this->id)) {
                $id = $this->id;
            } else {
                $id = '-1';
            }
        } else {
            $id = '-1';
        }

        if(!$this->beforeSave()) {
            return false;
        }

        if ($this->idExists($id)) {
            // ID is set, perform UPDATE
            $updates = '';
            foreach ($this->columns as $column) {
                if (property_exists($this, $column)) {
                    if(trim($this->$column) !== '') {
                        $updates .= $column.' = :'.$column.', ';
                        $values[$column] = $this->$column;
                    }
                }
            }

            // remove last "," from updates
            $updates = substr($updates, 0, -2);

            // query for UPDATE
            $query = 'UPDATE '.$this->tableName.' SET '
                .$updates.' WHERE '.$this->primary_key.'=:id';

            unset($updates);
        } else {
            // ID is not set, perform INSERT
            foreach ($this->columns as $column) {
                if (property_exists($this, $column)) {
                    if(trim($this->$column) !== '') {
                        $fields .= $column.', ';
                        $field_val .= ':'.$column.', ';
                        $values[$column] = $this->$column;
                    }
                }
            }

            // remove last ","
            $fields = substr($fields, 0, -2);
            $field_val = substr($field_val, 0, -2);

            // query for INSERT
            $query = 'INSERT INTO '.$this->tableName.' ('.$fields
                .') VALUES ('.$field_val.')';

            unset($fields, $field_val);
        }

        if($this->execute($query, $values)) {
            unset($query, $values);
            return true;
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error($this->className
                .': error occurred while saving record to model',
                E_USER_ERROR);
            unset($query, $values);
            return false;
        }
    }


    /**
     * Get one record from table.
     *
     * @param integer $id Value of primary_key column which will be returned
     * @return array $results if record is found or false
     *
     */
    public function getOne()
    {
        /*
         * fetch only record with specific ID
         */
        if(property_exists($this, 'id')) {
            if (isset($this->id)) {
                $id = $this->id;
            } else {
                $this->nRows = 0;
                return false;
            }
        } else if (func_num_args() == 1) {
            $id = func_get_arg(0);
        } else {
            $this->nRows = 0;
            return false;
        }

        // construct query string
        $query = 'SELECT ';
        $from = $this->tableName.', ';
        $where = $this->tableName.'.'.$this->primary_key.'=:id_parameter AND ';
        foreach ($this->columns as $column) {
            // check if column is Foreign Key
            if (isset($this->foreignKeys[$column])) {
                $foreign_table = strtolower(
                    $this->foreignKeys[$column]['table']);
                /* self referencing foreign keys are treated
                 * as standard columns
                 */
                if ($foreign_table == $this->tableName) {
                    $foreign_id = isset($this->foreignKeys[$column]['column']) ?
                        $this->foreignKeys[$column]['column'] : 'id';
                    $display = isset($this->foreignKeys[$column]['display']) ?
                        $this->foreignKeys[$column]['display'] : $column;
                    $query .= $foreign_table.'_parent.'
                        .$display.' AS '.$column.', ';
                    $from .= $foreign_table.' '.$foreign_table.'_parent, ';
                    $where .= $this->tableName.'.'.$column.'='
                        .$foreign_table.'_parent.'.$foreign_id.' AND ';
                } else {
                    $foreign_id = isset($this->foreignKeys[$column]['column']) ?
                        $this->foreignKeys[$column]['column'] : 'id';
                    $display = isset($this->foreignKeys[$column]['display']) ?
                        $this->foreignKeys[$column]['display'] : $foreign_id;
                    $query .= $foreign_table.'.'.$display.' AS '.$column.', ';
                    $from .= $foreign_table.', ';
                    $where .= $this->tableName.'.'.$column.'='
                        .$foreign_table.'.'.$foreign_id.' AND ';
                }
            } else {
                // standard column */
                $query .= $this->tableName.'.'.$column.' AS '.$column.', ';
            }
        }
        // remove last coma from $query
        $query2 = substr($query, 0, -2);
        // remove last coma from $from
        $from = substr($from, 0, -2);
        // remove last AND from $where
        $where = substr($where, 0, -5);

        // final query
        $query2 .= ' FROM '.$from.' WHERE '.$where.' LIMIT 1';
        $result = $this->query($query2, array('id_parameter' => $id));

        // check for one result
        if ($this->nRows == 1) {
            // fill class properties
            foreach ($result[0] as $key => $value) {
                $this->$key = $value;
            }
        } else {
            $this->nRows = 0;
            $result = false;
        }

        unset($query, $query2);

        return $result;
    }


    /**
     * Gets all records from table
     *
     * @param string $order represents in which order result should be returned
     * @param integer $limit limits number of results
     * @param integer $offset starting record for return
     * @return array $results if records exists or false
     *
     */
    public function getAll($order='', $limit=0, $offset=0)
    {
        // construct query string
        $query = 'SELECT ';
        $from = $this->tableName.', ';
        $where = '';
        foreach ($this->columns as $column) {
            // check if column is Foreign Key
            if (isset($this->foreignKeys[$column])) {
                $foreign_table = strtolower(
                    $this->foreignKeys[$column]['table']);
                /* self referencing foreign keys are
                 * treated as standard columns
                 */
                if ($foreign_table == $this->tableName) {
                    $foreign_id = isset($this->foreignKeys[$column]['column']) ?
                        $this->foreignKeys[$column]['column'] : 'id';
                    $display = isset($this->foreignKeys[$column]['display']) ?
                        $this->foreignKeys[$column]['display'] : $column;
                    $query .= $foreign_table.'_parent.'
                        .$display.' AS '.$column.', ';
                    $from .= $foreign_table.' '.$foreign_table.'_parent, ';
                    $where .= $this->tableName.'.'.$column.'='
                        .$foreign_table.'_parent.'.$foreign_id.' AND ';
                } else {
                    $foreign_id = isset($this->foreignKeys[$column]['column']) ?
                        $this->foreignKeys[$column]['column'] : 'id';
                    $display = isset($this->foreignKeys[$column]['display']) ?
                        $this->foreignKeys[$column]['display'] : $foreign_id;
                    $query .= $foreign_table.'.'.$display.' AS '.$column.', ';
                    $from .= $foreign_table.', ';
                    $where .= $this->tableName.'.'.$column.'='
                        .$foreign_table.'.'.$foreign_id.' AND ';
                }
            } else {
                // standard column
                $query .= $this->tableName.'.'.$column.' AS '.$column.', ';
            }
        }
        // remove last coma from $query
        $query2 = substr($query, 0, -2);
        // remove last coma from $from
        $from = substr($from, 0, -2);

        // final query
        $query2 .= ' FROM '.$from;

        if ($where != '') {
            // remove last AND from $where
            $where = substr($where, 0, -5);
            $query2 .= ' WHERE '.$where;
        }

        // ORDER BY clausule
        if ($order != '') {
            $query2 .= ' ORDER BY '.$order;
        }

        // LIMIT clausule
        if ($limit > 0) {
            $query2 .= ' LIMIT '.$limit;
        }

        // offset clausule
        if ($offset > 0) {
            $query2 .= ' OFFSET '.$offset;
        }

        $result = $this->query($query2);

        unset($query, $query2);

        // check for any result
        if ($this->nRows >= 1) {
            return $result;
        } else {
            return false;
        }
    }


    /**
     * Gets all records from model that satisfaid specific condition.
     *
     * @param string $condition SQL condition
     * @param string $order represents in which order result should be returned
     * @param integer $limit limits number of results
     * @param integer $offset starting record for return
     * @return array $results if records exists or false
     *
     */
    public function search($condition='', $order='', $limit=0, $offset=0)
    {
        $query = 'SELECT ';
        $from = $this->tableName.', ';
        $where = '';
        foreach ($this->columns as $column) {
            // check if column is Foreign Key
            if (isset($this->foreignKeys[$column])) {
                $foreign_table = strtolower(
                    $this->foreignKeys[$column]['table']);
                /* self referencing foreign keys are
                 * treated as standard columns
                 */
                if ($foreign_table == $this->tableName) {
                    $foreign_id = isset($this->foreignKeys[$column]['column']) ?
                        $this->foreignKeys[$column]['column'] : 'id';
                    $display = isset($this->foreignKeys[$column]['display']) ?
                        $this->foreignKeys[$column]['display'] : $column;
                    $query .= $foreign_table.'_parent.'
                        .$display.' AS '.$column.', ';
                    $from .= $foreign_table.' '.$foreign_table.'_parent, ';
                    $where .= $this->tableName.'.'.$column.'='
                        .$foreign_table.'_parent.'.$foreign_id.' AND ';
                } else {
                    $foreign_id = isset($this->foreignKeys[$column]['column']) ?
                        $this->foreignKeys[$column]['column'] : 'id';
                    $display = isset($this->foreignKeys[$column]['display']) ?
                        $this->foreignKeys[$column]['display'] : $foreign_id;
                    $query .= $foreign_table.'.'.$display.' AS '.$column.', ';
                    $from .= $foreign_table.', ';
                    $where .= $this->tableName.'.'.$column.'='
                        .$foreign_table.'.'.$foreign_id.' AND ';
                }
            } else {
                // standard column
                $query .= $this->tableName.'.'.$column.' AS '.$column.', ';
            }
        }
        // remove last coma from $query
        $query2 = substr($query, 0, -2);
        // remove last coma from $from
        $from = substr($from, 0, -2);

        // final query
        $query2 .= ' FROM '.$from;

        // WHERE
        if ($where != '') {
            // remove last AND from $where
            $where = substr($where, 0, -5);
            $query2 .= ' WHERE '.$where;
            if ($condition != '') {
                $query2 .= ' AND '.$condition;
            }
        } else {
            if ($condition != '') {
                $query2 .= ' WHERE '.$condition;
            }
        }

        // ORDER BY clausule
        if ($order != '') {
            $query2 .= ' ORDER BY '.$order;
        }

        // LIMIT clausule
        if ($limit > 0) {
            $query2 .= ' LIMIT '.$limit;
        }

        // offset clausule
        if ($offset > 0) {
            $query2 .= ' OFFSET '.$offset;
        }

        $result = $this->query($query2);

        unset($query, $query2);

        // check for any result
        if ($this->nRows >= 1) {
        // check for one result
        // fill class properties with first result row
            foreach ($result[0] as $key => $value) {
                $this->$key = $value;
            }
            return $result;
        } else {
            return false;
        }
    }


    /**
     * Gets next ID value from model
     *
     * @return integer $next_id
     */
    public function getNextId()
    {

        $query = 'SELECT COALESCE(MAX('.$this->primary_key
            .'), 0) + 1 AS next_id FROM '.$this->tableName;
        $result = $this->query($query);

        $next_id = intval($result[0]['next_id']);

        unset ($query, $result);

        return $next_id;
    }
}
