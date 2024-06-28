@extends('main._layouts.master')

<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        #tb_dkp_wrapper {
            display: none;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/anytime.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2_init.js') }}"></script>
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Employee Department Transfer List</h1>
        <div class="card-header header-elements-inline">

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

            @if ($errors->any())
                <div class="alert alert-danger border-0 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <p><b>Input data is not correct:</b></p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ action('TransferController@list') }}" method="GET">
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

            @if ((auth()->user()-> department !=5))
                <div class="form-group d-flex">
                    <div class="">
                        <button class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter">Create New</button>
                    </div>
                </div>
            @endif
        </div>
        <!-- Modal bsc -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ action('TransferController@create') }}" method="post">
                        @csrf
                        <input type="hidden" name="id_staff_create" value="{{ auth()->user()->id }}">
                        <input type="hidden" name="old_department" value="{{ auth()->user()->department }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Create New Transfer</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Employee name</label>
                                <div class="col-lg-9">
                                    @if (auth()->user()->department != 5)
                                        <div class="col-form-label">
                                            {{ auth()->user()->firstname . ' ' . auth()->user()->lastname }}
                                        </div>
                                        <input type="hidden" name="staff_id" value="{{ auth()->user()->id }}" old_department="{{ auth()->user()->department }}">
                                    @else
                                        <select class="form-control select_staff_transfer" name="staff_id" id="selected_staff">
                                            <option selected hidden value="">Select employee</option>
                                            @foreach ($listStaff as $staff)
                                                <option value="{{ $staff->id }}" old_department="{{ $staff->department }}">{{ $staff->firstname . ' ' . $staff->lastname }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Current Department:</label>
                                <div class="col-lg-9">
                                    @if (auth()->user()->department != 5)
                                         {{-- {{ auth()->user()->department }} --}}
                                            @foreach ($listDepartment as $department)
                                                @if ($department['id'] == auth()->user()->department)
                                                    {{ $department['name'] }}
                                                @endif
                                            @endforeach
                                    @else
                                        <select class="form-control old_department" name="old_department" readonly="true">
                                            <!-- Phòng ban cũ sẽ được điền tự động dựa trên nhân viên được chọn -->
                                        </select>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">New Department:(*)</label>
                                <div class="col-lg-9">
                                    <select class="form-control new_department" name="new_department">
                                        @foreach ($listDepartment as $department)
                                            <option value="{{ $department['id'] }}">{{ $department['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" hidden>
                                    <label>Hr approved:(*)</label>
                                    <input type="hidden" class="form-control" name="txthr" value="1">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Proposed Salary:</label>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" name="txtNewSalary" min="1000000" max="200000000" id="txtNewSalary" placeholder="Enter proposed salary..." />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Notes:</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" name="note" id="note" cols="20" rows="10" max="300" required placeholder="e.g., Manager's request, job specifics, ..."></textarea>
                                </div>
                            </div>

                            <div class="form-group row" hidden>
                                <label class="col-lg-3 col-form-label">Director's Opinion:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="txtnoteManager" id="txtnoteManager" placeholder="Enter proposed salary,..." />
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Employee Name</th>
                    <th>Current Department</th>
                    <th>New Department</th>
                    <th>Current Salary</th>
                    <th>Proposed Salary</th>
                    <th>Current Manager Approval</th>
                    <th>New Manager Approval</th>
                    <th>Director Approval</th>
                    <th class="text-center">Status</th>
                    <th>Action</th>
                    <th>Director</th>
                </tr>
            </thead>
            <tbody>
{{-- chu ý  --}}
        <?php $count = 1; ?>
            @if(auth()->user()->department !=0)
                @foreach ($data as $transfer)
                        <tr>
                            <td><?php echo $count; $count++ ?></td>
                            <td><?php echo $transfer['staff_transfer'] ?></td>
                            @foreach ($listDepartment as $depart)
                            @if($transfer['old_department'] == $depart['id'])
                            <td><?php echo $depart['name'] ?></td>
                            @endif
                            @endforeach
                            <td><?php echo $transfer['new_department_name'] ?></td>
                            <td>
                                @php
                                    $salary = 0;
                                @endphp
                                @foreach($listContact as $contract)
                                    @if($transfer['staff_id'] == $contract->staffId)
                                        @php
                                            $salary = $contract->baseSalary;
                                        @endphp
                                    @endif
                                @endforeach
                                {{ number_format($salary) }}
                            </td>
                            <td><?php echo number_format($transfer['new_salary']) ?></td>
                            <td>
                                <?php echo $transfer['old_manager_approved'] == 0 ? '<span class="badge badge-warning">Not approved yet</span>' : '<span class="badge badge-success">Approved</span>' ?>
                            </td>
                            <td>
                                <?php echo $transfer['new_manager_approved'] == 0 ? '<span class="badge badge-warning">Not approved yet</span>' : '<span class="badge badge-success">Approved</span>' ?>
                            </td>
                            <td>
                                <?php echo $transfer['manager_approved'] == 0 ? '<span class="badge badge-warning">Not approved yet</span>' : '<span class="badge badge-success">Approved</span>' ?>
                            </td>
                            @if(auth()->user()->department == 2 )
                                @if($transfer['old_manager_approved'] == 0 && $transfer['new_manager_approved'] == 0)
                                    <td>
                                        @if($transfer['hr_approved'] ==1)
                                        <div class="from-group d-flex">
                                        &nbsp;&nbsp; <a class="btn btn-info open-detail-approvedHR" id="{{ $transfer['id'] }}" style="padding: 5px;color: white;cursor: pointer;">Confirm</a>
                                        </div>
                                        @elseif(auth()->user()->is_manager == 1 && $transfer['hr_approved'] ==0 &&($transfer['old_department']==2 ||$transfer['new_department']==2))
                                            <a href="{{ action('TransferController@approve') }}?id={{ $transfer['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">approve</a>
                                        @elseif($transfer['hr_approved'] ==0)
                                            <a>Confirmed</a>
                                        @endif
                                    </td>
                                @elseif($transfer['old_manager_approved'] == 1 && $transfer['new_manager_approved'] == 1 && $transfer['manager_approved'] == 1)
                                    <td style="max-width: 160px;">Approved, employee has moved departments</td>
                                @else
                                    @if($transfer['old_manager_approved'] == 1 && $transfer['new_manager_approved'] == 1)
                                    <td style="max-width: 160px;">Wait for Director's approval</td>
                                    @elseif(auth()->user()->is_manager == 1)
                                        <td>
                                            <div class="from-group d-flex">
                                                <a href="{{ action('TransferController@approve') }}?id={{ $transfer['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">approve</a>
                                            </div>
                                        </td>
                                    @else
                                        <td style="max-width: 160px;">There is already at least one approval manager, which cannot be edited</td>
                                    @endif
                                @endif
                            <!-- Hth     -->
                            <td style="max-width: 160px;">
                                @elseif(auth()->user()->department == 5 and $transfer['old_manager_approved'] == 0 and $transfer['new_manager_approved'] == 0)
                                        Managers have not approved
                                @elseif(auth()->user()->department == 5 and $transfer['old_manager_approved'] == 0 )
                                        The old manager has not approved yet
                                @elseif(auth()->user()->department == 5 and  $transfer['new_manager_approved'] == 0)
                                        New management has not approved yet
                                @elseif(auth()->user()->department == 5 and $transfer['manager_approved'] == 1)
                                        Approved, employee has moved departments
                                @elseif(auth()->user()->department == 5 and $transfer['old_manager_approved'] == 1 and $transfer['new_manager_approved'] == 1)
                            </td>
                            <td>
                                <div class="from-group d-flex">
                                    <a href="{{ action('TransferController@approve') }}?id={{ $transfer['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">approve</a>
                                </div>
                            </td>
                            <!-- Hth     -->
                            @else
                                <td>
                                    <div class="from-group d-flex">
                                        <a href="{{ action('TransferController@approve') }}?id={{ $transfer['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">approve</a>
                                    </div>
                                </td>
                            @endif
                            <td>
                                <div class="from-group d-flex">
                                    <a class="btn btn-info open-detail-transfer1" id="{{ $transfer['id'] }}" style="color: white; cursor: pointer;">Detail</a>
                                </div>
                            </td>
                    
                        </tr>
                @endforeach
            {{-- modoul1     <!-- tach theo phong ban va id --> --}}
            @elseif(auth()->user()->department != 2 and auth()->user()->is_manager == 1)
                @foreach ($data as $transfer)
                        @if($transfer['hr_approved'] == 0)
                        <tr>
                            <td><?php echo $count; $count++ ?></td>
                            <td><?php echo $transfer['staff_transfer'] ?></td>
                            @foreach ($listDepartment as $depart)
                            @if($transfer['old_department'] == $depart['id'])
                            <td><?php echo $depart['name'] ?></td>
                            @endif
                            @endforeach
                            <td><?php echo $transfer['new_department_name'] ?></td>
                            <td>
                                @php
                                    $salary = 0;
                                @endphp
                                @foreach($listContact as $contract)
                                    @if($transfer['staff_id'] == $contract->staffId)
                                        @php
                                            $salary = $contract->baseSalary;
                                        @endphp
                                    @endif
                                @endforeach
                                {{ number_format($salary) }}
                            </td>
                            <td><?php echo number_format($transfer['new_salary']) ?></td>
                            <td>
                                <?php echo $transfer['old_manager_approved'] == 0 ? '<span class="badge badge-warning">not approved yet</span>' : '<span class="badge badge-success">approved</span>' ?>
                            </td>
                            <td>
                                <?php echo $transfer['new_manager_approved'] == 0 ? '<span class="badge badge-warning">not approved yet</span>' : '<span class="badge badge-success">approved</span>' ?>
                            </td>
                            <td>
                                <?php echo $transfer['manager_approved'] == 0 ? '<span class="badge badge-warning">not approved yet</span>' : '<span class="badge badge-success">approved</span>' ?>
                            </td>
                            @if(auth()->user()->department == 2 )
                                @if($transfer['old_manager_approved'] == 0 && $transfer['new_manager_approved'] == 0)
                                    <td>
                                        <div class="from-group d-flex">
                                            <a class="btn btn-info open-detail-transfer" id="{{ $transfer['id'] }}" style="color: white; cursor: pointer;">Edit</a>
                                            <a href="{{ action('TransferController@delete') }}?id={{ $transfer['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Delete</a>
                                        </div>
                                        @if(auth()->user()->is_manager == 1)
                                            <a href="{{ action('TransferController@approve') }}?id={{ $transfer['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">Approve</a>
                                        @endif
                                    </td>
                                @elseif($transfer['old_manager_approved'] == 1 && $transfer['new_manager_approved'] == 1 && $transfer['manager_approved'] == 1)
                                    <td style="max-width: 160px;">Approved, the employee has moved departments</td>
                                @else
                                    @if(auth()->user()->is_manager == 1)
                                        <td>
                                            <div class="from-group d-flex">
                                                <a href="{{ action('TransferController@approve') }}?id={{ $transfer['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">Approve</a>
                                            </div>
                                        </td>
                                    @else
                                        <td style="max-width: 160px;">There is already at least one approval manager, which cannot be edited</td>
                                    @endif
                                @endif
                            <!-- Hth     -->
                            @elseif(auth()->user()->department == 5 and $transfer['old_manager_approved'] == 0 and $transfer['new_manager_approved'] == 0)
                                    <td style="max-width: 160px;">Managers have not approved</td>
                            @elseif(auth()->user()->department == 5 and $transfer['old_manager_approved'] == 0 )
                                    <td style="max-width: 160px;">The old manager has not approved yet</td>
                            @elseif(auth()->user()->department == 5 and  $transfer['new_manager_approved'] == 0)
                                    <td style="max-width: 160px;">New management has not approved yet</td>
                            @elseif(auth()->user()->department == 5 and $transfer['manager_approved'] == 1)
                                    <td style="max-width: 160px;">Approved, the employee has moved departments</td>
                            @elseif(auth()->user()->department == 5 and $transfer['old_manager_approved'] == 1 and $transfer['new_manager_approved'] == 1)
                            <td>
                                <div class="from-group d-flex">
                                    <a href="{{ action('TransferController@approve') }}?id={{ $transfer['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">Approve</a>
                                </div>
                                <div class="from-group d-flex">
                                    <a class="btn btn-info open-detail-transferC ml-2" id="{{ $transfer['id'] }}" style="color: white; cursor: pointer;">Request</a>
                                </div>
                            </td>
                            <!-- Hth bat o day    -->
                            @elseif($transfer['old_manager_approved'] == 1 and $transfer['new_manager_approved'] == 1 and  $transfer['manager_approved'] == 1)
                            <td style="max-width: 160px;">Approved, the employee has moved departments</td>
                            @elseif($transfer['old_manager_approved'] == 1 and $transfer['new_manager_approved'] == 1)
                            <td style="max-width: 160px;">Wait for Director's approval</td>
                            @else
                                <td>
                                    <div class="from-group d-flex">
                                        <a href="{{ action('TransferController@approve') }}?id={{ $transfer['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">Approve</a>
                                    </div>
                                </td>
                            @endif
                            <td>
                                <div class="from-group d-flex">
                                    <a class="btn btn-info open-detail-transfer1" id="{{ $transfer['id'] }}" style="color: white; cursor: pointer;">Detail</a>
                                </div>
                            </td>
                            <td style="max-width: 160px; color: red;">
                                <?php
                                    if(strlen($transfer['note_manager']) > 100) echo substr($transfer['note_manager'], 0, 100) . '...';
                                    else echo $transfer['note_manager'];
                                ?>
                            </td>
                    
                        </tr>
                        @endif
                    @endforeach
                {{-- modoul2  <!-- Tach theo id nhan vien dang nhap -->  --}}
            @elseif(auth()->user()->is_manager == 0 || $data['note_manager'] != null)
                @foreach ($data as $transfer)
                    @if($transfer['staff_id'] == auth()->user()->id )
                        <tr>
                            <td><?php echo $count; $count++ ?></td>
                            <td><?php echo $transfer['staff_transfer'] ?></td>
                            @foreach ($listDepartment as $depart)
                            @if($transfer['old_department'] == $depart['id'])
                            <td><?php echo $depart['name'] ?></td>
                            @endif
                            @endforeach
                            <td><?php echo $transfer['new_department_name'] ?></td>
                            <td>
                                @php
                                    $salary = 0;
                                @endphp
                                @foreach($listContact as $contract)
                                    @if($transfer['staff_id'] == $contract->staffId)
                                        @php
                                            $salary = $contract->baseSalary;
                                        @endphp
                                    @endif
                                @endforeach
                                {{ number_format($salary) }}
                            </td>
                            <td><?php echo number_format($transfer['new_salary']) ?></td>
                            <td>
                                <?php echo $transfer['old_manager_approved'] == 0 ? '<span class="badge badge-warning">Not approved yet</span>' : '<span class="badge badge-success">approved</span>' ?>
                            </td>
                            <td>
                                <?php echo $transfer['new_manager_approved'] == 0 ? '<span class="badge badge-warning">Not approved yet</span>' : '<span class="badge badge-success">approved</span>' ?>
                            </td>
                            <td>
                                <?php echo $transfer['manager_approved'] == 0 ? '<span class="badge badge-warning">Not approved yet </span>' : '<span class="badge badge-success">approved</span>' ?>
                            </td>
                            @if(auth()->user()->department != 5 )
                                @if($transfer['old_manager_approved'] == 0 && $transfer['new_manager_approved'] == 0)
                                    <td>
                                        <div class="from-group d-flex">
                                            <a class="btn btn-info open-detail-transfer" id="{{ $transfer['id'] }}" style="color: white; cursor: pointer;">Edit</a>
                                            <a href="{{ action('TransferController@delete') }}?id={{ $transfer['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Delete</a>
                                        </div>
                                    </td>
                                @elseif($transfer['old_manager_approved'] == 1 && $transfer['new_manager_approved'] == 1 && $transfer['manager_approved'] == 1)
                                    <td style="max-width: 160px;">Approved, the employee has moved departments</td>
                                @else
                                    @if($transfer['note_manager'] != null)
                                        <td>
                                            <div class="from-group d-flex">
                                                <a class="btn btn-info open-detail-transfer" id="{{ $transfer['id'] }}" style="color: white; cursor: pointer;">Edit</a>
                                                <a href="{{ action('TransferController@delete') }}?id={{ $transfer['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Delete</a>
                                            </div>
                                        </td>
                                    @else
                                        <td style="max-width: 160px;">There is already at least one approval manager, which cannot be edited</td>
                                    @endif
                                @endif
                            <!-- Hth     -->
                            @elseif(auth()->user()->department == 5 and $transfer['old_manager_approved'] == 0 and $transfer['new_manager_approved'] == 0)
                                    <td style="max-width: 160px;">Managers have not approved</td>
                            @elseif(auth()->user()->department == 5 and $transfer['old_manager_approved'] == 0 )
                                    <td style="max-width: 160px;">The old manager has not approved yet</td>
                            @elseif(auth()->user()->department == 5 and  $transfer['new_manager_approved'] == 0)
                                    <td style="max-width: 160px;">New management has not approved yet</td>
                            @elseif(auth()->user()->department == 5 and $transfer['manager_approved'] == 1)
                                    <td style="max-width: 160px;">Approved, the employee has moved departments</td>
                            @elseif(auth()->user()->department == 5 and $transfer['old_manager_approved'] == 1 and $transfer['new_manager_approved'] == 1)
                            <td>
                                <div class="from-group d-flex">
                                    <a href="{{ action('TransferController@approve') }}?id={{ $transfer['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">Approve</a>
                                </div>
                            </td>
                            <!-- Hth     -->
                            @else
                                <td>
                                    <div class="from-group d-flex">
                                        <a href="{{ action('TransferController@approve') }}?id={{ $transfer['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">Approve</a>
                                    </div>
                                </td>
                            @endif
                            <td>
                                <div class="from-group d-flex">
                                    <a class="btn btn-info open-detail-transfer1" id="{{ $transfer['id'] }}" style="color: white; cursor: pointer;">Detail</a>
                                </div>
                            </td>
                            <td style="max-width: 160px; color: red;">
                                <?php
                                    if(strlen($transfer['note_manager']) > 100) echo substr($transfer['note_manager'], 0, 100) . '...';
                                    else echo $transfer['note_manager'];
                                ?>
                            </td>
                        
                        </tr>
                    @endif
                @endforeach

            @endif
                </tbody>

            {{-- chu y --}}
        </table>

        <div id="bsc-modal" class="modal fade" role="dialog"> <!-- modal bsc -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ action('TransferController@update') }}" method="post" class="form-horizontal">
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
        $(document).ready(function() {
            $('.open-detail-transfer').click(function() {
                var id = $(this).attr('id');

                $.ajax({
                    url: '{{ action('TransferController@detail') }}',
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

            $('.open-detail-transfer1').click(function() {
                var id = $(this).attr('id');

                $.ajax({
                    url: '{{ action('TransferController@detail1') }}',
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

            $('.open-detail-transferC').click(function() {
                var id = $(this).attr('id');

                $.ajax({
                    url: '{{ action('TransferController@detailC') }}',
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
            $('.open-detail-approvedHR').click(function() {
                var id = $(this).attr('id');

                $.ajax({
                    url: '{{ action('TransferController@approvedHR') }}',
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



            $(".select_staff_transfer").change(function() {
                var old_department = $('option:selected', this).attr('old_department');

                $.ajax({
                    url: '{{ action('TransferController@loadOldDepartment') }}',
                    Type: 'GET',
                    datatype: 'text',
                    data: {
                        old_department: old_department
                    },
                    cache: false,
                    success: function(data) {
                        $('.old_department').empty().append(data);
                    }
                });

            });
        });

        var DatatableBasic = function() {

            // Basic Datatable examples
            var _componentDatatableBasic = function() {
                if (!$().DataTable) {
                    console.warn('Warning - datatables.min.js is not loaded.');
                    return;
                }

                // Setting datatable defaults
                $.extend($.fn.dataTable.defaults, {
                    autoWidth: false,
                    columnDefs: [{
                        orderable: false,
                        width: 100,
                        targets: [5]
                    }],
                    dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
                    language: {
                        search: '<span>Search:</span> _INPUT_',
                        searchPlaceholder: 'Enter keyword...',
                        lengthMenu: '<span>Show:</span> _MENU_',
                        paginate: {
                            'first': 'First',
                            'last': 'Last',
                            'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
                            'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
                        }
                    }
                });

                // Basic datatable
                $('.datatable-basic').DataTable();
                $('.datatable-basic2').DataTable();

                // Alternative pagination
                $('.datatable-pagination').DataTable({
                    pagingType: "simple",
                    language: {
                        paginate: {
                            'next': $('html').attr('dir') == 'rtl' ? 'Next &larr;' : 'Next &rarr;',
                            'previous': $('html').attr('dir') == 'rtl' ? '&rarr; Prev' : '&larr; Prev'
                        }
                    }
                });

                // Datatable with saving state
                $('.datatable-save-state').DataTable({
                    stateSave: true
                });

                // Scrollable datatable
                var table = $('.datatable-scroll-y').DataTable({
                    autoWidth: true,
                    scrollY: 300
                });

                // Resize scrollable table when sidebar width changes
                $('.sidebar-control').on('click', function() {
                    table.columns.adjust().draw();
                });
            };

            // Select2 for length menu styling
            var _componentSelect2 = function() {
                if (!$().select2) {
                    console.warn('Warning - select2.min.js is not loaded.');
                    return;
                }

                // Initialize
                $('.dataTables_length select').select2({
                    minimumResultsForSearch: Infinity,
                    dropdownAutoWidth: true,
                    width: 'auto'
                });
            };

            return {
                init: function() {
                    _componentDatatableBasic();
                    _componentSelect2();
                }
            }
        }();

        document.addEventListener('DOMContentLoaded', function() {
            DatatableBasic.init();
        });
    </script>
@endsection
