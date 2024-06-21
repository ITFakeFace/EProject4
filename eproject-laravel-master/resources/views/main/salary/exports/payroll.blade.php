<table>
    <thead>
    <tr>
        <th rowspan="2">No.</th>
        <th rowspan="2">EMPLOYEE ID</th>
        <th rowspan="2">FULLNAME</th>
        <th rowspan="2">IDENTITY NUMBER</th>
        <th rowspan="2">JOIN DATE/th>
        <th rowspan="2">DEPARTMENT</th>
        <th rowspan="2">POSITION</th>
        <th colspan="4"></th>
        <th colspan="5">OVERTIME</th>
        <th colspan="4">ALLOWANCES</th>
        <th colspan="3">DEDUCTIONS</th>
        <th rowspan="2">NET SALARY</th>
    </tr>
    <tr>
        <th>BASED SALARY</th>
        <th>ATTENDANCES</th>
        <th>PAID LEAVES</th>
        <th>TOTAL</th>
        <th>OVERTIME 100%</th>
        <th>OVERTIME 150%</th>
        <th>OVERTIME 200%</th>
        <th>OVERTIME 300%</th>
        <th>GRAND TOTAL</th>
        <th>MEAL</th>
        <th>PHONE</th>
        <th>FUEL</th>
        <th>TOTAL</th>
        <th>INSURANCES</th>
        <th>PIT</th>
        <th>GRAND TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $index => $item)
        @php
            $_100 = 0;
            $_150 = 0;
            $_200 = 0;
            $_300 = 0;
            $allowances = json_decode($item->allowanceDetails);
            $eat = 0;
            $phone = 0;
            $oil = 0;
            foreach($allowances as $allowance){
                if($allowance->key == 'EAT'){
                    $eat = $allowance->value;
                }else if($allowance->key == 'OIL'){
                    $oil = $allowance->value;
                }else if($allowance->key == 'PHONE'){
                    $phone = $allowance->value;
                }
            }
        @endphp
        @if($item->details)
            @foreach($item->details as $detail)
                @php
                    $_150 += $detail->salary_of_ot_150;
                    $_200 += $detail->salary_of_ot_200;
                    $_300 += $detail->salary_of_ot_300;
                @endphp
            @endforeach
        @endif
        <tr>
            <td>{{ ++$index }}</td>
            <td>{{ $item->staff->code }}</td>
            <td>{{ $item->staff->firstname . ' ' . $item->staff->lastname }}</td>
            <td>{{ $item->staff->idNumber }}</td>
            <td>
                {{ \Carbon\Carbon::createFromFormat('Y-m-d', $item->staff->joinedAt)->format('d/m/Y') }}
            </td>
            <td>
                @foreach($data_department as $department)
                    @if($department['id'] == $item->staff->department)
                        {{ $department['name'] }}
                        @break
                    @endif
                @endforeach
            </td>
            <td>{{ $item->staff->isManager ? 'Trưởng nhóm' : 'Nhân viên' }}</td>
            <td>{{ number_format($item->baseSalaryContract) }}</td>
            <td>{{ $item->totalDayWork }}</td>
            <td>{{ $item->totalSpecialDay }}</td>
            <td>{{ number_format($item->salary + $item->salaryOt) }}</td>
            <td>{{ number_format($_100) }}</td>
            <td>{{ number_format($_150) }}</td>
            <td>{{ number_format($_200) }}</td>
            <td>{{ number_format($_300) }}</td>
            <td>{{ number_format($_150 + $_200 + $_300) }}</td>
            <td>{{ number_format($eat) }}</td>
            <td>{{ number_format($phone) }}</td>
            <td>{{ number_format($oil) }}</td>
            <td>{{ number_format($eat + $phone + $oil) }}</td>
            <td>{{ number_format($item->totalInsurance) }}</td>
            <td>{{ number_format($item->personalTax) }}</td>
            <td>{{ number_format($item->totalInsurance + $item->personalTax) }}</td>
            <td>{{ number_format($item->salaryActuallyReceived) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
