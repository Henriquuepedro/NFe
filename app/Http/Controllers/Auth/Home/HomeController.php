<?php

namespace App\Http\Controllers\Auth\Home;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('admin.home.index');
    }
}
