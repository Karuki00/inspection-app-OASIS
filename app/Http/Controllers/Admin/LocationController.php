<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function index()
    {
        return view('admin.locations.index');
    }

    public function show($id)
    {
        return view('admin.locations.show', compact('id'));
    }
}
