@extends('main._layouts.master')

@section('title')
Temporarily Deleted Departments
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
              <h3 class="font-weight-bold">Temporarily Deleted Departments</h3>
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
                            <tr>
                                <th>ID</th>
                                <th>Department Name</th>
                                <th>Department Name (Vietnamese)</th>
                                <th>Action</th>
                            </tr>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach($data_department as $department)
                            <tr>
                                <td>{{ $department['id'] }}</td>
                                <td>{{ $department['name'] }}</td>
                                <td>{{ $department['nameVn'] }}</td>
                                <!-- <td>
                                    @if($department['del'] == 0)
                                        Active
                                    @else
                                        Inactive
                                    @endif    
                                </td> -->
                                <td class="center"><i class="btn-btn-success"></i><a href="{{ action('DepartmentController@getUndoDep') }}?id={{ $department['id'] }}">Undo</a>&nbsp;
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
  <script>
       $(document).ready(function(){
        $("#comment").DataTable();
    });
  </script>
@endsection


