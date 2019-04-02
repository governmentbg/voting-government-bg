<?php

namespace App\Http\Controllers\Api;

use \Validator;
use App\VotingTour;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                    'name' => $post['name'],
                    'status' => VotingTour::STATUS_UPCOMING
                ]);

                DB::commit();
            } catch (QueryException $e) {
                DB::rollback();
                Log::error($e->getMessage());
            }

            if (isset($saved)) {
                return $this->successResponse(['voting_tour_id' => $saved->id]);
            }
        }

        return $this->errorResponse(__('custom.error_adding_tour'), $validator->errors()->messages());
    }

    /**
     * Change the voting tour status for the active tour
     *
     * @param integer id - required
     * @param integer new_status - required
     *
     * @return true on success, false on failure
     */
    public function changeStatus(Request $request)
    {
        $post = $request->all();
        $countActive = 0;

        $validator = Validator::make($post, [
            'new_status'    => 'required|int|in:'. implode(',', array_keys(VotingTour::getStatuses())),
        ]);

        if (!$validator->fails()) {
            $editVotingTour = VotingTour::getLatestTour();

            try {
                DB::beginTransaction();

                $editVotingTour->status = $post['new_status'];

                $changed = $editVotingTour->save();

                DB::commit();
            } catch (QueryException $e) {
                DB::rollback();
                Log::error($e->getMessage());
            }

            return $this->successResponse();
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

            try {
                DB::beginTransaction();

                $editVotingTour->name = $post['new_name'];

                $changed = $editVotingTour->save();

                DB::commit();
            } catch (QueryException $e) {
                DB::rollback();
                Log::error($e->getMessage());
            }

            return $this->successResponse();
        }

        return $this->errorResponse(__('custom.error_changing_status'), $validator->errors()->messages());
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
            return $this->successResponse(['voting_tour' => $votingTour]);
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

        $orderColumns = [
            'name',
            'status',
            'created_at',
            'updated_at',
        ];

        if (isset($post['order_field'])) {
            if (!in_array($post['order_field'], $orderColumns)) {
                return $this->errorResponse(__('custom.invalid_sort_field'));
            }

            $orderField = $post['order_field'];
            $orderType = $post['order_type'];
        } else {
            $orderField = VotingTour::DEFAULT_ORDER_FIELD;
            $orderType = 'DESC';
        }

        $tourList = VotingTour::orderBy($orderField, $orderType)->get();

        if (!empty($tourList)) {
            return $this->successResponse($tourList);
        } else {
            return $this->errorResponse(__('custom.tour_list_not_found'));
        }
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
            'id'    => 'required|integer|exists:voting_tour,id'
        ]);

        $votingTourData = VotingTour::where('id', $post['id'])->first();

        if ($votingTourData) {
            return $this->successResponse($votingTourData);
        } else {
            return $this->errorResponse(__('custom.no_data_found'));
        }
    }
}
