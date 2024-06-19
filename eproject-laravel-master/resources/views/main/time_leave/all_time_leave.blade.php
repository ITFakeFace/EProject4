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
        <h1 class="pt-3 pl-3 pr-3">Tổng Hợp Công Phép</h1>
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
            <form action="{{ action('TimeleaveController@getAllTimeLeave') }}" method="GET">
                @csrf
                <div class="form-group d-flex">
                    <div class="">
                        <select class="form-control" name="month" id="month">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" <?php echo $month == $i ? 'selected' : ''?>>Tháng {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="ml-2">
                        <input class="form-control" type="number" value="<?php echo $year ?>" name="year" id="year">
                    </div>
                    <div class="ml-3">
                        <input class="form-control btn btn-primary" type="submit" value="Tìm kiếm">
                    </div>
                </div>
            </form>
            <div class="export">
                <a href ="{{ action('ExportController@exportTimeLeave') }}?y_m={{ $y_m }}" class="btn btn-success export" id="export-button"> Xuất Excel </a>
           </div>
        </div>

        <table class="table datatable-basic">
            <thead>
            <tr>
                <th>Tên nhân viên</th>
                <th>Phòng ban</th>
                <th>Chức vụ</th>
                <th>Bổ sung công / Đăng kí phép</th>
                <th>Bổ sung công</th>
                <th>Bổ sung công đã duyệt</th>
                <th style="background-color: #ffffe7">Số công bổ sung đã duyệt</th>
                <th>Đăng kí phép</th>
                <th>Đăng kí phép đã duyệt</th>
                <th style="background-color: #ffffe7">Số công đăng kí phép đã duyệt</th>
             
                <th>Chi tiết</th>
            </tr>
            </thead>
            <tbody>
            @foreach($summary as $item)
                <tr>
                    <td>{{ $item['full_name'] }}</td>
                    <td>{{ $item['department_name'] }}</td>
                    <td>{{ $item['is_manager'] == 1 ? "Quản lý" : "Nhân viên" }}</td>
                    <td>{{ $item['number_time_leave'] }}</td>
                    <td>{{ $item['total_number_time'] }}</td>
                    <td>{{ $item['number_time_approved'] }}</td>
                    <td style="background-color: #ffffe7">{{ $item['number_time_time_approved'] }}</td>
                    <td>{{ $item['total_number_leave'] }}</td>
                    <td>{{ $item['number_leave_approved'] }}</td>
                    <td style="background-color: #ffffe7">{{ $item['number_time_leave_approved'] }}</td>
 
                    <td><button id="{{ $item['staff_id'] }}" class="btn btn-primary open-detail">Chi tiết</button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- /basic datatable -->

    <!-- Full width modal -->
    <div id="modalDetail" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi Tiết </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <table class="table datatable-detail">
                        <thead>
                            <tr>
                                <th>Tên nhân viên</th>
                                <th>Phòng ban</th>
                                <th>Chức vụ</th>
                                <th>Ngày </th>
                                <th>Thứ </th>
                                <th>Loại </th>
                                <th>Ngày công</th>
                                <th>Ngày công được tính</th>
                                <th>Phê duyệt</th>
                            </tr>
                        </thead>
                        <tbody id="detail">
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /full width modal -->
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('.open-detail').click(function() {
                var staff_id = $(this).attr('id');
                var month = <?php echo $month ?>;
                var year = <?php echo $year ?>;

                $.ajax({
                    url: '{{ action('TimeleaveController@getDetailTimeLeave') }}',
                    Type: 'POST',
                    datatype: 'text',
                    data:
                    {
                        staff_id: staff_id,
                        month: month,
                        year: year
                    },
                    cache: false,
                    success: function (data)
                    {
                        $('#detail').empty().append(data);
                        $('#modalDetail').modal();
                    }
                });
            });
        });

    </script>
@endsection