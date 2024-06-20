@extends('main._layouts.master')

<?php
    // {{ }} <--- special characters will be replaced
    // {!! !!} <--- special characters will not be replaced
    // {{-- --}} <--- comment code in Blade
    /**
     * section('scripts') <--- check in master.blade.php <--- it is @yield('scripts')
     * section opening should have a closing line
     * if writing PHP code, it's better to start at the top to load more accurately like PHP code on section('scripts') okay then
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
        <div class="card-header header-elements-inline">
            <h1 class="pt-3 pl-3 pr-3">List of Education Information</h1>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="#" method="GET">
                <!-- Placeholder for any form elements -->
            </form>
        </div>

        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee Name</th>
                    <th>Level</th>
                    <th>Level Name</th>
                    <th>School Name</th>
                    <th>Field of Study</th>
                    <th>Graduation Year</th>
                    <th>Grade</th>
                    <th>Study Mode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data_education as $education)
                    <tr>
                        <td>{{ $education['id'] }}</td>
                        @foreach ($data_staff as $staff)
                            @if ($education['staffId'] == $staff['id'])
                                <td>{{$staff['firstname']}} {{$staff['lastname']}}</td>
                            @endif
                        @endforeach
                        <td>{{ $education['level'] }}</td>
                        <td>{{ $education['levelName'] }}</td>
                        <td>{{ $education['school'] }}</td>
                        <td>{{ $education['fieldOfStudy'] }}</td>
                        <td>{{ $education['graduatedYear'] }}</td>
                        <td>{{ $education['grade'] }}</td>
                        <td>{{ $education['modeOfStudy'] }}</td>
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ action('EducationController@getEditEducation') }}?id={{ $education['id'] }}" class="dropdown-item">Edit</a>
                                        <a href="{{ action('EducationController@deleteEducation') }}?id={{ $education['id'] }}" class="dropdown-item" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
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
    <!-- Leave scripts section empty for now -->
@endsection
