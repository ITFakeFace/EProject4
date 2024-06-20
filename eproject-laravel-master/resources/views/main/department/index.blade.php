@extends('main._layouts.master')

@section('title')
    Department List
@endsection

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Include Bootstrap CSS if not already included -->
    <link href="{{ asset('path/to/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Include FontAwesome CSS if not already included -->
    <link href="{{ asset('path/to/fontawesome.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')

  <div class="content-wrapper">
      <div class="row">
        <div class="col-md-12 grid-margin">
          <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
              <h3 class="font-weight-bold">Department List</h3>
              {{-- <h6 class="font-weight-normal mb-0">Update Admin Password</h6> --}}
              <div>
                <div class="text-left">
                    {{-- alert --}}
                    @if($errors->any())
                    <div class="alert alert-danger border-0 alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <p><b>Input data is incorrect:</b></p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    {{-- alert --}}
                    <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter2">Add New Department<i class="icon-paperplane ml-2"></i></button>
                </div> 
               
                <div class="header-elements">
                 
                </div>
            </div>
            </div>
            
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
                            <th>Department Name</th>
                            <th>Department Name (Vietnamese)</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach($data_department as $department)
                            <tr>
                                <td>{{ $department['id'] }}</td>
                                <td>{{ $department['name'] }}</td>
                                <td>{{ $department['nameVn'] }}</td>
                        
                                <td class="text-center">
                                    <div class="dropdown">
                                        <a href="#" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                                            <i class="fas fa-bars"></i>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="{{ action('DepartmentController@getEditDep', ['id' => $department['id']]) }}" class="dropdown-item">Update</a>
                                            <a href="{{ action('DepartmentController@getDeleteDep', ['id' => $department['id']]) }}" class="dropdown-item" onclick="return confirm('Are you sure?')">Delete</a>
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

  <!-- Modal Add Department -->
  <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <form action="{{ action('DepartmentController@CreateDepartment') }}" method="post">
                  @csrf
                  <div class="modal-header">
                      <h5 class="modal-title btn btn-primary" id="exampleModalLongTitle">Add New Department</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="form-group row">
                          <label class="col-lg-6 col-form-label">Department Name:</label>
                          <div class="col-lg-9">
                              <input type="text" class="form-control" name="txtName" required>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-lg-6 col-form-label">Department Name (Vietnamese):</label>
                          <div class="col-lg-9">
                              <input type="text" class="form-control" name="txtName1" required>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Add New</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
  <!-- /Modal Add Department -->

@endsection

@section('script-content')
  <script>
        $(document).ready(function(){
            $("#comment").DataTable();

            // Handle delete confirmation
            $('.delete-department').on('click', function(e) {
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
    <!-- Include SweetAlert JS -->
    <script src="{{ asset('path/to/sweetalert2.min.js') }}"></script>
@endsection
