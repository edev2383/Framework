<?php

namespace Edev\Model;

class MockModel{

    private $table;
    private $pdoName;

    public function __construct(string $table, string $pdoName = 'default')  {
        $this->table = $table;
        $this->pdoName = $pdoName;
    }

    public function build() {
        $model = new Model();
        $model->setTable($this->table);
        $model->setPdoName($this->pdoName);
        $builder = $model->newModelBuilder();
        $builder->setModel($model);
        return $builder;
    }

    public static function create(string $table, string $pdoName = 'default') {
        $mock = new MockModel($table, $pdoName);
        return $mock->build();
    }
}