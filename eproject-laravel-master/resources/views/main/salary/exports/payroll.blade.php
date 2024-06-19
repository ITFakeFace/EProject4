<table>
    <thead>
    <tr>
        <th rowspan="2">STT</th>
        <th rowspan="2">MÃ SỐ</th>
        <th rowspan="2">HỌ VÀ TÊN</th>
        <th rowspan="2">CMND</th>
        <th rowspan="2">NGÀY VÀO</th>
        <th rowspan="2">PHÒNG BAN</th>
        <th rowspan="2">CHỨC DANH</th>
        <th colspan="4"></th>
        <th colspan="5">LÀM THÊM</th>
        <th colspan="4">PHỤ CẤP</th>
        <th colspan="3">CÁC KHOẢN KHẤU TRỪ</th>
        <th rowspan="2">THỰC LÃNH</th>
    </tr>
    <tr>
        <th>LƯƠNG HỢP ĐỒNG</th>
        <th>NGÀY CÔNG</th>
        <th>NGHỈ CÓ LƯƠNG</th>
        <th>THÀNH TIỀN</th>
        <th>THÊM GIỜ 100%</th>
        <th>THÊM GIỜ 150%</th>
        <th>THÊM GIỜ 200%</th>
        <th>THÊM GIỜ 300%</th>
        <th>TỔNG CỘNG</th>
        <th>CƠM</th>
        <th>ĐIỆN THOẠI</th>
        <th>XĂNG</th>
        <th>TỔNG CỘNG</th>
        <th>BHXH, BHYT, BHTN</th>
        <th>THUẾ TNCC</th>
        <th>TỔNG CỘNG</th>
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
