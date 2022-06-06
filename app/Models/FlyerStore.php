<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\UnionMember;

class FlyerStore extends Model
{
    /**
     * @var $table
     */
    protected $table = 'T_FlyerStoreSelect';

    /**
     * @var $primaryKey
     */
    protected $primaryKey = 'unionMemberCode';

    /**
     * @var $fillable
     */
    protected $fillable = [
        'cardNumber',
        'storeId',
        'viewFlg',
    ];

    /*======================================================================
     * CONSTANTS
     *======================================================================*/

    const VIEWFLG_ISNOTVIEW = 0;
    const VIEWFLG_ISVIEW = 1;

    /*======================================================================
     * SCOPES
     *======================================================================*/

    /**
     * isView()
     */
    public function scopeIsView($query)
    {
        $query->where('viewFlg', self::VIEWFLG_ISVIEW);

        return $query;
    }
}
