<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;

class AuthController extends Controller
{
    /**
     * Login page (show form)
     *
     * @return Response
     */
    public function index()
    {
        return view('page.auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $rules = [
            'uid' => 'required',
            'password' => 'required',
        ];

        $request->validate($rules);

        $uid = $request->get('uid');
        $username = $uid;
        $email = $uid;
        $password = $request->get('password');

        if (\Auth::attempt(compact('username', 'password')) || \Auth::attempt(compact('email', 'password'))) {
            $user = \Auth::user();
            \Auth::logoutOtherDevices($password);

            return redirect(route('dashboard.index'));
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors(['password' => __('messages.failed.login')]);
    }

    /**
     * Handle logout event
     *
     * @return Response
     */
    public function logout()
    {
        \Session::flush();
        \Auth::logout();

        return redirect()->route('home');
    }
}
