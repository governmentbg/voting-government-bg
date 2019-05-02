<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\VoteController as ApiVote;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\VotingTour;
use App\Jobs\SendAllVoteInvites;
use App\Organisation;
use App\Vote;

class VotingTourController extends BaseAdminController
{
    protected $redirectTo = 'admin/votingTours';

    const CREATE_SUCCESS = 'custom.create_success';

    const UPDATE_SUCCESS = 'custom.update_success';

    public function __construct()
    {
        parent::__construct();

        $this->addBreadcrumb(__('breadcrumbs.start'), route('admin.org_list'));
    }

    public function index()
    {
        $this->addBreadcrumb(__('breadcrumbs.settings'), route('admin.settings'));
        $this->addBreadcrumb(__('breadcrumbs.voting_tours'), '');
        list($votingTours, $errors) = api_result(ApiVotingTour::class, 'list');

        return view('tours.list', ['votingTours' => $votingTours, 'errors' => $errors]);
    }

    public function create()
    {
//        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
//
//        if($votingTour && $votingTour->status != VotingTour::STATUS_FINISHED){
//            return redirect()->back()->withErrors(['messsage' => __('custom.active_tour_exists')]);
//        }

        $this->addBreadcrumb(__('breadcrumbs.settings'), route('admin.settings'));
        $this->addBreadcrumb(__('breadcrumbs.voting_tours'), route('admin.voting_tour.list'));
        $this->addBreadcrumb(__('custom.create_voting_tour'), '');

        return view('tours.create');
    }

    public function edit($id)
    {
        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getData', ['tour_id' => $id]);

        if ($votingTour->status == VotingTour::STATUS_FINISHED) {
            return redirect()->back()->withErrors(['message' => __('custom.voting_tour_finished')]);
        }

        $this->addBreadcrumb(__('breadcrumbs.settings'), route('admin.settings'));
        $this->addBreadcrumb(__('breadcrumbs.voting_tours'), route('admin.voting_tour.list'));
        $this->addBreadcrumb($votingTour->name, '');

        $count = Organisation::whereIn('status', Organisation::getApprovedStatuses())->where('voting_tour_id', $votingTour->id)->count();

        return view('tours.edit', ['votingTour' => $votingTour, 'errors' => $errors, 'count' => $count]);
    }

    public function update($id)
    {
        $status = request()->get('status');
        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
        $oldStatus = $votingTour ? $votingTour->status : VotingTour::STATUS_FINISHED;

        list($data, $errors) = api_result(ApiVotingTour::class, 'changeStatus', ['new_status' => $status]);

        if (empty($errors)) {
            if ($oldStatus != $status && ($status == VotingTour::STATUS_VOTING || $status == VotingTour::STATUS_BALLOTAGE)) {
                //send emails to all orgs - voting is open
                $this->sendEmails();
            }

            session()->flash('alert-success', trans(self::UPDATE_SUCCESS));
            return redirect($this->redirectTo);
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }

    public function store()
    {
        list($id, $errors) = api_result(ApiVotingTour::class, 'add', request()->all());

        if (empty($errors)) {
            session()->flash('alert-success', trans(self::CREATE_SUCCESS));
            return redirect($this->redirectTo);
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }

    private function sendEmails()
    {
        SendAllVoteInvites::dispatch();
    }

    public function ranking($id)
    {
        $this->addBreadcrumb(__('breadcrumbs.settings'), route('admin.settings'));
        $this->addBreadcrumb(__('breadcrumbs.voting_tours'), route('admin.voting_tour.list'));
        $this->addBreadcrumb(__('custom.ranking'), '');

        $listData = [];
        $showBallotage = false;
        $stats = [];
        $errors = [];

        list($votingTour, $tourErrors) = api_result(ApiVotingTour::class, 'getData', ['tour_id' => $id]);

        if (!empty($votingTour) && in_array($votingTour->status, VotingTour::getRankingStatuses())) {
            // get vote status
            list($voteStatus, $listErrors) = api_result(ApiVote::class, 'getVoteStatus', ['tour_id' => $votingTour->id]);

            if (!empty($listErrors)) {
                $errors = ['message' => __('custom.list_ranking_fail')];
            } elseif (!empty($voteStatus)) {
                // list ranking
                $params = [
                    'tour_id' => $votingTour->id,
                    'status'  => VotingTour::STATUS_VOTING,
                ];
                list($listData, $listErrors) = api_result(ApiVote::class, 'ranking', $params);

                if (!empty($listErrors)) {
                    $errors = ['message' => __('custom.list_ranking_fail')];
                } elseif (!empty($listData)) {
                    // list registered organisations
                    $statParams = [
                        'filters' => [
                            'statuses' => Organisation::getApprovedStatuses(),
                            'tour_id'  => $votingTour->id,
                        ],
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
                            'percent' => 0,
                        ];
                        if ($stats['voting']['all'] > 0) {
                            $stats['voting']['percent'] = round($stats['voting']['voted'] / $stats['voting']['all'] * 100, 2);
                        }
                    }

                    // calculate votes limit
                    $votesLimit = -1;
                    $setBallotage = false;
                    $keys = collect($listData)->keys();
                    if ($maxVotesKey = $keys->get(Vote::MAX_VOTES)) {
                        if ($prevVotesKey = $keys->get(Vote::MAX_VOTES - 1)) {
                            if ($listData->{$prevVotesKey}->votes == $listData->{$maxVotesKey}->votes) {
                                $setBallotage = true;
                            }
                            $votesLimit = $listData->{$prevVotesKey}->votes;
                        }
                    }

                    // separate list data by votes limit
                    foreach ($listData as $data) {
                        if ($setBallotage && $data->votes == $votesLimit) {
                            $data->for_ballotage = true;
                        } elseif ($data->votes < $votesLimit) {
                            $data->dropped_out = true;
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
                                    'percent' => 0,
                                ];
                                if ($stats['ballotage']['all'] > 0) {
                                    $stats['ballotage']['percent'] = round($stats['ballotage']['voted'] / $stats['ballotage']['all'] * 100, 2);
                                }
                            }

                            // apply ballotage votes and reorder list data
                            $finalList = new \stdClass();
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
        } else {
            return redirect()->route('admin.voting_tour.list');
        }

        return view('tours.ranking', [
            'listTitle'     => $votingTour->name,
            'listData'      => $listData,
            'route'         => 'admin.org_edit',
            'showBallotage' => $showBallotage,
            'stats'         => $stats,
            'fullWidth'     => true,
        ])->withErrors($errors);
    }
}
