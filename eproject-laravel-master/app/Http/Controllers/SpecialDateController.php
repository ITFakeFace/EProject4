<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class SpecialDateController extends Controller
{
  public function index(Request $request)
  {
    $year = $request->input('year');
    $month = date("m");
    if (!$year) {
      $year = date("Y");
    }

    $date = $year . '-' . $month . '-' . '01';
    $data_request = ['special_date_from' => $date];

    $response = Http::get('http://localhost:8888/special-date/list-special-date?', $data_request);
    $body = json_decode($response->body(), true);

    $calendar = array();
    foreach ($body['data'] as $value) {
      if ($value['type_day'] == 1) {
        $arr = array();
        $arr['title'] = $value['note'];
        $arr['start'] = $value['day_special_from'];
        $arr['end'] = date("Y-m-d", strtotime('+1 days', strtotime($value['day_special_to'])));
        $arr['color'] = '#EF5350';

        array_push($calendar, $arr);
      }
    }

    return view('main.special_date.index')
      ->with('data', $body['data'])
      ->with('year', $year)
      ->with('calendar', json_encode($calendar))
      ->with('breadcrumbs', [['text' => 'Check In', 'url' => '../view-menu/time-leave'], ['text' => 'Manage Holidays', 'url' => '#']]);
  }

  public function createSpecialDate(Request $request)
  {
    $day_special_from = $request->input('day_special_from');
    $day_special_to = $request->input('day_special_to');
    $note = $request->input('note');
    $type_day = $request->input('type_day');
    $staff_ot = $request->input('staff_ot');

    if ($type_day == 2) {
      if (!$staff_ot) {
        return redirect()->back()->with('error', 'Please select staff for overtime');
      }
    }

    $date = date("Y-m-d");
    $data_request = ['special_date_from' => $date, 'staff_request' => auth()->user()->id, 'department_request' => auth()->user()->department];

    $response_check = Http::get('http://localhost:8888/special-date/get-request-ot?', $data_request);
    $body_check = json_decode($response_check->body(), true);

    if ($day_special_from < date('Y-m-d', strtotime(date("Y-m-d") . ' + 3 days'))) {
      return redirect()->back()->with('error', 'The start date must be at least 3 days from the current date! Please try again');
    }

    if ($type_day == 1) {
      $day_from_check = $day_special_from;
      if (date('w', strtotime($day_from_check)) == 6 or date('w', strtotime($day_from_check)) == 0) {
        return redirect()->back()->with('error', 'Cannot set a holiday that includes Saturday/Sunday! Please make adjustments');
      }
      while ($day_from_check <= $day_special_to) {
        if (date('w', strtotime($day_from_check)) == 6 or date('w', strtotime($day_from_check)) == 0) {
          return redirect()->back()->with('error', 'Cannot set a holiday that includes Saturday/Sunday! Please make adjustments');
        }
        $day_from_check = date('Y-m-d', strtotime($day_from_check . ' + 1 days'));
      }
    }

    foreach ($body_check['data'] as $value) {
      // if($value['type_day'] == 2 && $value['department_request'] == auth()->user()->department) {
      //     if(($value['day_special_from'] >= $day_special_from && $value['day_special_from'] <= $day_special_to) || ($value['day_special_to'] >= $day_special_from && $value['day_special_to'] <= $day_special_to)) {
      //         return redirect()->back()->with('error', 'Ngày tăng ca không được chồng chéo nhau!');
      //     }
      // }

      if ($value['type_day'] == 1) {
        if (($value['day_special_from'] >= $day_special_from && $value['day_special_from'] <= $day_special_to) || ($value['day_special_to'] >= $day_special_from && $value['day_special_to'] <= $day_special_to)) {
          if ($type_day == 1)
            return redirect()->back()->with('error', 'Holidays cannot overlap!');
          // else 
          //     return redirect()->back()->with('error', 'Ngày tăng ca không được chồng chéo ngày lễ!');
        }
      }
    }

    if ($day_special_from > $day_special_to) {
      return redirect()->back()->with('error', 'Start date cannot be greater than end date! Please try again');
    }

    if (strlen($note) > 300) {
      return redirect()->back()->with('error', 'Description cannot exceed 300 characters');
    }

    $data_request = [
      'day_special_from' => $day_special_from,
      'day_special_to' => $day_special_to,
      'note' => $note,
      'type_day' => $type_day
    ];

    if ($type_day == 2) {
      $data_request['staff_request'] = auth()->user()->id;
      $data_request['department_request'] = auth()->user()->department;
      $data_request['is_approved'] = 0;

      if ($staff_ot) {
        $string_staff_ot = implode(',', $staff_ot);

        if (strpos(implode(',', $staff_ot), "all")) {
          $string_staff_ot = "all";
        }
      }
      $data_request['string_staff_ot'] = $string_staff_ot;
    }

    $response = Http::post('http://localhost:8888/special-date/add', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['message'] == "Save SpecialDate success") {
      if ($type_day == 1)
        return redirect()->back()->with('success', 'Successfully added holiday!');
      else
        return redirect()->back()->with('success', 'Request for Overtime has been added successfully! Please wait for approval\'s director!');
    } else {
      if ($type_day == 1)
        return redirect()->back()->with('error', 'Failed to add holiday!');
      else
        return redirect()->back()->with('error', 'Request for Overtime Failed!');
    }
  }

  public function deleteSpecialDate(Request $request)
  {
    $id = $request->input('id');

    $data_request = [
      "id" => $id
    ];

    Http::post('http://localhost:8888/special-date/delete', $data_request);

    return redirect()->back()->with('success', 'Successfully deleted!');
  }

  public function detailSpecialDate(Request $request)
  {
    $id = $request->input('id');

    $data_request = [
      "id" => $id
    ];

    $response = Http::get('http://localhost:8888/special-date/detail', $data_request);
    $body = json_decode($response->body(), true);

    $title = "Holidays";
    $select2 = "";

    if ($body['data']['typeDay'] == 2) {
      $title = "Overtime";
      $select2 = '$(".select").select2({
                            minimumResultsForSearch: Infinity
                        });

                        $(".multiselect-full-featured").multiselect({
                            includeSelectAllOption: true,
                            enableFiltering: true
                        });';
    }

    $param_request = ['department' => auth()->user()->department];
    $response = Http::get('http://localhost:8888/staff/find-staff-department', $param_request);
    $data_staff = json_decode($response->body(), true);
    $check_staff = explode(',', $body['data']['staffOt']);


    $options = "";

    foreach ($data_staff['data'] as $item) {
      $selected = "";
      if (in_array($item['id'] . '', $check_staff)) {
        $selected = "selected";
      }

      $options .= "<option " . $selected . " value='" . $item['id'] . "'>" . $item['firstname'] . " " . $item['lastname'] . " || " . $item['code'] . "</option>";
    }

    $change_staff = '';
    if ($body['data']['typeDay'] == 2) {
      $change_staff = '<div class="form-group row">
                                <label class="col-lg-3 col-form-label">Select staff for overtime: </label>
                                <div class="col-lg-9">
                                    <select name="staff_ot[]" class="form-control multiselect-full-featured" multiple="multiple" data-fouc>
                                        ' . $options . '
                                    </select>
                                </div>
                            </div>';
    }
    $fromDate= Carbon::createFromTimestampMs($body['data']['daySpecialFrom'])->format('Y-m-d');
    $toDate= Carbon::createFromTimestampMs($body['data']['daySpecialTo'])->format('Y-m-d'); 

    $html = "<input type='hidden' name='id_update' value='" . $id . "'>";
    $html .= "<input type='hidden' name='type_day' value='" . $body['data']['typeDay'] . "'>";
    $html .= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Update ' . $title . '</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    $html .= '<span aria-hidden="true">&times;</span></button></div>';
    $html .= '
            <div class="modal-body">
                ' . $change_staff . '
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">From:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control day_leave" name="day_special_from" value="' . $fromDate . '" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">To:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control day_leave" name="day_special_to" value="' . $toDate . '" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Description ' . $title . ':</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="note" id="note" cols="20" rows="10" placeholder="VD: Lễ quốc khánh, Lễ Tết, ..." required>' . $body['data']['note'] . '</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Edit</button>
            </div>

            <script>
                ' . $select2 . '
                
                $(".day_leave").daterangepicker({
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

  public function updateSpecialDate(Request $request)
  {
    $user = auth()->user();

    $id_update = $request->input('id_update');
    $day_special_from = $request->input('day_special_from');
    $day_special_to = $request->input('day_special_to');
    $note = $request->input('note');
    $type_day = $request->input('type_day');

    $date = date("Y-m-d");
    $data_request = ['special_date_from' => $date, 'staff_request' => auth()->user()->id, 'department_request' => auth()->user()->department];

    $response_check = Http::get('http://localhost:8888/special-date/get-request-ot?', $data_request);
    $body_check = json_decode($response_check->body(), true);

    if ($day_special_from < date('Y-m-d', strtotime(date("Y-m-d") . ' + 3 days'))) {
      return redirect()->back()->with('error', 'The start date must be at least 3 days from the current date! Please try again');
    }

    foreach ($body_check['data'] as $value) {
      if ($id_update == $value['id']) {
        continue;
      }

      // if($value['type_day'] == 2 && $value['department_request'] == auth()->user()->department) {
      //     if(($value['day_special_from'] >= $day_special_from && $value['day_special_from'] <= $day_special_to) || ($value['day_special_to'] >= $day_special_from && $value['day_special_to'] <= $day_special_to)) {
      //         return redirect()->back()->with('error', 'Ngày tăng ca không được chồng chéo nhau!');
      //     }
      // }
      if ($value['type_day'] == 1 && $type_day == 1) {
        if (($value['day_special_from'] >= $day_special_from && $value['day_special_from'] <= $day_special_to) || ($value['day_special_to'] >= $day_special_from && $value['day_special_to'] <= $day_special_to)) {
          return redirect()->back()->with('error', 'Special dates cannot overlap!');
        }
      }
    }

    if ($day_special_from > $day_special_to) {
      return redirect()->back()->with('error', 'The start date cannot be greater than the end date! Please try again');
    }

    if (strlen($note) > 300) {
      return redirect()->back()->with('error', 'Description cannot exceed 300 characters');
    }

    $data_request = [
      "id" => $id_update,
      'day_special_from' => $day_special_from,
      'day_special_to' => $day_special_to,
      'note' => $note,
    ];

    $staff_ot = $request->input('staff_ot');
    if ($type_day == 2) {
      if (!$staff_ot) {
        return redirect()->back()->with('error', 'Please select staff for overtime');
      }
    }

    if ($staff_ot) {
      $string_staff_ot = implode(',', $staff_ot);

      if (in_array("all", $staff_ot)) {
        $string_staff_ot = "all";
      }

      $data_request['string_staff_ot'] = $string_staff_ot;
    }

    $response = Http::post('http://localhost:8888/special-date/update', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['message'] == "Update Special Date success") {
      if ($type_day == 2)
        return redirect()->back()->with('success', 'Successfully updated overtime request!');
      else
        return redirect()->back()->with('success', 'Successfully updated special date!');
    } else {
      if ($type_day == 2)
        return redirect()->back()->with('error', 'Failed to update overtime request!');
      else
        return redirect()->back()->with('error', 'Failed to update special date!');
    }
  }

  public function requestOverTime(Request $request)
  {
    $params_get_department = [
      'id' => auth()->user()->id,
    ];
    $response_get_department = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params_get_department);
    $body_get_department = json_decode($response_get_department->body(), true);

    $year = $request->input('year');
    $month = date("m");
    if (!$year) {
      $year = date("Y");
    }

    $date = $year . '-' . $month . '-' . '01';
    $data_request = ['special_date_from' => $date, 'staff_request' => auth()->user()->id, 'department_request' => auth()->user()->department];

    $response = Http::get('http://localhost:8888/special-date/get-request-ot?', $data_request);
    $body = json_decode($response->body(), true);

    $param_request = ['department' => auth()->user()->department];
    $response = Http::get('http://localhost:8888/staff/find-staff-department', $param_request);
    $data_staff = json_decode($response->body(), true);

    $calendar = array();
    foreach ($body['data'] as $value) {
      if ($value['is_approved'] == 1 or $value['type_day'] == 1) {
        $arr = array();
        if ($value['type_day'] == 1) {
          $arr['title'] = $value['note'];
        } else {
          $arr['title'] = $value['name_department_request'] . " - " . $value['note'];
        }
        $arr['start'] = $value['day_special_from'];
        $arr['end'] = date("Y-m-d", strtotime('+1 days', strtotime($value['day_special_to'])));
        if ($value['type_day'] == 1) {
          $arr['color'] = '#EF5350';
        } else {
          $arr['color'] = '#4B49AC';
        }

        array_push($calendar, $arr);
      }
    }

    return view('main.special_date.request_ot')
      ->with('data', $body['data'])
      ->with('year', $year)
      ->with('calendar', json_encode($calendar))
      ->with('staff', $body_get_department['data'])
      ->with('data_staff', $data_staff['data'])
      ->with('breadcrumbs', [['text' => 'Công phép', 'url' => '../view-menu/time-leave'], ['text' => 'Tăng ca', 'url' => '#']]);
  }

  public function detailOverTime(Request $request)
  {
    $id = $request->input('id');

    $data_request = [
      "id" => $id
    ];

    $response = Http::get('http://localhost:8888/special-date/detail-ot?', $data_request);
    $body = json_decode($response->body(), true);

    $title = "Lễ";

    $staff_will_ot = '';
    if ($body['data']['type_day'] == 2) {
      $title = "Ovetime";
      if ($body['data']['staff_ot'] == 'all') {
        $staff_will_ot = '<label class="col-form-label" style="color: #4B49AC">All employees in the department ' . $body['data']['name_department_request'] . '</label>';
      } else {
        $param_request = ['department' => $body['data']['department_request']];
        $response = Http::get('http://localhost:8888/staff/find-staff-department', $param_request);
        $data_staff = json_decode($response->body(), true);
        $check_staff = explode(',', $body['data']['staff_ot']);

        foreach ($data_staff['data'] as $item) {
          if (in_array($item['id'] . '', $check_staff)) {
            $staff_will_ot .= '<label class="col-form-label" style="color: #4B49AC">' . $item['firstname'] . " " . $item['lastname'] . " || " . $item['code'] . '</label><br>';
          }
        }
      }
    }
    $fromDate= Carbon::createFromTimestampMs($body['data']['day_special_from'])->format('Y-m-d');
    $toDate= Carbon::createFromTimestampMs($body['data']['day_special_to'])->format('Y-m-d');

    $footer = '';
    if (auth()->user()->id == 7) {
      $footer = '<div class="modal-footer">
                            <button type="submit" name="btn_approve" value="1" class="btn btn-success">Approve</button>
                            <button type="submit" name="btn_reject" value="-1" class="btn btn-danger">Reject</button>
                        </div>';
    } else {
      $footer = '<div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>';
    }

    $html = "<input type='hidden' name='id_update' value='" . $id . "'>";
    $html .= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Request Details ' . $title . '</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    $html .= '<span aria-hidden="true">&times;</span></button></div>';
    $html .= '
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Department Manager Name:</label>
                    <div class="col-lg-9">
                        <label class="col-form-label">' . $body['data']['full_name_staff_request'] . '</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Department requesting:</label>
                    <div class="col-lg-9">
                        <label class="col-form-label">' . $body['data']['name_department_request'] . '</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Overtime staff: </label>
                    <div class="col-lg-9">
                        ' . $staff_will_ot . '
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">From:</label>
                    <div class="col-lg-9">
                        <label class="col-form-label">' . $fromDate . '</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">To:</label>
                    <div class="col-lg-9">
                        <label class="col-form-label">' . $toDate . '</label>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Description ' . $title . ':</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="note" id="note" cols="15" rows="7" placeholder="VD: Lễ quốc khánh, Lễ Tết, ..." readonly>' . $body['data']['note'] . '</textarea>
                    </div>
                </div>
            </div>
            ' . $footer . '

            <script>
                $(".day_leave").daterangepicker({
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

  public function approveOverTime(Request $request)
  {
    $id_update = $request->input('id_update');
    $approve = $request->input('btn_approve');
    $reject = $request->input('btn_reject');
    $is_approve = $approve ? $approve : $reject;

    $data_request = [
      "id" => $id_update,
      'is_approved' => $is_approve,
    ];

    $response = Http::post('http://localhost:8888/special-date/approve-ot', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['message'] == "Approve success") {
      return redirect()->back()->with('success', 'Success!');
    } else {
      return redirect()->back()->with('error', 'Failed!');
    }
  }
}
