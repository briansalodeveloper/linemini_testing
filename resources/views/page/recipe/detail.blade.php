@extends('layouts.app')

@section('bodyClass', 'pg-contents pg-contents-detail pg-recipe pg-recipe-detail')

@include('assets.trumbowyg')
@include('assets.lightbox')

@section('contentHeader')
    @section('contentHeaderTitle')
        <i class="fas fa-utensils"></i>
        {{ $data->isEmpty ? __('words.RecipeNewPost') : __('words.RecipeEditPost') }}
    @endsection
    @include('common.menu.detailMenu', [
        'page' => 'recipe'
    ])
@endsection

@push('modals')
    @include('modals.contentPlan.preview')
@endpush

@section('content')
    @include('common.contentPlan.detailForm', [
        'contentType' => Globals::mContentPlan()::CONTENTTYPE_RECIPE
    ])
    @include('assets.page.contentPlanJs', [
        'contentType' => Globals::mContentPlan()::CONTENTTYPE_RECIPE
    ])
@endsection
