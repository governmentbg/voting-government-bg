<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $breadcrumbs;

    protected function addBreadcrumb($label, $link = null)
    {
        $this->breadcrumbs[] = (object) [
          'label' => $label,
          'link'  => $link,
        ];

        view()->share('breadcrumbs', $this->breadcrumbs); // share data for all views
    }

    protected function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    public function paginate($object)
    {
        if (empty($object)) {
            $object->data = [];
            $object->total = 0;
            $object->per_page = 15;
            $object->current_page = 1;
            $object->path = '/';
        }

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $object->data,
            $object->total,
            $object->per_page,
            $object->current_page,
            ['path' => $object->path]
        );

        return $paginator;
    }
}
