<?php

namespace App\Repositories;

use DB;
use App\Interfaces\UnionMemberRepositoryInterface;
use App\Models\UnionMember;

class UnionMemberEloquentRepository implements UnionMemberRepositoryInterface
{
    public function all()
    {
        //
    }

    public function findByContentId($unionMemberId)
    {
        //
    }

    public function getDistinctColumnsPagination($distinctColumn, $columns = ['*'])
    {
        return UnionMember::distinct([$distinctColumn])->whereNotNull($distinctColumn)->paginate(100, $columns);
    }
}
