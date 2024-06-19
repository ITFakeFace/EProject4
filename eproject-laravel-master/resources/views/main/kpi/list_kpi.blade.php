@extends('main._layouts.master')

<?php
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        #tb_department_wrapper {
            display: none;
        }
    </style>
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
        <h1 class="pt-3 pl-3 pr-3">Duyệt KPI</h1>
        <div class="card-header header-elements-inline">
            <h4 class="card-title font-weight-bold text-uppercase">
                <?php echo auth()->user()->firstname . " " . auth()->user()->lastname ?> 
                - <?php echo $staff[0][2] ?> 
                - <?php echo auth()->user()->is_manager == 1 ? 'Quản lý' : 'Nhân viên' ?>
            </h4>
        </div>
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
            <form action="{{ action('TimeleaveController@approveTimeLeave') }}" method="GET">
                @csrf
                {{-- <div class="form-group d-flex">
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
                        <input class="form-control btn btn-primary" type="submit" value="Search">
                    </div>
                </div> --}}
            </form>

             <ul class="nav nav-tabs">
                <li class="nav-item">
                  <button class="nav-link active" id="btn_tb_staff">Kpi Nhân Viên</button>
                <li class="nav-item">
                  <button class="nav-link" id="btn_tb_department">Kpi Phòng Ban</button>
                </li>
            </ul>
        </div>

        <table class="table datatable-basic" id="tb_staff">
            <thead>
                <tr>
                    <th>Tên nhân viên</th>
                    <th>Mã nhân viên</th>
                    <th>Phòng ban</th>
                    <td>Chức danh</td>
                    <th>Tên KPI</th>
                    <th>Thời gian tạo</th>
                    <th>Trạng thái</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data_staff as $kpi)
                    <tr>
                        <td>{{ $kpi['firstname'] . ' ' . $kpi['lastname'] }}</td>
                        <td>{{ $kpi['code'] }}</td>
                        <td>{{ $kpi['staff_department'] }}</td>
                        <td>{{ $kpi['is_manager'] == false ? 'Nhân viên' : 'Quản lý' }}</td>
                        <td>{{ $kpi['kpi_name'] }}</td>
                        <td style="min-width: 160px !important;">{{ $kpi['created_at'] }}</td>
                        <td>
                            <?php 
                                if($kpi['is_approved'] == 0)
                                    echo '<span class="badge badge-warning">Chưa phê duyệt</span>';
                                if($kpi['is_approved'] == 2)
                                    echo '<span class="badge badge-primary">Quản lý đã phê duyệt</span>';
                                if($kpi['is_approved'] == 1)
                                    echo '<span class="badge badge-success">HR đã phê duyệt</span>';
                                if($kpi['is_approved'] == 3)
                                    echo '<span class="badge badge-danger">Đã từ chối</span>';
                            ?>
                        </td>
                        <td>
                            <a href="../kpi/set-detail-kpi?kpi_id={{ $kpi['id'] }}&staff_id={{ $kpi['staff_id'] }}&kpi_name={{ $kpi['kpi_name'] }}&readonly=1&go_approve=1" class="btn btn-info" style="color: white; cursor: pointer;">Chi tiết</a>
                        </td>
                    </tr>                        
                @endforeach       
            </tbody>
        </table>

        <table class="table datatable-basic" id="tb_department" style="display: none">
             <thead>
                <tr>
                    <th>Phòng ban</th>
                    <th>Phòng ban (Vie)</th>
                    <th>Tên KPI</th>
                    <th>Thời gian tạo</th>
                    <th>Trạng thái</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data_department as $kpi)
                   <tr>
                        <td>{{ $kpi['department_name'] }}</td>
                        <td>{{ $kpi['department_name_vn'] }}</td>
                        <td>{{ $kpi['kpi_name'] }}</td>
                        <td style="min-width: 160px !important;">{{ $kpi['created_at'] }}</td>
                        <td>
                            <?php 
                                if($kpi['is_approved'] == 0)
                                    echo '<span class="badge badge-warning">Chưa phê duyệt</span>';
                                if($kpi['is_approved'] == 2)
                                    echo '<span class="badge badge-primary">Quản lý đã phê duyệt</span>';
                                if($kpi['is_approved'] == 1)
                                    echo '<span class="badge badge-success">HR đã phê duyệt</span>';
                                if($kpi['is_approved'] == 3)
                                    echo '<span class="badge badge-danger">Đã từ chối</span>';
                            ?>
                        </td>
                        <td>
                            <a href="../kpi/set-detail-kpi?kpi_id={{ $kpi['id'] }}&department_id={{ $kpi['department_id'] }}&kpi_name={{ $kpi['kpi_name'] }}&readonly=1&go_approve=1" class="btn btn-info" style="color: white; cursor: pointer;">Chi tiết</a>
                        </td>
                    </tr>
                @endforeach       
            </tbody>
        </table>

        <div id="bsc-modal" class="modal fade" role="dialog"> <!-- modal bsc -->
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="{{ action('TimeleaveController@approvedTimeLeave') }}" method="post" class="form-horizontal">
                    @csrf
                    <div id="html_pending">
                        
                    </div>
                </form> <!-- end form -->
              </div>
            </div>
        </div> <!-- end modal bsc -->
          
    </div>
    <!-- /basic datatable -->
@endsection

@section('scripts')
    <script>

        $( "#btn_tb_staff" ).click(function() {
            $('#tb_department').hide();
            $('#tb_department_wrapper').hide();
            $('#tb_staff').show();
            $('#tb_staff_wrapper').show();
            $(this).addClass('active');
            $('#btn_tb_department').removeClass('active');
        });

        $( "#btn_tb_department" ).click(function() {
            $('#tb_staff').hide();
            $('#tb_staff_wrapper').hide();
            $('#tb_department').show();
            $('#tb_department_wrapper').show();
            $(this).addClass('active');
            $('#btn_tb_staff').removeClass('active');
        });

        $('.open-detail-time-leave').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('TimeleaveController@detailStaffApprove') }}',
                Type: 'GET',
                datatype: 'html',
                data:
                {
                    id: id,
                },
                cache: false,
                success: function (data)
                {
                    $('#html_pending').empty().append(data);
                    $('#bsc-modal').modal();
                },
                error: (error) => {
                    console.log(JSON.stringify(error));
                }
            });
        });

    </script>
@endsection