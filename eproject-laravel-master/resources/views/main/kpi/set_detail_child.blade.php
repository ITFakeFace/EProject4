@extends('main._layouts.master')

<?php
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.min.css" rel="stylesheet') }}" type="text/css">
	<link href="{{ asset('assets/css/bootstrap_limitless.min.css" rel="stylesheet') }}" type="text/css">
	<link href="{{ asset('assets/css/layout.min.css" rel="stylesheet') }}" type="text/css">
	<link href="{{ asset('assets/css/components.min.css" rel="stylesheet') }}" type="text/css">
	<link href="{{ asset('assets/css/colors.min.css" rel="stylesheet') }}" type="text/css">
    <style>
        #tb_department_wrapper {
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

    <script src="{{ asset('global_assets/js/main/jquery.min.js') }}"></script>
	<script src="{{ asset('global_assets/js/main/bootstrap.bundle.min.js') }}"></script>
	<script src="{{ asset('global_assets/js/plugins/loaders/blockui.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
	<script src="{{ asset('global_assets/js/plugins/forms/styling/switch.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/demo_pages/form_checkboxes_radios.js') }}"></script>
@endsection

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Chi tiết công việc</h1>

        <div class="card-body">
            <h5 class="">Mục tiêu công việc: <b>{{ $kpi_detail['taskTarget'] }} </b></h5>

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

            <button id="btn_add" class="btn btn-primary">+</button>

            <div class="float-right">
                <button id="btn_submit_form" class="btn btn-success">Lưu</button>
            </div>

        </div>

        <form action="{{ action('KpiController@createDetailChild') }}" method="POST" id="form_detail_child">
        @csrf
        <input type="hidden" name="id_kpi_detail" value="{{ $kpi_detail['id'] }}">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên Kết quả</th>
                            <th>Mục tiêu</th>
                            <th>Đạt được</th>
                            {{-- <th>Mức độ tự tin hoàn thành</th> --}}
                            <th>Các bước thực hiện</th>
                            <th>Kĩ năng cần có</th>
                            <th>Quản lý phản hồi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @foreach ($data as $kpi_child)
                            <tr>
                                <input type="hidden" name="id_child[]" value="{{ $kpi_child['id'] }}">
                                <td><textarea name="name[]" id="" cols="20" rows="5">{{ $kpi_child['name'] }}</textarea></td>
                                <td><input type="number" name="number_target[]" id="" value="{{ $kpi_child['numberTarget'] }}"></td>
                                <td><input type="number" name="number_get[]" id="" value="{{ $kpi_child['numberGet'] }}"></td>
                                {{-- <td>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input-styled" name="stacked-radio-left" value="1" {{ $kpi_child['confidenceLevel'] == 1 ? 'checked' : '' }} data-fouc>
                                            Rất tự tin
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input-styled" name="stacked-radio-left" value="2" {{ $kpi_child['confidenceLevel'] == 2 ? 'checked' : '' }} data-fouc>
                                            Tự tin
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input-styled" name="stacked-radio-left" value="3" {{ $kpi_child['confidenceLevel'] == 3 ? 'checked' : '' }} data-fouc>
                                            Không tự tin
                                        </label>
                                    </div>
                                </td> --}}
                                <td><textarea name="duties_activities[]" id="" cols="20" rows="10">{{ $kpi_child['dutiesActivities'] }}</textarea></td>
                                <td><textarea name="skill[]" id="" cols="20" rows="10">{{ $kpi_child['skill'] }}</textarea></td>
                                <td><textarea name="" id="" cols="20" rows="10" disabled></textarea></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <!-- /both borders -->

@endsection

@section('scripts')
    <script>

        $("#btn_add").click(function() {
            html = '<tr>';
            html += '<input type="hidden" name="id_child[]" value="">';
            html += '<td><textarea name="name[]" id="" cols="20" rows="5"></textarea></td>';
            html += '<td><input type="number" name="number_target[]" id="" value=""></td>';
            html += '<td><input type="number" name="number_get[]" id="" value=""></td>';
            // html += '<td><div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input-styled" name="stacked-radio-left" value="1" data-fouc>Rất tự tin</label></div>';
            // html += '<div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input-styled" name="stacked-radio-left" value="2" data-fouc>Tự tin</label></div>';
            // html += '<div class="form-check"><label class="form-check-label"><input type="radio" class="form-check-input-styled" name="stacked-radio-left" value="3" data-fouc>Không tự tin</label></div></td>';
            html += '<td><textarea name="duties_activities[]" id="" cols="20" rows="10"></textarea></td>';
            html += '<td><textarea name="skill[]" id="" cols="20" rows="10"></textarea></td>';
            html += '<td><textarea name="" id="" cols="20" rows="10" disabled></textarea></td>';
            html += '</tr>';

            $("#tbody").append(html);
        });

        $("#btn_submit_form").click(function() {
            $("#form_detail_child").submit();
        });


        var DatatableBasic = function() {

            // Basic Datatable examples
            var _componentDatatableBasic = function() {
                if (!$().DataTable) {
                    console.warn('Warning - datatables.min.js is not loaded.');
                    return;
                }

                // Setting datatable defaults
                $.extend( $.fn.dataTable.defaults, {
                    autoWidth: false,
                    columnDefs: [{ 
                        orderable: false,
                        width: 100,
                        targets: [ 5 ]
                    }],
                    dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
                    language: {
                        search: '<span>Tìm kiếm:</span> _INPUT_',
                        searchPlaceholder: 'Nhập để tìm kiếm...',
                        lengthMenu: '<span>Hiển thị:</span> _MENU_',
                        paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
                    }
                });

                // Basic datatable
                $('.datatable-basic').DataTable();
                $('.datatable-basic2').DataTable();

                // Alternative pagination
                $('.datatable-pagination').DataTable({
                    pagingType: "simple",
                    language: {
                        paginate: {'next': $('html').attr('dir') == 'rtl' ? 'Next &larr;' : 'Next &rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr; Prev' : '&larr; Prev'}
                    }
                });

                // Datatable with saving state
                $('.datatable-save-state').DataTable({
                    stateSave: true
                });

                // Scrollable datatable
                var table = $('.datatable-scroll-y').DataTable({
                    autoWidth: true,
                    scrollY: 300
                });

                // Resize scrollable table when sidebar width changes
                $('.sidebar-control').on('click', function() {
                    table.columns.adjust().draw();
                });
            };

            // Select2 for length menu styling
            var _componentSelect2 = function() {
                if (!$().select2) {
                    console.warn('Warning - select2.min.js is not loaded.');
                    return;
                }

                // Initialize
                $('.dataTables_length select').select2({
                    minimumResultsForSearch: Infinity,
                    dropdownAutoWidth: true,
                    width: 'auto'
                });
            };

            return {
                init: function() {
                    _componentDatatableBasic();
                    _componentSelect2();
                }
            }
        }();

        document.addEventListener('DOMContentLoaded', function() {
            DatatableBasic.init();
        });

    </script>
@endsection