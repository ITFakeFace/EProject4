<?php

namespace App\Imports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PayrollImport implements FromView
{
    public $dataSalaryDetail = [];
    public $dataDepartment = [];

    public function __construct($dataSalaryDetail, $dataDepartment)
    {
        $this->dataSalaryDetail = $dataSalaryDetail;
        $this->dataDepartment = $dataDepartment;
    }

    public function view(): View
    {
        return view('main.salary.exports.payroll', [
            'data' => $this->dataSalaryDetail,
            'data_department' => $this->dataDepartment
        ]);
    }
}
