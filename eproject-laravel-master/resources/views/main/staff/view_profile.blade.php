@extends('main._layouts.master')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-left text-uppercase mx-3">Welcome <?php echo $staff['firstname'] . ' ' . $staff['lastname'].'!'; ?></h4>
                        <form class="forms-sample">
                            @csrf
                            <div class="container-xl px-4 mt-4">
                                <!-- Account page navigation-->
                                <div class="row">
                                    <div class="col-xl-4">
                                        <!-- Profile picture card-->
                                        <div class="card mb-4 mb-xl-0">
                                            <div class="card-header h4">Photo</div>
                                            <div class="card-body text-center">
                                                <!-- Profile picture image-->
                                                @if (!empty($staff['photo']))
                                                    <img class="img-account-profile rounded-circle mb-2" src="{{ asset($staff['photo']) }}" alt="Display_photo" width="310px" height="310px">
                                                    <input type="hidden" name="current_account_image" value="{{ asset($staff['photo']) }}">
                                                @endif
                                                <!-- Profile picture help block-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-8">
                                        <!-- Account details card-->
                                        <div class="card mb-4">
                                            <div class="card-header h4">Employee's Information</div>
                                            <div class="card-body">
                                                <form>
                                                    <div class="row gx-3 mb-3">
                                                        <!-- Form Group (first name)-->
                                                        <div class="col-md-6">
                                                            <label class="small mb-1" for="account_name">Fullname</label>
                                                            <input class="form-control" name="account_name" type="text" value="<?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?>" readonly>
                                                        </div>
                                                        <!-- Form Group (Account type)-->
                                                        <div class="col-md-6">
                                                            <label class="small mb-1" for="dob">Date of Birth</label>
                                                            <input class="form-control" name="dob" type="text" value="@php use Carbon\Carbon;  $date = Carbon::createFromTimestampMs((float)$staff['dob'],"Asia/Ho_Chi_Minh")->format('Y-m-d');
                                               echo $date; @endphp" readonly>
                                                        </div>
                                                    </div>
                                                    <!-- Form Row-->
                                                    <div class="row gx-3 mb-3">
                                                        <!-- Form Group (email)-->
                                                        <div class="col-md-6">
                                                            <label class="small mb-1" for="gender">Gender</label>
                                                            <input class="form-control" name="gender" type="text" value="<?php echo $staff['gender'] == 1 ? 'Nam' : 'Nữ'; ?>" readonly>
                                                        </div>
                                                        <!-- Form Group (last name)-->
                                                        <div class="col-md-6">
                                                            <label class="small mb-1" for="department">Department</label>
                                                            <input class="form-control" name="department" type="text" value="<?php echo $staff['department_name']; ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <!-- Form Row  -->
                                                    <div class="row gx-3 mb-3">
                                                        <!-- Form Group (email)-->
                                                        <div class="col-md-6">
                                                            <label class="small mb-1" for="email">Email</label>
                                                            <input class="form-control" name="email" type="text" value="{{ $staff['email'] }}" readonly>
                                                        </div>
                                                        <!-- Form Group (last name)-->
                                                        <div class="col-md-6">
                                                            <label class="small mb-1" for="phoneNumber">Phone Number</label>
                                                            <input class="form-control" name="phoneNumber" type="text" value="<?php echo $staff['phone_number']; ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <!-- Form Row  -->
                                                    <div class="row gx-3 mb-3">
                                                        <!-- Form Group (organization name)-->
                                                        <div class="col-md-6">
                                                            <label class="small mb-1" for="joinedAt">Joined Date</label>
                                                            <input class="form-control" name="joinedAt" type="text" value="@php $joinedDate = Carbon::createFromTimestampMs((float)$staff['joined_at'],"Asia/Ho_Chi_Minh")->format('Y-m-d');
                                               echo $joinedDate; @endphp" readonly>
                                                        </div>
                                                        <!-- Form Group (location)-->
                                                        <div class="col-md-6">
                                                            <label class="small mb-1" for="status">Status</label>
                                                            <input class="form-control" name="status" type="text" value="{{ $staff['off_date'] == null ? 'Enable' : 'Disable' }}" readonly>
                                                        </div>
                                                    </div>
                                                    
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <legend class="font-weight-semibold"><i class="icon-reading mr-2"></i> Education</legend>
                                        <div class="card-body p-0 mt-3 mb-3 ml-4 mr-4">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Level</th>
                                                            <th>Level Name</th>
                                                            <th>School</th>
                                                            <th>Field of Study</th>
                                                            <th>Graduation Year</th>
                                                            <th>Grade</th>
                                                            <th>Mode of Study</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $count = 1; ?>
                                                        @foreach ($educations as $de)
                                                            <tr>
                                                                @if ($staff['id'] == $de['staffId'])
                                                                    <td><?php echo $count; ?></td>
                                                                    <td>{{ $de['level'] }}</td>
                                                                    <td>{{ $de['levelName'] }}</td>
                                                                    <td>{{ $de['school'] }}</td>
                                                                    <td>{{ $de['fieldOfStudy'] }}</td>
                                                                    <td>{{ $de['graduatedYear'] }}</td>
                                                                    <td>{{ $de['grade'] }}</td>
                                                                    <td>{{ $de['modeOfStudy'] }}</td>
                                                                @endif
                                                            </tr>
                                                            <?php $count++; ?>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="button" class="btn btn-primary btn-sm" onclick="history.back();" value="Back">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- End Account Information --}}

        <!-- content-wrapper ends -->
        
    </div>
    <!-- partial:partials/_footer.html -->
    @include('main._partials.footer')
    <!-- partial -->
@endsection
