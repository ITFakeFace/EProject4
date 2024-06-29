<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DepartmentController extends Controller
{
    public function index(){

        $response = Http::get('http://localhost:8888/department/list');
        $body = json_decode($response->body(), true);
        $data_department = $body['data'];

        return view('main.department.index')
        ->with('data_department', $data_department)
        ->with('breadcrumbs', [['text' => 'Department', 'url' => '../view-menu/department'], ['text' => 'Department List', 'url' => '#']]);
    }

    public function listUndo(){

        $response = Http::get('http://localhost:8888/department/listUndo');
        $body = json_decode($response->body(), true);
        $data_department = $body['data'];

        return view('main.department.listUndo')
        ->with('data_department', $data_department)
        ->with('breadcrumbs', [['text' => 'Phòng ban', 'url' => '../view-menu/department'], ['text' => 'Phòng ban đã xóa', 'url' => '#']]);
    }

    // public function delete(Request $request){
    //     $id = $request->input('id');
        
    //     $data_request = [
    //         "id" => $id
    //     ];

    //     Http::post('http://localhost:8888/department/delete', $data_request);
    //     return redirect()->back()->with('success', 'Delete complete');
      
    // }

    


    public function add() {
        return view('main.department.add');
    }

    public function createDepartment(Request $request)
    {
         $rule = [
            'txtName' => 'bail|required|unique:department,name|min:2|max:50',
            'txtName1' => 'bail|required|unique:department,name_vn|min:2|max:50',
        ];
        $message = [
            'txtName.required' => 'Department Name cannot be empty',
            'txtName.unique' => 'Department Name already exists',
            'txtName.max' => 'Department Name can be maximum 20 characters',
            'txtName.min' => 'Department Name must be at least 2 characters',
            'txtName1.required' => 'Vietnamese Department Name cannot be empty',
            'txtName1.unique' => 'Vietnamese Department Name already exists',
            'txtName1.max' => 'Vietnamese Department Name can be maximum 20 characters',
            'txtName1.min' => 'Vietnamese Department Name must be at least 2 characters',
        ];
        $data = $request->all();
        $validate = Validator::make($data, $rule, $message);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors());
        }

        $name = $request->input('txtName');
        $nameVn = $request->input('txtName1');
        
        $data_request = [
            'name' => $name,
            'nameVn' =>$nameVn,
        ];

        // dd($data_request);
        $response = Http::post('http://localhost:8888/department/add', $data_request);
        $body = json_decode($response->body(), true);

        if($body['message'] == "Save success") {
            return redirect()->back()->with('success', 'Add complete!');
        } 
        else {
            return redirect()->back()->with('error', 'Add fail');
        }
    }

    public function getEditDep(Request $request) {
        $data_request = $request->all();

        $response = Http::get('http://localhost:8888/department/detail', $data_request);
        $body = json_decode($response->body(), true);
        //dd($body);
        if($body['isSuccess']){
            return view('main/department/edit', [
                'data' => $body['data'],
                'breadcrumbs' => [['text' => 'Phòng ban', 'url' => '../view-menu/department'], ['text' => 'Danh sách phòng ban', 'url' => '../deparment/index'], ['text' => 'Cập nhật phòng ban', 'url' => '#']]
            ]);

            
        }
        return redirect()->back()->with('message','not found');
    }

    public function postEditDep(Request $request) {
        // $data_request = $request->all();
        $rule = [
            'txtName' => 'bail|required|min:2|max:50',
            'txtName1' => 'bail|required|min:2|max:50',
        ];
        $message = [
            'txtName.required' => 'Department cannot null',
            // 'txtName.unique' => 'Tên Phòng Ban đã tồn tại',
            'txtName.max' => 'Department Name can be maximum 20 characters',
            'txtName.min' => 'Department Name must be at least 2 characters',
            'txtName1.required' => 'Vietnamese Department Name cannot be empty',
            'txtName1.max' => 'Vietnamese Department Name can be maximum 20 characters',
            'txtName1.min' => 'Vietnamese Department Name must be at least 2 characters',
        ];
        $data = $request->all();
        $validate = Validator::make($data, $rule, $message);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        }

        $id =$request->input('txtID');
        $name = $request->input('txtName');
        $nameVn = $request->input('txtName1');
        $del =$request->input('txtDel');

        //Check department name
        $data_check = [
            'id'=>$id,
            'name' => $name,
            'name_vn' =>$nameVn,
        ];

        $response_check = Http::get('http://localhost:8888/department/check-department', $data_check);
        $departments = json_decode($response_check->body(), true);

        if($departments['data']) {
            return redirect()->back()->withErrors('Department name already exists')->withInput();
        }
        
        $data_request = [
            'id'=>$id,
            'name' => $name,
            'nameVn' =>$nameVn,
            'del'=>$del,
        ];
        
        $response = Http::post('http://localhost:8888/department/update', $data_request);
        
        $body = json_decode($response->body(), true);
        
        if( $body['isSuccess'] == "Update success"){
            return redirect()->back()->with('message', 'Update complete!');
        }
        return redirect()->back()->with('message','Update fail');
    }


    // public function getDeleteDep(Request $request)
    // {
    //     $id = $request->id;
    //     $response = Http::get(config('app.api_url') . '/department/delete', ['id' => $id]);
    //     $body = json_decode($response->body(), false);
    //   // dd($body);
    //     if ($body->isSuccess) {
    //         return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Delete department complete.']);
    //     }
    //     return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Delete department fail.']);
    // }

    public function getDeleteDep(Request $request)
    {
        $id = $request->id;
    
        // Gọi API để lấy danh sách nhân viên
        $staffResponse = Http::get(config('app.api_url') . '/staff/list');
    
        if ($staffResponse->successful()) {
            $staffList = json_decode($staffResponse->body(), true)['data']; // Chuyển đổi kết quả trả về thành mảng JSON
    
            // Kiểm tra xem phòng ban có đang được sử dụng trong bảng Staff
            $staffCount = collect($staffList)->where('department', $id)->count();
    
            if ($staffCount > 0) {
                // Department is being used in the Staff table
                return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Department is currently being used by staff and cannot be deleted.']);
            }
    
            // Department is not being used, proceed with the delete
            $deleteResponse = Http::get(config('app.api_url') . '/department/delete', ['id' => $id]);
    
            $body = json_decode($deleteResponse->body(), false);
            if ($body->isSuccess) {
                return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Delete department complete.']);
            } else {
                return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Delete department fail.']);
            }
        } else {
            return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Failed to retrieve staff list.']);
        }
    }

    public function getUndoDep(Request $request)
    {
        $id = $request->id;
        $response = Http::get(config('app.api_url') . '/department/undo', ['id' => $id]);
        $body = json_decode($response->body(), false);
        if ($body->isSuccess) {
            return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Undo department complete']);
        }
        return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Undo department fail.']);
    }
   
}
