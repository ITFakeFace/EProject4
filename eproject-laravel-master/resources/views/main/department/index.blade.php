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
        <h1 class="pt-3 pl-3 pr-3">Department List</h1>
        <div class="card-header header-elements-inline">
            <div class="text-left">
                {{-- alert --}}
                @if (\Session::has('success'))
                <div class="">
                    <div class="alert alert-success">
                        {!! \Session::get('success') !!}
                    </div>
                </div>
                @endif

            {{-- @if (session('message'))
                <div class="">
                    <div class="alert alert-primary">
                        {!! session('message') !!}
                    </div>
                </div>
            @endif --}}

                @if($errors->any())
                <div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <p><b>Invalid Input Data:</b></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                {{-- alert --}}
                &nbsp; &nbsp; &nbsp;<button  class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter2">Add New Department <i class="icon-paperplane ml-2"></i></button>
            </div> 
           
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
                    <th>Department Name</th>
                    <th>Department Name (Vietnamese)</th>
                    <th>Action</th>
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
                                Show
                            @else
                                Hide
                            @endif    
                        </td> -->
                
                        <td class="text-center">
                        <div class="list-icons">
                            <div class="dropdown">
                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                    <i class="icon-menu9"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ action('DepartmentController@getEditDep') }}?id={{ $department['id'] }}" class="dropdown-item">Update</a>
                                        <a href="{{ action('DepartmentController@getDeleteDep') }}?id={{ $department['id'] }}" class="dropdown-item" onclick="return confirm('Are you sure?')">Delete</a>
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

     <!-- Modal Add Department -->
     <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{action('DepartmentController@CreateDepartment')}}" method="post">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">ADD NEW</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Department Name</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="txtName"  required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Department Name (Vietnamese)</label>
                                <div class="col-lg-9">
                                <input type="text" class="form-control" name="txtName1"  required>
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

    <!-- /basic datatable -->
@endsection

@section('scripts')
@endsection
