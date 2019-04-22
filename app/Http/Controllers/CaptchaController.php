<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function create()
    {
        return view('captchacreate');
    }

    public function refreshCaptcha()
    {
        return captcha_img();
    }
}
