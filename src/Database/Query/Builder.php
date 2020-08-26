<?php

namespace Edev\Database\Query;

use Edev\Database\Query\Processor;
use Edev\Resource\Email;
use Edev\System\Helpers\Arr;
use Edev\Model\MockModel;

class Builder
{
    protected $wheres = [];

    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=', '<=>', 'like', 'not like', 'in', 'not in',
    ];

    protected $bindings = [
        // field names to select
        'select' => [],
        /**
         * passed as an array, only one value allowed, more needs to throw an exception
         */
        'from' => [],
        // value bindings only here, wheres[] houses the actual evaluations
        'where' => [],
        /**
         * identifier {string} match column name
         * sortDirection {string} ['ASC' (default) || 'DESC']
         */
        'orderBy' => [],
        /**
         * limit {number}
         * max {number} if max is set, limit becomes `start`, so query looks like this; LIMIT x, max
         */
        'limit' => [],
        'max' => [],
        'id' => [],
        'group' => [],
        'include_deleted' => []
    ];

    protected $forceRound = false;

    protected function addBinding($value, $type = 'where')
    {
        if (!array_key_exists($type, $this->bindings)) {
            throw new \Exception('Invalid binding.');
        }

        if (is_array($value)) {
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
        } else {
            $this->bindings[$type][] = $value;
        }
    }

    public function __construct($connection)
    {

        $this->connection = $connection;
    }

    public function runSelect($columns)
    {
        if (is_array($columns)) {
            $this->bindings['select'] = array_merge($this->bindings['select'], $columns);
        } else {
            $this->addBinding($columns, 'select');
        }
        // echo 'runSelect Model()';

        return $this->_processQuery('select');
    }

    public function processDeleteWhere()
    {
        return $this->_processQuery('delete');
    }

    private function _processQuery($type, array $data = [])
    {

        if (!empty($data)) {
            $this->bindings['where'] = $data;
        }

        $processor = $this->_newProcessor($type, $this->bindings, $this->wheres);
        return $this->execute(
            $processor->run(),
            $processor->data()
        );
    }

    private function _newProcessor($type, array $bindings, array $wheres = [])
    {
        switch ($type) {
            case 'select':
                return new \Edev\Database\Query\Processor\Select($bindings, $wheres);
            case 'insert':
                return new \Edev\Database\Query\Processor\Insert($bindings, $wheres);
            case 'update':
                return new \Edev\Database\Query\Processor\Update($bindings, $wheres);
            case 'delete':
                return new \Edev\Database\Query\Processor\Delete($bindings, $wheres);
        }
        return false;
    }
    public function get($columns = null)
    {
        $columns = empty(func_get_args()) ? ['*'] : func_get_args();
        return $this->runSelect($columns);
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column)) {
            return $this->_addArrayOfWheres($column, $boolean);
        }

        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        if (is_null($value)) {
            return $this->whereNull($column, $boolean, $operator !== '=');
        }

        $type = 'basic';

        $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');

        $this->addBinding($value);

        // echo '<pre>';
        // print_r($this->wheres);
        return $this;
    }

    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotIn' : 'In';
        $this->wheres[] = compact('type', 'column', 'values', 'boolean');
        $this->addBinding($values, 'where');
    }
    private function invalidOperator($operator)
    {
        // if (isset($_GET['__debug'])) {
        //     Arr::pre($operator);
        // }
        return !in_array(strtolower($operator), $this->operators, true);
    }

    private function _addArrayOfWheres($column, $boolean, $method = 'where')
    {
        foreach ($column as $key => $value) {
            if (is_array($value)) {
                $this->addNestedWhere(array_values($value), $boolean);
            }
        }
    }

    private function addNestedWhere($query, $boolean)
    {
        extract($query);

        $type = 'nested';

        $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');

        $this->addBinding($value);

        return $this;
    }

    public function whereNot()
    {
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        return $this->where($column, $operator, $value, 'or');
    }

    public function whereFirst()
    {
        return $this;
    }
    public function whereLast()
    {
        return $this;
    }
    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'between';

        $this->wheres[] = compact('type', 'column', 'values', 'boolean', 'not');

        $this->addBinding($values, 'where');

        return $this;
    }
    public function orWhereBetween($column, array $values)
    {
        return $this->whereBetween($column, $values, 'or');
    }

    public function whereNotBetween($column, array $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    public function whereNull($column, $boolean, $not = false)
    {
        $type = $not ? 'notnull' : 'null';

        $this->wheres[] = compact('type', 'column', 'boolean');

        return $this;
    }

    public function orderBy($identifier, $order = null)
    {
        // echo 'testing here Query\Builder->orderBy() <hr />';
        $this->addBinding(compact('identifier', 'order'), 'orderBy');

        return $this;
    }

    public function newQuery()
    {
        return new Builder($this->connection);
    }

    public function results()
    {
        // echo '<pre>';
        // print_r($this);
        // echo 'Query\results()';
    }

    public function from($table)
    {
        $this->addBinding($table, 'from');
    }

    public function identifier($identifier)
    {
        $this->addBinding($identifier, 'id');
        $this->identifier = $identifier;
    }

    private function _getTable()
    {
        return current($this->bindings['from']);
    }

    public function execute($query, $data = null)
    {
        if ($data != null && !is_array($data)) {
            throw new \PDOException('Data type mismatch: data must be null OR array.');
        }

        if (!$this->connection) {
            if (isset($_GET['debug'])) {
                echo '<pre>';
                $x = \Edev\Database\Container::getInstance();
                Arr::pre($x);
                debug_print_backtrace();
            }

            echo '<pre>';
            debug_print_backtrace();
            die('<h2>no connection</h2>');
        }

        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);


        if (isset($_GET['test'])) {
            echo "<h3>query: $query</h3>";
            print_r($data);
        }
        $stmt = $this->connection->prepare($query);
        try {
            if ($stmt->execute($data)) {
                return $this->_handleExecuteResponse($stmt);
            } else {

                Email::to('jeff@jasoncases.com')
                    ->subject('DB ERROR')
                    ->message(json_encode($this->connection->errorInfo()))
                    ->from('no-reply@zerodock.com', 'Edev Database Error')
                    ->compose();

                // ! DEBUG
                echo '<h3>Error in Query Builder</h3>';
                $time = date('H:i:s');
                echo "<p>[ time: $time ]</p>";
                echo "<p>[ query: $query ]</p>";
                print_r($data);
                throw new \PDOException('Error executing query. <br />' . __CLASS__ . '\\' . __FUNCTION__);
            }
        } catch (\PDOException $e) {
            // echo 'testing';
            // $time = date('H:i:s');
            // echo "[ time: $time ]";
            // echo "[ query: $query ]";
            echo $e->getMessage();
            if ($e->errorInfo[1] == MYSQL_CODE_DUPLICATE_KEY) {
                echo $e->getMessage();
            }
        }
    }

    private function _handleExecuteResponse($pdoStatement) {
        $action = strtolower(trim(explode(' ', $pdoStatement->queryString)[0]));
        // IF THE ACTION IS SELECT, RETURN A FETCH STATEMENT
        // OTHERWISE RETURN TRUE BOOL VALUE
        if ($action == 'select') {
            return $this->forceRound ? $this->_trimToNull($pdoStatement->fetchAll(\PDO::FETCH_ASSOC)) : $this->_trimResults($pdoStatement->fetchAll(\PDO::FETCH_ASSOC));
        } else if ($action == 'delete' || $action == 'update') {
            return $pdoStatement->rowCount();
        } else {
            return $this->_returnInsertId();
        }
    }

    private function _returnInsertId() {
        $table = current($this->bindings['from']);
        $mock = MockModel::create($table);
        return $mock->get('MAX(id)');
    }


    private function _trimToNull($result)
    {
        if (is_null($result) || empty($result)) {
            return null;
        }
        return $result;
    }
    /**
     * Trim results from multi-level array when only one record is returned.
     * Then calls trimSingleResult to return current when only one value returned
     *
     * @param [type] $result
     * @return void
     */
    private function _trimResults($result)
    {
        if (is_null($result) || empty($result)) {
            return null;
        }
        return count($result) > 1 ? $result : $this->_trimSingleResult($result[0]);
    }

    private function _trimSingleResult($result)
    {
        return count($result) > 1 ? $result : current($result);
    }
    /**
     *
     */
    public function insert(array $values)
    {
        return $this->_processQuery('insert', $values);
    }



    public function deleteWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->where($column, $operator, $value, $boolean);
        return $this->_processQuery('delete');
    }

    /**
     *
     */
    public function update(array $values)
    {
        return $this->_processQuery('update', $values);
    }

    /**
     *
     */
    public function delete(int $id)
    {
        return $this->_processQuery('delete', [$id]);
    }

    /**
     * Changes the order of values to push the identifier to the last element
     *
     * @param array $updateValues
     * @return array
     */
    private function _formatUpdateValues(array $updateValues)
    {
        $id = $this->identifier;
        //
        if (!array_key_exists($id, $updateValues)) {
            throw new \Exception('Identifier not found in values sent');
        }

        $value = $updateValues[$id];
        unset($updateValues[$id]);
        $updateValues[$id] = $value;

        return $updateValues;
    }

    public function group($column)
    {

        $this->addBinding($column, 'group');
        return $this;
    }
    public function latest($column)
    {
        $this->orderBy($column, 'DESC');
        return $this;
    }

    public function oldest($column)
    {
        $this->orderBy($column, 'ASC');
        return $this;
    }

    public function limit($num, $max = null)
    {
        $this->addBinding($num, 'limit');
        if (!is_null($max)) {
            $this->addBinding($max, 'max');
        }
        return $this;
    }

    public function setRound()
    {
        $this->forceRound = true;
    }

    public function paginate($pageNum, $recPerPage)
    {
        $start = ($pageNum - 1) * $recPerPage;
        $this->limit($start, $recPerPage);
        return $this;
    }

    public function includeDeleted()
    {
        $this->addBinding(true, 'include_deleted');
        return $this;
    }
}