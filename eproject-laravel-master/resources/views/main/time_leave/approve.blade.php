@extends('main._layouts.master')

<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        #tb_dkp_wrapper,
        #tb_leave_other_wrapper {
            display: none;
        }

        .swal2-icon.swal2-warning:before {
            display: none;
        }

        .swal2-icon.swal2-warning {
            font-size: 2rem !important;
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
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        @if (auth()->user()->department == 2 && auth()->user()->is_manager == 1)
            <h1 class="pt-3 pl-3 pr-3">Approve Leave and Attendance</h1>
        @elseif(auth()->user()->department == 2)
            <h1 class="pt-3 pl-3 pr-3">View Leave and Attendance</h1>
        @else
            <h1 class="pt-3 pl-3 pr-3">Approve Leave and Attendance</h1>
        @endif

        <div class="card-header header-elements-inline">
            <h4 class="card-title font-weight-bold text-uppercase">
                <?php echo auth()->user()->firstname . ' ' . auth()->user()->lastname; ?>
                - <?php echo $staff[0][2]; ?>
                - <?php echo auth()->user()->is_manager == 1 ? 'Manager' : 'Staff'; ?>
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
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group d-flex">
                            <div class="">
                                <select class="form-control" name="month" id="month">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" <?php echo $month == $i ? 'selected' : ''; ?>>Month {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="ml-2">
                                <input class="form-control" type="number" value="<?php echo $year; ?>" name="year" id="year">
                            </div>
                            <div class="ml-3">
                                <input class="form-control btn btn-primary" type="submit" value="Search">
                            </div>
                        </div>
                    </div>

                    @if (auth()->user()->id == 7)
                        <div class="col-12 col-md-6">
                            <div class="float-right">
                                <input type="button" class="form-control btn btn-danger" data-toggle="modal" data-target="#exampleModalCenter" value="Close Leave and Attendance">
                            </div>
                        </div>
                    @endif
                </div>
            </form>

            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <button class="nav-link active" id="btn_tb_bsc" style="border: 1px solid gainsboro;">Additional Attendance</button>
                <li class="nav-item">
                    <button class="nav-link" id="btn_tb_dkp" style="border: 1px solid gainsboro;">Paid Annual Leave</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="btn_leave_other" style="border: 1px solid gainsboro;">Other Leave Requests</button>
                </li>
            </ul>
        </div>

        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="form-done-leave" action="{{ action('TimeleaveController@doneLeave') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Close Leave and Attendance</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">From Date:</label>
                                <div class="col-lg-9">
                                    <input id="from_date" type="text" class="form-control day_leave" name="from_date" value="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">To Date:</label>
                                <div class="col-lg-9">
                                    <input id="to_date" type="text" class="form-control day_leave" name="to_date" value="" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger" onclick="doneLeave()">Close Leave and Attendance</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <table class="table datatable-basic" id="tb_bsc">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Date</th>
                    <th>Recorded Hours</th>
                    <th>Calculated Hours</th>
                    <th>Type</th>
                    <th>Approval</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $time_leave)
                    @if ($time_leave['type'] == 0)
                        <tr style="
                                <?php
                                if ($time_leave['special_date_id'] !== null) {
                                    echo 'background-color: #ffe7e7';
                                } elseif ($time_leave['day_of_week'] == 1 or $time_leave['day_of_week'] == 7) {
                                    echo 'background-color: #d3ffd4';
                                }
                                ?>
                            ">
                            <td>{{ $time_leave['firstname'] . ' ' . $time_leave['lastname'] }}</td>
                            <td>{{ $time_leave['name_vn'] }}</td>
                            <td>{{ $time_leave['is_manager'] == 1 ? 'Manager' : 'Staff' }}</td>
                            <td>
                                {{ \Carbon\Carbon::createFromTimestampMs($time_leave['day_time_leave'])->format('d/m/Y') }}
                                <?php
                                if ($time_leave['day_of_week'] == 1) {
                                    echo 'Sunday';
                                } else {
                                    echo 'Day ' . $time_leave['day_of_week'];
                                }
                                ?>
                                <?php
                                if ($time_leave['special_date_id'] !== null) {
                                    echo '(Holiday)';
                                }
                                ?>
                            </td>
                            <td><?php echo $time_leave['time'] == '08:00:00' ? '1' : '0.5'; ?></td>
                            <td><?php echo $time_leave['time'] == '08:00:00' ? 1 * $time_leave['multiply'] . '' : 0.5 * $time_leave['multiply'] . ''; ?></td>
                            <td>
                                Additional Attendance
                            </td>
                            <td>
                                @if ($time_leave['is_approved'] == 0)
                                    <span class="badge badge-warning">Not Approved</span>
                                @elseif($time_leave['is_approved'] == 2)
                                    <span class="badge badge-success">Manager Approved</span>
                                @else
                                    <span class="badge badge-primary">Director Approved</span>
                                @endif
                            </td>
                            <td>
                                <?php
                                $date1 = \Carbon\Carbon::createFromTimestampMs($time_leave['created_at']);
                                $date2 = \Carbon\Carbon::today();
                                $diff = date_diff($date1, $date2);
                                ?>
                                @if ($time_leave['done'] == 1)
                                    <span class="badge badge-danger">Closed</span>
                                @elseif($time_leave['is_approved'] == 1)
                                    Director Approved
                                @elseif($time_leave['is_approved'] == 2 && auth()->user()->id !== 7)
                                    @if ($diff->format('%a') > 1)
                                        <div class="from-group d-flex">
                                            More than 2 days since additional attendance
                                        </div>
                                    @else
                                        Awaiting Director Approval
                                    @endif
                                @elseif(auth()->user()->id == 7 || (auth()->user()->is_manager == 1 && auth()->user()->department != 2) || (auth()->user()->is_manager == 1 && auth()->user()->department == 2 && $time_leave['department_id'] == 2))
                                    @if ($diff->format('%a') > 1)
                                        <div class="from-group d-flex">
                                            More than 2 days since additional attendance
                                        </div>
                                    @else
                                        <div class="from-group d-flex">
                                            <a class="btn btn-info open-detail-time-leave" id="{{ $time_leave['id'] }}" style="color: white; cursor: pointer;">Details</a>
                                        </div>
                                    @endif
                                @else
                                    @if ($diff->format('%a') > 1)
                                        <div class="from-group d-flex">
                                            More than 2 days since additional attendance
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <table class="table datatable-basic" id="tb_dkp" style="display: none">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Date</th>
                    <th>Recorded Hours</th>
                    <th>Type</th>
                    <th>Approval</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $time_leave)
                    @if ($time_leave['type'] == 1)
                        <tr>
                            <td>{{ $time_leave['firstname'] . ' ' . $time_leave['lastname'] }}</td>
                            <td>{{ $time_leave['name_vn'] }}</td>
                            <td>{{ $time_leave['is_manager'] == 1 ? 'Manager' : 'Staff' }}</td>
                            <td>
                                {{ $time_leave['day_time_leave'] }}
                                <?php
                                if ($time_leave['day_of_week'] == 1) {
                                    echo 'Sunday';
                                } else {
                                    echo 'Day ' . $time_leave['day_of_week'];
                                }
                                ?>
                                <?php
                                if ($time_leave['special_date_id'] !== null) {
                                    echo '(Holiday)';
                                }
                                ?>
                            </td>
                            <td><?php echo $time_leave['time'] == '08:00:00' ? '1 day' : '0.5 day'; ?></td>
                            <td>
                                Paid Annual Leave
                            </td>
                            <td>
                                @if ($time_leave['is_approved'] == 0)
                                    <span class="badge badge-warning">Not Approved</span>
                                @elseif($time_leave['is_approved'] == 2)
                                    <span class="badge badge-success">Manager Approved</span>
                                @else
                                    <span class="badge badge-primary">Director Approved</span>
                                @endif
                            </td>
                            <td>
                                @if ($time_leave['done'] == 1)
                                    <span class="badge badge-danger">Closed</span>
                                @elseif($time_leave['is_approved'] == 1)
                                    Director Approved
                                @elseif($time_leave['is_approved'] == 2 && auth()->user()->id !== 7)
                                    Awaiting Director Approval
                                @elseif(auth()->user()->id == 7 || (auth()->user()->is_manager == 1 && auth()->user()->department != 2) || (auth()->user()->is_manager == 1 && auth()->user()->department == 2 && $time_leave['department_id'] == 2))
                                    <div class="from-group d-flex">
                                        <a class="btn btn-info open-detail-time-leave" id="{{ $time_leave['id'] }}" style="color: white; cursor: pointer;">Details</a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <table class="table datatable-basic" id="tb_leave_other" style="display: none">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Leave Type</th>
                    <th>Approval</th>
                    <th>Action</th>
                </tr>

            </thead>
            <tbody>
                @foreach ($leave_other as $item)
                    <tr>
                        <td>{{ $item['firstname'] . ' ' . $item['lastname'] }}</td>
                        <td>{{ $item['name_vn'] }}</td>
                        <td>{{ $item['is_manager'] == 1 ? 'Manager' : 'Staff' }}</td>
                        <td>{{ $item['from_date'] }}</td>
                        <td>{{ $item['to_date'] }}</td>
                        <td>
                            <?php
                            if ($item['type_leave'] == 2) {
                                echo 'Unpaid Leave';
                            } elseif ($item['type_leave'] == 3) {
                                echo 'Short-term Sick Leave';
                            } elseif ($item['type_leave'] == 4) {
                                echo 'Long-term Sick Leave';
                            } elseif ($item['type_leave'] == 5) {
                                echo 'Maternity Leave';
                            } elseif ($item['type_leave'] == 6) {
                                echo 'Marriage Leave';
                            } elseif ($item['type_leave'] == 7) {
                                echo 'Bereavement Leave';
                            }
                            ?>
                        </td>
                        <td>
                            @if ($item['is_approved'] == 0)
                                <span class="badge badge-warning">Not Approved</span>
                            @elseif($item['is_approved'] == 2)
                                <span class="badge badge-success">Manager Approved</span>
                            @else
                                <span class="badge badge-primary">Director Approved</span>
                            @endif
                        </td>
                        <td>
                            @if ($item['done'] == 1)
                                <span class="badge badge-danger">Closed</span>
                            @elseif($item['is_approved'] == 1)
                                Director Approved
                            @elseif($item['is_approved'] == 2 && auth()->user()->id !== 7)
                                Awaiting Director Approval
                            @elseif(auth()->user()->id == 7 || (auth()->user()->is_manager == 1 && auth()->user()->department != 2) || (auth()->user()->is_manager == 1 && auth()->user()->department == 2 && $item['department_id'] == 2))
                                <div class="from-group d-flex">
                                    <a class="btn btn-info open-detail-leave-other" id="{{ $item['id'] }}" style="color: white; cursor: pointer;">Details</a>
                                </div>
                            @endif
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

        <div id="other-leave-modal" class="modal fade" role="dialog"> <!-- modal bsc -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ action('TimeleaveController@approvedLeaveOther') }}" method="post" class="form-horizontal">
                        @csrf
                        <div id="html_pending2">

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
        function doneLeave(row_number) {
            let from_date = document.getElementById("from_date").value;
            let to_date = document.getElementById("to_date").value;

            console.log(year);
            Swal.fire({
                title: 'Are you sure you want to close leave and attendance from ' + from_date + ' to ' + to_date + '?',
                text: "Leave and attendance after closing cannot be adjusted. At the same time, return leave for employees' paid leave requests that have not been approved!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#form-done-leave").submit();
                }
            });
        }
        $('.day_leave').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $("#btn_tb_bsc").click(function() {
            $('#tb_dkp').hide();
            $('#tb_dkp_wrapper').hide();
            $('#tb_leave_other').hide();
            $('#tb_leave_other_wrapper').hide();
            $('#tb_bsc').show();
            $('#tb_bsc_wrapper').show();
            $(this).addClass('active');
            $('#btn_tb_dkp').removeClass('active');
            $('#btn_leave_other').removeClass('active');
        });

        $("#btn_tb_dkp").click(function() {
            $('#tb_bsc').hide();
            $('#tb_bsc_wrapper').hide();
            $('#tb_leave_other').hide();
            $('#tb_leave_other_wrapper').hide();
            $('#tb_dkp').show();
            $('#tb_dkp_wrapper').show();
            $(this).addClass('active');
            $('#btn_tb_bsc').removeClass('active');
            $('#btn_leave_other').removeClass('active');
        });

        $("#btn_leave_other").click(function() {
            $('#tb_bsc').hide();
            $('#tb_bsc_wrapper').hide();
            $('#tb_dkp').hide();
            $('#tb_dkp_wrapper').hide();
            $('#tb_leave_other').show();
            $('#tb_leave_other_wrapper').show();
            $(this).addClass('active');
            $('#btn_tb_bsc').removeClass('active');
            $('#btn_tb_dkp').removeClass('active');
        });

        $('.open-detail-time-leave').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('TimeleaveController@detailStaffApprove') }}',
                Type: 'GET',
                datatype: 'html',
                data: {
                    id: id,
                },
                cache: false,
                success: function(data) {
                    $('#html_pending').empty().append(data);
                    $('#bsc-modal').modal();
                },
                error: (error) => {
                    console.log(JSON.stringify(error));
                }
            });
        });

        $('.open-detail-leave-other').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('TimeleaveController@detailOtherLeaveApprove') }}',
                Type: 'GET',
                datatype: 'html',
                data: {
                    id: id,
                },
                cache: false,
                success: function(data) {
                    $('#html_pending2').empty().append(data);
                    $('#other-leave-modal').modal();
                },
                error: (error) => {
                    console.log(JSON.stringify(error));
                }
            });
        });
    </script>
@endsection
