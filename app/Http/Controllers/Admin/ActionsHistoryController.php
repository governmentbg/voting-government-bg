<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\ActionsHistoryController as ApiHistory;
use App\Http\Controllers\Api\VotingTourController as ApiTour;

class ActionsHistoryController extends BaseAdminController
{
    public function list(Request $request)
    {
        $module = $request->offsetGet('module');
        $username = $request->offsetGet('username');
        $votingTourId = $request->offsetGet('voting_tour_id', null);
        $tour = $request->offsetGet('id');
        $action = $request->offsetGet('action');
        $ipAddress = $request->offsetGet('ip_address');
        $periodFrom = $request->offsetGet('period_from');
        $periodTo = $request->offsetGet('period_to');

        $votingTourData = isset($votingTourId) ? \App\VotingTour::where('id', $votingTourId)->first() : __('custom.no_tour');
        $name = !empty($votingTourData->name) ? $votingTourData->name : __('custom.no_tour');
        $this->addBreadcrumb(__('breadcrumbs.start'), route('admin.org_list'));
        $this->addBreadcrumb(__('breadcrumbs.settings'), route('admin.settings'));
        $this->addBreadcrumb(__('breadcrumbs.voting_tours'), route('admin.voting_tour.list'));
        $this->addBreadcrumb($name .' - '. __('custom.actions_history'), '');

        $allFilters = [];

        list($toursList, $tourErrors) = api_result(ApiTour::class, 'list');

        $toursList = !empty($toursList) ? $toursList : null;

        if (isset($module) && $module != 'all') {
            $allFilters['module'] = (int) $module;
        }

        if (isset($action) && $action != 'all') {
            $allFilters['action'] = (int) $action;
        }

        if (isset($username) && $username != '') {
            $allFilters['username'] = $username;
        }

        if (isset($ipAddress) && $ipAddress != '') {
            $allFilters['ip_address'] = $ipAddress;
        }

        if (isset($periodFrom)) {
            $allFilters['period_from'] = $periodFrom;
        }

        if (isset($periodTo)) {
            $allFilters['period_to'] = $periodTo;
        }

        $allFilters['voting_tour_id'] = isset($votingTourId) ? $votingTourId : null;

        // apply sort parameters
        if ($request->has('sort')) {
            $allFilters['order_field'] = $request->sort;
        } else {
            $allFilters['order_field'] = 'occurrence';
        }

        if ($request->has('order')) {
            $allFilters['order_type'] = $request->order;
        } else {
            $allFilters['order_type'] = 'desc';
        }

        $allFilters['page_number'] = $request->page;

        list($actionsHistory, $errors) = api_result(ApiHistory::class, 'search', [
            'filters'         => $allFilters,
        ]);

        list($modules, $modulesErrors) = api_result(ApiHistory::class, 'listModules');
        $modules = !empty($modules) ? collect($modules)->pluck('name', 'id')->toArray() : [];

        list($actions, $actionErrors) = api_result(ApiHistory::class, 'listActions');
        $actions = !empty($actions) ? collect($actions)->pluck('name', 'id')->toArray() : [];

        if (!empty($errors)) {
            $request->session()->flash('alert-danger', __('custom.error_getting_actions_history'));
        } else {
            $actionsHistory = !empty($actionsHistory->data) ? $this->paginate($actionsHistory) : [];
        }

        return view('admin.actions_history', [
            'historyList' => $actionsHistory,
            'modules'     => $modules,
            'actions'     => $actions,
            'filters'     => $allFilters,
            'toursList'   => $toursList
        ]);
    }
}
