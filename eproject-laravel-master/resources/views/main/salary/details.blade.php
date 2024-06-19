@extends('main._layouts.master')

@section('css')
@endsection

@section('js')
    <script src="{{ asset('global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/anytime.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable_init.js') }}"></script>
@endsection

@section('js')
@endsection


@section('content')
    <div class="card">
        <h1 class="pt-3 pl-3 pr-3">Chi tiết bảng tính lương</h1>
        <div class="card-header header-elements-inline">
            
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table datatable-basic">
                        <thead>
                        <tr>
                            <th>Mã nhân viên</th>
                            <th>Tên nhân viên</th>
                            <th>Công thường</th>
                            <th>Nghỉ có công</th>
                            <th>Lương CB (1)</th>
                            <th>Lương trong tháng (2)</th>
                            <th>Lương tăng ca (3)</th>
                            <th>Các khoản phụ cấp (4)</th>
                            <th>Các khoản khấu trừ (5)</th>
                            <th>
                                <p>Thu nhập chịu thuế (6)</p>
                                <p>= 2 + 3 + 4 (PC tính thuế) - 4 (PC không tính thuế)</p>
                            </th>
                            <th>
                                <p>Thu nhập tính thuế (7)</p>
                                <p>= 6 - 5 - (Các khoản giảm trừ gia cảnh)</p>
                            </th>
                            <th>Thuế TNCC (8)</th>
                            <th>
                                <p>Lương thực nhận (9)</p>
                                <p>= 2 + 3 + 4 - 5 - 8</p>
                            </th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $item)
                            <tr>
                                <td>{{ $item->staff->id }}</td>
                                <td>{{ $item->staff->firstname . ' '. $item->staff->lastname }}</td>
                                <td>{{ number_format($item->totalDayWork, 1) }}</td>
                                <td>{{ number_format($item->totalSpecialDay, 1) }}</td>
                                <td>{{ number_format($item->baseSalaryContract) }}</td> <!-- lương cơ bản -->
                                <td>{{ number_format($item->salary) }}</td> <!-- lương cơ bản -->
                                <td>{{ number_format($item->salaryOt) }}</td> <!-- lương tăng ca -->
                                <td>{{ number_format($item->totalAllowance) }}</td>
                                <td>{{ number_format($item->totalInsurance) }}</td>
                                <td>{{ number_format($item->incomeTax) }}</td>
                                <td>{{ number_format($item->taxableIncome) }}</td>
                                <td>{{ number_format($item->personalTax) }}</td>
                                <td>{{ number_format($item->salaryActuallyReceived) }}</td>
                                <td>
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modalDetail" onclick="loadDetailStaff({{ $item->staff->id }})" class="dropdown-item">Chi tiết lương</a>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modalDetailAllowance" onclick="loadDetailAllowanceStaff({{ $item->staff->id }})" class="dropdown-item">Chi tiết phụ cấp</a>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modalDetailInsurance" onclick="loadDetailInsuranceStaff({{ $item->staff->id }})" class="dropdown-item">Chi tiết khấu trừ</a>
                                            <a href="{{ route('exportStaffPayroll', ['id' => $item->id]) }}" class="dropdown-item">Xuất phiếu lương</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <!-- Full width modal -->
                    <div id="modalDetail" class="modal fade" tabindex="-1">
                        <div class="modal-dialog modal-full">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Chi tiết bảng lương</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body">
                                    <table class="table datatable-detail">
                                        <thead>
                                        <tr>
{{--                                            <th>Tên nhân viên</th>--}}
                                            <th>Ngày chấm công</th>
                                            <th>Ghi chú</th>
                                            <th>Công chuẩn của tháng (1)</th>
                                            <th>Lương hợp đồng (2)</th>
                                            <th>Lương 1 ngày (4)</th>
                                            <th>Công đã làm (5)</th>
                                            <th>Thành tiền lương ngày công (6)</th>
                                            <th>Lương 1 giờ (7)</th>
{{--                                            <th>Hệ số</th>--}}
                                            <th>Tổng giờ tăng ca (8)</th>
                                            <th>Tăng ca 150% (9)</th>
                                            <th>Tăng ca 200% (10)</th>
                                            <th>Tăng ca 300% (11)</th>
                                            <th>
                                                <p>Thành tiền lương tăng ca (12)</p>
                                                <p>= </p>
                                            </th>
                                            <th>Tổng lương (13)</th>
                                        </tr>
                                        </thead>
                                        <tbody id="dataDetail"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="modalDetailAllowance" class="modal fade" tabindex="-1">
                        <div class="modal-dialog modal-full">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Chi tiết phụ cấp : <strong class="staff_name"></strong></h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body">
                                    <table class="table datatable-detail-allowance">
                                        <thead>
                                        <tr>
                                            <th>Tên phụ cấp</th>
                                            <th>Loại</th>
                                            <th>Giá trị</th>
                                        </tr>
                                        </thead>
                                        <tbody id="dataDetailAllowance"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="modalDetailInsurance" class="modal fade" tabindex="-1">
                        <div class="modal-dialog modal-full">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Chi tiết phụ cấp : <strong class="staff_name"></strong></h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body">
                                    <table class="table datatable-detail-insurance">
                                        <thead>
                                        <tr>
                                            <th>Tên khấu trừ</th>
                                            <th>Loại</th>
                                            <th>Giá trị</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                        </thead>
                                        <tbody id="dataDetailInsurance"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /full width modal -->

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/picker_date_init.js') }}"></script>
    <script>
        let json_data = {!! json_encode($data) !!};
        Number.prototype.format = function (n, x) {
            var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
            return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
        };

        function loadDetailStaff(id) {
            let dataTable = $('.datatable-detail').DataTable();
            $('#dataDetail').html('');
            dataTable.clear().draw();
            json_data.forEach(item => {
                if (item.staffId === id && item.details != null && item.details.length > 0) {
                    item.details.forEach(detail => {
                        dataTable.row.add([
                            // `${detail.contract.staff.firstname + ' ' + detail.contract.staff.lastname}`,
                            `${detail.day_detail}`,
                            `${detail.type_note}`,
                            `${detail.standard_days}`,
                            `${detail.contract.baseSalary.format()}`,
                            `${detail.salary_per_day.format()}`,
                            `${detail.total_working_of_day}`,
                            `${detail.total_salary.format()}`,
                            `${detail.salary_by_one_hour.format()}`,
                            // `${detail.multiply_day.format()}`,
                            `${detail.ot_hours}`,
                            `${detail.salary_of_ot_150.format()}`,
                            `${detail.salary_of_ot_200.format()}`,
                            `${detail.salary_of_ot_300.format()}`,
                            `${detail.total_salary_ot.format()}`,
                            `${(detail.total_salary + detail.total_salary_ot).format()}`,
                        ]).draw(false);
                    });
                    return;
                }
            });
        }

        function loadDetailAllowanceStaff(id) {
            let dataTable = $('.datatable-detail-allowance').DataTable();
            $('#dataDetailAllowance').html('');
            dataTable.clear().draw();
            json_data.forEach(item => {
                if (item.staffId === id && item.allowanceDetails != null && item.allowanceDetails.length > 0) {
                    $('.staff_name').html(item.staff.firstname + ' ' + item.staff.lastname);
                    let details = JSON.parse(item.allowanceDetails);
                    details.forEach(detail => {
                        dataTable.row.add([
                            `${detail.name}`,
                            `${detail.unit === 'PERCENT' ? '%' : 'VND'}`,
                            `${Number(detail.value).format()}`,
                        ]).draw(false);
                    });
                    return;
                }
            });
        }

        function loadDetailInsuranceStaff(id) {
            let dataTable = $('.datatable-detail-insurance').DataTable();
            $('#dataDetailInsurance').html('');
            dataTable.clear().draw();
            json_data.forEach(item => {
                if (item.staffId === id && item.insuranceDetails != null && item.insuranceDetails.length > 0) {
                    $('.staff_name').html(item.staff.firstname + ' ' + item.staff.lastname);
                    let details = JSON.parse(item.insuranceDetails);
                    details.forEach(detail => {
                        dataTable.row.add([
                            `${detail.name}`,
                            `${detail.unit === 'PERCENT' ? '%' : 'VND'}`,
                            `${Number(detail.value * 100).toFixed(1)}`,
                            `${Number(detail.value * item.baseSalaryContract).format()}`,
                        ]).draw(false);
                    });
                    return;
                }
            });
        }
    </script>
@endsection
