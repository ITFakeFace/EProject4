<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class TransferController extends Controller
{

    public function list(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        if(!$month) {
            $month = date("m");
        }
        if(!$year) {
            $year = date("Y");
        }

        $date = $year . '-' . $month . '-' . '01';
        $data_request = ['day_get' => $date, 'department' => auth()->user()->department];

        $response = Http::get('http://localhost:8888/transfer/list', $data_request);
        $body = json_decode($response->body(), true);
    //  dd($body);

        $response = Http::get(config('app.api_url') . '/staff/list', []);
        $listStaff = json_decode($response->body(), false);

    
        $response = Http::get('http://localhost:8888/department/list');
        $body_department = json_decode($response->body(), true);
        $data_department = $body_department['data'];

        $response_contract = Http::get(config('app.api_url') . '/contract/list', [
            'del' => boolval(0)
        ]);
        $body_contract = json_decode($response_contract->body(), false);
        $data_contract = [];
        if ($body_contract->isSuccess) {
            $data_contract = $body_contract->data ?? [];
        }
        // dd($data_contract);
        return view('main.transfer.list', [
            'listContact'=>$data_contract,
            'listStaff' => $listStaff->data,
            'listDepartment' => $data_department,
            'year' => $year,
            'month' => $month,
            'data' => $body['data'] ?? [],
            'breadcrumbs' => [['text' => 'Điều chuyển', 'url' => '../view-menu/transfer']]
        ]);

    }

    public function loadOldDepartment(Request $request) {
        $id = $request->input('old_department');

        $data_request = ['id' => $id];

        $response = Http::get('http://localhost:8888/department/detail', $data_request);
        $department_old = json_decode($response->body(), true);
        $department_old_name = $department_old['data']['name'];


        $html = "<option value='$id' selected>$department_old_name</option>";

        echo $html;die;
    }

    public function create(Request $request)
    {
        $id_staff_transfer = $request->input('staff_id');
        $id_staff_create = $request->input('id_staff_create');
        $old_department = $request->input('old_department');
        $new_department = $request->input('new_department');
        $hr_approved =$request->input('txthr');
        $new_salary =$request->input('txtNewSalary');
        // $note_manager = null;
        $note = $request->input('note');  
//

        $month = $request->input('month');
        $year = $request->input('year');
        if(!$month) {
            $month = date("m");
        }
        if(!$year) {
            $year = date("Y");
        }

        $date = $year . '-' . $month . '-' . '01';
        $data_request = ['day_get' => $date, 'department' => auth()->user()->department];

        $response = Http::get('http://localhost:8888/transfer/list', $data_request);
        $body = json_decode($response->body(), true);
          //var_dump($body);die;
        $temp_date = null;
        
        foreach ($body['data'] as $tran) {
            if($tran['staff_id'] == auth()->user()->id) {
                if($temp_date == null)
                    $temp_date = $tran['created_at'];
                else {
                    if($temp_date < $tran['created_at']) 
                        $temp_date = $tran['created_at'];
                }
                
            }
        }

        // var_dump($temp_date);die;
        if($temp_date != null) {
            $date1=date_create($temp_date);
            $date2=date_create(date('Y-m-d'));
            $diff=date_diff($date1,$date2);
            if($diff->format("%a") < 30) {
                return redirect()->back()->with('error', 'Điều chuyển cách điều chuyển trước chưa quá 30 ngày');
            }   
        }


        if(!$id_staff_transfer) {
            return redirect()->back()->with('error', 'Vui lòng chọn nhân viên điều chuyển!');
        }

        if($old_department == $new_department) {
            return redirect()->back()->with('error', 'Phòng ban hiện tại và phòng ban điều chuyển phải khác nhau!');
        }

        if(strlen($note) > 300) {
            return redirect()->back()->with('error', 'Ghi chú không được vượt quá 300 kí tự');
        }

        $data_check = [
            'staff_id' => $id_staff_transfer
        ];

        $response_check = Http::post('http://localhost:8888/transfer/check', $data_check);
        $body_check = json_decode($response_check->body(), true);

        if($body_check['data']) {
            return redirect()->back()->with('error', 'Tạo điều chuyển thất bại! Nhân viên này đang trong trạng thái điều chuyển');
        }
        
        $data_request = [
            'staff_id' => $id_staff_transfer,
            'old_department' => $old_department,
            'new_department' => $new_department,
            'created_by' => $id_staff_create,
            'oldManagerApproved'=>"0",
            'newManagerApproved'=>"0",
            'managerApproved'=>"0",
            'hr_approved'=>$hr_approved,
            'new_salary'=>$new_salary,
            'del'=>"0",
            'note' => $note,
            // 'note_manager'=>$note_manager,
            'created_at' => date('Y-m-d')
        ];

        // dd($data_request);
        $response = Http::post('http://localhost:8888/transfer/create', $data_request);
        $body = json_decode($response->body(), true);

       
        if($body['message'] == "Save success") {
            return redirect()->back()->with('success', 'Tạo điều chuyển thành công!');
        } 
        else {
            return redirect()->back()->with('error', 'Tạo điều chuyển thất bại!');
        }
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        Http::post('http://localhost:8888/transfer/delete', $data_request);

        return redirect()->back()->with('success', 'Xóa thành công!');
    }

    // update
    public function detail(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        $response = Http::get('http://localhost:8888/transfer/detail', $data_request);
        $body = json_decode($response->body(), true);

        $data_request_staff = [
            "id" => $body['data']['staffId']
        ];

        $response_staff = Http::get('http://localhost:8888/staff/one', $data_request_staff);
        $body_staff = json_decode($response_staff->body(), true);

        $data_request_old_department = ['id' => $body_staff['data']['department']];

        $response_old_department = Http::get('http://localhost:8888/department/detail', $data_request_old_department);
        $department_old = json_decode($response_old_department->body(), true);
        $department_old_name = $department_old['data']['name'];

        $response = Http::get(config('app.api_url') . '/staff/list', []);
        $listStaff = json_decode($response->body(), false);

        $response = Http::get('http://localhost:8888/department/list');
        $body_department = json_decode($response->body(), true);
        $data_department = $body_department['data'];

        $html_list_staff = '';
        foreach ($listStaff->data as $staff) {
            if($body['data']['staffId'] == $staff->id) {
                $html_list_staff .= '<textarea class="form-control select-search select_staff_transfer" cols="1" rows="1" name="staff_id_update"  id="selected_staff" readonly="true" 
                value="'.$staff->id.'">'.$staff->firstname .' '. $staff->lastname.'</textarea>';
            }
        }

        $html_list_department = '';
        foreach ($data_department as $department) {
            if($body['data']['newDepartment'] == $department['id']) {
                $html_list_department .= '<option value="'.$department['id'].'" selected>'.$department['name'].'</option>';
            } else {
                $html_list_department .= '<option value="'.$department['id'].'">'.$department['name'].'</option>';
            }
        }
// dd($body['data']['hrApproved'] );
            $html_hr = '';
            if($body['data']['hrApproved'] == 0){
                $html_hr .= '<option value="0" selected>Xác nhận</option>';
            }
            else{
                    $html_hr .= '<option value="1">Không xác nhận</option>';
                }
               

        $html = '<input type="hidden" name="id_update" value="'.$id.'">';
        $html.= '
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Cập Nhật Điều Chuyển Mới</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        <div class="form-group row">
            <label class="col-lg-3 col-form-label">Tên nhân viên</label>
            <div class="col-lg-9">
                '.$html_list_staff.'
            </div>
        </div>

        <div class="form-group row">
        <label class="col-lg-3 col-form-label">Phòng ban hiện tại:</label>
        <div class="col-lg-9">
            <textarea class="form-control old_department" cols="1" rows="1" name="old_department_update" readonly="true" 
            value="'.$id.'">'.$department_old_name.'</textarea>
        </div>
         </div>

         <div class="form-group row">
         <label class="col-lg-3 col-form-label">Phòng ban điều chuyển:</label>
         <div class="col-lg-9">
             <select class="form-control new_department" name="new_department_update">
                 '.$html_list_department.'
             </select>
         </div>   
         </div>

            <div class="form-group row" hidden>
            <label class="col-lg-3 col-form-label">Xác nhận:(*)</label>
            <div class="col-lg-9">
                <select class="form-control txthr" name="txthr">
                '.$html_hr.'
                </select>
            </div>   
            </div>
           
            <div class="form-group row">
            <label class="col-lg-3 col-form-label">Lương đề xuất:</label>
            <div class="col-lg-9">
            <textarea class="form-control" name="txtNewSalary" id="txtNewSalary" cols="1" rows="1">'.$body['data']['newSalary'].'</textarea> 
            </div>   
            </div>

            <div class="form-group row" hidden>
            <label class="col-lg-3 col-form-label">Ý kiến Giám đốc:(*)</label>
            <div class="col-lg-9">
            <textarea class="form-control" name="txtnoteManager" id="txtnoteManager" cols="2" rows="3" placeholder="VD: Giám đốc nhập ý kiến...">'.$body['data']['noteManager'].'</textarea>
            </div>   
            </div>         

            <div class="form-group row" >
                <label class="col-lg-3 col-form-label">Ghi chú:</label>
                <div class="col-lg-9">
                    <textarea class="form-control" name="note_update" id="note" cols="20" rows="10" placeholder="VD: Quản lý yêu cầu, ...">'.$body['data']['note'].'</textarea>
                </div>
            </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-primary">Sửa</button>
        </div>
        ';
       
        echo $html;
        die;  
    }

    // HR Approved
    public function approvedHR(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        $response = Http::get('http://localhost:8888/transfer/detail', $data_request);
        $body = json_decode($response->body(), true);

        $data_request_staff = [
            "id" => $body['data']['staffId']
        ];

        $response_staff = Http::get('http://localhost:8888/staff/one', $data_request_staff);
        $body_staff = json_decode($response_staff->body(), true);

        $data_request_old_department = ['id' => $body_staff['data']['department']];

        $response_old_department = Http::get('http://localhost:8888/department/detail', $data_request_old_department);
        $department_old = json_decode($response_old_department->body(), true);
        $department_old_name = $department_old['data']['name'];

        $response = Http::get(config('app.api_url') . '/staff/list', []);
        $listStaff = json_decode($response->body(), false);

        $response = Http::get('http://localhost:8888/department/list');
        $body_department = json_decode($response->body(), true);
        $data_department = $body_department['data'];

        $html_list_staff = '';
        foreach ($listStaff->data as $staff) {
            if($body['data']['staffId'] == $staff->id) {
                $html_list_staff .= '<textarea class="form-control select-search select_staff_transfer" cols="1" rows="1" name="staff_id_update"  id="selected_staff" readonly="true" 
                value="'.$staff->id.'">'.$staff->firstname .' '. $staff->lastname.'</textarea>';
            }
        }

        $html_list_department = '';
        foreach ($data_department as $department) {
            if($body['data']['newDepartment'] == $department['id']) {
                $html_list_department .= '<textarea class="form-control" cols="1" rows="1" 
                name="new_department_update" id="new_department_update" readonly="true" value="'.$department['id'].'">'.$department['id'].'</textarea>';
            }
        }


        $html_hr = '';
                $html_hr .= '<option value="0" selected>Xác nhận</option>';
                $html_hr .= '<option value="1">Không xác nhận</option>';
           

        $html = '<input type="hidden" name="id_update" value="'.$id.'">';
        $html.= '
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Xác Nhận Điều Chuyển Mới</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Tên nhân viên</label>
                <div class="col-lg-9">
                    '.$html_list_staff.'
                </div>
            </div>

            <div class="form-group row" hidden>
                <label class="col-lg-3 col-form-label">Phòng ban hiện tại:</label>
                <div class="col-lg-9">
                    <textarea class="form-control old_department" cols="1" rows="1" name="old_department_update" readonly="true" 
                    value="'.$id.'">'.$department_old_name.'</textarea>
                </div>
            </div>

            <div class="form-group row" hidden>
                <label class="col-lg-3 col-form-label">Phòng ban điều chuyển:</label>
                <div class="col-lg-9"  readonly="true">
                '.$html_list_department.'    
                </div>   
            </div>

            <div class="form-group row">
            <label class="col-lg-3 col-form-label">Xác nhận:(*)</label>
            <div class="col-lg-9">
                <select class="form-control txthr" name="txthr">
                '.$html_hr.'
                </select>
            </div>   
            </div>
           
            <div class="form-group row" hidden>
            <label class="col-lg-3 col-form-label">Lương đề xuất:</label>
            <div class="col-lg-9">
            <textarea class="form-control" name="txtNewSalary" id="txtNewSalary" cols="1" rows="1"  readonly="true">'.$body['data']['newSalary'].'</textarea> 
            </div>   
            </div>

            <div class="form-group row" hidden>
            <label class="col-lg-3 col-form-label">Ý kiến Giám đốc:(*)</label>
            <div class="col-lg-9">
            <textarea class="form-control" name="txtnoteManager" id="txtnoteManager" require cols="2" rows="3" placeholder="VD: Giám đốc nhập ý kiến...">'.$body['data']['noteManager'].'</textarea>
            </div>   
            </div>         

            <div class="form-group row" hidden >
                <label class="col-lg-3 col-form-label">Ghi chú:</label>
                <div class="col-lg-9">
                    <textarea class="form-control" name="note_update" id="note" cols="20" rows="10" placeholder="VD: Quản lý yêu cầu, ..." required>'.$body['data']['note'].'</textarea>
                </div>
            </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-primary">Xác nhận</button>
        </div>
        ';
       
        echo $html;
        die;  
    }

    // GD Tu choi
    public function detailC(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        $response = Http::get('http://localhost:8888/transfer/detail', $data_request);
        $body = json_decode($response->body(), true);

        $data_request_staff = [
            "id" => $body['data']['staffId']
        ];

        $response_staff = Http::get('http://localhost:8888/staff/one', $data_request_staff);
        $body_staff = json_decode($response_staff->body(), true);

        $data_request_old_department = ['id' => $body_staff['data']['department']];

        $response_old_department = Http::get('http://localhost:8888/department/detail', $data_request_old_department);
        $department_old = json_decode($response_old_department->body(), true);
        $department_old_name = $department_old['data']['name'];

        $response = Http::get(config('app.api_url') . '/staff/list', []);
        $listStaff = json_decode($response->body(), false);

        $response = Http::get('http://localhost:8888/department/list');
        $body_department = json_decode($response->body(), true);
        $data_department = $body_department['data'];

        $html_list_staff = '';
        foreach ($listStaff->data as $staff) {
            if($body['data']['staffId'] == $staff->id) {
                $html_list_staff .= '<option value="'.$staff->id.'" selected>'.$staff->firstname .' '. $staff->lastname.'</option>';
            }
        }

        // $html_list_department = '';
        // foreach ($data_department as $department) {
        //     if($body['data']['newDepartment'] == $department['id']) {
        //         $html_list_department .= '<input class="form-control" cols="1" rows="1" 
        //         name="new_department_update" id="new_department_update" readonly="true" value="'.$department['id'].'" "'.$department['name'].'"/>';
        //     }
        // }

        $html_list_department = '';
        foreach ($data_department as $department) {
            if($body['data']['newDepartment'] == $department['id']) {
                $html_list_department .= '<option value="'.$department['id'].'" selected>'.$department['name'].'</option>';
            } else {
                $html_list_department .= '<option value="'.$department['id'].'">'.$department['name'].'</option>';
            }
        }

        $html_hr = '';
         
                $html_hr .= '<option value="0" selected>Xác nhận</option>';
                $html_hr .= '<option value="1">Không xác nhận</option>';
           

        $html = '<input type="hidden" name="id_update" value="'.$id.'">';
        $html.= '
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Từ Chối Điều Chuyển Mới</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Tên nhân viên</label>
                <div class="col-lg-9">
                    <select  disabled class="form-control select-search select_staff_transfer" name="staff_id_update"  id="selected_staff" readonly="true">
                        '.$html_list_staff.'
                    </select>
                </div>
            </div>

            <div class="form-group row" hidden>
                <label class="col-lg-3 col-form-label">Phòng ban hiện tại:</label>
                <div class="col-lg-9">
                    <select  class="form-control old_department" name="old_department_update" readonly="true">
                        <option value="'.$id.'">'.$department_old_name.'</option>
                    </select>
                </div>
            </div>

            <div class="form-group row" hidden>
            <label class="col-lg-3 col-form-label">Phòng ban điều chuyển:</label>
            <div class="col-lg-9">
                <select class="form-control new_department" name="new_department_update">
                '.$html_list_department.'
                </select>
            </div>   
            </div>

            <div class="form-group row" hidden>
            <label class="col-lg-3 col-form-label">Xác nhận:(*)</label>
            <div class="col-lg-9">
                <select class="form-control txthr" name="txthr">
                '.$html_hr.'
                </select>
            </div>   
            </div>

            <div class="form-group row" hidden>
            <label class="col-lg-3 col-form-label">Lương đề xuất:</label>
            <div class="col-lg-9">
            <textarea class="form-control" name="txtNewSalary" id="txtNewSalary" cols="1" rows="1">'.$body['data']['newSalary'].'</textarea> 
            </div>   
            </div>
           
            <div class="form-group row">
            <label class="col-lg-3 col-form-label">Ý kiến Giám đốc:(*)</label>
            <div class="col-lg-9">
                <textarea class="form-control" name="txtnoteManager" id="txtnoteManager" cols="2" rows="3" placeholder="VD: Giám đốc nhập ý kiến...">'.$body['data']['noteManager'].'</textarea>
            </div>   
            </div>         

            <div class="form-group row" hidden>
                <label class="col-lg-3 col-form-label">Ghi chú:</label>
                <div class="col-lg-9">
                    <textarea class="form-control" name="note_update" id="note" cols="20" rows="10" placeholder="VD: Quản lý yêu cầu, ..." required>'.$body['data']['note'].'</textarea>
                </div>
            </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-primary">Gửi</button>
        </div>
        ';
       
        echo $html;
        die;  
    }

    // detail
    public function detail1(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        $response = Http::get('http://localhost:8888/transfer/detail', $data_request);
        $body = json_decode($response->body(), true);
        // dd($body);
        $data_request_staff = [
            "id" => $body['data']['staffId']
        ];

        $response_staff = Http::get('http://localhost:8888/staff/one', $data_request_staff);
        $body_staff = json_decode($response_staff->body(), true);

        $data_request_old_department = ['id' => $body_staff['data']['department']];

        $response_old_department = Http::get('http://localhost:8888/department/detail', $data_request_old_department);
        $department_old = json_decode($response_old_department->body(), true);
        $department_old_name = $department_old['data']['name'];

        $response = Http::get(config('app.api_url') . '/staff/list', []);
        $listStaff = json_decode($response->body(), false);

        $response = Http::get('http://localhost:8888/department/list');
        $body_department = json_decode($response->body(), true);
        $data_department = $body_department['data'];

        $html_list_staff = '';
        foreach ($listStaff->data as $staff) {
            if($body['data']['staffId'] == $staff->id) {
                $html_list_staff .= '<option value="'.$staff->id.'" selected>'.$staff->firstname .' '. $staff->lastname.'</option>';
            }
        }

        $html_list_department = '';
        foreach ($data_department as $department) {
            if($body['data']['newDepartment'] == $department['id']) {
                $html_list_department .= '<option value="'.$department['id'].'" selected>'.$department['name'].'</option>';
            } else {
                $html_list_department .= '<option value="'.$department['id'].'">'.$department['name'].'</option>';
            }
        }
           

        $html = '<input type="hidden" name="id_update" value="'.$id.'">';
        $html.= '
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Chi Tiết Điều Chuyển</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Tên nhân viên</label>
                <div class="col-lg-9">
                    <select class="form-control select-search select_staff_transfer" name="staff_id_update"  id="selected_staff" readonly="true">
                        '.$html_list_staff.'
                    </select>
                </div>
            </div>

            <div class="form-group row" hidden>
                <label class="col-lg-3 col-form-label">Phòng ban hiện tại:</label>
                <div class="col-lg-9">
                    <select class="form-control old_department" name="old_department_update" readonly="true">
                        <option value="'.$id.'">'.$department_old_name.'</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Phòng ban điều chuyển:</label>
                <div class="col-lg-9">
                    <select disabled class="form-control new_department" name="new_department_update">
                        '.$html_list_department.'
                    </select>
                </div>   
            </div>
            <div class="form-group row">
            <label class="col-lg-3 col-form-label">Ngày nhân viên tạo:</label>
            <div class="col-lg-9" >
                <textarea readonly class="form-control" name="note_update" id="note" cols="1" rows="1">'.$body['data']['createdAt'].'</textarea>
            </div>
             </div>
            <div class="form-group row">
            <label class="col-lg-3 col-form-label">Ngày Giám đốc duyệt:</label>
            <div class="col-lg-9" >
                <textarea readonly class="form-control" name="note_update" id="note" cols="1" rows="1">'.$body['data']['updateAt'].'</textarea>
            </div>
             </div>

            <div class="form-group row">
            <label class="col-lg-3 col-form-label">Ý kiến Giám đốc:(*)</label>
            <div class="col-lg-9">
                <textarea readonly class="form-control" name="txtnoteManager" id="txtnoteManager" cols="2" rows="3" placeholder="VD: Giám đốc nhập ý kiến...">'.$body['data']['noteManager'].'</textarea>
            </div>   
            </div>  
            <div class="form-group row">
                <label class="col-lg-3 col-form-label">Ghi chú:</label>
                <div class="col-lg-9" >
                    <textarea readonly class="form-control" name="note_update" id="note" cols="5" rows="5" placeholder="VD: Quản lý yêu cầu, ..." required>'.$body['data']['note'].'</textarea>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        </div>
        ';
       
        echo $html;
        die;
    }

    public function update(Request $request)
    {
        // $rule = [
        //     'txtNewSalary' => 'min:1000000|max:200000000',
        //     'note_update' => 'max:300',
        //     'txtnoteManager' => 'max:300',
        // ];
        // $message = [
        //     'txtNewSalary.min' => 'Lương đề nghị phải lớn hơn 1,000,000 và nhỏ hơn 200,000,000đ',
        //     'txtNewSalary.max' => 'Lương đề nghị phải lớn hơn 1,000,000 và nhỏ hơn 200,000,000đ',
        //     'note_update.max' => 'Ghi chú không quá 300 ký tự',
        //     'txtnoteManager.min' => 'Không được nhập quá 300 ký tự',
        // ];
        // $data = $request->all();
        // $validate = Validator::make($data, $rule, $message);

        // if ($validate->fails()) {
        //     return redirect()->back()->withErrors($validate->errors())->withInput();
        // }

        $user = auth()->user();

        $id_update = $request->input('id_update');
        $old_department = $request->input('old_department_update');
        $new_department = $request->input('new_department_update');
        $hr_approved =$request->input('txthr');
        $new_salary =$request->input('txtNewSalary');
        $note_manager =$request->input('txtnoteManager');
        $note = $request->input('note_update');

        if($old_department == $new_department) {
            return redirect()->back()->with('error', 'Phòng ban hiện tại và phòng ban điều chuyển phải khác nhau!');
        }

        // 'age'=>'between:18,30'
        if(strlen($note) > 300) {
            return redirect()->back()->with('error', 'Ghi chú không được vượt quá 300 kí tự');
        }
        
        if(strlen($note_manager) > 300) {
            return redirect()->back()->with('error', 'Ý kiến không được vượt quá 300 kí tự');
        }
        
        $data_request = [
            'id' => $id_update,
            'new_department' => $new_department,    
            'hr_approved'=>$hr_approved,
            'new_salary'=>$new_salary,
            'note_manager'=>$note_manager,
            'note' => $note
        ];

        // dd($data_request);

        $response = Http::post('http://localhost:8888/transfer/update', $data_request);
        $body = json_decode($response->body(), true);

        // dd($data_request);

        if($body['message'] == "Update success") {
            return redirect()->back()->with('success', 'Đã thực hiện thành công!');
        } 
        else {
            return redirect()->back()->with('error', 'Thực hiện thất bại!');
        }
    }

    public function approve(Request $request) {
        $id = $request->input('id');
        $department = auth()->user()->department;

        $data_request = [
            'id' => $id,
            'department' => $department
        ];

        //validae
        $month = $request->input('month');
        $year = $request->input('year');
        if(!$month) {
            $month = date("m");
        }
        if(!$year) {
            $year = date("Y");
        }

        $date = $year . '-' . $month . '-' . '01';
        $data_request1 = ['day_get' => $date, 'department' => auth()->user()->department];

        $response1 = Http::get('http://localhost:8888/transfer/list', $data_request1);
        $transfer_json = json_decode($response1->body(), false);
        $tran = $transfer_json->data;

        // dd($body1);
               foreach ($tran as $toi) {
                //    dd($toi['id']);
                    if($toi->id == $id){
                        if($toi->old_manager_approved ==0){
                            if($toi->old_department != auth()->user()->department){
                                return redirect()->back()->with('error', 'Trưởng phòng hiện tại chưa phê duyệt');
                            }  
                        }
                    }  
                }

                foreach ($tran as $toi) {
                        if($toi->id == $id){
                            if($toi->new_manager_approved ==0){
                                if(auth()->user()->department==2){
                                    if($toi->old_department !=2 && $toi->new_department !=2){
                                        return redirect()->back()->with('error', 'Nhân viên không thuộc phòng HR');
                                    }
                                }  
                            }
                        }  
                    }
 

        $response = Http::get('http://localhost:8888/transfer/approve', $data_request);
        $body = json_decode($response->body(), true);
        // dd($data_request);
          
      

        if($body['data'] == "Approve Success") {
            return redirect()->back()->with('success', 'Phê duyệt thành công, khi quản lý còn lại và Giám đốc phê duyệt, nhân viên sẽ chuyển phòng ban!');
        }
         else if($body['data'] == "Approve manager") {
            return redirect()->back()->with('success', 'Phê duyệt thành công, nhân viên đã chuyển phòng ban!');
        }else if($body['data'] == "Staff changed department") {
            return redirect()->back()->with('success', 'Phê duyệt thành công, nhân viên đã chuyển phòng ban!'); 
        }
        else {
            return redirect()->back()->with('error', 'Phê duyệt điều chuyển thất bại!');
        }
    }

    // public function getDeleteTransfer(Request $request)
    // {
    //     $id = $request->id;
    //     $response = Http::get(config('app.api_url') . '/transfer/delete', ['id' => $id]);
    //     $body = json_decode($response->body(), false);
    //   // dd($body);
    //     if ($body->isSuccess) {
    //         return redirect()->back()->with('message', ['type' => 'success', 'message' => 'Xóa Phòng ban thành công.']);
    //     }
    //     return redirect()->back()->with('message', ['type' => 'danger', 'message' => 'Xóa Phòng ban thất bại.']);
    // }
}
