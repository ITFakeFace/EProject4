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

	<!-- /theme JS files -->
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Danh Sách Ngày Lễ</h1>
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
                        <input class="form-control" type="number" value="<?php echo $year ?>" name="year" id="year">
                    </div>
                    <div class="ml-3">
                        <input class="form-control btn btn-primary" type="submit" value="Tìm kiếm">
                    </div>
                </div>
            </form>

            <div class="form-group d-flex">
                <div class="export">
                    <a href ="{{ action('ExportController@exportSpecialDate') }}?y={{ $year }}" class="btn btn-success export" id="export-button"> Xuất Excel </a>
               </div>
               <div class=" ml-1">
                    <button class="btn btn-danger" data-toggle="modal" data-target="#exampleModalCenter">Tạo ngày lễ mới</button>
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
                            <h5 class="modal-title" id="exampleModalLongTitle">Tạo Ngày Lễ</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Từ ngày:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control day_leave" name="day_special_from" value="" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Đến ngày:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control day_leave" name="day_special_to" value="" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Mô tả ngày lễ:</label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" name="note" id="note" cols="20" rows="10" placeholder="VD: Lễ quốc khánh, Lễ Tết, ..." required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Tạo mới</button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>

        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Từ Ngày</th>
                    <th>Đến Ngày</th>
                    <th>Mô tả</th>
                    <th>Loại</th>
                    <th class="text-center">Sửa / Xóa</th>
                    <th style="max-width: 100px">Bổ sung công ngày lễ cho toàn nhân viên</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                @foreach ($data as $special_date)
                    @if($special_date['type_day'] == 1)
                        <tr>
                            <td><?php echo $count; $count++ ?></td>
                            <td><?php echo $special_date['day_special_from'] ?></td>
                            <td><?php echo $special_date['day_special_to'] ?></td>
                            <td>
                                <?php 
                                    if(strlen($special_date['note']) > 40) echo substr($special_date['note'], 0, 40) . '...';
                                    else echo $special_date['note'];    
                                ?>
                            </td>
                            <td>
                                <span class="badge badge-danger">Ngày lễ</span>
                            </td>
                            <td class="text-center">
                                @if(date("Y-m-d") < $special_date['day_special_from'])
                                    <div class="from-group">
                                        <a class="btn btn-info open-detail-special-date" id="{{ $special_date['id'] }}" style="color: white; cursor: pointer;">Sửa</a>
                                        <a href="{{ action('SpecialDateController@deleteSpecialDate') }}?id={{ $special_date['id'] }}" class="btn btn-danger ml-2" style="color: white; cursor: pointer;">Xóa</a>
                                    </div>
                                @else 
                                    <span class="badge badge-primary">Ngày lễ đã diễn ra!</span>
                                @endif
                            </td>
                            <td>
                                <?php
                                    $date_check = date("Y-m-d", strtotime('+2 days', strtotime($special_date['day_special_to'])));   
                                ?>
                                @if(date("Y-m-d") > $date_check)
                                    @if($special_date['detail_id'])
                                        <a href="{{ action('TimeSpecialController@details') }}?id_special_date={{ $special_date['id'] }}" class="btn btn-primary ml-2" style="color: white; cursor: pointer;">Chi tiết</a>
                                    @else
                                        <a href="{{ action('TimeSpecialController@create') }}?id={{ $special_date['id'] }}" class="btn btn-warning ml-2" style="color: white; cursor: pointer;">Bổ sung</a>
                                    @endif
                                @else
                                    <span class="">Sau kết thúc lễ 3 ngày mới được bổ sung công lễ!</span>
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