@extends('main._layouts.master')

<?php
    // {{ }} <--- cac ky tu dac biet se duoc thay the
    // {!! !!} <--- cac ky tu dac biet se khong thay the
    // {{-- --}} <--- comment code trong blade
    /**
     * section('scripts') <--- coi o? master.blade.php <--- no' la @yield('scripts')
     * section co' mo? la phai co' dong'
     * neu ma soan code php thi nen de? tren dau` de? no' load tuan tu chinh xac hon giong nhu code php nam tren section('scripts') vay ok roi
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
        <h1 class="pt-3 pl-3 pr-3">Danh Sách Phòng Ban</h1>
        <div class="card-header header-elements-inline">
            <div class="text-left">
                {{-- alert --}}
                @if($errors->any())
                <div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <p><b>Dữ liệu đầu vào không chính xác:</b></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                {{-- alert --}}
                &nbsp; &nbsp; &nbsp;<button  class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter2">Thêm Mới Phòng Ban <i class="icon-paperplane ml-2"></i></button>
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
                    <th>Mã ID</th>
                    <th>Tên Phòng Ban</th>
                    <th>Tên Tiếng Việt</th>
                    <th>Thao tác</th>
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
                                Hiện
                            @else
                                Ẩn
                            @endif    
                        </td> -->
                
                        <td class="text-center">
                        <div class="list-icons">
                            <div class="dropdown">
                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                    <i class="icon-menu9"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ action('DepartmentController@getEditDep') }}?id={{ $department['id'] }}" class="dropdown-item">Cập nhật</a>
                                        <a href="{{ action('DepartmentController@getDeleteDep') }}?id={{ $department['id'] }}" class="dropdown-item">Xóa</a>
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

     <!-- Modal Add Deparment -->
     <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{action('DepartmentController@CreateDepartment')}}" method="post">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">THÊM MỚI</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Tên Phòng Ban</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="txtName"  required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Tên Phòng Ban Tiếng Việt</label>
                                <div class="col-lg-9">
                                <input type="text" class="form-control" name="txtName1"  required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Thêm mới</button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>

    <!-- /basic datatable -->
@endsection

@section('scripts')
@endsection