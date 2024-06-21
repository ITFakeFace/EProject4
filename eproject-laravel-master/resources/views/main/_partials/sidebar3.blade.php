<?php
$url = request()->segments() ? request()->segments() : ($url = ['abc', 'zxc']);
?>

<div class="sidebar sidebar-light sidebar-main sidebar-expand-md">

    <!-- Sidebar mobile toggler -->
    <div class="sidebar-mobile-toggler text-center">
        <a href="#" class="sidebar-mobile-main-toggle">
            <i class="icon-arrow-left8"></i>
        </a>
        Navigation
        <a href="#" class="sidebar-mobile-expand">
            <i class="icon-screen-full"></i>
            <i class="icon-screen-normal"></i>
        </a>
    </div>
    <!-- /sidebar mobile toggler -->


    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- User menu -->
        {{-- <div class="sidebar-user">
            <div class="card-body">
                <div class="media">
                    <div class="mr-3">
                        <a href="{{ action('StaffController@viewProfile') }}"><img src="{{ asset('images/user/avatar/default_avatar.png') }}" width="38" height="38" class="rounded-circle" alt=""></a>
                    </div>

                    <div class="media-body">
                        <div class="media-title font-weight-semibold">{{ auth()->user()->firstname . ' ' . auth()->user()->lastname }}</div>
                        <div class="media-title font-weight-semibold">{{ session('department_name') }} - {{ auth()->user()->is_manager == 1 ? 'Manager' : 'Employee' }}</div>
                    </div>

                </div>
            </div>
        </div> --}}
        <!-- /user menu -->


        <!-- Main navigation -->
        <div class="card card-sidebar-mobile">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
                <li class="nav-item">
                    <a href="{{ action('ViewmenuController@index') }}" class="nav-link">
                        <i class="icon-home2"></i>
                        <span>Home</span>
                    </a>
                </li>

                @if (auth()->user()->department == 2 or auth()->user()->department == 5)
                    <li class="nav-item">
                        <a href="{{ action('DashboardController@index') }}" class="nav-link">
                            <i class="icon-stats-growth"></i>
                            <span>Charts</span>
                        </a>
                    </li>

                    <li class="nav-item nav-item-submenu <?php echo $url[0] == 'deparment' || $url[1] == 'department' ? 'nav-item-open' : ''; ?>">
                        <a href="#" class="nav-link"><i class="icon-credit-card"></i> <span>Department</span></a>
                        <ul class="nav nav-group-sub" data-submenu-title="Department" style="display: <?php echo $url[0] == 'deparment' || $url[1] == 'department' ? 'block' : 'none'; ?> ">
                            <li class="nav-item">
                                <a href="{{ action('DepartmentController@index') }}" class="nav-link">
                                    <i class="icon-list"></i>
                                    <span>Department List</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ action('DepartmentController@listUndo') }}" class="nav-link">
                                    <i class="icon-trash"></i>
                                    <span>Trash</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item nav-item-submenu <?php echo ($url[0] == 'staff' && $url[1] !== 'view-profile') || $url[1] == 'staff' ? 'nav-item-open' : ''; ?>">
                        <a href="#" class="nav-link"><i class="icon-user"></i> <span>Employees</span></a>
                        <ul class="nav nav-group-sub" data-submenu-title="Employee" style="display: <?php echo ($url[0] == 'staff' && $url[1] !== 'view-profile') || $url[1] == 'staff' ? 'block' : 'none'; ?>">
                            <li class="nav-item">
                                <a href="{{ action('StaffController@index') }}" class="nav-link">
                                    <i class="icon-list"></i>
                                    <span>List</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ action('StaffController@vaddStaff') }}" class="nav-link">
                                    <i class="icon-plus2"></i>
                                    <span>Add Employee</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ action('StaffController@listUndo') }}" class="nav-link">
                                    <i class="icon-trash"></i>
                                    <span>Trash</span>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <li class="nav-item nav-item-submenu <?php echo $url[0] == 'education' || $url[1] == 'education' ? 'nav-item-open' : ''; ?>">
                        <a href="#" class="nav-link"><i class="icon-graduation"></i> <span>Education</span></a>
                        <ul class="nav nav-group-sub" data-submenu-title="Education" style="display: <?php echo $url[0] == 'education' || $url[1] == 'education' ? 'block' : 'none'; ?>">
                            <li class="nav-item">
                                <a href="{{ action('EducationController@index') }}" class="nav-link">
                                    <i class="icon-list"></i>
                                    <span>List</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ action('EducationController@addEducation') }}" class="nav-link">
                                    <i class="icon-plus2"></i>
                                    <span>Add Degree</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif


                <li class="nav-item">
                    <a href="{{ action('TransferController@list') }}" class="nav-link">
                        <i class="icon-transmission"></i>
                        <span>Transfers</span>
                    </a>
                </li>


                @if (auth()->user()->department == 2 or auth()->user()->department == 5)
                    <li class="nav-item nav-item-submenu <?php echo $url[0] == 'contract' || $url[1] == 'contract' ? 'nav-item-open' : ''; ?>">
                        <a href="#" class="nav-link"><i class="icon-newspaper2"></i> <span>Contracts</span></a>
                        <ul class="nav nav-group-sub" data-submenu-title="Contracts" style="display: <?php echo $url[0] == 'contract' || $url[1] == 'contract' ? 'block' : 'none'; ?>">
                            <li class="nav-item">
                                <a href="{{ route('getListContract') }}" class="nav-link">
                                    <i class="icon-list"></i>
                                    <span>List</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('getCreateContract') }}" class="nav-link">
                                    <i class="icon-plus2"></i>
                                    <span>Create Contract</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <?php
                $active = '';
                $display = 'none';
                if ($url[0] == 'special-date' or $url[0] == 'over-time' or $url[0] == 'check-in-gps' or $url[0] == 'staff-time' or $url[0] == 'time-leave' or $url[0] == 'approve-time-leave' or $url[0] == 'staff-time') {
                    $active = 'nav-item-open';
                    $display = 'block';
                }
                
                if ($url[1] == 'time-leave') {
                    $active = 'nav-item-open';
                    $display = 'block';
                }
                
                ?>
                <li class="nav-item nav-item-submenu <?php echo $active; ?>">
                    <a href="#" class="nav-link"><i class="icon-stack"></i> <span>Leave</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Leave" style="display: <?php echo $display; ?>">
                        @if (auth()->user()->department == 2 or auth()->user()->id == 7 or auth()->user()->department == 5)
                            <li class="nav-item">
                                <a href="{{ action('SpecialDateController@index') }}" class="nav-link">
                                    <i class="icon-calendar2"></i>
                                    <span>Manage Holidays</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->is_manager == 1)
                            <a href="{{ action('SpecialDateController@requestOverTime') }}" class="nav-link">
                                <i class="icon-calendar22"></i>
                                @if (auth()->user()->id == 7 or auth()->user()->department == 5)
                                    <span>Manage Overtime</span>
                                @else
                                    <span>Request Overtime</span>
                                @endif
                            </a>
                        @endif
                        @if (auth()->user()->id != 7)
                            <li class="nav-item">
                                <a href="{{ action('CheckInOutController@index') }}" class="nav-link">
                                    <i class="icon-clipboard5"></i>
                                    <span>GPS Check-in</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ action('CheckInOutController@show') }}" class="nav-link">
                                    <i class="icon-clipboard6"></i>
                                    <span>Check-in History</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ action('TimeleaveController@index') }}" class="nav-link">
                                    <i class="icon-calendar"></i>
                                    <span>Supplement Leave</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->is_manager == 1)
                            <li class="nav-item">
                                <a href="{{ action('TimeleaveController@approveTimeLeave') }}" class="nav-link">
                                    <i class="icon-checkbox-checked"></i>
                                    @if (auth()->user()->is_manager == 1)
                                        <span>Approve Leave</span>
                                    @elseif(auth()->user()->department == 2)
                                        <span>View Leave</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->department == 2 or auth()->user()->id == 7)
                            <li class="nav-item">
                                <a href="{{ action('TimeleaveController@getAllStaffTime') }}" class="nav-link">
                                    <i class="icon-list"></i>
                                    <span>All Check-ins</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ action('TimeleaveController@getAllTimeLeave') }}" class="nav-link">
                                    <i class="icon-list2"></i>
                                    <span>All Leaves</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ action('TimeleaveController@getAllTimeInMonth') }}" class="nav-link">
                                    <i class="icon-paragraph-left2"></i>
                                    <span>Monthly Overview</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                @if (auth()->user()->department == 2 or auth()->user()->department == 5)
                    <li class="nav-item nav-item-submenu <?php echo $url[0] == 'salary' || $url[1] == 'salary' ? 'nav-item-open' : ''; ?>">
                        <a href="#" class="nav-link"><i class="icon-cash3"></i> <span>Salary</span></a>
                        <ul class="nav nav-group-sub" data-submenu-title="Salary" style="display: <?php echo $url[0] == 'salary' || $url[1] == 'salary' ? 'block' : 'none'; ?>">
                            <li class="nav-item">
                                <a href="{{ route('getIndexSalary') }}" class="nav-link">
                                    <i class="icon-list"></i>
                                    <span>List</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('getCreateSalary') }}" class="nav-link">
                                    <i class="icon-plus2"></i>
                                    <span>Calculate Salary</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (auth()->user()->id != 7 || auth()->user()->department != 5)
                    <li class="nav-item">
                        <a href="{{ route('mySalary', ['id' => auth()->id()]) }}" class="nav-link">
                            <i class="icon-cash3"></i>
                            <span>Personal Salary</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item nav-item-submenu <?php echo $url[0] == 'kpi' || $url[1] == 'kpi' ? 'nav-item-open' : ''; ?>">
                    <a href="#" class="nav-link"><i class="icon-racing"></i> <span>KPI</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="KPI" style="display: <?php echo $url[0] == 'kpi' || $url[1] == 'kpi' ? 'block' : 'none'; ?>">
                        @if (auth()->user()->id != 7)
                            <li class="nav-item">
                                <a href="{{ action('KpiController@setKpi') }}" class="nav-link">
                                    <i class="icon-finish"></i>
                                    <span>Set KPI</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->is_manager == 1 or auth()->user()->department == 2)
                            <li class="nav-item">
                                <a href="{{ action('KpiController@listKpi') }}" class="nav-link">
                                    <i class="icon-list2"></i>
                                    <span>List KPI</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                <!-- /main -->

            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>
