<?php

namespace App\Services;

use App\Interfaces\UnionMemberRepositoryInterface;
use App\Models\UnionMember;

class UnionMemberService extends MainService
{
    public function __construct(UnionMemberRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getDisplayTargetList()
    {
        $targetCodesList = [];
        $targetColumnList = UnionMember::NOTICE_TARGET_LIST;

        foreach ($targetColumnList as $target) {
            $targetCodesList[$target] = $this->repository->getDistinctColumnsPagination($target, [$target])->pluck($target)->toArray();
        }

        return $targetCodesList;
    }
}
