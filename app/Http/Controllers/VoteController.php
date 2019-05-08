<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\VoteController as ApiVote;
use App\Organisation;
use App\VotingTour;

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
        } else if ($votingTourData->status == VotingTour::STATUS_BALLOTAGE) {
            $memberStatus = [Organisation::STATUS_BALLOTAGE];
        } else {
            $memberStatus = [];
        }

        list($organisations, $orgErrors) = api_result(ApiOrganisation::class, 'search', [
            'filters' => ['statuses' => $memberStatus]
        ]);

        list($latestVote, $latestVoteErrors) = api_result(ApiVote::class, 'getLatestVote', ['org_id' => \Auth::user()->org_id]);

        if (!empty($latestVote)) {
            $latestVoteArray = explode(',', $latestVote->vote_data);
        }

        if (!empty($latestVote) && !isset($request->change)) {
            return view('organisation.latest_vote', [
                'latestVoteData'  => $latestVoteArray,
                'orgList'         => $organisations
            ]);
        }

        return view('organisation.vote', [
            'orgList'        => $organisations,
            'latestVoteData' => $latestVoteArray
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
                $voteString .= ','. $orgId;
            }
        }

        list($vote, $voteErrors) = api_result(ApiVote::class, 'vote', [
            'org_id'   => $loggedUserOrg,
            'org_list' => $voteString
        ]);

        if (empty($voteErrors)) {
            list($organisations, $orgErrors) = api_result(ApiOrganisation::class, 'search', [
                'filters' => ['statuses' => [Organisation::STATUS_CANDIDATE, Organisation::STATUS_BALLOTAGE]]
            ]);

            list($latestVote, $latestVoteErrors) = api_result(ApiVote::class, 'getLatestVote', ['org_id' => \Auth::user()->org_id]);

            if (!empty($latestVote)) {
                $latestVoteArray = explode(',', $latestVote->vote_data);
            }

            list($loggedOrg, $loggedOrgErrors) = api_result(ApiOrganisation::class, 'getData', [
                'org_id' => \Auth::user()->org_id
            ]);

            $mailResult = sendEmail('emails/vote_confirmation', ['name' => $loggedOrg->name], $loggedOrg->email, __('custom.vote_successful'));

            if (!$mailResult) {
                $request->session()->flash('alert-danger', __('custom.error_mail_confirmation'));
            }

            $request->session()->flash('alert-success', __('custom.vote_success'));

            return view('organisation.latest_vote', [
                'latestVoteData'  => $latestVoteArray,
                'orgList'         => $organisations
            ]);
        } else {
            foreach ($voteErrors as $singleError) {
                $request->session()->flash('alert-danger', $singleError);
            }

            return redirect()->back();
        }
    }
}
