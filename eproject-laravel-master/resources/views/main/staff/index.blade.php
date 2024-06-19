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
        <h1 class="pt-3 pl-3 pr-3">Danh Sách Nhân Viên</h1>
        <div class="card-header header-elements-inline">
           
        </div>
        <div class="card-body">
            <form action="#" method="GET">

            </form>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã</th>
                    <th>Tên</th>
                    <th>Họ</th>
                    <th>Phòng Ban</th>
                    <th>Chức vụ</th>
                    <th>Ngày vào</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>          
                    @foreach($data_staff as $staff)
                    <tr>
                        <td>{{ $staff['id'] }}</td>
                        <td>{{ $staff['code'] }}</td>
                        <td>{{ $staff['firstname'] }}</td>
                        <td>{{ $staff['lastname'] }}</td>
                        @foreach ($data_department as $department)
                                    @if ($staff['department'] == $department['id'])
                                        <td>{{$department['nameVn']}}</td>
                                    @endif
                        @endforeach
                        <td>@if($staff['isManager'] == 0)
                                Nhân viên
                            @else
                                Quản lý
                            @endif 
                        </td>
                        <td>{{ $staff['joinedAt'] }}</td>
                        <td>{{ $staff['dob'] }}</td>
                        <td>@if($staff['gender'] == 1)
                                Nam
                            @else
                                Nữ
                            @endif 
                        </td>
                        <td>
                        <div class="list-icons">
                            <div class="dropdown">
                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                    <i class="icon-menu9"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ action('StaffController@getEditStaff') }}?id={{ $staff['id'] }}" class="dropdown-item">Cập nhật</a>
                                        <a href="{{ action('StaffController@getDetail') }}?id={{ $staff['id'] }}" class="dropdown-item">Chi tiết</a>
                                        <a href="{{ route('exportWord1', ['id' => $staff['id']]) }}" class="dropdown-item">Xuất file nhân viên</a>
                                        <a href="{{ action('StaffController@getDeleteStaff') }}?id={{ $staff['id'] }}" class="dropdown-item">Xóa</a>
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