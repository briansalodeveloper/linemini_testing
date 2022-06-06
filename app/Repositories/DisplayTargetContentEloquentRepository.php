<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\MainRepositoryInterface;
use App\Interfaces\DisplayTargetContentRepositoryInterface;
use App\Models\DisplayTargetContent;

class DisplayTargetContentEloquentRepository extends MainEloquentRepository implements MainRepositoryInterface, DisplayTargetContentRepositoryInterface
{
    /*======================================================================
     * PROPERTIES
     *======================================================================*/

    /**
     * @var DisplayTargetContent $Model
     */
    public $Model = DisplayTargetContent::class;

    /*======================================================================
     * CUSTOM METHODS
     *======================================================================*/

    /**
     * acquire all ContentPlan records
     *
     * @return Collection
     */
    public function acquireAll()
    {
        return parent::acquireAll($id);
    }
    
    /**
     * acquire a ContentPlan record
     *
     * @param Int $id
     * @return DisplayTargetContent
     */
    public function acquire($id)
    {
        return parent::acquire($id);
    }

    /**
     * add a DisplayTargetContent record
     *
     * @param Array $attributes
     * @return Bool/DisplayTargetContent
     */
    public function add(array $attributes)
    {
        return parent::add($attributes);
    }

    /**
     * adjust a DisplayTargetContent record
     *
     * @param Int $id
     * @param Array $attributes
     * @return Bool/DisplayTargetContent
     */
    public function adjust(int $id, array $attributes)
    {
        return parent::adjust($id, $attributes);
    }

    /**
     * annul a DisplayTargetContent record
     *
     * @param Int $id
     * @return Bool
     */
    public function annul(int $id)
    {
        return parent::annul($id);
    }
}
