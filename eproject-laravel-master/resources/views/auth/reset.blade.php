@extends('auth._layouts.master')

@section('content')
<!-- Content area -->
<div class="content d-flex justify-content-center align-items-center">

    <!-- Password recovery form -->
    <form class="login-form" action="{{ action('AuthenticateController@postReset') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="card mb-0">
            <div class="card-body">
                @if (\Session::has('success'))
                    <div class="">
                        <div class="alert alert-success">
                            {!! \Session::get('success') !!}
                        </div>
                    </div>
                @endif

                @if (\Session::has('error'))
                    <div class="">
                        <div class="alert alert-danger">
                            {!! \Session::get('error') !!}
                        </div>
                    </div>
                @endif
                <div class="text-center mb-3">
                    <i class="icon-spinner11 icon-2x border-3 rounded-round p-3 mb-3 mt-1" style="color: #046A38; border-color: #046A38"></i>
                    <h5 class="mb-0">Khôi phục mật khẩu</h5>
                    <span class="d-block text-muted">Nhập mật khẩu mới để khôi phục lại mật khẩu của bạn</span>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="password" class="form-control" placeholder="Mật khẩu mới" name="password">
                    <div class="form-control-feedback">
                        <i class="icon-lock2 text-muted"></i>
                    </div>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-left">
                    <input type="password" class="form-control" placeholder="Nhập lại mật khẩu mới" name="password_confirm">
                    <div class="form-control-feedback">
                        <i class="icon-lock2 text-muted"></i>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn bg-blue btn-block"> Đặt lại mật khẩu</button>
                </div>

                <div class="text-center">
                    <a href="{{ action('AuthenticateController@getLogin') }}">Quay về trang đăng nhập!</a>
                </div>
            </div>
        </div>
    </form>
    <!-- /password recovery form -->

</div>
<!-- /content area -->
@endsection
