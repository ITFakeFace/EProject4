<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class DashboardController extends Controller
{
  public function index()
  {
    $response = Http::get('http://localhost:8888/staff/list');
    $body = json_decode($response->body(), true);
    $data_staffs = $body['data'];
    usort($data_staffs, function ($a, $b) {
      return $b['id'] - $a['id'];
    });
    $staffListTakeTen = array_slice($data_staffs, 0, 10);

    $response = Http::get('http://localhost:8888/department/list');
    $body = json_decode($response->body(), true);
    $data_department = $body['data'];
    // Sort the array by the 'created_at' field in descending order
    usort($data_department, function ($a, $b) {
      return $b['id'] - $a['id'];
    });

    // Get the latest 5 departments
    $departmentListTakeTen = array_slice($data_department, 0, 5);

    $response_count = Http::get('http://localhost:8888/staff/getStaffMonth');
    $body_staffs_count = json_decode($response_count->body(), true);
    $data_staffs_count = $body_staffs_count['data'];

    $response_count_education = Http::get('http://localhost:8888/education/getStaffOffDateMonth');
    $body_education = json_decode($response_count_education->body(), true);
    $data_education = $body_education['data'];

    $response_count_tn = Http::get('http://localhost:8888/staff/getTN');
    $body_tn = json_decode($response_count_tn->body(), true);
    // dd($body_tn);
    $data_tn = $body_tn['data'];

    $response_count_off = Http::get('http://localhost:8888/staff/getStaffOffDateMonth');
    $body_staffs_off_count = json_decode($response_count_off->body(), true);
    $data_staffs_off_count = $body_staffs_off_count['data'];

    $date = date_create("2013-03-15");
    $now = Carbon::now();

    //Chart Genders
    $staffs_gender = array();
    $count_male = 0;
    $count_female = 0;

    //Chart age
    $staffs_age = array();
    $age_18_to_25 = 0;
    $age_25_to_35 = 0;
    $age_35_to_45 = 0;
    $age_45_to_55 = 0;
    $age_other = 0;

    foreach ($data_staffs as $key => $value) {
      if ($value['gender'] == 1)
        $count_male++;
      if ($value['gender'] == 2)
        $count_female++;

      // $date = date_create($value['dob']);
      // $yob = date_format($date,"Y");
      // $age = $now['year'] - $yob;

      $date = Carbon::createFromTimestampMs($value["dob"]);
      $yob = $date->year; // Lấy năm sinh từ đối tượng Carbon
      $now = Carbon::now();
      $currentYear = $now->year; // Lấy năm hiện tại từ đối tượng Carbon
      $age = $currentYear - $yob;

      switch ($age) {
        case $age <= 25:
          $age_18_to_25++;
          break;
        case $age <= 35:
          $age_25_to_35++;
          break;
        case $age <= 45:
          $age_35_to_45++;
          break;
        case $age <= 55:
          $age_45_to_55++;
          break;
        default:
          $age_other++;
          break;
      }
    }

    $staffs_gender['Male'] = $count_male;
    $staffs_gender['Female'] = $count_female;
    $staffs_gender = json_encode($staffs_gender);

    $staffs_age['18_to_25'] = $age_18_to_25;
    $staffs_age['25_to_35'] = $age_25_to_35;
    $staffs_age['35_to_45'] = $age_35_to_45;
    $staffs_age['45_to_55'] = $age_45_to_55;
    $staffs_age['age_other'] = $age_other;
    $staffs_age = json_encode($staffs_age);

    //Chart education
    $staffs_education = array();
    $staffs_education['thpt'] = $data_education[0][0];
    $staffs_education['tc'] = $data_education[0][1];
    $staffs_education['cd'] = $data_education[0][2];
    $staffs_education['dh'] = $data_education[0][3];
    $staffs_education['tdh'] = $data_education[0][4];
    $staffs_education = json_encode($staffs_education);

    //Chart tn
    $staffs_tn = array();
    $staffs_tn['Under 6 Months'] = $data_tn[0][0];
    $staffs_tn['6 - 12 Months'] = $data_tn[0][1];
    $staffs_tn['1 - 3 Years'] = $data_tn[0][2];
    $staffs_tn['Over 3 Years'] = $data_tn[0][3];
    $staffs_tn = json_encode($staffs_tn);

    //Chart Totals Staff By Month
    $arr_chart_staffs_month = array();
    foreach ($data_staffs_count[0] as $value) {
      array_push($arr_chart_staffs_month, $value);
    }
    $staffs_month = json_encode($arr_chart_staffs_month);
    $last_year = date("Y", strtotime("-1 year"));

    //Chart Totals Staff By Month
    $arr_chart_staffs_off = array();
    foreach ($data_staffs_off_count[0] as $value) {
      array_push($arr_chart_staffs_off, $value);
    }
    $staffs_off = json_encode($arr_chart_staffs_off);

    //count department
     // Tạo mảng để đếm số lượng nhân viên cho mỗi phòng ban
     $staffCountByDepartment = [];
    
     foreach ($data_staffs as $staff) {
         $departmentId = $staff['department'];
         if (!isset($staffCountByDepartment[$departmentId])) {
             $staffCountByDepartment[$departmentId] = 0;
         }
         $staffCountByDepartment[$departmentId]++;
     }

 
     // Thêm số lượng nhân viên vào dữ liệu phòng ban
     foreach ($departmentListTakeTen as &$department) {
         $departmentId = $department['id'];
         $department['employee_count'] = $staffCountByDepartment[$departmentId] ?? 0;
     }

     foreach ($data_department as &$department) {
        $departmentId = $department['id'];
        $department['employee_count'] = $staffCountByDepartment[$departmentId] ?? 0;
      }

     // Tính tổng số nhân viên trong công ty
     $totalEmployees = array_sum($staffCountByDepartment);
    
 
    return view('main.dashboard.index')
      ->with('staffs_gender', $staffs_gender)
      ->with('staffs_age', $staffs_age)
      ->with('staffs_education', $staffs_education)
      ->with('staffs_tn', $staffs_tn)
      ->with('staffs_month', $staffs_month)
      ->with('last_year', $last_year)
      ->with('staffs_off', $staffs_off)
      ->with('data_staffs', $data_staffs)
      ->with('staffListTakeTen', $staffListTakeTen)
      ->with('totalEmployees', $totalEmployees)
      ->with('data_department', $data_department)
      ->with('departmentListTakeTen', $departmentListTakeTen)
      ->with('breadcrumbs', [
        ['text' => 'Charts', 'url' => '#']
      ]);
  }
}
