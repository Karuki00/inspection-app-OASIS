<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class FacilityController extends Controller
{
    public function index()
    {
        return view('admin.facilities.index');
    }

    public function create()
    {
        return view('admin.facilities.create');
    }
}
