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
     * @param int filters['user_id'] - optional
     * @param int filters['module'] - optional
     * @param int filters['action'] - optional
     * @param int filters['object'] - optional
     * @param string filters['ip_address'] - optional
     * @param integer filters['page_number'] - optional
     * @param string filters['order_type'] - optional
     * @param string filters['order_field'] - optional
     *
     * @return json with history data
     */
    public function search(Request $request)
    {
        $post = $request->all();
        $order = [];
        $allowedOrderFields = ['occurrence', 'module', 'username', 'action', 'ip_address', 'user_id', 'voting_tour_id', 'object'];

        $validator = \Validator::make($post, [
            'filters'     => 'nullable|array',
        ]);

        if (!$validator->fails()) {
            $filters = isset($post['filters']) ? $post['filters'] : [];

            $validator = \Validator::make($filters, [
                'period_from'       => 'nullable|date',
                'period_to'         => 'nullable|date',
                'username'          => 'nullable|string',
                'user_id'           => 'nullable|int|exists:users,id',
                'module'            => 'nullable|int',
                'action'            => 'nullable|int',
                'object'            => 'nullable|int',
                'ip_address'        => 'nullable|string',
                'voting_tour_id'    => 'nullable|int|exists:voting_tour,id',
                'page_number'       => 'nullable|integer',
                'order_type'        => 'nullable|string',
                'order_field'       => 'nullable|string|in:' . implode(',', $allowedOrderFields),
            ]);
        }

        $filters['order_type'] = !empty($filters['order_type']) ? $filters['order_type'] : 'desc';
        $filters['order_field'] = !empty($filters['order_field']) ? $filters['order_field'] : 'id';

        if (!$validator->fails()) {
            $query = ActionsHistory::select('actions_history.*');

            if (isset($filters['username']) || $filters['order_field'] == 'username') {
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

            if (isset($filters['voting_tour_id'])) {
                $query->where('actions_history.voting_tour_id', $filters['voting_tour_id']);
            }

            $query->orderBy($filters['order_field'], $filters['order_type']);
            if ($filters['order_field'] != 'id') {
                $query->orderBy('id', $filters['order_type']);
            }

            if (isset($filters['page_number'])) {
                $request->request->add(['page' => $filters['page_number']]);
            } else {
                $request->request->add(['page' => 1]);
            }

            try {
                return $this->successResponse($query->paginate());
            } catch (Exception $ex) {
                logger()->error($ex->getMessage());
                return $this->errorResponse(__('custom.error_getting_actions_history'));
            }
        } else {
            return $this->errorResponse(__('custom.error_getting_actions_history'), $validator->errors());
        }
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
