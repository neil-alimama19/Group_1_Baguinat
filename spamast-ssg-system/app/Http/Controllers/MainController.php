<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function view(Request $request)
    {
        return redirect()->route($request->user()->dashboardRoute());
    }
}