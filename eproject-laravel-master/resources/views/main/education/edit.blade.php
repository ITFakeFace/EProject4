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
                    <h1 class="pt-3 pl-3 pr-3">Cập Nhật Văn Bằng Chứng Chỉ</h1>
						<div class="header-elements">
							<div class="list-icons">
		                		<a class="list-icons-item" data-action="collapse"></a>
		                		<a class="list-icons-item" data-action="reload"></a>
		                		<a class="list-icons-item" data-action="remove"></a>
		                	</div>
	                	</div>
					</div>
                    @if (\Session::has('success'))
                        <div class="">
                            <div class="alert alert-success">
                                {!! \Session::get('success') !!}
                            </div>
                        </div>
                    @endif

                    @if (session('message'))
                        <div class="">
                            <div class="alert alert-primary">
                                {!! session('message') !!}
                            </div>
                        </div>
                    @endif

                    <!-- validate  -->
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

					<div class="card-body">
						<form action="{{action('EducationController@postEditEducation')}}" method="post" enctype="multipart/form-data">
                        @csrf
							<div class="row">
                            <div class="col-md-6">
									<fieldset>
					                	<legend class="font-weight-semibold"><i class="icon-reading mr-2"></i> Imformation</legend>

										<div class="row">
											<div class="col-md-6" hidden>
                                                <label>ID:</label>
                                                <input type="text" class="form-control" name="txtID" value="{{$data['id']}}" readonly>
											</div>

                                            <div class="col-md-6">
												<div class="form-group">
                                                <label>ID Nhân viên:</label>
                                                <select class="form-control form-control-select2" name="txtStaffID" color="red">
                                                    @foreach($data_staff as $staff)
                                                    @if($data['staffId']==$staff['id'])
                                                       <option value="{{$staff['id']}}">{{ $staff['lastname'] }} {{ $staff['firstname'] }} || {{ $staff['id'] }} </option>
                                                    @endif
                                                    @endforeach
                                                    @foreach($data_staff as $staff)
                                                        <option value="{{$staff['id']}}">{{ $staff['lastname'] }} {{ $staff['firstname'] }} || {{ $staff['id'] }} </option>
                                                    @endforeach
                                                </select>
											    </div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
                                                 <label>Cấp Bậc:</label>
                                                  <input type="text" class="form-control" name="txtLevel" value="{{$data['level']}}">
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
                                                    <label>Tên Cấp Bậc:</label>
                                                    <input type="text" class="form-control" name="txtLevelName" value="{{$data['levelName']}}">
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
                                                    <label>Tên Trường:</label>
                                                    <input type="text" class="form-control" name="txtSchool" value="{{$data['school']}}">
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
                                                    <label>Chuyên ngành:</label>
                                                    <input type="text" class="form-control" name="txtFieldOfStudy" value="{{$data['fieldOfStudy']}}">
					                            </div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
                                                    <label>Năm tốt nghiện:</label>
                                                    <input type="text" class="form-control" name="txtGraduatedYear" value="{{$data['graduatedYear']}}">
												</div>
											</div>
										</div>

                                        <div class="row">
											<div class="col-md-6">
												<div class="form-group">
                                                    <label>Xếp loại:</label>
                                                    <input type="text" class="form-control" name="txtGrade" value="{{$data['grade']}}">
					                            </div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
                                                    <label>Hình thức học:</label>
                                                    <input type="text" class="form-control" name="txtModeOf" value="{{$data['modeOfStudy']}}">
												</div>
											</div>
										</div>

                                       
									</fieldset>
								</div>
							</div>
							<div class="text-left">
                                <a class="btn btn-primary" role="button" href="{{ action('EducationController@index') }}" style="color:white;">Quay lại</a>
								<button type="submit" class="btn btn-success">Cập nhật <i class="icon-paperplane ml-2"></i></button>
							</div>
						</form>
					</div>
				</div>
				<!-- /2 columns form -->
    <!-- /basic datatable -->
@endsection

@section('scripts')
    <script>
        $('#province').on('change', function() {
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
                success: function (data)
                {
                    var obj = $.parseJSON( data);
                    $('#district').empty();
                    for (var i = 0; i < obj.length; i++) {
                        $('#district').append('<option value="'+obj[i]['id']+'">'+obj[i]['name']+'</option>');
                    }
                }
            });
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

    </script>



@endsection