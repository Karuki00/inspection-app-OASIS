<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ProblemController extends Controller
{
    public function index()
    {
        return view('admin.problem-statuses.index');
    }
}
