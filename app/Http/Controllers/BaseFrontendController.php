<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseFrontendController extends Controller
{
    public function __construct()
    {
        $this->setVotingTourData();
    }
}
