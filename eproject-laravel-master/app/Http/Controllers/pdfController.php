<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use PDF;

class pdfController extends Controller
{
    public function index(Request $request)
    {
        $y_m = $date = $request->input('y_m');
        $user = auth()->user();
        
        $data_request = ['staff_id' => $user->id, 'y_m' => $y_m];
        $start_date = $y_m;
        $end_date = date("Y-m-t", strtotime($start_date));

        $params_get_department = [
            'id' => auth()->user()->id,
        ];
        $response_get_department = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params_get_department);
        $body_get_department = json_decode($response_get_department->body(), true);

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

        $summary = [];
        $summary['total_number_time_all'] = 0;
        $summary['total_late'] = "00:00:00";
        $summary['total_soon'] = "00:00:00";
        $summary['total_time'] = "00:00:00";
        $summary['total_ot'] = "00:00:00";

        foreach ($time_special['data'] as $value) {
            if($value['staff_id'] == $user->id) {
                $summary['total_number_time_all'] += 1;
            }
        }

        foreach ($time_leave['data'] as $value) {
            if($value['is_approved'] == 1 && $value['staff_id'] == $user->id) {
                $value['time'] == "08:00:00" ? $num = 1 : $num = 0.5;
                $summary['total_number_time_all'] += ($num * $value['multiply']);
            }
        }

        foreach ($leave_other['data'] as $value) {
            if($value['isApproved'] == 1 && $value['staffId'] == $user->id) {
                $day_from_check = $value['fromDate'] > $start_date ? $value['fromDate'] : $start_date;
                $day_to_check = $value['toDate'] > $end_date ? $end_date : $value['toDate'];
                while($day_from_check <= $day_to_check) {
                    if($value['typeLeave'] == 6 or $value['typeLeave'] == 7) {
                        $summary['total_number_time_all'] += 1;
                    }
                    $day_from_check = date('Y-m-d', strtotime($day_from_check. ' + 1 days'));
                }
            }
        }

        foreach ($body['data'] as $value) {
            $summary['total_number_time_all'] += ($value['number_time'] * $value['multiply']);
        }

        $data_request = ['y_m' => $date];
        $response = Http::get('http://localhost:8888/time-leave/summary-staff-time', $data_request);
        $summary_all_staff = json_decode($response->body(), true);

        foreach ($summary_all_staff['data'] as $value) {
            if($value['staff_id'] == $user->id) {
                if($value['sum_time'])
                    $summary['total_time'] = $value['sum_time'];
                if($value['sum_in_late'])
                    $summary['total_late'] = $value['sum_in_late'];
                if($value['sum_out_soon'])
                    $summary['total_soon'] = $value['sum_out_soon'];
                if($value['sum_ot'])
                    $summary['total_ot'] = $value['sum_ot'];
            }
        }

    	$pdf = PDF::loadView('data_check_in',  
            ['date' => date("m-Y", strtotime($date)), 
            'department_of_staff' => $body_get_department['data'][0][2],
            'check_in' => $body['data'], 
            'time_leave' => $time_leave['data'], 
            'leave_other_table' => $leave_other_table['data'],
            'summary' => $summary,
            'time_special' => $time_special['data']
            ]);
        return $pdf->download('data_check_in.pdf');
        // return view('data_check_in', [
        //     'department_of_staff' => $body_get_department['data'][0][2],
        //     'date' => date("m-Y", strtotime($date)), 
        //     'check_in' => $body['data'], 
        //     'time_leave' => $time_leave['data'], 
        //     'leave_other_table' => $leave_other_table['data'],
        //     'summary' => $summary,
        //     'time_special' => $time_special['data']
        // ]);
    }
}
