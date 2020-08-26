<?php

namespace Edev\Database\Query\Processor;

class Insert extends Processor
{

    /**
     * Create the query by steps, then compress into a string by joining
     * 
     * 
     * @return string query
     */
    public function run()
    {

        // query creation step methods
        $this->_insert();
        $this->_from();
        $this->_columns();
        $this->_values();

        // return the comporessed query
        return $this->compressQuery();
    }


    /** ****************************************************************
     * Query Creation Step Methods
     ******************************************************************/
    private function _insert()
    {
        $this->query[] = 'INSERT INTO';
    }

    private function _from()
    {
        $table = current($this->bindings['from']);
        $this->query[] = $table;

        // inject system values
        // $this->_inject(
        //     'author',
        //     \Edev\Resource\User\User::getInstance()->get('employeeId')
        // );
        // $this->_inject(
        //     'manager_id',
        //     \Edev\Resource\User\User::getInstance()->get('employeeId')
        // );
        $this->_inject('_ip', $_SERVER['REMOTE_ADDR']);
    }

    private function _columns()
    {
        $columns = [];

        $this->query[] = '(';
        $data = array_keys($this->bindings['where']);

        foreach ($data as $value) {
            $columns[] = $value;
        }

        $this->query[] = join(', ', $columns);

        $this->query[] = ')';
    }

    private function _values()
    {
        $placeholders = [];
        $this->query[] = 'VALUES (';
        $len = count($this->bindings['where']);
        for ($ii = 0; $ii < $len; $ii++) {
            $placeholders[] = '?';
        }
        $this->query[] = join(', ', $placeholders);
        $this->query[] = ')';
    }
}