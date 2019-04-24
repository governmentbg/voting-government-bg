<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\VoteController as ApiVote;
use App\VotingTour;
use App\Organisation;
use App\Vote;

class PublicController extends BaseFrontendController
{
    protected $votingTour = null;

    public function __construct()
    {
        list($this->votingTour, $errors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
    }

    public function index()
    {
        if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_UPCOMING) {
            return redirect()->action('PublicController@listRegistered');
        }

        return view('home.index');
    }

    public function listRegistered()
    {
        $showLinks = [];
        $listData = [];
        $errors = [];

        if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_UPCOMING) {
            // set links that have to be displayed
            $showLinks['registered'] = true;
            $showLinks['candidates'] = true;
            if (!in_array($this->votingTour->status, VotingTour::getRegStatuses())) {
                $showLinks['voted'] = true;
                if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_VOTING) {
                    $showLinks['ranking'] = true;
                }
            }

            // list registered organisations
            $params = [
                'filters' => [
                    'statuses' => Organisation::getApprovedStatuses()
                ]
            ];
            list($listData, $listErrors) = api_result(ApiOrganisation::class, 'search', $params);

            if (!empty($listErrors)) {
                $errors = ['message' => __('custom.list_reg_org_fail')];
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks' => $showLinks,
            'listTitle' => __('custom.registered'),
            'listData'  => $listData,
            'route'     => 'list.registered',
        ])->withErrors($errors);
    }

    public function listCandidates()
    {
        $showLinks = [];
        $listData = [];
        $errors = [];

        if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_UPCOMING) {
            // set links that have to be displayed
            $showLinks['registered'] = true;
            $showLinks['candidates'] = true;
            if (!in_array($this->votingTour->status, VotingTour::getRegStatuses())) {
                $showLinks['voted'] = true;
                if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_VOTING) {
                    $showLinks['ranking'] = true;
                }
            }

            // list candidates
            $params = [
                'filters' => [
                    'statuses' => [Organisation::STATUS_CANDIDATE, Organisation::STATUS_BALLOTAGE]
                ]
            ];
            list($listData, $listErrors) = api_result(ApiOrganisation::class, 'search', $params);

            if (!empty($listErrors)) {
                $errors = ['message' => __('custom.list_candidates_fail')];
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks' => $showLinks,
            'listTitle' => __('custom.candidates'),
            'listData'  => $listData,
            'route'     => 'list.candidates',
        ])->withErrors($errors);
    }

    public function listVoted()
    {
        $showLinks = [];
        $listData = [];
        $errors = [];

        if (!empty($this->votingTour) && !in_array($this->votingTour->status, VotingTour::getRegStatuses())) {
            // set links that have to be displayed
            $showLinks['registered'] = true;
            $showLinks['candidates'] = true;
            $showLinks['voted'] = true;
            if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_VOTING) {
                $showLinks['ranking'] = true;
            }

            // list voted organisations
            list($listData, $listErrors) = api_result(ApiVote::class, 'listVoters');

            if (!empty($listErrors)) {
                $errors = ['message' => __('custom.list_voted_org_fail')];
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks' => $showLinks,
            'listTitle' => __('custom.voted'),
            'listData'  => $listData,
            'route'     => 'list.voted',
        ])->withErrors($errors);
    }

    public function listRanking()
    {
        $showLinks = [];
        $listData = [];
        $showBallotage = false;
        $stats = [];
        $errors = [];

        if (!empty($this->votingTour) && in_array($this->votingTour->status, VotingTour::getRankingStatuses())) {
            // set links that have to be displayed
            $showLinks['registered'] = true;
            $showLinks['candidates'] = true;
            $showLinks['voted'] = true;
            $showLinks['ranking'] = true;

            // get vote status
            list($voteStatus, $listErrors) = api_result(ApiVote::class, 'getVoteStatus', ['tour_id' => $this->votingTour->id]);

            if (!empty($listErrors)) {
                $errors = ['message' => __('custom.list_ranking_fail')];
            } elseif (!empty($voteStatus)) {
                // list ranking
                $params = [
                    'tour_id' => $this->votingTour->id,
                    'status' => VotingTour::STATUS_VOTING
                ];
                list($listData, $listErrors) = api_result(ApiVote::class, 'ranking', $params);

                if (!empty($listErrors)) {
                    $errors = ['message' => __('custom.list_ranking_fail')];
                } elseif (!empty($listData)) {
                    // list registered organisations
                    $statParams = [
                        'filters' => [
                            'statuses' => Organisation::getApprovedStatuses()
                        ]
                    ];
                    list($registered, $registeredErrors) = api_result(ApiOrganisation::class, 'search', $statParams);

                    // list voted organisations
                    list($voted, $votedErrors) = api_result(ApiVote::class, 'listVoters', $params);

                    if (!empty($registeredErrors) || !empty($votedErrors)) {
                        $errors['stat_message'] = __('custom.voter_turnout_fail');
                    } else {
                        // calculate voter turnout
                        $stats['voting'] = [
                            'all'     => count($registered),
                            'voted'   => count($voted),
                            'percent' => 0
                        ];
                        if ($stats['voting']['all'] > 0) {
                            $stats['voting']['percent'] = round($stats['voting']['voted'] / $stats['voting']['all'] * 100, 2);
                        }
                    }

                    // calculate votes limit
                    $votesLimit = 0;
                    $keys = collect($listData)->keys();
                    if ($maxVotesKey = $keys->get(Vote::MAX_VOTES)) {
                        if ($prevVotesKey = $keys->get(Vote::MAX_VOTES - 1)) {
                            if ($listData->{$prevVotesKey}->votes == $listData->{$maxVotesKey}->votes) {
                                $votesLimit = $listData->{$maxVotesKey}->votes;
                            }
                        }
                    }

                    // separate list data by votes limit
                    if ($votesLimit > 0) {
                        foreach ($listData as $data) {
                            if ($data->votes == $votesLimit) {
                                $data->for_ballotage = true;
                            } elseif ($data->votes < $votesLimit) {
                                $data->dropped_out = true;
                            }
                        }
                    }

                    if ($voteStatus->id == VotingTour::STATUS_BALLOTAGE) {
                        $showBallotage = true;

                        // list ballotage ranking
                        $params['status'] = $voteStatus->id;
                        list($ballotageData, $listErrors) = api_result(ApiVote::class, 'ranking', $params);

                        if (!empty($listErrors)) {
                            $errors['message'] = __('custom.list_ballotage_ranking_fail');
                        } elseif (!empty($ballotageData)) {
                            list($voted, $votedErrors) = api_result(ApiVote::class, 'listVoters', $params);

                            if (empty($errors['stat_message']) && !empty($votedErrors)) {
                                $errors['stat_message'] = __('custom.voter_turnout_ballotage_fail');
                            } elseif (!empty($stats)) {
                                // calculate ballotage voter turnout
                                $stats['ballotage'] = [
                                    'all'     => $stats['voting']['all'],
                                    'voted'   => count($voted),
                                    'percent' => 0
                                ];
                                if ($stats['ballotage']['all'] > 0) {
                                    $stats['ballotage']['percent'] = round($stats['ballotage']['voted'] / $stats['ballotage']['all'] * 100, 2);
                                }
                            }

                            // apply ballotage votes and reorder list data
                            $finalList = new \stdClass();
                            if ($votesLimit > 0) {
                                foreach ($listData as $orgId => $data) {
                                    if (isset($ballotageData->{$orgId})) {
                                        $ballotageData->{$orgId}->ballotage_votes = $ballotageData->{$orgId}->votes;
                                        $ballotageData->{$orgId}->votes = $data->votes;
                                        $ballotageData->{$orgId}->for_ballotage = true;
                                        $ballotageData->{$orgId}->dropped_out = false;
                                        unset($listData->{$orgId});
                                    } else {
                                        if (isset($data->for_ballotage) && $data->for_ballotage ||
                                            isset($data->dropped_out) && $data->dropped_out) {
                                            $data->for_ballotage = false;
                                            $data->dropped_out = true;
                                        } else {
                                            $finalList->{$orgId} = $data;
                                            unset($listData->{$orgId});
                                        }
                                    }
                                }
                                foreach ($ballotageData as $orgId => $data) {
                                    if (isset($data->ballotage_votes)) {
                                        $finalList->{$orgId} = $data;
                                    }
                                }
                                foreach ($listData as $orgId => $data) {
                                    $finalList->{$orgId} = $data;
                                }
                                $listData = $finalList;
                            }
                        }
                    }
                }
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks'     => $showLinks,
            'listTitle'     => __('custom.ranking'),
            'listData'      => $listData,
            'route'         => 'list.ranking',
            'isRanking'     => true,
            'showBallotage' => $showBallotage,
            'stats'         => $stats,
        ])->withErrors($errors);
    }
}
