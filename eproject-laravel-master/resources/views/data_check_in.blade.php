<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Attendance Check-in List</title>
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
        <caption><h1>Check-in List {{ $date }}</h1></caption>
        <div style="width: 100%; display: flex; height: 250px">
            <div style="width: 50%; float: left">
                <table>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Employee Id: </th>
                        <td>{{ auth()->user()->code }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Fullname: </th>
                        <td>{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Department: </th>
                        <td> {{ $department_of_staff }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Position: </th>
                        <td>{{ auth()->user()->is_manager == 1 ? "Manager" : "Staff" }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Export Date: </th>
                        <td>{{ date("d/m/Y") }}</td>
                    </tr>
                </table>
            </div>
            <div style="width: 50%; float: left">
                <table>
                    <tr>
                        <th style="text-align: left; width:100px; vertical-align: text-top;">Company: </th>
                        <td>HUDECO Join Stock Company</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Address: </th>
                        <td>199 Phạm Huy Thông, Phường 6, Quận Gò Vấp, Thành Phố Hồ Chí Minh.</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; vertical-align: text-top;">Email: </th>
                        <td>info@gmail.com</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; width:110px; vertical-align: text-top;">Phone: </th>
                        <td>0707997989</td>
                    </tr>
                </table>
            </div>
        </div>
 
        <table id="results" class="table table-bordered">
            <thead>
                <tr>
                    <th class="th">Date</th>
                    <th class="th">Check-in</th>
                    <th class="th">Check-out</th>
                    <th class="th">Working</th>
                    <th class="th">Attendances</th>
                    <th class="th">Note</th>
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
                            {{ \Carbon\Carbon::createFromTimestampMs($check_in_out['check_in_day'])->format('Y-m-d') }}
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
                                    echo 'Check-in late: ' . date_format($date,"H") . ' hour';
                                    echo ' ' . date_format($date,"i") . ' minute';
                                    echo ' ' . date_format($date,"s") . ' second';
                                    echo "<br>";
                                }
                                if($check_in_out['out_soon']){
                                    $date = date_create($check_in_out['out_soon']);
                                    echo 'Check-out early: ' . date_format($date,"H") . ' hour';
                                    echo ' ' . date_format($date,"i") . ' minute';
                                    echo ' ' . date_format($date,"s") . ' second';
                                    echo "<br>";
                                }
                                if($check_in_out['ot']){
                                    $date = date_create($check_in_out['ot']);
                                    echo 'Ovetime: ' . date_format($date,"H") . ' hour';
                                    echo ' ' . date_format($date,"i") . ' minute';
                                    echo ' ' . date_format($date,"s") . ' second';
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
                                    $date = Carbon\Carbon::createFromTimestampMs($item['day_time_leave'],'Asia/Ho_Chi_Minh')->format('d-m-Y');
                                    echo $date;
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
                            <td class="td"><?php echo $item['type'] == "0" ? 'Add more attendances' : 'Yearly paid leaves' ?></td>
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
                                            echo "Short-term sick leave";
                                            break;
                                        case 4:
                                            echo "Long-term sick leave";
                                            break;
                                        case 5:
                                            echo "Maternity leave";
                                            break;
                                        case 6:
                                            echo "Marriage leave";
                                            break;
                                        case 7:
                                            echo "Funeral leave";
                                            break;
                                        default:
                                            echo "Unpaid leave";
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
                                Holiday work
                            </td>
                        </tr>
                    @endif
                @endforeach   

                <tr style="background-color: rgb(231, 231, 231)">
                    <td class="td" colspan="3">Summary</td>
                    <td class="td">{{ $summary['total_time'] }}</td>
                    <td class="td">{{ $summary['total_number_time_all'] }}</td>
                    <td class="td">
                        Late check-in: {{ $summary['total_late'] }} <br>
                        Early check-out: {{ $summary['total_soon'] }} <br> 
                        Overtime: {{ $summary['total_ot'] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>