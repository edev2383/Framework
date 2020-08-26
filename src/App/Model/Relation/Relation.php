<?php

namespace Edev\Model\Relation;

class Relation
{

    public function __construct($query, $parent)
    {
        $this->query = $query;
        $this->parent = $parent;
        $this->related = $query->getModel();
    }
}
