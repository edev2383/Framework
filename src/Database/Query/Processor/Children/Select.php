<?php

namespace Edev\Database\Query\Processor;

use Edev\System\Helpers\Arr;

class Select extends Processor
{
    /**
     * Create the query by steps, then compress into a string by joining
     *
     * @return string query
     */
    public function run()
    {
        // Query creation step methods
        $this->_select();
        $this->_from();
        $this->_where();
        $this->_orderBy();
        $this->_limit();
        $this->_max();
        $this->_groupBy();

        if (isset($_GET['preview_query_object'])) {
            Arr::pre($this);
        }
        // return compressed query string
        return $this->compressQuery();
    }

    /** ****************************************************************
     * Query Creation Step Methods
     ******************************************************************/
    private function _select()
    {
        $this->query[] = 'SELECT';
        $this->query[] = implode(', ', $this->bindings['select']);
    }

    private function _from()
    {
        $table = current($this->bindings['from']);
        $this->query[] = 'FROM';
        $this->query[] = $table;

        $this->_injections();
    }

    /**
     * This method needs some documentation and clean up
     *
     * */
    private function _where()
    {

        if (!empty($this->wheres)) {
            //
            $len = count($this->wheres);
            for ($ii = 0; $ii < $len; $ii++) {
                $curr = $this->wheres[$ii];
                // print_r($curr);
                extract($curr);
                if ($ii != 0) {
                    $this->query[] = $boolean;
                } else {
                    $this->query[] = 'WHERE';
                }
                if ($type == 'In' || $type == 'NotIn') {
                    $this->query[] = $this->_whereIn($column, $type, $values);
                } else if ($type == 'between') {
                    $this->query[] = $this->_between($column, $values, $not);
                } else if (strtolower($type) == 'null' || strtolower($type) == 'notnull') {
                    $this->query[] = $this->_null($column, $type);
                } else {
                    $this->query[] = "$column $operator ?";
                }
            }
        }
    }

    private function _orderBy()
    {
        if (!empty($this->bindings['orderBy'])) {
            extract($this->bindings['orderBy']);
            $order = $this->bindings['orderBy'];
            $this->query[] = 'ORDER BY';
            $this->query[] = $order[0];
            $this->query[] = $this->__validateSortDirection($order[1]);
        }
    }

    private function __validateSortDirection(string $sortDirection = null)
    {
        $allowed = ['ASC', 'DESC'];
        return in_array(strtoupper($sortDirection), $allowed) ? strtoupper($sortDirection) : current($allowed);
    }

    private function _limit()
    {
        if (!empty($this->bindings['limit'])) {
            $limit = $this->bindings['limit'];
            $this->query[] = 'LIMIT';
            $this->query[] = current($limit);
        }
    }

    private function _max()
    {
        if (!empty($this->bindings['max'])) {
            $this->query[] = ',';
            $this->query[] = current($this->bindings['max']);
        }
    }

    private function _groupBy()
    {
        if (!empty($this->bindings['group'])) {
            $this->query[] = 'GROUP BY';
            $this->query[] = implode(',', $this->bindings['group']);
        }
    }

    /*******************************************************************
     * Where Type Handler Methods
     *   - These methods handle the various differnet types of where
     *     statements, i.e., NULL, IN, LIKE, BETWEEN, etc
     *     Notes:
     *           1.) The conditional conjunctions (and, or) are handled
     *               in the ::_where()  [var: $boolean]
     *           2.) Due to the different handling of each expression,
     *               All of these methods are documented below. The other
     *               Processor child classes are not as extensively
     *               Documented, because their behavior is more linear
     *******************************************************************/

    /**
     * Handles IS NULL, IS NOT NULL statement
     *
     * @param string $column
     * @param string $type
     * @return string => "column IS NULL"
     * */
    private function _null($column, $type)
    {
        $isNull = strtolower($type) == 'null' ? 'IS NULL' : 'IS NOT NULL';
        return "$column $isNull";
    }

    /**
     * Handles IN v NOT IN (...)
     *
     * @param string $column
     * @param string $type
     * @param string $values - values is only passed for the count
     *
     * @return string => "column NOT IN (?, ?, ?)"
     * */
    private function _whereIn($column, $type, $values)
    {
        $in = $type == "In" ? 'IN' : 'NOT IN';
        return "$column $in (" . join(', ', array_fill(0, count($values), '?')) . ")";
    }

    /**
     * BETWEEN/NOT BETWEEN
     *
     * @param string $column
     * @param string $type
     * @param bool $not
     *
     * @return string => "column NOT BETWEEN ? AND ?"
     * */
    private function _between($column, $values, $not = false)
    {
        $btwn = $not ? 'NOT BETWEEN' : 'BETWEEN';
        return "$column $btwn ? AND ?";
    }

    /**
     *
     *
     * */
    private function _like()
    {
    }

    /**
     * DATA/VALUE INJECTION METHODS
     */
    protected function _injections()
    {
        $this->_excludeDeletedAtRecords();
    }

    /**
     * Injection to ignore deleted_at records by default in all queries
     * These records must be explicitly requested
     * 
     *
     * @return void
     */
    private function _excludeDeletedAtRecords()
    {
        $removeSoftDeletes = empty($this->bindings['include_deleted']);
        if ($removeSoftDeletes) {

            // if (isset($_GET['preview_query_object'])) {
            $column = 'deleted_at';
            // $columnExists = array_key_exists($column, $this->bindings['where']);
            $wheresExists = in_array($column, array_column($this->wheres, 'column'));
            if (!$wheresExists) {
                if ($this->_fieldExists($column)) {
                    $this->wheres[] = [
                        'type' => 'null',
                        'column' => 'deleted_at',
                        'boolean' => 'and'
                    ];
                }
            }
            // }
        }
    }
}