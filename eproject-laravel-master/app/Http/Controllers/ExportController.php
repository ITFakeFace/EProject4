<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Exports\StaffTime;
use App\Exports\TimeLeave;
use App\Exports\SpecialDate;

class ExportController extends Controller
{
   
    public function exportStaffTime(Request $request){
        $date = $request->input('y_m');

        return Excel::download(new StaffTime($date), 'staff_time_'.date("H-i-s d-m-Y").'.xlsx'); //download file export
   }

    public function exportTimeLeave(Request $request){
        $date = $request->input('y_m');

        return Excel::download(new TimeLeave($date), 'time_leave_'.date("H-i-s d-m-Y").'.xlsx'); //download file export
    }

    public function exportSpecialDate(Request $request){
        $year = $request->input('y');
        $month = date("m");
        if(!$year) {
            $year = date("Y");
        }

        $date = $year . '-' . $month . '-' . '01';

        return Excel::download(new SpecialDate($date), 'special_date_'.date("H-i-s d-m-Y").'.xlsx'); //download file export
    }
}
