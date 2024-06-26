@extends('main._layouts.master')

<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        #tb_dkp_wrapper {
            display: none;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/notifications/jgrowl.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('global_assets/js/demo_pages/picker_date.js') }}"></script>

    <!-- Theme JS files -->
    <script src="{{ asset('global_assets/js/plugins/ui/fullcalendar/core/main.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/ui/fullcalendar/daygrid/main.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>

    <!-- /theme JS files -->
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">List of Special Dates</h1>
        <div class="card-header header-elements-inline">

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
            <form action="{{ action('SpecialDateController@index') }}" method="GET">
                @csrf
                <div class="form-group d-flex">
                    <div class="">
                        <input class="form-control" type="number" value="<?php echo $year; ?>" name="year" id="year">
                    </div>
                    <div class="ml-3">
                        <input class="form-control btn btn-primary" type="submit" value="Search">
                    </div>
                </div>
            </form>

            <div class="form-group d-flex">
                <div class="export">
                    <a href ="{{ action('ExportController@exportSpecialDate') }}?y={{ $year }}" class="btn btn-success export" id="export-button"> Export to Excel </a>
                </div>
                <div class=" ml-1">
                    <button class="btn btn-danger" data-toggle="modal" data-target="#exampleModalCenter">Create New Special Date</button>
                </div>
            </div>
        </div>
        <!-- Modal bsc -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ action('SpecialDateController@createSpecialDate') }}" method="post">
                        @csrf
                        <input type="hidden" name="type_day" value="1">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Create Special Date</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">From Date:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control day_leave" name="day_special_from" value="" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">To Date:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control day_leave" name="day_special_to" value="" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Description:</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" name="note" id="note" cols="20" rows="10" placeholder="E.g., National Day, New Year, ..." required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th class="text-center">Edit / Delete</th>
                    <th style="max-width: 100px">Add Holiday Workdays for All Employees</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                @foreach ($data as $special_date)
                    @if ($special_date['type_day'] == 1)
                        <tr>
                            <td><?php echo $count;
                            $count++; ?></td>
                            <td>
                                {{-- <?php echo $special_date['day_special_from']; ?> --}}
                                {{ \Carbon\Carbon::createFromTimestampMs($special_date['day_special_from'])->format('Y-m-d') }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::createFromTimestampMs($special_date['day_special_to'])->format('Y-m-d') }}
                                {{-- <?php echo $special_date['day_special_to']; ?> --}}
                            </td>
                            <td>
                                <?php
                                if (strlen($special_date['note']) > 40) {
                                    echo substr($special_date['note'], 0, 40) . '...';
                                } else {
                                    echo $special_date['note'];
                                }
                                ?>
                            </td>
                            <td>
                                <span class="badge badge-danger">Special Date</span>
                            </td>
                            <td class="text-center pt-4">
                                @if (now()->format('Y-m-d') < Carbon\Carbon::createFromTimestampMs($special_date['day_special_from'])->format('Y-m-d'))
                                    <div class="form-group">
                                        <a class="btn btn-sm btn-info open-detail-special-date" id="{{ $special_date['id'] }}" style="color: white; cursor: pointer;">Edit</a>
                                        <a href="{{ action('SpecialDateController@deleteSpecialDate', ['id' => $special_date['id']]) }}" class="btn btn-sm btn-danger ml-2" style="color: white; cursor: pointer;">Delete</a>
                                    </div>
                                @else
                                    <span class="badge badge-primary">Date has passed!</span>
                                @endif
                            </td>
                            
                            <td>
                                <?php
                                $date_check = date('Y-m-d', strtotime('+2 days', strtotime($special_date['day_special_to'])));
                                ?>
                                @if (date('Y-m-d') > $date_check)
                                    @if ($special_date['detail_id'])
                                        <a href="{{ action('TimeSpecialController@details') }}?id_special_date={{ $special_date['id'] }}" class="btn btm-sm btn-primary ml-2" style="color: white; cursor: pointer;">Details</a>
                                    @else
                                        <a href="{{ action('TimeSpecialController@create') }}?id={{ $special_date['id'] }}" class="btn btn-sm btn-warning ml-2" style="color: white; cursor: pointer;">Add</a>
                                    @endif
                                @else
                                    <span class="">Only allowed to add after 3 days of the end of the holiday!</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div id="bsc-modal" class="modal fade" role="dialog"> <!-- modal bsc -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ action('SpecialDateController@updateSpecialDate') }}" method="post" class="form-horizontal">
                        @csrf
                        <div id="html_pending">

                        </div>
                    </form> <!-- end form -->
                </div>
            </div>
        </div> <!-- end modal bsc -->

    </div>
    <!-- /basic datatable -->

    <!-- Basic view -->
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"></h5>
            <div class="header-elements">

            </div>
        </div>

        <div class="card-body">

            <div class="fullcalendar-basic"></div>
        </div>
    </div>
    <!-- /basic view -->
@endsection

@section('scripts')
    <script>
        $('.day_leave').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('.open-detail-special-date').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('SpecialDateController@detailSpecialDate') }}',
                Type: 'POST',
                datatype: 'text',
                data: {
                    id: id,
                },
                cache: false,
                success: function(data) {
                    console.log(data);
                    $('#html_pending').empty().append(data);
                    $('#bsc-modal').modal();
                }
            });
        });

        var FullCalendarBasic = function() {

            // Basic calendar
            var _componentFullCalendarBasic = function() {
                if (typeof FullCalendar == 'undefined') {
                    console.warn('Warning - Fullcalendar files are not loaded.');
                    return;
                }

                events = <?php echo $calendar; ?>;

                var dt = new Date();
                let now = new Date().toISOString().split('T')[0];

                now = now.slice(4);
                date_now = '';
                date_now += <?php echo $year; ?> + now;

                // Define element
                var calendarBasicViewElement = document.querySelector('.fullcalendar-basic');

                // Initialize
                if (calendarBasicViewElement) {
                    var calendarBasicViewInit = new FullCalendar.Calendar(calendarBasicViewElement, {
                        plugins: ['dayGrid', 'interaction'],
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,dayGridWeek,dayGridDay'
                        },
                        defaultDate: date_now,
                        editable: true,
                        events: events,
                        eventLimit: true
                    }).render();
                }
            };

            return {
                init: function() {
                    _componentFullCalendarBasic();
                }
            }
        }();

        document.addEventListener('DOMContentLoaded', function() {
            FullCalendarBasic.init();
        });
    </script>
@endsection
