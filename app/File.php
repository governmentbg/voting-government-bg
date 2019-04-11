<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordSignature;

class File extends Model
{
    use RecordSignature;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public $timestamps = false;

    public function message()
    {
        return $this->belongsTo('App\Message', 'message_id');
    }

    public function organisation()
    {
        return $this->belongsTo('App\Organisation', 'org_id');
    }

    public function votingTour()
    {
        return $this->belongsTo('App\VotingTour', 'voting_tour_id');
    }

    public function setUpdatedAtAttribute($value)
    {
        // to disable updated_at
    }
}
