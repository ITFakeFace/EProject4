@extends('main._layouts.master')

<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
?>

@section('css')
    <style>
        .border-red {
            border-color: red !important;
        }

        .list-icons-item-remove::after {
            cursor: pointer;
            content: "";
            font-size: .8125rem;
            font-family: icomoon;
            font-size: 1rem;
            min-width: 1rem;
            text-align: center;
            display: inline-block;
            vertical-align: middle;
            -webkit-font-smoothing: antialiased;
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
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                @if ($detail_of_kpi)
                    <h1 class="pt-3 pl-3 pr-3">
                        KPI -
                        {{ $detail_of_kpi[0]['kpi_name'] }} {{ $detail_of_kpi[0]['staff_create'] ? '- ' . $detail_of_kpi[0]['staff_create'] : '' }}
                        {{ $detail_of_kpi[0]['staff_create_is_manager'] == 1 ? '- Manager' : '' }} {{ $detail_of_kpi[0]['staff_create_is_manager'] == 0 && $detail_of_kpi[0]['staff_create_is_manager'] !== null ? '- Employee' : '' }}
                        {{ $detail_of_kpi[0]['department_staff_create'] ? '- ' . $detail_of_kpi[0]['department_staff_create'] : '' }}
                        {{ $detail_of_kpi[0]['department_create'] ? 'Department: ' . $detail_of_kpi[0]['department_create'] : '' }}
                    </h1>
                    <h4 class="pt-3 pl-3 pr-3">
                        Created at: {{ $detail_of_kpi[0]['created_at'] }}
                    </h4>
                    @if ($detail_of_kpi[0]['update_at'])
                        <h4 class="pt-3 pl-3 pr-3">
                            Last edited at: {{ $detail_of_kpi[0]['update_at'] }}
                        </h4>
                    @endif
                    @if ($detail_of_kpi[0]['is_approved'] == 3)
                        <h4 class="pt-3 pl-3 pr-3">
                            Rejected by {{ $detail_of_kpi[0]['staff_approve'] }} - {{ $detail_of_kpi[0]['staff_approve_is_manager'] == 1 ? 'Manager' : 'Employee' }} - {{ $detail_of_kpi[0]['staff_approve_department'] }}
                        </h4>
                    @elseif($detail_of_kpi[0]['is_approved'] != 0)
                        <h4 class="pt-3 pl-3 pr-3">
                            Approved by {{ $detail_of_kpi[0]['staff_approve'] }} - {{ $detail_of_kpi[0]['staff_approve_is_manager'] == 1 ? 'Manager' : 'Employee' }} - {{ $detail_of_kpi[0]['staff_approve_department'] }}
                        </h4>
                    @endif
                @else
                    <h1 class="pt-3 pl-3 pr-3">
                        Set KPI
                        - {{ $kpi_name }}
                        <?php
                        if ($staff_id !== null) {
                            echo '- ' . auth()->user()->firstname . ' ' . auth()->user()->lastname;
                        } elseif ($department_id !== null) {
                            echo '- Department: ' . $staff[0][2];
                        }
                        ?>
                    </h1>
                @endif
                @if ($create_success)
                    <div class="pt-3 pl-3 pr-3">
                        <div class="alert alert-success">
                            {{ $create_success }}
                        </div>
                    </div>
                @endif

                @if (\Session::has('success'))
                    <div class="pt-3 pl-3 pr-3">
                        <div class="alert alert-success">
                            {!! \Session::get('success') !!}
                        </div>
                    </div>
                @endif

                @if (\Session::has('error'))
                    <div class="pt-3 pl-3 pr-3">
                        <div class="alert alert-danger">
                            {!! \Session::get('error') !!}
                        </div>
                    </div>
                @endif

                @if (!$readonly)
                    <div class="card-body">
                        <div class="form-group">
                            <div class="float-left">
                                <button id="btn_add_more" class="btn btn-info">Add Task</button>
                            </div>
                            <div class="float-right">
                                <button id="btn_submit_form" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($go_approve)
                    <form class="pb-3" action="{{ action('KpiController@approveKpi') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kpi_id" value="{{ $kpi_id }}">
                        <div class="card-body">
                            <div class="form-group">
                                <div class="float-left">
                                    <a href="{{ action('KpiController@listKpi') }}" class="btn btn-light" style="cursor: pointer">Return to KPI list</a>
                                </div>

                                @if ((auth()->user()->department !== 2 && $detail_of_kpi[0]['is_approved'] != 2 && $detail_of_kpi[0]['is_approved'] != 1) || (auth()->user()->department == 2 && auth()->user()->is_manager == 1 && $detail_of_kpi[0]['is_approved'] != 1) || (auth()->user()->department == 2 && $detail_of_kpi[0]['is_approved'] != 1))
                                    <div class="float-right">
                                        <input class="btn btn-danger" type="submit" value="Reject" name="btn_reject">
                                    </div>

                                    <div class="float-right">
                                        <button id="" class="btn btn-success mr-2">Approve</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
    <form action="{{ action('KpiController@createKpi') }}" method="POST" id="form_detail_kpi">
        @csrf
        <input type="hidden" name="department_id" value="{{ $department_id }}">
        <input type="hidden" name="kpi_name" value="{{ $kpi_name }}">
        <input type="hidden" name="kpi_id" value="{{ $kpi_id }}">
        <input type="hidden" name="staff_id" value="{{ $staff_id }}">
        <div class="row" id="row_kpi_detail">
            <?php $count = 1; ?>
            @foreach ($kpi_details as $kpi_detail)
                <input type="hidden" name="kpi_detail_id[]" value="{{ $kpi_detail['id'] }}">
                <input id="input_del<?php echo $count; ?>" type="hidden" name="del[]" value="false">
                <div class="col-md-6 one_row" id="one_row<?php echo $count; ?>">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h6 class="card-title">Task <?php echo $count; ?></h6>
                            <div class="header-elements">
                                <div class="list-icons">
                                    @if (!$readonly)
                                        <a class="list-icons-item list-icons-item-remove" onclick="removeTask(<?php echo $count; ?>)"></a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-form-label col-lg-4">Task Target:</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control target" name="target[]" value="{{ $kpi_detail['taskTarget'] }}" onkeyup=checkEmpty(this) placeholder="E.g., Increase the conversion rate of the website by 20%" <?php echo $readonly ? 'readonly' : 'required'; ?>>
                                </div>
                            </div>

                            {{-- <div class="form-group row">
                                <label class="col-form-label col-lg-4">Task Details:</label>
                                <div class="col-lg-8">
                                    <textarea rows="3" cols="3" class="form-control task_description" name="task_description[]" onkeyup=checkEmpty(this) placeholder="E.g., The current conversion rate of the website is stagnating at 12%, to be competitive with peers, we must optimize it to 20% in 6 months" <?php echo $readonly ? 'readonly' : 'required'; ?>>{{ $kpi_detail['taskDescription'] }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-lg-4">Steps to Achieve:</label>
                                <div class="col-lg-8">
                                    <textarea rows="3" cols="3" class="form-control duties_activities" name="duties_activities[]" onkeyup=checkEmpty(this) placeholder="E.g., Market research, marketing campaigns, ..." <?php echo $readonly ? 'readonly' : 'required'; ?>>{{ $kpi_detail['dutiesActivities'] }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-lg-4">Required Skills:</label>
                                <div class="col-lg-8">
                                    <textarea rows="3" cols="3" class="form-control skill" name="skill[]" onkeyup=checkEmpty(this) placeholder="E.g., Information retrieval, ..." <?php echo $readonly ? 'readonly' : 'required'; ?>>{{ $kpi_detail['skill'] }}</textarea>
                                </div>
                            </div> --}}

                            <div class="form-group row">
                                <label class="col-form-label col-lg-4">Weight on Total Tasks:</label>
                                <div class="col-lg-8">
                                    <input id="ratio<?php echo $count; ?>" type="number" name="ratio[]" onkeyup=checkEmpty(this) class="form-control ratio" min="0" max="100" value="{{ $kpi_detail['ratio'] }}" placeholder="E.g., 20" <?php echo $readonly ? 'readonly' : 'required'; ?>>
                                </div>
                            </div>

                            <div>
                                <a href="../kpi/set-detail-child?kpi_detail_id=<?php echo $kpi_detail['id']; ?>" class="btn btn-success text-left float-right">Details</a>
                            </div>

                        </div>
                    </div>
                </div>
                <?php $count++; ?>
            @endforeach

        </div>
    </form>

@endsection

@section('scripts')
    <script>
        $('.day_leave').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        function removeTask(row_number) {
            Swal.fire({
                title: 'Are you sure you want to delete this task?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("one_row" + row_number).style.display = "none";
                    document.getElementById("input_del" + row_number).value = 'true';
                    document.getElementById("ratio" + row_number).value = '0';
                }
            });
        }

        function checkEmpty(e) {
            if (e.value) {
                e.classList.remove('border-red');
            } else {
                e.classList.add('border-red');
            }
        }

        $(document).ready(function() {
            var count_job = <?php echo $count; ?>;
            $("#btn_add_more").click(function() {
                html = '<div class="col-md-6" id="one_row' + count_job + '"><input id="input_del' + count_job + '" type="hidden" name="del[]" value="false"><div class="card"><div class="card-header header-elements-inline"><h6 class="card-title">Task ' + count_job + '</h6><div class="header-elements"><div class="list-icons"><a class="list-icons-item list-icons-item-remove"  onclick="removeTask(' + count_job + ')"></a></div></div></div>'
                html += '<div class="card-body">';
                html += '<div class="form-group row"><label class="col-form-label col-lg-4">Task Target:</label><div class="col-lg-8"><input type="text" class="form-control target" onkeyup=checkEmpty(this) name="target[]" placeholder="E.g., Increase the conversion rate of the website by 20%" required></div></div>';
                // html += '<div class="form-group row"><label class="col-form-label col-lg-4">Task Details:</label><div class="col-lg-8"><textarea rows="3" cols="3" class="form-control task_description" onkeyup=checkEmpty(this) name="task_description[]" placeholder="E.g., The current conversion rate of the website is stagnating at 12%, to be competitive with peers, we must optimize it to 20% in 6 months" required></textarea></div></div>';
                // html += '<div class="form-group row"><label class="col-form-label col-lg-4">Steps to Achieve:</label><div class="col-lg-8"><textarea rows="3" cols="3" class="form-control duties_activities" onkeyup=checkEmpty(this) name="duties_activities[]" placeholder="E.g., Market research, marketing campaigns, ..." required></textarea></div></div>';
                // html += '<div class="form-group row"><label class="col-form-label col-lg-4">Required Skills:</label><div class="col-lg-8"><textarea rows="3" cols="3" class="form-control skill" onkeyup=checkEmpty(this) name="skill[]" placeholder="E.g., Information retrieval, ..." required></textarea></div></div>';
                html += '<div class="form-group row"><label class="col-form-label col-lg-4">Weight on Total Tasks:</label><div class="col-lg-8"><input id="ratio' + count_job + '" type="number" name="ratio[]" class="form-control ratio" onkeyup=checkEmpty(this) min="0" max="100" placeholder="E.g., 20" required></div></div>';
                html += '</div></div></div>';
                $("#row_kpi_detail").append(html);
                count_job++;

            });

            $("#btn_submit_form").click(function() {
                $("#form_detail_kpi").submit();
            });

            $("#form_detail_kpi").submit(function() {
                //Check target        
                var target = $('.target').map(function() {
                    return $(this).val();
                });

                for (let i = 0; i < target.length; i++) {
                    if (!target[i]) {
                        $('.target:eq(' + i + ')').addClass('border-red');
                        $('.target:eq(' + i + ')').focus();
                        Swal.fire(
                            'Cannot save!',
                            'Task Target cannot be empty!',
                            'error'
                        );
                        return false;
                    } else if (target[i].length > 300) {
                        $('.target:eq(' + i + ')').addClass('border-red');
                        $('.target:eq(' + i + ')').focus();
                        Swal.fire(
                            'Cannot save!',
                            'Task Target cannot exceed 300 characters!',
                            'error'
                        );
                        return false;
                    }
                }

                //Check task_description        
                var task_description = $('.task_description').map(function() {
                    return $(this).val();
                });

                for (let i = 0; i < task_description.length; i++) {
                    if (!task_description[i]) {
                        $('.task_description:eq(' + i + ')').addClass('border-red');
                        $('.task_description:eq(' + i + ')').focus();
                        Swal.fire(
                            'Cannot save!',
                            'Task Details cannot be empty!',
                            'error'
                        );
                        return false;
                    } else if (task_description[i].length > 300) {
                        $('.task_description:eq(' + i + ')').addClass('border-red');
                        $('.task_description:eq(' + i + ')').focus();
                        Swal.fire(
                            'Cannot save!',
                            'Task Details cannot exceed 300 characters!',
                            'error'
                        );
                        return false;
                    }
                }

                //Check duties_activities        
                var duties_activities = $('.duties_activities').map(function() {
                    return $(this).val();
                });

                for (let i = 0; i < duties_activities.length; i++) {
                    if (!duties_activities[i]) {
                        $('.duties_activities:eq(' + i + ')').addClass('border-red');
                        $('.duties_activities:eq(' + i + ')').focus();
                        Swal.fire(
                            'Cannot save!',
                            'Steps to Achieve cannot be empty!',
                            'error'
                        );
                        return false;
                    } else if (duties_activities[i].length > 300) {
                        $('.duties_activities:eq(' + i + ')').addClass('border-red');
                        $('.duties_activities:eq(' + i + ')').focus();
                        Swal.fire(
                            'Cannot save!',
                            'Steps to Achieve cannot exceed 300 characters!',
                            'error'
                        );
                        return false;
                    }
                }

                //Check skill        
                var skill = $('.skill').map(function() {
                    return $(this).val();
                });

                for (let i = 0; i < skill.length; i++) {
                    if (!skill[i]) {
                        $('.skill:eq(' + i + ')').addClass('border-red');
                        $('.skill:eq(' + i + ')').focus();
                        Swal.fire(
                            'Cannot save!',
                            'Required Skills cannot be empty!',
                            'error'
                        );
                        return false;
                    } else if (skill[i].length > 300) {
                        $('.skill:eq(' + i + ')').addClass('border-red');
                        $('.skill:eq(' + i + ')').focus();
                        Swal.fire(
                            'Cannot save!',
                            'Required Skills cannot exceed 300 characters!',
                            'error'
                        );
                        return false;
                    }
                }

                //Check ratio        
                var ratio = $('.ratio').map(function() {
                    return $(this).val();
                });

                for (let i = 0; i < ratio.length; i++) {
                    if (!ratio[i]) {
                        $('.ratio:eq(' + i + ')').addClass('border-red');
                        $('.ratio:eq(' + i + ')').focus();
                        Swal.fire(
                            'Cannot save!',
                            'Task Weight cannot be empty!',
                            'error'
                        );
                        return false;
                    } else if (ratio[i] > 100 || ratio[i] < 0) {
                        $('.ratio:eq(' + i + ')').addClass('border-red');
                        $('.ratio:eq(' + i + ')').focus();
                        Swal.fire(
                            'Cannot save!',
                            'Task Weight cannot be less than 0 or exceed 100!',
                            'error'
                        );
                        return false;
                    }
                }


                //Check ratio 100
                var ratio = $('.ratio').map(function() {
                    return $(this).val();
                });

                let total_ratio = 0;
                for (let val of ratio) {
                    total_ratio += Number(val);
                }

                if (total_ratio !== 100) {
                    Swal.fire(
                        'Cannot save!',
                        'Total Task Weight must equal 100!',
                        'error'
                    );
                    return false;
                }

            });

        });
    </script>
@endsection
