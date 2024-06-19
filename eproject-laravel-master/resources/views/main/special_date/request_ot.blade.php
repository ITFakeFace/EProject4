@extends('main._layouts.master')

<?php
    header("Access-Control-Allow-Origin: *");
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

    <script src="{{ asset('global_assets/js/plugins/extensions/jquery_ui/interactions.min.js') }}"></script>
	<script src="{{ asset('global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
	<script src="{{ asset('global_assets/js/demo_pages/form_select2.js') }}"></script>

    <!-- Theme JS files -->
	<script src="{{ asset('global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
	<script src="{{ asset('global_assets/js/plugins/notifications/pnotify.min.js') }}"></script>
	<script src="{{ asset('global_assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>

	<script src="{{ asset('global_assets/js/demo_pages/form_multiselect.js') }}"></script>
	<!-- /theme JS files -->

	<!-- /theme JS files -->
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Danh Sách Đề Xuất Tăng Ca</h1>
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
            <form action="{{ action('SpecialDateController@requestOverTime') }}" method="GET">
                @csrf
                <div class="form-group d-flex">
                    <div class="">
                        <input class="form-control" type="number" value="<?php echo $year ?>" name="year" id="year">
                    </div>
                    <div class="ml-3">
                        <input class="form-control btn btn-primary" type="submit" value="Tìm kiếm">
                    </div>
                </div>
            </form>

            <div class="form-group d-flex">
                @if(auth()->user()->id !== 7)
                    <div class="">
                        <button class="btn btn-primary" style="background-color: #046A38" data-toggle="modal" data-target="#exampleModalCenter2">Tạo đề xuất tăng ca mới</button>
                    </div>
                @endif
            </div>
        </div>

         <!-- Modal bsc -->
         <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ action('SpecialDateController@createSpecialDate') }}" method="post">
                        @csrf
                        <input type="hidden" name="staff_request" value="{{ auth()->user()->id }}">
                        <input type="hidden" name="department_request" value="{{ auth()->user()->department }}">
                        <input type="hidden" name="type_day" value="2">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Đề Xuất Ngày Tăng Ca</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Tên quản lý phòng ban:</label>
                                <div class="col-lg-8">
                                    <div class="col-form-label">{{ auth()->user()->firstname . ' ' . auth()->user()->lastname }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Phòng ban đề xuất:</label>
                                <div class="col-lg-8">
                                    <div class="col-form-label">{{ $staff[0][2] }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Chọn nhân viên tăng ca: </label>
                                <div class="col-lg-8">
                                    <select name="staff_ot[]" class="form-control multiselect-full-featured" multiple="multiple" data-fouc>
                                        @foreach ($data_staff as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['firstname'] }} {{ $item['lastname'] }} || {{ $item['code'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Từ ngày:</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control day_leave" name="day_special_from" value="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Đến ngày:</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control day_leave" name="day_special_to" value="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Lý do tăng ca:</label>
                                <div class="col-lg-8">
                                    <textarea class="form-control" name="note" id="note" cols="20" rows="10" placeholder="VD: Tăng ca sản xuất sản phẩm mới, ..." required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Tạo đề xuất</button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>

        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Họ tên quản lý phòng ban</th>
                    <th>Phòng ban đề xuất</th>
                    <th>Từ Ngày</th>
                    <th>Đến Ngày</th>
                    <th>Trạng thái</th>
                    <th>Sửa / Xóa</th>
                    @if(auth()->user()->id != 7)
                        <th>Chi tiết</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                @foreach ($data as $special_date)
                    @if($special_date['type_day'] == 2)
                        <tr>
                            <td><?php echo $count; $count++ ?></td>
                            <td><?php echo $special_date['full_name_staff_request'] ?></td>
                            <td><?php echo $special_date['name_department_request'] ?></td>
                            <td><?php echo $special_date['day_special_from'] ?></td>
                            <td><?php echo $special_date['day_special_to'] ?></td>
                            <td>
                                @if($special_date['is_approved'] == 0)
                                    <span class="badge badge-warning">Chưa phê duyệt</span>
                                @elseif($special_date['is_approved'] == -1)
                                    <span class="badge badge-danger">Đã từ chối</span>
                                @else
                                    <span class="badge badge-primary" style="background-color: #046A38">Đã duyệt</span>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->id != 7)
                                    @if($special_date['is_approved'] == 1)
                                        <span class="badge badge-primary">Đã duyệt. Không thể chỉnh sửa!</span>
                                    @elseif($special_date['is_approved'] == -1)
                                        <span class="badge badge-danger">Đã từ chối!</span>
                                    @elseif(date("Y-m-d") <= $special_date['day_special_from'])
                                        <div class="from-group d-flex">
                                            <a class="btn btn-info open-detail-special-date" id="{{ $special_date['id'] }}" style="color: white; cursor: pointer;">Sửa</a>
                                            <a href="{{ action('SpecialDateController@deleteSpecialDate') }}?id={{ $special_date['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Xóa</a>
                                        </div>
                                    @else
                                        <span class="badge badge-warning">Đã hết hạn!</span>
                                    @endif
                                @else
                                    @if($special_date['is_approved'] == 1)
                                        <span class="badge badge-primary">Đã duyệt!</span>
                                    @elseif($special_date['is_approved'] == -1)
                                        <span class="badge badge-danger">Đã từ chối!</span>
                                    @elseif(date("Y-m-d") <= $special_date['day_special_from'])
                                        <div class="from-group d-flex">
                                            <a class="btn btn-info open-detail-approve-special-date" id="{{ $special_date['id'] }}" style="color: white; cursor: pointer;">Chi tiết</a>
                                        </div>
                                    @else
                                        <span class="badge badge-warning">Đã hết hạn!</span>
                                    @endif
                                @endif
                            </td>
                            @if(auth()->user()->id != 7)
                                <td>
                                    <div class="from-group d-flex">
                                        <a class="btn btn-info open-detail-approve-special-date" id="{{ $special_date['id'] }}" style="color: white; cursor: pointer;">Chi tiết</a>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endif
                @endforeach   
            </tbody>
        </table>

        <div id="bsc-modal" class="modal fade" role="dialog"> <!-- modal bsc -->
            <div class="modal-dialog">
              <div class="modal-content">
                @if(auth()->user()->id != 7)
                    <form action="{{ action('SpecialDateController@updateSpecialDate') }}" method="post" class="form-horizontal">
                    @csrf
                        <div id="html_pending">
                            
                        </div>
                    </form> <!-- end form -->
                @else
                    <form action="{{ action('SpecialDateController@approveOverTime') }}" method="post" class="form-horizontal">
                    @csrf
                        <div id="html_pending">
                            
                        </div>
                    </form> <!-- end form -->
                @endif
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
                data:
                {
                    id: id,
                },
                cache: false,
                success: function (data)
                {
                    console.log(data);
                    $('#html_pending').empty().append(data);
                    $('#bsc-modal').modal();
                }
            });
        });

        $('.open-detail-approve-special-date').click(function() {
            var id = $(this).attr('id');

            $.ajax({
                url: '{{ action('SpecialDateController@detailOverTime') }}',
                Type: 'POST',
                datatype: 'text',
                data:
                {
                    id: id,
                },
                cache: false,
                success: function (data)
                {
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

                events = <?php echo $calendar ?>;

                var dt = new Date();
                let now = new Date().toISOString().split('T')[0];

                now = now.slice(4);
                date_now = '';
                date_now += <?php echo $year?> + now;

                // Define element
                var calendarBasicViewElement = document.querySelector('.fullcalendar-basic');

                // Initialize
                if(calendarBasicViewElement) {
                    var calendarBasicViewInit = new FullCalendar.Calendar(calendarBasicViewElement, {
                        plugins: [ 'dayGrid', 'interaction' ],
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