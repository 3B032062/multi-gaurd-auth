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
    // AuthenticatedSessionController(用於一般Web使用者鄧入登出，用於web guard)
    // 本控制器作用與AuthenticatedSessionController幾乎一樣， 除登出部分，可相互比較。

    // admin登入的動作屬於CRUD當中C(Create)的動作，所以，先由admin.login路由[get(/admin/login)]，
    // 串接本控制器的login()的方法，以產生登入表單讓登入者輸入email & password，然後，資料上傳另一路由
    // post(/admin/login)，並串接本控制器的processLogin()的方法處理
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
        $credentials = $request->except(['_token']);   //去除_token，只留email & password
        //$credentials = ['email' => $request->email, 'password' => $request->password];

       //dd($credentials);
//
        if($this->isAdminActive($request->email))     //先用isAdminActive()檢查帳號是否有作用
        {
            // 使用admin guard驗證使用者身分，
            // 若成功，讓使用者登入，並重新產生會談(session)，然後轉向至/admin/dashboard
            //
//            dd(Auth::guard('admin')->attempt($credentials));
            if(Auth::guard('admin')->attempt($credentials))
            {
                //dd(AUTH::guard('admin')->check());
                //dd(Auth::guard('admin')->user());
                $request->session()->regenerate();
                //dd($request->session());

                //dd(RouteServiceProvider::ADMIN);
                return redirect(RouteServiceProvider::ADMIN);
//                return redirect('/');
            }
            // 若admin使用者身分驗證失敗，則重新進入本控制器的login()方法，並給帳號資料錯誤訊息
            return redirect()->action([
                LoginController::class,
                'login'
            ])->with('message','Credentials not matched in our records!');
        }
        // 若admin使用者身帳號失效，則重新進入本控制器的login()方法，並給帳號失效訊息
        return redirect()->action([
            LoginController::class,
            'login'
        ])->with('message','You are not an active admin!');
    }

    function isAdminActive($email) : bool
    {
        $admin= Admin::whereEmail($email)->isActive()->exists();

        return $admin ? true : false;
    }
}
