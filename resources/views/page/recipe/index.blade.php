@extends('layouts.app')

@section('bodyClass', 'pg-contents pg-contents-index pg-recipe pg-recipe-index')

@section('contentHeader')
    @section('contentHeaderTitle')
        <i class="fas fa-utensils"></i> {{ __('words.Recipe') }}
    @endsection
    <a href="{{route('recipe.create')}}"><button class="btn btn-dark" >{{ __('words.NewPost') }}</button></a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            @include('common.contentPlan.indexTable', [
                'data' => $data,
                'contentType' => Globals::mContentPlan()::CONTENTTYPE_RECIPE
            ])
        </div>
        <div class="card-footer clearfix">
            <div class="pagination pagination-sm m-0 justify-content-center">
                <p>{{ $data->links()}}</p>
            </div>
        </div>
    </div>
@endsection
