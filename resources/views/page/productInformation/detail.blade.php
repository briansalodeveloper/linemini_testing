@extends('layouts.app')

@section('bodyClass', 'pg-contents pg-contents-detail pg-notice pg-notice-detail')

@include('assets.trumbowyg')
@include('assets.lightbox')

@section('contentHeader')
    @section('contentHeaderTitle')
        <i class="fas fa-basket-shopping"></i>
        {{ $data->isEmpty ? __('words.ProductInformationNewPost') : __('words.ProductInformationEditPost') }}
    @endsection
    @include('common.menu.detailMenu', [
        'page' => 'productInformation'
    ])
@endsection

@push('modals')
    @include('modals.contentPlan.preview')
@endpush

@section('content')
    @include('common.contentPlan.detailForm', [
        'contentType' => Globals::mContentPlan()::CONTENTTYPE_PRODUCTINFO
    ])
    @include('assets.page.contentPlanJs', [
        'contentType' => Globals::mContentPlan()::CONTENTTYPE_PRODUCTINFO
    ])
@endsection
