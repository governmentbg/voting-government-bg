<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordSignature;
use Awobaz\Compoships\Compoships;

class Organisation extends Model
{
    use Compoships;
    use RecordSignature;

    const STATUS_NEW = 0;
    const STATUS_PARTICIPANT = 1;
    const STATUS_CANDIDATE = 2;
    const STATUS_PENDING = 3;
    const STATUS_BALLOTAGE = 4;
    const STATUS_REJECTED = 5;

    const IN_AV_FALSE = 0;
    const IN_AV_TRUE = 1;
    const IS_CANDIDATE_FALSE = 0;
    const IS_CANDIDATE_TRUE = 1;

    const DEFAULT_ORDER_FIELD = 'eik';
    const DEFAULT_ORDER_TYPE = 'ASC';

    protected $perPage = 50;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User', ['org_id', 'voting_tour_id'], ['org_id', 'voting_tour_id']);
    }

    public function files()
    {
        return $this->hasMany('App\File', ['org_id', 'voting_tour_id'], ['org_id', 'voting_tour_id']);
    }

    public function votes()
    {
        return $this->hasMany('App\Vote', ['voter_id', 'voting_tour_id'] , ['id', 'voting_tour_id']);
    }

    public function latestVote()
    {
        //return $this->votes()->latest('vote_time');
        return  $this->hasOne('App\Vote', ['voter_id', 'voting_tour_id'] , ['id', 'voting_tour_id'])->latest('vote_time');
    }

    public function setUpdatedAtAttribute($value)
    {
        // to disable updated_at
    }

    public static function getOrderColumns()
    {
        return [
            'eik',
            'name',
            'email',
            'is_ap',
            'is_candidate',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW         => __('custom.org_status_new'),
            self::STATUS_PARTICIPANT => __('custom.org_status_participant'),
            self::STATUS_CANDIDATE   => __('custom.org_status_candidate'),
            self::STATUS_PENDING     => __('custom.org_status_pending'),
            self::STATUS_BALLOTAGE   => __('custom.org_status_ballotage'),
            self::STATUS_REJECTED    => __('custom.org_status_rejected'),
        ];
    }

    public static function getCandidateStatuses()
    {
        return [
            self::IS_CANDIDATE_TRUE  => __('custom.status_yes'),
            self::IS_CANDIDATE_FALSE => __('custom.status_no'),
        ];
    }

    public static function getStatusHints()
    {
        return [];
    }
}
