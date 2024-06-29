@extends('auth._layouts.master')

@section('content')
<!-- Content area -->
<div class="content d-flex justify-content-center align-items-center">

    <!-- Password recovery form -->
    <form class="login-form" action="{{ action('AuthenticateController@postReset') }}" method="POST" style="width: 30rem; height:30rem">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="card mb-0">
            <div class="card-body">
                @if (\Session::has('success'))
                    <div class="">
                        <div class="alert alert-success">
                            {!! \Session::get('success') !!}
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
                        {{-- <i class="icon-spinner11 icon-2x border-3 rounded-round p-3 mb-3 mt-1" style="color: #4B49AC; border-color: #4B49AC"></i> --}}
                        <img src="{{ asset('images/Logo-FutureHRM-index.svg') }}" alt="Logo" style="width: 200px; height: 100px;">
                        <h5 class="mb-0">Reset Password</h5>
                        <span class="d-block text-muted">Enter a new password for your account</span>
                    </div>

                    <div class="form-group form-group-feedback form-group-feedback-left">
                        <input type="password" class="form-control" placeholder="Enter new password" name="password">
                        <div class="form-control-feedback">
                            <i class="icon-lock2 text-muted"></i>
                        </div>
                    </div>

                    <div class="form-group form-group-feedback form-group-feedback-left">
                        <input type="password" class="form-control" placeholder="Confirm new password" name="password_confirm">
                        <div class="form-control-feedback">
                            <i class="icon-lock2 text-muted"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn bg-blue btn-block"> Reset Password</button>
                    </div>

                    <div class="text-center">
                        Or <a href="{{ action('AuthenticateController@getLogin') }}">Login?</a>
                    </div>
                </div>
            </div>
        </form>
        <!-- /password recovery form -->

    </div>
    <!-- /content area -->
@endsection
