@extends('layouts.app')

@section('bodyClass', 'pg-contents pg-contents-detail pg-column pg-column-detail')

@include('assets.trumbowyg')
@include('assets.lightbox')

@section('contentHeader')
    @section('contentHeaderTitle')
        <i class="fas fa-file-pen"></i>
        {{ $data->isEmpty ? __('words.ColumnNewPost') : __('words.ColumnEditPost') }}
    @endsection
    @include('common.menu.detailMenu', [
        'page' => 'column'
    ])
@endsection

@push('modals')
    @include('modals.contentPlan.preview')
@endpush

@section('content')
    @include('common.contentPlan.detailForm', [
        'contentType' => Globals::mContentPlan()::CONTENTTYPE_COLUMN
    ])
    @include('assets.page.contentPlanJs', [
        'contentType' => Globals::mContentPlan()::CONTENTTYPE_COLUMN
    ])
@endsection
