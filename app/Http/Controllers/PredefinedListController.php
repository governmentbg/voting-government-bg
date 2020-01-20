<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\PredefinedListController as ApiPredefinedList;
use App\BulstatRegister;
use App\PredefinedOrganisation;
use App\TradeRegister;

class PredefinedListController extends Controller
{
    public function readData(Request $request)
    {
        $eik = $request->get('eik', null);

        $result = [];
        if (isset($eik) && is_numeric($eik)) {
            // predefined list types ordered by priority
            $types = [
                BulstatRegister::PREDEFINED_LIST_TYPE,
                PredefinedOrganisation::PREDEFINED_LIST_TYPE,
                TradeRegister::PREDEFINED_LIST_TYPE
            ];

            $params = ['eik' => $eik, 'only_main_fields' => true];

            foreach ($types as $type) {
                $params['type'] = $type;

                // get predefined list data
                list($data, $errors) = api_result(ApiPredefinedList::class, 'getData', $params);

                if (!empty($data)) {
                    $result['data'] = (array) $data;
                    if (trim($data->city) != '') {
                        $result['data']['fullAddress'] = $data->city . (trim($data->address) != '' ? ', '. $data->address : '');
                    } else {
                        $result['data']['fullAddress'] = $data->address;
                    }
                    break;
                }
            }
        }

        return json_encode($result);
    }
}
