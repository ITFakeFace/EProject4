<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DateTime;
use Illuminate\Support\Facades\Log;

class TimeleaveController extends Controller
{
  public function index(Request $request)
  {
    $params_get_department = [
      'id' => auth()->user()->id,
    ];
    $response_get_department = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params_get_department);
    $body_get_department = json_decode($response_get_department->body(), true);

    $user = auth()->user();

    $month = $request->input('month');
    $year = $request->input('year');
    if (!$month) {
      $month = date("m");
    }
    if (!$year) {
      $year = date("Y");
    }

    $date = $year . '-' . $month . '-' . '01';
    $data_request = ['staff_id' => $user->id, 'day_time_leave' => $date];

    $response = Http::post('http://localhost:8888/time-leave/list', $data_request);
    $body = json_decode($response->body(), true);

    $data_request_leave_other = ['staff_id' => $user->id, 'month_get' => $date];

    $response = Http::get('http://localhost:8888/leave-other/list', $data_request_leave_other);
    $leave_other = json_decode($response->body(), true);

    return view('main.time_leave.index')
      ->with('data', $body['data'])
      ->with('leave_other', $leave_other['data'])
      ->with('year', $year)
      ->with('month', $month)
      ->with('staff', $body_get_department['data'])
      ->with('breadcrumbs', [['text' => 'Paid Leave', 'url' => '../view-menu/time-leave'], ['text' => 'Add additional attendance', 'url' => '#']]);
  }

  public function createTime(Request $request)
  {
    $user = auth()->user();

    $day_leave = $request->input('day_leave');
    $number_day_leave = $request->input('number_day_leave');
    $note_bsc = $request->input('note_bsc');

    if ($day_leave > date('Y-m-d')) {
      return redirect()->back()->with('error', 'Cannot add attendance before the current date');
    }

    $date1 = date_create($day_leave);
    $date2 = date_create(date('Y-m-d'));
    $diff = date_diff($date1, $date2);
    if ($diff->format("%a") > 1) {
      return redirect()->back()->with('error', 'Cannot add attendance more than 2 days from the current date');
    }

    if (strlen($note_bsc) > 300) {
      return redirect()->back()->with('error', 'Reason cannot exceed 300 characters');
    }
    //Photo
    $now = Carbon::now();
    $image_time = '';

    if (request()->hasFile('txtImage')) {
      // random name cho ảnh
      $file_name_random = function ($key) {
        $ext = request()->file($key)->getClientOriginalExtension();
        $str_random = (string)Str::uuid();

        return $str_random . '.' . $ext;
      };

      $image = $file_name_random('txtImage');
      if (request()->file('txtImage')->move('./images/time_leave/' . $now->format('dmY') . '/', $image)) {
        // gán path ảnh vào model để lưu
        $image_time = '/images/time_leave/' . $now->format('dmY') . '/' . $image;
      }
    }

    if ($number_day_leave == 1)
      $time = "08:00:00";
    else
      $time = "04:00:00";

    $is_approved = 0;
    if ($user->is_manager == 1) {
      $is_approved = 2;
    }

    $data_request = [
      "staff_id" => $user->id,
      'staff_code' => $user->code,
      'day_time_leave' => $day_leave,
      'time' => $time,
      'image' => $image_time,
      'type' => false,
      'note' => $note_bsc,
      'is_approved' => $is_approved,
      'created_at' => date('Y-m-d')
    ];

    $response = Http::post('http://localhost:8888/time-leave/add', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['message'] == "Save success") {
      if ($user->is_manager == 1) {
        return redirect()->back()->with('success', 'Additional Attendance added successfully! As a manager, the additional attendance is automatically approved');
      } else {
        return redirect()->back()->with('success', 'Additional Attendance added successfully! Please wait for manager approval');
      }
    } else if ($body['data'] == "Added time") {
      return redirect()->back()->with('error', 'Failed to add additional attendance! You have already worked and checked in on ' . $day_leave . '! Please make corrections');
    } else {
      return redirect()->back()->with('error', 'Failed to add additional attendance! You have already added additional attendance or request for paid leave on ' . $day_leave . '! Please make corrections');
    }
  }

  public function deleteTime(Request $request)
  {
    $id = $request->input('id');

    $data_request = [
      "id" => $id
    ];

    Http::post('http://localhost:8888/time-leave/delete', $data_request);

    return redirect()->back()->with('success', 'Deletion successful!');
  }

  public function detailTime(Request $request)
  {
    $id = $request->input('id');

    $data_request = [
      "id" => $id
    ];

    $response = Http::get('http://localhost:8888/time-leave/detail', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['data']['time'] == '08:00:00') {
      $option = '
                <option value="1" selected>A day</option>
                <option value="0.5">Half day</option>
            ';
    } else {
      $option = '
                <option value="1">A day</option>
                <option value="0.5" selected>Half day</option>
            ';
    }


    $html = "<input type='hidden' name='id_update' value='" . $id . "'>";
    $html .= "<input type='hidden' name='type_update' value='" . $body['data']['type'] . "'>";
    $html .= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Add Attendance</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    $html .= '<span aria-hidden="true">&times;</span></button></div>';
    $html .= '
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Date of Attendance:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control day_leave_update" name="day_leave_update" value="' . $body['data']['dayTimeLeave'] . '" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Request for edit:</label>
                    <div class="col-lg-9">
                        <select class="form-control" name="number_day_leave_update" id="number_day_leave_update" required>
                            ' . $option . '
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Old Photo:</label>
                    <div class="col-lg-9">
                        <img src="..' . $body['data']['image'] . '" alt="" style="max-height: 250px; max-width: 200px">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">New Photo:</label>
                    <div class="col-lg-9">
                        <input type="file" class="" name="txtImage">
                        <input type="hidden" class="" name="txtImageOld" value="' . $body['data']['image'] . '">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Lý do:</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="note_bsc_update" id="note_bsc_update" cols="20" rows="5" placeholder="E.g: Forgot to check-in, check-out, ..." required>' . $body['data']['note'] . '</textarea>
                    </div>
                </div>

                <div class="des-bsc">
                    <h3>Details</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Maximum number of additional working days per request</b>
                                <p>1 day or 0.5 day / per additional attendance</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Additional Attendance Information</b>
                                <p>
                                    <b>1. Explanation: </b>Employees use it to add attendance for days they have worked but forgot to check in or out. It will be compensated if approved by department managers and directors. <br>
                                    <b>2. Applicable to: </b> Employees who have signed official contracts with the company. <br>
                                    <b>3. Required documents: </b> None. <br>
                                    <b>4. Salary: </b> The company will pay the salary for the days worked but forgot to check in.
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Edit</button>
            </div>

            <script>
                $(".day_leave_update").daterangepicker({
                    singleDatePicker: true,
                    locale: {
                        format: "YYYY-MM-DD"
                    }
                });
            </script>
        ';

    echo $html;
    die;
  }

  public function updateTime(Request $request)
  {
    $user = auth()->user();

    $id_update = $day_leave = $request->input('id_update');
    $day_leave = $request->input('day_leave_update');
    $number_day_leave = $request->input('number_day_leave_update');
    $note_bsc = $request->input('note_bsc_update');
    $image_time = $request->input('txtImageOld') ? $request->input('txtImageOld') : '';
    $type = $request->input('type_update');

    if ($type == 0) {
      if ($day_leave > date('Y-m-d')) {
        return redirect()->back()->with('error', 'Cannot add additional attendance before the current date');
      }

      $date1 = date_create($day_leave);
      $date2 = date_create(date('Y-m-d'));
      $diff = date_diff($date1, $date2);
      if ($diff->format("%a") > 1) {
        return redirect()->back()->with('error', 'Cannot add additional attendance more than 2 days from the current date');
      }
    }

    if (strlen($note_bsc) > 300) {
      return redirect()->back()->with('error', 'Reason cannot exceed 300 characters');
    }

    //Photo
    $now = Carbon::now();

    if (request()->hasFile('txtImage')) {
      // random name cho ảnh
      $file_name_random = function ($key) {
        $ext = request()->file($key)->getClientOriginalExtension();
        $str_random = (string)Str::uuid();

        return $str_random . '.' . $ext;
      };

      $image = $file_name_random('txtImage');
      if (request()->file('txtImage')->move('./images/time_leave/' . $now->format('dmY') . '/', $image)) {
        // gán path ảnh vào model để lưu
        $image_time = '/images/time_leave/' . $now->format('dmY') . '/' . $image;
      }
    }

    if ($number_day_leave == 1)
      $time = "08:00:00";
    else
      $time = "04:00:00";

    $check_special_day = [
      'day_check' => $day_leave
    ];

    $response = Http::get('http://localhost:8888/special-date/check-day', $check_special_day);
    $body = json_decode($response->body(), true);

    if ($body['data'] == "Yes") {
      return redirect()->back()->with('error', 'Update failed! ' . $day_leave . ' is a holiday! Please make changes');
    }

    $data_request = [
      "id" => $id_update,
      "staff_id" => $user->id,
      'day_time_leave' => $day_leave,
      'time' => $time,
      'note' => $note_bsc,
      'image' => $image_time,
    ];

    $response = Http::post('http://localhost:8888/time-leave/update', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['message'] == "Update success") {
      return redirect()->back()->with('success', 'Edit successful! Please wait for approval');
    } else if ($body['data'] == "Added time") {
      return redirect()->back()->with('error', 'Failed to add additional attendance / leave! You have already worked and checked in on ' . $day_leave . '! Please make changes');
    } else {
      return redirect()->back()->with('error', 'Edit failed! You have already added attendance / leave on ' . $day_leave . '!');
    }
  }

  //Phep
  public function createLeave(Request $request)
  {
    $user = auth()->user();

    $type_of_leave = $request->input('type_of_leave');

    if ($type_of_leave == 0) {
      if ($user->day_of_leave == 0) {
        return redirect()->back()->with('error', 'You have run out of annual leave days');
      }

      $day_leave = $request->input('day_leave');
      $number_day_leave = $request->input('number_day_leave');
      $note_dkp = $request->input('note_dkp');

      if (strlen($note_dkp) > 300) {
        return redirect()->back()->with('error', 'Reason cannot exceed 300 characters');
      }

      if ($number_day_leave == 1)
        $time = "08:00:00";
      else
        $time = "04:00:00";

      $is_approved = 0;
      if ($user->is_manager == 1) {
        $is_approved = 2;
      }

      if (date('w', strtotime($day_leave)) == 6 or date('w', strtotime($day_leave)) == 0) {
        return redirect()->back()->with('error', 'Leave registration failed! ' . $day_leave . ' is a Saturday / Sunday! Please make changes');
      }

      $check_special_day = [
        'day_check' => $day_leave
      ];

      $response = Http::get('http://localhost:8888/special-date/check-day', $check_special_day);
      $body = json_decode($response->body(), true);

      if ($body['data'] == "Yes") {
        return redirect()->back()->with('error', 'Leave registration failed! ' . $day_leave . ' is a holiday! Please make changes');
      }

      $data_request = [
        "staff_id" => $user->id,
        'staff_code' => $user->code,
        'day_time_leave' => $day_leave,
        'time' => $time,
        'type' => true,
        'note' => $note_dkp,
        'is_approved' => $is_approved
      ];

      $response = Http::post('http://localhost:8888/time-leave/addLeave', $data_request);
      $body = json_decode($response->body(), true);

      if ($body['message'] == "Save success") {
        if ($user->is_manager == 1) {
          return redirect()->back()->with('success', 'Leave registration successful! As a manager, the leave request is automatically approved');
        } else {
          return redirect()->back()->with('success', 'Leave registration successful! Please wait for approval');
        }
      } else if ($body['data'] == "Added time") {
        return redirect()->back()->with('error', 'Leave registration failed! You have already worked and clocked in on ' . $day_leave . '! Please make changes');
      } else {
        return redirect()->back()->with('error', 'Leave registration failed! You have already registered leave / added attendance on ' . $day_leave . '! Please make changes');
      }
    } else {
      $day_leave_from = $request->input('day_leave_from');
      $day_leave_to = $request->input('day_leave_to');
      $image_leave = $request->input('image_leave');
      $note_dkp = $request->input('note_dkp');

      if ($day_leave_from > $day_leave_to) {
        return redirect()->back()->with('error', 'Start date cannot be later than end date');
      }

      $data_check = [
        "staff_id" => $user->id,
        'day_leave_from' => $day_leave_from,
        'day_leave_to' => $day_leave_to
      ];

      $response = Http::post('http://localhost:8888/leave-other/check-list-time-leave', $data_check);
      $time_leave_exists = json_decode($response->body(), true);

      if (count($time_leave_exists['data']) > 0) {
        return redirect()->back()->with('error', 'There are already additional attendance or other leave days affecting the requested leave period! Please try again');
      }

      if ($type_of_leave == 6 or $type_of_leave == 7) {
        $day_from_check = $day_leave_from;
        if (date('w', strtotime($day_from_check)) == 6 or date('w', strtotime($day_from_check)) == 0) {
          return redirect()->back()->with('error', 'Cannot set wedding leave or bereavement leave on Saturday / Sunday! Please make changes');
        }
        while ($day_from_check <= $day_leave_to) {
          if (date('w', strtotime($day_from_check)) == 6 or date('w', strtotime($day_from_check)) == 0) {
            return redirect()->back()->with('error', 'Cannot set leave days containing Saturday / Sunday! Please make changes');
          }
          $day_from_check = date('Y-m-d', strtotime($day_from_check . ' + 1 days'));
        }
      }

      // Validate day of other leave
      switch ($type_of_leave) {
        case '2':
          $origin = new DateTime($day_leave_from);
          $target = new DateTime($day_leave_to);
          $interval = $origin->diff($target);
          if ($interval->format('%a') > 32) {
            return redirect()->back()->with('error', 'Unpaid leave type can only be registered for up to 31 days');
          }
          break;
        case '3':
          $origin = new DateTime($day_leave_from);
          $target = new DateTime($day_leave_to);
          $interval = $origin->diff($target);
          if ($interval->format('%a') > 2) {
            return redirect()->back()->with('error', 'Short sick leave type can only be registered for up to 3 days');
          }
          break;
        case '4':
          $origin = new DateTime($day_leave_from);
          $target = new DateTime($day_leave_to);
          $interval = $origin->diff($target);
          if ($interval->format('%a') > 32) {
            return redirect()->back()->with('error', 'Long sick leave type can only be registered for up to 31 days');
          }
          break;
        case '5':
          $origin = new DateTime($day_leave_from);
          $target = new DateTime($day_leave_to);
          $interval = $origin->diff($target);
          if ($interval->format('%a') > 184) {
            return redirect()->back()->with('error', 'Long-term sick leave type can only be registered for up to 6 months');
          }
          break;
        case '6':
          $origin = new DateTime($day_leave_from);
          $target = new DateTime($day_leave_to);
          $interval = $origin->diff($target);
          if ($interval->format('%a') > 2) {
            return redirect()->back()->with('error', 'Wedding leave type can only be registered for up to 3 days');
          }
          break;
        case '7':
          $origin = new DateTime($day_leave_from);
          $target = new DateTime($day_leave_to);
          $interval = $origin->diff($target);
          if ($interval->format('%a') > 2) {
            return redirect()->back()->with('error', 'Bereavement leave type can only be registered for up to 3 days');
          }
          break;
        default:
          # code...
          break;
      }

      if (strlen($note_dkp) > 300) {
        return redirect()->back()->with('error', 'Reason cannot exceed 300 characters');
      }

      $is_approved = 0;
      if ($user->is_manager == 1) {
        $is_approved = 2;
      }

      // Photo
      $now = Carbon::now();

      if (request()->hasFile('image_leave')) {
        // Generate random name for the image
        $file_name_random = function ($key) {
          $ext = request()->file($key)->getClientOriginalExtension();
          $str_random = (string)Str::uuid();

          return $str_random . '.' . $ext;
        };

        $image = $file_name_random('image_leave');
        if (request()->file('image_leave')->move('./images/other_leave/' . $now->format('dmY') . '/', $image)) {
          // Assign image path to model for saving
          $image_time = '/images/other_leave/' . $now->format('dmY') . '/' . $image;
        }
      } else {
        return redirect()->back()->with('error', 'Please upload an image');
      }

      $data_request = [
        'id_update' => null,
        "staff_id" => $user->id,
        'type_leave' => $type_of_leave,
        'day_leave_from' => $day_leave_from,
        'day_leave_to' => $day_leave_to,
        'image' => $image_time,
        'note' => $note_dkp,
        'is_approved' => $is_approved,
        'created_at' => date("Y-m-d")
      ];

      Log::info(json_encode($data_request));
      $response = Http::post('http://localhost:8888/leave-other/add', $data_request);
      $body = json_decode($response->body(), true);

      if ($body['message'] == "Save success") {
        if ($user->is_manager == 1) {
          return redirect()->back()->with('success', 'Leave registration successful! As a manager, the leave request is automatically approved');
        } else {
          return redirect()->back()->with('success', 'Leave registration successful! Please wait for approval');
        }
      } else if ($body['data'] == "Added time") {
        return redirect()->back()->with('error', 'Leave registration failed! You have already worked and clocked in on the days you requested leave for! Please make changes');
      } else if ($body['data'] == "Duplicate leave") {
        return redirect()->back()->with('error', 'Leave registration failed! You cannot overlap leave requests! Please make changes');
      } else {
        return redirect()->back()->with('error', 'Leave registration failed!' . json_encode($body));
      }
    }
  }


  public function detailLeave(Request $request)
  {
    $id = $request->input('id');

    $data_request = [
      "id" => $id
    ];

    $response = Http::get('http://localhost:8888/time-leave/detail', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['data']['time'] == '08:00:00') {
      $option = '
              <option value="1" selected>One day</option>
              <option value="0.5">Half day</option>
          ';
    } else {
      $option = '
              <option value="1">One day</option>
              <option value="0.5" selected>Half day</option>
          ';
    }

    $html = "<input type='hidden' name='id_update' value='" . $id . "'>";
    $html .= "<input type='hidden' name='type_update' value='" . $body['data']['type'] . "'>";
    $html .= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Leave Registration</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    $html .= '<span aria-hidden="true">&times;</span></button></div>';
    $html .= '
          <div class="modal-body">
              <div class="form-group row">
                  <label class="col-lg-3 col-form-label">Leave type:</label>
                  <div class="col-lg-9 col-form-label">
                      Annual leave with salary deduction
                  </div>
              </div>
              <div class="form-group row">
                  <label class="col-lg-3 col-form-label">Leave registration date:</label>
                  <div class="col-lg-9">
                      <input type="text" class="form-control day_leave_update" name="day_leave_update" value="' . $body['data']['dayTimeLeave'] . '" required>
                  </div>
              </div>
              <div class="form-group row">
                  <label class="col-lg-3 col-form-label">Leave request:</label>
                  <div class="col-lg-9">
                      <select class="form-control" name="number_day_leave_update" id="number_day_leave_update" required>
                          ' . $option . '
                      </select>
                  </div>
              </div>
  
              <div class="form-group row">
                  <label class="col-lg-3 col-form-label">Reason:</label>
                  <div class="col-lg-9">
                      <textarea class="form-control" name="note_bsc_update" id="note_bsc_update" cols="20" rows="10" placeholder="E.g. Family obligations, Study, ..." required>' . $body['data']['note'] . '</textarea>
                  </div>
              </div>
  
              <div class="des-leave des-leave0">
                  <h3>Detailed description</h3>
                  <table class="table table-bordered">
                      <tr>
                          <td>
                              <b>Maximum leave days per registration</b>
                              <p>1 day / 1 registration</p>
                          </td>
                      </tr>
                      <tr>
                          <td>
                              <b>Leave information</b>
                              <p>
                                  <b>1. Explanation: </b>Employees use annual leave days for personal use. <br>
                                  <b>2. Applicable to: </b> Employees who have signed an official contract with the company. <br>
                                  <b>3. Request documentation: </b> None. <br>
                                  <b>4. Salary: </b> The company pays salary for the leave days.
                              </p>
                          </td>
                      </tr>
                  </table>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Edit</button>
          </div>
  
          <script>
              $(".day_leave_update").daterangepicker({
                  singleDatePicker: true,
                  locale: {
                      format: "YYYY-MM-DD"
                  }
              });
          </script>
      ';

    echo $html;
    die;
  }


  public function detailLeaveOther(Request $request)
  {
    $id = $request->input('id');

    $data_request = [
      "id" => $id
    ];

    $response = Http::get('http://localhost:8888/leave-other/get-detail', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['data']['typeLeave'] == 2) {
      $option1 = '<option value="2" selected>Unpaid Leave</option>';
      $display1 = '';
    } else {
      $option1 = '<option value="2">Unpaid Leave</option>';
      $display1 = 'style="display: none"';
    }

    if ($body['data']['typeLeave'] == 3) {
      $option2 = '<option value="3" selected>Short-term Sick Leave</option>';
      $display2 = '';
    } else {
      $option2 = '<option value="3">Short-term Sick Leave</option>';
      $display2 = 'style="display: none"';
    }

    if ($body['data']['typeLeave'] == 4) {
      $option3 = '<option value="4" selected>Long-term Sick Leave</option>';
      $display3 = '';
    } else {
      $option3 = '<option value="4">Long-term Sick Leave</option>';
      $display3 = 'style="display: none"';
    }

    if ($body['data']['typeLeave'] == 5) {
      $option4 = '<option value="5" selected>Maternity Leave</option>';
      $display4 = '';
    } else {
      $option4 = '<option value="5">Maternity Leave</option>';
      $display4 = 'style="display: none"';
    }

    if ($body['data']['typeLeave'] == 6) {
      $option6 = '<option value="6" selected>Marriage Leave</option>';
      $display6 = '';
    } else {
      $option6 = '<option value="6">Marriage Leave</option>';
      $display6 = 'style="display: none"';
    }

    if ($body['data']['typeLeave'] == 7) {
      $option7 = '<option value="7" selected>Bereavement Leave</option>';
      $display7 = '';
    } else {
      $option7 = '<option value="7">Bereavement Leave</option>';
      $display7 = 'style="display: none"';
    }

    $html = "<input type='hidden' name='id_update' value='" . $id . "'>";
    $html .= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Register Leave</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    $html .= '<span aria-hidden="true">&times;</span></button></div>';
    $html .= '
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Type of Leave:</label>
                    <div class="col-lg-9">
                        <select class="form-control type_of_leave" name="type_of_leave" id="type_of_leave" required>
                            ' . $option1 . '
                            ' . $option2 . '
                            ' . $option3 . '
                            ' . $option4 . '
                            ' . $option6 . '
                            ' . $option7 . '
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">From Date:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control day_leave_update" name="day_leave_from" value="' . $body['data']['fromDate'] . '" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">To Date:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control day_leave_update" name="day_leave_to" value="' . $body['data']['toDate'] . '" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Old Image:</label>
                    <div class="col-lg-9">
                        <img src="..' . $body['data']['image'] . '" alt="" style="max-height: 250px; max-width: 200px">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">New Image:</label>
                    <div class="col-lg-9">
                        <input type="file" class="" name="image_leave">
                        <input type="hidden" class="" name="txtImageOld" value="' . $body['data']['image'] . '">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Reason:</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="note_bsc_update" id="note_bsc_update" cols="20" rows="3" placeholder="e.g., Family matters, Studying, ..." required>' . $body['data']['note'] . '</textarea>
                    </div>
                </div>

                <div class="des-leave des-leave2" ' . $display1 . '>
                    <h3>Detailed Description</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Maximum leave days per instance</b>
                                <p>1 month</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Leave Information</b>
                                <p>
                                    <b>1. Explanation: </b>Employees who have used up their annual leave in one cycle and do not meet the conditions for using other types of leave (paid personal leave, insurance leave). <br>
                                    <b>2. Applicable to: </b> Applies to all employees who need to take personal leave (grandparents passed away, sick leave without a doctor\'s note and insurance leave, military service examination leave...) <br>
                                    <b>3. Required documents: </b> None. <br>
                                    <b>4. Salary: </b> No salary is paid for leave days. <br>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="des-leave des-leave3" ' . $display2 . '>
                    <h3>Detailed Description</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Maximum leave days per instance</b>
                                <p>7 days</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Leave Information</b>
                                <p>
                                    <b>1. Explanation: </b>Self sick leave as prescribed by the doctor and certified by the hospital with a social insurance leave form (C65) or a discharge certificate during the leave period. <br>
                                    <b>2. Applicable to: </b> Employees participating in mandatory insurance at the company. <br>
                                    <b>3. Required documents: </b> Original social insurance leave form (C65) / original discharge certificate must be submitted. The social insurance agency will only pay salary for leave days if all required documents are properly submitted to the company. <br>
                                    <b>4. Salary: </b> The social insurance agency calculates & pays the salary for leave days based on the submitted documents (calculated based on the monthly mandatory insurance salary). <br>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="des-leave des-leave4" ' . $display3 . '>
                    <h3>Detailed Description</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Maximum leave days per instance</b>
                                <p>1 month</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Leave Information</b>
                                <p>
                                    <b>1. Explanation: </b>Only applies to individuals with illnesses requiring long-term treatment as listed by the Ministry of Health and prescribed by doctors and registered hospitals. <br>
                                    <b>2. Applicable to: </b> Employees participating in mandatory insurance at the company. <br>
                                    <b>3. Required documents: </b> Original discharge certificate for inpatient treatment; Certified hospital consultation record (original or notarized copy) and original treatment session certificate for outpatient treatment. The social insurance agency will only pay salary for leave days if all required documents are properly submitted to the company. <br>
                                    <b>4. Salary: </b> The social insurance agency calculates & pays the salary for leave days based on the submitted documents (calculated based on the monthly mandatory insurance salary). <br>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="des-leave des-leave5" ' . $display4 . '>
                    <h3>Detailed Description</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Maximum leave days per instance</b>
                                <p>6 months</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Leave Information</b>
                                <p>
                                    <b>1. Explanation: </b>Only applies to pregnant employees with a pregnancy certificate or certificate of childbirth. <br>
                                    <b>2. Applicable to: </b> Employees participating in mandatory insurance at the company. <br>
                                    <b>3. Required documents: </b> Certificate of pregnancy or certificate of childbirth. <br>
                                    <b>4. Salary: </b> The social insurance agency calculates & pays the salary for leave days based on the submitted documents (calculated based on the monthly mandatory insurance salary). <br>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="des-leave des-leave6" ' . $display6 . '>
                    <h3>Detailed Description</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Maximum leave days per instance</b>
                                <p>3 days</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Leave Information</b>
                                <p>
                                    <b>1. Explanation: </b>Only applies to employees getting married and requires a marriage certificate or wedding invitation. <br>
                                    <b>2. Applicable to: </b> All employees of the company. <br>
                                    <b>3. Required documents: </b> Marriage certificate or wedding invitation. <br>
                                    <b>4. Salary: </b> No salary is paid for leave days. <br>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="des-leave des-leave7" ' . $display7 . '>
                    <h3>Detailed Description</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Maximum leave days per instance</b>
                                <p>3 days</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Leave Information</b>
                                <p>
                                    <b>1. Explanation: </b>Only applies to employees experiencing the death of an immediate family member. <br>
                                    <b>2. Applicable to: </b> All employees of the company. <br>
                                    <b>3. Required documents: </b> Death certificate of the family member. <br>
                                    <b>4. Salary: </b> No salary is paid for leave days. <br>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>';

    return response()->json($html);
  }

  public function updateLeaveOther(Request $request)
  {
    $user = auth()->user();

    $type_of_leave = $request->input('type_of_leave');
    $id_update = $request->input('id_update');
    $note_bsc = $request->input('note_bsc_update');
    $image_time = $request->input('txtImageOld') ? $request->input('txtImageOld') : '';
    $day_leave_from = $request->input('day_leave_from');
    $day_leave_to = $request->input('day_leave_to');

    if ($day_leave_from > $day_leave_to) {
      return redirect()->back()->with('error', 'From date cannot be greater than to date');
    }

    $data_check = [
      "staff_id" => $user->id,
      'day_leave_from' => $day_leave_from,
      'day_leave_to' => $day_leave_to
    ];

    $response = Http::post('http://localhost:8888/leave-other/check-list-time-leave', $data_check);
    $time_leave_exists = json_decode($response->body(), true);

    if (count($time_leave_exists['data']) > 0) {
      return redirect()->back()->with('error', 'There is already a supplementary work or annual leave registration for the days you have registered! Please try again');
    }

    if ($type_of_leave == 6 or $type_of_leave == 7) {
      $day_from_check = $day_leave_from;
      if (date('w', strtotime($day_from_check)) == 6 or date('w', strtotime($day_from_check)) == 0) {
        return redirect()->back()->with('error', 'Cannot register leave containing Saturday/Sunday! Please adjust');
      }
      while ($day_from_check <= $day_leave_to) {
        if (date('w', strtotime($day_from_check)) == 6 or date('w', strtotime($day_from_check)) == 0) {
          return redirect()->back()->with('error', 'Cannot register leave containing Saturday/Sunday! Please adjust');
        }
        $day_from_check = date('Y-m-d', strtotime($day_from_check . ' + 1 days'));
      }
    }

    // Validate day of other leave
    switch ($type_of_leave) {
      case '2':
        $origin = new DateTime($day_leave_from);
        $target = new DateTime($day_leave_to);
        $interval = $origin->diff($target);
        if ($interval->format('%a') > 32) {
          return redirect()->back()->with('error', 'Unpaid leave can only be registered for a maximum of 31 days');
        }
        break;
      case '3':
        $origin = new DateTime($day_leave_from);
        $target = new DateTime($day_leave_to);
        $interval = $origin->diff($target);
        if ($interval->format('%a') > 2) {
          return redirect()->back()->with('error', 'Short-term sick leave can only be registered for a maximum of 3 days');
        }
        break;
      case '4':
        $origin = new DateTime($day_leave_from);
        $target = new DateTime($day_leave_to);
        $interval = $origin->diff($target);
        if ($interval->format('%a') > 32) {
          return redirect()->back()->with('error', 'Long-term sick leave can only be registered for a maximum of 31 days');
        }
        break;
      case '5':
        $origin = new DateTime($day_leave_from);
        $target = new DateTime($day_leave_to);
        $interval = $origin->diff($target);
        if ($interval->format('%a') > 184) {
          return redirect()->back()->with('error', 'Maternity leave can only be registered for a maximum of 6 months');
        }
        break;
      case '6':
        $origin = new DateTime($day_leave_from);
        $target = new DateTime($day_leave_to);
        $interval = $origin->diff($target);
        if ($interval->format('%a') > 2) {
          return redirect()->back()->with('error', 'Marriage leave can only be registered for a maximum of 3 days');
        }
        break;
      case '7':
        $origin = new DateTime($day_leave_from);
        $target = new DateTime($day_leave_to);
        $interval = $origin->diff($target);
        if ($interval->format('%a') > 2) {
          return redirect()->back()->with('error', 'Bereavement leave can only be registered for a maximum of 3 days');
        }
        break;
      default:
        break;
    }

    if (strlen($note_bsc) > 300) {
      return redirect()->back()->with('error', 'The reason cannot exceed 300 characters');
    }

    $is_approved = 0;
    if ($user->is_manager == 1) {
      $is_approved = 2;
    }

    // Photo
    $now = Carbon::now();

    if (request()->hasFile('image_leave')) {
      // Generate a random name for the image
      $file_name_random = function ($key) {
        $ext = request()->file($key)->getClientOriginalExtension();
        $str_random = (string)Str::uuid();

        return $str_random . '.' . $ext;
      };

      $image = $file_name_random('image_leave');
      if (request()->file('image_leave')->move('./images/other_leave/' . $now->format('dmY') . '/', $image)) {
        // Assign the image path to the model to save
        $image_time = '/images/other_leave/' . $now->format('dmY') . '/' . $image;
      }
    }

    $data_request = [
      'id_update' => $id_update,
      "staff_id" => $user->id,
      'type_leave' => $type_of_leave,
      'day_leave_from' => $day_leave_from,
      'day_leave_to' => $day_leave_to,
      'image' => $image_time,
      'note' => $note_bsc,
      'is_approved' => $is_approved,
      'created_at' => date("Y-m-d")
    ];

    $response = Http::post('http://localhost:8888/leave-other/add', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['message'] == "Save success") {
      return redirect()->back()->with('success', 'Edit successful! Please wait for approval');
    } else if ($body['data'] == "Added time") {
      return redirect()->back()->with('error', 'Failed to edit leave registration! You have already worked and logged in on the days you registered for leave! Please adjust');
    } else if ($body['data'] == "Duplicate leave") {
      return redirect()->back()->with('error', 'Failed to edit leave registration! You cannot register overlapping leaves! Please adjust');
    } else {
      return redirect()->back()->with('error', 'Failed to edit leave registration!');
    }
  }


  //Approve time leave
  public function approveTimeLeave(Request $request)
  {
    $params_get_department = [
      'id' => auth()->user()->id,
    ];
    $response_get_department = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params_get_department);
    $body_get_department = json_decode($response_get_department->body(), true);

    $user = auth()->user();

    $month = $request->input('month');
    $year = $request->input('year');
    if (!$month) {
      $month = date("m");
    }
    if (!$year) {
      $year = date("Y");
    }

    $date = $year . '-' . $month . '-' . '01';
    $data_request = ['department' => $user->department, 'day_time_leave' => $date, 'is_manager' => $user->is_manager, 'staff_id' => $user->id];

    $response = Http::post('http://localhost:8888/time-leave/get-staff-approve', $data_request);
    $body = json_decode($response->body(), true);

    $response = Http::post('http://localhost:8888/leave-other/get-staff-approve', $data_request);
    $leave_other = json_decode($response->body(), true);

    return view('main.time_leave.approve')
      ->with('data', $body['data'])
      ->with('leave_other', $leave_other['data'])
      ->with('year', $year)
      ->with('month', $month)
      ->with('staff', $body_get_department['data'])
      ->with('breadcrumbs', [['text' => 'Paid leave', 'url' => '../view-menu/time-leave'], ['text' => 'Approve paid leave', 'url' => '#']]);
  }

  public function deleteLeaveOther(Request $request)
  {
    $id = $request->input('id');

    $data_request = [
      "id" => $id
    ];

    Http::post('http://localhost:8888/leave-other/delete-leave-other', $data_request);

    return redirect()->back()->with('success', 'Deletion successful!');
  }

  public function detailStaffApprove(Request $request)
  {
    $id = $request->input('id');

    $data_request = [
      "id" => $id
    ];

    $response = Http::get('http://localhost:8888/time-leave/detail-time-staff-approve', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['data'][0][3] == 0) {
      $title = 'Employee\'s additional attendance';
      $day_time_leave = 'Date of additional attendance';
    } else {
      $title = 'Employee\'s Paid leave';
      $day_time_leave = 'Date of leave';
    }

    if ($body['data'][0][2] == '08:00:00') {
      $time = 'One day';
    } else {
      $time = 'half day';
    }

    if ($body['data'][0][5] == 1) {
      $approved = '
                Director has approved
            ';
    } else if ($body['data'][0][5] == 2) {
      $approved = '
                Manager has approved
            ';
    } else {
      $approved = '
                Pending approval
            ';
    }

    $html = "<input type='hidden' name='id' value='" . $id . "'>";
    $html .= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">' . $title . '</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    $html .= '<span aria-hidden="true">&times;</span></button></div>';
    $html .= '
        <div class="modal-body">
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Employee Name:</label>
                <div class="col-lg-9 col-form-label">
                    ' . $body['data'][0][6] . ' ' . $body['data'][0][7] . '
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Employee ID:</label>
                <div class="col-lg-9 col-form-label">
                    ' . $body['data'][0][8] . '
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Department:</label>
                <div class="col-lg-9 col-form-label">
                    ' . $body['data'][0][11] . '
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Leave Type:</label>
                <div class="col-lg-9 col-form-label">
                    Paid Annual Leave
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">' . $day_time_leave . ':</label>
                <div class="col-lg-9 col-form-label">
                    ' . $body['data'][0][1] . '
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Request:</label>
                <div class="col-lg-9 col-form-label">
                     ' . $time . '
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Image:</label>
                <div class="col-lg-9">
                    <img src="..' . $body['data'][0][12] . '" alt=""  style="max-height: 250px; max-width: 200px">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Status:</label>
                <div class="col-lg-9">
                    ' . $approved . '
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Reason:</label>
                <div class="col-lg-9">
                    <textarea class="form-control" name="note_bsc_update" id="note_bsc_update" cols="20" rows="5" placeholder="e.g., Family matters, School, ..." readonly>' . $body['data'][0][4] . '</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Approve</button>
        </div>

        <script>
            $(".day_leave_update").daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: "YYYY-MM-DD"
                }
            });
        </script>
    ';


    echo $html;
    die;
  }

  public function detailOtherLeaveApprove(Request $request)
  {
    $id = $request->input('id');

    $data_request = [
      "id" => $id
    ];

    $response = Http::get('http://localhost:8888/leave-other/detail-time-staff-approve', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['data'][0][3] == 2) {
      $type_leave = 'Unpaid Leave';
    } else if ($body['data'][0][3] == 3) {
      $type_leave = 'Short-term Sick Leave';
    } else if ($body['data'][0][3] == 4) {
      $type_leave = 'Long-term Sick Leave';
    } else if ($body['data'][0][3] == 5) {
      $type_leave = 'Maternity Leave';
    } else if ($body['data'][0][3] == 6) {
      $type_leave = 'Marriage Leave';
    } else if ($body['data'][0][3] == 7) {
      $type_leave = 'Funeral Leave';
    }

    if ($body['data'][0][5] == 1) {
      $approved = '
                  Approved by Director
              ';
    } else if ($body['data'][0][5] == 2) {
      $approved = '
                  Approved by Manager
              ';
    } else {
      $approved = '
                  Not Approved
              ';
    }

    $html = "<input type='hidden' name='id' value='" . $id . "'>";
    $html .= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Employee Leave Request</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    $html .= '<span aria-hidden="true">&times;</span></button></div>';
    $html .= '
              <div class="modal-body">
                  <div class="form-group row">
                      <label class="col-lg-3 col-form-label">Employee Name:</label>
                      <div class="col-lg-9 col-form-label">
                          ' . $body['data'][0][6] . ' ' . $body['data'][0][7] . '
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-3 col-form-label">Employee ID:</label>
                      <div class="col-lg-9 col-form-label">
                          ' . $body['data'][0][8] . '
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-3 col-form-label">Department:</label>
                      <div class="col-lg-9 col-form-label">
                          ' . $body['data'][0][11] . '
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-3 col-form-label">Leave Type:</label>
                      <div class="col-lg-9 col-form-label">
                          ' . $type_leave . '
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-3 col-form-label">From Date:</label>
                      <div class="col-lg-9 col-form-label">
                          ' . $body['data'][0][1] . '
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-3 col-form-label">To Date:</label>
                      <div class="col-lg-9 col-form-label">
                          ' . $body['data'][0][2] . '
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-3 col-form-label">Image:</label>
                      <div class="col-lg-9">
                          <img src="..' . $body['data'][0][12] . '" alt=""  style="max-height: 250px; max-width: 200px">
                      </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-lg-3 col-form-label">Status:</label>
                      <div class="col-lg-9 col-form-label">
                          ' . $approved . '
                      </div>
                  </div>
  
                  <div class="form-group row">
                      <label class="col-lg-3 col-form-label">Reason:</label>
                      <div class="col-lg-9">
                          <textarea class="form-control" name="note_bsc_update" id="note_bsc_update" cols="20" rows="5" placeholder="e.g., Family matters, School, ..." readonly>' . $body['data'][0][4] . '</textarea>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Approve</button>
              </div>
  
              <script>
                  $(".day_leave_update").daterangepicker({
                      singleDatePicker: true,
                      locale: {
                          format: "YYYY-MM-DD"
                      }
                  });
              </script>
          ';

    echo $html;
    die;
  }


  public function approvedTimeLeave(Request $request)
  {
    $id = $request->input('id');
    $is_approved = 2;
    $date = null;

    if (auth()->user()->id == 7) {
      $is_approved = 1;
      $date = date('Y-m-d');
    }

    $data_request = [
      "id" => $id,
      "is_approved" => $is_approved,
      "day_approved" => $date
    ];

    $response = Http::post('http://localhost:8888/time-leave/approve-time-leave', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['message'] == "Approve success") {
      return redirect()->back()->with('success', 'Approved successfully!');
    } else {
      return redirect()->back()->with('error', 'Approval failed');
    }
  }

  public function approvedLeaveOther(Request $request)
  {
    $id = $request->input('id');
    $is_approved = 2;
    $date = null;

    if (auth()->user()->id == 7) {
      $is_approved = 1;
      $date = date('Y-m-d');
    }

    $data_request = [
      "id" => $id,
      "is_approved" => $is_approved,
      "day_approved" => $date
    ];

    $response = Http::post('http://localhost:8888/leave-other/approve-leave-other', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['message'] == "Approve success") {
      return redirect()->back()->with('success', 'Approved successfully!');
    } else {
      return redirect()->back()->with('error', 'Approval failed');
    }
  }

  public function getAllStaffTime(Request $request)
  {
    $user = auth()->user();
    $month = $request->input('month');
    $year = $request->input('year');
    if (!$month) {
      $month = date("m");
    }
    if (!$year) {
      $year = date("Y");
    }
    $date = $year . '-' . $month . '-' . '01';
    $data_request = ['y_m' => $date];

    $response = Http::get('http://localhost:8888/department/list', []);
    $body = json_decode($response->body(), true);
    $departments = [];
    if ($body['isSuccess']) {
      $departments = $body['data'];
    }
    $response = Http::get('http://localhost:8888/staff/list');
    $body = json_decode($response->body(), true);
    $staffs = $body['data'];

    $response = Http::get('http://localhost:8888/time-leave/get-all-staff-time', $data_request);
    $body = json_decode($response->body(), true);

    $response = Http::get('http://localhost:8888/time-leave/summary-staff-time', $data_request);
    $summary = json_decode($response->body(), true);

    $stfList = [];
    //process all staff not in list
    foreach ($staffs as $staff) {
      $exists = false; // Biến kiểm tra nếu staff_id tồn tại
      foreach ($summary["data"] as $stf) {
        if ($staff["id"] == $stf["staff_id"]) {
          $exists = true;
          break;
        }
      }

      if (!$exists) {
        // Thêm phần tử vào $stfList nếu staff_id không tồn tại
        $stfList[] = $staff;
      }
    }
    // dd($body['data']);

    return view('main.time_leave.all_staff_time')
      ->with('data', $body['data'])
      ->with('summary', $summary['data'])
      ->with('department', $departments)
      ->with('staffNotCheck', $stfList)
      ->with('year', $year)
      ->with('month', $month)
      ->with('y_m', $date)
      ->with('breadcrumbs', [['text' => 'Paid Leave', 'url' => '../view-menu/time-leave'], ['text' => 'Attendance Overview', 'url' => '#']]);
  }

  public function getDetailStaffTime(Request $request)
  {
    $staff_id = $request->input('staff_id');
    $month = $request->input('month');
    $year = $request->input('year');
    if (!$month) {
      $month = date("m");
    }
    if (!$year) {
      $year = date("Y");
    }
    $date = $year . '-' . $month . '-' . '01';
    $data_request = ['y_m' => $date];

    $response = Http::get('http://localhost:8888/time-leave/get-all-staff-time', $data_request);
    $body = json_decode($response->body(), true);

    $html = "";
    foreach ($body['data'] as $check_in_out) {
      if ($check_in_out['staff_id'] == $staff_id) {
        if ($check_in_out['special_date_id'] !== null) $color = "#ffe7e7";
        else if ($check_in_out['day_of_week'] == 1 or $check_in_out['day_of_week'] == 7) $color = "#d3ffd4";
        else $color = "";

        $check_in_out['is_manager'] == 1 ? $manager = "Manager" : $manager = "Staff";
        $check_in_out['day_of_week'] !== 1 ? $day_of_week = "Day" . $check_in_out['day_of_week'] : $day_of_week = "Sunday";
        $check_in_out['special_date_id'] != null ? $day_of_week .= "(Holiday)" : '';
        // $check_in_img = $check_in_out['image_check_in'] != null && $check_in_out['image_check_in'] != "null" ? "<img width='80px' src='../images/check_in/" . $check_in_out['image_check_in'] . "'/>" : "";
        // $check_out_img = $check_in_out['image_check_out'] != null && $check_in_out['image_check_out'] != "null" ? "<img width='80px' src='../images/check_in/" . $check_in_out['image_check_out'] . "'/>" : "";
        $check_in_img = "";
        $check_out_img = "";

        $html .= "
                <tr style='background-color: " . $color . "'>
                    <td>" . $check_in_out['code'] . "</td>
                    <td>" . $check_in_out['full_name'] . "</td>
                    <td>" . $check_in_out['department_name'] . "</td>
                    <td>" . $manager . "</td>
                    <td>" . $check_in_out['check_in_day'] . "</td>
                    <td>" . $day_of_week . "</td>
                    <td class='text-center' style='max-width: 100px;'>" . $check_in_out['check_in'] . $check_in_img . " </td>
                    <td class='text-center' style='max-width: 100px;'>" . $check_in_out['check_out'] . $check_out_img . " </td>
                    <td>" . ($check_in_out['in_late'] == null ? "" : $check_in_out['in_late']) . "</td>
                    <td>" . ($check_in_out['out_soon'] == null ? "" : $check_in_out['out_soon']) . "</td>
                    <td>" . $check_in_out['number_time'] * $check_in_out['multiply'] . "</td>
                    <td>" . $check_in_out['time'] . "</td>
                    <td>" . $check_in_out['ot'] . "</td>
                </tr>";
      }
    }

    echo $html;
    die;
  }

  public function getAllTimeLeave(Request $request)
  {
    $month = $request->input('month');
    $year = $request->input('year');
    if (!$month) {
      $month = date("m");
    }
    if (!$year) {
      $year = date("Y");
    }
    $date = $year . '-' . $month . '-' . '01';

    $data_request = ['y_m' => $date];

    $response = Http::get('http://localhost:8888/time-leave/summary-time-leave', $data_request);
    $summary = json_decode($response->body(), true);

    return view('main.time_leave.all_time_leave')
      ->with('summary', $summary['data'])
      ->with('year', $year)
      ->with('month', $month)
      ->with('y_m', $date)
      ->with('breadcrumbs', [['text' => 'Paid Leave', 'url' => '../view-menu/time-leave'], ['text' => 'Attendance Overview', 'url' => '#']]);
  }

  public function getDetailTimeLeave(Request $request)
  {
    $staff_id = $request->input('staff_id');
    $month = $request->input('month');
    $year = $request->input('year');
    if (!$month) {
      $month = date("m");
    }
    if (!$year) {
      $year = date("Y");
    }
    $date = $year . '-' . $month . '-' . '01';
    $data_request = ['y_m' => $date];

    $data_request = ['month_get' => $date, 'staff_id' => $staff_id];

    $response = Http::get('http://localhost:8888/time-leave/detail-time-leave-all', $data_request);
    $body = json_decode($response->body(), true);

    $html = "";
    foreach ($body['data'] as $time_leave) {
      if ($time_leave['staff_id'] == $staff_id) {
        if ($time_leave['special_date_id'] !== null) $color = "#ffe7e7";
        else if ($time_leave['day_of_week'] == 1 or $time_leave['day_of_week'] == 7) $color = "#d3ffd4";
        else $color = "";

        $time_leave['is_manager'] == 1 ? $manager = "Manager" : $manager = "Employee";
        $time_leave['day_of_week'] !== 1 ? $day_of_week = "Day " . $time_leave['day_of_week'] : $day_of_week = "Sunday";
        $time_leave['day_of_week'] == null ? $day_of_week = "" : $day_of_week = $day_of_week;
        $time_leave['special_date_id'] !== null ? $day_of_week .= "(Holiday)" : '';
        $time_leave['time'] == "08:00:00" ? $time = '1' : $time = '0.5';
        $time_leave['time'] == null ? $time = '0' : $time = $time;
        $time_leave['time'] == "08:00:00" ? $time_multi = 1 * $time_leave['multiply'] . '' : $time_multi = 0.5 * $time_leave['multiply'];
        $time_leave['time'] == null ? $time_multi = '0' : $time_multi = $time_multi;

        switch ($time_leave['type']) {
          case '1':
            $type = "Leave Registration (Paid Leave)";
            break;
          case '2':
            $type = "Leave Registration (Unpaid Leave)";
            break;
          case '3':
            $type = "Leave Registration (Short-term Sick Leave)";
            break;
          case '4':
            $type = "Leave Registration (Long-term Sick Leave)";
            break;
          case '5':
            $type = "Leave Registration (Maternity Leave)";
            break;
          case '6':
            $type = "Leave Registration (Marriage Leave)";

            $arr_from_to = explode(' đến ', $time_leave['day_time_leave']);

            $day_from_check = $arr_from_to[0];
            $time = 0;
            $time_multi = 0;
            while ($day_from_check <= $arr_from_to[1]) {
              $time += 1;
              $time_multi += 1;
              $day_from_check = date('Y-m-d', strtotime($day_from_check . ' + 1 days'));
            }
            break;
          case '7':
            $type = "Leave Registration (Funeral Leave)";

            $arr_from_to = explode(' đến ', $time_leave['day_time_leave']);

            $day_from_check = $arr_from_to[0];
            $time = 0;
            $time_multi = 0;
            while ($day_from_check <= $arr_from_to[1]) {
              $time += 1;
              $time_multi += 1;
              $day_from_check = date('Y-m-d', strtotime($day_from_check . ' + 1 days'));
            }
            break;
          default:
            $type = "Additional Attendance";
            break;
        }

        if ($time_leave['is_approved'] == 0)
          $approve = '<span class="badge badge-warning">Not approved</span>';
        elseif ($time_leave['is_approved'] == 2)
          $approve = '<span class="badge badge-success">Manager approved</span>';
        else
          $approve = '<span class="badge badge-primary">Director approved</span>';

        $html .= "
                  <tr style='background-color: " . $color . "'>
                      <td>" . $time_leave['firstname'] . ' ' . $time_leave['lastname'] . "</td>
                      <td>" . $time_leave['name_vn'] . "</td>
                      <td>" . $manager . "</td>
                      <td>" . $time_leave['day_time_leave'] . "</td>
                      <td>" . $day_of_week . "</td>
                      <td>" . $type . "</td>
                      <td>" . $time . "</td>
                      <td>" . $time_multi . "</td>
                      <td>" . $approve . "</td>
                  </tr>";
      }
    }
    echo $html;
    echo "<pre>";
    print_r($html);
    echo "</pre>";
    die;
  }


  public function doneLeave(Request $request)
  {
    $from_date = $request->input('from_date');
    $to_date = $request->input('to_date');

    if ($from_date > $to_date) {
      return redirect()->back()->with('error', 'From date cannot be greater than to date! Please try again');
    }

    $data_request = ['from_date' => $from_date, 'to_date' => $to_date];

    Http::get('http://localhost:8888/time-leave/done-leave', $data_request);

    return redirect()->back()->with('success', 'Leave closed successfully');
  }


  public function getAllTimeInMonth(Request $request)
  {
    $month = $request->input('month');
    $year = $request->input('year');
    if (!$month) {
      $month = date("m");
    }
    if (!$year) {
      $year = date("Y");
    }
    $date = $year . '-' . $month . '-' . '01';

    $data_request = ['y_m' => $date];

    $response = Http::get('http://localhost:8888/time-leave/summary-time-leave', $data_request);
    $summary_time_leave = json_decode($response->body(), true);

    $response = Http::get('http://localhost:8888/time-leave/summary-staff-time', $data_request);
    $summary_staff_time = json_decode($response->body(), true);

    $response = Http::get('http://localhost:8888/staff/findStaffDepartment');
    $body = json_decode($response->body(), true);
    $data_staff = $body['data'];

    $from = $year . '-' . $month . '-' . '01';
    $to = $year . '-' . $month . '-' . date("t");
    $data_request_time_special = ['from_date' => $from, 'to_date' => $to];

    $response = Http::get('http://localhost:8888/time-special/get-time-special-from-to?', $data_request_time_special);
    $time_specials = json_decode($response->body(), true);

    for ($i = 0; $i < count($data_staff); $i++) {
      $data_staff[$i]['total_number_time_special'] = 0;
    }

    foreach ($time_specials['data'] as $time_special) {
      for ($i = 0; $i < count($data_staff); $i++) {
        if ($time_special['staff_id'] == $data_staff[$i][3]) {
          $data_staff[$i]['total_number_time_special'] += $time_special['number_time'];
        }
      }
    }


    foreach ($summary_staff_time['data'] as $staff_time) {
      for ($i = 0; $i < count($data_staff); $i++) {
        if ($staff_time['staff_id'] == $data_staff[$i][3]) {
          $data_staff[$i]['total_number_time_all'] = $staff_time['total_number_time_all'];
        }
      }
    }

    foreach ($summary_time_leave['data'] as $staff_time) {
      for ($i = 0; $i < count($data_staff); $i++) {
        if ($staff_time['staff_id'] == $data_staff[$i][3]) {
          $data_staff[$i]['number_time_time_approved'] = $staff_time['number_time_time_approved'];
          $data_staff[$i]['number_time_leave_approved'] = $staff_time['number_time_leave_approved'];
        }
      }
    }

    for ($i = 0; $i < count($data_staff); $i++) {
      $data_staff[$i]['total'] = 0;
      if (isset($data_staff[$i]['total_number_time_all'])) {
        $data_staff[$i]['total'] += $data_staff[$i]['total_number_time_all'];
      }
      if (isset($data_staff[$i]['number_time_time_approved'])) {
        $data_staff[$i]['total'] += $data_staff[$i]['number_time_time_approved'];
      }
      if (isset($data_staff[$i]['number_time_leave_approved'])) {
        $data_staff[$i]['total'] += $data_staff[$i]['number_time_leave_approved'];
      }
      if (isset($data_staff[$i]['total_number_time_special'])) {
        $data_staff[$i]['total'] += $data_staff[$i]['total_number_time_special'];
      }
    }

    return view('main.time_leave.all_time')
      ->with('data_staff', $data_staff)
      ->with('summary_staff_time', $summary_staff_time['data'])
      ->with('summary_time_leave', $summary_time_leave['data'])
      ->with('year', $year)
      ->with('month', $month)
      ->with('breadcrumbs', [['text' => 'Paid Leave', 'url' => '../view-menu/time-leave'], ['text' => 'Monthly Overview', 'url' => '#']]);
  }
}