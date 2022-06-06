<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\AdminRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Admin;

class AdminEloquentRepository implements AdminRepositoryInterface
{
    /**
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function all(): Collection
    {
        return Admin::all();
    }

    /**
     * @param int $contentId
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        $rtn = null;

        $rtn = Admin::paginate($perPage);

        return $rtn;
    }
}
