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
<script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatable_init.js') }}"></script>
@endsection

@section('js')
@endsection


@section('content')
<div class="card">
    <h1 class="pt-3 pl-3 pr-3">Payroll Details</h1>
    <div class="card-header header-elements-inline">

    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table dt-responsive nowrap w-75">
                    <thead>
                        <tr>
                            <th>Employee Id</th>
                            <th>Name</th>
                            <th>Regular Daily Rate</th>
                            <th>Paid Leave</th>
                            <th>Based Salary</th>
                            <th>Monthly Salary</th>
                            <th>Overtime Salary</th>
                            <th>Allowance</th>
                            <th>Deductions</th>
                            <th>
                                Gross Salary
                            </th>
                            <th>
                                Taxable Income
                            </th>
                            <th>Personal Income Tax (PIT)</th>
                            <th>
                                Net Salary
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                        <tr>
                            <td>{{ $item->staff->id }}</td>
                            <td>{{ $item->staff->firstname . ' '. $item->staff->lastname }}</td>
                            <td>{{ number_format($item->totalDayWork, 1) }}</td>
                            <td>{{ number_format($item->totalSpecialDay, 1) }}</td>
                            <td>{{ number_format($item->baseSalaryContract) }}</td> <!-- lương cơ bản -->
                            <td>{{ number_format($item->salary) }}</td> <!-- lương cơ bản -->
                            <td>{{ number_format($item->salaryOt) }}</td> <!-- lương tăng ca -->
                            <td>{{ number_format($item->totalAllowance) }}</td>
                            <td>{{ number_format($item->totalInsurance) }}</td>
                            <td>{{ number_format($item->incomeTax) }}</td>
                            <td>{{ number_format($item->taxableIncome) }}</td>
                            <td>{{ number_format($item->personalTax) }}</td>
                            <td>{{ number_format($item->salaryActuallyReceived) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/picker_date_init.js') }}"></script>
@endsection