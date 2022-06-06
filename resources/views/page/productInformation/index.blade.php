@extends('layouts.app')

@section('bodyClass', 'pg-contents pg-contents-index pg-productInformation pg-productInformation-index')

@section('contentHeader')
    @section('contentHeaderTitle')
        <i class="fas fa-basket-shopping"></i> {{ __('words.ProductInformation') }}
    @endsection
    <a href="{{route('productInformation.create')}}"><button class="btn btn-dark" >{{ __('words.NewPost') }}</button></a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            @include('common.contentPlan.indexTable', [
                'data' => $data,
                'contentType' => Globals::mContentPlan()::CONTENTTYPE_PRODUCTINFO
            ])
        </div>
        <div class="card-footer clearfix">
            <div class="pagination pagination-sm m-0 justify-content-center">
                <p>{{ $data->links()}}</p>
            </div>
        </div>
    </div>
@endsection
