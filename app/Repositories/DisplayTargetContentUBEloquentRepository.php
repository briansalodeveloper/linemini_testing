<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\MainRepositoryInterface;
use App\Interfaces\DisplayTargetContentUBRepositoryInterface;
use App\Models\DisplayTargetContentUB;

class DisplayTargetContentUBEloquentRepository extends MainEloquentRepository implements MainRepositoryInterface, DisplayTargetContentUBRepositoryInterface
{
    /*======================================================================
     * PROPERTIES
     *======================================================================*/

    /**
     * @var DisplayTargetContentUB $Model
     */
    public $Model = DisplayTargetContentUB::class;

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
     * @return DisplayTargetContentUB
     */
    public function acquire($id)
    {
        return parent::acquire($id);
    }

    /**
     * add a DisplayTargetContentUB record
     *
     * @param Array $attributes
     * @return Bool/DisplayTargetContentUB
     */
    public function add(array $attributes)
    {
        return parent::add($attributes);
    }

    /**
     * adjust a DisplayTargetContentUB record
     *
     * @param Int $id
     * @param Array $attributes
     * @return Bool/DisplayTargetContentUB
     */
    public function adjust(int $id, array $attributes)
    {
        return parent::adjust($id, $attributes);
    }

    /**
     * annul a DisplayTargetContentUB record
     *
     * @param Int $id
     * @return Bool
     */
    public function annul(int $id)
    {
        return parent::annul($id);
    }
}
