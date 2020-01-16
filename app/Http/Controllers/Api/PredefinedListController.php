<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\ApiController;
use App\PredefinedOrganisation;
use App\TradeRegister;
use App\BulstatRegister;

class PredefinedListController extends ApiController
{
    /**
     * Update predefined list record
     *
     * @param integer type - required
     * @param array data - required
     * @param big integer data[eik] - required
     * @param big integer data[reg_number] - required
     * @param datetime data[reg_date] - required
     * @param string data[name] - required
     * @param string data[city] - required
     * @param string data[address] - required
     * @param string data[phone] - required
     * @param string data[status] - required
     * @param datetime data[status_date] - optional
     * @param string data[email] - required
     * @param string data[goals] - required
     * @param string data[tools] - required
     * @param string data[description] - required
     * @param bool data[public_benefits] - optional
     *
     * @return json - response with status code and success or errors
     */
    public function update(Request $request)
    {
        $type = $request->get('type', null);
        $data = $request->get('data', []);

        $validator = \Validator::make(['type' => $type, 'data' => $data], [
            'type' => 'required|int|in:'. implode(',', $this->getEditableTypes()),
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.update_predefined_list_fail'), $validator->errors()->messages());
        }

        $rules = [
            'eik'             => 'required|digits_between:1,19',
            'reg_number'      => 'required|digits_between:1,19',
            'reg_date'        => 'required',
            'name'            => 'required|string|max:255',
            'city'            => 'required|string|max:255',
            'address'         => 'required|string|max:512',
            'phone'           => 'required|string|max:40',
            'status'          => 'required|string|max:30',
            'status_date'     => 'nullable',
            'email'           => 'string|min:0|email',
            'goals'           => 'string|min:0|max:8000',
            'tools'           => 'string|min:0|max:8000',
            'description'     => 'string|min:0|max:8000',
            'public_benefits' => 'required|bool',
        ];

        $data = Arr::only($data, array_keys($rules));

        if (!(isset($data['reg_date']) && $data['reg_date'] == '0000-00-00 00:00:00')) {
            $rules['reg_date'] .= '|date_format:Y-m-d H:i:s';
        }
        if (!(isset($data['status_date']) && $data['status_date'] == '0000-00-00 00:00:00')) {
            $rules['status_date'] .= '|date_format:Y-m-d H:i:s';
        }

        $validator = \Validator::make($data, $rules);

        if (!$validator->fails()) {
            try {
                $model = $this->getModelByType($type);

                $newData = $model::updateOrCreate(['eik' => $data['eik']], $data);

                return $this->successResponse();
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.update_predefined_list_fail'), __('custom.internal_server_error'));
            }
        }

        return $this->errorResponse(__('custom.update_predefined_list_fail'), $validator->errors()->messages());
    }

    /**
     * Get predefined list data
     *
     * @param integer type - required
     * @param big integer eik - required
     * @param boolean only_main_fields - optional
     *
     * @return json - response with status code and data or errors
     */
    public function getData(Request $request)
    {
        $rules = [
            'type'             => 'required|int|in:'. implode(',', array_keys($this->getTypes())),
            'eik'              => 'required|digits_between:1,19',
            'only_main_fields' => 'nullable|bool',
        ];

        $data = $request->only(array_keys($rules));

        $validator = \Validator::make($data, $rules);

        if (!$validator->fails()) {
            try {
                $model = $this->getModelByType($data['type']);

                if (isset($data['only_main_fields']) && $data['only_main_fields']) {
                    $fields = ['name', 'city', 'address', 'phone', 'email'];
                } else {
                    $fields = '*';
                }

                $orgData = $model::select($fields)->where('eik', $data['eik'])->first();

                if ($orgData) {
                    // add type in the result
                    $orgData->type = $data['type'];

                    return $this->successResponse($orgData);
                }
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.get_org_fail'), __('custom.internal_server_error'));
            }
        }

        return $this->errorResponse(__('custom.predefined_list_org_not_found'), $validator->errors()->messages());
    }

    /**
     * List types
     *
     * @param none
     *
     * @return json - response with status code and list of types or errors
     */
    public function listTypes(Request $request)
    {
        $results = [];

        $types = $this->getTypes();

        foreach ($types as $typeId => $typeName) {
            $results[] = [
                'id'   => $typeId,
                'name' => $typeName,
            ];
        }

        if ($results) {
            return $this->successResponse($results);
        } else {
            return $this->errorResponse('custom.type_list_not_found');
        }
    }

    private function getTypes()
    {
        $types = BulstatRegister::getType() + TradeRegister::getType() + PredefinedOrganisation::getType();

        return $types;
    }

    private function getEditableTypes()
    {
        return [
            BulstatRegister::PREDEFINED_LIST_TYPE,
            TradeRegister::PREDEFINED_LIST_TYPE,
        ];
    }

    private function getModelByType($type)
    {
        if ($type == BulstatRegister::PREDEFINED_LIST_TYPE) {
            $model = BulstatRegister::class;
        } elseif ($type == TradeRegister::PREDEFINED_LIST_TYPE) {
            $model = TradeRegister::class;
        } else {
            $model = PredefinedOrganisation::class;
        }

        return $model;
    }
}
