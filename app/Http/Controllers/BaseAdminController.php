<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseAdminController extends Controller
{
    public function __construct()
    {
        auth()->shouldUse('backend');
    }
}
