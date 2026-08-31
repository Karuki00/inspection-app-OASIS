<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class InspectionReportController extends Controller
{
    public function index()
    {
        return view('admin.inspection-reports.index');
    }

    public function show($id)
    {
        return view('admin.inspection-reports.show', compact('id'));
    }
}
