<?php

namespace App\Models;

use App\Models\MainModel;
use App\Models\UnionLine;

class UnionMember extends MainModel
{
    /**
     * @var $table
     */
    protected $table = 'M_UnionMemberId';

    /**
     * @var $primaryKey
     */
    protected $primaryKey = 'unionMemberId';

    /**
     * @var $fillable
     */
    protected $fillable = [
        'unionMemberCode',
        'affiliationOffice',
        'joinDate',
        'withdrawalApplicationDate',
        'withdrawalDate',
        'pointBalance',
        'utilizationBusiness1',
        'utilizationBusiness2',
        'utilizationBusiness3',
        'utilizationBusiness4',
        'utilizationBusiness5',
        'utilizationBusiness6',
        'utilizationBusiness7',
        'utilizationBusiness8',
        'utilizationBusiness9',
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

    public function unionLine()
    {
        return $this->hasOne(UnionLine::class, 'unionMemberCode', 'unionMemberCode')->where('delFlg', UnionLine::STATUS_NOTDELETED);
    }

    /*======================================================================
     * SCOPES
     *======================================================================*/
}
