<?php

namespace Edev\Database\Query\Processor;

/**
 * Processor is a parent class for each query type, i.e., INSERT, UPDATE
 * etc. It takes the bindings from the Query/Builder and turns them into
 * an executable query string. It also has an ::_inject() method for 
 * inserting dedicated system values into the database. Currently we are
 * auto injecting: 
 *      `author` -> [INSERT] - logged in employee id creating the record
 *                  generally used when the creation is active process
 *      `manager_id` -> [INSERT] - same as author, but created to allow 
 *                      for some permission checking at a later release 
 *      `edited_by` -> [UPDATE] - logs employee id when a table is 
 *                      updated
 *      `_ip` -> [INSERT] - logs the ip address of the request, helpful
 *               as it removes the need to explicitly state it. Useful
 *               When working with logs, i.e., system_access_log
 */
class Processor
{

    // 
    protected $wheres;
    protected $bindings;

    // query container array
    protected $query = [];

    //
    public function __construct($bindings, $wheres)
    {
        $this->bindings = $bindings;
        $this->wheres = $wheres;
    }

    /**
     * Container method for all child processing
     *
     * @return void
     */
    public function run()
    {
    }

    /**
     * Return the values in the where binding
     *
     * @return void
     */
    public function data()
    {
        return array_values($this->bindings['where']);
    }


    /**
     * Container method for query injections
     *
     * @return void
     */
    protected function _injections()
    {
    }

    /**
     * Join the query array into the query string to return to builder 
     * execute
     *
     * @return void
     */
    protected function compressQuery()
    {
        return join(' ', $this->query);
    }

    /**
     * Inject a value
     *
     * @param string $column
     * @param string $table_name
     * @param mixed $value
     * @return void
     */
    protected function _inject(string $column, $value)
    {
        $table_name = current($this->bindings['from']);
        // check that the field is not in the current bindings
        if (!array_key_exists($column, $this->bindings['where'])) {
            // check that the field is in the database table
            // if true - create the binding
            if ($this->_fieldExists($column, $table_name)) {
                $this->bindings['where'][$column] = $value;
            }
        }
    }

    /**
     * Check if a field exists in a table using information_schema
     *
     * @param string $column_name
     * @param string $table_name
     * @return boolean 
     */
    protected function _fieldExists(string $column_name, string $table_name = null)
    {
        $table_name = $table_name ?: current($this->bindings['from']);
        $pdo = \Edev\Database\Container::getInstance()->getConnectionByName('default');
        if ($pdo) {
            $query = 'SELECT * FROM information_schema.COLUMNS WHERE table_name=:table_name AND column_name=:column_name';
            $stmt = $pdo->prepare($query);
            if ($stmt->execute(compact('column_name', 'table_name'))) {
                return boolval($stmt->rowCount());
            }
        }
        return false;
    }
}