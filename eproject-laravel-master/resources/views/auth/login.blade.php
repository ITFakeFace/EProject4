@extends('auth._layouts.master')

@section('content')
    <div class="content d-flex justify-content-center align-items-center">

        <!-- Login form -->
        <form class="login-form" method="post" action="{{ route('postLogin') }}" style="width: 20rem">
            @csrf
            <div class="card mb-0">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-styled-left alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    @if(session('authentication'))
                        <div class="alert alert-danger alert-styled-left alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            {{ session('authentication') }}
                        </div>
                    @endif
                    <div class="text-center mb-3">
                        <i class="icon-reading icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1"></i>
                        <h5 class="mb-0">Đăng nhập tài khoản</h5>
                    </div>

                    <div class="form-group form-group-feedback form-group-feedback-left">
                        <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}">
                        <div class="form-control-feedback">
                            <i class="icon-user text-muted"></i>
                        </div>
                    </div>

                    <div class="form-group form-group-feedback form-group-feedback-left">
                        <input type="password" class="form-control" placeholder="Mật khẩu" name="password">
                        <div class="form-control-feedback">
                            <i class="icon-lock2 text-muted"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Đăng nhập <i class="icon-circle-right2 ml-2"></i></button>
                    </div>

                    <div class="text-center">
                        <a href="{{ action('AuthenticateController@getForgot') }}">Quên mật khẩu?</a>
                    </div>
                </div>
            </div>
        </form>
        <!-- /login form -->

    </div>
@endsection
