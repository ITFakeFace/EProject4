<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class KpiController extends Controller
{
  public function setKpi(Request $request)
  {
    return view('main.kpi.set_kpi', [
      'breadcrumbs' => [
        ['text' => 'Kpi', 'url' => '../view-menu/kpi'], ['text' => 'Set Kpi', 'url' => '#']
      ]
    ]);
  }

  public function findKpiStaff(Request $request)
  {
    $staff_id = $request->input('staff_id');
    $kpi_name = $request->input('kpi_name');

    $data_request = [
      "staff_id" => $staff_id,
      "kpi_name" => $kpi_name
    ];

    $response = Http::get('http://localhost:8888/kpi/find-kpi-staff', $data_request);
    $body = json_decode($response->body(), true);

    if ($kpi_name == null) {
      $html = '';
    } else if ($body['data'] !== null) {
      if ($body['data']['isApproved'] == "0") {
        $html = '<div class="alert alert-info div_set_kpi">
                            <strong>Status:</strong>
                            <b class="status_kpi">Set</b>
                            <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&staff_id=' . $staff_id . '&kpi_name=' . $kpi_name . '" class="go_set_kpi">Edit</a>
                        </div>';
      } else if ($body['data']['isApproved'] == "3") {
        $html = '<div class="alert alert-info div_set_kpi">
                            <strong>Status:</strong>
                            <b class="status_kpi">Rejected</b>
                            <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&staff_id=' . $staff_id . '&kpi_name=' . $kpi_name . '" class="go_set_kpi">Edit</a>
                        </div>';
      } else if ($body['data']['isApproved'] == "2") {
        if (auth()->user()->is_manager == 1) {
          $html = '<div class="alert alert-info div_set_kpi">
                                <strong>Status:</strong>
                                <b class="status_kpi">Manager approved</b>
                                <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&staff_id=' . $staff_id . '&kpi_name=' . $kpi_name . '" class="go_set_kpi">Edit</a>
                            </div>';
        } else {
          $html = '<div class="alert alert-info div_set_kpi">
                                <strong>Status:</strong>
                                <b class="status_kpi">Manager approved</b>
                                <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&staff_id=' . $staff_id . '&kpi_name=' . $kpi_name . '&readonly=1" class="go_set_kpi">View</a>
                            </div>';
        }
      } else {
        if ((auth()->user()->department == 2 && auth()->user()->is_manager == 1)) {
          $html = '<div class="alert alert-info div_set_kpi">
                                <strong>Status:</strong>
                                <b class="status_kpi">HR approved</b>
                                <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&staff_id=' . $staff_id . '&kpi_name=' . $kpi_name . '" class="go_set_kpi">Edit</a>
                            </div>';
        } else {
          $html = '<div class="alert alert-info div_set_kpi">
                                <strong>Status:</strong>
                                <b class="status_kpi">HR approved</b>
                                <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&staff_id=' . $staff_id . '&kpi_name=' . $kpi_name . '&readonly=1" class="go_set_kpi">View</a>
                            </div>';
        }
      }
    } else {
      $html = '<div class="alert alert-info div_set_kpi">
                        <strong>Status:</strong>
                        <b class="status_kpi">Not set</b>
                        <a href="../kpi/set-detail-kpi?staff_id=' . $staff_id . '&kpi_name=' . $kpi_name . '" class="go_set_kpi">Set</a>
                    </div>';
    }

    echo $html;
    die;
  }

  public function findKpiDepartment(Request $request)
  {
    $department_id = $request->input('department_id');
    $kpi_name = $request->input('kpi_name');
    $staff_manager = $request->input('staff_manager');

    $data_request = [
      "department_id" => $department_id,
      "kpi_name" => $kpi_name
    ];

    $response = Http::get('http://localhost:8888/kpi/find-kpi-department', $data_request);
    $body = json_decode($response->body(), true);

    if ($kpi_name == null) {
      $html = '';
    } else if ($body['data'] !== null) {
      if ($body['data']['isApproved'] == "0") {
        $html = '<div class="alert alert-info div_set_kpi">
                            <strong>Status:</strong>
                            <b class="status_kpi">Set</b>
                            <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&department_id=' . $department_id . '&kpi_name=' . $kpi_name . '" class="go_set_kpi">Edit</a>
                        </div>';
      } else if ($body['data']['isApproved'] == "3") {
        $html = '<div class="alert alert-info div_set_kpi">
                            <strong>Status:</strong>
                            <b class="status_kpi">Rejected</b>
                            <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&department_id=' . $department_id . '&kpi_name=' . $kpi_name . '" class="go_set_kpi">Edit</a>
                        </div>';
      } else if ($body['data']['isApproved'] == "2") {
        if (auth()->user()->is_manager == 1) {
          $html = '<div class="alert alert-info div_set_kpi">
                                <strong>Status:</strong>
                                <b class="status_kpi">Manager approved</b>
                                <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&department_id=' . $department_id . '&kpi_name=' . $kpi_name . '" class="go_set_kpi">Edit</a>
                            </div>';
        } else {
          $html = '<div class="alert alert-info div_set_kpi">
                                <strong>Status:</strong>
                                <b class="status_kpi">Manager approved</b>
                                <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&department_id=' . $department_id . '&kpi_name=' . $kpi_name . '&readonly=1" class="go_set_kpi">View</a>
                            </div>';
        }
      } else {
        if (auth()->user()->department == 2 && auth()->user()->is_manager == 1) {
          $html = '<div class="alert alert-info div_set_kpi">
                                <strong>Status:</strong>
                                <b class="status_kpi">HR approved</b>
                                <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&department_id=' . $department_id . '&kpi_name=' . $kpi_name . '" class="go_set_kpi">Edit</a>
                            </div>';
        } else {
          $html = '<div class="alert alert-info div_set_kpi">
                                <strong>Status:</strong>
                                <b class="status_kpi">HR approved</b>
                                <a href="../kpi/set-detail-kpi?kpi_id=' . $body['data']['id'] . '&department_id=' . $department_id . '&kpi_name=' . $kpi_name . '&readonly=1" class="go_set_kpi">View</a>
                            </div>';
        }
      }
    } else {
      if (auth()->user()->is_manager != 1) {
        $html = '<div class="alert alert-info div_set_kpi">
                            <strong>Status:</strong>
                            <b class="status_kpi">Not set</b>
                        </div>';
      } else {
        $html = '<div class="alert alert-info div_set_kpi">
                            <strong>Status:</strong>
                            <b class="status_kpi">Not set</b>
                            <a href="../kpi/set-detail-kpi?department_id=' . $department_id . '&kpi_name=' . $kpi_name . '" class="go_set_kpi">Set</a>
                        </div>';
      }
    }

    echo $html;
    die;
  }

  // Detail KPI
  public function setDetailKpi(Request $request)
  {
    $params_get_department = [
      'id' => auth()->user()->id,
    ];
    $response_get_department = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params_get_department);
    $body_get_department = json_decode($response_get_department->body(), true);

    $department_id = $request->input('department_id');
    $kpi_name = $request->input('kpi_name');
    $kpi_id = $request->input('kpi_id') ? $request->input('kpi_id') : 0;
    $staff_id = $request->input('staff_id');
    $create_success = $request->input('create_success');
    $readonly = $request->input('readonly');
    $go_approve = $request->input('go_approve');

    $data_request = [
      "kpi_id" => $kpi_id
    ];

    $response = Http::get('http://localhost:8888/kpi-detail/get-kpi-detail', $data_request);
    $body = json_decode($response->body(), true);

    $response_detail = Http::get('http://localhost:8888/kpi/get-detail-of-kpi', $data_request);
    $body_detail = json_decode($response_detail->body(), true);

    $kpi_details = $body['data'];

    // dd($body_detail['data']);

    return view('main.kpi.set_detail_kpi')
      ->with('department_id', $department_id)
      ->with('kpi_id', $kpi_id)
      ->with('kpi_name', $kpi_name)
      ->with('staff_id', $staff_id)
      ->with('kpi_details', $kpi_details)
      ->with('create_success', $create_success)
      ->with('readonly', $readonly)
      ->with('go_approve', $go_approve)
      ->with('detail_of_kpi', $body_detail['data'])
      ->with('staff', $body_get_department['data'])
      ->with('breadcrumbs', [
        ['text' => 'Kpi', 'url' => '../view-menu/kpi'], ['text' => 'Set Kpi', 'url' => '../kpi/set-kpi'], ['text' => 'Kpi Details', 'url' => '#']
      ]);
  }

  public function createKpi(Request $request)
  {
    $user = auth()->user();
    // data kpi
    $department_id = $request->input('department_id');
    $kpi_name = $request->input('kpi_name');
    $kpi_id = $request->input('kpi_id');
    $staff_id = $request->input('staff_id');

    //data kpi detail
    $kpi_detail_id = $request->input('kpi_detail_id');
    $target = $request->input('target');
    // $task_description = $request->input('task_description');
    // $duties_activities = $request->input('duties_activities');
    // $skill = $request->input('skill');
    $ratio = $request->input('ratio');
    $del = $request->input('del');

    if ($kpi_id == 0) {
      //create
      $tasks = array();
      for ($i = 0; $i < count($target); $i++) {
        $task = array();
        $task['target'] = $target[$i];
        // $task['task_description'] = $task_description[$i];
        // $task['duties_activities'] = $duties_activities[$i];
        // $task['skill'] = $skill[$i];
        $task['ratio'] = $ratio[$i];
        array_push($tasks, $task);
      }

      $is_approved = '0';
      $approved_by = null;
      if ($user->department != 2 and $user->is_manager == 1) {
        $is_approved = '2';
        $approved_by = $user->id;
      }

      if ($user->department == 2 and $user->is_manager == 1) {
        $is_approved = '1';
        $approved_by = $user->id;
      }

      $data_request_create = [
        //kpi
        'department_id' => $department_id,
        'kpi_name' => $kpi_name,
        'staff_id' => $staff_id,
        'created_at' => date('Y-m-d H:i:s'),
        'is_approved' => $is_approved,
        'approved_by' => $approved_by,


        //kpi details
        'tasks' => $tasks
      ];

      $response = Http::post('http://localhost:8888/kpi/save-kpi', $data_request_create);
      $body = json_decode($response->body(), true);

      if ($body['message'] == "Save kpi, kpi details success") {
        $alert_success = 'KPI created successfully, please wait for approval!';

        if ($user->department == 2 and $user->is_manager == 1) {
          $alert_success = 'KPI created successfully!';
        }
        return redirect()->action(
          [KpiController::class, 'setDetailKpi'],
          [
            'department_id' => $department_id,
            'staff_id' => $staff_id,
            'kpi_id' => $body['data']['id'],
            'kpi_name' => $kpi_name,
            'create_success' => $alert_success
          ]
        );
      } else {
        return redirect()->back()->with('error', 'Failed to add KPI!');
      }
    } else {
      //update
      $tasks = array();
      for ($i = 0; $i < count($target); $i++) {
        $task = array();
        $task['id'] = isset($kpi_detail_id[$i]) ? $kpi_detail_id[$i] : null;
        $task['target'] = $target[$i];
        // $task['task_description'] = $task_description[$i];
        // $task['duties_activities'] = $duties_activities[$i];
        // $task['skill'] = $skill[$i];
        $task['ratio'] = $ratio[$i];
        $task['del'] = $del[$i];
        array_push($tasks, $task);
      }

      $is_approved = '0';
      $approved_by = null;
      if ($user->department != 2 and $user->is_manager == 1) {
        $is_approved = '2';
        $approved_by = $user->id;
      }

      if ($user->department == 2 and $user->is_manager == 1) {
        $is_approved = '1';
        $approved_by = $user->id;
      }

      $data_request_update = [
        'kpi_id' => $kpi_id,
        'is_approved' => $is_approved,
        'update_at' => date('Y-m-d H:i:s'),
        'approved_by' => $approved_by,
        //kpi details update
        'tasks' => $tasks
      ];

      $response = Http::post('http://localhost:8888/kpi/update-kpi-details', $data_request_update);
      $body = json_decode($response->body(), true);

      if ($body['message'] == "Save kpi, kpi details success") {
        $alert_success = 'KPI updated successfully, please wait for approval!';

        if ($user->department == 2 and $user->is_manager == 1) {
          $alert_success = 'KPI updated successfully!';
        }
        return redirect()->action(
          [KpiController::class, 'setDetailKpi'],
          [
            'department_id' => $department_id,
            'staff_id' => $staff_id,
            'kpi_id' => $body['data']['id'],
            'kpi_name' => $kpi_name,
            'create_success' => $alert_success
          ]
        );
      } else {
        return redirect()->back()->with('error', 'Failed to update KPI!');
      }
    }
  }

  public function listKpi(Request $request)
  {
    $params_get_department = [
      'id' => auth()->user()->id,
    ];
    $response_get_department = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params_get_department);
    $body_get_department = json_decode($response_get_department->body(), true);

    $data_request = [
      'department' => auth()->user()->department,
      'is_manager' => auth()->user()->is_manager
    ];

    $response_staff = Http::get('http://localhost:8888/kpi/get-list-kpi-staff', $data_request);
    $body_staff = json_decode($response_staff->body(), true);

    $response_department = Http::get('http://localhost:8888/kpi/get-list-kpi-department', $data_request);
    $body_department = json_decode($response_department->body(), true);

    return view('main.kpi.list_kpi', [
      'data_staff' => $body_staff['data'],
      'data_department' => $body_department['data'],
      'staff' => $body_get_department['data'],
      'breadcrumbs' => [
        ['text' => 'Kpi', 'url' => '../view-menu/kpi'], ['text' => 'Kpi List', 'url' => '#']
      ]
    ]);
  }

  public function approveKpi(Request $request)
  {
    $user = auth()->user();
    $id = $request->input('kpi_id');
    $reject = $request->input('btn_reject');

    if ($reject) {
      $is_approved = '3';
    } else {
      $is_approved = '1';
      if ($user->department != 2 and $user->is_manager = 1) {
        $is_approved = '2';
      }
    }

    $data_request = [
      'id' => $id,
      'is_approved' => $is_approved,
      'approved_by' => $user->id
    ];

    $response = Http::post('http://localhost:8888/kpi/approve-kpi', $data_request);
    $body = json_decode($response->body(), true);

    if ($body['message'] == "Approve success") {
      if ($reject) {
        return redirect()->back()->with('success', 'KPI rejected successfully!');
      } else {
        return redirect()->back()->with('success', 'KPI approved successfully!');
      }
    } else {
      return redirect()->back()->with('error', 'Failed to approve KPI!');
    }
  }

  public function setDetailChild(Request $request)
  {
    $kpi_detail_id = $request->input('kpi_detail_id');

    $data_request_detail_kpi = [
      'id' => $kpi_detail_id
    ];

    $response = Http::get('http://localhost:8888/kpi-detail/get-one-kpi-detail', $data_request_detail_kpi);
    $kpi_detail = json_decode($response->body(), true);

    $data_request = [
      'kpi_detail_id' => $kpi_detail_id
    ];

    $response = Http::get('http://localhost:8888/kpi/get-kpi-detail-child', $data_request);
    $body = json_decode($response->body(), true);

    return view(
      'main.kpi.set_detail_child',
      [
        'kpi_detail' => $kpi_detail['data'],
        'data' => $body['data']
      ]
    );
  }

  public function createDetailChild(Request $request)
  {
    $id_kpi_detail = $request->input('id_kpi_detail');
    $id_child = $request->input('id_child');
    $name = $request->input('name');
    $number_target = $request->input('number_target');
    $number_get = $request->input('number_get');
    $duties_activities = $request->input('duties_activities');
    $skill = $request->input('skill');

    $tasks = array();
    for ($i = 0; $i < count($name); $i++) {
      $task = array();

      $task['id'] = $id_child[$i] ? $id_child[$i] : null;
      $task['name'] = $name[$i];
      $task['number_target'] = $number_target[$i];
      $task['number_get'] = $number_get[$i];
      $task['duties_activities'] = $duties_activities[$i];
      $task['skill'] = $skill[$i];

      array_push($tasks, $task);
    }

    $data_request_create = [
      'id_kpi_detail' => $id_kpi_detail,
      'tasks' => $tasks
    ];

    $response = Http::post('http://localhost:8888/kpi/save-detail-child', $data_request_create);
    $body = json_decode($response->body(), true);

    if ($body['data'] == "Success") {
      return redirect()->back()->with('success', 'Saved successfully!');
    } else {
      return redirect()->back()->with('error', 'Failed to save!');
    }
  }
}
