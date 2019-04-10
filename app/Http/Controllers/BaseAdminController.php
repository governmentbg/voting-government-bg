<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseAdminController extends Controller
{
    protected $breadcrumbs;
    
    public function __construct()
    {
        auth()->shouldUse('backend');
    }
    
    protected function addBreadcrumb($label, $link = null)
    {
        $this->breadcrumbs[] = (object) [
          'label' => $label,
          'link'  => $link,
        ];
 
        view()->share('breadcrumbs', $this->breadcrumbs);//share data for all views
    }

    protected function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }
}
