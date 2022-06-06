@extends('layouts.app')

@section('bodyClass', 'pg-top')

@section('content')
    <div class="d-flex justify-content-between m-4">
        <h3><i class="fas fa-user"></i> {{__('words.UserManagement') }}</h3>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card card-seemless">
                            <div class="card-body table-responsive">
                                <table class="table  text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('words.Id') }}</th>
                                            <th>{{ __('words.Name') }}</th>
                                            <th>{{ __('words.Email') }}</th>
                                            <th>{{ __('words.RegisteredDate') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($admins as $a)
                                            <tr>
                                                <td class="id">{{ $a->id }}</td>
                                                <td>{{ $a->name }}</td>
                                                <td>{{ $a->email }}</td>
                                                <td>{{ $a->created_at->format('Y年m月d日') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.edit', $a->id) }}" class="btn btn-secondary">{{ __('words.Detail') }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer clearfix">
                                <div class="pagination pagination-sm m-0 justify-content-center">
                                <p>{{ $admins->links() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
