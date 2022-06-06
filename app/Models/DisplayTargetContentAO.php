<?php

namespace App\Models;

use App\Models\MainModel;

class DisplayTargetContentAO extends MainModel
{
    /**
     * @var $table
     */
    protected $table = 'T_DisplayTargetContentAO';
    
    /**
     * @var $primaryKey
     */
    protected $primaryKey = 'displayTargetContentAOId';
    
    /**
     * @var $fillable
     */
    protected $fillable = [
        'contentPlanId',
        'affiliationOfficeId',
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

    /** ORDER IS OBSERVED */
    const AFFILIATION_OFFICE_LIST = [
        '1' => '宅配',
        '2' => '店舗',
        '3' => '夕食宅配',
        '4' => '共済',
        '5' => '保険',
        '6' => '福祉',
        '7' => 'その他１',
        '8' => 'その他２',
        '9' => 'その他３'
    ];
}
