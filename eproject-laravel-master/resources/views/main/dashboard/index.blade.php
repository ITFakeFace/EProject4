@extends('main._layouts.master')

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
<div class="card">
    <h1 class="pt-3 pl-3 pr-3"><a href="{{action('StaffController@index')}}">Newest Employees</a> </h1>
    <table class="table datatable-basic">
        <thead>
            <tr>
                <th>No.</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Department</th>
                <th>Position</th>
                <th>Joining Date</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                @if (Auth::user()->is_manager == 1 && (Auth::user()->department == 2 || Auth::user()->department == 5))
                <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($staffListTakeTen as $index =>  $staff)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $staff['firstname'] }}</td>
                    <td>{{ $staff['lastname'] }}</td>
                    @foreach ($data_department as $department)
                        @if ($staff['department'] == $department['id'])
                            <td>{{ $department['nameVn'] }}</td>
                        @endif
                    @endforeach
                    <td>
                        @if ($staff['isManager'] == 0)
                            Employee
                        @else
                            Manager
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::createFromTimestamp($staff['joinedAt'] / 1000)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::createFromTimestamp($staff['dob'] / 1000)->format('Y-m-d') }}</td>
                    <td>
                        @if ($staff['gender'] == 1)
                            Male
                        @else
                            Female
                        @endif
                    </td>
                    @if (Auth::user()->is_manager == 1 && (Auth::user()->department == 2 || Auth::user()->department == 5))
                    <td>
                        <div class="list-icons">
                            <div class="dropdown">
                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                    <i class="icon-menu9"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{{ action('StaffController@getEditStaff') }}?id={{ $staff['id'] }}" class="dropdown-item">Update</a>
                                    <a href="{{ action('StaffController@getDetail') }}?id={{ $staff['id'] }}" class="dropdown-item">Detail</a>
                                    <a href="{{ route('exportWord1', ['id' => $staff['id']]) }}" class="dropdown-item">Export Employee File</a>
                                    <a href="{{ action('StaffController@getDeleteStaff') }}?id={{ $staff['id'] }}" class="dropdown-item" onclick="return confirm('Are you sure?')">Delete</a>
                                </div>
                            </div>
                        </div>
                    </td>
                    @endif
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Basic datatable -->
<div class="card">
    <h1 class="pt-3 pl-3 pr-3"><a href="{{action('DepartmentController@index')}}">Latest Departments</a> </h1>
    

    <table class="table datatable-basic">
        <thead>
            <tr>
                <th>No.</th>
                <th>Department Name</th>
                <th>Department Name (Vietnamese)</th>
                <th>Number of employees</th>
                @if (Auth::user()->is_manager == 1 && (Auth::user()->department == 2 || Auth::user()->department == 5))
                <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
                @foreach($departmentListTakeTen as $index => $department)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $department['name'] }}</td>
                    <td>{{ $department['nameVn'] }}</td>
                    <td>{{ $department['employee_count'] }}</td>
                    <!-- <td>
                        @if($department['del'] == 0)
                            Show
                        @else
                            Hide
                        @endif    
                    </td> -->
                    @if (Auth::user()->is_manager == 1 && (Auth::user()->department == 2 || Auth::user()->department == 5))
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right">
                                            <a href="{{ action('DepartmentController@getEditDep') }}?id={{ $department['id'] }}" class="dropdown-item">Update</a>
                                            <a href="{{ action('DepartmentController@getDeleteDep') }}?id={{ $department['id'] }}" class="dropdown-item" onclick="return confirm('Are you sure?')">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    @endif    
                </tr>
                @endforeach
        </tbody>
    </table>
</div>
<!-- /basic datatable -->

 <!-- Modal Add Department -->
 <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{action('DepartmentController@CreateDepartment')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">ADD NEW</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Department Name</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="txtName"  required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Department Name (Vietnamese)</label>
                            <div class="col-lg-9">
                            <input type="text" class="form-control" name="txtName1"  required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add New</button>
                    </div>
                </form>  
            </div>
        </div>
    </div>

<!-- /basic datatable -->
    <!-- Pies -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Age</h5>
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
                    <h5 class="card-title">Gender</h5>
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
                    <h5 class="card-title">Education</h5>
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
                    <h5 class="card-title">Seniority</h5>
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
                    <h5 class="card-title">Yearly Growth {{ $last_year }}</h5>
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
                if (pie_chart_element) {
                    let staffs_age = {!! $staffs_age !!};

                    let arr_staffs_age = Object.entries(staffs_age);
                    arr_staffs_age[0][0] = "18-25 Years Old";
                    arr_staffs_age[1][0] = "25-35 Years Old";
                    arr_staffs_age[2][0] = "35-45 Years Old";
                    arr_staffs_age[3][0] = "45-55 Years Old";
                    arr_staffs_age[4][0] = "Others";

                    // Generate chart
                    var pie_chart = c3.generate({
                        bindto: pie_chart_element,
                        size: {
                            width: 350
                        },
                        color: {
                            pattern: ['#2ec7c9', '#b6a2de', '#5ab1ef', '#ffb980', '#d87a80']
                        },
                        data: {
                            columns: [
                                arr_staffs_age[0],
                                arr_staffs_age[1],
                                arr_staffs_age[2],
                                arr_staffs_age[3],
                                arr_staffs_age[4]
                            ],
                            type: 'pie'
                        }
                    });

                    // Resize chart on sidebar width change
                    sidebarToggle && sidebarToggle.addEventListener('click', function() {
                        pie_chart.resize();
                    });
                }

                // Donut chart
                if (donut_chart_element) {
                    let staffs_gender = {!! $staffs_gender !!};

                    let arr_staffs_gender = Object.entries(staffs_gender);

                    // Generate chart
                    var donut_chart = c3.generate({
                        bindto: donut_chart_element,
                        size: {
                            width: 350
                        },
                        color: {
                            pattern: ['#2ec7c9', '#b6a2de']
                        },
                        data: {
                            columns: [
                                arr_staffs_gender[0],
                                arr_staffs_gender[1],
                            ],
                            type: 'donut'
                        },
                        donut: {
                            title: "Male / Female Ratio"
                        }
                    });

                    // Resize chart on sidebar width change
                    sidebarToggle && sidebarToggle.addEventListener('click', function() {
                        donut_chart.resize();
                    });
                }

                // Pie chart
                if (pie_chart_element_education) {
                    let staffs_education = {!! $staffs_education !!};

                    let arr_staffs_education = Object.entries(staffs_education);
                    arr_staffs_education[0][0] = "High School";
                    arr_staffs_education[1][0] = "Vocational";
                    arr_staffs_education[2][0] = "College";
                    arr_staffs_education[3][0] = "University";
                    arr_staffs_education[4][0] = "Postgraduate";

                    // Generate chart
                    var pie_chart_education = c3.generate({
                        bindto: pie_chart_element_education,
                        size: {
                            width: 350
                        },
                        color: {
                            pattern: ['#2ec7c9', '#b6a2de', '#5ab1ef', '#ffb980', '#d87a80']
                        },
                        data: {
                            columns: [
                                arr_staffs_education[0],
                                arr_staffs_education[1],
                                arr_staffs_education[2],
                                arr_staffs_education[3],
                                arr_staffs_education[4]
                            ],
                            type: 'pie'
                        }
                    });

                    // Resize chart on sidebar width change
                    sidebarToggle && sidebarToggle.addEventListener('click', function() {
                        pie_chart_education.resize();
                    });
                }

                // Donut chart
                if (donut_chart_element_tn) {
                    let staffs_tn = {!! $staffs_tn !!};

                    let arr_staffs_tn = Object.entries(staffs_tn);

                    // Generate chart
                    var donut_chart_tn = c3.generate({
                        bindto: donut_chart_element_tn,
                        size: {
                            width: 350
                        },
                        color: {
                            pattern: ['#2ec7c9', '#b6a2de', '#5ab1ef', '#ffb980', '#d87a80']
                        },
                        data: {
                            columns: [
                                arr_staffs_tn[0],
                                arr_staffs_tn[1],
                                arr_staffs_tn[2],
                                arr_staffs_tn[3]
                            ],
                            type: 'donut'
                        },
                        donut: {
                            title: "Seniority"
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
                        color: ['#2ec7c9', '#b6a2de', '#5ab1ef', '#ffb980', '#d87a80'],

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
                            data: ['Number of Employees', 'Number of Leavers'],
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
                        series: [{
                                name: 'Number of Employees',
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
                                    data: [{
                                        type: 'average',
                                        name: 'Average'
                                    }]
                                }
                            },
                            {
                                name: 'Number of Leavers',
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
                                    data: [{
                                        type: 'average',
                                        name: 'Average'
                                    }]
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
                    resizeCharts = setTimeout(function() {
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