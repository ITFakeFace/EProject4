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
        <h1 class="pt-3 pl-3 pr-3">Detail of Special Date Supplement: {{ $data[0]['note'] }}</h1>
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
                <th>Employee Name</th>
                <th>Department</th>
                <th>Position</th>
                <th>Supplement Date</th>
                <th>Supplement leave days</th>
                <th>Creation Date</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $time_special)
                <tr>
                    <td>{{ $time_special['full_name'] }}</td>
                    <td>{{ $time_special['department_name'] }}</td>
                    <td>{{ $time_special['is_manager'] == 1 ? "Manager" : "Employee" }}</td>
                    <td>{{Carbon\Carbon::createFromTimestampMs($time_special['day_time_special'])->format('Y-m-d')}}</td>
                    <td>{{ $time_special['number_time'] }}</td>
                    <td>{{ Carbon\Carbon::createFromTimestampMs($time_special['date_create'])->format('Y-m-d') }}</td>
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