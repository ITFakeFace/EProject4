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

    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Danh sách hợp đồng</h1>
        <div class="card-header header-elements-inline">
           
        </div>
        <div class="card-body">
            <form action="{{ route('postSaveContract') }}" method="post">
                @if(session('message'))
                    <div class="alert alert-{{ session('message')['type'] }} border-0 alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        {{ session('message')['message'] }}
                    </div>
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ $contract->id }}">
                <input type="hidden" name="createAt" value="{{ $contract->createAt }}">
                <input type="hidden" name="del" value="{{ $contract->del ? 'true' : 'false' }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Mã nhân viên</label>
                            <select class="form-control select-search" name="staffId">
                                @foreach($listStaff as $staff)
                                    <option value="{{ $staff->id }}" @if($contract->staffId == $staff->id) selected @endif>{{ $staff->firstname .' '. $staff->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ngày bắt đầu hợp đồng:</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                        </span>
                                        <input type="text" class="form-control daterange-single" value="{{ $contract->startDate }}" name="startDate">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ngày kết thúc hợp đồng:</label>
                                    <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                        </span>
                                        <input type="text" class="form-control daterange-single" value="{{ $contract->endDate }}" name="endDate">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Lương:</label>
                            <input type="number" class="form-control" name="salary" value="{{ $contract->salary }}">
                        </div>
                        <button class="btn btn-success" type="submit">Lưu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/picker_date_init.js') }}"></script>
@endsection
