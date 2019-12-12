<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class Vote extends Model
{
    use Compoships;

    const MIN_VOTES = 1;
    const MAX_VOTES = 14;

    const GENESIS_RECORD = 1;

    // tour statuses for votes table
    const TOUR_VOTING = 1;
    const TOUR_RANKING = 2;
    const TOUR_BALLOTAGE = 3;
    const TOUR_BALLOTAGE_RANKING = 4;
    const TOUR_ORGANISATION_DECLASSED_RANKING = 5;
    const TOUR_CANCELLED_NO_RANKING = 6;

    protected $perPage = 20;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public $timestamps = false;

    public static function getVoteStatuses()
    {
        return [
            self::TOUR_VOTING                         => __('custom.voting'),
            self::TOUR_RANKING                        => __('custom.ranking'),
            self::TOUR_BALLOTAGE                      => __('custom.ballotage'),
            self::TOUR_BALLOTAGE_RANKING              => __('custom.ranking_ballotage'),
            self::TOUR_ORGANISATION_DECLASSED_RANKING => __('custom.org_declass_ranking'),
            self::TOUR_CANCELLED_NO_RANKING           => __('custom.tour_cancelled')
        ];
    }

    public static function getRankingStatuses()
    {
        return [
            self::TOUR_RANKING,
            self::TOUR_BALLOTAGE_RANKING,
            self::TOUR_ORGANISATION_DECLASSED_RANKING
        ];
    }

    public static function getVotingCount($tourId)
    {
        $votingCount = self::where('voter_id', null)
            ->where('voting_tour_id', $tourId)
            ->whereIn('tour_status', [self::TOUR_RANKING, self::TOUR_BALLOTAGE_RANKING])
            ->count();

        return $votingCount;
    }

    public static function getLatestRankingData($tourId)
    {
        $latestRankingData = self::select('*')
            ->where('voter_id', null)
            ->where('voting_tour_id', $tourId)
            ->whereIn('tour_status', self::getRankingStatuses())
            ->orderBy('id', 'DESC')
            ->first();

        return $latestRankingData;
    }

    public static function getLatestRankingId($tourId, $rankingStatus = null, $skipLast = 0)
    {
        $recordId = null;

        $latestRanking = self::select('id')->where('voter_id', null)->where('voting_tour_id', $tourId);
        if (isset($rankingStatus)) {
            $latestRanking->where('tour_status', $rankingStatus);
        } else {
            $latestRanking->whereIn('tour_status', [self::TOUR_RANKING, self::TOUR_BALLOTAGE_RANKING]);
        }
        $latestRanking->orderBy('id', 'DESC')->first();
        if ($skipLast > 0) {
            $latestRanking->skip($skipLast);
        }
        $latestRanking = $latestRanking->first();

        if (!empty($latestRanking)) {
            $recordId = $latestRanking->id;
        }

        return $recordId;
    }

    public static function getRankingIds($tourId)
    {
        $rankingData = self::select('id')
            ->where('voter_id', null)
            ->where('voting_tour_id', $tourId)
            ->whereIn('tour_status', [self::TOUR_RANKING, self::TOUR_BALLOTAGE_RANKING])
            ->orderBy('id')
            ->pluck('id')
            ->all();

        return $rankingData;
    }

    public static function getVoteLimits($tourId, $votingIndex, $rankingIds)
    {
        $voteLimits = [];

        if ($votingIndex == 0) {
            $voteLimits['status'] = self::TOUR_VOTING;
        } elseif ($votingIndex > 0) {
            $voteLimits['status'] = self::TOUR_BALLOTAGE;

            if (!empty($rankingIds)) {
                if (isset($rankingIds[$votingIndex - 1])) {
                    $voteLimits['minId'] = $rankingIds[$votingIndex - 1];
                }
                if (isset($rankingIds[$votingIndex])) {
                    $voteLimits['maxId'] = $rankingIds[$votingIndex];
                }
            }
        }

        return $voteLimits;
    }

    public static function calculateVoteLimit($data, $votingCount)
    {
        $limits = [
            'orgPos' => -1,
            'votes'  => []
        ];

        if ($votingCount > 0 && count($data) >= self::MAX_VOTES) {
            $keys = array_keys($data);
            if (isset($keys[self::MAX_VOTES])) {
                if (isset($data[$keys[self::MAX_VOTES]][$votingCount - 1]) && isset($data[$keys[self::MAX_VOTES - 1]][$votingCount - 1])) {
                    if ($data[$keys[self::MAX_VOTES]][$votingCount - 1] == $data[$keys[self::MAX_VOTES - 1]][$votingCount - 1]) {
                        $limits['votes'][$votingCount - 1] = $data[$keys[self::MAX_VOTES - 1]][$votingCount - 1];
                        for ($pos = 0; $pos < (self::MAX_VOTES - 1); $pos++) {
                            if (!isset($data[$keys[$pos]][$votingCount]) && isset($data[$keys[$pos]][$votingCount - 1]) &&
                                $data[$keys[$pos]][$votingCount - 1] <= $limits['votes'][$votingCount - 1]) {
                                break;
                            }
                            $limits['orgPos'] = $pos;
                        }
                    } else {
                        $limits['orgPos'] = self::MAX_VOTES - 1;
                    }
                } elseif (isset($data[$keys[self::MAX_VOTES - 1]][$votingCount - 1])) {
                    $limits['orgPos'] = self::MAX_VOTES - 1;
                } else {
                    if ($votingCount >= 2) {
                        $limits = self::calculateVoteLimit($data, $votingCount - 1);
                    }
                }
            } else {
                $limits['orgPos'] = self::MAX_VOTES - 1;
            }
        }

        return $limits;
    }

    public function organisation()
    {
        return $this->belongsTo('App\Organisation', 'voter_id');
    }
}
