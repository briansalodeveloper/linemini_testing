<?php

namespace App\Interfaces;

use App\Interfaces\MainRepositoryInterface;

interface UnionMemberRepositoryInterface
{
    public function getDistinctColumnsPagination($distinctColumn, $columns = ['*']);
}
