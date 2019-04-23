<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\VotingTour;

class BaseAdminController extends Controller
{
    public function __construct()
    {
        auth()->shouldUse('backend');
        list($votingTour, $tourErrors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');

        $votingTour->statusName = VotingTour::getStatuses()[$votingTour->status];
        $votingTour->showTick = in_array($votingTour->status, array_keys(VotingTour::getActiveStatuses())) ? true: false;

        view()->share('votingTourData', $votingTour);
    }
}
