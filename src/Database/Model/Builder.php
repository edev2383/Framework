<?php

namespace Edev\Database\Model;

use Edev\Database\Query\Builder as QueryBuilder;
use Edev\Model\Model;
use Edev\System\Helpers\Arr;

class Builder
{

    /**
     * Create a new Eloquent query builder instance.
     *
     * @param  \Database\Query\Builder  $query
     * @return void
     */
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }

    public function deleteWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column)) {
            return $this->_formatDeleteWhere($column, $boolean);
        }
        return $this->query->deleteWhere($column, $operator, $value, $boolean);
    }

    private function _formatDeleteWhere($column, $boolean = 'and')
    {
        foreach ($column as $val) {
            [$c, $o, $v] = $val;
            $this->query->where($c, $o, $v, $boolean);
        }
        return $this->query->processDeleteWhere();
    }

    public function save(array $values)
    {
        return $this->query->insert($values);
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        // print_r($value);
        // print_r(func_get_args());
        $this->query->where(...func_get_args());
        return $this;
    }

    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $this->query->whereIn($column, $values, $boolean, $not);
        return $this;
    }
    public function andWhereIn($column, $values, $boolean = 'and', $not = false)
    {
        $this->query->whereIn($column, $values, $boolean, $not);
        return $this;
    }
    public function whereNull($column, $boolean = 'and', $not = false)
    {
        $this->query->whereNull($column, $boolean, $not);
        return $this;
    }

    public function whereNotNull($column, $boolean = 'and')
    {
        $this->query->whereNull($column, $boolean, true);
        return $this;
    }

    public function andWhereNull($column, $boolean = 'and', $not = false)
    {
        $this->query->whereNull($column, $boolean, $not);
        return $this;
    }
    public function andWhereNotIn($column, $values, $boolean = 'and', $not = false)
    {
        $this->query->whereIn($column, $values, $boolean, true);
        return $this;
    }
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        $this->query->whereIn($column, $values, $boolean, true);
        return $this;
    }

    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $this->query->whereBetween($column, $values, $boolean, $not);
        return $this;
    }

    public function andWhereBetween($column, array $values)
    {
        $this->query->whereBetween($column, $values, 'and', false);
        return $this;
    }

    public function orWhereBetween($column, array $values)
    {
        $this->query->whereBetween($column, $values, 'or', false);
        return $this;
    }

    public function firstWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->where($column, $operator, $value, $boolean)->first();
    }

    public function first()
    {
        $this->query->oldest($column)->limit(1);
        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  \Closure|array|string  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        return $this->where($column, $operator, $value, 'or');
    }

    public function andWhere($column, $operator = null, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        return $this->where($column, $operator, $value, 'and');
    }
    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param  string  $column
     * @return $this
     */
    public function latest($column = null)
    {
        if (is_null($column)) {
            $column = $this->model->getCreatedAtColumn() ?? 'created_at';
        }

        $this->query->latest($column);

        return $this;
    }

    public function group($column)
    {

        foreach (func_get_args() as $groupField) {
            $this->query->group($groupField);
        }
        return $this;
    }

    public function orderBy($identifier, $order = null)
    {

        $this->query->orderBy($identifier, $order);
        return $this;
    }
    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param  string  $column
     * @return $this
     */
    public function oldest($column = null)
    {
        if (is_null($column)) {
            $column = $this->model->getCreatedAtColumn() ?? 'created_at';
        }

        $this->query->oldest($column);

        return $this;
    }

    /**
     *
     * @param mixed $columns - can be a series of strings, or an array
     *                         of strings
     *
     * @return mixed
     */
    public function get($columns = '*')
    {
        // if $columns is not an array, convert to default, or set to
        // array of incoming arguments, if it is an array, pass-thru
        if (!is_array($columns)) {
            $columns = empty(func_get_args()) ? ['*'] : func_get_args();
        }
        // end result is the same, an imploded array to string convert
        return $this->query->get(implode(', ', $columns));
    }

    public function last($column = 'id')
    {
        $this->query->latest($column)->limit(1);
        return $this;
    }

    public function results()
    {
        //
        echo 'results';
    }

    // /**
    //  * Paginate the given query.
    //  *
    //  * @param  int|null  $perPage
    //  * @param  array  $columns
    //  * @param  string  $pageName
    //  * @param  int|null  $page
    //  * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
    //  *
    //  * @throws \InvalidArgumentException
    //  */

    public function paginate($pageNum, $recordPerPage)
    {
        $this->query->paginate($pageNum, $recordPerPage);
        return $this;
    }

    /**
     * Update a record in the database.
     *
     * @param  array  $values
     * @return int
     */
    public function update(array $values)
    {
        return $this->query->update($values);
    }

    /**
     * Delete a record from the database.
     *
     * @return mixed
     */
    public function delete(int $id)
    {
        return $this->query->delete($id);
    }

    // /**
    //  * Set the relationships that should be eager loaded.
    //  *
    //  * @param  mixed  $relations
    //  * @return $this
    //  */
    // public function with($relations)
    // {
    //     $eagerLoad = $this->parseWithRelations(is_string($relations) ? func_get_args() : $relations);

    //     $this->eagerLoad = array_merge($this->eagerLoad, $eagerLoad);

    //     return $this;
    // }

    // /**
    //  * Prevent the specified relations from being eager loaded.
    //  *
    //  * @param  mixed  $relations
    //  * @return $this
    //  */
    // public function without($relations)
    // {
    //     $this->eagerLoad = array_diff_key($this->eagerLoad, array_flip(
    //         is_string($relations) ? func_get_args() : $relations
    //     ));

    //     return $this;
    // }

    /**
     * Get the model instance being queried.
     *
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set a model instance for the model being queried.
     *
     * @param  $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        $this->query->from($model->getTable());

        $this->query->identifier($model->getIdentifier());

        return $this;
    }

    public function find($id, $columns = ['*'])
    {
        //

        // echo '<h3>Model\Builder::find()</h3>';
        $this->floatRelations($id);

        // print_r($this);
        return $this;
    }

    private function _searchRelationViaForeignKey($id, $relation)
    {

        // echo '<h3>RELATION</h3>';
        // print_r($relation);
        if (isset($relation[0])) {
            $fK = $this->model->getForeignKey();
            return array_filter($relation, function ($v, $k) use ($fK, $id) {

                return $v[$fK] == $id;
            }, ARRAY_FILTER_USE_BOTH);
        }

        return $relation;
    }
    public function floatRelations($id)
    {
        $keys = $this->model->attributes;
        foreach ($keys as $value) {
            $currRelationModel = $this->model->relations[$value];
            $this->setAttribute($value, $this->_searchRelationViaForeignKey($id, $currRelationModel), $this->{$value} == null);
        }
    }

    public function setAttribute($key, $value, $overwrite = true)
    {
        if ($overwrite) {
            $this->{$key} = $value;
        } else {
            if (!is_null($this->{$key}) && !is_array($this->{$key})) {
                $tmp = $this->{$key};
                $this->{$key} = [$tmp];
            }
            $this->{$key}[] = $value;
        }
    }

    public function limit($num, $max = null)
    {
        $this->query->limit($num, $max);
        return $this;
    }

    public function round()
    {
        $this->query->setRound();
        return $this;
    }

    public function includeDeleted()
    {
        $this->query->includeDeleted();
        return $this;
    }
}