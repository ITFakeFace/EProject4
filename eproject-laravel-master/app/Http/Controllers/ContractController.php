<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use ZipArchive;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Log;

class ContractController extends Controller
{
  public function getList(Request $request)
  {
    $response = Http::get(config('app.api_url') . '/contract/list', [
      'del' => boolval($request->del)
    ]);
    $body = json_decode($response->body(), false);
    $data = [];
    if ($body->isSuccess) {
      $data = $body->data ?? [];
    }
    return view('main.contract.index', [
      'data' => $data,
      'breadcrumbs' => [
        ['text' => 'Hợp đồng', 'url' => '../view-menu/contract'], ['text' => 'Danh sách hợp đồng', 'url' => '#']
      ]
    ]);
  }

  public function getDetail($id)
  {
    $response = Http::get(config('app.api_url') . '/staff/list', []);
    $listStaff = json_decode($response->body(), false);

    $response = Http::get(config('app.api_url') . '/contract/detail', ['id' => $id]);
    $editContractResponse = json_decode($response->body(), false);
    $contract = null;
    if ($editContractResponse->isSuccess) {
      $contract = $editContractResponse->data;
    }
    return view('main.contract.detail', [
      'listStaff' => $listStaff->data,
      'contract' => $contract,
      'breadcrumbs' => [
        ['text' => 'Hợp đồng', 'url' => '../view-menu/contract'], ['text' => 'Chỉnh sửa hợp đồng', 'url' => '#']
      ]
    ]);
  }

  public function getCreate()
  {
    $response = Http::get(config('app.api_url') . '/staff/list', []);
    $listStaff = json_decode($response->body(), false);
    return view('main.contract.create', [
      'listStaff' => $listStaff->data,
      'breadcrumbs' => [
        ['text' => 'Hợp đồng', 'url' => '../view-menu/contract'], ['text' => 'Tạo mới hợp đồng', 'url' => '#']
      ]
    ]);
  }

  public function postSave(Request $request)
  {
    $rule = [
      'staffId' => 'required',
      'startDate' => 'required|date_format:Y-m-d',
      'endDate' => 'required|date_format:Y-m-d|after_or_equal:startDate',
      'baseSalary' => 'required|numeric|min:500000',
    ];
    $message = [
      'staffId.required' => 'Staff ID cannot be empty',
      'startDate.required' => 'Start date cannot be empty',
      'endDate.required' => 'End date cannot be empty',
      'startDate.date_format' => 'Start date is in the wrong format: YYYY-MM-DD',
      'endDate.date_format' => 'End date is in the wrong format: YYYY-MM-DD',
      'endDate.after_or_equal' => 'End date must be greater than or equal to start date',
      'baseSalary.required' => 'Base salary cannot be empty',
      'baseSalary.numeric' => 'Base salary must be a number',
      'baseSalary.min' => 'Base salary cannot be less than :min'
    ];
    $data = $request->all();
    $validate = Validator::make($data, $rule, $message);
    if ($validate->fails()) {
      return redirect()->back()->withErrors($validate);
    }
    $data['stopDate'] = $data['endDate']; // set cho stopDate bằng enddate lúc save

    $response = Http::post(config('app.api_url') . '/contract/save', $data);
    $body = json_decode($response->body(), false);;
    if ($body->isSuccess) {
      return redirect()->back()->with('message', [
        'type' => 'success',
        'message' => 'Save Contract complete.'
      ]);
    }
    return redirect()->back()->with('message', ['type' => 'danger', 'message' => "Failed to save the contract."]);
  }

  public function stopContract($id)
  {
    $response = Http::get(config('app.api_url') . '/contract/stop', ['id' => $id]);
    $editContractResponse = json_decode($response->body(), false);
    $contract = null;
    if ($editContractResponse->isSuccess) {
      $contract = $editContractResponse->data;
    }
    if (!$contract) {
      return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Contract not found']);
    }

    return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Contract terminated successfully']);
  }

  public function getDelete($id)
  {
    $response = Http::get(config('app.api_url') . '/contract/delete', ['id' => $id]);
    $body = json_decode($response->body(), false);

    if ($body->isSuccess) {
      return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Contract deleted successfully.']);
    } else {
      return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Failed to delete the contract.']);
    }
  }



  public function exportWord($id)
  {
    $template = 'HDLD.docx';
    $disk = Storage::disk('public_folder');

    if (!$disk->exists($template)) {
      return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Contract template not found.']);
    }

    // Generate a unique file name for the modified document
    $random_name = (string) Str::uuid() . '.docx';
    $destinationPath = 'contract_words/' . $random_name;

    // Copy the template file to the new location
    $disk->copy($template, $destinationPath);

    // Path to the newly created document
    $filePath = $disk->path($destinationPath);

    // Initialize ZipArchive to modify the document
    $zip_val = new ZipArchive;
    if ($zip_val->open($filePath) === true) {
      try {
        // Fetch contract details from API
        $response = Http::get(config('app.api_url') . '/contract/detail', ['id' => $id]);
        $contract_json = json_decode($response->body());
        if (!$contract_json || !isset($contract_json->data)) {
          throw new \Exception('Failed to fetch contract details.');
        }
        $contract = $contract_json->data;

        // Fetch department details from API
        $response = Http::get(config('app.api_url') . '/department/detail', ['id' => $contract->staff->department]);
        $department_json = json_decode($response->body());
        if (!$department_json || !isset($department_json->data)) {
          throw new \Exception('Failed to fetch department details.');
        }
        $department = $department_json->data;

        // Fetch regional and district details
        $responseCity = Http::get('http://localhost:8888/regional/get-one', ['id' => $contract->staff->regional]);
        $bodyCity = json_decode($responseCity->body());
        $responseDistrict = Http::get('http://localhost:8888/regional/get-one', ['id' => $bodyCity->data->parent]);
        $bodyDistrict = json_decode($responseDistrict->body());

        // Prepare replacements for placeholders in the document
        $replacements = [
          '[STAFF_NAME]' => $contract->staff->firstname . ' ' . $contract->staff->lastname,
          '[STAFF_BIRTHDAY]' => Carbon::createFromTimestampMs($contract->staff->dob)->format('d/m/Y'),
          '[STAFF_ADDRESS1]' => '', // Replace with actual address if available
          '[STAFF_PHONE]' => $contract->staff->phoneNumber,
          '[STAFF_EMAIL]' => $contract->staff->email,
          '[STAFF_ID_NUMBER]' => $contract->staff->idNumber,
          '[STAFF_ID_DATE]' => Carbon::createFromTimestampMs($contract->staff->identity_issue_date)->format('d/m/Y'),
          '[STAFF_ID_ADDRESS]' => $bodyDistrict->data->name . ', ' . $bodyCity->data->name,
          '[CONTRACT_EXPIRE]' => Carbon::createFromTimestampMs($contract->startDate)->diffInMonths($contract->endDate),
          '[CONTRACT_FROM]' => Carbon::createFromTimestampMs($contract->startDate)->format('d/m/Y'),
          '[CONTRACT_TO]' => Carbon::createFromTimestampMs($contract->endDate)->format('d/m/Y'),
          '[DEPARTMENT_NAME]' => $department->nameVn,
          '[POSITION]' => $contract->staff->isManager ? 'Trưởng nhóm' : 'Nhân viên',
          '[SALARY_BASE]' => number_format($contract->baseSalary),



        ];

        // Read the document content from the archive
        $documentContent = $zip_val->getFromName('word/document.xml');

        // Replace placeholders in the document content
        foreach ($replacements as $placeholder => $value) {
          $documentContent = str_replace($placeholder, $value, $documentContent);
        }

        // Update the document with modified content
        $zip_val->addFromString('word/document.xml', $documentContent);
        $zip_val->close();

        // Download the modified document
        if (file_exists($filePath)) {
          return $disk->download($destinationPath);
        } else {
          throw new \Exception('Failed to create or locate the modified document.');
        }
      } catch (\Exception $e) {
        // Log the error
        Log::error('Error exporting contract document: ' . $e->getMessage());
        return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'An error occurred while creating the contract document.']);
      }
    } else {
      return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Unable to open contract template file.']);
    }
  }
}
