<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 300);

use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\VoteController as ApiVote;
use App\VotingTour;
use App\Organisation;
use App\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicController extends BaseFrontendController
{
    public function __construct()
    {
        parent::__construct();

        $showRegister = (!empty($this->votingTour) && $this->votingTour->status == VotingTour::STATUS_OPENED_REG);

        view()->share('showRegister', $showRegister);
    }

    public function index()
    {
        if (auth()->guard('backend')->check()) {
            return redirect()->route('admin.org_list');
        }

        if (auth()->check()) {
            return redirect()->route('organisation.view');
        }

        if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_UPCOMING) {
            return redirect()->action('PublicController@listRegistered');
        }

        return view('home.index');
    }

    public function listRegistered(Request $request)
    {
        $showLinks = [];
        $listData = [];
        $errors = [];

        if (session()->has('errors')) {
            $errors = session()->get('errors')->messages();
        }

        $eik = $request->offsetGet('eik');

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
                    'statuses'         => array_merge(Organisation::getApprovedStatuses(), [Organisation::STATUS_DECLASSED]),
                    'only_main_fields' => true
                ],
                'with_pagination' => $request->has('download') ? false : true
            ];

            if (isset($eik)) {
                $params['filters']['eik'] = $eik;
            }

            list($listData, $listErrors) = api_result(ApiOrganisation::class, 'search', $params);

            if ($request->has('download')) {
                $filename = 'organisationsRegistered.csv';
                $tempname = tempnam(sys_get_temp_dir(), 'csv_');
                $temp = fopen($tempname, 'w+');
                $path = stream_get_meta_data($temp)['uri'];

                fputcsv($temp, [
                    __('custom.organisation'),
                    __('custom.candidate'),
                    __('custom.eik'),
                    __('custom.registered_at')
                ]);

                $statuses = \App\Organisation::getStatuses();

                foreach($listData as $singleOrg) {
                    fputcsv($temp, [
                        $singleOrg->name,
                        $singleOrg->is_candidate == true ? __('custom.status_yes') :  __('custom.status_no'),
                        $singleOrg->eik,
                        $singleOrg->created_at,
                    ]);
                }

                $headers = ['Content-Type' => 'text/csv'];

                return response()->download($path, $filename, $headers)->deleteFileAfterSend(true);
            }

            if (!empty($listErrors)) {
                $errors['message'] = __('custom.list_reg_org_fail');
            } else {
                $listData = !empty($listData->data) ? $this->paginate($listData) : [];
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks'  => $showLinks,
            'listTitle'  => __('custom.registered'),
            'listData'   => $listData,
            'eik'        => $eik,
            'route'      => 'list.registered',
            'ajaxMethod' => 'registeredAjax'
        ])->withErrors($errors);
    }

    public function listCandidates(Request $request)
    {
        $showLinks = [];
        $listData = [];
        $errors = [];

        $eik = $request->offsetGet('eik');

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
                    'statuses'         => [Organisation::STATUS_CANDIDATE, Organisation::STATUS_BALLOTAGE, Organisation::STATUS_DECLASSED],
                    'only_main_fields' => true
                ],
                'with_pagination' => $request->has('download') ? false : true
            ];

            if (isset($eik)) {
                $params['filters']['eik'] = $eik;
            }

            list($listData, $listErrors) = api_result(ApiOrganisation::class, 'search', $params);

            if ($request->has('download')) {
                $filename = 'organisationsCandidates.csv';
                $tempname = tempnam(sys_get_temp_dir(), 'csv_');
                $temp = fopen($tempname, 'w+');
                $path = stream_get_meta_data($temp)['uri'];

                fputcsv($temp, [
                    __('custom.organisation'),
                    __('custom.candidate'),
                    __('custom.eik'),
                    __('custom.registered_at')
                ]);

                $statuses = \App\Organisation::getStatuses();

                foreach($listData as $singleOrg) {
                    fputcsv($temp, [
                        $singleOrg->name,
                        $singleOrg->is_candidate == true ? __('custom.status_yes') :  __('custom.status_no'),
                        $singleOrg->eik,
                        $singleOrg->created_at,
                    ]);
                }

                $headers = ['Content-Type' => 'text/csv'];

                return response()->download($path, $filename, $headers)->deleteFileAfterSend(true);
            }

            if (!empty($listErrors)) {
                $errors['message'] = __('custom.list_candidates_fail');
            } else {
                $listData = !empty($listData->data) ? $this->paginate($listData) : [];
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks'  => $showLinks,
            'listTitle'  => __('custom.candidates'),
            'listData'   => $listData,
            'eik'        => $eik,
            'route'      => 'list.candidates',
            'ajaxMethod' => 'candidatesAjax'
        ])->withErrors($errors);
    }

    public function listVoted(Request $request)
    {
        $showLinks = [];
        $listData = [];
        $errors = [];

        $eik = $request->offsetGet('eik');

        if (!empty($this->votingTour) && !in_array($this->votingTour->status, VotingTour::getRegStatuses())) {
            // set links that have to be displayed
            $showLinks['registered'] = true;
            $showLinks['candidates'] = true;
            $showLinks['voted'] = true;
            if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_VOTING) {
                $showLinks['ranking'] = true;
            }

            // list voted organisations
            $params = [];

            $params['with_pagination'] = $request->has('download') ?  false : true;

            if (isset($eik)) {
                $params['filters']['eik'] = $eik;
            }

            list($listData, $listErrors) = api_result(ApiVote::class, 'listVoters', $params);

            if ($request->has('download')) {
                $filename = 'organisationsVoted.csv';
                $tempname = tempnam(sys_get_temp_dir(), 'csv_');
                $temp = fopen($tempname, 'w+');
                $path = stream_get_meta_data($temp)['uri'];

                fputcsv($temp, [
                    __('custom.organisation'),
                    __('custom.candidate'),
                    __('custom.eik'),
                    __('custom.registered_at')
                ]);

                $statuses = \App\Organisation::getStatuses();

                foreach($listData as $singleOrg) {
                    fputcsv($temp, [
                        $singleOrg->name,
                        $singleOrg->is_candidate == true ? __('custom.status_yes') :  __('custom.status_no'),
                        $singleOrg->eik,
                        $singleOrg->created_at,
                    ]);
                }

                $headers = ['Content-Type' => 'text/csv'];

                return response()->download($path, $filename, $headers)->deleteFileAfterSend(true);
            }

            if (!empty($listErrors)) {
                $errors['message'] = __('custom.list_voted_org_fail');
            } else {
                $listData = !empty($listData->data) ? $this->paginate($listData) : [];
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks'  => $showLinks,
            'listTitle'  => __('custom.voted'),
            'listData'   => $listData,
            'eik'        => $eik,
            'route'      => 'list.voted',
            'ajaxMethod' => 'votedAjax'
        ])->withErrors($errors);
    }

    public function listRanking(Request $request)
    {
        $showLinks = [];
        $listData = [];
        $showBallotage = false;
        $stats = [];
        $errors = [];

        if (!empty($this->votingTour) && in_array($this->votingTour->status, VotingTour::getRankingStatuses())) {
            $cacheKey = VotingTour::getCacheKey($this->votingTour->id);

            // set links that have to be displayed
            $showLinks['registered'] = true;
            $showLinks['candidates'] = true;
            $showLinks['voted'] = true;
            $showLinks['ranking'] = true;

            // check if vote result is cached
            if (Cache::has($cacheKey)) {
                $dataFromCache = Cache::get($cacheKey);
                $dataFromCache['listData'] = collect($dataFromCache['listData']);

                if ($request->has('download')) {
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

                    return response()->download($path, $filename, $headers)->deleteFileAfterSend(true);
                }

                $dataFromCache['listData'] = $dataFromCache['listData']->forPage(1, 100);

                return view('home.index', [
                    'showLinks'     => $showLinks,
                    'listTitle'     => __('custom.ranking'),
                    'listData'      => $dataFromCache['listData'],
                    'route'         => 'list.ranking',
                    'isRanking'     => true,
                    'showBallotage' => $dataFromCache['showBallotage'],
                    'stats'         => $dataFromCache['stats'],
                    'ajaxMethod'    => 'rankingAjax',
                    'orgNotEditable' => false
                ]);
            }

            // get vote status
            list($voteStatus, $listErrors) = api_result(ApiVote::class, 'getVoteStatus', ['tour_id' => $this->votingTour->id]);

            if (!empty($listErrors)) {
                $errors['message'] = __('custom.list_ranking_fail');
            } elseif (!empty($voteStatus)) {
                // list ranking
                $params = [
                    'tour_id' => $this->votingTour->id,
                    'status'  => VotingTour::STATUS_VOTING
                ];
                list($listData, $listErrors) = api_result(ApiVote::class, 'ranking', $params);

                if (!empty($listErrors)) {
                    $errors['message'] = __('custom.list_ranking_fail');
                } elseif (!empty($listData)) {
                    // count registered organisations
                    $registered = Organisation::countRegistered($this->votingTour->id);

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

            Cache::put($cacheKey, ['listData' => $listData, 'stats' => $stats, 'showBallotage' => $showBallotage], now()->addMinutes(60));
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks'     => $showLinks,
            'listTitle'     => __('custom.ranking'),
            'listData'      => collect($listData)->forPage(1, 100),
            'route'         => 'list.ranking',
            'isRanking'     => true,
            'showBallotage' => $showBallotage,
            'stats'         => $stats,
            'ajaxMethod'    => 'rankingAjax',
            'orgNotEditable' => false
        ])->withErrors($errors);
    }

    public function listRegisteredAjax(Request $request)
    {
        $listData = [];

        if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_UPCOMING) {
            $params = [
                'filters' => [
                    'statuses'         => Organisation::getApprovedStatuses(),
                    'only_main_fields' => true
                ],
                'with_pagination' => true
            ];

            list($listData, $listErrors) = api_result(ApiOrganisation::class, 'search', $params);

            if (empty($listErrors)) {
                $listData = !empty($listData->data) ? $this->paginate($listData) : [];
            }
        }

        return view('partials.public-list-rows', [
            'listData' => $listData,
            'counter'  => $request->offsetGet('consecNum')
        ]);
    }

    public function listCandidatesAjax(Request $request)
    {
        $listData = [];

        if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_UPCOMING) {
            $params = [
                'filters' => [
                    'statuses'         => [Organisation::STATUS_CANDIDATE, Organisation::STATUS_BALLOTAGE],
                    'only_main_fields' => true
                ],
                'with_pagination' => true
            ];

            list($listData, $listErrors) = api_result(ApiOrganisation::class, 'search', $params);

            if (empty($listErrors)) {
                $listData = !empty($listData->data) ? $this->paginate($listData) : [];
            }
        }

        return view('partials.public-list-rows', [
            'listData' => $listData,
            'counter'  => $request->offsetGet('consecNum')
        ]);
    }

    public function listVotedAjax(Request $request)
    {
        $listData = [];

        if (!empty($this->votingTour) && !in_array($this->votingTour->status, VotingTour::getRegStatuses())) {
            list($listData, $listErrors) = api_result(ApiVote::class, 'listVoters');

            if (empty($listErrors)) {
                $listData = !empty($listData->data) ? $this->paginate($listData) : [];
            }
        }

        return view('partials.public-list-rows', [
            'listData' => $listData,
            'counter'  => $request->offsetGet('consecNum')
        ]);
    }

    public function listRankingAjax(Request $request)
    {
        $dataFromCache = [];
        $dataFromCache['listData'] = [];

        $page = $request->offsetGet('page');

        if (!empty($this->votingTour) && in_array($this->votingTour->status, VotingTour::getRankingStatuses())) {
            $cacheKey = VotingTour::getCacheKey($this->votingTour->id);
            if (Cache::has($cacheKey)) {
                $dataFromCache = Cache::get($cacheKey);
                $dataFromCache['listData'] = collect($dataFromCache['listData']);
                $dataFromCache['listData'] = $dataFromCache['listData']->forPage($page, 100);
            }
        }

        return view('partials.ranking-rows', [
            'listData' => $dataFromCache['listData'],
            'counter'  => $request->offsetGet('consecNum'),
            'orgNotEditable' => false
        ]);
    }
}
