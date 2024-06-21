<?php
$url = request()->segments() ? request()->segments() : $url = ['abc', 'zxc'];
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
      <li class="nav-item">
        <a class="nav-link" href="ViewmenuController@index">
          <i class="icon-grid menu-icon"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>
      <!-- Chart -->
      @if(auth()->user()->department == 2 or auth()->user()->department == 5)
        <li class="nav-item">
          <a class="nav-link" href="{{action('DashboardController@index')}}">
            <i class="ti-pie-chart menu-icon"></i>
            <span class="menu-title">Charts</span>
          </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#departments" aria-expanded="false" aria-controls="departments">
              <i class="ti-layout-column3 menu-icon"></i>
              <span class="menu-title">Departments</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="departments">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="{{action('DepartmentController@index')}}">All Departments</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{action('DepartmentController@listUndo')}}">Inactive Departments</a></li>
              </ul>
            </div>
        </li>
        <!-- Staff -->
        <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#staff" aria-expanded="false" aria-controls="staff">
            <i class="icon-head menu-icon"></i>
            <span class="menu-title">Staff</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="staff">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="{{action('StaffController@index')}}">All Staff</a></li>
              <li class="nav-item"> <a class="nav-link" href="{{action('StaffController@vaddStaff')}}">Add New Staff</a></li>
              <li class="nav-item"> <a class="nav-link" href="{{action('StaffController@listUndo')}}">All Inactive Staff</a></li>
            </ul>
          </div>
        </li>
        <!-- education -->
        <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#education" aria-expanded="false" aria-controls="education">
            <i class="ti-agenda menu-icon"></i>
            <span class="menu-title">Education</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="education">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="{{action('EducationController@index')}}">All Certificates</a></li>
              <li class="nav-item"> <a class="nav-link" href="{{action('EducationController@addEducation')}}">Add New Certificate</a></li>
            </ul>
          </div>
        </li>
      @endif
      <!-- transfer -->
      <li class="nav-item">
        <a class="nav-link" href="{{ action('TransferController@list') }}">
          <i class="ti-reload menu-icon"></i>
          <span class="menu-title">Transfer</span>
        </a>
      </li>
  
      @if(auth()->user()->department == 2 or auth()->user()->department == 5)
        <!-- education -->
        <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#contract" aria-expanded="false" aria-controls="contract">
            <i class="ti-id-badge menu-icon"></i>
            <span class="menu-title">Contracts</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="contract">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="{{route('getListContract')}}">List of Contracts</a></li>
              <li class="nav-item"> <a class="nav-link" href="{{route('getCreateContract')}}">Add New Contract</a></li>
            </ul>
          </div>
        </li>
      @endif
      <!-- Personal Leave -->
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#leave" aria-expanded="false" aria-controls="leave">
          <i class="mdi mdi-airplane-landing menu-icon"></i>
            <span class="menu-title">Holidays</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="leave">
          <ul class="nav flex-column sub-menu">
            @if(auth()->user()->department == 2 or auth()->user()->id == 7 or auth()->user()->department == 5)
              <li class="nav-item"> <a class="nav-link" href="{{action('SpecialDateController@index')}}">Special Holidays Management</a></li>
            @endif
            @if(auth()->user()->is_manager == 1)
              <li class="nav-item"> 
                <a class="nav-link" href="{{action('SpecialDateController@requestOverTime')}}">
                  @if(auth()->user()->id == 7 or auth()->user()->department == 5)
                      Overtime Management
                  @else
                      Request for Overtime
                  @endif
                </a>
              </li>
            @endif
            @if(auth()->user()->id != 7)
              <li class="nav-item"> <a class="nav-link" href="{{action('CheckInOutController@index')}}">Attendance by GPS</a></li>
              <li class="nav-item"> <a class="nav-link" href="{{action('CheckInOutController@show')}}">Attendance Information </a></li>
              <li class="nav-item"> <a class="nav-link" href="{{action('TimeleaveController@index')}}">Add Attendance for Special Leave</a></li>
            @endif
            @if(auth()->user()->is_manager == 1)
              <li class="nav-item"> 
                <a class="nav-link" href="{{action('TimeleaveController@approveTimeLeave')}}">
                  @if(auth()->user()->is_manager == 1)
                      <span>Approve for Leaves</span>
                  @elseif(auth()->user()->department == 2)
                      <span>View Leaves</span>
                  @endif
                </a>
              </li>
            @endif
            @if(auth()->user()->department == 2 or auth()->user()->id == 7)
              <li class="nav-item"> <a class="nav-link" href="{{action('TimeleaveController@getAllStaffTime')}}">All Attendance </a></li>
              <li class="nav-item"> <a class="nav-link" href="{{action('TimeleaveController@getAllTimeLeave')}}">All Attendance On Special Holidays </a></li>
              <li class="nav-item"> <a class="nav-link" href="{{action('TimeleaveController@getAllTimeInMonth')}}">Total Attendance by Month </a></li>
            @endif
          </ul>
        </div>
      </li>
      <!-- Salary -->    
      @if(auth()->user()->department == 2 or auth()->user()->department == 5)
        <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#salary" aria-expanded="false" aria-controls="salary">
            <i class="mdi mdi-cash-multiple menu-icon"></i>
            <span class="menu-title">Salary</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="salary">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="{{route('getIndexSalary')}}">All Salary</a></li>
              <li class="nav-item"> <a class="nav-link" href="{{route('getCreateSalary')}}">Payroll</a></li>
            </ul>
          </div>
        </li>
      @endif 
      <!-- Personal Payrol -->
      @if(auth()->user()->id != 7 || auth()->user()->department != 5)
        <li class="nav-item">
            <a class="nav-link" href="{{ route('mySalary', ['id' => auth()->id()]) }}">
            <i class="mdi mdi-cash-usd menu-icon"></i>
            <span class="menu-title">Your Payroll</span>
            </a>
        </li>
      @endif
      <!-- About -->
        <li class="nav-item">
            <a class="nav-link" href="{{ action('AboutcompanyController@index') }}">
            <i class="mdi mdi-information-outline menu-icon"></i>
            <span class="menu-title">Introduction</span>
            </a>
        </li>
  </ul>
</nav>
  

