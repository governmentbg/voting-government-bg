<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\RecordSignature;
use Awobaz\Compoships\Compoships;

class User extends Authenticatable
{
    use Notifiable;
    use Compoships;
    use RecordSignature;
    
    protected $guarded = ['id']; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];
    
    public function organisation()
    {
        return $this->hasOne('App\Organisation', ['org_id', 'voting_tour_id'], ['org_id', 'voting_tour_id']);
    }   
}
