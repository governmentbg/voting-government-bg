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
     * @param big integer data[reg_number] - optional
     * @param datetime data[reg_date] - optional
     * @param string data[name] - required
     * @param string data[city] - optional
     * @param string data[address] - optional
     * @param string data[representative] - optional
     * @param string data[phone] - optional
     * @param string data[status] - optional
     * @param datetime data[status_date] - optional
     * @param string data[email] - optional
     * @param string data[goals] - optional
     * @param string data[tools] - optional
     * @param string data[description] - optional
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
            'reg_number'      => 'nullable|digits_between:1,19',
            'reg_date'        => 'nullable',
            'name'            => 'required|string|max:255',
            'city'            => 'nullable|string|max:255',
            'address'         => 'nullable|string|max:512',
            'representative'  => 'nullable|string|max:512',
            'phone'           => 'nullable|string|max:40',
            'status'          => 'nullable|string|max:30',
            'status_date'     => 'nullable',
            'email'           => 'nullable|string',
            'goals'           => 'nullable|string',
            'tools'           => 'nullable|string',
            'description'     => 'nullable|string|max:8000',
            'public_benefits' => 'nullable|bool',
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
                    $fields = ['name', 'city', 'address', 'phone', 'email', 'status'];
                    if ($data['type'] != PredefinedOrganisation::PREDEFINED_LIST_TYPE) {
                        $fields[] = 'representative';
                        if ($data['type'] == TradeRegister::PREDEFINED_LIST_TYPE) {
                            $fields[] = 'public_benefits';
                        }
                    }
                } else {
                    $fields = '*';
                }

                $orgData = $model::select($fields)->where('eik', $data['eik'])->first();

                if ($orgData) {
                    if ($data['type'] == PredefinedOrganisation::PREDEFINED_LIST_TYPE) {
                        $orgData->public_benefits = 1;
                    }

                    if (isset($model::getStatuses()[$orgData->status])) {
                        $orgData->status_name = $model::getStatuses()[$orgData->status];
                    } else {
                        $orgData->status_name = null;
                    }

                    // add type in the result
                    $orgData->type = $data['type'];
                } else {
                    $orgData = new $model;
                }

                return $this->successResponse($orgData);
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.get_org_fail'), __('custom.internal_server_error'));
            }
        }

        return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
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
