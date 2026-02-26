<?php

namespace App\Http\Controllers;

class UserLogController extends Controller
{
    public function index()
    {
        return view('user-logs.index');
    }
}

