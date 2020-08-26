<?php

namespace Edev\Database\Query\Processor;

use Edev\System\Helpers\Arr;

class Delete extends Processor
{

    /**
     * Create the query by steps, then compress into a string by joining
     *
     * @return string query
     */
    public function run()
    {

        // query creation step methods
        $this->_delete();
        $this->_from();
        $this->_where();

        // Arr::pre($this);
        // die();
        // return compressed query string
        return $this->compressQuery();
    }

    /** ****************************************************************
     * Query Creation Step Methods
     ******************************************************************/
    private function _delete()
    {
        $this->query[] = 'DELETE';
    }

    private function _from()
    {
        $this->query[] = 'FROM';
        $this->query[] = current($this->bindings['from']);
    }

    private function _where()
    {
        $this->query[] = 'WHERE';

        $this->_identifier();
    }

    private function _identifier()
    {
        if (empty($this->wheres)) {
            $id = current($this->bindings['id']);
            $this->query[] = "$id=?";
        } else {
            $this->_compactWheres();
        }
    }

    private function _compactWheres()
    {
        $len = count($this->wheres);

        for ($ii = 0; $ii < $len; $ii++) {
            $curr = $this->wheres[$ii];
            // print_r($curr);
            extract($curr);
            if ($ii != 0) {
                $this->query[] = $boolean;
            }

            $this->query[] = "$column $operator ?";
        }
    }
}