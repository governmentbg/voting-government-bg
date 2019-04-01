<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RecordSignature;

class VotingTour extends Model
{
    use RecordSignature;
    
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
}
