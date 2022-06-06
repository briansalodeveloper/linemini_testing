<?php

namespace App\Repositories;

use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\MainRepositoryInterface;
use App\Interfaces\ContentPlanRepositoryInterface;
use App\Models\ContentPlan;

class ContentPlanEloquentRepository extends MainEloquentRepository implements MainRepositoryInterface, ContentPlanRepositoryInterface
{
    /*======================================================================
     * PROPERTIES
     *======================================================================*/

    /**
     * @var ContentPlan $Model
     */
    public $Model = ContentPlan::class;

    /*======================================================================
     * CUSTOM METHODS
     *======================================================================*/

    /**
     * acquire all ContentPlan records
     *
     * @param Null/Int $contentType
     * @param Int $paginatePerPage
     * @return LengthAwarePaginator
     */
    public function acquireAll($contentType = null, $paginatePerPage = 10): LengthAwarePaginator
    {
        $rtn = $this->arrayToPagination([]);

        try {
            $query = $this->Model::whereNotDeleted();
    
            if ($contentType) {
                $query = $query->where('contentType', $contentType);
            }
    
            $rtn = $query->sortDesc()->paginate($paginatePerPage);
        } catch (\Exception $e) {
            \L0g::error('Exception: ' . $e->getMessage());
            \SlackLog::error('Exception: ' . $e->getMessage());
        } catch (\Error $e) {
            \L0g::error($e->getMessage());
            \SlackLog::error($e->getMessage());
        }

        return $rtn;
    }

    /**
     * acquire a ContentPlan record
     *
     * @param Int $id
     * @return ContentPlan
     */
    public function acquire($id)
    {
        return parent::acquire($id);
    }

    /**
     * add a ContentPlan record
     *
     * @param Array $attributes
     * @return Bool/ContentPlan
     */
    public function add(array $attributes)
    {
        return parent::add($attributes);
    }

    /**
     * adjust a ContentPlan record
     *
     * @param Int $id
     * @param Array $attributes
     * @return Bool/ContentPlan
     */
    public function adjust(int $id, array $attributes)
    {
        return parent::adjust($id, $attributes);
    }

    /**
     * annul a ContentPlan record
     *
     * @param Int $id
     * @return Bool
     */
    public function annul(int $id)
    {
        return parent::annul($id);
    }
}
