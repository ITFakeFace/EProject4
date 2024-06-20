@extends('main._layouts.master')

<?php
    // {{ }} <--- special characters will be replaced
    // {!! !!} <--- special characters will not be replaced
    // {{-- --}} <--- Blade comment
    /**
     * section('scripts') <--- check in master.blade.php <--- it's @yield('scripts')
     * section must have an opening line
     * if you write PHP code, put it on top for accurate loading, similar to PHP code in section('scripts') okay
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
        <h1 class="pt-3 pl-3 pr-3">Temporarily Deleted Departments</h1>
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
    <!-- /basic datatable -->

@endsection

@section('scripts')
@endsection
