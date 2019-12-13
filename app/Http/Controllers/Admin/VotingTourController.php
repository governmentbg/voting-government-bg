<?php

namespace App\Http\Controllers\Admin;

ini_set('max_execution_time', 300);

use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\VoteController as ApiVote;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\MessageController as ApiMessage;
use App\VotingTour;
use App\Jobs\SendAllVoteInvites;
use App\Jobs\SendResultsInvitation;
use App\Organisation;
use App\Vote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\ActionsHistory;

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

        $count = Organisation::countRegistered($votingTour->id);
        $statuses = VotingTour::getStatuses();

        switch ($votingTour->status) {
            case VotingTour::STATUS_UPCOMING:
                $disabledStatuses = array_diff_key(array_keys($statuses), array_flip([VotingTour::STATUS_UPCOMING, VotingTour::STATUS_OPENED_REG, VotingTour::STATUS_FINISHED]));
                break;
            case VotingTour::STATUS_OPENED_REG:
                $disabledStatuses = array_diff_key(array_keys($statuses), array_flip([VotingTour::STATUS_OPENED_REG, VotingTour::STATUS_CLOSED_REG, VotingTour::STATUS_FINISHED]));
                break;
            case VotingTour::STATUS_CLOSED_REG:
                $disabledStatuses = array_diff_key(array_keys($statuses), array_flip([VotingTour::STATUS_CLOSED_REG, VotingTour::STATUS_VOTING, VotingTour::STATUS_FINISHED]));
                break;
            case VotingTour::STATUS_VOTING:
                $disabledStatuses = array_diff_key(array_keys($statuses), array_flip([VotingTour::STATUS_VOTING, VotingTour::STATUS_RANKING, VotingTour::STATUS_FINISHED]));
                break;
            case VotingTour::STATUS_RANKING:
                $disabledStatuses = array_diff_key(array_keys($statuses), array_flip([VotingTour::STATUS_RANKING, VotingTour::STATUS_BALLOTAGE, VotingTour::STATUS_FINISHED]));
                break;
            case VotingTour::STATUS_BALLOTAGE:
                $disabledStatuses = array_diff_key(array_keys($statuses), array_flip([VotingTour::STATUS_BALLOTAGE, VotingTour::STATUS_RANKING, VotingTour::STATUS_FINISHED]));
                break;
        }

        return view('tours.edit', ['votingTour' => $votingTour, 'errors' => $errors, 'count' => $count, 'statuses' => $statuses, 'disabledStatuses' => $disabledStatuses]);
    }

    public function update($id)
    {
        $status = request()->get('status');
        list($votingTour, $errors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
        $oldStatus = $votingTour ? $votingTour->status : VotingTour::STATUS_FINISHED;
        $addition = '';
        list($data, $errors) = api_result(ApiVotingTour::class, 'changeStatus', ['new_status' => $status]);

        if (empty($errors)) {
            if ($oldStatus != $status && ($status == VotingTour::STATUS_VOTING || $status == VotingTour::STATUS_BALLOTAGE)) {
                //send emails to all orgs - voting is open
                $sender = auth()->guard('backend')->user()->id;

                if ($status == VotingTour::STATUS_BALLOTAGE) {
                    $addition = ' - ' . __('custom.ballotage');
                }

                $bulkData = [
                    'sender_user_id'   => $sender,
                    'subject'          => __('custom.vote_invite') . $addition,
                    'body'             => __('custom.use_right_vote', ['name' => $votingTour->name]). '<a href="'. route('organisation.vote').'"> '. __('custom.votingmenu'). '</a>',
                ];

                list($sent, $errors) = api_result(ApiMessage::class, 'sendBulkMessagesToOrg',  $bulkData);

                $this->sendEmails($status);
            }

            if ($oldStatus != $status && ($status == VotingTour::STATUS_RANKING)) {
                $sender = auth()->guard('backend')->user()->id;

                $bulkData = [
                    'sender_user_id'   => $sender,
                    'subject'          => __('custom.results_invite'),
                    'body'             => __('custom.results_from_last_tour', ['name' => $votingTour->name]) .' <a href="'. route('list.ranking').'"> '. __('custom.results'). '</a>',
                ];

                list($sent, $errors) = api_result(ApiMessage::class, 'sendBulkMessagesToOrg', $bulkData);

                $this->sendResultsEmails($votingTour);
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

    private function sendResultsEmails($votingTour)
    {
        try {
            SendResultsInvitation::dispatch($votingTour);
        } catch (\Exception $e) {
            logger()->error('Send results invites error: '. $e->getMessage());
            session()->flash('alert-info', __('custom.send_results_invites_failed'));
        }
    }

    private function sendEmails($status)
    {
        try {
            SendAllVoteInvites::dispatch($status);
        } catch (\Exception $e) {
            logger()->error('Send vote invites error: '. $e->getMessage());
            session()->flash('alert-info', __('custom.send_vote_invites_failed'));
        }
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
            $cacheKey = VotingTour::getCacheKey($votingTour->id);

            // check if vote result is cached
            if (Cache::has($cacheKey)) {
                $dataFromCache = Cache::get($cacheKey);
                $dataFromCache['listData'] = collect($dataFromCache['listData']);
                if (request()->has('download')) {
                    $filename = 'voteResults.csv';
                    $tempname = tempnam(sys_get_temp_dir(), 'csv_');
                    $temp = fopen($tempname, 'w+');
                    $path = stream_get_meta_data($temp)['uri'];

                    fputcsv($temp, [
                        __('custom.organisation'),
                        __('custom.eik'),
                        __('custom.votes')
                    ]);

                    $statuses = \App\Organisation::getStatuses();

                    foreach($dataFromCache['listData'] as $singleOrgData) {
                        fputcsv($temp, [
                            $singleOrgData->name,
                            $singleOrgData->eik,
                            $singleOrgData->votes,
                        ]);
                    }

                    $headers = ['Content-Type' => 'text/csv'];

                    $logData = [
                        'module' => ActionsHistory::VOTES,
                        'action' => ActionsHistory::TYPE_DOWNLOADED
                    ];

                    ActionsHistory::add($logData);

                    return response()->download($path, $filename, $headers)->deleteFileAfterSend(true);
                }

                $dataFromCache['listData'] = $dataFromCache['listData']->forPage(1, 100);

                return view('tours.ranking', [
                    'listTitle'      => $votingTour->name,
                    'listData'       => $dataFromCache['listData'],
                    'route'          => request()->segment(1) == 'admin' ? 'admin.ranking' : 'admin.org_edit',
                    'showBallotage'  => $dataFromCache['showBallotage'],
                    'stats'          => $dataFromCache['stats'],
                    'fullWidth'      => true,
                    'ajaxMethod'     => 'rankingAdminAjax',
                    'orgNotEditable' => true,
                    'tourId'         => $id
                ]);
            }

            // get vote status
            list($voteStatus, $listErrors) = api_result(ApiVote::class, 'getVoteStatus', ['tour_id' => $votingTour->id]);

            if (!empty($listErrors)) {
                $errors['message'] = __('custom.list_ranking_fail');
            } elseif (!empty($voteStatus)) {
                // list ranking
                $params = [
                    'tour_id' => $votingTour->id,
                    'status'  => VotingTour::STATUS_VOTING
                ];
                list($listData, $listErrors) = api_result(ApiVote::class, 'ranking', $params);

                if (!empty($listErrors)) {
                    $errors['message'] = __('custom.list_ranking_fail');
                } elseif (!empty($listData)) {
                    // count registered organisations
                    $registered = Organisation::countRegistered($votingTour->id);

                    // count voted organisations
                    $voted = Organisation::countVoted($params['tour_id'], $params['status']);

                    // calculate voter turnout
                    $stats['voting'] = [
                        'all'     => $registered,
                        'voted'   => $voted,
                        'percent' => 0
                    ];
                    if ($stats['voting']['all'] > 0) {
                        $stats['voting']['percent'] = round($stats['voting']['voted'] / $stats['voting']['all'] * 100, 2);
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
                            // count voted organisations
                            $voted = Organisation::countVoted($params['tour_id'], $params['status']);

                            if (!empty($stats)) {
                                // calculate ballotage voter turnout
                                $stats['ballotage'] = [
                                    'all'     => $stats['voting']['all'],
                                    'voted'   => $voted,
                                    'percent' => 0
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

            // cache computed vote results for 1 hour
            Cache::put($cacheKey, ['listData' => $listData, 'stats' => $stats, 'showBallotage' => $showBallotage], now()->addMinutes(60));
        } else {
            return redirect()->route('admin.voting_tour.list');
        }

        return view('tours.ranking', [
            'listTitle'      => $votingTour->name,
            'listData'       => collect($listData)->forPage(1, 100),
            'route'          => request()->segment(1) == 'admin' ? 'list.ranking' : 'admin.org_edit',
            'showBallotage'  => $showBallotage,
            'stats'          => $stats,
            'fullWidth'      => true,
            'ajaxMethod'     => 'rankingAdminAjax',
            'orgNotEditable' => true,
            'tourId'         => $id
        ])->withErrors($errors);
    }

    public function listAdminRankingAjax(Request $request, $id)
    {
        $dataFromCache = [];
        $dataFromCache['listData'] = [];

        $page = $request->offsetGet('page');

        if (!empty($id)) {
            $cacheKey = VotingTour::getCacheKey($id);

            if (Cache::has($cacheKey)) {
                $dataFromCache = Cache::get($cacheKey);
                $dataFromCache['listData'] = collect($dataFromCache['listData']);
                $dataFromCache['listData'] = $dataFromCache['listData']->forPage($page, 100);
            }
        }

        return view('partials.ranking-rows', [
            'listData' => $dataFromCache['listData'],
            'counter'  => $request->offsetGet('consecNum'),
            'orgNotEditable' => true
        ]);
    }
}
