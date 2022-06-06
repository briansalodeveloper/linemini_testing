<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\MainModel;
use App\Models\UnionMember;
use App\Models\FlyerStore;

class UnionLine extends MainModel
{
    /**
     * @var $table
     */
    protected $table = 'M_UnionLineId';

    /**
     * @var $primaryKey
     */
    protected $primaryKey = 'unionLineId';

    /**
     * @var $fillable
     */
    protected $fillable = [
        'LineTokenId',
        'cardNumber',
        'pinCode',
        'unionMemberCode',
        'cardAlignment',
        'stopFlg',
        'firstFlg',
        'dailyCheck',
        'incidental',
        'bikou1',
        'bikou2',
        'bikou3',
        'bikou4',
        'bikou5',
        'bikou6',
        'bikou7',
        'bikou8',
        'bikou9',
        'bikou10',
        'updateDate',
        'updateUser',
        'delFlg',
    ];

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

    /*======================================================================
     * ACCESSORS
     *======================================================================*/

    /*======================================================================
     * MUTATORS
     *======================================================================*/

    /*======================================================================
     * RELATIONSHIPS
     *======================================================================*/

    /**
     * unionMember()
     */
    public function unionMember()
    {
        return $this->hasOne(UnionMember::class, 'unionMemberCode', 'unionMemberCode')->where('delFlg', UnionMember::STATUS_NOTDELETED);
    }

    /**
     * flyerStore()
     */
    public function flyerStore()
    {
        return $this->hasOne(FlyerStore::class, 'unionMemberCode', 'unionMemberCode')->where('cardNumber', $this->cardNumber);
    }

    /*======================================================================
     * SCOPES
     *======================================================================*/
}
