@extends('main._layouts.master')

<?php
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <link href="{{ asset('assets/css/components_datatables.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .go_set_kpi {
            color: red;
            font-weight: bold;
            text-decoration: underline;
        }

        /* .div_set_kpi {
            display: none;
        } */

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
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h1 class="pt-3 pl-3 pr-3">Thiết Lập KPI</h1>
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
            </div>
        </div>
    </div>
  
    <div class="row">
        <div class="col-lg-6 col-12">         
            <!-- Left buttons -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">KPI Cá Nhân</h6>
                </div>

                <div class="card-body">
                    <form action="#">
                        <div class="form-group row">
                            <label class="col-form-label col-lg-4">Chọn thời gian thiết lập:</label>
                            <div class="col-lg-6">
                                <select class="form-control" name="select_kpi" id="select_kpi">
                                    <option value="">-- Select --</option>
                                    <option value="Năm <?php echo date('Y') ?>">Năm <?php echo date('Y') ?></option>
                                    <option value="Quý I - <?php echo date('Y') ?>">Quý I - <?php echo date('Y') ?></option>
                                    <option value="Quý II - <?php echo date('Y') ?>">Quý II - <?php echo date('Y') ?></option>
                                    <option value="Quý III - <?php echo date('Y') ?>">Quý III - <?php echo date('Y') ?></option>
                                    <option value="Quý IV - <?php echo date('Y') ?>">Quý IV - <?php echo date('Y') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="html_pending_staff">

                        </div>               
                    </form>
                </div>
            </div>
            <!-- /left buttons -->
        </div>

        <div class="col-lg-6 col-12">         
            <!-- Left buttons -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">KPI Phòng Ban</h6>
                </div>

                <div class="card-body">
                    <form action="#">
                        <div class="form-group row">
                            <label class="col-form-label col-lg-4">Chọn thời gian để xem:</label>
                            <div class="col-lg-6">
                                <select  class="form-control" name="select_kpi_department" id="select_kpi_department">
                                    <option value="">-- Select --</option>
                                    <option value="Năm <?php echo date('Y') ?>">Năm <?php echo date('Y') ?></option>
                                    <option value="Quý I - <?php echo date('Y') ?>">Quý I - <?php echo date('Y') ?></option>
                                    <option value="Quý II - <?php echo date('Y') ?>">Quý II - <?php echo date('Y') ?></option>
                                    <option value="Quý III - <?php echo date('Y') ?>">Quý III - <?php echo date('Y') ?></option>
                                    <option value="Quý IV - <?php echo date('Y') ?>">Quý IV - <?php echo date('Y') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="html_pending_department">

                        </div>  
                    </form>
                </div>
            </div>
            <!-- /left buttons -->
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.day_leave').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $( document ).ready(function() {
                    
            $( "#select_kpi" ).change(function() {
                var select_kpi = $(this).val();
                var staff_id = {{ auth()->user()->id }}

                $.ajax({
                    url: '{{ action('KpiController@findKpiStaff') }}',
                    Type: 'GET',
                    datatype: 'text',
                    data:
                    {
                        staff_id: staff_id,
                        kpi_name: select_kpi
                    },
                    cache: false,
                    success: function (data)
                    {
                        $('#html_pending_staff').empty().append(data);
                    }
                });

            }); 

            $( "#select_kpi_department" ).change(function() {
                var select_kpi_department = $(this).val();
                var department_id = {{ auth()->user()->department }}
                var staff_manager = {{ auth()->user()->is_manager }}

                $.ajax({
                    url: '{{ action('KpiController@findKpiDepartment') }}',
                    Type: 'GET',
                    datatype: 'text',
                    data:
                    {
                        department_id: department_id,
                        kpi_name: select_kpi_department,
                        staff_manager: staff_manager
                    },
                    cache: false,
                    success: function (data)
                    {
                        $('#html_pending_department').empty().append(data);
                    }
                });

            }); 
        });

    </script>
@endsection