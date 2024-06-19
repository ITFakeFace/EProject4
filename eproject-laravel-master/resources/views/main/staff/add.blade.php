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
        <h1 class="pt-3 pl-3 pr-3">Thêm Nhân Viên Mới</h1>
        <div class="card-header header-elements-inline">

        </div>
        <div class="card-body">
            <form action="{{ route('postAddStaff') }}" method="post" enctype="multipart/form-data">
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
                                                        <input type="text" class="form-control" name="txtCode" value="{{ old('txtCode') }}" require placeholder="Nhập Mã Nhân viên: TTN">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Phân Quyền:(*)</label>
                                                        <!-- <input type="text" class="form-control" name="txtGender"> -->
                                                        <select class="form-control" name="txtisManager" color="red">
                                                            <option value="0" selected>Nhân viên</option>
                                                            <option value="1">Quản lý</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Họ nhân viên:</label>
                                                        <input type="text" class="form-control" name="txtLname" value="{{ old('txtLname') }}" placeholder="Nhập Họ">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Tên Nhân viên:(*)</label>
                                                        <input type="text" class="form-control" name="txtFname" value="{{ old('txtFname') }}" require placeholder="Nhập Tên">
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Phòng Ban:(*)</label>
                                                        <select class="form-control" name="txtDepartment" value="{{ old('txtDepartment') }}">
                                                            @foreach($data_department as $dep)
                                                                <option value="{{ $dep['id'] }}">{{ $dep['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Ngày sinh:</label>
                                                        <input type="text" class="form-control daterange-single" name="txtDob" value="{{ old('txtDob') }}">
                                                        {{--                                                            <input type="Date" class="form-control" name="txtDob" value="{{ old('txtDob') }}">--}}
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Ngày Vào:(*)</label>
                                                        <input type="text" class="form-control daterange-single" name="txtJoinat" value="{{ old('txtJoinat') }}">
                                                        {{--                                                            <input type="Date" class="form-control" name="txtJoinat" value="{{ old('txtJoinat') }}">--}}
                                                    </div>
                                                </div>


                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Giới tính:(*)</label>
                                                        <select class="form-control" name="txtGender" color="red">
                                                            <option value="1" selected>Nam</option>
                                                            <option value="0">Nữ</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Khu vực:(*)</label>
                                                        <!-- <input type="text" class="form-control" name="txtGender"> -->
                                                        <select id="province" class="form-control form-control-select2" color="red" data-fouc>
                                                            @foreach($data_reg as $reg)
                                                                <option value="{{$reg['id']}}">{{ $reg['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Thành Phố/Huyện/Xã:(*)</label>
                                                        <select id="district" class="form-control form-control-select2" name="txtRegional" color="red" data-fouc>
                                                            @foreach($data_district as $district)
                                                                <option value="{{$district['id']}}">{{ $district['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Điện thoại:</label>
                                                        <input type="number" class="form-control" name="txtPhone" value="{{ old('txtPhone') }}" placeholder="Nhập số điện thoại">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Email:(*)</label>
                                                        <input type="text" class="form-control" name="txtEmail" value="{{ old('txtEmail') }}" placeholder="Nhập Email abc12@exam.com">
                                                    </div>
                                                </div>
                                            </div>

                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend class="font-weight-semibold"><i class="icon-paperplane mr-2"></i> Hình ảnh</legend>

                                            <div class="form-group" hidden>
                                                <label>Mật Khẩu:(*)</label>
                                                <input type="password" class="form-control" name="txtPass"  value="<?php echo md5(123456);?>" require>
                                            </div>

                                            <div class="form-group">
                                                <label>CMND:(*)</label>
                                                <input type="text" class="form-control" name="txtIDNumber" placeholder="Nhập số CMND" value="{{ old('txtIDNumber') }}">
                                            </div>

                                            <div class="form-group">
                                                <label>Ngày cấp:(*)</label>
                                                <input type="text" class="form-control daterange-single" name="txtIssue" value="{{ old('txtIssue') }}">
                                            </div>

                                            <div class="form-group">
                                                <label>Hình ảnh:</label>
                                                <input type="file" class="form-input-styled" name="txtPhoto">
                                            </div>

                                            <div class="form-group">
                                                <label>Mặt trước CMND:</label>
                                                <input type="file" class="form-input-styled" name="txtIDPhoto">
                                            </div>

                                            <div class="form-group">
                                                <label>Mặt sau CMND:</label>
                                                <input type="file" class="form-input-styled" name="txtIDPhoto2">
                                            </div>

                                            <div class="form-group">
                                                <label>Ghi chú:</label>
                                                <textarea rows="5" cols="5" class="form-control" name="txtNote" value="{{ old('txtNote') }}" placeholder="Nhập Ghi chú"></textarea>
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
                                            <div class="row">
                                                <div class="col-md-2"hidden>
                                                    <div class="form-group" >
                                                        <label>Cấp Bậc:</label>
                                                        <input type="text" class="form-control" name="education[0][level]" value="1">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Tên Cấp Bậc:</label>
                                                        <select id="txtLevelName" class="form-control" name="education[0][levelName]">
                                                            <option value="Tiểu học">Tiểu học</option>
                                                            <option value="Trung học cơ sở">Trung học cơ sở</option>
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
                                                        <input type="text" class="form-control text-uppercase" id="txtSchool" name="education[0][school]" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Chuyên ngành: (*)</label>
                                                        <input type="text" class="form-control" name="education[0][fieldOfStudy]" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Năm tốt nghiệp:(*)</label>
                                                        <input type="text" class="form-control" name="education[0][graduatedYear]" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Xếp loại:</label>
                                                        <input type="text" class="form-control" name="education[0][grade]" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Hình thức học:</label>
                                                        <input type="text" class="form-control" name="education[0][modeOfStudy]" value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-success" type="submit">Tạo mới <i class="icon-paperplane ml-2"></i></button>
                        <button type="reset" class="btn btn-primary">Nhập lại <i class="icon-paperplane ml-2"></i></button>
                    </div>
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
        let optionIndex = 0;

        function addOption() {
            optionIndex++;
            $('#education').append(`
                    <hr>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Cấp Bậc:</label>
                                <input type="text" class="form-control" name="education[${optionIndex}][level]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tên Cấp Bậc:</label>
                                <select id="txtLevelName" class="form-control" name="education[${optionIndex}][levelName]">
                                    <option value="Tiểu học">Tiểu học</option>
                                    <option value="Trung học cơ sở">Trung học cơ sở</option>
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
