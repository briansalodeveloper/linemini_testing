<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Traits\ModelCollectionTrait;

class MainEloquentRepository
{
    use ModelCollectionTrait;

    /*======================================================================
     *======================================================================
     * SAMPLE CHILD CLASS
     *======================================================================

    public function acquireAll()
    {
        return parent::acquireAll();
    }

    public function acquire($id)
    {
        return parent::acquire($id);
    }

    public function add(array $attributes)
    {
        return parent::add($attributes);
    }

    public function adjust(int $id, array $attributes)
    {
        return parent::adjust($id, $attributes);
    }

    public function annul(array $id)
    {
        return parent::annul($id);
    }

    *======================================================================
    *======================================================================*/

    /*======================================================================
     * PROPERTIES
     *======================================================================*/

    /**
     * @var Model
     */
    public $Model;

    /*======================================================================
     * CONSTANTS
     *======================================================================*/

    /**
     * acquire all model records
     * call NTC (No Try Catch) method
     *
     * @return Collection
     */
    public function acquireAll()
    {
        $rtn = $this->arrayToCollection([]);

        try {
            $rtn = $this->NTCacquireAll();
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
     * acquire all model records
     * NTC (No Try Catch) method
     *
     * @return Collection
     */
    public function NTCacquireAll()
    {
        $rtn = $this->arrayToCollection([]);

        if (!empty($this->Model)) {
            $rtn = $this->Model::all();
        }

        return $rtn;
    }

    /**
     * acquire a model record
     * call NTC (No Try Catch) method
     *
     * @param Int $id
     * @return Model
     */
    public function acquire($id)
    {
        $rtn = false;
        
        try {
            $rtn = $this->NTCacquire($id);
        } catch (\Exception $e) {
            \L0g::error('Exception: ' . $e->getMessage());
            \SlackLog::error('Exception: ' . $e->getMessage());
        } catch (\Error $e) {
            \L0g::error($e->getMessage());
            \SlackLog::error($e->getMessage());
        }

        if (empty($rtn)) {
            $rtn = $this->Model::empty();
        }
    
        return $rtn;
    }

    /**
     * acquire a model record
     * NTC (No Try Catch) method
     *
     * @param Int $id
     * @return Model
     */
    public function NTCacquire($id)
    {
        $rtn = false;

        if (!empty($this->Model) && !empty($id)) {
            $rtn = $this->Model::find($id);
        }

        if (empty($rtn)) {
            $rtn = $this->Model::empty();
        }

        return $rtn;
    }

    /**
     * add a model record
     * call NTC (No Try Catch) method
     *
     * @param Array $attributes
     * @return Bool/Model
     */
    public function add(array $attributes)
    {
        $rtn = false;

        try {
            $rtn = $this->NTCadd($attributes);
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
     * add a model record
     * NTC (No Try Catch) method
     *
     * @param Array $attributes
     * @return Bool/Model
     */
    public function NTCadd(array $attributes)
    {
        $rtn = false;

        if (!empty($this->Model) && count($attributes) != 0) {
            $rtn = $this->Model::create($attributes);

            if ($rtn) {
                $rtn = $rtn->fresh();
            }
        }

        return $rtn;
    }

    /**
     * adjust a model record
     * call NTC (No Try Catch) method
     *
     * @param Int $id
     * @param Array $attributes
     * @return Bool/Model
     */
    public function adjust(int $id, array $attributes)
    {
        $rtn = false;
        try {
            $rtn = $this->NTCadjust($id, $attributes);
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
     * adjust a model record
     * NTC (No Try Catch) method
     *
     * @param Int $id
     * @param Array $attributes
     * @return Bool/Model
     */
    public function NTCadjust(int $id, array $attributes)
    {
        $rtn = false;

        if (!empty($this->Model) && count($attributes) != 0) {
            $model = $this->NTCacquire($id);

            if (!$model->isEmpty) {
                $rtn = $model->update($attributes);

                if ($rtn) {
                    $rtn = $model->fresh();
                }
            }
        }

        return $rtn;
    }

    /**
     * annul a model record
     * call NTC (No Try Catch) method
     *
     * @param Int $id
     * @return Bool
     */
    public function annul(int $id)
    {
        $rtn = false;

        try {
            $rtn = $this->NTCannul($id);
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
     * annul a model record
     * NTC (No Try Catch) method
     *
     * @param Int $id
     * @return Bool
     */
    public function NTCannul(int $id)
    {
        $rtn = false;

        if (!empty($this->Model)) {
            $rtn = $this->NTCadjust($id, [
                'delFlg' => $this->Model::STATUS_DELETED
            ]);
        }

        return $rtn;
    }
}
