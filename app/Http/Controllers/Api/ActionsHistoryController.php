<?php

namespace App\Http\Controllers\Api;

use App\ActionsHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ActionsHistoryController extends ApiController
{
    /**
     * List history records based on supplied filter
     *
     * @param array filters - optional
     * @param date filters['period_from'] - optional
     * @param date filters['period_to'] - optional
     * @param string filters['username'] - optional
     * @param integer filters['user_id'] - optional
     * @param integer filters['module'] - optional
     * @param integer filters['action'] - optional
     * @param integer filters['object'] - optional
     * @param string filters['ip_address'] - optional
     * @param string filters['tour_id'] - optional
     * @param string order_field - optional
     * @param string order_type - optional
     * @param integer page_number - optional
     *
     * @return json with history data
     */
    public function search(Request $request)
    {
        $rules = [
            'filters'         => 'nullable|array',
            'order_field'     => 'nullable|string|in:'. implode(',', ActionsHistory::ALLOWED_ORDER_FIELDS),
            'order_type'      => 'nullable|string|in:'. implode(',', ActionsHistory::ALLOWED_ORDER_TYPES),
            'page_number'     => 'nullable|int|min:1',
        ];

        $data = $request->only(array_keys($rules));
        $data['order_type'] = isset($data['order_type']) ? strtoupper($data['order_type']) : null;

        $validator = \Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        $filters = isset($data['filters']) ? $data['filters'] : [];

        $validator = \Validator::make($filters, [
            'period_from'       => 'nullable|date',
            'period_to'         => 'nullable|date',
            'username'          => 'nullable|string',
            'user_id'           => 'nullable|int|exists:users,id',
            'module'            => 'nullable|int',
            'action'            => 'nullable|int',
            'object'            => 'nullable|int',
            'ip_address'        => 'nullable|string',
            'tour_id'           => 'nullable|int|exists:voting_tour,id',
        ]);

        if (!$validator->fails()) {
            $orderField = isset($data['order_field']) ? $data['order_field'] : ActionsHistory::DEFAULT_ORDER_FIELD;
            $orderType = isset($data['order_type']) ? $data['order_type'] : ActionsHistory::DEFAULT_ORDER_TYPE;
            $page = isset($data['page_number']) ? $data['page_number'] : null;

            try {
                $query = ActionsHistory::select('actions_history.*');

                if (isset($filters['username']) || $orderField == 'username') {
                    $query->join('users', 'users.id', '=', 'actions_history.user_id');

                    if (!empty($filters['username'])) {
                        $query->where('users.username', $filters['username']);
                    }
                }

                if (isset($filters['period_from'])) {
                    $query->where('occurrence', '>=', $filters['period_from'] .' 00:00:00');
                }

                if (isset($filters['period_to'])) {
                    $query->where('occurrence', '<=', $filters['period_to'] .' 23:59:59');
                }

                if (isset($filters['user_id'])) {
                    $query->where('user_id', $filters['user_id']);
                }

                if (isset($filters['module'])) {
                    $query->where('module', $filters['module']);
                }

                if (isset($filters['action'])) {
                    $query->where('action', $filters['action']);
                }

                if (isset($filters['object'])) {
                    $query->where('object', $filters['object']);
                }

                if (isset($filters['ip_address'])) {
                    $query->where('ip_address', $filters['ip_address']);
                }

                if (empty($filters['tour_id'])) {
                    $query->whereNull('actions_history.voting_tour_id');
                } else {
                    $query->where('actions_history.voting_tour_id', $filters['tour_id']);
                }

                $query->orderBy($orderField, $orderType);

                if ($orderField != 'id') {
                    $query->orderBy('id', $orderType);
                }

                $request->request->add(['page' => $page]);

                return $this->successResponse($query->paginate());
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.error_getting_actions_history'), __('custom.internal_server_error'));
            }
        }

        return $this->errorResponse(__('custom.error_getting_actions_history'), $validator->errors()->messages());
    }

    /**
     * Return a list of available modules
     *
     * @return json with modules and ids
     */
    public function listModules()
    {
        $modules = ActionsHistory::getModules();
        $modulesArray = [];

        if ($modules) {
            foreach ($modules as $moduleId => $moduleName) {
                $modulesArray[] = ['id' => $moduleId, 'name' => $moduleName];
            }

            return $this->successResponse($modulesArray);
        } else {
            return $this->errorResponse(__('custom.error_getting_modules'));
        }
    }

    /**
     * Return a list of available actions
     *
     * @return json with actions and ids
     */
    public function listActions()
    {
        $actions = ActionsHistory::getActions();
        $actionsArray = [];

        if ($actions) {
            foreach ($actions as $actionId => $actionName) {
                $actionsArray[] = ['id' => $actionId, 'name' => $actionName];
            }

            return $this->successResponse($actionsArray);
        } else {
            return $this->errorResponse(__('custom.error_getting_actions'));
        }

    }
}
