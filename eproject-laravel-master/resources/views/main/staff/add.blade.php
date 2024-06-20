@extends('main._layouts.master')

<?php
header('Access-Control-Allow-Origin: *');
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
    <script src="{{ asset('global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/demo_pages/form_layouts.js') }}"></script>
@endsection

@section('content')
    <!-- Basic datatable -->
    <!-- 2 columns form -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Add New Employee</h1>
        <div class="card-header header-elements-inline">

        </div>
        <div class="card-body">
            <form action="{{ route('postAddStaff') }}" method="post" enctype="multipart/form-data">
                @csrf
                @if (session('message'))
                    <div class="alert alert-{{ session('message')['type'] }} border-0 alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        {{ session('message')['message'] }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <p><b>Input data is not correct:</b></p>
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
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#staff" role="tab" aria-controls="staff" aria-selected="true">Employee</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="allowance-tab" data-toggle="tab" href="#allowance" role="tab" aria-controls="allowance" aria-selected="false">Education</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="staff" role="tabpanel" aria-labelledby="staff-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend class="font-weight-semibold"><i class="icon-reading mr-2"></i> Information</legend>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Employee Code:(*)</label>
                                                        <input type="text" class="form-control" name="txtCode" value="{{ old('txtCode') }}" required placeholder="Enter Employee Code: TTN">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Role:(*)</label>
                                                        <select class="form-control" name="txtisManager" color="red">
                                                            <option value="0" selected>Employee</option>
                                                            <option value="1">Manager</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Last Name:</label>
                                                        <input type="text" class="form-control" name="txtLname" value="{{ old('txtLname') }}" placeholder="Enter Last Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>First Name:(*)</label>
                                                        <input type="text" class="form-control" name="txtFname" value="{{ old('txtFname') }}" required placeholder="Enter First Name">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Department:(*)</label>
                                                        <select class="form-control" name="txtDepartment" value="{{ old('txtDepartment') }}">
                                                            @foreach ($data_department as $dep)
                                                                <option value="{{ $dep['id'] }}">{{ $dep['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Date of Birth:</label>
                                                        <input type="text" class="form-control daterange-single" name="txtDob" value="{{ old('txtDob') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Joining Date:(*)</label>
                                                        <input type="text" class="form-control daterange-single" name="txtJoinat" value="{{ old('txtJoinat') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Gender:(*)</label>
                                                        <select class="form-control" name="txtGender" color="red">
                                                            <option value="1" selected>Male</option>
                                                            <option value="0">Female</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Region:(*)</label>
                                                        <select id="province" class="form-control form-control-select2" color="red" data-fouc>
                                                            @foreach ($data_reg as $reg)
                                                                <option value="{{ $reg['id'] }}">{{ $reg['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>City/District/Commune:(*)</label>
                                                        <select id="district" class="form-control form-control-select2" name="txtRegional" color="red" data-fouc>
                                                            @foreach ($data_district as $district)
                                                                <option value="{{ $district['id'] }}">{{ $district['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Phone:</label>
                                                        <input type="number" class="form-control" name="txtPhone" value="{{ old('txtPhone') }}" placeholder="Enter phone number">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Email:(*)</label>
                                                        <input type="text" class="form-control" name="txtEmail" value="{{ old('txtEmail') }}" placeholder="Enter Email abc12@exam.com">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend class="font-weight-semibold"><i class="icon-paperplane mr-2"></i> Images</legend>

                                            <div class="form-group" hidden>
                                                <label>Password:(*)</label>
                                                <input type="password" class="form-control" name="txtPass" value="<?php echo md5(123456); ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label>ID Number:(*)</label>
                                                <input type="text" class="form-control" name="txtIDNumber" placeholder="Enter ID Number" value="{{ old('txtIDNumber') }}">
                                            </div>

                                            <div class="form-group">
                                                <label>Issue Date:(*)</label>
                                                <input type="text" class="form-control daterange-single" name="txtIssue" value="{{ old('txtIssue') }}">
                                            </div>

                                            <div class="form-group">
                                                <label>Image:</label>
                                                <input type="file" class="form-input-styled" name="txtPhoto">
                                            </div>

                                            <div class="form-group">
                                                <label>ID Front:</label>
                                                <input type="file" class="form-input-styled" name="txtIDPhoto">
                                            </div>

                                            <div class="form-group">
                                                <label>ID Back:</label>
                                                <input type="file" class="form-input-styled" name="txtIDPhoto2">
                                            </div>

                                            <div class="form-group">
                                                <label>Notes:</label>
                                                <textarea rows="5" cols="5" class="form-control" name="txtNote" value="{{ old('txtNote') }}" placeholder="Enter Notes"></textarea>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            {{-- TAB 2 --}}
                            <div class="tab-pane fade" id="allowance" role="tabpanel" aria-labelledby="allowance-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-success" onclick="addOption()"><i title="Add Details" class="icon-stack-plus "></i> Add Degree</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <legend class="font-weight-semibold"><i class="icon-reading mr-2"></i> Information</legend>
                                        <div id="education">
                                            <div class="row">
                                                <div class="col-md-2" hidden>
                                                    <div class="form-group">
                                                        <label>Level:</label>
                                                        <input type="text" class="form-control" name="education[0][level]" value="1">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Level Name:</label>
                                                        <select id="txtLevelName" class="form-control" name="education[0][levelName]">
                                                            <option value="Elementary">Elementary</option>
                                                            <option value="Secondary">Secondary</option>
                                                            <option value="High School">High School</option>
                                                            <option value="University">University</option>
                                                            <option value="Master">Master</option>
                                                            <option value="Doctor">Doctor</option>
                                                            <option value="Associate Professor">Associate Professor</option>
                                                            <option value="Professor">Professor</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>School Name: (*)</label>
                                                        <input type="text" class="form-control text-uppercase" id="txtSchool" name="education[0][school]" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Field of Study: (*)</label>
                                                        <input type="text" class="form-control" name="education[0][fieldOfStudy]" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Year of Graduation:(*)</label>
                                                        <input type="text" class="form-control" name="education[0][graduatedYear]" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Grade:</label>
                                                        <input type="text" class="form-control" name="education[0][grade]" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Mode of Study:</label>
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
                        <button class="btn btn-success" type="submit">Create New <i class="icon-paperplane ml-2"></i></button>
                        <button type="reset" class="btn btn-primary">Reset <i class="icon-paperplane ml-2"></i></button>
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
                                <label>Level:</label>
                                <input type="text" class="form-control" name="education[${optionIndex}][level]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Level Name:</label>
                                <select id="txtLevelName" class="form-control" name="education[${optionIndex}][levelName]">
                                    <option value="Elementary">Elementary</option>
                                    <option value="Secondary">Secondary</option>
                                    <option value="High School">High School</option>
                                    <option value="University">University</option>
                                    <option value="Master">Master</option>
                                    <option value="Doctor">Doctor</option>
                                    <option value="Associate Professor">Associate Professor</option>
                                    <option value="Professor">Professor</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>School Name: (*)</label>
                                <input type="text" class="form-control text-uppercase" id="txtSchool" name="education[${optionIndex}][school]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Field of Study: (*)</label>
                                <input type="text" class="form-control" name="education[${optionIndex}][fieldOfStudy]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Year of Graduation:(*)</label>
                                <input type="text" class="form-control" name="education[${optionIndex}][graduatedYear]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Grade:</label>
                                <input type="text" class="form-control" name="education[${optionIndex}][grade]">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Mode of Study:</label>
                                <input type="text" class="form-control" name="education[${optionIndex}][modeOfStudy]">
                            </div>
                        </div>
                    </div>
            `);
        }

        $('#province').on('change', function() {
            var parent = this.value;

            $.ajax({
                url: '{{ action('StaffController@loadRegional') }}',
                type: 'GET',
                datatype: 'text',
                data: {
                    parent: parent,
                },
                cache: false,
                success: function(data) {
                    var obj = $.parseJSON(data);
                    $('#district').empty();
                    for (var i = 0; i < obj.length; i++) {
                        $('#district').append('<option value="' + obj[i]['id'] + '">' + obj[i]['name'] + '</option>');
                    }
                }
            });
        });

        $('.open-detail-time-leave').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('TimeleaveController@detailTime') }}',
                type: 'POST',
                datatype: 'text',
                data: {
                    id: id,
                },
                cache: false,
                success: function(data) {
                    console.log(data);
                    $('#html_pending').empty().append(data);
                    $('#bsc-modal').modal();
                }
            });
        });
    </script>
@endsection
