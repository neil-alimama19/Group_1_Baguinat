<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboards.admin');
    }

    public function officer()
    {
        return view('dashboards.officer');
    }

    public function adviser()
    {
        return view('dashboards.adviser');
    }

    public function student()
    {
        return view('dashboards.student');
    }
}