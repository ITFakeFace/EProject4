<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class StaffController extends Controller
{
    public function index()
    {

        $response = Http::get('http://localhost:8888/department/list', []);
        $body = json_decode($response->body(), true);
        $dsPhongBan = [];
        if ($body['isSuccess']) {
            $dsPhongBan = $body['data'];
        }
        $response = Http::get('http://localhost:8888/staff/list');
        $body = json_decode($response->body(), true);
        $data_staff = $body['data'];

        return view('main.staff.index', [
            'data_staff' => $data_staff,
            'data_department' => $dsPhongBan,
            'breadcrumbs' => [['text' => 'Nhân viên', 'url' => '../view-menu/staff'], ['text' => 'Danh sách nhân viên', 'url' => '#']]
        ]);

    }

    public function createStaff(Request $request)
    {
        $rule = [
            'txtCode' => 'required|unique:staff,code|min:3|max:20',
            'txtFname' => 'required',
            'txtDob' => 'required|date_format:Y-m-d|before:' . now()->format('Y-m-d'),
            'txtJoinat' => 'required|date_format:Y-m-d|after:' . now()->subDay()->format('Y-m-d'),
            'txtIDNumber' => 'bail|required|unique:staff,id_number',
            'txtEmail' => 'required|unique:staff,email|email',
            'txtPhone' => 'required|numeric',
            'txtNote' => 'bail|max:500',

            'education.*.level' => 'required|numeric',
            'education.*.levelName' => 'required',
            'education.*.school' => 'required|min:3|max:100',
            'education.*.fieldOfStudy' => 'required',
            'education.*.graduatedYear' => 'required|numeric|min:1940|max:' . now()->year,
        ];
        $message = [
            'txtCode.required' => 'Mã nhân viên không để rỗng',
            'txtCode.unique' => 'Mã nhân viên đã tồn tại',
            'txtCode.max' => 'Mã nhân viên tối đa 20 ký tự',
            'txtCode.min' => 'Mã nhân viên tối thiểu 3 ký tự',
            'txtFname.required' => 'Tên Nhân viên không để rỗng',
            'txtJoinat.required' => 'Ngày vào không để rỗng',
            'txtJoinat.after' => 'Ngày vào phải sau ngày: ' . now()->subDay()->format('Y-m-d'),
            'txtDob.required' => 'Ngày sinh nhật không để rỗng',
            'txtDob.date_format' => 'Ngày sinh nhật sai định dạng',
            'txtDob.before' => 'Ngày sinh nhật phải trước ngày: ' . now()->format('Y-m-d'),
            'txtIDNumber.required' => 'Số CMND không để rỗng',
            'txtIDNumber.unique' => 'Số CMND đã tồn tại',
            'txtEmail.unique' => 'Email không được trùng',
            'txtEmail.email' => 'Email phải đúng định dang abc123@examp.com',
            'txtEmail.require' => 'Email không được trống',
            'txtPhone.required' => 'Số điện thoại không để rỗng',
            'txtPhone.numeric' => 'Số điện thoại chỉ chấp nhận số',
            'txtNote.max' => 'Ghi chú không quá 500 ký tự',

            'education.*.level.required' => 'Cấp bậc không để rỗng',
            'education.*.level.numeric' => 'Cấp bậc chỉ chấp nhận số',
            'education.*.levelName.required' => 'Tên cấp bậc không để rỗng',
            'education.*.school.required' => 'Tên trường không để rỗng',
            'education.*.school.max' => 'Tên Trường tối đa 100 ký tự',
            'education.*.fieldOfStudy.required' => 'Chuyên ngành không để rỗng',
            'education.*.graduatedYear.required' => 'Năm tốt nghiệp không để rỗng',
            'education.*.graduatedYear.numeric' => 'Năm tốt nghiệp chỉ chấp nhận số',
            'education.*.graduatedYear.min' => 'Năm tốt nghiệp nhỏ nhất :min',
            'education.*.graduatedYear.max' => 'Năm tốt nghiệp lớn nhất :max',
        ];
        $data = $request->all();
        $data['txtPass'] = md5(123456);
        $validate = Validator::make($data, $rule, $message);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        }

        $code = $request->input('txtCode');
        $firstname = $request->input('txtFname');
        $lastname = $request->input('txtLname');
        $department = $request->input('txtDepartment');
        $isManager = $request->input('txtisManager');
        $joinedAt = $request->input('txtJoinat');
        $dob = $request->input('txtDob');
        $gender = $request->input('txtGender');
        $regional = $request->input('txtRegional');
        $phoneNumber = $request->input('txtPhone');
        $email = $request->input('txtEmail');
        $password = $request->input('txtPass');
        $idNumber = $request->input('txtIDNumber');
        $identity_issue_date = $request->input('txtIssue');
        $photo = null;
        $idPhoto = null;
        $idPhotoBack = null;
        $note = $request->input('txtNote');
        $user = auth()->user();

        //Photo
        $now = Carbon::now();

        if (request()->hasFile('txtPhoto')) {
            // random name cho ảnh
            $file_name_random = function ($key) {
                $ext = request()->file($key)->getClientOriginalExtension();
                $str_random = (string)Str::uuid();

                return $str_random . '.' . $ext;
            };
            $image = $file_name_random('txtPhoto');
            if (request()->file('txtPhoto')->move('./images/user/avatar/' . $now->format('dmY') . '/', $image)) {
                // gán path ảnh vào model để lưu
                $photo = '/images/user/avatar/' . $now->format('dmY') . '/' . $image;
            }
        }

        if (request()->hasFile('txtIDPhoto')) {
            // random name cho ảnh
            $file_name_random = function ($key) {
                $ext = request()->file($key)->getClientOriginalExtension();
                $str_random = (string)Str::uuid();

                return $str_random . '.' . $ext;
            };
            $image = $file_name_random('txtIDPhoto');
            if (request()->file('txtIDPhoto')->move('./images/user/cmnd/' . $now->format('dmY') . '/', $image)) {
                // gán path ảnh vào model để lưu
                $idPhoto = '/images/user/cmnd/' . $now->format('dmY') . '/' . $image;
            }
        }

        if (request()->hasFile('txtIDPhoto2')) {
            // random name cho ảnh
            $file_name_random = function ($key) {
                $ext = request()->file($key)->getClientOriginalExtension();
                $str_random = (string)Str::uuid();

                return $str_random . '.' . $ext;
            };
            $image = $file_name_random('txtIDPhoto2');
            if (request()->file('txtIDPhoto2')->move('./images/user/cmnd/' . $now->format('dmY') . '/', $image)) {
                // gán path ảnh vào model để lưu
                $idPhotoBack = '/images/user/cmnd/' . $now->format('dmY') . '/' . $image;
            }
        }

        $data_staff = [
            'code' => $code,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'department' => $department,
            'isManager' => boolval($isManager),
            'joinedAt' => $joinedAt,
            'dob' => $dob,
            'gender' => $gender,
            'regional' => $regional,
            'phoneNumber' => $phoneNumber,
            'email' => $email,
            'password' => $password,
            'idNumber' => $idNumber,
            'identity_issue_date'=>$identity_issue_date,
            'photo' => $photo,
            'idPhoto' => $idPhoto,
            'idPhotoBack' => $idPhotoBack,
            "dayOfLeave" => 0,
            'note' => $note,
            "createdBy" => $user->id,
            "status" => 0,
        ];
// dd($data_staff);
        $response = Http::post('http://localhost:8888/staff/add', $data_staff);
        $staffBody = json_decode($response->body(), true);
        if ($staffBody['isSuccess']) {
            $staffDetail = $staffBody['data'];

            $educations = $data['education'];

            foreach ($educations as $education) {
                $education['staffId'] = $staffDetail['id'];

                $education_check = [
                    'school' => $education['school'],
                    'field_of_study' => $education['fieldOfStudy'],
                    'staff_id' => $education['staffId'],
                    'id' => 0
                ];

                $response_check = Http::get('http://localhost:8888/education/check-education', $education_check);
                $body_check = json_decode($response_check->body(), true);

                if($body_check['data']) {
                    return redirect()->back()->withErrors("Tên Trường và Chuyên Ngành của nhân viên không được trùng")->withInput();
                }

                Http::post('http://localhost:8888/education/add', $education);
            }

            return redirect()->back()
                ->with('message', ['type' => 'success', 'message' => 'Thêm thành công!']);
        } else {
            return redirect()->back()
                ->with('message', ['type' => 'danger', 'message' => $staffBody['message']]);
        }
    }

    public function vaddStaff()
    {
        $response = Http::get('http://localhost:8888/department/list', []);
        $body = json_decode($response->body(), true);
        $dsPhongBan = [];
        if ($body['isSuccess']) {
            $dsPhongBan = $body['data'];
        }

        $response = Http::get('http://localhost:8888/regional/list', []);
        $body = json_decode($response->body(), true);
        $dsKhuvuc = [];
        if ($body['isSuccess']) {
            $dsKhuvuc = $body['data'];
        }

        $response = Http::get('http://localhost:8888/regional/list-district', ['parent' => 3410]);
        $body = json_decode($response->body(), true);
        $district_default = [];
        if ($body['isSuccess']) {
            $district_default = $body['data'];
        }

        return view('main.staff.add', [
            'data_reg' => $dsKhuvuc,
            'data_department' => $dsPhongBan,
            'data_district' => $district_default,
            'breadcrumbs' => [['text' => 'Nhân viên', 'url' => '../view-menu/staff'], ['text' => 'Thêm nhân viên', 'url' => '#']]
        ]);
    }

    // Get & Post Update Staff

    public function getDetail(Request $request)
    {
        $data_request = $request->all();

        $response = Http::get('http://localhost:8888/staff/one', $data_request);
        $body = json_decode($response->body(), true);
        if ($body['isSuccess']) {
            $staff = $body['data'];
        }
      

        $response = Http::get('http://localhost:8888/regional/get-one', ['id' => $staff['regional']]);
        $body = json_decode($response->body(), true);
        $district_default = [];
        if ($body['isSuccess']) {
            $district_selected = $body['data'];
        }

        $response = Http::get('http://localhost:8888/regional/list', []);
        $body = json_decode($response->body(), true);
        $dsKhuvuc = [];
        if ($body['isSuccess']) {
            $dsKhuvuc = $body['data'];
        }

        $response = Http::get('http://localhost:8888/regional/list-district', ['parent' => $district_selected['parent']]);
        $body = json_decode($response->body(), true);
        $district_default = [];
        if ($body['isSuccess']) {
            $district_default = $body['data'];
        }

        $response = Http::get('http://localhost:8888/department/list', []);
        $body = json_decode($response->body(), true);
        $dsPhongBan = [];
        if ($body['isSuccess']) {
            $dsPhongBan = $body['data'];
        }

        $response = Http::get('http://localhost:8888/education/list', []);
        $body = json_decode($response->body(), true);
        $data_education = [];
        if ($body['isSuccess']) {
            $data_education = $body['data'];
        }
    
        if ($body['isSuccess']) {
            return view('main/staff/detail', [
                'data' => $staff,
                'data_department' => $dsPhongBan,
                'data_reg' => $dsKhuvuc,
                'data_district' => $district_default,
                'district_selected' => $district_selected,
                'educa' => $data_education,
                'breadcrumbs' => [['text' => 'Nhân viên', 'url' => '../view-menu/staff'], ['text' => 'Danh sách nhân viên', 'url' => '../staff/index'], ['text' => 'Chi tiết nhân viên', 'url' => '#']]
            ]);
        }
        return redirect()->back()->with('message', 'Khong tim nhan vien');
    }

    public function getEditStaff(Request $request)
    {
        //chinh
        $data_request = $request->all();

        $response = Http::get('http://localhost:8888/staff/one', $data_request);
        $body = json_decode($response->body(), true);
        if ($body['isSuccess']) {
            $staff = $body['data'];
        }

        $response = Http::get('http://localhost:8888/regional/get-one', ['id' => $staff['regional']]);
        $body = json_decode($response->body(), true);
        if ($body['isSuccess']) {
            $district_selected = $body['data'];
        }

        $response = Http::get('http://localhost:8888/regional/list', []);
        $body = json_decode($response->body(), true);
        $dsKhuvuc = [];
        if ($body['isSuccess']) {
            $dsKhuvuc = $body['data'];
        }

        $response = Http::get('http://localhost:8888/regional/list-district', ['parent' => $district_selected['parent']]);
        $body = json_decode($response->body(), true);
        $district_default = [];
        if ($body['isSuccess']) {
            $district_default = $body['data'];
        }

        $response = Http::get('http://localhost:8888/department/list', []);
        $body = json_decode($response->body(), true);
        $dsPhongBan = [];
        if ($body['isSuccess']) {
            $dsPhongBan = $body['data'];
        }

        $response_edu = Http::get('http://localhost:8888/education/get-education-by-staff-id', [
            'staff_id' => $data_request['id']
        ]);
        $body_edu = json_decode($response_edu->body(), true);

     
        if ($body['isSuccess']) {
            return view('main/staff/edit', [
                'data' => $staff,
                'data_edu' => $body_edu['data'] ?? [],
                'data_department' => $dsPhongBan,
                'data_reg' => $dsKhuvuc,
                'data_district' => $district_default,
                'district_selected' => $district_selected,
                'breadcrumbs' => [['text' => 'Nhân viên', 'url' => '../view-menu/staff'], ['text' => 'Danh sách nhân viên', 'url' => '../staff/index'], ['text' => 'Cập nhật nhân viên', 'url' => '#']]
            ]);
        }
        return redirect()->back()->with('message', 'Khong tim nhan vien');
    }

    public function postEditStaff(Request $request)
    {
        $rule = [
            'txtCode' => 'required|min:3|max:20',
            'txtFname' => 'required',
            'txtDob' => 'required|date_format:Y-m-d|before:' . now()->format('Y-m-d'),
           'txtJoinat' => 'required|date_format:Y-m-d|after:' . now()->subDay()->format('Y-m-d'),
            'txtJoinat' => 'required',
            'txtIDNumber' => 'bail|required',
            'txtEmail' => 'required|email',
            'txtPhone' => 'required|numeric',
            'txtNote' => 'bail|max:500',

            'education.*.staffId' => 'required|numeric',
            'education.*.level' => 'required|numeric',
            'education.*.levelName' => 'required',
            'education.*.school' => 'required|min:3|max:100',
            'education.*.fieldOfStudy' => 'required',
            'education.*.graduatedYear' => 'required|numeric|min:1940|max:' . now()->year,
        ];
        $message = [
            'txtCode.required' => 'Mã nhân viên không để rỗng',
            'txtCode.unique' => 'Mã nhân viên đã tồn tại',
            'txtCode.max' => 'Mã nhân viên tối đa 20 ký tự',
            'txtCode.min' => 'Mã nhân viên tối thiểu 3 ký tự',
            'txtFname.required' => 'Tên Nhân viên không để rỗng',
            'txtJoinat.required' => 'Ngày vào không để rỗng',
            'txtJoinat.after' => 'Ngày vào phải sau ngày: ' . now()->subDay()->format('Y-m-d'),
            'txtDob.required' => 'Ngày sinh nhật không để rỗng',
            'txtDob.date_format' => 'Ngày sinh nhật sai định dạng',
            'txtDob.before' => 'Ngày sinh nhật phải trước ngày: ' . now()->format('Y-m-d'),
            'txtIDNumber.required' => 'Số CMND không để rỗng',
            'txtIDNumber.unique' => 'Số CMND đã tồn tại',
            'txtEmail.email' => 'Email phải đúng định dang abc123@examp.com',
            'txtPhone.required' => 'Số điện thoại không để rỗng',
            'txtPhone.numeric' => 'Số điện thoại chỉ chấp nhận số',
            'txtNote.max' => 'Ghi chú không quá 500 ký tự',

            'education.*.staffId.required' => 'Mã nhân viên không để rỗng',
            'education.*.staffId.numeric' => 'Mã nhân viên chỉ chấp nhận số',
            'education.*.level.required' => 'Cấp bậc không để rỗng',
            'education.*.level.numeric' => 'Cấp bậc chỉ chấp nhận số',
            'education.*.levelName.required' => 'Tên cấp bậc không để rỗng',
            'education.*.school.required' => 'Tên trường không để rỗng',
            'education.*.school.max' => 'Tên Trường tối đa 100 ký tự',
            'education.*.fieldOfStudy.required' => 'Chuyên ngành không để rỗng',
            'education.*.graduatedYear.required' => 'Năm tốt nghiệp không để rỗng',
            'education.*.graduatedYear.numeric' => 'Năm tốt nghiệp chỉ chấp nhận số',
            'education.*.graduatedYear.min' => 'Năm tốt nghiệp nhỏ nhất :min',
            'education.*.graduatedYear.max' => 'Năm tốt nghiệp lớn nhất :max',
        ];

        $data = $request->all();
        $validate = Validator::make($data, $rule, $message);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        }


        $id = $request->input('txtID');
        $code = $request->input('txtCode');
        $firstname = $request->input('txtFname');
        $lastname = $request->input('txtLname');
        $department = $request->input('txtDepartment');
        $isManager = $request->input('txtisManager');
        $joinedAt = $request->input('txtJoinat');
        $dob = $request->input('txtDob');
        $gender = $request->input('txtGender');
        $regional = $request->input('txtRegional');
        $phoneNumber = $request->input('txtPhone');
        $email = $request->input('txtEmail');
        $password_old = $request->input('txtPassOld');
        $password = $request->input('txtPass');
        $idNumber = $request->input('txtIDNumber');
        $identity_issue_date = $request->input('txtIssue');
        $photo = $request->input('txtImagesOld') ? $request->input('txtImagesOld') : '';
        $idPhoto = $request->input('txtImagesOld2') ? $request->input('txtImagesOld2') : '';
        $idPhotoBack = $request->input('txtImagesOld3') ? $request->input('txtImagesOld3') : '';
        $note = $request->input('txtNote');
        $createdBy = $request->input('txtCreateBy');
        $createdAt = $request->input('txtCreatedAt');
        $user = auth()->user();

        //Photo
        $now = Carbon::now();

        if (request()->hasFile('txtPhoto')) {
            // random name cho ảnh
            $file_name_random = function ($key) {
                $ext = request()->file($key)->getClientOriginalExtension();
                $str_random = (string)Str::uuid();

                return $str_random . '.' . $ext;
            };
            $image = $file_name_random('txtPhoto');
            if (request()->file('txtPhoto')->move('./images/user/avatar/' . $now->format('dmY') . '/', $image)) {
                // gán path ảnh vào model để lưu
                $photo = '/images/user/avatar/' . $now->format('dmY') . '/' . $image;
            }
        }

        if (request()->hasFile('txtIDPhoto')) {
            // random name cho ảnh
            $file_name_random = function ($key) {
                $ext = request()->file($key)->getClientOriginalExtension();
                $str_random = (string)Str::uuid();

                return $str_random . '.' . $ext;
            };
            $image = $file_name_random('txtIDPhoto');
            if (request()->file('txtIDPhoto')->move('./images/user/cmnd/' . $now->format('dmY') . '/', $image)) {
                // gán path ảnh vào model để lưu
                $idPhoto = '/images/user/cmnd/' . $now->format('dmY') . '/' . $image;
            }
        }

        if (request()->hasFile('txtIDPhoto2')) {
            // random name cho ảnh
            $file_name_random = function ($key) {
                $ext = request()->file($key)->getClientOriginalExtension();
                $str_random = (string)Str::uuid();

                return $str_random . '.' . $ext;
            };
            $image = $file_name_random('txtIDPhoto2');
            if (request()->file('txtIDPhoto2')->move('./images/user/cmnd/' . $now->format('dmY') . '/', $image)) {
                // gán path ảnh vào model để lưu
                $idPhotoBack = '/images/user/cmnd/' . $now->format('dmY') . '/' . $image;
            }
        }

        $data_request = [
            'id' => $id,
            'code' => $code,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'department' => $department,
            'isManager' => boolval($isManager),
            'joinedAt' => $joinedAt,
            'dob' => $dob,
            'gender' => $gender,
            'regional' => $regional,
            'phoneNumber' => $phoneNumber,
            'email' => $email,
            'idNumber' => $idNumber,
            'identity_issue_date' =>$identity_issue_date,
            'photo' => $photo,
            'idPhoto' => $idPhoto,
            'idPhotoBack' => $idPhotoBack,
            "dayOfLeave" => 0,
            'note' => $note,
            'createdBy' => $createdBy,
            'createdAt' => $createdAt,
            'updatedBy' => $user->id,
            "status" => 0,
        ];

        if (!$password) {
            $data_request['password'] = $password_old;
        } else {
            $data_request['password'] = md5($password);
        }

        $response = Http::post('http://localhost:8888/staff/update', $data_request);
        $body = json_decode($response->body(), true);

        if($body['message'] == "Duplicate email") {
            return redirect()->back()->withErrors("Email không được trùng với nhân viên khác")->withInput();
        }

        if($body['message'] == "Duplicate id number") {
            return redirect()->back()->withErrors("CMND/CCCD không được trùng với nhân viên khác")->withInput();
        }

        if ($body['isSuccess']) {
            foreach ($data['education'] as $education) { 
                $education_check = [
                    'school' => $education['school'],
                    'field_of_study' => $education['fieldOfStudy'],
                    'staff_id' => $education['staffId'],
                    'id' => isset($education['id']) ? $education['id'] : 0
                ];

                $response_check = Http::get('http://localhost:8888/education/check-education', $education_check);
                $body_check = json_decode($response_check->body(), true);

                if($body_check['data']) {
                    return redirect()->back()->withErrors("Tên Trường và Chuyên Ngành của nhân viên không được trùng")->withInput();
                }

                $response = Http::post('http://localhost:8888/education/update', $education);
                $body = json_decode($response->body(), true);
            }

            return redirect()->back()
                ->with('message', ['type' => 'success', 'message' => 'Cập nhật thành công!']);
        }
        return redirect()->back()
            ->with('message', ['type' => 'danger', 'message' => 'Cập nhật thất bại']);

    }

    public function viewProfile(Request $request)
    {
        $params = [
            'staff_id' => auth()->user()->id,
        ];
        $response = Http::get('http://localhost:8888/staff/get-profile', $params);
        $body = json_decode($response->body(), true);

        $response_edu = Http::get('http://localhost:8888/education/get-education-by-staff-id', $params);
        $body_edu = json_decode($response_edu->body(), true);

        $response_contract = Http::get('http://localhost:8888/contract/by-staff', $params);
        $body_contract = json_decode($response_contract->body(), true);

        //dd($body_contract['data']);

        return view('main.staff.view_profile', [
            'staff' => $body['data'],
            'educations' => $body_edu['data'],
            'contracts' => $body_contract['data']
        ]);
    }

    public function changePassword(Request $request)
    {
        $pass_old = md5($request->input('pass_old'));
        $pass_new = md5($request->input('pass_new'));

        if (strlen($request->input('pass_new')) > 20) {
            return redirect()->back()->with('error', 'Mật khẩu mới không được dài quá 20 kí tự');
        }

        if ($request->input('pass_new') != $request->input('comfirm_pass')) {
            return redirect()->back()->with('error', 'Mật khẩu mới và xác nhận mật khẩu không giống nhau');
        }

        $params = [
            'id' => auth()->user()->id,
            'pass_old' => $pass_old,
            'pass_new' => $pass_new
        ];

        $response = Http::post('http://localhost:8888/staff/change-password', $params);
        $body = json_decode($response->body(), true);

        if ($body['data'] == "Change password Success") {
            return redirect()->back()->with('success', 'Đổi mật khẩu thành công');
        } else {
            return redirect()->back()->with('error', 'Mật khẩu cũ không chính xác');
        }
    }

    public function loadRegional(Request $request)
    {
        $parent = $request->input('parent');

        $params = [
            'parent' => $parent
        ];

        $response = Http::get('http://localhost:8888/regional/list-district', $params);
        $body = json_decode($response->body(), true);

        echo json_encode($body['data']);
        exit;
    }

    public function listUndo()
    {

        $response = Http::get('http://localhost:8888/department/list', []);
        $body = json_decode($response->body(), true);
        $dsPhongBan = [];
        if ($body['isSuccess']) {
            $dsPhongBan = $body['data'];
        }
        $response = Http::get('http://localhost:8888/staff/listUndo');
        $body = json_decode($response->body(), true);
        $data_staff = $body['data'];

        return view('main.staff.listUndo', [
            'data_staff' => $data_staff,
            'data_department' => $dsPhongBan,
            'breadcrumbs' => [['text' => 'Nhân viên', 'url' => '../view-menu/staff'], ['text' => 'Nhân viên đã xóa', 'url' => '#']]
        ]);

    }

    public function getDeleteStaff(Request $request)
    {
        $id = $request->id;
        $response = Http::get(config('app.api_url') . '/staff/delete', ['id' => $id]);
        $body = json_decode($response->body(), false);

        if ($body->isSuccess) {
            return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Xóa nhân viên thành công.']);
        }
        return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Xóa nhân viên thất bại.']);
    }

    public function getUndoStaff(Request $request)
    {
        $id = $request->id;
        $response = Http::get(config('app.api_url') . '/staff/undo', ['id' => $id]);
        $body = json_decode($response->body(), false);
        if ($body->isSuccess) {
            return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Khôi phục nhân viên thành công.']);
        }
        return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Khôi phục nhân viên thất bại.']);
    }

    public function exportWord1($id)
    {
        $template = 'NHANVIEN.docx';
        $disk = Storage::disk('public_folder');
        $zip_val = new ZipArchive;

        if ($disk->exists($template)) {
            //copy ra file khác để replace
            $random_name = ((string)Str::uuid()) . '.docx';
            $disk->copy($template, 'staff_words/' . $random_name);

            // mở file vừa copy ra để replace keyword
            if ($zip_val->open($disk->path('staff_words/' . $random_name))) {

                $response = Http::get(config('app.api_url') . '/staff/one', [
                    'id' => $id
                ]);

                $staff_json = json_decode($response->body(), false);
                $staff = $staff_json->data;

                $key_file_name = 'word/document.xml';
                $message = $zip_val->getFromName($key_file_name);
//                dd($message);

                // department
                $response = Http::get(config('app.api_url') . '/department/detail', [
                    'id' => $staff->department
                ]);
                // phòng ban
                $department_json = json_decode($response->body(), false);
                $department = $department_json->data;

                //education
                $response_edu = Http::get('http://localhost:8888/education/get-education-by-staff-id', [
                'staff_id' => $id
                ]);
                $education_json = json_decode($response_edu->body(), false);
                $body_edu = $education_json->data;

                // dd('staff_id');
                foreach($body_edu as $edu){
         
                    if('staff_id' == $id){
                      $edu;
                    }
                }

                // $response = Http::get('http://localhost:8888/education/list', []);
                // $education_json = json_decode($response->body(), false);
                // $body_edu = $education_json->data;
                    
                //         foreach($body_edu as $edu){
                //         $edu;
                //         }
                    
                // dd($edu);

                $responseCity = Http::get('http://localhost:8888/regional/get-one', ['id' => $staff->regional]);
                $bodyCity = json_decode($responseCity->body(), false);

                $responseDistrict = Http::get('http://localhost:8888/regional/get-one', ['id' => $bodyCity->data->parent]);
                $bodyDistrict = json_decode($responseDistrict->body(), false);

                $message = str_replace('[STAFF_NAME]', $staff->firstname . ' ' . $staff->lastname, $message);
                $message = str_replace('[STAFF_BIRTHDAY]', Carbon::createFromFormat('Y-m-d', $staff->dob)->format('d/m/Y'), $message);
                $message = str_replace('[STAFF_ADDRESS1]', '', $message);
                $message = str_replace('[STAFF_PHONE]', $staff->phoneNumber, $message);
                $message = str_replace('[STAFF_EMAIL]', $staff->email, $message);
                $message = str_replace('[STAFF_ID_NUMBER]', $staff->idNumber, $message);
                $message = str_replace('[STAFF_ID_DATE]', Carbon::createFromFormat('Y-m-d', $staff->identity_issue_date)->format('d/m/Y'), $message);
                $message = str_replace('[STAFF_ID_ADDRESS]', $bodyDistrict->data->name . ', ' . $bodyCity->data->name, $message);
                $message = str_replace('[DEPARTMENT_NAME]', $department->nameVn, $message);
                $message = str_replace('[POSITION]', $staff->isManager ? 'Trưởng nhóm' : 'Nhân viên', $message);
                $message = str_replace('[SCHOOL]', $edu->school, $message);
                $message = str_replace('[CODE]', $staff->code, $message);
                $message = str_replace('[DATEJOIN]', $staff->joinedAt, $message);
                $message = str_replace('[LEVEL_NAME]', $edu->levelName, $message);
                $message = str_replace('[STUDY]', $edu->fieldOfStudy, $message);
                $message = str_replace('[GRAND]', $edu->grade, $message);
                $message = str_replace('[YEAR]', $edu->graduatedYear, $message);
                
               
                

                $zip_val->addFromString($key_file_name, $message);
                $zip_val->close();

                return $disk->download('staff_words/' . $random_name);
            } else {
                return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Không tìm thấy mẫu hợp đồng.']);
            }
        } else {
            return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Không tìm thấy mẫu hợp đồng.']);
        }
    }
}
