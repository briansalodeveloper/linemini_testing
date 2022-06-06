@extends('layouts.app')

@section('bodyClass', 'pg-message pg-message-index')

@section('contentHeader')
    @section('contentHeaderTitle')
        <i class="fas fa-envelope"></i> {{ __('words.Message') }} {{ __('words.TransmissionHistory') }}
    @endsection
    <a href="{{route('message.create')}}"><button class="btn btn-dark" >{{ __('words.CreateNew') }}</button></a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table text-nowrap">
                <thead>
                    <tr>
                        <th>{{ __('words.Id') }}</th>
                        <th>{{ __('words.ManagementName') }}</th>
                        <th>{{ __('words.DateAndTimeTransmission') }}</th>
                        <th>{{ __('words.Status') }}</th>
                        <th>{{ __('words.TransmissionObject') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $datum)
                        <tr>
                            <td class="id">{{ $datum->id }}</td>
                            <td>{{ $datum->messageName }}</td>
                            <td>{{ $datum->formatDate('sendDateTime', 'Y/m/d H:i') }}</td>
                            <td>{{ $datum->statusStr }}</td>
                            <td>{{ $datum->sendTargetFlgStr }}</td>
                            <td>
                                <a href="{{ route('message.edit', $datum->id) }}"><button class="btn btn-01">{{
                                    $datum->isStatusSend ? __('words.Detail') : __('words.Edit')
                                }}</button></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            <div class="pagination pagination-sm m-0 justify-content-center">
                <p>{{ $data->links()}}</p>
            </div>
        </div>
    </div>
@endsection
