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
     */
    public function vote(Request $request)
    {
        $post = $request->all();

        $votingTour = VotingTour::getLatestTour();

        if ($votingTour) {
            // Check if voting tour status is 3 or 5 (statuses allowing voting)
            if (!array_key_exists($votingTour->status, VotingTour::getActiveStatuses())) {
                return $this->errorResponse(__('custom.cannot_vote_for_inactive_tour'));
            }

            $validator = Validator::make($post, [
                'org_id'      => 'required|int|exists:organisations,id',
                'org_list'    => 'required|string'
            ]);

            if (!$validator->fails()) {
                // Ensures the string consists of integers
                if (ctype_digit(str_replace(',', '', str_replace(' ', '', $post['org_list'])))) {
                    $votedForOrgArray = explode(',', str_replace(' ', '', $post['org_list']));

                    $votedForListSize = sizeof($votedForOrgArray);

                    if ($votedForListSize <= Vote::MAX_VOTES || ($votedForListSize >= Vote::MIN_VOTES)) {
                        $currentTourOrgList = Organisation::where('voting_tour_id', VotingTour::getLatestTour()->id)
                            ->whereIn('id', $votedForOrgArray)
                            ->whereIn('status', [Organisation::STATUS_CANDIDATE, Organisation::STATUS_BALLOTAGE])
                            ->get()->toArray();

                        if (sizeof($currentTourOrgList) != $votedForListSize) {
                            return $this->errorResponse(__('custom.invalid_org_in_vote_list'));
                        }

                        $vote = new Vote;
                        $prevRecord = Vote::orderBy('vote_time', 'DESC')->first();

                        $t = microtime(true);
                        $micro = sprintf('%06d',($t - floor($t)) * 1000000);
                        $d = new \DateTime(date('Y-m-d H:i:s.'. $micro, $t));

                        $vote->vote_time = $d->format('Y-m-d H:i:s.u');
                        $vote->voter_id = $post['org_id'];
                        $vote->voting_tour_id = $votingTour->id;
                        $vote->vote_data = $post['org_list'];
                        $vote->tour_status = $votingTour->status;

                        if (!is_null($prevRecord)) {
                            $vote->prev_hash = hash('sha256',
                                $prevRecord->vote_time .
                                $prevRecord->voter_id .
                                $prevRecord->voting_tour_id .
                                $prevRecord->vote_data .
                                $prevRecord->tour_status .
                                $prevRecord->prev_hash
                            );
                        }

                        $vote->save();

                        return $this->successResponse(__('custom.vote_successful'));
                    }
                }
            }
        }

        return $this->errorResponse(__('custom.vote_failed'), $validator->errors()->messages());
    }

    /**
     * Get latest organisation vote
     *
     * @param integer org_id - required
     *
     * @return json - response with status code and organisation vote or errors
     */
    public function getLatestVote(Request $request)
    {
        $votingTour = VotingTour::getLatestTour();
        if (empty($votingTour)) {
            return $this->errorResponse(__('custom.voting_tour_not_found'));
        }

        $post = $request->all();

        $validator = Validator::make($post, [
            'org_id' => 'required|int|exists:organisations,id|digits_between:1,10',
        ]);

        if (!$validator->fails()) {
            $voteStatus = $this->prepareVoteStatus($votingTour->id, $votingTour->status);
            if (is_null($voteStatus)) {
                return $this->errorResponse(__('custom.get_vote_not_allowed'));
            }

            try {
                $lastVote = Vote::where('voter_id', $post['org_id'])
                                ->where('voting_tour_id', $votingTour->id)
                                ->where('tour_status', $voteStatus)
                                ->orderBy('vote_time', 'DESC')->first();

                if (!$lastVote) {
                    $lastVote = new Vote;
                }

                return $this->successResponse($lastVote);
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.get_vote_fail'), $e->getMessage());
            }
        }

        return $this->errorResponse(__('custom.vote_not_found_for_org'), $validator->errors()->messages());
    }

    public function isBlockChainValid(Request $request)
    {
        $votes = Vote::select('*')->orderBy('id', 'ASC')->get();

        foreach ($votes as $singleVote) {
            $voteHash = hash('sha256',
                $singleVote->vote_time .
                $singleVote->voter_id .
                $singleVote->voting_tour_id .
                $singleVote->vote_data .
                $singleVote->tour_status .
                $singleVote->prev_hash
            );

            if ($singleVote->id < $votes->last()->id) {
                $nextVoteId = $singleVote->id + 1;

                $votes = $votes->keyBy('id');
                $nextVote = $votes->get($nextVoteId);

                if (isset($nextVote->prev_hash)) {
                    if (!($voteHash === $nextVote->prev_hash)) {
                        return $this->errorResponse(__('custom.inconsistent_voting_records'), [__('custom.inconsistent_record') => $singleVote->id]);
                    }
                }
            }
        }

        return $this->successResponse(__('custom.consistency_of_records_confirmed'));
    }

    public function ranking(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'tour_id' => 'required|int|exists:voting_tour,id',
            'status'  => 'required|int|in:'. implode(',', array_keys(VotingTour::getActiveStatuses())),
        ]);

        if (!$validator->fails()) {
            try {
                $tour = VotingTour::where('id', $post['tour_id'])->first();

                if ($post['status'] == VotingTour::STATUS_VOTING) {
                    $candidateStatus = [Organisation::STATUS_BALLOTAGE, Organisation::STATUS_CANDIDATE];
                } elseif ($post['status'] == VotingTour::STATUS_BALLOTAGE) {
                    $candidateStatus = [Organisation::STATUS_BALLOTAGE];
                }

                $tourVoteData = DB::select('
                    SELECT v.vote_data, v.vote_time, v.voter_id, v.tour_status
                    FROM votes v
                        INNER JOIN
                        (SELECT voter_id, MAX(vote_time) AS voteTime
                        FROM votes
                        WHERE voting_tour_id = '. $post['tour_id'] .' AND tour_status = '. $post['status'] .'
                        GROUP BY voter_id) innerv
                    ON v.voter_id = innerv.voter_id
                    INNER JOIN voting_tour vt ON vt.id = v.voting_tour_id
                    WHERE vt.id = '. $post['tour_id'] .'
                    AND v.tour_status = '. $post['status'] .'
                    AND v.vote_time = innerv.voteTime
                    AND v.id != '. Vote::GENESIS_RECORD .'
                    GROUP BY v.voter_id
                ');

                $listOfCandidates = Organisation::select('id', 'eik', 'name', DB::raw('0 as votes'))
                                        ->where('voting_tour_id', $post['tour_id'])
                                        ->whereIn('status', $candidateStatus)
                                        ->orderBy(Organisation::DEFAULT_ORDER_FIELD, Organisation::DEFAULT_ORDER_TYPE)->get();

                $fullResult = $listOfCandidates->keyBy('id');

                foreach ($fullResult as $orgId => $orgData) {
                    foreach ($tourVoteData as $singleVote) {
                        $fullResult[$orgId]['votes'] += in_array($orgId, explode(',', $singleVote->vote_data)) ? 1 : 0;
                    }
                }

                $fullResult = $fullResult->sortByDesc('votes');

                return $this->successResponse($fullResult);
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.ranking_failed'), $e->getMessage());
            }
        }

        return $this->errorResponse(__('custom.ranking_failed'), $validator->errors()->messages());
    }

    public function getVoteStatus(Request $request)
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'tour_id' => 'required|int|exists:voting_tour,id'
        ]);

        if (!$validator->fails()) {
            $voteTour = VotingTour::where('id', $post['tour_id'])->first();

            return $this->successResponse([
                'id' => $voteTour->status,
                'name' => VotingTour::getStatuses()[$voteTour->status]
            ]);
        }

        return $this->errorResponse(__('custom.voting_tour_not_found'), $validator->errors()->messages());
    }

    /**
     * List already voted organisations
     *
     * @param none
     *
     * @return json - response with status code and list of voted organisations or errors
     */
    public function listVoters(Request $request)
    {
        $votingTour = VotingTour::getLatestTour();
        if (empty($votingTour)) {
            return $this->errorResponse(__('custom.voting_tour_not_found'));
        }

        try {
            $voteStatus = $this->prepareVoteStatus($votingTour->id, $votingTour->status);
            if (is_null($voteStatus)) {
                return $this->errorResponse(__('custom.get_vote_not_allowed'));
            }

            $voters = Organisation::where('voting_tour_id', $votingTour->id)
                          ->whereIn('status', Organisation::getApprovedStatuses())
                          ->whereHas('votes', function($query) use ($voteStatus) {
                              $query->where('tour_status', $voteStatus);
                          });
            $voters->orderBy(Organisation::DEFAULT_ORDER_FIELD, Organisation::DEFAULT_ORDER_TYPE);

            return $this->successResponse($voters->get());
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.list_voters_fail'), $e->getMessage());
        }
    }

    private function prepareVoteStatus($tourId, $tourStatus)
    {
        $voteStatus = null;

        if (in_array($tourStatus, [VotingTour::STATUS_VOTING, VotingTour::STATUS_RANKING])) {
            $voteStatus = VotingTour::STATUS_VOTING;
        } elseif ($tourStatus == VotingTour::STATUS_BALLOTAGE) {
            $voteStatus = VotingTour::STATUS_BALLOTAGE;
        } elseif ($tourStatus == VotingTour::STATUS_FINISHED) {
            if (Organisation::hasOrgsForBallotage($tourId)) {
                $voteStatus = VotingTour::STATUS_BALLOTAGE;
            } else {
                $voteStatus = VotingTour::STATUS_VOTING;
            }
        }

        return $voteStatus;
    }
}
