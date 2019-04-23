<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\VoteController as ApiVote;
use App\VotingTour;
use App\Organisation;

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

    public function listRegistered($id = null)
    {
        $showLinks = [];
        $listData = [];
        $orgData = [];

        if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_UPCOMING) {
            $showLinks['registered'] = true;
            $showLinks['candidates'] = true;
            if (!in_array($this->votingTour->status, VotingTour::getRegStatuses())) {
                $showLinks['voted'] = true;
                if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_VOTING) {
                    $showLinks['ranking'] = true;
                }
            }

            $params = [
                'filters'     => [
                    'statuses' => Organisation::getApprovedStatuses()
                ]
            ];

            list($listData, $listErrors) = api_result(ApiOrganisation::class, 'search', $params);

            if (isset($id)) {
                list($orgData, $orgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $id]);
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks' => $showLinks,
            'listTitle' => __('custom.registered'),
            'listData'  => $listData,
            'orgData'   => $orgData,
            'route'     => 'list.registered',
            //'errors'    => $errors,
        ]);
    }

    public function listCandidates($id = null)
    {
        $showLinks = [];
        $listData = [];
        $orgData = [];

        if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_UPCOMING) {
            $showLinks['registered'] = true;
            $showLinks['candidates'] = true;
            if (!in_array($this->votingTour->status, VotingTour::getRegStatuses())) {
                $showLinks['voted'] = true;
                if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_VOTING) {
                    $showLinks['ranking'] = true;
                }
            }

            $params = [
                'filters' => [
                    'statuses' => [Organisation::STATUS_CANDIDATE, Organisation::STATUS_BALLOTAGE]
                ]
            ];
            list($listData, $listErrors) = api_result(ApiOrganisation::class, 'search', $params);

            if (isset($id)) {
                list($orgData, $orgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $id]);
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks' => $showLinks,
            'listTitle' => __('custom.candidates'),
            'listData'  => $listData,
            'orgData'   => $orgData,
            'route'     => 'list.candidates',
            //'errors'    => $errors,
        ]);
    }

    public function listVoted($id = null)
    {
        $showLinks = [];
        $listData = [];
        $orgData = [];

        if (!empty($this->votingTour) && !in_array($this->votingTour->status, VotingTour::getRegStatuses())) {
            $showLinks['registered'] = true;
            $showLinks['candidates'] = true;
            $showLinks['voted'] = true;
            if (!empty($this->votingTour) && $this->votingTour->status != VotingTour::STATUS_VOTING) {
                $showLinks['ranking'] = true;
            }

            list($listData, $listErrors) = api_result(ApiVote::class, 'listVoters');

            if (isset($id)) {
                list($orgData, $orgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $id]);
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks' => $showLinks,
            'listTitle' => __('custom.voted'),
            'listData'  => $listData,
            'orgData'   => $orgData,
            'route'     => 'list.voted',
            //'errors'    => $errors,
        ]);
    }

    public function listRanking($id = null)
    {
        $showLinks = [];
        $listData = [];
        $orgData = [];
        $showBallotage = false;

        if (!empty($this->votingTour) && in_array($this->votingTour->status, VotingTour::getRankingStatuses())) {
            $showLinks['registered'] = true;
            $showLinks['candidates'] = true;
            $showLinks['voted'] = true;
            $showLinks['ranking'] = true;

            list($voteStatus, $errors) = api_result(ApiVote::class, 'getVoteStatus', ['tour_id' => $this->votingTour->id]);
            if (!empty($voteStatus)) {
                $params = [
                    'tour_id' => $this->votingTour->id,
                    'status' => VotingTour::STATUS_VOTING
                ];
                list($listData, $listErrors) = api_result(ApiVote::class, 'ranking', $params);
                /*if (!empty($listData) && $voteStatus->id == VotingTour::STATUS_BALLOTAGE) {
                    $params['status'] = $voteStatus;
                    list($ballotageData, $ballotageErrors) = api_result(ApiVote::class, 'ranking', $params);
                    if (!empty($ballotageData)) {
                        $showBallotage = true;
                    }
                }*/

                if (isset($id)) {
                    list($orgData, $orgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $id]);
                }
            }
        } else {
            return redirect('/');
        }

        return view('home.index', [
            'showLinks'     => $showLinks,
            'listTitle'     => __('custom.ranking'),
            'listData'      => $listData,
            'orgData'       => $orgData,
            'route'         => 'list.ranking',
            'isRanking'     => true,
            'showBallotage' => $showBallotage,
            //'errors'        => $errors,
        ]);
    }
}
