@extends('layouts.app')

@section('bodyClass', 'pg-message pg-message-detail')

@include('assets.trumbowyg')
@include('assets.lightbox')
@include('assets.js.undoEdit')
@include('assets.page.messageJs')
@include('modals.message.preview')

@section('contentHeader')
    @section('contentHeaderTitle')
        <i class="fas fa-envelope"></i>
        {{ __('words.Message') . ' ' }}{{ $data->isEmpty ? __('words.CreateNew') : ($data->getAttr('isStatusSend', false) ? __('words.Detail') : __('words.Edit')) }}
    @endsection
    @include('common.menu.detailMenu', [
        'page' => 'message'
    ])
@endsection

@section('content')
    @php
        $route = $data->isEmpty ? route('message.store') : route('message.update', $data->id);

        $csvLabel = '';
        $csvUrl = old('unionMemberCsv');
        $logo = '';
        $imageLabel = '';
        $imageUrl = old('thumbnail', $data->getAttr('thumbnail'));

        if (old('unionMemberCsv', null) != null) {
            $logo = '<i class="fa fa-file-csv"></i> ';
            $csvLabel = '<a href="' . $csvUrl . '" download>' . $logo . \Globals::hUpload()::getBaseName($csvUrl) . '</a>';
        }

        if (old('thumbnail', null) == null) {
            if ($data->isNotEmpty) {
                if ($data->IsThumbnailExist) {
                    $logo = '<i class="fa fa-image"></i> ';
                    $imageLabel = '<a href="' . $imageUrl . '" data-toggle="lightbox" title="' . __('words.Preview') . '">' . $logo . \Globals::hUpload()::getBaseName($imageUrl) . '</a>';
                } else {
                    $logo = '<i class="fa fa-circle-xmark text-red" title="' . __('messages.custom.imageNotExist') . '"></i> ';
                    $imageLabel = $logo . \Globals::hUpload()::getBaseName($imageUrl);
                }
            }
        } else {
            $logo = '<i class="fa fa-image"></i> ';
            $imageLabel = '<a href="' . $imageUrl . '" data-toggle="lightbox" title="' . __('words.Preview') . '">' . $logo . \Globals::hUpload()::getBaseName($imageUrl) . '</a>';
        }
    @endphp
    <form method="POST" enctype="multipart/form-data" id="form" action="{{ $route }}">
        @csrf

        @if($data->isNotEmpty)
            <input type="text" hidden name="messageId" value="{{ $data->id }}">
    
            @if(!empty($data->kumicd))
                <input type="text" hidden name="kumicd" value="{{ $data->kumicd }}">
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
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="messageName">{{ __('words.ManagementName') }}</label>
            <div class="row">
                <div class="col-md-5">
                    <input type="text" class="form-control @if($errors->has('messageName')) is-invalid @endif" name="messageName" id="messageName"
                        placeholder="{{ __('words.ManagementName') }}" value="{{ old('messageName', $data->getAttr('messageName')) }}"
                        data-original-value="{{ $data->getAttr('messageName') }}"
                        {{ $data->getAttr('isStatusSend', false) ? 'readonly' : '' }}>
                    @include('common.validationError', ['key' => 'messageName'])
                </div>
            </div>
        </div>
        @if($data->getAttr('isStatusNotSend', true))
            <div class="form-group">
                <label>{{ __('words.SelectTransmissionTiming') }}</label>
                <div class="col-12{{ $errors->has('selectTransmissionTiming') ? ' is-invalid' : '' }}">
                    <div class="icheck-primary">
                        <input type="radio" id="sendType1"  value="0" name="selectTransmissionTiming" {!! old('selectTransmissionTiming') == '0' ? 'checked': ''!!}>
                        <label for="sendType1">{{ __('words.SendImmediately') }}</label>
                    </div>
                    <div class="icheck-primary">
                        <input type="radio" id="sendType2"  value="1" name="selectTransmissionTiming"  {!! old('selectTransmissionTiming') == '1' ? 'checked': ''!!}>
                        <label for="sendType2">{{ __('words.BookAndSend') }}</label>
                    </div>
                </div>
                @include('common.validationError', ['key' => 'selectTransmissionTiming'])
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="row">
                    @php
                        $sendDate = null;
                        $sendTime = null;
                        if($data->isNotEmpty) {
                            $sendDate = preg_split('/(0?[1-9]|1[0-2]):([0-5]\d)\s?((?:[Aa]|[Pp])\.?[Mm]\.?)/', $data->sendDateTime)[0];
                            $sendTime = preg_split('/\d{2}\/\d{2}\/\d{4}\s/', $data->sendDateTime)[1];
                        }
                    @endphp
                    <div class="form-group col-2">
                        <label>{{ __('words.SendDate') }}</label>
                        <div class="input-group date" id="sendDate" data-target-input="nearest">
                            <input type="text" id="sendDateInput" class="form-control datetimepicker-input @if($errors->has('sendDateTime')) is-invalid @endif" name="sendDate" placeholder="{{ __('words.DateFormat') }}" value="{{ old( 'sendDate', $sendDate ) }}" data-original-value="{{ $sendDate }}" data-target="#sendDate">
                            <div class="input-group-append" data-target="#sendDate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                            @if($errors->has('sendDateTime'))
                                <span id="sendDateTime-error" class="error invalid-feedback">{{ $errors->first('sendDateTime') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-2">
                        <label>{{ __('words.TransmissionTime') }}</label>
                        <div class="input-group date" id="sendTime" data-target-input="nearest">
                            <input type="text" id="sendTimeInput" class="form-control datetimepicker-input @if($errors->has('sendDateTime')) is-invalid @endif" name="sendTime" value="{{ old( 'sendTime', $sendTime ) }}" data-original-value="{{ $sendTime }}" data-target="#sendTime">
                            <div class="input-group-append" data-target="#sendTime" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-clock"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>{{ __('words.SelectTheRecipient') }}</label>
            <div class="icheck-primary">
                <input type="radio" id="sendTargetFlg1" value="{{ \Globals::mMessage()::SENDTARGET_UNCONDITIONAL }}"
                    name="sendTargetFlg" {!! old('sendTargetFlg', $data->getAttr('sendTargetFlg', '-1')) == \Globals::mMessage()::SENDTARGET_UNCONDITIONAL ? 'checked': '' !!}
                    data-original-value="{{ $data->getAttr('sendTargetFlg') }}">
                <label for="sendTargetFlg1">{{ __('words.Unconditional') }}</label>
            </div>
            <div class="icheck-primary">
                <input type="radio" id="sendTargetFlg2" value="{{ \Globals::mMessage()::SENDTARGET_UNIONMEMBER }}"
                    name="sendTargetFlg" {!! old('sendTargetFlg', $data->getAttr('sendTargetFlg', '-1')) == \Globals::mMessage()::SENDTARGET_UNIONMEMBER ? 'checked': '' !!}
                    data-original-value="{{ $data->getAttr('sendTargetFlg') }}">
                <label for="sendTargetFlg2">{{ __('words.UnionMemberDesignation') }}</label>
            </div>
            <div class="icheck-primary">
                <input type="radio" id="sendTargetFlg3" value="{{ \Globals::mMessage()::SENDTARGET_UB }}"
                    name="sendTargetFlg" {!! old('sendTargetFlg', $data->getAttr('sendTargetFlg', '-1')) == \Globals::mMessage()::SENDTARGET_UB ? 'checked': '' !!}
                    data-original-value="{{ $data->getAttr('sendTargetFlg') }}">
                <label for="sendTargetFlg3">{{ __('words.UserBusinessDesignation') }}</label>
            </div>
            <div class="icheck-primary{{ $errors->has('sendTargetFlg') ? ' is-invalid' : '' }}">
                <input type="radio" id="sendTargetFlg4" value="{{ \Globals::mMessage()::SENDTARGET_AO }}"
                    name="sendTargetFlg" {!! old('sendTargetFlg', $data->getAttr('sendTargetFlg', '-1')) == \Globals::mMessage()::SENDTARGET_AO ? 'checked': '' !!}
                    data-original-value="{{ $data->getAttr('sendTargetFlg') }}">
                <label for="sendTargetFlg4">{{ __('words.OfficeDesignation') }}</label>
            </div>
            <div class="icheck-primary{{ $errors->has('sendTargetFlg') ? ' is-invalid' : '' }}">
                <input type="radio" id="sendTargetFlg5" value="{{ \Globals::mMessage()::SENDTARGET_STORE }}"
                    name="sendTargetFlg" {!! old('sendTargetFlg', $data->getAttr('sendTargetFlg', '-1')) == \Globals::mMessage()::SENDTARGET_STORE ? 'checked': '' !!}
                    data-original-value="{{ $data->getAttr('sendTargetFlg') }}">
                <label for="sendTargetFlg5">{{ __('words.SelectAtTheRegisteredStore') }}</label>
            </div>
            @include('common.validationError', ['key' => 'sendTargetFlg'])
        </div>
        <div class="col-md-12 pl-3">
            <div class="form-group">
                <p><b>{{ __('words.CSVFileUploadInstruction') }}</b></p>
                @include('common.input.fileCustom', [
                    'id' => 'unionMemberCsvTrigger',
                    'name' => 'csv',
                    'classContainer' => 'file-csv' . ($errors->has('unionMemberCsv') ? ' is-invalid' : ''),
                    'accept' => \Globals::implode(\Globals::mMessage()::CSV_ACCEPTEDEXTENSION, ',', '.'),
                    'label' => $csvLabel,
                    'hiddenName' => 'unionMemberCsv',
                    'hiddenValue' => $csvUrl,
                    'disabled' => old('sendTargetFlg', $data->getAttr('sendTargetFlg')) != 1,
                    'originalValue' => '',
                ])
                @if($errors->has('unionMemberCsv'))
                    <span id="unionMemberCsv-error" class="error invalid-feedback">{{ $errors->first('unionMemberCsv') }}</span>
                @endif
                <span id="unionMemberCsv-error" class="error invalid-feedback"></span>
            </div>
            <div class="form-group ub-ao-store-box">
                <label>{{ __('messages.custom.specifyAoOrUbOrStore') }}</label>
                <select class="ub-list form-control col-md-3{{ $errors->has('ubId') ? ' is-invalid' : '' }}" name="ubId">
                    <option value="" disabled{!! empty(old('ubId', $data->ubId)) ? ' selected' : '' !!} >{{ __('words.BusinessSelection') }}</option>
                    @foreach( $ubList as $key => $val )
                        <option value="{{ $key }}"{!! old('ubId', $data->getAttr('ubId')) == $key ?  ' selected': '' !!}>{{ $val }}</option>
                    @endforeach
                </select>
                @include('common.validationError', ['key' => 'ubId'])
                <select class="ao-list form-control col-md-3{{ $errors->has('aoId') ? ' is-invalid' : '' }}" name="aoId" disabled>
                    <option value="" disabled {!! empty(old('aoId', $data->aoId)) ? ' selected' : '' !!} >{{ __('words.AffiliateOffice') }}</option>
                    @foreach( $aoList as $key => $val )
                        <option value="{{ $key }}" {!! old('aoId', $data->getAttr('aoId')) == $key ?  'selected': '' !!}>{{ $val }}</option>
                    @endforeach
                </select>
                @include('common.validationError', ['key' => 'aoId'])
                <select class="store-list form-control col-md-3{{ $errors->has('storeId') ? ' is-invalid' : '' }}" name="storeId" disabled>
                    <option value="" disabled {!! empty(old('storeId', $data->storeId)) ? ' selected' : '' !!} >{{ __('words.AffiliateOffice') }}</option>
                    @foreach( $storeList as $key => $val )
                        <option value="{{ $key }}" {!! old('storeId', $data->getAttr('storeId')) == $key ?  'selected': '' !!}>{{ $val }}</option>
                    @endforeach
                </select>
                @include('common.validationError', ['key' => 'storeId'])
            </div>
        </div>
        <div class="form-group">
            <p><b>{{ __('words.BodyContentTransmissionEmoji') }}</b></p>
            @if($data->getAttr('isStatusNotSend', true))
                <textarea id="contentsOriginalValue" hidden>{{ $data->getAttr('contents') }}</textarea>
                <textarea id="contents" name="contents" data-original-value="#contentsOriginalValue">{!! old('contents', $data->getAttr('contents')) !!}</textarea>
            @else
                <div class="contents">
                    {!! $data->getAttr('contents') !!}
                </div>
            @endif
        </div>
        <div class="form-group">
            <p>
                <b>{{ __('words.SendImage') }}</b>
                @if($data->getAttr('isStatusNotSend', true))
                    <span class="text-xs ml-4">ファイルサイズは2M以下、形式はJPEG or PNG のみとなります。</span>
                @endif
            </p>
            @include('common.input.fileCustom', [
                'id' => 'imageTrigger',
                'name' => 'image',
                'classContainer' => 'file-image',
                'accept' => \Globals::implode(\Globals::mMessage()::THUMBNAIL_ACCEPTEDEXTENSION, ',', '.'),
                'label' => $imageLabel,
                'hiddenName' => 'thumbnail',
                'hiddenValue' => $imageUrl,
                'disabled' => $data->getAttr('isStatusSend', false),
                'originalValue' => $data->getAttr('thumbnail')
            ])
            <span id="thumbnail-error" class="form-custom-error">{{ $errors->first('thumbnail') }}</span>
        </div>
        <div class="row justify-content-center mb-3">
            <button type="submit" id="preview" class="btn btn-02 col-md-3 col-sm-3 w-100">{{ __('words.Preview') }}</button>
        </div>
        @if($data->getAttr('isStatusNotSend', true))
            <div class="row justify-content-center mb-3">
                <button id="submit" type="submit" class="btn btn-success col-md-3 col-sm-3 w-100 text-white">{{ __('words.Send') }}</button>
            </div>
        @endif
        <div class="row justify-content-end mt-2 mb-3">
            <a href="{{ route('message.index') }}" class="btn btn-02">{{ __('words.BackToList') }}</a>
        </div>
    </form>
@endsection

