<?php

namespace App\Http\Controllers\Api;

use \Validator;
use App\Vote;
use App\VotingTour;
use App\Organisation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Http\Controllers\ApiController;

class VoteController extends ApiController
{
    /**
     * Used to vote by supplying org_id
     * of the voting organisation and a list
     * of vote choices
     *
     * @param integer org_id - required
     * @param string org_list - required
     *
     * @return true on success false on failure
     *
     */
    public function vote(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'org_id'      => 'required|int|exists:organisations,id',
            'org_list'    => 'required|string'
        ]);

        if (!$validator->fails()) {
            $votedForOrgArray = explode(', ', $post['org_list']);

            $currentTourOrgList = Organisation::where('voting_tour_id', VotingTour::getLatestTour()->id)->get();

            return $this->successResponse($currentTourOrgList);

        }

        return $this->errorResponse(__('custom.vote_failed'));
    }

    public function getLatestVote(Request $request)
    {

    }

    public function isBlockChainValid(Request $request)
    {

    }

    public function ranking(Request $request)
    {

    }

    public function getVoteStatus(Request $request)
    {

    }

    public function listVoters(Request $request)
    {

    }
}
