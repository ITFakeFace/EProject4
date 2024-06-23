@extends('main._layouts.master')

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
        <h1 class="pt-3 pl-3 pr-3">Employee List</h1>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Joining Date</th>
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
                                <td>{{ $department['nameVn'] }}</td>
                            @endif
                        @endforeach
                        <td>
                            @if ($staff['isManager'] == 0)
                                Employee
                            @else
                                Manager
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($staff['joinedAt'] / 1000)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($staff['dob'] / 1000)->format('d/m/Y') }}</td>
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
                                        <a href="{{ action('StaffController@getEditStaff') }}?id={{ $staff['id'] }}" class="dropdown-item">Update</a>
                                        <a href="{{ action('StaffController@getDetail') }}?id={{ $staff['id'] }}" class="dropdown-item">Detail</a>
                                        <a href="{{ route('exportWord1', ['id' => $staff['id']]) }}" class="dropdown-item">Export Employee File</a>
                                        <a href="{{ action('StaffController@getDeleteStaff') }}?id={{ $staff['id'] }}" class="dropdown-item" onclick="return confirm('Are you sure?')">Delete</a>
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
