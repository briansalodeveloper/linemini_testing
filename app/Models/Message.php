<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Upload;
use App\Models\MainModel;

class Message extends MainModel
{
    /**
     * @var $table
     */
    protected $table = 'M_Message';

    /**
     * @var $primaryKey
     */
    protected $primaryKey = 'messageId';

    /**
     * @var $fillable
     */
    protected $fillable = [
        'sendTargetFlg',
        'sendFlg',
        'sendDateTime',
        'messageName',
        'thumbnail',
        'thumbnailPreview',
        'contents',
        'kumicd',
        'ubId',
        'aoId',
        'storeId',
        'updateDate',
        'updateUser',
        'delFlg',
    ];

    /**
     * @var $casts
     */
    protected $casts = [
        'startDateTime',
    ];

    /**
     * @var $appends
     */
    protected $appends = [
        'id',
        'sendTargetFlgStr',
        'status',
        'statusStr',
        'isStatusSend',
        'isStatusNotSend',
        'isImageExist',
        'isEmpty',
        'isNotEmpty',
        'isTargetAll',
        'isTargetUm',
        'isTargetUb',
        'isTargetAo',
        'isTargetST',
    ];

    /*======================================================================
     * CONSTANTS
     *======================================================================*/

    const STATUS_SCHEDULETOBESENT = 0;
    const STATUS_SENT = 1;

    const SENDSATTUS_NO = 0;
    const SENDSATTUS_YES = 1;

    const SENDTARGET_UNCONDITIONAL = 0;
    const SENDTARGET_UNIONMEMBER = 1;
    const SENDTARGET_UB = 2;
    const SENDTARGET_AO = 3;
    const SENDTARGET_STORE = 4;

    const CSV_ACCEPTEDEXTENSION = ['csv'];
    const THUMBNAIL_ACCEPTEDEXTENSION = ['gif', 'jpg', 'jpeg', 'png'];
    
    const UTILIZATION_BUSINESS_LIST = [
        '1' => '宅配',
        '2' => '店舗',
        '3' => '夕食宅配',
        '4' => '共済',
        '5' => '保険',
        '6' => '福祉',
        '7' => 'その他１',
        '8' => 'その他２',
        '9' => 'その他３',
    ];
    
    const AFFILIATION_OFFICE_LIST = [
        '1' => '宅配',
        '2' => '店舗',
        '3' => '夕食宅配',
        '4' => '共済',
        '5' => '保険',
        '6' => '福祉',
        '7' => 'その他１',
        '8' => 'その他２',
        '9' => 'その他３',
    ];

    const STORE_LIST = [
        '1' => '新下関店',
        '2' => '宇部店',
        '3' => '小郡店',
        '4' => 'いずみ店',
        '5' => 'どうもん店',
        '6' => 'とくやま店',
        '7' => '島田店',
    ];

    /*======================================================================
     * ACCESSORS
     *======================================================================*/

    /**
     * sendTargetFlgStr
     *
     * @return String
     */
    public function getSendTargetFlgStrAttribute(): string
    {
        $rtn = '';

        if ($this->sendTargetFlg == self::SENDTARGET_UNCONDITIONAL) {
            $rtn = __('words.Everyone');
        } elseif ($this->sendTargetFlg == self::SENDTARGET_UNIONMEMBER) {
            $rtn = __('words.UnionMemberCode');
        } elseif ($this->sendTargetFlg == self::SENDTARGET_UB) {
            $rtn = __('words.UtilizationBusiness');
        } elseif ($this->sendTargetFlg == self::SENDTARGET_AO) {
            $rtn = __('words.AffiliateOffice');
        } elseif ($this->sendTargetFlg == self::SENDTARGET_STORE) {
            $rtn = __('words.SelectAtTheRegisteredStore');
        }

        return $rtn;
    }

    /**
     * sendDateTime
     *
     * @return String
     */
    public function getSendDateTimeAttribute(): string
    {
        $rtn = '';

        if (!empty($this->attributes['sendDateTime'])) {
            $rtn = new Carbon($this->attributes['sendDateTime']);
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

        if (!empty($this->sendDateTime)) {
            $currentDate = Carbon::now();
            $sendDateTime = Carbon::parse($this->sendDateTime);
            if ($currentDate->gt($sendDateTime) && $this->sendFlg == self::SENDSATTUS_YES) {
                $rtn = self::STATUS_SENT;
            } else {
                $rtn = self::STATUS_SCHEDULETOBESENT;
            }
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

        if ($this->status == self::STATUS_SCHEDULETOBESENT) {
            $rtn = __('words.ScheduleToBeSent');
        } elseif ($this->status == self::STATUS_SENT) {
            $rtn = __('words.Sent');
        }

        return $rtn;
    }

    /**
     * isStatusSend
     *
     * @return Bool $rtn
     */
    public function getIsStatusSendAttribute(): string
    {
        return $this->status == self::STATUS_SENT;
    }

    /**
     * isStatusNotSend
     *
     * @return Bool $rtn
     */
    public function getIsStatusNotSendAttribute(): string
    {
        return $this->status == self::STATUS_SCHEDULETOBESENT;
    }

    /**
     * isThumbnailExist
     *
     * @return String $rtn
     */
    public function getIsThumbnailExistAttribute(): string
    {
        $rtn = false;

        if (!empty($this->thumbnail)) {
            $rtn = Upload::exist($this->thumbnail);
        }

        return $rtn;
    }

    /**
     * isTargetAll
     *
     * @return Bool $rtn
     */
    public function getIsTargetAllAttribute(): string
    {
        return $this->sendTargetFlg == self::SENDTARGET_UNCONDITIONAL;
    }

    /**
     * isTargetUm
     *
     * @return Bool $rtn
     */
    public function getIsTargetUmAttribute(): string
    {
        return $this->sendTargetFlg == self::SENDTARGET_UNIONMEMBER;
    }

    /**
     * isTargetUb
     *
     * @return Bool $rtn
     */
    public function getIsTargetUbAttribute(): string
    {
        return $this->sendTargetFlg == self::SENDTARGET_UB;
    }

    /**
     * isTargetAo
     *
     * @return Bool $rtn
     */
    public function getIsTargetAoAttribute(): string
    {
        return $this->sendTargetFlg == self::SENDTARGET_AO;
    }

    /**
     * isTargetSt
     *
     * @return Bool $rtn
     */
    public function getIsTargetStAttribute(): string
    {
        return $this->sendTargetFlg == self::SENDTARGET_STORE;
    }

    /*======================================================================
     * MUTATORS
     *======================================================================*/

    public function setSendDateTimeAttribute($value)
    {
        $rtn = '';

        if (!empty($value)) {
            $rtn = Carbon::parse($value)->format('Y-m-d H:i:s');
        }

        $this->attributes['sendDateTime'] = $rtn;
    }

    /*======================================================================
     * SCOPES
     *======================================================================*/

    /**
     * whereNotSend()
     */
    public function scopeWhereNotSend($query)
    {
        $query->where('sendFlg', self::SENDSATTUS_NO);
        return $query;
    }
}
