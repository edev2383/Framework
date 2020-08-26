<?php

namespace Edev\Model;

use Edev\App\Logger\Log;
use Edev\Database\Container;
use Edev\Database\Model\Builder as Builder;
use Edev\Database\Query;
use Edev\System\Helpers\Str;

class Model
{
    protected $table;
    protected $identifier = 'id';

    protected $pdoName = 'default';

    public $relations = [];

    public $attributes = [];

    public function __construct()
    {
        $this->pageLoad();
    }

    protected function pageLoad()
    {
        $this->loadRelations();

        // $this->output();
    }
    protected function loadRelations()
    {
        //
        foreach ($this->attributes as $key) {
            $camelKey = Str::camel_case($key);
            $this->getRelationFromMethod($key, $camelKey);
        }
    }
    public function output()
    {
        // echo '<pre>';
        // print_r($this);
    }
    protected function getRelationFromMethod($key, $camelKey)
    {

        $relation = $this->$camelKey();

        if (!method_exists($this, $camelKey)) {
            throw new \Exception('Model\getRelationFromMethod()');
        }

        return $this->relations[$key] = $relation->getResults();
    }

    public static function getLast($columns = null)
    {
        $columns = empty(func_get_args()) ? '*' : func_get_args();
        return self::query()->latest('id')->limit(1)->get($columns);
    }

    public static function lastId()
    {
        return self::query()->get('MAX(id)');
    }

    public static function all($columns = null)
    {
        $columns = empty(func_get_args()) ? ['*'] : func_get_args();
        return self::query()->get(implode(', ', $columns));
    }

    public static function getById(int $id, $columns = '*')
    {
        $columns = is_array($columns) ? implode(', ', $columns) : $columns;
        return self::query()->where('id', $id)->get($columns);
    }

    public static function latest(string $column)
    {
        return self::query()->latest($column);
    }

    public static function oldest(string $column)
    {
        return self::query()->oldest($column);
    }

    public static function where($column, $operator = null, $value = null)
    {
        return self::query()->where($column, $operator, $value);
    }

    public static function whereNot($column, $value)
    {
        return self::query()->where($column, '!=', $value);
    }

    public static function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        return self::query()->whereIn($column, $values, $boolean, $not);
    }
    public static function whereBetween($column, $values, $boolean = 'and', $not = false)
    {
        return self::query()->whereBetween($column, $values, $boolean, $not);
    }
    public static function whereNotIn($column, $values, $boolean = 'and', $not = false)
    {
        return self::query()->whereNotIn($column, $values, $boolean, true);
    }

    public static function whereNull($column, $boolean = 'and', $not = false)
    {
        return self::query()->whereNull($column, $boolean, $not);
    }
    public static function whereNotNull($column, $boolean = 'and')
    {
        return self::query()->whereNull($column, $boolean, true);
    }
    public static function save(array $values)
    {
        return self::query()->save($values);
    }

    public static function update(array $values)
    {
        return self::query()->update($values);
    }

    /**
     * Not compatible w/ softDelete(). If you want to use softDelete
     * get the id where the column/value combo exists and use that id
     * to Model::delete(id)
     *
     * @param string $column
     * @param string @operator
     * @param mixed $value
     * @param string $boolean
     *
     * @return bool query execution result
     */
    public static function deleteWhere($column, $operator = null, $value = null, $boolean = 'and')
    {
        return self::query()->deleteWhere($column, $operator, $value, $boolean);
    }

    /**
     * Delete will check for `deleted_at` field. If present, it
     * will try to soft-delete by updating the deleted_at field and
     * updating the employee_id i.e., `deleted_by`. If not `deleted_at`
     * is present, it preforms a standard delete operation
     *
     * @param int $id
     *
     * @return bool
     */
    public static function delete(int $id): bool
    {
        if (self::hasSoftDelete($id)) {
            return self::softDelete($id);
        }
        return self::query()->delete($id);
    }

    /**
     * Check for the `deleted_at` column, using (new static ) because
     * we need to reference the $table value, so we need to be in $this
     *
     * @param int $id
     *
     * @return bool
     */
    private static function hasSoftDelete(int $id)
    {
        return (new static )->_hasSoftDelete($id);
    }

    /**
     * Simple container method to return _fieldExists method bool
     *
     * @param int $id
     */
    private function _hasSoftDelete(int $id)
    {
        return $this->_fieldExists('deleted_at', $this->table);
    }

    /**
     * If `deleted_at` is already set (read: not null), then perform
     * a standard delete. Otherwhise, perform an update query, setting
     * the values for `deleted_at` and `deleted_by`
     *
     * @param int $id
     */
    private static function softDelete(int $id)
    {
        if (self::where('id', $id)->get('deleted_at')) {
            return self::query()->delete($id);
        }
        $deleted_at = date('Y-m-d H:i:s');
        $deleted_by = \Edev\Resource\User\User::getInstance()
            ->get('employeeId');
        return self::update(compact('id', 'deleted_at', 'deleted_by'));
    }

    /**
     * Ctrl-Z a soft Delete
     *
     * Only works if the table has BOTH deleted_at and deleted_by colums
     *
     * @param integer $id
     * @return void
     */
    public static function restore(int $id)
    {
        return self::update(
            [
                'id' => $id,
                'deleted_at' => null,
                'deleted_by' => null,
            ]
        );
    }
    /**
     * Static container to return a newQuery() method, which contains
     * the model builder creation logic
     *
     * @return \Edev\Database\Query\Builder
     */
    public static function query()
    {
        return (new static )->newQuery();
    }

    /**
     *
     * @return
     */
    public function newQuery()
    {
        return $this->newModelBuilder()->setModel($this);
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }
    public function newBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * Return a new Model Builder object
     */
    public function newModelBuilder()
    {
        return $this->newBuilder($this->newQueryBuilder());
    }

    /**
     *
     * @return \Edev\Database\Query\Builder
     */
    public function newQueryBuilder()
    {
        return new \Edev\Database\Query\Builder(
            $this->resolvePdoConnection()
        );
    }

    /**
     *
     * @return pdo connection
     */
    public function resolvePdoConnection()
    {
        return Container::getInstance()->getConnectionByName($this->pdoName);
    }

    public function hasMany($relatedModel)
    {
        $instance = new $relatedModel;

        return new \Edev\Model\Relation\HasMany(
            $instance->newQuery(),
            $this
        );
    }
    public function hasOne($relatedModel)
    {
        $instance = new $relatedModel;

        return new \Edev\Model\Relation\HasOne(
            $instance->newQuery(),
            $this
        );
    }

    public function hasManyThrough($relatedModel, $throughModel)
    {
        $through = new $throughModel;
        return new \Edev\Model\Relation\HasManyThrough(
            (new $relatedModel)->newQuery(),
            $this,
            $through
        );
    }

    public function getForeignKey()
    {
        return snake_case(class_basename($this) . '_id');
    }

    public static function find($id, $columns = ['*'])
    {
        return (new static )->newQuery()->find($id, $columns);
    }

    public static function newPdo()
    {
        return (new static )->resolvePdoConnection();
    }

    /**
     * Check if a field exists in a table using information_schema
     *
     * @param string $column_name
     * @param string $table_name
     * @return void
     */
    protected function _fieldExists(string $column_name, string $table_name)
    {
        $pdo = \Edev\Database\Container::getInstance()
            ->getConnectionByName('client');
        $query = 'SELECT * FROM information_schema.COLUMNS WHERE
            table_name=:table_name AND column_name=:column_name';
        $stmt = $pdo->prepare($query);
        if ($stmt->execute(compact('column_name', 'table_name'))) {
            return $stmt->rowCount();
        }
    }

    public function getCreatedAtColumn() {
        return null;
    }

    /**
     * Shortcut log method for all child Model classes
     *
     * @param string $level
     * @param string $message
     * @param array $context
     *
     * @return \Edev\App\Logger\Log
     */
    protected function log(string $level, $message, $context = [])
    {
        $message = __CLASS__ . '::' . __FUNCTION__ . " - $message";
        return Log::log($level, $message, $context);
    }

    public function setTable(string $table)
    {
        $this->table = $table;
    }

    public function setPdoName(string $pdoName) {
        $this->pdoName = $pdoName;
    }

    public static function count()
    {
        return (new static )->newQuery()->get('COUNT(*)');
    }
}
