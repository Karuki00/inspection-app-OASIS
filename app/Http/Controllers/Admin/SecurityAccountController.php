<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SecurityAccountController extends Controller
{
    public function index()
    {
        return view('admin.security-accounts.index');
    }

    public function create()
    {
        return view('admin.security-accounts.create');
    }
}
