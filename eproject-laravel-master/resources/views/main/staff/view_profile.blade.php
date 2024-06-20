@extends('main._layouts.master')

@section('css')
    <style>
        .text-green {
            color: #008B56
        }

        .btn-link:hover {
            color: #1ab177;
        }

        .card-header {
            background-color: #cccccc40 !important;
        }

        .card {
            margin-bottom: 0px;
        }

        @media (min-width: 1365px) {
            .infomation-staff {
                margin-left: 20px;
            }
        }

        @media (min-width: 1920px) {
            .infomation-staff {
                margin-left: 80px;
            }

            .ml-1920-5 {
                margin-left: 3.75rem !important;
            }

            .mr-1920-5 {
                margin-right: 3.75rem !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row mr-lg-1 ml-1920-5 mr-1920-5">
        <div class="col-lg-4">
            <div class="wrapper" style="border: 1px solid gray">
                {{-- @dd($data[0]); --}}
                <div class="image text-center">
                    <img src="{{ asset($staff['photo']) }}" alt="" width="50%" height="auto">
                    <h3 class="text-green font-weight-bold"><?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?></h3>
                    <h4 class="text-green font-weight-bold">-- <?php echo $staff['department_name']; ?> --</h4>
                </div>
                <div class="infomation-staff" style="font-size: 14px">
                    <p>
                        <span class="text-green"><i class="icon-qrcode"></i> Code: </span> <i class="ml-2"><?php echo $staff['code']; ?></i>
                    </p>
                    <p>
                        <span class="text-green"><i class="icon-calendar"></i> Date of Birth: </span> <i class="ml-2">
                            <?php
                            use Carbon\Carbon;
                            $date = Carbon::parse($staff['dob']);
                            echo date_format($date, 'd/m/Y');
                            ?>
                        </i>
                    </p>
                    <p>
                        <span class="text-green"><i class="icon-user"></i> Gender: </span> <i class="ml-2"> <?php echo $staff['gender'] == 1 ? 'Male' : 'Female'; ?> </i>
                    </p>
                    <p>
                        <span class="text-green"><i class="icon-phone2"></i> Phone Number: </span> <i class="ml-2"> <?php echo $staff['phone_number']; ?> </i>
                    </p>
                    <p>
                        <span class="text-green"><i class="icon-mail5"></i> Email Address: </span> <i class="ml-2"> <?php echo $staff['email']; ?> </i>
                    </p>
                    <p>
                        <span class="text-green"><i class="icon-redo2"></i> Join Date: </span> <i class="ml-2">
                            <?php
                            $date = Carbon::parse($staff['joined_at']);
                            echo date_format($date, 'd/m/Y');
                            ?>
                        </i>
                    </p>
                    <p>
                        <span class="text-green"><i class="icon-user-check"></i> Status: </span> <i class="ml-2"> <?php echo $staff['off_date'] == null ? 'Enable' : 'Disable'; ?> </i>
                    </p>
                </div>
                <div class="image text-center mt-5">
                    <div class="front">
                        <img src="{{ asset($staff['id_photo']) }}" alt="" width="60%" height="auto">
                        <h6 class="text-green">ID Front Photo</h6>
                    </div>
                    <div class="back mt-4">
                        <img src="{{ asset($staff['id_photo_back']) }}" alt="" width="60%" height="auto">
                        <h6 class="text-green">ID Back Photo</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8" style="border: 1px solid gray">
            <div class="row">
                <div id="accordion" style="width: 100%">
                    <div class="card">
                        <div class="card-header p-1" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link text-green" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="font-size: 17px">
                                    Basic Information
                                </button>
                            </h5>
                        </div>

                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body p-0 mt-3 mb-3 ml-4 mr-4">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Full Name: </label>
                                            <div class="control col-8"><?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?></div>
                                        </div>
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Department: </label>
                                            <div class="control col-8"><?php echo $staff['department_name']; ?></div>
                                        </div>
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Position: </label>
                                            <div class="control col-8"><?php echo $staff['is_manager'] == 1 ? 'Manager' : 'Employee'; ?></div>
                                        </div>
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">District: </label>
                                            <div class="control col-8"><?php echo $staff['district']; ?></div>
                                        </div>
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Province/City: </label>
                                            <div class="control col-8"><?php echo $staff['province']; ?></div>
                                        </div>
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Remaining Leave Days: </label>
                                            <div class="control col-8"><?php echo $staff['day_of_leave']; ?></div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Join Date: </label>
                                            <div class="control col-8">
                                                <?php
                                                $date = date_create($staff['joined_at']);
                                                echo date_format($date, 'd/m/Y');
                                                ?>
                                            </div>
                                        </div>
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Off Date: </label>
                                            <div class="control col-8"><?php if ($staff['off_date']) {
                                                $date_off = date_create($staff['off_date']);
                                                echo date_format($date_off, 'd/m/Y');
                                            } ?></div>
                                        </div>
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Created By: </label>
                                            <div class="control col-8"><?php echo $staff['name_staff_create']; ?></div>
                                        </div>
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Created At: </label>
                                            <div class="control col-8"><?php $date = date_create($staff['created_at']);
                                            echo date_format($date, 'd/m/Y'); ?></div>
                                        </div>
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Updated By: </label>
                                            <div class="control col-8"><?php echo $staff['name_staff_update']; ?></div>
                                        </div>
                                        <div class="control-group row">
                                            <label for="" class="col-4 p-0">Updated At: </label>
                                            <div class="control col-8"><?php $date = date_create($staff['updated_at']);
                                            echo date_format($date, 'd/m/Y'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header p-1" id="headingTwo">
                            <h5 class="mb-0">
                                <button class="btn btn-link text-green collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="font-size: 17px">
                                    Education
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
                            <div class="card-body p-0 mt-3 mb-3 ml-4 mr-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Level</th>
                                                <th>School</th>
                                                <th>Field of Study</th>
                                                <th>Year of Graduation</th>
                                                <th>Grade</th>
                                                <th>Mode of Study</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $count = 1; ?>
                                            @foreach ($educations as $education)
                                                <tr>
                                                    <td><?php echo $count; ?></td>
                                                    <td>{{ $education['levelName'] }}</td>
                                                    <td>{{ $education['school'] }}</td>
                                                    <td>{{ $education['fieldOfStudy'] }}</td>
                                                    <td>{{ $education['graduatedYear'] }}</td>
                                                    <td>{{ $education['grade'] }}</td>
                                                    <td>{{ $education['modeOfStudy'] }}</td>
                                                </tr>
                                                <?php $count++; ?>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header p-1" id="headingThree">
                            <h5 class="mb-0">
                                <button class="btn btn-link text-green collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="font-size: 17px">
                                    Contract
                                </button>
                            </h5>
                        </div>
                        <div id="collapseThree" class="collapse show" aria-labelledby="headingThree" data-parent="#accordion">
                            <div class="card-body p-0 mt-3 mb-3 ml-4 mr-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Salary</th>
                                                <th>Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $count_ct = 1; ?>
                                            @foreach ($contracts as $contract)
                                                <tr>
                                                    <td><?php echo $count_ct; ?></td>
                                                    <td><?php $date = date_create($contract['startDate']);
                                                    echo date_format($date, 'd/m/Y'); ?></td>
                                                    <td><?php $date = date_create($contract['endDate']);
                                                    echo date_format($date, 'd/m/Y'); ?></td>
                                                    <td>{{ number_format($contract['baseSalary']) }} VND</td>
                                                    <td><?php $date = date_create($contract['createAt']);
                                                    echo date_format($date, 'd/m/Y'); ?></td>
                                                </tr>
                                                <?php $count_ct++; ?>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header p-1" id="headingFour">
                            <h5 class="mb-0">
                                <button class="btn btn-link text-green collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseThree" style="font-size: 17px">
                                    Change Password
                                </button>
                            </h5>
                        </div>
                        <div id="collapseFour" class="collapse show" aria-labelledby="headingFour" data-parent="#accordion">
                            <div class="card-body p-0 mt-3 mb-3 ml-4 mr-4">
                                <form action="{{ action('StaffController@changePassword') }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-lg-9">
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
                                            <div class="form-group row">
                                                <label class="col-lg-5 col-form-label">Old Password:</label>
                                                <div class="col-lg-7">
                                                    <input type="password" class="form-control" name="pass_old" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-5 col-form-label">New Password:</label>
                                                <div class="col-lg-7">
                                                    <input type="password" class="form-control" name="pass_new" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-5 col-form-label">Confirm New Password:</label>
                                                <div class="col-lg-7">
                                                    <input type="password" class="form-control" name="comfirm_pass" required>
                                                </div>
                                            </div>
                                            <div class="">
                                                <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">Change</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

        });
    </script>
@endsection

@section('scripts')
    <script></script>
@endsection
