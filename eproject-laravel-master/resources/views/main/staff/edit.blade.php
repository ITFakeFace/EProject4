@extends('main._layouts.master')

<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        #tb_dkp_wrapper {
            display: none;
        }

        .wrap-select {
            width: 302px;
            overflow: hidden;
        }

        .wrap-select select {
            width: 320px;
            margin: 0;
            background-color: #212121;
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
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
    <script src="{{asset('global_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
    <script src="{{asset('global_assets/js/plugins/forms/styling/uniform.min.js')}}"></script>
    <script src="{{asset('global_assets/js/demo_pages/form_layouts.js')}}"></script>

@endsection


@section('content')
    <!-- Basic datatable -->
    <!-- 2 columns form -->
    <div class="card">
        <div class="card-header header-elements-inline">
            <h1 class="pt-3 pl-3 pr-3">Cập Nhật Nhân Viên</h1>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ action('StaffController@postEditStaff') }}" method="post" enctype="multipart/form-data">
                <input type="hidden" value="{{$data['id']}}" name="txtID"/>
                @csrf
                @if(session('message'))
                    <div class="alert alert-{{ session('message')['type'] }} border-0 alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        {{ session('message')['message'] }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <p><b>Dữ liệu đầu vào không chính xác:</b></p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#staff" role="tab" aria-controls="staff" aria-selected="true">Nhân viên</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="allowance-tab" data-toggle="tab" href="#allowance" role="tab" aria-controls="allowance" aria-selected="false">Bằng cấp</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="staff" role="tabpanel" aria-labelledby="staff-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend class="font-weight-semibold"><i class="icon-reading mr-2"></i> Thông tin</legend>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mã Nhân viên:(*)</label>
                                                        <input type="text" class="form-control" name="txtCode" value="{{ $data['code'] }}" require placeholder="Nhập Mã Nhân viên: TTN" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Phân Quyền:(*)</label>
                                                        <!-- <input type="text" class="form-control" name="txtGender"> -->
                                                        <select class="form-control" name="txtisManager" color="red">
                                                            <option value="0" @if($data['isManager'] == 0) selected @endif>Nhân viên</option>
                                                            <option value="1" @if($data['isManager'] == 1) selected @endif>Quản lý</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Họ nhân viên:</label>
                                                        <input type="text" class="form-control" name="txtLname" value="{{ $data['lastname'] }}" placeholder="Nhập Họ">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Tên Nhân viên:(*)</label>
                                                        <input type="text" class="form-control" name="txtFname" value="{{ $data['firstname'] }}" require placeholder="Nhập Tên">
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-6" hidden>
                                                    <div class="form-group">
                                                        <label>Phòng Ban:(*)</label>
                                                        <select class="form-control" name="txtDepartment">
                                                            @foreach($data_department as $dep)
                                                                <option value="{{ $dep['id'] }}" @if($data['department'] == $dep['id']) selected @endif>{{ $dep['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Ngày sinh:</label>
                                                        <input type="text" class="form-control daterange-single" name="txtDob" value="{{ $data['dob'] }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Ngày Vào:(*)</label>
                                                        <input type="text" class="form-control daterange-single" name="txtJoinat" value="{{ $data['joinedAt'] }}">
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">
                                             
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Giới tính:(*)</label>
                                                        <select class="form-control" name="txtGender">
                                                            <option value="1" @if($data['gender'] == 1) selected @endif>Nam</option>
                                                            <option value="0" @if($data['gender'] == 0) selected @endif>Nữ</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Khu vực:(*)</label>
                                                        <!-- <input type="text" class="form-control" name="txtGender"> -->
                                                        <select id="province" class="form-control form-control-select2" color="red" data-fouc>
                                                            @foreach($data_reg as $reg)
                                                                <option value="{{ $reg['id'] }}" @if($district_selected['parent'] == $reg['id']) selected @endif>{{ $reg['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                            
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Thành Phố/Huyện/Xã:(*)</label>
                                                        <select id="district" class="form-control form-control-select2" name="txtRegional" color="red" data-fouc>
                                                            @foreach($data_district as $district)
                                                                <option value="{{ $district['id'] }}" @if($district_selected['id'] == $district['id']) selected @endif>{{ $district['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Điện thoại:</label>
                                                        <input type="number" class="form-control" name="txtPhone" value="{{ $data['phoneNumber'] }}" placeholder="Nhập số điện thoại">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Email:</label>
                                                        <input type="text" class="form-control" name="txtEmail" value="{{ $data['email'] }}" placeholder="Nhập Email abc12@exam.com">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mật khẩu:</label>
                                                        <input type="hidden" class="form-control" name="txtPassOld" value="{{ $data['password'] }}">
                                                        <input type="password" class="form-control" name="txtPass">
                                                    </div>
                                                </div>
                                            </div>
                                           

                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend class="font-weight-semibold"><i class="icon-paperplane mr-2"></i> Hình ảnh</legend>
                                            <div class="form-group">
                                                <label>CMND:(*)</label>
                                                <input type="text" class="form-control" name="txtIDNumber" placeholder="Nhập số CMND" value="{{ $data['idNumber'] }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Ngày cấp:(*)</label>
                                                <input type="text" class="form-control daterange-single" name="txtIssue" value="{{ old('txtIssue') }}">
                                            </div>

                                            <div class="form-group" hidden>
                                                <label>Hình ảnh:</label>
                                                <input type="text" class="form-input-styled" name="txtImagesOld" value="{{$data['photo']}}" data-fouc>
                                            </div>

                                            <div class="form-group">
                                                <label>Hình ảnh:</label>
                                                <p><img width="50px" height="50px" src="{{ asset($data['photo']) }}"></p>
                                                <input type="file" class="form-input-styled" name="txtPhoto" data-fouc>
                                            </div>

                                            <div class="form-group" hidden>
                                                <label>Mặt trước CMND:</label>
                                                <input type="text" class="form-input-styled" name="txtImagesOld2" value="{{$data['idPhoto']}}" data-fouc>
                                            </div>
                                            <div class="form-group">
                                                <label>Mặt trước CMND:</label>
                                                <p><img width="50px" height="50px" src="{{ asset($data['idPhoto']) }}"></p>
                                                <input type="file" class="form-input-styled" name="txtIDPhoto" data-fouc>
                                            </div>

                                            <div class="form-group" hidden>
                                                <label>Mặt sau CMND:</label>
                                                <input type="text" class="form-input-styled" name="txtImagesOld3" value="{{$data['idPhotoBack']}}" data-fouc>
                                            </div>
                                            <div class="form-group">
                                                <label>Mặt sau CMND:</label>
                                                <p><img width="50px" height="50px" src="{{ asset($data['idPhotoBack']) }}"></p>
                                                <input type="file" class="form-input-styled" name="txtIDPhoto2" data-fouc>
                                            </div>

                                            <div class="form-group">
                                                <label>Ghi chú:</label>
                                                <textarea rows="5" cols="5" class="form-control" name="txtNote" placeholder="Nhập Ghi chú">{{ $data['note'] }}</textarea>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label>Createby:</label>
                                                <textarea  class="form-control" name="txtCreateBy">{{ $data['createdBy'] }}</textarea>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label>Createat:</label>
                                                <textarea  class="form-control" name="txtCreatedAt" >{{ $data['createdAt'] }}</textarea>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            {{-- TAB 2 --}}
                            <div class="tab-pane fade" id="allowance" role="tabpanel" aria-labelledby="allowance-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-success" onclick="addOption()"><i title="Thêm chi tiết" class="icon-stack-plus "></i> Thêm bằng cấp</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <legend class="font-weight-semibold"><i class="icon-reading mr-2"></i> Thông tin</legend>
                                        <div id="education">
                                            @foreach($data_edu as $index => $education)
                                                <hr>
                                                <input type="hidden" class="form-control" name="education[{{ $index }}][staffId]" value="{{ $education['staffId'] }}">
                                                <input type="hidden" class="form-control" name="education[{{ $index }}][id]" value="{{ $education['id'] }}">

                                                <div class="row">
                                                    <div class="col-md-2" hidden>
                                                        <div class="form-group">
                                                            <label>Cấp Bậc:</label>
                                                            <input type="text" class="form-control" name="education[{{ $index }}][level]" value="{{ $education['level'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Tên Cấp Bậc:</label>
                                                            <select id="txtLevelName" class="form-control" name="education[{{ $index }}][levelName]">
                                                                <option value="Tiểu học" @if($education['levelName'] == 'Tiểu học') selected @endif>Tiểu học</option>
                                                                <option value="Trung học cơ sở" @if($education['levelName'] == 'Trung học cơ sở') selected @endif>Trung học cơ sở</option>
                                                                <option value="Trung học phổ thông" @if($education['levelName'] == 'Trung học phổ thông') selected @endif>THPT</option>
                                                                <option value="Đại học" @if($education['levelName'] == 'Đại học') selected @endif>Đại học</option>
                                                                <option value="Thạc sĩ" @if($education['levelName'] == 'Thạc sĩ') selected @endif>Thạc sĩ</option>
                                                                <option value="Tiến sĩ" @if($education['levelName'] == 'Tiến sĩ') selected @endif>Tiến sĩ</option>
                                                                <option value="Phó giáo sư" @if($education['levelName'] == 'Phó giáo sư') selected @endif>Phó Giáo sư</option>
                                                                <option value="Giáo sư" @if($education['levelName'] == 'Giáo sư') selected @endif>Giáo sư</option>
                                                                <option value="Khác"> @if($education['levelName'] == 'Khác') selected @endif>Khác</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Tên Trường: (*)</label>
                                                            <input type="text" class="form-control text-uppercase" id="txtSchool" name="education[{{ $index }}][school]" value="{{ $education['school'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Chuyên ngành: (*)</label>
                                                            <input type="text" class="form-control" name="education[{{ $index }}][fieldOfStudy]" value="{{ $education['fieldOfStudy'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Năm tốt nghiệp:(*)</label>
                                                            <input type="text" class="form-control" name="education[{{ $index }}][graduatedYear]" value="{{ $education['graduatedYear'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Xếp loại:</label>
                                                            <input type="text" class="form-control" name="education[{{ $index }}][grade]" value="{{ $education['grade'] }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Hình thức học:</label>
                                                            <input type="text" class="form-control" name="education[{{ $index }}][modeOfStudy]" value="{{ $education['modeOfStudy'] }}">
                                                        </div>
                                                    </div>
                                                        <div class="col-md-2">
                                                            <a href="{{ action('EducationController@deleteEducation') }}?id={{ $education['id'] }}" class="btn btn-success" onclick="return confirm('Bạn chắc chắn muốn xóa?')" style="
                                                            margin-top: -15px;">Xóa</a>
                                                        </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-success">Lưu <i class="icon-paperplane ml-2"></i></button>
                    <a class="btn btn-primary" role="button" href="{{ action('StaffController@index') }}" style="color:white;">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
    <!-- /2 columns form -->
    <!-- /basic datatable -->
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/picker_date_init.js') }}"></script>

    <script>
        let optionIndex = {{ count($data_edu) - 1 }};

        function addOption() {
            optionIndex++;
            $('#education').append(`
                    <hr>
                    <input type="hidden" value="{{$data['id']}}" name="education[${optionIndex}][staffId]"/>

                    <div class="row">
                        <div class="col-md-2" hidden>
                            <div class="form-group">
                                <label>Cấp Bậc:</label>
                                <input type="text" class="form-control" value="2" name="education[${optionIndex}][level]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tên Cấp Bậc:</label>
                                <select id="txtLevelName" class="form-control" name="education[${optionIndex}][levelName]">
                                    <option value="Tiểu học">Tiểu học</option>
                                    <option value="Trung học cơ sở">THCS</option>
                                    <option value="Trung học phổ thông">THPT</option>
                                    <option value="Đại học">Đại học</option>
                                    <option value="Thạc sĩ">Thạc sĩ</option>
                                    <option value="Tiến sĩ">Tiến sĩ</option>
                                    <option value="Phó giáo sư">Phó Giáo sư</option>
                                    <option value="Giáo sư">Giáo sư</option>
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tên Trường: (*)</label>
                                <input type="text" class="form-control text-uppercase" id="txtSchool" name="education[${optionIndex}][school]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Chuyên ngành: (*)</label>
                                <input type="text" class="form-control" name="education[${optionIndex}][fieldOfStudy]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Năm tốt nghiệp:(*)</label>
                                <input type="text" class="form-control" name="education[${optionIndex}][graduatedYear]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Xếp loại:</label>
                                <input type="text" class="form-control" name="education[${optionIndex}][grade]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Hình thức học:</label>
                                <input type="text" class="form-control" name="education[${optionIndex}][modeOfStudy]">
                            </div>
                        </div>
                    </div>
            `);
        }

        $('#province').on('change', function () {
            var parent = this.value;

            $.ajax({
                url: '{{ action('StaffController@loadRegional') }}',
                Type: 'GET',
                datatype: 'text',
                data:
                    {
                        parent: parent,
                    },
                cache: false,
                success: function (data) {
                    var obj = $.parseJSON(data);
                    $('#district').empty();
                    for (var i = 0; i < obj.length; i++) {
                        $('#district').append('<option value="' + obj[i]['id'] + '">' + obj[i]['name'] + '</option>');
                    }
                }
            });
        });

        $('.open-detail-time-leave').click(function () {
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
                success: function (data) {
                    console.log(data);
                    $('#html_pending').empty().append(data);
                    $('#bsc-modal').modal();
                }
            });
        });

    </script>



@endsection
