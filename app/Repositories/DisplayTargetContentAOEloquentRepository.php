<?php

namespace App\Repositories;

// use DB;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\MainRepositoryInterface;
use App\Interfaces\DisplayTargetContentAORepositoryInterface;
use App\Models\DisplayTargetContentAO;

class DisplayTargetContentAOEloquentRepository extends MainEloquentRepository implements MainRepositoryInterface, DisplayTargetContentAORepositoryInterface
{
    /*======================================================================
     * PROPERTIES
     *======================================================================*/

    /**
     * @var DisplayTargetContentAO $Model
     */
    public $Model = DisplayTargetContentAO::class;

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
     * @return DisplayTargetContentAO
     */
    public function acquire($id)
    {
        return parent::acquire($id);
    }

    /**
     * add a DisplayTargetContentAO record
     *
     * @param Array $attributes
     * @return Bool/DisplayTargetContentAO
     */
    public function add(array $attributes)
    {
        return parent::add($attributes);
    }

    /**
     * adjust a DisplayTargetContentAO record
     *
     * @param Int $id
     * @param Array $attributes
     * @return Bool/DisplayTargetContentAO
     */
    public function adjust(int $id, array $attributes)
    {
        return parent::adjust($id, $attributes);
    }

    /**
     * annul a DisplayTargetContentAO record
     *
     * @param Int $id
     * @return Bool
     */
    public function annul(int $id)
    {
        return parent::annul($id);
    }
}
