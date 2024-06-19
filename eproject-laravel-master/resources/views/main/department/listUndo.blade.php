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
        <h1 class="pt-3 pl-3 pr-3">Phòng Ban Tạm Xóa</h1>
        <div class="card-header header-elements-inline">
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
                    <td class="center"><i class="btn-btn-success"></i><a href="{{ action('DepartmentController@getUndoDep') }}?id={{ $department['id'] }}">Hoàn tác</a>&nbsp;
                    </tr>
                    @endforeach
            </tbody>
        </table>
    </div>
    <!-- /basic datatable -->

@endsection

@section('scripts')
@endsection