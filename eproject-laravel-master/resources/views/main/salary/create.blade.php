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
    <h1 class="pt-3 pl-3 pr-3">Create Payroll</h1>
    <div class="card-header header-elements-inline">

    </div>
    <div class="card-body">
        <form action="{{ route('postCalculatedSalary') }}" method="post">
            <div class="form-group d-flex w-25 p-3">
                <input class="form-control" type="month" name="month" min="2018-03" value="{{$currentYearMonth}}" />
            </div>
            @if (session('message'))
            <div class="alert alert-{{ session('message')['type'] }} border-0 alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                {{ session('message')['message'] }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger border-0 alert-dismissible">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <p><b>Invalid input data:</b></p>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#staff" role="tab" aria-controls="staff" aria-selected="true">Staff</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="allowance-tab" data-toggle="tab" href="#allowance" role="tab" aria-controls="allowance" aria-selected="false">Allowance</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="insurance-tab" data-toggle="tab" href="#insurance" role="tab" aria-controls="insurance" aria-selected="false">Insurance</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="tax-tab" data-toggle="tab" href="#tax" role="tab" aria-controls="tax" aria-selected="false">Tax</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="staff" role="tabpanel" aria-labelledby="staff-tab">
                            <table class="table datatable-basic">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Join Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listStaff as $index => $staff)
                                    <tr>
                                        <td>{{ $staff->code }}</td>
                                        <td>{{ $staff->firstname . ' ' . $staff->lastname }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::createFromTimestampMs($staff->joinedAt)->format('Y-m-d') }}
                                            {{-- {{ $staff->joinedAt }} --}}
                                        </td>
                                    </tr>
                                    <input type="checkbox" name="staffs[{{ $index }}]" value="{{ $staff->id }}" checked hidden>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="allowance" role="tabpanel" aria-labelledby="allowance-tab">
                            <table class="table datatable-basic">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        {{-- <th>Taxable</th> --}}
                                        <th>Unit</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listSalaryOption as $index => $item)
                                    @if ($item->type === 'ALLOWANCE')
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        {{-- <td>
                                                        <input type="checkbox" {{ $item->have_tax ? 'checked' : '' }} disabled>
                                        </td> --}}
                                        <td>
                                            @if ($item->unit == 'NUMBER')
                                            VNĐ
                                            @elseif($item->unit == 'PERCENT')
                                            Percent
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
                                        <th>Name</th>
                                        <th>Unit</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listSalaryOption as $index => $item)
                                    @if ($item->type === 'INSURANCE')
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            @if ($item->unit == 'NUMBER')
                                            VNĐ
                                            @elseif($item->unit == 'PERCENT')
                                            Percent
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
                                        <th>Name</th>
                                        <th>Unit</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listSalaryOption as $index => $item)
                                    @if ($item->type === 'TAX')
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            @if ($item->unit == 'NUMBER')
                                            VNĐ
                                            @elseif($item->unit == 'PERCENT')
                                            Percent
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
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/picker_date_init.js') }}"></script>
@endsection