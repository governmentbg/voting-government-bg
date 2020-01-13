<?php

namespace App\Console\Commands;

ini_set('max_execution_time', 300);

use App\VotingTour;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\VotingTourController as ApiVotingTour;
use App\Http\Controllers\Api\VoteController as ApiVote;

class CacheRanking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:ranking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache vote results for latest voting tour.';

    private $votingTour;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        list($this->votingTour, $tourErrors) = api_result(ApiVotingTour::class, 'getLatestVotingTour');
        if ($tourErrors) {
            $this->votingTour = null;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->votingTour) {
            $this->warn('Valid voting tour not found!');
            return;
        }

        if (!empty($this->votingTour) && in_array($this->votingTour->status, VotingTour::getRankingStatuses())) {
            $listData = [];
            $votingCount = 0;
            $stats = [];

            $cacheKey = VotingTour::getCacheKey($this->votingTour->id);

            // list ranking
            $params = [
                'tour_id' => $this->votingTour->id
            ];
            list($listData, $listErrors) = api_result(ApiVote::class, 'getLatestRanking', $params);

            if (!empty($listErrors)) {
                $this->error( __('custom.list_ranking_fail'));
            } else {
                if (!empty($listData) && isset($listData->ranking) && !empty($listData->ranking)) {
                    $votingCount = $listData->voting_count;
                    if (isset($listData->voter_turnout) && !empty($listData->voter_turnout)) {
                        $stats = $listData->voter_turnout;
                    } else {
                        $this->error( __('custom.voter_turnout_fail'));
                    }
                    $listData = $listData->ranking;
                }

                Cache::put($cacheKey, ['listData' => $listData, 'stats' => $stats, 'votingCount' => $votingCount], now()->addMinutes(60));
            }
        } else {
            $this->warn('Wrong voting tour status. Ranking can\'t be calculated.');
            return;
        }
    }
}
