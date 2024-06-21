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
              <h3 class="font-weight-bold">Contract List</h3>
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
                    <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter2">Add New Contract<i class="icon-paperplane ml-2"></i></button>
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
                        <table id="comment" class="table datatable-basic">
                            <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Salary</th>
                                <th>Created At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->staff->id }}</td>
                                    <td>{{ $item->staff->firstname . ' ' . $item->staff->lastname}}</td>
                                    <td>{{ $item->startDate }}</td>
                                    <td>{{ $item->endDate }}</td>
                                    <td>{{ number_format($item->baseSalary) }}</td>
                                    <td>{{ $item->createAt }}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                                                <i class="fas fa-bars"></i>
                                            </a>
                                            <div class="dropdown-menu">
                                                <a href="{{ route('getDetailContract', ['id' => $item->id]) }}" class="dropdown-item">Details</a>
                                                <a href="{{ route('exportWord', ['id' => $item->id]) }}" class="dropdown-item" onclick="return confirm('Are you sure?')">Export Contract</a>
                                                @php
                                                    $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $item->endDate);
                                                    $stopDate = \Carbon\Carbon::createFromFormat('Y-m-d', $item->stopDate);
                                                @endphp
                                                @if($stopDate->eq($endDate))
                                                    <a href="javascript:void(0);" onclick="stopContract({{ $item->id }})" class="dropdown-item" onclick="return confirm('Are you sure?')">Terminate Contract Early</a>
                                                    @endif
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
  @section('scripts')
    <script>
        function stopContract(id) {
            let conf = confirm('Are you sure you want to terminate this contract?');
            if (conf) {
                window.location.href = '{{ route('stopContractContract') }}/' + id;
            }
        }
    </script>
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


