<?php

namespace App\Http\Controllers;

use App\Exports\StaffPayrollExport;
use App\Imports\PayrollImport;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Excel;

class SalaryController extends Controller
{

  public function getIndex(Request $request)
  {
    $response = Http::get(config('app.api_url') . '/salary/list', [
      'del' => boolval($request->del)
    ]);
    $body = json_decode($response->body(), false);
    $data = [];
    if ($body->isSuccess) {
      $data = $body->data;
    }
    return view('main.salary.index', [
      'data' => $data
    ]);
  }

  public function getDetail(Request $request)
  {
    $response = Http::get(config('app.api_url') . '/salary/details', [
      'id' => $request->id
    ]);
    $body = json_decode($response->body(), false);
    $data = [];
    if ($body->isSuccess) {
      $data = $body->data;
    }

    if (count($data) > 0) {
      return view('main.salary.details', [
        'data' => $data,
      ]);
    }

    return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'No details for this salary calculation']);
  }

  public function getCreate()
  {
    $response = Http::get(config('app.api_url') . '/staff/list', []);
    $listStaffResponse = json_decode($response->body(), false);
    $responseSalaryOption = Http::get(config('app.api_url') . '/salary-option/list', []);
    $listSalaryOptionResponse = json_decode($responseSalaryOption->body(), false);
    $listStaff = [];
    $listSalaryOption = [];
    if ($listStaffResponse->isSuccess) {
      $listStaff = $listStaffResponse->data;
    }
    if ($listSalaryOptionResponse->isSuccess) {
      $listSalaryOption = $listSalaryOptionResponse->data;
    }
    $currentDate = Carbon::now();
    $currentMonth = $currentDate->month;
    $currentYear = $currentDate->year;

    $currentYearMonth = "{$currentYear}-0{$currentMonth}";
    return view('main.salary.create', [
      'listStaff' => $listStaff,
      'listSalaryOption' => $listSalaryOption,
      'currentYearMonth' => $currentYearMonth
    ]);
  }

  public function postCalculatedSalary(Request $request)
  {

    $data = $request->all();
    $data['staffs'] = array_values($data['staffs']);
    $response = Http::post(config('app.api_url') . '/salary/calculated', $data);
    $body = json_decode($response->body(), false);
    if ($body->isSuccess) {
      return redirect()->back()->with('message', [
        'type' => 'success',
        'message' => 'Salary calculation completed.'
      ]);
    }

    return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Salary calculation failed: ' . $body->message]);
  }

  public function getDeleteSalary($id)
  {
    $response = Http::get(config('app.api_url') . '/salary/find-salary', [
      'id' => $id
    ]);
    $body = json_decode($response->body(), false);
    $salary = null;
    if ($body->isSuccess) {
      $salary = $body->data;
    }
    if ($salary) {
      if ($salary->status == 'pending') {
        $response = Http::get(config('app.api_url') . '/salary/delete', [
          'id' => $id
        ]);
        $body = json_decode($response->body(), false);
        if ($body->isSuccess) {
          return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Deleted successfully']);
        } else {
          return redirect()->back()->with('message', ['type' => 'danger', 'message' => $body->message]);
        }
      } else {
        return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Only unlocked calculations can be deleted']);
      }
    }
    return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Salary calculation not found']);
  }

  public function getChangeStatusSuccessSalary($id)
  {
    $response = Http::get(config('app.api_url') . '/salary/find-salary', [
      'id' => $id
    ]);
    $body = json_decode($response->body(), false);
    $salary = null;
    if ($body->isSuccess) {
      $salary = $body->data;
    }
    if ($salary) {
      if ($salary->status == 'pending') {
        $response = Http::post(config('app.api_url') . '/salary/update-status', [
          'id' => $id,
          'status' => 'success',
        ]);
        $body = json_decode($response->body(), false);
        if ($body->isSuccess) {
          return redirect()->back()->with('message', ['type' => 'success', 'message' => $body->message]);
        } else {
          return redirect()->back()->with('message', ['type' => 'danger', 'message' => $body->message]);
        }
      } else {
        return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Only unlocked calculations can be changed']);
      }
    }
    return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Salary calculation not found']);
  }

  public function exportPayroll(Request $request)
  {
    $responseSalaryDetail = Http::get(config('app.api_url') . '/salary/details', [
      'id' => $request->id
    ]);
    $bodySalaryDetail = json_decode($responseSalaryDetail->body(), false);
    $dataSalaryDetail = [];
    if ($bodySalaryDetail->isSuccess) {
      $dataSalaryDetail = $bodySalaryDetail->data;
    }

    $responseDepartment = Http::get('http://localhost:8888/department/list');
    $bodyDepartment = json_decode($responseDepartment->body(), true);
    $data_department = $bodyDepartment['data'];

    return Excel::download(new PayrollImport($dataSalaryDetail, $data_department), 'payroll.xlsx');
  }

  public function exportStaffPayroll(Request $request)
  {
    $responseSalaryDetail = Http::get(config('app.api_url') . '/salary/detail', [
      'id' => $request->id
    ]);
    $bodySalaryDetail = json_decode($response->body(), false);
    $dataSalaryDetail = null;
    if ($bodySalaryDetail->isSuccess) {
      $dataSalaryDetail = $bodySalaryDetail->data;
    }

    $responseDepartment = Http::get('http://localhost:8888/department/list');
    $bodyDepartment = json_decode($responseDepartment->body(), true);
    $data_department = $bodyDepartment['data'];

    //        return view('main.salary.exports.payroll_personal', [
    //            'dataSalaryDetail' => $dataSalaryDetail,
    //            'data_department' => $data_department,
    //        ]);

    return Excel::download(new StaffPayrollExport($dataSalaryDetail, $data_department), now()->format('Y_m_d') . '_' . $dataSalaryDetail->staff->code . '.xlsx');
  }

  public function mySalary(Request $request)
  {

    $response = Http::get(config('app.api_url') . '/salary/find-salary-by-staff', [
      'staff_id' => $request->id
    ]);
    $body = json_decode($response->body(), false);
    $data = [];
    if ($body->isSuccess) {
      $data = $body->data;
    }
    return view('main.salary.my_salary', [
      'data' => $data,
    ]);
  }
}
