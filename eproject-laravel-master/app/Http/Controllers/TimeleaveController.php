<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DateTime;

class TimeleaveController extends Controller
{
    public function index(Request $request)
    {
        $params_get_department = [
            'id' => auth()->user()->id,
        ];
        $response_get_department = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params_get_department);
        $body_get_department = json_decode($response_get_department->body(), true);

        $user = auth()->user();

        $month = $request->input('month');
        $year = $request->input('year');
        if(!$month) {
            $month = date("m");
        }
        if(!$year) {
            $year = date("Y");
        }

        $date = $year . '-' . $month . '-' . '01';
        $data_request = ['staff_id' => $user->id, 'day_time_leave' => $date];

        $response = Http::post('http://localhost:8888/time-leave/list', $data_request);
        $body = json_decode($response->body(), true);

        $data_request_leave_other = ['staff_id' => $user->id, 'month_get' => $date];

        $response = Http::get('http://localhost:8888/leave-other/list', $data_request_leave_other);
        $leave_other = json_decode($response->body(), true);

        return view('main.time_leave.index')
            ->with('data', $body['data'])
            ->with('leave_other', $leave_other['data'])
            ->with('year', $year)
            ->with('month', $month)
            ->with('staff', $body_get_department['data'])
            ->with('breadcrumbs', [['text' => 'Công phép', 'url' => '../view-menu/time-leave'], ['text' => 'Bổ sung công phép', 'url' => '#']]);
    }

    public function createTime(Request $request)
    {
        $user = auth()->user();

        $day_leave = $request->input('day_leave');
        $number_day_leave = $request->input('number_day_leave');
        $note_bsc = $request->input('note_bsc');

        if($day_leave > date('Y-m-d')) {
            return redirect()->back()->with('error', 'Không được bổ sung công trước ngày hiện tại');
        }

        $date1=date_create($day_leave);
        $date2=date_create(date('Y-m-d'));
        $diff=date_diff($date1,$date2);
        if($diff->format("%a") > 1) {
            return redirect()->back()->with('error', 'Không được bổ sung công cách quá 2 ngày hiện tại');
        }

        if(strlen($note_bsc) > 300) {
            return redirect()->back()->with('error', 'Lý do không được vượt quá 300 kí tự');
        }
        //Photo
        $now = Carbon::now();
        $image_time = '';

        if(request()->hasFile('txtImage')) {
            // random name cho ảnh
            $file_name_random = function ($key) {
                $ext = request()->file($key)->getClientOriginalExtension();
                $str_random = (string)Str::uuid();

                return $str_random . '.' . $ext;
            };

            $image = $file_name_random('txtImage');
            if (request()->file('txtImage')->move('./images/time_leave/' . $now->format('dmY') . '/', $image)) {
                // gán path ảnh vào model để lưu
                $image_time = '/images/time_leave/' . $now->format('dmY') . '/' . $image;
            }
        }

        if($number_day_leave == 1)
            $time = "08:00:00";
        else
            $time = "04:00:00";

        $is_approved = 0;
        if($user->is_manager == 1) {
            $is_approved = 2;
        }
        
        $data_request = [
            "staff_id" => $user->id,
            'staff_code' => $user->code,
            'day_time_leave' => $day_leave,
            'time' => $time,
            'image' => $image_time,
            'type' => false,
            'note' => $note_bsc,
            'is_approved' => $is_approved,
            'created_at' => date('Y-m-d')
        ];

        $response = Http::post('http://localhost:8888/time-leave/add', $data_request);
        $body = json_decode($response->body(), true);

        if($body['message'] == "Save success") {
            if($user->is_manager == 1) {
                return redirect()->back()->with('success', 'Bổ sung công thành công! Vì là cấp quản lý nên bổ sung công tự động phê duyệt');
            } else {
                return redirect()->back()->with('success', 'Bổ sung công thành công! Vui lòng đợi quản lý phê duyệt');
            }
        } else if($body['data'] == "Added time") {
            return redirect()->back()->with('error', 'Bổ sung công thất bại! Bạn đã đi làm và chấm công ngày ' . $day_leave . ' rồi! Vui lòng chỉnh sửa');
        } else {
            return redirect()->back()->with('error', 'Bổ sung công thất bại! Bạn đã bổ sung công / đăng kí phép ngày ' . $day_leave . ' rồi! Vui lòng chỉnh sửa');
        }
    }

    public function deleteTime(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        Http::post('http://localhost:8888/time-leave/delete', $data_request);

        return redirect()->back()->with('success', 'Xóa thành công!');
    }

    public function detailTime(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        $response = Http::get('http://localhost:8888/time-leave/detail', $data_request);
        $body = json_decode($response->body(), true);

        if($body['data']['time'] == '08:00:00') {
            $option = '
                <option value="1" selected>Một ngày</option>
                <option value="0.5">Nửa ngày</option>
            ';
        } else {
            $option = '
                <option value="1">Một ngày</option>
                <option value="0.5" selected>Nửa ngày</option>
            ';
        }
        

        $html = "<input type='hidden' name='id_update' value='". $id ."'>";
        $html.= "<input type='hidden' name='type_update' value='". $body['data']['type'] ."'>";
        $html.= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Bổ Sung Công</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        $html.= '<span aria-hidden="true">&times;</span></button></div>';
        $html.= '
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Ngày bổ sung:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control day_leave_update" name="day_leave_update" value="'.$body['data']['dayTimeLeave'].'" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Yêu cầu điều chỉnh:</label>
                    <div class="col-lg-9">
                        <select class="form-control" name="number_day_leave_update" id="number_day_leave_update" required>
                            '.$option.'
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Hình ảnh cũ:</label>
                    <div class="col-lg-9">
                        <img src="..'.$body['data']['image'].'" alt="" style="max-height: 250px; max-width: 200px">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Hình ảnh mới:</label>
                    <div class="col-lg-9">
                        <input type="file" class="" name="txtImage">
                        <input type="hidden" class="" name="txtImageOld" value="'.$body['data']['image'].'">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Lý do:</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="note_bsc_update" id="note_bsc_update" cols="20" rows="5" placeholder="VD: Quên check in, Quên check out, ..." required>'.$body['data']['note'].'</textarea>
                    </div>
                </div>

                <div class="des-bsc">
                    <h3>Mô tả chi tiết</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Số ngày công bổ sung tối đa một lần</b>
                                <p>1 công hoặc 0.5 công / 1 lần bổ sung</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Thông tin bổ sung công</b>
                                <p>
                                    <b>1. Diễn giải: </b>Nhân viên sử dụng để bổ sung công cho những ngày có đi làm nhưng quên chấm công ra vào. Được cộng bù công nếu quản lý phòng ban và giám đốc phê duyệt. <br>
                                    <b>2. Đối tượng áp dụng: </b> Nhân viên đã ký hợp đồng chính thức với Công ty. <br>
                                    <b>3. Hồ sơ yêu cầu: </b> Không. <br>
                                    <b>4. Lương: </b> Được công ty trả lương những ngày có đi làm nhưng quên chấm công.
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Sửa</button>
            </div>

            <script>
                $(".day_leave_update").daterangepicker({
                    singleDatePicker: true,
                    locale: {
                        format: "YYYY-MM-DD"
                    }
                });
            </script>
        ';
       
        echo $html;
        die;
    }

    public function updateTime(Request $request)
    {
        $user = auth()->user();

        $id_update = $day_leave = $request->input('id_update');
        $day_leave = $request->input('day_leave_update');
        $number_day_leave = $request->input('number_day_leave_update');
        $note_bsc = $request->input('note_bsc_update');
        $image_time = $request->input('txtImageOld') ? $request->input('txtImageOld') : '';
        $type = $request->input('type_update');

        if($type == 0) {
            if($day_leave > date('Y-m-d')) {
                return redirect()->back()->with('error', 'Không được bổ sung công trước ngày hiện tại');
            }

            $date1=date_create($day_leave);
            $date2=date_create(date('Y-m-d'));
            $diff=date_diff($date1,$date2);
            if($diff->format("%a") > 1) {
                return redirect()->back()->with('error', 'Không được bổ sung công cách quá 2 ngày hiện tại');
            }
        }

        if(strlen($note_bsc) > 300) {
            return redirect()->back()->with('error', 'Lý do không được vượt quá 300 kí tự');
        }

        //Photo
        $now = Carbon::now();

        if(request()->hasFile('txtImage')) {
            // random name cho ảnh
            $file_name_random = function ($key) {
                $ext = request()->file($key)->getClientOriginalExtension();
                $str_random = (string)Str::uuid();

                return $str_random . '.' . $ext;
            };

            $image = $file_name_random('txtImage');
            if (request()->file('txtImage')->move('./images/time_leave/' . $now->format('dmY') . '/', $image)) {
                // gán path ảnh vào model để lưu
                $image_time = '/images/time_leave/' . $now->format('dmY') . '/' . $image;
            }
        }

        if($number_day_leave == 1)
            $time = "08:00:00";
        else
            $time = "04:00:00";

        $check_special_day = [
            'day_check' => $day_leave
        ];

        $response = Http::get('http://localhost:8888/special-date/check-day', $check_special_day);
        $body = json_decode($response->body(), true);

        if($body['data'] == "Yes") {
            return redirect()->back()->with('error', 'Chỉnh sửa thất bại! ' . $day_leave . ' là ngày lễ! Vui lòng chỉnh sửa');
        }
        
        $data_request = [
            "id" => $id_update,
            "staff_id" => $user->id,
            'day_time_leave' => $day_leave,
            'time' => $time,
            'note' => $note_bsc,
            'image' => $image_time,
        ];

        $response = Http::post('http://localhost:8888/time-leave/update', $data_request);
        $body = json_decode($response->body(), true);

        if($body['message'] == "Update success") {
            return redirect()->back()->with('success', 'Chỉnh sửa thành công! Vui lòng đợi phê duyệt');
        } 
        else if($body['data'] == "Added time") {
            return redirect()->back()->with('error', 'Bổ sung công / Đăng kí phép thất bại! Bạn đã đi làm và chấm công ngày ' . $day_leave . ' rồi! Vui lòng chỉnh sửa');
        }
        else {
            return redirect()->back()->with('error', 'Chỉnh sửa thất bại! Bạn đã Bổ sung công / Đăng kí phép ngày ' . $day_leave . ' rồi!');
        }
    }

    //Phep
    public function createLeave(Request $request)
    {
        $user = auth()->user();

        $type_of_leave = $request->input('type_of_leave');

        if($type_of_leave == 0) {
            if($user->day_of_leave == 0) {
                return redirect()->back()->with('error', 'Bạn đã hết ngày phép');
            }
    
            $day_leave = $request->input('day_leave');
            $number_day_leave = $request->input('number_day_leave');
            $note_dkp = $request->input('note_dkp');
    
            if(strlen($note_dkp) > 300) {
                return redirect()->back()->with('error', 'Lý do không được vượt quá 300 kí tự');
            }
    
            if($number_day_leave == 1)
                $time = "08:00:00";
            else
                $time = "04:00:00";
    
            $is_approved = 0;
            if($user->is_manager == 1) {
                $is_approved = 2;
            }
    
            if(date('w', strtotime($day_leave)) == 6 or date('w', strtotime($day_leave)) == 0) {
                return redirect()->back()->with('error', 'Đăng kí phép thất bại! ' . $day_leave . ' là Thứ 7 / Chủ nhật! Vui lòng chỉnh sửa');
            }
    
            $check_special_day = [
                'day_check' => $day_leave
            ];
    
            $response = Http::get('http://localhost:8888/special-date/check-day', $check_special_day);
            $body = json_decode($response->body(), true);
    
            if($body['data'] == "Yes") {
                return redirect()->back()->with('error', 'Đăng kí phép thất bại! ' . $day_leave . ' là ngày lễ! Vui lòng chỉnh sửa');
            }
            
            $data_request = [
                "staff_id" => $user->id,
                'staff_code' => $user->code,
                'day_time_leave' => $day_leave,
                'time' => $time,
                'type' => true,
                'note' => $note_dkp,
                'is_approved' => $is_approved
            ];
    
            $response = Http::post('http://localhost:8888/time-leave/addLeave', $data_request);
            $body = json_decode($response->body(), true);
    
            if($body['message'] == "Save success") {
                if($user->is_manager == 1) {
                    return redirect()->back()->with('success', 'Đăng kí phép thành công! Vì là cấp quản lý nên đăng kí phép tự động phê duyệt');
                } else {
                    return redirect()->back()->with('success', 'Đăng kí phép thành công! Vui lòng đợi phê duyệt');
                }
            } 
            else if($body['data'] == "Added time") {
                return redirect()->back()->with('error', 'Đăng kí phép thất bại! Bạn đã đi làm và chấm công ngày ' . $day_leave . ' rồi! Vui lòng chỉnh sửa');
            }
            else {
                return redirect()->back()->with('error', 'Đăng kí phép thất bại! Bạn đã Đăng kí phép / Bổ sung công ngày ' . $day_leave . ' rồi! Vui lòng chỉnh sửa');
            }
        } else {
            $day_leave_from = $request->input('day_leave_from');
            $day_leave_to = $request->input('day_leave_to');
            $image_leave = $request->input('image_leave');
            $note_dkp = $request->input('note_dkp');

            if($day_leave_from > $day_leave_to) {
                return redirect()->back()->with('error', 'Từ ngày không được lớn hơn đến ngày');
            }

            $data_check = [
                "staff_id" => $user->id,
                'day_leave_from' => $day_leave_from,
                'day_leave_to' => $day_leave_to
            ];

            $response = Http::post('http://localhost:8888/leave-other/check-list-time-leave', $data_check);
            $time_leave_exists = json_decode($response->body(), true);

            if(count($time_leave_exists['data']) > 0) {
                return redirect()->back()->with('error', 'Đã có bổ sung công hoặc đăng kí phép năm tính lương vào trong số ngày đăng kí phép trên! Vui lòng thử lại');
            }

            if($type_of_leave == 6 or $type_of_leave == 7) {
                $day_from_check = $day_leave_from;
                if(date('w', strtotime($day_from_check)) == 6 or date('w', strtotime($day_from_check)) == 0) {
                    return redirect()->back()->with('error', 'Không được đặt ngày lễ có chứa Thứ 7 / Chủ nhật! Vui lòng chỉnh sửa');
                }
                while($day_from_check <= $day_leave_to) {
                    if(date('w', strtotime($day_from_check)) == 6 or date('w', strtotime($day_from_check)) == 0) {
                        return redirect()->back()->with('error', 'Không được đặt ngày phép có chứa Thứ 7 / Chủ nhật! Vui lòng chỉnh sửa');
                    }
                    $day_from_check = date('Y-m-d', strtotime($day_from_check. ' + 1 days'));
                }            
            }

            //Validate day of other leave
            switch ($type_of_leave) {
                case '2':
                    $origin = new DateTime($day_leave_from);
                    $target = new DateTime($day_leave_to);
                    $interval = $origin->diff($target);
                    if($interval->format('%a') > 32) {
                        return redirect()->back()->with('error', 'Loại phép nghỉ không lương chỉ được đăng kí tối đa 31 ngày');
                    }
                    break;
                case '3':
                    $origin = new DateTime($day_leave_from);
                    $target = new DateTime($day_leave_to);
                    $interval = $origin->diff($target);
                    if($interval->format('%a') > 2) {
                        return redirect()->back()->with('error', 'Loại phép nghỉ ốm đau ngắn ngày chỉ được đăng kí tối đa 3 ngày');
                    }
                    break;
                case '4':
                    $origin = new DateTime($day_leave_from);
                    $target = new DateTime($day_leave_to);
                    $interval = $origin->diff($target);
                    if($interval->format('%a') > 32) {
                        return redirect()->back()->with('error', 'Loại phép nghỉ ốm đau dài ngày chỉ được đăng kí tối đa 31 ngày');
                    }
                    break;
                case '5':
                    $origin = new DateTime($day_leave_from);
                    $target = new DateTime($day_leave_to);
                    $interval = $origin->diff($target);
                    if($interval->format('%a') > 184) {
                        return redirect()->back()->with('error', 'Loại phép nghỉ ốm đau dài ngày chỉ được đăng kí tối đa 6 tháng');
                    }
                    break;
                case '6':
                    $origin = new DateTime($day_leave_from);
                    $target = new DateTime($day_leave_to);
                    $interval = $origin->diff($target);
                    if($interval->format('%a') > 2) {
                        return redirect()->back()->with('error', 'Loại phép nghỉ kết hôn chỉ được đăng kí tối đa 3 ngày');
                    }
                    break;
                case '7':
                    $origin = new DateTime($day_leave_from);
                    $target = new DateTime($day_leave_to);
                    $interval = $origin->diff($target);
                    if($interval->format('%a') > 2) {
                        return redirect()->back()->with('error', 'Loại phép nghỉ ma chay chỉ được đăng kí tối đa 3 ngày');
                    }
                    break;
                default:
                    # code...
                    break;
            }

            if(strlen($note_dkp) > 300) {
                return redirect()->back()->with('error', 'Lý do không được vượt quá 300 kí tự');
            }

            $is_approved = 0;
            if($user->is_manager == 1) {
                $is_approved = 2;
            }

            //Photo
            $now = Carbon::now();

            if(request()->hasFile('image_leave')) {
                // random name cho ảnh
                $file_name_random = function ($key) {
                    $ext = request()->file($key)->getClientOriginalExtension();
                    $str_random = (string)Str::uuid();

                    return $str_random . '.' . $ext;
                };

                $image = $file_name_random('image_leave');
                if (request()->file('image_leave')->move('./images/other_leave/' . $now->format('dmY') . '/', $image)) {
                    // gán path ảnh vào model để lưu
                    $image_time = '/images/other_leave/' . $now->format('dmY') . '/' . $image;
                }
            } else {
                return redirect()->back()->with('error', 'Vui lòng bổ sung hình ảnh');
            }

            $data_request = [
                'id_update' => null,
                "staff_id" => $user->id,
                'type_leave' => $type_of_leave,
                'day_leave_from' => $day_leave_from,
                'day_leave_to' => $day_leave_to,
                'image' => $image_time,
                'note' => $note_dkp,
                'is_approved' => $is_approved,
                'created_at' => date("Y-m-d")
            ];

            $response = Http::post('http://localhost:8888/leave-other/add', $data_request);
            $body = json_decode($response->body(), true);

            if($body['message'] == "Save success") {
                if($user->is_manager == 1) {
                    return redirect()->back()->with('success', 'Đăng kí phép thành công! Vì là cấp quản lý nên đăng kí phép tự động phê duyệt');
                } else {
                    return redirect()->back()->with('success', 'Đăng kí phép thành công! Vui lòng đợi phê duyệt');
                }
            }
            else if($body['data'] == "Added time") {
                return redirect()->back()->with('error', 'Đăng kí phép thất bại! Bạn đã có đi làm và chấm công trong những ngày bạn đăng kí phép rồi! Vui lòng chỉnh sửa');
            }
            else if($body['data'] == "Duplicate leave") {
                return redirect()->back()->with('error', 'Đăng kí phép thất bại! Bạn không thể đăng kí phép chồng chéo nhau! Vui lòng chỉnh sửa');
            }
            else {
                return redirect()->back()->with('error', 'Đăng kí phép thất bại!');
            }
        }
    }

    public function detailLeave(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        $response = Http::get('http://localhost:8888/time-leave/detail', $data_request);
        $body = json_decode($response->body(), true);

        if($body['data']['time'] == '08:00:00') {
            $option = '
                <option value="1" selected>Một ngày</option>
                <option value="0.5">Nửa ngày</option>
            ';
        } else {
            $option = '
                <option value="1">Một ngày</option>
                <option value="0.5" selected>Nửa ngày</option>
            ';
        }
        

        $html = "<input type='hidden' name='id_update' value='". $id ."'>";
        $html.= "<input type='hidden' name='type_update' value='". $body['data']['type'] ."'>";
        $html.= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Đăng Kí Phép</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        $html.= '<span aria-hidden="true">&times;</span></button></div>';
        $html.= '
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Loại phép:</label>
                    <div class="col-lg-9 col-form-label">
                        Phép năm tính lương
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Ngày đăng kí phép:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control day_leave_update" name="day_leave_update" value="'.$body['data']['dayTimeLeave'].'" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Yêu cầu phép:</label>
                    <div class="col-lg-9">
                        <select class="form-control" name="number_day_leave_update" id="number_day_leave_update" required>
                            '.$option.'
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Lý do:</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="note_bsc_update" id="note_bsc_update" cols="20" rows="10" placeholder="VD: Bận việc gia đình, Đi học, ..." required>'.$body['data']['note'].'</textarea>
                    </div>
                </div>

                <div class="des-leave des-leave0">
                    <h3>Mô tả chi tiết</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Số ngày nghỉ tối đa một lần</b>
                                <p>1 ngày / 1 lần đăng kí</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Thông tin phép</b>
                                <p>
                                    <b>1. Diễn giải: </b>Nhân viên sử dụng ngày phép năm để sử dụng việc riêng. <br>
                                    <b>2. Đối tượng áp dụng: </b> Nhân viên đã ký hợp đồng chính thức với Công ty. <br>
                                    <b>3. Hồ sơ yêu cầu: </b> Không. <br>
                                    <b>4. Lương: </b> Được công ty trả lương những ngày nghỉ.
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Sửa</button>
            </div>

            <script>
                $(".day_leave_update").daterangepicker({
                    singleDatePicker: true,
                    locale: {
                        format: "YYYY-MM-DD"
                    }
                });
            </script>
        ';
       
        echo $html;
        die;
    }

    public function detailLeaveOther(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        $response = Http::get('http://localhost:8888/leave-other/get-detail', $data_request);
        $body = json_decode($response->body(), true);

        if($body['data']['typeLeave'] == 2){
            $option1 = '<option value="2" selected>Nghỉ không lương</option>';
            $display1 = '';
        } else {
            $option1 = '<option value="2">Nghỉ không lương</option>';
            $display1 = 'style = "display: none"';
        }

        if($body['data']['typeLeave'] == 3) {
            $option2 = '<option value="3" selected>Nghỉ ốm đau ngắn ngày</option>';
            $display2 = '';
        } else {
            $option2 = '<option value="3">Nghỉ ốm đau ngắn ngày</option>';
            $display2 = 'style = "display: none"';
        }                     

        if($body['data']['typeLeave'] == 4){
            $option3 = '<option value="4" selected>Nghỉ ốm dài ngày</option>';
            $display3 = '';
        }else {
            $option3 = '<option value="4">Nghỉ ốm dài ngày</option>';
            $display3 = 'style = "display: none"';
        }       

        if($body['data']['typeLeave'] == 5) {
            $option4 = '<option value="5" selected>Thai sản</option>';
            $display4 = '';
        } else {
            $option4 = '<option value="5">Thai sản</option>';
            $display4 = 'style = "display: none"';
        }    
        
        if($body['data']['typeLeave'] == 6) {
            $option6 = '<option value="6" selected>Kết hôn</option>';
            $display6 = '';
        } else {
            $option6 = '<option value="6">Kết hôn</option>';
            $display6 = 'style = "display: none"';
        } 
        
        if($body['data']['typeLeave'] == 7) {
            $option7 = '<option value="7" selected>Ma chay</option>';
            $display7 = '';
        } else {
            $option7 = '<option value="7">Ma chay</option>';
            $display7 = 'style = "display: none"';
        } 
        
        $html = "<input type='hidden' name='id_update' value='". $id ."'>";
        $html.= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Đăng Kí Phép</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        $html.= '<span aria-hidden="true">&times;</span></button></div>';
        $html.= '
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Loại phép:</label>
                    <div class="col-lg-9">
                        <select class="form-control type_of_leave" name="type_of_leave" id="type_of_leave" required>
                            '.$option1.'
                            '.$option2.'
                            '.$option3.'
                            '.$option4.'
                            '.$option6.'
                            '.$option7.'
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Từ ngày:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control day_leave_update" name="day_leave_from" value="'.$body['data']['fromDate'].'" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Đến ngày:</label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control day_leave_update" name="day_leave_to" value="'.$body['data']['toDate'].'" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Hình ảnh cũ:</label>
                    <div class="col-lg-9">
                        <img src="..'.$body['data']['image'].'" alt="" style="max-height: 250px; max-width: 200px">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Hình ảnh mới:</label>
                    <div class="col-lg-9">
                        <input type="file" class="" name="image_leave">
                        <input type="hidden" class="" name="txtImageOld" value="'.$body['data']['image'].'">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Lý do:</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="note_bsc_update" id="note_bsc_update" cols="20" rows="3" placeholder="VD: Bận việc gia đình, Đi học, ..." required>'.$body['data']['note'].'</textarea>
                    </div>
                </div>

                <div class="des-leave des-leave2" '.$display1.'>
                    <h3>Mô tả chi tiết</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Số ngày nghỉ tối đa một lần</b>
                                <p>1 tháng</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Thông tin phép</b>
                                <p>
                                    <b>1. Diễn giải: </b>Nhân viên đã dùng hết phép năm trong 01 chu kỳ và khi không đáp ứng các điều kiện để sử dụng các loại phép còn lại (nghỉ việc riêng hưởng lương, nghỉ phép bảo hiểm). <br>
                                    <b>2. Đối tượng áp dụng: </b> Áp dụng cho tất cả nhân viên có nhu cầu nghỉ việc riêng (ông/ bà mất, nghỉ ốm đau không có chỉ định của bác sĩ và giấy nghỉ hưởng chế độ bảo hiểm, nghỉ khám nghĩa vụ quận sự...) <br>
                                    <b>3. Hồ sơ yêu cầu: </b> Không. <br>
                                    <b>4. Lương: </b> không được hưởng lương các ngày nghỉ. <br>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="des-leave des-leave3" '.$display2.'>
                    <h3>Mô tả chi tiết</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Số ngày nghỉ tối đa một lần</b>
                                <p>7 ngày</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Thông tin phép</b>
                                <p>
                                    <b>1. Diễn giải: </b>Bản thân nghỉ ốm đau theo chỉ định của Bác sĩ và được bệnh viện cấp giấy nghỉ hưởng bảo hiểm xã hội (theo mẫu C65) hoặc giấy ra viện trong thời gian nghỉ. <br>
                                    <b>2. Đối tượng áp dụng: </b> Nhân viên đang tham gia Bảo hiểm bắt buộc tại Công ty. <br>
                                    <b>3. Hồ sơ yêu cầu: </b> Yêu cầu gửi Giấy nghỉ hưởng bảo hiểm xã hội (theo mẫu C65)/ giấy ra viện bản chính. Cơ quan BHXH chỉ thanh toán tiền lương các ngày nghỉ khi nhân viên gửi đầy đủ các hồ sơ hợp lệ theo yêu cầu về cho Công Ty.. <br>
                                    <b>4. Lương: </b> Cơ quan BHXH tính & trả tiền lương các ngày nghỉ căn cứ trên hồ sơ mà cá nhân nộp lên cho Công ty (tính theo mức lương tham gia Bảo hiểm bắt buộc hàng tháng). <br>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="des-leave des-leave4" '.$display3.'>
                    <h3>Mô tả chi tiết</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Số ngày nghỉ tối đa một lần</b>
                                <p>1 tháng</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Thông tin phép</b>
                                <p>
                                    <b>1. Diễn giải: </b>Chỉ áp dụng đối với các cá nhân mắc các bệnh thuộc danh mục các bệnh cần chữa trị dài ngày do Bộ Y Tế ban hành theo chỉ định của bác sĩ, bệnh viên đăng ký khám chữa bệnh. <br>
                                    <b>2. Đối tượng áp dụng: </b> Nhân viên đang tham gia Bảo hiểm bắt buộc tại Công ty. <br>
                                    <b>3. Hồ sơ yêu cầu: </b> Yêu cầu gửi Giấy ra viện (bản chính) đối với trường hợp điều trị nội trú; Biên bản hội chẩn của bệnh viện (bản chính hoặc bản sao có chứng thực và Giấy xác nhận đợt điều trị (bản chính) trú đối với trường hợp điều trị ngoại trú. Cơ quan BHXH chỉ thanh toán tiền lương các ngày nghỉ khi nhân viên gửi đầy đủ các hồ sơ hợp lệ theo yêu cầu về cho Công Ty. <br>
                                    <b>4. Lương: </b> Cơ quan BHXH tính & trả tiền lương các ngày nghỉ căn cứ trên hồ sơ mà cá nhân nộp lên cho Công ty (tính theo mức lương tham gia Bảo hiểm bắt buộc hàng tháng)
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="des-leave des-leave5" '.$display4.'>
                    <h3>Mô tả chi tiết</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Số ngày nghỉ tối đa một lần</b>
                                <p>6 tháng</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Thông tin phép</b>
                                <p>
                                    <b>1. Diễn giải: </b>Nghỉ sinh con hưởng chế độ Thai sản theo quy định của Nhà nước. <br>
                                    <b>2. Đối tượng áp dụng: </b> Nhân viên có thời gian tham gia bảo hiểm xã hội từ đủ 6 tháng trở lên trong thời gian 12 tháng trước khi sinh con hoặc nhận con nuôi. <br>
                                    <b>3. Hồ sơ yêu cầu: </b> Yêu cầu gửi Giấy khai sinh /chứng sinh /trích lục giấy khai của con (01 bản sao chứng thực, 01 bản/ 01con). Cơ quan BHXH chỉ thanh toán tiền lương các ngày nghỉ khi nhân viên gửi đầy đủ các hồ sơ hợp lệ theo yêu cầu về cho Công Ty. thời gian gửi hồ sơ: ngay sau khi có đủ giấy tờ và không vượt quá thời gian nghỉ thai sản. <br>
                                    <b>4. Lương: </b> Không được Công ty trả lương những ngày nghỉ, chỉ được cơ quan bảo hiểm tính & trả tiền chế độ (dựa trên mức lương tham gia Bảo hiểm bắt buộc hàng tháng) các ngày nghỉ căn cứ trên hồ sơ mà cá nhân nộp lên cho Công ty
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="des-leave des-leave6" '.$display6.'>
                    <h3>Mô tả chi tiết</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Số ngày nghỉ tối đa một lần</b>
                                <p>3 ngày</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Thông tin phép</b>
                                <p>
                                    <b>1. Diễn giải: </b>Bản thân kết hôn. <br>
                                    <b>2. Đối tượng áp dụng: </b> Nhân viên đã ký hợp đồng lao động chính thức với Công ty. <br>
                                    <b>3. Hồ sơ yêu cầu: </b> Yêu cầu upload hình chụp giấy đăng ký kết hôn (Công ty chỉ tính & trả lương khi nhân viên upload hình chụp giấy đăng ký kết hôn lên hệ thống). Nếu không bổ sung hồ sơ hợp lệ, những ngày nghỉ đã đăng ký được tính là nghỉ phép không hưởng lương. <br>
                                    <b>4. Lương: </b> Được công ty tính & trả lương 03 ngày nghỉ
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="des-leave des-leave7" '.$display7.'>
                    <h3>Mô tả chi tiết</h3>
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Số ngày nghỉ tối đa một lần</b>
                                <p>3 ngày</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Thông tin phép</b>
                                <p>
                                    <b>1. Diễn giải: </b>Bố mẹ (cả bên vợ hoặc chồng), vợ, chồng hoặc con cái mất. <br>
                                    <b>2. Đối tượng áp dụng: </b> Nhân viên đã ký hợp đồng lao động chính thức với Công ty. <br>
                                    <b>3. Hồ sơ yêu cầu: </b> Yêu cầu upload hình chụp giấy chứng tử của người mất (Công ty chỉ tính & trả lương khi nhân viên upload hình chụp giấy chứng tử lên hệ thống). Nếu không bổ sung hồ sơ hợp lệ, những ngày nghỉ đã đăng ký được tính là nghỉ phép không hưởng lương. <br>
                                    <b>4. Lương: </b> Được công ty tính & trả lương 03 ngày nghỉ
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>

            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Sửa</button>
            </div>

            <script>
                $(".day_leave_update").daterangepicker({
                    singleDatePicker: true,
                    locale: {
                        format: "YYYY-MM-DD"
                    }
                });

                $(".type_of_leave").change(function() {
                    let type_of_leave = $(this).val();
                    if(type_of_leave == 0) {
                        $(".leave-basic").show();
                        $(".leave-long").hide();
                    } else {
                        $(".leave-basic").hide();
                        $(".leave-long").show();
                    }
        
                    switch (type_of_leave) {
                        case "0":
                            $(".des-leave").hide();
                            $(".des-leave0").show();
                            break;
                        case "2":
                            $(".des-leave").hide();
                            $(".des-leave2").show();
                            break;
                        case "3":
                            $(".des-leave").hide();
                            $(".des-leave3").show();
                            break;
                        case "4":
                            $(".des-leave").hide();
                            $(".des-leave4").show();
                            break;
                        case "5":
                            $(".des-leave").hide();
                            $(".des-leave5").show();
                            break;
                        case "6":
                            $(".des-leave").hide();
                            $(".des-leave6").show();
                            break;
                        case "7":
                            $(".des-leave").hide();
                            $(".des-leave7").show();
                            break;
                        default:
                            break;
                    }
                });
            </script>
        ';
       
        echo $html;
        die;
    }

    public function updateLeaveOther(Request $request)
    {
        $user = auth()->user();

        $type_of_leave = $request->input('type_of_leave');
        $id_update = $request->input('id_update');
        $note_bsc = $request->input('note_bsc_update');
        $image_time = $request->input('txtImageOld') ? $request->input('txtImageOld') : '';
        $day_leave_from = $request->input('day_leave_from');
        $day_leave_to = $request->input('day_leave_to');

        if($day_leave_from > $day_leave_to) {
            return redirect()->back()->with('error', 'Từ ngày không được lớn hơn đến ngày');
        }

        $data_check = [
            "staff_id" => $user->id,
            'day_leave_from' => $day_leave_from,
            'day_leave_to' => $day_leave_to
        ];

        $response = Http::post('http://localhost:8888/leave-other/check-list-time-leave', $data_check);
        $time_leave_exists = json_decode($response->body(), true);

        if(count($time_leave_exists['data']) > 0) {
            return redirect()->back()->with('error', 'Đã có bổ sung công hoặc đăng kí phép năm tính lương vào trong số ngày đăng kí phép trên! Vui lòng thử lại');
        }

        if($type_of_leave == 6 or $type_of_leave == 7) {
            $day_from_check = $day_leave_from;
            if(date('w', strtotime($day_from_check)) == 6 or date('w', strtotime($day_from_check)) == 0) {
                return redirect()->back()->with('error', 'Không được đặt ngày lễ có chứa Thứ 7 / Chủ nhật! Vui lòng chỉnh sửa');
            }
            while($day_from_check <= $day_leave_to) {
                if(date('w', strtotime($day_from_check)) == 6 or date('w', strtotime($day_from_check)) == 0) {
                    return redirect()->back()->with('error', 'Không được đặt ngày lễ có chứa Thứ 7 / Chủ nhật! Vui lòng chỉnh sửa');
                }
                $day_from_check = date('Y-m-d', strtotime($day_from_check. ' + 1 days'));
            }            
        }

        //Validate day of other leave
        switch ($type_of_leave) {
            case '2':
                $origin = new DateTime($day_leave_from);
                $target = new DateTime($day_leave_to);
                $interval = $origin->diff($target);
                if($interval->format('%a') > 32) {
                    return redirect()->back()->with('error', 'Loại phép nghỉ không lương chỉ được đăng kí tối đa 31 ngày');
                }
                break;
            case '3':
                $origin = new DateTime($day_leave_from);
                $target = new DateTime($day_leave_to);
                $interval = $origin->diff($target);
                if($interval->format('%a') > 2) {
                    return redirect()->back()->with('error', 'Loại phép nghỉ ốm đau ngắn ngày chỉ được đăng kí tối đa 3 ngày');
                }
                break;
            case '4':
                $origin = new DateTime($day_leave_from);
                $target = new DateTime($day_leave_to);
                $interval = $origin->diff($target);
                if($interval->format('%a') > 32) {
                    return redirect()->back()->with('error', 'Loại phép nghỉ ốm đau dài ngày chỉ được đăng kí tối đa 31 ngày');
                }
                break;
            case '5':
                $origin = new DateTime($day_leave_from);
                $target = new DateTime($day_leave_to);
                $interval = $origin->diff($target);
                if($interval->format('%a') > 184) {
                    return redirect()->back()->with('error', 'Loại phép nghỉ ốm đau dài ngày chỉ được đăng kí tối đa 6 tháng');
                }
                break;
            case '6':
                $origin = new DateTime($day_leave_from);
                $target = new DateTime($day_leave_to);
                $interval = $origin->diff($target);
                if($interval->format('%a') > 2) {
                    return redirect()->back()->with('error', 'Loại phép nghỉ kết hôn chỉ được đăng kí tối đa 3 ngày');
                }
                break;
            case '7':
                $origin = new DateTime($day_leave_from);
                $target = new DateTime($day_leave_to);
                $interval = $origin->diff($target);
                if($interval->format('%a') > 2) {
                    return redirect()->back()->with('error', 'Loại phép nghỉ ma chay chỉ được đăng kí tối đa 3 ngày');
                }
                break;
            
            default:
                # code...
                break;
        }

        if(strlen($note_bsc) > 300) {
            return redirect()->back()->with('error', 'Lý do không được vượt quá 300 kí tự');
        }

        $is_approved = 0;
        if($user->is_manager == 1) {
            $is_approved = 2;
        }

        //Photo
        $now = Carbon::now();

        if(request()->hasFile('image_leave')) {
            // random name cho ảnh
            $file_name_random = function ($key) {
                $ext = request()->file($key)->getClientOriginalExtension();
                $str_random = (string)Str::uuid();

                return $str_random . '.' . $ext;
            };

            $image = $file_name_random('image_leave');
            if (request()->file('image_leave')->move('./images/other_leave/' . $now->format('dmY') . '/', $image)) {
                // gán path ảnh vào model để lưu
                $image_time = '/images/other_leave/' . $now->format('dmY') . '/' . $image;
            }
        }

        $data_request = [
            'id_update' => $id_update,
            "staff_id" => $user->id,
            'type_leave' => $type_of_leave,
            'day_leave_from' => $day_leave_from,
            'day_leave_to' => $day_leave_to,
            'image' => $image_time,
            'note' => $note_bsc,
            'is_approved' => $is_approved,
            'created_at' => date("Y-m-d")
        ];

        $response = Http::post('http://localhost:8888/leave-other/add', $data_request);
        $body = json_decode($response->body(), true);

        if($body['message'] == "Save success") {
            return redirect()->back()->with('success', 'Chỉnh sửa thành công! Vui lòng đợi phê duyệt');
        }
        else if($body['data'] == "Added time") {
            return redirect()->back()->with('error', 'Chỉnh sửa đăng kí phép thất bại! Bạn đã có đi làm và chấm công trong những ngày bạn đăng kí phép rồi! Vui lòng chỉnh sửa');
        }
        else if($body['data'] == "Duplicate leave") {
            return redirect()->back()->with('error', 'Chỉnh sửa đăng kí phép thất bại! Bạn không thể đăng kí phép chồng chéo nhau! Vui lòng chỉnh sửa');
        }
        else {
            return redirect()->back()->with('error', 'Chỉnh sửa đăng kí phép thất bại!');
        }
    }

    //Approve time leave
    public function approveTimeLeave(Request $request)
    {
        $params_get_department = [
            'id' => auth()->user()->id,
        ];
        $response_get_department = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params_get_department);
        $body_get_department = json_decode($response_get_department->body(), true);

        $user = auth()->user();

        $month = $request->input('month');
        $year = $request->input('year');
        if(!$month) {
            $month = date("m");
        }
        if(!$year) {
            $year = date("Y");
        }

        $date = $year . '-' . $month . '-' . '01';
        $data_request = ['department' => $user->department, 'day_time_leave' => $date, 'is_manager' => $user->is_manager, 'staff_id' => $user->id];

        $response = Http::post('http://localhost:8888/time-leave/get-staff-approve', $data_request);
        $body = json_decode($response->body(), true);

        $response = Http::post('http://localhost:8888/leave-other/get-staff-approve', $data_request);
        $leave_other = json_decode($response->body(), true);

        return view('main.time_leave.approve')
            ->with('data', $body['data'])
            ->with('leave_other', $leave_other['data'])
            ->with('year', $year)
            ->with('month', $month)
            ->with('staff', $body_get_department['data'])
            ->with('breadcrumbs', [['text' => 'Công phép', 'url' => '../view-menu/time-leave'], ['text' => 'Duyệt Công Phép', 'url' => '#']]);  
    }

    public function deleteLeaveOther(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        Http::post('http://localhost:8888/leave-other/delete-leave-other', $data_request);

        return redirect()->back()->with('success', 'Xóa thành công!');
    }

    public function detailStaffApprove(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        $response = Http::get('http://localhost:8888/time-leave/detail-time-staff-approve', $data_request);
        $body = json_decode($response->body(), true);

        if($body['data'][0][3] == 0) {
            $title = 'Nhân Viên Bổ Sung Công';
            $day_time_leave = 'Ngày bổ sung công';
        } else {
            $title = 'Nhân Viên Đăng Kí Phép Năm Tính Lương';
            $day_time_leave = 'Ngày đăng kí phép';
        }

        if($body['data'][0][2] == '08:00:00') {
            $time = 'Một ngày công';
        } else {
            $time = 'Nửa ngày công';
        }

        if($body['data'][0][5] == 1) {
            $approved = '
                Giám đốc đã duyệt
            ';
        } else if($body['data'][0][5] == 2) {
            $approved = '
                Quản lý đã duyệt
            ';
        } else {
            $approved = '
                Chưa duyệt
            ';
        }
        
        $html = "<input type='hidden' name='id' value='". $id ."'>";
        $html.= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">'.$title.'</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        $html.= '<span aria-hidden="true">&times;</span></button></div>';
        $html.= '
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Tên nhân viên:</label>
                    <div class="col-lg-9 col-form-label">
                        '.$body['data'][0][6]. ' ' .$body['data'][0][7].'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Mã nhân viên:</label>
                    <div class="col-lg-9 col-form-label">
                        '.$body['data'][0][8].'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Phòng ban:</label>
                    <div class="col-lg-9 col-form-label">
                        '.$body['data'][0][11].'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Loại phép:</label>
                    <div class="col-lg-9 col-form-label">
                        Phép năm tính lương
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">'.$day_time_leave.':</label>
                    <div class="col-lg-9 col-form-label">
                        '.$body['data'][0][1].'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Yêu cầu:</label>
                    <div class="col-lg-9 col-form-label">
                         '.$time.'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Hình ảnh:</label>
                    <div class="col-lg-9">
                        <img src="..'.$body['data'][0][12].'" alt=""  style="max-height: 250px; max-width: 200px">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Trạng thái:</label>
                    <div class="col-lg-9">
                        '.$approved.'
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Lý do:</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="note_bsc_update" id="note_bsc_update" cols="20" rows="5" placeholder="VD: Bận việc gia đình, Đi học, ..." readonly>'.$body['data'][0][4].'</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Duyệt</button>
            </div>

            <script>
                $(".day_leave_update").daterangepicker({
                    singleDatePicker: true,
                    locale: {
                        format: "YYYY-MM-DD"
                    }
                });
            </script>
        ';
       
        echo $html;
        die;
    }

    public function detailOtherLeaveApprove(Request $request)
    {
        $id = $request->input('id');
        
        $data_request = [
            "id" => $id
        ];

        $response = Http::get('http://localhost:8888/leave-other/detail-time-staff-approve', $data_request);
        $body = json_decode($response->body(), true);

        if($body['data'][0][3] == 2) {
            $type_leave = 'Nghỉ không lương';
        } else if($body['data'][0][3] == 3){
            $type_leave = 'Nghỉ chữa bệnh ngắn ngày';
        } else if($body['data'][0][3] == 4){
            $type_leave = 'Nghỉ chữa bệnh dài ngày';
        } else if($body['data'][0][3] == 5){
            $type_leave = 'Nghỉ thai sản';
        } else if($body['data'][0][3] == 6){
            $type_leave = 'Nghỉ kết hôn';
        } else if($body['data'][0][3] == 7){
            $type_leave = 'Nghỉ ma chay';
        }

        if($body['data'][0][5] == 1) {
            $approved = '
                Giám đốc đã duyệt
            ';
        } else if($body['data'][0][5] == 2) {
            $approved = '
                Quản lý đã duyệt
            ';
        } else {
            $approved = '
                Chưa duyệt
            ';
        }
        
        $html = "<input type='hidden' name='id' value='". $id ."'>";
        $html.= '<div class="modal-header"><h5 class="modal-title" id="exampleModalLongTitle">Nhân Viên Đăng Kí Phép</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        $html.= '<span aria-hidden="true">&times;</span></button></div>';
        $html.= '
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Tên nhân viên:</label>
                    <div class="col-lg-9 col-form-label">
                        '.$body['data'][0][6]. ' ' .$body['data'][0][7].'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Mã nhân viên:</label>
                    <div class="col-lg-9 col-form-label">
                        '.$body['data'][0][8].'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Phòng ban:</label>
                    <div class="col-lg-9 col-form-label">
                        '.$body['data'][0][11].'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Loại phép:</label>
                    <div class="col-lg-9 col-form-label">
                        '.$type_leave.'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Từ ngày:</label>
                    <div class="col-lg-9 col-form-label">
                        '.$body['data'][0][1].'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Đến ngày:</label>
                    <div class="col-lg-9 col-form-label">
                        '.$body['data'][0][2].'
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Hình ảnh:</label>
                    <div class="col-lg-9">
                        <img src="..'.$body['data'][0][12].'" alt=""  style="max-height: 250px; max-width: 200px">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Trạng thái:</label>
                    <div class="col-lg-9 col-form-label">
                        '.$approved.'
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Lý do:</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="note_bsc_update" id="note_bsc_update" cols="20" rows="5" placeholder="VD: Bận việc gia đình, Đi học, ..." readonly>'.$body['data'][0][4].'</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Duyệt</button>
            </div>

            <script>
                $(".day_leave_update").daterangepicker({
                    singleDatePicker: true,
                    locale: {
                        format: "YYYY-MM-DD"
                    }
                });
            </script>
        ';
       
        echo $html;
        die;
    }

    public function approvedTimeLeave(Request $request)
    {
        $id = $request->input('id');
        $is_approved = 2;
        $date = null;

        if(auth()->user()->id == 7) {
            $is_approved = 1;
            $date = date('Y-m-d');
        }
        
        $data_request = [
            "id" => $id,
            "is_approved" => $is_approved,
            "day_approved" => $date
        ];

        $response = Http::post('http://localhost:8888/time-leave/approve-time-leave', $data_request);
        $body = json_decode($response->body(), true);

        if($body['message'] == "Approve success") {
            return redirect()->back()->with('success', 'Duyệt thành công!');
        } 
        else {
            return redirect()->back()->with('error', 'Duyệt thất bại');
        }
    }

    public function approvedLeaveOther(Request $request)
    {
        $id = $request->input('id');
        $is_approved = 2;
        $date = null;

        if(auth()->user()->id == 7) {
            $is_approved = 1;
            $date = date('Y-m-d');
        }
        
        $data_request = [
            "id" => $id,
            "is_approved" => $is_approved,
            "day_approved" => $date
        ];

        $response = Http::post('http://localhost:8888/leave-other/approve-leave-other', $data_request);
        $body = json_decode($response->body(), true);

        if($body['message'] == "Approve success") {
            return redirect()->back()->with('success', 'Duyệt thành công!');
        } 
        else {
            return redirect()->back()->with('error', 'Duyệt thất bại');
        }
    }

    public function getAllStaffTime(Request $request) {
        $user = auth()->user();
        $month = $request->input('month');
        $year = $request->input('year');
        if(!$month) {
            $month = date("m");
        }
        if(!$year) {
            $year = date("Y");
        }
        $date = $year . '-' . $month . '-' . '01';
        $data_request = ['y_m' => $date];

        $response = Http::get('http://localhost:8888/time-leave/get-all-staff-time', $data_request);
        $body = json_decode($response->body(), true);

        $response = Http::get('http://localhost:8888/time-leave/summary-staff-time', $data_request);
        $summary = json_decode($response->body(), true);

        return view('main.time_leave.all_staff_time')
            ->with('data', $body['data'])
            ->with('summary', $summary['data'])
            ->with('year', $year)
            ->with('month', $month)
            ->with('y_m', $date)
            ->with('breadcrumbs', [['text' => 'Công phép', 'url' => '../view-menu/time-leave'], ['text' => 'Tổng hợp chấm công', 'url' => '#']]);
    }

    public function getDetailStaffTime(Request $request) {
        $staff_id = $request->input('staff_id');
        $month = $request->input('month');
        $year = $request->input('year');
        if(!$month) {
            $month = date("m");
        }
        if(!$year) {
            $year = date("Y");
        }
        $date = $year . '-' . $month . '-' . '01';
        $data_request = ['y_m' => $date];

        $response = Http::get('http://localhost:8888/time-leave/get-all-staff-time', $data_request);
        $body = json_decode($response->body(), true);

        $html = "";
        foreach ($body['data'] as $check_in_out) {
            if($check_in_out['staff_id'] == $staff_id) {
                if($check_in_out['special_date_id'] !== null) $color = "#ffe7e7";
                else if($check_in_out['day_of_week'] == 1 or $check_in_out['day_of_week'] == 7) $color = "#d3ffd4";
                else $color = "";

                $check_in_out['is_manager'] == 1 ? $manager = "Quản lý" : $manager = "Nhân viên";
                $check_in_out['day_of_week'] !== 1 ? $day_of_week = "Thứ " . $check_in_out['day_of_week'] : $day_of_week = "Chủ Nhật";
                $check_in_out['special_date_id'] !== null ? $day_of_week .= "(Ngày lễ)" : '';
                

                $html .= "
                <tr style='background-color: ".$color."'>
                    <td>". $check_in_out['code'] ."</td>
                    <td>". $check_in_out['full_name'] ."</td>
                    <td>". $check_in_out['department_name'] ."</td>
                    <td>". $manager ."</td>
                    <td>". $check_in_out['check_in_day'] ."</td>
                    <td>". $day_of_week ."</td>
                    <td class='text-center' style='max-width: 100px;'>". $check_in_out['check_in'] ." <img width='80px' src='../images/check_in/".$check_in_out['image_check_in']."' </td>
                    <td class='text-center' style='max-width: 100px;'>". $check_in_out['check_out'] ." <img width='80px' src='../images/check_in/".$check_in_out['image_check_out']."' </td>
                    <td>". $check_in_out['in_late'] ."</td>
                    <td>". $check_in_out['out_soon'] ."</td>
                    <td>". $check_in_out['number_time'] * $check_in_out['multiply'] ."</td>
                    <td>". $check_in_out['time'] ."</td>
                    <td>". $check_in_out['ot'] ."</td>
                </tr>";
            }
        }
 
        echo $html;
        die;
    }

    public function getAllTimeLeave(Request $request) {
        $month = $request->input('month');
        $year = $request->input('year');
        if(!$month) {
            $month = date("m");
        }
        if(!$year) {
            $year = date("Y");
        }
        $date = $year . '-' . $month . '-' . '01';

        $data_request = ['y_m' => $date];

        $response = Http::get('http://localhost:8888/time-leave/summary-time-leave', $data_request);
        $summary = json_decode($response->body(), true);

        return view('main.time_leave.all_time_leave')
        ->with('summary', $summary['data'])
        ->with('year', $year)
        ->with('month', $month)
        ->with('y_m', $date)
        ->with('breadcrumbs', [['text' => 'Công phép', 'url' => '../view-menu/time-leave'], ['text' => 'Tổng hợp công phép', 'url' => '#']]);
    }

    public function getDetailTimeLeave(Request $request) {
        $staff_id = $request->input('staff_id');
        $month = $request->input('month');
        $year = $request->input('year');
        if(!$month) {
            $month = date("m");
        }
        if(!$year) {
            $year = date("Y");
        }
        $date = $year . '-' . $month . '-' . '01';
        $data_request = ['y_m' => $date];

        $data_request = ['month_get' => $date, 'staff_id' => $staff_id];

        $response = Http::get('http://localhost:8888/time-leave/detail-time-leave-all', $data_request);
        $body = json_decode($response->body(), true);

        $html = "";
        foreach ($body['data'] as $time_leave) {
            if($time_leave['staff_id'] == $staff_id) {
                if($time_leave['special_date_id'] !== null) $color = "#ffe7e7";
                else if($time_leave['day_of_week'] == 1 or $time_leave['day_of_week'] == 7) $color = "#d3ffd4";
                else $color = "";

                $time_leave['is_manager'] == 1 ? $manager = "Quản lý" : $manager = "Nhân viên";
                $time_leave['day_of_week'] !== 1 ? $day_of_week = "Thứ " . $time_leave['day_of_week'] : $day_of_week = "Chủ Nhật";
                $time_leave['day_of_week'] == null ? $day_of_week = "" : $day_of_week = $day_of_week;
                $time_leave['special_date_id'] !== null ? $day_of_week .= "(Ngày lễ)" : '';
                $time_leave['time'] == "08:00:00" ? $time = '1' : $time = '0.5';
                $time_leave['time'] == null ? $time = '0' : $time = $time;
                $time_leave['time'] == "08:00:00" ? $time_multi = 1 * $time_leave['multiply'] . '' : $time_multi = 0.5 * $time_leave['multiply'];
                $time_leave['time'] == null ? $time_multi = '0' : $time_multi = $time_multi;

                switch ($time_leave['type']) {
                    case '1':
                        $type = "Đăng kí phép (Phép năm tính lương)";
                        break;
                    case '2':
                        $type = "Đăng kí phép (Nghỉ không lương)";
                        break;
                    case '3':
                        $type = "Đăng kí phép (Nghỉ ốm đau ngắn ngày)";
                        break;
                    case '4':
                        $type = "Đăng kí phép (Nghỉ ốm dài ngày)";
                        break;
                    case '5':
                        $type = "Đăng kí phép (Nghỉ thai sản)";
                        break;
                    case '6':
                        $type = "Đăng kí phép (Nghỉ kết hôn)";

                        $arr_from_to = explode(' đến ', $time_leave['day_time_leave']);

                        $day_from_check = $arr_from_to[0];
                        $time = 0;
                        $time_multi = 0;
                        while($day_from_check <= $arr_from_to[1]) {
                            $time += 1;
                            $time_multi += 1;
                            $day_from_check = date('Y-m-d', strtotime($day_from_check. ' + 1 days'));
                        }
                        break;
                    case '7':
                        $type = "Đăng kí phép (Nghỉ ma chay)";

                        $arr_from_to = explode(' đến ', $time_leave['day_time_leave']);
                        
                        $day_from_check = $arr_from_to[0];
                        $time = 0;
                        $time_multi = 0;
                        while($day_from_check <= $arr_from_to[1]) {
                            $time += 1;
                            $time_multi += 1;
                            $day_from_check = date('Y-m-d', strtotime($day_from_check. ' + 1 days'));
                        }
                        break;
                    default:
                        $type = "Bổ sung công";
                        break;
                }

                if($time_leave['is_approved'] == 0)
                    $approve = '<span class="badge badge-warning">Chưa phê duyệt</span>';
                elseif($time_leave['is_approved'] == 2)
                    $approve = '<span class="badge badge-success">Quản lý đã phê duyệt</span>';
                else
                    $approve = '<span class="badge badge-primary">Giám đốc đã phê duyệt</span>';

                $html .= "
                <tr style='background-color: ".$color."'>
                    <td>". $time_leave['firstname'] . ' ' . $time_leave['lastname'] ."</td>
                    <td>". $time_leave['name_vn'] ."</td>
                    <td>". $manager ."</td>
                    <td>". $time_leave['day_time_leave'] ."</td>
                    <td>". $day_of_week ."</td>
                    <td>". $type ."</td>
                    <td>". $time ."</td>
                    <td>". $time_multi ."</td>
                    <td>". $approve ."</td>
                </tr>";
            }
        }

        echo $html;
        die;
    }

    public function doneLeave(Request $request) {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        if($from_date > $to_date) {
            return redirect()->back()->with('error', 'Từ ngày không được lớn hơn đến ngày! Vui lòng thử lại');
        }

        $data_request = ['from_date' => $from_date, 'to_date' => $to_date];

        Http::get('http://localhost:8888/time-leave/done-leave', $data_request);
        
        return redirect()->back()->with('success', 'Chốt phép thành công');
    }

    public function getAllTimeInMonth(Request $request) {
        $month = $request->input('month');
        $year = $request->input('year');
        if(!$month) {
            $month = date("m");
        }
        if(!$year) {
            $year = date("Y");
        }
        $date = $year . '-' . $month . '-' . '01';

        $data_request = ['y_m' => $date];

        $response = Http::get('http://localhost:8888/time-leave/summary-time-leave', $data_request);
        $summary_time_leave = json_decode($response->body(), true);

        $response = Http::get('http://localhost:8888/time-leave/summary-staff-time', $data_request);
        $summary_staff_time = json_decode($response->body(), true);

        $response = Http::get('http://localhost:8888/staff/findStaffDepartment');
        $body = json_decode($response->body(), true);
        $data_staff = $body['data'];

        $from = $year . '-' . $month . '-' . '01';
        $to = $year . '-' . $month . '-' . date("t");
        $data_request_time_special = ['from_date' => $from, 'to_date' => $to];

        $response = Http::get('http://localhost:8888/time-special/get-time-special-from-to?', $data_request_time_special);
        $time_specials = json_decode($response->body(), true);

        for ($i = 0; $i < count($data_staff); $i++) { 
            $data_staff[$i]['total_number_time_special'] = 0;
        }

        foreach ($time_specials['data'] as $time_special) {
            for ($i = 0; $i < count($data_staff); $i++) { 
                if($time_special['staff_id'] == $data_staff[$i][3]){
                    $data_staff[$i]['total_number_time_special'] += $time_special['number_time'];
                }
            }
        }


        foreach ($summary_staff_time['data'] as $staff_time) {
            for ($i = 0; $i < count($data_staff); $i++) { 
                if($staff_time['staff_id'] == $data_staff[$i][3]){
                    $data_staff[$i]['total_number_time_all'] = $staff_time['total_number_time_all'];
                }
            }
        }

        foreach ($summary_time_leave['data'] as $staff_time) {
            for ($i = 0; $i < count($data_staff); $i++) { 
                if($staff_time['staff_id'] == $data_staff[$i][3]){
                    $data_staff[$i]['number_time_time_approved'] = $staff_time['number_time_time_approved'];
                    $data_staff[$i]['number_time_leave_approved'] = $staff_time['number_time_leave_approved'];
                }
            }
        }

        for ($i = 0; $i < count($data_staff); $i++) { 
            $data_staff[$i]['total'] = 0;
            if(isset($data_staff[$i]['total_number_time_all'])) {
                $data_staff[$i]['total'] += $data_staff[$i]['total_number_time_all'];
            }
            if(isset($data_staff[$i]['number_time_time_approved'])) {
                $data_staff[$i]['total'] += $data_staff[$i]['number_time_time_approved'];
            }
            if(isset($data_staff[$i]['number_time_leave_approved'])) {
                $data_staff[$i]['total'] += $data_staff[$i]['number_time_leave_approved'];
            }
            if(isset($data_staff[$i]['total_number_time_special'])) {
                $data_staff[$i]['total'] += $data_staff[$i]['total_number_time_special'];
            }
        }

        return view('main.time_leave.all_time')
            ->with('data_staff', $data_staff)
            ->with('summary_staff_time', $summary_staff_time['data'])
            ->with('summary_time_leave', $summary_time_leave['data'])
            ->with('year', $year)
            ->with('month', $month)
            ->with('breadcrumbs', [['text' => 'Công phép', 'url' => '../view-menu/time-leave'], ['text' => 'Tổng công theo tháng', 'url' => '#']]);

    }
}
