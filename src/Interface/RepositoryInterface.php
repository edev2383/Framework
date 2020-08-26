<?php

namespace Ifs;

interface RepositoryInterface
{
    //
    public function getById($id, array $columns, array $excludes);
    public function getAll(array $columns, array $excludes);
    public function getByColumnValue(array $criteria, array $columns, array $excludes);
    public function save(array $values);
    public function update($id, array $values = []);
    public function delete($id);
}
