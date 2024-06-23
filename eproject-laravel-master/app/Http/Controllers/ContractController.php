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
            'baseSalary' => 'required|numeric|min:0',
        ];
        $message = [
            'staffId.required' => 'Mã nhân viên không để rỗng',
            'startDate.required' => 'Ngày bắt đầu không để rỗng',
            'endDate.required' => 'Ngày kết thúc không để rỗng',
            'startDate.date_format' => 'Ngày bắt đầu sai định dạng: YYYY-MM-DD',
            'endDate.date_format' => 'Ngày kết thúc sai định dạng: YYYY-MM-DD',
            'endDate.after_or_equal' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu',
            'baseSalary.required' => 'Lương không để rỗng',
            'baseSalary.numeric' => 'lương chỉ chấp nhận số',
            'baseSalary.min' => 'Lương không nhỏ hơn :min'
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
                'message' => 'Lưu hợp đồng thành công.'
            ]);
        }
        return redirect()->back()->with('message', ['type' => 'danger', 'message' => "Lưu hợp đồng thất bại."]);
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
            return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Không tìm thấy hợp đồng']);
        }
        return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Chấm dứt hợp đồng thành công']);
    }

    public function getDelete($id)
    {
        $response = Http::get(config('app.api_url') . '/contract/delete', ['id' => $id]);
        $body = json_decode($response->body(), false);
        if ($body->isSuccess) {
            return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Xóa hợp đồng thành công.']);
        }
        return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Xóa hợp đồng thất bại.']);
    }

  

//     public function exportWord($id)
// {
//     $template = 'HDLD.docx';
//     $disk = Storage::disk('public_folder');
//     $zip_val = new ZipArchive;

//     if ($disk->exists($template)) {
//         $random_name = Str::uuid() . '.docx';
//         $disk->copy($template, 'contract_words/' . $random_name);

//         $filePath = public_path('storage/contract_words/' . $random_name); // Đảm bảo bạn đang lưu trữ trong thư mục public/storage
//         if ($zip_val->open($filePath) === TRUE) {

//             $response = Http::get(config('app.api_url') . '/contract/detail', ['id' => $id]);
//             $contract_json = json_decode($response->body());
//             $contract = $contract_json->data;

//             $key_file_name = 'word/document.xml';
//             $message = $zip_val->getFromName($key_file_name);

//             if ($message === FALSE) {
//                 return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Không thể đọc tệp mẫu.']);
//             }

//             $contract_startdate = Carbon::createFromFormat('Y-m-d', $contract->startDate);
//             $contract_enddate = Carbon::createFromFormat('Y-m-d', $contract->endDate);

//             $response = Http::get(config('app.api_url') . '/department/detail', ['id' => $contract->staff->department]);
//             $department_json = json_decode($response->body());
//             $department = $department_json->data;

//             $responseCity = Http::get('http://localhost:8888/regional/get-one', ['id' => $contract->staff->regional]);
//             $bodyCity = json_decode($responseCity->body());

//             $responseDistrict = Http::get('http://localhost:8888/regional/get-one', ['id' => $bodyCity->data->parent]);
//             $bodyDistrict = json_decode($responseDistrict->body());

//             $replacements = [
//                 '[STAFF_NAME]' => $contract->staff->firstname . ' ' . $contract->staff->lastname,
//                 '[STAFF_BIRTHDAY]' => Carbon::createFromFormat('Y-m-d', $contract->staff->dob)->format('d/m/Y'),
//                 '[STAFF_ADDRESS1]' => '',
//                 '[STAFF_PHONE]' => $contract->staff->phoneNumber,
//                 '[STAFF_EMAIL]' => $contract->staff->email,
//                 '[STAFF_ID_NUMBER]' => $contract->staff->idNumber,
//                 '[STAFF_ID_DATE]' => Carbon::createFromFormat('Y-m-d', $contract->staff->identity_issue_date)->format('d/m/Y'),
//                 '[STAFF_ID_ADDRESS]' => $bodyDistrict->data->name . ', ' . $bodyCity->data->name,
//                 '[CONTRACT_EXPIRE]' => $contract_startdate->diffInMonths($contract_enddate),
//                 '[CONTRACT_FROM]' => $contract_startdate->format('d/m/Y'),
//                 '[CONTRACT_TO]' => $contract_enddate->format('d/m/Y'),
//                 '[DEPARTMENT_NAME]' => $department->nameVn,
//                 '[POSITION]' => $contract->staff->isManager ? 'Trưởng nhóm' : 'Nhân viên',
//                 '[SALARY_BASE]' => number_format($contract->baseSalary),
//             ];

//             foreach ($replacements as $key => $value) {
//                 $message = str_replace($key, $value, $message);
//             }

//             $zip_val->addFromString($key_file_name, $message);
//             $zip_val->close();

//             // Kiểm tra nếu tệp tồn tại trước khi tải xuống
//             if (file_exists($filePath)) {
//                 return response()->download($filePath)->deleteFileAfterSend(true);
//             } else {
//                 return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Không thể tìm thấy tệp đã tạo.']);
//             }
//         } else {
//             return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Không thể mở tệp mẫu hợp đồng.']);
//         }
//     } else {
//         return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Không tìm thấy mẫu hợp đồng.']);
//     }
// }

    // public function exportWord($id)
    // {
    //     $template = 'HDLD.docx';
    //     $disk = Storage::disk('public_folder');
    //     $zip_val = new ZipArchive;

    //     if ($disk->exists($template)) {
    //         //copy ra file khác để replace
    //         $random_name = ((string)Str::uuid()) . '.docx';
    //         $disk->copy($template, 'contract_words/' . $random_name);

    //         // mở file vừa copy ra để replace keyword
    //         if ($zip_val->open($disk->path('contract_words/' . $random_name))) {

    //             $response = Http::get(config('app.api_url') . '/contract/detail', [
    //                 'id' => $id
    //             ]);

    //             $contract_json = json_decode($response->body(), false);
    //             $contract = $contract_json->data;

    //             $key_file_name = 'word/document.xml';
    //             $message = $zip_val->getFromName($key_file_name);
    //     //    dd($message);

    //             $contract_startdate = Carbon::createFromFormat('Y-m-d', $contract->startDate);
    //             $contract_enddate = Carbon::createFromFormat('Y-m-d', $contract->endDate);

    //             // department
    //             $response = Http::get(config('app.api_url') . '/department/detail', [
    //                 'id' => $contract->staff->department
    //             ]);

    //             // phòng ban
    //             $department_json = json_decode($response->body(), false);
    //             $department = $department_json->data;

    //             $responseCity = Http::get('http://localhost:8888/regional/get-one', ['id' => $contract->staff->regional]);
    //             $bodyCity = json_decode($responseCity->body(), false);

    //             $responseDistrict = Http::get('http://localhost:8888/regional/get-one', ['id' => $bodyCity->data->parent]);
    //             $bodyDistrict = json_decode($responseDistrict->body(), false);

    //             $message = str_replace('[STAFF_NAME]', $contract->staff->firstname . ' ' . $contract->staff->lastname, $message);
    //             $message = str_replace('[STAFF_BIRTHDAY]', Carbon::createFromFormat('Y-m-d', $contract->staff->dob)->format('d/m/Y'), $message);
    //             $message = str_replace('[STAFF_ADDRESS1]', '', $message);
    //             $message = str_replace('[STAFF_PHONE]', $contract->staff->phoneNumber, $message);
    //             $message = str_replace('[STAFF_EMAIL]', $contract->staff->email, $message);
    //             $message = str_replace('[STAFF_ID_NUMBER]', $contract->staff->idNumber, $message);
    //             $message = str_replace('[STAFF_ID_DATE]', Carbon::createFromFormat('Y-m-d', $contract->staff->identity_issue_date)->format('d/m/Y'), $message);
    //             $message = str_replace('[STAFF_ID_ADDRESS]', $bodyDistrict->data->name . ', ' . $bodyCity->data->name, $message);
    //             $message = str_replace('[CONTRACT_EXPIRE]', $contract_startdate->diffInMonths($contract_enddate), $message);
    //             $message = str_replace('[CONTRACT_FROM]', $contract_startdate->format('d/m/Y'), $message);
    //             $message = str_replace('[CONTRACT_TO]', $contract_enddate->format('d/m/Y'), $message);
    //             $message = str_replace('[DEPARTMENT_NAME]', $department->nameVn, $message);
    //             $message = str_replace('[POSITION]', $contract->staff->isManager ? 'Trưởng nhóm' : 'Nhân viên', $message);
    //             $message = str_replace('[SALARY_BASE]', number_format($contract->baseSalary), $message);

    //             $zip_val->addFromString($key_file_name, $message);
    //             $zip_val->close();

    //             return $disk->download('contract_words/' . $random_name);
    //         } else {
    //             return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Không tìm thấy mẫu hợp đồng.']);
    //         }
    //     } else {
    //         return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Không tìm thấy mẫu hợp đồng.']);
    //     }
    // }


public function exportWord($id)
{
    $template = 'HDLD.docx';
    $disk = Storage::disk('public_folder');

    if ($disk->exists($template)) {
        // Copy file template để tránh chỉnh sửa file gốc
        $random_name = ((string)Str::uuid()) . '.docx';
        $disk->copy($template, 'contract_words/' . $random_name);

        // Đường dẫn tới file vừa copy
        $filePath = $disk->path('contract_words/' . $random_name);

        // Khởi tạo TemplateProcessor với file template
        $templateProcessor = new TemplateProcessor($filePath);

        // Lấy thông tin hợp đồng từ API
        $response = Http::get(config('app.api_url') . '/contract/detail', [
            'id' => $id
        ]);

        $contract_json = json_decode($response->body(), false);
        $contract = $contract_json->data;

        // Lấy thông tin phòng ban từ API
        $response = Http::get(config('app.api_url') . '/department/detail', [
            'id' => $contract->staff->department
        ]);
        $department_json = json_decode($response->body(), false);
        $department = $department_json->data;

        // Lấy thông tin thành phố và quận từ API
        $responseCity = Http::get('http://localhost:8888/regional/get-one', ['id' => $contract->staff->regional]);
        $bodyCity = json_decode($responseCity->body(), false);

        $responseDistrict = Http::get('http://localhost:8888/regional/get-one', ['id' => $bodyCity->data->parent]);
        $bodyDistrict = json_decode($responseDistrict->body(), false);

        // Thay thế các từ khóa trong template
        $templateProcessor->setValue('[STAFF_NAME]', $contract->staff->firstname . ' ' . $contract->staff->lastname);
        $templateProcessor->setValue('[STAFF_BIRTHDAY]', Carbon::createFromFormat('Y-m-d', $contract->staff->dob)->format('d/m/Y'));
        $templateProcessor->setValue('[STAFF_ADDRESS1]', '');
        $templateProcessor->setValue('[STAFF_PHONE]', $contract->staff->phoneNumber);
        $templateProcessor->setValue('[STAFF_EMAIL]', $contract->staff->email);
        $templateProcessor->setValue('[STAFF_ID_NUMBER]', $contract->staff->idNumber);
        $templateProcessor->setValue('[STAFF_ID_DATE]', Carbon::createFromFormat('Y-m-d', $contract->staff->identity_issue_date)->format('d/m/Y'));
        $templateProcessor->setValue('[STAFF_ID_ADDRESS]', $bodyDistrict->data->name . ', ' . $bodyCity->data->name);
        $templateProcessor->setValue('[CONTRACT_EXPIRE]', $contract_startdate->diffInMonths($contract_enddate));
        $templateProcessor->setValue('[CONTRACT_FROM]', $contract_startdate->format('d/m/Y'));
        $templateProcessor->setValue('[CONTRACT_TO]', $contract_enddate->format('d/m/Y'));
        $templateProcessor->setValue('[DEPARTMENT_NAME]', $department->nameVn);
        $templateProcessor->setValue('[POSITION]', $contract->staff->isManager ? 'Trưởng nhóm' : 'Nhân viên');
        $templateProcessor->setValue('[SALARY_BASE]', number_format($contract->baseSalary));

        // Lưu tài liệu Word đã chỉnh sửa
        $templateProcessor->saveAs($filePath);

        // Trả về file đã chỉnh sửa để download
        return $disk->download('contract_words/' . $random_name);
    } else {
        return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Không tìm thấy mẫu hợp đồng.']);
    }
}

}
