@extends('main._layouts.master')

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('js')
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
@endsection

@section('content')
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Payroll List</h1>
        <div class="card-header header-elements-inline">

        </div>
        <div class="card-body">
            @if (session('message'))
                <div class="alert alert-{{ session('message')['type'] }} border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    {{ session('message')['message'] }}
                </div>
            @endif
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <td>Id</td>
                    <th>Start</th>
                    <th>End</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestampMs($item->fromDate)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestampMs($item->toDate)->format('d/m/Y') }}</td>
                        <td>{{ $item->status == 'pending' ? 'Chưa khóa' : 'Đã khóa' }}</td>
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('getDetailSalary', ['id' => $item->id]) }}" class="dropdown-item">Details</a>
                                        @if ($item->status == 'pending')
                                            <a href="javascript:void(0)" onclick="deleteSalary({{ $item->id }})" class="dropdown-item">Delete Payroll</a>
                                            <a href="javascript:void(0)" onclick="setSuccessSalary({{ $item->id }})" class="dropdown-item">Complete Payroll</a>
                                        @endif
                                        <a href="{{ route('exportPayroll', ['id' => $item->id]) }}" class="dropdown-item">Export Payroll</a>
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
    <script>
        function deleteSalary(id) {
            let conf = confirm('Are you sure to delete salary ID: ' + id);
            if (conf) {
                window.location.href = '{{ route('getDeleteSalary') }}/' + id;
            }
        }

        function setSuccessSalary(id) {
            let conf = confirm('Are you sure to complete salary ID: ' + id);
            if (conf) {
                window.location.href = '{{ route('getChangeStatusSuccessSalary') }}/' + id;
            }
        }
    </script>
@endsection
