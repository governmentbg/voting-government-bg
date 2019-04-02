<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordSignature;
use Awobaz\Compoships\Compoships;

class Organisation extends Model
{
    use Compoships;
    use RecordSignature;

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

    public function latestVote(){
        //return $this->votes()->latest('vote_time');
        return  $this->hasOne('App\Vote', ['voter_id', 'voting_tour_id'] , ['id', 'voting_tour_id'])->latest('vote_time');
    }
}
