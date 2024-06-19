<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ViewMenuController extends Controller
{
    public function index()
    {
        
        return view('main.view_menu.index');
    }

    public function timeLeave()
    {
        return view('main.view_menu.time_leave',[
            'breadcrumbs' => [
                ['text' => 'Công Phép', 'url' => '#']
            ]
        ]);
    }
    
    public function kpi()
    {
        return view('main.view_menu.kpi',[
            'breadcrumbs' => [
                ['text' => 'Kpi', 'url' => '#']
            ]
        ]);
    }

    public function department()
    {
        return view('main.view_menu.department',[
            'breadcrumbs' => [
                ['text' => 'Phòng ban', 'url' => '#']
            ]
        ]);
    }
    
    public function staff()
    {
        return view('main.view_menu.staff',[
            'breadcrumbs' => [
                ['text' => 'Nhân viên', 'url' => '#']
            ]
        ]);
    }

    public function contract()
    {
        return view('main.view_menu.contract',[
            'breadcrumbs' => [
                ['text' => 'Hợp đồng', 'url' => '#']
            ]
        ]);
    }
    
    public function salary()
    {
        return view('main.view_menu.salary',[
            'breadcrumbs' => [
                ['text' => 'Lương', 'url' => '#']
            ]
        ]);
    }

    public function education()
    {
        return view('main.view_menu.education',[
            'breadcrumbs' => [
                ['text' => 'Bằng cấp', 'url' => '#']
            ]
        ]);
    }
}
