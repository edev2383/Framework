<?php

namespace Edev\Database\Query\Processor;

use Edev\System\Helpers\Arr;

class Update extends Processor
{

    /**
     * Create the query by steps, then compress into a string by joining
     *
     * @return string query
     */
    public function run()
    {
        // query creation step methods
        $this->_update();
        $this->_from();
        $this->_reorderIdentifier();
        $this->_set();
        $this->_where();

        // return compressed query string
        return $this->compressQuery();
    }

    /**
     * Move the model identifier from the where-binding to the end of 
     * the array, since the values are returned via ::data(), using
     * array_values, and the pdo statement uses relative values, we need
     * to move the identifier to the end so it can line up with 
     * "WHERE id=?"
     *
     * @return void
     */
    private function _reorderIdentifier()
    {
        $id = current($this->bindings['id']);
        $tmp = $this->bindings['where'][$id];
        unset($this->bindings['where'][$id]);
        $this->bindings['where'][$id] = $tmp;
    }

    /** ****************************************************************
     * Query Creation Step Methods
     ******************************************************************/
    private function _update()
    {
        $this->query[] = 'UPDATE';
    }

    private function _from()
    {
        $table = current($this->bindings['from']);
        $this->query[] = $table;

        $this->_inject(
            'edited_by',
            \Edev\Resource\User\User::getInstance()->get('employeeId')
        );
    }

    private function _set()
    {
        // get the model identifier
        $id = current($this->bindings['id']);

        $this->query[] = 'SET';
        $columns = [];

        // get keys for query string "SET value=?"
        $keys = array_keys($this->bindings['where']);
        foreach ($keys as $value) {
            // ignore the model identifier, it's addressed in _where()
            if ($value !== $id) {
                $columns[] = "$value=?";
            }
        }

        $this->query[] = join(', ', $columns);
    }

    private function _where()
    {
        // get the model identifier
        $id = current($this->bindings['id']);
        $this->query[] = 'WHERE';
        $this->query[] = "$id=?";
    }
}