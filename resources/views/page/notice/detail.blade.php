@extends('layouts.app')

@section('bodyClass', 'pg-contents pg-contents-detail pg-notice pg-notice-detail')

@include('assets.trumbowyg')
@include('assets.lightbox')

@section('contentHeader')
    @section('contentHeaderTitle')
        <i class="fas fa-bullhorn"></i>
        {{ $data->isEmpty ? __('words.DealsNewPost') : __('words.DealsEditPost') }}
    @endsection
    @include('common.menu.detailMenu', [
        'page' => 'notice'
    ])
@endsection

@push('modals')
    @include('modals.contentPlan.preview')
@endpush

@section('content')
    @include('common.contentPlan.detailForm', [
        'contentType' => Globals::mContentPlan()::CONTENTTYPE_NOTICE
    ])
    @include('assets.page.contentPlanJs', [
        'contentType' => Globals::mContentPlan()::CONTENTTYPE_NOTICE
    ])
@endsection
