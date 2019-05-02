<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use App\VotingTour;

class EloquentFrontendUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials)
    {
        $votingTour = VotingTour::getLatestTour();
        if (empty($votingTour)) {
            return null;
        }

        $credentials['voting_tour_id'] = $votingTour->id;

        $user = parent::retrieveByCredentials($credentials);

        return $user && $user->isAdmin() === false ? $user : null;
    }
}
