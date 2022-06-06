<?php

namespace App\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface AdminRepositoryInterface
{
    /**
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator;
}
