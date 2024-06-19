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
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
@endsection

@section('content')

    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Tạo Tính Lương</h1>
        <div class="card-header header-elements-inline">
            
        </div>
        <div class="card-body">
            <form action="{{ route('postCalculatedSalary') }}" method="post">
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
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ngày bắt đầu:</label>
                            <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                        </span>
                                <input type="text" class="form-control daterange-single" value="{{ now()->format('Y-m-d') }}" name="from_date">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ngày kết thúc:</label>
                            <div class="input-group">
                                        <span class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon-calendar22"></i></span>
                                        </span>
                                <input type="text" class="form-control daterange-single" value="{{ now()->format('Y-m-d') }}" name="to_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#staff" role="tab" aria-controls="staff" aria-selected="true">Nhân viên</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="allowance-tab" data-toggle="tab" href="#allowance" role="tab" aria-controls="allowance" aria-selected="false">Phụ cấp</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="insurance-tab" data-toggle="tab" href="#insurance" role="tab" aria-controls="insurance" aria-selected="false">Bảo hiểm</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tax-tab" data-toggle="tab" href="#tax" role="tab" aria-controls="tax" aria-selected="false">Thuế</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="staff" role="tabpanel" aria-labelledby="staff-tab">
                                <table class="table datatable-basic">
                                    <thead>
                                    <tr>
                                        <th>Mã nhân viên</th>
                                        <th>Tên nhân viên</th>
                                        <th>Ngày vào</th>
                                        <th>Chọn</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($listStaff as $index => $staff)
                                        <tr>
                                            <td>{{ $staff->code }}</td>
                                            <td>{{ $staff->firstname . ' '. $staff->lastname }}</td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $staff->joinedAt)->format('d/m/Y') }}</td>
                                            <td>
                                                <input type="checkbox" name="staffs[{{ $index }}]" value="{{ $staff->id }}" checked>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="allowance" role="tabpanel" aria-labelledby="allowance-tab">
                                <table class="table datatable-basic">
                                    <thead>
                                    <tr>
                                        <th>Tên</th>
                                        <th>Bị tính thuế</th>
                                        <th>Đơn vị tính</th>
                                        <th>Giá trị</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($listSalaryOption as $index => $item)
                                        @if($item->type === 'ALLOWANCE')
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    <input type="checkbox" {{ $item->have_tax ? 'checked' : '' }} disabled>
                                                </td>
                                                <td>
                                                    @if($item->unit == 'NUMBER')
                                                        Đồng
                                                    @elseif($item->unit == 'PERCENT')
                                                        Phần trăm
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="hidden" name="options[{{ $index }}][type]" value="{{ $item->type }}">
                                                    <input type="hidden" name="options[{{ $index }}][key]" value="{{ $item->key }}">
                                                    <input type="hidden" name="options[{{ $index }}][name]" value="{{ $item->name }}">
                                                    <input type="hidden" name="options[{{ $index }}][have_tax]" value="{{ $item->have_tax ? 1 : 0 }}">
                                                    <input type="hidden" name="options[{{ $index }}][unit]" value="{{ $item->unit }}">
                                                    <input type="number" class="form-control" name="options[{{ $index }}][value]" value="{{ $item->value }}">
                                                    <input type="hidden" name="options[{{ $index }}][min_tax]" value="{{ $item->min_tax }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="insurance" role="tabpanel" aria-labelledby="insurance-tab">
                                <table class="table datatable-basic">
                                    <thead>
                                    <tr>
                                        <th>Tên</th>
                                        <th>Đơn vị tính</th>
                                        <th>Giá trị</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($listSalaryOption as $index => $item)
                                        @if($item->type === 'INSURANCE')
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    @if($item->unit == 'NUMBER')
                                                        Đồng
                                                    @elseif($item->unit == 'PERCENT')
                                                        Phần trăm
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="hidden" name="options[{{ $index }}][type]" value="{{ $item->type }}">
                                                    <input type="hidden" name="options[{{ $index }}][key]" value="{{ $item->key }}">
                                                    <input type="hidden" name="options[{{ $index }}][name]" value="{{ $item->name }}">
                                                    <input type="hidden" name="options[{{ $index }}][have_tax]" value="{{ $item->have_tax ? 1 : 0 }}">
                                                    <input type="hidden" name="options[{{ $index }}][unit]" value="{{ $item->unit }}">
                                                    <input type="number" class="form-control" name="options[{{ $index }}][value]" value="{{ $item->value }}">
                                                    <input type="hidden" name="options[{{ $index }}][min_tax]" value="{{ $item->min_tax }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="tax" role="tabpanel" aria-labelledby="tax-tab">
                                <table class="table datatable-basic">
                                    <thead>
                                    <tr>
                                        <th>Tên</th>
                                        <th>Đơn vị tính</th>
                                        <th>Giá trị</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($listSalaryOption as $index => $item)
                                        @if($item->type === 'TAX')
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    @if($item->unit == 'NUMBER')
                                                        Đồng
                                                    @elseif($item->unit == 'PERCENT')
                                                        Phần trăm
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="hidden" name="options[{{ $index }}][type]" value="{{ $item->type }}">
                                                    <input type="hidden" name="options[{{ $index }}][key]" value="{{ $item->key }}">
                                                    <input type="hidden" name="options[{{ $index }}][name]" value="{{ $item->name }}">
                                                    <input type="hidden" name="options[{{ $index }}][have_tax]" value="{{ $item->have_tax ? 1 : 0 }}">
                                                    <input type="hidden" name="options[{{ $index }}][unit]" value="{{ $item->unit }}">
                                                    <input type="hidden" name="options[{{ $index }}][min_tax]" value="{{ $item->min_tax }}">
                                                    <input type="number" class="form-control" name="options[{{ $index }}][value]" value="{{ $item->value }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-success" type="submit">Tính toán</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/picker_date_init.js') }}"></script>
@endsection
