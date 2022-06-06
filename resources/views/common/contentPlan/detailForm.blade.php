@php
    $route = '';
    $routeList = '';

    if ($contentType == Globals::mContentPlan()::CONTENTTYPE_NOTICE) {
        $routeList = route('notice.index');
        $route = $data->isEmpty ? route('notice.store') : route('notice.update', $data->id);
    } elseif ($contentType == Globals::mContentPlan()::CONTENTTYPE_RECIPE) {
        $routeList = route('recipe.index');
        $route = $data->isEmpty ? route('recipe.store') : route('recipe.update', $data->id);
    } elseif ($contentType == Globals::mContentPlan()::CONTENTTYPE_PRODUCTINFO) {
        $routeList = route('productInformation.index');
        $route = $data->isEmpty ? route('productInformation.store') : route('productInformation.update', $data->id);
    } elseif ($contentType == Globals::mContentPlan()::CONTENTTYPE_COLUMN) {
        $routeList = route('column.index');
        $route = $data->isEmpty ? route('column.store') : route('column.update', $data->id);
    }

    $csvLabel = '';
    $csvUrl = old('unionMemberCsv');
    $logo = '';
    $thumbnailLabel = '';
    $thumbnailUrl = old('openingImg', $data->getAttr('openingImg'));

    if (old('unionMemberCsv', null) != null) {
        $logo = '<i class="fa fa-file-csv"></i> ';
        $csvLabel = '<a href="' . $csvUrl . '" download>' . $logo . \Globals::hUpload()::getBaseName($csvUrl) . '</a>';
    }

    if (old('openingImg', null) == null) {
        if ($data->isNotEmpty) {
            if ($data->IsThumbnailExist) {
                $logo = '<i class="fa fa-image"></i> ';
                $thumbnailLabel = '<a href="' . $thumbnailUrl . '" data-toggle="lightbox" title="' . __('words.Preview') . '">' . $logo . \Globals::hUpload()::getBaseName($thumbnailUrl) . '</a>';
            } else {
                $logo = '<i class="fa fa-circle-xmark text-red" title="' . __('messages.custom.imageNotExist') . '"></i> ';
                $thumbnailLabel = $logo . \Globals::hUpload()::getBaseName($thumbnailUrl);
            }
        }
    } else {
        $logo = '<i class="fa fa-image"></i> ';
        $thumbnailLabel = '<a href="' . $thumbnailUrl . '" data-toggle="lightbox" title="' . __('words.Preview') . '">' . $logo . \Globals::hUpload()::getBaseName($thumbnailUrl) . '</a>';
    }
@endphp
<form method="POST" enctype="multipart/form-data" id="form" action="{{ $route }}">
    @csrf

    @if($data->isNotEmpty)
        <input type="text" hidden name="contentPlanId" value="{{ $data->id }}">

        @if(!empty($data->displayTarget))
            <input type="text" hidden name="displayTarget" value="{{ $data->displayTarget->id }}">
        @endif
    @endif

    <input type="hidden" id="uploadType" name="uploadType" value="">
    <div class="form-group mt-3">
        <div class="row">
            @if($data->isNotEmpty)
                <div class="col-12">
                    <label>{{ __('words.Id') }}: {{ $data->getAttr('id') }}</label>
                </div>
            @endif
            <div class="col-12">
                <label class="mb-0">{{ __('words.Status') }}: {{ $data->getAttr('statusStr', __('words.New')) }}</label>
                {{-- TODO: check what is the used of this --}}
                {{-- @if($data->isNotEmpty)
                    <button class="btn btn-02 ml-2 def-size">{{ __('words.Edit') }}</button>
                @endif --}}
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="openingLetter">{{ __('words.Title') }}</label>
        <div class="row">
            <div class="col-md-5">
                <input type="text" class="form-control @if($errors->has('openingLetter')) is-invalid @endif" name="openingLetter" id="openingLetter" placeholder="{{ __('words.Title') }}" value="{{ old('openingLetter', $data->getAttr('openingLetter')) }}" data-original-value="{{ $data->getAttr('openingLetter') }}">
                @include('common.validationError', ['key' => 'openingLetter'])
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>{{ __('words.SelectPublicationDateTime') }}</label>
        <div class="col-12{{ $errors->has('selectPublicationDateTime') ? ' is-invalid' : '' }}">
            <div class="icheck-primary">
                <input type="radio" id="publishType1"  value="0" name="selectPublicationDateTime" {!! old('selectPublicationDateTime') == '0' ? 'checked': ''!!}>
                <label for="publishType1">{{ $data->isEmpty ? __('words.PublishSoon') : __('words.UpdateSoon') }}</label>
            </div>
            <div class="icheck-primary">
                <input type="radio" id="publishType2"  value="1" name="selectPublicationDateTime"  {!! old('selectPublicationDateTime') == '1' ? 'checked': ''!!}>
                <label for="publishType2">{{ $data->isEmpty ? __('words.BookAndPublish') : __('words.BookAndRenew') }}</label>
            </div>
        </div>
        @include('common.validationError', ['key' => 'selectPublicationDateTime'])
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                @php
                    $startDate = null;
                    $startTime = null;
                    if($data->isNotEmpty) {
                        $startDate = preg_split('/(0?[1-9]|1[0-2]):([0-5]\d)\s?((?:[Aa]|[Pp])\.?[Mm]\.?)/', $data->startDateTime)[0];
                        $startTime = preg_split('/\d{2}\/\d{2}\/\d{4}\s/', $data->startDateTime)[1];
                    }
                @endphp
                <div class="form-group col-2">
                    <label>{{ __('words.ReleaseDate') }}</label>
                    <div class="input-group date" id="startDate" data-target-input="nearest">
                        <input type="text" id="startDateInput" class="form-control datetimepicker-input @if($errors->has('startDateTime')) is-invalid @endif" name="startDate" placeholder="{{ __('words.DateFormat') }}" value="{{ old( 'startDate', $startDate ) }}" data-original-value="{{ $startDate }}" data-target="#startDate">
                        <div class="input-group-append" data-target="#startDate" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                        @if($errors->has('startDateTime'))
                            <span id="startDateTime-error" class="error invalid-feedback">{{ $errors->first('startDateTime') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-2">
                    <label>{{ __('words.PublicationTime') }}</label>
                    <div class="input-group date" id="startTime" data-target-input="nearest">
                        <input type="text" id="startTimeInput" class="form-control datetimepicker-input @if($errors->has('startDateTime')) is-invalid @endif" name="startTime" value="{{ old( 'startTime', $startTime ) }}" data-original-value="{{ $startTime }}" data-target="#startTime">
                        <div class="input-group-append" data-target="#startTime" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-clock"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row">
                @php
                    $endDate = null;
                    $endTime = null;
                    if($data->isNotEmpty) {
                        $endDate = preg_split('/(0?[1-9]|1[0-2]):([0-5]\d)\s?((?:[Aa]|[Pp])\.?[Mm]\.?)/', $data->endDateTime)[0];
                        $endTime = preg_split('/\d{2}\/\d{2}\/\d{4}\s/', $data->endDateTime)[1];
                    }
                @endphp
                <div class="form-group col-2">
                    <label>{{ __('words.ReleaseEndDate') }}</label>
                    <div class="input-group date" id="endDate" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input @if($errors->has('endDateTime')) is-invalid @endif" name="endDate" placeholder="{{ __('words.DateFormat') }}"  value="{{ old( 'endDate', $endDate ) }}" data-original-value="{{ $endDate }}" data-target="#endDate">
                        <div class="input-group-append" data-target="#endDate" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                        @if($errors->has('endDateTime'))
                            <span id="endDateTime-error" class="error invalid-feedback">{{ $errors->first('endDateTime') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-2">
                    <label>{{ __('words.PublicationEndTime') }}</label>
                    <div class="input-group date" id="endTime" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input @if($errors->has('endDateTime')) is-invalid  @endif" name="endTime" value="{{ old( 'endTime', $endTime ) }}" data-original-value="{{ $endTime }}" data-target="#endTime">
                        <div class="input-group-append" data-target="#endTime" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-clock"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>{{ __('words.DisplayToTopPage') }}</label>
        <div class="icheck-primary">
            <input type="radio" id="contentTypeNews1" value="{{ \Globals::mContentPlan()::CONTENTTYPENEWS_NOTTOP }}"
                name="contentTypeNews" {!! old('contentTypeNews', $data->getAttr('contentTypeNews', '-1')) == \Globals::mContentPlan()::CONTENTTYPENEWS_NOTTOP ? 'checked': '' !!}
                data-original-value="{{ $data->getAttr('contentTypeNews') }}">
            <label for="contentTypeNews1">{{ __('words.DontDisplayOnTopPage') }}</label>
        </div>
        <div class="icheck-primary">
            <input type="radio" id="contentTypeNews2" value="{{ \Globals::mContentPlan()::CONTENTTYPENEWS_NOTIFICATIONAREA }}"
                name="contentTypeNews" {!! old('contentTypeNews', $data->getAttr('contentTypeNews', '-1')) == \Globals::mContentPlan()::CONTENTTYPENEWS_NOTIFICATIONAREA ? 'checked': '' !!}
                data-original-value="{{ $data->getAttr('contentTypeNews') }}">
            <label for="contentTypeNews2">{{ __('words.DisplayOnNotification') }}</label>
        </div>
        <div class="icheck-primary{{ $errors->has('contentTypeNews') ? ' is-invalid' : '' }}">
            <input type="radio" id="contentTypeNews3" value="{{ \Globals::mContentPlan()::CONTENTTYPENEWS_DEALSAREA }}"
                name="contentTypeNews" {!!  old('contentTypeNews', $data->getAttr('contentTypeNews', '-1')) == \Globals::mContentPlan()::CONTENTTYPENEWS_DEALSAREA ? 'checked': '' !!}
                data-original-value="{{ $data->getAttr('contentTypeNews') }}">
            <label for="contentTypeNews3">{{ __('words.DisplayOnDeals') }}</label>
        </div>
        @include('common.validationError', ['key' => 'contentTypeNews'])
    </div>
    <div class="form-group">
        <label>{{ __('words.UnionMemberToDisplay') }}</label>
        <div class="icheck-primary">
            <input type="radio" id="displayTargetFlg1" value="{{ \Globals::mContentPlan()::DSPTARGET_UNCONDITIONAL }}"
                name="displayTargetFlg" {!! old('displayTargetFlg', $data->getAttr('displayTargetFlg', '-1')) == \Globals::mContentPlan()::DSPTARGET_UNCONDITIONAL ? 'checked': '' !!}
                data-original-value="{{ $data->getAttr('displayTargetFlg') }}">
            <label for="displayTargetFlg1">{{ __('words.Unconditional') }}</label>
        </div>
        <div class="icheck-primary">
            <input type="radio" id="displayTargetFlg2" value="{{ \Globals::mContentPlan()::DSPTARGET_UNIONMEMBER }}"
                name="displayTargetFlg" {!! old('displayTargetFlg', $data->getAttr('displayTargetFlg', '-1')) == \Globals::mContentPlan()::DSPTARGET_UNIONMEMBER ? 'checked': '' !!}
                data-original-value="{{ $data->getAttr('displayTargetFlg') }}">
            <label for="displayTargetFlg2">{{ __('words.UnionMemberDesignation') }}</label>
        </div>
        <div class="icheck-primary">
            <input type="radio" id="displayTargetFlg3" value="{{ \Globals::mContentPlan()::DSPTARGET_UB }}"
                name="displayTargetFlg" {!! old('displayTargetFlg', $data->getAttr('displayTargetFlg', '-1')) == \Globals::mContentPlan()::DSPTARGET_UB ? 'checked': '' !!}
                data-original-value="{{ $data->getAttr('displayTargetFlg') }}">
            <label for="displayTargetFlg3">{{ __('words.UserBusinessDesignation') }}</label>
        </div>
        <div class="icheck-primary{{ $errors->has('displayTargetFlg') ? ' is-invalid' : '' }}">
            <input type="radio" id="displayTargetFlg4" value="{{ \Globals::mContentPlan()::DSPTARGET_AO }}"
                name="displayTargetFlg" {!! old('displayTargetFlg', $data->getAttr('displayTargetFlg', '-1')) == \Globals::mContentPlan()::DSPTARGET_AO ? 'checked': '' !!}
                data-original-value="{{ $data->getAttr('displayTargetFlg') }}">
            <label for="displayTargetFlg4">{{ __('words.OfficeDesignation') }}</label>
        </div>
        @include('common.validationError', ['key' => 'displayTargetFlg'])
    </div>
    <div class="col-md-12 pl-3">
        <div class="form-group">
            <p><b>{{ __('words.CSVFileUploadInstruction') }}</b></p>
            @include('common.input.fileCustom', [
                'id' => 'unionMemberCsvTrigger',
                'name' => 'csv',
                'classContainer' => 'file-csv' . ($errors->has('unionMemberCsv') ? ' is-invalid' : ''),
                'accept' => \Globals::implode(\Globals::mContentPlan()::CSV_ACCEPTEDEXTENSION, ',', '.'),
                'label' => $csvLabel,
                'hiddenName' => 'unionMemberCsv',
                'hiddenValue' => $csvUrl,
                'disabled' => old('displayTargetFlg', $data->getAttr('displayTargetFlg')) != 1,
                'originalValue' => '',
            ])
            @if($errors->has('unionMemberCsv'))
                <span id="unionMemberCsv-error" class="error invalid-feedback">{{ $errors->first('unionMemberCsv') }}</span>
            @endif
            <span id="unionMemberCsv-error" class="error invalid-feedback"></span>
        </div>
        <div class="form-group ub-ao-box">
            <label>{{ __('messages.custom.specifyAoOrUb') }}</label>
            <select class="ub-list form-control col-md-3{{ $errors->has('utilizationBusiness') ? ' is-invalid' : '' }}" name="utilizationBusiness">
                <option value="" disabled{!! empty(old('utilizationBusiness', $data->displayTargetUB)) ? ' selected' : '' !!} >{{ __('words.BusinessSelection') }}</option>
                @foreach( $ubList as $key => $val )
                    <option value="{{ $key }}"{!! old('utilizationBusiness', $data->getRelAttr('displayTargetUB', 'utilizationBusinessId')) == $key ?  ' selected': '' !!}>{{ $val }}</option>
                @endforeach
            </select>
            @include('common.validationError', ['key' => 'utilizationBusiness'])
            <select class="ao-list form-control col-md-3{{ $errors->has('affiliationOffice') ? ' is-invalid' : '' }}" name="affiliationOffice" disabled>
                <option value="" disabled {!! empty(old('affiliationOffice', $data->displayTargetAO)) ? ' selected' : '' !!} >{{ __('words.AffiliateOffice') }}</option>
                @foreach( $aoList as $key => $val )
                    <option value="{{ $key }}" {!! old('affiliationOffice', $data->getRelAttr('displayTargetAO', 'affiliationOfficeId')) == $key ?  'selected': '' !!}>{{ $val }}</option>
                @endforeach
            </select>
            @include('common.validationError', ['key' => 'affiliationOffice'])
        </div>
    </div>
    <div class="form-group">
        <p><b>{{ __('words.TopImg') }}</b><span class="text-xs ml-4">ファイルサイズは2M以下、形式はJPEG or PNG のみとなります。</span></p>
        @include('common.input.fileCustom', [
            'id' => 'openingImgTrigger',
            'name' => 'thumbnail',
            'classContainer' => 'file-thumbnail',
            'accept' => \Globals::implode(\Globals::mContentPlan()::THUMBNAIL_ACCEPTEDEXTENSION, ',', '.'),
            'label' => $thumbnailLabel,
            'hiddenName' => 'openingImg',
            'hiddenValue' => $thumbnailUrl,
            'originalValue' => $data->getAttr('openingImg')
        ])
        <span id="openingImg-error" class="form-custom-error">{{ $errors->first('openingImg') }}</span>
    </div>

    <div class="form-group">
        <p><b>{{ __('words.BodyPostedContent') }}</b></p>
        <textarea id="contentsOriginalValue" hidden>{{ $data->getAttr('contents') }}</textarea>
        <textarea id="contents" name="contents" data-original-value="#contentsOriginalValue">{!! old('contents', $data->getAttr('contents')) !!}</textarea>
    </div>
    <div class="row justify-content-center mb-3">
        <button type="submit" id="preview" class="btn btn-02 col-md-3 col-sm-3 w-100">{{ __('words.Preview') }}</button>
    </div>
    <div class="row justify-content-center mb-3">
        <button id="submit" type="submit" class="btn btn-warning col-md-3 col-sm-3 w-100 text-white">{{ __('words.Post') }}</button>
    </div>
    <div class="row justify-content-end mt-2 mb-3">
        <a href="{{ $routeList }}" class="btn btn-02">{{ __('words.BackToList') }}</a>
    </div>
</form>
