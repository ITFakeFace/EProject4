<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StaffPayrollExport implements FromView
{

    private $dataSalaryDetail;
    private $data_department;

    public function __construct($dataSalaryDetail, $data_department)
    {
        $this->dataSalaryDetail = $dataSalaryDetail;
        $this->data_department = $data_department;
    }

    public function view(): View
    {
        return view('main.salary.exports.payroll_personal', [
            'dataSalaryDetail' => $this->dataSalaryDetail,
            'data_department' => $this->data_department,
        ]);
    }
}
