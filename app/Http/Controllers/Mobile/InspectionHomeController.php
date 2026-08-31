<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;

class InspectionHomeController extends Controller
{
    public function index()
    {
        return view('mobile.inspection-home');
    }
}
