<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RedirectsUsers;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    protected $loginView = 'extranet.pages.login';

    public function showLoginForm()
    {
        return view($this->loginView);
    }

    protected $guard = 'backend';
    
    //path to redirect to after login
    protected $redirectTo = null;
    
    /**
     * The maximum number of attempts to allow.
     * 
     * @var int 
     */
    protected $maxAttempts = 5;
    
    /**
     * The number of minutes to throttle for.
     * 
     * @var int 
     */
    protected $decayMinutes = 15;

    public function __construct()
    {
        $this->middleware('guest:backend', ['except' => 'logout']);
        $this->redirectTo = route('extranet.home');
    }

    protected function guard()
    {
        return Auth::guard($this->guard);
    }

    /**
     * Attempt to log the user into the extranet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

       // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
    
    public function username()
    {
        return 'username';
    }
    
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }
}