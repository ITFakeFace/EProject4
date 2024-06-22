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
    <script src="{{ asset('global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/demo_pages/form_layouts.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Additional Attendance / Leave Registration</h1>
        <div class="card-header header-elements-inline">
            <h4 class="card-title font-weight-bold text-uppercase">
                <?php echo auth()->user()->firstname . ' ' . auth()->user()->lastname; ?>
                - <?php echo $staff[0][2]; ?>
                - <?php echo auth()->user()->is_manager == 1 ? 'Manager' : 'Staff'; ?>
            </h4>
            <h4 class="card-title font-weight-bold text-uppercase">
                Remaining Leave Days: <?php echo auth()->user()->day_of_leave; ?> days
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
            <form action="{{ action('TimeleaveController@index') }}" method="GET">
                @csrf
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
            </form>

            <div class="form-group d-flex">
                <div class="">
                    <button class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter">Additional Attendance</button>
                </div>
                <div class="ml-1">
                    <button id="register_leave" class="btn btn-info" data-toggle="modal" data-target="#exampleModalCenter2">Leave Registration</button>
                </div>
            </div>

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
        <!-- Modal bsc -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ action('TimeleaveController@createTime') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Additional Attendance</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Date:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control day_leave" name="day_leave" value="" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Adjustment Request:</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="number_day_leave" id="number_day_leave" required>
                                        <option value="1">One day</option>
                                        <option value="0.5">Half day</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Image (if any):</label>
                                <div class="col-lg-9">
                                    <input type="file" class="form-input-styled" name="txtImage" data-fouc>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Reason:</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" name="note_bsc" id="note_bsc" cols="20" rows="10" placeholder="E.g., Forgot to check in, Forgot to check out, ..." required></textarea>
                                </div>
                            </div>

                            <div class="des-bsc">
                                <h3>Detailed Description</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>
                                            <b>Maximum number of additional attendance per request</b>
                                            <p>1 day or 0.5 day / request</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Additional Attendance Information</b>
                                            <p>
                                                <b>1. Description: </b>Employees use this to add attendance for days they worked but forgot to check in/out. Will be credited if approved by the department manager and director. <br>
                                                <b>2. Applicable to: </b> Employees who have signed official contracts with the company. <br>
                                                <b>3. Required documents: </b> None. <br>
                                                <b>4. Salary: </b> The company pays for the days worked but forgot to check in/out.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal dkp -->
        <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ action('TimeleaveController@createLeave') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Leave Registration</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Leave Type:</label>
                                <div class="col-lg-9">
                                    <select class="form-control type_of_leave" name="type_of_leave" id="type_of_leave" required>
                                        <option value="0" selected>Paid Annual Leave</option>
                                        <option value="2">Unpaid Leave</option>
                                        <option value="3">Short-term Sick Leave</option>
                                        <option value="4">Long-term Sick Leave</option>
                                        <option value="5">Maternity Leave</option>
                                        <option value="6">Marriage Leave</option>
                                        <option value="7">Bereavement Leave</option>
                                    </select>
                                </div>
                            </div>

                            <div class="leave-basic">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Leave Date:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control day_leave" name="day_leave" value="" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Leave Request:</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="number_day_leave" id="number_day_leave" required>
                                            <option value="1">One day</option>
                                            <option value="0.5">Half day</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="leave-long" style="display: none">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">From Date:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control day_leave" name="day_leave_from" value="" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">To Date:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control day_leave" name="day_leave_to" value="" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Image:</label>
                                    <div class="col-lg-9">
                                        <input type="file" class="form-input-styled" name="image_leave" data-fouc>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Reason:</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" name="note_dkp" id="note_dkp" cols="20" rows="5" placeholder="E.g., Personal matters, Studying, ..." required></textarea>
                                </div>
                            </div>

                            <div class="des-leave des-leave0">
                                <h3>Detailed Description</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>
                                            <b>Maximum leave per request</b>
                                            <p>1 day / request</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Leave Information</b>
                                            <p>
                                                <b>1. Description: </b>Employees use annual leave for personal matters. <br>
                                                <b>2. Applicable to: </b> Employees who have signed official contracts with the company. <br>
                                                <b>3. Required documents: </b> None. <br>
                                                <b>4. Salary: </b> The company pays for the leave days.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="des-leave des-leave2" style="display: none">
                                <h3>Detailed Description</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>
                                            <b>Maximum leave per request</b>
                                            <p>1 month</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Leave Information</b>
                                            <p>
                                                <b>1. Description: </b>Employees who have exhausted their annual leave in one cycle and do not meet the conditions to use other types of leave (personal leave with pay, insurance leave). <br>
                                                <b>2. Applicable to: </b> Applies to all employees needing personal leave (bereavement, sick leave without a doctor's prescription and insurance certificate, military examination leave...). <br>
                                                <b>3. Required documents: </b> None. <br>
                                                <b>4. Salary: </b> No salary for the leave days. <br>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="des-leave des-leave3" style="display: none">
                                <h3>Detailed Description</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>
                                            <b>Maximum leave per request</b>
                                            <p>3 days</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Leave Information</b>
                                            <p>
                                                <b>1. Description: </b>Personal sick leave as prescribed by a doctor and issued with a social insurance leave certificate (form C65) or hospital discharge certificate during the leave period. <br>
                                                <b>2. Applicable to: </b> Employees participating in compulsory insurance at the company. <br>
                                                <b>3. Required documents: </b> Must submit the original Social Insurance Leave Certificate (form C65) / hospital discharge certificate. The Social Insurance Agency only processes the salary for the leave days when the employee submits all valid documents as required to the company. <br>
                                                <b>4. Salary: </b> The Social Insurance Agency calculates and pays the salary for the leave days based on the documents submitted to the company (calculated based on the monthly compulsory insurance salary). <br>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="des-leave des-leave4" style="display: none">
                                <h3>Detailed Description</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>
                                            <b>Maximum leave per request</b>
                                            <p>1 month</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Leave Information</b>
                                            <p>
                                                <b>1. Description: </b>Applies only to individuals with diseases listed in the Ministry of Health's long-term treatment list as prescribed by the doctor and the registered hospital. <br>
                                                <b>2. Applicable to: </b> Employees participating in compulsory insurance at the company. <br>
                                                <b>3. Required documents: </b> Must submit the original hospital discharge certificate (for inpatient treatment); Medical consultation report (original or certified copy) and Treatment confirmation (original) for outpatient treatment. The Social Insurance Agency only processes the salary for the leave days when the employee submits all valid documents as required to the company. <br>
                                                <b>4. Salary: </b> The Social Insurance Agency calculates and pays the salary for the leave days based on the documents submitted to the company (calculated based on the monthly compulsory insurance salary)
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="des-leave des-leave5" style="display: none">
                                <h3>Detailed Description</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>
                                            <b>Maximum leave per request</b>
                                            <p>6 months</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Leave Information</b>
                                            <p>
                                                <b>1. Description: </b>Maternity leave according to state regulations. <br>
                                                <b>2. Applicable to: </b> Employees with a social insurance participation period of at least 6 months within 12 months before childbirth or adoption. <br>
                                                <b>3. Required documents: </b> Must submit a Birth Certificate / Birth Certificate / Birth Extract of the child (01 certified copy, 01 original/child). The Social Insurance Agency only processes the salary for the leave days when the employee submits all valid documents as required to the company. Submission period: immediately after obtaining all documents and no later than the maternity leave period. <br>
                                                <b>4. Salary: </b> No salary from the company for the leave days, only the Social Insurance Agency calculates and pays the benefit (based on the monthly compulsory insurance salary) for the leave days based on the documents submitted to the company.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="des-leave des-leave6" style="display: none">
                                <h3>Detailed Description</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>
                                            <b>Maximum leave per request</b>
                                            <p>3 days</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Leave Information</b>
                                            <p>
                                                <b>1. Description: </b>Personal marriage leave. <br>
                                                <b>2. Applicable to: </b> Employees who have signed official labor contracts with the company. <br>
                                                <b>3. Required documents: </b> Must upload a photo of the marriage certificate (the company only calculates and pays the salary when the employee uploads the marriage certificate photo to the system). If valid documents are not supplemented, the registered leave days will be considered unpaid leave. <br>
                                                <b>4. Salary: </b> The company calculates and pays the salary for 03 leave days
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="des-leave des-leave7" style="display: none">
                                <h3>Detailed Description</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>
                                            <b>Maximum leave per request</b>
                                            <p>3 days</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Leave Information</b>
                                            <p>
                                                <b>1. Description: </b>Parental (both sides), spouse, or child's death. <br>
                                                <b>2. Applicable to: </b> Employees who have signed official labor contracts with the company. <br>
                                                <b>3. Required documents: </b> Must upload a photo of the death certificate of the deceased (the company only calculates and pays the salary when the employee uploads the death certificate photo to the system). If valid documents are not supplemented, the registered leave days will be considered unpaid leave. <br>
                                                <b>4. Salary: </b> The company calculates and pays the salary for 03 leave days
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <table class="table datatable-basic" id="tb_bsc">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Recorded Hours</th>
                    <th>Type</th>
                    <th>Note</th>
                    <th>Approval</th>
                    <th>Edit / Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $time_leave)
                    @if ($time_leave['type'] == 0)
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::createFromTimestampMs($time_leave['dayTimeLeave'])->format('d/m/Y') }}
                            </td>
                            <td><?php echo $time_leave['time'] == '08:00:00' ? '1 day' : '0.5 day'; ?></td>
                            <td><?php echo $time_leave['type'] == 0 ? 'Additional Attendance' : 'Leave Registration'; ?></td>
                            <td>
                                <?php
                                if (strlen($time_leave['note']) > 20) {
                                    echo substr($time_leave['note'], 0, 30) . '...';
                                } else {
                                    echo $time_leave['note'];
                                }
                                ?>
                            </td>
                            <td>
                                @if ($time_leave['isApproved'] == 0)
                                    <span class="badge badge-warning">Not Approved</span>
                                @elseif($time_leave['isApproved'] == 2)
                                    <span class="badge badge-success">Manager Approved</span>
                                @else
                                    <span class="badge badge-primary">Director Approved</span>
                                @endif
                            </td>
                            @if ($time_leave['done'] == 1)
                                <td><span class="badge badge-danger">Closed</span></td>
                            @elseif($time_leave['isApproved'] == 0 || ($time_leave['isApproved'] == 2 && auth()->user()->is_manager == 1))
                                <?php
                                $date1 = \Carbon\Carbon::parse($time_leave['createdAt']);
                                $date2 = \Carbon\Carbon::today();
                                $diff = date_diff($date1, $date2);
                                ?>
                                @if ($diff->format('%a') > 1)
                                    <td>
                                        <div class="from-group d-flex">
                                            More than 2 days since additional attendance
                                        </div>
                                    </td>
                                @else
                                    <td>
                                        <div class="from-group d-flex">
                                            <a class="btn btn-info open-detail-time-leave" id="{{ $time_leave['id'] }}" style="color: white; cursor: pointer;">Edit</a>
                                            <a href="{{ action('TimeleaveController@deleteTime') }}?id={{ $time_leave['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Delete</a>
                                        </div>
                                    </td>
                                @endif
                            @elseif($time_leave['isApproved'] == 2)
                                <td>Manager Approved, Awaiting Director Approval!</td>
                            @else
                                <td>Director Approved, Cannot Edit!</td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <table class="table datatable-basic" id="tb_dkp" style="display: none">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Recorded Hours</th>
                    <th>Type</th>
                    <th>Note</th>
                    <th>Approval</th>
                    <th>Edit / Delete</th>
                </tr>

            </thead>
            <tbody>
                @foreach ($data as $time_leave)
                    @if ($time_leave['type'] == 1)
                        <tr>
                            <td>{{ $time_leave['dayTimeLeave'] }}</td>
                            <td><?php echo $time_leave['time'] == '08:00:00' ? '1 day' : '0.5 day'; ?></td>
                            <td><?php echo $time_leave['type'] == 0 ? 'Additional Attendance' : 'Leave Registration'; ?></td>
                            <td>
                                <?php
                                if (strlen($time_leave['note']) > 20) {
                                    echo substr($time_leave['note'], 0, 30) . '...';
                                } else {
                                    echo $time_leave['note'];
                                }
                                ?>
                            </td>
                            <td>
                                @if ($time_leave['isApproved'] == 0)
                                    <span class="badge badge-warning">Not Approved</span>
                                @elseif($time_leave['isApproved'] == 2)
                                    <span class="badge badge-success">Manager Approved</span>
                                @else
                                    <span class="badge badge-primary">Director Approved</span>
                                @endif
                            </td>
                            @if ($time_leave['done'] == 1)
                                <td><span class="badge badge-danger">Closed</span></td>
                            @elseif($time_leave['isApproved'] == 0 || ($time_leave['isApproved'] == 2 && auth()->user()->is_manager == 1))
                                <td>
                                    <div class="from-group d-flex">
                                        <a class="btn btn-info open-detail-dkp" id="{{ $time_leave['id'] }}" style="color: white; cursor: pointer;">Edit</a>
                                        <a href="{{ action('TimeleaveController@deleteTime') }}?id={{ $time_leave['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Delete</a>
                                    </div>
                                </td>
                            @elseif($time_leave['isApproved'] == 2)
                                <td>Manager Approved, Awaiting Director Approval!</td>
                            @else
                                <td>Director Approved, Cannot Edit!</td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <table class="table datatable-basic" id="tb_leave_other" style="display: none">
            <thead>
                <tr>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Leave Type</th>
                    <th>Note</th>
                    <th>Approval</th>
                    <th>Edit / Delete</th>
                </tr>

            </thead>
            <tbody>
                @foreach ($leave_other as $item)
                    <tr>
                        <td>{{ $item['fromDate'] }}</td>
                        <td>{{ $item['toDate'] }}</td>
                        <td>
                            <?php
                            if ($item['typeLeave'] == 2) {
                                echo 'Unpaid Leave';
                            } elseif ($item['typeLeave'] == 3) {
                                echo 'Short-term Sick Leave';
                            } elseif ($item['typeLeave'] == 4) {
                                echo 'Long-term Sick Leave';
                            } elseif ($item['typeLeave'] == 5) {
                                echo 'Maternity Leave';
                            } elseif ($item['typeLeave'] == 6) {
                                echo 'Marriage Leave';
                            } elseif ($item['typeLeave'] == 7) {
                                echo 'Bereavement Leave';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (strlen($item['note']) > 20) {
                                echo substr($item['note'], 0, 30) . '...';
                            } else {
                                echo $item['note'];
                            }
                            ?>
                        </td>
                        <td>
                            @if ($item['isApproved'] == 0)
                                <span class="badge badge-warning">Not Approved</span>
                            @elseif($item['isApproved'] == 2)
                                <span class="badge badge-success">Manager Approved</span>
                            @else
                                <span class="badge badge-primary">Director Approved</span>
                            @endif
                        </td>
                        @if ($item['done'] == 1)
                            <td><span class="badge badge-danger">Closed</span></td>
                        @elseif($item['isApproved'] == 0 || ($item['isApproved'] == 2 && auth()->user()->is_manager == 1))
                            <td>
                                <div class="from-group d-flex">
                                    <a class="btn btn-info open-detail-leave-other" id="{{ $item['id'] }}" style="color: white; cursor: pointer;">Edit</a>
                                    <a href="{{ action('TimeleaveController@deleteLeaveOther') }}?id={{ $item['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Delete</a>
                                </div>
                            </td>
                        @elseif($item['isApproved'] == 2)
                            <td>Manager Approved, Awaiting Director Approval!</td>
                        @else
                            <td>Director Approved, Cannot Edit!</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div id="bsc-modal" class="modal fade" role="dialog"> <!-- modal bsc -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ action('TimeleaveController@updateTime') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div id="html_pending">

                        </div>
                    </form> <!-- end form -->
                </div>
            </div>
        </div> <!-- end modal bsc -->

        <div id="dkp-modal" class="modal fade" role="dialog"> <!-- modal dkp -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ action('TimeleaveController@updateTime') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div id="html_pending">

                        </div>
                    </form> <!-- end form -->
                </div>
            </div>
        </div> <!-- end modal dkp -->

        <div id="dkp-leave-other" class="modal fade" role="dialog"> <!-- modal dkp -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ action('TimeleaveController@updateLeaveOther') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div id="html_pending3">

                        </div>
                    </form> <!-- end form -->
                </div>
            </div>
        </div> <!-- end modal dkp -->

    </div>
    <!-- /basic datatable -->
@endsection

@section('scripts')
    <script>
        $('.day_bsc').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('.day_leave').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('.type_of_leave').change(function() {
            let type_of_leave = $(this).val();
            if (type_of_leave == 0) {
                $('.leave-basic').show();
                $('.leave-long').hide();
            } else {
                $('.leave-basic').hide();
                $('.leave-long').show();
            }

            switch (type_of_leave) {
                case "0":
                    $('.des-leave').hide();
                    $('.des-leave0').show();
                    break;
                case "2":
                    $('.des-leave').hide();
                    $('.des-leave2').show();
                    break;
                case "3":
                    $('.des-leave').hide();
                    $('.des-leave3').show();
                    break;
                case "4":
                    $('.des-leave').hide();
                    $('.des-leave4').show();
                    break;
                case "5":
                    $('.des-leave').hide();
                    $('.des-leave5').show();
                    break;
                case "6":
                    $('.des-leave').hide();
                    $('.des-leave6').show();
                    break;
                case "7":
                    $('.des-leave').hide();
                    $('.des-leave7').show();
                    break;
                default:
                    break;
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
                url: '{{ action('TimeleaveController@detailTime') }}',
                Type: 'POST',
                datatype: 'text',
                data: {
                    id: id,
                },
                cache: false,
                success: function(data) {
                    console.log(data);
                    $('#html_pending').empty().append(data);
                    $('#bsc-modal').modal();
                }
            });
        });

        $('.open-detail-dkp').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('TimeleaveController@detailLeave') }}',
                Type: 'POST',
                datatype: 'text',
                data: {
                    id: id,
                },
                cache: false,
                success: function(data) {
                    console.log(data);
                    $('#html_pending').empty().append(data);
                    $('#bsc-modal').modal();
                }
            });
        });

        $('.open-detail-leave-other').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('TimeleaveController@detailLeaveOther') }}',
                Type: 'POST',
                datatype: 'text',
                data: {
                    id: id,
                },
                cache: false,
                success: function(data) {
                    console.log(data);
                    $('#html_pending3').empty().append(data);
                    $('#dkp-leave-other').modal();
                }
            });
        });
    </script>
@endsection
