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
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/demo_pages/form_layouts.js') }}"></script>
@endsection

@section('content')
    <!-- Basic datatable -->
    <!-- 2 columns form -->
    <div class="card">
        <div class="card-header header-elements-inline">
            <h1 class="pt-3 pl-3 pr-3">Employee Detailed Information</h1>
            <a href="{{ action('StaffController@getEditStaff') }}?id={{ $data['id'] }}" role="button" class="btn btn-primary">Update Information</a>
            {{-- <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div> --}}
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

        <div class="card-body">
            <form action="#" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <fieldset>
                            <legend class="font-weight-semibold"><i class="icon-reading mr-2"></i> Personal</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Employee ID:</label>
                                        <b><label>{{ $data['id'] }}</label></b>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Employee Code:</label>
                                        <b><label>{{ $data['code'] }}</label></b>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Full Name:</label>
                                        <b><label>{{ $data['lastname'] }} {{ $data['firstname'] }}</label></b>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Identity Number:</label>
                                        <b><label>{{ $data['idNumber'] }}</label></b>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Role:</label>
                                        <b>
                                            @if ($data['isManager'] == 1)
                                                Manager
                                            @else
                                                Employee
                                            @endif
                                        </b>
                                    </div>
                                </div>
                                <div class="col-md-6" hidden>
                                    <div class="form-group">
                                        <label>Department:</label>
                                        <b>
                                            @foreach ($data_department as $dep)
                                                @if ($data['department'] == $dep['id'])
                                                    <td>{{ $dep['name'] }}</td>
                                                @endif
                                            @endforeach
                                        </b>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender:</label>
                                        <b>
                                            @if ($data['isManager'] == 1)
                                                Male
                                            @else
                                                Female
                                            @endif
                                        </b>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date of Birth:</label>
                                        <b><label>{{ \Carbon\Carbon::createFromTimestampMs($data['dob'])->format('d/m/Y') }}</label></b>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Joining Date:</label>
                                        <b><label>{{ \Carbon\Carbon::createFromTimestampMs($data['joinedAt'])->format('d/m/Y') }}</label></b>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Province:</label>
                                        <label id="province" class="form-group">
                                            @foreach ($data_reg as $reg)
                                                @if ($reg['id'] == $district_selected['parent'])
                                                    <label value="{{ $reg['id'] }}" <?php echo $reg['id'] == $district_selected['parent'] ? 'selected' : ''; ?>><b>{{ $reg['name'] }}</b></label>
                                                @endif
                                            @endforeach
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>District/Town:</label>
                                        <b><label id="district" class="form-group" name="txtRegional" color="red" data-fouc>
                                                @foreach ($data_district as $district)
                                                    @if ($district['id'] == $district_selected['id'])
                                                        <label value="{{ $district['id'] }}" <?php echo $district['id'] == $district_selected['id'] ? 'selected' : ''; ?>><b>{{ $district['name'] }}</b></label>
                                                    @endif
                                                @endforeach
                                            </label></b>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone:</label>
                                        <b><label>{{ $data['phoneNumber'] }}</label></b>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email:</label>
                                        <b><label>{{ $data['email'] }}</label></b>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Department:</label>
                                        <b>
                                            @foreach ($data_department as $dep)
                                                @if ($data['department'] == $dep['id'])
                                                    <td>{{ $dep['name'] }}</td>
                                                @endif
                                            @endforeach
                                        </b>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Created At:</label>
                                        <b><label>{{ \Carbon\Carbon::createFromTimestampMs($data['createdAt'])->format('d/m/Y') }}</label></b>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-md-6">
                        <fieldset>
                            <legend class="font-weight-semibold"><i class="icon-reading mr-2"></i> Images</legend>
                            <div class="form-group">
                                <label>Photo:</label>
                                <p><img width="100px" height="120px" src="{{ asset($data['photo']) }}"></p>
                            </div>
                            <div class="form-group">
                                <label>ID Front:</label>
                                <p><img width="200px" height="135px" src="{{ asset($data['idPhoto']) }}"></p>
                            </div>
                            <div class="form-group">
                                <label>ID Back:</label>
                                <p><img width="200px" height="135px" src="{{ asset($data['idPhotoBack']) }}"></p>
                            </div>
                            <div class="form-group">
                                <label>Note:</label>
                                <b><label>{{ $data['note'] }}</label></b>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-12">
                        <legend class="font-weight-semibold"><i class="icon-reading mr-2"></i> Education</legend>
                        <div class="card-body p-0 mt-3 mb-3 ml-4 mr-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Level</th>
                                            <th>Level Name</th>
                                            <th>School</th>
                                            <th>Field of Study</th>
                                            <th>Graduation Year</th>
                                            <th>Grade</th>
                                            <th>Mode of Study</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 1; ?>
                                        @foreach ($educa as $de)
                                            <tr>
                                                @if ($data['id'] == $de['staffId'])
                                                    <td><?php echo $count; ?></td>
                                                    <td>{{ $de['level'] }}</td>
                                                    <td>{{ $de['levelName'] }}</td>
                                                    <td>{{ $de['school'] }}</td>
                                                    <td>{{ $de['fieldOfStudy'] }}</td>
                                                    <td>{{ $de['graduatedYear'] }}</td>
                                                    <td>{{ $de['grade'] }}</td>
                                                    <td>{{ $de['modeOfStudy'] }}</td>
                                                @endif
                                            </tr>
                                            <?php $count++; ?>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
                data: {
                    parent: parent
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
                Type: 'POST',
                datatype: 'text',
                data: {
                    id: id
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
