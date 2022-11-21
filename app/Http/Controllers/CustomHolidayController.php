<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomHolidayController extends Controller
{
    public function index()
    {
        return view('custom_holiday');
    }
}
