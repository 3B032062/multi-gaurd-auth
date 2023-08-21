<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
//use Facades\App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function login()
    {
        if(View::exists('admin.auth.login'))
        {
//            dd('login-in');
            return view('admin.auth.login');
        }
        abort(Response::HTTP_NOT_FOUND);
    }

    public function processLogin(Request $request)
    {
        $credentials = $request->except(['_token']);
        //$credentials = ['email' => $request->email, 'password' => $request->password];

       //dd($credentials);
//
        if($this->isAdminActive($request->email))
        {
//            dd(Auth::guard('admin')->attempt($credentials));
            if(Auth::guard('admin')->attempt($credentials))
            {
//                dd('PASS');
                $request->session()->regenerate();
                //dd($request->session());
                //dd(Auth::guard('admin')->user());
                //dd(RouteServiceProvider::ADMIN);
                return redirect(RouteServiceProvider::ADMIN);
//                return redirect('/');
            }
            return redirect()->action([
                LoginController::class,
                'login'
            ])->with('message','Credentials not matched in our records!');
        }
        return redirect()->action([
            LoginController::class,
            'login'
        ])->with('message','You are not an active doctors!');
    }

    function isAdminActive($email) : bool
    {
        $admin= Admin::whereEmail($email)->isActive()->exists();

        return $admin ? true : false;
    }
}
