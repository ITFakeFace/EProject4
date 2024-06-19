<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class AboutCompanyController extends Controller
{
    public function index()
    {
        return view('main.about_company.index')
                ->with('breadcrumbs', [['text' => 'Giới thiệu', 'url' => '#']]);;
    }

}
