@extends('main._layouts.master')

<?php
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        #tb_dkp_wrapper, #tb_leave_other_wrapper {
            display: none;
        }
    </style>  
@endsection

@section('js')    
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/notifications/jgrowl.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('global_assets/js/demo_pages/picker_date.js') }}"></script>
	<script src="{{asset('global_assets/js/plugins/forms/styling/uniform.min.js')}}"></script>
    <script src="{{asset('global_assets/js/demo_pages/form_layouts.js')}}"></script>
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Bổ Sung Công / Đăng Kí Phép</h1>
        <div class="card-header header-elements-inline">
            <h4 class="card-title font-weight-bold text-uppercase">
                <?php echo auth()->user()->firstname . " " . auth()->user()->lastname ?> 
                - <?php echo $staff[0][2] ?> 
                - <?php echo auth()->user()->is_manager == 1 ? 'Quản lý' : 'Nhân viên' ?>
            </h4>
            <h4 class="card-title font-weight-bold text-uppercase">
                Số ngày phép : <?php echo auth()->user()->day_of_leave ?> ngày 
            </h4>
        </div>
        <div class="card-body">
            @if (\Session::has('success'))
                <div class="">
                    <div class="alert alert-success">
                        {!! \Session::get('success') !!}
                    </div>
                </div>
            @endif

            @if (\Session::has('error'))
                <div class="">
                    <div class="alert alert-danger">
                        {!! \Session::get('error') !!}
                    </div>
                </div>
            @endif
            <form action="{{ action('TimeleaveController@index') }}" method="GET">
                @csrf
                <div class="form-group d-flex">
                    <div class="">
                        <select class="form-control" name="month" id="month">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" <?php echo $month == $i ? 'selected' : ''?>>Tháng {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="ml-2">
                        <input class="form-control" type="number" value="<?php echo $year ?>" name="year" id="year">
                    </div>
                    <div class="ml-3">
                        <input class="form-control btn btn-primary" type="submit" value="Tìm kiếm">
                    </div>
                </div>
            </form>

            <div class="form-group d-flex">
                <div class="">
                    <button class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter">Bổ Sung Công</button>
                </div>
                <div class="ml-1">
                    <button id="register_leave" class="btn btn-info" data-toggle="modal" data-target="#exampleModalCenter2">Đăng Kí Phép</button>
                </div>
            </div>

            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <button class="nav-link active" id="btn_tb_bsc" style="border: 1px solid gainsboro;">Bổ sung công</button>
                <li class="nav-item">
                    <button class="nav-link" id="btn_tb_dkp" style="border: 1px solid gainsboro;">Đăng kí phép năm tính lương</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="btn_leave_other" style="border: 1px solid gainsboro;">Đăng kí phép khác</button>
                </li>
            </ul>
        </div>
        <!-- Modal bsc -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ action('TimeleaveController@createTime') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Bổ Sung Công</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Ngày bổ sung:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control day_leave" name="day_leave" value="" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Yêu cầu điều chỉnh:</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="number_day_leave" id="number_day_leave" required>
                                        <option value="1">Một ngày</option>
                                        <option value="0.5">Nửa ngày</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Hình ảnh(Nếu có):</label>
                                <div class="col-lg-9">
                                    <input type="file" class="form-input-styled" name="txtImage" data-fouc>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Lý do:</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" name="note_bsc" id="note_bsc" cols="20" rows="10" placeholder="VD: Quên check in, Quên check out, ..." required></textarea>
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
                            <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>

         <!-- Modal dkp -->
        <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ action('TimeleaveController@createLeave') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Đăng Kí Phép</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Loại phép:</label>
                                <div class="col-lg-9">
                                    <select class="form-control type_of_leave" name="type_of_leave" id="type_of_leave" required>
                                        <option value="0" selected>Phép năm tính lương</option>
                                        <option value="2">Nghỉ không lương</option>
                                        <option value="3">Nghỉ ốm đau ngắn ngày</option>
                                        <option value="4">Nghỉ ốm dài ngày</option>
                                        <option value="5">Thai sản</option>
                                        <option value="6">Kết hôn</option>
                                        <option value="7">Ma chay</option>
                                    </select>
                                </div>
                            </div>

                            <div class="leave-basic">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Ngày đăng kí phép:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control day_leave" name="day_leave" value="" required>
                                    </div>
                                </div>
    
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Yêu cầu phép:</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="number_day_leave" id="number_day_leave" required>
                                            <option value="1">Một ngày</option>
                                            <option value="0.5">Nửa ngày</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="leave-long" style="display: none">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Từ ngày:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control day_leave" name="day_leave_from" value="" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Đến ngày:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control day_leave" name="day_leave_to" value="" required>
                                    </div>
                                </div>
    
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Hình ảnh:</label>
                                    <div class="col-lg-9">
                                        <input type="file" class="form-input-styled" name="image_leave" data-fouc>
                                    </div>
                                </div>
                            </div>                         

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Lý do:</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" name="note_dkp" id="note_dkp" cols="20" rows="5" placeholder="VD: Bận việc gia đình, Đi học, ..." required></textarea>
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

                            <div class="des-leave des-leave2" style="display: none">
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

                            <div class="des-leave des-leave3" style="display: none">
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
                                                <b>1. Diễn giải: </b>Bản thân nghỉ ốm đau theo chỉ định của Bác sĩ và được bệnh viện cấp giấy nghỉ hưởng bảo hiểm xã hội (theo mẫu C65) hoặc giấy ra viện trong thời gian nghỉ. <br>
                                                <b>2. Đối tượng áp dụng: </b> Nhân viên đang tham gia Bảo hiểm bắt buộc tại Công ty. <br>
                                                <b>3. Hồ sơ yêu cầu: </b> Yêu cầu gửi Giấy nghỉ hưởng bảo hiểm xã hội (theo mẫu C65)/ giấy ra viện bản chính. Cơ quan BHXH chỉ thanh toán tiền lương các ngày nghỉ khi nhân viên gửi đầy đủ các hồ sơ hợp lệ theo yêu cầu về cho Công Ty.. <br>
                                                <b>4. Lương: </b> Cơ quan BHXH tính & trả tiền lương các ngày nghỉ căn cứ trên hồ sơ mà cá nhân nộp lên cho Công ty (tính theo mức lương tham gia Bảo hiểm bắt buộc hàng tháng). <br>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="des-leave des-leave4" style="display: none">
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

                            <div class="des-leave des-leave5" style="display: none">
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

                            <div class="des-leave des-leave6" style="display: none">
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

                            <div class="des-leave des-leave7" style="display: none">
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
                            <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
  
        <table class="table datatable-basic" id="tb_bsc">
            <thead>
                <tr>
                    <th>Ngày </th>
                    <th>Ngày công</th>
                    <th>Loại</th>
                    <th>Ghi chú</th>
                    <th>Phê duyệt</th>
                    <th>Sửa / Xóa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $time_leave)
                    @if($time_leave['type'] == 0)
                        <tr>
                            <td>{{ $time_leave['dayTimeLeave'] }}</td>
                            <td><?php echo $time_leave['time'] == "08:00:00" ? '1 ngày công' : '0.5 ngày công' ?></td>
                            <td><?php echo $time_leave['type'] == 0 ? 'Bổ sung công' : 'Đăng kí phép' ?></td>
                            <td>
                                <?php 
                                    if(strlen($time_leave['note']) > 20) echo substr($time_leave['note'], 0, 30) . '...';
                                    else echo $time_leave['note'];    
                                ?>
                            </td>
                            <td>
                                @if($time_leave['isApproved'] == 0)
                                    <span class="badge badge-warning">Chưa phê duyệt</span>
                                @elseif($time_leave['isApproved'] == 2)
                                    <span class="badge badge-success">Quản lý đã phê duyệt</span>
                                @else
                                    <span class="badge badge-primary">Giám đốc đã phê duyệt</span>
                                @endif
                            </td>
                            @if($time_leave['done'] == 1)
                                <td><span class="badge badge-danger">Đã chốt</span></td>    
                            @elseif($time_leave['isApproved'] == 0 || ($time_leave['isApproved'] == 2 && auth()->user()->is_manager == 1))
                                <?php
                                    $date1=date_create($time_leave['createdAt']);
                                    $date2=date_create(date('Y-m-d'));
                                    $diff=date_diff($date1,$date2);
                                ?>
                                @if($diff->format("%a") > 1)
                                    <td>
                                        <div class="from-group d-flex">
                                            Đã quá 2 ngày kể từ khi bổ sung công
                                        </div>
                                    </td>
                                @else
                                    <td>
                                        <div class="from-group d-flex">
                                            <a class="btn btn-info open-detail-time-leave" id="{{ $time_leave['id'] }}" style="color: white; cursor: pointer;">Sửa</a>
                                            <a href="{{ action('TimeleaveController@deleteTime') }}?id={{ $time_leave['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Xóa</a>
                                        </div>
                                    </td>
                                @endif
                            @elseif($time_leave['isApproved'] == 2)
                                <td>Quản lý đã phê duyệt, chờ giám đốc phê duyệt!</td>
                            @else
                                <td>Giám đốc đã phê duyệt, không thể chỉnh sửa!</td>
                            @endif
                        </tr>                        
                    @endif
                @endforeach       
            </tbody>
        </table>

        <table class="table datatable-basic" id="tb_dkp" style="display: none">
            <thead>
                <tr>
                    <th>Ngày </th>
                    <th>Ngày công</th>
                    <th>Loại</th>
                    <th>Ghi chú</th>
                    <th>Phê duyệt</th>
                    <th>Sửa / Xóa</th>
                </tr>
                    
            </thead>
            <tbody>
                @foreach ($data as $time_leave)
                    @if($time_leave['type'] == 1)
                        <tr>
                            <td>{{ $time_leave['dayTimeLeave'] }}</td>
                            <td><?php echo $time_leave['time'] == "08:00:00" ? '1 ngày công' : '0.5 ngày công' ?></td>
                            <td><?php echo $time_leave['type'] == 0 ? 'Bổ sung công' : 'Đăng kí phép' ?></td>
                            <td>
                                <?php 
                                    if(strlen($time_leave['note']) > 20) echo substr($time_leave['note'], 0, 30) . '...';
                                    else echo $time_leave['note'];    
                                ?>
                            </td>
                            <td>
                                @if($time_leave['isApproved'] == 0)
                                    <span class="badge badge-warning">Chưa phê duyệt</span>
                                @elseif($time_leave['isApproved'] == 2)
                                    <span class="badge badge-success">Quản lý đã phê duyệt</span>
                                @else
                                    <span class="badge badge-primary">Giám đốc đã phê duyệt</span>
                                @endif
                            </td>
                            @if($time_leave['done'] == 1)
                                <td><span class="badge badge-danger">Đã chốt</span></td>    
                            @elseif($time_leave['isApproved'] == 0 || ($time_leave['isApproved'] == 2 && auth()->user()->is_manager == 1))
                                <td>
                                    <div class="from-group d-flex">
                                        <a class="btn btn-info open-detail-dkp" id="{{ $time_leave['id'] }}" style="color: white; cursor: pointer;">Sửa</a>
                                        <a href="{{ action('TimeleaveController@deleteTime') }}?id={{ $time_leave['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Xóa</a>
                                    </div>
                                </td>
                            @elseif($time_leave['isApproved'] == 2)
                                <td>Quản lý đã phê duyệt, chờ giám đốc phê duyệt!</td>
                            @else
                                <td>Giám đốc đã phê duyệt, không thể chỉnh sửa!</td>
                            @endif
                        </tr>                        
                    @endif
                @endforeach         
            </tbody>
        </table>

        <table class="table datatable-basic" id="tb_leave_other" style="display: none">
            <thead>
                <tr>
                    <th>Từ ngày </th>
                    <th>Đến ngày</th>
                    <th>Loại phép</th>
                    <th>Ghi chú</th>
                    <th>Phê duyệt</th>
                    <th>Sửa / Xóa</th>
                </tr>
                    
            </thead>
            <tbody>
                @foreach ($leave_other as $item)
                    <tr>
                        <td>{{ $item['fromDate'] }}</td>
                        <td>{{ $item['toDate'] }}</td>
                        <td>
                            <?php 
                                if($item['typeLeave'] == 2) echo "Nghỉ không lương";
                                else if($item['typeLeave'] == 3) echo "Nghỉ ốm đau ngắn ngày";
                                else if($item['typeLeave'] == 4) echo "Nghỉ ốm đau dài ngày";
                                else if($item['typeLeave'] == 5) echo "Nghỉ thai sản";
                                else if($item['typeLeave'] == 6) echo "Nghỉ kết hôn";
                                else if($item['typeLeave'] == 7) echo "Nghỉ ma chay";
                            ?>
                        </td>
                        <td>
                            <?php 
                                if(strlen($item['note']) > 20) echo substr($item['note'], 0, 30) . '...';
                                else echo $item['note'];    
                            ?>
                        </td>
                        <td>
                            @if($item['isApproved'] == 0)
                                <span class="badge badge-warning">Chưa phê duyệt</span>
                            @elseif($item['isApproved'] == 2)
                                <span class="badge badge-success">Quản lý đã phê duyệt</span>
                            @else
                                <span class="badge badge-primary">Giám đốc đã phê duyệt</span>
                            @endif
                        </td>
                        @if($item['done'] == 1)
                            <td><span class="badge badge-danger">Đã chốt</span></td>    
                        @elseif($item['isApproved'] == 0 || ($item['isApproved'] == 2 && auth()->user()->is_manager == 1))
                            <td>
                                <div class="from-group d-flex">
                                    <a class="btn btn-info open-detail-leave-other" id="{{ $item['id'] }}" style="color: white; cursor: pointer;">Sửa</a>
                                    <a href="{{ action('TimeleaveController@deleteLeaveOther') }}?id={{ $item['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Xóa</a>
                                </div>
                            </td>
                        @elseif($item['isApproved'] == 2)
                            <td>Quản lý đã phê duyệt, chờ giám đốc phê duyệt!</td>
                        @else
                            <td>Giám đốc đã phê duyệt, không thể chỉnh sửa!</td>
                        @endif
                    </tr>                        
                @endforeach         
            </tbody>
        </table>

        <div id="bsc-modal" class="modal fade" role="dialog"> <!-- modal bsc -->
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="{{ action('TimeleaveController@updateTime') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div id="html_pending">
                        
                    </div>
                </form> <!-- end form -->
              </div>
            </div>
        </div> <!-- end modal bsc -->

        <div id="dkp-modal" class="modal fade" role="dialog"> <!-- modal dkp -->
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="{{ action('TimeleaveController@updateTime') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div id="html_pending">
                        
                    </div>
                </form> <!-- end form -->
              </div>
            </div>
        </div> <!-- end modal dkp -->

        <div id="dkp-leave-other" class="modal fade" role="dialog"> <!-- modal dkp -->
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="{{ action('TimeleaveController@updateLeaveOther') }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div id="html_pending3">
                        
                    </div>
                </form> <!-- end form -->
              </div>
            </div>
        </div> <!-- end modal dkp -->
          
    </div>
    <!-- /basic datatable -->
@endsection

@section('scripts')
    <script>
        $('.day_bsc').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('.day_leave').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('.type_of_leave').change(function() {
            let type_of_leave = $(this).val();
            if(type_of_leave == 0) {
                $('.leave-basic').show();
                $('.leave-long').hide();
            } else {
                $('.leave-basic').hide();
                $('.leave-long').show();
            }

            switch (type_of_leave) {
                case "0":
                    $('.des-leave').hide();
                    $('.des-leave0').show();
                    break;
                case "2":
                    $('.des-leave').hide();
                    $('.des-leave2').show();
                    break;
                case "3":
                    $('.des-leave').hide();
                    $('.des-leave3').show();
                    break;
                case "4":
                    $('.des-leave').hide();
                    $('.des-leave4').show();
                    break;
                case "5":
                    $('.des-leave').hide();
                    $('.des-leave5').show();
                    break;
                case "6":
                    $('.des-leave').hide();
                    $('.des-leave6').show();
                    break;
                case "7":
                    $('.des-leave').hide();
                    $('.des-leave7').show();
                    break;
                default:
                    break;
            }
        });

        $( "#btn_tb_bsc" ).click(function() {
            $('#tb_dkp').hide();
            $('#tb_dkp_wrapper').hide();
            $('#tb_leave_other').hide();
            $('#tb_leave_other_wrapper').hide();
            $('#tb_bsc').show();
            $('#tb_bsc_wrapper').show();
            $(this).addClass('active');
            $('#btn_tb_dkp').removeClass('active');
            $('#btn_leave_other').removeClass('active');
        });

        $( "#btn_tb_dkp" ).click(function() {
            $('#tb_bsc').hide();
            $('#tb_bsc_wrapper').hide();
            $('#tb_leave_other').hide();
            $('#tb_leave_other_wrapper').hide();
            $('#tb_dkp').show();
            $('#tb_dkp_wrapper').show();
            $(this).addClass('active');
            $('#btn_tb_bsc').removeClass('active');
            $('#btn_leave_other').removeClass('active');
        });

        $( "#btn_leave_other" ).click(function() {
            $('#tb_bsc').hide();
            $('#tb_bsc_wrapper').hide();
            $('#tb_dkp').hide();
            $('#tb_dkp_wrapper').hide();
            $('#tb_leave_other').show();
            $('#tb_leave_other_wrapper').show();
            $(this).addClass('active');
            $('#btn_tb_bsc').removeClass('active');
            $('#btn_tb_dkp').removeClass('active');
        });

        $('.open-detail-time-leave').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('TimeleaveController@detailTime') }}',
                Type: 'POST',
                datatype: 'text',
                data:
                {
                    id: id,
                },
                cache: false,
                success: function (data)
                {
                    console.log(data);
                    $('#html_pending').empty().append(data);
                    $('#bsc-modal').modal();
                }
            });
        });

        $('.open-detail-dkp').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('TimeleaveController@detailLeave') }}',
                Type: 'POST',
                datatype: 'text',
                data:
                {
                    id: id,
                },
                cache: false,
                success: function (data)
                {
                    console.log(data);
                    $('#html_pending').empty().append(data);
                    $('#bsc-modal').modal();
                }
            });
        });

        $('.open-detail-leave-other').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('TimeleaveController@detailLeaveOther') }}',
                Type: 'POST',
                datatype: 'text',
                data:
                {
                    id: id,
                },
                cache: false,
                success: function (data)
                {
                    console.log(data);
                    $('#html_pending3').empty().append(data);
                    $('#dkp-leave-other').modal();
                }
            });
        });

    </script>
@endsection