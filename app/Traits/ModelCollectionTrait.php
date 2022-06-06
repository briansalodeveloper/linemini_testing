<?php

namespace App\Traits;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ModelCollectionTrait
{
    /**
     * convert array as pagination
     *
     * @param Array $items
     * @param Int $perPage
     * @param Null/Int $page
     * @param Array $options
     * @return LengthAwarePaginator
     */
    public function arrayToPagination(array $items, int $perPage = 5, $page = null, $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /**
     * convert array as collection
     *
     * @param Array $items
     * @return Collection
     */
    public function arrayToCollection(array $items): Collection
    {
        return Collection::make($items);
    }
}
