<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Http;

class SpecialDate implements FromCollection, WithHeadings
{
    protected $date;

    function __construct($date) {
        $this->date = $date;
    }

    public function collection()
    {          
        $data_request = ['special_date_from' => $this->date];

        $response = Http::get('http://localhost:8888/special-date/list-special-date?', $data_request);
        $body = json_decode($response->body(), true);

        $stt = 1;
        foreach ($body['data'] as $row) {
            if($row['type_day'] == 1) {
                $special_date[] = array(
                    '0' => $stt,
                    '1' => $row['day_special_from'],
                    '2' => $row['day_special_to'],
                    '3' => $row['note'],
                );
                $stt++;
            }            
        }

        return (collect($special_date));
    }

    public function headings(): array
    {
        $date = date_create($this->date);
        return [
            ['Danh sách ngày lễ năm ' . date_format($date, 'Y')],
            [
                'STT',
                'Ngày bắt đầu',
                'Ngày kết thúc',
                'Mô tả ngày lễ',
            ]
        ];
    }
}