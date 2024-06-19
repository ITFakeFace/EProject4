@extends('auth._layouts.master')

@section('content')
<!-- Content area -->
<div class="content d-flex justify-content-center align-items-center">

    <!-- Password recovery form -->
    <form class="login-form" action="{{ action('AuthenticateController@postForgot') }}" method="POST">
        @csrf
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
                    <span class="d-block text-muted">Chúng tôi sẽ gửi cho bạn hướng dẫn qua email</span>
                </div>

                <div class="form-group form-group-feedback form-group-feedback-right">
                    <input type="email" class="form-control" name="email" placeholder="Nhập email">
                    <div class="form-control-feedback">
                        <i class="icon-mail5 text-muted"></i>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn bg-blue btn-block"><i class="icon-spinner11 mr-2"></i> Đặt lại mật khẩu</button>
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
