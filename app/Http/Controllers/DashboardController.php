<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Upload;

class DashboardController extends Controller
{
    /**
     * Show dashboard page
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        return view('page.dashboard.index');
    }
}
