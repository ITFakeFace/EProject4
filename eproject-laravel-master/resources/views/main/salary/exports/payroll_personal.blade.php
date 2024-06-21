<table style="border:1px; border-style: solid;">
    <tr>
        <td colspan="5" style="text-align: center;">
            <b>Monthly Pay Slip {{ \Carbon\Carbon::createFromFormat('Y-m-d', $dataSalaryDetail->salaryDetail->fromDate)->format('m/Y') }}</b>
        </td>
    </tr>
    <tr>
        <td>Fullname</td>
        <td colspan="2">{{ $dataSalaryDetail->staff->firstname . ' ' . $dataSalaryDetail->staff->lastname}}</td>
        <td>Regular Attendance</td>
        <td>{{ $dataSalaryDetail->salaryDetail->standardDays }}</td>
    </tr>
    <tr>
        <td>Department</td>
        <td colspan="2">
            @foreach($data_department as $department)
                @if($department['id'] === $dataSalaryDetail->staff->department)
                    {{ $department['name'] }}
                    @break
                @endif
            @endforeach
        </td>
        <td>Position</td>
        <td>
            {{ $dataSalaryDetail->staff->isManager ? 'Manager' : 'Staff' }}
        </td>
    </tr>
    <tr>
        <td>
            <b>Monthly Salary</b>
        </td>
        <td></td>
        <td></td>
        <td>
            <b>Total(1)</b>
        </td>
        <td>{{ number_format($dataSalaryDetail->salary) }}</td>
    </tr>

    @if($total_paid_leave = 0) @endif
    @if($total_paid_normal = 0) @endif
    @if($_150 = 0) @endif
    @if($_200 = 0) @endif
    @if($_300 = 0) @endif
    @if($total_time_150 = 0) @endif
    @if($total_time_200 = 0) @endif
    @if($total_time_300 = 0) @endif
    @foreach($dataSalaryDetail->details as $detail)
        @if($detail->paid_leave)
            @if($total_paid_leave += $detail->salary_per_day) @endif
        @else
            @if($detail->multiply_day == 1)
                @if($total_paid_normal += $detail->total_salary) @endif
            @endif
        @endif

        @if($detail->salary_of_ot_150 > 0)
            @if($total_time_150 += $detail->ot_hours) @endif
        @elseif($detail->salary_of_ot_200 > 0)
            @if($total_time_200 += $detail->ot_hours) @endif
        @elseif($detail->salary_of_ot_300 > 0)
            @if($total_time_300 += $detail->ot_hours) @endif
        @endif

        @if($_150 += $detail->salary_of_ot_150) @endif
        @if($_200 += $detail->salary_of_ot_200) @endif
        @if($_300 += $detail->salary_of_ot_300) @endif
    @endforeach

    @foreach(json_decode($dataSalaryDetail->allowanceDetails) as $allowance)
    @endforeach
    <tr>
        <td>+ Daily Attendance</td>
        <td>{{ $dataSalaryDetail->totalDayWork }}</td>
        <td>Date</td>
        <td rowspan="2">Total</td>
        <td rowspan="2">
            {{ number_format($total_paid_leave + $total_paid_normal) }}
        </td>
    </tr>
    <tr>
        <td>+ Paid Leaves</td>
        <td>{{ $dataSalaryDetail->totalSpecialDay }}</td>
        <td>Date</td>
    </tr>
    <tr>
        <td>Overtime</td>
        <td></td>
        <td></td>
        <td><b>Total (2)</b></td>
        <td>{{ number_format($_150 + $_200 + $_300) }}</td>
    </tr>
    <tr>
        <td>+ Overtime (150%)</td>
        <td>-</td>
        <td>Hours</td>
        <td>Total</td>
        <td>{{ number_format($_150) }}</td>
    </tr>
    <tr>
        <td>+ Overtime (200%)</td>
        <td>-</td>
        <td>Hours</td>
        <td>Total</td>
        <td>{{ number_format($_200) }}</td>
    </tr>
    <tr>
        <td>+ Overtime (300%)</td>
        <td>-</td>
        <td>Hours</td>
        <td>Total</td>
        <td>{{ number_format($_300) }}</td>
    </tr>
    <tr>
        <td>Allowances/td>
        <td></td>
        <td></td>
        <td><b>Total (3)</b></td>
        <td>{{ number_format($dataSalaryDetail->totalAllowance) }}</td>
    </tr>
    @foreach(json_decode($dataSalaryDetail->allowanceDetails) as $allowance)
        <tr>
            <td>+ {{ $allowance->name }}</td>
            <td>{{ number_format($allowance->value) }}</td>
            <td>tháng</td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
    <tr>
        <td>Deductions</td>
        <td></td>
        <td></td>
        <td><b>Total (4)</b></td>
        <td>{{ number_format($dataSalaryDetail->totalInsurance) }}</td>
    </tr>
    @foreach(json_decode($dataSalaryDetail->insuranceDetails) as $insurance)
    <tr>
        <td>+ {{ $insurance->name }}</td>
        <td>{{ number_format($insurance->value * $dataSalaryDetail->baseSalaryContract) }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @endforeach
    <tr>
        <td>Personal Income Tax(PIT)</td>
        <td>{{ number_format($dataSalaryDetail->personalTax) }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>Net Salary = ((1+2+3)-4)</td>
        <td colspan="2">{{ number_format($dataSalaryDetail->salaryActuallyReceived) }}</td>
        <td>Signature</td>
        <td></td>
    </tr>
</table>
