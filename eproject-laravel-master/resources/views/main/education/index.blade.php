@extends('main._layouts.master')

@section('title')
    List of Education Information
@endsection

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Include Bootstrap CSS if not already included -->
    <link href="{{ asset('path/to/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Include FontAwesome CSS if not already included -->
    <link href="{{ asset('path/to/fontawesome.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Include SweetAlert2 CSS -->
    <link href="{{ asset('path/to/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">List of Education Information</h3>
                        {{-- <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                                <a class="list-icons-item" data-action="reload"></a>
                                <a class="list-icons-item" data-action="remove"></a>
                            </div>
                        </div> --}}

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive pt-3">
                                <table id="comment" class="table table-bordered table-hover">
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
                                        @foreach ($data_education as $education)
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
                                                    <div class="dropdown">
                                                        <a href="#" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                                                            <i class="fas fa-bars"></i>
                                                        </a>
                                                        <div class="dropdown-menu">
                                                            <a href="{{ action('EducationController@getEditEducation') }}?id={{ $education['id'] }}" class="dropdown-item">Edit</a>
                                                            <a href="{{ action('EducationController@deleteEducation') }}?id={{ $education['id'] }}" class="dropdown-item" onclick="return confirm('Are you sure?')">Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                {{-- @include('admin.layout.footer') --}}

            </div>
        @endsection

        @section('script-content')
            <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
            <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
            <!-- Include Bootstrap JS if not already included -->
            <script src="{{ asset('path/to/bootstrap.min.js') }}"></script>
            <!-- Include FontAwesome JS if not already included -->
            <script src="{{ asset('path/to/fontawesome.min.js') }}"></script>
            <!-- Include SweetAlert2 JS -->
            <script src="{{ asset('path/to/sweetalert2.min.js') }}"></script>

            <script>
                $(document).ready(function() {
                    $("#comment").DataTable();

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
