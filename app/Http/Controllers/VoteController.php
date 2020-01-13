<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\OrganisationController as ApiOrganisation;
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
        $organisations = [];
        $latestVoteArray = [];
        $maxVotes = 0;
        $infoLabel = '';

        $loggedUserOrg = \Auth::user()->org_id;

        list($loggedOrg, $loggedOrgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $loggedUserOrg]);

        if (empty($loggedOrg)) {
            $request->session()->flash('alert-danger', __('custom.vote_view_fail'));
        } elseif (!in_array($loggedOrg->status, Organisation::getApprovedStatuses())) {
            $infoLabel = 'custom.org_not_approved';
        } else {
            if (!empty($this->votingTour)) {
                if ($this->votingTour->status == VotingTour::STATUS_VOTING) {
                    $memberStatus = [Organisation::STATUS_CANDIDATE];
                } elseif ($this->votingTour->status == VotingTour::STATUS_BALLOTAGE) {
                    $memberStatus = [Organisation::STATUS_BALLOTAGE];
                } else {
                    $memberStatus = [];
                }

                if (!empty($memberStatus)) {
                    list($organisations, $orgErrors) = api_result(ApiOrganisation::class, 'search', [
                        'filters' => [
                            'statuses'         => $memberStatus,
                            'only_main_fields' => true,
                        ],
                    ]);

                    if (!empty($organisations)) {
                        list($latestVote, $latestVoteErrors) = api_result(ApiVote::class, 'getLatestVote', ['org_id' => $loggedUserOrg]);

                        if (!empty($latestVote)) {
                            $latestVoteArray = explode(',', $latestVote->vote_data);
                        } elseif ($latestVoteErrors) {
                            $request->session()->flash('alert-danger', __('custom.get_latest_vote_fail'));
                        }
                    }
                }
            }

            $result = api(ApiVote::class, 'getMaxVotes');
            if (isset($result->success) && $result->success && isset($result->max_votes)) {
                $maxVotes = $result->max_votes;
            }

            $infoLabel = 'custom.no_tours_available';
        }

        if (!empty($latestVoteArray) && !isset($request->change)) {
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
            'infoLabel'      => $infoLabel,
        ]);
    }

    public function vote(Request $request)
    {
        $loggedUserOrg = \Auth::user()->org_id;

        $voteListArray = $request->offsetGet('votefor');

        $voteString = is_array($voteListArray) ? implode(',', $voteListArray) : '';

        list($vote, $voteErrors) = api_result(ApiVote::class, 'vote', [
            'org_id'   => $loggedUserOrg,
            'org_list' => $voteString,
        ]);

        if (empty($voteErrors)) {
            $organisations = [];
            $latestVoteArray = [];

            if (!empty($this->votingTour)) {
                if ($this->votingTour->status == VotingTour::STATUS_VOTING) {
                    $memberStatus = [Organisation::STATUS_CANDIDATE];
                } elseif ($this->votingTour->status == VotingTour::STATUS_BALLOTAGE) {
                    $memberStatus = [Organisation::STATUS_BALLOTAGE];
                } else {
                    $memberStatus = [];
                }

                if (!empty($memberStatus)) {
                    list($organisations, $orgErrors) = api_result(ApiOrganisation::class, 'search', [
                        'filters' => [
                            'statuses'         => $memberStatus,
                            'only_main_fields' => true,
                        ],
                    ]);

                    if (!empty($organisations)) {
                        list($latestVote, $latestVoteErrors) = api_result(ApiVote::class, 'getLatestVote', ['org_id' => $loggedUserOrg]);

                        if (!empty($latestVote)) {
                            $latestVoteArray = explode(',', $latestVote->vote_data);
                        } elseif ($latestVoteErrors) {
                            $request->session()->flash('alert-danger', __('custom.get_latest_vote_fail'));
                        }
                    }
                }
            }

            list($loggedOrg, $loggedOrgErrors) = api_result(ApiOrganisation::class, 'getData', ['org_id' => $loggedUserOrg]);

            if (!empty($loggedOrgErrors)) {
                $mailResult = false;
            } else {
                $mailResult = sendEmail('emails/vote_confirmation', ['name' => $loggedOrg->name], $loggedOrg->email, __('custom.vote_successful'));
            }

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
            $singleError = is_array($singleError) ? array_pop($singleError) : $singleError;
            $request->session()->flash('alert-danger', $singleError);
        }

        return redirect()->back();
    }
}
