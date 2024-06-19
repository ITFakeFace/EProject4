<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class TimeSpecialController extends Controller
{
    public function create(Request $request)
    {
        $id_special_date = $request->input('id');

        $data_request = ['id' => $id_special_date];
  
        $response = Http::get('http://localhost:8888/special-date/detail?', $data_request);
        $body = json_decode($response->body(), true);
        $from_date = $body['data']['daySpecialFrom'];
        $to_date = $body['data']['daySpecialTo'];
        
        $data_request_check = ['from_date' => $body['data']['daySpecialFrom'], 'to_date' => $body['data']['daySpecialTo']];

        $response = Http::get('http://localhost:8888/time-leave/get-all-staff-time-from-to?', $data_request_check);
        $body_check = json_decode($response->body(), true);
        $data_check = $body_check['data'];

        $response = Http::get('http://localhost:8888/staff/list');
        $body_staff = json_decode($response->body(), true);
        $data_staff = $body_staff['data'];

        $request_create = [];
        $request_creates = array();
        while($from_date <= $to_date) {
            foreach ($data_staff as $staff) {
                $boolean = true;
                foreach ($data_check as $check) {
                    if($check['check_in_day_y_m_d'] == $from_date && $check['staff_id'] == $staff['id'] && $check['number_time'] != 0) {
                        $boolean = false;
                        break;
                    }
                }

                if($boolean == true) {
                    $data_request_create = [];
                    $data_request_create['staff_id'] = $staff['id'];
                    $data_request_create['special_date_id'] = $id_special_date;
                    $data_request_create['day_time_special'] = $from_date;
                    $data_request_create['number_time'] = 1;
                    $data_request_create['multiply'] = 1;
                    $data_request_create['day_create'] = date('Y-m-d');

                    array_push($request_creates, $data_request_create);
                }
            }
          
            $from_date = date('Y-m-d', strtotime($from_date . ' +1 day'));
        }
        $request_create['list_time_special'] = $request_creates;
        
        $response = Http::post('http://localhost:8888/time-special/save-time-special?', $request_create);
        $result = json_decode($response->body(), true);

        if($result['message'] == "Save success") {
            return redirect()->back()->with('success', 'Bổ sung công ngày lễ cho toàn nhân viên thành công!');
        } else {
            return redirect()->back()->with('error', 'Bổ sung công ngày lễ thất bại!');
        }
    
    }

    public function details(Request $request) {
        $id_special_date = $request->input('id_special_date');

        $data_request = ['special_date_id' => $id_special_date];

        $response = Http::get('http://localhost:8888/time-special/get-list-time-special', $data_request);
        $body = json_decode($response->body(), true);

        return view('main.special_date.detail_time')
            ->with('data', $body['data'])
            ->with('breadcrumbs', [['text' => 'Công phép', 'url' => '../view-menu/time-leave'], ['text' => 'Bổ sung công phép', 'url' => '#']]);
    }
}
