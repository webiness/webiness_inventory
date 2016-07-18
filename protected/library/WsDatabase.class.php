<?php
/**
 * WsDatabase
 * Is base class for working with database objects in Webiness
 * framework. It handles connection to database server and exeecution of
 * SQL commands.
 *
 * Example usage:
 *
 * <code>
 * $db = new WsDatabase()
 *
 * # query database
 * $res = $db->query('SELECT name FROM mytable WHERE id<=:id', array(
 *      ':id' => 3
 * ));
 * # number of results
 * $number_of_results = $db->nRows;
 *
 * # update record in database
 * $db->execute('UPDATE mytable SET name=:name WHERE id=:id',
 *     array(':name' => 'new', ':id' => 3));
 * </code>
 *
 */
class WsDatabase
{
    /**
     * @var PDO $_dbh PDO database handler
     *
     */
    private $_dbh;
    /**
     * @var integer $nRows Number of affected or returned rows
     * @see query()
     * @see execute()
     *
     */
    public $nRows;
    /**
     * @var boolean $isConnected Is database connection live
     *
     */
    public $isConnected = false;


    public function __construct()
    {
        // PDO connection string
        $cs = WsConfig::get('db_driver');
        if (WsConfig::get('db_driver') != 'sqlite') {
            $cs .= ':host='.WsConfig::get('db_host');
            if (WsConfig::get('db_port')) {
                $cs .= ';port='.WsConfig::get('db_port');
            }
            $cs .= ';dbname='.WsConfig::get('db_name');
        } else {
            $cs .= ':'.dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR;
            $cs .= 'runtime'.DIRECTORY_SEPARATOR.'webiness.db';
        }

        // connect to database
        try {
            $this->_dbh = new PDO($cs,
                WsConfig::get('db_user'), WsConfig::get('db_password'));
            $this->_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $this->isConnected = true;
            // enable foreign keys in sqlite
            if (WsConfig::get('db_driver') === 'sqlite') {
                $this->execute('PRAGMA foreign_keys = ON');
            }
        } catch(PDOException $ex) {
            $this->isConnected = false;
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error($ex->getMessage(), E_USER_ERROR);
        }

        unset($cs);
    }


    public function __destruct()
    {
        $this->_dbh = null;
    }


    /**
     * Prepare and execute custom SQL query that return results (SELECT).
     *
     * @param string $sql Custom SQL query
     * @param array $parameters List of parameters forwarded to $query
     * @return array $results Results of SQL query
     *
     */
    public function query($sql, $parameters = array())
    {
        if (!$this->isConnected) {
            return false;
        }

        // prepare SQL statment
        $sth = $this->_dbh->prepare($sql,
            array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

        // bind values
        foreach ($parameters as $key => &$value) {
            if (is_int($value)) {
                $param = PDO::PARAM_INT;
            } else if (is_bool($value)) {
                $param = PDO::PARAM_BOOL;
            } else if (is_null($value)) {
                $param = PDO::PARAM_NULL;
            } else if (is_string($value)) {
                $param = PDO::PARAM_STR;
            } else {
                $param = false;
            }

            if ($param) {
                $sth->bindValue(":$key", $value, $param);
            }
        }

        $this->nRows = 0;

        try {
            $sth->execute();
        } catch(PDOException $ex) {
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error($sql, E_USER_ERROR);
            return false;
        }

        $values = array();
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $values[] = $row;
            $this->nRows++;
        }
        $sth->closeCursor();

        unset($row, $sth);

        return $values;
    }


    /**
     * Prepare and execute custom SQL query that don't return results
     * (INSERT, UPDATE, DELETE).
     *
     * @param string $sql Custom SQL query
     * @param array $parameters List of parameters forwarded to $query
     *
     */
    public function execute($sql, $parameters = array())
    {
        if (!$this->isConnected) {
            return false;
        }

        // prepare SQL statement
        $sth = $this->_dbh->prepare($sql);

        // bind values
        foreach ($parameters as $key => $value) {
            if (is_numeric($value)) {
                $sth->bindValue(":$key", $value, PDO::PARAM_INT);
            } else if (is_bool($value)) {
                if (WsConfig::get('db_driver') === 'pgsql') {
                    $v = $value ? 't' : 'f';
                    $sth->bindValue(":$key", $v, PDO::PARAM_STR);
                } else {
                    $v = $value ? 1 : 0;
                    $sth->bindValue(":$key", $v, PDO::PARAM_INT);
                }
            } else if (is_null($value)) {
                $sth->bindValue(":$key", $value, PDO::PARAM_NULL);
            } else {
                $sth->bindValue(":$key", $value, PDO::PARAM_STR);
            }
        }

        // execute query
        $this->_dbh->beginTransaction();
        if (!$sth->execute()) {
            $this->_dbh->rollBack();
            $this->nRows = 0;
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error('WsDatabase: <code>'.$sql.'</code>',
                E_USER_ERROR);
            return false;
        } else {
            $this->_dbh->commit();
            $this->nRows = $sth->rowCount();
        }
        $sth->closeCursor();

        unset($sth);
        return true;
    }


    /**
     * Closes connection to database server.
     *
     */
    public function close()
    {
        $this->_dbh = null;
    }
}
