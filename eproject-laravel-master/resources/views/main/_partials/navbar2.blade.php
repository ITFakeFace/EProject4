<div class="navbar navbar-expand-md navbar-dark">
    <div class="navbar-brand p-0">
        <a href="{{ action('ViewmenuController@index') }}" class="d-inline-block">
            <img src="{{ asset('images/logo.png') }}" alt="" width="240px" style="height: auto">
            <span>Tân Thành Nam</span>
        </a>
    </div>

    <div class="d-md-none">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
            <i class="icon-tree5"></i>
        </button>
        <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
            <i class="icon-paragraph-justify3"></i>
        </button>
    </div>

    <div class="collapse navbar-collapse" id="navbar-mobile">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block">
                    <i class="icon-paragraph-justify3"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">

            <li class="nav-item dropdown dropdown-user">
                <a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown">
                    <span>{{ auth()->user() ? auth()->user()->firstname . ' ' . auth()->user()->lastname : null }}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ action('StaffController@viewProfile') }}" class="dropdown-item"><i class="icon-user-plus"></i> Thông tin cá nhân</a>
                    <a href="{{ action('AuthenticateController@getLogout') }}" class="dropdown-item"><i class="icon-switch2"></i> Đăng xuất</a>
                </div>
            </li>
        </ul>
    </div>
</div>
