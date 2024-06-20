@extends('main._layouts.master')
@section('content')
<div class="content-wrapper">
  <div class="row">
    <div class="col-md-12 grid-margin">
      <div class="row">
        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
          <h3 class="font-weight-bold">Thông tin tài khoản</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title text-center text-uppercase">Welcome <?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?></h4>
          <form class="forms-sample">
            @csrf
            <div class="container-xl px-4 mt-4">
              <!-- Account page navigation-->
              <div class="row">
                  <div class="col-xl-4">
                      <!-- Profile picture card-->
                      <div class="card mb-4 mb-xl-0">
                          <div class="card-header">Photo</div>
                          <div class="card-body text-center">
                              <!-- Profile picture image-->
                              @if(!empty($staff['photo']))
                                <img class="img-account-profile rounded-circle mb-2" src="{{ asset($staff['photo']) }}" alt="Display_photo" width="250px" height="250px">
                                <input type="hidden" name="current_account_image" value="{{ asset($staff['photo']) }}">
                              @endif
                              <!-- Profile picture help block-->
                          </div>
                      </div>
                  </div>
                  <div class="col-xl-8">
                      <!-- Account details card-->
                      <div class="card mb-4">
                          <div class="card-header">Employee's Information</div>
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
                                            <input class="form-control" name="dob" type="text" value="@php use Carbon\Carbon;  $date = Carbon::parse($staff['dob'],"Asia/Ho_Chi_Minh")->format('d/m/Y');
                                               echo $date; @endphp" readonly>
                                      </div>
                                  </div>
                                  <!-- Form Row-->
                                  <div class="row gx-3 mb-3">
                                      <!-- Form Group (email)-->
                                      <div class="col-md-6">
                                          <label class="small mb-1" for="gender">Gender</label>
                                          <input class="form-control"  name="gender"  type="text" value="<?php echo $staff['gender'] == 1 ? 'Nam' : 'Nữ'; ?>" readonly>
                                      </div>
                                      <!-- Form Group (last name)-->
                                      <div class="col-md-6">
                                          <label class="small mb-1" for="department">Department</label>
                                          <input class="form-control" name="department" type="text"  value="<?php echo $staff['department_name']; ?>" readonly>
                                      </div>
                                  </div>
                                  <!-- Form Row  -->
                                  <div class="row gx-3 mb-3">
                                      <!-- Form Group (email)-->
                                      <div class="col-md-6">
                                          <label class="small mb-1" for="email">Email</label>
                                          <input class="form-control"  name="email"  type="text" value="{{$staff['email']}}" readonly>
                                      </div>
                                      <!-- Form Group (last name)-->
                                      <div class="col-md-6">
                                          <label class="small mb-1" for="phoneNumber">Phone Number</label>
                                          <input class="form-control" name="phoneNumber" type="text"  value="<?php echo $staff['phone_number']; ?>" readonly>
                                      </div>
                                  </div>
                                  <!-- Form Row  -->
                                  <div class="row gx-3 mb-3">
                                    <!-- Form Group (organization name)-->
                                    <div class="col-md-6">
                                      <label class="small mb-1" for="joinedAt">Joined Date</label>
                                      <input class="form-control" name="joinedAt" type="text"  value="@php   $joinedDate = Carbon::parse($staff['joined_at'],"Asia/Ho_Chi_Minh")->format('d/m/Y');
                                               echo $joinedDate; @endphp" readonly>
                                    </div>
                                    <!-- Form Group (location)-->
                                    <div class="col-md-6">
                                      <label class="small mb-1" for="status">Status</label>
                                      <input class="form-control" name="status" type="text" value="{{$staff['off_date'] == null ? 'Enable' : 'Disable'}}" readonly>
                                    </div>
                                  </div>
                                  <input type="button" class="btn btn-primary btn-sm" onclick="history.back();" value="Back">
                              </form>
                          </div>
                      </div>
                  </div>
              </div>
            </div>              
          </form>
        </div>
      </div>
    </div>
  </div>
  {{-- End Account Information --}}

  <!-- content-wrapper ends -->
  <!-- partial:partials/_footer.html --> 
  @include('main._partials.footer')
  <!-- partial -->
</div>
@endsection