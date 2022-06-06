@extends('layouts.app')

@section('bodyClass', 'pg-top')

@section('contentHeaderTitle')
    <i class="fas fa-tachometer-alt half"></i>
    {{__('words.CurentNumberOfRegisteredPeople') }}  
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row card-count mb-5">
                            <div class="col-md-3">
                                <span>現在のカード連携人数</span>
                                <span class="count">{{ number_format(0, 0) }}人</span>
                            </div>
                            <div class="col-md-3">
                                <span>昨日のカード連携人数</span>
                                <span class="count">{{ number_format(0, 0) }}人</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <span>利用事業所別カード連携人数</span>
                        </div>
                        <div class="card card-seemless">
                            <div class="card-body table-responsive">
                                <table class="table  text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('words.OfficeCode') }}</th>
                                            <th>{{ __('words.OfficeName') }}</th>
                                            <th>{{ __('words.NumberOfCollaborators') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- TODO: data --}}
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer clearfix">
                                <div class="pagination pagination-sm m-0 justify-content-center">
                                {{-- TODO: data pagination --}}
                                <p>{{-- $data->links()--}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
