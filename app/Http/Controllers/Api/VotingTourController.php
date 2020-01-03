<?php

namespace App\Http\Controllers\Api;

use \Validator;
use App\VotingTour;
use App\ActionsHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;
use App\Http\Controllers\ApiController;

class VotingTourController extends ApiController
{
    /**
     * Add a voting tour
     *
     * @param string name - required
     *
     * @return json with tour id on success or error
     */
    public function add(Request $request)
    {
        $post = $request->all();
        $countActive = 0;

        $validator = Validator::make($post, [
            'name'    => 'required|string'
        ]);

        if (!$validator->fails()) {
            $allVotingTourStatuses = VotingTour::select('id', 'status')->get();

            foreach ($allVotingTourStatuses as $singleTourStatus) {
                if ($singleTourStatus->status != VotingTour::STATUS_FINISHED) {
                    $countActive += 1;
                }
            }

            if ($countActive >= 1) {
                return $this->errorResponse(__('custom.active_tour_exists'));
            }

            try {
                DB::beginTransaction();

                $saved = VotingTour::create([
                    'name'   => $post['name'],
                    'status' => VotingTour::STATUS_UPCOMING
                ]);

                DB::commit();
            } catch (QueryException $e) {
                DB::rollback();
                Log::error($e->getMessage());
            }

            if (isset($saved)) {
                if (\Auth::user()) {
                    $logData = [
                        'module' => ActionsHistory::VOTING_TOURS,
                        'action' => ActionsHistory::TYPE_ADD,
                        'object' => $saved->id
                    ];

                    ActionsHistory::add($logData);
                }

                return $this->successResponse(['id' => $saved->id], true);
            }
        }

        return $this->errorResponse(__('custom.error_adding_tour'), $validator->errors()->messages());
    }

    /**
     * Change the voting tour status for the active tour
     *
     * @param integer new_status - required
     *
     * @return true on success, false on failure
     */
    public function changeStatus(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'new_status'    => 'required|int|in:'. implode(',', array_keys(VotingTour::getStatuses())),
        ]);

        if (!$validator->fails()) {
            $editVotingTour = VotingTour::getLatestTour();

            if ($editVotingTour) {
                if ($editVotingTour->status + VotingTour::STATUS_STEP < $post['new_status']) {
                    if (!($post['new_status'] == VotingTour::STATUS_FINISHED)) {
                        return $this->errorResponse(__('custom.status_skipping'));
                    }
                }

                if ($editVotingTour->status > $post['new_status']) {
                    if (!($editVotingTour->status == VotingTour::STATUS_BALLOTAGE && $post['new_status'] == VotingTour::STATUS_VOTING)
                        &&
                        !($editVotingTour->status == VotingTour::STATUS_BALLOTAGE && $post['new_status'] == VotingTour::STATUS_RANKING)
                    ) {
                        return $this->errorResponse(__('custom.backward_status'));
                    }
                }

                if ($editVotingTour->status != $post['new_status'] && array_key_exists($post['new_status'], VotingTour::getActiveStatuses())) {
                    // clear cached max votes
                    $cacheKey = VotingTour::getCacheKey($editVotingTour->id, 'max-votes');
                    if (Cache::has($cacheKey)) {
                        Cache::forget($cacheKey);
                    }
                }

                try {
                    DB::beginTransaction();

                    $editVotingTour->status = $post['new_status'];

                    $changed = $editVotingTour->save();

                    DB::commit();
                } catch (QueryException $e) {
                    DB::rollback();
                    Log::error($e->getMessage());
                }

                if (\Auth::user()) {
                    $logData = [
                        'module' => ActionsHistory::VOTING_TOURS,
                        'action' => ActionsHistory::TYPE_MOD,
                        'object' => $editVotingTour->id
                    ];

                    ActionsHistory::add($logData);
                }

                return $this->successResponse();
            }
        }

        return $this->errorResponse(__('custom.error_changing_status'), $validator->errors()->messages());
    }

    /**
     * Rename the active/last active voting tour
     *
     * @param string new_name - required
     *
     * @return true on success false on failure
     */
    public function rename(Request $request)
    {
        $post = $request->all();
        $countActive = 0;

        $validator = Validator::make($post, [
            'new_name'      => 'required|string'
        ]);

        if (!$validator->fails()) {
            $editVotingTour = VotingTour::getLatestTour();

            if ($editVotingTour) {
                try {
                    DB::beginTransaction();

                    $editVotingTour->name = $post['new_name'];

                    $changed = $editVotingTour->save();

                    DB::commit();
                } catch (QueryException $e) {
                    DB::rollback();
                    Log::error($e->getMessage());
                }

                if (\Auth::user()) {
                    $logData = [
                        'module' => ActionsHistory::VOTING_TOURS,
                        'action' => ActionsHistory::TYPE_MOD,
                        'object' => $editVotingTour->id
                    ];

                    ActionsHistory::add($logData);
                }

                return $this->successResponse();
            }
        }

        return $this->errorResponse(__('custom.error_renaming_tour'), $validator->errors()->messages());
    }

    /**
     * Get the latest voting tour - if a status different from FINISHED is found
     * it is returned, otherwise returns the tour with the highest updated_at
     *
     * @param string order_field - required
     * @param string order_type - required
     *
     * @return tour or error
     */
    public function getLatestVotingTour(Request $request)
    {
        $votingTour = VotingTour::getLatestTour();

        if ($votingTour) {
            return $this->successResponse($votingTour);
        } else {
            return $this->errorResponse(__('custom.voting_tour_not_found'));
        }
    }

    /**
     * Lists voting tours
     *
     * @param string order_field - optional
     * @param string order_type - optional
     *
     * @return json with tour list or error message
     */
    public function list(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'order_field'    => 'nullable|string',
            'order_type'     => 'nullable|string'
        ]);

        if (!$validator->fails()) {
            $orderField = isset($post['order_field']) ? $post['order_field'] : VotingTour::DEFAULT_ORDER_FIELD;
            $orderType = isset($post['order_type']) ? $post['order_type'] : VotingTour::DEFAULT_ORDER_TYPE;

            $orderColumns = [
                'name',
                'status',
                'created_at',
                'updated_at',
            ];

            if (!in_array($orderField, $orderColumns)) {
                return $this->errorResponse(__('custom.invalid_sort_field'));
            }

            $tourList = VotingTour::orderBy($orderField, $orderType)->get();

            if ($tourList->first()) {
                if (\Auth::user()) {
                    $logData = [
                        'module' => ActionsHistory::VOTING_TOURS,
                        'action' => ActionsHistory::TYPE_SEE
                    ];

                    ActionsHistory::add($logData);
                }

                return $this->successResponse($tourList);
            } else {
                return $this->errorResponse(__('custom.tour_list_not_found'));
            }
        }

        return $this->errorResponse(__('custom.tour_list_not_found'));
    }

    /**
     * Gets data for a given voting tour
     *
     * @param integer id - required
     *
     * @return model Data
     */
    public function getData(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'tour_id'    => 'required|integer|exists:voting_tour,id'
        ]);

        if (!$validator->fails()) {
            $votingTourData = VotingTour::where('id', $post['tour_id'])->first();

            if ($votingTourData) {
                if (\Auth::user()) {
                    $logData = [
                        'module' => ActionsHistory::VOTING_TOURS,
                        'action' => ActionsHistory::TYPE_SEE,
                        'object' => $votingTourData->id
                    ];

                    ActionsHistory::add($logData);
                }

                return $this->successResponse($votingTourData);
            } else {
                return $this->errorResponse(__('custom.no_data_found'));
            }
        }

        return $this->errorResponse(__('custom.no_data_found'), $validator->errors()->messages());
    }

    public function listStatuses(Request $request)
    {
        $statuses = VotingTour::getStatuses();

        foreach ($statuses as $statusId => $statusName) {
            $results[] = [
                'id'     => $statusId,
                'name'   => $statusName
            ];
        }

        if ($results) {
            return $this->successResponse($results);
        } else {
            return $this->errorResponse('custom.status_list_not_found');
        }
    }
}
