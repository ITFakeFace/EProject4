<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckInOutController extends Controller
{
  public function index()
  {
    $params = [
      'id' => auth()->user()->id,
    ];
    $response = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params);
    $body = json_decode($response->body(), true);

    return view('main.check_in_out.index')
      ->with('staff', $body['data'])
      ->with('breadcrumbs', [['text' => 'Check In', 'url' => '../view-menu/time-leave'], ['text' => 'Check In GPS', 'url' => '#']]);
  }

  public function create(Request $request)
  {
    $user = auth()->user();
    $check_in_date = date('Y-m-d');
    $check_in_at = date('Y-m-d H:i:s');
    $latitude = $request->input('latitude');
    $longitude = $request->input('longitude');
    $image = $request->input('image_64');

    if (empty($image)) {
      return redirect()->back()->with('error', 'Please take a photo!');
    }

    if (empty($latitude) || empty($longitude)) {
      return redirect()->back()->with('error', 'Please enable GPS as instructed!');
    }

    // Converting to radians
    // 590 cmt8
    $lati1 = deg2rad('10.7863823');
    $longi1 = deg2rad('106.6641083');
    // $lati1 = deg2rad('10.778933');
    // $longi1 = deg2rad('106.6880956');
    $lati2 = deg2rad($latitude);
    $longi2 = deg2rad($longitude);

    // Haversine Formula
    $difflong = $longi2 - $longi1;
    $difflat = $lati2 - $lati1;

    $val = pow(sin($difflat / 2), 2) + cos($lati1) * cos($lati2) * pow(sin($difflong / 2), 2);

    $res2 = 6378.8 * (2 * asin(sqrt($val))); // for kilometer

    // if ($res2 > 0.5) {
    //     return redirect()->back()->with('error', 'You are too far from the office (over 500m)!');
    // }

    $image_name = "";

    if (preg_match('/^data:image\/(\w+);base64,/', $image)) {
      $data = substr($image, strpos($image, ',') + 1);

      $data = base64_decode($data);

      $image_name = date("YmdHis") . substr(microtime(), 2, 4);

      Storage::disk('custom')->put($image_name . ".png", $data);
    }

    $data_request = [
      "staff_id" => $user->id,
      'staff_code' => $user->code,
      'check_in_day' => $check_in_date,
      'check_in_at' => $check_in_at,
      'image' => $image_name . ".png"
    ];

    $response = Http::post('http://localhost:8888/check-in-out/create', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['message'] == "Save success") {
      return redirect()->back()->with('success', 'Check-in successful!');
    } else {
      return redirect()->back()->with('error', 'Check-in failed!');
    }

    return response($body);
  }

  public function show(Request $request)
  {
    $user = auth()->user();

    $params = [
      'id' => auth()->user()->id,
    ];
    $response_staff = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params);
    $body_staff = json_decode($response_staff->body(), true);

    $month = $request->input('month');
    if ($month && strlen($month) == 1) {
      $month = "0" . $month;
    }
    $year = $request->input('year');
    if (!$month) {
      $month = date("m");
    }
    if (!$year) {
      $year = date("Y");
    }
    $start_date = $year . '-' . $month . '-' . '01';
    $end_date = date("Y-m-t", strtotime($start_date));
    $date_special = $year . '-' . $month . '-' . '01';
    $data_request_special = ['special_date_from' => $date_special, 'staff_request' => auth()->user()->id, 'department_request' => auth()->user()->department];

    $response_special = Http::get('http://localhost:8888/special-date/get-request-ot?', $data_request_special);
    $body_special = json_decode($response_special->body(), true);

    $date = $year . '-' . $month . '-' . '01';
    $data_request = ['staff_id' => $user->id, 'y_m' => $date];

    $response = Http::post('http://localhost:8888/check-in-out/get-staff-time', $data_request);
    $body = json_decode($response->body(), true);

    $data_request_high = ['from_date' => $date, 'to_date' => $end_date];

    $response = Http::get('http://localhost:8888/time-leave/get-time-leave-from-to', $data_request_high);
    $time_leave = json_decode($response->body(), true);

    $data_request_leave_other = ['staff_id' => $user->id, 'month_get' => $date];
    $response = Http::get('http://localhost:8888/leave-other/list', $data_request_leave_other);
    $leave_other = json_decode($response->body(), true);

    $response = Http::get('http://localhost:8888/leave-other/get-leave-other-from-to', $data_request_high);
    $leave_other_table = json_decode($response->body(), true);

    $response = Http::get('http://localhost:8888/time-special/get-time-special-from-to', $data_request_high);
    $time_special = json_decode($response->body(), true);

    $calendar = array();
    foreach ($body_special['data'] as $value) {
      $check = false;
      if ($value['staff_ot']) {
        $arr_id_ot = explode(',', $value['staff_ot']);

        if (in_array(auth()->user()->id . '', $arr_id_ot) or $value['staff_ot'] == 'all') {
          $check = true;
        }
      }

      if (($value['is_approved'] == 1 && $check == true) or $value['type_day'] == 1) {

        $arr = array();
        $arr['title'] = $value['note'];
        $arr['start'] = $value['day_special_from'];
        $arr['end'] = date("Y-m-d", strtotime('+1 days', strtotime($value['day_special_to'])));
        if ($value['type_day'] == 1) {
          $arr['color'] = '#EF5350';
        } else {
          $arr['color'] = '#046A38';
        }

        array_push($calendar, $arr);
      }
    }

    $summary = [];
    $summary['total_go'] = 0;
    $summary['total_number_time'] = 0;
    $summary['total_number_time_all'] = 0;
    $summary['total_special'] = 0;
    $summary['total_day_off'] = 0;
    $summary['total_day_normal'] = 0;
    $summary['total_late'] = "00:00:00";
    $summary['total_soon'] = "00:00:00";
    $summary['total_time'] = "00:00:00";
    $summary['total_ot'] = "00:00:00";
    $summary['total_day_add'] = 0;
    $summary['total_day_leave'] = 0;
    $summary['total_time_special'] = 0;

    foreach ($time_special['data'] as $value) {
      if ($value['staff_id'] == $user->id) {
        $summary['total_time_special']++;
        $summary['total_number_time'] += 1;
        $summary['total_number_time_all'] += 1;
      }
    }

    foreach ($time_leave['data'] as $value) {
      if ($value['is_approved'] == 1 && $value['staff_id'] == $user->id) {
        $arr = array();
        $value['type'] == 0 ? $arr['title'] = "Additional work: " . $value['time'] : $arr['title'] = "Annual leave calculated: " . $value['time'];
        $arr['start'] = $value['day_time_leave'];
        $arr['end'] = $value['day_time_leave'];
        $arr['color'] = '#68683c';

        array_push($calendar, $arr);

        $value['time'] == "08:00:00" ? $num = 1 : $num = 0.5;
        if ($value['type'] == 0) {
          $summary['total_day_add'] += $num;
        } else {
          $summary['total_day_leave'] += $num;
        }

        $summary['total_number_time'] += $num;
        $summary['total_number_time_all'] += ($num * $value['multiply']);
      }
    }

    foreach ($leave_other['data'] as $value) {
      if ($value['isApproved'] == 1 && $value['staffId'] == $user->id) {
        $arr = array();

        switch ($value['typeLeave']) {
          case 3:
            $arr['title'] = "Short-term sick leave: 0 days";
            break;
          case 4:
            $arr['title'] = "Long-term sick leave: 0 days";
            break;
          case 5:
            $arr['title'] = "Maternity leave: 0 days";
            break;
          case 6:
            $arr['title'] = "Marriage leave: With pay";
            break;
          case 7:
            $arr['title'] = "Bereavement leave: With pay";
            break;
          default:
            $arr['title'] = "Unpaid leave: 0 days";
            break;
        }

        $arr['start'] = $value['fromDate'];
        $arr['end'] = date("Y-m-d", strtotime('+1 days', strtotime($value['toDate'])));
        $arr['color'] = '#68683c';

        array_push($calendar, $arr);

        $day_from_check = $value['fromDate'] > $start_date ? $value['fromDate'] : $start_date;
        $day_to_check = $value['toDate'] > $end_date ? $end_date : $value['toDate'];
        while ($day_from_check <= $day_to_check) {
          $summary['total_day_leave'] += 1;
          if ($value['typeLeave'] == 6 or $value['typeLeave'] == 7) {
            $summary['total_number_time'] += 1;
            $summary['total_number_time_all'] += 1;
          }
          $day_from_check = date('Y-m-d', strtotime($day_from_check . ' + 1 days'));
        }
      }
    }

    foreach ($body['data'] as $value) {

      if ($value['check_out']) {
        $arr = array();
        $title = 'End: ' . $value['check_out'];

        $arr['title'] = $title;
        $arr['start'] = $value['check_in_day_no_format'];
        $arr['color'] = '#4caf50';

        array_push($calendar, $arr);
      }

      if ($value['check_in']) {
        $arr = array();
        $title = 'Start: ' . $value['check_in'];

        $arr['title'] = $title;
        $arr['start'] = $value['check_in_day_no_format'];

        array_push($calendar, $arr);
      }

      $summary['total_go'] += 1;
      $summary['total_number_time'] += $value['number_time'];
      $summary['total_number_time_all'] += ($value['number_time'] * $value['multiply']);

      if ($value['multiply'] == 3) {
        $summary['total_special'] += $value['number_time'];
      } else if ($value['multiply'] == 2) {
        $summary['total_day_off'] += $value['number_time'];
      } else {
        $summary['total_day_normal'] += 1;
      }
    }

    $data_request = ['y_m' => $date];
    $response = Http::get('http://localhost:8888/time-leave/summary-staff-time', $data_request);
    $summary_all_staff = json_decode($response->body(), true);

    foreach ($summary_all_staff['data'] as $value) {
      if ($value['staff_id'] == $user->id) {
        if ($value['sum_time'])
          $summary['total_time'] = $value['sum_time'];
        if ($value['sum_in_late'])
          $summary['total_late'] = $value['sum_in_late'];
        if ($value['sum_out_soon'])
          $summary['total_soon'] = $value['sum_out_soon'];
        if ($value['sum_ot'])
          $summary['total_ot'] = $value['sum_ot'];
      }
    }

    return view('main.check_in_out.staff_time')
      ->with('data', $body['data'])
      ->with('time_leave', $time_leave['data'])
      ->with('leave_other_table', $leave_other_table['data'])
      ->with('summary', $summary)
      ->with('year', $year)
      ->with('month', $month)
      ->with('staff', $body_staff['data'])
      ->with('calendar', json_encode($calendar))
      ->with('y_m', $start_date)
      ->with('time_special', $time_special['data'])
      ->with('breadcrumbs', [['text' => 'Time Off', 'url' => '../view-menu/time-leave'], ['text' => 'Check-in history', 'url' => '#']]);
  }
}
