<div class="navbar navbar-expand-md navbar-light" style="border-bottom: solid rgba(195, 195, 195, 0.323) 2px">
    <div class="navbar-brand p-0" style="display: flex;align-items: center">
        <a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-inline-block" style="padding: 5px; margin-right: 20px;">
            <i class="icon-paragraph-justify3"></i>
        </a>
        <a href="{{ action('DashboardController@index') }}" class="d-inline-block">
            <img src="{{ asset('images/Logo-FutureHRM-index.svg') }}" alt="" style="height: 3rem;width: auto;">
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

            </li>
        </ul>

        <ul class="navbar-nav ml-auto">

            <li class="nav-item dropdown dropdown-user">
                <a href="#" class="navbar-nav-link d-flex align-items-center" data-toggle="dropdown">
                    <span style="margin-right: 10px">{{ auth()->user() ? auth()->user()->firstname . ' ' . auth()->user()->lastname : null }}</span>
                    <img src="{{ asset(auth()->user()->photo) }}" alt="profile" style="height: 3rem;width: auto; border-radius: 20px;" />
                </a>

                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ action('StaffController@viewProfile') }}" class="dropdown-item"><i class="icon-user-plus"></i> Profile</a>
                    <a href="{{ action('AuthenticateController@getLogout') }}" class="dropdown-item"><i class="icon-switch2"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>
</div>
