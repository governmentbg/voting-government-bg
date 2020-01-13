<?php

namespace App\Http\Controllers\Api;

use App\Vote;
use \Validator;
use App\VotingTour;
use App\Organisation;
use App\ActionsHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
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
                'org_id'      => [
                    'required',
                    'int',
                    Rule::exists('organisations', 'id')->where(function ($query) {
                        $query->whereIn('status', Organisation::getApprovedStatuses());
                    }),
                ],
                'org_list'    => 'required|string'
            ]);

            if (!$validator->fails()) {
                try {
                    // Ensures the string consists of integers
                    if (ctype_digit(str_replace(',', '', str_replace(' ', '', $post['org_list'])))) {
                        $votedForOrgArray = explode(',', str_replace(' ', '', $post['org_list']));

                        $votedForListSize = sizeof($votedForOrgArray);
                        $maxVotes = $this->prepareMaxVotes($votingTour);

                        if ($votedForListSize >= Vote::MIN_VOTES && $votedForListSize <= $maxVotes) {
                            $currentTourOrgCount = Organisation::where('voting_tour_id', $votingTour->id)
                                ->whereIn('id', $votedForOrgArray)
                                ->whereIn('status', Organisation::getApprovedCandidateStatuses())
                                ->count();

                            if ($currentTourOrgCount != $votedForListSize) {
                                return $this->errorResponse(__('custom.invalid_org_in_vote_list'));
                            }

                            $vote = new Vote;
                            $prevRecord = Vote::orderBy('id', 'DESC')->first();

                            $t = microtime(true);
                            $micro = sprintf('%06d',($t - floor($t)) * 1000000);
                            $d = new \DateTime(date('Y-m-d H:i:s.'. $micro, $t));

                            $vote->vote_time = $d->format('Y-m-d H:i:s.u');
                            $vote->voter_id = $post['org_id'];
                            $vote->voting_tour_id = $votingTour->id;
                            $vote->vote_data = $post['org_list'];

                            if ($votingTour->status == VotingTour::STATUS_VOTING) {
                                $vote->tour_status = Vote::TOUR_VOTING;
                            } else {
                                $vote->tour_status = Vote::TOUR_BALLOTAGE;
                            }

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

                            if (\Auth::user()) {
                                $logData = [
                                    'module' => ActionsHistory::VOTES,
                                    'action' => ActionsHistory::TYPE_VOTED
                                ];

                                ActionsHistory::add($logData);
                            }

                            return $this->successResponse(['id' => $vote->id], true);
                        }
                    }
                } catch (\Exception $e) {
                    logger()->error($e->getMessage());
                    return $this->errorResponse(__('custom.vote_failed'), __('custom.internal_server_error'));
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
            'org_id' => 'required|int|exists:organisations,id',
        ]);

        if (!$validator->fails()) {
            try {
                $voteLimits = $this->prepareVoteLimits($votingTour->id, $votingTour->status);
                if (!isset($voteLimits['status'])) {
                    return $this->errorResponse(__('custom.get_vote_not_allowed'));
                }

                $vote = Vote::where('voter_id', $post['org_id'])
                    ->where('voting_tour_id', $votingTour->id)
                    ->where('tour_status', $voteLimits['status']);
                if (isset($voteLimits['minId'])) {
                    $vote->where('id', '>', $voteLimits['minId']);
                }
                $vote->orderBy('id', 'DESC');

                $lastVote = $vote->first();

                if (!$lastVote) {
                    $lastVote = new Vote;
                }

                return $this->successResponse($lastVote);
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.get_vote_fail'), __('custom.internal_server_error'));
            }
        }

        return $this->errorResponse(__('custom.vote_not_found_for_org'), $validator->errors()->messages());
    }

    public function isBlockChainValid(Request $request)
    {
        $votes = Vote::select('*')->orderBy('id', 'ASC')->get();

        foreach ($votes->values() as $index => $singleVote) {
            $voteHash = hash('sha256',
                $singleVote->vote_time .
                $singleVote->voter_id .
                $singleVote->voting_tour_id .
                $singleVote->vote_data .
                $singleVote->tour_status .
                $singleVote->prev_hash
            );

            if ($singleVote->id < $votes->last()->id) {
                $nextVoteId = $votes->values()[$index + 1]->id;

                $votes = $votes->keyBy('id');
                $nextVote = $votes->get($nextVoteId);

                if (isset($nextVote->prev_hash)) {
                    if (!($voteHash === $nextVote->prev_hash)) {
                        return $this->errorResponse(__('custom.inconsistent_voting_records'), ['inconsistent_record' => $singleVote->id]);
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
            'status'         => 'required|int|in:'. implode(',', array_keys(Vote::getRankingStatuses())),
            'declass_org_id' => 'nullable|int|exists:organisations,id|required_if:status,'. Vote::TOUR_ORGANISATION_DECLASSED_RANKING
        ]);

        if (!$validator->fails()) {
            try {
                $votingTour = VotingTour::getLatestTour();
                if (!empty($votingTour) && $votingTour->status == VotingTour::STATUS_RANKING) {
                    if ($post['status'] == Vote::TOUR_RANKING) {
                        $voteRankingData = [];
                        $votingCount = 0;
                    } else {
                        $voteRankingData = Vote::getLatestRankingData($votingTour->id);
                        $votingCount = Vote::getVotingCount($votingTour->id);
                    }

                    $voteRecordData = [];

                    if (!empty($voteRankingData)) {
                        $voteRankingData = json_decode($voteRankingData['vote_data'], true);

                        if (is_null($voteRankingData)) {
                            return $this->errorResponse(__('custom.ranking_failed'));
                        }

                        foreach ($voteRankingData as $orgId => $data) {
                            if ($post['status'] == Vote::TOUR_ORGANISATION_DECLASSED_RANKING) {
                                if ($orgId == $post['declass_org_id']) {
                                   continue;
                                }
                            }

                            for ($i = 0; $i < $votingCount; $i++) {
                                if (isset($data[$i])) {
                                    $voteRecordData[$orgId][$i] = $data[$i];
                                }
                            }
                        }
                    }

                    if ($post['status'] != Vote::TOUR_ORGANISATION_DECLASSED_RANKING) {
                        $limitVoteCounting = '';

                        if ($post['status'] == Vote::TOUR_RANKING) {
                            $candidateStatus = [Organisation::STATUS_CANDIDATE];
                            $votesStatus = Vote::TOUR_VOTING;
                        } else {
                            $candidateStatus = [Organisation::STATUS_BALLOTAGE];
                            $votesStatus = Vote::TOUR_BALLOTAGE;

                            $latestRankingId = Vote::getLatestRankingId($votingTour->id);
                            if (!empty($latestRankingId)) {
                                $limitVoteCounting = 'AND id > '. $latestRankingId .' ';
                            }
                        }

                        $listOfCandidates = Organisation::select('id', 'eik', DB::raw('0 as votes'))
                            ->where('voting_tour_id', $votingTour->id)
                            ->whereIn('status', $candidateStatus)
                            ->orderBy(Organisation::DEFAULT_ORDER_FIELD, Organisation::DEFAULT_ORDER_TYPE)->get();

                        $listOfCandidates = $listOfCandidates->keyBy('id');

                        if ($listOfCandidates->isNotEmpty()) {
                            $tourVoteData = Vote::select('votes.vote_data', 'votes.vote_time', 'votes.voter_id', 'votes.tour_status')
                                ->join(DB::raw(
                                    '(SELECT voter_id, MAX(vote_time) AS voteTime '.
                                    'FROM votes '.
                                    'WHERE voting_tour_id = '. $votingTour->id .' '.
                                    'AND tour_status = '. $votesStatus .' '.
                                    $limitVoteCounting .
                                    'GROUP BY voter_id) innerv'
                                ), 'votes.voter_id', '=', 'innerv.voter_id')
                                ->join('organisations', 'organisations.id', '=', 'votes.voter_id')
                                ->whereIn('organisations.status', Organisation::getApprovedStatuses())
                                ->where('votes.voting_tour_id', $votingTour->id)
                                ->where('votes.tour_status', $votesStatus)
                                ->whereRaw('votes.vote_time = innerv.voteTime')
                                ->where('votes.id', '!=', Vote::GENESIS_RECORD)
                                ->groupBy('votes.voter_id')
                                ->get();

                            if ($tourVoteData->isNotEmpty()) {
                                foreach ($tourVoteData as $singleVote) {
                                    $votes = explode(',', $singleVote->vote_data);
                                    foreach ($votes as $orgId) {
                                        if (isset($listOfCandidates[$orgId])) {
                                            $listOfCandidates[$orgId]->votes += 1;
                                        }
                                    }
                                }
                            }
                        }

                        // sort list of candidates by votes, eik
                        $listOfCandidates = $listOfCandidates->sort(function ($a, $b) {
                            if ($a->votes === $b->votes) {
                                if ($a->eik === $b->eik) {
                                    return 0;
                                }
                                return $a->eik < $b->eik ? -1 : 1;
                            }

                            return $a->votes > $b->votes ? -1 : 1;
                        });

                        if (empty($voteRecordData)) {
                            foreach ($listOfCandidates as $candidate) {
                                $voteRecordData[$candidate->id][$votingCount] = intval($candidate->votes);
                            }
                        } else {
                            $electedOrgsData = [];
                            if (!empty($voteRecordData)) {
                                // calculate vote limit
                                $limits = Vote::calculateVoteLimit($voteRecordData, $votingCount);

                                // extract selected orgs data
                                $electedOrgsData = array_slice($voteRecordData, 0, $limits['orgPos'] + 1, true);
                                $voteRecordData = array_slice($voteRecordData, $limits['orgPos'] + 1, null, true);
                            }
                            $ballotageOrgsData = [];
                            foreach ($listOfCandidates as $candidate) {
                                $voteRecordData[$candidate->id][$votingCount] = intval($candidate->votes);

                                // extract ballotage orgs data
                                $ballotageOrgsData[$candidate->id] = $voteRecordData[$candidate->id];
                                unset($voteRecordData[$candidate->id]);
                            }
                            // reorder list data based on ballotage votes
                            $voteRecordData = $electedOrgsData + $ballotageOrgsData + $voteRecordData;
                        }
                    }

                    $prevRecord = Vote::orderBy('id', 'DESC')->first();

                    $prevHash = null;
                    if (!is_null($prevRecord)) {
                        $prevHash = hash('sha256',
                            $prevRecord->vote_time .
                            $prevRecord->voter_id .
                            $prevRecord->voting_tour_id .
                            $prevRecord->vote_data .
                            $prevRecord->tour_status .
                            $prevRecord->prev_hash
                        );
                    }

                    $t = microtime(true);
                    $micro = sprintf('%06d',($t - floor($t)) * 1000000);
                    $d = new \DateTime(date('Y-m-d H:i:s.'. $micro, $t));

                    Vote::create([
                        'vote_time'      => $d->format('Y-m-d H:i:s.u'),
                        'voting_tour_id' => $votingTour->id,
                        'vote_data'      => json_encode($voteRecordData, JSON_FORCE_OBJECT),
                        'tour_status'    => $post['status'],
                        'prev_hash'      => $prevHash
                    ]);

                    if (\Auth::user()) {
                        $logData = [
                            'module' => ActionsHistory::VOTES,
                            'action' => ActionsHistory::TYPE_RANKED
                        ];

                        ActionsHistory::add($logData);
                    }

                    return $this->successResponse();
                }

                return $this->errorResponse(__('custom.ranking_not_allowed'));
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.ranking_failed'), __('custom.internal_server_error'));
            }
        }

        return $this->errorResponse(__('custom.ranking_failed'), $validator->errors()->messages());
    }

    /**
     * Get latest ranking
     *
     * @param integer tour_id - required
     * @param boolean with_voter_turnout - optional
     *
     * @return json - response with status code and ranking data or errors
     */
    public function getLatestRanking(Request $request)
    {
        $post = $request->all();
        $withVoterTurnout = $request->get('with_voter_turnout', true);

        $validator = Validator::make($post, [
            'tour_id' => 'required|int|exists:voting_tour,id'
        ]);

        if (!$validator->fails()) {
            try {
                $votingTour = VotingTour::where('id', $post['tour_id'])->first();
                if (!empty($votingTour) && in_array($votingTour->status, VotingTour::getRankingStatuses())) {
                    $result = [];

                    // get ids of ranking records
                    $rankingIds = Vote::getRankingIds($votingTour->id);

                    if (empty($rankingIds)) {
                        if ($votingTour->status != VotingTour::STATUS_FINISHED) {
                            return $this->errorResponse(__('custom.ranking_not_found'));
                        }

                        $result['ranking'] = [];
                        $result['voting_count'] = 0;
                        if ($withVoterTurnout) {
                            $result['voter_turnout'] = [];
                        }
                    } else {
                        // get latest ranking data
                        $result['ranking'] = Vote::getLatestRankingData($votingTour->id);

                        if (empty($result['ranking'])) {
                            return $this->errorResponse(__('custom.ranking_not_found'));
                        }

                        $result['ranking'] = json_decode($result['ranking']['vote_data'], true);
                        if (is_null($result['ranking'])) {
                            return $this->errorResponse(__('custom.list_ranking_fail'));
                        }

                        $result['voting_count'] = count($rankingIds);

                        // calculate votes limit
                        $limits = Vote::calculateVoteLimit($result['ranking'], $result['voting_count']);

                        $orgPos = 0;
                        foreach ($result['ranking'] as $orgId => $data) {
                            $orgData = Organisation::select('id', 'eik', 'name', DB::raw('NULL as votes'))
                                ->where('id', $orgId)
                                ->where('voting_tour_id', $votingTour->id)
                                ->first();

                            if (empty($orgData)) {
                                return $this->errorResponse(__('custom.org_not_found'));
                            }

                            $orgData->votes = $data;
                            if ($orgPos <= $limits['orgPos']) {
                                $orgData->elected = true;
                            } else {
                                for ($i = $result['voting_count']; $i > 0; $i--) {
                                    if (isset($limits['votes'][$i - 1]) && isset($orgData->votes[$i - 1]) &&
                                        $orgData->votes[$i - 1] == $limits['votes'][$i - 1]) {
                                        $orgData->for_ballotage = true;
                                    }
                                }
                            }
                            $result['ranking'][$orgId] = $orgData;
                            $orgPos++;
                        }

                        if ($withVoterTurnout) {
                            $result['voter_turnout'] = [];

                            // count registered organisations
                            $registered = Organisation::countRegistered($votingTour->id);

                            for ($votingIndex = 0; $votingIndex < $result['voting_count']; $votingIndex++) {
                                // get vote limits
                                $voteLimits = Vote::getVoteLimits($votingTour->id, $votingIndex, $rankingIds);

                                // count voted organisations
                                $voted = Organisation::countVoted($votingTour->id, $voteLimits);

                                // calculate voter turnout
                                $result['voter_turnout'][$votingIndex] = [
                                    'all'     => $registered,
                                    'voted'   => $voted,
                                    'percent' => ($registered > 0 ? round($voted / $registered * 100, 2) : 0)
                                ];
                            }
                        }
                    }

                    return $this->successResponse($result, false, JSON_FORCE_OBJECT);
                }

                return $this->errorResponse(__('custom.ranking_not_allowed'));

            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.list_ranking_fail'), __('custom.internal_server_error'));
            }
        }

        return $this->errorResponse(__('custom.list_ranking_fail'), $validator->errors()->messages());
    }

    /**
     * List already voted organisations
     *
     * @param array filters - optional
     * @param big integer filters[eik] - optional
     * @param boolean with_pagination - optional
     *
     * @return json - response with status code and list of voted organisations or errors
     *
     */
    public function listVoters(Request $request)
    {
        $rules = [
            'filters'         => 'nullable|array',
            'with_pagination' => 'nullable|bool',
            'page_number'     => 'nullable|int|min:1',
        ];

        $data = $request->only(array_keys($rules));

        $validator = \Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse(__('custom.validation_error'), $validator->errors()->messages());
        }

        $filters = isset($data['filters']) ? $data['filters'] : [];
        $withPagination = isset($data['with_pagination']) ? $data['with_pagination'] : true;
        $page = isset($data['page_number']) ? $data['page_number'] : null;

        $validator = Validator::make($filters, [
            'eik' => 'nullable|digits_between:1,19',
        ]);

        if (!$validator->fails()) {
            try {
                $votingTour = VotingTour::getLatestTour();
                if (empty($votingTour)) {
                    return $this->errorResponse(__('custom.voting_tour_not_found'));
                }

                $voteLimits = $this->prepareVoteLimits($votingTour->id, $votingTour->status, true);
                if (!isset($voteLimits['status'])) {
                    return $this->errorResponse(__('custom.list_voters_not_allowed'));
                }

                $voters = Organisation::select('id', 'eik', 'name', 'is_candidate', 'created_at');
                if (isset($filters['eik'])) {
                    $voters->where('eik', $filters['eik']);
                }

                $voters->where('voting_tour_id', $votingTour->id)
                       ->whereIn('status', Organisation::getApprovedStatuses())
                       ->whereHas('votes', function($query) use ($voteLimits) {
                           $query->where('tour_status', $voteLimits['status']);
                           if (isset($voteLimits['minId'])) {
                               $query->where('votes.id', '>', $voteLimits['minId']);
                           }
                       })
                       ->orderBy(Organisation::DEFAULT_ORDER_FIELD, Organisation::DEFAULT_ORDER_TYPE);

                if ($withPagination) {
                    $request->request->add(['page' => $page]);
                    $voters = $voters->paginate();
                } else {
                    $voters = $voters->get();
                }

                return $this->successResponse($voters);
            } catch (\Exception $e) {
                logger()->error($e->getMessage());
                return $this->errorResponse(__('custom.list_voters_fail'), __('custom.internal_server_error'));
            }
        }

        return $this->errorResponse(__('custom.list_voters_fail'), $validator->errors()->messages());
    }

    public function cancelTour(Request $request)
    {
        try {
            $votingTour = VotingTour::getLatestTour();
            if (!empty($votingTour) && $votingTour->status == VotingTour::STATUS_FINISHED) {
                $prevRecord = Vote::orderBy('id', 'DESC')->first();

                $prevHash = null;
                if (!is_null($prevRecord)) {
                    $prevHash = hash('sha256',
                        $prevRecord->vote_time .
                        $prevRecord->voter_id .
                        $prevRecord->voting_tour_id .
                        $prevRecord->vote_data .
                        $prevRecord->tour_status .
                        $prevRecord->prev_hash
                    );
                }

                $t = microtime(true);
                $micro = sprintf('%06d',($t - floor($t)) * 1000000);
                $d = new \DateTime(date('Y-m-d H:i:s.'. $micro, $t));

                Vote::create([
                    'vote_time'      => $d->format('Y-m-d H:i:s.u'),
                    'voting_tour_id' => $votingTour->id,
                    'tour_status'    => Vote::TOUR_CANCELLED_NO_RANKING,
                    'prev_hash'      => $prevHash
                ]);

                if (\Auth::user()) {
                    $logData = [
                        'module' => ActionsHistory::VOTES,
                        'action' => ActionsHistory::TYPE_CANCELLED_TOUR
                    ];

                    ActionsHistory::add($logData);
                }
                return $this->successResponse();
            }

            return $this->errorResponse(__('custom.cancel_tour_not_allowed'));
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.cancel_tour_fail'), __('custom.internal_server_error'));
        }
    }

    public function getMaxVotes(Request $request)
    {
        try {
            $votingTour = VotingTour::getLatestTour();

            if (!empty($votingTour) && array_key_exists($votingTour->status, VotingTour::getActiveStatuses())) {
                $maxVotes = $this->prepareMaxVotes($votingTour);

                if ($maxVotes >= 0) {
                    return $this->successResponse(['max_votes' => $maxVotes], true);
                }

                return $this->errorResponse(__('custom.get_max_votes_fail'));
            }

            return $this->errorResponse(__('custom.get_max_votes_not_allowed'));
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return $this->errorResponse(__('custom.get_max_votes_fail'), __('custom.internal_server_error'));
        }
    }

    /**
     * List ranking statuses
     *
     * @param none
     *
     * @return json - response with status code and list of statuses or errors
     */
    public function listRankingStatuses(Request $request)
    {
        $statuses = Vote::getRankingStatuses();

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

    private function prepareVoteLimits($tourId, $tourStatus, $allowRanking = false)
    {
        $voteLimits = [];

        if ($tourStatus == VotingTour::STATUS_VOTING) {
            $voteLimits['status'] = Vote::TOUR_VOTING;
        } elseif ($tourStatus == VotingTour::STATUS_BALLOTAGE) {
            $voteLimits['status'] = Vote::TOUR_BALLOTAGE;
            $voteLimits['minId'] = Vote::getLatestRankingId($tourId, Vote::TOUR_BALLOTAGE_RANKING);
        } elseif ($allowRanking) {
            if ($tourStatus == VotingTour::STATUS_RANKING) {
                $votingCount = Vote::getVotingCount($tourId);
                if ($votingCount > 1) {
                    $voteLimits['status'] = Vote::TOUR_BALLOTAGE;
                    $voteLimits['minId'] = Vote::getLatestRankingId($tourId, Vote::TOUR_BALLOTAGE_RANKING, 1);
                } else {
                    $voteLimits['status'] = Vote::TOUR_VOTING;
                }
            } elseif ($tourStatus == VotingTour::STATUS_FINISHED) {
                $votingCount = Vote::getVotingCount($tourId);
                $tourCancelled = (Vote::getLatestRankingId($tourId, Vote::TOUR_CANCELLED_NO_RANKING) != null);
                if ($tourCancelled) {
                    $voteLimits['status'] = ($votingCount > 0) ? Vote::TOUR_BALLOTAGE : Vote::TOUR_VOTING;
                    $voteLimits['minId'] = Vote::getLatestRankingId($tourId);
                } else {
                    $voteLimits['status'] = ($votingCount > 1) ? Vote::TOUR_BALLOTAGE : Vote::TOUR_VOTING;
                    $voteLimits['minId'] = Vote::getLatestRankingId($tourId, null, 1);
                }
            }
        }

        return $voteLimits;
    }

    /**
     * Get maximum number of votes.
     * For ballotage the number is MAX_VOTES minus number of elected orgs.
     *
     * @param stdClass $votingTour
     *
     * @return integer
     */
    private function prepareMaxVotes($votingTour)
    {
        if ($votingTour->status == VotingTour::STATUS_VOTING) {
            return Vote::MAX_VOTES;
        }

        $cacheKey = VotingTour::getCacheKey($votingTour->id, 'max-votes');
        if ($votingTour->status == VotingTour::STATUS_BALLOTAGE) {
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
        }

        $maxVotes = -1;

        // get voting count
        $votingCount = Vote::getVotingCount($votingTour->id);

        if ($votingCount > 0) {
            // get latest ranking data
            $latestRanking = Vote::getLatestRankingData($votingTour->id);
            $latestRanking = !empty($latestRanking) ? json_decode($latestRanking['vote_data'], true) : null;

            if (!is_null($latestRanking)) {
                // calculate votes limit
                $limits = Vote::calculateVoteLimit($latestRanking, $votingCount);

                $maxVotes = Vote::MAX_VOTES - ($limits['orgPos'] + 1);
            }
        }

        Cache::put($cacheKey, $maxVotes, now()->addMinutes(60));

        return $maxVotes;
    }
}
