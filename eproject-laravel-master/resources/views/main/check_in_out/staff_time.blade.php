@extends('main._layouts.master')

<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('js')
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/notifications/jgrowl.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('global_assets/js/demo_pages/picker_date.js') }}"></script>

    <script src="{{ asset('global_assets/js/plugins/ui/fullcalendar/core/main.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/ui/fullcalendar/daygrid/main.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Attendance History</h1>
        <div class="card-header header-elements-inline">
            <h4 class="card-title font-weight-bold text-uppercase">
                <?php echo auth()->user()->firstname . ' ' . auth()->user()->lastname; ?>
                - <?php echo $staff[0][2]; ?>
                - <?php echo auth()->user()->is_manager == 1 ? 'Manager' : 'Employee'; ?>
            </h4>
            <div class="header-elements">
                <div class="list-icons">

                </div>
            </div>
        </div>
        <div class="card-body">
            @if (\Session::has('success'))
                <div class="">
                    <div class="alert alert-success">
                        {!! \Session::get('success') !!}
                    </div>
                </div>
            @endif

            @if (\Session::has('error'))
                <div class="">
                    <div class="alert alert-danger">
                        {!! \Session::get('error') !!}
                    </div>
                </div>
            @endif
            <form action="{{ action('CheckInOutController@show') }}" method="GET">
                @csrf
                <div class="form-group d-flex">
                    <div class="">
                        <select class="form-control" name="month" id="month">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" <?php echo $month == $i ? 'selected' : ''; ?>>Month {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="ml-2">
                        <input class="form-control" type="number" value="<?php echo $year; ?>" name="year" id="year">
                    </div>
                    <div class="ml-3">
                        <input class="form-control btn btn-primary" type="submit" value="Search">
                    </div>
                </div>
            </form>
            <div class="export">
                <a href ="{{ action('pdfController@index') }}?y_m={{ $y_m }}" class="btn btn-danger export" id="export-button"> Export PDF </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Total Work Days</th>
                        <th>Normal Work Days</th>
                        <th>Holiday Work Days</th>
                        <th>Off Work Days</th>
                        <th>Supplementary Work</th>
                        <th>Registered Leave</th>
                        <th>Holiday Leave</th>
                        <th>Work Time</th>
                        <th>Overtime</th>
                        <th>Late</th>
                        <th>Early Leave</th>
                        <th>Work Hours</th>
                        <th>Calculated Work Hours</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <tr>
                        <td>{{ $summary['total_go'] }}</td>
                        <td>{{ $summary['total_day_normal'] }}</td>
                        <td>{{ $summary['total_special'] }}</td>
                        <td>{{ $summary['total_day_off'] }}</td>
                        <td>{{ $summary['total_day_add'] }}</td>
                        <td>{{ $summary['total_day_leave'] }}</td>
                        <td>{{ $summary['total_time_special'] }}</td>
                        <td>{{ $summary['total_time'] }}</td>
                        <td>{{ $summary['total_ot'] }}</td>
                        <td>{{ $summary['total_late'] }}</td>
                        <td>{{ $summary['total_soon'] }}</td>
                        <td>{{ $summary['total_number_time'] }}</td>
                        <td>{{ $summary['total_number_time_all'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-center">Check-in Time</th>
                    <th class="text-center">Check-out Time</th>
                    <th>Total Work Time</th>
                    <th>Work Hours</th>
                    <th>Calculated Work Hours</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $check_in_out)
                    <tr style="
                        <?php
                        if ($check_in_out['special_date_id'] !== null) {
                            echo 'background-color: #ffe7e7';
                        } elseif ($check_in_out['day_of_week'] == 1 or $check_in_out['day_of_week'] == 7) {
                            echo 'background-color: #d3ffd4';
                        }
                        ?>
                    ">
                        <td>
                            {{ $check_in_out['check_in_day'] }},
                            <?php
                            if ($check_in_out['day_of_week'] == 1) {
                                echo 'Sunday';
                            } else {
                                echo 'Day ' . $check_in_out['day_of_week'];
                            }
                            ?>
                            <?php
                            if ($check_in_out['special_date_id'] !== null) {
                                echo '(Holiday)';
                            }
                            ?>
                        </td>
                        <td class="text-center" style="max-width: 100px;">
                            {{ $check_in_out['check_in'] }}
                            <img src="../images/check_in/{{ $check_in_out['image_check_in'] }}" width="80px" alt="">
                        </td>
                        <td class="text-center" style="max-width: 100px;">
                            {{ $check_in_out['check_out'] }}
                            <img src="../images/check_in/{{ $check_in_out['image_check_out'] }}" width="80px" alt="">
                        </td>
                        <td>{{ $check_in_out['time'] }}</td>
                        <td>{{ $check_in_out['number_time'] }}</td>
                        <td>{{ $check_in_out['number_time'] * $check_in_out['multiply'] }}</td>
                        <td style="min-width: 220px">
                            <?php
                            if ($check_in_out['in_late']) {
                                $date = date_create($check_in_out['in_late']);
                                echo 'Late: ' . date_format($date, 'H') . ' hours';
                                echo ' ' . date_format($date, 'i') . ' minutes';
                                echo ' ' . date_format($date, 's') . ' seconds';
                                echo '<br>';
                            }
                            if ($check_in_out['out_soon']) {
                                $date = date_create($check_in_out['out_soon']);
                                echo 'Early Leave: ' . date_format($date, 'H') . ' hours';
                                echo ' ' . date_format($date, 'i') . ' minutes';
                                echo ' ' . date_format($date, 's') . ' seconds';
                                echo '<br>';
                            }
                            if ($check_in_out['ot']) {
                                $date = date_create($check_in_out['ot']);
                                echo 'Overtime: ' . date_format($date, 'H') . ' hours';
                                echo ' ' . date_format($date, 'i') . ' minutes';
                                echo ' ' . date_format($date, 's') . ' seconds';
                                echo '<br>';
                            }
                            ?>
                        </td>
                    </tr>
                @endforeach
                @foreach ($time_leave as $item)
                    @if ($item['is_approved'] == 1 && $item['staff_id'] == auth()->user()->id)
                        <tr style="background-color: #ffffe7">
                            <td>
                                <?php
                                // $date = date_create($item['day_time_leave']);
                                $date = \Carbon\Carbon::createFromTimestampMs($item['day_time_leave'])->format('d/m/Y');
                                $dayofweek = date('w', strtotime($item['day_time_leave']));
                                $day = '';
                                
                                switch ($dayofweek) {
                                    case '0':
                                        $day = 'Sunday';
                                        break;
                                    case '1':
                                        $day = 'Monday';
                                        break;
                                    case '2':
                                        $day = 'Tuesday';
                                        break;
                                    case '3':
                                        $day = 'Wednesday';
                                        break;
                                    case '4':
                                        $day = 'Thursday';
                                        break;
                                    case '5':
                                        $day = 'Friday';
                                        break;
                                    case '6':
                                        $day = 'Saturday';
                                        break;
                                    default:
                                        break;
                                }
                                echo $date . ', ' . $day;
                                ?>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <?php
                                echo $item['time'] == '08:00:00' ? '1' : '0.5';
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $item['time'] == '08:00:00' ? '1' * $item['multiply'] : '0.5' * $item['multiply'];
                                ?>
                            </td>
                            <td><?php echo $item['type'] == '0' ? 'Supplementary work' : 'Paid leave'; ?></td>
                        </tr>
                    @endif
                @endforeach

                @foreach ($leave_other_table as $item)
                    @if ($item['is_approved'] == 1 && $item['staff_id'] == auth()->user()->id)
                        <tr style="background-color: #ffffe7">
                            <td>
                                <?php
                                $date = \Carbon\Carbon::createFromTimestampMs($item['day_leave_other'])->format('d/m/Y');
                                $dayofweek = date('w', strtotime($item['day_leave_other']));
                                $day = '';
                                
                                switch ($dayofweek) {
                                    case '0':
                                        $day = 'Sunday';
                                        break;
                                    case '1':
                                        $day = 'Monday';
                                        break;
                                    case '2':
                                        $day = 'Tuesday';
                                        break;
                                    case '3':
                                        $day = 'Wednesday';
                                        break;
                                    case '4':
                                        $day = 'Thursday';
                                        break;
                                    case '5':
                                        $day = 'Friday';
                                        break;
                                    case '6':
                                        $day = 'Saturday';
                                        break;
                                    default:
                                        break;
                                }
                                echo $date . ', ' . $day;
                                ?>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <?php
                                if ($item['type_leave'] == 6 or $item['type_leave'] == 7) {
                                    echo '1';
                                } else {
                                    echo '0';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($item['type_leave'] == 6 or $item['type_leave'] == 7) {
                                    echo '1';
                                } else {
                                    echo '0';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                switch ($item['type_leave']) {
                                    case 3:
                                        echo 'Short-term sick leave';
                                        break;
                                    case 4:
                                        echo 'Long-term sick leave';
                                        break;
                                    case 5:
                                        echo 'Maternity leave';
                                        break;
                                    case 6:
                                        echo 'Wedding leave';
                                        break;
                                    case 7:
                                        echo 'Funeral leave';
                                        break;
                                    default:
                                        echo 'Unpaid leave';
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                    @endif
                @endforeach

                @foreach ($time_special as $item)
                    @if ($item['staff_id'] == auth()->user()->id)
                        <tr style="background-color: #ffe7e7">
                            <td>
                                <?php
                                $date = \Carbon\Carbon::createFromTimestampMs($item['day_time_special'])->format('d/m/Y');
                                echo $date;
                                ?>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                1
                            </td>
                            <td>
                                1
                            </td>
                            <td>
                                Holiday work
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /basic datatable -->

    <!-- Event colors -->
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">View Attendance History</h5>
            {{-- <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div> --}}
        </div>

        <div class="card-body">
            <div class="fullcalendar-event-colors"></div>
        </div>
    </div>
    <!-- /event colors -->
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#register_leave').click(function() {
                var request = new Request('http://localhost:8888/staff/updateDayOfLeave');

                fetch(request, {
                    mode: 'no-cors'
                }).then(function(response) {
                    return response.json();
                }).then(function(j) {
                    console.log(JSON.stringify(j));
                }).catch(function(error) {
                    console.log('Request failed', error)
                });
            });
        });


        var FullCalendarStyling = function() {
            // External events
            var _componentFullCalendarStyling = function() {
                if (typeof FullCalendar == 'undefined') {
                    console.warn('Warning - Fullcalendar files are not loaded.');
                    return;
                }

                var eventColors = <?php echo $calendar; ?>;

                var dt = new Date();
                let date_now = new Date().toISOString().split('T')[0];

                let month = <?php echo $month; ?> + '';
                if (month.length == 1) {
                    month = '0' + month;
                }

                date_now = '';
                date_now += <?php echo $year; ?> + '-' + month + '-01';

                // Define element
                var calendarEventColorsElement = document.querySelector('.fullcalendar-event-colors');

                // Initialize
                if (calendarEventColorsElement) {
                    var calendarEventColorsInit = new FullCalendar.Calendar(calendarEventColorsElement, {
                        plugins: ['dayGrid', 'interaction'],
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,dayGridWeek,dayGridDay'
                        },
                        defaultDate: date_now,
                        editable: true,
                        events: eventColors
                    }).render();
                }

            };

            return {
                init: function() {
                    _componentFullCalendarStyling();
                }
            }
        }();


        // Initialize module
        // ------------------------------

        document.addEventListener('DOMContentLoaded', function() {
            FullCalendarStyling.init();
        });
    </script>
@endsection
