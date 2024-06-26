@extends('main._layouts.master')

@section('css')
@endsection

@section('js')
    <script src="{{ asset('global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/anytime.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2_init.js') }}"></script>
@endsection

@section('content')
    <form action="{{ route('postSaveContract') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <h1 class="pt-3 pl-3 pr-3">Create New Contract</h1>
                    <div class="card-header header-elements-inline">
                    </div>
                    <div class="card-body">
                        @if(session('message'))
                            <div class="alert alert-{{ session('message')['type'] }} border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                {{ session('message')['message'] }}
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger border-0 alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                <p><b>Incorrect input data:</b></p>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>Employee ID</label>
                            <select class="form-control select-search" name="staffId">
                                @foreach($listStaff as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->firstname .' '. $staff->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contract Start Date:</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                        </span>
                                        <input type="text" class="form-control daterange-single" <?php $today = date('Y-m-d')  ?> value="<?php echo $today; ?>" name="startDate">
                                    </div>
                                </div>
                            </div>
                               
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contract End Date:</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                        </span>
                                        <input type="text" class="form-control daterange-single" <?php $today = date('Y-m-d')  ?> value="<?php echo $today; ?>" name="endDate">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Base Salary:</label>
                                    <input type="number" class="form-control" name="baseSalary" value="0">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-success" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/picker_date_init.js') }}"></script>
@endsection
