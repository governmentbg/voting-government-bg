<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\VoteController as ApiVote;
use App\Organisation;
use App\VotingTour;
use App\Vote;
use Illuminate\Support\Facades\Cache;

class VoteController extends BaseFrontendController
{
    public function __construct()
    {
        parent::__construct();
        $this->addBreadcrumb(__('breadcrumbs.start'), route('organisation.view'));
        $this->addBreadcrumb(__('breadcrumbs.voting'), route('organisation.vote'));
    }

    public function view(Request $request)
    {
        $latestVoteArray = [];
        list($votingTourData, $tourErrors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');

        if ($votingTourData->status == VotingTour::STATUS_VOTING) {
            $memberStatus = [Organisation::STATUS_CANDIDATE];
        } elseif ($votingTourData->status == VotingTour::STATUS_BALLOTAGE) {
            $memberStatus = [Organisation::STATUS_BALLOTAGE];
        } else {
            $memberStatus = [];
        }

        list($organisations, $orgErrors) = api_result(ApiOrganisation::class, 'search', [
            'filters' => [
                'statuses'         => $memberStatus,
                'only_main_fields' => true,
            ],
        ]);

        list($latestVote, $latestVoteErrors) = api_result(ApiVote::class, 'getLatestVote', ['org_id' => \Auth::user()->org_id]);

        if (!empty($latestVote)) {
            $latestVoteArray = explode(',', $latestVote->vote_data);
        }

        $maxVotes = \App\Vote::MAX_VOTES;
        if ($votingTourData->status == VotingTour::STATUS_BALLOTAGE) {
            //max votes for ballotage
            $maxVotes = $this->getMaxVotes($votingTourData);
        }

        if (!empty($latestVote) && !isset($request->change)) {
            return view('organisation.latest_vote', [
                'latestVoteData'  => $latestVoteArray,
                'orgList'         => $organisations,
                'maxVotes'        => $maxVotes,
            ]);
        }

        return view('organisation.vote', [
            'orgList'        => $organisations,
            'latestVoteData' => $latestVoteArray,
            'maxVotes'       => $maxVotes,
        ]);
    }

    public function vote(Request $request)
    {
        $loggedUserOrg = \Auth::user()->org_id;

        $voteListArray = $request->offsetGet('votefor');

        $voteString = '';
        foreach ($voteListArray as $index => $orgId) {
            if ($voteString == '') {
                $voteString .= $orgId;
            } else {
                $voteString .= ',' . $orgId;
            }
        }

        list($vote, $voteErrors) = api_result(ApiVote::class, 'vote', [
            'org_id'   => $loggedUserOrg,
            'org_list' => $voteString,
        ]);

        if (empty($voteErrors)) {
            list($organisations, $orgErrors) = api_result(ApiOrganisation::class, 'search', [
                'filters' => [
                    'statuses'         => [Organisation::STATUS_CANDIDATE, Organisation::STATUS_BALLOTAGE],
                    'only_main_fields' => true,
                ],
            ]);

            list($latestVote, $latestVoteErrors) = api_result(ApiVote::class, 'getLatestVote', ['org_id' => \Auth::user()->org_id]);

            $latestVoteArray = [];

            if (!empty($latestVote)) {
                $latestVoteArray = explode(',', $latestVote->vote_data);
            }

            list($loggedOrg, $loggedOrgErrors) = api_result(ApiOrganisation::class, 'getData', [
                'org_id' => \Auth::user()->org_id,
            ]);

            $mailResult = sendEmail('emails/vote_confirmation', ['name' => $loggedOrg->name], $loggedOrg->email, __('custom.vote_successful'));

            if (!$mailResult) {
                $request->session()->flash('alert-danger', __('custom.error_mail_confirmation'));
            }

            $request->session()->flash('alert-success', __('custom.vote_success'));

            return view('organisation.latest_vote', [
                'latestVoteData'  => $latestVoteArray,
                'orgList'         => $organisations,
            ]);
        }

        foreach ($voteErrors as $singleError) {
            $request->session()->flash('alert-danger', $singleError);
        }

        return redirect()->back();
    }

    /**
     * Get maximum number of votes for ballotage. The number is MAX_VOTES minus number of elected orgs.
     * @param  stdClass $votingTour
     * @return integer
     */
    private function getMaxVotes($votingTour)
    {
        if ($votingTour->status == VotingTour::STATUS_VOTING) {
            return \App\Vote::MAX_VOTES;
        }

        $cacheKey = VotingTour::getCacheKey($votingTour->id, 'max-votes');
        if($votingTour->status == VotingTour::STATUS_BALLOTAGE){
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
        }

        $params = [
                    'tour_id' => $this->votingTour->id,
                    'status'  => VotingTour::STATUS_VOTING,
                ];
        list($listData, $listErrors) = api_result(ApiVote::class, 'ranking', $params);
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
        $electedOrgs = 0;

        foreach ($listData as $data) {
            if ($setBallotage && $data->votes > $votesLimit) {
                $electedOrgs++;
            }
        }

        Cache::put($cacheKey, Vote::MAX_VOTES - $electedOrgs, now()->addMinutes(60));
        return Vote::MAX_VOTES - $electedOrgs;
    }
}
