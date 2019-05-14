<?php
ini_set('max_execution_time', 300); 

namespace App\Console\Commands;

use App\Organisation;
use App\Vote;
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
        if($tourErrors){
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
        if(!$this->votingTour){
            $this->warn('Valid voting tour not found!');
            return;
        }
        
        $listData = [];
        $showBallotage = false;
        $stats = [];
        $errors = [];
        $cacheKey = VotingTour::getCacheKey($this->votingTour->id);

        if (!empty($this->votingTour) && in_array($this->votingTour->status, VotingTour::getRankingStatuses())) {          
            // get vote status
            list($voteStatus, $listErrors) = api_result(ApiVote::class, 'getVoteStatus', ['tour_id' => $this->votingTour->id]);

            if (!empty($listErrors)) {
                $errors['message'] = __('custom.list_ranking_fail');
            } elseif (!empty($voteStatus)) {
                // list ranking
                $params = [
                    'tour_id' => $this->votingTour->id,
                    'status' => VotingTour::STATUS_VOTING
                ];

                list($listData, $listErrors) = api_result(ApiVote::class, 'ranking', $params);

                if (!empty($listErrors)) {
                    $this->error( __('custom.list_ranking_fail'));
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
        } else {
            $this->warn('Wrong voting tour status. Ranking can\'t be calculate.');
            return;
        }

        Cache::put($cacheKey, ['listData' => $listData, 'stats' => $stats, 'showBallotage' => $showBallotage], now()->addMinutes(60));
    }
}
