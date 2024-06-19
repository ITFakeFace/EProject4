@extends('main._layouts.master')

<?php
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('js')    
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/notifications/jgrowl.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('global_assets/js/demo_pages/picker_date.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Chi Tiết Bổ Sung Công Lễ {{ date('d/m/Y', strtotime($data[0]['day_special_from'])) }} - {{ date('d/m/Y', strtotime($data[0]['day_special_to'])) }} ( {{ $data[0]['note'] }} )</h1>
        <div class="card-body">
            @if (\Session::has('success'))
                <div class="">
                    <div class="alert alert-success">
                        {!! \Session::get('success') !!}
                    </div>
                </div>
            @endif

            @if (\Session::has('error'))
                <div class="">
                    <div class="alert alert-danger">
                        {!! \Session::get('error') !!}
                    </div>
                </div>
            @endif
        </div>

        <table class="table datatable-basic">
            <thead>
            <tr>
                <th>Tên nhân viên</th>
                <th>Phòng ban</th>
                <th>Chức vụ</th>
                <th>Ngày bổ sung</th>
                <th>Công</th>
                <th>Ngày tạo</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $time_special)
                <tr>
                    <td>{{ $time_special['full_name'] }}</td>
                    <td>{{ $time_special['department_name'] }}</td>
                    <td>{{ $time_special['is_manager'] == 1 ? "Quản lý" : "Nhân viên" }}</td>
                    <td>{{ $time_special['day_time_special'] }}</td>
                    <td>{{ $time_special['number_time'] }}</td>
                    <td>{{ $time_special['date_create'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- /basic datatable -->
@endsection

@section('scripts')
    <script>

    </script>
@endsection