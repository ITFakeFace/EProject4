@extends('main._layouts.master')

<?php
    // {{ }} <--- cac ky tu dac biet se duoc thay the
    // {!! !!} <--- cac ky tu dac biet se khong thay the
    // {{-- --}} <--- comment code trong blade
    /**
     * section('scripts') <--- coi o? master.blade.php <--- no' la @yield('scripts')
     * section co' mo? la phai co' dong'
     * neu ma soan code php thi nen de? tren dau` de? no' load tuan tu chinh xac hon giong nhu code php nam tren section('scripts') vay ok roi
     * */
?>

@section('css')
   
@endsection

@section('js')    
    <!-- Theme JS files -->
    {{-- <link href="{{ asset('assets_chart/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets_chart/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets_chart/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets_chart/css/colors.min.css') }}" rel="stylesheet" type="text/css"> --}}
    
    <script src="{{ asset('global_assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/visualization/c3/c3.min.js') }}"></script>

    <script src="{{ asset('global_assets/js/plugins/visualization/echarts/echarts.min.js') }}"></script>
@endsection

@section('content')

    <!-- Pies -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Độ Tuổi</h5>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="chart-container text-center">
                        <div class="d-inline-block" id="c3-pie-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Giới Tính</h5>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="chart-container text-center">
                        <div class="d-inline-block" id="c3-donut-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pies -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Học Vấn</h5>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="chart-container text-center">
                        <div class="d-inline-block" id="c3-pie-chart-education"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Thâm niên</h5>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="chart-container text-center">
                        <div class="d-inline-block" id="c3-donut-chart-tn"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Tăng Trưởng Năm {{ $last_year }}</h5>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="chart-container">
                        <div class="chart has-fixed-height" id="columns_basic"></div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <!-- /content area -->

@endsection

@section('scripts')
<script>    
    var С3BarsPies = function() {
        // Chart
        var _barsPiesExamples = function() {
            if (typeof c3 == 'undefined') {
                console.warn('Warning - c3.min.js is not loaded.');
                return;
            }
        
            // Define charts elements
            var pie_chart_element = document.getElementById('c3-pie-chart');
            var donut_chart_element = document.getElementById('c3-donut-chart');
            var pie_chart_element_education = document.getElementById('c3-pie-chart-education');
            var donut_chart_element_tn = document.getElementById('c3-donut-chart-tn');
            var sidebarToggle = document.querySelector('.sidebar-control');
        
        
            // Pie chart
            if(pie_chart_element) {
                let staffs_age = {!! $staffs_age !!};

                let arr_staffs_age = Object.entries(staffs_age);
                arr_staffs_age[0][0] = "18-25 Tuổi";
                arr_staffs_age[1][0] = "25-35 Tuổi";
                arr_staffs_age[2][0] = "35-45 Tuổi";
                arr_staffs_age[3][0] = "45-55 Tuổi";
                arr_staffs_age[4][0] = "Khác";

                // Generate chart
                var pie_chart = c3.generate({
                    bindto: pie_chart_element,
                    size: { width: 350 },
                    color: {
                        pattern: ['#2ec7c9','#b6a2de','#5ab1ef','#ffb980','#d87a80']
                    },
                    data: {
                        columns: [
                            arr_staffs_age[0],
                            arr_staffs_age[1],
                            arr_staffs_age[2],
                            arr_staffs_age[3],
                            arr_staffs_age[4]
                        ],
                        type : 'pie'
                    }
                });
        
                // Resize chart on sidebar width change
                sidebarToggle && sidebarToggle.addEventListener('click', function() {
                    pie_chart.resize();
                });
            }

            // Donut chart
            if(donut_chart_element) {
                let staffs_gender = {!! $staffs_gender !!};

                let arr_staffs_gender = Object.entries(staffs_gender);    

                // Generate chart
                var donut_chart = c3.generate({
                    bindto: donut_chart_element,
                    size: { width: 350 },
                    color: {
                        pattern: ['#2ec7c9','#b6a2de']
                    },
                    data: {
                        columns: [
                            arr_staffs_gender[0],
                            arr_staffs_gender[1],
                        ],
                        type : 'donut'
                    },
                    donut: {
                        title: "Tỉ lệ Nam / Nữ"
                    }
                });
        
                // Resize chart on sidebar width change
                sidebarToggle && sidebarToggle.addEventListener('click', function() {
                    donut_chart.resize();
                });
            }

            // Pie chart
            if(pie_chart_element_education) {
                let staffs_education = {!! $staffs_education !!};

                let arr_staffs_education = Object.entries(staffs_education);
                arr_staffs_education[0][0] = "THPT";
                arr_staffs_education[1][0] = "Trung Cấp";
                arr_staffs_education[2][0] = "Cao Đẳng";
                arr_staffs_education[3][0] = "Đại Học";
                arr_staffs_education[4][0] = "Trên Đại Học";

                // Generate chart
                var pie_chart_education = c3.generate({
                    bindto: pie_chart_element_education,
                    size: { width: 350 },
                    color: {
                        pattern: ['#2ec7c9','#b6a2de','#5ab1ef','#ffb980','#d87a80']
                    },
                    data: {
                        columns: [
                            arr_staffs_education[0],
                            arr_staffs_education[1],
                            arr_staffs_education[2],
                            arr_staffs_education[3],
                            arr_staffs_education[4]
                        ],
                        type : 'pie'
                    }
                });
        
                // Resize chart on sidebar width change
                sidebarToggle && sidebarToggle.addEventListener('click', function() {
                    pie_chart_education.resize();
                });
            }
        
            // Donut chart
            if(donut_chart_element_tn) {
                let staffs_tn = {!! $staffs_tn !!};

                let arr_staffs_tn = Object.entries(staffs_tn);    

                // Generate chart
                var donut_chart_tn = c3.generate({
                    bindto: donut_chart_element_tn,
                    size: { width: 350 },
                    color: {
                        pattern: ['#2ec7c9','#b6a2de','#5ab1ef','#ffb980','#d87a80']
                    },
                    data: {
                        columns: [
                            arr_staffs_tn[0],
                            arr_staffs_tn[1],
                            arr_staffs_tn[2],
                            arr_staffs_tn[3]
                        ],
                        type : 'donut'
                    },
                    donut: {
                        title: "Thâm niên"
                    }
                });
        
                // Resize chart on sidebar width change
                sidebarToggle && sidebarToggle.addEventListener('click', function() {
                    donut_chart_tn.resize();
                });
            }
            
        };
        
        return {
            init: function() {
                _barsPiesExamples();
            }
        }
    }();
        
    var EchartsColumnsBasicLight = function() {

        var _columnsBasicLightExample = function() {
            if (typeof echarts == 'undefined') {
                console.warn('Warning - echarts.min.js is not loaded.');
                return;
            }

            var columns_basic_element = document.getElementById('columns_basic');

            if (columns_basic_element) {
                
                let staffs_month = {!! $staffs_month !!};
                let staffs_off = {!! $staffs_off !!};

                // Initialize chart
                var columns_basic = echarts.init(columns_basic_element);

                // Options
                columns_basic.setOption({

                    // Define colors
                    color: ['#2ec7c9','#b6a2de','#5ab1ef','#ffb980','#d87a80'],

                    // Global text styles
                    textStyle: {
                        fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                        fontSize: 13
                    },

                    // Chart animation duration
                    animationDuration: 750,

                    // Setup grid
                    grid: {
                        left: 0,
                        right: 40,
                        top: 35,
                        bottom: 0,
                        containLabel: true
                    },

                    // Add legend
                    legend: {
                        data: ['Số lượng Nhân viên', 'Số lượng Nghỉ việc'],
                        itemHeight: 8,
                        itemGap: 20,
                        textStyle: {
                            padding: [0, 5]
                        }
                    },

                    // Add tooltip
                    tooltip: {
                        trigger: 'axis',
                        backgroundColor: 'rgba(0,0,0,0.75)',
                        padding: [10, 15],
                        textStyle: {
                            fontSize: 13,
                            fontFamily: 'Roboto, sans-serif'
                        }
                    },

                    // Horizontal axis
                    xAxis: [{
                        type: 'category',
                        data: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        axisLabel: {
                            color: '#333'
                        },
                        axisLine: {
                            lineStyle: {
                                color: '#999'
                            }
                        },
                        splitLine: {
                            show: true,
                            lineStyle: {
                                color: '#eee',
                                type: 'dashed'
                            }
                        }
                    }],

                    // Vertical axis
                    yAxis: [{
                        type: 'value',
                        axisLabel: {
                            color: '#333'
                        },
                        axisLine: {
                            lineStyle: {
                                color: '#999'
                            }
                        },
                        splitLine: {
                            lineStyle: {
                                color: ['#eee']
                            }
                        },
                        splitArea: {
                            show: true,
                            areaStyle: {
                                color: ['rgba(250,250,250,0.1)', 'rgba(0,0,0,0.01)']
                            }
                        }
                    }],

                    // Add series
                    series: [
                        {
                            name: 'Số lượng Nhân viên',
                            type: 'bar',
                            //import data
                            data: staffs_month,
                            itemStyle: {
                                normal: {
                                    label: {
                                        show: true,
                                        position: 'top',
                                        textStyle: {
                                            fontWeight: 500
                                        }
                                    }
                                }
                            },
                            markLine: {
                                data: [{type: 'average', name: 'Average'}]
                            }
                        },
                        {
                            name: 'Số lượng Nghỉ việc',
                            type: 'bar',
                            //import data
                            data: staffs_off,
                            itemStyle: {
                                normal: {
                                    label: {
                                        show: true,
                                        position: 'top',
                                        textStyle: {
                                            fontWeight: 500
                                        }
                                    }
                                }
                            },
                            markLine: {
                                data: [{type: 'average', name: 'Average'}]
                            }
                        }
                    ]
                });
            }

            var triggerChartResize = function() {
                columns_basic_element && columns_basic.resize();
            };

            // On sidebar width change
            var sidebarToggle = document.querySelector('.sidebar-control');
            sidebarToggle && sidebarToggle.addEventListener('click', triggerChartResize);

            // On window resize
            var resizeCharts;
            window.addEventListener('resize', function() {
                clearTimeout(resizeCharts);
                resizeCharts = setTimeout(function () {
                    triggerChartResize();
                }, 200);
            });
        };

        return {
            init: function() {
                _columnsBasicLightExample();
            }
        }
    }();

    document.addEventListener('DOMContentLoaded', function() {
        С3BarsPies.init();
    });

    document.addEventListener('DOMContentLoaded', function() {
        EchartsColumnsBasicLight.init();
    });

</script>
@endsection