<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MainModel extends Model
{
    /**
     * @var $appends
     */
    protected $appends = [
        'id',
        'isEmpty',
        'isNotEmpty',
    ];

    /*======================================================================
     * CONSTANTS
     *======================================================================*/

    const CREATED_AT = null;
    const UPDATED_AT = 'updateDate';

    const STATUS_NOTDELETED = 0;
    const STATUS_DELETED = 1;

    /*======================================================================
     * CUSTOM METHODS
     *======================================================================*/
    
    /**
     * get valid attribute if exist, if not then return default value
     *
     * @return [ModelProperty] $rtn
     */
    public function getAttr(string $attribute, $default = '')
    {
        $rtn = $default;

        if ($this->isNotEmpty) {
            if (isset($this[$attribute])) {
                $rtn = $this->{$attribute};
            }
        }

        return $rtn;
    }

    /**
     * get valid relationship attribute if exist, if not then return default value
     *
     * @return [ModelProperty] $rtn
     */
    public function getRelAttr(string $relationshipMethodString, string $attribute, $default = '')
    {
        $rtn = $default;

        if (!empty($this->{$relationshipMethodString})) {
            if (isset($this->{$relationshipMethodString}[$attribute])) {
                $rtn = $this->{$relationshipMethodString}->{$attribute};
            }
        }

        return $rtn;
    }

    /**
     * carbon format a property date
     *
     * @param String $property
     * @param String $format
     * @return String $rtn
     */
    public function formatDate(string $property, string $format = 'Y年m月d日'): string
    {
        $rtn = '';

        if (!empty($this->{$property})) {
            $dt = Carbon::parse($this->{$property});
            
            if (!empty($dt)) {
                $rtn = $dt->format($format);
            }
        }

        return $rtn;
    }

    /*======================================================================
     * CUSTOM STATIC METHODS
     *======================================================================*/

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($data) {
            if (empty($data->delFlg)) {
                $data->delFlg = static::STATUS_NOTDELETED;
            }

            if (empty($data->updateUser)) {
                $data->updateUser = _trim(auth()->user()->name, 8, '...');
            }

            return $data;
        });
    }

    /**
     * empty table column values
     */
    public static function empty()
    {
        return new static();
    }

    /*======================================================================
     * ACCESSORS
     *======================================================================*/

    /**
     * id
     *
     * @return Int
     */
    public function getIdAttribute(): int
    {
        $rtn = 0;

        if (isset($this[$this->primaryKey])) {
            $rtn = $this[$this->primaryKey];
        }

        return $rtn;
    }

    /**
     * isEmpty
     *
     * @return Bool
     */
    public function getIsEmptyAttribute()
    {
        return empty($this->id);
    }

    /**
     * isNotEmpty
     *
     * @return Bool
     */
    public function getIsNotEmptyAttribute()
    {
        return !$this->isEmpty;
    }

    /*======================================================================
     * SCOPES
     *======================================================================*/

    public function scopeWhereDeleted($query)
    {
        $query->where('delFlg', self::STATUS_DELETED);
        return $query;
    }

    public function scopeWhereNotDeleted($query)
    {
        $query->where('delFlg', self::STATUS_NOTDELETED);
        return $query;
    }

    public function scopeSortAsc($query)
    {
        $query->orderBy('updateDate', 'asc');
        return $query;
    }

    public function scopeSortDesc($query)
    {
        $query->orderBy('updateDate', 'desc');
        return $query;
    }
}
