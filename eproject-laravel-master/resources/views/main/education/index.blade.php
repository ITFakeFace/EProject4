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
      
        <div class="card-header header-elements-inline">
        <h1 class="pt-3 pl-3 pr-3">Danh Sách Thông Tin Bằng Cấp</h1>
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

            </form>
        </div>

        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Mã ID</th>
                    <th>Họ tên Nhân viên</th>
                    <th>Bậc</th>
                    <th>Hạng Bậc</th>
                    <th>Tên Trường</th>
                    <th>Chuyên Ngành</th>
                    <th>Năm học</th>
                    <th>Xếp loại</th>
                    <th>Phương thức</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                    @foreach($data_education as $education)
                    <tr>
                        <td>{{ $education['id'] }}</td>
                        @foreach ($data_staff as $staff)
                                    @if ($education['staffId'] == $staff['id'])
                                        <td>{{$staff['firstname']}} {{$staff['lastname']}} </td>
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
                                        <a href="{{ action('EducationController@getEditEducation') }}?id={{ $education['id'] }}" class="dropdown-item">Cập nhật</a>
                                        <a href="{{ action('EducationController@deleteEducation') }}?id={{ $education['id'] }}" class="dropdown-item" onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</a>
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