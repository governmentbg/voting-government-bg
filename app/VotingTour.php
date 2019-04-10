<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordSignature;
use App\Traits\MetaData;

class VotingTour extends Model
{
    use RecordSignature;
    use MetaData;
  
    const STATUS_UPCOMING = 0;
    const STATUS_OPENED_REG = 1;
    const STATUS_CLOSED_REG = 2;
    const STATUS_VOTING = 3;
    const STATUS_RANKING = 4;
    const STATUS_BALLOTAGE = 5;
    const STATUS_FINISHED = 6;

    const DEFAULT_RECORDS_PER_PAGE = 50;
    const DEFAULT_ORDER_FIELD = 'created_at';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $table = 'voting_tour';

    public function votes()
    {
        return $this->hasMany('App\Vote', 'voting_tour_id');
    }

    public function organisations()
    {
        return $this->hasMany('App\Vote', 'voting_tour_id');
    }

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_UPCOMING => __('custom.upcoming'),
            self::STATUS_OPENED_REG => __('custom.opened_for_reg'),
            self::STATUS_CLOSED_REG => __('custom.closed_for_reg'),
            self::STATUS_VOTING => __('custom.voting'),
            self::STATUS_RANKING => __('custom.ranking'),
            self::STATUS_BALLOTAGE => __('custom.ballotage'),
            self::STATUS_FINISHED => __('custom.finished')
        ];
    }

    public static function getRegStatuses()
    {
        return [
            self::STATUS_UPCOMING,
            self::STATUS_OPENED_REG,
            self::STATUS_CLOSED_REG
        ];
    }

    public static function getLatestTour()
    {
        $latestTour = self::where('status', '!=', self::STATUS_FINISHED)->first();

        if (empty($latestTour)) {
            $latestTour = self::orderBy('updated_at', 'DESC')->orderBy('id', 'DESC')->first();
        }

        if (empty($latestTour)) {
            return false;
        }

        return $latestTour;
    }

    public static function getActiveStatuses()
    {
        return [
            self::STATUS_VOTING => __('custom.voting'),
            self::STATUS_BALLOTAGE => __('custom.ballotage')
        ];
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_FINISHED);
    }
}
