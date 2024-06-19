<table style="border:1px; border-style: solid;">
    <tr>
        <td colspan="5" style="text-align: center;">
            <b>PHIẾU LƯƠNG THÁNG {{ \Carbon\Carbon::createFromFormat('Y-m-d', $dataSalaryDetail->salaryDetail->fromDate)->format('m/Y') }}</b>
        </td>
    </tr>
    <tr>
        <td>Họ và tên</td>
        <td colspan="2">{{ $dataSalaryDetail->staff->firstname . ' ' . $dataSalaryDetail->staff->lastname}}</td>
        <td>Công chuẩn</td>
        <td>{{ $dataSalaryDetail->salaryDetail->standardDays }}</td>
    </tr>
    <tr>
        <td>Phòng ban</td>
        <td colspan="2">
            @foreach($data_department as $department)
                @if($department['id'] === $dataSalaryDetail->staff->department)
                    {{ $department['name'] }}
                    @break
                @endif
            @endforeach
        </td>
        <td>Chức danh</td>
        <td>
            {{ $dataSalaryDetail->staff->isManager ? 'Trưởng nhóm' : 'Nhân viên' }}
        </td>
    </tr>
    <tr>
        <td>
            <b>Lương tháng</b>
        </td>
        <td></td>
        <td></td>
        <td>
            <b>Thành tiền (1)</b>
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
        <td>+ Ngày công</td>
        <td>{{ $dataSalaryDetail->totalDayWork }}</td>
        <td>ngày</td>
        <td rowspan="2">Thành tiền</td>
        <td rowspan="2">
            {{ number_format($total_paid_leave + $total_paid_normal) }}
        </td>
    </tr>
    <tr>
        <td>+ Nghỉ có lương</td>
        <td>{{ $dataSalaryDetail->totalSpecialDay }}</td>
        <td>ngày</td>
    </tr>
    <tr>
        <td>Làm thêm</td>
        <td></td>
        <td></td>
        <td><b>Thành tiền (2)</b></td>
        <td>{{ number_format($_150 + $_200 + $_300) }}</td>
    </tr>
    <tr>
        <td>+ Thêm giờ (150%)</td>
        <td>-</td>
        <td>giờ</td>
        <td>Thành tiền</td>
        <td>{{ number_format($_150) }}</td>
    </tr>
    <tr>
        <td>+ Thêm giờ (200%)</td>
        <td>-</td>
        <td>giờ</td>
        <td>Thành tiền</td>
        <td>{{ number_format($_200) }}</td>
    </tr>
    <tr>
        <td>+ Thêm giờ (300%)</td>
        <td>-</td>
        <td>giờ</td>
        <td>Thành tiền</td>
        <td>{{ number_format($_300) }}</td>
    </tr>
    <tr>
        <td>Các khoản phụ cấp</td>
        <td></td>
        <td></td>
        <td><b>Thành tiền (3)</b></td>
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
        <td>Các khoản khấu trừ</td>
        <td></td>
        <td></td>
        <td><b>Thành tiền (4)</b></td>
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
        <td>Thuế TNCN</td>
        <td>{{ number_format($dataSalaryDetail->personalTax) }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>Thực lãnh = ((1+2+3)-4)</td>
        <td colspan="2">{{ number_format($dataSalaryDetail->salaryActuallyReceived) }}</td>
        <td>Ký nhận</td>
        <td></td>
    </tr>
</table>
