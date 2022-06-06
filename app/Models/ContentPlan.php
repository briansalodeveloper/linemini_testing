<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Upload;
use App\Models\MainModel;
use App\Models\DisplayTargetContent;
use App\Models\DisplayTargetContentAO;
use App\Models\DisplayTargetContentUB;

class ContentPlan extends MainModel
{
    /**
     * @var $table
     */
    protected $table = 'M_ContentPlan';

    /**
     * @var $primaryKey
     */
    protected $primaryKey = 'contentPlanId';

    /**
     * @var $fillable
     */
    protected $fillable = [
        'contentType',
        'contentTypeNews',
        'contentLayoutId', // TODO: check future value
        'displayTargetFlg',
        'startDateTime',
        'endDateTime',
        'contentName', // TODO: check future value
        'openingLetter',
        'openingImg',
        'contents',
        'updateDate',
        'updateUser',
        'delFlg',
    ];

    /**
     * @var $casts
     */
    protected $casts = [
        'startDateTime',
        'endDateTime'
    ];

    /**
     * @var $appends
     */
    protected $appends = [
        'id',
        'contentTypeNewsStr',
        'displayTargetFlgStr',
        'startDateTime',
        'endDateTime',
        'status',
        'statusStr',
        'isThumbnailExist',
        'isEmpty',
        'isNotEmpty',
    ];

    /*======================================================================
     * CONSTANTS
     *======================================================================*/

    const CONTENTTYPE_NOTICE = 1;
    const CONTENTTYPE_RECIPE = 2;
    const CONTENTTYPE_PRODUCTINFO = 3;
    const CONTENTTYPE_COLUMN = 4;

    const CONTENTTYPENEWS_NOTTOP = 0;
    const CONTENTTYPENEWS_NOTIFICATIONAREA = 1;
    const CONTENTTYPENEWS_DEALSAREA = 2;

    const STATUS_COMINGSOON = 0;
    const STATUS_ENDOFPUBLICATION = 1;
    const STATUS_OPENNOW = 2;

    const DSPTARGET_UNCONDITIONAL = 0;
    const DSPTARGET_UNIONMEMBER = 1;
    const DSPTARGET_UB = 2;
    const DSPTARGET_AO = 3;

    const CSV_ACCEPTEDEXTENSION = ['csv'];
    const THUMBNAIL_ACCEPTEDEXTENSION = ['gif', 'jpg', 'jpeg', 'png'];

    /*======================================================================
     * ACCESSORS
     *======================================================================*/

    /**
     * contentTypeNewsStr
     *
     * @return String
     */
    public function getContentTypeNewsStrAttribute(): string
    {
        $rtn = '';

        if ($this->contentTypeNews == self::CONTENTTYPENEWS_NOTTOP) {
            $rtn = __('words.DontDisplayOnTopPage');
        } elseif ($this->contentTypeNews == self::CONTENTTYPENEWS_NOTIFICATIONAREA) {
            $rtn = __('words.DisplayOnNotification');
        } elseif ($this->contentTypeNews == self::CONTENTTYPENEWS_DEALSAREA) {
            $rtn = __('words.DisplayOnDeals');
        }

        return $rtn;
    }

    /**
     * displayTargetFlgStr
     *
     * @return String
     */
    public function getDisplayTargetFlgStrAttribute(): string
    {
        $rtn = '';

        if ($this->displayTargetFlg == self::DSPTARGET_UNCONDITIONAL) {
            $rtn = __('words.Everyone');
        } elseif ($this->displayTargetFlg == self::DSPTARGET_UNIONMEMBER) {
            $rtn = __('words.UnionMemberCode');
        } elseif ($this->displayTargetFlg == self::DSPTARGET_UB) {
            $rtn = __('words.UtilizationBusiness');
        } elseif ($this->displayTargetFlg == self::DSPTARGET_AO) {
            $rtn = __('words.AffiliateOffice');
        }

        return $rtn;
    }

    /**
     * startDateTime
     *
     * @return String
     */
    public function getStartDateTimeAttribute(): string
    {
        $rtn = '';

        if (!empty($this->attributes['startDateTime'])) {
            $rtn = new Carbon($this->attributes['startDateTime']);
            $rtn = $rtn->format('m/d/Y g:i A');
        }

        return $rtn;
    }

    /**
     * endDateTime
     *
     * @return String
     */
    public function getEndDateTimeAttribute(): string
    {
        $rtn = '';

        if (!empty($this->attributes['endDateTime'])) {
            $rtn = new Carbon($this->attributes['endDateTime']);
            $rtn = $rtn->format('m/d/Y g:i A');
        }

        return $rtn;
    }

    /**
     * status
     *
     * @return String $rtn
     */
    public function getStatusAttribute(): string
    {
        $rtn = '';
        $currentDate = Carbon::now();
        $startDateTime = new Carbon($this->startDateTime);
        $endDateTime = new Carbon($this->endDateTime);

        if ($currentDate->gt($endDateTime)) {
            $rtn = self::STATUS_ENDOFPUBLICATION;
        } elseif ($currentDate->lt($startDateTime)) {
            $rtn = self::STATUS_COMINGSOON;
        } elseif (!empty($startDateTime) && !empty($endDateTime)) {
            $rtn = self::STATUS_OPENNOW;
        }

        return $rtn;
    }

    /**
     * statusStr
     *
     * @return String $rtn
     */
    public function getStatusStrAttribute(): string
    {
        $rtn = __('words.New');

        if ($this->status == self::STATUS_COMINGSOON) {
            $rtn = __('words.ComingSoon');
        } elseif ($this->status == self::STATUS_ENDOFPUBLICATION) {
            $rtn = __('words.EndOfPublication');
        } elseif ($this->status == self::STATUS_OPENNOW) {
            $rtn = __('words.OpenNow');
        }

        return $rtn;
    }

    /**
     * isThumbnailExist
     *
     * @return String $rtn
     */
    public function getIsThumbnailExistAttribute(): string
    {
        $rtn = false;

        if (!empty($this->openingImg)) {
            $rtn = Upload::exist($this->openingImg);
        }

        return $rtn;
    }

    /*======================================================================
     * MUTATORS
     *======================================================================*/

    public function setStartDateTimeAttribute($value)
    {
        $rtn = '';

        if (!empty($value)) {
            $rtn = Carbon::parse($value)->format('Y-m-d H:i:s');
        }

        $this->attributes['startDateTime'] = $rtn;
    }

    public function setEndDateTimeAttribute($value)
    {
        $rtn = '';

        if (!empty($value)) {
            $rtn = Carbon::parse($value)->format('Y-m-d H:i:s');
        }

        $this->attributes['endDateTime'] = $rtn;
    }

    /*======================================================================
     * RELATIONSHIPS
     *======================================================================*/

    public function displayTarget()
    {
        return $this->hasOne(DisplayTargetContent::class, 'contentPlanId', 'contentPlanId')->where('delFlg', DisplayTargetContent::STATUS_NOTDELETED);
    }

    public function displayTargetAO()
    {
        return $this->hasOne(DisplayTargetContentAO::class, 'contentPlanId', 'contentPlanId')->where('delFlg', DisplayTargetContentAO::STATUS_NOTDELETED);
    }

    public function displayTargetUB()
    {
        return $this->hasOne(DisplayTargetContentUB::class, 'contentPlanId', 'contentPlanId')->where('delFlg', DisplayTargetContentUB::STATUS_NOTDELETED);
    }
}
