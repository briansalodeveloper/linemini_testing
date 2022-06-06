<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ContentPlan;

class ContentPlanRequest extends FormRequest
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
        $isRegister = empty($this->contentPlanId);
        
        $rtn = [
            'openingLetter' => 'required',
            'targetCode' => 'integer',
            'contentTypeNews' => 'required',
            'displayTargetFlg' => 'required',
            'openingImg' => 'required',
        ];

        if (empty(trim($this->startDateTime, ' '))) {
            $rtn['selectPublicationDateTime'] = 'required';
        }

        if (!is_null($this->selectPublicationDateTime) || $this->selectPublicationDateTime == 1) {
            $rtn['startDateTime'] = 'required|date';
            $rtn['endDateTime'] = 'required|date|after:startDate';
        }

        if ($this->displayTargetFlg == ContentPlan::DSPTARGET_UNIONMEMBER && ($isRegister || (!$isRegister && empty($this->displayTarget)))) {
            $rtn['unionMemberCsv'] = 'required';
        }

        if ($this->displayTargetFlg == ContentPlan::DSPTARGET_UB) {
            $rtn['utilizationBusiness'] = 'required';
        }

        if ($this->displayTargetFlg == ContentPlan::DSPTARGET_AO) {
            $rtn['affiliationOffice'] = 'required';
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
            'startDateTime' => "$this->startDate $this->startTime",
            'endDateTime' => "$this->endDate $this->endTime",
        ]);

        request()->merge($this->all());
    }
}
