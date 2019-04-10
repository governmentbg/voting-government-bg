<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\RecordSignature;
use Awobaz\Compoships\Compoships;

class User extends Authenticatable
{
    use Notifiable;
    use Compoships;
    use RecordSignature;
    
    const EDITABLE_FIELDS = ['first_name', 'last_name', 'active', 'email'];
    
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
    
    public function scopeSort($query, $field, $order)
    {
        if (isset($field)) {
            return $query->orderBy($field, $order);
        }
        
        return $query;
    }
    
    public function isAdmin()
    {
        return !isset($this->org_id);
    }
    
    public function isSuperAdmin()
    {
        return !isset($this->org_id) && $this->name == config('auth.system.user');
    }
}
