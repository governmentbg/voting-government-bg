<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\PredefinedList as ApiPredefinedList;

class PredefinedListController extends Controller
{
    public function readData(Request $request)
    {
        $eik = $request->get('eik', null);

        $result = [];
        if (isset($eik) && is_numeric($eik)) {
            $params = ['eik' => $eik, 'only_main_fields' => true, 'test' => ''];

            // get predefined list types
            list($types, $errors) = api_result(ApiPredefinedList::class, 'listTypes');

            if (is_array($types)) {
                foreach ($types as $type) {
                    $params['type'] = $type->id;

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
        }

        return json_encode($result);
    }
}
