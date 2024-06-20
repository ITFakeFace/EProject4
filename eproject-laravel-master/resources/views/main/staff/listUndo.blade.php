@extends('main._layouts.master')

<?php
// {{ }} <--- special characters will be replaced
// {!! !!} <--- special characters will not be replaced
// {{-- --}} <--- comment code in blade
/**
 * section('scripts') <--- see in master.blade.php <--- it is @yield('scripts')
 * section must have both opening and closing tags
 * if writing PHP code, it should be at the top to load sequentially more accurately like PHP code placed at the top of section('scripts')
 * */
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('js')
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">List of Temporarily Deleted Employees</h1>
        <div class="card-header header-elements-inline">
            <div class="header-elements">

            </div>
        </div>
        <div class="card-body">
            <form action="#" method="GET">

            </form>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Department</th>
                    <th>Is Manager</th>
                    <th>Join Date</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data_staff as $staff)
                    <tr>
                        <td>{{ $staff['id'] }}</td>
                        <td>{{ $staff['code'] }}</td>
                        <td>{{ $staff['firstname'] }}</td>
                        <td>{{ $staff['lastname'] }}</td>
                        @foreach ($data_department as $department)
                            @if ($staff['department'] == $department['id'])
                                <td>{{ $department['name'] }}</td>
                            @endif
                        @endforeach
                        <td>
                            @if ($staff['isManager'] == 0)
                                Employee
                            @else
                                Manager
                            @endif
                        </td>
                        <td>{{ $staff['joinedAt'] }}</td>
                        <td>{{ $staff['dob'] }}</td>
                        <td>
                            @if ($staff['gender'] == 1)
                                Male
                            @else
                                Female
                            @endif
                        </td>
                        <td>
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ action('StaffController@getUndoStaff') }}?id={{ $staff['id'] }}" class="dropdown-item">Undo</a>
                                        <a href="{{ action('StaffController@getDetail') }}?id={{ $staff['id'] }}" class="dropdown-item">Details</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /basic datatable -->
@endsection

@section('scripts')
@endsection
