<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PredefinedOrganisation;

class PredefinedOrganisationController extends Controller
{
    public function readData(Request $request)
    {
        $eik = $request->get('eik', null);

        $result = [];
        if (isset($eik) && is_numeric($eik)) {
            $data = PredefinedOrganisation::getData($eik)->first();
            if (!empty($data)) {
                $result['data'] = $data;
                $result['data']['fullAddress'] = $data->fullAddress;
            }
        }

        return json_encode($result);
    }
}
