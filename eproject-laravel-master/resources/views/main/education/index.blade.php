@extends('main._layouts.master')

@section('title')
    List of Education Information
@endsection

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <div class="card-header header-elements-inline">
            <h1 class="pt-3 pl-3 pr-3">List of Educational Qualifications</h1>
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
                @if (\Session::has('success'))
                    <div class="">
                        <div class="alert alert-success">
                            {!! \Session::get('success') !!}
                        </div>
                    </div>
                @endif
    
                @if (\Session::has('message'))
                    @php
                        $message = \Session::get('message');
                    @endphp
                    <div class="alert alert-{{ $message['type'] }}">
                        {{ $message['message'] }}
                    </div>
                @endif
            </form>
        </div>

        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Level</th>
                    <th>Level Name</th>
                    <th>School Name</th>
                    <th>Field of Study</th>
                    <th>Graduation Year</th>
                    <th>Grade</th>
                    <th>Study Mode</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data_education as $education)
                    <tr>
                        <td>{{ $education['id'] }}</td>
                        @foreach ($data_staff as $staff)
                            @if ($education['staffId'] == $staff['id'])
                                <td>{{ $staff['firstname'] }} {{ $staff['lastname'] }}</td>
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
@section('Script')
                    // Handle delete confirmation
                    $('.delete-education').on('click', function(e) {
                        e.preventDefault();
                        var deleteUrl = $(this).attr('href');

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = deleteUrl;
                            }
                        });
                    });
                });
            </script>
        @endsection

        @section('js')
            <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
            <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
            <!-- Include Bootstrap JS if not already included -->
            <script src="{{ asset('path/to/bootstrap.min.js') }}"></script>
            <!-- Include FontAwesome JS if not already included -->
            <script src="{{ asset('path/to/fontawesome.min.js') }}"></script>
            <!-- Include SweetAlert2 JS -->
            <script src="{{ asset('path/to/sweetalert2.min.js') }}"></script>
        @endsection
