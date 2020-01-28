<?php

namespace App\Http\Controllers\Admin;

ini_set('max_execution_time', 300);

use App\Http\Controllers\BaseAdminController;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\VoteController as ApiVote;
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
        $votingTour = !empty($this->votingTour) ? $this->votingTour : [];
        $oldStatus = $votingTour ? $votingTour->status : VotingTour::STATUS_FINISHED;
        $addition = '';

        $cancelTour = ($oldStatus != VotingTour::STATUS_RANKING && $status == VotingTour::STATUS_FINISHED);

        if ($status != VotingTour::STATUS_RANKING && !$cancelTour) {
            list($data, $errors) = api_result(ApiVotingTour::class, 'changeStatus', ['new_status' => $status]);
        }

        if (empty($errors) && !empty($votingTour)) {
            if ($oldStatus != $status && ($status == VotingTour::STATUS_VOTING || $status == VotingTour::STATUS_BALLOTAGE)) {
                // send emails to all orgs - voting is open
                $sender = auth()->guard('backend')->user()->id;

                if ($status == VotingTour::STATUS_BALLOTAGE) {
                    $addition = ' - ' . __('custom.ballotage');
                }

                $bulkData = [
                    'sender_user_id'   => $sender,
                    'subject'          => __('custom.vote_invite') .' '. $addition,
                    'body'             => __('custom.greetings') .',<br><br>'. __('custom.you_are_registered') .'<br>'. __('custom.to_vote_link') .'<a href="'. route('organisation.vote') .'">'. uptrans('custom.vote') .'</a>',
                ];

                list($sent, $errors) = api_result(ApiMessage::class, 'sendBulkMessagesToOrg',  $bulkData);

                $this->sendEmails($status);
            }

            if ($oldStatus != $status && ($status == VotingTour::STATUS_RANKING || $cancelTour)) {
                \DB::beginTransaction();
                list($data, $changeErrors) = api_result(ApiVotingTour::class, 'changeStatus', ['new_status' => $status]);

                if ($cancelTour) {
                    list($result, $rankErrors) = api_result(ApiVote::class, 'cancelTour');
                } else {
                    $statusForRank = $oldStatus == VotingTour::STATUS_VOTING ? Vote::TOUR_RANKING : Vote::TOUR_BALLOTAGE_RANKING;

                    list($result, $rankErrors) = api_result(ApiVote::class, 'ranking', ['status' => $statusForRank]);
                }

                if (empty($rankErrors) && empty($changeErrors)) {
                    \DB::commit();
                } else {
                    \DB::rollBack();

                    session()->flash('alert-danger', __('custom.error_updating_tour'));
                    return redirect($this->redirectTo);
                }

                if ($status == VotingTour::STATUS_RANKING) {
                    // clear cached ranking
                    $cacheKey = VotingTour::getCacheKey($votingTour->id);
                    if (Cache::has($cacheKey)) {
                        Cache::forget($cacheKey);
                    }

                    $sender = auth()->guard('backend')->user()->id;

                    $bulkData = [
                        'sender_user_id'   => $sender,
                        'subject'          => __('custom.results_invite'),
                        'body'             => __('custom.greetings') .',<br><br>'. __('custom.ranking_for') .' '. $votingTour->name .' '. __('custom.was_done') .'<br>'. __('custom.results_available') .': <a href="'. route('list.ranking') .'">'. uptrans('custom.results') .'</a>'
                    ];

                    list($sent, $errors) = api_result(ApiMessage::class, 'sendBulkMessagesToOrg', $bulkData);

                    $this->sendResultsEmails($votingTour);
                }
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
        $votingCount = 0;
        $stats = [];
        $errors = [];

        list($votingTour, $tourErrors) = api_result(ApiVotingTour::class, 'getData', ['tour_id' => $id]);

        if (!empty($votingTour) && in_array($votingTour->status, VotingTour::getRankingStatuses())) {
            $cacheKey = VotingTour::getCacheKey($votingTour->id);

            // check if vote result is cached
            if (Cache::has($cacheKey)) {
                $dataFromCache = Cache::get($cacheKey);

                if (isset($dataFromCache['listData']) && isset($dataFromCache['stats']) && isset($dataFromCache['votingCount'])) {
                    $dataFromCache['listData'] = collect($dataFromCache['listData']);

                    // csv download
                    if (request()->has('download')) {
                        $fileData = $this->generateCSV($dataFromCache);
                        return response()->download($fileData['path'], $fileData['filename'], $fileData['headers'])->deleteFileAfterSend(true);
                    }

                    $dataFromCache['listData'] = $dataFromCache['listData']->forPage(1, 100);

                    return view('tours.ranking', [
                        'listTitle'      => $votingTour->name,
                        'listData'       => $dataFromCache['listData'],
                        'route'          => request()->segment(1) == 'admin' ? 'admin.ranking' : 'admin.org_edit',
                        'votingCount'    => $dataFromCache['votingCount'],
                        'stats'          => $dataFromCache['stats'],
                        'fullWidth'      => true,
                        'ajaxMethod'     => 'rankingAdminAjax',
                        'orgNotEditable' => true,
                        'tourId'         => $id
                    ]);
                }
            }

            // list ranking
            $params = [
                'tour_id' => $votingTour->id
            ];
            list($listData, $listErrors) = api_result(ApiVote::class, 'getLatestRanking', $params);

            if (!empty($listErrors)) {
                $errors['message'] = __('custom.list_ranking_fail');
            } else {
                if (!empty($listData) && isset($listData->ranking) && !empty($listData->ranking)) {
                    $votingCount = $listData->voting_count;
                    if (isset($listData->voter_turnout) && !empty($listData->voter_turnout)) {
                        $stats = $listData->voter_turnout;
                    } else {
                        $errors['message'] = __('custom.voter_turnout_fail');
                    }
                    $listData = $listData->ranking;
                }

                if (request()->has('download')) {
                    $fileData = $this->generateCSV(['listData' => $listData, 'votingCount' => $votingCount]);
                    return response()->download($fileData['path'], $fileData['filename'], $fileData['headers'])->deleteFileAfterSend(true);
                }

                // cache computed vote results for 1 hour
                Cache::put($cacheKey, ['listData' => $listData, 'stats' => $stats, 'votingCount' => $votingCount], now()->addMinutes(60));
            }
        } else {
            return redirect()->route('admin.voting_tour.list');
        }

        return view('tours.ranking', [
            'listTitle'      => $votingTour->name,
            'listData'       => collect($listData)->forPage(1, 100),
            'route'          => request()->segment(1) == 'admin' ? 'list.ranking' : 'admin.org_edit',
            'votingCount'    => $votingCount,
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

    private function generateCSV($data) {
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
        for ($votingIndex = 1; $votingIndex < $data['votingCount']; $votingIndex++) {
            $csvRow[] = __('custom.ballotage_votes') . ($data['votingCount'] > 1 ? ' '. $votingIndex : '');
        }
        fputcsv($temp, $csvRow);

        $counter = 0;
        foreach ($data['listData'] as $singleOrgData) {
            $csvRow = [
                ++$counter,
                $singleOrgData->name,
                $singleOrgData->eik,
            ];
            for ($votingIndex = 0; $votingIndex < $data['votingCount']; $votingIndex++) {
                $csvRow[] = isset($singleOrgData->votes->{$votingIndex}) ? $singleOrgData->votes->{$votingIndex} : null;
            }
            fputcsv($temp, $csvRow);
        }

        $headers = ['Content-Type' => 'text/csv'];

        return $fileData = ['path' => $path, 'filename' => $filename, 'headers' => $headers];
    }
}
