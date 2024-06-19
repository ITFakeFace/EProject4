<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TimeLeave implements FromCollection, WithHeadings
{
    protected $date;

    function __construct($date) {
        $this->date = $date;
    }

    public function collection()
    {      
        $data_request = ['month_get' => $this->date];
        $response = Http::get('http://localhost:8888/time-leave/all-detail-time-leave-export', $data_request);
        $all_time_leave = json_decode($response->body(), true);

        $stt = 1;
        foreach ($all_time_leave['data'] as $row) {
            $day_of_week = '';
            $row['special_date_id'] !== null ? $day_of_week .= "(Ngày lễ)" : '';

            $row['day_of_week'] !== 1 ? $day_of_week = "Thứ " . $row['day_of_week'] : $day_of_week = "Chủ Nhật";
            $row['day_of_week'] == null ? $day_of_week = "" : $day_of_week = $day_of_week;
            
            switch ($row['type']) {
                case '1':
                    $type = "Đăng kí phép (Phép năm tính lương)";
                    break;
                case '2':
                    $type = "Đăng kí phép (Nghỉ không lương)";
                    break;
                case '3':
                    $type = "Đăng kí phép (Nghỉ ốm đau ngắn ngày)";
                    break;
                case '4':
                    $type = "Đăng kí phép (Nghỉ ốm dài ngày)";
                    break;
                case '5':
                    $type = "Đăng kí phép (Nghỉ thai sản)";
                    break;
                case '6':
                    $type = "Đăng kí phép (Nghỉ kết hôn)";
                    break;
                case '7':
                    $type = "Đăng kí phép (Nghỉ ma chay)";
                    break;
                default:
                    $type = "Bổ sung công";
                    break;
            }
            $row['time'] == "08:00:00" ? $time = '1' : $time = '0.5';
            $row['time'] == null ? $time = '' : $time = $time;

            $row['time'] == "08:00:00" ? $time_multi = 1 * $row['multiply'] . '' : $time_multi = 0.5 * $row['multiply'];
            $row['time'] == null ? $time_multi = '' : $time_multi = $time_multi;

            if($row['is_approved'] == 0)
                $approve = 'Chưa phê duyệt';
            elseif($row['is_approved'] == 2)
                $approve = 'Quản lý đã phê duyệt';
            else
                $approve = 'Giám đốc đã phê duyệt';

            $time_leave[] = array(
                '0' => $stt,
                '1' => $row['firstname'] . ' ' . $row['lastname'],
                '2' => $row['name_vn'],
                '3' => $row['is_manager'] == 1 ? 'Quản lý' : 'Nhân viên',
                '4' => $day_of_week,
                '5' => $row['day_time_leave'],
                '6' => $type,
                '7' => $time,
                '8' => $time_multi,
                '9' => $row['note'],
                '10' => $approve,
            );
            $stt++;
        }

        return (collect($time_leave));
    }

    public function headings(): array
    {
        $date = date_create($this->date);
        return [
            ['Danh sách bổ sung công / đăng kí phép của nhân viên tháng ' . date_format($date, 'm/Y')],
            [
                'STT',
                'Họ và tên',
                'Phòng ban',
                'Chức vụ',
                'Thứ',
                'Ngày',
                'Loại',
                'Ngày công',
                'Ngày công được tính',
                'Lý do',
                'Trạng thái phê duyệt',
            ]
        ];
    }
}