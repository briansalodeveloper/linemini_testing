@extends('layouts.guest')

@section('bodyClass', 'pg-login bg-navy')

@section('content')
    <div class="box">
        <div class="card card-container">
            <div class="card-body">
                <div class="logo">
                    <img src="{{ _vers('images/logo/coop_hp.png') }}" alt="Coop service" class="brand-image">
                    <span class="brand-text font-weight-bold">コープやまぐち</span>
                </div>
                <i class="fa fa-user-tie person"></i>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <span id="reauth-email" class="reauth-email"></span>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            </div>
                            <input type="text" name="uid" class="form-control @if($errors->has('uid')) is-invalid @endif" id="uid" value="{{ old('uid') }}" placeholder="ログインID" autofocus @if($errors->has('uid')) aria-describedby="uid-error" aria-invalid="true" @endif>
                            @if($errors->has('uid'))
                                <span id="uid-error" class="error invalid-feedback">{{ $errors->first('uid') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input type="password" name="password" class="form-control @if($errors->has('password')) is-invalid @endif" id="password" placeholder="パスワード" @if($errors->has('password')) aria-describedby="password-error" aria-invalid="true" @endif>
                            @if($errors->has('password'))
                                <span id="password-error" class="error invalid-feedback">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">サインイン</button>
                </form>
            </div>
        </div>
    </div>
@endsection
