<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Danh sách chấm công</title>
    <style>
        body {
            font-family: DejaVu Sans
        }
        .table, .td, .th {
            border: 1px solid black;
            text-align: center;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <div class="container-fluid" style="height: 500px">
        <caption><h1>Danh sách chấm công {{ $date }}</h1></caption>
        <div style="width: 100%; display: flex; height: 250px">
            <div style="width: 50%; float: left">
                <table>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Mã nhân viên: </th>
                        <td>{{ auth()->user()->code }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Họ và tên: </th>
                        <td>{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Phòng ban: </th>
                        <td> {{ $department_of_staff }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Chức danh: </th>
                        <td>{{ auth()->user()->is_manager == 1 ? "Quản lý" : "Nhân viên" }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Ngày xuất dữ liệu: </th>
                        <td>{{ date("d/m/Y") }}</td>
                    </tr>
                </table>
            </div>
            <div style="width: 50%; float: left">
                <table>
                    <tr>
                        <th style="text-align: left; width:100px; vertical-align: text-top;">Công ty: </th>
                        <td>CÔNG TY TNHH XUẤT NHẬP KHẨU TÂN THÀNH NAM AGRICULTURE</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Địa chỉ: </th>
                        <td>82/1C Hoàng Bật Đạt, Phường 15, Quận Tân Bình, Thành Phố Hồ Chí Minh.</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Email: </th>
                        <td>Tanthanhnam.agriculture@gmail.com</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; width:110px; vertical-align: text-top;">Điện thoại: </th>
                        <td>02633.797.676</td>
                    </tr>
                </table>
            </div>
        </div>
 
        <table id="results" class="table table-bordered">
            <thead>
                <tr>
                    <th class="th">Ngày</th>
                    <th class="th">Giờ vào</th>
                    <th class="th">Giờ ra</th>
                    <th class="th">Làm việc</th>
                    <th class="th">Công</th>
                    <th class="th">Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($check_in as $check_in_out)
                    <tr style="
                        <?php 
                            if($check_in_out['special_date_id'] !== null) echo "background-color: #ffe7e7";
                            else if($check_in_out['day_of_week'] == 1 or $check_in_out['day_of_week'] == 7)  echo "background-color: #d3ffd4";
                        ?>
                    ">
                        <td class="td" style="width: 100px">
                            {{ $check_in_out['check_in_day'] }}
                            <?php 
                                if($check_in_out['special_date_id'] !== null) {
                                    echo '(Ngày lễ)';
                                }
                            ?>
                        </td>
                        <td class="td">
                            {{ $check_in_out['check_in'] }}
                        </td>
                        <td class="td">
                            {{ $check_in_out['check_out'] }}
                        </td>
                        <td class="td">{{ $check_in_out['time'] }}</td>
                        <td class="td">{{ $check_in_out['number_time'] * $check_in_out['multiply'] }}</td>
                        <td class="td" style="width: 260px">
                            <?php
                                if($check_in_out['in_late']){
                                    $date = date_create($check_in_out['in_late']);
                                    echo 'Đi trễ: ' . date_format($date,"H") . ' giờ';
                                    echo ' ' . date_format($date,"i") . ' phút';
                                    echo ' ' . date_format($date,"s") . ' giây';
                                    echo "<br>";
                                }
                                if($check_in_out['out_soon']){
                                    $date = date_create($check_in_out['out_soon']);
                                    echo 'Về sớm: ' . date_format($date,"H") . ' giờ';
                                    echo ' ' . date_format($date,"i") . ' phút';
                                    echo ' ' . date_format($date,"s") . ' giây';
                                    echo "<br>";
                                }
                                if($check_in_out['ot']){
                                    $date = date_create($check_in_out['ot']);
                                    echo 'Tăng ca: ' . date_format($date,"H") . ' giờ';
                                    echo ' ' . date_format($date,"i") . ' phút';
                                    echo ' ' . date_format($date,"s") . ' giây';
                                    echo "<br>";
                                }
                            ?>
                        </td>
                    </tr>
                @endforeach  

                @foreach ($time_leave as $item)
                    @if($item['is_approved'] == 1 && $item['staff_id'] == auth()->user()->id)
                        <tr style="background-color: #ffffe7">
                            <td class="td">
                                <?php
                                    $date = date_create($item['day_time_leave']);
                                    echo date_format($date,"d-m-Y")
                                ?>
                            </td>
                            <td class="td"></td>
                            <td class="td"></td>
                            <td class="td"></td>
                            <td class="td">
                                <?php 
                                    echo $item['time'] == "08:00:00" ? '1' * $item['multiply'] : '0.5' * $item['multiply']
                                ?>
                            </td>
                            <td class="td"><?php echo $item['type'] == "0" ? 'Bổ sung công' : 'Phép năm tính lương' ?></td>
                        </tr>
                    @endif
                @endforeach  
                
                @foreach ($leave_other_table as $item)
                    @if($item['is_approved'] == 1 && $item['staff_id'] == auth()->user()->id)
                        <tr style="background-color: #ffffe7">
                            <td class="td">
                                <?php
                                    $date = date_create($item['day_leave_other']);
                                    echo date_format($date,"d-m-Y");
                                ?>
                            </td>
                            <td class="td"></td>
                            <td class="td"></td>
                            <td class="td"></td>
                            <td class="td">
                                <?php 
                                    if($item['type_leave'] == 6 or $item['type_leave'] == 7) echo '1';
                                    else echo '0';
                                ?>
                            </td>
                            <td class="td">
                                <?php 
                                    switch ($item['type_leave']) {
                                        case 3:
                                            echo "Phép nghỉ ốm đau ngắn ngày";
                                            break;
                                        case 4:
                                            echo "Phép nghỉ ốm đau dài ngày";
                                            break;
                                        case 5:
                                            echo "Phép thai sản";
                                            break;
                                        case 6:
                                            echo "Phép kết hôn";
                                            break;
                                        case 7:
                                            echo "Phép ma chay";
                                            break;
                                        default:
                                            echo "Phép nghỉ không lương";
                                            break;
                                    }    
                                ?>
                            </td>
                        </tr>
                    @endif
                @endforeach   

                @foreach ($time_special as $item)
                    @if($item['staff_id'] == auth()->user()->id)
                        <tr style="background-color: #ffe7e7">
                            <td class="td">
                                <?php
                                    $date = date_create($item['day_time_special']);
                                    echo date_format($date,"d-m-Y");
                                ?>
                            </td>
                            <td class="td"></td>
                            <td class="td"></td>
                            <td class="td"></td>
                            <td class="td">
                                1
                            </td>
                            <td class="td">
                                Công ngày lễ
                            </td>
                        </tr>
                    @endif
                @endforeach   

                <tr style="background-color: rgb(231, 231, 231)">
                    <td class="td" colspan="3">Tổng kết</td>
                    <td class="td">{{ $summary['total_time'] }}</td>
                    <td class="td">{{ $summary['total_number_time_all'] }}</td>
                    <td class="td">
                        Đi trễ: {{ $summary['total_late'] }} <br>
                        Về sớm: {{ $summary['total_soon'] }} <br> 
                        Tăng ca: {{ $summary['total_ot'] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>