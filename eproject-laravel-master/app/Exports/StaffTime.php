<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StaffTime implements FromCollection, WithHeadings
{
    protected $date;

    function __construct($date) {
        $this->date = $date;
    }

    public function collection()
    {
        $data_request = ['y_m' => $this->date];
        $response = Http::get('http://localhost:8888/time-leave/get-all-staff-time', $data_request);
        $body = json_decode($response->body(), true);

        $stt = 1;
        foreach ($body['data'] as $row) {
            $day_of_week = '';
            $row['special_date_id'] !== null ? $day_of_week .= "(Ngày lễ)" : '';
            $check_in_out[] = array(
                '0' => $stt,
                '1' => $row['full_name'],
                '2' => $row['department_name'],
                '3' => $row['is_manager'] == 1 ? 'Quản lý' : 'Nhân viên',
                '4' => $row['day_of_week'] !== 1 ? "Thứ " . $row['day_of_week'] . $day_of_week : "Chủ Nhật" . $day_of_week,
                '5' => $row['check_in_day'],
                '6' => $row['check_in'],
                '7' => $row['check_out'],
                '8' => $row['in_late'],
                '9' => $row['out_soon'],
                '10' => $row['number_time'] * $row['multiply'],
                '11' => $row['time'],
                '12' => $row['ot']
            );
            $stt++;
        }

        return (collect($check_in_out));
    }

    public function headings(): array
    {
        $date = date_create($this->date);
        return [
            ['Danh sách chấm công của nhân viên tháng ' . date_format($date, 'm/Y')],
            [
                'STT',
                'Họ và tên',
                'Phòng ban',
                'Chức vụ',
                'Thứ',
                'Ngày',
                'Giờ vào',
                'Giờ ra',
                'Đi trễ',
                'Về sớm',
                'Công',
                'Tổng giờ làm việc',
                'Tăng ca'
            ]
        ];
    }
}