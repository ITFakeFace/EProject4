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
        <h1 class="pt-3 pl-3 pr-3">Danh Sách Hợp Đồng</h1>
        <div class="card-header header-elements-inline">
            
        </div>
        <div class="card-body">
            @if(session('message'))
                <div class="alert alert-{{ session('message')['type'] }} border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    {{ session('message')['message'] }}
                </div>
            @endif
        </div>
        <table class="table datatable-basic">
            <thead>
            <tr>
                <th>Mã nhân viên</th>
                <th>Nhân viên</th>
                <th>Ngày bắt đầu HĐ</th>
                <th>Ngày kết thúc HĐ</th>
                <th>Lương</th>
                <th>Ngày tạo</th>
                <th class="text-center">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->staff->id }}</td>
                    <td>{{ $item->staff->firstname . ' ' . $item->staff->lastname}}</td>
                    <td>{{ $item->startDate }}</td>
                    <td>{{ $item->endDate }}</td>
                    <td>{{ number_format($item->baseSalary) }}</td>
                    <td>{{ $item->createAt }}</td>
                    <td class="text-center">
                        <div class="list-icons">
                            <div class="dropdown">
                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                    <i class="icon-menu9"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{{ route('getDetailContract', ['id' => $item->id]) }}" class="dropdown-item">Chi tiết</a>
                                    <a href="{{ route('exportWord', ['id' => $item->id]) }}" class="dropdown-item">Xuất hợp đồng</a>
                                    @php
                                        $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $item->endDate);
                                        $stopDate = \Carbon\Carbon::createFromFormat('Y-m-d', $item->stopDate);
                                    @endphp
                                    @if($stopDate->eq($endDate))
                                        <a href="javascript:void(0);" onclick="stopContract({{ $item->id }})" class="dropdown-item">Chấm dứt hợp đồng trước kì hạn</a>
                                    @endif
                                    {{--                                    <a href="{{ route('getDeleteContract',['id' => $item->id]) }}" class="dropdown-item">Xóa</a>--}}
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
        function stopContract(id) {
            let conf = confirm('Bạn có chắc muốn chấm dứt hợp đồng này?');
            if (conf) {
                window.location.href = '{{ route('stopContractContract') }}/' + id;
            }
        }
    </script>
@endsection
