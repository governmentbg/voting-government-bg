<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionRequest extends Model
{
    const UPDATED_AT = null;
    
    protected $guarded = [];

    protected $primaryKey = 'uid';
    
    public $timestamps = ['created_at'];
}
