<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 300);

use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\VoteController as ApiVote;
use App\VotingTour;
use App\Organisation;
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
        session()->reflash();

        if (auth()->guard('backend')->check()) {
            return redirect()->route('admin.org_list');
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
                    'statuses'         => Organisation::getApprovedStatuses(),
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

                foreach ($listData as $singleOrg) {
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
                    'statuses'         => Organisation::getApprovedCandidateStatuses(),
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

                foreach ($listData as $singleOrg) {
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

                foreach ($listData as $singleOrg) {
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
        $votingCount = 0;
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

                if (isset($dataFromCache['listData']) && isset($dataFromCache['stats']) && isset($dataFromCache['votingCount'])) {
                    $dataFromCache['listData'] = collect($dataFromCache['listData']);

                    // csv download
                    if ($request->has('download')) {
                        $filename = 'voteResults.csv';
                        $tempname = tempnam(sys_get_temp_dir(), 'csv_');
                        $temp = fopen($tempname, 'w+');
                        $path = stream_get_meta_data($temp)['uri'];

                        $csvRow = [
                            __('custom.number'),
                            __('custom.organisation'),
                            __('custom.eik'),
                            __('custom.votes')
                        ];
                        for ($votingIndex = 1; $votingIndex < $dataFromCache['votingCount']; $votingIndex++) {
                            $csvRow[] = __('custom.ballotage_votes') . ($dataFromCache['votingCount'] > 1 ? ' '. $votingIndex : '');
                        }
                        fputcsv($temp, $csvRow);

                        $counter = 0;
                        foreach ($dataFromCache['listData'] as $singleOrgData) {
                            $csvRow = [
                                ++$counter,
                                $singleOrgData->name,
                                $singleOrgData->eik,
                            ];
                            for ($votingIndex = 0; $votingIndex < $dataFromCache['votingCount']; $votingIndex++) {
                                $csvRow[] = isset($singleOrgData->votes->{$votingIndex}) ? $singleOrgData->votes->{$votingIndex} : null;
                            }
                            fputcsv($temp, $csvRow);
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
                        'votingCount'   => $dataFromCache['votingCount'],
                        'stats'         => $dataFromCache['stats'],
                        'ajaxMethod'    => 'rankingAjax',
                        'orgNotEditable' => false
                    ]);
                }
            }

            // list ranking
            $params = [
                'tour_id' => $this->votingTour->id
            ];
            list($listData, $listErrors) = api_result(ApiVote::class, 'getLatestRanking', $params);

            if (!empty($listErrors)) {
                $errors['message'] = __('custom.list_ranking_fail');
            } elseif (!empty($listData) && isset($listData->ranking) && !empty($listData->ranking)) {
                $votingCount = $listData->voting_count;
                if (isset($listData->voter_turnout) && !empty($listData->voter_turnout)) {
                    $stats = $listData->voter_turnout;
                } else {
                    $errors['message'] = __('custom.voter_turnout_fail');
                }
                $listData = $listData->ranking;
            }

            Cache::put($cacheKey, ['listData' => $listData, 'stats' => $stats, 'votingCount' => $votingCount], now()->addMinutes(60));
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks'     => $showLinks,
            'listTitle'     => __('custom.ranking'),
            'listData'      => collect($listData)->forPage(1, 100),
            'route'         => 'list.ranking',
            'isRanking'     => true,
            'votingCount'   => $votingCount,
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
                    'statuses'         => Organisation::getApprovedCandidateStatuses(),
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
