<?php

namespace App\Models;

use App\Models\MainModel;

class DisplayTargetContent extends MainModel
{
    /**
     * @var $table
     */
    protected $table = 'T_DisplayTargetContent';
    
    /**
     * @var $primaryKey
     */
    protected $primaryKey = 'displayTargetContentId';
    
    /**
     * @var $fillable
     */
    protected $fillable = [
        'contentPlanId',
        'kumicd',
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
}
