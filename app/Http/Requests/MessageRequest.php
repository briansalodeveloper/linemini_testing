<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Message;

class MessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $isRegister = empty($this->messageId);

        $rtn = [
            'messageName' => 'required',
            'sendTargetFlg' => 'required',
            'thumbnail' => 'required',
        ];

        if (empty(trim($this->sendDateTime, ' '))) {
            $rtn['selectTransmissionTiming'] = 'required';
        }

        if (!is_null($this->selectTransmissionTiming) || $this->selectTransmissionTiming == 1) {
            $rtn['sendDateTime'] = 'required|date';
        }

        if ($this->sendTargetFlg == Message::SENDTARGET_UNIONMEMBER && ($isRegister || (!$isRegister && empty($this->kumicd)))) {
            $rtn['unionMemberCsv'] = 'required';
        }

        if ($this->sendTargetFlg == Message::SENDTARGET_UB) {
            $rtn['ubId'] = 'required';
        }

        if ($this->sendTargetFlg == Message::SENDTARGET_AO) {
            $rtn['aoId'] = 'required';
        }

        if ($this->sendTargetFlg == Message::SENDTARGET_STORE) {
            $rtn['storeId'] = 'required';
        }

        return $rtn;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'sendDateTime' => "$this->sendDate $this->sendTime",
        ]);

        request()->merge($this->all());
    }
}
