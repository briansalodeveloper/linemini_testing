<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\AdminService;
use App\Helpers\Upload;
use App\Models\Admin;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Show user page
     *
     * @param AdminService $adminService
     * @return View
     */
    public function index(AdminService $adminService): View
    {
        $admins = $adminService->getPaginate(10);

        return view('page.admin.index', compact('admins'));
    }

    /**
     * Show edit page
     *
     * @return View
     */
    public function edit(AdminService $adminService, Admin $admin): View
    {
        return view('page.admin.detail', compact('admin'));
    }
}
